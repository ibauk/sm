-- Upgrade to DBVersion=4

-- Lose the draft claims table
DROP TABLE IF EXISTS `claims`;

CREATE TABLE "claims" (
	"LoggedAt"	TEXT,
	"ClaimTime"	TEXT,
	"BCMethod"	INTEGER DEFAULT 0,
	"EntrantID"	INTEGER,
	"BonusID"	TEXT,
	"OdoReading"	INTEGER,
	"Judged"	INTEGER DEFAULT 0,
	"Decision"	INTEGER DEFAULT 0,
	"Applied"	INTEGER DEFAULT 0
);

ALTER TABLE `rallyparams` ADD `AutoRank` INTEGER NOT NULL DEFAULT 1;
ALTER TABLE `rallyparams` RENAME COLUMN `CertificateHours` TO `MaxHours`;

UPDATE `rallyparams` SET `DBVersion`=4;