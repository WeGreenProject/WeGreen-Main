-- BACKUP WEGREEN
SET GLOBAL event_scheduler = ON;
SET FOREIGN_KEY_CHECKS = 0;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: wegreen
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carrinho_itens`
--

DROP TABLE IF EXISTS `carrinho_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrinho_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilizador_id` varchar(50) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1 CHECK (`quantidade` > 0),
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`utilizador_id`,`produto_id`),
  KEY `produto_id` (`produto_id`),
  KEY `idx_utilizador_id` (`utilizador_id`),
  CONSTRAINT `carrinho_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrinho_itens`
--

LOCK TABLES `carrinho_itens` WRITE;
/*!40000 ALTER TABLE `carrinho_itens` DISABLE KEYS */;
INSERT INTO `carrinho_itens` VALUES (1,'1',1,1,'2025-12-06 19:18:54','2025-12-06 19:18:54'),(5,'1',5,1,'2025-12-06 19:18:54','2025-12-06 19:18:54'),(6,'1',6,1,'2025-12-06 19:18:54','2025-12-06 19:18:54'),(7,'1',7,2,'2025-12-06 19:18:54','2025-12-06 19:18:54'),(8,'2',3,1,'2025-12-16 20:55:36','2025-12-16 20:55:36'),(9,'3',1,2,'2025-12-29 23:13:04','2026-01-01 23:45:06'),(18,'3',9,1,'2025-12-30 17:58:24','2025-12-30 23:52:44'),(19,'3',3,1,'2025-12-30 23:41:40','2025-12-30 23:52:44'),(20,'temp_69546823d71d2',1,1,'2025-12-31 00:02:53','2025-12-31 00:02:53'),(21,'2',1,7,'2026-01-01 00:10:47','2026-01-20 20:45:35'),(27,'2',6,5,'2026-01-07 20:40:33','2026-01-07 20:44:44'),(28,'2',10,6,'2026-01-07 20:40:38','2026-01-07 20:44:44'),(31,'2',26,2,'2026-01-20 20:46:29','2026-01-23 17:37:38'),(33,'temp_6973ff71e892f',25,10,'2026-01-23 23:09:45','2026-01-23 23:09:50');
/*!40000 ALTER TABLE `carrinho_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `denuncias`
--

DROP TABLE IF EXISTS `denuncias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `denuncias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `denunciante_id` int(11) DEFAULT NULL,
  `denunciado_id` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_anexo` varchar(255) DEFAULT NULL,
  `estado` varchar(250) DEFAULT 'Pendente',
  `data_registo` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `denunciante_id` (`denunciante_id`),
  KEY `denunciado_id` (`denunciado_id`),
  CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`denunciante_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `denuncias_ibfk_2` FOREIGN KEY (`denunciado_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `denuncias`
--

LOCK TABLES `denuncias` WRITE;
/*!40000 ALTER TABLE `denuncias` DISABLE KEYS */;
/*!40000 ALTER TABLE `denuncias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devolucoes`
--

DROP TABLE IF EXISTS `devolucoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devolucoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) NOT NULL,
  `codigo_devolucao` varchar(50) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `anunciante_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `valor_reembolso` decimal(10,2) NOT NULL,
  `motivo` enum('defeituoso','tamanho_errado','nao_como_descrito','arrependimento','outro') NOT NULL,
  `motivo_detalhe` text DEFAULT NULL,
  `notas_cliente` text DEFAULT NULL,
  `notas_anunciante` text DEFAULT NULL,
  `estado` enum('solicitada','aprovada','rejeitada','produto_recebido','reembolsada','cancelada') DEFAULT 'solicitada',
  `payment_intent_id` varchar(100) DEFAULT NULL,
  `reembolso_stripe_id` varchar(100) DEFAULT NULL,
  `reembolso_status` varchar(50) DEFAULT NULL COMMENT 'pending, succeeded, failed, canceled',
  `fotos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array de URLs das fotos do produto/defeito' CHECK (json_valid(`fotos`)),
  `codigo_envio_devolucao` varchar(100) DEFAULT NULL,
  `transportadora_devolucao_id` int(11) DEFAULT NULL,
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_aprovacao` timestamp NULL DEFAULT NULL,
  `data_rejeicao` timestamp NULL DEFAULT NULL,
  `data_produto_recebido` timestamp NULL DEFAULT NULL,
  `data_reembolso` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_devolucao` (`codigo_devolucao`),
  KEY `idx_encomenda` (`encomenda_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_anunciante` (`anunciante_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_data_solicitacao` (`data_solicitacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devolucoes`
--

LOCK TABLES `devolucoes` WRITE;
/*!40000 ALTER TABLE `devolucoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `devolucoes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_devolucao_update`
AFTER UPDATE ON `devolucoes`
FOR EACH ROW
BEGIN
  IF OLD.estado != NEW.estado THEN
    INSERT INTO historico_devolucoes (devolucao_id, estado_anterior, estado_novo, alterado_por, observacao)
    VALUES (
      NEW.id,
      OLD.estado,
      NEW.estado,
      'sistema',
      CONCAT('Estado alterado de ', OLD.estado, ' para ', NEW.estado)
    );
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `encomendas`
--

DROP TABLE IF EXISTS `encomendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encomendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_encomenda` varchar(50) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL COMMENT 'ID do pagamento Stripe',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'M?todo de pagamento',
  `payment_status` varchar(50) DEFAULT 'paid' COMMENT 'Status do pagamento',
  `cliente_id` int(11) DEFAULT NULL,
  `anunciante_id` int(11) DEFAULT NULL,
  `transportadora_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `TipoProdutoNome` int(11) DEFAULT NULL,
  `data_envio` date DEFAULT NULL,
  `morada` varchar(250) DEFAULT NULL,
  `tipo_entrega` enum('domicilio','ponto_recolha') DEFAULT 'domicilio',
  `ponto_recolha_id` int(11) DEFAULT NULL,
  `morada_completa` text DEFAULT NULL,
  `nome_destinatario` varchar(255) DEFAULT NULL,
  `estado` varchar(250) DEFAULT NULL,
  `plano_rastreio` varchar(250) DEFAULT NULL,
  `codigo_rastreio` varchar(100) DEFAULT NULL COMMENT 'C?digo de rastreamento da transportadora',
  `nome_ponto_recolha` varchar(255) DEFAULT NULL COMMENT 'Nome do ponto de recolha',
  `morada_ponto_recolha` text DEFAULT NULL COMMENT 'Morada completa do ponto de recolha',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_encomenda` (`codigo_encomenda`),
  KEY `cliente_id` (`cliente_id`),
  KEY `anunciante_id` (`anunciante_id`),
  KEY `produto_id` (`produto_id`),
  KEY `transportadora_id` (`transportadora_id`),
  KEY `TipoProdutoNome` (`TipoProdutoNome`),
  KEY `idx_ponto_recolha` (`ponto_recolha_id`),
  KEY `idx_tipo_entrega` (`tipo_entrega`),
  CONSTRAINT `encomendas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `encomendas_ibfk_2` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `encomendas_ibfk_3` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`),
  CONSTRAINT `encomendas_ibfk_4` FOREIGN KEY (`transportadora_id`) REFERENCES `transportadora` (`id`),
  CONSTRAINT `encomendas_ibfk_5` FOREIGN KEY (`TipoProdutoNome`) REFERENCES `tipo_produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `encomendas`
--

LOCK TABLES `encomendas` WRITE;
/*!40000 ALTER TABLE `encomendas` DISABLE KEYS */;
INSERT INTO `encomendas` VALUES (1,'WG12345',NULL,NULL,'paid',2,3,1,1,1,'2025-12-06','Rua das Flores 15, Lisboa','domicilio',NULL,'Rua das Flores 15, 1200-001 Lisboa',NULL,'Pendente','B?sico',NULL,NULL,NULL),(2,'WG12346',NULL,NULL,'paid',2,3,1,2,1,'2025-12-06','Av. do Mar 20, Porto','domicilio',NULL,NULL,NULL,'Cancelado','B?sico',NULL,NULL,NULL),(3,'WG12347',NULL,NULL,'paid',7,3,2,3,2,'2025-12-06','Rua Central 12, Coimbra','domicilio',NULL,NULL,NULL,'Pendente','B?sico',NULL,NULL,NULL),(4,'WG12352',NULL,NULL,'paid',2,3,1,4,3,'2025-12-06','Rua Nova 8, Braga','domicilio',NULL,NULL,NULL,'Pendente','B?sico',NULL,NULL,NULL),(5,'WG12353',NULL,NULL,'paid',7,3,1,5,1,'2025-12-06','Av. das Oliveiras 30, Set?bal','domicilio',NULL,NULL,NULL,'Entregue','B?sico',NULL,NULL,NULL),(6,'WG12348',NULL,NULL,'paid',2,1,1,6,5,'2025-12-06','Rua Verde 5, Faro','domicilio',NULL,NULL,NULL,'Entregue','Avan?ado',NULL,NULL,NULL),(7,'WG12349',NULL,NULL,'paid',7,1,2,7,4,'2025-12-06','Rua do Sol 40, Lisboa','domicilio',NULL,NULL,NULL,'Pendente','Avan?ado',NULL,NULL,NULL),(8,'WG12354',NULL,NULL,'paid',2,1,2,1,1,'2025-12-06','Rua das Amendoeiras 22, Coimbra','domicilio',NULL,NULL,NULL,'Entregue','Avan?ado',NULL,NULL,NULL),(9,'WG12350',NULL,NULL,'paid',2,4,1,2,1,'2025-12-06','Av. dos Descobrimentos 50, Aveiro','domicilio',NULL,NULL,NULL,'Entregue','B?sico',NULL,NULL,NULL),(10,'WG12351',NULL,NULL,'paid',7,4,2,3,2,'2025-12-06','Rua dos Cedros 18, Leiria','domicilio',NULL,NULL,NULL,'Cancelada','B?sico',NULL,NULL,NULL),(11,'WG12355',NULL,NULL,'paid',7,4,1,4,3,'2025-12-06','Rua da Alegria 7, Viseu','domicilio',NULL,NULL,NULL,'Pendente','B?sico',NULL,NULL,NULL);
/*!40000 ALTER TABLE `encomendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `data_adicao` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito` (`cliente_id`,`produto_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_data` (`data_adicao`),
  KEY `idx_cliente_data` (`cliente_id`,`data_adicao`),
  CONSTRAINT `fk_favorito_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorito_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
INSERT INTO `favoritos` VALUES (1,2,1,'2026-01-19 17:27:28');
/*!40000 ALTER TABLE `favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fornecedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) DEFAULT NULL,
  `descricao` varchar(250) DEFAULT NULL,
  `tipo_produtos_id` int(11) NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `telefone` varchar(250) DEFAULT NULL,
  `morada` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_produtos_id` (`tipo_produtos_id`),
  CONSTRAINT `fornecedores_ibfk_1` FOREIGN KEY (`tipo_produtos_id`) REFERENCES `tipo_produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedores`
--

LOCK TABLES `fornecedores` WRITE;
/*!40000 ALTER TABLE `fornecedores` DISABLE KEYS */;
INSERT INTO `fornecedores` VALUES (1,'ModaCenter','Fornecedor de vestu?rio masculino e feminino',1,'contacto@modacenter.com','912345678','Rua das Flores 12, Lisboa'),(2,'FootWorld','Fornecedor de sapatilhas e cal?ado desportivo',2,'info@footworld.pt','934567890','Av. Central 45, Porto'),(3,'AccessoriArt','Fornecedor de bijuteria e acess?rios de moda',3,'suporte@accessoriart.com','965432187','Rua do Com?rcio 22, Braga'),(4,'BelleCosmetics','Fornecedor de produtos de beleza e skincare',4,'geral@bellecosmetics.pt','938221765','Rua Rosa 9, Coimbra'),(5,'MixSupplies','Fornecedor geral de artigos variados',5,'info@mixsupplies.com','926553421','Av. Nova 88, Faro');
/*!40000 ALTER TABLE `fornecedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gastos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anunciante_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_registo` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `anunciante_id` (`anunciante_id`),
  CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,3,50.00,'Publicidade online','2025-12-06');
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historico_devolucoes`
--

DROP TABLE IF EXISTS `historico_devolucoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historico_devolucoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `devolucao_id` int(11) NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_novo` varchar(50) NOT NULL,
  `observacao` text DEFAULT NULL,
  `alterado_por` varchar(50) DEFAULT NULL COMMENT 'cliente, anunciante, sistema',
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_devolucao` (`devolucao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico_devolucoes`
--

LOCK TABLES `historico_devolucoes` WRITE;
/*!40000 ALTER TABLE `historico_devolucoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `historico_devolucoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historico_produtos`
--

DROP TABLE IF EXISTS `historico_produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historico_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) DEFAULT NULL,
  `estado_encomenda` varchar(250) DEFAULT 'Pendente',
  `descricao` text DEFAULT NULL,
  `data_atualizacao` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `encomenda_id` (`encomenda_id`),
  CONSTRAINT `historico_produtos_ibfk_1` FOREIGN KEY (`encomenda_id`) REFERENCES `encomendas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico_produtos`
--

LOCK TABLES `historico_produtos` WRITE;
/*!40000 ALTER TABLE `historico_produtos` DISABLE KEYS */;
INSERT INTO `historico_produtos` VALUES (1,2,'Entregue','Produto entregue ao cliente.','2025-12-06'),(2,3,'Em tr?nsito','Produto enviado pela transportadora.','2025-12-06'),(3,4,'Entregue','Entrega conclu?da.','2025-12-06'),(4,5,'Pendente','Aguardando envio.','2025-12-06'),(5,6,'Entregue','Venda finalizada com sucesso.','2025-12-06'),(6,7,'Cancelada','Encomenda cancelada pelo cliente.','2025-12-06'),(7,8,'Em tr?nsito','Produto saiu do armaz?m.','2025-12-06'),(8,9,'Entregue','Cliente recebeu o produto.','2025-12-06'),(9,10,'Entregue','Envio conclu?do.','2025-12-06'),(10,11,'Pendente','Aguardando confirma??o de pagamento.','2025-12-06'),(11,1,'Processando','Status alterado para: Processando','2026-01-11'),(12,2,'Cancelado','Status alterado para: Cancelado','2026-01-11'),(13,1,'Pendente','Status alterado para: Pendente','2026-01-11');
/*!40000 ALTER TABLE `historico_produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagens`
--

DROP TABLE IF EXISTS `mensagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remetente_id` int(11) DEFAULT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `remetente_id` (`remetente_id`),
  KEY `destinatario_id` (`destinatario_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `mensagens_ibfk_3` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagens`
--

LOCK TABLES `mensagens` WRITE;
/*!40000 ALTER TABLE `mensagens` DISABLE KEYS */;
INSERT INTO `mensagens` VALUES (1,3,2,NULL,'Ol? Jo?o! O seu pedido j? foi enviado.','2025-12-06 19:18:55','2025-12-06 19:18:55'),(2,1,3,5,'Ol? eu sou a Luiza','2025-12-06 19:18:55','2025-12-06 19:18:55');
/*!40000 ALTER TABLE `mensagens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagensadmin`
--

DROP TABLE IF EXISTS `mensagensadmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensagensadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remetente_id` int(11) DEFAULT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `remetente_id` (`remetente_id`),
  KEY `destinatario_id` (`destinatario_id`),
  CONSTRAINT `mensagensadmin_ibfk_1` FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `mensagensadmin_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagensadmin`
--

LOCK TABLES `mensagensadmin` WRITE;
/*!40000 ALTER TABLE `mensagensadmin` DISABLE KEYS */;
INSERT INTO `mensagensadmin` VALUES (1,2,1,'Ol? Jo?o! O seu pedido j? foi enviado.','2025-12-16 22:56:09','2025-12-16 22:56:09'),(2,3,2,'Olá! Bem-vindo à WeGreen. Como posso ajudar?','2026-01-17 10:00:00','2026-01-18 21:25:54'),(3,2,3,'Olá! Gostaria de saber mais sobre os produtos eco-friendly.','2026-01-17 10:05:00','2026-01-18 21:25:54'),(4,3,2,'Temos uma grande variedade de produtos sustentáveis. Que tipo procura?','2026-01-17 10:10:00','2026-01-18 21:25:54'),(5,2,3,'Estou interessado em roupa de algodão orgânico.','2026-01-17 10:15:00','2026-01-18 21:25:54'),(6,3,2,'Perfeito! Veja nossa coleção de roupa orgânica. Temos peças certificadas.','2026-01-17 10:20:00','2026-01-18 21:25:54'),(7,1,2,'Olá! Em que posso ajudar?','2026-01-17 14:00:00','2026-01-18 21:26:28'),(8,2,1,'Olá! Gostaria de saber sobre o estado da minha encomenda.','2026-01-17 14:05:00','2026-01-18 21:26:28'),(9,1,2,'Claro! Pode fornecer o número da encomenda?','2026-01-17 14:10:00','2026-01-18 21:26:28'),(10,2,1,'Sim, é a encomenda #12345','2026-01-17 14:12:00','2026-01-18 21:26:28'),(11,1,2,'Obrigado! A sua encomenda foi enviada ontem e deverá chegar em 2-3 dias úteis.','2026-01-17 14:15:00','2026-01-18 21:26:28'),(12,2,1,'Perfeito! Muito obrigado pela ajuda! 😊','2026-01-17 14:20:00','2026-01-18 21:26:28'),(13,3,2,'Olá! Vi que adicionou um dos meus produtos aos favoritos. Posso ajudar?','2026-01-18 10:00:00','2026-01-18 21:26:28'),(14,2,3,'Olá Maria! Sim, estou interessado no casaco verde. Tem em tamanho M?','2026-01-18 10:15:00','2026-01-18 21:26:28'),(15,3,2,'Sim, temos em tamanho M! É feito com algodão orgânico certificado. Quer que reserve?','2026-01-18 10:20:00','2026-01-18 21:26:28'),(16,2,3,'Sim, por favor! Quanto fica com o envio?','2026-01-18 10:25:00','2026-01-18 21:26:28'),(17,3,2,'O produto custa 45€ e o envio é grátis em encomendas acima de 30€! 🎉','2026-01-18 10:30:00','2026-01-18 21:26:28'),(29,2,1,'asd','2026-01-18 21:59:39','2026-01-18 21:59:39'),(30,2,1,'ada','2026-01-18 22:49:06','2026-01-18 22:49:06');
/*!40000 ALTER TABLE `mensagensadmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacoes_preferencias`
--

DROP TABLE IF EXISTS `notificacoes_preferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificacoes_preferencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tipo_user` enum('cliente','anunciante') NOT NULL,
  `email_confirmacao` tinyint(1) DEFAULT 1 COMMENT 'Receber email de confirma??o de encomenda',
  `email_processando` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda est? a ser processada',
  `email_enviado` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda ? enviada',
  `email_entregue` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda ? entregue',
  `email_cancelamento` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda ? cancelada',
  `email_novas_encomendas_anunciante` tinyint(1) DEFAULT 1 COMMENT 'Receber email de novas encomendas',
  `email_encomendas_urgentes` tinyint(1) DEFAULT 1 COMMENT 'Receber alertas de encomendas pendentes urgentes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_devolucao_solicitada` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Confirma????o de pedido de devolu????o',
  `email_devolucao_aprovada` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Devolu????o aprovada',
  `email_devolucao_rejeitada` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Devolu????o rejeitada',
  `email_reembolso_processado` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Reembolso processado',
  `email_nova_devolucao_anunciante` tinyint(1) DEFAULT 1 COMMENT 'Anunciante: Nova devolu????o solicitada',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_preferencias` (`user_id`,`tipo_user`),
  CONSTRAINT `notificacoes_preferencias_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Prefer?ncias de notifica??es por email dos utilizadores';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacoes_preferencias`
--

LOCK TABLES `notificacoes_preferencias` WRITE;
/*!40000 ALTER TABLE `notificacoes_preferencias` DISABLE KEYS */;
INSERT INTO `notificacoes_preferencias` VALUES (1,2,'cliente',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(2,7,'cliente',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(4,3,'anunciante',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(5,4,'anunciante',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(6,5,'anunciante',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(7,6,'anunciante',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(8,8,'anunciante',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(11,1,'anunciante',1,1,1,1,1,1,1,'2026-01-10 19:41:25','2026-01-10 19:41:25',1,1,1,1,1),(12,3,'cliente',1,1,1,1,1,1,1,'2026-01-11 16:21:02','2026-01-11 16:21:02',1,1,1,1,1);
/*!40000 ALTER TABLE `notificacoes_preferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único do pedido de reset',
  `utilizador_id` int(11) NOT NULL COMMENT 'ID do utilizador que pediu reset',
  `email` varchar(255) NOT NULL COMMENT 'Email do utilizador',
  `token` varchar(255) NOT NULL COMMENT 'Token único de verificação (hash)',
  `expira_em` datetime NOT NULL COMMENT 'Data/hora de expiração do token (1 hora)',
  `usado` tinyint(1) DEFAULT 0 COMMENT '0=não usado, 1=já utilizado',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data/hora de criação do pedido',
  `usado_em` datetime DEFAULT NULL COMMENT 'Data/hora em que o token foi usado',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP de origem do pedido',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'User agent do browser',
  PRIMARY KEY (`id`),
  KEY `utilizador_id` (`utilizador_id`),
  KEY `idx_token` (`token`),
  KEY `idx_email` (`email`),
  KEY `idx_expira` (`expira_em`),
  KEY `idx_usado` (`usado`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planos`
--

DROP TABLE IF EXISTS `planos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `limite_produtos` int(11) DEFAULT NULL,
  `rastreio_tipo` varchar(200) NOT NULL,
  `relatorio_pdf` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planos`
--

LOCK TABLES `planos` WRITE;
/*!40000 ALTER TABLE `planos` DISABLE KEYS */;
INSERT INTO `planos` VALUES (1,'Plano Essencial Verde',0.00,5,'Basico',0),(2,'Plano Crescimento Circular',25.00,10,'Basico',1),(3,'Plano Profissional Eco+',100.00,NULL,'Avancado',1);
/*!40000 ALTER TABLE `planos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planos_ativos`
--

DROP TABLE IF EXISTS `planos_ativos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planos_ativos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anunciante_id` int(11) NOT NULL,
  `plano_id` int(11) NOT NULL,
  `data_inicio` date DEFAULT curdate(),
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `anunciante_id` (`anunciante_id`),
  KEY `plano_id` (`plano_id`),
  CONSTRAINT `planos_ativos_ibfk_1` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `planos_ativos_ibfk_2` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planos_ativos`
--

LOCK TABLES `planos_ativos` WRITE;
/*!40000 ALTER TABLE `planos_ativos` DISABLE KEYS */;
INSERT INTO `planos_ativos` VALUES (1,3,2,'2025-12-06',NULL,1),(2,4,2,'2026-01-23','2026-02-22',0),(3,4,3,'2026-01-23','2026-02-22',1);
/*!40000 ALTER TABLE `planos_ativos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_fotos`
--

DROP TABLE IF EXISTS `produto_fotos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto_fotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `foto` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `produto_fotos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_fotos`
--

LOCK TABLES `produto_fotos` WRITE;
/*!40000 ALTER TABLE `produto_fotos` DISABLE KEYS */;
INSERT INTO `produto_fotos` VALUES (1,1,'src/img/m83296720182_6.avif'),(2,1,'src/img/m83296720182_5.avif');
/*!40000 ALTER TABLE `produto_fotos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos` (
  `Produto_id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `tipo_produto_id` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `genero` varchar(255) DEFAULT NULL,
  `anunciante_id` int(11) DEFAULT NULL,
  `designer_id` int(11) DEFAULT NULL,
  `marca` varchar(30) DEFAULT NULL,
  `tamanho` varchar(30) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `data_criacao` date DEFAULT curdate(),
  `ativo` tinyint(1) DEFAULT 1,
  `stock` int(11) DEFAULT 0,
  PRIMARY KEY (`Produto_id`),
  KEY `tipo_produto_id` (`tipo_produto_id`),
  KEY `anunciante_id` (`anunciante_id`),
  CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`tipo_produto_id`) REFERENCES `tipo_produtos` (`id`),
  CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (1,'Blusa Colorida Custo Barcelona',1,15.00,'src/img/4aa0032639cfb631fe2d24df2baeb114.jpg','Mulher',1,NULL,'Custo Barcelona','S','Excelente','Blusa colorida da Custo Barcelona nunca usada, com etiqueta.','2025-12-06',1,0),(2,'Vestido Versace Coleção 2003',1,289.90,'src/img/ddbe3c8f8aef92c51a1745dcb4923752.jpg','Mulher',3,NULL,'Versace','S','Como Novo','Vestido Versace cole??o 2003, nunca usado.','2025-12-06',0,10),(3,'Calças de ganga Low Rise',1,35.40,'src/img/835c014897bbbab6c135f8dfd9f59bdf.jpg','Mulher',1,NULL,'NYWS','S','Excelente','Cal?as de ganga Low Rise, nunca usadas.','2025-12-06',1,10),(4,'Botas pele',2,79.00,'src/img/b7358d779d9fc865d22f428ec1641391.jpg','Mulher',3,NULL,'Ana Salazar','38','Como Novo','Botas de pele Michael Kors, eleg?ncia e sofistica??o.','2025-12-06',0,10),(5,'Blusão de ganga',1,23.80,'src/img/58c4e54e715841986e82c05fb3506a0d.jpg','Mulher',1,NULL,'Miss Sixty','M','Excelente','Blus?o de ganga, nunca usado.','2025-12-06',1,10),(6,'Sweatshirt Billabong',1,23.80,'src/img/6fa73f19c9a53d2c3d32f4c7e8d84300.jpg','Homem',2,NULL,'Billabong','M','Excelente','Sweatshirt Azul e Amarela da  Billabong, nunca usado.','2025-12-06',1,10),(7,'Leggings Yoga',1,10.90,'src/img/206af231b9b61956182afd1d73a47082.jpg','Mulher',3,NULL,'Abercrombie & Fitch','XS','Excelente','Leggings Yoga, confort?veis e novas.','2025-12-06',0,10),(8,'Camisa GAP Azul',1,23.80,'src/img/9b8d88a4f89987b10e27b2f05ece802f.jpg','Homem',2,NULL,'GAP','M','Excelente','Camisa Azul da  GAP, nunca usado.','2025-12-06',1,10),(9,'Casaco Desportivo Nike',1,22.00,'src/img/c5584a14a4d828c79754a5b553a3abe3.jpg','Homem',2,NULL,'Nike','L','Como Novo','Casaco desportivo Nike, em excelente estado.','2025-12-06',1,10),(10,'Sweatshirt North Face',1,18.00,'src/img/northface.webp','Crian?a',2,NULL,'North Face','7 anos','Excelente','Sweatshirt da North Face nunca usada, com etiqueta.','2025-12-06',1,10),(11,'Botas Timbaland',2,26.00,'src/img/image66.webp','Crian?a',3,NULL,'Timbaland','10 anos','Excelente','Botas de couro Timbaland para crian?a, nunca usadas.','2025-12-06',0,10),(12,'Camisa Polo Tommy Hilfiger',1,79.90,'src/img/camisapolotommy.jpg','Homem',2,NULL,'Tommy Hilfiger','M','Novo','Camisa Polo Tommy Hilfiger, nunca usada, com etiqueta.','2025-12-06',1,10),(13,'Ténis Adidas UltraBoost',2,249.99,'src/img/Adidas Ultraboost Light - lateral 2.jpg','Homem',2,NULL,'Adidas','42','Novo','T?nis Adidas UltraBoost, conforto incompar?vel para atividades f?sicas.','2025-12-06',1,10),(14,'Bolsa de Couro Michael Kors',3,359.99,'src/img/1298bdfb53f943dc29132b4684dbb507.jpg','Mulher',3,NULL,'Michael Kors','?nico tamanho','Novo','Bolsa de couro Michael Kors de 1981.','2025-12-06',0,10),(15,'Creme Hidratante Nivea',4,29.90,'src/img/b06b57ded4c5ded4bc7e600eada0649c.jpg','Mulher',3,NULL,'Nivea','?nico tamanho','Novo','Creme hidratante Nivea Edi??o Limitada de 2006 lacrado.','2025-12-06',0,0),(16,'Fone de Ouvido Bose QuietComfort 35',5,899.00,'src/img/s-l1200.webp','Homem',2,NULL,'Bose','?nico tamanho','Novo','Fone de ouvido Bose com cancelamento de ru?do, perfeito para viagens e uso di?rio.','2025-12-06',1,10),(17,'Calças de ganga Levis',1,19.00,'src/img/63ee2e61813747ba02e97868eb523680.jpg','Homem',2,NULL,'Levis','XL','Novo','Cal?as de gnaga Levis nunca usadas com etiqueta.','2025-12-06',1,10),(18,'Casaco Verde Pistachio',1,29.90,'src/img/image.webp','Mulher',3,NULL,'Naz','S','Novo','Camisola de Inverno Naz na cor verde pistachio.','2025-12-06',0,10),(19,'Ténis Azuis Vegan',2,69.90,'src/img/MG_7966.webp','Mulher',3,NULL,'Nea Vegan Shoes','38','Novo','T?nis azuis vegan da Nea Vegan Shoes, perfeitos para caminhadas e corridas.','2025-12-06',0,10),(20,'Calça de ganga em algodão',1,64.00,'src/img/1759334984_3ece5eec09df8225764acda4b0d3c1d6.jpg','Crian?a',3,NULL,'Playup','4-5 anos','Novo','Cal?a de ganga em algod?o para crian?a da Playup.','2025-12-06',0,10),(22,'Colar Animal Edition',3,15.00,'src/img/animalcross.jpeg','Mulher',4,1,'HeartKnows','Tamanho ?nico','Novo','Colar com missangas coloridas e daltamata cole??o Animal Cross.','2025-12-06',1,10),(23,'Mala Missangas',3,35.00,'src/img/download (10).jpeg','Mulher',5,1,'MatildeMayer','Tamanho ?nico','Novo','Mala branca de missangas feita ? m?o.','2025-12-06',1,10),(24,'Colar Cruz',3,12.00,'src/img/cora-pursley-dupe.jpeg','Mulher',8,1,'SolangeJewels','Tamanho ?nico','Novo','Colar com cruz.','2025-12-06',1,10),(25,'T-shirt Cult Classic',1,25.00,'src/img/ccult.jpeg','Homem',6,1,'Carlo Pereira','M','Novo','T-shirt Cult Classic Azul.','2025-12-06',1,10),(26,'T-shirt Cult Classic',1,25.00,'src/img/ccult.jpeg','Homem',6,1,'Carlo Pereira','M','Novo','T-shirt Cult Classic Azul.','2025-12-06',1,10);
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranking`
--

DROP TABLE IF EXISTS `ranking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) DEFAULT NULL,
  `pontos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranking`
--

LOCK TABLES `ranking` WRITE;
/*!40000 ALTER TABLE `ranking` DISABLE KEYS */;
INSERT INTO `ranking` VALUES (1,'Bronze',0),(2,'Ouro',50),(3,'Diamante',150);
/*!40000 ALTER TABLE `ranking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rendimento`
--

DROP TABLE IF EXISTS `rendimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rendimento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valor` decimal(10,2) DEFAULT NULL,
  `anunciante_id` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `anunciante_id` (`anunciante_id`),
  CONSTRAINT `rendimento_ibfk_1` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendimento`
--

LOCK TABLES `rendimento` WRITE;
/*!40000 ALTER TABLE `rendimento` DISABLE KEYS */;
INSERT INTO `rendimento` VALUES (1,25.00,3,'Premium comprado!','2025-12-06 19:18:55'),(2,100.00,4,'Enterprize comprado!','2025-12-06 19:18:55'),(3,25.00,5,'Premium comprado!','2025-12-06 19:18:55'),(4,5.00,NULL,'Comiss?o cobrada!','2025-12-06 19:18:55');
/*!40000 ALTER TABLE `rendimento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `stats_devolucoes_anunciante`
--

DROP TABLE IF EXISTS `stats_devolucoes_anunciante`;
/*!50001 DROP VIEW IF EXISTS `stats_devolucoes_anunciante`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `stats_devolucoes_anunciante` AS SELECT
 1 AS `anunciante_id`,
  1 AS `total_devolucoes`,
  1 AS `pendentes`,
  1 AS `aprovadas`,
  1 AS `rejeitadas`,
  1 AS `reembolsadas`,
  1 AS `valor_total_reembolsado` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tipo_produtos`
--

DROP TABLE IF EXISTS `tipo_produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_produtos`
--

LOCK TABLES `tipo_produtos` WRITE;
/*!40000 ALTER TABLE `tipo_produtos` DISABLE KEYS */;
INSERT INTO `tipo_produtos` VALUES (1,'Roupa'),(2,'Cal?ado'),(3,'Acess?rios'),(4,'Beleza'),(5,'Outros'),(6,'Artesanato');
/*!40000 ALTER TABLE `tipo_produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_utilizadores`
--

DROP TABLE IF EXISTS `tipo_utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_utilizadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_utilizadores`
--

LOCK TABLES `tipo_utilizadores` WRITE;
/*!40000 ALTER TABLE `tipo_utilizadores` DISABLE KEYS */;
INSERT INTO `tipo_utilizadores` VALUES (1,'Administrador'),(2,'Cliente'),(3,'Anunciante');
/*!40000 ALTER TABLE `tipo_utilizadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportadora`
--

DROP TABLE IF EXISTS `transportadora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transportadora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` enum('CTT','DPD') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportadora`
--

LOCK TABLES `transportadora` WRITE;
/*!40000 ALTER TABLE `transportadora` DISABLE KEYS */;
INSERT INTO `transportadora` VALUES (1,'CTT'),(2,'DPD');
/*!40000 ALTER TABLE `transportadora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `apelido` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_verificado` tinyint(1) DEFAULT 0,
  `token_verificacao` varchar(64) DEFAULT NULL,
  `token_expira_em` datetime DEFAULT NULL,
  `nif` varchar(20) DEFAULT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `morada` varchar(255) DEFAULT NULL,
  `foto` varchar(250) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_utilizador_id` int(11) DEFAULT NULL,
  `plano_id` int(11) DEFAULT NULL,
  `ranking_id` int(11) DEFAULT NULL,
  `pontos_conf` int(11) DEFAULT 0,
  `data_criacao` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_tipo` (`email`,`tipo_utilizador_id`),
  KEY `ranking_id` (`ranking_id`),
  KEY `tipo_utilizador_id` (`tipo_utilizador_id`),
  KEY `plano_id` (`plano_id`),
  CONSTRAINT `utilizadores_ibfk_1` FOREIGN KEY (`ranking_id`) REFERENCES `ranking` (`id`),
  CONSTRAINT `utilizadores_ibfk_2` FOREIGN KEY (`tipo_utilizador_id`) REFERENCES `tipo_utilizadores` (`id`),
  CONSTRAINT `utilizadores_ibfk_3` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores`
--

LOCK TABLES `utilizadores` WRITE;
/*!40000 ALTER TABLE `utilizadores` DISABLE KEYS */;
INSERT INTO `utilizadores` VALUES (1,'Admin WeGreen',NULL,'admin@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexels-beccacorreiaph-31095884.jpg','admin123',1,1,3,200,NULL),(2,'Joao',NULL,'joao@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexels-stefanstefancik-91227.jpg','cliente123',2,1,1,10,NULL),(3,'Maria Santos',NULL,'maria@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexels-olly-733872.jpg','anunciante123',3,2,2,75,NULL),(4,'Maria Luiza',NULL,'Luiza@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/josefina-pereyra-dupe.jpeg','anunciante123',3,3,3,54,NULL),(5,'Matilde Mayer',NULL,'Matilde@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexels-beccacorreiaph-31095531.jpg','anunciante123',3,1,3,350,NULL),(6,'Carlos Pereira',NULL,'Carlos@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexels-rb-audiovisual-1819481.jpg','anunciante123',3,1,3,98,NULL),(7,'Mango',NULL,'mango@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/3186bd92-91b4-480e-a033-be3c45e9fe76.jpeg','mango123',2,1,1,10,NULL),(8,'Solange Jewels',NULL,'Solange@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexepexels-victor-dubugras-1479988588-26976686.jpg','anunciante123',3,1,3,350,NULL),(11,'joão','santos','jmssgames@gmail.com',1,NULL,NULL,'012345678',NULL,NULL,'src/img/pexels-beccacorreiaph-31095884.jpg','3dfcab79ed21fd89c9eb25e9864a6155',2,1,1,0,'2026-01-13'),(14,'Joao','Silva','joao@wegreen.pt',1,NULL,NULL,NULL,NULL,NULL,'src/img/pexels-beccacorreiaph-31095884.jpg','e10adc3949ba59abbe56e057f20f883e',3,1,1,0,'2026-01-16');
/*!40000 ALTER TABLE `utilizadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendas`
--

DROP TABLE IF EXISTS `vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) DEFAULT NULL,
  `stripe_session_id` varchar(100) DEFAULT NULL COMMENT 'ID da sess?o Stripe',
  `anunciante_id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT 1,
  `valor` decimal(10,2) DEFAULT NULL,
  `lucro` decimal(10,2) DEFAULT NULL,
  `data_venda` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `encomenda_id` (`encomenda_id`),
  KEY `anunciante_id` (`anunciante_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`encomenda_id`) REFERENCES `encomendas` (`id`),
  CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`),
  CONSTRAINT `vendas_ibfk_3` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendas`
--

LOCK TABLES `vendas` WRITE;
/*!40000 ALTER TABLE `vendas` DISABLE KEYS */;
INSERT INTO `vendas` VALUES (1,1,NULL,3,1,2,39.98,2.40,'2025-12-06'),(2,2,NULL,3,3,1,89.99,5.40,'2025-12-06'),(3,3,NULL,3,2,1,49.99,3.00,'2025-12-06'),(4,4,NULL,4,3,3,269.97,16.20,'2025-12-06'),(5,5,NULL,4,2,1,49.99,3.00,'2025-12-06'),(6,NULL,NULL,3,1,2,100.00,20.00,'2025-12-08'),(7,NULL,NULL,3,2,1,50.00,10.00,'2025-12-07');
/*!40000 ALTER TABLE `vendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `view_devolucoes_completa`
--

DROP TABLE IF EXISTS `view_devolucoes_completa`;
/*!50001 DROP VIEW IF EXISTS `view_devolucoes_completa`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_devolucoes_completa` AS SELECT
 1 AS `id`,
  1 AS `encomenda_id`,
  1 AS `codigo_devolucao`,
  1 AS `cliente_id`,
  1 AS `anunciante_id`,
  1 AS `produto_id`,
  1 AS `valor_reembolso`,
  1 AS `motivo`,
  1 AS `motivo_detalhe`,
  1 AS `notas_cliente`,
  1 AS `notas_anunciante`,
  1 AS `estado`,
  1 AS `payment_intent_id`,
  1 AS `reembolso_stripe_id`,
  1 AS `reembolso_status`,
  1 AS `fotos`,
  1 AS `codigo_envio_devolucao`,
  1 AS `transportadora_devolucao_id`,
  1 AS `data_solicitacao`,
  1 AS `data_aprovacao`,
  1 AS `data_rejeicao`,
  1 AS `data_produto_recebido`,
  1 AS `data_reembolso`,
  1 AS `updated_at`,
  1 AS `codigo_encomenda`,
  1 AS `data_entrega_original`,
  1 AS `produto_nome`,
  1 AS `produto_foto` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'wegreen'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `expire_old_plans` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = cp850 */ ;;
/*!50003 SET character_set_results = cp850 */ ;;
/*!50003 SET collation_connection  = cp850_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`localhost`*/ /*!50106 EVENT `expire_old_plans` ON SCHEDULE EVERY 1 DAY STARTS '2026-01-24 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE affected_rows INT DEFAULT 0;

    
    UPDATE planos_ativos
    SET ativo = 0
    WHERE ativo = 1
      AND data_fim IS NOT NULL
      AND data_fim < CURDATE();

    
    SET affected_rows = ROW_COUNT();

    
    IF affected_rows > 0 THEN
        UPDATE utilizadores u
        LEFT JOIN planos_ativos pa ON u.id = pa.anunciante_id AND pa.ativo = 1
        SET u.plano_id = 1
        WHERE pa.id IS NULL
          AND u.plano_id != 1
          AND u.tipo = 3; 
    END IF;

    
    
    

END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
/*!50106 DROP EVENT IF EXISTS `notify_expiring_plans` */;;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = cp850 */ ;;
/*!50003 SET character_set_results = cp850 */ ;;
/*!50003 SET collation_connection  = cp850_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`localhost`*/ /*!50106 EVENT `notify_expiring_plans` ON SCHEDULE EVERY 1 DAY STARTS '2026-01-24 09:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    
    

    
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_expiring_plans AS
    SELECT
        pa.id AS plano_ativo_id,
        pa.anunciante_id,
        u.nome,
        u.email,
        p.nome AS plano_nome,
        pa.data_fim,
        DATEDIFF(pa.data_fim, CURDATE()) AS dias_restantes
    FROM planos_ativos pa
    INNER JOIN utilizadores u ON pa.anunciante_id = u.id
    INNER JOIN planos p ON pa.plano_id = p.id
    WHERE pa.ativo = 1
      AND pa.data_fim IS NOT NULL
      AND DATEDIFF(pa.data_fim, CURDATE()) IN (7, 3, 1);

    
    
    
    
    
    
    

    DROP TEMPORARY TABLE IF EXISTS temp_expiring_plans;

END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'wegreen'
--

--
-- Final view structure for view `stats_devolucoes_anunciante`
--

/*!50001 DROP VIEW IF EXISTS `stats_devolucoes_anunciante`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `stats_devolucoes_anunciante` AS select `d`.`anunciante_id` AS `anunciante_id`,count(`d`.`id`) AS `total_devolucoes`,sum(case when `d`.`estado` = 'solicitada' then 1 else 0 end) AS `pendentes`,sum(case when `d`.`estado` = 'aprovada' then 1 else 0 end) AS `aprovadas`,sum(case when `d`.`estado` = 'rejeitada' then 1 else 0 end) AS `rejeitadas`,sum(case when `d`.`estado` = 'reembolsada' then 1 else 0 end) AS `reembolsadas`,coalesce(sum(`d`.`valor_reembolso`),0) AS `valor_total_reembolsado` from `devolucoes` `d` group by `d`.`anunciante_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_devolucoes_completa`
--

/*!50001 DROP VIEW IF EXISTS `view_devolucoes_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_devolucoes_completa` AS select `d`.`id` AS `id`,`d`.`encomenda_id` AS `encomenda_id`,`d`.`codigo_devolucao` AS `codigo_devolucao`,`d`.`cliente_id` AS `cliente_id`,`d`.`anunciante_id` AS `anunciante_id`,`d`.`produto_id` AS `produto_id`,`d`.`valor_reembolso` AS `valor_reembolso`,`d`.`motivo` AS `motivo`,`d`.`motivo_detalhe` AS `motivo_detalhe`,`d`.`notas_cliente` AS `notas_cliente`,`d`.`notas_anunciante` AS `notas_anunciante`,`d`.`estado` AS `estado`,`d`.`payment_intent_id` AS `payment_intent_id`,`d`.`reembolso_stripe_id` AS `reembolso_stripe_id`,`d`.`reembolso_status` AS `reembolso_status`,`d`.`fotos` AS `fotos`,`d`.`codigo_envio_devolucao` AS `codigo_envio_devolucao`,`d`.`transportadora_devolucao_id` AS `transportadora_devolucao_id`,`d`.`data_solicitacao` AS `data_solicitacao`,`d`.`data_aprovacao` AS `data_aprovacao`,`d`.`data_rejeicao` AS `data_rejeicao`,`d`.`data_produto_recebido` AS `data_produto_recebido`,`d`.`data_reembolso` AS `data_reembolso`,`d`.`updated_at` AS `updated_at`,`e`.`codigo_encomenda` AS `codigo_encomenda`,`e`.`data_envio` AS `data_entrega_original`,`p`.`nome` AS `produto_nome`,`p`.`foto` AS `produto_foto` from ((`devolucoes` `d` join `encomendas` `e` on(`d`.`encomenda_id` = `e`.`id`)) join `produtos` `p` on(`d`.`produto_id` = `p`.`Produto_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-23 23:38:03


SET FOREIGN_KEY_CHECKS = 1;
SHOW EVENTS;
