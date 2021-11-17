alter table eventos
add column limite_avaliadores_trabalhos int(11) NOT NULL DEFAULT '2';

alter table trabalhos
add column nota int(11) NOT NULL DEFAULT '0',
CHANGE `apresentado` `apresentado` TINYINT(4) NOT NULL DEFAULT '0';


ALTER TABLE `usuarios` 
CHANGE `certificado_revisor` `certificado_avaliador` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
CHANGE `certificado_revisor_data` `certificado_avaliador_data` TIMESTAMP NULL DEFAULT NULL,
CHANGE `aprovado_certificado_revisor` `aprovado_certificado_avaliador` TINYINT(1) NOT NULL DEFAULT '0';
