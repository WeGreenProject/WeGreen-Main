/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET GLOBAL event_scheduler = ON;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `wegreen` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wegreen`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes_produtos`
--

DROP TABLE IF EXISTS `avaliacoes_produtos`;
CREATE TABLE IF NOT EXISTS `avaliacoes_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `encomenda_codigo` varchar(50) NOT NULL,
  `avaliacao` int(11) NOT NULL CHECK (`avaliacao` >= 1 and `avaliacao` <= 5),
  `comentario` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_avaliacao` (`utilizador_id`,`produto_id`,`encomenda_codigo`),
  KEY `idx_produto_id` (`produto_id`),
  KEY `idx_utilizador_id` (`utilizador_id`),
  KEY `idx_data_criacao` (`data_criacao`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `avaliacoes_produtos`
--

INSERT INTO `avaliacoes_produtos` (`id`, `produto_id`, `utilizador_id`, `encomenda_codigo`, `avaliacao`, `comentario`, `data_criacao`) VALUES
(1, 25, 1, 'WG12353', 5, 'Excelente qualidade! A t-shirt é muito confortável e o tecido é de primeira. Recomendo!', '2026-01-25 19:38:01'),
(2, 25, 2, 'WG12353_0', 4, 'Boa t-shirt, mas esperava um azul um pouco mais escuro. De resto, muito boa!', '2026-01-25 19:38:41'),
(3, 25, 3, 'WG12353_1', 5, 'Perfeita! Chegou rápido e a qualidade é excelente. Já comprei mais!', '2026-01-25 19:38:41'),
(4, 25, 4, 'WG12353_2', 3, 'Razoável. O tamanho M ficou um pouco justo.', '2026-01-25 19:38:41'),
(5, 26, 1, 'WG12353_P26_0', 5, 'Excelente qualidade! A t-shirt é muito confortável e o tecido é de primeira. Recomendo!', '2026-01-25 19:42:55'),
(6, 26, 2, 'WG12353_P26_1', 4, 'Boa t-shirt, mas esperava um azul um pouco mais escuro. De resto, muito boa!', '2026-01-25 19:42:55'),
(7, 26, 3, 'WG12353_P26_2', 5, 'Perfeita! Chegou rápido e a qualidade é excelente. Já comprei mais!', '2026-01-25 19:42:55'),
(8, 26, 4, 'WG12353_P26_3', 3, 'Razoável. O tamanho M ficou um pouco justo.', '2026-01-25 19:42:55'),
(10, 3, 2, 'WG12350', 4, 'excelente', '2026-02-17 18:05:11'),
(11, 18, 24, 'WG177152669362087bd7b', 4, 'bom produto', '2026-02-19 19:09:28'),
(12, 22, 24, 'WG177152669362087bd7b', 4, 'excelente', '2026-02-19 19:10:45'),
(13, 20, 24, 'WG177152669362087bd7b', 5, 'fantastico', '2026-02-19 19:10:51'),
(14, 19, 24, 'WG177152669362087bd7b', 5, 'excelente', '2026-02-19 19:10:59');

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho_itens`
--

DROP TABLE IF EXISTS `carrinho_itens`;
CREATE TABLE IF NOT EXISTS `carrinho_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilizador_id` varchar(50) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1 CHECK (`quantidade` > 0),
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`utilizador_id`,`produto_id`),
  KEY `produto_id` (`produto_id`),
  KEY `idx_utilizador_id` (`utilizador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `carrinho_itens`
--

INSERT INTO `carrinho_itens` (`id`, `utilizador_id`, `produto_id`, `quantidade`, `data_adicao`, `data_atualizacao`) VALUES
(39, '-422816405', 6, 1, '2026-02-14 20:05:31', '2026-02-14 20:05:31'),
(40, '-422816405', 26, 3, '2026-02-14 20:05:37', '2026-02-14 20:07:47'),
(69, '2', 20, 1, '2026-02-17 16:13:48', '2026-02-17 16:13:59'),
(70, '2', 17, 1, '2026-02-17 16:23:22', '2026-02-17 16:23:22'),
(74, '11', 20, 1, '2026-02-19 14:39:24', '2026-02-19 14:58:24'),
(77, '2', 19, 1, '2026-02-19 18:26:28', '2026-02-19 18:27:53'),
(78, '2', 18, 1, '2026-02-19 18:26:28', '2026-02-19 18:27:53');

-- --------------------------------------------------------

--
-- Estrutura da tabela `denuncias`
--

DROP TABLE IF EXISTS `denuncias`;
CREATE TABLE IF NOT EXISTS `denuncias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `denunciante_id` int(11) DEFAULT NULL,
  `denunciado_id` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_anexo` varchar(255) DEFAULT NULL,
  `estado` varchar(250) DEFAULT 'Pendente',
  `data_registo` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `denunciante_id` (`denunciante_id`),
  KEY `denunciado_id` (`denunciado_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `descontos_ranking`
--

DROP TABLE IF EXISTS `descontos_ranking`;
CREATE TABLE IF NOT EXISTS `descontos_ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anunciante_id` int(11) NOT NULL,
  `tipo_desconto` varchar(100) NOT NULL DEFAULT '50_pct_plano',
  `valor_desconto` decimal(5,2) NOT NULL DEFAULT 50.00,
  `usado` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_uso` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_anunciante_id` (`anunciante_id`),
  KEY `idx_usado` (`usado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `devolucoes`
--

DROP TABLE IF EXISTS `devolucoes`;
CREATE TABLE IF NOT EXISTS `devolucoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) NOT NULL,
  `codigo_devolucao` varchar(50) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `anunciante_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1 COMMENT 'Quantidade do produto sendo devolvida',
  `valor_reembolso` decimal(10,2) NOT NULL,
  `motivo` enum('defeituoso','tamanho_errado','nao_como_descrito','arrependimento','outro') NOT NULL,
  `motivo_detalhe` text DEFAULT NULL,
  `notas_cliente` text DEFAULT NULL,
  `notas_anunciante` text DEFAULT NULL,
  `estado` enum('solicitada','aprovada','rejeitada','produto_enviado','produto_recebido','reembolsada','cancelada') DEFAULT 'solicitada',
  `payment_intent_id` varchar(100) DEFAULT NULL,
  `reembolso_stripe_id` varchar(100) DEFAULT NULL,
  `reembolso_status` varchar(50) DEFAULT NULL COMMENT 'pending, succeeded, failed, canceled',
  `fotos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array de URLs das fotos do produto/defeito' CHECK (json_valid(`fotos`)),
  `codigo_rastreio` varchar(100) DEFAULT NULL,
  `data_envio_cliente` datetime DEFAULT NULL,
  `notas_recebimento` text DEFAULT NULL,
  `data_recebimento` datetime DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `devolucoes`
--

INSERT INTO `devolucoes` (`id`, `encomenda_id`, `codigo_devolucao`, `cliente_id`, `anunciante_id`, `produto_id`, `quantidade`, `valor_reembolso`, `motivo`, `motivo_detalhe`, `notas_cliente`, `notas_anunciante`, `estado`, `payment_intent_id`, `reembolso_stripe_id`, `reembolso_status`, `fotos`, `codigo_rastreio`, `data_envio_cliente`, `notas_recebimento`, `data_recebimento`, `codigo_envio_devolucao`, `transportadora_devolucao_id`, `data_solicitacao`, `data_aprovacao`, `data_rejeicao`, `data_produto_recebido`, `data_reembolso`, `updated_at`) VALUES
(3, 8, 'DEV20260124F3899A', 2, 1, 1, 1, 15.00, 'nao_como_descrito', 'A cor é diferente da foto.', 'Aguardo retorno sobre a devolução.', NULL, 'produto_recebido', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-21 13:40:47', NULL, NULL, NULL, NULL, '2026-01-24 13:40:47'),
(16, 1, 'DEV-20260125-0001', 10, 3, 1, 1, 29.99, 'defeituoso', 'Produto chegou com defeito', 'Cliente reportou defeito', '', 'aprovada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-25 15:55:39', '2026-02-16 15:22:20', NULL, NULL, NULL, '2026-02-16 15:22:20'),
(20, 2, 'DEV-20260125-0002', 11, 3, 2, 1, 45.50, 'tamanho_errado', 'Tamanho não serve', 'Tamanho muito pequeno', NULL, 'aprovada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-25 15:56:17', NULL, NULL, NULL, NULL, '2026-02-19 22:48:50'),
(21, 3, 'DEV-20260125-0003', 10, 3, 3, 1, 35.00, 'nao_como_descrito', 'A cor não é a mesma da foto', 'A cor nÒo Ú a mesma da foto', NULL, 'reembolsada', NULL, 'manual', 'manual', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-25 15:56:17', NULL, NULL, NULL, '2026-02-16 15:55:04', '2026-02-19 22:48:50'),
(22, 4, 'DEV-20260125-0004', 11, 3, 4, 1, 28.75, 'arrependimento', 'Não quero mais', 'Mudei de ideia', NULL, 'rejeitada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-25 15:56:17', NULL, NULL, NULL, NULL, '2026-02-19 22:48:50'),
(23, 5, 'DEV-20260125-0005', 10, 3, 5, 1, 52.00, 'outro', 'Observações diversas', 'Observaþ§es diversas', NULL, 'reembolsada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-25 15:56:17', NULL, NULL, NULL, NULL, '2026-02-19 22:48:50'),
(26, 55, 'DEV20260216170427435', 20, 6, 26, 1, 25.00, 'defeituoso', 'Produto com defeito na costura - TESTE AUTOMATIZADO', 'Teste automatizado do fluxo completo', 'Aprovado por teste automatizado', 'reembolsada', 'pi_test_20260214232357_B', 'manual_stripe_falha', 'manual', '[]', 'CTT-TEST-123456', '2026-02-16 16:04:32', 'Produto recebido em boas condições - teste', '2026-02-16 16:04:33', NULL, NULL, '2026-02-16 16:04:27', '2026-02-16 16:04:30', NULL, '2026-02-16 16:04:33', '2026-02-16 16:04:36', '2026-02-16 16:04:36'),
(29, 59, 'DEV20260219211120853', 24, 4, 22, 3, 45.00, 'tamanho_errado', 'produtos defeituosos ', 'produtos defeituosos', NULL, 'solicitada', 'pi_3T2cDXBgsjq4eGsl0S4IpSsV', NULL, NULL, '[\"assets\\/media\\/devolucoes\\/dev_69976e660cc9b_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e660bdef_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e6610cee_1771531878.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 20:11:20', NULL, NULL, NULL, NULL, '2026-02-19 20:11:20'),
(30, 59, 'DEV20260219211120893', 24, 3, 20, 3, 192.00, 'tamanho_errado', 'produtos defeituosos ', 'produtos defeituosos', 'mande os produtos', 'reembolsada', 'pi_3T2cDXBgsjq4eGsl0S4IpSsV', 're_3T2cDXBgsjq4eGsl0NTzGGJl', 'succeeded', '[\"assets\\/media\\/devolucoes\\/dev_69976e660cc9b_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e660bdef_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e6610cee_1771531878.png\"]', NULL, '2026-02-19 20:46:02', '', '2026-02-19 20:51:57', 'DEVENV20260219214602188', NULL, '2026-02-19 20:11:20', '2026-02-19 20:30:11', NULL, '2026-02-19 20:51:57', '2026-02-19 20:52:23', '2026-02-19 20:52:23'),
(31, 59, 'DEV20260219211120154', 24, 3, 19, 3, 209.70, 'tamanho_errado', 'produtos defeituosos ', 'produtos defeituosos', 'as fotos nao mostram defeito', 'rejeitada', 'pi_3T2cDXBgsjq4eGsl0S4IpSsV', NULL, NULL, '[\"assets\\/media\\/devolucoes\\/dev_69976e660cc9b_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e660bdef_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e6610cee_1771531878.png\"]', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 20:11:20', NULL, '2026-02-19 20:30:28', NULL, NULL, '2026-02-19 20:30:28'),
(32, 59, 'DEV20260219211120285', 24, 3, 18, 3, 89.70, 'tamanho_errado', 'produtos defeituosos ', 'produtos defeituosos', 'confirmo o defeito', 'reembolsada', 'pi_3T2cDXBgsjq4eGsl0S4IpSsV', 're_3T2cDXBgsjq4eGsl0VH37Elt', 'succeeded', '[\"assets\\/media\\/devolucoes\\/dev_69976e660cc9b_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e660bdef_1771531878.jpg\",\"assets\\/media\\/devolucoes\\/dev_69976e6610cee_1771531878.png\"]', NULL, '2026-02-19 20:56:24', 'perfeito', '2026-02-19 20:58:23', 'DEVENV20260219215624907', NULL, '2026-02-19 20:11:20', '2026-02-19 20:55:44', NULL, '2026-02-19 20:58:23', '2026-02-19 20:59:16', '2026-02-19 20:59:16');

--
-- Acionadores `devolucoes`
--
DROP TRIGGER IF EXISTS `after_devolucao_update`;
DELIMITER $$
CREATE TRIGGER `after_devolucao_update` AFTER UPDATE ON `devolucoes` FOR EACH ROW BEGIN
  IF OLD.estado != NEW.estado THEN
    INSERT INTO `historico_devolucoes` (`id`, `devolucao_id`, `estado_anterior`, `estado_novo`, `observacao`, `alterado_por`, `data_alteracao`) VALUES
(1, 16, 'solicitada', 'aprovada', 'Estado alterado de solicitada para aprovada', 'sistema', '2026-02-16 15:22:20'),
(2, 16, 'solicitada', 'aprovada', '', 'anunciante', '2026-02-16 15:22:20'),
(3, 21, 'produto_recebido', 'reembolsada', 'Estado alterado de produto_recebido para reembolsada', 'sistema', '2026-02-16 15:55:04'),
(4, 21, 'produto_recebido', 'reembolsada', 'Reembolso manual (sem pagamento Stripe associado)', 'sistema', '2026-02-16 15:55:04'),
(19, 26, NULL, 'solicitada', 'Devolução solicitada pelo cliente', 'cliente', '2026-02-16 16:04:27'),
(20, 26, 'solicitada', 'aprovada', 'Aprovado por teste automatizado', 'anunciante', '2026-02-16 16:04:30'),
(21, 26, 'aprovada', 'produto_enviado', 'Código de rastreio: CTT-TEST-123456', 'cliente', '2026-02-16 16:04:32'),
(22, 26, 'produto_enviado', 'produto_recebido', 'Produto recebido em boas condições - teste', 'anunciante', '2026-02-16 16:04:33'),
(23, 26, 'produto_recebido', 'reembolsada', 'Reembolso manual (falha Stripe: No such payment_intent: \'pi_test_20260214232357_B\')', 'sistema', '2026-02-16 16:04:36'),
(25, 29, NULL, 'solicitada', 'Devolução solicitada pelo cliente', 'cliente', '2026-02-19 20:11:20'),
(26, 30, NULL, 'solicitada', 'Devolução solicitada pelo cliente', 'cliente', '2026-02-19 20:11:20'),
(27, 31, NULL, 'solicitada', 'Devolução solicitada pelo cliente', 'cliente', '2026-02-19 20:11:20'),
(28, 32, NULL, 'solicitada', 'Devolução solicitada pelo cliente', 'cliente', '2026-02-19 20:11:20'),
(29, 30, 'solicitada', 'aprovada', 'mande os produtos', 'anunciante', '2026-02-19 20:30:11'),
(30, 31, 'solicitada', 'rejeitada', 'as fotos nao mostram defeito', 'anunciante', '2026-02-19 20:30:28'),
(31, 30, 'aprovada', 'produto_enviado', 'Código interno de envio: DEVENV20260219214602188', 'cliente', '2026-02-19 20:46:02'),
(32, 30, 'produto_enviado', 'produto_recebido', 'Código de envio validado: DEVENV20260219214602188', 'anunciante', '2026-02-19 20:51:57'),
(33, 30, 'produto_recebido', 'reembolsada', 'Reembolso Stripe ID: re_3T2cDXBgsjq4eGsl0NTzGGJl - Status: succeeded', 'sistema', '2026-02-19 20:52:23'),
(34, 32, 'solicitada', 'aprovada', 'confirmo o defeito', 'anunciante', '2026-02-19 20:55:44'),
(35, 32, 'aprovada', 'produto_enviado', 'Código interno de envio: DEVENV20260219215624907', 'cliente', '2026-02-19 20:56:24'),
(36, 32, 'produto_enviado', 'produto_recebido', 'perfeito | Código de envio validado: DEVENV20260219215624907', 'anunciante', '2026-02-19 20:58:23'),
(37, 32, 'produto_recebido', 'reembolsada', 'Reembolso Stripe ID: re_3T2cDXBgsjq4eGsl0VH37Elt - Status: succeeded', 'sistema', '2026-02-19 20:59:16');
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `encomendas`
--

DROP TABLE IF EXISTS `encomendas`;
CREATE TABLE IF NOT EXISTS `encomendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_encomenda` varchar(50) DEFAULT NULL,
  `payment_id` varchar(100) DEFAULT NULL COMMENT 'ID do pagamento Stripe',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Método de pagamento',
  `payment_status` varchar(50) DEFAULT 'paid' COMMENT 'Status do pagamento',
  `cliente_id` int(11) DEFAULT NULL,
  `transportadora_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `TipoProdutoNome` int(11) DEFAULT NULL,
  `data_envio` date DEFAULT NULL,
  `morada` varchar(250) DEFAULT NULL,
  `tipo_entrega` enum('domicilio','ponto_recolha') DEFAULT 'domicilio',
  `ponto_recolha_id` int(11) DEFAULT NULL,
  `morada_completa` text DEFAULT NULL,
  `nome_destinatario` varchar(255) DEFAULT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Pendente',
  `plano_rastreio` varchar(250) DEFAULT NULL,
  `codigo_rastreio` varchar(100) DEFAULT NULL COMMENT 'Código de rastreamento da transportadora',
  `nome_ponto_recolha` varchar(255) DEFAULT NULL COMMENT 'Nome do ponto de recolha',
  `morada_ponto_recolha` text DEFAULT NULL COMMENT 'Morada completa do ponto de recolha',
  `codigo_confirmacao_recepcao` varchar(20) DEFAULT NULL,
  `data_confirmacao_recepcao` datetime DEFAULT NULL,
  `ip_confirmacao` varchar(50) DEFAULT NULL,
  `prazo_estimado_entrega` date DEFAULT NULL,
  `lembrete_confirmacao_enviado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_encomenda` (`codigo_encomenda`),
  UNIQUE KEY `codigo_confirmacao_recepcao` (`codigo_confirmacao_recepcao`),
  KEY `cliente_id` (`cliente_id`),
  KEY `produto_id` (`produto_id`),
  KEY `transportadora_id` (`transportadora_id`),
  KEY `TipoProdutoNome` (`TipoProdutoNome`),
  KEY `idx_ponto_recolha` (`ponto_recolha_id`),
  KEY `idx_tipo_entrega` (`tipo_entrega`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `encomendas`
--

INSERT INTO `encomendas` (`id`, `codigo_encomenda`, `payment_id`, `payment_method`, `payment_status`, `cliente_id`, `transportadora_id`, `produto_id`, `TipoProdutoNome`, `data_envio`, `morada`, `tipo_entrega`, `ponto_recolha_id`, `morada_completa`, `nome_destinatario`, `estado`, `plano_rastreio`, `codigo_rastreio`, `nome_ponto_recolha`, `morada_ponto_recolha`, `codigo_confirmacao_recepcao`, `data_confirmacao_recepcao`, `ip_confirmacao`, `prazo_estimado_entrega`, `lembrete_confirmacao_enviado`) VALUES
(1, 'WG12345', NULL, NULL, 'paid', 2, 1, 1, 1, '2025-12-06', 'Rua das Flores 15, Lisboa', 'domicilio', NULL, 'Rua das Flores 15, 1200-001 Lisboa', NULL, 'Pendente', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(2, 'WG12346', NULL, NULL, 'paid', 2, 1, 2, 1, '2025-12-06', 'Av. do Mar 20, Porto', 'domicilio', NULL, NULL, NULL, 'Pendente', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(3, 'WG12347', NULL, NULL, 'paid', 7, 2, 3, 2, '2025-12-06', 'Rua Central 12, Coimbra', 'domicilio', NULL, NULL, NULL, 'Pendente', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4, 'WG12352', NULL, NULL, 'paid', 2, 1, 4, 3, '2025-12-06', 'Rua Nova 8, Braga', 'domicilio', NULL, NULL, NULL, 'Pendente', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(5, 'WG12353', NULL, NULL, 'paid', 7, 1, 5, 1, '2025-12-06', 'Av. das Oliveiras 30, Set??bal', 'domicilio', NULL, NULL, NULL, 'Entregue', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(6, 'WG12348', NULL, NULL, 'paid', 2, 1, 6, 5, '2025-12-06', 'Rua Verde 5, Faro', 'domicilio', NULL, NULL, NULL, 'Entregue', 'Avançado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(7, 'WG12349', NULL, NULL, 'paid', 7, 2, 7, 4, '2025-12-06', 'Rua do Sol 40, Lisboa', 'domicilio', NULL, NULL, NULL, 'Pendente', 'Avançado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(8, 'WG12354', NULL, NULL, 'paid', 2, 2, 1, 1, '2025-12-06', 'Rua das Amendoeiras 22, Coimbra', 'domicilio', NULL, NULL, NULL, 'Entregue', 'Avançado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(9, 'WG12350', NULL, NULL, 'paid', 2, 1, 2, 1, '2025-12-06', 'Av. dos Descobrimentos 50, Aveiro', 'domicilio', NULL, NULL, NULL, 'Entregue', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(10, 'WG12351', NULL, NULL, 'paid', 7, 2, 3, 2, '2025-12-06', 'Rua dos Cedros 18, Leiria', 'domicilio', NULL, NULL, NULL, 'Cancelada', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(11, 'WG12355', NULL, NULL, 'paid', 7, 1, 4, 3, '2025-12-06', 'Rua da Alegria 7, Viseu', 'domicilio', NULL, NULL, NULL, 'Pendente', 'Básico', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(12, 'WG35643', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(13, 'WG92289', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(14, 'WG39257', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(15, 'WG84712', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(16, 'WG85659', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(17, 'WG19273', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(18, 'WG82204', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(19, 'WG17091', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, 'Joao, Rua, Número, Código Postal, Cidade', 'Joao', 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(20, 'WG92781', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(21, 'WG78669', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(22, 'WG45929', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(23, 'WG52743', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(24, 'WG30838', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(25, 'WG79581', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(26, 'WG54248', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(27, 'WG59915', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(28, 'WG48750', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(29, 'WG39061', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(30, 'WG39162', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(31, 'WG64608', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Processando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(32, 'WG65461', NULL, NULL, 'paid', 2, 2, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(33, 'WG81782', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Entregue', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(34, 'WG75227', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Entregue', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(35, 'WG40268', NULL, NULL, 'paid', 2, 1, 1, NULL, '2026-01-24', 'Rua, Número, Código Postal, Cidade', 'domicilio', NULL, NULL, NULL, 'Entregue', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(47, 'WG17695262834326043ef', 'pi_3SuDRGBgsjq4eGsl0BRFdIic', 'card', 'paid', 2, 1, 3, NULL, '2026-01-27', 'Rua Teste 123 Ap 4, 1000-001 Lisboa, Lisboa', 'domicilio', NULL, 'Joao Silva, Rua Teste 123 Ap 4, 1000-001 Lisboa, Lisboa', 'Joao Silva', 'Cancelada', 'Básico', NULL, NULL, NULL, 'CONF-C11411', NULL, NULL, '2026-01-31', 0),
(48, 'WG1771102596464973188', 'pi_3T0psYBgsjq4eGsl1fbyaN1b', 'card', 'paid', 2, 1, 26, NULL, '2026-02-14', 'Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'domicilio', 0, 'João Santos, Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'João Santos', 'Cancelada', 'Básico', NULL, '', '', 'CONF-7B40EE', NULL, NULL, '2026-02-18', 0),
(49, 'WG1771102689568262c24', 'pi_3T0pujBgsjq4eGsl1jZG37Ll', 'card', 'paid', 2, 1, 26, NULL, '2026-02-14', 'Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'domicilio', 0, 'João Santos, Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'João Santos', 'Cancelada', 'Básico', NULL, '', '', 'CONF-E55785', NULL, NULL, '2026-02-18', 0),
(50, 'WG1771103078533307912', 'pi_3T0q10Bgsjq4eGsl09sB9br7', 'card', 'paid', 2, 1, 26, NULL, '2026-02-14', 'Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'domicilio', 0, 'João Santos, Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'João Santos', 'Cancelada', 'Básico', NULL, '', '', 'CONF-0032A6', NULL, NULL, '2026-02-18', 0),
(51, 'WG1771103458896966690', 'pi_3T0q78Bgsjq4eGsl1htYiO1x', 'card', 'paid', 2, 1, 26, NULL, '2026-02-14', 'Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'domicilio', 0, 'João Santos, Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'João Santos', 'Cancelada', 'Básico', NULL, '', '', 'CONF-D699C1', NULL, NULL, '2026-02-18', 0),
(52, 'WGTEST-20260214232205-A', 'pi_test_20260214232205_A', 'card', 'paid', 20, 1, 26, NULL, '2026-02-14', 'Rua Teste, Lisboa', 'domicilio', NULL, 'Rua Teste, Lisboa', 'Teste', 'Cancelada', 'Básico', NULL, NULL, NULL, 'CONFE03765', NULL, NULL, '2026-02-14', 0),
(53, 'WGTEST-20260214232205-B', 'pi_test_20260214232205_B', 'card', 'paid', 20, 1, 26, NULL, '2026-02-14', 'Rua Teste, Lisboa', 'domicilio', NULL, 'Rua Teste, Lisboa', 'Teste', 'Enviado', 'Básico', NULL, NULL, NULL, 'CONFE9565C', NULL, NULL, '2026-02-14', 0),
(54, 'WGTEST-20260214232357-A', 'pi_test_20260214232357_A', 'card', 'paid', 20, 1, 26, NULL, '2026-02-14', 'Rua Teste, Lisboa', 'domicilio', NULL, 'Rua Teste, Lisboa', 'Teste', 'Cancelada', 'Básico', NULL, NULL, NULL, 'CONF5AF9FD', NULL, NULL, '2026-02-14', 0),
(55, 'WGTEST-20260214232357-B', 'pi_test_20260214232357_B', 'card', 'paid', 20, 1, 26, NULL, '2026-02-14', 'Rua Teste, Lisboa', 'domicilio', NULL, 'Rua Teste, Lisboa', 'Teste', 'Devolvido', 'Básico', NULL, NULL, NULL, 'CONFF7EFF7', '2026-02-14 22:23:57', '127.0.0.1', '2026-02-14', 0),
(56, 'WG177120167534269991e', 'pi_3T1FeaBgsjq4eGsl1L7Ek09k', 'card', 'paid', 3, 1, 9, NULL, '2026-02-16', 'rua dqui ali , 7005-287 Évora, Évora', 'domicilio', 0, 'Maria Santos, rua dqui ali , 7005-287 Évora, Évora', 'Maria Santos', 'Processando', 'Básico', NULL, '', '', 'CONF-889927', NULL, NULL, '2026-02-20', 0),
(57, 'WG1771207408625840de8', 'pi_3T1H9kBgsjq4eGsl1bFx0WqB', 'card', 'paid', 2, 1, 2, NULL, '2026-02-16', 'rua daqui ali 232, 7005-287 Évora, Évora', 'domicilio', 0, 'Joao santos, rua daqui ali 232, 7005-287 Évora, Évora', 'Joao santos', 'Cancelada', 'Básico', NULL, '', '', 'CONF-3400B3', NULL, NULL, '2026-02-20', 0),
(58, 'WG1771296653286836de3', 'pi_3T1eNCBgsjq4eGsl1q0nhI7m', 'card', 'paid', 2, 1, 17, NULL, '2026-02-17', 'Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'domicilio', 0, 'João Santos, Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'João Santos', 'Entregue', 'Básico', NULL, '', '', 'CONF-129324', '2026-02-19 17:20:35', '::1', '2026-02-21', 0),
(59, 'WG177152669362087bd7b', 'pi_3T2cDXBgsjq4eGsl0S4IpSsV', 'card', 'paid', 24, 2, 22, NULL, '2026-02-19', 'Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'ponto_recolha', 51, 'João Santos, Rua Alexandre Rosado N° 28 3\'D 321, 7005-287 Évora, évora', 'João Santos', 'Entregue', 'Avançado', NULL, 'DPD Évora', 'Rua Vasco da Gama, 7005-841 Évora', 'CONF-D4B5CD', '2026-02-19 19:06:29', '::1', '2026-02-21', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `data_adicao` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito` (`cliente_id`,`produto_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_data` (`data_adicao`),
  KEY `idx_cliente_data` (`cliente_id`,`data_adicao`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `favoritos`
--

INSERT INTO `favoritos` (`id`, `cliente_id`, `produto_id`, `data_adicao`) VALUES
(11, 2, 26, '2026-02-16 01:51:15'),
(12, 2, 25, '2026-02-16 01:51:20'),
(13, 2, 17, '2026-02-17 00:39:15'),
(16, 2, 20, '2026-02-17 16:19:30');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) DEFAULT NULL,
  `descricao` varchar(250) DEFAULT NULL,
  `tipo_produtos_id` int(11) NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `telefone` varchar(250) DEFAULT NULL,
  `morada` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_produtos_id` (`tipo_produtos_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `nome`, `descricao`, `tipo_produtos_id`, `email`, `telefone`, `morada`) VALUES
(1, 'ModaCenter', 'Fornecedor de vestuário masculino e feminino', 1, 'contacto@modacenter.com', '912345678', 'Rua das Flores 12, Lisboa'),
(2, 'FootWorld', 'Fornecedor de sapatilhas e calçado desportivo', 2, 'info@footworld.pt', '934567890', 'Av. Central 45, Porto'),
(3, 'AccessoriArt', 'Fornecedor de bijuteria e acessórios de moda', 3, 'suporte@accessoriart.com', '965432187', 'Rua do Comércio 22, Braga'),
(4, 'BelleCosmetics', 'Fornecedor de produtos de beleza e skincare', 4, 'geral@bellecosmetics.pt', '938221765', 'Rua Rosa 9, Coimbra'),
(5, 'MixSupplies', 'Fornecedor geral de artigos variados', 5, 'info@mixsupplies.com', '926553421', 'Av. Nova 88, Faro');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE IF NOT EXISTS `gastos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anunciante_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_registo` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `anunciante_id` (`anunciante_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gastos`
--

INSERT INTO `gastos` (`id`, `anunciante_id`, `valor`, `descricao`, `data_registo`) VALUES
(1, 3, 50.00, 'Publicidade online', '2025-12-06'),
(4, 1, 23.00, 'asd', '2026-02-09'),
(5, 1, 32.00, 'asd', '2026-02-19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_devolucoes`
--

DROP TABLE IF EXISTS `historico_devolucoes`;
CREATE TABLE IF NOT EXISTS `historico_devolucoes` (
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

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_produtos`
--

DROP TABLE IF EXISTS `historico_produtos`;
CREATE TABLE IF NOT EXISTS `historico_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) DEFAULT NULL,
  `estado_encomenda` varchar(250) DEFAULT 'Pendente',
  `descricao` text DEFAULT NULL,
  `data_atualizacao` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `encomenda_id` (`encomenda_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `historico_produtos`
--

INSERT INTO `historico_produtos` (`id`, `encomenda_id`, `estado_encomenda`, `descricao`, `data_atualizacao`) VALUES
(1, 2, 'Entregue', 'Produto entregue ao cliente.', '2025-12-06'),
(2, 3, 'Em trânsito', 'Produto enviado pela transportadora.', '2025-12-06'),
(3, 4, 'Entregue', 'Entrega concluída.', '2025-12-06'),
(4, 5, 'Pendente', 'Aguardando envio.', '2025-12-06'),
(5, 6, 'Entregue', 'Venda finalizada com sucesso.', '2025-12-06'),
(6, 7, 'Cancelada', 'Encomenda cancelada pelo cliente.', '2025-12-06'),
(7, 8, 'Em trânsito', 'Produto saiu do armazém.', '2025-12-06'),
(8, 9, 'Entregue', 'Cliente recebeu o produto.', '2025-12-06'),
(9, 10, 'Entregue', 'Envio concluído.', '2025-12-06'),
(10, 11, 'Pendente', 'Aguardando confirmação de pagamento.', '2025-12-06'),
(11, 1, 'Processando', 'Status alterado para: Processando', '2026-01-11'),
(12, 2, 'Cancelado', 'Status alterado para: Cancelado', '2026-01-11'),
(13, 1, 'Pendente', 'Status alterado para: Pendente', '2026-01-11'),
(26, 47, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-01-27'),
(27, 48, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-14'),
(28, 49, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-14'),
(29, 50, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-14'),
(30, 51, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-14'),
(31, 52, 'Pendente', 'Teste CLI - encomenda criada', '2026-02-14'),
(32, 53, 'Enviado', 'Teste CLI - encomenda enviada', '2026-02-14'),
(33, 54, 'Pendente', 'Teste CLI - encomenda criada', '2026-02-14'),
(34, 55, 'Enviado', 'Teste CLI - encomenda enviada', '2026-02-14'),
(35, 55, 'Entregue', 'Entrega confirmada pelo cliente', '2026-02-14'),
(36, 56, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-16'),
(37, 57, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-16'),
(38, 56, 'Cancelado', 'Status alterado para: Cancelado', '2026-02-16'),
(39, 56, 'Pendente', 'Status alterado para: Pendente', '2026-02-16'),
(40, 56, 'Cancelado', 'Status alterado para: Cancelado', '2026-02-16'),
(41, 56, 'Pendente', 'Status alterado para: Pendente', '2026-02-16'),
(42, 56, 'Processando', 'Status alterado para: Processando', '2026-02-16'),
(43, 58, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-17'),
(44, 58, 'Processando', 'Status alterado para: Processando', '2026-02-17'),
(45, 58, 'Pendente', 'Status alterado para: Pendente', '2026-02-17'),
(46, 58, 'Processando', 'Teste proc', '2026-02-19'),
(47, 58, 'Enviado', 'Teste envio auto - Código de confirmação de receção: CONF-129324', '2026-02-19'),
(48, 58, 'Entregue', 'Entrega confirmada pelo cliente', '2026-02-19'),
(49, 59, 'Pendente', 'Encomenda criada - Aguardando confirmação', '2026-02-19'),
(50, 59, 'Processando', 'Status alterado para: Processando', '2026-02-19'),
(51, 59, 'Enviado', 'Status alterado para: Enviado - Código de confirmação de receção: CONF-D4B5CD', '2026-02-19'),
(52, 59, 'Entregue', 'Entrega confirmada pelo cliente', '2026-02-19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs_acesso`
--

DROP TABLE IF EXISTS `logs_acesso`;
CREATE TABLE IF NOT EXISTS `logs_acesso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilizador_id` int(11) NOT NULL,
  `acao` enum('login','logout') NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `utilizador_id` (`utilizador_id`),
  KEY `data_hora` (`data_hora`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `logs_acesso`
--

INSERT INTO `logs_acesso` (`id`, `utilizador_id`, `acao`, `email`, `data_hora`) VALUES
(1, 2, 'login', 'joao@wegreen.pt', '2026-01-25 17:07:04'),
(2, 3, 'login', 'maria@wegreen.pt', '2026-01-25 17:14:18'),
(3, 1, 'login', 'admin@wegreen.pt', '2026-01-25 17:28:08'),
(4, 1, 'logout', 'admin@wegreen.pt', '2026-01-25 17:35:17'),
(5, 2, 'login', 'joao@wegreen.pt', '2026-01-25 17:35:27'),
(6, 2, 'login', 'joao@wegreen.pt', '2026-01-25 18:20:49'),
(7, 3, 'login', 'maria@wegreen.pt', '2026-01-25 20:35:15'),
(8, 3, 'login', 'maria@wegreen.pt', '2026-01-25 22:31:28'),
(9, 3, 'login', 'maria@wegreen.pt', '2026-01-25 22:46:23'),
(10, 3, 'login', 'maria@wegreen.pt', '2026-01-25 22:47:31'),
(11, 3, 'login', 'maria@wegreen.pt', '2026-01-26 13:53:53'),
(12, 3, 'login', 'maria@wegreen.pt', '2026-01-26 15:59:55'),
(13, 2, 'login', 'joao@wegreen.pt', '2026-01-26 16:02:20'),
(14, 3, 'login', 'maria@wegreen.pt', '2026-01-26 16:03:15'),
(15, 3, 'login', 'maria@wegreen.pt', '2026-01-26 20:07:24'),
(16, 2, 'login', 'joao@wegreen.pt', '2026-01-26 20:20:38'),
(17, 2, 'login', 'joao@wegreen.pt', '2026-01-26 20:32:13'),
(18, 1, 'login', 'admin@wegreen.pt', '2026-01-26 20:39:34'),
(19, 2, 'login', 'joao@wegreen.pt', '2026-01-27 14:16:57'),
(20, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 15:37:15'),
(21, 2, 'login', 'joao@wegreen.pt', '2026-01-27 15:37:26'),
(22, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 15:38:07'),
(23, 2, 'login', 'joao@wegreen.pt', '2026-01-27 15:39:54'),
(24, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 15:39:59'),
(25, 2, 'login', 'joao@wegreen.pt', '2026-01-27 15:40:13'),
(26, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 15:40:22'),
(27, 3, 'login', 'maria@wegreen.pt', '2026-01-27 15:40:30'),
(28, 3, 'logout', 'maria@wegreen.pt', '2026-01-27 16:05:36'),
(29, 1, 'login', 'admin@wegreen.pt', '2026-01-27 16:06:11'),
(30, 1, 'login', 'admin@wegreen.pt', '2026-01-27 18:29:13'),
(31, 1, 'logout', 'admin@wegreen.pt', '2026-01-27 18:43:20'),
(32, 2, 'login', 'joao@wegreen.pt', '2026-01-27 18:43:34'),
(33, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 18:44:02'),
(34, 3, 'login', 'maria@wegreen.pt', '2026-01-27 18:44:19'),
(35, 1, 'login', 'admin@wegreen.pt', '2026-01-27 19:03:07'),
(36, 1, 'logout', 'admin@wegreen.pt', '2026-01-27 19:06:12'),
(37, 3, 'login', 'maria@wegreen.pt', '2026-01-27 19:53:16'),
(38, 3, 'logout', 'maria@wegreen.pt', '2026-01-27 20:27:33'),
(39, 2, 'login', 'joao@wegreen.pt', '2026-01-27 20:27:45'),
(40, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 20:30:04'),
(41, 3, 'login', 'maria@wegreen.pt', '2026-01-27 20:56:22'),
(42, 3, 'logout', 'maria@wegreen.pt', '2026-01-27 21:01:10'),
(43, 2, 'login', 'joao@wegreen.pt', '2026-01-27 21:01:17'),
(44, 2, 'logout', 'joao@wegreen.pt', '2026-01-27 21:03:33'),
(45, 3, 'login', 'maria@wegreen.pt', '2026-01-27 21:03:58'),
(46, 1, 'login', 'admin@wegreen.pt', '2026-01-29 17:45:52'),
(47, 1, 'logout', 'admin@wegreen.pt', '2026-01-29 18:34:31'),
(48, 1, 'login', 'admin@wegreen.pt', '2026-01-29 18:34:49'),
(49, 1, 'logout', 'admin@wegreen.pt', '2026-01-29 19:39:58'),
(50, 1, 'login', 'admin@wegreen.pt', '2026-01-29 19:40:15'),
(51, 1, 'login', 'admin@wegreen.pt', '2026-01-29 19:40:15'),
(52, 1, 'logout', 'admin@wegreen.pt', '2026-01-29 19:41:25'),
(53, 1, 'login', 'admin@wegreen.pt', '2026-01-29 19:43:17'),
(54, 1, 'logout', 'admin@wegreen.pt', '2026-01-29 19:43:23'),
(55, 1, 'login', 'admin@wegreen.pt', '2026-01-29 19:43:47'),
(56, 1, 'logout', 'admin@wegreen.pt', '2026-01-29 19:43:59'),
(57, 2, 'login', 'joao@wegreen.pt', '2026-01-29 19:44:15'),
(58, 2, 'logout', 'joao@wegreen.pt', '2026-01-29 19:44:32'),
(59, 3, 'login', 'maria@wegreen.pt', '2026-01-29 19:44:39'),
(60, 3, 'logout', 'maria@wegreen.pt', '2026-01-29 19:47:24'),
(61, 1, 'login', 'admin@wegreen.pt', '2026-01-29 19:47:37'),
(62, 1, 'login', 'admin@wegreen.pt', '2026-02-02 18:36:03'),
(63, 3, 'login', 'maria@wegreen.pt', '2026-02-02 19:23:21'),
(64, 2, 'login', 'joao@wegreen.pt', '2026-02-03 20:38:54'),
(65, 2, 'logout', 'joao@wegreen.pt', '2026-02-03 20:42:30'),
(66, 2, 'login', 'joao@wegreen.pt', '2026-02-03 21:02:01'),
(67, 2, 'logout', 'joao@wegreen.pt', '2026-02-03 21:06:28'),
(68, 2, 'login', 'joao@wegreen.pt', '2026-02-03 21:06:49'),
(69, 2, 'login', 'joao@wegreen.pt', '2026-02-10 22:49:17'),
(70, 2, 'login', 'joao@wegreen.pt', '2026-02-14 20:46:23'),
(71, 2, 'logout', 'joao@wegreen.pt', '2026-02-14 20:49:28'),
(72, 2, 'login', 'joao@wegreen.pt', '2026-02-14 20:49:48'),
(73, 2, 'logout', 'joao@wegreen.pt', '2026-02-14 21:49:04'),
(74, 2, 'login', 'joao@wegreen.pt', '2026-02-14 21:52:18'),
(75, 2, 'logout', 'joao@wegreen.pt', '2026-02-14 22:25:18'),
(76, 3, 'login', 'maria@wegreen.pt', '2026-02-14 22:25:25'),
(77, 2, 'login', 'joao@wegreen.pt', '2026-02-15 21:53:24'),
(78, 2, 'logout', 'joao@wegreen.pt', '2026-02-15 21:53:35'),
(79, 3, 'login', 'maria@wegreen.pt', '2026-02-15 21:53:43'),
(80, 3, 'login', 'maria@wegreen.pt', '2026-02-16 00:28:17'),
(81, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 00:32:18'),
(82, 2, 'login', 'joao@wegreen.pt', '2026-02-16 00:32:27'),
(83, 2, 'logout', 'joao@wegreen.pt', '2026-02-16 01:59:46'),
(84, 3, 'login', 'maria@wegreen.pt', '2026-02-16 01:59:56'),
(85, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 02:01:35'),
(86, 2, 'login', 'joao@wegreen.pt', '2026-02-16 02:02:37'),
(87, 2, 'logout', 'joao@wegreen.pt', '2026-02-16 02:04:07'),
(88, 3, 'login', 'maria@wegreen.pt', '2026-02-16 02:04:22'),
(89, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 02:37:23'),
(90, 1, 'login', 'admin@wegreen.pt', '2026-02-16 02:37:39'),
(91, 1, 'logout', 'admin@wegreen.pt', '2026-02-16 02:38:27'),
(92, 3, 'login', 'maria@wegreen.pt', '2026-02-16 12:17:35'),
(93, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 17:12:11'),
(94, 3, 'login', 'maria@wegreen.pt', '2026-02-16 17:29:22'),
(95, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 17:34:13'),
(96, 1, 'login', 'admin@wegreen.pt', '2026-02-16 17:34:23'),
(97, 1, 'logout', 'admin@wegreen.pt', '2026-02-16 23:31:06'),
(98, 3, 'login', 'maria@wegreen.pt', '2026-02-16 23:49:45'),
(99, 3, 'login', 'maria@wegreen.pt', '2026-02-16 23:49:45'),
(100, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 23:51:20'),
(101, 1, 'login', 'admin@wegreen.pt', '2026-02-16 23:51:28'),
(102, 1, 'logout', 'admin@wegreen.pt', '2026-02-16 23:53:19'),
(103, 3, 'login', 'maria@wegreen.pt', '2026-02-16 23:53:27'),
(104, 3, 'logout', 'maria@wegreen.pt', '2026-02-16 23:54:05'),
(105, 1, 'login', 'admin@wegreen.pt', '2026-02-16 23:54:18'),
(106, 1, 'logout', 'admin@wegreen.pt', '2026-02-17 00:04:57'),
(107, 2, 'login', 'joao@wegreen.pt', '2026-02-17 00:27:31'),
(108, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 01:26:31'),
(109, 3, 'login', 'maria@wegreen.pt', '2026-02-17 01:26:40'),
(110, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 01:53:50'),
(111, 2, 'login', 'joao@wegreen.pt', '2026-02-17 01:54:00'),
(112, 2, 'login', 'joao@wegreen.pt', '2026-02-17 02:18:38'),
(113, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 02:19:26'),
(114, 2, 'login', 'joao@wegreen.pt', '2026-02-17 02:19:36'),
(115, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 02:19:49'),
(116, 3, 'login', 'maria@wegreen.pt', '2026-02-17 02:19:56'),
(117, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 02:20:43'),
(118, 1, 'login', 'admin@wegreen.pt', '2026-02-17 02:20:57'),
(119, 1, 'login', 'admin@wegreen.pt', '2026-02-17 02:21:10'),
(120, 3, 'login', 'maria@wegreen.pt', '2026-02-17 02:29:18'),
(121, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 02:36:51'),
(122, 2, 'login', 'joao@wegreen.pt', '2026-02-17 02:36:59'),
(123, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 02:58:43'),
(124, 3, 'login', 'maria@wegreen.pt', '2026-02-17 02:58:58'),
(125, 3, 'login', 'maria@wegreen.pt', '2026-02-17 03:24:32'),
(126, 3, 'login', 'maria@wegreen.pt', '2026-02-17 12:23:42'),
(127, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 12:39:39'),
(128, 3, 'login', 'maria@wegreen.pt', '2026-02-17 12:40:07'),
(129, 3, 'login', 'maria@wegreen.pt', '2026-02-17 12:41:46'),
(130, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 15:36:07'),
(131, 2, 'login', 'joao@wegreen.pt', '2026-02-17 15:36:16'),
(132, 2, 'login', 'joao@wegreen.pt', '2026-02-17 15:50:50'),
(133, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 15:51:23'),
(134, 3, 'login', 'maria@wegreen.pt', '2026-02-17 15:51:32'),
(135, 2, 'login', 'joao@wegreen.pt', '2026-02-17 15:51:53'),
(136, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 16:09:03'),
(137, 2, 'login', 'joao@wegreen.pt', '2026-02-17 16:13:59'),
(138, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 16:25:17'),
(139, 2, 'login', 'joao@wegreen.pt', '2026-02-17 16:29:30'),
(140, 3, 'login', 'maria@wegreen.pt', '2026-02-17 17:30:09'),
(141, 3, 'login', 'maria@wegreen.pt', '2026-02-17 17:30:38'),
(142, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 17:33:51'),
(143, 2, 'login', 'joao@wegreen.pt', '2026-02-17 17:33:59'),
(144, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 17:45:08'),
(145, 2, 'login', 'joao@wegreen.pt', '2026-02-17 17:46:54'),
(146, 2, 'logout', 'joao@wegreen.pt', '2026-02-17 18:08:32'),
(147, 1, 'login', 'admin@wegreen.pt', '2026-02-17 18:08:40'),
(148, 1, 'logout', 'admin@wegreen.pt', '2026-02-17 18:15:35'),
(149, 3, 'login', 'maria@wegreen.pt', '2026-02-17 18:16:07'),
(150, 3, 'logout', 'maria@wegreen.pt', '2026-02-17 18:30:42'),
(151, 1, 'login', 'admin@wegreen.pt', '2026-02-18 22:05:03'),
(152, 11, 'login', 'jmssgames@gmail.com', '2026-02-19 14:58:24'),
(153, 11, 'login', 'jmssgames@gmail.com', '2026-02-19 15:03:11'),
(154, 21, 'logout', 'jmssgames@gmail.com', '2026-02-19 15:04:20'),
(155, 11, 'login', 'jmssgames@gmail.com', '2026-02-19 15:04:40'),
(156, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 15:12:23'),
(157, 2, 'login', 'joao@wegreen.pt', '2026-02-19 15:17:53'),
(158, 2, 'login', 'joao@wegreen.pt', '2026-02-19 15:19:25'),
(159, 2, 'login', 'joao@wegreen.pt', '2026-02-19 15:20:10'),
(160, 3, 'login', 'maria@wegreen.pt', '2026-02-19 15:25:49'),
(161, 3, 'login', 'maria@wegreen.pt', '2026-02-19 15:28:40'),
(162, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 15:39:50'),
(163, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 15:44:15'),
(164, 24, 'logout', 'jmssgames@gmail.com', '2026-02-19 15:45:53'),
(165, 1, 'login', 'admin@wegreen.pt', '2026-02-19 15:46:00'),
(166, 1, 'login', 'admin@wegreen.pt', '2026-02-19 16:22:17'),
(167, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 16:52:51'),
(168, 2, 'login', 'joao@wegreen.pt', '2026-02-19 16:53:30'),
(169, 2, 'logout', 'joao@wegreen.pt', '2026-02-19 16:57:36'),
(170, 7, 'login', 'mango@wegreen.pt', '2026-02-19 16:59:01'),
(171, 7, 'logout', 'mango@wegreen.pt', '2026-02-19 16:59:34'),
(172, 1, 'login', 'admin@wegreen.pt', '2026-02-19 16:59:46'),
(173, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 17:19:46'),
(174, 2, 'login', 'joao@wegreen.pt', '2026-02-19 17:19:53'),
(175, 2, 'logout', 'joao@wegreen.pt', '2026-02-19 17:29:38'),
(176, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 17:29:53'),
(177, 24, 'logout', 'jmssgames@gmail.com', '2026-02-19 17:30:49'),
(178, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 17:31:00'),
(179, 24, 'logout', 'jmssgames@gmail.com', '2026-02-19 17:36:55'),
(180, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 17:40:35'),
(181, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 17:41:14'),
(182, 24, 'logout', 'jmssgames@gmail.com', '2026-02-19 17:41:37'),
(183, 3, 'login', 'maria@wegreen.pt', '2026-02-19 17:41:49'),
(184, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 18:26:24'),
(185, 2, 'login', 'joao@wegreen.pt', '2026-02-19 18:27:53'),
(186, 2, 'logout', 'joao@wegreen.pt', '2026-02-19 18:43:33'),
(187, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 18:43:56'),
(188, 3, 'login', 'maria@wegreen.pt', '2026-02-19 18:46:01'),
(189, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 19:24:43'),
(190, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 19:24:52'),
(191, 24, 'logout', 'jmssgames@gmail.com', '2026-02-19 19:55:23'),
(192, 3, 'login', 'maria@wegreen.pt', '2026-02-19 19:55:40'),
(193, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 20:07:54'),
(194, 1, 'login', 'admin@wegreen.pt', '2026-02-19 20:08:09'),
(195, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 20:09:59'),
(196, 3, 'login', 'maria@wegreen.pt', '2026-02-19 20:10:11'),
(197, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 20:21:31'),
(198, 1, 'login', 'admin@wegreen.pt', '2026-02-19 20:21:38'),
(199, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 20:22:18'),
(200, 3, 'login', 'maria@wegreen.pt', '2026-02-19 20:22:32'),
(201, 24, 'logout', 'jmssgames@gmail.com', '2026-02-19 21:00:02'),
(202, 24, 'login', 'jmssgames@gmail.com', '2026-02-19 21:00:29'),
(203, 32, 'logout', 'jmssgames@gmail.com', '2026-02-19 21:01:09'),
(204, 3, 'login', 'maria@wegreen.pt', '2026-02-19 21:01:18'),
(205, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 21:07:15'),
(206, 1, 'login', 'admin@wegreen.pt', '2026-02-19 21:07:29'),
(207, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 21:31:28'),
(208, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 21:55:59'),
(209, 1, 'login', 'admin@wegreen.pt', '2026-02-19 21:56:12'),
(210, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 22:00:41'),
(211, 3, 'login', 'maria@wegreen.pt', '2026-02-19 22:03:47'),
(212, 2, 'login', 'joao@wegreen.pt', '2026-02-19 22:54:58'),
(213, 2, 'logout', 'joao@wegreen.pt', '2026-02-19 23:02:32'),
(214, 3, 'login', 'maria@wegreen.pt', '2026-02-19 23:02:47'),
(215, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 23:03:10'),
(216, 2, 'login', 'joao@wegreen.pt', '2026-02-19 23:03:55'),
(217, 2, 'logout', 'joao@wegreen.pt', '2026-02-19 23:07:09'),
(218, 3, 'login', 'maria@wegreen.pt', '2026-02-19 23:07:30'),
(219, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 23:08:47'),
(220, 1, 'login', 'admin@wegreen.pt', '2026-02-19 23:09:09'),
(221, 1, 'logout', 'admin@wegreen.pt', '2026-02-19 23:17:29'),
(222, 3, 'login', 'maria@wegreen.pt', '2026-02-19 23:17:56'),
(223, 3, 'logout', 'maria@wegreen.pt', '2026-02-19 23:22:54'),
(224, 1, 'login', 'admin@wegreen.pt', '2026-02-19 23:23:14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens`
--

DROP TABLE IF EXISTS `mensagens`;
CREATE TABLE IF NOT EXISTS `mensagens` (
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
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `remetente_id`, `destinatario_id`, `produto_id`, `mensagem`, `created_at`, `updated_at`) VALUES
(1, 3, 2, NULL, 'Olá João! O seu pedido já foi enviado.', '2025-12-06 19:18:55', '2026-02-19 22:48:50'),
(2, 1, 3, 5, 'Olá eu sou a Luiza', '2025-12-06 19:18:55', '2026-02-19 22:48:50');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagensadmin`
--

DROP TABLE IF EXISTS `mensagensadmin`;
CREATE TABLE IF NOT EXISTS `mensagensadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remetente_id` int(11) DEFAULT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `anexo` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `remetente_id` (`remetente_id`),
  KEY `destinatario_id` (`destinatario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `mensagensadmin`
--

INSERT INTO `mensagensadmin` (`id`, `remetente_id`, `destinatario_id`, `mensagem`, `anexo`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Olá João! O seu pedido já foi enviado.', NULL, '2025-12-16 22:56:09', '2026-02-19 22:48:50'),
(2, 3, 2, 'Olá! Bem-vindo à WeGreen. Como posso ajudar?', NULL, '2026-01-17 10:00:00', '2026-01-18 21:25:54'),
(3, 2, 3, 'Olá! Gostaria de saber mais sobre os produtos eco-friendly.', NULL, '2026-01-17 10:05:00', '2026-01-18 21:25:54'),
(4, 3, 2, 'Temos uma grande variedade de produtos sustentáveis. Que tipo procura?', NULL, '2026-01-17 10:10:00', '2026-01-18 21:25:54'),
(5, 2, 3, 'Estou interessado em roupa de algodão orgânico.', NULL, '2026-01-17 10:15:00', '2026-01-18 21:25:54'),
(6, 3, 2, 'Perfeito! Veja nossa coleção de roupa orgânica. Temos peças certificadas.', NULL, '2026-01-17 10:20:00', '2026-01-18 21:25:54'),
(7, 1, 2, 'Olá! Em que posso ajudar?', NULL, '2026-01-17 14:00:00', '2026-01-18 21:26:28'),
(8, 2, 1, 'Olá! Gostaria de saber sobre o estado da minha encomenda.', NULL, '2026-01-17 14:05:00', '2026-01-18 21:26:28'),
(9, 1, 2, 'Claro! Pode fornecer o número da encomenda?', NULL, '2026-01-17 14:10:00', '2026-01-18 21:26:28'),
(10, 2, 1, 'Sim, é a encomenda #12345', NULL, '2026-01-17 14:12:00', '2026-01-18 21:26:28'),
(11, 1, 2, 'Obrigado! A sua encomenda foi enviada ontem e deverá chegar em 2-3 dias úteis.', NULL, '2026-01-17 14:15:00', '2026-01-18 21:26:28'),
(12, 2, 1, 'Perfeito! Muito obrigado pela ajuda!', NULL, '2026-01-17 14:20:00', '2026-01-18 21:26:28'),
(13, 3, 2, 'Olá! Vi que adicionou um dos meus produtos aos favoritos. Posso ajudar?', NULL, '2026-01-18 10:00:00', '2026-01-18 21:26:28'),
(14, 2, 3, 'Olá Maria! Sim, estou interessado no casaco verde. Tem em tamanho M?', NULL, '2026-01-18 10:15:00', '2026-01-18 21:26:28'),
(15, 3, 2, 'Sim, temos em tamanho M! É feito com algodão orgânico certificado. Quer que reserve?', NULL, '2026-01-18 10:20:00', '2026-01-18 21:26:28'),
(16, 2, 3, 'Sim, por favor! Quanto fica com o envio?', NULL, '2026-01-18 10:25:00', '2026-01-18 21:26:28'),
(17, 3, 2, 'O produto custa 45€ e o envio é grátis em encomendas acima de 30€! 🎉', NULL, '2026-01-18 10:30:00', '2026-01-18 21:26:28'),
(31, 2, 6, 'ola', NULL, '2026-02-14 21:43:23', '2026-02-14 21:43:23'),
(32, 2, 8, 'ola', NULL, '2026-02-14 22:00:28', '2026-02-14 22:00:28'),
(33, 2, 4, 'ola', NULL, '2026-02-14 22:02:00', '2026-02-14 22:02:00'),
(34, 19, 1, 'Assunto: Teste automático suporte 20260214231613\nMensagem: Mensagem automática de validação do fluxo de contacto do utilizador.', NULL, '2026-02-14 22:16:37', '2026-02-14 22:16:37'),
(35, 20, 1, 'Assunto: Teste automático suporte 20260214231754\nMensagem: Mensagem automática de validação do fluxo de contacto do utilizador.', NULL, '2026-02-14 22:18:19', '2026-02-14 22:18:19'),
(36, 3, 2, 'imagem', NULL, '2026-02-16 14:35:13', '2026-02-16 14:35:13'),
(37, 3, 2, 'e', 'src/uploads/chat/chat_1771261837_445a5c6d4d510b2f.pdf', '2026-02-16 17:10:37', '2026-02-16 17:10:37'),
(38, 3, 2, 'imagem', 'src/uploads/chat/chat_1771261894_b1eb22e7f24488d3.jpeg', '2026-02-16 17:11:34', '2026-02-16 17:11:34'),
(39, 1, 20, 'sad', NULL, '2026-02-16 22:43:15', '2026-02-16 22:43:15'),
(40, 1, 20, 'asd', NULL, '2026-02-16 22:44:11', '2026-02-16 22:44:11'),
(41, 1, 20, 'sda', 'src/uploads/chat/chat_1771281862_680fb92eaba67857.docx', '2026-02-16 22:44:22', '2026-02-16 22:44:22'),
(42, 1, 1, 'Assunto: 23123\nMensagem: asda', NULL, '2026-02-16 23:05:04', '2026-02-16 23:05:04'),
(43, 2, 1, 'Nome: Visitante Teste\nEmail: joao@wegreen.pt\nAssunto: Teste suporte existente\nMensagem: Teste SUPORTE_EXIST_20260219151746', NULL, '2026-02-19 15:17:47', '2026-02-19 15:17:47'),
(44, 2, 1, 'Assunto: Teste suporte logado\nMensagem: Teste SUPORTE_LOGADO_20260219151746', NULL, '2026-02-19 15:17:53', '2026-02-19 15:17:53'),
(45, 2, 1, 'Nome: Visitante Teste\nEmail: joao@wegreen.pt\nAssunto: Teste suporte existente\nMensagem: Teste SUPORTE_EXIST_20260219151919', NULL, '2026-02-19 15:19:19', '2026-02-19 15:19:19'),
(46, 2, 1, 'Assunto: Teste suporte logado\nMensagem: Teste SUPORTE_LOGADO_20260219151919', NULL, '2026-02-19 15:19:25', '2026-02-19 15:19:25'),
(47, 2, 1, 'Nome: Visitante Teste\nEmail: joao@wegreen.pt\nAssunto: Teste suporte existente\nMensagem: Teste SUPORTE_EXIST_FIX_20260219152004', NULL, '2026-02-19 15:20:04', '2026-02-19 15:20:04'),
(48, 2, 1, 'Assunto: Teste suporte logado\nMensagem: Teste SUPORTE_LOGADO_FIX_20260219152004', NULL, '2026-02-19 15:20:10', '2026-02-19 15:20:10'),
(49, 24, 3, 'ola', NULL, '2026-02-19 15:45:28', '2026-02-19 15:45:28'),
(50, 3, 24, 'ola', NULL, '2026-02-19 23:07:36', '2026-02-19 23:07:36'),
(51, 3, 2, 'adeus', NULL, '2026-02-19 23:07:39', '2026-02-19 23:07:39'),
(52, 1, 2, 'ola', 'src/uploads/chat/chat_1771542897_3347047fa4a84fc9.pdf', '2026-02-19 23:14:57', '2026-02-19 23:14:57'),
(53, 1, 2, 'fatura', 'src/uploads/chat/chat_1771543034_c55171305bede427.pdf', '2026-02-19 23:17:14', '2026-02-19 23:17:14'),
(54, 1, 2, '', 'src/uploads/chat/chat_1771543041_81f90f09892bfd76.png', '2026-02-19 23:17:21', '2026-02-19 23:17:21'),
(55, 1, 2, '', 'src/uploads/chat/chat_1771543041_754480a75c8d20e4.png', '2026-02-19 23:17:21', '2026-02-19 23:17:21'),
(56, 3, 2, 'manual de utilizador', 'src/uploads/chat/chat_1771543340_b8703d321290c396.docx', '2026-02-19 23:22:20', '2026-02-19 23:22:20'),
(57, 3, 2, 'preview', 'src/uploads/chat/chat_1771543366_7b0f3263496ee00a.png', '2026-02-19 23:22:46', '2026-02-19 23:22:46');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes_lidas`
--

DROP TABLE IF EXISTS `notificacoes_lidas`;
CREATE TABLE IF NOT EXISTS `notificacoes_lidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilizador_id` int(11) NOT NULL,
  `tipo_notificacao` enum('encomenda','devolucao','utilizador','produto','chat','suporte') NOT NULL,
  `referencia_id` int(11) NOT NULL,
  `data_leitura` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_notificacao` (`utilizador_id`,`tipo_notificacao`,`referencia_id`),
  KEY `idx_utilizador` (`utilizador_id`),
  KEY `idx_tipo_ref` (`tipo_notificacao`,`referencia_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `notificacoes_lidas`
--

INSERT INTO `notificacoes_lidas` (`id`, `utilizador_id`, `tipo_notificacao`, `referencia_id`, `data_leitura`) VALUES
(1, 2, 'encomenda', 12, '2026-01-24 16:03:40'),
(2, 2, 'encomenda', 14, '2026-01-24 16:05:14'),
(3, 2, 'encomenda', 13, '2026-01-24 16:09:52'),
(4, 2, 'encomenda', 15, '2026-01-24 16:11:49'),
(5, 2, 'encomenda', 19, '2026-01-24 16:11:52'),
(6, 2, 'encomenda', 17, '2026-01-24 16:12:15'),
(7, 2, 'encomenda', 16, '2026-01-24 16:52:21'),
(8, 2, 'encomenda', 18, '2026-01-24 17:09:40'),
(9, 2, 'encomenda', 20, '2026-01-24 19:00:52'),
(21, 3, 'encomenda', 11, '2026-01-24 20:43:10'),
(22, 3, 'encomenda', 7, '2026-01-24 20:47:31'),
(23, 3, 'encomenda', 4, '2026-01-24 20:47:31'),
(24, 3, 'devolucao', 20, '2026-01-25 20:36:07'),
(25, 3, 'devolucao', 21, '2026-01-25 20:36:08'),
(26, 3, 'devolucao', 22, '2026-01-25 20:39:33'),
(27, 3, 'devolucao', 23, '2026-01-25 20:48:27'),
(28, 3, 'devolucao', 16, '2026-01-25 20:49:43'),
(29, 2, 'encomenda', 21, '2026-01-26 20:35:27'),
(79, 1, 'produto', 20, '2026-01-26 21:23:12'),
(80, 1, 'produto', 19, '2026-01-26 21:23:37'),
(81, 1, 'produto', 18, '2026-01-26 21:24:02'),
(82, 1, 'produto', 14, '2026-01-26 21:24:07'),
(83, 1, 'produto', 15, '2026-01-26 21:24:10'),
(84, 1, 'produto', 11, '2026-01-26 21:27:38'),
(85, 1, 'produto', 7, '2026-01-26 21:27:38'),
(87, 3, 'encomenda', 2, '2026-01-27 19:53:44'),
(88, 2, 'encomenda', 47, '2026-01-27 20:29:49'),
(89, 1, 'produto', 1, '2026-01-29 19:05:21'),
(90, 2, 'encomenda', 48, '2026-02-14 21:15:19'),
(91, 2, 'encomenda', 49, '2026-02-14 21:15:19'),
(92, 2, 'encomenda', 50, '2026-02-14 21:28:49'),
(93, 2, 'encomenda', 51, '2026-02-14 21:28:44'),
(96, 2, 'encomenda', 28, '2026-02-14 21:31:57'),
(97, 2, 'encomenda', 29, '2026-02-14 21:32:56'),
(98, 2, 'encomenda', 32, '2026-02-14 21:32:59'),
(99, 2, 'encomenda', 30, '2026-02-14 21:33:10'),
(100, 2, 'encomenda', 31, '2026-02-14 21:37:36'),
(101, 2, 'encomenda', 35, '2026-02-14 21:38:12'),
(102, 2, 'encomenda', 33, '2026-02-14 21:38:17'),
(103, 2, 'encomenda', 34, '2026-02-14 21:39:16'),
(104, 2, 'encomenda', 27, '2026-02-14 21:39:16'),
(105, 2, 'encomenda', 26, '2026-02-14 21:39:16'),
(106, 2, 'encomenda', 25, '2026-02-14 21:39:16'),
(107, 2, 'encomenda', 22, '2026-02-14 21:39:16'),
(108, 2, 'encomenda', 23, '2026-02-14 21:39:16'),
(109, 2, 'encomenda', 24, '2026-02-14 21:39:16'),
(110, 3, 'encomenda', 57, '2026-02-17 03:05:13'),
(111, 1, 'produto', 26, '2026-02-16 17:41:51'),
(112, 1, 'produto', 10, '2026-02-16 19:02:31'),
(113, 1, 'produto', 22, '2026-02-16 19:02:39'),
(114, 3, '', 2, '2026-02-17 12:41:51'),
(115, 3, '', 15, '2026-02-17 03:47:09'),
(116, 2, 'encomenda', 58, '2026-02-17 02:51:30'),
(117, 2, 'encomenda', 57, '2026-02-17 02:58:30'),
(134, 3, 'produto', 15, '2026-02-17 12:50:02'),
(136, 1, 'utilizador', 22, '2026-02-19 15:46:10'),
(137, 1, '', 48, '2026-02-19 16:00:42'),
(138, 1, '', 47, '2026-02-19 16:01:26'),
(139, 1, '', 46, '2026-02-19 16:00:42'),
(140, 1, '', 45, '2026-02-19 16:00:42'),
(141, 1, '', 44, '2026-02-19 16:00:42'),
(142, 1, '', 43, '2026-02-19 16:00:42'),
(143, 1, '', 42, '2026-02-19 16:00:42'),
(144, 1, '', 35, '2026-02-19 16:00:42'),
(145, 1, '', 34, '2026-02-19 16:01:29'),
(146, 1, '', 30, '2026-02-19 16:00:42'),
(147, 1, '', 29, '2026-02-19 16:00:42'),
(148, 1, '', 12, '2026-02-19 16:00:42'),
(149, 1, '', 10, '2026-02-19 16:00:42'),
(150, 1, '', 8, '2026-02-19 16:00:42'),
(151, 1, '', 1, '2026-02-19 16:00:42'),
(433, 1, 'suporte', 48, '2026-02-19 16:02:53'),
(434, 1, 'suporte', 46, '2026-02-19 16:02:55'),
(435, 1, 'suporte', 47, '2026-02-19 16:02:57'),
(436, 1, 'suporte', 45, '2026-02-19 16:02:57'),
(437, 1, 'suporte', 44, '2026-02-19 16:02:57'),
(438, 1, 'suporte', 43, '2026-02-19 16:02:57'),
(439, 1, 'suporte', 42, '2026-02-19 16:02:57'),
(440, 1, 'suporte', 35, '2026-02-19 16:02:57'),
(441, 1, 'suporte', 34, '2026-02-19 16:02:57'),
(442, 1, 'chat', 30, '2026-02-19 16:02:57'),
(443, 1, 'chat', 29, '2026-02-19 16:02:57'),
(444, 1, 'chat', 12, '2026-02-19 16:02:57'),
(445, 1, 'chat', 10, '2026-02-19 16:02:57'),
(446, 1, 'chat', 8, '2026-02-19 16:02:57'),
(447, 1, 'chat', 1, '2026-02-19 16:02:57'),
(448, 2, 'chat', 38, '2026-02-19 16:53:39'),
(449, 2, 'chat', 36, '2026-02-19 16:53:41'),
(450, 2, 'chat', 37, '2026-02-19 16:53:44'),
(451, 2, 'devolucao', 3, '2026-02-19 16:53:44'),
(452, 2, 'chat', 17, '2026-02-19 16:53:44'),
(453, 2, 'chat', 15, '2026-02-19 16:53:44'),
(454, 2, 'chat', 13, '2026-02-19 16:53:44'),
(455, 2, 'chat', 11, '2026-02-19 16:53:44'),
(456, 2, 'chat', 9, '2026-02-19 16:53:44'),
(457, 2, 'chat', 7, '2026-02-19 16:53:44'),
(458, 2, 'chat', 6, '2026-02-19 16:53:44'),
(459, 2, 'chat', 4, '2026-02-19 16:53:44'),
(460, 2, 'chat', 2, '2026-02-19 16:53:44'),
(461, 3, 'chat', 49, '2026-02-19 17:41:57'),
(462, 3, 'chat', 14, '2026-02-19 17:42:00'),
(463, 3, 'chat', 16, '2026-02-19 17:42:05'),
(464, 3, 'chat', 5, '2026-02-19 17:42:11'),
(465, 3, 'chat', 3, '2026-02-19 17:43:04'),
(467, 24, 'encomenda', 59, '2026-02-19 18:54:21'),
(468, 24, 'encomenda', 51, '2026-02-19 19:02:08'),
(469, 24, 'encomenda', 50, '2026-02-19 19:02:08'),
(470, 24, 'encomenda', 49, '2026-02-19 19:02:08'),
(471, 3, 'encomenda', 1, '2026-02-19 19:07:26'),
(472, 3, 'encomenda', 59, '2026-02-19 19:07:31'),
(473, 3, 'encomenda', 500000052, '2026-02-19 19:07:31'),
(474, 3, 'encomenda', 500000048, '2026-02-19 19:07:31'),
(475, 3, 'encomenda', 58, '2026-02-19 19:07:31'),
(476, 3, 'encomenda', 56, '2026-02-19 19:07:31'),
(477, 3, 'produto', 19, '2026-02-19 19:07:31'),
(478, 3, 'produto', 20, '2026-02-19 19:07:31'),
(479, 3, 'encomenda', 3, '2026-02-19 19:07:38'),
(480, 3, 'encomenda', 500000059, '2026-02-19 19:59:39'),
(481, 3, 'encomenda', 500000058, '2026-02-19 19:59:41'),
(482, 24, 'encomenda', 52, '2026-02-19 20:05:04'),
(484, 1, 'produto', 4, '2026-02-19 20:08:24'),
(487, 3, 'devolucao', 30, '2026-02-19 20:12:46'),
(488, 3, 'devolucao', 31, '2026-02-19 20:12:46'),
(489, 3, 'devolucao', 32, '2026-02-19 20:12:46'),
(490, 3, 'produto', 34, '2026-02-19 20:22:36'),
(491, 24, 'devolucao', 31, '2026-02-19 20:31:13'),
(492, 24, 'devolucao', 30, '2026-02-19 20:31:22'),
(493, 24, 'devolucao', 400000031, '2026-02-19 20:50:54'),
(494, 24, 'devolucao', 100000030, '2026-02-19 20:50:56'),
(495, 3, 'devolucao', 100000030, '2026-02-19 20:51:01'),
(496, 24, 'devolucao', 300000030, '2026-02-19 20:52:09'),
(497, 24, 'devolucao', 500000030, '2026-02-19 20:54:03'),
(498, 3, 'devolucao', 100000032, '2026-02-19 20:57:49'),
(499, 24, 'devolucao', 300000032, '2026-02-19 20:59:10'),
(500, 24, 'devolucao', 500000032, '2026-02-19 20:59:54'),
(502, 3, 'produto', 2, '2026-02-19 22:03:53'),
(503, 2, 'encomenda', 46, '2026-02-19 22:57:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes_preferencias`
--

DROP TABLE IF EXISTS `notificacoes_preferencias`;
CREATE TABLE IF NOT EXISTS `notificacoes_preferencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tipo_user` enum('cliente','anunciante') NOT NULL,
  `email_confirmacao` tinyint(1) DEFAULT 1 COMMENT 'Receber email de confirmação de encomenda',
  `email_processando` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda está a ser processada',
  `email_enviado` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda é enviada',
  `email_entregue` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda é entregue',
  `email_cancelamento` tinyint(1) DEFAULT 1 COMMENT 'Receber email quando encomenda é cancelada',
  `email_novas_encomendas_anunciante` tinyint(1) DEFAULT 1 COMMENT 'Receber email de novas encomendas',
  `email_encomendas_urgentes` tinyint(1) DEFAULT 1 COMMENT 'Receber alertas de encomendas pendentes urgentes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_devolucao_solicitada` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Confirmação de pedido de devolução',
  `email_devolucao_aprovada` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Devolução aprovada',
  `email_devolucao_rejeitada` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Devolução rejeitada',
  `email_reembolso_processado` tinyint(1) DEFAULT 1 COMMENT 'Cliente: Reembolso processado',
  `email_nova_devolucao_anunciante` tinyint(1) DEFAULT 1 COMMENT 'Anunciante: Nova devolução solicitada',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_preferencias` (`user_id`,`tipo_user`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Preferências de notificações por email dos utilizadores';

--
-- Extraindo dados da tabela `notificacoes_preferencias`
--

INSERT INTO `notificacoes_preferencias` (`id`, `user_id`, `tipo_user`, `email_confirmacao`, `email_processando`, `email_enviado`, `email_entregue`, `email_cancelamento`, `email_novas_encomendas_anunciante`, `email_encomendas_urgentes`, `created_at`, `updated_at`, `email_devolucao_solicitada`, `email_devolucao_aprovada`, `email_devolucao_rejeitada`, `email_reembolso_processado`, `email_nova_devolucao_anunciante`) VALUES
(1, 2, 'cliente', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(2, 7, 'cliente', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(4, 3, 'anunciante', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(5, 4, 'anunciante', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(6, 5, 'anunciante', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(7, 6, 'anunciante', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(8, 8, 'anunciante', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(11, 1, 'anunciante', 1, 1, 1, 1, 1, 1, 1, '2026-01-10 19:41:25', '2026-01-10 19:41:25', 1, 1, 1, 1, 1),
(12, 3, 'cliente', 1, 1, 1, 1, 1, 1, 1, '2026-01-11 16:21:02', '2026-01-11 16:21:02', 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
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
  KEY `idx_usado` (`usado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos`
--

DROP TABLE IF EXISTS `planos`;
CREATE TABLE IF NOT EXISTS `planos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `limite_produtos` int(11) DEFAULT NULL,
  `rastreio_tipo` varchar(200) NOT NULL,
  `relatorio_pdf` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `preco`, `limite_produtos`, `rastreio_tipo`, `relatorio_pdf`) VALUES
(1, 'Plano Essencial Verde', 0.00, 5, 'Basico', 0),
(2, 'Plano Crescimento Circular', 25.00, 10, 'Basico', 1),
(3, 'Plano Profissional Eco+', 70.00, NULL, 'Avancado', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos_ativos`
--

DROP TABLE IF EXISTS `planos_ativos`;
CREATE TABLE IF NOT EXISTS `planos_ativos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anunciante_id` int(11) NOT NULL,
  `plano_id` int(11) NOT NULL,
  `data_inicio` date DEFAULT curdate(),
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `anunciante_id` (`anunciante_id`),
  KEY `plano_id` (`plano_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `planos_ativos`
--

INSERT INTO `planos_ativos` (`id`, `anunciante_id`, `plano_id`, `data_inicio`, `data_fim`, `ativo`) VALUES
(1, 3, 2, '2025-12-06', NULL, 0),
(2, 4, 2, '2026-01-23', '2026-02-22', 0),
(3, 4, 3, '2026-01-23', '2026-02-22', 1),
(4, 3, 2, '2026-02-15', '2026-02-17', 0),
(5, 3, 3, '2026-02-17', '2026-03-19', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
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
  `sustentavel` tinyint(1) DEFAULT 0,
  `tipo_material` varchar(100) DEFAULT NULL,
  `motivo_rejeicao` text DEFAULT NULL,
  `data_criacao` date DEFAULT curdate(),
  `ativo` tinyint(1) DEFAULT 1,
  `stock` int(11) DEFAULT 0,
  PRIMARY KEY (`Produto_id`),
  KEY `tipo_produto_id` (`tipo_produto_id`),
  KEY `anunciante_id` (`anunciante_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`Produto_id`, `nome`, `tipo_produto_id`, `preco`, `foto`, `genero`, `anunciante_id`, `designer_id`, `marca`, `tamanho`, `estado`, `descricao`, `sustentavel`, `tipo_material`, `data_criacao`, `ativo`, `stock`, `motivo_rejeicao`) VALUES
(1, 'Blusa Colorida Custo Barcelona', 1, 15.00, 'src/img/4aa0032639cfb631fe2d24df2baeb114.jpg', 'Mulher', 1, NULL, 'Custo Barcelona', 'S', 'Excelente', 'Blusa colorida da Custo Barcelona nunca usada, com etiqueta.', 0, NULL, '2025-12-06', 1, 0, NULL),
(2, 'Vestido Versace Coleção 2003', 1, 289.90, 'assets/media/products/produto_Vestido_Versace_Cole____o_2003_20260219220200_0_4877.png', 'Mulher', 3, NULL, 'Versace', 'S', 'Como Novo', 'Vestido Versace coleção 2003, nunca usado.sdadda', 1, '100_reciclavel', '2025-12-06', 1, 5, 'APROVADO_ADMIN'),
(3, 'Calças de ganga Low Rise', 1, 35.40, 'src/img/835c014897bbbab6c135f8dfd9f59bdf.jpg', 'Mulher', 1, NULL, 'NYWS', 'S', 'Excelente', 'Calças de ganga Low Rise, nunca usadas.', 0, NULL, '2025-12-06', 1, 9, NULL),
(4, 'Botas pele', 2, 79.00, 'assets/media/products/produto_Botas_pele_20260219184412_0_3389.jpg', 'Mulher', 3, NULL, 'Ana Salazar', 'XXS', 'Como Novo', 'Botas de pele Michael Kors, elegância e sofisticação.sad', 0, '', '2025-12-06', 0, 10, 'PENDENTE_REVISAO_ANUNCIANTE'),
(5, 'Blusão de ganga', 1, 23.80, 'src/img/58c4e54e715841986e82c05fb3506a0d.jpg', 'Mulher', 1, NULL, 'Miss Sixty', 'M', 'Excelente', 'Blusão de ganga, nunca usado.', 0, NULL, '2025-12-06', 1, 10, NULL),
(6, 'Sweatshirt Billabong', 1, 23.80, 'src/img/6fa73f19c9a53d2c3d32f4c7e8d84300.jpg', 'Homem', 2, NULL, 'Billabong', 'M', 'Excelente', 'Sweatshirt Azul e Amarela da  Billabong, nunca usado.', 0, NULL, '2025-12-06', 1, 10, NULL),
(7, 'Leggings Yoga', 1, 10.90, 'src/img/206af231b9b61956182afd1d73a47082.jpg', 'Mulher', 3, NULL, 'Abercrombie & Fitch', 'XS', 'Excelente', 'Leggings Yoga, confortáveis e novas.', 0, NULL, '2025-12-06', 1, 10, NULL),
(8, 'Camisa GAP Azul', 1, 23.80, 'src/img/9b8d88a4f89987b10e27b2f05ece802f.jpg', 'Homem', 2, NULL, 'GAP', 'M', 'Excelente', 'Camisa Azul da  GAP, nunca usado.', 0, NULL, '2025-12-06', 1, 10, NULL),
(9, 'Casaco Desportivo Nike', 1, 22.00, 'src/img/c5584a14a4d828c79754a5b553a3abe3.jpg', 'Homem', 2, NULL, 'Nike', 'L', 'Como Novo', 'Casaco desportivo Nike, em excelente estado.', 0, NULL, '2025-12-06', 1, 9, NULL),
(10, 'Sweatshirt North Face', 1, 18.00, 'src/img/northface.webp', 'Criança', 2, NULL, 'North Face', '7 anos', 'Excelente', 'Sweatshirt da North Face nunca usada, com etiqueta.', 0, NULL, '2025-12-06', 1, 10, NULL),
(12, 'Camisa Polo Tommy Hilfiger', 1, 79.90, 'src/img/camisapolotommy.jpg', 'Homem', 2, NULL, 'Tommy Hilfiger', 'M', 'Novo', 'Camisa Polo Tommy Hilfiger, nunca usada, com etiqueta.', 0, NULL, '2025-12-06', 1, 10, NULL),
(13, 'Tênis Adidas UltraBoost', 2, 249.99, 'src/img/Adidas Ultraboost Light - lateral 2.jpg', 'Homem', 2, NULL, 'Adidas', '42', 'Novo', 'Tênis Adidas UltraBoost, conforto incomparável para atividades físicas.', 0, NULL, '2025-12-06', 1, 10, NULL),
(14, 'Bolsa de Couro Michael Kors', 3, 359.99, 'src/img/1298bdfb53f943dc29132b4684dbb507.jpg', 'Mulher', 3, NULL, 'Michael Kors', 'Único tamanho', 'Novo', 'Bolsa de couro Michael Kors de 1981.', 0, NULL, '2025-12-06', 1, 10, NULL),
(15, 'Creme Hidratante Nivea', 4, 29.90, 'src/img/b06b57ded4c5ded4bc7e600eada0649c.jpg', 'Mulher', 3, NULL, 'Nivea', 'Único tamanho', 'Novo', 'Creme hidratante Nivea Edição Limitada de 2006 lacrado.', 0, NULL, '2025-12-06', 1, 0, NULL),
(16, 'Fone de Ouvido Bose QuietComfort 35', 5, 899.00, 'src/img/s-l1200.webp', 'Homem', 2, NULL, 'Bose', 'Único tamanho', 'Novo', 'Fone de ouvido Bose com cancelamento de ruído, perfeito para viagens e uso diário.', 0, NULL, '2025-12-06', 1, 10, NULL),
(17, 'Calças de ganga Levis', 1, 19.00, 'src/img/63ee2e61813747ba02e97868eb523680.jpg', 'Homem', 2, NULL, 'Levis', 'XL', 'Novo', 'Calças de gnaga Levis nunca usadas com etiqueta.', 0, NULL, '2025-12-06', 1, 7, NULL),
(18, 'Casaco Verde Pistachio', 1, 29.90, 'src/img/image.webp', 'Mulher', 3, NULL, 'Naz', 'S', 'Novo', 'Camisola de Inverno Naz na cor verde pistachio.', 0, NULL, '2025-12-06', 1, 7, NULL),
(19, 'Tênis Azuis Vegan', 2, 69.90, 'src/img/MG_7966.webp', 'Mulher', 3, NULL, 'Nea Vegan Shoes', '38', 'Novo', 'Tênis azuis vegan da Nea Vegan Shoes, perfeitos para caminhadas e corridas.', 0, NULL, '2025-12-06', 1, 4, NULL),
(20, 'Calça de ganga em algodão', 1, 64.00, 'src/img/1759334984_3ece5eec09df8225764acda4b0d3c1d6.jpg', 'Criança', 3, NULL, 'Playup', '4-5 anos', 'Novo', 'Calça de ganga em algodão para criança da Playup.', 0, NULL, '2025-12-06', 1, 4, NULL),
(22, 'Colar Animal Edition', 3, 15.00, 'src/img/animalcross.jpeg', 'Mulher', 4, 1, 'HeartKnows', 'Tamanho único', 'Novo', 'Colar com missangas coloridas e daltamata coleção Animal Cross.', 0, NULL, '2025-12-06', 1, 7, NULL),
(23, '', NULL, NULL, 'src/img/download (10).jpeg', NULL, NULL, 1, NULL, NULL, 'Novo', 'Mala branca de missangas feita à mão.', 0, NULL, '2025-12-06', 0, 10, NULL),
(24, '', NULL, NULL, 'src/img/cora-pursley-dupe.jpeg', NULL, NULL, 1, NULL, NULL, 'Novo', 'Colar com cruz.', 0, NULL, '2025-12-06', 0, 10, NULL),
(25, '', NULL, NULL, 'src/img/ccult.jpeg', NULL, NULL, 1, NULL, NULL, 'Novo', 'T-shirt Cult Classic Azul.', 0, NULL, '2025-12-06', 0, 10, NULL),
(26, '', NULL, NULL, 'src/img/ccult.jpeg', NULL, NULL, 1, NULL, NULL, 'Novo', 'T-shirt Cult Classic Azul.', 0, NULL, '2025-12-06', 0, 10, NULL),
(34, 'Vestido Versace Coleção 20256', 1, 23.00, 'assets/media/products/produto_Vestido_Versace_Cole____o_20256_20260219212026_0_5009.jpg', 'Mulher', 3, NULL, 'Versace', 'L', 'Como Novo', 'excelente produto', 1, '70_reciclavel', '2026-02-19', 1, 23, 'APROVADO_ADMIN');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto_fotos`
--

DROP TABLE IF EXISTS `produto_fotos`;
CREATE TABLE IF NOT EXISTS `produto_fotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `foto` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produto_fotos`
--

INSERT INTO `produto_fotos` (`id`, `produto_id`, `foto`) VALUES
(1, 1, 'src/img/m83296720182_6.avif'),
(2, 1, 'src/img/m83296720182_5.avif');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ranking`
--

DROP TABLE IF EXISTS `ranking`;
CREATE TABLE IF NOT EXISTS `ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) DEFAULT NULL,
  `pontos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ranking`
--

INSERT INTO `ranking` (`id`, `nome`, `pontos`) VALUES
(1, 'Sem classificação', 0),
(2, 'Bronze', 200),
(3, 'Prata', 400),
(4, 'Ouro', 650),
(5, 'Platina', 850);

-- --------------------------------------------------------

--
-- Estrutura da tabela `rendimento`
--

DROP TABLE IF EXISTS `rendimento`;
CREATE TABLE IF NOT EXISTS `rendimento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valor` decimal(10,2) DEFAULT NULL,
  `anunciante_id` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `anunciante_id` (`anunciante_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `rendimento`
--

INSERT INTO `rendimento` (`id`, `valor`, `anunciante_id`, `descricao`, `data_registo`) VALUES
(1, 25.00, 3, 'Premium comprado!', '2025-12-06 19:18:55'),
(2, 100.00, 4, 'Enterprize comprado!', '2025-12-06 19:18:55'),
(3, 25.00, 5, 'Premium comprado!', '2025-12-06 19:18:55'),
(10, 4.25, 1, 'Comissão venda - Encomenda WG17695262834326043ef - Produto #3', '2026-01-27 15:04:43'),
(11, 6.30, 1, 'Comissão venda - Encomenda WG17695262834326043ef - Produto #1', '2026-01-27 15:04:43'),
(12, 7.14, 2, 'Comissão venda - Encomenda WG17695262834326043ef - Produto #6', '2026-01-27 15:04:43'),
(13, 6.48, 2, 'Comissão venda - Encomenda WG17695262834326043ef - Produto #10', '2026-01-27 15:04:43'),
(14, 6.00, 6, 'Comissão venda - Encomenda WG17695262834326043ef - Produto #26', '2026-01-27 15:04:43'),
(15, 4.50, 6, 'Comissão venda - Encomenda WG1771102596464973188 - Produto #26', '2026-02-14 20:56:36'),
(16, 1.50, 6, 'Comissão venda - Encomenda WG1771102689568262c24 - Produto #26', '2026-02-14 20:58:09'),
(17, 0.72, 8, 'Comissão venda - Encomenda WG1771102689568262c24 - Produto #24', '2026-02-14 20:58:09'),
(18, 1.50, 6, 'Comissão venda - Encomenda WG1771103078533307912 - Produto #26', '2026-02-14 21:04:38'),
(19, 1.50, 6, 'Comissão venda - Encomenda WG1771103458896966690 - Produto #26', '2026-02-14 21:10:58'),
(20, 25.00, 3, 'Plano Crescimento Circular ativado!', '2026-02-15 23:48:48'),
(21, 25.00, 3, 'Plano Crescimento Circular ativado!', '2026-02-15 23:56:45'),
(22, 1.32, 2, 'Comissão venda - Encomenda WG177120167534269991e - Produto #9', '2026-02-16 00:27:55'),
(23, 2.12, 1, 'Comissão venda - Encomenda WG177120167534269991e - Produto #3', '2026-02-16 00:27:55'),
(24, 34.79, 3, 'Comissão venda - Encomenda WG177120167534269991e - Produto #2', '2026-02-16 00:27:55'),
(25, 52.18, 3, 'Comissão venda - Encomenda WG1771207408625840de8 - Produto #2', '2026-02-16 02:03:28'),
(26, -1.50, 6, 'Reversão de comissão - Encomenda ID: 55 - Produto ID: 26', '2026-02-16 16:04:36'),
(27, 23.00, 1, 'dsa', '2026-02-12 00:00:00'),
(28, 32.00, 1, 'asd', '2026-02-16 00:00:00'),
(29, 23.00, 1, 'asd', '2026-02-16 00:00:00'),
(30, 12.34, 1, 'Teste rendimento debug', '2026-02-16 00:00:00'),
(31, 7.89, 1, 'Teste via controller op9', '2026-02-16 00:00:00'),
(32, 5.55, 1, 'Teste op9 sessão user', '2026-02-16 00:00:00'),
(33, 23.00, 1, 'asd', '2026-02-16 00:00:00'),
(34, 23.00, 1, 'sda', '2026-02-16 00:00:00'),
(35, 11.11, 1, 'Teste hora correta', '2026-02-16 14:37:00'),
(36, 23.00, 1, 'sad', '2026-02-16 22:08:00'),
(37, 3.42, 2, 'Comissão venda - Encomenda WG1771296653286836de3 - Produto #17', '2026-02-17 02:50:53'),
(38, 11.52, 3, 'Comissão venda - Encomenda WG1771296653286836de3 - Produto #20', '2026-02-17 02:50:53'),
(39, 12.58, 3, 'Comissão venda - Encomenda WG1771296653286836de3 - Produto #19', '2026-02-17 02:50:53'),
(40, 70.00, 3, 'Plano Profissional Eco+ ativado!', '2026-02-17 03:08:24'),
(41, 23.00, 1, 'asd', '2026-02-19 15:52:00'),
(42, 2.70, 4, 'Comissão venda - Encomenda WG177152669362087bd7b - Produto #22', '2026-02-19 18:44:53'),
(43, 11.52, 3, 'Comissão venda - Encomenda WG177152669362087bd7b - Produto #20', '2026-02-19 18:44:53'),
(44, 12.58, 3, 'Comissão venda - Encomenda WG177152669362087bd7b - Produto #19', '2026-02-19 18:44:53'),
(45, 5.38, 3, 'Comissão venda - Encomenda WG177152669362087bd7b - Produto #18', '2026-02-19 18:44:53'),
(46, -11.52, 3, 'Reversão de comissão - Encomenda ID: 59 - Produto ID: 20', '2026-02-19 20:52:23'),
(47, -5.38, 3, 'Reversão de comissão - Encomenda ID: 59 - Produto ID: 18', '2026-02-19 20:59:16');

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `stats_devolucoes_anunciante`
-- (Veja abaixo para a view atual)
--
DROP VIEW IF EXISTS `stats_devolucoes_anunciante`;
CREATE TABLE IF NOT EXISTS `stats_devolucoes_anunciante` (
`anunciante_id` int(11)
,`total_devolucoes` bigint(21)
,`pendentes` decimal(22,0)
,`aprovadas` decimal(22,0)
,`rejeitadas` decimal(22,0)
,`reembolsadas` decimal(22,0)
,`valor_total_reembolsado` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_produtos`
--

DROP TABLE IF EXISTS `tipo_produtos`;
CREATE TABLE IF NOT EXISTS `tipo_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipo_produtos`
--

INSERT INTO `tipo_produtos` (`id`, `descricao`) VALUES
(1, 'Roupa'),
(2, 'Calçado'),
(3, 'Acessórios'),
(4, 'Beleza'),
(5, 'Outros'),
(6, 'Artesanato');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_utilizadores`
--

DROP TABLE IF EXISTS `tipo_utilizadores`;
CREATE TABLE IF NOT EXISTS `tipo_utilizadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipo_utilizadores`
--

INSERT INTO `tipo_utilizadores` (`id`, `descricao`) VALUES
(1, 'Administrador'),
(2, 'Cliente'),
(3, 'Anunciante');

-- --------------------------------------------------------

--
-- Estrutura da tabela `transportadora`
--

DROP TABLE IF EXISTS `transportadora`;
CREATE TABLE IF NOT EXISTS `transportadora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` enum('CTT','DPD') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `transportadora`
--

INSERT INTO `transportadora` (`id`, `nome`) VALUES
(1, 'CTT'),
(2, 'DPD');

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
CREATE TABLE IF NOT EXISTS `utilizadores` (
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
  `distrito` varchar(120) DEFAULT NULL,
  `localidade` varchar(120) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `foto` varchar(250) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_utilizador_id` int(11) DEFAULT NULL,
  `plano_id` int(11) DEFAULT NULL,
  `ranking_id` int(11) DEFAULT NULL,
  `pontos_conf` int(11) DEFAULT 0,
  `data_criacao` date DEFAULT NULL,
  `data_expiracao_plano` datetime DEFAULT NULL COMMENT 'Data de expiracao do plano pago (NULL para plano gratuito)',
  `ultimo_email_expiracao` varchar(20) DEFAULT NULL COMMENT 'Tipo do ultimo email enviado: aviso_3dias, aviso_1dia, expirado',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_tipo` (`email`,`tipo_utilizador_id`),
  KEY `ranking_id` (`ranking_id`),
  KEY `tipo_utilizador_id` (`tipo_utilizador_id`),
  KEY `plano_id` (`plano_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `apelido`, `email`, `email_verificado`, `token_verificacao`, `token_expira_em`, `nif`, `telefone`, `morada`, `distrito`, `localidade`, `codigo_postal`, `foto`, `password`, `tipo_utilizador_id`, `plano_id`, `ranking_id`, `pontos_conf`, `data_criacao`, `data_expiracao_plano`, `ultimo_email_expiracao`) VALUES
(1, 'Admin WeGreen', NULL, 'admin@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/pexels-beccacorreiaph-31095884.jpg', 'admin123', 1, 1, 2, 225, NULL, NULL, NULL),
(2, 'Joao', NULL, 'joao@wegreen.pt', 1, NULL, NULL, '32424424433', '900000000', 'rua daqui ali', 'Évora', 'évora', '7005-287', 'src/img/pexels-stefanstefancik-91227.jpg', 'cliente123', 2, 1, 3, 515, NULL, NULL, NULL),
(3, 'Maria Santos', NULL, 'maria@wegreen.pt', 1, NULL, NULL, '123412312', '953234523', 'rua dqui ali', 'Évora', 'évora', '7005-287', 'src/img/pexels-olly-733872.jpg', 'anunciante123', 3, 3, 1, 130, NULL, '2026-03-19 04:08:24', NULL),
(4, 'Maria Luiza', NULL, 'Luiza@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/josefina-pereyra-dupe.jpeg', 'anunciante123', 3, 3, 2, 250, NULL, NULL, NULL),
(5, 'Matilde Mayer', NULL, 'Matilde@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/pexels-beccacorreiaph-31095531.jpg', 'anunciante123', 3, 1, 2, 275, NULL, NULL, NULL),
(6, 'Carlos Pereira', NULL, 'Carlos@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/pexels-rb-audiovisual-1819481.jpg', 'anunciante123', 3, 1, 1, 0, NULL, NULL, NULL),
(7, 'Mango', NULL, 'mango@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/3186bd92-91b4-480e-a033-be3c45e9fe76.jpeg', 'mango123', 2, 1, 1, 0, NULL, NULL, NULL),
(8, 'Solange Jewels', NULL, 'Solange@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/pexels-victor-dubugras.jpg', 'anunciante123', 3, 2, 1, 100, NULL, '2026-02-25 20:37:26', NULL),
(14, 'Joao', 'Silva', 'joao@wegreen.pt', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'src/img/pexels-beccacorreiaph-31095884.jpg', 'e10adc3949ba59abbe56e057f20f883e', 3, 2, 1, 0, '2026-01-16', '2026-02-25 20:38:25', NULL),
(19, 'Teste', 'EmailFlow', 'jmssgames+wegreenflow20260214231613@gmail.com', 1, NULL, NULL, '123456789', NULL, 'Rua de Teste 123, Lisboa', 'Lisboa', 'Lisboa', '1000-001', 'src/img/pexels-beccacorreiaph-31095884.jpg', '6453570d3075c2c68baf265b900ae6cc', 3, 1, 1, 0, '2026-02-14', NULL, NULL),
(20, 'Teste', 'EmailFlow', 'jmssgames+wegreenflow20260214231754@gmail.com', 1, NULL, NULL, '123456789', NULL, 'Rua de Teste 123, Lisboa', 'Lisboa', 'Lisboa', '1000-001', 'src/img/pexels-beccacorreiaph-31095884.jpg', '6453570d3075c2c68baf265b900ae6cc', 3, 1, 1, 0, '2026-02-14', NULL, NULL),
(23, 'Teste2', 'Copilot', 'teste.registo.fix.150003@wegreen.pt', 0, 'aaab6059bb7b0f7448454aad699eb0481ea73dcbd1ad506c263e44412489df34', '2026-02-20 16:00:03', '123456789', NULL, 'Rua de Teste Numero 456', 'Lisboa', 'Lisboa', '1000-002', 'src/img/pexels-beccacorreiaph-31095884.jpg', 'e10adc3949ba59abbe56e057f20f883e', 3, 1, 1, 0, '2026-02-19', NULL, NULL),
(24, 'Joao', 'Santos', 'jmssgames@gmail.com', 1, NULL, NULL, '542323242', NULL, 'Rua daqui ali', 'Évora', 'Évora', '7005-200', 'src/img/pexels-beccacorreiaph-31095884.jpg', '$2y$10$WPp1PluGRsVx7RxX7Dbtc.YKZ8LA4WLaGnqWnlZrlClQulNop7FWW', 2, 1, 1, 0, '2026-02-19', NULL, NULL),
(25, 'João Santos', NULL, 'jmssgames@gmail.com', 0, NULL, NULL, '21312312321341', '952312321321', NULL, NULL, NULL, NULL, NULL, '9a181fb17dcfc951d3f969fa0b528810', 1, NULL, NULL, 0, NULL, NULL, NULL),
(32, 'João Santos', NULL, 'jmssgames@gmail.com', 0, NULL, NULL, '24123213213', '95433234342323', 'Rua Alexandre Rosado N° 28 3\'D', NULL, NULL, NULL, NULL, '$2y$10$WPp1PluGRsVx7RxX7Dbtc.YKZ8LA4WLaGnqWnlZrlClQulNop7FWW', 3, NULL, 1, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE IF NOT EXISTS `vendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encomenda_id` int(11) DEFAULT NULL,
  `stripe_session_id` varchar(100) DEFAULT NULL COMMENT 'ID da sessão Stripe',
  `anunciante_id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT 1,
  `valor` decimal(10,2) DEFAULT NULL,
  `lucro` decimal(10,2) DEFAULT NULL,
  `data_venda` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `encomenda_id` (`encomenda_id`),
  KEY `anunciante_id` (`anunciante_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `vendas`
--

INSERT INTO `vendas` (`id`, `encomenda_id`, `stripe_session_id`, `anunciante_id`, `produto_id`, `quantidade`, `valor`, `lucro`, `data_venda`) VALUES
(1, 1, NULL, 3, 1, 2, 39.98, 2.40, '2025-12-06'),
(2, 2, NULL, 3, 3, 1, 89.99, 5.40, '2025-12-06'),
(3, 3, NULL, 3, 2, 1, 49.99, 3.00, '2025-12-06'),
(4, 4, NULL, 4, 3, 3, 269.97, 16.20, '2025-12-06'),
(5, 5, NULL, 4, 2, 1, 49.99, 3.00, '2025-12-06'),
(6, NULL, NULL, 3, 1, 2, 100.00, 20.00, '2025-12-08'),
(7, NULL, NULL, 3, 2, 1, 50.00, 10.00, '2025-12-07'),
(8, 12, '', 4, 22, 1, 15.00, 0.90, '2026-01-24'),
(9, 12, '', 2, 17, 2, 38.00, 2.28, '2026-01-24'),
(10, 13, '', 5, 23, 1, 35.00, 2.10, '2026-01-24'),
(11, 13, '', 5, 23, 1, 35.00, 2.10, '2026-01-24'),
(12, 13, '', 2, 6, 2, 47.60, 2.86, '2026-01-24'),
(13, 14, '', 2, 17, 1, 19.00, 1.14, '2026-01-24'),
(14, 15, '', 4, 22, 1, 15.00, 0.90, '2026-01-24'),
(15, 16, '', 5, 23, 2, 70.00, 4.20, '2026-01-24'),
(16, 16, '', 2, 8, 1, 23.80, 1.43, '2026-01-24'),
(17, 16, '', 4, 22, 2, 30.00, 1.80, '2026-01-24'),
(18, 17, '', 1, 3, 2, 70.80, 4.25, '2026-01-24'),
(19, 17, '', 2, 8, 2, 47.60, 2.86, '2026-01-24'),
(20, 17, '', 2, 17, 2, 38.00, 2.28, '2026-01-24'),
(21, 18, '', 2, 13, 1, 249.99, 15.00, '2026-01-24'),
(22, 18, '', 2, 6, 2, 47.60, 2.86, '2026-01-24'),
(23, 18, '', 1, 5, 2, 47.60, 2.86, '2026-01-24'),
(24, 19, '', 2, 13, 1, 249.99, 15.00, '2026-01-24'),
(25, 20, '', 4, 22, 1, 15.00, 0.90, '2026-01-24'),
(26, 20, '', 1, 5, 1, 23.80, 1.43, '2026-01-24'),
(27, 21, '', 6, 26, 1, 25.00, 1.50, '2026-01-24'),
(28, 21, '', 4, 22, 1, 15.00, 0.90, '2026-01-24'),
(29, 21, '', 2, 6, 2, 47.60, 2.86, '2026-01-24'),
(30, 22, '', 5, 23, 2, 70.00, 4.20, '2026-01-24'),
(31, 23, '', 2, 6, 2, 47.60, 2.86, '2026-01-24'),
(32, 24, '', 2, 13, 1, 249.99, 15.00, '2026-01-24'),
(33, 25, '', 2, 13, 1, 249.99, 15.00, '2026-01-24'),
(34, 26, '', 8, 24, 2, 24.00, 1.44, '2026-01-24'),
(35, 27, '', 6, 26, 2, 50.00, 3.00, '2026-01-24'),
(36, 27, '', 5, 23, 2, 70.00, 4.20, '2026-01-24'),
(37, 28, '', 5, 23, 1, 35.00, 2.10, '2026-01-24'),
(38, 28, '', 8, 24, 1, 12.00, 0.72, '2026-01-24'),
(39, 29, '', 1, 3, 2, 70.80, 4.25, '2026-01-24'),
(40, 29, '', 1, 3, 2, 70.80, 4.25, '2026-01-24'),
(41, 29, '', 8, 24, 2, 24.00, 1.44, '2026-01-24'),
(42, 30, '', 6, 26, 2, 50.00, 3.00, '2026-01-24'),
(43, 30, '', 2, 8, 2, 47.60, 2.86, '2026-01-24'),
(44, 30, '', 2, 17, 2, 38.00, 2.28, '2026-01-24'),
(45, 31, '', 1, 3, 1, 35.40, 2.12, '2026-01-24'),
(46, 31, '', 2, 13, 2, 499.98, 30.00, '2026-01-24'),
(47, 32, '', 1, 5, 1, 23.80, 1.43, '2026-01-24'),
(48, 32, '', 8, 24, 1, 12.00, 0.72, '2026-01-24'),
(49, 32, '', 5, 23, 2, 70.00, 4.20, '2026-01-24'),
(50, 33, '', 2, 17, 2, 38.00, 2.28, '2026-01-24'),
(51, 34, '', 1, 5, 2, 47.60, 2.86, '2026-01-24'),
(52, 34, '', 1, 5, 1, 23.80, 1.43, '2026-01-24'),
(53, 34, '', 2, 17, 2, 38.00, 2.28, '2026-01-24'),
(54, 35, '', 4, 22, 1, 15.00, 0.90, '2026-01-24'),
(55, 35, '', 5, 23, 1, 35.00, 2.10, '2026-01-24'),
(56, 35, '', 4, 22, 2, 30.00, 1.80, '2026-01-24'),
(57, 6, '', 8, 24, 2, 24.00, 1.44, '2025-12-06'),
(58, 6, '', 4, 22, 1, 15.00, 0.90, '2025-12-06'),
(59, 6, '', 5, 23, 1, 35.00, 2.10, '2025-12-06'),
(60, 7, '', 2, 6, 1, 23.80, 1.43, '2025-12-06'),
(61, 7, '', 1, 5, 1, 23.80, 1.43, '2025-12-06'),
(62, 7, '', 2, 17, 2, 38.00, 2.28, '2025-12-06'),
(63, 8, '', 1, 3, 1, 35.40, 2.12, '2025-12-06'),
(64, 9, '', 1, 3, 1, 35.40, 2.12, '2025-12-06'),
(65, 10, '', 4, 22, 1, 15.00, 0.90, '2025-12-06'),
(66, 11, '', 2, 13, 2, 499.98, 30.00, '2025-12-06'),
(67, 11, '', 6, 26, 2, 50.00, 3.00, '2025-12-06'),
(68, 11, '', 2, 8, 2, 47.60, 2.86, '2025-12-06'),
(74, 47, 'cs_test_b1MyBcL4o6XKpDM3h18kHbptPeFTgpGtOque9uc57y9kaT5vZK1zd1L1fg', 1, 3, 2, 70.80, 4.25, '2026-01-27'),
(75, 47, 'cs_test_b1MyBcL4o6XKpDM3h18kHbptPeFTgpGtOque9uc57y9kaT5vZK1zd1L1fg', 1, 1, 7, 105.00, 6.30, '2026-01-27'),
(76, 47, 'cs_test_b1MyBcL4o6XKpDM3h18kHbptPeFTgpGtOque9uc57y9kaT5vZK1zd1L1fg', 2, 6, 5, 119.00, 7.14, '2026-01-27'),
(77, 47, 'cs_test_b1MyBcL4o6XKpDM3h18kHbptPeFTgpGtOque9uc57y9kaT5vZK1zd1L1fg', 2, 10, 6, 108.00, 6.48, '2026-01-27'),
(78, 47, 'cs_test_b1MyBcL4o6XKpDM3h18kHbptPeFTgpGtOque9uc57y9kaT5vZK1zd1L1fg', 6, 26, 4, 100.00, 6.00, '2026-01-27'),
(79, 48, 'cs_test_b1zL2FHHeCUiDMhlts9Y7ScgK8pGc6JsXtgNPHXCtPQVHglX6YqsmzjTbf', 6, 26, 3, 75.00, 4.50, '2026-02-14'),
(80, 49, 'cs_test_b1QW8Z1zGOHCHsPRALYKILZA3ieX61kChiCyGxjDEPjuStchQQdaLXU3Rz', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(81, 49, 'cs_test_b1QW8Z1zGOHCHsPRALYKILZA3ieX61kChiCyGxjDEPjuStchQQdaLXU3Rz', 8, 24, 1, 12.00, 0.72, '2026-02-14'),
(82, 50, 'cs_test_b1C6trMk0p9La4E1dile6Z1LiYYeNthE0kUxVhf9BiDJnCxdUTj5Z6CEAb', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(83, 51, 'cs_test_b18JYhe5mCDeYBY5fgMljVsEBU2wdFFdWJKOpEdOwJxARVKasMGCnGjIVz', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(84, 52, 'cs_test_20260214232205_A', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(85, 53, 'cs_test_20260214232205_B', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(86, 54, 'cs_test_20260214232357_A', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(87, 55, 'cs_test_20260214232357_B', 6, 26, 1, 25.00, 1.50, '2026-02-14'),
(88, 56, 'cs_test_b1zVBa8QVxsGcIB1DOA3PAVooJtRMlEVkkU50czAIG4EgFibM7XO0k1vDO', 2, 9, 1, 22.00, 1.32, '2026-02-16'),
(89, 56, 'cs_test_b1zVBa8QVxsGcIB1DOA3PAVooJtRMlEVkkU50czAIG4EgFibM7XO0k1vDO', 1, 3, 1, 35.40, 2.12, '2026-02-16'),
(90, 56, 'cs_test_b1zVBa8QVxsGcIB1DOA3PAVooJtRMlEVkkU50czAIG4EgFibM7XO0k1vDO', 3, 2, 2, 579.80, 34.79, '2026-02-16'),
(91, 57, 'cs_test_b10GIw28MKPmNWHFhk0KCNSwkkP6FzAAwZTr8ptb2vy5LNDI8RRswoAhnB', 3, 2, 3, 869.70, 52.18, '2026-02-16'),
(92, 58, 'cs_test_b1yluF6gtKwmCYtMZuhNgm2gFM1fOz7i2HVL9nv0zeE7yHHK3KvQw32CZv', 2, 17, 3, 57.00, 3.42, '2026-02-17'),
(93, 58, 'cs_test_b1yluF6gtKwmCYtMZuhNgm2gFM1fOz7i2HVL9nv0zeE7yHHK3KvQw32CZv', 3, 20, 3, 192.00, 11.52, '2026-02-17'),
(94, 58, 'cs_test_b1yluF6gtKwmCYtMZuhNgm2gFM1fOz7i2HVL9nv0zeE7yHHK3KvQw32CZv', 3, 19, 3, 209.70, 12.58, '2026-02-17'),
(95, 59, 'cs_test_b1KpipWBpDSjc0zYukgTfzkNdzNDJxakeMtP2pl9Vbeo00Bn0KyZt7iFex', 4, 22, 3, 45.00, 2.70, '2026-02-19'),
(96, 59, 'cs_test_b1KpipWBpDSjc0zYukgTfzkNdzNDJxakeMtP2pl9Vbeo00Bn0KyZt7iFex', 3, 20, 3, 192.00, 11.52, '2026-02-19'),
(97, 59, 'cs_test_b1KpipWBpDSjc0zYukgTfzkNdzNDJxakeMtP2pl9Vbeo00Bn0KyZt7iFex', 3, 19, 3, 209.70, 12.58, '2026-02-19'),
(98, 59, 'cs_test_b1KpipWBpDSjc0zYukgTfzkNdzNDJxakeMtP2pl9Vbeo00Bn0KyZt7iFex', 3, 18, 3, 89.70, 5.38, '2026-02-19');

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `view_devolucoes_completa`
-- (Veja abaixo para a view atual)
--
DROP VIEW IF EXISTS `view_devolucoes_completa`;
CREATE TABLE IF NOT EXISTS `view_devolucoes_completa` (
`id` int(11)
,`encomenda_id` int(11)
,`codigo_devolucao` varchar(50)
,`cliente_id` int(11)
,`anunciante_id` int(11)
,`produto_id` int(11)
,`quantidade` int(11)
,`valor_reembolso` decimal(10,2)
,`motivo` enum('defeituoso','tamanho_errado','nao_como_descrito','arrependimento','outro')
,`motivo_detalhe` text
,`notas_cliente` text
,`notas_anunciante` text
,`notas_recebimento` text
,`estado` enum('solicitada','aprovada','rejeitada','produto_enviado','produto_recebido','reembolsada','cancelada')
,`payment_intent_id` varchar(100)
,`reembolso_stripe_id` varchar(100)
,`reembolso_status` varchar(50)
,`fotos` longtext
,`codigo_envio_devolucao` varchar(100)
,`codigo_rastreio` varchar(100)
,`transportadora_devolucao_id` int(11)
,`data_solicitacao` timestamp
,`data_aprovacao` timestamp
,`data_rejeicao` timestamp
,`data_envio_cliente` datetime
,`data_produto_recebido` timestamp
,`data_recebimento` datetime
,`data_reembolso` timestamp
,`updated_at` timestamp
,`codigo_encomenda` varchar(50)
,`data_envio` date
,`codigo_rastreio_original` varchar(100)
,`produto_nome` varchar(150)
,`produto_foto` varchar(255)
,`produto_imagem` varchar(255)
,`cliente_nome` varchar(100)
,`cliente_email` varchar(100)
,`anunciante_nome` varchar(100)
,`anunciante_email` varchar(100)
);

-- --------------------------------------------------------

--
-- Estrutura para vista `stats_devolucoes_anunciante`
--
DROP TABLE IF EXISTS `stats_devolucoes_anunciante`;

DROP VIEW IF EXISTS `stats_devolucoes_anunciante`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `stats_devolucoes_anunciante`  AS SELECT `d`.`anunciante_id` AS `anunciante_id`, count(`d`.`id`) AS `total_devolucoes`, sum(case when `d`.`estado` = 'solicitada' then 1 else 0 end) AS `pendentes`, sum(case when `d`.`estado` = 'aprovada' then 1 else 0 end) AS `aprovadas`, sum(case when `d`.`estado` in ('produto_enviado','produto_recebido') then 1 else 0 end) AS `em_progresso`, sum(case when `d`.`estado` = 'rejeitada' then 1 else 0 end) AS `rejeitadas`, sum(case when `d`.`estado` = 'reembolsada' then 1 else 0 end) AS `reembolsadas`, coalesce(sum(case when `d`.`estado` = 'reembolsada' then `d`.`valor_reembolso` else 0 end),0) AS `valor_total_reembolsado` FROM `devolucoes` AS `d` GROUP BY `d`.`anunciante_id` ;

-- --------------------------------------------------------

--
-- Estrutura para vista `view_devolucoes_completa`
--
DROP TABLE IF EXISTS `view_devolucoes_completa`;

DROP VIEW IF EXISTS `view_devolucoes_completa`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_devolucoes_completa`  AS SELECT `d`.`id` AS `id`, `d`.`encomenda_id` AS `encomenda_id`, `d`.`codigo_devolucao` AS `codigo_devolucao`, `d`.`cliente_id` AS `cliente_id`, `d`.`anunciante_id` AS `anunciante_id`, `d`.`produto_id` AS `produto_id`, `d`.`quantidade` AS `quantidade`, `d`.`valor_reembolso` AS `valor_reembolso`, `d`.`motivo` AS `motivo`, `d`.`motivo_detalhe` AS `motivo_detalhe`, `d`.`notas_cliente` AS `notas_cliente`, `d`.`notas_anunciante` AS `notas_anunciante`, `d`.`notas_recebimento` AS `notas_recebimento`, `d`.`estado` AS `estado`, `d`.`payment_intent_id` AS `payment_intent_id`, `d`.`reembolso_stripe_id` AS `reembolso_stripe_id`, `d`.`reembolso_status` AS `reembolso_status`, `d`.`fotos` AS `fotos`, `d`.`codigo_envio_devolucao` AS `codigo_envio_devolucao`, `d`.`codigo_rastreio` AS `codigo_rastreio`, `d`.`transportadora_devolucao_id` AS `transportadora_devolucao_id`, `d`.`data_solicitacao` AS `data_solicitacao`, `d`.`data_aprovacao` AS `data_aprovacao`, `d`.`data_rejeicao` AS `data_rejeicao`, `d`.`data_envio_cliente` AS `data_envio_cliente`, `d`.`data_produto_recebido` AS `data_produto_recebido`, `d`.`data_recebimento` AS `data_recebimento`, `d`.`data_reembolso` AS `data_reembolso`, `d`.`updated_at` AS `updated_at`, `e`.`codigo_encomenda` AS `codigo_encomenda`, `e`.`data_envio` AS `data_envio`, `e`.`codigo_rastreio` AS `codigo_rastreio_original`, `p`.`nome` AS `produto_nome`, `p`.`foto` AS `produto_foto`, `p`.`foto` AS `produto_imagem`, `u`.`nome` AS `cliente_nome`, `u`.`email` AS `cliente_email`, `a`.`nome` AS `anunciante_nome`, `a`.`email` AS `anunciante_email` FROM ((((`devolucoes` `d` left join `encomendas` `e` on(`d`.`encomenda_id` = `e`.`id`)) left join `produtos` `p` on(`d`.`produto_id` = `p`.`Produto_id`)) left join `utilizadores` `u` on(`d`.`cliente_id` = `u`.`id`)) left join `utilizadores` `a` on(`d`.`anunciante_id` = `a`.`id`)) ;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `avaliacoes_produtos`
--


--
-- Limitadores para a tabela `carrinho_itens`
--


--
-- Limitadores para a tabela `denuncias`
--


--
-- Limitadores para a tabela `descontos_ranking`
--


--
-- Limitadores para a tabela `encomendas`
--


--
-- Limitadores para a tabela `favoritos`
--


--
-- Limitadores para a tabela `fornecedores`
--


--
-- Limitadores para a tabela `gastos`
--


--
-- Limitadores para a tabela `historico_produtos`
--


--
-- Limitadores para a tabela `mensagens`
--


--
-- Limitadores para a tabela `mensagensadmin`
--


--
-- Limitadores para a tabela `notificacoes_preferencias`
--


--
-- Limitadores para a tabela `password_resets`
--


--
-- Limitadores para a tabela `planos_ativos`
--


--
-- Limitadores para a tabela `produtos`
--


--
-- Limitadores para a tabela `produto_fotos`
--


--
-- Limitadores para a tabela `rendimento`
--


--
-- Limitadores para a tabela `utilizadores`
--


--
-- Limitadores para a tabela `vendas`
--


-- --------------------------------------------------------
--
-- INSERTs adicionais
--

INSERT INTO `denuncias` (`id`, `denunciante_id`, `denunciado_id`, `descricao`, `imagem_anexo`, `estado`, `data_registo`) VALUES
(3, 7, 2, '[Avaliação #9] Motivo: conteudo_inapropriado | Detalhes: adeus', NULL, 'Rejeitado', '2026-02-19');

INSERT INTO `password_resets` (`id`, `utilizador_id`, `email`, `token`, `expira_em`, `usado`, `criado_em`, `usado_em`, `ip_address`, `user_agent`) VALUES
(7, 19, 'jmssgames+wegreenflow20260214231613@gmail.com', '760c0fd8c74a7521bb0213b74517e7f3a2e057d4d58fb47a441e73137120d6a0', '2026-02-15 00:16:16', 0, '2026-02-14 22:16:16', NULL, NULL, NULL),
(8, 19, 'jmssgames+wegreenflow20260214231613@gmail.com', '3af5ff354aecbcccb801c7cb72aa20abb6f4bc2b5db46a53034a62a8db7c0ca4', '2026-02-15 00:16:18', 1, '2026-02-14 22:16:18', '2026-02-14 23:16:18', '127.0.0.1', 'EmailFlowCLI'),
(9, 20, 'jmssgames+wegreenflow20260214231754@gmail.com', '6c76ed6d25ccb7855859a19bce8b356171e5d3e06a596aee62021d9354f8792d', '2026-02-15 00:17:59', 0, '2026-02-14 22:17:59', NULL, NULL, NULL),
(10, 20, 'jmssgames+wegreenflow20260214231754@gmail.com', '05655e3ade8f5b0f676e241963dbc244cfae2bc9ddf729b2b4efae7d45adfec6', '2026-02-15 00:18:01', 1, '2026-02-14 22:18:01', '2026-02-14 23:18:01', '127.0.0.1', 'EmailFlowCLI'),
(12, 24, 'jmssgames@gmail.com', '79abe41356354c1f2125a4145a6f8a46e65bac734d1a157660bb17f4e44cfc6f', '2026-02-19 17:39:11', 1, '2026-02-19 15:39:11', '2026-02-19 16:39:45', '::1', 'Mozilla/5.0 (Windows NT 10.0)');


-- --------------------------------------------------------

--

-- ALTER TABLEs adicionais

--


-- --------------------------------------------------------
--
-- ALTER TABLE (normalizado)
--




-- --------------------------------------------------------
--
-- ALTER TABLE (somente constraints para evitar conflito de PK/INDEX)
--

ALTER TABLE `avaliacoes_produtos`
  ADD CONSTRAINT `avaliacoes_produtos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_produtos_ibfk_2` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

ALTER TABLE `carrinho_itens`
  ADD CONSTRAINT `carrinho_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE;

ALTER TABLE `denuncias`
  ADD CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`denunciante_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `denuncias_ibfk_2` FOREIGN KEY (`denunciado_id`) REFERENCES `utilizadores` (`id`);

ALTER TABLE `encomendas`
  ADD CONSTRAINT `encomendas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `encomendas_ibfk_3` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`),
  ADD CONSTRAINT `encomendas_ibfk_4` FOREIGN KEY (`transportadora_id`) REFERENCES `transportadora` (`id`),
  ADD CONSTRAINT `encomendas_ibfk_5` FOREIGN KEY (`TipoProdutoNome`) REFERENCES `tipo_produtos` (`id`);

ALTER TABLE `favoritos`
  ADD CONSTRAINT `fk_favorito_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorito_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE;

ALTER TABLE `fornecedores`
  ADD CONSTRAINT `fornecedores_ibfk_1` FOREIGN KEY (`tipo_produtos_id`) REFERENCES `tipo_produtos` (`id`);

ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`);

ALTER TABLE `historico_produtos`
  ADD CONSTRAINT `historico_produtos_ibfk_1` FOREIGN KEY (`encomenda_id`) REFERENCES `encomendas` (`id`);

ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `mensagens_ibfk_3` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`);

ALTER TABLE `mensagensadmin`
  ADD CONSTRAINT `mensagensadmin_ibfk_1` FOREIGN KEY (`remetente_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `mensagensadmin_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `utilizadores` (`id`);

ALTER TABLE `notificacoes_preferencias`
  ADD CONSTRAINT `notificacoes_preferencias_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

ALTER TABLE `planos_ativos`
  ADD CONSTRAINT `planos_ativos_ibfk_1` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `planos_ativos_ibfk_2` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`tipo_produto_id`) REFERENCES `tipo_produtos` (`id`),
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`);

ALTER TABLE `produto_fotos`
  ADD CONSTRAINT `produto_fotos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE;

ALTER TABLE `rendimento`
  ADD CONSTRAINT `rendimento_ibfk_1` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`);

ALTER TABLE `utilizadores`
  ADD CONSTRAINT `utilizadores_ibfk_1` FOREIGN KEY (`ranking_id`) REFERENCES `ranking` (`id`),
  ADD CONSTRAINT `utilizadores_ibfk_2` FOREIGN KEY (`tipo_utilizador_id`) REFERENCES `tipo_utilizadores` (`id`),
  ADD CONSTRAINT `utilizadores_ibfk_3` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`encomenda_id`) REFERENCES `encomendas` (`id`),
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`anunciante_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `vendas_ibfk_3` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
