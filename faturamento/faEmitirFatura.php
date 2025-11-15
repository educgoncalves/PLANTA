<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../faturamento/faFuncoes.php");
require_once("../ativos/qrcode/vendor/autoload.php");
verificarExecucao();

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];
$usuario = $_SESSION['plantaUsuario'];

// Recebe os parâmetros
$idFaturamento = carregarGets('id', ""); 

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Emitir Fatura";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<div id="container">
    <div class="row mt-2 ps-5" >
         <div class="d-flex justify-content-start">
            <img class="d-inline-block align-text-top rounded-pill" src="../ativos/img/logo_medio.png" alt="logo">
            <div class="mt-2 ps-4" ><H5><?php echo $nomeAeroporto;?></H5></div>
        </div>
    </div>
    <div class="row mt-2 ps-5">
        <?php
        // Recuperando as informações
        //
        if ($idFaturamento != "") {
            $comando = selectDB("StatusFaturamento"," AND fa.id = ".$idFaturamento,"");
            try {
                $conexao = conexao();
                $sql = $conexao->prepare($comando);   
                if ($sql->execute()) {
                    $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
                    $totalFatura = 0;
                    $linha = 0;

                    foreach ($registros as $dados) {
                        // Só imprime na primeira linha
                        ++$linha;
                        if ($linha == 1) {
                            echo '<div class="row fw-bold mt-4 ps-5">Fatura: '.$dados['faturamento'].'</div>';
                            echo '<div class="row fw-bold mt-2 ps-5">Data: '.$dados['dhConfirmacaoFaturamento'].'</div>';
                            echo '<div class="row fw-bold mt-2 ps-5">Operador: '.$dados['operadorOperacao'].'</div>';
                            echo "
                                <div class='row mt-4 ps-5' style='width:90%'>
                                <table class='table table-striped table-hover table-bordered table-reduzida table-sm'>
                                <thead class='table-info'><tr>
                                <th>Status</th><th>Matrícula</th><th>PMD</th><th>Tipo</th><th>Origem</th><th>Destino</th>
                                <th>Primeiro Movimento</th><th>Último Movimento</th><th>PAT</th><th>EST</th><th>ISE</th>                   
                                <th>PPO</th><th>PPM</th><th>PPE</th>
                                </tr></thead><tbody>
                            ";
                        }
                        echo "
                            <tr>
                            <td>".$dados['status']."</td>
                            <td>".$dados['matricula']."</td> 
                            <td>".$dados['pmd']."</td> 
                            <td>".$dados['classe']." ".$dados['natureza']." ".$dados['servico']."</td> 
                            <td>".$dados['origem']."</td> 
                            <td>".$dados['destino']."</td>  
                            <td>".$dados['moPrimeiroMovimento']." - ".$dados['dataHoraPrimeiroMovimento']."</td> 
                            <td>".$dados['moUltimoMovimento']." - ".$dados['dataHoraUltimoMovimento']."</td>
                            <td>".$dados['tmpPatio']."</td> 
                            <td>".$dados['tmpEstadia']."</td>
                            <td>".$dados['tmpIsento']."</td>
                            <td align='right'>".number_format($dados['vlrPPO'],2,',','.')."</td>
                            <td align='right'>".number_format($dados['vlrPPM'],2,',','.')."</td>
                            <td align='right'>".number_format($dados['vlrPPE'],2,',','.')."</td>
                            </tr>
                        ";

                        $totalFatura += $dados['vlrPPO']+$dados['vlrPPM']+$dados['vlrPPE'];
                    }
                    echo "</tbody></table></div>";

                    echo '<div class="row mt-3 ps-5" style="width:90%">Endereço: '.$dados['enderecoCompleto'].'</div>';
                    echo '<div class="row mt-3 ps-5" style="width:90%">Contato: '.$dados['contatoCompleto'].'</div>';

                    echo '<div class="row mt-3 ps-5 fw-bold">Valor total: R$ '.number_format($totalFatura,2,',','.').'</div>';

                    $pix = pixQRCode('educgoncalves@gmail.com','',$totalFatura);
                    $qrcode = (new \chillerlan\QRCode\QRCode())->render($pix);

                    echo '<div><img src='.$qrcode.'></div>';
                    echo '<div class="row mt-2 ps-5" style="width:90%">Código PIX: '.$pix.'</div>';

                    // Data e hora local do Aeroporto
                    $date = dateTimeUTC($utcAeroporto)->format('Y-m-d H:i');
                    $retorno = atualizarEmissaoFatura($idFaturamento, $dados['faturamento'], $date); 
                } else {
                    throw new PDOException("Não foi possível recuperar este registro!");
                } 
            } catch (PDOException $e) {
                montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
            }
        } else {
            montarMensagem("danger",array("Fatura não informada"));
        }
        ?>
    </div>
</div>
</body>
<!-- *************************************************** -->