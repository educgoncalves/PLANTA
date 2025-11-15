<?php 
// Monta tela modal de visualização de informações complementares (vários)
//
function modalVisualizar() {
    //     <style="background-color:#cff4fc; color:#ff0000;" id="labelVisualizar">
    echo '
        <!-- *************************************************** -->
        <!-- Modal VISUALIZAR INFORMAÇÕES COMPLEMENTARES -->
        <!-- *************************************************** -->
        <button id="botaoVisualizar" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal"
            data-bs-target="#visualizar"></button>

        <div class="modal fade" id="visualizar" tabindex="-1" aria-labelledby="labelVisualizar" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="labelVisualizar"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="divVisualizar"></div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!-- *************************************************** -->
    ';
} 

// Monta tela modal de conexão de status, voos de chegada ou partida (opManterStatus.php e opMovimentos.php)
//
function modalConectar($_parametros) {
    echo '
    <!-- *************************************************** -->
    <!-- Modal STATUS CHEGADA ou PARTIDA-->
    <!-- *************************************************** -->
    <button id="botaoConectar" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" 
        data-bs-target="#conectar"></button>

    <div class="modal fade" id="conectar" tabindex="-1" aria-labelledby="conectarLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="conectarLabel">'.$_parametros['funcao'].' Voo de '.$_parametros['movimento'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <form action="?tipo='.$_parametros['tipo'].'&objetivo='.$_parametros['objetivo'].
                    '&evento=conectar&movimento='.$_parametros['movimento'].'" method="POST" class="form-group" 
                    id="editarForm">
    ';
    echo '
                    <input type="hidden" id="hdIdStatus" name="idStatus" value="'.$_parametros['idStatus'].'"/>
                    <input type="hidden" id="hdIdChegada" name="idChegada" value="'.$_parametros['idChegada'].'"/>
                    <input type="hidden" id="hdIdPartida" name="idPartida" value="'.$_parametros['idPartida'].'"/>
    ';
    echo '                    
                    <div class="modal-body">
                        <div class="row mt-2">
                            <div class="col-md-8">
                                <select class="form-select cpoConectar selConectar input-lg" id="pslConectarVoos" name="conectarVoos">
                                </select>
                            </div>
                        </div> 
                    </div>   
                    <div class="modal-footer d-flex justify-content-start">
                        <input type="button" class="btn btn-padrao" id="limparConectar" value="Limpar"/>
                        <input type="submit" class="btn btn-padrao" id="salvarConectar" value="Salvar">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}

// Monta tela de processamento da propaganda (adCadastrarPropagandas.php)
//
function modalPropaganda($_parametros) {
    echo '
    <!-- *************************************************** -->
    <!-- Modal PROPAGANDA -->
    <!-- *************************************************** -->
    <button id="botaoPropaganda" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" 
        data-bs-target="#propaganda">Propaganda</button>

    <div class="modal fade" id="propaganda" tabindex="-1" aria-labelledby="propagandaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="propagandaLabel">Propaganda '.$_parametros['propaganda'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <object data="'.$_parametros['arquivo'].'?nocache='.time().'" type="image/jpg" style="height:50%; width:50%">
                        <img src="../arquivos/propagandas/default.png?nocache='.time().'" style="height:50%; width:50%"/>
                    </object>
                </div>
                <form action="?evento=uploadPropaganda" method="POST" enctype="multipart/form-data">   
                    <div class="modal-body">
                        <input type="file" class="input-file-block" name="propagandaNova">
                        <input type="hidden" name="propagandaAtual" value="'.$_parametros['propaganda'].'"/>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-padrao">Upload</button>
    ';
    if (file_exists($_parametros['arquivo'])) {
    //    echo ' <a href="../suporte/suDownload.php?arquivo='.$_propagandaCompleta.'" class="btn btn-default">Download da Propaganda</a>';
        echo ' <a href="../suporte/suExcluirArquivo.php?arquivo='.$_parametros['arquivo'].'" class="btn btn-padrao">Excluir</a>';
    }
    echo '                  
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}

// Monta tela de processamento do mapa (vsCadastrarPlanos.php)
//
function modalMapa($_parametros) {
    echo '
    <!-- *************************************************** -->
    <!-- Modal MAPA -->
    <!-- *************************************************** -->
    <button id="botaoMapa" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" 
        data-bs-target="#mapa">Mapa</button>

    <div class="modal fade" id="mapa" tabindex="-1" aria-labelledby="mapaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="mapaLabel">Mapa '.$_parametros['mapa'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <object data="'.$_parametros['arquivo'].'?nocache='.time().'" type="image/jpg" style="height:100%; width:100%">
                        <img src="../arquivos/mapas/default.png?nocache='.time().'" style="height:100%; width:100%"/>
                    </object>
                </div>
                <form action="?evento=uploadMapa" method="POST" enctype="multipart/form-data">   
                    <div class="modal-body">
                        <input type="file" class="input-file-block" name="mapaNovo">
                        <input type="hidden" name="mapaAtual" value="'.$_parametros['mapa'].'"/>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-padrao">Upload</button>
    ';
    if (file_exists($_parametros['arquivo'])) {
    //    echo ' <a href="../suporte/suDownload.php?arquivo='.$_mapaCompleta.'" class="btn btn-default">Download da Mapa</a>';
        echo ' <a href="../suporte/suExcluirArquivo.php?arquivo='.$_parametros['arquivo'].'" class="btn btn-padrao">Excluir</a>';
    }
    echo '                  
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}

// Monta tela para copia de informações para outro aeroporto (adCadastrarMovimentos.php e adCadastrarPropagandas.php)
//
function modalCopiarAeroporto($parametros) {
    echo '
    <!-- *************************************************** -->
    <!-- Modal COPIAR PARA AEROPORTO -->
    <!-- *************************************************** -->
    <button id="botaoCopiarAeroporto" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" 
        data-bs-target="#copiarAeroporto">CopiarAeroporto</button>

    <div class="modal fade" id="copiarAeroporto" tabindex="-1" aria-labelledby="copiarAeroportoLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header alert alert-padrao">
                    <h5 class="modal-title fw-bold" id="copiarAeroportoLabel">'.$parametros['titulo'].'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <form action="?evento=executarCopiarAeroporto" method="POST" class="form-group">
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <select class="form-select input-lg" id="pslAeroportoDestino" name="aeroportoDestino">
                                </select>
                            </div>
                        </div> 
                        <p class="text-justify fw-bold h6">Atenção</p>
                        <p class="text-justify h6">'.$parametros['aviso'].'</p>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <input type="submit" class="btn btn-padrao" value="Copiar">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- *************************************************** -->
    ';
}
?>