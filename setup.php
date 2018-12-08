<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I run wizards to setup new rally scoring systems
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

 
$HOME_URL = "setup.php";
require_once("common.php");

$LAST_WIZARD_PAGE = 5;

function savePage($page_number)
{
	global $DB;
	
	$R = $DB->query("SELECT * FROM rallyparams");
	$rd = $R->fetchArray(SQLITE3_ASSOC);
	$sql = "UPDATE rallyparams SET ";
	$sql_fields = '';
	foreach ($_REQUEST as $rq => $rv)
	{
		if (!array_key_exists($rq,$rd))
			continue;
		if ($sql_fields != '')
			$sql_fields .= ',';
		$sql_fields .= $rq.'=';
		switch($rq)
		{
			case 'RallyTitle':
			case 'RallySlogan':
			case 'Cat1Label':
			case 'Cat2Label':
			case 'Cat3Label':
			case 'RejectReasons':
				$sql_fields .= "'".$DB->escapeString($rv)."'";
				break;
			case 'StartTime':
				$sql_fields .= "'".$DB->escapeString($_REQUEST['StartDate']).'T'.$DB->escapeString($_REQUEST['StartTime']);
				break;
			case 'FinishTime':
				$sql_fields .= "'".$DB->escapeString($_REQUEST['FinishDate']).'T'.$DB->escapeString($_REQUEST['FinishTime']);
				break;
			default:
				$sql_fields .= $rv;
		}
	}
	//echo($sql.$sql_fields);
	$DB->exec($sql.$sql_fields);
}


function showPage($page_number)
{
	global $TAGS,$DB,$KONSTANTS;
	
	
	function isChecked($n)
	{
		return ($n ? ' checked ' : '');
	}
	$page2show = $page_number;
	if ($page_number < 1)
	{
		// Figure out what to do based on contents of database
		$page2show = 1;
	}
	showPageHeader($page2show);
	$R = $DB->query("SELECT * FROM rallyparams");
	if ($rd = $R->fetchArray()) ; // Should complain or do something
	switch($page2show)
	{
		case 1:
			echo('<h2>'.$TAGS['WizTitle'][0].'</h2>');
			echo('<div class="wizitem"><p>'.$TAGS['RallyTitle'][1].'</p>');
			echo('<label for "RallyTitle">'.$TAGS['RallyTitle'][0].'</label> ');
			echo('<input type="text" name="RallyTitle" id="RallyTitle" value="'.$rd['RallyTitle'].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['RallySlogan'][1].'</p>');
			echo('<label for "RallySlogan">'.$TAGS['RallySlogan'][0].'</label> ');
			echo('<input type="text" name="RallySlogan" id="RallySlogan" value="'.$rd['RallySlogan'].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['CertificateHours'][1].'</p>');
			echo('<label for "CertificateHours">'.$TAGS['CertificateHours'][0].'</label> ');
			echo('<input type="number" class="smallnumber" name="CertificateHours" id="CertificateHours" value="'.$rd['CertificateHours'].'">');
			echo('</div>');
			break;
		case 2:
			$dt = splitDatetime($rd['StartTime']); 
			echo('<div class="wizitem"><p>'.$TAGS['StartDate'][1].'</p>');
			echo('<label for "StartDate">'.$TAGS['StartDate'][0].'</label> ');
			echo('<input type="date" name="StartDate" id="StartDate" value="'.$dt[0].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['StartTime'][1].'</p>');
			echo('<label for "StartTime">'.$TAGS['StartTime'][0].'</label> ');
			echo('<input type="time" name="StartTime" id="StartTime" value="'.$dt[1].'">');
			echo('</div>');
			$dt = splitDatetime($rd['FinishTime']); 
			echo('<div class="wizitem"><p>'.$TAGS['FinishDate'][1].'</p>');
			echo('<label for "FinishDate">'.$TAGS['FinishDate'][0].'</label> ');
			echo('<input type="date" name="FinishDate" id="FinishDate" value="'.$dt[0].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['FinishTime'][1].'</p>');
			echo('<label for "FinishTime">'.$TAGS['FinishTime'][0].'</label> ');
			echo('<input type="time" name="FinishTime" id="FinishTime" value="'.$dt[1].'">');
			echo('</div>');
			break;
		case 3:
			echo('<div class="wizitem"><p>'.$TAGS['OdoCheckUsed'][1].'</p>');
			echo('<label for "OdoCheckUsed">'.$TAGS['OdoCheckUsed'][0].'</label> ');
			$js = "document.getElementById('ocmDiv').className=(document.getElementById('OdoCheckUsed').checked?'':'wizhide');";
			echo('<input type="checkbox" name="OdoCheckUsed" id="OdoCheckUsed" '.isChecked($rd['OdoCheckMiles']).' onchange="'.$js.'">');
			
			$wclss = (isChecked($rd['OdoCheckMiles'])!= '' ? '' : 'wizhide');
			echo(' &nbsp;&nbsp;&nbsp;<span id="ocmDiv" class="'.$wclss.'" title="'.$TAGS['OdoCheckMiles'][1].'">');
			echo('<label for "OdoCheckMiles">'.$TAGS['OdoCheckMiles'][0].'</label> ');
			echo('<input type="number" name="OdoCheckMiles" id="OdoCheckMiles" value="'.$rd['OdoCheckMiles'].'">');
			echo('</span>');
			echo('</div>');

			echo('<div class="wizitem"><p>'.$TAGS['MinMilesUsed'][1].'</p>');
			echo('<label for "MinMilesUsed">'.$TAGS['MinMilesUsed'][0].'</label> ');
			$js = "document.getElementById('minmDiv').className=(document.getElementById('MinMilesUsed').checked?'':'wizhide');";
			echo('<input type="checkbox" name="MinMilesUsed" id="MinMilesUsed" '.isChecked($rd['MinMiles']).' onchange="'.$js.'">');
			$wclss = (isChecked($rd['MinMiles'])!= '' ? '' : 'wizhide');
			echo(' &nbsp;&nbsp;&nbsp;<span id="minmDiv" class="'.$wclss.'" title="'.$TAGS['MinMiles'][1].'">');
			echo('<label for "MinMiles">'.$TAGS['MinMiles'][0].'</label> ');
			echo('<input type="number" name="MinMiles" id="MinMiles" value="'.$rd['MinMiles'].'">');
			echo('</span>');
			echo('</div>');

			echo('<div class="wizitem"><p>'.$TAGS['MaxMilesUsed'][1].'</p>');
			echo('<label for "MaxMilesUsed">'.$TAGS['MaxMilesUsed'][0].'</label> ');
			$js = "document.getElementById('maxmDiv').className=(document.getElementById('MaxMilesUsed').checked?'':'wizhide');";
			echo('<input type="checkbox" name="MaxMilesUsed" id="MaxMilesUsed" '.isChecked($rd['PenaltyMilesDNF']).' onchange="'.$js.'">');
			$wclss = (isChecked($rd['PenaltyMilesDNF'])!= '' ? '' : 'wizhide');
			echo(' &nbsp;&nbsp;&nbsp;<span id="maxmDiv" class="'.$wclss.'" title="'.$TAGS['PenaltyMilesDNF'][1].'">');
			echo('<label for "PenaltyMilesDNF">'.$TAGS['PenaltyMilesDNF'][0].'</label> ');
			echo('<input type="number" name="PenaltyMilesDNF" id="PenaltyMilesDNF" value="'.$rd['PenaltyMilesDNF'].'">');
			echo('</span>');
			echo('</div>');

			echo('<div class="wizitem"><p>'.$TAGS['MinPointsUsed'][1].'</p>');
			echo('<label for "MinPointsUsed">'.$TAGS['MinPointsUsed'][0].'</label> ');
			$js = "document.getElementById('minpDiv').className=(document.getElementById('MinPointsUsed').checked?'':'wizhide');";
			echo('<input type="checkbox" name="MinPointsUsed" id="MinPointsUsed" '.isChecked($rd['MinPoints']).' onchange="'.$js.'">');
			$wclss = (isChecked($rd['MinPoints'])!= '' ? '' : 'wizhide');
			echo(' &nbsp;&nbsp;&nbsp;<span id="minpDiv" class="'.$wclss.'" title="'.$TAGS['MinPoints'][1].'">');
			echo('<label for "MinPoints">'.$TAGS['MinPoints'][0].'</label> ');
			echo('<input type="number" name="MinPoints" id="MinPoints" value="'.$rd['MinPoints'].'">');
			echo('</span>');
			echo('</div>');
			break;
		case 4:
			$sm = intval($rd['ScoringMethod']);
			echo('<div class="wizitem"><p>'.$TAGS['ScoringMethodWS'][1].'</p>');
			echo('<label for "ScoringMethodWS">'.$TAGS['ScoringMethodWS'][0].'</label> ');
			echo('<input type="radio" name="ScoringMethod" id="ScoringMethodWS" '.isChecked($rd['ScoringMethod']==$KONSTANTS['SimpleScoring']).' value="'.$KONSTANTS['SimpleScoring'].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['ScoringMethodWC'][1].'</p>');
			echo('<label for "ScoringMethodWC">'.$TAGS['ScoringMethodWC'][0].'</label> ');
			echo('<input type="radio" name="ScoringMethod" id="ScoringMethodWC" '.isChecked($rd['ScoringMethod']==$KONSTANTS['CompoundScoring']).' value="'.$KONSTANTS['CompoundScoring'].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['ScoringMethodWM'][1].'</p>');
			echo('<label for "ScoringMethodWM">'.$TAGS['ScoringMethodWM'][0].'</label> ');
			echo('<input type="radio" name="ScoringMethod" id="ScoringMethodWM" '.isChecked($rd['ScoringMethod']==$KONSTANTS['ManualScoring']).' value="'.$KONSTANTS['ManualScoring'].'">');
			echo('</div>');
			echo('<div class="wizitem"><p>'.$TAGS['ScoringMethodWA'][1].'</p>');
			echo('<label for "ScoringMethodWA">'.$TAGS['ScoringMethodWA'][0].'</label> ');
			echo('<input type="radio" name="ScoringMethod" id="ScoringMethodWA" '.isChecked($rd['ScoringMethod']==$KONSTANTS['AutoScoring']).' value="'.$KONSTANTS['AutoScoring'].'">');
			echo('</div>');
			break;
			
		case 5:
			echo('<div><p>'.$TAGS['WizFinishText'][0].'</p>');
			echo('<div><p>'.$TAGS['WizFinishText'][1].'</p>');
			echo('<input type="hidden" name="DBState" value="1">');
			break;
			
		
		
	}
	showPageTrailer($page2show);
}

function showPageHeader($page_number)
{
	global $TAGS;
	
	startHtml('<a href="about.php" class="techie" title="'.$TAGS['HelpAbout'][1].'">'.$TAGS['HelpAbout'][0].'</a>');

?>
<form method="post" action="setup.php">
<input type="hidden" name="frompage" value="<?php echo($page_number);?>">
<div id="setupwiz">
<?php
}

function showPageTrailer($page_number)
{
	global $TAGS, $LAST_WIZARD_PAGE;
	
	if ($page_number > 1)
		echo('<input type="submit" class="wizbutton" name="prevpage" title="'.$TAGS['WizPrevPage'][1].'" value="'.$TAGS['WizPrevPage'][0].'"> ');
	if ($page_number < $LAST_WIZARD_PAGE)
		echo('<input type="submit" class="wizbutton" name="nextpage" title="'.$TAGS['WizNextPage'][1].'" value="'.$TAGS['WizNextPage'][0].'"> ');
	else
		echo('<input type="submit" class="wizbutton" name="endwiz" title="'.$TAGS['WizFinish'][1].'" value="'.$TAGS['WizFinish'][0].'"> ');

		
?>
</div>
</form>
<?php
}
	//var_dump($_REQUEST);
	
	if (isset($_REQUEST['nextpage']) || isset($_REQUEST['prevpage']) || isset($_REQUEST['endwiz']))
		savePage($_REQUEST['frompage']);
	if (isset($_REQUEST['endwiz']))
	{
		$_REQUEST['menu'] = 'setup';
		include('admin.php');
		exit;
	}
	if (isset($_REQUEST['nextpage']))
		$_REQUEST['page'] = $_REQUEST['frompage'] + 1;
	if (isset($_REQUEST['prevpage']))
		$_REQUEST['page'] = $_REQUEST['frompage'] - 1;
	
	if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page']))
		showPage($_REQUEST['page']);
	else
		showPage(0);
?>