-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260405.bd94fa9f11
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 29, 2026 at 09:09 AM
-- Server version: 8.4.3
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference_table` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `occurred_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `module`, `action`, `description`, `reference_table`, `reference_id`, `ip_address`, `device`, `user_agent`, `occurred_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 09:53:48', '2026-04-17 09:53:48', '2026-04-17 09:53:48'),
(2, 1, 'positions', 'update', 'Cap nhat chuc vu \'Truong phong HR\': Cap them quyen: approve_leave, manage_leave_policy', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 09:55:22', '2026-04-17 09:55:22', '2026-04-17 09:55:22'),
(3, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 09:55:22', '2026-04-17 09:55:22', '2026-04-17 09:55:22'),
(4, 1, 'attendance', 'check_in', 'Check in attendance record #2', 'attendance_records', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 10:19:20', '2026-04-17 10:19:20', '2026-04-17 10:19:20'),
(5, 1, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 10:19:20', '2026-04-17 10:19:20', '2026-04-17 10:19:20'),
(6, 1, 'attendance', 'check_out', 'Check out attendance record #2', 'attendance_records', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 10:19:24', '2026-04-17 10:19:24', '2026-04-17 10:19:24'),
(7, 1, 'attendance:check-out', 'create', 'POST attendance/check-out (attendance.check-out)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 10:19:24', '2026-04-17 10:19:24', '2026-04-17 10:19:24'),
(8, 1, 'attendance', 'reject', 'Reject attendance record #2', 'attendance_records', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 10:19:42', '2026-04-17 10:19:42', '2026-04-17 10:19:42'),
(9, 1, 'attendance:reject', 'create', 'POST attendance/2/reject (attendance.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 10:19:42', '2026-04-17 10:19:42', '2026-04-17 10:19:42'),
(10, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:00:49', '2026-04-20 01:00:49', '2026-04-20 01:00:49'),
(11, 1, 'attendance', 'check_in', 'Check in attendance record #3', 'attendance_records', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:00:52', '2026-04-20 01:00:52', '2026-04-20 01:00:52'),
(12, 1, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:00:52', '2026-04-20 01:00:52', '2026-04-20 01:00:52'),
(13, 1, 'users', 'create', 'Tao tai khoan dophuhieu15@gmail.com', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:04:05', '2026-04-20 01:04:05', '2026-04-20 01:04:05'),
(14, 1, 'web:users:store', 'create', 'POST users (web.users.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:04:05', '2026-04-20 01:04:05', '2026-04-20 01:04:05'),
(15, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mat khau', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:05:06', '2026-04-20 01:05:06', '2026-04-20 01:05:06'),
(16, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: check_out, check_in, reply_feedback, create_feedback, view_own_attendance, request_attendance_adjustment, request_leave, update_own_profile, view_own_profile, view_own_leave_requests, view_holidays, view_work_shifts, update_project, update_project_task_status, view_dashboard', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:07:24', '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(17, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:07:24', '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(18, 4, 'attendance', 'check_in', 'Check in attendance record #4', 'attendance_records', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:07:39', '2026-04-20 01:07:39', '2026-04-20 01:07:39'),
(19, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:07:39', '2026-04-20 01:07:39', '2026-04-20 01:07:39'),
(20, 1, 'departments', 'update', 'Cap nhat phong ban #1', 'departments', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:17:28', '2026-04-20 01:17:28', '2026-04-20 01:17:28'),
(21, 1, 'departments:update', 'update', 'PUT departments/1 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:17:28', '2026-04-20 01:17:28', '2026-04-20 01:17:28'),
(22, 1, 'departments', 'update', 'Cap nhat phong ban #1', 'departments', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:17:36', '2026-04-20 01:17:36', '2026-04-20 01:17:36'),
(23, 1, 'departments:update', 'update', 'PUT departments/1 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:17:36', '2026-04-20 01:17:36', '2026-04-20 01:17:36'),
(24, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\'', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:30:31', '2026-04-20 01:30:31', '2026-04-20 01:30:31'),
(25, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:30:31', '2026-04-20 01:30:31', '2026-04-20 01:30:31'),
(26, 1, 'positions', 'update', 'Cap nhat chuc vu \'Truong phong HR\': Thu hoi quyen: manage_employees, approve_attendance, approve_requests', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:30:38', '2026-04-20 01:30:38', '2026-04-20 01:30:38'),
(27, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:30:38', '2026-04-20 01:30:38', '2026-04-20 01:30:38'),
(28, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: view_feedbacks', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:31:40', '2026-04-20 01:31:40', '2026-04-20 01:31:40'),
(29, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:31:40', '2026-04-20 01:31:40', '2026-04-20 01:31:40'),
(30, 4, 'feedbacks:store', 'create', 'POST feedbacks (feedbacks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 01:46:42', '2026-04-20 01:46:42', '2026-04-20 01:46:42'),
(31, 4, 'attendance', 'submit_adjustment_request', 'Submit attendance adjustment #1: on_time/early_leave, worked 232, late 0, early 325, overtime 232', 'attendance_adjustments', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:30:28', '2026-04-20 02:30:28', '2026-04-20 02:30:28'),
(32, 4, 'attendance:adjustments:store', 'create', 'POST attendance/adjustments (attendance.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:30:28', '2026-04-20 02:30:28', '2026-04-20 02:30:28'),
(33, 1, 'attendance', 'review_adjustment_approved', 'Review attendance adjustment #1', 'attendance_adjustments', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:36:40', '2026-04-20 02:36:40', '2026-04-20 02:36:40'),
(34, 1, 'attendance:adjustments:approve', 'create', 'POST attendance/adjustments/1/approve (attendance.adjustments.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:36:40', '2026-04-20 02:36:40', '2026-04-20 02:36:40'),
(35, 4, 'attendance', 'submit_adjustment_request', 'Submit attendance adjustment #2: on_time/early_leave, worked 532, late 0, early 25, overtime 532', 'attendance_adjustments', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:37:44', '2026-04-20 02:37:44', '2026-04-20 02:37:44'),
(36, 4, 'attendance:adjustments:store', 'create', 'POST attendance/adjustments (attendance.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:37:44', '2026-04-20 02:37:44', '2026-04-20 02:37:44'),
(37, 1, 'attendance', 'review_adjustment_rejected', 'Review attendance adjustment #2', 'attendance_adjustments', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:41:00', '2026-04-20 02:41:00', '2026-04-20 02:41:00'),
(38, 1, 'attendance:adjustments:reject', 'create', 'POST attendance/adjustments/2/reject (attendance.adjustments.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:41:00', '2026-04-20 02:41:00', '2026-04-20 02:41:00'),
(39, 4, 'attendance', 'submit_adjustment_request', 'Submit attendance adjustment #3: on_time/early_leave, worked 532, late 0, early 25, overtime 532', 'attendance_adjustments', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:41:32', '2026-04-20 02:41:32', '2026-04-20 02:41:32'),
(40, 4, 'attendance:adjustments:store', 'create', 'POST attendance/adjustments (attendance.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:41:32', '2026-04-20 02:41:32', '2026-04-20 02:41:32'),
(41, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/1/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:45:14', '2026-04-20 02:45:14', '2026-04-20 02:45:14'),
(42, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:45:24', '2026-04-20 02:45:24', '2026-04-20 02:45:24'),
(43, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:45:26', '2026-04-20 02:45:26', '2026-04-20 02:45:26'),
(44, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:48:24', '2026-04-20 02:48:24', '2026-04-20 02:48:24'),
(45, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:48:31', '2026-04-20 02:48:31', '2026-04-20 02:48:31'),
(46, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:48:36', '2026-04-20 02:48:36', '2026-04-20 02:48:36'),
(47, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:48:38', '2026-04-20 02:48:38', '2026-04-20 02:48:38'),
(48, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:48:39', '2026-04-20 02:48:39', '2026-04-20 02:48:39'),
(49, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/1/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 02:56:01', '2026-04-20 02:56:01', '2026-04-20 02:56:01'),
(50, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: view_salary', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:34:44', '2026-04-20 03:34:44', '2026-04-20 03:34:44'),
(51, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:34:44', '2026-04-20 03:34:44', '2026-04-20 03:34:44'),
(52, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: view_own_salary', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:35:33', '2026-04-20 03:35:33', '2026-04-20 03:35:33'),
(53, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:35:33', '2026-04-20 03:35:33', '2026-04-20 03:35:33'),
(54, 1, 'attendance', 'review_adjustment_approved', 'Review attendance adjustment #3', 'attendance_adjustments', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:46:26', '2026-04-20 03:46:26', '2026-04-20 03:46:26'),
(55, 1, 'attendance:adjustments:approve', 'create', 'POST attendance/adjustments/3/approve (attendance.adjustments.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:46:26', '2026-04-20 03:46:26', '2026-04-20 03:46:26'),
(56, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:54:15', '2026-04-20 03:54:15', '2026-04-20 03:54:15'),
(57, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:55:06', '2026-04-20 03:55:06', '2026-04-20 03:55:06'),
(58, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:55:12', '2026-04-20 03:55:12', '2026-04-20 03:55:12'),
(59, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:04:14', '2026-04-20 04:04:14', '2026-04-20 04:04:14'),
(60, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/1 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:04:54', '2026-04-20 04:04:54', '2026-04-20 04:04:54'),
(61, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:05:24', '2026-04-20 04:05:24', '2026-04-20 04:05:24'),
(62, 1, 'attendance:catalogs:holidays:store', 'create', 'POST attendance/catalogs/holidays (attendance.catalogs.holidays.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:06:24', '2026-04-20 04:06:24', '2026-04-20 04:06:24'),
(63, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:22:44', '2026-04-20 04:22:44', '2026-04-20 04:22:44'),
(64, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:27:58', '2026-04-20 04:27:58', '2026-04-20 04:27:58'),
(65, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:28:38', '2026-04-20 04:28:38', '2026-04-20 04:28:38'),
(66, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:29:01', '2026-04-20 04:29:01', '2026-04-20 04:29:01'),
(67, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:34:01', '2026-04-20 04:34:01', '2026-04-20 04:34:01'),
(68, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:34:14', '2026-04-20 04:34:14', '2026-04-20 04:34:14'),
(69, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:55:08', '2026-04-20 04:55:08', '2026-04-20 04:55:08'),
(70, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:55:15', '2026-04-20 04:55:15', '2026-04-20 04:55:15'),
(71, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/3/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:55:16', '2026-04-20 04:55:16', '2026-04-20 04:55:16'),
(72, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/3/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:55:18', '2026-04-20 04:55:18', '2026-04-20 04:55:18'),
(73, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:55:20', '2026-04-20 04:55:20', '2026-04-20 04:55:20'),
(74, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:55:27', '2026-04-20 04:55:27', '2026-04-20 04:55:27'),
(75, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:56:11', '2026-04-20 04:56:11', '2026-04-20 04:56:11'),
(76, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:58:22', '2026-04-20 04:58:22', '2026-04-20 04:58:22'),
(77, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:58:25', '2026-04-20 04:58:25', '2026-04-20 04:58:25'),
(78, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:59:53', '2026-04-20 04:59:53', '2026-04-20 04:59:53'),
(79, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/1/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:00:02', '2026-04-20 05:00:02', '2026-04-20 05:00:02'),
(80, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/3/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:00:05', '2026-04-20 05:00:05', '2026-04-20 05:00:05'),
(81, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/3/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:00:07', '2026-04-20 05:00:07', '2026-04-20 05:00:07'),
(82, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:38:49', '2026-04-20 06:38:49', '2026-04-20 06:38:49'),
(83, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:39:59', '2026-04-20 06:39:59', '2026-04-20 06:39:59'),
(84, 1, 'attendance:catalogs:work-shifts:store', 'create', 'POST attendance/catalogs/work-shifts (attendance.catalogs.work-shifts.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:40:29', '2026-04-20 06:40:29', '2026-04-20 06:40:29'),
(85, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:41:19', '2026-04-20 06:41:19', '2026-04-20 06:41:19'),
(86, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:48:00', '2026-04-20 06:48:00', '2026-04-20 06:48:00'),
(87, 1, 'attendance:catalogs:holidays:update', 'update', 'PUT attendance/catalogs/holidays/1 (attendance.catalogs.holidays.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:48:59', '2026-04-20 06:48:59', '2026-04-20 06:48:59'),
(88, 1, 'attendance:catalogs:holidays:update', 'update', 'PUT attendance/catalogs/holidays/1 (attendance.catalogs.holidays.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:49:09', '2026-04-20 06:49:09', '2026-04-20 06:49:09'),
(89, 1, 'attendance:catalogs:holidays:update', 'update', 'PUT attendance/catalogs/holidays/1 (attendance.catalogs.holidays.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:49:16', '2026-04-20 06:49:16', '2026-04-20 06:49:16'),
(90, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:52:03', '2026-04-20 06:52:03', '2026-04-20 06:52:03'),
(91, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 06:52:38', '2026-04-20 06:52:38', '2026-04-20 06:52:38'),
(92, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:18:50', '2026-04-20 07:18:50', '2026-04-20 07:18:50'),
(93, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:19:29', '2026-04-20 07:19:29', '2026-04-20 07:19:29'),
(94, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:28:50', '2026-04-20 07:28:50', '2026-04-20 07:28:50'),
(95, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:42:39', '2026-04-20 07:42:39', '2026-04-20 07:42:39'),
(96, 4, 'attendance', 'submit_overtime_request', 'Submit overtime request #1', 'overtime_requests', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:43:01', '2026-04-20 07:43:01', '2026-04-20 07:43:01'),
(97, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:43:01', '2026-04-20 07:43:01', '2026-04-20 07:43:01'),
(98, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/1/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:46:42', '2026-04-20 07:46:42', '2026-04-20 07:46:42'),
(99, 1, 'attendance:catalogs:work-shifts:toggle', 'update', 'PUT attendance/catalogs/work-shifts/2/toggle (attendance.catalogs.work-shifts.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:46:46', '2026-04-20 07:46:46', '2026-04-20 07:46:46'),
(100, 1, 'attendance', 'review_request_rejected', 'Review approval request #4', 'approval_requests', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:47:47', '2026-04-20 07:47:47', '2026-04-20 07:47:47'),
(101, 1, 'attendance:request-approvals:reject', 'create', 'POST attendance/request-approvals/4/reject (attendance.request-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:47:47', '2026-04-20 07:47:47', '2026-04-20 07:47:47'),
(102, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:49:40', '2026-04-20 07:49:40', '2026-04-20 07:49:40'),
(103, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:49:43', '2026-04-20 07:49:43', '2026-04-20 07:49:43'),
(104, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:50:02', '2026-04-20 07:50:02', '2026-04-20 07:50:02'),
(105, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:50:27', '2026-04-20 07:50:27', '2026-04-20 07:50:27'),
(106, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:50:29', '2026-04-20 07:50:29', '2026-04-20 07:50:29'),
(107, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:55:03', '2026-04-20 07:55:03', '2026-04-20 07:55:03'),
(108, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 07:55:05', '2026-04-20 07:55:05', '2026-04-20 07:55:05'),
(109, 1, 'attendance:catalogs:holidays:store', 'create', 'POST attendance/catalogs/holidays (attendance.catalogs.holidays.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 08:01:21', '2026-04-20 08:01:21', '2026-04-20 08:01:21'),
(110, 1, 'attendance:catalogs:holidays:update', 'update', 'PUT attendance/catalogs/holidays/1 (attendance.catalogs.holidays.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 08:42:55', '2026-04-20 08:42:55', '2026-04-20 08:42:55'),
(111, 1, 'positions', 'update', 'Cap nhat chuc vu \'Truong phong HR\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 09:00:33', '2026-04-20 09:00:33', '2026-04-20 09:00:33'),
(112, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 09:00:33', '2026-04-20 09:00:33', '2026-04-20 09:00:33'),
(113, 4, 'feedbacks:store', 'create', 'POST feedbacks (feedbacks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 09:22:51', '2026-04-20 09:22:51', '2026-04-20 09:22:51'),
(114, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/4/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 09:30:18', '2026-04-20 09:30:18', '2026-04-20 09:30:18'),
(115, 4, 'feedbacks:store', 'create', 'POST feedbacks (feedbacks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 09:42:40', '2026-04-20 09:42:40', '2026-04-20 09:42:40'),
(116, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 00:59:31', '2026-04-21 00:59:31', '2026-04-21 00:59:31'),
(117, 1, 'attendance', 'check_in', 'Check in attendance record #5', 'attendance_records', 5, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 00:59:37', '2026-04-21 00:59:37', '2026-04-21 00:59:37'),
(118, 1, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 00:59:37', '2026-04-21 00:59:37', '2026-04-21 00:59:37'),
(119, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mat khau', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:00:04', '2026-04-21 01:00:04', '2026-04-21 01:00:04'),
(120, 4, 'attendance', 'check_in', 'Check in attendance record #6', 'attendance_records', 6, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:00:12', '2026-04-21 01:00:12', '2026-04-21 01:00:12'),
(121, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:00:12', '2026-04-21 01:00:12', '2026-04-21 01:00:12'),
(122, 1, 'attendance', 'lock_month', 'Lock attendance month 4/2026', 'attendance_month_locks', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:33:40', '2026-04-21 01:33:40', '2026-04-21 01:33:40'),
(123, 1, 'attendance:month-locks:lock', 'create', 'POST attendance/month-locks/lock (attendance.month-locks.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:33:40', '2026-04-21 01:33:40', '2026-04-21 01:33:40'),
(124, 1, 'attendance', 'unlock_month', 'Unlock attendance month 4/2026', 'attendance_month_locks', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:33:47', '2026-04-21 01:33:47', '2026-04-21 01:33:47'),
(125, 1, 'attendance:month-locks:unlock', 'create', 'POST attendance/month-locks/unlock (attendance.month-locks.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:33:47', '2026-04-21 01:33:47', '2026-04-21 01:33:47'),
(126, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:53:24', '2026-04-21 01:53:24', '2026-04-21 01:53:24'),
(127, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 01:53:29', '2026-04-21 01:53:29', '2026-04-21 01:53:29'),
(128, 4, 'auth', 'logout', 'Dang xuat khoi he thong', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:02:37', '2026-04-21 02:02:37', '2026-04-21 02:02:37'),
(129, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mat khau', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:02:52', '2026-04-21 02:02:52', '2026-04-21 02:02:52'),
(130, 4, 'my-profile:avatar', 'create', 'POST my-profile/avatar (my-profile.avatar)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:03:06', '2026-04-21 02:03:06', '2026-04-21 02:03:06'),
(131, 4, 'my-profile:update', 'update', 'PUT my-profile (my-profile.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:10:58', '2026-04-21 02:10:58', '2026-04-21 02:10:58'),
(132, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:18:53', '2026-04-21 02:18:53', '2026-04-21 02:18:53'),
(133, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:18:57', '2026-04-21 02:18:57', '2026-04-21 02:18:57'),
(134, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:27:48', '2026-04-21 02:27:48', '2026-04-21 02:27:48'),
(135, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:28:34', '2026-04-21 02:28:34', '2026-04-21 02:28:34'),
(136, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/2 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:37:49', '2026-04-21 02:37:49', '2026-04-21 02:37:49'),
(137, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/3 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:38:55', '2026-04-21 02:38:55', '2026-04-21 02:38:55'),
(138, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/2 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:40:28', '2026-04-21 02:40:28', '2026-04-21 02:40:28'),
(139, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:51:54', '2026-04-21 02:51:54', '2026-04-21 02:51:54'),
(140, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:52:05', '2026-04-21 02:52:05', '2026-04-21 02:52:05'),
(141, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:53:59', '2026-04-21 02:53:59', '2026-04-21 02:53:59'),
(142, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:54:30', '2026-04-21 02:54:30', '2026-04-21 02:54:30'),
(143, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:54:45', '2026-04-21 02:54:45', '2026-04-21 02:54:45'),
(144, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:59:08', '2026-04-21 02:59:08', '2026-04-21 02:59:08'),
(145, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:59:13', '2026-04-21 02:59:13', '2026-04-21 02:59:13'),
(146, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 02:59:18', '2026-04-21 02:59:18', '2026-04-21 02:59:18'),
(147, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 03:18:52', '2026-04-21 03:18:52', '2026-04-21 03:18:52'),
(148, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 03:32:50', '2026-04-21 03:32:50', '2026-04-21 03:32:50');
INSERT INTO `activity_logs` (`id`, `user_id`, `module`, `action`, `description`, `reference_table`, `reference_id`, `ip_address`, `device`, `user_agent`, `occurred_at`, `created_at`, `updated_at`) VALUES
(149, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 03:36:55', '2026-04-21 03:36:55', '2026-04-21 03:36:55'),
(150, 1, 'attendance:confirm', 'create', 'POST attendance/6/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 03:47:45', '2026-04-21 03:47:45', '2026-04-21 03:47:45'),
(151, 1, 'attendance:confirm', 'create', 'POST attendance/6/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 03:47:58', '2026-04-21 03:47:58', '2026-04-21 03:47:58'),
(152, 1, 'leave', 'toggle_leave_type', 'Toggle leave type #3', 'leave_types', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:40:01', '2026-04-21 04:40:01', '2026-04-21 04:40:01'),
(153, 1, 'leave-management:types:toggle', 'update', 'PUT leave-management/types/3/toggle (leave-management.types.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:40:01', '2026-04-21 04:40:01', '2026-04-21 04:40:01'),
(154, 1, 'leave', 'toggle_leave_type', 'Toggle leave type #3', 'leave_types', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:41:15', '2026-04-21 04:41:15', '2026-04-21 04:41:15'),
(155, 1, 'leave-management:types:toggle', 'update', 'PUT leave-management/types/3/toggle (leave-management.types.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:41:15', '2026-04-21 04:41:15', '2026-04-21 04:41:15'),
(156, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 12 rows for 2026', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:53:12', '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(157, 1, 'leave-management:balances:grant-bulk', 'create', 'POST leave-management/balances/grant-bulk (leave-management.balances.grant-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:53:12', '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(158, 1, 'leave', 'grant_leave_balance', 'Grant leave balance #9', 'employee_leave_balances', 9, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:53:46', '2026-04-21 04:53:46', '2026-04-21 04:53:46'),
(159, 1, 'leave-management:balances:grant', 'create', 'POST leave-management/balances/grant (leave-management.balances.grant)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:53:46', '2026-04-21 04:53:46', '2026-04-21 04:53:46'),
(160, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:54:44', '2026-04-21 04:54:44', '2026-04-21 04:54:44'),
(161, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:54:57', '2026-04-21 04:54:57', '2026-04-21 04:54:57'),
(162, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:55:08', '2026-04-21 04:55:08', '2026-04-21 04:55:08'),
(163, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:55:11', '2026-04-21 04:55:11', '2026-04-21 04:55:11'),
(164, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 12 rows for 2026', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:00', '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(165, 1, 'leave-management:balances:grant-bulk', 'create', 'POST leave-management/balances/grant-bulk (leave-management.balances.grant-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:00', '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(166, 1, 'leave-management:balances:adjust', 'create', 'POST leave-management/balances/3/adjust (leave-management.balances.adjust)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:16', '2026-04-21 04:56:16', '2026-04-21 04:56:16'),
(167, 1, 'leave', 'adjust_leave_balance', 'Adjust leave balance #3', 'employee_leave_balances', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:19', '2026-04-21 04:56:19', '2026-04-21 04:56:19'),
(168, 1, 'leave-management:balances:adjust', 'create', 'POST leave-management/balances/3/adjust (leave-management.balances.adjust)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:19', '2026-04-21 04:56:19', '2026-04-21 04:56:19'),
(169, 1, 'leave', 'adjust_leave_balance', 'Adjust leave balance #6', 'employee_leave_balances', 6, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:27', '2026-04-21 04:56:27', '2026-04-21 04:56:27'),
(170, 1, 'leave-management:balances:adjust', 'create', 'POST leave-management/balances/6/adjust (leave-management.balances.adjust)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 04:56:27', '2026-04-21 04:56:27', '2026-04-21 04:56:27'),
(171, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 12 rows for 2026', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 05:01:14', '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(172, 1, 'leave-management:balances:grant-bulk', 'create', 'POST leave-management/balances/grant-bulk (leave-management.balances.grant-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 05:01:14', '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(173, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 12 rows for 2026', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:28:31', '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(174, 1, 'leave-management:balances:grant-bulk', 'create', 'POST leave-management/balances/grant-bulk (leave-management.balances.grant-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:28:31', '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(175, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 12 rows for 2026', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:28:42', '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(176, 1, 'leave-management:balances:grant-bulk', 'create', 'POST leave-management/balances/grant-bulk (leave-management.balances.grant-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:28:42', '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(177, 1, 'leave', 'update_leave_type', 'Update leave type #3', 'leave_types', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:39:22', '2026-04-21 06:39:22', '2026-04-21 06:39:22'),
(178, 1, 'leave-management:types:update', 'update', 'PUT leave-management/types/3 (leave-management.types.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:39:22', '2026-04-21 06:39:22', '2026-04-21 06:39:22'),
(179, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:46:07', '2026-04-21 06:46:07', '2026-04-21 06:46:07'),
(180, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 06:46:09', '2026-04-21 06:46:09', '2026-04-21 06:46:09'),
(181, 1, 'leave', 'create_leave_type', 'Create leave type #4', 'leave_types', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:05:04', '2026-04-21 07:05:04', '2026-04-21 07:05:04'),
(182, 1, 'leave-management:types:store', 'create', 'POST leave-management/types (leave-management.types.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:05:04', '2026-04-21 07:05:04', '2026-04-21 07:05:04'),
(183, 1, 'leave-management:types:store', 'create', 'POST leave-management/types (leave-management.types.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:08:40', '2026-04-21 07:08:40', '2026-04-21 07:08:40'),
(184, 4, 'attendance', 'submit_request', 'Submit attendance request #1', 'attendance_requests', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:25:56', '2026-04-21 07:25:56', '2026-04-21 07:25:56'),
(185, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:25:56', '2026-04-21 07:25:56', '2026-04-21 07:25:56'),
(186, 1, 'attendance', 'review_request_rejected', 'Review approval request #5', 'approval_requests', 5, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:27:12', '2026-04-21 07:27:12', '2026-04-21 07:27:12'),
(187, 1, 'attendance:request-approvals:reject', 'create', 'POST attendance/request-approvals/5/reject (attendance.request-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:27:12', '2026-04-21 07:27:12', '2026-04-21 07:27:12'),
(188, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 16 rows for 2026', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:37:27', '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(189, 1, 'leave-management:balances:grant-bulk', 'create', 'POST leave-management/balances/grant-bulk (leave-management.balances.grant-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:37:27', '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(190, 1, 'feedbacks:reply', 'create', 'POST feedbacks/1/reply (feedbacks.reply)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:52:05', '2026-04-21 07:52:05', '2026-04-21 07:52:05'),
(191, 4, 'feedbacks:reply', 'create', 'POST feedbacks/1/reply (feedbacks.reply)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:52:48', '2026-04-21 07:52:48', '2026-04-21 07:52:48'),
(192, 1, 'feedbacks:read', 'create', 'POST feedbacks/1/read (feedbacks.read)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 07:54:04', '2026-04-21 07:54:04', '2026-04-21 07:54:04'),
(193, 1, 'feedbacks:read', 'create', 'POST feedbacks/1/read (feedbacks.read)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:12:40', '2026-04-21 08:12:40', '2026-04-21 08:12:40'),
(194, 1, 'feedbacks:reply', 'create', 'POST feedbacks/1/reply (feedbacks.reply)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:22:42', '2026-04-21 08:22:42', '2026-04-21 08:22:42'),
(195, 1, 'feedbacks:read', 'create', 'POST feedbacks/1/read (feedbacks.read)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:23:11', '2026-04-21 08:23:11', '2026-04-21 08:23:11'),
(196, 4, 'feedbacks:reply', 'create', 'POST feedbacks/3/reply (feedbacks.reply)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:38:10', '2026-04-21 08:38:10', '2026-04-21 08:38:10'),
(197, 1, 'web:users:store', 'create', 'POST users (web.users.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:52:47', '2026-04-21 08:52:47', '2026-04-21 08:52:47'),
(198, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: manage_employees', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:58:22', '2026-04-21 08:58:22', '2026-04-21 08:58:22'),
(199, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:58:22', '2026-04-21 08:58:22', '2026-04-21 08:58:22'),
(200, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\'', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:58:56', '2026-04-21 08:58:56', '2026-04-21 08:58:56'),
(201, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:58:56', '2026-04-21 08:58:56', '2026-04-21 08:58:56'),
(202, 4, 'web:users:store', 'create', 'POST users (web.users.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:59:10', '2026-04-21 08:59:10', '2026-04-21 08:59:10'),
(203, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Thu hoi quyen: manage_employees', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:59:42', '2026-04-21 08:59:42', '2026-04-21 08:59:42'),
(204, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 08:59:42', '2026-04-21 08:59:42', '2026-04-21 08:59:42'),
(205, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: approve_department_change', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:00:12', '2026-04-21 09:00:12', '2026-04-21 09:00:12'),
(206, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:00:12', '2026-04-21 09:00:12', '2026-04-21 09:00:12'),
(207, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: view_departments', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:01:07', '2026-04-21 09:01:07', '2026-04-21 09:01:07'),
(208, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:01:07', '2026-04-21 09:01:07', '2026-04-21 09:01:07'),
(209, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: manage_departments', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:01:30', '2026-04-21 09:01:30', '2026-04-21 09:01:30'),
(210, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:01:30', '2026-04-21 09:01:30', '2026-04-21 09:01:30'),
(211, 4, 'departments:toggle', 'update', 'PUT departments/1/toggle (departments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:01:49', '2026-04-21 09:01:49', '2026-04-21 09:01:49'),
(212, 1, 'web:department-approvals:reject', 'create', 'POST departments/approvals/6/reject (web.department-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:02:08', '2026-04-21 09:02:08', '2026-04-21 09:02:08'),
(213, 4, 'departments:update', 'update', 'PUT departments/1 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:02:36', '2026-04-21 09:02:36', '2026-04-21 09:02:36'),
(214, 1, 'web:department-approvals:reject', 'create', 'POST departments/approvals/7/reject (web.department-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:02:45', '2026-04-21 09:02:45', '2026-04-21 09:02:45'),
(215, 1, 'positions', 'update', 'Cap nhat chuc vu \'Lap trinh vien\': Cap them quyen: manage_employees', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:07:33', '2026-04-21 09:07:33', '2026-04-21 09:07:33'),
(216, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 09:07:33', '2026-04-21 09:07:33', '2026-04-21 09:07:33'),
(217, 4, 'attendance', 'check_out', 'Check out attendance record #6', 'attendance_records', 6, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 10:32:49', '2026-04-21 10:32:49', '2026-04-21 10:32:49'),
(218, 4, 'attendance:check-out', 'create', 'POST attendance/check-out (attendance.check-out)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 10:32:49', '2026-04-21 10:32:49', '2026-04-21 10:32:49'),
(219, 1, 'attendance', 'confirm', 'Confirm attendance record #6', 'attendance_records', 6, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 10:33:11', '2026-04-21 10:33:11', '2026-04-21 10:33:11'),
(220, 1, 'attendance:confirm', 'create', 'POST attendance/6/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 10:33:11', '2026-04-21 10:33:11', '2026-04-21 10:33:11'),
(221, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mat khau', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 01:05:48', '2026-04-22 01:05:48', '2026-04-22 01:05:48'),
(222, 4, 'attendance', 'check_in', 'Check in attendance record #7', 'attendance_records', 7, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 01:05:57', '2026-04-22 01:05:57', '2026-04-22 01:05:57'),
(223, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 01:05:57', '2026-04-22 01:05:57', '2026-04-22 01:05:57'),
(224, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 01:07:27', '2026-04-22 01:07:27', '2026-04-22 01:07:27'),
(225, 1, 'web:users:update', 'update', 'PUT users/3 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:02:47', '2026-04-22 03:02:47', '2026-04-22 03:02:47'),
(226, 1, 'web:users:update', 'update', 'PUT users/3 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:02:59', '2026-04-22 03:02:59', '2026-04-22 03:02:59'),
(227, 1, 'users', 'update', 'Cap nhat tai khoan employee1@gmail.com', 'users', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:03:07', '2026-04-22 03:03:07', '2026-04-22 03:03:07'),
(228, 1, 'web:users:update', 'update', 'PUT users/3 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:03:07', '2026-04-22 03:03:07', '2026-04-22 03:03:07'),
(229, NULL, 'attendance', 'mark_absent', 'Auto mark absent attendance record #8', 'attendance_records', 8, '127.0.0.1', 'Unknown device', 'Symfony', '2026-04-22 03:11:55', '2026-04-22 03:11:55', '2026-04-22 03:11:55'),
(230, NULL, 'attendance', 'mark_absent', 'Auto mark absent attendance record #9', 'attendance_records', 9, '127.0.0.1', 'Unknown device', 'Symfony', '2026-04-22 03:11:55', '2026-04-22 03:11:55', '2026-04-22 03:11:55'),
(231, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:14:00', '2026-04-22 03:14:00', '2026-04-22 03:14:00'),
(232, 1, 'my-profile:avatar', 'create', 'POST my-profile/avatar (my-profile.avatar)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:46:41', '2026-04-22 03:46:41', '2026-04-22 03:46:41'),
(233, 1, 'my-profile:update', 'update', 'PUT my-profile (my-profile.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:49:59', '2026-04-22 03:49:59', '2026-04-22 03:49:59'),
(234, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 03:52:06', '2026-04-22 03:52:06', '2026-04-22 03:52:06'),
(235, 1, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:29:39', '2026-04-22 04:29:39', '2026-04-22 04:29:39'),
(236, 1, 'attendance', 'submit_adjustment_request', 'Submit attendance adjustment #4: on_time/present, worked 570, late 0, early 0, overtime 0', 'attendance_adjustments', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:30:47', '2026-04-22 04:30:47', '2026-04-22 04:30:47'),
(237, 1, 'attendance:adjustments:store', 'create', 'POST attendance/adjustments (attendance.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:30:47', '2026-04-22 04:30:47', '2026-04-22 04:30:47'),
(238, 1, 'attendance:adjustments:approve', 'create', 'POST attendance/adjustments/4/approve (attendance.adjustments.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:31:10', '2026-04-22 04:31:10', '2026-04-22 04:31:10'),
(239, 1, 'attendance:adjustments:approve', 'create', 'POST attendance/adjustments/4/approve (attendance.adjustments.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:31:21', '2026-04-22 04:31:21', '2026-04-22 04:31:21'),
(240, 1, 'attendance:adjustments:reject', 'create', 'POST attendance/adjustments/4/reject (attendance.adjustments.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:31:42', '2026-04-22 04:31:42', '2026-04-22 04:31:42'),
(241, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:33:12', '2026-04-22 04:33:12', '2026-04-22 04:33:12'),
(242, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:33:15', '2026-04-22 04:33:15', '2026-04-22 04:33:15'),
(243, 1, 'attendance', 'submit_request', 'Submit attendance request #2', 'attendance_requests', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:39:06', '2026-04-22 04:39:06', '2026-04-22 04:39:06'),
(244, 1, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:39:06', '2026-04-22 04:39:06', '2026-04-22 04:39:06'),
(245, 1, 'attendance:adjustments:reject', 'create', 'POST attendance/adjustments/4/reject (attendance.adjustments.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:39:29', '2026-04-22 04:39:29', '2026-04-22 04:39:29'),
(246, 4, 'attendance', 'submit_request', 'Submit attendance request #3', 'attendance_requests', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:39:53', '2026-04-22 04:39:53', '2026-04-22 04:39:53'),
(247, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:39:53', '2026-04-22 04:39:53', '2026-04-22 04:39:53'),
(248, 1, 'attendance', 'review_request_rejected', 'Review approval request #10', 'approval_requests', 10, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:40:22', '2026-04-22 04:40:22', '2026-04-22 04:40:22'),
(249, 1, 'attendance:request-approvals:reject', 'create', 'POST attendance/request-approvals/10/reject (attendance.request-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:40:22', '2026-04-22 04:40:22', '2026-04-22 04:40:22'),
(250, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:45:42', '2026-04-22 04:45:42', '2026-04-22 04:45:42'),
(251, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:46:09', '2026-04-22 04:46:09', '2026-04-22 04:46:09'),
(252, 1, 'attendance:adjustments:approve', 'create', 'POST attendance/adjustments/4/approve (attendance.adjustments.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 04:56:10', '2026-04-22 04:56:10', '2026-04-22 04:56:10'),
(253, 1, 'attendance:adjustments:approve', 'create', 'POST attendance/adjustments/4/approve (attendance.adjustments.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:28:21', '2026-04-22 06:28:21', '2026-04-22 06:28:21'),
(254, 1, 'attendance', 'check_in', 'Check in attendance record #10', 'attendance_records', 10, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:35:22', '2026-04-22 06:35:22', '2026-04-22 06:35:22'),
(255, 1, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:35:22', '2026-04-22 06:35:22', '2026-04-22 06:35:22'),
(256, 1, 'attendance', 'submit_request', 'Submit attendance request #4', 'attendance_requests', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:40:51', '2026-04-22 06:40:51', '2026-04-22 06:40:51'),
(257, 1, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:40:51', '2026-04-22 06:40:51', '2026-04-22 06:40:51'),
(258, 1, 'attendance', 'check_out', 'Check out attendance record #10', 'attendance_records', 10, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:44:49', '2026-04-22 06:44:49', '2026-04-22 06:44:49'),
(259, 1, 'attendance:check-out', 'create', 'POST attendance/check-out (attendance.check-out)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 06:44:49', '2026-04-22 06:44:49', '2026-04-22 06:44:49'),
(260, 1, 'positions:authority-levels:store', 'create', 'POST positions/authority-levels (positions.authority-levels.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:11:27', '2026-04-22 08:11:27', '2026-04-22 08:11:27'),
(261, 1, 'positions:authority-levels:store', 'create', 'POST positions/authority-levels (positions.authority-levels.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:12:33', '2026-04-22 08:12:33', '2026-04-22 08:12:33'),
(262, 1, 'positions:authority-levels:store', 'create', 'POST positions/authority-levels (positions.authority-levels.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:13:16', '2026-04-22 08:13:16', '2026-04-22 08:13:16'),
(263, 1, 'positions:authority-levels:toggle', 'update', 'PUT positions/authority-levels/11/toggle (positions.authority-levels.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:13:28', '2026-04-22 08:13:28', '2026-04-22 08:13:28'),
(264, 1, 'positions:authority-levels:store', 'create', 'POST positions/authority-levels (positions.authority-levels.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:14:16', '2026-04-22 08:14:16', '2026-04-22 08:14:16'),
(265, 1, 'positions', 'update', 'Cap nhat chuc vu \'Truong phong HR\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:37:12', '2026-04-22 08:37:12', '2026-04-22 08:37:12'),
(266, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:37:12', '2026-04-22 08:37:12', '2026-04-22 08:37:12'),
(267, 1, 'positions', 'update', 'Cap nhat chuc vu \'Truong phong HR\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:37:37', '2026-04-22 08:37:37', '2026-04-22 08:37:37'),
(268, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:37:37', '2026-04-22 08:37:37', '2026-04-22 08:37:37'),
(269, 1, 'positions', 'update', 'Cap nhat chuc vu \'Truong phong HR\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:37:45', '2026-04-22 08:37:45', '2026-04-22 08:37:45'),
(270, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 08:37:45', '2026-04-22 08:37:45', '2026-04-22 08:37:45'),
(271, 1, 'attendance', 'reject', 'Reject attendance record #8', 'attendance_records', 8, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:03:10', '2026-04-22 09:03:10', '2026-04-22 09:03:10'),
(272, 1, 'attendance:reject', 'create', 'POST attendance/8/reject (attendance.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:03:10', '2026-04-22 09:03:10', '2026-04-22 09:03:10'),
(273, 1, 'attendance:request-approvals:reject', 'create', 'POST attendance/request-approvals/9/reject (attendance.request-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:04:08', '2026-04-22 09:04:08', '2026-04-22 09:04:08'),
(274, 1, 'attendance:request-approvals:reject', 'create', 'POST attendance/request-approvals/9/reject (attendance.request-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:04:10', '2026-04-22 09:04:10', '2026-04-22 09:04:10'),
(275, 1, 'attendance:request-approvals:reject', 'create', 'POST attendance/request-approvals/11/reject (attendance.request-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:04:17', '2026-04-22 09:04:17', '2026-04-22 09:04:17'),
(276, 1, 'attendance:reject', 'create', 'POST attendance/10/reject (attendance.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:15:37', '2026-04-22 09:15:37', '2026-04-22 09:15:37'),
(277, 1, 'attendance:reject', 'create', 'POST attendance/10/reject (attendance.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:17:09', '2026-04-22 09:17:09', '2026-04-22 09:17:09'),
(278, 1, 'attendance:reject', 'create', 'POST attendance/10/reject (attendance.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:17:21', '2026-04-22 09:17:21', '2026-04-22 09:17:21'),
(279, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:18:24', '2026-04-22 09:18:24', '2026-04-22 09:18:24'),
(280, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:18:42', '2026-04-22 09:18:42', '2026-04-22 09:18:42'),
(281, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:18:53', '2026-04-22 09:18:53', '2026-04-22 09:18:53'),
(282, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:18:59', '2026-04-22 09:18:59', '2026-04-22 09:18:59'),
(283, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/1 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:19:25', '2026-04-22 09:19:25', '2026-04-22 09:19:25'),
(284, 1, 'salary:company:adjustments:destroy', 'delete', 'DELETE salary/company/adjustments/2 (salary.company.adjustments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:21:37', '2026-04-22 09:21:37', '2026-04-22 09:21:37'),
(285, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:22:21', '2026-04-22 09:22:21', '2026-04-22 09:22:21'),
(286, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:23:11', '2026-04-22 09:23:11', '2026-04-22 09:23:11'),
(287, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:23:20', '2026-04-22 09:23:20', '2026-04-22 09:23:20'),
(288, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:24:00', '2026-04-22 09:24:00', '2026-04-22 09:24:00'),
(289, 1, 'salary:company:adjustments:destroy', 'delete', 'DELETE salary/company/adjustments/4 (salary.company.adjustments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:24:23', '2026-04-22 09:24:23', '2026-04-22 09:24:23'),
(290, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:24:27', '2026-04-22 09:24:27', '2026-04-22 09:24:27'),
(291, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:24:36', '2026-04-22 09:24:36', '2026-04-22 09:24:36'),
(292, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:24:36', '2026-04-22 09:24:36', '2026-04-22 09:24:36'),
(293, 1, 'salary:company:adjustments:destroy', 'delete', 'DELETE salary/company/adjustments/6 (salary.company.adjustments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:25:05', '2026-04-22 09:25:05', '2026-04-22 09:25:05'),
(294, 1, 'salary:company:adjustments:destroy', 'delete', 'DELETE salary/company/adjustments/5 (salary.company.adjustments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:25:07', '2026-04-22 09:25:07', '2026-04-22 09:25:07'),
(295, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:25:27', '2026-04-22 09:25:27', '2026-04-22 09:25:27'),
(296, 1, 'attendance', 'lock_month', 'Lock attendance month 4/2026', 'attendance_month_locks', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:32:06', '2026-04-22 09:32:06', '2026-04-22 09:32:06'),
(297, 1, 'attendance:month-locks:lock', 'create', 'POST attendance/month-locks/lock (attendance.month-locks.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:32:06', '2026-04-22 09:32:06', '2026-04-22 09:32:06'),
(298, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:33:40', '2026-04-22 09:33:40', '2026-04-22 09:33:40'),
(299, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:34:15', '2026-04-22 09:34:15', '2026-04-22 09:34:15'),
(300, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:34:55', '2026-04-22 09:34:55', '2026-04-22 09:34:55'),
(301, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 09:35:09', '2026-04-22 09:35:09', '2026-04-22 09:35:09'),
(302, 1, 'attendance', 'unlock_month', 'Unlock attendance month 4/2026', 'attendance_month_locks', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 10:00:01', '2026-04-22 10:00:01', '2026-04-22 10:00:01'),
(303, 1, 'attendance:month-locks:unlock', 'create', 'POST attendance/month-locks/unlock (attendance.month-locks.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 10:00:01', '2026-04-22 10:00:01', '2026-04-22 10:00:01');
INSERT INTO `activity_logs` (`id`, `user_id`, `module`, `action`, `description`, `reference_table`, `reference_id`, `ip_address`, `device`, `user_agent`, `occurred_at`, `created_at`, `updated_at`) VALUES
(304, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:14:36', '2026-04-23 01:14:36', '2026-04-23 01:14:36'),
(305, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mat khau', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:15:24', '2026-04-23 01:15:24', '2026-04-23 01:15:24'),
(306, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:16:27', '2026-04-23 01:16:27', '2026-04-23 01:16:27'),
(307, 4, 'attendance', 'submit_request', 'Submit attendance request #5', 'attendance_requests', 5, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:19:31', '2026-04-23 01:19:31', '2026-04-23 01:19:31'),
(308, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:19:31', '2026-04-23 01:19:31', '2026-04-23 01:19:31'),
(309, 1, 'attendance', 'apply_attendance_formula', 'Apply forgot_check request #5 to record #7: forgot_check => missing_check_in:0->0, missing_check_out:1->0', 'attendance_records', 7, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:20:02', '2026-04-23 01:20:02', '2026-04-23 01:20:02'),
(310, 1, 'attendance', 'review_request_approved', 'Review approval request #12', 'approval_requests', 12, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:20:03', '2026-04-23 01:20:03', '2026-04-23 01:20:03'),
(311, 1, 'attendance:request-approvals:approve', 'create', 'POST attendance/request-approvals/12/approve (attendance.request-approvals.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:20:03', '2026-04-23 01:20:03', '2026-04-23 01:20:03'),
(312, 1, 'salary:company:lock', 'create', 'POST salary/company/lock (salary.company.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:35:20', '2026-04-23 01:35:20', '2026-04-23 01:35:20'),
(313, 1, 'salary:company:adjustments:store', 'create', 'POST salary/company/adjustments (salary.company.adjustments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:35:35', '2026-04-23 01:35:35', '2026-04-23 01:35:35'),
(314, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:51:45', '2026-04-23 01:51:45', '2026-04-23 01:51:45'),
(315, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:53:22', '2026-04-23 01:53:22', '2026-04-23 01:53:22'),
(316, 1, 'attendance', 'reject', 'Reject attendance record #9', 'attendance_records', 9, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:58:39', '2026-04-23 01:58:39', '2026-04-23 01:58:39'),
(317, 1, 'attendance:reject', 'create', 'POST attendance/9/reject (attendance.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 01:58:39', '2026-04-23 01:58:39', '2026-04-23 01:58:39'),
(318, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:01:38', '2026-04-23 02:01:38', '2026-04-23 02:01:38'),
(319, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:01:40', '2026-04-23 02:01:40', '2026-04-23 02:01:40'),
(320, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:03:33', '2026-04-23 02:03:33', '2026-04-23 02:03:33'),
(321, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/1 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:25:36', '2026-04-23 02:25:36', '2026-04-23 02:25:36'),
(322, 4, 'attendance', 'submit_request', 'Submit attendance request #6', 'attendance_requests', 6, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:28:35', '2026-04-23 02:28:35', '2026-04-23 02:28:35'),
(323, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:28:35', '2026-04-23 02:28:35', '2026-04-23 02:28:35'),
(324, 1, 'attendance', 'apply_attendance_formula', 'Apply leave request #6 to record #11: leave => worked=0, late=0, early=0, status=absent, day=leave', 'attendance_records', 11, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:29:03', '2026-04-23 02:29:03', '2026-04-23 02:29:03'),
(325, 1, 'attendance', 'review_request_approved', 'Review approval request #13', 'approval_requests', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:29:03', '2026-04-23 02:29:03', '2026-04-23 02:29:03'),
(326, 1, 'attendance:request-approvals:approve', 'create', 'POST attendance/request-approvals/13/approve (attendance.request-approvals.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:29:03', '2026-04-23 02:29:03', '2026-04-23 02:29:03'),
(327, 1, 'salary:company:unlock', 'create', 'POST salary/company/unlock (salary.company.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 02:40:24', '2026-04-23 02:40:24', '2026-04-23 02:40:24'),
(328, 1, 'my-profile:avatar', 'create', 'POST my-profile/avatar (my-profile.avatar)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 03:19:22', '2026-04-23 03:19:22', '2026-04-23 03:19:22'),
(329, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 03:52:34', '2026-04-23 03:52:34', '2026-04-23 03:52:34'),
(330, 1, 'feedbacks:read', 'create', 'POST feedbacks/3/read (feedbacks.read)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 04:02:29', '2026-04-23 04:02:29', '2026-04-23 04:02:29'),
(331, 1, 'feedbacks:reply', 'create', 'POST feedbacks/3/reply (feedbacks.reply)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 04:03:06', '2026-04-23 04:03:06', '2026-04-23 04:03:06'),
(332, 1, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 04:06:31', '2026-04-23 04:06:31', '2026-04-23 04:06:31'),
(333, 4, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 04:23:07', '2026-04-23 04:23:07', '2026-04-23 04:23:07'),
(334, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:11:36', '2026-04-23 08:11:36', '2026-04-23 08:11:36'),
(335, 4, 'attendance', 'submit_request', 'Submit attendance request #7', 'attendance_requests', 7, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:11:42', '2026-04-23 08:11:42', '2026-04-23 08:11:42'),
(336, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:11:42', '2026-04-23 08:11:42', '2026-04-23 08:11:42'),
(337, 4, 'attendance:requests:destroy', 'delete', 'DELETE attendance/requests/14 (attendance.requests.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:15:32', '2026-04-23 08:15:32', '2026-04-23 08:15:32'),
(338, 4, 'attendance:requests:destroy', 'delete', 'DELETE attendance/requests/14 (attendance.requests.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:15:39', '2026-04-23 08:15:39', '2026-04-23 08:15:39'),
(339, 4, 'attendance:requests:destroy', 'delete', 'DELETE attendance/requests/14 (attendance.requests.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:20:11', '2026-04-23 08:20:11', '2026-04-23 08:20:11'),
(340, 4, 'attendance:requests:destroy', 'delete', 'DELETE attendance/requests/14 (attendance.requests.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:20:19', '2026-04-23 08:20:19', '2026-04-23 08:20:19'),
(341, 4, 'attendance', 'cancel_request', 'Cancel approval request #14', 'approval_requests', 14, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:21:49', '2026-04-23 08:21:49', '2026-04-23 08:21:49'),
(342, 4, 'attendance:requests:destroy', 'delete', 'DELETE attendance/requests/14 (attendance.requests.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:21:49', '2026-04-23 08:21:49', '2026-04-23 08:21:49'),
(343, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Doi ten tu \'Lap trinh vien\' sang \'hR\'', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:44:16', '2026-04-23 09:44:16', '2026-04-23 09:44:16'),
(344, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:44:16', '2026-04-23 09:44:16', '2026-04-23 09:44:16'),
(345, 1, 'positions', 'update', 'Cap nhat chuc vu \'Nhân viên\': Doi ten tu \'Truong phong HR\' sang \'Nhân viên\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:44:30', '2026-04-23 09:44:30', '2026-04-23 09:44:30'),
(346, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:44:30', '2026-04-23 09:44:30', '2026-04-23 09:44:30'),
(347, 1, 'positions', 'update', 'Cap nhat chuc vu \'Nhân viên\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:44:37', '2026-04-23 09:44:37', '2026-04-23 09:44:37'),
(348, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:44:37', '2026-04-23 09:44:37', '2026-04-23 09:44:37'),
(349, 4, 'web:users:update', 'update', 'PUT users/2 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:47:12', '2026-04-23 09:47:12', '2026-04-23 09:47:12'),
(350, 4, 'web:users:update', 'update', 'PUT users/2 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:47:19', '2026-04-23 09:47:19', '2026-04-23 09:47:19'),
(351, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Cap them quyen: manage_salary, approve_attendance, view_all_attendance, export_attendance, approve_leave, manage_leave_policy, approve_requests, lock_attendance_month, unlock_attendance_month, approve_update_employee, lock_employee, create_employee', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:49:10', '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(352, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:49:10', '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(353, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Thu hoi quyen: manage_leave_policy', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:50:06', '2026-04-23 09:50:06', '2026-04-23 09:50:06'),
(354, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:50:06', '2026-04-23 09:50:06', '2026-04-23 09:50:06'),
(355, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Thu hoi quyen: approve_department_change', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:54:07', '2026-04-23 09:54:07', '2026-04-23 09:54:07'),
(356, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:54:07', '2026-04-23 09:54:07', '2026-04-23 09:54:07'),
(357, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Thu hoi quyen: manage_departments', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:54:23', '2026-04-23 09:54:23', '2026-04-23 09:54:23'),
(358, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:54:23', '2026-04-23 09:54:23', '2026-04-23 09:54:23'),
(359, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Cap them quyen: manage_departments', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:55:19', '2026-04-23 09:55:19', '2026-04-23 09:55:19'),
(360, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:55:19', '2026-04-23 09:55:19', '2026-04-23 09:55:19'),
(361, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Cap them quyen: request_department_change', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:56:05', '2026-04-23 09:56:05', '2026-04-23 09:56:05'),
(362, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:56:05', '2026-04-23 09:56:05', '2026-04-23 09:56:05'),
(363, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Thu hoi quyen: manage_departments', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:56:31', '2026-04-23 09:56:31', '2026-04-23 09:56:31'),
(364, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:56:31', '2026-04-23 09:56:31', '2026-04-23 09:56:31'),
(365, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Cap them quyen: manage_departments', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:58:47', '2026-04-23 09:58:47', '2026-04-23 09:58:47'),
(366, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 09:58:47', '2026-04-23 09:58:47', '2026-04-23 09:58:47'),
(367, 1, 'positions', 'update', 'Cap nhat chuc vu \'hR\': Thu hoi quyen: approve_requests', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:01:10', '2026-04-23 10:01:10', '2026-04-23 10:01:10'),
(368, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:01:10', '2026-04-23 10:01:10', '2026-04-23 10:01:10'),
(369, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:07:10', '2026-04-23 10:07:10', '2026-04-23 10:07:10'),
(370, 1, 'leave', 'update_leave_type', 'Update leave type #2', 'leave_types', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:10:24', '2026-04-23 10:10:24', '2026-04-23 10:10:24'),
(371, 1, 'leave-management:types:update', 'update', 'PUT leave-management/types/2 (leave-management.types.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:10:24', '2026-04-23 10:10:24', '2026-04-23 10:10:24'),
(372, 1, 'leave', 'update_leave_type', 'Update leave type #4', 'leave_types', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:10:43', '2026-04-23 10:10:43', '2026-04-23 10:10:43'),
(373, 1, 'leave-management:types:update', 'update', 'PUT leave-management/types/4 (leave-management.types.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 10:10:43', '2026-04-23 10:10:43', '2026-04-23 10:10:43'),
(374, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:06:31', '2026-04-24 01:06:31', '2026-04-24 01:06:31'),
(375, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:07:44', '2026-04-24 01:07:44', '2026-04-24 01:07:44'),
(376, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:07:54', '2026-04-24 01:07:54', '2026-04-24 01:07:54'),
(377, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mat khau', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:10:41', '2026-04-24 01:10:41', '2026-04-24 01:10:41'),
(378, 4, 'web:users:update', 'update', 'PUT users/2 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:18:25', '2026-04-24 01:18:25', '2026-04-24 01:18:25'),
(379, 4, 'users', 'update', 'Cap nhat tai khoan hr@gmail.com', 'users', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:18:33', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(380, 4, 'web:users:update', 'update', 'PUT users/2 (web.users.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:18:33', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(381, 1, 'web:user-approvals:approve', 'create', 'POST users/approvals/15/approve (web.user-approvals.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:29:32', '2026-04-24 01:29:32', '2026-04-24 01:29:32'),
(382, 4, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 01:31:58', '2026-04-24 01:31:58', '2026-04-24 01:31:58'),
(383, 4, 'departments:update', 'update', 'PUT departments/1 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:38:02', '2026-04-24 02:38:02', '2026-04-24 02:38:02'),
(384, 1, 'web:department-approvals:reject', 'create', 'POST departments/approvals/16/reject (web.department-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:38:30', '2026-04-24 02:38:30', '2026-04-24 02:38:30'),
(385, 4, 'api', 'create', 'POST api/notifications/38/mark-as-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:43:36', '2026-04-24 02:43:36', '2026-04-24 02:43:36'),
(386, 1, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:44:03', '2026-04-24 02:44:03', '2026-04-24 02:44:03'),
(387, 4, 'departments:toggle', 'update', 'PUT departments/1/toggle (departments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:47:03', '2026-04-24 02:47:03', '2026-04-24 02:47:03'),
(388, 1, 'web:department-approvals:reject', 'create', 'POST departments/approvals/17/reject (web.department-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:47:47', '2026-04-24 02:47:47', '2026-04-24 02:47:47'),
(389, 1, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:48:39', '2026-04-24 02:48:39', '2026-04-24 02:48:39'),
(390, 4, 'api', 'create', 'POST api/notifications/40/mark-as-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 02:52:08', '2026-04-24 02:52:08', '2026-04-24 02:52:08'),
(391, 4, 'attendance', 'check_in', 'Check in attendance record #12', 'attendance_records', 12, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:16:43', '2026-04-24 03:16:43', '2026-04-24 03:16:43'),
(392, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:16:43', '2026-04-24 03:16:43', '2026-04-24 03:16:43'),
(393, 4, 'web:users:salary', 'update', 'PUT users/2/salary (web.users.salary)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:22:07', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(394, 1, 'api', 'create', 'POST api/notifications/41/mark-as-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:22:14', '2026-04-24 03:22:14', '2026-04-24 03:22:14'),
(395, 1, 'web:user-approvals:reject', 'create', 'POST users/approvals/18/reject (web.user-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:23:30', '2026-04-24 03:23:30', '2026-04-24 03:23:30'),
(396, 4, 'web:users:salary', 'update', 'PUT users/2/salary (web.users.salary)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:23:50', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(397, 1, 'web:user-approvals:reject', 'create', 'POST users/approvals/19/reject (web.user-approvals.reject)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:25:11', '2026-04-24 03:25:11', '2026-04-24 03:25:11'),
(398, 1, 'auth', 'logout', 'Dang xuat khoi he thong', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:32:35', '2026-04-24 03:32:35', '2026-04-24 03:32:35'),
(399, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:32:59', '2026-04-24 03:32:59', '2026-04-24 03:32:59'),
(400, 1, 'salary:company:adjustments:destroy', 'delete', 'DELETE salary/company/adjustments/7 (salary.company.adjustments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:33:45', '2026-04-24 03:33:45', '2026-04-24 03:33:45'),
(401, 1, 'users', 'update_account_status', 'Cap nhat trang thai tai khoan dophuhieu15@gmail.com sang blocked', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:22', '2026-04-24 03:34:22', '2026-04-24 03:34:22'),
(402, 1, 'web:users:account-status', 'update', 'PUT users/4/account-status (web.users.account-status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:22', '2026-04-24 03:34:22', '2026-04-24 03:34:22'),
(403, 1, 'users', 'update_account_status', 'Cap nhat trang thai tai khoan dophuhieu15@gmail.com sang active', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:26', '2026-04-24 03:34:26', '2026-04-24 03:34:26'),
(404, 1, 'web:users:account-status', 'update', 'PUT users/4/account-status (web.users.account-status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:26', '2026-04-24 03:34:26', '2026-04-24 03:34:26'),
(405, 1, 'users', 'update_employment_status', 'Cap nhat trang thai lam viec dophuhieu15@gmail.com sang terminated', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:33', '2026-04-24 03:34:33', '2026-04-24 03:34:33'),
(406, 1, 'web:users:employment-status', 'update', 'PUT users/4/employment-status (web.users.employment-status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:33', '2026-04-24 03:34:33', '2026-04-24 03:34:33'),
(407, 1, 'users', 'update_employment_status', 'Cap nhat trang thai lam viec dophuhieu15@gmail.com sang active', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:36', '2026-04-24 03:34:36', '2026-04-24 03:34:36'),
(408, 1, 'web:users:employment-status', 'update', 'PUT users/4/employment-status (web.users.employment-status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:36', '2026-04-24 03:34:36', '2026-04-24 03:34:36'),
(409, 1, 'users', 'update_account_status', 'Cap nhat trang thai tai khoan dophuhieu15@gmail.com sang active', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:41', '2026-04-24 03:34:41', '2026-04-24 03:34:41'),
(410, 1, 'web:users:account-status', 'update', 'PUT users/4/account-status (web.users.account-status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:34:41', '2026-04-24 03:34:41', '2026-04-24 03:34:41'),
(411, 1, 'attendance', 'lock_month', 'Lock attendance month 4/2026', 'attendance_month_locks', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:44:59', '2026-04-24 03:44:59', '2026-04-24 03:44:59'),
(412, 1, 'attendance:month-locks:lock', 'create', 'POST attendance/month-locks/lock (attendance.month-locks.lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:44:59', '2026-04-24 03:44:59', '2026-04-24 03:44:59'),
(413, 1, 'attendance', 'unlock_month', 'Unlock attendance month 4/2026', 'attendance_month_locks', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:45:02', '2026-04-24 03:45:02', '2026-04-24 03:45:02'),
(414, 1, 'attendance:month-locks:unlock', 'create', 'POST attendance/month-locks/unlock (attendance.month-locks.unlock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 03:45:02', '2026-04-24 03:45:02', '2026-04-24 03:45:02'),
(415, 4, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:38:57', '2026-04-24 07:38:57', '2026-04-24 07:38:57'),
(416, 1, 'auth', 'logout', 'Dang xuat khoi hệ thống', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:03:30', '2026-04-24 08:03:30', '2026-04-24 08:03:30'),
(417, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:03:58', '2026-04-24 08:03:58', '2026-04-24 08:03:58'),
(418, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:19:18', '2026-04-24 08:19:18', '2026-04-24 08:19:18'),
(419, 1, 'projects:attachments:store', 'create', 'POST projects/1/attachments (projects.attachments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:56:52', '2026-04-24 08:56:52', '2026-04-24 08:56:52'),
(420, 4, 'attendance', 'submit_request', 'Submit attendance request #8', 'attendance_requests', 8, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:10:51', '2026-04-24 09:10:51', '2026-04-24 09:10:51'),
(421, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:10:51', '2026-04-24 09:10:51', '2026-04-24 09:10:51'),
(422, 1, 'projects:members:update', 'update', 'PUT projects/1/members/1 (projects.members.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:13:26', '2026-04-24 09:13:26', '2026-04-24 09:13:26'),
(423, 1, 'projects:implementation-details:toggle-lock', 'update', 'PUT projects/1/implementation-details/1/toggle-lock (projects.implementation-details.toggle-lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:18:30', '2026-04-24 09:18:30', '2026-04-24 09:18:30'),
(424, 1, 'projects:implementation-details:toggle-lock', 'update', 'PUT projects/1/implementation-details/1/toggle-lock (projects.implementation-details.toggle-lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:18:37', '2026-04-24 09:18:37', '2026-04-24 09:18:37'),
(425, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:22:12', '2026-04-24 09:22:12', '2026-04-24 09:22:12'),
(426, 1, 'projects:roles:store', 'create', 'POST projects/1/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:29:00', '2026-04-24 09:29:00', '2026-04-24 09:29:00'),
(427, 1, 'projects:roles:store', 'create', 'POST projects/1/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:42:57', '2026-04-24 09:42:57', '2026-04-24 09:42:57'),
(428, 1, 'attendance', 'apply_attendance_formula', 'Apply late_early request #8 to record #4: late_early(early_leave) => late:0->0, early:15->0, status:on_time->on_time', 'attendance_records', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:53:51', '2026-04-24 09:53:51', '2026-04-24 09:53:51'),
(429, 1, 'attendance', 'review_request_approved', 'Review approval request #20', 'approval_requests', 20, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:53:51', '2026-04-24 09:53:51', '2026-04-24 09:53:51'),
(430, 1, 'attendance:request-approvals:approve', 'create', 'POST attendance/request-approvals/20/approve (attendance.request-approvals.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:53:51', '2026-04-24 09:53:51', '2026-04-24 09:53:51'),
(431, 4, 'api', 'create', 'POST api/notifications/47/mark-as-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 09:54:32', '2026-04-24 09:54:32', '2026-04-24 09:54:32'),
(432, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:00:04', '2026-04-28 01:00:04', '2026-04-28 01:00:04'),
(433, 1, 'departments', 'update', 'Cap nhat phòng ban #2', 'departments', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:01:47', '2026-04-28 01:01:47', '2026-04-28 01:01:47'),
(434, 1, 'departments:update', 'update', 'PUT departments/2 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:01:47', '2026-04-28 01:01:47', '2026-04-28 01:01:47'),
(435, 1, 'departments', 'update', 'Cap nhat phòng ban #2', 'departments', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:01:53', '2026-04-28 01:01:53', '2026-04-28 01:01:53'),
(436, 1, 'departments:update', 'update', 'PUT departments/2 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:01:53', '2026-04-28 01:01:53', '2026-04-28 01:01:53'),
(437, 1, 'departments', 'update', 'Cap nhat phòng ban #3', 'departments', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:02:04', '2026-04-28 01:02:04', '2026-04-28 01:02:04'),
(438, 1, 'departments:update', 'update', 'PUT departments/3 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:02:04', '2026-04-28 01:02:04', '2026-04-28 01:02:04'),
(439, 1, 'departments', 'update', 'Cap nhat phòng ban #1', 'departments', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:02:31', '2026-04-28 01:02:31', '2026-04-28 01:02:31'),
(440, 1, 'departments:update', 'update', 'PUT departments/1 (departments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:02:31', '2026-04-28 01:02:31', '2026-04-28 01:02:31'),
(441, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:19:07', '2026-04-28 01:19:07', '2026-04-28 01:19:07'),
(442, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:19:51', '2026-04-28 01:19:51', '2026-04-28 01:19:51'),
(443, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:19:57', '2026-04-28 01:19:57', '2026-04-28 01:19:57'),
(444, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:23:26', '2026-04-28 01:23:26', '2026-04-28 01:23:26'),
(445, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:25:55', '2026-04-28 01:25:55', '2026-04-28 01:25:55'),
(446, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:26:27', '2026-04-28 01:26:27', '2026-04-28 01:26:27'),
(447, 4, 'attendance', 'submit_request', 'Submit attendance request #9', 'attendance_requests', 9, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:27:02', '2026-04-28 01:27:02', '2026-04-28 01:27:02'),
(448, 4, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:27:02', '2026-04-28 01:27:02', '2026-04-28 01:27:02'),
(449, 1, 'attendance', 'apply_attendance_formula', 'Apply forgot_check request #9 to record #12: forgot_check => missing_check_in:0->0, missing_check_out:1->0', 'attendance_records', 12, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:27:25', '2026-04-28 01:27:25', '2026-04-28 01:27:25'),
(450, 1, 'attendance', 'review_request_approved', 'Review approval request #21', 'approval_requests', 21, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:27:25', '2026-04-28 01:27:25', '2026-04-28 01:27:25'),
(451, 1, 'attendance:request-approvals:approve', 'create', 'POST attendance/request-approvals/21/approve (attendance.request-approvals.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:27:25', '2026-04-28 01:27:25', '2026-04-28 01:27:25'),
(452, 4, 'attendance', 'check_in', 'Check in attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:28:17', '2026-04-28 01:28:17', '2026-04-28 01:28:17'),
(453, 4, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:28:17', '2026-04-28 01:28:17', '2026-04-28 01:28:17'),
(454, 1, 'users', 'create', 'Tạo tài khoản thisoma1308@gmail.com', 'users', 23, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:33:46', '2026-04-28 01:33:46', '2026-04-28 01:33:46'),
(455, 1, 'web:users:store', 'create', 'POST users (web.users.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:33:46', '2026-04-28 01:33:46', '2026-04-28 01:33:46'),
(456, 23, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 23, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 01:35:20', '2026-04-28 01:35:20', '2026-04-28 01:35:20'),
(457, 23, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 23, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 01:36:47', '2026-04-28 01:36:47', '2026-04-28 01:36:47'),
(458, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: view_dashboard', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:37:23', '2026-04-28 01:37:23', '2026-04-28 01:37:23'),
(459, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:37:23', '2026-04-28 01:37:23', '2026-04-28 01:37:23'),
(460, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: update_own_profile, view_own_profile, check_in, check_out, view_salary; Thu hoi quyen: export_attendance, view_all_projects', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:39:00', '2026-04-28 01:39:00', '2026-04-28 01:39:00');
INSERT INTO `activity_logs` (`id`, `user_id`, `module`, `action`, `description`, `reference_table`, `reference_id`, `ip_address`, `device`, `user_agent`, `occurred_at`, `created_at`, `updated_at`) VALUES
(461, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:39:00', '2026-04-28 01:39:00', '2026-04-28 01:39:00'),
(462, 23, 'attendance', 'check_in', 'Check in attendance record #14', 'attendance_records', 14, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 01:39:19', '2026-04-28 01:39:19', '2026-04-28 01:39:19'),
(463, 23, 'attendance:check-in', 'create', 'POST attendance/check-in (attendance.check-in)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 01:39:19', '2026-04-28 01:39:19', '2026-04-28 01:39:19'),
(464, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Thu hoi quyen: view_salary', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:39:42', '2026-04-28 01:39:42', '2026-04-28 01:39:42'),
(465, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:39:42', '2026-04-28 01:39:42', '2026-04-28 01:39:42'),
(466, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: view_salary', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:40:05', '2026-04-28 01:40:05', '2026-04-28 01:40:05'),
(467, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:40:05', '2026-04-28 01:40:05', '2026-04-28 01:40:05'),
(468, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: view_own_attendance', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:40:46', '2026-04-28 01:40:46', '2026-04-28 01:40:46'),
(469, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:40:46', '2026-04-28 01:40:46', '2026-04-28 01:40:46'),
(470, 1, 'projects:members:store', 'create', 'POST projects/1/members (projects.members.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:42:18', '2026-04-28 01:42:18', '2026-04-28 01:42:18'),
(471, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: view_own_salary, view_all_projects', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:43:53', '2026-04-28 01:43:53', '2026-04-28 01:43:53'),
(472, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:43:53', '2026-04-28 01:43:53', '2026-04-28 01:43:53'),
(473, 1, 'projects:members:destroy', 'delete', 'DELETE projects/1/members/2 (projects.members.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 01:44:16', '2026-04-28 01:44:16', '2026-04-28 01:44:16'),
(474, 23, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 01:51:55', '2026-04-28 01:51:55', '2026-04-28 01:51:55'),
(475, 4, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:10:28', '2026-04-28 02:10:28', '2026-04-28 02:10:28'),
(476, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Thu hoi quyen: view_all_projects', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:16:33', '2026-04-28 02:16:33', '2026-04-28 02:16:33'),
(477, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:16:33', '2026-04-28 02:16:33', '2026-04-28 02:16:33'),
(478, 1, 'projects:members:store', 'create', 'POST projects/1/members (projects.members.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:17:07', '2026-04-28 02:17:07', '2026-04-28 02:17:07'),
(479, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: view_own_leave_requests, view_own_projects', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:18:00', '2026-04-28 02:18:00', '2026-04-28 02:18:00'),
(480, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:18:00', '2026-04-28 02:18:00', '2026-04-28 02:18:00'),
(481, 1, 'projects:roles:update', 'update', 'PUT projects/1/roles/3 (projects.roles.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:22:36', '2026-04-28 02:22:36', '2026-04-28 02:22:36'),
(482, 1, 'auth', 'logout', 'Dang xuat khoi hệ thống', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:32:19', '2026-04-28 02:32:19', '2026-04-28 02:32:19'),
(483, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 02:34:06', '2026-04-28 02:34:06', '2026-04-28 02:34:06'),
(484, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 06:27:30', '2026-04-28 06:27:30', '2026-04-28 06:27:30'),
(485, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 06:52:52', '2026-04-28 06:52:52', '2026-04-28 06:52:52'),
(486, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:01:49', '2026-04-28 07:01:49', '2026-04-28 07:01:49'),
(487, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:06:17', '2026-04-28 07:06:17', '2026-04-28 07:06:17'),
(488, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:12:20', '2026-04-28 07:12:20', '2026-04-28 07:12:20'),
(489, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:12:27', '2026-04-28 07:12:27', '2026-04-28 07:12:27'),
(490, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:12:34', '2026-04-28 07:12:34', '2026-04-28 07:12:34'),
(491, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:13:29', '2026-04-28 07:13:29', '2026-04-28 07:13:29'),
(492, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:18:24', '2026-04-28 07:18:24', '2026-04-28 07:18:24'),
(493, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:20:43', '2026-04-28 07:20:43', '2026-04-28 07:20:43'),
(494, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:20:48', '2026-04-28 07:20:48', '2026-04-28 07:20:48'),
(495, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:21:37', '2026-04-28 07:21:37', '2026-04-28 07:21:37'),
(496, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:21:41', '2026-04-28 07:21:41', '2026-04-28 07:21:41'),
(497, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:21:53', '2026-04-28 07:21:53', '2026-04-28 07:21:53'),
(498, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:22:08', '2026-04-28 07:22:08', '2026-04-28 07:22:08'),
(499, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:22:12', '2026-04-28 07:22:12', '2026-04-28 07:22:12'),
(500, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:22:45', '2026-04-28 07:22:45', '2026-04-28 07:22:45'),
(501, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:24:45', '2026-04-28 07:24:45', '2026-04-28 07:24:45'),
(502, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:25:33', '2026-04-28 07:25:33', '2026-04-28 07:25:33'),
(503, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:25:50', '2026-04-28 07:25:50', '2026-04-28 07:25:50'),
(504, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:28:50', '2026-04-28 07:28:50', '2026-04-28 07:28:50'),
(505, 1, 'positions', 'create', 'Da tao chức vụ moi: fsfd', 'positions', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:34:59', '2026-04-28 07:34:59', '2026-04-28 07:34:59'),
(506, 1, 'positions:store', 'create', 'POST positions (positions.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:34:59', '2026-04-28 07:34:59', '2026-04-28 07:34:59'),
(507, 1, 'positions', 'create', 'Da tao chức vụ moi: dev', 'positions', 5, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:41:43', '2026-04-28 07:41:43', '2026-04-28 07:41:43'),
(508, 1, 'positions:store', 'create', 'POST positions (positions.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:41:43', '2026-04-28 07:41:43', '2026-04-28 07:41:43'),
(509, 1, 'positions', 'delete', 'Da xoa chức vụ: fsfd', 'positions', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:41:46', '2026-04-28 07:41:46', '2026-04-28 07:41:46'),
(510, 1, 'positions:destroy', 'delete', 'DELETE positions/4 (positions.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:41:46', '2026-04-28 07:41:46', '2026-04-28 07:41:46'),
(511, 1, 'positions', 'delete', 'Da xoa chức vụ: dev', 'positions', 5, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:41:52', '2026-04-28 07:41:52', '2026-04-28 07:41:52'),
(512, 1, 'positions:destroy', 'delete', 'DELETE positions/5 (positions.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:41:52', '2026-04-28 07:41:52', '2026-04-28 07:41:52'),
(513, 1, 'positions', 'update', 'Cap nhat chức vụ \'hR\': Cap them quyen: update_project, view_salary, approve_attendance, approve_leave, lock_employee, create_employee, request_department_change; Thu hoi quyen: manage_leave_policy, manage_projects, manage_project_members, view_all_projects, manage_positions, view_reports, view_all_profiles, request_update_employee, view_salary_history, request_salary_change, approve_salary_change, export_salary, generate_payroll, view_team_attendance, view_department_attendance, approve_team_attendance, approve_department_attendance, view_team_leave_requests, view_department_leave_requests, view_all_leave_requests, approve_team_leave, approve_department_leave, request_overtime, view_own_overtime, view_team_overtime, view_department_overtime, view_all_overtime, approve_team_overtime, approve_department_overtime, view_own_projects, view_team_projects, view_department_projects, assign_project_member, remove_project_member, view_positions, manage_work_shifts, manage_holidays, view_approval_requests, approve_user_requests, approve_department_requests, approve_salary_requests', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:42:01', '2026-04-28 07:42:01', '2026-04-28 07:42:01'),
(514, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:42:01', '2026-04-28 07:42:01', '2026-04-28 07:42:01'),
(515, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/23/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:55:30', '2026-04-28 07:55:30', '2026-04-28 07:55:30'),
(516, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/23/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:56:05', '2026-04-28 07:56:05', '2026-04-28 07:56:05'),
(517, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/23/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:59:07', '2026-04-28 07:59:07', '2026-04-28 07:59:07'),
(518, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/23/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 07:59:08', '2026-04-28 07:59:08', '2026-04-28 07:59:08'),
(519, 1, 'web:users:capability-overrides:destroy', 'delete', 'DELETE users/23/capability-overrides/7 (web.users.capability-overrides.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:01:08', '2026-04-28 08:01:08', '2026-04-28 08:01:08'),
(520, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/23/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:01:19', '2026-04-28 08:01:19', '2026-04-28 08:01:19'),
(521, 1, 'web:users:capability-overrides:upsert', 'create', 'POST users/23/capability-overrides (web.users.capability-overrides.upsert)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:01:19', '2026-04-28 08:01:19', '2026-04-28 08:01:19'),
(522, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:22:17', '2026-04-28 08:22:17', '2026-04-28 08:22:17'),
(523, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:26:55', '2026-04-28 08:26:55', '2026-04-28 08:26:55'),
(524, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:30:37', '2026-04-28 08:30:37', '2026-04-28 08:30:37'),
(525, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:33:56', '2026-04-28 08:33:56', '2026-04-28 08:33:56'),
(526, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:33:57', '2026-04-28 08:33:57', '2026-04-28 08:33:57'),
(527, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:34:02', '2026-04-28 08:34:02', '2026-04-28 08:34:02'),
(528, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:35:09', '2026-04-28 08:35:09', '2026-04-28 08:35:09'),
(529, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:35:50', '2026-04-28 08:35:50', '2026-04-28 08:35:50'),
(530, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:36:14', '2026-04-28 08:36:14', '2026-04-28 08:36:14'),
(531, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 08:38:05', '2026-04-28 08:38:05', '2026-04-28 08:38:05'),
(532, 1, 'leave', 'bulk_grant_leave_balance', 'Bulk grant leave balances 35 rows for 2026', NULL, NULL, '127.0.0.1', 'Unknown device', 'Symfony', '2026-04-28 09:01:03', '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(533, 1, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 09:24:20', '2026-04-28 09:24:20', '2026-04-28 09:24:20'),
(534, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 09:34:01', '2026-04-28 09:34:01', '2026-04-28 09:34:01'),
(535, 1, 'web:users:salary', 'update', 'PUT users/23/salary (web.users.salary)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 09:41:39', '2026-04-28 09:41:39', '2026-04-28 09:41:39'),
(536, 1, 'web:users:salary', 'update', 'PUT users/23/salary (web.users.salary)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 09:42:02', '2026-04-28 09:42:02', '2026-04-28 09:42:02'),
(537, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: view_all_attendance, view_feedbacks, reply_feedback, view_salary; Thu hoi quyen: manage_projects, manage_project_members, request_update_employee, view_team_attendance, view_department_attendance, request_attendance_adjustment, approve_team_attendance, approve_department_attendance, request_leave, view_team_leave_requests, view_department_leave_requests, approve_team_leave, approve_department_leave, request_overtime, view_own_overtime, view_team_overtime, view_department_overtime, approve_team_overtime, approve_department_overtime, view_team_projects, view_department_projects, assign_project_member, remove_project_member, update_project_task_status, view_departments, view_positions, view_work_shifts, view_holidays, create_feedback', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:21:04', '2026-04-28 10:21:04', '2026-04-28 10:21:04'),
(538, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:21:04', '2026-04-28 10:21:04', '2026-04-28 10:21:04'),
(539, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:21:39', '2026-04-28 10:21:39', '2026-04-28 10:21:39'),
(540, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:21:39', '2026-04-28 10:21:39', '2026-04-28 10:21:39'),
(541, 1, 'positions', 'update', 'Cap nhat chức vụ \'hR\': Cap them quyen: request_overtime, view_own_overtime, view_own_projects, view_team_attendance, view_team_leave_requests, view_team_overtime, view_team_projects, approve_team_attendance, approve_team_leave, approve_team_overtime, manage_project_members, assign_project_member, remove_project_member, view_department_attendance, approve_department_attendance, view_department_leave_requests, approve_department_leave, view_department_overtime, approve_department_overtime, view_department_projects, request_update_employee, manage_projects, view_positions, view_all_profiles, view_salary_history, request_salary_change, approve_requests, view_approval_requests, view_reports, approve_salary_change, manage_leave_policy, view_all_leave_requests, view_all_overtime, view_all_projects, approve_user_requests, export_salary, generate_payroll, manage_positions, manage_work_shifts, manage_holidays, approve_department_requests, approve_salary_requests, view_financial_reports, sign_documents, manage_settings, export_reports; Thu hoi quyen: approve_attendance, approve_leave, create_employee, lock_employee, update_project, request_department_change', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:26:44', '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(542, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:26:44', '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(543, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:31:37', '2026-04-28 10:31:37', '2026-04-28 10:31:37'),
(544, 4, 'attendance', 'check_out', 'Check out attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:32:02', '2026-04-28 10:32:02', '2026-04-28 10:32:02'),
(545, 4, 'attendance:check-out', 'create', 'POST attendance/check-out (attendance.check-out)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:32:02', '2026-04-28 10:32:02', '2026-04-28 10:32:02'),
(546, 23, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 23, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 10:32:15', '2026-04-28 10:32:15', '2026-04-28 10:32:15'),
(547, 23, 'attendance', 'check_out', 'Check out attendance record #14', 'attendance_records', 14, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 10:32:18', '2026-04-28 10:32:18', '2026-04-28 10:32:18'),
(548, 23, 'attendance:check-out', 'create', 'POST attendance/check-out (attendance.check-out)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 10:32:18', '2026-04-28 10:32:18', '2026-04-28 10:32:18'),
(549, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Cap them quyen: request_attendance_adjustment, request_leave, request_overtime, view_own_overtime, update_project_task_status, create_feedback, view_team_attendance, view_team_leave_requests, view_team_overtime, view_team_projects, approve_team_attendance, approve_team_leave, approve_team_overtime, manage_project_members, assign_project_member, remove_project_member, view_department_attendance, approve_department_attendance, view_department_leave_requests, approve_department_leave, view_department_overtime, approve_department_overtime, view_department_projects, request_update_employee, manage_projects, view_departments, view_positions, view_work_shifts, view_holidays, view_all_profiles, view_salary_history, request_salary_change, approve_requests, view_approval_requests, view_reports, approve_update_employee, approve_salary_change, manage_leave_policy, view_all_leave_requests, view_all_overtime, view_all_projects, approve_user_requests, manage_employees, manage_salary, export_salary, generate_payroll, export_attendance, lock_attendance_month, unlock_attendance_month, manage_departments, manage_positions, manage_work_shifts, manage_holidays, approve_department_requests, approve_salary_requests', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:33:51', '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(550, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:33:51', '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(551, 1, 'positions', 'update', 'Cap nhat chức vụ \'hR\': Cap them quyen: approve_requests, approve_attendance', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:35:50', '2026-04-28 10:35:50', '2026-04-28 10:35:50'),
(552, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 10:35:50', '2026-04-28 10:35:50', '2026-04-28 10:35:50'),
(553, 1, 'auth', 'login', 'Dang nhap thanh cong bang Google', 'users', 1, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:02:54', '2026-04-29 01:02:54', '2026-04-29 01:02:54'),
(554, 23, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 23, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-29 01:03:30', '2026-04-29 01:03:30', '2026-04-29 01:03:30'),
(555, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\': Thu hoi quyen: manage_employees, view_salary, manage_salary, view_all_attendance, export_attendance, manage_leave_policy, manage_projects, manage_project_members, view_all_projects, manage_departments, manage_positions, view_reports, view_all_profiles, request_update_employee, approve_update_employee, view_salary_history, request_salary_change, approve_salary_change, export_salary, generate_payroll, view_team_attendance, view_department_attendance, approve_team_attendance, approve_department_attendance, lock_attendance_month, unlock_attendance_month, view_team_leave_requests, view_department_leave_requests, view_all_leave_requests, approve_team_leave, approve_department_leave, view_team_overtime, view_department_overtime, view_all_overtime, approve_team_overtime, approve_department_overtime, view_team_projects, view_department_projects, assign_project_member, remove_project_member, view_departments, view_positions, view_work_shifts, manage_work_shifts, view_holidays, manage_holidays, view_approval_requests, view_feedbacks, reply_feedback, approve_user_requests, approve_department_requests, approve_salary_requests', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:01', '2026-04-29 01:04:01', '2026-04-29 01:04:01'),
(556, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:01', '2026-04-29 01:04:01', '2026-04-29 01:04:01'),
(557, 1, 'positions', 'update', 'Cap nhat chức vụ \'Nhân viên\'', 'positions', 2, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:12', '2026-04-29 01:04:12', '2026-04-29 01:04:12'),
(558, 1, 'positions:update', 'update', 'PUT positions/2 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:12', '2026-04-29 01:04:12', '2026-04-29 01:04:12'),
(559, 1, 'positions', 'update', 'Cap nhat chức vụ \'hR\': Cap them quyen: approve_requests; Thu hoi quyen: approve_attendance', 'positions', 3, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:19', '2026-04-29 01:04:19', '2026-04-29 01:04:19'),
(560, 1, 'positions:update', 'update', 'PUT positions/3 (positions.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:19', '2026-04-29 01:04:19', '2026-04-29 01:04:19'),
(561, 1, 'projects:members:destroy', 'delete', 'DELETE projects/1/members/2 (projects.members.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:04:46', '2026-04-29 01:04:46', '2026-04-29 01:04:46'),
(562, 23, 'attendance', 'submit_request', 'Submit attendance request #10', 'attendance_requests', 10, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-29 01:19:41', '2026-04-29 01:19:41', '2026-04-29 01:19:41'),
(563, 23, 'attendance:requests:store', 'create', 'POST attendance/requests (attendance.requests.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-29 01:19:41', '2026-04-29 01:19:41', '2026-04-29 01:19:41'),
(564, 1, 'attendance', 'apply_attendance_formula', 'Apply late_early request #10 to record #14: late_early(late) => late:24->0, early:0->0, status:late->on_time', 'attendance_records', 14, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:19:59', '2026-04-29 01:19:59', '2026-04-29 01:19:59'),
(565, 1, 'attendance', 'review_request_approved', 'Review approval request #22', 'approval_requests', 22, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:19:59', '2026-04-29 01:19:59', '2026-04-29 01:19:59'),
(566, 1, 'attendance:request-approvals:approve', 'create', 'POST attendance/request-approvals/22/approve (attendance.request-approvals.approve)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:19:59', '2026-04-29 01:19:59', '2026-04-29 01:19:59'),
(567, 1, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:26:18', '2026-04-29 01:26:18', '2026-04-29 01:26:18'),
(568, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/4/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:48:06', '2026-04-29 01:48:06', '2026-04-29 01:48:06'),
(569, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/4/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:48:08', '2026-04-29 01:48:08', '2026-04-29 01:48:08'),
(570, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/2 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:49:34', '2026-04-29 01:49:34', '2026-04-29 01:49:34'),
(571, 1, 'attendance:catalogs:work-shifts:update', 'update', 'PUT attendance/catalogs/work-shifts/3 (attendance.catalogs.work-shifts.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:50:48', '2026-04-29 01:50:48', '2026-04-29 01:50:48'),
(572, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/30 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:51:19', '2026-04-29 01:51:19', '2026-04-29 01:51:19'),
(573, 1, 'attendance:catalogs:assignments:store', 'create', 'POST attendance/catalogs/assignments (attendance.catalogs.assignments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:51:58', '2026-04-29 01:51:58', '2026-04-29 01:51:58'),
(574, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/1/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:52:15', '2026-04-29 01:52:15', '2026-04-29 01:52:15'),
(575, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/2/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:52:17', '2026-04-29 01:52:17', '2026-04-29 01:52:17'),
(576, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/3/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:52:18', '2026-04-29 01:52:18', '2026-04-29 01:52:18'),
(577, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/4/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:52:20', '2026-04-29 01:52:20', '2026-04-29 01:52:20'),
(578, 1, 'attendance:catalogs:assignments:toggle', 'update', 'PUT attendance/catalogs/assignments/5/toggle (attendance.catalogs.assignments.toggle)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:52:21', '2026-04-29 01:52:21', '2026-04-29 01:52:21'),
(579, 1, 'attendance:catalogs:assignments:update', 'update', 'PUT attendance/catalogs/assignments/30 (attendance.catalogs.assignments.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:52:42', '2026-04-29 01:52:42', '2026-04-29 01:52:42'),
(580, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 01:59:43', '2026-04-29 01:59:43', '2026-04-29 01:59:43'),
(581, 1, 'attendance', 'confirm', 'Confirm attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:15:33', '2026-04-29 02:15:33', '2026-04-29 02:15:33'),
(582, 1, 'attendance:confirm', 'create', 'POST attendance/13/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:15:34', '2026-04-29 02:15:34', '2026-04-29 02:15:34'),
(583, 1, 'attendance', 'confirm', 'Confirm attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:16:34', '2026-04-29 02:16:34', '2026-04-29 02:16:34'),
(584, 1, 'attendance:confirm', 'create', 'POST attendance/13/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:16:34', '2026-04-29 02:16:34', '2026-04-29 02:16:34'),
(585, 1, 'attendance', 'confirm', 'Confirm attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:21:45', '2026-04-29 02:21:45', '2026-04-29 02:21:45'),
(586, 1, 'attendance:confirm', 'create', 'POST attendance/13/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:21:45', '2026-04-29 02:21:45', '2026-04-29 02:21:45'),
(587, 1, 'attendance', 'confirm', 'Confirm attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:22:02', '2026-04-29 02:22:02', '2026-04-29 02:22:02'),
(588, 1, 'attendance:confirm', 'create', 'POST attendance/13/confirm (attendance.confirm)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:22:02', '2026-04-29 02:22:02', '2026-04-29 02:22:02'),
(589, 1, 'attendance', 'confirm', 'Confirm attendance record #13', 'attendance_records', 13, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:28:05', '2026-04-29 02:28:05', '2026-04-29 02:28:05'),
(590, 1, 'attendance:confirm-bulk', 'create', 'POST attendance/confirm-bulk (attendance.confirm-bulk)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:28:05', '2026-04-29 02:28:05', '2026-04-29 02:28:05'),
(591, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:30:47', '2026-04-29 02:30:47', '2026-04-29 02:30:47'),
(592, 4, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:41:11', '2026-04-29 02:41:11', '2026-04-29 02:41:11'),
(593, 1, 'attendance', 'export_pdf', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:43:54', '2026-04-29 02:43:54', '2026-04-29 02:43:54'),
(594, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:44:28', '2026-04-29 02:44:28', '2026-04-29 02:44:28'),
(595, 1, 'projects:update', 'update', 'PUT projects/1 (projects.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:51:08', '2026-04-29 02:51:08', '2026-04-29 02:51:08'),
(596, 1, 'projects:update', 'update', 'PUT projects/1 (projects.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:51:15', '2026-04-29 02:51:15', '2026-04-29 02:51:15'),
(597, 1, 'positions:store', 'create', 'POST positions (positions.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:53:41', '2026-04-29 02:53:41', '2026-04-29 02:53:41'),
(598, 1, 'positions:store', 'create', 'POST positions (positions.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 02:53:51', '2026-04-29 02:53:51', '2026-04-29 02:53:51'),
(599, 1, 'projects:store', 'create', 'POST projects (projects.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:00:45', '2026-04-29 03:00:45', '2026-04-29 03:00:45'),
(600, 1, 'projects:roles:store', 'create', 'POST projects/2/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:01:05', '2026-04-29 03:01:05', '2026-04-29 03:01:05'),
(601, 1, 'projects:roles:destroy', 'delete', 'DELETE projects/2/roles/4 (projects.roles.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:01:14', '2026-04-29 03:01:14', '2026-04-29 03:01:14'),
(602, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:05:22', '2026-04-29 03:05:22', '2026-04-29 03:05:22');
INSERT INTO `activity_logs` (`id`, `user_id`, `module`, `action`, `description`, `reference_table`, `reference_id`, `ip_address`, `device`, `user_agent`, `occurred_at`, `created_at`, `updated_at`) VALUES
(603, 1, 'projects:implementation-details:store', 'create', 'POST projects/1/implementation-details (projects.implementation-details.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:06:55', '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(604, 1, 'projects:roles:destroy', 'delete', 'DELETE projects/1/roles/3 (projects.roles.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:31:39', '2026-04-29 03:31:39', '2026-04-29 03:31:39'),
(605, 1, 'projects:roles:destroy', 'delete', 'DELETE projects/1/roles/2 (projects.roles.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:31:44', '2026-04-29 03:31:44', '2026-04-29 03:31:44'),
(606, 1, 'projects:roles:destroy', 'delete', 'DELETE projects/1/roles/1 (projects.roles.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:31:49', '2026-04-29 03:31:49', '2026-04-29 03:31:49'),
(607, 1, 'projects:roles:store', 'create', 'POST projects/1/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:32:09', '2026-04-29 03:32:09', '2026-04-29 03:32:09'),
(608, 1, 'projects:roles:store', 'create', 'POST projects/1/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:32:24', '2026-04-29 03:32:24', '2026-04-29 03:32:24'),
(609, 1, 'projects:members:update', 'update', 'PUT projects/1/members/1 (projects.members.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:32:28', '2026-04-29 03:32:28', '2026-04-29 03:32:28'),
(610, 1, 'projects:members:update', 'update', 'PUT projects/1/members/1 (projects.members.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:32:36', '2026-04-29 03:32:36', '2026-04-29 03:32:36'),
(611, 1, 'projects:roles:destroy', 'delete', 'DELETE projects/1/roles/1 (projects.roles.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:32:43', '2026-04-29 03:32:43', '2026-04-29 03:32:43'),
(612, 1, 'projects:roles:store', 'create', 'POST projects/1/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:32:53', '2026-04-29 03:32:53', '2026-04-29 03:32:53'),
(613, 1, 'projects:roles:destroy', 'delete', 'DELETE projects/1/roles/5 (projects.roles.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:33:00', '2026-04-29 03:33:00', '2026-04-29 03:33:00'),
(614, 1, 'projects:roles:store', 'create', 'POST projects/1/roles (projects.roles.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:33:07', '2026-04-29 03:33:07', '2026-04-29 03:33:07'),
(615, 1, 'projects:members:store', 'create', 'POST projects/1/members (projects.members.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:34:51', '2026-04-29 03:34:51', '2026-04-29 03:34:51'),
(616, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:36:59', '2026-04-29 03:36:59', '2026-04-29 03:36:59'),
(617, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/2/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:38:34', '2026-04-29 03:38:34', '2026-04-29 03:38:34'),
(618, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/2/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:38:53', '2026-04-29 03:38:53', '2026-04-29 03:38:53'),
(619, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/2/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:43:24', '2026-04-29 03:43:24', '2026-04-29 03:43:24'),
(620, 1, 'projects:implementation-details:update', 'update', 'PUT projects/1/implementation-details/1 (projects.implementation-details.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 03:56:05', '2026-04-29 03:56:05', '2026-04-29 03:56:05'),
(621, 1, 'projects:implementation-details:update', 'update', 'PUT projects/1/implementation-details/2 (projects.implementation-details.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:02:37', '2026-04-29 04:02:37', '2026-04-29 04:02:37'),
(622, 1, 'projects:update', 'update', 'PUT projects/2 (projects.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:11:37', '2026-04-29 04:11:37', '2026-04-29 04:11:37'),
(623, 1, 'projects:implementation-details:subtasks:store', 'create', 'POST projects/1/implementation-details/2/subtasks (projects.implementation-details.subtasks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:13:01', '2026-04-29 04:13:01', '2026-04-29 04:13:01'),
(624, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/1/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:14:46', '2026-04-29 04:14:46', '2026-04-29 04:14:46'),
(625, 1, 'projects:implementation-details:subtasks:store', 'create', 'POST projects/1/implementation-details/2/subtasks (projects.implementation-details.subtasks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:34:40', '2026-04-29 04:34:40', '2026-04-29 04:34:40'),
(626, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:42:44', '2026-04-29 04:42:44', '2026-04-29 04:42:44'),
(627, 1, 'projects:implementation-details:subtasks:store', 'create', 'POST projects/1/implementation-details/2/subtasks (projects.implementation-details.subtasks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:43:10', '2026-04-29 04:43:10', '2026-04-29 04:43:10'),
(628, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:44:01', '2026-04-29 04:44:01', '2026-04-29 04:44:01'),
(629, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:44:14', '2026-04-29 04:44:14', '2026-04-29 04:44:14'),
(630, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/1/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:48:28', '2026-04-29 04:48:28', '2026-04-29 04:48:28'),
(631, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/1/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:48:33', '2026-04-29 04:48:33', '2026-04-29 04:48:33'),
(632, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:50:02', '2026-04-29 04:50:02', '2026-04-29 04:50:02'),
(633, 1, 'projects:implementation-details:subtasks:store', 'create', 'POST projects/1/implementation-details/2/subtasks (projects.implementation-details.subtasks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:50:38', '2026-04-29 04:50:38', '2026-04-29 04:50:38'),
(634, 23, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-29 04:53:01', '2026-04-29 04:53:01', '2026-04-29 04:53:01'),
(635, 1, 'projects:implementation-details:subtasks:update', 'update', 'PUT projects/1/implementation-details/2/subtasks/3 (projects.implementation-details.subtasks.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 04:53:19', '2026-04-29 04:53:19', '2026-04-29 04:53:19'),
(636, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 06:34:42', '2026-04-29 06:34:42', '2026-04-29 06:34:42'),
(637, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 06:35:03', '2026-04-29 06:35:03', '2026-04-29 06:35:03'),
(638, 1, 'projects:members:update', 'update', 'PUT projects/1/members/2 (projects.members.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 06:36:06', '2026-04-29 06:36:06', '2026-04-29 06:36:06'),
(639, 1, 'projects:update', 'update', 'PUT projects/2 (projects.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 06:37:02', '2026-04-29 06:37:02', '2026-04-29 06:37:02'),
(640, 1, 'attendance', 'export_excel', 'Export attendance report', 'export_histories', NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 06:38:50', '2026-04-29 06:38:50', '2026-04-29 06:38:50'),
(641, 1, 'projects:toggle-lock', 'update', 'PUT projects/1/toggle-lock (projects.toggle-lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:00:48', '2026-04-29 07:00:48', '2026-04-29 07:00:48'),
(642, 1, 'projects:toggle-lock', 'update', 'PUT projects/1/toggle-lock (projects.toggle-lock)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:00:57', '2026-04-29 07:00:57', '2026-04-29 07:00:57'),
(643, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:02:18', '2026-04-29 07:02:18', '2026-04-29 07:02:18'),
(644, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:02:31', '2026-04-29 07:02:31', '2026-04-29 07:02:31'),
(645, 1, 'projects:implementation-details:status', 'update', 'PUT projects/1/implementation-details/1/status (projects.implementation-details.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:05:21', '2026-04-29 07:05:21', '2026-04-29 07:05:21'),
(646, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:11:50', '2026-04-29 07:11:50', '2026-04-29 07:11:50'),
(647, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:12:05', '2026-04-29 07:12:05', '2026-04-29 07:12:05'),
(648, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/3/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:12:11', '2026-04-29 07:12:11', '2026-04-29 07:12:11'),
(649, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/4/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:12:17', '2026-04-29 07:12:17', '2026-04-29 07:12:17'),
(650, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:12:22', '2026-04-29 07:12:22', '2026-04-29 07:12:22'),
(651, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/3/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:13:03', '2026-04-29 07:13:03', '2026-04-29 07:13:03'),
(652, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/4/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:13:09', '2026-04-29 07:13:09', '2026-04-29 07:13:09'),
(653, 23, 'api', 'create', 'POST api/notifications/mark-all-read (unknown)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-29 07:15:26', '2026-04-29 07:15:26', '2026-04-29 07:15:26'),
(654, 1, 'projects:implementation-details:subtasks:store', 'create', 'POST projects/1/implementation-details/2/subtasks (projects.implementation-details.subtasks.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:17:21', '2026-04-29 07:17:21', '2026-04-29 07:17:21'),
(655, 1, 'projects:implementation-details:comments:store', 'create', 'POST projects/1/implementation-details/2/comments (projects.implementation-details.comments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:50:31', '2026-04-29 07:50:31', '2026-04-29 07:50:31'),
(656, 1, 'projects:implementation-details:comments:store', 'create', 'POST projects/1/implementation-details/2/comments (projects.implementation-details.comments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:50:31', '2026-04-29 07:50:31', '2026-04-29 07:50:31'),
(657, 1, 'projects:implementation-details:comments:store', 'create', 'POST projects/1/implementation-details/2/comments (projects.implementation-details.comments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:50:35', '2026-04-29 07:50:35', '2026-04-29 07:50:35'),
(658, 1, 'projects:implementation-details:comments:store', 'create', 'POST projects/1/implementation-details/1/comments (projects.implementation-details.comments.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:50:40', '2026-04-29 07:50:40', '2026-04-29 07:50:40'),
(659, 1, 'projects:implementation-details:comments:destroy', 'delete', 'DELETE projects/1/implementation-details/1/comments/4 (projects.implementation-details.comments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:50:57', '2026-04-29 07:50:57', '2026-04-29 07:50:57'),
(660, 1, 'projects:implementation-details:comments:destroy', 'delete', 'DELETE projects/1/implementation-details/2/comments/3 (projects.implementation-details.comments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:51:01', '2026-04-29 07:51:01', '2026-04-29 07:51:01'),
(661, 1, 'projects:implementation-details:comments:destroy', 'delete', 'DELETE projects/1/implementation-details/2/comments/2 (projects.implementation-details.comments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:51:03', '2026-04-29 07:51:03', '2026-04-29 07:51:03'),
(662, 1, 'projects:implementation-details:comments:destroy', 'delete', 'DELETE projects/1/implementation-details/2/comments/1 (projects.implementation-details.comments.destroy)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 07:51:05', '2026-04-29 07:51:05', '2026-04-29 07:51:05'),
(663, 1, 'projects:milestones:store', 'create', 'POST projects/1/milestones (projects.milestones.store)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:07:36', '2026-04-29 08:07:36', '2026-04-29 08:07:36'),
(664, 1, 'projects:milestones:update', 'update', 'PUT projects/1/milestones/1 (projects.milestones.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:07:46', '2026-04-29 08:07:46', '2026-04-29 08:07:46'),
(665, 1, 'projects:milestones:update', 'update', 'PUT projects/1/milestones/1 (projects.milestones.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:08:04', '2026-04-29 08:08:04', '2026-04-29 08:08:04'),
(666, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/1/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:14:16', '2026-04-29 08:14:16', '2026-04-29 08:14:16'),
(667, 1, 'projects:implementation-details:subtasks:status', 'update', 'PUT projects/1/implementation-details/2/subtasks/2/status (projects.implementation-details.subtasks.status)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:14:30', '2026-04-29 08:14:30', '2026-04-29 08:14:30'),
(668, 1, 'projects:implementation-details:subtasks:update', 'update', 'PUT projects/1/implementation-details/2/subtasks/3 (projects.implementation-details.subtasks.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:14:42', '2026-04-29 08:14:42', '2026-04-29 08:14:42'),
(669, 1, 'projects:roles:update', 'update', 'PUT projects/1/roles/7 (projects.roles.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:32:54', '2026-04-29 08:32:54', '2026-04-29 08:32:54'),
(670, 1, 'projects:roles:update', 'update', 'PUT projects/1/roles/7 (projects.roles.update)', NULL, NULL, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:33:26', '2026-04-29 08:33:26', '2026-04-29 08:33:26'),
(671, 4, 'auth', 'login', 'Dang nhap thanh cong bang Email va mật khẩu', 'users', 4, '127.0.0.1', 'Windows', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 08:59:01', '2026-04-29 08:59:01', '2026-04-29 08:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `approval_decision_deliveries`
--

CREATE TABLE `approval_decision_deliveries` (
  `id` bigint UNSIGNED NOT NULL,
  `dedupe_key` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mail',
  `decision` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `recipient_user_id` bigint UNSIGNED DEFAULT NULL,
  `recipient_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `triggered_by` bigint UNSIGNED DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `attempt_count` int UNSIGNED NOT NULL DEFAULT '0',
  `queued_at` timestamp NULL DEFAULT NULL,
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `last_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_decision_deliveries`
--

INSERT INTO `approval_decision_deliveries` (`id`, `dedupe_key`, `module`, `channel`, `decision`, `status`, `recipient_user_id`, `recipient_email`, `subject`, `action_url`, `reference_type`, `reference_id`, `triggered_by`, `payload`, `attempt_count`, `queued_at`, `last_attempt_at`, `sent_at`, `failed_at`, `last_error`, `created_at`, `updated_at`) VALUES
(1, '1d2d1342581b0e2f38758ab12e1bd401fdb2d5b3', 'department', 'mail', 'rejected', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu cập nhật phòng ban đã bị từ chối', '/departments', 'App\\Models\\ApprovalRequest', 16, 1, '{\"item_label\": \"Yêu cầu cập nhật phòng ban\", \"decision_at\": \"24/04/2026 09:38\", \"review_note\": \"chưa đủ tiêu cầu\", \"request_type\": \"department_update\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 2, '2026-04-24 02:38:30', '2026-04-24 02:45:45', '2026-04-24 02:45:50', NULL, NULL, '2026-04-24 02:38:30', '2026-04-24 02:45:50'),
(2, 'a630aa7f728866863143b06416cc585bf12e0f63', 'department', 'mail', 'rejected', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu khóa/mở phòng ban đã bị từ chối', '/departments', 'App\\Models\\ApprovalRequest', 17, 1, '{\"item_label\": \"Yêu cầu khóa/mở phòng ban\", \"decision_at\": \"24/04/2026 09:47\", \"review_note\": \"chưa đủ yêu cầu\", \"request_type\": \"department_toggle\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-24 02:47:47', '2026-04-24 02:47:47', '2026-04-24 02:47:52', NULL, NULL, '2026-04-24 02:47:47', '2026-04-24 02:47:52'),
(3, 'ceecdc8fd2d37f0aeb0014e6b409c711d24d4dd8', 'salary', 'mail', 'rejected', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu đổi lương cơ bản đã bị từ chối', '/users/employee-requests', 'App\\Models\\ApprovalRequest', 18, 1, '{\"item_label\": \"Yêu cầu đổi lương cơ bản\", \"decision_at\": \"24/04/2026 10:23\", \"review_note\": \"tuổi\", \"request_type\": \"user_salary_change\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-24 03:23:30', '2026-04-24 03:23:30', '2026-04-24 03:23:34', NULL, NULL, '2026-04-24 03:23:30', '2026-04-24 03:23:34'),
(4, '60f6a2e221900420e53a1a540625c8f7da7898a0', 'salary', 'mail', 'rejected', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu đổi lương cơ bản đã bị từ chối', '/users/employee-requests', 'App\\Models\\ApprovalRequest', 19, 1, '{\"item_label\": \"Yêu cầu đổi lương cơ bản\", \"decision_at\": \"24/04/2026 10:25\", \"review_note\": \"tuôit\", \"request_type\": \"user_salary_change\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-24 03:25:11', '2026-04-24 03:25:11', '2026-04-24 03:25:14', NULL, NULL, '2026-04-24 03:25:11', '2026-04-24 03:25:14'),
(5, '3ea6d2da1b7f2a911b93e4e7c8a7a0fdb4edd1d9', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Đơn xin đi muộn / về sớm đã được duyệt', '/my-attendance', 'App\\Models\\ApprovalRequest', 20, 1, '{\"item_label\": \"Đơn xin đi muộn / về sớm\", \"decision_at\": \"24/04/2026 16:53\", \"review_note\": \"Giải trình về sớm ngày 20/4/2026, về sớm 15 phút.\", \"request_type\": \"late_early\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-24 09:53:51', '2026-04-24 09:53:52', '2026-04-24 09:53:56', NULL, NULL, '2026-04-24 09:53:51', '2026-04-24 09:53:56'),
(6, '4055c5a8832fdfe0cf8c28fe394b0df9973cd984', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Đơn xin quên chấm công đã được duyệt', '/my-attendance', 'App\\Models\\ApprovalRequest', 21, 1, '{\"item_label\": \"Đơn xin quên chấm công\", \"decision_at\": \"28/04/2026 08:27\", \"review_note\": \"Giải trình bổ sung check-out ngày 24/4/2026.\", \"request_type\": \"forgot_check\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-28 01:27:25', '2026-04-28 01:27:25', '2026-04-28 01:27:29', NULL, NULL, '2026-04-28 01:27:25', '2026-04-28 01:27:29'),
(7, 'c190b40eb7a2ea0c9726be7315a5ca0504f5d4cd', 'salary', 'mail', 'approved', 'sent', 23, 'thisoma1308@gmail.com', '[HRM] Cập nhật lương cơ bản đã được duyệt', '/my-salary', 'App\\Models\\User', 23, 1, '{\"item_label\": \"Cập nhật lương cơ bản\", \"decision_at\": \"28/04/2026 16:41\", \"review_note\": \"làm tốt lắm\", \"request_type\": \"direct_salary_update\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"thi\"}', 1, '2026-04-28 09:41:39', '2026-04-28 09:41:39', '2026-04-28 09:41:44', NULL, NULL, '2026-04-28 09:41:39', '2026-04-28 09:41:44'),
(8, '353c1ba418261d78f394040cd77c76032d613c35', 'salary', 'mail', 'approved', 'sent', 23, 'thisoma1308@gmail.com', '[HRM] Cập nhật lương cơ bản đã được duyệt', '/my-salary', 'App\\Models\\User', 23, 1, '{\"item_label\": \"Cập nhật lương cơ bản\", \"decision_at\": \"28/04/2026 16:42\", \"review_note\": \"Lương cơ bản được cập nhật từ 7.000.000 VND lên 7.500.000 VND.\", \"request_type\": \"direct_salary_update\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"thi\"}', 1, '2026-04-28 09:42:02', '2026-04-28 09:42:02', '2026-04-28 09:42:06', NULL, NULL, '2026-04-28 09:42:02', '2026-04-28 09:42:06'),
(9, 'f7a87cf218bcf9161b22baa16fe046846c40093f', 'attendance', 'mail', 'approved', 'sent', 23, 'thisoma1308@gmail.com', '[HRM] Đơn xin đi muộn / về sớm đã được duyệt', '/my-attendance', 'App\\Models\\ApprovalRequest', 22, 1, '{\"item_label\": \"Đơn xin đi muộn / về sớm\", \"decision_at\": \"29/04/2026 08:19\", \"review_note\": \"Giải trình đi muộn ngày 28/4/2026, đi muộn 24 phút.\", \"request_type\": \"late_early\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"thi\"}', 1, '2026-04-29 01:19:59', '2026-04-29 01:19:59', '2026-04-29 01:20:05', NULL, NULL, '2026-04-29 01:19:59', '2026-04-29 01:20:05'),
(10, '7ed2d12fff29a3356be4537472f955985c514141', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', '/my-attendance?month=4&year=2026', 'App\\Models\\AttendanceRecord', 13, 1, '{\"item_label\": \"Bản ghi công ngày 28/04/2026\", \"decision_at\": \"29/04/2026 09:15\", \"review_note\": \"vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).\", \"request_type\": \"attendance_record\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-29 02:15:33', '2026-04-29 02:15:34', '2026-04-29 02:15:38', NULL, NULL, '2026-04-29 02:15:33', '2026-04-29 02:15:38'),
(11, '385424bbe591441c75de7cd2e2a4d899aa69f1a4', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', '/my-attendance?month=4&year=2026', 'App\\Models\\AttendanceRecord', 13, 1, '{\"item_label\": \"Bản ghi công ngày 28/04/2026\", \"decision_at\": \"29/04/2026 09:16\", \"review_note\": \"vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).\", \"request_type\": \"attendance_record\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-29 02:16:34', '2026-04-29 02:16:34', '2026-04-29 02:16:39', NULL, NULL, '2026-04-29 02:16:34', '2026-04-29 02:16:39'),
(12, 'fddd4e36ed969e82a1f3c9d9d132c702f9264bb0', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', '/my-attendance?month=4&year=2026', 'App\\Models\\AttendanceRecord', 13, 1, '{\"item_label\": \"Bản ghi công ngày 28/04/2026\", \"decision_at\": \"29/04/2026 09:21\", \"review_note\": \"vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).\", \"request_type\": \"attendance_record\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-29 02:21:45', '2026-04-29 02:21:45', '2026-04-29 02:21:49', NULL, NULL, '2026-04-29 02:21:45', '2026-04-29 02:21:49'),
(13, '23f2ee9827e05c252676008211e5c96be8f6d2e0', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', '/my-attendance?month=4&year=2026', 'App\\Models\\AttendanceRecord', 13, 1, '{\"item_label\": \"Bản ghi công ngày 28/04/2026\", \"decision_at\": \"29/04/2026 09:22\", \"review_note\": \"vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).\", \"request_type\": \"attendance_record\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-29 02:22:02', '2026-04-29 02:22:02', '2026-04-29 02:22:07', NULL, NULL, '2026-04-29 02:22:02', '2026-04-29 02:22:07'),
(14, '7f96ee8e868a851432fd9e8a51edbe8f97f9bd01', 'attendance', 'mail', 'approved', 'sent', 4, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', '/my-attendance?month=4&year=2026', 'App\\Models\\AttendanceRecord', 13, 1, '{\"item_label\": \"Bản ghi công ngày 28/04/2026\", \"decision_at\": \"29/04/2026 09:28\", \"review_note\": \"Duyệt hàng loạt | Duyệt thủ công (Có vi phạm)\", \"request_type\": \"attendance_record\", \"reviewer_name\": \"GTV Be Hieu\", \"recipient_name\": \"hieu duy\"}', 1, '2026-04-29 02:28:05', '2026-04-29 02:28:05', '2026-04-29 02:28:08', NULL, NULL, '2026-04-29 02:28:05', '2026-04-29 02:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `approval_requests`
--

CREATE TABLE `approval_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `request_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint UNSIGNED NOT NULL,
  `requested_by` bigint UNSIGNED NOT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `review_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_requests`
--

INSERT INTO `approval_requests` (`id`, `request_type`, `target_type`, `target_id`, `requested_by`, `reviewed_by`, `status`, `submitted_at`, `reviewed_at`, `reason`, `review_note`, `created_at`, `updated_at`) VALUES
(1, 'manual_adjustment', 'App\\Models\\AttendanceAdjustment', 1, 4, 1, 'approved', '2026-04-20 02:30:28', '2026-04-20 02:36:40', 'tôi chưa check out', NULL, '2026-04-20 02:30:28', '2026-04-20 02:36:40'),
(2, 'manual_adjustment', 'App\\Models\\AttendanceAdjustment', 2, 4, 1, 'rejected', '2026-04-20 02:37:44', '2026-04-20 02:41:00', 'tôi bị sai giờ', 'chưa đủ yêu cầu đề ra', '2026-04-20 02:37:44', '2026-04-20 02:41:00'),
(3, 'manual_adjustment', 'App\\Models\\AttendanceAdjustment', 3, 4, 1, 'approved', '2026-04-20 02:41:32', '2026-04-20 03:46:26', 'rất ok', 'ok', '2026-04-20 02:41:32', '2026-04-20 03:46:26'),
(4, 'overtime', 'App\\Models\\OvertimeRequest', 1, 4, 1, 'rejected', '2026-04-20 07:43:01', '2026-04-20 07:47:47', 'xin tăng ca', 'xin tăng ca', '2026-04-20 07:43:01', '2026-04-20 07:47:47'),
(5, 'leave', 'App\\Models\\AttendanceRequest', 1, 4, 1, 'rejected', '2026-04-21 07:25:56', '2026-04-21 07:27:12', 'cần đi bar', 'cần đi bar', '2026-04-21 07:25:56', '2026-04-21 07:27:12'),
(6, 'department_toggle', 'App\\Models\\Department', 1, 4, 1, 'rejected', '2026-04-21 09:01:49', '2026-04-21 09:02:08', 'Đề nghị khóa phòng ban Ban dieu hanh he thong', 'từ chối', '2026-04-21 09:01:49', '2026-04-21 09:02:08'),
(7, 'department_update', 'App\\Models\\Department', 1, 4, 1, 'rejected', '2026-04-21 09:02:36', '2026-04-21 09:02:45', 'Đề nghị cập nhật phòng ban Ban dieu hanh he thong', NULL, '2026-04-21 09:02:36', '2026-04-21 09:02:45'),
(8, 'manual_adjustment', 'App\\Models\\AttendanceAdjustment', 4, 1, NULL, 'pending', '2026-04-22 04:30:47', NULL, 'quên chưa check out', NULL, '2026-04-22 04:30:47', '2026-04-22 04:30:47'),
(9, 'forgot_check', 'App\\Models\\AttendanceRequest', 2, 1, NULL, 'pending', '2026-04-22 04:39:06', NULL, 'sdfsf', NULL, '2026-04-22 04:39:06', '2026-04-22 04:39:06'),
(10, 'forgot_check', 'App\\Models\\AttendanceRequest', 3, 4, 1, 'rejected', '2026-04-22 04:39:53', '2026-04-22 04:40:22', 'dfssdf', 'dfssdf', '2026-04-22 04:39:53', '2026-04-22 04:40:22'),
(11, 'leave', 'App\\Models\\AttendanceRequest', 4, 1, NULL, 'pending', '2026-04-22 06:40:51', NULL, 'ok', NULL, '2026-04-22 06:40:51', '2026-04-22 06:40:51'),
(12, 'forgot_check', 'App\\Models\\AttendanceRequest', 5, 4, 1, 'approved', '2026-04-23 01:19:31', '2026-04-23 01:20:02', 'quên', 'quên ok', '2026-04-23 01:19:31', '2026-04-23 01:20:02'),
(13, 'leave', 'App\\Models\\AttendanceRequest', 6, 4, 1, 'approved', '2026-04-23 02:28:35', '2026-04-23 02:29:03', 'dsdsfdsf', 'dsdsfdsf', '2026-04-23 02:28:35', '2026-04-23 02:29:03'),
(14, 'late_early', 'App\\Models\\AttendanceRequest', 7, 4, 4, 'cancelled', '2026-04-23 08:11:42', '2026-04-23 08:21:49', 'Giai trinh ve som ngay 20/4/2026.', 'Requester cancelled attendance request', '2026-04-23 08:11:42', '2026-04-23 08:21:49'),
(15, 'user_salary_change', 'App\\Models\\User', 2, 4, 1, 'approved', '2026-04-24 01:18:33', '2026-04-24 01:29:32', 'HR de nghi thay doi luong co ban', 'rất chi là được việc', '2026-04-24 01:18:33', '2026-04-24 01:29:32'),
(16, 'department_update', 'App\\Models\\Department', 1, 4, 1, 'rejected', '2026-04-24 02:38:02', '2026-04-24 02:38:30', 'Đề nghị cập nhật phòng ban Ban dieu hanh he thong', 'chưa đủ tiêu cầu', '2026-04-24 02:38:02', '2026-04-24 02:38:30'),
(17, 'department_toggle', 'App\\Models\\Department', 1, 4, 1, 'rejected', '2026-04-24 02:47:03', '2026-04-24 02:47:47', 'Đề nghị khóa phòng ban Ban dieu hanh he thong', 'chưa đủ yêu cầu', '2026-04-24 02:47:03', '2026-04-24 02:47:47'),
(18, 'user_salary_change', 'App\\Models\\User', 2, 4, 1, 'rejected', '2026-04-24 03:22:07', '2026-04-24 03:23:30', 'HR de nghi thay doi luong co ban', 'tuổi', '2026-04-24 03:22:07', '2026-04-24 03:23:30'),
(19, 'user_salary_change', 'App\\Models\\User', 2, 4, 1, 'rejected', '2026-04-24 03:23:50', '2026-04-24 03:25:11', 'làm tốt', 'tuôit', '2026-04-24 03:23:50', '2026-04-24 03:25:11'),
(20, 'late_early', 'App\\Models\\AttendanceRequest', 8, 4, 1, 'approved', '2026-04-24 09:10:50', '2026-04-24 09:53:51', 'Giải trình về sớm ngày 20/4/2026, về sớm 15 phút.', 'Giải trình về sớm ngày 20/4/2026, về sớm 15 phút.', '2026-04-24 09:10:50', '2026-04-24 09:53:51'),
(21, 'forgot_check', 'App\\Models\\AttendanceRequest', 9, 4, 1, 'approved', '2026-04-28 01:27:02', '2026-04-28 01:27:25', 'Giải trình bổ sung check-out ngày 24/4/2026.', 'Giải trình bổ sung check-out ngày 24/4/2026.', '2026-04-28 01:27:02', '2026-04-28 01:27:25'),
(22, 'late_early', 'App\\Models\\AttendanceRequest', 10, 23, 1, 'approved', '2026-04-29 01:19:41', '2026-04-29 01:19:59', 'Giải trình đi muộn ngày 28/4/2026, đi muộn 24 phút.', 'Giải trình đi muộn ngày 28/4/2026, đi muộn 24 phút.', '2026-04-29 01:19:41', '2026-04-29 01:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `approval_request_changes`
--

CREATE TABLE `approval_request_changes` (
  `id` bigint UNSIGNED NOT NULL,
  `approval_request_id` bigint UNSIGNED NOT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_request_changes`
--

INSERT INTO `approval_request_changes` (`id`, `approval_request_id`, `field_name`, `old_value`, `new_value`, `created_at`, `updated_at`) VALUES
(1, 6, 'is_active', 'true', 'false', '2026-04-21 09:01:49', '2026-04-21 09:01:49'),
(2, 7, 'name', '\"Ban dieu hanh he thong\"', '\"Ban dieu hanh he thongg\"', '2026-04-21 09:02:36', '2026-04-21 09:02:36'),
(3, 7, 'description', '\"Nhom van hanh va quan tri toan bo he thong.\"', '\"Nhom van hanh va quan tri toan bo he thong.\"', '2026-04-21 09:02:36', '2026-04-21 09:02:36'),
(4, 7, 'manager_user_id', '1', '1', '2026-04-21 09:02:36', '2026-04-21 09:02:36'),
(5, 7, 'is_active', '1', 'true', '2026-04-21 09:02:36', '2026-04-21 09:02:36'),
(6, 15, 'user_id', 'null', '2', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(7, 15, 'employee_profile_id', 'null', '2', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(8, 15, 'employee_name', 'null', '\"HR Manager\"', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(9, 15, 'employee_email', 'null', '\"hr@gmail.com\"', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(10, 15, 'employee_code', 'null', '\"EMP-HR-001\"', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(11, 15, 'old_salary', 'null', '0', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(12, 15, 'new_salary', '0', '10000000', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(13, 15, 'currency', 'null', '\"VND\"', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(14, 15, 'effective_date', 'null', '\"2026-04-24\"', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(15, 15, 'request_reason', 'null', '\"HR de nghi thay doi luong co ban\"', '2026-04-24 01:18:33', '2026-04-24 01:18:33'),
(16, 16, 'name', '\"Ban dieu hanh he thong\"', '\"Ban dieu hanh he thongg\"', '2026-04-24 02:38:02', '2026-04-24 02:38:02'),
(17, 16, 'description', '\"Nhom van hanh va quan tri toan bo he thong.\"', '\"Nhom van hanh va quan tri toan bo he thong.\"', '2026-04-24 02:38:02', '2026-04-24 02:38:02'),
(18, 16, 'manager_user_id', '1', '1', '2026-04-24 02:38:02', '2026-04-24 02:38:02'),
(19, 16, 'is_active', '1', 'true', '2026-04-24 02:38:02', '2026-04-24 02:38:02'),
(20, 17, 'is_active', 'true', 'false', '2026-04-24 02:47:03', '2026-04-24 02:47:03'),
(21, 18, 'user_id', 'null', '2', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(22, 18, 'employee_profile_id', 'null', '2', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(23, 18, 'employee_name', 'null', '\"HR Manager\"', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(24, 18, 'employee_email', 'null', '\"hr@gmail.com\"', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(25, 18, 'employee_code', 'null', '\"EMP-HR-001\"', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(26, 18, 'old_salary', 'null', '10000000', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(27, 18, 'new_salary', '10000000', '100000000', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(28, 18, 'currency', 'null', '\"VND\"', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(29, 18, 'effective_date', 'null', '\"2026-04-24\"', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(30, 18, 'request_reason', 'null', '\"HR de nghi thay doi luong co ban\"', '2026-04-24 03:22:07', '2026-04-24 03:22:07'),
(31, 19, 'user_id', 'null', '2', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(32, 19, 'employee_profile_id', 'null', '2', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(33, 19, 'employee_name', 'null', '\"HR Manager\"', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(34, 19, 'employee_email', 'null', '\"hr@gmail.com\"', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(35, 19, 'employee_code', 'null', '\"EMP-HR-001\"', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(36, 19, 'old_salary', 'null', '10000000', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(37, 19, 'new_salary', '10000000', '1000000000', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(38, 19, 'currency', 'null', '\"VND\"', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(39, 19, 'effective_date', 'null', '\"2026-04-24\"', '2026-04-24 03:23:50', '2026-04-24 03:23:50'),
(40, 19, 'request_reason', 'null', '\"l\\u00e0m t\\u1ed1t\"', '2026-04-24 03:23:50', '2026-04-24 03:23:50');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_adjustments`
--

CREATE TABLE `attendance_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `attendance_record_id` bigint UNSIGNED NOT NULL,
  `approval_request_id` bigint UNSIGNED DEFAULT NULL,
  `old_check_in_at` datetime DEFAULT NULL,
  `new_check_in_at` datetime DEFAULT NULL,
  `old_check_out_at` datetime DEFAULT NULL,
  `new_check_out_at` datetime DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint UNSIGNED NOT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu de nghi sua cong check-in/check-out.';

--
-- Dumping data for table `attendance_adjustments`
--

INSERT INTO `attendance_adjustments` (`id`, `attendance_record_id`, `approval_request_id`, `old_check_in_at`, `new_check_in_at`, `old_check_out_at`, `new_check_out_at`, `reason`, `status`, `requested_by`, `reviewed_by`, `reviewed_at`, `review_note`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2026-04-20 08:07:39', NULL, NULL, '2026-04-20 12:00:00', 'tôi chưa check out', 'approved', 4, 1, '2026-04-20 02:36:40', NULL, '2026-04-20 02:30:28', '2026-04-20 02:36:40'),
(2, 4, 2, '2026-04-20 08:07:39', NULL, '2026-04-20 12:00:00', '2026-04-20 17:00:00', 'tôi bị sai giờ', 'rejected', 4, 1, '2026-04-20 02:41:00', 'chưa đủ yêu cầu đề ra', '2026-04-20 02:37:44', '2026-04-20 02:41:00'),
(3, 4, 3, '2026-04-20 08:07:39', NULL, '2026-04-20 12:00:00', '2026-04-20 17:00:00', 'rất ok', 'approved', 4, 1, '2026-04-20 03:46:26', 'ok', '2026-04-20 02:41:32', '2026-04-20 03:46:26'),
(4, 5, 8, '2026-04-21 07:59:37', NULL, NULL, '2026-04-21 17:30:00', 'quên chưa check out', 'pending', 1, NULL, NULL, NULL, '2026-04-22 04:30:47', '2026-04-22 04:30:47');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_approvals`
--

CREATE TABLE `attendance_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `attendance_record_id` bigint UNSIGNED NOT NULL,
  `approval_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by` bigint UNSIGNED NOT NULL,
  `approved_at` timestamp NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_approvals`
--

INSERT INTO `attendance_approvals` (`id`, `attendance_record_id`, `approval_type`, `approved_by`, `approved_at`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, 2, 'daily_confirmation', 1, '2026-04-17 10:19:42', 'rejected', 'quá trễ giờ', '2026-04-17 10:19:42', '2026-04-17 10:19:42'),
(2, 6, 'daily_confirmation', 1, '2026-04-21 10:33:11', 'approved', 'đi làm tốt', '2026-04-21 10:33:11', '2026-04-21 10:33:11'),
(3, 8, 'daily_confirmation', 1, '2026-04-22 09:03:10', 'rejected', 'Auto marked unpaid leave', '2026-04-22 09:03:10', '2026-04-22 09:03:10'),
(4, 9, 'daily_confirmation', 1, '2026-04-23 01:58:39', 'rejected', 'Auto marked unpaid leave', '2026-04-23 01:58:39', '2026-04-23 01:58:39'),
(5, 13, 'daily_confirmation', 1, '2026-04-29 02:15:33', 'approved', 'vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).', '2026-04-29 02:15:33', '2026-04-29 02:15:33'),
(6, 13, 'daily_confirmation', 1, '2026-04-29 02:16:34', 'approved', 'vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).', '2026-04-29 02:16:34', '2026-04-29 02:16:34'),
(7, 13, 'daily_confirmation', 1, '2026-04-29 02:21:45', 'approved', 'vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).', '2026-04-29 02:21:45', '2026-04-29 02:21:45'),
(8, 13, 'daily_confirmation', 1, '2026-04-29 02:22:02', 'approved', 'vi phạm | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình). | [Hệ thống] Duyệt thủ công ngày công có vi phạm (không kèm đơn giải trình).', '2026-04-29 02:22:02', '2026-04-29 02:22:02'),
(9, 13, 'daily_confirmation', 1, '2026-04-29 02:28:05', 'approved', 'Duyệt hàng loạt | Duyệt thủ công (Có vi phạm)', '2026-04-29 02:28:05', '2026-04-29 02:28:05');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_events`
--

CREATE TABLE `attendance_events` (
  `id` bigint UNSIGNED NOT NULL,
  `attendance_record_id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `event_type` enum('check_in','check_out','manual_adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_at` datetime NOT NULL,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_info` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_events`
--

INSERT INTO `attendance_events` (`id`, `attendance_record_id`, `employee_profile_id`, `event_type`, `event_at`, `source`, `ip_address`, `device_info`, `note`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'check_in', '2026-04-17 08:05:00', 'seed', NULL, NULL, NULL, 2, '2026-04-17 10:24:22', '2026-04-17 10:24:22'),
(2, 1, 3, 'check_out', '2026-04-17 17:30:00', 'seed', NULL, NULL, NULL, 2, '2026-04-17 10:24:22', '2026-04-17 10:24:22'),
(3, 2, 1, 'check_in', '2026-04-17 17:19:20', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-17 10:19:20', '2026-04-17 10:19:20'),
(4, 2, 1, 'check_out', '2026-04-17 17:19:24', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check out via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-17 10:19:24', '2026-04-17 10:19:24'),
(5, 3, 1, 'check_in', '2026-04-20 08:00:52', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-20 01:00:52', '2026-04-20 01:00:52'),
(6, 4, 4, 'check_in', '2026-04-20 08:07:39', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-20 01:07:39', '2026-04-20 01:07:39'),
(7, 4, 4, 'manual_adjustment', '2026-04-20 09:36:40', 'web', NULL, NULL, 'Manual adjustment approved #1', 1, '2026-04-20 02:36:40', '2026-04-20 02:36:40'),
(8, 4, 4, 'manual_adjustment', '2026-04-20 10:46:26', 'web', NULL, NULL, 'Manual adjustment approved #3', 1, '2026-04-20 03:46:26', '2026-04-20 03:46:26'),
(9, 5, 1, 'check_in', '2026-04-21 07:59:37', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-21 00:59:37', '2026-04-21 00:59:37'),
(10, 6, 4, 'check_in', '2026-04-21 08:00:12', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-21 01:00:12', '2026-04-21 01:00:12'),
(11, 6, 4, 'check_out', '2026-04-21 17:32:49', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check out via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-21 10:32:49', '2026-04-21 10:32:49'),
(12, 7, 4, 'check_in', '2026-04-22 08:05:57', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-22 01:05:57', '2026-04-22 01:05:57'),
(13, 10, 1, 'check_in', '2026-04-22 13:35:21', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-22 06:35:21', '2026-04-22 06:35:21'),
(14, 10, 1, 'check_out', '2026-04-22 13:44:49', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check out via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-22 06:44:49', '2026-04-22 06:44:49'),
(15, 12, 4, 'check_in', '2026-04-24 10:16:43', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-24 03:16:43', '2026-04-24 03:16:43'),
(16, 13, 4, 'check_in', '2026-04-28 08:28:17', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-28 01:28:17', '2026-04-28 01:28:17'),
(17, 14, 23, 'check_in', '2026-04-28 08:39:19', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'Check in via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 23, '2026-04-28 01:39:19', '2026-04-28 01:39:19'),
(18, 13, 4, 'check_out', '2026-04-28 17:32:02', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'Check out via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 4, '2026-04-28 10:32:02', '2026-04-28 10:32:02'),
(19, 14, 23, 'check_out', '2026-04-28 17:32:18', 'web', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'Check out via web - Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 23, '2026-04-28 10:32:18', '2026-04-28 10:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_monthly_summaries`
--

CREATE TABLE `attendance_monthly_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `total_working_days` int UNSIGNED NOT NULL DEFAULT '0',
  `total_present_days` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_absent_days` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_leave_days` int UNSIGNED NOT NULL DEFAULT '0',
  `total_unpaid_leave_days` int UNSIGNED NOT NULL DEFAULT '0',
  `total_business_trip_days` int UNSIGNED NOT NULL DEFAULT '0',
  `total_late_count` int UNSIGNED NOT NULL DEFAULT '0',
  `total_early_leave_count` int UNSIGNED NOT NULL DEFAULT '0',
  `total_overtime_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_monthly_summaries`
--

INSERT INTO `attendance_monthly_summaries` (`id`, `employee_profile_id`, `month`, `year`, `total_working_days`, `total_present_days`, `total_absent_days`, `total_leave_days`, `total_unpaid_leave_days`, `total_business_trip_days`, `total_late_count`, `total_early_leave_count`, `total_overtime_minutes`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 2026, 4, 0.00, 4.00, 0, 0, 0, 2, 0, 0, '2026-04-17 10:19:20', '2026-04-22 06:44:49'),
(2, 4, 4, 2026, 6, 5.00, 1.00, 1, 0, 0, 2, 0, 0, '2026-04-20 01:07:39', '2026-04-29 02:28:05'),
(3, 2, 4, 2026, 1, 0.00, 1.00, 0, 1, 0, 0, 0, 0, '2026-04-22 03:11:55', '2026-04-22 03:11:55'),
(4, 3, 4, 2026, 2, 1.00, 1.00, 0, 1, 0, 0, 0, 0, '2026-04-22 03:11:55', '2026-04-22 03:11:55'),
(5, 23, 4, 2026, 1, 1.00, 0.00, 0, 0, 0, 0, 0, 0, '2026-04-28 01:39:19', '2026-04-29 01:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_month_locks`
--

CREATE TABLE `attendance_month_locks` (
  `id` bigint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '1',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` bigint UNSIGNED DEFAULT NULL,
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `unlocked_by` bigint UNSIGNED DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang khoa bang cong theo ky thang va phong ban.';

--
-- Dumping data for table `attendance_month_locks`
--

INSERT INTO `attendance_month_locks` (`id`, `month`, `year`, `department_id`, `is_locked`, `locked_at`, `locked_by`, `unlocked_at`, `unlocked_by`, `note`, `created_at`, `updated_at`) VALUES
(1, 4, 2026, NULL, 0, '2026-04-24 03:44:59', 1, '2026-04-24 03:45:02', 1, 'chốt công', '2026-04-21 01:33:40', '2026-04-24 03:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `work_shift_id` bigint UNSIGNED DEFAULT NULL,
  `work_date` date NOT NULL,
  `check_in_at` datetime DEFAULT NULL,
  `check_in_ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in_device` text COLLATE utf8mb4_unicode_ci,
  `check_out_at` datetime DEFAULT NULL,
  `check_out_ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_device` text COLLATE utf8mb4_unicode_ci,
  `worked_minutes` int NOT NULL DEFAULT '0',
  `late_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `early_leave_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `overtime_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `missing_check_in` tinyint(1) NOT NULL DEFAULT '0',
  `missing_check_out` tinyint(1) NOT NULL DEFAULT '0',
  `attendance_status` enum('on_time','late','absent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'on_time',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `day_status` enum('present','late','early_leave','leave','unpaid_leave','business_trip','missing_check_in','missing_check_out','absent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `confirmed_by` bigint UNSIGNED DEFAULT NULL,
  `rejected_by` bigint UNSIGNED DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `approval_note` text COLLATE utf8mb4_unicode_ci,
  `shift_snapshot` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `employee_profile_id`, `work_shift_id`, `work_date`, `check_in_at`, `check_in_ip_address`, `check_in_device`, `check_out_at`, `check_out_ip_address`, `check_out_device`, `worked_minutes`, `late_minutes`, `early_leave_minutes`, `overtime_minutes`, `missing_check_in`, `missing_check_out`, `attendance_status`, `approval_status`, `day_status`, `is_confirmed`, `confirmed_by`, `rejected_by`, `confirmed_at`, `rejected_at`, `note`, `approval_note`, `shift_snapshot`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2026-04-17', '2026-04-17 08:05:00', NULL, NULL, '2026-04-17 17:30:00', NULL, NULL, 505, 0, 0, 0, 0, 0, 'on_time', 'approved', 'present', 1, 2, NULL, '2026-04-17 10:24:22', NULL, NULL, NULL, '{\"id\": 1, \"end_time\": \"17:00:00\", \"shift_name\": \"Ca hanh chinh\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:00:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": null, \"late_grace_minutes\": 15, \"overtime_start_time\": null, \"overtime_hourly_rate\": null, \"handover_break_minutes\": 0, \"early_leave_grace_minutes\": 15}', '2026-04-17 10:24:22', '2026-04-29 07:49:12'),
(2, 1, NULL, '2026-04-17', '2026-04-17 17:19:20', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 17:19:24', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 0, 549, 5, 0, 0, 0, 'on_time', 'rejected', 'early_leave', 0, NULL, 1, NULL, '2026-04-17 10:19:42', NULL, 'quá trễ giờ', NULL, '2026-04-17 10:19:20', '2026-04-22 10:19:47'),
(3, 1, NULL, '2026-04-20', '2026-04-20 08:00:52', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, NULL, NULL, 569, 0, 0, 0, 0, 1, 'on_time', 'pending', 'missing_check_out', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 01:00:52', '2026-04-22 10:19:47'),
(4, 4, 2, '2026-04-20', '2026-04-20 08:07:39', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 17:00:00', NULL, NULL, 442, 0, 0, 0, 0, 0, 'on_time', 'approved', 'present', 1, 1, NULL, '2026-04-24 09:53:51', NULL, 'Applied from request #8 | Formula: late_early(early_leave) => late:0->0, early:15->0, status:on_time->on_time', 'Giải trình về sớm ngày 20/4/2026, về sớm 15 phút.', '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"Ca hành chính sáng(mùa hè)\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-20 01:07:39', '2026-04-29 07:49:12'),
(5, 1, NULL, '2026-04-21', '2026-04-21 07:59:37', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, NULL, NULL, 570, 0, 0, 0, 0, 1, 'on_time', 'pending', 'missing_check_out', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-21 00:59:37', '2026-04-22 10:19:47'),
(6, 4, 2, '2026-04-21', '2026-04-21 08:00:12', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-21 17:32:49', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 482, 0, 0, 0, 0, 0, 'on_time', 'approved', 'present', 1, 1, NULL, '2026-04-21 10:33:11', NULL, 'đi làm tốt', 'đi làm tốt', '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"Ca hành chính sáng(mùa hè)\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-21 01:00:12', '2026-04-29 07:49:12'),
(7, 4, 2, '2026-04-22', '2026-04-22 08:05:57', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 17:30:00', NULL, NULL, 474, 0, 0, 0, 0, 0, 'on_time', 'approved', 'present', 1, 1, NULL, '2026-04-23 01:20:02', NULL, 'Applied from request #5 | Formula: forgot_check => missing_check_in:0->0, missing_check_out:1->0', 'quên ok', '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"Ca hành chính sáng(mùa hè)\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-22 01:05:57', '2026-04-29 07:49:12'),
(8, 2, 1, '2026-04-21', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'absent', 'rejected', 'absent', 0, NULL, 1, NULL, '2026-04-22 09:03:10', 'Auto marked unpaid leave', 'Auto marked unpaid leave', '{\"id\": 1, \"end_time\": \"17:00:00\", \"shift_name\": \"Ca hanh chinh\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:00:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": null, \"late_grace_minutes\": 15, \"overtime_start_time\": null, \"overtime_hourly_rate\": null, \"handover_break_minutes\": 0, \"early_leave_grace_minutes\": 15}', '2026-04-22 03:11:55', '2026-04-29 07:49:12'),
(9, 3, 1, '2026-04-21', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'absent', 'rejected', 'absent', 0, NULL, 1, NULL, '2026-04-23 01:58:39', 'Auto marked unpaid leave', 'Auto marked unpaid leave', '{\"id\": 1, \"end_time\": \"17:00:00\", \"shift_name\": \"Ca hanh chinh\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:00:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": null, \"late_grace_minutes\": 15, \"overtime_start_time\": null, \"overtime_hourly_rate\": null, \"handover_break_minutes\": 0, \"early_leave_grace_minutes\": 15}', '2026-04-22 03:11:55', '2026-04-29 07:49:12'),
(10, 1, 2, '2026-04-22', '2026-04-22 13:35:21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 13:44:49', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 9, 320, 210, 0, 0, 0, 'on_time', 'pending', 'early_leave', 0, NULL, NULL, NULL, NULL, NULL, NULL, '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"ca sáng hành chính\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-22 06:35:21', '2026-04-23 09:50:24'),
(11, 4, 2, '2026-04-23', NULL, NULL, NULL, NULL, NULL, NULL, 480, 0, 0, 0, 0, 0, 'absent', 'approved', 'leave', 1, 1, NULL, '2026-04-23 02:29:03', NULL, 'Applied from request #6 | Formula: leave => worked=0, late=0, early=0, status=absent, day=leave', 'dsdsfdsf', '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"Ca hành chính sáng(mùa hè)\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-23 02:29:03', '2026-04-29 07:49:12'),
(12, 4, 2, '2026-04-24', '2026-04-24 10:16:43', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 17:30:00', NULL, NULL, 343, 121, 0, 0, 0, 0, 'late', 'approved', 'late', 1, 1, NULL, '2026-04-28 01:27:25', NULL, 'Applied from request #9 | Formula: forgot_check => missing_check_in:0->0, missing_check_out:1->0', 'Giải trình bổ sung check-out ngày 24/4/2026.', '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"Ca hành chính sáng(mùa hè)\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-24 03:16:43', '2026-04-29 07:49:12'),
(13, 4, 2, '2026-04-28', '2026-04-28 08:28:17', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 17:32:02', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 453, 13, 0, 0, 0, 0, 'late', 'approved', 'late', 1, 1, NULL, '2026-04-29 02:28:05', NULL, 'Duyệt hàng loạt | Duyệt thủ công (Có vi phạm)', 'Duyệt hàng loạt | Duyệt thủ công (Có vi phạm)', '{\"id\": 2, \"end_time\": \"17:30:00\", \"shift_name\": \"Ca hành chính sáng(mùa hè)\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:30:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": \"20:00:00\", \"late_grace_minutes\": 15, \"overtime_start_time\": \"17:30:00\", \"overtime_hourly_rate\": \"50000.00\", \"handover_break_minutes\": 30, \"early_leave_grace_minutes\": 15}', '2026-04-28 01:28:17', '2026-04-29 07:49:12'),
(14, 23, 1, '2026-04-28', '2026-04-28 08:39:19', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-28 17:32:18', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 472, 0, 0, 0, 0, 0, 'on_time', 'approved', 'present', 1, 1, NULL, '2026-04-29 01:19:59', NULL, 'Applied from request #10 | Formula: late_early(late) => late:24->0, early:0->0, status:late->on_time', 'Giải trình đi muộn ngày 28/4/2026, đi muộn 24 phút.', '{\"id\": 1, \"end_time\": \"17:00:00\", \"shift_name\": \"Ca hanh chinh\", \"start_time\": \"08:00:00\", \"is_overnight\": false, \"grace_minutes\": 15, \"break_end_time\": \"13:00:00\", \"allows_overtime\": true, \"break_start_time\": \"12:00:00\", \"half_day_minutes\": 240, \"standard_minutes\": 480, \"overtime_end_time\": null, \"late_grace_minutes\": 15, \"overtime_start_time\": null, \"overtime_hourly_rate\": null, \"handover_break_minutes\": 0, \"early_leave_grace_minutes\": 15}', '2026-04-28 01:39:19', '2026-04-29 07:49:12');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_requests`
--

CREATE TABLE `attendance_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `approval_request_id` bigint UNSIGNED DEFAULT NULL,
  `request_type` enum('leave','late_early','forgot_check','business_trip','make_up') COLLATE utf8mb4_unicode_ci NOT NULL,
  `leave_type_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `request_date` date DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `from_time` time DEFAULT NULL,
  `to_time` time DEFAULT NULL,
  `leave_type` enum('paid','unpaid') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_days` decimal(8,2) NOT NULL DEFAULT '0.00',
  `leave_duration_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `requested_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_trip_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make_up_related_leave_date` date DEFAULT NULL,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `applied_at` timestamp NULL DEFAULT NULL,
  `applied_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu don de nghi cham cong cua nhan vien.';

--
-- Dumping data for table `attendance_requests`
--

INSERT INTO `attendance_requests` (`id`, `employee_profile_id`, `approval_request_id`, `request_type`, `leave_type_id`, `status`, `request_date`, `from_date`, `to_date`, `from_time`, `to_time`, `leave_type`, `leave_days`, `leave_duration_type`, `leave_hours`, `requested_status`, `business_trip_location`, `make_up_related_leave_date`, `attachment_path`, `reason`, `applied_at`, `applied_by`, `created_at`, `updated_at`) VALUES
(1, 4, 5, 'leave', 2, 'rejected', NULL, '2026-04-22', '2026-04-24', NULL, NULL, 'paid', 3.00, 'full_day', 0.00, NULL, NULL, NULL, 'attendance-requests/74oXzym7qcUZ48dyZdWlaFQQclkiQTZcuELe42Qz.jpg', 'cần đi bar', NULL, NULL, '2026-04-21 07:25:56', '2026-04-21 07:27:12'),
(2, 1, 9, 'forgot_check', NULL, 'pending', '2026-04-22', NULL, NULL, NULL, '12:00:00', NULL, 0.00, NULL, 0.00, NULL, NULL, NULL, NULL, 'sdfsf', NULL, NULL, '2026-04-22 04:39:06', '2026-04-22 04:39:06'),
(3, 4, 10, 'forgot_check', NULL, 'rejected', '2026-04-22', NULL, NULL, NULL, '12:00:00', NULL, 0.00, NULL, 0.00, NULL, NULL, NULL, NULL, 'dfssdf', NULL, NULL, '2026-04-22 04:39:53', '2026-04-22 04:40:22'),
(4, 1, 11, 'leave', 2, 'pending', NULL, '2026-04-23', '2026-04-23', NULL, NULL, 'paid', 1.00, 'full_day', 0.00, NULL, NULL, NULL, 'attendance-requests/IAx7DeuqJYSXrNf401Dy2Y2jnx1TjB2v27ZhkR7I.jpg', 'ok', NULL, NULL, '2026-04-22 06:40:51', '2026-04-22 06:40:51'),
(5, 4, 12, 'forgot_check', NULL, 'approved', '2026-04-22', NULL, NULL, NULL, '17:30:00', NULL, 0.00, NULL, 0.00, 'late', NULL, NULL, NULL, 'quên', '2026-04-23 01:20:02', 1, '2026-04-23 01:19:31', '2026-04-23 01:20:02'),
(6, 4, 13, 'leave', 2, 'approved', NULL, '2026-04-23', '2026-04-23', NULL, NULL, 'paid', 1.00, 'full_day', 0.00, 'late', NULL, NULL, 'attendance-requests/yloXK6uRc0VoXem3nC9Jfgff5iOSYl4uJKZnFCTo.jpg', 'dsdsfdsf', '2026-04-23 02:29:03', 1, '2026-04-23 02:28:35', '2026-04-23 02:29:03'),
(7, 4, 14, 'late_early', NULL, 'cancelled', '2026-04-24', NULL, NULL, '12:00:00', '13:00:00', NULL, 0.00, NULL, 0.00, 'early_leave', NULL, NULL, NULL, 'Giai trinh ve som ngay 20/4/2026.', NULL, NULL, '2026-04-23 08:11:42', '2026-04-23 08:21:49'),
(8, 4, 20, 'late_early', NULL, 'approved', '2026-04-20', NULL, NULL, '17:00:00', '17:30:00', NULL, 0.00, NULL, 0.00, 'early_leave', NULL, NULL, NULL, 'Giải trình về sớm ngày 20/4/2026, về sớm 15 phút.', '2026-04-24 09:53:51', 1, '2026-04-24 09:10:51', '2026-04-24 09:53:51'),
(9, 4, 21, 'forgot_check', NULL, 'approved', '2026-04-24', NULL, NULL, NULL, '17:30:00', NULL, 0.00, NULL, 0.00, 'late', NULL, NULL, NULL, 'Giải trình bổ sung check-out ngày 24/4/2026.', '2026-04-28 01:27:25', 1, '2026-04-28 01:27:02', '2026-04-28 01:27:25'),
(10, 23, 22, 'late_early', NULL, 'approved', '2026-04-28', NULL, NULL, '08:00:00', '08:39:00', NULL, 0.00, NULL, 0.00, 'late', NULL, NULL, NULL, 'Giải trình đi muộn ngày 28/4/2026, đi muộn 24 phút.', '2026-04-29 01:19:59', 1, '2026-04-29 01:19:41', '2026-04-29 01:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `authority_levels`
--

CREATE TABLE `authority_levels` (
  `id` bigint UNSIGNED NOT NULL,
  `rank` smallint UNSIGNED NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang danh muc cac muc tham quyen theo thu bac.';

--
-- Dumping data for table `authority_levels`
--

INSERT INTO `authority_levels` (`id`, `rank`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Muc 1 - Nhan vien', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(2, 2, 'Muc 2 - To pho / Senior', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(3, 3, 'Muc 3 - Truong nhom', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(4, 4, 'Muc 4 - Truong phong', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(5, 5, 'Muc 5 - Giam doc / Quan ly cao', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(6, 6, 'Muc 6 - Giam doc khoi / VP', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(7, 7, 'Muc 7 - Pho tong giam doc', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(8, 8, 'Muc 8 - Tong giam doc', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(9, 9, 'Muc 9 - Hoi dong quan tri', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(10, 10, 'Muc 10 - Quan tri he thong / Admin', 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('hrm-cache-feedback:auto-escalation:last-check', 'i:1777372347;', 1777372647);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `manager_user_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `manager_user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Ban điều hành hệ thống', 'Nhom van hanh va quan tri toan bo he thong.', 1, 1, '2026-04-17 09:50:45', '2026-04-28 01:02:31'),
(2, 'Phòng nhân sự', 'Quan ly nhan su va cham cong.', 2, 1, '2026-04-17 09:50:45', '2026-04-28 01:01:53'),
(3, 'Phòng kĩ thuật', 'Phat trien du an va van hanh ky thuat.', 1, 1, '2026-04-17 09:50:45', '2026-04-28 01:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `receiver_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_summary` text COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`id`, `sender_id`, `receiver_email`, `subject`, `body_summary`, `sent_at`, `status`, `error_message`, `created_at`, `updated_at`) VALUES
(1, 4, 'hr@gmail.com', '[Feedback] Phan hoi moi: Tôi muốn tăng lương', 'feedback_message_id=1; from=dophuhieu15@gmail.com; to_position=Truong phong HR; type=new_feedback', '2026-04-20 01:46:42', 'success', NULL, '2026-04-20 01:46:42', '2026-04-20 01:46:42'),
(2, 4, 'hr@gmail.com', '[Feedback] Phan hoi moi: haha', 'feedback_message_id=2; from=dophuhieu15@gmail.com; to_position=Truong phong HR; type=new_feedback', '2026-04-20 09:22:51', 'success', NULL, '2026-04-20 09:22:51', '2026-04-20 09:22:51'),
(3, 4, 'hr@gmail.com', '[Feedback] Phan hoi moi: đánh cầu', 'feedback_message_id=3; from=dophuhieu15@gmail.com; to_position=Truong phong HR; type=new_feedback', '2026-04-20 09:42:40', 'success', NULL, '2026-04-20 09:42:40', '2026-04-20 09:42:40'),
(4, 4, 'gtvbehieu@gmail.com', '[Feedback] Escalated: Tôi muốn tăng lương', 'feedback_message_id=1; from=dophuhieu15@gmail.com; to_position=System Full Access; type=feedback_escalation', '2026-04-21 07:50:52', 'success', NULL, '2026-04-21 07:50:52', '2026-04-21 07:50:52'),
(5, 1, 'dophuhieu15@gmail.com', '[Feedback] Yeu cau da duoc tra loi: Tôi muốn tăng lương', 'feedback_message_id=1; sender_id=4; type=reply', '2026-04-21 07:52:05', 'success', NULL, '2026-04-21 07:52:05', '2026-04-21 07:52:05'),
(6, 4, 'gtvbehieu@gmail.com', '[Feedback] Co tin nhan moi: Tôi muốn tăng lương', 'feedback_message_id=1; actor_id=4; type=thread_reply', '2026-04-21 07:52:48', 'success', NULL, '2026-04-21 07:52:48', '2026-04-21 07:52:48'),
(7, 1, 'dophuhieu15@gmail.com', '[Feedback] Yeu cau da duoc tra loi: Tôi muốn tăng lương', 'feedback_message_id=1; sender_id=4; type=reply', '2026-04-21 08:22:42', 'success', NULL, '2026-04-21 08:22:42', '2026-04-21 08:22:42'),
(8, 4, 'hr@gmail.com', '[Feedback] Co tin nhan moi: đánh cầu', 'feedback_message_id=3; actor_id=4; type=thread_reply', '2026-04-21 08:38:10', 'success', NULL, '2026-04-21 08:38:10', '2026-04-21 08:38:10'),
(9, 4, 'employee1@gmail.com', '[Feedback] Escalated: đánh cầu', 'feedback_message_id=3; from=dophuhieu15@gmail.com; to_position=Lap trinh vien; type=feedback_escalation', '2026-04-22 02:53:23', 'success', NULL, '2026-04-22 02:53:23', '2026-04-22 02:53:23'),
(10, 4, 'gtvbehieu@gmail.com', '[Feedback] Escalated: đánh cầu', 'feedback_message_id=3; from=dophuhieu15@gmail.com; to_position=System Full Access; type=feedback_escalation', '2026-04-23 03:59:58', 'success', NULL, '2026-04-23 03:59:58', '2026-04-23 03:59:58'),
(11, 1, 'dophuhieu15@gmail.com', '[Feedback] Yeu cau da duoc tra loi: đánh cầu', 'feedback_message_id=3; sender_id=4; type=reply', '2026-04-23 04:03:06', 'success', NULL, '2026-04-23 04:03:06', '2026-04-23 04:03:06'),
(12, 1, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu cập nhật phòng ban đã bị từ chối', 'Yêu cầu cập nhật phòng ban', '2026-04-24 02:45:50', 'success', NULL, '2026-04-24 02:45:50', '2026-04-24 02:45:50'),
(13, 1, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu khóa/mở phòng ban đã bị từ chối', 'Yêu cầu khóa/mở phòng ban', '2026-04-24 02:47:52', 'success', NULL, '2026-04-24 02:47:52', '2026-04-24 02:47:52'),
(14, 1, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu đổi lương cơ bản đã bị từ chối', 'Yêu cầu đổi lương cơ bản', '2026-04-24 03:23:34', 'success', NULL, '2026-04-24 03:23:34', '2026-04-24 03:23:34'),
(15, 1, 'dophuhieu15@gmail.com', '[HRM] Yêu cầu đổi lương cơ bản đã bị từ chối', 'Yêu cầu đổi lương cơ bản', '2026-04-24 03:25:14', 'success', NULL, '2026-04-24 03:25:14', '2026-04-24 03:25:14'),
(16, 1, 'dophuhieu15@gmail.com', '[HRM] Đơn xin đi muộn / về sớm đã được duyệt', 'Đơn xin đi muộn / về sớm', '2026-04-24 09:53:56', 'success', NULL, '2026-04-24 09:53:56', '2026-04-24 09:53:56'),
(17, 1, 'dophuhieu15@gmail.com', '[HRM] Đơn xin quên chấm công đã được duyệt', 'Đơn xin quên chấm công', '2026-04-28 01:27:29', 'success', NULL, '2026-04-28 01:27:29', '2026-04-28 01:27:29'),
(18, 1, 'thisoma1308@gmail.com', '[HRM] Cập nhật lương cơ bản đã được duyệt', 'Cập nhật lương cơ bản', '2026-04-28 09:41:44', 'success', NULL, '2026-04-28 09:41:44', '2026-04-28 09:41:44'),
(19, 1, 'thisoma1308@gmail.com', '[HRM] Cập nhật lương cơ bản đã được duyệt', 'Cập nhật lương cơ bản', '2026-04-28 09:42:06', 'success', NULL, '2026-04-28 09:42:06', '2026-04-28 09:42:06'),
(20, 1, 'thisoma1308@gmail.com', '[HRM] Đơn xin đi muộn / về sớm đã được duyệt', 'Đơn xin đi muộn / về sớm', '2026-04-29 01:20:05', 'success', NULL, '2026-04-29 01:20:05', '2026-04-29 01:20:05'),
(21, 1, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026', '2026-04-29 02:15:38', 'success', NULL, '2026-04-29 02:15:38', '2026-04-29 02:15:38'),
(22, 1, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026', '2026-04-29 02:16:39', 'success', NULL, '2026-04-29 02:16:39', '2026-04-29 02:16:39'),
(23, 1, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026', '2026-04-29 02:21:49', 'success', NULL, '2026-04-29 02:21:49', '2026-04-29 02:21:49'),
(24, 1, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026', '2026-04-29 02:22:07', 'success', NULL, '2026-04-29 02:22:07', '2026-04-29 02:22:07'),
(25, 1, 'dophuhieu15@gmail.com', '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026', '2026-04-29 02:28:08', 'success', NULL, '2026-04-29 02:28:08', '2026-04-29 02:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `employee_department_histories`
--

CREATE TABLE `employee_department_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `old_department_id` bigint UNSIGNED DEFAULT NULL,
  `new_department_id` bigint UNSIGNED NOT NULL,
  `changed_at` timestamp NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_balances`
--

CREATE TABLE `employee_leave_balances` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `opening_balance` decimal(8,2) NOT NULL DEFAULT '0.00',
  `accrued_days` decimal(8,2) NOT NULL DEFAULT '0.00',
  `used_days` decimal(8,2) NOT NULL DEFAULT '0.00',
  `pending_days` decimal(8,2) NOT NULL DEFAULT '0.00',
  `adjusted_days` decimal(8,2) NOT NULL DEFAULT '0.00',
  `carryover_expires_on` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_leave_balances`
--

INSERT INTO `employee_leave_balances` (`id`, `employee_profile_id`, `leave_type_id`, `year`, `opening_balance`, `accrued_days`, `used_days`, `pending_days`, `adjusted_days`, `carryover_expires_on`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2026, 0.00, 8.25, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(2, 1, 2, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(3, 1, 3, 2026, 0.00, 1.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(4, 1, 4, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(5, 1, 5, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(6, 1, 6, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(7, 1, 7, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(8, 2, 1, 2026, 0.00, 8.25, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(9, 2, 2, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(10, 2, 3, 2026, 0.00, 1.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(11, 2, 4, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(12, 2, 5, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(13, 2, 6, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(14, 2, 7, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(15, 3, 1, 2026, 0.00, 8.25, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(16, 3, 2, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(17, 3, 3, 2026, 0.00, 1.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(18, 3, 4, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(19, 3, 5, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(20, 3, 6, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(21, 3, 7, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(22, 4, 1, 2026, 0.00, 8.25, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(23, 4, 2, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(24, 4, 3, 2026, 0.00, 1.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(25, 4, 4, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(26, 4, 5, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(27, 4, 6, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(28, 4, 7, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(29, 23, 1, 2026, 0.00, 8.25, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(30, 23, 2, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(31, 23, 3, 2026, 0.00, 1.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(32, 23, 4, 2026, 0.00, 3.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(33, 23, 5, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(34, 23, 6, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(35, 23, 7, 2026, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-04-28 09:01:03', '2026-04-28 09:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `employee_position_histories`
--

CREATE TABLE `employee_position_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `old_position_id` bigint UNSIGNED DEFAULT NULL,
  `new_position_id` bigint UNSIGNED NOT NULL,
  `changed_at` timestamp NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_profiles`
--

CREATE TABLE `employee_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `employee_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `position_id` bigint UNSIGNED DEFAULT NULL,
  `default_work_shift_id` bigint UNSIGNED DEFAULT NULL,
  `reports_to_user_id` bigint UNSIGNED DEFAULT NULL,
  `province_id` bigint UNSIGNED DEFAULT NULL,
  `ward_id` bigint UNSIGNED DEFAULT NULL,
  `address_line` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `hire_date` date NOT NULL,
  `termination_date` date DEFAULT NULL,
  `base_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `salary_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `employment_status` enum('active','inactive','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `employment_type` enum('probation','official','intern','collaborator') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'official',
  `is_department_head` tinyint(1) NOT NULL DEFAULT '0',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_profiles`
--

INSERT INTO `employee_profiles` (`id`, `user_id`, `employee_code`, `department_id`, `position_id`, `default_work_shift_id`, `reports_to_user_id`, `province_id`, `ward_id`, `address_line`, `date_of_birth`, `hire_date`, `termination_date`, `base_salary`, `salary_currency`, `employment_status`, `employment_type`, `is_department_head`, `locked_at`, `locked_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'EMP-GTVBEHIEU', 1, 1, NULL, NULL, 45, 851, 'tiên cảnh trần gian', '2026-04-22', '2026-04-17', NULL, 0.00, 'VND', 'active', 'official', 1, NULL, NULL, '2026-04-17 09:50:45', '2026-04-22 03:49:59'),
(2, 2, 'EMP-HR-001', 2, 2, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-17', NULL, 10000000.00, 'VND', 'active', 'official', 1, NULL, NULL, '2026-04-17 09:50:45', '2026-04-24 01:29:32'),
(3, 3, 'EMP-001', 3, 3, 1, NULL, 35, 1, 'So 1, duong 2', '1995-05-10', '2026-04-18', NULL, 15000000.00, 'VND', 'active', 'official', 0, NULL, NULL, '2026-04-17 09:50:45', '2026-04-22 03:03:07'),
(4, 4, 'EMP-002', 3, 3, NULL, NULL, 49, 1345, 'hà nam', '2003-04-10', '2026-04-20', NULL, 5000000.00, 'VND', 'active', 'official', 0, NULL, NULL, '2026-04-20 01:04:05', '2026-04-24 03:34:36'),
(23, 23, 'EMP-003', 2, 2, NULL, NULL, 35, 3, 'Ba đình', '2003-04-03', '2026-04-28', NULL, 7500000.00, 'VND', 'active', 'official', 0, NULL, NULL, '2026-04-28 01:33:46', '2026-04-28 09:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `employee_status_logs`
--

CREATE TABLE `employee_status_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `old_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_status_logs`
--

INSERT INTO `employee_status_logs` (`id`, `employee_profile_id`, `old_status`, `new_status`, `reason`, `changed_by`, `created_at`, `updated_at`) VALUES
(1, 4, 'active', 'terminated', 'Updated from user employment status action', 1, '2026-04-24 03:34:33', '2026-04-24 03:34:33'),
(2, 4, 'terminated', 'active', 'Updated from user employment status action', 1, '2026-04-24 03:34:36', '2026-04-24 03:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `employee_work_shift_assignments`
--

CREATE TABLE `employee_work_shift_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `work_shift_id` bigint UNSIGNED NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `weekdays` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang phan ca linh hoat theo nhan vien/phong ban.';

--
-- Dumping data for table `employee_work_shift_assignments`
--

INSERT INTO `employee_work_shift_assignments` (`id`, `employee_profile_id`, `department_id`, `work_shift_id`, `effective_from`, `effective_to`, `weekdays`, `is_active`, `note`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 1, '2026-04-10', NULL, '[1, 2, 3, 4, 5]', 0, NULL, 1, '2026-04-20 04:27:58', '2026-04-29 01:52:15'),
(2, 4, NULL, 2, '2026-04-01', '2026-04-30', '[1, 2, 3, 4, 5, 6]', 0, NULL, 1, '2026-04-20 04:29:01', '2026-04-29 01:52:17'),
(3, 4, NULL, 1, '2026-05-01', '2026-05-02', '[1, 2, 3, 4, 5, 6]', 0, NULL, 1, '2026-04-20 04:34:14', '2026-04-29 01:52:18'),
(4, NULL, 3, 2, '2026-04-22', NULL, '[1, 2, 3, 4, 5]', 0, NULL, 1, '2026-04-22 03:14:00', '2026-04-29 01:52:20'),
(5, NULL, 1, 2, '2026-04-22', NULL, '[1, 2, 3, 4, 5]', 0, NULL, 1, '2026-04-22 09:18:42', '2026-04-29 01:52:21'),
(30, NULL, NULL, 2, '2026-04-01', '2026-06-30', '[1, 2, 3, 4, 5]', 1, NULL, 1, '2026-04-24 01:07:54', '2026-04-29 01:52:42'),
(31, NULL, NULL, 3, '2026-07-01', '2026-12-31', '[1, 2, 3, 4, 5]', 1, NULL, 1, '2026-04-29 01:51:58', '2026-04-29 01:51:58');

-- --------------------------------------------------------

--
-- Table structure for table `export_histories`
--

CREATE TABLE `export_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filter_data` json DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exported_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `export_histories`
--

INSERT INTO `export_histories` (`id`, `user_id`, `module`, `file_type`, `filter_data`, `file_path`, `exported_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-22 03:52:06', '2026-04-22 03:52:06', '2026-04-22 03:52:06'),
(2, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-23 10:07:10', '2026-04-23 10:07:10', '2026-04-23 10:07:10'),
(3, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 06:52:52', '2026-04-28 06:52:52', '2026-04-28 06:52:52'),
(4, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:01:49', '2026-04-28 07:01:49', '2026-04-28 07:01:49'),
(5, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:06:17', '2026-04-28 07:06:17', '2026-04-28 07:06:17'),
(6, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:12:20', '2026-04-28 07:12:20', '2026-04-28 07:12:20'),
(7, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:12:27', '2026-04-28 07:12:27', '2026-04-28 07:12:27'),
(8, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:12:34', '2026-04-28 07:12:34', '2026-04-28 07:12:34'),
(9, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:13:29', '2026-04-28 07:13:29', '2026-04-28 07:13:29'),
(10, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:18:24', '2026-04-28 07:18:24', '2026-04-28 07:18:24'),
(11, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:20:43', '2026-04-28 07:20:43', '2026-04-28 07:20:43'),
(12, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:20:48', '2026-04-28 07:20:48', '2026-04-28 07:20:48'),
(13, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:21:37', '2026-04-28 07:21:37', '2026-04-28 07:21:37'),
(14, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:21:41', '2026-04-28 07:21:41', '2026-04-28 07:21:41'),
(15, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:21:53', '2026-04-28 07:21:53', '2026-04-28 07:21:53'),
(16, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:22:08', '2026-04-28 07:22:08', '2026-04-28 07:22:08'),
(17, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:22:12', '2026-04-28 07:22:12', '2026-04-28 07:22:12'),
(18, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:22:45', '2026-04-28 07:22:45', '2026-04-28 07:22:45'),
(19, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:24:45', '2026-04-28 07:24:45', '2026-04-28 07:24:45'),
(20, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:25:33', '2026-04-28 07:25:33', '2026-04-28 07:25:33'),
(21, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:25:50', '2026-04-28 07:25:50', '2026-04-28 07:25:50'),
(22, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 07:28:50', '2026-04-28 07:28:50', '2026-04-28 07:28:50'),
(23, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:22:17', '2026-04-28 08:22:17', '2026-04-28 08:22:17'),
(24, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:26:55', '2026-04-28 08:26:55', '2026-04-28 08:26:55'),
(25, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:30:37', '2026-04-28 08:30:37', '2026-04-28 08:30:37'),
(26, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:33:56', '2026-04-28 08:33:56', '2026-04-28 08:33:56'),
(27, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:33:57', '2026-04-28 08:33:57', '2026-04-28 08:33:57'),
(28, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:34:02', '2026-04-28 08:34:02', '2026-04-28 08:34:02'),
(29, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:35:09', '2026-04-28 08:35:09', '2026-04-28 08:35:09'),
(30, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:35:50', '2026-04-28 08:35:50', '2026-04-28 08:35:50'),
(31, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 08:36:14', '2026-04-28 08:36:14', '2026-04-28 08:36:14'),
(32, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 09:34:01', '2026-04-28 09:34:01', '2026-04-28 09:34:01'),
(33, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-28 10:31:37', '2026-04-28 10:31:37', '2026-04-28 10:31:37'),
(34, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-29 02:30:47', '2026-04-29 02:30:47', '2026-04-29 02:30:47'),
(35, 1, 'attendance', 'pdf', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-29 02:43:54', '2026-04-29 02:43:54', '2026-04-29 02:43:54'),
(36, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 4, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-29 02:44:28', '2026-04-29 02:44:28', '2026-04-29 02:44:28'),
(37, 1, 'attendance', 'excel', '{\"year\": 2026, \"month\": 3, \"years\": [{\"label\": \"2023\", \"value\": 2023}, {\"label\": \"2024\", \"value\": 2024}, {\"label\": \"2025\", \"value\": 2025}, {\"label\": \"2026\", \"value\": 2026}, {\"label\": \"2027\", \"value\": 2027}], \"months\": [{\"label\": \"Tháng 01\", \"value\": 1}, {\"label\": \"Tháng 02\", \"value\": 2}, {\"label\": \"Tháng 03\", \"value\": 3}, {\"label\": \"Tháng 04\", \"value\": 4}, {\"label\": \"Tháng 05\", \"value\": 5}, {\"label\": \"Tháng 06\", \"value\": 6}, {\"label\": \"Tháng 07\", \"value\": 7}, {\"label\": \"Tháng 08\", \"value\": 8}, {\"label\": \"Tháng 09\", \"value\": 9}, {\"label\": \"Tháng 10\", \"value\": 10}, {\"label\": \"Tháng 11\", \"value\": 11}, {\"label\": \"Tháng 12\", \"value\": 12}], \"keyword\": \"\", \"employee_profile_id\": null}', NULL, '2026-04-29 06:38:50', '2026-04-29 06:38:50', '2026-04-29 06:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_escalations`
--

CREATE TABLE `feedback_escalations` (
  `id` bigint UNSIGNED NOT NULL,
  `feedback_message_id` bigint UNSIGNED NOT NULL,
  `from_position_id` bigint UNSIGNED DEFAULT NULL,
  `to_position_id` bigint UNSIGNED NOT NULL,
  `escalation_count` smallint UNSIGNED NOT NULL DEFAULT '1',
  `escalated_at` timestamp NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback_escalations`
--

INSERT INTO `feedback_escalations` (`id`, `feedback_message_id`, `from_position_id`, `to_position_id`, `escalation_count`, `escalated_at`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 1, '2026-04-21 07:50:47', 'Timeout: feedback unreplied past escalation threshold', '2026-04-21 07:50:47', '2026-04-21 07:50:47'),
(2, 3, 2, 3, 1, '2026-04-22 02:53:19', 'Timeout: feedback unreplied past escalation threshold', '2026-04-22 02:53:19', '2026-04-22 02:53:19'),
(3, 3, 3, 1, 2, '2026-04-23 03:59:54', 'Timeout: feedback unreplied past escalation threshold', '2026-04-23 03:59:54', '2026-04-23 03:59:54');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_messages`
--

CREATE TABLE `feedback_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED DEFAULT NULL,
  `receiver_position_id` bigint UNSIGNED DEFAULT NULL,
  `receiver_group` enum('admin','hr','specific_user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hr',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('sent','read','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `conversation_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting_handler',
  `waiting_for` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_replied` tinyint(1) NOT NULL DEFAULT '0',
  `reply_message` text COLLATE utf8mb4_unicode_ci,
  `replied_by` bigint UNSIGNED DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `last_escalated_at` timestamp NULL DEFAULT NULL,
  `escalation_count` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback_messages`
--

INSERT INTO `feedback_messages` (`id`, `sender_id`, `receiver_id`, `receiver_position_id`, `receiver_group`, `subject`, `message`, `status`, `conversation_status`, `waiting_for`, `read_at`, `is_replied`, `reply_message`, `replied_by`, `replied_at`, `resolved_at`, `closed_at`, `last_escalated_at`, `escalation_count`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, 1, 'specific_user', 'Tôi muốn tăng lương', 'sau nhiều năm cống hiến cho cty tôi hiện giờ tôi cảm thấy mình mức lương ở hiện tại chưa phù hợp với năng lực của tôi , tôi xin muốn được tăng lương lên', 'read', 'resolved', NULL, '2026-04-21 07:54:04', 1, 'lên gặp nhân sự để làm đơn nhé', 1, '2026-04-21 08:22:38', '2026-04-21 08:22:38', NULL, '2026-04-21 07:50:47', 1, '2026-04-20 01:46:38', '2026-04-21 08:22:38'),
(2, 4, NULL, 2, 'specific_user', 'haha', 'xin về sớm', 'sent', 'waiting_handler', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-20 09:22:47', '2026-04-20 09:22:47'),
(3, 4, NULL, 1, 'specific_user', 'đánh cầu', 'saf', 'read', 'resolved', NULL, '2026-04-23 04:02:29', 1, 's', 1, '2026-04-23 04:03:02', '2026-04-23 04:03:02', NULL, '2026-04-23 03:59:54', 2, '2026-04-20 09:42:36', '2026-04-23 04:03:02');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_replies`
--

CREATE TABLE `feedback_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `feedback_message_id` bigint UNSIGNED NOT NULL,
  `replied_by` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu cac phan hoi theo chuoi hoi-thoai feedback.';

--
-- Dumping data for table `feedback_replies`
--

INSERT INTO `feedback_replies` (`id`, `feedback_message_id`, `replied_by`, `message`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'bảo hr hướng dẫn làm đơn báo cáo trình lên cấp trên để duyệt', '2026-04-21 07:52:00', '2026-04-21 07:52:00'),
(2, 1, 4, 'oki', '2026-04-21 07:52:44', '2026-04-21 07:52:44'),
(3, 1, 1, 'ok', '2026-04-21 08:21:28', '2026-04-21 08:21:28'),
(4, 1, 1, 'lên gặp nhân sự để làm đơn nhé', '2026-04-21 08:22:38', '2026-04-21 08:22:38'),
(5, 3, 4, 'sdfdsf', '2026-04-21 08:38:06', '2026-04-21 08:38:06'),
(6, 3, 1, 's', '2026-04-23 04:03:02', '2026-04-23 04:03:02');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint UNSIGNED NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `holiday_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `is_paid_leave` tinyint(1) NOT NULL DEFAULT '1',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `holiday_date`, `holiday_name`, `holiday_type`, `is_paid_leave`, `is_recurring`, `note`, `created_at`, `updated_at`) VALUES
(1, '2026-02-16', 'Tết Nguyên Đán (Ngày 1) 2026', 'public', 1, 0, NULL, '2026-04-28 09:31:18', '2026-04-28 09:31:18'),
(2, '2026-02-17', 'Tết Nguyên Đán (Ngày 2) 2026', 'public', 1, 0, NULL, '2026-04-28 09:31:18', '2026-04-28 09:31:18'),
(3, '2026-02-18', 'Tết Nguyên Đán (Ngày 3) 2026', 'public', 1, 0, NULL, '2026-04-28 09:31:18', '2026-04-28 09:31:18'),
(4, '2026-02-19', 'Tết Nguyên Đán (Ngày 4) 2026', 'public', 1, 0, NULL, '2026-04-28 09:31:18', '2026-04-28 09:31:18'),
(5, '2026-02-20', 'Tết Nguyên Đán (Ngày 5) 2026', 'public', 1, 0, NULL, '2026-04-28 09:31:18', '2026-04-28 09:31:18'),
(6, '2026-04-26', 'Giỗ tổ Hùng Vương 2026', 'public', 1, 0, NULL, '2026-04-28 09:31:18', '2026-04-28 09:31:18'),
(7, '2025-01-01', 'Tết Dương lịch', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(8, '2025-04-30', 'Ngày Giải phóng Miền Nam', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(9, '2025-05-01', 'Ngày Quốc tế Lao động', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(10, '2025-09-02', 'Quốc khánh', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(11, '2025-09-03', 'Quốc khánh (Ngày 2)', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(12, '2025-01-27', 'Tết Nguyên Đán (Ngày 1) 2025', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(13, '2025-01-28', 'Tết Nguyên Đán (Ngày 2) 2025', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(14, '2025-01-29', 'Tết Nguyên Đán (Ngày 3) 2025', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(15, '2025-01-30', 'Tết Nguyên Đán (Ngày 4) 2025', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(16, '2025-01-31', 'Tết Nguyên Đán (Ngày 5) 2025', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(17, '2025-04-07', 'Giỗ tổ Hùng Vương 2025', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(18, '2026-01-01', 'Tết Dương lịch', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(19, '2026-04-30', 'Ngày Giải phóng Miền Nam', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(20, '2026-05-01', 'Ngày Quốc tế Lao động', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(21, '2026-09-02', 'Quốc khánh', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(22, '2026-09-03', 'Quốc khánh (Ngày 2)', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(23, '2027-01-01', 'Tết Dương lịch', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(24, '2027-04-30', 'Ngày Giải phóng Miền Nam', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(25, '2027-05-01', 'Ngày Quốc tế Lao động', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(26, '2027-09-02', 'Quốc khánh', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(27, '2027-09-03', 'Quốc khánh (Ngày 2)', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(28, '2027-02-05', 'Tết Nguyên Đán (Ngày 1) 2027', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(29, '2027-02-06', 'Tết Nguyên Đán (Ngày 2) 2027', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(30, '2027-02-07', 'Tết Nguyên Đán (Ngày 3) 2027', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(31, '2027-02-08', 'Tết Nguyên Đán (Ngày 4) 2027', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(32, '2027-02-09', 'Tết Nguyên Đán (Ngày 5) 2027', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(33, '2027-04-15', 'Giỗ tổ Hùng Vương 2027', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(34, '2028-01-01', 'Tết Dương lịch', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(35, '2028-04-30', 'Ngày Giải phóng Miền Nam', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(36, '2028-05-01', 'Ngày Quốc tế Lao động', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(37, '2028-09-02', 'Quốc khánh', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(38, '2028-09-03', 'Quốc khánh (Ngày 2)', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(39, '2028-01-25', 'Tết Nguyên Đán (Ngày 1) 2028', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(40, '2028-01-26', 'Tết Nguyên Đán (Ngày 2) 2028', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(41, '2028-01-27', 'Tết Nguyên Đán (Ngày 3) 2028', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(42, '2028-01-28', 'Tết Nguyên Đán (Ngày 4) 2028', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(43, '2028-01-29', 'Tết Nguyên Đán (Ngày 5) 2028', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(44, '2028-04-04', 'Giỗ tổ Hùng Vương 2028', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(45, '2029-01-01', 'Tết Dương lịch', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(46, '2029-04-30', 'Ngày Giải phóng Miền Nam', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(47, '2029-05-01', 'Ngày Quốc tế Lao động', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(48, '2029-09-02', 'Quốc khánh', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(49, '2029-09-03', 'Quốc khánh (Ngày 2)', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(50, '2029-02-12', 'Tết Nguyên Đán (Ngày 1) 2029', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(51, '2029-02-13', 'Tết Nguyên Đán (Ngày 2) 2029', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(52, '2029-02-14', 'Tết Nguyên Đán (Ngày 3) 2029', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(53, '2029-02-15', 'Tết Nguyên Đán (Ngày 4) 2029', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(54, '2029-02-16', 'Tết Nguyên Đán (Ngày 5) 2029', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(55, '2029-04-23', 'Giỗ tổ Hùng Vương 2029', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(56, '2030-01-01', 'Tết Dương lịch', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(57, '2030-04-30', 'Ngày Giải phóng Miền Nam', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(58, '2030-05-01', 'Ngày Quốc tế Lao động', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(59, '2030-09-02', 'Quốc khánh', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(60, '2030-09-03', 'Quốc khánh (Ngày 2)', 'public', 1, 1, NULL, '2026-04-28 09:55:00', '2026-04-29 01:26:39'),
(61, '2030-02-02', 'Tết Nguyên Đán (Ngày 1) 2030', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(62, '2030-02-03', 'Tết Nguyên Đán (Ngày 2) 2030', 'public', 1, 0, NULL, '2026-04-28 09:55:00', '2026-04-28 09:55:00'),
(63, '2030-02-04', 'Tết Nguyên Đán (Ngày 3) 2030', 'public', 1, 0, NULL, '2026-04-28 09:55:01', '2026-04-28 09:55:01'),
(64, '2030-02-05', 'Tết Nguyên Đán (Ngày 4) 2030', 'public', 1, 0, NULL, '2026-04-28 09:55:01', '2026-04-28 09:55:01'),
(65, '2030-02-06', 'Tết Nguyên Đán (Ngày 5) 2030', 'public', 1, 0, NULL, '2026-04-28 09:55:01', '2026-04-28 09:55:01'),
(66, '2030-04-12', 'Giỗ tổ Hùng Vương 2030', 'public', 1, 0, NULL, '2026-04-28 09:55:01', '2026-04-28 09:55:01');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(50, 'default', '{\"uuid\":\"fdda6f65-ffb3-47fd-9c8b-9c09da4f3f96\",\"displayName\":\"App\\\\Mail\\\\SuccessfulLoginMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:28:\\\"App\\\\Mail\\\\SuccessfulLoginMail\\\":7:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:11:\\\"loginMethod\\\";s:21:\\\"Email va mật khẩu\\\";s:9:\\\"ipAddress\\\";s:9:\\\"127.0.0.1\\\";s:9:\\\"userAgent\\\";s:111:\\\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\\\";s:10:\\\"loggedInAt\\\";s:19:\\\"29\\/04\\/2026 08:59:43\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:21:\\\"dophuhieu15@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1777427983,\"delay\":null}', 0, NULL, 1777427983, 1777427983),
(51, 'default', '{\"uuid\":\"ef25e254-f9b3-45b9-9364-6cbf92272f4b\",\"displayName\":\"App\\\\Mail\\\\SuccessfulLoginMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:28:\\\"App\\\\Mail\\\\SuccessfulLoginMail\\\":7:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:11:\\\"loginMethod\\\";s:21:\\\"Email va mật khẩu\\\";s:9:\\\"ipAddress\\\";s:9:\\\"127.0.0.1\\\";s:9:\\\"userAgent\\\";s:111:\\\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\\\";s:10:\\\"loggedInAt\\\";s:19:\\\"29\\/04\\/2026 15:59:01\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:21:\\\"dophuhieu15@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1777453141,\"delay\":null}', 0, NULL, 1777453141, 1777453141);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_balance_transactions`
--

CREATE TABLE `leave_balance_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_leave_balance_id` bigint UNSIGNED NOT NULL,
  `attendance_request_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `days` decimal(8,2) NOT NULL,
  `balance_after` decimal(8,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_balance_transactions`
--

INSERT INTO `leave_balance_transactions` (`id`, `employee_leave_balance_id`, `attendance_request_id`, `type`, `days`, `balance_after`, `note`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(2, 3, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(3, 4, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(4, 5, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(5, 6, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(6, 7, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(7, 8, NULL, 'grant', 0.00, 12.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(8, 9, NULL, 'grant', 0.00, 6.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(9, 10, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(10, 11, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(11, 12, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(12, 13, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:53:12', '2026-04-21 04:53:12'),
(13, 9, NULL, 'grant', 0.00, 6.00, 'Cap quy phep nam tu han muc loai nghi', 1, '2026-04-21 04:53:46', '2026-04-21 04:53:46'),
(14, 2, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(15, 3, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(16, 4, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(17, 5, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(18, 6, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(19, 7, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(20, 8, NULL, 'grant', 0.00, 12.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(21, 9, NULL, 'grant', 0.00, 6.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(22, 10, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(23, 11, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(24, 12, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(25, 13, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 04:56:00', '2026-04-21 04:56:00'),
(26, 3, NULL, 'adjust', 6.00, 10.50, 'dfs', 1, '2026-04-21 04:56:19', '2026-04-21 04:56:19'),
(27, 6, NULL, 'adjust', 8.00, 12.50, 'gfd', 1, '2026-04-21 04:56:27', '2026-04-21 04:56:27'),
(28, 2, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(29, 3, NULL, 'grant', 0.00, 10.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(30, 4, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(31, 5, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(32, 6, NULL, 'grant', 0.00, 12.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(33, 7, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(34, 8, NULL, 'grant', 0.00, 12.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(35, 9, NULL, 'grant', 0.00, 6.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(36, 10, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(37, 11, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(38, 12, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(39, 13, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 05:01:14', '2026-04-21 05:01:14'),
(40, 2, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(41, 3, NULL, 'grant', 0.00, 10.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(42, 4, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(43, 5, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(44, 6, NULL, 'grant', 0.00, 12.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(45, 7, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(46, 8, NULL, 'grant', 0.00, 12.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(47, 9, NULL, 'grant', 0.00, 6.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(48, 10, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(49, 11, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(50, 12, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(51, 13, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:31', '2026-04-21 06:28:31'),
(52, 2, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(53, 3, NULL, 'grant', 0.00, 10.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(54, 4, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(55, 5, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(56, 6, NULL, 'grant', 0.00, 12.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(57, 7, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(58, 8, NULL, 'grant', 0.00, 12.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(59, 9, NULL, 'grant', 0.00, 6.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(60, 10, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(61, 11, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(62, 12, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(63, 13, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 06:28:42', '2026-04-21 06:28:42'),
(64, 12, 1, 'pending', 3.00, 1.50, 'Giu phep cho don cho duyet', 4, '2026-04-21 07:25:56', '2026-04-21 07:25:56'),
(65, 12, 1, 'reject', 3.00, 4.50, 'Hoan phep do tu choi don', 1, '2026-04-21 07:27:12', '2026-04-21 07:27:12'),
(66, 2, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(67, 3, NULL, 'grant', 0.00, 10.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(68, 4, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(69, 14, NULL, 'grant', 0.00, 10.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(70, 5, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(71, 6, NULL, 'grant', 0.00, 12.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(72, 7, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(73, 15, NULL, 'grant', 0.00, 10.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(74, 8, NULL, 'grant', 0.00, 12.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(75, 9, NULL, 'grant', 0.00, 6.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(76, 10, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(77, 16, NULL, 'grant', 0.00, 10.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(78, 11, NULL, 'grant', 0.00, 9.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(79, 12, NULL, 'grant', 0.00, 4.50, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(80, 13, NULL, 'grant', 0.00, 0.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(81, 17, NULL, 'grant', 0.00, 10.00, 'Dong bo/cap phep hang loat tu han muc loai nghi', 1, '2026-04-21 07:37:27', '2026-04-21 07:37:27'),
(82, 3, 4, 'pending', 1.00, 9.50, 'Giu phep cho don cho duyet', 1, '2026-04-22 06:40:51', '2026-04-22 06:40:51'),
(83, 12, 6, 'pending', 1.00, 3.50, 'Giu phep cho don cho duyet', 4, '2026-04-23 02:28:35', '2026-04-23 02:28:35'),
(84, 12, 6, 'approve', 1.00, 3.50, 'Duyet don nghi phep', 1, '2026-04-23 02:29:03', '2026-04-23 02:29:03'),
(85, 1, NULL, 'grant', 0.00, 8.25, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(86, 2, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(87, 3, NULL, 'grant', 0.00, 1.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(88, 4, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(89, 5, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(90, 6, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(91, 7, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(92, 8, NULL, 'grant', 0.00, 8.25, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(93, 9, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(94, 10, NULL, 'grant', 0.00, 1.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(95, 11, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(96, 12, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(97, 13, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(98, 14, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(99, 15, NULL, 'grant', 0.00, 8.25, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(100, 16, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(101, 17, NULL, 'grant', 0.00, 1.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(102, 18, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(103, 19, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(104, 20, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(105, 21, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(106, 22, NULL, 'grant', 0.00, 8.25, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(107, 23, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(108, 24, NULL, 'grant', 0.00, 1.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(109, 25, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(110, 26, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(111, 27, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(112, 28, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(113, 29, NULL, 'grant', 0.00, 8.25, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(114, 30, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(115, 31, NULL, 'grant', 0.00, 1.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(116, 32, NULL, 'grant', 0.00, 3.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(117, 33, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(118, 34, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03'),
(119, 35, NULL, 'grant', 0.00, 0.00, 'Đồng bộ/cấp phép hàng loạt từ hạn mức loại nghỉ', 1, '2026-04-28 09:01:03', '2026-04-28 09:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '1',
  `deducts_balance` tinyint(1) NOT NULL DEFAULT '1',
  `requires_attachment` tinyint(1) NOT NULL DEFAULT '0',
  `annual_quota` decimal(8,2) NOT NULL DEFAULT '0.00',
  `prorate_by_hire_date` tinyint(1) NOT NULL DEFAULT '1',
  `max_days_per_request` decimal(8,2) DEFAULT NULL,
  `carryover_limit` decimal(8,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `code`, `name`, `is_paid`, `deducts_balance`, `requires_attachment`, `annual_quota`, `prorate_by_hire_date`, `max_days_per_request`, `carryover_limit`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ANNUAL', 'Nghỉ hàng năm', 1, 1, 0, 12.00, 1, NULL, NULL, 'Người lao động có đủ 12 tháng làm việc được nghỉ 12 ngày/năm. Cứ 05 năm làm việc được nghỉ thêm 01 ngày.', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23'),
(2, 'MARRIAGE', 'Nghỉ kết hôn', 1, 0, 1, 3.00, 0, NULL, NULL, 'Bản thân kết hôn: nghỉ 03 ngày, hưởng nguyên lương.', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23'),
(3, 'CHILD_MARRIAGE', 'Nghỉ con kết hôn', 1, 0, 1, 1.00, 0, NULL, NULL, 'Con đẻ, con nuôi kết hôn: nghỉ 01 ngày, hưởng nguyên lương.', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23'),
(4, 'BEREAVEMENT', 'Nghỉ tang chế', 1, 0, 1, 3.00, 0, NULL, NULL, 'Bố đẻ, mẹ đẻ, bố vợ, mẹ vợ (chồng), vợ hoặc chồng, con đẻ, con nuôi chết: nghỉ 03 ngày, hưởng nguyên lương.', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23'),
(5, 'NGHI_OM', 'Nghỉ bệnh (Ốm đau)', 1, 0, 0, 0.00, 0, NULL, NULL, 'Nghỉ bệnh hưởng chế độ. Nghỉ từ 03 ngày trở lên bắt buộc có giấy xác nhận của bác sĩ/bệnh viện.', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23'),
(6, 'UNPAID', 'Nghỉ việc riêng (Không lương)', 0, 0, 0, 0.00, 0, NULL, NULL, 'Nghỉ việc riêng khác không hưởng lương (phải thỏa thuận với người quản lý).', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23'),
(7, 'MATERNITY', 'Nghỉ thai sản', 1, 0, 1, 0.00, 0, NULL, NULL, 'Nghỉ thai sản theo quy định của pháp luật về bảo hiểm xã hội.', 1, '2026-04-28 08:59:23', '2026-04-28 08:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `login_histories`
--

CREATE TABLE `login_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `login_at` timestamp NOT NULL,
  `logout_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_histories`
--

INSERT INTO `login_histories` (`id`, `user_id`, `login_at`, `logout_at`, `ip_address`, `device`, `browser`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-17 09:53:48', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-17 09:53:48', '2026-04-17 09:53:48'),
(2, 1, '2026-04-20 01:00:49', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-20 01:00:49', '2026-04-20 01:00:49'),
(3, 4, '2026-04-20 01:05:06', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-20 01:05:06', '2026-04-20 01:05:06'),
(4, 1, '2026-04-21 00:59:31', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-21 00:59:31', '2026-04-21 00:59:31'),
(5, 4, '2026-04-21 01:00:04', '2026-04-21 02:02:37', '127.0.0.1', 'Windows', 'Chrome', '2026-04-21 01:00:04', '2026-04-21 02:02:37'),
(6, 4, '2026-04-21 02:02:52', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-21 02:02:52', '2026-04-21 02:02:52'),
(7, 4, '2026-04-22 01:05:48', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-22 01:05:48', '2026-04-22 01:05:48'),
(8, 1, '2026-04-22 01:07:27', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-22 01:07:27', '2026-04-22 01:07:27'),
(9, 1, '2026-04-23 01:14:36', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-23 01:14:36', '2026-04-23 01:14:36'),
(10, 4, '2026-04-23 01:15:24', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-23 01:15:24', '2026-04-23 01:15:24'),
(11, 1, '2026-04-24 01:06:31', '2026-04-24 03:32:35', '127.0.0.1', 'Windows', 'Chrome', '2026-04-24 01:06:31', '2026-04-24 03:32:35'),
(12, 4, '2026-04-24 01:10:41', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-24 01:10:41', '2026-04-24 01:10:41'),
(13, 1, '2026-04-24 03:32:59', '2026-04-24 08:03:30', '127.0.0.1', 'Windows', 'Chrome', '2026-04-24 03:32:59', '2026-04-24 08:03:30'),
(14, 1, '2026-04-24 08:03:58', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-24 08:03:58', '2026-04-24 08:03:58'),
(15, 4, '2026-04-24 08:19:18', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-24 08:19:18', '2026-04-24 08:19:18'),
(16, 1, '2026-04-28 01:00:04', '2026-04-28 02:32:19', '127.0.0.1', 'Windows', 'Chrome', '2026-04-28 01:00:04', '2026-04-28 02:32:19'),
(17, 4, '2026-04-28 01:19:07', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-28 01:19:07', '2026-04-28 01:19:07'),
(18, 23, '2026-04-28 01:35:20', NULL, '127.0.0.1', 'Windows', 'Edge', '2026-04-28 01:35:20', '2026-04-28 01:35:20'),
(19, 23, '2026-04-28 01:36:47', NULL, '127.0.0.1', 'Windows', 'Edge', '2026-04-28 01:36:47', '2026-04-28 01:36:47'),
(20, 1, '2026-04-28 02:34:06', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-28 02:34:06', '2026-04-28 02:34:06'),
(21, 1, '2026-04-28 06:27:30', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-28 06:27:30', '2026-04-28 06:27:30'),
(22, 4, '2026-04-28 08:38:05', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-28 08:38:05', '2026-04-28 08:38:05'),
(23, 23, '2026-04-28 10:32:15', NULL, '127.0.0.1', 'Windows', 'Edge', '2026-04-28 10:32:15', '2026-04-28 10:32:15'),
(24, 1, '2026-04-29 01:02:54', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-29 01:02:54', '2026-04-29 01:02:54'),
(25, 23, '2026-04-29 01:03:30', NULL, '127.0.0.1', 'Windows', 'Edge', '2026-04-29 01:03:30', '2026-04-29 01:03:30'),
(26, 4, '2026-04-29 01:59:43', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-29 01:59:43', '2026-04-29 01:59:43'),
(27, 4, '2026-04-29 08:59:01', NULL, '127.0.0.1', 'Windows', 'Chrome', '2026-04-29 08:59:01', '2026-04-29 08:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `login_otps`
--

CREATE TABLE `login_otps` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu OTP xac thuc dang nhap cho nguoi dung.';

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_26_032146_create_permission_tables', 1),
(5, '2026_03_25_000001_create_notifications_table', 1),
(6, '2026_04_01_115211_add_thumbnail_and_slug_to_users_table', 1),
(7, '2026_04_13_160000_create_hrm_management_tables', 1),
(8, '2026_04_13_161000_create_hrm_missing_tables', 1),
(9, '2026_04_14_081500_add_authority_level_to_positions_table', 1),
(10, '2026_04_14_083500_add_is_employee_to_users_table', 1),
(11, '2026_04_14_084500_add_termination_date_to_employee_profiles_table', 1),
(12, '2026_04_14_091500_split_employee_status_and_type', 1),
(13, '2026_04_14_102000_add_remember_token_to_users_table', 1),
(14, '2026_04_14_114751_create_password_reset_otps_table', 1),
(15, '2026_04_14_155401_add_capabilities_to_positions_table', 1),
(16, '2026_04_15_090000_create_login_otps_table', 1),
(17, '2026_04_15_103000_add_trace_columns_to_activity_logs_table', 1),
(18, '2026_04_15_110000_update_attendance_status_enum_values', 1),
(19, '2026_04_15_113000_extend_attendance_management_features', 1),
(20, '2026_04_16_100000_add_progress_fields_to_project_implementation_details_table', 1),
(21, '2026_04_16_130000_add_reply_fields_to_feedback_messages_table', 1),
(22, '2026_04_16_140000_create_feedback_replies_table', 1),
(23, '2026_04_16_210000_create_position_capability_tables', 1),
(24, '2026_04_16_220000_create_authority_levels_table', 1),
(25, '2026_04_17_150000_drop_minimum_role_from_authority_levels_table', 1),
(26, '2026_04_17_171500_add_receiver_position_to_feedback_messages_table', 2),
(27, '2026_04_20_083300_add_escalation_fields_to_feedback_messages_table', 3),
(28, '2026_04_20_084500_create_feedback_escalations_table', 4),
(29, '2026_04_20_100000_extend_attendance_catalogs_table', 5),
(30, '2026_04_20_120000_add_overtime_settings_to_work_shifts_table', 6),
(31, '2026_04_20_130000_create_work_shift_overtime_rules_table', 7),
(32, '2026_04_20_140000_drop_legacy_overtime_columns_from_work_shifts_table', 8),
(33, '2026_04_20_180000_drop_districts_and_roles_tables', 8),
(34, '2026_04_21_100000_create_payroll_periods_and_salary_snapshots_tables', 9),
(35, '2026_04_21_110000_create_salary_adjustments_table', 10),
(36, '2026_04_21_111000_add_breakdown_columns_to_salary_snapshots_table', 10),
(37, '2026_04_21_120000_create_leave_management_tables', 11),
(38, '2026_04_21_121000_add_advanced_leave_management_columns', 12),
(39, '2026_04_21_153500_add_conversation_fields_to_feedback_messages_table', 13),
(40, '2026_04_22_120000_add_business_trip_and_make_up_link_columns_to_attendance_requests', 14),
(41, '2026_04_23_140000_drop_unused_settings_tables', 15),
(42, '2026_04_23_150000_allow_cancelled_attendance_request_statuses', 16),
(43, '2026_04_23_160000_deny_gtvbehieu_self_service_capabilities', 17),
(44, '2026_04_24_090000_split_general_approval_capabilities', 18),
(45, '2026_04_24_110000_create_approval_decision_deliveries_table', 19),
(46, '2026_04_24_150000_create_password_change_otps_table', 20),
(47, '2026_04_24_160000_create_project_attachments_table', 21),
(48, '2026_04_24_170000_create_project_detail_comments_table', 22),
(49, '2026_04_24_180000_add_permissions_to_project_roles_table', 23),
(50, '2026_04_28_090000_create_project_milestones_table', 24),
(51, '2026_04_28_150127_add_requested_by_to_salary_histories_table', 25),
(52, '2026_04_28_171217_add_department_id_to_positions_table', 26),
(53, '2026_04_29_090000_add_end_date_to_projects_table', 27),
(54, '2026_04_29_100000_create_project_implementation_subtasks_table', 28),
(55, '2026_04_29_112652_rename_progress_percent_to_weight_percent_in_subtasks', 29),
(56, '2026_04_29_143000_create_project_work_logs_table', 30),
(57, '2026_04_29_150000_add_project_reminder_columns', 31);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang pivot gan quyen truc tiep cho tung model.';

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tieu de ngan de hien thi trong danh sach thong bao.',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Noi dung chi tiet cua thong bao.',
  `data` json DEFAULT NULL COMMENT 'Du lieu bo sung dang JSON de client xu ly.',
  `url_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lien ket dieu huong khi nguoi dung bam vao thong bao.',
  `subdomain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Subdomain lien quan neu he thong tach theo tenant.',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'Nhom thong bao: general, hrm, project...',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'Thoi diem nguoi dung da doc thong bao.',
  `creater_id` bigint DEFAULT NULL COMMENT 'ID nguoi tao ra thong bao.',
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loai doi tuong goc ma thong bao dang tham chieu.',
  `reference_id` bigint DEFAULT NULL COMMENT 'ID doi tuong goc ma thong bao dang tham chieu.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu thong bao he thong gui den nguoi dung.';

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `data`, `url_link`, `subdomain`, `category`, `read_at`, `creater_id`, `reference_type`, `reference_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Thong bao cham cong', 'GTV Be Hieu da check-in luc 17/04/2026 17:19.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 2}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 1, 'App\\Models\\AttendanceRecord', 2, '2026-04-17 10:19:20', '2026-04-23 04:06:30'),
(2, 2, 'Thong bao cham cong', 'GTV Be Hieu da check-in luc 17/04/2026 17:19.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 2}', '/attendance/approvals', NULL, 'attendance', NULL, 1, 'App\\Models\\AttendanceRecord', 2, '2026-04-17 10:19:20', '2026-04-17 10:19:20'),
(3, 1, 'Thong bao cham cong', 'GTV Be Hieu da check-out luc 17/04/2026 17:19.', '{\"event_type\": \"check_out\", \"attendance_record_id\": 2}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 1, 'App\\Models\\AttendanceRecord', 2, '2026-04-17 10:19:24', '2026-04-23 04:06:30'),
(4, 2, 'Thong bao cham cong', 'GTV Be Hieu da check-out luc 17/04/2026 17:19.', '{\"event_type\": \"check_out\", \"attendance_record_id\": 2}', '/attendance/approvals', NULL, 'attendance', NULL, 1, 'App\\Models\\AttendanceRecord', 2, '2026-04-17 10:19:24', '2026-04-17 10:19:24'),
(5, 1, 'Thong bao cham cong', 'GTV Be Hieu da check-in luc 20/04/2026 08:00.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 3}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 1, 'App\\Models\\AttendanceRecord', 3, '2026-04-20 01:00:52', '2026-04-23 04:06:30'),
(6, 2, 'Thong bao cham cong', 'GTV Be Hieu da check-in luc 20/04/2026 08:00.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 3}', '/attendance/approvals', NULL, 'attendance', NULL, 1, 'App\\Models\\AttendanceRecord', 3, '2026-04-20 01:00:52', '2026-04-20 01:00:52'),
(7, 1, 'Thong bao cham cong', 'hieu duy da check-in luc 20/04/2026 08:07.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 4}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceRecord', 4, '2026-04-20 01:07:39', '2026-04-23 04:06:30'),
(8, 2, 'Thong bao cham cong', 'hieu duy da check-in luc 20/04/2026 08:07.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 4}', '/attendance/approvals', NULL, 'attendance', NULL, 4, 'App\\Models\\AttendanceRecord', 4, '2026-04-20 01:07:39', '2026-04-20 01:07:39'),
(9, 2, 'Phan hoi noi bo moi', 'hieu duy vua gui phan hoi: Tôi muốn tăng lương', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 1}', '/feedbacks', NULL, 'feedback', NULL, 4, 'App\\Models\\FeedbackMessage', 1, '2026-04-20 01:46:38', '2026-04-20 01:46:38'),
(10, 1, 'Yeu cau dieu chinh cong', 'hieu duy vua gui yeu cau dieu chinh cong ngay 20/04/2026.', '{\"work_date\": \"2026-04-20\", \"request_type\": \"manual_adjustment\", \"attendance_record_id\": 4, \"attendance_adjustment_id\": 1}', '/attendance/adjustments/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceAdjustment', 1, '2026-04-20 02:30:28', '2026-04-23 04:06:30'),
(11, 4, 'Ket qua dieu chinh cong', 'Yeu cau dieu chinh cong ngay 20/04/2026 da duoc duyet.', '{\"decision\": \"approved\", \"work_date\": \"2026-04-20\", \"reviewer_id\": 1, \"attendance_record_id\": 4, \"attendance_adjustment_id\": 1}', '/attendance/adjustments', NULL, 'attendance', '2026-04-23 04:23:07', 1, 'App\\Models\\AttendanceAdjustment', 1, '2026-04-20 02:36:40', '2026-04-23 04:23:07'),
(12, 1, 'Yeu cau dieu chinh cong', 'hieu duy vua gui yeu cau dieu chinh cong ngay 20/04/2026.', '{\"work_date\": \"2026-04-20\", \"request_type\": \"manual_adjustment\", \"attendance_record_id\": 4, \"attendance_adjustment_id\": 2}', '/attendance/adjustments/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceAdjustment', 2, '2026-04-20 02:37:44', '2026-04-23 04:06:30'),
(13, 4, 'Ket qua dieu chinh cong', 'Yeu cau dieu chinh cong ngay 20/04/2026 da bi tu choi.', '{\"decision\": \"rejected\", \"work_date\": \"2026-04-20\", \"reviewer_id\": 1, \"attendance_record_id\": 4, \"attendance_adjustment_id\": 2}', '/attendance/adjustments', NULL, 'attendance', '2026-04-23 04:23:07', 1, 'App\\Models\\AttendanceAdjustment', 2, '2026-04-20 02:41:00', '2026-04-23 04:23:07'),
(14, 1, 'Yeu cau dieu chinh cong', 'hieu duy vua gui yeu cau dieu chinh cong ngay 20/04/2026.', '{\"work_date\": \"2026-04-20\", \"request_type\": \"manual_adjustment\", \"attendance_record_id\": 4, \"attendance_adjustment_id\": 3}', '/attendance/adjustments/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceAdjustment', 3, '2026-04-20 02:41:32', '2026-04-23 04:06:30'),
(15, 4, 'Ket qua dieu chinh cong', 'Yeu cau dieu chinh cong ngay 20/04/2026 da duoc duyet.', '{\"decision\": \"approved\", \"work_date\": \"2026-04-20\", \"reviewer_id\": 1, \"attendance_record_id\": 4, \"attendance_adjustment_id\": 3}', '/attendance/adjustments', NULL, 'attendance', '2026-04-23 04:23:07', 1, 'App\\Models\\AttendanceAdjustment', 3, '2026-04-20 03:46:26', '2026-04-23 04:23:07'),
(16, 2, 'Phan hoi noi bo moi', 'hieu duy vua gui phan hoi: haha', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 2}', '/feedbacks', NULL, 'feedback', NULL, 4, 'App\\Models\\FeedbackMessage', 2, '2026-04-20 09:22:47', '2026-04-20 09:22:47'),
(17, 2, 'Phan hoi noi bo moi', 'hieu duy vua gui phan hoi: đánh cầu', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 3}', '/feedbacks', NULL, 'feedback', NULL, 4, 'App\\Models\\FeedbackMessage', 3, '2026-04-20 09:42:36', '2026-04-20 09:42:36'),
(18, 1, 'Thong bao cham cong', 'GTV Be Hieu da check-in luc 21/04/2026 07:59.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 5}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 1, 'App\\Models\\AttendanceRecord', 5, '2026-04-21 00:59:37', '2026-04-23 04:06:30'),
(19, 1, 'Thong bao cham cong', 'hieu duy da check-in luc 21/04/2026 08:00.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 6}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceRecord', 6, '2026-04-21 01:00:12', '2026-04-23 04:06:30'),
(20, 1, 'Feedback qua han da leo thang', 'Feedback \'Tôi muốn tăng lương\' cua hieu duy da qua han va duoc chuyen len System Full Access.', '{\"action_url\": \"/feedbacks\", \"escalation_count\": 1, \"feedback_message_id\": 1}', '/feedbacks', NULL, 'feedback', '2026-04-23 04:06:30', 4, 'App\\Models\\FeedbackMessage', 1, '2026-04-21 07:50:47', '2026-04-23 04:06:30'),
(21, 4, 'Phan hoi da duoc tra loi', 'Yeu cau \'Tôi muốn tăng lương\' da duoc GTV Be Hieu phan hoi.', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 1}', '/feedbacks', NULL, 'feedback', '2026-04-23 04:23:07', 1, 'App\\Models\\FeedbackMessage', 1, '2026-04-21 07:52:00', '2026-04-23 04:23:07'),
(22, 1, 'Phan hoi noi bo co tin nhan moi', 'hieu duy vua phan hoi them: Tôi muốn tăng lương', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 1}', '/feedbacks', NULL, 'feedback', '2026-04-23 04:06:30', 4, 'App\\Models\\FeedbackMessage', 1, '2026-04-21 07:52:44', '2026-04-23 04:06:30'),
(23, 4, 'Phan hoi da duoc tra loi', 'Yeu cau \'Tôi muốn tăng lương\' da duoc GTV Be Hieu phan hoi.', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 1}', '/feedbacks', NULL, 'feedback', '2026-04-23 04:23:07', 1, 'App\\Models\\FeedbackMessage', 1, '2026-04-21 08:22:38', '2026-04-23 04:23:07'),
(24, 2, 'Phan hoi noi bo co tin nhan moi', 'hieu duy vua phan hoi them: đánh cầu', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 3}', '/feedbacks', NULL, 'feedback', NULL, 4, 'App\\Models\\FeedbackMessage', 3, '2026-04-21 08:38:06', '2026-04-21 08:38:06'),
(25, 1, 'Yêu cầu khóa phòng ban', 'hieu duy vừa gửi yêu cầu khóa phòng ban \"Ban dieu hanh he thong\".', '{\"type\": \"department\", \"approval_request_id\": 6}', 'http://127.0.0.1:8000/departments/approvals', NULL, 'department', '2026-04-23 04:06:30', 4, NULL, NULL, '2026-04-21 09:01:49', '2026-04-23 04:06:30'),
(26, 4, 'Yeu cau phong ban bi tu choi', 'Yeu cau khoa/mo phong ban cua ban bi tu choi boi GTV Be Hieu.', '{\"decision\": \"rejected\", \"action_url\": \"/departments/approvals\", \"review_note\": \"từ chối\", \"request_type\": \"department_toggle\", \"approval_request_id\": 6}', '/departments/approvals', NULL, 'approval', '2026-04-23 04:23:07', 1, 'App\\Models\\ApprovalRequest', 6, '2026-04-21 09:02:08', '2026-04-23 04:23:07'),
(27, 1, 'Yêu cầu cập nhật phòng ban', 'hieu duy vừa gửi yêu cầu cập nhật phòng ban \"Ban dieu hanh he thong\".', '{\"type\": \"department\", \"approval_request_id\": 7}', 'http://127.0.0.1:8000/departments/approvals', NULL, 'department', '2026-04-23 04:06:30', 4, NULL, NULL, '2026-04-21 09:02:36', '2026-04-23 04:06:30'),
(28, 4, 'Yeu cau phong ban bi tu choi', 'Yeu cau cap nhat phong ban cua ban bi tu choi boi GTV Be Hieu.', '{\"decision\": \"rejected\", \"action_url\": \"/departments/approvals\", \"review_note\": null, \"request_type\": \"department_update\", \"approval_request_id\": 7}', '/departments/approvals', NULL, 'approval', '2026-04-23 04:23:07', 1, 'App\\Models\\ApprovalRequest', 7, '2026-04-21 09:02:45', '2026-04-23 04:23:07'),
(29, 1, 'Thong bao cham cong', 'hieu duy da check-out luc 21/04/2026 17:32.', '{\"event_type\": \"check_out\", \"attendance_record_id\": 6}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceRecord', 6, '2026-04-21 10:32:49', '2026-04-23 04:06:30'),
(30, 1, 'Thong bao cham cong', 'hieu duy da check-in luc 22/04/2026 08:05.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 7}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 4, 'App\\Models\\AttendanceRecord', 7, '2026-04-22 01:05:57', '2026-04-23 04:06:30'),
(31, 3, 'Feedback qua han da leo thang', 'Feedback \'đánh cầu\' cua hieu duy da qua han va duoc chuyen len Lap trinh vien.', '{\"action_url\": \"/feedbacks\", \"escalation_count\": 1, \"feedback_message_id\": 3}', '/feedbacks', NULL, 'feedback', NULL, 4, 'App\\Models\\FeedbackMessage', 3, '2026-04-22 02:53:19', '2026-04-22 02:53:19'),
(32, 1, 'Thong bao cham cong', 'GTV Be Hieu da check-in luc 22/04/2026 13:35.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 10}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 1, 'App\\Models\\AttendanceRecord', 10, '2026-04-22 06:35:22', '2026-04-23 04:06:30'),
(33, 1, 'Thong bao cham cong', 'GTV Be Hieu da check-out luc 22/04/2026 13:44.', '{\"event_type\": \"check_out\", \"attendance_record_id\": 10}', '/attendance/approvals', NULL, 'attendance', '2026-04-23 04:06:30', 1, 'App\\Models\\AttendanceRecord', 10, '2026-04-22 06:44:49', '2026-04-23 04:06:30'),
(34, 1, 'Feedback qua han da leo thang', 'Feedback \'đánh cầu\' cua hieu duy da qua han va duoc chuyen len System Full Access.', '{\"action_url\": \"/feedbacks\", \"escalation_count\": 2, \"feedback_message_id\": 3}', '/feedbacks', NULL, 'feedback', '2026-04-23 04:06:30', 4, 'App\\Models\\FeedbackMessage', 3, '2026-04-23 03:59:54', '2026-04-23 04:06:30'),
(35, 4, 'Phan hoi da duoc tra loi', 'Yeu cau \'đánh cầu\' da duoc GTV Be Hieu phan hoi.', '{\"action_url\": \"/feedbacks\", \"feedback_message_id\": 3}', '/feedbacks', NULL, 'feedback', '2026-04-23 04:23:07', 1, 'App\\Models\\FeedbackMessage', 3, '2026-04-23 04:03:02', '2026-04-23 04:23:07'),
(36, 4, 'Yeu cau da duoc duyet', 'Yeu cau doi luong co ban cua ban duoc duyet boi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/users/employee-requests\", \"review_note\": \"rất chi là được việc\", \"request_type\": \"user_salary_change\", \"approval_request_id\": 15}', '/users/employee-requests', NULL, 'approval', '2026-04-24 01:31:58', 1, 'App\\Models\\ApprovalRequest', 15, '2026-04-24 01:29:32', '2026-04-24 01:31:58'),
(37, 1, 'Yêu cầu cập nhật phòng ban', 'hieu duy vừa gửi yêu cầu cập nhật phòng ban \"Ban dieu hanh he thong\".', '{\"type\": \"department\", \"approval_request_id\": 16}', 'http://127.0.0.1:8000/departments/approvals', NULL, 'department', '2026-04-24 02:44:03', 4, NULL, NULL, '2026-04-24 02:38:02', '2026-04-24 02:44:03'),
(38, 4, '[HRM] Yêu cầu cập nhật phòng ban đã bị từ chối', 'Yêu cầu cập nhật phòng ban của bạn đã bị từ chối bởi GTV Be Hieu.', '{\"decision\": \"rejected\", \"action_url\": \"/departments\", \"reference_id\": 16, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/departments', NULL, 'approval', '2026-04-24 02:43:36', 1, 'App\\Models\\ApprovalRequest', 16, '2026-04-24 02:38:30', '2026-04-24 02:43:36'),
(39, 1, 'Yêu cầu khóa phòng ban', 'hieu duy vừa gửi yêu cầu khóa phòng ban \"Ban dieu hanh he thong\".', '{\"type\": \"department\", \"approval_request_id\": 17}', 'http://127.0.0.1:8000/departments/approvals', NULL, 'department', '2026-04-24 02:48:39', 4, NULL, NULL, '2026-04-24 02:47:03', '2026-04-24 02:48:39'),
(40, 4, '[HRM] Yêu cầu khóa/mở phòng ban đã bị từ chối', 'Yêu cầu khóa/mở phòng ban của bạn đã bị từ chối bởi GTV Be Hieu.', '{\"decision\": \"rejected\", \"action_url\": \"/departments\", \"reference_id\": 17, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/departments', NULL, 'approval', '2026-04-24 02:52:08', 1, 'App\\Models\\ApprovalRequest', 17, '2026-04-24 02:47:47', '2026-04-24 02:52:08'),
(41, 1, 'Thong bao cham cong', 'hieu duy da check-in luc 24/04/2026 10:16.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 12}', '/attendance/approvals', NULL, 'attendance', '2026-04-24 03:22:14', 4, 'App\\Models\\AttendanceRecord', 12, '2026-04-24 03:16:43', '2026-04-24 03:22:14'),
(42, 3, 'Thong bao cham cong', 'hieu duy da check-in luc 24/04/2026 10:16.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 12}', '/attendance/approvals', NULL, 'attendance', NULL, 4, 'App\\Models\\AttendanceRecord', 12, '2026-04-24 03:16:43', '2026-04-24 03:16:43'),
(43, 4, 'Thong bao cham cong', 'hieu duy da check-in luc 24/04/2026 10:16.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 12}', '/attendance/approvals', NULL, 'attendance', '2026-04-24 07:38:57', 4, 'App\\Models\\AttendanceRecord', 12, '2026-04-24 03:16:43', '2026-04-24 07:38:57'),
(44, 4, '[HRM] Yêu cầu đổi lương cơ bản đã bị từ chối', 'Yêu cầu đổi lương cơ bản của bạn đã bị từ chối bởi GTV Be Hieu.', '{\"decision\": \"rejected\", \"action_url\": \"/users/employee-requests\", \"reference_id\": 18, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/users/employee-requests', NULL, 'approval', '2026-04-24 07:38:57', 1, 'App\\Models\\ApprovalRequest', 18, '2026-04-24 03:23:30', '2026-04-24 07:38:57'),
(45, 4, '[HRM] Yêu cầu đổi lương cơ bản đã bị từ chối', 'Yêu cầu đổi lương cơ bản của bạn đã bị từ chối bởi GTV Be Hieu.', '{\"decision\": \"rejected\", \"action_url\": \"/users/employee-requests\", \"reference_id\": 19, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/users/employee-requests', NULL, 'approval', '2026-04-24 07:38:57', 1, 'App\\Models\\ApprovalRequest', 19, '2026-04-24 03:25:11', '2026-04-24 07:38:57'),
(46, 3, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Đang làm\" với tiến độ 3%.', '{\"status\": \"in_progress\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Đang làm\", \"progress_percent\": 3, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-24 09:22:12', '2026-04-24 09:22:12'),
(47, 4, '[HRM] Đơn xin đi muộn / về sớm đã được duyệt', 'Đơn xin đi muộn / về sớm của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance\", \"reference_id\": 20, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/my-attendance', NULL, 'approval', '2026-04-24 09:54:32', 1, 'App\\Models\\ApprovalRequest', 20, '2026-04-24 09:53:51', '2026-04-24 09:54:32'),
(48, 4, '[HRM] Đơn xin quên chấm công đã được duyệt', 'Đơn xin quên chấm công của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance\", \"reference_id\": 21, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/my-attendance', NULL, 'approval', '2026-04-28 02:10:28', 1, 'App\\Models\\ApprovalRequest', 21, '2026-04-28 01:27:25', '2026-04-28 02:10:28'),
(49, 1, 'Thông báo chấm công', 'hieu duy da check-in luc 28/04/2026 08:28.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 13}', '/attendance/approvals', NULL, 'attendance', '2026-04-28 09:24:20', 4, 'App\\Models\\AttendanceRecord', 13, '2026-04-28 01:28:17', '2026-04-28 09:24:20'),
(50, 3, 'Thông báo chấm công', 'hieu duy da check-in luc 28/04/2026 08:28.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 13}', '/attendance/approvals', NULL, 'attendance', NULL, 4, 'App\\Models\\AttendanceRecord', 13, '2026-04-28 01:28:17', '2026-04-28 01:28:17'),
(51, 4, 'Thông báo chấm công', 'hieu duy da check-in luc 28/04/2026 08:28.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 13}', '/attendance/approvals', NULL, 'attendance', '2026-04-28 02:10:28', 4, 'App\\Models\\AttendanceRecord', 13, '2026-04-28 01:28:17', '2026-04-28 02:10:28'),
(52, 1, 'Thông báo chấm công', 'thi da check-in luc 28/04/2026 08:39.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 14}', '/attendance/approvals', NULL, 'attendance', '2026-04-28 09:24:20', 23, 'App\\Models\\AttendanceRecord', 14, '2026-04-28 01:39:19', '2026-04-28 09:24:20'),
(53, 3, 'Thông báo chấm công', 'thi da check-in luc 28/04/2026 08:39.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 14}', '/attendance/approvals', NULL, 'attendance', NULL, 23, 'App\\Models\\AttendanceRecord', 14, '2026-04-28 01:39:19', '2026-04-28 01:39:19'),
(54, 4, 'Thông báo chấm công', 'thi da check-in luc 28/04/2026 08:39.', '{\"event_type\": \"check_in\", \"attendance_record_id\": 14}', '/attendance/approvals', NULL, 'attendance', '2026-04-28 02:10:28', 23, 'App\\Models\\AttendanceRecord', 14, '2026-04-28 01:39:19', '2026-04-28 02:10:28'),
(55, 23, 'Bạn được phân công vào dự án', 'Bạn đã được phân công vào dự án \"He thong HRM Cloud\" với vai trò \"haha\".', '{\"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"project_role\": \"haha\"}', '/my-projects', '127', 'project', '2026-04-28 01:51:55', 1, 'App\\Models\\Project', 1, '2026-04-28 01:42:18', '2026-04-28 01:51:55'),
(56, 23, 'Bạn được phân công vào dự án', 'Bạn đã được phân công vào dự án \"He thong HRM Cloud\" với vai trò \"haha\".', '{\"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"project_role\": \"haha\"}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\Project', 1, '2026-04-28 02:17:07', '2026-04-29 07:15:26'),
(57, 23, '[HRM] Cập nhật lương cơ bản đã được duyệt', 'Cập nhật lương cơ bản của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-salary\", \"reference_id\": 23, \"reference_type\": \"App\\\\Models\\\\User\"}', '/my-salary', NULL, 'approval', '2026-04-29 07:15:26', 1, 'App\\Models\\User', 23, '2026-04-28 09:41:39', '2026-04-29 07:15:26'),
(58, 23, '[HRM] Cập nhật lương cơ bản đã được duyệt', 'Cập nhật lương cơ bản của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-salary\", \"reference_id\": 23, \"reference_type\": \"App\\\\Models\\\\User\"}', '/my-salary', NULL, 'approval', '2026-04-29 07:15:26', 1, 'App\\Models\\User', 23, '2026-04-28 09:42:02', '2026-04-29 07:15:26'),
(59, 1, 'Thông báo chấm công', 'hieu duy da check-out luc 28/04/2026 17:32.', '{\"event_type\": \"check_out\", \"attendance_record_id\": 13}', '/attendance/approvals', NULL, 'attendance', '2026-04-29 01:26:18', 4, 'App\\Models\\AttendanceRecord', 13, '2026-04-28 10:32:02', '2026-04-29 01:26:18'),
(60, 1, 'Thông báo chấm công', 'thi da check-out luc 28/04/2026 17:32.', '{\"event_type\": \"check_out\", \"attendance_record_id\": 14}', '/attendance/approvals', NULL, 'attendance', '2026-04-29 01:26:18', 23, 'App\\Models\\AttendanceRecord', 14, '2026-04-28 10:32:18', '2026-04-29 01:26:18'),
(61, 23, '[HRM] Đơn xin đi muộn / về sớm đã được duyệt', 'Đơn xin đi muộn / về sớm của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance\", \"reference_id\": 22, \"reference_type\": \"App\\\\Models\\\\ApprovalRequest\"}', '/my-attendance', NULL, 'approval', '2026-04-29 07:15:26', 1, 'App\\Models\\ApprovalRequest', 22, '2026-04-29 01:19:59', '2026-04-29 07:15:26'),
(62, 4, '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026 của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance?month=4&year=2026\", \"reference_id\": 13, \"reference_type\": \"App\\\\Models\\\\AttendanceRecord\"}', '/my-attendance?month=4&year=2026', NULL, 'approval', '2026-04-29 02:41:11', 1, 'App\\Models\\AttendanceRecord', 13, '2026-04-29 02:15:33', '2026-04-29 02:41:11'),
(63, 4, '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026 của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance?month=4&year=2026\", \"reference_id\": 13, \"reference_type\": \"App\\\\Models\\\\AttendanceRecord\"}', '/my-attendance?month=4&year=2026', NULL, 'approval', '2026-04-29 02:41:11', 1, 'App\\Models\\AttendanceRecord', 13, '2026-04-29 02:16:34', '2026-04-29 02:41:11'),
(64, 4, '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026 của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance?month=4&year=2026\", \"reference_id\": 13, \"reference_type\": \"App\\\\Models\\\\AttendanceRecord\"}', '/my-attendance?month=4&year=2026', NULL, 'approval', '2026-04-29 02:41:11', 1, 'App\\Models\\AttendanceRecord', 13, '2026-04-29 02:21:45', '2026-04-29 02:41:11'),
(65, 4, '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026 của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance?month=4&year=2026\", \"reference_id\": 13, \"reference_type\": \"App\\\\Models\\\\AttendanceRecord\"}', '/my-attendance?month=4&year=2026', NULL, 'approval', '2026-04-29 02:41:11', 1, 'App\\Models\\AttendanceRecord', 13, '2026-04-29 02:22:02', '2026-04-29 02:41:11'),
(66, 4, '[HRM] Bản ghi công ngày 28/04/2026 đã được duyệt', 'Bản ghi công ngày 28/04/2026 của bạn đã được duyệt bởi GTV Be Hieu.', '{\"decision\": \"approved\", \"action_url\": \"/my-attendance?month=4&year=2026\", \"reference_id\": 13, \"reference_type\": \"App\\\\Models\\\\AttendanceRecord\"}', '/my-attendance?month=4&year=2026', NULL, 'approval', '2026-04-29 02:41:11', 1, 'App\\Models\\AttendanceRecord', 13, '2026-04-29 02:28:05', '2026-04-29 02:41:11'),
(67, 3, 'Bạn được giao đầu việc mới', 'Bạn được giao đầu việc \"dswfdsf\" trong dự án \"He thong HRM Cloud\".', '{\"status\": \"planned\", \"content\": \"dswfdsf\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Chưa làm\", \"progress_percent\": 0, \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 2, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(68, 23, 'Bạn được phân công vào dự án', 'Bạn đã được phân công vào dự án \"He thong HRM Cloud\" với vai trò \"Trưởng nhóm\".', '{\"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"project_role\": \"Trưởng nhóm\"}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\Project', 1, '2026-04-29 03:34:50', '2026-04-29 07:15:26'),
(69, 3, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Hoàn thành\" với tiến độ 100%.', '{\"status\": \"completed\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Hoàn thành\", \"progress_percent\": 100, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 03:36:59', '2026-04-29 03:36:59'),
(70, 3, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Đang làm\" với tiến độ 1%.', '{\"status\": \"in_progress\", \"content\": \"dswfdsf\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Đang làm\", \"progress_percent\": 1, \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 2, '2026-04-29 03:38:34', '2026-04-29 03:38:34'),
(71, 3, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Tạm dừng\" với tiến độ 0%.', '{\"status\": \"cancelled\", \"content\": \"dswfdsf\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Tạm dừng\", \"progress_percent\": 0, \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 2, '2026-04-29 03:38:53', '2026-04-29 03:38:53'),
(72, 3, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Chưa làm\" với tiến độ 0%.', '{\"status\": \"planned\", \"content\": \"dswfdsf\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Chưa làm\", \"progress_percent\": 0, \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 2, '2026-04-29 03:43:24', '2026-04-29 03:43:24'),
(73, 23, 'Bạn được giao đầu việc mới', 'Bạn được giao đầu việc \"Lam module Database Seeder\" trong dự án \"He thong HRM Cloud\".', '{\"status\": \"completed\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Hoàn thành\", \"progress_percent\": 100, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 03:56:04', '2026-04-29 07:15:26'),
(74, 3, 'Đầu việc dự án đã được cập nhật', 'Đầu việc của bạn đã được cập nhật.', '{\"status\": \"planned\", \"content\": \"CHức vụ\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Chưa làm\", \"progress_percent\": 0, \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectImplementationDetail', 2, '2026-04-29 04:02:37', '2026-04-29 04:02:37'),
(75, 23, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Đang làm\" với tiến độ 99%.', '{\"status\": \"in_progress\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Đang làm\", \"progress_percent\": 99, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 06:34:41', '2026-04-29 07:15:26'),
(76, 23, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Chưa làm\" với tiến độ 0%.', '{\"status\": \"planned\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Chưa làm\", \"progress_percent\": 0, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 06:35:03', '2026-04-29 07:15:26'),
(77, 23, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Đang làm\" với tiến độ 1%.', '{\"status\": \"in_progress\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Đang làm\", \"progress_percent\": 1, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 07:02:18', '2026-04-29 07:15:26'),
(78, 23, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Chưa làm\" với tiến độ 0%.', '{\"status\": \"planned\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Chưa làm\", \"progress_percent\": 0, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 07:02:31', '2026-04-29 07:15:26'),
(79, 23, 'Đầu việc dự án đã được cập nhật', 'Trạng thái đầu việc đã đổi sang \"Hoàn thành\" với tiến độ 100%.', '{\"status\": \"completed\", \"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"status_label\": \"Hoàn thành\", \"progress_percent\": 100, \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', '2026-04-29 07:15:26', 1, 'App\\Models\\ProjectImplementationDetail', 1, '2026-04-29 07:05:21', '2026-04-29 07:15:26'),
(80, 3, 'Đầu việc có bình luận mới', 'GTV Be Hieu đã bình luận trong đầu việc \"CHức vụ\".', '{\"content\": \"CHức vụ\", \"action_url\": \"/my-projects\", \"comment_id\": 1, \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"comment_content\": \"âs\", \"comment_author_name\": \"GTV Be Hieu\", \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectDetailComment', 1, '2026-04-29 07:50:30', '2026-04-29 07:50:30'),
(81, 3, 'Đầu việc có bình luận mới', 'GTV Be Hieu đã bình luận trong đầu việc \"CHức vụ\".', '{\"content\": \"CHức vụ\", \"action_url\": \"/my-projects\", \"comment_id\": 2, \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"comment_content\": \"âs\", \"comment_author_name\": \"GTV Be Hieu\", \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectDetailComment', 2, '2026-04-29 07:50:31', '2026-04-29 07:50:31'),
(82, 3, 'Đầu việc có bình luận mới', 'GTV Be Hieu đã bình luận trong đầu việc \"CHức vụ\".', '{\"content\": \"CHức vụ\", \"action_url\": \"/my-projects\", \"comment_id\": 3, \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"comment_content\": \"á\", \"comment_author_name\": \"GTV Be Hieu\", \"implementation_detail_id\": 2}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectDetailComment', 3, '2026-04-29 07:50:35', '2026-04-29 07:50:35'),
(83, 23, 'Đầu việc có bình luận mới', 'GTV Be Hieu đã bình luận trong đầu việc \"Lam module Database Seeder\".', '{\"content\": \"Lam module Database Seeder\", \"action_url\": \"/my-projects\", \"comment_id\": 4, \"project_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"comment_content\": \"sas\", \"comment_author_name\": \"GTV Be Hieu\", \"implementation_detail_id\": 1}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectDetailComment', 4, '2026-04-29 07:50:40', '2026-04-29 07:50:40'),
(84, 3, 'Dự án có mốc quan trọng mới', 'Mốc \"test\" của dự án \"He thong HRM Cloud\" đang ở trạng thái \"Đang làm\", hạn hoàn thành 29/04/2026.', '{\"status\": \"in_progress\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"milestone_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"milestone_name\": \"test\"}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectMilestone', 1, '2026-04-29 08:07:36', '2026-04-29 08:07:36'),
(85, 3, 'Mốc quan trọng của dự án đã cập nhật', 'Mốc \"test\" của dự án \"He thong HRM Cloud\" đang ở trạng thái \"Hoàn thành\", hạn hoàn thành 29/04/2026.', '{\"status\": \"completed\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"milestone_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"milestone_name\": \"test\"}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectMilestone', 1, '2026-04-29 08:07:46', '2026-04-29 08:07:46'),
(86, 3, 'Mốc quan trọng của dự án đã cập nhật', 'Mốc \"test\" của dự án \"He thong HRM Cloud\" đang ở trạng thái \"Đang làm\", hạn hoàn thành 29/04/2026.', '{\"status\": \"in_progress\", \"action_url\": \"/my-projects\", \"project_id\": 1, \"milestone_id\": 1, \"project_name\": \"He thong HRM Cloud\", \"milestone_name\": \"test\"}', '/my-projects', '127', 'project', NULL, 1, 'App\\Models\\ProjectMilestone', 1, '2026-04-29 08:08:04', '2026-04-29 08:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `approval_request_id` bigint UNSIGNED DEFAULT NULL,
  `work_date` date NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `requested_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `approved_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','approved','rejected','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `requested_by` bigint UNSIGNED NOT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu de nghi tang ca va ket qua duyet.';

--
-- Dumping data for table `overtime_requests`
--

INSERT INTO `overtime_requests` (`id`, `employee_profile_id`, `approval_request_id`, `work_date`, `start_at`, `end_at`, `requested_minutes`, `approved_minutes`, `status`, `reason`, `requested_by`, `reviewed_by`, `reviewed_at`, `review_note`, `created_at`, `updated_at`) VALUES
(1, 4, 4, '2026-04-20', '2026-04-20 17:31:00', '2026-04-20 22:00:00', 179, 0, 'rejected', 'xin tăng ca', 4, 1, '2026-04-20 07:47:47', 'xin tăng ca', '2026-04-20 07:43:01', '2026-04-20 07:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `password_change_otps`
--

CREATE TABLE `password_change_otps` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu OTP xac thuc doi mat khau cho nguoi da dang nhap.';

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_otps`
--

CREATE TABLE `password_reset_otps` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu OTP dat lai mat khau theo email.';

--
-- Dumping data for table `password_reset_otps`
--

INSERT INTO `password_reset_otps` (`id`, `email`, `otp`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'gtvbehieu@gmail.com', '461436', '2026-04-24 08:18:47', '2026-04-24 08:03:47', '2026-04-24 08:03:47');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Thoi diem tao token reset.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_periods`
--

CREATE TABLE `payroll_periods` (
  `id` bigint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` bigint UNSIGNED DEFAULT NULL,
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `unlocked_by` bigint UNSIGNED DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_periods`
--

INSERT INTO `payroll_periods` (`id`, `month`, `year`, `status`, `locked_at`, `locked_by`, `unlocked_at`, `unlocked_by`, `note`, `created_at`, `updated_at`) VALUES
(1, 4, 2026, 'draft', '2026-04-23 01:35:35', 1, '2026-04-23 02:40:24', 1, NULL, '2026-04-21 01:53:24', '2026-04-23 02:40:24');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mo ta muc dich cua quyen.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu danh sach quyen trong he thong.';

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `authority_level` tinyint UNSIGNED DEFAULT NULL,
  `capabilities` json DEFAULT NULL COMMENT 'Danh sach capability code cua chuc vu.',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`, `description`, `authority_level`, `capabilities`, `is_active`, `created_at`, `updated_at`, `department_id`) VALUES
(1, 'System Full Access', 'Chuc vu toan quyen cho tai khoan van hanh he thong.', 10, '[\"manage_employees\", \"view_all_profiles\", \"view_own_profile\", \"update_own_profile\", \"view_salary\", \"view_own_salary\", \"view_all_salary\", \"manage_salary\", \"approve_attendance\", \"view_all_attendance\", \"view_own_attendance\", \"export_attendance\", \"check_in\", \"check_out\", \"request_attendance_adjustment\", \"approve_leave\", \"manage_leave_policy\", \"manage_projects\", \"manage_project_members\", \"manage_project_roles\", \"view_all_projects\", \"view_own_projects\", \"update_project_task_status\", \"manage_departments\", \"manage_positions\", \"transfer_employee\", \"sign_documents\", \"view_reports\", \"export_reports\", \"view_activity_logs\", \"view_dashboard\", \"view_feedbacks\", \"create_feedback\", \"reply_feedback\", \"manage_feedbacks\", \"cancel_requests\", \"view_approval_requests\", \"approve_department_attendance\", \"approve_team_attendance\", \"lock_attendance_month\", \"unlock_attendance_month\", \"view_department_attendance\", \"view_team_attendance\", \"approve_update_employee\", \"create_employee\", \"lock_employee\", \"request_update_employee\", \"approve_department_leave\", \"approve_team_leave\", \"request_leave\", \"view_all_leave_requests\", \"view_department_leave_requests\", \"view_own_leave_requests\", \"view_team_leave_requests\", \"approve_department_change\", \"approve_position_change\", \"manage_holidays\", \"manage_work_shifts\", \"request_department_change\", \"request_position_change\", \"view_departments\", \"view_holidays\", \"view_positions\", \"view_work_shifts\", \"approve_department_overtime\", \"approve_overtime\", \"approve_team_overtime\", \"request_overtime\", \"view_all_overtime\", \"view_department_overtime\", \"view_own_overtime\", \"view_team_overtime\", \"approve_project_change\", \"assign_project_member\", \"create_project\", \"delete_project\", \"lock_project\", \"remove_project_member\", \"request_project_change\", \"update_project\", \"view_department_projects\", \"view_team_projects\", \"view_financial_reports\", \"approve_salary_change\", \"export_salary\", \"generate_payroll\", \"request_salary_change\", \"view_salary_history\", \"manage_settings\", \"manage_users\", \"view_settings\", \"approve_user_requests\", \"approve_department_requests\", \"approve_salary_requests\"]', 1, '2026-04-17 09:50:45', '2026-04-24 01:21:15', NULL),
(2, 'Nhân viên', 'Quan ly nhan su va phe duyet van hanh.', 1, '[\"view_own_profile\", \"update_own_profile\", \"view_own_salary\", \"check_in\", \"check_out\", \"view_own_attendance\", \"request_attendance_adjustment\", \"request_leave\", \"view_own_leave_requests\", \"request_overtime\", \"view_own_overtime\", \"view_own_projects\", \"update_project_task_status\", \"create_feedback\", \"view_dashboard\"]', 1, '2026-04-17 09:50:45', '2026-04-29 01:04:12', 3),
(3, 'hR', 'Nhan su tham gia trien khai du an.', 9, '[\"view_own_profile\", \"update_own_profile\", \"view_own_salary\", \"check_in\", \"check_out\", \"view_own_attendance\", \"request_attendance_adjustment\", \"request_leave\", \"view_own_leave_requests\", \"request_overtime\", \"view_own_overtime\", \"view_own_projects\", \"update_project_task_status\", \"create_feedback\", \"view_dashboard\", \"view_team_attendance\", \"view_team_leave_requests\", \"view_team_overtime\", \"view_team_projects\", \"approve_team_attendance\", \"approve_team_leave\", \"approve_team_overtime\", \"manage_project_members\", \"assign_project_member\", \"remove_project_member\", \"view_department_attendance\", \"approve_department_attendance\", \"view_department_leave_requests\", \"approve_department_leave\", \"view_department_overtime\", \"approve_department_overtime\", \"view_department_projects\", \"request_update_employee\", \"manage_projects\", \"view_departments\", \"view_positions\", \"view_work_shifts\", \"view_holidays\", \"view_all_profiles\", \"view_salary_history\", \"request_salary_change\", \"approve_requests\", \"view_approval_requests\", \"view_reports\", \"view_feedbacks\", \"reply_feedback\", \"approve_update_employee\", \"approve_salary_change\", \"manage_leave_policy\", \"view_all_attendance\", \"view_all_leave_requests\", \"view_all_overtime\", \"view_all_projects\", \"approve_user_requests\", \"manage_employees\", \"manage_salary\", \"export_salary\", \"generate_payroll\", \"export_attendance\", \"lock_attendance_month\", \"unlock_attendance_month\", \"manage_departments\", \"manage_positions\", \"manage_work_shifts\", \"manage_holidays\", \"approve_department_requests\", \"approve_salary_requests\", \"view_financial_reports\", \"sign_documents\", \"manage_settings\", \"export_reports\", \"view_salary\"]', 1, '2026-04-17 09:50:45', '2026-04-29 01:04:19', 2);

-- --------------------------------------------------------

--
-- Table structure for table `position_capabilities`
--

CREATE TABLE `position_capabilities` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang danh muc capability theo module de phan quyen.';

--
-- Dumping data for table `position_capabilities`
--

INSERT INTO `position_capabilities` (`id`, `code`, `name`, `module`, `description`, `is_system`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'manage_employees', 'Quản lý nhân sự', 'human_resources', 'Thêm, sửa, khóa nhân sự', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(2, 'view_salary', 'Xem luong nhan vien', 'human_resources', 'Xem muc luong cua toan bo nhan su', 1, 1, '2026-04-17 09:50:44', '2026-04-17 09:50:44'),
(3, 'manage_salary', 'Điều chỉnh lương', 'salary', 'Đề xuất và phê duyệt thay đổi lương', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(4, 'approve_attendance', 'Duyệt chấm công', 'attendance', 'Xác nhận và phê duyệt công của nhân viên', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(5, 'view_all_attendance', 'Xem toàn bộ chấm công', 'attendance', 'Xem báo cáo công của tất cả nhân sự', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(6, 'export_attendance', 'Xuất báo cáo chấm công', 'attendance', 'Xuất Excel / PDF chấm công', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(7, 'approve_leave', 'Phê duyệt ngày nghỉ', 'leave', 'Duyệt hoặc từ chối đơn xin nghỉ', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(8, 'manage_leave_policy', 'Quản lý chính sách nghỉ', 'leave', 'Thiết lập quy định nghỉ phép', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(9, 'manage_projects', 'Quản lý dự án', 'project', 'Tạo, sửa, phân công dự án', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(10, 'manage_project_members', 'Quản lý thành viên dự án', 'project', 'Thêm/xóa thành viên dự án', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(11, 'manage_project_roles', 'Quản lý vai trò dự án', 'project', 'Tạo và xóa vai trò trong từng dự án', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(12, 'view_all_projects', 'Xem toàn bộ dự án', 'project', 'Xem tất cả dự án trong hệ thống', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(13, 'manage_departments', 'Quản lý phòng ban', 'organization', 'Thêm, sửa, khóa phòng ban', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(14, 'manage_positions', 'Quản lý chức vụ', 'organization', 'Thêm, sửa, khóa chức vụ', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(15, 'transfer_employee', 'Điều chuyển nhân sự', 'human_resources', 'Chuyển nhân viên sang phòng ban khác', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(16, 'approve_requests', 'Duyệt yêu cầu chung', 'approval', 'Phê duyệt các yêu cầu từ nhân viên', 1, 0, '2026-04-17 09:50:44', '2026-04-24 01:21:15'),
(17, 'sign_documents', 'Ký duyệt văn bản', 'approval', 'Ký và phê duyệt hợp đồng, quyết định', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(18, 'view_reports', 'Xem báo cáo tổng hợp', 'report', 'Dashboard và báo cáo toàn hệ thống', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(19, 'export_reports', 'Xuất báo cáo', 'report', 'Xuất dữ liệu Excel / PDF', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(20, 'view_activity_logs', 'Xem nhật ký hoạt động', 'system', 'Xem lịch sử thao tác hệ thống', 1, 1, '2026-04-17 09:50:44', '2026-04-17 10:24:21'),
(21, 'view_all_profiles', 'Xem lương nhân viên', 'human_resources', 'Xem mức lương của toàn bộ nhân sự', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(22, 'view_own_profile', 'Xem hồ sơ cá nhân', 'human_resources', 'Xem thông tin hồ sơ của chính mình', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(23, 'update_own_profile', 'Cập nhật hồ sơ cá nhân', 'human_resources', 'Cập nhật thông tin hồ sơ cá nhân', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(24, 'create_employee', 'Tạo nhân sự', 'human_resources', 'Tạo mới hồ sơ nhân sự', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(25, 'lock_employee', 'Khóa nhân sự', 'human_resources', 'Khóa tài khoản hoặc trạng thái nhân sự', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(26, 'request_update_employee', 'Yêu cầu sửa nhân sự', 'human_resources', 'Tạo yêu cầu thay đổi thông tin nhân sự', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(27, 'approve_update_employee', 'Duyệt sửa nhân sự', 'human_resources', 'Phê duyệt yêu cầu thay đổi thông tin nhân sự', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(28, 'view_own_salary', 'Xem lương cá nhân', 'salary', 'Xem mức lương của chính mình', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(29, 'view_all_salary', 'Xem lương toàn công ty', 'salary', 'Xem mức lương của toàn bộ nhân sự', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(30, 'view_salary_history', 'Xem lịch sử lương', 'salary', 'Xem lịch sử thay đổi lương', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(31, 'request_salary_change', 'Yêu cầu đổi lương', 'salary', 'Tạo đề xuất thay đổi lương', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(32, 'approve_salary_change', 'Duyệt đổi lương', 'salary', 'Phê duyệt đề xuất thay đổi lương', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(33, 'export_salary', 'Xuất dữ liệu lương', 'salary', 'Xuất báo cáo hoặc dữ liệu lương', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(34, 'generate_payroll', 'Tạo bảng lương', 'salary', 'Sinh bảng lương theo kỳ lương', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(35, 'check_in', 'Chấm công vào', 'attendance', 'Thực hiện check-in', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(36, 'check_out', 'Chấm công ra', 'attendance', 'Thực hiện check-out', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(37, 'view_own_attendance', 'Xem chấm công cá nhân', 'attendance', 'Xem dữ liệu chấm công của chính mình', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(38, 'view_team_attendance', 'Xem chấm công team', 'attendance', 'Xem dữ liệu chấm công trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(39, 'view_department_attendance', 'Xem chấm công phòng ban', 'attendance', 'Xem dữ liệu chấm công trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(40, 'request_attendance_adjustment', 'Yêu cầu chỉnh công', 'attendance', 'Tạo yêu cầu chỉnh công', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(41, 'approve_team_attendance', 'Duyệt chấm công team', 'attendance', 'Phê duyệt công trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(42, 'approve_department_attendance', 'Duyệt chấm công phòng ban', 'attendance', 'Phê duyệt công trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(43, 'lock_attendance_month', 'Khóa tháng công', 'attendance', 'Khóa dữ liệu công theo tháng', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(44, 'unlock_attendance_month', 'Mở khóa tháng công', 'attendance', 'Mở khóa dữ liệu công theo tháng', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(45, 'request_leave', 'Tạo đơn nghỉ phép', 'leave', 'Gửi yêu cầu nghỉ phép', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(46, 'view_own_leave_requests', 'Xem đơn nghỉ cá nhân', 'leave', 'Xem các đơn nghỉ của chính mình', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(47, 'view_team_leave_requests', 'Xem đơn nghỉ team', 'leave', 'Xem các đơn nghỉ trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(48, 'view_department_leave_requests', 'Xem đơn nghỉ phòng ban', 'leave', 'Xem các đơn nghỉ trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(49, 'view_all_leave_requests', 'Xem toàn bộ đơn nghỉ', 'leave', 'Xem tất cả đơn nghỉ trong hệ thống', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(50, 'approve_team_leave', 'Duyệt nghỉ team', 'leave', 'Duyệt đơn nghỉ trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(51, 'approve_department_leave', 'Duyệt nghỉ phòng ban', 'leave', 'Duyệt đơn nghỉ trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(52, 'request_overtime', 'Tạo đơn tăng ca', 'overtime', 'Gửi yêu cầu tăng ca', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(53, 'view_own_overtime', 'Xem OT cá nhân', 'overtime', 'Xem dữ liệu tăng ca của chính mình', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(54, 'view_team_overtime', 'Xem OT team', 'overtime', 'Xem dữ liệu tăng ca trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(55, 'view_department_overtime', 'Xem OT phòng ban', 'overtime', 'Xem dữ liệu tăng ca trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(56, 'view_all_overtime', 'Xem toàn bộ OT', 'overtime', 'Xem toàn bộ dữ liệu tăng ca', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(57, 'approve_overtime', 'Duyệt tăng ca', 'overtime', 'Phê duyệt đơn tăng ca', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(58, 'approve_team_overtime', 'Duyệt OT team', 'overtime', 'Duyệt đơn tăng ca trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(59, 'approve_department_overtime', 'Duyệt OT phòng ban', 'overtime', 'Duyệt đơn tăng ca trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(60, 'view_own_projects', 'Xem dự án cá nhân', 'project', 'Xem các dự án mình tham gia', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(61, 'view_team_projects', 'Xem dự án team', 'project', 'Xem dự án trong phạm vi team', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(62, 'view_department_projects', 'Xem dự án phòng ban', 'project', 'Xem dự án trong phạm vi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(63, 'create_project', 'Tạo dự án', 'project', 'Tạo mới dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(64, 'update_project', 'Cập nhật dự án', 'project', 'Cập nhật thông tin dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(65, 'delete_project', 'Xóa dự án', 'project', 'Xóa hoặc hủy dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(66, 'request_project_change', 'Yêu cầu sửa dự án', 'project', 'Tạo yêu cầu thay đổi dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(67, 'approve_project_change', 'Duyệt sửa dự án', 'project', 'Phê duyệt thay đổi dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(68, 'assign_project_member', 'Phân công thành viên dự án', 'project', 'Gán nhân sự vào dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(69, 'remove_project_member', 'Xóa thành viên dự án', 'project', 'Gỡ nhân sự khỏi dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(70, 'update_project_task_status', 'Cập nhật trạng thái đầu việc', 'project', 'Cập nhật trạng thái task', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(71, 'lock_project', 'Khóa dự án', 'project', 'Khóa chỉnh sửa dự án', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(72, 'view_departments', 'Xem phòng ban', 'organization', 'Xem danh sách phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(73, 'view_positions', 'Xem chức vụ', 'organization', 'Xem danh sách chức vụ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(74, 'request_department_change', 'Yêu cầu sửa phòng ban', 'organization', 'Tạo yêu cầu thay đổi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(75, 'approve_department_change', 'Duyệt sửa phòng ban', 'organization', 'Phê duyệt thay đổi phòng ban', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(76, 'request_position_change', 'Yêu cầu sửa chức vụ', 'organization', 'Tạo yêu cầu thay đổi chức vụ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(77, 'approve_position_change', 'Duyệt sửa chức vụ', 'organization', 'Phê duyệt thay đổi chức vụ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(78, 'view_work_shifts', 'Xem ca làm việc', 'organization', 'Xem danh sách ca làm việc', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(79, 'manage_work_shifts', 'Quản lý ca làm việc', 'organization', 'Thêm, sửa, khóa ca làm việc', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(80, 'view_holidays', 'Xem ngày nghỉ lễ', 'organization', 'Xem danh sách ngày nghỉ lễ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(81, 'manage_holidays', 'Quản lý ngày nghỉ lễ', 'organization', 'Thêm, sửa, xóa ngày nghỉ lễ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(82, 'cancel_requests', 'Hủy yêu cầu', 'approval', 'Hủy yêu cầu đang chờ xử lý khi được phép', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(83, 'view_approval_requests', 'Xem yêu cầu duyệt', 'approval', 'Xem danh sách các yêu cầu cần duyệt', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(84, 'view_financial_reports', 'Xem báo cáo tài chính', 'report', 'Xem báo cáo tài chính hoặc báo cáo chi phí', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(85, 'view_dashboard', 'Xem dashboard tổng quan', 'system', 'Xem dashboard và thông tin tổng quan', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(86, 'manage_settings', 'Quản lý cấu hình hệ thống', 'system', 'Xem và cập nhật cấu hình hệ thống', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(87, 'view_settings', 'Xem cấu hình hệ thống', 'system', 'Xem cấu hình hệ thống', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(88, 'manage_users', 'Quản lý tài khoản', 'system', 'Tạo, sửa, khóa tài khoản người dùng', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(89, 'view_feedbacks', 'Xem phản hồi', 'feedback', 'Xem danh sách feedback nội bộ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(90, 'create_feedback', 'Tạo phản hồi', 'feedback', 'Gửi feedback nội bộ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(91, 'reply_feedback', 'Phản hồi feedback', 'feedback', 'Trả lời feedback nội bộ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(92, 'manage_feedbacks', 'Quản lý phản hồi', 'feedback', 'Quản lý, xử lý feedback nội bộ', 1, 1, '2026-04-17 10:05:12', '2026-04-17 10:24:21'),
(386, 'approve_user_requests', 'Duyet yeu cau nhan su', 'approval', 'Phe duyet tao moi va thay doi thong tin nhan su', 1, 1, '2026-04-24 01:21:15', '2026-04-24 01:21:15'),
(387, 'approve_department_requests', 'Duyet yeu cau phong ban', 'approval', 'Phe duyet tao moi, cap nhat va khoa/mo phong ban', 1, 1, '2026-04-24 01:21:15', '2026-04-24 01:21:15'),
(388, 'approve_salary_requests', 'Duyet yeu cau luong', 'approval', 'Phe duyet thay doi luong co ban', 1, 1, '2026-04-24 01:21:15', '2026-04-24 01:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `position_capability_position`
--

CREATE TABLE `position_capability_position` (
  `position_id` bigint UNSIGNED NOT NULL,
  `capability_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang pivot capability theo tung chuc vu.';

--
-- Dumping data for table `position_capability_position`
--

INSERT INTO `position_capability_position` (`position_id`, `capability_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 2, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 3, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 4, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 5, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 6, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 7, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 8, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 9, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 10, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 11, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 12, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 13, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 14, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 15, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 16, '2026-04-28 07:33:57', '2026-04-28 07:33:57'),
(1, 17, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 18, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 19, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 20, '2026-04-17 09:50:45', '2026-04-17 09:50:45'),
(1, 21, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 22, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 23, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 24, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 25, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 26, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 27, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 28, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 29, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 30, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 31, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 32, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 33, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 34, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 35, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 36, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 37, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 38, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 39, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 40, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 41, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 42, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 43, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 44, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 45, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 46, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 47, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 48, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 49, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 50, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 51, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 52, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 53, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 54, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 55, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 56, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 57, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 58, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 59, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 60, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 61, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 62, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 63, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 64, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 65, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 66, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 67, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 68, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 69, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 70, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 71, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 72, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 73, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 74, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 75, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 76, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 77, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 78, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 79, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 80, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 81, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 82, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 83, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 84, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 85, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 86, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 87, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 88, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 89, '2026-04-17 10:07:19', '2026-04-17 10:07:19'),
(1, 90, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 91, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 92, '2026-04-17 10:07:18', '2026-04-17 10:07:18'),
(1, 386, '2026-04-24 01:21:15', '2026-04-24 01:21:15'),
(1, 387, '2026-04-24 01:21:15', '2026-04-24 01:21:15'),
(1, 388, '2026-04-24 01:21:15', '2026-04-24 01:21:15'),
(2, 22, '2026-04-28 01:39:00', '2026-04-28 01:39:00'),
(2, 23, '2026-04-28 01:39:00', '2026-04-28 01:39:00'),
(2, 28, '2026-04-28 01:43:53', '2026-04-28 01:43:53'),
(2, 35, '2026-04-28 01:39:00', '2026-04-28 01:39:00'),
(2, 36, '2026-04-28 01:39:00', '2026-04-28 01:39:00'),
(2, 37, '2026-04-28 01:40:46', '2026-04-28 01:40:46'),
(2, 40, '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(2, 45, '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(2, 46, '2026-04-28 02:18:00', '2026-04-28 02:18:00'),
(2, 52, '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(2, 53, '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(2, 60, '2026-04-28 02:18:00', '2026-04-28 02:18:00'),
(2, 70, '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(2, 85, '2026-04-28 01:37:23', '2026-04-28 01:37:23'),
(2, 90, '2026-04-28 10:33:51', '2026-04-28 10:33:51'),
(3, 1, '2026-04-21 09:07:33', '2026-04-21 09:07:33'),
(3, 2, '2026-04-28 07:42:01', '2026-04-28 07:42:01'),
(3, 3, '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(3, 5, '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(3, 6, '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(3, 8, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 9, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 10, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 12, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 13, '2026-04-23 09:58:47', '2026-04-23 09:58:47'),
(3, 14, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 16, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 17, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 18, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 19, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 21, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 22, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 23, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 26, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 27, '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(3, 28, '2026-04-20 03:35:33', '2026-04-20 03:35:33'),
(3, 30, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 31, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 32, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 33, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 34, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 35, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 36, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 37, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 38, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 39, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 40, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 41, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 42, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 43, '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(3, 44, '2026-04-23 09:49:10', '2026-04-23 09:49:10'),
(3, 45, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 46, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 47, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 48, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 49, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 50, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 51, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 52, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 53, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 54, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 55, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 56, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 58, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 59, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 60, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 61, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 62, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 68, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 69, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 70, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 72, '2026-04-21 09:01:07', '2026-04-21 09:01:07'),
(3, 73, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 78, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 79, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 80, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 81, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 83, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 84, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 85, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 86, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 89, '2026-04-20 01:31:40', '2026-04-20 01:31:40'),
(3, 90, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 91, '2026-04-20 01:07:24', '2026-04-20 01:07:24'),
(3, 386, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 387, '2026-04-28 10:26:44', '2026-04-28 10:26:44'),
(3, 388, '2026-04-28 10:26:44', '2026-04-28 10:26:44');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planning','in_progress','on_hold','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `start_date`, `end_date`, `status`, `description`, `is_locked`, `locked_at`, `locked_by`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'He thong HRM Cloud', '2026-03-17', '2026-04-29', 'in_progress', 'Du an mau de kiem thu chuc nang HRM.', 0, NULL, NULL, 1, 1, '2026-04-17 09:50:45', '2026-04-29 07:00:57'),
(2, 'Quản lí bán hàng', '2026-04-29', '2026-05-30', 'planning', 'quản lí các mặt hàng và xuất kho và nhập kho hàng', 0, NULL, NULL, 1, 1, '2026-04-29 03:00:45', '2026-04-29 06:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `project_attachments`
--

CREATE TABLE `project_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `implementation_detail_id` bigint UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `disk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_attachments`
--

INSERT INTO `project_attachments` (`id`, `project_id`, `implementation_detail_id`, `uploaded_by`, `disk`, `path`, `original_name`, `mime_type`, `size`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 'local', 'project-attachments/1/F3Lg2twhZBcaBSwAZ0IH1UtiQsQoIRjCjbDg1u0J.webp', 'shopping.webp', 'image/webp', 10962, '2026-04-24 08:56:52', '2026-04-24 08:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `project_detail_comments`
--

CREATE TABLE `project_detail_comments` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `implementation_detail_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_detail_logs`
--

CREATE TABLE `project_detail_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `implementation_detail_id` bigint UNSIGNED NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_detail_logs`
--

INSERT INTO `project_detail_logs` (`id`, `implementation_detail_id`, `field_name`, `old_value`, `new_value`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'is_locked', '', '1', 1, '2026-04-24 09:18:30', '2026-04-24 09:18:30'),
(2, 1, 'is_locked', '1', '', 1, '2026-04-24 09:18:37', '2026-04-24 09:18:37'),
(3, 1, 'progress_percent', '0', '3', 1, '2026-04-24 09:22:12', '2026-04-24 09:22:12'),
(4, 2, 'content', NULL, 'dswfdsf', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(5, 2, 'assigned_to', NULL, '3', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(6, 2, 'execution_date', NULL, '2026-04-29', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(7, 2, 'duration_days', NULL, '2', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(8, 2, 'expected_end_date', NULL, '2026-04-30', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(9, 2, 'detail_status', NULL, 'planned', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(10, 2, 'progress_percent', NULL, '0', 1, '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(11, 1, 'detail_status', 'in_progress', 'completed', 1, '2026-04-29 03:36:59', '2026-04-29 03:36:59'),
(12, 1, 'progress_percent', '3', '100', 1, '2026-04-29 03:36:59', '2026-04-29 03:36:59'),
(13, 2, 'detail_status', 'planned', 'in_progress', 1, '2026-04-29 03:38:34', '2026-04-29 03:38:34'),
(14, 2, 'progress_percent', '0', '1', 1, '2026-04-29 03:38:34', '2026-04-29 03:38:34'),
(15, 2, 'detail_status', 'in_progress', 'cancelled', 1, '2026-04-29 03:38:53', '2026-04-29 03:38:53'),
(16, 2, 'progress_percent', '1', '0', 1, '2026-04-29 03:38:53', '2026-04-29 03:38:53'),
(17, 2, 'detail_status', 'cancelled', 'planned', 1, '2026-04-29 03:43:24', '2026-04-29 03:43:24'),
(18, 1, 'assigned_to', '3', '23', 1, '2026-04-29 03:56:04', '2026-04-29 03:56:04'),
(19, 1, 'expected_end_date', '2026-04-19', '2026-04-18', 1, '2026-04-29 03:56:04', '2026-04-29 03:56:04'),
(20, 2, 'content', 'dswfdsf', 'CHức vụ', 1, '2026-04-29 04:02:37', '2026-04-29 04:02:37'),
(21, 1, 'detail_status', 'completed', 'in_progress', 1, '2026-04-29 06:34:41', '2026-04-29 06:34:41'),
(22, 1, 'progress_percent', '100', '99', 1, '2026-04-29 06:34:41', '2026-04-29 06:34:41'),
(23, 1, 'detail_status', 'in_progress', 'planned', 1, '2026-04-29 06:35:03', '2026-04-29 06:35:03'),
(24, 1, 'progress_percent', '99', '0', 1, '2026-04-29 06:35:03', '2026-04-29 06:35:03'),
(25, 1, 'detail_status', 'planned', 'in_progress', 1, '2026-04-29 07:02:18', '2026-04-29 07:02:18'),
(26, 1, 'progress_percent', '0', '1', 1, '2026-04-29 07:02:18', '2026-04-29 07:02:18'),
(27, 1, 'detail_status', 'in_progress', 'planned', 1, '2026-04-29 07:02:31', '2026-04-29 07:02:31'),
(28, 1, 'progress_percent', '1', '0', 1, '2026-04-29 07:02:31', '2026-04-29 07:02:31'),
(29, 1, 'detail_status', 'planned', 'completed', 1, '2026-04-29 07:05:21', '2026-04-29 07:05:21'),
(30, 1, 'progress_percent', '0', '100', 1, '2026-04-29 07:05:21', '2026-04-29 07:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `project_implementation_details`
--

CREATE TABLE `project_implementation_details` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `project_milestone_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `execution_date` date NOT NULL,
  `duration_days` int UNSIGNED NOT NULL,
  `expected_end_date` date NOT NULL,
  `actual_end_date` date DEFAULT NULL,
  `detail_status` enum('planned','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `progress_percent` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_implementation_details`
--

INSERT INTO `project_implementation_details` (`id`, `project_id`, `project_milestone_id`, `assigned_to`, `content`, `execution_date`, `duration_days`, `expected_end_date`, `actual_end_date`, `detail_status`, `progress_percent`, `is_locked`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 23, 'Lam module Database Seeder', '2026-04-17', 2, '2026-04-18', '2026-04-29', 'completed', 100, 0, 1, 1, '2026-04-17 09:50:45', '2026-04-29 07:05:21'),
(2, 1, NULL, 3, 'CHức vụ', '2026-04-29', 11, '2026-05-12', NULL, 'in_progress', 80, 0, 1, 1, '2026-04-29 03:06:55', '2026-04-29 07:17:21');

-- --------------------------------------------------------

--
-- Table structure for table `project_implementation_subtasks`
--

CREATE TABLE `project_implementation_subtasks` (
  `id` bigint UNSIGNED NOT NULL,
  `project_implementation_detail_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `duration_days` int UNSIGNED NOT NULL DEFAULT '1',
  `due_date` date NOT NULL,
  `actual_end_date` date DEFAULT NULL,
  `deadline_reminded_at` timestamp NULL DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `weight_percent` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_implementation_subtasks`
--

INSERT INTO `project_implementation_subtasks` (`id`, `project_implementation_detail_id`, `project_id`, `assigned_to`, `title`, `description`, `start_date`, `duration_days`, `due_date`, `actual_end_date`, `deadline_reminded_at`, `status`, `weight_percent`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 3, 'xóa', NULL, '2026-04-29', 2, '2026-04-30', '2026-04-29', NULL, 'completed', 100, 1, 1, 1, '2026-04-29 04:13:01', '2026-04-29 04:48:33'),
(2, 2, 1, 3, 'Thêm', NULL, '2026-05-01', 2, '2026-05-02', '2026-04-29', NULL, 'completed', 0, 2, 1, 1, '2026-04-29 04:34:40', '2026-04-29 07:12:22'),
(3, 2, 1, 23, 'cập nhật', NULL, '2026-05-03', 2, '2026-05-04', '2026-04-29', NULL, 'completed', 0, 3, 1, 1, '2026-04-29 04:43:10', '2026-04-29 07:13:03'),
(4, 2, 1, 3, 'Danh sách', NULL, '2026-05-08', 2, '2026-05-09', '2026-04-29', NULL, 'completed', 0, 4, 1, 1, '2026-04-29 04:50:38', '2026-04-29 07:13:09'),
(5, 2, 1, 23, 'Gửi mail', NULL, '2026-05-10', 3, '2026-05-12', NULL, NULL, 'planned', 0, 5, 1, 1, '2026-04-29 07:17:21', '2026-04-29 07:17:21');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `project_role_id` bigint UNSIGNED DEFAULT NULL,
  `joined_at` date DEFAULT NULL,
  `left_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `employee_profile_id`, `project_role_id`, `joined_at`, `left_at`, `is_active`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 6, '2026-03-17', NULL, 1, NULL, '2026-04-17 09:50:45', '2026-04-29 03:32:36'),
(2, 1, 23, 7, '2026-04-29', NULL, 1, NULL, '2026-04-28 01:42:18', '2026-04-29 06:36:06');

-- --------------------------------------------------------

--
-- Table structure for table `project_milestones`
--

CREATE TABLE `project_milestones` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `phase_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `planned_start_date` date DEFAULT NULL,
  `planned_end_date` date DEFAULT NULL,
  `completed_at` date DEFAULT NULL,
  `deadline_reminded_at` timestamp NULL DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_milestones`
--

INSERT INTO `project_milestones` (`id`, `project_id`, `phase_name`, `name`, `description`, `planned_start_date`, `planned_end_date`, `completed_at`, `deadline_reminded_at`, `status`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kiểm thử', 'test', NULL, '2026-04-22', '2026-04-29', NULL, NULL, 'in_progress', 1, 1, 1, '2026-04-29 08:07:36', '2026-04-29 08:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `project_progress_histories`
--

CREATE TABLE `project_progress_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `old_progress` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `new_progress` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `changed_at` timestamp NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_progress_histories`
--

INSERT INTO `project_progress_histories` (`id`, `project_id`, `old_progress`, `new_progress`, `changed_at`, `changed_by`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, '2026-04-24 09:22:12', 1, 'Cập nhật trạng thái đầu việc: 1% -> 3% (theo trọng số số ngày đầu việc)', '2026-04-24 09:22:12', '2026-04-24 09:22:12'),
(2, 2, 0, 10, '2026-04-29 03:00:45', 1, 'Tạo mới dự án', '2026-04-29 03:00:45', '2026-04-29 03:00:45'),
(3, 1, 3, 2, '2026-04-29 03:06:55', 1, 'Thêm đầu việc triển khai: 3% -> 2% (theo trọng số số ngày đầu việc)', '2026-04-29 03:06:55', '2026-04-29 03:06:55'),
(4, 1, 2, 50, '2026-04-29 03:36:59', 1, 'Cập nhật trạng thái đầu việc: 2% -> 50% (theo trọng số số ngày đầu việc)', '2026-04-29 03:36:59', '2026-04-29 03:36:59'),
(5, 1, 50, 51, '2026-04-29 03:38:34', 1, 'Cập nhật trạng thái đầu việc: 50% -> 51% (theo trọng số số ngày đầu việc)', '2026-04-29 03:38:34', '2026-04-29 03:38:34'),
(6, 1, 51, 100, '2026-04-29 03:38:53', 1, 'Cập nhật trạng thái đầu việc: 51% -> 100% (theo trọng số số ngày đầu việc)', '2026-04-29 03:38:53', '2026-04-29 03:38:53'),
(7, 1, 100, 50, '2026-04-29 03:43:24', 1, 'Cập nhật trạng thái đầu việc: 100% -> 50% (theo trọng số số ngày đầu việc)', '2026-04-29 03:43:24', '2026-04-29 03:43:24'),
(8, 1, 50, 100, '2026-04-29 04:14:46', 1, 'Cập nhật trạng thái công việc con: 50% -> 100% (theo trọng số số ngày đầu việc)', '2026-04-29 04:14:46', '2026-04-29 04:14:46'),
(9, 1, 100, 67, '2026-04-29 04:34:40', 1, 'Thêm công việc con: 100% -> 67% (theo trọng số số ngày đầu việc)', '2026-04-29 04:34:40', '2026-04-29 04:34:40'),
(10, 1, 75, 50, '2026-04-29 04:48:28', 1, 'Cập nhật trạng thái công việc con: 75% -> 50% (theo trọng số số ngày đầu việc)', '2026-04-29 04:48:28', '2026-04-29 04:48:28'),
(11, 1, 50, 75, '2026-04-29 04:48:33', 1, 'Cập nhật trạng thái công việc con: 50% -> 75% (theo trọng số số ngày đầu việc)', '2026-04-29 04:48:33', '2026-04-29 04:48:33'),
(12, 1, 75, 50, '2026-04-29 04:50:02', 1, 'Cập nhật trạng thái công việc con: 75% -> 50% (theo trọng số số ngày đầu việc)', '2026-04-29 04:50:02', '2026-04-29 04:50:02'),
(13, 1, 50, 40, '2026-04-29 04:50:38', 1, 'Thêm công việc con: 50% -> 40% (theo trọng số số ngày đầu việc)', '2026-04-29 04:50:38', '2026-04-29 04:50:38'),
(14, 1, 40, 20, '2026-04-29 06:35:03', 1, 'Cập nhật trạng thái đầu việc: 40% -> 20% (theo trọng số số ngày đầu việc)', '2026-04-29 06:35:03', '2026-04-29 06:35:03'),
(15, 1, 20, 40, '2026-04-29 07:05:21', 1, 'Cập nhật trạng thái đầu việc: 20% -> 40% (theo trọng số số ngày đầu việc)', '2026-04-29 07:05:21', '2026-04-29 07:05:21'),
(16, 1, 40, 60, '2026-04-29 07:12:22', 1, 'Cập nhật trạng thái công việc con: 40% -> 60% (theo trọng số số ngày đầu việc)', '2026-04-29 07:12:22', '2026-04-29 07:12:22'),
(17, 1, 60, 80, '2026-04-29 07:13:03', 1, 'Cập nhật trạng thái công việc con: 60% -> 80% (theo trọng số số ngày đầu việc)', '2026-04-29 07:13:03', '2026-04-29 07:13:03'),
(18, 1, 80, 100, '2026-04-29 07:13:09', 1, 'Cập nhật trạng thái công việc con: 80% -> 100% (theo trọng số số ngày đầu việc)', '2026-04-29 07:13:09', '2026-04-29 07:13:09'),
(19, 1, 100, 83, '2026-04-29 07:17:21', 1, 'Thêm công việc con: 100% -> 83% (theo trọng số số ngày đầu việc)', '2026-04-29 07:17:21', '2026-04-29 07:17:21');

-- --------------------------------------------------------

--
-- Table structure for table `project_roles`
--

CREATE TABLE `project_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `permissions` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_roles`
--

INSERT INTO `project_roles` (`id`, `project_id`, `name`, `description`, `permissions`, `created_at`, `updated_at`) VALUES
(6, 1, 'Trưởng nhóm', NULL, '[]', '2026-04-29 03:32:24', '2026-04-29 03:32:24'),
(7, 1, 'Nhân viên', NULL, '[\"remove_project_member\", \"create_implementation_detail\", \"add_project_member\"]', '2026-04-29 03:33:07', '2026-04-29 08:33:26');

-- --------------------------------------------------------

--
-- Table structure for table `project_work_logs`
--

CREATE TABLE `project_work_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `project_implementation_detail_id` bigint UNSIGNED NOT NULL,
  `project_implementation_subtask_id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `work_date` date NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(35, 'HNI', 'Thành phố Hà Nội', NULL, 1, NULL, NULL),
(36, 'CBG', 'Cao Bằng', NULL, 1, NULL, NULL),
(37, 'TGQ', 'Tuyên Quang', NULL, 1, NULL, NULL),
(38, 'DBN', 'Điện Biên', NULL, 1, NULL, NULL),
(39, 'LCU', 'Lai Châu', NULL, 1, NULL, NULL),
(40, 'SLA', 'Sơn La', NULL, 1, NULL, NULL),
(41, 'LCI', 'Lào Cai', NULL, 1, NULL, NULL),
(42, 'TNN', 'Thái Nguyên', NULL, 1, NULL, NULL),
(43, 'LSN', 'Lạng Sơn', NULL, 1, NULL, NULL),
(44, 'QNH', 'Quảng Ninh', NULL, 1, NULL, NULL),
(45, 'BNH', 'Bắc Ninh', NULL, 1, NULL, NULL),
(46, 'PTO', 'Phú Thọ', NULL, 1, NULL, NULL),
(47, 'HPG', 'Thành phố Hải Phòng', NULL, 1, NULL, NULL),
(48, 'HYN', 'Hưng Yên', NULL, 1, NULL, NULL),
(49, 'NBH', 'Ninh Bình', NULL, 1, NULL, NULL),
(50, 'THA', 'Thanh Hóa', NULL, 1, NULL, NULL),
(51, 'NAN', 'Nghệ An', NULL, 1, NULL, NULL),
(52, 'HTH', 'Hà Tĩnh', NULL, 1, NULL, NULL),
(53, 'QTI', 'Quảng Trị', NULL, 1, NULL, NULL),
(54, 'TTH', 'Thành phố Huế', NULL, 1, NULL, NULL),
(55, 'DNG', 'Thành phố Đà Nẵng', NULL, 1, NULL, NULL),
(56, 'QNI', 'Quảng Ngãi', NULL, 1, NULL, NULL),
(57, 'GLI', 'Gia Lai', NULL, 1, NULL, NULL),
(58, 'KHA', 'Khánh Hòa', NULL, 1, NULL, NULL),
(59, 'DLK', 'Đắk Lắk', NULL, 1, NULL, NULL),
(60, 'LDG', 'Lâm Đồng', NULL, 1, NULL, NULL),
(61, 'DNI', 'Đồng Nai', NULL, 1, NULL, NULL),
(62, 'HCM', 'Thành phố Hồ Chí Minh', NULL, 1, NULL, NULL),
(63, 'TNH', 'Tây Ninh', NULL, 1, NULL, NULL),
(64, 'DTP', 'Đồng Tháp', NULL, 1, NULL, NULL),
(65, 'VLG', 'Vĩnh Long', NULL, 1, NULL, NULL),
(66, 'AGG', 'An Giang', NULL, 1, NULL, NULL),
(67, 'CTO', 'Thành phố Cần Thơ', NULL, 1, NULL, NULL),
(68, 'CMU', 'Cà Mau', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salary_adjustments`
--

CREATE TABLE `salary_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_adjustments`
--

INSERT INTO `salary_adjustments` (`id`, `employee_profile_id`, `month`, `year`, `type`, `label`, `amount`, `note`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 3, 4, 2026, 'allowance', 'xăng xe', 100000.00, 'cty phụ cấp thêm', 1, 1, '2026-04-21 02:27:48', '2026-04-21 02:27:48'),
(3, 4, 4, 2026, 'allowance', 'cty thưởng vì đạt tiến độ', 150000.00, 'có cô gắng', 1, 1, '2026-04-22 09:22:21', '2026-04-22 09:22:21');

-- --------------------------------------------------------

--
-- Table structure for table `salary_histories`
--

CREATE TABLE `salary_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `old_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `new_salary` decimal(15,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `effective_date` date NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `requested_by` bigint UNSIGNED DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_histories`
--

INSERT INTO `salary_histories` (`id`, `employee_profile_id`, `old_salary`, `new_salary`, `currency`, `effective_date`, `approved_by`, `requested_by`, `note`, `created_at`, `updated_at`) VALUES
(1, 2, 0.00, 10000000.00, 'VND', '2026-04-24', 1, 4, 'rất chi là được việc', '2026-04-24 01:29:32', '2026-04-28 08:07:14'),
(2, 23, 5000000.00, 7000000.00, 'VND', '2026-04-28', 1, 1, 'làm tốt lắm', '2026-04-28 09:41:39', '2026-04-28 09:41:39'),
(3, 23, 7000000.00, 7500000.00, 'VND', '2026-04-28', 1, 1, 'Cập nhật lương trực tiếp từ danh sách nhân sự', '2026-04-28 09:42:02', '2026-04-28 09:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `salary_snapshots`
--

CREATE TABLE `salary_snapshots` (
  `id` bigint UNSIGNED NOT NULL,
  `payroll_period_id` bigint UNSIGNED NOT NULL,
  `employee_profile_id` bigint UNSIGNED NOT NULL,
  `employee_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `base_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `approved_work_units` decimal(8,2) NOT NULL DEFAULT '0.00',
  `approved_overtime_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `base_salary_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `overtime_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allowance_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pending_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `attendance_deduction_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `manual_deduction_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deduction_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `warning_count` int UNSIGNED NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_snapshots`
--

INSERT INTO `salary_snapshots` (`id`, `payroll_period_id`, `employee_profile_id`, `employee_code`, `employee_name`, `department_name`, `position_name`, `currency`, `base_salary`, `approved_work_units`, `approved_overtime_minutes`, `base_salary_amount`, `overtime_amount`, `allowance_amount`, `pending_amount`, `attendance_deduction_amount`, `manual_deduction_amount`, `deduction_amount`, `net_amount`, `warning_count`, `payload`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'EMP-001', 'Nguyen Van A', 'Phong ky thuat', 'Lap trinh vien', 'VND', 15000000.00, 2.00, 0, 1764705.88, 0.00, 103434.00, 0.00, 13235294.12, 0.00, 13235294.12, 1868139.88, 1, '{\"profile\": {\"id\": 3, \"name\": \"Nguyen Van A\", \"email\": \"employee1@gmail.com\", \"currency\": \"VND\", \"position\": \"Lap trinh vien\", \"department\": \"Phong ky thuat\", \"employee_code\": \"EMP-001\", \"employment_type\": \"official\"}, \"records\": [{\"id\": 1, \"work_date\": \"2026-04-17\", \"work_unit\": 1, \"day_status\": \"present\", \"shift_name\": \"Ca hanh chinh\", \"check_in_at\": \"08:05\", \"check_out_at\": \"17:30\", \"approval_note\": null, \"payable_amount\": 882352.94, \"worked_minutes\": 505, \"approval_status\": \"approved\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:00\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 0, \"overtime_rate_source\": \"multiplier\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": \"holiday-2026-04-20\", \"work_date\": \"2026-04-20\", \"work_unit\": 1, \"day_status\": \"holiday_paid\", \"shift_name\": \"Giỗ tổ HùngVương\", \"approval_note\": \"Ngay le co luong\", \"payable_amount\": 882352.94, \"worked_minutes\": 0, \"approval_status\": \"approved\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"Nghi le co luong\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 3, \"overtime_type_label\": \"Ngay le\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 9, \"work_date\": \"2026-04-21\", \"work_unit\": 0, \"day_status\": \"absent\", \"shift_name\": \"Ca hanh chinh\", \"check_in_at\": null, \"check_out_at\": null, \"approval_note\": null, \"payable_amount\": 0, \"worked_minutes\": 0, \"approval_status\": \"pending\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:00\", \"attendance_status\": \"absent\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 0, \"overtime_rate_source\": \"multiplier\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}], \"summary\": {\"warnings\": [\"Co 1 ban ghi cho duyet.\"], \"daily_rate\": 882352.94, \"net_amount\": 1868139.88, \"base_salary\": 15000000, \"hourly_rate\": 110294.12, \"gross_amount\": 1868139.88, \"total_records\": 2, \"warning_count\": 1, \"pending_amount\": 0, \"overtime_amount\": 0, \"pending_records\": 1, \"allowance_amount\": 103434, \"approved_records\": 1, \"deduction_amount\": 13235294.12, \"rejected_records\": 0, \"unpaid_work_units\": 15, \"base_salary_amount\": 1764705.88, \"expected_work_days\": 17, \"pending_work_units\": 0, \"approved_work_units\": 2, \"manual_deduction_amount\": 0, \"approved_overtime_minutes\": 0, \"attendance_deduction_amount\": 13235294.12}, \"adjustments\": [{\"id\": 7, \"note\": null, \"type\": \"allowance\", \"label\": \"fsdf\", \"amount\": 3434}, {\"id\": 1, \"note\": \"cty phụ cấp thêm\", \"type\": \"allowance\", \"label\": \"xăng xe\", \"amount\": 100000}], \"salaryHistory\": []}', '2026-04-21 01:53:24', '2026-04-23 01:35:35'),
(2, 1, 4, 'EMP-002', 'hieu duy', 'Phong ky thuat', 'Lap trinh vien', 'VND', 5000000.00, 2.50, 0, 625000.00, 0.00, 150000.00, 0.00, 4375000.00, 0.00, 4375000.00, 775000.00, 0, '{\"profile\": {\"id\": 4, \"name\": \"hieu duy\", \"email\": \"dophuhieu15@gmail.com\", \"currency\": \"VND\", \"position\": \"Lap trinh vien\", \"department\": \"Phong ky thuat\", \"employee_code\": \"EMP-002\", \"employment_type\": \"official\"}, \"records\": [{\"id\": 4, \"work_date\": \"2026-04-20\", \"work_unit\": 0.5, \"day_status\": \"early_leave\", \"shift_name\": \"ca sáng hành chính\", \"check_in_at\": \"08:07\", \"check_out_at\": \"17:00\", \"approval_note\": \"ok\", \"payable_amount\": 125000, \"worked_minutes\": 442, \"approval_status\": \"approved\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:30\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 3, \"overtime_type_label\": \"Ngay le\", \"overtime_hourly_rate\": 50000, \"overtime_rate_source\": \"catalog\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 6, \"work_date\": \"2026-04-21\", \"work_unit\": 1, \"day_status\": \"present\", \"shift_name\": \"ca sáng hành chính\", \"check_in_at\": \"08:00\", \"check_out_at\": \"17:32\", \"approval_note\": \"đi làm tốt\", \"payable_amount\": 250000, \"worked_minutes\": 482, \"approval_status\": \"approved\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:30\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 50000, \"overtime_rate_source\": \"catalog\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 7, \"work_date\": \"2026-04-22\", \"work_unit\": 1, \"day_status\": \"present\", \"shift_name\": \"ca sáng hành chính\", \"check_in_at\": \"08:05\", \"check_out_at\": \"17:30\", \"approval_note\": \"quên ok\", \"payable_amount\": 250000, \"worked_minutes\": 474, \"approval_status\": \"approved\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:30\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 50000, \"overtime_rate_source\": \"catalog\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}], \"summary\": {\"warnings\": [], \"daily_rate\": 250000, \"net_amount\": 775000, \"base_salary\": 5000000, \"hourly_rate\": 31250, \"gross_amount\": 775000, \"total_records\": 3, \"warning_count\": 0, \"pending_amount\": 0, \"overtime_amount\": 0, \"pending_records\": 0, \"allowance_amount\": 150000, \"approved_records\": 3, \"deduction_amount\": 4375000, \"rejected_records\": 0, \"unpaid_work_units\": 17.5, \"base_salary_amount\": 625000, \"expected_work_days\": 20, \"pending_work_units\": 0, \"approved_work_units\": 2.5, \"manual_deduction_amount\": 0, \"approved_overtime_minutes\": 0, \"attendance_deduction_amount\": 4375000}, \"adjustments\": [{\"id\": 3, \"note\": \"có cô gắng\", \"type\": \"allowance\", \"label\": \"cty thưởng vì đạt tiến độ\", \"amount\": 150000}], \"salaryHistory\": []}', '2026-04-21 01:53:24', '2026-04-23 01:35:35'),
(3, 1, 1, 'EMP-GTVBEHIEU', 'GTV Be Hieu', 'Ban dieu hanh he thong', 'System Full Access', 'VND', 0.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5, '{\"profile\": {\"id\": 1, \"name\": \"GTV Be Hieu\", \"email\": \"gtvbehieu@gmail.com\", \"currency\": \"VND\", \"position\": \"System Full Access\", \"department\": \"Ban dieu hanh he thong\", \"employee_code\": \"EMP-GTVBEHIEU\", \"employment_type\": \"official\"}, \"records\": [{\"id\": 2, \"work_date\": \"2026-04-17\", \"work_unit\": 0, \"day_status\": \"early_leave\", \"shift_name\": \"Ca mac dinh\", \"check_in_at\": \"17:19\", \"check_out_at\": \"17:19\", \"approval_note\": \"quá trễ giờ\", \"payable_amount\": 0, \"worked_minutes\": 0, \"approval_status\": \"rejected\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"-\", \"attendance_status\": \"late\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 0, \"overtime_rate_source\": \"multiplier\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 3, \"work_date\": \"2026-04-20\", \"work_unit\": 0, \"day_status\": \"missing_check_out\", \"shift_name\": \"Ca mac dinh\", \"check_in_at\": \"08:00\", \"check_out_at\": null, \"approval_note\": null, \"payable_amount\": 0, \"worked_minutes\": 569, \"approval_status\": \"pending\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"-\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 3, \"overtime_type_label\": \"Ngay le\", \"overtime_hourly_rate\": 0, \"overtime_rate_source\": \"multiplier\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 5, \"work_date\": \"2026-04-21\", \"work_unit\": 0, \"day_status\": \"missing_check_out\", \"shift_name\": \"Ca mac dinh\", \"check_in_at\": \"07:59\", \"check_out_at\": null, \"approval_note\": null, \"payable_amount\": 0, \"worked_minutes\": 570, \"approval_status\": \"pending\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"-\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 0, \"overtime_rate_source\": \"multiplier\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 10, \"work_date\": \"2026-04-22\", \"work_unit\": 0, \"day_status\": \"early_leave\", \"shift_name\": \"ca sáng hành chính\", \"check_in_at\": \"13:35\", \"check_out_at\": \"13:44\", \"approval_note\": null, \"payable_amount\": 0, \"worked_minutes\": 9, \"approval_status\": \"pending\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:30\", \"attendance_status\": \"late\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 50000, \"overtime_rate_source\": \"catalog\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}], \"summary\": {\"warnings\": [\"Chua khai bao luong co ban.\", \"Co 2 ban ghi thieu check-in/check-out.\", \"Co 3 ban ghi cho duyet.\", \"Co 1 ban ghi bi tu choi.\", \"Chua co cong duyet hop le de tinh luong.\"], \"daily_rate\": 0, \"net_amount\": 0, \"base_salary\": 0, \"hourly_rate\": 0, \"gross_amount\": 0, \"total_records\": 4, \"warning_count\": 5, \"pending_amount\": 0, \"overtime_amount\": 0, \"pending_records\": 3, \"allowance_amount\": 0, \"approved_records\": 0, \"deduction_amount\": 0, \"rejected_records\": 1, \"unpaid_work_units\": 17, \"base_salary_amount\": 0, \"expected_work_days\": 17, \"pending_work_units\": 0, \"approved_work_units\": 0, \"manual_deduction_amount\": 0, \"approved_overtime_minutes\": 0, \"attendance_deduction_amount\": 0}, \"adjustments\": [], \"salaryHistory\": []}', '2026-04-21 01:53:24', '2026-04-23 01:35:35'),
(4, 1, 2, 'EMP-HR-001', 'HR Manager', 'Phong nhan su', 'Truong phong HR', 'VND', 0.00, 1.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3, '{\"profile\": {\"id\": 2, \"name\": \"HR Manager\", \"email\": \"hr@gmail.com\", \"currency\": \"VND\", \"position\": \"Truong phong HR\", \"department\": \"Phong nhan su\", \"employee_code\": \"EMP-HR-001\", \"employment_type\": \"official\"}, \"records\": [{\"id\": \"holiday-2026-04-20\", \"work_date\": \"2026-04-20\", \"work_unit\": 1, \"day_status\": \"holiday_paid\", \"shift_name\": \"Giỗ tổ HùngVương\", \"approval_note\": \"Ngay le co luong\", \"payable_amount\": 0, \"worked_minutes\": 0, \"approval_status\": \"approved\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"Nghi le co luong\", \"attendance_status\": \"on_time\", \"overtime_multiplier\": 3, \"overtime_type_label\": \"Ngay le\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}, {\"id\": 8, \"work_date\": \"2026-04-21\", \"work_unit\": 0, \"day_status\": \"absent\", \"shift_name\": \"Ca hanh chinh\", \"check_in_at\": null, \"check_out_at\": null, \"approval_note\": \"Auto marked unpaid leave\", \"payable_amount\": 0, \"worked_minutes\": 0, \"approval_status\": \"rejected\", \"overtime_amount\": 0, \"overtime_minutes\": 0, \"shift_time_range\": \"08:00 - 17:00\", \"attendance_status\": \"absent\", \"overtime_multiplier\": 1.5, \"overtime_type_label\": \"Ngay thuong\", \"overtime_hourly_rate\": 0, \"overtime_rate_source\": \"multiplier\", \"shift_half_day_minutes\": 240, \"shift_standard_minutes\": 480}], \"summary\": {\"warnings\": [\"Chua khai bao luong co ban.\", \"Co 1 ban ghi bi tu choi.\", \"Chua co cong duyet hop le de tinh luong.\"], \"daily_rate\": 0, \"net_amount\": 0, \"base_salary\": 0, \"hourly_rate\": 0, \"gross_amount\": 0, \"total_records\": 1, \"warning_count\": 3, \"pending_amount\": 0, \"overtime_amount\": 0, \"pending_records\": 0, \"allowance_amount\": 0, \"approved_records\": 0, \"deduction_amount\": 0, \"rejected_records\": 1, \"unpaid_work_units\": 16, \"base_salary_amount\": 0, \"expected_work_days\": 17, \"pending_work_units\": 0, \"approved_work_units\": 1, \"manual_deduction_amount\": 0, \"approved_overtime_minutes\": 0, \"attendance_deduction_amount\": 0}, \"adjustments\": [], \"salaryHistory\": []}', '2026-04-21 01:53:24', '2026-04-23 01:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP dang nhap cua session.',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Thong tin trinh duyet/thiet bi.',
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Du lieu session da duoc serialize.',
  `login_at` timestamp NULL DEFAULT NULL COMMENT 'Thoi diem bat dau dang nhap.',
  `logout_at` time DEFAULT NULL COMMENT 'Thoi diem dang xuat neu co.',
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ten thiet bi neu ung dung co gui len.',
  `session_type` enum('web','mobile','api') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT 'Loai phien dang nhap.',
  `last_activity` int NOT NULL COMMENT 'Moc thoi gian hoat dong cuoi dang unix timestamp.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu phien dang nhap cua nguoi dung.';

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `login_at`, `logout_at`, `device_name`, `session_type`, `last_activity`) VALUES
('esILnEEdFMx9C7hnMNvoQ7muhFcYk6Jln6MxzGZe', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoibXVqdXZkTk5tMkFicWtvaDZZd3FscUlpdVNEME1IUE1EMUtxY2l2dCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcHJvamVjdHMiO3M6NToicm91dGUiO3M6MTQ6InByb2plY3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNmI4NGI5OGM4MGM4MDc2N2RkY2Y1ZjZkMzA4YTBiMTc4MDViZTUxMzY4YjYzMDZkMGI0NjcyN2VkNDQ2MTlmNCI7fQ==', NULL, NULL, NULL, 'web', 1777453066),
('Iu0tJQASTRYsqyqbUjzztyxTXOTG2sWxdhaPCKZc', 23, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiWHVWeTZFeHZ5Q2daNXZub0hNSzc3cnlBWHRaU2RIbkR4Y3dnWWxrUyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbXktcHJvamVjdHMiO3M6NToicm91dGUiO3M6MTM6InByb2plY3RzLm1pbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyMztzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZmM5MmRjZmVlYTJkZDViYWQ0MmU1Zjg2NjZmZjgxNjQ5YTcwMTExYjYxZjI2OTczOGYzYmJmYTgyMjIzM2MzMyI7fQ==', NULL, NULL, NULL, 'web', 1777451614),
('tecqjmzMYu1ECVLBZMmK2wQvEFUM4xl3WLiNunzg', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiSHA3YXZZUXRKOTlFaTZjcU5hRFVCaDZGbjc1MEZLeFJST1poZjZxNyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbXktcHJvamVjdHMiO3M6NToicm91dGUiO3M6MTM6InByb2plY3RzLm1pbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI2Yjg0Yjk4YzgwYzgwNzY3ZGRjZjVmNmQzMDhhMGIxNzgwNWJlNTEzNjhiNjMwNmQwYjQ2NzI3ZWQ0NDYxOWY0Ijt9', NULL, NULL, NULL, 'web', 1777453145),
('vSt7wxP2qyA13aqPII1BGvj5FVjLseShGLEntU5F', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiU0UyRUFhbkpMRmd3cFR4YnRmOHFhNklxVGlicXhwSHlYUUxyWktZZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcHJvamVjdHMiO3M6NToicm91dGUiO3M6MTQ6InByb2plY3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNTAxZmJmNGIyODM5ZDE4NzkyNWY4MWNjYjU0M2EyZDJmM2YwZmE0ZDIzOTQ3MTE1NDUyMzRmNmIwZTNiNzIzZSI7fQ==', NULL, NULL, NULL, 'web', 1777452966);

-- --------------------------------------------------------

--
-- Table structure for table `social_accounts`
--

CREATE TABLE `social_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_accounts`
--

INSERT INTO `social_accounts` (`id`, `user_id`, `provider`, `provider_id`, `provider_email`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 1, 'google', '116268031580956176186', 'gtvbehieu@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocKZGjJRGNeV1Slt712ttqYTETRoCHFLM4cfF54zBDllN4hRphnY=s96-c', '2026-04-17 09:52:46', '2026-04-17 09:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` text COLLATE utf8mb4_unicode_ci COMMENT 'Anh thumbnail nho dung cho danh sach hoac preview.',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Slug than thien de tao URL ho so.',
  `creater_id` bigint DEFAULT NULL,
  `status` enum('active','inactive','blocked','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Trang thai tai khoan: active, inactive, blocked, pending.',
  `is_employee` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `zalo_verified` tinyint(1) NOT NULL DEFAULT '0',
  `zalo_verified_at` timestamp NULL DEFAULT NULL,
  `zalo_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang luu thong tin tai khoan nguoi dung va khach hang.';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `phone`, `password`, `remember_token`, `address`, `avatar`, `thumbnail`, `slug`, `creater_id`, `status`, `is_employee`, `email_verified_at`, `zalo_verified`, `zalo_verified_at`, `zalo_user_id`, `last_login_at`, `last_login_ip`, `created_at`, `updated_at`) VALUES
(1, 'GTV Be Hieu', 'gtvbehieu@gmail.com', 'gtvbehieu', '0353234018', '$2y$12$5qOpM1HhkLRimVPjMpjTROlyXGHee1LsL5nmPWenWpx/P.5gnv4pm', NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKZGjJRGNeV1Slt712ttqYTETRoCHFLM4cfF54zBDllN4hRphnY=s96-c', 'https://lh3.googleusercontent.com/a/ACg8ocKZGjJRGNeV1Slt712ttqYTETRoCHFLM4cfF54zBDllN4hRphnY=s96-c', NULL, NULL, 'active', 1, '2026-04-17 09:52:46', 0, NULL, NULL, '2026-04-29 01:02:54', '127.0.0.1', '2026-04-17 09:50:45', '2026-04-29 01:02:54'),
(2, 'HR Manager', 'hr@gmail.com', 'hr@gmail.com', '0123478887', '$2y$12$bxyVTLU28IBAfsUjhZJ84u2mDStv2JBOpwkLIliK.AYyG1H6ndwHa', NULL, NULL, NULL, NULL, NULL, NULL, 'active', 1, NULL, 0, NULL, NULL, NULL, NULL, '2026-04-17 09:50:45', '2026-04-24 01:18:33'),
(3, 'Nguyen Van A', 'employee1@gmail.com', 'employee1@gmail.com', '0123456788', '$2y$12$6Ujx0r9XcwGpAVD/RulfxuA7Sa/DklCavBm8zzflT9z4VdPk2dMFq', NULL, NULL, NULL, NULL, NULL, NULL, 'active', 1, NULL, 0, NULL, NULL, NULL, NULL, '2026-04-17 09:50:45', '2026-04-22 03:03:07'),
(4, 'hieu duy', 'dophuhieu15@gmail.com', 'dophuhieu15@gmail.com', '0123456789', '$2y$12$yUCMXCOwJNTgPk7bJ3RfSeIEAMgiPhijOZ/sxYtlEKsmHwwM7dLhG', NULL, NULL, '/storage/avatars/avatar_4_1776736986.jpg', NULL, 'hieu-duy-CL2BiJ', 1, 'active', 1, NULL, 0, NULL, NULL, '2026-04-29 08:59:01', '127.0.0.1', '2026-04-20 01:04:05', '2026-04-29 08:59:01'),
(23, 'thi', 'thisoma1308@gmail.com', 'thisoma1308@gmail.com', '0354133621', '$2y$12$pN8KtF43USrg.SyqLty/QOvfuxqqQkUjv2dzfR5Alc92gq8v7hrjK', NULL, NULL, NULL, NULL, 'thi-uh7FPb', 1, 'active', 1, NULL, 0, NULL, NULL, '2026-04-29 01:03:30', '127.0.0.1', '2026-04-28 01:33:46', '2026-04-29 01:03:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_position_capability_overrides`
--

CREATE TABLE `user_position_capability_overrides` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `capability_id` bigint UNSIGNED NOT NULL,
  `effect` enum('allow','deny') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bang override capability theo user so voi quyen theo chuc vu.';

--
-- Dumping data for table `user_position_capability_overrides`
--

INSERT INTO `user_position_capability_overrides` (`id`, `user_id`, `capability_id`, `effect`, `reason`, `expires_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 4, 82, 'allow', 'ok', '2026-04-20 09:31:00', 1, '2026-04-20 09:30:18', '2026-04-20 09:30:18'),
(2, 1, 35, 'deny', 'Disable self-service attendance, leave, and personal salary for system operator account.', NULL, NULL, '2026-04-23 09:38:31', '2026-04-23 09:38:31'),
(3, 1, 36, 'deny', 'Disable self-service attendance, leave, and personal salary for system operator account.', NULL, NULL, '2026-04-23 09:38:31', '2026-04-23 09:38:31'),
(4, 1, 40, 'deny', 'Disable self-service attendance, leave, and personal salary for system operator account.', NULL, NULL, '2026-04-23 09:38:31', '2026-04-23 09:38:31'),
(5, 1, 37, 'deny', 'Disable self-service attendance, leave, and personal salary for system operator account.', NULL, NULL, '2026-04-23 09:38:31', '2026-04-23 09:38:31'),
(6, 1, 28, 'deny', 'Disable self-service attendance, leave, and personal salary for system operator account.', NULL, NULL, '2026-04-23 09:38:31', '2026-04-23 09:38:31'),
(8, 23, 388, 'allow', NULL, '2026-04-30 08:01:00', 1, '2026-04-28 08:01:19', '2026-04-28 08:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `wards`
--

CREATE TABLE `wards` (
  `id` bigint UNSIGNED NOT NULL,
  `province_id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wards`
--

INSERT INTO `wards` (`id`, `province_id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 35, NULL, 'Phường Hoàn Kiếm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(2, 35, NULL, 'Phường Cửa Nam', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(3, 35, NULL, 'Phường Ba Đình', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(4, 35, NULL, 'Phường Ngọc Hà', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(5, 35, NULL, 'Phường Giảng Võ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(6, 35, NULL, 'Phường Hai Bà Trưng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(7, 35, NULL, 'Phường Vĩnh Tuy', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(8, 35, NULL, 'Phường Bạch Mai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(9, 35, NULL, 'Phường Đống Đa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(10, 35, NULL, 'Phường Kim Liên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(11, 35, NULL, 'Phường Văn Miếu - Quốc Tử Giám', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(12, 35, NULL, 'Phường Láng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(13, 35, NULL, 'Phường Ô Chợ Dừa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(14, 35, NULL, 'Phường Hồng Hà', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(15, 35, NULL, 'Phường Lĩnh Nam', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(16, 35, NULL, 'Phường Hoàng Mai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(17, 35, NULL, 'Phường Vĩnh Hưng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(18, 35, NULL, 'Phường Tương Mai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(19, 35, NULL, 'Phường Định Công', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(20, 35, NULL, 'Phường Hoàng Liệt', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(21, 35, NULL, 'Phường Yên Sở', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(22, 35, NULL, 'Phường Thanh Xuân', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(23, 35, NULL, 'Phường Khương Đình', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(24, 35, NULL, 'Phường Phương Liệt', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(25, 35, NULL, 'Phường Cầu Giấy', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(26, 35, NULL, 'Phường Nghĩa Đô', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(27, 35, NULL, 'Phường Yên Hòa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(28, 35, NULL, 'Phường Tây Hồ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(29, 35, NULL, 'Phường Phú Thượng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(30, 35, NULL, 'Phường Tây Tựu', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(31, 35, NULL, 'Phường Phú Diễn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(32, 35, NULL, 'Phường Xuân Đỉnh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(33, 35, NULL, 'Phường Đông Ngạc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(34, 35, NULL, 'Phường Thượng Cát', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(35, 35, NULL, 'Phường Từ Liêm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(36, 35, NULL, 'Phường Xuân Phương', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(37, 35, NULL, 'Phường Tây Mỗ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(38, 35, NULL, 'Phường Đại Mỗ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(39, 35, NULL, 'Phường Long Biên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(40, 35, NULL, 'Phường Bồ Đề', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(41, 35, NULL, 'Phường Việt Hưng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(42, 35, NULL, 'Phường Phúc Lợi', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(43, 35, NULL, 'Phường Hà Đông', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(44, 35, NULL, 'Phường Dương Nội', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(45, 35, NULL, 'Phường Yên Nghĩa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(46, 35, NULL, 'Phường Phú Lương', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(47, 35, NULL, 'Phường Kiến Hưng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(48, 35, NULL, 'Xã Thanh Trì', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(49, 35, NULL, 'Xã Đại Thanh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(50, 35, NULL, 'Xã Nam Phù', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(51, 35, NULL, 'Xã Ngọc Hồi', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(52, 35, NULL, 'Phường Thanh Liệt', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(53, 35, NULL, 'Xã Thượng Phúc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(54, 35, NULL, 'Xã Thường Tín', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(55, 35, NULL, 'Xã Chương Dương', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(56, 35, NULL, 'Xã Hồng Vân', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(57, 35, NULL, 'Xã Phú Xuyên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(58, 35, NULL, 'Xã Phượng Dực', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(59, 35, NULL, 'Xã Chuyên Mỹ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(60, 35, NULL, 'Xã Đại Xuyên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(61, 35, NULL, 'Xã Thanh Oai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(62, 35, NULL, 'Xã Bình Minh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(63, 35, NULL, 'Xã Tam Hưng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(64, 35, NULL, 'Xã Dân Hòa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(65, 35, NULL, 'Xã Vân Đình', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(66, 35, NULL, 'Xã Ứng Thiên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(67, 35, NULL, 'Xã Hòa Xá', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(68, 35, NULL, 'Xã Ứng Hòa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(69, 35, NULL, 'Xã Mỹ Đức', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(70, 35, NULL, 'Xã Hồng Sơn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(71, 35, NULL, 'Xã Phúc Sơn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(72, 35, NULL, 'Xã Hương Sơn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(73, 35, NULL, 'Phường Chương Mỹ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(74, 35, NULL, 'Xã Phú Nghĩa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(75, 35, NULL, 'Xã Xuân Mai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(76, 35, NULL, 'Xã Trần Phú', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(77, 35, NULL, 'Xã Hòa Phú', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(78, 35, NULL, 'Xã Quảng Bị', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(79, 35, NULL, 'Xã Minh Châu', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(80, 35, NULL, 'Xã Quảng Oai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(81, 35, NULL, 'Xã Vật Lại', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(82, 35, NULL, 'Xã Cổ Đô', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(83, 35, NULL, 'Xã Bất Bạt', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(84, 35, NULL, 'Xã Suối Hai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(85, 35, NULL, 'Xã Ba Vì', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(86, 35, NULL, 'Xã Yên Bài', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(87, 35, NULL, 'Phường Sơn Tây', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(88, 35, NULL, 'Phường Tùng Thiện', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(89, 35, NULL, 'Xã Đoài Phương', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(90, 35, NULL, 'Xã Phúc Thọ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(91, 35, NULL, 'Xã Phúc Lộc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(92, 35, NULL, 'Xã Hát Môn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(93, 35, NULL, 'Xã Thạch Thất', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(94, 35, NULL, 'Xã Hạ Bằng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(95, 35, NULL, 'Xã Tây Phương', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(96, 35, NULL, 'Xã Hòa Lạc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(97, 35, NULL, 'Xã Yên Xuân', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(98, 35, NULL, 'Xã Quốc Oai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(99, 35, NULL, 'Xã Hưng Đạo', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(100, 35, NULL, 'Xã Kiều Phú', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(101, 35, NULL, 'Xã Phú Cát', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(102, 35, NULL, 'Xã Hoài Đức', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(103, 35, NULL, 'Xã Dương Hòa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(104, 35, NULL, 'Xã Sơn Đồng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(105, 35, NULL, 'Xã An Khánh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(106, 35, NULL, 'Xã Đan Phượng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(107, 35, NULL, 'Xã Ô Diên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(108, 35, NULL, 'Xã Liên Minh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(109, 35, NULL, 'Xã Gia Lâm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(110, 35, NULL, 'Xã Thuận An', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(111, 35, NULL, 'Xã Bát Tràng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(112, 35, NULL, 'Xã Phù Đổng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(113, 35, NULL, 'Xã Thư Lâm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(114, 35, NULL, 'Xã Đông Anh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(115, 35, NULL, 'Xã Phúc Thịnh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(116, 35, NULL, 'Xã Thiên Lộc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(117, 35, NULL, 'Xã Vĩnh Thanh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(118, 35, NULL, 'Xã Mê Linh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(119, 35, NULL, 'Xã Yên Lãng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(120, 35, NULL, 'Xã Tiến Thắng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(121, 35, NULL, 'Xã Quang Minh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(122, 35, NULL, 'Xã Sóc Sơn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(123, 35, NULL, 'Xã Đa Phúc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(124, 35, NULL, 'Xã Nội Bài', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(125, 35, NULL, 'Xã Trung Giã', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(126, 35, NULL, 'Xã Kim Anh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(127, 36, NULL, 'Phường Thục Phán', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(128, 36, NULL, 'Phường Nùng Trí Cao', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(129, 36, NULL, 'Phường Tân Giang', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(130, 36, NULL, 'Xã Quảng Lâm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(131, 36, NULL, 'Xã Nam Quang', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(132, 36, NULL, 'Xã Lý Bôn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(133, 36, NULL, 'Xã Bảo Lâm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(134, 36, NULL, 'Xã Yên Thổ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(135, 36, NULL, 'Xã Sơn Lộ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(136, 36, NULL, 'Xã Hưng Đạo', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(137, 36, NULL, 'Xã Bảo Lạc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(138, 36, NULL, 'Xã Cốc Pàng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(139, 36, NULL, 'Xã Cô Ba', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(140, 36, NULL, 'Xã Khánh Xuân', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(141, 36, NULL, 'Xã Xuân Trường', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(142, 36, NULL, 'Xã Huy Giáp', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(143, 36, NULL, 'Xã Ca Thành', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(144, 36, NULL, 'Xã Phan Thanh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(145, 36, NULL, 'Xã Thành Công', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(146, 36, NULL, 'Xã Tĩnh Túc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(147, 36, NULL, 'Xã Tam Kim', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(148, 36, NULL, 'Xã Nguyên Bình', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(149, 36, NULL, 'Xã Minh Tâm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(150, 36, NULL, 'Xã Thanh Long', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(151, 36, NULL, 'Xã Cần Yên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(152, 36, NULL, 'Xã Thông Nông', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(153, 36, NULL, 'Xã Trường Hà', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(154, 36, NULL, 'Xã Hà Quảng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(155, 36, NULL, 'Xã Lũng Nặm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(156, 36, NULL, 'Xã Tổng Cọt', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(157, 36, NULL, 'Xã Nam Tuấn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(158, 36, NULL, 'Xã Hòa An', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(159, 36, NULL, 'Xã Bạch Đằng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(160, 36, NULL, 'Xã Nguyễn Huệ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(161, 36, NULL, 'Xã Minh Khai', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(162, 36, NULL, 'Xã Canh Tân', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(163, 36, NULL, 'Xã Kim Đồng', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(164, 36, NULL, 'Xã Thạch An', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(165, 36, NULL, 'Xã Đông Khê', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(166, 36, NULL, 'Xã Đức Long', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(167, 36, NULL, 'Xã Phục Hòa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(168, 36, NULL, 'Xã Bế Văn Đàn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(169, 36, NULL, 'Xã Độc Lập', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(170, 36, NULL, 'Xã Quảng Uyên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(171, 36, NULL, 'Xã Hạnh Phúc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(172, 36, NULL, 'Xã Quang Hán', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(173, 36, NULL, 'Xã Trà Lĩnh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(174, 36, NULL, 'Xã Quang Trung', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(175, 36, NULL, 'Xã Đoài Dương', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(176, 36, NULL, 'Xã Trùng Khánh', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(177, 36, NULL, 'Xã Đàm Thuỷ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(178, 36, NULL, 'Xã Đình Phong', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(179, 36, NULL, 'Xã Lý Quốc', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(180, 36, NULL, 'Xã Hạ Lang', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(181, 36, NULL, 'Xã Vinh Quý', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(182, 36, NULL, 'Xã Quang Long', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(183, 37, NULL, 'Xã Thượng Lâm', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(184, 37, NULL, 'Xã Lâm Bình', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(185, 37, NULL, 'Xã Minh Quang', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(186, 37, NULL, 'Xã Bình An', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(187, 37, NULL, 'Xã Côn Lôn', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(188, 37, NULL, 'Xã Yên Hoa', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(189, 37, NULL, 'Xã Thượng Nông', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(190, 37, NULL, 'Xã Hồng Thái', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(191, 37, NULL, 'Xã Nà Hang', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(192, 37, NULL, 'Xã Tân Mỹ', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(193, 37, NULL, 'Xã Yên Lập', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(194, 37, NULL, 'Xã Tân An', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(195, 37, NULL, 'Xã Chiêm Hoá', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(196, 37, NULL, 'Xã Hoà An', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(197, 37, NULL, 'Xã Kiên Đài', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(198, 37, NULL, 'Xã Tri Phú', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(199, 37, NULL, 'Xã Kim Bình', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(200, 37, NULL, 'Xã Yên Nguyên', NULL, 1, '2025-11-28 13:34:39', '2025-11-28 13:34:39'),
(201, 37, NULL, 'Xã Trung Hà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(202, 37, NULL, 'Xã Yên Phú', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(203, 37, NULL, 'Xã Bạch Xa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(204, 37, NULL, 'Xã Phù Lưu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(205, 37, NULL, 'Xã Hàm Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(206, 37, NULL, 'Xã Bình Xa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(207, 37, NULL, 'Xã Thái Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(208, 37, NULL, 'Xã Thái Hoà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(209, 37, NULL, 'Xã Hùng Đức', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(210, 37, NULL, 'Xã Hùng Lợi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(211, 37, NULL, 'Xã Trung Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(212, 37, NULL, 'Xã Thái Bình', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(213, 37, NULL, 'Xã Tân Long', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(214, 37, NULL, 'Xã Xuân Vân', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(215, 37, NULL, 'Xã Lực Hành', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(216, 37, NULL, 'Xã Yên Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(217, 37, NULL, 'Xã Nhữ Khê', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(218, 37, NULL, 'Xã Kiến Thiết', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(219, 37, NULL, 'Xã Tân Trào', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(220, 37, NULL, 'Xã Minh Thanh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(221, 37, NULL, 'Xã Sơn Dương', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(222, 37, NULL, 'Xã Bình Ca', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(223, 37, NULL, 'Xã Tân Thanh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(224, 37, NULL, 'Xã Sơn Thuỷ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(225, 37, NULL, 'Xã Phú Lương', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(226, 37, NULL, 'Xã Trường Sinh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(227, 37, NULL, 'Xã Hồng Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(228, 37, NULL, 'Xã Đông Thọ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(229, 37, NULL, 'Phường Mỹ Lâm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(230, 37, NULL, 'Phường Minh Xuân', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(231, 37, NULL, 'Phường Nông Tiến', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(232, 37, NULL, 'Phường An Tường', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(233, 37, NULL, 'Phường Bình Thuận', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(234, 37, NULL, 'Xã Lũng Cú', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(235, 37, NULL, 'Xã Đồng Văn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(236, 37, NULL, 'Xã Sà Phìn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(237, 37, NULL, 'Xã Phố Bảng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(238, 37, NULL, 'Xã Lũng Phìn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(239, 37, NULL, 'Xã Sủng Máng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(240, 37, NULL, 'Xã Sơn Vĩ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(241, 37, NULL, 'Xã Mèo Vạc', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(242, 37, NULL, 'Xã Khâu Vai', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(243, 37, NULL, 'Xã Niêm Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(244, 37, NULL, 'Xã Tát Ngà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(245, 37, NULL, 'Xã Thắng Mố', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(246, 37, NULL, 'Xã Bạch Đích', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(247, 37, NULL, 'Xã Yên Minh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(248, 37, NULL, 'Xã Mậu Duệ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(249, 37, NULL, 'Xã Ngọc Long', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(250, 37, NULL, 'Xã Du Già', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(251, 37, NULL, 'Xã Đường Thượng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(252, 37, NULL, 'Xã Lùng Tám', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(253, 37, NULL, 'Xã Cán Tỷ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(254, 37, NULL, 'Xã Nghĩa Thuận', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(255, 37, NULL, 'Xã Quản Bạ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(256, 37, NULL, 'Xã Tùng Vài', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(257, 37, NULL, 'Xã Yên Cường', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(258, 37, NULL, 'Xã Đường Hồng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(259, 37, NULL, 'Xã Bắc Mê', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(260, 37, NULL, 'Xã Giáp Trung', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(261, 37, NULL, 'Xã Minh Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(262, 37, NULL, 'Xã Minh Ngọc', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(263, 37, NULL, 'Xã Ngọc Đường', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(264, 37, NULL, 'Phường Hà Giang 1', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(265, 37, NULL, 'Phường Hà Giang 2', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(266, 37, NULL, 'Xã Lao Chải', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(267, 37, NULL, 'Xã Thanh Thuỷ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(268, 37, NULL, 'Xã Minh Tân', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(269, 37, NULL, 'Xã Thuận Hoà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(270, 37, NULL, 'Xã Tùng Bá', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(271, 37, NULL, 'Xã Phú Linh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(272, 37, NULL, 'Xã Linh Hồ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(273, 37, NULL, 'Xã Bạch Ngọc', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(274, 37, NULL, 'Xã Vị Xuyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(275, 37, NULL, 'Xã Việt Lâm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(276, 37, NULL, 'Xã Cao Bồ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(277, 37, NULL, 'Xã Thượng Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(278, 37, NULL, 'Xã Tân Quang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(279, 37, NULL, 'Xã Đồng Tâm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(280, 37, NULL, 'Xã Liên Hiệp', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(281, 37, NULL, 'Xã Bằng Hành', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(282, 37, NULL, 'Xã Bắc Quang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(283, 37, NULL, 'Xã Hùng An', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(284, 37, NULL, 'Xã Vĩnh Tuy', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(285, 37, NULL, 'Xã Đồng Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(286, 37, NULL, 'Xã Tiên Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(287, 37, NULL, 'Xã Xuân Giang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(288, 37, NULL, 'Xã Bằng Lang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(289, 37, NULL, 'Xã Yên Thành', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(290, 37, NULL, 'Xã Quang Bình', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(291, 37, NULL, 'Xã Tân Trịnh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(292, 37, NULL, 'Xã Tiên Nguyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(293, 37, NULL, 'Xã Thông Nguyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(294, 37, NULL, 'Xã Hồ Thầu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(295, 37, NULL, 'Xã Nậm Dịch', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(296, 37, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(297, 37, NULL, 'Xã Hoàng Su Phì', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(298, 37, NULL, 'Xã Thàng Tín', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(299, 37, NULL, 'Xã Bản Máy', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(300, 37, NULL, 'Xã Pờ Ly Ngài', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(301, 37, NULL, 'Xã Xín Mần', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(302, 37, NULL, 'Xã Pà Vầy Sủ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(303, 37, NULL, 'Xã Nấm Dẩn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(304, 37, NULL, 'Xã Trung Thịnh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(305, 37, NULL, 'Xã Quảng Nguyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(306, 37, NULL, 'Xã Khuôn Lùng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(307, 38, NULL, 'Xã Mường Phăng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(308, 38, NULL, 'Phường Điện Biên Phủ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(309, 38, NULL, 'Phường Mường Thanh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(310, 38, NULL, 'Phường Mường Lay', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(311, 38, NULL, 'Xã Thanh Nưa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(312, 38, NULL, 'Xã Thanh An', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(313, 38, NULL, 'Xã Thanh Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(314, 38, NULL, 'Xã Sam Mứn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(315, 38, NULL, 'Xã Núa Ngam', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(316, 38, NULL, 'Xã Mường Nhà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(317, 38, NULL, 'Xã Tuần Giáo', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(318, 38, NULL, 'Xã Quài Tở', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(319, 38, NULL, 'Xã Mường Mùn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(320, 38, NULL, 'Xã Pú Nhung', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(321, 38, NULL, 'Xã Chiềng Sinh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(322, 38, NULL, 'Xã Tủa Chùa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(323, 38, NULL, 'Xã Sín Chải', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(324, 38, NULL, 'Xã Sính Phình', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(325, 38, NULL, 'Xã Tủa Thàng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(326, 38, NULL, 'Xã Sáng Nhè', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(327, 38, NULL, 'Xã Na Sang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(328, 38, NULL, 'Xã Mường Tùng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(329, 38, NULL, 'Xã Pa Ham', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(330, 38, NULL, 'Xã Nậm Nèn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(331, 38, NULL, 'Xã Mường Pồn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(332, 38, NULL, 'Xã Na Son', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(333, 38, NULL, 'Xã Xa Dung', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(334, 38, NULL, 'Xã Pu Nhi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(335, 38, NULL, 'Xã Mường Luân', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(336, 38, NULL, 'Xã Tìa Dình', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(337, 38, NULL, 'Xã Phình Giàng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(338, 38, NULL, 'Xã Mường Chà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(339, 38, NULL, 'Xã Nà Hỳ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(340, 38, NULL, 'Xã Nà Bủng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(341, 38, NULL, 'Xã Chà Tở', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(342, 38, NULL, 'Xã Si Pa Phìn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(343, 38, NULL, 'Xã Mường Nhé', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(344, 38, NULL, 'Xã Sín Thầu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(345, 38, NULL, 'Xã Mường Toong', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(346, 38, NULL, 'Xã Nậm Kè', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(347, 38, NULL, 'Xã Quảng Lâm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(348, 38, NULL, 'Xã Mường Ảng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(349, 38, NULL, 'Xã Nà Tấu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(350, 38, NULL, 'Xã Búng Lao', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(351, 38, NULL, 'Xã Mường Lạn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(352, 39, NULL, 'Xã Mường Kim', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(353, 39, NULL, 'Xã Khoen On', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(354, 39, NULL, 'Xã Than Uyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(355, 39, NULL, 'Xã Mường Than', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(356, 39, NULL, 'Xã Pắc Ta', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(357, 39, NULL, 'Xã Nậm Sỏ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(358, 39, NULL, 'Xã Tân Uyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(359, 39, NULL, 'Xã Mường Khoa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(360, 39, NULL, 'Xã Bản Bo', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(361, 39, NULL, 'Xã Bình Lư', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(362, 39, NULL, 'Xã Tả Lèng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(363, 39, NULL, 'Xã Khun Há', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(364, 39, NULL, 'Phường Tân Phong', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(365, 39, NULL, 'Phường Đoàn Kết', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(366, 39, NULL, 'Xã Sin Suối Hồ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(367, 39, NULL, 'Xã Phong Thổ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(368, 39, NULL, 'Xã Sì Lở Lầu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(369, 39, NULL, 'Xã Dào San', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(370, 39, NULL, 'Xã Khổng Lào', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(371, 39, NULL, 'Xã Tủa Sín Chải', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(372, 39, NULL, 'Xã Sìn Hồ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(373, 39, NULL, 'Xã Hồng Thu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(374, 39, NULL, 'Xã Nậm Tăm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(375, 39, NULL, 'Xã Pu Sam Cáp', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(376, 39, NULL, 'Xã Nậm Cuổi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(377, 39, NULL, 'Xã Nậm Mạ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(378, 39, NULL, 'Xã Lê Lợi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(379, 39, NULL, 'Xã Nậm Hàng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(380, 39, NULL, 'Xã Mường Mô', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(381, 39, NULL, 'Xã Hua Bum', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(382, 39, NULL, 'Xã Pa Tần', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(383, 39, NULL, 'Xã Bum Nưa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(384, 39, NULL, 'Xã Bum Tở', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(385, 39, NULL, 'Xã Mường Tè', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(386, 39, NULL, 'Xã Thu Lũm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(387, 39, NULL, 'Xã Pa Ủ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(388, 39, NULL, 'Xã Tà Tổng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(389, 39, NULL, 'Xã Mù Cả', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(390, 40, NULL, 'Phường Tô Hiệu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(391, 40, NULL, 'Phường Chiềng An', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(392, 40, NULL, 'Phường Chiềng Cơi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(393, 40, NULL, 'Phường Chiềng Sinh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(394, 40, NULL, 'Phường Mộc Châu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(395, 40, NULL, 'Phường Mộc Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(396, 40, NULL, 'Phường Vân Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(397, 40, NULL, 'Phường Thảo Nguyên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(398, 40, NULL, 'Xã Đoàn Kết', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(399, 40, NULL, 'Xã Lóng Sập', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(400, 40, NULL, 'Xã Chiềng Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(401, 40, NULL, 'Xã Vân Hồ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(402, 40, NULL, 'Xã Song Khủa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(403, 40, NULL, 'Xã Tô Múa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(404, 40, NULL, 'Xã Xuân Nha', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(405, 40, NULL, 'Xã Quỳnh Nhai', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(406, 40, NULL, 'Xã Mường Chiên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(407, 40, NULL, 'Xã Mường Giôn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(408, 40, NULL, 'Xã Mường Sại', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(409, 40, NULL, 'Xã Thuận Châu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(410, 40, NULL, 'Xã Chiềng La', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(411, 40, NULL, 'Xã Nậm Lầu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(412, 40, NULL, 'Xã Muổi Nọi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(413, 40, NULL, 'Xã Mường Khiêng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(414, 40, NULL, 'Xã Co Mạ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(415, 40, NULL, 'Xã Bình Thuận', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(416, 40, NULL, 'Xã Mường É', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(417, 40, NULL, 'Xã Long Hẹ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(418, 40, NULL, 'Xã Mường La', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(419, 40, NULL, 'Xã Chiềng Lao', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(420, 40, NULL, 'Xã Mường Bú', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(421, 40, NULL, 'Xã Chiềng Hoa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(422, 40, NULL, 'Xã Bắc Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(423, 40, NULL, 'Xã Tà Xùa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(424, 40, NULL, 'Xã Tạ Khoa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(425, 40, NULL, 'Xã Xím Vàng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(426, 40, NULL, 'Xã Pắc Ngà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(427, 40, NULL, 'Xã Chiềng Sại', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(428, 40, NULL, 'Xã Phù Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(429, 40, NULL, 'Xã Gia Phù', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(430, 40, NULL, 'Xã Tường Hạ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(431, 40, NULL, 'Xã Mường Cơi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(432, 40, NULL, 'Xã Mường Bang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(433, 40, NULL, 'Xã Tân Phong', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(434, 40, NULL, 'Xã Kim Bon', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(435, 40, NULL, 'Xã Yên Châu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(436, 40, NULL, 'Xã Chiềng Hặc', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(437, 40, NULL, 'Xã Lóng Phiêng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(438, 40, NULL, 'Xã Yên Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(439, 40, NULL, 'Xã Chiềng Mai', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(440, 40, NULL, 'Xã Mai Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(441, 40, NULL, 'Xã Phiêng Pằn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(442, 40, NULL, 'Xã Chiềng Mung', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(443, 40, NULL, 'Xã Phiêng Cằm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(444, 40, NULL, 'Xã Mường Chanh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(445, 40, NULL, 'Xã Tà Hộc', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(446, 40, NULL, 'Xã Chiềng Sung', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(447, 40, NULL, 'Xã Bó Sinh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(448, 40, NULL, 'Xã Chiềng Khương', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(449, 40, NULL, 'Xã Mường Hung', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(450, 40, NULL, 'Xã Chiềng Khoong', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(451, 40, NULL, 'Xã Mường Lầm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(452, 40, NULL, 'Xã Nậm Ty', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(453, 40, NULL, 'Xã Sông Mã', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(454, 40, NULL, 'Xã Huổi Một', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(455, 40, NULL, 'Xã Chiềng Sơ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(456, 40, NULL, 'Xã Sốp Cộp', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(457, 40, NULL, 'Xã Púng Bánh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(458, 40, NULL, 'Xã Tân Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(459, 40, NULL, 'Xã Mường Bám', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(460, 40, NULL, 'Xã Ngọc Chiến', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(461, 40, NULL, 'Xã Suối Tọ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(462, 40, NULL, 'Xã Phiêng Khoài', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(463, 40, NULL, 'Xã Mường Lạn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(464, 40, NULL, 'Xã Mường Lèo', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(465, 41, NULL, 'Xã Khao Mang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(466, 41, NULL, 'Xã Mù Cang Chải', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(467, 41, NULL, 'Xã Púng Luông', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(468, 41, NULL, 'Xã Tú Lệ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(469, 41, NULL, 'Xã Trạm Tấu', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(470, 41, NULL, 'Xã Hạnh Phúc', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(471, 41, NULL, 'Xã Phình Hồ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(472, 41, NULL, 'Phường Nghĩa Lộ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(473, 41, NULL, 'Phường Trung Tâm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(474, 41, NULL, 'Phường Cầu Thia', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(475, 41, NULL, 'Xã Liên Sơn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(476, 41, NULL, 'Xã Gia Hội', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(477, 41, NULL, 'Xã Sơn Lương', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(478, 41, NULL, 'Xã Thượng Bằng La', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(479, 41, NULL, 'Xã Chấn Thịnh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(480, 41, NULL, 'Xã Nghĩa Tâm', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(481, 41, NULL, 'Xã Văn Chấn', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(482, 41, NULL, 'Xã Phong Dụ Hạ', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(483, 41, NULL, 'Xã Châu Quế', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(484, 41, NULL, 'Xã Lâm Giang', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(485, 41, NULL, 'Xã Đông Cuông', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(486, 41, NULL, 'Xã Tân Hợp', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(487, 41, NULL, 'Xã Mậu A', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(488, 41, NULL, 'Xã Xuân Ái', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(489, 41, NULL, 'Xã Mỏ Vàng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(490, 41, NULL, 'Xã Lâm Thượng', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(491, 41, NULL, 'Xã Lục Yên', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(492, 41, NULL, 'Xã Tân Lĩnh', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(493, 41, NULL, 'Xã Khánh Hòa', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(494, 41, NULL, 'Xã Phúc Lợi', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(495, 41, NULL, 'Xã Mường Lai', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(496, 41, NULL, 'Xã Cảm Nhân', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(497, 41, NULL, 'Xã Yên Thành', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(498, 41, NULL, 'Xã Thác Bà', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(499, 41, NULL, 'Xã Yên Bình', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(500, 41, NULL, 'Xã Bảo Ái', NULL, 1, '2025-11-28 13:36:28', '2025-11-28 13:36:28'),
(501, 41, NULL, 'Phường Văn Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(502, 41, NULL, 'Phường Yên Bái', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(503, 41, NULL, 'Phường Nam Cường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(504, 41, NULL, 'Phường Âu Lâu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(505, 41, NULL, 'Xã Trấn Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(506, 41, NULL, 'Xã Hưng Khánh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(507, 41, NULL, 'Xã Lương Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(508, 41, NULL, 'Xã Việt Hồng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(509, 41, NULL, 'Xã Quy Mông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(510, 41, NULL, 'Xã Phong Hải', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(511, 41, NULL, 'Xã Xuân Quang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(512, 41, NULL, 'Xã Bảo Thắng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(513, 41, NULL, 'Xã Tằng Loỏng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(514, 41, NULL, 'Xã Gia Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(515, 41, NULL, 'Xã Cốc San', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(516, 41, NULL, 'Xã Hợp Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(517, 41, NULL, 'Phường Cam Đường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(518, 41, NULL, 'Phường Lào Cai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(519, 41, NULL, 'Xã Mường Hum', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(520, 41, NULL, 'Xã Dền Sáng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(521, 41, NULL, 'Xã Y Tý', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(522, 41, NULL, 'Xã A Mú Sung', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(523, 41, NULL, 'Xã Trịnh Tường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(524, 41, NULL, 'Xã Bản Xèo', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(525, 41, NULL, 'Xã Bát Xát', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(526, 41, NULL, 'Xã Nghĩa Đô', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(527, 41, NULL, 'Xã Thượng Hà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(528, 41, NULL, 'Xã Bảo Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(529, 41, NULL, 'Xã Xuân Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(530, 41, NULL, 'Xã Phúc Khánh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(531, 41, NULL, 'Xã Bảo Hà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(532, 41, NULL, 'Xã Võ Lao', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(533, 41, NULL, 'Xã Khánh Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(534, 41, NULL, 'Xã Văn Bàn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(535, 41, NULL, 'Xã Dương Quỳ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(536, 41, NULL, 'Xã Chiềng Ken', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(537, 41, NULL, 'Xã Minh Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(538, 41, NULL, 'Xã Nậm Chày', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(539, 41, NULL, 'Xã Mường Bo', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(540, 41, NULL, 'Xã Bản Hồ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(541, 41, NULL, 'Xã Tả Phìn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(542, 41, NULL, 'Xã Tả Van', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(543, 41, NULL, 'Phường Sa Pa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(544, 41, NULL, 'Xã Cốc Lầu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(545, 41, NULL, 'Xã Bảo Nhai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(546, 41, NULL, 'Xã Bản Liền', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(547, 41, NULL, 'Xã Bắc Hà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(548, 41, NULL, 'Xã Tả Củ Tỷ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(549, 41, NULL, 'Xã Lùng Phình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(550, 41, NULL, 'Xã Pha Long', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(551, 41, NULL, 'Xã Mường Khương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(552, 41, NULL, 'Xã Bản Lầu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(553, 41, NULL, 'Xã Cao Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(554, 41, NULL, 'Xã Si Ma Cai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(555, 41, NULL, 'Xã Sín Chéng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(556, 41, NULL, 'Xã Lao Chải', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(557, 41, NULL, 'Xã Chế Tạo', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(558, 41, NULL, 'Xã Nậm Có', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(559, 41, NULL, 'Xã Tà Xi Láng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(560, 41, NULL, 'Xã Phong Dụ Thượng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(561, 41, NULL, 'Xã Cát Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(562, 41, NULL, 'Xã Nậm Xé', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(563, 41, NULL, 'Xã Ngũ Chỉ Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(564, 42, NULL, 'Phường Phan Đình Phùng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(565, 42, NULL, 'Phường Linh Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(566, 42, NULL, 'Phường Tích Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(567, 42, NULL, 'Phường Gia Sàng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(568, 42, NULL, 'Phường Quyết Thắng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(569, 42, NULL, 'Phường Quan Triều', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(570, 42, NULL, 'Xã Tân Cương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(571, 42, NULL, 'Xã Đại Phúc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(572, 42, NULL, 'Xã Đại Từ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(573, 42, NULL, 'Xã Đức Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(574, 42, NULL, 'Xã Phú Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(575, 42, NULL, 'Xã La Bằng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(576, 42, NULL, 'Xã Phú Lạc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(577, 42, NULL, 'Xã An Khánh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(578, 42, NULL, 'Xã Quân Chu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(579, 42, NULL, 'Xã Vạn Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(580, 42, NULL, 'Xã Phú Xuyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(581, 42, NULL, 'Phường Phổ Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35');
INSERT INTO `wards` (`id`, `province_id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(582, 42, NULL, 'Phường Vạn Xuân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(583, 42, NULL, 'Phường Trung Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(584, 42, NULL, 'Phường Phúc Thuận', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(585, 42, NULL, 'Xã Thành Công', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(586, 42, NULL, 'Xã Phú Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(587, 42, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(588, 42, NULL, 'Xã Điềm Thụy', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(589, 42, NULL, 'Xã Kha Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(590, 42, NULL, 'Xã Tân Khánh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(591, 42, NULL, 'Xã Đồng Hỷ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(592, 42, NULL, 'Xã Quang Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(593, 42, NULL, 'Xã Trại Cau', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(594, 42, NULL, 'Xã Nam Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(595, 42, NULL, 'Xã Văn Hán', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(596, 42, NULL, 'Xã Văn Lăng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(597, 42, NULL, 'Phường Sông Công', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(598, 42, NULL, 'Phường Bá Xuyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(599, 42, NULL, 'Phường Bách Quang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(600, 42, NULL, 'Xã Phú Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(601, 42, NULL, 'Xã Vô Tranh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(602, 42, NULL, 'Xã Yên Trạch', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(603, 42, NULL, 'Xã Hợp Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(604, 42, NULL, 'Xã Định Hóa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(605, 42, NULL, 'Xã Bình Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(606, 42, NULL, 'Xã Trung Hội', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(607, 42, NULL, 'Xã Phượng Tiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(608, 42, NULL, 'Xã Phú Đình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(609, 42, NULL, 'Xã Bình Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(610, 42, NULL, 'Xã Kim Phượng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(611, 42, NULL, 'Xã Lam Vỹ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(612, 42, NULL, 'Xã Võ Nhai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(613, 42, NULL, 'Xã Dân Tiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(614, 42, NULL, 'Xã Nghinh Tường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(615, 42, NULL, 'Xã Thần Sa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(616, 42, NULL, 'Xã La Hiên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(617, 42, NULL, 'Xã Tràng Xá', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(618, 42, NULL, 'Xã Bằng Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(619, 42, NULL, 'Xã Nghiên Loan', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(620, 42, NULL, 'Xã Cao Minh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(621, 42, NULL, 'Xã Ba Bể', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(622, 42, NULL, 'Xã Chợ Rã', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(623, 42, NULL, 'Xã Phúc Lộc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(624, 42, NULL, 'Xã Thượng Minh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(625, 42, NULL, 'Xã Đồng Phúc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(626, 42, NULL, 'Xã Yên Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(627, 42, NULL, 'Xã Bằng Vân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(628, 42, NULL, 'Xã Ngân Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(629, 42, NULL, 'Xã Nà Phặc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(630, 42, NULL, 'Xã Hiệp Lực', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(631, 42, NULL, 'Xã Nam Cường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(632, 42, NULL, 'Xã Quảng Bạch', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(633, 42, NULL, 'Xã Yên Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(634, 42, NULL, 'Xã Chợ Đồn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(635, 42, NULL, 'Xã Yên Phong', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(636, 42, NULL, 'Xã Nghĩa Tá', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(637, 42, NULL, 'Xã Phủ Thông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(638, 42, NULL, 'Xã Cẩm Giàng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(639, 42, NULL, 'Xã Vĩnh Thông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(640, 42, NULL, 'Xã Bạch Thông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(641, 42, NULL, 'Xã Phong Quang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(642, 42, NULL, 'Phường Đức Xuân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(643, 42, NULL, 'Phường Bắc Kạn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(644, 42, NULL, 'Xã Văn Lang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(645, 42, NULL, 'Xã Cường Lợi', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(646, 42, NULL, 'Xã Na Rì', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(647, 42, NULL, 'Xã Trần Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(648, 42, NULL, 'Xã Côn Minh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(649, 42, NULL, 'Xã Xuân Dương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(650, 42, NULL, 'Xã Tân Kỳ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(651, 42, NULL, 'Xã Thanh Mai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(652, 42, NULL, 'Xã Thanh Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(653, 42, NULL, 'Xã Chợ Mới', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(654, 42, NULL, 'Xã Sảng Mộc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(655, 42, NULL, 'Xã Thượng Quan', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(656, 43, NULL, 'Xã Thất Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(657, 43, NULL, 'Xã Đoàn Kết', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(658, 43, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(659, 43, NULL, 'Xã Tràng Định', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(660, 43, NULL, 'Xã Quốc Khánh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(661, 43, NULL, 'Xã Kháng Chiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(662, 43, NULL, 'Xã Quốc Việt', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(663, 43, NULL, 'Xã Bình Gia', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(664, 43, NULL, 'Xã Tân Văn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(665, 43, NULL, 'Xã Hồng Phong', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(666, 43, NULL, 'Xã Hoa Thám', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(667, 43, NULL, 'Xã Quý Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(668, 43, NULL, 'Xã Thiện Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(669, 43, NULL, 'Xã Thiện Thuật', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(670, 43, NULL, 'Xã Thiện Long', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(671, 43, NULL, 'Xã Bắc Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(672, 43, NULL, 'Xã Hưng Vũ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(673, 43, NULL, 'Xã Vũ Lăng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(674, 43, NULL, 'Xã Nhất Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(675, 43, NULL, 'Xã Vũ Lễ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(676, 43, NULL, 'Xã Tân Tri', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(677, 43, NULL, 'Xã Văn Quan', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(678, 43, NULL, 'Xã Điềm He', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(679, 43, NULL, 'Xã Tri Lễ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(680, 43, NULL, 'Xã Yên Phúc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(681, 43, NULL, 'Xã Tân Đoàn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(682, 43, NULL, 'Xã Khánh Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(683, 43, NULL, 'Xã Na Sầm', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(684, 43, NULL, 'Xã Văn Lãng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(685, 43, NULL, 'Xã Hội Hoan', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(686, 43, NULL, 'Xã Thụy Hùng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(687, 43, NULL, 'Xã Tân Thanh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(688, 43, NULL, 'Xã Lộc Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(689, 43, NULL, 'Xã Mẫu Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(690, 43, NULL, 'Xã Na Dương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(691, 43, NULL, 'Xã Lợi Bác', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(692, 43, NULL, 'Xã Thống Nhất', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(693, 43, NULL, 'Xã Xuân Dương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(694, 43, NULL, 'Xã Khuất Xá', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(695, 43, NULL, 'Xã Đình Lập', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(696, 43, NULL, 'Xã Châu Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(697, 43, NULL, 'Xã Kiên Mộc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(698, 43, NULL, 'Xã Thái Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(699, 43, NULL, 'Xã Hữu Lũng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(700, 43, NULL, 'Xã Tuấn Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(701, 43, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(702, 43, NULL, 'Xã Vân Nham', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(703, 43, NULL, 'Xã Thiện Tân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(704, 43, NULL, 'Xã Yên Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(705, 43, NULL, 'Xã Hữu Liên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(706, 43, NULL, 'Xã Cai Kinh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(707, 43, NULL, 'Xã Chi Lăng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(708, 43, NULL, 'Xã Nhân Lý', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(709, 43, NULL, 'Xã Chiến Thắng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(710, 43, NULL, 'Xã Quan Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(711, 43, NULL, 'Xã Bằng Mạc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(712, 43, NULL, 'Xã Vạn Linh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(713, 43, NULL, 'Xã Đồng Đăng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(714, 43, NULL, 'Xã Cao Lộc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(715, 43, NULL, 'Xã Công Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(716, 43, NULL, 'Xã Ba Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(717, 43, NULL, 'Phường Tam Thanh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(718, 43, NULL, 'Phường Lương Văn Tri', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(719, 43, NULL, 'Phường Hoàng Văn Thụ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(720, 43, NULL, 'Phường Đông Kinh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(721, 44, NULL, 'Phường An Sinh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(722, 44, NULL, 'Phường Đông Triều', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(723, 44, NULL, 'Phường Bình Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(724, 44, NULL, 'Phường Mạo Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(725, 44, NULL, 'Phường Hoàng Quế', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(726, 44, NULL, 'Phường Yên Tử', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(727, 44, NULL, 'Phường Vàng Danh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(728, 44, NULL, 'Phường Uông Bí', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(729, 44, NULL, 'Phường Đông Mai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(730, 44, NULL, 'Phường Hiệp Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(731, 44, NULL, 'Phường Quảng Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(732, 44, NULL, 'Phường Hà An', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(733, 44, NULL, 'Phường Phong Cốc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(734, 44, NULL, 'Phường Liên Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(735, 44, NULL, 'Phường Tuần Châu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(736, 44, NULL, 'Phường Việt Hưng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(737, 44, NULL, 'Phường Bãi Cháy', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(738, 44, NULL, 'Phường Hà Tu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(739, 44, NULL, 'Phường Hà Lầm', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(740, 44, NULL, 'Phường Cao Xanh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(741, 44, NULL, 'Phường Hồng Gai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(742, 44, NULL, 'Phường Hạ Long', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(743, 44, NULL, 'Phường Hoành Bồ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(744, 44, NULL, 'Xã Quảng La', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(745, 44, NULL, 'Xã Thống Nhất', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(746, 44, NULL, 'Phường Mông Dương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(747, 44, NULL, 'Phường Quang Hanh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(748, 44, NULL, 'Phường Cẩm Phả', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(749, 44, NULL, 'Phường Cửa Ông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(750, 44, NULL, 'Xã Hải Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(751, 44, NULL, 'Xã Tiên Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(752, 44, NULL, 'Xã Điền Xá', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(753, 44, NULL, 'Xã Đông Ngũ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(754, 44, NULL, 'Xã Hải Lạng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(755, 44, NULL, 'Xã Lương Minh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(756, 44, NULL, 'Xã Kỳ Thượng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(757, 44, NULL, 'Xã Ba Chẽ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(758, 44, NULL, 'Xã Quảng Tân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(759, 44, NULL, 'Xã Đầm Hà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(760, 44, NULL, 'Xã Quảng Hà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(761, 44, NULL, 'Xã Đường Hoa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(762, 44, NULL, 'Xã Quảng Đức', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(763, 44, NULL, 'Xã Hoành Mô', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(764, 44, NULL, 'Xã Lục Hồn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(765, 44, NULL, 'Xã Bình Liêu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(766, 44, NULL, 'Xã Hải Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(767, 44, NULL, 'Xã Hải Ninh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(768, 44, NULL, 'Xã Vĩnh Thực', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(769, 44, NULL, 'Phường Móng Cái 1', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(770, 44, NULL, 'Phường Móng Cái 2', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(771, 44, NULL, 'Phường Móng Cái 3', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(772, 44, NULL, 'Đặc khu Vân Đồn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(773, 44, NULL, 'Đặc khu Cô Tô', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(774, 44, NULL, 'Xã Cái Chiên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(775, 45, NULL, 'Xã Đại Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(776, 45, NULL, 'Xã Sơn Động', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(777, 45, NULL, 'Xã Tây Yên Tử', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(778, 45, NULL, 'Xã Dương Hưu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(779, 45, NULL, 'Xã Yên Định', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(780, 45, NULL, 'Xã An Lạc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(781, 45, NULL, 'Xã Vân Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(782, 45, NULL, 'Xã Biển Động', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(783, 45, NULL, 'Xã Lục Ngạn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(784, 45, NULL, 'Xã Đèo Gia', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(785, 45, NULL, 'Xã Sơn Hải', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(786, 45, NULL, 'Xã Tân Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(787, 45, NULL, 'Xã Biên Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(788, 45, NULL, 'Xã Sa Lý', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(789, 45, NULL, 'Xã Nam Dương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(790, 45, NULL, 'Xã Kiên Lao', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(791, 45, NULL, 'Phường Chũ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(792, 45, NULL, 'Phường Phượng Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(793, 45, NULL, 'Xã Lục Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(794, 45, NULL, 'Xã Trường Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(795, 45, NULL, 'Xã Cẩm Lý', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(796, 45, NULL, 'Xã Đông Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(797, 45, NULL, 'Xã Nghĩa Phương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(798, 45, NULL, 'Xã Lục Nam', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(799, 45, NULL, 'Xã Bắc Lũng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(800, 45, NULL, 'Xã Bảo Đài', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(801, 45, NULL, 'Xã Lạng Giang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(802, 45, NULL, 'Xã Mỹ Thái', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(803, 45, NULL, 'Xã Kép', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(804, 45, NULL, 'Xã Tân Dĩnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(805, 45, NULL, 'Xã Tiên Lục', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(806, 45, NULL, 'Xã Yên Thế', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(807, 45, NULL, 'Xã Bố Hạ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(808, 45, NULL, 'Xã Đồng Kỳ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(809, 45, NULL, 'Xã Xuân Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(810, 45, NULL, 'Xã Tam Tiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(811, 45, NULL, 'Xã Tân Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(812, 45, NULL, 'Xã Ngọc Thiện', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(813, 45, NULL, 'Xã Nhã Nam', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(814, 45, NULL, 'Xã Phúc Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(815, 45, NULL, 'Xã Quang Trung', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(816, 45, NULL, 'Xã Hợp Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(817, 45, NULL, 'Xã Hiệp Hoà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(818, 45, NULL, 'Xã Hoàng Vân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(819, 45, NULL, 'Xã Xuân Cẩm', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(820, 45, NULL, 'Phường Tự Lạn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(821, 45, NULL, 'Phường Việt Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(822, 45, NULL, 'Phường Nếnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(823, 45, NULL, 'Phường Vân Hà', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(824, 45, NULL, 'Xã Đồng Việt', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(825, 45, NULL, 'Phường Bắc Giang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(826, 45, NULL, 'Phường Đa Mai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(827, 45, NULL, 'Phường Tiền Phong', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(828, 45, NULL, 'Phường Tân An', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(829, 45, NULL, 'Phường Yên Dũng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(830, 45, NULL, 'Phường Tân Tiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(831, 45, NULL, 'Phường Cảnh Thụy', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(832, 45, NULL, 'Phường Kinh Bắc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(833, 45, NULL, 'Phường Võ Cường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(834, 45, NULL, 'Phường Vũ Ninh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(835, 45, NULL, 'Phường Hạp Lĩnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(836, 45, NULL, 'Phường Nam Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(837, 45, NULL, 'Phường Từ Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(838, 45, NULL, 'Phường Tam Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(839, 45, NULL, 'Phường Đồng Nguyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(840, 45, NULL, 'Phường Phù Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(841, 45, NULL, 'Phường Thuận Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(842, 45, NULL, 'Phường Mão Điền', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(843, 45, NULL, 'Phường Trạm Lộ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(844, 45, NULL, 'Phường Trí Quả', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(845, 45, NULL, 'Phường Song Liễu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(846, 45, NULL, 'Phường Ninh Xá', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(847, 45, NULL, 'Phường Quế Võ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(848, 45, NULL, 'Phường Phương Liễu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(849, 45, NULL, 'Phường Nhân Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(850, 45, NULL, 'Phường Đào Viên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(851, 45, NULL, 'Phường Bồng Lai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(852, 45, NULL, 'Xã Chi Lăng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(853, 45, NULL, 'Xã Phù Lãng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(854, 45, NULL, 'Xã Yên Phong', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(855, 45, NULL, 'Xã Văn Môn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(856, 45, NULL, 'Xã Tam Giang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(857, 45, NULL, 'Xã Yên Trung', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(858, 45, NULL, 'Xã Tam Đa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(859, 45, NULL, 'Xã Tiên Du', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(860, 45, NULL, 'Xã Liên Bão', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(861, 45, NULL, 'Xã Tân Chi', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(862, 45, NULL, 'Xã Đại Đồng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(863, 45, NULL, 'Xã Phật Tích', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(864, 45, NULL, 'Xã Gia Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(865, 45, NULL, 'Xã Nhân Thắng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(866, 45, NULL, 'Xã Đại Lai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(867, 45, NULL, 'Xã Cao Đức', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(868, 45, NULL, 'Xã Đông Cứu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(869, 45, NULL, 'Xã Lương Tài', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(870, 45, NULL, 'Xã Lâm Thao', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(871, 45, NULL, 'Xã Trung Chính', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(872, 45, NULL, 'Xã Trung Kênh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(873, 45, NULL, 'Xã Tuấn Đạo', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(874, 46, NULL, 'Phường Việt Trì', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(875, 46, NULL, 'Phường Nông Trang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(876, 46, NULL, 'Phường Thanh Miếu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(877, 46, NULL, 'Phường Vân Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(878, 46, NULL, 'Xã Hy Cương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(879, 46, NULL, 'Xã Lâm Thao', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(880, 46, NULL, 'Xã Xuân Lũng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(881, 46, NULL, 'Xã Phùng Nguyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(882, 46, NULL, 'Xã Bản Nguyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(883, 46, NULL, 'Phường Phong Châu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(884, 46, NULL, 'Phường Phú Thọ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(885, 46, NULL, 'Phường Âu Cơ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(886, 46, NULL, 'Xã Phù Ninh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(887, 46, NULL, 'Xã Dân Chủ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(888, 46, NULL, 'Xã Phú Mỹ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(889, 46, NULL, 'Xã Trạm Thản', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(890, 46, NULL, 'Xã Bình Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(891, 46, NULL, 'Xã Thanh Ba', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(892, 46, NULL, 'Xã Quảng Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(893, 46, NULL, 'Xã Hoàng Cương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(894, 46, NULL, 'Xã Đông Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(895, 46, NULL, 'Xã Chí Tiên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(896, 46, NULL, 'Xã Liên Minh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(897, 46, NULL, 'Xã Đoan Hùng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(898, 46, NULL, 'Xã Tây Cốc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(899, 46, NULL, 'Xã Chân Mộng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(900, 46, NULL, 'Xã Chí Đám', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(901, 46, NULL, 'Xã Bằng Luân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(902, 46, NULL, 'Xã Hạ Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(903, 46, NULL, 'Xã Đan Thượng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(904, 46, NULL, 'Xã Yên Kỳ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(905, 46, NULL, 'Xã Vĩnh Chân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(906, 46, NULL, 'Xã Văn Lang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(907, 46, NULL, 'Xã Hiền Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(908, 46, NULL, 'Xã Cẩm Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(909, 46, NULL, 'Xã Phú Khê', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(910, 46, NULL, 'Xã Hùng Việt', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(911, 46, NULL, 'Xã Đồng Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(912, 46, NULL, 'Xã Tiên Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(913, 46, NULL, 'Xã Vân Bán', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(914, 46, NULL, 'Xã Tam Nông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(915, 46, NULL, 'Xã Thọ Văn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(916, 46, NULL, 'Xã Vạn Xuân', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(917, 46, NULL, 'Xã Hiền Quan', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(918, 46, NULL, 'Xã Thanh Thuỷ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(919, 46, NULL, 'Xã Đào Xá', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(920, 46, NULL, 'Xã Tu Vũ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(921, 46, NULL, 'Xã Thanh Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(922, 46, NULL, 'Xã Võ Miếu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(923, 46, NULL, 'Xã Văn Miếu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(924, 46, NULL, 'Xã Cự Đồng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(925, 46, NULL, 'Xã Hương Cần', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(926, 46, NULL, 'Xã Yên Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(927, 46, NULL, 'Xã Khả Cửu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(928, 46, NULL, 'Xã Tân Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(929, 46, NULL, 'Xã Minh Đài', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(930, 46, NULL, 'Xã Lai Đồng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(931, 46, NULL, 'Xã Thu Cúc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(932, 46, NULL, 'Xã Xuân Đài', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(933, 46, NULL, 'Xã Long Cốc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(934, 46, NULL, 'Xã Yên Lập', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(935, 46, NULL, 'Xã Thượng Long', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(936, 46, NULL, 'Xã Sơn Lương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(937, 46, NULL, 'Xã Xuân Viên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(938, 46, NULL, 'Xã Minh Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(939, 46, NULL, 'Xã Trung Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(940, 46, NULL, 'Xã Tam Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(941, 46, NULL, 'Xã Sông Lô', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(942, 46, NULL, 'Xã Hải Lựu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(943, 46, NULL, 'Xã Yên Lãng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(944, 46, NULL, 'Xã Lập Thạch', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(945, 46, NULL, 'Xã Tiên Lữ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(946, 46, NULL, 'Xã Thái Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(947, 46, NULL, 'Xã Liên Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(948, 46, NULL, 'Xã Hợp Lý', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(949, 46, NULL, 'Xã Sơn Đông', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(950, 46, NULL, 'Xã Tam Đảo', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(951, 46, NULL, 'Xã Đại Đình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(952, 46, NULL, 'Xã Đạo Trù', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(953, 46, NULL, 'Xã Tam Dương', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(954, 46, NULL, 'Xã Hội Thịnh', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(955, 46, NULL, 'Xã Hoàng An', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(956, 46, NULL, 'Xã Tam Dương Bắc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(957, 46, NULL, 'Xã Vĩnh Tường', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(958, 46, NULL, 'Xã Thổ Tang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(959, 46, NULL, 'Xã Vĩnh Hưng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(960, 46, NULL, 'Xã Vĩnh An', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(961, 46, NULL, 'Xã Vĩnh Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(962, 46, NULL, 'Xã Vĩnh Thành', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(963, 46, NULL, 'Xã Yên Lạc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(964, 46, NULL, 'Xã Tề Lỗ', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(965, 46, NULL, 'Xã Liên Châu', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(966, 46, NULL, 'Xã Tam Hồng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(967, 46, NULL, 'Xã Nguyệt Đức', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(968, 46, NULL, 'Xã Bình Nguyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(969, 46, NULL, 'Xã Xuân Lãng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(970, 46, NULL, 'Xã Bình Xuyên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(971, 46, NULL, 'Xã Bình Tuyền', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(972, 46, NULL, 'Phường Vĩnh Phúc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(973, 46, NULL, 'Phường Vĩnh Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(974, 46, NULL, 'Phường Phúc Yên', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(975, 46, NULL, 'Phường Xuân Hòa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(976, 46, NULL, 'Xã Cao Phong', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(977, 46, NULL, 'Xã Mường Thàng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(978, 46, NULL, 'Xã Thung Nai', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(979, 46, NULL, 'Xã Đà Bắc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(980, 46, NULL, 'Xã Cao Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(981, 46, NULL, 'Xã Đức Nhàn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(982, 46, NULL, 'Xã Quy Đức', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(983, 46, NULL, 'Xã Tân Pheo', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(984, 46, NULL, 'Xã Tiền Phong', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(985, 46, NULL, 'Xã Kim Bôi', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(986, 46, NULL, 'Xã Mường Động', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(987, 46, NULL, 'Xã Dũng Tiến', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(988, 46, NULL, 'Xã Hợp Kim', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(989, 46, NULL, 'Xã Nật Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(990, 46, NULL, 'Xã Lạc Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(991, 46, NULL, 'Xã Mường Vang', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(992, 46, NULL, 'Xã Đại Đồng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(993, 46, NULL, 'Xã Ngọc Sơn', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(994, 46, NULL, 'Xã Nhân Nghĩa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(995, 46, NULL, 'Xã Quyết Thắng', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(996, 46, NULL, 'Xã Thượng Cốc', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(997, 46, NULL, 'Xã Yên Phú', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(998, 46, NULL, 'Xã Lạc Thủy', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(999, 46, NULL, 'Xã An Bình', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(1000, 46, NULL, 'Xã An Nghĩa', NULL, 1, '2025-11-28 13:37:35', '2025-11-28 13:37:35'),
(1001, 46, NULL, 'Xã Lương Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1002, 46, NULL, 'Xã Cao Dương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1003, 46, NULL, 'Xã Liên Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1004, 46, NULL, 'Xã Mai Châu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1005, 46, NULL, 'Xã Bao La', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1006, 46, NULL, 'Xã Mai Hạ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1007, 46, NULL, 'Xã Pà Cò', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1008, 46, NULL, 'Xã Tân Mai', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1009, 46, NULL, 'Xã Tân Lạc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1010, 46, NULL, 'Xã Mường Bi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1011, 46, NULL, 'Xã Mường Hoa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1012, 46, NULL, 'Xã Toàn Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1013, 46, NULL, 'Xã Vân Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1014, 46, NULL, 'Xã Yên Thủy', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1015, 46, NULL, 'Xã Lạc Lương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1016, 46, NULL, 'Xã Yên Trị', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1017, 46, NULL, 'Xã Thịnh Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1018, 46, NULL, 'Phường Hòa Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1019, 46, NULL, 'Phường Kỳ Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1020, 46, NULL, 'Phường Tân Hòa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1021, 46, NULL, 'Phường Thống Nhất', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1022, 47, NULL, 'Phường Thuỷ Nguyên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1023, 47, NULL, 'Phường Thiên Hương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1024, 47, NULL, 'Phường Hoà Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1025, 47, NULL, 'Phường Nam Triệu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1026, 47, NULL, 'Phường Bạch Đằng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1027, 47, NULL, 'Phường Lưu Kiếm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1028, 47, NULL, 'Phường Lê Ích Mộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1029, 47, NULL, 'Phường Hồng Bàng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1030, 47, NULL, 'Phường Hồng An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1031, 47, NULL, 'Phường Ngô Quyền', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1032, 47, NULL, 'Phường Gia Viên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1033, 47, NULL, 'Phường Lê Chân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1034, 47, NULL, 'Phường An Biên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1035, 47, NULL, 'Phường Hải An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1036, 47, NULL, 'Phường Đông Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1037, 47, NULL, 'Phường Kiến An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1038, 47, NULL, 'Phường Phù Liễn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1039, 47, NULL, 'Phường Nam Đồ Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1040, 47, NULL, 'Phường Đồ Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1041, 47, NULL, 'Phường Hưng Đạo', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1042, 47, NULL, 'Phường Dương Kinh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1043, 47, NULL, 'Phường An Dương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1044, 47, NULL, 'Phường An Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1045, 47, NULL, 'Phường An Phong', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1046, 47, NULL, 'Xã An Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1047, 47, NULL, 'Xã An Khánh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1048, 47, NULL, 'Xã An Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1049, 47, NULL, 'Xã An Trường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1050, 47, NULL, 'Xã An Lão', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1051, 47, NULL, 'Xã Kiến Thụy', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1052, 47, NULL, 'Xã Kiến Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1053, 47, NULL, 'Xã Kiến Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1054, 47, NULL, 'Xã Kiến Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1055, 47, NULL, 'Xã Nghi Dương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1056, 47, NULL, 'Xã Quyết Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1057, 47, NULL, 'Xã Tiên Lãng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1058, 47, NULL, 'Xã Tân Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1059, 47, NULL, 'Xã Tiên Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1060, 47, NULL, 'Xã Chấn Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1061, 47, NULL, 'Xã Hùng Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1062, 47, NULL, 'Xã Vĩnh Bảo', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1063, 47, NULL, 'Xã Nguyễn Bỉnh Khiêm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1064, 47, NULL, 'Xã Vĩnh Am', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1065, 47, NULL, 'Xã Vĩnh Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1066, 47, NULL, 'Xã Vĩnh Hòa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1067, 47, NULL, 'Xã Vĩnh Thịnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1068, 47, NULL, 'Xã Vĩnh Thuận', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1069, 47, NULL, 'Xã Việt Khê', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1070, 47, NULL, 'Đặc khu Cát Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1071, 47, NULL, 'Đặc khu Bạch Long Vĩ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1072, 47, NULL, 'Phường Hải Dương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1073, 47, NULL, 'Phường Lê Thanh Nghị', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1074, 47, NULL, 'Phường Việt Hòa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1075, 47, NULL, 'Phường Thành Đông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1076, 47, NULL, 'Phường Nam Đồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1077, 47, NULL, 'Phường Tân Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1078, 47, NULL, 'Phường Thạch Khôi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1079, 47, NULL, 'Phường Tứ Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1080, 47, NULL, 'Phường Ái Quốc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1081, 47, NULL, 'Phường Chu Văn An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1082, 47, NULL, 'Phường Chí Linh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1083, 47, NULL, 'Phường Trần Hưng Đạo', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1084, 47, NULL, 'Phường Nguyễn Trãi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1085, 47, NULL, 'Phường Trần Nhân Tông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1086, 47, NULL, 'Phường Lê Đại Hành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1087, 47, NULL, 'Phường Kinh Môn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1088, 47, NULL, 'Phường Nguyễn Đại Năng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1089, 47, NULL, 'Phường Trần Liễu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1090, 47, NULL, 'Phường Bắc An Phụ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1091, 47, NULL, 'Phường Phạm Sư Mạnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1092, 47, NULL, 'Phường Nhị Chiểu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1093, 47, NULL, 'Xã Nam An Phụ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1094, 47, NULL, 'Xã Nam Sách', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1095, 47, NULL, 'Xã Thái Tân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1096, 47, NULL, 'Xã Hợp Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1097, 47, NULL, 'Xã Trần Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1098, 47, NULL, 'Xã An Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1099, 47, NULL, 'Xã Thanh Hà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1100, 47, NULL, 'Xã Hà Tây', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1101, 47, NULL, 'Xã Hà Bắc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1102, 47, NULL, 'Xã Hà Nam', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1103, 47, NULL, 'Xã Hà Đông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1104, 47, NULL, 'Xã Cẩm Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1105, 47, NULL, 'Xã Tuệ Tĩnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1106, 47, NULL, 'Xã Mao Điền', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1107, 47, NULL, 'Xã Cẩm Giàng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1108, 47, NULL, 'Xã Kẻ Sặt', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1109, 47, NULL, 'Xã Bình Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1110, 47, NULL, 'Xã Đường An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1111, 47, NULL, 'Xã Thượng Hồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1112, 47, NULL, 'Xã Gia Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1113, 47, NULL, 'Xã Yết Kiêu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1114, 47, NULL, 'Xã Gia Phúc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1115, 47, NULL, 'Xã Trường Tân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1116, 47, NULL, 'Xã Tứ Kỳ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1117, 47, NULL, 'Xã Tân Kỳ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1118, 47, NULL, 'Xã Đại Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1119, 47, NULL, 'Xã Chí Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1120, 47, NULL, 'Xã Lạc Phượng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1121, 47, NULL, 'Xã Nguyên Giáp', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1122, 47, NULL, 'Xã Ninh Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1123, 47, NULL, 'Xã Vĩnh Lại', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1124, 47, NULL, 'Xã Khúc Thừa Dụ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1125, 47, NULL, 'Xã Tân An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1126, 47, NULL, 'Xã Hồng Châu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1127, 47, NULL, 'Xã Thanh Miện', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1128, 47, NULL, 'Xã Bắc Thanh Miện', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1129, 47, NULL, 'Xã Hải Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1130, 47, NULL, 'Xã Nguyễn Lương Bằng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1131, 47, NULL, 'Xã Nam Thanh Miện', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1132, 47, NULL, 'Xã Phú Thái', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1133, 47, NULL, 'Xã Lai Khê', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1134, 47, NULL, 'Xã An Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1135, 47, NULL, 'Xã Kim Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1136, 48, NULL, 'Phường Phố Hiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1137, 48, NULL, 'Phường Sơn Nam', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1138, 48, NULL, 'Phường Hồng Châu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1139, 48, NULL, 'Phường Mỹ Hào', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1140, 48, NULL, 'Phường Đường Hào', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1141, 48, NULL, 'Phường Thượng Hồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1142, 48, NULL, 'Xã Tân Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1143, 48, NULL, 'Xã Hoàng Hoa Thám', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1144, 48, NULL, 'Xã Tiên Lữ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1145, 48, NULL, 'Xã Tiên Hoa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1146, 48, NULL, 'Xã Quang Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1147, 48, NULL, 'Xã Đoàn Đào', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1148, 48, NULL, 'Xã Tiên Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1149, 48, NULL, 'Xã Tống Trân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1150, 48, NULL, 'Xã Lương Bằng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1151, 48, NULL, 'Xã Nghĩa Dân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1152, 48, NULL, 'Xã Hiệp Cường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1153, 48, NULL, 'Xã Đức Hợp', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1154, 48, NULL, 'Xã Ân Thi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1155, 48, NULL, 'Xã Xuân Trúc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1156, 48, NULL, 'Xã Phạm Ngũ Lão', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44');
INSERT INTO `wards` (`id`, `province_id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1157, 48, NULL, 'Xã Nguyễn Trãi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1158, 48, NULL, 'Xã Hồng Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1159, 48, NULL, 'Xã Khoái Châu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1160, 48, NULL, 'Xã Triệu Việt Vương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1161, 48, NULL, 'Xã Việt Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1162, 48, NULL, 'Xã Chí Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1163, 48, NULL, 'Xã Châu Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1164, 48, NULL, 'Xã Yên Mỹ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1165, 48, NULL, 'Xã Việt Yên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1166, 48, NULL, 'Xã Hoàn Long', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1167, 48, NULL, 'Xã Nguyễn Văn Linh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1168, 48, NULL, 'Xã Như Quỳnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1169, 48, NULL, 'Xã Lạc Đạo', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1170, 48, NULL, 'Xã Đại Đồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1171, 48, NULL, 'Xã Nghĩa Trụ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1172, 48, NULL, 'Xã Phụng Công', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1173, 48, NULL, 'Xã Văn Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1174, 48, NULL, 'Xã Mễ Sở', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1175, 48, NULL, 'Phường Thái Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1176, 48, NULL, 'Phường Trần Lãm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1177, 48, NULL, 'Phường Trần Hưng Đạo', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1178, 48, NULL, 'Phường Trà Lý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1179, 48, NULL, 'Phường Vũ Phúc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1180, 48, NULL, 'Xã Thái Thụy', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1181, 48, NULL, 'Xã Đông Thụy Anh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1182, 48, NULL, 'Xã Bắc Thụy Anh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1183, 48, NULL, 'Xã Thụy Anh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1184, 48, NULL, 'Xã Nam Thụy Anh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1185, 48, NULL, 'Xã Bắc Thái Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1186, 48, NULL, 'Xã Thái Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1187, 48, NULL, 'Xã Đông Thái Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1188, 48, NULL, 'Xã Nam Thái Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1189, 48, NULL, 'Xã Tây Thái Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1190, 48, NULL, 'Xã Tây Thụy Anh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1191, 48, NULL, 'Xã Tiền Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1192, 48, NULL, 'Xã Tây Tiền Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1193, 48, NULL, 'Xã Ái Quốc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1194, 48, NULL, 'Xã Đồng Châu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1195, 48, NULL, 'Xã Đông Tiền Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1196, 48, NULL, 'Xã Nam Cường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1197, 48, NULL, 'Xã Hưng Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1198, 48, NULL, 'Xã Nam Tiền Hải', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1199, 48, NULL, 'Xã Quỳnh Phụ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1200, 48, NULL, 'Xã Minh Thọ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1201, 48, NULL, 'Xã Nguyễn Du', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1202, 48, NULL, 'Xã Quỳnh An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1203, 48, NULL, 'Xã Ngọc Lâm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1204, 48, NULL, 'Xã Đồng Bằng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1205, 48, NULL, 'Xã A Sào', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1206, 48, NULL, 'Xã Phụ Dực', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1207, 48, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1208, 48, NULL, 'Xã Hưng Hà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1209, 48, NULL, 'Xã Tiên La', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1210, 48, NULL, 'Xã Lê Quý Đôn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1211, 48, NULL, 'Xã Hồng Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1212, 48, NULL, 'Xã Thần Khê', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1213, 48, NULL, 'Xã Diên Hà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1214, 48, NULL, 'Xã Ngự Thiên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1215, 48, NULL, 'Xã Long Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1216, 48, NULL, 'Xã Đông Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1217, 48, NULL, 'Xã Bắc Tiên Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1218, 48, NULL, 'Xã Đông Tiên Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1219, 48, NULL, 'Xã Nam Đông Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1220, 48, NULL, 'Xã  Bắc Đông Quan', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1221, 48, NULL, 'Xã Bắc Đông Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1222, 48, NULL, 'Xã Đông Quan', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1223, 48, NULL, 'Xã Nam Tiên Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1224, 48, NULL, 'Xã Tiên Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1225, 48, NULL, 'Xã Lê Lợi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1226, 48, NULL, 'Xã Kiến Xương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1227, 48, NULL, 'Xã Quang Lịch', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1228, 48, NULL, 'Xã Vũ Quý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1229, 48, NULL, 'Xã Bình Thanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1230, 48, NULL, 'Xã Bình Định', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1231, 48, NULL, 'Xã Hồng Vũ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1232, 48, NULL, 'Xã Bình Nguyên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1233, 48, NULL, 'Xã Trà Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1234, 48, NULL, 'Xã Vũ Thư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1235, 48, NULL, 'Xã Thư Trì', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1236, 48, NULL, 'Xã Tân Thuận', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1237, 48, NULL, 'Xã Thư Vũ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1238, 48, NULL, 'Xã Vũ Tiên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1239, 48, NULL, 'Xã Vạn Xuân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1240, 49, NULL, 'Xã Gia Viễn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1241, 49, NULL, 'Xã Đại Hoàng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1242, 49, NULL, 'Xã Gia Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1243, 49, NULL, 'Xã Gia Phong', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1244, 49, NULL, 'Xã Gia Vân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1245, 49, NULL, 'Xã Gia Trấn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1246, 49, NULL, 'Xã Nho Quan', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1247, 49, NULL, 'Xã Gia Lâm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1248, 49, NULL, 'Xã Gia Tường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1249, 49, NULL, 'Xã Phú Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1250, 49, NULL, 'Xã Cúc Phương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1251, 49, NULL, 'Xã Phú Long', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1252, 49, NULL, 'Xã Thanh Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1253, 49, NULL, 'Xã Quỳnh Lưu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1254, 49, NULL, 'Xã Yên Khánh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1255, 49, NULL, 'Xã Khánh Nhạc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1256, 49, NULL, 'Xã Khánh Thiện', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1257, 49, NULL, 'Xã Khánh Hội', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1258, 49, NULL, 'Xã Khánh Trung', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1259, 49, NULL, 'Xã Yên Mô', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1260, 49, NULL, 'Xã Yên Từ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1261, 49, NULL, 'Xã Yên Mạc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1262, 49, NULL, 'Xã Đồng Thái', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1263, 49, NULL, 'Xã Chất Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1264, 49, NULL, 'Xã Kim Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1265, 49, NULL, 'Xã Quang Thiện', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1266, 49, NULL, 'Xã Phát Diệm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1267, 49, NULL, 'Xã Lai Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1268, 49, NULL, 'Xã Định Hóa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1269, 49, NULL, 'Xã Bình Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1270, 49, NULL, 'Xã Kim Đông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1271, 49, NULL, 'Xã Bình Lục', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1272, 49, NULL, 'Xã Bình Mỹ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1273, 49, NULL, 'Xã Bình An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1274, 49, NULL, 'Xã Bình Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1275, 49, NULL, 'Xã Bình Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1276, 49, NULL, 'Xã Liêm Hà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1277, 49, NULL, 'Xã Tân Thanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1278, 49, NULL, 'Xã Thanh Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1279, 49, NULL, 'Xã Thanh Lâm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1280, 49, NULL, 'Xã Thanh Liêm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1281, 49, NULL, 'Xã Lý Nhân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1282, 49, NULL, 'Xã Nam Xang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1283, 49, NULL, 'Xã Bắc Lý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1284, 49, NULL, 'Xã Vĩnh Trụ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1285, 49, NULL, 'Xã Trần Thương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1286, 49, NULL, 'Xã Nhân Hà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1287, 49, NULL, 'Xã Nam Lý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1288, 49, NULL, 'Xã Nam Trực', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1289, 49, NULL, 'Xã Nam Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1290, 49, NULL, 'Xã Nam Đồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1291, 49, NULL, 'Xã Nam Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1292, 49, NULL, 'Xã Nam Hồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1293, 49, NULL, 'Xã Minh Tân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1294, 49, NULL, 'Xã Hiển Khánh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1295, 49, NULL, 'Xã Vụ Bản', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1296, 49, NULL, 'Xã Liên Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1297, 49, NULL, 'Xã Ý Yên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1298, 49, NULL, 'Xã Yên Đồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1299, 49, NULL, 'Xã Yên Cường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1300, 49, NULL, 'Xã Vạn Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1301, 49, NULL, 'Xã Vũ Dương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1302, 49, NULL, 'Xã Tân Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1303, 49, NULL, 'Xã Phong Doanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1304, 49, NULL, 'Xã Cổ Lễ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1305, 49, NULL, 'Xã Ninh Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1306, 49, NULL, 'Xã Cát Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1307, 49, NULL, 'Xã Trực Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1308, 49, NULL, 'Xã Quang Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1309, 49, NULL, 'Xã Minh Thái', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1310, 49, NULL, 'Xã Ninh Cường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1311, 49, NULL, 'Xã Xuân Trường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1312, 49, NULL, 'Xã Xuân Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1313, 49, NULL, 'Xã  Xuân Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1314, 49, NULL, 'Xã Xuân Hồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1315, 49, NULL, 'Xã Hải Hậu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1316, 49, NULL, 'Xã Hải Anh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1317, 49, NULL, 'Xã Hải Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1318, 49, NULL, 'Xã Hải Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1319, 49, NULL, 'Xã Hải An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1320, 49, NULL, 'Xã Hải Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1321, 49, NULL, 'Xã Hải Xuân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1322, 49, NULL, 'Xã Hải Thịnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1323, 49, NULL, 'Xã Giao Minh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1324, 49, NULL, 'Xã Giao Hoà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1325, 49, NULL, 'Xã Giao Thuỷ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1326, 49, NULL, 'Xã Giao Phúc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1327, 49, NULL, 'Xã Giao Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1328, 49, NULL, 'Xã Giao Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1329, 49, NULL, 'Xã Giao Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1330, 49, NULL, 'Xã Đồng Thịnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1331, 49, NULL, 'Xã Nghĩa Hưng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1332, 49, NULL, 'Xã Nghĩa Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1333, 49, NULL, 'Xã Hồng Phong', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1334, 49, NULL, 'Xã Quỹ Nhất', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1335, 49, NULL, 'Xã Nghĩa Lâm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1336, 49, NULL, 'Xã Rạng Đông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1337, 49, NULL, 'Phường Tây Hoa Lư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1338, 49, NULL, 'Phường Hoa Lư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1339, 49, NULL, 'Phường Nam Hoa Lư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1340, 49, NULL, 'Phường Đông Hoa Lư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1341, 49, NULL, 'Phường Tam Điệp', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1342, 49, NULL, 'Phường Yên Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1343, 49, NULL, 'Phường Trung Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1344, 49, NULL, 'Phường Yên Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1345, 49, NULL, 'Phường Hà Nam', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1346, 49, NULL, 'Phường Phủ Lý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1347, 49, NULL, 'Phường Phù Vân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1348, 49, NULL, 'Phường Châu Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1349, 49, NULL, 'Phường Liêm Tuyền', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1350, 49, NULL, 'Phường Duy Tiên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1351, 49, NULL, 'Phường Duy Tân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1352, 49, NULL, 'Phường Đồng Văn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1353, 49, NULL, 'Phường Duy Hà', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1354, 49, NULL, 'Phường Tiên Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1355, 49, NULL, 'Phường Lê Hồ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1356, 49, NULL, 'Phường Nguyễn Uý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1357, 49, NULL, 'Phường Lý Thường Kiệt', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1358, 49, NULL, 'Phường Kim Thanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1359, 49, NULL, 'Phường Tam Chúc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1360, 49, NULL, 'Phường Kim Bảng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1361, 49, NULL, 'Phường Nam Định', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1362, 49, NULL, 'Phường Thiên Trường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1363, 49, NULL, 'Phường Đông A', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1364, 49, NULL, 'Phường Vị Khê', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1365, 49, NULL, 'Phường Thành Nam', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1366, 49, NULL, 'Phường Trường Thi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1367, 49, NULL, 'Phường Hồng Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1368, 49, NULL, 'Phường Mỹ Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1369, 50, NULL, 'Phường Hạc Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1370, 50, NULL, 'Phường Quảng Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1371, 50, NULL, 'Phường Đông Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1372, 50, NULL, 'Phường Đông Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1373, 50, NULL, 'Phường Đông Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1374, 50, NULL, 'Phường Hàm Rồng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1375, 50, NULL, 'Phường Nguyệt Viên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1376, 50, NULL, 'Phường Sầm Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1377, 50, NULL, 'Phường Nam Sầm Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1378, 50, NULL, 'Phường Bỉm Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1379, 50, NULL, 'Phường Quang Trung', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1380, 50, NULL, 'Phường Ngọc Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1381, 50, NULL, 'Phường Tân Dân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1382, 50, NULL, 'Phường Hải Lĩnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1383, 50, NULL, 'Phường Tĩnh Gia', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1384, 50, NULL, 'Phường Đào Duy Tư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1385, 50, NULL, 'Phường Hải Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1386, 50, NULL, 'Phường Trúc Lâm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1387, 50, NULL, 'Phường Nghi Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1388, 50, NULL, 'Xã Các Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1389, 50, NULL, 'Xã Trường Lâm', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1390, 50, NULL, 'Xã Hà Trung', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1391, 50, NULL, 'Xã Tống Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1392, 50, NULL, 'Xã Hà Long', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1393, 50, NULL, 'Xã Hoạt Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1394, 50, NULL, 'Xã Lĩnh Toại', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1395, 50, NULL, 'Xã Triệu Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1396, 50, NULL, 'Xã Đông Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1397, 50, NULL, 'Xã Hậu Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1398, 50, NULL, 'Xã Hoa Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1399, 50, NULL, 'Xã Vạn Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1400, 50, NULL, 'Xã Nga Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1401, 50, NULL, 'Xã Nga Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1402, 50, NULL, 'Xã Hồ Vương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1403, 50, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1404, 50, NULL, 'Xã Nga An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1405, 50, NULL, 'Xã Ba Đình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1406, 50, NULL, 'Xã Hoằng Hóa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1407, 50, NULL, 'Xã Hoằng Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1408, 50, NULL, 'Xã Hoằng Thanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1409, 50, NULL, 'Xã Hoằng Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1410, 50, NULL, 'Xã Hoằng Châu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1411, 50, NULL, 'Xã Hoằng Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1412, 50, NULL, 'Xã Hoằng Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1413, 50, NULL, 'Xã Hoằng Giang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1414, 50, NULL, 'Xã Lưu Vệ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1415, 50, NULL, 'Xã Quảng Yên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1416, 50, NULL, 'Xã Quảng Ngọc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1417, 50, NULL, 'Xã Quảng Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1418, 50, NULL, 'Xã Quảng Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1419, 50, NULL, 'Xã Tiên Trang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1420, 50, NULL, 'Xã Quảng Chính', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1421, 50, NULL, 'Xã Nông Cống', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1422, 50, NULL, 'Xã Thắng Lợi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1423, 50, NULL, 'Xã Trung Chính', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1424, 50, NULL, 'Xã Trường Văn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1425, 50, NULL, 'Xã Thăng Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1426, 50, NULL, 'Xã Tượng Lĩnh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1427, 50, NULL, 'Xã Công Chính', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1428, 50, NULL, 'Xã Thiệu Hóa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1429, 50, NULL, 'Xã Thiệu Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1430, 50, NULL, 'Xã Thiệu Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1431, 50, NULL, 'Xã Thiệu Toán', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1432, 50, NULL, 'Xã Thiệu Trung', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1433, 50, NULL, 'Xã Yên Định', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1434, 50, NULL, 'Xã Yên Trường', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1435, 50, NULL, 'Xã Yên Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1436, 50, NULL, 'Xã Quý Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1437, 50, NULL, 'Xã Yên Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1438, 50, NULL, 'Xã Định Tân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1439, 50, NULL, 'Xã Định Hòa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1440, 50, NULL, 'Xã Thọ Xuân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1441, 50, NULL, 'Xã Thọ Long', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1442, 50, NULL, 'Xã Xuân Hòa', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1443, 50, NULL, 'Xã Sao Vàng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1444, 50, NULL, 'Xã Lam Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1445, 50, NULL, 'Xã Thọ Lập', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1446, 50, NULL, 'Xã Xuân Tín', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1447, 50, NULL, 'Xã Xuân Lập', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1448, 50, NULL, 'Xã Vĩnh Lộc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1449, 50, NULL, 'Xã Tây Đô', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1450, 50, NULL, 'Xã Biện Thượng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1451, 50, NULL, 'Xã Triệu Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1452, 50, NULL, 'Xã Thọ Bình', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1453, 50, NULL, 'Xã Thọ Ngọc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1454, 50, NULL, 'Xã Thọ Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1455, 50, NULL, 'Xã Hợp Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1456, 50, NULL, 'Xã An Nông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1457, 50, NULL, 'Xã Tân Ninh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1458, 50, NULL, 'Xã Đồng Tiến', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1459, 50, NULL, 'Xã Mường Chanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1460, 50, NULL, 'Xã Quang Chiểu', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1461, 50, NULL, 'Xã Tam Chung', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1462, 50, NULL, 'Xã Mường Lát', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1463, 50, NULL, 'Xã Pù Nhi', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1464, 50, NULL, 'Xã Nhi Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1465, 50, NULL, 'Xã Mường Lý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1466, 50, NULL, 'Xã Trung Lý', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1467, 50, NULL, 'Xã Hồi Xuân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1468, 50, NULL, 'Xã Nam Xuân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1469, 50, NULL, 'Xã Thiên Phủ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1470, 50, NULL, 'Xã Hiền Kiệt', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1471, 50, NULL, 'Xã Phú Xuân', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1472, 50, NULL, 'Xã Phú Lệ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1473, 50, NULL, 'Xã Trung Thành', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1474, 50, NULL, 'Xã Trung Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1475, 50, NULL, 'Xã Na Mèo', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1476, 50, NULL, 'Xã Sơn Thủy', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1477, 50, NULL, 'Xã Sơn Điện', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1478, 50, NULL, 'Xã Mường Mìn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1479, 50, NULL, 'Xã Tam Thanh', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1480, 50, NULL, 'Xã Tam Lư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1481, 50, NULL, 'Xã Quan Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1482, 50, NULL, 'Xã Trung Hạ', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1483, 50, NULL, 'Xã Linh Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1484, 50, NULL, 'Xã Đồng Lương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1485, 50, NULL, 'Xã Văn Phú', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1486, 50, NULL, 'Xã Giao An', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1487, 50, NULL, 'Xã Yên Khương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1488, 50, NULL, 'Xã Yên Thắng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1489, 50, NULL, 'Xã Văn Nho', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1490, 50, NULL, 'Xã Thiết Ống', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1491, 50, NULL, 'Xã Bá Thước', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1492, 50, NULL, 'Xã Cổ Lũng', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1493, 50, NULL, 'Xã Pù Luông', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1494, 50, NULL, 'Xã Điền Lư', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1495, 50, NULL, 'Xã Điền Quang', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1496, 50, NULL, 'Xã Quý Lương', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1497, 50, NULL, 'Xã Ngọc Lặc', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1498, 50, NULL, 'Xã Thạch Lập', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1499, 50, NULL, 'Xã Ngọc Liên', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1500, 50, NULL, 'Xã Minh Sơn', NULL, 1, '2025-11-28 13:38:44', '2025-11-28 13:38:44'),
(1501, 50, NULL, 'Xã Nguyệt Ấn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1502, 50, NULL, 'Xã Kiên Thọ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1503, 50, NULL, 'Xã Cẩm Thạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1504, 50, NULL, 'Xã Cẩm Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1505, 50, NULL, 'Xã Cẩm Tú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1506, 50, NULL, 'Xã Cẩm Vân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1507, 50, NULL, 'Xã Cẩm Tân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1508, 50, NULL, 'Xã Kim Tân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1509, 50, NULL, 'Xã Vân Du', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1510, 50, NULL, 'Xã Ngọc Trạo', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1511, 50, NULL, 'Xã Thạch Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1512, 50, NULL, 'Xã Thành Vinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1513, 50, NULL, 'Xã Thạch Quảng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1514, 50, NULL, 'Xã Như Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1515, 50, NULL, 'Xã Thượng Ninh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1516, 50, NULL, 'Xã Xuân Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1517, 50, NULL, 'Xã Hóa Quỳ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1518, 50, NULL, 'Xã Thanh Quân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1519, 50, NULL, 'Xã Thanh Phong', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1520, 50, NULL, 'Xã Xuân Du', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1521, 50, NULL, 'Xã Mậu Lâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1522, 50, NULL, 'Xã Như Thanh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1523, 50, NULL, 'Xã Yên Thọ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1524, 50, NULL, 'Xã Xuân Thái', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1525, 50, NULL, 'Xã Thanh Kỳ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1526, 50, NULL, 'Xã Bát Mọt', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1527, 50, NULL, 'Xã Yên Nhân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1528, 50, NULL, 'Xã Lương Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1529, 50, NULL, 'Xã Thường Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1530, 50, NULL, 'Xã Luận Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1531, 50, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1532, 50, NULL, 'Xã Vạn Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1533, 50, NULL, 'Xã Thắng Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1534, 50, NULL, 'Xã Xuân Chinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1535, 51, NULL, 'Xã Anh Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1536, 51, NULL, 'Xã Yên Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1537, 51, NULL, 'Xã Nhân Hòa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1538, 51, NULL, 'Xã Anh Sơn Đông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1539, 51, NULL, 'Xã Vĩnh Tường', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1540, 51, NULL, 'Xã Thành Bình Thọ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1541, 51, NULL, 'Xã Con Cuông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1542, 51, NULL, 'Xã Môn Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1543, 51, NULL, 'Xã Mậu Thạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1544, 51, NULL, 'Xã Cam Phục', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1545, 51, NULL, 'Xã Châu Khê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1546, 51, NULL, 'Xã Bình Chuẩn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1547, 51, NULL, 'Xã Diễn Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1548, 51, NULL, 'Xã Đức Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1549, 51, NULL, 'Xã Quảng Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1550, 51, NULL, 'Xã Hải Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1551, 51, NULL, 'Xã Tân Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1552, 51, NULL, 'Xã An Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1553, 51, NULL, 'Xã Minh Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1554, 51, NULL, 'Xã Hùng Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1555, 51, NULL, 'Xã Đô Lương', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1556, 51, NULL, 'Xã Bạch Ngọc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1557, 51, NULL, 'Xã Văn Hiến', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1558, 51, NULL, 'Xã Bạch Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1559, 51, NULL, 'Xã Thuần Trung', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1560, 51, NULL, 'Xã Lương Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1561, 51, NULL, 'Phường Hoàng Mai', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1562, 51, NULL, 'Phường Tân Mai', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1563, 51, NULL, 'Phường Quỳnh Mai', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1564, 51, NULL, 'Xã Hưng Nguyên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1565, 51, NULL, 'Xã Yên Trung', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1566, 51, NULL, 'Xã Hưng Nguyên Nam', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1567, 51, NULL, 'Xã Lam Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1568, 51, NULL, 'Xã Mường Xén', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1569, 51, NULL, 'Xã Hữu Kiệm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1570, 51, NULL, 'Xã Nậm Cắn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1571, 51, NULL, 'Xã Chiêu Lưu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1572, 51, NULL, 'Xã Na Loi', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1573, 51, NULL, 'Xã Mường Típ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1574, 51, NULL, 'Xã Na Ngoi', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1575, 51, NULL, 'Xã Mỹ Lý', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1576, 51, NULL, 'Xã Bắc Lý', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1577, 51, NULL, 'Xã Keng Đu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1578, 51, NULL, 'Xã Huồi Tụ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1579, 51, NULL, 'Xã Mường Lống', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1580, 51, NULL, 'Xã Vạn An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1581, 51, NULL, 'Xã Nam Đàn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1582, 51, NULL, 'Xã Đại Huệ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1583, 51, NULL, 'Xã Thiên Nhẫn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1584, 51, NULL, 'Xã Kim Liên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1585, 51, NULL, 'Xã Nghĩa Đàn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1586, 51, NULL, 'Xã Nghĩa Thọ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1587, 51, NULL, 'Xã Nghĩa Lâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1588, 51, NULL, 'Xã Nghĩa Mai', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1589, 51, NULL, 'Xã Nghĩa Hưng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1590, 51, NULL, 'Xã Nghĩa Khánh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1591, 51, NULL, 'Xã Nghĩa Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1592, 51, NULL, 'Xã Nghi Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1593, 51, NULL, 'Xã Phúc Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1594, 51, NULL, 'Xã Đông Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1595, 51, NULL, 'Xã Trung Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1596, 51, NULL, 'Xã Thần Lĩnh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1597, 51, NULL, 'Xã Hải Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1598, 51, NULL, 'Xã Văn Kiều', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1599, 51, NULL, 'Xã Quế Phong', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1600, 51, NULL, 'Xã Tiền Phong', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1601, 51, NULL, 'Xã Tri Lễ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1602, 51, NULL, 'Xã Mường Quàng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1603, 51, NULL, 'Xã Thông Thụ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1604, 51, NULL, 'Xã Quỳ Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1605, 51, NULL, 'Xã Châu Tiến', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1606, 51, NULL, 'Xã Hùng Chân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1607, 51, NULL, 'Xã Châu Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1608, 51, NULL, 'Xã Quỳ Hợp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1609, 51, NULL, 'Xã Tam Hợp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1610, 51, NULL, 'Xã Châu Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1611, 51, NULL, 'Xã Châu Hồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1612, 51, NULL, 'Xã Mường Ham', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1613, 51, NULL, 'Xã Mường Chọng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1614, 51, NULL, 'Xã Minh Hợp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1615, 51, NULL, 'Xã Quỳnh Lưu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1616, 51, NULL, 'Xã Quỳnh Văn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1617, 51, NULL, 'Xã Quỳnh Anh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1618, 51, NULL, 'Xã Quỳnh Tam', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1619, 51, NULL, 'Xã Quỳnh Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1620, 51, NULL, 'Xã Quỳnh Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1621, 51, NULL, 'Xã Quỳnh Thắng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1622, 51, NULL, 'Xã Tân Kỳ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1623, 51, NULL, 'Xã Tân Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1624, 51, NULL, 'Xã Tân An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1625, 51, NULL, 'Xã Nghĩa Đồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1626, 51, NULL, 'Xã Giai Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1627, 51, NULL, 'Xã Nghĩa Hành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1628, 51, NULL, 'Xã Tiên Đồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1629, 51, NULL, 'Phường Thái Hòa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1630, 51, NULL, 'Phường Tây Hiếu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1631, 51, NULL, 'Xã Đông Hiếu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1632, 51, NULL, 'Xã Cát Ngạn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1633, 51, NULL, 'Xã Tam Đồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1634, 51, NULL, 'Xã Hạnh Lâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1635, 51, NULL, 'Xã Sơn Lâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1636, 51, NULL, 'Xã Hoa Quân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1637, 51, NULL, 'Xã Kim Bảng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1638, 51, NULL, 'Xã Bích Hào', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1639, 51, NULL, 'Xã Đại Đồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1640, 51, NULL, 'Xã Xuân Lâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1641, 51, NULL, 'Xã Tam Quang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1642, 51, NULL, 'Xã Tam Thái', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1643, 51, NULL, 'Xã Tương Dương', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1644, 51, NULL, 'Xã Lượng Minh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1645, 51, NULL, 'Xã Yên Na', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1646, 51, NULL, 'Xã Yên Hòa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1647, 51, NULL, 'Xã Nga My', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1648, 51, NULL, 'Xã Hữu Khuông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1649, 51, NULL, 'Xã Nhôn Mai', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1650, 51, NULL, 'Phường Trường Vinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1651, 51, NULL, 'Phường Thành Vinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1652, 51, NULL, 'Phường Vinh Hưng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1653, 51, NULL, 'Phường Vinh Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1654, 51, NULL, 'Phường Vinh Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1655, 51, NULL, 'Phường Cửa Lò', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1656, 51, NULL, 'Xã Yên Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1657, 51, NULL, 'Xã Quan Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1658, 51, NULL, 'Xã Hợp Minh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1659, 51, NULL, 'Xã Vân Tụ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1660, 51, NULL, 'Xã Vân Du', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1661, 51, NULL, 'Xã Quang Đồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1662, 51, NULL, 'Xã Giai Lạc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1663, 51, NULL, 'Xã Bình Minh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1664, 51, NULL, 'Xã Đông Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1665, 52, NULL, 'Phường Sông Trí', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1666, 52, NULL, 'Phường Hải Ninh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1667, 52, NULL, 'Phường Hoành Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1668, 52, NULL, 'Phường Vũng Áng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1669, 52, NULL, 'Xã Kỳ Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1670, 52, NULL, 'Xã Kỳ Anh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1671, 52, NULL, 'Xã Kỳ Hoa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1672, 52, NULL, 'Xã Kỳ Văn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1673, 52, NULL, 'Xã Kỳ Khang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1674, 52, NULL, 'Xã Kỳ Lạc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1675, 52, NULL, 'Xã Kỳ Thượng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1676, 52, NULL, 'Xã Cẩm Xuyên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1677, 52, NULL, 'Xã Thiên Cầm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1678, 52, NULL, 'Xã Cẩm Duệ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1679, 52, NULL, 'Xã Cẩm Hưng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1680, 52, NULL, 'Xã Cẩm Lạc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1681, 52, NULL, 'Xã Cẩm Trung', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1682, 52, NULL, 'Xã Yên Hòa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1683, 52, NULL, 'Phường Thành Sen', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1684, 52, NULL, 'Phường Trần Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1685, 52, NULL, 'Phường Hà Huy Tập', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1686, 52, NULL, 'Xã Thạch Lạc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1687, 52, NULL, 'Xã Đồng Tiến', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1688, 52, NULL, 'Xã Thạch Khê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1689, 52, NULL, 'Xã Cẩm Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1690, 52, NULL, 'Xã Thạch Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1691, 52, NULL, 'Xã Toàn Lưu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1692, 52, NULL, 'Xã Việt Xuyên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1693, 52, NULL, 'Xã Đông Kinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1694, 52, NULL, 'Xã Thạch Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1695, 52, NULL, 'Xã Lộc Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1696, 52, NULL, 'Xã Hồng Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1697, 52, NULL, 'Xã Mai Phụ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1698, 52, NULL, 'Xã Can Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1699, 52, NULL, 'Xã Tùng Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1700, 52, NULL, 'Xã Gia Hanh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1701, 52, NULL, 'Xã Trường Lưu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1702, 52, NULL, 'Xã Xuân Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1703, 52, NULL, 'Xã Đồng Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1704, 52, NULL, 'Phường Bắc Hồng Lĩnh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1705, 52, NULL, 'Phường Nam Hồng Lĩnh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1706, 52, NULL, 'Xã Tiên Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1707, 52, NULL, 'Xã Nghi Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1708, 52, NULL, 'Xã Cổ Đạm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1709, 52, NULL, 'Xã Đan Hải', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1710, 52, NULL, 'Xã Đức Thọ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1711, 52, NULL, 'Xã Đức Quang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1712, 52, NULL, 'Xã Đức Đồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1713, 52, NULL, 'Xã Đức Thịnh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1714, 52, NULL, 'Xã Đức Minh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1715, 52, NULL, 'Xã Hương Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1716, 52, NULL, 'Xã Sơn Tây', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1717, 52, NULL, 'Xã Tứ Mỹ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1718, 52, NULL, 'Xã Sơn Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1719, 52, NULL, 'Xã Sơn Tiến', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1720, 52, NULL, 'Xã Sơn Hồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1721, 52, NULL, 'Xã Kim Hoa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1722, 52, NULL, 'Xã Vũ Quang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1723, 52, NULL, 'Xã Mai Hoa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1724, 52, NULL, 'Xã Thượng Đức', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1725, 52, NULL, 'Xã Hương Khê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1726, 52, NULL, 'Xã Hương Phố', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1727, 52, NULL, 'Xã Hương Đô', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1728, 52, NULL, 'Xã Hà Linh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46');
INSERT INTO `wards` (`id`, `province_id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1729, 52, NULL, 'Xã Hương Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1730, 52, NULL, 'Xã Phúc Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1731, 52, NULL, 'Xã Hương Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1732, 52, NULL, 'Xã Sơn Kim 1', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1733, 52, NULL, 'Xã Sơn Kim 2', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1734, 53, NULL, 'Phường Đồng Hới', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1735, 53, NULL, 'Phường Đồng Thuận', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1736, 53, NULL, 'Phường Đồng Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1737, 53, NULL, 'Xã Nam Gianh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1738, 53, NULL, 'Xã Nam Ba Đồn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1739, 53, NULL, 'Phường Ba Đồn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1740, 53, NULL, 'Phường Bắc Gianh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1741, 53, NULL, 'Xã Dân Hóa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1742, 53, NULL, 'Xã Kim Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1743, 53, NULL, 'Xã Kim Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1744, 53, NULL, 'Xã Minh Hóa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1745, 53, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1746, 53, NULL, 'Xã Tuyên Lâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1747, 53, NULL, 'Xã Tuyên Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1748, 53, NULL, 'Xã Đồng Lê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1749, 53, NULL, 'Xã Tuyên Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1750, 53, NULL, 'Xã Tuyên Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1751, 53, NULL, 'Xã Tuyên Hóa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1752, 53, NULL, 'Xã Tân Gianh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1753, 53, NULL, 'Xã Trung Thuần', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1754, 53, NULL, 'Xã Quảng Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1755, 53, NULL, 'Xã Hòa Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1756, 53, NULL, 'Xã Phú Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1757, 53, NULL, 'Xã Thượng Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1758, 53, NULL, 'Xã Phong Nha', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1759, 53, NULL, 'Xã Bắc Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1760, 53, NULL, 'Xã Đông Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1761, 53, NULL, 'Xã Hoàn Lão', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1762, 53, NULL, 'Xã Bố Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1763, 53, NULL, 'Xã Nam Trạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1764, 53, NULL, 'Xã Quảng Ninh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1765, 53, NULL, 'Xã Ninh Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1766, 53, NULL, 'Xã Trường Ninh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1767, 53, NULL, 'Xã Trường Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1768, 53, NULL, 'Xã Lệ Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1769, 53, NULL, 'Xã Cam Hồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1770, 53, NULL, 'Xã Sen Ngư', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1771, 53, NULL, 'Xã Tân Mỹ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1772, 53, NULL, 'Xã Trường Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1773, 53, NULL, 'Xã Lệ Ninh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1774, 53, NULL, 'Xã Kim Ngân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1775, 53, NULL, 'Phường Đông Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1776, 53, NULL, 'Phường Nam Đông Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1777, 53, NULL, 'Phường Quảng Trị', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1778, 53, NULL, 'Xã Vĩnh Linh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1779, 53, NULL, 'Xã Cửa Tùng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1780, 53, NULL, 'Xã Vĩnh Hoàng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1781, 53, NULL, 'Xã Vĩnh Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1782, 53, NULL, 'Xã Bến Quan', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1783, 53, NULL, 'Xã Cồn Tiên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1784, 53, NULL, 'Xã Cửa Việt', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1785, 53, NULL, 'Xã Gio Linh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1786, 53, NULL, 'Xã Bến Hải', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1787, 53, NULL, 'Xã Hướng Lập', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1788, 53, NULL, 'Xã Hướng Phùng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1789, 53, NULL, 'Xã Khe Sanh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1790, 53, NULL, 'Xã Tân Lập', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1791, 53, NULL, 'Xã Lao Bảo', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1792, 53, NULL, 'Xã Lìa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1793, 53, NULL, 'Xã A Dơi', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1794, 53, NULL, 'Xã La Lay', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1795, 53, NULL, 'Xã Tà Rụt', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1796, 53, NULL, 'Xã Đakrông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1797, 53, NULL, 'Xã Ba Lòng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1798, 53, NULL, 'Xã Hướng Hiệp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1799, 53, NULL, 'Xã Cam Lộ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1800, 53, NULL, 'Xã Hiếu Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1801, 53, NULL, 'Xã Triệu Phong', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1802, 53, NULL, 'Xã Ái Tử', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1803, 53, NULL, 'Xã Triệu Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1804, 53, NULL, 'Xã Triệu Cơ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1805, 53, NULL, 'Xã Nam Cửa Việt', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1806, 53, NULL, 'Xã Diên Sanh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1807, 53, NULL, 'Xã Mỹ Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1808, 53, NULL, 'Xã Hải Lăng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1809, 53, NULL, 'Xã Vĩnh Định', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1810, 53, NULL, 'Xã Nam Hải Lăng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1811, 53, NULL, 'Đặc khu Cồn Cỏ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1812, 54, NULL, 'Phường Thuận An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1813, 54, NULL, 'Phường Hóa Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1814, 54, NULL, 'Phường Mỹ Thượng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1815, 54, NULL, 'Phường Vỹ Dạ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1816, 54, NULL, 'Phường Thuận Hóa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1817, 54, NULL, 'Phường An Cựu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1818, 54, NULL, 'Phường Thủy Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1819, 54, NULL, 'Phường Kim Long', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1820, 54, NULL, 'Phường Hương An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1821, 54, NULL, 'Phường Phú Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1822, 54, NULL, 'Phường Hương Trà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1823, 54, NULL, 'Phường Kim Trà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1824, 54, NULL, 'Phường Thanh Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1825, 54, NULL, 'Phường Hương Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1826, 54, NULL, 'Phường Phú Bài', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1827, 54, NULL, 'Phường Phong Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1828, 54, NULL, 'Phường Phong Thái', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1829, 54, NULL, 'Phường Phong Dinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1830, 54, NULL, 'Phường Phong Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1831, 54, NULL, 'Phường Phong Quảng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1832, 54, NULL, 'Xã Đan Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1833, 54, NULL, 'Xã Quảng Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1834, 54, NULL, 'Xã Phú Vinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1835, 54, NULL, 'Xã Phú Hồ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1836, 54, NULL, 'Xã Phú Vang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1837, 54, NULL, 'Xã Vinh Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1838, 54, NULL, 'Xã Hưng Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1839, 54, NULL, 'Xã Lộc An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1840, 54, NULL, 'Xã Phú Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1841, 54, NULL, 'Xã Chân Mây – Lăng Cô', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1842, 54, NULL, 'Xã Long Quảng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1843, 54, NULL, 'Xã Nam Đông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1844, 54, NULL, 'Xã Khe Tre', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1845, 54, NULL, 'Xã Bình Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1846, 54, NULL, 'Xã A Lưới 1', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1847, 54, NULL, 'Xã A Lưới 2', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1848, 54, NULL, 'Xã A Lưới 3', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1849, 54, NULL, 'Xã A Lưới 4', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1850, 54, NULL, 'Xã A Lưới 5', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1851, 54, NULL, 'Phường Dương Nỗ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1852, 55, NULL, 'Phường Hải Châu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1853, 55, NULL, 'Phường Hòa Cường', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1854, 55, NULL, 'Phường Thanh Khê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1855, 55, NULL, 'Phường An Khê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1856, 55, NULL, 'Phường An Hải', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1857, 55, NULL, 'Phường Sơn Trà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1858, 55, NULL, 'Phường Ngũ Hành Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1859, 55, NULL, 'Phường Hòa Khánh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1860, 55, NULL, 'Phường Hải Vân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1861, 55, NULL, 'Phường Liên Chiểu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1862, 55, NULL, 'Phường Cẩm Lệ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1863, 55, NULL, 'Phường Hòa Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1864, 55, NULL, 'Xã Hòa Vang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1865, 55, NULL, 'Xã Hòa Tiến', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1866, 55, NULL, 'Xã Bà Nà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1867, 55, NULL, 'Đặc khu Hoàng Sa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1868, 55, NULL, 'Xã Núi Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1869, 55, NULL, 'Xã Tam Mỹ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1870, 55, NULL, 'Xã Tam Anh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1871, 55, NULL, 'Xã Đức Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1872, 55, NULL, 'Xã Tam Xuân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1873, 55, NULL, 'Xã Tam Hải', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1874, 55, NULL, 'Phường Tam Kỳ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1875, 55, NULL, 'Phường Quảng Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1876, 55, NULL, 'Phường Hương Trà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1877, 55, NULL, 'Phường Bàn Thạch', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1878, 55, NULL, 'Xã Tây Hồ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1879, 55, NULL, 'Xã Chiên Đàn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1880, 55, NULL, 'Xã Phú Ninh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1881, 55, NULL, 'Xã Lãnh Ngọc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1882, 55, NULL, 'Xã Tiên Phước', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1883, 55, NULL, 'Xã Thạnh Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1884, 55, NULL, 'Xã Sơn Cẩm Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1885, 55, NULL, 'Xã Trà Liên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1886, 55, NULL, 'Xã Trà Giáp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1887, 55, NULL, 'Xã Trà Tân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1888, 55, NULL, 'Xã Trà Đốc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1889, 55, NULL, 'Xã Trà My', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1890, 55, NULL, 'Xã Nam Trà My', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1891, 55, NULL, 'Xã Trà Tập', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1892, 55, NULL, 'Xã Trà Vân', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1893, 55, NULL, 'Xã Trà Linh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1894, 55, NULL, 'Xã Trà Leng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1895, 55, NULL, 'Xã Thăng Bình', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1896, 55, NULL, 'Xã Thăng An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1897, 55, NULL, 'Xã Thăng Trường', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1898, 55, NULL, 'Xã Thăng Điền', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1899, 55, NULL, 'Xã Thăng Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1900, 55, NULL, 'Xã Đồng Dương', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1901, 55, NULL, 'Xã Quế Sơn Trung', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1902, 55, NULL, 'Xã Quế Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1903, 55, NULL, 'Xã Xuân Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1904, 55, NULL, 'Xã Nông Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1905, 55, NULL, 'Xã Quế Phước', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1906, 55, NULL, 'Xã Duy Nghĩa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1907, 55, NULL, 'Xã Nam Phước', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1908, 55, NULL, 'Xã Duy Xuyên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1909, 55, NULL, 'Xã Thu Bồn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1910, 55, NULL, 'Phường Điện Bàn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1911, 55, NULL, 'Phường Điện Bàn Đông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1912, 55, NULL, 'Phường An Thắng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1913, 55, NULL, 'Phường Điện Bàn Bắc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1914, 55, NULL, 'Xã Điện Bàn Tây', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1915, 55, NULL, 'Xã Gò Nổi', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1916, 55, NULL, 'Phường Hội An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1917, 55, NULL, 'Phường Hội An Đông', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1918, 55, NULL, 'Phường Hội An Tây', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1919, 55, NULL, 'Xã Tân Hiệp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1920, 55, NULL, 'Xã Đại Lộc', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1921, 55, NULL, 'Xã Hà Nha', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1922, 55, NULL, 'Xã Thượng Đức', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1923, 55, NULL, 'Xã Vu Gia', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1924, 55, NULL, 'Xã Phú Thuận', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1925, 55, NULL, 'Xã Thạnh Mỹ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1926, 55, NULL, 'Xã Bến Giằng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1927, 55, NULL, 'Xã Nam Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1928, 55, NULL, 'Xã Đắc Pring', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1929, 55, NULL, 'Xã La Dêê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1930, 55, NULL, 'Xã La Êê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1931, 55, NULL, 'Xã Sông Vàng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1932, 55, NULL, 'Xã Sông Kôn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1933, 55, NULL, 'Xã Đông Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1934, 55, NULL, 'Xã Bến Hiên', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1935, 55, NULL, 'Xã Avương', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1936, 55, NULL, 'Xã Tây Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1937, 55, NULL, 'Xã Hùng Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1938, 55, NULL, 'Xã Hiệp Đức', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1939, 55, NULL, 'Xã Việt An', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1940, 55, NULL, 'Xã Phước Trà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1941, 55, NULL, 'Xã Khâm Đức', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1942, 55, NULL, 'Xã Phước Năng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1943, 55, NULL, 'Xã Phước Chánh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1944, 55, NULL, 'Xã Phước Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1945, 55, NULL, 'Xã Phước Hiệp', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1946, 56, NULL, 'Xã Tịnh Khê', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1947, 56, NULL, 'Phường Trương Quang Trọng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1948, 56, NULL, 'Xã An Phú', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1949, 56, NULL, 'Phường Cẩm Thành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1950, 56, NULL, 'Phường Nghĩa Lộ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1951, 56, NULL, 'Phường Trà Câu', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1952, 56, NULL, 'Xã Nguyễn Nghiêm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1953, 56, NULL, 'Phường Đức Phổ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1954, 56, NULL, 'Xã Khánh Cường', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1955, 56, NULL, 'Phường Sa Huỳnh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1956, 56, NULL, 'Xã Bình Minh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1957, 56, NULL, 'Xã Bình Chương', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1958, 56, NULL, 'Xã Bình Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1959, 56, NULL, 'Xã Vạn Tường', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1960, 56, NULL, 'Xã Đông Sơn', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1961, 56, NULL, 'Xã Trường Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1962, 56, NULL, 'Xã Ba Gia', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1963, 56, NULL, 'Xã Sơn Tịnh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1964, 56, NULL, 'Xã Thọ Phong', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1965, 56, NULL, 'Xã Tư Nghĩa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1966, 56, NULL, 'Xã Vệ Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1967, 56, NULL, 'Xã Nghĩa Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1968, 56, NULL, 'Xã Trà Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1969, 56, NULL, 'Xã Nghĩa Hành', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1970, 56, NULL, 'Xã Đình Cương', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1971, 56, NULL, 'Xã Thiện Tín', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1972, 56, NULL, 'Xã Phước Giang', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1973, 56, NULL, 'Xã Long Phụng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1974, 56, NULL, 'Xã Mỏ Cày', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1975, 56, NULL, 'Xã Mộ Đức', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1976, 56, NULL, 'Xã Lân Phong', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1977, 56, NULL, 'Xã Trà Bồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1978, 56, NULL, 'Xã Đông Trà Bồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1979, 56, NULL, 'Xã Tây Trà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1980, 56, NULL, 'Xã Thanh Bồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1981, 56, NULL, 'Xã Cà Đam', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1982, 56, NULL, 'Xã Tây Trà Bồng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1983, 56, NULL, 'Xã Sơn Hạ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1984, 56, NULL, 'Xã Sơn Linh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1985, 56, NULL, 'Xã Sơn Hà', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1986, 56, NULL, 'Xã Sơn Thủy', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1987, 56, NULL, 'Xã Sơn Kỳ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1988, 56, NULL, 'Xã Sơn Tây', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1989, 56, NULL, 'Xã Sơn Tây Thượng', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1990, 56, NULL, 'Xã Sơn Tây Hạ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1991, 56, NULL, 'Xã Minh Long', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1992, 56, NULL, 'Xã Sơn Mai', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1993, 56, NULL, 'Xã Ba Vì', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1994, 56, NULL, 'Xã Ba Tô', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1995, 56, NULL, 'Xã Ba Dinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1996, 56, NULL, 'Xã Ba Tơ', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1997, 56, NULL, 'Xã Ba Vinh', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1998, 56, NULL, 'Xã Ba Động', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(1999, 56, NULL, 'Xã Đặng Thùy Trâm', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(2000, 56, NULL, 'Xã Ba Xa', NULL, 1, '2025-11-28 13:39:46', '2025-11-28 13:39:46'),
(2001, 56, NULL, 'Đặc khu Lý Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2002, 56, NULL, 'Phường Kon Tum', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2003, 56, NULL, 'Phường Đăk Cấm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2004, 56, NULL, 'Phường Đăk BLa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2005, 56, NULL, 'Xã Ngọk Bay', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2006, 56, NULL, 'Xã Ia Chim', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2007, 56, NULL, 'Xã Đăk Rơ Wa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2008, 56, NULL, 'Xã Đăk Pxi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2009, 56, NULL, 'Xã Đăk Mar', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2010, 56, NULL, 'Xã Đăk Ui', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2011, 56, NULL, 'Xã Ngọk Réo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2012, 56, NULL, 'Xã Đăk Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2013, 56, NULL, 'Xã Ngọk Tụ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2014, 56, NULL, 'Xã Đăk Tô', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2015, 56, NULL, 'Xã Kon Đào', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2016, 56, NULL, 'Xã Đăk Sao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2017, 56, NULL, 'Xã Đăk Tờ Kan', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2018, 56, NULL, 'Xã Tu Mơ Rông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2019, 56, NULL, 'Xã Măng Ri', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2020, 56, NULL, 'Xã Bờ Y', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2021, 56, NULL, 'Xã Sa Loong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2022, 56, NULL, 'Xã Dục Nông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2023, 56, NULL, 'Xã Xốp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2024, 56, NULL, 'Xã Ngọc Linh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2025, 56, NULL, 'Xã Đăk Plô', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2026, 56, NULL, 'Xã Đăk Pék', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2027, 56, NULL, 'Xã Đăk Môn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2028, 56, NULL, 'Xã Sa Thầy', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2029, 56, NULL, 'Xã Sa Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2030, 56, NULL, 'Xã Ya Ly', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2031, 56, NULL, 'Xã Ia Tơi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2032, 56, NULL, 'Xã Đăk Kôi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2033, 56, NULL, 'Xã Kon Braih', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2034, 56, NULL, 'Xã Đăk Rve', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2035, 56, NULL, 'Xã Măng Đen', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2036, 56, NULL, 'Xã Măng Bút', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2037, 56, NULL, 'Xã Kon Plông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2038, 56, NULL, 'Xã Đăk Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2039, 56, NULL, 'Xã Rờ Kơi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2040, 56, NULL, 'Xã Mô Rai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2041, 56, NULL, 'Xã Ia Đal', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2042, 57, NULL, 'Phường Quy Nhơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2043, 57, NULL, 'Phường Quy Nhơn Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2044, 57, NULL, 'Phường Quy Nhơn Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2045, 57, NULL, 'Phường Quy Nhơn Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2046, 57, NULL, 'Phường Quy Nhơn Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2047, 57, NULL, 'Phường Bình Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2048, 57, NULL, 'Phường An Nhơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2049, 57, NULL, 'Phường An Nhơn Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2050, 57, NULL, 'Phường An Nhơn Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2051, 57, NULL, 'Phường An Nhơn Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2052, 57, NULL, 'Xã An Nhơn Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2053, 57, NULL, 'Phường Bồng Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2054, 57, NULL, 'Phường Hoài Nhơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2055, 57, NULL, 'Phường Tam Quan', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2056, 57, NULL, 'Phường Hoài Nhơn Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2057, 57, NULL, 'Phường Hoài Nhơn Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2058, 57, NULL, 'Phường Hoài Nhơn Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2059, 57, NULL, 'Phường Hoài Nhơn Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2060, 57, NULL, 'Xã Phù Cát', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2061, 57, NULL, 'Xã Xuân An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2062, 57, NULL, 'Xã Ngô Mây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2063, 57, NULL, 'Xã Cát Tiến', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2064, 57, NULL, 'Xã Đề Gi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2065, 57, NULL, 'Xã Hòa Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2066, 57, NULL, 'Xã Hội Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2067, 57, NULL, 'Xã Phù Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2068, 57, NULL, 'Xã An Lương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2069, 57, NULL, 'Xã Bình Dương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2070, 57, NULL, 'Xã Phù Mỹ Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2071, 57, NULL, 'Xã Phù Mỹ Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2072, 57, NULL, 'Xã Phù Mỹ Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2073, 57, NULL, 'Xã Phù Mỹ Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2074, 57, NULL, 'Xã Tuy Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2075, 57, NULL, 'Xã Tuy Phước Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2076, 57, NULL, 'Xã Tuy Phước Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2077, 57, NULL, 'Xã Tuy Phước Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2078, 57, NULL, 'Xã Tây Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2079, 57, NULL, 'Xã Bình Khê', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2080, 57, NULL, 'Xã Bình Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2081, 57, NULL, 'Xã Bình Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2082, 57, NULL, 'Xã Bình An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2083, 57, NULL, 'Xã Hoài Ân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2084, 57, NULL, 'Xã Ân Tường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2085, 57, NULL, 'Xã Kim Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2086, 57, NULL, 'Xã Vạn Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2087, 57, NULL, 'Xã Ân Hảo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2088, 57, NULL, 'Xã Vân Canh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2089, 57, NULL, 'Xã Canh Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2090, 57, NULL, 'Xã Canh Liên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2091, 57, NULL, 'Xã Vĩnh Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2092, 57, NULL, 'Xã Vĩnh Thịnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2093, 57, NULL, 'Xã Vĩnh Quang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2094, 57, NULL, 'Xã Vĩnh Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2095, 57, NULL, 'Xã An Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2096, 57, NULL, 'Xã An Lão', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2097, 57, NULL, 'Xã An Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2098, 57, NULL, 'Xã An Toàn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2099, 57, NULL, 'Phường Pleiku', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2100, 57, NULL, 'Phường Hội Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2101, 57, NULL, 'Phường Thống Nhất', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2102, 57, NULL, 'Phường Diên Hồng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2103, 57, NULL, 'Phường An Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2104, 57, NULL, 'Xã Biển Hồ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2105, 57, NULL, 'Xã Gào', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2106, 57, NULL, 'Xã Ia Ly', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2107, 57, NULL, 'Xã Chư Păh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2108, 57, NULL, 'Xã Ia Khươl', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2109, 57, NULL, 'Xã Ia Phí', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2110, 57, NULL, 'Xã Chư Prông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2111, 57, NULL, 'Xã Bàu Cạn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2112, 57, NULL, 'Xã Ia Boòng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2113, 57, NULL, 'Xã Ia Lâu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2114, 57, NULL, 'Xã Ia Pia', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2115, 57, NULL, 'Xã Ia Tôr', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2116, 57, NULL, 'Xã Chư Sê', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2117, 57, NULL, 'Xã Bờ Ngoong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2118, 57, NULL, 'Xã Ia Ko', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2119, 57, NULL, 'Xã Albá', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2120, 57, NULL, 'Xã Chư Pưh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2121, 57, NULL, 'Xã Ia Le', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2122, 57, NULL, 'Xã Ia Hrú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2123, 57, NULL, 'Phường An Khê', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2124, 57, NULL, 'Phường An Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2125, 57, NULL, 'Xã Cửu An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2126, 57, NULL, 'Xã Đak Pơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2127, 57, NULL, 'Xã Ya Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2128, 57, NULL, 'Xã Kbang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2129, 57, NULL, 'Xã Kông Bơ La', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2130, 57, NULL, 'Xã Tơ Tung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2131, 57, NULL, 'Xã Sơn Lang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2132, 57, NULL, 'Xã Đak Rong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2133, 57, NULL, 'Xã Kông Chro', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2134, 57, NULL, 'Xã Ya Ma', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2135, 57, NULL, 'Xã Chư Krey', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2136, 57, NULL, 'Xã SRó', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2137, 57, NULL, 'Xã Đăk Song', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2138, 57, NULL, 'Xã Chơ Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2139, 57, NULL, 'Phường Ayun Pa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2140, 57, NULL, 'Xã Ia Rbol', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2141, 57, NULL, 'Xã Ia Sao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2142, 57, NULL, 'Xã Phú Thiện', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2143, 57, NULL, 'Xã Chư A Thai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2144, 57, NULL, 'Xã Ia Hiao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2145, 57, NULL, 'Xã Pờ Tó', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2146, 57, NULL, 'Xã Ia Pa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2147, 57, NULL, 'Xã Ia Tul', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2148, 57, NULL, 'Xã Phú Túc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2149, 57, NULL, 'Xã Ia Dreh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2150, 57, NULL, 'Xã Ia Rsai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2151, 57, NULL, 'Xã Uar', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2152, 57, NULL, 'Xã Đak Đoa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2153, 57, NULL, 'Xã Kon Gang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2154, 57, NULL, 'Xã Ia Băng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2155, 57, NULL, 'Xã KDang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2156, 57, NULL, 'Xã Đak Sơmei', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2157, 57, NULL, 'Xã Mang Yang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2158, 57, NULL, 'Xã Lơ Pang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2159, 57, NULL, 'Xã Kon Chiêng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2160, 57, NULL, 'Xã Hra', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2161, 57, NULL, 'Xã Ayun', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2162, 57, NULL, 'Xã Ia Grai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2163, 57, NULL, 'Xã Ia Krái', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2164, 57, NULL, 'Xã Ia Hrung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2165, 57, NULL, 'Xã Đức Cơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2166, 57, NULL, 'Xã Ia Dơk', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2167, 57, NULL, 'Xã Ia Krêl', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2168, 57, NULL, 'Xã Nhơn Châu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2169, 57, NULL, 'Xã Ia Púch', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2170, 57, NULL, 'Xã Ia Mơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2171, 57, NULL, 'Xã Ia Pnôn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2172, 57, NULL, 'Xã Ia Nan', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2173, 57, NULL, 'Xã Ia Dom', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2174, 57, NULL, 'Xã Ia Chia', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2175, 57, NULL, 'Xã Ia O', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2176, 57, NULL, 'Xã Krong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2177, 58, NULL, 'Phường Nha Trang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2178, 58, NULL, 'Phường Bắc Nha Trang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2179, 58, NULL, 'Phường Tây Nha Trang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2180, 58, NULL, 'Phường Nam Nha Trang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2181, 58, NULL, 'Phường Bắc Cam Ranh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2182, 58, NULL, 'Phường Cam Ranh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2183, 58, NULL, 'Phường Cam Linh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2184, 58, NULL, 'Phường Ba Ngòi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2185, 58, NULL, 'Xã Nam Cam Ranh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2186, 58, NULL, 'Xã Bắc Ninh Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2187, 58, NULL, 'Phường Ninh Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2188, 58, NULL, 'Xã Tân Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2189, 58, NULL, 'Phường Đông Ninh Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2190, 58, NULL, 'Phường Hòa Thắng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2191, 58, NULL, 'Xã Nam Ninh Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2192, 58, NULL, 'Xã Tây Ninh Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2193, 58, NULL, 'Xã Hòa Trí', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2194, 58, NULL, 'Xã Đại Lãnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2195, 58, NULL, 'Xã Tu Bông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2196, 58, NULL, 'Xã Vạn Thắng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2197, 58, NULL, 'Xã Vạn Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2198, 58, NULL, 'Xã Vạn Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2199, 58, NULL, 'Xã Diên Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2200, 58, NULL, 'Xã Diên Lạc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2201, 58, NULL, 'Xã Diên Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2202, 58, NULL, 'Xã Diên Lâm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2203, 58, NULL, 'Xã Diên Thọ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2204, 58, NULL, 'Xã Suối Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2205, 58, NULL, 'Xã Cam Lâm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2206, 58, NULL, 'Xã Suối Dầu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2207, 58, NULL, 'Xã Cam Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2208, 58, NULL, 'Xã Cam An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2209, 58, NULL, 'Xã Bắc Khánh Vĩnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2210, 58, NULL, 'Xã Trung Khánh Vĩnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2211, 58, NULL, 'Xã Tây Khánh Vĩnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2212, 58, NULL, 'Xã Nam Khánh Vĩnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2213, 58, NULL, 'Xã Khánh Vĩnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2214, 58, NULL, 'Xã Khánh Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2215, 58, NULL, 'Xã Tây Khánh Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2216, 58, NULL, 'Xã Đông Khánh Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2217, 58, NULL, 'Đặc khu Trường Sa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2218, 58, NULL, 'Phường Phan Rang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2219, 58, NULL, 'Phường Đông Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2220, 58, NULL, 'Phường Ninh Chử', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2221, 58, NULL, 'Phường Bảo An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2222, 58, NULL, 'Phường Đô Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2223, 58, NULL, 'Xã Ninh Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2224, 58, NULL, 'Xã Phước Hữu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2225, 58, NULL, 'Xã Phước Hậu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2226, 58, NULL, 'Xã Thuận Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2227, 58, NULL, 'Xã Cà Ná', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2228, 58, NULL, 'Xã Phước Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2229, 58, NULL, 'Xã Phước Dinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2230, 58, NULL, 'Xã Ninh Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2231, 58, NULL, 'Xã Xuân Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2232, 58, NULL, 'Xã Vĩnh Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2233, 58, NULL, 'Xã Thuận Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2234, 58, NULL, 'Xã Công Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2235, 58, NULL, 'Xã Ninh Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2236, 58, NULL, 'Xã Lâm Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2237, 58, NULL, 'Xã Anh Dũng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2238, 58, NULL, 'Xã Mỹ Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2239, 58, NULL, 'Xã Bác Ái Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2240, 58, NULL, 'Xã Bác Ái', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2241, 58, NULL, 'Xã Bác Ái Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2242, 59, NULL, 'Xã Hòa Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2243, 59, NULL, 'Phường  Buôn Ma Thuột', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2244, 59, NULL, 'Phường  Tân An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2245, 59, NULL, 'Phường  Tân Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2246, 59, NULL, 'Phường  Thành Nhất', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2247, 59, NULL, 'Phường Ea Kao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2248, 59, NULL, 'Xã Ea Drông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2249, 59, NULL, 'Phường Buôn Hồ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2250, 59, NULL, 'Phường Cư Bao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2251, 59, NULL, 'Xã Ea Súp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2252, 59, NULL, 'Xã Ea Rốk', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2253, 59, NULL, 'Xã Ea Bung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2254, 59, NULL, 'Xã Ia Rvê', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2255, 59, NULL, 'Xã Ia Lốp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2256, 59, NULL, 'Xã Ea Wer', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2257, 59, NULL, 'Xã Ea Nuôl', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2258, 59, NULL, 'Xã Buôn Đôn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2259, 59, NULL, 'Xã Ea Kiết', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2260, 59, NULL, 'Xã Ea M’Droh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2261, 59, NULL, 'Xã Quảng Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2262, 59, NULL, 'Xã Cuôr Đăng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2263, 59, NULL, 'Xã Cư M’gar', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2264, 59, NULL, 'Xã Ea Tul', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2265, 59, NULL, 'Xã Pơng Drang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2266, 59, NULL, 'Xã Krông Búk', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2267, 59, NULL, 'Xã Cư Pơng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2268, 59, NULL, 'Xã Ea Khăl', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2269, 59, NULL, 'Xã Ea Drăng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2270, 59, NULL, 'Xã Ea Wy', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2271, 59, NULL, 'Xã Ea H’leo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2272, 59, NULL, 'Xã Ea Hiao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2273, 59, NULL, 'Xã Krông Năng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2274, 59, NULL, 'Xã Dliê Ya', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2275, 59, NULL, 'Xã Tam Giang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2276, 59, NULL, 'Xã Phú Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2277, 59, NULL, 'Xã Krông Pắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2278, 59, NULL, 'Xã Ea Knuếc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2279, 59, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2280, 59, NULL, 'Xã Ea Phê', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2281, 59, NULL, 'Xã Ea Kly', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2282, 59, NULL, 'Xã Vụ Bổn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2283, 59, NULL, 'Xã Ea Kar', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2284, 59, NULL, 'Xã Ea Ô', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2285, 59, NULL, 'Xã Ea Knốp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2286, 59, NULL, 'Xã Cư Yang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2287, 59, NULL, 'Xã Ea Păl', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2288, 59, NULL, 'Xã M’Drắk', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2289, 59, NULL, 'Xã Ea Riêng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2290, 59, NULL, 'Xã Cư M’ta', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2291, 59, NULL, 'Xã Krông Á', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2292, 59, NULL, 'Xã Cư Prao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2293, 59, NULL, 'Xã Ea Trang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2294, 59, NULL, 'Xã Hòa Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2295, 59, NULL, 'Xã Dang Kang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2296, 59, NULL, 'Xã Krông Bông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2297, 59, NULL, 'Xã Yang Mao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2298, 59, NULL, 'Xã Cư Pui', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2299, 59, NULL, 'Xã Liên Sơn Lắk', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2300, 59, NULL, 'Xã Đắk Liêng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2301, 59, NULL, 'Xã Nam Ka', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50');
INSERT INTO `wards` (`id`, `province_id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(2302, 59, NULL, 'Xã Đắk Phơi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2303, 59, NULL, 'Xã Krông Nô', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2304, 59, NULL, 'Xã Ea Ning', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2305, 59, NULL, 'Xã Dray Bhăng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2306, 59, NULL, 'Xã Ea Ktur', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2307, 59, NULL, 'Xã Krông Ana', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2308, 59, NULL, 'Xã Dur Kmăl', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2309, 59, NULL, 'Xã Ea Na', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2310, 59, NULL, 'Phường Tuy Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2311, 59, NULL, 'Phường Phú Yên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2312, 59, NULL, 'Phường Bình Kiến', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2313, 59, NULL, 'Xã Xuân Thọ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2314, 59, NULL, 'Xã Xuân Cảnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2315, 59, NULL, 'Xã Xuân Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2316, 59, NULL, 'Phường Xuân Đài', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2317, 59, NULL, 'Phường Sông Cầu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2318, 59, NULL, 'Xã Hòa Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2319, 59, NULL, 'Phường Đông Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2320, 59, NULL, 'Phường Hòa Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2321, 59, NULL, 'Xã Tuy An Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2322, 59, NULL, 'Xã Tuy An Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2323, 59, NULL, 'Xã Ô Loan', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2324, 59, NULL, 'Xã Tuy An Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2325, 59, NULL, 'Xã Tuy An Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2326, 59, NULL, 'Xã Phú Hòa 1', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2327, 59, NULL, 'Xã Phú Hòa 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2328, 59, NULL, 'Xã Tây Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2329, 59, NULL, 'Xã Hòa Thịnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2330, 59, NULL, 'Xã Hòa Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2331, 59, NULL, 'Xã Sơn Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2332, 59, NULL, 'Xã Sơn Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2333, 59, NULL, 'Xã Vân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2334, 59, NULL, 'Xã Tây Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2335, 59, NULL, 'Xã Suối Trai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2336, 59, NULL, 'Xã Ea Ly', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2337, 59, NULL, 'Xã Ea Bá', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2338, 59, NULL, 'Xã Đức Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2339, 59, NULL, 'Xã Sông Hinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2340, 59, NULL, 'Xã Xuân Lãnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2341, 59, NULL, 'Xã Phú Mỡ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2342, 59, NULL, 'Xã Xuân Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2343, 59, NULL, 'Xã Đồng Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2344, 60, NULL, 'Phường Xuân Hương - Đà Lạt', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2345, 60, NULL, 'Phường Cam Ly - Đà Lạt', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2346, 60, NULL, 'Phường Lâm Viên - Đà Lạt', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2347, 60, NULL, 'Phường Xuân Trường - Đà Lạt', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2348, 60, NULL, 'Phường Lang Biang - Đà Lạt', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2349, 60, NULL, 'Phường 1 Bảo Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2350, 60, NULL, 'Phường 2 Bảo Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2351, 60, NULL, 'Phường 3 Bảo Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2352, 60, NULL, 'Phường B\'Lao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2353, 60, NULL, 'Xã Lạc Dương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2354, 60, NULL, 'Xã Đơn Dương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2355, 60, NULL, 'Xã Ka Đô', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2356, 60, NULL, 'Xã Quảng Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2357, 60, NULL, 'Xã D\'Ran', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2358, 60, NULL, 'Xã Hiệp Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2359, 60, NULL, 'Xã Đức Trọng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2360, 60, NULL, 'Xã Tân Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2361, 60, NULL, 'Xã Tà Hine', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2362, 60, NULL, 'Xã Tà Năng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2363, 60, NULL, 'Xã Đinh Văn Lâm Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2364, 60, NULL, 'Xã Phú Sơn Lâm Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2365, 60, NULL, 'Xã Nam Hà Lâm Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2366, 60, NULL, 'Xã Nam Ban Lâm Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2367, 60, NULL, 'Xã Tân Hà Lâm Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2368, 60, NULL, 'Xã Phúc Thọ Lâm Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2369, 60, NULL, 'Xã Đam Rông 1', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2370, 60, NULL, 'Xã Đam Rông 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2371, 60, NULL, 'Xã Đam Rông 3', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2372, 60, NULL, 'Xã Đam Rông 4', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2373, 60, NULL, 'Xã Di Linh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2374, 60, NULL, 'Xã Hòa Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2375, 60, NULL, 'Xã Hòa Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2376, 60, NULL, 'Xã Đinh Trang Thượng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2377, 60, NULL, 'Xã Bảo Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2378, 60, NULL, 'Xã Sơn Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2379, 60, NULL, 'Xã Gia Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2380, 60, NULL, 'Xã Bảo Lâm 1', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2381, 60, NULL, 'Xã Bảo Lâm 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2382, 60, NULL, 'Xã Bảo Lâm 3', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2383, 60, NULL, 'Xã Bảo Lâm 4', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2384, 60, NULL, 'Xã Bảo Lâm 5', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2385, 60, NULL, 'Xã Đạ Huoai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2386, 60, NULL, 'Xã Đạ Huoai 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2387, 60, NULL, 'Xã Đạ Huoai 3', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2388, 60, NULL, 'Xã Đạ Tẻh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2389, 60, NULL, 'Xã Đạ Tẻh 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2390, 60, NULL, 'Xã Đạ Tẻh 3', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2391, 60, NULL, 'Xã Cát Tiên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2392, 60, NULL, 'Xã Cát Tiên 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2393, 60, NULL, 'Xã Cát Tiên 3', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2394, 60, NULL, 'Phường Hàm Thắng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2395, 60, NULL, 'Phường Bình Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2396, 60, NULL, 'Phường Mũi Né', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2397, 60, NULL, 'Phường Phú Thuỷ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2398, 60, NULL, 'Phường Phan Thiết', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2399, 60, NULL, 'Phường Tiến Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2400, 60, NULL, 'Phường La Gi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2401, 60, NULL, 'Phường Phước Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2402, 60, NULL, 'Xã Tuyên Quang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2403, 60, NULL, 'Xã Tân Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2404, 60, NULL, 'Xã Vĩnh Hảo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2405, 60, NULL, 'Xã Liên Hương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2406, 60, NULL, 'Xã Tuy Phong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2407, 60, NULL, 'Xã Phan Rí Cửa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2408, 60, NULL, 'Xã Bắc Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2409, 60, NULL, 'Xã Hồng Thái', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2410, 60, NULL, 'Xã Hải Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2411, 60, NULL, 'Xã Phan Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2412, 60, NULL, 'Xã Sông Lũy', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2413, 60, NULL, 'Xã Lương Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2414, 60, NULL, 'Xã Hòa Thắng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2415, 60, NULL, 'Xã Đông Giang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2416, 60, NULL, 'Xã La Dạ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2417, 60, NULL, 'Xã Hàm Thuận Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2418, 60, NULL, 'Xã Hàm Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2419, 60, NULL, 'Xã Hồng Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2420, 60, NULL, 'Xã Hàm Liêm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2421, 60, NULL, 'Xã Hàm Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2422, 60, NULL, 'Xã Hàm Kiệm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2423, 60, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2424, 60, NULL, 'Xã Hàm Thuận Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2425, 60, NULL, 'Xã Tân Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2426, 60, NULL, 'Xã Tân Minh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2427, 60, NULL, 'Xã Hàm Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2428, 60, NULL, 'Xã Sơn Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2429, 60, NULL, 'Xã Bắc Ruộng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2430, 60, NULL, 'Xã Nghị Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2431, 60, NULL, 'Xã Đồng Kho', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2432, 60, NULL, 'Xã Tánh Linh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2433, 60, NULL, 'Xã Suối Kiết', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2434, 60, NULL, 'Xã Nam Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2435, 60, NULL, 'Xã Đức Linh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2436, 60, NULL, 'Xã Hoài Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2437, 60, NULL, 'Xã Trà Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2438, 60, NULL, 'Đặc khu Phú Quý', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2439, 60, NULL, 'Phường Bắc Gia Nghĩa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2440, 60, NULL, 'Phường Nam Gia Nghĩa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2441, 60, NULL, 'Phường Đông Gia Nghĩa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2442, 60, NULL, 'Xã Đắk Wil', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2443, 60, NULL, 'Xã Nam Dong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2444, 60, NULL, 'Xã Cư Jút', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2445, 60, NULL, 'Xã Thuận An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2446, 60, NULL, 'Xã Đức Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2447, 60, NULL, 'Xã Đắk Mil', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2448, 60, NULL, 'Xã Đắk Sắk', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2449, 60, NULL, 'Xã Nam Đà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2450, 60, NULL, 'Xã Krông Nô', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2451, 60, NULL, 'Xã Nâm Nung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2452, 60, NULL, 'Xã Quảng Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2453, 60, NULL, 'Xã Đắk song', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2454, 60, NULL, 'Xã Đức An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2455, 60, NULL, 'Xã Thuận Hạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2456, 60, NULL, 'Xã Trường Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2457, 60, NULL, 'Xã Tà Đùng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2458, 60, NULL, 'Xã Quảng Khê', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2459, 60, NULL, 'Xã Quảng Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2460, 60, NULL, 'Xã Tuy Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2461, 60, NULL, 'Xã Kiến Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2462, 60, NULL, 'Xã Nhân Cơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2463, 60, NULL, 'Xã Quảng Tín', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2464, 60, NULL, 'Xã Ninh Gia', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2465, 60, NULL, 'Xã Quảng Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2466, 60, NULL, 'Xã Quảng Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2467, 60, NULL, 'Xã Quảng Trực', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2468, 61, NULL, 'Phường Biên Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2469, 61, NULL, 'Phường Trấn Biên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2470, 61, NULL, 'Phường Tam Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2471, 61, NULL, 'Phường Long Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2472, 61, NULL, 'Phường Trảng Dài', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2473, 61, NULL, 'Phường Hố Nai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2474, 61, NULL, 'Phường Long Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2475, 61, NULL, 'Xã Đại Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2476, 61, NULL, 'Xã Nhơn Trạch', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2477, 61, NULL, 'Xã Phước An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2478, 61, NULL, 'Xã Phước Thái', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2479, 61, NULL, 'Xã Long Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2480, 61, NULL, 'Xã Bình An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2481, 61, NULL, 'Xã Long Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2482, 61, NULL, 'Xã An Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2483, 61, NULL, 'Xã An Viễn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2484, 61, NULL, 'Xã Bình Minh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2485, 61, NULL, 'Xã Trảng Bom', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2486, 61, NULL, 'Xã Bàu Hàm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2487, 61, NULL, 'Xã Hưng Thịnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2488, 61, NULL, 'Xã Dầu Giây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2489, 61, NULL, 'Xã Gia Kiệm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2490, 61, NULL, 'Xã Thống Nhất', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2491, 61, NULL, 'Phường Bình Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2492, 61, NULL, 'Phường Bảo Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2493, 61, NULL, 'Phường Xuân Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2494, 61, NULL, 'Phường Long Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2495, 61, NULL, 'Phường Hàng Gòn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2496, 61, NULL, 'Xã Xuân Quế', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2497, 61, NULL, 'Xã Xuân Đường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2498, 61, NULL, 'Xã Cẩm Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2499, 61, NULL, 'Xã Sông Ray', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2500, 61, NULL, 'Xã Xuân Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2501, 61, NULL, 'Xã Xuân Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2502, 61, NULL, 'Xã Xuân Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2503, 61, NULL, 'Xã Xuân Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2504, 61, NULL, 'Xã Xuân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2505, 61, NULL, 'Xã Xuân Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2506, 61, NULL, 'Xã Xuân Bắc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2507, 61, NULL, 'Xã La Ngà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2508, 61, NULL, 'Xã Định Quán', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2509, 61, NULL, 'Xã Phú Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2510, 61, NULL, 'Xã Phú Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2511, 61, NULL, 'Xã Tà Lài', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2512, 61, NULL, 'Xã Nam Cát Tiên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2513, 61, NULL, 'Xã Tân Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2514, 61, NULL, 'Xã Phú Lâm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2515, 61, NULL, 'Xã Trị An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2516, 61, NULL, 'Xã Tân An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2517, 61, NULL, 'Phường Tân Triều', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2518, 61, NULL, 'Phường Minh Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2519, 61, NULL, 'Phường Chơn Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2520, 61, NULL, 'Xã Nha Bích', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2521, 61, NULL, 'Xã Tân Quan', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2522, 61, NULL, 'Xã Tân Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2523, 61, NULL, 'Xã Tân Khai', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2524, 61, NULL, 'Xã Minh Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2525, 61, NULL, 'Phường Bình Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2526, 61, NULL, 'Phường An Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2527, 61, NULL, 'Xã Lộc Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2528, 61, NULL, 'Xã Lộc Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2529, 61, NULL, 'Xã Lộc Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2530, 61, NULL, 'Xã Lộc Tấn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2531, 61, NULL, 'Xã Lộc Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2532, 61, NULL, 'Xã Lộc Quang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2533, 61, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2534, 61, NULL, 'Xã Thiện Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2535, 61, NULL, 'Xã Hưng Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2536, 61, NULL, 'Xã Phú Nghĩa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2537, 61, NULL, 'Xã Đa Kia', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2538, 61, NULL, 'Phường Phước Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2539, 61, NULL, 'Phường Phước Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2540, 61, NULL, 'Xã Bình Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2541, 61, NULL, 'Xã Long Hà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2542, 61, NULL, 'Xã Phú Riềng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2543, 61, NULL, 'Xã Phú Trung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2544, 61, NULL, 'Phường Đồng Xoài', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2545, 61, NULL, 'Phường Bình Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2546, 61, NULL, 'Xã Thuận Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2547, 61, NULL, 'Xã Đồng Tâm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2548, 61, NULL, 'Xã Tân Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2549, 61, NULL, 'Xã Đồng Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2550, 61, NULL, 'Xã Phước Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2551, 61, NULL, 'Xã Nghĩa Trung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2552, 61, NULL, 'Xã Bù Đăng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2553, 61, NULL, 'Xã Thọ Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2554, 61, NULL, 'Xã Đak Nhau', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2555, 61, NULL, 'Xã Bom Bo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2556, 61, NULL, 'Phường Tam Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2557, 61, NULL, 'Phường Phước Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2558, 61, NULL, 'Xã Thanh Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2559, 61, NULL, 'Xã Đak Lua', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2560, 61, NULL, 'Xã Phú Lý', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2561, 61, NULL, 'Xã Bù Gia Mập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2562, 61, NULL, 'Xã Đăk Ơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2563, 62, NULL, 'Phường Vũng Tàu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2564, 62, NULL, 'Phường Tam Thắng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2565, 62, NULL, 'Phường  Rạch Dừa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2566, 62, NULL, 'Phường Phước Thắng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2567, 62, NULL, 'Phường Bà Rịa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2568, 62, NULL, 'Phường Long Hương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2569, 62, NULL, 'Phường Phú Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2570, 62, NULL, 'Phường Tam Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2571, 62, NULL, 'Phường Tân Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2572, 62, NULL, 'Phường Tân Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2573, 62, NULL, 'Phường Tân Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2574, 62, NULL, 'Xã Châu Pha', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2575, 62, NULL, 'Xã Ngãi Giao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2576, 62, NULL, 'Xã Bình Giã', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2577, 62, NULL, 'Xã Kim Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2578, 62, NULL, 'Xã Châu Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2579, 62, NULL, 'Xã Xuân Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2580, 62, NULL, 'Xã Nghĩa Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2581, 62, NULL, 'Xã Hồ Tràm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2582, 62, NULL, 'Xã Xuyên Mộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2583, 62, NULL, 'Xã Hòa Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2584, 62, NULL, 'Xã Bàu Lâm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2585, 62, NULL, 'Xã Phước Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2586, 62, NULL, 'Xã Long Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2587, 62, NULL, 'Xã Đất Đỏ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2588, 62, NULL, 'Xã Long Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2589, 62, NULL, 'Đặc khu Côn Đảo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2590, 62, NULL, 'Phường Đông Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2591, 62, NULL, 'Phường Dĩ An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2592, 62, NULL, 'Phường Tân Đông Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2593, 62, NULL, 'Phường Thuận An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2594, 62, NULL, 'Phường Thuận Giao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2595, 62, NULL, 'Phường Bình Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2596, 62, NULL, 'Phường Lái Thiêu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2597, 62, NULL, 'Phường An Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2598, 62, NULL, 'Phường Bình Dương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2599, 62, NULL, 'Phường Chánh Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2600, 62, NULL, 'Phường Thủ Dầu Một', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2601, 62, NULL, 'Phường Phú Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2602, 62, NULL, 'Phường Vĩnh Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2603, 62, NULL, 'Phường Bình Cơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2604, 62, NULL, 'Phường Tân Uyên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2605, 62, NULL, 'Phường Tân Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2606, 62, NULL, 'Phường Tân Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2607, 62, NULL, 'Phường Hòa Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2608, 62, NULL, 'Phường Phú An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2609, 62, NULL, 'Phường Tây Nam', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2610, 62, NULL, 'Phường Long Nguyên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2611, 62, NULL, 'Phường Bến Cát', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2612, 62, NULL, 'Phường Chánh Phú Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2613, 62, NULL, 'Xã Bắc Tân Uyên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2614, 62, NULL, 'Xã Thường Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2615, 62, NULL, 'Xã An Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2616, 62, NULL, 'Xã Phước Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2617, 62, NULL, 'Xã Phước Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2618, 62, NULL, 'Xã Phú Giáo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2619, 62, NULL, 'Xã Trừ Văn Thố', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2620, 62, NULL, 'Xã Bàu Bàng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2621, 62, NULL, 'Xã Minh Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2622, 62, NULL, 'Xã Long Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2623, 62, NULL, 'Xã Dầu Tiếng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2624, 62, NULL, 'Xã Thanh An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2625, 62, NULL, 'Phường Sài Gòn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2626, 62, NULL, 'Phường Tân Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2627, 62, NULL, 'Phường Bến Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2628, 62, NULL, 'Phường Cầu Ông Lãnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2629, 62, NULL, 'Phường Bàn Cờ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2630, 62, NULL, 'Phường Xuân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2631, 62, NULL, 'Phường Nhiêu Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2632, 62, NULL, 'Phường Xóm Chiếu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2633, 62, NULL, 'Phường Khánh Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2634, 62, NULL, 'Phường Vĩnh Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2635, 62, NULL, 'Phường Chợ Quán', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2636, 62, NULL, 'Phường An Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2637, 62, NULL, 'Phường Chợ Lớn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2638, 62, NULL, 'Phường Bình Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2639, 62, NULL, 'Phường Bình Tiên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2640, 62, NULL, 'Phường Bình Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2641, 62, NULL, 'Phường Phú Lâm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2642, 62, NULL, 'Phường Tân Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2643, 62, NULL, 'Phường Phú Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2644, 62, NULL, 'Phường Tân Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2645, 62, NULL, 'Phường Tân Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2646, 62, NULL, 'Phường Chánh Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2647, 62, NULL, 'Phường Phú Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2648, 62, NULL, 'Phường Bình Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2649, 62, NULL, 'Phường Diên Hồng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2650, 62, NULL, 'Phường Vườn Lài', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2651, 62, NULL, 'Phường Hòa Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2652, 62, NULL, 'Phường Minh Phụng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2653, 62, NULL, 'Phường Bình Thới', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2654, 62, NULL, 'Phường Hòa Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2655, 62, NULL, 'Phường Phú Thọ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2656, 62, NULL, 'Phường Đông Hưng Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2657, 62, NULL, 'Phường Trung Mỹ Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2658, 62, NULL, 'Phường Tân Thới Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2659, 62, NULL, 'Phường Thới An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2660, 62, NULL, 'Phường An Phú Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2661, 62, NULL, 'Phường An Lạc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2662, 62, NULL, 'Phường Tân Tạo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2663, 62, NULL, 'Phường Bình Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2664, 62, NULL, 'Phường Bình Trị Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2665, 62, NULL, 'Phường Bình Hưng Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2666, 62, NULL, 'Phường Gia Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2667, 62, NULL, 'Phường Bình Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2668, 62, NULL, 'Phường Bình Lợi Trung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2669, 62, NULL, 'Phường Thạnh Mỹ Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2670, 62, NULL, 'Phường Bình Quới', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2671, 62, NULL, 'Phường Hạnh Thông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2672, 62, NULL, 'Phường  An Nhơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2673, 62, NULL, 'Phường Gò Vấp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2674, 62, NULL, 'Phường An Hội Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2675, 62, NULL, 'Phường Thông Tây Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2676, 62, NULL, 'Phường An Hội Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2677, 62, NULL, 'Phường Đức Nhuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2678, 62, NULL, 'Phường Cầu Kiệu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2679, 62, NULL, 'Phường Phú Nhuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2680, 62, NULL, 'Phường Tân Sơn Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2681, 62, NULL, 'Phường Tân Sơn Nhất', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2682, 62, NULL, 'Phường Tân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2683, 62, NULL, 'Phường Bảy Hiền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2684, 62, NULL, 'Phường Tân Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2685, 62, NULL, 'Phường Tân Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2686, 62, NULL, 'Phường Tây Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2687, 62, NULL, 'Phường Tân Sơn Nhì', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2688, 62, NULL, 'Phường Phú Thọ Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2689, 62, NULL, 'Phường Tân Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2690, 62, NULL, 'Phường Phú Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2691, 62, NULL, 'Phường Hiệp Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2692, 62, NULL, 'Phường Thủ Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2693, 62, NULL, 'Phường Tam Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2694, 62, NULL, 'Phường Linh Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2695, 62, NULL, 'Phường Tăng Nhơn Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2696, 62, NULL, 'Phường Long Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2697, 62, NULL, 'Phường Long Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2698, 62, NULL, 'Phường Long Trường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2699, 62, NULL, 'Phường Cát Lái', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2700, 62, NULL, 'Phường Bình Trưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2701, 62, NULL, 'Phường Phước Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2702, 62, NULL, 'Phường An Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2703, 62, NULL, 'Xã Vĩnh Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2704, 62, NULL, 'Xã Tân Vĩnh Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2705, 62, NULL, 'Xã Bình Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2706, 62, NULL, 'Xã Tân Nhựt', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2707, 62, NULL, 'Xã Bình Chánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2708, 62, NULL, 'Xã Hưng Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2709, 62, NULL, 'Xã Bình Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2710, 62, NULL, 'Xã Bình Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2711, 62, NULL, 'Xã An Thới Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2712, 62, NULL, 'Xã Cần Giờ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2713, 62, NULL, 'Xã Củ Chi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2714, 62, NULL, 'Xã Tân An Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2715, 62, NULL, 'Xã Thái Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2716, 62, NULL, 'Xã An Nhơn Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2717, 62, NULL, 'Xã Nhuận Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2718, 62, NULL, 'Xã Phú Hòa Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2719, 62, NULL, 'Xã Bình Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2720, 62, NULL, 'Xã Đông Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2721, 62, NULL, 'Xã Hóc Môn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2722, 62, NULL, 'Xã Xuân Thới Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2723, 62, NULL, 'Xã Bà Điểm', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2724, 62, NULL, 'Xã Nhà Bè', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2725, 62, NULL, 'Xã Hiệp Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2726, 62, NULL, 'Xã Long Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2727, 62, NULL, 'Xã Hòa Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2728, 62, NULL, 'Xã Bình Châu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2729, 62, NULL, 'Phường Thới Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2730, 62, NULL, 'Xã Thạnh An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2731, 63, NULL, 'Xã Hưng Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2732, 63, NULL, 'Xã Vĩnh Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2733, 63, NULL, 'Xã Tân Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2734, 63, NULL, 'Xã Vĩnh Châu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2735, 63, NULL, 'Xã Tuyên Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2736, 63, NULL, 'Xã Vĩnh Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2737, 63, NULL, 'Xã Khánh Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2738, 63, NULL, 'Xã Tuyên Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2739, 63, NULL, 'Xã Bình Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2740, 63, NULL, 'Phường Kiến Tường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2741, 63, NULL, 'Xã Bình Hoà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2742, 63, NULL, 'Xã Mộc Hoá', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2743, 63, NULL, 'Xã Hậu Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2744, 63, NULL, 'Xã Nhơn Hòa Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2745, 63, NULL, 'Xã Nhơn Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2746, 63, NULL, 'Xã Tân Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2747, 63, NULL, 'Xã Bình Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2748, 63, NULL, 'Xã Thạnh Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2749, 63, NULL, 'Xã Thạnh Hóa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2750, 63, NULL, 'Xã Tân Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2751, 63, NULL, 'Xã Thủ Thừa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2752, 63, NULL, 'Xã Mỹ An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2753, 63, NULL, 'Xã Mỹ Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2754, 63, NULL, 'Xã Tân Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2755, 63, NULL, 'Xã Mỹ Quý', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2756, 63, NULL, 'Xã Đông Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2757, 63, NULL, 'Xã Đức Huệ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2758, 63, NULL, 'Xã An Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2759, 63, NULL, 'Xã Hiệp Hoà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2760, 63, NULL, 'Xã Hậu Nghĩa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2761, 63, NULL, 'Xã Hoà Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2762, 63, NULL, 'Xã Đức Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2763, 63, NULL, 'Xã Mỹ Hạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2764, 63, NULL, 'Xã Đức Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2765, 63, NULL, 'Xã Thạnh Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2766, 63, NULL, 'Xã Bình Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2767, 63, NULL, 'Xã Lương Hoà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2768, 63, NULL, 'Xã Bến Lức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2769, 63, NULL, 'Xã Mỹ Yên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2770, 63, NULL, 'Xã Long Cang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2771, 63, NULL, 'Xã Rạch Kiến', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2772, 63, NULL, 'Xã Mỹ Lệ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2773, 63, NULL, 'Xã Tân Lân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2774, 63, NULL, 'Xã Cần Đước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2775, 63, NULL, 'Xã Long Hựu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2776, 63, NULL, 'Xã Phước Lý', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2777, 63, NULL, 'Xã Mỹ Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2778, 63, NULL, 'Xã Cần Giuộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2779, 63, NULL, 'Xã Phước Vĩnh Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2780, 63, NULL, 'Xã Tân Tập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2781, 63, NULL, 'Xã Vàm Cỏ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2782, 63, NULL, 'Xã Tân Trụ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2783, 63, NULL, 'Xã Nhựt Tảo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2784, 63, NULL, 'Xã Thuận Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2785, 63, NULL, 'Xã An Lục Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2786, 63, NULL, 'Xã Tầm Vu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2787, 63, NULL, 'Xã Vĩnh Công', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2788, 63, NULL, 'Phường Long An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2789, 63, NULL, 'Phường Tân An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2790, 63, NULL, 'Phường Khánh Hậu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2791, 63, NULL, 'Phường Tân Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2792, 63, NULL, 'Phường Bình Minh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2793, 63, NULL, 'Phường Ninh Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2794, 63, NULL, 'Phường Long Hoa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2795, 63, NULL, 'Phường Hoà Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2796, 63, NULL, 'Phường Thanh Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2797, 63, NULL, 'Phường Trảng Bàng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2798, 63, NULL, 'Phường An Tịnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2799, 63, NULL, 'Phường Gò Dầu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2800, 63, NULL, 'Phường Gia Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2801, 63, NULL, 'Xã Hưng Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2802, 63, NULL, 'Xã Phước Chỉ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2803, 63, NULL, 'Xã Thạnh Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2804, 63, NULL, 'Xã Phước Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2805, 63, NULL, 'Xã Truông Mít', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2806, 63, NULL, 'Xã Lộc Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2807, 63, NULL, 'Xã Cầu Khởi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2808, 63, NULL, 'Xã Dương Minh Châu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2809, 63, NULL, 'Xã Tân Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2810, 63, NULL, 'Xã Tân Châu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2811, 63, NULL, 'Xã Tân Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2812, 63, NULL, 'Xã Tân Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2813, 63, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2814, 63, NULL, 'Xã Tân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2815, 63, NULL, 'Xã Tân Lập', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2816, 63, NULL, 'Xã Tân Biên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2817, 63, NULL, 'Xã Thạnh Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2818, 63, NULL, 'Xã Trà Vong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2819, 63, NULL, 'Xã Phước Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2820, 63, NULL, 'Xã Hoà Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2821, 63, NULL, 'Xã Ninh Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2822, 63, NULL, 'Xã Châu Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2823, 63, NULL, 'Xã Hảo Đước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2824, 63, NULL, 'Xã Long Chữ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2825, 63, NULL, 'Xã Long Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2826, 63, NULL, 'Xã Bến Cầu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2827, 64, NULL, 'Phường Mỹ Tho', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2828, 64, NULL, 'Phường Đạo Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2829, 64, NULL, 'Phường Mỹ Phong', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2830, 64, NULL, 'Phường Thới Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2831, 64, NULL, 'Phường Trung An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2832, 64, NULL, 'Phường Gò Công', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2833, 64, NULL, 'Phường Long Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2834, 64, NULL, 'Phường Sơn Qui', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2835, 64, NULL, 'Phường Bình Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2836, 64, NULL, 'Phường Mỹ Phước Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2837, 64, NULL, 'Phường Thanh Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2838, 64, NULL, 'Phường Cai Lậy', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2839, 64, NULL, 'Phường Nhị Quý', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2840, 64, NULL, 'Xã Tân Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2841, 64, NULL, 'Xã Thanh Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2842, 64, NULL, 'Xã An Hữu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2843, 64, NULL, 'Xã Mỹ Lợi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2844, 64, NULL, 'Xã Mỹ Đức Tây', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2845, 64, NULL, 'Xã Mỹ Thiện', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2846, 64, NULL, 'Xã Hậu Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2847, 64, NULL, 'Xã Hội Cư', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2848, 64, NULL, 'Xã Cái Bè', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2849, 64, NULL, 'Xã Bình Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2850, 64, NULL, 'Xã Hiệp Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2851, 64, NULL, 'Xã Ngũ Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2852, 64, NULL, 'Xã Long Tiên', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2853, 64, NULL, 'Xã Mỹ Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2854, 64, NULL, 'Xã Thạnh Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2855, 64, NULL, 'Xã Tân Phước 1', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2856, 64, NULL, 'Xã Tân Phước 2', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2857, 64, NULL, 'Xã Tân Phước 3', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2858, 64, NULL, 'Xã Hưng Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2859, 64, NULL, 'Xã Tân Hương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2860, 64, NULL, 'Xã Châu Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2861, 64, NULL, 'Xã Long Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2862, 64, NULL, 'Xã Long Định', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2863, 64, NULL, 'Xã Vĩnh Kim', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2864, 64, NULL, 'Xã Kim Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2865, 64, NULL, 'Xã Bình Trưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2866, 64, NULL, 'Xã Mỹ Tịnh An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2867, 64, NULL, 'Xã Lương Hòa Lạc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50');
INSERT INTO `wards` (`id`, `province_id`, `code`, `name`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(2868, 64, NULL, 'Xã Tân Thuận Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2869, 64, NULL, 'Xã Chợ Gạo', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2870, 64, NULL, 'Xã An Thạnh Thủy', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2871, 64, NULL, 'Xã Bình Ninh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2872, 64, NULL, 'Xã Vĩnh Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2873, 64, NULL, 'Xã Đồng Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2874, 64, NULL, 'Xã Phú Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2875, 64, NULL, 'Xã Long Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2876, 64, NULL, 'Xã Vĩnh Hựu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2877, 64, NULL, 'Xã Gò Công Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2878, 64, NULL, 'Xã Tân Điền', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2879, 64, NULL, 'Xã Tân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2880, 64, NULL, 'Xã Tân Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2881, 64, NULL, 'Xã Gia Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2882, 64, NULL, 'Xã Tân Thới', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2883, 64, NULL, 'Xã Tân Phú Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2884, 64, NULL, 'Xã Tân Hồng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2885, 64, NULL, 'Xã Tân Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2886, 64, NULL, 'Xã Tân Hộ Cơ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2887, 64, NULL, 'Xã An Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2888, 64, NULL, 'Phường An Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2889, 64, NULL, 'Phường Hồng Ngự', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2890, 64, NULL, 'Phường Thường Lạc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2891, 64, NULL, 'Xã Thường Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2892, 64, NULL, 'Xã Long Khánh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2893, 64, NULL, 'Xã Long Phú Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2894, 64, NULL, 'Xã An Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2895, 64, NULL, 'Xã Tam Nông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2896, 64, NULL, 'Xã Phú Thọ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2897, 64, NULL, 'Xã Tràm Chim', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2898, 64, NULL, 'Xã Phú Cường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2899, 64, NULL, 'Xã An Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2900, 64, NULL, 'Xã Thanh Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2901, 64, NULL, 'Xã Tân Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2902, 64, NULL, 'Xã Bình Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2903, 64, NULL, 'Xã Tân Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2904, 64, NULL, 'Xã Tháp Mười', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2905, 64, NULL, 'Xã Thanh Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2906, 64, NULL, 'Xã Mỹ Quí', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2907, 64, NULL, 'Xã Đốc Binh Kiều', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2908, 64, NULL, 'Xã Trường Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2909, 64, NULL, 'Xã Phương Thịnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2910, 64, NULL, 'Xã Phong Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2911, 64, NULL, 'Xã Ba Sao', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2912, 64, NULL, 'Xã Mỹ Thọ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2913, 64, NULL, 'Xã Bình Hàng Trung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2914, 64, NULL, 'Xã Mỹ Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2915, 64, NULL, 'Phường Cao Lãnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2916, 64, NULL, 'Phường Mỹ Ngãi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2917, 64, NULL, 'Phường Mỹ Trà', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2918, 64, NULL, 'Xã Mỹ An Hưng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2919, 64, NULL, 'Xã Tân Khánh Trung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2920, 64, NULL, 'Xã Lấp Vò', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2921, 64, NULL, 'Xã Lai Vung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2922, 64, NULL, 'Xã Hòa Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2923, 64, NULL, 'Xã Phong Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2924, 64, NULL, 'Phường Sa Đéc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2925, 64, NULL, 'Xã Tân Dương', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2926, 64, NULL, 'Xã Phú Hựu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2927, 64, NULL, 'Xã Tân Nhuận Đông', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2928, 64, NULL, 'Xã Tân Phú Trung', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2929, 65, NULL, 'Xã Cái Nhum', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2930, 65, NULL, 'Xã Tân Long Hội', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2931, 65, NULL, 'Xã Nhơn Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2932, 65, NULL, 'Xã Bình Phước', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2933, 65, NULL, 'Xã An Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2934, 65, NULL, 'Xã Long Hồ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2935, 65, NULL, 'Xã Phú Quới', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2936, 65, NULL, 'Phường Thanh Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2937, 65, NULL, 'Phường Long Châu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2938, 65, NULL, 'Phường Phước Hậu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2939, 65, NULL, 'Phường Tân Hạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2940, 65, NULL, 'Phường Tân Ngãi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2941, 65, NULL, 'Xã Quới Thiện', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2942, 65, NULL, 'Xã Trung Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2943, 65, NULL, 'Xã Trung Ngãi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2944, 65, NULL, 'Xã Quới An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2945, 65, NULL, 'Xã Trung Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2946, 65, NULL, 'Xã Hiếu Phụng', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2947, 65, NULL, 'Xã Hiếu Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2948, 65, NULL, 'Xã Lục Sỹ Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2949, 65, NULL, 'Xã Trà Ôn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2950, 65, NULL, 'Xã Trà Côn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2951, 65, NULL, 'Xã Vĩnh Xuân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2952, 65, NULL, 'Xã Hòa Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2953, 65, NULL, 'Xã Hòa Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2954, 65, NULL, 'Xã Tam Bình', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2955, 65, NULL, 'Xã Ngãi Tứ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2956, 65, NULL, 'Xã Song Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2957, 65, NULL, 'Xã Cái Ngang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2958, 65, NULL, 'Xã Tân Quới', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2959, 65, NULL, 'Xã Tân Lược', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2960, 65, NULL, 'Xã Mỹ Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2961, 65, NULL, 'Phường Bình Minh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2962, 65, NULL, 'Phường Cái Vồn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2963, 65, NULL, 'Phường Đông Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2964, 65, NULL, 'Phường Long Đức', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2965, 65, NULL, 'Phường Trà Vinh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2966, 65, NULL, 'Phường Nguyệt Hóa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2967, 65, NULL, 'Phường Hòa Thuận', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2968, 65, NULL, 'Xã An Trường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2969, 65, NULL, 'Xã Tân An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2970, 65, NULL, 'Xã Càng Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2971, 65, NULL, 'Xã Nhị Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2972, 65, NULL, 'Xã Bình Phú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2973, 65, NULL, 'Xã Song Lộc', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2974, 65, NULL, 'Xã Châu Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2975, 65, NULL, 'Xã Hưng Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2976, 65, NULL, 'Xã Hòa Minh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2977, 65, NULL, 'Xã Long Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2978, 65, NULL, 'Xã Cầu Kè', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2979, 65, NULL, 'Xã Phong Thạnh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2980, 65, NULL, 'Xã An Phú Tân', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2981, 65, NULL, 'Xã Tam Ngãi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2982, 65, NULL, 'Xã Tân Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2983, 65, NULL, 'Xã Hùng Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2984, 65, NULL, 'Xã Tiểu Cần', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2985, 65, NULL, 'Xã Tập Ngãi', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2986, 65, NULL, 'Xã Mỹ Long', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2987, 65, NULL, 'Xã Vinh Kim', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2988, 65, NULL, 'Xã Cầu Ngang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2989, 65, NULL, 'Xã Nhị Trường', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2990, 65, NULL, 'Xã Hiệp Mỹ', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2991, 65, NULL, 'Xã Lưu Nghiệp Anh', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2992, 65, NULL, 'Xã Đại An', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2993, 65, NULL, 'Xã Hàm Giang', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2994, 65, NULL, 'Xã Trà Cú', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2995, 65, NULL, 'Xã Long Hiệp', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2996, 65, NULL, 'Xã Tập Sơn', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2997, 65, NULL, 'Phường Duyên Hải', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2998, 65, NULL, 'Phường Trường Long Hòa', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(2999, 65, NULL, 'Xã Long Hữu', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(3000, 65, NULL, 'Xã Long Thành', NULL, 1, '2025-11-28 13:58:50', '2025-11-28 13:58:50'),
(3001, 65, NULL, 'Xã Đông Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3002, 65, NULL, 'Xã Long Vĩnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3003, 65, NULL, 'Xã Đôn Châu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3004, 65, NULL, 'Xã Ngũ Lạc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3005, 65, NULL, 'Phường An Hội', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3006, 65, NULL, 'Phường Phú Khương', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3007, 65, NULL, 'Phường Bến Tre', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3008, 65, NULL, 'Phường Sơn Đông', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3009, 65, NULL, 'Phường Phú Tân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3010, 65, NULL, 'Xã Phú Túc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3011, 65, NULL, 'Xã Giao Long', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3012, 65, NULL, 'Xã Tiên Thủy', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3013, 65, NULL, 'Xã Tân Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3014, 65, NULL, 'Xã Phú Phụng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3015, 65, NULL, 'Xã Chợ Lách', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3016, 65, NULL, 'Xã Vĩnh Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3017, 65, NULL, 'Xã Hưng Khánh Trung', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3018, 65, NULL, 'Xã Phước Mỹ Trung', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3019, 65, NULL, 'Xã Tân Thành Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3020, 65, NULL, 'Xã Nhuận Phú Tân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3021, 65, NULL, 'Xã Đồng Khởi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3022, 65, NULL, 'Xã Mỏ Cày', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3023, 65, NULL, 'Xã Thành Thới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3024, 65, NULL, 'Xã An Định', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3025, 65, NULL, 'Xã Hương Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3026, 65, NULL, 'Xã Đại Điền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3027, 65, NULL, 'Xã Quới Điền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3028, 65, NULL, 'Xã Thạnh Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3029, 65, NULL, 'Xã An Qui', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3030, 65, NULL, 'Xã Thạnh Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3031, 65, NULL, 'Xã Thạnh Phong', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3032, 65, NULL, 'Xã Tân Thủy', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3033, 65, NULL, 'Xã Bảo Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3034, 65, NULL, 'Xã Ba Tri', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3035, 65, NULL, 'Xã Tân Xuân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3036, 65, NULL, 'Xã Mỹ Chánh Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3037, 65, NULL, 'Xã An Ngãi Trung', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3038, 65, NULL, 'Xã An Hiệp', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3039, 65, NULL, 'Xã Hưng Nhượng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3040, 65, NULL, 'Xã Giồng Trôm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3041, 65, NULL, 'Xã Tân Hào', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3042, 65, NULL, 'Xã Phước Long', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3043, 65, NULL, 'Xã Lương Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3044, 65, NULL, 'Xã Châu Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3045, 65, NULL, 'Xã Lương Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3046, 65, NULL, 'Xã Thới Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3047, 65, NULL, 'Xã Thạnh Phước', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3048, 65, NULL, 'Xã Bình Đại', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3049, 65, NULL, 'Xã Thạnh Trị', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3050, 65, NULL, 'Xã Lộc Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3051, 65, NULL, 'Xã Châu Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3052, 65, NULL, 'Xã Phú Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3053, 66, NULL, 'Xã Mỹ Hòa Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3054, 66, NULL, 'Phường Long Xuyên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3055, 66, NULL, 'Phường Bình Đức', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3056, 66, NULL, 'Phường Mỹ Thới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3057, 66, NULL, 'Phường Châu Đốc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3058, 66, NULL, 'Phường Vĩnh Tế', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3059, 66, NULL, 'Xã An Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3060, 66, NULL, 'Xã Vĩnh Hậu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3061, 66, NULL, 'Xã Nhơn Hội', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3062, 66, NULL, 'Xã Khánh Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3063, 66, NULL, 'Xã Phú Hữu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3064, 66, NULL, 'Xã Tân An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3065, 66, NULL, 'Xã Châu Phong', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3066, 66, NULL, 'Xã Vĩnh Xương', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3067, 66, NULL, 'Phường Tân Châu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3068, 66, NULL, 'Phường Long Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3069, 66, NULL, 'Xã Phú Tân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3070, 66, NULL, 'Xã Phú An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3071, 66, NULL, 'Xã Bình Thạnh Đông', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3072, 66, NULL, 'Xã Chợ Vàm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3073, 66, NULL, 'Xã Hòa Lạc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3074, 66, NULL, 'Xã Phú Lâm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3075, 66, NULL, 'Xã Châu Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3076, 66, NULL, 'Xã Mỹ Đức', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3077, 66, NULL, 'Xã Vĩnh Thạnh Trung', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3078, 66, NULL, 'Xã Bình Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3079, 66, NULL, 'Xã Thạnh Mỹ Tây', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3080, 66, NULL, 'Xã An Cư', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3081, 66, NULL, 'Xã Núi Cấm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3082, 66, NULL, 'Phường Tịnh Biên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3083, 66, NULL, 'Phường Thới Sơn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3084, 66, NULL, 'Phường Chi Lăng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3085, 66, NULL, 'Xã Ba Chúc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3086, 66, NULL, 'Xã Tri Tôn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3087, 66, NULL, 'Xã Ô Lâm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3088, 66, NULL, 'Xã Cô Tô', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3089, 66, NULL, 'Xã Vĩnh Gia', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3090, 66, NULL, 'Xã An Châu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3091, 66, NULL, 'Xã Bình Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3092, 66, NULL, 'Xã Cần Đăng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3093, 66, NULL, 'Xã Vĩnh Hanh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3094, 66, NULL, 'Xã Vĩnh An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3095, 66, NULL, 'Xã Chợ Mới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3096, 66, NULL, 'Xã Cù Lao Giêng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3097, 66, NULL, 'Xã Hội An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3098, 66, NULL, 'Xã Long Điền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3099, 66, NULL, 'Xã Nhơn Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3100, 66, NULL, 'Xã Long Kiến', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3101, 66, NULL, 'Xã Thoại Sơn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3102, 66, NULL, 'Xã Óc Eo', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3103, 66, NULL, 'Xã Định Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3104, 66, NULL, 'Xã Phú Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3105, 66, NULL, 'Xã Vĩnh Trạch', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3106, 66, NULL, 'Xã Tây Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3107, 66, NULL, 'Xã Vĩnh Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3108, 66, NULL, 'Xã Vĩnh Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3109, 66, NULL, 'Xã Vĩnh Phong', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3110, 66, NULL, 'Xã Vĩnh Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3111, 66, NULL, 'Xã U Minh Thượng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3112, 66, NULL, 'Xã Đông Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3113, 66, NULL, 'Xã Tân Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3114, 66, NULL, 'Xã Đông Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3115, 66, NULL, 'Xã An Minh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3116, 66, NULL, 'Xã Vân Khánh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3117, 66, NULL, 'Xã Tây Yên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3118, 66, NULL, 'Xã Đông Thái', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3119, 66, NULL, 'Xã An Biên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3120, 66, NULL, 'Xã Định Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3121, 66, NULL, 'Xã Gò Quao', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3122, 66, NULL, 'Xã Vĩnh Hòa Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3123, 66, NULL, 'Xã Vĩnh Tuy', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3124, 66, NULL, 'Xã Giồng Riềng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3125, 66, NULL, 'Xã Thạnh Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3126, 66, NULL, 'Xã Long Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3127, 66, NULL, 'Xã Hòa Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3128, 66, NULL, 'Xã Ngọc Chúc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3129, 66, NULL, 'Xã Hòa Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3130, 66, NULL, 'Xã Tân Hội', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3131, 66, NULL, 'Xã Tân Hiệp', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3132, 66, NULL, 'Xã Thạnh Đông', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3133, 66, NULL, 'Xã Thạnh Lộc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3134, 66, NULL, 'Xã Châu Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3135, 66, NULL, 'Xã Bình An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3136, 66, NULL, 'Xã Hòn Đất', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3137, 66, NULL, 'Xã Sơn Kiên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3138, 66, NULL, 'Xã Mỹ Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3139, 66, NULL, 'Xã Bình Sơn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3140, 66, NULL, 'Xã Bình Giang', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3141, 66, NULL, 'Xã Giang Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3142, 66, NULL, 'Xã Vĩnh Điều', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3143, 66, NULL, 'Xã Hòa Điền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3144, 66, NULL, 'Xã Kiên Lương', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3145, 66, NULL, 'Xã Sơn Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3146, 66, NULL, 'Xã Hòn Nghệ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3147, 66, NULL, 'Đặc khu Kiên Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3148, 66, NULL, 'Phường Vĩnh Thông', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3149, 66, NULL, 'Phường Rạch Giá', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3150, 66, NULL, 'Phường Hà Tiên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3151, 66, NULL, 'Phường Tô Châu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3152, 66, NULL, 'Xã Tiên Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3153, 66, NULL, 'Đặc khu Phú Quốc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3154, 66, NULL, 'Đặc khu Thổ Châu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3155, 67, NULL, 'Phường Ninh Kiều', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3156, 67, NULL, 'Phường Cái Khế', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3157, 67, NULL, 'Phường Tân An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3158, 67, NULL, 'Phường An Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3159, 67, NULL, 'Phường Thới An Đông', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3160, 67, NULL, 'Phường Bình Thủy', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3161, 67, NULL, 'Phường Long Tuyền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3162, 67, NULL, 'Phường Cái Răng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3163, 67, NULL, 'Phường Hưng Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3164, 67, NULL, 'Phường Ô Môn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3165, 67, NULL, 'Phường Thới Long', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3166, 67, NULL, 'Phường Phước Thới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3167, 67, NULL, 'Phường Trung Nhứt', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3168, 67, NULL, 'Phường Thốt Nốt', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3169, 67, NULL, 'Phường Thuận Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3170, 67, NULL, 'Phường Tân Lộc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3171, 67, NULL, 'Xã Phong Điền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3172, 67, NULL, 'Xã Nhơn Ái', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3173, 67, NULL, 'Xã Trường Long', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3174, 67, NULL, 'Xã Thới Lai', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3175, 67, NULL, 'Xã Đông Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3176, 67, NULL, 'Xã Trường Xuân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3177, 67, NULL, 'Xã Trường Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3178, 67, NULL, 'Xã Cờ Đỏ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3179, 67, NULL, 'Xã Đông Hiệp', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3180, 67, NULL, 'Xã Thạnh Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3181, 67, NULL, 'Xã Thới Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3182, 67, NULL, 'Xã Trung Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3183, 67, NULL, 'Xã Vĩnh Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3184, 67, NULL, 'Xã Vĩnh Trinh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3185, 67, NULL, 'Xã Thạnh An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3186, 67, NULL, 'Xã Thạnh Quới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3187, 67, NULL, 'Xã Hỏa Lựu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3188, 67, NULL, 'Phường Vị Thanh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3189, 67, NULL, 'Phường Vị Tân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3190, 67, NULL, 'Xã Vị Thủy', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3191, 67, NULL, 'Xã Vĩnh Thuận Đông', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3192, 67, NULL, 'Xã Vị Thanh 1', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3193, 67, NULL, 'Xã Vĩnh Tường', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3194, 67, NULL, 'Xã Vĩnh Viễn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3195, 67, NULL, 'Xã Xà Phiên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3196, 67, NULL, 'Xã Lương Tâm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3197, 67, NULL, 'Phường Long Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3198, 67, NULL, 'Phường Long Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3199, 67, NULL, 'Phường Long Phú 1', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3200, 67, NULL, 'Xã Thạnh Xuân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3201, 67, NULL, 'Xã Tân Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3202, 67, NULL, 'Xã Trường Long Tây', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3203, 67, NULL, 'Xã Châu Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3204, 67, NULL, 'Xã Đông Phước', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3205, 67, NULL, 'Xã  Phú Hữu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3206, 67, NULL, 'Phường Đại Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3207, 67, NULL, 'Phường Ngã Bảy', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3208, 67, NULL, 'Xã Tân Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3209, 67, NULL, 'Xã Hòa An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3210, 67, NULL, 'Xã Phương Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3211, 67, NULL, 'Xã Tân Phước Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3212, 67, NULL, 'Xã Hiệp Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3213, 67, NULL, 'Xã Phụng Hiệp', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3214, 67, NULL, 'Xã Thạnh Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3215, 67, NULL, 'Phường Phú Lợi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3216, 67, NULL, 'Phường Sóc Trăng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3217, 67, NULL, 'Phường Mỹ Xuyên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3218, 67, NULL, 'Xã Hòa Tú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3219, 67, NULL, 'Xã Gia Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3220, 67, NULL, 'Xã Nhu Gia', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3221, 67, NULL, 'Xã Ngọc Tố', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3222, 67, NULL, 'Xã Trường Khánh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3223, 67, NULL, 'Xã Đại Ngãi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3224, 67, NULL, 'Xã Tân Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3225, 67, NULL, 'Xã Long Phú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3226, 67, NULL, 'Xã Nhơn Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3227, 67, NULL, 'Xã Phong Nẫm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3228, 67, NULL, 'Xã An Lạc Thôn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3229, 67, NULL, 'Xã Kế Sách', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3230, 67, NULL, 'Xã Thới An Hội', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3231, 67, NULL, 'Xã  Đại Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3232, 67, NULL, 'Xã Phú Tâm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3233, 67, NULL, 'Xã An Ninh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3234, 67, NULL, 'Xã Thuận Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3235, 67, NULL, 'Xã Hồ Đắc Kiện', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3236, 67, NULL, 'Xã Mỹ Tú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3237, 67, NULL, 'Xã Long Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3238, 67, NULL, 'Xã Mỹ Phước', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3239, 67, NULL, 'Xã Mỹ Hương', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3240, 67, NULL, 'Xã Vĩnh Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3241, 67, NULL, 'Xã Lai Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3242, 67, NULL, 'Phường Vĩnh Phước', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3243, 67, NULL, 'Phường Vĩnh Châu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3244, 67, NULL, 'Phường Khánh Hòa', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3245, 67, NULL, 'Xã Tân Long', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3246, 67, NULL, 'Phường Ngã Năm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3247, 67, NULL, 'Phường Mỹ Quới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3248, 67, NULL, 'Xã Phú Lộc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3249, 67, NULL, 'Xã Vĩnh Lợi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3250, 67, NULL, 'Xã Lâm Tân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3251, 67, NULL, 'Xã Thạnh Thới An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3252, 67, NULL, 'Xã Tài Văn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3253, 67, NULL, 'Xã Liêu Tú', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3254, 67, NULL, 'Xã Lịch Hội Thượng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3255, 67, NULL, 'Xã Trần Đề', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3256, 67, NULL, 'Xã An Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3257, 67, NULL, 'Xã Cù Lao Dung', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3258, 68, NULL, 'Phường An Xuyên', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3259, 68, NULL, 'Phường Lý Văn Lâm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3260, 68, NULL, 'Phường Tân Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3261, 68, NULL, 'Phường Hòa Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3262, 68, NULL, 'Xã Tân Thuận', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3263, 68, NULL, 'Xã Tân Tiến', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3264, 68, NULL, 'Xã Tạ An Khương', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3265, 68, NULL, 'Xã Trần Phán', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3266, 68, NULL, 'Xã Thanh Tùng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3267, 68, NULL, 'Xã Đầm Dơi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3268, 68, NULL, 'Xã Quách Phẩm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3269, 68, NULL, 'Xã U Minh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3270, 68, NULL, 'Xã Nguyễn Phích', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3271, 68, NULL, 'Xã Khánh Lâm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3272, 68, NULL, 'Xã Khánh An', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3273, 68, NULL, 'Xã Phan Ngọc Hiển', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3274, 68, NULL, 'Xã Đất Mũi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3275, 68, NULL, 'Xã Tân Ân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3276, 68, NULL, 'Xã Khánh Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3277, 68, NULL, 'Xã Đá Bạc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3278, 68, NULL, 'Xã Khánh Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3279, 68, NULL, 'Xã Sông Đốc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3280, 68, NULL, 'Xã Trần Văn Thời', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3281, 68, NULL, 'Xã Thới Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3282, 68, NULL, 'Xã Trí Phải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3283, 68, NULL, 'Xã Tân Lộc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3284, 68, NULL, 'Xã Hồ Thị Kỷ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3285, 68, NULL, 'Xã Biển Bạch', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3286, 68, NULL, 'Xã Đất Mới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3287, 68, NULL, 'Xã Năm Căn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3288, 68, NULL, 'Xã Tam Giang', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3289, 68, NULL, 'Xã Cái Đôi Vàm', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3290, 68, NULL, 'Xã Nguyễn Việt Khái', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3291, 68, NULL, 'Xã Phú Tân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3292, 68, NULL, 'Xã Phú Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3293, 68, NULL, 'Xã Lương Thế Trân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3294, 68, NULL, 'Xã Tân Hưng', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3295, 68, NULL, 'Xã Hưng Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3296, 68, NULL, 'Xã Cái Nước', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3297, 68, NULL, 'Phường Bạc Liêu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3298, 68, NULL, 'Phường Vĩnh Trạch', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3299, 68, NULL, 'Phường Hiệp Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3300, 68, NULL, 'Phường Giá Rai', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3301, 68, NULL, 'Phường Láng Tròn', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3302, 68, NULL, 'Xã Phong Thạnh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3303, 68, NULL, 'Xã Hồng Dân', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3304, 68, NULL, 'Xã Vĩnh Lộc', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3305, 68, NULL, 'Xã Ninh Thạnh Lợi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3306, 68, NULL, 'Xã Ninh Quới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3307, 68, NULL, 'Xã Gành Hào', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3308, 68, NULL, 'Xã Định Thành', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3309, 68, NULL, 'Xã An Trạch', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3310, 68, NULL, 'Xã Long Điền', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3311, 68, NULL, 'Xã Đông Hải', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3312, 68, NULL, 'Xã Hòa Bình', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3313, 68, NULL, 'Xã Vĩnh Mỹ', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3314, 68, NULL, 'Xã Vĩnh Hậu', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3315, 68, NULL, 'Xã Phước Long', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3316, 68, NULL, 'Xã Vĩnh Phước', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3317, 68, NULL, 'Xã Phong Hiệp', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3318, 68, NULL, 'Xã Vĩnh Thanh', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3319, 68, NULL, 'Xã Vĩnh Lợi', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3320, 68, NULL, 'Xã Hưng Hội', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13'),
(3321, 68, NULL, 'Xã Châu Thới', NULL, 1, '2025-11-28 14:00:13', '2025-11-28 14:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `work_shifts`
--

CREATE TABLE `work_shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `shift_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_start_time` time DEFAULT NULL,
  `break_end_time` time DEFAULT NULL,
  `standard_minutes` int UNSIGNED NOT NULL,
  `half_day_minutes` int UNSIGNED NOT NULL DEFAULT '240',
  `handover_break_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `grace_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `late_grace_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `early_leave_grace_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `allows_overtime` tinyint(1) NOT NULL DEFAULT '1',
  `is_overnight` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_shifts`
--

INSERT INTO `work_shifts` (`id`, `shift_code`, `shift_name`, `start_time`, `end_time`, `break_start_time`, `break_end_time`, `standard_minutes`, `half_day_minutes`, `handover_break_minutes`, `grace_minutes`, `late_grace_minutes`, `early_leave_grace_minutes`, `allows_overtime`, `is_overnight`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CA002', 'Ca hanh chinh', '08:00:00', '17:00:00', '12:00:00', '13:00:00', 480, 240, 0, 15, 15, 15, 1, 0, NULL, 1, '2026-04-17 09:50:45', '2026-04-20 04:04:54'),
(2, 'CA001', 'Ca hành chính sáng(mùa hè)', '08:00:00', '17:30:00', '12:00:00', '13:30:00', 480, 240, 30, 15, 15, 15, 1, 0, NULL, 1, '2026-04-20 02:48:31', '2026-04-29 01:49:34'),
(3, 'CA003', 'Ca hành chính sáng (mùa đông)', '08:30:00', '17:30:00', '12:00:00', '13:00:00', 480, 240, 30, 10, 10, 10, 1, 0, NULL, 1, '2026-04-20 03:55:12', '2026-04-29 01:50:48');

-- --------------------------------------------------------

--
-- Table structure for table `work_shift_overtime_rules`
--

CREATE TABLE `work_shift_overtime_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `work_shift_id` bigint UNSIGNED NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `hourly_rate` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_shift_overtime_rules`
--

INSERT INTO `work_shift_overtime_rules` (`id`, `work_shift_id`, `start_time`, `end_time`, `hourly_rate`, `created_at`, `updated_at`) VALUES
(1, 2, '17:30:00', '20:00:00', 50000.00, '2026-04-20 06:41:19', '2026-04-21 03:36:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `approval_decision_deliveries`
--
ALTER TABLE `approval_decision_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `approval_decision_deliveries_dedupe_key_unique` (`dedupe_key`),
  ADD KEY `approval_decision_deliveries_recipient_user_id_foreign` (`recipient_user_id`),
  ADD KEY `approval_decision_deliveries_triggered_by_foreign` (`triggered_by`),
  ADD KEY `approval_decision_deliveries_module_status_index` (`module`,`status`),
  ADD KEY `approval_decision_deliveries_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `approval_decision_deliveries_recipient_email_decision_index` (`recipient_email`,`decision`);

--
-- Indexes for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_requests_requested_by_foreign` (`requested_by`),
  ADD KEY `approval_requests_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `approval_requests_target_type_target_id_index` (`target_type`,`target_id`),
  ADD KEY `approval_requests_status_request_type_index` (`status`,`request_type`);

--
-- Indexes for table `approval_request_changes`
--
ALTER TABLE `approval_request_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_request_changes_approval_request_id_foreign` (`approval_request_id`);

--
-- Indexes for table `attendance_adjustments`
--
ALTER TABLE `attendance_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_adjustments_attendance_record_id_foreign` (`attendance_record_id`),
  ADD KEY `attendance_adjustments_approval_request_id_foreign` (`approval_request_id`),
  ADD KEY `attendance_adjustments_requested_by_foreign` (`requested_by`),
  ADD KEY `attendance_adjustments_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `attendance_approvals`
--
ALTER TABLE `attendance_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_approvals_attendance_record_id_foreign` (`attendance_record_id`),
  ADD KEY `attendance_approvals_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `attendance_events`
--
ALTER TABLE `attendance_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_events_attendance_record_id_foreign` (`attendance_record_id`),
  ADD KEY `attendance_events_created_by_foreign` (`created_by`),
  ADD KEY `attendance_events_employee_profile_id_event_at_index` (`employee_profile_id`,`event_at`);

--
-- Indexes for table `attendance_monthly_summaries`
--
ALTER TABLE `attendance_monthly_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `att_monthly_sum_emp_m_y_idx` (`employee_profile_id`,`month`,`year`);

--
-- Indexes for table `attendance_month_locks`
--
ALTER TABLE `attendance_month_locks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_month_locks_unique_period_department` (`month`,`year`,`department_id`),
  ADD KEY `attendance_month_locks_department_id_foreign` (`department_id`),
  ADD KEY `attendance_month_locks_locked_by_foreign` (`locked_by`),
  ADD KEY `attendance_month_locks_unlocked_by_foreign` (`unlocked_by`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_records_employee_profile_id_work_date_unique` (`employee_profile_id`,`work_date`),
  ADD KEY `attendance_records_confirmed_by_foreign` (`confirmed_by`),
  ADD KEY `attendance_records_work_date_attendance_status_index` (`work_date`,`attendance_status`),
  ADD KEY `attendance_records_work_shift_id_foreign` (`work_shift_id`),
  ADD KEY `attendance_records_rejected_by_foreign` (`rejected_by`),
  ADD KEY `attendance_records_work_date_approval_status_idx` (`work_date`,`approval_status`),
  ADD KEY `attendance_records_work_date_day_status_idx` (`work_date`,`day_status`);

--
-- Indexes for table `attendance_requests`
--
ALTER TABLE `attendance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_requests_approval_request_id_foreign` (`approval_request_id`),
  ADD KEY `attendance_requests_applied_by_foreign` (`applied_by`),
  ADD KEY `attendance_requests_emp_type_status_idx` (`employee_profile_id`,`request_type`,`status`),
  ADD KEY `attendance_requests_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `authority_levels`
--
ALTER TABLE `authority_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `authority_levels_rank_unique` (`rank`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`),
  ADD KEY `departments_manager_user_id_foreign` (`manager_user_id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_logs_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `employee_department_histories`
--
ALTER TABLE `employee_department_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_department_histories_employee_profile_id_foreign` (`employee_profile_id`),
  ADD KEY `employee_department_histories_old_department_id_foreign` (`old_department_id`),
  ADD KEY `employee_department_histories_new_department_id_foreign` (`new_department_id`),
  ADD KEY `employee_department_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_leave_balances_unique_year_type` (`employee_profile_id`,`leave_type_id`,`year`),
  ADD KEY `employee_leave_balances_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `employee_position_histories`
--
ALTER TABLE `employee_position_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_position_histories_employee_profile_id_foreign` (`employee_profile_id`),
  ADD KEY `employee_position_histories_old_position_id_foreign` (`old_position_id`),
  ADD KEY `employee_position_histories_new_position_id_foreign` (`new_position_id`),
  ADD KEY `employee_position_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_profiles_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `employee_profiles_employee_code_unique` (`employee_code`),
  ADD KEY `employee_profiles_reports_to_user_id_foreign` (`reports_to_user_id`),
  ADD KEY `employee_profiles_province_id_foreign` (`province_id`),
  ADD KEY `employee_profiles_ward_id_foreign` (`ward_id`),
  ADD KEY `employee_profiles_locked_by_foreign` (`locked_by`),
  ADD KEY `employee_profiles_department_id_employment_status_index` (`department_id`,`employment_status`),
  ADD KEY `employee_profiles_position_id_employment_status_index` (`position_id`,`employment_status`),
  ADD KEY `employee_profiles_hire_date_index` (`hire_date`),
  ADD KEY `employee_profiles_default_work_shift_id_foreign` (`default_work_shift_id`);

--
-- Indexes for table `employee_status_logs`
--
ALTER TABLE `employee_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_status_logs_employee_profile_id_foreign` (`employee_profile_id`),
  ADD KEY `employee_status_logs_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `employee_work_shift_assignments`
--
ALTER TABLE `employee_work_shift_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_work_shift_assignments_work_shift_id_foreign` (`work_shift_id`),
  ADD KEY `employee_work_shift_assignments_created_by_foreign` (`created_by`),
  ADD KEY `emp_shift_assignment_emp_effective_idx` (`employee_profile_id`,`effective_from`,`effective_to`),
  ADD KEY `emp_shift_assignment_dept_effective_idx` (`department_id`,`effective_from`,`effective_to`);

--
-- Indexes for table `export_histories`
--
ALTER TABLE `export_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `export_histories_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedback_escalations`
--
ALTER TABLE `feedback_escalations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_escalations_from_position_id_foreign` (`from_position_id`),
  ADD KEY `feedback_escalations_to_position_id_foreign` (`to_position_id`),
  ADD KEY `feedback_escalations_feedback_message_id_escalated_at_index` (`feedback_message_id`,`escalated_at`);

--
-- Indexes for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_messages_sender_id_foreign` (`sender_id`),
  ADD KEY `feedback_messages_receiver_id_foreign` (`receiver_id`),
  ADD KEY `feedback_messages_receiver_group_status_index` (`receiver_group`,`status`),
  ADD KEY `feedback_messages_replied_by_foreign` (`replied_by`),
  ADD KEY `feedback_messages_receiver_position_id_foreign` (`receiver_position_id`);

--
-- Indexes for table `feedback_replies`
--
ALTER TABLE `feedback_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_replies_replied_by_foreign` (`replied_by`),
  ADD KEY `feedback_replies_feedback_message_id_created_at_index` (`feedback_message_id`,`created_at`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `holidays_holiday_date_unique` (`holiday_date`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_balance_transactions`
--
ALTER TABLE `leave_balance_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_balance_transactions_employee_leave_balance_id_foreign` (`employee_leave_balance_id`),
  ADD KEY `leave_balance_transactions_attendance_request_id_foreign` (`attendance_request_id`),
  ADD KEY `leave_balance_transactions_created_by_foreign` (`created_by`),
  ADD KEY `leave_balance_transactions_type_created_idx` (`type`,`created_at`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_types_code_unique` (`code`);

--
-- Indexes for table `login_histories`
--
ALTER TABLE `login_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_histories_user_id_login_at_index` (`user_id`,`login_at`);

--
-- Indexes for table `login_otps`
--
ALTER TABLE `login_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_otps_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_index` (`user_id`),
  ADD KEY `notifications_category_index` (`category`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overtime_requests_approval_request_id_foreign` (`approval_request_id`),
  ADD KEY `overtime_requests_requested_by_foreign` (`requested_by`),
  ADD KEY `overtime_requests_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `overtime_requests_emp_work_date_status_idx` (`employee_profile_id`,`work_date`,`status`);

--
-- Indexes for table `password_change_otps`
--
ALTER TABLE `password_change_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_change_otps_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_reset_otps_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_periods_month_year_unique` (`month`,`year`),
  ADD KEY `payroll_periods_locked_by_foreign` (`locked_by`),
  ADD KEY `payroll_periods_unlocked_by_foreign` (`unlocked_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_name_unique` (`name`),
  ADD KEY `positions_department_id_index` (`department_id`);

--
-- Indexes for table `position_capabilities`
--
ALTER TABLE `position_capabilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_capabilities_code_unique` (`code`),
  ADD KEY `position_capabilities_module_index` (`module`);

--
-- Indexes for table `position_capability_position`
--
ALTER TABLE `position_capability_position`
  ADD PRIMARY KEY (`position_id`,`capability_id`),
  ADD KEY `position_capability_position_capability_id_foreign` (`capability_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_name_start_date_unique` (`name`,`start_date`),
  ADD KEY `projects_locked_by_foreign` (`locked_by`),
  ADD KEY `projects_created_by_foreign` (`created_by`),
  ADD KEY `projects_updated_by_foreign` (`updated_by`),
  ADD KEY `projects_status_index` (`status`);

--
-- Indexes for table `project_attachments`
--
ALTER TABLE `project_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_attachments_implementation_detail_id_foreign` (`implementation_detail_id`),
  ADD KEY `project_attachments_project_id_implementation_detail_id_index` (`project_id`,`implementation_detail_id`),
  ADD KEY `project_attachments_uploaded_by_index` (`uploaded_by`);

--
-- Indexes for table `project_detail_comments`
--
ALTER TABLE `project_detail_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdc_detail_id_idx` (`implementation_detail_id`,`id`),
  ADD KEY `pdc_project_detail_idx` (`project_id`,`implementation_detail_id`),
  ADD KEY `pdc_user_id_idx` (`user_id`);

--
-- Indexes for table `project_detail_logs`
--
ALTER TABLE `project_detail_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_detail_logs_implementation_detail_id_foreign` (`implementation_detail_id`),
  ADD KEY `project_detail_logs_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `project_implementation_details`
--
ALTER TABLE `project_implementation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_implementation_details_created_by_foreign` (`created_by`),
  ADD KEY `project_implementation_details_updated_by_foreign` (`updated_by`),
  ADD KEY `project_implementation_details_project_id_detail_status_index` (`project_id`,`detail_status`),
  ADD KEY `project_implementation_details_assigned_to_execution_date_index` (`assigned_to`,`execution_date`),
  ADD KEY `project_implementation_details_project_milestone_id_foreign` (`project_milestone_id`),
  ADD KEY `project_impl_details_project_milestone_idx` (`project_id`,`project_milestone_id`);

--
-- Indexes for table `project_implementation_subtasks`
--
ALTER TABLE `project_implementation_subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_implementation_subtasks_created_by_foreign` (`created_by`),
  ADD KEY `project_implementation_subtasks_updated_by_foreign` (`updated_by`),
  ADD KEY `proj_subtasks_detail_status_idx` (`project_implementation_detail_id`,`status`),
  ADD KEY `proj_subtasks_project_due_idx` (`project_id`,`due_date`),
  ADD KEY `proj_subtasks_assignee_start_idx` (`assigned_to`,`start_date`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_members_project_id_employee_profile_id_unique` (`project_id`,`employee_profile_id`),
  ADD KEY `project_members_employee_profile_id_foreign` (`employee_profile_id`),
  ADD KEY `project_members_project_role_id_foreign` (`project_role_id`),
  ADD KEY `project_members_project_id_is_active_index` (`project_id`,`is_active`);

--
-- Indexes for table `project_milestones`
--
ALTER TABLE `project_milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_milestones_created_by_foreign` (`created_by`),
  ADD KEY `project_milestones_updated_by_foreign` (`updated_by`),
  ADD KEY `project_milestones_project_id_status_index` (`project_id`,`status`),
  ADD KEY `project_milestones_project_id_phase_name_index` (`project_id`,`phase_name`),
  ADD KEY `project_milestones_project_id_sort_order_index` (`project_id`,`sort_order`);

--
-- Indexes for table `project_progress_histories`
--
ALTER TABLE `project_progress_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_progress_histories_project_id_foreign` (`project_id`),
  ADD KEY `project_progress_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `project_roles`
--
ALTER TABLE `project_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_roles_project_id_name_unique` (`project_id`,`name`);

--
-- Indexes for table `project_work_logs`
--
ALTER TABLE `project_work_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_work_logs_project_implementation_detail_id_foreign` (`project_implementation_detail_id`),
  ADD KEY `project_work_logs_created_by_foreign` (`created_by`),
  ADD KEY `project_work_logs_updated_by_foreign` (`updated_by`),
  ADD KEY `project_work_logs_project_date_idx` (`project_id`,`work_date`),
  ADD KEY `project_work_logs_employee_date_idx` (`employee_profile_id`,`work_date`),
  ADD KEY `project_work_logs_subtask_idx` (`project_implementation_subtask_id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provinces_code_unique` (`code`);

--
-- Indexes for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_adjustments_created_by_foreign` (`created_by`),
  ADD KEY `salary_adjustments_updated_by_foreign` (`updated_by`),
  ADD KEY `salary_adjustments_month_year_index` (`month`,`year`),
  ADD KEY `salary_adjustments_period_employee_idx` (`employee_profile_id`,`month`,`year`);

--
-- Indexes for table `salary_histories`
--
ALTER TABLE `salary_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_histories_employee_profile_id_foreign` (`employee_profile_id`),
  ADD KEY `salary_histories_approved_by_foreign` (`approved_by`),
  ADD KEY `salary_histories_requested_by_foreign` (`requested_by`);

--
-- Indexes for table `salary_snapshots`
--
ALTER TABLE `salary_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salary_snapshots_period_emp_unique` (`payroll_period_id`,`employee_profile_id`),
  ADD KEY `salary_snapshots_employee_profile_id_foreign` (`employee_profile_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_accounts_provider_provider_id_unique` (`provider`,`provider_id`),
  ADD KEY `social_accounts_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD UNIQUE KEY `users_slug_unique` (`slug`);

--
-- Indexes for table `user_position_capability_overrides`
--
ALTER TABLE `user_position_capability_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_capability_override_unique` (`user_id`,`capability_id`),
  ADD KEY `user_position_capability_overrides_capability_id_foreign` (`capability_id`),
  ADD KEY `user_position_capability_overrides_created_by_foreign` (`created_by`),
  ADD KEY `user_position_capability_overrides_expires_at_index` (`expires_at`);

--
-- Indexes for table `wards`
--
ALTER TABLE `wards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wards_code_unique` (`code`),
  ADD KEY `wards_province_id_name_index` (`province_id`,`name`),
  ADD KEY `wards_district_id_name_index` (`name`);

--
-- Indexes for table `work_shifts`
--
ALTER TABLE `work_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_shifts_shift_code_unique` (`shift_code`);

--
-- Indexes for table `work_shift_overtime_rules`
--
ALTER TABLE `work_shift_overtime_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_shift_overtime_rules_work_shift_id_unique` (`work_shift_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=672;

--
-- AUTO_INCREMENT for table `approval_decision_deliveries`
--
ALTER TABLE `approval_decision_deliveries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `approval_requests`
--
ALTER TABLE `approval_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `approval_request_changes`
--
ALTER TABLE `approval_request_changes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `attendance_adjustments`
--
ALTER TABLE `attendance_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `attendance_approvals`
--
ALTER TABLE `attendance_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attendance_events`
--
ALTER TABLE `attendance_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `attendance_monthly_summaries`
--
ALTER TABLE `attendance_monthly_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attendance_month_locks`
--
ALTER TABLE `attendance_month_locks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `attendance_requests`
--
ALTER TABLE `attendance_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `authority_levels`
--
ALTER TABLE `authority_levels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `employee_department_histories`
--
ALTER TABLE `employee_department_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `employee_position_histories`
--
ALTER TABLE `employee_position_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `employee_status_logs`
--
ALTER TABLE `employee_status_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee_work_shift_assignments`
--
ALTER TABLE `employee_work_shift_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `export_histories`
--
ALTER TABLE `export_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_escalations`
--
ALTER TABLE `feedback_escalations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback_replies`
--
ALTER TABLE `feedback_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `leave_balance_transactions`
--
ALTER TABLE `leave_balance_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_histories`
--
ALTER TABLE `login_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `login_otps`
--
ALTER TABLE `login_otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_change_otps`
--
ALTER TABLE `password_change_otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `position_capabilities`
--
ALTER TABLE `position_capabilities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=389;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_attachments`
--
ALTER TABLE `project_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_detail_comments`
--
ALTER TABLE `project_detail_comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_detail_logs`
--
ALTER TABLE `project_detail_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `project_implementation_details`
--
ALTER TABLE `project_implementation_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_implementation_subtasks`
--
ALTER TABLE `project_implementation_subtasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_milestones`
--
ALTER TABLE `project_milestones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_progress_histories`
--
ALTER TABLE `project_progress_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `project_roles`
--
ALTER TABLE `project_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_work_logs`
--
ALTER TABLE `project_work_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `salary_histories`
--
ALTER TABLE `salary_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `salary_snapshots`
--
ALTER TABLE `salary_snapshots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `social_accounts`
--
ALTER TABLE `social_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_position_capability_overrides`
--
ALTER TABLE `user_position_capability_overrides`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wards`
--
ALTER TABLE `wards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3322;

--
-- AUTO_INCREMENT for table `work_shifts`
--
ALTER TABLE `work_shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `work_shift_overtime_rules`
--
ALTER TABLE `work_shift_overtime_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_decision_deliveries`
--
ALTER TABLE `approval_decision_deliveries`
  ADD CONSTRAINT `approval_decision_deliveries_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approval_decision_deliveries_triggered_by_foreign` FOREIGN KEY (`triggered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD CONSTRAINT `approval_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_request_changes`
--
ALTER TABLE `approval_request_changes`
  ADD CONSTRAINT `approval_request_changes_approval_request_id_foreign` FOREIGN KEY (`approval_request_id`) REFERENCES `approval_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_adjustments`
--
ALTER TABLE `attendance_adjustments`
  ADD CONSTRAINT `attendance_adjustments_approval_request_id_foreign` FOREIGN KEY (`approval_request_id`) REFERENCES `approval_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_adjustments_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_adjustments_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_adjustments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance_approvals`
--
ALTER TABLE `attendance_approvals`
  ADD CONSTRAINT `attendance_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_approvals_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_events`
--
ALTER TABLE `attendance_events`
  ADD CONSTRAINT `attendance_events_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_events_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_monthly_summaries`
--
ALTER TABLE `attendance_monthly_summaries`
  ADD CONSTRAINT `attendance_monthly_summaries_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_month_locks`
--
ALTER TABLE `attendance_month_locks`
  ADD CONSTRAINT `attendance_month_locks_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_month_locks_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_month_locks_unlocked_by_foreign` FOREIGN KEY (`unlocked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_records_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_records_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_records_work_shift_id_foreign` FOREIGN KEY (`work_shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance_requests`
--
ALTER TABLE `attendance_requests`
  ADD CONSTRAINT `attendance_requests_applied_by_foreign` FOREIGN KEY (`applied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_requests_approval_request_id_foreign` FOREIGN KEY (`approval_request_id`) REFERENCES `approval_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_requests_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_manager_user_id_foreign` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_department_histories`
--
ALTER TABLE `employee_department_histories`
  ADD CONSTRAINT `employee_department_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_department_histories_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_department_histories_new_department_id_foreign` FOREIGN KEY (`new_department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_department_histories_old_department_id_foreign` FOREIGN KEY (`old_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD CONSTRAINT `employee_leave_balances_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_position_histories`
--
ALTER TABLE `employee_position_histories`
  ADD CONSTRAINT `employee_position_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_position_histories_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_position_histories_new_position_id_foreign` FOREIGN KEY (`new_position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_position_histories_old_position_id_foreign` FOREIGN KEY (`old_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD CONSTRAINT `employee_profiles_default_work_shift_id_foreign` FOREIGN KEY (`default_work_shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_profiles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_profiles_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_profiles_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_profiles_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_profiles_reports_to_user_id_foreign` FOREIGN KEY (`reports_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_profiles_ward_id_foreign` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_status_logs`
--
ALTER TABLE `employee_status_logs`
  ADD CONSTRAINT `employee_status_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_status_logs_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_work_shift_assignments`
--
ALTER TABLE `employee_work_shift_assignments`
  ADD CONSTRAINT `employee_work_shift_assignments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_work_shift_assignments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_work_shift_assignments_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_work_shift_assignments_work_shift_id_foreign` FOREIGN KEY (`work_shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `export_histories`
--
ALTER TABLE `export_histories`
  ADD CONSTRAINT `export_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_escalations`
--
ALTER TABLE `feedback_escalations`
  ADD CONSTRAINT `feedback_escalations_feedback_message_id_foreign` FOREIGN KEY (`feedback_message_id`) REFERENCES `feedback_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_escalations_from_position_id_foreign` FOREIGN KEY (`from_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `feedback_escalations_to_position_id_foreign` FOREIGN KEY (`to_position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD CONSTRAINT `feedback_messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `feedback_messages_receiver_position_id_foreign` FOREIGN KEY (`receiver_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `feedback_messages_replied_by_foreign` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `feedback_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_replies`
--
ALTER TABLE `feedback_replies`
  ADD CONSTRAINT `feedback_replies_feedback_message_id_foreign` FOREIGN KEY (`feedback_message_id`) REFERENCES `feedback_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_replies_replied_by_foreign` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_balance_transactions`
--
ALTER TABLE `leave_balance_transactions`
  ADD CONSTRAINT `leave_balance_transactions_attendance_request_id_foreign` FOREIGN KEY (`attendance_request_id`) REFERENCES `attendance_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_balance_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_balance_transactions_employee_leave_balance_id_foreign` FOREIGN KEY (`employee_leave_balance_id`) REFERENCES `employee_leave_balances` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_histories`
--
ALTER TABLE `login_histories`
  ADD CONSTRAINT `login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_otps`
--
ALTER TABLE `login_otps`
  ADD CONSTRAINT `login_otps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_approval_request_id_foreign` FOREIGN KEY (`approval_request_id`) REFERENCES `approval_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `overtime_requests_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtime_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtime_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_change_otps`
--
ALTER TABLE `password_change_otps`
  ADD CONSTRAINT `password_change_otps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD CONSTRAINT `payroll_periods_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payroll_periods_unlocked_by_foreign` FOREIGN KEY (`unlocked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `position_capability_position`
--
ALTER TABLE `position_capability_position`
  ADD CONSTRAINT `position_capability_position_capability_id_foreign` FOREIGN KEY (`capability_id`) REFERENCES `position_capabilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `position_capability_position_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_attachments`
--
ALTER TABLE `project_attachments`
  ADD CONSTRAINT `project_attachments_implementation_detail_id_foreign` FOREIGN KEY (`implementation_detail_id`) REFERENCES `project_implementation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_attachments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_detail_comments`
--
ALTER TABLE `project_detail_comments`
  ADD CONSTRAINT `project_detail_comments_implementation_detail_id_foreign` FOREIGN KEY (`implementation_detail_id`) REFERENCES `project_implementation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_detail_comments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_detail_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_detail_logs`
--
ALTER TABLE `project_detail_logs`
  ADD CONSTRAINT `project_detail_logs_implementation_detail_id_foreign` FOREIGN KEY (`implementation_detail_id`) REFERENCES `project_implementation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_detail_logs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_implementation_details`
--
ALTER TABLE `project_implementation_details`
  ADD CONSTRAINT `project_implementation_details_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `employee_profiles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_implementation_details_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_implementation_details_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_implementation_details_project_milestone_id_foreign` FOREIGN KEY (`project_milestone_id`) REFERENCES `project_milestones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_implementation_details_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_implementation_subtasks`
--
ALTER TABLE `project_implementation_subtasks`
  ADD CONSTRAINT `proj_subtasks_assignee_fk` FOREIGN KEY (`assigned_to`) REFERENCES `employee_profiles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proj_subtasks_detail_fk` FOREIGN KEY (`project_implementation_detail_id`) REFERENCES `project_implementation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proj_subtasks_project_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_implementation_subtasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_implementation_subtasks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_project_role_id_foreign` FOREIGN KEY (`project_role_id`) REFERENCES `project_roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_milestones`
--
ALTER TABLE `project_milestones`
  ADD CONSTRAINT `project_milestones_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_milestones_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_milestones_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_progress_histories`
--
ALTER TABLE `project_progress_histories`
  ADD CONSTRAINT `project_progress_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_progress_histories_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_roles`
--
ALTER TABLE `project_roles`
  ADD CONSTRAINT `project_roles_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_work_logs`
--
ALTER TABLE `project_work_logs`
  ADD CONSTRAINT `project_work_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_work_logs_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_work_logs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_work_logs_project_implementation_detail_id_foreign` FOREIGN KEY (`project_implementation_detail_id`) REFERENCES `project_implementation_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_work_logs_project_implementation_subtask_id_foreign` FOREIGN KEY (`project_implementation_subtask_id`) REFERENCES `project_implementation_subtasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_work_logs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD CONSTRAINT `salary_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `salary_adjustments_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `salary_adjustments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `salary_histories`
--
ALTER TABLE `salary_histories`
  ADD CONSTRAINT `salary_histories_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `salary_histories_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `salary_histories_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `salary_snapshots`
--
ALTER TABLE `salary_snapshots`
  ADD CONSTRAINT `salary_snapshots_employee_profile_id_foreign` FOREIGN KEY (`employee_profile_id`) REFERENCES `employee_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `salary_snapshots_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD CONSTRAINT `social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_position_capability_overrides`
--
ALTER TABLE `user_position_capability_overrides`
  ADD CONSTRAINT `user_position_capability_overrides_capability_id_foreign` FOREIGN KEY (`capability_id`) REFERENCES `position_capabilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_position_capability_overrides_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_position_capability_overrides_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wards`
--
ALTER TABLE `wards`
  ADD CONSTRAINT `wards_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_shift_overtime_rules`
--
ALTER TABLE `work_shift_overtime_rules`
  ADD CONSTRAINT `work_shift_overtime_rules_work_shift_id_foreign` FOREIGN KEY (`work_shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
