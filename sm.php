<?php

/*
 * I B A U K   -   S C O R E M A S T E R
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

 
 if (!isset($_REQUEST['c']))
{
	echo('404');
	exit;
}

 
 
 
 
$HOME_URL = 'admin.php';

require_once('common.php');

// Alphabetic from here on in


function deleteSpecial($bonusid)
{
	global $DB;

	$sql = "DELETE FROM specials WHERE BonusID='".$DB->escapeString($bonusid)."'";
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) 
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
	
}


function emitBonusTicks()
{
	global $DB;
	
	$R = $DB->query('SELECT * FROM bonuses ORDER BY BonusID');
	while ($rd = $R->fetchArray())
	{
		echo("<label for=\"B".$rd['BonusID']."\">".$rd['BonusID']." </label>");
		echo("<input type=\"checkbox\" name=\"BonusID[]\" id=\"B".$rd['BonusID']."\" value=\"".$rd['BonusID']."\">");
		echo("<input type=\"text\" name=\"BriefDesc[]\" value=\"".$rd['BriefDesc']."\">");
		echo("<br>");
	}
}

function saveBonuses() 
{
	global $DB, $TAGS, $KONSTANTS;

//	var_dump($_REQUEST);
	$arr = $_REQUEST['BonusID'];
	$DB->query("BEGIN TRANSACTION");
	for ($i=0; $i < count($arr); $i++)
	{
		$sql = "INSERT OR REPLACE INTO bonuses (BonusID,BriefDesc,Points";
		for ($ai=1; $ai <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $ai++)
			if (isset($_REQUEST['Cat'.$ai.'Entry']))
				$sql .= ",Cat".$ai;

		$sql .= ",Compulsory) VALUES(";
		$sql .= "'".$DB->escapeString($_REQUEST['BonusID'][$i])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'][$i])."'";
		$sql .= ",".intval($_REQUEST['Points'][$i]);
		for ($ai=1; $ai <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $ai++)
			if (isset($_REQUEST['Cat'.$ai.'Entry'][$i]))
				$sql .= ",".intval($_REQUEST["Cat".$ai."Entry"][$i]);
		$sql .= ",0)";
		if ($_REQUEST['BonusID'][$i]<>'')
		{
			//echo($sql.'<br>');			
			$DB->exec($sql);
			if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			exit;
			}
			
		}
	}
	if (isset($_REQUEST['Compulsory']))
	{
		$arr = $_REQUEST['Compulsory'];
		for ($i = 0 ; $i < count($arr) ; $i++ )
		{	
			$sql = "UPDATE bonuses SET Compulsory=1 WHERE BonusID='".$DB->escapeString($_REQUEST['Compulsory'][$i])."'";
			$DB->exec($sql);
		}
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		$arr = $_REQUEST['DeleteEntry'];
		for ($i=0; $i < count($arr); $i++)
		{
			$sql = "DELETE FROM bonuses WHERE BonusID='".$DB->escapeString($_REQUEST['DeleteEntry'][$i])."'";
			$DB->exec($sql);
		}
	}
	$DB->query('COMMIT TRANSACTION');

	if (retraceBreadcrumb())
		exit;

	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}

}



function saveCategories()
{
	global $DB, $TAGS, $KONSTANTS;

	$sql = "UPDATE rallyparams SET Cat".$_REQUEST['axis']."Label='".$DB->escapeString($_REQUEST['catlabel'])."'";
	$DB->exec($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	
	$DB->query('BEGIN TRANSACTION');
	for ($i = 0; $i < count($_REQUEST['Entry']); $i++)
	{
		$n = intval($_REQUEST['Entry'][$i]);
		$x = $_REQUEST['BriefDesc'][$i];
		if ($x <> '')
		{
			$sql = 'INSERT OR REPLACE INTO categories (Axis, Cat, BriefDesc) VALUES (';
			$sql .= intval($_REQUEST['axis']).','.$n.",'".$DB->escapeString($x)."')";
			//echo($sql.'<br>');
		
			$DB->exec($sql);
			if (($res = $DB->lastErrorCode()) <> 0)
				echo('ERROR: '.$DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		}
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		for ($i = 0; $i < count($_REQUEST['DeleteEntry']); $i++)
		{
			$sql = 'DELETE FROM categories WHERE Axis='.intval($_REQUEST['axis']).' AND Cat='.intval($_REQUEST['DeleteEntry'][$i]);
			//echo($sql.'<br>');
			$DB->exec($sql);
			if (($res = $DB->lastErrorCode()) <> 0)
				echo('ERROR: '.$DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		}
	}
	$DB->query('COMMIT TRANSACTION');
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}
	
}	

function saveCombinations()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	//var_dump($_REQUEST); echo('<br>');
	$arr = $_REQUEST['ComboID'];
	$DB->query('BEGIN TRANSACTION');
	for ($i=0; $i < count($arr); $i++)
	{
		// Let's make sure the bonus list is good
		$bl = str_replace(' ',',',$_REQUEST['Bonuses'][$i]); // we want commas as separators not spaces
		$bls = explode(',',$bl);
		// On second thoughts, let's not bothering validating them here.
		$sql = "INSERT OR REPLACE INTO combinations (ComboID,BriefDesc,ScoreMethod,ScorePoints,Bonuses";
		if ($DBVERSION >= 3)
		{
			$sql .= ",MinimumTicks";
			for ($ai=1; $ai <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $ai++)
				if (isset($_REQUEST['Cat'.$ai.'Entry']))
					$sql .= ",Cat".$ai;
		}
		$sql .=") VALUES(";
		$sql .= "'".$DB->escapeString($_REQUEST['ComboID'][$i])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'][$i])."'";
		$sql .= ','.intval($_REQUEST['ScoreMethod'][$i]);
		$sql .= ",'".$_REQUEST['ScorePoints'][$i]."'";
		$sql .= ",'".$DB->escapeString($bl)."'";
		if ($DBVERSION >= 3)
		{
			$sql .=','.intval($_REQUEST['MinimumTicks'][$i]);
			for ($ai=1; $ai <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $ai++)
				if (isset($_REQUEST['Cat'.$ai.'Entry'][$i]))
					$sql .= ",".intval($_REQUEST["Cat".$ai."Entry"][$i]);
		}
		$sql .= ")";
		if ($_REQUEST['ComboID'][$i]<>'')
		{
			//echo($sql.'<br>');			
			$DB->exec($sql);
		}
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		$arr = $_REQUEST['DeleteEntry'];
		for ($i=0; $i < count($arr); $i++)
		{
			$sql = "DELETE FROM combinations WHERE ComboID='".$DB->escapeString($_REQUEST['DeleteEntry'][$i])."'";
			$DB->exec($sql);
		}
	}
	$DB->query('COMMIT TRANSACTION');
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}

	
}





function saveCompoundCalcs()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	//var_dump($_REQUEST);
	
	if (isset($_REQUEST['newcc']))
	{
		$sql = "INSERT INTO catcompound (Axis,Cat,NMethod,ModBonus,NMin,PointsMults,NPower";
		if ($DBVERSION >= 3)
			$sql .= ",Compulsory";
		$sql .= ") VALUES(";
		$sql .= intval($_REQUEST['axis']);
		$sql .= ",".intval($_REQUEST['Cat']);
		$sql .= ",".intval($_REQUEST['NMethod']);
		$sql .= ",".intval($_REQUEST['ModBonus']);
		$sql .= ",".intval($_REQUEST['NMin']);
		$sql .= ",".intval($_REQUEST['PointsMults']);
		$sql .= ",".intval($_REQUEST['NPower']);
		if ($DBVERSION >= 3)
			$sql .= ",".intval($_REQUEST['Compulsory']);
		$sql .= ")";
		//echo($sql.'<hr>');
		$DB->exec($sql);
		if ($DB->lastErrorCode() <> 0)
			echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		return;
	}
	
	$arr = $_REQUEST['axis'];
	$DB->query('BEGIN TRANSACTION');
	for ($i=0; $i < count($arr); $i++)
	{
		$sql = "UPDATE catcompound SET "; //(id,Cat,NMethod,ModBonus,NMin,PointsMults,NPower) VALUES(";
		//$sql .= "".intval($_REQUEST['id'][$i]);
		$sql .= "Axis=".intval($_REQUEST['axis'][$i]);
		$sql .= ",Cat=".intval($_REQUEST['Cat'][$i]);
		$sql .= ",NMethod=".intval($_REQUEST['NMethod'][$i]);
		$sql .= ",ModBonus=".intval($_REQUEST['ModBonus'][$i]);
		$sql .= ",NMin=".intval($_REQUEST['NMin'][$i]);
		$sql .= ',PointsMults='.intval($_REQUEST['PointsMults'][$i]);
		$sql .= ',NPower='.intval($_REQUEST['NPower'][$i]);
		if ($DBVERSION >= 3)
			$sql .= ',Compulsory='.intval($_REQUEST['Compulsory'][$i]);
		$sql .= " WHERE rowid=".intval($_REQUEST['id'][$i]);
		$DB->exec($sql);
		if ($DB->lastErrorCode() <> 0)
			echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		$arr = $_REQUEST['DeleteEntry'];
		for ($i=0; $i < count($arr); $i++)
		{
			$sql = "DELETE FROM catcompound WHERE rowid=".$_REQUEST['DeleteEntry'][$i];
			$DB->exec($sql);
		}
	}
	$DB->query('COMMIT TRANSACTION');
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}
	
}

function saveSGroups()
{
	global $DB, $TAGS, $KONSTANTS;

	//var_dump($_REQUEST);
	$arr = $_REQUEST['GroupName'];
	$DB->query('BEGIN TRANSACTION');
	for ($i=0; $i < count($arr); $i++)
	{
		if (isset($_REQUEST['GroupName'][$i]) && isset($_REQUEST['GroupType'][$i]))
		{
			$sql = "INSERT OR REPLACE INTO sgroups (GroupName,GroupType) VALUES(";
			$sql .= "'".$DB->escapeString($_REQUEST['GroupName'][$i])."'";
			$sql .= ",'".$DB->escapeString($_REQUEST['GroupType'][$i])."'";
			$sql .= ")";
			if ($_REQUEST['GroupName'][$i]<>'')
			{
				//echo($sql.'<br>');			
				$DB->exec($sql);
			}
		}
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		$arr = $_REQUEST['DeleteEntry'];
		for ($i=0; $i < count($arr); $i++)
		{
			$sql = "DELETE FROM sgroups WHERE GroupName='".$DB->escapeString($_REQUEST['DeleteEntry'][$i])."'";
			$DB->exec($sql);
		}
	}
	$DB->query('COMMIT TRANSACTION');
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}
	
}




function saveRallyConfig()
{
	global $DB, $KONSTANTS, $DBVERSION;

	$RejectReasons = "";
	$k = count($_REQUEST['RejectReason']);
	for ($i =  0; $i < $k; $i++) {
		$ix = $i + 1;
		$v = $_REQUEST['RejectReason'][$i];
		$RejectReasons .= "$ix=$v\n";
	}

	$sql = "UPDATE rallyparams SET ";
	$sql .= "RallyTitle='".$DB->escapeString($_REQUEST['RallyTitle'])."'";
	$sql .= ",RallySlogan='".$DB->escapeString($_REQUEST['RallySlogan'])."'";
	if ($DBVERSION >= 4)
		$sql .= ",MaxHours=".intval($_REQUEST['MaxHours']);
	else
		$sql .= ",CertificateHours=".intval($_REQUEST['CertificateHours']);
	$sql .= ",StartTime='".$DB->escapeString($_REQUEST['StartDate']).'T'.$DB->escapeString($_REQUEST['StartTime'])."'";
	$sql .= ",FinishTime='".$DB->escapeString($_REQUEST['FinishDate']).'T'.$DB->escapeString($_REQUEST['FinishTime'])."'";
	$sql .= ",OdoCheckMiles=".floatval($_REQUEST['OdoCheckMiles']);
	$sql .= ",MinMiles=".intval($_REQUEST['MinMiles']);
	$sql .= ",MinPoints=".intval($_REQUEST['MinPoints']);
	$sql .= ",PenaltyMaxMiles=".intval($_REQUEST['PenaltyMaxMiles']);
	$sql .= ",MaxMilesMethod=".intval($_REQUEST['MaxMilesMethod']);
	$sql .= ",MaxMilesPoints=".intval($_REQUEST['MaxMilesPoints']);
	$sql .= ",PenaltyMilesDNF=".intval($_REQUEST['PenaltyMilesDNF']);
	$sql .= ",ScoringMethod=".intval($_REQUEST['ScoringMethod']);
	$sql .= ",ShowMultipliers=".intval($_REQUEST['ShowMultipliers']);
	$sql .= ",TiedPointsRanking=0".(isset($_REQUEST['TiedPointsRanking']) ? intval($_REQUEST['TiedPointsRanking']) : 0);
	$sql .= ",TeamRanking=".intval($_REQUEST['TeamRanking']);
	if ($DBVERSION >= 4)
	{
		$sql .= ",AutoRank=".(isset($_REQUEST['AutoRank']) ? intval($_REQUEST['AutoRank']) : 0);
	}
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		$sql .= ",Cat".$i."Label='".$DB->escapeString($_REQUEST['Cat'.$i.'Label'])."'";
	$sql .= ",RejectReasons='".$DB->escapeString($RejectReasons)."'";
	//echo($sql.'<hr>');
	$DB->exec($sql);
	//echo("Rally configuration saved ".$DB->lastErrorCode().' ['.$DB->lastErrorMsg().']<hr>');
	//show_regular_admin_screen();
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}
	
}

function saveSingleCombo()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	if (isset($_REQUEST['DeleteCombo']))
	{
		if (!isset($_REQUEST['comboid']) || $_REQUEST['comboid'] == '')
			return;
		$sql = "DELETE FROM combinations WHERE ComboID='".$DB->escapeString($_REQUEST['comboid'])."'";
		$DB->exec($sql);
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			exit;
		}
		return;
	}


	
	$sql = "INSERT OR REPLACE INTO combinations (";
	$sql .= "ComboID,BriefDesc,Compulsory,ScoreMethod,Bonuses,MinimumTicks,ScorePoints";
	if ($DBVERSION >= 3)
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			$sql .= ',Cat'.$i;
	$sql .= ") VALUES (";
	$sql .= "'".$DB->escapeString($_REQUEST['comboid'])."'";
	$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'])."'";
	$sql .= ",'".$DB->escapeString(isset($_REQUEST['Compulsory']) ? $_REQUEST['Compulsory'] : '')."'";
	$sql .= ','.intval($_REQUEST['ScoreMethod']);
	$sql .= ",'".$DB->escapeString($_REQUEST['Bonuses'])."'";
	$sql .= ','.intval($_REQUEST['MinimumTicks']);
	$sql .= ",'".$DB->escapeString($_REQUEST['ScorePoints'])."'";
	if ($DBVERSION >= 3)
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			$sql .= ','.(isset($_REQUEST['Cat'.$i.'Entry']) ? intval($_REQUEST['Cat'.$i.'Entry']) : 0);
	$sql .= ")";
	if ($_REQUEST['comboid']<>'')
	{
		//echo($sql.'<br>');			
		$DB->exec($sql);
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			exit;
		}
	}
	
}


function saveSpecial()
{
	global $DB, $TAGS, $KONSTANTS;

	$R = $DB->query("SELECT BonusID FROM specials WHERE BonusID='".$DB->escapeString($_REQUEST['BonusID'])."'");
	$newrec = $R->fetchArray()==FALSE;
	
	if ($newrec)
	{
		$sql = "INSERT INTO specials (BonusID,BriefDesc,GroupName,Points,AskPoints,MultFactor";
		$sql .= ",Compulsory,RestMinutes,AskMinutes) VALUES(";
		$sql .= "'".$DB->escapeString($_REQUEST['BonusID'])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['GroupName'])."'";
		$sql .= ",".intval($_REQUEST['Points']);
		$sql .= ",".intval($_REQUEST['AskPoints']);
		$sql .= ",".intval($_REQUEST['MultFactor']);
		$sql .= ",".intval($_REQUEST['Compulsory']);
		$sql .= ",".intval($_REQUEST['RestMinutes']);
		$sql .= ",".intval($_REQUEST['AskMinutes']);
		$sql .= ")";
	}
	else
	{
		$sql = "UPDATE specials SET BriefDesc='".$DB->escapeString($_REQUEST['BriefDesc'])."'";
		$sql .= ",GroupName='".$DB->escapeString($_REQUEST['GroupName'])."'";
		$sql .= ",Points=".intval($_REQUEST['Points']);
		$sql .= ",AskPoints=".intval($_REQUEST['AskPoints']);
		$sql .= ",MultFactor=".intval($_REQUEST['MultFactor']);
		$sql .= ",Compulsory=".intval($_REQUEST['Compulsory']);
		$sql .= ",RestMinutes=".intval($_REQUEST['RestMinutes']);
		$sql .= ",AskMinutes=".intval($_REQUEST['AskMinutes']);
		$sql .= " WHERE BonusID='".$DB->escapeString($_REQUEST['BonusID'])."'";
	}
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}
}


function saveSpecials()
{
	global $DB, $TAGS, $KONSTANTS;

	//var_dump($_REQUEST);
	$arr = $_REQUEST['BonusID'];
	$DB->query('BEGIN TRANSACTION');
	for ($i=0; $i < count($arr); $i++)
	{
		$sql = "INSERT OR REPLACE INTO specials (BonusID,BriefDesc,GroupName,Points,MultFactor) VALUES(";
		$sql .= "'".$DB->escapeString($_REQUEST['BonusID'][$i])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'][$i])."'";
		$sql .= ",'".$DB->escapeString(isset($_REQUEST['GroupName'][$i]) ? $_REQUEST['GroupName'][$i] : '')."'";
		$sql .= ','.intval($_REQUEST['Points'][$i]);
		$sql .= ','.intval($_REQUEST['MultFactor'][$i]);
		$sql .= ")";
		if ($_REQUEST['BonusID'][$i]<>'')
		{
			//echo($sql.'<br>');			
			$DB->exec($sql);
			if ($DB->lastErrorCode()<>0) {
				echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
				exit;
			}
		}
	}
	if (isset($_REQUEST['Compulsory']))
	{
		$arr = $_REQUEST['Compulsory'];
		for ($i = 0 ; $i < count($arr) ; $i++ )
		{
			$sql = "UPDATE specials SET Compulsory=1 WHERE BonusID='".$DB->escapeString($_REQUEST['Compulsory'][$i])."'";
			$DB->exec($sql);
		}
	}
	if (isset($_REQUEST['AskPoints']))
	{
		$arr = $_REQUEST['AskPoints'];
		for ($i = 0 ; $i < count($arr) ; $i++ )
		{
			$sql = "UPDATE specials SET AskPoints=1 WHERE BonusID='".$DB->escapeString($_REQUEST['AskPoints'][$i])."'";
			$DB->exec($sql);
		}
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		$arr = $_REQUEST['DeleteEntry'];
		for ($i=0; $i < count($arr); $i++)
		{
			$sql = "DELETE FROM specials WHERE BonusID='".$DB->escapeString($_REQUEST['DeleteEntry'][$i])."'";
			$DB->exec($sql);
		}
	}
	$DB->query('COMMIT TRANSACTION');
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}

	
}

function saveTimePenalties()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	$DB->query('BEGIN TRANSACTION');
	$DB->exec('DELETE FROM timepenalties');
	
	$Nmax = count($_REQUEST['PenaltyFactor']);
	for ($i = 0; $i < $Nmax; $i++)
	{
		if ($DBVERSION < 3)
		{
			$ts = '';
			$_REQUEST['TimeSpec'][$i] = $KONSTANTS['TimeSpecDatetime'];
		}
		else
			$ts = 'TimeSpec,';
		
		$sql = "INSERT INTO timepenalties (".$ts."PenaltyStart,PenaltyFinish,PenaltyMethod,PenaltyFactor) VALUES (";
		if ($ts != '')
			$sql .= $_REQUEST['TimeSpec'][$i].',';
		if ($_REQUEST['TimeSpec'][$i] == $KONSTANTS['TimeSpecDatetime'])
		{
			$sql .= "'".$_REQUEST['PenaltyStartDate'][$i].'T'.$_REQUEST['PenaltyStartTime'][$i]."'";
			$sql .= ",'".$_REQUEST['PenaltyFinishDate'][$i].'T'.$_REQUEST['PenaltyFinishTime'][$i]."'";
		}
		else
		{
			$sql .= $_REQUEST['PenaltyStartTime'][$i];
			$sql .= ','.$_REQUEST['PenaltyFinishTime'][$i];
		}
		$sql .= ",".$_REQUEST['PenaltyMethod'][$i];
		$sql .= ",".$_REQUEST['PenaltyFactor'][$i];
		$sql .= ")";
			
		if ( ($_REQUEST['TimeSpec'][$i] != $KONSTANTS['TimeSpecDatetime'] || $_REQUEST['PenaltyStartDate'][$i] <> '') && 
			$_REQUEST['PenaltyStartTime'][$i] <> '')
		{
			$DB->exec($sql);
			if ($DB->lastErrorCode() <> 0)
				echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		}
		else
			echo('Row '.$i." wasn't posted");
		
		
	}
	$DB->query('COMMIT TRANSACTION');
	if (retraceBreadcrumb())
		exit;
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}

	
}




function showBonuses()
{
	global $DB, $TAGS, $KONSTANTS;
	

	
	$R = $DB->query('SELECT * FROM bonuses ORDER BY BonusID');
	if (!$rd = $R->fetchArray())
		$rd = [];

?>
<script>
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('bonuses').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
</script>
<?php	
	
	pushBreadcrumb('#');

	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		$catlabels[$i] = $rd['Cat'.$i.'Label'];
	

	echo('<form method="post" action="sm.php">');
	emitBreadcrumbs();

	$R = $DB->query('SELECT * FROM categories ORDER BY Axis,BriefDesc');

	$lc = 0;

	while ($rd = $R->fetchArray())
	{
		if (!isset($cats[$rd['Axis']]))
			$cats[$rd['Axis']][0] = '';
		$cats[$rd['Axis']][$rd['Cat']] = $rd['BriefDesc'];
		
	}
	//print_r($cats1);
	
	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0)>0);
	
	echo('<input type="hidden" name="c" value="bonuses">');
//	echo('<input type="hidden" name="menu" value="setup">');
	echo("\r\n");
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	echo('<table id="bonuses">');
	echo('<caption title="'.htmlentities($TAGS['BonusMaintHead'][1]).'">'.htmlentities($TAGS['BonusMaintHead'][0]).'</caption>');
	echo('<thead class="listhead"><tr><th style="text-align:left;">'.$TAGS['BonusIDLit'][0].'</th>');
	//echo('<thead><tr><th>B</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	echo('<th>'.$TAGS['BonusPoints'][0].'</th>');

	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($cats[$i]))
			echo('<th>'.$catlabels[$i].'</th>');		
		
	echo('<th>'.$TAGS['CompulsoryBonus'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	if ($showclaimsbutton)
		echo('<th class="ClaimsCount">'.$TAGS['ShowClaimsCount'][0].'</th>');
	echo("</tr>\r\n");
	echo('</thead><tbody>');
	
	
	$sql = 'SELECT * FROM bonuses ORDER BY BonusID';
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="hoverlite"><td><input class="BonusID" type="text" readonly name="BonusID[]"  value="'.$rd['BonusID'].'"></td>');
		echo('<td><input class="BriefDesc" type="text" name="BriefDesc[]" value="'.$rd['BriefDesc'].'"></td>');
		echo('<td><input type="number" name="Points[]" value="'.$rd['Points'].'"></td>');
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			if (isset($cats[$i]))
			{
				echo('<td><select name="Cat'.$i.'Entry[]">');
				foreach ($cats[$i] as $ce => $bd)
				{
					echo('<option value="'.$ce.'" ');
					if ($ce == $rd['Cat'.$i])
						echo('selected ');
					echo('>'.htmlspecialchars($bd).'</option>');
				}
				echo('</select></td>');
			}
		
		if ($rd['Compulsory']==1)
			$chk = " checked ";
		else
			$chk = "";
		echo('<td class="center"><input type="checkbox"'.$chk.' name="Compulsory[]" value="'.$rd['BonusID'].'"></td>');
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['BonusID'].'"></td>');
		if ($showclaimsbutton)
		{
			$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || BonusesVisited || ',' LIKE '%,".$rd['BonusID'].",%'","rex",0);
			echo('<td class="ClaimsCount" title="'.$TAGS['ShowClaimsButton'][1].'">');
			if ($rex > 0)
				echo('<a href='."'entrants.php?c=entrants&mode=bonus&bonus=".$rd['BonusID']."'".'> '.$rex.' </a>');
			echo('</td>');
		}
		echo("</tr>\r\n");
	}
	echo('<tr class="newrow"><td><input class="BonusID" type="text" name="BonusID[]" onchange="triggerNewRow(this)"></td>');
	echo('<td><input type="text" name="BriefDesc[]"></td>');
	echo('<td><input type="number" name="Points[]" value="'.$rd['Points'].'"</td>');
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($cats[$i]))
		{
			$S = ' selected ';
			echo('<td><select name="Cat'.$i.'Entry[]">');
			foreach ($cats[$i] as $ce => $bd)
			{
				echo('<option value="'.$ce.'" ');
				echo($S);
				$S = '';
				echo('>'.htmlspecialchars($bd).'</option>');
			}
			echo('</select></td>');
		}
		
	// Can't make new row compulsory, just update afterwards
	//echo('<td><input type="checkbox"'.$chk.' name="Compulsory[]" value="'.$rd['BonusID'].'">');
	echo('<td></td><td></td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	echo('</form>');
	
	
}




function showCategories($axis,$ord)
{
	global $DB, $TAGS, $KONSTANTS;
	

?>
<script>
function triggerNewRow(obj)
{
	obj.onchange = '';
	tab = document.getElementById('cats').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	var cell1 = row.insertCell(0);
	cell1.innerHTML ='<input type="number" onchange="triggerNewRow(this)" name="Entry[]">';
	var cell2 = row.insertCell(-1);
	cell2.innerHTML = '<input type="text" name="BriefDesc[]">';
}
</script>
<?php	
	if ($axis < 1 || $axis > $KONSTANTS['NUMBER_OF_COMPOUND_AXES'])
	{
		echo($TAGS['AxisLit'][0]." [$axis] is not supported, please tell Bob<br><br>");
		return;
	}
	$R = $DB->query('SELECT Cat'.$axis.'Label AS CatLabel FROM rallyparams');
	
	if (!$rd = $R->fetchArray())
	{
		echo("The database is not setup!, please tell Bob<br><br>");
		return;
	}
	$CatLabel = $rd['CatLabel'];
	echo('<p class="explain">'.$TAGS['CatExplainer'][1].'</p>');
	echo('<form method="post" action="sm.php">');
	$bcurldtl ='&amp;breadcrumbs='.urlencode($_REQUEST['breadcrumbs']);

	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<input type="hidden" name="c" value="showcat">');
	echo('<input type="hidden" name="axis" value="'.$axis.'">');
	echo('<input type="hidden" name="ord" value="'.$ord.'">');
	echo('<input type="hidden" name="menu" value="setup">');
	
	echo('<table id="cats"><caption>'.$TAGS['AxisLit'][0].' '.$axis.'  <input type="text" name="catlabel" value="'.htmlspecialchars($CatLabel).'"></caption>');
	echo('<thead class="listhead"><tr><th><a href="sm.php?c=showcat&amp;axis='.$axis.'&amp;ord=Cat'.$bcurldtl.'">'.$TAGS['CatEntry'][0].'</a></th>');
	echo('<th><a href="sm.php?c=showcat&amp;axis='.$axis.'&amp;ord=BriefDesc'.$bcurdtl.'">'.$TAGS['CatBriefDesc'][0].'</a></th><th>'.$TAGS['DeleteEntryLit'][0].'</th></tr>');
	echo('</thead><tbody>');
	
	$sql = 'SELECT * FROM categories WHERE Axis='.$axis;
	if ($ord <> '')
		$sql .= ' ORDER BY '.$ord;
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="hoverlite"><td><input type="number" name="Entry[]" readonly value="'.$rd['Cat'].'"></td>');
		echo('<td><input type="text" name="BriefDesc[]" value="'.$rd['BriefDesc'].'"></td>');
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['Cat'].'"></td>');
		echo('</tr>');
	}
	echo('<tr><td><input type="number" name="Entry[]" onchange="triggerNewRow(this)"></td>');
	echo('<td><input type="text" name="BriefDesc[]"></td><td></td></tr>');
	
	echo('</tbody></table>');
	echo('<br><input type="submit" name="savedata" value="'.$TAGS['UpdateAxis'][0].'"> ');

	$R = $DB->query("SELECT * FROM rallyparams");
	if ($rd = $R->fetchArray())
	{
		echo('<hr>');
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			if ($i != $axis)
				echo('[ <a href="sm.php?c=showcat&amp;ord=Cat&amp;axis='.$i.$bcurldtl.'">'.$i.'-'.$rd['Cat'.$i.'Label'].'</a> ] ');
			else
				echo(' &nbsp;&nbsp; ');
	}
	
	echo('</form>');
	//showFooter();
}


function showCombinations()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
	

	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0)>0);

	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		$catlabels[$i] = $rd['Cat'.$i.'Label'];

	$R = $DB->query('SELECT * FROM categories ORDER BY Axis,BriefDesc');
	while ($rd = $R->fetchArray())
	{
		if (!isset($cats[$rd['Axis']]))
			$cats[$rd['Axis']][0] = '';
		$cats[$rd['Axis']][$rd['Cat']] = $rd['BriefDesc'];
		
	}


	
	$R = $DB->query('SELECT * FROM combinations ORDER BY ComboID');
	if (!$rd = $R->fetchArray())
		$rd = [];
	if ($DBVERSION < 3)
		$rd['MinimumTicks'] = 0;

?>
<script>
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('bonuses').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
</script>
<?php	


	$myurl = "<a href='sm.php?c=combos'>".$TAGS['ComboMaintHead'][0].'</a>';
	pushBreadcrumb($myurl);
	$bcurldtl ='&amp;breadcrumbs='.urlencode($_REQUEST['breadcrumbs']);
	echo('<form method="post" action="sm.php">');
	echo('<input type="hidden" name="c" value="combo">');
	echo('<input type="hidden" name="comboid" value="">');
	echo('<input type="hidden" name="breadcrumbs" value="'.$_REQUEST['breadcrumbs'].'">');
	echo('<input type="submit" value="'.$TAGS['InsertNewCombo'][0].'" title="'.$TAGS['InsertNewCombo'][1].'">');
	echo('</form>');


	echo('<form method="post" action="sm.php">');

	emitBreadcrumbs();

	echo('<input type="hidden" name="c" value="combos">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<table id="bonuses">');
	echo('<caption title="'.htmlentities($TAGS['ComboMaintHead'][1]).'">'.htmlentities($TAGS['ComboMaintHead'][0]).'</caption>');
	echo('<theadclass="listhead"><tr><th>'.$TAGS['ComboIDLit'][0].'</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	if (false) {
	echo('<th>'.$TAGS['ScoreMethodLit'][0].'</th>');
	}
	echo('<th>'.$TAGS['BonusListLit'][0].'</th>');
	if (false) {
	echo('<th>'.$TAGS['MinimumTicks'][0].'</th>');
	}
	echo('<th>'.$TAGS['PointsMults'][0].'</th>');
	if (false) {
	if ($DBVERSION >= 3)
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			if (isset($cats[$i]))
				echo('<th>'.$catlabels[$i].'</th>');		
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	}
	if ($showclaimsbutton)
		echo('<th class="ClaimsCount">'.$TAGS['ShowClaimsCount'][0].'</th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	
	$sql = 'SELECT * FROM combinations ORDER BY ComboID';
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
		if ($DBVERSION < 3)
			$rd['MinimumTicks'] = 0;
		echo('<tr class="hoverlite" onclick="window.location=\'sm.php?c=combo&amp;comboid='.$rd['ComboID'].$bcurldtl.'\'">');
		echo('<td><input class="ComboID" type="text" name="ComboID[]" readonly value="'.$rd['ComboID'].'"></td>');
		echo('<td><input readonly class="BriefDesc" type="text" name="BriefDesc[]" value="'.$rd['BriefDesc'].'"></td>');
		if (false) {
		echo('<td><select disabled name="ScoreMethod[]">');
		echo('<option value="0" '.($rd['ScoreMethod']<>1 ? 'selected="selected" ' : '').'>'.$TAGS['AddPoints'][0].'</option>');
		echo('<option value="1" '.($rd['ScoreMethod']==1 ? 'selected="selected" ' : '').'>'.$TAGS['AddMults'][0].'</option>');
		echo('</select></td>');
		}
		echo('<td><input readonly title="'.$TAGS['BonusListLit'][1].'" class="Bonuses" type="text" name="Bonuses[]" value="'.$rd['Bonuses'].'" ></td>');
		if (false) {
		echo('<td><input readonly title="'.$TAGS['MinimumTicks'][1].'" type="number" name="MinimumTicks[]" value="'.$rd['MinimumTicks'].'"></td>');
		}
		echo('<td><input readonly title="'.$TAGS['PointsMults'][1].'" class="ScorePoints" type="text" name="ScorePoints[]" value="'.$rd['ScorePoints'].'"></td> ');
		if (false) {
		if ($DBVERSION >= 3)
			for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
				if (isset($cats[$i]))
				{
					echo('<td><select name="Cat'.$i.'Entry[]">');
					foreach ($cats[$i] as $ce => $bd)
					{
						echo('<option value="'.$ce.'" ');
						if ($ce == $rd['Cat'.$i])
							echo('selected ');
						echo('>'.htmlspecialchars($bd).'</option>');
					}
					echo('</select></td>');
				}
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['ComboID'].'">');
		}
		if ($showclaimsbutton)
		{
			$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || CombosTicked || ',' LIKE '%,".$rd['ComboID'].",%'","rex",0);
			echo('<td class="ClaimsCount" title="'.$TAGS['ShowClaimsButton'][1].'">');
			if ($rex > 0)
				echo('<a href='."'entrants.php?c=entrants&mode=combo&bonus=".$rd['ComboID'].$bcurldtl."'".'> &nbsp;'.$rex.'&nbsp; </a>');
			echo('</td>');
		}
		echo('</tr>');
	}
	if (false) {
	echo('<tr class="newrow"><td><input class="ComboID" type="text" name="ComboID[]" onchange="triggerNewRow(this)"></td>');
	echo('<td><input type="text" name="BriefDesc[]"></td>');
	echo('<td><select name="ScoreMethod[]">');
	echo('<option value="0" selected="selected" >'.$TAGS['AddPoints'][0].'</option>');
	echo('<option value="1" >'.$TAGS['AddMults'][0].'</option>');
	echo('</select></td>');
	echo('<td><input title="'.$TAGS['BonusListLit'][1].'" class="Bonuses" type="text" name="Bonuses[]" placeholder="'.$TAGS['CommaSeparated'][0].'"></td>');
	echo('<td><input title="'.$TAGS['MinimumTicks'][1].'" type="number" name="MinimumTicks[]" ></td>');
	echo('<td><input title="'.$TAGS['PointsMults'][1].'" class="ScorePoints" type="text" name="ScorePoints[]" ></td> ');
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($cats[$i]))
		{
			$S = ' selected ';
			echo('<td><select name="Cat'.$i.'Entry[]">');
			foreach ($cats[$i] as $ce => $bd)
			{
				echo('<option value="'.$ce.'" ');
				echo($S);
				$S = '';
				echo('>'.htmlspecialchars($bd).'</option>');
			}
			echo('</select></td>');
		}
	echo('</tr>');
	}
	echo('</tbody></table>');
	if (false) {
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	}
	echo('</form>');
	//showFooter();
	
}

function showSingleCombo($comboid)
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;

	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();

	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		$catlabels[$i] = $rd['Cat'.$i.'Label'];
	


	$R = $DB->query('SELECT * FROM categories ORDER BY Axis,BriefDesc');

	while ($rd = $R->fetchArray())
	{
		if (!isset($cats[$rd['Axis']]))
			$cats[$rd['Axis']][0] = '';
		$cats[$rd['Axis']][$rd['Cat']] = $rd['BriefDesc'];
		
	}

	if ($comboid=='')
	{
		$comboid_ro = '';
		$R = $DB->query("SELECT ComboID FROM combinations ORDER BY ComboID");
		$combos = [];
		while ($rd = $R->fetchArray())
			$combos[$rd['ComboID']] = $rd['ComboID'];
	}
	else
	{
		$comboid_ro = ' readonly ';
		$R = $DB->query("SELECT * FROM combinations WHERE ComboID='".$comboid."'");
		if (!($rd = $R->fetchArray()))
			return;
	}
	
	echo('<div class="comboedit">');
	echo('<form method="post" action="sm.php">');
	echo('<input type="hidden" name="c" value="combo">');
	echo('<input type="hidden" name="menu" value="setup">');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateCombo'][0].'"> ');
	if ($comboid != '')
	{
		echo('<span title="'.$TAGS['DeleteEntryLit'][1].'"><label for="deletecombo">'.$TAGS['DeleteEntryLit'][0].'</label>');
		echo('<input type="checkbox" id="deletecombo" name="DeleteCombo"></span>');
	}
	echo('<span class="vlabel" title="'.$TAGS['ComboIDLit'][1].'"><label class="wide" for="comboid">'.$TAGS['ComboIDLit'][0].'</label> ');
	echo('<input type="text" '.$comboid_ro.' name="comboid" id="comboid" value="'.$rd['ComboID'].'"> </span>');
	echo('<span class="vlabel" title="'.$TAGS['BriefDescLit'][1].'"><label class="wide" for="briefdesc">'.$TAGS['BriefDescLit'][0].'</label> ');
	echo('<input type="text" name="BriefDesc" id="briefdesc" value="'.$rd['BriefDesc'].'"> </span>');
	echo('<span class="vlabel" title="'.$TAGS['CompulsoryBonus'][1].'"><label class="wide" for="compulsory">'.$TAGS['CompulsoryBonus'][0].'</label> ');
	echo('<select name="Compulsory" id="scoremethod">');
	echo('<option value="0" '.($rd['Compulsory']<>1 ? 'selected ' : '').'>'.$TAGS['optOptional'][0].'</option>');
	echo('<option value="1" '.($rd['Compulsory']==1 ? 'selected ' : '').'>'.$TAGS['optCompulsory'][0].'</option>');
	echo('</select></span>');
	echo('<span class="vlabel" title="'.$TAGS['ComboScoreMethod'][1].'"><label class="wide" for="scoremethod">'.$TAGS['ComboScoreMethod'][0].'</label> ');
	echo('<select name="ScoreMethod" id="scoremethod">');
	echo('<option value="0" '.($rd['ScoreMethod']<>1 ? 'selected ' : '').'>'.$TAGS['AddPoints'][0].'</option>');
	echo('<option value="1" '.($rd['ScoreMethod']==1 ? 'selected ' : '').'>'.$TAGS['AddMults'][0].'</option>');
	echo('</select></span>');
	echo('<span class="vlabel" title="'.$TAGS['BonusListLit'][1].'"><label class="wide" for="bonuses">'.$TAGS['BonusListLit'][0].'</label> ');
	echo('<input type="text" name="Bonuses" id="bonuses" value="'.$rd['Bonuses'].'"> </span>');
	echo('<span class="vlabel" title="'.$TAGS['MinimumTicks'][1].'"><label class="wide" for="minimumticks">'.$TAGS['MinimumTicks'][0].'</label> ');
	echo('<input type="number" class="smallnumber" name="MinimumTicks" id="minimumticks" value="'.$rd['MinimumTicks'].'"> </span>');
	echo('<span class="vlabel" title="'.$TAGS['ScoreValue'][1].'"><label class="wide" for="scorepoints">'.$TAGS['ScoreValue'][0].'</label> ');
	echo('<input type="text" name="ScorePoints" id="scorepoints" value="'.$rd['ScorePoints'].'"> </span>');
	
	if ($DBVERSION >= 3)
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			if (isset($cats[$i]))
			{
				echo('<span class="vlabel"><label class="wide" for="Cat'.$i.'Entry">'.$catlabels[$i].'</label> ');
				echo('<select id="Cat'.$i.'Entry" name="Cat'.$i.'Entry">');
				foreach ($cats[$i] as $ce => $bd)
				{
					echo('<option value="'.$ce.'" ');
					if ($ce == $rd['Cat'.$i])
						echo('selected ');
					echo('>'.htmlspecialchars($bd).'</option>');
				}
				echo('</select></span>');
			}
			
	echo('</form>');
	echo('</div>');
}


function showCompoundCalcs()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
	
	$cats = fetchCategoryArrays();
	// Now add the '0' entries
	for ($i=1;$i<=$KONSTANTS['NUMBER_OF_COMPOUND_AXES'];$i++)
		if (isset($cats[$i]))
			$cats[$i][0] = $TAGS['ccApplyToAll'][0];
	
	
	$R = $DB->query('SELECT * FROM rallyparams');
	$AxisLabels = $R->fetchArray();
	$AxisLabels['Cat0Label'] = $TAGS['Cat0Label'][0];
	for ($i=0;$i<=$KONSTANTS['NUMBER_OF_COMPOUND_AXES'];$i++) // Possible to specify no axis
		if ($AxisLabels['Cat'.$i.'Label']=='')
			$AxisLabels['Cat'.$i.'Label']="$i (not used)";
		else
			$AxisLabels['Cat'.$i.'Label']=$AxisLabels['Cat'.$i.'Label'];

	$sql = ($DBVERSION < 3 ? ',0 as Compulsory' : ',Compulsory');
	$R = $DB->query('SELECT rowid as id,Cat,Axis,NMethod,ModBonus,NMin,PointsMults,NPower'.$sql.' FROM catcompound ORDER BY Axis,Cat,NMin DESC');
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	if (!$rd = $R->fetchArray())
		$rd = [];
	$R->reset();

?>
<script>
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('catcalcs').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
</script>
<?php	

	$myurl =  "<a href='sm.php?c=catcalcs'>".$TAGS['AdmCompoundCalcs'][0].'</a>';

	pushBreadcrumb($myurl);
	$bcurldtl ='&amp;breadcrumbs='.urlencode($_REQUEST['breadcrumbs']);
	echo('<form method="get" action="sm.php">');
	echo('<input type="hidden" name="c" value="newcc">');
	echo('<input type="hidden" name="breadcrumbs" value="'.$_REQUEST['breadcrumbs'].'">');
	echo('<input type="submit" value="'.$TAGS['InsertNewCC'][0].'">');
	echo('</form>');
	echo('<form method="post" action="sm.php">');
	emitBreadcrumbs();
	for ($i = 0; $i < $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
	{
		$j = $i + 1;
		$k = '0='.$TAGS['ccApplyToAll'][0];
		if (isset($cats[$j]))
			foreach($cats[$j] as $cat => $bd)
				$k .= ",$cat=$bd";
		echo('<input type="hidden" id="axis'.$j.'cats" value="'.$k.'">');
	}
	echo('<input type="hidden" name="c" value="catcalcs">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<table id="catcalcs">');
	echo('<caption title="'.htmlentities($TAGS['CalcMaintHead'][1]).'">'.htmlentities($TAGS['CalcMaintHead'][0]).'</caption>');
	echo("\r\n".'<thead class="listhead"><tr><th class="rowcol"></th><th class="rowcol">'.$TAGS['AxisLit'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['CatEntry'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['ModBonusLit'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['NMethodLit'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['NMinLit'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['PointsMults'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['NPowerLit'][0].'</th>');
	echo('<th class="rowcol">'.$TAGS['ccCompulsory'][0].'</th>');
	if (false) {
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	}
	echo('</tr>');
	echo('</thead><tbody>');
	
	$rowid = 0;
	while ($rd = $R->fetchArray())
	{
		$rowid++;
		echo("\r\n".'<tr class="hoverlite" onclick="window.location=\'sm.php?c=showcc&amp;ruleid='.$rd['id'].$bcurldtl.'\'">');
		echo('<td class="rowcol">'.$rd['id'].'</td>');
		echo('<td  class="rowcol" title="'.$TAGS['AxisLit'][1].'"><input type="hidden" name="id[]" value="'.$rd['id'].'">');
		//echo(' &nbsp;&nbsp;&nbsp; ');
		if (false) {
		echo('<select disabled  onchange="enableSaveButton();ccShowSelectAxisCats(this.value,document.getElementById(\'selcat'.$rowid.'\'));" name="axis[]">');
		for ($i=0;$i<=$KONSTANTS['NUMBER_OF_COMPOUND_AXES'];$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['Axis'])
				echo(' selected');
			echo('>'.$AxisLabels['Cat'.$i.'Label'].'</option>');
		}
		echo('</select>');
		}
		echo('<span class="rowcol">'.$AxisLabels['Cat'.$rd['Axis'].'Label'].' ('.$rd['Axis'].')</span>');
		echo('</td>');
		echo('<td  class="rowcol" title="'.$TAGS['CatEntry'][1].'">');
		//echo('<input type="number"  onchange="enableSaveButton();" name="Cat[]" value="');
		//echo($rd['Cat']);
		//echo('">');
		
		if (false) {
		echo('<select disabled id="selcat'.$rowid.'" name="Cat[]" onchange="enableSaveButton();" >');
		echo('<option value="0"');
		if ($rd['Cat']==0)
			echo(' selected');
		echo('>'.$TAGS['ccApplyToAll'][0].' (0)</option>');
		if (isset($cats[1]))
			foreach($cats[1] as $cat => $bd)
			{
				echo('<option value="'.$cat.'"');
				if ($rd['Cat']==$cat)
					echo(' selected');
				echo('>'.$bd.' ('.$cat.')</option>');
			}
	
		echo('</select>');
		}
		//print_r($cats);
		$sax = strval($rd['Axis']);  // Key not index
		$scat = strval($rd['Cat']);	 // Key not index
		echo('<span class="rowcol">'.$cats[$sax][$scat].' ('.$rd['Cat'].')</span>');
		
		echo('</td>');
		echo('<td  class="rowcol" title="'.$TAGS['ModBonusLit'][1].'">');
		if (false) {
		echo('<select disabled onchange="enableSaveButton();" name="ModBonus[]">');
		for ($i=0;$i<=1;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['ModBonus'])
				echo(' selected');
			echo('>'.$TAGS['ModBonus'.$i][0].'</option>');
		}
		
		echo('</select>');
		}
		echo('<span class=rowcol">'.$TAGS['ModBonus'.$rd['ModBonus']][0].'</span>');
		echo('</td>');
		
		
		echo('<td  class="rowcol" title="'.$TAGS['NMethodLit'][1].'">');
		if (false) {
		echo('<select disabled onchange="enableSaveButton();" name="NMethod[]">');
		for ($i=-1;$i<=1;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['NMethod'])
				echo(' selected');
			echo('>'.$TAGS['NMethod'.$i][0].'</option>');
		}
		echo('</select>');
		}
		echo('<span class="rowcol">'.$TAGS['NMethod'.$rd['NMethod']][0].'</span>');
		echo('</td>');
		echo('<td  class="rowcol" title="'.$TAGS['NMinLit'][1].'">');
		if (false) {
			echo('<input readonly onchange="enableSaveButton();" type="number" name="NMin[]" value="'.$rd['NMin'].'">');
		}
		echo($rd['NMin']);
		echo('</td>');
		echo('<td  class="rowcol" title="'.$TAGS['PointsMults'][1].'">');
		if (false) {
		echo('<select disabled onchange="enableSaveButton();" name="PointsMults[]">');
		for ($i=0;$i<=1;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['PointsMults'])
				echo(' selected');
			echo('>'.$TAGS['PointsMults'.$i][1].'</option>');
		}
		echo('</select>');
		}
		echo($TAGS['PointsMults'.$rd['PointsMults']][1]);
		echo('</td>');
		echo('<td class="rowcol" title="'.$TAGS['NPowerLit'][1].'">');
		if (false) {
		echo('<input readonly onchange="enableSaveButton();" type="number" name="NPower[]"  value="'.$rd['NPower'].'">');
		}
		echo($rd['NPower']);
		echo('</td>');
		
		echo('<td class="rowcol" title="'.$TAGS['ccCompulsory'][1].'">');
//		echo('<input onchange="enableSaveButton();" type="number" name="Compulsory[]" value="'.$rd['Compulsory'].'">');
		
	if (false) {
	echo('<select disabled name="Compulsory" onchange="enableSaveButton();">');
	for ($i=0;$i<=3;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==$rd['Compulsory'])
			echo(' selected');
		echo('>'.$TAGS['ccCompulsory'.$i][0].'</option>');
	}
	echo('</select>');
	}
		echo($TAGS['ccCompulsory'.$rd['Compulsory']][0]);
		echo('</td>');
		if (false) {
		echo('<td><input onchange="enableSaveButton();" type="checkbox" name="DeleteEntry[]" value="'.$rd['id'].'">');
		}
		echo('</tr>');
	}
	
	
	
	echo('</tbody></table>');
	if (false) {
	echo('<input type="submit" id="savedata" name="savedata" disabled value="'.$TAGS['UpdateCCs'][0].'"> ');
	}
	
	echo('</form>');
	
	//showFooter();
}

function fetchCategoryArrays()
{
	global $DB;
	
	$R = $DB->query("SELECT Axis,Cat,BriefDesc FROM categories ORDER BY Axis,BriefDesc");
	$res = [];
	while ($rd = $R->fetchArray())
		$res[$rd['Axis']][$rd['Cat']] = $rd['BriefDesc'];
	return $res;
}

function saveCompoundCalc()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
	
	if (!isset($_REQUEST['ruleid']))
	{
		$sql = "INSERT INTO catcompound (Axis, Cat, NMethod, ModBonus, NMin, PointsMults, NPower";
		if ($DBVERSION >= 3)
			$sql .= ", Compulsory";
		$sql .= ") VALUES (";
		$sql .= intval($_REQUEST['Axis']);
		$sql .= ','.intval($_REQUEST['Cat']);
		$sql .= ','.intval($_REQUEST['NMethod']);
		$sql .= ','.intval($_REQUEST['ModBonus']);
		$sql .= ','.intval($_REQUEST['NMin']);
		$sql .= ','.intval($_REQUEST['PointsMults']);
		$sql .= ','.intval($_REQUEST['NPower']);
		if ($DBVERSION >= 3)
			$sql .= ','.intval($_REQUEST['Compulsory']);
		$sql .= ');';
	}
	else if (isset($_REQUEST['deletecc']))
	{
		$sql = "DELETE FROM catcompound WHERE rowid=".intval($_REQUEST['ruleid']);
	}
	else
	{
		$sql = "UPDATE catcompound SET ";
		$sql .= "Axis=".intval($_REQUEST['Axis']);
		$sql .= ",Cat=".intval($_REQUEST['Cat']);
		$sql .= ",NMethod=".intval($_REQUEST['NMethod']);
		$sql .= ",ModBonus=".intval($_REQUEST['ModBonus']);
		$sql .= ",NMin=".intval($_REQUEST['NMin']);
		$sql .= ",PointsMults=".intval($_REQUEST['PointsMults']);
		$sql .= ",NPower=".intval($_REQUEST['NPower']);
		if ($DBVERSION >= 3)
			$sql .= ",Compulsory=".intval($_REQUEST['Compulsory']);
		$sql .=  " WHERE rowid=".intval($_REQUEST['ruleid']);
	}
	$DB->exec($sql);

	if (retraceBreadcrumb())
		exit;
}

function showCompoundCalc($ruleid)
{

	global $DB, $TAGS, $KONSTANTS;
	
	$cats = fetchCategoryArrays();

	$R = $DB->query('SELECT * FROM rallyparams');
	
	$AxisLabels = $R->fetchArray();
	$AxisLabels['Cat0Label'] = $TAGS['Cat0Label'][0];
	
	for ($i=0;$i<=$KONSTANTS['NUMBER_OF_COMPOUND_AXES'];$i++) // Possible to specify no axis
		if ($AxisLabels['Cat'.$i.'Label']=='')
			$AxisLabels['Cat'.$i.'Label']="$i ".$TAGS['CatNotUsed'][0];
		else
			$AxisLabels['Cat'.$i.'Label']=$AxisLabels['Cat'.$i.'Label'];
	
	if ($ruleid > 0)
	{
		$R = $DB->query("SELECT * FROM catcompound WHERE rowid=".$ruleid);
		$rd = $R->fetchArray();
	}
	else
	{
		$rd['Axis'] = 1;
		$rd['Cat'] = 0;
		$rd['NMethod'] = 1;
		$rd['ModBonus'] = 0;
		$rd['NMin'] = 1;
		$rd['PointsMults'] = 0;
		$rd['NPower'] = 0;
		$rd['Compulsory'] = 0;
	}
	echo('<form method="post" action="sm.php">');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	for ($i = 0; $i < $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
	{
		$j = $i + 1;
		$k = '0='.$TAGS['ccApplyToAll'][0];
		if (isset($cats[$j]))
			foreach($cats[$j] as $cat => $bd)
				$k .= ",$cat=$bd";
		echo('<input type="hidden" id="axis'.$j.'cats" value="'.$k.'">');
	}
	echo('<input type="hidden" name="c" value="savecalc">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<input type="submit" name="savedata" value="'.$TAGS['SaveNewCC'][0].'"> ');
	if ($ruleid < 1)
		echo('<input type="hidden" name="newcc" value="1">');
	else
	{
		echo('<input type="hidden" name="ruleid" value="'.$ruleid.'">');
		echo('<span title="'.$TAGS['DeleteEntryLit'][1].'">');
		echo('<label for="deletecmd">'.$TAGS['DeleteEntryLit'][0].'</label> ');
		echo('<input id="deletecmd" type="checkbox" name="deletecc">'); 
		echo('</span>');
	}
	echo('<span class="vlabel" title="'.$TAGS['AxisLit'][1].'">');
	echo('<label class="wide" for="axis">'.$TAGS['AxisLit'][0].'</label> ');
	echo('<select id="axis" name="Axis" onchange="ccShowSelectAxisCats(this.value,document.getElementById(\'selcat\'));">');
	for ($i=1;$i<=$KONSTANTS['NUMBER_OF_COMPOUND_AXES'];$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==$rd['Axis'])
			echo(' selected');
		echo('>'.$AxisLabels['Cat'.$i.'Label'].'</option>');
	}
	echo('</select> ');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['CatEntryCC'][1].'">');
	echo('<label class="wide" for="Cat">'.$TAGS['CatEntryCC'][0].'</label> ');
	//echo('<input type="number" name="Cat" id="Cat" value="0">');
	echo('<select id="selcat" name="Cat">');
	echo('<option value="0" ');
	if ($rd['Cat']==0) echo(' selected');
	echo('>'.$TAGS['ccApplyToAll'][0].' (0)</option>');
	if (isset($cats[1]))
		foreach($cats[1] as $cat => $bd)
		{
			echo('<option value="'.$cat.'"');
			if ($rd['Cat']==$cat) echo(' selected');
			echo('>'.$bd.' ('.$cat.')</option>');
		}
	
	echo('</select>');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['ModBonusLit'][1].'">');
	echo('<label class="wide" for="ModBonus">'.$TAGS['ModBonusLit'][0].'</label> ');
	echo('<select name="ModBonus">');
	for ($i=0;$i<=1;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==$rd['ModBonus'])
			echo(' selected');
		echo('>'.$TAGS['ModBonus'.$i][1].'</option>');
	}
	echo('</select>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NMethodLit'][1].'">');
	echo('<label class="wide" for="NMethod">'.$TAGS['NMethodLit'][0].'</label> ');
	echo('<select name="NMethod">');
	echo('<option value="0" '.($rd['NMethod']==0 ? 'selected>' : '>').$TAGS['NMethod0'][1].'</option>');
	echo('<option value="1"'.($rd['NMethod']==1 ? 'selected>' : '>').$TAGS['NMethod1'][1].'</option>');
	echo('<option value="-1"'.($rd['NMethod']<0 ? 'selected>' : '>').$TAGS['NMethod-1'][1].'</option>');
	echo('</select> ');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NMinLit'][1].'">');
	echo('<label class="wide" for="NMin">'.$TAGS['NMinLit'][0].'</label> ');
	echo('<input type="number" name="NMin" value="'.$rd['NMin'].'">');
	echo('</span>');
	
	echo('<span class="vlabel" title"'.$TAGS['PointsMults'][1].'">');
	echo('<label class="wide" for="PointsMults">'.$TAGS['PointsMults'][0].'</label> ');
	echo('<select name="PointsMults">');
	for ($i=0;$i<=1;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==$rd['PointsMults'])
			echo(' selected');
		echo('>'.$TAGS['PointsMults'.$i][1].'</option>');
	}
	echo('</select>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NPowerLit'][1].'">');
	echo('<label class="wide" for="NPower">'.$TAGS['NPowerLit'][0].'</label> ');
	echo('<input type="number" name="NPower"  value="'.$rd['NPower'].'">');
	echo('</span>');

	echo('<span class="vlabel" title="'.$TAGS['ccCompulsory'][1].'">');
	echo('<label class="wide" for="ccCompulsory">'.$TAGS['ccCompulsory'][0].'</label> ');
	//echo('<input type="number" id="ccCompulsory" name="Compulsory" value="0">');
	echo('<select name="Compulsory">');
	for ($i=0;$i<=3;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==$rd['Compulsory'])
			echo(' selected');
		echo('>'.$TAGS['ccCompulsory'.$i][1].'</option>');
	}
	echo('</select>');
	echo('</span>');

	echo('</form>');
	//showFooter();
}

function showNewCompoundCalc()
{
	
	showCompoundCalc(0);
	
}




function showRallyConfig()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
	

	
	$R = $DB->query('SELECT * FROM rallyparams');
	if (!$rd = $R->fetchArray())
		$rd = [];
	echo('<br><form method="post" action="sm.php">');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<input type="hidden" name="c" value="rallyparams">');
	echo('<input type="hidden" name="menu" value="setup">');
	
	echo('<div class="tabs_area" style="display:inherit"><ul id="tabs">');
	echo('<li><a href="#tab_basic">'.$TAGS['BasicRallyConfig'][0].'</a></li>');
	echo('<li><a href="#tab_scoring">'.$TAGS['ScoringMethod'][0].'</a></li>');
	echo('<li><a href="#tab_categories">'.$TAGS['rcCategories'][0].'</a></li>');
	echo('<li><a href="#tab_penalties">'.$TAGS['ExcessMileage'][0].'</a></li>');
	echo('<li><a href="#tab_rejections">'.$TAGS['RejectReasons'][0].'</a></li>');
	echo('</ul></div>');
	
	
	
	
	echo('<fieldset id="tab_basic" class="tabContent"><legend>'.$TAGS['BasicRallyConfig'][0].'</legend>');
	echo('<span class="vlabel">');
	echo('<label for="RallyTitle" class="vlabel">'.$TAGS['RallyTitle'][0].' </label> ');
	echo('<input size="50" type="text" name="RallyTitle" id="RallyTitle" value="'.htmlspecialchars($rd['RallyTitle']).'" title="'.$TAGS['RallyTitle'][1].'"> ');
	//echo(' <input type="button" onclick="alert(document.getElementById(\'RallyTitle\').getAttribute(\'title\'));" value="?">');
	echo('</span>');
	
	echo('<span class="vlabel">');
	echo('<label for="RallySlogan" class="vlabel">'.$TAGS['RallySlogan'][0].' </label> ');
	echo('<input size="50" type="text" name="RallySlogan" id="RallySlogan" value="'.htmlspecialchars($rd['RallySlogan']).'" title="'.$TAGS['RallySlogan'][1].'"> ');
	echo('</span>');
	
	$maxhourslit = ($DBVERSION >= 4) ? "MaxHours" : "CertificateHours";
	echo('<span class="vlabel">');
	echo('<label for="MaxHours" class="vlabel">'.$TAGS['MaxHours'][0].' </label> ');
	echo('<input type="number" name="MaxHours" id="MaxHours" value="'.$rd[$maxhourslit].'" title="'.$TAGS['MaxHours'][1].'"> ');
	echo('</span>');

	$dt = splitDatetime($rd['StartTime']); 

	echo('<span class="vlabel">');
	echo('<label for="StartDate" class="vlabel">'.$TAGS['StartDate'][0].' </label> ');
	echo('<input type="date" name="StartDate" id="StartDate" value="'.$dt[0].'" title="'.$TAGS['StartDate'][1].'"> ');
	echo('<label for="StartTime">'.$TAGS['StartTime'][0].' </label> ');
	echo('<input type="time" name="StartTime" id="StartTime" value="'.$dt[1].'" title="'.$TAGS['StartTime'][1].'"> ');
	echo('</span>');


	$dt = splitDatetime($rd['FinishTime']); 

	echo('<span class="vlabel">');
	echo('<label for="FinishDate" class="vlabel">'.$TAGS['FinishDate'][0].' </label> ');
	echo('<input type="date" name="FinishDate" id="FinishDate" value="'.$dt[0].'" title="'.$TAGS['FinishDate'][1].'"> ');
	echo('<label for="FinishTime">'.$TAGS['FinishTime'][0].' </label> ');
	echo('<input type="time" name="FinishTime" id="FinishTime" value="'.$dt[1].'" title="'.$TAGS['FinishTime'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="OdoCheckMiles" class="vlabel">'.$TAGS['OdoCheckMiles'][0].' </label> ');
	echo('<input type="number" step="any" name="OdoCheckMiles" id="OdoCheckMiles" value="'.$rd['OdoCheckMiles'].'" title="'.$TAGS['OdoCheckMiles'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="MinMiles" class="vlabel">'.$TAGS['MinMiles'][0].' </label> ');
	echo('<input type="number" name="MinMiles" id="MinMiles" value="'.$rd['MinMiles'].'" title="'.$TAGS['MinMiles'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="MinPoints" class="vlabel">'.$TAGS['MinPoints'][0].' </label> ');
	echo('<input type="number" name="MinPoints" id="MinPoints" value="'.$rd['MinPoints'].'" title="'.$TAGS['MinPoints'][1].'"> ');
	echo('</span>');

	echo('</fieldset>');

	echo('<fieldset id="tab_scoring" class="tabContent"><legend>'.$TAGS['LegendScoring'][0].'</legend>');
	
	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['ScoringMethod'][0].': </span> ');
	echo('<select name="ScoringMethod">');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['ManualScoring']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['ManualScoring'].'">'.$TAGS['ScoringMethodM'][0].'</option>');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['SimpleScoring']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['SimpleScoring'].'">'.$TAGS['ScoringMethodS'][0].'</option>');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['CompoundScoring']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['CompoundScoring'].'">'.$TAGS['ScoringMethodC'][0].'</option>');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['AutoScoring']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['AutoScoring'].'">'.$TAGS['ScoringMethodA'][0].'</option>');
	echo('</select>');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['ShowMultipliers'][0].': </span> ');
	echo('<select name="ShowMultipliers">');
	$chk = ($rd['ShowMultipliers']==$KONSTANTS['SuppressMults']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['SuppressMults'].'">'.$TAGS['ShowMultipliersN'][0].'</option>');
	$chk = ($rd['ShowMultipliers']==$KONSTANTS['ShowMults']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['ShowMults'].'">'.$TAGS['ShowMultipliersY'][0].'</option>');
	$chk = ($rd['ShowMultipliers']==$KONSTANTS['AutoShowMults']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['AutoShowMults'].'">'.$TAGS['ScoringMethodA'][0].'</option>');
	
	echo('</select>');
	echo('</span>');


	echo('<span class="vlabel">');
	echo('<label for="TiedPointsRanking" title="'.$TAGS['TiedPointsRanking'][1].'">'.$TAGS['TiedPointsRanking'][0].' </label> ');
	$chk = ($rd['TiedPointsRanking']==$KONSTANTS['TiedPointsSplit']) ? ' checked="checked" ' : '';
	echo(' &nbsp;&nbsp;<input type="checkbox"'.$chk.' name="TiedPointsRanking" id="TiedPointsRanking" value="'.$KONSTANTS['TiedPointsSplit'].'">');
	echo('</span>');


	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['TeamRankingText'][0].': </span>');
	echo('<select name="TeamRanking">');
	$chk = ($rd['TeamRanking']==$KONSTANTS['RankTeamsAsIndividuals']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['RankTeamsAsIndividuals'].'">'.$TAGS['TeamRankingI'][0].'</option>');
	$chk = ($rd['TeamRanking']==$KONSTANTS['RankTeamsHighest']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['RankTeamsHighest'].'">'.$TAGS['TeamRankingH'][0].'</option>');
	$chk = ($rd['TeamRanking']==$KONSTANTS['RankTeamsLowest']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['RankTeamsLowest'].'">'.$TAGS['TeamRankingL'][0].'</option>');
	echo('</select>');
	echo('</span>');

	if ($DBVERSION >= 4)
	{
		echo('<span class="vlabel">');
		echo('<label for="AutoRank" title="'.$TAGS['AutoRank'][1].'">'.$TAGS['AutoRank'][0].' </label> ');
		$chk = ($rd['AutoRank']==$KONSTANTS['AutoRank']) ? ' checked ' : '';
		echo(' &nbsp;&nbsp;<input type="checkbox"'.$chk.' name="AutoRank" id="AutoRank" value="'.$KONSTANTS['AutoRank'].'">');
		echo('</span>');
	}
	echo('</fieldset>');

	echo('<fieldset id="tab_categories" class="tabContent"><legend>'.$TAGS['rcCategories'][0].'</legend>');
	

	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
	{
		echo('<span class="vlabel">');
		echo('<label for="Cat'.$i.'Label"  class="vlabel" title="'.$TAGS['Cat'.$i.'Label'][1].'">'.$TAGS['Cat'.$i.'Label'][0].' </label> ');
		echo('<input type="text" name="Cat'.$i.'Label" id="Cat'.$i.'Label" value="'.htmlspecialchars($rd['Cat'.$i.'Label']).'" title="'.$TAGS['Cat'.$i.'Label'][1].'" placeholder="'.$TAGS['unset'][0].'"> ');
		echo('</span>');
	}
	

	echo('</fieldset>');

	
	echo('<fieldset id="tab_penalties" class="tabContent"><legend>'.$TAGS['ExcessMileage'][0].'</legend>');

	echo('<span class="vlabel">');
	echo('<label for="PenaltyMaxMiles" class="vlabel wide">'.$TAGS['PenaltyMaxMiles'][0].' </label> ');
	echo('<input type="number" name="PenaltyMaxMiles" id="PenaltyMaxMiles" value="'.$rd['PenaltyMaxMiles'].'" title="'.$TAGS['PenaltyMaxMiles'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['MilesPenaltyText'][0].': </span> ');
	echo('<select name="MaxMilesMethod">');
	//echo('<label for="MaxMilesFixedP"  class="vlabel" title="'.$TAGS['MaxMilesFixedP'][1].'">'.$TAGS['MaxMilesFixedP'][0].' </label> ');
	$chk = ($rd['MaxMilesMethod']==$KONSTANTS['MaxMilesFixedP']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['MaxMilesFixedP'].'">'.$TAGS['MaxMilesFixedP'][0].'</option>');
	//echo(' <input type="radio"'.$chk.' name="MaxMilesMethod" id="MaxMilesFixedP" value="'.$KONSTANTS['MaxMilesFixedP'].'" title="'.$TAGS['MaxMilesFixedP'][1].'"> ');
	//echo('<label for="MaxMilesFixedM" title="'.$TAGS['MaxMilesFixedM'][1].'">'.$TAGS['MaxMilesFixedM'][0].' </label> ');
	$chk = ($rd['MaxMilesMethod']==$KONSTANTS['MaxMilesFixedM']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['MaxMilesFixedM'].'">'.$TAGS['MaxMilesFixedM'][0].'</option>');	
	//echo(' <input type="radio"'.$chk.' name="MaxMilesMethod" id="MaxMilesFixedM" value="'.$KONSTANTS['MaxMilesFixedM'].'" title="'.$TAGS['MaxMilesFixedM'][1].'"> ');
	//echo('<label for="MaxMilesPerMile" title="'.$TAGS['MaxMilesPerMile'][1].'">'.$TAGS['MaxMilesPerMile'][0].' </label> ');
	$chk = ($rd['MaxMilesMethod']==$KONSTANTS['MaxMilesPerMile']) ? ' selected ' : '';
	echo('<option '.$chk.' value="'.$KONSTANTS['MaxMilesPerMile'].'">'.$TAGS['MaxMilesPerMile'][0].'</option>');
	//echo(' <input type="radio"'.$chk.' name="MaxMilesMethod" id="MaxMilesPerMile" value="'.$KONSTANTS['MaxMilesPerMile'].'" title="'.$TAGS['MaxMilesPerMile'][1].'"> ');
	echo('</select>');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="MaxMilesPoints" class="vlabel wide">'.$TAGS['MaxMilesPoints'][0].' </label> ');
	echo('<input type="number" name="MaxMilesPoints" id="MaxMilesPoints" value="'.$rd['MaxMilesPoints'].'" title="'.$TAGS['MaxMilesPoints'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="PenaltyMilesDNF" class="vlabel wide">'.$TAGS['PenaltyMilesDNF'][0].' </label> ');
	echo('<input type="number" name="PenaltyMilesDNF" id="PenaltyMilesDNF" value="'.$rd['PenaltyMilesDNF'].'" title="'.$TAGS['PenaltyMilesDNF'][1].'"> ');
	echo('</span>');

	echo('</fieldset>');

	echo('<fieldset id="tab_rejections" class="tabContent"><legend>'.$TAGS['RejectReasons'][0].'</legend>');
	echo('<p>'.$TAGS['RejectReasons'][1].'</p>');
	echo('<ol>');
	$rejectreasons = explode("\n",$rd['RejectReasons']);
	foreach($rejectreasons as $rrline)
	{
		$rr = explode('=',$rrline);
		if (count($rr)==2 && intval($rr[0])>0 && intval($rr[0])<10) {
			echo('<li>');
			echo('<input type="text" name="RejectReason[]" data-code="'.$rr[0].'" value="'.$rr[1].'">');
			echo('</li>');
		}		
	}
	echo('</ol>');
	echo('</fieldset>');
	
	echo('<input type="submit" name="savedata" value="'.$TAGS['SaveRallyConfig'][0].'">');
	echo('</form>');
	//showFooter();
}


function showSGroups()
{
	global $DB, $TAGS, $KONSTANTS;
	

	
	$R = $DB->query('SELECT * FROM sgroups ORDER BY GroupName');
	if (!$rd = $R->fetchArray())
		$rd = [];

?>
<script>
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	var tab = document.getElementById('sgroups').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
	document.getElementsByClassName('newGroupType')[0].disabled = false;
	
}
</script>
<?php	


	echo('<form method="post" action="sm.php">');

	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" name="c" value="sgroups">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<table id="sgroups">');
	echo('<caption title="'.htmlentities($TAGS['SGroupMaintHead'][1]).'">'.htmlentities($TAGS['SGroupMaintHead'][0]).'</caption>');
	echo('<thead><tr><th style="text-align: left; ">'.$TAGS['SGroupLit'][0].'</th>');
	echo('<th style="text-align: left; ">'.$TAGS['SGroupTypeLit'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	
	$sql = 'SELECT * FROM sgroups ORDER BY GroupName';
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="hoverlite"><td><input class="SGroupName" type="text" name="GroupName[]" readonly value="'.$rd['GroupName'].'"></td>');
		echo('<td>');
		echo('<select name="GroupType[]">');
		echo('<option value="R"'.($rd['GroupType']=='R' ? ' selected ' : '').'>'.$TAGS['SGroupTypeR'][1].'</option>');
		echo('<option value="C"'.($rd['GroupType']=='C' ? ' selected ' : '').'>'.$TAGS['SGroupTypeC'][1].'</option>');
		echo('</select>');
		echo('</td>');
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['GroupName'].'">');
		echo('</tr>');
	}
	echo('<tr class="newrow"><td><input type="text" placeholder="'.$TAGS['NewPlaceholder'][0].'" name="GroupName[]" onchange="triggerNewRow(this)"></td>');
		echo('<td><select class="newGroupType" name="GroupType[]" disabled>');
		echo('<option value="R">'.$TAGS['SGroupTypeR'][1].'</option>');
		echo('<option value="C" selected>'.$TAGS['SGroupTypeC'][1].'</option>');
		echo('</select>');
		echo('</td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateSGroups'][0].'"> ');
	echo('</form>');
	//showFooter();
	
}



function showSpecial($specialid)
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
	
	$sql = "SELECT * FROM sgroups ORDER BY GroupName";
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	$groups = [''];
	$ngroups = 1;
	while($rd = $R->fetchArray())
	{
		array_push($groups,$rd['GroupName']);
		$ngroups++;
	}
	//print_r($groups);
	//echo($ngroups);
	$sql = "SELECT * FROM specials WHERE BonusID='".$specialid."'";
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	$rd = $R->fetchArray();
	$valid = "if(document.querySelector('#BonusID').value==''){";
	$valid .= "document.querySelector('#BonusID').focus();return false;}";
	$valid .= "if(document.querySelector('#BriefDesc').value==''){";
	$valid .= "document.querySelector('#BriefDesc').focus();return false;}";
	$valid .= "return true;";
	echo("\r\n");
	echo('<form onsubmit="'.$valid.'">');
	echo('<input type="hidden" name="c" value="special">');
	emitBreadcrumbs();
	echo('<span class="vlabel" title="'.$TAGS['BonusIDLit'][1].'"><label for="BonusID">'.$TAGS['BonusIDLit'][0].'</label> ');
	$ro = ($specialid != '' ? ' readonly ' : '');
	echo('<input type="text"'.$ro.' name="BonusID" id="BonusID" value="'.$specialid.'" onchange="enableSaveButton();">');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['BriefDescLit'][1].'"><label for="BriefDesc">'.$TAGS['BriefDescLit'][0].'</label> ');
	echo('<input type="text" name="BriefDesc" id="BriefDesc" value="'.$rd['BriefDesc'].'" onchange="enableSaveButton();">');
	echo('</span>');
	if ($ngroups > 1)
	{
		echo('<span class="vlabel" title="'.$TAGS['GroupNameLit'][1].'"><label for="GroupName">'.$TAGS['GroupNameLit'][0].'</label> ');
		echo('<select name="GroupName" id="GroupName" onchange="enableSaveButton();">');
		for ($i=0; $i<$ngroups; $i++)
			echo('<option value="'.$groups[$i].'" '.($rd['GroupName']==$groups[$i] ? ' selected' : '').'>'.$groups[$i].'</option>');
		echo('</select>');
		echo('</span>');
	}
	else // No special groups available so don't offer any
		echo('<input type="hidden" name="GroupName" id="GroupName" value="'.$rd['GroupName'].'">');
	echo('<span class="vlabel" title="'.$TAGS['SpecialPointsLit'][1].'"><label for="Points">'.$TAGS['SpecialPointsLit'][0].'</label> ');
	echo('<input type="number" name="Points" id="Points" value="'.$rd['Points'].'" onchange="enableSaveButton();">');
//	echo('</span>');
//	echo('<span class="vlabel"><label for="AskPoints">'.$TAGS['AskPoints'][0].'</label> ');
//	echo(' <label for="AskPoints">'.$TAGS['AskPoints'][0].'</label> ');
	echo(' <select name="AskPoints" id="AskPoints" onchange="enableSaveButton();">');
	echo('<option value="0"'.($rd['AskPoints']==0 ? ' selected>' : '>').$TAGS['AskPoints0'][0].'</option>');
	echo('<option value="1"'.($rd['AskPoints']==0 ? '>' : ' selected>').$TAGS['AskPoints1'][0].'</option>');
	echo('</select>');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['SpecialMultLit'][1].'"><label for="MultFactor">'.$TAGS['SpecialMultLit'][0].'</label> ');
	echo('<input type="number" class="smallnumber" name="MultFactor" id="MultFactor" value="'.$rd['MultFactor'].'" onchange="enableSaveButton();">');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['CompulsoryBonus'][1].'"><label for="Compulsory">'.$TAGS['CompulsoryBonus'][0].'</label> ');
	echo('<select name="Compulsory" id="Compulsory" onchange="enableSaveButton();">');
	echo('<option value="0"'.($rd['Compulsory']==0 ? ' selected>' : '>').$TAGS['CompulsoryBonus0'][0].'</option>');
	echo('<option value="1"'.($rd['Compulsory']==0 ? ' >' : ' selected>').$TAGS['CompulsoryBonus1'][0].'</option>');
	echo('</select>');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['RestMinutesLit'][1].'"><label for="RestMinutes">'.$TAGS['RestMinutesLit'][0].'</label> ');
	echo('<input type="number" class="smallnumber" name="RestMinutes" id="RestMinutes" value="'.$rd['RestMinutes'].'" onchange="enableSaveButton();">');
//	echo('</span>');
//	echo('<span class="vlabel"><label for="AskMinutes">'.$TAGS['AskMinutes'][0].'</label> ');
//	echo(' <label for="AskMinutes">'.$TAGS['AskMinutes'][0].'</label> ');
	echo(' <select name="AskMinutes" id="AskMinutes" onchange="enableSaveButton();">');
	echo('<option value="0"'.($rd['AskMinutes']==0 ? ' selected>' : '>').$TAGS['AskMinutes0'][0].'</option>');
	echo('<option value="1"'.($rd['AskMinutes']==0 ? '>' : ' selected>').$TAGS['AskMinutes1'][0].'</option>');
	echo('</select>');
	echo('<span class="vlabel">');
	if ($specialid != '')
	{
		echo(' <input type="submit" name="savedata" id="savedata" data-altvalue="'.$TAGS['SaveRecord'][0].'" value="'.$TAGS['RecordSaved'][0].'" disabled> ');
		$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || SpecialsTicked || ',' LIKE '%,".$rd['BonusID'].",%'","rex",0);
		if ($rex < 1)
		{
			echo(' <input type="submit" name="delete" value="'.$TAGS['DeleteBonus'][0].'"');
			if ($rex > 0)
				echo(' disabled');
			echo('>');
		}
	}
	else
		echo(' <input type="submit" name="savedata" id="savedata" value="'.$TAGS['SaveRecord'][0].'"> ');
	
	
	
	echo('</span>');
	echo('</form>');
}


function showSpecials()
{
	global $DB, $TAGS, $KONSTANTS;

	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0)>0);

	$bcurldtl ='&amp;breadcrumbs='.urlencode($_REQUEST['breadcrumbs']);

	echo('<table id="bonuses">');
	echo('<caption title="'.htmlentities($TAGS['SpecialMaintHead'][1]).'">'.htmlentities($TAGS['SpecialMaintHead'][0]));
	echo(' <input type="button" value="'.$TAGS['AdmNewBonus'][0].'" onclick="window.location='."'sm.php?c=special&bonus".$bcurldtl."'".'">');
	echo('</caption>');
	echo('<thead><tr><th>'.$TAGS['BonusIDLit'][0].'</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	echo('<th>'.$TAGS['SpecialPointsLit'][0].'</th>');
	echo('<th>'.$TAGS['AskPoints'][0].'</th>');
	echo('<th>'.$TAGS['CompulsoryBonus'][0].'</th>');
	if ($showclaimsbutton)
		echo('<th class="ClaimsCount">'.$TAGS['ShowClaimsCount'][0].'</th>');
	echo('</tr>');
	echo('</thead><tbody>');

	$sql = 'SELECT * FROM specials ORDER BY BonusID';
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
	
		echo('<tr class="link" onclick="window.location.href=\'sm.php?c=special&amp;bonus='.$rd['BonusID'].$bcurldtl.'\'">');
		echo('<td>'.$rd['BonusID'].'</td>');
		echo('<td>'.$rd['BriefDesc'].'</td>');
		echo('<td>'.$rd['Points'].'</td>');
		if ($rd['AskPoints']<>0)
			$chk = " &checkmark; ";
		else
			$chk = "";
		echo('<td class="center">'.$chk.'</td>');
		if ($rd['Compulsory']<>0)
			$chk = " &checkmark; ";
		else
			$chk = "";
		echo('<td class="center">'.$chk.'</td>');
		if ($showclaimsbutton)
		{
			$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || SpecialsTicked || ',' LIKE '%,".$rd['BonusID'].",%'","rex",0);
			echo('<td class="ClaimsCount" title="'.$TAGS['ShowClaimsButton'][1].'">');
			if ($rex > 0)
				echo('<a href='."'entrants.php?c=entrants&mode=special&bonus=".$rd['BonusID']."'".'> '.$rex.' </a>');
			echo('</td>');
		}
	
	echo('</tr>');

	}
	echo('</tbody></table>');
}

function showSpecialsX()
{
	global $DB, $TAGS, $KONSTANTS;
	
	$R = $DB->query("SELECT GroupName FROM sgroups ORDER BY GroupName");
	$SG = array(''=>'');
	while ($rd = $R->fetchArray())
	{
		$SG[$rd['GroupName']] = $rd['GroupName'];
	}
	$groups_used = count($SG) > 1;
	
	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0)>0);
	
	$R = $DB->query('SELECT * FROM specials ORDER BY BonusID');
	if (!$rd = $R->fetchArray())
		$rd = [];

?>
<script>
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('bonuses').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
</script>
<?php	


	echo('<form method="post" action="sm.php">');

	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" name="c" value="specials">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	echo('<table id="bonuses">');
	echo('<caption title="'.htmlentities($TAGS['SpecialMaintHead'][1]).'">'.htmlentities($TAGS['SpecialMaintHead'][0]).'</caption>');
	echo('<thead><tr><th>'.$TAGS['BonusIDLit'][0].'</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	if ($groups_used)
		echo('<th>'.$TAGS['GroupNameLit'][0].'</th>');
	echo('<th>'.$TAGS['SpecialPointsLit'][0].'</th>');
	echo('<th>'.$TAGS['SpecialMultLit'][0].'</th>');
	echo('<th>'.$TAGS['CompulsoryBonus'][0].'</th>');
	echo('<th>'.$TAGS['AskPoints'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	if ($showclaimsbutton)
		echo('<th class="ClaimsCount">'.$TAGS['ShowClaimsCount'][0].'</th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	
	$sql = 'SELECT * FROM specials ORDER BY BonusID';
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="hoverlite"><td><input class="BonusID" type="text" name="BonusID[]" readonly value="'.$rd['BonusID'].'"></td>');
		echo('<td><input class="BriefDesc" type="text" name="BriefDesc[]" value="'.$rd['BriefDesc'].'"></td>');
		if ($groups_used)
		{
			echo('<td><select class="GroupName" name="GroupName[]">');
			foreach ($SG As $G => $gv)
			{
				echo('<option value="'.$G.'"');
				if ($G == $rd['GroupName'])
					echo(' selected');
				echo('>'.$G.'</option>');
			}
			echo('</select></td>');
		}
		echo('<td><input class="Points" type="number" name="Points[]" value="'.$rd['Points'].'"></td>');
		echo('<td><input class="MultFactor" type="number" name="MultFactor[]" value="'.$rd['MultFactor'].'"></td>');
		if ($rd['Compulsory']==1)
			$chk = " checked ";
		else
			$chk = "";
		echo('<td class="center"><input type="checkbox"'.$chk.' name="Compulsory[]" value="'.$rd['BonusID'].'">');
		if ($rd['AskPoints']==1)
			$chk = " checked ";
		else
			$chk = "";
		echo('<td class="center"><input type="checkbox"'.$chk.' name="AskPoints[]" value="'.$rd['BonusID'].'">');
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['BonusID'].'">');
		if ($showclaimsbutton)
		{
			$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || SpecialsTicked || ',' LIKE '%,".$rd['BonusID'].",%'","rex",0);
			echo('<td class="ClaimsCount" title="'.$TAGS['ShowClaimsButton'][1].'">');
			if ($rex > 0)
				echo('<a href='."'entrants.php?c=entrants&mode=special&bonus=".$rd['BonusID']."'".'> '.$rex.' </a>');
			echo('</td>');
		}
		echo('</tr>');
	}
	echo('<tr class="newrow"><td><input type="text" name="BonusID[]" onchange="triggerNewRow(this)"></td>');
	echo('<td><input type="text" name="BriefDesc[]"></td>');
		if ($groups_used)
		{
			echo('<td><select class="GroupName" name="GroupName[]">');
			foreach ($SG As $G => $gv)
			{
				echo('<option value="'.$G.'"');
				echo('>'.$G.'</option>');
			}
			echo('</select></td>');
		}
	echo('<td><input type="number" name="Points[]"></td>');
	echo('<td><input type="number" name="MultFactor[]"></td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	echo('</form>');
	//showFooter();
	
}








function showTimePenalties()
{
	global $DB, $TAGS, $KONSTANTS, $DBVERSION;
	

	if ($DBVERSION < 3)
		$ts = "0 as TimeSpec,";
	else
		$ts = "TimeSpec,";
	
	$R = $DB->query('SELECT rowid AS id,'.$ts.'PenaltyStart,PenaltyFinish,PenaltyMethod,PenaltyFactor FROM timepenalties ORDER BY PenaltyStart');
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');

?>
<script>
function deleteRow(e)
{
    e = e || window.event;
    let target = e.target || e.srcElement;	
	document.querySelector('#timepenalties').deleteRow(target.parentNode.parentNode.rowIndex);
	enableSaveButton();
}
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('timepenalties').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
function changeTimeSpec(obj)
{
	function setv(obj,v)
	{
		try {
			obj.value = v;
		} catch(err) {
		}
	}
	
	var row = obj.parentNode.parentNode; // TR
	var opt = obj.value;
	var idt = row.getElementsByClassName('date');
	var iti = row.getElementsByClassName('time');
	xdt = opt==0 ? 'date' : 'hidden';
	xti = opt==0 ? 'time' : 'number';
	for (var i=0; i < idt.length; i++)
	{
		var v = idt[i].value;
		idt[i].type = xdt;
		setv(idt[i],v);
		v = iti[i].value;
		iti[i].type = xti;
		setv(iti[i],v);
	}
	enableSaveButton();
}
</script>
<?php	


	echo('<form method="post" action="sm.php">');

	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" name="c" value="timep">');
	echo('<input type="hidden" name="menu" value="setup">');
	if ($DBVERSION >= 3)
	{
		echo('<p class="explain">'.$TAGS['TimePExplain'][0].'</p>');
	}
	echo('<table id="timepenalties">');
	echo('<caption title="'.htmlentities($TAGS['TimepMaintHead'][1]).'">'.htmlentities($TAGS['TimepMaintHead'][0]).'</caption>');
	echo('<thead><tr><th>'.$TAGS['tpTimeSpecLit'][0].'</th><th>'.$TAGS['tpStartLit'][0].'</th>');
	echo('<th>'.$TAGS['tpFinishLit'][0].'</th>');
	echo('<th>'.$TAGS['tpMethodLit'][0].'</th>');
	echo('<th>'.$TAGS['tpFactorLit'][0].'</th>');
	echo('<th></th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	
	while ($rd = $R->fetchArray())
	{
		echo("\n".'<tr class="hoverlite">');
		echo('<td><input type="hidden" name="id[]" value="'.$rd['id'].'">');
		echo('<select name="TimeSpec[]" onchange="changeTimeSpec(this)">');
		for ($i=0; $i<3; $i++) // Max TimeSpec==3
		{
			echo('<option value="'.$i.'" ');
			if ($i==$rd['TimeSpec'])
				echo(' selected ');
			echo('>'.$TAGS['tpTimeSpec'.$i][0].'</option>');
		}
		echo('</select></td><td title="'.$TAGS['tpStartLit'][1].'">');
		if ($rd['TimeSpec']==$KONSTANTS['TimeSpecDatetime'])
		{
			$dtx = splitDatetime($rd['PenaltyStart']);
			echo('<input type="date" class="date" name="PenaltyStartDate[]" value="'.$dtx[0].'" onchange="enableSaveButton();"> ');
			echo('<input type="time" class="time" name="PenaltyStartTime[]" value="'.$dtx[1].'" onchange="enableSaveButton();"></td>');
			$dtx = splitDatetime($rd['PenaltyFinish']);		
			echo('<td title="'.$TAGS['tpFinishLit'][1].'"><input class="date" type="date" name="PenaltyFinishDate[]" value="'.$dtx[0].'" onchange="enableSaveButton();"> ');
			echo('<input class="time" type="time" name="PenaltyFinishTime[]" value="'.$dtx[1].'" onchange="enableSaveButton();"></td>');
		}
		else
		{
			echo('<input class="date" type="hidden" name="PenaltyStartDate[]" value="0"> ');
			echo('<input class="time" type="number" name="PenaltyStartTime[]" value="'.$rd['PenaltyStart'].'" onchange="enableSaveButton();"></td>');
			echo('<td title="'.$TAGS['tpFinishLit'][1].'"><input class="date" type="hidden" name="PenaltyFinishDate[]" value="0"> ');
			echo('<input class="time" type="number" name="PenaltyFinishTime[]" value="'.$rd['PenaltyFinish'].'" onchange="enableSaveButton();"></td>');
		}
		echo('<td><select name="PenaltyMethod[]" onchange="enableSaveButton();">');
		for ($i=0;$i<=3;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['PenaltyMethod'])
				echo(' selected');
			echo(">");
			echo($TAGS['tpMethod'.$i][1].'</option>');
		}
		echo('</select></td>');
		echo('<td><input type="number" name="PenaltyFactor[]" value="'.$rd['PenaltyFactor'].'" onchange="enableSaveButton();"></td>');
		echo('<td class="center"><button value="-" onclick="deleteRow(event);return false;">-</button></td>');
		echo('</tr>');
	}
	echo('<tr class="newrow hide"><td><input type="hidden" name="id[]" value="">');
	echo('<select name="TimeSpec[]" onchange="changeTimeSpec(this)">');
	for ($i=0; $i<3; $i++) // Max TimeSpec==3
	{
		echo('<option value="'.$i.'" ');
		if ($i==$KONSTANTS['TimeSpecDatetime'])
			echo(' selected ');
		echo('>'.$TAGS['tpTimeSpec'.$i][0].'</option>');
	}
	echo('</select></td><td title="'.$TAGS['tpStartLit'][1].'">');
	echo('<input class="date" type="date" name="PenaltyStartDate[]" value="" onchange="enableSaveButton();"> ');
	echo('<input class="time" type="time" name="PenaltyStartTime[]" value="" onchange="enableSaveButton();"></td>');
	echo('<td title="'.$TAGS['tpFinishLit'][1].'"><input class="date" type="date" name="PenaltyFinishDate[]" value="" onchange="enableSaveButton();"> ');
	echo('<input class="time" type="time" name="PenaltyFinishTime[]" value="" onchange="enableSaveButton();"></td>');
	echo('<td><select name="PenaltyMethod[]" onchange="enableSaveButton();">');
	for ($i=0;$i<=3;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==0)
			echo(' selected');
		echo(">");
		echo($TAGS['tpMethod'.$i][1].'</option>');
	}
	echo('</select></td>');
	echo('<td><input type="number" name="PenaltyFactor[]" value="0" onchange="enableSaveButton();"></td>');
	echo('<td class="center"><button value="-" onclick="deleteRow(event);return false;">-</button></td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<button value="+" onclick="triggerNewRow(this);return false;">+</button><br>');
	
	echo('<input type="submit" class="noprint" title="'.$TAGS['SaveSettings'][1].'" id="savedata" data-triggered="0" onclick="'."this.setAttribute('data-triggered','1')".'" disabled accesskey="S" name="savedata" data-altvalue="'.$TAGS['SaveSettings'][0].'" value="'.$TAGS['SettingsSaved'][0].'" /> ');
	echo('</form>');
	//showFooter();
	
}







startHtml($TAGS['ttSetup'][0]);

if (isset($_REQUEST['c']))
{
	switch($_REQUEST['c'])
	{
		case 'rallyparams':
			if (isset($_REQUEST['savedata']))
				saveRallyConfig();
			showRallyConfig();
			break;
			
		case 'showcat':
			if (isset($_REQUEST['savedata']))
				saveCategories();
			// Terminology change from cat to axis
			if (isset($_REQUEST['axis']))
				$axis = $_REQUEST['axis'];
			else
				$axis = $_REQUEST['cat'];
			showCategories($axis,isset($_REQUEST['ord']) ? $_REQUEST['ord'] : '');
			break;
			
		case 'bonuses':
			if (isset($_REQUEST['savedata']))
				saveBonuses();
			showBonuses();
			break;

		case 'special':
			//print_r($_REQUEST);
			if (isset($_REQUEST['delete']) && isset($_REQUEST['BonusID']))
			{
				deleteSpecial($_REQUEST['BonusID']);
				showSpecials();
				break;
			}
			if (isset($_REQUEST['savedata']))
				saveSpecial();
			if (isset($_REQUEST['bonus']))
			{
				showSpecial($_REQUEST['bonus']);
				break;
			}
			
		case 'specials':
//			if (isset($_REQUEST['savedata']))
//				saveSpecials();
			showSpecials();
			break;

		case 'combo':
			if (isset($_REQUEST['comboid']))
			{
				if (isset($_REQUEST['savedata']))
				{
					saveSingleCombo();
					if (!retraceBreadcrumb())
						showCombinations();
					exit;
				}
				showSingleCombo($_REQUEST['comboid']);
				break;
			}
		case 'combos':
			if (isset($_REQUEST['savedata']))
				saveCombinations();
			showCombinations();
			break;
		case 'savecalc':
			saveCompoundCalc();
		case 'catcalcs':
			if (isset($_REQUEST['savedata']))
			{
				saveCompoundCalcs();
				if (retraceBreadcrumb())
					exit;
			}
			showCompoundCalcs();
			break;
		case 'newcc':
			showNewCompoundCalc();
			break;
		case 'showcc':
			showCompoundCalc(isset($_REQUEST['ruleid'])?$_REQUEST['ruleid']:0);
			break;
			
		case 'timep':
			if (isset($_REQUEST['savedata']))
				saveTimePenalties();
			showTimePenalties();
			break;
		case 'sgroups':
			if (isset($_REQUEST['savedata']))
				saveSGroups();
			showSGroups();
			break;

		default:
			showSpecial($_REQUEST['c']);
			//echo("<p>I don't know what to do with '".$_REQUEST['c']."'!");
	}
} else
	include "score.php"; // Some mistake has happened or maybe someone just tried logging on
//	print_r($_REQUEST);

?>

