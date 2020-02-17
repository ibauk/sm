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
 * Copyright (c) 2019 Bob Stammers
 *
 *
 * This file is part of IBAUK-SCOREMASTER.
 *
 * IBAUK-SCOREMASTER is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License
 *
 * IBAUK-SCOREMASTER is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * MIT License for more details.
 *
 */


$KONSTANTS['DistanceIsMiles'] = 0;
$KONSTANTS['DistanceIsKilometres'] = 1;
$KONSTANTS['OdoCountsMiles'] = 0;
$KONSTANTS['OdoCountsKilometres'] = 1;

require_once('customvars.php');
	
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
$KONSTANTS['AutoRank'] = 1;
$KONSTANTS['AutoShowMults'] = 2;
$KONSTANTS['TiedPointsSplit'] = 1;
$KONSTANTS['RankTeamsAsIndividuals'] = 0;	
$KONSTANTS['RankTeamsHighest'] = 1;
$KONSTANTS['RankTeamsLowest'] = 2;
$KONSTANTS['EntrantDNS'] = 0;
$KONSTANTS['EntrantOK'] = 1;
$KONSTANTS['EntrantFinisher'] = 8;
$KONSTANTS['EntrantDNF'] = 3;
$KONSTANTS['BeingScored'] = 1;
$KONSTANTS['NotBeingScored'] = 0;
$KONSTANTS['AreYouSureYes'] = 'yesIamSure';
$KONSTANTS['TiesSplitByMiles'] = 1;
$KONSTANTS['TeamRankIndividuals'] = 0;
$KONSTANTS['TeamRankHighest'] = 1;
$KONSTANTS['TeamRankLowest'] = 2;
$KONSTANTS['TimeSpecDatetime'] = 0;
$KONSTANTS['TimeSpecRallyDNF'] = 1;
$KONSTANTS['TimeSpecEntrantDNF'] = 2;
$KONSTANTS['BCM_UNKNOWN'] = 0;
$KONSTANTS['BCM_EBC'] = 1;
$KONSTANTS['BCM_PAPER'] = 2;

// Beware, these next two used for combinations & catcompounds
$KONSTANTS['ComboScoreMethodPoints'] = 0;
$KONSTANTS['ComboScoreMethodMults'] = 1;


$KONSTANTS['DefaultOdoScaleFactor'] = 1;
$KONSTANTS['DefaultEntrantStatus'] = $KONSTANTS['EntrantOK'];




// Common subroutines below here; nothing translateable below
	
	
// Open the database	
try
{
	$DB = new SQLite3($DBFILENAME);
} catch(Exception $ex) {
	echo("OMG ".$ex->getMessage().' file=[ '.$DBFILENAME.' ]');
}
$DBVERSION = getValueFromDB("SELECT DBVersion FROM rallyparams","DBVersion",0);

if ($DBVERSION >= 4)
	$AUTORANK =  getValueFromDB("SELECT AutoRank FROM rallyparams","AutoRank",0);
else
	$AUTORANK = 0;

/* Each simple bonus may be classified using
 * this number of categories. This reflects 
 * the database structure, it may not be
 * arbitrarily increased.
 */
 
if ($DBVERSION < 3)
	$KONSTANTS['NUMBER_OF_COMPOUND_AXES'] = 3;
else
	$KONSTANTS['NUMBER_OF_COMPOUND_AXES'] = 9; // 9




$RALLY_INITIALISED = (1==1);
$HTML_STARTED = false;

// Common subroutines

function popBreadcrumb()
{
	if (!isset($_REQUEST['breadcrumbs']))
		return;
	$bc = strrpos($_REQUEST['breadcrumbs'],';');
	if (!$bc)
		return;
	$_REQUEST['breadcrumbs'] = substr($_REQUEST['breadcrumbs'],0,$bc);
	
}

function pushBreadcrumb($step)
{
	$bchome = "<a href='".'admin.php'."'> / </a>";

	if (!isset($_REQUEST['breadcrumbs']))
		$_REQUEST['breadcrumbs'] = $bchome;
	$_REQUEST['breadcrumbs'] .= ";".$step;
		
}

function emitBreadcrumbs()
{
	echo('<input type="hidden" name="breadcrumbs" id="breadcrumbs" value="'.$_REQUEST['breadcrumbs'].'">');
}

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
		return ucwords(strtolower(str_replace('  ',' ',$enteredName)));
	else
		return str_replace('  ',' ',$enteredName);
	
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


function presortTeams($TeamRanking)
{
	global $DB, $TAGS, $KONSTANTS;
	
	$sql = 'SELECT * FROM _ranking WHERE TeamID>0 ORDER BY TeamID,TotalPoints';
	if ($TeamRanking == $KONSTANTS['TeamRankHighest'])
		$sql .= ' DESC';
	$LastTeamID = -1;
	$LastTeamPoints = 0;
	$LastTeamMiles = 0;

	//echo($sql.'<br>');
	$R = $DB->query($sql);

	while ($rd = $R->fetchArray())
	{

		if ($LastTeam <> $rd['TeamID'])
		{
			$LastTeam = $rd['TeamID'];
			$LastTeamPoints = $rd['TotalPoints'];
			$LastTeamMiles = $rd['CorrectedMiles'];
			//echo("UPDATE _ranking SET TotalPoints=$LastTeamPoints, CorrectedMiles=$LastTeamMiles WHERE TeamID=$LastTeam<br>");
			$DB->exec("UPDATE _ranking SET TotalPoints=$LastTeamPoints, CorrectedMiles=$LastTeamMiles WHERE TeamID=$LastTeam");
		}	
	}

}



function rankEntrants()
{
	global $DB, $TAGS, $KONSTANTS;

	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	$TiedPointsRanking = $rd['TiedPointsRanking'];
	$TeamRanking = $rd['TeamRanking'];
	
	$DB->exec('UPDATE entrants SET FinishPosition=0');

	$sql = 'CREATE TEMPORARY TABLE "_ranking" ';
	$sql .= 'AS SELECT EntrantID,TeamID,TotalPoints,CorrectedMiles,0 AS Rank FROM entrants WHERE EntrantStatus = '.$KONSTANTS['EntrantFinisher'];
	$DB->exec($sql);

	if ($TeamRanking != $KONSTANTS['TeamRankIndividuals'])
		presortTeams($TeamRanking);

	$R = $DB->query('SELECT * FROM _ranking ORDER BY TotalPoints DESC,CorrectedMiles ASC');
	
	$fp = 0;
	$lastTotalPoints = -1;
	$N = 1;
	$LastTeam = -1;

	$DB->query('BEGIN TRANSACTION');

	While ($rd = $R->fetchArray()) 
	{
		//echo($rd['EntrantID'].':'.$rd['TeamID'].' = '.$rd['TotalPoints'].', '.$rd['CorrectedMiles'].'<br>');
		
		
		If (($TiedPointsRanking != $KONSTANTS['TiesSplitByMiles']) || ($rd['TotalPoints'] <> $lastTotalPoints)) 
		{
			// No splitting needed, just assign rank
			if ($rd['TeamID'] == $LastTeam && $TeamRanking != $KONSTANTS['TeamRankIndividuals']) 
				;
			else if ($rd['TotalPoints'] == $lastTotalPoints)
				$N++;
		
			else 
			{
				$fp += $N;
				$N = 1;
			}
		}
		else
		{
			// Must be split according to mileage
			if ($LastTeam != $rd['TeamID'])
			{
				$fp += $N;
				$N = 1;
			}
			else if ($rd['TeamID'] > 0 && $rd['TeamID'] != $LastTeam)
				$N++;
		}
		if ($rd['TeamID'] > 0)
			$LastTeam = $rd['TeamID'];
		
		$lastTotalPoints = $rd['TotalPoints'];
		$sql = "UPDATE entrants SET FinishPosition=$fp WHERE EntrantID=".$rd['EntrantID'];
		//echo($sql.'; LastTeam='.$LastTeam.', N='.$N.', fp='.$fp.'<br>');
		$DB->exec($sql);

	}
	$DB->query('COMMIT TRANSACTION');
}







function retraceBreadcrumb()
{
	// This returns to penultimate breadcrumb
	
	if (!isset($_REQUEST['breadcrumbs']))
		return false;
	$bc = explode(';',$_REQUEST['breadcrumbs']);
	$i = sizeof($bc) - 2;
	if ($i < 0)
		return false;
	$lnk = $bc[$i];
	//echo("lnk==".htmlentities($lnk)."<br>");
	$lnka = [];
	if (preg_match('/href=\'([^\']*)\'/',$lnk,$lnka))
	{
		if ($i > 0)
		{
			// Strip out embedded breadcrumbs
			
			preg_replace('/(breadcrumbs=[^&])/','',$lnka[1]);
			if (strpos($lnka[1],'?'))
				$lnka[1] .= '&';
			else
				$lnka[1] .= '?';
			$lnka[1] .= 'breadcrumbs=';
			for ($j = 0; $j < $i; $j++)
			{
				echo(htmlentities($bc[$j]).'<br>');
				if ($j > 0)
					$lnka[1] .= ';';
				$lnka[1] .= $bc[$j];
			}
		}
		if (strpos($lnka[1],'?'))
			$lnka[1] .= '&';
		else
			$lnka[1] .= '?';
		$lnka[1] .= 'nobc';
		
		echo("<hr>".htmlentities($lnka[1])."<hr>");
		//exit;
		header('Location: '.$lnka[1]);
	}
	return true;
}

function show_menu_taglist()
{
	global $TAGS,$DB;
	
	$R = $DB->query("SELECT * FROM functions");
	$mytags = array();
	while ($rd = $R->fetchArray())
	{
		$t = explode(',',$rd['Tags']);
		foreach($t as $tg)
		{
			$mytags[$tg] = $tg;
		}
	}
	sort($mytags);
	echo("<select id='navbar_tagselect'");
	echo(' title="'.$TAGS['AdmSelectTag'][1].'"');
	echo(' onchange="window.location.href = '."'admin.php?tag='");
	echo("+document.getElementById('navbar_tagselect').value".';"');
	echo('>');
	echo('<option value="">'.$TAGS['AdmSelectTag'][0].'</option>');
	foreach($mytags as $t)
	{
		if ($t != '')
			echo("<option value='$t'>$t</option>");
	}
	echo("</select>");
	
}


function showNav()
{
	global $TAGS;
	
	echo('<div id="navbar">');
	
	echo( '<span id="navbar_breadcrumbs"></span> ');
	
	$xx = "return document.getElementById('EntrantSearchKey').value!='';";
	echo('<form method="get" action="entrants.php"  onsubmit="'.$xx.'">');
	echo(' <span title="'.$TAGS['UtlFindEntrant'][1].'">');
	echo('<input type="hidden" name="c" value="entrants">');
	echo('<input type="hidden" name="mode" value="find">');
	echo('<input type="text" name="x" id="EntrantSearchKey" placeholder="'.$TAGS['UtlFindEntrant'][0].'">');
	echo('<input type="submit" value="?"> ');
	echo('</span> ');
	echo('</form>');
	
	show_menu_taglist();
	echo('</div>');
}

function startHtml($pagetitle,$otherInfo = '',$showNav=true)
{
	global $DB, $TAGS, $KONSTANTS, $HTML_STARTED;
	global $HOME_URL, $DBVERSION;
	
	if ($HTML_STARTED)
		return;
	
	$HTML_STARTED = true;
	
	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
echo('<title>'.$pagetitle.'</title>');
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="reboot.css?ver=<?= filemtime('reboot.css')?>">
<link rel="stylesheet" type="text/css" href="score.css?ver=<?= filemtime('score.css')?>">
<script src="custom.js?ver=<?= filemtime('custom.js')?>" defer></script>
<script src="score.js?ver=<?= filemtime('score.js')?>" defer></script>
</head>
<body onload="bodyLoaded();">
<?php echo('<input type="hidden" id="BasicDistanceUnits" value="'.$KONSTANTS['BasicDistanceUnits'].'"/>'); ?>
<?php echo('<input type="hidden" id="DBVERSION" value="'.$DBVERSION.'"/>'); ?>
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
	if ($showNav)
		showNav();
}
function showFooter()
{
	global $DB, $TAGS;
	echo('<div id="footer">');
	echo('<span id="ftrAdminMenu" title="'.$TAGS['AdminMenu'][1].'"><a href="admin.php">'.$TAGS['AdminMenu'][0].'</a></span></div>');
	echo('</body></html>');
}

function rally_params_established()
{
	global $DB;
	
	$sql = "SELECT DBState FROM rallyparams";
	$R = $DB->query($sql);
	$rd = $R->fetchArray();
	return ($rd['DBState'] > 0);
}

?>

