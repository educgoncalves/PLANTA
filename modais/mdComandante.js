// *************************************************** 
// Modal Cadastro Rápido - COMANDO 
// *************************************************** 
// Receber o SELETOR da janela modal
const mdComando = new bootstrap.Modal(document.getElementById("mdComando"));
// Receber o SELETOR do formulário da janela modal 
const mdComandoFormulario = document.getElementById("mdComandoFormulario");
// Receber o SELETOR do botão da janela modal 
const mdComandoBotao = document.getElementById("mdComandoBotao");
// Receber o SELETOR do mensagem da janela modal 
const mdComandoMensagem = document.getElementById("mdComandoMensagem");

// Formata os campos de digitação
$(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
$("#mtxTelefone").mask('+YY YY YYYYY-YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
$("#mtxCodigoAnac").mask('YYYYYY', {'translation': {Y: {pattern: /[0-9]/}}});

// Somente acessa o IF quando existir o SELETOR "mdComandoFormulario"
if (mdComandoFormulario) {
    // Aguardar o usuario clicar no botao cadastrar
    mdComandoFormulario.addEventListener("submit", async (e) => {
        // Não permitir a atualização da pagina
        e.preventDefault();
        // Receber os dados do formulário
        const dadosForm = new FormData(mdComandoFormulario);

        // Chamar o arquivo PHP responsável em salvar o novo registro
        const dados = await fetch("../modais/mdComandanteIncluir.php", {
            method: "POST",
            body: dadosForm
        });
        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();
        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta['status']) {
            // Enviar a mensagem para o HTML
            mdComandoMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){ mdComandoMensagem.innerHTML = ""; },3000);
        } else {
            //********************************************************* */
            // Se for PESQUISA
            //********************************************************* */
            var txStsCmpComando = document.getElementById("txStsCmpComando");
            var idStsCmpComando = document.getElementById("idStsCmpComando");
            txStsCmpComando.value = resposta['descricao'];
            idStsCmpComando.value = resposta['id'];
            //********************************************************* */

            // Enviar a mensagem para o HTML, limpar o formulário e fechar a modal
            mdComandoMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){
                mdComandoMensagem.innerHTML = "";
                mdComandoFormulario.reset();
                mdComando.hide();
                var sobreposto = document.getElementById('mdComandoSobreposto').value;
                var mdComandoSobreposto = new bootstrap.Modal(document.getElementById(sobreposto));
                mdComandoSobreposto.show();
            },1000);
        }
    });
}
// ***************************************************************************************************
