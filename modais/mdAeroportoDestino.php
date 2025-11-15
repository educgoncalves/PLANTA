<?php 
function mdAeroportoDestino($mdSobreposto = '') {
    echo '
        <!-- *************************************************** -->
        <!-- Modal Cadastro Rápido - AEROPORTO DESTINO -->
        <!-- *************************************************** -->
        <div class="modal fade" id="mdDestino" tabindex="2" aria-labelledby="mdDestinoLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mdDestinoLabel">Cadastrar Comando</h1>
    ';
    if ($mdSobreposto == '') {
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '<input type="hidden" id="mdDestinoSobreposto" value=""/>';
    } else {
        echo '<button class="btn btn-close" data-bs-target="#'.$mdSobreposto.'" data-bs-toggle="modal" data-bs-dismiss="modal"></button>';
        echo '<input type="hidden" id="mdDestinoSobreposto" value="'.$mdSobreposto.'"/>';
    }
    echo '          </div>
                    <div class="modal-body">
                        <span id="mdDestinoMensagem"></span>
                        <form class="row g-3" id="mdDestinoFormulario">
                            <div class="col-12">
                                <label for="dtxIcao">ICAO</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="dtxIcao" name="dtxIcao" maxlength="4"/>
                                <label for="dtxIata">IATA</label>
                                <input type="text" class="form-control caixaAlta input-lg" id="dtxIata" name="dtxIata" maxlength="3"/>
                                <label for="dtxNome">Nome</label>
                                <input type="text" class="form-control input-lg" id="dtxNome" name="dtxNome" maxlength="250"/>
                                <label for="dtxLocalidade">Localidade</label>
                                <input type="text" class="form-control input-lg" id="dtxLocalidade" name="dtxLocalidade" maxlength="250"/>
                                <label for="dtxPais">País</label>
                                <input type="text" class="form-control input-lg" id="dtxPais" name="dtxPais" maxlength="250"/>
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-padrao" id="mdDestinoBotao" value="Cadastrar">
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