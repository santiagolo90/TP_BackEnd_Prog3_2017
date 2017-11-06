-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-11-2017 a las 19:36:35
-- Versión del servidor: 10.1.28-MariaDB
-- Versión de PHP: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `estacionamiento`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int(32) NOT NULL,
  `nombre` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `sexo` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `turno` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `perfil` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id`, `nombre`, `sexo`, `email`, `clave`, `turno`, `perfil`) VALUES
(1, 'Jefe', 'masculino', 'admin@estacionar.com', 'admin123', 'mañana', 'administrador'),
(2, 'Empleado01', 'masculino', 'emp01@estacionar.com', 'emp123', 'mañana', 'usuario'),
(3, 'Empleado02', 'femenino', 'emp02@estacionar.com', 'emp456', 'tarde', 'usuario'),
(4, 'Empleado03', 'masculino', 'emp03@estacionar.com', 'emp789', 'noche', 'usuario'),
(5, '', '', '', '', '', ''),
(9, 'NombrePrueba', 'SexoPrueba', 'EmailPrueba', 'ClavePrueba', 'TurnoPrueb', 'PerfilPrueba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estacionados`
--

CREATE TABLE `estacionados` (
  `id` int(32) NOT NULL,
  `patente` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `color` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `marca` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `id_emp_ing` int(32) NOT NULL,
  `fecha_hora_ing` datetime NOT NULL,
  `id_emp_sal` int(32) DEFAULT NULL,
  `fecha_hora_sal` datetime DEFAULT NULL,
  `importe` double DEFAULT NULL,
  `tiempo_trans` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historico`
--

CREATE TABLE `historico` (
  `id` int(32) NOT NULL,
  `patente` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `fecha_hora_ing` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `fecha_hora_sal` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `empleado` varchar(32) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `importe` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estacionados`
--
ALTER TABLE `estacionados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `estacionados`
--
ALTER TABLE `estacionados`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historico`
--
ALTER TABLE `historico`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
