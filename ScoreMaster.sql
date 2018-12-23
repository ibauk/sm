/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I provide the initial database when building new rallies.
 *
 * I am written for readability rather than efficiency, please keep me that way.
 *
 *
 * Copyright (c) 2018 Bob Stammers
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

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS `rallyparams` (
	`RallyTitle`	TEXT,
	`RallySlogan`	TEXT,
	`CertificateHours`	INTEGER DEFAULT 36,
	`StartTime`	TEXT,
	`FinishTime`	TEXT,
	`MinMiles`	INTEGER DEFAULT 0,
	`PenaltyMaxMiles`	INTEGER DEFAULT 0,
	`MaxMilesMethod`	INTEGER DEFAULT 0,
	`MaxMilesPoints`	INTEGER DEFAULT 0,
	`PenaltyMilesDNF`	INTEGER DEFAULT 0,
	`MinPoints`	INTEGER DEFAULT 0,
	`ScoringMethod`	INTEGER DEFAULT 1,
	`ShowMultipliers`	INTEGER DEFAULT 2,
	`TiedPointsRanking`	INTEGER DEFAULT 0,
	`TeamRanking`	INTEGER DEFAULT 0,
	`OdoCheckMiles`	NUMERIC DEFAULT 20,
	`Cat1Label`	TEXT DEFAULT 'Cat1',
	`Cat2Label`	TEXT DEFAULT 'Cat2',
	`Cat3Label`	TEXT DEFAULT 'Cat3',
	`RejectReasons`	TEXT DEFAULT "1=Photo missing
2=Photo wrong
3=Photo unclear
4=Out of hours
5=Wrong info
6=Reason 6
7=Reason 7
8=Reason 8
9=RallyMaster",
	`DBState` INTEGER NOT NULL DEFAULT 0,
	`DBVersion` INTEGER NOT NULL DEFAULT 1
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
	`EntrantID`	INTEGER DEFAULT 0,
	`css`	TEXT,
	`html`	TEXT,
	`options`	TEXT,
	`image`	TEXT,
	`Class`	INTEGER DEFAULT 0,
	`Title`	TEXT,
	PRIMARY KEY(`EntrantID`,`Class`)
);


CREATE TABLE IF NOT EXISTS `timepenalties` (
	`PenaltyStart`	TEXT,
	`PenaltyFinish`	TEXT,
	`PenaltyMethod`	INTEGER DEFAULT 0,
	`PenaltyFactor`	INTEGER DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `specials` (
	`BonusID`	TEXT,
	`BriefDesc`	TEXT,
	`GroupName`	TEXT,
	`Points`	INTEGER DEFAULT 0,
	`MultFactor`	INTEGER DEFAULT 0,
	`Compulsory`	INTEGER DEFAULT 0,
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
	`TeamID`	INTEGER DEFAULT 0,
	`Country`	TEXT DEFAULT 'UK',
	`OdoKms`	INTEGER DEFAULT 0,
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
	`TotalPoints`	INTEGER DEFAULT 0,
	`StartTime`	TEXT,
	`FinishPosition`	INTEGER DEFAULT 0,
	`EntrantStatus`	INTEGER DEFAULT 1,
	`ScoringNow`	INTEGER DEFAULT 0,
	`ScoredBy`	TEXT,
	`ExtraData`	TEXT,
	`Class`	INTEGER DEFAULT 0,
	`ScoreX`	TEXT,
	`RejectedClaims`	TEXT,
	PRIMARY KEY(`EntrantID`)
);
CREATE TABLE IF NOT EXISTS `combinations` (
	`ComboID`	TEXT,
	`BriefDesc`	TEXT,
	`ScoreMethod`	INTEGER DEFAULT 0,
	`ScorePoints`	INTEGER DEFAULT 0,
	`Bonuses`	TEXT,
	`Compulsory`	INTEGER DEFAULT 0,
	PRIMARY KEY(`ComboID`)
);
CREATE TABLE IF NOT EXISTS `claims` (
	`EntrantID`	INTEGER,
	`BonusID`	TEXT,
	`ClaimTime`	TEXT,
	`ClaimStatus`	INTEGER DEFAULT 0,
	`Reason`	INTEGER DEFAULT 0,
	`StatusChanged`	TEXT
);

CREATE TABLE IF NOT EXISTS `categories` (
	`Axis`	INTEGER DEFAULT 1,
	`Cat`	INTEGER,
	`Entry`	INTEGER,
	`BriefDesc`	TEXT,
	PRIMARY KEY(`Axis`,`Cat`)
);
CREATE TABLE IF NOT EXISTS `catcompound` (
	`Axis`	INTEGER DEFAULT 1,
	`NMethod`	INTEGER DEFAULT -1,
	`ModBonus`	INTEGER DEFAULT 0,
	`NMin`	INTEGER DEFAULT 1,
	`PointsMults`	INTEGER DEFAULT 0,
	`NPower`	INTEGER DEFAULT 2
);
CREATE TABLE IF NOT EXISTS `bonuses` (
	`BonusID`	TEXT,
	`BriefDesc`	TEXT,
	`Points`	INTEGER DEFAULT 1,
	`Cat1`	INTEGER DEFAULT 0,
	`Cat2`	INTEGER DEFAULT 0,
	`Cat3`	INTEGER DEFAULT 0,
	`Compulsory`	INTEGER DEFAULT 0,
	PRIMARY KEY(`BonusID`)
);


DELETE FROM `rallyparams`;
DELETE FROM `functions`;
DELETE FROM `menus`;

DELETE FROM `certificates`;
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
INSERT INTO `functions` (functionid,menulbl,url,onclick,Tags) VALUES (13,'AdmDoBlank','score.php?c=blank',NULL,'score,blank score sheet');
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

INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('admin','AdmMenuHeader','1,2,3,4,24,5,6,25');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('setup','AdmSetupHeader','16,17,18,19,20,21,22');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('entrant','AdmEntrantsHeader','1,11,12,2,13,15,24');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('bonus','AdmBonusHeader','7,8,9,10');





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
	width: 160mm;
	height: 25.5cm;
    padding: 1mm 14mm 1mm 14mm; 
	border:  none; /*2mm double;*/
	margin-left:auto;
	margin-right:auto;
	margin-top: 10mm;
	margin-bottom: auto;
	page-break-after:always;
	position: relative;
	top: 40mm;
}
h1, h2, p
{ 
	text-align: center; padding-top: 1em;
}
h1.RallyTitle 
{
	margin-top: 3em; 
}
h2 
{ 
	margin-top: 0; padding-top: 0; 
	font-size: 80%;
	font-style: italic;
}
sup
{
	font-size: 80%;
}
p.main
{
    text-align: justify;
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
#signature1 
{
	margin-top: 8em;
	float: left;
	border-top: solid;
	padding-top: 0;
}
#signature2
{
	margin-top: 8em;
	float: right;
	border-top: solid;
	padding-top: 0;
}
.CrewName
{
	font-weight: bold;
}
','<h1 class="RallyTitle">#RallyTitleSplit#</h1>
<h2 class="RallySlogan">#RallySlogan#</h2>
<h1 class="FinishPosition">#FinishPosition# place</h1>
<p class="main">This is to certify that on the <span class="DateRallyRange">#DateRallyRange#</span>, <span class="CrewName">#CrewName#</span> rode a <span class="Bike">#Bike#</span> throughout Yorkshire, within <span class="CertificateHours">#CertificateHours#</span> hours. <span class="CrewFirst">#CrewFirst#</span>  accrued a total of <span class="TotalPoints">#TotalPoints#</span> bonus points on the way to finishing in <span class="FinishPosition">#FinishPosition#</span> place in the <span class="RallyTitle">#RallyTitle#</span>. An outstanding achievement!</p>

<p class="rules">The <span class="RallyTitle">#RallyTitle#</span> was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the country and beyond have managed to solve the challenges such a gruelling ride involves.</p>
<p id="signature1"><strong>Graeme Dawson</strong><br>Rally Master, #RallyTitleShort#</p>
<p id="signature2"><strong>John Cunniffe</strong><br>Rally Master, #RallyTitleShort#</p>
<p class="footer">The Iron Butt Association is dedicated to safe long-distance motorcycle riding</p>',NULL,NULL,0,'Rally finisher');


COMMIT;
