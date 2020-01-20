/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I provide the initial database when building new rallies.
 *
 * I am written for readability rather than efficiency, please keep me that way.
 *
 *
 * Copyright (c) 2019 Bob Stammers
 *
 *
 * This file is part of IBAUK-SCOREMASTER.
 *
 * IBAUK-SCOREMASTER is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * IBAUK-SCOREMASTER is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with IBAUK-SCOREMASTER.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

-- DBVERSION: 4

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS `rallyparams` (
	`RallyTitle`	TEXT,
	`RallySlogan`	TEXT,
	`MaxHours`	INTEGER NOT NULL DEFAULT 12,
	`StartTime`	TEXT,
	`FinishTime`	TEXT,
	`MinMiles`	INTEGER NOT NULL DEFAULT 0,
	`PenaltyMaxMiles`	INTEGER NOT NULL DEFAULT 0,
	`MaxMilesMethod`	INTEGER NOT NULL DEFAULT 0,
	`MaxMilesPoints`	INTEGER NOT NULL DEFAULT 0,
	`PenaltyMilesDNF`	INTEGER NOT NULL DEFAULT 0,
	`MinPoints`	INTEGER NOT NULL DEFAULT 0,
	`ScoringMethod`	INTEGER NOT NULL DEFAULT 3,
	`ShowMultipliers`	INTEGER NOT NULL DEFAULT 2,
	`TiedPointsRanking`	INTEGER NOT NULL DEFAULT 0,
	`TeamRanking`	INTEGER NOT NULL DEFAULT 0,
	`OdoCheckMiles`	NUMERIC DEFAULT 0,
	`Cat1Label`	TEXT,
	`Cat2Label`	TEXT,
	`Cat3Label`	TEXT,
	`Cat4Label`	TEXT,
	`Cat5Label`	TEXT,
	`Cat6Label`	TEXT,
	`Cat7Label`	TEXT,
	`Cat8Label`	TEXT,
	`Cat9Label`	TEXT,
	`RejectReasons`	TEXT DEFAULT "1=No/wrong photo
2=Photo unclear
3=Out of hours
4=Face not in photo
5=Bike not in photo
6=Flag not in photo
7=Missing rider/pillion
8=Missing receipt
9=Rallymaster!",
	`DBState` INTEGER NOT NULL DEFAULT 0,
	`DBVersion` INTEGER NOT NULL DEFAULT 4, 		/* DBVERSION */
	`AutoRank` INTEGER NOT NULL DEFAULT 1
);


CREATE TABLE IF NOT EXISTS `functions` (
	`functionid`	INTEGER,
	`menulbl`	TEXT,
	`url`	TEXT,
	`onclick`	TEXT,
	`Tags`	TEXT,
	PRIMARY KEY(`functionid`)
);


CREATE TABLE IF NOT EXISTS `menus` (
	`menuid`	TEXT,
	`menulbl`	TEXT,
	`menufuncs`	TEXT,
	PRIMARY KEY(`menuid`)
);


CREATE TABLE IF NOT EXISTS `certificates` (
	`EntrantID`	INTEGER NOT NULL DEFAULT 0,
	`css`	TEXT,
	`html`	TEXT,
	`options`	TEXT,
	`image`	TEXT,
	`Class`	INTEGER NOT NULL DEFAULT 0,
	`Title`	TEXT,
	PRIMARY KEY(`EntrantID`,`Class`)
);


CREATE TABLE IF NOT EXISTS `timepenalties` (
	`TimeSpec`	INTEGER NOT NULL DEFAULT 0,
	`PenaltyStart`	TEXT,
	`PenaltyFinish`	TEXT,
	`PenaltyMethod`	INTEGER NOT NULL DEFAULT 0,
	`PenaltyFactor`	INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `specials` (
	`BonusID`	TEXT,
	`BriefDesc`	TEXT,
	`GroupName`	TEXT,
	`Points`	INTEGER NOT NULL DEFAULT 0,
	`MultFactor`	INTEGER NOT NULL DEFAULT 0,
	`Compulsory`	INTEGER NOT NULL DEFAULT 0,
	`AskPoints`		INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(`BonusID`)
);
CREATE TABLE IF NOT EXISTS `sgroups` (
	`GroupName`	TEXT NOT NULL,
	`GroupType`	TEXT DEFAULT 'C',
	PRIMARY KEY(`GroupName`)
);
CREATE TABLE IF NOT EXISTS `entrants` (
	`EntrantID`	INTEGER,
	`Bike`	TEXT,
	`BikeReg`	TEXT,
	`RiderName`	TEXT,
	`RiderFirst`	TEXT,
	`RiderIBA`	INTEGER,
	`PillionName`	TEXT,
	`PillionFirst`	TEXT,
	`PillionIBA`	INTEGER,
	`TeamID`	INTEGER NOT NULL DEFAULT 0,
	`Country`	TEXT DEFAULT 'UK',
	`OdoKms`	INTEGER NOT NULL DEFAULT 0,
	`OdoCheckStart`	NUMERIC,
	`OdoCheckFinish`	NUMERIC,
	`OdoCheckTrip`	NUMERIC,
	`OdoScaleFactor`	NUMERIC DEFAULT 1,
	`OdoRallyStart`	NUMERIC,
	`OdoRallyFinish`	NUMERIC,
	`CorrectedMiles`	NUMERIC,
	`FinishTime`	TEXT,
	`BonusesVisited`	TEXT,
	`SpecialsTicked`	TEXT,
	`CombosTicked`	TEXT,
	`TotalPoints`	INTEGER NOT NULL DEFAULT 0,
	`StartTime`	TEXT,
	`FinishPosition`	INTEGER NOT NULL DEFAULT 0,
	`EntrantStatus`	INTEGER NOT NULL DEFAULT 1,
	`ScoringNow`	INTEGER NOT NULL DEFAULT 0,
	`ScoredBy`	TEXT,
	`ExtraData`	TEXT,
	`Class`	INTEGER NOT NULL DEFAULT 0,
	`ScoreX`	TEXT,
	`RejectedClaims`	TEXT,
	`Phone` TEXT,
	`Email` TEXT,
	`NoKName` TEXT,
	`NoKRelation` TEXT,
	`NoKPhone` TEXT,
	`BCMethod` INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(`EntrantID`)
);
CREATE TABLE IF NOT EXISTS `combinations` (
	`ComboID`	TEXT,
	`BriefDesc`	TEXT,
	`ScoreMethod`	INTEGER NOT NULL DEFAULT 0,
	`MinimumTicks`	INTEGER NOT NULL DEFAULT 0,
	`ScorePoints`	TEXT DEFAULT '0',
	`Bonuses`	TEXT,
	`Cat1`	INTEGER NOT NULL DEFAULT 0,
	`Cat2`	INTEGER NOT NULL DEFAULT 0,
	`Cat3`	INTEGER NOT NULL DEFAULT 0,
	`Cat4`	INTEGER NOT NULL DEFAULT 0,
	`Cat5`	INTEGER NOT NULL DEFAULT 0,
	`Cat6`	INTEGER NOT NULL DEFAULT 0,
	`Cat7`	INTEGER NOT NULL DEFAULT 0,
	`Cat8`	INTEGER NOT NULL DEFAULT 0,
	`Cat9`	INTEGER NOT NULL DEFAULT 0,
	`Compulsory`	INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(`ComboID`)
);
CREATE TABLE IF NOT EXISTS `claims` (
	`LoggedAt`	TEXT,
	`ClaimTime`	TEXT,
	`BCMethod`	INTEGER NOT NULL DEFAULT 0,
	`EntrantID`	INTEGER,
	`BonusID`	TEXT,
	`OdoReading`	INTEGER,
	`Judged`	INTEGER NOT NULL DEFAULT 0,
	`Decision`	INTEGER NOT NULL DEFAULT 0,
	`Applied`	INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `categories` (
	`Axis`	INTEGER NOT NULL DEFAULT 1,
	`Cat`	INTEGER,
	`BriefDesc`	TEXT,
	PRIMARY KEY(`Axis`,`Cat`)
);
CREATE TABLE IF NOT EXISTS `catcompound` (
	`Axis`	INTEGER NOT NULL DEFAULT 1,
	`Cat`	INTEGER,
	`NMethod`	INTEGER NOT NULL DEFAULT -1,
	`ModBonus`	INTEGER NOT NULL DEFAULT 0,
	`NMin`	INTEGER NOT NULL DEFAULT 1,
	`PointsMults`	INTEGER NOT NULL DEFAULT 0,
	`NPower`	INTEGER NOT NULL DEFAULT 2,
	`Compulsory` INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `bonuses` (
	`BonusID`	TEXT,
	`BriefDesc`	TEXT,
	`Points`	INTEGER NOT NULL DEFAULT 1,
	`Cat1`	INTEGER NOT NULL DEFAULT 0,
	`Cat2`	INTEGER NOT NULL DEFAULT 0,
	`Cat3`	INTEGER NOT NULL DEFAULT 0,
	`Cat4`	INTEGER NOT NULL DEFAULT 0,
	`Cat5`	INTEGER NOT NULL DEFAULT 0,
	`Cat6`	INTEGER NOT NULL DEFAULT 0,
	`Cat7`	INTEGER NOT NULL DEFAULT 0,
	`Cat8`	INTEGER NOT NULL DEFAULT 0,
	`Cat9`	INTEGER NOT NULL DEFAULT 0,
	`Compulsory`	INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(`BonusID`)
);

CREATE TABLE IF NOT EXISTS "speedpenalties" (
	"Basis"	INTEGER NOT NULL DEFAULT 0,
	"MinSpeed"	INTEGER NOT NULL,
	"PenaltyType"	INTEGER NOT NULL DEFAULT 0,
	"PenaltyPoints"	INTEGER DEFAULT 0
);

DELETE FROM `rallyparams`;
DELETE FROM `functions`;
DELETE FROM `menus`;

DELETE FROM `certificates`;
DELETE FROM `speedpenalties`;
DELETE FROM `timepenalties`;
DELETE FROM `specials`;
DELETE FROM `sgroups`;
DELETE FROM `entrants`;
DELETE FROM `combinations`;
DELETE FROM `claims`;
DELETE FROM `categories`;
DELETE FROM `catcompound`;
DELETE FROM `bonuses`;


INSERT INTO `rallyparams` (RallyTitle,RallySlogan) VALUES ('IBA rally','Fun with motorcycles');

INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (1,'AdmEntrantChecks','entrants.php?c=entrants&amp;ord=EntrantID&amp;mode=check',NULL,'entrant,check-in/check-out');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (2,'AdmDoScoring','score.php',NULL,'entrant,score');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (3,'AdmRankEntries','admin.php?c=rank',NULL,'entrant,rank,finisher');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (4,'AdmPrintCerts','certificate.php?c=showcerts','window.open(''certificate.php?c=showcerts'',''certificates'');return false;','entrant,rank,finisher,certificate');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (5,'AdmShowSetup','admin.php?menu=setup',NULL,'setup');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (6,'AdmExportFinishers','exportxls.php?c=expfinishers','this.firstChild.innerHTML=FINISHERS_EXPORTED;','entrant,finisher,export');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (7,'AdmBonusTable','sm.php?c=bonuses',NULL,'bonus');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (8,'AdmSpecialTable','sm.php?c=specials',NULL,'bonus,special,penalty');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (9,'AdmSGroups','sm.php?c=sgroups',NULL,'bonus,special,group,penalty');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (10,'AdmCombosTable','sm.php?c=combos',NULL,'bonus,combo/combination');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (11,'AdmEntrants','entrants.php?c=entrants&amp;ord=EntrantID&amp;mode=full',NULL,'entrant');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (12,'AdmNewEntrant','entrants.php?c=newentrant',NULL,'entrant');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (13,'AdmDoBlank','score.php?c=blank&prf=1',NULL,'score,blank score sheet');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (14,'AdmRankEntries','admin.php?c=rank',NULL,'entrant,rank,finisher');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (15,'AdmImportEntrants','importxls.php?showupload',NULL,'entrant,import');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (16,'AdmRallyParams','sm.php?c=rallyparams',NULL,'params,rally');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (17,'AdmEditCert','admin.php?c=editcert',NULL,'entrant,certificate');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (18,'AdmEntrantsHeader','admin.php?menu=entrant',NULL,'entrant');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (19,'AdmBonusHeader','admin.php?menu=bonus',NULL,'bonus');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (20,'AdmTimePenalties','sm.php?c=timep',NULL,'params,time,penalty');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (21,'AdmCatTable','sm.php?c=showcat&axis=1',NULL,'params,category,compound');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (22,'AdmCompoundCalcs','sm.php?c=catcalcs',NULL,'params,category,compound');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (23,'AdmSetupWiz','setup.php',NULL,'params,category,compound');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (24,'AdmPrintScoreX','entrants.php?c=scorex','window.open(''entrants.php?c=scorex'',''scorex'');return false;','entrant,score,finisher');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (25,'AdmPrintQlist','entrants.php?c=qlist','window.open(''entrants.php?c=qlist'',''qlist'');return false;','entrant,rank,finisher');
INSERT INTO `functions` (functionid,menulbl,url,onclick,tags) VALUES (26,'UtlFolderMaker','utils.php','window.open(''utils.php'',''utils'');return false;','entrant,bonus,folder,directory,script');
INSERT INTO `functions` (functionid,menulbl,url,onclick,tags) VALUES (27,'UtlDeleteEntrant','entrants.php?c=delentrant',NULL,'entrant,delete entrant');
INSERT INTO `functions` (functionid,menulbl,url,onclick,tags) VALUES (28,'UtlRenumEntrant','entrants.php?c=moveentrant',NULL,'entrant,renumber entrant,entrant number,number');
INSERT INTO `functions` (functionid,menulbl,url,onclick,tags) VALUES (29,'UtlRAE','entrants.php?c=showrae',NULL,'entrant,renumber all entrants,entrant number,number');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (30,'AdmUtilHeader','admin.php?menu=util',NULL,'utilities,delete');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (31,'UtlFindEntrant','#','return findEntrant();','entrant,find');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (32,'AdmDoBlankB4','score.php?c=blank&prf=0',NULL,'score,blank score sheet');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (33,'ttTeams','teams.php?m=3&g=2',NULL,'teams,integrity');
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (34,'AdmSpeedPenalties','speeding.php',NULL,'speeding,penalties');

INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('admin','AdmMenuHeader','25,5,2,4,24,6');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('setup','AdmSetupHeader','16,17,18,19,20,34,21,22,23,30');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('entrant','AdmEntrantsHeader','1,11,12,2,15,24,31');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('bonus','AdmBonusHeader','7,8,9,10');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('util','AdmUtilHeader','29,28,27,26,32,13,33');





INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/* This gives acceptable results in both Chrome v60 and FireFox v55
 * but FireFox prints first certificate too far down the page.
 *
 * This is intended to use preprinted stationery rather than plain paper.
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}
body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 14pt;
	background: #fff;
	text-align: left;
	color: #000;
}
.certificate
{	/*                                     BBR / Jorvic - preprinted
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
        padding: 1mm 14mm 1mm 14mm; 
	border:  none; /*2mm double;*/
	margin-left:auto;
	margin-right:auto;
	margin-top: 0mm;
	margin-bottom: auto;
	page-break-after:always;
	position: relative;
	top: 30mm;
}
#topimagefiller
{
        margin-top:16em;
}
#hdrlogo
{
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 400px;
}
h1, h2, h3, p
{ 
	text-align: center; padding-top: 1em;
}
h1.RallyTitle 
{
	margin-top: 3em; 
}
h2, h3 
{ 
	margin-top: 1em; padding-top: 0; 
	
	
}
sup
{
	font-size: 80%;
}
p.main
{
    clear:both;
    margin-left: auto; margin-right: auto;
    text-align: center;
    padding-top: 2em;
    width: 100%;
}
p.rules
{
	font-size: 90%;
	font-style: italic;
                    text-align: justify;
}
p.footer
{
	font-size: 80%;
	clear: both;
	padding-top: 6em;
}
#signature
{
	margin-top: 6em;
	margin-left: auto;
	margin-right: auto;
	border-top: solid;
	padding-top: 0;
        width: 12em;
        text-align: center;
}
#signature1 
{
	margin-top: 6em;
	float: left;
	border-top: solid;
	padding-top: 0;
        width: 12em;
        text-align: center;
}
#signature2
{
	margin-top: 6em;
	float: right;
	border-top: solid;
	padding-top: 0;
        width: 12em;
        text-align: center;
        margin-right: 1em;
}
.CrewName
{
	font-weight: bold;
}
','<div id="topimagefiller"></div>
<h1 class="CrewName">#CrewName#</h1>
<h2 class="FinishPosition">#FinishPosition# place</h2>
<h3><span class="TotalPoints">#TotalPoints#</span> Points | #CorrectedMiles# Miles</h3>
<p class="main">Congratulations on your outstanding performance in the <br><br>2019 Jorvic Rally</p>
<p id="signature">Graeme Dawson<br>Rallymaster</p>

',NULL,NULL,0,'Rally finisher');


COMMIT;
