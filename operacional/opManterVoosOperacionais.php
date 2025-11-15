<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../operacional/opFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Recuperando as informações do Aeroporto
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Adicionais 
$filtro = carregarCookie($siglaAeroporto.'_opMVO_filtro');
$descricao = carregarCookie($siglaAeroporto.'_opMVO_descricao');
$ordenacao = carregarCookie($siglaAeroporto.'_opMVO_ordenacao');

// // Verificar se foi enviando dados via POST ou inicializa as variáveis
// $limparCampos = false;
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $id = carregarPosts('id'); 
//     $operacao = carregarPosts('operacao');
//     $operador = carregarPosts('operador');
//     $numeroVoo = carregarPosts('numeroVoo');
//     $equipamento = carregarPosts('equipamento');
//     $assentos = carregarPosts('assentos');
//     $dtMovimento = carregarPosts('dtMovimento');
//     $dhPrevista = carregarPosts('dhPrevista');
//     $dhConfirmada = carregarPosts('dhConfirmada');
//     $naturezaOperacao = carregarPosts('naturezaOperacao');
//     $numeroEtapa = carregarPosts('numeroEtapa');
//     $de = carregarPosts('de');
//     $para = carregarPosts('para');
//     $servico = carregarPosts('servico');
//     $objetoTransporte = carregarPosts('objetoTransporte');
//     $codeshare = carregarPosts('codeshare');
//     $situacao = carregarPosts('situacao');

//     // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
//     if (!verificaRequest('')) { goto formulario; }
         
// } else  {
//     $id = carregarGets('id'); 
//     $limparCampos = true;
// }

// // Salvando as informações
// if ($evento == "salvar") {
//     // Verifica se campos estão preenchidos
//     $erros = camposPreenchidos(['operador','empresa','numeroVoo']); 
//     if (!$erros) {
//         try {
//             $conexao = conexao();
//             // if ($id != "") {
//             //     $comando = "UPDATE gear_voos_operacionais
//             // } else {
//             //     $comando = "INSERT INTO gear_voos_operacionais
//             // }
//             $sql = $conexao->prepare($comando);               
//             if ($sql->execute()) {
//                 if ($sql->rowCount() > 0) {
//                     gravaDLog("gear_voos_operacionais", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
//                                 $_SESSION['plantaUsuario'], ($id != "" ? $id  : $conexao->lastInsertId()), $comando);
//                     montarMensagem("success",array("Registro ".($id != "" ? "alterado" : "incluído")." com sucesso!"));
//                     $id = null;
//                     $limparCampos = true;
//                 } else {
//                     throw new PDOException("Não foi possível efetivar esta ".($id != "" ? "alteração" : "inclusão")."!");
//                 }
//             } else {
//                 throw new PDOException("Não foi possível ".($id != "" ? "alterar" : "incluir")." este registro!");
//             } 
//         } catch (PDOException $e) {
//             montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
//         }
//     } else {
//         montarMensagem("danger", $erros);
//     } 
// }
    
// // Recuperando as informações
// if ($evento == "recuperar" && $id != "") {
//     try {
//         $conexao = conexao();
//         $comando = "SELECT * FROM gear_voos_operacionais WHERE id = ".$id;
//         $sql = $conexao->prepare($comando);     
//         if ($sql->execute()) {
//             $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
//             foreach ($registros as $dados) {
//                 $operacao = $dados['operacao'];
//                 $operador = $dados['operador'];
//                 $numeroVoo = $dados['numeroVoo'];
//                 $equipamento = $dados['equipamento'];
//                 $assentos = $dados['assentos'];
//                 $dtMovimento = $dados['dtMovimento'];
//                 $dhPrevista = $dados['dhPrevista'];
//                 $dhConfirmada = $dados['dhConfirmada'];
//                 $naturezaOperacao = $dados['naturezaOperacao'];
//                 $numeroEtapa = $dados['numeroEtapa'];
//                 $de = $dados['de'];
//                 $para = $dados['para'];
//                 $servico = $dados['servico'];
//                 $objetoTransporte = $dados['objetoTransporte'];
//                 $codeshare = $dados['codeshare'];
//                 $situacao = $dados['situacao'];
//             }
//             $limparCampos = false;
//         } else {
//             throw new PDOException("Não foi possível recuperar este registro!");
//         } 
//     } catch (PDOException $e) {
//         montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
//     }
// }

// // Excluindo as informações
// if ($evento == "excluir" && $id != "") {
//     try {
//         $conexao = conexao();
//         $comando = "DELETE FROM gear_voos_operacionais WHERE id = ".$id;
//         $sql = $conexao->prepare($comando); 
//         if ($sql->execute()){
//             gravaDLog("gear_voos_operacionais", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $id, $comando);            
//             montarMensagem("success",array("Registro excluído com sucesso!"));
//             $id = null;
//             $limparCampos = true;
//         } else {
//             throw new PDOException("Não foi possível excluir este registro!");
//         } 
//     } catch (PDOException $e) {
//         montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
//     }
// }

// Limpar movimento
if ($evento == "limparMovimento") {
    if (!empty($filtro)) {
        if (limparMovimento($filtro)){
            gravaDLog("gear_voos_operacionais", "Limpar Movimento", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], 0, $filtro);            
            montarMensagem("success",array("Movimento excluído com sucesso!"));
        } else {
            montarMensagem("danger",array("Não foi possível excluir este movimento!"),$filtro);
        }
    } else {
        montarMensagem("danger",array("Pesquisa deve estar atualizada!"));
    }    
}

// // Limpeza dos campos 
// //
// if ($limparCampos == true) {
//     $operacao = null;
//     $operador = null;
//     $numeroVoo = null;
//     $equipamento = null;
//     $assentos = null;
//     $dtMovimento = null;
//     $dhPrevista = null;
//     $dhConfirmada = null;
//     $naturezaOperacao = null;
//     $numeroEtapa = null;
//     $de = null;
//     $para = null;
//     $servico = null;
//     $objetoTransporte = null;
//     $codeshare = null;
//     $situacao = null;
// }

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie('opMVO_ordenacao','vo.dhPrevista,vo.operacao,vo.operador,vo.numeroVoo,vm.id');
metaTagsBootstrap('');
$titulo = "Voos Operacionais";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>  
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;">
            <?php barraFuncoesCadastro($titulo,array("","X","","X","X","","","","","","","","X")); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <!-- <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\""?>/> -->
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdOrdenacao" <?="value=\"{$ordenacao}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class ="col-lg-12 d-flex justify-content-end">
                    <div class="row pt-4 px-2">
                        <a href='?evento=limparMovimento' class="btn btn-padrao" role="button" 
                            onclick="return confirm('Confirma a exclusão do movimento listado abaixo?');">Limpar Movimento</a>
                    </div>
                </div>                
                <?php destacarVoosOperacionais(); ?>  
            </div>
        </form>
    </div>
    <div class="container">
        <div class="row" >
            <div class="col-md-8" id="divTitulo"></div>
            <div class="col-md-4" id="divPagina"></div>
        </div>
    </div>
    <div class="container table-responsive" id="divTabela"></div>
    <div class="container" id="divImpressao" style="display:none"></div>
</div>

<!-- *************************************************** -->
<!-- Modal PESQUISA -->
<!-- *************************************************** -->
<div class="modal fade" id="pesquisarCadastro" tabindex="-1" aria-labelledby="sobreLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sobreLabel">Pesquisar <?php echo $titulo ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="pslOperador">Operador</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslOperador">
                        </select>
                    </div>   
                    <div class="col-md-6">
                        <label for="ptxNumero">Número</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNumero">
                    </div>                          
                </div>
                <div class="row mt-2" >
                    <div class="col-md-6">
                        <label for="pdtInicioMovimento">Período de Movimento</label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtInicioMovimento" size="10"/>
                    </div>
                    <div class="col-md-6">
                        <label for="pdtFinalMovimento"></label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtFinalMovimento" size="10"/>
                    </div>   
                </div>   
                <div class="row mt-2" >
                    <div class="col-md-6">
                        <label for="pdtInicioPrevisao">Período de Previsão</label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtInicioPrevisao" size="10"/>
                    </div>
                    <div class="col-md-6">
                        <label for="pdtFinalPrevisao"></label>
                        <input type="date" class="form-control cpoCookie input-lg" id="pdtFinalPrevisao" size="10"/>
                    </div>   
                </div> 
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="pslClasse">Classe</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslClasse">
                        </select>
                    </div>                             
                    <div class="col-md-6">
                        <label for="pslNatureza">Natureza</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslNatureza">
                        </select>
                    </div>
                </div>   
                <div class="row mt-2">                    
                    <div class="col-md-12">
                        <label for="pslServico">Tipo do Serviço</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslServico">
                        </select>
                    </div>  
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-6">
                        <label for="ptxIcaoOrigem">Origem</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxIcaoOrigem">
                    </div>
                    <div class="col-md-6">
                        <label for="ptxIcaoDestino">Destino</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxIcaoDestino">
                    </div>
                </div>
                <br>
                <div class="row mt-2">                    
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'vo.dhPrevista,vo.operacao,vo.operador,vo.numeroVoo,vm.id') ? 'selected' : '';?> 
                                                        value='vo.dhPrevista,vo.operacao,vo.operador,vo.numeroVoo,vm.id'>Previsão</option>
                            <option <?php echo ($ordenacao == 'vo.operacao,vo.dhPrevista,vo.operador,vo.numeroVoo,vm.id') ? 'selected' : '';?> 
                                                        value='vo.operacao,vo.dhPrevista,vo.operador,vo.numeroVoo,vm.id'>Operação</option>
                            <option <?php echo ($ordenacao == 'vo.operador,vo.numeroVoo,vo.dhPrevista,vo.operacao,vm.id') ? 'selected' : '';?> 
                                                        value='vo.operador,vo.numeroVoo,vo.dhPrevista,vo.operacao,vm.id'>Voo</option>                                                                                                                          
                        </select> 
                    </div>
                </div>
            </div>
            <?php barraFuncoesPesquisa($titulo); ?>
        </div>
    </div>
</div>
<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->   

<script src="../operacional/opFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#slOperador").focus();
        });

        $(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            var busca = $("#hdSiglaAeroporto").val();

            // Monta filtro fixo da indentificação do aeroporto
            filtro = " AND vo.idAeroporto = "+$("#hdAeroporto").val();
            descricaoFiltro = ' <br>Aeroporto : '+$("#hdNomeAeroporto").val();

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslOperador":
                            filtro += " AND vo.operador = '"+$("#pslOperador").val()+"'";
                            descricaoFiltro += ' <br>Operador : '+$("#pslOperador :selected").text();
                        break; 
                        case "ptxNumero":
                            filtro += " AND vo.numeroVoo = '"+$("#ptxNumero").val()+"'";
                            descricaoFiltro += " <br>Número : "+$("#ptxNumero").val();
                        break;
                        case "pdtInicioMovimento":
                            filtro += " AND (DATE_FORMAT(vo.dtMovimento,'%Y-%m-%d') >= '"+mudarDataAMD($("#pdtInicioMovimento").val())+"'"+
                                        " AND DATE_FORMAT(vo.dtMovimento,'%Y-%m-%d') <= '"+mudarDataAMD($("#pdtFinalMovimento").val())+"')"
                            descricaoFiltro += ' <br>Período de Movimento : '+mudarDataDMA($("#pdtInicioMovimento").val())+' a '+
                                                                            mudarDataDMA($("#pdtFinalMovimento").val());
                        break;  
                        case "pdtInicioPrevisao":
                            filtro += " AND (DATE_FORMAT(vo.dhPrevista,'%Y-%m-%d') >= '"+mudarDataAMD($("#pdtInicioPrevisao").val())+"'"+
                                        " AND DATE_FORMAT(vo.dhPrevista,'%Y-%m-%d') <= '"+mudarDataAMD($("#pdtFinalPrevisao").val())+"')"
                            descricaoFiltro += ' <br>Período de Previsão : '+mudarDataDMA($("#pdtInicioPrevisao").val())+' a '+
                                                                            mudarDataDMA($("#pdtFinalPrevisao").val());
                        break;  
                        case "pslClasse":
                            filtro += " AND vo.classe = '"+$("#pslClasse").val()+"'";
                            descricaoFiltro += ' <br>Classe : '+$("#pslClasse :selected").text();
                        break;  
                        case "pslNatureza":
                            filtro += " AND vo.natureza = '"+$("#pslNatureza").val()+"'";
                            descricaoFiltro += ' <br>Natureza : '+$("#pslNatureza :selected").text();
                        break;                        
                        case "pslServico":                            
                            filtro += " AND vo.servico = '"+$("#pslServico").val()+"'";
                            descricaoFiltro += ' <br>Tipo de Serviço : '+$("#pslServico :selected").text();
                        break;
                        case "ptxIcaoOrigem":
                            filtro += " AND vo.origem = '"+$("#ptxIcaoOrigem").val()+"'";
                            descricaoFiltro += ' <br>Origem : '+$("#ptxIcaoOrigem").val();
                        break;   
                        case "ptxIcaoDestino":
                            filtro += " AND vo.destino = '"+$("#ptxIcaoDestino").val()+"'";
                            descricaoFiltro += ' <br>Destino : '+$("#ptxIcaoDestino").val();
                        break;
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVO_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVO_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVO_descricao', descricaoFiltro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_opMVO_busca', busca);
            
            await opCarregarVoosOperacionais('Consultar', filtro, ordem, descricaoFiltro, busca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slOperador").focus();
        }

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="L">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#slOperador").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_opMVO_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_opMVO_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_opMVO_descricao');
        var pesquisaBusca = await valorCookie($('#hdSiglaAeroporto').val()+'_opMVO_busca');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslOperador").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            //await suCarregarSelectTodos('DestinoIcao','#pslIcaoDestino','','', 'Consulta');
            //await suCarregarSelectTodos('OrigemIcao','#pslIcaoOrigem','','', 'Consulta');
            await suCarregarSelectTodos('OperadorANAC','#pslOperador','','', 'Consulta', 'codigo');
            await suCarregarSelectTodas('Classe','#pslClasse', '', '', 'Consulta');
            await suCarregarSelectTodas('Natureza','#pslNatureza', '', '', 'Consulta');
            await suCarregarSelectTodas('Servico','#pslServico', '', '', 'Consulta');
        });            

        // Adequações para o cadastro    
        if (isEmpty(pesquisaFiltro)) {
            pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND vo.idAeroporto = "+$("#hdAeroporto").val() : "");
            pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');
        }      

        await opCarregarVoosOperacionais('Consultar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, pesquisaBusca, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#slOperador").focus();
    });
</script>
