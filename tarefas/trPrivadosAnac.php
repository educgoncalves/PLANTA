<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function executarImportacaoPrivadosAnac($identificacao, $usuario = 'GEAR', $modo = 'AUT') {
    // Inicializando variável URL  
    $url = 'https://sistemas.anac.gov.br/dadosabertos/Aerodromos/Aer%C3%B3dromos%20Privados/Lista%20de%20aer%C3%B3dromos%20privados/Aerodromos%20Privados/AerodromosPrivados.csv';

    $tarefa = 'APRI';
    $resultado = "trPrivadosAnac_".$identificacao;
    $desprezadas = "trPrivadosAnac_Desprezadas_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Importação de Aeródromos Privados da ANAC - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$usuario.' '.$modo;

    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        // Iniciando o download do arquivo CSV
        //
        $comando = "";
        $_mensagemArquivo = "";
        $file_name = "../arquivos/anac/".basename($url); 
        $info = pathinfo($file_name);
        if ($info["extension"] == "csv") {
            // Transferindo o arquivo csv para o servidor
            $_mensagemArquivo = (file_put_contents($file_name, file_get_contents($url))) ? "Download executado - " : "Download não executado - "; 
            clearstatcache();
            if (is_file($file_name)) {
                $info = new SplFileInfo($file_name);
                $_mensagemArquivo .= "Utilizando arquivo atualizado em ". date("d/m/Y H:i:s", $info->getMTime());
            } else {
                throw new PDOException("Falha no download do arquivo!");
            }
        } else {
            throw new PDOException("Arquivo disponibilizado não obedece ao formato CSV!");
        }

        // Começando a importação
        //
        $conexao = conexao();
        $linhasTotal = 0;
        $linhasControle = 0;
        $linhasGravadas = 0;
        $linhasDesprezadas = 0;
        $erros = array();

        if (($arquivo = fopen($file_name, "r")) !== FALSE) {
        //      while ((($dados = fgetcsv($arquivo, 1000, ";")) !== FALSE) && ($linhasTotal < 5)){
           while (($dados = fgetcsv($arquivo, 1000, ";")) !== FALSE){               
                $linhasTotal++;
                $dados = str_replace(array('null','"',"'"),array('','',''),$dados);
                $dados = mb_convert_encoding($dados,"UTF-8","Windows-1252");

                // Verificando a primeira linha do arquivo
                 if ($linhasTotal == 1) {
                    $linhasControle++;
                    if (strpos($dados[0], "Atualizado") === FALSE) {
                        throw new PDOException("Arquivo não é o esperado para a importação dos aeródromos privados ANAC!");
                    }
                } 

                // Verificando a segunda linha do arquivo
                // Se o arquivo é válido, inicia o controle de atualização
                if ($linhasTotal == 2) {
                    $linhasControle++;
                    $linha = implode(";",$dados);

                    if (strpos($linha,"Código OACI;CIAD;Nome;Município") === FALSE) {
                        throw new PDOException("Arquivo não é o esperado para a importação dos aeródromos privados ANAC! [".$linha."]");
                    } else {
                        // Marcar registros de origem = PRI (importação) com origem = ATU (atualizando) 
                        $comando = "UPDATE gear_aeroportos SET origem = 'ATU' WHERE origem = 'PRI'";
                        $sql = $conexao->prepare($comando); 
                        if (!$sql->execute()) {
                            throw new PDOException("Não consegui preparar os aeródromos privados para atualização!");
                        }
                    }
                }

                // Grava o voo regular caso tenha informação na linha
                //
                if ($linhasTotal > 2) {
                    $critica = '';
                    if (!(!empty($dados[0]))) {
                        $critica .= '[Sigla ICAO não preenchida] ';
                    }

                    // Só prossegue se critica em branco
                    if ($critica == '') {
                        $icao = $dados[0];
                        $CIAD = $dados[1];
                        $nome = $dados[2];
                        $localidade = $dados[3]." - ".$dados[4];
                        $pais = "BRASIL";

                        // Atualizando AEROPORTOS PUBLICOS que foram importados por esta importação 
                        $id = "";
                        $comando = "SELECT id, fonte, origem FROM gear_aeroportos WHERE icao = '".$icao."'";
                        $sql = $conexao->prepare($comando); 
                        if ($sql->execute()){
                            if ($sql->rowCount() > 0) {
                                $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($registros as $dados) {
                                    $id = $dados['id'];
                                    $fonte = $dados['fonte'];
                                    $origem = $dados['origem'];
                                }
                            }
                            $comando = '';
                            if ($id != "") {
                                // Só regravar se fonte ANAC e origem ATU
                                if ($fonte == 'ANAC' && $origem == 'ATU') {
                                    $comando = "UPDATE gear_aeroportos SET nome = '".$nome."', localidade = '".$localidade."', pais = '".$pais."', origem = 'PRI', cadastro = UTC_TIMESTAMP() WHERE id = ".$id;                         
                                }
                            } else {
                                $comando = "INSERT INTO gear_aeroportos(icao, nome, localidade, pais, fonte, origem, cadastro) VALUES('".
                                            $icao."','".$nome."','".$localidade."','".$pais."','ANAC', 'PRI', UTC_TIMESTAMP())";
                            } 
                            if ($comando != '') {
                                $sql = $conexao->prepare($comando); 
                                if (!$sql->execute()){
                                    $erros[] = "Não foi possível atualizar os campos do aeródromo público! [".$icao." - ".$nome." - ".$localidade." - ".$pais."]";
                                } else {
                                    $linhasGravadas++;
                                }
                            }
                        } else {
                            $erros[] = "Não foi possível atualizar a tabela de aeroportos!";
                        }
                    } else {
                        $linhasDesprezadas++;
                        gravaXLogProcesso($desprezadas, "warning", $dados[1]. ' '.$critica, $identificacao);
                    }
                }
            }
            fclose($arquivo);

            // Verifica se arquivo vazio
            // Senão, finaliza o controle de atualização
            if ($linhasTotal == 0) {
                throw new PDOException("Arquivo vazio!");
            } else {
                // Ao final gravar como inativo todos os registros com origem = ATU (atualizando) 
                $comando = "UPDATE gear_aeroportos SET situacao = 'INA' WHERE origem = 'ATU'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível desativar os aeródromos privados ANAC pendentes!";
                }
           }   
        } else {
            throw new PDOException("Erro na abertura do arquivo ".$file_name."!");
        }

        $_tipoMsg = ($linhasDesprezadas == 0  && count($erros) == 0 ? "success" : "warning");
        $_mensagens[] = "";
        $_mensagens[] = "Arquivo: ".$file_name;
        $_mensagens[] = $_mensagemArquivo;
        $_mensagens[] = "";
        $_mensagens[] = "Total de Linhas = ".$linhasTotal;
        $_mensagens[] = "Linhas de Controle = ".$linhasControle;
        $_mensagens[] = "Linhas Gravadas = ".$linhasGravadas;
        $_mensagens[] = "Linhas Desprezadas = ".$linhasDesprezadas;

        if (count($erros) != 0) {
            $_mensagens[] = "";
            $_mensagens[] = "Erros";
            array_push($_mensagens, ...$erros);
        }

    } catch (PDOException $e) {
        $_tipoMsg = "danger";
        $_mensagens[] = "";
        $_mensagens[] = traduzPDO($e->getMessage());
    }

    // Registrando o log
    foreach ($_mensagens as $msg) {
        gravaXLogProcesso($resultado, $_tipoMsg, $msg, $identificacao);
    }
            
    // Enviar email de resultado da execução da importação - depende de configuração na tabela de tarefas
    enviarEmailTarefa($tarefa,$resultado,$_tipoMsg);
}
?>