// ***************************************************
// Modal Cadastro Rápido - AEROPORTO ORIGEM
// *************************************************** 
// Receber o SELETOR da janela modal
const mdOrigem = new bootstrap.Modal(document.getElementById("mdOrigem"));
// Receber o SELETOR do formulário da janela modal 
const mdOrigemFormulario = document.getElementById("mdOrigemFormulario");
// Receber o SELETOR do botão da janela modal 
const mdOrigemBotao = document.getElementById("mdOrigemBotao");
// Receber o SELETOR do mensagem da janela modal 
const mdOrigemMensagem = document.getElementById("mdOrigemMensagem");

// Formata os campos de digitação
$(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
$("#mtxIata").mask('YYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/},}});
$("#mtxIcao").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z0-9]/}}});

// Somente acessa o IF quando existir o SELETOR "mdOrigemFormulario"
if (mdOrigemFormulario) {
    // Aguardar o usuario clicar no botao cadastrar
    mdOrigemFormulario.addEventListener("submit", async (e) => {
        // Não permitir a atualização da pagina
        e.preventDefault();
        // Receber os dados do formulário
        const dadosForm = new FormData(mdOrigemFormulario);

        // Chamar o arquivo PHP responsável em salvar o novo registro
        const dados = await fetch("../modais/mdAeroportoOrigemIncluir.php", {
            method: "POST",
            body: dadosForm
        });
        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();
        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta['status']) {
            // Enviar a mensagem para o HTML
            mdOrigemMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){ mdOrigemMensagem.innerHTML = ""; },3000);
        } else {
            //********************************************************* */
            // Se for PESQUISA
            //********************************************************* */
            var txStsOrigem = document.getElementById("txStsOrigem");
            var idStsOrigem = document.getElementById("idStsOrigem");
            txStsOrigem.value = resposta['descricao'];
            idStsOrigem.value = resposta['id'];
            //********************************************************* */

            // Enviar a mensagem para o HTML, limpar o formulário e fechar a modal
            mdOrigemMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){
                mdOrigemMensagem.innerHTML = "";
                mdOrigemFormulario.reset();
                mdOrigem.hide();
                var sobreposto = document.getElementById('mdOrigemSobreposto').value;
                var mdOrigemSobreposto = new bootstrap.Modal(document.getElementById(sobreposto));
                mdOrigemSobreposto.show();
            },1000);
        }
    });
}
// ***************************************************************************************************
