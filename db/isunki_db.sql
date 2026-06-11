-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 09:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `isunki_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `multas`
--

CREATE TABLE `multas` (
  `id_multa` int(11) NOT NULL,
  `id_usuario_multado` int(11) NOT NULL,
  `id_usuario_creador` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `monto_a_pagar` decimal(10,2) NOT NULL,
  `estado` enum('sin_pagar','pendiente','pagado') NOT NULL DEFAULT 'sin_pagar',
  `fecha_infraccion` date NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `multas`
--

INSERT INTO `multas` (`id_multa`, `id_usuario_multado`, `id_usuario_creador`, `motivo`, `monto_a_pagar`, `estado`, `fecha_infraccion`, `fecha_registro`) VALUES
(2, 4, 2, 'Prueba primera multa', 3.00, 'pagado', '2026-06-07', '2026-06-10 16:55:46'),
(3, 4, 2, 'Llegar 10 minutos tarde', 5.00, 'pagado', '2026-05-29', '2026-06-10 18:47:26'),
(4, 2, 2, 'Te has dejado la sudadera', 2.00, 'sin_pagar', '2026-06-02', '2026-06-10 18:48:01'),
(7, 4, 2, 'Llegar 5 minutos tarde', 2.00, 'pagado', '2026-06-04', '2026-06-11 01:47:50'),
(8, 4, 2, 'Te has dejado la sudadera', 2.00, 'sin_pagar', '2026-06-05', '2026-06-11 06:23:54'),
(10, 6, 7, 'Te has dejado la sudadera', 2.00, 'sin_pagar', '2026-06-11', '2026-06-11 06:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `quejas`
--

CREATE TABLE `quejas` (
  `id_queja` int(11) NOT NULL,
  `id_usuario_emisor` int(11) NOT NULL,
  `id_multa_referencia` int(11) NOT NULL,
  `motivo_queja` text NOT NULL,
  `estado` enum('pendiente','rechazada','aceptada') NOT NULL DEFAULT 'pendiente',
  `fecha_queja` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `quejas`
--

INSERT INTO `quejas` (`id_queja`, `id_usuario_emisor`, `id_multa_referencia`, `motivo_queja`, `estado`, `fecha_queja`) VALUES
(2, 4, 3, 'llegue 5 minutos tarde no 10', '', '2026-06-10 20:11:31'),
(6, 4, 7, 'Queja de prueba', '', '2026-06-11 01:53:00'),
(8, 4, 7, 'alkñdfjalñkdjfñadjfañdjfasdjfñlasd', 'pendiente', '2026-06-11 06:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(200) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','multero','jugador') NOT NULL DEFAULT 'jugador',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'admin', 'admin', 'admin@isunki.eus', '$2y$10$P4LajPK1MSbLsUoR4P08Hu7jVMgPaBNkpvsY8OrZdNymZPPYkHOKG', 'admin', '2026-06-10 11:41:34'),
(2, 'multero1', 'multero', 'multero@ejemplo.com', '$2y$10$0JTC7DgYTF.MItqOP9cX5Op74IMANgpbxjmufbft/4mICEXP7X9oS', 'multero', '2026-06-10 13:32:42'),
(4, 'jugador1', 'jugador', 'jugador@ejemplo.com', '$2y$10$/FBBaA6m/.JYk/AcTe.WOeAKvhzMLMRZiB3.kFLehLgEDdQd95McK', 'jugador', '2026-06-10 13:39:21'),
(5, 'jugador2', 'jugador', 'jugador2@ejemplo.com', '$2y$10$OV3stEcGoQC30Yl5QzVtSuLYO9rCrYU5E.Jxxos/vZxm0MHB6us8K', 'jugador', '2026-06-11 06:13:35'),
(6, 'josemi', 'garcia', 'josemi@correo.com', '$2y$10$hI2/X/XCiBJrg.CSxGQkHebq8gyDL.ysTpwHXg7Z36wR1gQlxyJLG', 'jugador', '2026-06-11 06:29:15'),
(7, 'erik', 'menendez', 'erik@correo.com', '$2y$10$JUNCN5h0TiQGRavgLagaW.gglT0IsQhsWPnkHHm.3oY1/Vg50NQX2', 'multero', '2026-06-11 06:31:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `multas`
--
ALTER TABLE `multas`
  ADD PRIMARY KEY (`id_multa`),
  ADD KEY `id_usuario_multado` (`id_usuario_multado`),
  ADD KEY `id_usuario_creador` (`id_usuario_creador`);

--
-- Indexes for table `quejas`
--
ALTER TABLE `quejas`
  ADD PRIMARY KEY (`id_queja`),
  ADD KEY `id_multa_referencia` (`id_multa_referencia`),
  ADD KEY `id_usuario_emisor` (`id_usuario_emisor`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `multas`
--
ALTER TABLE `multas`
  MODIFY `id_multa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quejas`
--
ALTER TABLE `quejas`
  MODIFY `id_queja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `multas`
--
ALTER TABLE `multas`
  ADD CONSTRAINT `multas_ibfk_1` FOREIGN KEY (`id_usuario_multado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `multas_ibfk_2` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Constraints for table `quejas`
--
ALTER TABLE `quejas`
  ADD CONSTRAINT `quejas_ibfk_1` FOREIGN KEY (`id_multa_referencia`) REFERENCES `multas` (`id_multa`) ON DELETE CASCADE,
  ADD CONSTRAINT `quejas_ibfk_2` FOREIGN KEY (`id_usuario_emisor`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
