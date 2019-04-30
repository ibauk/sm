-- Upgrade to include Entrant contact details

ALTER TABLE `entrants`	ADD 	`Phone` TEXT;
ALTER TABLE `entrants`	ADD 	`Email` TEXT;
ALTER TABLE `entrants`	ADD 	`NoKName` TEXT;
ALTER TABLE `entrants`	ADD 	`NoKRelation` TEXT;
ALTER TABLE `entrants`	ADD 	`NoKPhone` TEXT;

UPDATE `rallyparams` SET `DBVersion`=2;