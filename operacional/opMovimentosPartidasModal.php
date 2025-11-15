<?php
function modalPartida($_parametros,$_partida) {
    //var_dump($_partida);
    echo '
    <!-- *************************************************** -->
    <!-- Modal PARTIDA -->
    <!-- *************************************************** -->
    <button id="botaoPartida" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" data-bs-target="#editarPartida">Partida</button>

    <div class="modal fade" id="editarPartida" tabindex="-1" aria-labelledby="editarPartidaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header alert alert-padrao">
                    <h5 class="modal-title" id="editarPartidaLabel">'.$_parametros['funcao'].' de '.$_parametros['movimento'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <div class="modal-body">
                    <form action="?tipo=Partida&objetivo='.$_parametros['objetivo'].'&evento=salvar&movimento='.$_parametros['movimento'].'" method="POST" class="form-group" id="editarPartidaForm">
    ';
    // Obrigatório em todos os modais
    echo '
                        <input type="hidden" id="hdFuncao" name="funcao" value="'.$_parametros['funcao'].'"/>
                        <input type="hidden" id="hdIdStatus" name="idStatus" value="'.$_parametros['idStatus'].'"/>
                        <input type="hidden" id="hdIdChegada" name="idChegada" value="'.$_parametros['idChegada'].'"/>
                        <input type="hidden" id="hdIdPartida" name="idPartida" value="'.$_parametros['idPartida'].'"/>
                        <input type="hidden" id="hdIdMovimento" name="idMovimento" value="'.$_parametros['idMovimento'].'"/>
                        <input type="hidden" id="hdIdUltimo" name="idUltimo" value="'.$_parametros['idUltimo'].'"/>
    ';
    exibirMensagem('Partida');
    // Ações
    echo '              <div class="row mt-2">
                            <div class="col-md-12 d-md-flex justify-content-md-end">
                                <button class="btn btn-outline-primary" type="button" title="Limpar" id="limparFormularioPartida">
                                    <img src="../ativos/img/apagar.png"/></button>
                                <button class="btn btn-outline-primary" type="submit" title="Salvar" id="salvarFormularioPartida">
                                    <img src="../ativos/img/salvar.png"/></button>                        
                            </div>
                        </div> 
    ';    
    // Identificação
    echo '
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txPrtPartida">Partida</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="txPrtPartida" name="partida" readonly="true" value="'.$_partida['voo'].'"/>
                            </div>                        
                            <div class="col-md-4" id="divPrtChegada">
                                <label for="txPrtChegada">Chegada associada</label>
                                <input type="text" class="form-control input-lg" id="txPrtChegada" name="chegada" readonly="true" value="'.$_partida['vooChegada'].'"/>
                            </div>
                            <div class="col-md-4" id="divPrtStatus">
                                <label for="txPrtStatus">Status</label>
                                <input type="text" class="form-control input-lg" id="txPrtStatus" name="statusPartida" readonly="true" value="'.$_partida['statusPartida'].'"/>
                            </div>
                        </div>
    ';
    // Horários
    echo '
                        <div class="row mt-2" id="divPrtHorarios">
                            <div class="col-md-4">
                                <label for="txPrtPrevista">Previsão</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="txPrtPrevista" name="prevista" readonly="true" value="'.$_partida['dhPrevista'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txPrtConfirmada">Confirmada</label>
                                <input type="text" class="form-control input-lg" id="txPrtConfirmada" name="confirmada" readonly="true" value="'.$_partida['dhConfirmada'].'"/>
                            </div>
                        </div>
    '; 
    // Equipamento e Destino
    echo '                       
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="idPrtEquipamento">Equipamento</label>
                                <input type="text" class="form-select caixaAlta cpoLimparPartida input-lg" id="txPrtEquipamento" placeholder="Selecionar" name="txEquipamento"
                                    value="'.$_partida['txEquipamento'].'"
                                    onfocus="iniciarPesquisa(\'PrtEquipamento\',this.value)"
                                    oninput="executarPesquisa(\'PrtEquipamento\',this.value)"
                                    onblur="finalizarPesquisa(\'PrtEquipamento\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida cpoObrigatorio" id="idPrtEquipamento" name="equipamento" value="'.$_partida['equipamento'].'"/>
                                <span id="spantxPrtEquipamento"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idPrtDestino">Destino</label>
                                <input type="text" class="form-select caixaAlta cpoLimparPartida input-lg" id="txPrtDestino" placeholder="Selecionar" name="txDestino"
                                    value="'.$_partida['txDestino'].'"
                                    onfocus="iniciarPesquisa(\'PrtDestino\',this.value)"
                                    oninput="executarPesquisa(\'PrtDestino\',this.value)"
                                    onblur="finalizarPesquisa(\'PrtDestino\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida cpoObrigatorio" id="idPrtDestino" name="destino" value="'.$_partida['destino'].'"/>
                                <span id="spantxPrtDestino"></span>
                            </div>
                        </div>
    ';
    // Classificação                                
    echo '                            
                        <div class="row mt-2" id="divPrtClassificacao">
                            <div class="col-md-4">
                                <label for="idPrtClasse">Classe</label>
                                <input type="text" class="form-select cpoLimparPartida input-lg" id="txPrtClasse" placeholder="Selecionar" name="txClasse"
                                    value="'.$_partida['txClasse'].'"
                                    onfocus="iniciarPesquisa(\'PrtClasse\',this.value)"
                                    oninput="executarPesquisa(\'PrtClasse\',this.value)"
                                    onblur="finalizarPesquisa(\'PrtClasse\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida" id="idPrtClasse" name="classe" value="'.$_partida['classe'].'"/>
                                <span id="spantxPrtClasse"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idPrtsNatureza">Natureza</label>
                                <input type="text" class="form-select cpoLimparPartida input-lg" id="txPrtNatureza" placeholder="Selecionar" name="txNatureza"
                                    value="'.$_partida['txNatureza'].'"
                                    onfocus="iniciarPesquisa(\'PrtNatureza\',this.value)"
                                    oninput="executarPesquisa(\'PrtNatureza\',this.value)"
                                    onblur="finalizarPesquisa(\'PrtNatureza\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida" id="idPrtNatureza" name="natureza" value="'.$_partida['natureza'].'"/>
                                <span id="spantxPrtNatureza"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idPrtServico">Tipo de Serviço</label>
                                <input type="text" class="form-select cpoLimparPartida input-lg" id="txPrtServico" placeholder="Selecionar" name="txServico"
                                    value="'.$_partida['txServico'].'"
                                    onfocus="iniciarPesquisa(\'PrtServico\',this.value)"
                                    oninput="executarPesquisa(\'PrtServico\',this.value)"
                                    onblur="finalizarPesquisa(\'PrtServico\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida" id="idPrtServico" name="servico" value="'.$_partida['servico'].'"/>
                                <span id="spantxPrtServico"></span>
                            </div>
                        </div>
    ';
    // Informações de Passageiros
    echo '                        
                        <div class="row mt-2" id="divPrtInformacoes">
                            <div class="col-md-4">
                                <label for="txPrtAssentos">Assentos</label></th>
                                <input type="text" class="form-control cpoLimparPartida cpoObrigatorio input-lg" id="txPrtAssentos" name="assentos"
                                    value="'.$_partida['assentos'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txPrtPax">PAX</label></th>
                                <input type="text" class="form-control cpoLimparPartida cpoObrigatorio input-lg" id="txPrtPax" name="pax"
                                    value="'.$_partida['pax'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txPrtPnae">PNAE</label></th>
                                <input type="text" class="form-control cpoLimparPartida cpoObrigatorio input-lg" id="txPrtPnae" name="pnae"
                                    value="'.$_partida['pnae'].'"/>
                            </div>
                        </div>
    ';  
    // Recursos   
    echo '
                        <div class="row mt-2" id="divPrtRecurso">
                            <div class="col-md-4">
                                <label for="idPrtPosicao">Posição</label>
                                <input type="text" class="form-select caixaAlta cpoLimparPartida input-lg" id="txPrtPosicao" placeholder="Selecionar" name="txPosicao"
                                    value="'.$_partida['txPosicao'].'"
                                    onfocus="iniciarPesquisa(\'PrtPosicao\',this.value,\'POS\')"
                                    oninput="executarPesquisa(\'PrtPosicao\',this.value,\'POS\')"
                                    onblur="finalizarPesquisa(\'PrtPosicao\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida" id="idPrtPosicao" name="posicao" value="'.$_partida['idPosicao'].'"/>
                                <span id="spantxPrtPosicao"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idPrtPortao">Portão</label>
                                <input type="text" class="form-select caixaAlta cpoLimparPartida input-lg" id="txPrtPortao" placeholder="Selecionar" name="txPortao"
                                    value="'.$_partida['txPortao'].'"
                                    onfocus="iniciarPesquisa(\'PrtPortao\',this.value,\'POR\')"
                                    oninput="executarPesquisa(\'PrtPortao\',this.value,\'POR\')"
                                    onblur="finalizarPesquisa(\'PrtPortao\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida" id="idPrtPortao" name="portao" value="'.$_partida['idPortao'].'"/>
                                <span id="spantxPrtPortao"></span>
                            </div>
                        </div>                            
    ';
    // Movimento
    echo '                        
                        <div class="row mt-4" id="divPrtDhMovimento">
                            <div class="col-md-4">
                                <label for="idPrtMovimento">Movimento</label>
                                <input type="text" class="form-select cpoLimparPartida input-lg" id="txPrtMovimento" placeholder="Selecionar" name="txMovimento"
                                    value="'.$_partida['txMovimento'].'"
                                    onfocus="iniciarPesquisa(\'PrtMovimento\',this.value,\'PRT\')"
                                    oninput="executarPesquisa(\'PrtMovimento\',this.value,\'PRT\')"
                                    onblur="finalizarPesquisa(\'PrtMovimento\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparPartida" id="idPrtMovimento" name="cdMovimento" value="'.$_partida['movimento'].'"/>
                                <span id="spantxPrtMovimento"></span>
                            </div>                        
                            <div class="col-md-4">
                                <label for="txPrtDtMovimento">Data Movimento</label></th>
                                <input type="date" class="form-control cpoLimparPartida cpoObrigatorio input-lg" id="txPrtDtMovimento" name="dtMovimento"
                                    value="'.$_partida['dtMovimento'].'"/>
                            </div>
                            <div class="col-md-4" id="divPrtHrMovimento">
                                <label for="txPrtHrMovimento">Hora Movimento</label></th>
                                <input type="time" class="form-control cpoLimparPartida cpoObrigatorio input-lg" id="txPrtHrMovimento" name="hrMovimento"
                                    value="'.$_partida['hrMovimento'].'"/>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}

function prepararPartida($_parametros){
    $_retorno = limparPartida($_parametros);
    $_comando = null;

    if (($_parametros['funcao'] == "Alteração") || 
        ($_parametros['funcao'] == "Inclusão" && $_parametros['movimento'] != "Pouso" && $_parametros['movimento'] != "Previsão")) {
        try {
            $_conexao = conexao();
            $_comando = selectDB("UltimosMovimentosVoos"," AND vo.id = ".$_parametros['idPartida']." AND vm.id = ".$_parametros['idUltimo'],"");
            $_sql = $_conexao->prepare($_comando);    
            if ($_sql->execute()) {
                $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
                foreach ($_registros as $_dados) {
                    $_retorno['voo'] = $_dados['voo'];
                    $_retorno['operacao'] = $_dados['operacao'];
                    $_retorno['operador'] = $_dados['numeroVoo'];
                    $_retorno['numeroVoo'] = $_dados['numeroVoo'];
                    $_retorno['equipamento'] = $_dados['equipamento'];
                    $_retorno['assentos'] = $_dados['assentos'];
                    $_retorno['dhPrevista'] = $_dados['dhPrevista'];
                    $_retorno['dhConfirmada'] = $_dados['dhConfirmada'];
                    $_retorno['classe'] = $_dados['classe'];
                    $_retorno['natureza'] = $_dados['natureza'];
                    $_retorno['servico'] = $_dados['servico'];
                    $_retorno['origem'] = $_dados['origem'];
                    $_retorno['destino'] = $_dados['destino'];
                    $_retorno['codeshare'] = $_dados['codeshare'];
                    $_retorno['pax'] = $_dados['pax'];
                    $_retorno['pnae'] = $_dados['pnae'];
                    $_retorno['situacao'] = $_dados['situacao'];

                    $_retorno['idChegada'] = $_dados['idChegada'];
                    $_retorno['idPartida'] = $_dados['idPartida'];
                    $_retorno['idPosicao'] = $_dados['idPosicao'];
                    $_retorno['idEsteira'] = $_dados['idEsteira'];
                    $_retorno['idPortao'] = $_dados['idPortao'];

                    $_retorno['statusChegada'] = $_dados['statusChegada'];
                    $_retorno['statusPartida'] = $_dados['statusPartida'];
                    $_retorno['vooChegada'] = $_dados['vooChegada'];
                    $_retorno['vooPartida'] = $_dados['vooPartida'];

                    $_retorno['movimento'] = $_dados['movimento'];
                    $_retorno['dtMovimento'] = $_dados['dtMovimento'];
                    $_retorno['hrMovimento'] = $_dados['hrMovimento'];
                    $_retorno['recurso'] = $_dados['idRecurso'];

                    $_retorno['txEquipamento'] = $_dados['equipamento'];
                    $_retorno['txMovimento'] = $_dados['descMovimento'];
                    $_retorno['txClasse'] = $_dados['descClasse'];
                    $_retorno['txNatureza'] = $_dados['descNatureza'];
                    $_retorno['txServico'] = $_dados['descServico'];
                    $_retorno['txOrigem'] = $_dados['origem'];
                    $_retorno['txDestino'] = $_dados['destino'];
                    $_retorno['txRecurso'] = $_dados['recurso'];
                    $_retorno['txSituacao'] = $_dados['descSituacao'];
                    $_retorno['txPosicao'] = $_dados['posicao'];
                    $_retorno['txEsteira'] = $_dados['esteira'];
                    $_retorno['txPortao'] = $_dados['portao'];

                    $_retorno['resultado'] = 'success';
                }
            } else {
                throw new PDOException("Não foi possível recuperar este registro!");
            } 
        } catch (PDOException $e) {
            $_retorno['resultado'] = 'danger';
            $_retorno['mensagem'] = traduzPDO($e->getMessage());
            $_retorno['complemento'] = $_comando;
        }
    }
    return $_retorno;    
}

function pegarIdUltimoMovimentoPartida($_parametros){
    $_comando = selectDB("UltimoIdMovimentoVoo","","",$_parametros['idPartida']);
    $_parametros['status'] = 'success';
    $_parametros['mensagem'] = 'OK';
    $_parametros['complemento'] = $_comando;

    if ($_parametros['idUltimo'] == '') {
        try {
            $_conexao = conexao();
            $_sql = $_conexao->prepare($_comando);    
            if ($_sql->execute()) {
                if ($_sql->rowCount() > 0) {
                    $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($_registros as $_dados) {
                        $_parametros['idUltimo'] = $_dados['id'];
                    }
                } else {
                    throw new PDOException("Não existe último movimento para esta partida! [id ".$_parametros['idPartida']."]");
                }
            } else {
                throw new PDOException("Não foi possível recuperar o último movimento desta partida! [id ".$_parametros['idPartida']."]");
            } 
        } catch (PDOException $e) {
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = traduzPDO($e->getMessage());
            $_parametros['complemento'] = $_comando;
            $_parametros['idUltimo'] = '';
            $_parametros['funcao'] = null; 
        }
    }
    return $_parametros; 
}

function pegarDigitacaoPartida($_parametros) {
    $_retorno = limparPartida($_parametros);
    
    // Carregar os posts
    $_retorno['voo'] = carregarPosts('partida');
    $_retorno['equipamento'] = carregarPosts('equipamento');
    $_retorno['assentos'] = carregarPosts('assentos');
    $_retorno['dhPrevista'] = carregarPosts('prevista');
    $_retorno['dhConfirmada'] = carregarPosts('confirmada');
    $_retorno['classe'] = carregarPosts('classe');
    $_retorno['natureza'] = carregarPosts('natureza');
    $_retorno['servico'] = carregarPosts('servico');
    $_retorno['destino'] = carregarPosts('destino');
    $_retorno['pax'] = carregarPosts('pax');
    $_retorno['pnae'] = carregarPosts('pnae');

    $_retorno['idPosicao'] = carregarPosts('posicao');
    $_retorno['idPortao'] =carregarPosts('portao');

    $_retorno['statusPartida'] = carregarPosts('statusPartida');
    $_retorno['vooChegada'] = carregarPosts('chegada');

    $_retorno['movimento'] = carregarPosts('cdMovimento');
    $_retorno['dtMovimento'] = carregarPosts('dtMovimento');
    $_retorno['hrMovimento'] = carregarPosts('hrMovimento');

    $_retorno['txEquipamento'] = carregarPosts('txEquipamento');
    $_retorno['txMovimento'] = carregarPosts('txMovimento');
    $_retorno['txClasse'] = carregarPosts('txClasse');
    $_retorno['txNatureza'] = carregarPosts('txNatureza');
    $_retorno['txServico'] = carregarPosts('txServico');
    $_retorno['txDestino'] = carregarPosts('txDestino');
    $_retorno['txPosicao'] = carregarPosts('txPosicao');
    $_retorno['txPortao'] = carregarPosts('txPortao');
    
    return $_retorno;  
}

function salvarPartida($_parametros,$_partida){
    // Salvando as informações
    //
    $erros = "";
    // Inclusão ou Alteração
    $funcao = $_parametros['funcao'];
    // Chegada, Previsão, Movimento, outros
    $movimento = $_parametros['movimento'];
    //
    // Verifica críticas e consistências de acordo com o movimento que estiver salvando
    //
    $dhMovimento = mudarDataHoraAMD($_partida['dtMovimento']." ".$_partida['hrMovimento']);
    switch ($movimento) {
        case "Partida":
            $erros = camposPreenchidos(['partida','equipamento','destino','classe','natureza','servico']);
        break;
        case "Previsão":
            $erros = camposPreenchidos(['partida','equipamento','destino','classe','natureza','servico','cdMovimento','dtMovimento','hrMovimento']);
        break;
        case "Movimento":
            $erros = camposPreenchidos(['cdMovimento','dtMovimento','hrMovimento']);
        break;
        default:
            $erros = camposPreenchidos(['cdMovimento','dtMovimento','hrMovimento']);
        break;
    }
    //
    // Só prossegue se tudo ok
    //
    // idChegada => id do registro de partida
    // idMovimento => id do registro do movimento que está sendo alterado
    // idUltimo => id do registro do ultimo movimento
    //
    if($erros){
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = $erros;
        $_parametros['complemento'] = "";
    } else {
        // Monta comando de atualização de acordo com o movimento 
        $comando = "";
        $arqDLOg = "";
        $chvDLOG = "";
        switch ($movimento) {
            case "Partida":
                if ($_parametros['idPartida'] != "") {
                    $comando = "UPDATE gear_voos_operacionais SET idAeroporto=".$_parametros['aeroporto'].
                        ",equipamento='".substr($_partida['txEquipamento'],0,100).
                        "',assentos=".mudarEmptyZeroMysql($_partida['assentos']).
                        ",classe='".$_partida['classe']."',natureza='".$_partida['natureza'].
                        "',servico='".$_partida['servico'].
                        "',destino='".substr($_partida['txDestino'],0,4).
                        "',idPosicao=".mudarEmptyNuloMysql($_partida['idPosicao']).
                        ",idPortao=".mudarEmptyNuloMysql($_partida['idPortao']).
                        ",pax=".mudarEmptyZeroMysql($_partida['pax']).
                        ",pnae=".mudarEmptyZeroMysql($_partida['pnae']).
                        ", cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idPartida'];
                }
                $arqDLOg = "gear_voos_operacionais";
                $chvDLOG = $_parametros['idPartida'];
            break;
            case "Previsão":
                $comando = "INSERT INTO gear_voos_operacionais(idAeroporto,operacao,operador,numeroVoo,". 
                            "equipamento,assentos,dtMovimento,dhPrevista,classe,natureza,". 
                            "servico,destino,idPosicao,idPortao,pax,pnae,situacao,fonte,cadastro) VALUES (". 
                            $_parametros['aeroporto'].",'PRT','".substr($_partida['voo'],0,3)."','".
                            substr($_partida['voo'],3,4)."','".
                            substr($_partida['txEquipamento'],0,100)."',".
                            mudarEmptyZeroMysql($_partida['assentos']).",UTC_TIMESTAMP(),".$dhMovimento.",'".
                            $_partida['classe']."','".$_partida['natureza']."','".
                            $_partida['servico']."','".substr($_partida['txDestino'],0,4)."',".
                            mudarEmptyNuloMysql($_partida['idPosicao']).",".
                            mudarEmptyNuloMysql($_partida['idPortao']).",".
                            mudarEmptyZeroMysql($_partida['pax']).",".
                            mudarEmptyZeroMysql($_partida['pnae']).",'ATV','".
                            $_parametros['siglaAeroporto']."', UTC_TIMESTAMP())";
                $arqDLOg = "gear_voos_operacionais";
            break;
            case "Movimento":
                $comando = "INSERT INTO gear_voos_movimentos (idVoo, dhMovimento, movimento, usuario, cadastro) VALUES (".
                            $_parametros['idPartida'].",".$dhMovimento.",'".$_partida['movimento']."', '".$_parametros['usuario'].
                            "', UTC_TIMESTAMP())";
                $arqDLOg = "gear_voos_movimentos";
            break;
            default:
                if ($_parametros['idMovimento'] != "") {
                    $comando = "UPDATE gear_voos_movimentos SET dhMovimento = ".$dhMovimento.", usuario = '".$_parametros['usuario'].
                                "' ,cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idMovimento'];
                }
                $arqDLOg = "gear_voos_movimentos";
                $chvDLOG = $_parametros['idMovimento'];
            break;
        }
        // Verifica se comando foi montado
        if ($comando != "") {
            //gravaXTrace($comando);
            try {
                $conexao = conexao();
                $sql = $conexao->prepare($comando);
                if ($sql->execute()) {
                    if ($sql->rowCount() > 0) {
                        gravaDLog($arqDLOg, $funcao, $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                                  ($funcao == "Alteração" ? $chvDLOG : $conexao->lastInsertId()), $comando);
                        $_parametros['status'] = "success";
                        $_parametros['mensagem'] = array("Registro ".($funcao == "Alteração" ? "alterado" : "incluído")." com sucesso!");
                        $_parametros['complemento'] = "";
                        $_parametros['idPartida'] = null;
                        $_parametros['idMovimento'] = null;
                        $_parametros['idUltimo'] = null;
                        $_parametros['funcao'] = null;
                    } else {
                        throw new PDOException("Não foi possível efetivar esta ".($funcao == "Alteração" ? "alteração" : "inclusão")."!");
                    }
                } else {
                    throw new PDOException("Não foi possível ".($funcao == "Alteração" ? "alterar" : "incluir")." este registro!");
                }
            } catch (PDOException $e) {
                $_parametros['status'] = 'danger';
                $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
                $_parametros['complemento'] = $comando;
            }
        } else {
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = array("Não foi possível realizar esta operação!");
            $_parametros['complemento'] = "";
        }
    }
    return $_parametros;
}

function limparPartida($_parametros){
    $_retorno = array(
        'voo'=>null,
        'operacao'=>null,
        'operador'=>null,
        'numeroVoo'=>null,
        'equipamento'=>null,
        'assentos'=>null,
        'dhPrevista'=>null,
        'dhConfirmada'=>null,
        'classe'=>null,
        'natureza'=>null,
        'servico'=>null,
        'origem'=>null,
        'destino'=>null,
        'codeshare'=>null,
        'pax'=>null,
        'pnae'=>null,
        'situacao'=>null,

        'idChegada'=>null,
        'idPartida'=>null,
        'idPosicao'=>null,
        'idEsteira'=>null,
        'idPortao'=>null,

        'statusChegada'=>null,
        'statusPartida'=>null,
        'vooChegada'=>null,
        'vooPartida'=>null,
        'statusChegada'=>null,
        'statusPartida'=>null, 
        
        // Data e hora local do aeroporto
        'dtMovimento'=>dateTimeUTC($_parametros['utcAeroporto'])->format('Y-m-d'),
        'hrMovimento'=>dateTimeUTC($_parametros['utcAeroporto'])->format('H:i'),
        'movimento'=>'PRV',
        'recurso'=>null,

        'txEquipamento'=>null,
        'txMovimento'=>'Previsão de Partida',
        'txClasse'=>null,
        'txNatureza'=>null,
        'txServico'=>null,
        'txPosicao'=>null,
        'txEsteira'=>null,
        'txPortao'=>null,
        'txOrigem'=>null,
        'txDestino'=>null,
        'txRecurso'=>null,

        'resultado'=>null,
        'mensagem'=>null,
        'complemento'=>null);
    return $_retorno;
}

function excluirMovimentoPartida($_parametros){
    // Preparando chamada da API apiManterPartidas
    $_token = gerarToken($_SESSION['plantaSistema']);
    $_post = ['token'=>$_token,'funcao'=>'ExcluirMovimentoPartida','parametros'=>$_parametros, 'partida'=> array()];
    $_parametros = executaAPIs('apiManterPartidas.php', $_post);
    return $_parametros;
}

function barraFuncoesPartida($_titulo, $_parametros, $_impressao) {
    echo '
    <div class="row justify-content-between py-2">
        <li class="col-4 px-4 painel-header"><h5>'.$_titulo.'</h5></li> 
        <div class="col-4">
            <a href="?tipo=Partida&funcao=Inclusão&movimento=Previsão&idChegada=&idMovimento=&idUltimo=" class="btn btn-outline-primary btn-sm">
                <img src="../ativos/img/previsto.png"/> Incluir previsão</a> 
            </a>
        </div>
        <div class ="col-4 d-md-flex justify-content-end">
            <button class="btn btn-outline-primary" type="button" title="Atualizar" id="buscarPartida">
                <img src="../ativos/img/atualizar.png"/></button>
    ';
    echo '        
            <button class="btn btn-outline-primary" type="button" title="Pesquisar" id="iniciarPesquisaPartida" data-bs-toggle="modal" 
                data-bs-target="#pesquisarPartida">
                <img src="../ativos/img/pesquisar.png"/></button>
    ';
    if ($_impressao) {
        echo '<button class="btn btn-outline-primary" type="button" title="Exportar PDF" id="exportarPDF">
                <img src="../ativos/img/exportarPDF.png"/></button>';
        echo '<button class="btn btn-outline-primary" type="button" title="Impressão" id="print" onclick="window.print()">
                <img src="../ativos/img/imprimir.png"/></button>';    }   
    if ($_parametros['objetivo'] == 'painel') {
        echo '<button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#painelPartidas">
                <img src="../ativos/img/visualizar.png" title="Expandir painel"/></button>
        ';
    }
    echo '                
        </div>
    </div>
    ';     
} 

function pesquisarPartida($_parametros){
    switch ($_parametros['objetivo']) {
        case "partida":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opVPR_ordenacao',"vo.dhPrevista,vo.operador,vo.numeroVoo"); 
        break;
        case "movimento":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opMPR_ordenacao',"vo.dhPrevista,vo.operador,vo.numeroVoo"); 
        break;
        case "painel":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opPPR_ordenacao',"vo.dhPrevista,vo.operador,vo.numeroVoo"); 
        break;
    }
    echo
    '
    <!-- *************************************************** -->
    <!-- Modal PESQUISA PARTIDA -->
    <!-- *************************************************** -->
    <div class="modal fade" id="pesquisarPartida" tabindex="-1" aria-labelledby="sobreLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sobreLabel">Pesquisar Partida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    ';
    // modal-body
    echo '
                <div class="modal-body">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="ptxPrtVoo">Voo</label>
                            <input type="text" class="form-control cpoCookiePartida caixaAlta input-lg" id="ptxPrtVoo"/>
                        </div>
                    </div>
    ';
    echo '
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="pslPrtClasse">Classe</label>
                            <select class="form-select cpoCookiePartida selCookiePartida input-lg" id="pslPrtClasse">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pslPrtNatureza">Natureza</label>
                            <select class="form-select cpoCookiePartida selCookiePartida input-lg" id="pslPrtNatureza">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="pslPrtServico">Tipo de Serviço</label>
                            <select class="form-select cpoCookiePartida selCookiePartida input-lg" id="pslPrtServico">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="ptxPrtDestino">Destino</label>
                            <input type="text" class="form-control cpoCookiePartida caixaAlta input-lg" id="ptxPrtDestino"/>
                        </div>
                    </div>
    ';
    echo '
                <br>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label for="pslPrtOrdenacao">Ordenação da lista</label>
                        <select class="form-select cpoCookiePartida selCookiePartida input-lg" id="pslPrtOrdenacao">
                            <option '.($_ordenacao == "vo.dhPrevista,vo.operador,vo.numeroVoo" ? "selected" : "").' value="vo.dhPrevista,vo.operador,vo.numeroVoo">Movimentação</option>
                        </select>
                    </div>
                </div>
            </div>
    ';
    // modal-footer
    echo '
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" id="limparPesquisaPartida"><img src="../ativos/img/limpar.png" title="Limpar"/></button>
                    <button type="button" class="btn btn-outline-primary" id="aplicarPesquisaPartida" data-bs-dismiss="modal"><img src="../ativos/img/pesquisar.png" title="Pesquisar"/></button>
                </div>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->    
    ';
}

function salvarConectarPartida($_parametros) {
    $comando = null;
    $conexao = conexao();

    // Conecta voos 
    try {
        // Verifica se ID de chegada foi informado
        if (empty($_parametros['idChegada'])) {
            throw new PDOException("Voo de chegada não foi informado!");
        }

        $conexao->beginTransaction();
        
        // Conectando voo de partida a chegada
        $comando = "UPDATE gear_voos_operacionais SET idPartida = ".$_parametros['idPartida']." WHERE id = ".$_parametros['idChegada'];
        $sql = $conexao->prepare($comando);
        if ($sql->execute()){
            gravaDLog("gear_voos_operacionais", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                        $_parametros['idChegada'], $comando);
            
            // Conectando voo de chegada a partida
            $comando = "UPDATE gear_voos_operacionais SET idChegada = ".$_parametros['idChegada']." WHERE id = ".$_parametros['idPartida'];
            $sql = $conexao->prepare($comando);
            if ($sql->execute()){
                gravaDLog("gear_voos_operacionais", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                            $_parametros['idPartida'], $comando);

                // Verifica se a chegada já está conectada a um status, caso positivo conecta o voo de partida ao status da chegada
                $comando = "SELECT id FROM gear_status WHERE idChegada = ".$_parametros['idChegada'];
                $sql = $conexao->prepare($comando);
                if ($sql->execute()){
                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $dados) {
                        $_id = $dados['id'];
                    }
                    if (!empty($_id)) {
                        $comando = "UPDATE gear_status SET idPartida = ".$_parametros['idPartida']." WHERE id = ".$_id;
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()){
                            gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                        $_parametros['idStatus'], $comando);
                        } else {
                            throw new PDOException("Não foi possível conectar o voo de partida ao status!");
                        }
                    } 
                    $_parametros['status'] = "success";
                    $_parametros['mensagem'] = array("Voos conectados com sucesso!");
                    $_parametros['idChegada'] = null;
                    $_parametros['idPartida'] = null;
                    $_parametros['funcao'] = null;   
                    if ($conexao->inTransaction()) {$conexao->commit();}      
                } else {
                    throw new PDOException("Não foi possível verificar conexão do voo de chegada com o status!");
                }                 
            } else {
                throw new PDOException("Não foi possível conectar o voo de chegada ao voo de partida!");
            }
        } else {
            throw new PDOException("Não foi possível conectar o voo de partida ao voo de chegada!");
        }
    } catch (PDOException $e) {
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
        $_parametros['complemento'] = $comando;
        $_parametros['funcao'] = null;
        if ($conexao->inTransaction()) {$conexao->rollBack();}
    }
    return $_parametros;
}
?>

<script>
    async function buscarPartida() {
        var filtro = "";
        var descricaoFiltro = "";

        // Decidindo o filtro base de acordo com o objetivo do formulario
        switch ($('#hdObjetivo').val()) {
            case "partida":
                filtro += " AND vo.idAeroporto = "+$("#hdAeroporto").val();
                descricaoFiltro += ' <br>Aeroporto : '+$("#hdNomeAeroporto").val();
            break;
            case "movimento":
                filtro += " AND vo.idAeroporto = "+$('#hdAeroporto').val()+
                        " AND vo.situacao = 'ATV' AND vm.movimento <> 'CND'";
                descricaoFiltro += ' <br>Aeroporto : '+$("#hdNomeAeroporto").val();
            break;
            case "painel":
                filtro += " AND vo.idAeroporto = "+$('#hdAeroporto').val()+
                        " AND vo.situacao = 'ATV' AND vm.movimento <> 'CND'";
                descricaoFiltro += '';
            break;
        }

        $(".cpoCookiePartida").each(function(){
            if (!isEmpty($(this).val())) {
                switch ($(this).attr('id')) {
                    case "ptxPrtVoo":
                        filtro += " AND CONCAT('!',vo.operador,vo.numeroVoo,'!') LIKE '%_"+$("#ptxPrtVoo").val()+"_%'";
                        descricaoFiltro += " <br>Voo : "+$("#ptxPrtVoo").val();
                    break;
                    case "pslPrtNatureza":
                        filtro += " AND vo.natureza = '"+$("#pslPrtNatureza").val()+"'";
                        descricaoFiltro += ' <br>Natureza : '+$("#pslPrtNatureza :selected").text();
                    break;
                    case "pslPrtClasse":
                        filtro += " AND vo.classe = '"+$("#pslPrtClasse").val()+"'";
                        descricaoFiltro += ' <br>Classe : '+$("#pslPrtClasse :selected").text();
                    break;
                    case "pslPrtServico":
                        filtro += " AND vo.servico = '"+$("#pslPrtServico").val()+"'";
                        descricaoFiltro += ' <br>Tipo de Serviço : '+$("#pslPrtServico :selected").text();
                    break;
                    case "ptxPrtDestino":
                        filtro += " AND vo.destino LIKE '%"+$("#ptxPrtDestino").val()+"%'";
                        descricaoFiltro += " <br>Destino : "+$("#ptxPrtDestino").val();
                    break;
                    case "pslPrtMovimento":
                        filtro += " AND vm.movimento = '"+$("#pslPrtMovimento").val()+"'";
                        descricaoFiltro += ' <br>Movimento : '+$("#pslPrtMovimento :selected").text();
                    break;
                    default:
                        filtro += "";
                        descricaoFiltro += "";
                }
            }
        });

        // Montagem da ordem
        var ordem = $("#pslPrtOrdenacao").val();

        // Decidindo o carregamento das informações de acordo com o objetivo do formulario
        switch ($('#hdObjetivo').val()) {
            case "partida":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opVPR_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opVPR_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opVPR_descricao', descricaoFiltro);
                //await opCarregarStatus('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            break;
            case "movimento":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMPR_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMPR_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMPR_descricao', descricaoFiltro);
                //await opCarregarUltimosMovimentos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            break;
            case "painel":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPPR_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPPR_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPPR_descricao', descricaoFiltro);
                await ajaxPartidas($('#hdSiglaAeroporto').val());
            break;
        }
    }

    function prepararModalPartida(){
        if ($('#hdMovimento').val() != "") {
            switch($('#hdMovimento').val()) {
                case "Partida":
                    $('#divPrtDhMovimento').css('display','none');
                break;
                case "Previsão":
                    $('#txPrtPartida').attr('readonly', false);
                    $('#divPrtChegada').css('display', 'none');
                    $('#divPrtStatus').css('display', 'none');
                    $('#txPrtMovimento').attr('readonly', true);
                break;
                case "Movimento":
                    $('#divPrtEqpDestino').css('display', 'none');
                    $('#divPrtClassificacao').css('display', 'none');
                    $('#divPrtInformacoes').css('display', 'none');
                    $('#divPrtRecurso').css('display', 'none');
                break;
                default:
                    $('#divPrtEqpDestino').css('display', 'none');
                    $('#divPrtClassificacao').css('display', 'none');
                    $('#divPrtInformacoes').css('display', 'none');
                    $('#divPrtRecurso').css('display', 'none');
                    $('#txPrtMovimento').attr('readonly', true);
                break;
            }
        } 
    }

    async function prepararConectarPartida(idAeroporto, idPartida){
        //
        // REVISAR - PEGAR A dhConfirmada da Partida
        //
        var filtro = " AND vo.idAeroporto = "+idAeroporto+
                     " AND vo.operacao = 'CHG' AND vo.idPartida IS NULL "+
                     " AND vo.dhConfirmada <= (SELECT dhConfirmada FROM gear_voos_horario_confirmado WHERE idVoo = "+idPartida+")";
        await suCarregarSelectTodos('ConectarVoos','#pslConectarVoos', '', filtro, 'Cadastrar');
    }

    // function ajaxPartidas(siglaAeroporto){
    //     var ordem = valorCookie(siglaAeroporto+'_opPPR_ordenacao','');
    //     var filtro = " " + valorCookie(siglaAeroporto+'_opPPR_filtro','');
    //     var descricao = valorCookie(siglaAeroporto+'_opPPR_descricao','');
    //     $.ajax({
    //         url: "opMovimentosPartidasPainel.php?filtro="+filtro+"&descricao="+descricao+"&ordem="+ordem+"&busca=&pagina=0&limite=0",
    //         type: "GET",
    //         success: function(partida){
    //             $("#painelPartidas").fadeOut(0, function(){
    //                 $(this).html(partida);
    //                 $(this).fadeIn(0);
    //             });
    //         }
    //     });
    // }
</script>