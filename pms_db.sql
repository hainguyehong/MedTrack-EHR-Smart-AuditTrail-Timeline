-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 21, 2025 lúc 08:19 PM
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
-- Cấu trúc bảng cho bảng `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `medicine_name` varchar(60) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `medicines`
--

INSERT INTO `medicines` (`id`, `medicine_name`, `is_deleted`) VALUES
(1, 'Amoxicillin', 0),
(2, 'Mefenamic', 0),
(3, 'Losartan', 0),
(4, 'Antibiotic', 0),
(5, 'Antihistamine', 0),
(6, 'Atorvastatin', 0),
(7, 'Oxymetazoline', 0),
(8, 'Smecta', 0),
(9, 'Yumagel', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medicine_details`
--

CREATE TABLE `medicine_details` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `packing` varchar(60) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `medicine_details`
--

INSERT INTO `medicine_details` (`id`, `medicine_id`, `packing`, `is_deleted`) VALUES
(1, 1, '150', 0),
(3, 5, '50', 0),
(4, 6, '25', 0),
(5, 3, '80', 0),
(6, 2, '100', 0),
(7, 7, '25', 0),
(10, 9, '50', 0),
(11, 8, '50', 0),
(16, 4, '50', 0);

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
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patients`
--

INSERT INTO `patients` (`id`, `patient_name`, `address`, `cnic`, `date_of_birth`, `phone_number`, `gender`, `is_deleted`, `created_at`) VALUES
(1, 'Mark Cooper', 'Sample Address 101 - Updated', '123654789', '2001-09-06', '091235649879', 'Nam', 0, '2025-09-11 02:22:05'),
(2, 'Hải Nguyễn', 'Cau Giay Ha Noi', '123456789', '2003-01-19', '091235649879', 'Nam', 0, '0000-00-00 00:00:00'),
(3, 'Hồng Hải', 'Hà Nội', '314141441414141', '2025-09-24', '091235649879', 'Nam', 0, '0000-00-00 00:00:00'),
(4, 'Test', 'Test', '314141441414142', '2025-09-20', '091235649879', 'Khác', 1, '0000-00-00 00:00:00'),
(5, 'Ttesst', 'Cau Giay Ha Noi', '314141441414143', '2025-09-13', '091235649879', 'Nữ', 0, '0000-00-00 00:00:00'),
(6, 'Test', 'Test', '1313131313', '2025-09-16', '131313131', 'Nữ', 0, '0000-00-00 00:00:00'),
(9, 'Test', 'Cau Giay Ha Noi', '314141441414146', '2025-09-25', '091235649879', 'Nam', 0, '2025-09-17 21:36:10'),
(10, 'Nguyễn Hồng Hải', '39 Hồ Tùng Mậu', '025203005770', '2003-01-19', '0362111351', 'Nam', 0, '2025-09-18 02:52:02'),
(12, 'Nguyễn Hồng Hải', '39 Hồ Tùng Mậu', '02520300577099', '2025-09-05', '091235649879', 'Nam', 1, '2025-09-18 02:53:27'),
(16, 'Nguyễn Hồng Hải Hải', '39 Hồ Tùng Mậu', '0987654321', '2025-09-10', '0362111351', 'Nam', 0, '2025-09-18 09:07:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patient_diseases`
--

CREATE TABLE `patient_diseases` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `trieu_chung` text NOT NULL,
  `tien_su_benh` text NOT NULL,
  `huyet_ap` varchar(155) DEFAULT NULL,
  `nhip_tim` varchar(155) DEFAULT NULL,
  `can_nang` int(11) NOT NULL,
  `chieu_cao` int(11) NOT NULL,
  `nhiet_do` int(11) DEFAULT NULL,
  `mach_dap` varchar(155) DEFAULT NULL,
  `anh_sieu_am` varchar(155) DEFAULT NULL,
  `anh_chup_xq` varchar(155) DEFAULT NULL,
  `nhap_vien` int(11) NOT NULL COMMENT '0- ko nhập viện, 1 - nhập viện ',
  `is_deleted` int(11) NOT NULL,
  `chuan_doan` varchar(255) NOT NULL,
  `bien_phap` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patient_diseases`
--

INSERT INTO `patient_diseases` (`id`, `patient_id`, `trieu_chung`, `tien_su_benh`, `huyet_ap`, `nhip_tim`, `can_nang`, `chieu_cao`, `nhiet_do`, `mach_dap`, `anh_sieu_am`, `anh_chup_xq`, `nhap_vien`, `is_deleted`, `chuan_doan`, `bien_phap`, `created_at`) VALUES
(1, 1, 'ada', 'adad', '12', '12', 12, 12, 21, '12', 'uploads/anhsieuam/anh-dep-68.jpg', 'uploads/xquang/anh-dep-68.jpg', 2, 0, 'đâs', 'adada', '2025-09-20 16:28:12'),
(2, 1, 'ada', 'adad', '12', '12', 12, 12, 21, '12', 'uploads/anhsieuam/anh-dep-68.jpg', 'uploads/xquang/anh-dep-68.jpg', 2, 0, 'đâs', 'adada', '2025-09-20 21:28:58'),
(18, 1, 'ada', 'adad', '12', '12', 12, 12, 21, '12', 'uploads/anhsieuam/anh-dep-68.jpg', 'uploads/xquang/anh-dep-68.jpg', 2, 0, 'đâs', 'adada', '2025-09-20 22:39:15'),
(20, 1, 'ada', 'adad', '12', '12', 12, 12, 21, '12', 'uploads/anhsieuam/anh-dep-68.jpg', 'uploads/xquang/anh-dep-68.jpg', 2, 0, 'đâs', 'adada', '2025-09-20 22:51:21'),
(21, 16, 'test', 'test', '12', '12', 12, 12, 12, '12', 'uploads/anhsieuam/', 'uploads/xquang/', 1, 0, 'ff', 'fff', '2025-09-21 00:24:51');

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
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `visit_date` date DEFAULT NULL,
  `next_visit_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `patient_medication_history`
--

INSERT INTO `patient_medication_history` (`id`, `quantity`, `dosage`, `note`, `patient_id`, `medicine_id`, `created_at`, `is_deleted`, `visit_date`, `next_visit_date`) VALUES
(1, 12, '12', 'test', 16, 1, '2025-09-21 00:24:51', 0, NULL, NULL);

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
(1, '2022-06-28', '2022-06-30', '120/80', '65 kg.', 'Wounded Arm', 1, 0),
(2, '2022-06-30', '2022-07-02', '120/80', '65 kg.', 'Rhinovirus', 1, 0),
(4, '2025-09-08', '2025-09-13', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(5, '2025-08-09', '2025-12-09', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(6, '2025-08-09', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(7, '0000-00-00', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(8, '0000-00-00', '2025-01-10', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(9, '2025-09-09', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(10, '2025-01-10', '2025-10-10', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(11, '2025-09-30', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 1, 0),
(12, '2025-09-24', '0000-00-00', '100', '50', 'Chơi game lắm', 2, 1),
(13, '2025-09-11', '0000-00-00', '99', '50', 'ngáo', 2, 1),
(14, '2025-10-01', '0000-00-00', '122', '100', 'Chơi game lắm', 2, 1),
(15, '2025-09-24', '0000-00-00', '122', '50', 'ngáo', 2, 0),
(16, '2025-09-11', '0000-00-00', '122', '50', 'ngủ ít chơi nhiều', 5, 0),
(17, '2025-09-09', '0000-00-00', '122', '50', 'ngáo', 5, 0),
(19, '2025-09-18', '0000-00-00', '11', '11', 'ngủ ít chơi nhiều', 6, 0);

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
(4, 'v', 'n', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '0000-00-00 00:00:00'),
(5, 'Hồng Hải', 'admin1', 'c81e728d9d4c2f636f067f89cc14862c', 1, 0, NULL, '0000-00-00 00:00:00'),
(10, 'tet', 'ttee', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, NULL, '0000-00-00 00:00:00'),
(11, 'ngu', 'g', 'c4ca4238a0b923820dcc509a6f75849b', 3, 1, NULL, '0000-00-00 00:00:00'),
(12, 'Hải', 'fsfs', 'c4ca4238a0b923820dcc509a6f75849b', 1, 0, NULL, '0000-00-00 00:00:00'),
(13, 'test', 'ttttt', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '0000-00-00 00:00:00'),
(14, 'ttt', 'ttt', '698d51a19d8a121ce581499d7b701668', 2, 1, NULL, '0000-00-00 00:00:00'),
(15, '111', '1', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '0000-00-00 00:00:00'),
(16, 'Test', '31414144141414', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '2025-09-17 21:36:10'),
(17, 'Nguyễn Hồng Hải', '025203005770', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '2025-09-17 21:38:06'),
(19, 'Nguyễn Hồng Hải', '02520300577099', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, NULL, '2025-09-18 02:48:57');

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
(1, '0987654321', 'Nguyễn Hồng Hải Hải', 'c4ca4238a0b923820dcc509a6f75849b', 3, 0, 16, '2025-09-18 09:07:47');

--
-- Chỉ mục cho các bảng đã đổ
--

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_medicine_details_medicine_id` (`medicine_id`);

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
-- AUTO_INCREMENT cho bảng `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `medicine_details`
--
ALTER TABLE `medicine_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `patient_diseases`
--
ALTER TABLE `patient_diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `patient_medication_history`
--
ALTER TABLE `patient_medication_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `user_patients`
--
ALTER TABLE `user_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `medicine_details`
--
ALTER TABLE `medicine_details`
  ADD CONSTRAINT `fk_medicine_details_medicine_id` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);

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
