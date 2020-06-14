-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: abc_test
-- ------------------------------------------------------
-- Server version	5.7.29-log

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
-- Table structure for table `Delivery`
--

DROP TABLE IF EXISTS `Delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Delivery` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
  `Cost` decimal(10,2) DEFAULT NULL COMMENT 'Delivery tax',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UI_Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Delivery`
--
-- ORDER BY:  `Id`

LOCK TABLES `Delivery` WRITE;
/*!40000 ALTER TABLE `Delivery` DISABLE KEYS */;
INSERT INTO `Delivery` VALUES (1,'Pick up',0.00),(2,'UPS',5.00);
/*!40000 ALTER TABLE `Delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Image`
--

DROP TABLE IF EXISTS `Image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Image` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Image filename',
  `Title` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Image title',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Image`
--
-- ORDER BY:  `Id`

LOCK TABLES `Image` WRITE;
/*!40000 ALTER TABLE `Image` DISABLE KEYS */;
INSERT INTO `Image` VALUES (1,'00000.jpg','Cheese'),(2,'00001.jpg','Beer'),(3,'00002.jpg','Apple'),(4,'00003.jpg','Water');
/*!40000 ALTER TABLE `Image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Measure`
--

DROP TABLE IF EXISTS `Measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Measure` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Short` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Short measure name',
  `One` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'One measure',
  `Some` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Some measures',
  `Many` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Many measures',
  `Decimals` tinyint(1) DEFAULT '0' COMMENT 'Decimal points',
  `InputStep` float(9,3) DEFAULT '1.000' COMMENT 'Input step',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Measure`
--
-- ORDER BY:  `Id`

LOCK TABLES `Measure` WRITE;
/*!40000 ALTER TABLE `Measure` DISABLE KEYS */;
INSERT INTO `Measure` VALUES (1,'kg','kg','kg','kg',3,0.100),(2,'btl','btl','btls','btls',0,1.000);
/*!40000 ALTER TABLE `Measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Product`
--

DROP TABLE IF EXISTS `Product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Code` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Product code',
  `Name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name',
  `Cost` decimal(10,2) DEFAULT NULL COMMENT 'Product cost',
  `MeasureSize` decimal(10,1) DEFAULT NULL COMMENT 'Measure size',
  `Measure` int(11) DEFAULT NULL COMMENT 'Measure identifier',
  `PreviewImage` int(11) DEFAULT NULL COMMENT 'Preview image ID',
  `Description` text COLLATE utf8_unicode_ci COMMENT 'Product description',
  `AverageRate` float DEFAULT '0' COMMENT 'Average rate',
  `RateCount` int(11) DEFAULT '0' COMMENT 'Rate count',
  PRIMARY KEY (`Id`),
  KEY `IX_Measure` (`Measure`),
  KEY `IX_PreviewImage` (`PreviewImage`),
  CONSTRAINT `FK_Image_Product` FOREIGN KEY (`PreviewImage`) REFERENCES `Image` (`Id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `FK_Measure_Product` FOREIGN KEY (`Measure`) REFERENCES `Measure` (`Id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Product`
--
-- ORDER BY:  `Id`

LOCK TABLES `Product` WRITE;
/*!40000 ALTER TABLE `Product` DISABLE KEYS */;
INSERT INTO `Product` VALUES (1,'001','Apple',0.30,1.0,NULL,3,'An Apple',0,0),(2,'002','Beer',2.00,1.0,NULL,2,'A beer',0,0),(3,'003','Water',1.00,1.0,2,4,'A water',0,0),(4,'004','Cheese',3.74,1.0,1,1,'A cheese',0,0);
/*!40000 ALTER TABLE `Product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Rate`
--

DROP TABLE IF EXISTS `Rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rate` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `Session` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Session',
  `Product` int(11) DEFAULT NULL COMMENT 'Product',
  `Rate` int(11) DEFAULT '0' COMMENT 'Rate',
  PRIMARY KEY (`Id`),
  KEY `IX_Product` (`Product`),
  KEY `IX_Session` (`Session`),
  CONSTRAINT `FK_Product_Rate` FOREIGN KEY (`Product`) REFERENCES `Product` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_Session_Rate` FOREIGN KEY (`Session`) REFERENCES `Session` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Rate`
--
-- ORDER BY:  `Id`

LOCK TABLES `Rate` WRITE;
/*!40000 ALTER TABLE `Rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `Rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Session`
--

DROP TABLE IF EXISTS `Session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Session` (
  `Id` char(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Session hash',
  `Deposit` decimal(10,2) DEFAULT '100.00' COMMENT 'Deposit',
  `Incoming` decimal(10,2) DEFAULT '0.00' COMMENT 'Incoming',
  `Expenses` decimal(10,2) DEFAULT '0.00' COMMENT 'Expenses',
  `Balance` decimal(10,2) DEFAULT '100.00' COMMENT 'Balance',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Session`
--
-- ORDER BY:  `Id`

LOCK TABLES `Session` WRITE;
/*!40000 ALTER TABLE `Session` DISABLE KEYS */;
/*!40000 ALTER TABLE `Session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SessionOrder`
--

DROP TABLE IF EXISTS `SessionOrder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `SessionOrder` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'record identifier',
  `Session` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Session',
  `BTime` int(11) DEFAULT NULL COMMENT 'Buy time',
  `Total` decimal(10,2) DEFAULT NULL COMMENT 'Total cost',
  `Delivery` int(11) DEFAULT NULL COMMENT 'Delivery record Id',
  `DeliveryTax` decimal(10,2) DEFAULT NULL COMMENT 'Delivery tax used',
  PRIMARY KEY (`Id`),
  KEY `IX_Session` (`Session`),
  KEY `IX_Delivery` (`Delivery`),
  CONSTRAINT `FK_Delivery_SessionOrde` FOREIGN KEY (`Delivery`) REFERENCES `Delivery` (`Id`),
  CONSTRAINT `FK_Session_SessionOrder` FOREIGN KEY (`Session`) REFERENCES `Session` (`Id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SessionOrder`
--
-- ORDER BY:  `Id`

LOCK TABLES `SessionOrder` WRITE;
/*!40000 ALTER TABLE `SessionOrder` DISABLE KEYS */;
/*!40000 ALTER TABLE `SessionOrder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SessionOrderProduct`
--

DROP TABLE IF EXISTS `SessionOrderProduct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `SessionOrderProduct` (
  `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record identifier',
  `SessionOrder` int(11) DEFAULT NULL COMMENT 'Session order id',
  `Product` int(11) DEFAULT NULL COMMENT 'Product id',
  `Quantity` float DEFAULT NULL COMMENT 'Product quantity',
  `Cost` decimal(10,2) DEFAULT NULL COMMENT 'Product cost',
  PRIMARY KEY (`Id`),
  KEY `IX_SessionOrder` (`SessionOrder`),
  KEY `IX_Product` (`Product`),
  CONSTRAINT `FK_Product_SessionOrderProduct` FOREIGN KEY (`Product`) REFERENCES `Product` (`Id`),
  CONSTRAINT `FK_SessionOrder_SessionOrderProduct` FOREIGN KEY (`SessionOrder`) REFERENCES `SessionOrder` (`Id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SessionOrderProduct`
--
-- ORDER BY:  `Id`

LOCK TABLES `SessionOrderProduct` WRITE;
/*!40000 ALTER TABLE `SessionOrderProduct` DISABLE KEYS */;
/*!40000 ALTER TABLE `SessionOrderProduct` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-01-30  8:26:07
