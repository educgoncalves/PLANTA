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
$utcAeroporto = $_SESSION['plantaUTCSite'];
$siglaAeroporto = $_SESSION['plantaSite'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id'); 
    $usuario = carregarPosts('usuario');
    $aeroporto = carregarPosts('site');
    $sistema = carregarPosts('sistema');
    $grupo = carregarPosts('grupo');
    $preferencial = carregarPosts('preferencial');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
    
} else  {
    $id = carregarGets('id'); 
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se campos estão preenchidos
    $erros = camposPreenchidos(['usuario','site','grupo']); //,'preferencial']);
    if (!$erros) {
        try {
            $conexao = conexao();
            if ($id != "") {
                $comando = "UPDATE planta_acessos SET idUsuario = ".$usuario.", idSite = ".$aeroporto.", sistema = '".$sistema."', grupo = '".$grupo.
                            "', preferencial = '".$preferencial."', cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            } else {
                $comando = "INSERT INTO planta_acessos (idUsuario, idSite, sistema, grupo, preferencial, cadastro) VALUES (".
                            $usuario.", ".$aeroporto.", '".$sistema."', '".$grupo."', '".$preferencial."', UTC_TIMESTAMP())";
            }
            $sql = $conexao->prepare($comando);               
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("planta_acessos", ($id != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaSite'], 
                                $_SESSION['plantaUsuario'], ($id != "" ? $id  : $conexao->lastInsertId()), $comando);
                    montarMensagem("success",array("Registro ".($id != "" ? "alterado" : "incluído")." com sucesso!"));
                    $id = null;
                    $limparCampos = true;
                } else {
                    throw new PDOException("Não foi possível efetivar esta ".($id != "" ? "alteração" : "inclusão")."!");
                }
            } else {
                throw new PDOException("Não foi possível ".($id != "" ? "alterar" : "incluir")." este registro!");
            } 
        } catch (PDOException $e) {
            montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        }
    } else {
        montarMensagem("danger", $erros);
    } 
}
    
// Recuperando as informações
if ($evento == "recuperar" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "SELECT * FROM planta_acessos WHERE id = ".$id;
        $sql = $conexao->prepare($comando);     
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $usuario = $dados['idUsuario'];
                $aeroporto = $dados['idSite'];
                $sistema = $dados['sistema'];
                $grupo = $dados['grupo'];
                $preferencial = $dados['preferencial'];
            }
            $limparCampos = false;
        } else {
            throw new PDOException("Não foi possível recuperar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Excluindo as informações
if ($evento == "excluir" && $id != "") {
    try {
        $conexao = conexao();
        $comando = "DELETE FROM planta_acessos WHERE id = ".$id;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("planta_acessos", "Exclusão", $_SESSION['plantaSite'], $_SESSION['plantaUsuario'], $id, $comando);            
            montarMensagem("success",array("Registro excluído com sucesso!"));
            $id = null;
            $limparCampos = true;
        } else {
            throw new PDOException("Não foi possível excluir este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $usuario = null;
    $aeroporto = null;
    $sistema = null;
    $grupo = null;
    $preferencial = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_adCA_ordenacao','us.usuario,st.site,ac.sistema');            
metaTagsBootstrap('');
$titulo = "Acessos dos Usuários";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <div class="container alert alert-padrao" >
        <form action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;">
            <?php barraFuncoesCadastro($titulo); ?>           
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>                
                <input type="hidden" class="cpoLimpar" id="hdId" name="id" <?="value=\"{$id}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdSistema" <?="value=\"{$sistema}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdGrupo" <?="value=\"{$grupo}\"";?>/>
                <input type="hidden" class="cpoLimpar" id="hdPreferencial" <?="value=\"{$preferencial}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label for="slUsuario">Usuário</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slUsuario" name="usuario">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">                            
                            <div class="col-md-8">
                                <label for="slAeroporto">Aeroporto</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slAeroporto" name="aeroporto">
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label for="slSistema">Sistema</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSistema" name="sistema">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="slGrupo">Grupo</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slGrupo" name="grupo">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="slPreferencial">Preferencial</label>
                                <select class="form-select cpoObrigatorio selLimpar input-lg" id="slPreferencial" name="preferencial">
                                </select> 
                            </div>
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
                    <div class="col-md-8">
                        <label for="pslUsuario">Usuário</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslUsuario">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">                    
                    <div class="col-md-8">
                        <label for="ptxNome">Nome</label>
                        <input type="text" class="form-control cpoCookie input-lg" id="ptxNome"/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label for="pslAeroporto">Aeroporto</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslAeroporto">
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label for="pslSistema">Sistema</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslSistema">
                        </select>
                    </div>  
                </div>
                <div class="row mt-2">                                     
                    <div class="col-md-8">
                        <label for="pslGrupo">Grupo</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslGrupo">
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pslPreferencial">Preferencial</label>
                        <select class="form-select cpoCookie selCookie input-lg" id="pslPreferencial">
                        </select> 
                    </div>
                </div>
                <br>
                <div class="row mt-2">                      
                    <div class="col-md-8">
                        <label for="pslOrdenacao">Ordenação da lista</label>
                        <select class="form-select selCookie input-lg" id="pslOrdenacao">
                            <option <?php echo ($ordenacao == 'us.usuario,ae.iata,ac.sistema') ? 'selected' : '';?> value='us.usuario,st.site,ac.sistema'>Usuário</option>
                            <option <?php echo ($ordenacao == 'ae.iata,us.usuario,ac.sistema') ? 'selected' : '';?> value='st.site,us.usuario,ac.sistema'>Aeroporto</option>
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

<script src="../administracao/adFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txUsuario").focus();
        });

        $("#buscarCadastro").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro();});        
        async function buscarCadastro() {
            var filtro = "";
            var descricaoFiltro = "";

            $(".cpoCookie").each(function(){
                if (!isEmpty($(this).val())) {
                    switch ($(this).attr('id')) {
                        case "pslUsuario":
                            filtro += " AND ac.idUsuario = "+$("#pslUsuario").val();
                            descricaoFiltro += " <br>Usuário : "+$("#pslUsuario :selected").text();
                            break;
                        case "ptxNome":
                            filtro += " AND us.nome LIKE '%"+$("#ptxNome").val()+"%'";
                            descricaoFiltro += " <br>Nome : "+$("#ptxNome").val();
                            break; 
                        case "pslAeroporto":
                            filtro += " AND ac.idSite = "+$("#pslAeroporto").val();
                            descricaoFiltro += " <br>Aeroporto : "+$("#pslAeroporto :selected").text();
                        break; 
                        case "pslSistema":
                            filtro += " AND ac.sistema = '"+$("#pslSistema").val()+"'";
                            descricaoFiltro += " <br>Sistema : "+$("#pslSistema :selected").text();
                            break;                                                        
                        case "pslGrupo":
                            filtro += " AND ac.grupo = '"+$("#pslGrupo").val()+"'";
                            descricaoFiltro += " <br>Grupo : "+$("#pslGrupo :selected").text();
                        break; 
                        case "pslPreferencial":
                            filtro += " AND ac.preferencial = '"+$("#pslPreferencial").val()+"'";
                            descricaoFiltro += " <br>Preferencial : "+$("#pslPreferencial :selected").text();
                            break;                           
                        default:
                            filtro += "";
                            descricaoFiltro += "";
                    }
                }
            });            

            // Montagem da ordem
            var ordem = $("#pslOrdenacao").val();

            await criarCookie($('#hdSiglaAeroporto').val()+'_adCA_ordenacao', ordem);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCA_filtro', filtro);
            await criarCookie($('#hdSiglaAeroporto').val()+'_adCA_descricao', descricaoFiltro);
            
            await adCarregarAcessos('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
            $("#slUsuario").focus();
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
            $("#txUsuario").focus();
        });

        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_adCA_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_adCA_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_adCA_descricao');
        $("#limparPesquisa").click(function(){ limparPesquisa(); $("#pslUsuario").focus(); });
        $("#aplicarPesquisa").click(function(){ document.getElementById("hdPagina").value = 1; buscarCadastro(); });
        $("#iniciarPesquisar").click(async function(){ 
            await suCarregarSelectTodos('AcessosPreferencial','#pslPreferencial','','','Consultar');
            await suCarregarSelectTodos('AcessosGrupos','#pslGrupo','','','Consultar');
            await suCarregarSelectTodos('TodosSistema','#pslSistema','','','Consultar');
            await suCarregarSelectTodos('AeroportosClientes','#pslAeroporto','','','Consultar');
            await suCarregarSelectTodos('Usuarios','#pslUsuario','','','Consultar');
        });
        
        // Adequações para o cadastro          
        await suCarregarSelectTodos('AcessosPreferencial','#slPreferencial', $('#hdPreferencial').val(),'','Cadastrar');
        await suCarregarSelectTodos('AcessosGrupos','#slGrupo', $('#hdGrupo').val(),'','Cadastrar');
        await suCarregarSelectTodos('TodosSistema','#slSistema', $('#hdSistema').val(), '','Cadastrar');
        await suCarregarSelectTodos('AeroportosClientes','#slAeroporto', $('#hdSite').val(), '','Cadastrar');
        await suCarregarSelectTodos('Usuarios','#slUsuario', $('#hdUsuario').val(),'','Cadastrar');
        await adCarregarAcessos('Cadastrar', pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        $("#txUsuario").focus();
    });
</script>