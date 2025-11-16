-- ********************************************************
--
-- RESTRICOES
--
-- ********************************************************



-- ********************************************************

--
-- Limitadores para a tabela `planta_acessos`
--
ALTER TABLE `planta_acessos`
    ADD CONSTRAINT `fk_acessos_aeroportos` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_acessos_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_clientes` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_acessos_usuarios` FOREIGN KEY (`idUsuario`) REFERENCES `planta_usuarios` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `planta_clientes`
--
ALTER TABLE `planta_clientes`
    ADD CONSTRAINT `fk_clientes_aeroportos` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT,

--
-- Limitadores para a tabela `planta_faturamentos`
--
ALTER TABLE `planta_faturamentos`
    ADD CONSTRAINT `fk_faturamentos_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `planta_matriculas`
--
ALTER TABLE `planta_matriculas`
    ADD CONSTRAINT `fk_matriculas_equipamentos` FOREIGN KEY (`idEquipamento`) REFERENCES `planta_equipamentos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_matriculas_operadores` FOREIGN KEY (`idOperador`) REFERENCES `planta_operadores` (`id`) ON DELETE RESTRICT;

-- 
-- Limitadores para a tabela `planta_status_movimentos`
--
ALTER TABLE `planta_status_movimentos`
    ADD CONSTRAINT `fk_status_movimentos_recurso` FOREIGN KEY (`idRecurso`) REFERENCES `planta_recursos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_status_movimentos_status` FOREIGN KEY (`idStatus`) REFERENCES `planta_status` (`id`) ON DELETE RESTRICT;

-- 
-- Limitadores para a tabela `planta_voos_movimentos`
--
ALTER TABLE `planta_voos_movimentos`
    ADD CONSTRAINT `fk_voos_movimentos_recurso` FOREIGN KEY (`idRecurso`) REFERENCES `planta_recursos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_voos_movimentos_voos` FOREIGN KEY (`idVoo`) REFERENCES `planta_voos_operacionais` (`id`) ON DELETE CASCADE;  

--
-- Limitadores para a tabela `planta_calculos`
--
ALTER TABLE `planta_calculos`
    ADD CONSTRAINT `fk_calculos_faturamentos` FOREIGN KEY (`idFaturamento`) REFERENCES `planta_faturamentos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `planta_recursos`
--
ALTER TABLE `planta_recursos`
    ADD CONSTRAINT `fk_recursos_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `planta_status`
--
ALTER TABLE `planta_status`
    ADD CONSTRAINT `fk_status_aeroportos_destino` FOREIGN KEY (`idDestino`) REFERENCES `planta_aeroportos` (`id`),
    ADD CONSTRAINT `fk_status_aeroportos_origem` FOREIGN KEY (`idOrigem`) REFERENCES `planta_aeroportos` (`id`),
    ADD CONSTRAINT `fk_status_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`),
    ADD CONSTRAINT `fk_status_matricula` FOREIGN KEY (`idMatricula`) REFERENCES `planta_matriculas` (`id`),
    ADD CONSTRAINT `fk_status_voo_chegada` FOREIGN KEY (`idChegada`) REFERENCES `planta_voos_operacionais` (`id`),
    ADD CONSTRAINT `fk_status_voo_partida` FOREIGN KEY (`idPartida`) REFERENCES `planta_voos_operacionais` (`id`);
COMMIT;

-- OK
-- Limitadores para a tabela `planta_status_complementos`
--
ALTER TABLE `planta_status_complementos`
    ADD CONSTRAINT `fk_status_complementos_status` FOREIGN KEY (`idStatus`) REFERENCES `planta_status` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `planta_vistoria_itens`
--
ALTER TABLE `planta_vistoria_itens`
    ADD CONSTRAINT `fk_vistoria_itens_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `planta_vistoria_planos`
--
ALTER TABLE `planta_vistoria_planos`
    ADD CONSTRAINT `fk_vistoria_planos_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `planta_vistoria_planos_itens`
--
ALTER TABLE `planta_vistoria_planos_itens`
    ADD CONSTRAINT `fk_vistoria_planos_itens_plano` FOREIGN KEY (`idPlano`) REFERENCES `planta_vistoria_planos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_vistoria_planos_itens_item` FOREIGN KEY (`idItem`) REFERENCES `planta_vistoria_itens` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `planta_vistoria_agendamentos`
--
ALTER TABLE `planta_vistoria_agendamentos`
    ADD CONSTRAINT `fk_vistoria_agendamentos_clientes` FOREIGN KEY (`idSite`) REFERENCES `planta_clientes` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_vistoria_agendamentos_plano` FOREIGN KEY (`idPlano`) REFERENCES `planta_vistoria_planos` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `planta_vistoria_resultados`
--
ALTER TABLE `planta_vistoria_resultados`
    ADD CONSTRAINT `fk_resultados_agendamentos` FOREIGN KEY (`idAgendamento`) REFERENCES `planta_vistoria_agendamentos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_resultados_planos` FOREIGN KEY (`idPlano`) REFERENCES `planta_vistoria_planos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_resultados_itens` FOREIGN KEY (`idItem`) REFERENCES `planta_vistoria_itens` (`id`) ON DELETE RESTRICT;
    
ALTER TABLE `planta_empresas`
    ADD CONSTRAINT `fk_empresas_aeroportos` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;

ALTER TABLE `planta_veiculos_credenciados`
    ADD CONSTRAINT `fk_veiculos_credenciados_empresas` FOREIGN KEY (`idEmpresa`) REFERENCES `planta_empresas` (`id`) ON DELETE RESTRICT;

ALTER TABLE `planta_pessoas_credenciadas`
    ADD CONSTRAINT `fk_pessoas_credenciadas_empresas` FOREIGN KEY (`idEmpresa`) REFERENCES `planta_empresas` (`id`) ON DELETE RESTRICT;

-- OK
ALTER TABLE `planta_status_complementos`
    ADD CONSTRAINT `fk_status_complementos_status` FOREIGN KEY (`idStatus`) REFERENCES `planta_status` (`id`) ON DELETE CASCADE;

-- OK
ALTER TABLE `planta_status_complementos`
    ADD CONSTRAINT `fk_status_complementos_comandante` FOREIGN KEY (`idComandante`) REFERENCES `planta_comandantes` (`id`) ON DELETE RESTRICT;
  
ALTER TABLE `planta_operadores`
    ADD CONSTRAINT `fk_operadores_cobranca` FOREIGN KEY (`idCobranca`) REFERENCES `planta_operadores_cobranca` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `planta_reservas`
--
ALTER TABLE `planta_reservas`
    ADD CONSTRAINT `fk_reservas_aeroportos` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;
    ADD CONSTRAINT `fk_reservas_usuarios` FOREIGN KEY (`idUsuario`) REFERENCES `planta_reservas_usuarios` (`id`) ON DELETE RESTRICT;

-- OK
ALTER TABLE `planta_reservas_historicos`
    ADD CONSTRAINT `fk_reservas_historicos` FOREIGN KEY (`idReserva`) REFERENCES `planta_reservas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `planta_monitores`
--
ALTER TABLE `planta_monitores`
    ADD CONSTRAINT `fk_monitores_aeroportos` FOREIGN KEY (`idSite`) REFERENCES `planta_aeroportos` (`id`) ON DELETE RESTRICT;

-- OK
ALTER TABLE `planta_monitores_paginas`
    ADD CONSTRAINT `fk_monitores_paginas` FOREIGN KEY (`idMonitor`) REFERENCES `planta_monitor` (`id`) ON DELETE CASCADE;    

-- ****************************************************************