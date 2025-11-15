// *************************************************** 
// Modal Cadastro Rápido - FORMULARIO
// *************************************************** 
// Receber o SELETOR da janela modal
const mdFormulario = new bootstrap.Modal(document.getElementById("mdFormulario"));
// Receber o SELETOR do formulário da janela modal 
const mdFormularioFormulario = document.getElementById("mdFormularioFormulario");
// Receber o SELETOR do botão da janela modal 
const mdFormularioBotao = document.getElementById("mdFormularioBotao");
// Receber o SELETOR da mensagem da janela modal 
const mdFormularioMensagem = document.getElementById("mdFormularioMensagem");

// Formata os campos de digitação
$(".caixaAlta").keyup(function(){ $(this).val($(this).val().toUpperCase()); });
$("#mtxFormulario").mask('YYYY', {'translation': {Y: {pattern: /[0-9]/},}});
$("#mtxSistema").mask('YYYY', {'translation': {Y: {pattern: /[A-Za-z]/}}});

// Somente acessa o IF quando existir o SELETOR "mdFormularioFormulario"
if (mdFormularioFormulario) {
    // Aguardar o usuario clicar no botao cadastrar
    mdFormularioFormulario.addEventListener("submit", async (e) => {
        // Não permitir a atualização da pagina
        e.preventDefault();
        // Receber os dados do formulário
        const dadosForm = new FormData(mdFormularioFormulario);
        
        // Chamar o arquivo PHP responsável em salvar o novo registro
        const dados = await fetch("../modais/mdFormularioIncluir.php", {
            method: "POST",
            body: dadosForm
        });
        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();
        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta['status']) {
            // Enviar a mensagem para o HTML
            mdFormularioMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){ mdFormularioMensagem.innerHTML = ""; },3000);
        } else {
            //********************************************************* */
            // Se for SELECT
            //********************************************************* */
            // Cria um novo elemento <option>
            var slFormulario = document.getElementById("slFormulario");
            var novoOption = document.createElement("option");
            // Definir os novos valores para o novo <option>
            novoOption.value = resposta['codigo'];
            novoOption.text = resposta['descricao'];
            // Definir o atributo "selected" para tornar este <option> selecionado
            novoOption.setAttribute("selected", "selected");
            // Adiciona o novo <option> ao <select>
            slFormulario.appendChild(novoOption);
            //********************************************************* */
                        
            // Enviar a mensagem para o HTML, limpar o formulário e fechar a modal
            mdFormularioMensagem.innerHTML = resposta['msg'];
            setTimeout(function(){
                mdFormularioMensagem.innerHTML = "";
                mdFormularioFormulario.reset();
                mdFormulario.hide();
            },1000);
         }
    });
}
// ***************************************************************************************************
