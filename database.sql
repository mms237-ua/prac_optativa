-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-12-2025 a las 10:10:48
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
-- Base de datos: `prac_optativa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `imagen_principal` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`id`, `usuario_id`, `titulo`, `descripcion`, `precio`, `imagen_principal`, `fecha_creacion`, `fecha_actualizacion`, `activo`) VALUES
(1, 1, 'iPhone 13 Pro Max', 'Teléfono en perfecto estado, con caja y accesorios', 799.99, 'uploads/fotos/foto1.jpg', '2025-12-18 07:37:33', '2025-12-18 08:20:01', 1),
(2, 1, 'Bicicleta de montaña', 'Bicicleta seminueva, ideal para rutas', 350.00, 'uploads/fotos/foto1.jpg', '2025-12-18 07:37:33', '2025-12-18 08:22:55', 1),
(3, 2, 'PlayStation 5', 'Consola nueva, con 2 mandos', 499.99, 'uploads/fotos/foto2.jpg', '2025-12-18 07:37:33', '2025-12-18 08:22:55', 1),
(4, 2, 'Portátil Gaming MSI', 'RTX 3060, 16GB RAM, SSD 512GB', 1200.00, 'uploads/fotos/foto3.jpg', '2025-12-18 07:37:33', '2025-12-18 08:22:55', 1),
(5, 1, 'Cámara Canon EOS R6', 'Cámara profesional, poco uso', 2100.00, 'uploads/fotos/foto4.jpeg', '2025-12-18 07:37:33', '2025-12-18 08:22:55', 1),
(6, 1, 'iPhone 13 Pro Max', 'Teléfono en perfecto estado, con caja y accesorios', 799.99, 'uploads/fotos/foto5.jpg', '2025-12-18 07:48:05', '2025-12-18 08:22:55', 1),
(7, 1, 'Bicicleta de montaña', 'Bicicleta seminueva, ideal para rutas', 350.00, 'uploads/fotos/foto6.jpg', '2025-12-18 07:48:05', '2025-12-18 08:22:55', 1),
(8, 2, 'PlayStation 5', 'Consola nueva, con 2 mandos', 499.99, 'uploads/fotos/foto7.jpg', '2025-12-18 07:48:05', '2025-12-18 08:53:41', 1),
(9, 2, 'Portátil Gaming MSI', 'RTX 3060, 16GB RAM, SSD 512GB', 1200.00, 'uploads/fotos/foto8.jpg', '2025-12-18 07:48:05', '2025-12-18 08:22:55', 1),
(10, 1, 'Cámara Canon EOS R6', 'Cámara profesional, poco uso', 2100.00, 'uploads/fotos/profile-picture.jpeg', '2025-12-18 07:48:05', '2025-12-18 08:22:55', 1),
(11, 1, 'cosa', 'hola', 10.00, 'uploads/fotos/img_6943be7158b4f2.61745658.jpeg', '2025-12-18 08:42:20', '2025-12-18 08:42:25', 1),
(12, 1, 'cosa', 'asdfg', 1234.00, 'uploads/fotos/img_6943c3cc1ec330.90156063.jpg', '2025-12-18 09:05:09', '2025-12-18 09:05:16', 1),
(13, 1, 'hola', 'dfghj', 34567.00, 'uploads/fotos/img_6943c3e0431350.79971401.jpg', '2025-12-18 09:05:31', '2025-12-18 09:05:36', 1),
(14, 1, 'PlayStation 5', 'w345tyui', 234567.00, 'uploads/fotos/img_6943c3fa4fe485.55929748.jpg', '2025-12-18 09:05:55', '2025-12-18 09:06:02', 1),
(15, 1, 'PlayStation 5', 'ASDFGH', 2345.00, 'uploads/fotos/img_6943c41691ddc0.51429357.jpg', '2025-12-18 09:06:20', '2025-12-18 09:06:30', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `id` int(11) NOT NULL,
  `anuncio_id` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_original` varchar(255) NOT NULL,
  `ruta_miniatura` varchar(255) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `texto_alternativo` varchar(200) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fotos`
--

INSERT INTO `fotos` (`id`, `anuncio_id`, `nombre_archivo`, `ruta_original`, `ruta_miniatura`, `titulo`, `texto_alternativo`, `orden`, `fecha_subida`) VALUES
(1, 1, 'foto1.jpg', 'uploads/fotos/foto1.jpg', 'uploads/fotos/foto1.jpg', 'Foto 1', 'Foto 1', 1, '2025-12-18 08:10:24'),
(2, 1, 'foto2.jpg', 'uploads/fotos/foto2.jpg', 'uploads/fotos/foto2.jpg', 'Foto 2', 'Foto 2', 2, '2025-12-18 08:10:24'),
(3, 1, 'foto3.jpg', 'uploads/fotos/foto3.jpg', 'uploads/fotos/foto3.jpg', 'Foto 3', 'Foto 3', 3, '2025-12-18 08:10:24'),
(4, 1, 'foto4.jpg', 'uploads/fotos/foto4.jpeg', 'uploads/fotos/foto4.jpg', 'Foto 4', 'Foto 4', 4, '2025-12-18 08:10:24'),
(5, 1, 'foto5.jpg', 'uploads/fotos/foto5.jpg', 'uploads/fotos/foto5.jpg', 'Foto 5', 'Foto 5', 5, '2025-12-18 08:10:24'),
(6, 1, 'foto6.jpg', 'uploads/fotos/foto6.jpg', 'uploads/fotos/foto6.jpg', 'Foto 6', 'Foto 6', 6, '2025-12-18 08:10:24'),
(7, 1, 'foto7.jpg', 'uploads/fotos/foto7.jpg', 'uploads/fotos/foto7.jpg', 'Foto 7', 'Foto 7', 7, '2025-12-18 08:10:24'),
(8, 1, 'foto8.jpg', 'uploads/fotos/foto8.jpg', 'uploads/fotos/foto8.jpg', 'Foto 8', 'Foto 8', 8, '2025-12-18 08:10:24'),
(9, 1, 'foto9.jpg', 'uploads/fotos/foto9.jpg', 'uploads/fotos/profile-picture.jpeg', 'Foto 9', 'Foto 9', 9, '2025-12-18 08:10:24'),
(10, 1, 'foto1.jpg', 'uploads/fotos/foto1.jpg', 'uploads/fotos/foto1.jpg', 'Foto 1', 'Foto 1', 1, '2025-12-18 08:11:54'),
(37, 6, 'logo.png', 'uploads/fotos/img_6943be3fd67513.60390898.png', 'uploads/miniaturas/img_6943be3fd67513.60390898.png', 'logo', 'logo', 0, '2025-12-18 08:41:35'),
(38, 11, '1764272174_foto_piso6.jpeg', 'uploads/fotos/img_6943be7158b4f2.61745658.jpeg', 'uploads/miniaturas/img_6943be7158b4f2.61745658.jpeg', '1764272174_foto_piso6', '1764272174_foto_piso6', 0, '2025-12-18 08:42:25'),
(39, 3, 'maria.jpg', 'uploads/fotos/img_6943c0a78cd6f9.14826119.jpg', 'uploads/miniaturas/img_6943c0a78cd6f9.14826119.jpg', 'maria', 'maria', 0, '2025-12-18 08:51:51'),
(40, 8, 'logo.png', 'uploads/fotos/img_6943c0b77c99f6.09742186.png', 'uploads/miniaturas/img_6943c0b77c99f6.09742186.png', 'logo', 'logo', 0, '2025-12-18 08:52:07'),
(41, 11, 'foto_piso5.jpeg', 'uploads/fotos/img_6943c21fd040b6.09420871.jpeg', 'uploads/miniaturas/img_6943c21fd040b6.09420871.jpeg', 'foto_piso5', 'foto_piso5', 0, '2025-12-18 08:58:07'),
(42, 11, 'foto_piso5.jpeg', 'uploads/fotos/img_6943c251be3a33.63458368.jpeg', 'uploads/miniaturas/img_6943c251be3a33.63458368.jpeg', 'foto_piso5', 'foto_piso5', 0, '2025-12-18 08:58:57'),
(43, 12, 'casa1.jpg', 'uploads/fotos/img_6943c3cc1ec330.90156063.jpg', 'uploads/miniaturas/img_6943c3cc1ec330.90156063.jpg', 'casa1', 'casa1', 0, '2025-12-18 09:05:16'),
(44, 13, 'foto_piso2.jpg', 'uploads/fotos/img_6943c3e0431350.79971401.jpg', 'uploads/miniaturas/img_6943c3e0431350.79971401.jpg', 'foto_piso2', 'foto_piso2', 0, '2025-12-18 09:05:36'),
(45, 14, 'anun_1_5_1765365845_0b3a4b33.jpg', 'uploads/fotos/img_6943c3fa4fe485.55929748.jpg', 'uploads/miniaturas/img_6943c3fa4fe485.55929748.jpg', 'anun_1_5_1765365845_0b3a4b33', 'anun_1_5_1765365845_0b3a4b33', 0, '2025-12-18 09:06:02'),
(46, 15, 'foto3.jpg', 'uploads/fotos/img_6943c41691ddc0.51429357.jpg', 'uploads/miniaturas/img_6943c41691ddc0.51429357.jpg', 'foto3', 'foto3', 0, '2025-12-18 09:06:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `email`, `nombre_completo`, `fecha_registro`) VALUES
(1, 'usuario1', '$2y$10$pmYH/MzwgL5rMBo3QuzVR.dhNhpvWVuI1OnKy97cRnjmFMHjj1zQW', 'usuario1@test.com', 'Usuario Uno', '2025-12-18 07:37:33'),
(2, 'usuario2', '$2y$10$pmYH/MzwgL5rMBo3QuzVR.dhNhpvWVuI1OnKy97cRnjmFMHjj1zQW', 'usuario2@test.com', 'Usuario Dos', '2025-12-18 07:37:33'),
(3, 'admin', '$2y$10$pmYH/MzwgL5rMBo3QuzVR.dhNhpvWVuI1OnKy97cRnjmFMHjj1zQW', 'admin@test.com', 'Administrador', '2025-12-18 07:37:33');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha_creacion`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_anuncio` (`anuncio_id`),
  ADD KEY `idx_fecha` (`fecha_subida`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD CONSTRAINT `anuncios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fotos_ibfk_1` FOREIGN KEY (`anuncio_id`) REFERENCES `anuncios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
