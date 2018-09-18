<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I contain all the text literals used throughout the system. If translation/improvement
 * is needed, this is the file to be doing it.
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


// Full/relative path to database file
$DBFILENAME = 'ScoreMaster.db';


// This array specifies labels and tootips for each onscreen field to avoid the need for 'literals in the procedure division'.
// This is in alphabetic order by key. It doesn't need to be but it makes your life easier, doesn't it?
// DO NOT alter key names!
$TAGS = array(
	'abtAuthor'			=> array('Author','Who developed this application'),
	'abtDatabase'		=> array('Database file','Full path to the file containing the database'),
	'abtHostname'		=> array('Hostname','Name of the computer hosting this application'),
	'abtHostOS'			=> array('HostOS','Details of the host\'s operating system'),
	'abtPHP'			=> array('PHP version',''),
	'abtSQLite'			=> array('SQLite version',''),
	'abtWebserver'		=> array('Webserver','What webserver software is hosting this'),
	'AddPoints'			=> array('Add points',''),
	'AddMults'			=> array('Add multipliers',''),
	'AdmBonusHeader'	=> array('Bonuses',''),
	'AdmBonusTable'		=> array('Ordinary bonuses','View/edit schedule of ordinary bonuses'),
	'AdmCatTable'		=> array('Categories','View/edit axis categories'),
	'AdmCombosTable'	=> array('Combinations','View/edit combination bonuses'),
	'AdmCompoundCalcs'	=> array('Compound calculations','Maintain table of calculation records'),
	'AdmDoScoring'		=> array('Scoring','Score individual entrants'),
	'AdmEditCert'		=> array('Edit certificate content','Edit the HTML &amp; CSS of the master certificate'),
	'AdmEntrants'		=> array('Entrants table','View/edit list of Entrants'),
	'AdmEntrantChecks'	=> array('Check-out/in','Entrant checks @ start/end of rally'),
	'AdmEntrantsHeader'	=> array('Entrants',''),
	'AdmExportFinishers'=> array('Export finishers','Save CSV containing details of finishers'),
	'AdmImportEntrants'	=> array('Import Entrants','Load entrant details from a spreadsheet'),
	'AdminMenu'			=> array('Rally Administration','Logon to carry out administration (not scoring) of the rally'),
	'AdmMenuHeader'		=> array('Rally administration',''),
	'AdmNewEntrant'		=> array('Setup new entrant','Add details of another entrant'),
	'AdmPrintCerts'		=> array('Print finisher certificates','Print certificates for finishers'),
	'AdmRallyParams'	=> array('Rally parameters','View/edit current rally parameters'),
	'AdmRankEntries'	=> array('Rank finishers','Calculate and apply the rank of each finisher'),
	'AdmSetupHeader'	=> array('Rally setup',''),
	'AdmSGroups'		=> array('Specials groups','Maintain groups of specials'),
	'AdmShowSetup'		=> array('Rally setup &amp; config','View/maintain rally configuration records'),
	'AdmSpecialTable'	=> array('Special bonuses','View/edit special bonuses'),
	'AdmTimePenalties'	=> array('Time penalties','Maintain table of time penalties'),
	
	'AskEnabledSave'	=> array('Save this scoresheet?',''),
	'AxisLit'			=> array('Axis',''),
	'BasicDetails'		=> array('Basic',''),
	'BasicRallyConfig'	=> array('Basic','Basic rally configuration fields'),
	'Bike'				=> array('Bike','Make &amp; model of bike'),
	'BikeReg'			=> array('Registration','Registration number of the bike if known'),
	'BonusesLit'		=> array('Bonuses','Ordinary bonuses'),
	'BonusIDLit'		=> array('BonusID',''),
	'BonusListLit'		=> array('Combination bonuses',''),
	'BonusMaintHead'	=> array('Ordinary Bonuses','List of Ordinary (geographic) bonuses'),
	'BonusPoints'		=> array('Points','The basic points value of this bonus'),
	'BriefDescLit'		=> array('Brief description',''),
	'CalcMaintHead'		=> array('Compound Calculation Rules','List of rules for compound score calculations'),
	'Cat1Label'			=> array('X-axis is','What do values on the X-axis represent?'),
	'Cat2Label'			=> array('Y-axis is','What do values on the Y-axis represent?'),	
	'Cat3Label'			=> array('Z-axis is','What do values on the Z-axis represent?'),
	'CatBriefDesc'		=> array('Description',''),
	'CategoryLit'		=> array('Category',''),
	'CatEntry'			=> array('Category','The number of this category within the axis'),
	'CatExplainer'		=> array('CatExplainer','You can amend the description of entries or delete them entirely. New entries must have an entry number which is unique within the axis.'),
	'CertificateHours'	=> array('Certificate hours','The duration of the rally in hours for the certificate'),
	'Class'				=> array('Class #','The certificate class applicable'),
	'ComboIDLit'		=> array('ComboID',''),
	'ComboMaintHead'	=> array('Combination Bonuses','List of Combination bonuses'),
	'CombosLit'			=> array('Combinations','Combination bonuses'),
	'CommaSeparated'	=> array('Comma separated list',''),
	'CompulsoryBonus'	=> array('Compulsory?','This bonus is required for Finisher status'),
	'CorrectedMiles'	=> array('Corrected miles','Official rally mileage'),
	'Country'			=> array('Country',"Entrant's home country"),
	'DeleteEntryLit'	=> array('Delete?',''),
	'EntrantDNF'		=> array('DNF','Did not qualify as a finisher'),
	'EntrantDNS'		=> array('DNS','Entrant failed to start the rally'),
	'EntrantFinisher'	=> array('Finisher','Rally finisher'),
	'EntrantID'			=> array('Entrant #','The unique reference for this Entrant'),
	'EntrantListCheck'	=> array('Entrant check-ins/outs','Choose an entrant for checkin-in or checking-out'),
	'EntrantListFull'	=> array('Full list of Entrants','Choose an entrant to view/edit his/her details'),
	'EntrantOK'			=> array('ok','Status normal'),
	'EntrantStatus'		=> array('Status','The current rally status of this entrant'),
	
							// Careful! this is executed as PHP, get it right.
	'EntrantStatusV'	=> array('array("0" => "DNS", "1" => "ok", "8" => "Finisher", "3" => "DNF");','array used for vertical tables'),
	'ExcessMileage'		=> array('Excess miles',''),
	'FetchCert'			=> array('Fetch certificate','Fetch the HTML, CSS &amp; options for this certificate'),
	'FinishDate'		=> array('Finish date','The last day of the rally'),
	'FinishersExported'	=> array('Finishers exported!','Finisher details exported to CSV'),
	'FinishPosition'	=> array('Final place','Finisher ranking position',''),
	'FinishTime'		=> array('Finish time','Official finish time'),
	'gblMainMenu'		=> array('Main menu','Return to main menu'),
	'GroupNameLit'		=> array('Special group','Group used for presentation purposes'),
	'HelpAbout'			=> array('About ScoreMaster',''),
	'InsertNewCC'		=> array('Enter new compound calc',''),
	'LegendPenalties'	=> array('Penalties',''),
	'LegendScoring'		=> array('Scoring &amp; Ranking',''),
	'LegendTeams'		=> array('Teams'),
	'login'				=> array('login','Go on, log me in then!'),
	'LogoutScorer'		=> array('Logout','Log the named scorer off this terminal'),
	'MaxMilesFixedM'	=> array('Multiplier','Excess mileage incurs deduction of multipliers'),
	'MaxMilesFixedP'	=> array('Fixed points','Excess mileage incurs fixed points deduction'),
	'MaxMilesPerMile'	=> array('Points per mile','Excess mileage incurs points deduction per excess mile'),
	'MaxMilesPoints'	=> array('Points or Multipliers deducted','Number of points or multipliers for excess mileage'),
	'MilesPenaltyText'	=> array('Mileage penalty deduction',''),
	'MinMiles'			=> array('Minimum miles','Minimum number of miles for finisher'),
	'MinPoints'			=> array('Minimum points','Minimum points scored to be a finisher'),
	'ModBonus0'			=> array('ModBonus0','Affects compound axis score'),
	'ModBonus1'			=> array('ModBonus1','Modifies bonus score'),
	'ModBonusLit'		=> array('Usage','1=This calc affects bonus value, 0=This calc affects axis score'),
	'NameFilter'		=> array('Rider name','Use this to filter the list of riders shown below'),
	'NewPlaceholder'	=> array('start new entry','Placeholder for new table entries'),
	'NMethod-1'			=> array('NMethod-1','Not used'),
	'NMethod0'			=> array('NMethod0','No of bonuses per cat'),
	'NMethod1'			=> array('NMethod1','No of NZ cats per axis'),
	'NMethodLit'		=> array('NMethod','0=# entries per cat, 1=# of NZ cats, -1=record not used'),
	'NMinLit'			=> array('NMin',''),
	'NoCerts2Print'		=> array('Sorry, no certificates to print.',''),
	'nowlit'			=> array('Now','Record the current date/time'),
	'NPowerLit'			=> array('NPower',''),
	'OdoCheckFinish'	=> array('Odo check finish','The odometer reading at the end of the odo check'),
	'OdoCheckMiles'		=> array('Odo check distance','The mileage used to check the accuracy of odometers'),
	'OdoCheckStart'		=> array('Odo check start','The reading at the start of the odometer check'),
	'OdoCheckTrip'		=> array('Odo check trip','What distance did the trip meter record?'),
	'OdoKms'			=> array('Odo counts',''),
	'OdoKmsK'			=> array('kilometres',''),
	'OdoKmsM'			=> array('miles',''),
	'Odometer'			=> array('Odo readings',''),
	'OdoRallyStart'		=> array('Start of rally','The reading at the start of the rally'),
	'OdoRallyFinish'	=> array('At end of rally','The odometer reading at the end of the rally'),
	'OdoScaleFactor'	=> array('Correction factor','The number to multiply odo readings to get true distance'),
	'OfferScore'		=> array('OfferScore','Would you like to help score this rally? If so, please tell me your name'),
	'PenaltyMaxMiles'	=> array('Max miles (penalties)','Mileage beyond this incurs penalties; 0=doesn\'t apply'),
	'PenaltyMilesDNF'	=> array('DNF mileage','Miles beyond here result in DNF;0=doesn\'t apply'),
	'PickAnEntrant'		=> array('Pick an entrant','Pick an entrant using the list below or by entering an Entrant number. Type a name to filter the list.'),
	'PillionFirst'		=> array('Informal name',"Used for repeat mentions on finisher's certificate"),
	'PillionIBA'		=> array('IBA #',"Pillion's IBA number if known"),
	'PillionName'		=> array('Pillion','Full name of the pillion rider'),
	'PointsMults'		=> array('Points/Mults',''),
	'PointsMults0'		=> array('PointsMults0','Points'),
	'PointsMults1'		=> array('PointsMults1','Multipliers'),
	'RallyResults'		=> array('Rally results',''),
	'RallySlogan'		=> array('Rally slogan','Brief description of the rally'),
	'RallyTitle'		=> array('Rally title','Formal title of the rally. Surround an optional part with []; Use | for newlines'),
	
	// Used as 'clear' line in claim reject popup menu
	'RejectReason0'		=> array('0=not rejected','Bonus claim is not rejected'),
	
	// These are actually held in the rallyparams table
	'RejectReason1'		=> array('1=Photo missing',''),
	'RejectReason2'		=> array('2=Photo wrong',''),
	'RejectReason3'		=> array('3=Photo unclear',''),
	'RejectReason4'		=> array('4=Out of hours',''),
	'RejectReason5'		=> array('5=Wrong info',''),
	'RejectReason6'		=> array('6=Reason 6',''),
	'RejectReason7'		=> array('7=Reason 7',''),
	'RejectReason8'		=> array('8=Reason 8',''),
	'RejectReason9'		=> array('9=Ask Rallymaster',''),
	
	'RiderFirst'		=> array('Informal name',"Used for repeat mentions on finisher's certificate"),
	'RiderIBA'			=> array('IBA #',"Rider's IBA number if known"),
	'RiderName'			=> array('Rider name','The full name of the rider'),
	'SaveCertificate'	=> array('Save certificate','Save the updated copy of this certificate'),
	'SaveEntrantRecord' => array('Save entrant details',''),
	'SaveNewCC'			=> array('Save new CC',''),
	'SaveRallyConfig'	=> array('Update rally configuration parameters',''),
	'SaveScore'			=> array('Save scorecard','Save the updated score/status of this entrant'),
	'ScoredBy'			=> array('Scored by','Who is (or did) scoring this entrant?'),
	'ScoreMethodLit'	=> array('Score method',''),
	'Scorer'			=> array('Scorer','Person doing the scoring'),
	'ScoreSaved'		=> array('Scorecard saved','This screen matches the database, no changes yet'),
	'ScoreThis'			=> array('Score this rider',''),
	'ScoringMethod'		=> array('Scoring method',''),
	'ScoringMethodA'	=> array('Automatic','The system will figure it out'),
	'ScoringMethodC'	=> array('Compound','Bonuses are ticked and point accrued by category'),
	'ScoringMethodM'	=> array('Manual','Entrant scores are entered manually as number of points'),
	'ScoringMethodS'	=> array('Simple','Bonuses are ticked and points added up'),
	'ScoringNow'		=> array('Being scored now','Is this entrant being scored by someone right now?'),
	'SGroupLit'			=> array('Specials Group','Specials group name'),
	'SGroupMaintHead'	=> array('Specials Bonus Groups','List of groups for specials'),
	'SGroupTypeLit'		=> array('Interface','Radio buttons or checkboxes'),
	'SGroupTypeC'		=> array('Checkbox','Checkboxes, multiple choices'),
	'SGroupTypeR'		=> array('Radio','Radio buttons, one choice'),
	'ShowEntrants'		=> array('Show entrant picklist','Return to entrant picklist'),
	'ShowMultipliers'	=> array('Show multipliers',''),
	'ShowMultipliersA'	=> array('Automatic','Let the system decide'),
	'ShowMultipliersN'	=> array('Hide','Don\'t show multipliers'),
	'ShowMultipliersY'	=> array('Show','Show multipliers scored'),
	'SMDesc'			=> array('ScoreMaster description','An application designed to make scoring &amp; administration of IBA style motorcycle rallies easy'),
	'SpecialMaintHead'	=> array('Special Bonuses','List of Special bonuses'),
	'SpecialMultLit'	=> array('Multipliers',''),
	'SpecialPointsLit'	=> array('Points',''),
	'SpecialsLit'		=> array('Specials','Special bonuses'),
	'StartDate'			=> array('Start date','The first day of the rally'),
	'StartTime'			=> array('Start time','Official start time'),
	'TeamID'			=> array('Team #','The team number this Entrant is a member of'),
	'TeamRankingH'		=> array('Highest ranked member','Rank team as highest member'),
	'TeamRankingI'		=> array('Individual placing','Rank each team member separately'),
	'TeamRankingL'		=> array('Lowest ranked member','Rank team as lowest member'),
	'TeamRankingText'	=> array('Teams are ranked according to',''),
	'TiedPointsRanking'	=> array('Split ties by mileage','In the event of a tie entrants will be ranked by mileage'),
	'TimepMaintHead'	=> array('Time Penalties','List of time penalty entries'),

	// time penalties
	'tpFactorLit'		=> array('Number','Number of points/mults'),
	'tpFinishLit'		=> array('Finish time','Time this penalty ends'),
	'tpMethod0'			=> array('tpMethod0','Deduct points'),
	'tpMethod1'			=> array('tpMethod1','Deduct multipliers'),
	'tpMethod2'			=> array('tpMethod2','Points per minute'),
	'tpMethod3'			=> array('tpMethod3','Mults per minute'),
	'tpMethodLit'		=> array('Penalty method','Which penalty method applies'),
	'tpStartLit'		=> array('Start time','Time this penalty starts from'),

	'TotalMults'		=> array('Total multipliers','The number of multipliers applied compiling the total score'),
	'TotalPoints'		=> array('Total points','Final rally score'),
	'unset'				=> array('unset, empty, null',''),
	'unused'			=> array('unused',''),
	'UpdateAxis'		=> array('Update these records',''),
	'UpdateBonuses'		=> array('Update bonuses',''),
	'UpdateCategory'	=> array('Update category',''),
	'UpdateCCs'			=> array('Update compound calcs',''),
	'UpdateSGroups'		=> array('Update special groups',''),
	'UpdateTimeP'		=> array('Update time penalties',''),
	
	'Upload'			=> array('Upload','Upload the file to the server'),
	'UploadEntrantsH1'	=> array('Uploading Entrants','Upload Entrants data from spreadsheet'),
	'UploadForce'		=> array('Force overwrite','Overwrite existing Entrant records'),
	'UploadPickFile'	=> array('Pick a file','Please select the input file'),
	
	'xlsImporting'		=> array('Importing','Importing entrants data from spreadsheet'),
	'xlsNoSpecfile'		=> array('!specfile','No "specfile" parameter supplied'),
	'xlsNotEmpty'		=> array('Entrants already setup!','The table of entrants is not empty, please tick override and retry'),
	
	'ZapDatabaseOffer'	=> array('Zap / Reinitialize Database','Clear the database ready to start from scratch'),
	'ZapDatabaseZapped'	=> array('Database Zapped/Initialized','The database is empty and ready to start from scratch'),
	
						/* Index 1 used as html content of P, pay attention */
	'ZapDBCaution'		=> array('BEWARE!','This will empty the database of all content except certificate templates. The rally database must then be setup from scratch.'),
						/* Index 1 used as default values in database, beware SQL */
	'ZapDBRallySlogan'	=> array('Toughest Motorcycle Rally','Toughest Motorcycle Rally'),
	'ZapDBRallyTitle'	=> array('IBA Motorcycle Rally','IBA Motorcycle Rally'),
	
	'ZapDBGo'			=> array('Go ahead, Zap the lot!','Execute the zap command'),
	
						/* Index 0 is the truth value of the checkbox, Index 1 is the associated question */
	'ZapDBRUSure1'		=> array('yESiMsURE','I am absolutely sure I want to do this'),
	'ZapDBRUSure2'		=> array('ImReallySure','Quite, quite definitely'),
	'ZapDBRUCancel'		=> array('NoIWont','I don\'t really mean this'),
	
	
	'zzzzzzzzzz'		=> array('zzz','dummy to mark end of array')
	);


	
	
// Uninteresting values
$KONSTANTS['MaxMilesFixedP'] = 0;
$KONSTANTS['MaxMilesFixedM'] = 1;
$KONSTANTS['MaxMilesPerMile'] = 2;
$KONSTANTS['ManualScoring'] = 0;
$KONSTANTS['SimpleScoring'] = 1;
$KONSTANTS['CompoundScoring'] = 2;
$KONSTANTS['AutoScoring'] = 3;
$KONSTANTS['SuppressMults'] = 0;
$KONSTANTS['ShowMults'] = 1;
$KONSTANTS['AutoShowMults'] = 2;
$KONSTANTS['TiedPointsSplit'] = 1;
$KONSTANTS['RankTeamsAsIndividuals'] = 0;	
$KONSTANTS['RankTeamsHighest'] = 1;
$KONSTANTS['RankTeamsLowest'] = 2;
$KONSTANTS['DistanceIsMiles'] = 0;
$KONSTANTS['DistanceIsKilometres'] = 1;
$KONSTANTS['OdoCountsMiles'] = 0;
$KONSTANTS['OdoCountsKilometres'] = 1;
$KONSTANTS['EntrantDNS'] = 0;
$KONSTANTS['EntrantOK'] = 1;
$KONSTANTS['EntrantFinisher'] = 8;
$KONSTANTS['EntrantDNF'] = 3;
$KONSTANTS['BeingScored'] = 1;
$KONSTANTS['NotBeingScored'] = 0;
$KONSTANTS['TiesSplitByMiles'] = 1;
$KONSTANTS['TeamRankIndividuals'] = 0;
$KONSTANTS['TeamRankHighest'] = 1;
$KONSTANTS['TeamRankLowest'] = 2;
// Beware, these next two used for combinations & catcompounds
$KONSTANTS['ComboScoreMethodPoints'] = 0;
$KONSTANTS['ComboScoreMethodMults'] = 1;

/* Each simple bonus may be classified using
 * this number of categories. This reflects 
 * the database structure, it may not be
 * arbitrarily increased.
 */
$KONSTANTS['NUMBER_OF_COMPOUND_AXES'] = 3;


/*
 * This next constant determines whether the basic unit of distance used
 * by this application is the mile or the kilometre. The field names on
 * the database remain as 'miles' or 'mileage' as they're only used 
 * internally but calculations switching between miles and kilometres
 * are affected by this setting. Field labels and tooltips must also
 * be altered manually. It is assumed that a particular instance of this 
 * application will always use the same unit of measure.
 */
 
$KONSTANTS['BasicDistanceUnits'] = $KONSTANTS['DistanceIsMiles'];
// $KONSTANTS['BasicDistanceUnits'] = $KONSTANTS['DistanceIsKilometres'];




// Default settings

// This setting should normally reflect BasicDistanceUnits above
$KONSTANTS['DefaultKmsOdo'] = $KONSTANTS['OdoCountsMiles']; 
// $KONSTANTS['DefaultKmsOdo'] = $KONSTANTS['OdoCountsKilometres'];

$KONSTANTS['DefaultOdoScaleFactor'] = 1;
$KONSTANTS['DefaultCountry'] = 'UK';
$KONSTANTS['DefaultEntrantStatus'] = $KONSTANTS['EntrantOK'];



	
	
// Open the database	
try
{
	$DB = new SQLite3($DBFILENAME);
} catch(Exception $ex) {
	echo("OMG ".$ex->getMessage().' file=[ '.$DBFILENAME.' ]');
}

$RALLY_INITIALISED = (1==1);
$HTML_STARTED = false;

// Common subroutines

function properName($enteredName)
// Used to fix names entered online; not everyone knows about shift keys
// If they've tried, I just return what they entered but if not I'll
// return initial capitals followed by lowercase
{
	$x = explode(' ',$enteredName);
	$z = false;
	for ($i = 0; $i < sizeof($x); $i++)
		if (ctype_lower($x[$i]) || ctype_upper($x[$i]))
			$z = true;
	if ($z)
		return ucwords(strtolower($enteredName));
	else
		return $enteredName;
	
}

function splitDatetime($dt)
/* Accept either 'T' or space as splitting date/time */
{
	if (strpos($dt,'T'))
		$S = 'T';
	else if (strpos($dt,' '))
		$S = ' ';
	else
		return ['','']; 
	
	$dtx = explode($S,$dt);
	return $dtx;
		
}

function getValueFromDB($sql,$col,$defaultvalue)
{
	global $DB;
	
	try {
		$R = $DB->query($sql);
		if ($rd = $R->fetchArray())
			return $rd[$col];
		else
			return $defaultvalue;
	} catch (Exception $ex) {
		return $defaultvalue;
	}
}

function startHtml($otherInfo = '')
{
	global $DB, $TAGS, $KONSTANTS, $HTML_STARTED;
	global $HOME_URL;
	
	if ($HTML_STARTED)
		return;
	
	$HTML_STARTED = true;
	
	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>ScoreMaster</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
body, input, select		{ 
    font-family: Arial, Helvetica, sans-serif; font-size: calc(14pt + 1vmin);  background-color: #11E3FF; /*color: #B25D00;*/ }

input,select			{ color: #000; font-weight: bold; }
input[type="number"], .number	{ width:4em; }
input[type="date"]		{ width: 10em; }
input:read-only			{ border: none; }
input.wide				{ width: 12em; }
input[type='submit']	{
	background-color: #00ff48;
    border: none;
    border-radius: 10px;
    color: #b20000;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.6em;
	font-weight: bold;
    margin: 4px 2px;
    cursor: pointer;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}
input[type='submit']:disabled	{
	background-color: #1EB206;
    border: none;
    border-radius: 10px;
    color: #b20000;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.6em;
	font-weight: bold;
    margin: 4px 2px;
    cursor: pointer;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}

label 					{ white-space: nowrap; }
caption					{ margin-bottom: 1em; }
span.vlabel				{ display: block; padding-top: 1em;}
span.vlabel label		{ text-align: right; width: 8em; display: inline-block; }
span.vlabel label.wide	{ text-align: right; width: 12em; display: inline-block; }
span.xlabel:before		{ content: '\A\A'; white-space: pre; }
input[name="BikeReg"]	{ width: 6em; }
select[name="EntrantStatus"]	{ margin-bottom: 1em; }
fieldset				{ margin-bottom: 1em; }
input[name=FinishPosition]	{ width: 3em; }
.link:hover				{ cursor: pointer; background-color: lightgray;}

/* Tabbed interface */
ul#tabs { list-style-type: none; margin: 30px 0 0 0; padding: 0 0 0.3em 0; }
ul#tabs li { display: inline; }
ul#tabs li a { color: #42454a; background-color: #dedbde; border: 1px solid #c9c3ba; border-bottom: none; padding: 0.3em; text-decoration: none;  

	border-top-left-radius: 10px; border-top-right-radius: 10px;

    background:      -o-linear-gradient(to top, #ECECEC 50%, #D1D1D1 100%);
    background:     -ms-linear-gradient(to top, #ECECEC 50%, #D1D1D1 100%);
    background:    -moz-linear-gradient(to top, #ECECEC 50%, #D1D1D1 100%);
    background: -webkit-linear-gradient(to top, #ECECEC 50%, #D1D1D1 100%);
    background: linear-gradient(to top, #ECECEC 50%, #D1D1D1 100%);
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.4), inset 0 1px 0 #FFF;
    text-shadow: 0 1px #FFF;
    margin: 0 5px;
    padding: 0 2px;



}
ul#tabs li a:hover { background-color: #f1f0ee; }
ul#tabs li a.selected { color: #000; background-color: #f1f0ee; font-weight: bold; padding: 0.7em 0.3em 0.38em 0.3em; }
.tabContent { border: 10px solid #c9c3ba;  padding: 0.5em;  display:inherit; }
.tabContenthide { display: none; }
.tabcell	{ display: table-cell; }

.compulsory		{ font-weight: bold; }
.techie			{ font-size: .6em; } /* For largely uninteresting items */
.slogan			{ font-size: .8em; font-style: italic; text-align: center; }

#header			{ background-color: lightgray; border-bottom: solid; }
#hdrRallyTitle 	{ padding-left: 1em; }
#hdrOtherInfo 	{ padding-right: 1em; float: right; }
#frontpage		{ margin-top: 3em; margin-left: 3em; font-size: 1.3em; }
#ScoreHeader 	{ margin-top: 1em; padding-bottom: .5em; border-bottom: solid;  max-width: 100%; }
#ScoreHeader span { margin-left: 1em; margin-right: 1em; white-space: nowrap; }

#ScoreHeader.manualscoring span:before
				{ content: '\A\A'; white-space: pre; }

span.keep		{ white-space: nowrap; display: inline-block; }

#footer			{ background-color: lightgray; border-top: solid; position: fixed; bottom: 0; margin-bottom: .1em; width: 98%;}
#ftrAdminMenu	{ padding-left: 1em; padding-right: 1em; float: right; }

.menulist		{ list-style-type: none; }

#sgroups		{ margin-top: 2em; }
#sgroups td		{ padding-bottom: 2em; padding-right: 2em; }

#adminMM		{ margin-left: auto; margin-right: auto; width: 12em;}
#adminMM *		{ display: inline-block; width: 100%; text-align: center; margin-left: auto; margin-right: auto; padding-left: 0; padding-right: 0; }
#adminMM a		{ text-decoration: none; }
#adminMM a:hover{ background: lightgray; }
#adminMM a:visited { color: #b20000; /* #FF0076; */ }
#adminMM a:link	{
	background-color: #00ff48; /*#2BFF2F;*/
    border: none;
    border-radius: 10px;
    color: #b20000;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 0.6em;
	font-weight: bold;
    margin: 4px 2px;
    cursor: pointer;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}

table#entrants	th.EntrantID	{ text-align: left; }
table#entrants	td.EntrantID	{ text-align: center; }
table#entrants	.RiderName,.PillionName,.Bike	{ text-align: left; }
table#entrants	.FinishPosition,.TotalPoints,.CorrectedMiles	{ text-align: center; }


#header a		{ text-decoration: none; }
#header a:link	{ color: black; }
#header a:visited	{ color: black; }

#footer a		{ text-decoration: none; }
#footer a:link	{ color: black; }
#footer a:visited	{ color: black; }

/* Use different background colour for output only items */
#tab_combos		{ background-color: #66D2FF; margin-top: .8em; } 

#cat_results	{ background-color: #66D2FF; }
#cat_results td.catdesc { width: 6em; text-align: right; padding-right: .5em; }
#cat_results caption { border-bottom: solid; margin-bottom: .1em; font-style: italic; }
#cat1			{ background-color: inherit; float:left; display: block; padding-left: .5em; padding-right: .5em; font-size:.8em; }
#cat2			{ margin-left: .2em; background-color: inherit; float:left; display: block; padding-left: .1em; padding-right: .5em;  font-size:.8em; }
#cat3			{ margin-left: .2em; background-color: inherit; float:left; display: block; padding-left: .1em; padding-right: .5em;  font-size:.8em; }

/* Score explanation */
#scorex			{  }
#scorex table,caption	{ background-color: #66D2FF; margin-left: auto; margin-right: auto; }
#scorex	caption	{ border: solid; padding: .5em; }
#scorex	tr:last-of-type td 	{ border-top: solid; }
#scorex .bp		{ padding-left:1em; padding-right:.3em; text-align: right; }
#scorex .bm		{ padding-left:1em; padding-right:.3em; text-align: right; }
#scorex .tp		{ padding-left:1em; padding-right:.3em; text-align: right; }
.hidescorex		{ display:none; }
.showscorex		{ display:inherit; }

.clickme		{ cursor: pointer; }

/* Help about */

#helpabout		{ margin-left: auto; margin-right: auto; max-width: 40em; border: solid; padding:.2em; margin-top:1em; background-color: white; font-size:.8em;}
#helpabout h1	{ text-align:center; }
#helpabout dd	{ font-weight: bold; }



/* Right-click claim reject menu */
#rcmenu { 
	border:solid 1px #CCC; 
	position: absolute; 
	z-index: 10; 
	background-color: lightgray; 
	width: 8em; 
}
#rcmenu ul { list-style-type: none; margin: 0; padding: 0; font-size: .75em; }
#rcmenu li { border-bottom:solid 1px #CCC; }
#rcmenu li:last-child { border:none; }
#rcmenu li a {
    display:block;
    text-decoration:none;
    color:blue;
	 padding-left: .5em; padding-right: .5em; 
}
#rcmenu li a:hover {
    background:blue;
	cursor: pointer; 
    color:#FFF;
}

/* Used to specifically style three bonus states */
.showbonus		{ white-space: nowrap; display: inline-block; padding-left: 2px; padding-right: 2px; }
.rejected,.rejected > *		{background-color: red; color: white; }
.checked,.checked > * 		{background-color: #31ad16; color: white; }
.unchecked,.unchecked > * 	{background-color: inherited; color: inherited; }

@media print {
.noprint		{ display: none; }
}
</style>
<script src="score.js?ver=<?= filemtime('score.js')?>" defer="defer"></script>
</head>
<body onload="bodyLoaded();">
<?php echo('<input type="hidden" id="BasicDistanceUnits" value="'.$KONSTANTS['BasicDistanceUnits'].'"/>'); ?>
<div id="header">
<?php	
	echo("<a href=\"".$HOME_URL);
	if (isset($_REQUEST['ScorerName']))
	{
		$scorer = $_REQUEST['ScorerName'];
		if ($scorer <> '')
			echo("?ScorerName=$scorer&amp;clear");
	}
	echo("\">");
	echo('<span id="hdrRallyTitle" title="'.htmlspecialchars($TAGS['gblMainMenu'][1]).'"> '.htmlspecialchars(preg_replace('/\[|\]|\|/','',$rd['RallyTitle'])).' </span>');
	echo("</a>");
	echo('<span id="hdrOtherInfo">'.$otherInfo.'</span>');
	echo("\r\n</div>\r\n");
	
}
function showFooter()
{
	global $DB, $TAGS;
	echo('<div id="footer">');
	echo('<span id="ftrAdminMenu" title="'.$TAGS['AdminMenu'][1].'"><a href="admin.php">'.$TAGS['AdminMenu'][0].'</a></span></div>');
	echo('</body></html>');
}
?>

