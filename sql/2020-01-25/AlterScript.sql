
ALTER TABLE `product`
  ADD COLUMN  `AverageRate` Float DEFAULT 0
 COMMENT 'Average rate',
  ADD COLUMN  `RateCount` Int DEFAULT 0
 COMMENT 'Rate count'
;


CREATE TABLE `Rate`
(
  `Id` Int NOT NULL AUTO_INCREMENT
 COMMENT 'Record identifier',
  `Session` Char(40)
 COMMENT 'Session',
  `Product` Int
 COMMENT 'Product',
  `Rate` Int DEFAULT 0
 COMMENT 'Rate',
  PRIMARY KEY (`Id`)
)
;

CREATE INDEX `IX_Product` ON `Rate` (`Product`)
;

CREATE INDEX `IX_Session` ON `Rate` (`Session`)
;


DROP INDEX `IX_Measure` ON `product`
;
CREATE INDEX `IX_Measure` ON `product` (`Measure`)
;


DROP INDEX `IX_PreviewImage` ON `product`
;
CREATE INDEX `IX_PreviewImage` ON `product` (`PreviewImage`)
;

ALTER TABLE `Rate` ADD CONSTRAINT `FK_Product_Rate` FOREIGN KEY (`Product`) REFERENCES `product` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
;
ALTER TABLE `Rate` ADD CONSTRAINT `FK_Session_Rate` FOREIGN KEY (`Session`) REFERENCES `session` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
;
