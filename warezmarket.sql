-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 01, 2023 at 12:57 AM
-- Server version: 10.10.1-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warezmarket`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `shippingfullname` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingcountrycode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingphone` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippinglineone` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippinglinetwo` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingzipcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingcity` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingfullname` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingcountrycode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billinglineone` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billinglinetwo` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingzipcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingcity` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL,
  `imageurl` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternativetext` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slidetext` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `userid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `addeddate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guid` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderaddress`
--

CREATE TABLE `orderaddress` (
  `id` int(11) NOT NULL,
  `shippingfullname` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingcountrycode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingphone` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippinglineone` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippinglinetwo` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingzipcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shippingcity` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingfullname` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingcountrycode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billinglineone` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billinglinetwo` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingzipcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billingcity` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `orderid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unitprice` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `ordernum` varchar(28) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userid` int(11) NOT NULL,
  `addressid` int(11) NOT NULL,
  `shippingcost` float NOT NULL DEFAULT 0,
  `status` enum('PENDING','PROCESSING','SHIPPED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `tracknum` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `placedat` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imageuri` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upc` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instock` int(11) NOT NULL,
  `categoryid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype` enum('ADMINISTRATOR','CUSTOMER') COLLATE utf8mb4_unicode_ci NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE_USER` (`userid`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD UNIQUE KEY `IDINCART` (`userid`,`productid`) USING BTREE,
  ADD KEY `PRODID` (`productid`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE_INDEX` (`guid`);

--
-- Indexes for table `orderaddress`
--
ALTER TABLE `orderaddress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUEINDEX` (`shippingfullname`,`shippingcountrycode`,`shippingphone`,`shippinglineone`,`shippinglinetwo`,`shippingzipcode`,`shippingcity`,`billingfullname`,`billingcountrycode`,`billinglineone`,`billinglinetwo`,`billingzipcode`,`billingcity`) USING HASH;

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD UNIQUE KEY `UNIQUE_KEY` (`orderid`,`itemid`),
  ADD KEY `ITEMIDFK` (`itemid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `USERIDFK` (`userid`),
  ADD KEY `ADDRESSIDFK` (`addressid`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `CATEGORYID` (`categoryid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE_EMAIL` (`email`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orderaddress`
--
ALTER TABLE `orderaddress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `USERID` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `PRODID` FOREIGN KEY (`productid`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `USRID` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `ITEMIDFK` FOREIGN KEY (`itemid`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ORDERIDFK` FOREIGN KEY (`orderid`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `ADDRESSIDFK` FOREIGN KEY (`addressid`) REFERENCES `orderaddress` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `USERIDFK` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `CATEGORYID` FOREIGN KEY (`categoryid`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
