<?php 
require_once("../suporte/suConexao.php");
require_once("../menu/meFuncoes.php");
verificarExecucao();
verificarConexao($_SESSION['plantaIDAeroporto'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaIPCliente']);
verificarConexaoAtiva($_SESSION['plantaIDAeroporto'], $_SESSION['plantaSistema'], $_SESSION['plantaUsuario'], $_SESSION['plantaIPCliente']);

// Modal
// Ativando a chamada do modal pelo javascript para a troca do aeroporto
//
$modalAeroporto = "";
if (carregarGets('evento','') == "modalAeroporto") {
    $modalAeroporto = "modalAeroporto";
}
?>

<!-- *************************************************** -->
<!-- Modal SOBRE -->
<!-- *************************************************** -->
<div class="modal fade" id="sobre" tabindex="-1" aria-labelledby="sobreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sobreLabel">Sobre o software</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img src="../ativos/img/decola+.png">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <!-- <h5>Analistas Responsáveis</h5>
                        <p class="mt-3">João Teixeira - (61) 8137-3206</p>
                        <p class="mb-0">Eduardo Gonçalves - (21) 98755-8797</p>  -->
                        <p class="mt-5">Software desenvolvido por equipe própria</p> 
                        <p class="mt-3">Contatos através de e-mail: suporte@decolamais.com.br</p> 
                        <p class="mt-5 mb-3 text-body-secondary">&copy; 2024–2025</p>
                        <a href="http://icons8.com.br" target="_blank">icons by icons8</a> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- *************************************************** -->

<!-- *************************************************** -->
<!-- Modal TROCA DE AEROPORTO -->
<!-- *************************************************** -->
<button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalAeroporto" style="display:none" id="botaoModalAeroporto"></button>
<input type="button" class="btn btn-success btn-lg" id="trocarAeroporto" style="display:none" value="Trocar Aeroporto"/>

<div class="modal fade" id="modalAeroporto" tabindex="-1" aria-labelledby="aeroportoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aeroportoLabel">Escolha um dos aeroportos que você tem acesso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row text-left">
                    <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" id="mn_slAeroporto" name="mn_slAeroporto" onchange="$('#trocarAeroporto').trigger('click');">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal" id="fecharTrocarAeroporto">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- *************************************************** -->

<?php 
    montagemMenu($_SESSION['plantaSistema'],$_SESSION['plantaIDAeroporto'],$_SESSION['plantaGrupo'],$_SESSION['plantaIDUsuario']); 
    exibirMensagem();
?>

<script src="../administracao/adFuncoes.js"></script>
<script>
    // Para recolher ou expandir o menu lateral
    const menu = document.querySelector(".menu");
    document.querySelector("#sidebar").classList.toggle("collapsed");
    menu.addEventListener("click",function(){
        document.querySelector("#sidebar").classList.toggle("collapsed");
    });

    // Para esconder a barra de atalhos
    document.getElementById("atalhos").style.display = "none";
    document.getElementById("graficos").style.display = "none";
</script>

<script>
    $(async function() {
        // Chamando modal
        var modalAeroporto = "<?php echo $modalAeroporto; ?>";
        var usID = "<?php echo $_SESSION['plantaIDUsuario']; ?>";
        var usSistema = "<?php echo $_SESSION['plantaSistema']; ?>";
        var usAeroporto = "<?php echo $_SESSION['plantaAeroporto']; ?>";
        var modalAeroporto = "<?php echo $modalAeroporto;?>";
        if (modalAeroporto != "") {
            await adCarregarSelectAeroportosAcessados('#mn_slAeroporto', usAeroporto, " AND ac.sistema = '"+usSistema+"' AND ac.idUsuario = "+usID, 'Cadastrar');
            $('#botaoModalAeroporto').trigger('click');
        }
        
        $("#trocarAeroporto").click(function(){
            $('#fecharTrocarAeroporto').trigger('click');
            window.location.href = "../menu/menu.php?aeroporto="+$("#mn_slAeroporto").val();
        });
    });

    // Define um temporizador para fechar a mensagem em 10 segundos
    $(document).ready(function() { setTimeout(() => { $('#mensagem .btn-close').click(); }, 10000); });

    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona todos os links que têm o atributo data-target
        const links = document.querySelectorAll('a[data-target]');

        // Adiciona um "escutador de eventos" para cada link
        links.forEach(link => {
            link.addEventListener('click', function(e) {
            // Impede o comportamento padrão do link (o redirecionamento)
            e.preventDefault();

            // Pega o ID do formulário a partir do atributo data-target
            const targetId = this.getAttribute('data-target');

            // Seleciona o formulário correspondente
            const targetForm = document.getElementById(targetId);

            // Se o formulário existir, ele é submetido
            if (targetForm) {
                targetForm.submit();
            }
            });
        });
    });
</script>