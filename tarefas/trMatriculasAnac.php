<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");

function executarImportacaoMatriculasAnac($identificacao, $usuario = 'GEAR', $modo = 'AUT') {
    // Inicializando variável URL  
    $url = 'https://sistemas.anac.gov.br/dadosabertos/Aeronaves/RAB/dados_aeronaves.csv';

    $tarefa = 'MATR';
    $resultado = "trMatriculasAnac_".$identificacao;
    $desprezadas = "trMatriculasAnac_Desprezadas_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Importação de Matrículas da ANAC/RAB - executado por ".$usuario;
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
        $integracao = integracao();
        $comando = '';
        $linhasTotal = 0;
        $linhasControle = 0;
        $linhasGravadas = 0;
        $linhasDesprezadas = 0;
        $linhasOperadores = 0;
        $linhasEquipamentos = 0;
        $linhasMatriculas = 0;
        $integracaoGravadas = 0;
        $erros = array();

        if (($arquivo = fopen($file_name, "r")) !== FALSE) {


            //while ((($dados = fgetcsv($arquivo, 1000, ";")) !== FALSE) && ($linhasTotal < 50)){
            while (($dados = fgetcsv($arquivo, 1000, ";")) !== FALSE){               
                $linhasTotal++;
                $dados = str_replace(array('null','"',"'"),array('','',''),$dados);
                //$dados = mb_convert_encoding($dados,"UTF-8","Windows-1252");

                // Verificando a primeira linha do arquivo
                 if ($linhasTotal == 1) {
                    $linhasControle++;
                    if (strpos($dados[0], "Atualizado em") === FALSE) {
                        throw new PDOException("Arquivo não é o esperado para a importação das matrículas!");
                    }
                } 

                // Verificando a segunda linha do arquivo
                // Se o arquivo é válido, inicia o controle de atualização
                if ($linhasTotal == 2) {
                    $linhasControle++;
                    $linha = implode(";",$dados);
                    if ($linha !== 'MARCA;PROPRIETARIO;OUTROS_PROPRIETARIOS;SG_UF;CPF_CNPJ;NM_OPERADOR;OUTROS_OPERADORES;UF_OPERADOR;CPF_CGC;NR_CERT_MATRICULA;NR_SERIE;CD_CATEGORIA;CD_TIPO;DS_MODELO;NM_FABRICANTE;CD_CLS;NR_PMD;CD_TIPO_ICAO;NR_TRIPULACAO_MIN;NR_PASSAGEIROS_MAX;NR_ASSENTOS;NR_ANO_FABRICACAO;DT_VALIDADE_CVA;DT_VALIDADE_CA;DT_CANC;DS_MOTIVO_CANC;CD_INTERDICAO;CD_MARCA_NAC1;CD_MARCA_NAC2;CD_MARCA_NAC3;CD_MARCA_ESTRANGEIRA;DS_GRAVAME;DT_MATRICULA;TP_MOTOR;QT_MOTOR;TP_POUSO') {
                        throw new PDOException("Arquivo não é o esperado para a importação das matrículas! [".$linha."]");
                    } else {
                        // Excluir todos os registros de matrículas da tabela ANAC
                        $comando = "DELETE FROM gear_matriculas_anac";
                        $sql = $conexao->prepare($comando); 
                        if (!$sql->execute()) {
                            throw new PDOException("Não consegui limpar as matrículas ANAC para atualização!");
                        }
                        // Excluir todos os registros de matrículas da tabela INTEGRACAO gear_matriculas_anac
                        $comando = "DELETE FROM gear_matriculas_anac";
                        $sql = $integracao->prepare($comando); 
                        if (!$sql->execute()) {
                            throw new PDOException("INTEGRAÇÃO - Não consegui limpar as matrículas ANAC para atualização!");
                        }
                    }
                }

                // Grava a matrícula caso tenha informação na linha
                //
                // Campos marca, nm_operador e cd_tipo_icao preenchidos
                // Campo cpf_cgc deverá estar preenchido e ter o tamanho de 14 ou 18 caracteres
                // Linha deve ter 36 campos
                // Campo ds_gravame não poderá conter a palavra CANCELADA ou RESERVADA
                // Campo cd_cls não poderá ser igual a RPA
                if ($linhasTotal > 2) {
                    $critica = '';
                    if (!count($dados) == 36) {
                        $critica .= '[Linha deve ter 36 campos] ';
                    } else {
                        if (empty($dados[0]) || empty($dados[5]) || empty($dados[17])) {
                            $critica .= '[Campos marca, nm_operador ou cd_tipo_icao não preenchido] ';
                        } else {
                            if (empty($dados[8]) || (strLen(trim($dados[8])) != 14 && strLen(trim($dados[8])) != 18)) {
                                $critica .= '[Campo cpf_cgc não preenchido ou não tem o tamanho de 14 ou 18 caracteres] ';
                            }
                            if (strpos($dados[31],"CANCELADA") != 0 || strpos($dados[31],"RESERVADA") != 0) {
                                $critica .= '[Campo ds_gravame contém a palavra CANCELADA ou RESERVADA] ';
                            }
                            if ($dados[15] == "RPA") {
                                $critica .= '[Campo cd_cls igual a RPA]';
                            }
                            switch (substr($dados[26],0,1)) {
                                case "S":
                                    $critica .= '[Campo cd_interdicao contém a caracter S - CERTIFICADO DE AERONAVEGABILIDADE SUSPENSO]';
                                break;
                                case "C":
                                    $critica .= '[Campo cd_interdicao contém a caracter C - CERTIFICADO DE AERONAVEGABILIDADE CANCELADO]';
                                break;
                                case "V":
                                    $critica .= '[Campo cd_interdicao contém a caracter V - CERTIFICADO DE AERONAVEGABILIDADE VENCIDO]';
                                break;
                                case "X":
                                    $critica .= '[Campo cd_interdicao contém a caracter X - AERONAVE INTERDITADA]';
                                break;
                                case "P":
                                    $critica .= '[Campo cd_interdicao contém a caracter P - AERONAVE COM SITUAÇÃO PUNITIVA EM VIGOR]';
                                break;
                                case "M":
                                    $critica .= '[Campo cd_interdicao contém a caracter M - MATRÍCULA CANCELADA]';
                                break;
                            }
                        }
                    }

                    // Só prossegue se critica em branco
                    if ($critica == '') {
                        $marca = tirarSimbolosAcentos(trim($dados[0]));
                        $nm_operador = tirarSimbolosAcentos(trim($dados[5]));
                        $uf_operador = $dados[7];
                        $cpf_cgc = trim($dados[8]);
                        $cd_categoria = $dados[11];
                        $cd_tipo = $dados[12];
                        $ds_modelo = tirarSimbolosAcentos(trim($dados[13]));
                        $nm_fabricante = tirarSimbolosAcentos(trim($dados[14]));
                        $cd_cls = $dados[15];
                        $nr_pmd = is_numeric($dados[16]) ? $dados[16] : 0; 
                        $cd_tipo_icao = tirarSimbolosAcentos(trim($dados[17]));
                        $nr_assentos = is_numeric($dados[20]) ? $dados[20] : 0; 
                        $ds_gravame = substr($dados[31],0,250);

                        // Inserindo aeronave
                        $comando = 'INSERT INTO gear_matriculas_anac (marca, nm_operador, uf_operador, cpf_cgc, cd_categoria, cd_tipo, ds_modelo,'.
                                    'nm_fabricante, cd_cls, nr_pmd, cd_tipo_icao, nr_assentos, ds_gravame, origem, cadastro) VALUES ("'.
                                    $marca.'","'.$nm_operador.'","'.$uf_operador.'","'.$cpf_cgc.'","'.$cd_categoria.'","'.
                                    $cd_tipo.'","'.$ds_modelo.'","'.$nm_fabricante.'","'.$cd_cls.'",'.$nr_pmd.',"'.$cd_tipo_icao.'",'.
                                    $nr_assentos.',"'.$ds_gravame.'", "IMP", UTC_TIMESTAMP())';
                        $sql = $conexao->prepare($comando);         
                        if ($sql->execute()) {
                            if ($sql->rowCount() > 0) {
                                $linhasGravadas++;

                                // Inserindo aeronave na INTEGRACAO
                                $sql = $integracao->prepare($comando);         
                                if ($sql->execute()) {
                                    if ($sql->rowCount() > 0) {
                                        $integracaoGravadas++;
                                    } else {
                                        throw new PDOException("INTEGRAÇÃO - Não foi possível atualizar a matrícula ".$marca."!");
                                    }
                                } else {
                                    throw new PDOException("INTEGRAÇÃO - Não foi possível atualizar a matrícula ".$marca."!");
                                } 

                            } else {
                                throw new PDOException("Não foi possível atualizar a matrícula ".$marca."!");
                            }
                        } else {
                            throw new PDOException("Não foi possível atualizar a matrícula ".$marca."!");
                        } 
                    } else {
                        $linhasDesprezadas++;
                        gravaXLogProcesso($desprezadas, "warning", $dados[0].' '.$critica, $identificacao);
                    }
                }
            }
            fclose($arquivo);

            // Verifica se arquivo vazio
            // Senão, finaliza o controle de atualização
            if ($linhasTotal == 0) {
                throw new PDOException("Arquivo vazio!");
            } else {
                // Fazer o controle de atualização dos dados importados da ANAC
                $comando = "UPDATE gear_operadores SET origem = 'ATU' WHERE origem = 'IMP' AND fonte = 'ANAC'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()) {
                    throw new PDOException("Não consegui preparar os operadores para atualização!");
                }
                $comando = "UPDATE gear_equipamentos SET origem = 'ATU' WHERE origem = 'IMP' AND fonte = 'ANAC'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()) {
                    throw new PDOException("Não consegui preparar os equipamentos para atualização!");
                }
                $comando = "UPDATE gear_matriculas SET origem = 'ATU' WHERE origem = 'IMP' AND fonte = 'ANAC'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()) {
                    throw new PDOException("Não consegui preparar as matrículas para atualização!");
                }

                // Carga das tabelas gear_operadores, gear_equipamentos e gear_matriculas
                $comando = "SELECT * FROM gear_matriculas_anac ORDER BY marca";
                $sql = $conexao->prepare($comando); 
                if ($sql->execute()) {
                    if ($sql->rowCount() > 0) {
                        $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($registros as $dados) {
                        // Verificar operador
                            $idOperador = 0;
                            $cpfCNPJ =  $dados['cpf_cgc'];
                            $nomeLongo = substr($dados['nm_operador'],0,100);
                            $nomeCurto = substr($nomeLongo,0,25);
                            $comando = "SELECT id, fonte FROM gear_operadores WHERE cpfCNPJ = '".$cpfCNPJ."' AND operador = '".$nomeCurto."' LIMIT 1";
                            $sql = $conexao->prepare($comando); 
                            if ($sql->execute()) {
                                if ($sql->rowCount() > 0) {
                                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($registros as $_dados) {
                                        $idOperador = $_dados['id'];
                                        $fonte = $_dados['fonte'];
                                    }
                                    // Regravando o controle de importação - somente se a matrícula encontrada for de origem ANAC
                                    if ($fonte == 'ANAC') {
                                        $comando = "UPDATE gear_operadores SET origem = 'IMP', cadastro = UTC_TIMESTAMP() WHERE id = ".$idOperador;
                                        $sql = $conexao->prepare($comando); 
                                        if ($sql->execute()) {   
                                            $linhasOperadores++;
                                        } else { 
                                            throw new PDOException("Não consegui atualizar o operador!");  
                                        }     
                                    }                         
                                } else {
                                    // Inserindo operador 
                                    $comando = "INSERT INTO gear_operadores(operador, nome, cpfCnpj, fonte, origem, cadastro) VALUES ('". 
                                                $nomeCurto."', '".$nomeLongo."', '".$cpfCNPJ."', 'ANAC', 'IMP', UTC_TIMESTAMP())";
                                    $sql = $conexao->prepare($comando); 
                                    if ($sql->execute()) {
                                        if ($sql->rowCount() > 0) {
                                            $idOperador = $conexao->lastInsertId();   
                                            $linhasOperadores++;
                                        } else {
                                            throw new PDOException("Não consegui incluir o operador para atualização!");
                                        }    
                                    } else {
                                        throw new PDOException("Não consegui incluir o operador para atualização!");
                                    }        
                                }
                            } else {
                                throw new PDOException("Não consegui identificar o operador para atualização!");
                            }
            
                        // Verificar equipamento
                            $idEquipamento = 0;
                            $equipamento =  $dados['cd_tipo_icao'];
                            $modelo = $dados['ds_modelo'];
                            $fabricante = $dados['nm_fabricante'];
                            $asa = (substr($dados['cd_cls'],0,1) == 'H' ? 'MOV' : 'FIX');
                            $assentos = is_numeric($dados['nr_assentos']) ? $dados['nr_assentos'] : 0;
                            $fonte = 'XXXX';
                            $comando = "SELECT id, fonte FROM gear_equipamentos WHERE equipamento = '".$equipamento."' AND modelo = '".$modelo."' LIMIT 1";
                            $sql = $conexao->prepare($comando); 
                            if ($sql->execute()) {
                                if ($sql->rowCount() > 0) {
                                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($registros as $_dados) {
                                        $idEquipamento = $_dados['id'];
                                        $fonte = $_dados['fonte'];
                                    }
                                    // Regravando o controle de importação - somente se equipamento encontrado for de origem ANAC
                                    if ($fonte == 'ANAC') {
                                        $comando = "UPDATE gear_equipamentos SET origem = 'IMP', fabricante = '".$fabricante."', asa = '".$asa.
                                                    "', assentos = ".$assentos.", cadastro = UTC_TIMESTAMP() WHERE id = ".$idEquipamento;
                                        $sql = $conexao->prepare($comando); 
                                        if ($sql->execute()) {    
                                            $linhasEquipamentos++;
                                        } else {
                                            throw new PDOException("Não consegui atualizar o equipamento!");  
                                        }        
                                    }                      
                                } else {
                                    // Inserindo equipamento
                                    $comando = "INSERT INTO gear_equipamentos(equipamento, modelo, fabricante, asa, assentos, fonte, origem, cadastro) VALUES ('". 
                                                $equipamento."', '".$modelo."', '".$fabricante."', '".$asa."', ".$assentos.", 'ANAC', 'IMP', UTC_TIMESTAMP())";
                                    $sql = $conexao->prepare($comando); 
                                    if ($sql->execute()) {
                                        if ($sql->rowCount() > 0) {
                                            $idEquipamento = $conexao->lastInsertId();   
                                            $linhasEquipamentos++;
                                        } else {
                                            throw new PDOException("Não consegui incluir o equipamento para atualização!");
                                        }    
                                    } else {
                                        throw new PDOException("Não consegui incluir o equipamento para atualização!");
                                    }        
                                }
                            } else {
                                throw new PDOException("Não consegui identificar o equipamento para atualização!");
                            }

                        // Verificar matrícula
                            $idMatricula = 0;
                            $marca =  $dados['marca'];
                            $modelo = $dados['ds_modelo'];
                            $fabricante = $dados['nm_fabricante'];
                            $asa = (substr($dados['cd_cls'],0,1) == 'H' ? 'MOV' : 'FIX');
                            $assentos = is_numeric($dados['nr_assentos']) ? $dados['nr_assentos'] : 0;
                            $pmd = is_numeric($dados['nr_pmd']) ? $dados['nr_pmd'] : 0;
                            $fonte = 'XXXX';
                            $comando = "SELECT id, fonte FROM gear_matriculas WHERE matricula = '".$marca."' LIMIT 1";
                            $sql = $conexao->prepare($comando); 
                            if ($sql->execute()) {
                                if ($sql->rowCount() > 0) {
                                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($registros as $_dados) {
                                        $idMatricula = $_dados['id'];
                                        $fonte = $_dados['fonte'];
                                    }
                                    // Regravando o controle de importação - somente se a matrícula encontrada for de origem ANAC
                                    if ($fonte == 'ANAC') {
                                        $comando = "UPDATE gear_matriculas SET idEquipamento = ".$idEquipamento.", idOperador = ".$idOperador.
                                                    ", assentos = ".$assentos.", pmd = ".$pmd.", origem = 'IMP', cadastro = UTC_TIMESTAMP() WHERE id = ".$idMatricula;
                                        $sql = $conexao->prepare($comando); 
                                        if ($sql->execute()) {   
                                            $linhasMatriculas++;
                                        } else {                                             
                                            throw new PDOException("Não consegui atualizar a matrícula!");  
                                        }  
                                    }                            
                                } else {
                                    // Inserindo matrícula
                                    $comando = "INSERT INTO gear_matriculas(matricula, idEquipamento, idOperador, assentos, pmd, categoria, fonte, origem, cadastro) VALUES ('". 
                                                $marca."', ".$idEquipamento.", ".$idOperador.", ".$assentos.", ".$pmd.", 'NDN', 'ANAC', 'IMP', UTC_TIMESTAMP())";
                                    $sql = $conexao->prepare($comando); 
                                    if ($sql->execute()) {
                                        if ($sql->rowCount() > 0) {
                                            $linhasMatriculas++;
                                        } else {
                                            throw new PDOException("Não consegui incluir a matrícula para atualização!");
                                        }    
                                    } else {
                                        throw new PDOException("Não consegui incluir a matrícula para atualização!");
                                    }        
                                }
                            } else {
                                throw new PDOException("Não consegui identificar a matrícula para atualização!");
                            }
                        }
                    } else {
                        throw new PDOException("Tabela ANAC vazia!");
                    }
                } else {
                    throw new PDOException("Carga das tabelas não pode ser executada!");
                }    
                      
            // Excluir todas as matrículas que não estão mais sendo usados nos status e não foram atualizadas pela importação
                $comando = "DELETE mt FROM gear_matriculas AS mt WHERE (SELECT COUNT(*) FROM gear_status st WHERE st.idMatricula = mt.id) = 0 AND mt.fonte = 'ANAC' AND mt.origem = 'ATU'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    throw new PDOException("Não foi possível excluir as matrículas não atualizadas sem correspondência nos status!");
                }

            // Gravar como INATIVAS todos as matrículas com origem ATU que não foram excluidas (não foram importados mas estão sendo utilizados nos status)
                $comando = "UPDATE gear_matriculas SET situacao = 'INA', fonte = 'IMP' WHERE fonte = 'ATU'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    throw new PDOException("Não foi possível inativar as matrículas não atualizadas!");
                }

            // Excluir todas os operadores que não estão mais sendo usados nas matrículas
                $comando = "DELETE op FROM gear_operadores AS op WHERE (SELECT COUNT(*) FROM gear_matriculas mt WHERE mt.idOperador = op.id) = 0 AND op.fonte = 'ANAC'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    throw new PDOException("Não foi possível excluir os operadores sem correspondência nas matrículas!");
                }

            // Gravar como INATIVOS todos os operadores com origem ATU que não foram excluidos (não foram importados mas estão sendo utilizados nas matrículas)
                $comando = "UPDATE gear_operadores SET situacao = 'INA', fonte = 'IMP' WHERE fonte = 'ATU'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    throw new PDOException("Não foi possível inativar os operadores não atualizados!");
                }

            // Excluir todas os equipamentos que não estão mais sendo usados nas matrículas
                $comando = "DELETE eq FROM gear_equipamentos AS eq WHERE (SELECT COUNT(*) FROM gear_matriculas mt WHERE mt.idEquipamento = eq.id) = 0 AND eq.fonte = 'ANAC'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    throw new PDOException("Não foi possível excluir os equipamentos sem correspondência nas matrículas!");
                }

            // Gravar como INATIVOS todos os equipamentos com origem ATU que não foram excluidos (não foram importados mas estão sendo utilizados nas matrículas)
                $comando = "UPDATE gear_equipamentos SET situacao = 'INA', fonte = 'IMP' WHERE fonte = 'ATU'";
                $sql = $conexao->prepare($comando); 
                if (!$sql->execute()){ 
                    throw new PDOException("Não foi possível inativar os equipamentos não atualizados!");
                }
            }
        } else {
            throw new PDOException("Erro na abertura do arquivo ".$file_name."!");
        }

        $_tipoMsg = ($linhasDesprezadas == 0  && count($erros) == 0 ? "success" : "warning");
        $_mensagens[] = "";
        $_mensagens[] = "Arquivo: ".$file_name;
        $_mensagens[] = "";
        $_mensagens[] = "Total de Linhas = ".$linhasTotal;
        $_mensagens[] = "Linhas de Controle = ".$linhasControle;
        $_mensagens[] = "Linhas Gravadas = ".$linhasGravadas;
        $_mensagens[] = "Integração Gravadas = ".$integracaoGravadas;
        $_mensagens[] = "Operadores = ".$linhasOperadores;
        $_mensagens[] = "Equipamentos = ".$linhasEquipamentos;
        $_mensagens[] = "Matrículas = ".$linhasMatriculas;
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