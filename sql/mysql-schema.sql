CREATE DATABASE myhealth;

USE myhealth;

CREATE TABLE `Account` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`firstname` varchar(100) NOT NULL,
	`lastname` varchar(100) NOT NULL,
	`email` varchar(255) NOT NULL UNIQUE,
	`password` varchar(50) NOT NULL,
	`status` INT NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `Oversight` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`accountId` INT NOT NULL,
	`date` DATETIME NOT NULL,
	`title` varchar(255) NOT NULL,
	`status` INT NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `Parameter` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`oversightId` INT NOT NULL,
	`name` varchar(100) NOT NULL,
	`unit` varchar(25),
	`status` INT NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `OversightEntry` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`oversightId` INT NOT NULL,
	`date` DATETIME NOT NULL,
	`status` INT NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `EntryDetail` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`entryId` INT NOT NULL,
	`parameterId` INT NOT NULL,
	`value` DOUBLE,
	`status` INT NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
);

ALTER TABLE `Oversight` ADD CONSTRAINT `Oversight_fk0` FOREIGN KEY (`accountId`) REFERENCES `Account`(`id`);

ALTER TABLE `Parameter` ADD CONSTRAINT `Parameter_fk0` FOREIGN KEY (`oversightId`) REFERENCES `Oversight`(`id`);

ALTER TABLE `OversightEntry` ADD CONSTRAINT `OversightEntry_fk0` FOREIGN KEY (`oversightId`) REFERENCES `Oversight`(`id`);

ALTER TABLE `EntryDetail` ADD CONSTRAINT `EntryDetail_fk0` FOREIGN KEY (`entryId`) REFERENCES `OversightEntry`(`id`);

ALTER TABLE `EntryDetail` ADD CONSTRAINT `EntryDetail_fk1` FOREIGN KEY (`parameterId`) REFERENCES `Parameter`(`id`);











