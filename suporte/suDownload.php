<?php 
    // header("Content-Type: text/html; charset=UTF-8",true);
    // require_once("../suporte/suFuncoes.php");

    //Recebendo campos
    $arquivo = $_GET["arquivo"];
    $excluir = $_GET["excluir"];


    // $fileName = $_GET['file']; // pega o endereço do arquivo
    //                        // ou o nome dele se o arquivo 
    //                        // estiver na mesma pagina
    // $file= $fileName; 
    
    // header("Content-Type: application/save");
    // header("Content-Length:".filesize($file)); 
    // header('Content-Disposition: attachment; filename="' . $fileName . '"'); 
    // header("Content-Transfer-Encoding: binary");
    // header('Expires: 0'); 
    // header('Pragma: no-cache'); 
    
    // // nesse momento ele le o arquivo e envia
    // $fp = fopen("$file", "r"); 
    // fpassthru($fp); 
    // fclose($fp); 

    // $file= "arquivos/".$fileName; 
    
    // header("Content-Type: application/save");
    // header("Content-Length:".filesize($file)); 
    // header('Content-Disposition: attachment; filename="' . $fileName . '"'); 
    // header("Content-Transfer-Encoding: binary");
    // header('Expires: 0'); 
    // header('Pragma: no-cache'); 
    
    // // nesse momento ele le o arquivo e envia
    // $fp = fopen("$file", "r"); 
    // fpassthru($fp); 
    // fclose($fp); 


// // le o tamanho do arquivo em bytes
// $tamanho = filesize($arquivo);

// // pega extensão do arquivo
// $ext = explode (".",$arquivo);

// // aqui bloqueia downloads indevido
// if ($ext[1]=="php") {
//     echo "Arquivo não autorizado para download!";
// }
 
// // envia todos cabecalhos HTTP para o browser (tipo, tamanho, etc..)
// header("Content-Type: application/save"); 
// header("Content-Length: $tamanho");
// header("Content-Disposition: attachment; filename=$nome.$ext[1]"); 
// header("Content-Transfer-Encoding: binary");

// // nesse momento ele le o arquivo e envia
// $fp = fopen($arquivo, "r"); 
// fpassthru($fp); 
// fclose($fp);
// exit; 

    // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
    if(isset($arquivo) && file_exists($arquivo)){
        // verifica a extensão do arquivo para pegar o tipo
        switch(strtolower(substr(strrchr(basename($arquivo),"."),1))){
            case "pdf": $tipo="application/pdf"; break;
            case "exe": $tipo="application/octet-stream"; break;
            case "zip": $tipo="application/zip"; break;
            case "doc": $tipo="application/msword"; break;
            case "xls": $tipo="application/vnd.ms-excel"; break;
            case "ppt": $tipo="application/vnd.ms-powerpoint"; break;
            case "gif": $tipo="image/gif"; break;
            case "png": $tipo="image/png"; break;
            case "jpg": $tipo="image/jpg"; break;
            case "mp3": $tipo="audio/mpeg"; break;
            case "csv": $tipo="text/csv; charset=utf-8"; break;
            case "txt": $tipo="text/html"; break;
            case "php": // deixar vazio por seurança
            case "htm": // deixar vazio por seurança
            case "html": // deixar vazio por seurança
        }

        // informa o tipo do arquivo ao navegador
        header("Content-Type: ".$tipo);
        // informa o tamanho do arquivo ao navegador
        header("Content-Length: ".filesize($arquivo));
        // informa ao navegador que é tipo anexo e faz abrir a janela de download,
        // tambem informa o nome do arquivo
        header("Content-Disposition: attachment; filename=".basename($arquivo));
        // lê o arquivo
        readfile($arquivo); 
        // verifica se tem que excluir o arquivo
        if (isset($excluir) && ($excluir=='sim')) {
            unlink($arquivo);
        }
        // aborta pós-ações
        // exit; 
        // header("Location: ../importacoes/imMatriculasAnac.php");
        // $anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
        // header("location: ".$anterior);
        exit; 
    }   
?> 