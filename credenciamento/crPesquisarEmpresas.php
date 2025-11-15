<?php
header("Content-Type: text/html; charset=UTF-8",true);
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

// Montando os cookies
$empresa = carregarCookie($siglaAeroporto.'_crPE_empresa');
$atividade = carregarCookie($siglaAeroporto.'_crPE_atividade');
$endereco = carregarCookie($siglaAeroporto.'_crPE_endereco');
$bairro = carregarCookie($siglaAeroporto.'_crPE_bairro');
$email = carregarCookie($siglaAeroporto.'_crPE_email');
$telefone = carregarCookie($siglaAeroporto.'_crPE_telefone');
$situacao = carregarCookie($siglaAeroporto.'_crPE_situacao');
$ordenacao = carregarCookie($siglaAeroporto.'_crPE_ordenacao');

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Pesquisar Empresas";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <h4><?php echo $titulo ?></h4>
        <form action="#" method="POST"  class="form-group" autocomplete="off">
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdSituacao" class="cpoLimpar" <?="value=\"{$situacao}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div class ="col-lg-10">                
                        <div class="row mt-2">  
                            <div class="col-md-6">
                                <label for="txEmpresa">Empresa</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txEmpresa" name="empresa"
                                    <?php echo (!isNullOrEmpty($empresa)) ? "value=\"{$empresa}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-9">
                                <label for="txEndereco">Endereço</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txEndereco" name="endereco"
                                    <?php echo (!isNullOrEmpty($endereco)) ? "value=\"{$endereco}\"" : "";?>/>
                            </div>
                            <div class="col-md-3">
                                <label for="txBairro">Bairro</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txBairro" name="bairro"
                                    <?php echo (!isNullOrEmpty($bairro)) ? "value=\"{$bairro}\"" : "";?>/>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-9">
                                <label for="txEmail">Email</label>
                                <input type="email" class="form-control cpoCookie input-lg" id="txEmail" name="email"
                                    <?php echo (!isNullOrEmpty($email)) ? "value=\"{$email}\"" : "";?>/>
                            </div>                            
                            <div class="col-md-3">
                                <label for="txTelefone">Telefone</label>
                                <input type="text" class="form-control cpoCookie phone" id="txTelefone" name="telefone" maxlength="15"
                                    <?php echo (!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>
                            </div>  
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="txAtividade">Atividade</label>
                                <input type="text" class="form-control cpoCookie caixaAlta input-lg" id="txAtividade" name="atividade"
                                    <?php echo (!isNullOrEmpty($atividade)) ? "value=\"{$atividade}\"" : "";?>/>
                            </div>   
                            <div class="col-md-3">
                                <label for="slSituacao">Situação</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slSituacao" name="situacao">
                                </select> 
                            </div>
                            <div class="col-md-3">
                                <label for="slOrdenacao">Ordenação da lista</label>
                                <select class="form-select cpoCookie selCookie input-lg" id="slOrdenacao" name="ordenacao">
                                    <option <?php echo ($ordenacao == 'em.empresa') ? 'selected' : '';?> value='em.empresa'>Empresa</option>
                                    <option <?php echo ($ordenacao == 'em.atividade, em.empresa') ? 'selected' : '';?> value='em.atividade, em.empresa'>Atividade</option>
                                    <option <?php echo ($ordenacao == 'em.bairro, em.empresa') ? 'selected' : '';?> value='em.bairro, em.empresa'>Bairro</option>
                                </select> 
                            </div>                            
                        </div>
                    </div>  
                    <div class ="col-lg-2">
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="limparFormulario" value="Limpar"/>
                        </div>
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="exportarPDF" value="Imprimir"/>
                        </div>
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="buscarCadastro" value="Buscar"/>
                        </div>
                    </div>
                </div>  
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
</body>
</html>    

<script src="../credenciamento/crFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            processarCookies('apagar',$('#hdSiglaAeroporto').val()+'_crPE');
            $("#txEmpresa").focus();
        });

        $(".caixaAlta").keyup(function(){
            $(this).val($(this).val().toUpperCase());
        });

        // Montagem dos filtros e descrição para buscar o cadastro
        $("#buscarCadastro").click(function(){
            document.getElementById("hdPagina").value = 1;
            buscarCadastro();
        });
        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";
            filtro += (!isEmpty($("#hdAeroporto").val()) ? " AND em.idAeroporto = "+$("#hdAeroporto").val() : "");
            descricaoFiltro += (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val(): '');

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "txEmpresa":
                            filtro += " AND em.empresa LIKE '%"+$("#txEmpresa").val()+"%'";
                            descricaoFiltro += " <br>Empresa : "+$("#txEmpresa").val();
                            break;
                        case "txEndereco":
                            filtro += " AND em.endereco LIKE '%"+$("#txEndereco").val()+"%'";
                            descricaoFiltro += " <br>Endereço : "+$("#txEndereco").val();
                            break;    
                        case "txBairro":
                            filtro += " AND em.bairro LIKE '%"+$("#txBairro").val()+"%'";
                            descricaoFiltro += " <br>Bairro : "+$("#txBairro").val();
                            break; 
                        case "txEmail":
                            filtro += " AND em.email LIKE '%"+$("#txEmail").val()+"%'";
                            descricaoFiltro += " <br>Email : "+$("#txEmail").val();
                            break; 
                        case "txTelefone":
                            filtro += " AND em.telefone LIKE '%"+$("#txTelefone").val()+"%'";
                            descricaoFiltro += " <br>Telefone : "+$("#txTelefone").val();
                            break; 
                        case "txAtividade":
                            filtro += " AND em.atividade LIKE '%"+$("#txAtividade").val()+"%'";
                            descricaoFiltro += " <br>Atividade : "+$("#txAtividade").val();
                            break; 
                        case "slSituacao":
                            filtro += " AND em.situacao = '"+$("#slSituacao").val()+"'";
                            descricaoFiltro += " <br>Situação : "+$("#slSituacao :selected").text();
                            break;                           
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });

            // Montagem da ordem
            var ordem = $("#slOrdenacao").val();

            await processarCookies('criar',$('#hdSiglaAeroporto').val()+'_crPE');
            await crCarregarEmpresas('Consultar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#txEmpresa").focus();
        }

        $("#exportarPDF").click(function(){
            var form = "<form id='relatorio' action='../suporte/suRelatorio.php' method='post' >";
            form += '<input type="hidden" name="arquivo" value="'+$("#hdSiglaAeroporto").val()+'">';
            form += '<input type="hidden" name="titulo" value="' + $('#divTitulo').text() + '">';
            form += '<input type="hidden" name="relatorio" value="' + $('#divImpressao').html().replace(/\"/g,'\'') + '">';
            form += '<input type="hidden" name="download" value="1">';
            form += '<input type="hidden" name="orientacao" value="P">';
            form += '</form>';
            $('body').append(form);
            $('#relatorio').submit().remove();
            $("#txEmpresa").focus();
        });

        await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Consultar');
        $("#txEmpresa").focus();

        // Executa a paginação
        if ($('#hdPaginacao').val() == 'SIM') {
            buscarCadastro();
        }

        // Interceptar a tecla enter e submeter o botão principal
        //
        document.addEventListener("keypress", function(e) {
            if(e.key === 'Enter') {
                event.preventDefault();
                buscarCadastro();
            }
        });
    });
    
    var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }, options = { onKeyPress: function (val, e, field, options) {
                    field.mask(behavior.apply({}, arguments), options);}};
    $('.phone').mask(behavior, options);
</script>