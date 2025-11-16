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
-- Banco de dados: `planta`
--
CREATE DATABASE IF NOT EXISTS `planta` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `planta`;

-- ****************************************************************************************
--
-- LIMPANDO O BANCO
--
-- Tabelas com chaves estrangeiras
--
DROP TABLE IF EXISTS `planta_clientes`;
DROP TABLE IF EXISTS `planta_sites`;
DROP TABLE IF EXISTS `planta_data_centers`;
DROP TABLE IF EXISTS `planta_maquinas`;

DROP TABLE IF EXISTS `planta_acessos`;
DROP TABLE IF EXISTS `planta_restricoes`;
DROP TABLE IF EXISTS `planta_usuarios`;
--
-- Tabelas Serviço
--
DROP TABLE IF EXISTS `planta_menus`;
DROP TABLE IF EXISTS `planta_logs`;
DROP TABLE IF EXISTS `planta_conexoes`;
DROP TABLE IF EXISTS `planta_dominios`;
--
-- Views
--

-- --------------------------------------------------------

-- ********************************************************
-- Estrutura da tabela `planta_usuarios`
--

DROP TABLE IF EXISTS `planta_usuarios`;
CREATE TABLE IF NOT EXISTS `planta_usuarios` (
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
-- Extraindo dados da tabela `planta_usuarios`
--
TRUNCATE TABLE `planta_usuarios`;
INSERT INTO `planta_usuarios` (`id`, `usuario`, `senha`, `nome`, `email`, `situacao`, `cadastro`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrador', 'educgoncalves@gmail.com', 'ATV', UTC_TIMESTAMP()),
(2, 'eduardo', '81f705dc2ce1a61a2621e0e4b442a9474e1d0c70', 'Eduardo Gonçalves', 'educgoncalves@gmail.com', 'ATV', UTC_TIMESTAMP()),
(3, 'gerente', 'e0ffb90b074691c42ebd7b3cc39771b344c0083b', 'Gerente', 'gerente@gmail.com', 'ATV', UTC_TIMESTAMP()),
(4, 'supervisor', '0f4d09e43d208d5e9222322fbc7091ceea1a78c3', 'Supervisor', 'supervisor@gmail.com', 'ATV', UTC_TIMESTAMP()),
(5, 'encarregado', 'ba2bbe6d0f6e66f9eafcc2721eddbb9584758c03', 'Encarregado', 'encarregado@gmail.com', 'ATV', UTC_TIMESTAMP()),
(6, 'fiscal', '7b7e741b68aa05929c7c1f540e6e8799a3059706', 'Fiscal', 'fiscal@gmail.com', 'ATV', UTC_TIMESTAMP()),
(7, 'convidado', 'c3c972c694cfed330ee6429cdcc7e8f7351375c3', 'Convidado', 'convidado@gmail.com', 'ATV', UTC_TIMESTAMP());

--
-- Acionadores `planta_usuarios`
--
DROP TRIGGER IF EXISTS `buUsuarios`;
DELIMITER $$
CREATE TRIGGER `buUsuarios` BEFORE UPDATE ON `planta_usuarios` FOR EACH ROW BEGIN
  IF (new.usuario <> old.usuario) THEN
   	IF (SELECT COUNT(*) FROM planta_acessos ac WHERE ac.idUsuario = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'ACESSOS => Usuário não pode ser alterado';
		END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

-- ********************************************************
-- Estrutura da tabela `planta_acessos`
--

DROP TABLE IF EXISTS `planta_acessos`;
CREATE TABLE IF NOT EXISTS `planta_acessos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint UNSIGNED NOT NULL,
  `idSite` bigint UNSIGNED NOT NULL,
  `sistema` varchar(4) NOT NULL,
  `grupo` varchar(3) NOT NULL DEFAULT 'CVD',
  `preferencial` varchar(3) NOT NULL DEFAULT 'SIM',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_acessos_usuario` (`idUsuario`,`idSite`,`sistema`),
  KEY `idx_acessos_site` (`idSite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `planta_acessos`
--
TRUNCATE TABLE `planta_acessos`;
INSERT INTO `planta_acessos` (`idUsuario`, `idSite`, `sistema`, `grupo`, `preferencial`) VALUES
(1, 0, 'PLNT', 'ADM', 'SIM'),
(2, 0, 'PLNT', 'ADM', 'SIM'),
(3, 0, 'PLNT', 'GER', 'SIM');

-- --------------------------------------------------------

-- ********************************************************
-- Estrutura da tabela `planta_sites`
--

DROP TABLE IF EXISTS `planta_sites`;
CREATE TABLE IF NOT EXISTS `planta_sites` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `site` varchar(4) NOT NULL DEFAULT '',
  `nome` varchar(250) NOT NULL DEFAULT '',
  `localidade` varchar(50) NOT NULL DEFAULT '',
  `pais` varchar(25) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_sites_sigla` (`sigla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `planta_sites`
--
TRUNCATE TABLE `planta_sites`;
INSERT INTO `planta_sites` (`id`, `sigla`, `nome`, `localidade`, `pais`, `cadastro`) VALUES
(1, 'XQUE', 'Xique-Xique', 'Bahia', 'Brasil', UTC_TIMESTAMP());

-- 
-- Acionadores `planta_sites`
--
DROP TRIGGER IF EXISTS `buSites`;
DELIMITER $$
CREATE TRIGGER `buSites` BEFORE UPDATE ON `planta_sites` FOR EACH ROW BEGIN
  IF (new.sigla <> old.sigla) THEN
   	IF (SELECT COUNT(*) FROM planta_acessos ac WHERE ac.idSite = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'ACESSOS => Sigla SITE não pode ser alterada';
		END IF;
   	IF (SELECT COUNT(*) FROM planta_data_centers dc WHERE dc.idSite = new.id) <> 0 THEN
  		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'DATA_CENTERS => Sigla SITE não pode ser alterada';
		END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

-- ********************************************************
-- Estrutura da tabela `planta_clientes`
--

DROP TABLE IF EXISTS `planta_clientes`;
CREATE TABLE IF NOT EXISTS `planta_clientes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idSite` bigint UNSIGNED NOT NULL,
  `sistema` varchar(10) NOT NULL,
  `cliente` varchar(10) NOT NULL,
  `conexoes` int UNSIGNED NOT NULL DEFAULT '1',
  `debug` varchar(3) NOT NULL DEFAULT 'NAO',
  `regPorPagina` int UNSIGNED NOT NULL DEFAULT '10',
  `tmpRefreshPagina` int UNSIGNED NOT NULL DEFAULT '90',
  `tmpRefreshTela` int UNSIGNED NOT NULL DEFAULT '60',
  `utc` int NOT NULL DEFAULT '-3',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `planta_conexoes`
--

DROP TABLE IF EXISTS `planta_conexoes`;
CREATE TABLE IF NOT EXISTS `planta_conexoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idSite` bigint UNSIGNED NOT NULL,
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
-- Estrutura da tabela `planta_dominios`
--

DROP TABLE IF EXISTS `planta_dominios`;
CREATE TABLE IF NOT EXISTS `planta_dominios` (
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
-- Extraindo dados da tabela `planta_dominios`
--
TRUNCATE TABLE `planta_dominios`;
INSERT INTO `planta_dominios` (`tabela`, `coluna`, `codigo`, `descricao`, `ordenacao`) VALUES
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

('planta_notificacoes', 'situacao', 'LDS', 'Lidas', 0),
('planta_notificacoes', 'situacao', 'NLD', 'Não lidas', 1),

('planta_todos', 'sistema', 'PLNT', 'Gerenciamento de Plantas de Produção', 0),

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
('planta_todos', 'dia', '7', 'Sábado', 7);

-- Inserindo dominios tabelas do sistema
--
INSERT INTO `planta_dominios` (`tabela`, `coluna`, `codigo`, `descricao`, `ordenacao`)
 SELECT 'planta_logs', 'tabela', TABLE_NAME, 
        (CASE WHEN TABLE_COMMENT <> "" THEN TABLE_COMMENT ELSE TABLE_NAME END), 0 
  FROM information_schema.tables WHERE UCASE(table_schema) = 'PLANTA' AND TABLE_TYPE = 'BASE TABLE';

-- --------------------------------------------------------

--
-- Estrutura da tabela `planta_empresas`
--

DROP TABLE IF EXISTS `planta_empresas`;
CREATE TABLE IF NOT EXISTS `planta_empresas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idSite` bigint UNSIGNED NOT NULL,
  `empresa` varchar(25) NOT NULL,
  `atividade` varchar(25) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `bairro` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_empresas_empresa` (`idSite`,`empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `planta_logs`
--

DROP TABLE IF EXISTS `planta_logs`;
CREATE TABLE IF NOT EXISTS `planta_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabela` varchar(25) NOT NULL,
  `operacao` varchar(15) NOT NULL,
  `site` varchar(4) NOT NULL,
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
-- Estrutura da tabela `planta_menus`
--
DROP TABLE IF EXISTS `planta_menus`;
CREATE TABLE IF NOT EXISTS `planta_menus` (
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
-- Extraindo dados da tabela `planta_menus`
--
TRUNCATE TABLE `planta_menus`;
INSERT INTO `planta_menus` (`id`, `sistema`, `tipo`, `formulario`, `modulo`, `descricao`, `href`, `target`, `iconeSVG`, `ordem`, `atalho`, `cadastro`) VALUES
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
-- Estrutura da tabela `planta_tarefas`
--

DROP TABLE IF EXISTS `planta_tarefas`;
CREATE TABLE IF NOT EXISTS `planta_tarefas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(4) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `tmpTolerancia` int UNSIGNED NOT NULL DEFAULT '180',
  `email` varchar(3) NOT NULL DEFAULT 'SIM',
  `dhExecucao` datetime NULL DEFAULT NULL,
  `modo` varchar(3) NOT NULL DEFAULT 'MNL',
  `situacao` varchar(3) NOT NULL DEFAULT 'ATV',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tarefas_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Extraindo dados da tabela `planta_tarefas`
--
TRUNCATE TABLE `planta_tarefas`;
INSERT INTO `planta_tarefas` (`id`, `codigo`, `descricao`, `email`, `dhExecucao`, `modo`, `situacao`, `cadastro`) VALUES
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
-- Estrutura da tabela `planta_restricoes`
--

DROP TABLE IF EXISTS `planta_restricoes`;
CREATE TABLE IF NOT EXISTS `planta_restricoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idSite` bigint UNSIGNED NOT NULL,
  `sistema` varchar(4) NOT NULL,
  `formulario` varchar(4) NOT NULL,
  `grupo` varchar(3) NOT NULL,
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_restricoes_aeroporto` (`idSite`,`sistema`,`formulario`,`grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `planta_notificacoes`
--

DROP TABLE IF EXISTS `planta_notificacoes`;
CREATE TABLE IF NOT EXISTS `planta_notificacoes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUsuario` bigint UNSIGNED NOT NULL,
  `idSite` bigint UNSIGNED NOT NULL,
  `sistema` varchar(4) NOT NULL,
  `notificacao` varchar(500) NOT NULL DEFAULT '',
  `situacao` varchar(3) NOT NULL DEFAULT 'NLD',
  `cadastro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ********************************************************