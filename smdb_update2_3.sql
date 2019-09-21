-- Upgrade to DBVersion=3

ALTER TABLE `catcompound`	ADD 		`Compulsory` INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 		`MinimumTicks`	INTEGER DEFAULT 0;
-- Need to alter the type of `ScorePoints` to TEXT in order to permit multiple point values
ALTER TABLE `combinations`	ADD 	`Cat1`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat2`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat3`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat4`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat5`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat6`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat7`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat8`	INTEGER DEFAULT 0;
ALTER TABLE `combinations`	ADD 	`Cat9`	INTEGER DEFAULT 0;

ALTER TABLE `rallyparams`	ADD 	`Cat4Label`	TEXT;
ALTER TABLE `rallyparams`	ADD 	`Cat5Label`	TEXT;
ALTER TABLE `rallyparams`	ADD 	`Cat6Label`	TEXT;
ALTER TABLE `rallyparams`	ADD 	`Cat7Label`	TEXT;
ALTER TABLE `rallyparams`	ADD 	`Cat8Label`	TEXT;
ALTER TABLE `rallyparams`	ADD 	`Cat9Label`	TEXT;

ALTER TABLE `timepenalties`	ADD	`TimeSpec`	INTEGER DEFAULT 0;

ALTER TABLE `bonuses`	ADD 		`Cat4`	INTEGER DEFAULT 0;
ALTER TABLE `bonuses`	ADD 		`Cat5`	INTEGER DEFAULT 0;
ALTER TABLE `bonuses`	ADD 		`Cat6`	INTEGER DEFAULT 0;
ALTER TABLE `bonuses`	ADD 		`Cat7`	INTEGER DEFAULT 0;
ALTER TABLE `bonuses`	ADD 		`Cat8`	INTEGER DEFAULT 0;
ALTER TABLE `bonuses`	ADD 		`Cat9`	INTEGER DEFAULT 0;

ALTER TABLE `entrants`	ADD 		`BCMethod` INTEGER DEFAULT 0;

UPDATE `rallyparams` SET `DBVersion`=3;