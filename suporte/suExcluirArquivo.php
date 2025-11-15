<?php 
    $arquivo = $_GET["arquivo"];
    // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
    if(isset($arquivo) && file_exists($arquivo)){
        // exclui arquivo
        unlink($arquivo); 
    }   
    $fallback = 'index.php';
    $anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $fallback;
    header("location: {$anterior}");
    exit; 
?> 