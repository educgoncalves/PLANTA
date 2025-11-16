<?php
require_once("../suporte/suEnviarEmail.php");

// Verificar se a tarefa a ser executada está ativa
//
function verificarTarefaAtiva($_tarefa) {
    $_retorno = false;
    $_conexao = conexao();
    $_comando = "SELECT id FROM planta_tarefas WHERE situacao = 'ATV' AND codigo = '".$_tarefa."'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_retorno = ($_sql->rowCount() > 0);
    }
    return $_retorno;
}

// Verificar se a tarefa a ser executada está ativa
//
function registrarExecucaoTarefa($_tarefa, $modo) {
    $_retorno = false;
    $_conexao = conexao();
    $_comando = "UPDATE planta_tarefas SET dhExecucao = UTC_TIMESTAMP, modo = '".$modo."' WHERE codigo = '".$_tarefa."'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_retorno = ($_sql->rowCount() > 0);
    }
    return $_retorno;
}

// Enviar email de aviso que houve problema na importacao
//
function enviarEmailTarefa($_tarefa, $_resultado, $_tipo) {
    $_retorno = false;
    $_conexao = conexao();
    $_comando = "SELECT id FROM planta_tarefas WHERE email = 'SIM' AND codigo = '".$_tarefa."'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        if ($_sql->rowCount() > 0) {
            $sistema = 'GEAR'; 
            $aeroporto = '';
            $usuario = 'Tarefa Automática';
            $email = '';
            $assunto = $_tipo;
            $mensagem = 'Execução da tarefa '.$_resultado;
            $anexo = '../logs/'.$_resultado.'.txt';
            $_retorno = enviarEmail($sistema, $aeroporto, $usuario, $email, $assunto, $mensagem, $anexo);
        }
    }
    return $_retorno;
}

// Destacar importações diversas
//
function destacarTarefaVoosANAC(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i') as dhAtualizacao FROM planta_voos_anac";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última importação ANAC: '.$_dados['dhAtualizacao'].' - Horários dos voos em UTC.</h8></div>';
            echo '</div>';
        }
    }
    return;
}

function destacarTarefaPublicosANAC(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i') as dhAtualizacao FROM planta_aeroportos WHERE origem = 'PUB'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última importação ANAC: '.$_dados['dhAtualizacao'].'</h8></div>';
            echo '</div>';
        }
    }
    return;
}

function destacarTarefaPrivadosANAC(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i') as dhAtualizacao FROM planta_aeroportos WHERE origem = 'PRI'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última importação ANAC: '.$_dados['dhAtualizacao'].'</h8></div>';
            echo '</div>';
        }
    }
    return;
}

function destacarTarefaAeroportosANAC(){
    $_conexao = conexao();
    $_comando = "SELECT CONCAT('aeroportos com voos regulares: ', DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i')) as descricao FROM planta_voos_anac
                UNION 
                SELECT CONCAT('aeródromos públicos: ', DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i')) as descricao FROM planta_aeroportos WHERE origem = 'PUB'
                UNION 
                SELECT CONCAT('aeródromos privados: ', DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i')) as descricao FROM planta_aeroportos WHERE origem = 'PRI'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        echo '<br><div class="row">';
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '  <div class ="col-lg-12 text-primary"><h8>Última importação de '.$_dados['descricao'].'</h8></div>';
        }
        echo '</div>';
    }
    return;
}

function destacarTarefaEquipamentosANAC(){
    $_conexao = conexao();
    $_comando = "SELECT CONCAT('equipamentos ICAO: ', DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i')) as descricao FROM planta_equipamentos WHERE fonte = 'ICAO'
                UNION 
                SELECT CONCAT('equipamentos RAB: ', DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i')) as descricao FROM planta_matriculas WHERE fonte = 'ANAC'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        echo '<br><div class="row">';
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '  <div class ="col-lg-12 text-primary"><h8>Última importação de '.$_dados['descricao'].'</h8></div>';
        }
        echo '</div>';
    }
    return;
}

function destacarTarefaICAO(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i') as dhAtualizacao FROM planta_equipamentos WHERE fonte = 'ICAO'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última importação ICAO: '.$_dados['dhAtualizacao'].'</h8></div>';
            echo '</div>';
        }
    }
    return;
}

function destacarTarefaRAB(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(cadastro),'%d/%m/%Y %H:%i') as dhAtualizacao FROM planta_matriculas WHERE fonte = 'ANAC'";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última importação RAB: '.$_dados['dhAtualizacao'].'</h8></div>';
            echo '</div>';
        }
    }
    return;
}
?>