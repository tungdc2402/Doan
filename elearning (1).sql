-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 05:58 AM
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
-- Database: `elearning`
--

-- --------------------------------------------------------

--
-- Table structure for table `bai_giang`
--

CREATE TABLE `bai_giang` (
  `id` int(11) NOT NULL,
  `chuong_id` int(11) NOT NULL,
  `ten_bai` varchar(255) NOT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `video` varchar(100) NOT NULL,
  `thoi_luong` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bai_giang`
--

INSERT INTO `bai_giang` (`id`, `chuong_id`, `ten_bai`, `thu_tu`, `video`, `thoi_luong`) VALUES
(11, 1, 'Bài 1: Giới thiệu khóa học', 1, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '04:32'),
(12, 1, 'Bài 2: Tại sao nên học C++?', 2, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '03:28'),
(13, 1, 'Bài 3: Cài đặt Visual Studio Code và MinGW', 3, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '04:32'),
(14, 2, 'Bài 4: Biến và các kiểu dữ liệu', 1, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '03:10'),
(15, 2, 'Bài 5: Toán tử trong C++', 2, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '03:20'),
(16, 2, 'Bài 6: Nhập và xuất dữ liệu (cin/cout)', 3, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '03:01'),
(17, 3, 'Bài 7: Câu lệnh if-else', 1, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '04:32'),
(18, 3, 'Bài 8: Vòng lặp for', 2, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '04:32'),
(19, 3, 'Bài 9: Vòng lặp while và do-while', 3, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '04:32'),
(20, 3, 'Bài 10: Switch-case', 4, 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4', '04:32');

-- --------------------------------------------------------

--
-- Table structure for table `bai_kiem_tra`
--

CREATE TABLE `bai_kiem_tra` (
  `id` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `ngay_mo` datetime DEFAULT NULL,
  `han_nop` datetime DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT 0,
  `so_lan_cho_phep` int(11) DEFAULT 1,
  `so_lan_da_lam` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bai_kiem_tra`
--

INSERT INTO `bai_kiem_tra` (`id`, `tieu_de`, `mo_ta`, `ngay_mo`, `han_nop`, `trang_thai`, `so_lan_cho_phep`, `so_lan_da_lam`) VALUES
(1, 'Toán 10/09', 'Làm bài kiểm tra trước thời hạn', '2024-09-10 00:00:00', '2024-09-10 17:00:00', 0, 2, 1),
(2, 'Lý 17/09', 'Làm bài kiểm tra trước thời hạn', '2024-09-10 00:00:00', '2024-09-17 17:00:00', 1, 1, 0),
(3, 'Nộp bài tập Chương 1', 'Làm bài kiểm tra trước thời hạn', '2024-09-17 00:00:00', '2024-09-22 00:00:00', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id` int(11) NOT NULL,
  `khoa_hoc_id` int(11) DEFAULT NULL,
  `ten_user` varchar(50) DEFAULT NULL,
  `avatar_text` varchar(5) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `binh_luan`
--

INSERT INTO `binh_luan` (`id`, `khoa_hoc_id`, `ten_user`, `avatar_text`, `noi_dung`, `ngay_tao`) VALUES
(1, 101, 'Nguyễn Văn A', 'A', 'Thầy ơi cho em hỏi cài VS Code được không ạ?', '2025-12-04 17:03:45'),
(2, 101, 'Trần B', 'B', 'Phần con trỏ hơi khó hiểu, mong có thêm ví dụ.', '2025-12-04 17:03:45'),
(3, 101, 'Học viên mới', 'HV', '1', '2025-12-04 17:04:37'),
(4, 101, 'Đặng Công Tùng', 'Đ', 'adaf', '2025-12-04 22:09:04'),
(5, 101, 'Học viên mới', 'HV', '123', '2025-12-05 00:48:54'),
(6, 101, 'Học viên', 'HV', '9', '2025-12-05 20:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `cau_hoi`
--

CREATE TABLE `cau_hoi` (
  `id` int(11) NOT NULL,
  `bai_kiem_tra_id` int(11) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `giai_thich` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cau_hoi`
--

INSERT INTO `cau_hoi` (`id`, `bai_kiem_tra_id`, `noi_dung`, `giai_thich`) VALUES
(1, 1, 'Trong HTML, thẻ nào được sử dụng để định nghĩa một đoạn văn bản?', 'Thẻ <p> là viết tắt của \"paragraph\" dùng để định nghĩa đoạn văn.'),
(2, 1, 'Ký tự nào được sử dụng để bắt đầu một biến trong PHP?', 'Trong PHP, mọi biến đều bắt đầu bằng ký tự $ (Ví dụ: $name).'),
(3, 1, 'Thuộc tính CSS nào dùng để thay đổi màu chữ?', 'Thuộc tính \"color\" dùng cho màu chữ, còn \"background-color\" dùng cho màu nền.'),
(4, 1, 'HTML là một ngôn ngữ lập trình?', 'HTML là Ngôn ngữ đánh dấu siêu văn bản (Markup Language), không phải ngôn ngữ lập trình.'),
(5, 1, 'Làm thế nào để kết thúc một câu lệnh trong PHP?', 'Dấu chấm phẩy (;) là bắt buộc để kết thúc lệnh trong PHP.'),
(6, 3, 'HTML dùng để làm gì?', 'HTML là ngôn ngữ đánh dấu cấu trúc trang web.'),
(7, 3, 'Phím tắt sao chép trên Windows?', 'Ctrl + C để sao chép.'),
(8, 3, 'Biến trong PHP bắt đầu bằng ký tự nào?', 'Trong PHP mọi biến bắt đầu bằng $.'),
(9, 3, 'Số nào là số chẵn?', '2 là số chẵn duy nhất trong các số nguyên tố nhỏ.'),
(10, 3, 'CSS dùng để làm gì?', 'CSS dùng để trang trí giao diện.');

-- --------------------------------------------------------

--
-- Table structure for table `chuong_hoc`
--

CREATE TABLE `chuong_hoc` (
  `id` int(11) NOT NULL,
  `khoa_hoc_id` int(11) NOT NULL,
  `ten_chuong` varchar(255) NOT NULL,
  `thu_tu` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chuong_hoc`
--

INSERT INTO `chuong_hoc` (`id`, `khoa_hoc_id`, `ten_chuong`, `thu_tu`) VALUES
(1, 101, 'Chương 1: Giới thiệu và Cài đặt môi trường', 1),
(2, 101, 'Chương 2: Các khái niệm cơ bản', 2),
(3, 101, 'Chương 3: Cấu trúc điều khiển', 3);

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id` int(11) NOT NULL,
  `khoa_hoc_id` int(11) DEFAULT NULL,
  `ten_user` varchar(50) DEFAULT NULL,
  `avatar_text` varchar(5) DEFAULT NULL,
  `so_sao` int(11) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `hinh_anh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `danh_gia`
--

INSERT INTO `danh_gia` (`id`, `khoa_hoc_id`, `ten_user`, `avatar_text`, `so_sao`, `noi_dung`, `ngay_tao`, `hinh_anh`) VALUES
(1, 101, 'Nghiaa Trongg', 'NT', 5, 'hay dễ hiểu cảm ơn 28 tech', '2025-12-04 16:52:38', NULL),
(2, 101, 'Nguyễn Tiến Dũng', 'NTD', 5, 'Khoá học chất lượng, cảm ơn team 28tech <3', '2025-12-04 16:52:38', NULL),
(3, 101, 'Đoàn Quang Thành', 'DQT', 4, 'cái slide ở trên vid để ảnh giảng lấy ở đâu v mọi người', '2025-12-04 16:52:38', NULL),
(4, 101, 'Bùi Ngọc Trung', 'BNT', 5, 'Giọng anh Lộc giảng quá hay luôn', '2025-12-04 16:52:38', NULL),
(5, 101, 'Lê Thị Lan Anh', 'LA', 3, 'Mình đã gửi yêu cầu tham gia nhóm zalo lớp và chưa được chấp nhận.', '2025-12-04 16:52:38', NULL),
(6, 101, 'ttungd', 'TT', 3, '123', '2025-12-04 17:10:51', NULL),
(7, 101, 'ttungd', 'TT', 5, 'gagsfasf', '2025-12-04 17:17:14', '1764843434_Ảnh chụp màn hình 2025-09-06 101300.png'),
(8, 101, 'Đặng Công Tùng', '?T', 5, 'tatqtqe', '2025-12-04 22:12:04', '1764861124_198a85dd-0476-426e-b90d-b61bbf1370bf.jpg'),
(9, 101, 'Đặng Công Tùnggg', '?T', 5, 'agaga', '2025-12-05 01:21:23', '1764872483_やゆよ.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `dap_an`
--

CREATE TABLE `dap_an` (
  `id` int(11) NOT NULL,
  `cau_hoi_id` int(11) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `la_dap_an_dung` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dap_an`
--

INSERT INTO `dap_an` (`id`, `cau_hoi_id`, `noi_dung`, `la_dap_an_dung`) VALUES
(1, 1, '<p>', 1),
(2, 1, '<h1>', 0),
(3, 1, '<div>', 0),
(4, 1, '<span>', 0),
(5, 2, '&', 0),
(6, 2, '$', 1),
(7, 2, '#', 0),
(8, 2, '@', 0),
(9, 3, 'font-color', 0),
(10, 3, 'text-color', 0),
(11, 3, 'color', 1),
(12, 3, 'background-color', 0),
(13, 4, 'Đúng', 0),
(14, 4, 'Sai', 1),
(15, 5, 'Dấu chấm (.)', 0),
(16, 5, 'Dấu phẩy (,)', 0),
(17, 5, 'Dấu hai chấm (:)', 0),
(18, 5, 'Dấu chấm phẩy (;)', 1),
(19, 6, 'Thiết kế bố cục trang web', 0),
(20, 6, 'Định dạng văn bản', 0),
(21, 6, 'Xây dựng cấu trúc trang web', 1),
(22, 6, 'Lập trình server', 0),
(23, 7, 'Ctrl + V', 0),
(24, 7, 'Ctrl + X', 0),
(25, 7, 'Ctrl + C', 1),
(26, 7, 'Ctrl + Z', 0),
(27, 8, '%', 0),
(28, 8, '&', 0),
(29, 8, '$', 1),
(30, 8, '#', 0),
(31, 9, '3', 0),
(32, 9, '5', 0),
(33, 9, '2', 1),
(34, 9, '7', 0),
(35, 10, 'Thiết kế giao diện', 1),
(36, 10, 'Quản lý dữ liệu', 0),
(37, 10, 'Xử lý backend', 0),
(38, 10, 'Lưu trữ file', 0);

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tong_tien` decimal(10,0) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `trang_thai` varchar(50) DEFAULT 'Thành công'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`id`, `user_id`, `tong_tien`, `ngay_tao`, `trang_thai`) VALUES
(1, 2, 399200, '2025-12-05 21:33:57', 'Thành công'),
(2, 2, 399200, '2025-12-05 21:42:44', 'Thành công'),
(3, 2, 399200, '2025-12-05 21:43:10', 'Thành công'),
(4, 2, 399200, '2025-12-05 21:57:29', 'Thành công'),
(5, 2, 399200, '2025-12-10 11:00:39', 'Thành công');

-- --------------------------------------------------------

--
-- Table structure for table `khoa_hoc`
--

CREATE TABLE `khoa_hoc` (
  `id` int(11) NOT NULL,
  `ten_khoa_hoc` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT 'default.jpg',
  `gia` double NOT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `danh_muc` varchar(100) DEFAULT NULL,
  `danh_gia_tb` float DEFAULT 0,
  `so_luong_hoc_vien` int(11) DEFAULT 0,
  `so_luong_danh_gia` int(11) NOT NULL,
  `giang_vien` varchar(255) NOT NULL,
  `giam_gia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khoa_hoc`
--

INSERT INTO `khoa_hoc` (`id`, `ten_khoa_hoc`, `mo_ta`, `hinh_anh`, `gia`, `ngay_tao`, `danh_muc`, `danh_gia_tb`, `so_luong_hoc_vien`, `so_luong_danh_gia`, `giang_vien`, `giam_gia`) VALUES
(101, 'Lap trinh C++ Co ban cho Nguoi moi bat dau', 'Nam vung nen tang C++ tu con so 0, xay dung tu duy lap trinh va tao ra cac ung dung dau tien cua ban.', 'https://tse3.mm.bing.net/th/id/OIP._aMP53CbOlw01GcJnIuCCwHaFS?pid=Api&P=0&h=220', 499000, '2025-10-01 00:00:00', 'Lap trinh', 4.8, 15890, 12345, 'Đặng Công Tùng', 20),
(102, 'Toán-12', 'Học toán đi nâng cao cái đầu', 'https://tse3.mm.bing.net/th/id/OIP.uSEQBFxFtXdsbPUMZX4ngAHaEK?pid=Api&P=0&h=220', 99999999.99, '2025-11-20 23:32:31', NULL, 0, 0, 0, 'Đặng Công Tùng', 20),
(103, 'Toán-12', 'Học toán đi nâng cao cái đầu', 'https://tse3.mm.bing.net/th/id/OIP.uSEQBFxFtXdsbPUMZX4ngAHaEK?pid=Api&P=0&h=220', 99999999.99, '2025-11-20 23:32:31', NULL, 0, 0, 0, 'Đặng Công Tùng', 20);

-- --------------------------------------------------------

--
-- Table structure for table `khoa_hoc_da_mua`
--

CREATE TABLE `khoa_hoc_da_mua` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `khoa_hoc_id` int(11) DEFAULT NULL,
  `ngay_mua` datetime DEFAULT current_timestamp(),
  `tien_do` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khoa_hoc_da_mua`
--

INSERT INTO `khoa_hoc_da_mua` (`id`, `user_id`, `khoa_hoc_id`, `ngay_mua`, `tien_do`) VALUES
(1, 2, 101, '2025-12-10 11:00:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `noi_dung_hoc_duoc`
--

CREATE TABLE `noi_dung_hoc_duoc` (
  `id` int(11) NOT NULL,
  `khoa_hoc_id` int(11) DEFAULT NULL,
  `noi_dung` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `noi_dung_hoc_duoc`
--

INSERT INTO `noi_dung_hoc_duoc` (`id`, `khoa_hoc_id`, `noi_dung`) VALUES
(13, 101, 'Nắm vững tư duy lập trình và giải quyết vấn đề.'),
(14, 101, 'Hiểu rõ các khái niệm cốt lõi của C++: biến, kiểu dữ liệu, vòng lặp, câu lệnh điều kiện.'),
(15, 101, 'Làm quen với Lập trình Hướng đối tượng (OOP) cơ bản.'),
(16, 101, 'Tạo nền tảng vững chắc để học các ngôn ngữ lập trình khác.'),
(17, 101, 'Xây dựng được các ứng dụng console đơn giản.'),
(18, 101, 'Biết cách gỡ lỗi (debug) một chương trình hiệu quả.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `role` enum('student','lecturer','admin') DEFAULT 'student',
  `avatar` varchar(255) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `gioi_thieu` text DEFAULT NULL,
  `reset_token` varchar(10) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ho_ten`, `email`, `mat_khau`, `role`, `avatar`, `ngay_tao`, `so_dien_thoai`, `gioi_thieu`, `reset_token`, `reset_expiry`) VALUES
(1, 'Quản Trị Viên', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, '2025-12-04 21:13:52', NULL, NULL, NULL, NULL),
(2, 'Đặng Công Tùng', 'tungdc.24it@vku.udn.vn', '$2y$10$oO17M7s8jkjDqhxr..Dkx.zvkrY9.KjOkiCul3MHwISY1.4xEeqly', 'student', '1764859098_IMG_20250130_202202.jpg', '2025-12-04 21:30:30', '0705955589', 'đẹp trai', '960260', '2025-12-10 05:18:28'),
(3, 'Đặng Công Tùng', 'dangcongtung2426@gmail.com', '$2y$10$eTFLVR3va8VMxIC5JGBP1eYAqrrQp..miFlv65cfaMMAwDUGpipt.', 'lecturer', NULL, '2025-12-10 11:55:01', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bai_giang`
--
ALTER TABLE `bai_giang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chuong_id` (`chuong_id`);

--
-- Indexes for table `bai_kiem_tra`
--
ALTER TABLE `bai_kiem_tra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cau_hoi`
--
ALTER TABLE `cau_hoi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chuong_hoc`
--
ALTER TABLE `chuong_hoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `khoa_hoc_id` (`khoa_hoc_id`);

--
-- Indexes for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dap_an`
--
ALTER TABLE `dap_an`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `khoa_hoc`
--
ALTER TABLE `khoa_hoc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `khoa_hoc_da_mua`
--
ALTER TABLE `khoa_hoc_da_mua`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `noi_dung_hoc_duoc`
--
ALTER TABLE `noi_dung_hoc_duoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `khoa_hoc_id` (`khoa_hoc_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bai_giang`
--
ALTER TABLE `bai_giang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `bai_kiem_tra`
--
ALTER TABLE `bai_kiem_tra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `binh_luan`
--
ALTER TABLE `binh_luan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cau_hoi`
--
ALTER TABLE `cau_hoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `chuong_hoc`
--
ALTER TABLE `chuong_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `dap_an`
--
ALTER TABLE `dap_an`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `khoa_hoc`
--
ALTER TABLE `khoa_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `khoa_hoc_da_mua`
--
ALTER TABLE `khoa_hoc_da_mua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `noi_dung_hoc_duoc`
--
ALTER TABLE `noi_dung_hoc_duoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bai_giang`
--
ALTER TABLE `bai_giang`
  ADD CONSTRAINT `bai_giang_ibfk_1` FOREIGN KEY (`chuong_id`) REFERENCES `chuong_hoc` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chuong_hoc`
--
ALTER TABLE `chuong_hoc`
  ADD CONSTRAINT `chuong_hoc_ibfk_1` FOREIGN KEY (`khoa_hoc_id`) REFERENCES `khoa_hoc` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `noi_dung_hoc_duoc`
--
ALTER TABLE `noi_dung_hoc_duoc`
  ADD CONSTRAINT `noi_dung_hoc_duoc_ibfk_1` FOREIGN KEY (`khoa_hoc_id`) REFERENCES `khoa_hoc` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
