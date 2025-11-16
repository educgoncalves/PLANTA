-- ****************************************************************************
--
--  VIEWS
--
-- ****************************************************************************

DROP VIEW IF EXISTS `planta_status_primeiro_movimento`;
DROP VIEW IF EXISTS `planta_status_ultimo_movimento`;
DROP VIEW IF EXISTS `planta_status_primeira_entrada`;
DROP VIEW IF EXISTS `planta_status_ultima_entrada`;
DROP VIEW IF EXISTS `planta_voos_primeiro_movimento`;
DROP VIEW IF EXISTS `planta_voos_ultimo_movimento`;
DROP VIEW IF EXISTS `planta_voos_ultimo_confirmado`;
DROP VIEW IF EXISTS `planta_voos_horario_confirmado`;

-- ****************************************************************************

-- 
-- Estrutura para vista `planta_status_primeiro_movimento`
--

DROP VIEW IF EXISTS `planta_status_primeiro_movimento`;
CREATE VIEW `planta_status_primeiro_movimento`  AS 
  SELECT sm.id, sm.idStatus, sm.dhMovimento, sm.idMovimento, sm.movimento, sm.idRecurso, sm.idSegundoRecurso, sm.usuario, sm.cadastro
  FROM planta_status_movimentos sm
  INNER JOIN (SELECT min(id) AS id FROM planta_status_movimentos GROUP BY idStatus) pm
    ON pm.id = sm.id;

-- 
-- Estrutura para vista `planta_status_ultimo_movimento`
--

DROP VIEW IF EXISTS `planta_status_ultimo_movimento`;
CREATE VIEW `planta_status_ultimo_movimento`  AS 
  SELECT sm.id, sm.idStatus, sm.dhMovimento, sm.idMovimento, sm.movimento, sm.idRecurso, sm.idSegundoRecurso, sm.usuario, sm.cadastro
  FROM planta_status_movimentos sm
  INNER JOIN (SELECT max(id) AS id FROM planta_status_movimentos GROUP BY idStatus) um
    ON um.id = sm.id;

-- 
-- Estrutura para vista `planta_status_primeira_entrada'`
--

DROP VIEW IF EXISTS `planta_status_primeira_entrada`;
CREATE VIEW `planta_status_primeira_entrada`  AS 
  SELECT sm.id, sm.idStatus, sm.dhMovimento, sm.idMovimento, sm.movimento, sm.idRecurso, sm.idSegundoRecurso, sm.usuario, sm.cadastro
  FROM planta_status_movimentos sm
  INNER JOIN (SELECT min(id) AS id FROM planta_status_movimentos WHERE movimento = 'ENT' GROUP BY idStatus) pe
    ON pe.id = sm.id;

-- 
-- Estrutura para vista `planta_status_ultima_entrada'`
--

DROP VIEW IF EXISTS `planta_status_ultima_entrada`;
CREATE VIEW `planta_status_ultima_entrada`  AS 
  SELECT sm.id, sm.idStatus, sm.dhMovimento, sm.idMovimento, sm.movimento, sm.idRecurso, sm.idSegundoRecurso, sm.usuario, sm.cadastro
  FROM planta_status_movimentos sm
  INNER JOIN (SELECT max(id) AS id FROM planta_status_movimentos WHERE movimento = 'ENT' GROUP BY idStatus) ue
    ON ue.id = sm.id;

-- 
-- Estrutura para vista `planta_voos_primeiro_movimento`
--

DROP VIEW IF EXISTS `planta_voos_primeiro_movimento`;
CREATE VIEW `planta_voos_primeiro_movimento`  AS 
  SELECT vm.id, vm.idVoo, vm.dhMovimento, vm.movimento, vm.idRecurso, vm.cadastro
  FROM planta_voos_movimentos vm
  INNER JOIN (SELECT min(id) AS id FROM planta_voos_movimentos GROUP BY idVoo) pm
    ON pm.id = vm.id;

-- 
-- Estrutura para vista `planta_voos_ultimo_movimento`
--

DROP VIEW IF EXISTS `planta_voos_ultimo_movimento`;
CREATE VIEW `planta_voos_ultimo_movimento`  AS 
  SELECT vm.id, vm.idVoo, vm.dhMovimento, vm.movimento, vm.idRecurso, vm.cadastro
  FROM planta_voos_movimentos vm
  INNER JOIN (SELECT max(id) AS id FROM planta_voos_movimentos GROUP BY idVoo) um
    ON um.id = vm.id;

-- 
-- Estrutura para vista `planta_voos_ultimo_confirmado`
--

DROP VIEW IF EXISTS `planta_voos_ultimo_confirmado`;
CREATE VIEW `planta_voos_ultimo_confirmado`  AS 
  SELECT vm.id, vm.idVoo, vm.dhMovimento, vm.movimento, vm.idRecurso, vm.cadastro
  FROM planta_voos_movimentos vm
  INNER JOIN (SELECT max(id) AS id FROM planta_voos_movimentos WHERE movimento = 'CNF' GROUP BY idVoo) uc 
    ON uc.id = vm.id;

-- 
-- Estrutura para vista `planta_voos_horario_confirmado`
--

DROP VIEW IF EXISTS `planta_voos_horario_confirmado`;
CREATE VIEW `planta_voos_horario_confirmado`  AS 
  SELECT vo.id as idVoo, (CASE WHEN uc.dhMovimento IS NULL THEN vo.dhPrevista ELSE uc.dhMovimento END) as dhConfirmada
  FROM planta_voos_operacionais vo
  LEFT JOIN planta_voos_ultimo_confirmado uc ON uc.idVoo = vo.id;

-- ****************************************************************************