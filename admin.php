<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle the rally scoring administration
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


$HOME_URL = "admin.php";
require_once('common.php');

function fetchCertificate($EntrantID,$Class)
{
	global $DB, $TAGS, $KONSTANTS;
	if ($EntrantID == '')
		$EntrantID = 0;
	if ($Class == '')
		$Class = 0;
	$sql = "SELECT * FROM certificates WHERE EntrantID=";
	$R = $DB->query($sql.$EntrantID." AND Class=$Class");
	$rd = $R->fetchArray();
	return ['html'=>$rd['html'],'css'=>$rd['css'],'Title'=>$rd['Title']];
	
}

function saveCertificate()
{
	global $DB, $TAGS, $KONSTANTS;
	
	//var_dump($_REQUEST);
	$R = $DB->query("SELECT Count(*) As Rex FROM certificates WHERE EntrantID=".$_REQUEST['EntrantID']." AND Class=".$_REQUEST['Class']);
	$rd = $R->fetchArray();
	$adding = $rd['Rex'] < 1;
	
	if ($adding)
	{
		$sql = "INSERT INTO certificates(EntrantID,Class,html,css,Title) VALUES(";
		$sql .= $_REQUEST['EntrantID'];
		$sql .= ",";
		$sql .= $_REQUEST['Class'];
		$sql .= ",'";
		$sql .= $DB->escapeString($_REQUEST['certhtml'])."'";
		$sql .= ",'";
		$sql .= $DB->escapeString($_REQUEST['certcss'])."'";
		$sql .= ",'";
		$sql .= $DB->escapeString($_REQUEST['Title'])."'";
		$sql .= ')';
	}
	else
	{
		$sql = "UPDATE certificates SET html='".$DB->escapeString($_REQUEST['certhtml'])."'";
		$sql .= ",css='".$DB->escapeString($_REQUEST['certcss'])."'";
		$sql .= ",Title='".$DB->escapeString($_REQUEST['Title'])."'";
		$sql .= " WHERE EntrantID=".$_REQUEST['EntrantID']." AND Class=".$_REQUEST['Class'];
	}
	//echo($sql."<hr>");
	$DB->exec($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorCode().' == '.$DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	
}

function editCertificate()
{
	global $DB, $TAGS, $KONSTANTS;

	$EntrantID = (isset($_REQUEST['EntrantID']) ? intval($_REQUEST['EntrantID']) : 0);
	$class = 0;
	if (isset($_REQUEST['Class']))
		$class = $_REQUEST['Class'];
	$rd = fetchCertificate($EntrantID,$class);
	startHtml('');
	echo('<p>'.$TAGS['CertExplainer'][0].'<br>');
	echo($TAGS['CertExplainer'][1].'</p>');
	echo('<form id="certform" method="post" action="admin.php">');
	echo('<input type="hidden" name="c" value="editcert">');
	echo('<input type="hidden" name="EntrantID" value="'.$EntrantID.'">');
	echo('<label for="Class">'.$TAGS['Class'][0].' </label>');
	$x = ' onchange="document.getElementById('."'".'fetchcert'."'".').disabled=false;"';
	echo('<input title="'.$TAGS['Class'][1].'" type="number" name="Class" id="Class" value="'.$class.'" '.$x.'> ');
	
	echo('<input type="submit" disabled id="fetchcert" name="fetchcert" value="'.$TAGS['FetchCert'][0].'" title="'.$TAGS['FetchCert'][1].'"> ');
	echo('<label for="Title">'.$TAGS['CertTitle'][0].' </label>');
	echo('<input title="'.$TAGS['CertTitle'][1].'" type="text" name="Title" id="Title" value="'.$rd['Title'].'" > ');

	echo('<br>html<br>');
	echo("<textarea form='certform' name='certhtml' id='certhtml' contenteditable='true' style='height:20em; width:90%;'>");
	echo($rd['html']);
	echo('</textarea>');
	echo('<br><br>css<br>');
	echo("<textarea form='certform' name='certcss' id='certcss' contenteditable='true' style='height:10em; width:90%;'>");
	echo($rd['css']);
	echo('</textarea>');
	
	echo('<br><input type="submit" name="savecert" value="'.$TAGS['SaveCertificate'][0].'" title="'.$TAGS['SaveCertificate'][1].'">');
	echo('</form>');
	//showFooter();
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

if (isset($_REQUEST['c']) && $_REQUEST['c']=='rank')
{
	rankEntrants();
	include("entrants.php");
	listEntrants('EntrantStatus DESC,FinishPosition');
	exit;
}
if (isset($_REQUEST['savecert']))
	saveCertificate();

if (isset($_REQUEST['c']) && $_REQUEST['c']=='editcert')
{
	editCertificate();
	exit;
}


function showAdminMenu()
{
	global $DB;

	if ($R = $DB->query("SELECT DBState FROM rallyparams"))
	{
	$rd = $R->fetchArray();
	if ($rd['DBState'] == 0) // Database is in initial virgin state
	{
		include("setup.php");
		exit;
	}
	}
	show_menu('admin');
	
}


function showBonusMenu()
{
	global $TAGS;
	
	$MAINMENU = array(
		'AdmBonusTable'		=> array('sm.php?c=bonuses',NULL),
		'AdmSpecialTable'	=> array('sm.php?c=specials',NULL),
		'AdmSGroups'		=> array('sm.php?c=sgroups',NULL),
		'AdmCombosTable'	=> array('sm.php?c=combos',NULL)
	);
	showMenu($MAINMENU,$TAGS['AdmBonusHeader'][0]);
	
}

function showEntrantsMenu()
{
	global $TAGS;
	
	$MAINMENU = array(
		'AdmEntrantChecks'	=> array('entrants.php?c=entrants&amp;ord=EntrantID&amp;mode=check',NULL),
		'AdmEntrants'		=> array('entrants.php?c=entrants&amp;ord=EntrantID&amp;mode=full',NULL),
		'AdmNewEntrant'		=> array('entrants.php?c=newentrant',NULL),
		'AdmDoScoring'		=> array('score.php',NULL),
		'AdmDoBlank'		=> array('score.php?c=blank',NULL),
		'AdmRankEntries'	=> array('admin.php?c=rank',NULL),
		'AdmExportFinishers'=> array('exportxls.php?c=expfinishers',"this.firstChild.innerHTML='".$TAGS['FinishersExported'][0]."';"),
		'AdmImportEntrants'	=> array('importxls.php?showupload',NULL)
	);
	showMenu($MAINMENU,$TAGS['AdmEntrantsHeader'][0]);
	
}



function showMenu($menu,$title)
{
	global $TAGS;
	
	echo('<div id="adminMM">');
	echo('<h4>'.$title.'</h4>');
	echo('<ul class="menulist">');
	foreach($menu as $itm => $dst)
	{
		echo('<li title="'.$TAGS[$itm][1].'"');
		if ($dst[1])
			echo(' onclick="'.$dst[1].'"');
		echo('>');
		echo('<a href="'.$dst[0].'">'.$TAGS[$itm][0].'</a>');
		echo('</li>');
	}
	echo('</ul>');

	echo('</div>');
	showFooter();
	exit;
}

function show_menu($menuid)
{
	global $TAGS,$DB;
	
	$R = $DB->query("SELECT * FROM menus WHERE menuid='$menuid'");
	if (!($rd = $R->fetchArray()))
	{
		return; // Should complain
	}
	$menulbl = $rd['menulbl'];
	$functions = explode(',',$rd['menufuncs']);
	echo('<div id="adminMM">');
	echo('<h4 title="'.$TAGS[$menulbl][1].'">'.$TAGS[$menulbl][0].'</h4>');
	echo('<ul class="menulist">');
	foreach($functions as $f)
	{
		if ($f == '')
		{
			continue;
		}
		$R = $DB->query("SELECT * FROM functions WHERE functionid=$f");
		if (!($rd = $R->fetchArray()))
		{
			continue;
		}
		echo('<li title="'.$TAGS[$rd['menulbl']][1].'"');
		if (!is_null($rd['onclick']))
		{
			$x = $rd['onclick'];
			echo(' onclick="'.$x.'"');
		}
		echo('>');
		echo('<a href="'.$rd['url'].'">'.$TAGS[$rd['menulbl']][0].'</a>');
		echo('</li>');
		
	}
	echo('</ul>');
	show_menu_taglist();
	echo('</div>');
	showFooter();
	exit;
	
	
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
	echo("<select id='menu_tagselect'");
	echo(' title="'.$TAGS['AdmSelectTag'][1].'"');
	echo(' onchange="window.location.href = '."'admin.php?tag='");
	echo("+document.getElementById('menu_tagselect').value".'";');
	echo('>');
	echo('<option value="">'.$TAGS['AdmSelectTag'][0].'</option>');
	foreach($mytags as $t)
	{
		if ($t != '')
			echo("<option value='$t'>$t</option>");
	}
	echo("</select>");
	
}

function show_tagmenu($tag)
{
	global $TAGS,$DB;
	
	echo('<div id="adminMM">');
	echo('<h4 title="'.$TAGS['AdmShowTagMatches'][1].$tag.'">'.$TAGS['AdmShowTagMatches'][0].$tag.'</h4>');
	echo('<ul class="menulist">');
	$R = $DB->query("SELECT * FROM functions");
	while ($rd = $R->fetchArray())
	{
		$taglist = explode(',',$rd['Tags']);
		//var_dump($taglist);
		if (!in_array($tag,$taglist))
			continue;
		echo('<li title="'.$TAGS[$rd['menulbl']][1].'"');
		if (!is_null($rd['onclick']))
		{
			$x = $rd['onclick'];
			echo(' onclick="'.$x.'"');
		}
		echo('>');
		echo('<a href="'.$rd['url'].'">'.$TAGS[$rd['menulbl']][0].'</a>');
		echo('</li>');
		
	}
	echo('</ul>');
	show_menu_taglist();
	echo('</div>');
	showFooter();
	exit;
	
	
}



function showSetupMenu()
{
	global $TAGS;
	
	$MAINMENU = array(
		'AdmRallyParams'	=> array('sm.php?c=rallyparams',NULL),
		'AdmEditCert'		=> array('admin.php?c=editcert',NULL),
		'AdmEntrantsHeader'	=> array('admin.php?c=entrants',NULL),
		'AdmBonusHeader'	=> array('admin.php?c=bonus',NULL),
		'AdmTimePenalties'	=> array('sm.php?c=timep',NULL),
		'AdmCatTable'		=> array('sm.php?c=showcat&axis=1',NULL),
		'AdmCompoundCalcs'	=> array('sm.php?c=catcalcs',NULL)
	);
	showMenu($MAINMENU,$TAGS['AdmSetupHeader'][0]);
	
}

function showInitialisationOffer()
{
	global $TAGS, $DB;

	echo('<h4 title="'.$TAGS['ZapDatabaseOffer'][1].'">'.$TAGS['ZapDatabaseOffer'][0].'</h4>');
	echo('<p>'.$TAGS['ZapDBCaution'][1].'</p>');
	echo('<form method="post" action="admin.php">');
	echo('<input type="hidden" name="c" value="zapdb">');
	echo('<label for="ruSure1">'.$TAGS['ZapDBRUSure1'][1].'</label> ');
	echo('<input type="checkbox" id="ruSure1" name="ruSure1" value="'.$TAGS['ZapDBRUSure1'][0].'"><br>');
	echo('<label for="ruSure2">'.$TAGS['ZapDBRUSure2'][1].'</label> ');
	echo('<input type="checkbox" id="ruSure2" name="ruSure2" value="'.$TAGS['ZapDBRUSure2'][0].'"><br>');
	echo('<label for="ruCancel">'.$TAGS['ZapDBRUCancel'][1].'</label> ');
	echo('<input type="checkbox" id="ruCancel" name="ruCancel" value="'.$TAGS['ZapDBRUCancel'][0].'"><br>');
	echo('<input type="submit" name="zapdb" title="'.$TAGS['ZapDBGo'][1].'" value="'.$TAGS['ZapDBGo'][0].'">');
	echo('</form>');
	
	echo('</body>');
	echo('</html>');
	
	
}

function zapDatabase()
{
	global $TAGS, $DB;
	
	$DB->query('BEGIN TRANSACTION');
	$DB->query('DELETE FROM rallyparams');
	$sql = "INSERT INTO rallyparams (RallyTitle,RallySlogan) VALUES('".$TAGS['ZapDBRallyTitle'][1]."','".$TAGS['ZapDBRallySlogan'][1]."')";
	//echo($sql.'<hr>');
	$DB->query($sql);
	$DB->query('DELETE FROM bonuses');
	$DB->query('DELETE FROM catcompound');
	$DB->query('DELETE FROM categories');
	$DB->query('DELETE FROM claims');
	$DB->query('DELETE FROM combinations');
	$DB->query('DELETE FROM entrants');
	$DB->query('DELETE FROM sgroups');
	$DB->query('DELETE FROM specials');
	$DB->query('DELETE FROM timepenalties');
	$DB->query('COMMIT TRANSACTION');
	
	echo('<h4 title="'.TAGS['ZapDatabaseZapped'][1].'">'.$TAGS['ZapDatabaseZapped'][0].'</h4>');
	showSetupMenu();
	
}

function isZapDBCommand()
{
	global $TAGS;
	
	$res = isset($_REQUEST['c']) && isset($_REQUEST['zapdb']) && isset($_REQUEST['ruSure1']) &&	isset($_REQUEST['ruSure2']);
	if (isset($_REQUEST['ruCancel']))
	{
		$res = FALSE;
	}
	if (!$res)
		return FALSE;
	if ($_REQUEST['ruSure1'] != $TAGS['ZapDBRUSure1'][0])
		$res = FALSE;
	if ($_REQUEST['ruSure2'] != $TAGS['ZapDBRUSure2'][0])
		$res = FALSE;
			
	return $res;
	
}

global $TAGS;

startHtml('<a href="about.php" class="techie" title="'.$TAGS['HelpAbout'][1].'">'.$TAGS['HelpAbout'][0].'</a>');
//var_dump($_REQUEST);
if (isset($_REQUEST['c']) && $_REQUEST['c']=='setup')
	showSetupMenu();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='entrants')
	showEntrantsMenu();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='bonus')
	showBonusMenu();
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='offerzap')
	showInitialisationOffer();
else if (isZapDBCommand())
		ZapDatabase();
else if (isset($_REQUEST['menu']))
	show_menu($_REQUEST['menu']);
else if (isset($_REQUEST['tag']))
	show_tagmenu($_REQUEST['tag']);
else if (isset($_REQUEST['c']) && $_REQUEST['c']=='testmenu')
	show_tagmenu('score');
else
	showAdminMenu();


?>