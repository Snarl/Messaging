CREATE  TABLE `ta`.`inboundq` (
  `idinboundq` INT NOT NULL AUTO_INCREMENT ,
  `ts` TIMESTAMP NULL ,
  `source` VARCHAR(45) NULL ,
  `id` VARCHAR(45) NULL ,
  `sender` VARCHAR(256) NULL ,
  `textcontent` VARCHAR(500) NULL ,
  `richcontent` VARCHAR(500) NULL ,
  `status` VARCHAR(45) NULL ,
  `tag` VARCHAR(45) NULL ,
  PRIMARY KEY (`idinboundq`) )
ENGINE = InnoDB;
