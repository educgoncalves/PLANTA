<?php 
function montagemMenu($_sistema,$_aeroporto,$_grupo,$_usuario) {
    // Verifica se existe notificação não lida para o usuário
    $_token = gerarToken($_sistema);
    $_dados = ['tabela'=>'Notificacoes',
                'filtro'=>" AND nt.idUsuario = ".$_usuario." AND nt.situacao = 'NLD'",
                'ordem'=>'nt.cadastro','busca'=>''];
    $_post = ['token'=>$_token,'funcao'=>'Consulta','dados'=>$_dados];
    $_retorno = executaAPIs('apiConsultas.php', $_post);
    if ($_retorno['status'] == 'OK') {
        $_notificacoes = count($_retorno['dados']);
    } else {
        $_notificacoes = 0;
    }

    // Montagem do menu
    $_hrefs = "";
    $_atalhos = array();
    $_graficos = array();
    $_informacoes = array();
    $_dados = ['sistema'=>$_sistema,'aeroporto'=>$_aeroporto,'grupo'=>$_grupo];
    $_post = ['token'=>$_token,'funcao'=>'MontarMenu','dados'=>$_dados];
    $_retorno = executaAPIs('apiMenu.php', $_post);
    if ($_retorno['status'] == 'OK') {
        foreach ($_retorno['dados'] as $_dados) {
            if ($_dados['href'] != '') {
                $_hrefs .= "#".$_dados['formulario'];
            }
            switch ($_dados['atalho']) {
                case 'ACR':
                    $_atalhos[] = $_dados;
                break;
                case 'GRF':
                    $_graficos[] = $_dados;
                break;
                case 'INF':
                    $_informacoes[] = $_dados;
                break;                
            }
        }
    } 

    // Header da tela
    //
    metaTagsSVG();
    echo '<nav class="navbar px-3 border-bottom">';
    echo '  <!-- Botão para alternar a barra lateral -->', "\n";
    echo '  <div class="d-flex justify-content-start">', "\n";
    echo '      <button class="btn menu" type="button"><span class="navbar-toggler-icon"></span></button>', "\n";
    echo '      <a class="navbar-brand" href="../menu/menu.php">', "\n";
    echo '        <img class="d-inline-block align-text-top rounded-pill" src="../ativos/img/logo_medio.png" alt="logo"></a>', "\n";
    echo '  </div>';
    echo '  <!-- Aguardando processamento -->', "\n";
    echo '  <div class="carregando justify-content-center">', "\n";
    echo '      <img src="../ativos/img/carregando.gif" title="Aguarde o processamento" width="30" height="30">  Aguarde o processamento...</img>', "\n";
    echo '  </div>', "\n";
    echo '  <!-- Conjunto de botões de informação -->', "\n";
    echo '  <div class="d-flex justify-content-end">', "\n";
    echo '      <button class="btn btn-outline-primary" type="button" title="Usuário">'.$_SESSION['plantaGrupo'].' - '.$_SESSION['plantaUsuario'].'</button>', "\n";
    if ($_SESSION['plantaGrupo'] == 'ADM') {
        echo '      <a href="?evento=modalAeroporto" class="btn btn-outline-danger" role="button" title="Aeroportos disponíveis">'.$_SESSION['plantaSistema'].' - '.$_SESSION['plantaAeroporto'].'</a>', "\n";
    } else {
        echo '      <a href="../suporte/suLogout.php" class="btn btn-outline-danger" role="button" title="Novo login">'.$_SESSION['plantaSistema'].' - '.$_SESSION['plantaAeroporto'].'</a>', "\n";
    }
    echo '      <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#sobre" title="Sobre o software">Sobre</button>', "\n";
    // Montando o botao de notificacoes
    echo '      <a href="../servicos/svExibirNotificacoes.php" class="btn btn-outline-success" role="button">
                    <img src="../ativos/img/notificacao.png"><span class="badge text-bg-'.
                    ($_notificacoes != 0 ? 'danger">'.$_notificacoes : 'success">').'</span></a>', "\n";
    echo '      <a href="../suporte/suLogout.php" class="btn btn-outline-contrast" role="button" title="Sair">Sair</a>', "\n";
    metaTagsTema(false);    
    echo '  </div>', "\n";
    echo '</nav>', "\n";

    // Inicio da base do menu
    //
    echo '<div class="wrapper">', "\n";
    echo '    <!-- Sidebar -->', "\n";
    echo '    <aside id="sidebar">', "\n";
    echo '        <div class="h-100">', "\n";
    // echo '            <!-- Logotipo -->', "\n";
    // echo '            <div class="sidebar-logo">', "\n";
    // echo '                <a class="navbar-brand" href="../menu/menu.php"><img class="mt-2 d-inline-block align-text-top rounded-pill" src="../ativos/img/logo_medio.png" alt="logo"></a>', "\n";
    // echo '            </div>', "\n";
    echo '            <!-- Sidebar Navigation -->', "\n";
    echo '            <ul class="sidebar-nav">', "\n";

    // Looping de montagem do menu
    $_abriuMenu = false;
    $_abriuSubMenu = false;
    foreach ($_retorno['dados'] as $_dados) {

        // Verifica se abriu SubMenu e tipo != 'SubMenuOpcao'
        if ($_abriuSubMenu && $_dados['tipo'] != 'SubMenuOpcao') {
            echo '</ul>', "\n";
            echo '</li>', "\n";
            $_abriuSubMenu = false;
        }

        // Verifica se abriu Menu e tipo != 'MenuOpcao'
        if ($_abriuMenu && ($_dados['tipo'] == 'Menu' || $_dados['tipo'] == 'Header' || $_dados['tipo'] == 'Opcao')) {
            echo '</ul>', "\n";
            echo '</li>', "\n";
            $_abriuMenu = false;
        }

        // Processa de acordo com o tipo do registro
        switch ($_dados['tipo']) {
            case 'Header':
                echo '<li class="sidebar-header">'.
                ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                $_dados['descricao'].'</li>', "\n";
            break;

            case 'Menu':
                $_href = preg_replace("/\\s+/", "", $_dados['descricao']);
                echo '<li class="sidebar-item">', "\n";
                echo '  <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#'.
                    $_href.'" aria-expanded="false" aria-controls="'.$_href.'">'.
                    ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                    $_dados['descricao'].'</a>', "\n";
                echo '  <ul id="'.$_href.'" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">', "\n";  
                $_abriuMenu = true;
            break;

            case 'SubMenu':
                $_href = preg_replace("/\\s+/", "", $_dados['descricao']);
                echo '<li class="sidebar-item">', "\n";
                echo '  <a href="#" class="sidebar-link-2 collapsed" data-bs-toggle="collapse" data-bs-target="#'.
                    $_href.'" aria-expanded="false" aria-controls="'.$_href.'">'.
                    ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                    $_dados['descricao'].'</a>', "\n";
                echo '  <ul id="'.$_href.'" class="sidebar-dropdown list-unstyled collapse">', "\n";  
                $_abriuSubMenu = true;
            break;

            case 'Opcao':
                // echo '  <li><a class="sidebar-link" href="'.$_dados['href'].'"'.
                //     ($_dados['target'] != '' ? ' target="'.$_dados['target'].'"' : '').'>'.
                //     ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                //     $_dados['descricao'].'</a></li>', "\n";    
                
                echo '  <li><a class="sidebar-link" href="#" data-target="F'.$_dados['formulario'].'">'.
                    ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                    $_dados['descricao'].'</a></li>', "\n"; 
                    
                // Personaliza formulário com a sigla ICAO do Aeroporto
                $_href = str_replace("GEAR", $_SESSION['plantaAeroporto'], $_dados['href']);
                echo '<form id="F'.$_dados['formulario'].'" action="'.$_href.'" method="POST"'.
                    ($_dados['target'] != '' ? ' target="'.$_dados['target'].'"' : '').'>'.
                    '<input type="submit" style="display: none;"></form>';
            break;

            case 'MenuOpcao':
                // Personaliza formulário com a sigla ICAO do Aeroporto
                $_href = str_replace("GEAR", $_SESSION['plantaAeroporto'], $_dados['href']);
                echo '  <li><a class="sidebar-item" href="'.$_href.'"'.
                    ($_dados['target'] != '' ? ' target="'.$_dados['target'].'"' : '').'>'.
                    ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                    $_dados['descricao'].'</a></li>', "\n";
            break;

            case 'SubMenuOpcao':
                // Personaliza formulário com a sigla ICAO do Aeroporto
                $_href = str_replace("GEAR", $_SESSION['plantaAeroporto'], $_dados['href']);
                echo '  <li><a class="sidebar-item-2" href="'.$_href.'"'.
                    ($_dados['target'] != '' ? ' target="'.$_dados['target'].'"' : '').'>'.
                    ($_dados['iconeSVG'] != "" ? '<svg class="iconsvg"><use href="#apl_'.$_dados['iconeSVG'].'"></use></svg>' : '').
                    $_dados['descricao'].'</a></li>', "\n";
            break;            
        }

    }

    // Se abriu SubMenu - fecha
    if ($_abriuSubMenu) {
        echo '</ul>', "\n";
        echo '</li>', "\n";
        $_abriuSubMenu = false;
    }
    // Se abriu Menu - fecha
    if ($_abriuMenu) {
        echo '</ul>', "\n";
        echo '</li>', "\n";
        $_abriuMenu = false;
    }
    echo '<li class="sidebar-header"><a href="http://icons8.com.br" target="_blank">icons by icons8</a></li>', "\n";

    echo '          </ul>', "\n";
    echo '        </div>', "\n";  
    echo '    </aside>', "\n";

    echo '<!-- Cabeçalho da Área do Formulário -->', "\n";
    echo '    <div class="main">', "\n";
    echo '      <main class="content px-3 py-2">', "\n";
//    echo '          <div class="container-fluid">', "\n";
//    echo '              <div class="mb-3">', "\n";
    echo '<!-- Corpo da Área do Formulário -->', "\n";

    echo '<div class="row">',"\n";
    
    // Verifica se monta os atalhos, gráficos e informações
    if (count($_atalhos) != 0) {
        montaAtalhos($_atalhos);
    }
    if (count($_graficos) != 0) {
        montaGraficos($_graficos);
    }
    if (count($_informacoes) != 0) {
        montaInformacoes($_informacoes);
    }      

    echo '</div>',"\n";
 
    return;
}

function montaAtalhos($__atalhos) {
    echo '  <div class="col-lg-12">';
    echo '      <aside id="atalhos">', "\n";
    echo '          <div class="mt-3 text-center"><span><h3>Acesso rápido</h3></span></div>';
    echo '          <div class="row row-cols-auto justify-content-md-center">', "\n";
    foreach ($__atalhos as $_dados) {
        $_href = str_replace("GEAR", $_SESSION['plantaAeroporto'], $_dados['href']);
        echo '<div class="col">', "\n";
        echo '  <a href="'.$_href.'" '.($_dados['target'] != "" ? 'target="'.$_dados['target'].'"' : "").'">', "\n";
        echo '      <div class="card text-primary mb-1" style="min-width: 15rem;">', "\n";
        echo '          <div class="card-body">';
        if (file_exists("../ativos/atalhos/".$_dados['formulario'].".png")) {
            echo '          <img src="../ativos/atalhos/'.$_dados['formulario'].'.png">', "\n";
            $_imagem = '';
        } else {
            $_imagem = ' ['.$_dados['formulario'].']';
            echo '          <img class="d-inline-block" src="../ativos/atalhos/sem_img.png">', "\n";
        }
        echo '              <small style="margin-left: 5px;">'.$_dados['descricao'].$_imagem.'</small></img>', "\n";
        echo '              <img style="float:right; margin-top:3px;" src="../ativos/atalhos/seguir.png"></img>', "\n";
        echo '          </div>';
        echo '          <div class="card-footer"><small>'.$_dados['modulo'].'</small></div>', "\n";
        echo '      </div>', "\n";
        echo '  </a>', "\n";
        echo '</div>', "\n";
    };
    echo '          </div>', "\n";        
    echo '      </aside>', "\n";
    echo '  </div>', "\n";

//     echo'
//     <div class="bd-callout bd-callout-info">
//   <h4>Aviso Importante</h4>
//   <p>Este é um "callout" de informação. Ele serve para destacar uma nota importante para o usuário.</p>
// </div>

// <div class="bd-callout bd-callout-warning">
//   <h4>Atenção</h4>
//   <p>Este é um "callout" de aviso. Use-o para chamar a atenção para um problema ou precaução.</p>
// </div>

// <div class="bd-callout bd-callout-danger">
//   <h4>Perigo</h4>
//   <p>Este é um "callout" de perigo. Use-o para informações críticas ou erros graves.</p>
// </div>';
}

function montaGraficos($__atalhos) {
    echo '  <div class="col-lg-12">', "\n";
    echo '      <aside id="graficos">', "\n";
    echo '          <div class="mt-3 text-center"><span><h3>Gráficos</h3></span></div>', "\n";
    echo '          <div class="row row-cols-auto justify-content-md-center">', "\n";
    foreach ($__atalhos as $_dados) {
        $_href = str_replace("GEAR", $_SESSION['plantaAeroporto'], $_dados['href']);
        echo '<div class="col">', "\n";
        echo '  <a href="'.$_href.'" '.($_dados['target'] != "" ? 'target="'.$_dados['target'].'"' : "").'">', "\n";
        echo '  <div class="card p-2 mb-1" style="min-width: 15rem;">', "\n";
        echo '      <iframe src="'.$_href.'?w=450&h=250" width="450" height="250"></iframe>', "\n";
        echo '  </div>', "\n";
        echo '  </a>', "\n";
        echo '</div>', "\n";
    }
    echo '          </div>', "\n";        
    echo '      </aside>', "\n";
    echo '  </div>', "\n";
}


function montaInformacoes($__atalhos) {
    echo '  <div class="col-lg-12">', "\n";
    echo '      <aside id="informacoes">', "\n";
    echo '          <div class="mt-3 text-center"><span>Informações</span></div>', "\n";
    echo '          <div class="row row-cols-auto justify-content-md-center">', "\n";
    foreach ($__atalhos as $_dados) {
        $_href = str_replace("GEAR", $_SESSION['plantaAeroporto'], $_dados['href']);
        echo '<div class="col">', "\n";
        echo '  <a href="'.$_href.'" '.($_dados['target'] != "" ? 'target="'.$_dados['target'].'"' : "").'">', "\n";
        echo '  <div class="card p-2 mb-1" style="min-width: 15rem;">', "\n";
        echo '      <iframe src="'.$_href.'?w=450&h=250" width="450" height="250"></iframe>', "\n";
        echo '  </div>', "\n";
        echo '  </a>', "\n";
        echo '</div>', "\n";
    }
    echo '          </div>', "\n";        
    echo '      </aside>', "\n";
    echo '  </div>', "\n";
}

function fechamentoMenuLateral() {
    echo '<!-- *************************************************** -->', "\n";
//    echo '</div></div>',"\n";
    echo '</main>',"\n";
    echo '</div></div>', "\n";
}

function fechamentoHtml() {
    echo '</body></html>', "\n";
}
?>