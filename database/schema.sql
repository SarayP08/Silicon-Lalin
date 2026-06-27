-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2026 a las 11:16:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `material_deza`
--
CREATE DATABASE IF NOT EXISTS `material_deza` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `material_deza`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion`
--

CREATE TABLE `devolucion` (
  `id_devolucion` int(11) NOT NULL,
  `id_movimiento` int(11) DEFAULT NULL,
  `id_validador` int(11) DEFAULT NULL,
  `id_material` int(11) NOT NULL,
  `fecha_devolucion` datetime NOT NULL,
  `observaciones` varchar(220) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `devolucion`:
--   `id_material`
--       `material` -> `id_material`
--   `id_movimiento`
--       `movimiento_material` -> `id_movimiento`
--   `id_validador`
--       `usuario` -> `id_usuario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(550) NOT NULL,
  `categoria` enum('informatica','ofimatica','mobiliario','otros') NOT NULL,
  `estado` enum('alta','devolucion','reparacion','asignado','transferido','devuelto') NOT NULL DEFAULT 'asignado',
  `id_persona` int(11) DEFAULT NULL,
  `id_ubicacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `material`:
--   `id_ubicacion`
--       `ubicacion` -> `id_ubicacion`
--   `id_persona`
--       `persona` -> `id_persona`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_material`
--

CREATE TABLE `movimiento_material` (
  `id_movimiento` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_persona` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_origen` int(11) DEFAULT NULL,
  `id_destino` int(11) DEFAULT NULL,
  `tipo_movimiento` enum('alta','devolucion','reparacion','asignado','transferido','devuelto') NOT NULL DEFAULT 'alta',
  `fecha_movimiento` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `movimiento_material`:
--   `id_destino`
--       `ubicacion` -> `id_ubicacion`
--   `id_origen`
--       `ubicacion` -> `id_ubicacion`
--   `id_material`
--       `material` -> `id_material`
--   `id_persona`
--       `persona` -> `id_persona`
--   `id_usuario`
--       `usuario` -> `id_usuario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `nif` varchar(9) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellidos` varchar(40) NOT NULL,
  `correo` varchar(40) NOT NULL,
  `telefono` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `persona`:
--

--
-- Volcado de datos para la tabla `persona`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id_ubicacion` int(11) NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `CP` varchar(10) NOT NULL,
  `provincia` char(20) NOT NULL,
  `direccion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `ubicacion`:
--

--
-- Volcado de datos para la tabla `ubicacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellidos` varchar(40) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `rol` enum('admin','usuario','validador') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `usuario`:
--

--
-- Volcado de datos para la tabla `usuario`
--
INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellidos`, `email`, `password`, `activo`, `rol`) VALUES
(1, 'Administrador', 'Pérez', 'admin@gmail.com', '$2y$10$Zq5ON9CXJNkyWlZzLNmaY.bUqGRROW9QOeJVh9Xyu9cNpKgcWSlKG', 1, 'admin'),
(2, 'Usuario', 'Común', 'usuario@gmail.com', '$2y$10$cefa5eV7sQAD96FZkwx/NOFQe.V1Wc9fmjWdcNU1Zyf5dC12rw1fG', 1, 'usuario');
--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD PRIMARY KEY (`id_devolucion`),
  ADD KEY `fk_devolucion_material` (`id_material`),
  ADD KEY `fk_devolucion_validador` (`id_validador`),
  ADD KEY `fk_devolucion_movimiento` (`id_movimiento`);

--
-- Indices de la tabla `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_id_persona` (`id_persona`),
  ADD KEY `fk_id_ubicacion` (`id_ubicacion`);

--
-- Indices de la tabla `movimiento_material`
--
ALTER TABLE `movimiento_material`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `fk_movimiento_material` (`id_material`),
  ADD KEY `fk_material_persona` (`id_persona`),
  ADD KEY `fk_material_origen` (`id_origen`),
  ADD KEY `fk_material_destino` (`id_destino`),
  ADD KEY `fk_validador_devolucion` (`id_usuario`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `NIF` (`nif`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  MODIFY `id_devolucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_material`
--
ALTER TABLE `movimiento_material`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD CONSTRAINT `fk_devolucion_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_devolucion_movimiento` FOREIGN KEY (`id_movimiento`) REFERENCES `movimiento_material` (`id_movimiento`),
  ADD CONSTRAINT `fk_devolucion_validador` FOREIGN KEY (`id_validador`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `fk_id_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`),
  ADD CONSTRAINT `fk_material_id_persona` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`);

--
-- Filtros para la tabla `movimiento_material`
--
ALTER TABLE `movimiento_material`
  ADD CONSTRAINT `fk_material_destino` FOREIGN KEY (`id_destino`) REFERENCES `ubicacion` (`id_ubicacion`),
  ADD CONSTRAINT `fk_material_origen` FOREIGN KEY (`id_origen`) REFERENCES `ubicacion` (`id_ubicacion`),
  ADD CONSTRAINT `fk_movimiento_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_movimiento_persona` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`),
  ADD CONSTRAINT `fk_validador_devolucion` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
