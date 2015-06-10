-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2015 a las 21:42:16
-- Versión del servidor: 5.6.21
-- Versión de PHP: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `csgoplay`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campeonato`
--

CREATE TABLE IF NOT EXISTS `campeonato` (
  `id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campeonato_equipo`
--

CREATE TABLE IF NOT EXISTS `campeonato_equipo` (
  `equipo_id` int(11) NOT NULL,
  `campeonato_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE IF NOT EXISTS `comentario` (
`id` int(11) NOT NULL,
  `texto` text NOT NULL,
  `fecha` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `noticia_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `comentario`
--

INSERT INTO `comentario` (`id`, `texto`, `fecha`, `usuario_id`, `noticia_id`) VALUES
(1, 'Esto es un comentario del user5 a una noticia escita por Administrador', '2015-05-24 00:00:00', 5, 4),
(2, 'Esto es un comentario del user2 a una noticia escita por Administrador', '2015-05-24 00:00:00', 2, 4),
(3, 'Esto es un comentario del user3 a la noticia escrito por user2', '2015-05-23 00:00:00', 3, 3),
(4, 'Esto es un comentario escrito por user4 a la noticia escrita por user2', '2015-05-22 00:00:00', 4, 3),
(5, 'Mola!', '2015-06-09 19:50:23', 1, 3),
(6, 'Hola?', '2015-06-09 20:00:43', 1, 3),
(7, 'vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett vendo opel kadett ', '2015-06-09 20:06:17', 6, 2),
(8, 'he dicho que vendo opel kadet??\r\n', '2015-06-09 20:08:15', 6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE IF NOT EXISTS `equipo` (
`id` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `logo` varchar(100) DEFAULT '/imagenes/interrogacion.jpg',
  `capitan_id` int(11) NOT NULL,
  `fecha_creacion` varchar(45) NOT NULL,
  `web` varchar(45) DEFAULT NULL,
  `grupo_steam` varchar(45) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`id`, `nombre`, `logo`, `capitan_id`, `fecha_creacion`, `web`, `grupo_steam`) VALUES
(1, 'Dignitas', '/imagenes/dignitas.jpg', 6, '2015/05/04', 'http://www.team-dignitas.net/', 'groups/dignitas'),
(2, 'DinamicCS', '/imagenes/interrogacion.jpg', 2, '2015/05/12', 'www.dinamicCS.com', 'groups/dinamicCs'),
(14, 'Equipo A', '/imagenes/interrogacion.jpg', 11, '2015/06/01', 'www.equipoAfalso.com', 'www.steam.com/equip0a'),
(15, 'Equipo B', '/imagenes/interrogacion.jpg', 17, '2015/06/08', 'www.equipob.com', 'www.steam.com/equipoB'),
(16, 'Equipo C', '/imagenes/interrogacion.jpg', 22, '2015/06/08', 'www.foro.equiC.com', 'www.steam.com/equipoC'),
(18, 'EQuipo i', '/imagenes/interrogacion.jpg', 47, '2015/06/08', 'www.equipoi.com', 'www.steam.com/equipoI'),
(19, 'Equipo J', '/imagenes/interrogacion.jpg', 52, '2015/06/08', 'www.equipoJ.com', 'www.steram.com/equipoj'),
(20, 'Equipo F', '/imagenes/interrogacion.jpg', 32, '2015/06/08', 'www.equipoF.com', 'www.steam.com/equipoF'),
(23, 'Equipo E', '/imagenes/interrogacion.jpg', 27, '2015/06/08', 'www.equipoE.com', 'www.steam.com/equipoE'),
(25, 'Equipo G', '/imagenes/interrogacion.jpg', 37, '2015/05/19', 'www.equifoG.com', 'www.steam.com/equipoG'),
(26, 'Equipo H', '/imagenes/interrogacion.jpg', 42, '2015/04/30', 'www.equipoh.com', 'www.steam.com/equipoH.com'),
(27, 'Equipo K', '/imagenes/interrogacion.jpg', 57, '2015/06/01', 'www.equipok.com', 'www.steam.com/equipok'),
(28, 'Equipo L', '/imagenes/interrogacion.jpg', 62, '2015/05/01', 'www.equifoL.com', 'www.steam.com/equipoL'),
(29, 'Equipo M', '/imagenes/interrogacion.jpg', 67, '2015/03/30', 'www.equipom.com', 'www.steam.com/equipoM'),
(30, 'Equipo N', '/imagenes/interrogacion.jpg', 72, '2015/06/08', 'www.equifoN.com', 'www.steam.com/equipoN'),
(31, 'Equipo O', '/imagenes/interrogacion.jpg', 77, '2015/06/09', 'www.equipoO.com', 'www.steam.com/equipoO'),
(32, 'Equipo P', '/imagenes/interrogacion.jpg', 82, '2015/03/12', 'www.equipoP.com', 'www.steam.com/equipoP'),
(33, 'Equipo Q', '/imagenes/interrogacion.jpg', 87, '2015/05/02', 'www.equipoQ.com', 'www.steam.com/equipoQ'),
(34, 'Equipo R', '/imagenes/interrogacion.jpg', 92, '2015/03/26', 'www.equipor.com', 'www.steam.com/equipor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_fase`
--

CREATE TABLE IF NOT EXISTS `equipo_fase` (
  `equipo_id` int(11) NOT NULL,
  `fase_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_usuario`
--

CREATE TABLE IF NOT EXISTS `equipo_usuario` (
`id` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','aprobada','denegada','') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `equipo_usuario`
--

INSERT INTO `equipo_usuario` (`id`, `equipo_id`, `usuario_id`, `estado`) VALUES
(1, 14, 1, 'pendiente'),
(2, 15, 18, 'pendiente'),
(3, 18, 48, 'pendiente'),
(5, 16, 6, 'pendiente'),
(6, 1, 1, 'denegada'),
(7, 23, 28, 'aprobada'),
(8, 23, 29, 'aprobada'),
(9, 23, 30, 'aprobada'),
(10, 23, 31, 'aprobada'),
(11, 19, 53, 'aprobada'),
(12, 19, 54, 'aprobada'),
(13, 19, 55, 'aprobada'),
(14, 19, 1, 'denegada'),
(15, 19, 1, 'denegada'),
(16, 23, 1, 'pendiente'),
(17, 19, 1, 'denegada'),
(18, 19, 56, 'aprobada'),
(19, 1, 56, 'denegada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fase`
--

CREATE TABLE IF NOT EXISTS `fase` (
`id` int(11) NOT NULL,
  `fase` varchar(45) NOT NULL,
  `terminada` tinyint(1) NOT NULL DEFAULT '0',
  `campeonato_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE IF NOT EXISTS `mensaje` (
`id` int(11) NOT NULL,
  `remitente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` date NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `mensaje`
--

INSERT INTO `mensaje` (`id`, `remitente_id`, `usuario_id`, `asunto`, `mensaje`, `fecha`, `leido`) VALUES
(1, 5, 1, 'Asunto User5 - Admin', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Unde blanditiis ipsam at dolores pariatur repudiandae atque excepturi sit reprehenderit, reiciendis officiis, necessitatibus amet eligendi deleniti rerum a? Porro, commodi, architecto. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil praesentium natus hic recusandae? Aliquid modi non cumque hic in sed, laudantium, similique laborum, ipsam voluptatum dolor assumenda iste ex tenetur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.     ', '2015-05-24', 1),
(2, 4, 1, 'Asunto User4 - Admin', 'Porro, commodi, architecto. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil praesentium natus hic recusandae? Aliquid modi non cumque hic in sed, laudantium, similique laborum, ipsam voluptatum dolor assumenda iste ex tenetur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-20', 1),
(3, 2, 5, 'Asunto User2 - User5', 'um, similique laborum, ipsam voluptatum dolor assumenda iste ex tenetur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptaPorro, commodi, architecto. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil praesentium natus hic recusandae? Aliquid modi non cumque hic in sed, laudantium, similique laborum, ipsam voluptatum dolor assumenda iste ex tenetur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.s, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-14', 1),
(4, 1, 5, 'Asunto Admin - User5', 'sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.Porro, commodi, architecto. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil praesentium natus hic recusandae? Aliquid modi non cumque hic in sed, laudantium, similique laborum, ipsam voluptatum dolor assumenda iste ex tenetur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-23', 0),
(5, 5, 2, 'Asunto user5-user2', 'rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-12', 1),
(6, 3, 2, 'Asunto User3-User2', 'rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '0000-00-00', 1),
(7, 1, 3, 'Asunto Admin-User3', 'rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-27', 0),
(8, 5, 3, 'Asunto User5-User3', 'dfdfgdfgggrporis dolor impedit quasdfi, dfgdfgarchitecto nihil nam eveniet offidfgcia, sunt dicta vitae? Vero mollitiafg autem, recusandae quaerat ad.rporis gddolor impedit quasi, architecto nihigdfl nam eveniet officia, sunt dicta vidftae? Vero mollitia autem, recusandagdfge quaerat ad.sum dolor sit amet, dfgdsfgdfconsectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-24', 0),
(9, 3, 4, 'Asunto User3-User4', 'rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-01', 0),
(10, 2, 4, 'Asunto User2-User4', 'rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.rporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.sum dolor sit amet, consectetur adipisicing elit. Id distinctio voluptas, mollitia corporis dolor impedit quasi, architecto nihil nam eveniet officia, sunt dicta vitae? Vero mollitia autem, recusandae quaerat ad.', '2015-05-28', 0),
(11, 1, 4, 'Re:Asunto User4 - Admin', 'Hola user4 ,soy admin', '2015-05-31', 0),
(12, 1, 5, 'NUEVO MENSAJE Admin a user ', 'Esto es un nuevo mensaje escrito desde nuevomensaje', '2015-05-31', 1),
(13, 5, 1, 'Re:NUEVO MENSAJE Admin a user ', 'ok, recibido', '2015-05-31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia`
--

CREATE TABLE IF NOT EXISTS `noticia` (
`id` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `texto` text NOT NULL,
  `fecha` date NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `noticia`
--

INSERT INTO `noticia` (`id`, `titulo`, `texto`, `fecha`, `usuario_id`) VALUES
(1, '80.000$ para la Gfinity Summer Masters I', 'La organización Gfinity ha vuelto a aumentar los premios para el primer Summer Masters, pasando de 50.000$ a 80.000$. Además los equipos que participarán en la competición se han reducido pasando a 8 equipos.A finales de 2014 Gfinity anunció el Summer Masters I, que tendría lugar en este 2015, y que contaría con unos premios de 20.000$. Sin embargo, unas semanas después decidieron aumentar en más del doble los premios, alcanzando los 50.000$. No bastante con eso, hoy la organización ha anunciado un nuevo aumento de los premios para esta primera edición de la Masters en verano, alcanzando definitivamente, salvo sorpresa, los 80.000$ en premios para dicha competición.Con estos cambios, la distribución de los premios es la siguiente:    40.000$    20.000$    5.000$    5.000$    2.500$    2.500$    2.500$    2.500$También ha habido un cambio que tiene que ver con los participantes, y es que ahora serán 8 los equipos que participarán en esta Summer Masters I, que se espera tenga lugar el fin de semana del 27 al 28 de Junio.Con este último aumento, Gfinity Masters será la organización que más premios reparta a los equipos participantes, por detrás de las finales de la ESEA Pro League (500.000$), las finales de la CEVO 7 Professional (135.000$), la ESL One Cologne (250.000%) y, por último, los World Championships (100.000%)', '2015-05-19', 1),
(2, 'Geck0 sustituye a Kinta en wSystem', '\r\nDespués del terrible suceso de la muerte de Kinta, wSystem tenía que mirar para adelante, intentar reponerse de algo tan inesperado y retomar lo antes posible los entrenamientos ya que Gamergy está a la vuelta de al esquina. \r\n \r\nPara cerrar el quinteto, wSystem ha decidido contar con Geck0, viejo conocido tanto de Torpe como de dragunov y Peelk con los que jugó en gBots y Wizards respectivamente. Con estos últimos viajó a la anterior edición de Gamergy, equipo en el que también competía Kinta. \r\n \r\nDe este modo Gecko abandona Summa.GG, que pese a la repentina salida del jugador gallego, ya tiene quinto jugador para también poder seguir compitiendo, se trata de Krypton. \r\n \r\nEn poco más de un mes tendremos la tercera edición de Gamergy, el evento donde se disputan las finales de las diferentes ligas de la LVP y la escena de Counter-Strike: Global Offensive volverá a disfrutar de un torneo en LAN donde, seguramente, podremos ver a estos dos equipos con sus nuevas alineaciones.\r\n', '2015-05-21', 1),
(3, 'sukitRon entra en x6tence de forma oficial', 'Aunque después de la GGcup muchos ya lo daban por hecho como un fichaje cerrado, la verdad es que sukitRon ha seguido durante todo este tiempo en un período de pruebas y de adaptación a x6tence. No ha sido hasta hace escasos minutos cuando se ha anunciado que el jugador de origen venezolano cierra de forma oficial el quinteto del equipo de MusambaN1. La verdad es que la actuación de sukitRon en la GGcup de SocialNat ha sorprendido a propios y a extraños. Con prácticamente cero horas en su cuenta personal de steam, y después de una fase de grupos discreta, ha resultado ser el jugador más importante de las finales sumando el mayor número de frags y aportando una motivación y un ánimo hasta ese momento bastante desaparecidos. De este modo se da una situación curiosa y es que vuelven a tener un quitento prácticamente igual al que en enero del 2014 ganaba la EPS defendiendo los colores de Wizards, con sólo el cambio de loWel por FlipiN, que por aquel entonces militaba en ASES. Con este fichaje x6tence pasará a tener dos jugadores especializados en la AWP lo que sin duda les permitirá, a priori, defender mejor los mapas. Veremos como es la nueva adaptación de sukitRon a la gaming house y al equipo.', '2015-05-23', 2),
(4, 'A la venta las entradas de DreamHack Valencia', '\r\n\r\nDurante la tarde de ayer se pusieron a la venta las entradas para DreamHack Valencia 2015, uno de los festivales digitales más esperados del verano, tanto por su zona LAN, como por los eventos de deportes electrónicos que crecen cada año, y su espacio Expo con todas las grandes marcas del sector mostrando sus últimas novedades.\r\n\r\nA falta de anuncio de los principales juegos en su zona eSports, ya teníamos la confirmación semanas atrás de que el DreamHack Open será mixto, contando con torneo de CS:GO y StarCraft2 por partes iguales. Próximamente se darán a conocer el resto de juegos que tendrán competición.\r\n\r\nEn cuanto a la zona LAN, este año se añade una Entrada VIP con reserva del puesto por adelantado, acceso preferente, acreditación VIP, paquete de bienvenida y más sorpresas por parte de los patrocinadores. Aparte, está la entrada LAN normal a un precio reducido de 56€ hasta el 15 de Mayo, y luego la Entrada Gamer para aquellos que deseen competir (40€), y la Entrada Evento para disfrutar como espectador todos los días del evento (20€). Para más información, visita su web. \r\n', '2015-05-22', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partido_fase`
--

CREATE TABLE IF NOT EXISTS `partido_fase` (
`id` int(11) NOT NULL,
  `equipo_1` int(11) NOT NULL,
  `equipo_2` int(11) NOT NULL,
  `mapa` varchar(45) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `res_eq_1` int(11) DEFAULT NULL,
  `res_eq_2` int(11) DEFAULT NULL,
  `fase_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reto`
--

CREATE TABLE IF NOT EXISTS `reto` (
`reto` int(11) NOT NULL,
  `retador_id` int(11) NOT NULL,
  `retado_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `aceptado` tinyint(1) NOT NULL DEFAULT '0',
  `res_eq_retador` int(11) DEFAULT NULL,
  `res_eq_retado` int(11) DEFAULT NULL,
  `mapa` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
`id` int(11) NOT NULL,
  `user` varchar(45) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellidos` varchar(45) DEFAULT NULL,
  `edad` int(11) NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT '0',
  `es_moderador` tinyint(1) NOT NULL DEFAULT '0',
  `imagen` varchar(200) DEFAULT '/imagenes/interrogacion.jpg',
  `steam` varchar(200) NOT NULL,
  `email` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `equipo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `user`, `nombre`, `apellidos`, `edad`, `es_admin`, `es_moderador`, `imagen`, `steam`, `email`, `password`, `equipo_id`) VALUES
(1, 'admin', 'Administrador', 'Admin', 29, 1, 0, '/imagenes/interrogacion.jpg', 'UrlSteamAdmin', 'admin@admin.com', 'admin', NULL),
(2, 'User2', 'User', 'Uno', 21, 0, 0, '/imagenes/interrogacion.jpg', 'UrlSteamUser1', 'user1@user1.com', '123456', NULL),
(3, 'User3', 'User', 'Dos', 23, 0, 0, '/imagenes/interrogacion.jpg', 'UrlSteamUser2', 'user2@user2.com', '123456', NULL),
(4, 'User4', 'User', 'Tres', 25, 0, 0, '/imagenes/interrogacion.jpg', 'UsrlSteamUser3', 'user3@user3.com', '123456', NULL),
(5, 'User5', 'User', 'Cuatro', 26, 0, 0, '/imagenes/interrogacion.jpg', 'UrlSteamUser4', 'user4@user4.com', '123456', NULL),
(6, 'userD_1', 'UuarioD1', NULL, 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userD_1sdadsadadsda', 'userD1@dignitas.com', '123456', 1),
(7, 'userD_2', 'UsuarioD2', NULL, 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userD_2', 'userD_2@dignitas.com', '123456', 1),
(8, 'userD_3', 'UsuarioD3', NULL, 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userD_3', 'userD_3@dignitas.com', '123456', 1),
(9, 'userD_4', 'UsuarioD4', '', 26, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userD_4', 'userD_4@dignitas.com', '123456', 1),
(10, 'userD_5', 'UsuarioD5', NULL, 24, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userD_5', 'userD_5@dignitas.com', '123456', 1),
(11, 'UserA_1', 'UsuarioA1', 'Uno', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/UserA_1', 'usera1@email.com', '123456', 14),
(12, 'UserA_2', 'UsuarioA2', 'Dos', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userA2', 'usera2@email.com', '123456', 14),
(14, 'UserA_3', 'UsuarioA3', 'TRes', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usera3', 'usera3@email.com', '123456', 14),
(15, 'UserA_4', 'UserA4', 'Usuario', 34, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usera4', 'usera4@email.com', '123456', 14),
(16, 'userA_5', 'UserA5', 'Cinco', 26, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usera5', 'usera5@email,com', '', 14),
(17, 'UserB_1', 'Userb1', 'Uno', 26, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userb1', 'userb1@email.com', '123456', 15),
(18, 'UserB_2', 'Userb2', 'Dos', 27, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userb2', 'userb2@email.com', '123456', 15),
(19, 'Userb_3', 'Userb3', 'Tres', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userb3', 'userb3@email.com', '123456', 15),
(20, 'UserB_4', 'userb4', 'Cuatro', 26, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userb5', 'userb5@email.com', '123456', 15),
(21, 'userB_5', 'userb5', 'Cinco', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userb5', 'userb5@email.com', '123456', 15),
(22, 'userC_1', 'userc1', 'Uno', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userc1', 'userc1@email.com', '123456', 16),
(23, 'USerC_2', 'userc2', 'Dos', 19, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userc2', 'userc2@email.com', '123456', 16),
(24, 'userc_3', 'userc3', 'TRes', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userc3', 'userc3@email.com', '123456', 16),
(25, 'userC_4', 'userc4', 'Cuatro', 24, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userc4', 'userc4@email.com', '123456', 16),
(26, 'userC_5', 'userc5', 'Cinco', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userc5', 'userc5@email.com', '123456', 16),
(27, 'userE_1', 'userE1', 'Uno', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usere1', 'usere1@email.com', '123456', 23),
(28, 'userE_2', 'usere2', 'Dos', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usere2', 'usere2@email.cm', '123456', 23),
(29, 'userE_3', 'usere3', 'Tres', 27, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usere3', 'usere3@email.com', '123456', 23),
(30, 'userE_4', 'usere4', 'Cuatro', 35, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usere4', 'usere4@email.com', '123456', 23),
(31, 'userE_5', 'usere5', 'Cinco', 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usere5', 'usere5@email.com', '123456', 23),
(32, 'userF_1', 'userf1', 'Uno', 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userf1', 'userf1@email.com', '123456', 20),
(33, 'UserF_2', 'userf2', 'Dos', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userf2', 'userf2@email.com', '123456', 20),
(34, 'userF_3', 'userf3', 'Tres', 21, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userf3', 'userf3@email.com', '123456', 20),
(35, 'userF_4', 'userf4', 'Cuatro', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userf4', 'userf4@email.com', '123456', 20),
(36, 'userF_5', 'userF5', 'Cinco', 19, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userf5', 'userf5@email.com', '123456', 20),
(37, 'userG_1', 'userg1', 'Uno', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userg1', 'userg1@email.com', '123456', 25),
(38, 'userG_2', 'userg2', 'Dos', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/user2g2', 'userg2@email.com', '123456', 25),
(39, 'UserG_3', 'userg3', 'Tres', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userg3', 'userg3@email.com', '123456', 25),
(40, 'userG_4', 'usreg4', 'Cuatro', 26, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userg4', 'usreg4@email.com', '123456', 25),
(41, 'userG_5', 'userg5', 'Cinco', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userg5', 'userg5@email.com', '123456', 25),
(42, 'userH_1', 'userh1', 'Uno', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userh1', 'userh1@email.com', '123456', 26),
(43, 'userH_2', 'userh2', 'Dos', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userh2', 'userh2@email.com', '123456', 26),
(44, 'userH_3', 'userh3', 'Tres', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userh3', 'userh3@email.com', '123456', 26),
(45, 'userH_4', 'user4', 'Cuatro', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userh4', 'userh4@email.com', '123456', 26),
(46, 'userH_5', 'userh5', 'Cinco', 32, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userh5', 'userh5@email.com', '123456', 26),
(47, 'useri_1', 'useri1', 'Uno', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/useri1', 'useri1@email.com', '123456', 18),
(48, 'useri_2', 'useri2', 'Dos', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/useri2', 'useri2@email.com', '123456', 18),
(49, 'useri_3', 'useri3', 'Tres', 39, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/useri3', 'useri3@email.com', '123456', 18),
(50, 'useri_4', 'useri4', 'Cuatro', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/useri4', 'useri4@email.com', '123456', 18),
(51, 'useri_5', 'useri5', 'Cinco', 32, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/useri5', 'useri5@email.com', '123456', 18),
(52, 'userJ_1', 'userj1', 'Uno', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userj1', 'userj1@email.com', '123456', 19),
(53, 'userJ_2', 'userj2', 'Dos', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userj2', 'userj2@email.com', '123456', 19),
(54, 'userJ_3', 'userj3', 'Tres', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userj3', 'userJ3@email.com', '123456', 19),
(55, 'userJ_4', 'userj4', 'Cuatro', 21, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userj4', 'userj4@email.com', '123456', 19),
(56, 'userJ_5', 'userj5', 'Cinco', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userj5', 'userj5@email.com', '123456', 19),
(57, 'userK_1', 'userk1', 'Uno', 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userK_1', 'userk1@email.com', '123456', 27),
(58, 'userK_2', 'userk2', 'Dos', 55, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userK_2', 'emailk2@email.com', '123456', 27),
(59, 'userK_3', 'userk3', 'tres', 45, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userK_3', 'emailk3@email.com', '123456', 27),
(60, 'userK_4', 'userk4', 'Cuatro', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userK_4', 'userk4@email.com', '123456', 27),
(61, 'userK_5', 'userk5', 'Cinco', 32, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userK_5', 'userk5@email.com', '123456', 27),
(62, 'userL_1', 'userl1', 'Uno', 32, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userL_1', 'emaill1@email.com', '123456', 28),
(63, 'userL_2', 'userL2', 'Dos', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userL_2', 'emailL2@email.com', '123456', 28),
(64, 'userL_3', 'userl3', 'Tres', 55, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userL_3', 'emailL3@email.com', '', 28),
(65, 'userL_4', 'userl4', 'Cuatro', 57, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userL_4', 'emailL3@email.com', '', 28),
(66, 'userL_5', 'userl5', 'Cinco', 20, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userL_5', 'emailL5@email.com', '', 28),
(67, 'userM_1', 'userm1', 'Uno', 32, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userm1', 'userm1@email.com', '123456', 29),
(68, 'userM_2', 'userm2', 'Dos', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userm2', 'userm2@email.com', '123456', 29),
(69, 'userM_3', 'userm3', 'Tres', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userm3', 'userm3@email.com', '123456', 29),
(70, 'userM_4', 'userm4', 'Cuatro', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userm4', 'userm4@email.com', '123456', 29),
(71, 'userM_5', 'userm5', 'Cinco', 19, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userm5', 'userm5@email.com', '123456', 29),
(72, 'userN_1', 'usern1', 'Uno', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usern1', 'usern1@email.com', '123456', 30),
(73, 'userN_2', 'usern2', 'Dos', 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usern2', 'usern2@email.com', '123456', 30),
(74, 'userN_3', 'usern3', 'Tres', 19, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usern3', 'usern3@email.com', '123456', 30),
(75, 'userN_4', 'usern4', 'Cuatro', 27, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usern4', 'usern4@email.com', '123456', 30),
(76, 'userN_5', 'usern5', 'Cinco', 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usern5', 'usern5@email.com', '123456', 30),
(77, 'userO_1', 'usero1', 'Uno', 23, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usero1', 'usern1@email.com', '123456', 31),
(78, 'userO_2', 'usero2', 'Dos', 24, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usero2', 'usero2@email.com', '123456', 31),
(79, 'userO_3', 'usero3', 'Tres', 28, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usero3', 'usero3@email.com', '123456', 31),
(80, 'userO_4', 'usero4', 'Cuatro', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usero4', 'usero4@email.com', '123456', 31),
(81, 'userO_5', 'usero5', 'Cinco', 19, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/usero5', 'usero5@email.com', '123456', 31),
(82, 'userP_1', 'userp1', 'Uno', 19, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userp1', 'userp1@email.com', '123456', 32),
(83, 'userP_2', 'userp2', 'Dos', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userp2', 'userp2@email.com', '123456', 32),
(84, 'userP_3', 'userp3', 'Tres', 30, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userp3', 'userp3@email.com', '123456', 32),
(85, 'userP_4', 'userp4', 'Cuatro', 31, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userp4', 'userp4@email.com', '123456', 32),
(86, 'userP_5', 'userp5', 'Cinco', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userp5', 'userp5@email.com', '123456', 32),
(87, 'userQ_1', 'userq1', 'Uno', 25, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userq1', 'userq1@email.com', '123456', 33),
(88, 'userQ_2', 'userq2', 'Dos', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userq2', 'userq2@email.com', '123456', 33),
(89, 'userQ_3', 'userq3', 'Tres', 36, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userq3', 'userq3@email.com', '123456', 33),
(90, 'userQ_4', 'userq4', 'Cuaatro', 38, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userq4', 'userq4@email.com', '123456', 33),
(91, 'userQ_5', 'userq5', 'Cinco', 26, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userq5', 'userq5@email.com', '123456', 33),
(92, 'userR_1', 'userr1', 'Uno', 29, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userr1', 'userr1@email.com', '123456', 34),
(93, 'userR_2', 'userr2', 'Dos', 20, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userr2', 'userr2@email.com', '123456', 34),
(94, 'userR_3', 'userr3', 'Tres', 24, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userr3', 'userr3@email.com', '123456', 34),
(95, 'userR_4', 'userr4', 'Cuatro', 22, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userr4', 'userr4@email.com', '123456', 34),
(96, 'userR_5', 'userr5', 'Cinco', 40, 0, 0, '/imagenes/interrogacion.jpg', 'www.steam.com/userr5', 'userr5@email.com', '123456', 34);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `campeonato`
--
ALTER TABLE `campeonato`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_campeonato_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `campeonato_equipo`
--
ALTER TABLE `campeonato_equipo`
 ADD PRIMARY KEY (`equipo_id`,`campeonato_id`), ADD KEY `fk_ce_equipo_id` (`equipo_id`), ADD KEY `fk_ce_campeonato_id` (`campeonato_id`);

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_comentario_usuario_id` (`usuario_id`), ADD KEY `fk_comentario_noticia_id` (`noticia_id`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_equipo_capitan_id` (`capitan_id`);

--
-- Indices de la tabla `equipo_fase`
--
ALTER TABLE `equipo_fase`
 ADD PRIMARY KEY (`equipo_id`,`fase_id`), ADD KEY `fk_ef_equipo_id` (`equipo_id`), ADD KEY `fk_ef_fase_id` (`fase_id`);

--
-- Indices de la tabla `equipo_usuario`
--
ALTER TABLE `equipo_usuario`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_eu_usuario_id` (`usuario_id`), ADD KEY `fk_eu_equipo_id` (`equipo_id`);

--
-- Indices de la tabla `fase`
--
ALTER TABLE `fase`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_fase_campeonato_id` (`campeonato_id`);

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_mensaje_usuario_id` (`usuario_id`), ADD KEY `fk_mensaje_remitente_id` (`remitente_id`);

--
-- Indices de la tabla `noticia`
--
ALTER TABLE `noticia`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_noticia_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `partido_fase`
--
ALTER TABLE `partido_fase`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_pf_equipo_1` (`equipo_1`), ADD KEY `fk_pf_equipo_2` (`equipo_2`);

--
-- Indices de la tabla `reto`
--
ALTER TABLE `reto`
 ADD PRIMARY KEY (`reto`), ADD KEY `fk_reto_retador_id` (`retador_id`), ADD KEY `fk_reto_retado_id` (`retado_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_usuario_equipo_id` (`equipo_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `equipo`
--
ALTER TABLE `equipo`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT de la tabla `equipo_usuario`
--
ALTER TABLE `equipo_usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `fase`
--
ALTER TABLE `fase`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `noticia`
--
ALTER TABLE `noticia`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `partido_fase`
--
ALTER TABLE `partido_fase`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `reto`
--
ALTER TABLE `reto`
MODIFY `reto` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=97;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `campeonato`
--
ALTER TABLE `campeonato`
ADD CONSTRAINT `fk_campeonato_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `campeonato_equipo`
--
ALTER TABLE `campeonato_equipo`
ADD CONSTRAINT `fk_ce_campeonato_id` FOREIGN KEY (`campeonato_id`) REFERENCES `campeonato` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_ce_equipo_id` FOREIGN KEY (`equipo_id`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
ADD CONSTRAINT `fk_comentario_noticia_id` FOREIGN KEY (`noticia_id`) REFERENCES `noticia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_comentario_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
ADD CONSTRAINT `fk_equipo_capitan_id` FOREIGN KEY (`capitan_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `equipo_fase`
--
ALTER TABLE `equipo_fase`
ADD CONSTRAINT `fk_ef_equipo_id` FOREIGN KEY (`equipo_id`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_ef_fase_id` FOREIGN KEY (`fase_id`) REFERENCES `fase` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `equipo_usuario`
--
ALTER TABLE `equipo_usuario`
ADD CONSTRAINT `fk_eu_equipo_id` FOREIGN KEY (`equipo_id`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_eu_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `fase`
--
ALTER TABLE `fase`
ADD CONSTRAINT `fk_fase_campeonato_id` FOREIGN KEY (`campeonato_id`) REFERENCES `campeonato` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `mensaje`
--
ALTER TABLE `mensaje`
ADD CONSTRAINT `fk_mensaje_remitente_id` FOREIGN KEY (`remitente_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_mensaje_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `noticia`
--
ALTER TABLE `noticia`
ADD CONSTRAINT `fk_noticia_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `partido_fase`
--
ALTER TABLE `partido_fase`
ADD CONSTRAINT `fk_pf_equipo_1` FOREIGN KEY (`equipo_1`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_pf_equipo_2` FOREIGN KEY (`equipo_2`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reto`
--
ALTER TABLE `reto`
ADD CONSTRAINT `fk_reto_retado_id` FOREIGN KEY (`retado_id`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_reto_retador_id` FOREIGN KEY (`retador_id`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
ADD CONSTRAINT `fk_usuario_equipo_id` FOREIGN KEY (`equipo_id`) REFERENCES `equipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
