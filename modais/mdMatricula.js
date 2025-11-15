// *************************************************** 
// Modal Cadastro Rápido - MATRICULA 
// *************************************************** 
// Receber o SELETOR da janela modal
const mdMatricula = new bootstrap.Modal(document.getElementById("mdMatricula"));
// Receber o SELETOR do formulário da janela modal 
const mdMatriculaFormulario = document.getElementById("mdMatriculaFormulario");
// Receber o SELETOR do botão da janela modal 
const mdMatriculaBotao = document.getElementById("mdMatriculaBotao");
// Receber o SELETOR do mensagem da janela modal 
const mdMatriculaMensagem = document.getElementById("mdMatriculaMensagem");

// Formata os campos de digitação
$(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });

// Somente acessa o IF quando existir o SELETOR "mdMatriculaFormulario"
if (mdMatriculaFormulario) {
    // Aguardar o usuario clicar no botao cadastrar
    mdMatriculaFormulario.addEventListener("submit", async (e) => {
        // Não permitir a atualização da pagina
        e.preventDefault();
        // Receber os dados do formulário
        const dadosForm = new FormData(mdMatriculaFormulario);

        // Chamar o arquivo PHP responsável em salvar o novo registro
        const dados = await fetch("../modais/mdMatriculaIncluir.php", {
            method: "POST",
            body: dadosForm
        });
        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();
        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta['status']) {
            // Enviar a mensagem para o HTML
            mdMatriculaMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){ mdMatriculaMensagem.innerHTML = ""; },3000);
        } else {
            //********************************************************* */
            // Se for PESQUISA
            //********************************************************* */
            var txStsMatricula = document.getElementById("txStsMatricula");
            var idStsMatricula = document.getElementById("idStsMatricula");
            txStsMatricula.value = resposta['descricao'];
            idStsMatricula.value = resposta['id'];
            //********************************************************* */

            // Enviar a mensagem para o HTML, limpar o formulário e fechar a modal
            mdMatriculaMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){
                mdMatriculaMensagem.innerHTML = "";
                mdMatriculaFormulario.reset();
                mdMatricula.hide();
                var sobreposto = document.getElementById('mdMatriculaSobreposto').value;
                var mdMatriculaSobreposto = new bootstrap.Modal(document.getElementById(sobreposto));
                mdMatriculaSobreposto.show();
            },1000);
        }
    });
}
// ***************************************************************************************************
