-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2024 at 08:35 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chadoyven_car_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `book_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `selfie` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `car_name` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `price` double NOT NULL,
  `from_date` varchar(100) NOT NULL,
  `created_at` varchar(11) NOT NULL,
  `until_date` varchar(100) NOT NULL,
  `pickup_time` varchar(100) NOT NULL,
  `drop_time` varchar(100) NOT NULL,
  `message` varchar(200) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `cancel` int(11) NOT NULL,
  `viewed` int(11) NOT NULL,
  `reference_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`book_id`, `name`, `address`, `city`, `email`, `contact`, `valid_id`, `selfie`, `image`, `car_name`, `model`, `price`, `from_date`, `created_at`, `until_date`, `pickup_time`, `drop_time`, `message`, `payment_method`, `status`, `cancel`, `viewed`, `reference_ID`) VALUES
(17, 'nico', 'odiongan', 'gingoog City', '2110071@gcci.edu.ph', '0932434234', 'valid_id_67062217b96165.53708524.jpg', '', 'hiace decontent.jpg', 'Urvan NV350 Manual', '2020', 0, '10/09/2024', '', '10/12/2024', '10:25 AM', '10:24 AM', '', 'gcash', 1, 1, 1, '5S6IZ4'),
(18, 'joven', 'odiongan', 'gingoog City', '2110071@gcci.edu.ph', '0932434234', 'valid_id_6706223b148296.25344309.jpg', '', 'hiace decontent.jpg', 'Urvan NV350 Manual', '2020', 0, '10/09/2024', '', '10/12/2024', '10:25 AM', '10:24 AM', '', 'gcash', 1, 0, 1, 'SRFX14'),
(19, 'joven', 'odiongan', 'gingoog City', '2110071@gcci.edu.ph', '0932434234', 'valid_id_670622e006e601.03987540.jpg', '', 'hiace decontent.jpg', 'Urvan NV350 Manual', '2020', 0, '10/09/2024', '', '10/12/2024', '10:25 AM', '10:24 AM', '', 'gcash', 1, 0, 1, 'YUKQ91'),
(20, 'joven', 'odiongan', 'gingoog City', '2110071@gcci.edu.ph', '0932434234', 'valid_id_6706238955a678.55361540.png', '', 'hiace decontent.jpg', 'Urvan NV350 Manual', '2020', 0, '10/09/2024', '', '10/12/2024', '10:25 AM', '10:24 AM', '', 'gcash', 1, 1, 1, '0ZVFKT'),
(21, 'joven', 'odiongan', 'gingoog City', '2110071@gcci.edu.ph', '0932434234', 'valid_id_67062464897931.07349154.jpg', '', 'hiace decontent.jpg', 'Urvan NV350 Manual', '2020', 3020, '10/23/2024', '', '10/26/2024', '10:25 AM', '10:24 AM', '', 'gcash', 1, 0, 1, 'TD7I34'),
(25, 'joven nazareno', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09353540437', 'valid_id_670ded5b9dbda7.94219170.jpg', 'selfie_670ded5b9e1514.59069042.png', 'toyota vios.jpg', 'Vios XLE', '2020', 3100, '2024-10-15', '', '2024-10-17', '08:18 AM', '08:18 AM', '', 'gcash', 0, 0, 1, 'FVKU7W'),
(26, 'nico', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09353540437', 'valid_id_670df3c07740f8.70588540.png', 'selfie_670df3c077a629.87352435.png', 'toyota vios auto.jpg', 'Vios', '2022', 1620, '2024-10-15', '', '2024-10-16', '08:38 AM', '08:39 AM', '', 'gcash', 1, 0, 1, '8SR5CL'),
(27, 'joven nazareno', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09353540437', 'valid_id_6711dada1a5510.48778186.jpg', 'selfie_6711dada1ab103.80517080.png', 'toyota vios.jpg', 'Vios XLE', '2020', 120, '2024-10-18', '', '2024-10-22', '05:22 AM', '05:22 AM', '', 'gcash', 0, 0, 1, 'JAT2YB'),
(28, 'hays', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '0932434234', 'valid_id_6711dee2b2cd91.18102349.jpg', 'selfie_6711dee2b33f44.68900572.png', 'hiace decontent.jpg', 'Urvan NV350 Manual', '2020', 12120, '2024-10-18', '', '2024-10-22', '05:22 AM', '05:22 AM', '', 'gcash', 0, 0, 1, 'P4ZSM3'),
(29, 'nico', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09550541077', 'valid_id_6711e1d0424823.76083746.png', 'selfie_6711e1d042c234.80996853.png', 'toyota vios.jpg', 'Vios XLE', '2020', 120, '2024-10-21', '', '2024-10-24', '08:18 AM', '08:18 AM', '', 'gcash', 0, 0, 1, 'K71A2R'),
(30, 'stress', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09353540437', 'valid_id_6711e248c76837.87910396.jpg', 'selfie_6711e248c7e1e3.94242581.png', 'toyota vios auto.jpg', 'Vios', '2022', 4620, '2024-10-21', '', '2024-10-24', '08:18 AM', '08:18 AM', '', 'gcash', 0, 0, 1, 'EAXCTZ'),
(31, 'stress', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09353540437', 'valid_id_6711e39374bd36.68596495.png', 'selfie_6711e393751218.65368515.png', 'toyota vios auto.jpg', 'Vios', '2022', 4620, '2024-10-21', '', '2024-10-24', '08:18 AM', '08:18 AM', '', 'gcash', 0, 0, 1, 'E4MJW5'),
(32, 'stress', 'odiongan', 'gingoog City', 'robertgumapo7@gmail.com', '09353540437', 'valid_id_6711e4e01591d6.22892576.png', 'selfie_6711e4e0164589.64085325.png', 'toyota vios auto.jpg', 'Vios', '2022', 4620, '2024-10-21', '', '2024-10-24', '08:18 AM', '08:18 AM', '', 'gcash', 1, 0, 1, 'ABHPNS');

-- --------------------------------------------------------

--
-- Table structure for table `book_cancel`
--

CREATE TABLE `book_cancel` (
  `ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `car_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_cancel`
--

INSERT INTO `book_cancel` (`ID`, `name`, `email`, `car_name`) VALUES
(1, 'robert Gumapo', 'robertgumapo7@gmail.com', 'Vios XLE'),
(2, 'mark', 'robertgumapo7@gmail.com', 'Mirage Hatchback'),
(3, 'reyes', 'robertgumapo7@gmail.com', 'Ertiga GLX');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `created_date` date NOT NULL,
  `status` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand`, `created_date`, `status`) VALUES
(7, 'Toyota', '2024-09-01', 0),
(8, 'Nissan', '2024-09-01', 0),
(9, 'Mitsubishi', '2024-09-01', 0),
(10, 'Suzuki', '2024-09-01', 0),
(11, 'Ford', '2024-09-01', 0),
(12, 'Yamaha', '2024-09-01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`login_id`, `name`, `email`, `password`) VALUES
(4, 'Chadoyven', 'chadoyven@gmail.com', 'companyadmin');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(100) NOT NULL,
  `book_id` int(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `book_id`, `image`, `date`) VALUES
(1, 32, '25903a4e24685e6e43007f5d5d1a078c.jpg', '2024-10-18 22:46:22');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `ID` int(100) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `review` text NOT NULL,
  `date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`ID`, `Name`, `Email`, `review`, `date`) VALUES
(1, 'joven nazareno', 'joven@gmail.com', 'sdasdasd', '2024-10-14'),
(2, 'joven nazareno', 'joven@gmail.com', 'sdasdasd', '2024-10-14'),
(3, 'joven nazareno', 'joven@gmail.com', 'sdasdasd', '2024-10-14'),
(4, 'joven nazareno', 'joven@gmail.com', 'sdasdasd', '2024-10-14'),
(5, 'joven nazareno', 'robertgumapo7@gmail.com', 'fgdsfgdfgdfg', '2024-10-15 12:20:11'),
(6, 'nico', 'robertgumapo7@gmail.com', 'hi', '2024-10-15'),
(7, 'nico', 'robertgumapo7@gmail.com', 'hello', '2024-10-15'),
(8, 'clark', 'robertgumapo7@gmail.com', 'hello world!', '2024-10-15 13:19:53');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(100) NOT NULL,
  `car_name` varchar(100) NOT NULL,
  `car_brand` varchar(100) NOT NULL,
  `overview` varchar(200) NOT NULL,
  `price` varchar(100) NOT NULL,
  `seat` varchar(100) NOT NULL,
  `fuel` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `accessories` varchar(100) NOT NULL,
  `available` varchar(100) NOT NULL,
  `end_date` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `car_name`, `car_brand`, `overview`, `price`, `seat`, `fuel`, `model`, `image`, `accessories`, `available`, `end_date`, `status`) VALUES
(24, 'Vios', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Special', '2022', 'toyota vios auto.jpg', 'cd-player, bluetooth, leather-seats, ', '2024-10-21', '2024-10-24', 0),
(25, 'Urvan NV350 Manual', 'Nissan', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '3000', '15', 'Special', '2020', 'hiace decontent.jpg', 'cd-player, leather-seats, backup-camera, parking-sensors, ', '10/09/2024', '10/12/2024', 0),
(26, 'Vios XLE', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Unleaded', '2020', 'toyota vios.jpg', 'navigation-system, bluetooth, backup-camera, ', '', '', 1),
(27, 'Mirage Hatchback', 'Mitsubishi', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Special', '2022', 'mitsubishi mirage g4.png', 'aircon, power-steering, backup-camera, ', '', '', 1),
(28, 'Mirage G4 Automatic', 'Mitsubishi', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.\r\n', '1500', '4-5', 'Unleaded', '2022', 'mitsubishi mirage g4 automatic.jpg', 'aircon, backup-camera, parking-sensors, ', '', '', 1),
(29, 'Ertiga GLX', 'Suzuki', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2300', '7', 'Special', '2020', 'ertiga 2022.jfif', 'aircon, sunroof, backup-camera, parking-sensors, ', '', '', 1),
(30, 'Ertiga GLX Manual', 'Suzuki', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2300', '7', 'Unleaded', '2019', 'ertiga 2023.jpg', 'aircon, leather-seats, backup-camera, ', '', '', 1),
(31, 'Urvan NV350 Automatic', 'Nissan', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '3000', '18', 'Unleaded', '2020', 'hiace decontent.jpg', 'navigation-system, leather-seats, backup-camera, parking-sensors, ', '', '', 1),
(32, 'Hilux', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2500', '4-5', 'Special', '2020', 'nissan navara.png', 'sunroof, leather-seats, backup-camera, parking-sensors, ', '', '', 1),
(33, 'Ecosport Trend', 'Ford', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Unleaded', '2018', 'ford esosport trend.png', 'aircon, sunroof, leather-seats, backup-camera, parking-sensors, ', '', '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `book_cancel`
--
ALTER TABLE `book_cancel`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `book_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `book_cancel`
--
ALTER TABLE `book_cancel`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
