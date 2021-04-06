-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2021 at 12:29 PM
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
-- Database: `ski_manufacturerdb`
--

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
(11, 'Stian', '2021-03-21', NULL),
(69, 'Stian', '2021-03-23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_representative`
--

CREATE TABLE `customer_representative` (
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `ID` int(11) NOT NULL,
  `department` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `manufacturer_ID` int(11) NOT NULL
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
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_number`, `total_price`, `state`, `reference_to_larger_order`, `shipment_number`, `customer_id`) VALUES
(420, 1000, 'new', NULL, 100, 10),
(423, 1, 'new', NULL, NULL, 11),
(424, 1, 'new', NULL, NULL, 69),
(426, 50, 'new', NULL, NULL, 69),
(427, 1, 'new', NULL, NULL, 10);

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
(1, 5, 420),
(2, 10, 420);

-- --------------------------------------------------------

--
-- Table structure for table `production_plan`
--

CREATE TABLE `production_plan` (
  `ID` int(11) NOT NULL,
  `month` date NOT NULL,
  `planner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `production_planner`
--

CREATE TABLE `production_planner` (
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `production_skis`
--

CREATE TABLE `production_skis` (
  `ski_type_id` int(11) NOT NULL,
  `daily_amount` int(11) NOT NULL,
  `production_plan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(100, 'kys', 'Nyvegen 16', '2021-03-23', 'pickupable', 5, 500);

-- --------------------------------------------------------

--
-- Table structure for table `shopkeeper`
--

CREATE TABLE `shopkeeper` (
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ski`
--

CREATE TABLE `ski` (
  `production_number` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `order_no` int(11) NOT NULL,
  `ski_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 'Super ski 3000', 'Classic', 'warm', 'super grip', 142, '80-90', 'is good ski yes', 0, '', 1000),
(2, 'Active', 'skate', 'cold', 'wax', 147, '80-90', 'very shit ski', 0, 'google.com', 500);

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
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `customer_representative`
--
ALTER TABLE `customer_representative`
  ADD PRIMARY KEY (`employee_id`);

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
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `planners` (`planner_id`);

--
-- Indexes for table `production_planner`
--
ALTER TABLE `production_planner`
  ADD PRIMARY KEY (`employee_id`);

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
-- Indexes for table `shopkeeper`
--
ALTER TABLE `shopkeeper`
  ADD PRIMARY KEY (`employee_id`);

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
  MODIFY `order_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;

--
-- AUTO_INCREMENT for table `production_plan`
--
ALTER TABLE `production_plan`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `shipment_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `ski_type`
--
ALTER TABLE `ski_type`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transporter`
--
ALTER TABLE `transporter`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_representative`
--
ALTER TABLE `customer_representative`
  ADD CONSTRAINT `customer_representative_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `order_skis_ibfk_2` FOREIGN KEY (`order_number`) REFERENCES `order` (`order_number`) ON UPDATE CASCADE;

--
-- Constraints for table `production_plan`
--
ALTER TABLE `production_plan`
  ADD CONSTRAINT `production_plan_ibfk_1` FOREIGN KEY (`planner_id`) REFERENCES `production_planner` (`employee_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `production_planner`
--
ALTER TABLE `production_planner`
  ADD CONSTRAINT `production_planner_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `shopkeeper`
--
ALTER TABLE `shopkeeper`
  ADD CONSTRAINT `shopkeeper_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ski`
--
ALTER TABLE `ski`
  ADD CONSTRAINT `ski_ibfk_1` FOREIGN KEY (`order_no`) REFERENCES `order` (`order_number`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ski_ibfk_2` FOREIGN KEY (`ski_type_id`) REFERENCES `ski_type` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `team_skier`
--
ALTER TABLE `team_skier`
  ADD CONSTRAINT `team_skier_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
