-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 10-Abr-2024 às 23:18
-- Versão do servidor: 8.0.31
-- versão do PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

/*SELECT SUBDATE(current_timestamp(), INTERVAL 3 hour)*/

--
-- Banco de dados: `gear`
--
CREATE DATABASE IF NOT EXISTS `gear` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gear`;

-- ****************************************************************************************
--
-- LIMPANDO O BANCO
--
-- Tabelas com chaves estrangeiras
--
DROP TABLE IF EXISTS `gear_propagandas`;
DROP TABLE IF EXISTS `gear_veiculos_credenciados`;
DROP TABLE IF EXISTS `gear_pessoas_credenciadas`;
DROP TABLE IF EXISTS `gear_clientes`;
DROP TABLE IF EXISTS `gear_calculos`;
DROP TABLE IF EXISTS `gear_faturamentos`;
DROP TABLE IF EXISTS `gear_status_complementos`;
DROP TABLE IF EXISTS `gear_status_movimentos`;
DROP TABLE IF EXISTS `gear_acessos`;
DROP TABLE IF EXISTS `gear_recursos`;
DROP TABLE IF EXISTS `gear_remessas`;
DROP TABLE IF EXISTS `gear_status`;
DROP TABLE IF EXISTS `gear_voos_planejados`;
DROP TABLE IF EXISTS `gear_restricoes`;
DROP TABLE IF EXISTS `gear_tarifas`;
DROP TABLE IF EXISTS `gear_vistoria_resultados`;
DROP TABLE IF EXISTS `gear_vistoria_agendamentos`;
DROP TABLE IF EXISTS `gear_vistoria_planos_itens`;
DROP TABLE IF EXISTS `gear_vistoria_itens`;
DROP TABLE IF EXISTS `gear_vistoria_planos`;


--
-- Tabelas Referenciadas
--
DROP TABLE IF EXISTS `gear_matriculas`;
DROP TABLE IF EXISTS `gear_empresas`;
DROP TABLE IF EXISTS `gear_usuarios`;
DROP TABLE IF EXISTS `gear_operadores`;
DROP TABLE IF EXISTS `gear_operadores_cobranca`;
DROP TABLE IF EXISTS `gear_equipamentos`;
DROP TABLE IF EXISTS `gear_aeroportos`;
DROP TABLE IF EXISTS `gear_comandantes`;

--
-- Tabelas Serviço
--
DROP TABLE IF EXISTS `gear_menus`;
DROP TABLE IF EXISTS `gear_logs`;
DROP TABLE IF EXISTS `gear_conexoes`;
DROP TABLE IF EXISTS `gear_dominios`;
DROP TABLE IF EXISTS `gear_matriculas_anac`;
DROP TABLE IF EXISTS `gear_voos_anac`;
DROP TABLE IF EXISTS `gear_dominios_anac`;
DROP TABLE IF EXISTS `gear_depara_anac`;
DROP TABLE IF EXISTS 
--
-- Views
--
DROP VIEW IF EXISTS `gear_primeiro_movimento`;
DROP VIEW IF EXISTS `gear_ultimo_movimento`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_usuarios`
--

DROP TABLE IF EXISTS `gear_usuarios`;
CREATE TABLE IF NOT EXISTS `gear_usuarios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario` varchar(25) NOT NULL,
  `celular` varchar(25) NOT NULL DEFAULT '',
  `senha` varchar(40) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usuarios_usuario` (`usuario`),
  UNIQUE KEY `idx_usuarios_senha` (`senha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_usuarios`
--
TRUNCATE TABLE `gear_usuarios`;
INSERT INTO `gear_usuarios` (`id`, `usuario`, `senha`, `nome`, `email`, `situacao`, `cadastro`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrador', 'educgoncalves@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(2, 'eduardo', '81f705dc2ce1a61a2621e0e4b442a9474e1d0c70', 'Eduardo Gonçalves', 'educgoncalves@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(3, 'cidikley', '2880d881b84be988c7c2b35c46d29eb4d5286f01', 'Cidikley Barbosa', 'cidikley@gmail.com', 'ATV', '2024-04-09 20:45:32'),
(4, 'jean', '51f8b1fa9b424745378826727452997ee2a7c3d7', 'Jean Delrio', 'jppdr2010@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(5, 'gerente', 'e0ffb90b074691c42ebd7b3cc39771b344c0083b', 'Gerente', 'gerente@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(6, 'supervisor', '0f4d09e43d208d5e9222322fbc7091ceea1a78c3', 'Supervisor', 'supervisor@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(7, 'encarregado', 'ba2bbe6d0f6e66f9eafcc2721eddbb9584758c03', 'Encarregado', 'encarregado@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(8, 'fiscal', '7b7e741b68aa05929c7c1f540e6e8799a3059706', 'Fiscal', 'fiscal@gmail.com', 'ATV', '2023-12-19 22:36:15'),
(9, 'convidado', 'c3c972c694cfed330ee6429cdcc7e8f7351375c3', 'Convidado', 'convidado@gmail.com', 'ATV', '2023-12-19 22:36:15');

--
-- Acionadores `gear_usuarios`
--
DROP TRIGGER IF EXISTS `buUsuarios`;
DELIMITER $$
CREATE TRIGGER `buUsuarios` BEFORE UPDATE ON `gear_usuarios` FOR EACH ROW BEGIN
  IF (new.usuario <> old.usuario) THEN
   	IF (SELECT COUNT(*) FROM gear_acessos ac WHERE ac.idUsuario = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'ACESSOS => Usuário não pode ser alterado';
		END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_aeroportos`
--

DROP TABLE IF EXISTS `gear_aeroportos`;
CREATE TABLE IF NOT EXISTS `gear_aeroportos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `iata` varchar(3) NOT NULL DEFAULT '',
  `icao` varchar(4) NOT NULL DEFAULT '',
  `nome` varchar(250) NOT NULL DEFAULT '',
  `localidade` varchar(50) NOT NULL DEFAULT '',
  `pais` varchar(25) NOT NULL DEFAULT '',
  `fonte` varchar(4) NOT NULL DEFAULT '',
  `origem` varchar(3) NOT NULL DEFAULT 'MNL',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_aeroportos_icao` (`icao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_aeroportos`
--
TRUNCATE TABLE `gear_aeroportos`;
INSERT INTO `gear_aeroportos` (`id`, `iata`, `icao`, `nome`, `localidade`, `pais`, `fonte`) VALUES
(0, 'GEA', 'GEAR', 'AEROPORTO PADRÃO', 'Sede', 'Brasil', 'GEAR');

-- 
-- Acionadores `gear_aeroportos`
--
DROP TRIGGER IF EXISTS `buAeroportos`;
DELIMITER $$
CREATE TRIGGER `buAeroportos` BEFORE UPDATE ON `gear_aeroportos` FOR EACH ROW BEGIN
  IF (new.icao <> old.icao) THEN
   	IF (SELECT COUNT(*) FROM gear_acessos ac WHERE ac.idAeroporto = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'ACESSOS => Sigla ICAO não pode ser alterada';
		END IF;
   	IF (SELECT COUNT(*) FROM gear_recursos po WHERE po.idAeroporto = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'RECURSOS => Sigla ICAO não pode ser alterada';
		END IF;
   	IF (SELECT COUNT(*) FROM gear_status st WHERE st.idAeroporto = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'STATUS => Sigla ICAO não pode ser alterada';
		END IF;
   	IF (SELECT COUNT(*) FROM gear_status st WHERE st.idOrigem = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'STATUS => Sigla ICAO Origem não pode ser alterada';
		END IF;
   	IF (SELECT COUNT(*) FROM gear_status st WHERE st.idDestino = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'STATUS => Sigla ICAO Destino não pode ser alterada';
		END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_acessos`
--

DROP TABLE IF EXISTS `gear_acessos`;
CREATE TABLE IF NOT EXISTS `gear_acessos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint UNSIGNED NOT NULL,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `sistema` varchar(4) NOT NULL,
  `grupo` varchar(3) NOT NULL DEFAULT 'CVD',
  `preferencial` varchar(3) NOT NULL DEFAULT 'SIM',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_acessos_usuario` (`idUsuario`,`idAeroporto`,`sistema`),
  KEY `idx_acessos_aeroportos` (`idAeroporto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_acessos`
--
TRUNCATE TABLE `gear_acessos`;
INSERT INTO `gear_acessos` (`idUsuario`, `idAeroporto`, `sistema`, `grupo`, `preferencial`) VALUES
(1, 0, 'GEAR', 'ADM', 'SIM'),
(2, 0, 'GEAR', 'ADM', 'SIM'),
(3, 0, 'GEAR', 'ADM', 'SIM'),
(1, 0, 'MAER', 'ADM', 'SIM'),
(2, 0, 'MAER', 'ADM', 'SIM'),
(3, 0, 'MAER', 'ADM', 'SIM'),
(1, 0, 'VAER', 'ADM', 'SIM'),
(2, 0, 'VAER', 'ADM', 'SIM'),
(3, 0, 'VAER', 'ADM', 'SIM');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_clientes`
--

DROP TABLE IF EXISTS `gear_clientes`;
CREATE TABLE IF NOT EXISTS `gear_clientes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `sistema` varchar(10) CHARACTER NOT NULL,
  `conexoes` int UNSIGNED NOT NULL DEFAULT '1',
  `debug` varchar(3) NOT NULL DEFAULT 'NAO',
  `regPorPagina` int UNSIGNED NOT NULL DEFAULT '10',
  `tmpIsencao` int UNSIGNED NOT NULL DEFAULT '180',
  `tmpReserva` int UNSIGNED NOT NULL DEFAULT '180',
  `tmpRetorno` int UNSIGNED NOT NULL DEFAULT '120',
  `tmpTaxiG1` int UNSIGNED NOT NULL DEFAULT '5',
  `tmpTaxiG2` int UNSIGNED NOT NULL DEFAULT '5',
  `tmpRefreshPagina` int UNSIGNED NOT NULL DEFAULT '90',
  `tmpRefreshTela` int UNSIGNED NOT NULL DEFAULT '60',
  `utc` int NOT NULL DEFAULT '-3',
  `hrAbertura` time NOT NULL DEFAULT '00:00:00',
  `hrFechamento` time NOT NULL DEFAULT '23:59:00',
  `categoria` varchar(3) NOT NULL DEFAULT '',
  `tipoOperador` varchar(15) NOT NULL DEFAULT '',
  `avsec` varchar(4) COLLATE NOT NULL DEFAULT '',
  `celular` varchar(60) CHARACTER NOT NULL DEFAULT '',
  `situacao` varchar(3) COLLATE NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_clientes`
--
INSERT INTO `gear_clientes` (`id`, `idAeroporto`, `sistema`, `conexoes`, `debug`, `regPorPagina`, `tmpIsencao`, `tmpReserva`, `tmpRetorno`, `utc`, `hrAbertura`, `hrFechamento`, `categoria`, `tipoOperador`, `avsec`, `celular`, `situacao`, `cadastro`) VALUES
(1, 7, 'GEAR', 1, 'SIM', 10, 180, 180, 120, -8, '05:30:00', '20:00:00', 'PRI', 'C.I Geral', 'AP-0', '', 'ATV', '2025-08-12 13:08:14'),
(2, 5, 'GEAR', 1, 'SIM', 20, 180, 180, 120, -3, '09:00:00', '01:00:00', 'PRI', 'C.I Geral', 'AP-0', '5521987558797', 'ATV', '2025-08-14 22:35:03'),
(5, 5, 'VAER', 10, 'SIM', 10, 180, 180, 120, -3, '00:00:00', '23:59:00', 'PRI', 'C.I Geral', 'AP-0', '', 'ATV', '2024-09-26 18:34:24'),
(10, 0, 'GEAR', 1, 'SIM', 20, 180, 180, 120, -3, '05:00:00', '23:59:00', 'PRI', 'C.I Geral', 'AP-0', '5521987558797', 'ATV', '2025-08-12 22:08:37'),
(14, 5, 'MAER', 1, 'SIM', 20, 180, 180, 120, -3, '20:00:00', '05:00:00', 'PRI', 'C.I Geral', 'AP-0', '5521987558797, 556195533036, 556192564939', 'ATV', '2025-08-14 22:43:15');
-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_conexoes`
--

DROP TABLE IF EXISTS `gear_conexoes`;
CREATE TABLE IF NOT EXISTS `gear_conexoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `sistema` varchar(10) NOT NULL,
  `usuario` varchar(25) NOT NULL,
  `grupo` varchar(3) NOT NULL,
  `identificacao` varchar(15) NOT NULL,
  `entrada` datetime NOT NULL,
  `saida` datetime DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_dominios`
--

DROP TABLE IF EXISTS `gear_dominios`;
CREATE TABLE IF NOT EXISTS `gear_dominios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabela` varchar(30) NOT NULL,
  `coluna` varchar(30) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `ordenacao` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_dominios` (`tabela`,`coluna`,`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Extraindo dados da tabela `gear_dominios`
--
TRUNCATE TABLE `gear_dominios`;
INSERT INTO `gear_dominios` (`tabela`, `coluna`, `codigo`, `descricao`, `ordenacao`) VALUES
('planta_acessos', 'grupo', 'ADM', 'Administrador', 0),
('planta_acessos', 'grupo', 'GER', 'Gerente', 0),
('planta_acessos', 'grupo', 'SUP', 'Supervisor', 0),
('planta_acessos', 'grupo', 'ENC', 'Encarregado', 0),
('planta_acessos', 'grupo', 'FIS', 'Fiscal', 0),
('planta_acessos', 'grupo', 'CVD', 'Convidado', 0),
('planta_acessos', 'nivel', 'ADM', '30', 0),
('planta_acessos', 'nivel', 'GER', '25', 0),
('planta_acessos', 'nivel', 'SUP', '20', 0),
('planta_acessos', 'nivel', 'ENC', '15', 0),
('planta_acessos', 'nivel', 'FIS', '10', 0),
('planta_acessos', 'nivel', 'CVD', '0', 0),
('planta_acessos', 'preferencial', 'SIM', 'Sim', 0),
('planta_acessos', 'preferencial', 'NAO', 'Não', 1),

('planta_calculos', 'situacao', 'CNF', 'Confirmado', 0),
('planta_calculos', 'situacao', 'NCN', 'A confirmar', 1),
('planta_calculos', 'situacao', 'PEN', 'Pendente', 1),

('planta_clientes', 'reserva', 'SIM', 'Sim', 0),
('planta_clientes', 'reserva', 'NAO', 'Não', 1),
('planta_clientes', 'categoria', 'PRI', 'Primeira', 0),
('planta_clientes', 'categoria', 'SEG', 'Segunda', 1),
('planta_clientes', 'categoria', 'TER', 'Terceira', 2),
('planta_clientes', 'categoria', 'QUA', 'Quarta', 3),
('planta_clientes', 'tipoOperador', 'C.I Geral', 'Aeródromo de Uso Público Classe I - Geral', 0),
('planta_clientes', 'tipoOperador', 'C.I RBAC 135', 'Aeródromo de Uso Público Classe I - RBAC 135 Regular', 2),
('planta_clientes', 'tipoOperador', 'C.I RBAC 121', 'Aeródromo de Uso Público Classe I - RBAC 121', 1),
('planta_clientes', 'tipoOperador', 'C.II', 'Aeródromo de Uso Público Classe II', 3),
('planta_clientes', 'tipoOperador', 'C.III', 'Aeródromo de Uso Público Classe III', 4),
('planta_clientes', 'tipoOperador', 'C.IV', 'Aeródromo de Uso Público Classe IV', 5),
('planta_clientes', 'avsec', 'AP-0', 'Aeródromo exclusivo aviação geral', 0),
('planta_clientes', 'avsec', 'AP-1', 'Aeródromo aviação comercial regular < 600.000 PAX', 1),
('planta_clientes', 'avsec', 'AP-2', 'Aeródromo aviação comercial regular >= 600.000 e < 5.000.000 PAX', 2),
('planta_clientes', 'avsec', 'AP-3', 'Aeródromo aviação comercial regular >= 5.000.000 PAX', 3),

('planta_equipamentos', 'asa', 'FIX', 'Asa Fixa', 0),
('planta_equipamentos', 'asa', 'MOV', 'Asa Móvel', 0),
('planta_equipamentos', 'origem', 'ATU', 'Atualizando', 0),
('planta_equipamentos', 'origem', 'MNL', 'Manual', 0),
('planta_equipamentos', 'origem', 'IMP', 'Importado', 0),
('planta_equipamentos', 'tipoMotor', 'ELE', 'Eletric', 0),
('planta_equipamentos', 'tipoMotor', 'JET', 'Jet', 0),
('planta_equipamentos', 'tipoMotor', 'PST', 'Piston', 0),
('planta_equipamentos', 'tipoMotor', 'RCK', 'Rocket', 0),
('planta_equipamentos', 'tipoMotor', 'TRB', 'Turboprop/Turboshaft', 0),

('planta_faturamentos', 'situacao', 'CNF', 'Faturado', 0),
('planta_faturamentos', 'situacao', 'NCN', 'A confirmar', 1),
('planta_faturamentos', 'situacao', 'PRC', 'Processamento', 2),

('planta_matriculas', 'categoria', 'NDN', 'Não Definido', 0),
('planta_matriculas', 'categoria', 'RPC', 'Regular ou Não Regular de Passageiros ou Carga', 0),
('planta_matriculas', 'categoria', 'TAX', 'Taxi Aéreo', 0),
('planta_matriculas', 'categoria', 'AVG', 'Aviação Geral', 0),
('planta_matriculas', 'categoria', 'INS', 'Instrução', 0),
('planta_matriculas', 'categoria', 'MIL', 'Militar', 0),

('planta_monitores_paginas', 'resolucao', '1280x960', '1280x960', 0),
('planta_monitores_paginas', 'resolucao', '1440x900', '1440x900', 1),
('planta_monitores_paginas', 'resolucao', '1920x1080', '1920x1080', 2),
('planta_monitores_paginas', 'acao', 'DST', 'Desativar', 0),
('planta_monitores_paginas', 'acao', 'EXB', 'Exibir', 1),
('planta_monitores_paginas', 'acao', 'INT', 'Interromper', 2),

('planta_movimentos', 'operacao', 'TDS', 'Todas', 4),
('planta_movimentos', 'operacao', 'CHG', 'Voos de Chegada', 2),
('planta_movimentos', 'operacao', 'PRT', 'Voos de Partida', 3),
('planta_movimentos', 'operacao', 'STA', 'Status', 1),

('planta_menus', 'atalho', 'NAO', 'Não', 0),
('planta_menus', 'atalho', 'ACR', 'Acesso Rápido', 1),
('planta_menus', 'atalho', 'GRF', 'Gráficos', 2),
('planta_menus', 'atalho', 'INF', 'Informações', 3),

('planta_menus', 'tipo', 'Header', 'Header', 0),
('planta_menus', 'tipo', 'Opcao', 'Opção simples', 0),
('planta_menus', 'tipo', 'Menu', 'Menu de opções', 0),
('planta_menus', 'tipo', 'MenuOpcao', 'Opção de menu', 0),
('planta_menus', 'tipo', 'SubMenu', 'Submenu de opções', 0),
('planta_menus', 'tipo', 'SubMenuOpcao', 'Opção de submenu', 0),

('planta_propagandas', 'situacao', 'AGD', 'Aguardando', 0),
('planta_propagandas', 'situacao', 'EXB', 'Exibindo', 1),
('planta_propagandas', 'situacao', 'INA', 'Inativo', 2),
('planta_propagandas', 'situacao', 'INT', 'Intervalo', 3),

('planta_notificacoes', 'situacao', 'LDS', 'Lidas', 0),
('planta_notificacoes', 'situacao', 'NLD', 'Não lidas', 1),

('planta_recursos', 'classe', 'NAN', 'Não Informar', 0),
('planta_recursos', 'classe', 'DOM', 'Doméstica', 1),
('planta_recursos', 'classe', 'INT', 'Internacional', 2),

('planta_recursos', 'natureza', 'NAN', 'Não Informar', 0),
('planta_recursos', 'natureza', 'CRG', 'Carga', 1),
('planta_recursos', 'natureza', 'PAX', 'Passageiro/Misto', 2),

('planta_recursos', 'situacao', 'ATV', 'Ativa', 0),
('planta_recursos', 'situacao', 'IMP', 'Impedida', 1),
('planta_recursos', 'situacao', 'INA', 'Inativa', 2),
('planta_recursos', 'situacao', 'MNT', 'Manutenção', 3),

('planta_recursos', 'tipo', 'AMB', 'Ambulância', 0),
('planta_recursos', 'tipo', 'LFT', 'Ambulift', 0),
('planta_recursos', 'tipo', 'CHK', 'Checkin', 0),
('planta_recursos', 'tipo', 'EST', 'Esteira', 0),
('planta_recursos', 'tipo', 'PIS', 'Pista', 0),
('planta_recursos', 'tipo', 'CAB', 'Cabeceira', 0),
('planta_recursos', 'tipo', 'PON', 'Ponte', 0),
('planta_recursos', 'tipo', 'POR', 'Portão', 0),
('planta_recursos', 'tipo', 'POS', 'Posição', 0),
('planta_recursos', 'tipo', 'ONI', 'Ônibus', 0),
('planta_recursos', 'tipo', 'ESC', 'Escada', 0),
('planta_recursos', 'tipo', 'TER', 'Terminal', 0),
('planta_recursos', 'tipo', 'SET', 'Setor', 0),
('planta_recursos', 'tipo', 'SAL', 'Sala', 0),
('planta_recursos', 'tipo', 'ARA', 'Área', 0),

('planta_recursos', 'unidade', 'NAN', 'Não Informar', 0),
('planta_recursos', 'unidade', 'PAX', 'Passageiros', 1),
('planta_recursos', 'unidade', 'UND', 'Unidades', 2),

('planta_recursos', 'utilizacao', 'NAN', 'Não Informar', 0),
('planta_recursos', 'utilizacao', 'EST', 'Estadia', 1),
('planta_recursos', 'utilizacao', 'HNG', 'Hangar', 2),
('planta_recursos', 'utilizacao', 'ISE', 'Isenta', 3),
('planta_recursos', 'utilizacao', 'MNB', 'Manobra', 4),

('planta_recursos', 'sentido', 'NAN', 'Não Informar', 0),
('planta_recursos', 'sentido', 'EMB', 'Partida/Embarque', 1),
('planta_recursos', 'sentido', 'DES', 'Chegada/Desembarque', 2),
('planta_recursos', 'sentido', 'CHG', 'Chegadas', 3),
('planta_recursos', 'sentido', 'PRT', 'Partidas', 4),

('planta_reservas', 'situacao', 'APR', 'Aprovada', 0),
('planta_reservas', 'situacao', 'AVN', 'A Vencer', 1),
('planta_reservas', 'situacao', 'CAN', 'Cancelada', 2),
('planta_reservas', 'situacao', 'NEG', 'Negada', 3),
('planta_reservas', 'situacao', 'PEN', 'Pendente', 4),
('planta_reservas', 'situacao', 'VEN', 'Vencida', 5),

('planta_status', 'faturado', 'SIM', 'Sim', 0),
('planta_status', 'faturado', 'NAO', 'Não', 1),
('planta_status', 'situacao', 'ATV', 'Ativo', 0),
('planta_status', 'situacao', 'INA', 'Inativo', 1),
('planta_status', 'situacao', 'FCH', 'Fechado', 2),

('planta_status_complementos', 'regra', 'VIS', 'Visual', 0),
('planta_status_complementos', 'regra', 'INS', 'Instrumentos', 1),

('planta_status_movimentos', 'situacao', 'ATV', 'Ativo', 0),
('planta_status_movimentos', 'situacao', 'CAN', 'Cancelado', 1),
('planta_status_movimentos', 'situacao', 'EXC', 'Excluído', 2),
('planta_status_movimentos', 'situacao', 'PEN', 'Pendente', 3),

('planta_tarefas', 'modo', 'MNL', 'Manual', 0),
('planta_tarefas', 'modo', 'AUT', 'Automático', 0),

('planta_todos', 'grupo', '1', 'Grupo 1', 0),
('planta_todos', 'grupo', '2', 'Grupo 2', 0),

('planta_todos', 'sistema', 'GEAR', 'Gerenciamento de Aeroportos', 0),
('planta_todos', 'sistema', 'MAER', 'Movimentação de Aeronaves', 0),
('planta_todos', 'sistema', 'VAER', 'Vistoria Aeroportuária', 0),

('planta_todos', 'situacao', 'ATV', 'Ativo', 0),
('planta_todos', 'situacao', 'INA', 'Inativo', 0),

('planta_todos', 'simnao', 'SIM', 'Sim', 0),
('planta_todos', 'simnao', 'NAO', 'Não', 1),

('planta_todos', 'origem', 'ATU', 'Pendente', 0),
('planta_todos', 'origem', 'MNL', 'Manual', 0),
('planta_todos', 'origem', 'IMP', 'Regular', 0),
('planta_todos', 'origem', 'PRI', 'Privado', 0),
('planta_todos', 'origem', 'PUB', 'Público', 0),

('planta_todos', 'hexadecimal', '#0d6efd', 'Azul', 0),
('planta_todos', 'hexadecimal', '#6c757d', 'Cinza', 0),
('planta_todos', 'hexadecimal', '#198754', 'Verde', 0),
('planta_todos', 'hexadecimal', '#dc3545', 'Vermelho', 0),
('planta_todos', 'hexadecimal', '#ffc107', 'Amarelo', 0),
('planta_todos', 'hexadecimal', '#212529', 'Escuro', 0),
('planta_todos', 'hexadecimal', '#0dcaf0', 'Ciano', 0),
('planta_todos', 'hexadecimal', '#f8f9fa', 'Suave', 0),

('planta_todos', 'destaque', 'primary', 'Azul', 0),
('planta_todos', 'destaque', 'secondary', 'Cinza', 0),
('planta_todos', 'destaque', 'success', 'Verde', 0),
('planta_todos', 'destaque', 'danger', 'Vermelho', 0),
('planta_todos', 'destaque', 'warning', 'Amarelo', 0),
('planta_todos', 'destaque', 'dark', 'Escuro', 0),
('planta_todos', 'destaque', 'info', 'Ciano', 0),
('planta_todos', 'destaque', 'light', 'Suave', 0),

('planta_todos', 'mes', '1', 'Janeiro', 1),
('planta_todos', 'mes', '2', 'Fevereiro', 2),
('planta_todos', 'mes', '3', 'Março', 3),
('planta_todos', 'mes', '4', 'Abril', 4),
('planta_todos', 'mes', '5', 'Maio', 5),
('planta_todos', 'mes', '6', 'Junho', 6),
('planta_todos', 'mes', '7', 'Julho', 7),
('planta_todos', 'mes', '8', 'Agosto', 8),
('planta_todos', 'mes', '9', 'Setembro', 9),
('planta_todos', 'mes', '10', 'Outubro', 10),
('planta_todos', 'mes', '11', 'Novembro', 11),
('planta_todos', 'mes', '12', 'Dezembro', 12),

('planta_todos', 'dia', '1', 'Domingo', 1),
('planta_todos', 'dia', '2', 'Segunda', 2),
('planta_todos', 'dia', '3', 'Terça', 3),
('planta_todos', 'dia', '4', 'Quarta', 4),
('planta_todos', 'dia', '5', 'Quinta', 5),
('planta_todos', 'dia', '6', 'Sexta', 6),
('planta_todos', 'dia', '7', 'Sábado', 7),

('planta_voos', 'servico', 'J', 'Regular de passageiros', 3),
('planta_voos', 'servico', 'F', 'Regular de carga e mala postal', 8),
('planta_voos', 'servico', 'M', 'Regular somente de mala postal', 9),
('planta_voos', 'servico', 'G', 'Extra de passageiros', 6),
('planta_voos', 'servico', 'C', 'Charter de passageiros', 4),
('planta_voos', 'servico', 'H', 'Charter cargueiro e de mala postal', 10),
('planta_voos', 'servico', 'P', 'Não comercial (posicionamento e ferry)', 5),
('planta_voos', 'servico', 'T', 'Teste da aeronave', 11),
('planta_voos', 'servico', 'K', 'Treinamento (check da tripulação)', 12),
('planta_voos', 'servico', 'X', 'Pouso Técnico (abastecimento, etc.)', 7),
('planta_voos', 'servico', 'D', 'Aviação Geral', 0),
('planta_voos', 'servico', 'N', 'Taxi Aéreo', 1),
('planta_voos', 'servico', 'W', 'Militar', 2),
('planta_voos', 'servico', '???', 'Não Definido', 20),

('planta_voos', 'classe', 'DOM', 'Doméstica', 0),
('planta_voos', 'classe', 'INT', 'Internacional', 0),
('planta_voos', 'classe', '???', 'Não Definido', 9),

('planta_voos', 'natureza', 'PAX', 'Passageiro/Misto', 0),
('planta_voos', 'natureza', 'CRG', 'Carga', 0),
('planta_voos', 'natureza', 'NDN', 'Não Aplicado', 0),
('planta_voos', 'natureza', '???', 'Não Definido', 9),

('planta_voos', 'situacao', 'ATV', 'Ativo', 0),
('planta_voos', 'situacao', 'CAN', 'Cancelado', 1),
('planta_voos', 'situacao', 'EXC', 'Excluído', 2),
('planta_voos', 'situacao', 'PEN', 'Pendente', 3),

('planta_vistoria_planos', 'frequencia', 'D', 'Diária', 0),
('planta_vistoria_planos', 'frequencia', 'S', 'Semanal', 1),
('planta_vistoria_planos', 'frequencia', 'Q', 'Quinzenal', 2),
('planta_vistoria_planos', 'frequencia', 'M', 'Mensal', 3),

('planta_vistoria_planos', 'quantidade', '1', '1x', 0),
('planta_vistoria_planos', 'quantidade', '2', '2x', 1),
('planta_vistoria_planos', 'quantidade', '3', '3x', 2),
('planta_vistoria_planos', 'quantidade', '4', '4x', 3),
('planta_vistoria_planos', 'quantidade', '5', '5x', 4),
('planta_vistoria_planos', 'quantidade', '6', '6x', 5),
('planta_vistoria_planos', 'quantidade', '7', '7x', 6),
('planta_vistoria_planos', 'quantidade', '8', '8x', 7),

('planta_vistoria_planos', 'periodo', 'L', 'Livre', 0),
('planta_vistoria_planos', 'periodo', 'M', 'Manhã', 1),
('planta_vistoria_planos', 'periodo', 'T', 'Tarde', 2),
('planta_vistoria_planos', 'periodo', 'N', 'Noite', 3),

('planta_vistoria_planos', 'situacao', 'APG', 'A programar', 0),
('planta_vistoria_planos', 'situacao', 'ATV', 'Ativo', 0),
('planta_vistoria_planos', 'situacao', 'INA', 'Inativo', 0),

('planta_vistoria_itens', 'tipo', 'PPD', '(ÁREA DE MOVIMENTO) - PISTAS DE POUSO E DECOLAGEM', 0),
('planta_vistoria_itens', 'tipo', 'PST', '(ÁREA DE MOVIMENTO) - PISTA(S) DE TÁXI', 1),
('planta_vistoria_itens', 'tipo', 'PEA', '(ÁREA DE MOVIMENTO) - PÁTIO DE ESTACIONAMENTO DE AERONAVES', 2),
('planta_vistoria_itens', 'tipo', 'MNT', '(MANUTENÇÃO)', 3),
('planta_vistoria_itens', 'tipo', 'SEG', '(SEGURANÇA)', 4);

-- Inserindo dominios tabelas do sistema
--
INSERT INTO `gear_dominios` (`tabela`, `coluna`, `codigo`, `descricao`, `ordenacao`)
 SELECT 'planta_logs', 'tabela', TABLE_NAME, 
        (CASE WHEN TABLE_COMMENT <> "" THEN TABLE_COMMENT ELSE TABLE_NAME END), 0 
  FROM information_schema.tables WHERE UCASE(table_schema) = 'GEAR' AND TABLE_TYPE = 'BASE TABLE';

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_dominios_anac`
--

DROP TABLE IF EXISTS `gear_dominios_anac`;
CREATE TABLE IF NOT EXISTS `gear_dominios_anac` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabela` varchar(30) NOT NULL,
  `coluna` varchar(30) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `ordenacao` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_dominios_anac` (`tabela`,`coluna`,`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `gear_depara_anac`
--

DROP TABLE IF EXISTS `gear_depara_anac`;
CREATE TABLE IF NOT EXISTS `gear_depara_anac` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` varchar(15) NOT NULL,
  `anac` varchar(50) NOT NULL,
  `gear` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_depara_anac`
--
TRUNCATE TABLE `gear_depara_anac`;
INSERT INTO `gear_depara_anac` (`id`, `tipo`, `anac`, `gear`) VALUES
(1, 'classe', 'INTERNACIONAL', 'INT'),
(2, 'classe', 'DOMÉSTICA', 'DOM'),
(3, 'natureza', 'PASSAGEIROS', 'PAX'),
(4, 'natureza', 'CARGA', 'CRG'),
(5, 'natureza', 'NÃO APLICADO', 'NDN'),
(6, 'servico', 'REGULAR DE PASSAGEIROS', 'J'),
(7, 'servico', 'NÃO REGULAR DE PASSAGEIROS', 'C'),
(8, 'servico', 'NÃO REGULAR DE CARGA', 'H'),
(10, 'servico', 'REGULAR DE CARGA', 'F'),
(11, 'servico', 'REGULAR DE CARGA DE CORREIO', 'M'),
(12, 'servico', 'SOBREVOOS OU TRASLADOS OPERACIONAIS', '???');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_empresas`
--

DROP TABLE IF EXISTS `gear_empresas`;
CREATE TABLE IF NOT EXISTS `gear_empresas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `empresa` varchar(25) NOT NULL,
  `atividade` varchar(25) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `bairro` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_empresas_empresa` (`idAeroporto`,`empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_equipamentos`
--

DROP TABLE IF EXISTS `gear_equipamentos`;
CREATE TABLE IF NOT EXISTS `gear_equipamentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `equipamento` varchar(25) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `fabricante` varchar(100) NOT NULL DEFAULT '',
  `iataEquipamento` varchar(10) NOT NULL DEFAULT '',
  `icaoCategoria` varchar(10) NOT NULL DEFAULT '',
  `tipoMotor` varchar(3) NOT NULL DEFAULT 'JET',
  `qtdMotor` int UNSIGNED DEFAULT NULL,
  `qtc` int UNSIGNED DEFAULT NULL,
  `envergadura` decimal(5,2) NOT NULL DEFAULT '0.00',
  `comprimento` decimal(5,2) NOT NULL DEFAULT '0.00',
  `assentos` int UNSIGNED DEFAULT NULL,
  `asa` varchar(3) NOT NULL DEFAULT 'FIX',
  `fonte` varchar(4) NOT NULL DEFAULT 'ANAC',
  `origem` varchar(3) NOT NULL DEFAULT 'IMP', 
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_equipamentos_modelo` (`equipamento`,`modelo`,`fabricante`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

--
-- Acionadores `gear_equipamentos`
--
DROP TRIGGER IF EXISTS `buModelos`;
DELIMITER $$
CREATE TRIGGER `buModelos` BEFORE UPDATE ON `gear_equipamentos` FOR EACH ROW BEGIN
  IF (new.modelo <> old.modelo) THEN
   	IF (SELECT COUNT(*) FROM gear_matriculas mt WHERE mt.idEquipamento = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'MATRICULAS => Modelo não pode ser alterado';
		END IF;
  END IF;
  IF (new.equipamento <> old.equipamento) THEN
   	IF (SELECT COUNT(*) FROM gear_matriculas mt WHERE mt.idEquipamento = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'MATRICULAS => Equipamento não pode ser alterado';
		END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_faturamentos`
--

DROP TABLE IF EXISTS `gear_faturamentos`;
CREATE TABLE IF NOT EXISTS `gear_faturamentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `ano` varchar(4) NOT NULL DEFAULT '0000',
  `numero` varchar(6) NOT NULL DEFAULT '000000',
  `idOperador` bigint UNSIGNED NOT NULL,
  `idRemessa` bigint UNSIGNED NULL,
  `fatura` datetime DEFAULT NULL,
  `pagamento` datetime DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'NCN',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_faturamentos_aeroportos` (`idAeroporto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_faturamentos`
--
DROP TRIGGER IF EXISTS `biFaturamentos`;
DELIMITER $$
CREATE TRIGGER `biFaturamentos` BEFORE INSERT ON `gear_faturamentos` FOR EACH ROW BEGIN
  -- Gera o número do faturamento
	SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,6,'0')
                			FROM gear_faturamentos 
					            WHERE idAeroporto = new.idAeroporto 
                        AND ano = DATE_FORMAT(new.cadastro, '%Y')
                	    GROUP BY ano), '000001'));
  SET new.ano = DATE_FORMAT(new.cadastro, '%Y');
END
$$
DELIMITER ;

--
-- Estrutura da tabela `gear_remessas`
--

DROP TABLE IF EXISTS `gear_remessas`;
CREATE TABLE IF NOT EXISTS `gear_remessas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `ano` varchar(4) NOT NULL DEFAULT '0000',
  `numero` varchar(6) NOT NULL DEFAULT '000000',
  `qtdFaturas` int UNSIGNED NOT NULL DEFAULT 0,  
  `qtdLinhas` int UNSIGNED NOT NULL DEFAULT 0, 
  `vlrTotal` decimal(8,2) NOT NULL DEFAULT 0,
  `idUsuario` bigint UNSIGNED NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_remessas_aeroportos` (`idAeroporto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_remessas`
--
DROP TRIGGER IF EXISTS `biRemessas`;
DELIMITER $$
CREATE TRIGGER `biRemessas` BEFORE INSERT ON `gear_faturamentos` FOR EACH ROW BEGIN
  -- Gera o número da remessa
	SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,6,'0')
                			FROM gear_remessas 
					            WHERE idAeroporto = new.idAeroporto 
                        AND ano = DATE_FORMAT(new.cadastro, '%Y')
                	    GROUP BY ano), '000001'));
  SET new.ano = DATE_FORMAT(new.cadastro, '%Y');
END
$$
DELIMITER ;
-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_logs`
--

DROP TABLE IF EXISTS `gear_logs`;
CREATE TABLE IF NOT EXISTS `gear_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabela` varchar(25) NOT NULL,
  `operacao` varchar(15) NOT NULL,
  `aeroporto` varchar(4) NOT NULL,
  `usuario` varchar(25) NOT NULL,
  `registro` int DEFAULT NULL,
  `comando` varchar(500) DEFAULT NULL,
  `observacao` varchar(500) DEFAULT NULL,
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logs_cadastro` (`cadastro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_matriculas`
--

DROP TABLE IF EXISTS `gear_matriculas`;
CREATE TABLE IF NOT EXISTS `gear_matriculas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `matricula` varchar(10) NOT NULL,
  `idEquipamento` bigint UNSIGNED NOT NULL,
  `idOperador` bigint UNSIGNED NOT NULL,
  `assentos` int UNSIGNED DEFAULT NULL,
  `pmd` int UNSIGNED DEFAULT NULL,
  `categoria` varchar(3) DEFAULT NULL,
  `fonte` varchar(4) NOT NULL DEFAULT 'ANAC',
  `origem` varchar(3) NOT NULL DEFAULT 'IMP', 
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_matriculas_matricula` (`matricula`),
  KEY `idx_matriculas_operador` (`idOperador`,`matricula`),
  KEY `idx_matriculas_equipamentos` (`idEquipamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_matriculas`
--

DROP TRIGGER IF EXISTS `buMatriculas`;
DELIMITER $$
CREATE TRIGGER `buMatriculas` BEFORE UPDATE ON `gear_matriculas` FOR EACH ROW BEGIN
  IF (new.matricula <> old.matricula) THEN
   	IF (SELECT COUNT(*) FROM gear_status st WHERE st.idMatricula = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'STATUS => Matrícula não pode ser alterada';
		END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_menus`
--
DROP TABLE IF EXISTS `gear_menus`;
CREATE TABLE IF NOT EXISTS `gear_menus` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sistema` varchar(4) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `formulario` varchar(4) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `href` varchar(150) NOT NULL DEFAULT '',
  `target` varchar(50) NOT NULL DEFAULT '',
  `iconeSVG` varchar(50) NOT NULL DEFAULT '',
  `ordem` int NOT NULL DEFAULT '0',
  `atalho` varchar(3) NOT NULL DEFAULT 'NAO',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Extraindo dados da tabela `gear_menus`
--
TRUNCATE TABLE `gear_menus`;
INSERT INTO `gear_menus` (`id`, `sistema`, `tipo`, `formulario`, `modulo`, `descricao`, `href`, `target`, `iconeSVG`, `ordem`, `atalho`, `cadastro`) VALUES
(1, 'GEAR', 'Opcao', '1000', 'Logout', 'Sair', '../suporte/suLogout.php', '', 'exit', 10, 'NAO', '2025-05-21 09:32:26'),
(2, 'GEAR', 'Menu', '2000', 'Cadastros', 'Cadastros', '', '', 'cadastros', 25, 'NAO', '2025-05-21 12:48:00'),
(3, 'GEAR', 'MenuOpcao', '2010', 'Cadastros', 'Aeroportos', '../cadastros/cdCadastrarAeroportos.php', '', '', 25, 'NAO', '2025-04-04 19:35:18'),
(4, 'GEAR', 'MenuOpcao', '2020', 'Cadastros', 'Equipamentos', '../cadastros/cdCadastrarEquipamentos.php', '', '', 25, 'NAO', '2025-05-25 23:05:33'),
(5, 'GEAR', 'SubMenuOpcao', '2031', 'Cadastros', 'Informações RAB', '../cadastros/cdCadastrarOperadoresRAB.php', '', '', 25, 'NAO', '2025-05-21 10:48:25'),
(6, 'GEAR', 'MenuOpcao', '2025', 'Cadastros', 'Matrículas', '../cadastros/cdCadastrarMatriculas.php', '', '', 25, 'NAO', '2025-05-25 23:06:59'),
(7, 'GEAR', 'MenuOpcao', '2015', 'Cadastros', 'Comandantes', '../cadastros/cdCadastrarComandantes.php', '', '', 25, 'NAO', '2025-05-25 23:06:23'),
(8, 'GEAR', 'MenuOpcao', '9225', 'Administração', 'Propagandas', '../administracao/adCadastrarPropagandas.php', '', '', 20, 'NAO', '2025-05-25 22:33:26'),
(9, 'GEAR', 'Menu', '2200', 'Consultas', 'Consultas', '', '', 'consultas', 30, 'NAO', '2025-05-21 12:48:10'),
(10, 'GEAR', 'MenuOpcao', '2210', 'Consultas', 'Aeroportos', '../consultas/csConsultarAeroportos.php', '', '', 30, 'NAO', '2025-04-04 19:35:18'),
(11, 'GEAR', 'MenuOpcao', '2220', 'Consultas', 'Equipamentos', '../consultas/csConsultarEquipamentos.php', '', '', 30, 'NAO', '2025-04-04 19:35:18'),
(12, 'GEAR', 'SubMenuOpcao', '2231', 'Consultas', 'Informações RAB', '../consultas/csConsultarOperadoresRAB.php', '', '', 30, 'NAO', '2025-05-13 12:22:44'),
(13, 'GEAR', 'MenuOpcao', '2225', 'Consultas', 'Matrículas', '../consultas/csConsultarMatriculas.php', '', '', 30, 'NAO', '2025-05-25 23:09:10'),
(14, 'GEAR', 'MenuOpcao', '2215', 'Consultas', 'Comandantes', '../consultas/csConsultarComandantes.php', '', '', 30, 'NAO', '2025-05-25 23:07:57'),
(15, 'GEAR', 'MenuOpcao', '2240', 'Consultas', 'Recursos', '../consultas/csConsultarRecursos.php', '', '', 30, 'NAO', '2025-05-25 23:09:44'),
(16, 'GEAR', 'MenuOpcao', '2245', 'Consultas', 'Tarifas', '../consultas/csConsultarTarifas.php', '', '', 30, 'NAO', '2025-05-25 23:09:57'),
(17, 'GEAR', 'Menu', '3000', 'Informativos', 'Informativos', '', '', 'informativos', 35, 'NAO', '2025-08-15 01:55:13'),
(18, 'GEAR', 'SubMenu', '3010', 'Informativos', 'Movimentos Grupo I', '', '', '', 35, 'NAO', '2025-05-06 23:06:16'),
(19, 'GEAR', 'SubMenuOpcao', '3011', 'Informativos', 'Página 1', '	../siv/inVisualizadorInformativos.html?pagina=../siv/GEAR_MovimentosGrupoI_P1.html', '_blank', '', 35, 'NAO', '2025-09-18 18:35:08'),
(20, 'GEAR', 'SubMenuOpcao', '3012', 'Informativos', 'Página 2', '../siv/inVisualizadorInformativos.html?pagina=../siv/GEAR_MovimentosGrupoI_P2.html', '_blank', '', 35, 'NAO', '2025-09-18 15:40:29'),
(21, 'GEAR', 'SubMenu', '3020', 'Informativos', 'Movimentos Grupo II', '', '', '', 35, 'NAO', '2025-05-06 23:06:27'),
(22, 'GEAR', 'SubMenuOpcao', '3021', 'Informativos', 'Página 1', '../siv/inVisualizadorInformativos.html?pagina=../siv/GEAR_MovimentosGrupoII_P1.html', '_blank', '', 35, 'NAO', '2025-09-18 18:35:18'),
(23, 'GEAR', 'SubMenuOpcao', '3022', 'Informativos', 'Página 2', '../siv/inVisualizadorInformativos.html?pagina=../siv/GEAR_MovimentosGrupoII_P2.html', '_blank', '', 35, 'NAO', '2025-09-18 15:41:09'),
(24, 'GEAR', 'SubMenu', '2400', 'Gráficos', 'Gráficos', '', '', '', 35, 'NAO', '2025-09-15 20:46:34'),
(25, 'GEAR', 'MenuOpcao', '3300', 'Informativos', 'Chegadas', '../siv/Siiv_C_CHG_MC_P_01.html', '_blank', '', 35, 'NAO', '2025-04-04 19:35:18'),
(26, 'GEAR', 'MenuOpcao', '3400', 'Informativos', 'Partidas', '../siv/Siiv_C_PAR_MP_P_01.html', '_blank', '', 35, 'NAO', '2025-04-04 19:35:18'),
(27, 'GEAR', 'Menu', '5000', 'Operacional', 'Operacional', '', '', 'operacional', 40, 'NAO', '2025-05-21 12:19:24'),
(28, 'GEAR', 'SubMenu', '5010', 'Operacional', 'Planejamento', '', '', '', 40, 'NAO', '2025-05-25 13:48:59'),
(30, 'GEAR', 'SubMenuOpcao', '5014', 'Operacional', 'Gerar', '../operacional/opGerarVoosPlanejados.php', '', '', 40, 'NAO', '2025-05-25 13:50:33'),
(31, 'GEAR', 'SubMenuOpcao', '5016', 'Operacional', 'Manter', '../operacional/opManterVoosPlanejados.php', '', '', 40, 'NAO', '2025-05-25 13:50:41'),
(32, 'GEAR', 'SubMenu', '5020', 'Operacional', 'Grupo I - Voos', '', '', '', 40, 'NAO', '2025-05-25 13:58:24'),
(33, 'GEAR', 'SubMenuOpcao', '5022', 'Operacional', 'Gerar', '../operacional/opGerarVoosOperacionais.php', '', '', 40, 'NAO', '2025-05-25 13:51:45'),
(34, 'GEAR', 'SubMenuOpcao', '5024', 'Operacional', 'Manter', '../operacional/opManterVoosOperacionais.php', '', '', 40, 'NAO', '2025-05-25 13:51:52'),
(35, 'GEAR', 'SubMenu', '5030', 'Operacional', 'Grupo II - Status', '', '', '', 40, 'NAO', '2025-05-25 13:54:24'),
(36, 'GEAR', 'MenuOpcao', '5040', 'Operacional', 'Painel de Movimentação', '../operacional/opMovimentos.php?objetivo=painel', '_blank', '', 40, 'NAO', '2025-05-25 13:57:41'),
(37, 'GEAR', 'SubMenuOpcao', '5034', 'Operacional', 'Movimentação', '../operacional/opManterStatus.php?objetivo=movimento', '', '', 40, 'ACR', '2025-08-18 19:31:09'),
(38, 'GEAR', 'SubMenuOpcao', '5032', 'Operacional', 'Manter', '../operacional/opManterStatus.php?objetivo=status', '', '', 40, 'NAO', '2025-05-25 13:56:00'),
(39, 'GEAR', 'Menu', '6000', 'Faturamento', 'Faturamento', '', '', 'faturamento', 45, 'NAO', '2025-05-21 12:48:40'),
(40, 'GEAR', 'MenuOpcao', '6010', 'Faturamento', 'Gerar', '../faturamento/faGerarFaturamento.php', '', '', 45, 'ACR', '2025-08-18 19:27:40'),
(41, 'GEAR', 'MenuOpcao', '6020', 'Faturamento', 'Manter', '../faturamento/faManterFaturamento.php', '', '', 45, 'NAO', '2025-04-04 19:35:18'),
(42, 'GEAR', 'Menu', '7000', 'Credenciamento', 'Credenciamento', '', '', 'credenciamento', 50, 'NAO', '2025-05-21 12:48:53'),
(43, 'GEAR', 'MenuOpcao', '7010', 'Credenciamento', 'Empresas', '../credenciamento/crCadastrarEmpresas.php', '', '', 50, 'NAO', '2025-04-04 19:35:18'),
(44, 'GEAR', 'MenuOpcao', '7020', 'Credenciamento', 'Credenciados', '../credenciamento/crCadastrarCredenciados.php', '', '', 50, 'NAO', '2025-04-04 19:35:18'),
(45, 'GEAR', 'Menu', '8000', 'Vistoria', 'Vistoria', '', '', 'vistoria', 55, 'NAO', '2025-05-21 12:49:03'),
(46, 'GEAR', 'MenuOpcao', '8010', 'Vistoria', 'Itens', '../vistoria/vsCadastrarItens.php', '', '', 55, 'NAO', '2025-05-06 21:50:05'),
(47, 'GEAR', 'MenuOpcao', '8020', 'Vistoria', 'Planos', '../vistoria/vsCadastrarPlanos.php', '', '', 55, 'NAO', '2025-05-06 21:50:44'),
(50, 'GEAR', 'MenuOpcao', '8030', 'Vistoria', 'Resultados', '../vistoria/vsManterResultados.php', '', '', 55, 'NAO', '2025-06-04 22:52:42'),
(51, 'GEAR', 'Menu', '8100', 'Ocorrência', 'Ocorrência', '', '', 'ocorrencia', 60, 'NAO', '2025-05-21 12:49:14'),
(52, 'GEAR', 'MenuOpcao', '8110', 'Ocorrência', 'Itens', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:29:52'),
(53, 'GEAR', 'MenuOpcao', '8120', 'Ocorrência', 'Planos', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:29:59'),
(54, 'GEAR', 'MenuOpcao', '8130', 'Ocorrência', 'Gerar Programação', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:06'),
(55, 'GEAR', 'MenuOpcao', '8140', 'Ocorrência', 'Gerar Vistoria', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:11'),
(56, 'GEAR', 'MenuOpcao', '8150', 'Ocorrência', 'Lançar Resultados', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:17'),
(57, 'GEAR', 'Header', '9000', 'Serviços', 'Serviços', '', '', '', 65, 'NAO', '2025-05-06 23:11:37'),
(58, 'GEAR', 'Opcao', '9010', 'Serviços', 'Alterar Chave de Acesso', '../servicos/svAlterarSenha.php', '', 'senha', 65, 'NAO', '2025-05-21 10:18:30'),
(59, 'GEAR', 'Opcao', '9020', 'Serviços', 'Contato', '../servicos/svContato.php', '', 'email', 65, 'ACR', '2025-08-18 19:32:30'),
(60, 'GEAR', 'Header', '9100', 'Suporte', 'Suporte', '', '', '', 70, 'NAO', '2025-05-06 23:11:45'),
(61, 'GEAR', 'Menu', '9120', 'Suporte', 'Logs', '', '', 'logs', 70, 'NAO', '2025-05-25 14:20:58'),
(62, 'GEAR', 'MenuOpcao', '9122', 'Suporte', 'Atividades', '../servicos/svLogAtividades.php', '', '', 70, 'NAO', '2025-05-25 14:22:09'),
(63, 'GEAR', 'MenuOpcao', '9124', 'Suporte', 'Tarefas', '../tarefas/trLogTarefas.php', '', '', 70, 'NAO', '2025-05-25 14:22:29'),
(64, 'GEAR', 'MenuOpcao', '9126', 'Suporte', 'Limpeza ', '../servicos/svLimparLogs.php', '', '', 70, 'NAO', '2025-05-25 14:22:47'),
(65, 'GEAR', 'Menu', '9140', 'Suporte', 'Tarefas', '', '', 'tarefas', 70, 'NAO', '2025-05-25 23:13:28'),
(66, 'GEAR', 'Menu', '9130', 'Suporte', 'Importações', '', '', 'importacoes', 70, 'NAO', '2025-05-21 12:49:36'),
(67, 'GEAR', 'MenuOpcao', '9131', 'Suporte', 'Equipamentos', '../importacoes/imEquipamentos.php', '', '', 70, 'NAO', '2025-04-04 19:35:18'),
(68, 'GEAR', 'MenuOpcao', '9132', 'Suporte', 'Matrículas', '../importacoes/imMatriculasAnac.php', '', '', 70, 'NAO', '2025-04-04 19:35:18'),
(69, 'GEAR', 'MenuOpcao', '9133', 'Suporte', 'Voos Regulares', '../importacoes/imVoosAnac.php', '', '', 70, 'NAO', '2025-04-04 19:35:18'),
(70, 'GEAR', 'MenuOpcao', '9134', 'Suporte', 'Aeródromos Públicos', '../importacoes/imPublicosAnac.php', '', '', 70, 'NAO', '2025-04-04 19:35:18'),
(71, 'GEAR', 'MenuOpcao', '9135', 'Suporte', 'Aeródromos Privados', '../importacoes/imPrivadosAnac.php', '', '', 70, 'NAO', '2025-04-04 19:35:18'),
(72, 'GEAR', 'Menu', '9200', 'Administração', 'Administração', '', '', 'administracao', 20, 'NAO', '2025-05-21 12:47:46'),
(73, 'GEAR', 'MenuOpcao', '9210', 'Administração', 'Clientes', '../administracao/adCadastrarClientes.php', '', '', 20, 'NAO', '2025-05-25 22:43:06'),
(74, 'GEAR', 'MenuOpcao', '9245', 'Administração', 'Tarifas', '../administracao/adCadastrarTarifas.php', '', '', 20, 'ACR', '2025-08-18 19:27:09'),
(75, 'GEAR', 'MenuOpcao', '9220', 'Administração', 'Movimentos', '../administracao/adCadastrarMovimentos.php', '', '', 20, 'ACR', '2025-08-18 19:26:58'),
(76, 'GEAR', 'MenuOpcao', '9235', 'Administração', 'Recursos', '../administracao/adCadastrarRecursos.php', '', '', 20, 'ACR', '2025-08-18 19:28:01'),
(77, 'GEAR', 'SubMenu', '9250', 'Administração', 'Usuários', '', '', '', 20, 'NAO', '2025-08-18 19:28:30'),
(78, 'GEAR', 'SubMenuOpcao', '9252', 'Administração', 'Acessos', '../administracao/adCadastrarAcessos.php', '', '', 20, 'NAO', '2025-06-13 16:20:28'),
(79, 'GEAR', 'MenuOpcao', '9215', 'Administração', 'Menus', '../administracao/adCadastrarMenus.php', '', '', 20, 'NAO', '2025-05-25 22:32:45'),
(80, 'GEAR', 'SubMenuOpcao', '9241', 'Administração', 'Formulários', '../administracao/adCadastrarRestricoes.php', '', '', 20, 'NAO', '2025-05-25 22:41:38'),
(81, 'MAER', 'Opcao', '1000', 'Logout', 'Sair', '../suporte/suLogout.php', '', 'exit', 10, 'NAO', '2025-05-21 09:37:24'),
(82, 'MAER', 'Menu', '2000', 'Cadastros', 'Cadastros', '', '', 'cadastros', 25, 'NAO', '2025-05-21 12:49:47'),
(83, 'MAER', 'MenuOpcao', '2015', 'Cadastros', 'Comandantes', '../cadastros/cdCadastrarComandantes.php', '', '', 25, 'NAO', '2025-05-25 23:07:14'),
(84, 'MAER', 'Menu', '2200', 'Consultas', 'Consultas', '', '', 'consultas', 30, 'NAO', '2025-05-21 12:49:57'),
(85, 'MAER', 'MenuOpcao', '2210', 'Consultas', 'Aeroportos', '../consultas/csConsultarAeroportos.php', '', '', 30, 'NAO', '2025-04-04 19:35:18'),
(86, 'MAER', 'MenuOpcao', '2220', 'Consultas', 'Equipamentos', '../consultas/csConsultarEquipamentos.php', '', '', 30, 'NAO', '2025-04-04 19:35:18'),
(88, 'MAER', 'MenuOpcao', '2225', 'Consultas', 'Matrículas', '../consultas/csConsultarMatriculas.php', '', '', 30, 'NAO', '2025-05-25 23:09:20'),
(89, 'MAER', 'MenuOpcao', '2215', 'Consultas', 'Comandantes', '../consultas/csConsultarComandantes.php', '', '', 30, 'NAO', '2025-05-25 23:08:17'),
(90, 'MAER', 'MenuOpcao', '2240', 'Consultas', 'Recursos', '../consultas/csConsultarRecursos.php', '', '', 30, 'NAO', '2025-05-25 23:10:08'),
(91, 'MAER', 'MenuOpcao', '2245', 'Consultas', 'Tarifas', '../consultas/csConsultarTarifas.php', '', '', 30, 'NAO', '2025-05-25 23:10:16'),
(92, 'MAER', 'Menu', '3000', 'Informativos', 'Informativos', '', '', 'informativos', 35, 'NAO', '2025-05-21 12:50:10'),
(93, 'MAER', 'SubMenu', '3020', 'Informativos', 'Movimentos Grupo II', '', '', '', 35, 'NAO', '2025-08-16 17:33:58'),
(94, 'MAER', 'MenuOpcao', '3021', 'Informativos', 'Página 1', '../siv/inVisualizadorInformativos.html?pagina=../siv/GEAR_MovimentosGrupoII_P1.html', '_blank', '', 35, 'NAO', '2025-09-18 15:50:56'),
(95, 'MAER', 'MenuOpcao', '3022', 'Informativos', 'Página 2', '../siv/inVisualizadorInformativos.html?pagina=../siv/GEAR_MovimentosGrupoII_P2.html', '_blank', '', 35, 'NAO', '2025-09-18 15:51:23'),
(96, 'MAER', 'Menu', '5000', 'Operacional', 'Operacional', '', '', 'operacional', 40, 'NAO', '2025-05-21 12:19:34'),
(100, 'MAER', 'Menu', '6000', 'Faturamento', 'Faturamento', '', '', 'faturamento', 45, 'NAO', '2025-05-21 12:50:37'),
(101, 'MAER', 'MenuOpcao', '6010', 'Faturamento', 'Gerar', '../faturamento/faGerarFaturamento.php', '', '', 45, 'ACR', '2025-08-18 19:34:45'),
(102, 'MAER', 'MenuOpcao', '6020', 'Faturamento', 'Manter', '../faturamento/faManterFaturamento.php', '', '', 45, 'NAO', '2025-04-04 19:35:18'),
(103, 'MAER', 'Header', '9000', 'Serviços', 'Serviços', '', '', '', 65, 'NAO', '2025-05-06 23:11:54'),
(104, 'MAER', 'Opcao', '9010', 'Serviços', 'Alterar Chave de Acesso', '../servicos/svAlterarSenha.php', '', 'senha', 65, 'NAO', '2025-05-21 10:18:25'),
(106, 'VAER', 'Opcao', '1000', 'Logout', 'Sair', '../suporte/suLogout.php', '', 'exit', 10, 'NAO', '2025-05-21 09:37:30'),
(107, 'VAER', 'Menu', '8000', 'Vistoria', 'Vistoria', '', '', 'vistoria', 55, 'NAO', '2025-05-21 12:50:49'),
(108, 'VAER', 'MenuOpcao', '8010', 'Vistoria', 'Itens', '../vistoria/vsCadastrarItens.php', '', '', 55, 'NAO', '2025-04-04 19:35:18'),
(109, 'VAER', 'MenuOpcao', '8020', 'Vistoria', 'Planos', '../vistoria/vsCadastrarPlanos.php', '', '', 55, 'NAO', '2025-04-04 19:35:18'),
(112, 'VAER', 'MenuOpcao', '8030', 'Vistoria', 'Resultados', '../vistoria/vsManterResultados.php', '', '', 55, 'NAO', '2025-06-04 22:53:07'),
(113, 'VAER', 'Menu', '8100', 'Ocorrência', 'Ocorrência', '', '', 'ocorrencia', 60, 'NAO', '2025-05-21 12:51:00'),
(114, 'VAER', 'MenuOpcao', '8110', 'Ocorrência', 'Itens', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:25'),
(115, 'VAER', 'MenuOpcao', '8120', 'Ocorrência', 'Planos', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:35'),
(116, 'VAER', 'MenuOpcao', '8130', 'Ocorrência', 'Gerar Programação', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:41'),
(117, 'VAER', 'MenuOpcao', '8140', 'Ocorrência', 'Gerar Vistoria', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:51'),
(118, 'VAER', 'MenuOpcao', '8150', 'Ocorrência', 'Lançar Resultados', '../suporte/suAImplementar.php', '', '', 60, 'NAO', '2025-06-05 00:30:57'),
(119, 'MAER', 'Header', '0000', 'Disponíveis', 'Módulos Disponíveis', '', '', '', 0, 'NAO', '2025-05-13 23:14:58'),
(120, 'MAER', 'Opcao', '9020', 'Serviços', 'Contato', '../servicos/svContato.php', '', 'email', 65, 'SIM', '2025-05-21 10:13:00'),
(121, 'GEAR', 'SubMenu', '2230', 'Consultas', 'Operadores Aéreos', '', '', '', 30, 'NAO', '2025-05-13 12:21:21'),
(122, 'GEAR', 'SubMenuOpcao', '2032', 'Cadastros', 'Informações Cobrança', '../cadastros/cdCadastrarOperadoresCobranca.php', '', '', 25, 'NAO', '2025-05-13 12:11:01'),
(124, 'GEAR', 'SubMenuOpcao', '2232', 'Consultas', 'Informações Cobrança', '../consultas/csConsultarOperadoresCobranca.php', '', '', 30, 'NAO', '2025-05-13 12:23:00'),
(126, 'GEAR', 'SubMenu', '2030', 'Cadastros', 'Operadores Aéreos', '', '', 'clientes', 25, 'NAO', '2025-05-21 09:51:59'),
(127, 'MAER', 'SubMenu', '2230', 'Consultas', 'Operadores Aéreos', '', '', '', 30, 'NAO', '2025-05-13 23:08:54'),
(128, 'MAER', 'SubMenuOpcao', '2231', 'Consultas', 'Informações RAB', '../consultas/csConsultarOperadoresRAB.php', '', '', 30, 'NAO', '2025-05-13 23:09:16'),
(129, 'MAER', 'SubMenuOpcao', '2232', 'Consultas', 'Informações Cobrança', '../consultas/csConsultarOperadoresCobranca.php', '', '', 30, 'NAO', '2025-05-13 23:09:35'),
(130, 'VAER', 'Header', '0000', 'Disponíveis', 'Módulos Disponíveis', '', '', '', 0, 'NAO', '2025-05-13 23:15:35'),
(131, 'GEAR', 'Header', '0000', 'Disponíveis', 'Módulos Disponíveis', '', '', '', 0, 'NAO', '2025-09-17 00:47:50'),
(132, 'GEAR', 'MenuOpcao', '9142', 'Suporte', 'Controlar Propagandas', '../servicos/svControlarPropagandas.php', '', '', 72, 'NAO', '2025-08-14 21:40:33'),
(133, 'GEAR', 'MenuOpcao', '9148', 'Suporte', 'Processar Voos Operacionais', '../servicos/svProcessarVoosOperacionais.php', '', '', 74, 'NAO', '2025-09-18 19:50:10'),
(134, 'GEAR', 'SubMenuOpcao', '5012', 'Operacional', 'Voos ANAC', '../operacional/opPesquisarVoosANAC.php', '', '', 40, 'NAO', '2025-05-25 13:49:09'),
(135, 'GEAR', 'SubMenuOpcao', '5026', 'Operacional', 'Movimentação', '../suporte/suAImplementar.php', '', '', 40, 'NAO', '2025-05-26 19:20:49'),
(136, 'MAER', 'SubMenu', '5030', 'Operacional', 'Grupo II - Status', '', '', '', 40, 'NAO', '2025-05-25 13:59:09'),
(137, 'MAER', 'SubMenuOpcao', '5032', 'Operacional', 'Manter', '../operacional/opManterStatus.php?objetivo=status', '', '', 40, 'NAO', '2025-05-25 13:59:53'),
(138, 'MAER', 'SubMenuOpcao', '5034', 'Operacional', 'Movimentação', '../operacional/opManterStatus.php?objetivo=movimento', '', '', 40, 'ACR', '2025-08-18 19:34:33'),
(139, 'GEAR', 'MenuOpcao', '9141', 'Suporte', 'Manter', '../tarefas/trCadastrarTarefas.php', '', '', 70, 'NAO', '2025-08-14 21:38:28'),
(140, 'GEAR', 'SubMenuOpcao', '9251', 'Administração', 'Manter', '../administracao/adCadastrarUsuarios.php', '', '', 20, 'ACR', '2025-08-18 19:26:49'),
(141, 'GEAR', 'SubMenu', '9240', 'Administração', 'Restrições', '', '', '', 20, 'NAO', '2025-05-25 22:41:17'),
(142, 'GEAR', 'SubMenuOpcao', '9242', 'Administração', 'Operações', '../suporte/suAimplementar.php', '', '', 20, 'NAO', '2025-05-25 22:42:35'),
(144, 'GEAR', 'MenuOpcao', '9145', 'Suporte', 'Construtor de Informativos', '../servicos/svConstrutorInformativos.php', '', '', 70, 'NAO', '2025-08-14 21:42:28'),
(145, 'GEAR', 'MenuOpcao', '9150', 'Suporte', 'Processar Status', '../servicos/svProcessarStatus.php', '', '', 74, 'NAO', '2025-09-18 19:50:33'),
(146, 'GEAR', 'Menu', '5100', 'Reserva', 'Reservas', '', '', 'reservas', 40, 'NAO', '2025-08-14 20:17:32'),
(147, 'GEAR', 'MenuOpcao', '5110', 'Reserva', 'Avaliar', '../reserva/rsAvaliarReservas.php', '', '', 40, 'NAO', '2025-08-14 19:54:14'),
(148, 'GEAR', 'MenuOpcao', '5120', 'Reserva', 'Usuários', '../reserva/rsCadastrarReservasUsuarios.php', '', '', 40, 'NAO', '2025-08-14 19:54:21'),
(149, 'GEAR', 'MenuOpcao', '9143', 'Suporte', 'Processar Conexões', '../servicos/svProcessarConexoes.php', '', '', 74, 'NAO', '2025-09-18 19:49:47'),
(150, 'GEAR', 'MenuOpcao', '3100', 'Informativos', 'Painel Operacional', '../siv/inVisualizadorInformativos.html?pagina=../informativos/inPainelOperacional.php', '_blank', '', 35, 'NAO', '2025-09-18 15:19:07'),
(155, 'GEAR', 'SubMenuOpcao', '2420', 'Gráficos', 'Pousos e Decolagens', '../estatisticas/esGraficoPousosDecolagens.php', '_blank', '', 35, 'GRF', '2025-09-15 20:52:00'),
(163, 'GEAR', 'SubMenuOpcao', '2425', 'Gráficos', 'Reservas', '../estatisticas/esGraficoReservas.php', '_blank', '', 35, 'GRF', '2025-09-15 20:52:07'),
(164, 'GEAR', 'SubMenuOpcao', '2415', 'Gráficos', 'Ocupação', '../estatisticas/esGraficoOcupacao.php', '_blank', '', 35, 'GRF', '2025-09-15 20:51:51'),
(167, 'GEAR', 'SubMenuOpcao', '2405', 'Gráficos', 'Conexões', '../estatisticas/esGraficoConexoes.php', '_blank', '', 35, 'GRF', '2025-09-15 20:51:35'),
(170, 'GEAR', 'MenuOpcao', '9217', 'Administração', 'Monitores', '../administracao/adCadastrarMonitores.php', '', '', 20, 'NAO', '2025-09-10 22:31:36'),
(171, 'GEAR', 'Menu', '2300', 'Estatísticas', 'Estatísticas', '', '', 'cadastros', 32, 'NAO', '2025-09-11 22:39:21'),
(172, 'GEAR', 'MenuOpcao', '2310', 'Estatísticas', 'Pousos e Decolagens', '../estatisticas/esStatusPousosDecolagens.php', '', '', 32, 'NAO', '2025-09-15 20:50:02'),
(175, 'MAER', 'Menu', '2300', 'Estatísticas', 'Estatísticas', '', '', 'cadastros', 32, 'NAO', '2025-09-13 23:36:44'),
(176, 'MAER', 'MenuOpcao', '2310', 'Estatísticas', 'Pousos e Decolagens', '../estatisticas/esStatusPousosDecolagens.php', '', '', 32, 'NAO', '2025-09-15 20:50:50'),
(177, 'MAER', 'MenuOpcao', '3100', 'Informativos', 'Painel Operacional', '../siv/inVisualizadorInformativos.html?pagina=../informativos/inPainelOperacional.php', '_blank', '', 35, 'NAO', '2025-09-18 15:19:46'),
(178, 'MAER', 'SubMenu', '2400', 'Gráficos', 'Gráficos', '', '', '', 35, 'NAO', '2025-09-15 20:52:17'),
(179, 'MAER', 'SubMenuOpcao', '2405', 'Gráficos', 'Conexões', '../estatisticas/esGraficoConexoes.php', '_blank', '', 35, 'GRF', '2025-09-15 20:52:25'),
(180, 'MAER', 'SubMenuOpcao', '2415', 'Gráficos', 'Ocupação', '../estatisticas/esGraficoOcupacao.php', '_blank', '', 35, 'GRF', '2025-09-15 20:52:41'),
(181, 'MAER', 'SubMenuOpcao', '2420', 'Gráficos', 'Pousos e Decolagens', '../estatisticas/esGraficoPousosDecolagens.php', '_blank', '', 35, 'GRF', '2025-09-17 01:23:00'),
(182, 'MAER', 'SubMenuOpcao', '2425', 'Gráficos', 'Reservas', '../estatisticas/esGraficoReservas.php', '_blank', '', 35, 'GRF', '2025-09-15 20:52:55'),
(183, 'GEAR', 'MenuOpcao', '2305', 'Estatísticas', 'Entradas e Saídas', '../estatisticas/esStatusEntradasSaidas.php', '', '', 32, 'NAO', '2025-09-15 20:49:54'),
(184, 'MAER', 'MenuOpcao', '2305', 'Estatísticas', 'Entradas e Saídas', '../estatisticas/esStatusEntradasSaidas.php', '', '', 32, 'NAO', '2025-09-15 20:50:43'),
(185, 'GEAR', 'SubMenuOpcao', '2410', 'Gráficos', 'Entradas e Saídas', '../estatisticas/esGraficoEntradasSaidas.php', '_blank', '', 35, 'GRF', '2025-09-15 20:51:44'),
(186, 'MAER', 'SubMenuOpcao', '2410', 'Gráficos', 'Entradas e Saídas', '../estatisticas/esGraficoEntradasSaidas.php', '_blank', '', 35, 'GRF', '2025-09-15 20:52:31'),
(187, 'GEAR', 'SubMenuOpcao', '2417', 'Gráficos', 'Passageiros', '../estatisticas/esGraficoPassageiros.php', '_blank', '', 35, 'GRF', '2025-09-15 22:29:47'),
(188, 'MAER', 'SubMenuOpcao', '2417', 'Gráficos', 'Passageiros', '../estatisticas/esGraficoPassageiros.php', '_blank', '', 35, 'GRF', '2025-09-15 22:30:11'),
(189, 'GEAR', 'MenuOpcao', '2307', 'Estatísticas', 'Passageiros', '../estatisticas/esStatusPassageiros.php', '', '', 32, 'NAO', '2025-09-15 22:32:43'),
(190, 'MAER', 'MenuOpcao', '2307', 'Estatísticas', 'Passageiros', '../estatisticas/esStatusPassageiros.php', '', '', 32, 'NAO', '2025-09-15 22:33:00'),
(191, 'GEAR', 'MenuOpcao', '9146', 'Suporte', 'Processar Reservas', '../servicos/svProcessarReservas.php', '', '', 74, 'NAO', '2025-09-18 19:50:01'),
(192, 'GEAR', 'MenuOpcao', '6030', 'Faturamento', 'Remessas', '../faturamento/faConsultarRemessas.php', '', '', 45, 'NAO', '2025-11-10 22:21:31'),
(193, 'MAER', 'MenuOpcao', '6030', 'Faturamento', 'Remessas', '../faturamento/faConsultarRemessas.php', '', '', 45, 'NAO', '2025-11-10 22:21:51');

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `gear_status_movimentos`
--

DROP TABLE IF EXISTS `gear_status_movimentos`;
CREATE TABLE IF NOT EXISTS `gear_status_movimentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStatus` bigint UNSIGNED NOT NULL,
  `dhMovimento` datetime NOT NULL,
  `idMovimento` bigint UNSIGNED NOT NULL,
  `movimento` varchar(3) NOT NULL DEFAULT '',
  `idRecurso` bigint UNSIGNED DEFAULT NULL,
  `idSegundoRecurso` bigint UNSIGNED DEFAULT NULL,
  `usuario` varchar(25) NOT NULL DEFAULT 'GEAR',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_status_movimentos_movimento` (`idStatus`,`dhMovimento`,`movimento`),
  KEY `idx_status_movimentos_recurso` (`idRecurso`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_tarefas`
--

DROP TABLE IF EXISTS `gear_tarefas`;
CREATE TABLE IF NOT EXISTS `gear_tarefas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(4) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `tmpTolerancia` int UNSIGNED NOT NULL DEFAULT '180',
  `email` varchar(3) NOT NULL DEFAULT 'SIM',
  `dhExecucao` datetime NULL DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tarefas_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Extraindo dados da tabela `gear_tarefas`
--
TRUNCATE TABLE `gear_tarefas`;
INSERT INTO `gear_tarefas` (`id`, `codigo`, `descricao`, `email`, `dhExecucao`, `modo`, `situacao`, `cadastro`) VALUES
(1, 'APRI', 'ANAC - Aeroportos Privados ', 'NAO', NULL, '', 'ATV', '2024-11-02 17:03:56'),
(2, 'APUB', 'ANAC - Aeroportos Públicos', 'NAO', NULL, '', 'ATV', '2024-11-02 17:03:48'),
(3, 'IEQP', 'ICAO - Equipamentos', 'NAO', '2025-05-26 12:22:45', 'MNL', 'ATV', '2025-05-26 18:17:24'),
(4, 'MATR', 'RAB - Matrículas', 'NAO', '2025-08-12 21:19:34', 'MNL', 'ATV', '2024-11-02 17:04:03'),
(5, 'GVOP', 'GEAR - Gerar Voos Operacionais', 'NAO', '2025-05-26 20:05:14', 'AUT', 'ATV', '2025-05-26 19:03:00'),
(6, 'ELOG', 'GEAR - Excluir arquivo e registros de log', 'NAO', NULL, '', 'ATV', '2025-05-26 18:19:19'),
(7, 'VOOS', 'ANAC - Voos regulares', 'NAO', NULL, '', 'ATV', '2024-11-08 18:59:49'),
(8, 'PVOP', 'GEAR - Processar Voos Operacionais', 'NAO', '2025-05-26 19:10:25', 'MNL', 'ATV', '2025-05-26 18:19:59'),
(9, 'PRPG', 'GEAR - Controlar Propagadas', 'NAO', '2025-05-29 13:57:16', 'MNL', 'ATV', '2025-05-26 18:19:50'),
(10, 'GVPL', 'GEAR - Gerar Voos Planejados', 'NAO', '2025-05-26 20:05:13', 'AUT', 'ATV', '2025-05-26 18:19:40'),
(11, 'CINF', 'GEAR - Construtor de Informativos', 'NAO', '2025-05-29 14:19:39', 'MNL', 'ATV', '2025-05-27 14:23:06'),
(12, 'PSTA', 'GEAR - Processar Status', 'NAO', '2025-06-19 20:07:10', 'MNL', 'ATV', '2025-06-19 20:00:06'),
(13, 'WRSR', 'WhatsApp - Robo para a Reserva de voos', 'NAO', '2025-08-14 22:38:54', 'AUT', 'ATV', '2025-07-22 17:26:58'),
(14, 'PRSR', 'GEAR  - Processar Reservas', 'NAO', '2025-08-14 22:39:56', 'MNL', 'ATV', '2025-08-14 22:32:49');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_operadores`
--

DROP TABLE IF EXISTS `gear_operadores`;
CREATE TABLE IF NOT EXISTS `gear_operadores` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `operador` varchar(25) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `iata` varchar(2) NOT NULL DEFAULT '',
  `icao` varchar(3) NOT NULL DEFAULT '',
  `grupo` varchar(1) NOT NULL DEFAULT '2',
  `cpfCnpj` varchar(14) DEFAULT NULL,
  `idMatriz` bigint DEFAULT NULL,
  `idCobranca` bigint DEFAULT NULL,
  `fonte` varchar(4) NOT NULL DEFAULT 'ANAC',
  `origem` varchar(3) NOT NULL DEFAULT 'IMP', 
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_operadores_operador` (`operador`,`cpfCnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_operadores`
--

DROP TRIGGER IF EXISTS `buOperadores`;
DELIMITER $$
CREATE TRIGGER `buOperadores` BEFORE UPDATE ON `gear_operadores` FOR EACH ROW BEGIN
  IF (new.operador <> old.operador) THEN
   	IF (SELECT COUNT(*) FROM gear_matriculas mt WHERE mt.idOperador = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'MATRICULAS => Operador Aéreo não pode ser alterado';
		END IF;
  END IF;
END
$$
DELIMITER ;

--
-- Estrutura da tabela `gear_operadores_cobranca`
--

DROP TABLE IF EXISTS `gear_operadores_cobranca`;
CREATE TABLE IF NOT EXISTS `gear_operadores_cobranca` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `operador` varchar(25) NOT NULL,
  `nome` varchar(100) NOT NULL DEFAULT '',
  `cpfCnpj` varchar(18) DEFAULT NULL,
  `endereco` varchar(100) NOT NULL DEFAULT '',
  `complemento` varchar(100) NOT NULL DEFAULT '',
  `bairro` varchar(100) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(2) NOT NULL DEFAULT '',
  `cep` varchar(10) NOT NULL DEFAULT '',
  `contato` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `fonte` varchar(4) NOT NULL DEFAULT '',
  `origem` varchar(3) NOT NULL DEFAULT 'MNL', 
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_operadores_cobranca_operador` (`operador`,`cpfCnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_calculos`
--

DROP TABLE IF EXISTS `gear_calculos`;
CREATE TABLE IF NOT EXISTS `gear_calculos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idFaturamento` bigint UNSIGNED NOT NULL,
  `idStatus` bigint UNSIGNED NOT NULL,
  `dhPouso` datetime NULL,
  `dhDecolagem` datetime NULL,
  `tmpPatio` int UNSIGNED DEFAULT NULL,
  `tmpEstadia` int UNSIGNED DEFAULT NULL,
  `tmpIsento` int UNSIGNED DEFAULT NULL,
  `vlrPPO` decimal(8,2) NOT NULL,
  `vlrPPM` decimal(8,2) NOT NULL,
  `vlrPPE` decimal(8,2) NOT NULL,  
  `situacao` varchar(3) NOT NULL DEFAULT 'NCN',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_calculos_faturamentos` (`idFaturamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_pessoas_credenciadas`
--

DROP TABLE IF EXISTS `gear_pessoas_credenciadas`;
CREATE TABLE IF NOT EXISTS `gear_pessoas_credenciadas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idEmpresa` bigint UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `documento` varchar(25) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `bairro` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `cargo` varchar(25) NOT NULL,
  `responsavel` varchar(3) NOT NULL DEFAULT 'NAO',
  `credencial` varchar(25) NOT NULL,
  `idArea` bigint UNSIGNED NOT NULL,
  `validade` datetime DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pessoas_credenciadas_empresa` (`idEmpresa`,`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_pessoas_credenciadas`
--
DROP TRIGGER IF EXISTS `biPessoasCredenciadas`;
DELIMITER $$
CREATE TRIGGER `biPessoasCredenciadas` BEFORE INSERT ON `gear_pessoas_credenciadas` FOR EACH ROW BEGIN
  -- Gera o número do credenciamento
	SET new.credencial = (SELECT IFNULL((SELECT LPAD(MAX(credencial)+1,6,'0')
                			    FROM gear_pessoas_credenciadas 
                          WHERE idEmpresa = new.idEmpresa
                	        GROUP BY idEmpresa), '000001'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_propagandas`
--

DROP TABLE IF EXISTS `gear_propagandas`;
CREATE TABLE IF NOT EXISTS `gear_propagandas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `empresa` varchar(25) NOT NULL,
  `propaganda` varchar(25) NOT NULL,
  `dtInicio` datetime NOT NULL,
  `dtFinal` datetime NOT NULL,
  `dhExibicao` datetime NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'AGD',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_propagandas_empresa` (`idAeroporto`,`empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_recursos`
--

DROP TABLE IF EXISTS `gear_recursos`;
CREATE TABLE IF NOT EXISTS `gear_recursos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `recurso` varchar(25) NOT NULL,
  `descricao` varchar(50) NOT NULL DEFAULT 'Recurso',
  `tipo` varchar(3) NOT NULL,
  `utilizacao` varchar(3) NOT NULL,
  `natureza` varchar(3) NOT NULL,
  `classe` varchar(3) NOT NULL,
  `sentido` varchar(3) NOT NULL,
  `capacidade` int UNSIGNED DEFAULT NULL,
  `unidade` varchar(3) NOT NULL DEFAULT 'UND',
  `envergadura` decimal(5,2) NOT NULL DEFAULT '0.00',
  `comprimento` decimal(5,2) NOT NULL DEFAULT '0.00',
  `idDireita` bigint UNSIGNED,
  `idEsquerda` bigint UNSIGNED,
  `idGrupamento` bigint UNSIGNED,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_recursos_aeroporto_recurso` (`idAeroporto`,`recurso`,`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_recursos`
--
DELETE FROM `gear_recursos`;
INSERT INTO `gear_recursos` (`id`, `idAeroporto`, `recurso`, `descricao`, `tipo`, `utilizacao`, `natureza`, `classe`, `sentido`, `capacidade`, `unidade`, `situacao`) VALUES
(1, 0, 'POS 1', 'Posição 01', 'POS', 'PAT', 'PAX', 'DOM', 'NAN', 1, 'UND', 'ATV'),
(2, 0, 'POS 2', 'Posição 02', 'POS', 'EST', 'PAX', 'INT', '', 1, 'UND', 'ATV'),
(3, 0, 'POS 3', 'Posição 03', 'POS', 'ISE', 'PAX', 'DOM', 'DES', 1, 'UND', 'ATV'),
(4, 0, 'PIS 1', 'Pista 01', 'PIS', 'NAN', 'NAN', 'NAN', 'NAN', 0, 'NAN', 'ATV'),
(5, 0, 'PIS 2', 'Pista 02', 'PIS', 'NAN', 'NAN', 'NAN', '', 0, 'NAN', 'ATV'),
(6, 0, 'PIS 3', 'Pista 03', 'PIS', 'NAN', 'NAN', 'NAN', '', 0, 'NAN', 'ATV'),
(7, 0, 'AREA 1', 'Área 01', 'ARA', 'NAN', 'NAN', 'NAN', 'EMB', 0, 'NAN', 'ATV'),
(8, 0, 'AREA 2', 'Área 02', 'ARA', 'NAN', 'NAN', 'NAN', 'NAN', 0, 'NAN', 'ATV'),
(9, 0, 'AREA 3', 'Área 03', 'ARA', 'NAN', 'NAN', 'NAN', 'NAN', 0, 'NAN', 'ATV'),
(10, 0, 'POR 01', 'Portão 01', 'POR', 'NAN', 'PAX', 'INT', '', 150, 'PAX', 'ATV'),
(11, 0, 'POR 02', 'Portão 02', 'POR', 'NAN', 'PAX', 'INT', '', 150, 'PAX', 'ATV'),
(12, 0, 'POR 03', 'Portão 03', 'POR', 'NAN', 'PAX', 'INT', '', 150, 'PAX', 'ATV');

-- 
-- Acionadores `gear_recursos`
--
-- DROP TRIGGER IF EXISTS `buRecursos`;
-- DELIMITER $$
-- CREATE TRIGGER `buRecursos` BEFORE UPDATE ON `gear_recursos` FOR EACH ROW BEGIN
--   IF (new.recurso <> old.recurso) THEN
--    	IF (SELECT COUNT(*) FROM gear_status_movimentos mo WHERE mo.idRecurso = new.id) <> 0 THEN
--   		SIGNAL SQLSTATE '45000'
-- 			SET MESSAGE_TEXT = 'STATUS MOVIMENTOS => Recurso não pode ser alterado';
-- 		END IF;
--   END IF;
--     IF (new.recurso <> old.recurso) THEN
--    	IF (SELECT COUNT(*) FROM gear_voos_movimentos mo WHERE mo.idRecurso = new.id) <> 0 THEN
--   		SIGNAL SQLSTATE '45000'
-- 			SET MESSAGE_TEXT = 'VOOS MOVIMENTOS => Recurso não pode ser alterado';
-- 		END IF;
--   END IF;
-- END
-- $$
-- DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_restricoes`
--

DROP TABLE IF EXISTS `gear_restricoes`;
CREATE TABLE IF NOT EXISTS `gear_restricoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `sistema` varchar(4) NOT NULL,
  `formulario` varchar(4) NOT NULL,
  `grupo` varchar(3) NOT NULL,
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_restricoes_aeroporto` (`idAeroporto`,`sistema`,`formulario`,`grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `gear_status`
--

DROP TABLE IF EXISTS `gear_status`;
CREATE TABLE IF NOT EXISTS `gear_status` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `ano` varchar(4) NOT NULL DEFAULT '0000',
  `mes` varchar(2) NOT NULL DEFAULT '00',
  `numero` varchar(6) NOT NULL DEFAULT '000000',
  `idMatricula` bigint UNSIGNED NOT NULL,
  `classe` varchar(3) NOT NULL DEFAULT '???',
  `natureza` varchar(3) NOT NULL DEFAULT '???',
  `servico` varchar(3) NOT NULL DEFAULT '???',
  `idOrigem` bigint UNSIGNED NOT NULL,
  `idDestino` bigint UNSIGNED DEFAULT NULL,
  `idPMovimento` bigint UNSIGNED DEFAULT NULL,
  `idUMovimento` bigint UNSIGNED DEFAULT NULL,
  `idStatusPai` bigint UNSIGNED DEFAULT NULL,
  `idChegada` bigint UNSIGNED DEFAULT NULL,
  `idPartida` bigint UNSIGNED DEFAULT NULL,
  `faturado` varchar(3) NOT NULL DEFAULT 'NAO',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_status` (`idAeroporto`,`ano`,`mes`,`numero`),
  KEY `idx_status_matricula` (`idMatricula`),
  KEY `idx_status_aeroportos_origem` (`idOrigem`),
  KEY `idx_status_aeroportos_destino` (`idDestino`),
  KEY `idx_status_voo_chegada` (`idChegada`),
  KEY `idx_status_voo_partida` (`idPartida`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_status`
--
DROP TRIGGER IF EXISTS `biStatus`;
DELIMITER $$
CREATE TRIGGER `biStatus` BEFORE INSERT ON `gear_status` FOR EACH ROW BEGIN
  -- Gera o número do status
	SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,6,'0')
                			FROM gear_status 
					            WHERE idAeroporto = new.idAeroporto 
                        AND ano = DATE_FORMAT(new.cadastro, '%Y')
                        AND mes = DATE_FORMAT(new.cadastro, '%m')
                	    GROUP BY ano, mes), '000001'));
  SET new.ano = DATE_FORMAT(new.cadastro, '%Y');
  SET new.mes = DATE_FORMAT(new.cadastro, '%m');
END
$$
DELIMITER ;


--
-- Estrutura da tabela `gear_veiculos_credenciados`
--

DROP TABLE IF EXISTS `gear_veiculos_credenciados`;
CREATE TABLE IF NOT EXISTS `gear_veiculos_credenciados` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idEmpresa` bigint UNSIGNED NOT NULL,
  `placa` varchar(8) NOT NULL,
  `marca` varchar(25) NOT NULL,
  `modelo` varchar(25) NOT NULL,
  `cor` varchar(25) NOT NULL,
  `tipo` varchar(25) NOT NULL,
  `credencial` varchar(25) NOT NULL,
  `idArea` bigint UNSIGNED NOT NULL,
  `validade` datetime DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_veiculos_credenciados_empresa` (`idEmpresa`,`placa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_veiculos_credenciados`
--
DROP TRIGGER IF EXISTS `biVeiculosCredenciados`;
DELIMITER $$
CREATE TRIGGER `biVeiculosCredenciados` BEFORE INSERT ON `gear_veiculos_credenciados` FOR EACH ROW BEGIN
  -- Gera o número do credenciamento
	SET new.credencial = (SELECT IFNULL((SELECT LPAD(MAX(credencial)+1,6,'0')
                			    FROM gear_veiculos_credenciados 
                          WHERE idEmpresa = new.idEmpresa
                	        GROUP BY idEmpresa), '000001'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_voos_anac`
--

DROP TABLE IF EXISTS `gear_voos_anac`;
CREATE TABLE IF NOT EXISTS `gear_voos_anac` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `operador` varchar(3) NOT NULL DEFAULT '',
  `empresa` varchar(100) NOT NULL,
  `numeroVoo` varchar(5) NOT NULL,
  `equipamento` varchar(10) NOT NULL DEFAULT '',
  `segunda` varchar(1) NOT NULL DEFAULT '0',
  `terca` varchar(1) NOT NULL DEFAULT '0',
  `quarta` varchar(1) NOT NULL DEFAULT '0',
  `quinta` varchar(1) NOT NULL DEFAULT '0',
  `sexta` varchar(1) NOT NULL DEFAULT '0',
  `sabado` varchar(1) NOT NULL DEFAULT '0',
  `domingo` varchar(1) NOT NULL DEFAULT '0',
  `assentos` int UNSIGNED DEFAULT NULL,
  `siros` varchar(30) NOT NULL,
  `situacaoSiros` varchar(30) NOT NULL,
  `dataRegistro` datetime NOT NULL,
  `inicioOperacao` datetime NOT NULL,
  `fimOperacao` datetime NOT NULL,
  `naturezaOperacao` varchar(30) NOT NULL,
  `numeroEtapa` int UNSIGNED DEFAULT NULL,
  `icaoOrigem` varchar(4) NOT NULL,
  `aeroportoOrigem` varchar(100) NOT NULL,
  `icaoDestino` varchar(4) NOT NULL,
  `aeroportoDestino` varchar(100) NOT NULL,
  `horarioPartida` time NOT NULL,
  `horarioChegada` time NOT NULL,
  `servico` varchar(50) NOT NULL,
  `objetoTransporte` varchar(30) NOT NULL,
  `codeshare` varchar(250) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `origem` varchar(3) NOT NULL DEFAULT 'MNL',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_voos_anac_voo` (`operador`,`numeroVoo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_matriculas_anac`
--

DROP TABLE IF EXISTS `gear_matriculas_anac`;
CREATE TABLE IF NOT EXISTS `gear_matriculas_anac` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `marca` varchar(10) NOT NULL,
  -- `proprietario` varchar(250) NOT NULL,
  -- `outros_proprietarios` varchar(250) NOT NULL,
  -- `sg_uf` varchar(2) NOT NULL,
  -- `cpf_cnpj` varchar(18) NOT NULL,
  `nm_operador` varchar(250) NOT NULL,
  -- `outros_operadores` varchar(250) NOT NULL,
  `uf_operador` varchar(2) NOT NULL,
  `cpf_cgc` varchar(18) NOT NULL,
  -- `nr_cert_matricula` varchar(15) NOT NULL,
  -- `nr_serie` varchar(15) NOT NULL,
  `cd_categoria` varchar(10) NOT NULL,
  `cd_tipo` varchar(10) NOT NULL,
  `ds_modelo` varchar(30) NOT NULL,
  `nm_fabricante` varchar(250) NOT NULL,
  `cd_cls` varchar(10) NOT NULL,
  `nr_pmd` int UNSIGNED NOT NULL,
  `cd_tipo_icao` varchar(10) NOT NULL,
  -- `nr_tripulacao_min` int UNSIGNED NOT NULL,
  -- `nr_passageiros_max` int UNSIGNED NOT NULL,
  `nr_assentos` int UNSIGNED NOT NULL,
  -- `nr_ano_fabricacao` varchar(4) NOT NULL,
  -- `dt_validade_cva` varchar(10) NOT NULL,
  -- `dt_validade_ca` varchar(10) NOT NULL,
  -- `dt_canc` varchar(250) NOT NULL,
  -- `ds_motivo_canc` varchar(250) NOT NULL,
  -- `cd_interdicao` varchar(250) NOT NULL,
  -- `cd_marca_nac1` varchar(250) NOT NULL,
  -- `cd_marca_nac2` varchar(250) NOT NULL,
  -- `cd_marca_nac3` varchar(250) NOT NULL,
  -- `cd_marca_estrangeira` varchar(250) NOT NULL,
  `ds_gravame` varchar(250) NOT NULL,
  -- `dt_matricula` varchar(250) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `origem` varchar(3) NOT NULL DEFAULT 'MNL',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_matriculas_anac_marca` (`marca`,`nm_operador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_voos_planejados`
--

DROP TABLE IF EXISTS `gear_voos_planejados`;
CREATE TABLE IF NOT EXISTS `gear_voos_planejados` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `operador` varchar(3) NOT NULL DEFAULT '',
  `empresa` varchar(100) NOT NULL,
  `numeroVoo` varchar(5) NOT NULL,
  `equipamento` varchar(10) NOT NULL DEFAULT '',
  `segunda` varchar(1) NOT NULL DEFAULT '0',
  `terca` varchar(1) NOT NULL DEFAULT '0',
  `quarta` varchar(1) NOT NULL DEFAULT '0',
  `quinta` varchar(1) NOT NULL DEFAULT '0',
  `sexta` varchar(1) NOT NULL DEFAULT '0',
  `sabado` varchar(1) NOT NULL DEFAULT '0',
  `domingo` varchar(1) NOT NULL DEFAULT '0',
  `assentos` int UNSIGNED DEFAULT NULL,
  `siros` varchar(30) NOT NULL,
  `situacaoSiros` varchar(30) NOT NULL,
  `dataRegistro` datetime NOT NULL,
  `inicioOperacao` datetime NOT NULL,
  `fimOperacao` datetime NOT NULL,
  `naturezaOperacao` varchar(30) NOT NULL,
  `numeroEtapa` int UNSIGNED DEFAULT NULL,
  `icaoOrigem` varchar(4) NOT NULL,
  `icaoDestino` varchar(4) NOT NULL,
  `horarioPartida` time NOT NULL,
  `horarioChegada` time NOT NULL,
  `servico` varchar(50) NOT NULL,
  `objetoTransporte` varchar(30) NOT NULL,
  `codeshare` varchar(250) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `fonte` varchar(4) NOT NULL DEFAULT '',
  `origem` varchar(3) NOT NULL DEFAULT 'MNL',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_voos_planejados_voo` (`operador`,`numeroVoo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `gear_voos_operacionais`
--

DROP TABLE IF EXISTS `gear_voos_operacionais`;
CREATE TABLE IF NOT EXISTS `gear_voos_operacionais` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `operacao` varchar(3) NOT NULL DEFAULT '',
  `operador` varchar(3) NOT NULL DEFAULT '',
  `numeroVoo` varchar(5) NOT NULL,
  `equipamento` varchar(10) NOT NULL DEFAULT '',
  `assentos` int UNSIGNED DEFAULT NULL,
  `dtMovimento` datetime NOT NULL,
  `dhPrevista` datetime NOT NULL,
  `classe` varchar(3) NOT NULL DEFAULT '',
  `natureza` varchar(3) NOT NULL DEFAULT '',
  `servico` varchar(3) NOT NULL DEFAULT '',
  `origem` varchar(4) NOT NULL,
  `destino` varchar(4) NOT NULL,
  `numeroEtapa` int UNSIGNED DEFAULT NULL,
  `codeshare` varchar(250) NOT NULL,
  `idChegada` bigint UNSIGNED DEFAULT NULL,
  `idPartida` bigint UNSIGNED DEFAULT NULL,
  `idPosicao` bigint UNSIGNED DEFAULT NULL,
  `idEsteira` bigint UNSIGNED DEFAULT NULL,
  `idPortao` bigint UNSIGNED DEFAULT NULL,
  `pax` int UNSIGNED DEFAULT NULL,
  `pnae` int UNSIGNED DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `fonte` varchar(4) NOT NULL DEFAULT '',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_voos_operacionais_operacao` (`operacao`,`dhPrevista`,`operador`,`numeroVoo`),
  KEY `idx_voos_operacionais_dhprevista` (`dhPrevista`,`operacao`,`operador`,`numeroVoo`),
  KEY `idx_voos_operacionais_operador` (`operador`,`numeroVoo`,`operacao`,`dhPrevista`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Acionadores `gear_voos_operacionais`
--
DROP TRIGGER IF EXISTS `aiVoosOperacionais`;
DELIMITER $$
CREATE TRIGGER `aiVoosOperacionais` AFTER INSERT ON `gear_voos_operacionais` FOR EACH ROW BEGIN
  INSERT INTO gear_voos_movimentos(idVoo, dhMovimento, movimento, cadastro) values (new.id, new.dhPrevista, 'PRV', UTC_TIMESTAMP());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_tarifas`
--

DROP TABLE IF EXISTS `gear_tarifas`;
CREATE TABLE IF NOT EXISTS `gear_tarifas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `grupo` varchar(1) NOT NULL,
  `inicioPMD` int UNSIGNED DEFAULT NULL,
  `finalPMD` int UNSIGNED DEFAULT NULL,
  `domTPO` decimal(8,2) NOT NULL,
  `domTPM` decimal(8,2) NOT NULL,
  `domTPE` decimal(8,2) NOT NULL,
  `intTPO` decimal(8,2) NOT NULL,
  `intTPM` decimal(8,2) NOT NULL,
  `intTPE` decimal(8,2) NOT NULL,
  `domTPOF` decimal(8,2) NOT NULL,
  `domTPMF` decimal(8,2) NOT NULL,
  `domTPEF` decimal(8,2) NOT NULL,
  `intTPOF` decimal(8,2) NOT NULL,
  `intTPMF` decimal(8,2) NOT NULL,
  `intTPEF` decimal(8,2) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tarifas_aeroporto_grupo` (`idAeroporto`,`grupo`,`inicioPMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
DELETE FROM `gear_tarifas`;
INSERT INTO `gear_tarifas` (`id`, `idAeroporto`, `grupo`, `inicioPMD`, `finalPMD`, `domTPO`, `domTPM`, `domTPE`, `intTPO`, `intTPM`, `intTPE`, `domTPOF`, `domTPMF`, `domTPEF`, `intTPOF`, `intTPMF`, `intTPEF`, `situacao`) VALUES
(1, 0, '1', 0, 0, '7.03', '1.41', '0.30', '23.47', '4.68', '0.95', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(2, 0, '2', 0, 1, '184.85', '30.57', '2.02', '266.05', '28.75', '1.84', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(3, 0, '2', 1, 2, '184.85', '30.57', '2.02', '266.05', '28.75', '1.84', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(4, 0, '2', 2, 4, '224.42', '30.57', '2.02', '468.23', '28.75', '3.73', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(5, 0, '2', 4, 6, '453.96', '30.57', '2.64', '941.72', '34.56', '6.63', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(6, 0, '2', 6, 12, '594.28', '30.57', '4.52', '1239.70', '57.48', '11.44', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(7, 0, '2', 12, 24, '1343.01', '44.39', '8.84', '2798.63', '115.45', '22.61', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(8, 0, '2', 24, 48, '3446.31', '88.95', '17.73', '6283.64', '225.14', '44.95', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(9, 0, '2', 48, 100, '4079.54', '147.24', '29.43', '8534.26', '374.59', '75.02', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(10, 0, '2', 100, 200, '6658.39', '333.59', '66.93', '14184.75', '847.57', '170.23', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(11, 0, '2', 200, 300, '10511.14', '581.61', '116.36', '22575.37', '1482.34', '296.89', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV'),
(12, 0, '2', 300, 0, '17568.04', '845.73', '169.11', '37372.03', '2156.98', '432.56', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'ATV');

-- --------------------------------------------------------
--
-- Estrutura da tabela `gear_voos_movimentos`
--

DROP TABLE IF EXISTS `gear_voos_movimentos`;
CREATE TABLE IF NOT EXISTS `gear_voos_movimentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idVoo` bigint UNSIGNED NOT NULL,
  `dhMovimento` datetime NOT NULL,
  `movimento` varchar(3) NOT NULL DEFAULT '',
  `idRecurso` bigint UNSIGNED DEFAULT NULL,
  `usuario` varchar(25) NOT NULL DEFAULT 'GEAR',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_voos_movimentos_recurso` (`idRecurso`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 

-- --------------------------------------------------------
-- 
-- Estrutura da tabela `gear_movimentos`
--

DROP TABLE IF EXISTS `gear_movimentos`;
CREATE TABLE IF NOT EXISTS `gear_movimentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `movimento` varchar(3) NOT NULL DEFAULT '',
  `descricao` varchar(30) NOT NULL DEFAULT '',
  `operacao` varchar(3) NOT NULL DEFAULT 'TDS',
  `ordem` int UNSIGNED NOT NULL DEFAULT '200',
  `sucessora` varchar(3) NOT NULL DEFAULT '',
  `antes` int NOT NULL DEFAULT 0,
  `depois` int NOT NULL DEFAULT 0,
  `destaque` varchar(15) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;   

-- 
-- Extraindo dados da tabela `gear_movimentos`
--
TRUNCATE TABLE `gear_movimentos`;
INSERT INTO `gear_movimentos` (`id`, `idAeroporto`, `movimento`, `descricao`, `operacao`, `ordem`, `sucessora`, `antes`, `depois`, `antecessoras`, `destaque`, `situacao`, `cadastro`) VALUES
(1, 0, 'PRV', 'Previsão de Chegada', 'CHG', 10, 'ATR', 10, 0, '', 'secondary', 'ATV', '2025-05-29 19:56:02'),
(2, 0, 'PRV', 'Previsão de Partida', 'PRT', 10, 'ATR', 10, 0, '', 'secondary', 'ATV', '2025-05-29 19:56:09'),
(3, 0, 'PRV', 'Previsão', 'STA', 10, 'ATR', 10, 0, 'NAO', 'secondary', 'ATV', '2025-06-14 19:41:30'),
(4, 0, 'POU', 'Pouso', 'CHG', 20, '', 0, 0, '', 'warning', 'ATV', '2025-05-29 19:56:46'),
(5, 0, 'POU', 'Pouso', 'PRT', 20, '', 0, 0, '', 'warning', 'ATV', '2025-05-29 19:56:54'),
(6, 0, 'POU', 'Pouso', 'STA', 20, '', 0, 0, 'PRV,CNF,ATR', 'warning', 'ATV', '2025-05-29 19:31:35'),
(7, 0, 'ENT', 'Entrada na Posição', 'CHG', 30, '', 0, 0, '', 'primary', 'ATV', '2025-05-29 19:57:10'),
(8, 0, 'ENT', 'Entrada na Posição', 'PRT', 30, '', 0, 0, '', 'primary', 'ATV', '2025-05-29 19:57:24'),
(9, 0, 'ENT', 'Entrada', 'STA', 30, '', 0, 0, 'POU,SAI', 'primary', 'ATV', '2025-05-29 19:31:54'),
(10, 0, 'DSM', 'Desembarcando', 'CHG', 40, '', 0, 0, '', 'success', 'ATV', '2025-05-29 23:57:36'),
(11, 0, 'EPX', 'Embarque Próximo', 'PRT', 40, '', 0, 0, '', 'warning', 'ATV', '2025-05-30 00:03:17'),
(12, 0, 'SAI', 'Saída', 'STA', 40, '', 0, 0, 'ENT', 'danger', 'ATV', '2025-05-29 19:32:24'),
(14, 0, 'EIM', 'Embarque Imediato', 'PRT', 60, '', 0, 0, '', 'warning', 'ATV', '2025-05-30 00:03:06'),
(15, 0, 'UCH', 'Última Chamada', 'PRT', 70, '', 0, 0, '', 'danger', 'ATV', '2025-05-30 00:04:00'),
(16, 0, 'ATR', 'Atrasado', 'CHG', 80, 'ETC', 0, 10, '', 'danger', 'ATV', '2025-05-29 23:59:37'),
(17, 0, 'ATR', 'Atrasado', 'PRT', 80, 'ETC', 0, 10, '', 'danger', 'ATV', '2025-05-30 00:00:09'),
(18, 0, 'ATR', 'Atrasado', 'STA', 80, 'ETC', 0, 10, 'PRV,CNF', 'danger', 'ATV', '2025-05-29 23:59:15'),
(19, 0, 'CNC', 'Cancelado', 'CHG', 80, 'ETC', 0, 10, '', 'danger', 'ATV', '2025-05-29 23:57:25'),
(20, 0, 'CNC', 'Cancelado', 'PRT', 80, 'ETC', 0, 10, '', 'danger', 'ATV', '2025-05-30 00:00:22'),
(21, 0, 'CNC', 'Cancelado', 'STA', 80, 'ETC', 0, 10, 'PRV,CNF,ALT,ATR', 'danger', 'ATV', '2025-05-29 23:54:49'),
(22, 0, 'EIT', 'Embarque Interrompido', 'PRT', 80, '', 0, 0, '', 'warning', 'ATV', '2025-05-30 00:00:40'),
(23, 0, 'ALT', 'Alternado', 'CHG', 80, 'ETC', 0, 10, '', 'warning', 'ATV', '2025-05-30 00:02:18'),
(24, 0, 'ALT', 'Alternado', 'PRT', 80, 'ETC', 0, 10, '', 'warning', 'ATV', '2025-05-30 00:02:34'),
(25, 0, 'ALT', 'Alternado', 'STA', 80, 'ETC', 0, 10, 'PRV,CNF', 'warning', 'ATV', '2025-05-29 23:55:49'),
(27, 0, 'DEC', 'Decolagem', 'PRT', 90, '', 0, 0, '', 'success', 'ATV', '2025-05-29 19:57:57'),
(28, 0, 'DEC', 'Decolagem', 'STA', 90, '', 0, 0, 'POU,SAI', 'success', 'ATV', '2025-05-29 19:32:42'),
(29, 0, 'CND', 'Concluido', 'CHG', 100, '', 0, 0, '', 'success', 'ATV', '2025-05-29 20:03:30'),
(30, 0, 'CND', 'Concluido', 'PRT', 100, '', 0, 0, '', 'success', 'ATV', '2025-05-29 20:03:43'),
(31, 0, 'CND', 'Concluido', 'STA', 100, '', 0, 0, 'PRV,CNF,ALT,ATR,CNC,DEC,ETC', 'success', 'ATV', '2025-05-29 20:03:55'),
(32, 0, 'CNF', 'Confirmado', 'CHG', 15, '', 0, 0, '', 'primary', 'ATV', '2025-06-02 20:36:41'),
(33, 0, 'CNF', 'Confirmado', 'PRT', 15, '', 0, 0, '', 'primary', 'ATV', '2025-05-29 19:30:39'),
(34, 0, 'CNF', 'Confirmado', 'STA', 15, 'ATR', 10, 0, 'PRV', 'primary', 'ATV', '2025-06-09 18:02:57'),
(35, 0, 'ETC', 'Etapa Concluída', 'STA', 90, 'CND', 0, 10, 'PRV,CNF,ALT,ATR,CNC,DEC', 'success', 'ATV', '2025-05-29 23:58:38'),
(36, 0, 'ETC', 'Etapa Concluída', 'CHG', 90, 'CND', 0, 10, '', 'success', 'ATV', '2025-05-29 23:58:13'),
(37, 0, 'ETC', 'Etapa Concluída', 'PRT', 90, 'CND', 0, 10, '', 'success', 'ATV', '2025-05-30 00:01:38');

-- 
-- Acionadores `gear_movimentos`
--
DROP TRIGGER IF EXISTS `bdMovimentos`;
DELIMITER $$
CREATE TRIGGER `bdMovimentos` BEFORE DELETE ON `gear_movimentos` FOR EACH ROW BEGIN
 	IF (SELECT COUNT(*) 
      FROM gear_status_movimentos sm 
      LEFT JOIN gear_status st ON st.id = sm.idStatus
      WHERE sm.movimento = old.movimento AND st.idAeroporto = old.idAeroporto
        AND (old.operacao = 'TDS' OR old.operacao = 'STA')) <> 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'MOVIMENTOS => Movimento não pode ser excluído';
  END IF;
  IF (SELECT COUNT(*) 
      FROM gear_voos_movimentos vm
      LEFT JOIN gear_voos_operacionais vo ON vo.id = vm.idVoo
      WHERE vm.movimento = old.movimento AND vo.idAeroporto = old.idAeroporto
        AND (old.operacao = 'TDS' OR old.operacao = vo.operacao)) <> 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'VOOS OPERACIONAL => Movimento não pode ser excluído';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `gear_comandantes`
--

DROP TABLE IF EXISTS `gear_comandantes`;
CREATE TABLE IF NOT EXISTS `gear_comandantes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigoAnac` varchar(6) NOT NULL,
  `nome` varchar(150),
  `telefone` varchar(20),
  `email` varchar(50),
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_comandantes_anac` (`codigoAnac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Acionadores `gear_comandantes`
--

DROP TRIGGER IF EXISTS `bdComandantes`;
DELIMITER $$
CREATE TRIGGER `gear_comandantes` BEFORE DELETE ON `gear_comandantes` FOR EACH ROW BEGIN
 	IF (SELECT COUNT(*) 
      FROM gear_status_complementos sc 
      WHERE sc.idComandante = old.id ) <> 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'COMANDANTES => Piloto não pode ser excluído';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `gear_status_complementos'`
--

DROP TABLE IF EXISTS `gear_status_complementos`;
CREATE TABLE IF NOT EXISTS `gear_status_complementos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStatus` bigint UNSIGNED NOT NULL,
  `idComandante` bigint UNSIGNED NOT NULL,
  `regra` varchar(3) NOT NULL,
  `embarque_pax` int,
  `embarque_carga` int,
  `embarque_correio` int,
  `desembarque_pax` int,
  `desembarque_carga` int,
  `desembarque_correio` int,
  `transito_pax` int,
  `observacao` varchar(255),
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_status_complemento_comandantes` (`idStatus`,`idComandante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ********************************************************
--
-- SISTEMA DE VISTORIAS
--
-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_vistoria_planos_itens`
--

DROP TABLE IF EXISTS `gear_vistoria_planos_itens`;
CREATE TABLE IF NOT EXISTS `gear_vistoria_planos_itens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idPlano` bigint UNSIGNED NOT NULL,
  `idItem` bigint UNSIGNED NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vistoria_planos_itens` (`idPlano`,`idItem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `gear_vistoria_itens`
--

DROP TABLE IF EXISTS `gear_vistoria_itens`;
CREATE TABLE IF NOT EXISTS `gear_vistoria_itens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `tipo` varchar(3) NOT NULL,
  `numero` varchar(6) NOT NULL,
  `item` varchar(250) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vistoria_aeroporto_itens` (`idAeroporto`,`tipo`,`numero`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `gear_vistoria_planos`
--

DROP TABLE IF EXISTS `gear_vistoria_planos`;
CREATE TABLE IF NOT EXISTS `gear_vistoria_planos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `numero` varchar(8) NOT NULL DEFAULT '00000000',
  `finalidade` varchar(250) NOT NULL DEFAULT '',
  `inicio` datetime NOT NULL,
  `frequencia` varchar(1) NOT NULL,
  `quantidade` int UNSIGNED NOT NULL,
  `periodo` varchar(1) NOT NULL,
  `mapa` varchar(25) NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'APG',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vistoria_plano_numero` (`idAeroporto`,`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_vistoria_planos`
--

DROP TRIGGER IF EXISTS `buVistoriaPlanos`;
DELIMITER $$
CREATE TRIGGER `biVistoriaPlanos` BEFORE INSERT ON `gear_vistoria_planos` FOR EACH ROW BEGIN
  -- Gera o número do plano por ano 
  SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,8,'0')
                      FROM gear_vistoria_planos 
                      WHERE idAeroporto = new.idAeroporto), '00000001'));
  IF (LEFT(new.numero,2) <> DATE_FORMAT(CURRENT_DATE,'%y')) THEN
    SET new.numero = CONCAT(DATE_FORMAT(CURRENT_DATE,'%y'),'000001');
  END IF;                      
  SET new.situacao = 'APG';
END
$$
CREATE TRIGGER `buVistoriaPlanos` BEFORE UPDATE ON `gear_vistoria_planos` FOR EACH ROW BEGIN
  -- Se alterar a data de inicio, a frequencia, a quantidade altera a situacao para "A programar"
  IF (old.inicio <> new.inicio) OR (old.frequencia <> new.frequencia) OR 
      (old.quantidade <> new.quantidade) OR (old.periodo <> new.periodo) THEN
    SET new.situacao = 'APG';
  END IF;
END
$$
DELIMITER ;

--
-- Estrutura da tabela `gear_vistoria_agendamentos`
--

DROP TABLE IF EXISTS `gear_vistoria_agendamento`;
CREATE TABLE IF NOT EXISTS `gear_vistoria_agendamentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `idPlano` bigint UNSIGNED NOT NULL,
  `numero` varchar(8) NOT NULL DEFAULT '0000000',
  `inicio` datetime NOT NULL,
  `final` datetime NOT NULL,
  `periodo` varchar(1) NOT NULL,
  `execucao` datetime,
  `idUsuario` bigint UNSIGNED,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vistoria_agendamentos_numero` (`idAeroporto`,`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `gear_vistoria_agendamentos`
--

DROP TRIGGER IF EXISTS `biVistoriaAgendamentos`;
DELIMITER $$
CREATE TRIGGER `biVistoriaAgendamentos` BEFORE INSERT ON `gear_vistoria_agendamentos` FOR EACH ROW BEGIN
  -- Gera o número do plano por ano 
  SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,8,'0')
                      FROM gear_vistoria_agendamentos 
                      WHERE idAeroporto = new.idAeroporto), '00000001'));
  IF (LEFT(new.numero,2) <> DATE_FORMAT(CURRENT_DATE,'%y')) THEN
    SET new.numero = CONCAT(DATE_FORMAT(CURRENT_DATE,'%y'),'000001');
  END IF;    
END
$$
DELIMITER ;

--
-- Estrutura da tabela `gear_vistoria_resultados`
--

DROP TABLE IF EXISTS `gear_vistoria_resultados`;
CREATE TABLE IF NOT EXISTS `gear_vistoria_resultados` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAgendamento` bigint UNSIGNED NOT NULL,
  `idPlano` bigint UNSIGNED NOT NULL,
  `idItem` bigint UNSIGNED NOT NULL,
  `local` varchar(250) NULL DEFAULT '',
  `parecer` varchar(250) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vistoria_resultados_agendamento` (`idAgendamento`,`idPlano`,`idItem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `gear_vistoria_agendamentos_itens`
--

DROP TABLE IF EXISTS `gear_vistoria_agendamentos_itens`;
CREATE TABLE IF NOT EXISTS `gear_vistoria_agendamentos_itens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAgendamento` bigint UNSIGNED NOT NULL,
  `idPlano` bigint UNSIGNED NOT NULL,
  `idItem` bigint UNSIGNED NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vistoria_agendamentos_itens` (`idAgendamento`,`idPlano`,`idItem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `gear_notificacoes`
--

DROP TABLE IF EXISTS `gear_notificacoes`;
CREATE TABLE IF NOT EXISTS `gear_notificacoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint UNSIGNED NOT NULL,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `sistema` varchar(4) NOT NULL,
  `notificacao` varchar(500) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'NLD',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ********************************************************
--
-- SISTEMA DE RESERVAS
--
-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_reservas`
--

DROP TABLE IF EXISTS `gear_reservas`;
CREATE TABLE IF NOT EXISTS `gear_reservas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint UNSIGNED NOT NULL,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `ano` varchar(4) NOT NULL DEFAULT '0000',
  `mes` varchar(2) NOT NULL DEFAULT '00',
  `numero` varchar(4) NOT NULL DEFAULT '0000',
  `matricula` varchar(10) NOT NULL,
  `origem` varchar(4) NOT NULL,
  `chegada` datetime NOT NULL,
  `pob` int NOT NULL DEFAULT '0',
  `destino` varchar(4) DEFAULT NULL,
  `partida` datetime DEFAULT NULL,
  `fonte` varchar(4) NOT NULL DEFAULT 'GEAR',
  `observacao` varchar(500) NOT NULL DEFAULT '',
  `enviar` varchar(3) NOT NULL DEFAULT 'NAO',
  `envio` datetime DEFAULT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'PEN',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_reservas`
--

INSERT INTO `gear_reservas` (`idUsuario`, `idAeroporto`, `matricula`, `origem`, `chegada`, `destino`, `partida`, `fonte`, `situacao`, `observacao`) VALUES
(2, 5, 'PPAAJ', 'SBSP', '2025-07-15 23:00:00', '', NULL, 'WAPP', 'PEN', ''),
(2, 5, 'PPAAJ', 'SBRJ', '2025-07-15 22:00:00', 'SBSP', '2025-07-15 23:00:00', 'WAPP', 'PEN', ''),
(2, 5, 'PPAAJ', 'SBRJ', '2025-07-15 22:30:00', '', NULL, 'WAPP', 'PEN', ''),
(3, 5, 'PTTAX', 'SBBR', '2025-07-15 19:00:00', 'SBSP', '2025-07-16 08:00:00', 'WAPP', 'PEN', ''),
(3, 5, 'PSEMB', 'SBGO', '2025-07-16 10:00:00', 'SBPF', '2025-07-16 20:00:00', 'WAPP', 'PEN', '');
COMMIT;

--
-- Acionadores `gear_reservas`
--
DROP TRIGGER IF EXISTS `biReservas`;
DELIMITER $$
CREATE TRIGGER `biReservas` BEFORE INSERT ON `gear_reservas` FOR EACH ROW BEGIN
  -- Gera o número da reserva
  SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,4,'0')
                			FROM gear_reservas 
					            WHERE idAeroporto = new.idAeroporto 
                        AND ano = DATE_FORMAT(new.cadastro, '%Y')
                        AND mes = DATE_FORMAT(new.cadastro, '%m')
                	    GROUP BY ano, mes), '0001'));
  SET new.ano = DATE_FORMAT(new.cadastro, '%Y');
  SET new.mes = DATE_FORMAT(new.cadastro, '%m');
END
$$
DELIMITER ;

--
-- Estrutura da tabela `gear_reservas_historico`
--

DROP TABLE IF EXISTS `gear_reservas_historicos`;
CREATE TABLE IF NOT EXISTS `gear_reservas_historicos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idReserva` bigint UNSIGNED NOT NULL,
  `observacao` varchar(500) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'PEN',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `gear_reservas_usuarios`
--

DROP TABLE IF EXISTS `gear_reservas_usuarios`;
CREATE TABLE IF NOT EXISTS `gear_reservas_usuarios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fonte` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'GEAR',
  `situacao` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_reservas_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ********************************************************
--
-- SISTEMA DE MONITORES
--
-- --------------------------------------------------------

--
-- Estrutura da tabela `gear_monitores`
--

DROP TABLE IF EXISTS `gear_monitores`;
CREATE TABLE IF NOT EXISTS `gear_monitores` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAeroporto` bigint UNSIGNED NOT NULL,
  `numero` varchar(3) NOT NULL DEFAULT '000',
  `localizacao` varchar(100) NOT NULL DEFAULT '',
  `hash` varchar(250) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gear_monitores`
--

INSERT INTO `gear_monitores` (`idAeroporto`, `numero`, `localizacao`, `situacao`, `cadastro`) VALUES
(5, '001', 'TPS', 'ATV', UTC_TIMESTAMP()),
(5, '002', 'TPS', 'ATV', UTC_TIMESTAMP()),
(5, '003', 'TPS', 'ATV', UTC_TIMESTAMP()),
(5, '004', 'TPS', 'ATV', UTC_TIMESTAMP()),
(5, '005', 'TPS', 'ATV', UTC_TIMESTAMP());
COMMIT;

--
-- Acionadores `gear_monitores`
--
DROP TRIGGER IF EXISTS `biMonitores`;
DELIMITER $$
CREATE TRIGGER `biMonitores` BEFORE INSERT ON `gear_monitores` FOR EACH ROW BEGIN
  -- Gera o número do monitor
  SET new.numero = (SELECT IFNULL((SELECT LPAD(MAX(numero)+1,3,'0')
                			FROM gear_monitores 
					            WHERE idAeroporto = new.idAeroporto 
                	    GROUP BY idAeroporto), '001'));
END
$$
DELIMITER ;

--
-- Estrutura da tabela `gear_monitores_paginas`
--

DROP TABLE IF EXISTS `gear_monitores_paginas`;
CREATE TABLE IF NOT EXISTS `gear_monitores_paginas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idMonitor` bigint UNSIGNED NOT NULL,
  `acao` varchar(3) NOT NULL DEFAULT 'EXI',                        
  `pagina` varchar(100) NOT NULL DEFAULT 'MAER.html',
  `segundos` int UNSIGNED NOT NULL DEFAULT '30',
  `resolucao` varchar(9) NOT NULL DEFAULT '1440x900',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ********************************************************