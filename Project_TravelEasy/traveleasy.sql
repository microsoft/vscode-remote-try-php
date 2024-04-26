-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 26, 2024 lúc 04:00 AM
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
-- Cơ sở dữ liệu: `traveleasy`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblagency`
--

CREATE TABLE `tblagency` (
  `idAgency` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `idTourGuide` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblcustomer`
--

CREATE TABLE `tblcustomer` (
  `idCustomer` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblhotel`
--

CREATE TABLE `tblhotel` (
  `idHotel` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbllocation`
--

CREATE TABLE `tbllocation` (
  `idLocation` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `idHotel` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblregister`
--

CREATE TABLE `tblregister` (
  `idRegister` varchar(15) NOT NULL,
  `idCustomer` varchar(15) DEFAULT NULL,
  `idTour` varchar(15) DEFAULT NULL,
  `quantityTicket` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblticket`
--

CREATE TABLE `tblticket` (
  `idTicket` varchar(15) NOT NULL,
  `idTour` varchar(15) DEFAULT NULL,
  `idCustomer` varchar(15) DEFAULT NULL,
  `idAgency` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbltour`
--

CREATE TABLE `tbltour` (
  `idTour` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `startDay` date DEFAULT NULL,
  `endDay` date DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `idLocation` varchar(15) DEFAULT NULL,
  `idHotel` varchar(15) DEFAULT NULL,
  `idVehicle` varchar(15) DEFAULT NULL,
  `idTourGuide` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbltourguide`
--

CREATE TABLE `tbltourguide` (
  `idTourGuide` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblvehicle`
--

CREATE TABLE `tblvehicle` (
  `idVehicle` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `licensePlate` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tblagency`
--
ALTER TABLE `tblagency`
  ADD PRIMARY KEY (`idAgency`),
  ADD KEY `idTourGuide` (`idTourGuide`);

--
-- Chỉ mục cho bảng `tblcustomer`
--
ALTER TABLE `tblcustomer`
  ADD PRIMARY KEY (`idCustomer`);

--
-- Chỉ mục cho bảng `tblhotel`
--
ALTER TABLE `tblhotel`
  ADD PRIMARY KEY (`idHotel`);

--
-- Chỉ mục cho bảng `tbllocation`
--
ALTER TABLE `tbllocation`
  ADD PRIMARY KEY (`idLocation`),
  ADD KEY `idHotel` (`idHotel`);

--
-- Chỉ mục cho bảng `tblregister`
--
ALTER TABLE `tblregister`
  ADD PRIMARY KEY (`idRegister`),
  ADD KEY `idCustomer` (`idCustomer`),
  ADD KEY `idTour` (`idTour`);

--
-- Chỉ mục cho bảng `tblticket`
--
ALTER TABLE `tblticket`
  ADD PRIMARY KEY (`idTicket`),
  ADD KEY `idTour` (`idTour`),
  ADD KEY `idCustomer` (`idCustomer`),
  ADD KEY `idAgency` (`idAgency`);

--
-- Chỉ mục cho bảng `tbltour`
--
ALTER TABLE `tbltour`
  ADD PRIMARY KEY (`idTour`),
  ADD KEY `idLocation` (`idLocation`),
  ADD KEY `idHotel` (`idHotel`),
  ADD KEY `idVehicle` (`idVehicle`),
  ADD KEY `idTourGuide` (`idTourGuide`);

--
-- Chỉ mục cho bảng `tbltourguide`
--
ALTER TABLE `tbltourguide`
  ADD PRIMARY KEY (`idTourGuide`);

--
-- Chỉ mục cho bảng `tblvehicle`
--
ALTER TABLE `tblvehicle`
  ADD PRIMARY KEY (`idVehicle`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tblagency`
--
ALTER TABLE `tblagency`
  ADD CONSTRAINT `tblagency_ibfk_1` FOREIGN KEY (`idTourGuide`) REFERENCES `tbltourguide` (`idTourGuide`);

--
-- Các ràng buộc cho bảng `tbllocation`
--
ALTER TABLE `tbllocation`
  ADD CONSTRAINT `tbllocation_ibfk_1` FOREIGN KEY (`idHotel`) REFERENCES `tblhotel` (`idHotel`);

--
-- Các ràng buộc cho bảng `tblregister`
--
ALTER TABLE `tblregister`
  ADD CONSTRAINT `tblregister_ibfk_1` FOREIGN KEY (`idCustomer`) REFERENCES `tblcustomer` (`idCustomer`),
  ADD CONSTRAINT `tblregister_ibfk_2` FOREIGN KEY (`idTour`) REFERENCES `tbltour` (`idTour`);

--
-- Các ràng buộc cho bảng `tblticket`
--
ALTER TABLE `tblticket`
  ADD CONSTRAINT `tblticket_ibfk_1` FOREIGN KEY (`idTour`) REFERENCES `tbltour` (`idTour`),
  ADD CONSTRAINT `tblticket_ibfk_2` FOREIGN KEY (`idCustomer`) REFERENCES `tblcustomer` (`idCustomer`),
  ADD CONSTRAINT `tblticket_ibfk_3` FOREIGN KEY (`idAgency`) REFERENCES `tblagency` (`idAgency`);

--
-- Các ràng buộc cho bảng `tbltour`
--
ALTER TABLE `tbltour`
  ADD CONSTRAINT `tbltour_ibfk_1` FOREIGN KEY (`idLocation`) REFERENCES `tbllocation` (`idLocation`),
  ADD CONSTRAINT `tbltour_ibfk_2` FOREIGN KEY (`idHotel`) REFERENCES `tblhotel` (`idHotel`),
  ADD CONSTRAINT `tbltour_ibfk_3` FOREIGN KEY (`idVehicle`) REFERENCES `tblvehicle` (`idVehicle`),
  ADD CONSTRAINT `tbltour_ibfk_4` FOREIGN KEY (`idTourGuide`) REFERENCES `tbltourguide` (`idTourGuide`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
