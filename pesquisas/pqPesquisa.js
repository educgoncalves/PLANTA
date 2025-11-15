//
// Função para a PESQUISA de qualquer item
//
async function iniciarPesquisa(tabItem, vlrItem, fltItem = '', cpoIdItem = '', cpoTxItem = '') {
    await executarPesquisa(tabItem, vlrItem, fltItem, cpoIdItem, cpoTxItem);
}

function getIdItem(cpoIdItem, cpoTxItem, id, descricao) {
    document.getElementById(cpoIdItem).value = id;
    document.getElementById(cpoTxItem).value = descricao;
}

async function finalizarPesquisa(tabItem, cpoIdItem = '', cpoTxItem = '') {
    // Preparando nome dos campos dos formularios
    cpoIdItem = (cpoIdItem == '' ? "id"+tabItem : cpoIdItem);
    cpoTxItem = (cpoTxItem == '' ? "tx"+tabItem : cpoTxItem);

    if (document.getElementById(cpoIdItem).value === "" || document.getElementById(cpoTxItem).value === "") {
        getIdItem(cpoIdItem,cpoTxItem,"","");
    }
    document.getElementById("span"+cpoTxItem).innerHTML = "";
}

async function executarPesquisa(tabItem, vlrItem, fltItem = '', cpoIdItem = '', cpoTxItem = '') {
    var conteudoHTML = "";

    // Preparando nome dos campos dos formularios
    cpoIdItem = (cpoIdItem == '' ? "id"+tabItem : cpoIdItem);
    cpoTxItem = (cpoTxItem == '' ? "tx"+tabItem : cpoTxItem);

    // Verifica se campos está readonly
    if ($("#"+cpoTxItem).prop('readonly')) { return }

    // Montagem da pesquisa
    if (vlrItem.length >= 0) {

        // Fazer a requisição para o arquivo PHP responsável em recuperar as informações do banco de dados    
        $('.carregando').show();
        const dados = await fetch('../pesquisas/pqPesquisa.php?tabela=' + tabItem + '&chave=' + vlrItem+'&filtro=' + fltItem);
        const resposta = await dados.json();
        $('.carregando').hide();

        // Montar a lista para seleção (position-absolute ou relative)
        conteudoHTML = "<ul class='list-group position-absolute'>"; 

        if (resposta['status']) {
            // Percorrer a lista de informações retornada do banco de dados se tiver mais de uma ocorrência
            // caso contrário assume o registro encontrado
            for (i = 0; i < resposta['dados'].length; i++) {
                // if (resposta['dados'].length > 1) {
                    conteudoHTML += "<li class='list-group-item list-group-item-action' style='cursor: pointer;' onmouseover='getIdItem(" + 
                                    JSON.stringify(cpoIdItem) + "," + JSON.stringify(cpoTxItem) + "," +
                                    JSON.stringify(resposta['dados'][i].id) + "," + JSON.stringify(resposta['dados'][i].descricao) + ")'>" + 
                                    resposta['dados'][i].descricao + "</li>";
                                }
                // } else {
                //     getIdItem(cpoIdItem,cpoTxItem,resposta['dados'][i].id,resposta['dados'][i].descricao);
                // }
                if (resposta['dados'].length == 1) {
                  getIdItem(cpoIdItem,cpoTxItem,resposta['dados'][0].id,resposta['dados'][0].descricao);
            //    } else {
            //     document.getElementById(cpoIdItem).value = "";
            //     document.getElementById(cpoTxItem).value = "";
               }

        } else {
            conteudoHTML += "<li class='list-group-item list-group-item-danger'>" + resposta['msg'] + "</li>";
            setTimeout(function() {
                document.getElementById(cpoIdItem).value = "";
                document.getElementById(cpoTxItem).value = "";
                document.getElementById("span"+cpoTxItem).innerHTML = "";
                }, 1000);
        }
        conteudoHTML += "</ul>";
        document.getElementById("span"+cpoTxItem).innerHTML = conteudoHTML;
    }
}