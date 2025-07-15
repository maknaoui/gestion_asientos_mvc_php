--
-- Table structure for table `asientos`
--

CREATE TABLE `asientos` (
  `id` int NOT NULL,
  `codigo` varchar(10) NOT NULL DEFAULT '',
  `top_pos` int NOT NULL DEFAULT '0',
  `left_pos` int NOT NULL DEFAULT '0',
  `discapacitado` tinyint(1) NOT NULL DEFAULT '0',
  `estado` enum('habilitado','deshabilitado') NOT NULL DEFAULT 'habilitado',
  `evento_id` int NOT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_por` int NOT NULL DEFAULT '0',
  `fecha_eliminacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `eventos`
--

CREATE TABLE `eventos` (
  `id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `descripcion` text,
  `maxima_capacidad` int NOT NULL DEFAULT '0',
  `tipo_id` int NOT NULL,
  `fecha_evento` datetime NOT NULL,
  `eliminado` int NOT NULL DEFAULT '0',
  `eliminado_por` int NOT NULL DEFAULT '0',
  `fecha_eliminacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `reservas`
--

CREATE TABLE `reservas` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `apellido` varchar(50) NOT NULL DEFAULT '',
  `correo` varchar(100) NOT NULL DEFAULT '',
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(20) NOT NULL DEFAULT '',
  `rut` varchar(20) NOT NULL DEFAULT '',
  `numero_personas` int NOT NULL DEFAULT '1',
  `evento_id` int NOT NULL,
  `fecha_reserva` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','confirmado','rechazado') NOT NULL DEFAULT 'pendiente',
  `aprobado_por` int NOT NULL DEFAULT '0',
  `fecha_aprobacion` datetime DEFAULT NULL,
  `eliminado` int NOT NULL DEFAULT '0',
  `eliminado_por` int NOT NULL DEFAULT '0',
  `fecha_eliminacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `reserva_asientos`
--

CREATE TABLE `reserva_asientos` (
  `id` int NOT NULL,
  `reserva_id` int NOT NULL,
  `asiento_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `tipo_eventos`
--

CREATE TABLE `tipo_eventos` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `matrix` text NOT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `apellido` varchar(50) NOT NULL DEFAULT '',
  `correo` varchar(255) NOT NULL DEFAULT '',
  `clave` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo`, `clave`) VALUES
(1, 'Yassine', 'Maknaoui', 'maknaoui.yassine@gmail.com', '$2y$10$a1Z8M98dTxZ5Lirzbuu0GOtfsNJEYaVdGhQPPtSkH7SGm3./U6afy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asientos`
--
ALTER TABLE `asientos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reserva_asientos`
--
ALTER TABLE `reserva_asientos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipo_eventos`
--
ALTER TABLE `tipo_eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--
