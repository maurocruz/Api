--
-- CREATE TABLE order
--
-- dependency and ralations with
-- -- offer
--

CREATE TABLE IF NOT EXISTS `order` (
    `idorder` INT NOT NULL AUTO_INCREMENT,
    `orderedItem` INT NULL,
    `customer` INT NOT NULL,
    `seller` INT NULL,
    `orderDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `orderStatus` VARCHAR(45) NULL DEFAULT NULL,
    `paymentDueDate` DATETIME NULL,
    `discount` INT NULL,
    PRIMARY KEY (`idorder`),
    INDEX `fk_order_service1_idx` (`seller` ASC),
    CONSTRAINT `fk_order_service1` FOREIGN KEY (`seller`) REFERENCES `service` (`provider`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `order_has_offer` (
  `idorder` INT NOT NULL,
  `idoffer` INT NOT NULL,
  PRIMARY KEY (`idorder`, `idoffer`),
  INDEX `fk_order_has_offer_offer1_idx` (`idoffer` ASC),
  INDEX `fk_order_has_offer_order1_idx` (`idorder` ASC),
  CONSTRAINT `fk_order_has_offer_order1` FOREIGN KEY (`idorder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_has_offer_offer1` FOREIGN KEY (`idoffer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `order_has_invoice` (
    `idinvoice` INT NOT NULL,
    `idorder` INT NOT NULL,
    PRIMARY KEY (`idinvoice`, `idorder`),
    INDEX `fk_invoice_has_order_order1_idx` (`idorder` ASC),
    INDEX `fk_invoice_has_order_invoice1_idx` (`idinvoice` ASC),
    CONSTRAINT `fk_invoice_has_order_invoice1` FOREIGN KEY (`idinvoice`) REFERENCES `invoice` (`idinvoice`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_invoice_has_order_order1` FOREIGN KEY (`idorder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;