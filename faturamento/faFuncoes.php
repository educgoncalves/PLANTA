<?php
// Consiste se existe algum faturamento em processamento ou não confirmado
//
function faturamentoEmProcessamento($_idAeroporto){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Não existe faturamento pendente!');
	$_comando = "SELECT id FROM gear_faturamentos fa
					WHERE idAeroporto = ".$_idAeroporto." AND (fa.situacao = 'NCN' OR fa.situacao = 'PRC') LIMIT 1";
    try{
        $_conexao = conexao();
        $_sql = $_conexao->prepare($_comando);
		$_sql->execute(); 
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
            throw new PDOException("Existe faturamento em processamento. Você não pode prosseguir com um novo cálculo sem antes cancelar ou confirmar os faturamentos pendentes!");
        } 
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
    }
    gravaXTrace('faturamentoEmProcessamento '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;	
}

// Incluir Faturamentos
//
function incluirFaturamentos($_idOperador){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Faturamento incluído com sucesso!', 'faturamento'=> '', 'idFaturamento'=> 0);
	$_comando = "SELECT id, CONCAT(fa.ano,'/',fa.numero) as faturamento FROM gear_faturamentos fa WHERE idAeroporto = ".$_SESSION['plantaIDAeroporto'].
				" AND idOperador = ".$_idOperador." AND (fa.situacao = 'NCN' OR fa.situacao = 'PRC') LIMIT 1";
    try{
		$_conexao = conexao();
		$_sql = $_conexao->prepare($_comando); 
		if ($_sql->execute()) {
			if ($_sql->rowCount() > 0) {
				$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);				
				foreach ($_registros as $_dados) {
					$_retorno['idFaturamento'] = $_dados['id'];
					$_retorno['faturamento'] = $_dados['faturamento'];
				} 
			} else {
		        $_comando = "INSERT INTO gear_faturamentos (idAeroporto, idOperador, situacao, cadastro) VALUES (".
								$_SESSION['plantaIDAeroporto'].", ".$_idOperador.", 'PRC', UTC_TIMESTAMP())";
				$_sql = $_conexao->prepare($_comando); 
				if ($_sql->execute()) {
					if ($_sql->rowCount() > 0) {
						$_retorno['idFaturamento'] = $_conexao->lastInsertId();
						$_comando = "SELECT CONCAT(fa.ano,'/',fa.numero) as faturamento 
									FROM gear_faturamentos fa WHERE id = ".$_retorno['idFaturamento'];
						$_sql = $_conexao->prepare($_comando);
						$_sql->execute(); 
						$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
						foreach ($_registros as $_dados) {
							$_retorno['faturamento'] = $_dados['faturamento'];
						} 
                		gravaDLog("gear_faturamentos", "Inclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $_retorno['idFaturamento'] , $_comando, "Faturamento : ".$_retorno['faturamento']);  
            		} else {
                		throw new PDOException("Não foi possível incluir um faturamento!");
            		}
        		} else {
            		throw new PDOException("Não foi possível incluir um faturamento!");
        		} 
			} 
		} else {
			throw new PDOException("Não foi possível acessar o faturamento!");
		} 				
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
    }
    gravaXTrace('incluirFaturamentos '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;	
}

// Calcular tempos de permanência
//
function calcularStatus($_filtro){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Faturamento foi calculado com sucesso!');
	$_calculo = array('ID'=>0, 'POU'=>0, 'DEC'=>0, 'ENT'=>0, 'SAI'=>0, 'EST'=>0, 'MNB'=>0, 'ISE'=>0, 'HNG'=>0, 'NAN'=>0, 
						'POS'=>0, 'Operador'=>0, 'Grupo'=>0, 'Classe'=>'DOM', 'Status'=>'', 'Isencao'=>0);
	$_comando = "SELECT gst.id, gst.idAeroporto, gst.ano, gst.mes, gst.numero, CONCAT(gst.ano,'/',gst.mes,'/',gst.numero) as status, 
						gst.idMatricula, gst.classe, gst.natureza, gst.servico, gst.idOrigem, gst.idDestino, gmo.id as idMovimento, 
						gmo.dhMovimento, DATE_FORMAT(gmo.dhMovimento,'%d/%m/%Y %H:%i') as dataHoraMovimento, gmo.movimento, gmo.idRecurso,
						gcl.tmpIsencao, gcl.tmpRetorno, gre.recurso, gre.tipo, gmt.idOperador, gop.grupo, gmt.pmd, 
						gmt.matricula, IFNULL(gst.idChegada, '') as idChegada, IFNULL(gst.idPartida, '') as idPartida,
						gop.id as idOperador, IFNULL(gre.utilizacao,'ISE') as utilizacao
					FROM gear_status gst
					INNER JOIN gear_status_movimentos gmo ON gmo.idStatus = gst.id 
					INNER JOIN (SELECT DISTINCT st.id
								FROM gear_status st
								LEFT JOIN gear_status_primeiro_movimento mpri ON mpri.idStatus = st.id
                        		LEFT JOIN gear_status_ultimo_movimento mult ON mult.idStatus = st.id
								INNER JOIN gear_matriculas mt ON mt.id = st.idMatricula
								LEFT JOIN gear_operadores op on op.id = mt.idOperador
								WHERE 1 = 1 ".$_filtro.") filtro ON filtro.id = gst.id
					LEFT JOIN gear_recursos gre ON gre.id = gmo.idRecurso
					LEFT JOIN gear_matriculas gmt on gmt.id = gst.idMatricula 									
					LEFT JOIN gear_operadores gop on gop.id = gmt.idOperador 
					INNER JOIN gear_clientes gcl ON gcl.idAeroporto = gst.idAeroporto AND gcl.sistema = 'GEAR'
					ORDER BY gop.id, gst.id, gmo.id";
	gravaXTrace('calcularStatus '.$_comando);					
	try{
		$_conexao = conexao();
		// Abrindo a transação
		$_conexao->beginTransaction();
		$_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			if ($_sql->rowCount() > 0) {
				$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
				foreach ($_registros as $_dados) {
					// Critica o PMD da Matricula
					if ($_dados['pmd'] == 0) {
						throw new PDOException('Matrícula '.$_dados['matricula'].' com PMD zerado!');
					}

                    // Caso matrícula FAB modifica utilização para ISENTO
                    $_dados['utilizacao'] = (substr($_dados['matricula'],0,3) === 'FAB' || 
                                                $_dados['utilizacao'] === 'HNG' ? 'ISE' : $_dados['utilizacao']);

					// Controle do status que está sendo processado
					$_calculo['ID'] = ($_calculo['ID'] == 0 ? $_dados['id'] : $_calculo['ID']);
					
					// Se trocou o status, inclui a permanência, limpa o array e recomeça o cálculo do novo status
					if ($_calculo['ID'] != $_dados['id']) {
						$_retorno = incluirCalculos($_calculo);
						if ($_retorno['tipo'] == "success") {
							foreach ($_calculo as $_idx => $_valor) {
								$_calculo[$_idx] = 0;
							}
							$_calculo['ID'] = $_dados['id'];
						} else {
							throw new PDOException($_retorno['mensagem']);
						}
					}

					// Atualiza informações para o callculo da tarifa
					$_calculo['Operador'] = $_dados['idOperador'];
					// Considera grupo do status como I caso haja voo regular, caso contrario grupo II
					$_calculo['Grupo'] = ($_dados['idChegada'] != '' ? 1 : 2);
					// Utilizar o peso em toneladas
					$_calculo['PMD'] = (fmod($_dados['pmd'],1000) != 0 ? intdiv($_dados['pmd'],1000) + 1 : $_dados['pmd']/1000);
					$_calculo['Classe'] = $_dados['classe'];
					$_calculo['Status'] = $_dados['status'];
					$_calculo['Isencao'] = $_dados['tmpIsencao'];
					$_calculo['Retorno'] = $_dados['tmpRetorno'];

					// Processa o cálculo de acordo com o movimento 
					switch ($_dados['movimento']) {
						case 'POU':
							$_calculo['POU'] = $_dados['dhMovimento'];
						break;
						case 'ENT':
							// Se for o primeiro movimento de entrada, utiliza a data e hora do pouso para o cálculo da permanência
							$_calculo['ENT'] = ($_calculo['ENT'] == 0 ? $_calculo['POU'] : $_dados['dhMovimento']);
							$_calculo['SAI'] = 0;
						break;
						case 'SAI':
							// Salva a utilização da posição da última saída para somar a diferença para o horário de decolagem 
							// Se existe tolerancia de Retorno
							// 		Se saiu anteriormete do Hangar ou da Estadia e entrou em Manobras 
							// 			cobrar as primeiras horas de Retorno como Estadia
							//
							$_normal = true;
							if ($_calculo['Retorno'] != 0) {
								// Converter para segundos
								$_tmpRetorno = $_calculo['Retorno'] * 60;
								if (($_calculo['POS'] == 'HNG' || $_calculo['POS'] == 'EST') && ($_dados['utilizacao'] == 'MNB')) {
									$_normal = false;
									$_calculo['POS'] = $_dados['utilizacao'];
									$_calculo['SAI'] = $_dados['dhMovimento'];
									$_tmp = (strtotime($_calculo['SAI']) - strtotime($_calculo['ENT']));
									if ($_tmp <= $_tmpRetorno) {
										$_calculo['EST'] += $_tmp;
									} else {
										$_calculo['EST'] += $_tmpRetorno;
										$_calculo['MNB'] += ($_tmp - $_tmpRetorno);
									}
								}
							}
							if ($_normal) {
								$_calculo['POS'] = $_dados['utilizacao'];
								$_calculo['SAI'] = $_dados['dhMovimento'];
								$_calculo[$_calculo['POS']] += (strtotime($_calculo['SAI']) - strtotime($_calculo['ENT']));
							}
						break;
						case 'DEC':
							// Se hoveram movimentos, soma a diferença da data e hora da Decolagem para a última Saída
							// Se não houveram movimentos, calcular a permanência em Pátio utilizando a data e hora do Pouso e Decolagem
							$_calculo['DEC'] = $_dados['dhMovimento'];
							if ($_calculo['MNB']+$_calculo['EST']+$_calculo['ISE'] != 0) {
								$_calculo[$_calculo['POS']] += (strtotime($_calculo['DEC']) - strtotime($_calculo['SAI']));
							} else {
								$_calculo['MNB'] += (strtotime($_calculo['DEC']) - strtotime($_calculo['POU']));
							}
						break;
					}
					gravaXTrace("Status ".$_calculo['Status']." POU ".$_calculo['POU']." ENT ".$_calculo['ENT']." SAI ".$_calculo['SAI']." DEC ".
								$_calculo['DEC']." - PAT ".$_calculo['MNB']." EST ".$_calculo['EST']." ISE ".$_calculo['ISE']." Posição ".$_calculo['POS']);
				}

				// Incluir a permanência caso haja resíduo no vetor do cálculo
				if ($_calculo['ID'] != 0){
					$_retorno = incluirCalculos($_calculo);
					if ($_retorno['tipo'] == "danger") {
						throw new PDOException($_retorno['mensagem']);
					}					
				}
				// Faturamento calculado com sucesso!
				// Commit da transação
				$_conexao->commit();
				$_retorno['tipo'] = 'success';
				$_retorno['mensagem'] = 'Faturamento foi calculado com sucesso!';
            } else {
                throw new PDOException("Não foi possível calcular as mensagens!");
            }
        } else {
			throw new PDOException("Não foi possível calcular as mensagens!");
        } 
	} catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
		// Rollback da transação
		if ($_conexao->inTransaction()) {$_conexao->rollBack();}
    }
	gravaXTrace('calcularStatus '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
	return $_retorno;
}

// Confirmar cálculo das permanências atualizando o status e gerando o faturamento
//
function confirmarCalculo($_idAeroporto, $_faturamento, $_idFaturamento){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Faturamento '.$_faturamento.' - Cálculo confirmado com sucesso!');
	$_comando = "UPDATE gear_status st 
					SET st.faturado = 'SIM', 
						st.situacao = (CASE 
										WHEN (SELECT um.movimento 
												FROM gear_status_ultimo_movimento um WHERE um.idStatus = st.id) = 'DEC'
										THEN 'FCH' ELSE st.situacao END)
					WHERE st.id in  
						(SELECT ca.idStatus 
							FROM gear_calculos ca
							LEFT JOIN gear_faturamentos fa 
								ON fa.idAeroporto = ".$_idAeroporto." AND fa.id = ca.idFaturamento
							WHERE fa.situacao = 'PRC' AND ca.situacao = 'NCN')";
	try{
		$_conexao = conexao();
		// Abrindo a transação
		$_conexao->beginTransaction();

		// Atualizando os status
		$_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			if ($_sql->rowCount() > 0) {
				// Atualizando as mensagens 
				$_comando = "UPDATE gear_calculos ca SET ca.situacao = 'CNF', ca.cadastro = UTC_TIMESTAMP() 
								WHERE ca.situacao = 'NCN' 
									AND ca.idFaturamento in (SELECT fa.id 
																FROM gear_faturamentos fa 
																WHERE fa.idAeroporto = ".$_idAeroporto." AND fa.situacao = 'PRC')";
				$_sql = $_conexao->prepare($_comando);
				if ($_sql->execute()) {
					if ($_sql->rowCount() > 0) {
						// Atualizando o faturamento 
						$_comando = "UPDATE gear_faturamentos fa SET fa.situacao = 'CNF', fa.cadastro = UTC_TIMESTAMP() 
										WHERE fa.idAeroporto = ".$_idAeroporto." AND fa.situacao = 'PRC'";
						$_sql = $_conexao->prepare($_comando);
						if ($_sql->execute()) {
							if ($_sql->rowCount() > 0) {
								// Faturamento confirmado com sucesso!
								// Commit da transação
								gravaDLog("gear_faturamentos", "Confirmação", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $_idFaturamento , $_comando, "Faturamento : ".$_faturamento,);  
								$_conexao->commit();
							} else {
								throw new PDOException("[".$_faturamento."] Não foi possível confirmar o cálculo!");				
							}
						} else {
							throw new PDOException("[".$_faturamento."] Não foi possível confirmar o cálculo!");				
						}
					} else {
						throw new PDOException("[".$_faturamento."] Não foi possível atualizar o cálculo!");				
					}
				} else {
					throw new PDOException("[".$_faturamento."] Não foi possível atualizar o cálculo!");		
				}				
			} else {
				throw new PDOException("[".$_faturamento."] Não foi possível atualizar os status!");				
			}
		} else {
			throw new PDOException("[".$_faturamento."] Não foi possível atualizar os status!");		
		}
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
		// Rollback da transação
		if ($_conexao->inTransaction()) {$_conexao->rollBack();}
    }
	gravaXTrace('confirmarCalculo '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;
}

// Incluir calculos
//
function incluirCalculos($__calculo) {
	$__retorno = array('tipo'=> 'success','mensagem'=> 'Cálculo incluído com sucesso!', 'faturamento'=> '', 'idFaturamento'=> 0);
	$__comando = "SELECT domTPO, domTPM, domTPE, intTPO, intTPM, intTPE, domTPOF, domTPMF, domTPEF, intTPOF, intTPMF, intTPEF 
					FROM gear_tarifas WHERE grupo = ".$__calculo['Grupo'];
	if ($__calculo['Grupo'] == '2') {
		$__comando .= " AND ((".$__calculo['PMD']." > inicioPMD AND ".$__calculo['PMD']." <= finalPMD) OR (".
                        $__calculo['PMD']." > inicioPMD AND finalPMD = 0))";
	}	

    try{
        $__conexao = conexao();

		// Pega a tarifa especifica do aeroporto, caso não ache pegar a tarifa geral (idAeroporto = 0)
		// Query de teste
		// SELECT inicioPMD, finalPMD, domTPO, domTPM, domTPE, intTPO, intTPM, intTPE FROM gear_tarifas WHERE grupo = 1 AND ((inicioPMD <= 10000 AND finalPMD < 10000) OR finalPMD = 0) AND idAeroporto = 5 LIMIT 1
		//
 		$__sql = $__conexao->prepare($__comando." AND idAeroporto = ".$_SESSION['plantaIDAeroporto']." LIMIT 1"); 
		if ($__sql->execute()) {
			if ($__sql->rowCount() == 0) {
				$__sql = $__conexao->prepare($__comando." AND idAeroporto = 0 LIMIT 1"); 
				if ($__sql->execute()) {
					if ($__sql->rowCount() == 0) {
						throw new PDOException("Não foi possível pegar a tarifa correspondente ao status ".$__calculo['Status']." [PMD ".$__calculo['PMD']."] !");
					} 
				} else {
					throw new PDOException("Não foi possível pegar a tarifa correspondente ao status ".$__calculo['Status']." [PMD ".$__calculo['PMD']."] !");
				} 
			}
		} else {
			throw new PDOException("Não foi possível pegar a tarifa correspondente ao status ".$__calculo['Status']."!");
        } 

		// Calcular isenção
		if ($__calculo['Isencao'] != 0){
			// Coverter em segundos
			$__isencao = $__calculo['Isencao'] * 60;
			// Descontando do tempo de Pátio
			if ($__isencao > $__calculo['MNB']){
				$__isencao -= $__calculo['MNB'];
				$__calculo['MNB'] = 0;
				// Descontando do tempo de Estadia
				if ($__isencao > $__calculo['EST']){
					$__isencao -= $__calculo['EST'];
					$__calculo['EST'] = 0;
					// Descontando do tempo de Isencao
					if ($__isencao > $__calculo['ISE']){
						$__isencao -= $__calculo['ISE'];
						$__calculo['ISE'] = 0;
					} else {
						$__calculo['ISE'] -= $__isencao;
					}
				} else {
					$__calculo['EST'] -= $__isencao;
				}
			} else {
				$__calculo['MNB'] -= $__isencao;
			}
		}

		// Arredondando a conversão de segundos para horas
		// Pátio
		$__calculo['MNB'] = intdiv($__calculo['MNB'],3600) + (fmod($__calculo['MNB'],3600) != 0 ? 1 : 0);
		// Estadia
		$__calculo['EST'] = intdiv($__calculo['EST'],3600) + (fmod($__calculo['EST'],3600) != 0 ? 1 : 0);
		// Isento
		$__calculo['ISE'] = intdiv($__calculo['ISE'],3600) + (fmod($__calculo['ISE'],3600) != 0 ? 1 : 0);

		// Cálculo das tarifas
		$__registros = $__sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($__registros as $__dados) {
			if ($__calculo['Grupo'] == '1') {
				// PPO = TPOF + (TPO x PMD)
				$__vlrPPO = ($__calculo['Classe'] == 'DOM' ? $__dados['domTPOF'] : $__dados['intTPOF']) +
							(($__calculo['Classe'] == 'DOM' ? $__dados['domTPO'] : $__dados['intTPO']) * $__calculo['PMD']);
				// PPM = (TPMF + (TPM * PMD)) * permanencia
				$__vlrPPM = (($__calculo['Classe'] == 'DOM' ? $__dados['domTPMF'] : $__dados['intTPMF']) +
							(($__calculo['Classe'] == 'DOM' ? $__dados['domTPM'] : $__dados['intTPM']) * $__calculo['PMD'])) * 
							$__calculo['MNB'];
				// PPE = (TPEF + (TPE * PMD)) * permanencia
				$__vlrPPE = (($__calculo['Classe'] == 'DOM' ? $__dados['domTPEF'] : $__dados['intTPEF']) +
							(($__calculo['Classe'] == 'DOM' ? $__dados['domTPE'] : $__dados['intTPE']) * $__calculo['PMD'])) *
							$__calculo['EST'];
			} else {
				// PPO = TPOF + TPO
				$__vlrPPO = ($__calculo['Classe'] == 'DOM' ? $__dados['domTPOF'] : $__dados['intTPOF']) +
							($__calculo['Classe'] == 'DOM' ? $__dados['domTPO'] : $__dados['intTPO']);
				// PPM = (TPMF + TPM) * permanencia
				$__vlrPPM = (($__calculo['Classe'] == 'DOM' ? $__dados['domTPMF'] : $__dados['intTPMF']) +
							($__calculo['Classe'] == 'DOM' ? $__dados['domTPM'] : $__dados['intTPM'])) * $__calculo['MNB'];
				// PPE = (TPEF + TPE) * permanencia
				$__vlrPPE = (($__calculo['Classe'] == 'DOM' ? $__dados['domTPEF'] : $__dados['intTPEF']) +
							($__calculo['Classe'] == 'DOM' ? $__dados['domTPE'] : $__dados['intTPE'])) * $__calculo['EST'];
			}
		}

		// Verifica se já existe faturamento aberto para esta empresa
		$__retorno = incluirFaturamentos($__calculo['Operador']);
        if ($__retorno['tipo'] == 'success') {
            $__idFaturamento = $__retorno['idFaturamento'];
		} else {
			throw new PDOException($__retorno['mensagem']);
		}

		// Verificar se ja existe calculo para este status e faz o acerto
		$__antPPO = 0;
		$__antPPM = 0;
		$__antPPE = 0;
        $__comando = "SELECT SUM(vlrPPO) as antPPO, SUM(vlrPPM) as antPPM, SUM(vlrPPE) as antPPE FROM gear_calculos WHERE idStatus = ".$__calculo['ID'];
		$__sql = $__conexao->prepare($__comando); 
		if ($__sql->execute()) {
			if ($__sql->rowCount() > 0) {
				$__registros = $__sql->fetchAll(PDO::FETCH_ASSOC);
				foreach ($__registros as $__dados) {
					$__antPPO += $__dados['antPPO'];
					$__antPPM += $__dados['antPPM'];
					$__antPPE += $__dados['antPPE'];
				} 
			}
		}
		$__vlrPPO -= $__antPPO;
		$__vlrPPM -= $__antPPM;
		$__vlrPPE -= $__antPPE;

		// Inserir o cálculo
		$__pouso = mudarDataHoraAMD(($__calculo['POU']==0 ? null : $__calculo['POU']));
		$__decolagem = mudarDataHoraAMD(($__calculo['DEC']==0 ? null : $__calculo['DEC']));
        $__comando = "INSERT INTO gear_calculos (idFaturamento, idStatus, dhPouso, dhDecolagem, tmpPatio, tmpEstadia, tmpIsento, vlrPPO, vlrPPM, vlrPPE, situacao, cadastro) VALUES (".
						$__idFaturamento.", ".$__calculo['ID'].", ".$__pouso.", ".$__decolagem.", ".$__calculo['MNB'].", ".$__calculo['EST'].", ".
						$__calculo['ISE'].", ".$__vlrPPO.", ".$__vlrPPM.", ".$__vlrPPE.", 'NCN', UTC_TIMESTAMP())";
		$__sql = $__conexao->prepare($__comando); 
		if ($__sql->execute()) {
			if ($__sql->rowCount() > 0) {
				$__retorno['tipo'] = 'success';
				$__retorno['mensagem'] = 'Cálculo do status '.$__calculo['Status'].' realizado com sucesso!';
			} else {
				throw new PDOException("Não foi possível calcular o status ".$__calculo['Status']."!");
            }
        } else {
			throw new PDOException("Não foi possível calcular o status ".$__calculo['Status']."!");
        } 
    } catch (PDOException $e) {
		$__retorno['tipo'] = 'danger';
		$__retorno['mensagem'] = traduzPDO($e->getMessage());
    }
    gravaXTrace('incluirCalculos '.$__retorno['tipo'].' '.$__retorno['mensagem'].' '.$__comando);
    return $__retorno;
}

// Cancelar cálculos pendentes e suas mensagens
//
function cancelarCalculosPendentes($_idAeroporto){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Cálculos pendentes foram cancelados com sucesso!');
	$_comando = "SELECT fa.id, CONCAT(fa.ano,'/',fa.numero) as faturamento, DATE_FORMAT(fa.cadastro,'%d/%m/%Y %H:%i') as cadastro, descricao as descSituacao
					FROM gear_faturamentos fa
					LEFT JOIN gear_dominios dm 
							ON dm.tabela = 'planta_faturamentos' AND dm.coluna = 'situacao' AND dm.codigo = fa.situacao
					WHERE idAeroporto = ".$_idAeroporto." && (fa.situacao = 'NCN' || fa.situacao = 'PRC')";
    try{
        $_conexao = conexao();
        $_sql = $_conexao->prepare($_comando);
		$_sql->execute(); 
		if ($_sql->rowCount() > 0) {
			$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
			foreach ($_registros as $_dados) {
				$_comando = "DELETE FROM gear_faturamentos WHERE id = ".$_dados['id'];
				$sql = $_conexao->prepare($_comando); 
				if ($sql->execute()){
					gravaDLog("gear_faturamentos", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $_dados['id'], $_comando, 
								"Faturamento: ".$_dados['faturamento']." de ".$_dados['cadastro'].", com situação de ".$_dados['descSituacao']);   
				} else {
					throw new PDOException("Cálculo não pode ser cancelado!");
				}
			} 
		} else {
			throw new PDOException("Não há calculo para ser cancelado!");
		}
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
    }
    gravaXTrace('cancelarCalculo '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;
}

// Confirmar faturamento e suas mensagens
//
function cancelarFaturamentoConfirmado($_faturamento, $_idFaturamento){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Faturamento '.$_faturamento.' cancelado com sucesso!');
	$_comando = "UPDATE gear_status st SET st.faturado = 'NAO', st.situacao = 'ATV'
					WHERE st.id in (SELECT ca.idStatus 
									FROM gear_faturamentos fa
									LEFT JOIN gear_calculos ca ON ca.idFaturamento = fa.id 
									WHERE fa.id = ".$_idFaturamento.")";
	try{
		$_conexao = conexao();
		// Abrindo a transação
		$_conexao->beginTransaction();

		// Atualizando os status
		$_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			// if ($_sql->rowCount() > 0) {
				// Excluindo o faturamento
				$_comando = "DELETE FROM gear_faturamentos WHERE id = ".$_idFaturamento;
				$_sql = $_conexao->prepare($_comando);
				if ($_sql->execute()) {
					if ($_sql->rowCount() > 0) {
						// Faturamento confirmado com sucesso!
						// Commit da transação
						gravaDLog("gear_faturamentos", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $_idFaturamento , $_comando, "Faturamento : ".$_faturamento,);  
						$_conexao->commit();
					} else {
						throw new PDOException("[".$_faturamento."] Não foi possível cancelar o faturamento!");				
					}
				} else {
					throw new PDOException("[".$_faturamento."] Não foi possível cancelar o faturamento!");				
				}
			// } else {
			// 	throw new PDOException("[".$_faturamento."] Não foi possível atualizar os status deste faturamento!");				
			// }
		} else {
			throw new PDOException("[".$_faturamento."] Não foi possível atualizar os status deste faturamento!");		
		}
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
		// Rollback da transação
		if ($_conexao->inTransaction()) {$_conexao->rollBack();}
    }
	gravaXTrace('cancelarFaturamentoConfirmado '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;
}

// Gerar integracao ASAAS
//
function integracaoASAAS($_aeroporto, $_siglaAeroporto, $_idUsuario, $_filtro, $_utc) {
    $_retorno = array('tipo'=> 'success','mensagem'=> 'Integração ASAAS gerada com sucesso! ','remessa'=> '');
	$_comando = selectDB("StatusFaturamento",$_filtro,"faturamento, status");

    try {
        // Abrindo as conexões necessárias
        $_conexao = conexao();
        $_integracao = integracao();

        // Verificar se existem apenas Faturas pendentes
        $_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			if ($_sql->rowCount() > 0) {
				$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
				foreach ($_registros as $_dados) {
                    if (!empty($_dados['dhFatura']) || !empty($_dados['dhPagamento'])) {
                        throw new PDOException("Integração ASAAS - Todas as Faturas filtradas devem estar em situação PENDENTE!");
                    }
                }
            } else {
                throw new PDOException("Integração ASAAS - Não foi possível acessar os dados do faturamento!");
            }
        } else {
			throw new PDOException("Integração ASAAS - Não foi possível acessar os dados do faturamento!");
        } 

        // Iniciando a integração
        // Data e hora local do Aeroporto
        $_date = dateTimeUTC($_utc)->format('Y-m-d H:i');

		// Abrindo as transações
        $_conexao->beginTransaction();
        $_integracao->beginTransaction();

        // Pegando as informações do faturamento
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        
        // Gerar o controle de Remessa
        $_remessa = '';
        $_idRemessa = 0;
        $_comando = "INSERT INTO gear_remessas(idAeroporto, idUsuario, cadastro) VALUES (".
                    $_aeroporto.",".$_idUsuario.",UTC_TIMESTAMP())";
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) {
                $_idRemessa = $_conexao->lastInsertId();
                $_comando = "SELECT CONCAT(rm.ano,'/',rm.numero) as remessa FROM gear_remessas rm WHERE id = ".$_idRemessa;
                $_sql = $_conexao->prepare($_comando);
                if ($_sql->execute()) {
                    if ($_sql->rowCount() > 0) {
                        $_remessa = $_sql->fetch(PDO::FETCH_ASSOC)['remessa'];
                    } else {
                        throw new PDOException("Integração ASAAS - Não foi possível acessar o controle da remessa!");
                    }
                } else {
                    throw new PDOException("Integração ASAAS - Não foi possível acessar o controle da remessa!");
                }                                
            } else {
                throw new PDOException("Integração ASAAS - Não foi possível gerar o controle da remessa!");
            }
        } else {
            throw new PDOException("Integração ASAAS - Não foi possível gerar o controle da remessa!");
        }

        // Inicia a geração da remessa
        $_qtdFaturas = 0;
        $_qtdLinhas = 0;
        $_vlrTotal = 0;
        $_faturamentoAnterior = '';

        foreach ($_registros as $_dados) {
            $_comando = "INSERT INTO faturamento_maer_asaas (aeroporto, remessa, faturamento, data, operador, status, matricula, ". 
                        "classe, natureza, servico, origem, destino, primeiro_movimento, ultimo_movimento, patio, estadia, isento, ". 
                        "ppo, ppm, ppe, cobranca, cpf_cnpj, endereco, contato, cadastro) VALUES ('".
                        $_siglaAeroporto."','".$_remessa."','".$_dados['faturamento']."','".$_dados['cadastro']."','".
                        $_dados['operadorOperacao']."','".$_dados['status']."','".$_dados['matricula']."','".
                        $_dados['classe']."','".$_dados['natureza']."','".$_dados['servico']."','".$_dados['origem']."','".
                        $_dados['destino']."','".$_dados['moPrimeiroMovimento'].' - '.$_dados['dataHoraPrimeiroMovimento']."','".
                        $_dados['moUltimoMovimento'].' - '.$_dados['dataHoraUltimoMovimento']."',".$_dados['tmpPatio'].",".
                        $_dados['tmpEstadia'].",".$_dados['tmpIsento'].",".$_dados['vlrPPO'].",".$_dados['vlrPPM'].",". 
                        $_dados['vlrPPE'].",'".$_dados['operadorCobranca']."','".$_dados['cpfCnpjCobranca']."','".
                        $_dados['enderecoCompleto']."','".$_dados['contatoCompleto']."', UTC_TIMESTAMP())";
            
            $_sql = $_integracao->prepare($_comando);
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_qtdLinhas++;
                    $_vlrTotal += $_dados['vlrPPO'] + $_dados['vlrPPM'] + $_dados['vlrPPE'];
                    if ($_faturamentoAnterior != $_dados['faturamento']) {
                        $_qtdFaturas++;
                        $_faturamentoAnterior = $_dados['faturamento'];
                    }
                    
                    // Atualizando a fatura com o número da remessa
                    $_comando = "UPDATE gear_faturamentos SET fatura = '".$_date."', idRemessa = ".$_idRemessa.
                                " WHERE id = ".$_dados['idFatura'];
                    $_sql = $_conexao->prepare($_comando);
                    if ($_sql->execute()) {
                        if ($_sql->rowCount() > 0) {
                        } else {
                            throw new PDOException("Integração ASAAS - Não foi possível atualizar a fatura!");
                        }
                    } else {
                        throw new PDOException("Integração ASAAS - Não foi possível atualizar a fatura!");
                    } 
                } else {
                    throw new PDOException("Integração ASAAS - Não foi possível gerar o registro na integração!");
                }
            } else {
                throw new PDOException("Integração ASAAS - Não foi possível gerar o registro na integração!");
            }
        }

        // Atualiza controle da remessa
        $_comando = "UPDATE gear_remessas SET qtdFaturas = ".$_qtdFaturas.", qtdLinhas = ".$_qtdLinhas.
                    ", vlrTotal = ".$_vlrTotal." WHERE id = ".$_idRemessa;
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) {
                $_retorno['remessa'] = "Remessa ".$_remessa." com ".$_qtdFaturas." fatura(s) - R$ ".$_vlrTotal;  
            } else {
                throw new PDOException("Integração ASAAS - Não foi possível atualizar o controle da remessa!");
            }
        } else {
            throw new PDOException("Integração ASAAS - Não foi possível atualizar o controle da remessa!");
        } 
        
        $_conexao->commit();
        $_integracao->commit();

	} catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
        if ($_conexao->inTransaction()) {$_conexao->rollBack();}
        if ($_integracao->inTransaction()) {$_integracao->rollBack();}
    }

	gravaXTrace('integração ASAAS '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
	return $_retorno;
}

// Confirmar faturamento e suas mensagens
//
function baixarArquivoCSV($_filtro, $_utc){
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Arquivo CSV gerado com sucesso!','arquivo'=> '');
	$_comando = selectDB("StatusFaturamento",$_filtro,"faturamento, status");
    try{
		$_conexao = conexao();
		$_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			if ($_sql->rowCount() > 0) {

				// Data e hora local do aeroporto
				$_date = dateTimeUTC($_utc)->format('Ymd_His');
				$_file = '../arquivos/planilhas/'.$_SESSION['plantaAeroporto'].'_Movimentos_Faturados_'.$_date.'.csv';
				$_arquivo = fopen($_file,'w');
				$_cabecalho = ['Faturamento', 'Data', 'Operador', 'Status', 
								mb_convert_encoding('Matrícula', "ISO-8859-1", "UTF-8"), 'Classe', 'Natureza', 'Tipo Serviço', 
								mb_convert_encoding('origem', "ISO-8859-1", "UTF-8"), 'Destino', 'Primeiro Movimento', 
								mb_convert_encoding('Último Movimento', "ISO-8859-1", "UTF-8"),
                                mb_convert_encoding('Dh.Fatura', "ISO-8859-1", "UTF-8"),
                                mb_convert_encoding('Dh.Pagamento', "ISO-8859-1", "UTF-8"),
                                mb_convert_encoding('Remessa ASAAS', "ISO-8859-1", "UTF-8"),
								mb_convert_encoding('Pátio', "ISO-8859-1", "UTF-8"), 'Estadia', 'Isento', 'PPO', 'PPM', 'PPE',
								mb_convert_encoding('Cobrança', "ISO-8859-1", "UTF-8"),'CPF/CNPJ',
								mb_convert_encoding('Endereço', "ISO-8859-1", "UTF-8"),'Contato','Aeroporto'];
				fputcsv($_arquivo, $_cabecalho, ';'); 

				$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
				foreach ($_registros as $_dados) {
					// Ajeitando a ordem dos campos
					$_linha = [$_dados['faturamento'], $_dados['dhConfirmacaoFaturamento'], 
								mb_convert_encoding($_dados['operadorOperacao'], "ISO-8859-1", "UTF-8"), 
								$_dados['status'], $_dados['matricula'], 
								$_dados['classe'], $_dados['natureza'], $_dados['servico'], $_dados['origem'], 
								$_dados['destino'], 
								$_dados['moPrimeiroMovimento'].' - '.$_dados['dataHoraPrimeiroMovimento'],  
								$_dados['moUltimoMovimento'].' - '.$_dados['dataHoraUltimoMovimento'],  
                                $_dados['dhFatura'],
                                $_dados['dhPagamento'],
                                $_dados['remessa'],
								$_dados['tmpPatio'], $_dados['tmpEstadia'], $_dados['tmpIsento'],
								str_replace('.',',',$_dados['vlrPPO']), str_replace('.',',',$_dados['vlrPPM']), 
								str_replace('.',',',$_dados['vlrPPE']),
								mb_convert_encoding($_dados['operadorCobranca'], "ISO-8859-1", "UTF-8"),
								$_dados['cpfCnpjCobranca'],
								mb_convert_encoding($_dados['enderecoCompleto'], "ISO-8859-1", "UTF-8"),
								mb_convert_encoding($_dados['contatoCompleto'], "ISO-8859-1", "UTF-8"),$_SESSION['plantaAeroporto']];
					fputcsv($_arquivo, $_linha, ';'); 
				}
				fclose($_arquivo);
				$_retorno['arquivo'] = $_file;
            } else {
                throw new PDOException("Não foi possível gerar o arquivo CSV!");
            }
        } else {
			throw new PDOException("Não foi possível gerar o arquivo CSV!");
        } 
	} catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
    }

	gravaXTrace('baixarArquivoCSV '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
	return $_retorno;
}

// Atualiza emissão da fatura
function atualizarEmissaoFatura($_idFaturamento, $_faturamento, $_fatura) {
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Fatura '.$_faturamento.
											' '.($_fatura != '' ? 'emitida' : 'cancelada').' com sucesso!');
	$_comando = "UPDATE gear_faturamentos fa SET fa.fatura = '".$_fatura."'	WHERE fa.id = ".$_idFaturamento;
	try{
		$_conexao = conexao();
		// Abrindo a transação
		$_conexao->beginTransaction();

		// Atualizando os status
		$_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			gravaDLog("gear_faturamentos", ($_fatura != '' ? 'Emissão' : 'Cancelamento'), $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $_idFaturamento , $_comando, "Faturamento : ".$_faturamento,);  
			$_conexao->commit();
		} else {
			throw new PDOException("[".$_faturamento."] Não foi possível atualizar este faturamento!");		
		}
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
		// Rollback da transação
		if ($_conexao->inTransaction()) {$_conexao->rollBack();}
    }
	gravaXTrace('atualizarEmissaoFatura '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;
}

// Atualiza emissão da fatura
function atualizarPagamentoFatura($_idFaturamento,$_faturamento,$_pagamento) {
	$_pagamento = mudarDataHoraAMDHM($_pagamento);
	$_retorno = array('tipo'=> 'success','mensagem'=> 'Pagamento '.$_faturamento.
					' '.($_pagamento != 'null' ? 'efetuado' : 'cancelado').' com sucesso!');
	$_comando = "UPDATE gear_faturamentos fa SET fa.pagamento = ".$_pagamento." WHERE fa.id = ".$_idFaturamento;
	try{
		$_conexao = conexao();
		// Abrindo a transação
		$_conexao->beginTransaction();

		// Atualizando os status
		$_sql = $_conexao->prepare($_comando);
		if ($_sql->execute()) {
			gravaDLog("gear_faturamentos", "Pagamento", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $_idFaturamento , $_comando, "Faturamento : ".$_faturamento,);  
			$_conexao->commit();
		} else {
			throw new PDOException("[".$_faturamento."] Não foi possível atualizar este faturamento!");		
		}
    } catch (PDOException $e) {
		$_retorno['tipo'] = 'danger';
		$_retorno['mensagem'] = traduzPDO($e->getMessage());
		// Rollback da transação
		if ($_conexao->inTransaction()) {$_conexao->rollBack();}
    }
	gravaXTrace('atualizarPagamentoFatura '.$_retorno['tipo'].' '.$_retorno['mensagem'].' '.$_comando);
    return $_retorno;
}

// Funções para gerar PIX Codigo e QRCode 
//
function pixQRCode($_chave,$_transacao,$_valor) {
    // // Exemplos de chave PIX
    // // E-mail: nome@exemplo.com.br
    // // CPF: 12345678901 (só números)
    // // CNPJ: 12345678000123 (só números)
    // // Celular: +5511912345678 (+55 + DDD + número)
    // $_chave = carregarPosts("chave", "educgoncalves@gmail.com");
    // // Valor da transação
    // $_valor = carregarPosts("valor", "1.0");
    // // Identificador único da transação, caso exista
    // $_transacao = carregarPosts("transacao", "");

    // Obtem código copia e cola do PIX
    $_codigoPix = pixQrCode_Gerar($_chave, $_transacao, $_valor);
    
    // Exibe o QRCode com o PIX
    // echo '<p><img src="https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . urlencode($_codigoPix) . '"></p>';
    // Exibe o Código PIX (copia e cola)
    // echo "<p>Código PIX: " . $_codigoPix . "<p>";

    return $_codigoPix;
}

function pixQrCode_FormataCampo($id, $valor) {
    return $id . str_pad(strlen($valor), 2, '0', STR_PAD_LEFT) . $valor;
}

function pixQrCode_CalculaCRC16($dados) {
    $resultado = 0xFFFF;
    for ($i = 0; $i < strlen($dados); $i++) {
        $resultado ^= (ord($dados[$i]) << 8);
        for ($j = 0; $j < 8; $j++) {
            if ($resultado & 0x8000) {
                $resultado = ($resultado << 1) ^ 0x1021;
            } else {
                $resultado <<= 1;
            }
            $resultado &= 0xFFFF;
        }
    }
    return strtoupper(str_pad(dechex($resultado), 4, '0', STR_PAD_LEFT));
}

function pixQrCode_Gerar($chave, $idTx = '', $valor = 0.00) {
    $resultado = "000201";
    $resultado .= pixQrCode_FormataCampo("26", "0014br.gov.bcb.pix" . pixQrCode_FormataCampo("01", $chave));
    $resultado .= "52040000"; // Código fixo
    $resultado .= "5303986";  // Moeda (Real)
    if ($valor > 0) {
        $resultado .= pixQrCode_FormataCampo("54", number_format($valor, 2, '.', ''));
    }
    $resultado .= "5802BR"; // País
    $resultado .= "5914DECOLAMAIS.COM";  // Nome
    $resultado .= "6014Rio de Janeiro";  // Cidade
    $resultado .= pixQrCode_FormataCampo("62", pixQrCode_FormataCampo("05", $idTx ?: '***'));
    $resultado .= "6304"; // Início do CRC16
    $resultado .= pixQrCode_CalculaCRC16($resultado); // Adiciona o CRC16 ao final
    return $resultado;
}
?>