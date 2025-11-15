<?php 
// definindo os namespaces
//require_once("../ativos/dompdf/lib/html5lib/Parser.php");
//require_once("../ativos/dompdf/lib/php-font-lib-master/src/FontLib/Autoloader.php");
//require_once("../ativos/dompdf/lib/php-svg-lib-master/src/autoload.php");
//require_once("../ativos/dompdf/src/Autoloader.php");
require_once("../ativos/dompdf/vendor/autoload.php");
//Dompdf\Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\Options;

function gerarDOMPDF($_arquivo, $_html, $_download = true, $_tipo = "P") {
    $options = new Options();
    $options->setChroot(__DIR__);
    $options->setIsRemoteEnabled(true);

    $dompdf = new Dompdf($options);
    $dompdf->setPaper("A4", ($_tipo == "L" ? "landscape" : "portrait")); 
    $dompdf->loadHtml($_html); 
    $dompdf->render();

    //Verifica se baixa o arquivo no cliente ou imprime na tela
    if ($_download) {
       $dompdf->stream($_arquivo.".pdf", array("Attachment" => $_download));
    } else {
        header('Content-type: application/pdf');
        echo $dompdf->output();
    }

    //Para salvar no servidor
    //$_arquivo = $_SERVER['DOCUMENT_ROOT']."/crrg/relatorios/".$_arquivo.".pdf";
    //file_put_contents($_arquivo,$dompdf->output());
}

function htmlHeader($_titulo, $_arquivo = 'Relatório', $_date){
        return "<html lang='pt-BR'>
                <head>
                <meta charset='utf-8'>
                <title>".$_arquivo."</title>
                <style ='text/css'>
                    body {margin: 0; padding: 0; border: none; font-size: 12px;}
                    .topo {width: 100%; height: auto; overflow: hidden; padding: 5px 0;
                            text-align: center; color: #000; font-size: 14px;}
                    .rodape {width: 100%; height: auto; overflow: hidden; padding: 0px 0;
                            text-align: center; color: #000; font-size: 14px;}
                    .table-topo {width: 100%; border-collapse: collapse;}   
                    hr { border: 1px solid #ddd; margin: 10px 0px; }          
                    
                    .pagenum:before {content: counter(page);}
                    footer .pagenum:before {content: counter(page);}

                    table {
                        background-color: transparent; border-collapse : collapse;
                      }
                      caption {
                        padding-top: 8px;
                        padding-bottom: 8px;
                        color: #777;
                        text-align: left;
                      }
                      th {
                        text-align: left;
                      }
                      .table {
                        width: 100%;
                        max-width: 100%;
                        margin-bottom: 20px;
                      }
                      .table > thead > tr > th,
                      .table > tbody > tr > th,
                      .table > tfoot > tr > th,
                      .table > thead > tr > td,
                      .table > tbody > tr > td,
                      .table > tfoot > tr > td {
                        padding: 8px;
                        line-height: 1.42857143;
                        vertical-align: top;
                        border-top: 1px solid #ddd;
                      }
                      .table > thead > tr > th {
                        vertical-align: bottom;
                        border-bottom: 1px solid #ddd;
                      }
                      .table > caption + thead > tr:first-child > th,
                      .table > colgroup + thead > tr:first-child > th,
                      .table > thead:first-child > tr:first-child > th,
                      .table > caption + thead > tr:first-child > td,
                      .table > colgroup + thead > tr:first-child > td,
                      .table > thead:first-child > tr:first-child > td {
                        border-top: 0;
                      }
                      .table > tbody + tbody {
                        border-top: 1px solid #ddd;
                      }
                      .table .table {
                        background-color: #fff;
                      }
                    //   .table-condensed > thead > tr > th,
                    //   .table-condensed > tbody > tr > th,
                    //   .table-condensed > tfoot > tr > th,
                    //   .table-condensed > thead > tr > td,
                    //   .table-condensed > tbody > tr > td,
                    //   .table-condensed > tfoot > tr > td {
                    //     padding: 5px;
                    //   }
                      .table-bordered {
                        border: 1px solid #ddd;
                      }
                      .table-bordered > thead > tr > th,
                      .table-bordered > tbody > tr > th,
                      .table-bordered > tfoot > tr > th,
                      .table-bordered > thead > tr > td,
                      .table-bordered > tbody > tr > td,
                      .table-bordered > tfoot > tr > td {
                        border: 1px solid #ddd;
                      }
                      .table-bordered > thead > tr > th,
                      .table-bordered > thead > tr > td {
                        border-bottom-width: 2px;
                      }
                      .table-striped > tbody > tr:nth-of-type(odd) {
                        background-color: #f9f9f9;
                      }
                      .table-hover > tbody > tr:hover {
                        background-color: #f5f5f5;
                      }
                    //   table col[class*='col-'] {
                    //     position: static;
                    //     display: table-column;
                    //     float: none;
                    //   }
                    //   table td[class*='col-'],
                    //   table th[class*='col-'] {
                    //     position: static;
                    //     display: table-cell;
                    //     float: none;
                    //   }
                      .table > thead > tr > td.active,
                      .table > tbody > tr > td.active,
                      .table > tfoot > tr > td.active,
                      .table > thead > tr > th.active,
                      .table > tbody > tr > th.active,
                      .table > tfoot > tr > th.active,
                      .table > thead > tr.active > td,
                      .table > tbody > tr.active > td,
                      .table > tfoot > tr.active > td,
                      .table > thead > tr.active > th,
                      .table > tbody > tr.active > th,
                      .table > tfoot > tr.active > th {
                        background-color: #f5f5f5;
                      }
                      .table-hover > tbody > tr > td.active:hover,
                      .table-hover > tbody > tr > th.active:hover,
                      .table-hover > tbody > tr.active:hover > td,
                      .table-hover > tbody > tr:hover > .active,
                      .table-hover > tbody > tr.active:hover > th {
                        background-color: #e8e8e8;
                      }
                      .table > thead > tr > td.success,
                      .table > tbody > tr > td.success,
                      .table > tfoot > tr > td.success,
                      .table > thead > tr > th.success,
                      .table > tbody > tr > th.success,
                      .table > tfoot > tr > th.success,
                      .table > thead > tr.success > td,
                      .table > tbody > tr.success > td,
                      .table > tfoot > tr.success > td,
                      .table > thead > tr.success > th,
                      .table > tbody > tr.success > th,
                      .table > tfoot > tr.success > th {
                        background-color: #dff0d8;
                      }
                      .table-hover > tbody > tr > td.success:hover,
                      .table-hover > tbody > tr > th.success:hover,
                      .table-hover > tbody > tr.success:hover > td,
                      .table-hover > tbody > tr:hover > .success,
                      .table-hover > tbody > tr.success:hover > th {
                        background-color: #d0e9c6;
                      }
                      .table > thead > tr > td.info,
                      .table > tbody > tr > td.info,
                      .table > tfoot > tr > td.info,
                      .table > thead > tr > th.info,
                      .table > tbody > tr > th.info,
                      .table > tfoot > tr > th.info,
                      .table > thead > tr.info > td,
                      .table > tbody > tr.info > td,
                      .table > tfoot > tr.info > td,
                      .table > thead > tr.info > th,
                      .table > tbody > tr.info > th,
                      .table > tfoot > tr.info > th {
                        background-color: #d9edf7;
                      }
                      .table-hover > tbody > tr > td.info:hover,
                      .table-hover > tbody > tr > th.info:hover,
                      .table-hover > tbody > tr.info:hover > td,
                      .table-hover > tbody > tr:hover > .info,
                      .table-hover > tbody > tr.info:hover > th {
                        background-color: #c4e3f3;
                      }
                      .table > thead > tr > td.warning,
                      .table > tbody > tr > td.warning,
                      .table > tfoot > tr > td.warning,
                      .table > thead > tr > th.warning,
                      .table > tbody > tr > th.warning,
                      .table > tfoot > tr > th.warning,
                      .table > thead > tr.warning > td,
                      .table > tbody > tr.warning > td,
                      .table > tfoot > tr.warning > td,
                      .table > thead > tr.warning > th,
                      .table > tbody > tr.warning > th,
                      .table > tfoot > tr.warning > th {
                        background-color: #fcf8e3;
                      }
                      .table-hover > tbody > tr > td.warning:hover,
                      .table-hover > tbody > tr > th.warning:hover,
                      .table-hover > tbody > tr.warning:hover > td,
                      .table-hover > tbody > tr:hover > .warning,
                      .table-hover > tbody > tr.warning:hover > th {
                        background-color: #faf2cc;
                      }
                      .table > thead > tr > td.danger,
                      .table > tbody > tr > td.danger,
                      .table > tfoot > tr > td.danger,
                      .table > thead > tr > th.danger,
                      .table > tbody > tr > th.danger,
                      .table > tfoot > tr > th.danger,
                      .table > thead > tr.danger > td,
                      .table > tbody > tr.danger > td,
                      .table > tfoot > tr.danger > td,
                      .table > thead > tr.danger > th,
                      .table > tbody > tr.danger > th,
                      .table > tfoot > tr.danger > th {
                        background-color: #f2dede;
                      }
                      .table-hover > tbody > tr > td.danger:hover,
                      .table-hover > tbody > tr > th.danger:hover,
                      .table-hover > tbody > tr.danger:hover > td,
                      .table-hover > tbody > tr:hover > .danger,
                      .table-hover > tbody > tr.danger:hover > th {
                        background-color: #ebcccc;
                      }
                    }
                </style>
                </head>
                <body>
                    <table class='table-topo'>
                        <tr><td style='width: 5%; align: center;'>
                            <img src='".$_SESSION['plantaServidor']."/ativos/img/logo_pequeno.png'/></td>
                        <td>
                            <div class='topo'><h2>".$_titulo."</h2></div></td>
                        <td style='width: 13%;'>
                            <div class='topo'>".$_date."</div></td>
                        </tr>
                    </table><hr>";

        //             <header>cabecalho<hr></header>";



        // return '<html><head>
        //         <title>Relatório</title>
        //         <style type="text/css">
        //           @page {margin: 120px 50px 80px 50px;}
        //           .head{font-size: 12px;height: 100px;width: 100%;position: fixed;top: -90px;left: 0;right: 0;margin: auto;}
        //           .main-content{width: 600px;position: relative;margin: auto;}
        //           .footer {font-size: 12px;position: fixed;bottom: 0;width: 100%;text-align: right;}
        //           #footer 
        //           .page:after{content: counter(page); }
        //         </style></head><body>
                
        //         <div class="head">Cabeçalho</div>';
                             
    }

function htmlFooter($_date){
//  return '<div class="footer">Rodapé</div>';
//   <footer>
//   <table width='100%'>
//   <tr>
//   <td>DATA ".$_date."<div class='pagenum-container'>Página <span class='pagenum'></span></div>
//   </td>
//   </tr>
//   </table>
//   </footer>";
    return "<div class='rodape'><hr></div></body></html>";
}
?>