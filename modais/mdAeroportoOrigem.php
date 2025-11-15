<?php 
function mdAeroportoOrigem($mdSobreposto = '') {
    echo '
        <!-- *************************************************** -->
        <!-- Modal Cadastro Rápido - AEROPORTO ORIGEM -->
        <!-- *************************************************** -->
        <div class="modal fade" id="mdOrigem" tabindex="2" aria-labelledby="mdOrigemLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mdOrigemLabel">Cadastrar Aeroporto</h1>
    ';
    if ($mdSobreposto == '') {
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '<input type="hidden" id="mdOrigemSobreposto" value=""/>';
    } else {
        echo '<button class="btn btn-close" data-bs-target="#'.$mdSobreposto.'" data-bs-toggle="modal" data-bs-dismiss="modal"></button>';
        echo '<input type="hidden" id="mdOrigemSobreposto" value="'.$mdSobreposto.'"/>';
    }
    echo '          </div>
                    <div class="modal-body">
                        <span id="mdOrigemMensagem"></span>
                        <form class="row g-3" id="mdOrigemFormulario">
                            <div class="col-12">
                                <label for="mtxIcao">ICAO</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="mtxIcao" name="mtxIcao" maxlength="4"/>
                                <label for="mtxIata">IATA</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="mtxIata" name="mtxIata" maxlength="3"/>
                                <label for="mtxNome">Nome</label>
                                <input type="text" class="form-control input-lg" id="mtxNome" name="mtxNome" maxlength="250"/>
                                <label for="mtxLocalidade">Localidade</label>
                                <input type="text" class="form-control input-lg" id="mtxLocalidade" name="mtxLocalidade" maxlength="250"/>
                                <label for="mtxPais">País</label>
                                <input type="text" class="form-control input-lg" id="mtxPais" name="mtxPais" maxlength="250"/>
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-padrao" id="mdOrigemBotao" value="Cadastrar">
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