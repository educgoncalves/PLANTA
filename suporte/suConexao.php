<?php
function conexao() {
    // Decide em qual banco vai conectar - desenvolvimento ou produção
    //
    if (isset($_SERVER) && $_SERVER["HTTP_HOST"] == "localhost") {
        $bd_host = "localhost";                     // Endereço do servidor mySQL pode ser o localhost
        $bd_user = "root";                          // Seu Login no mySQL
        $bd_pass = "";                              // Sua Senha no mySQL
        $bd_bd = "planta";                           // Nome do Banco de Dados
    } else {
        $bd_host = "localhost";    // Endereço do servidor mySQL pode ser o localhost
        $bd_user = "planta";                              // Seu Login no mySQL
        $bd_pass = "gear@0615";                         // Sua Senha no mySQL
        $bd_bd = "planta";                                // Nome do Banco de Dados
    }
    $opcoes = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];
    try {
        $con = new PDO("mysql:host=".$bd_host."; dbname=".$bd_bd.";", $bd_user, $bd_pass, $opcoes);
        //echo 'Banco integracao conectado';
    } catch (PDOException $e) {
        $con = null;
        echo 'Erro na Integração: ' . $e->getMessage();
    }
    return $con;
}

function integracao() {
    // Decide em qual banco vai conectar - desenvolvimento ou produção
    //
    if (isset($_SERVER) && $_SERVER["HTTP_HOST"] == "localhost") {
        $bd_host = "localhost";                     // Endereço do servidor mySQL pode ser o localhost
        $bd_user = "root";                          // Seu Login no mySQL
        $bd_pass = "";                              // Sua Senha no mySQL
        $bd_bd = "integracao";              // Nome do Banco de Dados
    } else {
        //mysql://planta_int:@DecolaMais2025#@31.97.23.50:3306/integracao
        $bd_host = "31.97.23.50";                   // Endereço do servidor mySQL pode ser o localhost
        $bd_user = "planta_int";                      // Seu Login no mySQL
        $bd_pass = "@DecolaMais2025#";              // Sua Senha no mySQL
        $bd_bd = "integracao"; 
    }
    $opcoes = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];
    try {
        $con = new PDO("mysql:host=".$bd_host."; port=3306; dbname=".$bd_bd.";", $bd_user, $bd_pass, $opcoes);
        //echo 'Banco integracao conectado';
    } catch (PDOException $e) {
        $con = null;
        echo 'Erro na Integração: ' . $e->getMessage();
    }
    return $con;
}

function traduzPDO($_mensagem) {
    $_mensagem = str_replace('SQLSTATE[23000]: ','ERRO: ',$_mensagem);
    $_mensagem = str_replace('Integrity constraint violation:','Violação de integridade:',$_mensagem);
    $_mensagem = str_replace('Duplicate entry','entrada duplicada',$_mensagem);
    $_mensagem = str_replace('for key','para a chave',$_mensagem);
    $_mensagem = str_replace('Cannot delete or update a parent row: a foreign key constraint fails','Esta chave não pode ser alterada ou excluída!',$_mensagem);
    // if (strpos($_mensagem, '(') !== false) {
    //     $_mensagem = substr($_mensagem,0,strpos($_mensagem, '('));
    // }
    return $_mensagem;
}

function selectDB($_tabela,$_filtro = "",$_ordem = "",$_busca = ""){
    $_retorno = "";

    switch ($_tabela) {
        case 'Acessos':
            $_retorno = "SELECT ac.id, ac.idUsuario, ac.idSite, ac.sistema, ac.grupo, ac.preferencial, st.site, st.nome, 
                    st.localidade, dm.descricao as nivel, us.usuario, CONCAT(us.usuario,' - ',us.nome) as usuarioCompleto, 
                    CONCAT(st.site,' - ',st.localidade) as aeroportoCompleto, CONCAT(ac.grupo,' - ',dm2.descricao) as grupoCompleto, 
                    dm3.descricao as descPreferencial, CONCAT(ac.sistema,' - ',dm4.descricao) as sistemaCompleto
                FROM planta_acessos ac
                LEFT JOIN planta_usuarios us ON us.id = ac.idUsuario
                LEFT JOIN planta_sites st ON st.id = ac.idSite
                LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_acessos' and dm.coluna = 'nivel' and dm.codigo = ac.grupo
                LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_acessos' and dm2.coluna = 'grupo' and dm2.codigo = ac.grupo
                LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_acessos' and dm3.coluna = 'preferencial' and dm3.codigo = ac.preferencial
                LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_todos' and dm4.coluna = 'sistema' and dm4.codigo = ac.sistema";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "us.usuario, st.site");	 
        break;

    //     case 'AcessosGrupos':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //             FROM planta_dominios dm
    //             WHERE dm.tabela = 'planta_acessos' and dm.coluna = 'grupo'
    //             ORDER BY dm.ordenacao, dm.descricao";
    //     break;    

    //     case 'AcessosPreferencial':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //             FROM planta_dominios dm
    //             WHERE dm.tabela = 'planta_acessos' and dm.coluna = 'preferencial'
    //             ORDER BY dm.ordenacao, dm.descricao";
    //     break;
 

    //     case 'Aeroportos':
    //         $_retorno = "SELECT ae.id, ae.iata, st.site, st.nome, st.localidade, ae.pais, ae.origem, ae.fonte, 
    //                         CONCAT(ae.fonte,' - ',dm2.descricao) as descFonte,
    //                         ae.situacao, dm.descricao as descSituacao, CONCAT(st.site,' - ',st.nome) as aeroportoCompleto,
    //                         (CASE WHEN (SELECT COUNT(*) FROM planta_clientes cl WHERE cl.idSite = ae.id) = 0 THEN 'Não' ELSE 'Sim' END) as cliente,
    //                         ae.id as codigo, CONCAT(st.site,' - ',st.localidade) as descricao
    //                     FROM planta_sites ae
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = ae.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'origem' and dm2.codigo = ae.origem";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.site");
    //     break;
            
    //     case 'AeroportosClientes':
    //         $_retorno = "SELECT DISTINCT ae.id, ae.iata, st.site, st.nome, st.localidade, ae.pais, ae.situacao, dm2.descricao as descSituacao, 
    //                     CONCAT(st.site,' - ',st.nome) as aeroportoCompleto, CONCAT(st.site,' - ',st.localidade) as aeroportoLocalidade,
    //                     ae.id as codigo, CONCAT(st.site,' - ',st.localidade) as descricao
    //                     FROM planta_sites ae
    //                     INNER JOIN planta_clientes cl ON cl.idSite = ae.id
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'situacao' and dm2.codigo = ae.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.site");
    //     break;

        case 'Atalhos':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                FROM planta_dominios dm
                WHERE dm.tabela = 'planta_menus' and dm.coluna = 'atalho'
                ORDER BY dm.ordenacao, dm.descricao";
        break;

    //     case 'Classe':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_voos' and dm.coluna = 'classe'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Clientes':
    //         $_retorno = "SELECT cl.id, cl.idSite, cl.celular, cl.sistema, cl.conexoes, cl.tmpIsencao, cl.regPorPagina, cl.debug, 
    //                     dm.descricao as descDebug, cl.utc, TIME_FORMAT(cl.hrAbertura,'%H:%i') as horaAbertura, cl.tmpReserva,
    //                     TIME_FORMAT(cl.hrFechamento,'%H:%i') as horaFechamento, cl.tmpRetorno,
    //                     st.site, cl.situacao, dm2.descricao as descSituacao, CONCAT(st.site,' - ',st.nome) as aeroportoCompleto,
    //                     cl.categoria, IFNULL(dm3.descricao,'') as descCategoria, cl.tipoOperador, IFNULL(dm4.descricao,'') as descTipoOperador, 
    //                     cl.avsec, IFNULL(dm5.descricao,'') as descAvsec, cl.tmpTaxiG1, cl.tmpTaxiG2, cl.tmpRefreshPagina, cl.tmpRefreshTela
    //             FROM planta_clientes cl
    //             LEFT JOIN planta_sites st ON st.id = cl.idSite
    //             LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'simnao' and dm.codigo = cl.debug
    //             LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'situacao' and dm2.codigo = cl.situacao
    //             LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_clientes' and dm3.coluna = 'categoria' and dm3.codigo = cl.categoria
    //             LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_clientes' and dm4.coluna = 'tipoOperador' and dm4.codigo = cl.tipoOperador
    //             LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_clientes' and dm5.coluna = 'avsec' and dm5.codigo = cl.avsec";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.site, cl.sistema");	 
    //     break;
        
    //     case 'ClientesAvsec':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, CONCAT(dm.codigo,' - ',dm.descricao) as descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_clientes' and dm.coluna = 'avsec'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
        
    //     case 'ClientesCategoria':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_clientes' and dm.coluna = 'categoria'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
    
    //     case 'ClientesTipoOperador':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_clientes' and dm.coluna = 'tipoOperador'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Comandantes':
    //         $_retorno = "SELECT co.id, co.codigoAnac, co.nome, co.telefone, co.email, co.situacao, 
    //                         dm.descricao as descSituacao
    //                     FROM planta_comandantes co
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = co.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "co.codigoAnac");
    //     break;

    //     case 'Conexoes':
    //         $_retorno = "SELECT co.id, co.idSite, co.sistema, co.usuario, co.grupo, co.identificacao, co.entrada, co.saida, 
    //                             co.situacao, co.cadastro, st.site
    //                     FROM planta_conexoes co
    //                     LEFT JOIN planta_sites st ON st.id = co.idSite";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "co.id");                        
    //     break;

    //     case 'ConexoesSomaSituacao':
    //         $_retorno = "SELECT dm.descricao as descSituacao, count(*) as somatorio
    //                     FROM planta_conexoes co
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = co.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " GROUP BY co.situacao";
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "co.id");
    //     break;

    //     case 'ConectarVoos':
    //         //
    //         // Pega o horário do último movimento CNF, se nulo utiliza a dhPrevista do voo
    //         //
    //         $_retorno = "SELECT id, idSite, voo, dhConfirmada, operacao, idChegada, idPartida,
    //                             vo.id as codigo, voo as descricao
    //                     FROM 
    //                         (SELECT vo.id, vo.idSite,
    //                             CONCAT(vo.operador, '', vo.numeroVoo, ' - ', DATE_FORMAT(hc.dhConfirmada,'%d/%m/%Y %H:%i')) as voo, 
    //                             hc.dhConfirmada, vo.operacao, vo.idChegada, vo.idPartida
    //                         FROM planta_voos_operacionais vo
    //                         LEFT JOIN planta_voos_horario_confirmado hc ON hc.idVoo = vo.id) vo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vo.voo");                        
    //     break;

    //     case 'Credenciados':
    //         $_retorno = "SELECT pcr.id, pcr.idEmpresa, pcr.nome, pcr.documento, pcr.endereco, pcr.bairro, pcr.email, pcr.telefone, pcr.cargo,
    //                         pcr.responsavel, dm2.descricao as descResponsavel, pcr.credencial, pcr.idArea, CONCAT(ae.iata,'_pes_',pcr.id) as imagem,
    //                         (CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso, em.empresa,
    //                         pcr.validade, IFNULL(DATE_FORMAT(pcr.validade,'%d/%m/%Y'),'') as dataValidade, pcr.situacao, dm.descricao as descSituacao, pcr.cadastro
    //                     FROM planta_pessoas_credenciadas pcr
    //                     LEFT JOIN planta_empresas em ON em.id = pcr.idEmpresa
    //                     LEFT JOIN planta_sites st ON st.id = em.idSite					
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = pcr.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'simnao' and dm2.codigo = pcr.responsavel
    //                     LEFT JOIN planta_recursos re ON re.id = pcr.idArea";
    
    //         // SELECT pcr.id, pcr.idEmpresa, pcr.nome, pcr.documento, pcr.endereco, pcr.bairro, pcr.email, pcr.telefone, pcr.cargo,
    //         // 						pcr.responsavel, dm2.descricao as descResponsavel, pcr.credencial, pcr.idArea, 
    //         // 						(CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso,
    //         // 						pcr.validade, DATE_FORMAT(pcr.validade,'%d/%m/%Y') as dataValidade, pcr.situacao, dm.descricao as descSituacao, pcr.cadastro
    //         // 					FROM planta_pessoas_credenciadas pcr
    //         // 					LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = pcr.situacao
    //         // 					LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'simnao' and dm2.codigo = pcr.responsavel
    //         // 					LEFT JOIN planta_recursos re ON re.id = pcr.idArea
    //         // UNION                   
    //         // SELECT vcr.id, vcr.idEmpresa, "" as nome, vcr.placa, vcr.marca, vcr.modelo, vcr.cor, vcr.tipo, "" as cargo,
    //         // 						"" as responsavel, "" as descResponsavel, vcr.credencial, vcr.idArea, 
    //         // 						(CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso,
    //         // 						vcr.validade, DATE_FORMAT(vcr.validade,'%d/%m/%Y') as dataValidade, vcr.situacao, dm.descricao as descSituacao, vcr.cadastro
    //         // 					FROM planta_veiculos_credenciados vcr
    //         // 					LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = vcr.situacao
    //         // 					LEFT JOIN planta_recursos re ON re.id = vcr.idArea  
    
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "pcr.nome");
    //     break;

    //     case 'DestinoIcao':
    //         $_retorno = "SELECT st.site as codigo, st.site as descricao, st.nome, CONCAT(st.site,' - ',st.nome) as codigoCompleto
    //                     FROM planta_sites ae
    //                     ORDER BY st.site";
    //     break;

    //     case 'Empresas':
    //         $_retorno = "SELECT em.id, em.idSite, em.empresa, em.atividade, em.endereco, em.bairro, em.email, em.telefone, 
    //                         em.situacao, dm.descricao as descSituacao, em.cadastro,
    //                         em.id as codigo, em.empresa as descricao
    //                     FROM planta_empresas em
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = em.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "em.empresa");
    //     break;

    //     case 'Equipamentos':
    //         $_retorno = "SELECT eq.id, eq.equipamento, eq.modelo, eq.iataEquipamento, eq.icaoCategoria, eq.tipoMotor, 
    //                         eq.fabricante, eq.envergadura, eq.comprimento, eq.assentos, eq.asa, eq.situacao, eq.fonte,
    //                         dm.descricao as descTipoMotor, dm2.descricao as descSituacao, dm3.descricao as descAsa,
    //                         CONCAT(eq.equipamento,' - ',eq.modelo,' - ',eq.fabricante) as equipamentoCompleto,
    //                         CONCAT(eq.fonte,' - ',dm4.descricao) as descFonte,
    //                         eq.id as codigo, CONCAT(eq.equipamento,' - ',eq.modelo) as descricao
    //                     FROM planta_equipamentos eq
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_equipamentos' and dm.coluna = 'tipoMotor' and dm.codigo = eq.tipoMotor
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'situacao' and dm2.codigo = eq.situacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_equipamentos' and dm3.coluna = 'asa' and dm3.codigo = eq.asa
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_todos' and dm4.coluna = 'origem' and dm4.codigo = eq.origem";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "eq.equipamento,eq.modelo,eq.fabricante");
    //     break;
    
    //     case 'EquipamentosAsa':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_equipamentos' and dm.coluna = 'asa'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
   
    //     case 'EquipamentosTipoMotor':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_equipamentos' and dm.coluna = 'tipoMotor'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
    
    //     case 'FonteAeroportos':
    //         $_retorno = "SELECT fonte as codigo, fonte as descricao
    //                     FROM (SELECT DISTINCT(CONCAT(ae.fonte,' - ',dm2.descricao)) as fonte
    //                     FROM planta_sites ae
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'origem' and dm2.codigo = ae.origem
    //                     ORDER BY fonte) T";
    //     break;

    //     case 'FonteEquipamentos':
    //         $_retorno = "SELECT fonte as codigo, fonte as descricao
    //                     FROM (SELECT DISTINCT(CONCAT(eq.fonte,' - ',dm2.descricao)) as fonte
    //                     FROM planta_equipamentos eq
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'origem' and dm2.codigo = eq.origem
    //                     ORDER BY fonte) T";
    //     break;
            
    //     case 'FonteMatriculas':
    //         $_retorno = "SELECT fonte as codigo, fonte as descricao
    //                     FROM (SELECT DISTINCT(CONCAT(mt.fonte,' - ',dm2.descricao)) as fonte
    //                     FROM planta_matriculas mt
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'origem' and dm2.codigo = mt.origem
    //                     ORDER BY fonte) T";
    //     break;

    //     case 'FonteOperadores':
    //         $_retorno = "SELECT fonte as codigo, fonte as descricao
    //                     FROM (SELECT DISTINCT(CONCAT(op.fonte,' - ',dm2.descricao)) as fonte
    //                     FROM planta_operadores op
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'origem' and dm2.codigo = op.origem
    //                     ORDER BY fonte) T";
    //     break;

    //     case 'FonteVoos':
    //         $_retorno = "SELECT fonte as codigo, fonte as descricao
    //                     FROM (SELECT DISTINCT(CONCAT(vp.fonte,' - ',IFNULL(dm2.descricao, vp.origem))) as fonte
    //                     FROM planta_voos_planejados vp
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'origem' and dm2.codigo = vp.origem
    //                     ORDER BY fonte) T";
    //     break;

        case 'Logs':
            $_retorno = "SELECT lg.id, lg.cadastro, lg.tabela, lg.operacao, lg.site, lg.usuario, lg.registro, lg.comando, lg.observacao
                FROM planta_logs lg";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "data,hora");
        break;

        case 'LogsTabela':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                FROM planta_dominios dm
                WHERE dm.tabela = 'planta_logs' and dm.coluna = 'tabela'
                ORDER BY dm.ordenacao, dm.descricao";
        break;

        case 'LogsOperacao':
            $_retorno = "SELECT operacao as codigo, operacao as descricao
                        FROM (SELECT DISTINCT (lg.operacao) as operacao
                        FROM planta_logs lg
                        ORDER BY operacao) T";
        break;

    //     case 'Matriculas':
    //         $_retorno = "SELECT mt.id, mt.matricula, mt.idOperador, mt.idEquipamento, mt.assentos, mt.pmd, mt.categoria, mt.situacao, dm.descricao as descSituacao, 
    //                         eq.equipamento, eq.modelo, CONCAT(eq.equipamento,' - ',eq.modelo,' - ',eq.fabricante) as equipamentoCompleto, mt.fonte,
    //                         IFNULL(dm2.descricao,'') as descCategoria,
    //                         (CASE 	WHEN IFNULL(op.icao,'') = ''
    //                                 THEN op.operador 
    //                                 ELSE CONCAT(op.icao,' - ',op.operador)
    //                         END) as operadorCompleto, op.icao, op.operador, op.nome,
    //                         CONCAT(mt.fonte,' - ',dm3.descricao) as descFonte
    //                     FROM planta_matriculas mt
    //                     LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento
    //                     LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = mt.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_matriculas' and dm2.coluna = 'categoria' and dm2.codigo = mt.categoria
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' and dm3.coluna = 'origem' and dm3.codigo = mt.origem";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "mt.matricula");
    //     break;

    //     case 'MatriculasCategoria':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_matriculas' and dm.coluna = 'categoria'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'MatriculasLivres':
    //         $_retorno = "SELECT mt.id, mt.matricula
    //                     FROM planta_matriculas mt
    //                     LEFT JOIN (SELECT st.idMatricula 
    //                                 FROM planta_status st
    //                                 LEFT JOIN planta_status_movimentos sm ON sm.idStatus = st.id AND sm.movimento <> 'DEC'
    //                                 INNER JOIN planta_status_ultimo_movimento um ON um.id = sm.id
    //                                 WHERE st.situacao = 'ATV') tb ON tb.idMatricula = mt.id
    //                     WHERE tb.idMatricula IS NULL";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "mt.matricula");	
    //     break;

        case 'MenusFormulario':
            $_retorno = "SELECT me.id, me.sistema, me.tipo, dm.descricao as descTipo, me.formulario, me.modulo, me.descricao, 
                        me.href, me.target, me.iconeSVG, me.ordem, me.atalho, dm2.descricao as descAtalho, 
                        CONCAT(me.sistema,' - ',me.modulo,' - ',me.descricao) as formularioCompleto
                        FROM planta_menus me
                        LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_menus' and dm.coluna = 'tipo' and dm.codigo = me.tipo
                        LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_menus' and dm2.coluna = 'atalho' and dm2.codigo = me.atalho";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "me.sistema, me.ordem, me.formulario");
        break;

        case 'Modulos':
            $_retorno = "SELECT modulo as codigo, modulo as descricao FROM (SELECT DISTINCT me.modulo FROM planta_menus me) T";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "modulo");
        break;

    //     case 'Monitores':
    //         $_retorno = "SELECT mt.id, mt.idSite, mt.numero, CONCAT(st.site, mt.numero) as identificacao,
    //                         mt.localizacao, mt.situacao, dm.descricao as descSituacao,
    //                         DATE_FORMAT(mt.cadastro,'%d/%m/%Y %H:%i') as dataHoraCadastro
    //                     FROM planta_monitores mt 
    //                     LEFT JOIN planta_sites st ON st.id = mt.idSite
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = mt.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "identificacao");
    //     break;

    //     case 'MonitoresAcoes':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_monitores_paginas' and dm.coluna = 'acao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'MonitoresPaginas':
    //         $_retorno = "SELECT mt.id, mt.idSite, mt.numero, CONCAT(st.site, mt.numero) as identificacao,
    //                         mt.localizacao, mt.situacao, dm.descricao as descSituacao,
    //                         DATE_FORMAT(mt.cadastro,'%d/%m/%Y %H:%i') as dataHoraCadastro,
    //                         mp.id as idPagina, mp.idMonitor, IFNULL(mp.acao,'') as acao, IFNULL(dm2.descricao,'') as descAcao, 
    //                         IFNULL(mp.pagina,'') as pagina, IFNULL(mp.segundos,'') as segundos, IFNULL(mp.resolucao,'') as resolucao, 
    //                         IFNULL(dm3.descricao,'') as descResolucao, IFNULL(mp.situacao, '') as situacaoPagina, 
    //                         IFNULL(dm4.descricao,'') as descSituacaoPagina,
    //                         DATE_FORMAT(mp.cadastro,'%d/%m/%Y %H:%i') as dataHoraCadastroPagina
    //                     FROM planta_monitores mt 
    //                     LEFT JOIN planta_monitores_paginas mp ON mp.idMonitor = mt.id
    //                     LEFT JOIN planta_sites st ON st.id = mt.idSite
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = mt.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_monitores_paginas' and dm2.coluna = 'acao' and dm2.codigo = mp.acao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_monitores_paginas' and dm3.coluna = 'resolucao' and dm3.codigo = mp.resolucao
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_todos' and dm4.coluna = 'situacao' and dm4.codigo = mp.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "identificacao, mp.id");
    //     break;

    //     case 'MonitoresResolucoes':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_monitores_paginas' and dm.coluna = 'resolucao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Movimentos':
    //         $_retorno = "SELECT mo.id, mo.idSite, mo.movimento, mo.descricao, mo.operacao, mo.ordem, mo.sucessora, mo.antes,
    //                         mo.depois, mo.antecessoras, IFNULL(mo.destaque,'') as destaque, mo.situacao, dm.descricao as descSituacao,
    //                         dm2.descricao as descOperacao, IFNULL(dm3.descricao, '') as descDestaque, IFNULL(mo.alerta,0) as alerta,
    //                         mo.movimento as codigo
    //                     FROM planta_movimentos mo
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = mo.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_movimentos' and dm2.coluna = 'operacao' and dm2.codigo = mo.operacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' and dm3.coluna = 'destaque' and dm3.codigo = mo.destaque";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "mo.ordem,mo.movimento,mo.operacao");
    //     break;
   
    //     case 'MovimentosOperacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_movimentos' and dm.coluna = 'operacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Natureza':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_voos' and dm.coluna = 'natureza'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'NaturezaOperacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios_anac dm
    //                     WHERE dm.tabela = 'planta_voos_anac' and dm.coluna = 'naturezaOperacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Notificacoes':
    //         $_retorno = "SELECT nt.id, nt.idUsuario, nt.idSite, nt.sistema, nt.notificacao, nt.situacao, nt.cadastro,
    //                 st.site, us.usuario, CONCAT(us.usuario,' - ',us.nome) as usuarioCompleto, 
    //                 dm.descricao as descSituacao, CONCAT(nt.sistema,' - ',dm2.descricao) as sistemaCompleto,
    //                 IFNULL(DATE_FORMAT(nt.cadastro,'%d/%m/%Y %H:%i'),'') as dataHoraCadastro
    //             FROM planta_notificacoes nt
    //             LEFT JOIN planta_usuarios us ON us.id = nt.idUsuario
    //             LEFT JOIN planta_sites st ON st.id = nt.idSite
    //             LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_notificacoes' and dm.coluna = 'situacao' and dm.codigo = nt.situacao
    //             LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'sistema' and dm2.codigo = nt.sistema";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "nt.cadastro");	 
    //     break;

    //     case 'NotificacoesSituacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_notificacoes' and dm.coluna = 'situacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'ObjetoTransporte':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios_anac dm
    //                     WHERE dm.tabela = 'planta_voos_anac' and dm.coluna = 'objetoTransporte'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'OperadorANAC':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.codigo as descricao, dm.descricao as nome, dm.ordenacao, 
    //                         CONCAT(dm.codigo,' - ',dm.descricao) as codigoCompleto
    //                     FROM planta_dominios_anac dm
    //                     WHERE dm.tabela = 'planta_voos_anac' and dm.coluna = 'operador'
    //                     ORDER BY dm.ordenacao, dm.codigo";
    //     break;

    //     case 'OperadoresCobranca':
    //         $_retorno = "SELECT opc.id, opc.operador, opc.nome, opc.situacao, dm.descricao as descSituacao, 
    //                     opc.endereco, opc.complemento, opc.bairro, opc.municipio, opc.cidade, opc.estado, opc.cep,
    //                     (CONCAT(IFNULL(opc.endereco, ''), ' - ', IFNULL(opc.complemento, ''), ' - ', IFNULL(opc.bairro, ''), 
    //                             ' - ', IFNULL(opc.municipio, ''), ' - ', IFNULL(opc.cidade, ''), ' - ', IFNULL(opc.estado, ''), 
    //                             ' - ', opc.cep)) as enderecoCompleto,
    //                     opc.contato, opc.email, opc.telefone, 
    //                     (CONCAT(IFNULL(opc.contato, ''), ' - ', IFNULL(opc.email, ''), ' - ', IFNULL(opc.telefone, ''))) as contatoCompleto,
    //                     IFNULL(opc.cpfCnpj, '') as cpfCnpj, opc.fonte, CONCAT(opc.fonte,' - ',dm3.descricao) as descFonte,
    //                     (SELECT COUNT(*) FROM planta_operadores op WHERE op.idCobranca = opc.id) as qtdOperadores
    //                     FROM planta_operadores_cobranca opc
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = opc.situacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' and dm3.coluna = 'origem' and dm3.codigo = opc.origem";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "opc.operador");
    //     break;

    //     case 'OperadoresRAB':
    //         $_retorno = "SELECT op.id, op.operador, op.nome, op.iata, op.icao, op.situacao, 
    //                     dm.descricao as descSituacao, dm2.descricao as descGrupo,
    //                     (CASE WHEN IFNULL(op.icao,'') = ''
    //                         THEN op.operador ELSE CONCAT(op.icao,' - ',op.operador) END) as operadorCompleto,
    //                     (CASE WHEN IFNULL(mz.operador,'') = ''
    //                         THEN '' 
    //                             WHEN IFNULL(mz.icao,'') = ''
    //                             THEN mz.operador  
    //                                 ELSE CONCAT(mz.icao,' - ',mz.operador) END) as matrizCompleta, 
    //                     IFNULL(mz.icao,'') as matrizIcao, 
    //                     IFNULL(op.idCobranca,'') as idCobranca, 
    //                     (CASE WHEN IFNULL(op.idCobranca,'') = '' 
    //                         THEN '' ELSE CONCAT(opc.cpfCnpj,' - ',opc.operador) END) as descCobranca,
    //                     IFNULL(op.idMatriz,'') as idMatriz, op.grupo, IFNULL(op.cpfCnpj, '') as cpfCnpj,
    //                     op.fonte, CONCAT(op.fonte,' - ',dm3.descricao) as descFonte,
    //                     (SELECT COUNT(*) FROM planta_matriculas mt WHERE mt.idOperador = op.id) as qtdMatriculas
    //                     FROM planta_operadores op
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = op.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'grupo' and dm2.codigo = op.grupo
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' and dm3.coluna = 'origem' and dm3.codigo = op.origem
    //                     LEFT JOIN planta_operadores_cobranca opc ON opc.id = op.idCobranca
    //                     LEFT JOIN planta_operadores mz ON mz.id = op.idMatriz";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "op.operador");
    //     break;

    //     case 'OrigemIcao':
    //         $_retorno = "SELECT st.site as codigo, st.site as descricao, st.nome, CONCAT(st.site,' - ',st.nome) as codigoCompleto
    //                     FROM planta_sites ae
    //                     ORDER BY st.site";
    //     break;

    //     case 'PessoasCredenciadas':
    //         $_retorno = "SELECT pcr.id, pcr.idEmpresa, pcr.nome, pcr.documento, pcr.endereco, pcr.bairro, pcr.email, pcr.telefone, pcr.cargo,
    //                         pcr.responsavel, dm2.descricao as descResponsavel, pcr.credencial, pcr.idArea, 
    //                         (CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso,
    //                         pcr.validade, IFNULL(DATE_FORMAT(pcr.validade,'%d/%m/%Y'),'') as dataValidade, pcr.situacao, dm.descricao as descSituacao, pcr.cadastro
    //                     FROM planta_pessoas_credenciadas pcr
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = pcr.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' and dm2.coluna = 'simnao' and dm2.codigo = pcr.responsavel
    //                     LEFT JOIN planta_recursos re ON re.id = pcr.idArea";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "pcr.nome");
    //     break;

    //     case 'PosicoesLivres':
    //         $_retorno = "SELECT re.*
    //                     FROM planta_recursos re
    //                     LEFT JOIN (SELECT sm.idRecurso, st.idSite
    //                                 FROM planta_status st
    //                                 LEFT JOIN planta_status_movimentos sm ON sm.idStatus = st.id AND sm.movimento <> 'DEC' AND sm.movimento <> 'SAI' AND sm.idRecurso IS NOT NULL
    //                                 INNER JOIN planta_status_ultimo_movimento um ON um.id = sm.id
    //                                 WHERE st.situacao = 'ATV') tb ON tb.idRecurso = re.id AND tb.tipo = 'POS' AND tb.idSite = po.idSite
    //                     WHERE tb.idRecurso IS NULL";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "re.recurso");	
    //     break;   

    //     case 'ProcedenciaDestino':
    //         $_retorno = "SELECT ae.id as codigo, st.site as descricao
    //                     FROM planta_sites ae";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.site");
    //     break;

    //     case 'Propagandas':
    //         $_retorno = "SELECT pg.id, pg.idSite, pg.empresa, pg.propaganda, 
    //                     DATE_FORMAT(pg.dtInicio,'%Y-%m-%d') as dtInicio, DATE_FORMAT(pg.dtInicio,'%d/%m/%Y') as dataInicio, 
    //                     DATE_FORMAT(pg.dtFinal,'%Y-%m-%d') as dtFinal, DATE_FORMAT(pg.dtFinal,'%d/%m/%Y') as dataFinal, 
    //                     DATE_FORMAT(pg.dhExibicao,'%Y-%m-%d') as dhExibicao, IFNULL(DATE_FORMAT(pg.dhExibicao,'%d/%m/%Y %H:%i'),'') as dataHoraExibicao, 
    //                     pg.situacao, dm.descricao as descSituacao
    //                     FROM planta_propagandas pg
    //                     LEFT JOIN planta_sites st ON st.id = pg.idSite
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_propagandas' AND dm.coluna = 'situacao' AND dm.codigo = pg.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "pg.empresa,pg.dtInicio");
    //     break;

    //     case 'PropagandasParaAtivar':
    //         $_retorno = "SELECT pg.id, pg.idSite, pg.empresa, pg.propaganda, 
    //                         DATE_FORMAT(pg.dtInicio,'%Y-%m-%d') as dtInicio, DATE_FORMAT(pg.dtInicio,'%d/%m/%Y') as dataInicio, 
    //                         DATE_FORMAT(pg.dtFinal,'%Y-%m-%d') as dtFinal, DATE_FORMAT(pg.dtFinal,'%d/%m/%Y') as dataFinal, 
    //                         DATE_FORMAT(pg.dhExibicao,'%Y-%m-%d') as dhExibicao, 
    //                         IFNULL(DATE_FORMAT(pg.dhExibicao,'%d/%m/%Y %H:%i'),'') as dataHoraExibicao, 
    //                         pg.situacao, dm.descricao as descSituacao
    //                     FROM 
    //                     (
    //                         SELECT MIN(CONCAT(IFNULL(pg1.dhExibicao,'1900-01-01 00:00:00'),'-',id)) as chave
    //                         FROM planta_propagandas pg1
    //                         WHERE (SELECT COUNT(*) from planta_propagandas pg2 
    //                                 WHERE pg2.idSite = pg1.idSite AND pg2.situacao IN ('EXB','INT')) = 0
    //                                     AND CURRENT_DATE BETWEEN dtInicio AND dtFinal
    //                         GROUP BY pg1.idSite
    //                     ) pg0
    //                     LEFT JOIN planta_propagandas pg ON CONCAT(IFNULL(pg.dhExibicao,'1900-01-01 00:00:00'),'-',pg.id) = pg0.chave
    //                     LEFT JOIN planta_sites st ON st.id = pg.idSite
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_propagandas' AND dm.coluna = 'situacao' AND dm.codigo = pg.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "pg.empresa,pg.dtInicio");  
    //     break;                      

    //     case 'PropagandasSituacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_propagandas' and dm.coluna = 'situacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Recursos':
    //         $_retorno = "SELECT re.id, re.idSite, re.recurso, re.descricao, re.tipo, dm.descricao as descTipo, re.natureza, dm2.descricao as descNatureza, 
    //                         re.situacao, dm3.descricao as descSituacao, re.utilizacao, dm4.descricao as descUtilizacao, re.classe, dm5.descricao as descClasse, 
    //                         re.capacidade, re.unidade, dm6.descricao as descUnidade, re.sentido, IFNULL(dm7.descricao,'') as descSentido, re.envergadura, 
    //                         re.comprimento, CONCAT(st.site,' - ',st.nome) as aeroportoCompleto, 
    //                         re.idDireita, IFNULL(di.recurso,'') as descDireita, re.idEsquerda, IFNULL(es.recurso,'') as descEsquerda,
    //                         re.idGrupamento, IFNULL(gr.recurso,'') as descGrupamento,
    //                         re.id as codigo, re.recurso as descricao
    //                     FROM planta_recursos re
    //                     LEFT JOIN planta_sites st ON st.id = re.idSite
    //                     LEFT JOIN planta_dominios dm  ON dm.tabela  = 'planta_recursos' and dm.coluna = 'tipo' and dm.codigo = re.tipo
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_recursos' and dm2.coluna = 'natureza' and dm2.codigo = re.natureza
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_recursos' and dm3.coluna = 'situacao' and dm3.codigo = re.situacao
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_recursos' and dm4.coluna = 'utilizacao' and dm4.codigo = re.utilizacao
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_recursos' and dm5.coluna = 'classe' and dm5.codigo = re.classe
    //                     LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_recursos' and dm6.coluna = 'unidade' and dm6.codigo = re.unidade
    //                     LEFT JOIN planta_dominios dm7 ON dm7.tabela = 'planta_recursos' and dm7.coluna = 'sentido' and dm7.codigo = re.sentido
    //                     LEFT JOIN planta_recursos di ON di.id = re.idDireita
    //                     LEFT JOIN planta_recursos es ON es.id = re.idEsquerda
    //                     LEFT JOIN planta_recursos gr ON gr.id = re.idGrupamento";
    //                     $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "re.tipo,re.recurso");
    //     break;
    
    //     case 'RecursosClasse':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'classe'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'RecursosNatureza':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'natureza'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'RecursosSentido':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'sentido'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'RecursosSituacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'situacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'RecursosTipo':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'tipo'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
        
    //     case 'RecursosUnidade':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'unidade'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
        
    //     case 'RecursosUtilizacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_recursos' and dm.coluna = 'utilizacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'Remessas':
    //         $_retorno = "SELECT rm.id, rm.idSite, rm.ano, rm.numero, CONCAT(rm.ano,'/',rm.numero) as remessa,
    //                         rm.qtdFaturas, rm.qtdFaturas, rm.qtdLinhas, rm.vlrTotal, rm.idUsuario, us.usuario, 
    //                         rm.situacao, dm.descricao as descSituacao, rm.cadastro, 
    //                         DATE_FORMAT(DATE_ADD(rm.cadastro, INTERVAL cl.utc HOUR),'%d/%m/%Y %H:%i') as dataHoraCadastro
    //                     FROM planta_remessas rm
    //                     LEFT JOIN planta_sites st ON st.id = rm.idSite 
    //                     LEFT JOIN planta_clientes cl ON cl.idSite = ae.id AND cl.sistema = 'MAER'
    //                     LEFT JOIN planta_usuarios us ON us.id = rm.idUsuario
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' AND dm.coluna = 'situacao' AND dm.codigo = rm.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "remessa, rm.id");     
    //     break;

    //     case 'Reservas':
    //         $_retorno = "SELECT rs.id, rs.idUsuario, rs.idSite, rs.numero, CONCAT(rs.ano,'/',rs.mes,'/',rs.numero) as reserva,
    //                         rs.matricula, rs.origem, rs.chegada, rs.pob, rs.destino, rs.partida, 
    //                         rs.fonte, rs.observacao, rs.enviar, rs.envio, rs.situacao, rs.cadastro, eq.equipamento,
    //                         CONCAT(rs.matricula,' - ',IFNULL(eq.equipamento,'Indefinido')) as matriculaCompleta,
    //                         IFNULL(DATE_FORMAT(rs.cadastro,'%d/%m/%Y %H:%i'),'') as dataHoraCadastro,
    //                         IFNULL(DATE_FORMAT(rs.chegada,'%d/%m/%Y %H:%i'),'') as dataHoraChegada,
    //                         IFNULL(DATE_FORMAT(rs.partida,'%d/%m/%Y %H:%i'),'') as dataHoraPartida,
    //                         IFNULL(DATE_FORMAT(rs.envio,'%d/%m/%Y %H:%i'),'') as dataHoraEnvio, dm.descricao as descSituacao,
    //                         us.usuario, us.nome, us.email, CONCAT(us.usuario,' - ',us.nome) as usuarioCompleto
    //                     FROM planta_reservas rs 
    //                     LEFT JOIN planta_reservas_usuarios us ON us.id = rs.idUsuario
    //                     LEFT JOIN planta_matriculas mt ON mt.matricula = rs.matricula
    //                     LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento 
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_reservas' and dm.coluna = 'situacao' and dm.codigo = rs.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "reserva");
    //     break;

    //     case 'ReservasHistoricos':
    //         $_retorno = "SELECT rs.id, rs.idUsuario, rs.idSite, rs.numero, CONCAT(rs.ano,'/',rs.mes,'/',rs.numero) as reserva,
    //                         rs.matricula, rs.origem, rs.chegada, rs.pob, rs.destino, rs.partida, dm.descricao as descSituacao,
    //                         rs.fonte, rs.observacao, rs.enviar, rs.envio, rs.situacao, rs.cadastro, eq.equipamento,
    //                         CONCAT(rs.matricula,' - ',IFNULL(eq.equipamento,'Indefinido')) as matriculaCompleta,
    //                         IFNULL(DATE_FORMAT(rs.cadastro,'%d/%m/%Y %H:%i'),'') as dataHoraCadastro,
    //                         IFNULL(DATE_FORMAT(rs.chegada,'%d/%m/%Y %H:%i'),'') as dataHoraChegada,
    //                         IFNULL(DATE_FORMAT(rs.partida,'%d/%m/%Y %H:%i'),'') as dataHoraPartida,
    //                         IFNULL(DATE_FORMAT(rs.envio,'%d/%m/%Y %H:%i'),'') as dataHoraEnvio, 
    //                         us.usuario, us.nome, us.email, CONCAT(us.usuario,' - ',us.nome) as usuarioCompleto,
    //                         IFNULL(rh.id,'') as idHistorico, IFNULL(rh.situacao, '') as situacaoHistorico, 
    //                         IFNULL(dm2.descricao,'') as descSituacaoHistorico, IFNULL(rh.observacao,'') as observacaoHistorico, 
    //                         IFNULL(DATE_FORMAT(rh.cadastro,'%d/%m/%Y %H:%i'),'') as dataHoraCadastroHistorico
    //                     FROM planta_reservas rs 
    //                     LEFT JOIN planta_reservas_historicos rh ON rh.idReserva = rs.id
    //                     LEFT JOIN planta_reservas_usuarios us ON us.id = rs.idUsuario
    //                     LEFT JOIN planta_matriculas mt ON mt.matricula = rs.matricula
    //                     LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento 
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_reservas' and dm.coluna = 'situacao' and dm.codigo = rs.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_reservas' and dm2.coluna = 'situacao' and dm2.codigo = rh.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "reserva");
    //     break;

    //     case 'ReservasSituacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_reservas' and dm.coluna = 'situacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'ReservasSomaSituacao':
    //         $_retorno = "SELECT dm.descricao as descSituacao, count(*) as somatorio
    //                     FROM planta_reservas rs 
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_reservas' and dm.coluna = 'situacao' and dm.codigo = rs.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " GROUP BY rs.situacao";
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "rs.reserva");
    //     break;

    //     case 'ReservasUsuarios':
    //         $_retorno = "SELECT us.id, us.usuario, us.nome, us.email, us.fonte, us.situacao, dm.descricao as descSituacao,
    //                 CONCAT(us.usuario,' - ',us.nome) as usuarioCompleto
    //             FROM planta_reservas_usuarios us
    //             LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = us.situacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "us.usuario");
    //     break;

        case 'Restricoes':
            $_retorno = "SELECT re.id, re.idSite, re.sistema, re.formulario, CONCAT(re.sistema,' - ',me.modulo,' - ',me.descricao) as formularioCompleto,
                            re.grupo, CONCAT(re.grupo,' - ',dm.descricao) as grupoCompleto, CONCAT(st.site,' - ',st.localidade) as aeroportoCompleto
                        FROM planta_restricoes re
                        LEFT JOIN planta_sites st ON st.id = re.idSite
                        LEFT JOIN planta_menus me ON me.sistema = re.sistema AND me.formulario = re.formulario
                        LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_acessos' and dm.coluna = 'grupo' and dm.codigo = re.grupo";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "re.sistema, me.modulo, me.descricao, re.grupo");
        break;

    //     case 'Servico':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_voos' and dm.coluna = 'servico'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'ServicoAnac':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios_anac dm
    //                     WHERE dm.tabela = 'planta_voos_anac' and dm.coluna = 'servico'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;

        case 'Sistemas':
            $_retorno = "SELECT sistema as codigo, sistema as descricao FROM (SELECT DISTINCT me.sistema FROM planta_menus me) T";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "sistema");
        break;
    
    //     case 'Plantas':
    //         $_retorno = "SELECT DISTINCT ac.idSite, ac.sistema, CONCAT(st.site,' - ',ac.sistema) as site,
	// 		                (CASE WHEN cl.idSite IS NULL THEN '(Falta definição do cliente)' ELSE '' END) as cliente
    //                     FROM planta_acessos ac
    //                     LEFT JOIN planta_sites st ON st.id = ac.idSite
    //                     LEFT JOIN planta_clientes cl ON cl.idSite = ac.idSite and cl.sistema = ac.sistema";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.site,ac.sistema");
    //     break;

    //     case 'SituacaoSiros':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios_anac dm
    //                     WHERE dm.tabela = 'planta_voos_anac' and dm.coluna = 'situacaoSiros'
    //                     ORDER BY dm.ordenacao, dm.codigo";
    //     break;

    //     case 'Status':
    //         $_retorno = "SELECT st.id, st.idSite, st.site as operacao, st.ano, st.mes, st.numero, CONCAT(st.ano,'/',st.mes,'/',st.numero) as status, 
    //                         st.idMatricula, st.faturado, dm1.descricao as descFaturado, st.classe, st.natureza, st.servico, st.situacao, 
    //                         dm2.descricao as descSituacao, CONCAT(dm3.descricao,' - ',dm4.descricao,' - ',dm5.descricao) as descTipo,
    //                         dm3.descricao as descClasse, dm4.descricao as descNatureza, dm5.descricao as descServico,
    //                         st.idOrigem, IFNULL(pr.icao, '') as origem, st.idDestino, IFNULL(de.icao, '') as destino,
    //                         sm.id as idMovimento, IFNULL(sm.movimento, '') as movimento, IFNULL(mo.descricao,'') as descMovimento, 
    //                         sm.dhMovimento, IFNULL(DATE_FORMAT(sm.dhMovimento,'%d/%m/%Y %H:%i'),'') as dataHoraMovimento,
    //                         DATE_FORMAT(sm.dhMovimento,'%Y-%m-%d') as dtMovimento, DATE_FORMAT(sm.dhMovimento,'%H:%i') as hrMovimento,
    //                         sm.idRecurso, re.tipo as tipoRecurso, IFNULL(re.recurso,'') as descRecurso,
    //                         (CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso, sm.usuario,
    //                         sm.idSegundoRecurso, re2.tipo as tipoSegundoRecurso, IFNULL(re2.recurso,'') as descSegundoRecurso,
    //                         (CASE WHEN re2.recurso IS NULL THEN '' ELSE CONCAT(re2.tipo,' - ',re2.recurso) END) as segundoRecurso,
    //                         pm.dhMovimento as dhPrimeiroMovimento, um.id as idUltimoMovimento,
    //                         st.idChegada, IFNULL(CONCAT(ch.operador, ch.numeroVoo),'') as vooChegada,
    //                         st.idPartida, IFNULL(CONCAT(pa.operador, pa.numeroVoo),'') as vooPartida,
    //                         mt.matricula, eq.equipamento, eq.modelo, cm.id as idComplemento, cm.regra, 
    //                         IFNULL(cm.embarque_pax,0) as embarque_pax, IFNULL(cm.embarque_carga,0) as embarque_carga, IFNULL(cm.embarque_correio,0) as embarque_correio, 
    //                         IFNULL(cm.desembarque_pax,0) as desembarque_pax, IFNULL(cm.desembarque_carga,0) as desembarque_carga, IFNULL(cm.desembarque_correio,0) as desembarque_correio, 
    //                         IFNULL(cm.transito_pax,0) as transito_pax, IFNULL(cm.observacao,'') as observacao, IFNULL(dm6.descricao,'') as descRegra,
    //                         co.id as idComando, co.codigoAnac, co.nome, IFNULL(co.email,'') as email, 
    //                         (CASE WHEN co.id IS NULL THEN '' ELSE CONCAT(co.codigoAnac,' - ',co.nome) END) as comandante, 
    //                         op.grupo, op.operador, IFNULL(mo.destaque,'') as destaque
    //                     FROM planta_status st
    //                     LEFT JOIN planta_status_movimentos sm ON sm.idStatus = st.id 
    //                     LEFT JOIN planta_status_primeiro_movimento pm ON pm.idStatus = st.id 
    //                     LEFT JOIN planta_status_ultimo_movimento um ON um.idStatus = st.id 
    //                     LEFT JOIN planta_movimentos mo ON mo.idSite = st.idSite AND mo.movimento = sm.movimento AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')
    //                     LEFT JOIN planta_dominios dm1 ON dm1.tabela = 'planta_status' AND dm1.coluna = 'faturado' AND dm1.codigo = st.faturado
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_status' AND dm2.coluna = 'situacao' AND dm2.codigo = st.situacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_voos' AND dm3.coluna = 'classe' AND dm3.codigo = st.classe
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'natureza' AND dm4.codigo = st.natureza
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'servico' AND dm5.codigo = st.servico
    //                     LEFT JOIN planta_sites st ON st.id = st.idSite 
    //                     LEFT JOIN planta_sites pr ON pr.id = st.idOrigem 
    //                     LEFT JOIN planta_sites de ON de.id = st.idDestino
    //                     LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //                     LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento
    //                     LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //                     LEFT JOIN planta_recursos re ON re.id = sm.idRecurso
    //                     LEFT JOIN planta_recursos re2 ON re2.id = sm.idSegundoRecurso
    //                     LEFT JOIN planta_voos_operacionais ch ON ch.id = st.idChegada
    //                     LEFT JOIN planta_voos_operacionais pa ON pa.id = st.idPartida
    //                     LEFT JOIN planta_status_complementos cm ON cm.idStatus = st.id 
    //                     LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_status_complementos' AND dm6.coluna = 'regra' AND dm6.codigo = cm.regra
    //                     LEFT JOIN planta_comandantes co ON co.id = cm.idComandante";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "status, sm.id");
    //     break;

    //     case 'StatusComplementos':
    //         $_retorno = "SELECT st.id, st.idSite, CONCAT(st.ano,'/',st.mes,'/',st.numero) as status, 
    //                         st.idMatricula, mt.matricula, 
    //                         cm.id as idComplemento, cm.regra, cm.embarque_pax, cm.embarque_carga, cm.embarque_correio, 
    //                         cm.desembarque_pax, cm.desembarque_carga, cm.desembarque_correio, cm.transito_pax, 
    //                         cm.observacao, dm1.descricao as descRegra,
    //                         co.id as idComando, co.codigoAnac, co.nome,
    //                         (CASE WHEN co.id IS NULL THEN '' ELSE CONCAT(co.codigoAnac,' - ',co.nome) END) as comandante
    //                     FROM planta_status st
    //                     LEFT JOIN planta_status_complementos cm ON cm.idStatus = st.id 
    //                     LEFT JOIN planta_comandantes co ON co.id = cm.idComandante
    //                     LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //                     LEFT JOIN planta_dominios dm1 ON dm1.tabela = 'planta_status_complementos' AND dm1.coluna = 'regra' AND dm1.codigo = cm.regra";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "status");
    //     break;
      
    //     case 'StatusSituacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_status' and dm.coluna = 'situacao'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;
    
    //     case 'StatusFaturado':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_status' and dm.coluna = 'faturado'
    //                     ORDER BY dm.ordenacao, dm.descricao";
    //     break;    

    //     case 'StatusFaturamento':
    //         $_retorno = "SELECT st.id, st.idSite, ae.iata as operacao, st.ano, st.mes, st.numero, st.idMatricula,  
    //                             CONCAT(st.ano,'/',st.mes,'/',st.numero) as status, st.idDestino, st.idOrigem, 
    //                             st.classe, st.natureza, st.servico, 
    //                             dm1.descricao as descFaturado, dm2.descricao as descSituacao, 
    //                             st.idChegada, IFNULL(CONCAT(ch.operador, '', ch.numeroVoo),'') as vooChegada,
    //                             st.idPartida, IFNULL(CONCAT(pa.operador, '', pa.numeroVoo),'') as vooPartida,
    //                             mt.matricula, mt.pmd, 
    //                             IFNULL(pr.icao, '') as origem, IFNULL(de.icao, '') as destino,
    //                             mpri.id as idPrimeiroMovimento, mpri.movimento as moPrimeiroMovimento, 
    //                             mpri.dhMovimento as dhPrimeiroMovimento, 
    //                             DATE_FORMAT(mpri.dhMovimento,'%d/%m/%Y %H:%i') as dataHoraPrimeiroMovimento, 
    //                             mult.id as idUltimoMovimento, mult.movimento as moUltimoMovimento, 
    //                             mult.dhMovimento as dhUltimoMovimento, 
    //                             DATE_FORMAT(mult.dhMovimento,'%d/%m/%Y %H:%i') as dataHoraUltimoMovimento, 
    //                             ca.idFaturamento,
    //                             ca.dhPouso, DATE_FORMAT(ca.dhPouso,'%d/%m/%Y %H:%i') as dataHoraPouso, 
    //                             ca.dhDecolagem, DATE_FORMAT(ca.dhDecolagem,'%d/%m/%Y %H:%i') as dataHoraDecolagem, 
    //                             ca.cadastro, DATE_FORMAT(ca.cadastro,'%d/%m/%Y %H:%i') as dhConfirmacaoCalculo,
    //                             IFNULL(ca.tmpPatio,0) as tmpPatio, IFNULL(ca.tmpEstadia,0) as tmpEstadia, 
    //                             IFNULL(ca.tmpIsento,0) as tmpIsento, IFNULL(ca.vlrPPO,0) as vlrPPO, IFNULL(ca.vlrPPM,0) as vlrPPM, 
    //                             IFNULL(ca.vlrPPE,0) as vlrPPE,  IFNULL(ca.situacao,'PEN') as situacaoCalculo, dm3.descricao as descCalculo,  
    //                             CONCAT(fa.ano,'/',fa.numero) as faturamento, fa.id as idFatura,
    //                             (CASE WHEN fa.idRemessa IS NOT NULL THEN CONCAT(rm.ano,'/',rm.numero) ELSE '' END) as remessa, 
    //                             DATE_FORMAT(fa.cadastro,'%d/%m/%Y %H:%i') as dhConfirmacaoFaturamento, 
    //                             IFNULL(DATE_FORMAT(fa.fatura,'%d/%m/%Y %H:%i'),'') as dhFatura, 
    //                             IFNULL(DATE_FORMAT(fa.pagamento,'%d/%m/%Y %H:%i'),'') as dhPagamento, 
    //                             IFNULL(fa.situacao,'PRC') as situacaoFaturamento, dm4.descricao as descFaturamento,  
    //                             op.id as idOperador, op.operador as operadorOperacao, op.grupo, op.cpfCnpj as cpfCnpjOperacao,
    //                             IFNULL(opc.id, '') as idCobranca, IFNULL(opc.operador, op.operador) as operadorCobranca, 
    //                             IFNULL(opc.cpfCnpj, op.cpfCnpj) as cpfCnpjCobranca,
    //                             (CONCAT(IFNULL(opc.endereco, ''), ' - ', IFNULL(opc.complemento, ''), ' - ', IFNULL(opc.bairro, ''), 
    //                             ' - ', IFNULL(opc.municipio, ''), ' - ', IFNULL(opc.cidade, ''), ' - ', IFNULL(opc.estado, ''), 
    //                             ' - ', IFNULL(opc.cep, ''))) as enderecoCompleto,
    //                             (CONCAT(IFNULL(opc.contato, ''), ' - ', IFNULL(opc.email, ''), ' - ', IFNULL(opc.telefone, ''))) as contatoCompleto
    //                         FROM planta_status st 
    //                         LEFT JOIN planta_status_primeiro_movimento mpri ON mpri.idStatus = st.id
    //                         LEFT JOIN planta_status_ultimo_movimento mult ON mult.idStatus = st.id
    //                         LEFT JOIN planta_dominios dm1 ON dm1.tabela = 'planta_status' AND dm1.coluna = 'faturado' AND dm1.codigo = st.faturado
    //                         LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_status' AND dm2.coluna = 'situacao' AND dm2.codigo = st.situacao
    //                         LEFT JOIN planta_sites st ON st.id = st.idSite 
    //                         LEFT JOIN planta_sites pr ON pr.id = st.idOrigem
    //                         LEFT JOIN planta_sites de ON de.id = st.idDestino
    //                         LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //                         LEFT JOIN planta_operadores op on op.id = mt.idOperador
    //                         LEFT JOIN planta_operadores_cobranca opc on opc.id = op.idCobranca
    //                         LEFT JOIN planta_calculos ca ON ca.idStatus = st.id
    //                         LEFT JOIN planta_dominios dm3 
    //                             ON dm3.tabela = 'planta_calculos' AND dm3.coluna = 'situacao' AND dm3.codigo = IFNULL(ca.situacao,'PEN')
    //                         LEFT JOIN planta_faturamentos fa ON fa.id = ca.idFaturamento
    //                         LEFT JOIN planta_remessas rm ON rm.id = fa.idRemessa
    //                         LEFT JOIN planta_dominios dm4 
    //                             ON dm4.tabela = 'planta_faturamentos' AND dm4.coluna = 'situacao' AND dm4.codigo = IFNULL(fa.situacao,'PRC')
    //                         LEFT JOIN planta_voos_operacionais ch ON ch.id = st.idChegada
    //                         LEFT JOIN planta_voos_operacionais pa ON pa.id = st.idPartida";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "status,faturamento");
    //     break;

        case 'Tarefas':
            $_retorno = "SELECT tr.id, tr.codigo, tr.descricao, tr.email, tr.situacao, tr.cadastro, 
                            dm.descricao as descEmail, dm1.descricao as descSituacao, tr.modo, IFNULL(dm2.descricao, '') as descModo,
                            tr.tmpTolerancia, TIMESTAMPDIFF(MINUTE, tr.dhExecucao, UTC_TIMESTAMP) as tmpDiferenca,
                            DATE_FORMAT(tr.dhExecucao,'%Y-%m-%d') as dhExecucao, 
                            IFNULL(DATE_FORMAT(tr.dhExecucao,'%d/%m/%Y %H:%i'),'') as dataHoraExecucao 
                        FROM planta_tarefas tr
                        LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'simnao' and dm.codigo = tr.email
                        LEFT JOIN planta_dominios dm1 ON dm1.tabela = 'planta_todos' AND dm1.coluna = 'situacao' AND dm1.codigo = tr.situacao
                        LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_tarefas' AND dm2.coluna = 'modo' AND dm2.codigo = tr.modo";
                        $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "codigo");
        break;

    //     case 'Tarifas':
    //         $_retorno = "SELECT tr.id, tr.idSite, IFNULL(CONCAT(st.site,' - ',st.nome),'Todos os Aeroportos') as aeroportoCompleto,
    //                         tr.grupo, dm.descricao as descGrupo, tr.situacao, dm1.descricao as descSituacao,
    //                         tr.inicioPMD, tr.finalPMD, 
    //                         tr.domTPO, tr.domTPM, tr.domTPE, tr.intTPO, tr.intTPM, tr.intTPE,
    //                         tr.domTPOF, tr.domTPMF, tr.domTPEF, tr.intTPOF, tr.intTPMF, tr.intTPEF,
    //                         (CASE 	WHEN (tr.inicioPMD = 0 && tr.finalPMD = 0)
    //                                 THEN 'Não considerar PMD'
    //                             WHEN (tr.finalPMD = 0)
    //                                 THEN CONCAT('Acima de ',tr.inicioPMD) 
    //                             ELSE CONCAT('Acima de ',tr.inicioPMD,' até ', tr.finalPMD)
    //                         END) as faixaCompleta
    //                     FROM planta_tarifas tr
    //                     LEFT JOIN planta_sites st ON st.id = tr.idSite
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'grupo' and dm.codigo = tr.grupo
    //                     LEFT JOIN planta_dominios dm1 ON dm1.tabela = 'planta_todos' and dm1.coluna = 'situacao' and dm1.codigo = tr.situacao";					
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.site,tr.grupo,tr.inicioPMD");
    //     break;

        case 'TipoMenu':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                        FROM planta_dominios dm
                        WHERE dm.tabela = 'planta_menus' and dm.coluna = 'tipo'
                        ORDER BY dm.ordenacao, dm.descricao";
        break;

        case 'TodosDestaque':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                        FROM planta_dominios dm
                        WHERE dm.tabela = 'planta_todos' and dm.coluna = 'destaque'
                        ORDER BY dm.ordenacao, dm.descricao";
        break;

        case 'TodosGrupo':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                        FROM planta_dominios dm
                        WHERE dm.tabela = 'planta_todos' and dm.coluna = 'grupo'
                        ORDER BY dm.ordenacao, dm.descricao";
        break;
        
        case 'TodosSimNao':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                FROM planta_dominios dm
                WHERE dm.tabela = 'planta_todos' and dm.coluna = 'simnao'
                ORDER BY dm.ordenacao, dm.descricao";
        break;
        
        case 'TodosSistema':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                FROM planta_dominios dm
                WHERE dm.tabela = 'planta_todos' and dm.coluna = 'sistema'
                ORDER BY dm.ordenacao, dm.descricao";
        break;

        case 'TodosSituacao':
            $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
                        FROM planta_dominios dm
                        WHERE dm.tabela = 'planta_todos' and dm.coluna = 'situacao'
                        ORDER BY dm.ordenacao, dm.descricao";
        break;

    //     case 'UltimosMovimentosStatus':
    //         $_retorno = "SELECT st.id, st.idSite, st.site as operacao, st.ano, st.mes, st.numero, 
    //                         CONCAT(st.ano,'/',st.mes,'/',st.numero) as status, st.idMatricula, st.faturado, 
    //                         st.classe, st.natureza, st.servico, dm1.descricao as descFaturado, st.situacao, dm2.descricao as descSituacao, 
    //                         CONCAT(dm3.descricao,' - ',dm4.descricao,' - ',dm5.descricao) as descTipo,
    //                         dm3.descricao as descClasse, dm4.descricao as descNatureza, dm5.descricao as descServico,
    //                         st.idOrigem, IFNULL(pr.icao, '') as origem, st.idDestino, IFNULL(de.icao, '') as destino,
    //                         sm.id as idMovimento, sm.dhMovimento, sm.movimento, mo.descricao as descMovimento, 
    //                         DATE_FORMAT(sm.dhMovimento,'%d/%m/%Y %H:%i') as dataHoraMovimento, 
    //                         DATE_FORMAT(sm.dhMovimento,'%Y-%m-%d') as dtMovimento, DATE_FORMAT(sm.dhMovimento,'%H:%i') as hrMovimento,
    //                         sm.idRecurso, re.tipo as tipoRecurso, IFNULL(re.recurso,'') as descRecurso, sm.usuario,
    //                         (CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso,
    //                         sm.idSegundoRecurso, re2.tipo as tipoSegundoRecurso, IFNULL(re2.recurso,'') as descSegundoRecurso,
    //                         (CASE WHEN re2.recurso IS NULL THEN '' ELSE CONCAT(re2.tipo,' - ',re2.recurso) END) as segundoRecurso,
    //                         st.idChegada, IFNULL(CONCAT(ch.operador, ch.numeroVoo),'') as vooChegada,
    //                         st.idPartida, IFNULL(CONCAT(pa.operador, pa.numeroVoo),'') as vooPartida,
    //                         mt.matricula, eq.equipamento, eq.modelo, IFNULL(mo.destaque,'') as destaque, 
    //                         op.grupo, op.operador, IFNULL(mo.alerta,0) as alerta
    //                     FROM planta_status st
    //                     LEFT JOIN planta_status_ultimo_movimento sm ON sm.idStatus = st.id
    //                     LEFT JOIN planta_movimentos mo 
    //                         ON mo.idSite = st.idSite AND mo.movimento = sm.movimento 
    //                         AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')
    //                     LEFT JOIN planta_dominios dm1 ON dm1.tabela = 'planta_status' AND dm1.coluna = 'faturado' AND dm1.codigo = st.faturado
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_status' AND dm2.coluna = 'situacao' AND dm2.codigo = st.situacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_voos' AND dm3.coluna = 'classe' AND dm3.codigo = st.classe
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'natureza' AND dm4.codigo = st.natureza
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'servico' AND dm5.codigo = st.servico					
    //                     LEFT JOIN planta_sites st ON st.id = st.idSite 
    //                     LEFT JOIN planta_sites pr ON pr.id = st.idOrigem 
    //                     LEFT JOIN planta_sites de ON de.id = st.idDestino
    //                     LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //                     LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento
    //                     LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //                     LEFT JOIN planta_recursos re ON re.id = sm.idRecurso
    //                     LEFT JOIN planta_recursos re2 ON re2.id = sm.idSegundoRecurso
    //                     LEFT JOIN planta_voos_operacionais ch ON ch.id = st.idChegada
    //                     LEFT JOIN planta_voos_operacionais pa ON pa.id = st.idPartida"; 
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "status");
    //     break;

    //     case 'UltimosMovimentosStatusSucessora':
    //         $_retorno = "SELECT st.id, st.site, sm.movimento, sm.dhMovimento,  mo.sucessora, cl.utc, mo.antes, mo.depois
    //                     FROM planta_status st
    //                     LEFT JOIN planta_status_ultimo_movimento sm ON sm.idStatus = st.id
    //                     LEFT JOIN planta_sites st ON st.id = st.idSite 
    //                     LEFT JOIN planta_clientes cl ON cl.idSite = st.idSite AND cl.sistema = 'MAER'
    //                     LEFT JOIN planta_movimentos mo 
    //                         ON mo.idSite = st.idSite AND mo.movimento = sm.movimento 
    //                         AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')"; 
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.id");                        
    //     break;

    //     case 'UltimosMovimentosStatusIntegrado':
    //         $_retorno = "SELECT st.id, st.site, sm.movimento, sm.dhMovimento,  mo.sucessora, cl.utc, sm.idSegundoRecurso,
    //                         ((CASE WHEN op.grupo = '1' THEN cl.tmpTaxiG1 ELSE cl.tmpTaxiG2 END)) as tmpGap
    //                     FROM planta_status st
    //                     LEFT JOIN planta_status_ultimo_movimento sm ON sm.idStatus = st.id
    //                     LEFT JOIN planta_sites st ON st.id = st.idSite 
    //                     LEFT JOIN planta_clientes cl ON cl.idSite = st.idSite AND cl.sistema = 'MAER'
    //                     LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //                     LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //                     LEFT JOIN planta_movimentos mo 
    //                         ON mo.idSite = st.idSite AND mo.movimento = sm.movimento 
    //                         AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')"; 
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "st.id");                        
    //     break;

    //     case 'UltimosMovimentosVoos':
    //         $_retorno = "SELECT vo.id, vo.idSite, vo.operacao, vo.operador, vo.numeroVoo, 
    //                         CONCAT(vo.operador, '', vo.numeroVoo) as voo, vo.equipamento, vo.assentos, 
    //                         IFNULL(vo.pax,'') as pax, IFNULL(vo.pnae,'') as pnae, 
    //                         DATE_FORMAT(vo.dtMovimento,'%d/%m/%Y') as dtMovimento,
    //                         DATE_FORMAT(vo.dhPrevista,'%d/%m/%Y %H:%i') as dhPrevista,
    //                         DATE_FORMAT(hc.dhConfirmada,'%d/%m/%Y %H:%i') as dhConfirmada,
    //                         vo.classe, vo.numeroEtapa, vo.origem, vo.destino, vo.servico, vo.natureza, 
    //                         vo.codeshare, LEFT(vo.codeshare, 8) as parteCodeshare, vo.cadastro, 
    //                         dm3.descricao as descClasse, dm4.descricao as descNatureza, dm5.descricao as descServico,
    //                         CONCAT(dm3.descricao,' - ',dm4.descricao,' - ',dm5.descricao) as descTipo,
    //                         (CASE WHEN vo.origem <> '' THEN CONCAT(og.nome,' - ',og.localidade,' - ',og.pais) ELSE '' END) as descOrigemCompleta,
    //                         (CASE WHEN vo.destino <> '' THEN CONCAT(de.nome,' - ',de.localidade,' - ',de.pais) ELSE '' END) as descDestinoCompleto,
    //                         (CASE WHEN vo.origem <> '' THEN CONCAT(og.icao,' - ',og.localidade) ELSE '' END) as descOrigem,
    //                         (CASE WHEN vo.destino <> '' THEN CONCAT(de.icao,' - ',de.localidade) ELSE '' END) as descDestino,
    //                         vo.situacao, dm2.descricao as descSituacao,
    //                         vm.id as idMovimento, vm.dhMovimento, IFNULL(DATE_FORMAT(vm.dhMovimento,'%d/%m/%Y %H:%i'),'') as dataHoraMovimento,
    //                         DATE_FORMAT(vm.dhMovimento,'%Y-%m-%d') as dtMovimento, DATE_FORMAT(vm.dhMovimento,'%H:%i') as hrMovimento,
    //                         IFNULL(vm.movimento, '') as movimento, IFNULL(mo.descricao,'') as descMovimento, vm.usuario,
    //                         vm.idRecurso, (CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso,
    //                         vo.idChegada, IFNULL(CONCAT(ch.operador, '', ch.numeroVoo),'') as vooChegada,
    //                         vo.idPartida, IFNULL(CONCAT(pa.operador, '', pa.numeroVoo),'') as vooPartida,
    //                         sch.id as idStatusChegada, IFNULL(CONCAT(sch.ano,'/',sch.mes,'/',sch.numero),'') as statusChegada, 
    //                         spa.id as idStatusPartida, IFNULL(CONCAT(spa.ano,'/',spa.mes,'/',spa.numero),'') as statusPartida, 
    //                         vo.idPosicao, IFNULL(po.recurso,'') as posicao,
    //                         vo.idEsteira, IFNULL(es.recurso,'') as esteira,
    //                         vo.idPortao, IFNULL(pr.recurso,'') as portao, IFNULL(mo.destaque,'') as destaque, 
    //                         IFNULL(mo.alerta,0) as alerta
    //                     FROM planta_voos_operacionais vo
    //                     LEFT JOIN planta_voos_ultimo_movimento vm ON vm.idVoo = vo.id
    //                     LEFT JOIN planta_voos_horario_confirmado hc ON hc.idVoo = vo.id
    //                     LEFT JOIN planta_movimentos mo ON mo.idSite = vo.idSite AND mo.movimento = vm.movimento AND (mo.operacao = 'TDS' OR mo.operacao = vo.operacao)
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_voos' AND dm2.coluna = 'situacao' AND dm2.codigo = vo.situacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_voos' AND dm3.coluna = 'classe' AND dm3.codigo = vo.classe
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'natureza' AND dm4.codigo = vo.natureza
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'servico' AND dm5.codigo = vo.servico
    //                     LEFT JOIN planta_sites st ON st.id = vo.idSite 
    //                     LEFT JOIN planta_sites og ON og.icao = vo.origem
    //                     LEFT JOIN planta_sites de ON de.icao = vo.destino
    //                     LEFT JOIN planta_recursos re ON re.id = vm.idRecurso
    //                     LEFT JOIN planta_voos_operacionais ch ON ch.id = vo.idChegada
    //                     LEFT JOIN planta_voos_operacionais pa ON pa.id = vo.idPartida
    //                     LEFT JOIN planta_status sch ON sch.idChegada = vo.id
    //                     LEFT JOIN planta_status spa ON spa.idPartida = vo.id
    //                     LEFT JOIN planta_recursos po ON po.id = vo.idPosicao
    //                     LEFT JOIN planta_recursos es ON es.id = vo.idEsteira
    //                     LEFT JOIN planta_recursos pr ON pr.id = vo.idPortao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vo.dhPrevista,vo.operacao,vo.operador,vo.numeroVoo");                        
    //     break;

    //     case 'UltimosMovimentosVoosSucessora':
    //         $_retorno = "SELECT vo.id, st.site, vm.movimento, vm.dhMovimento, mo.sucessora, mo.antes, mo.depois, 
    //                             cl.utc, DATE_ADD(UTC_TIMESTAMP(), INTERVAL cl.utc HOUR)
    //                     FROM planta_voos_operacionais vo
    //                     LEFT JOIN planta_voos_ultimo_movimento vm ON vm.idVoo = vo.id
    //                     LEFT JOIN planta_sites st ON st.id = vo.idSite 
    //                     LEFT JOIN planta_clientes cl ON cl.idSite = vo.idSite AND cl.sistema = 'MAER'
    //                     LEFT JOIN planta_movimentos mo 
    //                         ON mo.idSite = vo.idSite AND mo.movimento = vm.movimento 
    //                         AND (mo.operacao = 'TDS' OR mo.operacao = vo.operacao)"; 
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vo.id");                        
    //     break;

    //     case 'UltimoIdMovimentoStatus':
    //         $_retorno = "SELECT IFNULL(id,'') as id FROM planta_status_ultimo_movimento WHERE idStatus = ".$_busca;
    //     break;

    //     case 'UltimoIdMovimentoVoo':
    //         $_retorno = "SELECT IFNULL(id,'') as id FROM planta_voos_ultimo_movimento WHERE idVoo = ".$_busca;
    //     break;

        case 'Usuarios':
            $_retorno = "SELECT us.id, us.usuario, us.nome, us.email, us.situacao, dm.descricao as descSituacao,
                    CONCAT(us.usuario,' - ',us.nome) as usuarioCompleto, us.celular,
                    us.id as codigo, CONCAT(us.usuario,' - ',us.nome) as descricao
                FROM planta_usuarios us
                LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = us.situacao";
            $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
            $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "usuario");
        break;

    //     case 'VistoriaItens':
    //         $_retorno = "SELECT vi.id, vi.idSite, vi.tipo, vi.numero, vi.item, vi.situacao, dm.descricao as descSituacao, 
    //                         dm2.descricao as descTipo, st.site, (CASE WHEN st.site = 'ZZZZ' THEN 'Padrão' ELSE st.site END) as aeroporto,
    //                         CONCAT(vi.numero,' - ',vi.item) as descItem, '' as parecer,
    //                         vi.id as codigo, CONCAT(vi.numero,' - ',vi.item) as descricao
    //                     FROM planta_vistoria_itens vi
    //                     LEFT JOIN planta_sites st ON st.id = vi.idSite 
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = vi.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_vistoria_itens' and dm2.coluna = 'tipo' and dm2.codigo = vi.tipo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vi.tipo,vi.numero");
    //     break;

    //     case 'VistoriaItensAgendamentos':
    //         $_retorno = "SELECT vp.id, vp.idSite, vp.numero, vp.finalidade, vp.frequencia, vp.quantidade, vp.periodo,
    //                             vp.situacao, DATE_FORMAT(vp.inicio,'%d/%m/%Y') as dataInicio, vp.mapa,
    //                             DATE_FORMAT(vp.inicio,'%Y-%m-%d') as dtInicio, dm.descricao as descSituacao, 
    //                             dm2.descricao as descFrequencia, dm3.descricao as descQuantidade, dm4.descricao as descPeriodo, 
    //                             dm5.descricao as descSituacao, st.site, 
    //                             va.id as idAgendamento, va.numero as numeroAgendamento, va.inicio, va.local as localAgendamento,
    //                             DATE_FORMAT(va.inicio,'%d/%m/%Y') as dataInicioAgendamento, 
    //                             va.final, DATE_FORMAT(va.final,'%d/%m/%Y') as dataFinalAgendamento, 
    //                             va.periodo, dm6.descricao as descPeriodoAgendamento, 
    //                             va.execucao, IFNULL(DATE_FORMAT(va.execucao,'%d/%m/%Y %H:%i'),'') as dataExecucao, 
    //                             va.idUsuario, IFNULL(us.usuario,'') as usuarioExecucao,
    //                             vi.id as idItemParecer, vi.tipo, dm7.descricao as descTipo, vi.numero as numeroItem, vi.item, 
    //                             '' as parecer
    //                         FROM planta_vistoria_planos vp
    //                         LEFT JOIN planta_sites st ON st.id = vp.idSite 
    //                         LEFT JOIN planta_vistoria_agendamentos va ON va.idPlano = vp.id
    //                         LEFT JOIN planta_vistoria_planos_itens vpi ON vpi.idPlano = vp.id
    //                         LEFT JOIN planta_vistoria_itens vi ON vi.id = vpi.idItem
    //                         LEFT JOIN planta_usuarios us ON us.id = va.idUsuario
    //                         LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = vp.situacao
    //                         LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_vistoria_planos' and dm2.coluna = 'frequencia' and dm2.codigo = vp.frequencia
    //                         LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_vistoria_planos' and dm3.coluna = 'quantidade' and dm3.codigo = vp.quantidade
    //                         LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_vistoria_planos' and dm4.coluna = 'periodo' and dm4.codigo = vp.periodo
    //                         LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_vistoria_planos' and dm5.coluna = 'situacao' and dm5.codigo = vp.situacao
    //                         LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_vistoria_planos' and dm6.coluna = 'periodo' and dm6.codigo = va.periodo
    //                         LEFT JOIN planta_dominios dm7 ON dm7.tabela = 'planta_vistoria_itens' and dm7.coluna = 'tipo' and dm7.codigo = vi.tipo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vi.numero");
    //     break;

    //     case 'VistoriaItensResultados':
    //         $_retorno = "SELECT vp.id, vp.idSite, vp.numero, vp.finalidade, vp.frequencia, vp.quantidade, vp.periodo,
    //                             vp.situacao, DATE_FORMAT(vp.inicio,'%d/%m/%Y') as dataInicio, vp.mapa,
    //                             DATE_FORMAT(vp.inicio,'%Y-%m-%d') as dtInicio, dm.descricao as descSituacao, 
    //                             dm2.descricao as descFrequencia, dm3.descricao as descQuantidade, dm4.descricao as descPeriodo, 
    //                             dm5.descricao as descSituacao, st.site, 
    //                             va.id as idAgendamento, va.numero as numeroAgendamento, va.inicio, va.local as localAgendamento,
    //                             DATE_FORMAT(va.inicio,'%d/%m/%Y') as dataInicioAgendamento, 
    //                             va.final, DATE_FORMAT(va.final,'%d/%m/%Y') as dataFinalAgendamento, 
    //                             va.periodo, dm6.descricao as descPeriodoAgendamento, 
    //                             va.execucao, IFNULL(DATE_FORMAT(va.execucao,'%d/%m/%Y %H:%i'),'') as dataExecucao, 
    //                             va.idUsuario, IFNULL(us.usuario,'') as usuarioExecucao,
    //                             vr.id as idResultado, vr.idItem as idItemParecer, vr.local as localResultado, vr.parecer, 
    //                             vi.tipo, dm7.descricao as descTipo, vi.numero as numeroItem, vi.item
    //                         FROM planta_vistoria_planos vp
    //                         LEFT JOIN planta_sites st ON st.id = vp.idSite 
    //                         LEFT JOIN planta_vistoria_agendamentos va ON va.idPlano = vp.id
    //                         LEFT JOIN planta_vistoria_resultados vr ON vr.idAgendamento = va.id
    //                         LEFT JOIN planta_vistoria_itens vi ON vi.id = vr.idItem 
    //                         LEFT JOIN planta_usuarios us ON us.id = va.idUsuario
    //                         LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = vp.situacao
    //                         LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_vistoria_planos' and dm2.coluna = 'frequencia' and dm2.codigo = vp.frequencia
    //                         LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_vistoria_planos' and dm3.coluna = 'quantidade' and dm3.codigo = vp.quantidade
    //                         LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_vistoria_planos' and dm4.coluna = 'periodo' and dm4.codigo = vp.periodo
    //                         LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_vistoria_planos' and dm5.coluna = 'situacao' and dm5.codigo = vp.situacao
    //                         LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_vistoria_planos' and dm6.coluna = 'periodo' and dm6.codigo = va.periodo
    //                         LEFT JOIN planta_dominios dm7 ON dm7.tabela = 'planta_vistoria_itens' and dm7.coluna = 'tipo' and dm7.codigo = vi.tipo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vi.numero");
    //     break;

    //     case 'VistoriaItensTipos':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_vistoria_itens' and dm.coluna = 'tipo'";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "")." ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'VistoriaPlanos':
    //         $_retorno = "SELECT vp.id, vp.idSite, vp.numero, vp.finalidade, vp.frequencia, vp.quantidade, vp.periodo, 
    //                         vp.situacao, DATE_FORMAT(vp.inicio,'%d/%m/%Y') as dataInicio, vp.mapa, 
    //                         CONCAT(vp.numero,' - ',vp.finalidade) as planoCompleto,
    //                         DATE_FORMAT(vp.inicio,'%Y-%m-%d') as dtInicio, dm.descricao as descSituacao, 
    //                         dm2.descricao as descFrequencia, dm3.descricao as descQuantidade, dm4.descricao as descPeriodo, 
    //                         st.site, cl.utc,
    //                         vp.id as codigo, CONCAT(vp.numero,' - ',vp.finalidade) as descricao
    //                     FROM planta_vistoria_planos vp
    //                     LEFT JOIN planta_sites st ON st.id = vp.idSite 
    //                     LEFT JOIN planta_clientes cl ON cl.idSite = vp.idSite AND cl.sistema = 'VAER'
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_vistoria_planos' and dm.coluna = 'situacao' and dm.codigo = vp.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_vistoria_planos' and dm2.coluna = 'frequencia' and dm2.codigo = vp.frequencia
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_vistoria_planos' and dm3.coluna = 'quantidade' and dm3.codigo = vp.quantidade
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_vistoria_planos' and dm4.coluna = 'periodo' and dm4.codigo = vp.periodo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vp.numero, dtInicio");
    //     break;

    //     case 'VistoriaPlanosAgendamentos':
    //         $_retorno = "SELECT vp.id, vp.idSite, vp.numero, vp.finalidade, vp.frequencia, vp.quantidade, vp.periodo,
    //                         vp.situacao, DATE_FORMAT(vp.inicio,'%d/%m/%Y') as dataInicio, vp.mapa,
    //                         DATE_FORMAT(vp.inicio,'%Y-%m-%d') as dtInicio, dm.descricao as descSituacao, 
    //                         dm2.descricao as descFrequencia, dm3.descricao as descQuantidade, dm4.descricao as descPeriodo, 
    //                         dm5.descricao as descSituacao, st.site, 
    //                         va.id as idAgendamento, va.numero as numeroAgendamento, va.inicio, 
    //                         DATE_FORMAT(va.inicio,'%d/%m/%Y') as dataInicioAgendamento, 
    //                         va.final, DATE_FORMAT(va.final,'%d/%m/%Y') as dataFinalAgendamento, 
    //                         va.periodo, dm6.descricao as descPeriodoAgendamento, 
    //                         va.execucao, IFNULL(DATE_FORMAT(va.execucao,'%d/%m/%Y %H:%i'),'') as dataExecucao, 
    //                         va.idUsuario, IFNULL(us.usuario,'') as usuarioExecucao
    //                     FROM planta_vistoria_planos vp
    //                     LEFT JOIN planta_sites st ON st.id = vp.idSite 
    //                     LEFT JOIN planta_vistoria_agendamentos va ON va.idPlano = vp.id
    //                     LEFT JOIN planta_usuarios us ON us.id = va.idUsuario
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = vp.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_vistoria_planos' and dm2.coluna = 'frequencia' and dm2.codigo = vp.frequencia
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_vistoria_planos' and dm3.coluna = 'quantidade' and dm3.codigo = vp.quantidade
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_vistoria_planos' and dm4.coluna = 'periodo' and dm4.codigo = vp.periodo
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_vistoria_planos' and dm5.coluna = 'situacao' and dm5.codigo = vp.situacao
    //                     LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_vistoria_planos' and dm6.coluna = 'periodo' and dm6.codigo = va.periodo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vp.numero, dtInicio, va.numero");
    //     break;

    //     case 'VistoriaPlanosFrequencia':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_vistoria_planos' and dm.coluna = 'frequencia'";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "")." ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'VistoriaPlanosItens':
    //         $_retorno = "SELECT vp.id, vp.idSite, vp.numero, vp.finalidade, vp.frequencia, vp.quantidade, vp.periodo,
    //                         vp.situacao, DATE_FORMAT(vp.inicio,'%d/%m/%Y') as dataInicio, vp.mapa,
    //                         DATE_FORMAT(vp.inicio,'%Y-%m-%d') as dtInicio, dm.descricao as descSituacao, 
    //                         dm2.descricao as descFrequencia, dm3.descricao as descQuantidade, dm4.descricao as descPeriodo,
    //                         dm5.descricao as descSituacao, st.site, vpi.id as idPlanoItem,
    //                         vpi.idItem, vi.tipo, dm6.descricao as descTipo, vi.numero as numeroItem, vi.item
    //                     FROM planta_vistoria_planos vp
    //                     LEFT JOIN planta_sites st ON st.id = vp.idSite 
    //                     LEFT JOIN planta_vistoria_planos_itens vpi ON vpi.idPlano = vp.id
    //                     LEFT JOIN planta_vistoria_itens vi ON vi.id = vpi.idItem
    //                     LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' and dm.coluna = 'situacao' and dm.codigo = vp.situacao
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_vistoria_planos' and dm2.coluna = 'frequencia' and dm2.codigo = vp.frequencia
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_vistoria_planos' and dm3.coluna = 'quantidade' and dm3.codigo = vp.quantidade
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_vistoria_planos' and dm4.coluna = 'periodo' and dm4.codigo = vp.periodo
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_vistoria_planos' and dm5.coluna = 'situacao' and dm5.codigo = vp.situacao
    //                     LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_vistoria_itens' and dm6.coluna = 'tipo' and dm6.codigo = vi.tipo";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vp.numero, vp.inicio, vi.numero");
    //     break;

    //     case 'VistoriaPlanosPeriodo':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_vistoria_planos' and dm.coluna = 'periodo'";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "")." ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'VistoriaPlanosQuantidade':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_vistoria_planos' and dm.coluna = 'quantidade'";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "")." ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'VistoriaPlanosSituacao':
    //         $_retorno = "SELECT dm.tabela, dm.coluna, dm.codigo, dm.descricao, dm.ordenacao
    //                     FROM planta_dominios dm
    //                     WHERE dm.tabela = 'planta_vistoria_planos' and dm.coluna = 'situacao'";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "")." ORDER BY dm.ordenacao, dm.descricao";
    //     break;

    //     case 'VistoriaUsuarios':
    //         $_retorno = "SELECT DISTINCT va.idUsuario as codigo, IFNULL(us.usuario,'') as descricao
    //                         FROM planta_vistoria_planos vp
    //                         LEFT JOIN planta_sites st ON st.id = vp.idSite 
    //                         LEFT JOIN planta_vistoria_agendamentos va ON va.idPlano = vp.id
    //                         LEFT JOIN planta_usuarios us ON us.id = va.idUsuario
    //                         WHERE 1 = 1 AND va.idUsuario IS NOT NULL";
    //         $_retorno .= ($_filtro != '' ? $_filtro : "")." ORDER BY us.usuario";
    //     break;

    //     case 'VoosANAC':
    //         $_retorno = "SELECT vr.id, vr.operador, vr.empresa, vr.numeroVoo, vr.equipamento, CONCAT(vr.operador, '', vr.numeroVoo) as voo,
    //                         vr.segunda, vr.terca, vr.quarta, vr.quinta, vr.sexta, vr.sabado, vr.domingo, 
    //                         CONCAT(IF(vr.segunda = 0, '', vr.segunda),IF(vr.terca = 0, '', vr.terca),IF(vr.quarta = 0, '', vr.quarta),
    //                                 IF(vr.quinta = 0, '', vr.quinta),IF(vr.sexta = 0, '', vr.sexta),IF(vr.sabado = 0, '', vr.sabado),
    //                                 IF(vr.domingo = 0, '', vr.domingo)) as frequencia,
    //                         vr.assentos, vr.siros, vr.situacaoSiros,
    //                         DATE_FORMAT(vr.dataRegistro,'%d/%m/%Y') as dataRegistro,
    //                         DATE_FORMAT(vr.inicioOperacao,'%d/%m/%Y') as inicioOperacao,
    //                         DATE_FORMAT(vr.fimOperacao,'%d/%m/%Y') as fimOperacao,
    //                         vr.naturezaOperacao, vr.numeroEtapa, 
    //                         vr.icaoOrigem, vr.aeroportoOrigem, vr.icaoDestino, vr.aeroportoDestino, 
    //                         vr.horarioPartida, vr.horarioChegada,";
    
    //         // Montagem do campo horário de operação, 
    //         // Verificar $busca éstá preenchido com o icao do Aeroporto solicitado para pegar o horario de Partida ou Chegada, senão monta branco
    //         if (!empty($_busca)) {
    //             $_retorno .= " IF(vr.icaoOrigem = '".$_busca."', vr.horarioPartida, vr.horarioChegada) as horarioOperacao,";
    //         } else {
    //             $_retorno .= " '' as horarioOperacao,";
    //         }		
    
    //         $_retorno .= "	vr.servico, vr.objetoTransporte, 
    //                         vr.codeshare, LEFT(vr.codeshare, 8) as parteCodeshare,
    //                         vr.situacao, vr.origem, vr.cadastro
    //                     FROM planta_voos_anac vr";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vr.operador,vr.numeroVoo");
    //     break;

    //     case 'VoosOperacionais':
    //         // Pega o horário do último movimento CNF, se nulo utiliza a dhPrevista do voo
    //         //
    //         $_retorno = "SELECT vo.id, vo.idSite, vo.operacao, vo.operador, vo.numeroVoo, 
    //                         CONCAT(vo.operador, '', vo.numeroVoo) as voo, vo.equipamento, vo.assentos, 
    //                         IFNULL(vo.pax,'') as pax, IFNULL(vo.pnae,'') as pnae, 
    //                         DATE_FORMAT(vo.dtMovimento,'%d/%m/%Y') as dtMovimento,
    //                         DATE_FORMAT(vo.dhPrevista,'%d/%m/%Y %H:%i') as dhPrevista,
    //                         DATE_FORMAT(hc.dhConfirmada,'%d/%m/%Y %H:%i') as dhConfirmada,
    //                         vo.classe, vo.numeroEtapa, vo.origem, vo.destino, vo.servico, vo.natureza, 
    //                         vo.codeshare, LEFT(vo.codeshare, 8) as parteCodeshare, vo.situacao, dm2.descricao as descSituacao, vo.cadastro, 
    //                         dm3.descricao as descClasse, dm4.descricao as descNatureza, dm5.descricao as descServico,
    //                         CONCAT(dm3.descricao,' - ',dm4.descricao,' - ',dm5.descricao) as descTipo,
    //                         (CASE WHEN vo.origem <> '' THEN CONCAT(og.nome,' - ',og.localidade,' - ',og.pais) ELSE '' END) as descOrigemCompleta,
    //                         (CASE WHEN vo.destino <> '' THEN CONCAT(de.nome,' - ',de.localidade,' - ',de.pais) ELSE '' END) as descDestinoCompleto,
    //                         (CASE WHEN vo.origem <> '' THEN CONCAT(og.icao,' - ',og.localidade) ELSE '' END) as descOrigem,
    //                         (CASE WHEN vo.destino <> '' THEN CONCAT(de.icao,' - ',de.localidade) ELSE '' END) as descDestino,
    //                         vm.id as idMovimento, IFNULL(vm.movimento, '') as movimento, IFNULL(mo.descricao,'') as descMovimento, 
    //                         vm.idRecurso, vm.dhMovimento, IFNULL(DATE_FORMAT(vm.dhMovimento,'%d/%m/%Y %H:%i'),'') as dataHoraMovimento, 
    //                         DATE_FORMAT(vm.dhMovimento,'%Y-%m-%d') as dtMovimento, DATE_FORMAT(vm.dhMovimento,'%H:%i') as hrMovimento,
    //                         (CASE WHEN re.recurso IS NULL THEN '' ELSE CONCAT(re.tipo,' - ',re.recurso) END) as recurso, vm.usuario,
    //                         pm.dhMovimento as dhPrimeiroMovimento, um.id as idUltimoMovimento, vm.usuario,
    //                         vo.idChegada, IFNULL(CONCAT(ch.operador, '', ch.numeroVoo),'') as vooChegada,
    //                         vo.idPartida, IFNULL(CONCAT(pa.operador, '', pa.numeroVoo),'') as vooPartida,
    //                         sch.id as idStatusChegada, IFNULL(CONCAT(sch.ano,'/',sch.mes,'/',sch.numero),'') as statusChegada, 
    //                         spa.id as idStatusPartida, IFNULL(CONCAT(spa.ano,'/',spa.mes,'/',spa.numero),'') as statusPartida, 
    //                         vo.idPosicao, IFNULL(po.recurso,'') as posicao,
    //                         vo.idEsteira, IFNULL(es.recurso,'') as esteira,
    //                         vo.idPortao, IFNULL(pr.recurso,'') as portao, IFNULL(mo.destaque,'') as destaque
    //                     FROM planta_voos_operacionais vo
    //                     LEFT JOIN planta_voos_movimentos vm ON vm.idVoo = vo.id  
    //                     LEFT JOIN planta_voos_primeiro_movimento pm ON pm.idVoo = vo.id
    //                     LEFT JOIN planta_voos_ultimo_movimento um ON um.idVoo = vo.id
    //                     LEFT JOIN planta_voos_horario_confirmado hc ON hc.idVoo = vo.id
    //                     LEFT JOIN planta_movimentos mo ON mo.idSite = vo.idSite AND mo.movimento = vm.movimento AND (mo.operacao = 'TDS' OR mo.operacao = vo.operacao)
    //                     LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_voos' AND dm2.coluna = 'situacao' AND dm2.codigo = vo.situacao
    //                     LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_voos' AND dm3.coluna = 'classe' AND dm3.codigo = vo.classe
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'natureza' AND dm4.codigo = vo.natureza
    //                     LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'servico' AND dm5.codigo = vo.servico
    //                     LEFT JOIN planta_sites st ON st.id = vo.idSite 
    //                     LEFT JOIN planta_sites og ON og.icao = vo.origem
    //                     LEFT JOIN planta_sites de ON de.icao = vo.destino
    //                     LEFT JOIN planta_recursos re ON re.id = vm.idRecurso
    //                     LEFT JOIN planta_voos_operacionais ch ON ch.id = vo.idChegada
    //                     LEFT JOIN planta_voos_operacionais pa ON pa.id = vo.idPartida
    //                     LEFT JOIN planta_status sch ON sch.idChegada = vo.id
    //                     LEFT JOIN planta_status spa ON spa.idPartida = vo.id
    //                     LEFT JOIN planta_recursos po ON po.id = vo.idPosicao
    //                     LEFT JOIN planta_recursos es ON es.id = vo.idEsteira
    //                     LEFT JOIN planta_recursos pr ON pr.id = vo.idPortao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vo.dhPrevista,vo.operacao,vo.operador,vo.numeroVoo");
    //     break;

    //     case 'VoosPlanejados':
    //         $_retorno = "SELECT vp.id, vp.idSite, vp.operador, vp.empresa, vp.numeroVoo, vp.equipamento, CONCAT(vp.operador, '', vp.numeroVoo) as voo,
    //                         vp.segunda, vp.terca, vp.quarta, vp.quinta, vp.sexta, vp.sabado, vp.domingo, 
    //                         CONCAT(IF(vp.segunda = 0, '', vp.segunda),IF(vp.terca = 0, '', vp.terca),IF(vp.quarta = 0, '', vp.quarta),
    //                                 IF(vp.quinta = 0, '', vp.quinta),IF(vp.sexta = 0, '', vp.sexta),IF(vp.sabado = 0, '', vp.sabado),
    //                                 IF(vp.domingo = 0, '', vp.domingo)) as frequencia,
    //                         vp.assentos, vp.siros, vp.situacaoSiros,
    //                         DATE_FORMAT(vp.dataRegistro,'%d/%m/%Y') as dataRegistro,
    //                         DATE_FORMAT(vp.inicioOperacao,'%d/%m/%Y') as inicioOperacao,
    //                         DATE_FORMAT(vp.fimOperacao,'%d/%m/%Y') as fimOperacao,
    //                         vp.naturezaOperacao, vp.numeroEtapa, 
    //                         vp.icaoOrigem, vp.icaoDestino,
    //                         vp.horarioPartida, vp.horarioChegada,";
    
    //         // Montagem do campo horário de operação, 
    //         // Verificar $busca éstá preenchido com o icao do Aeroporto solicitado para pegar o horario de Partida ou Chegada, senão monta branco
    //         if (!empty($_busca)) {
    //             $_retorno .= " IF(vp.icaoOrigem = '".$_busca."', vp.horarioPartida, vp.horarioChegada) as horarioOperacao,";
    //         } else {
    //             $_retorno .= " '' as horarioOperacao,";
    //         }		
    
    //         $_retorno .= "	vp.servico, vp.objetoTransporte, 
    //                         vp.codeshare, LEFT(vp.codeshare, 8) as parteCodeshare,
    //                         vp.situacao, vp.fonte, vp.origem, vp.cadastro
    //                     FROM planta_voos_planejados vp
    //                     LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_todos' and dm4.coluna = 'origem' and dm4.codigo = vp.origem";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "vp.operador,vp.numeroVoo");
    //     break;  

    //     // Querys para estatísticas
    //     // Grupamentos devem ser feitos externamente
    //     case 'EstatisticasPousosDecolagens':
    //         $_retorno = "SELECT sm.dhMovimento, re.recurso, CONCAT(eq.equipamento,' - ',eq.modelo) as equipamento, op.operador, op.grupo,
    //                         st.classe, st.natureza, st.servico, 
    //                         (CASE WHEN sm.movimento = 'POU' THEN 1 ELSE 0 END) as pouso, 
    //                         (CASE WHEN sm.movimento = 'DEC' THEN 1 ELSE 0 END) as decolagem,
    //                         IFNULL(dm.descricao,'Não definido') as descMes, IFNULL(dm2.descricao,'Não definido') as descDiaSemana, 
    //                         IFNULL(dm3.descricao,'Não definido') as descGrupo, IFNULL(dm4.descricao,'Não definida') as descClasse, 
    //                         IFNULL(dm5.descricao,'Não definida') as descNatureza, IFNULL(dm6.descricao,'Não definida') as descServico
    //             FROM planta_status st
    //             LEFT JOIN planta_status_movimentos sm ON sm.idStatus = st.id 
    //             LEFT JOIN planta_movimentos mo ON mo.idSite = st.idSite 
    //                 AND mo.movimento = sm.movimento AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')
    //             LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //             LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento
    //             LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //             LEFT JOIN planta_recursos re ON re.id = sm.idRecurso
    //             LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' AND dm.coluna = 'mes' AND dm.codigo = MONTH(sm.dhMovimento)
    //             LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' AND dm2.coluna = 'dia' AND dm2.codigo = DAYOFWEEK(sm.dhMovimento)
    //             LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' AND dm3.coluna = 'grupo' AND dm3.codigo = op.grupo
    //             LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'classe' AND dm4.codigo = st.classe
    //             LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'natureza' AND dm.codigo = st.natureza
    //             LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_voos' AND dm6.coluna = 'servico' AND dm6.codigo = st.servico";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "sm.dhMovimento");
    //     break;

    //     case 'EstatisticasEntradasSaidas':
    //         $_retorno = "SELECT sm.dhMovimento, re.recurso, re.utilizacao, CONCAT(eq.equipamento,' - ',eq.modelo) as equipamento, 
    //                         st.classe, st.natureza, st.servico, 
    //                         op.operador, op.grupo,
    //                         (CASE WHEN sm.movimento = 'ENT' THEN 1 ELSE 0 END) as entrada, 
    //                         (CASE WHEN sm.movimento = 'SAI' THEN 1 ELSE 0 END) as saida,
    //                         IFNULL(dm.descricao,'Não definido') as descMes, IFNULL(dm2.descricao,'Não definido') as descDiaSemana, 
    //                         IFNULL(dm3.descricao,'Não definido') as descGrupo, IFNULL(dm4.descricao,'Não definida') as descClasse, 
    //                         IFNULL(dm5.descricao,'Não definida') as descNatureza, IFNULL(dm6.descricao,'Não definido') as descServico, 
    //                         IFNULL(dm7.descricao,'Não definida') as descUtilizacao
    //             FROM planta_status st
    //             LEFT JOIN planta_status_movimentos sm ON sm.idStatus = st.id 
    //             LEFT JOIN planta_movimentos mo ON mo.idSite = st.idSite 
    //                 AND mo.movimento = sm.movimento AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')
    //             LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //             LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento
    //             LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //             LEFT JOIN planta_recursos re ON re.id = sm.idRecurso
    //             LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' AND dm.coluna = 'mes' AND dm.codigo = MONTH(sm.dhMovimento)
    //             LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' AND dm2.coluna = 'dia' AND dm2.codigo = DAYOFWEEK(sm.dhMovimento)
    //             LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' AND dm3.coluna = 'grupo' AND dm3.codigo = op.grupo
    //             LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'classe' AND dm4.codigo = st.classe
    //             LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'natureza' AND dm.codigo = st.natureza
    //             LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_voos' AND dm6.coluna = 'servico' AND dm6.codigo = st.servico
    //             LEFT JOIN planta_dominios dm7 ON dm7.tabela = 'planta_recursos' AND dm7.coluna = 'utilizacao' AND dm7.codigo = re.utilizacao";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "sm.dhMovimento");
    //     break;

    //     case 'EstatisticasPassageiros':
    //         $_retorno = "SELECT sm.dhMovimento, re.recurso, re.utilizacao, CONCAT(eq.equipamento,' - ',eq.modelo) as equipamento, 
    //                         st.classe, st.natureza, st.servico, 
    //                         op.operador, op.grupo,
    //                         (CASE WHEN sm.movimento = 'POU' THEN sc.desembarque_pax ELSE 0 END) as desembarque, 
    //                         (CASE WHEN sm.movimento = 'DEC' THEN sc.embarque_pax ELSE 0 END) as embarque,
    //                         (CASE WHEN sm.movimento = 'DEC' THEN sc.transito_pax ELSE 0 END) as transito,
    //                         IFNULL(dm.descricao,'Não definido') as descMes, IFNULL(dm2.descricao,'Não definido') as descDiaSemana, 
    //                         IFNULL(dm3.descricao,'Não definido') as descGrupo, IFNULL(dm4.descricao,'Não definida') as descClasse, 
    //                         IFNULL(dm5.descricao,'Não definida') as descNatureza, IFNULL(dm6.descricao,'Não definido') as descServico
    //             FROM planta_status st
    //             LEFT JOIN planta_status_movimentos sm ON sm.idStatus = st.id 
    //             LEFT JOIN planta_status_complementos sc ON sc.idStatus = st.id
    //             LEFT JOIN planta_movimentos mo ON mo.idSite = st.idSite 
    //                 AND mo.movimento = sm.movimento AND (mo.operacao = 'TDS' OR mo.operacao = 'STA')
    //             LEFT JOIN planta_matriculas mt ON mt.id = st.idMatricula
    //             LEFT JOIN planta_equipamentos eq ON eq.id = mt.idEquipamento
    //             LEFT JOIN planta_operadores op ON op.id = mt.idOperador
    //             LEFT JOIN planta_recursos re ON re.id = sm.idRecurso
    //             LEFT JOIN planta_dominios dm ON dm.tabela = 'planta_todos' AND dm.coluna = 'mes' AND dm.codigo = MONTH(sm.dhMovimento)
    //             LEFT JOIN planta_dominios dm2 ON dm2.tabela = 'planta_todos' AND dm2.coluna = 'dia' AND dm2.codigo = DAYOFWEEK(sm.dhMovimento)
    //             LEFT JOIN planta_dominios dm3 ON dm3.tabela = 'planta_todos' AND dm3.coluna = 'grupo' AND dm3.codigo = op.grupo
    //             LEFT JOIN planta_dominios dm4 ON dm4.tabela = 'planta_voos' AND dm4.coluna = 'classe' AND dm4.codigo = st.classe
    //             LEFT JOIN planta_dominios dm5 ON dm5.tabela = 'planta_voos' AND dm5.coluna = 'natureza' AND dm.codigo = st.natureza
    //             LEFT JOIN planta_dominios dm6 ON dm6.tabela = 'planta_voos' AND dm6.coluna = 'servico' AND dm6.codigo = st.servico";
    //         $_retorno .= " WHERE 1 = 1 ".($_filtro != '' ? $_filtro : "");
    //         $_retorno .= " ORDER BY ".($_ordem != '' ? $_ordem : "sm.dhMovimento");
    //     break;

    //     default:
    //         $_retorno = "";
    }

    return ($_retorno);
}

// Gravação do arquivo de Log DB
function gravaDLog($_tabela, $_operacao, $_site, $_usuario, $_id, $_comando = "", $_observacao = ""){
    try {
        $_conexao = conexao();
        $_comando = "INSERT INTO planta_logs (tabela, operacao, site, usuario, registro, comando, observacao, cadastro) 
                            VALUES ('".$_tabela."', '". $_operacao."', '".$_site."', '".$_usuario."', '".$_id.
                                    "', \"".$_comando."\" , \"".$_observacao."\", UTC_TIMESTAMP())";
                               
        $_sql = $_conexao->prepare($_comando); 
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) {
            } else {
                throw new PDOException("gravaDLog - Não foi possível gravar o log desta operação!");
            }
        } else {
            throw new PDOException("gravaDLog - Não foi possível gravar o log desta operação!");
        } 
    } catch (PDOException $e) {
        montarMensagem("danger",array(traduzPDO($e->getMessage())),$_comando);
    }
    return;
}

// Gravação do arquivo de Log DB pelas APIs
function gravaDLogAPI($_tabela, $_operacao, $_site, $_usuario, $_id, $_comando = "", $_observacao = ""){
    try {
        $_conexao = conexao();
        $_comando = "INSERT INTO planta_logs (tabela, operacao, site, usuario, registro, comando, observacao, cadastro) 
                            VALUES ('".$_tabela."', '". $_operacao."', '".$_site."', '".$_usuario."', ".$_id.
                                    ", \"".$_comando."\" , \"".$_observacao."\", UTC_TIMESTAMP())";
               
        $_sql = $_conexao->prepare($_comando); 
        if ($_sql->execute()) {
            if ($_sql->rowCount() > 0) {
            } else {
                throw new PDOException("gravaDLogAPI - Não foi possível gravar o log desta operação!");
            }
        } else {
            throw new PDOException("gravaDLogAPI - Não foi possível gravar o log desta operação!");
        } 
    } catch (PDOException $e) {
        gravaXTrace('Erro na gravação do LogAPI '.traduzPDO($e->getMessage()).' '.$_comando);
    }
    return;
}
?>