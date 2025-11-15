<?php 
    require_once("../suporte/suFuncoes.php");
    require_once("../suporte/suGerarPDF.php");
    
    // Recebendo campos
    $_arquivo = carregarPosts("arquivo","Relatorio");
    $_titulo = carregarPosts("titulo","");
    $_relatorio = carregarPosts("relatorio","", false);
    $_download = carregarPosts("download","true");
    $_orientacao = carregarPosts("orientacao","P");

    //gravaXTrace('Arquivo - '.$_arquivo);
    //gravaXTrace('Título - '.$_titulo);
    //gravaXTrace('Relatório - '.$_relatorio);
    //gravaXTrace('Download - '.$_download);
    //gravaXTrace('Orientação - '.$_orientacao);

    // Imprimindo as informações
    // Data e hora local do aeroporto
    $date = dateTimeUTC($_SESSION['plantaUTCAeroporto']);
    $_aux = trim(strstr($_titulo, '-', true));
    $_arquivo .= "_".(!empty($_aux) ? $_aux : $_titulo)."_".$date->format('Ymd_His');
    $html = htmlHeader($_titulo,$_arquivo,$date->format('d/m/Y H:i'));
    $html .=  $_relatorio;
    $html .= htmlFooter($date->format('d/m/Y H:i'));
    //gravaXTrace('PDF - '.$html);
    gerarDOMPDF($_arquivo, $html, (bool) $_download, $_orientacao);

    // aborta pós-ações
    exit; 
?> 