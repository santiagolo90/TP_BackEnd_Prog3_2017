-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-02-2018 a las 20:45:20
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
-- Estructura de tabla para la tabla `cocheras`
--

CREATE TABLE `cocheras` (
  `id` int(32) NOT NULL,
  `piso` int(20) NOT NULL,
  `numero` int(32) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `tipo` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cocheras`
--

INSERT INTO `cocheras` (`id`, `piso`, `numero`, `estado`, `tipo`) VALUES
(1, 1, 10, 'libre', 'especial'),
(2, 1, 11, 'libre', 'especial'),
(3, 1, 12, 'libre', 'especial'),
(4, 1, 13, 'libre', 'normal'),
(5, 1, 14, 'libre', 'normal'),
(6, 1, 15, 'ocupada', 'normal'),
(7, 2, 20, 'libre', 'normal'),
(8, 2, 21, 'libre', 'normal'),
(9, 2, 22, 'libre', 'normal'),
(10, 2, 23, 'libre', 'normal'),
(11, 2, 24, 'libre', 'normal'),
(12, 2, 25, 'libre', 'normal'),
(13, 3, 30, 'libre', 'normal'),
(14, 3, 31, 'libre', 'normal'),
(15, 3, 32, 'libre', 'normal'),
(16, 3, 33, 'libre', 'normal'),
(17, 3, 34, 'ocupado', 'normal'),
(18, 3, 35, 'ocupado', 'normal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int(32) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `sexo` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `turno` varchar(20) NOT NULL,
  `perfil` varchar(20) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `alta` varchar(50) NOT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id`, `nombre`, `sexo`, `email`, `clave`, `turno`, `perfil`, `foto`, `alta`, `estado`) VALUES
(1, 'adminUno', 'masculino', 'admin@admin.com', 'abc123', 'maniana', 'admin', 'fotosEmpleados/adminUno.png', '2017-12-15 18:04:04', 'activo'),
(23, 'usuarioUno', 'masculino', 'user@user.com', 'abc123', 'maniana', 'user', 'fotosEmpleados/usuarioUno.png', '2017-12-18 15:44:34', 'activo'),
(24, 'Juan', 'masculino', 'jcaver@user.com', 'abc123', 'maniana', 'user', 'fotosEmpleados/Juan.png', '2017-12-18 20:05:16', 'activo'),
(29, 'santiago', 'masculino', 'slopez@slopez.com', 'abc123', 'tarde', 'admin', 'fotosEmpleados/SANTIAGO.png', '2018-02-01 20:07:12', 'activo'),
(30, 'pepe', 'masculino', 'pepe@pepe.com', 'abc123', 'noche', 'user', 'fotosEmpleados/pepe.png', '2018-02-01 20:50:57', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estacionados`
--

CREATE TABLE `estacionados` (
  `id` int(32) NOT NULL,
  `patente` varchar(30) NOT NULL,
  `color` varchar(30) NOT NULL,
  `marca` varchar(30) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `idEmpleadoIngreso` int(32) NOT NULL,
  `fechaHoraIngreso` varchar(50) NOT NULL,
  `idCochera` int(32) NOT NULL,
  `idEmpleadoSalida` int(32) DEFAULT NULL,
  `fechaHoraSalida` varchar(50) DEFAULT NULL,
  `tiempoTrans` varchar(50) DEFAULT NULL,
  `importe` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estacionados`
--

INSERT INTO `estacionados` (`id`, `patente`, `color`, `marca`, `foto`, `idEmpleadoIngreso`, `fechaHoraIngreso`, `idCochera`, `idEmpleadoSalida`, `fechaHoraSalida`, `tiempoTrans`, `importe`) VALUES
(19, 'abc1233', 'rojo', 'Honda', 'fotosVehiculos/abc123.jpg', 23, '2018-01-25 15:14:46', 18, 24, '2018-01-25 19:38:59', '15853', -1),
(20, 'abc321', 'rojo', 'Honda', 'fotosVehiculos/abc123.jpg', 23, '2018-01-25 15:14:47', 18, 23, '2018-01-25 19:37:51', '15784', -1),
(21, 'abc456', 'rojo', 'Honda', 'fotosVehiculos/abc123.jpg', 23, '2018-01-25 15:20:50', 17, 23, '2018-01-25 19:39:15', '15505', -1),
(22, 'abc654', 'rojo', 'Honda', 'fotosVehiculos/abc123.jpg', 23, '2018-01-25 15:39:47', 17, 23, '2018-01-25 19:39:21', '14374', -1),
(27, 'abc123', 'rojo', 'Honda', 'fotosVehiculos/abc123.png', 24, '2018-01-29 14:38:00', 17, NULL, NULL, NULL, NULL),
(28, 'abc456', 'Blanco', 'Toyota', 'fotosVehiculos/abc456.png', 23, '2018-01-29 14:43:29', 18, NULL, NULL, NULL, NULL),
(29, 'abc789', 'Azul', 'Ford', 'fotosVehiculos/abc789.png', 24, '2018-01-29 15:06:26', 6, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialEmpleado`
--

CREATE TABLE `historialEmpleado` (
  `id` int(32) NOT NULL,
  `fecha` varchar(50) NOT NULL,
  `hora` varchar(50) NOT NULL,
  `idEmpleado` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `historialEmpleado`
--

INSERT INTO `historialEmpleado` (`id`, `fecha`, `hora`, `idEmpleado`) VALUES
(1, '2018-01-22', '20:38:59', 1),
(2, '2018-01-22', '20:40:07', 24),
(3, '2018-01-29', '15:05:30', 24),
(4, '2018-01-30', '15:53:16', 24),
(5, '2018-01-30', '15:53:28', 23),
(6, '2018-01-30', '15:54:25', 1),
(7, '2018-01-31', '18:48:14', 1),
(8, '2018-01-31', '19:18:10', 1),
(9, '2018-02-01', '18:02:07', 23),
(10, '2018-02-01', '18:12:27', 1),
(11, '2018-02-01', '18:13:48', 1),
(12, '2018-02-01', '18:19:45', 23),
(13, '2018-02-01', '18:20:14', 1),
(14, '2018-02-01', '20:09:16', 29),
(15, '2018-02-02', '14:06:51', 1),
(16, '2018-02-02', '14:12:30', 23);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cocheras`
--
ALTER TABLE `cocheras`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `historialEmpleado`
--
ALTER TABLE `historialEmpleado`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cocheras`
--
ALTER TABLE `cocheras`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `estacionados`
--
ALTER TABLE `estacionados`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `historialEmpleado`
--
ALTER TABLE `historialEmpleado`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
