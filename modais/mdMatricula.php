<?php 
function mdMatricula($mdSobreposto = '') {
    echo '
        <!-- *************************************************** -->
        <!-- Modal Cadastro Rápido - MATRICULA -->
        <!-- *************************************************** -->
        <div class="modal fade" id="mdMatricula" tabindex="2" aria-labelledby="mdMatriculaLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="mdMatriculaLabel">Cadastrar Matrícula</h1>
    ';
    if ($mdSobreposto == '') {
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '<input type="hidden" id="mdMatriculaSobreposto" value=""/>';
    } else {
        echo '<button class="btn btn-close" data-bs-target="#'.$mdSobreposto.'" data-bs-toggle="modal" data-bs-dismiss="modal"></button>';
        echo '<input type="hidden" id="mdMatriculaSobreposto" value="'.$mdSobreposto.'"/>';
    }
    echo '          </div>
                    <div class="modal-body">
                        <span id="mdMatriculaMensagem"></span>
                        <form class="row g-3" id="mdMatriculaFormulario">
                            <div class="col-12">
                                <label for="mtxMatricula">Matrícula</label>
                                <input type="text" class="form-control cpoObrigatorio caixaAlta input-lg" id="mtxMatricula" name="mtxMatricula"/>
                                <label for="mslCategoria">Categoria</label>
                                <select class="form-select cpoObrigatorio input-lg" id="mslCategoria" name="mslCategoria">
                                </select> 
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-padrao" id="mdMatriculaBotao" value="Cadastrar">
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