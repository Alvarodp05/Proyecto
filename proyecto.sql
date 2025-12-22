-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 18:55:44
-- Versión del servidor: 8.0.20
-- Versión de PHP: 8.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinas`
--

CREATE TABLE `maquinas` (
  `id_maquina` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `imagen` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `orden` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `maquinas`
--

INSERT INTO `maquinas` (`id_maquina`, `nombre`, `descripcion`, `imagen`, `link`, `orden`) VALUES
('maquina_0', 'SLOT VACÍO 0', 'Este slot está disponible.', '../../Lib/Imágenes/maquina_default.jpg', '#', 0),
('maquina_1', 'LAVADORA DE ACEITUNAS', 'La máquina retira arena, polvo, hojas y piedras mediante lavado con agua y aire (hidro-neumático) para proteger la piel.', '../../Lib/Imagenes/maquina1.jpg', 'ESTADO_A', 1),
('maquina_1_1', 'LAVADORA Y LIMPIADORA DE ACEITUNAS', 'El modelo de limpieza y lavado de aceitunas Agro STI 90 Compact es una máquina integral de formato mega que tiene como función principal, la separación de todos los subproductos provenientes de la cosecha manual y/o mecánica de la aceituna.', '../../Lib/Imagenes/maquina_1_1.jpg', '#', 99),
('maquina_2', 'CINTA TRANSPORTADORA', 'La máquina transporta las aceitunas desde tolva hacia el molino con control de velocidad y flujo.', '../../Lib/Imagenes/maquina2.jpg', '../Maquinas/maquina_jefe_2.php', 2),
('maquina_3', 'MOLINO DE ACEITUNAS', 'La máquina tritura la aceituna hasta obtener una pasta homogénea que facilite la extracción de aceite.', '../../Lib/Imagenes/maquina3.jpg', '../Maquinas/maquina_jefe_3.php', 3),
('maquina_4', 'BATIDORA DE PASTA', 'La máquina homogeneiza y acondiciona la pasta resultante del triturado.', '../../Lib/Imagenes/batidora.jpg', '../Maquinas/maquina_jefe_4.php', 4),
('maquina_5', 'DECANTADOR CENTRÍFUGO', 'La máquina separa el aceite de la pasta mediante la fuerza centrífuga.', '../../Lib/Imagenes/decantador.jpg', '../Maquinas/maquina_jefe_5.php', 5),
('maquina_6', 'TANQUE DE ALMACENAMIENTO', 'La máquina conserva el aceite en condiciones óptimas de calidad.', '../../Lib/Imagenes/tanque.jpg', '../Maquinas/maquina_jefe_6.php', 6),
('maquina_7', 'SLOT VACÍO 7', 'Este slot está disponible.', '../../Lib/Imágenes/maquina_default.jpg', '#', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros`
--

CREATE TABLE `parametros` (
  `id` int NOT NULL,
  `id_maquina` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `id_parametro` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `unidades` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `alarm_c_min` decimal(10,2) DEFAULT NULL,
  `alarm_c_max` decimal(10,2) DEFAULT NULL,
  `alarm_p_min` decimal(10,2) DEFAULT NULL,
  `alarm_p_max` decimal(10,2) DEFAULT NULL,
  `rand_min` decimal(10,2) DEFAULT NULL,
  `rand_max` decimal(10,2) DEFAULT NULL,
  `valor_actual` decimal(10,2) DEFAULT NULL,
  `valor_ayer` decimal(10,2) DEFAULT NULL,
  `valor_antier` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `parametros`
--

INSERT INTO `parametros` (`id`, `id_maquina`, `id_parametro`, `nombre`, `unidades`, `alarm_c_min`, `alarm_c_max`, `alarm_p_min`, `alarm_p_max`, `rand_min`, `rand_max`, `valor_actual`, `valor_ayer`, `valor_antier`) VALUES
(16, 'maquina_4', 'temperatura', 'Temperatura', '°C', 20.00, 30.00, 23.00, 27.00, 10.00, 40.00, NULL, NULL, NULL),
(17, 'maquina_4', 'vel_agit', 'Velocidad de agitación', 'rpm', 15.00, 60.00, 18.00, 54.00, 10.00, 70.00, NULL, NULL, NULL),
(18, 'maquina_4', 'potenc_motor', 'Potencia del motor eléctrico', 'kW', 5.00, 30.00, 7.00, 27.00, 1.00, 40.00, NULL, NULL, NULL),
(19, 'maquina_4', 'caudal', 'Caudal', 'L/h', 4.00, 8.00, 4.00, 8.00, 1.00, 10.00, NULL, NULL, NULL),
(24, 'maquina_6', 'temp_glob', 'Temperatura Global', '°C', 25.00, 70.00, 29.00, 62.00, 20.00, 80.00, 66.00, 71.00, 49.00),
(25, 'maquina_6', 'temp_aceit', 'Temperatura de Aceite', '°C', 40.00, 70.00, 45.00, 62.00, 30.00, 80.00, 46.00, 66.00, 71.00),
(26, 'maquina_6', 'caudal_aceit', 'Caudal de aceite', 'L/h', 5.00, 50.00, 7.00, 44.00, 1.00, 60.00, 40.00, 46.00, 49.00),
(27, 'maquina_6', 'pres_int', 'Presión interna', 'bar', 4.00, 8.00, 4.00, 8.00, 1.00, 10.00, 1.00, 1.00, 7.00),
(75, 'maquina_1_1', 'agua', 'agua', 'L/h', 40.00, 60.00, 45.00, 55.00, 0.00, 100.00, 58.00, 82.00, 72.00),
(76, 'maquina_1_1', 'capacidad_proceso', 'Capacidad del proceso', '[kg/h]', 4000.00, 6000.00, 4020.00, 5980.00, 3980.00, 6020.00, 5959.00, 5459.00, 4756.00),
(77, 'maquina_1_1', 'caudal_agua', 'Caudal de agua', '[L/h]', 2000.00, 3000.00, 2020.00, 2980.00, 1990.00, 3005.00, 2066.00, 2292.00, 2817.00),
(78, 'maquina_1_1', 'potencia_bomba', 'Potencia de la bomba', '[kW]', 3.50, 5.00, 3.60, 4.90, 3.40, 5.10, 4.00, 4.00, 4.00),
(79, 'maquina_1_1', 'velocidad_tambor', 'Velocidad del tambor', '[rpm]', 2500.00, 4000.00, 2550.00, 3990.00, 2470.00, 4010.00, 2730.00, 2542.00, 3219.00),
(85, 'maquina_1', 'Aire', 'Aire', 'km/h', 50.00, 70.00, 55.00, 65.00, 40.00, 70.00, 49.00, 70.00, 68.00),
(86, 'maquina_1', 'capacidad_proceso', 'Capacidad del proceso', '[kg/h]', 1000.00, 5500.00, 1100.00, 5300.00, 900.00, 5600.00, 3309.00, 1206.00, 2633.00),
(87, 'maquina_1', 'caudal_agua', 'Caudal de agua', '[L/h]', 1600.00, 3000.00, 1700.00, 2800.00, 1500.00, 3200.00, 3026.00, 2850.00, 2440.00),
(88, 'maquina_1', 'potencia_bomba', 'Potencia de la bomba', '[kW]', 1.50, 3.00, 1.60, 2.90, 1.00, 3.50, 1.00, 3.00, 3.00),
(89, 'maquina_1', 'velocidad_tambor', 'Velocidad del tambor', '[rpm]', 1500.00, 2800.00, 1600.00, 2800.00, 1400.00, 2900.00, 2737.00, 1525.00, 2118.00),
(95, 'maquina_2', 'anchura_cinturon', 'Anchura del cinturon', 'm', 1.50, 3.00, 1.60, 2.90, 1.00, 3.50, 2.00, 3.00, 1.00),
(96, 'maquina_2', 'perdida_elastica', 'Perdida elastica', '%', 1500.00, 2800.00, 1600.00, 2800.00, 1400.00, 2900.00, 2270.00, 2836.00, 2055.00),
(97, 'maquina_2', 'potencia_motor', 'Potencia del motor', 'kW', 1000.00, 5500.00, 1100.00, 5300.00, 900.00, 5600.00, 2350.00, 2951.00, 4995.00),
(98, 'maquina_2', 'roscas', 'roscas', 'unidades', 0.00, 100.00, 10.00, 90.00, 0.00, 100.00, 5.00, 71.00, 72.00),
(99, 'maquina_2', 'velocidad_cinturon', 'Velocidad del cinturon', 'm/s', 1600.00, 3000.00, 1700.00, 2800.00, 1500.00, 3200.00, 1743.00, 2718.00, 2897.00),
(100, 'maquina_3', 'apertura_criba', 'Apertura de la criba', 'mm', 1000.00, 5500.00, 1100.00, 5300.00, 900.00, 5600.00, 4456.00, 2167.00, 2110.00),
(101, 'maquina_3', 'potencia_motor', 'Potencia del motor', 'kW', 1500.00, 2800.00, 1600.00, 2800.00, 1400.00, 2900.00, 2091.00, 2606.00, 2126.00),
(102, 'maquina_3', 'temperatura_pasta', 'Temperatura de la Pasta', '°C', 1600.00, 3000.00, 1700.00, 2800.00, 1500.00, 3200.00, 2156.00, 1896.00, 2210.00),
(103, 'maquina_3', 'velocidad_rotor', 'Velocidad del rotor', 'rpm', 1.50, 3.00, 1.60, 2.90, 1.00, 3.50, 1.00, 3.00, 1.00),
(108, 'maquina_5', 'caudal_alim', 'Caudal de alimentación', 'L/h', 5.00, 50.00, 5.00, 45.00, 1.00, 60.00, 25.00, 54.00, 53.00),
(109, 'maquina_5', 'pres_casq', 'Presión de los casquillos', 'bar', 4.00, 8.00, 4.00, 8.00, 1.00, 10.00, 2.00, 10.00, 4.00),
(110, 'maquina_5', 'temp_aceit', 'Temperatura del aceite', '°C', 30.00, 90.00, 33.00, 81.00, 20.00, 100.00, 99.00, 81.00, 91.00),
(111, 'maquina_5', 'velocidad_rot', 'Velocidad de rotación', 'rpm', 2000.00, 5000.00, 2200.00, 4500.00, 1500.00, 5500.00, 3354.00, 5198.00, 5234.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `id` int NOT NULL,
  `id_maquina` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `id_stock` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `alarm_c_min` decimal(10,2) DEFAULT NULL,
  `alarm_c_max` decimal(10,2) DEFAULT NULL,
  `alarm_p_min` decimal(10,2) DEFAULT NULL,
  `alarm_p_max` decimal(10,2) DEFAULT NULL,
  `rand_min` decimal(10,2) DEFAULT NULL,
  `rand_max` decimal(10,2) DEFAULT NULL,
  `valor_actual` decimal(10,2) DEFAULT NULL,
  `valor_ayer` decimal(10,2) DEFAULT NULL,
  `valor_antier` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`id`, `id_maquina`, `id_stock`, `nombre`, `alarm_c_min`, `alarm_c_max`, `alarm_p_min`, `alarm_p_max`, `rand_min`, `rand_max`, `valor_actual`, `valor_ayer`, `valor_antier`) VALUES
(17, 'maquina_4', 'stock_palas', 'Palas', 3.00, 5.00, 3.00, 5.00, 1.00, 8.00, NULL, NULL, NULL),
(18, 'maquina_4', 'stock_filtro', 'Filtro de aire', 2.00, 5.00, 2.00, 5.00, 1.00, 7.00, NULL, NULL, NULL),
(19, 'maquina_4', 'stock_valv', 'Válvulas', 2.00, 8.00, 3.00, 7.00, 1.00, 10.00, NULL, NULL, NULL),
(20, 'maquina_4', 'stock_rodamientos', 'Rodamientos', 3.00, 7.00, 3.00, 7.00, 1.00, 10.00, NULL, NULL, NULL),
(25, 'maquina_6', 'stock_recub', 'Recubrimiento interno', 3.00, 5.00, 3.00, 5.00, 1.00, 8.00, 4.00, 5.00, NULL),
(26, 'maquina_6', 'stock_bisagras', 'Bisagras', 2.00, 5.00, 2.00, 5.00, 1.00, 7.00, 3.00, 6.00, NULL),
(27, 'maquina_6', 'stock_res', 'Resistencias Eléctricas', 2.00, 5.00, 2.00, 5.00, 1.00, 7.00, 7.00, 3.00, NULL),
(28, 'maquina_6', 'stock_valv', 'Válvulas de control', 3.00, 7.00, 3.00, 7.00, 1.00, 10.00, 1.00, 8.00, NULL),
(71, 'maquina_1_1', 'stock_impulsor', 'Impulsor de la bomba', 1.00, 999.00, 2.00, 999.00, 1.00, 10.00, 3.00, 10.00, NULL),
(72, 'maquina_1_1', 'stock_mallas', 'Mallas filtrantes de agua', 1.00, 999.00, 2.00, 999.00, 1.00, 10.00, 5.00, 6.00, NULL),
(73, 'maquina_1_1', 'stock_piezas', 'Piezas de descarga', 1.00, 999.00, 2.00, 999.00, 1.00, 10.00, 3.00, 3.00, NULL),
(74, 'maquina_1_1', 'stock_retenes', 'Retenes de empaquetadura', 2.00, 999.00, 3.00, 999.00, 1.00, 10.00, 6.00, 2.00, NULL),
(79, 'maquina_1', 'stock_impulsor', 'Impulsor de bomba', 1.00, 999.00, 3.00, 999.00, 0.00, 10.00, 0.00, 6.00, NULL),
(80, 'maquina_1', 'stock_mallas', 'Mallas filtrantes', 1.00, 999.00, 3.00, 999.00, 0.00, 10.00, 2.00, 10.00, NULL),
(81, 'maquina_1', 'stock_piezas', 'Piezas de descarga', 1.00, 999.00, 3.00, 999.00, 0.00, 10.00, 8.00, 8.00, NULL),
(82, 'maquina_1', 'stock_retenes', 'Retenes de empaquetadura', 2.00, 999.00, 4.00, 999.00, 0.00, 10.00, 4.00, 9.00, NULL),
(89, 'maquina_2', 'cinta', 'cinta', 0.00, 100.00, 10.00, 90.00, 0.00, 100.00, 29.00, 69.00, NULL),
(90, 'maquina_2', 'muelle', 'muelle', 0.00, 100.00, 10.00, 90.00, 0.00, 1000.00, 333.00, 247.00, NULL),
(91, 'maquina_2', 'stock_correa', 'Correa transportadora', 1.00, 999.00, 2.00, 999.00, 0.00, 10.00, 8.00, 7.00, NULL),
(92, 'maquina_2', 'stock_engranajes', 'Engranajes', 1.00, 999.00, 2.00, 999.00, 0.00, 10.00, 2.00, 1.00, NULL),
(93, 'maquina_2', 'stock_rodillos', 'Rodillos tensores', 2.00, 999.00, 3.00, 999.00, 0.00, 10.00, 5.00, 3.00, NULL),
(94, 'maquina_2', 'stock_tornilleria', 'Tornilleria de fijacion', 1.00, 999.00, 2.00, 999.00, 0.00, 10.00, 6.00, 3.00, NULL),
(95, 'maquina_3', 'stock_cribas', 'Cribas (rejillas)', 1.00, 999.00, 2.00, 999.00, 0.00, 10.00, 4.00, 10.00, NULL),
(96, 'maquina_3', 'stock_martillo', 'Martillos de Impacto', 2.00, 999.00, 3.00, 999.00, 0.00, 10.00, 8.00, 3.00, NULL),
(97, 'maquina_3', 'stock_rodamiento', 'Rodamientos del Eje', 1.00, 999.00, 2.00, 999.00, 0.00, 10.00, 4.00, 5.00, NULL),
(98, 'maquina_3', 'stock_sellos', 'Juegos de sellos', 1.00, 999.00, 2.00, 999.00, 0.00, 10.00, 10.00, 8.00, NULL),
(104, 'maquina_5', 'stock_aceit', 'Aceite lubricante', 10.00, 15.00, 12.00, 13.00, 5.00, 20.00, 19.00, 7.00, NULL),
(105, 'maquina_5', 'stock_amort', 'Amortiguadores antivibraciones', 4.00, 8.00, 4.00, 8.00, 1.00, 10.00, 5.00, 1.00, NULL),
(106, 'maquina_5', 'stock_filtro', 'Filtro de aceite', 4.00, 8.00, 4.00, 8.00, 1.00, 10.00, 3.00, 3.00, NULL),
(107, 'maquina_5', 'stock_sensor', 'Sensor de velocidad', 4.00, 8.00, 4.00, 8.00, 1.00, 10.00, 2.00, 5.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `rol` enum('jefe','empleado') CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`nombre`, `clave`, `rol`) VALUES
('empleado1', '$2y$10$5D/DMAz2v.cj0k0XTxTeNeRAKuWxXIsQYoT7EF1xAxSI2GWZLlBRu', 'empleado'),
('empleado2', '$2y$10$rcfG9e3c3QjOaUAd/qzjqONft6QNkj4GdN4qVQQ85i9ZzAxY6E6Ym', 'empleado'),
('empleado3', '$2y$10$Z9qrmwzdpKkmjKHGJliKPO56yTjPehrEYyqXJo7ihOMZCcJsP/KC.', 'empleado'),
('empleado4', '$2y$10$aXn8rmGQOjt/8f6GBylhB.mMpJGRwv3t2C2xexoaN5P3UxHS61CH.', 'empleado'),
('empleado5', '$2y$10$q.2JUzSbiXkIVYhjF5COgeKbSMBASAsbHpT46ODi2NCfUBr9/L1a.', 'empleado'),
('jefe1', '$2y$10$Y3q2UJTpM5rWvUOwIwlTre2ZIhx535LP7X4hALmT4E6IXLxVidbsa', 'jefe'),
('jefe2', '$2y$10$Xv544xFxk9h9hgmn2pAHsOZnbeezaEADyOcY8/HPWofksNMqpDEu.', 'jefe'),
('jefe3', '$2y$10$4XMsTBClwvtFXsGjH/eiZe8/j1c4L5wAgxHwS0BV.3b6i7hBi4PIq', 'jefe');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `maquinas`
--
ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id_maquina`);

--
-- Indices de la tabla `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_maquina` (`id_maquina`,`id_parametro`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_maquina` (`id_maquina`,`id_stock`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `parametros`
--
ALTER TABLE `parametros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `parametros`
--
ALTER TABLE `parametros`
  ADD CONSTRAINT `parametros_ibfk_1` FOREIGN KEY (`id_maquina`) REFERENCES `maquinas` (`id_maquina`) ON DELETE CASCADE;

--
-- Filtros para la tabla `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_maquina`) REFERENCES `maquinas` (`id_maquina`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
