<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle basic maintenance of entrant records
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


$HOME_URL = 'admin.php';

/*
 *
 *	2.1	Autosuppress Team# in listings
 *	2.1	Certificate class
 *
 */
 
require_once('common.php');



// Alphabetic order below


function fetchShowEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	
	$sql = "SELECT * FROM entrants WHERE EntrantID=".intval($_REQUEST['id']);
	
	$R = $DB->query($sql);
	
	if ($rd = $R->fetchArray())
		if ($_REQUEST['mode']=='full')
			showEntrantRecord($rd);
		else
			showEntrantChecks($rd);
}

function listEntrants($ord = "EntrantID")
{
	global $DB, $TAGS, $KONSTANTS;

	eval("\$evs = ".$TAGS['EntrantStatusV'][0]);

	$ShowTeamCol = TRUE;
	$R = $DB->query("SELECT Count(*) As Rex FROM entrants WHERE TeamID <> 0");
	if ($rd = $R->fetchArray())
		if ($rd['Rex'] == 0)
			$ShowTeamCol = FALSE;
		
	$sql = "SELECT * FROM entrants";
	if ($ord <> '')
		$sql .= " ORDER BY $ord";
	//echo('<br>listEntrants: '.$sql.'<br>');
	$R = $DB->query($sql);
	
	if (!isset($_REQUEST['mode']))
		$_REQUEST['mode'] = 'full';

	echo('<table id="entrants">');
	if ($_REQUEST['mode']=='full')
		echo('<caption title="'.htmlentities($TAGS['EntrantListFull'][1]).'">'.htmlentities($TAGS['EntrantListFull'][0]).'</caption>');
	else
		echo('<caption title="'.htmlentities($TAGS['EntrantListCheck'][1]).'">'.htmlentities($TAGS['EntrantListCheck'][0]).'</caption>');
		
	echo('<thead><tr><th class="EntrantID"><a href="entrants.php?c=entrants&amp;ord=EntrantID&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['EntrantID'][0].'</a></th>');
	echo('<th class="RiderName"><a href="entrants.php?c=entrants&amp;ord=RiderName&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['RiderName'][0].'</a></th>');
	echo('<th class="PillionName"><a href="entrants.php?c=entrants&amp;ord=PillionName&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['PillionName'][0].'</a></th>');
	echo('<th class="Bike"><a href="entrants.php?c=entrants&amp;ord=Bike&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['Bike'][0].'</a></th>');
	if ($ShowTeamCol && $_REQUEST['mode']=='full')
		echo('<th class="TeamID"><a href="entrants.php?c=entrants&amp;ord=TeamID&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['TeamID'][0].'</a></th>');
	echo('<th class="EntrantStatus"><a href="entrants.php?c=entrants&amp;ord=EntrantStatus&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['EntrantStatus'][0].'</a></th>');
	if ($_REQUEST['mode']=='full')
	{
		echo('<th class="FinishPosition"><a href="entrants.php?c=entrants&amp;ord=EntrantStatus DESC,FinishPosition&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['FinishPosition'][0].'</a></th>');
		echo('<th class="TotalPoints"><a href="entrants.php?c=entrants&amp;ord=TotalPoints&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['TotalPoints'][0].'</a></th>');
		echo('<th class="CorrectedMiles"><a href="entrants.php?c=entrants&amp;ord=CorrectedMiles&amp;mode='.$_REQUEST['mode'].'">'.$TAGS['CorrectedMiles'][0].'</a></th>');
	}
	echo('</tr>');
	echo('</thead><tbody>');
	
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="link" onclick="window.location.href=\'entrants.php?c=entrant&amp;id='.$rd['EntrantID'].'&amp;mode='.$_REQUEST['mode'].'\'">');
		echo('<td class="EntrantID">'.$rd['EntrantID'].'</td>');
		echo('<td class="RiderName">'.$rd['RiderName'].'</td>');
		echo('<td class="PillionName">'.$rd['PillionName'].'</td>');
		echo('<td class="Bike">'.$rd['Bike'].'</td>');
		if ($ShowTeamCol && $_REQUEST['mode']=='full')
		{
			echo('<td class="TeamID">');
			if ($rd['TeamID'] <> 0)
				echo($rd['TeamID']);
			echo('</td>');
		}
		$es = $evs[''.$rd['EntrantStatus']];
		if ($es=='')
			$es = '[[ '.$rd['EntrantStatus'].']]';
		echo('<td class="EntrantStatus">'.$es.'</td>');
		if ($_REQUEST['mode']=='full')
		{
			echo('<td class="FinishPosition">'.$rd['FinishPosition'].'</td>');
			echo('<td class="TotalPoints">'.$rd['TotalPoints'].'</td>');
			echo('<td class="CorrectedMiles">'.$rd['CorrectedMiles'].'</td>');
		}
		echo('</tr>');
	}
	echo('</tbody></table>');

}



function saveEntrantRecord()
{
	global $DB, $TAGS, $KONSTANTS;

	$fa = array('RiderName','RiderFirst','RiderIBA','PillionName','PillionFirst','PillionIBA',
				'Bike','BikeReg','TeamID','Country','OdoKms','OdoCheckStart','OdoCheckFinish',
				'OdoScaleFactor','OdoRallyStart','OdoRallyFinish','CorrectedMiles','FinishTime',
				'BonusesVisited','SpecialsTicked','CombosTicked','TotalPoints','FinishPosition',
				'EntrantStatus','ScoredBy','StartTime','Class');

	$fab = array('BonusesVisited' => 'BonusID','SpecialsTicked' => 'SpecialID', 'CombosTicked' => 'ComboID');
	
	//var_dump($_REQUEST);
	//echo('<hr>');
	
	//if (isset($_REQUEST['BonusID']))
		//echo(" BonusID ");

	$adding = $_REQUEST['EntrantID']=='';
	
	if ($adding)
	{
		$sql = "SELECT Max(EntrantID) AS MaxID FROM entrants";
		$R = $DB->query($sql);
		$rd = $R->fetchArray();
		$newid = $rd['MaxID'] + 1;
		if (isset($_REQUEST['EntrantID']) && intval($_REQUEST['EntrantID']) > $newid)
			$newid = intval($_REQUEST['EntrantID']);
	}
	
	
	if (!$adding)
		$sql = "UPDATE entrants SET ";
	else
	{
		$sql = "INSERT INTO entrants (EntrantID,";
		$comma = '';
		foreach($fa as $faa)
		{
			if (isset($_REQUEST[$faa]) || (isset($fab[$faa]) && isset($_REQUEST[$fab[$faa]])))
			{
				$sql .= $comma.$faa;
				$comma = ',';
			}
		}
		if (!$adding)
			$sql .= ",ScoringNow";
		$sql .= ") VALUES (";
	}

	if ($adding)
	{
		$sql .= $newid.',';
	}
	$comma = '';
	foreach($fa as $faa)
	{
			if (isset($_REQUEST[$faa]) || (isset($fab[$faa]) && isset($_REQUEST[$fab[$faa]])))
		{
			$sql .= $comma;
			$comma = ',';
			if (!$adding) 
				$sql .= $faa.'=';
			switch($faa)
			{
				case 'RiderIBA':
				case 'PillionIBA':
				case 'OdoKms':
				case 'TeamID':
				case 'CorrectedMiles':
				case 'TotalPoints':
				case 'Class':
					$sql .= intval($_REQUEST[$faa]);
					break;
				case 'OdoCheckStart':
				case 'OdoCheckFinish':
				case 'OdoScaleFactor':
				case 'OdoRallyStart':
				case 'OdoRallyFinish':
					$sql .= floatval($_REQUEST[$faa]);
					break;
				case 'FinishTime':
					if ($_REQUEST['FinishDate']<>'' && $_REQUEST['FinishTime']<>'')
						$sql .= "'".$_REQUEST['FinishDate'].'T'.$_REQUEST['FinishTime']."'";
					else
						$sql .= "null";
					break;
				case 'StartTime':
					if ($_REQUEST['StartDate']<>'' && $_REQUEST['StartTime']<>'')
						$sql .= "'".$_REQUEST['StartDate'].'T'.$_REQUEST['StartTime']."'";
					else
						$sql .= "null";
					break;
				case 'BonusesVisited':
				case 'SpecialsTicked':
				case 'CombosTicked':
					//echo(' $fab[$faa] == '.$fab[$faa].' == '.$_REQUEST[$fab[$faa]].' ;');
					if (isset($_REQUEST[$fab[$faa]]))
					{
						$sql .= "'".$DB->escapeString(implode(',',$_REQUEST[$fab[$faa]]))."'";
						break;
					}
				default:
					$sql .= "'".$DB->escapeString($_REQUEST[$faa])."'";
			}
		}
	}
	if (!$adding)
	{
		$sql .= $comma."ScoringNow=";
		if (isset($_REQUEST['ScoringNow']))
			$sql .= $KONSTANTS['BeingScored'];
		else
			$sql .= $KONSTANTS['NotBeingScored'];
	}

	
	if ($adding)
		$sql .= ")";
	else
		$sql .= " WHERE EntrantID=".$_REQUEST['EntrantID'];
	
	//echo($sql.'<br>');
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}
	
}

function showEntrantBonuses($bonuses)
{
	global $DB, $TAGS, $KONSTANTS;

	$BA = explode(',',','.$bonuses); // The leading comma means that the first element is index 1 not 0
	$R = $DB->query('SELECT * FROM bonuses ORDER BY BonusID');
	while ($rd = $R->fetchArray())
	{
		$BP[$rd['BonusID']] = $rd['BriefDesc'];
	}
	foreach($BP as $bk => $b)
	{
		if ($bk <> '') {
			echo('<span class="keep" title="'.htmlspecialchars($b).'">');
			echo('<label for="B'.$bk.'">'.$bk.' </label>');
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			echo('<input type="checkbox"'.$chk.' name="BonusID[]" id="B'.$bk.'" value="'.$bk.'"> ');
			echo('</span>'."\r\n");
		}
	}
}







/* Check-in/check-out stuff */
function showEntrantChecks($rd)
{
	global $DB, $TAGS, $KONSTANTS;

	echo('<form method="post" action="entrants.php">');

	echo('<input type="hidden" name="c" value="entrants">');
	echo('<input type="hidden" name="mode" value="check">');
	
	echo('<span class="vlabel"  style="font-weight: bold;" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].' </label> ');
	echo('<input type="text" class="number"  readonly name="EntrantID" id="EntrantID" value="'.$rd['EntrantID'].'">'.' '.htmlspecialchars($rd['RiderName']).'</span>');
	
	
	
	
	
	
	echo('<fieldset  id="tab_odo">');
	
	$odoF = $DB->query("SELECT OdoCheckMiles,StartTime FROM rallyparams");
	$odoC = $odoF->fetchArray();
	
	echo('<input type="hidden" name="OdoCheckMiles" id="OdoCheckMiles" value="'.$odoC['OdoCheckMiles'].'">');

	if (floatval($odoC['OdoCheckMiles']) < 1.0)
		$hideOdoCheck = true;
	else
		$hideOdoCheck = false;
	
	echo('<span   title="'.$TAGS['OdoKms'][1].' "> '.$TAGS['OdoKms'][0].' ');
	echo('<label for="OdoKmsM">'.$TAGS['OdoKmsM'][0].': </label> ');
	$chk = $rd['OdoKms'] <> $KONSTANTS['OdoCountsKilos'] ? ' checked="checked" ' : '';
	echo('<input onchange="odoAdjust();" type="radio" name="OdoKms" id="OdoKmsM" value="'.$KONSTANTS['OdoCountsMiles'].'"'.$chk.'></span>');
	echo('&nbsp;&nbsp;&nbsp;<span><label for="OdoKmsK">'.$TAGS['OdoKmsK'][0].' </label> ');
	$chk = $rd['OdoKms'] == $KONSTANTS['OdoCountsKilos'] ? ' checked="checked" ' : '';
	echo('<input  onchange="odoAdjust();" type="radio" name="OdoKms" id="OdoKmsK" value="'.$KONSTANTS['OdoCountsKilos'].'"'.$chk.'></span>');

	if ($hideOdoCheck)
		echo('<div style="display:none;">');
	echo('<span  class="xlabel" title="'.$TAGS['OdoCheckStart'][1].' "><label for="OdoCheckStart">'.$TAGS['OdoCheckStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoCheckStart" id="OdoCheckStart" value="'.$rd['OdoCheckStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoCheckFinish'][1].' "><label for="OdoCheckFinish">'.$TAGS['OdoCheckFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoCheckFinish" id="OdoCheckFinish" value="'.$rd['OdoCheckFinish'].'"> </span>');
	
	echo('<span   title="'.$TAGS['OdoScaleFactor'][1].'"><label for="OdoScaleFactor">'.$TAGS['OdoScaleFactor'][0].' </label> ');
	echo('<input type="number" step="any" name="OdoScaleFactor" id="OdoScaleFactor" value="'.$rd['OdoScaleFactor'].'"> </span>');
	
	if ($hideOdoCheck)
		echo('</div>');
	
	echo('<span  class="xlabel" title="'.$TAGS['OdoRallyStart'][1].' "><label for="OdoRallyStart">'.$TAGS['OdoRallyStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoRallyStart" id="OdoRallyStart" value="'.$rd['OdoRallyStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoRallyFinish'][1].' "><label for="OdoRallyFinish">'.$TAGS['OdoRallyFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoRallyFinish" id="OdoRallyFinish" value="'.$rd['OdoRallyFinish'].'"> </span>');
	
	echo('<span >');
	echo('<label for="CorrectedMiles" >'.$TAGS['CorrectedMiles'][0].' </label>');
	echo(' <input type="number" name="CorrectedMiles" id="CorrectedMiles" value="'.$rd['CorrectedMiles'].'" title="'.$TAGS['CorrectedMiles'][1].'"> ');
	echo('</span>');
	
	echo('<hr><br>');
	echo('<span   title="'.$TAGS['EntrantStatus'][1].'"><label for="EntrantStatus">'.$TAGS['EntrantStatus'][0].' </label>');
	echo('<select name="EntrantStatus" id="EntrantStatus">');
	if ($rd['EntrantStatus']=='')
		$rd['EntrantStatus'] = $KONSTANTS['DefaultEntrantStatus'];
	echo('<option value="'.$KONSTANTS['EntrantDNS'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantDNS'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantDNS'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantOK'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantOK'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantOK'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantFinisher'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantFinisher'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantFinisher'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantDNF'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantDNF'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantDNF'][0].'</option>');
	echo('</select></span>');
	echo('<br><hr>');
	$dt = splitDatetime($rd['StartTime']); 
	if ($dt[0]=='')
	{
		$dt = splitDatetime($odoC['StartTime']); // Default to rally start time
	}
	echo('<span class="vlabel">');
	echo('<label for="StartDate" class="vlabel">'.$TAGS['StartDate'][0].' </label>');
	echo(' <input type="date" name="StartDate" id="StartDate" value="'.$dt[0].'" title="'.$TAGS['StartDate'][1].'"> ');
	echo('<label for="StartTime">'.$TAGS['StartTime'][0].' </label>');
	echo(' <input type="time" name="StartTime" id="StartTime" value="'.$dt[1].'" title="'.$TAGS['StartTime'][1].'"> ');
	echo(' <input type="button" value="'.$TAGS['nowlit'][0].'" onclick="setSplitNow(\'Start\');">');
	echo('</span>');

	$dt = splitDatetime($rd['FinishTime']); 

	echo('<span class="vlabel">');
	echo('<label for="FinishDate" class="vlabel">'.$TAGS['FinishDate'][0].' </label>');
	echo(' <input type="date" name="FinishDate" id="FinishDate" value="'.$dt[0].'" title="'.$TAGS['FinishDate'][1].'"> ');
	echo('<label for="FinishTime">'.$TAGS['FinishTime'][0].' </label>');
	echo(' <input type="time" name="FinishTime" id="FinishTime" value="'.$dt[1].'" title="'.$TAGS['FinishTime'][1].'"> ');
	echo(' <input type="button" value="'.$TAGS['nowlit'][0].'" onclick="setSplitNow(\'Finish\');">');
	echo('</span>');

	
	
	echo('</fieldset>');
	
	
	echo('<input type="submit" name="savedata" value="'.$TAGS['SaveEntrantRecord'][0].'">');
	echo('</form>');
		
}






















function showEntrantSpecials($specials)
{
	global $DB, $TAGS, $KONSTANTS;

	$BA = explode(',',','.$specials); // The leading comma means that the first element is index 1 not 0

	$R = $DB->query('SELECT * FROM specials ORDER BY BonusID');
	while ($rd = $R->fetchArray())
	{
		$BP[$rd['BonusID']] = $rd['BriefDesc'];
	}
	echo('<span  class="xlabel" ></span>');
	foreach($BP as $bk => $b)
	{
		if ($bk <> '') {
			echo('<span title="'.htmlspecialchars($bk).'">');
			echo('<label for="S'.$bk.'">'.htmlspecialchars($b).' </label>');
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			echo('<input type="checkbox"'.$chk.' name="SpecialID[]" id="S'.$bk.'" value="'.$bk.'"> ');
			echo(' &nbsp;&nbsp;</span>');
		}
	}
}

function showEntrantCombinations($Combos)
{
	global $DB, $TAGS, $KONSTANTS;

	$BA = explode(',',','.$Combos); // The leading comma means that the first element is index 1 not 0
	
	$R = $DB->query('SELECT * FROM combinations ORDER BY ComboID');
	while ($rd = $R->fetchArray())
	{
		$BA[$rd['ComboID']] = $rd['BriefDesc'];
	}
	echo('<span  class="xlabel" ></span>');
	foreach($BA as $bk => $b)
	{
		if ($bk <> '') {
			echo('<span title="'.htmlspecialchars($bk).'">');
			echo('<label for="C'.$bk.'">'.htmlspecialchars($b).' </label>');
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			echo('<input type="checkbox"'.$chk.' name="ComboID[]" id="C'.$bk.'" value="'.$bk.'"> ');
			echo(' &nbsp;&nbsp;</span>');
		}
	}
}


function showEntrantRecord($rd)
{
	global $DB, $TAGS, $KONSTANTS;

	echo('<form method="post" action="entrants.php">');

	echo('<input type="hidden" name="c" value="entrants">');
	echo('<span class="vlabel"  style="font-weight: bold;" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].' </label> ');
	if ($rd['EntrantID']=='')
		$ro = '';
	else
		$ro = ' readonly ';
	echo('<input type="text"  onchange="enableSaveButton();"  class="number"  '.$ro.' name="EntrantID" id="EntrantID" value="'.$rd['EntrantID'].'">'.' '.htmlspecialchars($rd['RiderName']).'</span>');
	
	echo('<div class="tabs_area" style="display:inherit"><ul id="tabs">');
	echo('<li><a href="#tab_basic">'.$TAGS['BasicDetails'][0].'</a></li>');
	echo('<li><a href="#tab_odo">'.$TAGS['Odometer'][0].'</a></li>');
	echo('<li><a href="#tab_results">'.$TAGS['RallyResults'][0].'</a></li>');
	echo('<li><a href="#tab_bonuses">'.$TAGS['BonusesLit'][0].'</a></li>');
	echo('<li><a href="#tab_specials">'.$TAGS['SpecialsLit'][0].'</a></li>');
	echo('<li><a href="#tab_combos">'.$TAGS['CombosLit'][0].'</a></li>');
	echo('</ul></div>');
	
	
	
	echo('<fieldset class="tabContent" id="tab_basic"><legend>'.$TAGS['BasicDetails'][0].'</legend>');
	echo('<span  class="xlabel" title="'.$TAGS['RiderName'][1].'"><label for="RiderName">'.$TAGS['RiderName'][0].' </label> ');
	echo('<input type="text" onchange="enableSaveButton();" name="RiderName" id="RiderName" value="'.htmlspecialchars($rd['RiderName']).'"> </span>');
	echo('<span  title="'.$TAGS['RiderFirst'][1].'"><label for="RiderFirst">'.$TAGS['RiderFirst'][0].' </label> ');
	echo('<input type="text" onchange="enableSaveButton();"  name="RiderFirst" id="RiderFirst" value="'.htmlspecialchars($rd['RiderFirst']).'"> </span>');
	
	echo('<span  title="'.$TAGS['RiderIBA'][1].'"><label for="RiderIBA">'.$TAGS['RiderIBA'][0].' </label> ');
	echo('<input type="number"  onchange="enableSaveButton();" name="RiderIBA" id="RiderIBA" value="'.$rd['RiderIBA'].'"> </span>');
	
	echo('<span class="xlabel" title="'.$TAGS['Bike'][1].'"><label for="Bike">'.$TAGS['Bike'][0].' </label> ');
	echo('<input type="text"  onchange="enableSaveButton();" name="Bike" id="Bike" value="'.$rd['Bike'].'"> </span>');
	
	echo('<span title="'.$TAGS['BikeReg'][1].'"><label for="BikeReg">'.$TAGS['BikeReg'][0].' </label> ');
	echo('<input type="text"  onchange="enableSaveButton();" name="BikeReg" id="BikeReg" value="'.$rd['BikeReg'].'"> </span>');
	
	
	
	echo('<span  class="xlabel" title="'.$TAGS['PillionName'][1].'"><label for="PillionName">'.$TAGS['PillionName'][0].' </label> ');
	echo('<input type="text"  onchange="enableSaveButton();" name="PillionName" id="PillionName" value="'.htmlspecialchars($rd['PillionName']).'"> </span>');
	echo('<span  title="'.$TAGS['PillionFirst'][1].'"><label for="PillionFirst">'.$TAGS['PillionFirst'][0].' </label> ');
	echo('<input type="text"  onchange="enableSaveButton();" name="PillionFirst" id="PillionFirst" value="'.htmlspecialchars($rd['PillionFirst']).'"> </span>');
	
	echo('<span  title="'.$TAGS['PillionIBA'][1].'"><label for="PillionIBA">'.$TAGS['PillionIBA'][0].' </label> ');
	echo('<input type="number"  onchange="enableSaveButton();" name="PillionIBA" id="PillionIBA" value="'.$rd['PillionIBA'].'"> </span>');
	
	echo('<span class="vlabel" title="'.$TAGS['Country'][1].'"><label for="Country">'.$TAGS['Country'][0].' </label> ');
	echo('<input type="text"  onchange="enableSaveButton();" name="Country" id="Country" value="'.$rd['Country'].'"> </span>');
	
	echo('<span class="vlabel" title="'.$TAGS['TeamID'][1].'"><label for="TeamID">'.$TAGS['TeamID'][0].' </label> ');
	echo('<input type="number"  onchange="enableSaveButton();" name="TeamID" id="TeamID" value="'.$rd['TeamID'].'"> </span>');
	
	echo('<span class="vlabel" title="'.$TAGS['Class'][1].'"><label for="Class">'.$TAGS['Class'][0].' </label> ');
	echo('<input type="number"  onchange="enableSaveButton();" name="Class" id="Class" value="'.$rd['Class'].'"> </span>');
	
	echo('</fieldset>');
	
	
	
	echo('<fieldset  class="tabContent" id="tab_odo"><legend>'.$TAGS['Odometer'][0].'</legend>');
	
	$odoF = $DB->query("SELECT OdoCheckMiles FROM rallyparams");
	$odoC = $odoF->fetchArray();
	
	echo('<input type="hidden" name="OdoCheckMiles" id="OdoCheckMiles" value="'.$odoC['OdoCheckMiles'].'">');

	echo('<span  class="xlabel" title="'.$TAGS['OdoKms'][1].' "> '.$TAGS['OdoKms'][0].' ');
	echo('<label for="OdoKmsM">'.$TAGS['OdoKmsM'][0].': </label> ');
	$chk = $rd['OdoKms'] <> $KONSTANTS['OdoCountsKilos'] ? ' checked="checked" ' : '';
	echo('<input onchange="odoAdjust();" type="radio" name="OdoKms" id="OdoKmsM" value="'.$KONSTANTS['OdoCountsMiles'].'"'.$chk.'></span>');
	echo('&nbsp;&nbsp;&nbsp;<span><label for="OdoKmsK">'.$TAGS['OdoKmsK'][0].' </label> ');
	$chk = $rd['OdoKms'] == $KONSTANTS['OdoCountsKilos'] ? ' checked="checked" ' : '';
	echo('<input  onchange="odoAdjust();" type="radio" name="OdoKms" id="OdoKmsK" value="'.$KONSTANTS['OdoCountsKilos'].'"'.$chk.'></span>');

	echo('<span  class="xlabel" title="'.$TAGS['OdoCheckStart'][1].' "><label for="OdoCheckStart">'.$TAGS['OdoCheckStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoCheckStart" id="OdoCheckStart" value="'.$rd['OdoCheckStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoCheckFinish'][1].' "><label for="OdoCheckFinish">'.$TAGS['OdoCheckFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoCheckFinish" id="OdoCheckFinish" value="'.$rd['OdoCheckFinish'].'"> </span>');
	
	echo('<span  class="xlabel" title="'.$TAGS['OdoScaleFactor'][1].'"><label for="OdoScaleFactor">'.$TAGS['OdoScaleFactor'][0].' </label> ');
	echo('<input type="number" step="any" name="OdoScaleFactor" id="OdoScaleFactor" value="'.$rd['OdoScaleFactor'].'"> </span>');
	
	echo('<span  class="xlabel" title="'.$TAGS['OdoRallyStart'][1].' "><label for="OdoRallyStart">'.$TAGS['OdoRallyStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoRallyStart" id="OdoRallyStart" value="'.$rd['OdoRallyStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoRallyFinish'][1].' "><label for="OdoRallyFinish">'.$TAGS['OdoRallyFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust();" type="number" step="any" name="OdoRallyFinish" id="OdoRallyFinish" value="'.$rd['OdoRallyFinish'].'"> </span>');
	
	
	echo('</fieldset>');

	
	echo('<fieldset  class="tabContent" id="tab_results"><legend>'.$TAGS['RallyResults'][0].'</legend>');
	echo('<span  class="xlabel" title="'.$TAGS['EntrantStatus'][1].'"><label for="EntrantStatus">'.$TAGS['EntrantStatus'][0].' </label>');
	echo('<select name="EntrantStatus" id="EntrantStatus">');
	if ($rd['EntrantStatus']=='')
		$rd['EntrantStatus'] = $KONSTANTS['DefaultEntrantStatus'];
	echo('<option value="'.$KONSTANTS['EntrantDNS'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantDNS'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantDNS'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantOK'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantOK'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantOK'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantFinisher'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantFinisher'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantFinisher'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantDNF'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantDNF'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantDNF'][0].'</option>');
	echo('</select></span>');
	
	$dt = splitDatetime($rd['StartTime']); 

	echo('<span class="vlabel">');
	echo('<label for="StartDate" class="vlabel">'.$TAGS['StartDate'][0].' </label>');
	echo(' <input type="date" name="StartDate" id="StartDate" value="'.$dt[0].'" title="'.$TAGS['StartDate'][1].'"> ');
	echo('<label for="StartTime">'.$TAGS['StartTime'][0].' </label>');
	echo(' <input type="time" name="StartTime" id="StartTime" value="'.$dt[1].'" title="'.$TAGS['StartTime'][1].'"> ');
	echo('</span>');

	$dt = splitDatetime($rd['FinishTime']); 

	echo('<span class="vlabel">');
	echo('<label for="FinishDate" class="vlabel">'.$TAGS['FinishDate'][0].' </label>');
	echo(' <input type="date" name="FinishDate" id="FinishDate" value="'.$dt[0].'" title="'.$TAGS['FinishDate'][1].'"> ');
	echo('<label for="FinishTime">'.$TAGS['FinishTime'][0].' </label>');
	echo(' <input type="time" name="FinishTime" id="FinishTime" value="'.$dt[1].'" title="'.$TAGS['FinishTime'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="CorrectedMiles" class="vlabel">'.$TAGS['CorrectedMiles'][0].' </label>');
	echo(' <input type="number" name="CorrectedMiles" id="CorrectedMiles" value="'.$rd['CorrectedMiles'].'" title="'.$TAGS['CorrectedMiles'][1].'"> ');
	echo('</span>');
	
	echo('<span class="vlabel">');
	echo('<label for="TotalPoints" class="vlabel">'.$TAGS['TotalPoints'][0].' </label>');
	echo(' <input type="number" name="TotalPoints" id="TotalPoints" value="'.$rd['TotalPoints'].'" title="'.$TAGS['TotalPoints'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="FinishPosition" class="vlabel">'.$TAGS['FinishPosition'][0].' </label>');
	echo(' <input type="number" name="FinishPosition" id="FinishPosition" value="'.$rd['FinishPosition'].'" title="'.$TAGS['FinishPosition'][1].'"> ');
	echo('</span>');

	echo('<span class="xlabel" title="'.$TAGS['ScoringNow'][1].'">');
	echo('<label for="ScoringNow" class="vlabel">'.$TAGS['ScoringNow'][0].' </label>');
	$chk = $rd['ScoringNow'] == $KONSTANTS['BeingScored'] ? ' checked="checked" ' : '';
	echo('<input type="checkbox"'.$chk.' name="ScoringNow" disabled id="ScoringNow" value="'.$KONSTANTS['BeingScored'].'"> ');
	echo('</span>');
	
	echo('<span title="'.$TAGS['ScoredBy'][1].'">');
	echo('<label for="ScoredBy" class="vlabel">'.$TAGS['ScoredBy'][0].' </label>');
	echo('<input type="text" name="ScoredBy" readonly id="ScoredBy" value="'.$rd['ScoredBy'].'"> ');
	echo('</span>');
	
	echo('</fieldset>');
	
	echo('<fieldset  class="tabContent" id="tab_bonuses"><legend>'.$TAGS['BonusesLit'][0].'</legend>');
	showEntrantBonuses($rd['BonusesVisited']);
	echo('<!-- B --> </fieldset>');
	echo('<fieldset  class="tabContent" id="tab_specials"><legend>'.$TAGS['SpecialsLit'][0].'</legend>');
	showEntrantSpecials($rd['SpecialsTicked']);
	echo('</fieldset>');
	echo('<fieldset  class="tabContent" id="tab_combos"><legend>'.$TAGS['CombosLit'][0].'</legend>');
	showEntrantCombinations($rd['CombosTicked']);
	echo('</fieldset>');
	
	if ($rd['RiderName'] <> '')
		$dis = '';
	else
		$dis = ' disabled ';
	echo('<input type="submit"'.$dis.' id="savedata" name="savedata" value="'.$TAGS['SaveEntrantRecord'][0].'">');
	echo('</form>');
		
}

















function showNewEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	$rd = [];
	
	// Set some defaults
	$rd['KmsOdo'] = $KONSTANTS['DefaultKmsOdo'];
	$rd['OdoScaleFactor'] = $KONSTANTS['DefaultOdoScaleFactor'];
	$rd['EntrantStatus'] = $KONSTANTS['DefaultEntrantStatus'];
	$rd['Country'] = $KONSTANTS['DefaultCountry'];
	$rd['StartTime'] = getValueFromDB('SELECT StartTime FROM rallyparams','StartTime','');
	
	showEntrantRecord($rd);
}


startHtml();

if (isset($_REQUEST['savedata']))
	saveEntrantRecord();


if (isset($_REQUEST['c']) && $_REQUEST['c']=='entrant')
	fetchShowEntrant();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='newentrant')
	showNewEntrant();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='entrants')
	listEntrants(isset($_REQUEST['ord']) ? $_REQUEST['ord'] : '');


?>

