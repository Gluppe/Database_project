-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2021 at 09:20 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_token`
--

CREATE TABLE `auth_token` (
  `token` varchar(100) NOT NULL,
  `endpoint` enum('customer','employee','public','shipper') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `auth_token`
--

INSERT INTO `auth_token` (`token`, `endpoint`) VALUES
('0ee1345a-2a0c-497b-8c6e-4b25507fb931', 'shipper'),
('4e114242-a7ee-42d0-9c4e-fe25e89a5321', 'employee'),
('5b2c303a-43a9-4abf-a51a-b2d109527c7d', 'customer'),
('8940207e-eca3-479f-8231-6c7ddf038a78', 'public');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `ID` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`ID`, `name`, `start_date`, `end_date`) VALUES
(10, 'Stian', '2021-03-21', NULL),
(11, 'Stian', '2021-03-21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `ID` int(11) NOT NULL,
  `department` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `manufacturer_ID` int(11) NOT NULL,
  `roleFlag` enum('Customer Representative','Shopkeeper','Production Planner') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `franchise`
--

CREATE TABLE `franchise` (
  `customer_id` int(11) NOT NULL,
  `negotiated_buying_price` int(11) NOT NULL,
  `partner_stores` text NOT NULL,
  `shipping_address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `individual_stores`
--

CREATE TABLE `individual_stores` (
  `customer_id` int(11) NOT NULL,
  `shipping_address` text NOT NULL,
  `negotiated_buying_price` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_number` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `state` varchar(50) NOT NULL,
  `reference_to_larger_order` int(11) DEFAULT NULL,
  `shipment_number` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_number`, `total_price`, `state`, `reference_to_larger_order`, `shipment_number`, `customer_id`, `date`) VALUES
(1, 1000, 'new', NULL, 100, 10, '2021-04-07'),
(428, 500, 'new', NULL, 100, 10, '2021-04-01');

-- --------------------------------------------------------

--
-- Table structure for table `order_skis`
--

CREATE TABLE `order_skis` (
  `ski_type_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_skis`
--

INSERT INTO `order_skis` (`ski_type_id`, `quantity`, `order_number`) VALUES
(1, 15, 1),
(1, 5, 428),
(2, 20, 1),
(2, 10, 428);

-- --------------------------------------------------------

--
-- Table structure for table `production_plan`
--

CREATE TABLE `production_plan` (
  `ID` int(11) NOT NULL,
  `month` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `production_plan`
--

INSERT INTO `production_plan` (`ID`, `month`) VALUES
(1, '2021-05-01');

-- --------------------------------------------------------

--
-- Table structure for table `production_skis`
--

CREATE TABLE `production_skis` (
  `ski_type_id` int(11) NOT NULL,
  `daily_amount` int(11) NOT NULL,
  `production_plan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `production_skis`
--

INSERT INTO `production_skis` (`ski_type_id`, `daily_amount`, `production_plan_id`) VALUES
(1, 100, 1),
(2, 50, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `shipment_number` int(11) NOT NULL,
  `store_name` varchar(55) NOT NULL,
  `shipping_address` varchar(101) NOT NULL,
  `scheduled_pickup_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `transporter_company_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`shipment_number`, `store_name`, `shipping_address`, `scheduled_pickup_date`, `status`, `driver_id`, `transporter_company_id`) VALUES
(100, 'Best store', 'This is an address', '2021-03-23', 'pickupable', 5, 500);

-- --------------------------------------------------------

--
-- Table structure for table `ski`
--

CREATE TABLE `ski` (
  `production_number` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `order_no` int(11) DEFAULT NULL,
  `ski_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ski`
--

INSERT INTO `ski` (`production_number`, `available`, `order_no`, `ski_type_id`) VALUES
(1, 0, 1, 2),
(2, 1, NULL, 1),
(3, 1, NULL, 1),
(4, 1, NULL, 1),
(5, 1, NULL, 1),
(8, 1, NULL, 2),
(9, 1, NULL, 2),
(10, 1, NULL, 2),
(11, 1, NULL, 2),
(12, 1, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ski_type`
--

CREATE TABLE `ski_type` (
  `ID` int(11) NOT NULL,
  `model` varchar(55) NOT NULL,
  `type` varchar(55) NOT NULL,
  `temperature` varchar(20) NOT NULL,
  `grip_system` varchar(55) NOT NULL,
  `size` int(11) NOT NULL,
  `weight_class` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `discontinued` tinyint(1) NOT NULL,
  `url` text NOT NULL,
  `MSRP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ski_type`
--

INSERT INTO `ski_type` (`ID`, `model`, `type`, `temperature`, `grip_system`, `size`, `weight_class`, `description`, `discontinued`, `url`, `MSRP`) VALUES
(1, 'Super ski 3000', 'Classic', 'warm', 'super grip', 142, '80-90', 'This is a classic ski', 0, '', 1000),
(2, 'Active', 'skate', 'cold', 'wax', 147, '80-90', 'This is a ski', 0, '', 500);

-- --------------------------------------------------------

--
-- Table structure for table `team_skier`
--

CREATE TABLE `team_skier` (
  `customer_id` int(11) NOT NULL,
  `club` varchar(55) NOT NULL,
  `number_of_skis_pr_year` int(11) NOT NULL,
  `date_of_birth` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transition_history`
--

CREATE TABLE `transition_history` (
  `transition_history_id` int(11) NOT NULL,
  `order_number` int(11) NOT NULL,
  `state_change` varchar(50) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transition_history`
--

INSERT INTO `transition_history` (`transition_history_id`, `order_number`, `state_change`, `datetime`) VALUES
(1, 1, 'new -> open', '2021-05-13 14:18:16'),
(2, 1, 'new -> skis-available', '2021-05-13 21:19:08');

-- --------------------------------------------------------

--
-- Table structure for table `transporter`
--

CREATE TABLE `transporter` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transporter`
--

INSERT INTO `transporter` (`company_id`, `company_name`) VALUES
(500, 'Stian shipping inc');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_token`
--
ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `manufacturer_ID` (`manufacturer_ID`),
  ADD KEY `manufacturer_ID_2` (`manufacturer_ID`);

--
-- Indexes for table `franchise`
--
ALTER TABLE `franchise`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `individual_stores`
--
ALTER TABLE `individual_stores`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `franchise_id` (`franchise_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_number`),
  ADD UNIQUE KEY `reference_to_larger_order` (`reference_to_larger_order`,`shipment_number`),
  ADD KEY `shipment_number` (`shipment_number`),
  ADD KEY `customer_id` (`customer_id`) USING BTREE;

--
-- Indexes for table `order_skis`
--
ALTER TABLE `order_skis`
  ADD PRIMARY KEY (`ski_type_id`,`order_number`),
  ADD KEY `order_number` (`order_number`) USING BTREE;

--
-- Indexes for table `production_plan`
--
ALTER TABLE `production_plan`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `production_skis`
--
ALTER TABLE `production_skis`
  ADD PRIMARY KEY (`ski_type_id`,`production_plan_id`),
  ADD KEY `production_plan_id` (`production_plan_id`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`shipment_number`),
  ADD UNIQUE KEY `driver_id` (`driver_id`,`transporter_company_id`),
  ADD UNIQUE KEY `shipment_number` (`shipment_number`),
  ADD KEY `transporter_company_id` (`transporter_company_id`);

--
-- Indexes for table `ski`
--
ALTER TABLE `ski`
  ADD PRIMARY KEY (`production_number`),
  ADD KEY `order_no` (`order_no`,`ski_type_id`),
  ADD KEY `ski_type_id` (`ski_type_id`);

--
-- Indexes for table `ski_type`
--
ALTER TABLE `ski_type`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `team_skier`
--
ALTER TABLE `team_skier`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `transition_history`
--
ALTER TABLE `transition_history`
  ADD PRIMARY KEY (`transition_history_id`),
  ADD KEY `order_number` (`order_number`);

--
-- Indexes for table `transporter`
--
ALTER TABLE `transporter`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_id` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=429;

--
-- AUTO_INCREMENT for table `production_plan`
--
ALTER TABLE `production_plan`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `shipment_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `ski`
--
ALTER TABLE `ski`
  MODIFY `production_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ski_type`
--
ALTER TABLE `ski_type`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transition_history`
--
ALTER TABLE `transition_history`
  MODIFY `transition_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transporter`
--
ALTER TABLE `transporter`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `franchise`
--
ALTER TABLE `franchise`
  ADD CONSTRAINT `franchise_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `individual_stores`
--
ALTER TABLE `individual_stores`
  ADD CONSTRAINT `individual_stores_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `individual_stores_ibfk_2` FOREIGN KEY (`franchise_id`) REFERENCES `franchise` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`shipment_number`) REFERENCES `shipments` (`shipment_number`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_3` FOREIGN KEY (`reference_to_larger_order`) REFERENCES `order` (`order_number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_4` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `order_skis`
--
ALTER TABLE `order_skis`
  ADD CONSTRAINT `order_skis_ibfk_1` FOREIGN KEY (`ski_type_id`) REFERENCES `ski_type` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_skis_ibfk_2` FOREIGN KEY (`order_number`) REFERENCES `order` (`order_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `production_skis`
--
ALTER TABLE `production_skis`
  ADD CONSTRAINT `production_skis_ibfk_1` FOREIGN KEY (`ski_type_id`) REFERENCES `ski_type` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `production_skis_ibfk_2` FOREIGN KEY (`production_plan_id`) REFERENCES `production_plan` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`transporter_company_id`) REFERENCES `transporter` (`company_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ski`
--
ALTER TABLE `ski`
  ADD CONSTRAINT `ski_ibfk_1` FOREIGN KEY (`order_no`) REFERENCES `order` (`order_number`) ON DELETE NO ACTION,
  ADD CONSTRAINT `ski_ibfk_2` FOREIGN KEY (`ski_type_id`) REFERENCES `ski_type` (`ID`) ON DELETE NO ACTION;

--
-- Constraints for table `team_skier`
--
ALTER TABLE `team_skier`
  ADD CONSTRAINT `team_skier_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transition_history`
--
ALTER TABLE `transition_history`
  ADD CONSTRAINT `transition_history_ibfk_1` FOREIGN KEY (`order_number`) REFERENCES `order` (`order_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
