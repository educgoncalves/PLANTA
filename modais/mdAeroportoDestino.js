// *************************************************** 
// Modal Cadastro Rápido - AEROPORTO DESTINO 
// *************************************************** 
// Receber o SELETOR da janela modal
const mdDestino = new bootstrap.Modal(document.getElementById("mdDestino"));
// Receber o SELETOR do formulário da janela modal 
const mdDestinoFormulario = document.getElementById("mdDestinoFormulario");
// Receber o SELETOR do botão da janela modal 
const mdDestinoBotao = document.getElementById("mdDestinoBotao");
// Receber o SELETOR do mensagem da janela modal 
const mdDestinoMensagem = document.getElementById("mdDestinoMensagem");

// Formata os campos de digitação
$(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
$("#dtxIata").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
$("#dtxIcao").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});

// Somente acessa o IF quando existir o SELETOR "mdDestinoFormulario"
if (mdDestinoFormulario) {
    // Aguardar o usuario clicar no botao cadastrar
    mdDestinoFormulario.addEventListener("submit", async (e) => {
        // Não permitir a atualização da pagina
        e.preventDefault();
        // Receber os dados do formulário
        const dadosForm = new FormData(mdDestinoFormulario);

        // Chamar o arquivo PHP responsável em salvar o novo registro
        const dados = await fetch("../modais/mdAeroportoDestinoIncluir.php", {
            method: "POST",
            body: dadosForm
        });
        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();
        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta['status']) {
            // Enviar a mensagem para o HTML
            mdDestinoMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){ mdDestinoMensagem.innerHTML = ""; },3000);
        } else {
            //********************************************************* */
            // Se for PESQUISA
            //********************************************************* */
            var txStsDestino = document.getElementById("txStsDestino");
            var idStsDestino = document.getElementById("idStsDestino");
            txStsDestino.value = resposta['descricao'];
            idStsDestino.value = resposta['id'];
            //********************************************************* */

            // Enviar a mensagem para o HTML, limpar o formulário e fechar a modal
            mdDestinoMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){
                mdDestinoMensagem.innerHTML = "";
                mdDestinoFormulario.reset();
                mdDestino.hide();
                var sobreposto = document.getElementById('mdDestinoSobreposto').value;
                var mdDestinoSobreposto = new bootstrap.Modal(document.getElementById(sobreposto));
                mdDestinoSobreposto.show();
            },1000);
        }
    });
}
// ***************************************************************************************************
