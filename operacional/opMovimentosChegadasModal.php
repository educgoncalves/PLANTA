<?php
function modalChegada($_parametros,$_chegada) {
    //var_dump($_chegada);
    echo '
        <!-- *************************************************** -->
        <!-- Modal CHEGADA -->
        <!-- *************************************************** -->
        <button id="botaoChegada" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" data-bs-target="#editarChegada">Chegada</button>

        <div class="modal fade" id="editarChegada" tabindex="-1" aria-labelledby="editarChegadaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header alert alert-padrao">
                        <h5 class="modal-title" id="editarChegadaLabel">'.$_parametros['funcao'].' de '.$_parametros['movimento'].'</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                    </div>
                    <div class="modal-body">
                        <form action="?tipo=Chegada&objetivo='.$_parametros['objetivo'].'&evento=salvar&movimento='.$_parametros['movimento'].'" method="POST" class="form-group" id="editarChegadaForm">
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
    exibirMensagem('Chegada');
    // Ações
    echo '              <div class="row mt-2">
                            <div class="col-md-12 d-md-flex justify-content-md-end">
                                <button class="btn btn-outline-primary" type="button" title="Limpar" id="limparFormularioChegada">
                                    <img src="../ativos/img/apagar.png"/></button>
                                <button class="btn btn-outline-primary" type="submit" title="Salvar" id="salvarFormularioChegada">
                                    <img src="../ativos/img/salvar.png"/></button>                        
                            </div>
                        </div> 
    ';
    // Identificação
    echo '
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="txChgChegada">Chegada</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="txChgChegada" name="chegada" readonly="true" value="'.$_chegada['voo'].'"/>
                            </div>
                            <div class="col-md-4" id="divChgPartida">
                                <label for="txChgPartida">Partida associada</label>
                                <input type="text" class="form-control input-lg" id="txChgPartida" name="partida" readonly="true" value="'.$_chegada['vooPartida'].'"/>
                            </div>
                            <div class="col-md-4" id="divChgStatus">
                                <label for="txChgStatus">Status</label>
                                <input type="text" class="form-control input-lg" id="txChgStatus" name="statusChegada" readonly="true" value="'.$_chegada['statusChegada'].'"/>
                            </div>
                        </div>
    ';
    // Horários
    echo '
                        <div class="row mt-2" id="divChgHorarios">
                            <div class="col-md-4">
                                <label for="txChgPrevista">Previsão</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="txChgPrevista" name="prevista" readonly="true" value="'.$_chegada['dhPrevista'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txChgConfirmada">Confirmada</label>
                                <input type="text" class="form-control input-lg" id="txChgConfirmada" name="confirmada" readonly="true" value="'.$_chegada['dhConfirmada'].'"/>
                            </div>
                        </div>
    ';
    // Equipamento e Origem
    echo '                        
                        <div class="row mt-2" id="divChgEqpOrigem">
                            <div class="col-md-4">
                                <label for="idChgEquipamento">Equipamento</label>
                                <input type="text" class="form-select caixaAlta cpoLimparChegada input-lg" id="txChgEquipamento" placeholder="Selecionar" name="txEquipamento"
                                    value="'.$_chegada['txEquipamento'].'"
                                    onfocus="iniciarPesquisa(\'ChgEquipamento\',this.value)"
                                    oninput="executarPesquisa(\'ChgEquipamento\',this.value)"
                                    onblur="finalizarPesquisa(\'ChgEquipamento\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada cpoObrigatorio" id="idChgEquipamento" name="equipamento" value="'.$_chegada['equipamento'].'"/>
                                <span id="spantxChgEquipamento"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idChgOrigem">Origem</label>
                                <input type="text" class="form-select caixaAlta cpoLimparChegada input-lg" id="txChgOrigem" placeholder="Selecionar" name="txOrigem"
                                    value="'.$_chegada['txOrigem'].'"
                                    onfocus="iniciarPesquisa(\'ChgOrigem\',this.value)"
                                    oninput="executarPesquisa(\'ChgOrigem\',this.value)"
                                    onblur="finalizarPesquisa(\'ChgOrigem\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada cpoObrigatorio" id="idChgOrigem" name="origem" value="'.$_chegada['origem'].'"/>
                                <span id="spantxChgOrigem"></span>
                            </div>
                        </div>
    '; 
    // Classificação                           
    echo '                            
                        <div class="row mt-2" id="divChgClassificacao">
                            <div class="col-md-4">
                                <label for="idChgClasse">Classe</label>
                                <input type="text" class="form-select cpoLimparChegada input-lg" id="txChgClasse" placeholder="Selecionar" name="txClasse"
                                    value="'.$_chegada['txClasse'].'"
                                    onfocus="iniciarPesquisa(\'ChgClasse\',this.value)"
                                    oninput="executarPesquisa(\'ChgClasse\',this.value)"
                                    onblur="finalizarPesquisa(\'ChgClasse\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada" id="idChgClasse" name="classe" value="'.$_chegada['classe'].'"/>
                                <span id="spantxChgClasse"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idChgsNatureza">Natureza</label>
                                <input type="text" class="form-select cpoLimparChegada input-lg" id="txChgNatureza" placeholder="Selecionar" name="txNatureza"
                                    value="'.$_chegada['txNatureza'].'"
                                    onfocus="iniciarPesquisa(\'ChgNatureza\',this.value)"
                                    oninput="executarPesquisa(\'ChgNatureza\',this.value)"
                                    onblur="finalizarPesquisa(\'ChgNatureza\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada" id="idChgNatureza" name="natureza" value="'.$_chegada['natureza'].'"/>
                                <span id="spantxChgNatureza"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idChgServico">Tipo de Serviço</label>
                                <input type="text" class="form-select cpoLimparChegada input-lg" id="txChgServico" placeholder="Selecionar" name="txServico"
                                    value="'.$_chegada['txServico'].'"
                                    onfocus="iniciarPesquisa(\'ChgServico\',this.value)"
                                    oninput="executarPesquisa(\'ChgServico\',this.value)"
                                    onblur="finalizarPesquisa(\'ChgServico\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada" id="idChgServico" name="servico" value="'.$_chegada['servico'].'"/>
                                <span id="spantxChgServico"></span>
                            </div>
                        </div>
    ';
    // Informações de Passageiros
    echo '                        
                        <div class="row mt-2" id="divChgInformacoes">
                            <div class="col-md-4">
                                <label for="txChgAssentos">Assentos</label></th>
                                <input type="text" class="form-control cpoLimparChegada cpoObrigatorio input-lg" id="txChgAssentos" name="assentos"
                                    value="'.$_chegada['assentos'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txChgPax">PAX</label></th>
                                <input type="text" class="form-control cpoLimparChegada cpoObrigatorio input-lg" id="txChgPax" name="pax"
                                    value="'.$_chegada['pax'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txChgPnae">PNAE</label></th>
                                <input type="text" class="form-control cpoLimparChegada cpoObrigatorio input-lg" id="txChgPnae" name="pnae"
                                    value="'.$_chegada['pnae'].'"/>
                            </div>
                        </div>
    ';

    // Recursos     
    echo '
                        <div class="row mt-2" id="divChgRecurso">
                            <div class="col-md-4">
                                <label for="idChgPosicao">Posição</label>
                                <input type="text" class="form-select caixaAlta cpoLimparChegada input-lg" id="txChgPosicao" placeholder="Selecionar" name="txPosicao"
                                    value="'.$_chegada['txPosicao'].'"
                                    onfocus="iniciarPesquisa(\'ChgPosicao\',this.value,\'POS\')"
                                    oninput="executarPesquisa(\'ChgPosicao\',this.value,\'POS\')"
                                    onblur="finalizarPesquisa(\'ChgPosicao\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada" id="idChgPosicao" name="posicao" value="'.$_chegada['idPosicao'].'"/>
                                <span id="spantxChgPosicao"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="idChgEsteira">Esteira</label>
                                <input type="text" class="form-select caixaAlta cpoLimparChegada input-lg" id="txChgEsteira" placeholder="Selecionar" name="txEsteira"
                                    value="'.$_chegada['txEsteira'].'"
                                    onfocus="iniciarPesquisa(\'ChgEsteira\',this.value,\'EST\')"
                                    oninput="executarPesquisa(\'ChgEsteira\',this.value,\'EST\')"
                                    onblur="finalizarPesquisa(\'ChgEsteira\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada" id="idChgEsteira" name="esteira" value="'.$_chegada['idEsteira'].'"/>
                                <span id="spantxChgEsteira"></span>
                            </div>
                        </div>                            
    ';

    // Movimento
    echo '                        
                        <div class="row mt-4" id="divChgDhMovimento">
                            <div class="col-md-4">
                                <label for="idChgMovimento">Movimento</label>
                                <input type="text" class="form-select cpoLimparChegada input-lg" id="txChgMovimento" placeholder="Selecionar" name="txMovimento"
                                    value="'.$_chegada['txMovimento'].'"
                                    onfocus="iniciarPesquisa(\'ChgMovimento\',this.value,\'CHG\')"
                                    oninput="executarPesquisa(\'ChgMovimento\',this.value,\'CHG\')"
                                    onblur="finalizarPesquisa(\'ChgMovimento\')"
                                    autocomplete="off">
                                <input type="hidden" class="cpoLimparChegada" id="idChgMovimento" name="cdMovimento" value="'.$_chegada['movimento'].'"/>
                                <span id="spantxChgMovimento"></span>
                            </div>                        
                            <div class="col-md-4">
                                <label for="txChgDtMovimento">Data Movimento</label></th>
                                <input type="date" class="form-control cpoLimparChegada cpoObrigatorio input-lg" id="txChgDtMovimento" name="dtMovimento"
                                    value="'.$_chegada['dtMovimento'].'"/>
                            </div>
                            <div class="col-md-4">
                                <label for="txChgHrMovimento">Hora Movimento</label></th>
                                <input type="time" class="form-control cpoLimparChegada cpoObrigatorio input-lg" id="txChgHrMovimento" name="hrMovimento"
                                    value="'.$_chegada['hrMovimento'].'"/>
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

function prepararChegada($_parametros){
    $_retorno = limparChegada($_parametros);
    $_comando = null;

    if (($_parametros['funcao'] == "Alteração") || 
        ($_parametros['funcao'] == "Inclusão" && $_parametros['movimento'] != "Previsão")) {
        try {
            $_conexao = conexao();
            $_comando = selectDB("UltimosMovimentosVoos"," AND vo.id = ".$_parametros['idChegada']." AND vm.id = ".$_parametros['idUltimo'],"");
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
            $_retorno['mensagem'] = array(traduzPDO($e->getMessage()));
            $_retorno['complemento'] = $_comando;
        }
    }

    if ($_parametros['movimento'] == "Previsão") {
        $_retorno['movimento'] = 'PRV';
        $_retorno['txMovimento'] = 'Previsão de Chegada';
        // Data e hora local do aeroporto
        $_retorno['dtMovimento'] = dateTimeUTC($_parametros['utcAeroporto'])->format('Y-m-d'); 
        $_retorno['hrMovimento'] = dateTimeUTC($_parametros['utcAeroporto'])->format('H:i');
    }

    return $_retorno;
}

function pegarIdUltimoMovimentoChegada($_parametros){
    $_comando = selectDB("UltimoIdMovimentoVoo","","",$_parametros['idChegada']);
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
                    throw new PDOException("Não existe último movimento para esta chegada! [id ".$_parametros['idChegada']."]");
                }
            } else {
                throw new PDOException("Não foi possível recuperar o último movimento desta chegada! [id ".$_parametros['idChegada']."]");
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

function pegarDigitacaoChegada($_parametros) {
    $_retorno = limparChegada($_parametros);

    // Carregar os posts
    $_retorno['voo'] = carregarPosts('chegada');;
    $_retorno['equipamento'] = carregarPosts('equipamento');
    $_retorno['assentos'] = carregarPosts('assentos');
    $_retorno['dhPrevista'] = carregarPosts('prevista');
    $_retorno['dhConfirmada'] = carregarPosts('confirmada');
    $_retorno['classe'] = carregarPosts('classe');
    $_retorno['natureza'] = carregarPosts('natureza');
    $_retorno['servico'] = carregarPosts('servico');
    $_retorno['origem'] = carregarPosts('origem');
    $_retorno['pax'] = carregarPosts('pax');
    $_retorno['pnae'] = carregarPosts('pnae');

    $_retorno['idPosicao'] = carregarPosts('posicao');
    $_retorno['idEsteira'] =carregarPosts('esteira');

    $_retorno['statusChegada'] = carregarPosts('statusChegada');
    $_retorno['vooPartida'] = carregarPosts('partida');

    $_retorno['movimento'] = carregarPosts('cdMovimento');
    $_retorno['dtMovimento'] = carregarPosts('dtMovimento');
    $_retorno['hrMovimento'] = carregarPosts('hrMovimento');

    $_retorno['txEquipamento'] = carregarPosts('txEquipamento');
    $_retorno['txMovimento'] = carregarPosts('txMovimento');
    $_retorno['txClasse'] = carregarPosts('txClasse');
    $_retorno['txNatureza'] = carregarPosts('txNatureza');
    $_retorno['txServico'] = carregarPosts('txServico');
    $_retorno['txOrigem'] = carregarPosts('txOrigem');
    $_retorno['txPosicao'] = carregarPosts('txPosicao');
    $_retorno['txEsteira'] = carregarPosts('txEsteira');

    return $_retorno;
}

function salvarChegada($_parametros,$_chegada){
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
    $dhMovimento = mudarDataHoraAMD($_chegada['dtMovimento']." ".$_chegada['hrMovimento']);
    switch ($movimento) {
        case "Chegada":
            $erros = camposPreenchidos(['chegada','equipamento','origem','classe','natureza','servico']);
        break;
        case "Previsão":
            $erros = camposPreenchidos(['chegada','equipamento','origem','classe','natureza','servico','cdMovimento','dtMovimento','hrMovimento']);
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
    // idChegada => id do registro de chegada
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
            case "Chegada":
                if ($_parametros['idChegada'] != "") {
                    $comando = "UPDATE gear_voos_operacionais SET idAeroporto=".$_parametros['aeroporto'].
                        ",equipamento='".substr($_chegada['txEquipamento'],0,100).
                        "',assentos=".mudarEmptyZeroMysql($_chegada['assentos']).
                        ",classe='".$_chegada['classe']."',natureza='".$_chegada['natureza'].
                        "',servico='".$_chegada['servico'].
                        "',origem='".substr($_chegada['txOrigem'],0,4).
                        "',idPosicao=".mudarEmptyNuloMysql($_chegada['idPosicao']).
                        ",idEsteira=".mudarEmptyNuloMysql($_chegada['idEsteira']).
                        ",pax=".mudarEmptyZeroMysql($_chegada['pax']).
                        ",pnae=".mudarEmptyZeroMysql($_chegada['pnae']).
                        ", cadastro = UTC_TIMESTAMP() WHERE id = ".$_parametros['idChegada'];
                }
                $arqDLOg = "gear_voos_operacionais";
                $chvDLOG = $_parametros['idChegada'];
            break;
            case "Previsão":
                $comando = "INSERT INTO gear_voos_operacionais(idAeroporto,operacao,operador,numeroVoo,". 
                            "equipamento,assentos,dtMovimento,dhPrevista,classe,natureza,". 
                            "servico,origem,idPosicao,idEsteira,pax,pnae,situacao,fonte,cadastro) VALUES (". 
                            $_parametros['aeroporto'].",'CHG','".substr($_chegada['voo'],0,3)."','".
                            substr($_chegada['voo'],3,4)."','".
                            substr($_chegada['txEquipamento'],0,100)."',".
                            mudarEmptyZeroMysql($_chegada['assentos']).",UTC_TIMESTAMP(),".$dhMovimento.",'".
                            $_chegada['classe']."','".$_chegada['natureza']."','".
                            $_chegada['servico']."','".substr($_chegada['txOrigem'],0,4)."',".
                            mudarEmptyNuloMysql($_chegada['idPosicao']).",".
                            mudarEmptyNuloMysql($_chegada['idEsteira']).",".
                            mudarEmptyZeroMysql($_chegada['pax']).",".
                            mudarEmptyZeroMysql($_chegada['pnae']).",'ATV','".
                            $_parametros['siglaAeroporto']."', UTC_TIMESTAMP())";
                $arqDLOg = "gear_voos_operacionais";
            break;
            case "Movimento":
                $comando = "INSERT INTO gear_voos_movimentos (idVoo, dhMovimento, movimento, usuario, cadastro) VALUES (".
                            $_parametros['idChegada'].",".$dhMovimento.",'".$_chegada['movimento']."', '".$_parametros['usuario'].
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
                        $_parametros['idChegada'] = null;
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

function limparChegada($_parametros){
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
        'txMovimento'=>'Previsão de Chegada',
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

function excluirMovimentoChegada($_parametros){
    // Preparando chamada da API apiManterChegadas
    $_token = gerarToken($_SESSION['plantaSistema']);
    $_post = ['token'=>$_token,'funcao'=>'ExcluirMovimentoChegada','parametros'=>$_parametros, 'chegada'=> array()];
    $_parametros = executaAPIs('apiManterChegadas.php', $_post);
    return $_parametros;
}

function barraFuncoesChegada($_titulo, $_parametros, $_impressao) {
    echo '
    <div class="row justify-content-between py-2">
        <li class="col-4 px-4 painel-header"><h5>'.$_titulo.'</h5></li>
        <div class="col-4">
            <a href="?tipo=Chegada&funcao=Inclusão&movimento=Previsão" class="btn btn-outline-primary btn-sm">
                <img src="../ativos/img/previsto.png"/> Incluir previsão</a>
            </a>
        </div>
        <div class ="col-4 d-md-flex justify-content-end">
            <button class="btn btn-outline-primary" type="button" title="Atualizar" id="buscarChegada">
                <img src="../ativos/img/atualizar.png"/></button>
    ';
    echo '
            <button class="btn btn-outline-primary" type="button" title="Pesquisar" id="iniciarPesquisaChegada"
                data-bs-toggle="modal" data-bs-target="#pesquisarChegada">
                <img src="../ativos/img/pesquisar.png"/></button>
    ';
    if ($_impressao) {
        echo '<button class="btn btn-outline-primary" type="button" title="Exportar PDF" id="exportarPDF">
                <img src="../ativos/img/exportarPDF.png"/></button>';
        echo '<button class="btn btn-outline-primary" type="button" title="Impressão" id="print" onclick="window.print()">
                <img src="../ativos/img/imprimir.png"/></button>';
    }
    if ($_parametros['objetivo'] == 'painel') {
        echo '<button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#painelChegadas">
                <img src="../ativos/img/visualizar.png" title="Expandir painel"/></button>
        ';
    }
    echo '
        </div>
    </div>
    ';
}

function pesquisarChegada($_parametros){
    switch ($_parametros['objetivo']) {
        case "chegada":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opVCH_ordenacao',"sm.id desc, sm.dhMovimento desc");
        break;
        case "movimento":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opMCH_ordenacao',"sm.id desc, sm.dhMovimento desc");
        break;
        case "painel":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opPCH_ordenacao',"sm.id desc, sm.dhMovimento desc");
        break;
    }
    echo '
    <!-- *************************************************** -->
    <!-- Modal PESQUISA CHEGADA -->
    <!-- *************************************************** -->
    <div class="modal fade" id="pesquisarChegada" tabindex="-1" aria-labelledby="pesquisarChegadaLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pesquisarChegadaLabel">Pesquisar Chegada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    ';
    // modal-body
    echo '
                <div class="modal-body">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="ptxChgVoo">Voo</label>
                            <input type="text" class="form-control cpoCookieChegada caixaAlta input-lg" id="ptxChgVoo"/>
                        </div>
                    </div>
    ';
    echo '
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="pslChgClasse">Classe</label>
                            <select class="form-select cpoCookieChegada selCookieChegada input-lg" id="pslChgClasse">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pslChgNatureza">Natureza</label>
                            <select class="form-select cpoCookieChegada selCookieChegada input-lg" id="pslChgNatureza">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="pslChgServico">Tipo de Serviço</label>
                            <select class="form-select cpoCookieChegada selCookieChegada input-lg" id="pslChgServico">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="ptxChgOrigem">Origem</label>
                            <input type="text" class="form-control cpoCookieChegada caixaAlta input-lg" id="ptxChgOrigem"/>
                        </div>
                    </div>
    ';
    echo '
                <br>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label for="pslChgOrdenacao">Ordenação da lista</label>
                        <select class="form-select cpoCookieChegada selCookieChegada input-lg" id="pslChgOrdenacao">
                            <option '.($_ordenacao == "vo.dhPrevista,vo.operador,vo.numeroVoo" ? "selected" : "").' value="vo.dhPrevista,vo.operador,vo.numeroVoo">Movimentação</option>
                        </select>
                    </div>
                </div>
            </div>
    ';
    // modal-footer
    echo '
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" id="limparPesquisaChegada"><img src="../ativos/img/limpar.png" title="Limpar"/></button>
                    <button type="button" class="btn btn-outline-primary" id="aplicarPesquisaChegada" data-bs-dismiss="modal"><img src="../ativos/img/pesquisar.png" title="Pesquisar"/></button>
                </div>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}

function desconectarVoos($_parametros){
    $comando = null;
    $conexao = conexao();
    try {
        $conexao = conexao();
        $conexao->beginTransaction();
        // Desconectando voo de partida da chegada
        $comando = "UPDATE gear_voos_operacionais SET idPartida = NULL WHERE id = ".$_parametros['idChegada'];
        $sql = $conexao->prepare($comando);
        if ($sql->execute()){
            gravaDLog("gear_voos_operacionais", "Desconectar", $_parametros['aeroporto'], $_parametros['usuario'],
                        $_parametros['idChegada'], $comando);

            // Desconectando voo de chegada da partida
            $comando = "UPDATE gear_voos_operacionais SET idChegada = NULL WHERE id = ".$_parametros['idPartida'];
            $sql = $conexao->prepare($comando);
            if ($sql->execute()){
                gravaDLog("gear_voos_operacionais", "Desconectar", $_parametros['aeroporto'], $_parametros['usuario'],
                            $_parametros['idPartida'], $comando);

                $_parametros['status'] = "success";
                $_parametros['mensagem'] = array("Voos desconectados com sucesso!");
                $_parametros['idChegada'] = null;
                $_parametros['idPartida'] = null;
                $_parametros['funcao'] = null;
                $conexao->commit();
            } else {
                throw new PDOException("Não foi possível desconectar a partida!");
            }
        } else {
            throw new PDOException("Não foi possível desconectar a chegada!");
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

// Conectando voo de partida a chegada
function salvarConectarChegada($_parametros) {
    $comando = null;
    $conexao = conexao();

    // Conecta voos 
    try {
        // Verifica se ID de partida foi informado
        if (empty($_parametros['idPartida'])) {
            throw new PDOException("Voo de partida não foi informado!");
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

                // Verifica se a partida já está conectada a um status, caso positivo conecta o voo de chegada ao status da partida
                $comando = "SELECT id FROM gear_status WHERE idPartida = ".$_parametros['idPartida'];
                $sql = $conexao->prepare($comando);
                if ($sql->execute()){
                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $dados) {
                        $_id = $dados['id'];
                    }
                    if (!empty($_id)) {
                        $comando = "UPDATE gear_status SET idChegada = ".$_parametros['idChegada']." WHERE id = ".$_id;
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()){
                            gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                        $_parametros['idStatus'], $comando);
                        } else {
                            throw new PDOException("Não foi possível conectar o voo de chegada ao status!");
                        }
                    } 
                    $_parametros['status'] = "success";
                    $_parametros['mensagem'] = array("Voos conectados com sucesso!");
                    $_parametros['idChegada'] = null;
                    $_parametros['idPartida'] = null;
                    $_parametros['funcao'] = null;   
                    if ($conexao->inTransaction()) {$conexao->commit();}        
                } else {
                    throw new PDOException("Não foi possível verificar conexão do voo de partida com o status!");
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
    async function buscarChegada() {
        var filtro = "";
        var descricaoFiltro = "";

        // Decidindo o filtro base de acordo com o objetivo do formulario
        switch ($('#hdObjetivo').val()) {
            case "chegada":
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

        $(".cpoCookieChegada").each(function(){
            if (!isEmpty($(this).val())) {
                switch ($(this).attr('id')) {
                    case "ptxChgVoo":
                        filtro += " AND CONCAT('!',vo.operador,vo.numeroVoo,'!') LIKE '%_"+$("#ptxChgVoo").val()+"_%'";
                        descricaoFiltro += " <br>Voo : "+$("#ptxChgVoo").val();
                    break;
                    case "pslChgNatureza":
                        filtro += " AND vo.natureza = '"+$("#pslChgNatureza").val()+"'";
                        descricaoFiltro += ' <br>Natureza : '+$("#pslChgNatureza :selected").text();
                    break;
                    case "pslChgClasse":
                        filtro += " AND vo.classe = '"+$("#pslChgClasse").val()+"'";
                        descricaoFiltro += ' <br>Classe : '+$("#pslChgClasse :selected").text();
                    break;
                    case "pslChgServico":
                        filtro += " AND vo.servico = '"+$("#pslChgServico").val()+"'";
                        descricaoFiltro += ' <br>Tipo de Serviço : '+$("#pslChgServico :selected").text();
                    break;
                    case "ptxChgOrigem":
                        filtro += " AND vo.origem LIKE '%"+$("#ptxChgOrigem").val()+"%'";
                        descricaoFiltro += " <br>Origem : "+$("#ptxChgOrigem").val();
                    break;
                    case "pslChgMovimento":
                        filtro += " AND vm.movimento = '"+$("#pslChgMovimento").val()+"'";
                        descricaoFiltro += ' <br>Movimento : '+$("#pslChgMovimento :selected").text();
                    break;
                    default:
                        filtro += "";
                        descricaoFiltro += "";
                }
            }
        });

        // Montagem da ordem
        var ordem = $("#pslChgOrdenacao").val();

        // Decidindo o carregamento das informações de acordo com o objetivo do formulario
        switch ($('#hdObjetivo').val()) {
            case "chegada":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opVCH_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opVCH_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opVCH_descricao', descricaoFiltro);
                //await opCarregarStatus('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            break;
            case "movimento":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMCH_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMCH_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMCH_descricao', descricaoFiltro);
                //await opCarregarUltimosMovimentos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            break;
            case "painel":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPCH_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPCH_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPCH_descricao', descricaoFiltro);
                await ajaxChegadas($('#hdSiglaAeroporto').val());
            break;
        }
    }

    async function prepararModalChegada(){
        if ($('#hdMovimento').val() != "") {
            switch($('#hdMovimento').val()) {
                case "Chegada":
                    $('#divChgDhMovimento').css('display','none');
                break;
                case "Previsão":
                    $('#txChgChegada').attr('readonly', false);
                    $('#divChgPartida').css('display', 'none');
                    $('#divChgStatus').css('display', 'none');
                    $('#divChgHorarios').css('display', 'none');
                    $('#txChgMovimento').attr('readonly', true);
                break;
                case "Movimento":
                    $('#divChgEqpOrigem').css('display', 'none');
                    $('#divChgClassificacao').css('display', 'none');
                    $('#divChgInformacoes').css('display', 'none');
                    $('#divChgRecurso').css('display', 'none');
                break;
                default:
                    $('#divChgEqpOrigem').css('display', 'none');
                    $('#divChgClassificacao').css('display', 'none');
                    $('#divChgInformacoes').css('display', 'none');
                    $('#divChgRecurso').css('display', 'none');
                    $('#txChgMovimento').attr('readonly', true);
                break;
            }
        }        
    }

    async function prepararConectarChegada(idAeroporto, idChegada){
        //
        // REVISAR - PEGAR A dhConfirmada da Chegada
        //
        var filtro = " AND vo.idAeroporto = "+idAeroporto+
                     " AND vo.operacao = 'PRT' AND vo.idChegada IS NULL "+
                     " AND vo.dhConfirmada >= (SELECT dhConfirmada FROM gear_voos_horario_confirmado WHERE idVoo = "+idChegada+")";
        await suCarregarSelectTodos('ConectarVoos','#pslConectarVoos', '', filtro, 'Cadastrar');
    }

    // function ajaxChegadas(siglaAeroporto){
    //     var ordem = valorCookie(siglaAeroporto+'_opPCH_ordenacao','');
    //     var filtro = " " + valorCookie(siglaAeroporto+'_opPCH_filtro','');
    //     var descricao = valorCookie(siglaAeroporto+'_opPCH_descricao','');
    //     $.ajax({
    //         url: "opMovimentosChegadasPainel.php?filtro="+filtro+"&descricao="+descricao+"&ordem="+ordem+"&busca=&pagina=0&limite=0",
    //         type: "GET",
    //         success: function(chegada){
    //             $("#painelChegadas").fadeOut(0, function(){
    //                 $(this).html(chegada);
    //                 $(this).fadeIn(0);
    //             });
    //         }
    //     });
    // }
</script>