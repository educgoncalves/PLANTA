// FUNCOES SUPORTE
//
// Verifica se um campo é nulo, indefinido ou vazio
//
function isEmpty(value) {
    return (value == null || (typeof value === "string" && value.trim().length === 0));
}
//
// Retorna data e hora (dd/mm/aaaa hh:mm:ss)
//
function horaAtual(formato){
    var agora = new Date(); 
    var dia = ("0" + agora.getDate()).slice(-2);
    var mes = ("0" + (agora.getMonth() + 1)).slice(-2);
    var ano = agora.getFullYear();
    var hora = ("0" + agora.getHours()).slice(-2);
    var minuto = ("0" + agora.getMinutes()).slice(-2); 
    var segundo = ("0" + agora.getSeconds()).slice(-2); 
    var retorno = "";
    if (formato == "d/m/Y H:i:s") 
        retorno = dia + "/" + mes + "/" + ano + " " + hora + ":" + minuto + ":" + segundo; 
    if (formato == "Y-m-d")            
        retorno = ano + "-" + mes + "-" + dia; 
    if (formato == "H:i") 
        retorno = hora + ":" + minuto; 
    return retorno;        
}
// Retorna data (aaaa/mm/dd)
//
function mudarDataAMD(date){
    return date.split('/').reverse().join('-');
}
// Retorna data (aaaa/mm/dd)
//
function mudarDataDMA(date){
    return date.split('-').reverse().join('/');
}
// Muda Valor decimal para Mysql
function mudarDecimalMysql(valor){
    return valor.replaceAll('.','').replace(',','.');
}
// Limpar os campos do formulário
//
function limparCampos(){
    apagarCampos(true);
    $(".cpoLimpar").val('');
    $(".numLimpar").val('0');
    $(".selLimpar").prop('selectedIndex', 0);
}
// Limpar os campos do formulário de pesquisa
//
function limparPesquisa(){
    $(".cpoCookie").val('');
    $(".selCookie").prop('selectedIndex', 0);
}
function limparClasse(classe){
    $(".cpo"+classe).val('');
    $(".sel"+classe).prop('selectedIndex', 0);
}
// Apagar ou acender os campos
//
function apagarCampos(operacao){
    $(".cpoApagar").each(function(){
        if (operacao) {
            $(this).css('display','none');
        } else {
            $(this).css('display','block');
        }
    });
}
// Habilitar ou desabilita os campos conforme operação
//
function habilitarCampos(operacao){
    $(".cpoHabilitar").attr('disabled', operacao);
}
// Readonly os campos conforme a operação
//
function readonlyCampos(operacao){
    $(".cpoReadOnly").attr('readonly', operacao);
    $(".cpoDisable").prop('disabled', operacao);
}
// Testar se campos obrigatórios estão preenchidos
//
function camposObrigatorios(frm){
    var erros = 0;
    var msgs = "";
    $(".cpoObrigatorio").each(function(){
        if ($(this).val() == '' || $(this).val() == null) {
            msgs += 'Campo [' + $("label[for='"+$(this).attr('id')+"']").html() + '] deve ser informado!\n';
            //msgs += 'Campo [' + $(this).attr('id')+"']" + '] deve ser informado!\n';
            erros++;
        }
    });
    if(erros>0){
        alert(msgs);
        return false;
    } 
    frm.submit();
}
// Sleep
//
function sleep(milliseconds) {
    return new Promise(resolve => setTimeout(resolve,milliseconds));
}
// Checar campo data
//
function checarData(data) {
    return data instanceof Date && !isNaN(data);
}
// // Excluir todos os cookies
// //
// function excluirTodosCookies() {
// //    var c = document.cookie.split(";");
// //    for (i in c) 
// //     document.cookie = /^[^=]+/.exec(c[i])[0]+"=;expires=Thu, 01 Jan 1970 00:00:00 GMT";    

//         var allCookies = document.cookie.split(';');
        
//        for (var i = 0; i < allCookies.length; i++)
//             document.cookie = allCookies[i] + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";   
//}
function processarCookies(operacao, formulario) {
    $(".cpoCookie").each(function(){
        if (operacao == 'criar') {
            criarCookie(formulario+'_'+$(this).attr('name'), $(this).val())
        } else {
            apagarCookie(formulario+'_'+$(this).attr('name'));
        }
    });
}

// Cria um novo cookie
function criarCookie(nome, valor) {
    var amanha = new Date()
    amanha.setDate(amanha.getDate() + 3);
    document.cookie = nome + '=' + valor + ';' + amanha.toUTCString() + '; path=/';
}

// Apaga o cookie
function apagarCookie(nome){
    var ontem = new Date()
    ontem.setDate(ontem.getDate() - 1);
    document.cookie = nome + '=;' + ontem.toUTCString() + '; path=/';
}

// Obtém o valor de um cookie
function valorCookie(nome) {
    // Obtém todos os cookies do documento
    var cookies = document.cookie;
    var cname = ' ' + nome + '=';
    
    // Verifica se seu cookie existe
    if (cookies.indexOf(cname) == -1) {
        return "";
    }
    
    // Remove a parte que não interessa dos cookies
    cookies = cookies.substr(cookies.indexOf(cname), cookies.length);

    // Obtém o valor do cookie até o ;
    if (cookies.indexOf(';') != -1) {
        cookies = cookies.substr(0, cookies.indexOf(';'));
    }
    
    // Remove o nome do cookie e o sinal de = e retorna
    return cookies.split(cname)[1];
}

// Posiciona a ocorrência de um select
function posicionaSelect(sel,valor) {
    var select = document.querySelector(sel);
    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].value === valor) {
            select.selectedIndex = i;
            break;
        }
    }
}

// Barra de paginacao 
function barraPaginacao(pagina, limite, total){
    var div = "";
    if (total > limite) {
        if (pagina != 0) {
            var anterior = pagina - 1;
            var seguinte = pagina + 1;
            var ultima = Math.ceil( total / limite );
            var inferior = (pagina - 2 >= 1 ? pagina - 2 : 1);
            var superior = (pagina + 2 <= ultima ? pagina + 2 : ultima);

            div = '<nav aria-label="Page navigation"><ul class="pagination justify-content-end">';
            // Todas
            div += 
            '    <li class="page-item"><a class="page-link" href="?page=0&paginacao=SIM" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Exibe todos os '+total+' registros">Todas</a></li>';
            // Primeira
            div += '<li class="page-item"><a class="page-link" href="?page=1&paginacao=SIM" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Primeira página">\<\<</a></li>';
            // Anterior
            if (anterior != 0) {
                div += 
                    // '    <li class="page-item">'+
                    // '      <a class="page-link" href="?page='+anterior+'&paginacao=SIM" aria-label="Anterior">'+
                    // '        <span aria-hidden="true">&laquo</span>'+
                    // '      </a>'+
                    // '    </li>';
                    '    <li class="page-item"><a class="page-link" href="?page='+anterior+'&paginacao=SIM" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Página anterior">\<</a></li>';
            }
            // Intermediarias
            for (let i = inferior; i <= ultima && i <= superior; i++) {
                div += '<li class="page-item'+(i == pagina ? ' active' : '')+'"><a class="page-link" href="?page='+i+'&paginacao=SIM">'+i+'</a></li>';
            }
            // Seguinte 
            if (seguinte <= ultima) {
                div += 
                    // '    <li class="page-item">'+
                    // '      <a class="page-link"href="?page='+seguinte+'&paginacao=SIM" aria-label="Seguinte">'+
                    // '        <span aria-hidden="true">&raquo;</span>'+
                    // '      </a>'+
                    // '    </li>';
                    '    <li class="page-item"><a class="page-link" href="?page='+seguinte+'&paginacao=SIM" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Página seguinte">\></a></li>';
            }
            // Ultima
            div += '<li class="page-item"><a class="page-link" href="?page='+ultima+'&paginacao=SIM" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Última página">\>\></a></li>';
        
            div += '</ul></nav>';            
        }  else {
            //if (total > limite) {
                div = '<nav aria-label="Page navigation"><ul class="pagination justify-content-end">';
                div += '<li class="page-item"><a class="page-link" href="?page=1&paginacao=SIM" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ativa a paginação dos registros">Paginação</a></li>';
                div += '</ul></nav>'; 
            //}
        }        
    }
return div
}

// Formatação CPF/CNPJ
// Utilização
// <input type="text" class="form-control cpoObrigatorio cpoLimpar input-lg" id="txCpfCnpj" name="cpfCnpj"
//      onfocus="javascript: retirarSimbolos(this);" onblur="javascript: formatarCpfCnpj(this);" maxlength="14"
//      <?php echo (!isNullOrEmpty($cpfCnpj)) ? "value=\"{$cpfCnpj}\"" : "";?>/>  
//        
function formatarCpfCnpj(campoTexto) {
    // Retira tudo que não é dígito
    var retorno = campoTexto.value.replace(/\D/g, "");
    // Verifica qual a formatação de acordo com o tamanho do campo
    if (retorno.length == 11) {
        retorno = retorno.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g,"\$1.\$2.\$3\-\$4");
    } else if (retorno.length == 14) {
        retorno = retorno.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g,"\$1.\$2.\$3\/\$4\-\$5");
    } else {
        retorno = "";//retorno.replace(/^(\d*)/, "$1");
    }
    campoTexto.value = retorno;
}
function retirarSimbolos(campoTexto) {
    campoTexto.value = campoTexto.value.replace(/\D/g, "");
}
function mascaraCpf(valor) {
    return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g,"\$1.\$2.\$3\-\$4");
}
function mascaraCnpj(valor) {
    return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g,"\$1.\$2.\$3\/\$4\-\$5");
}

// Formatação Telefone
// Utilização
//  <input type="text" class="form-control cpoLimpar input-lg" id="txTelefone" name="telefone"
//      onfocus="javascript: retirarSimbolos(this);" onblur="javascript: formatarTelefone(this);" maxlength="11"
//      <?php echo (!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>  
// 
function formatarTelefone(campoTexto) {
    // Retira tudo que não é dígito
    var retorno = campoTexto.value.replace(/\D/g, "");
    // Retira o primeiro zero
    retorno = retorno.replace(/^0/, "");
    // Verifica qual a formatação de acordo com o tamanho do campo
    if (retorno.length == 11) {
        retorno = retorno.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (retorno.length == 10) {
        retorno = retorno.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else {
        retorno = ""; //retorno.replace(/^(\d*)/, "($1");
    }
    campoTexto.value = retorno;
}

// Formatação CEP
// Utilização
//  <input type="text" class="form-control cpoLimpar input-lg" id="txTelefone" name="telefone"
//      onfocus="javascript: retirarSimbolos(this);" onblur="javascript: formatarTelefone(this);" maxlength="11"
//      <?php echo (!isNullOrEmpty($telefone)) ? "value=\"{$telefone}\"" : "";?>/>  
// 
function formatarCEP(campoTexto) {
    // Retira tudo que não é dígito
    var retorno = campoTexto.value.replace(/\D/g, "");
    // Verifica qual a formatação de acordo com o tamanho do campo
    if (retorno.length == 8) {
        retorno = retorno.replace(/^(\d{5})(\d{3}).*/, "$1-$2");
    } else {
        retorno = ""; //retorno.replace(/^(\d*)/, "($1");
    }
    campoTexto.value = retorno;
}

// Limpa os parametros de query string da url
function urlSemQueryString(pagina) {
    var index = pagina.indexOf("?");
    return (index != -1 ? pagina.slice(0,index) : pagina);
}

// Mensagem de erro padronizada para o Javascript
function exibirErro(error) {
    var mensagem = '' 
    if (error instanceof Error) {
        // Se for um objeto de erro, use a mensagem dele
        mensagem = 'Erro: '+error.message
    } else if (typeof error === 'string') {
        // Se for uma string, use-a diretamente
        mensagem = 'Erro: '+error;
    } else {
        // Se for de outro tipo, use JSON.stringify para ver o que é
        mensagem = 'Erro desconhecido: '+JSON.stringify(error);
    }
    return mensagem
}

// Exporta div contendo tabela para um arquivo CSV
function exportarTabelaParaCSV(dados,aeroporto,titulo) {
    const tabela = document.getElementById(dados);
    if (!tabela) {
    console.error("Tabela não encontrada.");
    return;
    }

    let csv = [];
    const linhas = tabela.querySelectorAll('tr');

    linhas.forEach(function(linha) {
        const colunas = linha.querySelectorAll('th, td');
        const dadosLinha = [];

        colunas.forEach(function(coluna) {
            // Pega o texto da célula e remove espaços extras
            let texto = coluna.innerText.trim();
            
            // Se o texto contiver vírgulas, aspas ou quebras de linha,
            // ele precisa ser envolvido em aspas duplas.
            if (texto.includes(',') || texto.includes('"') || texto.includes('\n')) {
            // Substitui aspas duplas por duas aspas duplas (padrão CSV)
            texto = `"${texto.replace(/"/g, '""')}"`;
            }
            dadosLinha.push(texto);
        });

        // Adiciona a linha formatada com vírgulas
        csv.push(dadosLinha.join(';'));
    });

    // Junta todas as linhas com quebras de linha
    const csvString = csv.join('\n');

    // Adiciona a BOM UTF-8 no início da string.
    // Isso é crucial para que programas como o Excel abram o arquivo corretamente.
    const bom = '\ufeff'; // BOM (Byte Order Mark) para UTF-8
    const conteudoComBOM = bom + csvString;

    // Cria um Blob (objeto de dados) a partir da string CSV
    const blob = new Blob([conteudoComBOM], { type: 'text/csv;charset=utf-8;' });

    // Cria um link de download
    const link = document.createElement("a");

    // Cria uma URL para o Blob
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);

    // Define o nome do arquivo a ser baixado
    const indiceDoHifen = titulo.indexOf('-');
    const arquivo = aeroporto+'_'+(indiceDoHifen !== -1 ? titulo.slice(0, indiceDoHifen).trim() : titulo.trim());
    link.setAttribute("download", arquivo+".csv");

    // Adiciona o link ao corpo do documento, simula um clique e o remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
} 

// Carregar SELECTS PADRAO - TODOS 
//
async function suCarregarSelectTodos(funcao,select,codigo,filtro,operacao = '',selecionar = true) {
    $('.carregando').show();
    await $.getJSON('../suporte/suBuscar.php?funcao='+funcao+'&filtro='+filtro, function(dados){
        var option = (operacao == 'Cadastrar' ? 
                        (selecionar ? '<option value="" disabled selected>Selecionar</option>' : '') :
                            '<option value="" selected>Todos</option>');
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.codigo+'"'+
                    ((obj.codigo === codigo) ? ' selected ' : '') +'>'+
                    obj.descricao+'</option>';
            });
        } else {
            option = '<option value="" disabled selected>Sem registros</option>'
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};

// Carregar SELECTS PADRAO - TODAS 
//
async function suCarregarSelectTodas(funcao,select,codigo,filtro,operacao = '',selecionar = true) {
    $('.carregando').show();
    await $.getJSON('../suporte/suBuscar.php?funcao='+funcao+'&filtro='+filtro, function(dados){
        var option = (operacao == 'Cadastrar' ? 
                        (selecionar ? '<option value="" disabled selected>Selecionar</option>' : '') :
                            '<option value="" selected>Todas</option>');
        if (dados != null) {
            $.each(dados, function(i, obj){
                option += '<option value="'+obj.codigo+'"'+
                    ((obj.codigo === codigo) ? ' selected ' : '') +'>'+
                    obj.descricao+'</option>';
            });
        } else {
            option = '<option value="" disabled selected>Sem registros</option>'
        }
        $(select).html(option).show();
    });
    $('.carregando').hide();
};

// Barra de progresso
//
// document.addEventListener('DOMContentLoaded', () => {
function barraProgresso(refresh, passo = 11) {
    const barra = document.getElementById('barra-progresso');
    const numero = document.getElementById('porcentagem-progresso');

    // Função para atualizar a barra de progresso
    function atualizarProgresso(porcentagem) {
        // Garantir que a porcentagem esteja entre 0 e 100
        const progresso = Math.min(100, Math.max(0, porcentagem));

        // Atualiza a largura da barra e o texto da porcentagem
        if (barra) { barra.style.width = progresso + '%'; }
        if (numero) {numero.textContent = progresso + '%'; }
    }

    // Exemplo de uso: simular o progresso ao longo do tempo
    let progressoAtual = 0;
    const intervalo = setInterval(() => {
        progressoAtual += 10; // Aumenta o progresso em 10%

        if (progressoAtual > 100) {
            if (barra) { barra.style.width = '0%'; }
            if (numero) {numero.textContent = '0%'; }
            clearInterval(intervalo); // Para o intervalo quando o progresso atingir 100%
        } else {
            atualizarProgresso(progressoAtual);
        }
    }, refresh/passo); // Executa a cada 500 milissegundos (meio segundo)
}
// ***************************************************************************************************