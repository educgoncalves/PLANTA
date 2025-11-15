<?php
function modalStatus($_parametros,$_status) {
    echo '
    <!-- *************************************************** -->
    <!-- Modal STATUS -->
    <!-- *************************************************** -->
    <button id="botaoStatus" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal"
        data-bs-target="#editarStatus">Status</button>

    <div class="modal fade" id="editarStatus" tabindex="-1" aria-labelledby="editarStatusLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header alert alert-padrao">
                    <h5 class="modal-title" id="editarStatusLabel">'.$_parametros['funcao'].' de '.$_parametros['movimento'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <div class="modal-body">
                
                <form action="?tipo=Status'.
                                '&objetivo='.$_parametros['objetivo'].
                                '&funcao='.$_parametros['funcao'].
                                '&evento=salvar&movimento='.$_parametros['movimento'].
                                '" method="POST" class="form-group" id="editarStatusForm">
                           
    ';
    // Obrigatório em todos os modais
    echo '
                    <input type="hidden" id="hdIdStatus" name="idStatus" value="'.$_parametros['idStatus'].'"/>
                    <input type="hidden" id="hdIdChegada" name="idChegada" value="'.$_parametros['idChegada'].'"/>
                    <input type="hidden" id="hdIdPartida" name="idPartida" value="'.$_parametros['idPartida'].'"/>
                    <input type="hidden" id="hdIdMovimento" name="idMovimento" value="'.$_parametros['idMovimento'].'"/>
                    <input type="hidden" id="hdIdUltimo" name="idUltimo" value="'.$_parametros['idUltimo'].'"/>
    ';
    exibirMensagem('Status');
    echo '
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="col-md-12" id="divStsStatus">
                                <label for="txStsStatus">Status</label>
                                <input type="text" class="form-control input-lg" id="txStsStatus" name="status" readonly value="'.$_status['status'].'"/>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-12 d-md-flex justify-content-md-end">
                                <button class="btn btn-outline-primary" type="button" title="Limpar" id="limparFormularioStatus">
                                    <img src="../ativos/img/apagar.png"/></button>
                                <button class="btn btn-outline-primary" type="submit" title="Salvar" id="salvarFormularioStatus">
                                    <img src="../ativos/img/salvar.png"/></button>                        
                            </div>
                        </div>     
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4" id="divStsMatricula">
                            <label for="idStsMatricula" class="cpoObrigatorio">Matrícula</label>
                            <div class="input-group">
                                <input type="text" class="form-select caixaAlta cpoLimparStatus input-lg" id="txStsMatricula" placeholder="Selecionar" name="txMatricula"
                                    value="'.$_status['txMatricula'].'"
                                    onfocus="iniciarPesquisa(\'StsMatricula\',this.value)"
                                    oninput="executarPesquisa(\'StsMatricula\',this.value)"
                                    onblur="finalizarPesquisa(\'StsMatricula\')"
                                    autocomplete="off">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mdMatricula"> + </button>
                            </div>   
                            <input type="hidden" class="cpoLimparStatus cpoObrigatorio" id="idStsMatricula" name="matricula" value="'.$_status['matricula'].'"/>
                            <span id="spantxStsMatricula"></span>
                        </div>
                        <div class="col-md-4" id="divStsOrigem">
                            <label for="idStsOrigem" class="cpoObrigatorio">Origem</label>
                            <div class="input-group">
                                <input type="text" class="form-select caixaAlta cpoLimparStatus input-lg" id="txStsOrigem" placeholder="Selecionar" name="txOrigem"
                                    value="'.$_status['txOrigem'].'"
                                    onfocus="iniciarPesquisa(\'StsOrigem\',this.value)"
                                    oninput="executarPesquisa(\'StsOrigem\',this.value)"
                                    onblur="finalizarPesquisa(\'StsOrigem\')"
                                    autocomplete="off">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mdOrigem"> + </button>
                            </div>  
                            <input type="hidden" class="cpoLimparStatus cpoObrigatorio" id="idStsOrigem" name="origem" value="'.$_status['origem'].'"/>
                            <span id="spantxStsOrigem"></span>
                        </div>
                        <div class="col-md-4" id="divStsDestino">
                            <label for="idStsDestino">Destino</label>
                            <div class="input-group">
                                <input type="text" class="form-select caixaAlta cpoLimparStatus input-lg" id="txStsDestino" placeholder="Selecionar" name="txDestino"
                                    value="'.$_status['txDestino'].'"
                                    onfocus="iniciarPesquisa(\'StsDestino\',this.value)"
                                    oninput="executarPesquisa(\'StsDestino\',this.value)"
                                    onblur="finalizarPesquisa(\'StsDestino\')"
                                    autocomplete="off">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mdDestino"> + </button>
                            </div>                                  
                            <input type="hidden" class="cpoLimparStatus" id="idStsDestino" name="destino" value="'.$_status['destino'].'"/>
                            <span id="spantxStsDestino"></span>
                        </div>
                    </div>
                    <div class="row mt-2" id="divStsClassificacao">
                        <div class="col-md-4">
                            <label for="idStsClasse">Classe</label>
                            <input type="text" class="form-select cpoLimparStatus input-lg" id="txStsClasse" placeholder="Selecionar" name="txClasse"
                                value="'.$_status['txClasse'].'"
                                onfocus="iniciarPesquisa(\'StsClasse\',this.value)"
                                oninput="executarPesquisa(\'StsClasse\',this.value)"
                                onblur="finalizarPesquisa(\'StsClasse\')"
                                autocomplete="off">
                            <input type="hidden" class="cpoLimparStatus" id="idStsClasse" name="classe" value="'.$_status['classe'].'"/>
                            <span id="spantxStsClasse"></span>
                        </div>
                        <div class="col-md-4">
                            <label for="idStsNatureza">Natureza</label>
                            <input type="text" class="form-select cpoLimparStatus input-lg" id="txStsNatureza" placeholder="Selecionar" name="txNatureza"
                                value="'.$_status['txNatureza'].'"
                                onfocus="iniciarPesquisa(\'StsNatureza\',this.value)"
                                oninput="executarPesquisa(\'StsNatureza\',this.value)"
                                onblur="finalizarPesquisa(\'StsNatureza\')"
                                autocomplete="off">
                            <input type="hidden" class="cpoLimparStatus" id="idStsNatureza" name="natureza" value="'.$_status['natureza'].'"/>
                            <span id="spantxStsNatureza"></span>
                        </div>
                        <div class="col-md-4">
                            <label for="idStsServico">Tipo de Serviço</label>
                            <input type="text" class="form-select cpoLimparStatus input-lg" id="txStsServico" placeholder="Selecionar" name="txServico"
                                value="'.$_status['txServico'].'"
                                onfocus="iniciarPesquisa(\'StsServico\',this.value)"
                                oninput="executarPesquisa(\'StsServico\',this.value)"
                                onblur="finalizarPesquisa(\'StsServico\')"
                                autocomplete="off">
                            <input type="hidden" class="cpoLimparStatus" id="idStsServico" name="servico" value="'.$_status['servico'].'"/>
                            <span id="spantxStsServico"></span>
                        </div>
                    </div>
    ';
    // Movimento
    echo '          <div class="row mt-4" id="divStsMovimento">
    ';
    echo '              <div class="col-md-3">
                            <label for="idStsMovimento">Movimento</label>
                            <input type="text" class="form-select cpoLimparStatus cpoObrigatorio input-lg" id="txStsMovimento" placeholder="Selecionar" name="txMovimento"
                                value="'.$_status['txMovimento'].'"
                                onfocus="iniciarPesquisa(\'StsMovimento\',this.value,\'STA,'.$_status['movimento'].'\')"
                                oninput="executarPesquisa(\'StsMovimento\',this.value,\'STA,'.$_status['movimento'].'\')"
                                onblur="finalizarPesquisa(\'StsMovimento\'); filtrarStsRecursos();"
                                onchange="filtrarStsRecursos()"
                                autocomplete="off">
                            <input type="hidden" class="cpoLimparStatus" id="idStsMovimento" name="cdMovimento" value="'.$_status['movimento'].'"/>
                            <span id="spantxStsMovimento"></span>
                        </div> 
    ';
    echo '              <div class="row mt-4">
                        <div class="col-md-3">
                            <label for="txStsDtMovimento">Data Movimento</label></th>
                            <input type="date" class="form-control cpoLimparStatus cpoObrigatorio input-lg" id="txStsDtMovimento" name="dtMovimento"
                                value="'.$_status['dtMovimento'].'"/>
                        </div>
                        <div class="col-md-3">
                            <label for="txStsHrMovimento">Hora Movimento</label></th>
                            <input type="time" class="form-control cpoLimparStatus cpoObrigatorio input-lg" id="txStsHrMovimento" name="hrMovimento"
                                value="'.$_status['hrMovimento'].'"/>
                        </div>
                        <div class="col-md-3"> 
                            <div id="divStsRecurso"></div>
                            <input type="hidden" class="cpoLimparStatus" id="idStsRecurso" name="recurso" value="'.$_status['recurso'].'"/>
                            <input type="hidden" class="cpoLimparStatus" id="dsStsRecurso" value="'.$_status['txRecurso'].'"/>
                            <span id="spantxStsRecurso"></span>
                        </div>
                        <div class="col-md-3"> 
                            <div id="divStsSegundoRecurso"></div>
                            <input type="hidden" class="cpoLimparStatus" id="idStsSegundoRecurso" name="segundoRecurso" value="'.$_status['segundoRecurso'].'"/>
                            <input type="hidden" class="cpoLimparStatus" id="dsStsSegundoRecurso" value="'.$_status['txSegundoRecurso'].'"/>
                            <span id="spantxStsSegundoRecurso"></span>
                        </div>
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

function modalStatusComplementos($_parametros,$_status) {
    echo '
        <!-- *************************************************** -->
        <!-- Modal STATUS COMPLEMENTO-->
        <!-- *************************************************** -->
        <button id="botaoStatusComplementos" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal"
            data-bs-target="#editarStatusComplementos">Complementos</button>

        <div class="modal fade" id="editarStatusComplementos" tabindex="-1" aria-labelledby="editarStatusComplementosLabel" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header alert alert-padrao">
                        <h5 class="modal-title" id="editarStatusComplementosLabel">Informações Complementares</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                    </div>
                    <div class="modal-body">
                    <form action="?tipo=StatusComplementos'.
                                    '&objetivo='.$_parametros['objetivo'].
                                    '&funcao='.$_parametros['funcao'].
                                    '&evento=salvar&movimento='.$_parametros['movimento'].
                                    '" method="POST" class="form-group" id="editarStatusComplementosForm">
    ';
    // Obrigatório em todos os modais
    echo '
                    <input type="hidden" id="hdIdStatus" name="idStatus" value="'.$_parametros['idStatus'].'"/>
                    <input type="hidden" id="hdIdChegada" name="idChegada" value="'.$_parametros['idChegada'].'"/>
                    <input type="hidden" id="hdIdPartida" name="idPartida" value="'.$_parametros['idPartida'].'"/>
                    <input type="hidden" id="hdIdMovimento" name="idMovimento" value="'.$_parametros['idMovimento'].'"/>
                    <input type="hidden" id="hdIdUltimo" name="idUltimo" value="'.$_parametros['idUltimo'].'"/>
                    <input type="hidden" id="hdIdComplemento" name="idComplemento" value="'.$_status['idComplemento'].'"/>
    ';
    exibirMensagem('StatusComplementos');
    echo '
                    <div class="row mt-2">
                        <div class="col-md-3" id="divStsCmpStatus">
                            <label for="txStsCmpStatus">Status</label>
                            <input type="text" class="form-control input-lg" id="txStsCmpStatus" name="statusxx" readonly="true" value="'.$_status['status'].'"/>
                        </div>
                        <div class="col-md-3" id="divStsCplMatricula">
                            <label for="txStsCmpMatricula">Matrícula</label>
                            <input type="text" class="form-control input-lg" id="txStsCmpMatricula" name="matricula" readonly="true" 
                                value="'.$_status['txMatricula'].'"/>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12 d-md-flex justify-content-md-end">
                                <button class="btn btn-outline-primary" type="button" title="Limpar" id="limparFormularioStsComplementos">
                                    <img src="../ativos/img/apagar.png"/></button>
                                <button class="btn btn-outline-primary" type="submit" title="Salvar" id="salvarFormularioStsComplementos">
                                    <img src="../ativos/img/salvar.png"/></button>                        
                            </div>
                        </div>     
                    </div>
    ';
    echo '          <div class="row mt-2">
                        <div class="col-md-8" id="divStsCmpComando">
                            <label for="idStsCmpComando">Comando</label>
                            <div class="input-group">
                                <input type="text" class="form-select caixaAlta cpoLimparStsComplementos input-lg" id="txStsCmpComando" placeholder="Selecionar" name="txComando"
                                    value="'.$_status['txComando'].'"
                                    onfocus="iniciarPesquisa(\'StsCmpComando\',this.value)"
                                    oninput="executarPesquisa(\'StsCmpComando\',this.value)"
                                    onblur="finalizarPesquisa(\'StsCmpComando\')"
                                    autocomplete="off">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mdComando"> + </button>
                            </div>                                    
                            <input type="hidden" class="cpoLimparStsComplementos" id="idStsCmpComando" name="comando" value="'.$_status['idComando'].'"/>
                            <span id="spantxStsCmpComando"></span>
                        </div>
                        <div class="col-md-4" id="divStsCmpRegra">
                            <label for="idStsCmpRegra">Regra</label>
                            <input type="text" class="form-select cpoLimparStsComplementos input-lg" id="txStsCmpRegra" placeholder="Selecionar" name="txRegra"
                                value="'.$_status['txRegra'].'"
                                onfocus="iniciarPesquisa(\'StsCmpRegra\',this.value)"
                                oninput="executarPesquisa(\'StsCmpRegra\',this.value)"
                                onblur="finalizarPesquisa(\'StsCmpRegra\')"
                                autocomplete="off">
                            <input type="hidden" class="cpoLimparStsComplementos" id="idStsCmpRegra" name="regra" value="'.$_status['regra'].'"/>
                            <span id="spantxStsCmpRegra"></span>
                        </div>
                    </div>
    ';   
    echo '          <div class="row mt-2">   
                        <div class="col-md-6"> 
                            <div class="row mt-2">  
                                <label><b>Desembarque</b></label>
                                <div class="col-md-4">
                                    <label for="txDesembarquePax">Passageiros</label>
                                    <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txDesembarquePax" name="desembarque_pax" maxlength="4"
                                        value="'.$_status['desembarque_pax'].'"/>
                                </div>
                                <div class="col-md-4">
                                    <label for="txDesembarqueCarga">Carga</label>
                                    <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txDesembarqueCarga" name="desembarque_carga" maxlength="4"
                                        value="'.$_status['desembarque_carga'].'"/>
                                </div>
                                <div class="col-md-4">
                                    <label for="txDesembarqueCorreio">Correio</label>
                                    <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txDesembarqueCorreio" name="desembarque_correio" maxlength="4"
                                        value="'.$_status['desembarque_correio'].'"/>
                                </div>
                            </div>
                            <div class="row mt-2">  
                                <label><b>Embarque</b></label>
                                <div class="col-md-4">
                                    <label for="txEmbarquePax">Passageiros</label>
                                    <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txEmbarquePax" name="embarque_pax" maxlength="4"
                                        value="'.$_status['embarque_pax'].'"/>
                                </div>
                                <div class="col-md-4">
                                    <label for="txEmbarqueCarga">Carga</label>
                                    <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txEmbarqueCarga" name="embarque_carga" maxlength="4"
                                        value="'.$_status['embarque_carga'].'"/>
                                </div>
                                <div class="col-md-4">
                                    <label for="txEmbarqueCorreio">Correio</label>
                                    <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txEmbarqueCorreio" name="embarque_correio" maxlength="4"
                                        value="'.$_status['embarque_correio'].'"/>
                                </div>
                            </div>
                            <div class="row mt-2">   
                                <div class="row mt-2">  
                                    <label><b>Trânsito</b></label>                                
                                    <div class="col-md-4">
                                        <label for="txTransitoPax">Passageiros</label>
                                        <input type="text" class="form-control cpoLimparStsComplementos input-lg" id="txTransitoPax" name="transito_pax" maxlength="4"
                                            value="'.$_status['transito_pax'].'"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">                         
                            <div class="row mt-2">  
                                <label></label>
                                <div class="col-md-12">
                                    <label for="txObservacao"><b>Observação</b></label>
                                    <textarea class="form-control cpoLimparStsComplementos input-lg" id="txObservacao" name="observacao"
                                        rows="10" maxlength="255">'.$_status['observacao'].'</textarea> 
                                </div>
                            </div>
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

function prepararStatus($_parametros) {
    $_retorno = limparStatus($_parametros);
    $_comando = null;
    
    if (($_parametros['funcao'] == "Alteração") || 
        ($_parametros['funcao'] == "Inclusão" && $_parametros['movimento'] != "Pouso" && $_parametros['movimento'] != "Previsão")) {
        try {
            $_conexao = conexao();
            $_comando = selectDB("UltimosMovimentosStatus"," AND st.id = ".$_parametros['idStatus'],"");
            $_sql = $_conexao->prepare($_comando);
            if ($_sql->execute()) {
                $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
                foreach ($_registros as $_dados) {
                    $_retorno['status'] = $_dados['status'];
                    $_retorno['matricula'] = $_dados['idMatricula'];
                    $_retorno['classe'] = $_dados['classe'];
                    $_retorno['natureza'] = $_dados['natureza'];
                    $_retorno['servico'] = $_dados['servico'];

                    $_retorno['movimento'] = $_dados['movimento'];
                    $_retorno['dtMovimento'] = $_dados['dtMovimento'];
                    $_retorno['hrMovimento'] = $_dados['hrMovimento'];

                    $_retorno['origem'] = $_dados['idOrigem'];
                    $_retorno['destino'] = $_dados['idDestino'];
                    $_retorno['recurso'] = $_dados['idRecurso']; 
                    $_retorno['segundoRecurso'] = $_dados['idSegundoRecurso'];

                    //($_parametros['funcao'] == "Alteração" || $_parametros['movimento'] == "Saída" ? $_dados['idRecurso'] : "");
                    $_retorno['faturado'] = $_dados['descFaturado'];
                    $_retorno['situacao'] = $_dados['situacao'];

                    $_retorno['txMatricula'] = $_dados['matricula'];
                    $_retorno['txMovimento'] = $_dados['descMovimento'];
                    $_retorno['txClasse'] = $_dados['descClasse'];
                    $_retorno['txNatureza'] = $_dados['descNatureza'];
                    $_retorno['txServico'] = $_dados['descServico'];
                    $_retorno['txOrigem'] = $_dados['origem'];
                    $_retorno['txDestino'] = $_dados['destino'];
                    $_retorno['txRecurso'] = $_dados['descRecurso']; 
                    $_retorno['txSegundoRecurso'] = $_dados['descSegundoRecurso'];

                    //($_parametros['funcao'] == "Alteração" || $_parametros['movimento'] == "Saída" ? $_dados['descRecurso'] : "");
                    $_retorno['txSituacao'] = $_dados['descSituacao'];

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
    return $_retorno;
}

function prepararStatusComplementos($_parametros) {
    $_retorno = limparStatusComplementos();
    $_comando = null;
    
    try {
        $_conexao = conexao();
        $_comando = selectDB("StatusComplementos"," AND st.id = ".$_parametros['idStatus'],"");
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()) {
            $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($_registros as $_dados) {
                $_retorno['idStatus'] = $_dados['id'];
                $_retorno['status'] = $_dados['status'];
                $_retorno['idComplemento'] = $_dados['idComplemento'];
                $_retorno['regra'] = $_dados['regra'];
                $_retorno['embarque_pax'] = $_dados['embarque_pax'];
                $_retorno['embarque_carga'] = $_dados['embarque_carga'];
                $_retorno['embarque_correio'] = $_dados['embarque_correio'];
                $_retorno['desembarque_pax'] = $_dados['desembarque_pax'];
                $_retorno['desembarque_carga'] = $_dados['desembarque_carga'];
                $_retorno['desembarque_correio'] = $_dados['desembarque_correio'];
                $_retorno['transito_pax'] = $_dados['transito_pax'];
                $_retorno['observacao'] = $_dados['observacao'];
                $_retorno['idComando'] = $_dados['idComando'];
                $_retorno['codigoAnac'] = $_dados['codigoAnac'];
                $_retorno['nome'] = $_dados['nome'];

                $_retorno['txMatricula'] = $_dados['matricula'];
                $_retorno['txRegra'] = $_dados['descRegra'];
                $_retorno['txComando'] = $_dados['comandante'];

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

    return $_retorno;
}

function pegarIdUltimoMovimentoStatus($_parametros){
    $_comando = selectDB("UltimoIdMovimentoStatus","","",$_parametros['idStatus']);
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
                    throw new PDOException("Não existe último movimento para este status! [id ".$_parametros['idStatus']."]");
                }
            } else {
                throw new PDOException("Não foi possível recuperar o último movimento deste status! [id ".$_parametros['idStatus']."]");
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

function pegarDigitacaoStatus($_parametros) {
    $_retorno = limparStatus($_parametros);

    $_retorno['status'] = carregarPosts('status','TESTE');
    $_retorno['matricula'] = carregarPosts('matricula');
    $_retorno['classe'] = carregarPosts('classe');
    $_retorno['natureza'] = carregarPosts('natureza');
    $_retorno['servico'] = carregarPosts('servico');
    
    $_retorno['movimento'] = carregarPosts('cdMovimento');
    $_retorno['dtMovimento'] = carregarPosts('dtMovimento');
    $_retorno['hrMovimento'] = carregarPosts('hrMovimento');
    
    $_retorno['origem'] = carregarPosts('origem');
    $_retorno['destino'] = carregarPosts('destino');
    $_retorno['recurso'] = carregarPosts('recurso');
    $_retorno['segundoRecurso'] = carregarPosts('segundoRecurso');

    $_retorno['txMovimento'] = carregarPosts('txMovimento');
    $_retorno['txMatricula'] = carregarPosts('txMatricula');
    $_retorno['txClasse'] = carregarPosts('txClasse');
    $_retorno['txNatureza'] = carregarPosts('txNatureza');
    $_retorno['txServico'] = carregarPosts('txServico');
    $_retorno['txOrigem'] = carregarPosts('txOrigem');
    $_retorno['txDestino'] = carregarPosts('txDestino');
    $_retorno['txRecurso'] = carregarPosts('txRecurso');
    $_retorno['txSegundoRecurso'] = carregarPosts('txSegundoRecurso');
    $_retorno['txSituacao'] = carregarPosts("txSituacao");

    return $_retorno;
}

function pegarDigitacaoStatusComplementos() {
    $_retorno = limparStatusComplementos();

    $_retorno['idStatus'] = carregarPosts('idStatus');
    $_retorno['status'] = carregarPosts('status');
    $_retorno['idComplemento'] = carregarPosts('idComplemento');
    $_retorno['regra'] = carregarPosts('regra','');
    $_retorno['embarque_pax'] = carregarPosts('embarque_pax','0');
    $_retorno['embarque_carga'] = carregarPosts('embarque_carga','0');
    $_retorno['embarque_correio'] = carregarPosts('embarque_correio','0');
    $_retorno['desembarque_pax'] = carregarPosts('desembarque_pax','0');
    $_retorno['desembarque_carga'] = carregarPosts('desembarque_carga','0');
    $_retorno['desembarque_correio'] = carregarPosts('desembarque_correio','0');
    $_retorno['transito_pax'] = carregarPosts('transito_pax','0');
    $_retorno['observacao'] = carregarPosts('observacao','');
    $_retorno['idComando'] = carregarPosts('comando','0');

    $_retorno['txMatricula'] = carregarPosts('txMatricula');
    $_retorno['txRegra'] = carregarPosts('txRegra');
    $_retorno['txComando'] = carregarPosts('txComando');

    return $_retorno;
}

function salvarStatus($_parametros,$_status) {
    // var_dump('Parametros');
    // var_dump($_parametros);
    // var_dump('Status');
    // var_dump($_status);
    // Preparando chamada da API apiManterStatus
    $_token = gerarToken($_SESSION['plantaSistema']);
    $_post = ['token'=>$_token,'funcao'=>'SalvarStatus','parametros'=>$_parametros, 'status'=>$_status];
    $_parametros = executaAPIs('apiManterStatus.php', $_post);
    return $_parametros;
}

function salvarStatusComplementos($_parametros,$_status) {
    // Salvando as informações
    //
    $erros = "";
    //
    // Verifica críticas e consistências 
    //
    $erros = camposPreenchidos(['comando', 'regra', 'embarque_pax', 'embarque_carga', 'embarque_correio', 
                'desembarque_pax', 'desembarque_carga', 'desembarque_correio', 'transito_pax', 'observacao']);
    //
    // Só prossegue se tudo ok
    //
    $operacao = "";
    $funcao = "";
    switch  (true) {
        case ($_status['idComplemento'] == ""):
            if(count($erros) != 10){
                $operacao = "Inclusão";
                $funcao = "incluído";
                $comando = "INSERT INTO gear_status_complementos (idStatus, idComandante, regra, embarque_pax, embarque_carga".
                            ", embarque_correio, desembarque_pax, desembarque_carga, desembarque_correio, transito_pax". 
                            ", observacao, cadastro) VALUES (".$_status['idStatus'].", ".$_status['idComando'].", '".$_status['regra'].
                            "', ".$_status['embarque_pax'].", ".$_status['embarque_carga'].", ".$_status['embarque_correio'].
                            ", ".$_status['desembarque_pax'].", ".$_status['desembarque_carga'].
                            ", ".$_status['desembarque_correio'].", ".$_status['transito_pax'].", '".
                            $_status['observacao']."', UTC_TIMESTAMP())";
            } else {
                $_parametros['status'] = 'danger';
                $_parametros['mensagem'] = array("Um dos campos deve ser informado!");
                $_parametros['complemento'] = "";
            }
        break;

        case ($_status['idComplemento'] != ""):
            if(count($erros) == 10){
                $operacao = "Exclusão";
                $funcao = "excluído";
                $comando = "DELETE FROM gear_status_complementos WHERE id = ".$_status['idComplemento'];
            } else {
                $operacao = "Alteração";
                $funcao = "alterado";
                $comando = "UPDATE gear_status_complementos SET idComandante = ".$_status['idComando'].", regra = '".
                            $_status['regra']."', embarque_pax = ".$_status['embarque_pax'].", embarque_carga = ".
                            $_status['embarque_carga'].", embarque_correio = ".$_status['embarque_correio'].", desembarque_pax = ".
                            $_status['desembarque_pax'].", desembarque_carga = ".$_status['desembarque_carga'].", desembarque_correio = ".
                            $_status['desembarque_correio'].", transito_pax = ".$_status['transito_pax'].", observacao = '".
                            $_status['observacao']."', cadastro = UTC_TIMESTAMP() WHERE id = ".$_status['idComplemento'];
            }
        break;  
    }

    if ($operacao != "") {
        try {
            $conexao = conexao();
            $sql = $conexao->prepare($comando);
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_status_complementos", $operacao, $_parametros['siglaAeroporto'], $_parametros['usuario'], 
                        ($_status['idComplemento'] != "" ? $_status['idComplemento']  : $conexao->lastInsertId()), $comando);
                    $_parametros['status'] = "success";
                    $_parametros['mensagem'] = array("Registro ".$funcao." com sucesso!");
                    $_parametros['complemento'] = "";
                    $_parametros['idStatus'] = null;
                    $_parametros['idMovimento'] = null;
                    $_parametros['idUltimo'] = null;
                    $_parametros['funcao'] = null;
                } else {
                    throw new PDOException("Não foi possível efetivar esta ".($_status['idComplemento'] != "" ? "alteração" : "inclusão")."!");
                }
            } else {
                throw new PDOException("Não foi possível ".($_status['idComplemento'] != "" ? "alterar" : "incluir")." este registro!");
            }
        } catch (PDOException $e) {
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
            $_parametros['complemento'] = $comando;
        }
            
    }
    return $_parametros;
}

function limparStatus($_parametros){
    $_retorno = array(
        'status'=>null,
        'matricula'=>null,
        'classe'=>null,
        'natureza'=>null,
        'servico'=>null,
        'origem'=>null,
        'destino'=>null,

        // Data e hora local do aeroporto
        'dtMovimento'=>dateTimeUTC($_parametros['utcAeroporto'])->format('Y-m-d'),
        'hrMovimento'=>dateTimeUTC($_parametros['utcAeroporto'])->format('H:i'),
        'movimento'=>null,
        'recurso'=>null,
        'segundoRecurso'=>null,

        'txMatricula'=>null,
        'txMovimento'=>null,
        'txClasse'=>null,
        'txNatureza'=>null,
        'txServico'=>null,
        'txOrigem'=>null,
        'txDestino'=>null,
        'txRecurso'=>null,
        'txSegundoRecurso'=>null,
        'txSituacao'=>null,

        'resultado'=>null,
        'mensagem'=>null,
        'complemento'=>null);
    return $_retorno;
}

function limparStatusComplementos(){
    $_retorno = array(
        'idStatus'=>null,
        'status'=>null,
        'idComplemento'=>null,
        'regra'=>null,
        'embarque_pax'=>null,
        'embarque_carga'=>null,
        'embarque_correio'=>null,
        'desembarque_pax'=>null,
        'desembarque_carga'=>null,
        'desembarque_correio'=>null,
        'transito_pax'=>null,
        'observacao'=>null,
        'idComando'=>null,
        'codigoAnac'=>null,
        'nome'=>null,

        'txMatricula'=>null,
        'txRegra'=>null,
        'txComando'=>null,

        'resultado'=>null,
        'mensagem'=>null,
        'complemento'=>null);
    return $_retorno;
}

function excluirMovimentoStatus($_parametros){
    // var_dump('Parametros');
    // var_dump($_parametros);
    // Preparando chamada da API apiManterStatus
    $_token = gerarToken($_SESSION['plantaSistema']);
    $_post = ['token'=>$_token,'funcao'=>'ExcluirMovimentoStatus','parametros'=>$_parametros, 'status'=> array()];
    $_parametros = executaAPIs('apiManterStatus.php', $_post);
    return $_parametros;
}

function barraFuncoesStatus($_titulo, $_parametros, $_impressao) {
    echo '
    <div class="row justify-content-between py-2">
        <li class="col-4 px-4 painel-header"><h5>'.$_titulo.'</h5></li>
        <div class="col-4">
            <a href="?tipo=Status&funcao=Inclusão&movimento=Previsão&idStatus=&idMovimento=&idUltimo=" class="btn btn-outline-primary btn-sm">
                <img src="../ativos/img/previsto.png"/> Incluir previsão</a>
            </a>
            <a href="?tipo=Status&funcao=Inclusão&movimento=Pouso&idStatus=&idMovimento=&idUltimo=" class="btn btn-outline-primary btn-sm">
                <img src="../ativos/img/pouso.png"/> Incluir pouso</a>
            </a>
        </div>
        <div class ="col-4 d-md-flex justify-content-end">
            <button class="btn btn-outline-primary" type="button" title="Atualizar" id="buscarStatus">
                <img src="../ativos/img/atualizar.png"/></button>
    ';
    echo '
            <button class="btn btn-outline-primary" type="button" title="Pesquisar" id="iniciarPesquisaStatus"
                data-bs-toggle="modal" data-bs-target="#pesquisarStatus">
                <img src="../ativos/img/pesquisar.png"/></button>
    ';
    if ($_impressao) {
        echo '<button class="btn btn-outline-primary" type="button" title="Exportar PDF" id="exportarPDF">
                <img src="../ativos/img/exportarPDF.png"/></button>';
        echo '<button class="btn btn-outline-primary" type="button" title="Impressão" id="print" onclick="window.print()">
                <img src="../ativos/img/imprimir.png"/></button>';
    }
    if ($_parametros['objetivo'] == 'painel') {
        echo '<button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#painelStatus">
                <img src="../ativos/img/visualizar.png" title="Expandir painel"/></button>
        ';
    }
    echo '                
        </div>
    </div>
    ';
}

function pesquisarStatus($_parametros){
    switch ($_parametros['objetivo']) {
        case "status":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opSST_ordenacao',"sm.id desc, sm.dhMovimento desc, st.id desc");
        break;
        case "movimento":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opMST_ordenacao',"sm.id desc, sm.dhMovimento desc, st.id desc");
        break;
        case "painel":
            $_ordenacao = carregarCookie($_parametros['siglaAeroporto'].'_opPST_ordenacao',"sm.id desc, sm.dhMovimento desc, st.id desc");
        break;
    }
    echo '
    <!-- *************************************************** -->
    <!-- Modal PESQUISA STATUS -->
    <!-- *************************************************** -->
    <div class="modal fade" id="pesquisarStatus" tabindex="-1" aria-labelledby="pesquisarStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pesquisarStatusLabel">Pesquisar Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    ';
    // modal-body
    echo '
                <div class="modal-body">
                    <div class="row mt-2">
                        <label for="ptxStsStatusInicial">Intervalo de Status</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control cpoCookieStatus input-lg" id="ptxStsStatusInicial" placeholder="aaaa/mm/nnnnnn"/>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control cpoCookieStatus input-lg" id="ptxStsStatusFinal" placeholder="aaaa/mm/nnnnnn"/>
                        </div>
                    </div>
    ';
    if (($_parametros['objetivo']) == "status") {
        echo '                
                    <div class="row mt-2" >
                        <label for="pdtStsPeriodoInicio">Primeiro movimento</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control cpoCookieStatus input-lg" id="pdtStsPeriodoInicio" size="10"/>
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control cpoCookieStatus input-lg" id="pdtStsPeriodoFinal" size="10"/>
                        </div>
                    </div>
        ';
    }
    echo '
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="ptxStsMatricula">Matrícula</label>
                            <input type="text" class="form-control cpoCookieStatus caixaAlta input-lg" id="ptxStsMatricula"/>
                        </div>
                        <div class="col-md-6">
                            <label for="ptxStsOperador">Operador</label>
                            <input type="text" class="form-control cpoCookieStatus caixaAlta input-lg" id="ptxStsOperador"/>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="pslStsClasse">Classe</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsClasse">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pslStsNatureza">Natureza</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsNatureza">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="pslStsServico">Tipo de Serviço</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsServico">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="ptxStsOrigem">Origem</label>
                            <input type="text" class="form-control cpoCookieStatus caixaAlta input-lg" id="ptxStsOrigem"/>
                        </div>
                        <div class="col-md-6">
                            <label for="ptxStsDestino">Destino</label>
                            <input type="text" class="form-control cpoCookieStatus caixaAlta input-lg" id="ptxStsDestino"/>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="pslStsMovimento">Movimento</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsMovimento">
                            </select>
                        </div>
                    </div>
    ';
    if (($_parametros['objetivo']) == "status") {
        echo '
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="pslStsFaturado">Faturado</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsFaturado">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pslStsSituacao">Situação</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsSituacao">
                            </select>
                        </div>
                    </div>
        ';
    }
    echo '
                    <br>
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <label for="pslStsOrdenacao">Ordenação da lista</label>
                            <select class="form-select cpoCookieStatus selCookieStatus input-lg" id="pslStsOrdenacao">
                                <option '.($_ordenacao == "st.id desc, sm.id" ? "selected" : "").' value="st.id desc, sm.id">Status</option>
                                <option '.($_ordenacao == "mt.matricula, st.id desc, sm.id" ? "selected" : "").' value="mt.matricula, st.id desc, sm.id">Matrícula</option>
                                <option '.($_ordenacao == "sm.id desc, sm.dhMovimento desc, st.id desc" ? "selected" : "").' value="sm.id desc, sm.dhMovimento desc, st.id desc">Movimentação</option>
                            </select>
                        </div>
                    </div>
                </div>
    ';
    // modal-footer
    echo '
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" id="limparPesquisaStatus"><img src="../ativos/img/limpar.png" title="Limpar"/></button>
                    <button type="button" class="btn btn-outline-primary" id="aplicarPesquisaStatus" data-bs-dismiss="modal"><img src="../ativos/img/pesquisar.png" title="Pesquisar"/></button>
                </div>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}

function desconectarStatus($_parametros){
    $comando = null;
    $conexao = conexao();
    try {
        $conexao = conexao();
        $conexao->beginTransaction();
        $comando = "UPDATE gear_status SET idChegada = NULL, idPartida = NULL WHERE id = ".$_parametros['idStatus'];
        $sql = $conexao->prepare($comando);
        if ($sql->execute()){
            gravaDLog("gear_status", "Desconectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                        $_parametros['idStatus'], $comando);
        } else {
            throw new PDOException("Não foi possível desconectar os voos!");
        }
        $conexao->commit();
    } catch (PDOException $e) {
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
        $_parametros['complemento'] = $comando;
        $_parametros['funcao'] = null;
        if ($conexao->inTransaction()) {$conexao->rollBack();}
    }
    return $_parametros;
}

function salvarConectarStatus($_parametros) {
    $comando = null;
    $conexao = conexao();
    // Conecta voo de chegada ao status 
    if ($_parametros['movimento'] == "Chegada") {
        try {
            // Verifica se ID de chegada foi informado
            if (empty($_parametros['idChegada'])) {
                throw new PDOException("Voo de chegada não foi informado!");
            }
            $conexao->beginTransaction();
            // Conectando voo de chegada ao status
            $comando = "UPDATE gear_status SET idChegada = ".$_parametros['idChegada']." WHERE id = ".$_parametros['idStatus'];
            $sql = $conexao->prepare($comando);
            if ($sql->execute()){
                gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'],
                            $_parametros['idStatus'], $comando);

                // Verifica se voo de chegada está conectado a um voo de partida
                $comando = "SELECT idPartida FROM gear_voos_operacionais WHERE id = ".$_parametros['idChegada'];
                $sql = $conexao->prepare($comando);
                if ($sql->execute()){
                    $_registro = $sql->fetch(PDO::FETCH_ASSOC);
                    $_idPartida = $_registro['idPartida'];
                    if (!empty($_idPartida)) {
                        // Conecta o voo de partida ao status
                        $comando = "UPDATE gear_status SET idPartida = ".$_idPartida." WHERE id = ".$_parametros['idStatus'];
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()){
                            gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                        $_parametros['idStatus'], $comando);
                        } else {
                            throw new PDOException("Não foi possível conectar o voo de partida associado ao status!");
                        }
                    } else {
                        // Verifica se o status está conectado a um voo de partida
                        $comando = "SELECT idPartida FROM gear_status WHERE id = ".$_parametros['idStatus'];
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()){
                            $_registro = $sql->fetch(PDO::FETCH_ASSOC);
                            $_idPartida = $_registro['idPartida'];
                            if (!empty($_idPartida)) {
                                // Conecta o voo de partida a chegada
                                $comando = "UPDATE gear_voos_operacionais SET idPartida = ".$_idPartida." WHERE id = ".$_parametros['idChegada'];
                                $sql = $conexao->prepare($comando);
                                if ($sql->execute()){
                                    gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                                $_parametros['idChegada'], $comando);
                                } else {
                                    throw new PDOException("Não foi possível conectar o voo de partida associado ao status ao voo de chegada!");
                                }
                                // Conecta o voo de chegada a partida
                                $comando = "UPDATE gear_voos_operacionais SET idChegada = ".$_parametros['idChegada']." WHERE id = ".$_idPartida;
                                $sql = $conexao->prepare($comando);
                                if ($sql->execute()){
                                    gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                                $_idPartida, $comando);
                                } else {
                                    throw new PDOException("Não foi possível conectar o voo de chegada associado ao status, ao voo de partida!");
                                }
                            }
                        } else {
                            throw new PDOException("Não foi possível verificar o voo de partida associado ao status!");
                        }
                    }
                } else {
                    throw new PDOException("Não foi possível verificar o voo de partida associado ao voo de chegada!");
                }

                $_parametros['status'] = "success";
                $_parametros['mensagem'] = array("Voos conectados com sucesso!");
                $_parametros['idChegada'] = null;
                $_parametros['idPartida'] = null;
                $_parametros['idStatus'] = null;
                $_parametros['funcao'] = null;   
                if ($conexao->inTransaction()) {$conexao->commit();}
            } else {
                throw new PDOException("Não foi possível conectar o voo de chegada ao status!");
            }
        } catch (PDOException $e) {
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
            $_parametros['complemento'] = $comando;
            $_parametros['funcao'] = null;
            if ($conexao->inTransaction()) {$conexao->rollBack();}
        }
    // Conecta voo de partida ao status     
    } else {
        try {
            // Verifica se ID de partida foi informado
            if (empty($_parametros['idPartida'])) {
                throw new PDOException("Voo de partida não foi informado!");
            }

            $conexao->beginTransaction();
            // Conectando voo de partida ao status
            $comando = "UPDATE gear_status SET idPartida = ".$_parametros['idPartida']." WHERE id = ".$_parametros['idStatus'];
            $sql = $conexao->prepare($comando);
            if ($sql->execute()){
                gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'],
                            $_parametros['idStatus'], $comando);

                // Verifica se voo de partida está conectado a um voo de chegada
                $comando = "SELECT idChegada FROM gear_voos_operacionais WHERE id = ".$_parametros['idPartida'];
                $sql = $conexao->prepare($comando);
                if ($sql->execute()){
                    $_registro = $sql->fetch(PDO::FETCH_ASSOC);
                    $_idChegada = $_registro['idChegada'];
                    if (!empty($_idChegada)) {
                        // Conecta o voo de chegada ao status
                        $comando = "UPDATE gear_status SET idChegada = ".$_idChegada." WHERE id = ".$_parametros['idStatus'];
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()){
                            gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                        $_parametros['idStatus'], $comando);
                        } else {
                            throw new PDOException("Não foi possível conectar o voo de chegada associado ao status!");
                        }
                    } else {
                        // Verifica se o status está conectado a um voo de chegada
                        $comando = "SELECT idChegada FROM gear_status WHERE id = ".$_parametros['idStatus'];
                        $sql = $conexao->prepare($comando);
                        if ($sql->execute()){
                            $_registro = $sql->fetch(PDO::FETCH_ASSOC);
                            $_idChegada = $_registro['idChegada'];
                            if (!empty($_idChegada)) {
                                // Conecta o voo de chegada a partida
                                $comando = "UPDATE gear_voos_operacionais SET idChegada = ".$_idChegada." WHERE id = ".$_parametros['idPartida'];
                                $sql = $conexao->prepare($comando);
                                if ($sql->execute()){
                                    gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                                $_parametros['idPartida'], $comando);
                                } else {
                                    throw new PDOException("Não foi possível conectar o voo de chegada associado ao status, ao voo de partida!");
                                }
                                // Conecta o voo de partida a chegada
                                $comando = "UPDATE gear_voos_operacionais SET idPartida = ".$_parametros['idPartida']." WHERE id = ".$_idChegada;
                                $sql = $conexao->prepare($comando);
                                if ($sql->execute()){
                                    gravaDLog("gear_status", "Conectar", $_parametros['aeroporto'], $_parametros['usuario'], 
                                                $_idChegada, $comando);
                                } else {
                                    throw new PDOException("Não foi possível conectar o voo de partida associado ao status, ao voo de chegada!");
                                }
                            }
                        } else {
                            throw new PDOException("Não foi possível verificar o voo de chegada associado ao status!");
                        }
                    }
                } else {
                    throw new PDOException("Não foi possível verificar o voo de chegado associado ao voo de partida!");
                }

                $_parametros['status'] = "success";
                $_parametros['mensagem'] = array("Voos conectados com sucesso!");
                $_parametros['idChegada'] = null;
                $_parametros['idPartida'] = null;
                $_parametros['idStatus'] = null;
                $_parametros['funcao'] = null;   
                if ($conexao->inTransaction()) {$conexao->commit();}
            } else {
                throw new PDOException("Não foi possível conectar o voo de partida ao status!");
            }
        } catch (PDOException $e) {
            $_parametros['status'] = 'danger';
            $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
            $_parametros['complemento'] = $comando;
            $_parametros['funcao'] = null;
            if ($conexao->inTransaction()) {$conexao->rollBack();}
        }
    }
    return $_parametros;
}
?>

<script src="../cadastros/cdFuncoes.js"></script>
<script>
    async function buscarStatus() {
        var filtro = "";
        var descricaoFiltro = "";

        // Decidindo o filtro base de acordo com o objetivo do formulario
        switch ($('#hdObjetivo').val()) {
            case "status":
                filtro += " AND st.idAeroporto = "+$("#hdAeroporto").val();
                descricaoFiltro += ' <br>Aeroporto : '+$("#hdNomeAeroporto").val();
            break;
            case "movimento":
                filtro += " AND st.idAeroporto = "+$('#hdAeroporto').val()+
                        " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND sm.movimento <> 'CND'";
                descricaoFiltro += ' <br>Aeroporto : '+$("#hdNomeAeroporto").val();
            break;
            case "painel":
                filtro += " AND st.idAeroporto = "+$('#hdAeroporto').val()+
                        " AND st.faturado = 'NAO' AND st.situacao = 'ATV' AND sm.movimento <> 'CND'";
                descricaoFiltro += '';
            break;
        }

        $(".cpoCookieStatus").each(function(){
            if (!isEmpty($(this).val())) {
                switch ($(this).attr('id')) {
                    case "ptxStsStatusInicial":
                        filtro += " AND CONCAT(st.ano,'/',st.mes,'/',st.numero) >= '"+$("#ptxStsStatusInicial").val()+"'"+
                                    " AND CONCAT(st.ano,'/',st.mes,'/',st.numero) <= '"+$("#ptxStsStatusFinal").val()+"'";
                        descricaoFiltro += ' <br>Intervalo de Status : '+$("#ptxStsStatusInicial").val()+' a '+$("#ptxStsStatusFinal").val();
                    break;
                    case "pdtStsPeriodoInicio":
                        filtro += " AND (DATE_FORMAT(pm.dhMovimento,'%Y-%m-%d')  >= '"+mudarDataAMD($("#pdtStsPeriodoInicio").val())+"'"+
                                    " AND DATE_FORMAT(pm.dhMovimento,'%Y-%m-%d') <= '"+mudarDataAMD($("#pdtStsPeriodoFinal").val())+"')"
                        descricaoFiltro += ' <br>Primeiro Movimento : '+mudarDataDMA($("#pdtStsPeriodoInicio").val())+' a '+
                                                                        mudarDataDMA($("#pdtStsPeriodoFinal").val());
                    break;
                    case "ptxStsMatricula":
                        filtro += " AND mt.matricula LIKE '%"+$("#ptxStsMatricula").val()+"%'";
                        descricaoFiltro += " <br>Matrícula : "+$("#ptxStsMatricula").val();
                    break;
                    case "ptxStsOperador":
                        filtro += " AND op.operador LIKE '%"+$("#ptxStsOperador").val()+"%'";
                        descricaoFiltro += " <br>Operador : "+$("#ptxStsOperador").val();
                    break;
                    case "pslStsNatureza":
                        filtro += " AND st.natureza = '"+$("#pslStsNatureza").val()+"'";
                        descricaoFiltro += ' <br>Natureza : '+$("#pslStsNatureza :selected").text();
                    break;
                    case "pslStsClasse":
                        filtro += " AND st.classe = '"+$("#pslStsClasse").val()+"'";
                        descricaoFiltro += ' <br>Classe : '+$("#pslStsClasse :selected").text();
                    break;
                    case "pslStsServico":
                        filtro += " AND st.servico = '"+$("#pslStsServico").val()+"'";
                        descricaoFiltro += ' <br>Tipo de Serviço : '+$("#pslStsServico :selected").text();
                    break;
                    case "ptxStsOrigem":
                        filtro += " AND pr.icao LIKE '%"+$("#ptxStsOrigem").val()+"%'";
                        descricaoFiltro += " <br>Origem : "+$("#ptxStsOrigem").val();
                    break;
                    case "ptxStsDestino":
                        filtro += " AND de.icao LIKE '%"+$("#ptxStsDestino").val()+"%'";
                        descricaoFiltro += " <br>Destino : "+$("#ptxStsDestino").val();
                    break;
                    case "pslStsMovimento":
                        filtro += " AND sm.movimento = '"+$("#pslStsMovimento").val()+"'";
                        descricaoFiltro += ' <br>Movimento : '+$("#pslStsMovimento :selected").text();
                    break;
                    case "pslStsFaturado":
                        filtro += " AND st.faturado = '"+$("#pslStsFaturado").val()+"'";
                        descricaoFiltro += ' <br>Faturado : '+$("#pslStsFaturado :selected").text();
                    break;
                    case "pslStsSituacao":
                        filtro += " AND st.situacao = '"+$("#pslStsSituacao").val()+"'";
                        descricaoFiltro += " <br>Situação : "+$("#pslStsSituacao :selected").text();
                    break;
                    default:
                        filtro += "";
                        descricaoFiltro += "";
                }
            }
        });

        // Montagem da ordem
        var ordem = $("#pslStsOrdenacao").val();

        // Decidindo o carregamento das informações de acordo com o objetivo do formulario
        switch ($('#hdObjetivo').val()) {
            case "status":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opSST_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opSST_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opSST_descricao', descricaoFiltro);
                //await ajaxManterStatus($('#hdSiglaAeroporto').val(),"_opSST_");
                await opCarregarStatus('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            break;
            case "movimento":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMST_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMST_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opMST_descricao', descricaoFiltro);
                //await ajaxManterStatus($('#hdSiglaAeroporto').val(),"_opMST_");
                await opCarregarUltimosMovimentos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            break;
            case "painel":
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPST_ordenacao', ordem);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPST_filtro', filtro);
                await criarCookie($('#hdSiglaAeroporto').val()+'_opPST_descricao', descricaoFiltro);
                await ajaxMovimentosStatus($('#hdSiglaAeroporto').val());
            break;
        }
    };

    async function prepararModalStatus(){
        await suCarregarSelectTodas('MatriculasCategoria','#mslCategoria','','','Cadastrar');

        //alert($('#hdMovimento').val());

        if ($('#hdMovimento').val() != "") {
            switch($('#hdMovimento').val()) {
                //ok
                case "Status":
                    $('#divStsMovimento').css('display','none');
                break;
                
                //ok
                case "Previsão":
                    if ($('#hdFuncao').val() == "Inclusão") {
                        $('#divStsStatus').css('display','none');
                        $("#txStsMovimento").val('Previsão');
                        $("#idStsMovimento").val('PRV');
                    }
                    $('#divStsClassificacao').css('display','none');
                    $("#txStsMovimento").attr('readonly', true);
                    $('#divStsRecurso').css('display', 'none');
                    $('#divStsSegundoRecurso').css('display', 'none');
                break;

                //ok
                case "Pouso":
                    if ($('#hdFuncao').val() == "Inclusão") {
                        $('#divStsStatus').css('display','none');
                        $("#txStsMovimento").val('Pouso');
                        $("#idStsMovimento").val('POU');
                    } else {
                        $("#txStsMatricula").attr('readonly', true);
                        $("#txStsOrigem").attr('readonly', true);
                        $("#txStsDestino").attr('readonly', true);
                    }
                    $('#divStsClassificacao').css('display','none');
                    $("#txStsMovimento").attr('readonly', true);
                    filtrarStsRecursos($('#hdFuncao').val());
                break;

                case "Entrada":
                    $("#txStsMatricula").attr('readonly', true);
                    $("#txStsOrigem").attr('readonly', true);
                    $("#txStsDestino").attr('readonly', true);
                    $('#divStsClassificacao').css('display','none');
                    $("#txStsMovimento").attr('readonly', true);
                    filtrarStsRecursos($('#hdFuncao').val());
                break;

                case "Saída":
                    $("#txStsMovimento").attr('readonly', true);
                    $("#txStsMatricula").attr('readonly', true);
                    $("#txStsOrigem").attr('readonly', true);
                    $("#txStsDestino").attr('readonly', true);
                    $('#divStsClassificacao').css('display','none');
                    filtrarStsRecursos($('#hdFuncao').val());
                    $("#txStsRecurso").attr('readonly', true);
                break;

                case "Decolagem":
                    $("#txStsMatricula").attr('readonly', true);
                    $("#txStsOrigem").attr('readonly', true);
                    $("#txStsDestino").attr('readonly', true);
                    $('#divStsClassificacao').css('display','none');
                    filtrarStsRecursos($('#hdFuncao').val());
                break;

                //ok
                case "Movimento":
                    $("#txStsMatricula").attr('readonly', true);
                    $("#txStsOrigem").attr('readonly', true);
                    $("#txStsDestino").attr('readonly', true);
                    $('#divStsClassificacao').css('display','none');
                    $("#txStsMovimento").val('');
                    $("#idStsMovimento").val('');
                break;
            }
        }
    }

    async function prepararModalStatusComplementos(){
        $("#txEmbarquePax").mask('9999', {'translation': {9: {pattern: /[0-9]/} } });         
        $("#txEmbarqueCarga").mask('9999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txEmbarqueCorreio").mask('9999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txDesembarquePax").mask('9999', {'translation': {9: {pattern: /[0-9]/} } });         
        $("#txDesembarqueCarga").mask('9999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txDesembarqueCorreio").mask('9999', {'translation': {9: {pattern: /[0-9]/} } }); 
        $("#txTransitoPax").mask('9999', {'translation': {9: {pattern: /[0-9]/} } });         
    }

    async function prepararConectarStatus(idAeroporto, idStatus, movimento) {
        var filtro = "";
        if (movimento == "Chegada") {
            filtro = " AND vo.idAeroporto = "+idAeroporto+" AND vo.operacao = 'CHG' "+
                        " AND NOT EXISTS(SELECT st.idChegada FROM gear_status st WHERE st.idChegada = vo.id)";
        } else {
            filtro = " AND vo.idAeroporto = "+idAeroporto+" AND vo.operacao = 'PRT' "+
                        " AND NOT EXISTS(SELECT st.idPartida FROM gear_status st WHERE st.idPartida = vo.id)";
        }
        filtro += " AND vo.dhConfirmada >= ("+
                    " SELECT CASE "+
                    "   WHEN (SELECT COUNT(id) FROM gear_status_movimentos mo WHERE mo.idStatus = "+idStatus+" and mo.movimento = 'POU') > 0"+
                    "   THEN (SELECT mo.dhMovimento FROM gear_status_movimentos mo WHERE mo.idStatus = "+idStatus+" and mo.movimento = 'POU')"+
                    "   ELSE vo.dhConfirmada END)";
        filtro += " AND vo.dhConfirmada <= ("+
                    " SELECT CASE "+
                    "   WHEN (SELECT COUNT(id) FROM gear_status_movimentos mo WHERE mo.idStatus = "+idStatus+" and mo.movimento = 'DEC') > 0"+
                    "   THEN (SELECT mo.dhMovimento FROM gear_status_movimentos mo WHERE mo.idStatus = "+idStatus+" and mo.movimento = 'DEC')"+
                    "   ELSE vo.dhConfirmada END)";
        await suCarregarSelectTodos('ConectarVoos','#pslConectarVoos', '', filtro, 'Cadastrar');
    }

    function filtrarStsRecursos(funcao = 'Inclusão') {
        var divRecurso = document.getElementById("divStsRecurso");
        var divSegundoRecurso = document.getElementById("divStsSegundoRecurso");
        var htmlRecurso = "";
        var htmlSegundoRecurso = "";

        // Se inclusão limpar o recurso para não influenciar na seleção 
        if (funcao === 'Inclusão' && ($("#idStsMovimento").val() === 'ENT' || $("#idStsMovimento").val() === 'DEC')) {
            $("#idStsRecurso").val('');
            $("#dsStsRecurso").val('');
        }

        // // Habilita origem se movimento for pouso
        // if ($("#idStsMovimento").val() == 'POU') { $("#txStsOrigem").attr('readonly', false); }

        // // Habilita destino se movimento for decolagem
        // if ($("#idStsMovimento").val() == 'DEC') { $("#txStsDestino").attr('readonly', false); }

        // Habilita divStsRecurso para recurso de Pista
        if ($("#idStsMovimento").val() === 'POU' || $("#idStsMovimento").val() === 'DEC') {
            htmlRecurso =  '<label for="idStsRecurso">Pista</label>'+
                    '<input type="text" class="form-select caixaAlta cpoLimparStatus cpoObrigatorio input-lg" id="txStsRecurso" placeholder="Selecionar" name="txRecurso"'+
                    '   value="'+$("#dsStsRecurso").val()+'"'+
                    '   onfocus="iniciarPesquisa(\'StsRecurso\',this.value,\'PIS\')"'+
                    '   oninput="executarPesquisa(\'StsRecurso\',this.value,\'PIS\')"'+
                    '   onblur="finalizarPesquisa(\'StsRecurso\')" autocomplete="off">';
        }

        // Habilita divStsRecurso para recurso de Posição
        if ($("#idStsMovimento").val() === 'ENT') {
            htmlRecurso =  '<label for="idStsRecurso">Posição</label>'+
                    '<input type="text" class="form-select caixaAlta cpoLimparStatus cpoObrigatorio input-lg" id="txStsRecurso" placeholder="Selecionar" name="txRecurso"'+
                    '   value="'+$("#dsStsRecurso").val()+'"'+
                    '   onfocus="iniciarPesquisa(\'StsRecurso\',this.value,\'POS\')"'+
                    '   oninput="executarPesquisa(\'StsRecurso\',this.value,\'POS\')"'+
                    '   onblur="finalizarPesquisa(\'StsRecurso\')" autocomplete="off">';
        }

        // Desabilita divStsRecurso para recurso de Posição (considera a posição do movimento anterior de Entrada)
        if ($("#idStsMovimento").val() === 'SAI') {
            htmlRecurso =  '<label for="idStsRecurso">Posição</label>'+
                    '<input type="text" class="form-select caixaAlta cpoLimparStatus input-lg" id="txStsRecurso" placeholder="Selecionar" name="txRecurso"'+
                    '   value="'+$("#dsStsRecurso").val()+'" readonly>';
        }

        // Habilita 2o. recurso de Posição se movimento for de Pouso
        if ($("#idStsMovimento").val() === 'POU') {
            htmlSegundoRecurso =  '<label for="idStsSegundoRecurso">Posição</label>'+
                    '<input type="text" class="form-select caixaAlta cpoLimparStatus input-lg" id="txStsSegundoRecurso" placeholder="Selecionar" name="txSegundoRecurso"'+
                    '   value="'+$("#dsStsSegundoRecurso").val()+'"'+
                    '   onfocus="iniciarPesquisa(\'StsSegundoRecurso\',this.value,\'POS\')"'+
                    '   oninput="executarPesquisa(\'StsSegundoRecurso\',this.value,\'POS\')"'+
                    '   onblur="finalizarPesquisa(\'StsSegundoRecurso\')" autocomplete="off">';
        }

        // Habilita 2o. recurso de Pista se movimento for de Saida
        if ($("#idStsMovimento").val() === 'SAI') {
            htmlSegundoRecurso =  '<label for="idStsSegundoRecurso">Pista</label>'+
                    '<input type="text" class="form-select caixaAlta cpoLimparStatus input-lg" id="txStsSegundoRecurso" placeholder="Selecionar" name="txSegundoRecurso"'+
                    '   value="'+$("#dsStsSegundoRecurso").val()+'"'+
                    '   onfocus="iniciarPesquisa(\'StsSegundoRecurso\',this.value,\'PIS\')"'+
                    '   oninput="executarPesquisa(\'StsSegundoRecurso\',this.value,\'PIS\')"'+
                    '   onblur="finalizarPesquisa(\'StsSegundoRecurso\')" autocomplete="off">';
        }
        divRecurso.innerHTML = htmlRecurso;
        divSegundoRecurso.innerHTML = htmlSegundoRecurso;
    }
</script>