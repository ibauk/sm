<?php

/*
 * I B A U K   -   S C O R E M A S T E R
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

 
 if (!isset($_REQUEST['c']))
{
	echo('404');
	exit;
}

 
 
 
 
$HOME_URL = 'admin.php';

require_once('common.php');

// Alphabetic from here on in


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

	//var_dump($_REQUEST);
	$arr = $_REQUEST['BonusID'];
	$DB->query("BEGIN TRANSACTION");
	for ($i=0; $i < count($arr); $i++)
	{
		$sql = "INSERT OR REPLACE INTO bonuses (BonusID,BriefDesc,Points";
		if (isset($_REQUEST['Cat1Entry']))
			$sql .= ",Cat1";
		if (isset($_REQUEST['Cat2Entry']))
			$sql .= ",Cat2";
		if (isset($_REQUEST['Cat3Entry']))
			$sql .= ",Cat3";
		$sql .= ",Compulsory) VALUES(";
		$sql .= "'".$DB->escapeString($_REQUEST['BonusID'][$i])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'][$i])."'";
		$sql .= ",".intval($_REQUEST['Points'][$i]);
		if (isset($_REQUEST['Cat1Entry']))
			$sql .= ",".intval(isset($_REQUEST['Cat1Entry'][$i]) ? $_REQUEST['Cat1Entry'][$i] : 0);
		if (isset($_REQUEST['Cat2Entry']))
			$sql .= ",".intval(isset($_REQUEST['Cat2Entry'][$i]) ? $_REQUEST['Cat2Entry'][$i] : 0);
		if (isset($_REQUEST['Cat3Entry']))
			$sql .= ",".intval(isset($_REQUEST['Cat3Entry'][$i]) ? $_REQUEST['Cat3Entry'][$i] : 0);
		$sql .= ",0)";
		if ($_REQUEST['BonusID'][$i]<>'')
		{
			//echo($sql.'<br>');			
			$DB->exec($sql);
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
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}
	
}	

function saveCombinations()
{
	global $DB, $TAGS, $KONSTANTS;

	//var_dump($_REQUEST); echo('<br>');
	$arr = $_REQUEST['ComboID'];
	$DB->query('BEGIN TRANSACTION');
	for ($i=0; $i < count($arr); $i++)
	{
		// Let's make sure the bonus list is good
		$bl = str_replace(' ',',',$_REQUEST['Bonuses'][$i]); // we want commas as separators not spaces
		$bls = explode(',',$bl);
		// On second thoughts, let's not bothering validating them here.
		$sql = "INSERT OR REPLACE INTO combinations (ComboID,BriefDesc,ScoreMethod,ScorePoints,Bonuses) VALUES(";
		$sql .= "'".$DB->escapeString($_REQUEST['ComboID'][$i])."'";
		$sql .= ",'".$DB->escapeString($_REQUEST['BriefDesc'][$i])."'";
		$sql .= ','.intval($_REQUEST['ScoreMethod'][$i]);
		$sql .= ','.intval($_REQUEST['ScorePoints'][$i]);
		$sql .= ",'".$DB->escapeString($bl)."'";
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
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}

	
}





function saveCompoundCalcs()
{
	global $DB, $TAGS, $KONSTANTS;

	//var_dump($_REQUEST);
	
	if (isset($_REQUEST['newcc']))
	{
		$sql = "INSERT INTO catcompound (Axis,Cat,ModBonus,NMin,PointsMults,NPower) VALUES(";
		$sql .= intval($_REQUEST['axis']);
		$sql .= intval($_REQUEST['Cat']);
		$sql .= ",".intval($_REQUEST['NMethod']);
		$sql .= ",".intval($_REQUEST['ModBonus']);
		$sql .= ",".intval($_REQUEST['NMin']);
		$sql .= ",".intval($_REQUEST['PointsMults']);
		$sql .= ",".intval($_REQUEST['NPower']);
		$sql .= ")";
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
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}
	
}




function saveRallyConfig()
{
	global $DB;

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
	$sql .= ",Cat1Label='".$DB->escapeString($_REQUEST['Cat1Label'])."'";
	$sql .= ",Cat2Label='".$DB->escapeString($_REQUEST['Cat2Label'])."'";
	$sql .= ",Cat3Label='".$DB->escapeString($_REQUEST['Cat3Label'])."'";
	$sql .= ",RejectReasons='".$DB->escapeString($RejectReasons)."'";
	//echo($sql.'<hr>');
	$DB->exec($sql);
	//echo("Rally configuration saved ".$DB->lastErrorCode().' ['.$DB->lastErrorMsg().']<hr>');
	//show_regular_admin_screen();
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
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
	if (isset($_REQUEST['menu'])) 
	{
		$_REQUEST['c'] = $_REQUEST['menu'];
		include("admin.php");
		exit;
	}

	
}

function saveTimePenalties()
{
	global $DB, $TAGS, $KONSTANTS;

	//var_dump($_REQUEST);
	
	$arr = $_REQUEST['id'];
	$DB->query('BEGIN TRANSACTION');
	for ($i=0; $i < count($arr); $i++)
	{
		if ($arr[$i]=='')
		{
			$sql = "INSERT INTO timepenalties (PenaltyStart,PenaltyFinish,PenaltyMethod,PenaltyFactor) VALUES (";
			$sql .= "'".$_REQUEST['PenaltyStartDate'][$i].'T'.$_REQUEST['PenaltyStartTime'][$i]."'";
			$sql .= ",'".$_REQUEST['PenaltyFinishDate'][$i].'T'.$_REQUEST['PenaltyFinishTime'][$i]."'";
			$sql .= ",".$_REQUEST['PenaltyMethod'][$i];
			$sql .= ",".$_REQUEST['PenaltyFactor'][$i];
			$sql .= ")";
		}
		else
		{
			$sql = "UPDATE timepenalties SET ";
			$sql .= "PenaltyStart='".$_REQUEST['PenaltyStartDate'][$i].'T'.$_REQUEST['PenaltyStartTime'][$i]."'";
			$sql .= ",PenaltyFinish='".$_REQUEST['PenaltyFinishDate'][$i].'T'.$_REQUEST['PenaltyFinishTime'][$i]."'";
			$sql .= ",PenaltyMethod=".$_REQUEST['PenaltyMethod'][$i];
			$sql .= ",PenaltyFactor=".$_REQUEST['PenaltyFactor'][$i];
			$sql .= " WHERE rowid=".$_REQUEST['id'][$i];
		}
		if ($_REQUEST['PenaltyStartDate'][$i] <> '' && $_REQUEST['PenaltyStartTime'][$i] <> '')
		{
			$DB->exec($sql);
			if ($DB->lastErrorCode() <> 0)
				echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		}
	}
	if (isset($_REQUEST['DeleteEntry']))
	{
		$arr = $_REQUEST['DeleteEntry'];
		for ($i=0; $i < count($arr); $i++)
		{
			$sql = "DELETE FROM timepenalties WHERE rowid=".$_REQUEST['DeleteEntry'][$i];
			$DB->exec($sql);
			if ($DB->lastErrorCode() <> 0)
				echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		}
	}
	$DB->query('COMMIT TRANSACTION');
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

	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
	$cat1label = $rd['Cat1Label'];
	$cat2label = $rd['Cat2Label'];
	$cat3label = $rd['Cat3Label'];


	echo('<form method="post" action="sm.php">');

	$R = $DB->query('SELECT * FROM categories ORDER BY Axis,BriefDesc');

	$lc = 0;
	$cats = []; $cats1 = []; $cats2 = []; $cats3 = [];
	while ($rd = $R->fetchArray())
	{
		switch($rd['Axis'])
		{
			case 1: $cats1[$rd['Cat']] = $rd['BriefDesc']; break;
			case 2: $cats2[$rd['Cat']] = $rd['BriefDesc']; break;
			case 3: $cats3[$rd['Cat']] = $rd['BriefDesc']; break;
		}
	}
	//print_r($cats1);
	
	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0)>0);
	
	echo('<input type="hidden" name="c" value="bonuses">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo("\r\n");
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	echo('<table id="bonuses">');
	echo('<caption title="'.htmlentities($TAGS['BonusMaintHead'][1]).'">'.htmlentities($TAGS['BonusMaintHead'][0]).'</caption>');
	echo('<thead><tr><th style="text-align:left;">'.$TAGS['BonusIDLit'][0].'</th>');
	//echo('<thead><tr><th>B</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	echo('<th>'.$TAGS['BonusPoints'][0].'</th>');
	if (count($cats1) > 0)
	{
		$cats1[0] = '';
		echo('<th>'.$cat1label.'</th>');
	}
	if (count($cats2) > 0)
	{
		$cats2[0] = '';
		echo('<th>'.$cat2label.'</th>');
	}
	if (count($cats3) > 0)
	{
		$cats3[0] = '';
		echo('<th>'.$cat3label.'</th>');
	}
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
		if (count($cats1) > 0)
		{
			echo('<td><select name=Cat1Entry[]>');
			foreach ($cats1 as $ce => $bd)
			{
				echo('<option value="'.$ce.'" ');
				if ($ce == $rd['Cat1'])
					echo('selected="selected" ');
				echo('>'.htmlspecialchars($bd).'</option>');
			}
			echo('</select></td>');
		}
		if (count($cats2) > 0)
		{
			echo('<td><select name=Cat2Entry[]>');
			foreach ($cats2 as $ce => $bd)
			{
				echo('<option value="'.$ce.'" ');
				if ($ce == $rd['Cat2'])
					echo('selected="selected" ');
				echo('>'.htmlspecialchars($bd).'</option>');
			}
			echo('</select></td>');
		}
		if (count($cats3) > 0)
		{
			echo('<td><select name=Cat3Entry[]>');
			foreach ($cats3 as $ce => $bd)
			{
				echo('<option value="'.$ce.'" ');
				if ($ce == $rd['Cat3'])
					echo('selected="selected" ');
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
	if (count($cats1) > 0)
	{
		echo('<td><select name="Cat1Entry[]">');
		$S = ' selected="selected" ';
		foreach ($cats1 as $ce => $bd)
		{
			echo('<option value="'.$ce.'" '.$S);
			echo('>'.htmlspecialchars($bd).'</option>');
			$S = '';
		}
		echo('</select></td>');
	}
	if (count($cats2) > 0)
	{
		echo('<td><select name=Cat2Entry[]>');
		$S = ' selected="selected" ';
		foreach ($cats2 as $ce => $bd)
		{
			echo('<option value="'.$ce.'" '.$S);
			echo('>'.htmlspecialchars($bd).'</option>');
			$S = '';
		}
		echo('</select></td>');
	}
	if (count($cats3) > 0)
	{
		echo('<td><select name=Cat3Entry[]>');
		$S = ' selected="selected" ';
		foreach ($cats3 as $ce => $bd)
		{
			echo('<option value="'.$ce.'" '.$S);
			echo('>'.htmlspecialchars($bd).'</option>');
			$S = '';
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
	if (!preg_match('/1|2|3/i',$axis))
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
	echo('<p>'.$TAGS['CatExplainer'][1].'</p>');
	echo('<form method="post" action="sm.php">');
	echo('<input type="hidden" name="c" value="showcat">');
	echo('<input type="hidden" name="axis" value="'.$axis.'">');
	echo('<input type="hidden" name="ord" value="'.$ord.'">');
	echo('<input type="hidden" name="menu" value="setup">');
	
	echo('<table id="cats"><caption>'.$TAGS['AxisLit'][0].' '.$axis.'  <input type="text" name="catlabel" value="'.htmlspecialchars($CatLabel).'"></caption>');
	echo('<thead><tr><th><a href="sm.php?c=showcat&amp;axis='.$axis.'&amp;ord=Entry">'.$TAGS['CatEntry'][0].'</a></th>');
	echo('<th><a href="sm.php?c=showcat&amp;axis='.$axis.'&amp;ord=BriefDesc">'.$TAGS['CatBriefDesc'][0].'</a></th><th>'.$TAGS['DeleteEntryLit'][0].'</th></tr>');
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
	
	
	switch($axis)
	{
		case '1': $CatA = 2; $CatB = 3; break;
		case '2': $CatA = 1; $CatB = 3; break;
		case '3': $CatA = 1; $CatB = 2; break;
	}
	$R = $DB->query('SELECT Cat'.$CatA.'Label AS CatALabel,Cat'.$CatB.'Label AS CatBLabel FROM rallyparams');
	if ($rd = $R->fetchArray())
	{
		echo('<hr>[ <a href="sm.php?c=showcat&amp;ord=Entry&amp;axis='.$CatA.'">'.$CatA.'-'.$rd['CatALabel'].'</a> ] ');
		echo(' [ <a href="sm.php?c=showcat&amp;ord=Entry&amp;axis='.$CatB.'">'.$CatB.'-'.$rd['CatBLabel'].'</a> ]');
	}
	
	echo('</form>');
	//showFooter();
}


function showCombinations()
{
	global $DB, $TAGS, $KONSTANTS;
	

	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0)>0);
	
	$R = $DB->query('SELECT * FROM combinations ORDER BY ComboID');
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

	
	echo('<input type="hidden" name="c" value="combos">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<table id="bonuses">');
	echo('<caption title="'.htmlentities($TAGS['ComboMaintHead'][1]).'">'.htmlentities($TAGS['ComboMaintHead'][0]).'</caption>');
	echo('<thead><tr><th>'.$TAGS['ComboIDLit'][0].'</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	echo('<th>'.$TAGS['ScoreMethodLit'][0].'</th>');
	echo('<th>'.$TAGS['PointsMults'][0].'</th>');
	echo('<th>'.$TAGS['BonusListLit'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
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
		echo('<tr class="hoverlite"><td><input class="ComboID" type="text" name="ComboID[]" readonly value="'.$rd['ComboID'].'"></td>');
		echo('<td><input class="BriefDesc" type="text" name="BriefDesc[]" value="'.$rd['BriefDesc'].'"></td>');
		echo('<td><select name="ScoreMethod[]">');
		echo('<option value="0" '.($rd['ScoreMethod']<>1 ? 'selected="selected" ' : '').'>'.$TAGS['AddPoints'][0].'</option>');
		echo('<option value="1" '.($rd['ScoreMethod']==1 ? 'selected="selected" ' : '').'>'.$TAGS['AddMults'][0].'</option>');
		echo('</select></td>');
		echo('<td><input class="ScorePoints" type="number" name="ScorePoints[]" value="'.$rd['ScorePoints'].'"></td>');
		echo('<td><input title="'.$TAGS['BonusListLit'][1].'" class="Bonuses" type="text" name="Bonuses[]" value="'.$rd['Bonuses'].'" ></td>');
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['ComboID'].'">');
		if ($showclaimsbutton)
		{
			$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || CombosTicked || ',' LIKE '%,".$rd['ComboID'].",%'","rex",0);
			echo('<td class="ClaimsCount" title="'.$TAGS['ShowClaimsButton'][1].'">');
			if ($rex > 0)
				echo('<a href='."'entrants.php?c=entrants&mode=combo&bonus=".$rd['ComboID']."'".'> '.$rex.' </a>');
			echo('</td>');
		}
		echo('</tr>');
	}
	echo('<tr class="newrow"><td><input type="text" name="ComboID[]" onchange="triggerNewRow(this)"></td>');
	echo('<td><input type="text" name="BriefDesc[]"></td>');
	echo('<td><select name="ScoreMethod[]">');
	echo('<option value="0" selected="selected" >'.$TAGS['AddPoints'][0].'</option>');
	echo('<option value="1" >'.$TAGS['AddMults'][0].'</option>');
	echo('</select></td>');
	echo('<td><input class="ScorePoints" type="number" name="ScorePoints[]" ></td>');
	echo('<td><input title="'.$TAGS['BonusListLit'][1].'" class="Bonuses" type="text" name="Bonuses[]" placeholder="'.$TAGS['CommaSeparated'][0].'"></td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<input type="submit" name="savedata" value="'.$TAGS['UpdateBonuses'][0].'"> ');
	echo('</form>');
	//showFooter();
	
}



function showCompoundCalcs()
{
	global $DB, $TAGS, $KONSTANTS;
	

	
	$R = $DB->query('SELECT Cat1Label,Cat2Label,Cat3Label FROM rallyparams');
	$AxisLabels = $R->fetchArray();
	for ($i=1;$i<=3;$i++)
		if ($AxisLabels['Cat'.$i.'Label']=='')
			$AxisLabels['Cat'.$i.'Label']="$i (not used)";
		else
			$AxisLabels['Cat'.$i.'Label']="$i ".$AxisLabels['Cat'.$i.'Label'];
	$R = $DB->query('SELECT rowid as id,Cat,Axis,NMethod,ModBonus,NMin,PointsMults,NPower FROM catcompound ORDER BY Axis,Cat,NMin DESC');
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

	echo('<form method="post" action="sm.php">');
	echo('<input type="hidden" name="c" value="catcalcs">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<table id="catcalcs">');
	echo('<caption title="'.htmlentities($TAGS['CalcMaintHead'][1]).'">'.htmlentities($TAGS['CalcMaintHead'][0]).'</caption>');
	echo('<thead><tr><th>'.$TAGS['AxisLit'][0].'</th>');
	echo('<th>'.$TAGS['CatEntry'][0].'</th>');
	echo('<th>'.$TAGS['ModBonusLit'][0].'</th>');
	echo('<th>'.$TAGS['NMethodLit'][0].'</th>');
	echo('<th>'.$TAGS['NMinLit'][0].'</th>');
	echo('<th>'.$TAGS['PointsMults'][0].'</th>');
	echo('<th>'.$TAGS['NPowerLit'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="hoverlite">');

		echo('<td title="'.$TAGS['AxisLit'][1].'"><input type="hidden" name="id[]" value="'.$rd['id'].'"><select onchange="enableSaveButton();" name="axis[]">');
		for ($i=1;$i<=3;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['Axis'])
				echo(' selected');
			echo('>'.$AxisLabels['Cat'.$i.'Label'].'</option>');
		}
		echo('</select></td>');
		echo('<td title="'.$TAGS['CatEntry'][1].'">');
		echo('<input type="number"  onchange="enableSaveButton();" name="Cat[]" value="');
		echo($rd['Cat']);
		echo('">');
		echo('</td>');
		echo('</select></td>');
		echo('<td title="'.$TAGS['ModBonusLit'][1].'"><select onchange="enableSaveButton();" name="ModBonus[]">');
		for ($i=0;$i<=1;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['ModBonus'])
				echo(' selected');
			echo('>'.$TAGS['ModBonus'.$i][1].'</option>');
		}
		
		echo('</select></td>');
		echo('<td title="'.$TAGS['NMethodLit'][1].'"><select onchange="enableSaveButton();" name="NMethod[]">');
		for ($i=-1;$i<=1;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['NMethod'])
				echo(' selected');
			echo('>'.$TAGS['NMethod'.$i][1].'</option>');
		}
		echo('<td title="'.$TAGS['NMinLit'][1].'"><input onchange="enableSaveButton();" type="number" name="NMin[]" value="'.$rd['NMin'].'"></td>');
		echo('<td title="'.$TAGS['PointsMults'][1].'"><select onchange="enableSaveButton();" name="PointsMults[]">');
		for ($i=0;$i<=1;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['PointsMults'])
				echo(' selected');
			echo('>'.$TAGS['PointsMults'.$i][1].'</option>');
		}
		echo('</select>');
		echo('<td title="'.$TAGS['NPowerLit'][1].'"><input onchange="enableSaveButton();" type="number" name="NPower[]"  value="'.$rd['NPower'].'"></td>');
		echo('<td><input onchange="enableSaveButton();" type="checkbox" name="DeleteEntry[]" value="'.$rd['id'].'">');
		echo('</tr>');
	}
	
	
	
	echo('</tbody></table>');
	
	echo('<input type="submit" id="savedata" name="savedata" disabled value="'.$TAGS['UpdateCCs'][0].'"> ');
	
	echo('</form>');
	
	echo('<form method="get" action="sm.php">');
	echo('<input type="hidden" name="c" value="newcc">');
	echo('<input type="submit" value="'.$TAGS['InsertNewCC'][0].'">');
	echo('</form>');
	//showFooter();
}


function showNewCompoundCalc()
{

	global $DB, $TAGS, $KONSTANTS;
	


	$R = $DB->query('SELECT Cat1Label,Cat2Label,Cat3Label FROM rallyparams');
	
	$AxisLabels = $R->fetchArray();
	
	for ($i=1;$i<=3;$i++)
		if ($AxisLabels['Cat'.$i.'Label']=='')
			$AxisLabels['Cat'.$i.'Label']="$i (not used)";
		else
			$AxisLabels['Cat'.$i.'Label']="$i ".$AxisLabels['Cat'.$i.'Label'];

	echo('<form method="post" action="sm.php">');
	echo('<input type="hidden" name="c" value="catcalcs">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<input type="hidden" name="newcc" value="1">');
	echo('<span class="vlabel" title="'.$TAGS['AxisLit'][1].'">');
	echo('<label for="axis">'.$TAGS['AxisLit'][0].'</label> ');
	echo('<select id="axis" name="axis">');
	for ($i=1;$i<=3;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==1)
			echo(' selected');
		echo('>'.$AxisLabels['Cat'.$i.'Label'].'</option>');
	}
	echo('</select> ');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['CatEntry'][1].'">');
	echo('<label for="Cat">'.$TAGS['CatEntry'][0].'</label> ');
	echo('<input type="number" name="Cat" id="Cat" value="0">');
	echo('</span>');
	echo('<span class="vlabel" title="'.$TAGS['ModBonusLit'][1].'">');
	echo('<label for="ModBonus">'.$TAGS['ModBonusLit'][0].'</label> ');
	echo('<select name="ModBonus">');
	for ($i=0;$i<=1;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==0)
			echo(' selected');
		echo('>'.$TAGS['ModBonus'.$i][1].'</option>');
	}
	echo('</select>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NMethodLit'][1].'">');
	echo('<label for="NMethod">'.$TAGS['NMethodLit'][0].'</label> ');
	echo('<select name="NMethod">');
	echo('<option value="0" selected>'.$TAGS['NMethod0'][1].'</option>');
	echo('<option value="1">'.$TAGS['NMethod1'][1].'</option>');
	echo('<option value="-1">'.$TAGS['NMethod-1'][1].'</option>');
	echo('</select> ');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NMinLit'][1].'">');
	echo('<label for="NMin">'.$TAGS['NMinLit'][0].'</label> ');
	echo('<input type="number" name="NMin" value="1">');
	echo('</span>');
	
	echo('<span class="vlabel" title"'.$TAGS['PointsMults'][1].'">');
	echo('<label for="PointsMults">'.$TAGS['PointsMults'][0].'</label> ');
	echo('<select name="PointsMults">');
	for ($i=0;$i<=1;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==0)
			echo(' selected');
		echo('>'.$TAGS['PointsMults'.$i][1].'</option>');
	}
	echo('</select>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['NPowerLit'][1].'">');
	echo('<label for="NPower">'.$TAGS['NPowerLit'][0].'</label> ');
	echo('<input type="number" name="NPower"  value="0">');
	echo('</span>');

	echo('<br><br><input type="submit" name="savedata" value="'.$TAGS['SaveNewCC'][0].'"> ');

	echo('</form>');
	//showFooter();
}






function showRallyConfig()
{
	global $DB, $TAGS, $KONSTANTS;
	

	
	$R = $DB->query('SELECT * FROM rallyparams');
	if (!$rd = $R->fetchArray())
		$rd = [];
	echo('<br><form method="post" action="sm.php">');
	echo('<input type="hidden" name="c" value="rallyparams">');
	echo('<input type="hidden" name="menu" value="setup">');
	
	echo('<div class="tabs_area" style="display:inherit"><ul id="tabs">');
	echo('<li><a href="#tab_basic">'.$TAGS['BasicRallyConfig'][0].'</a></li>');
	echo('<li><a href="#tab_scoring">'.$TAGS['ScoringMethod'][0].'</a></li>');
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
	
	echo('<span class="vlabel">');
	echo('<label for="CertificateHours" class="vlabel">'.$TAGS['CertificateHours'][0].' </label> ');
	echo('<input type="number" name="CertificateHours" id="CertificateHours" value="'.$rd['CertificateHours'].'" title="'.$TAGS['CertificateHours'][1].'"> ');
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
	echo('<label for="ScoringMethodM" title="'.$TAGS['ScoringMethodM'][1].'">'.$TAGS['ScoringMethodM'][0].' </label> ');
		$chk = ($rd['ScoringMethod']==$KONSTANTS['ManualScoring']) ? ' checked="checked" ' : '';
	echo('<input type="radio"'.$chk.' name="ScoringMethod" id="ScoringMethodM" value="'.$KONSTANTS['ManualScoring'].'" title="'.$TAGS['ScoringMethodM'][1].'"> ');
	echo('<label for="ScoringMethodS" title="'.$TAGS['ScoringMethodS'][1].'">'.$TAGS['ScoringMethodS'][0].' </label> ');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['SimpleScoring']) ? ' checked="checked" ' : '';
	echo('<input type="radio"'.$chk.' name="ScoringMethod" id="ScoringMethodS" value="'.$KONSTANTS['SimpleScoring'].'" title="'.$TAGS['ScoringMethodS'][1].'"> ');
	echo('<label for="ScoringMethodC" title="'.$TAGS['ScoringMethodC'][1].'">'.$TAGS['ScoringMethodC'][0].' </label> ');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['CompoundScoring']) ? ' checked="checked" ' : '';
	echo('<input type="radio" '.$chk.'name="ScoringMethod" id="ScoringMethodC" value="'.$KONSTANTS['CompoundScoring'].'" title="'.$TAGS['ScoringMethodC'][1].'"> ');
	echo('<label for="ScoringMethodA" title="'.$TAGS['ScoringMethodA'][1].'">'.$TAGS['ScoringMethodA'][0].' </label> ');
	$chk = ($rd['ScoringMethod']==$KONSTANTS['AutoScoring']) ? ' checked="checked" ' : '';
	echo('<input type="radio" '.$chk.'name="ScoringMethod" id="ScoringMethodA" value="'.$KONSTANTS['AutoScoring'].'" title="'.$TAGS['ScoringMethodA'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['ShowMultipliers'][0].': </span> ');
	echo('<label for="ShowMultipliersN" title="'.$TAGS['ShowMultipliersN'][1].'">'.$TAGS['ShowMultipliersN'][0].' </label> ');
	$chk = ($rd['ShowMultipliers']==$KONSTANTS['SuppressMults']) ? ' checked="checked" ' : '';
	echo('<input type="radio"'.$chk.' name="ShowMultipliers" id="ShowMultipliersN" value="'.$KONSTANTS['SuppressMults'].'" title="'.$TAGS['ShowMultipliersN'][1].'"> ');
	
	echo('<label for="ShowMultipliersY" title="'.$TAGS['ShowMultipliersY'][1].'">'.$TAGS['ShowMultipliersY'][0].' </label> ');
	$chk = ($rd['ShowMultipliers']==$KONSTANTS['ShowMults']) ? ' checked="checked" ' : '';
	echo('<input type="radio"'.$chk.' name="ShowMultipliers" id="ShowMultipliersY" value="'.$KONSTANTS['ShowMults'].'" title="'.$TAGS['ShowMultipliersY'][1].'"> ');
	
	echo('<label for="ShowMultipliersA" title="'.$TAGS['ShowMultipliersA'][1].'">'.$TAGS['ShowMultipliersA'][0].' </label> ');
	$chk = ($rd['ShowMultipliers']==$KONSTANTS['AutoShowMults']) ? ' checked="checked" ' : '';
	echo('<input type="radio"'.$chk.' name="ShowMultipliers" id="ShowMultipliersA" value="'.$KONSTANTS['AutoShowMults'].'" title="'.$TAGS['ShowMultipliersA'][1].'"> ');
	echo('</span>');


	echo('<span class="vlabel">');
	echo('<label for="TiedPointsRanking" title="'.$TAGS['TiedPointsRanking'][1].'">'.$TAGS['TiedPointsRanking'][0].' </label> ');
	$chk = ($rd['TiedPointsRanking']==$KONSTANTS['TiedPointsSplit']) ? ' checked="checked" ' : '';
	echo(' &nbsp;&nbsp;<input type="checkbox"'.$chk.' name="TiedPointsRanking" id="TiedPointsRanking" value="'.$KONSTANTS['TiedPointsSplit'].'">');
	echo('</span>');


	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['TeamRankingText'][0].': </span>');
	echo('<label for="TeamRankingI" class="inline wide" title="'.$TAGS['TeamRankingI'][1].'">'.$TAGS['TeamRankingI'][0].'</label> ');
	$chk = ($rd['TeamRanking']==$KONSTANTS['RankTeamsAsIndividuals']) ? ' checked="checked" ' : '';
	echo('<input type="radio" '.$chk.'name="TeamRanking" id="TeamRankingI" value="'.$KONSTANTS['RankTeamsAsIndividuals'].'" title="'.$TAGS['TeamRankingI'][1].'"> ');
	echo('<label for="TeamRankingH" class="inline wide" title="'.$TAGS['TeamRankingH'][1].'">'.$TAGS['TeamRankingH'][0].'</label> ');
	$chk = ($rd['TeamRanking']==$KONSTANTS['RankTeamsHighest']) ? ' checked="checked" ' : '';
	echo('<input type="radio" '.$chk.'name="TeamRanking" id="TeamRankingH" value="'.$KONSTANTS['RankTeamsHighest'].'" title="'.$TAGS['TeamRankingH'][1].'"> ');
	echo('<label for="TeamRankingL" class="inline wide" title="'.$TAGS['TeamRankingL'][1].'">'.$TAGS['TeamRankingL'][0].'</label> ');
	$chk = ($rd['TeamRanking']==$KONSTANTS['RankTeamsLowest']) ? ' checked="checked" ' : '';
	echo('<input type="radio" '.$chk.'name="TeamRanking" id="TeamRankingL" value="'.$KONSTANTS['RankTeamsLowest'].'" title="'.$TAGS['TeamRankingL'][1].'"> ');
	echo('</span>');

	
	echo('<span class="vlabel">');
	echo('<label for="Cat1Label"  class="vlabel" title="'.$TAGS['Cat1Label'][1].'">'.$TAGS['Cat1Label'][0].' </label> ');
	echo('<input type="text" name="Cat1Label" id="Cat1Label" value="'.htmlspecialchars($rd['Cat1Label']).'" title="'.$TAGS['Cat1Label'][1].'" placeholder="'.$TAGS['unset'][0].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="Cat2Label"  class="vlabel" title="'.$TAGS['Cat2Label'][1].'">'.$TAGS['Cat2Label'][0].' </label> ');
	echo('<input type="text" name="Cat2Label" id="Cat2Label" value="'.htmlspecialchars($rd['Cat2Label']).'" title="'.$TAGS['Cat2Label'][1].'" placeholder="'.$TAGS['unset'][0].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<label for="Cat3Label" class="vlabel" title="'.$TAGS['Cat3Label'][1].'">'.$TAGS['Cat3Label'][0].' </label> ');
	echo('<input type="text" name="Cat3Label" placeholder="'.$TAGS['unset'][0].'" id="Cat3Label" value="'.htmlspecialchars($rd['Cat3Label']).'" title="'.$TAGS['Cat3Label'][1].'" > ');
	echo('</span>');
	

	echo('</fieldset>');

	
	echo('<fieldset id="tab_penalties" class="tabContent"><legend>'.$TAGS['ExcessMileage'][0].'</legend>');

	echo('<span class="vlabel">');
	echo('<label for="PenaltyMaxMiles" class="vlabel wide">'.$TAGS['PenaltyMaxMiles'][0].' </label> ');
	echo('<input type="number" name="PenaltyMaxMiles" id="PenaltyMaxMiles" value="'.$rd['PenaltyMaxMiles'].'" title="'.$TAGS['PenaltyMaxMiles'][1].'"> ');
	echo('</span>');

	echo('<span class="vlabel">');
	echo('<span>'.$TAGS['MilesPenaltyText'][0].': </span> ');
	echo('<label for="MaxMilesFixedP"  class="vlabel" title="'.$TAGS['MaxMilesFixedP'][1].'">'.$TAGS['MaxMilesFixedP'][0].' </label> ');
	$chk = ($rd['MaxMilesMethod']==$KONSTANTS['MaxMilesFixedP']) ? ' checked="checked" ' : '';
	echo(' <input type="radio"'.$chk.' name="MaxMilesMethod" id="MaxMilesFixedP" value="'.$KONSTANTS['MaxMilesFixedP'].'" title="'.$TAGS['MaxMilesFixedP'][1].'"> ');
	echo('<label for="MaxMilesFixedM" title="'.$TAGS['MaxMilesFixedM'][1].'">'.$TAGS['MaxMilesFixedM'][0].' </label> ');
	$chk = ($rd['MaxMilesMethod']==$KONSTANTS['MaxMilesFixedM']) ? ' checked="checked" ' : '';
	echo(' <input type="radio"'.$chk.' name="MaxMilesMethod" id="MaxMilesFixedM" value="'.$KONSTANTS['MaxMilesFixedM'].'" title="'.$TAGS['MaxMilesFixedM'][1].'"> ');
	echo('<label for="MaxMilesPerMile" title="'.$TAGS['MaxMilesPerMile'][1].'">'.$TAGS['MaxMilesPerMile'][0].' </label> ');
	$chk = ($rd['MaxMilesMethod']==$KONSTANTS['MaxMilesPerMile']) ? ' checked="checked" ' : '';
	echo(' <input type="radio"'.$chk.' name="MaxMilesMethod" id="MaxMilesPerMile" value="'.$KONSTANTS['MaxMilesPerMile'].'" title="'.$TAGS['MaxMilesPerMile'][1].'"> ');
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






function showSpecials()
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
	global $DB, $TAGS, $KONSTANTS;
	

	
	$R = $DB->query('SELECT rowid AS id,PenaltyStart,PenaltyFinish,PenaltyMethod,PenaltyFactor FROM timepenalties ORDER BY PenaltyStart');
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');

?>
<script>
function triggerNewRow(obj)
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('timepenalties').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
</script>
<?php	


	echo('<form method="post" action="sm.php">');

	
	echo('<input type="hidden" name="c" value="timep">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<table id="timepenalties">');
	echo('<caption title="'.htmlentities($TAGS['TimepMaintHead'][1]).'">'.htmlentities($TAGS['TimepMaintHead'][0]).'</caption>');
	echo('<thead><tr><th>'.$TAGS['tpStartLit'][0].'</th>');
	echo('<th>'.$TAGS['tpFinishLit'][0].'</th>');
	echo('<th>'.$TAGS['tpMethodLit'][0].'</th>');
	echo('<th>'.$TAGS['tpFactorLit'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	
	while ($rd = $R->fetchArray())
	{
		echo('<tr class="hoverlite">');
		echo('<td><input type="hidden" name="id[]" value="'.$rd['id'].'">');
		$dtx = splitDatetime($rd['PenaltyStart']);
		echo('<input type="date" name="PenaltyStartDate[]" value="'.$dtx[0].'"> ');
		echo('<input type="time" name="PenaltyStartTime[]" value="'.$dtx[1].'"></td>');
		$dtx = splitDatetime($rd['PenaltyFinish']);		
		echo('<td><input type="date" name="PenaltyFinishDate[]" value="'.$dtx[0].'"> ');
		echo('<input type="time" name="PenaltyFinishTime[]" value="'.$dtx[1].'"></td>');
		echo('<td><select name="PenaltyMethod[]">');
		for ($i=0;$i<=3;$i++)
		{
			echo("<option value=\"$i\"");
			if ($i==$rd['PenaltyMethod'])
				echo(' selected');
			echo(">");
			echo($TAGS['tpMethod'.$i][1].'</option>');
		}
		echo('</select></td>');
		echo('<td><input type="number" name="PenaltyFactor[]" value="'.$rd['PenaltyFactor'].'"></td>');
		echo('<td class="center"><input type="checkbox" name="DeleteEntry[]" value="'.$rd['id'].'"></td>');
		echo('</tr>');
	}
	echo('<tr class="newrow"><td><input type="hidden" name="id[]" value="">');
	echo('<input type="date" name="PenaltyStartDate[]" value=""> ');
	echo('<input type="time" name="PenaltyStartTime[]" value=""></td>');
	echo('<td><input type="date" name="PenaltyFinishDate[]" value=""> ');
	echo('<input type="time" name="PenaltyFinishTime[]" value=""></td>');
	echo('<td><select name="PenaltyMethod[]">');
	for ($i=0;$i<=3;$i++)
	{
		echo("<option value=\"$i\"");
		if ($i==0)
			echo(' selected');
		echo(">");
		echo($TAGS['tpMethod'.$i][1].'</option>');
	}
	echo('</select></td>');
	echo('<td><input type="number" name="PenaltyFactor[]" value="0"></td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<br><input type="submit" name="savedata" value="'.$TAGS['UpdateTimeP'][0].'"> ');
	echo('</form>');
	//showFooter();
	
}







startHtml();

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
			
		case 'specials':
			if (isset($_REQUEST['savedata']))
				saveSpecials();
			showSpecials();
			break;
			
		case 'combos':
			if (isset($_REQUEST['savedata']))
				saveCombinations();
			showCombinations();
			break;
		case 'catcalcs':
			if (isset($_REQUEST['savedata']))
				saveCompoundCalcs();
			showCompoundCalcs();
			break;
		case 'newcc':
			showNewCompoundCalc();
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
			echo("<p>I don't know what to do with '".$_REQUEST['c']."'!");
	}
} else
	include "score.php"; // Some mistake has happened or maybe someone just tried logging on
//	print_r($_REQUEST);

?>

