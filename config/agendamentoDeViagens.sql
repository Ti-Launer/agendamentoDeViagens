-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 10, 2024 at 06:25 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agendamentoDeViagens`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
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

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `nome`, `username`, `email`, `senha`, `master`, `forca_senha`, `ativo`) VALUES
(3, 'Diogo Augusto Wermann', 'diogo-augusto-wermann', 'ti@launer.com.br', '$2y$10$NvQPoyWzWXxrU.TWrWt2lOEKdJV95xx1s3BmKv/zuf7STezlK0Ece', 'no', 'no', 'yes'),
(4, 'Alexandre Zang', 'alexandre-zang', 'compras@launer.com.br', '$2y$10$2sxojKBISQPkKZRI9CputuirpbxPpJtLasBcpwn2PHDqb74xsg65e', 'no', 'no', 'no'),
(6, 'Diogo Augusto Wermann', 'diogo-augusto-wermann-2', 'rh@launer.com.br', '$2y$10$QpmAuuLCEkzSXuu9KfZcpe25Mx8.KeP3000TMo0dSSr0insWkoRW.', 'no', 'no', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `carros`
--

CREATE TABLE `carros` (
  `id` int(11) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `placa` varchar(7) NOT NULL,
  `detalhe` text NOT NULL,
  `condicao` enum('boa','ruim') NOT NULL DEFAULT 'boa',
  `ativo` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carros`
--

INSERT INTO `carros` (`id`, `modelo`, `placa`, `detalhe`, `condicao`, `ativo`) VALUES
(1, 'Saveiro', 'ABC1D23', '', 'boa', 'yes'),
(2, 'Saveiro', 'EFG4H56', '', 'boa', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `carro_id` int(11) DEFAULT NULL,
  `tipo_reserva` enum('longa','curta') NOT NULL,
  `status` enum('pendente','confirmado','cancelado') DEFAULT 'pendente',
  `motivo` text NOT NULL,
  `km_inicial` float NOT NULL,
  `km_final` float NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `hora_inicio` datetime NOT NULL,
  `nome_func` varchar(100) NOT NULL,
  `email_func` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`);

--
-- Indexes for table `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `carros`
--
ALTER TABLE `carros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
