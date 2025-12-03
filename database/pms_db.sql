-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 21, 2025 lúc 06:59 AM
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
(1, '1', 'patients', 22, 'insert', 'null', '{\"patient_name\":\"Testaudit\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"012345678911\",\"date_of_birth\":\"2003-11-06\",\"phone_number\":\"0987676211\",\"gender\":\"Nam\"}', '2025-11-06 10:28:34'),
(2, '1', 'patients', 22, 'update', 'null', '{\"patient_name\":\"Testaudit\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"012345678911\",\"date_of_birth\":\"2003-11-06\",\"phone_number\":\"0987676211\",\"gender\":\"Nam\"}', '2025-11-06 10:34:36'),
(3, '1', 'patients', 24, 'insert', 'null', '{\"patient_name\":\"Mark Cooper Audit\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"112345678955\",\"date_of_birth\":\"2005-11-06\",\"phone_number\":\"0312111356\",\"gender\":\"Nam\"}', '2025-11-06 10:49:03'),
(4, '1', 'patients', 24, 'update', '{\"id\":24,\"patient_name\":\"Mark Cooper Audit\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"112345678955\",\"date_of_birth\":\"2005-11-06\",\"phone_number\":\"0312111356\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 10:49:03\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":24,\"patient_name\":\"Mark Cooper Audit New\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"112345678955\",\"date_of_birth\":\"2005-11-06\",\"phone_number\":\"0312111356\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 10:49:03\",\"updated_at\":null,\"deleted_at\":null}', '2025-11-06 10:49:41'),
(5, '1', 'patients', 23, 'delete', '{\"id\":23,\"patient_name\":\"Testnewaudit\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"112345678922\",\"date_of_birth\":\"2004-11-07\",\"phone_number\":\"0977676211\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 10:47:49\",\"updated_at\":null,\"deleted_at\":null}', '{\"is_deleted\":1}', '2025-11-06 10:53:36'),
(6, '2', 'patients', 22, 'update', '{\"id\":22,\"patient_name\":\"Testauditnew\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"012345678911\",\"date_of_birth\":\"2003-11-06\",\"phone_number\":\"0987676211\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 10:28:34\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":22,\"patient_name\":\"Testauditnewbs\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"012345678911\",\"date_of_birth\":\"2003-11-06\",\"phone_number\":\"0987676211\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 10:28:34\",\"updated_at\":null,\"deleted_at\":null}', '2025-11-06 10:55:06'),
(7, '1', 'patients', 25, 'insert', 'null', '{\"patient_name\":\"Mark Cooperdddddd\",\"address\":\"Cau Giay Ha Noi\",\"cnic\":\"123456789134\",\"date_of_birth\":\"2004-11-06\",\"phone_number\":\"0971676211\",\"gender\":\"Nam\"}', '2025-11-06 10:58:49'),
(8, '1', 'users', 22, 'insert', 'null', '{\"display_name\":\"testaduit\",\"user_name\":\"audit23\",\"role\":\"1\"}', '2025-11-06 11:05:27'),
(9, '1', 'users', 22, 'update', '{\"id\":22,\"display_name\":\"testaduit\",\"user_name\":\"audit23\",\"password\":\"c4ca4238a0b923820dcc509a6f75849b\",\"role\":1,\"is_deleted\":0,\"profile_picture\":null,\"created_at\":\"0000-00-00 00:00:00\"}', '{\"display_name\":\"testaduit\",\"user_name\":\"audit235\",\"role\":\"1\"}', '2025-11-06 11:05:40'),
(10, '1', 'users', 22, 'delete', '{\"id\":22,\"display_name\":\"testaduit\",\"user_name\":\"audit235\",\"password\":\"c4ca4238a0b923820dcc509a6f75849b\",\"role\":1,\"is_deleted\":0,\"profile_picture\":null,\"created_at\":\"2025-11-06 11:05:40\"}', 'null', '2025-11-06 11:06:03'),
(11, '1', 'medicines', 19, 'insert', 'null', '{\"medicine_name\":\"Amoxicillinaudit\",\"created_at\":\"2025-11-06 11:10:11\"}', '2025-11-06 11:10:11'),
(12, '1', 'medicines', 19, 'update', '{\"id\":19,\"medicine_name\":\"Amoxicillinauditnew\",\"is_deleted\":0,\"created_at\":\"2025-11-06 11:10:11\",\"deleted_at\":null,\"updated_at\":\"2025-11-06 11:11:33\"}', '{\"medicine_name\":\"Amoxicillinauditnewnew\",\"updated_at\":\"2025-11-06 11:12:20\"}', '2025-11-06 11:12:20'),
(13, '1', 'medicines', 13, 'delete', 'null', '{\"is_deleted\":1}', '2025-11-06 11:16:06'),
(14, '1', 'users', 20, 'delete', '{\"id\":20,\"display_name\":\"audit\",\"user_name\":\"audit\",\"password\":\"c4ca4238a0b923820dcc509a6f75849b\",\"role\":2,\"is_deleted\":0,\"profile_picture\":null,\"created_at\":\"0000-00-00 00:00:00\"}', '{\"is_deleted\":1}', '2025-11-06 11:17:03'),
(15, '1', 'patient_diseases', 33, 'insert', 'null', '{\"patient_id\":\"21\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"169\",\"nhiet_do\":\"37\",\"mach_dap\":\"90\",\"nhip_tim\":\"9\",\"trieu_chung\":\"nghiện game\",\"chuan_doan\":\"khó chữa\",\"bien_phap\":\"không có\",\"nhap_vien\":\"2\",\"tien_su_benh\":\"nghiện bida\",\"thuoc\":[{\"medicine_id\":\"11\",\"quantity\":\"1\",\"dosage\":\"2 viên\\/ ngày\",\"note\":\"sau ăn\"},{\"medicine_id\":\"12\",\"quantity\":\"2\",\"dosage\":\"1v\\/ngày\",\"note\":\"trước ăn\"}],\"created_at\":\"2025-11-06 11:21:50\"}', '2025-11-06 11:21:50'),
(16, '1', 'patients', 26, 'insert', 'null', '{\"patient_name\":\"Nguyễn Hồng Hải Audit\",\"address\":\"39 Hồ Tùng Mậu\",\"cnic\":\"025203005776\",\"date_of_birth\":\"2003-05-08\",\"phone_number\":\"0362111355\",\"gender\":\"Nam\"}', '2025-11-06 16:55:07'),
(17, '1', 'patients', 26, 'update', '{\"id\":26,\"patient_name\":\"Nguyễn Hồng Hải Audit\",\"address\":\"39 Hồ Tùng Mậu\",\"cnic\":\"025203005776\",\"date_of_birth\":\"2003-05-08\",\"phone_number\":\"0362111355\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 16:55:07\",\"updated_at\":null,\"deleted_at\":null}', '{\"id\":26,\"patient_name\":\"Nguyễn Hồng Hải Audit New\",\"address\":\"39 Hồ Tùng Mậu\",\"cnic\":\"025203005776\",\"date_of_birth\":\"2003-05-08\",\"phone_number\":\"0362111355\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 16:55:07\",\"updated_at\":null,\"deleted_at\":null}', '2025-11-06 16:56:17'),
(18, '1', 'patients', 26, 'delete', '{\"id\":26,\"patient_name\":\"Nguyễn Hồng Hải Audit New\",\"address\":\"39 Hồ Tùng Mậu\",\"cnic\":\"025203005776\",\"date_of_birth\":\"2003-05-08\",\"phone_number\":\"0362111355\",\"gender\":\"Nam\",\"is_deleted\":0,\"created_at\":\"2025-11-06 16:55:07\",\"updated_at\":null,\"deleted_at\":null}', '{\"is_deleted\":1}', '2025-11-06 16:56:28'),
(19, '1', 'patient_diseases', 34, 'insert', 'null', '{\"patient_id\":\"21\",\"huyet_ap\":\"122\",\"can_nang\":\"12\",\"chieu_cao\":\"12\",\"nhiet_do\":\"12\",\"mach_dap\":\"12\",\"nhip_tim\":\"12\",\"trieu_chung\":\"12\",\"chuan_doan\":\"12\",\"bien_phap\":\"12\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"12\",\"thuoc\":[{\"medicine_id\":\"11\",\"quantity\":\"12\",\"dosage\":\"12\",\"note\":\"12\"}],\"created_at\":\"2025-11-06 16:57:01\"}', '2025-11-06 16:57:01'),
(20, '1', 'medicines', 20, 'insert', 'null', '{\"medicine_name\":\"Te\",\"created_at\":\"2025-11-06 16:57:16\"}', '2025-11-06 16:57:16'),
(21, '1', 'medicines', 20, 'update', '{\"id\":20,\"medicine_name\":\"Te\",\"is_deleted\":0,\"created_at\":\"2025-11-06 16:57:16\",\"deleted_at\":null,\"updated_at\":null}', '{\"medicine_name\":\"Ted\",\"updated_at\":\"2025-11-06 16:57:24\"}', '2025-11-06 16:57:24'),
(22, '1', 'medicines', 20, 'delete', 'null', '{\"is_deleted\":1}', '2025-11-06 16:57:36'),
(23, '1', 'users', 21, 'update', '{\"id\":21,\"display_name\":\"audit\",\"user_name\":\"audit1\",\"password\":\"c4ca4238a0b923820dcc509a6f75849b\",\"role\":1,\"is_deleted\":0,\"profile_picture\":null,\"created_at\":\"0000-00-00 00:00:00\"}', '{\"display_name\":\"auditdđ\",\"user_name\":\"audit1\",\"role\":\"1\"}', '2025-11-06 16:57:49'),
(24, '1', 'patient_diseases', 1, 'insert', 'null', '{\"patient_id\":\"21\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"50\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"99\",\"nhip_tim\":\"10\",\"trieu_chung\":\"Đau đầu chóng mặt, thiếu máu, tim đập loạn\",\"chuan_doan\":\"Rối loạn nhịp tim\",\"bien_phap\":\"Điều trị tại bệnh viện\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"Hẹp van tim\",\"thuoc\":[],\"created_at\":\"2025-11-11 13:26:23\"}', '2025-11-11 13:26:23'),
(25, '1', 'patient_diseases', 2, 'insert', 'null', '{\"patient_id\":\"16\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"100\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"99\",\"nhip_tim\":\"99\",\"trieu_chung\":\"Béo Phì\",\"chuan_doan\":\"Thừa cân, Thừa chất\",\"bien_phap\":\"Giảm cân\",\"nhap_vien\":\"2\",\"tien_su_benh\":\"không có\",\"thuoc\":[],\"created_at\":\"2025-11-11 13:27:24\"}', '2025-11-11 13:27:24'),
(26, '1', 'patient_diseases', 3, 'insert', 'null', '{\"patient_id\":\"17\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"100\",\"chieu_cao\":\"170\",\"nhiet_do\":\"36\",\"mach_dap\":\"99\",\"nhip_tim\":\"99\",\"trieu_chung\":\"khó thở, đau đầu, chóng mặt\",\"chuan_doan\":\"rối loạn nhịp tim\",\"bien_phap\":\"nhập viện\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"hẹp van tim\",\"thuoc\":[],\"created_at\":\"2025-11-14 15:50:18\"}', '2025-11-14 15:50:18'),
(27, '1', 'patient_diseases', 4, 'insert', 'null', '{\"patient_id\":\"25\",\"huyet_ap\":\"120\\/50\",\"can_nang\":\"100\",\"chieu_cao\":\"170\",\"nhiet_do\":\"37\",\"mach_dap\":\"99\",\"nhip_tim\":\"99\",\"trieu_chung\":\"99\",\"chuan_doan\":\"99\",\"bien_phap\":\"99\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"999\",\"thuoc\":[],\"created_at\":\"2025-11-14 15:50:52\"}', '2025-11-14 15:50:52'),
(28, '1', 'patient_diseases', 5, 'insert', 'null', '{\"patient_id\":\"21\",\"huyet_ap\":\"1\",\"can_nang\":\"2\",\"chieu_cao\":\"1\",\"nhiet_do\":\"2\",\"mach_dap\":\"1\",\"nhip_tim\":\"2\",\"trieu_chung\":\"1\",\"chuan_doan\":\"1\",\"bien_phap\":\"1\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"1\",\"thuoc\":[{\"medicine_id\":\"7\",\"quantity\":\"12\",\"dosage\":\"2\",\"note\":\"2\"},{\"medicine_id\":\"5\",\"quantity\":\"1\",\"dosage\":\"1\",\"note\":\"1\"}],\"created_at\":\"2025-11-14 15:55:31\"}', '2025-11-14 15:55:31'),
(29, '1', 'medicines', 21, 'insert', 'null', '{\"medicine_name\":\"Auditlog\",\"created_at\":\"2025-11-14 16:14:53\"}', '2025-11-14 16:14:53'),
(30, '1', 'medicines', 21, 'update', '{\"id\":21,\"medicine_name\":\"Auditlog\",\"is_deleted\":0,\"created_at\":\"2025-11-14 16:14:53\",\"deleted_at\":null,\"updated_at\":null}', '{\"medicine_name\":\"Auditlognew\",\"updated_at\":\"2025-11-14 16:15:44\"}', '2025-11-14 16:15:44'),
(31, '1', 'medicines', 21, 'delete', 'null', '{\"is_deleted\":1}', '2025-11-14 16:15:58'),
(32, '1', 'patient_diseases', 1, 'insert', 'null', '{\"benh_nhan_id\":1,\"trieu_chung\":\"\",\"noi_dung_kham\":null,\"created_at\":\"2025-11-15 18:38:40\"}', '2025-11-15 18:38:40'),
(33, '1', 'book', 1, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"ted\",\"noi_dung_kham\":\"test\",\"date_visit\":\"2025-11-15\",\"time_visit\":\"08:00 - 09:00 \",\"created_at\":\"2025-11-15 18:44:50\"}', '2025-11-15 18:44:50'),
(34, '1', 'book', 2, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"\",\"noi_dung_kham\":\"\",\"date_visit\":\"2025-11-15\",\"time_visit\":\"\",\"created_at\":\"2025-11-15 19:03:44\"}', '2025-11-15 19:03:44'),
(35, '1', 'book', 1, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"testa\",\"noi_dung_kham\":\"test\",\"date_visit\":\"2025-11-15\",\"time_visit\":\"10:00 - 11:00 \",\"created_at\":\"2025-11-15 20:26:37\"}', '2025-11-15 20:26:37'),
(36, '1', 'book', 1, 'delete', '{\"id\":1,\"id_patient\":1,\"date_visit\":\"2025-11-15\",\"time_visit\":\"10:00:00\",\"trieu_chung\":\"testa\",\"noi_dung_kham\":\"test\",\"created_at\":\"2025-11-15 20:26:37\",\"is_deleted\":0}', '{\"is_deleted\":1}', '2025-11-15 21:17:00'),
(37, '1', 'patient_diseases', 6, 'insert', 'null', '{\"patient_id\":\"21\",\"huyet_ap\":\"122\",\"can_nang\":\"1\",\"chieu_cao\":\"1\",\"nhiet_do\":\"1\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"1\",\"chuan_doan\":\"1\",\"bien_phap\":\"1\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"1\",\"thuoc\":[],\"created_at\":\"2025-11-15 21:59:02\"}', '2025-11-15 21:59:02'),
(38, '1', 'patient_diseases', 7, 'insert', 'null', '{\"patient_id\":\"2\",\"huyet_ap\":\"122\",\"can_nang\":\"50\",\"chieu_cao\":\"1\",\"nhiet_do\":\"11\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"2\",\"chuan_doan\":\"2\",\"bien_phap\":\"2\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"2\",\"thuoc\":[],\"created_at\":\"2025-11-16 18:57:16\"}', '2025-11-16 18:57:16'),
(39, '1', 'patient_diseases', 16, 'insert', 'null', '{\"patient_id\":\"21\",\"huyet_ap\":\"1\",\"can_nang\":\"1\",\"chieu_cao\":\"1\",\"nhiet_do\":\"1\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"1\",\"chuan_doan\":\"1\",\"bien_phap\":\"1\",\"nhap_vien\":\"2\",\"tien_su_benh\":\"1\",\"thuoc\":[],\"created_at\":\"2025-11-16 19:34:27\"}', '2025-11-16 19:34:27'),
(40, '1', 'patient_medication_history', 0, 'insert', 'null', '[]', '2025-11-16 19:39:23'),
(41, '1', 'patient_medication_history', 0, 'insert', 'null', '[]', '2025-11-16 19:41:59'),
(42, '1', 'patient_medication_history', 0, 'insert', 'null', '[]', '2025-11-16 19:42:13'),
(43, '2', 'patient_diseases', 18, 'insert', 'null', '{\"patient_id\":\"22\",\"huyet_ap\":\"1\",\"can_nang\":\"1\",\"chieu_cao\":\"1\",\"nhiet_do\":\"1\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"1\",\"chuan_doan\":\"1\",\"bien_phap\":\"1\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"1\",\"created_at\":\"2025-11-17 15:24:48\",\"next_visit_date\":\"2025-11-18\"}', '2025-11-17 15:24:48'),
(44, '2', 'patient_diseases', 19, 'insert', 'null', '{\"patient_id\":\"17\",\"huyet_ap\":\"1\",\"can_nang\":\"1\",\"chieu_cao\":\"1\",\"nhiet_do\":\"1\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"1\",\"chuan_doan\":\"12\",\"bien_phap\":\"1\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"2\",\"created_at\":\"2025-11-17 15:33:19\",\"next_visit_date\":\"2025-10-31\"}', '2025-11-17 15:33:19'),
(45, '1', 'book', 1, 'delete', '{\"id\":1,\"id_patient\":1,\"date_visit\":\"2025-11-15\",\"time_visit\":\"10:00:00\",\"trieu_chung\":\"testa\",\"noi_dung_kham\":\"test\",\"created_at\":\"2025-11-15 20:26:37\",\"is_deleted\":0}', '{\"is_deleted\":1}', '2025-11-17 15:48:17'),
(46, '2', 'patient_diseases', 20, 'insert', 'null', '{\"patient_id\":\"2\",\"huyet_ap\":\"1\",\"can_nang\":\"1\",\"chieu_cao\":\"11\",\"nhiet_do\":\"1\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"1\",\"chuan_doan\":\"1\",\"bien_phap\":\"1\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"1\",\"thuoc\":[{\"medicine_id\":\"11\",\"quantity\":\"1\",\"dosage\":\"1\",\"note\":\"1\"}],\"created_at\":\"2025-11-17 23:55:14\"}', '2025-11-17 23:55:14'),
(47, '2', 'patient_diseases', 21, 'insert', 'null', '{\"patient_id\":\"16\",\"huyet_ap\":\"1\",\"can_nang\":\"1\",\"chieu_cao\":\"1\",\"nhiet_do\":\"1\",\"mach_dap\":\"1\",\"nhip_tim\":\"1\",\"trieu_chung\":\"1\",\"chuan_doan\":\"1\",\"bien_phap\":\"1\",\"nhap_vien\":\"1\",\"tien_su_benh\":\"1\",\"thuoc\":[{\"medicine_id\":\"8\",\"quantity\":\"1\",\"dosage\":\"1\",\"note\":\"1\"}],\"created_at\":\"2025-11-17 23:56:31\"}', '2025-11-17 23:56:31'),
(48, '1', 'book', 2, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"test1\",\"noi_dung_kham\":\"test\",\"date_visit\":\"2025-11-20\",\"time_visit\":\"11:00 - 12:00 \",\"created_at\":\"2025-11-20 01:21:32\"}', '2025-11-20 01:21:32'),
(49, '1', 'book', 3, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"tes51\",\"noi_dung_kham\":\"teata\",\"date_visit\":\"2025-11-21\",\"time_visit\":\"15:00 - 16:00 \",\"created_at\":\"2025-11-20 01:21:43\"}', '2025-11-20 01:21:43'),
(50, '1', 'book', 4, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"test\",\"noi_dung_kham\":\"tets\",\"date_visit\":\"2025-11-28\",\"time_visit\":\"15:00 - 16:00 \",\"created_at\":\"2025-11-20 01:21:50\"}', '2025-11-20 01:21:50'),
(51, '1', 'book', 5, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"test\",\"noi_dung_kham\":\"test\",\"date_visit\":\"2025-11-30\",\"time_visit\":\"13:00 - 14:00 \",\"created_at\":\"2025-11-20 01:21:59\"}', '2025-11-20 01:21:59'),
(52, '1', 'book', 6, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"tét\",\"noi_dung_kham\":\"t\",\"date_visit\":\"2025-11-27\",\"time_visit\":\"11:00 - 12:00 \",\"created_at\":\"2025-11-20 01:52:56\"}', '2025-11-20 01:52:56'),
(53, '1', 'book', 7, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"test\",\"noi_dung_kham\":\"test - nơi khám\",\"date_visit\":\"2025-11-29\",\"time_visit\":\"13:00 - 14:00 \",\"created_at\":\"2025-11-20 02:02:21\"}', '2025-11-20 02:02:21'),
(54, '1', 'book', 8, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"test\",\"noi_dung_kham\":\"test nơi khám\",\"date_visit\":\"2025-12-01\",\"time_visit\":\"15:00 - 16:00 \",\"created_at\":\"2025-11-20 02:02:47\"}', '2025-11-20 02:02:47'),
(55, '1', 'book', 9, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"test\",\"noi_dung_kham\":\"test igglog\",\"date_visit\":\"2025-11-20\",\"time_visit\":\"16:00 - 17:30 \",\"created_at\":\"2025-11-20 02:19:36\"}', '2025-11-20 02:19:36'),
(56, '1', 'users', 23, 'insert', 'null', '{\"display_name\":\"testlog\",\"user_name\":\"tsstlog\",\"role\":\"1\"}', '2025-11-20 02:22:12'),
(57, '1', 'users', 23, 'update', '{\"id\":23,\"display_name\":\"testlog\",\"user_name\":\"tsstlog\",\"password\":\"c4ca4238a0b923820dcc509a6f75849b\",\"role\":1,\"is_deleted\":0,\"profile_picture\":null,\"created_at\":\"0000-00-00 00:00:00\"}', '{\"display_name\":\"testlog\",\"user_name\":\"tsstloggggg\",\"role\":\"1\"}', '2025-11-20 02:22:55'),
(58, '1', 'users', 23, 'delete', '{\"id\":23,\"display_name\":\"testlog\",\"user_name\":\"tsstloggggg\",\"password\":\"c4ca4238a0b923820dcc509a6f75849b\",\"role\":1,\"is_deleted\":0,\"profile_picture\":null,\"created_at\":\"2025-11-20 02:22:55\"}', '{\"is_deleted\":1}', '2025-11-20 02:24:17'),
(59, '2', 'appointment_status_log', 9, 'update', '{\"status\":null,\"doctor_note\":null}', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '2025-11-20 02:35:42'),
(60, '2', 'appointment_status_log', 6, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '{\"status\":\"rejected\",\"doctor_note\":\"test\"}', '2025-11-20 02:36:04'),
(61, '5', 'book', 10, 'insert', 'null', '{\"id_benh_nhan\":5,\"trieu_chung\":\"test neww\",\"noi_dung_kham\":\"test new\",\"date_visit\":\"2025-11-30\",\"time_visit\":\"11:00 - 12:00 \",\"created_at\":\"2025-11-20 23:40:52\"}', '2025-11-20 23:40:52'),
(62, '1', 'book', 11, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"hải\",\"noi_dung_kham\":\"hải\",\"date_visit\":\"2025-11-27\",\"time_visit\":\"14:00 - 15:00 \",\"created_at\":\"2025-11-20 23:44:19\"}', '2025-11-20 23:44:19'),
(63, '6', 'book', 12, 'insert', 'null', '{\"id_benh_nhan\":6,\"trieu_chung\":\"test2111\",\"noi_dung_kham\":\"test2111\",\"date_visit\":\"2025-11-21\",\"time_visit\":\"13:00 - 14:00 \",\"created_at\":\"2025-11-21 01:29:54\"}', '2025-11-21 01:29:54'),
(64, '1', 'appointment_status_log', 12, 'update', '{\"status\":null,\"doctor_note\":null}', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '2025-11-21 01:30:11'),
(65, '1', 'appointment_status_log', 12, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '{\"status\":\"rejected\",\"doctor_note\":\"2111\"}', '2025-11-21 01:33:42'),
(66, '6', 'appointment_status_log', 9, 'update', '{\"status\":\"confirmed\",\"doctor_note\":\"\"}', '{\"status\":\"pending\",\"doctor_note\":\"\"}', '2025-11-21 01:37:01'),
(67, '1', 'book', 1, 'insert', 'null', '{\"id_benh_nhan\":1,\"trieu_chung\":\"211\",\"noi_dung_kham\":\"211\",\"date_visit\":\"2025-11-21\",\"time_visit\":\"07:00 - 08:00 \",\"created_at\":\"2025-11-21 12:46:54\"}', '2025-11-21 12:46:54'),
(68, '1', 'users', 24, 'insert', 'null', '{\"display_name\":\"Hoàng Huyền\",\"user_name\":\"hoanghuyen\",\"role\":\"2\"}', '2025-11-21 12:53:13'),
(69, '24', 'patients', 27, 'insert', 'null', '{\"patient_name\":\"Hoàng Huyền\",\"address\":\"Ha Nam\",\"cnic\":\"035304002700\",\"date_of_birth\":\"2020-06-14\",\"phone_number\":\"0339877303\",\"gender\":\"Nữ\"}', '2025-11-21 12:54:21'),
(70, '11', 'book', 2, 'insert', 'null', '{\"id_benh_nhan\":11,\"trieu_chung\":\"đau họng\",\"noi_dung_kham\":\"đau họng\",\"date_visit\":\"2025-11-22\",\"time_visit\":\"14:00 - 15:00 \",\"created_at\":\"2025-11-21 12:54:54\"}', '2025-11-21 12:54:54');

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
(1, 1, '2025-11-21', '07:00:00', '211', '211', '2025-11-21 12:46:54', 0),
(2, 11, '2025-11-22', '14:00:00', 'đau họng', 'đau họng', '2025-11-21 12:54:54', 0);

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
(1, 'Amoxicillin', 0, NULL, NULL, NULL),
(2, 'Mefenamic', 0, NULL, NULL, NULL),
(3, 'Losartan', 0, NULL, NULL, NULL),
(4, 'Antibiotic', 0, NULL, NULL, NULL),
(5, 'Antihistamine', 0, NULL, NULL, NULL),
(6, 'Atorvastatin', 0, NULL, NULL, NULL),
(7, 'Oxymetazoline', 0, NULL, NULL, NULL),
(8, 'Smecta', 0, NULL, NULL, NULL),
(9, 'Yumagel', 0, NULL, NULL, NULL),
(10, 'Testgvfg', 0, NULL, NULL, NULL),
(11, 'Test', 0, NULL, NULL, '2025-10-22 05:51:07'),
(12, 'Test1', 0, NULL, NULL, '2025-10-22 06:00:15'),
(13, 'Nguuu', 1, NULL, '2025-11-06 11:16:06', '2025-10-22 06:08:14'),
(14, 'Tét', 1, '2025-10-22 05:42:33', '2025-10-22 05:57:49', '2025-10-22 05:49:57'),
(15, 'T', 1, '2025-10-22 05:42:38', NULL, NULL),
(16, 'Ngu', 1, '2025-10-22 05:43:00', NULL, NULL),
(17, 'Amoxicillin11111d111112àafafafa', 0, '2025-10-22 06:03:36', NULL, '2025-10-22 06:11:08'),
(18, 'Amoxicillin11111', 0, '2025-10-22 06:20:15', NULL, NULL),
(19, 'Amoxicillinauditnewnew', 1, '2025-11-06 11:10:11', '2025-11-06 11:14:27', '2025-11-06 11:12:20'),
(20, 'Ted', 1, '2025-11-06 16:57:16', '2025-11-06 16:57:36', '2025-11-06 16:57:24'),
(21, 'Auditlognew', 1, '2025-11-14 16:14:53', '2025-11-14 16:15:58', '2025-11-14 16:15:44');

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
(1, 'Mark Cooper', 'Sample Address 101 - Updated', '123654789', '2001-09-06', '091235649879', 'Nam', 1, '2025-09-11 02:22:05', NULL, NULL),
(2, 'Hải Nguyễn', 'Cau Giay Ha Noi', '123456789', '2003-01-19', '091235649879', 'Nam', 0, '2025-09-27 13:49:12', NULL, NULL),
(3, 'Hồng Hải', 'Hà Nội', '314141441414141', '2025-09-24', '091235649879', 'Nam', 1, '2025-09-27 13:49:26', NULL, NULL),
(4, 'Test', 'Test', '314141441414142', '2025-09-20', '091235649879', 'Khác', 1, '2025-09-27 13:49:29', NULL, NULL),
(5, 'Ttesst', 'Cau Giay Ha Noi', '314141441414143', '2025-09-13', '091235649879', 'Nữ', 1, '2025-09-27 13:49:32', NULL, NULL),
(6, 'Test', 'Test', '1313131313', '2025-09-16', '131313131', 'Nam', 1, '2025-09-29 13:49:44', NULL, NULL),
(9, 'Test', 'Cau Giay Ha Noi', '314141441414146', '2025-09-25', '091235649879', 'Nam', 1, '2025-09-17 21:36:10', NULL, NULL),
(10, 'Nguyễn Hồng Hải', '39 Hồ Tùng Mậu', '025203005770', '2003-01-19', '0362111351', 'Nam', 1, '2025-09-18 02:52:02', NULL, NULL),
(12, 'Nguyễn Hồng Hải', '39 Hồ Tùng Mậu', '02520300577099', '2025-09-05', '091235649879', 'Nam', 1, '2025-09-18 02:53:27', NULL, NULL),
(16, 'Nguyễn Hồng Hải Hải', '39 Hồ Tùng Mậu', '0987654321', '2025-09-10', '18777804236', 'Nam', 0, '2025-09-18 09:07:47', NULL, NULL),
(17, 'Mark Cooper', 'Cau Giay Ha Noi', '31414144141414', '2025-09-27', '091235649879', 'Nam', 0, '2025-09-27 13:57:30', NULL, NULL),
(19, 'Test', 'Cầu Giấy - Hồ Tùng Mậu', '025203005771', '2000-09-18', '0987654312', 'Nam', 1, '2025-09-30 01:59:07', NULL, NULL),
(20, 'Mark Cooperee', 'Sample Address 101 - Updated', '025203005774', '2002-09-30', '09876543122', 'Khác', 0, '2025-09-30 01:59:54', NULL, NULL),
(21, 'Hải Nguyễn Audit New New', '39 Hồ Tùng Mậu', '025203005775', '2004-09-30', '0362111351', 'Nam', 0, '2025-09-30 02:04:30', NULL, NULL),
(22, 'test211', 'Cau Giay Ha Noi', '012345678911', '2003-11-06', '0987676211', 'Nam', 0, '2025-11-06 10:28:34', NULL, NULL),
(23, 'Testnewaudit', 'Cau Giay Ha Noi', '112345678922', '2004-11-07', '0977676211', 'Nam', 1, '2025-11-06 10:47:49', NULL, NULL),
(24, 'Mark Cooper Audit New', 'Cau Giay Ha Noi', '112345678955', '2005-11-06', '0312111356', 'Nam', 1, '2025-11-06 10:49:03', NULL, NULL),
(25, 'Mark Cooperdddddd', 'Cau Giay Ha Noi', '123456789134', '2004-11-06', '0971676211', 'Nam', 0, '2025-11-06 10:58:49', NULL, NULL),
(26, 'Nguyễn Hồng Hải Audit New', '39 Hồ Tùng Mậu', '025203005776', '2003-05-08', '0362111355', 'Nam', 1, '2025-11-06 16:55:07', NULL, NULL),
(27, 'Hoàng Huyền', 'Ha Nam', '035304002700', '2020-06-14', '0339877303', 'Nữ', 0, '2025-11-21 12:54:21', NULL, NULL);

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
(1, 21, 'Đau đầu chóng mặt, thiếu máu, tim đập loạn', 'Hẹp van tim', '120/50', '10', 50, 170, 37, '99', NULL, NULL, 1, 0, 'Rối loạn nhịp tim', 'Điều trị tại bệnh viện', '2025-11-11 13:26:23', NULL, NULL, '2025-11-20'),
(2, 16, 'Béo Phì', 'không có', '120/50', '99', 100, 170, 37, '99', NULL, NULL, 2, 0, 'Thừa cân, Thừa chất', 'Giảm cân', '2025-11-11 13:27:24', NULL, NULL, '2025-11-23'),
(3, 17, 'khó thở, đau đầu, chóng mặt', 'hẹp van tim', '120/50', '99', 100, 170, 36, '99', NULL, NULL, 1, 0, 'rối loạn nhịp tim', 'nhập viện', '2025-11-14 15:50:18', NULL, NULL, '2025-11-16'),
(4, 25, '99', '999', '120/50', '99', 100, 170, 37, '99', NULL, NULL, 1, 0, '99', '99', '2025-11-14 15:50:52', NULL, NULL, '2025-11-17'),
(5, 21, '1', '1', '1', '2', 2, 1, 2, '1', NULL, NULL, 1, 0, '1', '1', '2025-11-14 15:55:31', NULL, NULL, '2025-11-27'),
(6, 21, '1', '1', '122', '1', 1, 1, 1, '1', NULL, NULL, 1, 0, '1', '1', '2025-11-15 21:59:02', NULL, NULL, '2025-11-22'),
(7, 2, '2', '2', '122', '1', 50, 1, 11, '1', NULL, NULL, 1, 0, '2', '2', '2025-11-16 18:57:16', NULL, NULL, '2025-11-27'),
(16, 21, '1', '1', '1', '1', 1, 1, 1, '1', NULL, NULL, 2, 0, '1', '1', '2025-11-16 19:34:27', NULL, NULL, '2025-11-27'),
(17, 21, '1', '1', '1', '1', 1, 1, 1, '1', NULL, NULL, 2, 0, '1', '1', '2025-11-16 20:06:45', NULL, NULL, '2025-11-27'),
(18, 22, '1', '1', '1', '1', 1, 1, 1, '1', NULL, NULL, 1, 0, '1', '1', '2025-11-17 15:24:48', NULL, NULL, '2025-11-18'),
(19, 17, '1', '2', '1', '1', 1, 1, 1, '1', NULL, NULL, 1, 0, '12', '1', '2025-11-17 15:33:19', NULL, NULL, '2025-10-31'),
(20, 2, '1', '1', '1', '1', 1, 11, 1, '1', NULL, NULL, 1, 0, '1', '1', '2025-11-17 23:55:14', NULL, NULL, '2025-11-30'),
(21, 16, '1', '1', '1', '1', 1, 1, 1, '1', NULL, NULL, 1, 0, '1', '1', '2025-11-17 23:56:31', NULL, NULL, '2025-11-30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patient_medication_history`
--

CREATE TABLE `patient_medication_history` (
  `id` int(11) NOT NULL,
  `quantity` tinyint(4) NOT NULL,
  `dosage` varchar(20) NOT NULL,
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
(1, 12, '2', '2', 21, 7, '2025-11-14 15:55:31', 0),
(2, 1, '1', '1', 21, 5, '2025-11-14 15:55:31', 0),
(3, 1, '1', '1', 2, 11, '2025-11-17 23:55:14', 0),
(4, 1, '1', '1', 16, 8, '2025-11-17 23:56:31', 0);

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
(1, 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 1, 0, NULL, '0000-00-00 00:00:00'),
(2, 'John Doe', 'jdoe', '9c86d448e84d4ba23eb089e0b5160207', 2, 0, NULL, '0000-00-00 00:00:00'),
(3, 'Hải', 'a', 'c4ca4238a0b923820dcc509a6f75849b', 1, 0, NULL, '0000-00-00 00:00:00'),
(4, 'vk', 'nm', 'c4ca4238a0b923820dcc509a6f75849b', 2, 0, NULL, '2025-09-27 14:37:56'),
(5, 'Hồng Hải', 'admin1', 'c81e728d9d4c2f636f067f89cc14862c', 1, 0, NULL, '0000-00-00 00:00:00'),
(10, 'tet', 'ttee', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, NULL, '0000-00-00 00:00:00'),
(11, 'ngu', 'g', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, NULL, '0000-00-00 00:00:00'),
(12, 'Hải', 'fsfs', 'c4ca4238a0b923820dcc509a6f75849b', 1, 0, NULL, '0000-00-00 00:00:00'),
(13, 'test', 'ttttt', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '0000-00-00 00:00:00'),
(14, 'ttt', 'ttt', '698d51a19d8a121ce581499d7b701668', 2, 1, NULL, '0000-00-00 00:00:00'),
(15, '111', '1', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '0000-00-00 00:00:00'),
(16, 'Test', '31414144141414', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '2025-09-17 21:36:10'),
(17, 'Nguyễn Hồng Hải', '025203005770', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '2025-09-17 21:38:06'),
(19, 'Nguyễn Hồng Hải', '02520300577099', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '2025-09-18 02:48:57'),
(20, 'audit', 'audit', 'c4ca4238a0b923820dcc509a6f75849b', 2, 1, NULL, '2025-11-06 11:17:03'),
(21, 'auditdđ', 'audit1', 'c4ca4238a0b923820dcc509a6f75849b', 1, 0, NULL, '2025-11-06 16:57:49'),
(22, 'testaduit', 'audit235', 'c4ca4238a0b923820dcc509a6f75849b', 1, 1, NULL, '2025-11-06 11:06:03'),
(23, 'testlog', 'tsstloggggg', 'c4ca4238a0b923820dcc509a6f75849b', 1, 1, NULL, '2025-11-20 02:24:17'),
(24, 'Hoàng Huyền', 'hoanghuyen', 'c4ca4238a0b923820dcc509a6f75849b', 2, 0, NULL, '0000-00-00 00:00:00');

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
(1, '0987654321', 'Nguyễn Hồng Hải Hải', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 16, '2025-09-18 09:07:47'),
(2, '31414144141414', 'Mark Cooper', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 17, '2025-09-27 13:57:30'),
(3, '025203005771', 'Test', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, 19, '2025-09-30 01:59:07'),
(4, '025203005774', 'Mark Cooperee', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 20, '2025-09-30 01:59:54'),
(5, '025203005775', 'Hải Nguyễn Audit New New', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 21, '2025-09-30 02:04:30'),
(6, '012345678911', 'test2111', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 22, '2025-11-06 10:28:34'),
(7, '112345678922', 'Testnewaudit', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, 23, '2025-11-06 10:47:49'),
(8, '112345678955', 'Mark Cooper Audit New', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, 24, '2025-11-06 10:49:03'),
(9, '123456789134', 'Mark Cooperdddddd', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 25, '2025-11-06 10:58:49'),
(10, '025203005776', 'Nguyễn Hồng Hải Audit New', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, 26, '2025-11-06 16:55:07'),
(11, '035304002700', 'Hoàng Huyền', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 27, '2025-11-21 12:54:21');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `patient_diseases`
--
ALTER TABLE `patient_diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `patient_medication_history`
--
ALTER TABLE `patient_medication_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `user_patients`
--
ALTER TABLE `user_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
