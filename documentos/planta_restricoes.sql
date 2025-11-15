-- ********************************************************
--
-- RESTRICOES
--
-- ********************************************************



-- ********************************************************

--
-- Limitadores para a tabela `gear_acessos`
--
ALTER TABLE `gear_acessos`
    ADD CONSTRAINT `fk_acessos_aeroportos` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_acessos_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_clientes` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_acessos_usuarios` FOREIGN KEY (`idUsuario`) REFERENCES `gear_usuarios` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `gear_clientes`
--
ALTER TABLE `gear_clientes`
    ADD CONSTRAINT `fk_clientes_aeroportos` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT,

--
-- Limitadores para a tabela `gear_faturamentos`
--
ALTER TABLE `gear_faturamentos`
    ADD CONSTRAINT `fk_faturamentos_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `gear_matriculas`
--
ALTER TABLE `gear_matriculas`
    ADD CONSTRAINT `fk_matriculas_equipamentos` FOREIGN KEY (`idEquipamento`) REFERENCES `gear_equipamentos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_matriculas_operadores` FOREIGN KEY (`idOperador`) REFERENCES `gear_operadores` (`id`) ON DELETE RESTRICT;

-- 
-- Limitadores para a tabela `gear_status_movimentos`
--
ALTER TABLE `gear_status_movimentos`
    ADD CONSTRAINT `fk_status_movimentos_recurso` FOREIGN KEY (`idRecurso`) REFERENCES `gear_recursos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_status_movimentos_status` FOREIGN KEY (`idStatus`) REFERENCES `gear_status` (`id`) ON DELETE RESTRICT;

-- 
-- Limitadores para a tabela `gear_voos_movimentos`
--
ALTER TABLE `gear_voos_movimentos`
    ADD CONSTRAINT `fk_voos_movimentos_recurso` FOREIGN KEY (`idRecurso`) REFERENCES `gear_recursos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_voos_movimentos_voos` FOREIGN KEY (`idVoo`) REFERENCES `gear_voos_operacionais` (`id`) ON DELETE CASCADE;  

--
-- Limitadores para a tabela `gear_calculos`
--
ALTER TABLE `gear_calculos`
    ADD CONSTRAINT `fk_calculos_faturamentos` FOREIGN KEY (`idFaturamento`) REFERENCES `gear_faturamentos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `gear_recursos`
--
ALTER TABLE `gear_recursos`
    ADD CONSTRAINT `fk_recursos_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `gear_status`
--
ALTER TABLE `gear_status`
    ADD CONSTRAINT `fk_status_aeroportos_destino` FOREIGN KEY (`idDestino`) REFERENCES `gear_aeroportos` (`id`),
    ADD CONSTRAINT `fk_status_aeroportos_origem` FOREIGN KEY (`idOrigem`) REFERENCES `gear_aeroportos` (`id`),
    ADD CONSTRAINT `fk_status_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`),
    ADD CONSTRAINT `fk_status_matricula` FOREIGN KEY (`idMatricula`) REFERENCES `gear_matriculas` (`id`),
    ADD CONSTRAINT `fk_status_voo_chegada` FOREIGN KEY (`idChegada`) REFERENCES `gear_voos_operacionais` (`id`),
    ADD CONSTRAINT `fk_status_voo_partida` FOREIGN KEY (`idPartida`) REFERENCES `gear_voos_operacionais` (`id`);
COMMIT;

-- OK
-- Limitadores para a tabela `gear_status_complementos`
--
ALTER TABLE `gear_status_complementos`
    ADD CONSTRAINT `fk_status_complementos_status` FOREIGN KEY (`idStatus`) REFERENCES `gear_status` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `gear_vistoria_itens`
--
ALTER TABLE `gear_vistoria_itens`
    ADD CONSTRAINT `fk_vistoria_itens_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `gear_vistoria_planos`
--
ALTER TABLE `gear_vistoria_planos`
    ADD CONSTRAINT `fk_vistoria_planos_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `gear_vistoria_planos_itens`
--
ALTER TABLE `gear_vistoria_planos_itens`
    ADD CONSTRAINT `fk_vistoria_planos_itens_plano` FOREIGN KEY (`idPlano`) REFERENCES `gear_vistoria_planos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_vistoria_planos_itens_item` FOREIGN KEY (`idItem`) REFERENCES `gear_vistoria_itens` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `gear_vistoria_agendamentos`
--
ALTER TABLE `gear_vistoria_agendamentos`
    ADD CONSTRAINT `fk_vistoria_agendamentos_clientes` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_clientes` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_vistoria_agendamentos_plano` FOREIGN KEY (`idPlano`) REFERENCES `gear_vistoria_planos` (`id`) ON DELETE RESTRICT;

-- OK
-- Limitadores para a tabela `gear_vistoria_resultados`
--
ALTER TABLE `gear_vistoria_resultados`
    ADD CONSTRAINT `fk_resultados_agendamentos` FOREIGN KEY (`idAgendamento`) REFERENCES `gear_vistoria_agendamentos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_resultados_planos` FOREIGN KEY (`idPlano`) REFERENCES `gear_vistoria_planos` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_resultados_itens` FOREIGN KEY (`idItem`) REFERENCES `gear_vistoria_itens` (`id`) ON DELETE RESTRICT;
    
ALTER TABLE `gear_empresas`
    ADD CONSTRAINT `fk_empresas_aeroportos` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;

ALTER TABLE `gear_veiculos_credenciados`
    ADD CONSTRAINT `fk_veiculos_credenciados_empresas` FOREIGN KEY (`idEmpresa`) REFERENCES `gear_empresas` (`id`) ON DELETE RESTRICT;

ALTER TABLE `gear_pessoas_credenciadas`
    ADD CONSTRAINT `fk_pessoas_credenciadas_empresas` FOREIGN KEY (`idEmpresa`) REFERENCES `gear_empresas` (`id`) ON DELETE RESTRICT;

-- OK
ALTER TABLE `gear_status_complementos`
    ADD CONSTRAINT `fk_status_complementos_status` FOREIGN KEY (`idStatus`) REFERENCES `gear_status` (`id`) ON DELETE CASCADE;

-- OK
ALTER TABLE `gear_status_complementos`
    ADD CONSTRAINT `fk_status_complementos_comandante` FOREIGN KEY (`idComandante`) REFERENCES `gear_comandantes` (`id`) ON DELETE RESTRICT;
  
ALTER TABLE `gear_operadores`
    ADD CONSTRAINT `fk_operadores_cobranca` FOREIGN KEY (`idCobranca`) REFERENCES `gear_operadores_cobranca` (`id`) ON DELETE RESTRICT;

--
-- Limitadores para a tabela `gear_reservas`
--
ALTER TABLE `gear_reservas`
    ADD CONSTRAINT `fk_reservas_aeroportos` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;
    ADD CONSTRAINT `fk_reservas_usuarios` FOREIGN KEY (`idUsuario`) REFERENCES `gear_reservas_usuarios` (`id`) ON DELETE RESTRICT;

-- OK
ALTER TABLE `gear_reservas_historicos`
    ADD CONSTRAINT `fk_reservas_historicos` FOREIGN KEY (`idReserva`) REFERENCES `gear_reservas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `gear_monitores`
--
ALTER TABLE `gear_monitores`
    ADD CONSTRAINT `fk_monitores_aeroportos` FOREIGN KEY (`idAeroporto`) REFERENCES `gear_aeroportos` (`id`) ON DELETE RESTRICT;

-- OK
ALTER TABLE `gear_monitores_paginas`
    ADD CONSTRAINT `fk_monitores_paginas` FOREIGN KEY (`idMonitor`) REFERENCES `gear_monitor` (`id`) ON DELETE CASCADE;    

-- ****************************************************************