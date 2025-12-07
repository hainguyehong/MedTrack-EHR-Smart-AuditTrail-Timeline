-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 06, 2025 lúc 08:34 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `pms_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointment_status_log`
--

CREATE TABLE `appointment_status_log` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','rejected') NOT NULL COMMENT 'pending: chờ xác nhận\r\n rejected : từ chối',
  `doctor_note` text DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `appointment_status_log`
--

INSERT INTO `appointment_status_log` (`id`, `book_id`, `status`, `doctor_note`, `changed_at`) VALUES
(1, 1, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-11-14 11:21:52'),
(2, 2, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-09-28 01:22:03'),
(3, 3, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-11-09 15:53:58'),
(4, 4, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-11-22 09:44:02'),
(5, 5, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-05-22 01:52:54'),
(6, 6, 'rejected', 'Đang chờ bác sĩ xác nhận lịch.', '2025-01-14 01:00:02'),
(7, 7, 'pending', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-03-12 03:59:17'),
(8, 8, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-11-12 15:04:02'),
(9, 9, 'rejected', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-11-27 15:09:55'),
(10, 10, 'confirmed', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-05-20 09:49:08'),
(11, 11, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-02-06 22:23:37'),
(12, 12, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-09-14 05:31:54'),
(13, 13, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-01-22 02:20:48'),
(14, 14, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-07-12 05:28:37'),
(15, 15, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-11-12 23:40:42'),
(16, 16, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-02-09 14:57:44'),
(17, 17, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-10-07 08:47:02'),
(18, 18, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-05-08 03:15:11'),
(19, 19, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-11-17 21:18:43'),
(20, 20, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-11-11 15:36:42'),
(21, 21, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-07-23 22:04:45'),
(22, 22, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-04-26 01:48:51'),
(23, 23, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-05-18 05:24:38'),
(24, 24, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-03-11 06:21:50'),
(25, 25, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-04-23 10:20:27'),
(26, 26, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-03-13 15:32:13'),
(27, 27, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-01-27 19:01:18'),
(28, 28, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-07-08 16:24:02'),
(29, 29, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-06-19 16:29:06'),
(30, 30, 'pending', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-06-02 08:26:11'),
(31, 31, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-08-23 18:42:09'),
(32, 32, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-03-07 23:41:09'),
(33, 33, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-03-18 04:40:31'),
(34, 34, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-09-23 00:59:56'),
(35, 35, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-11-10 07:31:56'),
(36, 36, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-01-03 21:05:04'),
(37, 37, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-04-13 14:56:41'),
(38, 38, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-02-16 20:12:29'),
(39, 39, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-06-17 21:09:25'),
(40, 40, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-08-24 06:36:53'),
(41, 41, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-02-03 14:34:30'),
(42, 42, 'confirmed', 'Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.', '2025-02-04 23:52:07'),
(43, 43, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-03-29 21:58:08'),
(44, 44, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-01-20 12:37:16'),
(45, 45, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-09-18 16:04:10'),
(46, 46, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-09-27 12:47:13'),
(47, 47, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-05-27 08:45:19'),
(48, 48, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-04-06 12:14:36'),
(49, 49, 'pending', 'Đang chờ bác sĩ xác nhận lịch.', '2025-10-29 22:13:00'),
(50, 50, 'rejected', 'Từ chối do trùng lịch / vui lòng đặt lại.', '2025-01-15 13:18:13'),
(68, 65, 'rejected', 'Lịch bị hủy', '2025-12-05 13:25:42'),
(77, 68, 'confirmed', 'Xác nhận', '2025-12-05 14:21:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL COMMENT 'Người thực hiện hành động\r\n1-admin, 2- bác sỹ',
  `table_name` varchar(100) NOT NULL COMMENT 'Tên bảng bị thay đổi',
  `record_id` int(11) NOT NULL COMMENT 'ID bản ghi bị thay đổi',
  `action` enum('insert','update','delete') NOT NULL COMMENT 'Loại hành động',
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dữ liệu cũ (trước khi thay đổi)' CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dữ liệu mới (sau khi thay đổi)' CHECK (json_valid(`new_value`)),
  `changed_at` datetime DEFAULT current_timestamp() COMMENT 'Thời điểm thực hiện hành động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi lại lịch sử chỉnh sửa dữ liệu (Audit Trail)';

--
-- Đang đổ dữ liệu cho bảng `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `table_name`, `record_id`, `action`, `old_value`, `new_value`, `changed_at`) VALUES
(1, '10', 'appointment_status_log', 50, 'update', '{\"status\":\"pending\",\"doctor_note\":\"Từ chối do trùng lịch \\/ vui lòng đặt lại.\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"Từ chối do trùng lịch \\/ vui lòng đặt lại.\"}', '2025-12-03 01:54:25'),
(2, '10', 'appointment_status_log', 7, 'update', '{\"status\":\"pending\",\"doctor_note\":\"Từ chối do trùng lịch \\/ vui lòng đặt lại.\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '2025-12-03 01:54:38'),
(3, '10', 'appointment_status_log', 40, 'update', '{\"status\":\"pending\",\"doctor_note\":\"Từ chối do trùng lịch \\/ vui lòng đặt lại.\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '2025-12-03 01:54:46'),
(4, '10', 'appointment_status_log', 50, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"Từ chối do trùng lịch \\/ vui lòng đặt lại.\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '2025-12-03 01:54:54'),
(5, '1', 'book', 64, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"giờ mới\",\"noi_dung_kham\":\"giwof mới\",\"date_visit\":\"2025-12-03\",\"time_visit\":\"16:00 - 17:30 \",\"created_at\":\"2025-12-03 02:43:36\"}', '2025-12-03 02:43:36'),
(6, '3', 'patients', 71, 'insert', 'null', '{\"patient_name\":\"Hải Nguyễn\",\"address\":\"39 Hồ Tùng Mậu\",\"cnic\":\"025203005779\",\"date_of_birth\":\"2003-01-19\",\"phone_number\":\"0362111355\",\"gender\":\"Nam\"}', '2025-12-04 10:04:39'),
(7, '71', 'book', 65, 'insert', 'null', '{\"id_benh_nhan\":71,\"trieu_chung\":\"Đau đầu chóng mặt \",\"noi_dung_kham\":\"Chụp CT Não\",\"date_visit\":\"2025-12-06\",\"time_visit\":\"14:00 - 15:00 \",\"created_at\":\"2025-12-04 10:07:11\"}', '2025-12-04 10:07:11'),
(8, '3', 'appointment_status_log', 65, 'update', '{\"status\":null,\"doctor_note\":null}', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '2025-12-05 13:25:42'),
(9, '3', 'appointment_status_log', 65, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '{\"status\":\"pending\",\"doctor_note\":\"\"}', '2025-12-05 13:25:58'),
(10, '3', 'appointment_status_log', 41, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '{\"status\":\"pending\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '2025-12-05 13:28:28'),
(11, '3', 'appointment_status_log', 21, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '{\"status\":\"pending\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '2025-12-05 13:28:47'),
(12, '3', 'appointment_status_log', 6, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '{\"status\":\"pending\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '2025-12-05 13:28:54'),
(13, '3', 'appointment_status_log', 24, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '{\"status\":\"pending\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '2025-12-05 13:29:05'),
(14, '3', 'appointment_status_log', 9, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.\"}', '2025-12-05 13:29:15'),
(15, '3', 'appointment_status_log', 23, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '{\"status\":\"pending\",\"doctor_note\":\"Đang chờ bác sĩ xác nhận lịch.\"}', '2025-12-05 13:29:21'),
(16, '3', 'appointment_status_log', 38, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"Lịch hẹn đã được xác nhận. Vui lòng đến đúng giờ.\"}', '2025-12-05 13:29:33'),
(17, '70', 'book', 66, 'insert', 'null', '{\"id_benh_nhan\":70,\"trieu_chung\":\"Đau đầu, chóng mặt, buồn nôn\",\"noi_dung_kham\":\"Chụp XQ não\",\"date_visit\":\"2025-12-05\",\"time_visit\":\"16:00 - 17:30 \",\"created_at\":\"2025-12-05 13:33:44\"}', '2025-12-05 13:33:44'),
(18, '69', 'book', 36, 'delete', '{\"id\":36,\"id_patient\":69,\"date_visit\":\"2025-07-28\",\"time_visit\":\"09:15:00\",\"trieu_chung\":\"Đau bụng, tiêu chảy\",\"noi_dung_kham\":\"Khám tiêu hóa, tư vấn ăn uống\",\"created_at\":\"2025-08-05 07:33:15\",\"is_deleted\":0}', '{\"is_deleted\":1}', '2025-12-05 14:15:06'),
(19, '69', 'book', 67, 'insert', 'null', '{\"id_benh_nhan\":69,\"trieu_chung\":\"\",\"noi_dung_kham\":\"khám tổng quát\",\"date_visit\":\"2025-12-06\",\"time_visit\":\"10:00 - 11:00 \",\"created_at\":\"2025-12-05 14:15:20\"}', '2025-12-05 14:15:20'),
(20, '71', 'book', 68, 'insert', 'null', '{\"id_benh_nhan\":71,\"trieu_chung\":\"Mắt mờ\",\"noi_dung_kham\":\"Khám mắt\",\"date_visit\":\"2025-12-07\",\"time_visit\":\"16:00 - 17:30 \",\"created_at\":\"2025-12-05 14:19:59\"}', '2025-12-05 14:19:59'),
(21, '3', 'appointment_status_log', 68, 'insert', '{\"status\":null,\"doctor_note\":null}', '{\"status\":\"confirmed\",\"doctor_note\":\"Xác nhận\"}', '2025-12-05 14:21:45'),
(22, '3', 'appointment_status_log', 68, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"Xác nhận\"}', '{\"status\":\"rejected\",\"doctor_note\":\"Từ chối\"}', '2025-12-05 14:22:14'),
(23, '3', 'appointment_status_log', 65, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '{\"status\":\"rejected\",\"doctor_note\":\"Lịch bị hủy\"}', '2025-12-05 14:23:42'),
(24, '3', 'appointment_status_log', 68, 'update', '{\"status\":\"rejected\",\"doctor_note\":\"Từ Chối\"}', '{\"status\":\"confirmed\",\"doctor_note\":\"Xác nhận\"}', '2025-12-05 15:32:24'),
(25, '3', 'patient_diseases', 151, 'insert', 'null', '{\"patient_id\":\"71\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"90\",\"nhip_tim\":\"90\",\"trieu_chung\":\"Đau Đầu, chóng mặt, Buồn nôn, Ho nhiều\",\"chuan_doan\":\"Tái Covid-19\",\"bien_phap\":\"Cách ly\",\"nhap_vien\":\"2\",\"tien_su_benh\":\"Covid-19\",\"created_at\":\"2025-12-07 00:28:56\",\"next_visit_date\":\"2025-12-14\",\"anh_sieu_am\":\"uploads\\/anhsieuam\\/1765042136_Hoa Màu Nước Thanh Lịch Bài Đăng Facebook Chúc Mừng Ngày 2011 Sang Trọng (2).png\",\"anh_chup_xq\":\"uploads\\/xquang\\/1765042136_Hoa Màu Nước Thanh Lịch Bài Đăng Facebook Chúc Mừng Ngày 2011 Sang Trọng (2).png\"}', '2025-12-07 00:28:56'),
(26, '3', 'patient_diseases', 152, 'insert', 'null', '{\"patient_id\":\"71\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"90\",\"nhip_tim\":\"90\",\"trieu_chung\":\"Đau nhức xương khớp\",\"chuan_doan\":\"Thiếu Canxi\",\"bien_phap\":\"Bổ sung vitamin\",\"nhap_vien\":\"2\",\"tien_su_benh\":\"Không có\",\"created_at\":\"2025-12-07 00:53:54\",\"next_visit_date\":\"2025-12-21\",\"anh_sieu_am\":null,\"anh_chup_xq\":null}', '2025-12-07 00:53:54'),
(27, '3', 'patient_diseases', 153, 'insert', 'null', '{\"patient_id\":\"71\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"90\",\"nhip_tim\":\"90\",\"trieu_chung\":\"Đau dạ dày, ợ nóng\",\"chuan_doan\":\"Viêm dạ loét dạ dày tái phát\",\"bien_phap\":\"Tránh ăn đồ cay nóng, uống thuốc đúng giờ\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"Viêm dạ loét dạ dày\",\"created_at\":\"2025-12-07 01:05:38\",\"next_visit_date\":\"2025-12-16\",\"anh_sieu_am\":null,\"anh_chup_xq\":null}', '2025-12-07 01:05:38'),
(28, '3', 'patient_diseases', 154, 'insert', 'null', '{\"patient_id\":\"20\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"90\",\"nhip_tim\":\"90\",\"trieu_chung\":\"Đau dạ dày, ợ nóng\",\"chuan_doan\":\"Viêm dạ loét dạ dày tái phát\",\"bien_phap\":\"Tránh ăn đồ cay nóng, uống thuốc đúng giờ\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"Viêm dạ loét dạ dày\",\"created_at\":\"2025-12-07 01:06:24\",\"next_visit_date\":\"2025-12-16\",\"anh_sieu_am\":null,\"anh_chup_xq\":null}', '2025-12-07 01:06:24'),
(29, '3', 'patient_medication_history', 154, 'insert', 'null', '{\"patient_id\":\"20\",\"visit_id\":\"154\",\"thuoc\":[{\"medicine_id\":\"222\",\"quantity\":\"12\",\"dosage\":\"2 viên\\/ ngày\",\"note\":\"sau ăn\"}],\"created_at\":\"2025-12-07 01:47:30\"}', '2025-12-07 01:47:30'),
(30, '3', 'patient_diseases', 155, 'insert', 'null', '{\"patient_id\":\"71\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"70\",\"nhip_tim\":\"70\",\"trieu_chung\":\"Đau bụng dữ dội\",\"chuan_doan\":\"Viêm loét dạ dày tái lại\",\"bien_phap\":\"Ăn uống ngủ nghỉ đúng giờ\",\"nhap_vien\":\"2\",\"tien_su_benh\":\"Viêm loét dạ dày cấp\",\"created_at\":\"2025-12-07 01:58:20\",\"next_visit_date\":\"2025-12-08\",\"anh_sieu_am\":null,\"anh_chup_xq\":null}', '2025-12-07 01:58:20'),
(31, '3', 'patient_medication_history', 155, 'insert', 'null', '{\"patient_id\":\"71\",\"visit_id\":\"155\",\"thuoc\":[{\"medicine_id\":\"24\",\"quantity\":\"20\",\"dosage\":\"3 ống\\/ 1 ngày\",\"note\":\"Trước ăn\"}],\"created_at\":\"2025-12-07 01:59:09\"}', '2025-12-07 01:59:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `date_visit` date NOT NULL,
  `time_visit` time NOT NULL,
  `trieu_chung` text NOT NULL,
  `noi_dung_kham` text NOT NULL,
  `created_at` datetime NOT NULL,
  `is_deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `book`
--

INSERT INTO `book` (`id`, `id_patient`, `date_visit`, `time_visit`, `trieu_chung`, `noi_dung_kham`, `created_at`, `is_deleted`) VALUES
(1, 57, '2025-12-31', '17:00:00', 'Tiểu buốt, đau hông lưng', 'Khám cơ xương khớp, chỉ định chụp X-quang nếu cần', '2025-10-09 06:39:14', 0),
(2, 51, '2025-07-25', '15:15:00', 'Đau thượng vị, ợ chua', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-02-01 06:25:37', 0),
(3, 58, '2025-07-29', '17:15:00', 'Đau đầu, chóng mặt', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-05-05 02:42:33', 0),
(4, 51, '2025-06-07', '13:30:00', 'Tiểu buốt, đau hông lưng', 'Khám tiêu hóa, tư vấn ăn uống', '2025-04-10 19:14:16', 0),
(5, 57, '2025-09-04', '16:00:00', 'Đau thượng vị, ợ chua', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-11-05 20:13:58', 0),
(6, 20, '2025-04-16', '16:30:00', 'Đau ngực nhẹ, hồi hộp', 'Khám tổng quát, đo sinh hiệu, tư vấn điều trị', '2025-10-13 14:36:49', 0),
(7, 66, '2025-01-24', '12:00:00', 'Đau thượng vị, ợ chua', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-08-05 15:14:44', 0),
(8, 43, '2025-09-28', '17:15:00', 'Mẩn ngứa, nổi mề đay', 'Khám cơ xương khớp, chỉ định chụp X-quang nếu cần', '2025-03-07 19:48:40', 0),
(9, 68, '2025-06-04', '10:30:00', 'Đau lưng, tê chân', 'Khám hô hấp, nghe phổi, kê thuốc', '2025-08-19 01:41:13', 0),
(10, 16, '2025-08-31', '16:15:00', 'Đau lưng, tê chân', 'Khám da liễu, tư vấn tránh dị nguyên', '2025-04-28 22:08:23', 0),
(11, 30, '2025-10-30', '13:00:00', 'Đau lưng, tê chân', 'Khám cơ xương khớp, chỉ định chụp X-quang nếu cần', '2025-09-07 23:06:00', 0),
(12, 32, '2025-01-24', '11:45:00', 'Ho có đờm, khó thở nhẹ', 'Khám cơ xương khớp, chỉ định chụp X-quang nếu cần', '2025-06-16 02:50:07', 0),
(13, 59, '2025-09-12', '13:00:00', 'Đau ngực nhẹ, hồi hộp', 'Khám tiêu hóa, tư vấn ăn uống', '2025-05-13 06:26:41', 0),
(14, 19, '2025-11-14', '10:00:00', 'Khó ngủ, lo âu', 'Khám da liễu, tư vấn tránh dị nguyên', '2025-01-17 17:00:19', 0),
(15, 14, '2025-11-29', '10:00:00', 'Đau ngực nhẹ, hồi hộp', 'Khám tổng quát, đo sinh hiệu, tư vấn điều trị', '2025-01-27 22:51:38', 0),
(16, 60, '2025-07-22', '15:45:00', 'Đau khớp gối/khớp tay', 'Khám tiêu hóa, tư vấn ăn uống', '2025-11-06 13:49:09', 0),
(17, 15, '2025-11-13', '12:45:00', 'Đau thượng vị, ợ chua', 'Khám tim mạch, đo huyết áp, theo dõi định kỳ', '2025-02-10 01:19:57', 0),
(18, 28, '2025-06-13', '11:00:00', 'Khát nước nhiều, tiểu nhiều', 'Khám da liễu, tư vấn tránh dị nguyên', '2025-07-10 05:21:52', 0),
(19, 18, '2025-12-02', '15:30:00', 'Đau bụng, tiêu chảy', 'Khám cơ xương khớp, chỉ định chụp X-quang nếu cần', '2025-11-10 04:41:21', 0),
(20, 16, '2025-09-12', '16:15:00', 'Đau bụng, tiêu chảy', 'Khám hô hấp, nghe phổi, kê thuốc', '2025-12-19 17:31:22', 0),
(21, 2, '2025-03-16', '12:15:00', 'Tiểu buốt, đau hông lưng', 'Khám tổng quát, đo sinh hiệu, tư vấn điều trị', '2025-08-02 02:07:33', 0),
(22, 16, '2025-11-30', '12:30:00', 'Khó ngủ, lo âu', 'Khám tổng quát, đo sinh hiệu, tư vấn điều trị', '2025-06-29 01:08:46', 0),
(23, 52, '2025-07-14', '13:15:00', 'Đau lưng, tê chân', 'Khám tiêu hóa, tư vấn ăn uống', '2025-04-22 02:26:23', 0),
(24, 65, '2025-05-18', '09:45:00', 'Đau thượng vị, ợ chua', 'Khám tim mạch, đo huyết áp, theo dõi định kỳ', '2025-11-07 01:57:11', 0),
(25, 58, '2025-06-12', '12:30:00', 'Đau bụng, tiêu chảy', 'Khám tiêu hóa, tư vấn ăn uống', '2025-04-11 08:02:51', 0),
(26, 55, '2025-10-23', '07:45:00', 'Sốt, ho, đau họng', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-07-09 00:51:47', 0),
(27, 63, '2025-03-29', '15:45:00', 'Đau lưng, tê chân', 'Khám da liễu, tư vấn tránh dị nguyên', '2025-10-18 15:19:25', 0),
(28, 34, '2025-08-13', '16:15:00', 'Đau khớp gối/khớp tay', 'Khám hô hấp, nghe phổi, kê thuốc', '2025-06-24 23:30:26', 0),
(29, 38, '2025-05-15', '07:45:00', 'Đau thượng vị, ợ chua', 'Khám tổng quát, đo sinh hiệu, tư vấn điều trị', '2025-09-21 02:40:25', 0),
(30, 42, '2025-02-09', '16:15:00', 'Đau đầu, chóng mặt', 'Khám hô hấp, nghe phổi, kê thuốc', '2025-07-29 22:17:57', 0),
(31, 26, '2025-05-26', '10:30:00', 'Đau khớp gối/khớp tay', 'Khám nội tiết, kiểm tra đường huyết', '2025-10-13 09:27:17', 0),
(32, 16, '2025-11-03', '14:30:00', 'Sốt, ho, đau họng', 'Khám hô hấp, nghe phổi, kê thuốc', '2025-11-16 10:24:44', 0),
(33, 39, '2025-06-25', '15:00:00', 'Đau lưng, tê chân', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-02-27 01:50:42', 0),
(34, 2, '2025-04-26', '10:30:00', 'Mẩn ngứa, nổi mề đay', 'Khám nội tiết, kiểm tra đường huyết', '2025-05-02 19:54:49', 0),
(35, 60, '2025-08-18', '09:30:00', 'Đau bụng, tiêu chảy', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-11-30 15:43:26', 0),
(36, 69, '2025-07-28', '09:15:00', 'Đau bụng, tiêu chảy', 'Khám tiêu hóa, tư vấn ăn uống', '2025-08-05 07:33:15', 1),
(37, 22, '2025-05-15', '09:15:00', 'Sốt, ho, đau họng', 'Khám nội tiết, kiểm tra đường huyết', '2025-01-16 13:21:24', 0),
(38, 51, '2025-10-28', '12:30:00', 'Đau thượng vị, ợ chua', 'Khám nội tiết, kiểm tra đường huyết', '2025-08-27 18:15:21', 0),
(39, 14, '2025-08-27', '15:45:00', 'Đau thượng vị, ợ chua', 'Khám tổng quát, đo sinh hiệu, tư vấn điều trị', '2025-01-29 01:32:03', 0),
(40, 6, '2025-10-25', '11:30:00', 'Đau ngực nhẹ, hồi hộp', 'Khám tim mạch, đo huyết áp, theo dõi định kỳ', '2025-06-16 23:02:19', 0),
(41, 52, '2025-01-18', '09:15:00', 'Sốt, ho, đau họng', 'Khám cơ xương khớp, chỉ định chụp X-quang nếu cần', '2025-12-16 08:03:42', 0),
(42, 8, '2025-10-11', '08:30:00', 'Đau lưng, tê chân', 'Khám da liễu, tư vấn tránh dị nguyên', '2025-08-15 18:04:11', 0),
(43, 51, '2025-12-15', '11:45:00', 'Đau bụng, tiêu chảy', 'Khám nội tiết, kiểm tra đường huyết', '2025-12-08 21:30:11', 0),
(44, 68, '2025-09-07', '07:15:00', 'Sốt, ho, đau họng', 'Khám tiêu hóa, tư vấn ăn uống', '2025-05-01 21:40:10', 0),
(45, 5, '2025-08-31', '15:00:00', 'Tiểu buốt, đau hông lưng', 'Khám hô hấp, nghe phổi, kê thuốc', '2025-03-03 20:44:20', 0),
(46, 14, '2025-04-09', '11:00:00', 'Đau đầu, chóng mặt', 'Khám nội tiết, kiểm tra đường huyết', '2025-12-10 21:39:40', 0),
(47, 53, '2025-01-29', '16:30:00', 'Đau bụng, tiêu chảy', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-11-01 00:14:36', 0),
(48, 40, '2025-08-26', '16:15:00', 'Đau khớp gối/khớp tay', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-01-16 16:21:23', 0),
(49, 13, '2025-06-29', '07:30:00', 'Đau ngực nhẹ, hồi hộp', 'Tái khám theo hẹn, đánh giá đáp ứng thuốc', '2025-04-22 18:19:54', 0),
(50, 27, '2025-09-16', '08:00:00', 'Đau bụng, tiêu chảy', 'Khám nội tiết, kiểm tra đường huyết', '2025-04-25 07:39:17', 0),
(64, 1, '2025-12-03', '16:00:00', 'giờ mới', 'giwof mới', '2025-12-03 02:43:36', 0),
(65, 71, '2025-12-06', '14:00:00', 'Đau đầu chóng mặt ', 'Chụp CT Não não', '2025-12-04 10:07:11', 0),
(66, 70, '2025-12-05', '16:00:00', 'Đau đầu, chóng mặt, buồn nôn', 'Chụp XQ não', '2025-12-05 13:33:44', 0),
(67, 69, '2025-12-06', '10:00:00', '', 'khám tổng quát', '2025-12-05 14:15:20', 0),
(68, 71, '2025-12-07', '16:00:00', 'Mắt mờ', 'Khám mắt', '2025-12-05 14:19:59', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `medicine_name` varchar(60) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `medicines`
--

INSERT INTO `medicines` (`id`, `medicine_name`, `is_deleted`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 'Paracetamol 500mg', 0, '2025-07-28 07:32:41', NULL, '2025-08-12 07:32:41'),
(2, 'Efferalgan 500mg', 0, '2025-10-23 22:36:52', NULL, '2025-11-08 22:36:52'),
(3, 'Panadol Extra', 0, '2025-04-09 01:46:52', NULL, '2025-04-30 01:46:52'),
(4, 'Ambroxol 30mg', 0, '2025-10-22 22:18:52', NULL, '2025-11-17 22:18:52'),
(5, 'Acetylcysteine 200mg', 0, '2025-12-07 23:57:04', NULL, '2025-12-08 23:57:04'),
(6, 'Bromhexine 8mg', 0, '2025-06-24 14:14:48', NULL, '2025-06-30 14:14:48'),
(7, 'Alpha chymotrypsin', 0, '2025-09-06 07:07:46', NULL, '2025-09-27 07:07:46'),
(8, 'Salbutamol inhaler', 0, '2025-08-15 07:14:51', NULL, '2025-09-11 07:14:51'),
(9, 'Budesonide inhaler', 0, '2025-08-29 01:32:33', NULL, '2025-09-15 01:32:33'),
(10, 'Natri Clorid 0.9% (xịt/nhỏ mũi)', 0, '2025-12-01 12:05:14', NULL, '2025-12-26 12:05:14'),
(11, 'Loratadine 10mg', 0, '2025-06-29 23:46:50', NULL, '2025-07-26 23:46:50'),
(12, 'Cetirizine 10mg', 0, '2025-02-14 07:02:50', NULL, '2025-03-11 07:02:50'),
(13, 'Fexofenadine 180mg', 0, '2025-11-13 15:52:37', NULL, '2025-12-06 15:52:37'),
(14, 'Fluticasone xịt mũi', 0, '2025-05-23 22:14:39', NULL, '2025-06-08 22:14:39'),
(15, 'Mometasone xịt mũi', 0, '2025-08-19 15:45:05', NULL, '2025-09-02 15:45:05'),
(16, 'Hydrocortisone cream 1%', 0, '2025-06-26 03:05:12', NULL, '2025-07-25 03:05:12'),
(17, 'Omeprazole 20mg', 0, '2025-06-25 12:53:09', NULL, '2025-07-08 12:53:09'),
(18, 'Esomeprazole 40mg', 0, '2025-10-18 01:03:31', NULL, '2025-11-05 01:03:31'),
(19, 'Pantoprazole 40mg', 0, '2025-10-09 00:08:47', NULL, '2025-11-06 00:08:47'),
(20, 'Sucralfate 1g', 0, '2025-06-23 17:15:41', NULL, '2025-07-08 17:15:41'),
(21, 'Almagel', 0, '2025-02-11 14:39:03', NULL, '2025-02-12 14:39:03'),
(22, 'Smecta (Diosmectite)', 0, '2025-11-19 03:00:57', NULL, '2025-11-27 03:00:57'),
(23, 'ORS (Oresol)', 0, '2025-09-28 21:27:07', NULL, '2025-10-24 21:27:07'),
(24, 'Men vi sinh Bacillus clausii', 0, '2025-02-27 20:27:05', NULL, '2025-03-03 20:27:05'),
(25, 'Loperamide 2mg', 0, '2025-05-02 03:30:29', NULL, '2025-05-07 03:30:29'),
(26, 'Domperidone 10mg', 0, '2025-11-18 02:35:48', NULL, '2025-12-14 02:35:48'),
(27, 'Itopride 50mg', 0, '2025-09-23 17:46:12', NULL, '2025-09-23 17:46:12'),
(28, 'Berberin 100mg', 0, '2025-12-01 02:16:05', NULL, '2025-12-16 02:16:05'),
(29, 'Amoxicillin 500mg', 0, '2025-11-03 03:10:15', NULL, '2025-11-22 03:10:15'),
(30, 'Amoxicillin/Clavulanate 625mg (Augmentin)', 0, '2025-09-12 15:50:12', NULL, '2025-09-28 15:50:12'),
(31, 'Azithromycin 500mg', 0, '2025-09-16 08:21:53', NULL, '2025-10-11 08:21:53'),
(32, 'Clarithromycin 500mg', 0, '2025-03-03 16:24:20', NULL, '2025-03-11 16:24:20'),
(33, 'Cefuroxime 500mg', 0, '2025-11-03 20:25:23', NULL, '2025-11-14 20:25:23'),
(34, 'Cefixime 200mg', 0, '2025-06-15 00:23:43', NULL, '2025-06-17 00:23:43'),
(35, 'Ciprofloxacin 500mg', 0, '2025-01-09 15:42:23', NULL, '2025-02-04 15:42:23'),
(36, 'Metronidazole 250mg', 0, '2025-05-20 06:52:02', NULL, '2025-05-27 06:52:02'),
(37, 'Fosfomycin 3g', 0, '2025-01-14 13:07:39', NULL, '2025-01-28 13:07:39'),
(38, 'Amlodipine 5mg', 0, '2025-04-09 19:37:07', NULL, '2025-05-06 19:37:07'),
(39, 'Losartan 50mg', 0, '2025-10-23 14:36:38', NULL, '2025-10-31 14:36:38'),
(40, 'Telmisartan 40mg', 0, '2025-12-14 05:02:32', NULL, '2026-01-10 05:02:32'),
(41, 'Perindopril 4mg', 0, '2025-10-14 12:18:46', NULL, '2025-10-18 12:18:46'),
(42, 'Hydrochlorothiazide 25mg', 0, '2025-05-09 20:34:00', NULL, '2025-05-19 20:34:00'),
(43, 'Atorvastatin 20mg', 0, '2025-08-23 15:57:16', NULL, '2025-08-28 15:57:16'),
(44, 'Rosuvastatin 10mg', 0, '2025-01-14 22:02:34', NULL, '2025-02-01 22:02:34'),
(45, 'Omega-3', 0, '2025-12-09 11:13:22', NULL, '2026-01-03 11:13:22'),
(46, 'Metformin 500mg', 0, '2025-06-26 14:51:28', NULL, '2025-07-21 14:51:28'),
(47, 'Gliclazide MR 30mg', 0, '2025-10-01 13:17:41', NULL, '2025-10-07 13:17:41'),
(48, 'Glimepiride 2mg', 0, '2025-11-19 22:53:18', NULL, '2025-12-11 22:53:18'),
(49, 'Sitagliptin 100mg', 0, '2025-01-25 03:13:47', NULL, '2025-01-27 03:13:47'),
(50, 'Empagliflozin 10mg', 0, '2025-04-17 23:22:11', NULL, '2025-04-22 23:22:11'),
(51, 'Insulin glargine', 0, '2025-12-22 06:57:57', NULL, '2026-01-01 06:57:57'),
(52, 'Ibuprofen 400mg', 0, '2025-11-09 18:01:40', NULL, '2025-11-15 18:01:40'),
(53, 'Diclofenac 50mg', 0, '2025-07-09 15:50:23', NULL, '2025-08-06 15:50:23'),
(54, 'Meloxicam 7.5mg', 0, '2025-02-28 18:42:36', NULL, '2025-03-29 18:42:36'),
(55, 'Celecoxib 200mg', 0, '2025-05-20 20:56:16', NULL, '2025-06-18 20:56:16'),
(56, 'Eperisone 50mg (giãn cơ)', 0, '2025-10-31 13:25:46', NULL, '2025-11-05 13:25:46'),
(57, 'Vitamin B1-B6-B12', 0, '2025-05-09 18:43:33', NULL, '2025-05-16 18:43:33'),
(58, 'Gabapentin 300mg', 0, '2025-03-21 18:55:53', NULL, '2025-03-30 18:55:53'),
(59, 'Glucosamine sulfate', 0, '2025-12-23 23:09:52', NULL, '2026-01-19 23:09:52'),
(60, 'Paracetamol syrup (trẻ em)', 0, '2025-08-13 05:45:01', NULL, '2025-08-23 05:45:01'),
(61, 'Tenofovir TDF 300mg', 0, '2025-11-14 20:46:52', NULL, '2025-11-23 20:46:52'),
(62, 'Tenofovir TAF 25mg', 0, '2025-01-07 07:05:35', NULL, '2025-01-10 07:05:35'),
(63, 'Entecavir 0.5mg', 0, '2025-06-28 19:44:28', NULL, '2025-07-01 19:44:28'),
(64, 'Silymarin (bổ gan)', 0, '2025-02-25 23:14:56', NULL, '2025-03-08 23:14:56'),
(65, 'Tamsulosin 0.4mg', 0, '2025-07-04 00:03:51', NULL, '2025-07-14 00:03:51'),
(66, 'Buscopan (Hyoscine butylbromide)', 0, '2025-03-14 07:59:01', NULL, '2025-04-11 07:59:01'),
(67, 'Rotunda (Lạc tiên + tâm sen)', 0, '2025-03-25 07:06:54', NULL, '2025-04-01 07:06:54'),
(68, 'Zolpidem 10mg', 0, '2025-07-21 15:20:03', NULL, '2025-07-21 15:20:03'),
(69, 'Sertraline 50mg', 0, '2025-06-11 11:19:25', NULL, '2025-06-15 11:19:25'),
(128, 'Ceftriaxone 1g', 0, '2025-11-14 22:59:31', NULL, '2025-12-05 22:59:31'),
(129, 'Cefotaxime 1g', 0, '2025-12-07 06:00:13', NULL, '2025-12-23 06:00:13'),
(130, 'Cephalexin 500mg', 0, '2025-11-15 03:08:48', NULL, '2025-12-07 03:08:48'),
(131, 'Cefazolin 1g', 0, '2025-03-04 05:45:32', NULL, '2025-03-21 05:45:32'),
(132, 'Levofloxacin 500mg', 0, '2025-05-25 09:06:06', NULL, '2025-06-01 09:06:06'),
(133, 'Moxifloxacin 400mg', 0, '2025-12-25 01:06:24', NULL, '2025-12-31 01:06:24'),
(134, 'Ofloxacin 200mg', 0, '2025-01-29 11:07:19', NULL, '2025-02-21 11:07:19'),
(135, 'Doxycycline 100mg', 0, '2025-08-27 09:21:20', NULL, '2025-09-24 09:21:20'),
(136, 'Tetracycline 500mg', 0, '2025-09-16 05:40:05', NULL, '2025-10-08 05:40:05'),
(137, 'Erythromycin 500mg', 0, '2025-07-25 23:29:49', NULL, '2025-08-12 23:29:49'),
(138, 'Clindamycin 300mg', 0, '2025-05-10 11:26:07', NULL, '2025-06-07 11:26:07'),
(139, 'Vancomycin 1g', 0, '2025-08-30 05:47:59', NULL, '2025-09-13 05:47:59'),
(140, 'Linezolid 600mg', 0, '2025-05-14 07:16:55', NULL, '2025-05-26 07:16:55'),
(141, 'Meropenem 1g', 0, '2025-12-28 14:09:55', NULL, '2026-01-18 14:09:55'),
(142, 'Imipenem/Cilastatin 500mg', 0, '2025-07-15 11:36:36', NULL, '2025-08-01 11:36:36'),
(143, 'Piperacillin/Tazobactam 4.5g', 0, '2025-04-05 21:06:25', NULL, '2025-04-22 21:06:25'),
(144, 'Trimethoprim/Sulfamethoxazole', 0, '2025-02-13 08:32:48', NULL, '2025-03-10 08:32:48'),
(145, 'Nitrofurantoin 100mg', 0, '2025-12-01 20:25:29', NULL, '2025-12-01 20:25:29'),
(146, 'Rifaximin 200mg', 0, '2025-05-12 23:09:30', NULL, '2025-06-03 23:09:30'),
(147, 'Gentamicin 80mg', 0, '2025-08-12 16:11:18', NULL, '2025-09-06 16:11:18'),
(148, 'Amikacin 500mg', 0, '2025-05-23 03:09:36', NULL, '2025-06-04 03:09:36'),
(149, 'Spiramycin 3M IU', 0, '2025-11-19 10:21:01', NULL, '2025-11-24 10:21:01'),
(150, 'Cotrimoxazole 960mg', 0, '2025-04-13 00:13:17', NULL, '2025-05-08 00:13:17'),
(151, 'Tinidazole 500mg', 0, '2025-05-12 07:03:20', NULL, '2025-05-20 07:03:20'),
(152, 'Secnidazole 1g', 0, '2025-04-27 12:04:12', NULL, '2025-05-19 12:04:12'),
(153, 'Ertapenem 1g', 0, '2025-10-29 21:50:06', NULL, '2025-11-23 21:50:06'),
(154, 'Colistin 1M IU', 0, '2025-11-04 18:14:21', NULL, '2025-11-22 18:14:21'),
(155, 'Aztreonam 1g', 0, '2025-07-30 06:02:13', NULL, '2025-07-30 06:02:13'),
(156, 'Cefepime 1g', 0, '2025-05-10 22:33:19', NULL, '2025-05-31 22:33:19'),
(157, 'Ceftazidime 1g', 0, '2025-07-31 14:13:23', NULL, '2025-08-21 14:13:23'),
(158, 'Cefoperazone/Sulbactam 1g', 0, '2025-10-23 19:14:45', NULL, '2025-11-19 19:14:45'),
(159, 'Amoxicillin/Clavulanate 625mg', 0, '2025-02-19 22:32:35', NULL, '2025-02-27 22:32:35'),
(160, 'Phenoxymethylpenicillin 1M IU', 0, '2025-03-29 23:38:38', NULL, '2025-04-11 23:38:38'),
(161, 'Ampicillin 500mg', 0, '2025-08-10 20:38:34', NULL, '2025-08-29 20:38:34'),
(162, 'Sultamicillin 375mg', 0, '2025-05-19 11:25:32', NULL, '2025-06-17 11:25:32'),
(163, 'Minocycline 100mg', 0, '2025-09-27 07:18:21', NULL, '2025-10-19 07:18:21'),
(164, 'Roxithromycin 150mg', 0, '2025-08-09 11:59:19', NULL, '2025-08-30 11:59:19'),
(165, 'Daptomycin 500mg', 0, '2025-11-02 18:02:03', NULL, '2025-12-01 18:02:03'),
(166, 'Teicoplanin 400mg', 0, '2025-06-24 18:45:29', NULL, '2025-07-05 18:45:29'),
(167, 'Montelukast 10mg', 0, '2025-07-24 02:06:01', NULL, '2025-08-10 02:06:01'),
(168, 'Desloratadine 5mg', 0, '2025-04-25 15:05:12', NULL, '2025-05-18 15:05:12'),
(169, 'Chlorpheniramine 4mg', 0, '2025-12-08 03:44:56', NULL, '2025-12-18 03:44:56'),
(170, 'Diphenhydramine 25mg', 0, '2025-12-07 07:27:25', NULL, '2025-12-25 07:27:25'),
(171, 'Phenylephrine 10mg', 0, '2025-04-21 11:56:50', NULL, '2025-05-10 11:56:50'),
(172, 'Pseudoephedrine 60mg', 0, '2025-05-12 15:53:19', NULL, '2025-06-06 15:53:19'),
(173, 'Dextromethorphan 15mg', 0, '2025-02-17 02:21:40', NULL, '2025-02-20 02:21:40'),
(174, 'Terbutaline 2.5mg', 0, '2025-03-16 21:51:58', NULL, '2025-04-05 21:51:58'),
(175, 'Theophylline 200mg', 0, '2025-09-27 10:12:49', NULL, '2025-10-17 10:12:49'),
(176, 'Prednisolone 5mg', 0, '2025-03-06 21:16:05', NULL, '2025-03-31 21:16:05'),
(177, 'Methylprednisolone 16mg', 0, '2025-09-26 00:10:22', NULL, '2025-09-29 00:10:22'),
(178, 'Dexamethasone 0.5mg', 0, '2025-05-09 03:52:48', NULL, '2025-05-21 03:52:48'),
(179, 'Ipratropium bromide inhaler', 0, '2025-01-24 00:10:03', NULL, '2025-01-25 00:10:03'),
(180, 'Fluimucil 200mg', 0, '2025-01-14 16:16:04', NULL, '2025-01-15 16:16:04'),
(181, 'Prospan syrup', 0, '2025-02-22 19:34:50', NULL, '2025-03-11 19:34:50'),
(182, 'Bổ phế Nam Hà', 0, '2025-05-31 09:37:36', NULL, '2025-06-10 09:37:36'),
(183, 'Eugica ho', 0, '2025-07-12 06:41:09', NULL, '2025-07-29 06:41:09'),
(184, 'Siro Astex', 0, '2025-04-20 16:23:53', NULL, '2025-05-13 16:23:53'),
(185, 'Xịt mũi NaCl 0.9%', 0, '2025-12-20 11:23:29', NULL, '2026-01-04 11:23:29'),
(186, 'Xịt mũi Xylometazoline', 0, '2025-09-11 20:51:54', NULL, '2025-10-08 20:51:54'),
(187, 'Xịt mũi Oxymetazoline', 0, '2025-07-02 05:55:03', NULL, '2025-07-24 05:55:03'),
(188, 'Bisoprolol 2.5mg', 0, '2025-07-12 16:51:37', NULL, '2025-07-25 16:51:37'),
(189, 'Metoprolol 50mg', 0, '2025-08-28 04:32:39', NULL, '2025-09-24 04:32:39'),
(190, 'Carvedilol 6.25mg', 0, '2025-09-13 06:36:00', NULL, '2025-10-03 06:36:00'),
(191, 'Nebivolol 5mg', 0, '2025-05-23 12:30:35', NULL, '2025-06-17 12:30:35'),
(192, 'Enalapril 5mg', 0, '2025-02-15 10:17:28', NULL, '2025-02-16 10:17:28'),
(193, 'Captopril 25mg', 0, '2025-11-07 10:56:35', NULL, '2025-11-10 10:56:35'),
(194, 'Lisinopril 10mg', 0, '2025-01-15 19:18:21', NULL, '2025-02-09 19:18:21'),
(195, 'Valsartan 80mg', 0, '2025-02-12 04:19:33', NULL, '2025-02-13 04:19:33'),
(196, 'Irbesartan 150mg', 0, '2025-10-31 04:26:37', NULL, '2025-11-01 04:26:37'),
(197, 'Olmesartan 20mg', 0, '2025-09-28 21:30:06', NULL, '2025-10-15 21:30:06'),
(198, 'Nifedipine 20mg', 0, '2025-08-17 08:32:47', NULL, '2025-08-29 08:32:47'),
(199, 'Diltiazem 60mg', 0, '2025-03-17 16:52:23', NULL, '2025-04-09 16:52:23'),
(200, 'Verapamil 80mg', 0, '2025-04-24 03:40:18', NULL, '2025-04-29 03:40:18'),
(201, 'Spironolactone 25mg', 0, '2025-01-09 13:54:35', NULL, '2025-01-25 13:54:35'),
(202, 'Furosemide 40mg', 0, '2025-08-28 15:14:30', NULL, '2025-09-16 15:14:30'),
(203, 'Indapamide 1.5mg', 0, '2025-04-06 20:24:48', NULL, '2025-04-17 20:24:48'),
(204, 'Clopidogrel 75mg', 0, '2025-01-30 10:40:47', NULL, '2025-02-07 10:40:47'),
(205, 'Aspirin 81mg (tim mạch)', 0, '2025-03-03 19:12:03', NULL, '2025-04-01 19:12:03'),
(206, 'Warfarin 2mg', 0, '2025-06-29 04:41:58', NULL, '2025-07-12 04:41:58'),
(207, 'Rivaroxaban 10mg', 0, '2025-10-21 12:29:23', NULL, '2025-11-09 12:29:23'),
(208, 'Apixaban 5mg', 0, '2025-11-11 03:00:59', NULL, '2025-11-21 03:00:59'),
(209, 'Nitroglycerin ngậm', 0, '2025-02-11 16:54:41', NULL, '2025-02-27 16:54:41'),
(210, 'Isosorbide mononitrate 30mg', 0, '2025-06-05 06:10:03', NULL, '2025-06-18 06:10:03'),
(211, 'Digoxin 0.25mg', 0, '2025-01-20 12:59:20', NULL, '2025-02-15 12:59:20'),
(212, 'Amiodarone 200mg', 0, '2025-03-10 23:16:55', NULL, '2025-03-20 23:16:55'),
(213, 'Simvastatin 20mg', 0, '2025-02-09 13:27:36', NULL, '2025-02-25 13:27:36'),
(214, 'Ezetimibe 10mg', 0, '2025-05-11 22:12:24', NULL, '2025-05-16 22:12:24'),
(215, 'Fenofibrate 145mg', 0, '2025-11-03 12:47:33', NULL, '2025-11-22 12:47:33'),
(216, 'Insulin aspart', 0, '2025-09-23 19:12:17', NULL, '2025-10-13 19:12:17'),
(217, 'Insulin lispro', 0, '2025-04-12 09:45:39', NULL, '2025-04-21 09:45:39'),
(218, 'Insulin detemir', 0, '2025-09-22 12:37:07', NULL, '2025-10-12 12:37:07'),
(219, 'Insulin NPH', 0, '2025-04-09 07:54:38', NULL, '2025-04-17 07:54:38'),
(220, 'Liraglutide', 0, '2025-08-06 12:44:21', NULL, '2025-08-10 12:44:21'),
(221, 'Dulaglutide', 0, '2025-11-25 02:51:08', NULL, '2025-11-27 02:51:08'),
(222, 'Acarbose 50mg', 0, '2025-09-19 11:32:51', NULL, '2025-09-29 11:32:51'),
(223, 'Pioglitazone 15mg', 0, '2025-07-10 01:44:19', NULL, '2025-07-28 01:44:19'),
(224, 'Saxagliptin 5mg', 0, '2025-06-13 01:06:34', NULL, '2025-06-25 01:06:34'),
(225, 'Vildagliptin 50mg', 0, '2025-10-20 05:26:17', NULL, '2025-11-10 05:26:17'),
(226, 'Linagliptin 5mg', 0, '2025-03-11 21:20:35', NULL, '2025-04-04 21:20:35'),
(227, 'Glipizide 5mg', 0, '2025-06-07 20:34:36', NULL, '2025-06-29 20:34:36'),
(228, 'Glyburide 5mg', 0, '2025-06-30 06:52:38', NULL, '2025-07-05 06:52:38'),
(229, 'Levothyroxine 50mcg', 0, '2025-06-29 17:28:49', NULL, '2025-07-25 17:28:49'),
(230, 'Methimazole 5mg', 0, '2025-11-29 06:14:06', NULL, '2025-12-26 06:14:06'),
(231, 'Rabeprazole 20mg', 0, '2025-11-12 09:08:55', NULL, '2025-11-28 09:08:55'),
(232, 'Lansoprazole 30mg', 0, '2025-03-23 21:42:49', NULL, '2025-04-04 21:42:49'),
(233, 'Famotidine 20mg', 0, '2025-06-27 18:13:19', NULL, '2025-07-01 18:13:19'),
(234, 'Bismuth subcitrate', 0, '2025-03-27 06:48:08', NULL, '2025-04-18 06:48:08'),
(235, 'Simethicone 40mg', 0, '2025-01-21 13:14:48', NULL, '2025-01-21 13:14:48'),
(236, 'Activated charcoal', 0, '2025-12-19 22:28:04', NULL, '2026-01-10 22:28:04'),
(237, 'Lactulose syrup', 0, '2025-11-12 19:40:47', NULL, '2025-11-13 19:40:47'),
(238, 'Bisacodyl 5mg', 0, '2025-09-27 03:42:28', NULL, '2025-10-11 03:42:28'),
(239, 'Senna (nhuận tràng)', 0, '2025-03-23 13:20:13', NULL, '2025-04-11 13:20:13'),
(240, 'Trimebutine 100mg', 0, '2025-08-11 04:02:10', NULL, '2025-08-13 04:02:10'),
(241, 'Mebeverine 135mg', 0, '2025-07-22 04:39:30', NULL, '2025-08-07 04:39:30'),
(242, 'Ursodeoxycholic acid 250mg', 0, '2025-01-18 04:11:22', NULL, '2025-02-05 04:11:22'),
(243, 'Hep-Merz', 0, '2025-11-28 13:40:30', NULL, '2025-12-19 13:40:30'),
(244, 'Duphalac', 0, '2025-11-02 06:07:38', NULL, '2025-11-03 06:07:38'),
(245, 'Diazepam 5mg', 0, '2025-01-17 10:29:42', NULL, '2025-02-13 10:29:42'),
(246, 'Alprazolam 0.5mg', 0, '2025-06-05 19:15:46', NULL, '2025-06-16 19:15:46'),
(247, 'Clonazepam 0.5mg', 0, '2025-09-15 07:32:41', NULL, '2025-09-25 07:32:41'),
(248, 'Amitriptyline 25mg', 0, '2025-07-21 17:59:30', NULL, '2025-08-12 17:59:30'),
(249, 'Fluoxetine 20mg', 0, '2025-02-27 09:56:44', NULL, '2025-03-13 09:56:44'),
(250, 'Paroxetine 20mg', 0, '2025-01-06 03:02:27', NULL, '2025-01-23 03:02:27'),
(251, 'Escitalopram 10mg', 0, '2025-11-07 16:16:15', NULL, '2025-11-22 16:16:15'),
(252, 'Venlafaxine 75mg', 0, '2025-01-17 17:47:18', NULL, '2025-02-06 17:47:18'),
(253, 'Quetiapine 25mg', 0, '2025-03-18 23:32:30', NULL, '2025-03-19 23:32:30'),
(254, 'Olanzapine 5mg', 0, '2025-08-05 23:12:12', NULL, '2025-08-30 23:12:12'),
(255, 'Risperidone 2mg', 0, '2025-05-25 12:59:44', NULL, '2025-06-08 12:59:44'),
(256, 'Valproate 500mg', 0, '2025-03-05 14:05:25', NULL, '2025-03-18 14:05:25'),
(257, 'Carbamazepine 200mg', 0, '2025-09-30 06:45:21', NULL, '2025-10-11 06:45:21'),
(258, 'Levetiracetam 500mg', 0, '2025-08-10 01:33:14', NULL, '2025-09-06 01:33:14'),
(259, 'Piracetam 800mg', 0, '2025-10-17 16:43:32', NULL, '2025-10-23 16:43:32'),
(260, 'Betahistine 16mg', 0, '2025-08-18 15:48:22', NULL, '2025-09-03 15:48:22'),
(261, 'Naproxen 250mg', 0, '2025-10-24 05:47:37', NULL, '2025-11-06 05:47:37'),
(262, 'Ketoprofen 100mg', 0, '2025-10-04 15:49:51', NULL, '2025-10-18 15:49:51'),
(263, 'Etoricoxib 60mg', 0, '2025-02-05 08:38:54', NULL, '2025-02-06 08:38:54'),
(264, 'Tramadol 50mg', 0, '2025-01-07 21:50:26', NULL, '2025-02-03 21:50:26'),
(265, 'Colchicine 1mg', 0, '2025-06-28 01:08:18', NULL, '2025-07-19 01:08:18'),
(266, 'Allopurinol 100mg', 0, '2025-02-05 17:04:07', NULL, '2025-02-15 17:04:07'),
(267, 'Febuxostat 40mg', 0, '2025-06-21 02:03:36', NULL, '2025-06-29 02:03:36'),
(268, 'Calcium carbonate + Vitamin D3', 0, '2025-01-10 05:31:44', NULL, '2025-01-18 05:31:44'),
(269, 'Alendronate 70mg', 0, '2025-04-06 14:30:48', NULL, '2025-04-21 14:30:48'),
(270, 'Clotrimazole cream 1%', 0, '2025-10-01 09:59:50', NULL, '2025-10-07 09:59:50'),
(271, 'Ketoconazole cream 2%', 0, '2025-11-16 11:19:56', NULL, '2025-12-07 11:19:56'),
(272, 'Terbinafine 250mg', 0, '2025-11-21 05:20:46', NULL, '2025-11-30 05:20:46'),
(273, 'Mupirocin ointment', 0, '2025-12-31 23:35:35', NULL, '2025-12-31 23:35:35'),
(274, 'Fusidic acid cream', 0, '2025-01-02 16:09:39', NULL, '2025-01-02 16:09:39'),
(275, 'Benzoyl peroxide gel 5%', 0, '2025-02-01 22:43:09', NULL, '2025-02-12 22:43:09'),
(276, 'Adapalene gel 0.1%', 0, '2025-08-15 22:13:14', NULL, '2025-09-13 22:13:14'),
(277, 'Tretinoin cream 0.05%', 0, '2025-01-10 01:43:28', NULL, '2025-01-15 01:43:28'),
(278, 'Acyclovir cream 5%', 0, '2025-11-17 00:46:48', NULL, '2025-12-11 00:46:48'),
(279, 'Permethrin 5%', 0, '2025-06-08 23:18:31', NULL, '2025-06-30 23:18:31'),
(280, 'Tobramycin nhỏ mắt', 0, '2025-05-21 03:50:46', NULL, '2025-06-11 03:50:46'),
(281, 'Chloramphenicol nhỏ mắt', 0, '2025-05-18 07:18:23', NULL, '2025-06-09 07:18:23'),
(282, 'Ofloxacin nhỏ mắt', 0, '2025-09-10 08:12:28', NULL, '2025-09-14 08:12:28'),
(283, 'Nước mắt nhân tạo', 0, '2025-10-01 18:11:50', NULL, '2025-10-08 18:11:50'),
(284, 'Sắt fumarate + acid folic', 0, '2025-01-11 07:52:54', NULL, '2025-01-22 07:52:54'),
(285, 'Canxi corbiere', 0, '2025-10-15 09:58:45', NULL, '2025-11-08 09:58:45'),
(286, 'Progesterone 200mg', 0, '2025-09-15 22:40:36', NULL, '2025-09-17 22:40:36'),
(287, 'Magie B6', 0, '2025-05-05 05:02:49', NULL, '2025-05-17 05:02:49'),
(288, 'Metronidazole đặt âm đạo', 0, '2025-02-08 11:48:26', NULL, '2025-02-15 11:48:26'),
(289, 'Clotrimazole đặt âm đạo', 0, '2025-12-10 10:55:10', NULL, '2026-01-07 10:55:10'),
(290, 'Cranberry extract', 0, '2025-12-06 05:12:41', NULL, '2025-12-29 05:12:41'),
(291, 'Alfuzosin 10mg', 0, '2025-03-11 00:44:54', NULL, '2025-03-27 00:44:54'),
(292, 'Finasteride 5mg', 0, '2025-03-25 15:19:06', NULL, '2025-04-08 15:19:06'),
(293, 'Albendazole 400mg', 0, '2025-08-26 20:18:58', NULL, '2025-09-20 20:18:58'),
(294, 'Mebendazole 500mg', 0, '2025-04-27 08:08:45', NULL, '2025-04-27 08:08:45'),
(295, 'Praziquantel 600mg', 0, '2025-03-11 19:55:47', NULL, '2025-04-06 19:55:47'),
(296, 'Ivermectin 6mg', 0, '2025-10-07 17:21:14', NULL, '2025-10-13 17:21:14'),
(297, 'Vitamin A 5000IU', 0, '2025-11-03 06:21:48', NULL, '2025-11-18 06:21:48'),
(298, 'Vitamin D3 1000IU', 0, '2025-01-17 12:43:34', NULL, '2025-02-06 12:43:34'),
(299, 'Vitamin E 400IU', 0, '2025-04-23 16:31:38', NULL, '2025-05-07 16:31:38'),
(300, 'Kẽm gluconate 70mg', 0, '2025-06-17 07:29:20', NULL, '2025-07-12 07:29:20'),
(301, 'Multivitamin Centrum', 0, '2025-12-06 17:33:00', NULL, '2025-12-08 17:33:00'),
(302, 'Omega 3-6-9', 0, '2025-07-24 02:03:32', NULL, '2025-08-10 02:03:32'),
(303, 'Pancreatin (men tiêu hóa)', 0, '2025-04-01 01:10:18', NULL, '2025-04-15 01:10:18'),
(304, 'Acid tranexamic 250mg', 0, '2025-08-28 03:44:10', NULL, '2025-09-22 03:44:10'),
(305, 'Spasfon (Phloroglucinol)', 0, '2025-03-25 12:26:46', NULL, '2025-04-12 12:26:46'),
(306, 'Lidocaine 2%', 0, '2025-06-14 10:51:25', NULL, '2025-06-25 10:51:25'),
(307, 'Betadine 10%', 0, '2025-07-02 22:16:07', NULL, '2025-07-13 22:16:07'),
(308, 'Cồn y tế 70 độ', 0, '2025-06-20 13:24:00', NULL, '2025-06-24 13:24:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medicine_details`
--

CREATE TABLE `medicine_details` (
  `id` int(11) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `packing` varchar(60) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `medicine_details`
--

INSERT INTO `medicine_details` (`id`, `medicine_name`, `packing`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Amoxicillin', '50', 0, '2025-10-22 03:44:14', '2025-10-22 04:05:08', '2025-10-22 04:01:58'),
(2, 'Test222', '12222', 1, '2025-10-22 04:55:22', '2025-10-22 04:55:29', '2025-10-22 04:55:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(60) NOT NULL,
  `address` varchar(100) NOT NULL,
  `cnic` varchar(17) NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `gender` varchar(21) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patients`
--

INSERT INTO `patients` (`id`, `patient_name`, `address`, `cnic`, `date_of_birth`, `phone_number`, `gender`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Dương Thị Quỳnh', '44 Bạch Mai, Hai Bà Trưng, Hà Nội', '000170967902', '1973-11-22', '0707978200', 'nữ', 0, '2025-01-24 12:47:59', '2025-02-17 12:47:59', NULL),
(2, 'Lý Hoàng Phúc', '67 Nguyễn Tất Thành, Hạ Long, Quảng Ninh', '001163089252', '1967-05-09', '0307984837', 'nữ', 0, '2025-03-05 09:25:54', '2025-03-05 09:25:54', NULL),
(3, 'Đinh Thị Yến', '40 Võ Văn Tần, Quận 3, TP.HCM', '002146976133', '1978-06-23', '0173050697', 'nam', 0, '2025-01-22 22:34:36', '2025-02-01 22:34:36', NULL),
(4, 'Hồ Thị Phương', '9 Lê Lợi, Vinh, Nghệ An', '003156758445', '2002-11-22', '0522142380', 'nữ', 0, '2025-10-24 13:01:16', '2025-11-06 13:01:16', NULL),
(5, 'Tạ Thị Thanh', '21 Minh Khai, Hai Bà Trưng, Hà Nội', '004169443250', '1995-05-17', '0559588004', 'nữ', 0, '2025-05-20 12:46:17', '2025-06-03 12:46:17', NULL),
(6, 'Đỗ Đức Long', '19 Lê Duẩn, Hải Châu, Đà Nẵng', '005187296625', '2004-01-09', '0543129601', 'nam', 0, '2025-06-18 21:49:59', '2025-06-25 21:49:59', NULL),
(7, 'Lưu Văn Thắng', '28 Trần Phú, Nha Trang, Khánh Hòa', '006101860574', '1948-02-14', '0213955398', 'nữ', 0, '2025-11-24 15:51:43', '2025-12-22 15:51:43', NULL),
(8, 'Vũ Anh Tuấn', '28 Trần Phú, Nha Trang, Khánh Hòa', '000240445046', '1957-12-26', '0508040858', 'nam', 0, '2025-01-05 15:26:53', '2025-01-09 15:26:53', NULL),
(9, 'Ngô Văn Toàn', '76 Hoàng Diệu, Hải Phòng', '001260329724', '2002-06-21', '0438427552', 'nam', 0, '2025-11-25 15:55:46', '2025-11-30 15:55:46', NULL),
(10, 'Đặng Thùy Linh', '90 Nguyễn Chí Thanh, Đống Đa, Hà Nội', '002298680123', '2020-07-04', '0285142466', 'nam', 0, '2025-07-15 19:42:17', '2025-08-09 19:42:17', NULL),
(11, 'Dương Thị Quỳnh', '73 Phan Đăng Lưu, Bình Thạnh, TP.HCM', '003208723701', '1966-01-06', '0013224257', 'nam', 0, '2025-06-20 03:16:52', '2025-06-20 03:16:52', NULL),
(12, 'Tạ Thị Thanh', '40 Võ Văn Tần, Quận 3, TP.HCM', '004281572192', '2001-05-17', '0861987130', 'nữ', 0, '2025-01-14 05:37:25', '2025-02-01 05:37:25', NULL),
(13, 'Lưu Thị Hòa', '67 Nguyễn Tất Thành, Hạ Long, Quảng Ninh', '005236317397', '1995-12-15', '0521069228', 'nữ', 0, '2025-08-19 14:07:41', '2025-08-26 14:07:41', NULL),
(14, 'Trần Văn Hùng', '9 Lê Lợi, Vinh, Nghệ An', '006227309844', '1949-08-20', '0366259239', 'nam', 0, '2025-07-02 21:06:32', '2025-07-18 21:06:32', NULL),
(15, 'Hoàng Ngọc Mai', '15 Nguyễn Huệ, Quận 1, TP.HCM', '000373570239', '2003-10-18', '0967527495', 'nam', 0, '2025-10-06 00:30:06', '2025-10-18 00:30:06', NULL),
(16, 'Mai Văn Hiếu', '73 Phan Đăng Lưu, Bình Thạnh, TP.HCM', '001367161008', '1972-11-11', '0879419200', 'nữ', 0, '2025-08-31 12:47:41', '2025-09-10 12:47:41', NULL),
(17, 'Tạ Quốc Việt', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '002351428028', '1936-05-22', '0379171058', 'nữ', 0, '2025-07-30 03:22:31', '2025-07-31 03:22:31', NULL),
(18, 'Đinh Thị Yến', '200 Lê Văn Sỹ, Phú Nhuận, TP.HCM', '003381793825', '1966-04-27', '0804227410', 'nam', 0, '2025-12-02 09:21:38', '2025-12-13 09:21:38', NULL),
(19, 'Hoàng Ngọc Mai', '33 Phạm Văn Đồng, Thủ Đức, TP.HCM', '004392175802', '1947-09-13', '0527884838', 'nam', 0, '2025-09-25 21:45:06', '2025-10-09 21:45:06', NULL),
(20, 'Bùi Thanh Tùng', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '005309615888', '1946-06-19', '0073407956', 'nữ', 0, '2025-07-13 04:51:20', '2025-07-24 04:51:20', NULL),
(21, 'Lê Thị Ngọc', '10 Hùng Vương, Huế, Thừa Thiên Huế', '006376839417', '1982-11-22', '0031564146', 'nữ', 0, '2025-09-07 17:20:32', '2025-09-11 17:20:32', NULL),
(22, 'Tạ Quốc Việt', '85 Xuân Thủy, Cầu Giấy, Hà Nội', '000443091052', '1952-03-11', '0750664232', 'nam', 0, '2025-05-21 07:34:16', '2025-06-18 07:34:16', NULL),
(23, 'Đinh Thị Yến', '33 Phạm Văn Đồng, Thủ Đức, TP.HCM', '001495435924', '2023-10-17', '0199787409', 'nữ', 0, '2025-03-17 10:56:07', '2025-03-21 10:56:07', NULL),
(24, 'Vũ Anh Tuấn', '27 Láng Hạ, Đống Đa, Hà Nội', '002460454748', '1986-07-27', '0318724737', 'nam', 0, '2025-08-13 10:01:25', '2025-08-16 10:01:25', NULL),
(25, 'Chu Văn Lợi', '55 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', '003492278557', '1971-04-29', '0211487012', 'nam', 0, '2025-09-18 07:29:08', '2025-10-10 07:29:08', NULL),
(26, 'Dương Thị Quỳnh', '14 Lý Thường Kiệt, Hoàn Kiếm, Hà Nội', '004487625831', '1980-10-14', '0825004530', 'nữ', 0, '2025-10-15 21:33:01', '2025-11-13 21:33:01', NULL),
(27, 'Hồ Thanh Tâm', '9 Lê Lợi, Vinh, Nghệ An', '005456827877', '1998-11-09', '0744444953', 'nữ', 0, '2025-01-11 22:19:19', '2025-01-28 22:19:19', NULL),
(28, 'Lý Hoàng Phúc', '73 Phan Đăng Lưu, Bình Thạnh, TP.HCM', '006424071103', '1980-05-28', '0752879620', 'nam', 0, '2025-02-20 11:37:41', '2025-03-17 11:37:41', NULL),
(29, 'Lý Hoàng Phúc', '19 Lê Duẩn, Hải Châu, Đà Nẵng', '000594402224', '1969-02-28', '0268022370', 'nam', 0, '2025-08-06 14:55:55', '2025-08-22 14:55:55', NULL),
(30, 'Lưu Văn Thắng', '76 Hoàng Diệu, Hải Phòng', '001503943080', '1995-09-26', '0504495506', 'nữ', 0, '2025-03-26 03:30:35', '2025-04-10 03:30:35', NULL),
(31, 'Chu Thị Duyên', '55 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', '002588052781', '1992-04-19', '0172223646', 'nữ', 0, '2025-05-07 20:05:34', '2025-05-19 20:05:34', NULL),
(32, 'Trần Minh Quân', '39 Hồ Tùng Mậu, Cầu Giấy, Hà Nội', '003586948319', '1995-02-17', '0047929242', 'nam', 0, '2025-02-01 03:17:26', '2025-02-21 03:17:26', NULL),
(33, 'Bùi Thanh Tùng', '33 Phạm Văn Đồng, Thủ Đức, TP.HCM', '004583222165', '1978-02-03', '0106955258', 'nữ', 0, '2025-07-17 21:16:49', '2025-08-09 21:16:49', NULL),
(34, 'Trần Thị Hương', '90 Nguyễn Chí Thanh, Đống Đa, Hà Nội', '005554932977', '1952-04-02', '0392079407', 'nữ', 0, '2025-07-04 12:32:20', '2025-07-31 12:32:20', NULL),
(35, 'Lê Thị Thu Hà', '40 Võ Văn Tần, Quận 3, TP.HCM', '006590486407', '1967-01-16', '0503333724', 'nữ', 0, '2025-06-03 03:23:00', '2025-06-13 03:23:00', NULL),
(36, 'Phạm Thị Nga', '27 Láng Hạ, Đống Đa, Hà Nội', '000661658883', '1978-05-07', '0739713980', 'nam', 0, '2025-07-04 10:08:00', '2025-07-06 10:08:00', NULL),
(37, 'Lưu Thị Hòa', '135 Cách Mạng Tháng 8, Quận 3, TP.HCM', '001636268423', '1980-01-15', '0407277893', 'nữ', 0, '2025-06-23 01:18:36', '2025-07-15 01:18:36', NULL),
(38, 'Trần Thị Hương', '200 Lê Văn Sỹ, Phú Nhuận, TP.HCM', '002624648721', '1946-11-27', '0597874350', 'nam', 0, '2025-02-21 13:09:25', '2025-03-09 13:09:25', NULL),
(39, 'Trần Văn Hùng', '9 Lê Lợi, Vinh, Nghệ An', '003647574360', '1957-07-29', '0316962685', 'nam', 0, '2025-09-28 07:38:43', '2025-10-18 07:38:43', NULL),
(40, 'Hoàng Ngọc Mai', '39 Hồ Tùng Mậu, Cầu Giấy, Hà Nội', '004649017251', '1992-04-09', '0344854189', 'nữ', 0, '2025-02-21 08:19:56', '2025-03-16 08:19:56', NULL),
(41, 'Hồ Thanh Tâm', '200 Lê Văn Sỹ, Phú Nhuận, TP.HCM', '005654831545', '1990-01-09', '0295378572', 'nam', 0, '2025-09-05 05:37:18', '2025-09-18 05:37:18', NULL),
(42, 'Hoàng Ngọc Mai', '19 Lê Duẩn, Hải Châu, Đà Nẵng', '006691891145', '1983-04-19', '0557429463', 'nam', 0, '2025-04-28 03:34:57', '2025-05-10 03:34:57', NULL),
(43, 'Lê Thị Thu Hà', '27 Láng Hạ, Đống Đa, Hà Nội', '000765640992', '1958-03-16', '0739936268', 'nam', 0, '2025-02-25 03:38:11', '2025-03-06 03:38:11', NULL),
(44, 'Hoàng Ngọc Mai', '12 Nguyễn Trãi, Thanh Xuân, Hà Nội', '001765812475', '2014-07-29', '0601095574', 'nữ', 0, '2025-07-01 15:47:39', '2025-07-16 15:47:39', NULL),
(45, 'Nguyễn Bình An', '19 Lê Duẩn, Hải Châu, Đà Nẵng', '002740074478', '1945-10-14', '0197316105', 'nam', 0, '2025-02-21 22:34:33', '2025-03-13 22:34:33', NULL),
(46, 'Lưu Thị Hòa', '40 Võ Văn Tần, Quận 3, TP.HCM', '003734967872', '1946-10-30', '0292650422', 'nữ', 0, '2025-05-31 20:04:05', '2025-06-14 20:04:05', NULL),
(47, 'Hoàng Ngọc Mai', '40 Võ Văn Tần, Quận 3, TP.HCM', '004742071828', '2001-08-20', '0032610978', 'nam', 0, '2025-11-01 02:20:38', '2025-11-01 02:20:38', NULL),
(48, 'Đinh Thị Yến', '55 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', '005769032976', '1982-06-15', '0290073322', 'nam', 0, '2025-04-19 22:12:28', '2025-04-30 22:12:28', NULL),
(49, 'Trần Minh Quân', '102 Trần Duy Hưng, Cầu Giấy, Hà Nội', '006744326523', '1949-01-26', '0887205251', 'nữ', 0, '2025-11-13 15:24:47', '2025-12-01 15:24:47', NULL),
(50, 'Võ Thị Kim', '40 Võ Văn Tần, Quận 3, TP.HCM', '000865246318', '1991-03-26', '0915710612', 'nam', 0, '2025-07-12 02:40:43', '2025-07-20 02:40:43', NULL),
(51, 'Mai Văn Hiếu', '14 Lý Thường Kiệt, Hoàn Kiếm, Hà Nội', '001868278228', '1985-07-08', '0142441118', 'nam', 0, '2025-07-24 21:20:48', '2025-08-11 21:20:48', NULL),
(52, 'Lê Thị Ngọc', '12 Nguyễn Trãi, Thanh Xuân, Hà Nội', '002823459592', '1940-03-18', '0005745034', 'nam', 0, '2025-12-31 04:39:46', '2026-01-11 04:39:46', NULL),
(53, 'Lưu Văn Thắng', '21 Minh Khai, Hai Bà Trưng, Hà Nội', '003824059511', '2022-06-25', '0414120468', 'nữ', 0, '2025-09-14 13:41:38', '2025-09-30 13:41:38', NULL),
(54, 'Dương Thị Quỳnh', '15 Nguyễn Huệ, Quận 1, TP.HCM', '004861787125', '1972-02-15', '0081742426', 'nữ', 0, '2025-12-29 04:14:29', '2025-12-29 04:14:29', NULL),
(55, 'Phạm Quốc Bảo', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '005829935060', '1943-06-14', '0631471122', 'nam', 0, '2025-04-21 21:15:15', '2025-04-27 21:15:15', NULL),
(56, 'Lê Thị Thu Hà', '9 Lê Lợi, Vinh, Nghệ An', '006895468812', '2003-04-14', '0339091496', 'nữ', 0, '2025-11-07 10:46:45', '2025-11-16 10:46:45', NULL),
(57, 'Phạm Quốc Bảo', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '000940741893', '1989-03-12', '0764067248', 'nữ', 0, '2025-11-07 10:26:45', '2025-11-18 10:26:45', NULL),
(58, 'Nguyễn Thị Lan', '90 Nguyễn Chí Thanh, Đống Đa, Hà Nội', '001972317050', '1964-06-11', '0192890050', 'nữ', 0, '2025-02-27 01:51:36', '2025-03-27 01:51:36', NULL),
(59, 'Đỗ Đức Long', '44 Bạch Mai, Hai Bà Trưng, Hà Nội', '002906041227', '2003-02-10', '0031506042', 'nam', 0, '2025-01-21 00:57:51', '2025-02-19 00:57:51', NULL),
(60, 'Mai Văn Hiếu', '9 Lê Lợi, Vinh, Nghệ An', '003993889260', '2010-03-12', '0002259495', 'nữ', 0, '2025-08-05 09:46:48', '2025-08-17 09:46:48', NULL),
(61, 'Đỗ Đức Long', '39 Hồ Tùng Mậu, Cầu Giấy, Hà Nội', '004931347620', '1976-06-19', '0753629096', 'nam', 0, '2025-07-01 15:33:53', '2025-07-01 15:33:53', NULL),
(62, 'Ngô Văn Toàn', '18 Tôn Đức Thắng, Đống Đa, Hà Nội', '005930829832', '1957-11-29', '0800524906', 'nữ', 0, '2025-06-08 21:07:38', '2025-07-04 21:07:38', NULL),
(63, 'Trần Minh Quân', '19 Lê Duẩn, Hải Châu, Đà Nẵng', '006933180891', '1976-03-19', '0712546027', 'nữ', 0, '2025-07-21 12:20:54', '2025-08-18 12:20:54', NULL),
(64, 'Lê Thị Thu Hà', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '001049192796', '1950-08-17', '0654683713', 'nữ', 0, '2025-01-05 05:57:31', '2025-01-25 05:57:31', NULL),
(65, 'Trần Thị Hương', '40 Võ Văn Tần, Quận 3, TP.HCM', '002014606734', '1948-06-06', '0814365465', 'nữ', 0, '2025-02-27 19:02:26', '2025-03-05 19:02:26', NULL),
(66, 'Ngô Thị Trang', '19 Lê Duẩn, Hải Châu, Đà Nẵng', '003047848151', '1999-06-26', '0978953924', 'nam', 0, '2025-04-18 05:36:38', '2025-04-27 05:36:38', NULL),
(67, 'Mai Văn Hiếu', '9 Lê Lợi, Vinh, Nghệ An', '004001338375', '1982-08-18', '0311263343', 'nam', 0, '2025-10-17 15:29:51', '2025-11-01 15:29:51', NULL),
(68, 'Nguyễn Thị Lan', '10 Hùng Vương, Huế, Thừa Thiên Huế', '005010357290', '2010-08-24', '0483164309', 'nữ', 0, '2025-04-01 12:10:54', '2025-04-13 12:10:54', NULL),
(69, 'Lê Văn Cường', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '006066292957', '1965-03-25', '0329788204', 'nữ', 0, '2025-04-20 08:34:59', '2025-05-05 08:34:59', NULL),
(70, 'Tạ Quốc Việt', '12 Nguyễn Trãi, Thanh Xuân, Hà Nội', '007006892792', '2007-01-04', '0789170126', 'nam', 0, '2025-05-17 11:12:00', '2025-06-10 11:12:00', NULL),
(71, 'Hải Nguyễn', '39 Hồ Tùng Mậu', '025203005779', '2003-01-19', '0362111355', 'Nam', 0, '2025-12-04 10:04:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patient_diseases`
--

CREATE TABLE `patient_diseases` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `trieu_chung` text NOT NULL,
  `tien_su_benh` text NOT NULL,
  `huyet_ap` varchar(155) NOT NULL,
  `nhip_tim` varchar(155) NOT NULL,
  `can_nang` int(11) NOT NULL,
  `chieu_cao` int(11) NOT NULL,
  `nhiet_do` int(11) NOT NULL,
  `mach_dap` varchar(155) NOT NULL,
  `anh_sieu_am` varchar(155) DEFAULT NULL,
  `anh_chup_xq` varchar(155) DEFAULT NULL,
  `nhap_vien` int(11) NOT NULL COMMENT '0- ko nhập viện, 1 - nhập viện ',
  `is_deleted` int(11) NOT NULL,
  `chuan_doan` varchar(255) NOT NULL,
  `bien_phap` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `next_visit_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patient_diseases`
--

INSERT INTO `patient_diseases` (`id`, `patient_id`, `trieu_chung`, `tien_su_benh`, `huyet_ap`, `nhip_tim`, `can_nang`, `chieu_cao`, `nhiet_do`, `mach_dap`, `anh_sieu_am`, `anh_chup_xq`, `nhap_vien`, `is_deleted`, `chuan_doan`, `bien_phap`, `created_at`, `updated_at`, `deleted_at`, `next_visit_date`) VALUES
(1, 19, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '145/78', '80', 80, 148, 40, '96', NULL, NULL, 1, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-12-24 08:28:00', '2026-01-13 08:28:00', NULL, '2026-01-04'),
(2, 14, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '121/85', '60', 62, 154, 40, '97', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-08-07 03:07:24', '2025-08-30 03:07:24', NULL, '2025-09-02'),
(3, 3, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '121/62', '70', 70, 163, 38, '74', NULL, NULL, 0, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-10-02 18:42:26', '2025-10-14 18:42:26', NULL, '2025-10-19'),
(4, 58, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '136/61', '63', 45, 162, 40, '118', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-06-22 15:52:14', '2025-07-11 15:52:14', NULL, '2025-07-14'),
(5, 13, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '124/77', '83', 51, 145, 37, '92', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-03-12 21:39:57', '2025-03-24 21:39:57', NULL, '2025-04-24'),
(6, 14, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '103/71', '101', 55, 163, 37, '87', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-01-08 08:34:37', '2025-01-23 08:34:37', NULL, '2025-03-09'),
(7, 26, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '122/67', '116', 86, 178, 38, '96', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-03-26 04:58:41', '2025-03-27 04:58:41', NULL, '2025-05-18'),
(8, 42, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '110/73', '98', 82, 158, 36, '102', NULL, NULL, 1, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-12-09 04:37:15', '2025-12-18 04:37:15', NULL, '2025-12-30'),
(9, 15, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '123/74', '64', 84, 153, 38, '87', NULL, NULL, 1, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-05-24 09:32:18', '2025-06-16 09:32:18', NULL, '2025-07-16'),
(10, 30, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '144/87', '117', 40, 152, 40, '102', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-07-14 09:31:30', '2025-08-08 09:31:30', NULL, '2025-08-06'),
(11, 52, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '146/81', '106', 79, 168, 38, '115', NULL, NULL, 1, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-09-29 06:46:29', '2025-10-14 06:46:29', NULL, '2025-10-23'),
(12, 58, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '106/80', '60', 40, 184, 40, '95', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-06-22 01:31:50', '2025-07-02 01:31:50', NULL, '2025-06-30'),
(13, 51, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '105/73', '113', 47, 147, 40, '115', NULL, NULL, 1, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-09-18 10:26:19', '2025-10-17 10:26:19', NULL, '2025-10-19'),
(14, 38, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '116/68', '87', 61, 175, 38, '88', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-09-29 10:26:09', '2025-09-29 10:26:09', NULL, '2025-10-09'),
(15, 19, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '110/87', '113', 78, 150, 38, '95', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-10-24 01:50:10', '2025-11-22 01:50:10', NULL, '2025-12-04'),
(16, 57, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '111/67', '93', 43, 172, 37, '62', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-09-23 09:59:43', '2025-10-06 09:59:43', NULL, '2025-11-10'),
(17, 46, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '124/63', '68', 57, 157, 38, '104', NULL, NULL, 1, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-11-06 10:59:29', '2025-11-16 10:59:29', NULL, '2025-12-03'),
(18, 22, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '142/62', '114', 53, 170, 38, '67', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-05-28 13:04:14', '2025-06-16 13:04:14', NULL, '2025-07-27'),
(19, 50, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '149/61', '72', 87, 148, 39, '109', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-12-14 04:31:41', '2026-01-11 04:31:41', NULL, '2026-02-09'),
(20, 66, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '139/63', '72', 76, 184, 39, '118', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-07-19 23:23:00', '2025-08-02 23:23:00', NULL, '2025-09-17'),
(21, 23, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '125/77', '79', 84, 165, 40, '99', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-08-30 12:54:29', '2025-09-26 12:54:29', NULL, '2025-09-15'),
(22, 32, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '111/76', '65', 79, 171, 40, '114', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-09-09 22:24:36', '2025-09-16 22:24:36', NULL, '2025-10-08'),
(23, 22, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '116/73', '72', 76, 147, 36, '69', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-07-03 16:33:43', '2025-07-22 16:33:43', NULL, '2025-07-25'),
(24, 9, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '127/87', '110', 65, 184, 37, '62', NULL, NULL, 1, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-06-15 02:28:10', '2025-07-12 02:28:10', NULL, '2025-07-22'),
(25, 25, 'Đau lưng, lan xuống chân, tê bì', 'Thoát vị đĩa đệm', '105/84', '103', 48, 173, 36, '69', NULL, NULL, 0, 0, 'Đau thần kinh tọa / Thoát vị đĩa đệm', 'Giảm đau, giãn cơ, tập phục hồi', '2025-10-01 07:21:58', '2025-10-19 07:21:58', NULL, '2025-10-21'),
(26, 51, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '119/65', '105', 50, 177, 38, '103', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-12-22 07:54:01', '2026-01-04 07:54:01', NULL, '2026-01-08'),
(27, 16, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '135/88', '96', 48, 146, 39, '88', NULL, NULL, 0, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-03-07 11:51:37', '2025-03-24 11:51:37', NULL, '2025-04-03'),
(28, 53, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '103/68', '67', 80, 172, 40, '109', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-09-09 16:43:02', '2025-09-24 16:43:02', NULL, '2025-09-16'),
(29, 39, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '125/75', '61', 70, 183, 40, '110', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-01-09 19:10:38', '2025-01-18 19:10:38', NULL, '2025-02-11'),
(30, 46, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '123/87', '66', 79, 172, 40, '115', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-08-30 01:23:17', '2025-09-13 01:23:17', NULL, '2025-09-27'),
(31, 8, 'Đau lưng, lan xuống chân, tê bì', 'Thoát vị đĩa đệm', '130/84', '78', 43, 160, 39, '98', NULL, NULL, 0, 0, 'Đau thần kinh tọa / Thoát vị đĩa đệm', 'Giảm đau, giãn cơ, tập phục hồi', '2025-12-29 08:20:37', '2026-01-18 08:20:37', NULL, '2026-02-09'),
(32, 65, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '110/63', '115', 52, 165, 39, '83', NULL, NULL, 0, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-12-07 20:07:00', '2026-01-05 20:07:00', NULL, '2026-02-03'),
(33, 58, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '143/73', '102', 47, 168, 38, '119', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-08-23 21:55:37', '2025-09-07 21:55:37', NULL, '2025-10-09'),
(34, 50, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '106/73', '111', 84, 181, 40, '106', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-01-12 03:56:25', '2025-01-24 03:56:25', NULL, '2025-02-25'),
(35, 8, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '108/83', '86', 81, 177, 38, '78', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-02-24 18:09:46', '2025-03-16 18:09:46', NULL, '2025-04-09'),
(36, 53, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '119/85', '64', 81, 183, 37, '94', NULL, NULL, 1, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-06-21 06:38:01', '2025-06-29 06:38:01', NULL, '2025-07-20'),
(37, 29, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '112/60', '77', 60, 152, 39, '117', NULL, NULL, 0, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-07-10 14:05:11', '2025-07-27 14:05:11', NULL, '2025-09-01'),
(38, 27, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '125/88', '75', 61, 161, 39, '87', NULL, NULL, 1, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-09-14 04:40:32', '2025-09-18 04:40:32', NULL, '2025-10-10'),
(39, 40, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '119/86', '71', 58, 155, 37, '75', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-04-27 11:28:59', '2025-05-14 11:28:59', NULL, '2025-06-17'),
(40, 62, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '122/81', '77', 55, 172, 38, '95', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-06-20 02:22:04', '2025-06-21 02:22:04', NULL, '2025-07-08'),
(41, 7, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '143/80', '109', 43, 179, 36, '67', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-04-04 11:33:32', '2025-04-22 11:33:32', NULL, '2025-05-08'),
(42, 26, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '133/84', '63', 82, 148, 40, '78', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-01-24 03:19:48', '2025-02-19 03:19:48', NULL, '2025-02-04'),
(43, 18, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '140/84', '98', 76, 173, 38, '115', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-05-26 19:40:28', '2025-05-28 19:40:28', NULL, '2025-06-21'),
(44, 26, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '124/70', '76', 57, 183, 39, '106', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-03-31 13:51:45', '2025-04-02 13:51:45', NULL, '2025-04-29'),
(45, 35, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '140/83', '86', 86, 155, 38, '119', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-05-30 06:16:35', '2025-06-16 06:16:35', NULL, '2025-07-25'),
(46, 68, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '142/74', '117', 54, 167, 40, '60', NULL, NULL, 0, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-09-20 03:16:40', '2025-09-23 03:16:40', NULL, '2025-11-15'),
(47, 4, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '111/72', '80', 63, 157, 37, '64', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-02-07 12:08:53', '2025-02-19 12:08:53', NULL, '2025-04-05'),
(48, 35, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '119/64', '98', 75, 170, 36, '78', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-05-02 16:05:43', '2025-05-03 16:05:43', NULL, '2025-05-13'),
(49, 42, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '112/61', '87', 46, 157, 36, '105', NULL, NULL, 0, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-11-19 20:21:52', '2025-12-12 20:21:52', NULL, '2025-12-29'),
(50, 9, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '140/65', '85', 69, 174, 40, '119', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-03-24 08:14:04', '2025-04-03 08:14:04', NULL, '2025-04-17'),
(51, 13, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '130/62', '96', 79, 149, 36, '84', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-02-08 18:06:40', '2025-03-04 18:06:40', NULL, '2025-03-03'),
(52, 17, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '103/71', '102', 60, 182, 38, '93', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-05-13 04:34:29', '2025-06-11 04:34:29', NULL, '2025-07-07'),
(53, 9, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '126/88', '74', 56, 180, 38, '96', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-09-05 10:17:19', '2025-09-24 10:17:19', NULL, '2025-09-23'),
(54, 25, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '103/81', '82', 77, 169, 39, '62', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-04-03 19:56:27', '2025-04-16 19:56:27', NULL, '2025-05-04'),
(55, 20, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '149/76', '103', 89, 177, 36, '105', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-02-25 16:56:10', '2025-03-02 16:56:10', NULL, '2025-04-17'),
(56, 51, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '127/69', '117', 80, 152, 38, '118', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-03-11 16:08:58', '2025-04-05 16:08:58', NULL, '2025-03-30'),
(57, 43, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '129/66', '77', 83, 162, 38, '88', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-11-07 07:29:51', '2025-11-13 07:29:51', NULL, '2025-12-29'),
(58, 10, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '129/73', '87', 86, 155, 38, '107', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-09-17 15:56:19', '2025-10-09 15:56:19', NULL, '2025-10-17'),
(59, 59, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '149/77', '116', 40, 153, 36, '99', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-12-12 21:40:52', '2025-12-31 21:40:52', NULL, '2026-02-03'),
(60, 60, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '119/69', '85', 48, 168, 37, '75', NULL, NULL, 1, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-09-20 08:09:37', '2025-10-05 08:09:37', NULL, '2025-10-19'),
(61, 40, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '127/73', '101', 44, 159, 38, '105', NULL, NULL, 1, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-11-11 10:56:43', '2025-11-17 10:56:43', NULL, '2025-12-30'),
(62, 32, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '114/62', '95', 73, 169, 36, '84', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-12-15 18:16:11', '2025-12-24 18:16:11', NULL, '2026-02-02'),
(63, 40, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '101/83', '111', 84, 180, 40, '81', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-01-05 18:31:49', '2025-01-29 18:31:49', NULL, '2025-02-28'),
(64, 35, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '149/69', '99', 56, 170, 37, '78', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-08-03 21:36:45', '2025-09-01 21:36:45', NULL, '2025-09-10'),
(65, 28, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '147/61', '78', 63, 160, 38, '79', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-07-23 05:41:00', '2025-08-18 05:41:00', NULL, '2025-09-15'),
(66, 52, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '137/64', '85', 75, 156, 37, '87', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-07-20 06:48:54', '2025-07-24 06:48:54', NULL, '2025-08-12'),
(67, 61, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '101/66', '116', 46, 177, 39, '66', NULL, NULL, 0, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-02-15 16:53:16', '2025-03-10 16:53:16', NULL, '2025-03-31'),
(68, 41, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '102/65', '104', 47, 167, 37, '116', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-04-14 21:19:16', '2025-05-13 21:19:16', NULL, '2025-05-23'),
(69, 47, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '103/76', '92', 41, 166, 38, '72', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-08-18 17:41:34', '2025-08-21 17:41:34', NULL, '2025-09-25'),
(70, 16, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '128/63', '107', 73, 182, 39, '106', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-11-08 15:41:25', '2025-11-16 15:41:25', NULL, '2025-11-29'),
(71, 54, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '124/78', '95', 45, 177, 39, '119', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-05-28 22:52:00', '2025-06-14 22:52:00', NULL, '2025-06-11'),
(72, 11, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '148/73', '80', 56, 169, 36, '85', NULL, NULL, 1, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-12-24 05:56:27', '2026-01-16 05:56:27', NULL, '2026-02-19'),
(73, 44, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '112/73', '90', 49, 161, 38, '78', NULL, NULL, 1, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-02-16 01:53:00', '2025-02-23 01:53:00', NULL, '2025-02-28'),
(74, 44, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '138/77', '93', 44, 175, 38, '87', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-02-08 18:58:25', '2025-03-03 18:58:25', NULL, '2025-04-09'),
(75, 66, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '130/62', '95', 76, 180, 37, '95', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-01-05 00:03:36', '2025-01-10 00:03:36', NULL, '2025-01-28'),
(76, 7, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '149/62', '81', 69, 178, 38, '97', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-01-05 20:07:57', '2025-01-17 20:07:57', NULL, '2025-02-11'),
(77, 53, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '124/84', '96', 70, 154, 37, '105', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-10-01 01:43:22', '2025-10-08 01:43:22', NULL, '2025-11-05'),
(78, 54, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '144/85', '91', 47, 152, 38, '106', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-01-13 17:48:47', '2025-01-17 17:48:47', NULL, '2025-01-30'),
(79, 46, 'Đau lưng, lan xuống chân, tê bì', 'Thoát vị đĩa đệm', '128/66', '88', 73, 182, 39, '101', NULL, NULL, 0, 0, 'Đau thần kinh tọa / Thoát vị đĩa đệm', 'Giảm đau, giãn cơ, tập phục hồi', '2025-09-04 00:42:45', '2025-09-23 00:42:45', NULL, '2025-09-26'),
(80, 48, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '120/66', '113', 80, 157, 36, '119', NULL, NULL, 0, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-02-19 12:53:30', '2025-03-16 12:53:30', NULL, '2025-03-04'),
(81, 34, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '104/63', '76', 43, 164, 37, '98', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-12-21 15:05:32', '2026-01-11 15:05:32', NULL, '2025-12-30'),
(82, 21, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '101/61', '70', 75, 147, 36, '95', NULL, NULL, 0, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-05-16 05:50:51', '2025-06-05 05:50:51', NULL, '2025-07-15'),
(83, 11, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '142/68', '114', 73, 171, 37, '73', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-11-29 15:31:11', '2025-12-11 15:31:11', NULL, '2026-01-18'),
(84, 35, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '141/81', '62', 42, 152, 39, '65', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-05-28 03:50:22', '2025-05-30 03:50:22', NULL, '2025-07-10'),
(85, 35, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '102/68', '71', 46, 149, 36, '93', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-03-17 10:40:54', '2025-04-04 10:40:54', NULL, '2025-04-06'),
(86, 8, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '121/71', '96', 86, 178, 37, '85', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-12-16 00:57:01', '2025-12-31 00:57:01', NULL, '2026-01-30'),
(87, 27, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '101/89', '110', 53, 175, 36, '65', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-07-30 12:23:21', '2025-08-25 12:23:21', NULL, '2025-09-14'),
(88, 25, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '101/87', '91', 84, 177, 38, '112', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-03-08 11:36:20', '2025-03-12 11:36:20', NULL, '2025-05-04'),
(89, 57, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '108/62', '111', 42, 172, 37, '92', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-07-08 09:00:19', '2025-08-05 09:00:19', NULL, '2025-08-14'),
(90, 59, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '149/68', '84', 52, 183, 36, '91', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-01-23 06:50:31', '2025-02-02 06:50:31', NULL, '2025-03-02'),
(91, 8, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '144/80', '105', 78, 166, 37, '69', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-05-23 01:25:56', '2025-06-01 01:25:56', NULL, '2025-06-12'),
(92, 15, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '115/83', '62', 81, 145, 39, '61', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-01-06 09:25:15', '2025-01-12 09:25:15', NULL, '2025-02-01'),
(93, 4, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '107/78', '96', 52, 162, 38, '104', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-03-27 02:48:48', '2025-03-31 02:48:48', NULL, '2025-04-17'),
(94, 19, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '146/85', '86', 72, 182, 39, '108', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-10-07 20:20:35', '2025-11-02 20:20:35', NULL, '2025-11-27'),
(95, 7, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '123/85', '114', 87, 145, 37, '70', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-06-15 10:29:31', '2025-06-21 10:29:31', NULL, '2025-07-28'),
(96, 63, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '136/78', '110', 58, 159, 39, '89', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-06-12 19:40:29', '2025-07-07 19:40:29', NULL, '2025-07-17'),
(97, 32, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '102/80', '78', 64, 164, 40, '80', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-11-03 22:20:57', '2025-11-04 22:20:57', NULL, '2025-12-26'),
(98, 8, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '105/89', '98', 52, 156, 39, '102', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-12-02 08:03:04', '2025-12-24 08:03:04', NULL, '2026-01-09'),
(99, 18, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '136/87', '81', 41, 147, 37, '119', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-07-27 10:58:37', '2025-08-03 10:58:37', NULL, '2025-08-30'),
(100, 56, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '140/76', '75', 72, 165, 39, '91', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-05-24 19:45:31', '2025-05-29 19:45:31', NULL, '2025-07-09'),
(101, 65, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '102/61', '68', 65, 152, 37, '70', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-07-15 11:19:35', '2025-08-06 11:19:35', NULL, '2025-08-02'),
(102, 36, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '140/72', '99', 40, 148, 38, '110', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-06-02 23:43:22', '2025-06-04 23:43:22', NULL, '2025-07-15'),
(103, 44, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '106/78', '105', 83, 148, 40, '118', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-02-12 21:46:21', '2025-03-05 21:46:21', NULL, '2025-03-23'),
(104, 38, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '144/78', '82', 41, 147, 37, '118', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-07-21 20:14:09', '2025-08-14 20:14:09', NULL, '2025-08-29'),
(105, 64, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '128/61', '93', 71, 162, 37, '86', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-10-11 12:54:53', '2025-10-20 12:54:53', NULL, '2025-10-27'),
(106, 10, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '146/64', '114', 44, 176, 39, '111', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-03-13 16:32:44', '2025-03-21 16:32:44', NULL, '2025-04-07'),
(107, 40, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '141/64', '71', 68, 156, 39, '90', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-08-31 14:04:30', '2025-09-02 14:04:30', NULL, '2025-10-20'),
(108, 43, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '140/79', '108', 43, 181, 37, '67', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-07-27 07:58:21', '2025-08-03 07:58:21', NULL, '2025-09-06'),
(109, 67, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '125/77', '80', 41, 147, 37, '71', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-06-22 20:53:30', '2025-06-23 20:53:30', NULL, '2025-08-11'),
(110, 33, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '146/67', '87', 64, 150, 37, '104', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-10-06 17:45:49', '2025-10-26 17:45:49', NULL, '2025-11-11'),
(111, 70, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '130/73', '85', 78, 167, 38, '109', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-03-03 14:41:23', '2025-03-26 14:41:23', NULL, '2025-04-27'),
(112, 15, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '107/62', '116', 64, 169, 39, '76', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-03-25 13:49:30', '2025-04-10 13:49:30', NULL, '2025-04-19'),
(113, 43, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '102/67', '61', 60, 183, 39, '68', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-04-24 23:51:46', '2025-04-26 23:51:46', NULL, '2025-06-07'),
(114, 43, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '110/89', '77', 63, 166, 37, '97', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-06-03 12:03:41', '2025-06-03 12:03:41', NULL, '2025-08-02'),
(115, 41, 'Mẩn ngứa, hắt hơi, sổ mũi', 'Dị ứng thời tiết', '143/70', '67', 69, 168, 36, '119', NULL, NULL, 0, 0, 'Viêm mũi dị ứng / Mề đay', 'Thuốc chống dị ứng, tránh tác nhân', '2025-06-12 16:57:13', '2025-06-27 16:57:13', NULL, '2025-06-22'),
(116, 55, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '140/86', '117', 47, 180, 40, '69', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-12-07 14:32:24', '2025-12-08 14:32:24', NULL, '2026-01-08'),
(117, 40, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '112/84', '82', 61, 145, 39, '116', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-12-07 07:55:41', '2025-12-27 07:55:41', NULL, '2026-01-11'),
(118, 27, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '130/73', '84', 76, 159, 39, '67', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-03-30 16:24:38', '2025-04-05 16:24:38', NULL, '2025-05-27'),
(119, 57, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '106/83', '89', 46, 150, 37, '83', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-07-16 02:35:24', '2025-07-20 02:35:24', NULL, '2025-08-03'),
(120, 10, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '128/66', '78', 85, 171, 39, '64', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-07-26 09:58:07', '2025-08-01 09:58:07', NULL, '2025-09-06'),
(121, 34, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '129/89', '72', 42, 169, 40, '96', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-10-07 04:03:45', '2025-10-12 04:03:45', NULL, '2025-11-24'),
(122, 55, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '112/87', '114', 76, 183, 38, '116', NULL, NULL, 1, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-05-05 02:12:56', '2025-05-19 02:12:56', NULL, '2025-05-23'),
(123, 12, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '129/69', '113', 63, 171, 40, '96', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-11-06 22:58:00', '2025-11-18 22:58:00', NULL, '2025-12-02'),
(124, 53, 'Đau khớp gối/khớp tay, cứng khớp buổi sáng', 'Thoái hóa khớp', '125/76', '66', 87, 162, 37, '79', NULL, NULL, 0, 0, 'Thoái hóa khớp', 'Giảm đau, vật lý trị liệu', '2025-06-30 19:38:36', '2025-07-09 19:38:36', NULL, '2025-08-06'),
(125, 59, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '145/85', '93', 51, 163, 39, '104', NULL, NULL, 0, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-07-27 01:30:42', '2025-08-25 01:30:42', NULL, '2025-08-21'),
(126, 33, 'Đau lưng, lan xuống chân, tê bì', 'Thoát vị đĩa đệm', '138/85', '60', 60, 145, 40, '80', NULL, NULL, 1, 0, 'Đau thần kinh tọa / Thoát vị đĩa đệm', 'Giảm đau, giãn cơ, tập phục hồi', '2025-11-01 16:33:23', '2025-11-10 16:33:23', NULL, '2025-11-30'),
(127, 16, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '105/67', '60', 51, 150, 36, '107', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-04-03 07:52:28', '2025-04-24 07:52:28', NULL, '2025-04-19'),
(128, 66, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '135/89', '106', 87, 163, 38, '107', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-05-04 14:53:30', '2025-05-29 14:53:30', NULL, '2025-05-31'),
(129, 35, 'Đau đầu, chóng mặt, tê tay chân', 'Tiền sử tăng huyết áp', '113/67', '89', 72, 175, 40, '77', NULL, NULL, 0, 0, 'Tăng huyết áp', 'Thuốc hạ áp, giảm muối, theo dõi định kỳ', '2025-11-02 08:15:02', '2025-11-20 08:15:02', NULL, '2025-12-28'),
(130, 7, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '138/63', '77', 43, 165, 37, '119', NULL, NULL, 1, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-02-28 21:37:43', '2025-03-10 21:37:43', NULL, '2025-04-06'),
(131, 35, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '140/68', '64', 63, 150, 37, '63', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-12-13 15:21:02', '2026-01-07 15:21:02', NULL, '2025-12-21'),
(132, 14, 'Sốt, ho, đau rát họng, mất vị giác', 'Tiền sử COVID/viêm hô hấp', '130/87', '108', 52, 180, 39, '83', NULL, NULL, 1, 0, 'COVID-19', 'Hạ sốt, theo dõi SpO2, thuốc theo phác đồ', '2025-09-22 19:03:57', '2025-10-13 19:03:57', NULL, '2025-11-20'),
(133, 37, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '139/60', '102', 63, 153, 39, '100', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-11-17 10:36:15', '2025-12-11 10:36:15', NULL, '2026-01-15'),
(134, 22, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '121/67', '116', 87, 181, 39, '110', NULL, NULL, 1, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-09-15 13:25:46', '2025-10-07 13:25:46', NULL, '2025-10-20'),
(135, 32, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '120/75', '80', 48, 178, 39, '107', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-03-07 01:27:09', '2025-03-17 01:27:09', NULL, '2025-05-02'),
(136, 48, 'Khó ngủ, lo âu, tim đập nhanh', 'Rối loạn lo âu', '127/60', '82', 82, 149, 40, '98', NULL, NULL, 0, 0, 'Rối loạn lo âu / Mất ngủ', 'Tư vấn tâm lý, vệ sinh giấc ngủ', '2025-07-12 04:56:51', '2025-07-22 04:56:51', NULL, '2025-07-21'),
(137, 56, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '106/75', '73', 67, 147, 39, '80', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-04-02 21:05:54', '2025-05-01 21:05:54', NULL, '2025-04-16'),
(138, 58, 'Ho có đờm, đau ngực, khó thở nhẹ', 'Hay viêm phế quản tái phát', '137/69', '82', 86, 164, 39, '111', NULL, NULL, 0, 0, 'Viêm phế quản', 'Kháng viêm/giảm ho, theo dõi hô hấp', '2025-07-20 08:56:57', '2025-08-15 08:56:57', NULL, '2025-08-26'),
(139, 31, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '106/89', '92', 78, 154, 40, '87', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-09-15 02:57:54', '2025-10-02 02:57:54', NULL, '2025-10-19'),
(140, 29, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '139/72', '106', 69, 169, 37, '105', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-05-27 05:35:48', '2025-06-23 05:35:48', NULL, '2025-06-05'),
(141, 34, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '123/67', '110', 61, 171, 36, '67', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-09-29 14:18:20', '2025-10-13 14:18:20', NULL, '2025-11-15'),
(142, 17, 'Đau thượng vị, ợ chua, đầy hơi', 'Viêm dạ dày tá tràng', '113/61', '90', 57, 155, 37, '90', NULL, NULL, 0, 0, 'Viêm dạ dày - trào ngược', 'Thuốc dạ dày, ăn uống điều độ, tránh đồ cay', '2025-02-11 00:32:07', '2025-02-19 00:32:07', NULL, '2025-02-23'),
(143, 50, 'Mệt mỏi, chán ăn, vàng da nhẹ', 'Viêm gan B mạn', '135/65', '110', 70, 167, 36, '80', NULL, NULL, 0, 0, 'Viêm gan B mạn', 'Theo dõi men gan, điều trị kháng virus khi cần', '2025-08-01 10:14:09', '2025-08-15 10:14:09', NULL, '2025-08-25'),
(144, 16, 'Đau bụng, tiêu chảy, buồn nôn', 'Rối loạn tiêu hóa', '106/81', '70', 75, 145, 40, '101', NULL, NULL, 0, 0, 'Rối loạn tiêu hóa', 'Bù nước điện giải, men tiêu hóa', '2025-02-01 23:21:34', '2025-02-27 23:21:34', NULL, '2025-03-16'),
(145, 2, 'Sốt, ho khan, đau họng, mệt mỏi', 'Hay cảm cúm theo mùa', '135/75', '88', 79, 165, 37, '88', NULL, NULL, 0, 0, 'Viêm đường hô hấp trên / Cúm', 'Hạ sốt, giảm ho, nghỉ ngơi, uống nhiều nước', '2025-05-22 17:56:18', '2025-06-07 17:56:18', NULL, '2025-06-18'),
(146, 47, 'Tiểu nhiều, khát nước, sụt cân', 'Đái tháo đường type 2', '111/61', '92', 66, 147, 39, '68', NULL, NULL, 0, 0, 'Đái tháo đường type 2', 'Kiểm soát đường huyết, ăn kiêng, metformin', '2025-06-13 19:00:28', '2025-06-24 19:00:28', NULL, '2025-07-19'),
(147, 27, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '132/76', '112', 73, 174, 39, '76', NULL, NULL, 1, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-12-31 01:48:37', '2026-01-12 01:48:37', NULL, '2026-01-29'),
(148, 52, 'Đau hông lưng, tiểu buốt/rát, tiểu ít', 'Sỏi thận/viêm tiết niệu', '137/76', '84', 63, 149, 36, '75', NULL, NULL, 0, 0, 'Sỏi thận / Nhiễm trùng tiểu', 'Uống nhiều nước, giảm đau, kháng sinh nếu nhiễm trùng', '2025-04-13 10:05:33', '2025-05-10 10:05:33', NULL, '2025-05-30'),
(149, 7, 'Sốt cao đột ngột, đau hốc mắt, đau cơ, phát ban', 'Từng sốt xuất huyết', '101/85', '73', 67, 149, 40, '62', NULL, NULL, 0, 0, 'Sốt xuất huyết Dengue', 'Theo dõi tiểu cầu, bù dịch, hạ sốt đúng cách', '2025-06-09 12:09:14', '2025-07-01 12:09:14', NULL, '2025-06-17'),
(150, 29, 'Nặng bụng, mệt, tăng cân', 'Rối loạn mỡ máu', '143/67', '99', 65, 169, 38, '99', NULL, NULL, 0, 0, 'Gan nhiễm mỡ / Rối loạn lipid', 'Ăn giảm mỡ, tập luyện, kiểm soát lipid', '2025-04-29 13:06:56', '2025-05-04 13:06:56', NULL, '2025-05-24'),
(151, 71, 'Đau Đầu, chóng mặt, Buồn nôn, Ho nhiều', 'Covid-19', '120/50', '90', 50, 170, 37, '90', 'uploads/anhsieuam/1765042136_Hoa Màu Nước Thanh Lịch Bài Đăng Facebook Chúc Mừng Ngày 2011 Sang Trọng (2).png', 'uploads/xquang/1765042136_Hoa Màu Nước Thanh Lịch Bài Đăng Facebook Chúc Mừng Ngày 2011 Sang Trọng (2).png', 2, 0, 'Tái Covid-19', 'Cách ly', '2025-12-07 00:28:56', NULL, NULL, '2025-12-14'),
(152, 71, 'Đau nhức xương khớp', 'Không có', '120/50', '90', 50, 170, 37, '90', NULL, NULL, 2, 0, 'Thiếu Canxi', 'Bổ sung vitamin', '2025-12-07 00:53:54', NULL, NULL, '2025-12-21'),
(153, 71, 'Đau dạ dày, ợ nóng', 'Viêm dạ loét dạ dày', '120/50', '90', 50, 170, 37, '90', NULL, NULL, 1, 0, 'Viêm dạ loét dạ dày tái phát', 'Tránh ăn đồ cay nóng, uống thuốc đúng giờ', '2025-12-07 01:05:38', NULL, NULL, '2025-12-16'),
(154, 20, 'Đau dạ dày, ợ nóng', 'Viêm dạ loét dạ dày', '120/50', '90', 50, 170, 37, '90', NULL, NULL, 1, 0, 'Viêm dạ loét dạ dày tái phát', 'Tránh ăn đồ cay nóng, uống thuốc đúng giờ', '2025-12-07 01:06:24', NULL, NULL, '2025-12-16'),
(155, 71, 'Đau bụng dữ dội', 'Viêm loét dạ dày cấp', '120/50', '70', 50, 170, 37, '70', NULL, NULL, 2, 0, 'Viêm loét dạ dày tái lại', 'Ăn uống ngủ nghỉ đúng giờ', '2025-12-07 01:58:20', NULL, NULL, '2025-12-08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patient_medication_history`
--

CREATE TABLE `patient_medication_history` (
  `id` int(11) NOT NULL,
  `quantity` tinyint(4) NOT NULL,
  `dosage` text DEFAULT NULL,
  `note` text NOT NULL,
  `patient_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patient_medication_history`
--

INSERT INTO `patient_medication_history` (`id`, `quantity`, `dosage`, `note`, `patient_id`, `medicine_id`, `created_at`, `is_deleted`) VALUES
(1, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 33, 53, '2025-03-22 16:29:54', 0),
(2, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 33, 57, '2025-07-22 11:08:35', 0),
(3, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 58, 43, '2025-07-20 15:14:10', 0),
(4, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 58, 45, '2025-06-14 21:12:09', 0),
(5, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 18, 17, '2025-08-21 01:43:35', 0),
(6, 27, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 18, 21, '2025-01-06 09:59:03', 0),
(7, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 32, 17, '2025-07-24 06:09:52', 0),
(8, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 32, 21, '2025-07-02 03:16:32', 0),
(9, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 16, 4, '2025-06-12 06:45:30', 0),
(10, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 16, 8, '2025-12-16 02:56:03', 0),
(11, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 35, 17, '2025-03-04 18:55:54', 0),
(12, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 35, 21, '2025-08-29 21:17:49', 0),
(13, 59, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 8, 46, '2025-12-28 18:15:43', 0),
(14, 59, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 8, 47, '2025-11-22 11:18:37', 0),
(15, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 52, 61, '2025-01-09 02:32:37', 0),
(16, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 52, 63, '2025-05-25 18:32:11', 0),
(17, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 38, 43, '2025-05-03 17:18:16', 0),
(18, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 38, 45, '2025-10-26 17:57:41', 0),
(19, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 4, 61, '2025-07-11 17:31:54', 0),
(20, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 4, 63, '2025-08-13 03:27:17', 0),
(21, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 20, 1, '2025-12-10 03:51:30', 0),
(22, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 20, 5, '2025-05-03 18:13:21', 0),
(23, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 14, 1, '2025-12-05 23:30:19', 0),
(24, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 14, 5, '2025-08-14 18:36:19', 0),
(25, 43, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 26, 38, '2025-05-24 21:57:29', 0),
(26, 48, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 26, 39, '2025-12-04 09:45:42', 0),
(27, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 27, 191, '2025-05-26 22:35:44', 0),
(28, 7, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 27, 26, '2025-05-26 09:51:47', 0),
(29, 37, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 44, 38, '2025-02-18 07:53:05', 0),
(30, 55, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 44, 39, '2025-12-06 13:02:42', 0),
(31, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 40, 17, '2025-06-26 08:54:03', 0),
(32, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 40, 21, '2025-10-23 03:42:46', 0),
(33, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 25, 61, '2025-01-06 14:26:26', 0),
(34, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 25, 63, '2025-01-06 00:57:28', 0),
(35, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 43, 65, '2025-02-15 08:14:07', 0),
(36, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 43, 66, '2025-03-28 03:51:11', 0),
(37, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 16, 22, '2025-05-14 23:05:49', 0),
(38, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 16, 23, '2025-11-06 20:10:16', 0),
(39, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 43, 65, '2025-10-29 07:23:01', 0),
(40, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 43, 66, '2025-01-17 06:45:13', 0),
(41, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 13, 65, '2025-06-02 13:51:58', 0),
(42, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 13, 66, '2025-07-17 10:40:52', 0),
(43, 48, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 40, 46, '2025-07-08 06:09:11', 0),
(44, 50, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 40, 47, '2025-12-06 20:46:48', 0),
(45, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 22, 67, '2025-01-19 03:27:02', 0),
(46, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 22, 68, '2025-07-25 22:32:38', 0),
(47, 35, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 50, 46, '2025-04-10 13:17:03', 0),
(48, 53, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 50, 47, '2025-02-08 01:30:25', 0),
(49, 7, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 27, 18, '2025-07-13 04:22:10', 0),
(50, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 27, 238, '2025-09-09 15:55:58', 0),
(51, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 15, 61, '2025-12-31 20:43:49', 0),
(52, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 15, 63, '2025-10-15 10:29:12', 0),
(53, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 40, 1, '2025-12-19 12:40:38', 0),
(54, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 40, 5, '2025-04-07 02:29:42', 0),
(55, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 10, 67, '2025-04-24 05:18:44', 0),
(56, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 10, 68, '2025-07-11 06:29:02', 0),
(57, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 52, 65, '2025-03-02 03:23:13', 0),
(58, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 52, 66, '2025-06-01 05:59:55', 0),
(59, 45, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 27, 46, '2025-05-23 11:29:42', 0),
(60, 41, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 27, 47, '2025-09-08 20:25:23', 0),
(61, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 7, 55, '2025-07-18 20:35:28', 0),
(62, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 7, 59, '2025-03-02 15:25:42', 0),
(63, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 30, 61, '2025-04-03 11:49:09', 0),
(64, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 30, 63, '2025-04-27 13:42:47', 0),
(65, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 58, 65, '2025-10-15 10:10:52', 0),
(66, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 58, 66, '2025-02-01 22:01:44', 0),
(67, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 56, 306, '2025-07-14 06:40:20', 0),
(68, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 56, 303, '2025-10-23 19:35:01', 0),
(69, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 57, 17, '2025-02-22 16:25:57', 0),
(70, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 57, 21, '2025-06-13 15:57:59', 0),
(71, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 61, 4, '2025-03-22 11:28:24', 0),
(72, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 61, 8, '2025-02-26 02:18:09', 0),
(73, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 55, 43, '2025-12-08 12:29:24', 0),
(74, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 55, 45, '2025-05-25 00:35:37', 0),
(75, 31, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 47, 46, '2025-01-28 18:52:21', 0),
(76, 36, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 47, 47, '2025-11-23 17:14:14', 0),
(77, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 11, 43, '2025-04-28 13:39:56', 0),
(78, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 11, 45, '2025-01-24 08:30:45', 0),
(79, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 27, 55, '2025-04-26 14:45:08', 0),
(80, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 27, 59, '2025-06-11 11:41:00', 0),
(81, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 46, 43, '2025-07-22 17:13:12', 0),
(82, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 46, 45, '2025-07-28 06:02:02', 0),
(83, 45, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 66, 38, '2025-11-04 07:40:02', 0),
(84, 50, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 66, 39, '2025-11-11 19:22:47', 0),
(85, 38, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 42, 38, '2025-10-23 11:12:57', 0),
(86, 36, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 42, 39, '2025-08-14 14:41:16', 0),
(87, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 52, 43, '2025-06-17 10:07:55', 0),
(88, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 52, 45, '2025-02-25 12:19:11', 0),
(89, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 31, 17, '2025-10-05 22:53:36', 0),
(90, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 31, 21, '2025-02-06 12:06:04', 0),
(91, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 8, 65, '2025-01-17 23:53:07', 0),
(92, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 8, 66, '2025-12-26 08:54:55', 0),
(93, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 65, 22, '2025-04-16 02:25:56', 0),
(94, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 65, 23, '2025-08-11 13:58:41', 0),
(95, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 13, 55, '2025-12-30 03:41:58', 0),
(96, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 13, 59, '2025-06-01 16:29:42', 0),
(97, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 14, 1, '2025-12-10 16:06:41', 0),
(98, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 14, 5, '2025-02-24 13:40:04', 0),
(99, 29, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 67, 55, '2025-05-14 18:41:14', 0),
(100, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 67, 59, '2025-08-14 16:03:09', 0),
(101, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 47, 55, '2025-06-13 16:48:59', 0),
(102, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 47, 59, '2025-12-16 07:02:41', 0),
(103, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 9, 43, '2025-02-23 06:30:32', 0),
(104, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 9, 45, '2025-02-28 12:16:13', 0),
(105, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 35, 12, '2025-09-25 06:33:26', 0),
(106, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 35, 11, '2025-06-21 14:09:38', 0),
(107, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 65, 61, '2025-05-05 18:02:23', 0),
(108, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 65, 63, '2025-04-06 23:19:00', 0),
(109, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 29, 4, '2025-12-02 16:39:50', 0),
(110, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 29, 8, '2025-02-09 10:46:13', 0),
(111, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 3, 22, '2025-10-11 06:39:39', 0),
(112, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 3, 23, '2025-10-10 05:37:56', 0),
(113, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 53, 55, '2025-09-15 23:04:10', 0),
(114, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 53, 59, '2025-06-21 03:38:02', 0),
(115, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 51, 67, '2025-07-20 05:25:19', 0),
(116, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 51, 68, '2025-03-15 22:19:06', 0),
(117, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 2, 1, '2025-11-29 15:15:10', 0),
(118, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 2, 5, '2025-06-29 16:56:41', 0),
(119, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 26, 67, '2025-02-25 16:40:49', 0),
(120, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 26, 68, '2025-06-20 20:25:33', 0),
(121, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 41, 12, '2025-07-09 21:58:26', 0),
(122, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 41, 11, '2025-09-03 17:40:33', 0),
(123, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 34, 65, '2025-02-09 18:04:59', 0),
(124, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 34, 66, '2025-01-04 16:29:49', 0),
(125, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 56, 43, '2025-03-19 06:41:44', 0),
(126, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 56, 45, '2025-12-07 02:44:58', 0),
(127, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 35, 1, '2025-10-19 15:59:16', 0),
(128, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 35, 5, '2025-10-18 15:01:41', 0),
(129, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 48, 67, '2025-12-08 21:38:49', 0),
(130, 27, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 48, 68, '2025-08-19 12:02:56', 0),
(131, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 37, 67, '2025-07-27 10:06:42', 0),
(132, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 37, 68, '2025-02-11 11:41:10', 0),
(133, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 34, 65, '2025-01-17 11:02:01', 0),
(134, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 34, 66, '2025-11-01 08:28:06', 0),
(135, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 59, 1, '2025-06-09 13:36:09', 0),
(136, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 59, 5, '2025-10-20 14:14:18', 0),
(137, 46, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 7, 46, '2025-04-19 04:06:43', 0),
(138, 55, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 7, 47, '2025-06-01 21:59:09', 0),
(139, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 12, 67, '2025-03-29 20:43:31', 0),
(140, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 12, 68, '2025-10-30 03:54:54', 0),
(141, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 21, 4, '2025-07-25 07:51:43', 0),
(142, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 21, 8, '2025-03-28 18:08:54', 0),
(143, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 22, 61, '2025-06-15 10:03:58', 0),
(144, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 22, 63, '2025-06-22 03:55:26', 0),
(145, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 32, 1, '2025-11-19 07:57:57', 0),
(146, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 32, 5, '2025-07-14 20:02:33', 0),
(147, 27, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 10, 17, '2025-03-12 21:07:11', 0),
(148, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 10, 21, '2025-11-20 04:53:35', 0),
(149, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 43, 65, '2025-01-28 22:53:54', 0),
(150, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 43, 66, '2025-06-27 07:01:52', 0),
(151, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 48, 22, '2025-07-16 23:30:50', 0),
(152, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 48, 23, '2025-11-18 23:29:26', 0),
(153, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 29, 1, '2025-11-26 18:42:11', 0),
(154, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 29, 5, '2025-03-24 23:56:49', 0),
(155, 29, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 9, 4, '2025-03-10 22:40:05', 0),
(156, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 9, 8, '2025-07-17 23:10:20', 0),
(157, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 51, 61, '2025-08-09 20:16:04', 0),
(158, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 51, 63, '2025-08-13 08:32:27', 0),
(159, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 7, 271, '2025-05-27 11:29:02', 0),
(160, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 7, 13, '2025-09-01 06:54:13', 0),
(161, 45, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 39, 38, '2025-07-11 21:57:28', 0),
(162, 33, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 39, 39, '2025-12-28 19:04:20', 0),
(163, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 16, 55, '2025-02-06 20:45:25', 0),
(164, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 16, 59, '2025-12-31 06:44:00', 0),
(165, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 36, 61, '2025-01-20 13:07:51', 0),
(166, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 36, 63, '2025-01-13 20:53:18', 0),
(167, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 44, 67, '2025-03-10 14:42:19', 0),
(168, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 44, 68, '2025-04-03 19:24:40', 0),
(169, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 10, 61, '2025-02-20 20:28:08', 0),
(170, 29, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 10, 63, '2025-05-11 22:12:40', 0),
(171, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 33, 276, '2025-08-16 23:18:20', 0),
(172, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 33, 239, '2025-08-26 03:17:50', 0),
(173, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 35, 55, '2025-06-11 06:01:01', 0),
(174, 29, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 35, 59, '2025-07-19 12:39:13', 0),
(175, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 40, 55, '2025-05-30 18:16:17', 0),
(176, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 40, 59, '2025-11-16 17:16:31', 0),
(177, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 38, 1, '2025-12-13 19:47:55', 0),
(178, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 38, 5, '2025-03-04 04:41:57', 0),
(179, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 54, 55, '2025-09-08 07:05:52', 0),
(180, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 54, 59, '2025-06-30 20:34:31', 0),
(181, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 68, 22, '2025-03-30 15:03:09', 0),
(182, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 68, 23, '2025-02-26 04:49:30', 0),
(183, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 44, 22, '2025-09-28 04:12:17', 0),
(184, 29, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 44, 23, '2025-09-05 12:28:24', 0),
(185, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 8, 55, '2025-03-23 07:09:19', 0),
(186, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 8, 59, '2025-02-16 23:41:43', 0),
(187, 19, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 17, 17, '2025-06-11 01:48:50', 0),
(188, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 17, 21, '2025-03-24 13:12:48', 0),
(189, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 15, 1, '2025-04-29 18:43:54', 0),
(190, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 15, 5, '2025-02-23 20:59:13', 0),
(191, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 7, 1, '2025-11-16 14:10:12', 0),
(192, 7, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 7, 5, '2025-04-23 08:21:11', 0),
(193, 27, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 4, 17, '2025-12-20 07:11:03', 0),
(194, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 4, 21, '2025-08-15 13:19:44', 0),
(195, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 35, 55, '2025-01-30 20:35:39', 0),
(196, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 35, 59, '2025-12-20 07:20:03', 0),
(197, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 50, 12, '2025-08-28 02:00:39', 0),
(198, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 50, 11, '2025-09-25 20:12:15', 0),
(199, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 43, 65, '2025-01-18 05:47:19', 0),
(200, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 43, 66, '2025-04-18 00:08:44', 0),
(201, 42, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 53, 38, '2025-02-22 22:40:20', 0),
(202, 45, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 53, 39, '2025-02-12 21:28:08', 0),
(203, 31, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 35, 38, '2025-12-05 11:51:45', 0),
(204, 44, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 35, 39, '2025-07-25 08:10:29', 0),
(205, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 70, 1, '2025-05-06 00:33:42', 0),
(206, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 70, 5, '2025-06-12 09:06:15', 0),
(207, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 42, 4, '2025-05-15 23:55:39', 0),
(208, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 42, 8, '2025-01-09 12:41:29', 0),
(209, 37, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 34, 38, '2025-02-12 01:47:36', 0),
(210, 55, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 34, 39, '2025-12-24 15:51:14', 0),
(211, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 59, 43, '2025-07-31 21:05:26', 0),
(212, 29, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 59, 45, '2025-03-18 18:02:34', 0),
(213, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 53, 12, '2025-10-09 09:00:08', 0),
(214, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 53, 11, '2025-10-03 08:39:56', 0),
(215, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 19, 1, '2025-05-25 11:37:44', 0),
(216, 7, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 19, 5, '2025-09-24 22:57:54', 0),
(217, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 64, 12, '2025-05-02 19:40:45', 0),
(218, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 64, 11, '2025-10-04 23:13:28', 0),
(219, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 35, 61, '2025-06-01 00:09:29', 0),
(220, 27, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 35, 63, '2025-01-12 09:59:19', 0),
(221, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 29, 43, '2025-11-14 20:21:43', 0),
(222, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 29, 45, '2025-04-07 08:59:16', 0),
(223, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 59, 61, '2025-03-07 11:30:48', 0),
(224, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 59, 63, '2025-01-12 17:52:00', 0),
(225, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 58, 55, '2025-12-30 19:51:05', 0),
(226, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 58, 59, '2025-08-26 00:23:12', 0),
(227, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 19, 17, '2025-05-14 20:41:09', 0),
(228, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 19, 21, '2025-02-15 05:33:21', 0),
(229, 11, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 25, 53, '2025-01-20 15:23:32', 0),
(230, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 25, 57, '2025-11-28 02:27:14', 0),
(231, 44, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 53, 38, '2025-10-02 04:05:57', 0),
(232, 38, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 53, 39, '2025-02-09 07:48:26', 0),
(233, 13, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 14, 1, '2025-04-15 15:26:44', 0),
(234, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 14, 5, '2025-06-24 07:12:28', 0),
(235, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 50, 61, '2025-06-29 21:56:26', 0),
(236, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 50, 63, '2025-01-25 16:00:24', 0),
(237, 36, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 54, 46, '2025-11-06 10:39:46', 0),
(238, 48, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 54, 47, '2025-07-02 10:32:37', 0),
(239, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 32, 67, '2025-11-13 08:21:54', 0),
(240, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 32, 68, '2025-12-20 16:02:51', 0),
(241, 28, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 8, 53, '2025-08-11 13:37:37', 0),
(242, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 8, 57, '2025-11-12 18:29:11', 0),
(243, 16, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 57, 67, '2025-12-13 12:36:15', 0),
(244, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 57, 68, '2025-04-03 01:06:29', 0),
(245, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 18, 12, '2025-04-01 03:37:08', 0),
(246, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 18, 11, '2025-06-04 10:47:24', 0),
(247, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 46, 61, '2025-10-26 04:26:42', 0),
(248, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 46, 63, '2025-09-18 11:21:11', 0),
(249, 27, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 66, 43, '2025-03-17 07:42:20', 0),
(250, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 66, 45, '2025-07-01 20:14:01', 0),
(251, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 7, 1, '2025-08-11 03:05:33', 0),
(252, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 7, 5, '2025-01-29 20:39:46', 0),
(253, 37, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 15, 46, '2025-01-10 04:45:34', 0),
(254, 41, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 15, 47, '2025-10-08 00:28:12', 0),
(255, 24, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 17, 61, '2025-05-08 14:06:14', 0),
(256, 21, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 17, 63, '2025-09-26 23:44:50', 0),
(257, 30, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 63, 46, '2025-11-09 12:16:14', 0),
(258, 37, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 63, 47, '2025-08-20 19:25:13', 0),
(259, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 16, 1, '2025-05-12 18:33:21', 0),
(260, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 16, 5, '2025-02-22 09:47:02', 0),
(261, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 60, 43, '2025-06-24 18:53:29', 0),
(262, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 60, 45, '2025-10-22 06:53:12', 0),
(263, 38, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 19, 46, '2025-01-04 08:49:51', 0),
(264, 35, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 19, 47, '2025-12-02 09:48:22', 0),
(265, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 8, 52, '2025-11-15 04:57:49', 0),
(266, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 8, 149, '2025-07-05 01:53:43', 0),
(267, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 9, 61, '2025-04-15 21:14:36', 0),
(268, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Hạn chế rượu bia, tái khám đúng hẹn', 9, 63, '2025-11-24 02:34:07', 0),
(269, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 32, 1, '2025-06-11 01:37:14', 0),
(270, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 32, 5, '2025-04-27 07:40:40', 0),
(271, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 26, 1, '2025-01-31 11:17:54', 0),
(272, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 26, 5, '2025-09-20 01:34:13', 0),
(273, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 55, 22, '2025-11-22 00:09:34', 0),
(274, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 55, 23, '2025-06-16 12:54:05', 0),
(275, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 62, 1, '2025-08-28 03:41:52', 0),
(276, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 62, 5, '2025-05-06 02:08:59', 0),
(277, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 23, 185, '2025-09-08 21:08:56', 0),
(278, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 23, 287, '2025-01-24 23:39:32', 0),
(279, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 57, 55, '2025-06-25 08:08:41', 0),
(280, 25, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 57, 59, '2025-07-15 13:49:20', 0),
(281, 15, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 46, 53, '2025-11-09 21:19:33', 0),
(282, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 46, 57, '2025-06-19 16:47:45', 0),
(283, 6, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 51, 263, '2025-05-28 19:27:28', 0),
(284, 10, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 51, 297, '2025-10-07 04:17:39', 0),
(285, 22, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 66, 12, '2025-10-17 14:03:50', 0),
(286, 12, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 66, 11, '2025-03-11 12:34:14', 0),
(287, 48, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 40, 38, '2025-06-24 16:16:01', 0),
(288, 46, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 40, 39, '2025-05-19 03:56:40', 0),
(289, 14, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 58, 4, '2025-11-20 00:32:45', 0),
(290, 26, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 58, 8, '2025-05-29 22:52:36', 0),
(291, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 11, 130, '2025-09-15 14:03:17', 0),
(292, 9, 'Ngày 2 lần mỗi lần 2 viên', 'Chỉ dùng paracetamol, tránh NSAID', 11, 8, '2025-03-01 14:45:16', 0),
(293, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 25, 17, '2025-01-14 04:57:51', 0),
(294, 17, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 25, 21, '2025-03-21 09:32:00', 0),
(295, 8, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 41, 1, '2025-10-22 16:27:41', 0),
(296, 7, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 41, 5, '2025-05-20 10:58:27', 0),
(297, 18, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 28, 55, '2025-12-29 12:21:32', 0),
(298, 23, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 28, 59, '2025-06-07 16:03:29', 0),
(299, 33, 'Ngày 2 lần mỗi lần 2 viên', 'Uống đủ liều, tái khám nếu không giảm', 22, 46, '2025-04-09 01:52:36', 0),
(300, 30, 'Ngày 2 lần mỗi lần 2 viên', 'Thuốc hỗ trợ điều trị', 22, 47, '2025-03-21 11:08:23', 0),
(301, 12, 'Ngày 2 lần mỗi lần 2 viên', 'sau ăn', 20, 222, '2025-12-07 01:47:30', 0),
(302, 20, 'Ngày 2 lần mỗi lần 2 viên', 'Trước ăn', 71, 24, '2025-12-07 01:59:09', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `next_visit_date` date DEFAULT NULL,
  `bp` varchar(23) NOT NULL,
  `weight` varchar(12) NOT NULL,
  `disease` varchar(30) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patient_visits`
--

INSERT INTO `patient_visits` (`id`, `visit_date`, `next_visit_date`, `bp`, `weight`, `disease`, `patient_id`, `is_deleted`) VALUES
(1, '2022-06-28', '2022-06-30', '120/80', '65 kg.', 'Wounded Arm', 1, 1),
(2, '2022-06-30', '2022-07-02', '120/80', '65 kg.', 'Rhinovirus', 1, 1),
(4, '2025-09-08', '2025-09-13', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(5, '2025-08-09', '2025-12-09', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(6, '2025-08-09', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(7, '0000-00-00', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(8, '0000-00-00', '2025-01-10', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(9, '2025-09-09', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(10, '2025-01-10', '2025-10-10', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(11, '2025-09-30', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 1),
(12, '2025-09-24', '0000-00-00', '100', '50', 'Chơi game lắm', 2, 1),
(13, '2025-09-11', '0000-00-00', '99', '50', 'ngáo', 2, 1),
(14, '2025-10-01', '0000-00-00', '122', '100', 'Chơi game lắm', 2, 1),
(15, '2025-09-24', '0000-00-00', '122', '50', 'ngáo', 2, 1),
(16, '2025-09-11', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 5, 1),
(17, '2025-09-09', '0000-00-00', '122', '50', 'ngáo', 5, 1),
(19, '2025-09-18', '0000-00-00', '11', '11', 'ngủ ít chơi nhiều', 6, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `display_name` varchar(30) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` int(11) NOT NULL COMMENT '1 - admin\r\n2 - bác sĩ\r\n3 - bệnh nhân\r\n',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `profile_picture` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `display_name`, `user_name`, `password`, `role`, `is_deleted`, `profile_picture`, `created_at`) VALUES
(1, 'Ngô Thị Trang', 'admin01', 'e10adc3949ba59abbe56e057f20f883e', 1, 0, NULL, '2025-10-09 05:45:30'),
(2, 'Nguyễn Bình An', 'admin02', 'e10adc3949ba59abbe56e057f20f883e', 1, 0, NULL, '2025-12-25 08:30:35'),
(3, 'Đinh Thị Yến', 'doctor001', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-20 13:52:03'),
(4, 'Nguyễn Thị Lan', 'doctor002', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-10-31 18:54:07'),
(5, 'Tạ Thị Thanh', 'doctor003', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-05-04 18:20:04'),
(6, 'Hồ Thị Phương', 'doctor004', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-14 10:14:20'),
(7, 'Nguyễn Thị Lan', 'doctor005', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-13 05:32:08'),
(8, 'Nguyễn Bình An', 'doctor006', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-26 13:01:39'),
(9, 'Bùi Thanh Tùng', 'doctor007', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-11 05:42:15'),
(10, 'Nguyễn Thị Lan', 'doctor008', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-26 15:08:12'),
(11, 'Đỗ Đức Long', 'doctor009', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-27 20:16:09'),
(12, 'Phạm Văn Dũng', 'doctor010', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-04 08:53:30'),
(13, 'Hồ Thị Phương', 'doctor011', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-19 10:31:14'),
(14, 'Dương Minh Đức', 'doctor012', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-23 23:52:25'),
(15, 'Đỗ Đức Long', 'doctor013', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-16 22:54:55'),
(16, 'Đinh Văn Sơn', 'doctor014', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-11 04:31:49'),
(17, 'Trần Thị Hương', 'doctor015', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-26 23:50:37'),
(18, 'Phạm Văn Dũng', 'doctor016', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-22 04:15:59'),
(19, 'Nguyễn Hồng Hải', 'doctor017', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-16 10:28:18'),
(20, 'Nguyễn Văn Nam', 'doctor018', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-11 16:34:11'),
(21, 'Lê Thị Ngọc', 'doctor019', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-18 04:34:51'),
(22, 'Ngô Văn Toàn', 'doctor020', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-21 09:54:49'),
(23, 'Trần Minh Quân', 'doctor021', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-04 03:08:13'),
(24, 'Ngô Thị Trang', 'doctor022', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-08 18:23:06'),
(25, 'Nguyễn Thị Lan', 'doctor023', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-10-31 09:05:00'),
(26, 'Nguyễn Hồng Hải', 'doctor024', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-08 07:07:31'),
(27, 'Tạ Quốc Việt', 'doctor025', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-08 05:38:13'),
(28, 'Dương Minh Đức', 'doctor026', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-05-10 18:58:46'),
(29, 'Hoàng Ngọc Mai', 'doctor027', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-05-05 21:52:52'),
(30, 'Dương Thị Quỳnh', 'doctor028', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-21 04:55:45'),
(31, 'Ngô Thị Trang', 'doctor029', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-28 16:43:38'),
(32, 'Phạm Văn Dũng', 'doctor030', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-10-05 09:27:35'),
(33, 'Phạm Quốc Bảo', 'doctor031', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-13 05:11:54'),
(34, 'Phạm Quốc Bảo', 'doctor032', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-03-21 13:38:09'),
(35, 'Võ Văn Khánh', 'doctor033', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-05-18 20:18:00'),
(36, 'Trần Minh Quân', 'doctor034', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-03-19 22:58:59'),
(37, 'Dương Thị Quỳnh', 'doctor035', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-29 13:58:47'),
(38, 'Đinh Thị Yến', 'doctor036', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-08 10:46:35'),
(39, 'Dương Thị Quỳnh', 'doctor037', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-29 06:56:10'),
(40, 'Ngô Văn Toàn', 'doctor038', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-03 10:48:13'),
(41, 'Tạ Thị Thanh', 'doctor039', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-03 22:50:55'),
(42, 'Phạm Văn Dũng', 'doctor040', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-22 00:53:41'),
(43, 'Đinh Thị Yến', 'doctor041', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-13 03:22:10'),
(44, 'Lê Văn Cường', 'doctor042', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-02 07:58:08'),
(45, 'Võ Văn Khánh', 'doctor043', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-03 01:05:10'),
(46, 'Đặng Thùy Linh', 'doctor044', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-27 20:55:20'),
(47, 'Đinh Thị Yến', 'doctor045', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-05-08 03:45:01'),
(48, 'Võ Văn Khánh', 'doctor046', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-16 02:57:34'),
(49, 'Tạ Thị Thanh', 'doctor047', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-14 22:28:58'),
(50, 'Bùi Thanh Tùng', 'doctor048', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-12 16:32:21'),
(51, 'Dương Thị Quỳnh', 'doctor049', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-17 15:54:13'),
(52, 'Dương Thị Quỳnh', 'doctor050', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-22 09:19:31'),
(53, 'Võ Văn Khánh', 'doctor051', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-03-30 10:03:06'),
(54, 'Đỗ Đức Long', 'doctor052', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-06 11:03:33'),
(55, 'Hoàng Ngọc Mai', 'doctor053', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-05 23:39:53'),
(56, 'Hồ Thanh Tâm', 'doctor054', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-06 14:06:07'),
(57, 'Hồ Thị Phương', 'doctor055', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-07 06:57:20'),
(58, 'Hoàng Ngọc Mai', 'doctor056', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-30 05:14:52'),
(59, 'Nguyễn Bình An', 'doctor057', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-21 13:06:45'),
(60, 'Phạm Thị Nga', 'doctor058', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-23 09:54:46'),
(61, 'Hồ Thanh Tâm', 'doctor059', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-18 22:20:08'),
(62, 'Lê Thu Hà', 'doctor060', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-26 23:10:04'),
(63, 'Phạm Văn Dũng', 'doctor061', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-10-19 23:10:22'),
(64, 'Nguyễn Văn Nam', 'doctor062', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-28 15:46:38'),
(65, 'Phạm Thị Nga', 'doctor063', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-04 19:12:57'),
(66, 'Ngô Văn Toàn', 'doctor064', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-07 07:06:44'),
(67, 'Hồ Thanh Tâm', 'doctor065', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-25 10:11:33'),
(68, 'Nguyễn Hồng Hải', 'doctor066', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-28 09:39:19'),
(69, 'Hồ Thanh Tâm', 'doctor067', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-03-18 19:43:05'),
(70, 'Dương Thị Quỳnh', 'doctor068', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-23 23:44:58'),
(71, 'Hoàng Ngọc Mai', 'doctor069', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-22 09:46:52'),
(72, 'Hồ Thị Phương', 'doctor070', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-25 20:06:06'),
(73, 'Đỗ Đức Long', 'doctor071', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-04 05:54:36'),
(74, 'Phạm Quốc Bảo', 'doctor072', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-05 03:22:50'),
(75, 'Đinh Thị Yến', 'doctor073', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-10 11:02:39'),
(76, 'Bùi Thanh Tùng', 'doctor074', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-05-09 11:24:58'),
(77, 'Nguyễn Bình An', 'doctor075', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-03-27 21:30:48'),
(78, 'Nguyễn Hồng Hải', 'doctor076', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-21 18:00:46'),
(79, 'Lê Thị Ngọc', 'doctor077', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-25 04:37:46'),
(80, 'Đinh Thị Yến', 'doctor078', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-25 21:51:01'),
(81, 'Đinh Thị Yến', 'doctor079', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-11-23 13:29:33'),
(82, 'Đỗ Đức Long', 'doctor080', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-03 11:42:42'),
(83, 'Võ Thị Kim', 'doctor081', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-19 05:05:41'),
(84, 'Nguyễn Thị Lan', 'doctor082', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-06 15:23:59'),
(85, 'Trần Văn Hùng', 'doctor083', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-18 06:44:45'),
(86, 'Lê Văn Cường', 'doctor084', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-20 13:34:39'),
(87, 'Vũ Anh Tuấn', 'doctor085', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-10-12 05:16:00'),
(88, 'Đỗ Đức Long', 'doctor086', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-04-28 01:52:36'),
(89, 'Võ Văn Khánh', 'doctor087', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-02-01 12:15:33'),
(90, 'Võ Văn Khánh', 'doctor088', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-10-03 00:02:19'),
(91, 'Tạ Quốc Việt', 'doctor089', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-16 10:24:33'),
(92, 'Dương Minh Đức', 'doctor090', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-12 05:25:41'),
(93, 'Hồ Thị Phương', 'doctor091', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-08 09:27:23'),
(94, 'Võ Thị Kim', 'doctor092', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-06-03 10:49:33'),
(95, 'Vũ Anh Tuấn', 'doctor093', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-07-31 00:35:13'),
(96, 'Nguyễn Thị Lan', 'doctor094', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-03-15 09:43:24'),
(97, 'Dương Thị Quỳnh', 'doctor095', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-08-14 11:27:47'),
(98, 'Phạm Văn Dũng', 'doctor096', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-12-05 10:17:44'),
(99, 'Tạ Thị Thanh', 'doctor097', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-01-21 20:27:19'),
(100, 'Nguyễn Thị Lan', 'doctor098', 'e10adc3949ba59abbe56e057f20f883e', 2, 0, NULL, '2025-09-28 07:59:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_patients`
--

CREATE TABLE `user_patients` (
  `id` int(11) NOT NULL,
  `user_name` varchar(155) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `is_deleted` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_patients`
--

INSERT INTO `user_patients` (`id`, `user_name`, `display_name`, `password`, `role`, `is_deleted`, `id_patient`, `created_at`) VALUES
(1, '000170967902', 'Dương Thị Quỳnh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 1, '2025-01-22 08:37:18'),
(2, '001163089252', 'Lý Hoàng Phúc', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 2, '2025-10-08 11:43:12'),
(3, '002146976133', 'Đinh Thị Yến', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 3, '2025-09-01 08:44:10'),
(4, '003156758445', 'Hồ Thị Phương', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 4, '2025-01-11 08:31:16'),
(5, '004169443250', 'Tạ Thị Thanh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 5, '2025-02-21 16:23:51'),
(6, '005187296625', 'Đỗ Đức Long', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 6, '2025-08-16 08:25:28'),
(7, '006101860574', 'Lưu Văn Thắng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 7, '2025-09-09 16:55:52'),
(8, '000240445046', 'Vũ Anh Tuấn', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 8, '2025-07-31 11:22:57'),
(9, '001260329724', 'Ngô Văn Toàn', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 9, '2025-10-30 06:07:12'),
(10, '002298680123', 'Đặng Thùy Linh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 10, '2025-05-27 20:27:11'),
(11, '003208723701', 'Dương Thị Quỳnh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 11, '2025-07-12 11:54:19'),
(12, '004281572192', 'Tạ Thị Thanh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 12, '2025-06-06 22:10:07'),
(13, '005236317397', 'Lưu Thị Hòa', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 13, '2025-07-27 03:07:38'),
(14, '006227309844', 'Trần Văn Hùng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 14, '2025-07-19 21:07:50'),
(15, '000373570239', 'Hoàng Ngọc Mai', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 15, '2025-01-14 00:16:18'),
(16, '001367161008', 'Mai Văn Hiếu', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 16, '2025-07-15 09:58:00'),
(17, '002351428028', 'Tạ Quốc Việt', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 17, '2025-07-28 01:01:12'),
(18, '003381793825', 'Đinh Thị Yến', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 18, '2025-03-30 23:12:00'),
(19, '004392175802', 'Hoàng Ngọc Mai', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 19, '2025-07-05 16:56:25'),
(20, '005309615888', 'Bùi Thanh Tùng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 20, '2025-10-24 15:06:07'),
(21, '006376839417', 'Lê Thị Ngọc', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 21, '2025-07-16 00:41:22'),
(22, '000443091052', 'Tạ Quốc Việt', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 22, '2025-04-01 06:08:31'),
(23, '001495435924', 'Đinh Thị Yến', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 23, '2025-08-17 04:38:32'),
(24, '002460454748', 'Vũ Anh Tuấn', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 24, '2025-05-21 04:47:08'),
(25, '003492278557', 'Chu Văn Lợi', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 25, '2025-01-17 10:00:06'),
(26, '004487625831', 'Dương Thị Quỳnh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 26, '2025-01-27 11:39:06'),
(27, '005456827877', 'Hồ Thanh Tâm', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 27, '2025-03-25 04:15:14'),
(28, '006424071103', 'Lý Hoàng Phúc', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 28, '2025-12-03 10:18:52'),
(29, '000594402224', 'Lý Hoàng Phúc', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 29, '2025-12-04 14:48:45'),
(30, '001503943080', 'Lưu Văn Thắng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 30, '2025-11-10 19:07:09'),
(31, '002588052781', 'Chu Thị Duyên', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 31, '2025-07-11 03:09:35'),
(32, '003586948319', 'Trần Minh Quân', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 32, '2025-01-15 06:26:30'),
(33, '004583222165', 'Bùi Thanh Tùng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 33, '2025-08-16 22:43:44'),
(34, '005554932977', 'Trần Thị Hương', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 34, '2025-01-02 22:19:15'),
(35, '006590486407', 'Lê Thị Thu Hà', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 35, '2025-02-25 19:25:03'),
(36, '000661658883', 'Phạm Thị Nga', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 36, '2025-10-01 06:07:31'),
(37, '001636268423', 'Lưu Thị Hòa', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 37, '2025-04-14 20:22:31'),
(38, '002624648721', 'Trần Thị Hương', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 38, '2025-03-06 11:30:04'),
(39, '003647574360', 'Trần Văn Hùng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 39, '2025-01-11 20:22:46'),
(40, '004649017251', 'Hoàng Ngọc Mai', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 40, '2025-08-14 19:23:39'),
(41, '005654831545', 'Hồ Thanh Tâm', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 41, '2025-01-02 11:50:03'),
(42, '006691891145', 'Hoàng Ngọc Mai', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 42, '2025-03-02 00:59:18'),
(43, '000765640992', 'Lê Thị Thu Hà', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 43, '2025-10-23 17:26:20'),
(44, '001765812475', 'Hoàng Ngọc Mai', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 44, '2025-07-23 12:13:50'),
(45, '002740074478', 'Nguyễn Bình An', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 45, '2025-05-11 08:50:14'),
(46, '003734967872', 'Lưu Thị Hòa', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 46, '2025-02-11 07:29:42'),
(47, '004742071828', 'Hoàng Ngọc Mai', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 47, '2025-06-30 10:57:49'),
(48, '005769032976', 'Đinh Thị Yến', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 48, '2025-02-18 08:20:03'),
(49, '006744326523', 'Trần Minh Quân', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 49, '2025-03-07 08:46:46'),
(50, '000865246318', 'Võ Thị Kim', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 50, '2025-07-01 18:53:42'),
(51, '001868278228', 'Mai Văn Hiếu', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 51, '2025-12-14 20:08:13'),
(52, '002823459592', 'Lê Thị Ngọc', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 52, '2025-04-09 20:00:07'),
(53, '003824059511', 'Lưu Văn Thắng', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 53, '2025-06-30 15:35:54'),
(54, '004861787125', 'Dương Thị Quỳnh', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 54, '2025-08-30 17:59:13'),
(55, '005829935060', 'Phạm Quốc Bảo', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 55, '2025-10-29 19:08:27'),
(56, '006895468812', 'Lê Thị Thu Hà', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 56, '2025-02-23 17:44:36'),
(57, '000940741893', 'Phạm Quốc Bảo', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 57, '2025-04-04 07:17:40'),
(58, '001972317050', 'Nguyễn Thị Lan', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 58, '2025-11-02 07:14:34'),
(59, '002906041227', 'Đỗ Đức Long', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 59, '2025-06-01 14:19:53'),
(60, '003993889260', 'Mai Văn Hiếu', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 60, '2025-07-27 01:55:42'),
(61, '004931347620', 'Đỗ Đức Long', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 61, '2025-08-04 14:38:55'),
(62, '005930829832', 'Ngô Văn Toàn', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 62, '2025-04-02 19:27:32'),
(63, '006933180891', 'Trần Minh Quân', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 63, '2025-06-27 05:20:54'),
(64, '001049192796', 'Lê Thị Thu Hà', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 64, '2025-09-03 16:21:55'),
(65, '002014606734', 'Trần Thị Hương', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 65, '2025-11-28 17:46:58'),
(66, '003047848151', 'Ngô Thị Trang', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 66, '2025-07-11 15:49:06'),
(67, '004001338375', 'Mai Văn Hiếu', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 67, '2025-11-25 01:44:38'),
(68, '005010357290', 'Nguyễn Thị Lan', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 68, '2025-12-02 09:15:57'),
(69, '006066292957', 'Lê Văn Cường', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 69, '2025-11-24 17:05:52'),
(70, '007006892792', 'Tạ Quốc Việt', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 70, '2025-09-25 09:41:30'),
(71, '025203005779', 'Hải Nguyễn', 'e10adc3949ba59abbe56e057f20f883e', 3, 0, 71, '2025-12-04 10:04:39');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `appointment_status_log`
--
ALTER TABLE `appointment_status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Chỉ mục cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_ibfk_1` (`id_patient`);

--
-- Chỉ mục cho bảng `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicine_name` (`medicine_name`);

--
-- Chỉ mục cho bảng `medicine_details`
--
ALTER TABLE `medicine_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cnic` (`cnic`);

--
-- Chỉ mục cho bảng `patient_diseases`
--
ALTER TABLE `patient_diseases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_patient` (`patient_id`);

--
-- Chỉ mục cho bảng `patient_medication_history`
--
ALTER TABLE `patient_medication_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Chỉ mục cho bảng `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_patients_visit_patient_id` (`patient_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Chỉ mục cho bảng `user_patients`
--
ALTER TABLE `user_patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_patient` (`id_patient`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `appointment_status_log`
--
ALTER TABLE `appointment_status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT cho bảng `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT cho bảng `medicine_details`
--
ALTER TABLE `medicine_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT cho bảng `patient_diseases`
--
ALTER TABLE `patient_diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT cho bảng `patient_medication_history`
--
ALTER TABLE `patient_medication_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT cho bảng `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `user_patients`
--
ALTER TABLE `user_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `appointment_status_log`
--
ALTER TABLE `appointment_status_log`
  ADD CONSTRAINT `appointment_status_log_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`);

--
-- Các ràng buộc cho bảng `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `user_patients` (`id`);

--
-- Các ràng buộc cho bảng `patient_diseases`
--
ALTER TABLE `patient_diseases`
  ADD CONSTRAINT `patient_diseases_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Các ràng buộc cho bảng `patient_medication_history`
--
ALTER TABLE `patient_medication_history`
  ADD CONSTRAINT `patient_medication_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `patient_medication_history_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);

--
-- Các ràng buộc cho bảng `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD CONSTRAINT `fk_patients_visit_patient_id` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Các ràng buộc cho bảng `user_patients`
--
ALTER TABLE `user_patients`
  ADD CONSTRAINT `user_patients_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
