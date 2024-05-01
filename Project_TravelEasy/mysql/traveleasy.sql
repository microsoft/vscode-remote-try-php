-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 01, 2024 lúc 05:37 PM
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
-- Cấu trúc bảng cho bảng `tbladdress`
--

CREATE TABLE `tbladdress` (
  `idAddress` varchar(15) NOT NULL,
  `idCity` varchar(15) DEFAULT NULL,
  `idDistrict` varchar(15) DEFAULT NULL,
  `idWard` varchar(15) DEFAULT NULL,
  `idDetailAddress` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblagency`
--

CREATE TABLE `tblagency` (
  `idAgency` varchar(15) NOT NULL,
  `idAddress` varchar(15) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `idTourGuide` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblcity`
--

CREATE TABLE `tblcity` (
  `idCity` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblcustomer`
--

CREATE TABLE `tblcustomer` (
  `idCustomer` varchar(15) NOT NULL,
  `idAddress` varchar(15) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbldetailaddress`
--

CREATE TABLE `tbldetailaddress` (
  `idDetailAddress` varchar(15) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbldistrict`
--

CREATE TABLE `tbldistrict` (
  `idDistrict` varchar(15) NOT NULL,
  `idCity` varchar(15) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblhotel`
--

CREATE TABLE `tblhotel` (
  `idHotel` varchar(15) NOT NULL,
  `idAddress` varchar(15) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL
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
  `idAddress` varchar(15) DEFAULT NULL,
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
  `idAddress` varchar(15) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblward`
--

CREATE TABLE `tblward` (
  `idWard` varchar(15) NOT NULL,
  `idDistrict` varchar(15) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbladdress`
--
ALTER TABLE `tbladdress`
  ADD PRIMARY KEY (`idAddress`),
  ADD KEY `fk_Address_City` (`idCity`),
  ADD KEY `fk_Address_District` (`idDistrict`),
  ADD KEY `fk_Address_Ward` (`idWard`),
  ADD KEY `fk_Address_DetailAddress` (`idDetailAddress`);

--
-- Chỉ mục cho bảng `tblagency`
--
ALTER TABLE `tblagency`
  ADD PRIMARY KEY (`idAgency`),
  ADD KEY `fk_Agency_Address` (`idAddress`),
  ADD KEY `fk_Agency_TourGuide` (`idTourGuide`);

--
-- Chỉ mục cho bảng `tblcity`
--
ALTER TABLE `tblcity`
  ADD PRIMARY KEY (`idCity`);

--
-- Chỉ mục cho bảng `tblcustomer`
--
ALTER TABLE `tblcustomer`
  ADD PRIMARY KEY (`idCustomer`),
  ADD KEY `fk_Customer_Address` (`idAddress`);

--
-- Chỉ mục cho bảng `tbldetailaddress`
--
ALTER TABLE `tbldetailaddress`
  ADD PRIMARY KEY (`idDetailAddress`);

--
-- Chỉ mục cho bảng `tbldistrict`
--
ALTER TABLE `tbldistrict`
  ADD PRIMARY KEY (`idDistrict`);

--
-- Chỉ mục cho bảng `tblhotel`
--
ALTER TABLE `tblhotel`
  ADD PRIMARY KEY (`idHotel`),
  ADD KEY `fk_Hotel_Address` (`idAddress`);

--
-- Chỉ mục cho bảng `tblregister`
--
ALTER TABLE `tblregister`
  ADD PRIMARY KEY (`idRegister`),
  ADD KEY `fk_Register_Customer` (`idCustomer`),
  ADD KEY `fk_Register_Tour` (`idTour`);

--
-- Chỉ mục cho bảng `tblticket`
--
ALTER TABLE `tblticket`
  ADD PRIMARY KEY (`idTicket`),
  ADD KEY `fk_Ticket_Tour` (`idTour`),
  ADD KEY `fk_Ticket_Customer` (`idCustomer`),
  ADD KEY `fk_Ticket_Agency` (`idAgency`);

--
-- Chỉ mục cho bảng `tbltour`
--
ALTER TABLE `tbltour`
  ADD PRIMARY KEY (`idTour`),
  ADD KEY `fk_Tour_Address` (`idAddress`),
  ADD KEY `fk_Tour_Hotel` (`idHotel`),
  ADD KEY `fk_Tour_Vehicle` (`idVehicle`),
  ADD KEY `fk_Tour_TourGuide` (`idTourGuide`);

--
-- Chỉ mục cho bảng `tbltourguide`
--
ALTER TABLE `tbltourguide`
  ADD PRIMARY KEY (`idTourGuide`),
  ADD KEY `fk_TourGuide_Address` (`idAddress`);

--
-- Chỉ mục cho bảng `tblvehicle`
--
ALTER TABLE `tblvehicle`
  ADD PRIMARY KEY (`idVehicle`);

--
-- Chỉ mục cho bảng `tblward`
--
ALTER TABLE `tblward`
  ADD PRIMARY KEY (`idWard`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tbladdress`
--
ALTER TABLE `tbladdress`
  ADD CONSTRAINT `fk_Address_City` FOREIGN KEY (`idCity`) REFERENCES `tblcity` (`idCity`),
  ADD CONSTRAINT `fk_Address_DetailAddress` FOREIGN KEY (`idDetailAddress`) REFERENCES `tbldetailaddress` (`idDetailAddress`),
  ADD CONSTRAINT `fk_Address_District` FOREIGN KEY (`idDistrict`) REFERENCES `tbldistrict` (`idDistrict`),
  ADD CONSTRAINT `fk_Address_Ward` FOREIGN KEY (`idWard`) REFERENCES `tblward` (`idWard`);

--
-- Các ràng buộc cho bảng `tblagency`
--
ALTER TABLE `tblagency`
  ADD CONSTRAINT `fk_Agency_Address` FOREIGN KEY (`idAddress`) REFERENCES `tbladdress` (`idAddress`),
  ADD CONSTRAINT `fk_Agency_TourGuide` FOREIGN KEY (`idTourGuide`) REFERENCES `tbltourguide` (`idTourGuide`);

--
-- Các ràng buộc cho bảng `tblcustomer`
--
ALTER TABLE `tblcustomer`
  ADD CONSTRAINT `fk_Customer_Address` FOREIGN KEY (`idAddress`) REFERENCES `tbladdress` (`idAddress`);

--
-- Các ràng buộc cho bảng `tblhotel`
--
ALTER TABLE `tblhotel`
  ADD CONSTRAINT `fk_Hotel_Address` FOREIGN KEY (`idAddress`) REFERENCES `tbladdress` (`idAddress`);

--
-- Các ràng buộc cho bảng `tblregister`
--
ALTER TABLE `tblregister`
  ADD CONSTRAINT `fk_Register_Customer` FOREIGN KEY (`idCustomer`) REFERENCES `tblcustomer` (`idCustomer`),
  ADD CONSTRAINT `fk_Register_Tour` FOREIGN KEY (`idTour`) REFERENCES `tbltour` (`idTour`);

--
-- Các ràng buộc cho bảng `tblticket`
--
ALTER TABLE `tblticket`
  ADD CONSTRAINT `fk_Ticket_Agency` FOREIGN KEY (`idAgency`) REFERENCES `tblagency` (`idAgency`),
  ADD CONSTRAINT `fk_Ticket_Customer` FOREIGN KEY (`idCustomer`) REFERENCES `tblcustomer` (`idCustomer`),
  ADD CONSTRAINT `fk_Ticket_Tour` FOREIGN KEY (`idTour`) REFERENCES `tbltour` (`idTour`);

--
-- Các ràng buộc cho bảng `tbltour`
--
ALTER TABLE `tbltour`
  ADD CONSTRAINT `fk_Tour_Address` FOREIGN KEY (`idAddress`) REFERENCES `tbladdress` (`idAddress`),
  ADD CONSTRAINT `fk_Tour_Hotel` FOREIGN KEY (`idHotel`) REFERENCES `tblhotel` (`idHotel`),
  ADD CONSTRAINT `fk_Tour_TourGuide` FOREIGN KEY (`idTourGuide`) REFERENCES `tbltourguide` (`idTourGuide`),
  ADD CONSTRAINT `fk_Tour_Vehicle` FOREIGN KEY (`idVehicle`) REFERENCES `tblvehicle` (`idVehicle`);

--
-- Các ràng buộc cho bảng `tbltourguide`
--
ALTER TABLE `tbltourguide`
  ADD CONSTRAINT `fk_TourGuide_Address` FOREIGN KEY (`idAddress`) REFERENCES `tbladdress` (`idAddress`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
