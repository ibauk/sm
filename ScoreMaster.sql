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
	`ScoringMethod`	INTEGER DEFAULT 0,
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

INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('admin','AdmMenuHeader','1,2,3,4,24,5,6');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('setup','AdmSetupHeader','16,17,18,19,20,21,22');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('entrant','AdmEntrantsHeader','1,11,12,2,13,15,24');
INSERT INTO `menus` (menuid,menulbl,menufuncs) VALUES ('bonus','AdmBonusHeader','7,8,9,10');



/*
 * C E R T I F I C A T E S
 *
 * The first of these is the 'standard' rally template, the rest are RBLR1000 variants
 *
 */





INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 2 NCW -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Fort William, Wick and Edinburgh before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,2,'RBLR 1000 NCW');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/* This gives acceptable results in both Chrome v60 and FireFox v55
 * but FireFox prints first certificate too far down the page.
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
{	/* 
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
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 1 NAC -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Edinburgh, Wick and Fort William before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,1,'RBLR 1000 NAC');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 7 500AC -->
<img class="header_badge" src="images/route500AC.jpg" alt="Iron Butt Association Ride Certificate" />
<p>500 miles in less than 24 hours</p>
</div>
<div class="citation">
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 504 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Beverly, Berwick and Millom before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>This ride was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/rblr.png" alt="" style="float:right;padding-top:0em;"/>
<img src="images/poppy.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,7,'RBLR 500 AC');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 6 500CW -->
<img class="header_badge" src="images/route500CW.jpg" alt="Iron Butt Association Ride Certificate" />
<p>500 miles in less than 24 hours</p>
</div>
<div class="citation">
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 504 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Millom, Berwick and Beverly before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>This ride was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/rblr.png" alt="" style="float:right;padding-top:0em;"/>
<img src="images/poppy.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,6,'RBLR 500 CW');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 5 BBG -->
<img class="header_badge" src="images/bbg1500.png" alt="Iron Butt Association Ride Certificate" />
<p>1,500 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,527 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Folkestone, Swansea and Perth before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The BunBurner Gold extreme ride was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,5,'RBLR BBG');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 3 SAC -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em;/>
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Bangor, Brighton and Lowestoft before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,3,'RBLR 1000 SAC');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 4 SCW -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Lowestoft, Brighton and Bangor before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,4,'RBLR 1000 SCW');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 8 Cert NAC -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles starting in Squires cafe, Yorkshire continuing onto Edinburgh, Wick and Fort William before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,8,'RBLR cert NAC');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 9 Cert NCW -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles starting in Squires cafe, Yorkshire continuing onto Fort William, Wick and Edinburgh before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,9,'RBLR cert NCW');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 10 Cert SAC -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles starting in Squires cafe, Yorkshire continuing onto Bangor, Brighton and Lowestoft before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,10,'RBLR cert SAC');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 11 Cert SCW -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles starting in Squires cafe, Yorkshire continuing onto Bangor, Brighton and Lowestoft before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,11,'RBLR cert SCW');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 12 BB -->
<img class="header_badge" src="images/bb1500.jpg" alt="Iron Butt Association Ride Certificate" />
<p>1,500 miles in less than 36 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #RiderName# rode a #Bike# a total of 1,527 gruelling miles in less than 36 hours starting in Squires cafe, Yorkshire continuing onto Folkestone, Swansea and Perth before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The BunBurner ride was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,12,'RBLR bb1500');
INSERT INTO `certificates` (EntrantID,css,html,options,image,Class,Title) VALUES (0,'/*
 * ridecertsm.css
 *
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
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.5cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 14mm 5mm 14mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 15mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 13 FSB -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Folkestone, Swansea and Bodmin before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,13,'RBLR 1000 FSB');
COMMIT;