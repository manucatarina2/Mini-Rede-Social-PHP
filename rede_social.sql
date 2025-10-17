-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/10/2025 às 23:58
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `rede_social`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `conteudo` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `post_id`, `usuario_id`, `conteudo`, `created_at`) VALUES
(2, 6, 1, 'oiii', '2025-10-09 17:22:21'),
(3, 6, 1, 'oiii', '2025-10-09 17:22:22'),
(4, 13, 7, 'oiii', '2025-10-09 18:09:23'),
(5, 14, 3, 'OIIIII', '2025-10-09 18:16:10'),
(6, 15, 3, 'asssssss', '2025-10-09 18:19:07'),
(7, 15, 3, 'oiii', '2025-10-09 21:02:04'),
(8, 15, 11, 'oiii', '2025-10-09 21:56:58'),
(9, 15, 11, 'oiii', '2025-10-09 21:57:01'),
(10, 17, 11, 'oiii', '2025-10-09 21:57:59'),
(11, 17, 3, 'asd', '2025-10-13 13:35:30'),
(12, 19, 3, 'veeemm', '2025-10-13 16:12:21'),
(13, 19, 12, 'arrasou', '2025-10-13 16:12:53'),
(14, 20, 3, '8IOIU', '2025-10-13 16:51:45'),
(15, 19, 3, 'AEEE', '2025-10-13 16:52:01'),
(16, 7, 3, 'KKKKKKK', '2025-10-13 16:52:26'),
(17, 20, 12, 'oq?', '2025-10-13 16:56:50'),
(18, 22, 12, 'ai mds', '2025-10-13 18:37:03'),
(19, 22, 12, 'ai mds', '2025-10-13 18:37:06'),
(20, 22, 12, 'ai mds', '2025-10-13 18:37:07'),
(21, 22, 12, 'ai mds', '2025-10-13 18:37:07'),
(22, 22, 12, 'ai mds', '2025-10-13 18:37:07'),
(23, 22, 12, 'ai mds', '2025-10-13 18:37:07'),
(24, 21, 12, 'caramba', '2025-10-13 18:37:12'),
(25, 20, 12, 'mulher', '2025-10-13 18:37:18'),
(26, 23, 12, 'caramba', '2025-10-13 18:38:01'),
(27, 23, 12, 'caramba', '2025-10-13 18:38:02'),
(28, 23, 12, 'caramba', '2025-10-13 18:38:03'),
(29, 23, 12, 'caramba', '2025-10-13 18:38:03'),
(30, 22, 12, 'que', '2025-10-13 18:38:10'),
(31, 4, 12, 'o que', '2025-10-13 18:42:55'),
(32, 23, 12, 'o que', '2025-10-13 19:23:06'),
(33, 23, 12, 'uau', '2025-10-13 19:29:23'),
(34, 22, 12, 'oii', '2025-10-13 19:29:39'),
(35, 25, 12, 'oq aconteceu', '2025-10-13 19:32:31'),
(36, 24, 10, 'oi lindaaa', '2025-10-13 19:39:44'),
(37, 26, 12, 'HEHEHEHE', '2025-10-14 14:43:49'),
(38, 27, 3, 'KKKKKKKKKKKK', '2025-10-14 14:52:56'),
(39, 28, 12, 'AHH', '2025-10-14 15:44:35'),
(40, 28, 3, 'ASDFGHJ', '2025-10-14 15:48:46'),
(41, 26, 3, 'OMG', '2025-10-14 15:51:28'),
(42, 29, 12, 'OIIII', '2025-10-14 18:12:47'),
(43, 29, 14, 'HAHAHAHAHA', '2025-10-14 18:14:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `followers`
--

INSERT INTO `followers` (`id`, `follower_id`, `followed_id`, `created_at`, `status`) VALUES
(2, 3, 2, '2025-10-16 01:38:33', 1),
(19, 10, 3, '2025-10-16 11:22:24', 1),
(26, 3, 10, '2025-10-16 21:57:01', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `usuario_id`, `created_at`) VALUES
(9, 6, 1, '2025-10-09 17:05:02'),
(10, 5, 1, '2025-10-09 17:08:19'),
(13, 7, 1, '2025-10-09 17:22:50'),
(14, 4, 1, '2025-10-09 17:23:51'),
(15, 8, 1, '2025-10-09 17:40:44'),
(16, 10, 1, '2025-10-09 17:49:09'),
(17, 13, 7, '2025-10-09 18:09:18'),
(18, 14, 3, '2025-10-09 18:16:02'),
(22, 10, 11, '2025-10-09 21:56:49'),
(23, 17, 11, '2025-10-09 21:57:56'),
(24, 17, 3, '2025-10-13 13:35:26'),
(25, 18, 12, '2025-10-13 13:46:48'),
(27, 19, 12, '2025-10-13 16:12:46'),
(31, 20, 3, '2025-10-13 16:55:04'),
(34, 21, 3, '2025-10-13 17:35:40'),
(35, 19, 3, '2025-10-13 17:35:45'),
(37, 22, 12, '2025-10-13 18:36:53'),
(38, 4, 12, '2025-10-13 18:42:49'),
(39, 23, 12, '2025-10-13 19:23:02'),
(40, 20, 12, '2025-10-13 19:30:35'),
(41, 16, 12, '2025-10-13 19:30:39'),
(42, 25, 12, '2025-10-13 19:32:23'),
(43, 24, 10, '2025-10-13 19:39:35'),
(44, 26, 12, '2025-10-14 14:43:24'),
(45, 27, 3, '2025-10-14 14:52:57'),
(47, 28, 3, '2025-10-14 15:43:52'),
(48, 29, 12, '2025-10-14 18:12:43'),
(49, 29, 14, '2025-10-14 18:15:27'),
(50, 30, 14, '2025-10-14 18:15:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `conteudo` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `lida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `from_id`, `to_id`, `conteudo`, `created_at`, `lida`) VALUES
(1, 7, 3, 'oiiiiiii', '2025-10-09 18:52:09', 0),
(2, 7, 8, 'oii', '2025-10-09 19:01:38', 1),
(6, 11, 10, 'oiiiii', '2025-10-09 21:58:34', 1),
(7, 10, 11, 'tudo bem?', '2025-10-09 21:58:48', 1),
(8, 10, 3, 'ASDFGHJK', '2025-10-13 12:55:09', 1),
(9, 12, 10, 'oiii', '2025-10-13 13:14:39', 1),
(10, 12, 13, 'hello', '2025-10-13 15:36:00', 1),
(11, 12, 13, 'oiiiiiii', '2025-10-13 15:36:09', 1),
(12, 12, 13, 'oiiiiiii', '2025-10-13 15:54:52', 1),
(13, 12, 13, 'hello', '2025-10-13 15:54:59', 1),
(14, 13, 12, 'oiiii', '2025-10-13 16:00:42', 1),
(15, 3, 12, 'oiiiii', '2025-10-13 16:02:21', 1),
(16, 12, 10, 'oiiiiiiii', '2025-10-13 16:43:10', 1),
(17, 3, 12, 'aeee', '2025-10-13 16:54:09', 1),
(18, 12, 3, 'aeeee', '2025-10-13 16:54:24', 1),
(19, 12, 3, 'oiii', '2025-10-13 16:54:33', 1),
(20, 3, 10, 'oiiiii', '2025-10-13 17:01:06', 1),
(21, 12, 10, 'oiiii', '2025-10-13 18:43:19', 1),
(22, 10, 12, 'oiiii', '2025-10-13 18:43:26', 1),
(23, 12, 3, 'oi aninha', '2025-10-13 18:52:01', 1),
(24, 3, 12, 'oqueeeeeeeeeeeeeeeeeeeeeeeeeeeeee', '2025-10-13 18:52:25', 1),
(25, 12, 10, 'hello', '2025-10-13 19:02:04', 1),
(26, 12, 3, 'tudo bem?', '2025-10-13 19:10:40', 1),
(27, 3, 12, 'tudo e com vc', '2025-10-13 19:11:08', 1),
(28, 12, 10, 'tudo bem', '2025-10-13 19:12:33', 1),
(29, 10, 12, 'tudo ss', '2025-10-13 19:12:49', 1),
(30, 12, 10, 'cara', '2025-10-13 19:13:05', 1),
(31, 12, 10, 'mds', '2025-10-13 19:13:06', 1),
(32, 12, 10, 'aconteceu uma coisa', '2025-10-13 19:13:11', 1),
(33, 12, 3, 'tudo ss', '2025-10-13 19:29:52', 1),
(34, 12, 10, 'deixa eu te contar', '2025-10-13 19:31:39', 1),
(35, 10, 3, 'tudo bem?', '2025-10-13 19:33:09', 1),
(36, 12, 3, 'deixa', '2025-10-13 19:34:07', 1),
(37, 12, 3, 'deixa eu te contar', '2025-10-13 19:34:09', 1),
(38, 10, 12, 'contee', '2025-10-13 19:36:06', 1),
(39, 10, 12, 'conta logo', '2025-10-13 19:37:50', 1),
(40, 10, 12, 'vai mulher', '2025-10-13 19:39:30', 1),
(41, 3, 10, 'tudooo', '2025-10-13 20:10:45', 1),
(42, 3, 12, 'ahhh', '2025-10-13 20:10:49', 1),
(43, 3, 12, 'OIIIII', '2025-10-13 20:22:42', 1),
(44, 10, 12, 'oqq', '2025-10-13 20:29:19', 1),
(45, 12, 10, 'é', '2025-10-13 20:29:37', 1),
(46, 10, 3, 'OQ', '2025-10-14 14:31:10', 1),
(47, 10, 3, 'PSÉ MENINA', '2025-10-14 14:31:15', 1),
(48, 10, 3, 'BABADO', '2025-10-14 14:31:18', 1),
(49, 3, 10, 'NÃO ACREDITOOOOOOOOO', '2025-10-14 14:33:14', 1),
(50, 10, 3, 'PSÉ', '2025-10-14 14:33:36', 1),
(51, 3, 10, 'NAO ACREDITOOOOOOOOOOOOOOOO', '2025-10-14 14:34:33', 1),
(52, 3, 10, 'PSEEE', '2025-10-14 14:35:13', 1),
(53, 3, 12, 'OQQQQ', '2025-10-14 14:41:39', 1),
(54, 14, 12, 'OIIII', '2025-10-14 18:13:42', 1),
(55, 14, 12, 'TUDO BEM??', '2025-10-14 18:13:45', 1),
(56, 14, 12, 'EAI???', '2025-10-14 18:13:49', 1),
(57, 12, 14, 'OIIIIII', '2025-10-14 18:14:01', 1),
(58, 12, 14, 'AAAAAAA', '2025-10-14 18:14:10', 1),
(59, 10, 3, 'tudo bem', '2025-10-16 08:28:59', 1),
(60, 3, 10, 'hahahha', '2025-10-16 08:29:09', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario_acao_id` int(11) DEFAULT NULL,
  `origem_usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('like','comentario','mensagem') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `usuario_acao_id`, `origem_usuario_id`, `tipo`, `referencia_id`, `mensagem`, `lida`, `created_at`) VALUES
(1, 13, NULL, 12, 'mensagem', 12, 'Manuzinha enviou uma mensagem: \'oiiiiiii\'', 1, '2025-10-13 15:54:52'),
(2, 13, NULL, 12, 'mensagem', 13, 'Manuzinha enviou uma mensagem: \'hello\'', 1, '2025-10-13 15:54:59'),
(3, 12, NULL, 13, 'mensagem', 14, 'Ana enviou uma mensagem: \'oiiii\'', 1, '2025-10-13 16:00:42'),
(4, 12, NULL, 3, 'mensagem', 15, 'ana enviou uma mensagem: \'oiiiii\'', 1, '2025-10-13 16:02:21'),
(5, 10, NULL, 12, 'mensagem', 16, 'Manuzinha enviou uma mensagem: \'oiiiiiiii\'', 1, '2025-10-13 16:43:10'),
(6, 12, NULL, 3, 'mensagem', 17, 'anaaaaa enviou uma mensagem: \'aeee\'', 1, '2025-10-13 16:54:09'),
(7, 3, NULL, 12, 'mensagem', 18, 'Manuzinha enviou uma mensagem: \'aeeee\'', 1, '2025-10-13 16:54:24'),
(8, 3, NULL, 12, 'mensagem', 19, 'Manuzinha enviou uma mensagem: \'oiii\'', 1, '2025-10-13 16:54:33'),
(9, 10, NULL, 3, 'mensagem', 20, 'ana enviou uma mensagem: \'oiiiii\'', 1, '2025-10-13 17:01:06'),
(10, 10, NULL, 12, 'mensagem', 21, 'Manuzinha enviou uma mensagem: \'oiiii\'', 1, '2025-10-13 18:43:19'),
(11, 12, NULL, 10, 'mensagem', 22, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'oiiii\'', 1, '2025-10-13 18:43:26'),
(12, 3, NULL, 12, 'mensagem', 23, 'Manuzinha enviou uma mensagem: \'oi aninha\'', 1, '2025-10-13 18:52:01'),
(13, 12, NULL, 3, 'mensagem', 24, 'ana enviou uma mensagem: \'oqueeeeeeeeeeeeeeeeeeeeeeeeeeeeee\'', 1, '2025-10-13 18:52:25'),
(14, 10, NULL, 12, 'mensagem', 25, 'Manuzinha enviou uma mensagem: \'hello\'', 1, '2025-10-13 19:02:04'),
(15, 3, NULL, 12, 'mensagem', 26, 'Manuzinha enviou uma mensagem: \'tudo bem?\'', 1, '2025-10-13 19:10:40'),
(16, 12, NULL, 3, 'mensagem', 27, 'ana enviou uma mensagem: \'tudo e com vc\'', 1, '2025-10-13 19:11:08'),
(17, 10, NULL, 12, 'mensagem', 28, 'Manuzinha enviou uma mensagem: \'tudo bem\'', 1, '2025-10-13 19:12:33'),
(18, 12, NULL, 10, 'mensagem', 29, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'tudo ss\'', 1, '2025-10-13 19:12:49'),
(19, 10, NULL, 12, 'mensagem', 30, 'Manuzinha enviou uma mensagem: \'cara\'', 1, '2025-10-13 19:13:05'),
(20, 10, NULL, 12, 'mensagem', 31, 'Manuzinha enviou uma mensagem: \'mds\'', 1, '2025-10-13 19:13:06'),
(21, 10, NULL, 12, 'mensagem', 32, 'Manuzinha enviou uma mensagem: \'aconteceu uma coisa\'', 1, '2025-10-13 19:13:11'),
(22, 3, NULL, 12, 'comentario', 22, 'Comentou na sua publicação', 1, '2025-10-13 19:29:39'),
(23, 3, NULL, 12, 'mensagem', 33, 'Manuzinha enviou uma mensagem: \'tudo ss\'', 1, '2025-10-13 19:29:52'),
(24, 3, NULL, 12, 'like', 20, NULL, 1, '2025-10-13 19:30:35'),
(25, 3, NULL, 12, 'like', 16, NULL, 1, '2025-10-13 19:30:39'),
(26, 10, NULL, 12, 'mensagem', 34, 'Manuzinha enviou uma mensagem: \'deixa eu te contar\'', 1, '2025-10-13 19:31:39'),
(27, 10, NULL, 12, 'like', 25, NULL, 1, '2025-10-13 19:32:23'),
(28, 10, NULL, 12, 'comentario', 25, 'Comentou na sua publicação', 1, '2025-10-13 19:32:31'),
(29, 3, NULL, 10, 'mensagem', 35, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'tudo bem?\'', 1, '2025-10-13 19:33:10'),
(30, 3, NULL, 12, 'mensagem', 36, 'Manuzinha enviou uma mensagem: \'deixa\'', 1, '2025-10-13 19:34:07'),
(31, 3, NULL, 12, 'mensagem', 37, 'Manuzinha enviou uma mensagem: \'deixa eu te contar\'', 1, '2025-10-13 19:34:09'),
(32, 12, NULL, 10, 'mensagem', 38, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'contee\'', 1, '2025-10-13 19:36:06'),
(33, 12, NULL, 10, 'mensagem', 39, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'conta logo\'', 1, '2025-10-13 19:37:50'),
(34, 12, NULL, 10, 'mensagem', 40, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'vai mulher\'', 1, '2025-10-13 19:39:30'),
(35, 12, NULL, 10, 'like', 24, NULL, 1, '2025-10-13 19:39:35'),
(36, 12, NULL, 10, 'comentario', 24, 'Comentou na sua publicação', 1, '2025-10-13 19:39:44'),
(37, 10, NULL, 3, 'mensagem', 41, 'ana enviou uma mensagem: \'tudooo\'', 1, '2025-10-13 20:10:45'),
(38, 12, NULL, 3, 'mensagem', 42, 'ana enviou uma mensagem: \'ahhh\'', 1, '2025-10-13 20:10:49'),
(39, 12, NULL, 3, 'mensagem', 43, 'ana enviou uma mensagem: \'OIIIII\'', 1, '2025-10-13 20:22:42'),
(40, 12, NULL, 10, 'mensagem', 44, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'oqq\'', 1, '2025-10-13 20:29:19'),
(41, 10, NULL, 12, 'mensagem', 45, 'Manuzinha enviou uma mensagem: \'é\'', 1, '2025-10-13 20:29:37'),
(42, 3, NULL, 10, 'mensagem', 46, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'OQ\'', 1, '2025-10-14 14:31:10'),
(43, 3, NULL, 10, 'mensagem', 47, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'PSÉ MENINA\'', 1, '2025-10-14 14:31:15'),
(44, 3, NULL, 10, 'mensagem', 48, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'BABADO\'', 1, '2025-10-14 14:31:18'),
(45, 10, NULL, 3, 'mensagem', 49, 'ana enviou uma mensagem: \'NÃO ACREDITOOOOOOOOO\'', 1, '2025-10-14 14:33:14'),
(46, 3, NULL, 10, 'mensagem', 50, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'PSÉ\'', 1, '2025-10-14 14:33:36'),
(47, 10, NULL, 3, 'mensagem', 51, 'ana enviou uma mensagem: \'NAO ACREDITOOOOOOOOOOOOOOOO\'', 1, '2025-10-14 14:34:33'),
(48, 10, NULL, 3, 'mensagem', 52, 'ana enviou uma mensagem: \'PSEEE\'', 1, '2025-10-14 14:35:13'),
(49, 12, NULL, 3, 'mensagem', 53, 'ana enviou uma mensagem: \'OQQQQ\'', 1, '2025-10-14 14:41:39'),
(50, 3, NULL, 12, 'like', 26, NULL, 1, '2025-10-14 14:43:24'),
(51, 3, NULL, 12, 'comentario', 26, 'Comentou na sua publicação', 1, '2025-10-14 14:43:49'),
(52, 12, NULL, 3, 'comentario', 27, 'Comentou na sua publicação', 1, '2025-10-14 14:52:56'),
(53, 12, NULL, 3, 'like', 27, NULL, 1, '2025-10-14 14:52:57'),
(54, 12, NULL, 3, 'like', 28, NULL, 1, '2025-10-14 15:43:21'),
(55, 12, NULL, 3, 'like', 28, NULL, 1, '2025-10-14 15:43:52'),
(56, 12, NULL, 3, 'comentario', 28, 'Comentou na sua publicação', 1, '2025-10-14 15:48:46'),
(57, 14, NULL, 12, 'like', 29, NULL, 1, '2025-10-14 18:12:43'),
(58, 14, NULL, 12, 'comentario', 29, 'Comentou na sua publicação', 1, '2025-10-14 18:12:47'),
(59, 12, NULL, 14, 'mensagem', 54, 'aninhaA enviou uma mensagem: \'OIIII\'', 1, '2025-10-14 18:13:42'),
(60, 12, NULL, 14, 'mensagem', 55, 'aninhaA enviou uma mensagem: \'TUDO BEM??\'', 1, '2025-10-14 18:13:45'),
(61, 12, NULL, 14, 'mensagem', 56, 'aninhaA enviou uma mensagem: \'EAI???\'', 1, '2025-10-14 18:13:49'),
(62, 14, NULL, 12, 'mensagem', 57, 'Manuzinha enviou uma mensagem: \'OIIIIII\'', 1, '2025-10-14 18:14:01'),
(63, 14, NULL, 12, 'mensagem', 58, 'Manuzinha enviou uma mensagem: \'AAAAAAA\'', 1, '2025-10-14 18:14:10'),
(64, 12, NULL, 14, 'like', 30, NULL, 1, '2025-10-14 18:15:48'),
(65, 3, NULL, 10, 'mensagem', 59, 'CAMILA BRAZ ALEIXO enviou uma mensagem: \'tudo bem\'', 1, '2025-10-16 08:28:59'),
(66, 10, NULL, 3, 'mensagem', 60, 'ana enviou uma mensagem: \'hahahha\'', 1, '2025-10-16 08:29:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `conteudo` text NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `posts`
--

INSERT INTO `posts` (`id`, `usuario_id`, `conteudo`, `imagem`, `created_at`) VALUES
(4, 1, 'oiiiiiiii', NULL, '2025-10-09 16:43:49'),
(5, 1, 'oii', '68e8117c004a5_Document 17.pdf', '2025-10-09 16:48:12'),
(6, 1, 'oiiiiiiiiiiiii', '68e8122f9c839_image-removebg-preview (3).png', '2025-10-09 16:51:11'),
(7, 1, 'oiiiii', 'uploads/68e8199428b20_image-removebg-preview (9).png', '2025-10-09 17:22:44'),
(8, 1, 'oiiiiiiiiiiiiiiii', NULL, '2025-10-09 17:29:47'),
(9, 1, 'aaaaaaaaaaaaaaaaa', NULL, '2025-10-09 17:41:57'),
(10, 1, 'OQUE', NULL, '2025-10-09 17:42:27'),
(12, 1, 'oi', NULL, '2025-10-09 17:52:36'),
(13, 7, 'ahhh', NULL, '2025-10-09 18:09:16'),
(14, 3, 'AHHHHHHHHH', 'uploads/68e826062f260_image-removebg-preview (6).png', '2025-10-09 18:15:50'),
(15, 3, 'oiiiiiiiiiiiiiiii', 'uploads/68e826bd6a835_image-removebg-preview (7).png', '2025-10-09 18:18:53'),
(16, 3, 'oiiii', NULL, '2025-10-09 21:02:15'),
(17, 11, 'oiii', NULL, '2025-10-09 21:57:54'),
(18, 12, 'oiiii', NULL, '2025-10-13 13:46:46'),
(19, 12, 'VEM PRA DS', NULL, '2025-10-13 13:47:12'),
(20, 3, 'HAHAHAHAHA', NULL, '2025-10-13 16:51:37'),
(21, 3, 'oq??', NULL, '2025-10-13 17:04:26'),
(22, 3, 'OQQ', NULL, '2025-10-13 18:33:11'),
(23, 12, 'oiii', NULL, '2025-10-13 18:37:56'),
(24, 12, 'oiii', NULL, '2025-10-13 19:29:28'),
(25, 10, 'caraca', NULL, '2025-10-13 19:32:17'),
(26, 3, 'HAHAHAH', NULL, '2025-10-14 14:42:41'),
(27, 12, 'MDS', NULL, '2025-10-14 14:52:47'),
(28, 12, 'OMG', NULL, '2025-10-14 15:43:10'),
(29, 14, 'OIIIII', NULL, '2025-10-14 18:12:28'),
(30, 12, 'OIII', NULL, '2025-10-14 18:15:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `presence`
--

CREATE TABLE `presence` (
  `usuario_id` int(11) NOT NULL,
  `last_seen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `presence`
--

INSERT INTO `presence` (`usuario_id`, `last_seen`) VALUES
(1, '2025-10-06 21:17:07'),
(3, '2025-10-16 18:58:07'),
(7, '2025-10-09 21:43:35'),
(8, '2025-10-09 20:19:39'),
(10, '2025-10-16 18:58:16'),
(11, '2025-10-09 21:58:56'),
(12, '2025-10-14 18:15:37'),
(13, '2025-10-13 16:01:01'),
(14, '2025-10-14 18:14:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nivel` enum('membro','admin') NOT NULL DEFAULT 'membro',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `privado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `foto`, `nivel`, `last_login`, `created_at`, `privado`) VALUES
(1, 'aaaaa', 'ana@gmail.com', '$2y$10$Rq9c9nI3ijLfH2y.nA7sReyYCq642mpIXaSGvyi8qqhYz5Xl7qSZy', NULL, 'membro', '2025-10-06 21:17:07', '2025-10-06 16:17:00', 0),
(2, 'Manuela', 'manu.catarina@gmail.com', '$2y$10$/U95ADEd6Zd.OutJmnPVl.c4pnDCYKnNFhbhehLBapEzcb31tbwRy', NULL, 'membro', '2025-10-08 21:06:19', '2025-10-08 16:06:02', 0),
(3, 'ana', 'anavic.aleixo@gmail.com', '$2y$10$fAYwng7jjnpTMuttbHKxBePsSAzR2XS.Bai9XmoRNhV34aYRvjzvy', 'uploads/68ed5a6de4082.png', 'membro', '2025-10-16 18:53:09', '2025-10-08 16:34:38', 0),
(4, 'manu', 'manu@gmail.com', '$2y$10$qsHB5rumUnwLq7ESX9e1bOvOtpK1CziVOIt8Hb.DU3quEnGRuzxEu', NULL, 'membro', NULL, '2025-10-08 19:04:11', 0),
(5, 'so', 'so@gmail.com', '$2y$10$3mWbCXc1g.HEVaytMsitQe.XARyMz5j0NRubhYM/gvj.jddMjzd2e', NULL, 'membro', NULL, '2025-10-08 19:29:04', 0),
(6, 'anaaaa', 'anavic.1aleixo@gmail.com', '$2y$10$BhAf1GAn/3w.F7aLJxiuMOEKhy6OKbYSAfHB03w5nxvNNQle7cOCu', NULL, 'membro', NULL, '2025-10-08 19:43:07', 0),
(7, 'Aninha1', 'anavictoria@gmail.com', '$2y$10$gGycF4UIhQEytwxe3cflceNiQoishdosYEFgxNRclFBOWKTTBoTwK', 'uploads/68e8258989b05.png', 'membro', '2025-10-09 21:43:35', '2025-10-09 16:12:12', 0),
(8, 'ANA VICtoria', 'anavictoria.aleixo@gmail.com', '$2y$10$1a6yyLKWp1PDUFInJy8mb.d8v7PCowgUPQqEtADZZpw88uA5FDoxa', NULL, 'membro', '2025-10-09 21:42:49', '2025-10-09 18:55:45', 0),
(10, 'CAMILA BRAZ ALEIXO', 'camilabrazaleixo.ca@gmail.com', '$2y$10$CM5gTnZznBXG/vTjqU4z9O2MzKhgd/uL39OYcyBgNHNtXVY0SKo3y', NULL, 'membro', '2025-10-16 16:05:06', '2025-10-09 21:46:31', 0),
(11, 'anaaaaaaa', 'anavic1.aleixo@gmail.com', '$2y$10$fgLMxdjQg9SXHmisMAhpue./rT4hFJkrZm5pgkfiTDNMAofU/dqF6', 'uploads/68e85a2e34950.png', 'membro', '2025-10-09 21:58:56', '2025-10-09 21:56:16', 0),
(12, 'Manuzinha', 'manulinda@gmail.com', '$2y$10$jVZ7fAKcVGu8gdINUEF74.peJgVe1Ns13um/MAx4/IIEtkmQITDbu', 'user.jpg', 'membro', '2025-10-14 18:15:33', '2025-10-13 13:13:33', 0),
(13, 'Ana', 'aninha@gmail.com', '$2y$10$nIa.hhcS5Szi0uGPr4g1ZeA3IRKTbDECVlWoet/ZFytpMj4DjjEEi', NULL, 'membro', '2025-10-13 16:00:52', '2025-10-13 15:35:10', 0),
(14, 'aninhaA', 'anaa@gmail.com', '$2y$10$ofYzI3obYYnNq/xmGJj0M.vw99KjeOtoGlmHfBexEqVxFJV7XL49W', 'uploads/68eebc8738321.jpg', 'membro', '2025-10-14 18:14:13', '2025-10-14 18:11:35', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `presence`
--
ALTER TABLE `presence`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`from_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`to_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `presence`
--
ALTER TABLE `presence`
  ADD CONSTRAINT `presence_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
