-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 06:06 AM
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
-- Database: `cways_prod`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_timers`
--

CREATE TABLE `active_timers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `issue_time_log_id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `last_heartbeat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_token` varchar(255) DEFAULT NULL,
  `browser_tab_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size` int(10) UNSIGNED NOT NULL,
  `path` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `old_values` longtext DEFAULT NULL,
  `new_values` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'logout', 'user', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 04:42:11'),
(2, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 04:42:22'),
(3, 1, 'issue_transitioned', 'issue', 12, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 04:48:14'),
(4, 1, 'issue_transitioned', 'issue', 12, '{\"status_id\":3}', '{\"status_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 04:48:19'),
(5, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:10:14'),
(6, 1, 'sprint_created', 'sprint', 6, NULL, '{\"name\":\"Browser Verified Sprint\",\"goal\":null,\"start_date\":null,\"end_date\":null}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:17:23'),
(7, 1, 'issue_type_updated', 'issue_type', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:22:40'),
(8, 1, 'issue_transitioned', 'issue', 12, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:24:47'),
(9, 1, 'sprint_created', 'sprint', 7, NULL, '{\"name\":\"Backlog Fix Verified Sprint\",\"goal\":null,\"start_date\":null,\"end_date\":null}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:29:34'),
(10, 1, 'sprint_created', 'sprint', 8, NULL, '{\"name\":\"Backlog Final Verify 2\",\"goal\":null,\"start_date\":null,\"end_date\":null}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:40:42'),
(11, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 06:07:52'),
(12, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 03:43:50'),
(13, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 03:48:57'),
(14, 1, 'sprint_created', 'sprint', 9, NULL, '{\"name\":\"Redesign Fix Verified\",\"goal\":null,\"start_date\":null,\"end_date\":null}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 03:50:00'),
(15, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:03:58'),
(16, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:17:28'),
(17, 1, 'project_created', 'project', 4, NULL, '{\"key\":\"CWAYSMIS\",\"name\":\"CWays MIS\",\"description\":\"CWays MIS\",\"lead_id\":\"1\",\"category_id\":\"3\",\"default_assignee\":\"project_lead\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:19:42'),
(18, 1, 'project_updated', 'project', 4, '{\"id\":4,\"key\":\"CWAYSMIS\",\"name\":\"CWays MIS\",\"description\":\"CWays MIS\",\"lead_id\":1,\"category_id\":3,\"default_assignee\":\"project_lead\",\"avatar\":null,\"is_archived\":0,\"is_private\":0,\"issue_count\":0,\"budget\":\"0.00\",\"budget_currency\":\"USD\",\"created_by\":1,\"created_at\":\"2026-01-12 09:49:42\",\"updated_at\":\"2026-01-12 09:49:42\",\"lead_name\":\"System Administrator\",\"lead_avatar\":\"\\/uploads\\/avatars\\/avatar_1_1767933698.png\",\"category_name\":\"Infrastructure\",\"created_by_name\":\"System Administrator\",\"lead\":{\"display_name\":\"System Administrator\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1767933698.png\"}}', '{\"name\":\"CWays MIS\",\"description\":\"CWays MIS\",\"lead_id\":\"1\",\"category_id\":\"3\",\"avatar\":\"\\/uploads\\/avatars\\/project_4_696479b71a577.jpg\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:33:59'),
(19, 1, 'member_added', 'project', 4, NULL, '{\"user_id\":4,\"role_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:34:14'),
(20, 1, 'member_added', 'project', 4, NULL, '{\"user_id\":1,\"role_id\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:34:20'),
(21, 1, 'member_added', 'project', 4, NULL, '{\"user_id\":3,\"role_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:34:27'),
(22, 1, 'member_added', 'project', 4, NULL, '{\"user_id\":5,\"role_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:34:33'),
(23, 1, 'issue_created', 'issue', 14, NULL, '{\"project_id\":\"4\",\"issue_key\":\"CWAYSMIS-1\",\"summary\":\"Issue being created\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:35:50'),
(24, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:35:55'),
(25, 1, 'sprint_created', 'sprint', 10, NULL, '{\"name\":\"creating sprint\",\"goal\":\"yes i want to accomplish something in my life.\",\"start_date\":\"2026-01-12\",\"end_date\":\"2026-01-19\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:50:21'),
(26, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":3}', '{\"status_id\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:53:23'),
(27, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":4}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:53:24'),
(28, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 05:56:38'),
(29, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":3}', '{\"status_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:03:12'),
(30, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:03:15'),
(31, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:32:00'),
(32, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:37:25'),
(33, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":3}', '{\"status_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:40:03'),
(34, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:07:45'),
(35, 1, 'issue_transitioned', 'issue', 3, '{\"status_id\":3}', '{\"status_id\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:14:59'),
(36, 1, 'issue_transitioned', 'issue', 3, '{\"status_id\":4}', '{\"status_id\":5}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:15:08'),
(37, 1, 'issue_transitioned', 'issue', 2, '{\"status_id\":3}', '{\"status_id\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:16:46'),
(38, 1, 'issue_transitioned', 'issue', 2, '{\"status_id\":4}', '{\"status_id\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:16:59'),
(39, 1, 'issue_transitioned', 'issue', 1, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:18:16'),
(40, 1, 'issue_transitioned', 'issue', 1, '{\"status_id\":3}', '{\"status_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:18:22'),
(41, 1, 'issue_transitioned', 'issue', 6, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:19:18'),
(42, 1, 'issue_transitioned', 'issue', 1, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:19:21'),
(43, 1, 'issue_transitioned', 'issue', 6, '{\"status_id\":3}', '{\"status_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:21:01'),
(44, 1, 'issue_transitioned', 'issue', 3, '{\"status_id\":5}', '{\"status_id\":6}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:21:11'),
(45, 1, 'issue_transitioned', 'issue', 3, '{\"status_id\":6}', '{\"status_id\":5}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:21:16'),
(46, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 08:16:58'),
(47, 1, 'issue_created', 'issue', 15, NULL, '{\"project_id\":\"4\",\"issue_key\":\"CWAYSMIS-2\",\"summary\":\"Assigning yhe issue to me\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 08:46:42'),
(48, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 08:46:51'),
(49, 1, 'issue_transitioned', 'issue', 14, '{\"status_id\":3}', '{\"status_id\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 08:50:08'),
(50, 1, 'issue_created', 'issue', 18, NULL, '{\"project_id\":\"4\",\"issue_key\":\"CWAYSMIS-3\",\"summary\":\"sadf\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 09:12:04'),
(51, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 09:22:43'),
(52, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 09:28:34'),
(53, 1, 'login', 'user', 1, NULL, '{\"ip\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 04:11:43'),
(54, 1, 'issue_transitioned', 'issue', 15, '{\"status_id\":2}', '{\"status_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 04:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `boards` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('scrum','kanban') NOT NULL DEFAULT 'scrum',
  `filter_jql` text DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `owner_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`id`, `project_id`, `name`, `type`, `filter_jql`, `is_private`, `owner_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'ECOM Development Board', 'scrum', NULL, 0, 2, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(2, 1, 'ECOM Kanban Board', 'kanban', NULL, 0, 3, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 2, 'Mobile Apps Board', 'scrum', NULL, 0, 3, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 3, 'Infrastructure Board', 'kanban', NULL, 0, 4, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(5, 3, 'Infrastructure Scrum Board', 'scrum', NULL, 0, 1, '2026-01-12 04:29:52', '2026-01-12 04:29:52'),
(6, 4, 'CWays MIS Scrum Board', 'scrum', NULL, 0, 1, '2026-01-12 04:29:52', '2026-01-12 04:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `board_columns`
--

CREATE TABLE `board_columns` (
  `id` int(10) UNSIGNED NOT NULL,
  `board_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status_ids` longtext DEFAULT NULL,
  `min_issues` int(10) UNSIGNED DEFAULT NULL,
  `max_issues` int(10) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `board_columns`
--

INSERT INTO `board_columns` (`id`, `board_id`, `name`, `status_ids`, `min_issues`, `max_issues`, `sort_order`) VALUES
(21, 1, 'Open', '[1]', NULL, NULL, 0),
(22, 1, 'To Do', '[2]', NULL, NULL, 1),
(23, 1, 'In Progress', '[3]', NULL, NULL, 2),
(24, 1, 'In Review', '[4]', NULL, NULL, 3),
(25, 1, 'Testing', '[5]', NULL, NULL, 4),
(26, 1, 'Done', '[6]', NULL, NULL, 5),
(27, 1, 'Closed', '[7]', NULL, NULL, 6),
(28, 1, 'Reopened', '[8]', NULL, NULL, 7),
(29, 2, 'Open', '[1]', NULL, NULL, 0),
(30, 2, 'To Do', '[2]', NULL, NULL, 1),
(31, 2, 'In Progress', '[3]', NULL, NULL, 2),
(32, 2, 'In Review', '[4]', NULL, NULL, 3),
(33, 2, 'Testing', '[5]', NULL, NULL, 4),
(34, 2, 'Done', '[6]', NULL, NULL, 5),
(35, 2, 'Closed', '[7]', NULL, NULL, 6),
(36, 2, 'Reopened', '[8]', NULL, NULL, 7),
(37, 3, 'Open', '[1]', NULL, NULL, 0),
(38, 3, 'To Do', '[2]', NULL, NULL, 1),
(39, 3, 'In Progress', '[3]', NULL, NULL, 2),
(40, 3, 'In Review', '[4]', NULL, NULL, 3),
(41, 3, 'Testing', '[5]', NULL, NULL, 4),
(42, 3, 'Done', '[6]', NULL, NULL, 5),
(43, 3, 'Closed', '[7]', NULL, NULL, 6),
(44, 3, 'Reopened', '[8]', NULL, NULL, 7),
(45, 4, 'Open', '[1]', NULL, NULL, 0),
(46, 4, 'To Do', '[2]', NULL, NULL, 1),
(47, 4, 'In Progress', '[3]', NULL, NULL, 2),
(48, 4, 'In Review', '[4]', NULL, NULL, 3),
(49, 4, 'Testing', '[5]', NULL, NULL, 4),
(50, 4, 'Done', '[6]', NULL, NULL, 5),
(51, 4, 'Closed', '[7]', NULL, NULL, 6),
(52, 4, 'Reopened', '[8]', NULL, NULL, 7),
(53, 5, 'Open', '[1]', NULL, NULL, 0),
(54, 5, 'To Do', '[2]', NULL, NULL, 1),
(55, 5, 'In Progress', '[3]', NULL, NULL, 2),
(56, 5, 'In Review', '[4]', NULL, NULL, 3),
(57, 5, 'Testing', '[5]', NULL, NULL, 4),
(58, 5, 'Done', '[6]', NULL, NULL, 5),
(59, 5, 'Closed', '[7]', NULL, NULL, 6),
(60, 5, 'Reopened', '[8]', NULL, NULL, 7),
(61, 6, 'Open', '[1]', NULL, NULL, 0),
(62, 6, 'To Do', '[2]', NULL, NULL, 1),
(63, 6, 'In Progress', '[3]', NULL, NULL, 2),
(64, 6, 'In Review', '[4]', NULL, NULL, 3),
(65, 6, 'Testing', '[5]', NULL, NULL, 4),
(66, 6, 'Done', '[6]', NULL, NULL, 5),
(67, 6, 'Closed', '[7]', NULL, NULL, 6),
(68, 6, 'Reopened', '[8]', NULL, NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `budget_alerts`
--

CREATE TABLE `budget_alerts` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_budget_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `alert_type` enum('warning','critical','exceeded') NOT NULL,
  `threshold_percentage` decimal(5,2) NOT NULL,
  `actual_percentage` decimal(5,2) NOT NULL,
  `cost_at_alert` decimal(15,2) NOT NULL,
  `remaining_budget_at_alert` decimal(15,2) NOT NULL,
  `is_acknowledged` tinyint(1) DEFAULT 0,
  `acknowledged_by` int(10) UNSIGNED DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_events`
--

CREATE TABLE `calendar_events` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_type` enum('issue','sprint','milestone','reminder','meeting') NOT NULL DEFAULT 'reminder',
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `priority_id` int(10) UNSIGNED DEFAULT NULL,
  `attendees` text DEFAULT NULL COMMENT 'Comma-separated user emails or JSON',
  `reminders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Reminder settings array' CHECK (json_valid(`reminders`)),
  `recurring_type` enum('none','daily','weekly','monthly','yearly','custom') NOT NULL DEFAULT 'none',
  `recurring_interval` int(10) UNSIGNED DEFAULT NULL COMMENT 'Interval for recurring events',
  `recurring_ends` enum('never','after','on') DEFAULT NULL,
  `recurring_end_date` date DEFAULT NULL COMMENT 'End date for recurring events',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `body` longtext NOT NULL,
  `is_internal` tinyint(1) DEFAULT 0,
  `edit_count` int(11) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `issue_id`, `user_id`, `parent_id`, `body`, `is_internal`, `edit_count`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, 'I\'ve started working on the authentication module. Initial draft is ready for review.', 0, 0, 0, '2026-01-07 04:39:59', '2026-01-09 04:39:59'),
(2, 1, 4, NULL, 'Please make sure to add comprehensive unit tests for the OAuth flow.', 0, 0, 0, '2026-01-08 04:39:59', '2026-01-09 04:39:59'),
(3, 1, 2, NULL, 'Tests added and all passing. Ready for code review. @jane.doe', 0, 0, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 2, 3, NULL, 'Product catalog is looking good. Need to optimize the database queries.', 0, 0, 0, '2026-01-06 04:39:59', '2026-01-09 04:39:59'),
(5, 3, 2, NULL, 'Database schema reviewed and approved. Ready to proceed with implementation.', 0, 0, 0, '2026-01-07 04:39:59', '2026-01-09 04:39:59'),
(6, 4, 5, NULL, 'I found the mobile styling issue. It\'s related to the viewport meta tag.', 0, 0, 0, '2026-01-08 04:39:59', '2026-01-09 04:39:59'),
(7, 6, 3, NULL, 'Implemented full-text search using MySQL MATCH/AGAINST. Performance is good.', 0, 0, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(8, 7, 4, NULL, 'Email templates need to be reviewed by marketing team.', 0, 0, 0, '2026-01-07 04:39:59', '2026-01-09 04:39:59'),
(9, 9, 6, NULL, 'API client is complete and tested with all endpoints.', 0, 0, 0, '2026-01-08 04:39:59', '2026-01-09 04:39:59'),
(10, 11, 4, NULL, 'CI/CD pipeline is now live and running all tests automatically.', 0, 0, 0, '2026-01-06 04:39:59', '2026-01-09 04:39:59'),
(11, 14, 1, NULL, 'adding comment here, and checking it is working or not.', 0, 0, 0, '2026-01-19 04:19:34', '2026-01-19 04:19:48');

-- --------------------------------------------------------

--
-- Table structure for table `comment_history`
--

CREATE TABLE `comment_history` (
  `id` int(11) NOT NULL,
  `comment_id` int(10) UNSIGNED NOT NULL,
  `edited_by` int(10) UNSIGNED NOT NULL,
  `old_body` longtext DEFAULT NULL,
  `new_body` longtext DEFAULT NULL,
  `edited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `change_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `components`
--

CREATE TABLE `components` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `lead_id` int(10) UNSIGNED DEFAULT NULL,
  `default_assignee_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE `custom_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('text','textarea','number','date','datetime','select','multiselect','checkbox','radio','url','user') NOT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_field_contexts`
--

CREATE TABLE `custom_field_contexts` (
  `id` int(10) UNSIGNED NOT NULL,
  `field_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `issue_type_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_field_values`
--

CREATE TABLE `custom_field_values` (
  `id` int(10) UNSIGNED NOT NULL,
  `field_id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dashboards`
--

CREATE TABLE `dashboards` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `share_type` enum('private','project','global') NOT NULL DEFAULT 'private',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_gadgets`
--

CREATE TABLE `dashboard_gadgets` (
  `id` int(10) UNSIGNED NOT NULL,
  `dashboard_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `position_x` int(11) NOT NULL DEFAULT 0,
  `position_y` int(11) NOT NULL DEFAULT 0,
  `width` int(11) NOT NULL DEFAULT 1,
  `height` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE `email_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `error` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description?` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `issue_type_id` int(10) UNSIGNED NOT NULL,
  `status_id` int(10) UNSIGNED NOT NULL,
  `priority_id` int(10) UNSIGNED NOT NULL,
  `issue_key` varchar(20) NOT NULL,
  `issue_number` int(10) UNSIGNED NOT NULL,
  `summary` varchar(500) NOT NULL,
  `description` longtext DEFAULT NULL,
  `reporter_id` int(10) UNSIGNED NOT NULL,
  `assignee_id` int(10) UNSIGNED DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `epic_id` int(10) UNSIGNED DEFAULT NULL,
  `sprint_id` int(10) UNSIGNED DEFAULT NULL,
  `story_points` decimal(5,2) DEFAULT NULL,
  `original_estimate` int(10) UNSIGNED DEFAULT NULL,
  `remaining_estimate` int(10) UNSIGNED DEFAULT NULL,
  `time_spent` int(10) UNSIGNED DEFAULT 0,
  `environment` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `project_id`, `issue_type_id`, `status_id`, `priority_id`, `issue_key`, `issue_number`, `summary`, `description`, `reporter_id`, `assignee_id`, `parent_id`, `epic_id`, `sprint_id`, `story_points`, `original_estimate`, `remaining_estimate`, `time_spent`, `environment`, `due_date`, `start_date`, `end_date`, `resolved_at`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 3, 3, 'ECOM-1', 1, 'Implement user authentication', 'Set up login/signup functionality with OAuth support', 1, 2, NULL, NULL, 1, 8.00, 28800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-04 04:39:59', '2026-01-12 07:19:21'),
(2, 1, 2, 4, 2, 'ECOM-2', 2, 'Create product catalog system', 'Build backend and frontend for product browsing', 1, 3, NULL, NULL, 1, 13.00, 46800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-05 04:39:59', '2026-01-12 07:16:46'),
(3, 1, 3, 5, 3, 'ECOM-3', 3, 'Setup database schema', 'Create all required database tables and relationships', 1, 2, NULL, NULL, 1, 5.00, 18000, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-06 04:39:59', '2026-01-12 07:21:16'),
(4, 1, 4, 1, 1, 'ECOM-4', 4, 'Login page styling issues on mobile', 'Fix responsive design for mobile devices', 2, 3, NULL, NULL, 1, 3.00, 10800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-07 04:39:59', '2026-01-09 04:39:59'),
(5, 1, 2, 4, 2, 'ECOM-5', 5, 'Payment gateway integration', 'Integrate Stripe or PayPal for payments', 1, NULL, NULL, NULL, 2, 13.00, 46800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-08 04:39:59', '2026-01-09 04:39:59'),
(6, 1, 3, 2, 3, 'ECOM-6', 6, 'Add product search functionality', 'Implement full-text search for products', 2, 4, NULL, NULL, 1, 5.00, 18000, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-09 04:39:59', '2026-01-12 07:21:01'),
(7, 1, 3, 5, 2, 'ECOM-7', 7, 'Email notification system', 'Set up email notifications for orders', 1, 5, NULL, NULL, NULL, 3.00, 10800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-06 04:39:59', '2026-01-09 04:39:59'),
(8, 2, 2, 2, 3, 'MOBILE-1', 1, 'iOS app development', 'Build core features for iOS app', 1, 6, NULL, NULL, 4, 8.00, 28800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-05 04:39:59', '2026-01-09 04:39:59'),
(9, 2, 3, 3, 3, 'MOBILE-2', 2, 'API client for mobile', 'Create REST client for mobile apps', 1, 6, NULL, NULL, 4, 5.00, 18000, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-06 04:39:59', '2026-01-09 04:39:59'),
(10, 2, 4, 1, 2, 'MOBILE-3', 3, 'App crashing on Android devices', 'Fix crash on specific Android versions', 3, 5, NULL, NULL, 4, 3.00, 10800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-08 04:39:59', '2026-01-09 04:39:59'),
(11, 3, 3, 3, 2, 'INFRA-1', 1, 'Setup CI/CD pipeline', 'Configure GitHub Actions for automated testing', 1, 4, NULL, NULL, NULL, 8.00, 28800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-04 04:39:59', '2026-01-09 04:39:59'),
(12, 3, 3, 3, 3, 'INFRA-2', 2, 'Database backup strategy', 'Implement automated daily backups', 2, NULL, NULL, NULL, NULL, 5.00, 18000, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-07 04:39:59', '2026-01-09 05:24:47'),
(13, 3, 3, 4, 3, 'INFRA-3', 3, 'Monitor server performance', 'Set up monitoring dashboards and alerts', 1, 6, NULL, NULL, NULL, 3.00, 10800, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59');
INSERT INTO `issues` (`id`, `project_id`, `issue_type_id`, `status_id`, `priority_id`, `issue_key`, `issue_number`, `summary`, `description`, `reporter_id`, `assignee_id`, `parent_id`, `epic_id`, `sprint_id`, `story_points`, `original_estimate`, `remaining_estimate`, `time_spent`, `environment`, `due_date`, `start_date`, `end_date`, `resolved_at`, `sort_order`, `created_at`, `updated_at`) VALUES
(14, 4, 3, 4, 3, 'CWAYSMIS-1', 1, 'Issue being created', '<p>The issue being <strong>created<br></strong></p>\r\n<p>You are an AI assistant embedded in a production, enterprise-grade software system.</p>\r\n<p>PROJECT CONTEXT<br>- This is a production-ready, enterprise-level Jira clone for internal company use.<br>- The codebase is modular and used by real users; stability and backward compatibility are mandatory.</p>\r\n<p>MANDATORY CONTEXT LOADING<br>- Always read and understand all `.md` files and `agents.md` before proposing changes.</p>\r\n<p>CORE RULES<br>- Be accurate, practical, and concise.<br>- Never invent APIs, data, or behavior.<br>- If information is missing, state it clearly and request what is needed.<br>- All output must be production-ready.</p>\r\n<p>CHANGE POLICY (CRITICAL)<br>- Never change code blindly.<br>- First identify where the code is used and what depends on it.<br>- Preserve existing behavior unless explicitly approved to change it.</p>\r\n<ol>\r\n<li>BUG / FIX WORKFLOW<br>1. Identify the root cause.<br>2. Verify current usage and dependencies.<br>3. Propose the minimal safe fix.<br>4. Ensure backward compatibility.<br>5. Explain why the fix is safe.</li>\r\n</ol>\r\n<p>DEFAULT MODE<br>- Production-first<br>- Stability-first<br>- Backward-compatibility-first<br><img src=\"data:image/png;base64,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L+kBEgiAgG0CdKfQ/UJ3TZE3cVDHFkX6wME2ATC0zYdq6waFPDPktREf/jDwzW/CB7zhExoZ0qxF104tE29m5D9Ibt+6ab+/jwjr8mLrHq8+OXhhl5ErmnYeWat2EDVEsk0AArRtPqgFARAAARAAARAoDgH4ggAIgAAIgAAIMObp4V4/0D80pD4SCICAbQJ0p9D9Ys/HRvu/9bbHDT42CIChDTgmVcEtHusycEyP8e8/MeifQz7a9ernv7Z77tMBH+1q1WeOT0CvWrU7urrXN2mCQxsEIEDbgFOVqzB2EAABEAABEAABEAABEAABEAABEACB6k+gpsyw1V8jO0zqX1NmWwbzJHrEsAwCIyQIFE0AAnTRjOABAiAAAiAAAiAAAiAAAkURQD0IgAAIgAAIgEDZEuj23uh+X05t8GRLhUJRtj1Vo+gKhYKIETeiV42mhalUMQIQoKvYCcNwQQAEiiKAehAAARAAARAAARAAARAAARAAgepJoNVfI4dv+2Dm7f/OurMJyR4CxIqIEbdqeUFgUlWFAAToqnKmME4QAAEQAAEQAAEQAAEQAAEQqIwEMCYQAAEQAAEQAAEQsEEAArQNOKgCARAAARAAgapEAGMFARAAARAAARAAARAAARAAARAAgcpGAAK0488IIoIACIAACIAACIAACIAACIAACIAACFR/ApghCIAACICAHQQgQNsBCS4gAAIgAAIgAAIgAAKVmQDGBgIgAAIgAAIgAAIgAAIgUFkJQICurGcG4wKBqkgAYwYBEAABEAABEAABEAABEAABEAABEKj+BDBDECgGAQjQxYAFVxAAARAAARAAARAAARAAARCoTAQwFhAAARAAARAAARCo7AQgQFf2M4TxgQAIgAAIVAUCGCMIgAAIgAAIgAAIgAAIgAAIgAAIgIAFAtVMgLYwQ5hAAARAAARAAARAAARAAARAAARAAASqGQFMBwRAAARAoKoQgABdVc4UxgkCIAACIAACIAAClZEAxgQCIAACIAACIAACIAACIAACNghAgLYBB1UgUJUIYKwgAAIgAAIgAAIgAAIgAAIgAAIgAALVnwBmCAJVjQAE6Kp2xjBeEAABEAABEAABEAABEACBykAAYwABEAABEAABEAABELCDAARoOyDBBQRAAARAoDITwNhAAARAAARAAARAAARAAARAAARAAAQqKwHHCdCV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TJrUh3dhdjKnRgIkQAIkQAIkQAIkQAIkQAIkQAIk4P0EuEISKNkEGIDOaf9Ce+tbQi8dtbBPTveDzqkl7flOoLq5GfSS/xvY5KZ875sdkgAJkAAJkAAJkAAJkICHBOhGAiRAAiRAAiRAAiSQZwIMQOcZGRuQAAmQAAkUNQGOTwIkQAIkQAIkQAIkQAIkQAIkQAIkUDIIXEsAumSskLMkARIgARIgARIgARIgARIgARIgARK4FgJsSwIkQAIkQAJXTYAB6KtGx4YkQAIkQAIkQAIkUNgEOB4JkAAJkAAJkAAJkAAJkAAJlCwCDECXrP3ibIsLAc6DBEiABEiABEiABEiABEiABEiABEjA+wlwhSRAAtdMgAHoa0bIDkiABEiABEiABEiABEiABAqaAPsnARIgARIgARIgARIomQQYgC6Z+8ZZkwAJkEBREeC4JEACJEACJEACJEACJEACJEACJEAC3k8g31bIAHS+oWRHJEACJEACJEACJEACJEACJEACJJDfBNgfCZAACZAACZRsAgxAl+z94+xJgARIgARIgAQKiwDHIQESIAESIAESIAESIAESIAESyDMBBqDzjIwNipoAxycBEiABEiABEiABEiABEiABEiABEvB+AlwhCZCAdxBgANo79pGrIAESIAESIAESIAESIIGCIsB+SYAESIAESIAESIAESOCqCTAAfdXo2JAESIAECpsAxyMBEiABEiABEiABEiABEiABEiABEvB+At61QgagvWs/uRoSIAESIAESIAESIAESIAESIIH8IsB+SIAESIAESIAErpkAA9DXjJAdkAAJkAAJkAAJFDQB9k8CJEACJEACJEACJEACJEACJFAyCTAAXTL3rahmzXFJgARIgARIgARIgARIgARIgARIgAS8nwBXSAIkQAL5RoAB6HxDyY5IgARIgARIgARIgARIIL8JsD8SIAESIAESIAESIAESKNkEGIAu2fvH2ZMACRQWAY5DAiRAAiRAAiRAAiRAAiRAAiRAAiTg/QS4wnwnwAB0viNlhyRAAiRAAiRAAiRAAiRAAiRAAtdKgO1JgARIgARIgAS8gwAD0N6xj1wFCZAACZAACRQUAfZLAiRAAiRAAiRAAiRAAiRAAiRAAldNgAHoq0ZX2A05HgmQAAmQAAmQAAmQAAmQAAmQAAmQgPcT4ApJgARIwLsIMADtXfvJ1ZAACZAACZAACZAACeQXAfZDAiRAAiRAAiRAAiRAAiRwzQQYgL5mhOyABEigoAmwfxIgARIgARIgARIgARIgARIgARIgAe8nwBV6J4HrjsYnUkiABEiABEiABEiABEiABEiABEjAToAfEkmABEiABEiABEggM4Grjo5fVys4kEICJEACJEACJFAsCfA1mgRIgARIgARIgARIgARIgARIgASKBYGrD0BfdcvS1JBrJQESIAESIAESIAESIAESIAESIAES8H4CXCEJkAAJkEC+E+A9oPMdKTskARIgARIgARIgARK4VgJsTwIkQAIkQAIkQAIkQAIk4B0EGID2jn3kKkigoAiwXxIgARIgARIgARIgARIgARIgARIgAe8nwBWSQIERYAC6wNCyYxIgARIgARIgARIgARIgARLIKwH6kwAJkAAJkAAJkIB3EWAA2rv2k6shARIgARLILwLshwRIgARIgARIgARIgARIgARIgARI4JoJFPsA9DWvkB2QAAmQAAmQAAmQAAmQAAmQAAmQAAkUewKcIAmQAAmQgHcSYADaO/eVqyIBEiABEiABEiCBqyXAdiRAAiRAAiRAAiRAAiRAAiSQbwQYgM43lOyIBPKbAPsjARIgARIgARIgARIgARIgARIgARLwfgJcIQl4NwEGoL17f7k6EiABEiABEiABEiABEiABTwnQjwRIgARIgARIgARIIN8JMACd70jZIQmQAAmQwLUSYHsSIAESIAESIAESIAESIAESIAESIAHvIOAuAO0dK+QqSIAESIAESIAESIAESIAESIAESIAE3BFgHQmQAAmQAAkUGAEGoAsMLTsmARIgARIgARIggbwSoD8JkAAJkAAJkAAJkAAJkAAJeBcBdwHoyzt2/PLGGxQSKI0E+JdPAiRAAiRAAiRAAiRAAiRAAiRAAiTg/QQY+CIBEnASuLR2bUGEvt0FoFM3bfp5yhQKCZAACZAACZAACZAACZAACRQwAX7uIAESIAESIAESIAESKGICvy1bVtgB6IIYj32SAAmQAAkUbwKcHQmQAAmQAAmQAAmQAAmQAAmQAAmQgPcTKLQVursCutAmwYFIgARIgARIgARIgARIgARIgARIoHQS4KpJgARIgARIwLsJMADt3fvL1ZEACZAACZAACXhKgH4kQAIkQAIkQAIkQAIkQAIkQAL5TiAPAejrW7e+6ZFHKCRQ0ATYPwmQAAmQAAmQAAmQAAmQAAmQAAmQgPcTYJSJBEigqAmUCw/P93Bz1g7zEID27dLl5okTKSRAAiRAAiRAAiRAAiRAAl5FgG/ySYAESIAESIAESIAESiWBGwYOzBovzndLHgLQ+T42OyQBEiABEshAgAUSIAESIAESIAESIAESIAESIAESIAHvJ1C6VsgAdOnab66WBEiABEiABEiABEiABEiABEjAToA5CZAACZAACZBAgRNgALrAEXMAEiABEiABEiCB3AiwngRIgARIgARIgARIgARIgARIwDsJMADtnft6tatiOxIgARIgARIgARIgARIgARIgARIgAe8nwBWSAAmQQKERYAC60FBzIBIgARIgARIgARIgARLITIBlEiABEiABEiABEiABEvBuAgxAe/f+cnUkQAKeEqAfCZAACZAACZAACZAACZAACZAACZCA9xPgCgudAAPQhY6cA5IACZAACZAACZAACZAACZAACZAACZAACZAACZBA6SDAAHTp2GeukgRIgARIgARyIkA7CZAACZAACZAACZAACZAACZAACRQYAQagCwxtXjumPwmQAAmQAAmQAAmQAAmQAAmQAAmQgPcT4ApJgARIoHQRYAC6dO03V0sCJEACJEACJEACJGAnwJwESIAESIAESIAESIAESKDACTAAXeCIOQAJkEBuBFhPAiRAAiRAAiRAAiRAAiRAAiRAAiTg/QS4wtJJgAHo0rnvXDUJkAAJkAAJkAAJkAAJkEDpJcCVkwAJkAAJkAAJkEChEWAAutBQcyASIAESIAESyEyAZRIgARIgARIgARIgARIgARIgARLwbgIMQOv95UkCJEACJEACJEACJEACJEACJEACJOD9BLhCEiABEiCBQifAAHShI+eAJEACJEACJEACJEACJEACJEACJEACJEACJEACJFA6CDAAXTr2maskgZwI0E4CJEACJEACJEACJEACJEACJEACJOD9BLhCEigyAgxAFxl6DkwCJEACJEACJEACJEACJFD6CHDFJEACJEACJEACJFC6CDAAXbr2m6slARIgARKwE2BOAiRAAiRAAiRAAiRAAiRAAiRAAiRQ4ASKPABd4CvkACRAAiRAAiRAAiRAAiRAAiRAAiRAAkVOgBMgARIgARIonQQYgC6d+85VkwAJkAAJkAAJlF4CXDkJkAAJkAAJ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EigMAgxAFwZljkECJEACJEACORNgDQmQAAmQAAmQAAmQAAmQAAmQAAl4LQEGoB1bS4UESIAESIAESIAESIAESIAESIAESMD7CXCFJEACJEAChUmAAejCpM2xSIAESIAESIAESIAEnASokQAJkAAJkAAJkAAJkAAJeD0BBqC9fou5QBLInQA9SIAESIAESIAESIAESIAESIAESIAEvJ8AV0gCRUGAAeiioM4xSYAESIAESIAESIAESIAESjMBrp0ESIAESIAESIAESg0BBqBLzVZzoSRAAiRAAlkJ0EICJEACJEACJEACJEACJEACJEACJFCQBIpHALogV8i+SYAESIAESIAESIAESIAESIAESIAEigcBzoIESIAESKDUEWAAutRtORdMAiRAAiRAAiRAAiJkQAIkQAIkQAIkQAIkQAIkQAKFQYAB6MKgzDFIIGcCrCEBEiABEiABEiABEiABEiABEiABEvB+AlwhCZRaAgxAl9qt58JJgARIgARIgARIgARIoDQS4JpJgARIgARIgARIgAQKk0AeAtC/JyZe3rmTQgIkQAIkQAL5QoCdkAAJkAAJkAAJkAAJkAAJkAAJkAAJFCGBPxITCyESfZ3nY/y6ZMnZ3r0pJEACJEACJEACJEACJEACJEACJEACJY8AP9GTAAmQAAmQQEYCyePHex4cvmrPPFwBfdVjsCEJkAAJkAAJkAAJkICTADUSIAESIAESIAESIAESIAESKDUEGIAuNVvNhWYlQAsJkAAJkAAJkAAJkAAJkAAJkAAJkID3E+AKSYAEipIAA9BFSZ9jkwAJkAAJkAAJkAAJkEBpIsC1kgAJkAAJkAAJkAAJlDoC7gLQZQIDr2/VikICJEACJOB1BPjcTgIkQAIkQAIkQAIkQAIkQAIkQAIk4P0E8hTQuC4wsCCi4+4C0DcOHlx59WoKCZAACZAACZAACZAACZAACZAACZDAtRBgWxIgARIgARIo/gT8p08v7AB0QYzHPkmABEiABEiABEigCAlwaBIgARIgARIgARIgARIgARIggcIk4O4K6MKcB8cqbQS4XhIgARIgARIgARIgARIgARIgARIgAe8nwBWSAAmUegIMQJf6PwECIAESIAESIAESIAESKA0EuEYSIAESIAESIAESIAESKAoCDEAXBXWOSQIkUJoJcO0kQAIkQAIkQAIkQAIkQAIkQAIkQALeT4ArtBFgANoGghkJkAAJkAAJkAAJkAAJkAAJkIA3EuCaSIAESIAESIAEipIAA9BFSZ9jkwAJkAAJkEBpIsC1kgAJkAAJkAAJkAAJkAAJkAAJlDoCDECXui0X4ZJJgARIgARIgARIgARIgARIgARIgAS8nwBXSAIkQALFgQAD0MVhFzgHEiABEiABEiABEiABbybAtZEACZAACZAACZAACZBAqSXAAHSp3XounARKIwGumQRIgARIgARIgARIgARIgARIgARIwPsJcIXFiQAD0MVpNzgXEiABEiABEiABEiABEiABEvAmAlwLCZAACZAACZBAqSfAAHSp/xMgABIgARIggdJAgGskARIgARIgARIgARIgARIgARIggaIgwAB04VLnaCRAAiRAAiRAAiRAAiRAAiRAAiRAAt5PgCskARIgARKwEWAA2gaCGQmQAAmQAAmQAAmQgDcS4JpIgARIgARIgARIgARIgASKkgAD0EVJn2OTQGkiwLWSAAmQAAmQAAmQAAmQAAmQAAmQAAl4PwGukAQyEWAAOhMQFkmABEiABEiABEiABEiABEjAGwhwDSRAAiRAAiRAAiRQHAgwAF0cdoFzIAESIAES8GYCXBsJkAAJkAAJkAAJkAAJkAAJkAAJlFoCpSgAXWr3mAsnARIgARIgARIgARIgARIgARIggVJEgEslARIgARIoTgQYgC5Ou8G5kAAJkAAJkAAJkIA3EeBaSIAESIAESIAESIAESIAESj2B69J5kAAJeD0BLpAESIAESIAESIAESIAESIAESIAESMD7CXCFJFCABK46kH7dkZ8SDh89YZMjxw9TSIAESIAESIAESIAESIAESIAErokAP1iRAAmQAAmQAAmQgFcQsMeNEUO++gB0nVrV69auYZM6NetSSIAESIAESMB7CPB1jQRIgARIgARIgARIgARIgARIgARI4GoJ2OPGiCFffQD6qlvmpSF9SYAESIAESIAESIAESIAESIAESIAEvJ8AV0gCJEACJEACmQjwRwgzAWGRBEiABEiABEiABLyBANdAAiRAAiRAAiRAAiRAAiRAAsWBAAPQxWEXOAdvJsC1kQAJkAAJkAAJkAAJkAAJkAAJkAAJeD8BrpAESCAHAgxA5wCGZhIgARIgARIgARIgARIggZJIgHMmARIgARIgARIgARIoTgQYgC5Ou8G5kAAJkIA3EeBaSIAESIAESIAESIAESIAESIAESIAEvJ9ALitkADoXQKwmARIgARIgARIgARIgARIgARIggZJAgHMkARIgARIggeJIgAHo4rgrnBMJkAAJkAAJkEBJJsC5kwAJkAAJkAAJkAAJkAAJkAAJ2AgwAG0DwcwbCXBNJEACJEACJEACJEACJEACJEACJEAC3k+AKyQBEijOBBiALs67w7mRAAmQAAmQAAmQAAmQQEkiwLmSAAmQAAmQAAmQAAmQQCYCDEBnAsIiCZAACXgDAa6BBEiABEiABEiABEiABEiABEiABEjA+wmUhBUyAF0SdolzJAESIAESIAESIAESIAESIAESKM4EODcSIAESIAESIIEcCDAAnQMYmkmABEiABEiABEoiAc6ZBEiABEiABEiABEiABEiABEigOBFgALo47YY3zYVrIQESIAESIAESIAESIAESIAESIAES8H4CXCEJkAAJ5EKAAehcALGaBEiABEiABEiABEiABEoCAc6RBEiABEiABEiABEiABIojtWhHDAAAEABJREFUAQagi+OucE4kkC2BlF9/Szx19mh8YukRrBerzpZGMTZyaiRAAiRAAiRAAiRAAiRAAiRAAsWdAD5s4iNn6fl87X6lQAEgnu/Zvg+i/9vl6ZnVH3wpqH9pFmvt4AAaYOI5QNAGc/ebci216BxDeD6fgvZkALqgCbN/EsgfAucvXEy+mFKh/E21ggNLj2C9WDXWnj8Q2QsJkAAJkAAJkAAJkIA3EuCaSIAESCCvBPAxEx828ZGz9Hy+dr9SoAAQYPGE5MZJizf8c+GZ/fHp6eme+Hu9DziABpiAjCeLBWfQBnP3m3IttegcQ2AgT+ZTCD75H4BGfB1R9msJ0rMtCZQSAnik4PHiyeMcbr9duhx0S5UbbvD1xN9rfLBerBprBwGvWRQXQgJeTIBLIwESIAESIAESIAESIIHiTwAfMPExEx828ZGz+M+2cGYIFAACLIDjfsR9H0T/8P4Xqcm/uncrhbVgAjLg437tIAzOoA3m7j2vpRadYwgMhOGupZ/8apvPAWhE1i8k/3LjDTfcUq0KpagIcNySQgCPFDxe8KjJ9fF88edfK/rfnKubtzpg7SDgravjukiABEiABEiABEiABEiABEjgKgmw2VURwAdMfMy8qqZe3ghYAMf9IrfOXY1Iq3uf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hiLw7Bcc12hcetG3i8llioIAABAASURBVBcjLz3oBa/41RNf8Qv79nksGzY83ucntB/+tku+/Xt/fvFWZfUjjcdumXTl4vdedPuMAb9737pGY+VRL91/xrszXFx396cmRf7O/9jz+L962/P/09O3v/0n13/2ixd//b4ZOm25NL37lnvDqh37/gsu+sTZ515wwZl+CWFba3Doib+5382f+uy/rJ6+B735rsvOvfA7m/d/bt83qbIN/bpf3u32C8ff46Kx23P+uLkxPfb2FOd89IwPtF74fOXnz3r72T89duyNNV42xz+1tDX3PjQq/wj7MGkhqyBw8DMPzl+uP7h1rz32nCgrl1Vh5MZIgAABAgQGI+ApBAgQIECAwOgI7PGqU3650fjGme/8xqTfzzQrQOt76ou/9f3xBo9c+W/XNRr77dV8dW/z2iMPr2l+aDQe/tfv31zUxo933Dm+u3rP9y+9pdF45kFt79uOhyjbx7+/5O47Go0DfvHEv2z95sA5h3fznQ81GstWHrbk+r+/fKL89OoT9z9sceORG6/49Y9d8uFLFi1bPFuMWbrP1nyA1xfteezJJ736ec0XdA/wqb181EBfAd1obP+s33zLG3e6+oMf/MJ5V98+9q7sGx6+8+pLPvbBr3z9nscba376V+defOOjvZzhTLGeus+ejdX3tv6f3Hu3Le9oManpVVdc39jqjTUm3Zt0sveu+zRuv2LiVxROutflyZVX3944/OBj0nv68O694YrVxQur2x5h4jQa11699W8vvO9O7wHdYnHokcCBRx/WaNxwwbnnX/jlb93ROOCk33lhY8MX3nzcH37x0u99/9KLvnjmyX/WegOQHj2th2GEIkCAAAECBAgQIECAAAEC/RVY+uo/+5vX73Hf197ynKNPPv30P3zv6X94+snvu2TWZx7w2jNP3rP4nvriiy757Ltec9o3G0e9//dfvSz7sM89YUVj1V+cfvoXLrn4C2e+4iM/XDo5yhX/z282b130xdNfc+YVjf3e+abjJ9+v4tlVF/zRP63esHjlK9/01ps/feplf9Ys//rZd172f0+fzMOfuOq+DY1l//FNb/3/v+tl//VNr/jLPz71n99zTOPBxx5tNFY8/YhP/t8n/uVHj3vxLlt1vGtd/lFgxdOP+Mt3vfq/nzhL962aD6v6+CPXfOuSS6/NWIc1goU+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AQIEeipgA7qnnIIRIECAQK8ExCFAgAABAgQIECBAgAABAgSqLzDfBnT1Z2gGBAgQIECAAAECBAgQIECAwHwC7hMgQIAAgb4I2IDuC6ugBAgQIECAAIFuBfQjQIAAAQIECBAgQIBAfQRsQNdnLc2k1wLiESBAgAABAgQIECBAgAABAvUXMEMCBPoqYAO6r7yCEyBAgAABAgQIECDQroB2BAgQIECAAAEC9ROwAV2/NTUjAgQILFRAfwIECBAgQIAAAQIECBAgQKD+AgOZoQ3ogTB7CAECBAgQIECAAAECBAgQmE3AdQIECBAgUF8BG9D1XVszI0CAAAECBDoV0J4AAQIECBAgQIAAAQIEeipgA7qnnIL1SkAcAgQIECBAgAABAgQIECBAoP4CZkiAQP0FbEDXf43NkAABAgQIECBAgMB8Au4TIECAAAECBAgQ6IuADei+sApKgACBbgX0I0CAAAECBAgQIECAAAECBOovMDoztAE9OmttpgQIECBAgAABAgQIECAwVcA5AQIECBAg0FcBG9B95RWcAAECBAgQaFdAOwIECBAgQIAAAQIECBCon4AN6Pqt6UJnpD8BAgQIECBAgAABAgQIECBQfwEzJECAwEAEbEAPhNlDCBAgQIAAAQIECMwm4DoBAgQIECBAgACB+grYgK7v2poZAQKdCmhPgAABAgQIECBAgAABAgQI1F/ADAcqYAN6oNweRoAAAQIECBAgQIAAAQLjAj4SIECAAAEC9RewAV3/NTZDAgQIECAwn4D7BAgQIECAAAECBAgQIECgLwI2oPvC2m1Q/QgQIECAAAECBAgQIECAAIH6C5ghAQIERkfABvTorLWZEiBAgAABAgQITBVwToAAAQIECBAgQIBAXwVsQPeVV3ACBNoV0I4AAQIECBAgQIAAAQIECBCov4AZjp6ADejRW3MzJkCAAAECBAgQIECAAAECBAgQIECAwEAEbEAPhNlDCBAgQIDAbAKuEyBAgAABAgQIECBAgACB+grYgB5fWx8JECBAgAABAgQIECBAgACB+guYIQECBAgMVMAG9EC5PYwAAQIECBAgQGBcwEcCBAgQIECAAAECBOovYAO6/mtshgTmE3CfAAECBAgQIECAAAECBAgQqL+AGRIYioAN6KGweygBAgQIECBAgAABAqMrYOYECBAgQIAAgdERsAE9OmttpgQIECAwVcA5AQIECBAgQIAAAQIECBAg0FeBUmxA93WGghMgQIAAAQIECBAgQIAAAQKlEDAIAgQIEBg9ARvQo7fmZkyAAAECBAgQIECAAAECBAgQIECAAIGBCNiAHgizhxCYTcB1AgQIECBAgAABAgQIECBAoP4CZkhgdAVsQI/u2ps5AQIECBAgQIAAgdETMGMCBAgQIECAAIGBCtiAHii3hxEgQIDAuICPBAgQIECAAAECBAgQIECAQP0FbEDXf43NkAABAgQIECBAgAABAgQIECBAgAABAkMRsAE9FHYPJUCAAAECBEZXwMwJECBAgAABAgQIECAwOgI2oEdnrc10qoBzAgQIECBAgAABAgQIECBAoP4CZkiAwFAFbEAPld/DCRAgQIAAAQIECIyOgJkSIECAAAECBAiMnoAN6NFbczMmQIAAAQIECBAgQIAAAQIECBAgQKD+AqWYoQ3oUiyDQRAgQIAAAQIECBAgQIBAfQXMjAABAgQIjK6ADejRXXszJ0CAAAECoydgxgQIECBAgAABAgQIECAwUAEb0APl9rBxAR8JECBAgAABAgQIECBAgACB+guYIQECBGxA+xwgQIAAAQIECBAgUH8BMyRAgAABAgQIECAwFAEb0ENh91ACBEZXwMwJECBAgAABAgQIECBAgACB+guY4biADehxCR8JECBAgAABAgQIECBAoH4CZkSAAAECBAgMVcAG9FD5PZwAAQIECIyOgJkSIECAAAECBAgQIECAwOgJ2IAevTU3YwIECBAgQIAAAQIECBAgQKD+AmZIgACBUgjYgC7FMhgEAQIECBAgQIBAfQXMjAABAgQIECBAgMDoCtiAHt21N3MCoydgxgQIECBAgAABAgQIECBAgED9BcywVAI2oEu1HAZDgAABAgQIECBAgACB+giYCQECBAgQIEDABrTPAQIECBAgUH8BMyRAgAABAgQIECBAgAABAkMRsAE9UHYPI0CAAAECBAgQIECAAAECBOovYIYECBAgMC5gA3pcwkcCBAgQIECAAIH6CZgRAQIECBAgQIAAAQJDFbABPVR+DycwOgJmSoAAAQIECBAgQIAAAQIECNRfwAwJTBWwAT1VxDkBAgQIECBAgAABAgSqL2AGBAgQIECAAIFSCNiALsUyGAQBAgQI1FfAzAgQIECAAAECBAgQIECAwOgKjM4G9OiusZkTIECAAAECBAgQIECAAIHRETBTAgQIECiVgA3oUi2HwRAgQIAAAQIE6iNgJgQIECBAgAABAgQIELAB7XOAQP0FzJAAAQIECBAgQIAAAQIECBCov4AZEiilgA3oUi6LQREgQIAAAQIECBAgUF0BIydAgAABAgQIEBgXsAE9LuEjAQIECNRPwIwIECBAgAABAgQIECBAgACBoQoMZAN6qDP0cAIECBAgQIAAAQIECBAgQGAgAh5CgAABAgSmCtiAnirinAABAgQIECBQfQEzIECAAAECBAgQIECAQCkEbECXYhkMor4CZkaAAAECBAgQIECAAAECBAjUX8AMCRCYTcAG9GwyrhMgQIAAAQIECBAgUD0BIyZAgAABAgQIECiVgA3oUi2HwRAgQKA+AmZCgAABAgQIECBAgAABAgQI1F9gvhnagJ5PyH0CBAgQIECAAAECBAgQIFB+ASMkQIAAAQKlFLABXcplMSgCBAgQIECgugJGToAAAQIECBAgQIAAAQLjAjagxyV8rJ+AGREgQIAAAQIECBAgQIAAAQL1FzBDAgRKLWADutTLY3AECBAgQIAAAQIEqiNgpAQIECBAgAABAgSmCtiAnirinAABAtUXMAMCBAgQIECAAAECBAgQIECg/gKVmKEN6Eosk0ESIECAAAECBAgQIECAQHkFjIwAAQIECBCYTcAG9GwyrhMgQIAAAQLVEzBiAgQIECBAgAABAgQIECiVgA3oUi1HfQZjJgQIECBAgAABAgQIECBAgED9BcyQAAEC8wnYgJ5PyH0CBAgQIECAAAEC5RcwQgIECBAgQIAAAQKlFLABXcplMSgCBKorYOQECBAgQIAAAQIECBAgQIBA/QXMsF0BG9DtSmlHgAABAgQIECBAgAABAuUTMCICBAgQIECg1AI2oEu9PAZHgAABAgSqI2CkBAgQIECAAAECBAgQIEBgqoAN6Kki1T83AwIECBAgQIAAAQIECBAgQKD+AmZIgACBSgjYgK7EMhkkAQIECBAgQIBAeQWMjAABAgQIECBAgACB2QRsQM8m4zoBAtUTMGICBAgQIECAAAECBAgQIECg/gJmWCkBG9CVWi6DJUCAAAECBAgQIECAQHkEjIQAAQIECBAgMJ+ADej5hNwnQIAAAQLlFzBCAgQIECBAgAABAgQIECBQSgEb0D1dFsEIECBAgAABAgQIECBAgACB+guYIQECBAi0K2ADul0p7QgQIECAAAECBMonYEQECBAgQIAAAQIECJRawAZ0qZfH4AhUR8BICRAgQIAAAQIECBAgQIAAgfoLmCGBTgVsQHcqpj0BAgQIECBAgAABAgSGL2AEBAgQIECAAIFKCNiArsQyGSQBAgQIlFfAyAgQIECAAAECBAgQIECAAIHZBOqzAT3bDF0nQIAAAQIECBAgQIAAAQIE6iNgJgQIECBQKQEb0JVaLoMlQIAAAQIECJRHwEgIECBAgAABAgQIECAwn4AN6PmE3CdQfgEjJECAAAECBAgQIECAAAECBOovYIYEKilgA7qSy2bQBAgQIECAAAECBAgMT8CTCRAgQIAAAQIE2hWwAd2ulHYECBAgUD4BIyJAgAABAgQIECBAgAABAgRKLdCTDehSz9DgCBAgQIAAAQIECBAgQIAAgZ4ICEKAAAECBDoVsAHdqZj2BAgQIECAAIHhCxgBAQIECBAgQIAAAQIEKiFgA7oSy2SQ5RUwMgIECBAgQIAAAQIECBAgQKD+AmZIgEC3Ajagu5XTjwABAgQIECBAgACBwQt4IgECBAgQIECAQKUEbEBXarkMlgABAuURMBICBAgQIECAAAECBAgQIECg/gILE4XyiwAADGBJREFUnaEN6IUK6k+AAAECBAgQIECAAAECBPov4AkECBAgQKCSAjagK7lsBk2AAAECBAgMT8CTCRAgQIAAAQIECBAgQKBdARvQ7UppVz4BIyJAgAABAgQIECBAgAABAgTqL2CGBAhUWsAGdKWXz+AJECBAgAABAgQIDE7AkwgQIECAAAECBAh0KmADulMx7QkQIDB8ASMgQIAAAQIECBAgQIAAAQIE6i9QixnagK7FMpoEAQIECBAgQIAAAQIECPRPQGQCBAgQIECgWwEb0N3K6UeAAAECBAgMXsATCRAgQIAAAQIECBAgQKBSAjagK7Vc5RmskRAgQIAAAQIECBAgQIAAAQL1FzBDAgQILFTABvRCBfUnQIAAAQIECBAg0H8BTyBAgAABAgQIECBQSQEb0JVcNoMmQGB4Ap5MgAABAgQIECBAgAABAgQI1F/ADHslYAO6V5LiECBAgAABAgQIECBAgEDvBUQkQIAAAQIEKi1gA7rSy2fwBAgQIEBgcAKeRIAAAQIECBAgQIAAAQIEOhWwAd2p2PDbGwEBAgQIECBAgAABAgQIECBQfwEzJECAQC0EbEDXYhlNggABAgQIECBAoH8CIhMgQIAAAQIECBAg0K2ADehu5fQjQGDwAp5IgAABAgQIECBAgAABAgQI1F/ADGslYAO6VstpMgQIECBAgAABAgQIEOidgEgECBAgQIAAgYUK2IBeqKD+BAgQIECg/wKeQIAAAQIECBAgQIAAAQIEKilgA7qjZdOYAAECBAgQIECAAAECBAgQqL+AGRIgQIDAHALr169v/64N6Dms3CJAgAABAgQIEBiygMcTIECAAAECBAgQIFA2gVtvvXWOIU25awN6Diu3CBDYIqBGgAABAgQIECBAgAABAgQI1F/ADAm0IXDllVfO0WrKXRvQc1i5RYAAAQIECBAgQIAAgSEJeCwBAgQIECBAoKwCq1evvuiii2YcXa7n7ta3bEBvraFOgAABAgSmCbhAgAABAgQIECBAgAABAgQITBa48cYbP//5z1977bXF+0HnmHqu5Prkho3qbEBPGbhTAgQIECBAgAABAgQIECBAoIYCpkSAAAEC1RBYvXr1JZdc8qlPfepjH/tYjqnnyvSh24CebuIKAQIECBAgQIBAo9GAQIAAAQIECBAgQIAAgYUK2IBeqKD+BPov4AkECBAgQIAAAQIECBAgQIBA/QXMkEAtBWxA13JZTYoAAQIECBAgQIAAge4F9CRAgAABAgQIEOiVgA3oXkmKQ4AAAQK9FxCRAAECBAgQIECAAAECBAgQqLRAWxvQlZ6hwRMgQIAAAQIECBAgQIAAAQJtCWhEgAABAgR6LWADutei4hEgQIAAAQIEFi4gAgECBAgQIECAAAECBGohYAO6FstoEv0TEJkAAQIECBAgQIAAAQIECBCov4AZEiDQLwEb0P2SFZcAAQIECBAgQIAAgc4F9CBAgAABAgQIEKiVgA3oWi2nyRAgQKB3AiIRIECAAAECBAgQIECAAAEC9Rfo9wxtQPdbWHwCBAgQIECAAAECBAgQIDC/gBYECBAgQKCWAjaga7msJkWAAAECBAh0L6AnAQIECBAgQIAAAQIECPRKwAZ0ryTF6b2AiAQIECBAgAABAgQIECBAgED9BcyQAIFaC9iArvXymhwBAgQIECBAgACB9gW0JECAAAECBAgQINBrARvQvRYVjwABAgsXEIEAAQIECBAgQIAAAQIECBCov8BIzNAG9Egss0kSIECAAAECBAgQIECAwOwC7hAgQIAAAQL9ErAB3S9ZcQkQIECAAIHOBfQgQIAAAQIECBAgQIAAgVoJ2ICu1XL2bjIiESBAgAABAgQIECBAgAABAvUXMEMCBAj0W8AGdL+FxSdAgAABAgQIECAwv4AWBAgQIECAAAECBGopYAO6lstqUgQIdC+gJwECBAgQIECAAAECBAgQIFB/ATMclIAN6EFJew4BAgQIECBAgAABAgQITBdwhQABAgQIEKi1gA3oWi+vyREgQIAAgfYFtCRAgAABAgQIECBAgAABAr0WsAHda9GFxxOBAAECBAgQIECAAAECBAgQqL+AGRIgQGAkBGxAj8QymyQBAgQIECBAgMDsAu4QIECAAAECBAgQINAvARvQ/ZIVlwCBzgX0IECAAAECBAgQIECAAAECBOovYIYjJWADeqSW22QJECBAgAABAgQIECCwRUCNAAECBAgQINBvARvQ/RYWnwABAgQIzC+gBQECBAgQIECAAAECBAgQqKWADehJy+qEAAECBAgQIECAAAECBAgQqL+AGRIgQIDAoARsQA9K2nMIECBAgAABAgSmC7hCgAABAgQIECBAgECtBWxA13p5TY5A+wJaEiBAgAABAgQIECBAgAABAvUXMEMCgxawAT1occ8jQIAAAQIECBAgQIBAo8GAAAECBAgQIDASAjagR2KZTZIAAQIEZhdwhwABAgQIECBAgAABAgQIEOiXQHk2oPs1Q3EJECBAgAABAgQIECBAgACB8ggYCQECBAiMlIAN6JFabpMlQIAAAQIECGwRUCNAgAABAgQIECBAgEC/BWxA91tYfALzC2hBgAABAgQIECBAgAABAgQI1F/ADAmMpIAN6JFcdpMmQIAAAQIECBAgMMoC5k6AAAECBAgQIDAoARvQg5L2HAIECBCYLuAKAQIECBAgQIAAAQIECBAgUGuB1gZ0rWdocgQIECBAgAABAgQIECBAgEBLwIEAAQIECAxawAb0oMU9jwABAgQIECDQaDAgQIAAAQIECBAgQIDASAjYgB6JZTbJ2QXcIUCAAAECBAgQIECAAAECBOovYIYECAxLwAb0sOQ9lwABAgQIECBAgMAoCpgzAQIECBAgQIDASAnYgB6p5TZZAgQIbBFQI0CAAAECBAgQIECAAAECBOovMOwZ9ngD+uG163525703/uxOhQABAgQIECBAgAABAiMlkG+FHlqzdtjf4nl+iQUMjQABAgQIjKRALzegs/t8788f3Ljp8ZGUNGkCBAgQIECgIgKGSYAAgf4I5FuhVfc/ZA+6P7qiEiBAgAABAlUV6OUG9AMPrakqg3EPRcBDCRAgQIAAAQIECNRO4MGHvQi6dotqQgQILFRAfwIERlqglxvQ+Qf/kbY0eQIECBAgQIAAAQKlFjC4QQj4tmgQyp5BgAABAgQIVEeglxvQSxYvqs7EjZQAAQJDFPBoAgQIECBAoLYCvi2q7dKaGAECBAgQ6FxAjwj0cgN61513SkSFAAECBAgQIECAAAECIyuwy8rlIzv3Mk/c2AgQIECAAIFhCfRyA3rl8mV777GLf/Af1lp6LgECBAgQKL+AERIgQKDGAvlWaK/dd955JxvQNV5kUyNAgAABAgQ6FujlBnQenj3op++796FP31cpuYDhESBAgAABAgQIECDQW4F8K2T3Od8VKgQIlEvAaAgQINAHgWOOOeZls/+Xu1s/s8cb0FuHVidAgAABAgQIECBAYEzABwIECBAgQIAAAQK1EMj+8kte8pJnz/5f7qbNxFxtQE9QqBAgMBoCZkmAAAECBAgQIECAAAECBAjUX8AMyyJgA7osK2EcBAgQIECAAAECBAgQqKOAOREgQIAAAQK1ErjyyisffPDBOaaUu2kz0cAG9ASFCgECBAgQqLeA2REgQIAAAQIECBAgQIAAgR4IXHDBBRs3bpwxUK7n7ta3bEBvrTGYuqcQIECAAAECBAgQIECAAAEC9RcwQwIECNRVYPXq1VddddWMs8v13N36lg3orTXUCRAgQIAAAQIEaihgSgQIECBAgAABAgQI9Fbg8ssvX7Vq1ZSYuZLrUy7agJ4C4pQAgT4KCE2AAAECBAgQIECAAAECBAjUX8AMR0Pga1/72sat3ogj9VyZPnUb0NNNXCFAgAABAgQIECBAgEAtBEyCAAECBAgQINA3gUcfffRb3/rWRPjUc2XidKJiA3qCQoUAAQIECPRNQGACBAgQIECAAAECBAgQIFA7gWuvvfaCCy64+uqrc0x9xvmN2Ab0jAYuEiBAgAABAgQIECBAgAABArUSMBkCBAgQGJDALbfcctlll+U42/NsQM8m4zoBAgQIECBAgMDCBUQgQIAAAQIECBAgQGCkBWxAj/Tym/woCZgrAQIECBAgQIAAAQIECBAgUH8BMyRQNgEb0GVbEeMhQIAAAQIECBAgQKAOAuZAgAABAgQIECDQaDT+PwAAAP//K/tfAQAAAAZJREFUAwB/q11e09U2zwAAAABJRU5ErkJggg==\"></p>\r\n<p><strong>&nbsp;</strong></p>', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-01-12', '2026-01-31', NULL, 0, '2026-01-11 23:05:50', '2026-01-18 22:49:34');
INSERT INTO `issues` (`id`, `project_id`, `issue_type_id`, `status_id`, `priority_id`, `issue_key`, `issue_number`, `summary`, `description`, `reporter_id`, `assignee_id`, `parent_id`, `epic_id`, `sprint_id`, `story_points`, `original_estimate`, `remaining_estimate`, `time_spent`, `environment`, `due_date`, `start_date`, `end_date`, `resolved_at`, `sort_order`, `created_at`, `updated_at`) VALUES
(15, 4, 3, 3, 5, 'CWAYSMIS-2', 2, 'Assigning yhe issue to me', '<p>sadf</p>', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-12 03:16:42', '2026-01-19 04:24:40'),
(16, 1, 2, 2, 4, 'ECOM-8', 8, 'asfsd', '', 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-12 03:36:53', '2026-01-12 03:36:53'),
(17, 1, 2, 2, 4, 'ECOM-9', 9, 'asfsd', '', 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-12 03:37:16', '2026-01-12 03:37:16'),
(18, 4, 3, 2, 3, 'CWAYSMIS-3', 3, 'sadf', '<p>asdf</p>', 1, 1, NULL, NULL, 10, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-01-12 03:42:03', '2026-01-19 05:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `issue_attachments`
--

CREATE TABLE `issue_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL COMMENT 'Random secure filename',
  `original_name` varchar(255) NOT NULL COMMENT 'Original filename uploaded by user',
  `mime_type` varchar(100) NOT NULL DEFAULT 'application/octet-stream',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_path` varchar(500) NOT NULL COMMENT 'Relative path in storage directory',
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issue_attachments`
--

INSERT INTO `issue_attachments` (`id`, `issue_id`, `filename`, `original_name`, `mime_type`, `file_size`, `file_path`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 14, '66ff5da5e8ed53e104d360c04f66f889.pdf', 'Tohidealam_Nadaf_resume (2).pdf', 'application/pdf', 112082, 'uploads/attachments/2026/01/66ff5da5e8ed53e104d360c04f66f889.pdf', 1, '2026-01-11 23:05:50', '2026-01-12 04:35:50'),
(2, 15, 'd50fef54f797bd427a88847a9751ac15.sql', 'cways_mis (1).sql', 'application/octet-stream', 36125, 'uploads/attachments/2026/01/d50fef54f797bd427a88847a9751ac15.sql', 1, '2026-01-12 03:16:42', '2026-01-12 08:46:42'),
(3, 15, 'bae0378cf675a533b4cf0e1e9435ee56.sql', 'cways_mis (2).sql', 'application/octet-stream', 1459706, 'uploads/attachments/2026/01/bae0378cf675a533b4cf0e1e9435ee56.sql', 1, '2026-01-12 03:16:42', '2026-01-12 08:46:42'),
(4, 18, '74a9ef21f6797fd8d5795ba3d63cde39.sql', 'cways_prod v6.sql', 'application/octet-stream', 310729, 'uploads/attachments/2026/01/74a9ef21f6797fd8d5795ba3d63cde39.sql', 1, '2026-01-12 03:42:05', '2026-01-12 09:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `issue_components`
--

CREATE TABLE `issue_components` (
  `issue_id` int(10) UNSIGNED NOT NULL,
  `component_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_history`
--

CREATE TABLE `issue_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `field` varchar(50) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issue_history`
--

INSERT INTO `issue_history` (`id`, `issue_id`, `user_id`, `field`, `old_value`, `new_value`, `created_at`) VALUES
(1, 12, 1, 'status', 'To Do', 'In Progress', '2026-01-09 04:48:14'),
(2, 12, 1, 'status', 'In Progress', 'To Do', '2026-01-09 04:48:19'),
(3, 12, 1, 'status', 'To Do', 'In Progress', '2026-01-09 05:24:47'),
(4, 14, 1, 'attachment', NULL, '1 files attached', '2026-01-11 23:05:50'),
(5, 14, 1, 'status', 'To Do', 'In Progress', '2026-01-12 04:35:55'),
(6, 14, 1, 'status', 'In Progress', 'In Review', '2026-01-12 04:53:23'),
(7, 14, 1, 'status', 'In Review', 'In Progress', '2026-01-12 04:53:24'),
(8, 14, 1, 'status', 'In Progress', 'To Do', '2026-01-12 06:03:12'),
(9, 14, 1, 'status', 'To Do', 'In Progress', '2026-01-12 06:03:15'),
(10, 14, 1, 'status', 'In Progress', 'To Do', '2026-01-12 06:40:03'),
(11, 3, 1, 'status', 'In Progress', 'In Review', '2026-01-12 07:14:59'),
(12, 3, 1, 'status', 'In Review', 'Testing', '2026-01-12 07:15:08'),
(13, 2, 1, 'status', 'In Progress', 'In Review', '2026-01-12 07:16:46'),
(14, 2, 1, 'status', 'In Review', 'In Review', '2026-01-12 07:16:59'),
(15, 1, 1, 'status', 'To Do', 'In Progress', '2026-01-12 07:18:16'),
(16, 1, 1, 'status', 'In Progress', 'To Do', '2026-01-12 07:18:22'),
(17, 6, 1, 'status', 'To Do', 'In Progress', '2026-01-12 07:19:18'),
(18, 1, 1, 'status', 'To Do', 'In Progress', '2026-01-12 07:19:21'),
(19, 6, 1, 'status', 'In Progress', 'To Do', '2026-01-12 07:21:01'),
(20, 3, 1, 'status', 'Testing', 'Done', '2026-01-12 07:21:11'),
(21, 3, 1, 'status', 'Done', 'Testing', '2026-01-12 07:21:16'),
(22, 15, 1, 'attachment', NULL, '2 files attached', '2026-01-12 03:16:42'),
(23, 14, 1, 'status', 'To Do', 'In Progress', '2026-01-12 08:46:51'),
(24, 14, 1, 'status', 'In Progress', 'In Review', '2026-01-12 08:50:08'),
(25, 18, 1, 'attachment', NULL, '1 files attached', '2026-01-12 03:42:05'),
(26, 15, 1, 'status', 'To Do', 'In Progress', '2026-01-19 04:24:40'),
(27, 18, 1, 'sprint', 'Backlog', 'creating sprint', '2026-01-19 05:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `issue_labels`
--

CREATE TABLE `issue_labels` (
  `issue_id` int(10) UNSIGNED NOT NULL,
  `label_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issue_labels`
--

INSERT INTO `issue_labels` (`issue_id`, `label_id`) VALUES
(1, 2),
(1, 4),
(2, 2),
(3, 4),
(4, 3),
(5, 2),
(6, 2),
(7, 4),
(8, 2),
(9, 4),
(10, 3),
(11, 1),
(12, 5),
(13, 1);

-- --------------------------------------------------------

--
-- Table structure for table `issue_links`
--

CREATE TABLE `issue_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `source_issue_id` int(10) UNSIGNED NOT NULL,
  `target_issue_id` int(10) UNSIGNED NOT NULL,
  `link_type_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_link_types`
--

CREATE TABLE `issue_link_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `outward` varchar(50) NOT NULL,
  `inward` varchar(50) NOT NULL,
  `outward_description` varchar(255) DEFAULT NULL,
  `inward_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_priorities`
--

CREATE TABLE `issue_priorities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'medium',
  `color` varchar(7) DEFAULT '#FFAB00',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issue_priorities`
--

INSERT INTO `issue_priorities` (`id`, `name`, `description`, `icon`, `color`, `sort_order`, `is_default`, `created_at`) VALUES
(1, 'Highest', 'Must be done immediately', 'highest', '#AE2A19', 1, 0, '2026-01-09 04:39:59'),
(2, 'High', 'Should be done soon', 'high', '#F15638', 2, 0, '2026-01-09 04:39:59'),
(3, 'Medium', 'Normal priority', 'medium', '#FFAB00', 3, 1, '2026-01-09 04:39:59'),
(4, 'Low', 'Can be deferred', 'low', '#936B00', 4, 0, '2026-01-09 04:39:59'),
(5, 'Lowest', 'Nice to have', 'lowest', '#626F86', 5, 0, '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `issue_time_logs`
--

CREATE TABLE `issue_time_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('running','paused','stopped','manual') NOT NULL DEFAULT 'manual',
  `work_date` date NOT NULL DEFAULT curdate(),
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `resumed_at` datetime DEFAULT NULL,
  `paused_at` datetime DEFAULT NULL,
  `duration_seconds` int(10) UNSIGNED DEFAULT 0,
  `paused_seconds` int(10) UNSIGNED DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_billable` tinyint(1) DEFAULT 1,
  `user_rate_type` enum('hourly','minutely','secondly') DEFAULT 'hourly',
  `user_rate_amount` decimal(10,2) DEFAULT 0.00,
  `total_cost` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_types`
--

CREATE TABLE `issue_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'task',
  `color` varchar(7) DEFAULT '#4A90D9',
  `is_subtask` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issue_types`
--

INSERT INTO `issue_types` (`id`, `name`, `description`, `icon`, `color`, `is_subtask`, `is_default`, `sort_order`, `created_at`) VALUES
(1, 'Epic', 'Epic-level initiative', 'epic', '#4A3F93', 0, 0, 1, '2026-01-09 04:39:59'),
(2, 'Story', 'User story or feature', 'story', '#0052CC', 0, 1, 2, '2026-01-09 04:39:59'),
(3, 'Task', 'General task or activity', 'task', '#00ff11', 0, 0, 3, '2026-01-09 04:39:59'),
(4, 'Bug', 'Bug or defect', 'bug', '#AE2A19', 0, 0, 4, '2026-01-09 04:39:59'),
(5, 'Subtask', 'Subtask of an issue', 'subtask', '#626F86', 1, 0, 5, '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `issue_versions`
--

CREATE TABLE `issue_versions` (
  `issue_id` int(10) UNSIGNED NOT NULL,
  `version_id` int(10) UNSIGNED NOT NULL,
  `type` enum('affect','fix') NOT NULL DEFAULT 'fix'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_votes`
--

CREATE TABLE `issue_votes` (
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_watchers`
--

CREATE TABLE `issue_watchers` (
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issue_watchers`
--

INSERT INTO `issue_watchers` (`issue_id`, `user_id`, `created_at`) VALUES
(15, 1, '2026-01-12 08:47:14');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'general',
  `category` varchar(50) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) DEFAULT '#42526E',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `project_id`, `name`, `color`, `created_at`) VALUES
(1, 1, 'urgent', '#AE2A19', '2026-01-09 04:39:59'),
(2, 1, 'feature', '#0052CC', '2026-01-09 04:39:59'),
(3, 1, 'bug', '#E5534B', '2026-01-09 04:39:59'),
(4, 1, 'documentation', '#5243AA', '2026-01-09 04:39:59'),
(5, 1, 'technical-debt', '#997799', '2026-01-09 04:39:59'),
(6, 2, 'feature', '#0052CC', '2026-01-09 04:39:59'),
(7, 2, 'bug', '#E5534B', '2026-01-09 04:39:59'),
(8, 3, 'infrastructure', '#5243AA', '2026-01-09 04:39:59'),
(9, 3, 'security', '#AE2A19', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `mentions`
--

CREATE TABLE `mentions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `comment_id` int(10) UNSIGNED DEFAULT NULL,
  `issue_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('issue_created','issue_assigned','issue_commented','issue_status_changed','issue_mentioned','issue_watched','project_created','project_member_added','comment_reply','custom') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `actor_user_id` int(10) UNSIGNED DEFAULT NULL,
  `related_issue_id` int(10) UNSIGNED DEFAULT NULL,
  `related_project_id` int(10) UNSIGNED DEFAULT NULL,
  `priority` enum('high','normal','low') DEFAULT 'normal',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `dispatch_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications_archive`
--

CREATE TABLE `notifications_archive` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('issue_created','issue_assigned','issue_commented','issue_status_changed','issue_mentioned','issue_watched','project_created','project_member_added','comment_reply','custom') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `actor_user_id` int(10) UNSIGNED DEFAULT NULL,
  `related_issue_id` int(10) UNSIGNED DEFAULT NULL,
  `related_project_id` int(10) UNSIGNED DEFAULT NULL,
  `priority` enum('high','normal','low') DEFAULT 'normal',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `dispatch_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_deliveries`
--

CREATE TABLE `notification_deliveries` (
  `id` int(10) UNSIGNED NOT NULL,
  `notification_id` int(10) UNSIGNED NOT NULL,
  `channel` enum('in_app','email','push') NOT NULL,
  `status` enum('pending','sent','failed','bounced') DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `retry_count` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_dispatch_log`
--

CREATE TABLE `notification_dispatch_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dispatch_id` varchar(255) NOT NULL,
  `dispatch_type` enum('comment_added','status_changed','other') NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `comment_id` int(10) UNSIGNED DEFAULT NULL,
  `actor_user_id` int(10) UNSIGNED NOT NULL,
  `recipients_count` int(10) UNSIGNED DEFAULT 0,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_preferences`
--

CREATE TABLE `notification_preferences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `event_type` enum('issue_created','issue_assigned','issue_commented','issue_status_changed','issue_mentioned','issue_watched','project_created','project_member_added','comment_reply','all') NOT NULL,
  `in_app` tinyint(1) DEFAULT 1,
  `email` tinyint(1) DEFAULT 1,
  `push` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_preferences`
--

INSERT INTO `notification_preferences` (`id`, `user_id`, `event_type`, `in_app`, `email`, `push`, `created_at`, `updated_at`) VALUES
(1, 1, 'issue_created', 1, 0, 0, '2026-01-12 07:33:51', '2026-01-12 07:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at?` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `category`, `created_at`) VALUES
(1, 'View Issues', 'view_issues', 'View project issues', 'issues', '2026-01-09 04:39:59'),
(2, 'Create Issue', 'create_issue', 'Create new issues', 'issues', '2026-01-09 04:39:59'),
(3, 'Edit Issue', 'edit_issue', 'Edit existing issues', 'issues', '2026-01-09 04:39:59'),
(4, 'Delete Issue', 'delete_issue', 'Delete issues', 'issues', '2026-01-09 04:39:59'),
(5, 'View Projects', 'view_projects', 'View projects', 'projects', '2026-01-09 04:39:59'),
(6, 'Create Project', 'create_project', 'Create new projects', 'projects', '2026-01-09 04:39:59'),
(7, 'Edit Project', 'edit_project', 'Edit project settings', 'projects', '2026-01-09 04:39:59'),
(8, 'Manage Board', 'manage_board', 'Manage project boards', 'projects', '2026-01-09 04:39:59'),
(9, 'Manage Sprints', 'manage_sprints', 'Create and manage sprints', 'sprints', '2026-01-09 04:39:59'),
(10, 'View Reports', 'view_reports', 'View project reports', 'reports', '2026-01-09 04:39:59'),
(11, 'Manage Users', 'manage_users', 'Manage user accounts', 'admin', '2026-01-09 04:39:59'),
(12, 'Manage Roles', 'manage_roles', 'Manage roles and permissions', 'admin', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `abilities` longtext DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at?` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `lead_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `default_assignee` enum('project_lead','unassigned') DEFAULT 'unassigned',
  `avatar` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `issue_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `budget` decimal(12,2) DEFAULT 0.00 COMMENT 'Project budget in default currency',
  `budget_currency` varchar(3) DEFAULT 'USD' COMMENT 'Budget currency code',
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `key`, `name`, `description`, `lead_id`, `category_id`, `default_assignee`, `avatar`, `is_archived`, `issue_count`, `budget`, `budget_currency`, `is_private`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'ECOM', 'E-Commerce Platform', 'Main e-commerce platform and APIs', 2, 1, 'project_lead', NULL, 0, 7, 0.00, 'USD', 0, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(2, 'MOBILE', 'Mobile Apps', 'iOS and Android mobile applications', 3, 5, 'project_lead', NULL, 0, 3, 0.00, 'USD', 0, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 'INFRA', 'Infrastructure', 'DevOps, deployment, and cloud infrastructure', 4, 3, 'unassigned', NULL, 0, 3, 0.00, 'USD', 0, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 'CWAYSMIS', 'CWays MIS', 'CWays MIS', 1, 3, 'project_lead', '/uploads/avatars/project_4_696479b71a577.jpg', 0, 0, 0.00, 'USD', 0, 1, '2026-01-12 04:19:42', '2026-01-12 04:33:59');

-- --------------------------------------------------------

--
-- Table structure for table `project_budgets`
--

CREATE TABLE `project_budgets` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `total_budget` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `budget_type` enum('fixed','monthly','quarterly','yearly') NOT NULL DEFAULT 'fixed',
  `budget_period` enum('one_time','recurring') NOT NULL DEFAULT 'one_time',
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `alert_threshold` decimal(5,2) NOT NULL DEFAULT 80.00 COMMENT 'Percentage used to trigger alert',
  `remaining_budget` decimal(15,2) GENERATED ALWAYS AS (`total_budget` - `total_cost`) STORED,
  `budget_used_percentage` decimal(5,2) GENERATED ALWAYS AS (if(`total_budget` > 0,`total_cost` / `total_budget` * 100,0)) STORED,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_categories`
--

CREATE TABLE `project_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_categories`
--

INSERT INTO `project_categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Backend Systems', 'Core backend and API development', '2026-01-09 04:39:59'),
(2, 'Frontend', 'Web and mobile front-end projects', '2026-01-09 04:39:59'),
(3, 'Infrastructure', 'DevOps and infrastructure projects', '2026-01-09 04:39:59'),
(4, 'Data & Analytics', 'Data engineering and analytics', '2026-01-09 04:39:59'),
(5, 'Mobile Apps', 'Native mobile applications', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size` int(10) UNSIGNED NOT NULL,
  `path` varchar(500) NOT NULL,
  `category` enum('requirement','design','technical','user_guide','training','report','other','specification') DEFAULT 'other',
  `version` varchar(20) DEFAULT '1.0.0',
  `is_public` tinyint(1) DEFAULT 0,
  `download_count` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_documents`
--

INSERT INTO `project_documents` (`id`, `project_id`, `user_id`, `title`, `description`, `filename`, `original_filename`, `mime_type`, `size`, `path`, `category`, `version`, `is_public`, `download_count`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 'The', 'sdf', 'doc_6964ab52899ed6.42454025.pptx', 'Presentation (1).pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 70742, 'uploads/documents/doc_6964ab52899ed6.42454025.pptx', 'requirement', '1.0', 1, 0, '2026-01-12 08:05:38', '2026-01-12 08:05:38'),
(2, 4, 1, 'Test Manual V3', 'Test Desc V3', 'test_v3.txt', 'test_orig_v3.txt', 'text/plain', 1024, 'test/path/v3', 'other', '1.0', 1, 0, '2026-01-12 08:10:39', '2026-01-12 08:10:39'),
(3, 4, 1, 'test1', 'Test multiple upload', 'doc_6964aee432df72.43063330.txt', 'test1.txt', 'text/plain', 15, 'uploads/documents/doc_6964aee432df72.43063330.txt', 'technical', '1.0', 1, 0, '2026-01-12 08:20:52', '2026-01-12 08:20:52'),
(4, 4, 1, 'test2', 'Test multiple upload', 'doc_6964aee440bdf6.34619341.txt', 'test2.txt', 'text/plain', 15, 'uploads/documents/doc_6964aee440bdf6.34619341.txt', 'technical', '1.0', 1, 0, '2026-01-12 08:20:52', '2026-01-12 08:20:52'),
(5, 4, 1, 'test3', 'Test multiple upload', 'doc_6964aee4445349.89358435.txt', 'test3.txt', 'text/plain', 15, 'uploads/documents/doc_6964aee4445349.89358435.txt', 'technical', '1.0', 1, 0, '2026-01-12 08:20:52', '2026-01-12 08:20:52'),
(6, 4, 1, 'test1', '', 'doc_6964b0bc5ab470.78387638.txt', 'test1.txt', 'text/plain', 14, 'uploads/documents/doc_6964b0bc5ab470.78387638.txt', 'technical', '1.0', 1, 0, '2026-01-12 08:28:44', '2026-01-12 08:28:44'),
(7, 4, 1, 'test2', '', 'doc_6964b0bc74cf99.59899784.txt', 'test2.txt', 'text/plain', 14, 'uploads/documents/doc_6964b0bc74cf99.59899784.txt', 'technical', '1.0', 1, 0, '2026-01-12 08:28:44', '2026-01-12 08:28:44'),
(8, 4, 1, 'test3', '', 'doc_6964b1df51ccc4.49701959.txt', 'test3.txt', 'text/plain', 14, 'uploads/documents/doc_6964b1df51ccc4.49701959.txt', 'requirement', '1.0', 1, 0, '2026-01-12 08:33:35', '2026-01-12 08:33:35'),
(9, 4, 1, 'Single Upload Test', '', 'doc_6964b39b92acc8.48517972.txt', 'test1.txt', 'text/plain', 14, 'uploads/documents/doc_6964b39b92acc8.48517972.txt', 'requirement', '1.0', 1, 0, '2026-01-12 08:40:59', '2026-01-12 08:40:59'),
(10, 4, 1, 'Presentation (2)', 'The', 'doc_6964b469678c49.65804890.pptx', 'Presentation (2).pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 70742, 'uploads/documents/doc_6964b469678c49.65804890.pptx', 'requirement', '1.0', 1, 0, '2026-01-12 08:44:25', '2026-01-12 08:44:25'),
(11, 4, 1, 'jiira_clonee_system', 'The', 'doc_6964b46971cac1.29663584.txt', 'jiira_clonee_system.txt', 'text/plain', 1456350, 'uploads/documents/doc_6964b46971cac1.29663584.txt', 'requirement', '1.0', 1, 0, '2026-01-12 08:44:25', '2026-01-12 08:44:25'),
(12, 4, 1, 'Presentation (1)', 'The', 'doc_6964b469735341.90294433.pptx', 'Presentation (1).pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 70742, 'uploads/documents/doc_6964b469735341.90294433.pptx', 'requirement', '1.0', 1, 0, '2026-01-12 08:44:25', '2026-01-12 08:44:25'),
(13, 4, 1, 'Presentation', 'The', 'doc_6964b469743072.04529908.pptx', 'Presentation.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 70742, 'uploads/documents/doc_6964b469743072.04529908.pptx', 'requirement', '1.0', 1, 0, '2026-01-12 08:44:25', '2026-01-12 08:44:25'),
(14, 4, 1, 'Internship_Presentation_Tohidealam_Nadaf (1)', 'The', 'doc_6964b469751921.58609775.pptx', 'Internship_Presentation_Tohidealam_Nadaf (1).pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 42085, 'uploads/documents/doc_6964b469751921.58609775.pptx', 'requirement', '1.0', 1, 0, '2026-01-12 08:44:25', '2026-01-12 08:44:25'),
(15, 4, 1, 'Invoice-4IIUN1LG-0001', 'The', 'doc_6964b46975f395.60449359.pdf', 'Invoice-4IIUN1LG-0001.pdf', 'application/pdf', 48778, 'uploads/documents/doc_6964b46975f395.60449359.pdf', 'requirement', '1.0', 1, 0, '2026-01-12 08:44:25', '2026-01-12 08:44:25');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `project_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`project_id`, `user_id`, `role_id`, `created_at`) VALUES
(1, 1, 1, '2026-01-09 04:39:59'),
(1, 2, 2, '2026-01-09 04:39:59'),
(1, 3, 2, '2026-01-09 04:39:59'),
(1, 4, 3, '2026-01-09 04:39:59'),
(1, 5, 4, '2026-01-09 04:39:59'),
(2, 1, 1, '2026-01-09 04:39:59'),
(2, 3, 2, '2026-01-09 04:39:59'),
(2, 5, 4, '2026-01-09 04:39:59'),
(2, 6, 2, '2026-01-09 04:39:59'),
(3, 1, 1, '2026-01-09 04:39:59'),
(3, 4, 3, '2026-01-09 04:39:59'),
(3, 6, 2, '2026-01-09 04:39:59'),
(4, 1, 1, '2026-01-12 04:34:20'),
(4, 3, 2, '2026-01-12 04:34:27'),
(4, 4, 2, '2026-01-12 04:34:14'),
(4, 5, 2, '2026-01-12 04:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_workflows`
--

CREATE TABLE `project_workflows` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `issue_type_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_workflows_backup_placeholder`
--

CREATE TABLE `project_workflows_backup_placeholder` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `push_device_tokens`
--

CREATE TABLE `push_device_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `platform` enum('ios','android','web') NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roadmap_dependencies`
--

CREATE TABLE `roadmap_dependencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `depends_on_item_id` int(10) UNSIGNED NOT NULL,
  `dependency_type` enum('blocks','depends_on','relates_to') NOT NULL DEFAULT 'depends_on',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roadmap_items`
--

CREATE TABLE `roadmap_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('epic','feature','milestone') NOT NULL DEFAULT 'feature',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('planned','in_progress','on_track','at_risk','delayed','completed') NOT NULL DEFAULT 'planned',
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `owner_id` int(10) UNSIGNED DEFAULT NULL,
  `estimated_hours` decimal(10,2) DEFAULT 0.00,
  `actual_hours` decimal(10,2) DEFAULT 0.00,
  `progress_percentage` int(10) UNSIGNED DEFAULT 0,
  `color` varchar(7) DEFAULT '#8b1956',
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roadmap_item_issues`
--

CREATE TABLE `roadmap_item_issues` (
  `id` int(10) UNSIGNED NOT NULL,
  `roadmap_item_id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roadmap_item_sprints`
--

CREATE TABLE `roadmap_item_sprints` (
  `id` int(10) UNSIGNED NOT NULL,
  `roadmap_item_id` int(10) UNSIGNED NOT NULL,
  `sprint_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'administrator', 'Full system access', 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(2, 'Developer', 'developer', 'Develop and test features', 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 'Project Manager', 'project_manager', 'Manage projects and teams', 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 'QA Tester', 'qa_tester', 'Test and report issues', 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(5, 'Viewer', 'viewer', 'View-only access', 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(6, 'Product Owner', 'product_owner', 'Define requirements', 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 7),
(2, 8),
(2, 10),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(4, 1),
(4, 2),
(4, 3),
(4, 5),
(4, 7),
(4, 8),
(4, 10),
(5, 1),
(5, 5),
(5, 10),
(6, 1),
(6, 2),
(6, 3),
(6, 5),
(6, 7),
(6, 8),
(6, 9),
(6, 10);

-- --------------------------------------------------------

--
-- Table structure for table `saved_filters`
--

CREATE TABLE `saved_filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `jql` text NOT NULL,
  `is_favorite` tinyint(1) NOT NULL DEFAULT 0,
  `share_type` enum('private','project','global') NOT NULL DEFAULT 'private',
  `shared_with` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shared_with`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sprints`
--

CREATE TABLE `sprints` (
  `id` int(10) UNSIGNED NOT NULL,
  `board_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `goal` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('future','active','completed') NOT NULL DEFAULT 'future',
  `velocity` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sprints`
--

INSERT INTO `sprints` (`id`, `board_id`, `name`, `goal`, `start_date`, `end_date`, `started_at`, `completed_at`, `status`, `velocity`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sprint 1', 'Implement core e-commerce features', '2025-12-09', '2025-12-20', NULL, NULL, 'active', NULL, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(2, 1, 'Sprint 2', 'Payment integration and testing', '2025-12-21', '2026-01-03', NULL, NULL, 'future', NULL, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 1, 'Sprint 3', 'Performance optimization', '2026-01-04', '2026-01-17', NULL, NULL, 'future', NULL, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 3, 'Sprint 1', 'Mobile app core features', '2025-12-09', '2025-12-20', NULL, NULL, 'active', NULL, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(5, 3, 'Sprint 2', 'Testing and refinement', '2025-12-21', '2026-01-03', NULL, NULL, 'future', NULL, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(6, 1, 'Browser Verified Sprint', NULL, NULL, NULL, NULL, NULL, 'future', NULL, '2026-01-09 05:17:23', '2026-01-09 05:17:23'),
(7, 1, 'Backlog Fix Verified Sprint', NULL, NULL, NULL, NULL, NULL, 'future', NULL, '2026-01-09 05:29:34', '2026-01-09 05:29:34'),
(8, 1, 'Backlog Final Verify 2', NULL, NULL, NULL, NULL, NULL, 'future', NULL, '2026-01-09 05:40:42', '2026-01-09 05:40:42'),
(9, 1, 'Redesign Fix Verified', NULL, NULL, NULL, NULL, NULL, 'future', NULL, '2026-01-12 03:50:00', '2026-01-12 03:50:00'),
(10, 6, 'creating sprint', 'yes i want to accomplish something in my life.', '2026-01-12', '2026-01-19', NULL, NULL, 'future', NULL, '2026-01-12 04:50:21', '2026-01-12 04:50:21');

-- --------------------------------------------------------

--
-- Table structure for table `sprint_issues`
--

CREATE TABLE `sprint_issues` (
  `sprint_id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sprint_issues`
--

INSERT INTO `sprint_issues` (`sprint_id`, `issue_id`, `sort_order`) VALUES
(10, 18, 0);

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('todo','in_progress','done') NOT NULL DEFAULT 'todo',
  `color` varchar(7) DEFAULT '#42526E',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `name`, `description`, `category`, `color`, `sort_order`, `created_at`) VALUES
(1, 'Open', 'New issue waiting to start', 'todo', '#1F77E8', 1, '2026-01-09 04:39:59'),
(2, 'To Do', 'Queued for development', 'todo', '#9C27B0', 2, '2026-01-09 04:39:59'),
(3, 'In Progress', 'Currently being worked on', 'in_progress', '#FF9800', 3, '2026-01-09 04:39:59'),
(4, 'In Review', 'Waiting for code review', 'in_progress', '#4CAF50', 4, '2026-01-09 04:39:59'),
(5, 'Testing', 'In QA testing phase', 'in_progress', '#00BCD4', 5, '2026-01-09 04:39:59'),
(6, 'Done', 'Completed and released', 'done', '#2E7D32', 6, '2026-01-09 04:39:59'),
(7, 'Closed', 'Issue closed/resolved', 'done', '#616161', 7, '2026-01-09 04:39:59'),
(8, 'Reopened', 'Issue reopened', 'todo', '#AB47BC', 8, '2026-01-12 07:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `time_tracking_reports`
--

CREATE TABLE `time_tracking_reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_type` enum('user_daily','user_weekly','project_daily','project_weekly','budget_status') NOT NULL,
  `report_date` date NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`report_data`)),
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `cache_valid_until` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_tracking_settings`
--

CREATE TABLE `time_tracking_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `data_type` enum('string','integer','float','boolean','json') DEFAULT 'string',
  `group` varchar(50) DEFAULT 'general',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `display_name` varchar(200) GENERATED ALWAYS AS (concat(`first_name`,' ',`last_name`)) STORED,
  `avatar` varchar(255) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT 'UTC',
  `locale` varchar(10) DEFAULT 'en',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `failed_login_attempts` int(10) UNSIGNED DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `unread_notifications_count` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `avatar`, `timezone`, `locale`, `is_active`, `is_admin`, `email_verified_at`, `last_login_at`, `last_login_ip`, `failed_login_attempts`, `locked_until`, `unread_notifications_count`, `created_at`, `updated_at`) VALUES
(1, 'admin@example.com', '$2y$10$Vkt2i3XFfUC45i.WClc/3Ow4CywAIHay1O9YTDwthARuB5bszrDU6', 'System', 'Administrator', '/uploads/avatars/avatar_1_1767933698.png', 'UTC', 'en', 1, 1, NULL, '2026-01-18 22:41:43', '::1', 0, NULL, 0, '2026-01-09 04:39:59', '2026-01-19 04:11:43'),
(2, 'john@example.com', '$2y$10$Vkt2i3XFfUC45i.WClc/3Ow4CywAIHay1O9YTDwthARuB5bszrDU6', 'John', 'Developer', NULL, 'UTC', 'en', 1, 0, NULL, NULL, NULL, 0, NULL, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 'jane@example.com', '$2y$10$Vkt2i3XFfUC45i.WClc/3Ow4CywAIHay1O9YTDwthARuB5bszrDU6', 'Jane', 'Doe', NULL, 'UTC', 'en', 1, 0, NULL, NULL, NULL, 0, NULL, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 'mike@example.com', '$2y$10$Vkt2i3XFfUC45i.WClc/3Ow4CywAIHay1O9YTDwthARuB5bszrDU6', 'Mike', 'Manager', NULL, 'UTC', 'en', 1, 0, NULL, NULL, NULL, 0, NULL, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(5, 'sarah@example.com', '$2y$10$Vkt2i3XFfUC45i.WClc/3Ow4CywAIHay1O9YTDwthARuB5bszrDU6', 'Sarah', 'Tester', NULL, 'UTC', 'en', 1, 0, NULL, NULL, NULL, 0, NULL, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(6, 'david@example.com', '$2y$10$Vkt2i3XFfUC45i.WClc/3Ow4CywAIHay1O9YTDwthARuB5bszrDU6', 'David', 'Coder', NULL, 'UTC', 'en', 1, 0, NULL, NULL, NULL, 0, NULL, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_rates`
--

CREATE TABLE `user_rates` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `rate_type` enum('hourly','minutely','secondly') NOT NULL DEFAULT 'hourly',
  `rate_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `is_active` tinyint(1) DEFAULT 1,
  `effective_from` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `last_activity`, `created_at`) VALUES
('1cs3q23s212rtotjpmrgs9kijd', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:17:28', '2026-01-12 04:17:28'),
('6bnr7rjr0i4mnpvrdd97c1573v', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 03:43:50', '2026-01-12 03:43:50'),
('6p7cjh1tmkkisg605n8fst9bo2', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 08:16:58', '2026-01-12 08:16:58'),
('8a8arku8eohre0mu4a2mjtjmku', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 03:48:57', '2026-01-12 03:48:57'),
('91hfb06meobvulpvi492jvlhpv', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 04:03:58', '2026-01-12 04:03:58'),
('9heetfrssqeee74qnla21hp88v', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 06:07:52', '2026-01-09 06:07:52'),
('g9n991d00ac7o8fue0ffjspfb4', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 07:07:45', '2026-01-12 07:07:45'),
('gnlmmej69f2i1okkjet7u4j7g0', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 09:28:34', '2026-01-12 09:28:34'),
('gtjdv9m0p0tm66pgrk1ubt39mf', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 05:10:14', '2026-01-09 05:10:14'),
('m1hk620hr9t818ee782o065guf', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-09 04:42:22', '2026-01-09 04:42:22'),
('m82a6j62ibior0cnb9i74nv18d', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 05:56:38', '2026-01-12 05:56:38'),
('p7fsqigv8hc5tm4q9m2hdpa92u', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 09:22:43', '2026-01-12 09:22:43'),
('pvj8i80s84ccqa1l21t3pjbaht', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 04:11:43', '2026-01-19 04:11:43'),
('qka65oi179v2hbj7f6j8s1gmlh', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:32:00', '2026-01-12 06:32:00'),
('rg9vn5t1tcsapv6uopga6e2k1v', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 06:37:25', '2026-01-12 06:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `language` varchar(10) DEFAULT 'en',
  `timezone` varchar(50) DEFAULT 'UTC',
  `date_format` varchar(20) DEFAULT 'MM/DD/YYYY',
  `items_per_page` int(10) UNSIGNED DEFAULT 25,
  `compact_view` tinyint(1) DEFAULT 0,
  `auto_refresh` tinyint(1) DEFAULT 0,
  `show_profile` tinyint(1) DEFAULT 1,
  `show_activity` tinyint(1) DEFAULT 1,
  `show_email` tinyint(1) DEFAULT 0,
  `high_contrast` tinyint(1) DEFAULT 0,
  `reduce_motion` tinyint(1) DEFAULT 0,
  `large_text` tinyint(1) DEFAULT 0,
  `annual_package` decimal(15,2) DEFAULT NULL,
  `rate_currency` varchar(3) DEFAULT 'USD',
  `hourly_rate` decimal(10,4) GENERATED ALWAYS AS (if(`annual_package` is null,NULL,`annual_package` / 2210)) STORED,
  `minute_rate` decimal(12,6) GENERATED ALWAYS AS (if(`annual_package` is null,NULL,`annual_package` / 2210 / 60)) STORED,
  `second_rate` decimal(14,8) GENERATED ALWAYS AS (if(`annual_package` is null,NULL,`annual_package` / 2210 / 60 / 60)) STORED,
  `daily_rate` decimal(10,2) GENERATED ALWAYS AS (if(`annual_package` is null,NULL,`annual_package` / 2210 * 8.5)) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `versions`
--

CREATE TABLE `versions` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `versions`
--

INSERT INTO `versions` (`id`, `project_id`, `name`, `description`, `start_date`, `release_date`, `released_at`, `is_archived`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, '1.0', 'Initial release', '2025-11-01', '2025-12-15', NULL, 0, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(2, 1, '1.1', 'Bug fixes and improvements', '2025-12-16', NULL, NULL, 0, 2, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 1, '2.0', 'Major feature release', '2026-01-01', NULL, NULL, 0, 3, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(4, 2, '1.0', 'iOS and Android launch', '2025-12-01', NULL, NULL, 0, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(5, 3, '1.0', 'Initial infrastructure setup', '2025-11-01', '2025-12-01', NULL, 1, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`id`, `name`, `description`, `is_active`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Standard Workflow', 'Default workflow for most projects', 1, 1, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(2, 'Agile Workflow', 'Workflow optimized for agile teams', 1, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59'),
(3, 'Kanban Workflow', 'Simplified workflow for Kanban boards', 1, 0, '2026-01-09 04:39:59', '2026-01-09 04:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_statuses`
--

CREATE TABLE `workflow_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `status_id` int(10) UNSIGNED NOT NULL,
  `is_initial` tinyint(1) NOT NULL DEFAULT 0,
  `x_position` int(11) DEFAULT 0,
  `y_position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_statuses`
--

INSERT INTO `workflow_statuses` (`id`, `workflow_id`, `status_id`, `is_initial`, `x_position`, `y_position`) VALUES
(1, 1, 1, 1, 0, 0),
(2, 1, 2, 0, 100, 0),
(3, 1, 3, 0, 200, 0),
(4, 1, 4, 0, 300, 0),
(5, 1, 5, 0, 400, 0),
(6, 1, 6, 0, 500, 0),
(7, 1, 7, 0, 600, 0);

-- --------------------------------------------------------

--
-- Table structure for table `workflow_transitions`
--

CREATE TABLE `workflow_transitions` (
  `id` int(10) UNSIGNED NOT NULL,
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `from_status_id` int(10) UNSIGNED DEFAULT NULL,
  `to_status_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `worklogs`
--

CREATE TABLE `worklogs` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `time_spent` int(11) NOT NULL COMMENT 'Time spent in seconds',
  `started_at` datetime NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_timers`
--
ALTER TABLE `active_timers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `active_timers_user_unique` (`user_id`),
  ADD KEY `active_timers_issue_id_fk` (`issue_id`),
  ADD KEY `active_timers_time_log_id_fk` (`issue_time_log_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_issue_id_idx` (`issue_id`),
  ADD KEY `attachments_user_id_fk` (`user_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_idx` (`user_id`),
  ADD KEY `audit_logs_entity_idx` (`entity_type`,`entity_id`),
  ADD KEY `audit_logs_created_at_idx` (`created_at`),
  ADD KEY `audit_logs_action_idx` (`action`);

--
-- Indexes for table `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boards_project_id_idx` (`project_id`),
  ADD KEY `boards_owner_id_idx` (`owner_id`);

--
-- Indexes for table `board_columns`
--
ALTER TABLE `board_columns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `board_columns_board_id_idx` (`board_id`);

--
-- Indexes for table `budget_alerts`
--
ALTER TABLE `budget_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_alerts_project_budget_id_idx` (`project_budget_id`),
  ADD KEY `budget_alerts_project_id_idx` (`project_id`),
  ADD KEY `budget_alerts_acknowledged_by_fk` (`acknowledged_by`);

--
-- Indexes for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendar_events_event_type_idx` (`event_type`),
  ADD KEY `calendar_events_project_id_idx` (`project_id`),
  ADD KEY `calendar_events_priority_id_idx` (`priority_id`),
  ADD KEY `calendar_events_created_by_idx` (`created_by`),
  ADD KEY `calendar_events_start_date_idx` (`start_date`),
  ADD KEY `calendar_events_end_date_idx` (`end_date`),
  ADD KEY `calendar_events_created_at_idx` (`created_at`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_issue_id_idx` (`issue_id`),
  ADD KEY `comments_user_id_idx` (`user_id`),
  ADD KEY `comments_created_at_idx` (`created_at`),
  ADD KEY `comments_parent_id_fk` (`parent_id`);

--
-- Indexes for table `comment_history`
--
ALTER TABLE `comment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comment_id` (`comment_id`),
  ADD KEY `idx_edited_at` (`edited_at`),
  ADD KEY `idx_edited_by` (`edited_by`);

--
-- Indexes for table `components`
--
ALTER TABLE `components`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `components_project_name_unique` (`project_id`,`name`),
  ADD KEY `components_lead_id_idx` (`lead_id`),
  ADD KEY `components_default_assignee_id_fk` (`default_assignee_id`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `custom_fields_name_unique` (`name`);

--
-- Indexes for table `custom_field_contexts`
--
ALTER TABLE `custom_field_contexts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_field_contexts_field_id_idx` (`field_id`),
  ADD KEY `custom_field_contexts_project_id_idx` (`project_id`),
  ADD KEY `custom_field_contexts_issue_type_id_idx` (`issue_type_id`);

--
-- Indexes for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `custom_field_values_unique` (`field_id`,`issue_id`),
  ADD KEY `custom_field_values_issue_id_idx` (`issue_id`);

--
-- Indexes for table `dashboards`
--
ALTER TABLE `dashboards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dashboards_user_id_idx` (`user_id`);

--
-- Indexes for table `dashboard_gadgets`
--
ALTER TABLE `dashboard_gadgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dashboard_gadgets_dashboard_id_idx` (`dashboard_id`);

--
-- Indexes for table `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_queue_sent_at_idx` (`sent_at`),
  ADD KEY `email_queue_created_at_idx` (`created_at`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `groups_name_unique` (`name`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`group_id`,`user_id`),
  ADD KEY `group_members_user_id_idx` (`user_id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issues_key_unique` (`issue_key`),
  ADD KEY `issues_project_id_idx` (`project_id`),
  ADD KEY `issues_issue_type_id_idx` (`issue_type_id`),
  ADD KEY `issues_status_id_idx` (`status_id`),
  ADD KEY `issues_priority_id_idx` (`priority_id`),
  ADD KEY `issues_reporter_id_idx` (`reporter_id`),
  ADD KEY `issues_assignee_id_idx` (`assignee_id`),
  ADD KEY `issues_parent_id_idx` (`parent_id`),
  ADD KEY `issues_epic_id_idx` (`epic_id`),
  ADD KEY `issues_sprint_id_idx` (`sprint_id`),
  ADD KEY `issues_created_at_idx` (`created_at`),
  ADD KEY `issues_due_date_idx` (`due_date`),
  ADD KEY `idx_issues_start_date` (`start_date`),
  ADD KEY `idx_issues_end_date` (`end_date`);
ALTER TABLE `issues` ADD FULLTEXT KEY `issues_fulltext` (`summary`,`description`);

--
-- Indexes for table `issue_attachments`
--
ALTER TABLE `issue_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_attachments_issue_id_idx` (`issue_id`),
  ADD KEY `issue_attachments_uploaded_by_idx` (`uploaded_by`),
  ADD KEY `issue_attachments_created_at_idx` (`created_at`);

--
-- Indexes for table `issue_components`
--
ALTER TABLE `issue_components`
  ADD PRIMARY KEY (`issue_id`,`component_id`),
  ADD KEY `issue_components_component_id_fk` (`component_id`);

--
-- Indexes for table `issue_history`
--
ALTER TABLE `issue_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_history_issue_id_idx` (`issue_id`),
  ADD KEY `issue_history_created_at_idx` (`created_at`),
  ADD KEY `issue_history_user_id_fk` (`user_id`);

--
-- Indexes for table `issue_labels`
--
ALTER TABLE `issue_labels`
  ADD PRIMARY KEY (`issue_id`,`label_id`),
  ADD KEY `issue_labels_label_id_fk` (`label_id`);

--
-- Indexes for table `issue_links`
--
ALTER TABLE `issue_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issue_links_unique` (`source_issue_id`,`target_issue_id`,`link_type_id`),
  ADD KEY `issue_links_target_idx` (`target_issue_id`),
  ADD KEY `issue_links_created_by_idx` (`created_by`),
  ADD KEY `issue_links_type_fk` (`link_type_id`);

--
-- Indexes for table `issue_link_types`
--
ALTER TABLE `issue_link_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issue_link_types_name_unique` (`name`);

--
-- Indexes for table `issue_priorities`
--
ALTER TABLE `issue_priorities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issue_priorities_name_unique` (`name`);

--
-- Indexes for table `issue_time_logs`
--
ALTER TABLE `issue_time_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_time_logs_issue_id_idx` (`issue_id`),
  ADD KEY `issue_time_logs_user_id_idx` (`user_id`),
  ADD KEY `issue_time_logs_project_id_idx` (`project_id`),
  ADD KEY `idx_time_logs_user_issue` (`user_id`,`issue_id`),
  ADD KEY `idx_time_logs_date_range` (`work_date`,`status`);

--
-- Indexes for table `issue_types`
--
ALTER TABLE `issue_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issue_types_name_unique` (`name`);

--
-- Indexes for table `issue_versions`
--
ALTER TABLE `issue_versions`
  ADD PRIMARY KEY (`issue_id`,`version_id`,`type`),
  ADD KEY `issue_versions_version_id_fk` (`version_id`);

--
-- Indexes for table `issue_votes`
--
ALTER TABLE `issue_votes`
  ADD PRIMARY KEY (`issue_id`,`user_id`),
  ADD KEY `issue_votes_user_id_fk` (`user_id`);

--
-- Indexes for table `issue_watchers`
--
ALTER TABLE `issue_watchers`
  ADD PRIMARY KEY (`issue_id`,`user_id`),
  ADD KEY `issue_watchers_user_id_fk` (`user_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `items_created_by_idx` (`created_by`);

--
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `labels_project_name_unique` (`project_id`,`name`);

--
-- Indexes for table `mentions`
--
ALTER TABLE `mentions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentions_user_id_idx` (`user_id`),
  ADD KEY `mentions_comment_id_idx` (`comment_id`),
  ADD KEY `mentions_issue_id_idx` (`issue_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_notifications_dispatch_id` (`dispatch_id`),
  ADD KEY `notifications_user_unread_idx` (`user_id`,`is_read`,`created_at`),
  ADD KEY `notifications_actor_user_id_idx` (`actor_user_id`),
  ADD KEY `notifications_issue_id_idx` (`related_issue_id`),
  ADD KEY `notifications_dispatch_id_idx` (`dispatch_id`),
  ADD KEY `notifications_project_id_fk` (`related_project_id`);

--
-- Indexes for table `notifications_archive`
--
ALTER TABLE `notifications_archive`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_notifications_dispatch_id` (`dispatch_id`),
  ADD KEY `notifications_user_unread_idx` (`user_id`,`is_read`,`created_at`),
  ADD KEY `notifications_actor_user_id_idx` (`actor_user_id`),
  ADD KEY `notifications_issue_id_idx` (`related_issue_id`),
  ADD KEY `notifications_dispatch_id_idx` (`dispatch_id`),
  ADD KEY `notifications_project_id_fk` (`related_project_id`);

--
-- Indexes for table `notification_deliveries`
--
ALTER TABLE `notification_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_deliveries_status_idx` (`status`,`created_at`),
  ADD KEY `notification_deliveries_notification_id_idx` (`notification_id`);

--
-- Indexes for table `notification_dispatch_log`
--
ALTER TABLE `notification_dispatch_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dispatch_id` (`dispatch_id`),
  ADD KEY `idx_dispatch_id` (`dispatch_id`),
  ADD KEY `idx_issue_id` (`issue_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `actor_user_id` (`actor_user_id`);

--
-- Indexes for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_preferences_user_event_unique` (`user_id`,`event_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_resets_user_id_idx` (`user_id`),
  ADD KEY `password_resets_token_idx` (`token`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`),
  ADD KEY `permissions_category_idx` (`category`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_hash_unique` (`token_hash`),
  ADD KEY `personal_access_tokens_user_id_idx` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_key_unique` (`key`),
  ADD KEY `projects_lead_id_idx` (`lead_id`),
  ADD KEY `projects_category_id_idx` (`category_id`),
  ADD KEY `projects_is_archived_idx` (`is_archived`),
  ADD KEY `projects_created_by_fk` (`created_by`);

--
-- Indexes for table `project_budgets`
--
ALTER TABLE `project_budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_id` (`project_id`),
  ADD KEY `project_budgets_project_id_idx` (`project_id`),
  ADD KEY `idx_budgets_period` (`budget_period`,`period_start`,`period_end`);

--
-- Indexes for table `project_categories`
--
ALTER TABLE `project_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_documents_project_id_idx` (`project_id`),
  ADD KEY `project_documents_user_id_idx` (`user_id`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`project_id`,`user_id`),
  ADD KEY `project_members_user_id_idx` (`user_id`),
  ADD KEY `project_members_role_id_idx` (`role_id`);

--
-- Indexes for table `project_workflows`
--
ALTER TABLE `project_workflows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_workflows_backup_placeholder`
--
ALTER TABLE `project_workflows_backup_placeholder`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `push_device_tokens`
--
ALTER TABLE `push_device_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `push_device_tokens_user_id_idx` (`user_id`);

--
-- Indexes for table `roadmap_dependencies`
--
ALTER TABLE `roadmap_dependencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roadmap_dependencies_unique` (`item_id`,`depends_on_item_id`),
  ADD KEY `roadmap_dependencies_depends_on_idx` (`depends_on_item_id`);

--
-- Indexes for table `roadmap_items`
--
ALTER TABLE `roadmap_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roadmap_items_project_id_idx` (`project_id`),
  ADD KEY `roadmap_items_owner_id_idx` (`owner_id`),
  ADD KEY `roadmap_items_status_idx` (`status`),
  ADD KEY `roadmap_items_start_date_idx` (`start_date`),
  ADD KEY `roadmap_items_end_date_idx` (`end_date`),
  ADD KEY `roadmap_items_created_by_idx` (`created_by`);

--
-- Indexes for table `roadmap_item_issues`
--
ALTER TABLE `roadmap_item_issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roadmap_item_issues_unique` (`roadmap_item_id`,`issue_id`),
  ADD KEY `roadmap_item_issues_issue_idx` (`issue_id`);

--
-- Indexes for table `roadmap_item_sprints`
--
ALTER TABLE `roadmap_item_sprints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roadmap_item_sprints_unique` (`roadmap_item_id`,`sprint_id`),
  ADD KEY `roadmap_item_sprints_sprint_id_idx` (`sprint_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_idx` (`permission_id`);

--
-- Indexes for table `saved_filters`
--
ALTER TABLE `saved_filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saved_filters_user_id_idx` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `sprints`
--
ALTER TABLE `sprints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sprints_board_id_idx` (`board_id`),
  ADD KEY `sprints_status_idx` (`status`);

--
-- Indexes for table `sprint_issues`
--
ALTER TABLE `sprint_issues`
  ADD PRIMARY KEY (`sprint_id`,`issue_id`),
  ADD KEY `sprint_issues_issue_id_idx` (`issue_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `statuses_name_unique` (`name`),
  ADD KEY `statuses_category_idx` (`category`);

--
-- Indexes for table `time_tracking_reports`
--
ALTER TABLE `time_tracking_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `time_tracking_reports_unique` (`report_type`,`report_date`,`user_id`,`project_id`),
  ADD KEY `time_tracking_reports_user_id_fk` (`user_id`),
  ADD KEY `time_tracking_reports_project_id_fk` (`project_id`);

--
-- Indexes for table `time_tracking_settings`
--
ALTER TABLE `time_tracking_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_is_active_idx` (`is_active`),
  ADD KEY `users_created_at_idx` (`created_at`);

--
-- Indexes for table `user_rates`
--
ALTER TABLE `user_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_rates_user_id_idx` (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_unique` (`user_id`,`role_id`,`project_id`),
  ADD KEY `user_roles_role_id_idx` (`role_id`),
  ADD KEY `user_roles_project_id_idx` (`project_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_sessions_user_id_idx` (`user_id`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_settings_user_id_idx` (`user_id`),
  ADD KEY `user_settings_currency_idx` (`rate_currency`),
  ADD KEY `user_settings_created_at_idx` (`created_at`);

--
-- Indexes for table `versions`
--
ALTER TABLE `versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `versions_project_name_unique` (`project_id`,`name`),
  ADD KEY `versions_released_at_idx` (`released_at`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `workflows_name_unique` (`name`);

--
-- Indexes for table `workflow_statuses`
--
ALTER TABLE `workflow_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `workflow_statuses_unique` (`workflow_id`,`status_id`),
  ADD KEY `workflow_statuses_status_id_idx` (`status_id`);

--
-- Indexes for table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_transitions_workflow_id_idx` (`workflow_id`),
  ADD KEY `workflow_transitions_from_status_id_idx` (`from_status_id`),
  ADD KEY `workflow_transitions_to_status_id_idx` (`to_status_id`);

--
-- Indexes for table `worklogs`
--
ALTER TABLE `worklogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `worklogs_issue_id_idx` (`issue_id`),
  ADD KEY `worklogs_user_id_idx` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_timers`
--
ALTER TABLE `active_timers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `boards`
--
ALTER TABLE `boards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `board_columns`
--
ALTER TABLE `board_columns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `budget_alerts`
--
ALTER TABLE `budget_alerts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calendar_events`
--
ALTER TABLE `calendar_events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `comment_history`
--
ALTER TABLE `comment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `components`
--
ALTER TABLE `components`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_field_contexts`
--
ALTER TABLE `custom_field_contexts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dashboards`
--
ALTER TABLE `dashboards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dashboard_gadgets`
--
ALTER TABLE `dashboard_gadgets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `issue_attachments`
--
ALTER TABLE `issue_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `issue_history`
--
ALTER TABLE `issue_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `issue_links`
--
ALTER TABLE `issue_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_link_types`
--
ALTER TABLE `issue_link_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_priorities`
--
ALTER TABLE `issue_priorities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `issue_time_logs`
--
ALTER TABLE `issue_time_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_types`
--
ALTER TABLE `issue_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `mentions`
--
ALTER TABLE `mentions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications_archive`
--
ALTER TABLE `notifications_archive`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_deliveries`
--
ALTER TABLE `notification_deliveries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_dispatch_log`
--
ALTER TABLE `notification_dispatch_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_budgets`
--
ALTER TABLE `project_budgets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_categories`
--
ALTER TABLE `project_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `project_workflows`
--
ALTER TABLE `project_workflows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_workflows_backup_placeholder`
--
ALTER TABLE `project_workflows_backup_placeholder`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `push_device_tokens`
--
ALTER TABLE `push_device_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roadmap_dependencies`
--
ALTER TABLE `roadmap_dependencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roadmap_items`
--
ALTER TABLE `roadmap_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roadmap_item_issues`
--
ALTER TABLE `roadmap_item_issues`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roadmap_item_sprints`
--
ALTER TABLE `roadmap_item_sprints`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `saved_filters`
--
ALTER TABLE `saved_filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sprints`
--
ALTER TABLE `sprints`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `time_tracking_reports`
--
ALTER TABLE `time_tracking_reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_tracking_settings`
--
ALTER TABLE `time_tracking_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_rates`
--
ALTER TABLE `user_rates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `versions`
--
ALTER TABLE `versions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workflow_statuses`
--
ALTER TABLE `workflow_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `worklogs`
--
ALTER TABLE `worklogs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_timers`
--
ALTER TABLE `active_timers`
  ADD CONSTRAINT `active_timers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `active_timers_time_log_id_fk` FOREIGN KEY (`issue_time_log_id`) REFERENCES `issue_time_logs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `active_timers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `boards`
--
ALTER TABLE `boards`
  ADD CONSTRAINT `boards_owner_id_fk` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `boards_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `board_columns`
--
ALTER TABLE `board_columns`
  ADD CONSTRAINT `board_columns_board_id_fk` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_alerts`
--
ALTER TABLE `budget_alerts`
  ADD CONSTRAINT `budget_alerts_acknowledged_by_fk` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `budget_alerts_project_budget_id_fk` FOREIGN KEY (`project_budget_id`) REFERENCES `project_budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_alerts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD CONSTRAINT `calendar_events_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calendar_events_priority_id_fk` FOREIGN KEY (`priority_id`) REFERENCES `issue_priorities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `calendar_events_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `comment_history`
--
ALTER TABLE `comment_history`
  ADD CONSTRAINT `comment_history_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_history_ibfk_2` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `components`
--
ALTER TABLE `components`
  ADD CONSTRAINT `components_default_assignee_id_fk` FOREIGN KEY (`default_assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `components_lead_id_fk` FOREIGN KEY (`lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `components_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_field_contexts`
--
ALTER TABLE `custom_field_contexts`
  ADD CONSTRAINT `custom_field_contexts_field_id_fk` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `custom_field_contexts_issue_type_id_fk` FOREIGN KEY (`issue_type_id`) REFERENCES `issue_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `custom_field_contexts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  ADD CONSTRAINT `custom_field_values_field_id_fk` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `custom_field_values_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dashboards`
--
ALTER TABLE `dashboards`
  ADD CONSTRAINT `dashboards_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dashboard_gadgets`
--
ALTER TABLE `dashboard_gadgets`
  ADD CONSTRAINT `dashboard_gadgets_dashboard_id_fk` FOREIGN KEY (`dashboard_id`) REFERENCES `dashboards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_assignee_id_fk` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issues_epic_id_fk` FOREIGN KEY (`epic_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issues_issue_type_id_fk` FOREIGN KEY (`issue_type_id`) REFERENCES `issue_types` (`id`),
  ADD CONSTRAINT `issues_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issues_priority_id_fk` FOREIGN KEY (`priority_id`) REFERENCES `issue_priorities` (`id`),
  ADD CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issues_reporter_id_fk` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `issues_sprint_id_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issues_status_id_fk` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

--
-- Constraints for table `issue_attachments`
--
ALTER TABLE `issue_attachments`
  ADD CONSTRAINT `issue_attachments_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_attachments_user_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `issue_components`
--
ALTER TABLE `issue_components`
  ADD CONSTRAINT `issue_components_component_id_fk` FOREIGN KEY (`component_id`) REFERENCES `components` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_components_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_history`
--
ALTER TABLE `issue_history`
  ADD CONSTRAINT `issue_history_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_history_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `issue_labels`
--
ALTER TABLE `issue_labels`
  ADD CONSTRAINT `issue_labels_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_labels_label_id_fk` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_links`
--
ALTER TABLE `issue_links`
  ADD CONSTRAINT `issue_links_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issue_links_source_fk` FOREIGN KEY (`source_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_links_target_fk` FOREIGN KEY (`target_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_links_type_fk` FOREIGN KEY (`link_type_id`) REFERENCES `issue_link_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_time_logs`
--
ALTER TABLE `issue_time_logs`
  ADD CONSTRAINT `issue_time_logs_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_time_logs_project_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issue_time_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_versions`
--
ALTER TABLE `issue_versions`
  ADD CONSTRAINT `issue_versions_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_versions_version_id_fk` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_votes`
--
ALTER TABLE `issue_votes`
  ADD CONSTRAINT `issue_votes_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_votes_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_watchers`
--
ALTER TABLE `issue_watchers`
  ADD CONSTRAINT `issue_watchers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_watchers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentions`
--
ALTER TABLE `mentions`
  ADD CONSTRAINT `mentions_comment_id_fk` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentions_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_actor_user_id_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_issue_id_fk` FOREIGN KEY (`related_issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_project_id_fk` FOREIGN KEY (`related_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_deliveries`
--
ALTER TABLE `notification_deliveries`
  ADD CONSTRAINT `notification_deliveries_notification_id_fk` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_dispatch_log`
--
ALTER TABLE `notification_dispatch_log`
  ADD CONSTRAINT `notification_dispatch_log_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_dispatch_log_ibfk_2` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD CONSTRAINT `notification_preferences_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD CONSTRAINT `personal_access_tokens_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `project_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_lead_id_fk` FOREIGN KEY (`lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_budgets`
--
ALTER TABLE `project_budgets`
  ADD CONSTRAINT `project_budgets_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD CONSTRAINT `project_documents_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_documents_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `project_members_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_device_tokens`
--
ALTER TABLE `push_device_tokens`
  ADD CONSTRAINT `push_device_tokens_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roadmap_dependencies`
--
ALTER TABLE `roadmap_dependencies`
  ADD CONSTRAINT `roadmap_dependencies_depends_on_fk` FOREIGN KEY (`depends_on_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roadmap_dependencies_item_fk` FOREIGN KEY (`item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roadmap_items`
--
ALTER TABLE `roadmap_items`
  ADD CONSTRAINT `roadmap_items_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `roadmap_items_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roadmap_item_issues`
--
ALTER TABLE `roadmap_item_issues`
  ADD CONSTRAINT `roadmap_item_issues_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roadmap_item_issues_item_fk` FOREIGN KEY (`roadmap_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roadmap_item_sprints`
--
ALTER TABLE `roadmap_item_sprints`
  ADD CONSTRAINT `roadmap_item_sprints_item_fk` FOREIGN KEY (`roadmap_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roadmap_item_sprints_sprint_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_filters`
--
ALTER TABLE `saved_filters`
  ADD CONSTRAINT `saved_filters_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sprints`
--
ALTER TABLE `sprints`
  ADD CONSTRAINT `sprints_board_id_fk` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sprint_issues`
--
ALTER TABLE `sprint_issues`
  ADD CONSTRAINT `sprint_issues_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sprint_issues_sprint_id_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_tracking_reports`
--
ALTER TABLE `time_tracking_reports`
  ADD CONSTRAINT `time_tracking_reports_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `time_tracking_reports_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_rates`
--
ALTER TABLE `user_rates`
  ADD CONSTRAINT `user_rates_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `versions`
--
ALTER TABLE `versions`
  ADD CONSTRAINT `versions_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_statuses`
--
ALTER TABLE `workflow_statuses`
  ADD CONSTRAINT `workflow_statuses_status_id_fk` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_statuses_workflow_id_fk` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  ADD CONSTRAINT `workflow_transitions_from_status_id_fk` FOREIGN KEY (`from_status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_transitions_to_status_id_fk` FOREIGN KEY (`to_status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_transitions_workflow_id_fk` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `worklogs`
--
ALTER TABLE `worklogs`
  ADD CONSTRAINT `worklogs_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `worklogs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
