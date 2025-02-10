-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 10/02/2025 às 17:43
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `agendamentoDeViagens`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `master` enum('yes','no') NOT NULL DEFAULT 'no',
  `forca_senha` enum('yes','no') NOT NULL,
  `ativo` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `admins_ativos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `admins_ativos` (
`id` int(11)
,`nome` varchar(100)
,`username` varchar(100)
,`email` varchar(100)
,`senha` varchar(255)
,`master` enum('yes','no')
,`forca_senha` enum('yes','no')
,`ativo` enum('yes','no')
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda_carros`
--

CREATE TABLE `agenda_carros` (
  `id` int(11) NOT NULL,
  `carro` varchar(25) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `destino_motivo` text NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carros`
--

CREATE TABLE `carros` (
  `placa` varchar(7) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `detalhe` text NOT NULL,
  `condicao` enum('boa','manutencao') NOT NULL DEFAULT 'boa',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `tipo_carro` enum('carga','passeio') NOT NULL,
  `km_atual` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `carros_disponiveis`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `carros_disponiveis` (
`placa` varchar(7)
,`modelo` varchar(100)
,`detalhe` text
,`condicao` enum('boa','manutencao')
,`ativo` tinyint(1)
,`tipo_carro` enum('carga','passeio')
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo_reserva` enum('curta','longa') NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime DEFAULT NULL,
  `tipo_carro` enum('carga','passeio','indiferente') NOT NULL,
  `carro` varchar(7) DEFAULT NULL,
  `destino_motivo` text NOT NULL,
  `status` enum('pendente','confirmado','cancelado','fechada') NOT NULL DEFAULT 'pendente',
  `km_inicial` float DEFAULT NULL,
  `km_final` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `reservas`
--
DELIMITER $$
CREATE TRIGGER `after_reserva_confirmada` AFTER UPDATE ON `reservas` FOR EACH ROW BEGIN
    -- Verifica se o status foi alterado para 'confirmado'
    IF NEW.status = 'confirmado' AND OLD.status != 'confirmado' THEN
        -- Insere os dados na tabela agenda_carros
        INSERT INTO agenda_carros (carro, nome, destino_motivo, data_inicio, data_fim)
        VALUES (NEW.carro, NEW.nome, NEW.destino_motivo, NEW.data_inicio, NEW.data_fim);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `reservas_pendentes`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `reservas_pendentes` (
`id` bigint(20) unsigned
,`nome` varchar(100)
,`email` varchar(255)
,`destino_motivo` text
,`data_inicio` datetime
,`data_fim` datetime
,`tipo_carro` enum('carga','passeio','indiferente')
,`carro` varchar(7)
,`status` enum('pendente','confirmado','cancelado','fechada')
,`tipo_reserva` enum('curta','longa')
);

-- --------------------------------------------------------

--
-- Estrutura para view `admins_ativos`
--
DROP TABLE IF EXISTS `admins_ativos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `admins_ativos`  AS SELECT `admins`.`id` AS `id`, `admins`.`nome` AS `nome`, `admins`.`username` AS `username`, `admins`.`email` AS `email`, `admins`.`senha` AS `senha`, `admins`.`master` AS `master`, `admins`.`forca_senha` AS `forca_senha`, `admins`.`ativo` AS `ativo` FROM `admins` WHERE `admins`.`ativo` = 1 ;

-- --------------------------------------------------------

--
-- Estrutura para view `carros_disponiveis`
--
DROP TABLE IF EXISTS `carros_disponiveis`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `carros_disponiveis`  AS SELECT `c`.`placa` AS `placa`, `c`.`modelo` AS `modelo`, `c`.`detalhe` AS `detalhe`, `c`.`condicao` AS `condicao`, `c`.`ativo` AS `ativo`, `c`.`tipo_carro` AS `tipo_carro` FROM `carros` AS `c` WHERE `c`.`condicao` = 'boa' AND `c`.`ativo` = 1 AND !(`c`.`placa` in (select `r`.`carro` from `reservas` `r` where `r`.`data_inicio` < current_timestamp() AND `r`.`data_fim` > current_timestamp())) ;

-- --------------------------------------------------------

--
-- Estrutura para view `reservas_pendentes`
--
DROP TABLE IF EXISTS `reservas_pendentes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reservas_pendentes`  AS SELECT `r`.`id` AS `id`, `r`.`nome` AS `nome`, `r`.`email` AS `email`, `r`.`destino_motivo` AS `destino_motivo`, `r`.`data_inicio` AS `data_inicio`, `r`.`data_fim` AS `data_fim`, `r`.`tipo_carro` AS `tipo_carro`, `r`.`carro` AS `carro`, `r`.`status` AS `status`, `r`.`tipo_reserva` AS `tipo_reserva` FROM `reservas` AS `r` WHERE `r`.`status` = 'pendente' ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `agenda_carros`
--
ALTER TABLE `agenda_carros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`placa`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carro` (`carro`),
  ADD KEY `reservas_status_idx` (`status`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `agenda_carros`
--
ALTER TABLE `agenda_carros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_carro` FOREIGN KEY (`carro`) REFERENCES `carros` (`placa`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
