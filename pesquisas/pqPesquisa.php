<?php
// Script para a PESQUISA de qualquer item
//
require_once("../suporte/suConexao.php");
require_once("../suporte/suFuncoes.php");

// Recebe o campo tabela e chave para a pesquisa
$_tabela = filter_input(INPUT_GET, "tabela", FILTER_DEFAULT);
$_chave = filter_input(INPUT_GET, "chave", FILTER_DEFAULT);
$_filtro = filter_input(INPUT_GET, "filtro", FILTER_DEFAULT);

// Monta o like para a pesquisa
$_pesquisa = "%" . $_chave . "%";

// Verifica se tem mais de um filtro separado por virgula
$_filtros = explode(",",$_filtro);
$_filtro = $_filtros[0];
$_filtroAuxiliar = (count($_filtros) > 1 ? $_filtros[1] : '');



// Monta a query para a pesquisa
$_query = "";

switch ($_tabela) {
    case 'ChgDestino':
    case 'PrtDestino':
    case 'StsDestino':
    case 'Destino':
        $_query = "SELECT id, descricao FROM 
                    (SELECT ae.id, CONCAT(ae.icao, ' - ', ae.localidade) as descricao 
                        FROM gear_aeroportos ae
                        WHERE ae.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa 
                    ORDER BY descricao LIMIT 10";        
    break;

    case 'ChgEquipamento':
    case 'PrtEquipamento':
    case 'StsEquipamento':
    case 'Equipamento':
        $_query = "SELECT id, descricao FROM 
                    (SELECT id, CONCAT(eq.equipamento,' - ',eq.modelo) as descricao 
                        FROM gear_equipamentos eq
                        WhERE eq.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa 
                    ORDER BY descricao LIMIT 10";        
	break;
   
    case 'EquipamentosAsa':
		$_query = "SELECT dm.codigo as id, dm.descricao
					FROM gear_dominios dm
					WHERE dm.tabela = 'planta_equipamentos' and dm.coluna = 'asa' AND dm.descricao LIKE :pesquisa 
					ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
	break;

    case 'EquipamentosTipoMotor':
		$_query = "SELECT dm.codigo as id, dm.descricao
					FROM gear_dominios dm
					WHERE dm.tabela = 'planta_equipamentos' and dm.coluna = 'tipoMotor' AND dm.descricao LIKE :pesquisa 
					ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
	break;

    case 'Fonte':
        $_query = "SELECT id, descricao
                    FROM 
                    (   SELECT 'ANAC' as id, 'ANAC' as descricao
                            UNION
                        SELECT DISTINCT ae.icao as id, ae.icao as descricao
                        FROM gear_aeroportos ae
                        INNER JOIN gear_clientes cl ON cl.idAeroporto = ae.id
                    ) T
                    WHERE descricao LIKE :pesquisa
                    ORDER BY descricao LIMIT 10";
    break;

    case 'ChgMatricula':
    case 'PrtMatricula':
    case 'StsMatricula':
    case 'Matricula':
        $_query = "SELECT id, descricao FROM 
                    (SELECT mt.id, CONCAT(mt.matricula, ' - ', eq.equipamento) as descricao 
                        FROM gear_matriculas mt
                        LEFT JOIN gear_equipamentos eq ON eq.id = mt.idEquipamento
                        WHERE mt.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa 
                    ORDER BY descricao LIMIT 10";
	break;

    case 'MatriculasCategoria':
		$_query = "SELECT dm.codigo as id, dm.descricao
					FROM gear_dominios dm
					WHERE dm.tabela = 'planta_matriculas' and dm.coluna = 'categoria' AND dm.descricao LIKE :pesquisa 
					ORDER BY dm.ordenacao, dm.descricao";
	break;

    case 'ChgMovimento':
    case 'PrtMovimento':
    case 'StsMovimento':         
    case 'Movimento':
        $_query = "SELECT mo.movimento as id, mo.descricao
                    FROM gear_movimentos mo 
                    WHERE mo.situacao = 'ATV' AND mo.idAeroporto = ".$_SESSION['plantaIDAeroporto'].
                    ($_filtro != '' ? " AND mo.operacao = '".$_filtro."'" : "").
                    ($_filtroAuxiliar != '' ? " AND (mo.antecessoras LIKE '%".$_filtroAuxiliar."%' OR mo.antecessoras = '')" : "").
                    " AND mo.descricao LIKE :pesquisa ". 
                    " ORDER BY mo.ordem, mo.descricao LIMIT 10";
    break;

    case 'Matriz':
        $_query = "SELECT id, descricao FROM 
                    (SELECT op.id, (CASE WHEN IFNULL(op.icao,'') = '' THEN op.operador ELSE CONCAT(op.icao,' - ',op.operador) END) as descricao
                        FROM gear_operadores op 
                        WHERE op.idMatriz IS NULL AND op.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa 
                    ORDER BY descricao LIMIT 10";        
	break;

    case 'Cobranca':
        $_query = "SELECT id, descricao FROM 
                    (SELECT opc.id, CONCAT(opc.cpfCnpj,' - ',opc.operador) as descricao
                        FROM gear_operadores_cobranca opc
                        WHERE opc.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa 
                    ORDER BY descricao LIMIT 10";        
	break;

    case 'Operador':
        $_query = "SELECT id, descricao FROM 
                    (SELECT op.id, (CASE WHEN IFNULL(op.icao,'') = '' THEN op.operador ELSE CONCAT(op.icao,' - ',op.operador) END) as descricao
                        FROM gear_operadores op
                        WHERE op.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa
                    ORDER BY descricao LIMIT 10";        
	break;

    case 'ChgEsteira':
    case 'ChgPosicao':
    case 'PrtPortao':
    case 'PrtPosicao':        
    case 'StsRecurso':
    case 'StsSegundoRecurso':
    case 'Recurso':        
		$_query = "SELECT re.id, re.recurso as descricao
					FROM gear_recursos re
                    WHERE re.situacao = 'ATV' AND re.idAeroporto = ".$_SESSION['plantaIDAeroporto'].
                    ($_filtro != '' ? " AND re.tipo = '".$_filtro."'" : "")." AND re.recurso LIKE :pesquisa ". 
                    " ORDER BY re.tipo,re.recurso LIMIT 10";
	break;

    case 'StsCmpRegra':
        $_query = "SELECT dm.codigo as id, dm.descricao as descricao
                    FROM gear_dominios dm
                    WHERE dm.tabela = 'planta_status_complementos' AND dm.coluna = 'regra' AND dm.descricao LIKE :pesquisa 
                    ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
    break;

    case 'StsCmpComando': 
    case 'Comandantes':        
		$_query = "SELECT co.id, CONCAT(co.codigoAnac,' - ',co.nome) as descricao
					FROM gear_comandantes co
                    WHERE co.situacao = 'ATV' AND CONCAT(co.codigoAnac,' - ',co.nome) LIKE :pesquisa ". 
                    " ORDER BY co.codigoAnac LIMIT 10";
	break;

    case 'ChgOrigem':
    case 'PrtOrigem':
    case 'StsOrigem':
    case 'Origem':
        $_query = "SELECT id, descricao FROM 
                    (SELECT ae.id, CONCAT(ae.icao, ' - ', ae.localidade) as descricao 
                        FROM gear_aeroportos ae
                        WHERE ae.situacao = 'ATV') T
                    WHERE descricao LIKE :pesquisa 
                    ORDER BY descricao LIMIT 10";        
    break;

    case 'ChgClasse':
    case 'PrtClasse':
    case 'StsClasse':
        $_query = "SELECT dm.codigo as id, CONCAT(dm.codigo,' - ',dm.descricao) as descricao
                    FROM gear_dominios dm
                    WHERE dm.tabela = 'planta_voos' AND dm.coluna = 'classe' AND CONCAT(dm.codigo,' - ',dm.descricao) LIKE :pesquisa 
                    ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
    break;

    case 'ChgNatureza':    
    case 'PrtNatureza':
    case 'StsNatureza':
        $_query = "SELECT dm.codigo as id, CONCAT(dm.codigo,' - ',dm.descricao) as descricao
                    FROM gear_dominios dm
                    WHERE dm.tabela = 'planta_voos' AND dm.coluna = 'natureza' AND CONCAT(dm.codigo,' - ',dm.descricao) LIKE :pesquisa 
                    ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
    break;

    case 'ChgServico':    
    case 'PrtServico':
    case 'StsServico':
        $_query = "SELECT dm.codigo as id, CONCAT(dm.codigo,' - ',dm.descricao) as descricao
                    FROM gear_dominios dm
                    WHERE dm.tabela = 'planta_voos' AND dm.coluna = 'servico' AND CONCAT(dm.codigo,' - ',dm.descricao) LIKE :pesquisa 
                    ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
    break;

    case 'TodosGrupo':
        $_query = "SELECT dm.codigo as id, dm.descricao
                    FROM gear_dominios dm
                    WHERE dm.tabela = 'planta_todos' AND dm.coluna = 'grupo' AND dm.descricao LIKE :pesquisa 
                    ORDER BY dm.ordenacao, dm.descricao";
    break;

    case 'TodosSituacao':
		$_query = "SELECT dm.codigo as id, dm.descricao
					FROM gear_dominios dm
					WHERE dm.tabela = 'planta_todos' AND dm.coluna = 'situacao' AND dm.descricao LIKE :pesquisa 
					ORDER BY dm.ordenacao, dm.descricao LIMIT 10";
	break;

	default:
		$_query = "";
}

// Buscar no banco de dados a pesquisa solicitada
$_retorno = ['status' => false, 'msg' => "Erro: Nenhum registro encontrado!"];
if ($_query != "") {
	try {
        //gravaXTrace($_query);
		$_conexao = conexao();    
        $_resultado = $_conexao->prepare($_query);
        $_resultado->bindParam(':pesquisa', $_pesquisa);
        $_resultado->execute();
        if (($_resultado) and ($_resultado->rowCount() != 0)) {
            while ($_row = $_resultado->fetch(PDO::FETCH_ASSOC)) {
                $dados[] = $_row;
            }
            $_retorno = ['status' => true, 'dados' => $dados];
        }
	} catch (PDOException $e) {
    }
}

// Retornar os dados
echo json_encode($_retorno);
?>