<?php 
function mdComando($mdSobreposto = '') {
    echo '
        <!-- *************************************************** -->
        <!-- Modal Cadastro Rápido - COMANDO -->
        <!-- *************************************************** -->
        <div class="modal fade" id="mdComando" tabindex="2" aria-labelledby="mdComandoLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mdComandoLabel">Cadastrar Comandantes</h1>
    ';
    if ($mdSobreposto == '') {
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '<input type="hidden" id="mdComandoSobreposto" value=""/>';
    } else {
        echo '<button class="btn btn-close" data-bs-target="#'.$mdSobreposto.'" data-bs-toggle="modal" data-bs-dismiss="modal"></button>';
        echo '<input type="hidden" id="mdComandoSobreposto" value="'.$mdSobreposto.'"/>';
    }
    echo '          </div>
                    <div class="modal-body">
                        <span id="mdComandoMensagem"></span>
                        <form class="row g-3" id="mdComandoFormulario">
                            <div class="col-12">
                                <label for="mtxCodigoAnac">Código ANAC</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="mtxCodigoAnac" name="mtxCodigoAnac"  maxlength="6"/>
                                <label for="mtxNome">Nome</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="mtxNome" name="mtxNome" maxlength="150"/>
                                <label for="mtxTelefone">Telefone</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="mtxTelefone" name="mtxTelefone"/>
                                <label for="mtxEmail">Email</label>
                                <input type="email" class="form-control cpoLimpar input-lg" id="mtxEmail" name="mtxEmail" maxlength="50"/>
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-padrao" id="mdComandoBotao" value="Cadastrar">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- *************************************************** -->
    ';
}
?>