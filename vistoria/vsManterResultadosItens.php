<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../vistoria/vsFuncoes.php");
verificarExecucao();

// Controle de paginação
$_page = carregarGets('page', 1);
$_paginacao = carregarGets('paginacao', 'NAO'); 
$_limite = carregarGets('limite', $_SESSION['plantaRegPorPagina']);  

// Token
$token = gerarToken($_SESSION['plantaSistema']);

// Recuperando as informações do Aeroporto
$usuario = $_SESSION['plantaUsuario'];
$idUsuario = $_SESSION['plantaIDUsuario'];
$aeroporto = $_SESSION['plantaIDAeroporto'];
$utcAeroporto = $_SESSION['plantaUTCAeroporto'];
$siglaAeroporto = $_SESSION['plantaAeroporto'];
$nomeAeroporto = $_SESSION['plantaAeroporto'].' - '.$_SESSION['plantaLocalidadeAeroporto'];

// Recebendo eventos e parametros para executar os procedimentos
$evento = carregarGets('evento',carregarPosts('evento'));
$collapse = ($evento != "recuperar" ? "hide" : "show");
$parametros = array('evento'=>$evento);

// Adicionais 
$local = carregarCookie($siglaAeroporto.'_vsMRI_local');

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$collapse = "hide";
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = carregarPosts('id');              // Agendamento
    $idPlano = carregarPosts('idPlano');
    $execucao = carregarPosts('execucao');
    $tipo = carregarPosts('tipo');
    $numero = carregarPosts('numero');
    $item = carregarPosts('item');
    $parecer = carregarPosts('parecer');
    $idItem = carregarPosts('idItem');

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest($evento)) { goto formulario; }
     
} else  {
    $id = carregarGets('id');              // Agendamento
    $idPlano = carregarGets('idPlano');
    $idItem = carregarGets('idItem');
    $execucao = carregarGets('execucao');
    $limparCampos = true;
}

// Salvando as informações
if ($evento == "salvar") {
    // Verifica se existe este agendamento na tabela de resultados, caso negativo grava todos os itens com parecer zerado
    //  
    $retorno = gerarResultadosAgendamento($id, $siglaAeroporto, $usuario, $idUsuario);
    if ($retorno['status'] == 'OK') {
        try {
            // So regrava o item no resultados se ele vier informado
            //
            $conexao = conexao();
            if (!empty($idItem)) {
                $comando = "UPDATE gear_vistoria_resultados SET parecer = '".$parecer."' , cadastro = UTC_TIMESTAMP() WHERE idAgendamento = ".
                            $id. " AND idItem = ".$idItem;
                $sql = $conexao->prepare($comando); 
                if ($sql->execute()) {
                    if ($sql->rowCount() > 0) {
                        gravaDLog("gear_vistoria_resultados", "Alteração", $siglaAeroporto, $usuario, $id, $comando, "Atualizar parecer");                
                        montarMensagem("success",array("Resultado atualizado com sucesso!"));
                        $idItem = null;
                    } else {
                        throw new PDOException("Não foi atualizar o resultado!");
                    }
                } else {
                    throw new PDOException("Não foi atualizar o resultado!");
                }
            }
            $limparCampos = true;
        } catch (PDOException $e) {
            montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        }
    } else {
        montarMensagem("danger",array($retorno['msg']));   
    }
}

// Salvando as informações
if ($evento == "salvarMapa") {
    // Verifica se existe este agendamento na tabela de resultados, caso negativo grava todos os itens com parecer zerado
    //  
    $retorno = gerarResultadosAgendamento($id, $siglaAeroporto, $usuario, $idUsuario);
    if ($retorno['status'] == 'OK') {
        try {
            // Verifica se grava a execução, usuário e local no agendamentos 
            $conexao = conexao();
            $comando = "UPDATE gear_vistoria_agendamentos SET local = '".$local."' , cadastro = UTC_TIMESTAMP() WHERE id = ".$id;
            $sql = $conexao->prepare($comando); 
            if ($sql->execute()) {
                if ($sql->rowCount() > 0) {
                    gravaDLog("gear_vistoria_agendamentos", "Alteração", $siglaAeroporto, $usuario, $id, $comando, "Atualização do Mapa");                
                    montarMensagem("success",array("Mapa atualizado com sucesso!"));
                } else {
                    throw new PDOException("Não foi possível atualizar o mapa!");
                }
            } else {
                throw new PDOException("Não foi possível atualizar o mapa!");
            }            
            $limparCampos = true;
        } catch (PDOException $e) {
            montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
        }
    } else {
        montarMensagem("danger",array($retorno['msg']));   
    }
}

// Recuperando as informações
// Caso ainda não existe pegar da tabela de itens
if ($evento == "recuperar" && $id != "" && $idItem != "") {
    try {
        $conexao = conexao();
        $comando = selectDB("VistoriaItensResultados"," AND va.id = ".$id." AND vi.id = ".$idItem,"");
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            if ($sql->rowCount() == 0) {
                $comando = selectDB("VistoriaItens"," AND vi.id = ".$idItem,"");
                $sql = $conexao->prepare($comando);  
                if ($sql->execute()) {
                    if ($sql->rowCount() == 0) {
                        throw new PDOException("Não foi possível recuperar este registro!");
                    }
                } else {
                    throw new PDOException("Não foi possível recuperar este registro!");
                } 
            }
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $tipo = $dados['descTipo'];
                $numero = $dados['numero'];
                $item = $dados['item'];
                $parecer = $dados['parecer'];
            }
            $limparCampos = false;
            // Descolapsar o formulario
            $collapse="show";
        } else {
            throw new PDOException("Não foi possível recuperar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Verifica se o agendamento existe na tabela de resultados
if ($execucao == "") {
    $retorno = verificarResultadosAgendamento($id);
    $execucao = $retorno['execucao'];
}

// Limpeza dos campos 
//
if ($limparCampos == true) {
    $tipo = null;
    $numero = null;
    $item = null;
    $parecer = null;
}

// Ponto para exibição do formulário
formulario:
$ordenacao = carregarCookie($siglaAeroporto.'_vsMRIordenacao','vi.numero');
metaTagsBootstrap('');
$titulo = "Manter Resultados - Itens";
?>
<!-- CSS -->
<link rel="stylesheet" href="../ativos/css/mapa.css">
<!-- *************************************************** -->
<head>
    <title><?=$_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto)?>    
    <div class="container alert alert-padrao" >
        <form id="frmResultado" action="?evento=salvar" method="POST"  class="form-group" autocomplete="off" onsubmit="camposObrigatorios(this); return false;"> 
            <?php barraFuncoesCadastro($titulo,array("X","X","X","","X","X","","","","X","","","X")); ?>        
	    	<div class="form-group">
                <!-- Campos hidden -->
                <input type="hidden" name="usuario" id="hdUsuario" <?="value=\"{$usuario}\"";?>/>
                <input type="hidden" name="aeroporto" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" name="siglaAeroporto" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>

                <input type="hidden" name="id" id="hdId" <?="value=\"{$id}\"";?>/>
                <input type="hidden" name="idPlano" id="hdIdPlano" <?="value=\"{$idPlano}\"";?>/>
                <input type="hidden" name="idItem" id="hdIdItem" <?="value=\"{$idItem}\"";?>/>
                <input type="hidden" name="execucao" id="hdExecucao" <?="value=\"{$execucao}\"";?>/>
                <input type="hidden" name="local" id="hdLocal" <?="value=\"{$local}\"";?>/>

                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
            </div>
            <div id="formularioCadastro" class = "col-lg-12 collapse <?="{$collapse}" ?>">
                <div class="row mt-2">
                    <div class="col-md-10">
                        <label for="txTipo">Tipo</label>
                        <input type="text" class="form-control cpoHabilitar input-lg" id="txTipo" name="tipo"
                            <?=(!isNullOrEmpty($tipo)) ? "value=\"{$tipo}\"" : "";?>/>
                    </div>
                </div>
                <div class="row mt-2">                             
                    <div class="col-md-2">
                        <label for="txNumero">Número</label>
                        <input type="text" class="form-control cpoHabilitar input-lg" id="txNumero" name="numero"
                            <?=(!isNullOrEmpty($numero)) ? "value=\"{$numero}\"" : "";?>/>
                    </div>
                    <div class="col-md-8">
                        <label for="txItem">Item</label>
                        <input type="text" class="form-control cpoHabilitar input-lg" id="txItem" name="item"
                            <?=(!isNullOrEmpty($item)) ? "value=\"{$item}\"" : "";?>/>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="txParecer">Parecer</label>
                        <input type="text" class="form-control cpoLimpar input-lg" id="txParecer" name="parecer" maxlength="250"
                            <?=(!isNullOrEmpty($parecer)) ? "value=\"{$parecer}\"" : "";?>/>
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

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->

<script src="../vistoria/vsFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparFormulario").click(function(){
            limparCampos();
            $("#txParecer").change();
        });

        // $("#buscarCadastro").click($("#formularioCadastro").collapse());
        $("#retornarFormulario").click(function(){ window.location.href = "../vistoria/vsManterResultados.php?idPlano="+$("#hdIdPlano").val(); });

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
        });
        
        // Adequações para a pesquisa
        var pesquisaOrdem = await valorCookie($('#hdSiglaAeroporto').val()+'_vsMRI_ordenacao');
        var pesquisaFiltro = " " + await valorCookie($('#hdSiglaAeroporto').val()+'_vsMRI_filtro');
        var pesquisaDescricao = await valorCookie($('#hdSiglaAeroporto').val()+'_vsMRI_descricao');

        // Adequações para o cadastro
        pesquisaFiltro = (!isEmpty($("#hdAeroporto").val()) ? " AND vp.idAeroporto = "+$("#hdAeroporto").val()+" AND va.id = "+$("#hdId").val(): "");
        pesquisaDescricao = (!isEmpty($("#hdAeroporto").val()) ? ' <br>Aeroporto : '+$("#hdNomeAeroporto").val() : '');
        habilitarCampos(true);

        // Verifica o agendamento já foi executado para pegar os itens do resultado ou do plano
        base = (!isEmpty($("#hdExecucao").val()) ? 'VistoriaItensResultados' : 'VistoriaItensAgendamentos')
        await vsCarregarVistoriaParecer('Cadastrar', base, pesquisaFiltro, pesquisaOrdem, pesquisaDescricao, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));

        // Obtém todos os TD da tabela e adiciona o evento de click 
        const tds = document.querySelectorAll('#mapa td');
        tds.forEach(td => {
            td.addEventListener('click', function() {
                const elemento = document.getElementById(this.id).classList.toggle('local');
            });
        });

        // Salvar
        $("#salvarMapa").click( async function(){ obterMapa(); });
        async function obterMapa() {
            const ids = [];
            const tds = document.querySelectorAll('#mapa td');
            tds.forEach(td => {
                if (document.getElementById(td.id).classList.contains('local')) {
                    ids.push(td.id);
                }
            });
            $("#hdLocal").val(ids);
            await criarCookie($('#hdSiglaAeroporto').val()+'_vsMRI_local', ids);
        };
    });
</script>