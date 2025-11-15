<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function executarImportacaoEquipamentos($identificacao, $file_name, $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'IEQP';
    $resultado = "trEquipamentos_".$identificacao;
    $desprezadas = "trEquipamentos_Desprezadas_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Importação dos Equipamentos - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$file_name.' '.$usuario.' '.$modo;
    
    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

        $conexao = conexao();

        // Validando arquivo de importação
        $arquivo = fopen ($file_name, 'r');
        if (feof($arquivo)) {
            throw new PDOException("Arquivo vazio!");
        } else {
            $linha = fgets($arquivo, 1024);
            $linha = Trim(mb_convert_encoding($linha,"UTF-8","Windows-1252"));
            if ($linha != "Fabricante;Modelo;ICAO;Tipo;Motorização;Qtd Motor;QTC;IATA;Categoria;Envergadura;Comprimento;Assentos;Ativa/Inativa") {
                throw new PDOException("Arquivo não é o esperado para a importação dos equipamentos!");
            }
        } 

        // Marcar registros de origem = IMP (importação) com origem = ATU (atualizando) 
        $comando = "UPDATE gear_equipamentos SET origem = 'ATU' WHERE fonte = 'ICAO' AND (origem = 'IMP' OR origem = 'PND')";
        $sql = $conexao->prepare($comando); 
        if (!$sql->execute()) {
            throw new PDOException("Não consegui preparar os equipamentos para atualização!");
        }
    
        // Looping de atualiação
        $linhasTotal = 0;
        $linhasGravadas = 0;
        $linhasRegravadas = 0;
        $linhasDesprezadas = 0;
        $linhasEmBranco = 0;
        $erros = array();

        while(!feof($arquivo)) {
            $linha = fgets($arquivo, 1024);
            if (!feof($arquivo)) {
                $linhasTotal++;
                if (!empty($linha)) {
                    $linha = Trim(mb_convert_encoding($linha,"UTF-8","Windows-1252"));
                    $linha = str_replace(array('null','"',"'"),array('','',''),$linha);
                    $dados = explode(';', $linha);
                    $fabricante = $dados[0];
                    $modelo = $dados[1];
                    $icao = $dados[2];
                    $asa = (!empty($dados[3]) ? ($dados[3] == 'Helicopter' ? 'MOV' : 'FIX') : 'FIX');
                    $motor = (!empty($dados[4]) ? ($dados[4] == 'Jet' ? 'JET' : 
                                                    ($dados[4] == 'Eletric' ? 'ELE' : 
                                                        ($dados[4] == 'Piston' ? 'PST' : 
                                                            ($dados[4] == 'Rocket' ? 'RCK' : 'TRB')))) : 'JET');
                    $qtc = $dados[6];
                    $iata = $dados[7];
                    $categoria = $dados[8];
                    $envergadura = (!empty($dados[9]) ? $dados[9] : 0);
                    $comprimento = (!empty($dados[10]) ? $dados[10] : 0);
                    $assentos = (!empty($dados[11]) ? $dados[11] : 0);
                    $situacao = (!empty($dados[12]) ? $dados[12] : 'ATV');

                    // Verifica se Equipamento e Modelo existe 
                    $id = "";
                    $origem = 'ATU';
                    $comando = "SELECT id, fonte FROM gear_equipamentos WHERE equipamento = '".$icao."' AND modelo = '".$modelo.
                                "' AND fabricante = '".$fabricante."' LIMIT 1";
                    $sql = $conexao->prepare($comando);    
                    if ($sql->execute()) {
                        $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($registros as $dados) {
                            $id = $dados['id'];
                            $fonte = $dados['fonte'];     
                        }

                        // Só atualizar registros origem = ATU regravando a origem para IMP
                        // Registros com origem - MNL (manual) devem ser preservados
                        if ($fonte == 'ICAO'){
                            if ($id != "") {
                                $comando = "UPDATE gear_equipamentos SET iataEquipamento='".$iata."',icaoCategoria='".$categoria."',tipoMotor='".
                                            $motor."',envergadura=".$envergadura.",comprimento=".$comprimento.",assentos=".$assentos.",asa='".$asa.
                                            "',situacao='".$situacao."',fonte='ICAO',origem='IMP',cadastro=UTC_TIMESTAMP() WHERE id = ".$id;                         
                                $linhasRegravadas++;
                            } else {
                                $comando = "INSERT INTO gear_equipamentos(equipamento,modelo,iataEquipamento,icaoCategoria,tipoMotor,".
                                            "fabricante,envergadura,comprimento,assentos,asa,situacao,fonte,origem,cadastro) VALUES ('".$icao."','".
                                            $modelo."','".$iata."','".$categoria."','".$motor."','".$fabricante."',".$envergadura.",".
                                            $comprimento.",".$assentos.",'".$asa."','".$situacao."','ICAO','IMP', UTC_TIMESTAMP())";
                                $linhasGravadas++;
                            }        
                            $sql = $conexao->prepare($comando);         
                            if ($sql->execute()) {
                                if ($sql->rowCount() > 0) {
                                } else {
                                    throw new PDOException("Não foi possível atualizar o equipamento ".$icao."/".$modelo."!");
                                }
                            } else {
                                throw new PDOException("Não foi possível atualizar o equipamento ".$icao."/".$modelo."!");
                            } 
                        } else {
                            $linhasDesprezadas++;
                            gravaXLogProcesso($desprezadas, "warning", $icao.";".$modelo.";".$fabricante." [Atualizado por outro processo]", $identificacao);
                        }
                    } else {
                        throw new PDOException("Não consegui posicionar no equipamento ".$icao."/".$modelo." para atualização!");
                    }
                } else {
                    $linhasEmBranco++; 
                }
            }
        }
        fclose($arquivo);

        // Ao final excluir todos os registros com origem = ATU (atualizando) que não foram utilizados na matrícula
        $comando = "DELETE eq FROM gear_equipamentos eq
                    WHERE eq.origem = 'ATU' AND eq.fonte = 'ICAO' AND 
                        (SELECT COUNT(mt.idEquipamento) FROM gear_matriculas mt WHERE mt.idEquipamento = eq.id) = 0";
        $sql = $conexao->prepare($comando); 
        if (!$sql->execute()){ 
            throw new PDOException("Não foi possível excluir os equipamentos pendentes e não utilizados na matrícula!");
        }

        // Ao final alterar todos os registros com origem = ATU (atualizando) para PND (pendente) que foram utilizados na matrícula
        $comando = "UPDATE gear_equipamentos eq SET origem = 'PND', situacao = 'INA'
                    WHERE eq.origem = 'ATU' AND eq.fonte = 'ICAO' AND 
                        (SELECT COUNT(mt.idEquipamento) FROM gear_matriculas mt WHERE mt.idEquipamento = eq.id) <> 0";
        $sql = $conexao->prepare($comando); 
        if (!$sql->execute()){ 
            throw new PDOException("Não foi possível alterar os equipamentos pendentes e utilizados na matrícula!");
        }
    
        $_tipoMsg = ($linhasDesprezadas == 0 ? "success" : "warning");
        $_mensagens[] = "";
        $_mensagens[] = "Arquivo: ".$file_name;
        $_mensagens[] = "";
        $_mensagens[] = "Total de Linhas = ".$linhasTotal;
        $_mensagens[] = "Linhas Gravadas = ".$linhasGravadas;
        $_mensagens[] = "Linhas Atualizadas = ".$linhasRegravadas;
        $_mensagens[] = "Linhas Desprezadas = ".$linhasDesprezadas;
        $_mensagens[] = "Linhas em branco = ".$linhasEmBranco;

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