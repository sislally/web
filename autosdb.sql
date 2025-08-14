-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-08-2025 a las 19:16:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `autosdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autos`
--

CREATE TABLE `autos` (
  `id_auto` int(11) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `año` year(4) DEFAULT NULL,
  `placa` varchar(20) DEFAULT NULL,
  `estado` enum('disponible','en alquiler','mantenimiento','no disponible') DEFAULT 'disponible',
  `precio_dia` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autos`
--

INSERT INTO `autos` (`id_auto`, `marca`, `modelo`, `tipo`, `año`, `placa`, `estado`, `precio_dia`, `imagen`) VALUES
(1, 'Toyota', 'Corolla', 'sedán', '2022', 'P123456', 'disponible', 25.00, '689a5c06d70f0_corolla.jpg'),
(2, 'Hyundai', 'Tucson', 'SUV', '2023', 'P234568', 'disponible', 40.00, '689a577d2184e_hyundai.jpg'),
(3, 'Nissan', 'Frontier', 'pickup', '2021', 'P345678', 'en alquiler', 35.00, '689a57b6efd25_nissan.jpg'),
(4, 'For Ranger Raptor', 'pickup', 'Raptor', '2025', 'A-12345', 'disponible', 50.00, '689a56fd25ce5_ford.jpg'),
(5, 'For Mustang GTD', 'For', 'Deportivo', '2025', '1A', 'disponible', 89.99, '689b5e0122bf2_mustang.jpg'),
(9, 'Auto Guisante', 'Guisante', 'Auto', '2000', '87654563', 'disponible', 25.87, '689bae8ac851c_car.jpg'),
(10, 'Ardidas', 'Ultimo', 'Deportivo', '2025', '736537365B', 'disponible', 100.99, '689c97afe2d87_mini.jpg'),
(11, 'Star Wars', 'Supra', 'Estandar', '2015', '736538776A', 'disponible', 80.01, '689c98b04b6eb_rum.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientos`
--

CREATE TABLE `mantenimientos` (
  `id_mantenimiento` int(11) NOT NULL,
  `id_auto` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `tipo` enum('preventivo','correctivo') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mantenimientos`
--

INSERT INTO `mantenimientos` (`id_mantenimiento`, `id_auto`, `descripcion`, `fecha_inicio`, `fecha_fin`, `tipo`) VALUES
(1, 2, 'Cambio de aceite y revisión general', '2025-07-25', '2025-07-28', 'preventivo'),
(2, 3, 'Reparación de frenos traseros', '2025-07-29', '2025-08-02', 'correctivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `multas`
--

CREATE TABLE `multas` (
  `id_multa` int(11) NOT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `tipo_multa` enum('retraso','daño') DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_multa` date DEFAULT NULL,
  `estado_pago` enum('pendiente','pagado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `multas`
--

INSERT INTO `multas` (`id_multa`, `id_reserva`, `tipo_multa`, `descripcion`, `monto`, `fecha_multa`, `estado_pago`) VALUES
(1, 1, 'retraso', 'Entrega del vehículo con 2 días de retraso', 50.00, '2025-08-07', 'pendiente'),
(2, 2, 'daño', 'Daño leve en la defensa trasera', 30.00, '2025-08-12', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado_pago` enum('pendiente','pagado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_reserva`, `monto_total`, `metodo_pago`, `fecha_pago`, `estado_pago`) VALUES
(7, 1, 100.00, 'tarjeta', '2025-07-01', 'pagado'),
(8, 2, 70.00, 'efectivo', '2025-08-10', 'pagado'),
(9, 2, 120000.00, 'efectivo', '2025-08-13', 'pagado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_auto` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado_reserva` enum('pendiente','confirmada','cancelada','finalizada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_usuario`, `id_auto`, `fecha_inicio`, `fecha_fin`, `estado_reserva`) VALUES
(1, 1, 1, '2025-08-01', '2025-08-05', 'confirmada'),
(2, 1, 3, '2025-08-10', '2025-08-12', 'pendiente'),
(3, 4, 3, '2025-07-24', '2025-08-07', 'confirmada'),
(4, 1, 10, '2025-08-13', '2025-08-15', 'pendiente'),
(5, 1, 10, '2025-08-13', '2025-08-15', 'pendiente'),
(6, 1, 10, '2025-08-13', '2025-08-15', 'pendiente'),
(7, 1, 10, '2025-08-13', '2025-08-15', 'pendiente'),
(8, 1, 4, '2025-08-19', '2025-08-28', 'pendiente'),
(9, 1, 4, '2025-08-19', '2025-08-28', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `dui` varchar(20) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `rol` enum('cliente','empleado','administrador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `email`, `telefono`, `direccion`, `dui`, `usuario`, `clave`, `rol`) VALUES
(1, 'okelin', 'okelincito@example.com', '87095678', 'Colonia Morazán', '93837738-1', 'oke', '$2y$10$OEXV67NNJZiuE7jFFC74De317nFw6BCqAk1Mq9eno5NJ7gYYbtTfe', 'cliente'),
(2, 'natasha', 'natti@example.com', '87095678', 'japon', '43432333-2', 'natti', '$2y$10$zFbjevN5ZWczAeKjxYc.ZexFNj9OPwYebURGk4.xGiwa61gyuR1b6', 'administrador'),
(3, 'sislally', 'sis@example.com', '73737339', 'delicias', '434322223-3', 'sis', '$2y$10$8v2P8xUeh4FhCrpkaRaq..kS9vnlHCRUHuTiUjuyJ/1HB.1c/hnkK', 'empleado'),
(4, 'Krissia', 'chocokrispi@example.com', '76563409', 'Su casa', '7656765-1', 'Madi', '$2y$10$YWzacellF0znVOxmz5zePeQUQMHxPUHKA6e4t4ydFoPZNqcQ43.lO', 'cliente'),
(5, 'Santa Clous', 'polonorte@example.com', '00000001', 'Polo Norte', '10101010-1', 'santa', '$2y$10$IZJhO6fJT3g6k2h5NlfVkOAHtL9IQbdBQWbLZROM/SVzxSMlNQ9T.', 'administrador'),
(8, 'Hiraku Kamiki', 'mataidols@example.com', '00000002', 'Tokio, Japon', '14141414-2', 'kamiki', '$2y$10$dsGUg88mhFunGtslD44o6e27L4j3X0XNruFMDo3Srzu0JEXhJxYCW', 'empleado');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autos`
--
ALTER TABLE `autos`
  ADD PRIMARY KEY (`id_auto`),
  ADD UNIQUE KEY `placa` (`placa`);

--
-- Indices de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `id_auto` (`id_auto`);

--
-- Indices de la tabla `multas`
--
ALTER TABLE `multas`
  ADD PRIMARY KEY (`id_multa`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_auto` (`id_auto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dui` (`dui`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autos`
--
ALTER TABLE `autos`
  MODIFY `id_auto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  MODIFY `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `multas`
--
ALTER TABLE `multas`
  MODIFY `id_multa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD CONSTRAINT `mantenimientos_ibfk_1` FOREIGN KEY (`id_auto`) REFERENCES `autos` (`id_auto`);

--
-- Filtros para la tabla `multas`
--
ALTER TABLE `multas`
  ADD CONSTRAINT `multas_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_auto`) REFERENCES `autos` (`id_auto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
