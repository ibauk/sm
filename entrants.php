<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle basic maintenance of entrant records
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


$HOME_URL = 'admin.php?menu=entrant';

/*
 *
 *	2.1	Autosuppress Team# in listings
 *	2.1	Certificate class
 *
 */
 
require_once('common.php');



// Alphabetic order below


function deleteEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	$entrantid = $_POST['entrantid'];
	if ($entrantid == '')
		return;
	if ($_POST['rusure'] != $KONSTANTS['AreYouSureYes'])
		return;
	$DB->exec("DELETE FROM entrants WHERE EntrantID=$entrantid");
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}

}

function fetchShowEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	
	$sql = "SELECT * FROM entrants WHERE EntrantID=".intval($_REQUEST['id']);
	
	$R = $DB->query($sql);
	
	if ($rd = $R->fetchArray())
		if ($_REQUEST['mode']!='check')
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
		
	$sql = "SELECT *,substr(RiderName,1,RiderPos-1) As RiderFirst";
	$sql .= ",substr(RiderName,RiderPos+1) As RiderLast";
	$sql .= " FROM (SELECT *,instr(RiderName,' ') As RiderPos FROM entrants) ";
	$bonus = '';
	if (isset($_REQUEST['mode']))
	{
		if ($_REQUEST['mode']=='bonus')
		{
			$bonus = $_REQUEST['bonus'];
			$sql .= " WHERE ',' || BonusesVisited || ',' LIKE '%,$bonus,%'";
		}
		else if ($_REQUEST['mode']=='special')
		{
			$bonus = $_REQUEST['bonus'];
			$sql .= " WHERE ',' || SpecialsTicked || ',' LIKE '%,$bonus,%'";
		}
		else if ($_REQUEST['mode']=='combo')
		{
			$bonus = $_REQUEST['bonus'];
			$sql .= " WHERE ',' || CombosTicked || ',' LIKE '%,$bonus,%'";
		}
		else if ($_REQUEST['mode']=='find' && isset($_REQUEST['x']) && is_numeric($_REQUEST['x']))
		{
			$n = intval($_REQUEST['x']);
			if (substr($n,0,1) > '0' && strlen($n) <= 3) // Make sure it's reasonable to suppose it's an EntrantID
			$sql .= " WHERE EntrantID=$n";
		}
	}
	if ($ord <> '')
		$sql .= " ORDER BY $ord";
	//echo('<br>listEntrants: '.$sql.'<br>');
	$R = $DB->query($sql);
	
	if (!isset($_REQUEST['mode']))
		$_REQUEST['mode'] = 'full';

	echo('<table id="entrants">');
	$eltag = "EntrantList".ucfirst($_REQUEST['mode']);
	$eltag0 = htmlentities($TAGS[$eltag][0]);
	echo('<caption title="'.htmlentities($TAGS[$eltag][1]).'">'.$eltag0.' '.$bonus);
	if ($eltag0 == '')
		$eltag0 = '..';
	
	$bcurl ='&amp;breadcrumbs='.urlencode($_REQUEST['breadcrumbs']);
	
	
	$myurl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$myurl = "entrants.php?c=entrants";
	if (isset($_REQUEST['mode']))
		$myurl .= '&mode='.$_REQUEST['mode'];
	$myurl .= '&ord='.$ord;
	$p = strpos($myurl,'&nobc');
	if ($p)
		$p = strpos($myurl,'?nobc');
	if ($p)
		$myurl = substr($myurl,0,$p);
	$mybc = "<a href='".$myurl."'>".$eltag0."</a>";
	if (!isset($_REQUEST['nobc']))
		pushBreadcrumb($mybc);
	else
		pushBreadcrumb($mybc);
	$bcurldtl ='&amp;breadcrumbs='.urlencode($_REQUEST['breadcrumbs']);
	emitBreadcrumbs();
	if (isset($_REQUEST['nobc']))
		popBreadcrumb();
	
	
	switch($_REQUEST['mode'])
	{
		case 'full':
		case 'check':
			echo(' <input type="button" value="'.$TAGS['AdmNewEntrant'][0].'" onclick="window.location='."'entrants.php?c=newentrant'".'">');
	}
	echo('</caption>');
	/**
	if ($_REQUEST['mode']=='full')
		echo('<caption title="'.htmlentities($TAGS['EntrantListFull'][1]).'">'.htmlentities($TAGS['EntrantListFull'][0]).'</caption>');
	else if ($_REQUEST['mode']=='bonus')
		echo('<caption title="'.htmlentities($TAGS['EntrantListBonus'][1]).'">'.htmlentities($TAGS['EntrantListBonus'][0]).' '.$bonus.'</caption>');
	else
		echo('<caption title="'.htmlentities($TAGS['EntrantListCheck'][1]).'">'.htmlentities($TAGS['EntrantListCheck'][0]).'</caption>');
	**/
	
	echo('<thead><tr><th class="EntrantID"><a href="entrants.php?c=entrants&amp;ord=EntrantID&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['EntrantID'][0].'</a></th>');
	if ($ord == 'RiderName' || $ord == 'RiderFirst')
		$riderord = 'RiderLast';
	else
		$riderord = 'RiderName';
	echo('<th class="RiderName"><a href="entrants.php?c=entrants&amp;ord='.$riderord.'&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['RiderName'][0].'</a></th>');
	echo('<th class="PillionName"><a href="entrants.php?c=entrants&amp;ord=PillionName&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['PillionName'][0].'</a></th>');
	echo('<th class="Bike"><a href="entrants.php?c=entrants&amp;ord=Bike&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['Bike'][0].'</a></th>');
	if ($ShowTeamCol && $_REQUEST['mode']=='full')
		echo('<th class="TeamID"><a href="entrants.php?c=entrants&amp;ord=TeamID&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['TeamID'][0].'</a></th>');
	echo('<th class="EntrantStatus"><a href="entrants.php?c=entrants&amp;ord=EntrantStatus&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['EntrantStatus'][0].'</a></th>');
	if ($_REQUEST['mode']=='find')
	{
		echo('<th>');
		echo('</th>');
	}
	else if ($_REQUEST['mode']!='check')
	{
		echo('<th class="FinishPosition"><a href="entrants.php?c=entrants&amp;ord=EntrantStatus DESC,FinishPosition&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['FinishPosition'][0].'</a></th>');
		echo('<th class="TotalPoints"><a href="entrants.php?c=entrants&amp;ord=TotalPoints&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['TotalPoints'][0].'</a></th>');
		echo('<th class="CorrectedMiles"><a href="entrants.php?c=entrants&amp;ord=CorrectedMiles&amp;mode='.$_REQUEST['mode'].$bcurl.'">'.$TAGS['CorrectedMiles'][0].'</a></th>');
	}
	echo('</tr>');
	echo('</thead><tbody>');
	
	while ($rd = $R->fetchArray(SQLITE3_ASSOC))
	{
		$show_row = true;
		$found_field = '';
		$found_value = '';
		if ($_REQUEST['mode']=='find')
		{
			$show_row = false;
			foreach ($rd as $rdf=>$rdv)
				if (stripos($rdv,$_REQUEST['x'])!==FALSE)
				{
					$found_field = $rdf;
					$found_value = $rdv;
					$show_row = true;
					break;
				}
			if (!$show_row)
				continue;
			//var_dump($rd);
		}
		$bclast = (isset($_REQUEST['nobc']) ? '' : '');
		
		echo('<tr class="link" onclick="window.location.href=\'entrants.php?c=entrant&amp;id='.$rd['EntrantID'].'&amp;mode='.$_REQUEST['mode'].$bcurldtl.'\'">');
		echo('<td class="EntrantID">'.$rd['EntrantID'].'</td>');
		echo('<td class="RiderName">'.$rd['RiderName'].'</td>');
		echo('<td class="PillionName">'.$rd['PillionName'].'</td>');
		echo('<td class="Bike">'.$rd['Bike'].'</td>');
		if ($ShowTeamCol && $_REQUEST['mode']!='check')
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
		if ($_REQUEST['mode']=='find')
		{
			echo('<td>');
			if ($found_field != 'ExtraData')
				echo($found_field.'=');
			echo($found_value.'</td>');
		}
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
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	$fa1 = array('RiderName','RiderFirst','RiderIBA','PillionName','PillionFirst','PillionIBA',
				'Bike','BikeReg','TeamID','Country','OdoKms','OdoCheckStart','OdoCheckFinish',
				'OdoScaleFactor','OdoRallyStart','OdoRallyFinish','CorrectedMiles','FinishTime',
				'BonusesVisited','SpecialsTicked','CombosTicked','TotalPoints','FinishPosition',
				'EntrantStatus','ScoredBy','StartTime','Class','OdoCheckTrip','ExtraData');

	if ($DBVERSION >= 2) {
		$fa2 = array('Phone','Email','NoKName','NoKRelation','NoKPhone');
		$fa = array_merge($fa1,$fa2);
	} else {
		$fa = $fa1;
	}

	$fab = array('BonusesVisited' => 'BonusID','SpecialsTicked' => 'SpecialID', 'CombosTicked' => 'ComboID');
	
	//var_dump($_REQUEST);
	//echo('<hr>');
	
	//if (isset($_REQUEST['BonusID']))
		//echo(" BonusID ");

	$adding = !isset($_REQUEST['updaterecord']);
	
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
	
//	echo($sql.'<br>');
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}
	
}

function showEntrantBonuses($bonuses,$rejections)
{
	global $DB, $TAGS, $KONSTANTS;

	$ro = ' onclick="return false;" ';
	echo('<p>'.$TAGS['ROUseScore'][1].'</p>');
	$REJ = parseStringArray($rejections,',','=');
	$BA = explode(',',','.$bonuses); // The leading comma means that the first element is index 1 not 0
	$R = $DB->query('SELECT * FROM bonuses ORDER BY BonusID');
	$BP = array();
	while ($rd = $R->fetchArray())
	{
		$BP[$rd['BonusID']] = $rd['BriefDesc'];
	}
	foreach($BP as $bk => $b)
	{
		if ($bk <> '') {
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			echo('<span title="'.htmlspecialchars($b).'"');
			if ($chk) echo(' class="keep checked"'); else if (isset($REJ['B'.$bk]) && $REJ['B'.$bk] != '') echo(' class="rejected"'); else echo(' class="keep"');
			echo('><label for="B'.$bk.'">'.$bk.' </label>');
			echo('<input '.$ro.' type="checkbox"'.$chk.' name="BonusID[]" id="B'.$bk.'" value="'.$bk.'"> ');
			echo('</span>'."\r\n");
		}
	}
}

function parseStringArray($str,$delim1,$delim2)
/*
 * Takes a string containing one or more item, each comprising a key, value pair
 *
 */
{
	$xx = explode($delim1,$str);
	$res = array();
	foreach($xx as $x)
	{
		$kvp = explode($delim2,$x);
		if (count($kvp) > 1)
			$res[$kvp[0]] = $kvp[1];
	}
	return $res;
}

function renumberAllEntrants()
// 
// This will renumber all the entrants into a single contiguous range
// The strategy is make two update passes to avoid problems with PK
// clashes
{
	global $DB, $TAGS, $KONSTANTS;
	
	$firstnum	= $_POST['firstnum'];
	$step		= $_POST['step'];		// No-one's ever going to use step <> 1 but ...
	$order		= $_POST['order'];

	if ($_POST['rusure'] != $KONSTANTS['AreYouSureYes'])
		return;
	
	$rex = [];
	$sql = "SELECT *,substr(RiderName,1,RiderPos-1) As RiderFirst";
	$sql .= ",substr(RiderName,RiderPos+1) As RiderLast";
	$sql .= " FROM (SELECT *,instr(RiderName,' ') As RiderPos FROM entrants) ";
	
	$R = $DB->query("$sql ORDER BY $order");
	$nextnum = $firstnum;
	$hinum = 0;
	while ($rd = $R->fetchArray())
	{
		if ($rd['EntrantID'] > $hinum)
			$hinum = $rd['EntrantID'];
		$rex[$rd['EntrantID']] = $nextnum;
		$nextnum += $step;
	}
	$base = 0;
	while ($firstnum + $base <= $hinum)
		$base += 1000; // Should be big enough
	$DB->exec("START TRANSACTION");
	foreach ($rex as $k => $v)
	{
		$newnumber = $base + $v;
		$DB->exec("UPDATE entrants SET EntrantID=$newnumber WHERE EntrantID=$k");
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			exit;
		}
	}
	
	if ($base > 0)
	{
		foreach ($rex as $k => $v)
		{
			$newnumber = $base + $v;
			$DB->exec("UPDATE entrants SET EntrantID=$v WHERE EntrantID=$newnumber");
			if ($DB->lastErrorCode()<>0) {
				echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
				exit;
			}
		}
	}
	$DB->exec("COMMIT");
}

function renumberEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	$entrantid = $_POST['entrantid'];
	if ($entrantid == '')
		return;
	$newnumber = $_POST['newnumber'];
	if ($newnumber == '' || $newnumber == $entrantid)
		return;
	if (getValueFromDB("SELECT EntrantID FROM entrants WHERE EntrantID=$newnumber","EntrantID",0) != 0)
		return;
	$DB->exec("UPDATE entrants SET EntrantID=$newnumber WHERE EntrantID=$entrantid");
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}

}


function showEntrantRejectedClaims($rejections)
{
	global $DB, $TAGS, $KONSTANTS;

	$R = $DB->query('SELECT RejectReasons FROM rallyparams');
	$rd = $R->fetchArray();
	
	$RRlines = explode("\n",$rd['RejectReasons']);
	//var_dump($RRlines);
	//echo('<hr>');
	//var_dump($rejections);
	//$R->close();
	$RR = array();
	foreach($RRlines as $rrl)
	{
		//var_dump($rrl);
		$x = explode('=',$rrl);
		$RR[$x[0]] = $x[1];
	}
	$BA = explode(',',$rejections); // The leading comma means that the first element is index 1 not 0
	
	//var_dump($BA);

	echo('<ul>');
	foreach($BA as $r)
	{
		//echo(' # '.$r.' ## ');
		$reject = explode('=',$r);
		$bonustype = substr($reject[0],0,1);
		$bonusid = substr($reject[0],1);
		//echo(' [[ '.$r.' - '.$bonustype.' -- '.$bonusid.'  ]] ');
		switch($bonustype)
		{
			case 'B':
				$sql = "SELECT BonusID as bid, BriefDesc as bd FROM bonuses WHERE BonusID='$bonusid'";
				break;
			case 'C':
				$sql = "SELECT ComboID as bd, BriefDesc as bid FROM combinations WHERE ComboID='$bonusid'";
				break;
			case 'S':
				$sql = "SELECT BonusID as bd, BriefDesc as bid FROM specials WHERE BonusID='$bonusid'";
				break;
			case '':
				continue 2; // next foreach
			default:
				echo('<p>OMG</p>');
				var_dump($bonustype);
				return; // don't know what's going on so give up
		}
		$x = $RR[$reject[1]];
		if ($x == '')
			continue;
		$R = $DB->query($sql);
		$rd = $R->fetchArray();
		echo('<li title="'.$rd['bd'].'">');
		echo($rd['bid'].' = '.htmlspecialchars($x).'; ');
		echo('</li>');
	}	
	echo('</ul>');
	
}


function showEntrantScorex($scorex)
{
	global $TAGS;
	
	echo('<div id="scorex" title="'.$TAGS['dblclickprint'][0].'" ondblclick="sxprint();" >');
	echo($scorex);
	echo('</div>');
}


function showFinisherList()
/*
 * quick & dirty list of finishers only
 *
 */
{

	global $DB, $TAGS, $KONSTANTS;

	$sortspec = 'FinishPosition ';
	if (isset($_REQUEST['seq']))
		$sortspec = $_REQUEST['seq'];
	
	$sql = "SELECT *,substr(RiderName,1,RiderPos-1) As RiderFirst";
	$sql .= ",substr(RiderName,RiderPos+1) As RiderLast";
	$sql .= " FROM (SELECT *,instr(RiderName,' ') As RiderPos FROM entrants) ";

	$sql .= " WHERE EntrantStatus==".$KONSTANTS['EntrantFinisher'];
	
	if (isset($_REQUEST['class']))
		$sql .= ' AND Class In ('.$_REQUEST['class'].')';
	$sql .= ' ORDER BY '.$sortspec;
	$R = $DB->query($sql);
?><!DOCTYPE html>
<html>
<head>
<?php
echo('<title>'.$TAGS['ttFinishers'][0].'</title>');
?>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="score.css?ver=<?= filemtime('score.css')?>">
</head>
<body>
<?php
	echo('<table class="qdfinishers">');
	echo('<thead><tr>');
	echo('<th>'.$TAGS['qPlace'][0].'</th>');
	echo('<th>'.$TAGS['qName'][0].'</th>');
	echo('<th>'.$TAGS['qMiles'][0].'</th>');
	echo('<th>'.$TAGS['qPoints'][0].'</th>');
	
	echo('</tr></thead><tbody>');
	$n = 0;
	while ($rd = $R->fetchArray())
	{
		echo('<tr>');
		echo('<td>'.$rd['FinishPosition'].'</td>');
		echo('<td>'.$rd['RiderName']);
		if ($rd['PillionName'] > '')
			echo(' & '.$rd['PillionName']);
		echo('</td>');
		echo('<td>'.$rd['CorrectedMiles'].'</td>');
		echo('<td>'.$rd['TotalPoints'].'</td>');
		echo('</tr>');
		$n++;
	}
	echo('</tbody></table>');
	if ($n < 1)
		echo('<p>'.$TAGS['NoCerts2Print'][0].'</p>');
	echo('<p>&nbsp;</p>'); //Spacer to facilitate screen capture
?>
</body>
</html>
<?php	
}
















function showAllScorex()
{
	global $DB, $TAGS, $KONSTANTS;

	$R = $DB->query("SELECT RallyTitle FROM rallyparams");
	$rd = $R->fetchArray();
	$title = htmlspecialchars(preg_replace('/\[|\]|\|/','',$rd['RallyTitle']));

	$sortspec = 'RiderLast ';
	if (isset($_REQUEST['seq']))
		$sortspec = $_REQUEST['seq'];
	
	$sql = "SELECT *,substr(RiderName,1,RiderPos-1) As RiderFirst";
	$sql .= ",substr(RiderName,RiderPos+1) As RiderLast";
	$sql .= " FROM (SELECT *,instr(RiderName,' ') As RiderPos FROM entrants) ";

	$sql .= " WHERE EntrantStatus<>".$KONSTANTS['EntrantDNS'];
	
	$sql .= " AND ScoreX Is Not Null";

	if (isset($_REQUEST['class']))
		$sql .= ' AND Class In ('.$_REQUEST['class'].')';
	if (isset($_REQUEST['entrant']))
		$sql .= ' AND EntrantID In ('.$_REQUEST['entrant'].')';
	$sql .= ' ORDER BY '.$sortspec;
	$R = $DB->query($sql);
?><!DOCTYPE html>
<html>
<head>
<?php
echo('<title>'.$TAGS['ttScoreX'][0].'</title>');
?>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="score.css?ver=<?= filemtime('score.css')?>">
</head>
<body>
<?php
	$n = 0;
	while ($rd = $R->fetchArray())
	{
		echo('<h1 class="center">'.$title.'</h1>');
		echo('<div class="scorex">');
		echo($rd['ScoreX']);
		echo('</div>');
		$n++;
	}
	if ($n < 1)
		echo('<p>'.$TAGS['NoScoreX2Print'][0].'</p>');
?>
</body>
</html>
<?php	
}


function showDeleteEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	echo('<div class="maindiv">');
	echo('<form method="post" action="entrants.php">');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<input type="hidden" name="c" value="kill">');
	echo('<span class="vlabel" title="'.$TAGS['ChooseEntrant'][1].'">');
	echo('<select id="entrantid" name="entrantid">');
	echo('<option value="">'.$TAGS['ChooseEntrant'][0].'</option>');
	$R = $DB->query("SELECT * FROM entrants ORDER BY EntrantID");
	while ($rd = $R->fetchArray())
	{
		echo('<option value="'.$rd['EntrantID'].'">'.$rd['EntrantID'].' - '.$rd['RiderName'].'</option>');
	}
	echo('</select>');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['ConfirmDelEntrant'][1].'">');
	echo('<label class="wide" for="rusure">'.$TAGS['ConfirmDelEntrant'][0].'</label> ');
	echo('<input type="checkbox" id="rusure" name="rusure" value="'.$KONSTANTS['AreYouSureYes'].'">');
	echo('</span>');
	echo('<span class="vlabel">');
	echo('<input type="submit" onclick="'."alert('hello sailor')".' name="killer" value="'.$TAGS['DeleteEntrant'][0].'">');
	echo('</span>');
	echo('</form>');
	echo('</div>');
}

function showRAE()
{
	global $DB, $TAGS, $KONSTANTS;

	echo('<div class="maindiv">');
	echo('<form method="post" action="entrants.php">');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<input type="hidden" name="c" value="rae">');
	echo('<input type="hidden" name="step" value="1">');
	echo('<input type="hidden" name="seq" value="">'); // ascending/descending
	echo('<span class="vlabel" title="'.$TAGS['raeFirst'][1].'">');
	echo('<label for="firstnum">'.$TAGS['raeFirst'][0].'</label> ');
	echo('<input type="number" id="firstnum" name="firstnum" value="1">');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['raeOrder'][1].'">');
	echo('<label for="order">'.$TAGS['raeOrder'][0].'</label> ');
	echo('<select id="order" name="order">');
	echo('<option selected value="EntrantID">'.$TAGS['EntrantID'][0].'</option>');
	echo('<option value="RiderLast">'.$TAGS['raeRiderLast'][0].'</option>');
	echo('<option value="RiderFirst">'.$TAGS['raeRiderFirst'][0].'</option>');
	echo('<option value="random()">'.$TAGS['raeRandom'][0].'</option>');
	echo('</select> ');
	
	echo('<span title="'.$TAGS['raeSortA'][1].'">');
	echo('<label for="seqasc">'.$TAGS['raeSortA'][0].'</label> ');
	echo('<input type="radio" id="seqasc" name="seq" checked value="">  ');
	echo('</span>');
	echo('<span title="'.$TAGS['raeSortD'][1].'">');
	echo('<label for="seqdes">'.$TAGS['raeSortD'][0].'</label> ');
	echo('<input type="radio" id="seqdes" name="seq" value=" DESC">');
	echo('</span>');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['raeConfirm'][1].'">');
	echo('<label class="wide" for="rusure">'.$TAGS['raeConfirm'][0].'</label> ');
	echo('<input type="checkbox" id="rusure" name="rusure" value="'.$KONSTANTS['AreYouSureYes'].'">');
	echo('</span>');
	echo('<span class="vlabel">');
	echo('<input type="submit" name="killer" value="'.$TAGS['raeSubmit'][0].'">');
	echo('</span>');
	echo('</form>');
	echo('</div>');
	
}



function showRenumberEntrant()
{
	global $DB, $TAGS, $KONSTANTS;

	echo('<div class="maindiv">');
	echo('<form method="post" action="entrants.php">');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<input type="hidden" name="c" value="renumentrant">');
	echo('<span class="vlabel" title="'.$TAGS['ChooseEntrant'][1].'">');
	echo('<select id="entrantid" name="entrantid">');
	echo('<option value="">'.$TAGS['ChooseEntrant'][0].'</option>');
	$R = $DB->query("SELECT * FROM entrants ORDER BY EntrantID");
	while ($rd = $R->fetchArray())
	{
		echo('<option value="'.$rd['EntrantID'].'">'.$rd['EntrantID'].' - '.$rd['RiderName'].'</option>');
	}
	echo('</select>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NewEntrantNum'][1].'">');
	echo('<label for="newnumber">'.$TAGS['NewEntrantNum'][0].'</label> ');
	echo('<input type="number" id="newnumber" name="newnumber" value="">');
	echo('</span>');
	echo('<span class="vlabel">');
	echo('<input type="submit" name="killer" value="'.$TAGS['RenumberGo'][0].'">');
	echo('</span>');
	echo('</form>');
	echo('</div>');
}



/* Check-in/check-out stuff */
function showEntrantChecks($rd)
{
	global $DB, $TAGS, $KONSTANTS;

	echo('<form method="post" action="entrants.php">');
	pushBreadcrumb('#');
	emitBreadcrumbs();

	echo('<input type="hidden" name="c" value="entrants">');
	echo('<input type="hidden" name="mode" value="check">');
	echo('<input type="hidden" name="updaterecord" value="'.$rd['EntrantID'].'">');
	
	echo('<span class="vlabel"  style="font-weight: bold;" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].' </label> ');
	echo('<input type="text" class="number"  readonly name="EntrantID" id="EntrantID" value="'.$rd['EntrantID'].'">'.' '.htmlspecialchars($rd['RiderName']));
	
	popBreadcrumb();
	echo('<input title="'.$TAGS['FullDetails'][1].'" id="FullDetailsButton" type="button" value="'.$TAGS['FullDetails'][0].'"');
	echo(' onclick="window.location='."'entrants.php?c=entrant&amp;id=".$rd['EntrantID']."&mode=full");
	echo('&breadcrumbs='.urlencode($_REQUEST['breadcrumbs']));
	echo("'".'"> ');
	
	echo('<input type="submit" name="savedata" value="'.$TAGS['SaveEntrantRecord'][0].'">');
	echo('</span>');

	
	
	
	
	
	
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
	$chk = $rd['OdoKms'] <> $KONSTANTS['OdoCountsKilometres'] ? ' checked="checked" ' : '';
	echo('<input onchange="odoAdjust();" type="radio" name="OdoKms" id="OdoKmsM" value="'.$KONSTANTS['OdoCountsMiles'].'"'.$chk.'></span>');
	echo('&nbsp;&nbsp;&nbsp;<span><label for="OdoKmsK">'.$TAGS['OdoKmsK'][0].' </label> ');
	$chk = $rd['OdoKms'] == $KONSTANTS['OdoCountsKilometres'] ? ' checked="checked" ' : '';
	echo('<input  onchange="odoAdjust();" type="radio" name="OdoKms" id="OdoKmsK" value="'.$KONSTANTS['OdoCountsKilometres'].'"'.$chk.'></span>');

	if ($hideOdoCheck)
		echo('<div style="display:none;">');
	echo('<span  class="xlabel" title="'.$TAGS['OdoCheckStart'][1].' "><label for="OdoCheckStart">'.$TAGS['OdoCheckStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust(false);" type="number" step="any" name="OdoCheckStart" id="OdoCheckStart" value="'.$rd['OdoCheckStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoCheckFinish'][1].' "><label for="OdoCheckFinish">'.$TAGS['OdoCheckFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust(false);" type="number" step="any" name="OdoCheckFinish" id="OdoCheckFinish" value="'.$rd['OdoCheckFinish'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoCheckTrip'][1].' "><label for="OdoCheckTrip">'.$TAGS['OdoCheckTrip'][0].' </label> ');
	echo('<input  onchange="odoAdjust(true);" type="number" step="any" name="OdoCheckTrip" id="OdoCheckTrip" value="'.$rd['OdoCheckTrip'].'"> </span>');
	
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
	
	
	echo('</form>');
		
}






















function showEntrantSpecials($specials,$rejections)
{
	global $DB, $TAGS, $KONSTANTS;

	$ro = ' onclick="return false;" ';
	echo('<p>'.$TAGS['ROUseScore'][1].'</p>');
	$REJ = parseStringArray($rejections,',','=');
	$BA = explode(',',','.$specials); // The leading comma means that the first element is index 1 not 0

	$R = $DB->query('SELECT * FROM specials ORDER BY BonusID');
	while ($rd = $R->fetchArray())
	{
		$BP[$rd['BonusID']] = $rd['BriefDesc'];
	}
	echo('<span  class="xlabel" ></span>');
	if (isset($BP))
		foreach($BP as $bk => $b)
		{
			if ($bk <> '') {
				$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
				echo('<span title="'.htmlspecialchars($bk).'"');
				if ($chk) echo(' class="keep checked"'); else if (isset($REJ['S'.$bk]) && $REJ['S'.$bk] != '') echo(' class="rejected"'); else echo(' class="keep"');
				echo('><label for="S'.$bk.'">'.htmlspecialchars($b).' </label>');
				echo('<input '.$ro.' type="checkbox"'.$chk.' name="SpecialID[]" id="S'.$bk.'" value="'.$bk.'"> ');
				echo(' &nbsp;&nbsp;</span>');
			}
		}
}

function showEntrantCombinations($Combos,$rejections)
{
	global $DB, $TAGS, $KONSTANTS;
	
	$ro = ' onclick="return false;" ';
	echo('<p>'.$TAGS['ROUseScore'][1].'</p>');

	$REJ = parseStringArray($rejections,',','=');
	$BAB = explode(',',','.$Combos); // The leading comma means that the first element is index 1 not 0
	
	$R = $DB->query('SELECT * FROM combinations ORDER BY ComboID');
	$BA = array();
	while ($rd = $R->fetchArray())
	{
		$BA[$rd['ComboID']] = $rd['BriefDesc'];
	}
	echo('<span  class="xlabel" ></span>');
	//var_dump($BA);
	foreach($BA as $bk => $b)
	{
		if ($bk <> '') {
			$chk = array_search($bk, $BAB) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			echo('<span title="'.htmlspecialchars($bk).'"');
			if ($chk) echo(' class="keep checked"'); else if (isset($REJ['C'.$bk]) && $REJ['C'.$bk] != '') echo(' class="rejected"'); else echo(' class="keep"');
			echo('><label for="C'.$bk.'">'.htmlspecialchars($b).' </label>');
			echo('<input '.$ro.' type="checkbox"'.$chk.' name="ComboID[]" id="C'.$bk.'" value="'.$bk.'"> ');
			echo(' &nbsp;&nbsp;</span>');
		}
	}
}

function showEntrantExtraData($xd)
{
	global $DB, $TAGS, $KONSTANTS;

	$rows = substr_count($xd,"\n") + 1;
	echo('<p>'.$TAGS['ExtraData'][1].'</p>');
	echo('<textarea onchange="enableSaveButton();" name="ExtraData" style="width:100%;" rows="'.$rows.'">'.$xd.'</textarea>');
}


function showEntrantRecord($rd)
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
//var_dump($rd);
	$is_new_record = ($rd['EntrantID']=='');
	echo('<form method="post" action="entrants.php">');

	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" name="c" value="entrants">');
	echo('<span class="vlabel"  style="font-weight: bold;" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].' </label> ');
	if ($is_new_record)
	{
		$ro = '';
		$rd['OdoKms'] = $KONSTANTS['DefaultKmsOdo'];
	}
	else
		$ro = ' readonly ';
	echo('<input type="text" onchange="enableSaveButton();"  class="number"  '.$ro.' name="EntrantID" id="EntrantID" value="'.$rd['EntrantID'].'">');
	echo(' '.htmlspecialchars($rd['RiderName']).' ');
	if (!$is_new_record)
	{
		echo('<input type="hidden" name="updaterecord" value="'.$rd['EntrantID'].'">');
		echo('<input title="'.$TAGS['ScoreNow'][1].'" id="ScoreNowButton" type="button" value="'.$TAGS['ScoreNow'][0].'"');
		echo(' onclick="window.open('."'score.php?c=score&amp;EntrantID=".$rd['EntrantID']."','score'".')" >');
	}
	if ($rd['RiderName'] <> '')
		$dis = '';
	else
		;
		$dis = ' disabled ';
	echo('<input type="submit"'.$dis.' id="savedata" name="savedata" value="'.$TAGS['RecordSaved'][0].'" data-altvalue="'.$TAGS['SaveEntrantRecord'][0].'">');
	echo('</span> ');
	
	echo('<div class="tabs_area" style="display:inherit"><ul id="tabs">');
	echo('<li><a href="#tab_basic">'.$TAGS['BasicDetails'][0].'</a></li>');
	if ($DBVERSION >= 2)
		echo('<li><a href="#tab_contact">'.$TAGS['ContactDetails'][0].'</a></li>');
	echo('<li><a href="#tab_odo">'.$TAGS['Odometer'][0].'</a></li>');
	echo('<li><a href="#tab_results">'.$TAGS['RallyResults'][0].'</a></li>');
	if (!$is_new_record)
	{
		echo('<li><a href="#tab_bonuses">'.$TAGS['BonusesLit'][0].'</a></li>');
		echo('<li><a href="#tab_specials">'.$TAGS['SpecialsLit'][0].'</a></li>');
		echo('<li><a href="#tab_combos">'.$TAGS['CombosLit'][0].'</a></li>');
		echo('<li><a href="#tab_rejects">'.$TAGS['RejectsLit'][0].'</a></li>');
		echo('<li><a href="#tab_scorex">'.$TAGS['ScorexLit'][0].'</a></li>');
		echo('<li><a href="#tab_xtra">'.$TAGS['ExtraData'][0].'</a></li>');
	}
	echo('</ul></div>');
	
	
	
	echo('<fieldset class="tabContent" id="tab_basic"><legend>'.$TAGS['BasicDetails'][0].'</legend>');
	echo('<span class="xlabel" title="'.$TAGS['RiderName'][1].'"><label for="RiderName">'.$TAGS['RiderName'][0].' </label> ');
	$blurJS = "var f=document.getElementById('RiderFirst');if (f.value=='') {var n=document.getElementById('RiderName').value.split(' ');f.value=n[0];}";
	echo('<input autofocus type="text" onchange="enableSaveButton();" onblur="'.$blurJS.'" name="RiderName" id="RiderName" value="'.htmlspecialchars($rd['RiderName']).'"> </span>');
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
	
	if ($DBVERSION >= 2)
	{
		echo('<fieldset  class="tabContent" id="tab_contact"><legend>'.$TAGS['ContactDetails'][0].'</legend>');
		
		echo('<span class="vlabel" title="'.$TAGS['EntrantPhone'][1].'"><label for="Phone">'.$TAGS['EntrantPhone'][0].' </label> ');
		echo('<input type="tel"  onchange="enableSaveButton();" name="Phone" id="Phone" value="'.$rd['Phone'].'"> </span>');
	
		echo('<span class="vlabel" title="'.$TAGS['EntrantEmail'][1].'"><label for="Email">'.$TAGS['EntrantEmail'][0].' </label> ');
		echo('<input type="email"  onchange="enableSaveButton();" name="Email" id="Email" value="'.$rd['Email'].'"> </span>');
	
		echo('<span class="vlabel" title="'.$TAGS['NoKName'][1].'"><label for="NoKName">'.$TAGS['NoKName'][0].' </label> ');
		echo('<input type="text"  onchange="enableSaveButton();" name="NoKName" id="NoKName" value="'.$rd['NoKName'].'"> </span>');
	
		echo('<span class="vlabel" title="'.$TAGS['NoKRelation'][1].'"><label for="NoKRelation">'.$TAGS['NoKRelation'][0].' </label> ');
		echo('<input type="text"  onchange="enableSaveButton();" name="NoKRelation" id="NoKRelation" value="'.$rd['NoKRelation'].'"> </span>');
	
		echo('<span class="vlabel" title="'.$TAGS['NoKPhone'][1].'"><label for="NoKPhone">'.$TAGS['NoKPhone'][0].' </label> ');
		echo('<input type="tel"  onchange="enableSaveButton();" name="NoKPhone" id="NoKPhone" value="'.$rd['NoKPhone'].'"> </span>');
	
		echo('</fieldset>');
	}
	
	
	echo('<fieldset  class="tabContent" id="tab_odo"><legend>'.$TAGS['Odometer'][0].'</legend>');
	
	$odoF = $DB->query("SELECT OdoCheckMiles FROM rallyparams");
	$odoC = $odoF->fetchArray();
	
	echo('<input type="hidden" name="OdoCheckMiles" id="OdoCheckMiles" value="'.$odoC['OdoCheckMiles'].'">');

	echo('<span  class="xlabel" title="'.$TAGS['OdoKms'][1].' "> '.$TAGS['OdoKms'][0].' ');
	echo('<label for="OdoKmsM">'.$TAGS['OdoKmsM'][0].': </label> ');
	$chk = $rd['OdoKms'] <> $KONSTANTS['OdoCountsKilometres'] ? ' checked="checked" ' : '';
	echo('<input onchange="odoAdjust();enableSaveButton();" type="radio" name="OdoKms" id="OdoKmsM" value="'.$KONSTANTS['OdoCountsMiles'].'"'.$chk.'></span>');
	echo('&nbsp;&nbsp;&nbsp;<span><label for="OdoKmsK">'.$TAGS['OdoKmsK'][0].' </label> ');
	$chk = $rd['OdoKms'] == $KONSTANTS['OdoCountsKilometres'] ? ' checked="checked" ' : '';
	echo('<input  onchange="odoAdjust();enableSaveButton();" type="radio" name="OdoKms" id="OdoKmsK" value="'.$KONSTANTS['OdoCountsKilometres'].'"'.$chk.'></span>');

	echo('<span  class="xlabel" title="'.$TAGS['OdoCheckStart'][1].' "><label for="OdoCheckStart">'.$TAGS['OdoCheckStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust();enableSaveButton();" type="number" step="any" name="OdoCheckStart" id="OdoCheckStart" value="'.$rd['OdoCheckStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoCheckFinish'][1].' "><label for="OdoCheckFinish">'.$TAGS['OdoCheckFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust();enableSaveButton();" type="number" step="any" name="OdoCheckFinish" id="OdoCheckFinish" value="'.$rd['OdoCheckFinish'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoCheckTrip'][1].' "><label for="OdoCheckTrip">'.$TAGS['OdoCheckTrip'][0].' </label> ');
	echo('<input  onchange="odoAdjust(true);enableSaveButton();" type="number" step="any" name="OdoCheckTrip" id="OdoCheckTrip" value="'.$rd['OdoCheckTrip'].'"> </span>');

	echo('<span  class="xlabel" title="'.$TAGS['OdoScaleFactor'][1].'"><label for="OdoScaleFactor">'.$TAGS['OdoScaleFactor'][0].' </label> ');
	echo('<input type="number" step="any" name="OdoScaleFactor" id="OdoScaleFactor"  onchange="enableSaveButton();" value="'.$rd['OdoScaleFactor'].'"> </span>');
	
	echo('<span  class="xlabel" title="'.$TAGS['OdoRallyStart'][1].' "><label for="OdoRallyStart">'.$TAGS['OdoRallyStart'][0].' </label> ');
	echo('<input  onchange="odoAdjust();enableSaveButton();" type="number" step="any" name="OdoRallyStart" id="OdoRallyStart" value="'.$rd['OdoRallyStart'].'"> </span>');
	
	echo('<span  title="'.$TAGS['OdoRallyFinish'][1].' "><label for="OdoRallyFinish">'.$TAGS['OdoRallyFinish'][0].' </label> ');
	echo('<input  onchange="odoAdjust();enableSaveButton();" type="number" step="any" name="OdoRallyFinish" id="OdoRallyFinish" value="'.$rd['OdoRallyFinish'].'"> </span>');
	
	
	echo('</fieldset>');

	
	echo('<fieldset  class="tabContent" id="tab_results"><legend>'.$TAGS['RallyResults'][0].'</legend>');
	echo('<span  class="xlabel" title="'.$TAGS['EntrantStatus'][1].'"><label for="EntrantStatus">'.$TAGS['EntrantStatus'][0].' </label>');
	echo('<select name="EntrantStatus" id="EntrantStatus" onchange="enableSaveButton();">');
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
	echo(' <input type="date" name="StartDate" id="StartDate" onchange="enableSaveButton();" value="'.$dt[0].'" title="'.$TAGS['StartDate'][1].'"> ');
	echo('<label for="StartTime">'.$TAGS['StartTime'][0].' </label>');
	echo(' <input type="time" name="StartTime" id="StartTime" onchange="enableSaveButton();" value="'.$dt[1].'" title="'.$TAGS['StartTime'][1].'"> ');
	echo('</span>');

	$dt = splitDatetime($rd['FinishTime']); 

	echo('<span class="vlabel">');
	echo('<label for="FinishDate" class="vlabel">'.$TAGS['FinishDate'][0].' </label>');
	echo(' <input type="date" name="FinishDate" id="FinishDate" value="'.$dt[0].'" onchange="enableSaveButton();" title="'.$TAGS['FinishDate'][1].'"> ');
	echo('<label for="FinishTime">'.$TAGS['FinishTime'][0].' </label>');
	echo(' <input type="time" name="FinishTime" id="FinishTime" value="'.$dt[1].'" onchange="enableSaveButton();" title="'.$TAGS['FinishTime'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="CorrectedMiles" class="vlabel">'.$TAGS['CorrectedMiles'][0].' </label>');
	echo(' <input type="number" name="CorrectedMiles" id="CorrectedMiles" value="'.$rd['CorrectedMiles'].'" onchange="enableSaveButton();" title="'.$TAGS['CorrectedMiles'][1].'"> ');
	echo('</span>');
	
	echo('<span class="vlabel">');
	echo('<label for="TotalPoints" class="vlabel">'.$TAGS['TotalPoints'][0].' </label>');
	echo(' <input type="number" name="TotalPoints" id="TotalPoints" onchange="enableSaveButton();" value="'.$rd['TotalPoints'].'" title="'.$TAGS['TotalPoints'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="FinishPosition" class="vlabel">'.$TAGS['FinishPosition'][0].' </label>');
	echo(' <input type="number" name="FinishPosition" id="FinishPosition" onchange="enableSaveButton();" value="'.$rd['FinishPosition'].'" title="'.$TAGS['FinishPosition'][1].'"> ');
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
	
	if (!$is_new_record)
	{
		echo('<fieldset  class="tabContent" id="tab_bonuses"><legend>'.$TAGS['BonusesLit'][0].'</legend>');
		showEntrantBonuses($rd['BonusesVisited'],$rd['RejectedClaims']);
		echo('<!-- B --> </fieldset>');
		echo('<fieldset  class="tabContent" id="tab_specials"><legend>'.$TAGS['SpecialsLit'][0].'</legend>');
		showEntrantSpecials($rd['SpecialsTicked'],$rd['RejectedClaims']);
		echo('</fieldset>');
		echo('<fieldset  class="tabContent" id="tab_combos"><legend>'.$TAGS['CombosLit'][0].'</legend>');
		showEntrantCombinations($rd['CombosTicked'],$rd['RejectedClaims']);
		echo('</fieldset>');
		echo('<fieldset  class="tabContent" id="tab_rejects"><legend>'.$TAGS['RejectsLit'][0].'</legend>');
		showEntrantRejectedClaims($rd['RejectedClaims']);
		echo('</fieldset>');
		echo('<fieldset  class="tabContent" id="tab_scorex"><legend>'.$TAGS['ScorexLit'][0].'</legend>');
		showEntrantScorex($rd['ScoreX']);
		echo('</fieldset>');
		echo('<fieldset  class="tabContent" id="tab_xtra"><legend>'.$TAGS['ExtraData'][0].'</legend>');
		showEntrantExtraData($rd['ExtraData']);
		echo('</fieldset>');

	}
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

	if (isset($_REQUEST['c']) && $_REQUEST['c']=='scorex')
	{
		showAllScorex();
		exit;
	}

	if (isset($_REQUEST['c']) && $_REQUEST['c']=='qlist')
	{
		showFinisherList();
		exit;
	}


startHtml($TAGS['ttEntrants'][0]);
//echo(htmlspecialchars($_REQUEST['breadcrumbs']));

if (isset($_REQUEST['savedata']))
{
	saveEntrantRecord();
	if (retraceBreadcrumb())
		;//exit;
	
}


if (isset($_POST['c']) && $_POST['c']=='kill')
{
	deleteEntrant();
	listEntrants();
}
else if (isset($_POST['c']) && $_POST['c']=='rae')
{
	renumberAllEntrants();
	listEntrants();
}
else if (isset($_POST['c']) && $_POST['c']=='renumentrant')
{
	renumberEntrant();
	listEntrants();
}
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='showrae')
	showRAE();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='moveentrant')
	showRenumberEntrant();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='delentrant')
	showDeleteEntrant();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='entrant')
	fetchShowEntrant();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='newentrant')
	showNewEntrant();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='entrants')
	listEntrants(isset($_REQUEST['ord']) ? $_REQUEST['ord'] : '');


?>

