<?php
function inMovimentosGrupoI($_idAeroporto, $_page, $_limite) {
    try {
        $conexao = conexao();
        $filtro =  " AND vo.idAeroporto = ".$_idAeroporto." AND vo.situacao = 'ATV' AND vm.movimento <> 'CND'";
        $order = "vo.dhPrevista,vo.operacao,vo.operador,vo.numeroVoo";
        $comando = selectDB("UltimosMovimentosVoos", $filtro, $order)." LIMIT ".(($_page - 1) * $_limite).",".$_limite;

        $sql = $conexao->prepare($comando);
        $sql->execute(); 
        $qtdRegistros = $sql->rowCount();
        $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
        $htmlTabela = "<table class='table table-striped table-hover table-bordered table-reduzida table-sm table-dark rounded-3 overflow-hidden'>".
                        "<thead class='table-info'><tr>".
                        "<th class='tdCabecalho'>Cia</th>".
                        "<th class='tdCabecalho'>Voo</th>".
                        "<th class='tdCabecalho'>Confirmado</th>".
                        "<th class='tdCabecalho'>Origem</th>".
                        "<th class='tdCabecalho'>Destino</th>".
                        "<th class='tdCabecalho'>Equipamento</th>".
                        "<th class='tdCabecalho'>Movimento</th>".
                        "<th class='tdCabecalho'>Dh.Movimento</th>".
                        "<th class='tdCabecalho'>Posição</th>".
                        "<th class='tdCabecalho'>Esteira</th>".
                        "<th class='tdCabecalho'>Portão</th>".
                        "</tr></thead><tbody>";
        foreach ($registros as $dados) {
            // Destaca o movimento
            $destaque = "class='tdDadosGrI ".($dados['destaque'] != '' ? "table-".$dados['destaque'] : '')."'";

            $htmlTabela .= "<tr>".
                            "<td><object data='../siv/img/IM_".$dados['operador'].".jpg'?nocache='.time().' type='image/jpg' ".
                                "class='cia'>".$dados['operador']."</object></td>".
                            "<td class='tdDadosGrI'>".$dados['numeroVoo']."</td>".
                            "<td class='tdDadosGrI'>".$dados['dhConfirmada']."</td>".
                            "<td class='tdDadosGrI'>".$dados['origem']."</td>".
                            "<td class='tdDadosGrI'>".$dados['destino']."</td>".
                            "<td class='tdDadosGrI'>".$dados['equipamento']."</td>".
                            "<td ".$destaque.">".$dados['descMovimento']."</td>".
                            "<td class='tdDadosGrI'>".$dados['dataHoraMovimento']."</td>".
                            "<td class='tdDadosGrI'>".$dados['posicao']."</td>".
                            "<td class='tdDadosGrI'>".$dados['esteira']."</td>".
                            "<td class='tdDadosGrI'>".$dados['portao']."</td>".
                            "</tr>";
        } 
        // Completa a tela até o limite
        for ($i = $qtdRegistros; $i < $_limite; $i++) {
            $htmlTabela .= "<tr>";
            for($j = 1; $j <= 11; $j++) {
                $htmlTabela .= "<td class='tdDadosGrI'><br></td>";
            }
            $htmlTabela .= "</tr>";
        }
        $htmlTabela .= "</tbody></table>";
    } catch (PDOException $e) {
        $htmlTabela = "";
    }
    return $htmlTabela;
}

function inMovimentosGrupoII($_idAeroporto, $_page, $_limite) {
    try {
        $conexao = conexao();
        $filtro =  " AND st.idAeroporto = ".$_idAeroporto." AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND sm.movimento <> 'CND'";
        $order = "sm.id desc, sm.dhMovimento desc";
        $comando = selectDB("UltimosMovimentosStatus", $filtro, $order)." LIMIT ".(($_page - 1) * $_limite).",".$_limite;

        $sql = $conexao->prepare($comando);
        $sql->execute(); 
        $qtdRegistros = $sql->rowCount();
        $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
        $htmlTabela = "<table class='table table-striped table-hover table-bordered table-reduzida table-sm table-dark rounded-3 overflow-hidden'>".
                        "<thead class='table-info'><tr>".
                        "<th class='tdCabecalho'>Status</th>".
                        "<th class='tdCabecalho'>Matrícula</th>".
                        "<th class='tdCabecalho'>Tipo</th>".
                        "<th class='tdCabecalho'>Origem</th>".
                        "<th class='tdCabecalho'>Destino</th>".
                        "<th class='tdCabecalho'>Movimento</th>".
                        "<th class='tdCabecalho'>Dh.Movimento</th>".
                        "<th class='tdCabecalho'>Pista</th>".
                        "<th class='tdCabecalho'>Posição</th>".
                        "</tr></thead><tbody>";
    foreach ($registros as $dados) {
            // Destaca o movimento
            $destaque = "class='tdDadosGrII ".($dados['destaque'] != '' ? "table-".$dados['destaque'] : '')."'";

            $htmlTabela .= "<tr>".
                            "<td class='tdDadosGrII'>".$dados['status']."</td>".
                            "<td class='tdDadosGrII'>".$dados['matricula']."</td>".
                            "<td class='tdDadosGrII'>".$dados['classe']."-".$dados['natureza']."-".$dados['servico']."</td>".
                            "<td class='tdDadosGrII'>".$dados['origem']."</td>".
                            "<td class='tdDadosGrII'>".$dados['destino']."</td>".
                            "<td ".$destaque.">".$dados['descMovimento']."</td>".
                            "<td class='tdDadosGrII'>".$dados['dataHoraMovimento']."</td>".
                            ($dados['tipoRecurso'] == 'PIS' ? 
                                "<td class='tdDadosGrII'>".$dados['descRecurso']."</td><td class='tdDadosGrII'></td>" : 
                                "<td class='tdDadosGrII'></td><td class='tdDadosGrII'>".$dados['descRecurso']."</td>");
                            "</tr>";
        } 
        // Completa a tela até o limite
        for ($i = $qtdRegistros; $i < $_limite; $i++) {
            $htmlTabela .= "<tr>";
            for($j = 1; $j <= 9; $j++) {
                $htmlTabela .= "<td class='tdDadosGrII'><br></td>";
            }
            $htmlTabela .= "</tr>";
        }
        $htmlTabela .= "</tbody></table>";
    } catch (PDOException $e) {
        $htmlTabela = "";
    }
    return $htmlTabela;
}

function montarPropaganda($aeroporto, $siglaAeroporto, $usuario, $sistema = 'GEAR') {
    // Recuperar propaganda a ser exibida
    $idPropaganda = "";
    $propaganda = "";
    $token = gerarToken($sistema);
    $dados = ["id"=>$aeroporto,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto];
    $post = ['token'=>$token,'funcao'=>'RecuperarPropaganda','dados'=>$dados];
    $retorno = executaAPIs('apiPropaganda.php', $post);
    if ($retorno['status'] == 'OK') {
        foreach ($retorno['dados'] as $dados) {
            $idPropaganda = $dados['id'];
            $propaganda = '../arquivos/propagandas/'.$dados['propaganda'];
        }
    }
    return array("id"=>$idPropaganda,"propaganda"=>$propaganda);
}

function registrarPropaganda($titulo, $idPropaganda, $propaganda, $siglaAeroporto, $usuario, $sistema = 'GEAR', $origem = 'Tarefas') {
    // Registrar a propaganda a exibir
    $token = gerarToken($sistema);
    $dados = ["id"=>$idPropaganda,"usuario"=>$usuario,"siglaAeroporto"=>$siglaAeroporto,"propaganda"=>$propaganda,
                "origem"=>$origem,"tela"=>$titulo];
    $post = ['token'=>$token,'funcao'=>'RegistrarExibicao','dados'=>$dados];
    $retorno = executaAPIs('apiPropaganda.php', $post);
    return $retorno;
}
?>