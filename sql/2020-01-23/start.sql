/*
Created: 22.01.2020
Modified: 23.01.2020
Model: MySQL 5.7
Database: MySQL 5.7
*/


-- Create tables section -------------------------------------------------

-- Table Session

CREATE TABLE `Session`
(
  `Id` Char(40) NOT NULL
 COMMENT 'Session hash',
  `Deposit` Decimal(10,2) DEFAULT 100.00
 COMMENT 'Deposit',
  `Incoming` Decimal(10,2) DEFAULT 0.00
 COMMENT 'Incoming',
  `Expenses` Decimal(10,2) DEFAULT 0.00
 COMMENT 'Expenses',
  `Balance` Decimal(10,2) DEFAULT 0.00
 COMMENT 'Balance'
)
;

ALTER TABLE `Session` ADD PRIMARY KEY (`Id`)
;

-- Table SessionOrder

CREATE TABLE `SessionOrder`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'record identifier',
  `Session` Char(40)
 COMMENT 'Session',
  `BTime` Int
 COMMENT 'Buy time',
  `Total` Decimal(10,2)
 COMMENT 'Total cost',
  `Delivery` Int
 COMMENT 'Delivery record Id',
  `DeliveryTax` Decimal(10,2)
 COMMENT 'Delivery tax used',
  PRIMARY KEY (`Id`)
)
;

CREATE INDEX `IX_Session` ON `SessionOrder` (`Session`)
;

CREATE INDEX `IX_Delivery` ON `SessionOrder` (`Delivery`)
;

-- Table Measure

CREATE TABLE `Measure`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'Record identifier',
  `Short` Varchar(8)
 COMMENT 'Short measure name',
  `One` Varchar(32)
 COMMENT 'One measures',
  `Some` Varchar(32)
 COMMENT 'Some measures',
  `Many` Varchar(32)
 COMMENT 'Many measures',
  `Decimals` Tinyint(1) DEFAULT 0
 COMMENT 'Decimal points',
  PRIMARY KEY (`Id`)
)
;

-- Table Product

CREATE TABLE `Product`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'Record identifier',
  `Code` Varchar(16)
 COMMENT 'Product code',
  `Name` Varchar(64)
 COMMENT 'Name',
  `Cost` Decimal(10,2)
 COMMENT 'Product cost',
  `MeasureSize` Decimal(10,1)
 COMMENT 'Measure size',
  `Measure` Int
 COMMENT 'Measure identifier',
  `PreviewImage` Int
 COMMENT 'Preview image ID',
  `Description` Text
 COMMENT 'Product description',
  PRIMARY KEY (`Id`)
)
;

CREATE INDEX `IX_Measure` ON `Product` (`Measure`)
;

CREATE INDEX `IX_PreviewImage` ON `Product` (`PreviewImage`)
;

-- Table SessionOrderProduct

CREATE TABLE `SessionOrderProduct`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'Record identifier',
  `SessionOrder` Int
 COMMENT 'Session order id',
  `Product` Int
 COMMENT 'Product id',
  `Quantity` Float
 COMMENT 'Product quantity',
  `Cost` Decimal(10,2)
 COMMENT 'Product cost',
  PRIMARY KEY (`Id`)
)
;

CREATE INDEX `IX_SessionOrder` ON `SessionOrderProduct` (`SessionOrder`)
;

CREATE INDEX `IX_Product` ON `SessionOrderProduct` (`Product`)
;

-- Table Image

CREATE TABLE `Image`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'Record identifier',
  `Filename` Varchar(255)
 COMMENT 'Image filename',
  `Title` Varchar(127)
 COMMENT 'Image title',
  PRIMARY KEY (`Id`)
)
;

-- Table Delivery

CREATE TABLE `Delivery`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'Record identifier',
  `Name` Varchar(64) NOT NULL
 COMMENT 'Name',
  `Cost` Decimal(10,2)
 COMMENT 'Delivery tax',
  PRIMARY KEY (`Id`)
)
;

CREATE UNIQUE INDEX `UI_Name` ON `Delivery` (`Name`)
;

-- Create foreign keys (relationships) section ------------------------------------------------- 


ALTER TABLE `SessionOrder` ADD CONSTRAINT `FK_Session_SessionOrder` FOREIGN KEY (`Session`) REFERENCES `Session` (`Id`) ON DELETE RESTRICT ON UPDATE CASCADE
;


ALTER TABLE `Product` ADD CONSTRAINT `FK_Measure_Product` FOREIGN KEY (`Measure`) REFERENCES `Measure` (`Id`) ON DELETE RESTRICT ON UPDATE NO ACTION
;


ALTER TABLE `Product` ADD CONSTRAINT `FK_Image_Product` FOREIGN KEY (`PreviewImage`) REFERENCES `Image` (`Id`) ON DELETE SET NULL ON UPDATE NO ACTION
;


ALTER TABLE `SessionOrderProduct` ADD CONSTRAINT `FK_SessionOrder_SessionOrderProduct` FOREIGN KEY (`SessionOrder`) REFERENCES `SessionOrder` (`Id`) ON DELETE RESTRICT ON UPDATE NO ACTION
;


ALTER TABLE `SessionOrderProduct` ADD CONSTRAINT `FK_Product_SessionOrderProduct` FOREIGN KEY (`Product`) REFERENCES `Product` (`Id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;


ALTER TABLE `SessionOrder` ADD CONSTRAINT `FK_Delivery_SessionOrde` FOREIGN KEY (`Delivery`) REFERENCES `Delivery` (`Id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;


