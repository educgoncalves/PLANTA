<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function executarImportacaoVoosAnac($identificacao, $usuario = 'GEAR', $modo = 'AUT') {
    // Inicializando variável URL  
    $url = 'http://siros.anac.gov.br/siros/registros/registros/registros.csv';
    
    $tarefa = 'VOOS';
    $resultado = "trVoosAnac_".$identificacao;
    $desprezadas = "trVoosAnac_Desprezadas_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Importação de Voos da ANAC - executado por ".$usuario;
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
            // Transferindo o arquivo para o cliente
            // header("Content-Description: File Transfer"); 
            // header("Content-Type: application/octet-stream"); 
            // header("Content-Disposition: attachment; filename=\"".$file_name."\""); 
            // readfile ($url);
            //montarMensagem("success",array("Arquivo baixado com sucesso!"));
        } else {
            throw new PDOException("Arquivo disponibilizado não obedece ao formato CSV!");
        }
               
        // Começando a importação
        //
        $conexao = conexao();
        $linhasTotal = 0;
        $linhasControle = 0;
        $linhasGravadas = 0;
        $linhasDePara = 0;
        $linhasDesprezadas = 0;
        $erros = array();

        if (($arquivo = fopen($file_name, "r")) !== FALSE) {
            //while ((($dados = fgetcsv($arquivo, 1000, ";")) !== FALSE) && ($linhasTotal < 5)){
            while (($dados = fgetcsv($arquivo, 1000, ";")) !== FALSE){               
                $linhasTotal++;
                $dados = str_replace(array('null','"',"'"),array('','',''),$dados);
                //$dados = mb_convert_encoding($dados,"UTF-8","Windows-1252");

        // Verificando a primeira linha do arquivo
                 if ($linhasTotal == 1) {
                    $linhasControle++;
                    if (strpos($dados[0], "Importante: Horários em UTC") === FALSE) {
                        throw new PDOException("Arquivo não é o esperado para a importação dos voos ANAC!");
                    }
                } 

        // Verificando a segunda linha do arquivo
        // Se o arquivo é válido, inicia o controle de atualização
                if ($linhasTotal == 2) {
                    $linhasControle++;
                    $linha = implode(";",$dados);
                    if ($linha !== "Cód. Empresa;Empresa;Nº Voo;Equip.;Seg;Ter;Qua;Qui;Sex;Sáb;Dom;Quant. Assentos;Nº SIROS;Situação SIROS;Data Registro;Início Operação;Fim Operação;Natureza Operação;Nº Etapa;Cód. Origem;Arpt Origem;Cód Destino;Arpt Destino;Horário Partida;Horário Chegada;Tipo Serviço;Objeto Transporte;Codeshare") {
                        throw new PDOException("Arquivo não é o esperado para a importação dos voos ANAC! [".$linha."]");
                    } else {
                        // Marcar registros de origem = IMP (importação) com origem = ATU (atualizando) 
                        $comando = "UPDATE gear_voos_anac SET origem = 'ATU' WHERE origem = 'IMP'";
                        $sql = $conexao->prepare($comando); 
                        if (!$sql->execute()) {
                            throw new PDOException("Não consegui preparar os voos ANAC para atualização!");
                        }
                    }
                }

        // Grava o voo regular caso tenha informação na linha
                if ($linhasTotal > 2) {
                    $critica = '';
                    if (!count($dados) == 28) {
                        $critica .= '[Linha deve ter 28 campos] ';
                    } else {
                        if (!(!empty($dados[0]))) {
                            $critica .= '[Campos operador não preenchido] ';
                        }
                    }

        // Só prossegue se critica em branco
                    if ($critica == '') {
                        $operador = $dados[0];
                        $empresa = $dados[1];
                        $numeroVoo = $dados[2];
                        $equipamento = $dados[3];
                        $segunda = $dados[4];
                        $terca = $dados[5];
                        $quarta = $dados[6];
                        $quinta = $dados[7];
                        $sexta = $dados[8];
                        $sabado = $dados[9];
                        $domingo = $dados[10];
                        $assentos = $dados[11];
                        $siros = $dados[12];
                        $situacaoSiros = $dados[13];
                        $dataRegistro = mudarDataHoraAMD($dados[14]);
                        $inicioOperacao = $dados[15];
                        $fimOperacao = $dados[16];
                        $naturezaOperacao = $dados[17];
                        $numeroEtapa = $dados[18];
                        $icaoOrigem = $dados[19];
                        $aeroportoOrigem = $dados[20];
                        $icaoDestino = $dados[21];
                        $aeroportoDestino = $dados[22];
                        $horarioPartida = ($dados[23] == '' ? '00:00' : $dados[23]);
                        $horarioChegada = ($dados[24] == '' ? '00:00' : $dados[24]);
                        $servico = $dados[25];
                        $objetoTransporte = $dados[26];
                        $codeshare = $dados[27];
        
        // Inserindo voo
                        $comando = "INSERT INTO gear_voos_anac(operador, empresa, numeroVoo, equipamento,".
                                    "segunda, terca, quarta, quinta, sexta, sabado, domingo, assentos, siros, situacaoSiros,". 
                                    "dataRegistro, inicioOperacao, fimOperacao, naturezaOperacao, numeroEtapa, icaoOrigem,". 
                                    "aeroportoOrigem, icaoDestino, aeroportoDestino, horarioPartida, horarioChegada, servico,". 
                                    "objetoTransporte, codeshare, origem, cadastro) VALUES ('".
                                    $operador."','".$empresa."','".$numeroVoo."','".$equipamento."','".$segunda."','".
                                    $terca."','".$quarta."','".$quinta."','".$sexta."','".$sabado."','".$domingo."',".
                                    $assentos.",'".$siros."','".$situacaoSiros."', ".$dataRegistro.",'".$inicioOperacao."','".
                                    $fimOperacao."','".$naturezaOperacao."',".$numeroEtapa.",'".$icaoOrigem."','".
                                    $aeroportoOrigem."','".$icaoDestino."','".$aeroportoDestino."','".$horarioPartida."','".
                                    $horarioChegada."','".$servico."','".$objetoTransporte."','".$codeshare."','IMP', UTC_TIMESTAMP())";
                        $sql = $conexao->prepare($comando);         
                        if ($sql->execute()) {
                            if ($sql->rowCount() > 0) {
                                $linhasGravadas++;
                            } else {
                                throw new PDOException("Não foi possível atualizar o voo regular - siros ".$siros."!");
                            }
                        } else {
                            throw new PDOException("Não foi possível atualizar o voo regular - siros ".$siros."!");
                        } 
                    } else {
                        $linhasDesprezadas++;
                        gravaXLogProcesso($desprezadas, "warning", implode(";",$dados).' '.$critica, $identificacao);
                    }
                }
            }
            fclose($arquivo);

        // Verifica se arquivo vazio
        // Senão, finaliza o controle de atualização
            if ($linhasTotal == 0) {
                throw new PDOException("Arquivo vazio!");
            } else {
        // Ao final excluir todos os registros com origem = ATU (atualizando) 
                $comando = "DELETE FROM gear_voos_anac WHERE origem = 'ATU'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível excluir os voos ANAC pendentes!";
                }

        // Excluir todos os dominios ANAC
                $comando = "DELETE FROM gear_dominios_anac WHERE tabela = 'planta_voos_anac'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível excluir os dominios ANAC!";
                }

        // Gravar domínios ICAO OPERADOR
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'operador', operador, empresa FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir dominios ANAC - ICAO OPERADOR!";
                }
            
        // Gravar domínios SITUAÇÃO SIROS
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'situacaoSiros', situacaoSiros, situacaoSiros FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir dominios ANAC - SITUAÇÃO SIROS!";
                }

        // Gravar domínios NATUREZA DA OPERAÇÃO
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'naturezaOperacao', naturezaOperacao, naturezaOperacao FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir dominios ANAC - NATUREZA DA OPERAÇÃO!";
                }

        // Gravar domínios SERVIÇO
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'servico', servico, servico FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir dominios ANAC - SERVIÇO!";
                }
                
        // Gravar domínios OBJETO TRANSPORTE
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'objetoTransporte', objetoTransporte, objetoTransporte FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir dominios ANAC - OBJETO TRANSPORTE!";
                }

        // Gravar domínios ICAO ORIGEM
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'icaoOrigem', icaoOrigem, aeroportoOrigem FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir domínios ANAC - ICAO ORIGEM!";
                }

        // Gravar domínios ICAO DESTINO
                $comando = "INSERT INTO gear_dominios_anac (tabela, coluna, codigo, descricao)
                                SELECT DISTINCT 'planta_voos_anac', 'icaoDestino', icaoDestino, aeroportoDestino FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    $erros[] = "Não foi possível incluir domínios ANAC - ICAO DESTINO!";
                }       

        // Atualizar DEPARA ANAC
                $comando = "SELECT tipo, anac, gear
                            FROM
                            (
                                SELECT DISTINCT 'classe' as tipo, naturezaOperacao as anac, '???' as gear FROM gear_voos_anac
                                UNION
                                SELECT DISTINCT 'natureza' as tipo, objetoTransporte as anac, '???' as gear FROM gear_voos_anac
                                UNION
                                SELECT DISTINCT 'servico' as tipo, servico as anac, '???' as gear FROM gear_voos_anac
                            ) T 
                            WHERE NOT EXISTS (SELECT * FROM gear_depara_anac dp WHERE dp.tipo = T.tipo AND dp.anac = T.anac)";
                $sql = $conexao->prepare($comando); 
                if ($sql->execute()){
                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $dados) {
                        $linhasDePara++;
                        $comando = "INSERT INTO gear_depara_anac(tipo,anac,gear) VALUES('".
                                    $dados['tipo']."','".$dados['anac']."','".$dados['planta']."')";
                        $sql = $conexao->prepare($comando); 
                        if (!$sql->execute()){
                            $erros[] = "Não foi possível atualizar os campos depara ANAC!";
                        }                                        
                    }
                } else {
                    $erros[] = "Não foi possível atualizar a tabela depara ANAC!";
                }
 
        // Atualizando AEROPORTOS
            // Marcar registros de origem = IMP (importação) com origem = ATU (atualizando) 
                $comando = "UPDATE gear_aeroportos SET origem = 'ATU' WHERE origem = 'IMP'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()) {
                    throw new PDOException("Não consegui preparar os aeroportos regulares para atualização!");
                }

            // Inicando a atualização
                $comando = "SELECT icaoOrigem as icao, aeroportoOrigem as aeroporto FROM gear_voos_anac
                            UNION 
                            SELECT icaoDestino as icao, aeroportoDestino as aeroporto FROM gear_voos_anac";
                $sql = $conexao->prepare($comando); 
                if ($sql->execute()){
                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $dados) {
                        $id = "";
                        $icao = $dados['icao'];
                        $nome = $dados['aeroporto'];
                        $fonte = 'ANAC';
                        $comando = "SELECT id, fonte FROM gear_aeroportos WHERE icao = '".$icao."'";
                        $sql = $conexao->prepare($comando); 
                        if ($sql->execute()){
                            if ($sql->rowCount() > 0) {
                                $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($registros as $dados) {
                                    $id = $dados['id'];
                                    $fonte = $dados['fonte'];
                                }
                            }
                        }
                        if ($fonte == 'ANAC') {
                            $vetor = explode(' - ', $nome);
                            $nome = $vetor[0];
                            $localidade = "";
                            $pais = "";
                            if (count($vetor) > 1){
                                $indPais = count($vetor) - 1;
                                $pais = $vetor[$indPais];
                                $localidade = "";
                                for ($i = 1; $i < $indPais; $i++) {
                                    $localidade .= ($localidade != "" ? " - " : "").$vetor[$i];
                                }
                            }
                            if ($id != "") {
                                $comando = "UPDATE gear_aeroportos SET nome = '".$nome."', localidade = '".$localidade."', pais = '".
                                            $pais."', origem = 'IMP' WHERE id = ".$id;                         
                            } else {
                                $comando = "INSERT INTO gear_aeroportos(icao, nome, localidade, pais, fonte, origem, cadastro) VALUES('".
                                            $icao."','".$nome."','".$localidade."','".$pais."','ANAC', 'IMP', UTC_TIMESTAMP())";
                            } 
                            $sql = $conexao->prepare($comando); 
                            if (!$sql->execute()){
                                $erros[] = "Não foi possível atualizar os campos de localidade e pais! [".$icao." - ".$nome." - ".$localidade." - ".$pais."]";
                            }
                        }
                    }
                // Excluir todos os aeroportos que não foram atualizados pela importação e que podem ser excluídos
                    $comando = "DELETE ae FROM gear_aeroportos AS ae 
                                WHERE (SELECT COUNT(*) FROM gear_acessos ac WHERE ac.idAeroporto = ae.id) = 0 
                                    AND (SELECT COUNT(*) FROM gear_status st1  WHERE st1.idAeroporto = ae.id) = 0 
                                    AND (SELECT COUNT(*) FROM gear_status st2  WHERE st2.idOrigem = ae.id) = 0 
                                    AND (SELECT COUNT(*) FROM gear_status st3  WHERE st3.idDestino = ae.id) = 0 
                                    AND (SELECT COUNT(*) FROM gear_vistoria_itens vis  WHERE vis.idAeroporto = ae.id) = 0 
                                    AND fonte = 'ANAC' AND origem = 'ATU'";
                    $sql = $conexao->prepare($comando); 
                    if (!$sql->execute()){ 
                        throw new PDOException("Não foi possível excluir os aeroportos não atualizados pela importação!");
                    }
                // Gravar como INATIVAS todos os aeroportos com origem ATU não puderam ser excluídos
                    $comando = "UPDATE gear_aeroportos SET situacao = 'INA', fonte = 'IMP' WHERE fonte = 'ATU'";
                    $sql = $conexao->prepare($comando); 
                    if (!$sql->execute()){ 
                        throw new PDOException("Não foi possível inativar os aeroportos não atualizados!");
                    }                    
                } else {
                    $erros[] = "Não foi possível atualizar a tabela de aeroportos!";
                }
            }    
        } else {
            throw new PDOException("Erro na abertura do arquivo ".$file_name."!");
        }

        $_tipoMsg = ($linhasDesprezadas == 0 && $linhasDePara == 0 && count($erros) == 0 ? "success" : "warning");
        $_mensagens[] = "";
        $_mensagens[] = "Arquivo: ".$file_name;
        $_mensagens[] = $_mensagemArquivo;
        $_mensagens[] = "";
        $_mensagens[] = "Total de Linhas = ".$linhasTotal;
        $_mensagens[] = "Linhas de Controle = ".$linhasControle;
        $_mensagens[] = "Linhas Gravadas = ".$linhasGravadas;
        $_mensagens[] = "Linhas Desprezadas = ".$linhasDesprezadas;
        $_mensagens[] = "Linhas Depara ANAC = ".$linhasDePara;

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