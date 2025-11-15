<?php 
function mdFormulario() {
    echo '
        <!-- *************************************************** -->
        <!-- Modal Cadastro Rápido - FORMULARIO -->
        <!-- *************************************************** -->
        <div class="modal fade" id="mdFormulario" tabindex="-1" aria-labelledby="mdFormularioLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mdFormularioLabel">Cadastrar Formulário</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span id="mdFormularioMensagem"></span>
                        <form class="row g-3" id="mdFormularioFormulario">
                            <div class="col-12">
                                <label for="mtxSistema">Sistema</label>
                                <input type="text" class="form-control cpoLimpar caixaAlta input-lg" id="mtxSistema" name="mtxSistema"/>
                                <label for="mtxFormulario">Formulário</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="mtxFormulario" name="mtxFormulario"/>
                                <label for="mtxModulo">Módulo</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="mtxModulo" name="mtxModulo" maxlength="50"/>
                                <label for="mtxDescricao">Descrição</label>
                                <input type="text" class="form-control cpoLimpar input-lg" id="mtxDescricao" name="mtxDescricao" maxlength="50"/>
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-padrao" id="mdFormularioBotao" value="Cadastrar">
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