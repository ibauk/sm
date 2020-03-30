<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle the claims log
 *
 * I am written for readability rather than efficiency, please keep me that way.
 *
 *
 * Copyright (c) 2020 Bob Stammers
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
 *
 */


$HOME_URL = "admin.php";
require_once('common.php');

function listclaims()
{
	global $DB,$TAGS,$KONSTANTS;
	
	$sql = "SELECT * FROM claims ORDER BY LoggedAt DESC";
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	startHtml("claims");
	echo('<table><thead>');
	echo('<tr><th>Entrant</th><th>Bonus</th><th>Decision</th><th>LoggedAt</th></tr>');
	echo('<tbody>');
	while ($rd = $R->fetchArray()) {
		echo('<tr>');
		echo('<td>');
		$ename = getValueFromDB("SELECT RiderName FROM entrants WHERE EntrantID=".$rd['EntrantID'],"RiderName",$rd['EntrantID']);
		echo(htmlspecialchars($ename).'</td>');
		echo('<td>'.$rd['BonusID'].'</td>');
		$status = ($rd['Judged'] != 0 ? $rd['Decision'] : '-');
		echo('<td>'.$status.'</td>');	
		echo('<td>'.$rd['LoggedAt'].'</td>');
		echo('</tr>');
	}
	
	
}

function saveNewClaim()
{
	global $DB,$TAGS,$KONSTANTS;

	$sql = "INSERT INTO claims (LoggedAt,ClaimTime,BCMethod,EntrantID,BonusID,OdoReading,Judged,Decision,Applied) VALUES(";
	$la = (isset($_REQUEST['LoggedAt']) && $_REQUEST['LoggedAt'] != '' ? $_REQUEST['LoggedAt'] : date('Y-m-d H:i:s'));
	$sql .= "'".$la."'";
	$cd = (isset($_REQUEST['ClaimDate']) && $_REQUEST['ClaimDate'] != '' ? $_REQUEST['ClaimDate'] : date('Y-m-d'));
	$ct = (isset($_REQUEST['ClaimTime']) && $_REQUEST['ClaimTime'] != '' ? $cd.' '.$_REQUEST['ClaimTime'] : $la);
	$sql .= ",'".$ct."'";
	$sql .= ",".(isset($_REQUEST['BCMethod']) ? $_REQUEST['BCMethod'] : $KONSTANTS['BCM_EBC']);
	$sql .= ",".intval($_REQUEST['EntrantID']);
	$sql .= ",'".$DB->escapeString($_REQUEST['BonusID'])."'";
	$sql .= ",".intval($_REQUEST['OdoReading']);
	$judged = (isset($_REQUEST['Judged']) ? $_REQUEST['Judged'] : 0);
	if ($judged == 0 && isset($_REQUEST['Decision']) && $_REQUEST['Decision'] >= 0)
		$judged = 1;
	$sql .= ",".$judged;
	$sql .= ",".(isset($_REQUEST['Decision']) ? $_REQUEST['Decision'] : 0);
	$sql .= ",".(isset($_REQUEST['Applied']) ? $_REQUEST['Applied'] : 0);
	$sql .= ")";
	$DB->exec($sql);
	
}

function showNewClaim()
{
	global $DB,$TAGS,$KONSTANTS;

	startHtml('new claim');
	echo('<form method="post" action="claims.php">');
	echo('<input type="hidden" name="c" value="newclaim">');
	echo('<input type="hidden" name="LoggedAt" value="">');
	echo('<input type="hidden" name="BCMethod" value="'.$KONSTANTS['BCM_EBC'].'">');
	echo('<input type="hidden" name="Applied" value="0">');
	
	echo('<span class="vlabel" title="'.'"><label for="EntrantID">'.'Entrant'.'</label> ');
?>
<script>
function showEntrant(str) {
  var xhttp;
  if (str == "") {
    document.getElementById("EntrantName").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("EntrantName").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "claims.php?c=entnam&e="+str, true);
  xhttp.send();
}
</script>
<?php	
	echo('<input type="number" name="EntrantID" id="EntrantID" tabindex="1" onchange="showEntrant(this.value);"> ');
	echo('<span id="EntrantName"></span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.'"><label for="BonusID">'.'Bonus'.'</label> ');
?>
<script>
function showBonus(str) {
  var xhttp;
  if (str == "") {
    document.getElementById("BonusName").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("BonusName").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "claims.php?c=bondes&b="+str, true);
  xhttp.send();
}
</script>
<?php	
	echo('<input type="text" name="BonusID" id="BonusID" tabindex="2" onchange="showBonus(this.value);"> ');
	echo('<span id="BonusName"></span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.'"><label for="OdoReading">'.'Odo'.'</label> ');
	echo('<input type="number" class="bignumber" name="OdoReading" id="OdoReading" tabindex="3">');
	echo('</span>');

	echo('<span class="vlabel" title="'.'"><label for="ClaimTime">'.'Claim time'.'</label> ');
	echo('<input type="date" name="ClaimDate" id="ClaimDate" value="'.date("Y-m-d").'" tabindex="8"> ');
	echo('<input type="time" name="ClaimTime" id="ClaimTime" value="" tabindex="4">');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.'"><label for="Decision">'.'Decision'.'</label> ');
	echo('<select name="Decision" id="Decision" tabindex="5">');
	echo('<option selected value="-1">'.'undecided'.'</option>');
	echo('<option value="0">'.'Good claim'.'</option>');
	$rr = explode("\n",str_replace("\r","",getValueFromDB("SELECT RejectReasons FROM rallyparams","RejectReasons","1=1")));
	foreach($rr as $rt) {
		$rtt = explode('=',$rt);
		echo('<option value="'.$rtt[0].'">'.$rtt[1].'</option>');
	}
	echo('</select></span>');

	//echo('<span class="vlabel" title="'.'"><label for="Applied">'.'Applied?'.'</label> ');
	//echo('<select name="Applied" id="Applied">');
	//echo('<option selected value="0">'.'outstanding'.'</option>');
	//echo('<option value="1">'.'Decision processed'.'</option>');
	//echo('</select></span>');
	
	echo('<span class="vlabel" title="'.'"><label for="submitbutton">'.''.'</label>');
	echo('<input type="submit" name="savenewclaim" id="submitbutton" value="'.'Submit'.'" tabindex="7"></span>');
	echo('</form>');
	echo('<body></html>');
}

function fetchBonusName()
{
	global $DB,$TAGS,$KONSTANTS;

	if (!isset($_REQUEST['b'])) {
		echo('');
		exit;
	}
	$R = $DB->query("SELECT BriefDesc FROM bonuses WHERE BonusID='".$_REQUEST['b']."'");
	if ($rd = $R->fetchArray())
		echo($rd['BriefDesc']);
	else {
		$R = $DB->query("SELECT BriefDesc FROM specials WHERE BonusID='".$_REQUEST['b']."'");
		if ($rd = $R->fetchArray())
			echo($rd['BriefDesc']);
		else {
			$R = $DB->query("SELECT BriefDesc FROM combinations WHERE ComboID='".$_REQUEST['b']."'");
			if ($rd = $R->fetchArray())
				echo($rd['BriefDesc']);
			else
				echo('');
		}
	}
	exit;
}

function fetchEntrantName()
{
	global $DB,$TAGS,$KONSTANTS;

	if (!isset($_REQUEST['e'])) {
		echo('');
		exit;
	}
	$R = $DB->query("SELECT * FROM entrants WHERE EntrantID=".$_REQUEST['e']);
	if ($rd = $R->fetchArray())
		echo($rd['RiderName']);
	else
		echo('');
	exit;
}

if (isset($_REQUEST['savenewclaim']))
	saveNewClaim();
if (isset($_REQUEST['c'])) {
	if ($_REQUEST['c']=='shownew')
		showNewClaim();
	if ($_REQUEST['c']=='entnam')
		fetchEntrantName();
	if ($_REQUEST['c']=='bondes')
		fetchBonusName();
	
	// If dropped through then just list the buggers.
	listClaims();
}
else
	listclaims();

?>
