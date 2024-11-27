-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2024 at 04:15 PM
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
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `car_name` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `fuel` varchar(100) NOT NULL,
  `seats` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `from_date` varchar(100) NOT NULL,
  `until_date` varchar(100) NOT NULL,
  `pickup_time` varchar(100) NOT NULL,
  `drop_time` varchar(100) NOT NULL,
  `message` varchar(200) NOT NULL,
  `screenshot` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`book_id`, `name`, `address`, `email`, `contact`, `car_name`, `model`, `fuel`, `seats`, `price`, `from_date`, `until_date`, `pickup_time`, `drop_time`, `message`, `screenshot`, `status`) VALUES
(11, 'joven tampus', 'odiongan', 'joven@gmail.com', '0932434234', 'Vios', '2022', 'Regular', '4-5', 1500, '2024-09-04', '2024-09-05', '9:44 AM', '9:44 AM', 'none', 'att.00wb4TyREO9dscWX3zWaELRndfuap-ue-S9BpTPmwA0.jpg', 0);

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
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `age` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `valid` varchar(100) NOT NULL,
  `profile` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`login_id`, `lastname`, `firstname`, `age`, `email`, `password`, `address`, `contact`, `valid`, `profile`) VALUES
(1, 'tampus', 'joven', '50', 'joven@gmail.com', '$2y$10$WURRHdUfDka8Kq9HG98EeOSJ2Vkt/5A0ES6ZN/GkVvF4qhUJQoLmS', 'odiongan', '0932434234', 'gcash.png', 'QR.jpg'),
(2, 'tampus', 'joven', '30', 'joven@gmail.com', '$2y$10$ipD/u9KBnqpVxvnG5TJvhO4h0U7OD9RiiAMs6EtP8GVzl/KmxMSlm', 'odiongan', '0932434234', 'gcash.png', 'QR.jpg');

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
  `status` int(100) NOT NULL,
  `papular` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `car_name`, `car_brand`, `overview`, `price`, `seat`, `fuel`, `model`, `image`, `accessories`, `status`, `papular`) VALUES
(9, 'Vios', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Regular', '2022', 'toyota vios auto.jpg', 'aircon, power-steering, navigation-system, leather-seats, backup-camera, parking-sensors', 0, 0),
(10, 'Urvan NV350', 'Nissan', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '3000', '15', 'Regular', '2020', 'hiace decontent.jpg', 'aircon, power-steering, navigation-system, leather-seats, backup-camera, parking-sensors', 0, 0),
(11, 'Vios XLE', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Regular', '2022', 'toyota vios.jpg', 'aircon, power-steering, navigation-system, leather-seats, backup-camera, parking-sensors, heated-sea', 0, 0),
(12, 'Mirage Hatchback', 'Mitsubishi', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Regular', '2022', 'mitsubishi mirage g4.png', 'aircon, power-steering, leather-seats, backup-camera, parking-sensors', 0, 0),
(13, 'Mirage G4 Manual', 'Mitsubishi', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Regular', '2020', 'mitsubishi mirage g4 automatic.jpg', 'aircon, power-steering, navigation-system, leather-seats, backup-camera, parking-sensors', 0, 0),
(14, 'Mirage G4 Automatic', 'Mitsubishi', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Special', '2022', 'toyota  rush g-sports.jpg', 'aircon, bluetooth, leather-seats, backup-camera, parking-sensors', 0, 0),
(15, 'Ertiga GLX', 'Suzuki', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2300', '7', 'Special', '2020', 'ertiga 2022.jfif', 'aircon, navigation-system, backup-camera, parking-sensors, heated-seats', 0, 0),
(16, 'Expander', 'Mitsubishi', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2300', '7-8', 'Special', '2021', 'ertiga 2023.jpg', 'aircon, power-steering, backup-camera, parking-sensors', 0, 0),
(17, 'Urvan NV350 Manual', 'Nissan', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '3000', '18', 'Special', '2020', 'hiace decontent.jpg', 'aircon, navigation-system, bluetooth, leather-seats, backup-camera, parking-sensors', 0, 0),
(18, 'Hilux', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2500', '4-5', 'Regular', '2020', 'nissan navara.png', 'aircon, navigation-system, leather-seats, backup-camera, parking-sensors', 0, 0),
(19, 'Hiace Commuter', 'Toyota', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '2500', '15', 'Regular', '2017', 'toyota hiace.png', 'aircon, navigation-system, bluetooth, leather-seats, backup-camera, parking-sensors', 0, 0),
(20, 'Ecosport Trend', 'Ford', 'This vehicle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '1500', '4-5', 'Regular', '2018', 'ford esosport trend.png', 'aircon, leather-seats, backup-camera, parking-sensors', 0, 0),
(21, 'Nmax', 'Yamaha', 'This motorcycle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '800', '2', 'Regular', '2022', 'Yamaha-Nmax-2.png', 'bluetooth, parking-sensors', 0, 0),
(22, 'Aerox', 'Yamaha', 'This motorcycle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '800', '2', 'Regular', '2021', 'aerox.jpeg', 'parking-sensors', 0, 0),
(23, 'Raider Fi', 'Suzuki', 'This motorcycle comes with complete and up-to-date documentation, including registration, insurance, and any necessary permits, ensuring a hassle-free rental experience.', '800', '2', 'Regular', '2022', 'RAIDER-R150-FI-Matte-Blue.jpg', 'heated-seats', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`book_id`);

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
  MODIFY `book_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
