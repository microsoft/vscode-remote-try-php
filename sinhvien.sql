-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 23, 2024 lúc 12:02 PM
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
-- Cơ sở dữ liệu: `sinhvien`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbldangky`
--

CREATE TABLE `tbldangky` (
  `MSSV` varchar(15) NOT NULL,
  `MaMH` varchar(15) NOT NULL,
  `Ky` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbldangky`
--

INSERT INTO `tbldangky` (`MSSV`, `MaMH`, `Ky`) VALUES
('21011620', 'MH1', 3),
('21011620', 'MH2', 3),
('21012057', 'MH1', 3),
('21012057', 'MH3', 3),
('21012078', 'MH3', 3),
('21012078', 'MH4', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblmonhoc`
--

CREATE TABLE `tblmonhoc` (
  `MaMH` varchar(15) NOT NULL,
  `TenMH` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblmonhoc`
--

INSERT INTO `tblmonhoc` (`MaMH`, `TenMH`) VALUES
('MH1', 'Thiết kế web'),
('MH2', 'Đồ án liên ngành'),
('MH3', 'Thị giác máy tính'),
('MH4', 'Đánh giá và kiểm định chất lượng phần mềm');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblsinhvien`
--

CREATE TABLE `tblsinhvien` (
  `MSSV` varchar(15) NOT NULL,
  `HoTen` varchar(50) NOT NULL,
  `Lop` varchar(15) NOT NULL,
  `Khoa` varchar(50) NOT NULL,
  `SDT` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblsinhvien`
--

INSERT INTO `tblsinhvien` (`MSSV`, `HoTen`, `Lop`, `Khoa`, `SDT`) VALUES
('21011620', 'Vũ Minh Phong', 'K15-CNTT3', 'CNTT', '0331313154'),
('21012057', 'Trần Duy Bim', 'K15-CNTT3', 'CNTT', '9874563210'),
('21012078', 'Nguyễn Đức Anh', 'K15-CNTT4', 'CNTT', '1234569871');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbldangky`
--
ALTER TABLE `tbldangky`
  ADD PRIMARY KEY (`MSSV`,`MaMH`),
  ADD KEY `MaMH` (`MaMH`);

--
-- Chỉ mục cho bảng `tblmonhoc`
--
ALTER TABLE `tblmonhoc`
  ADD PRIMARY KEY (`MaMH`);

--
-- Chỉ mục cho bảng `tblsinhvien`
--
ALTER TABLE `tblsinhvien`
  ADD PRIMARY KEY (`MSSV`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tbldangky`
--
ALTER TABLE `tbldangky`
  ADD CONSTRAINT `tbldangky_ibfk_1` FOREIGN KEY (`MSSV`) REFERENCES `tblsinhvien` (`MSSV`),
  ADD CONSTRAINT `tbldangky_ibfk_2` FOREIGN KEY (`MaMH`) REFERENCES `tblmonhoc` (`MaMH`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
