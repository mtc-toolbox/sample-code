CREATE DATABASE  IF NOT EXISTS `abc_test` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `abc_test`;
-- MySQL dump 10.13  Distrib 8.0.18, for Win64 (x86_64)
--
-- Host: localhost    Database: abc_test
-- ------------------------------------------------------
-- Server version	5.7.28-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `delivery`
--

DROP TABLE IF EXISTS `delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `Cost` decimal(10,2) DEFAULT NULL COMMENT 'Delivery tax',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UI_Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery`
--

LOCK TABLES `delivery` WRITE;
/*!40000 ALTER TABLE `delivery` DISABLE KEYS */;
INSERT INTO `delivery` VALUES (1,'Pick up',0.00),(2,'UPS',5.00);
/*!40000 ALTER TABLE `delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `image` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Image filename',
  `Title` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Image title',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image`
--

LOCK TABLES `image` WRITE;
/*!40000 ALTER TABLE `image` DISABLE KEYS */;
INSERT INTO `image` VALUES (1,'00000.jpg','Cheese'),(2,'00001.jpg','Beer'),(3,'00002.jpg','Apple'),(4,'00003.png','Water');
/*!40000 ALTER TABLE `image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `measure`
--

DROP TABLE IF EXISTS `measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `measure` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Short` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Short measure name',
  `One` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'One measures',
  `Some` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Some measures',
  `Many` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Many measures',
  `Decimals` tinyint(1) DEFAULT '0' COMMENT 'Decimal points',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `measure`
--

LOCK TABLES `measure` WRITE;
/*!40000 ALTER TABLE `measure` DISABLE KEYS */;
INSERT INTO `measure` VALUES (1,'kg','kg','kg','kg',3),(2,'btl','btl','btls','btls',0);
/*!40000 ALTER TABLE `measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Code` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Product code',
  `Name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `Cost` decimal(10,2) DEFAULT NULL COMMENT 'Product cost',
  `MeasureSize` decimal(10,1) DEFAULT NULL COMMENT 'Measure size',
  `Measure` int(11) DEFAULT NULL COMMENT 'Measure identifier',
  `PreviewImage` int(11) DEFAULT NULL COMMENT 'Preview image ID',
  `Description` text COLLATE utf8_unicode_ci COMMENT 'Product description',
  PRIMARY KEY (`Id`),
  KEY `IX_Measure` (`Measure`),
  KEY `IX_PreviewImage` (`PreviewImage`),
  CONSTRAINT `FK_Image_Product` FOREIGN KEY (`PreviewImage`) REFERENCES `image` (`Id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `FK_Measure_Product` FOREIGN KEY (`Measure`) REFERENCES `measure` (`Id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'001','Apple',0.30,1.0,NULL,3,'An Apple'),(2,'002','Beer',2.00,1.0,2,2,'A beer'),(3,'003','Water',1.00,1.0,2,4,'A water'),(4,'004','Cheese',3.74,1.0,1,1,'A cheese');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `Id` char(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Session hash',
  `Deposit` decimal(10,2) DEFAULT '100.00' COMMENT 'Deposit',
  `Incoming` decimal(10,2) DEFAULT '0.00' COMMENT 'Incoming',
  `Expenses` decimal(10,2) DEFAULT '0.00' COMMENT 'Expenses',
  `Balance` decimal(10,2) DEFAULT '100.00' COMMENT 'Balance',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessionorder`
--

DROP TABLE IF EXISTS `sessionorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessionorder` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'record identifier',
  `Session` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Session',
  `BTime` int(11) DEFAULT NULL COMMENT 'Buy time',
  `Total` decimal(10,2) DEFAULT NULL COMMENT 'Total cost',
  `Delivery` int(11) DEFAULT NULL COMMENT 'Delivery record Id',
  `DeliveryTax` decimal(10,2) DEFAULT NULL COMMENT 'Delivery tax used',
  PRIMARY KEY (`Id`),
  KEY `IX_Session` (`Session`),
  KEY `IX_Delivery` (`Delivery`),
  CONSTRAINT `FK_Delivery_SessionOrde` FOREIGN KEY (`Delivery`) REFERENCES `delivery` (`Id`),
  CONSTRAINT `FK_Session_SessionOrder` FOREIGN KEY (`Session`) REFERENCES `session` (`Id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessionorder`
--

LOCK TABLES `sessionorder` WRITE;
/*!40000 ALTER TABLE `sessionorder` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessionorder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessionorderproduct`
--

DROP TABLE IF EXISTS `sessionorderproduct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessionorderproduct` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `SessionOrder` int(11) DEFAULT NULL COMMENT 'Session order id',
  `Product` int(11) DEFAULT NULL COMMENT 'Product id',
  `Quantity` float DEFAULT NULL COMMENT 'Product quantity',
  `Cost` decimal(10,2) DEFAULT NULL COMMENT 'Product cost',
  PRIMARY KEY (`Id`),
  KEY `IX_SessionOrder` (`SessionOrder`),
  KEY `IX_Product` (`Product`),
  CONSTRAINT `FK_Product_SessionOrderProduct` FOREIGN KEY (`Product`) REFERENCES `product` (`Id`),
  CONSTRAINT `FK_SessionOrder_SessionOrderProduct` FOREIGN KEY (`SessionOrder`) REFERENCES `sessionorder` (`Id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessionorderproduct`
--

LOCK TABLES `sessionorderproduct` WRITE;
/*!40000 ALTER TABLE `sessionorderproduct` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessionorderproduct` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-01-24  9:03:13
