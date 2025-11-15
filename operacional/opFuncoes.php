<?php 
// Atualizar status
//
function atualizarStatus($_conexao, $_parametros, $_status) { 
    try {
        $_destino = (isNullOrEmpty($_status['destino'])) ? 'null' : $_status['destino'];
        if ($_parametros['idStatus'] != "") {
            $_comando = "UPDATE gear_status SET  idMatricula = ".$_status['matricula'].", classe = '".$_status['classe'].
                        "', natureza = '".$_status['natureza']."', servico = '".$_status['servico']."', idOrigem = ".
                        $_status['origem'].", idDestino = ".$_destino." WHERE id = ".$_parametros['idStatus'];

        } else {
            $_comando = "INSERT INTO gear_status (idAeroporto, idMatricula, classe, natureza, servico, idOrigem, idDestino, cadastro) VALUES (".
                        $_parametros['aeroporto'].", ".$_status['matricula'].", '".$_status['classe']."', '".$_status['natureza']."', '".
                        $_status['servico']."', ".$_status['origem'].", ".$_destino.", UTC_TIMESTAMP())";
        }
        $_sql = $_conexao->prepare($_comando);
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) {
                $_idStatus = ($_parametros['idStatus'] != "" ? $_parametros['idStatus']  : $_conexao->lastInsertId());
                gravaDLog("gear_status", ($_parametros['idStatus'] != "" ? "Alteração" : "Inclusão"), $_parametros['siglaAeroporto'],
                            $_parametros['usuario'], $_idStatus, $_comando);
                $_parametros['status'] = "success";
                $_parametros['mensagem'] = array("Registro ".($_idStatus != "" ? "alterado" : "incluído")." com sucesso!");
                $_parametros['complemento'] = "";
                $_parametros['idStatus'] = $_idStatus;
                $_parametros['funcao'] = null;
            } else {
                throw new PDOException("Não foi possível efetivar esta ".($_parametros['idStatus'] != "" ? "alteração" : "inclusão")."!");
            }
        } else {
            throw new PDOException("Não foi possível ".($_parametros['idStatus'] != "" ? "alterar" : "incluir")." este registro!");
        }
    } catch (PDOException $e) {
        gravaTrace(traduzPDO($e->getMessage())."\n".$_comando);
        $_parametros['status'] = 'danger';
        $_parametros['mensagem'] = array(traduzPDO($e->getMessage()));
        $_parametros['complemento'] = $_comando;
        $_parametros['idStatus'] = null;
    }
    return $_parametros;
}

// Consiste se matricula está sendo usada em algum status aberto
//
function matriculaStatusAberto($_parametros,$_status){
    $_erros = "";
    try{
        $_conexao = conexao();
        $_comando = "SELECT CONCAT(st.ano,'/',st.mes,'/',st.numero) as status
                    FROM gear_status st
                    INNER JOIN gear_status_ultimo_movimento um ON um.idStatus = st.id AND um.movimento NOT REGEXP 'DEC|ETC|CND'
                    WHERE st.situacao = 'ATV' AND st.idAeroporto = ".
                        $_parametros['aeroporto']." AND st.idMatricula = ".$_status['matricula'].
                        ($_parametros['idStatus'] != "" ? " AND st.id < ".$_parametros['idStatus'] : "")."  LIMIT 1";
        //gravaXTrace($_comando);
        $_sql = $_conexao->prepare($_comando);
		$_sql->execute(); 
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
            $_erros = "Matricula ".$_status['txMatricula']." sendo utlizada no status ".$_dados['status']." ainda em aberto!";
        } 
    } catch (PDOException $e) {
        $_erros = "matriculaStatusAberto => ".traduzPDO($e->getMessage());
    }
    return $_erros;
}

// Consiste se posicao está ocupada
//
function posicaoOcupada($__parametros,$__status){
    $_erros = "";
    $_comando = "SELECT CONCAT(st.ano,'/',st.mes,'/',st.numero) as status, re.recurso
                FROM gear_status st
                LEFT JOIN gear_status_movimentos mo ON mo.idStatus = st.id AND mo.movimento = 'ENT' AND mo.idRecurso IS NOT NULL
                INNER JOIN gear_status_ultimo_movimento um ON um.idMovimento = mo.id
                LEFT JOIN gear_recursos re ON re.id = mo.idRecurso AND re.tipo = 'POS'
                WHERE st.situacao = 'ATV' AND st.idAeroporto = ".$__parametros['aeroporto'].
                    " AND mo.idRecurso = ".$__status['recurso'].
                    ($__parametros['idStatus'] != "" ? " AND st.id <> ".$__parametros['idStatus'] : "")."  LIMIT 1";
    try{
        $_conexao = conexao();
        $_sql = $_conexao->prepare($_comando);
		$_sql->execute(); 
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
            $_erros = "Posição ".$_dados['recurso']." sendo ocupada pelo status ".$_dados['status']."!";
        } 
    } catch (PDOException $e) {
        $_erros = "posicaoOcupada => ".traduzPDO($e->getMessage());
    }
    return $_erros;
}

// Consiste data e hora do movimento com data e hora atual
//
function dataHoraMovimentoAtual($__parametros,$__status){ 
    $_erros = "";
    // Data e hora do movimento
    $_dhMovimento = mudarDataHoraAMDHM($__status['dtMovimento']." ".$__status['hrMovimento']);
    // Data e hora local do aeroporto
    $_dhAtual = "'".dateTimeUTC($__parametros['utcAeroporto'])->format('Y-m-d H:i')."'";
    if ($_dhMovimento > $_dhAtual && $__parametros['movimento'] != 'Previsão') {
        $_erros = "Data e hora deste movimento ".$_dhMovimento.
                " não pode ser maior do que a data e hora atual ".$_dhAtual."!";
    }
    return $_erros;
}

// Consiste data e hora do movimento com movimentos anteriores
//
function dataHoraMovimentoAnterior($__parametros,$__status){ 
    $_erros = "";
    try{
        $_conexao = conexao();
        $_dhMovimento = mudarDataHoraAMDHM($__status['dtMovimento']." ".$__status['hrMovimento']);
        if ($__parametros['idMovimento'] != "") {
            // Como é alteração, pega a data e hora do movimento anterior descobrindo quem é o penúltimo movimento
            $_comando = "SELECT DATE_FORMAT(m1.dhMovimento,'%Y/%m/%d %H:%i') as dhMovimento
                            FROM gear_status_movimentos m1
                            INNER JOIN (SELECT DISTINCT m2.idStatus, MAX(m2.id) as idPenultimo
                            FROM gear_status_movimentos m2
                            WHERE m2.id < (SELECT MAX(m3.id) FROM gear_status_movimentos m3 WHERE m3.idStatus = m2.idStatus GROUP BY m3.idStatus)
                            GROUP BY m2.idStatus) m0 ON m0.idStatus = m1.idStatus AND m0.idPenultimo = m1.id
                            WHERE m1.idStatus = ".$__parametros['idStatus']." AND m1.dhMovimento > ".$_dhMovimento;
        } else {
            // Como é inclusão, pega a data e hora do último movimento montado no campo $__parametros['idUltimo']
            $_comando = "SELECT DATE_FORMAT(mo.dhMovimento,'%Y/%m/%d %H:%i') as dhMovimento 
                        FROM gear_status_movimentos mo WHERE mo.idStatus = ".$__parametros['idStatus'].
                        " AND mo.id = ".$__parametros['idUltimo']." AND mo.dhMovimento > ".$_dhMovimento;
        }
        $_sql = $_conexao->prepare($_comando);
		$_sql->execute(); 
		$_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
		foreach ($_registros as $_dados) {
            if ($__parametros['idMovimento'] != "") {
                $_erros = "Data e hora deste movimento ".$_dhMovimento." não pode ser menor do que a data e hora '".$_dados['dhMovimento']."' do movimento anterior!";
            } else {
                $_erros = "Data e hora do novo movimento ".$_dhMovimento." não pode ser menor do que a data e hora '".$_dados['dhMovimento']."' do último movimento!";
            }
        } 
    } catch (PDOException $e) {
        $_erros = "dataHoraMovimento => ".traduzPDO($e->getMessage());
    }
    return $_erros;
}

// Destacar criação dos voos planejados
//
function destacarVoosPlanejados(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(vp.cadastro),'%d/%m/%Y %H:%i') as dhAtualizacao, ".
		        "DATE_FORMAT(DATE_ADD(MAX(vp.cadastro), INTERVAL cl.utc HOUR),'%d/%m/%Y %H:%i') as dhLocal ".
                "FROM gear_voos_planejados vp ".
                "LEFT JOIN gear_clientes cl ON cl.idAeroporto = vp.idAeroporto ".
                "WHERE vp.fonte = 'ANAC' AND vp.idAeroporto = 5";
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última atualização do Planejamento: '.$_dados['dhAtualizacao'].' - Horários dos voos em UTC.</h8></div>';
            echo '</div>';
        }
    }
    return;
}

// Destacar criação dos voos operacionais
//
function destacarVoosOperacionais(){
    $_conexao = conexao();
    $_comando = "SELECT DATE_FORMAT(MAX(dtMovimento),'%d/%m/%Y') as dtMovimento FROM gear_voos_operacionais WHERE idAeroporto = ".$_SESSION['plantaIDAeroporto'];
    $_sql = $_conexao->prepare($_comando);     
    if ($_sql->execute()) {
        $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($_registros as $_dados) {
            echo '<div class="row">';
            echo '  <div class ="col-lg-12 text-primary"><br><h8>Última geração de Voos Operacionais: '.$_dados['dtMovimento'].' - Horários dos voos em local.</h8></div>';
            echo '</div>';
        }
    }
    return;
}

// Limpar movimento operacional
function limparMovimento($_filtro) {
    try {
        $_retorno = true;
        $_conexao = conexao();
        $_comando = "SELECT vo.id
                    FROM gear_voos_operacionais vo
                    LEFT JOIN gear_voos_ultimo_movimento um ON um.idVoo = vo.id
                    LEFT JOIN gear_status st ON st.idChegada = vo.id OR st.idPartida = vo.id
                    LEFT JOIN gear_voos_operacionais vop ON vop.idChegada = vo.id OR vop.idPartida = vo.id
                    WHERE st.idChegada is null AND st.idPartida is null 
                        AND vop.idChegada is null AND vop.idPartida is null 
                        AND IFNULL(um.movimento, 'PRV') = 'PRV'".$_filtro;
        $_sql = $_conexao->prepare($_comando); 
        if ($_sql->execute()){
            $_registros = $_sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($_registros as $_dados) {
                $_comando = "DELETE FROM gear_voos_operacionais WHERE id = ".$_dados['id'];
                $_sql = $_conexao->prepare($_comando); 
                $_sql->execute();                    
            }
        } 
    } catch (PDOException $e) {
        $_retorno = false;
    }
    return $_retorno;
}

// Correção dos campos
// Na tela de Pouso , acho que podia dar uma simplificada, para a operação pelo Celular:
// Deixar os seguintes campos para o Pouso unificado a entrada no calço (primeira entrada):

// Matrícula
// Origem
// Destino
// Data do Pouso,
// Hora do Pouso
// Hora da Entrada no Calço: 
// (O sistema pode sugerir tantos minutos após o horpario do pouso, esse "tantos" minutos a mais do pouso, 
// deve ser um parâmetro do cadastro do aeroporto, e pode ser um para aeronaves do Grupo 1 e outro para 
// aeronaves do Grupo 2.)
// Pista:
// Box:

// Correção
// Se conteúdo voltar diferente de branco, alterar os campos e não deixar alterar
//
function corrigirCamposStatus($_tipo, $_status) {
    $_matricula = $_status['txMatricula'];
    $_local = ($_tipo === 'origem' ? $_status['txOrigem'] : $_status['txDestino']);
    $_regMatricula = '/^(FAB|PT|PR|PS|PU)/i';
    $_regLocal = '/^(SB|SD|SI|SJ|SN|SS|SW|8T)/i';

    // Determina o Grupo da matrícula
    try {
        $_grupo = 2;
        $_conexao = conexao();
        $_comando = "SELECT op.grupo FROM gear_matriculas mt LEFT JOIN gear_operadores op ON op.id = mt.idOperador
                    WHERE mt.id = ".$_status['matricula'];
        $_sql = $_conexao->prepare($_comando); 
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) { 
                $_grupo = $_sql->fetch(PDO::FETCH_ASSOC)['grupo'];
            }
        } 
    } catch (PDOException $e) {
        $_grupo = 2;
    }

    // Classe - Obrigatório
    // Se matrícula RAB e local BR, classe doméstica
    if (preg_match($_regMatricula, $_matricula)) {
        if (preg_match($_regLocal, $_local)) {
            if ($_status['classe'] !== 'DOM') {
                $_status['classe'] = 'DOM';
                $_status['txClasse'] = 'DOM - Doméstica';
                $_status['resultado'] = 'warning';
            }
        } else {
            if ($_status['classe'] !== 'INT') {
                $_status['classe'] = 'INT';
                $_status['txClasse'] = 'INT - Internacional';
                $_status['resultado'] = 'warning';
            }  
        }   
    } else {
        if ($_status['classe'] !== 'INT') {
            $_status['classe'] = 'INT';
            $_status['txClasse'] = 'INT - Internacional';
            $_status['resultado'] = 'warning';          
        }
    } 

    // Natureza - Obrigatório
    // Se Grupo 2 será sempre Passageiro Misto
    if ($_grupo === '2') {
        if ($_status['natureza'] !== 'PAX') {
            $_status['natureza'] = 'PAX';
            $_status['txNatureza'] = 'PAX - Passageiro/Misto';
            $_status['resultado'] = 'warning';             
        }
    }
    // Opcional - Caso não tenha conteúdo atribui PAX
    if ($_status['natureza'] === "") {
        $_status['natureza'] = 'PAX';
        $_status['txNatureza'] = 'PAX - Passageiro/Misto';
    } 

    // Servico
    // Se Matrícula começa com FAB, o voo será do tipo de Serviço Militar
    if (substr($_matricula, 0, 3) === 'FAB') {
        if ($_status['servico'] !== 'W') {
            $_status['servico'] = 'W';
            $_status['txServico'] = 'W - Militar';
            $_status['resultado'] = 'warning'; 
        }
    } else {
        if ($_status['servico'] === 'W') {
            $_status['servico'] = '';
            $_status['txServico'] = '';
            $_status['resultado'] = 'warning'; 
        }
    }
    // Opcional - Caso não tenha conteúdo atribui AVG para Grupo II e REG para grupo I
    if ($_status['servico'] === "") {
        if ($_grupo === '2') {
            $_status['servico'] = 'D';
            $_status['txServico'] = 'D - Avição Geral';
        } else {
            $_status['servico'] = 'J';
            $_status['txServico'] = 'J - Regular de passageiros';
        }
    } 

    // Mensagem se alguma informação foi corrigido - Obrigatorio
    if ($_status['resultado'] === 'warning') {
        $_status['mensagem'] = array('Classificação do voo foi ajustada seguindo normas da ANAC. Favor verificar.');
    }
    return $_status;
}
?>