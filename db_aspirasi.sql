-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 10:32 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_aspirasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`) VALUES
('admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int(5) NOT NULL,
  `id_pelaporan` int(5) NOT NULL,
  `status` enum('Menunggu','Proses','Selesai') NOT NULL,
  `id_kategori` int(5) NOT NULL,
  `feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `input_aspirasi`
--

CREATE TABLE `input_aspirasi` (
  `id_pelaporan` int(5) NOT NULL,
  `nis` int(10) NOT NULL,
  `id_kategori` int(5) NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `ket` text NOT NULL,
  `tgl_input` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `input_aspirasi`
--

INSERT INTO `input_aspirasi` (`id_pelaporan`, `nis`, `id_kategori`, `lokasi`, `ket`, `tgl_input`) VALUES
(25, 84953847, 12, 'Lab 1 dan 2', 'Komputernya tambahin RTX', '28-03-2026 15:49:53'),
(26, 84953847, 12, 'Lab 1 dan 2', 'Komputernya tambahin RTX', '28-03-2026 15:50:49'),
(27, 84953847, 12, 'Lab 1 dan 2', 'Komputernya tambahin RTX', '28-03-2026 16:28:05');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(5) NOT NULL,
  `ket_kategori` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `ket_kategori`) VALUES
(7, 'JARINGAN INTERNET'),
(9, 'TOILET'),
(10, 'FASILITAS OLAHRAGA'),
(12, 'FASILITAS KOMPUTER'),
(13, 'KEBERSIHAN'),
(14, 'LABORATORIUM'),
(15, 'KOMPUTER');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` int(10) NOT NULL,
  `Kelas` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `Kelas`) VALUES
(0, 'XII RPL 2'),
(0, 'XI RPL 1'),
(432352524, 'XI RPL 1'),
(432352524, 'XI RPL 1'),
(432352524, 'XI RPL 2'),
(784574837, 'X RPL 2'),
(84953847, 'XII RPL 2'),
(84953847, 'X RPL 1'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(84953847, 'X RPL 2'),
(12364738, 'XI RPL 3'),
(84953847, 'XII RPL 2'),
(45685655, 'XII RPL 2'),
(84953847, 'XI RPL 3'),
(84953847, 'XI RPL 3'),
(84953847, 'XI RPL 3');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_pelaporan` (`id_pelaporan`);

--
-- Indexes for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD PRIMARY KEY (`id_pelaporan`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  MODIFY `id_pelaporan` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD CONSTRAINT `aspirasi_ibfk_1` FOREIGN KEY (`id_pelaporan`) REFERENCES `aspirasi` (`id_aspirasi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
