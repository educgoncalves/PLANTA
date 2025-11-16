<?php
require_once("../tarefas/trFuncoes.php");
require_once("../suporte/suFuncoes.php");
require_once("../suporte/suConexao.php");
require_once("../informativos/inFuncoes.php");

function construtorInformativos($identificacao, $idSite, $siglaAeroporto, $usuario = 'GEAR', $modo = 'AUT') {
    $tarefa = 'CINF';
    $resultado = "trConstrutorInformativos_".$identificacao;
    $_mensagens[] = "";
    $_mensagens[] = "Construtor Informativos - executado por ".$usuario;
    $_mensagens[] = "";
    $_mensagens[] = "Parâmentros: ".$idSite.' '.$usuario.' '.$modo;
    
    $erros = array();

    try {
        // Verifica se pode executar a tarefa
        if (!verificarTarefaAtiva($tarefa)) {
            throw new PDOException("Tarefa não cadastrada ou desativada no momento!");
        } else {
            registrarExecucaoTarefa($tarefa, $modo);
        }

    // Montar a propaganda quando for o caso
        $propaganda = montarPropaganda($idSite, $siglaAeroporto, $usuario);
    // ******************************************************************************************        

    // Gerando tela html - Movimentos Grupo I - pagina 1 e 2
        for ($page = 1; $page <= 2; $page++) {
            $html = $siglaAeroporto."_MovimentosGrupoI_P".$page.".html";
            $h = fopen('../siv/'.$html,'w'); 

            $limite = 21;  
            $titulo = 'Movimentos Grupo I';
            $subTitulo = '';

            // Escolhe o script base e registra a propaganda
            if ($propaganda['propaganda'] != "") {
                $base = "../ativos/html/baseListaPropagandas.html";
                registrarPropaganda($titulo, $propaganda['id'], $propaganda['propaganda'], $siglaAeroporto, $usuario);
            } else {
                $base = "../ativos/html/baseLista.html";
            }
            
            // Altera as informações do script base
            $conteudo = "";
            if (file_exists($base)) {
                $conteudo = file_get_contents($base);
                if ($conteudo) {
                    $chaves = array('$page','$titulo','$subTitulo','$idSite','$siglaAeroporto','$propaganda');
                    $dados = array($page, $titulo, $subTitulo, $idSite, $siglaAeroporto, $propaganda['propaganda']);
                    $conteudo = str_replace($chaves, $dados, $conteudo);
                    
                    // Monta as informações do sistema 
                    $_mensagens[] = 'Montagem para '.$siglaAeroporto.' - página: '.$page." tela: ".$titulo;
                    $tabela = inMovimentosGrupoI($idSite, $page, $limite);
                    $conteudo = str_replace('$tabela', $tabela, $conteudo);
                    if ($tabela == "") {
                        $erros[] = "Informações para ".$siglaAeroporto."-".$titulo." não foram montadas!";
                    }
                } else {
                    $conteudo = "Sem acesso ao arquivo base '.$base.'!";
                    $erros[] = $conteudo; 
                }
            } else {
                $conteudo = "Arquivo base '.$base.' não encontrado!";
                $erros[] = $conteudo; 
            }
            // Gravando o arquivo html
            fwrite($h, $conteudo."\n");
            fclose($h);
        }
    // ******************************************************************************************

    // Gerando tela html - Movimentos Grupo II - pagina 1 e 2
        for ($page = 1; $page <= 2; $page++) {
            $html = $siglaAeroporto."_MovimentosGrupoII_P".$page.".html";
            $h = fopen('../siv/'.$html,'w'); 

            $limite = 21;  
            $titulo = 'Movimentos Grupo II';
            $subTitulo = '';

            // Escolhe o script base e registra a propaganda
            if ($propaganda['propaganda'] != "") {
                $base = "../ativos/html/baseListaPropagandas.html";
                registrarPropaganda($titulo, $propaganda['id'], $propaganda['propaganda'], $siglaAeroporto, $usuario);
            } else {
                $base = "../ativos/html/baseLista.html";
            }
            
            // Altera as informações do script base
            $conteudo = "";
            if (file_exists($base)) {
                $conteudo = file_get_contents($base);
                if ($conteudo) {
                    $chaves = array('$page','$titulo','$subTitulo','$idSite','$siglaAeroporto','$propaganda');
                    $dados = array($page, $titulo, $subTitulo, $idSite, $siglaAeroporto, $propaganda['propaganda']);
                    $conteudo = str_replace($chaves, $dados, $conteudo);
                    
                    // Monta as informações do sistema 
                    $_mensagens[] = 'Montagem para '.$siglaAeroporto.' - página: '.$page." tela: ".$titulo;
                    $tabela = inMovimentosGrupoII($idSite, $page, $limite);
                    $conteudo = str_replace('$tabela', $tabela, $conteudo);
                    if ($tabela == "") {
                        $erros[] = "Informações para ".$siglaAeroporto."-".$titulo." não foram montadas!";
                    }
                } else {
                    $conteudo = "Sem acesso ao arquivo base '.$base.'!";
                    $erros[] = $conteudo; 
                }
            } else {
                $conteudo = "Arquivo base '.$base.' não encontrado!";
                $erros[] = $conteudo; 
            }
            // Gravando o arquivo html
            fwrite($h, $conteudo."\n");
            fclose($h);
        }
    // ******************************************************************************************

    // Sucesso
        $_tipoMsg = "success";

        if (count($erros) != 0) {
            $_mensagens[] = "";
            $_mensagens[] = "Erros";
            array_push($_mensagens, ...$erros);
        }
    // ******************************************************************************************

    } catch (PDOException $e) {
        $_tipoMsg = "danger";
        $_mensagens[] = "";
        $_mensagens[] = traduzPDO($e->getMessage());
    }

    // Registrando o log
    foreach ($_mensagens as $msg) {
        gravaXLogProcesso($resultado, $_tipoMsg, $msg, $identificacao);
    }
        
    // Enviar email de resultado da execução da importação - depende de configuração na tabela de tarefas
    enviarEmailTarefa($tarefa,$resultado,$_tipoMsg);
}
?>