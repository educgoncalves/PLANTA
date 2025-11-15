<?php 
//
// Funcoes para telas modais
//
function botaoFoto(){
    echo '<button id="botaoFoto" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" data-bs-target="#alterarFoto">Alterar foto</button>';
}

function telaFoto($_foto,$_tipo,$_credencial){
    $_fotoCompleta = '../arquivos/credenciamentos/'.$_foto.'.jpg';
    echo '<div id="alterarFoto" class="modal fade" tabindex="-1" aria-labelledby="alterarFotoLabel" aria-hidden="true">';
    echo '  <div class="modal-dialog modal-lg">';
    echo '      <div class="modal-content">';
    echo '          <div class="modal-header">';
    echo '              <h4 class="modal-title">Credencial '.$_credencial.' Foto '.$_foto.' - </h4>';
    echo '    		    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    echo '          </div>';
    echo '  	    <div class="modal-body">';
    echo '              <object data="'.$_fotoCompleta.'?nocache='.time().'" type="image/jpg" style="height:25%; width:25%">';
    echo '                  <img src="../arquivos/credenciamentos/default.png?nocache='.time().'" style="height:25%; width:25%"/>';
    echo '              </object>';
    echo '          </div>';
    echo '          <form action="?evento=uploadFoto" method="POST" enctype="multipart/form-data">';     
    echo '              <div class="modal-body">';
    echo '                  <input type="file" class="input-file-block" name="fotoNova">';
    echo '                  <input type="hidden" name="fotoAtual" value="'.$_foto.'"/>';
    echo '              </div>';
    echo '              <div class="modal-footer">';
    echo '                  <button type="submit" class="btn btn-default">Upload da foto</button>';
    if (file_exists($_fotoCompleta)) {
    //    echo '                  <a href="../suporte/suDownload.php?arquivo='.$_fotoCompleta.'" class="btn btn-default">Download da foto</a>';
        echo '                  <a href="../suporte/suExcluirArquivo.php?arquivo='.$_fotoCompleta.'" class="btn btn-default">Excluir foto</a>';
    }
    echo '                  <button type="button" class="btn btn-default" data-bs-dismiss="modal">Fechar</button>';
    echo '              </div>';
    echo '          </form>';
    echo '      </div>';
    echo '  </div>';
    echo '</div>';
}
?>