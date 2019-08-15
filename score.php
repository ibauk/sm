<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle the scoring end of things, formatting the scoresheets and recording the results
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


$HOME_URL = "score.php";

/*
 *	2.1	Use update_bonuses/combos/specials to update when nothing ticked
 *	2.1	Show PillionName on scoresheet
 *	2.1	Use '-' instead of space between label and bonus checkbox
 *	2.1	Multiple radio groups of specials
 *	2.1 RejectedClaims handling
  *	2.1 Odo check trip reading - used though not stored
 *
 */

require_once('common.php');

// If ShowMults is automatic, I decide.
function chooseShowMults($ScoringMethod)
{
	global $DB, $TAGS, $KONSTANTS;
	
	if ($ScoringMethod <> $KONSTANTS['CompoundScoring'])                              
		return $KONSTANTS['SuppressMults'];
	
	$R = $DB->query("SELECT Count(*) AS Rex FROM catcompound WHERE PointsMults=".$KONSTANTS['ComboScoreMethodMults']);
	$rd = $R->fetchArray();
	if ($rd['Rex'] > 0)
		return $KONSTANTS['ShowMults'];
	$R = $DB->query("SELECT Count(*) AS Rex FROM combinations WHERE ScoreMethod=".$KONSTANTS['ComboScoreMethodMults']);
	$rd = $R->fetchArray();
	if ($rd['Rex'] > 0)
		return $KONSTANTS['ShowMults'];
	$R = $DB->query("SELECT Count(*) AS Rex FROM specials WHERE MultFactor<> 0");
	$rd = $R->fetchArray();
	if ($rd['Rex'] > 0)
		return $KONSTANTS['ShowMults'];
	
	return $KONSTANTS['SuppressMults'];
}


// If ScoringMethod is automatic, I choose what to do
function chooseScoringMethod()
{
	global $DB, $TAGS, $KONSTANTS;
	
	$R = $DB->query("SELECT Count(*) AS Rex FROM catcompound");
	$rd = $R->fetchArray();
	if ($rd['Rex'] > 0)
		return $KONSTANTS['CompoundScoring'];	

	$R = $DB->query("SELECT Count(*) AS Rex FROM bonuses");
	$rd = $R->fetchArray();
	if ($rd['Rex'] > 0)
		return $KONSTANTS['SimpleScoring'];
	
	$R = $DB->query("SELECT Count(*) AS Rex FROM specials");
	$rd = $R->fetchArray();
	if ($rd['Rex'] > 0)
		return $KONSTANTS['SimpleScoring'];
	
	return $KONSTANTS['ManualScoring'];
	
	
}


/* This calculates a 'safe' default finishing time to be used until and
 * entrant has her actual time entered. This time should not affect
 * the entrant's score or finisher status.
 */
function defaultFinishTime()
{
	global $DB;

	$notime = ['',''];
	$R = $DB->query("SELECT FinishTime FROM rallyparams");
	if (!$R)
		return $notime;
	$RD = $R->fetchArray();
	$dtx = splitdatetime($RD['FinishTime']);
	$finishTime = $RD['FinishTime'];
	$R = $DB->query("SELECT PenaltyStart FROM timepenalties ORDER BY PenaltyStart");
	if ($R)
	{
		if ($RD = $R->fetchArray())
		{
			$finishTime = $RD['PenaltyStart'];
		}
	}
	// Make it one minute earlier
	if ($finishTime != '')
	{
		//echo("<br>\r\nFinishTime={".$finishTime."} ");
		$finishTime = date_sub(DateTime::createFromFormat('Y-m-d\TH:i',$finishTime),new DateInterval('PT1M'))->format('Y-m-d H:i');
	}
	
	$res = splitDatetime($finishTime);

	return $res;
}
	


function inviteScorer()
{
	global $DB, $TAGS, $KONSTANTS;
	
	$rally = getValueFromDB('SELECT RallyTitle FROM rallyparams','RallyTitle','');
	
	startHtml($TAGS['ttWelcome'][0]);
	echo('<div id="frontpage"><p>'.$TAGS['OfferScore'][1].'</p>');
	echo('<form method="post" action="score.php">');
	echo('<input type="text" autofocus name="ScorerName" value="'.$KONSTANTS['DefaultScorer'].'" onfocus="this.select();">');
	echo('<input type="submit" name="login" value="'.$TAGS['login'][1].'">');
	echo('</form></div>');
	showFooter();
	
}

function loginNewScorer()
{
	global $DB, $TAGS, $KONSTANTS;

	$_REQUEST['ScorerName'] = ucwords(strtolower($_REQUEST['ScorerName']));
	showPicklist('EntrantID');
}


function putScore()
{
	global $DB, $TAGS, $KONSTANTS;

	//var_dump($_REQUEST);
	
	$sql = "UPDATE entrants SET ScoredBy='".$DB->escapeString($_REQUEST['ScorerName'])."'";
	
	$sql .= ",ScoringNow=0";	// Score's being saved so probably not continuing to be scored
	
	if (isset($_REQUEST['OdoCheckStart']))
		$sql .= ",OdoCheckStart=".floatval($_REQUEST['OdoCheckStart']);
	if (isset($_REQUEST['OdoCheckFinish']))
		$sql .= ",OdoCheckFinish=".floatval($_REQUEST['OdoCheckFinish']);
	if (isset($_REQUEST['OdoScaleFactor']))
		$sql .= ",OdoScaleFactor=".floatval($_REQUEST['OdoScaleFactor']);
	if (isset($_REQUEST['OdoRallyStart']))
		$sql .= ",OdoRallyStart=".floatval($_REQUEST['OdoRallyStart']);
	if (isset($_REQUEST['OdoRallyFinish']))
		$sql .= ",OdoRallyFinish=".floatval($_REQUEST['OdoRallyFinish']);
	if (isset($_REQUEST['CorrectedMiles']))
		$sql .= ",CorrectedMiles=".intval($_REQUEST['CorrectedMiles']);
	if (isset($_REQUEST['FinishTime']))
			$sql .= ",FinishTime='".$DB->escapeString($_REQUEST['FinishDate']).'T'.$DB->escapeString($_REQUEST['FinishTime'])."'";
	if (isset($_REQUEST['BonusID']) || isset($_REQUEST['update_bonuses'])) 
		$sql .= ",BonusesVisited='".implode(',',$_REQUEST['BonusID'])."'";
	if (isset($_REQUEST['SpecialID']) || isset($_REQUEST['update_specials']))
		//$sql .= ",SpecialsTicked='".implode(',',$_REQUEST['SpecialID'])."'";
		$sql .= saveSpecials();
	if (isset($_REQUEST['ComboID']) || isset($_REQUEST['update_combos']))
		$sql .= ",CombosTicked='".implode(',',$_REQUEST['ComboID'])."'";
	if (isset($_REQUEST['TotalPoints']))
		$sql .= ",TotalPoints=".intval(str_replace(',','',$_REQUEST['TotalPoints']));
	if (isset($_REQUEST['StartTime']))
		$sql .= ",StartTime='".$DB->escapeString($_REQUEST['StartDate']).'T'.$DB->escapeString($_REQUEST['StartTime'])."'";
	if (isset($_REQUEST['FinishPosition']))
		$sql .= ",FinishPosition=".intval($_REQUEST['FinishPosition']);
	if (isset($_REQUEST['EntrantStatus']))
		$sql .= ",EntrantStatus=".intval($_REQUEST['EntrantStatus']);
	if (isset($_REQUEST['ScoreX']))
		$sql .= ",ScoreX='".$DB->escapeString($_REQUEST['ScoreX'])."'";
	if (isset($_REQUEST['RejectedClaims']))
		$sql .= ",RejectedClaims='".$DB->escapeString($_REQUEST['RejectedClaims'])."'";
	$sql .= " WHERE EntrantID=".$_REQUEST['EntrantID'];
	
	//echo('<hr>'.$sql.'<hr>');
	$DB->exec($sql);
	if (($res = $DB->lastErrorCode()) <> 0)
		echo('ERROR: '.$DB->lastErrorMsg().'<br />'.$sql.'<hr>');
	
}

function saveSpecials()
{
//	print_r($_REQUEST);
	if (isset($_REQUEST['SpecialID']))
		$sv = implode(',',$_REQUEST['SpecialID']);
	else
		$sv = '';
	$sg = explode(',',$_REQUEST['SGroupsUsed']);
	foreach ($sg as $g)
	{
		if ($sv <> '')
			$sv .= ',';
		if (isset($_REQUEST['SpecialID'.'_'.$g]))
			$sv .= implode($_REQUEST['SpecialID'.'_'.$g]);
	}
	$TickedSpecials = explode(',',$sv);
	foreach ($TickedSpecials as $ts)
		if (isset($_REQUEST['apS'.$ts]))
		{
			array_push($TickedSpecials,$ts.'='.$_REQUEST['apS'.$ts]);
			unset($TickedSpecials[$ts]);
		}
	$sv = implode(',',$TickedSpecials);
	return ",SpecialsTicked='".$sv."'";
}

function scoreEntrant($showBlankForm = FALSE)
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	
	if (!$showBlankForm) 
	{
		$sql = 'SELECT * FROM entrants';
		$sql .= ' WHERE EntrantID='.$_REQUEST['EntrantID'];
		$R = $DB->query($sql);
		$rd = $R->fetchArray();
	
		if (!$rd)
		{
			showPicklist('EntrantID');
			exit;
		}
		$ScorerName = (isset($_REQUEST['ScorerName']) ? $_REQUEST['ScorerName'] : '');
	}
	else
	{
		$ScorerName = '__________';
	}
	
	startHtml($TAGS['ttScoring'][0],$TAGS['Scorer'][0].': '.$ScorerName,false);

	eval("\$evs = ".$TAGS['EntrantStatusV'][0]);
	
	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
	$dts = splitDatetime($rd['StartTime']);
	$dtf = splitDatetime($rd['FinishTime']);
	$OneDayRally = $dts[0] == $dtf[0];
	$rallyTimeDNF = $rd['FinishTime'];
	$rallyTimeStart = $rd['StartTime'];
	$certhours = $rd['CertificateHours'];
	
	$axisnames = [];

	// Flag this page as a scoresheet so the javascript knows what to do.
	echo('<input type="hidden" name="scoresheetpage" id="scoresheetpage" value="scoresheetpage">');

	for ($i = 0; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
	{
		echo('<input type="hidden" name="AxisScores[]" id="Axis'.$i.'Score" value="'.htmlspecialchars($rd['Cat'.$i.'Label']).'" data-bonuses="0" data-points="0" data-mults="0" data-axis="'.$i.'">');
		$axisnames[$i] = $rd['Cat'.$i.'Label'];
	}
	
	$rejectreasons = explode("\n",$rd['RejectReasons']);
	foreach($rejectreasons as $rrline)
	{
		$rr = explode('=',$rrline);
		if (count($rr)==2 && intval($rr[0])>0 && intval($rr[0])<10)
			echo('<input type="hidden" name="RejectReason" data-code="'.$rr[0].'" value="'.$rr[1].'">');
	}
	$RallyFinishTime = defaultFinishTime();
	$RallyStartTime = $dts;
	
	$ScoringMethod = $rd['ScoringMethod'];
	if ($ScoringMethod == $KONSTANTS['AutoScoring'])
		$ScoringMethod = chooseScoringMethod();
	$ShowMults = $rd['ShowMultipliers'];
	if ($ShowMults == $KONSTANTS['AutoShowMults'])
		$ShowMults = chooseShowMults($ScoringMethod);
	
	updateScoringFlags((isset($_REQUEST['EntrantID']) ? $_REQUEST['EntrantID'] : 0));
	
	echo("\r\n");
	echo('<div id="rcmenu" style="display:none;">');
	echo('<ul>');
	echo('<li><a href="#">'.$TAGS['RejectReason0'][0].'</a></li>');
	
	foreach($rejectreasons as $rrline)
	{
		$rr = explode('=',$rrline);
		echo('<li data-code="'.$rr[0].'"><a href="#'.$rr[0].'">'.$rrline.'</a></li>');
	}
	echo('</ul>');
	echo('</div>');
	echo("\r\n");
	
	echo('<div id="ScoreSheet">'."\r\n");
	echo("\r\n");
	echo('<form method="post" action="score.php" onsubmit="submitScore();">');
	echo('<input type="hidden" name="ScorerName" value="'.htmlspecialchars((isset($_REQUEST['ScorerName']) ? $_REQUEST['ScorerName'] : '')).'">');
	echo('<input type="hidden" id="MinPoints" value="'.$rd['MinPoints'].'">');
	echo('<input type="hidden" id="MinMiles" value="'.$rd['MinMiles'].'">');
	echo('<input type="hidden" id="PenaltyMaxMiles" value="'.$rd['PenaltyMaxMiles'].'">');
	echo('<input type="hidden" id="MaxMilesMethod" value="'.$rd['MaxMilesMethod'].'">');
	echo('<input type="hidden" id="MaxMilesPoints" value="'.$rd['MaxMilesPoints'].'">');
	echo('<input type="hidden" id="PenaltyMilesDNF" value="'.$rd['PenaltyMilesDNF'].'">');
	echo('<input type="hidden" id="ScoringMethod" value="'.$ScoringMethod.'">');
	echo('<input type="hidden" id="ShowMults" value="'.$ShowMults.'">');
	echo('<input type="hidden" name="ScoreX" id="scorexstore" value=""/>');
	//echo(" 1 ");
	$TimePenaltyTime =($DBVERSION < 3 ? '0 as TimeSpec' : 'TimeSpec');
		
	$R = $DB->query('SELECT rowid AS id,'.$TimePenaltyTime.',PenaltyStart,PenaltyFinish,PenaltyMethod,PenaltyFactor FROM timepenalties ORDER BY PenaltyStart,PenaltyFinish');
	while ($rd = $R->fetchArray())
		echo('<input type="hidden" name="TimePenalty[]" data-spec="'.$rd['TimeSpec'].'" data-start="'.$rd['PenaltyStart'].'" data-end="'.$rd['PenaltyFinish'].'" data-factor="'.$rd['PenaltyFactor'].'" data-method="'.$rd['PenaltyMethod'].'">');
	//echo(" 2 ");
	$sql = ($DBVERSION < 3 ? ',0 as Compulsory' : ',Compulsory');
	$R = $DB->query('SELECT rowid AS id,Axis,Cat,NMethod,ModBonus,NMin,PointsMults,NPower'.$sql.' FROM catcompound ORDER BY Axis,NMin DESC');
	while ($rd = $R->fetchArray())
		echo('<input type="hidden" name="catcompound[]" data-axis="'.$rd['Axis'].'" data-cat="'.$rd['Cat'].'" data-method="'.$rd['NMethod'].'" data-mb="'.$rd['ModBonus'].'" data-min="'.$rd['NMin'].'" data-pm="'.$rd['PointsMults'].'" data-power="'.$rd['NPower'].'" data-reqd="'.$rd['Compulsory'].'">');
	//echo(" 3 ");
	$sql = 'SELECT * FROM entrants';
	if ($showBlankForm) 
		$sql .= ' LIMIT 1';	// Just need a valid array, don't care about the contents
	else
		$sql .= ' WHERE EntrantID='.$_REQUEST['EntrantID'];
	$R = $DB->query($sql);
	$rd = $R->fetchArray();

	if ($showBlankForm)
	{
		$_REQUEST['EntrantID']	= '';
		$rd['EntrantID']		= '';
		$rd['RiderName']		= '_________________';
		$rd['FinishTime']		= '';
		$rd['OdoCheckFinish']	= '';
		$rd['CorrectedMiles']	= '';
		$rd['TotalPoints']		= '';
		//$rd['EntrantStatus']	= '';
		$rd['BonusesVisited']	= '';
		$rd['SpecialsTicked']	= '';
		$rd['CombosTicked']		= '';
	}
	$mtDNF = DateTime::createFromFormat('Y\-m\-d\TH\:i',$rd['StartTime']);
	try {
		$mtDNF = date_add($mtDNF,new DateInterval("PT".$certhours."H"));
	} catch(Exception $e) {
		echo('omg! '.$e->getMessage());
	}
	$myTimeDNF = date_format($mtDNF,'Y-m-d').'T'.date_format($mtDNF,'H:i');
	if ($rallyTimeDNF < $myTimeDNF)
		$myTimeDNF = $rallyTimeDNF;
		
		
	echo('<input type="hidden" id="CertificateHours" value="'.$certhours.'">');
	echo('<input type="hidden" id="RallyTimeDNF" value="'.$rallyTimeDNF.'">');
	echo('<input type="hidden" id="RallyTimeStart" value="'.$rallyTimeStart.'">');
	echo('<input type="hidden" id="FinishTimeDNF" value="'.$myTimeDNF.'">');
	
	$chk = $rd['OdoKms'] == $KONSTANTS['OdoCountsKilometres'] ? ' checked="checked" ' : ' ';
	echo('<input type="hidden" id="OdoKmsK" '.$chk.'>');
	echo('<input type="hidden" id="OdoScaleFactor" name="OdoScaleFactor" value="'.$rd['OdoScaleFactor'].'">');
	echo('<input type="hidden" id="EntrantID" name="EntrantID" value="'.$_REQUEST['EntrantID'].'">');
	echo('<input type="hidden" name="RejectedClaims" id="RejectedClaims" value="'.$rd['RejectedClaims'].'">');
	
	
	echo("\r\n");
	echo('<div id="ScoreHeader"');
	if ($ScoringMethod == $KONSTANTS['ManualScoring'])
		echo(' class="manualscoring" ');
	echo('>');
	echo("\r\n");
	echo('<span style="font-weight: bold;" id="RiderID">'.$TAGS['EntrantID'][0].' '.$_REQUEST['EntrantID'].' - '.htmlspecialchars($rd['RiderName']));
	if ($rd['PillionName'] <> '')
		echo(' &amp; '.htmlspecialchars($rd['PillionName']));
	echo('</span> ');
	$dt1 = splitDatetime($rd['StartTime']);
	if ($dt1[0] == '')
		$dt1 = $RallyStartTime;
	$hideclass = ' class="hide" ';
	$dt = splitDatetime($rd['FinishTime']);
	if ($dt[0] == '')
		$dt = $RallyFinishTime;
	if ($OneDayRally)
		$hideclass = ' class="hide" ';
	else
		$hideclass = '';
	$datetype = 'date';
	$timetype = 'time';
	$numbertype = 'number';
	$sbfro = '';
	if ($showBlankForm)
	{
		$dt[0] = '__________';
		$dt[1] = '______';
		$dt1[0] = $dt[0];
		$dt1[1] = $dt[1];
		$datetype = 'text';
		$timetype = 'text';
		$numbertype = 'text';
		$sbfro = ' readonly="readonly" ';
		$codof = '______';
		$codof1 = $codof;
		$cmiles = '_____';
	}
	else
	{
		$codof = $rd['OdoRallyFinish'];
		$codof1 = $rd['OdoRallyStart'];
		$cmiles = intval($rd['CorrectedMiles']);
	}
	echo("\r\n");
	echo('<span '.$hideclass.'title="'.$TAGS['StartDateE'][1].'"><label for="StartDate">'.$TAGS['StartDateE'][0].' </label> ');
	echo('<input type="'.$datetype.'" id="StartDate" name="StartDate" value="'.$dt1[0].'" onchange="calcScore(true)"  />');
	echo('</span> ');
	echo('<span  title="'.$TAGS['StartTimeE'][1].'"><label for="StartTime">'.$TAGS['StartTimeE'][0].' </label> ');
	echo('<input '.$sbfro.'type="'.$timetype.'" id="StartTime" name="StartTime" value="'.$dt1[1].'" onchange="calcScore(true)"  />');
	echo('</span>');
	echo("\r\n");
	echo('<span '.$hideclass.'title="'.$TAGS['FinishDateE'][1].'"><label for="FinishDate">'.$TAGS['FinishDateE'][0].' </label> ');
	echo('<input '.$sbfro.' type="'.$datetype.'" id="FinishDate" name="FinishDate" value="'.$dt[0].'" onchange="calcScore(true)" />');
	echo('</span> ');
	echo('<span id="Timings" title="'.$TAGS['FinishTimeE'][1].'"><label for="FinishTime">'.$TAGS['FinishTimeE'][0].' </label> ');
	echo('<input '.$sbfro.'type="'.$timetype.'" id="FinishTime" name="FinishTime" value="'.$dt[1].'" onchange="calcScore(true)" />');
	if ($ScoringMethod == $KONSTANTS['ManualScoring'])
		echo(' <input type="button" value="'.$TAGS['nowlit'][0].'" onclick="setSplitNow(\'Finish\');" />');	
	echo('</span> ');
	
	//echo('<input type="hidden" id="OdoRallyStart" name="OdoRallyStart" value="0'.$rd['OdoRallyStart'].'">');

	echo("\r\n");
	echo('<span title="'.$TAGS['OdoRallyStart'][1].'"><label for="OdoRallyStart">'.$TAGS['OdoRallyStart'][0].' </label> ');
	echo('<input '.$sbfro.' type="'.$numbertype.'" name="OdoRallyStart" id="OdoRallyStart" value="'.$codof1.'" onchange="calcMiles()" /> ');
	echo('</span>');
	echo("\r\n");
	echo('<span title="'.$TAGS['OdoRallyFinish'][1].'"><label for="OdoRallyFinish">'.$TAGS['OdoRallyFinish'][0].' </label> ');
	echo('<input '.$sbfro.' type="'.$numbertype.'" name="OdoRallyFinish" id="OdoRallyFinish" value="'.$codof.'" onchange="calcMiles()" /> ');
	echo('</span>');
	echo('<span title="'.$TAGS['CorrectedMiles'][1].'"><label for="CorrectedMiles">'.$TAGS['CorrectedMiles'][0].' </label> ');
	echo('<input '.$sbfro.' type="'.$numbertype.'"  name="CorrectedMiles" id="CorrectedMiles" value="'.$cmiles.'" onchange="calcScore(true)" /> ');
	echo('</span> ');
	
	
		
	echo("\r\n".'<span><label  class="clickme" title="'.$TAGS['ToggleScoreX'][1].'" for="TotalPoints">'.$TAGS['TotalPoints'][0].' </label> ');
	if ($ScoringMethod <> $KONSTANTS['ManualScoring'])
		$ro = 'readonly="readonly" ';
	else
		$ro = '';
	if ($showBlankForm)
	{
		$ctotal = '_____';
		$tp_id = 'tpoints';
	}
	else
	{
		$ctotal = $rd['TotalPoints'];
		$tp_id = 'TotalPoints';
	}
	echo('<input  class="clickme"  ondblclick="sxprint();" onclick="sxtoggle();" title="'.$TAGS['TotalPoints'][1].'" type="'.($ro != ''? 'text' : 'number').'" '.$ro.' name="TotalPoints" id="'.$tp_id.'" value="'.$ctotal.'" onchange="calcScore(true)" /> ');
	echo('</span> ');
	
	if (!$showBlankForm)
	{
	
	// echo(' <span class="clickme noprint" onclick="sxtoggle();"> ? </span>');
	if ($ScoringMethod == $KONSTANTS['CompoundScoring'])
	{
		if ($ShowMults == $KONSTANTS['SuppressMults'])
			$style = ' style="display:none;" ';
		else
			$style = '';
		echo('<span '.$style.' title="'.$TAGS['TotalMults'][1].'"><label for="TotalMults">'.$TAGS['TotalMults'][0].'</label> ');
		echo(' <input type="text" readonly="readonly" title="'.$TAGS['TotalMults'][1].'" id="TotalMults" value="0" onchange="calcScore(true)" /> ');
		echo('</span>'."\r\n");
	}
	echo('<br /><span title="'.$TAGS['EntrantStatus'][1].'"><label for="EntrantStatus">'.$TAGS['EntrantStatus'][0].' </label> ');
	echo('<select name="EntrantStatus" id="EntrantStatus" onchange="enableSaveButton()">'); // Don't recalculate if status changed manually
	if ($rd['EntrantStatus']=='')
		$rd['EntrantStatus'] = $KONSTANTS['DefaultEntrantStatus'];
	echo('<option value="'.$KONSTANTS['EntrantDNS'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantDNS'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantDNS'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantOK'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantOK'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantOK'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantFinisher'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantFinisher'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantFinisher'][0].'</option>');
	echo('<option value="'.$KONSTANTS['EntrantDNF'].'" '.($rd['EntrantStatus']==$KONSTANTS['EntrantDNF'] ? ' selected="selected" ' : '').'>'.$TAGS['EntrantDNF'][0].'</option>');
	echo('</select>');
	echo('</span> ');
	echo('<input type="submit" class="noprint" id="savescorebutton" disabled accesskey="S" name="savescore" data-altvalue="'.$TAGS['SaveScore'][0].'" value="'.$TAGS['ScoreSaved'][0].'" /> ');
	//echo('<input type="submit" id="backtolistbutton" name="showpicklist" data-altvalue="'.$TAGS['ShowEntrants'][0].'" value="'.$TAGS['ShowEntrants'][0].'"> ');
	
	
	} // End !$showBlank Form
	
	
	
	
	
	echo("\r\n");
	echo('</div>');
	echo("\r\n");
	
	
	if ($ScoringMethod <> $KONSTANTS['ManualScoring'])
	{
		if ($showBlankForm && false)
		{
			echo('<ul id="BlankFormRejectReasons">');
			foreach($rejectreasons as $rrline)
			{
				echo('<li>'.$rrline.'</li>');
			}
			echo('</ul>');
		}
		
		
		echo('<fieldset id="tab_bonuses"><legend>'.$TAGS['BonusesLit'][0].'</legend>');
		showBonuses($rd['BonusesVisited'],$showBlankForm);
		echo('</fieldset><!-- showBonuses -->'."\r\n");
		if (getValueFromDB('SELECT count(*) as rex FROM specials','rex',0) > 0)
		{
			echo('<fieldset id="tab_specials"><legend>'.$TAGS['SpecialsLit'][0].'</legend>');
			showSpecials($rd['SpecialsTicked']);
			echo('</fieldset><!-- showSpecials -->'."\r\n");
		}
		if (!$showBlankForm && getValueFromDB('SELECT count(*) as rex FROM combinations','rex',0) > 0)
		{
			echo('<fieldset id="tab_combos"><legend>'.$TAGS['CombosLit'][0].'</legend>');
			showCombinations($rd['CombosTicked']);
			echo('</fieldset><!-- showCombinations -->'."\r\n");
		}
	}
	
	echo("\r\n");
	echo('</form>');
	echo("\r\n");
	echo('</div>'."\r\n"); // End ScoreSheet


	if ($ScoringMethod == $KONSTANTS['CompoundScoring'] && !$showBlankForm)
	{
		echo('<div id="cat_results">');
		for ($i = 1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			if ($axisnames[$i] <> '')
				showCategory($i,$axisnames[$i]);
		echo('</div>');
	}
	echo('<div id="scorex" title="'.$TAGS['dblclickprint'][0].'" class="hidescorex scorex" data-show="0" ondblclick="sxprint();" >'.$rd['ScoreX'].'</div>');
	echo('</body></html>');
}

function showBonuses($bonuses,$showBlankForm)
{
	global $DB, $TAGS, $KONSTANTS;

	$BA = explode(',',','.$bonuses); // The leading comma means that the first element is index 1 not 0
	$BP = [];
	$R = $DB->query('SELECT * FROM bonuses ORDER BY BonusID');
	while ($rd = $R->fetchArray())
	{
		$BP[$rd['BonusID']] = array($rd['BriefDesc'],$rd['Points'],$rd['Cat1'],$rd['Cat2'],$rd['Cat3'],$rd['Compulsory']);
	}
	echo('<input type="hidden" name="update_bonuses" value="1" />'); // Flag in case of no bonuses ticked = empty array
	foreach($BP as $bk => $b)
	{
		if ($bk <> '') {
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			$spncls = ($chk <> '') ? ' checked' : ' unchecked';
			echo('<span class="showbonus'.$spncls.'" oncontextmenu="showPopup(this);"');
			if ($b[5]<>0)
				echo(' compulsory');
			echo(' title="'.htmlspecialchars($b[0]).' [ '.$b[1].' ]">');
			echo('<label for="B'.$bk.'">'.$bk.'-</label>');
			echo('<input type="checkbox"'.$chk.' name="BonusID[]" id="B'.$bk.'" value="'.$bk.'" onchange="calcScore(true)"');
			echo(' data-points="'.$b[1].'" data-cat1="'.intval($b[2]).'" data-cat2="'.intval($b[3]).'" data-cat3="'.intval($b[4]).'" data-reqd="'.intval($b[5]).'" /> ');
			if ($showBlankForm)
			{
				echo(' ____ ____ &nbsp;&nbsp;&nbsp;&nbsp;');
			}
			echo('</span>');
			echo("\r\n");
		}
	}
	echo("\r\n");
}


function showCategory($axis,$axisdesc)
{
	global $DB, $TAGS, $KONSTANTS;

	$R = $DB->query("SELECT * FROM categories WHERE Axis=$axis ORDER BY Cat");
	echo("\r\n");
	echo('<table id="cat'.$axis.'">');
	echo("\r\n");
	if ($axisdesc <> '')
		echo('<caption>'.$axisdesc.'</caption>');
	while ($rd = $R->fetchArray())
		echo('<tr><td class="catdesc">'.$rd['BriefDesc'].'</td><td class="scoredetail" id="cat'.$axis.'_'.$rd['Cat'].'"></td></tr>');
	echo("\r\n");
	echo('</table>');
	echo("\r\n");
}



function showCombinations($Combos)
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	$BA = explode(',',','.$Combos); // The leading comma means that the first element is index 1 not 0
	
	$R = $DB->query('SELECT * FROM combinations ORDER BY ComboID');
	while ($rd = $R->fetchArray())
	{
		if ($DBVERSION < 3)
			$rd['MinimumTicks'] = 0;
		
		$BP[$rd['ComboID']] = array($rd['BriefDesc'],$rd['ScoreMethod'],$rd['ScorePoints'],$rd['Bonuses'],$rd['Compulsory'],$rd['MinimumTicks']);
	}
	echo('<input type="hidden" name="update_combos" value="1" />'."\r\n");
	foreach($BP as $bk => $b)
	{
		if ($bk <> '') {
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			$spncls = ($chk <> '') ? ' checked' : ' unchecked';
			echo('<span class="combo '.$spncls.'" title="'.htmlspecialchars($bk).' [ ');
			if ($b[1]==$KONSTANTS['ComboScoreMethodMults'])
				echo('x');
			echo($b[2]);
			echo(' ]" oncontextmenu="showPopup(this);">');
			echo('<label for="C'.$bk.'">'.htmlspecialchars($b[0]).' </label>');
			echo('<input type="checkbox"'.$chk.' name="ComboID[]" disabled="disabled" id="C'.$bk.'" value="'.$bk.'"');
			echo(' data-method="'.$b[1].'" data-points="'.$b[2].'" data-bonuses="'.$b[3].'" data-reqd="'.$b[4].'"');
			echo(' data-minticks="'.$b[5].'"');
			echo(' data-pointsarray="'.$b[2].'"'); // Combos might have different values depending on MinimumTicks
			echo('/> ');
			echo(' &nbsp;&nbsp;</span> ');
			echo("\r\n");
		}
	}
	//echo('<br />');
}




function showPicklist($ord)
{
	global $DB, $TAGS, $KONSTANTS, $HOME_URL;
	

	$minEntrant = getValueFromDB("SELECT min(EntrantID) as MaxID FROM entrants","MaxID",1);
	$maxEntrant = getValueFromDB("SELECT max(EntrantID) as MaxID FROM entrants","MaxID",$minEntrant);

	$R = $DB->query('SELECT * FROM entrants ORDER BY '.$ord);
	
	$lnk = '<a href="'.$HOME_URL.'" onclick="return areYouSure(\'\r\n'.$TAGS['LogoutScorer'][0].' '.$_REQUEST['ScorerName'].' ?\');">';

	startHtml($TAGS['ttScoring'][0],$lnk.$TAGS['Scorer'][0].': '.$_REQUEST['ScorerName'].'</a>');

	eval("\$evs = ".$TAGS['EntrantStatusV'][0]);
?>
<script>
function submitMe(obj)
{
	var ent = '';
    for ( var i = 0; i < obj.childNodes.length; i++ ) {
        if ( obj.childNodes[i].className == 'EntrantID' )
			ent = obj.childNodes[i].innerText;
     }
	 if (ent == '')
		 return;
	 var frm = document.getElementById('entrantpick');
	 document.getElementById('EntrantID').value = ent;
	 frm.submit();

}
function filterByName(x)
{
	//alert('FBN=='+x);
	var tab = document.getElementById('entrantrows');
	var firstLink = -1;
	for (var i = 0; i < tab.childNodes.length; i++ )
	{
		//alert('Row ' + i + ' is ' + tab.childNodes[i].className);
		for ( var j = 0; j < tab.childNodes[i].childNodes.length; j++ )
		{
			//alert('col ' + j + ' is ' + tab.childNodes[i].childNodes[j].className);
			if ( tab.childNodes[i].childNodes[j].className == 'EntrantID' )
			{
				//alert('Row ' + i + '[' + tab.childNodes[i].childNodes[j].innerText + ']');
				tab.childNodes[i].setAttribute('data-ent',tab.childNodes[i].childNodes[j].innerText);
			}
			if ( tab.childNodes[i].childNodes[j].className == 'RiderName' )
			{
				if (tab.childNodes[i].childNodes[j].innerText.toUpperCase().indexOf(x.toUpperCase()) < 0)
					tab.childNodes[i].className = 'tabContenthide';
				else
				{
					//alert('Row ' + i + ' has firstLink already set to ' + firstLink);
					if (firstLink < 0)
						firstLink = i;
					tab.childNodes[i].className = 'link';
				}
			}
		}
	}
	if (firstLink >= 0)
	{
		//alert('Setting EntrantID from row '+firstLink+'; value '+tab.childNodes[firstLink].getAttribute('data-ent'));
		document.getElementById('EntrantID').value = tab.childNodes[firstLink].getAttribute('data-ent');
	}
}
</script>
<?php	
	echo('<p>'.$TAGS['PickAnEntrant'][1].'</p>');
	echo('<form id="entrantpick" method="get" action="score.php">');
	echo('<label for="EntrantID">'.$TAGS['EntrantID'][0].'</label> ');
	echo('<input oninput="showPickedName();" type="number" autofocus id="EntrantID" name="EntrantID" min="'.$minEntrant.'" max="'.$maxEntrant.'"> '); 
	echo('<input type="hidden" name="c" value="score">');
	echo('<input type="hidden" name="ScorerName" value="'.htmlspecialchars($_REQUEST['ScorerName']).'">');
	echo('<label for="NameFilter">'.$TAGS['NameFilter'][0].' </label>');
	echo(' <input onchange="enableSaveButton();" type="text" id="NameFilter" title="'.$TAGS['NameFilter'][1].'" onkeyup="filterByName(this.value)">');
	echo('<input class="button" type="submit" id="savedata" disabled="disabled" value="'.$TAGS['ScoreThis'][0].'" > ');
	echo('</form>');
	echo('<table><thead><tr>');
	echo('<th></th>');
	echo('<th></th>');
	echo('<th></th>');
	echo('<th></th>');
	echo('</tr></thead><tbody id="entrantrows">');
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="link" onclick="submitMe(this)"><td class="EntrantID">'.$rd['EntrantID'].'</td>');
		echo('<td class="RiderName">'.$rd['RiderName'].'</td>');
		$es = $evs[''.$rd['EntrantStatus']];
		if ($es=='')
			$es = '[[ '.$rd['EntrantStatus'].']]';
		echo('<td class="EntrantStatus">'.$es.'</td>');
		echo('<td class="ScoredBy">');
		if ($rd['ScoringNow']<>0)
			echo('== '.$rd['ScoredBy']);
		echo('</td>');
		echo('</tr>');
	}
	echo('</tbody></table>');
	echo("\r\n");
}



function showSpecials($specials)
{
	global $DB, $TAGS, $KONSTANTS;

	$BA = explode(',',','.$specials); // The leading comma means that the first element is index 1 not 0
	
	$R = $DB->query('SELECT specials.*,sgroups.GroupType FROM specials LEFT JOIN sgroups ON specials.GroupName=sgroups.GroupName ORDER BY GroupName,BonusID');
	while ($rd = $R->fetchArray())
	{
		$BP[$rd['BonusID']] = array($rd['BriefDesc'],$rd['Points'],$rd['MultFactor'],$rd['Compulsory'],$rd['GroupName'],$rd['GroupType'],$rd['AskPoints']);
	}
	echo('<input type="hidden" name="update_specials" value="1" />');
	$lastSpecialGroup = '';
	$SGroupsUsed = '';
	$AP = array();
	foreach ($BA as $bk)
	{
		if (strpos($bk,'=')>0)
		{
			$x = explode('=',$bk);
			if (count($x)==2)
			{
				$AP[$x[0]] = $x[1];
			}
		}
	}
	//print_r($AP);
	$firstRadio = ' checked="checked" ';
	foreach($BP as $bk => $b)
	{
		if ($bk <> '') {
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			if ($b[4] <> $lastSpecialGroup)
			{
				if ($lastSpecialGroup <> '')
					echo('</fieldset>');
				echo('<fieldset><legend>'.$b[4].'</legend>');
				$lastSpecialGroup = $b[4];
				$firstRadio = ' checked="checked" ';
				if ($SGroupsUsed <> '')
					$SGroupsUsed .= ',';
				$SGroupsUsed .= $lastSpecialGroup;
			}
			if ($b[5] == 'R')
				$optType = 'radio';
			else
				$optType = 'checkbox';
			echo('<span ');
			echo('class="showbonus ');
			$spncls = ($chk <> '') ? ' checked' : ' unchecked';
			echo($spncls);
			if ($b[3]<>0)
				echo(' compulsory ');
			echo('" ');
			$specialid = str_replace(' ','_',$bk);
			$points = $b[1];
			$onchange = 'calcScore(true);';
			if ($b[6]==1)
			{
				$onchange='askPoints(this);';
				if (isset($AP[$specialid]))
					$points = $AP[$specialid];
			}
			echo('title="'.htmlspecialchars($bk).' [ ');
			echo($points); // points value
			if ($b[2]<>0)
				echo(' x'.$b[2]);
			echo(' ]" ');
			if ($optType == 'checkbox')
				echo(' oncontextmenu="showPopup(this);"');
			echo('>');
			echo('<label for="S'.str_replace(' ','_',$bk).'">'.htmlspecialchars($b[0]).' </label>');
			$chk = array_search($bk, $BA) ? ' checked="checked" ' : '';  // Depends on first item having index 1 not 0
			if ($chk=='' && $firstRadio <> '' && $b[5]=='R')
				$chk = $firstRadio;
			$firstRadio = '';
			echo('<input type="'.$optType.'"'.$chk.' name="SpecialID');
			if ($lastSpecialGroup <> '')
				echo("_".$lastSpecialGroup);
			echo('[]" id="S'.$specialid.'" value="'.$bk.'"');
			echo(' onchange="'.$onchange.'"');
			
			echo(' data-points="'.$points.'" data-mult="'.$b[2].'" data-reqd="'.$b[3].'"> ');
			echo(' &nbsp;&nbsp;</span>');
			echo("\r\n");
	}
	}
	if ($lastSpecialGroup <> '')
		echo('</fieldset>');
	echo("\r\n");
	echo('<span id="apspecials">');
	foreach ($AP as $b => $p)
	{
		echo('<input type="hidden" name=ap'.$b.'" id="ap'.$b.'" value="'.$p.'">');
	}
	echo('</span>');
	echo('<input type="hidden" name="SGroupsUsed" id="SGroupsUsed" value="'.$SGroupsUsed.'"/>');
	
}

function updateScoringFlags($EntrantID=0)
{
	global $DB;
	
	$DB->exec('BEGIN TRANSACTION');
	// Clear records being scored by this scorer
	$sql = "UPDATE entrants SET ScoringNow=0 WHERE ScoredBy='".$DB->escapeString((isset($_REQUEST['ScorerName']) ? $_REQUEST['ScorerName'] : ''))."'";
	$DB->exec($sql);
	if ($EntrantID <> 0)
	{
		// Mark this one as being scored now
		$sql = "UPDATE entrants SET ScoringNow=1, ScoredBy='".$DB->escapeString((isset($_REQUEST['ScorerName']) ? $_REQUEST['ScorerName'] : ''))."' WHERE EntrantID=".$EntrantID;
		$DB->exec($sql);
	}
	$DB->exec('COMMIT TRANSACTION');
	
}

//var_dump($_REQUEST);

if (isset($_REQUEST['clear']))
	updateScoringFlags(0);

if (isset($_REQUEST['showpicklist']))
{
	if ($_REQUEST['showpicklist']=='savescore')
		putScore();
	showPicklist('EntrantID');
	exit;
}

if (isset($_REQUEST['savescore']))
{
	putScore();
	showPicklist('EntrantID');
	exit;
}

if (isset($_REQUEST['login']) && $_REQUEST['ScorerName'] <> '')
	loginNewScorer();
else if (isset($_REQUEST['c']) && $_REQUEST['c'] == 'score')
	scoreEntrant(FALSE);
else if (isset($_REQUEST['c']) && $_REQUEST['c'] == 'blank')
	scoreEntrant(TRUE);
else if (isset($_REQUEST['c']) && $_REQUEST['c'] == 'pickentrant')
	showPicklist($_REQUEST['ord']);
else if (isset($_REQUEST['ScorerName']))
	showPicklist('EntrantID');
else if (rally_params_established())
	inviteScorer();
else
	include("setup.php");
exit;
?>