<?php
header("Content-Type: text/html; charset=UTF-8",true);
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../suporte/suClassUpload.php");
require_once("../credenciamento/crModal.php");
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

// Montando os cookies
$idEmpresa = carregarCookie($siglaAeroporto.'_crCC_idEmpresa');
$nomeEmpresa = carregarCookie($siglaAeroporto.'_crCC_nomeEmpresa');

// Verificar se foi enviando dados via POST ou inicializa as variáveis
$limparCampos = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPessoa = carregarPosts('idPessoa'); 
    $nome = carregarPosts('nome');
    $documento = carregarPosts('documento');
    $endereco = carregarPosts('endereco');
    $bairro = carregarPosts('bairro');
    $email = carregarPosts('email');
    $telefone = carregarPosts('telefone');
    $cargo = carregarPosts('cargo');
    $responsavel = carregarPosts('responsavel','NAO');
    $credencial = carregarPosts('credencial');
    $area = carregarPosts('area');
    $imagem = carregarPosts('imagem','default.png');
    $validade = carregarPosts('validade');
    $situacao = carregarPosts('situacao','ATV');
    $fotoImagem = "";
    $fotoTipo = "";
    $fotoCredencial = "";

    // Verifica se o request é invalido (F5) não executa nenhum evento, só exibe o formulario
    if (!verificaRequest('')) { goto formulario; }
        
} else  {
    $idPessoa = carregarGets('idPessoa'); 
    $limparCampos = true;
}


// Salvando as informações
if ($evento == "salvarPessoas" && empty($erros)) {
    try {
        $conexao = conexao();
        $dtValidade = mudarDataAMD($validade);
        if ($idPessoa != "") {
            $comando = "UPDATE gear_pessoas_credenciadas SET idEmpresa = ".$idEmpresa.", nome = '".$nome."', documento = '".$documento."', endereco = '".
                        $endereco."', bairro = '".$bairro."', email = '".$email."', telefone = '".$telefone."', cargo = '".$cargo."', responsavel = '".
                        $responsavel."', idArea = ".$area.", validade = ".$dtValidade.", situacao = '".$situacao."', cadastro = UTC_TIMESTAMP() WHERE id = ".$idPessoa;
        } else {
            $comando = "INSERT INTO gear_pessoas_credenciadas (idEmpresa, nome, documento, endereco, bairro, email, telefone, cargo, responsavel, idArea, validade, situacao, cadastro) VALUES (".
                        $idEmpresa.", '".$nome."', '".$documento."', '".$endereco."', '".$bairro."', '".$email."', '".$telefone."', '".
                        $cargo."', '".$responsavel."', ".$area.", ".$dtValidade.", '".$situacao."', UTC_TIMESTAMP())";
        }
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()) {
            if ($sql->rowCount() > 0) {
                gravaDLog("gear_pessoas_credenciadas", ($idPessoa != "" ? "Alteração" : "Inclusão"), $_SESSION['plantaAeroporto'], 
                            $_SESSION['plantaUsuario'], ($idPessoa != "" ? $idPessoa  : $conexao->lastInsertId()), $comando);                
                montarMensagem("success",array("Registro ".($idPessoa != "" ? "alterado" : "incluído")." com sucesso!"));
                $idPessoa = null;
                $limparCampos = true;
            } else {
                throw new PDOException("Não foi possível efetivar esta ".($idPessoa != "" ? "alteração" : "inclusão")."!");
            }
        } else {
            throw new PDOException("Não foi possível ".($idPessoa != "" ? "alterar" : "incluir")." este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Modal
// Ativando a chamada do modal pelo javascript para a Inclusão ou Alteração dos credenciados
//
$modalPessoasCredenciadas = "";
if ($evento == "incluirPessoas" && $idPessoa == "") {
    $modalPessoasCredenciadas = "incluir";
    $limparCampos = true;
}

// Recuperando as informações
if ($evento == "alterarPessoas" && $idPessoa != "") {
    try {
        $conexao = conexao();
        $comando = "SELECT pcr.id, pcr.idEmpresa, pcr.nome, pcr.documento, pcr.endereco, pcr.bairro, pcr.email, pcr.telefone, pcr.cargo,
                        pcr.responsavel, pcr.credencial, pcr.idArea, DATE_FORMAT(pcr.validade,'%Y-%m-%d') as validade, pcr.situacao,
                        CONCAT(ae.iata,'_pes_',pcr.id) as imagem
                    FROM gear_pessoas_credenciadas pcr 
                    LEFT JOIN gear_empresas em ON em.id = pcr.idEmpresa
					LEFT JOIN gear_aeroportos ae ON ae.id = em.idAeroporto	
                    WHERE pcr.id = ".$idPessoa;
        $sql = $conexao->prepare($comando);  
        if ($sql->execute()) {
            $registros = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($registros as $dados) {
                $idEmpresa = $dados['idEmpresa'];
                $nome = $dados['nome'];
                $documento = $dados['documento'];
                $endereco = $dados['endereco'];
                $bairro = $dados['bairro'];
                $email = $dados['email'];
                $telefone = $dados['telefone'];
                $cargo = $dados['cargo'];
                $responsavel = $dados['responsavel'];
                $credencial = $dados['credencial'];
                $area = $dados['idArea'];
                $imagem = $dados['imagem'];
                $validade = $dados['validade'];
                $situacao = $dados['situacao'];
            }
            $limparCampos = false;
            $modalPessoasCredenciadas = "alterar";
        } else {
            throw new PDOException("Não foi possível recuperar este registro!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Excluindo as informações
if ($evento == "excluirPessoas" && $idPessoa != "") {
    try {
        $conexao = conexao();
        $comando = "DELETE FROM gear_pessoas_credenciadas WHERE id = ".$idPessoa;
        $sql = $conexao->prepare($comando); 
        if ($sql->execute()){
            gravaDLog("gear_pessoas_credenciadas", "Exclusão", $_SESSION['plantaAeroporto'], $_SESSION['plantaUsuario'], $idPessoa, $comando);   
            montarMensagem("success",array("Registro excluído com sucesso!"));
            $idPessoa = null;
            $limparCampos = true;
    } else {
        throw new PDOException("Não foi possível excluir este registro!");
    } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$comando);
    }
}

// Exibindo/Upload da foto
$fotoImagem = "";
$fotoTipo = "";
$fotoCredencial = "";
if ($evento == "foto") {
    $fotoImagem = $_REQUEST["imagem"];
    $fotoTipo = $_REQUEST["tipo"];
    $fotoCredencial = $_REQUEST["credencial"];
}
if ($evento == "uploadFoto") {
    $fotoAtual = carregarPosts('fotoAtual');
    if(($_FILES['fotoNova']['error'] == 0) && !empty($fotoAtual)) {
        $upload = new UploadImagem();
        $upload->width = 500;
        $upload->height = 500;
        $msg = $upload->salvar("../arquivos/credenciamentos/", $_FILES['fotoNova'], $fotoAtual);
        montarMensagem($msg['tipo'],array($msg['mensagem']));
    } else {
        montarMensagem("danger",array('Upload não pode ser executado!'));
    }
}

// Limpeza dos campos 
if ($limparCampos == true) {
    $nome = null;
    $documento = null;
    $endereco = null;
    $bairro = null;
    $email = null;
    $telefone = null;
    $cargo = null;
    $responsavel = null;
    $credencial = null;
    $area = null;
    $imagem = 'default.png';
    // Data local do aeroporto
    $validade = dateTimeUTC($utcAeroporto)->format('Y-m-d');
    $situacao = 'ATV';
}

// Ponto para exibição do formulário
formulario:
metaTagsBootstrap('');
$titulo = "Cadastrar Credenciados";
?>
<head>
    <title><?php echo $_SESSION['plantaSistema']." - ".$titulo?></title>
</head>
<body>
<?php require_once("../menu/menuPrincipal.php");?>

<div id="container">
    <?php destacarCabecalho("Aeroporto : ".$nomeAeroporto); ?>    
    <div class="container alert alert-padrao" >
        <h4><?php echo $titulo; ?></h4>
        <form action="?evento=incluirPessoas&idPessoa=" method="POST"  class="form-group" autocomplete="off">
	    	<div class="form-group">
                <!-- Campos hidden -->
                <!--***************************************************************** -->
                <input type="hidden" id="hdSiglaAeroporto" <?="value=\"{$siglaAeroporto}\"";?>/>
                <input type="hidden" id="hdAeroporto" <?="value=\"{$aeroporto}\"";?>/>
                <input type="hidden" id="hdNomeAeroporto" <?="value=\"{$nomeAeroporto}\"";?>/>
                <input type="hidden" id="hdEmpresa" <?="value=\"{$idEmpresa}\"";?>/>
                <input type="hidden" id="hdPagina" <?="value=\"{$_page}\"";?>/>
                <input type="hidden" id="hdPaginacao" <?="value=\"{$_paginacao}\"";?>/>
                <input type="hidden" id="hdLimite" <?="value=\"{$_limite}\"";?>/>
                <!--***************************************************************** -->
                <div class="row">  
                    <div class ="col-lg-10">                
                        <div class="col-md-6">
                            <label for="slEmpresa">Empresa</label>
                            <select class="form-select input-lg" id="slEmpresa" name="slEmpresa" onchange="$('#buscarCredenciados').trigger('click');">
                            </select>
                        </div>
                    </div>
                    <div class ="col-lg-2">     
                        <div class="row pt-2 px-2">
                            <input type="button" class="btn btn-padrao" id="buscarCredenciados" value="Buscar Credenciados"/>
                        </div>
                        <div class="row pt-2 px-2">
                            <input type="submit" class="btn btn-padrao" id="incluirPessoas" value="Incluir Pessoas"/>
                        </div>   
                        <div class="row pt-2 px-2">
                            <input type="submit" class="btn btn-padrao" id="incluirVeiculos" value="Incluir Veículos"/>
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
    <?php botaoFoto(); telaFoto($fotoImagem,$fotoTipo,$fotoCredencial);?>
</div>

<!-- 
    Tela EDITAR CREDENCIADO - PESSOAS
-->
<button id="botaoPessoas" style="display:none" type="button" class="btn btn-link btn-lg" data-bs-toggle="modal" data-bs-target="#editPessoasModal">Pessoas Credenciadas</button>

<div class="modal fade" id="editPessoasModal" tabindex="-1" aria-labelledby="editPessoasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header alert alert-padrao">
                <h5 class="modal-title" id="editPessoasModalLabel"><?php echo $nomeEmpresa.' - Pessoas Credenciadas'?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
            </div>
            <div class="modal-body">
                <form action="?evento=salvarPessoas" method="POST" class="form-group" id="editPessoasModal" onsubmit="camposObrigatorios(this); return false;">
                    <!-- Campos hidden -->
                    <input type="hidden" class="cpoLimpar" id="hdIdPessoa" name="idPessoa" <?="value=\"{$idPessoa}\"";?>/>
                    <input type="hidden" class="cpoLimpar" id="hdArea" <?="value=\"{$area}\"";?>/>
                    <input type="hidden" class="cpoLimpar" id="hdResponsavel" <?="value=\"{$responsavel}\"";?>/>
                    <input type="hidden" class="cpoLimpar" id="hdSituacao" <?="value=\"{$situacao}\"";?>/>
                    <!--***************************************************************** -->
                    <div class="row">
                        <div class ="col-lg-8"> 
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <label for="txCredencial">Credencial</label>
                                    <input type="text" class="form-control caixaAlta input-lg" id="txCredencial" name="credencial" readonly
                                        <?php echo (!isNullOrEmpty($credencial)) ? "value=\"{$credencial}\"" : "";?>/>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label for="txNome">Nome</label>
                                    <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txNome" name="nome"
                                        <?php echo (!isNullOrEmpty($nome)) ? "value=\"{$nome}\"" : "";?>/>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label for="txDocumento">Documento</label>
                                    <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txDocumento" name="documento"
                                        <?php echo (!isNullOrEmpty($documento)) ? "value=\"{$documento}\"" : "";?>/>
                                </div>
                                <div class="col-md-6">
                                    <label for="txCargo">Cargo</label>
                                    <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txCargo" name="cargo"
                                        <?php echo (!isNullOrEmpty($cargo)) ? "value=\"{$cargo}\"" : "";?>/>
                                </div>  
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label for="slResponsavel">Responsável</label>
                                    <select class="form-select cpoObrigatorio selLimpar input-lg" id="slResponsavel" name="responsavel">
                                    </select> 
                                </div> 
                            </div>
                        </div>
                        <div class ="col-lg-4">
                            <div class='my-2 mx-2 rounded img-fluid img-thumbnail'>
                            <?php
                                echo "<object data='../arquivos/credenciamentos/".$imagem."?nocache=".time()."' type='image/jpg' style='height:100%; width:100%'>";
                                echo "<img src='../arquivos/credenciamentos/default.png?nocache=".time()."' style='height:100%; width:100%'/></object>";
                             ?> 
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <label for="txEndereco">Endereço</label>
                            <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txEndereco" name="endereco"
                                <?php echo (!isNullOrEmpty($endereco)) ? "value=\"{$endereco}\"" : "";?>/>
                        </div>
                        <div class="col-md-4">
                            <label for="txBairro">Bairro</label>
                            <input type="text" class="form-control cpoObrigatorio cpoLimpar caixaAlta input-lg" id="txBairro" name="bairro"
                                <?php echo (!isNullOrEmpty($bairro)) ? "value=\"{$bairro}\"" : "";?>/>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <label for="txEmail">Email</label>
                            <input type="email" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txEmail" name="email" autocomplete="true"
                                <?php echo (!isNullOrEmpty($email)) ? "value=\"{$email}\"" : "";?>/>
                        </div>                            
                        <div class="col-md-4">
                            <label for="txTelefone">Telefone</label>
                            <input type="text" class="form-control cpoObrigatorio cpoLimpar phone" id="txTelefone" name="telefone" maxlength="15"
                                <?php echo (!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>
                        </div>  
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label for="slArea">Área</label>
                            <select class="form-select cpoObrigatorio selLimpar input-lg" id="slArea" name="area">
                            </select> 
                        </div>
                        <div class="col-md-4">
                            <label for="txValidade">Validade</label></th>
                            <input type="date" class="form-control cpoLimpar input-lg" id="txValidade" name="validade"
                                <?php echo (!isNullOrEmpty($validade)) ? "value=\"{$validade}\"" : "";?>>
                        </div>
                        <div class="col-md-4">
                                    <label for="slSituacao">Situação</label>
                                    <select class="form-select cpoObrigatorio selLimpar input-lg" id="slSituacao" name="situacao">
                                    </select> 
                        </div>
                    </div>
                    <br>
                    <div class="form-inline">
                        <input type="button" class="btn btn-padrao" id="limparPessoas" value="Limpar"/>
                        <input type="submit" class="btn btn-padrao" id="salvarPessoas" value="Salvar"/>
                    </div>
                </form>    
            </div>
        </div>
    </div>
</div>

<!-- *************************************************** -->

<?php fechamentoMenuLateral();?>
<?php fechamentoHtml(); ?>
<!-- *************************************************** -->   

<script src="../credenciamento/crFuncoes.js"></script>
<script src="../suporte/suFuncoes.js"></script>
<script>
    $(async function() {
        $("#limparPessoas").click(function(){
            limparCampos();
            $("#txNome").focus();
        });

        $(".caixaAlta").keyup(function(){
            $(this).val($(this).val().toUpperCase());
        });
        
        // Configurando o formulario
        await crCarregarSelectEmpresas('#slEmpresa', $('#hdEmpresa').val(), ' AND em.idAeroporto = '+$('#hdAeroporto').val(), 'Cadastrar');
        if (!isEmpty($("#slEmpresa").val()) || !isEmpty($("#hdEmpresa").val())) {
            exibirCredenciados('Cadastrar', $("#slEmpresa").val());
        }
        $("#txNome").focus();

        // Chamando modal
        var modalPessoasCredenciadas = "<?php echo $modalPessoasCredenciadas;?>";
        if (modalPessoasCredenciadas != "") {
            await suCarregarSelectTodas('Recursos','#slArea', $('#hdArea').val(), " AND re.tipo = 'ARA' AND re.situacao = 'ATV' AND re.idAeroporto = "+$('#hdAeroporto').val(),'Cadastrar');
            await suCarregarSelectTodos('TodosSimNao','#slResponsavel', $('#hdResponsavel').val(),'','Cadastrar');
            await suCarregarSelectTodas('TodosSituacao','#slSituacao', $('#hdSituacao').val(),'','Cadastrar');
            readonlyCampos(true);
            $('#botaoPessoas').trigger('click');
            $('#txNome').focus();
        }

        // Inibe os botões caso a empresa não esteja selecionada
        $("#buscarCredenciados").attr("disabled", isEmpty($("#slEmpresa").val()));
        $("#incluirPessoas").attr("disabled", isEmpty($("#slEmpresa").val()));
        $("#incluirVeiculos").attr("disabled", isEmpty($("#slEmpresa").val()));

        $("#buscarCredenciados").click(function(){
            $("#buscarCredenciados").attr("disabled", false);
            $("#incluirPessoas").attr("disabled", false);
            $("#incluirVeiculos").attr("disabled", false);
            exibirCredenciados('Cadastrar', $("#slEmpresa").val());
        });

        // Chamando modal
        var fotoImagem = "<?php echo $fotoImagem;?>";
        if (fotoImagem != "") {
            $('#botaoFoto').trigger('click');
        }
    });

    var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }, options = { onKeyPress: function (val, e, field, options) {
                    field.mask(behavior.apply({}, arguments), options);}};
    $('.phone').mask(behavior, options);

    // Funcao para exibir os credenciados
    //
    async function exibirCredenciados(funcao, empresa){
        // Montagem dos filtros
        var vetEmpresa = empresa.split("#");
        var filtro = ' AND pcr.idEmpresa = '+vetEmpresa[0];
                
        // Montagem da ordem
        var ordem = '';
        
        // Montagem da tag descrição do filtro
        var descricaoFiltro = '';
        await criarCookie($('#hdSiglaAeroporto').val()+'_crCC_idEmpresa', vetEmpresa[0]);
        await criarCookie($('#hdSiglaAeroporto').val()+'_crCC_nomeEmpresa', vetEmpresa[1]);
        // await crCarregarPessoasCredenciadas('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
        await crCarregarCredenciados('Cadastrar', filtro, ordem, descricaoFiltro, parseInt($('#hdPagina').val()), parseInt($('#hdLimite').val()));
    }
</script>