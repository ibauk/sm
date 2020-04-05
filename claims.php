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

function emitClaimsJS()
{
?>

<script>
//<!--
function showCurrentClaim(obj)
{
	let claimid = obj.parentNode.getAttribute('data-rowid');
	let url = "claims.php?c=showclaim&claimid="+claimid+'&dd=';
	url += document.getElementById('decisiondefault').value;
	url += '&entrantid='+document.getElementById('entrantid').value;
	url += '&bonusid='+document.getElementById('bonusid').value;
	window.location.href = url;
}
function updateClaimApplied(obj)
{
	let val = obj.checked ? 1 : 0;
	let rowid = obj.parentNode.parentNode.getAttribute('data-rowid');
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		let ok = new RegExp("\W*ok\W*");
		if (this.readyState == 4 && this.status == 200) {
			console.log('{'+this.responseText+'}');
			if (!ok.test(this.responseText))
				alert(UPDATE_FAILED);
		}
	};
	xhttp.open("GET", "claims.php?c=applyclaim&claim="+rowid+'&val='+val, true);
	xhttp.send();
	
}
function updateClaimDecision(obj)
{
	let val = obj.value;
	let rowid = obj.parentNode.parentNode.getAttribute('data-rowid');
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		let ok = new RegExp("\W*ok\W*");
		if (this.readyState == 4 && this.status == 200) {
			console.log('{'+this.responseText+'}');
			if (!ok.test(this.responseText))
				alert(UPDATE_FAILED);
		}
	};
	xhttp.open("GET", "claims.php?c=decideclaim&claim="+rowid+'&val='+val, true);
	xhttp.send();
	
}
//-->
</script>
<?php	
}

function deleteClaim()
{
	global $DB,$TAGS,$KONSTANTS;
		
	$DB->exec("DELETE FROM claims WHERE rowid=".$_REQUEST['claimid']);
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}
	
}

function listclaims()
{
	global $DB,$TAGS,$KONSTANTS;
	
	$showAll = 0;		// ignore Judged/applied status
	$showOnly = 1;		// show only Judged/applied claims
	$showNot = 2;		// show only undecided/unapplied claims
	
	$rr = explode("\n",str_replace("\r","",getValueFromDB("SELECT RejectReasons FROM rallyparams","RejectReasons","1=1")));
	$decisions = [];
	$decisions['0'] = $TAGS['BonusClaimOK'][0];
	foreach($rr as $rt) {
		$rtt = explode('=',$rt);
		if (isset($rtt[1]))
			$decisions[$rtt[0]] = $rtt[1];
	}
	$sql = "SELECT rowid, * FROM claims";
	if (isset($_REQUEST['entrantid']) || isset($_REQUEST['bonusid']) || isset($_REQUEST['showd']) || isset($_REQUEST['showa']) ) {
		$sqlw = '';
		if (isset($_REQUEST['entrantid']) && $_REQUEST['entrantid']!='')
			$sqlw .= "EntrantID=".$_REQUEST['entrantid'];
		if (isset($_REQUEST['bonusid']) && $_REQUEST['bonusid']!='')
			$sqlw .= ($sqlw != ''? ' AND ' : '')."Bonusid='".$DB->escapeString($_REQUEST['bonusid'])."'";
		if (isset($_REQUEST['showd']) && $_REQUEST['showd']!=$showAll)
			$sqlw .= ($sqlw != ''? ' AND ' : '').'Judged'.($_REQUEST['showd']==$showNot ? '=0': '<>0');
		if (isset($_REQUEST['showa']) && $_REQUEST['showa']!=$showAll)
			$sqlw .= ($sqlw != ''? ' AND ' : '').'Applied'.($_REQUEST['showa']==$showNot ? '=0': '<>0');
		if ($sqlw !='')
			$sql .= " WHERE ".$sqlw;
	}
	$sql .= " ORDER BY LoggedAt DESC";
	//echo($sql);
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	startHtml("claims");
	emitClaimsJS();
	echo('<div>');
	echo('<form method="get" action="claims.php">');
	echo('<input id="refreshc" type="hidden" name="c" value="listclaims">');
	echo('<input type="submit" id="refreshlist" onclick="document.getElementById(\'refreshc\').value=\'listclaims\';" title="'.$TAGS['cl_RefreshList'][1].'" value="'.$TAGS['cl_RefreshList'][0].'"> ');
	echo('<input type="number" placeholder1="'.$TAGS['cl_FilterEntrant'][0].'" title="'.$TAGS['cl_FilterEntrant'][1].'" id="entrantid" name="entrantid" value="'.(isset($_REQUEST['entrantid'])? $_REQUEST['entrantid']:'').'"> ');
	echo('<input type="text" placeholder1="'.$TAGS['cl_FilterBonus'][0].'" title="'.$TAGS['cl_FilterBonus'][1].'" id="bonusid" name="bonusid" value="'.(isset($_REQUEST['bonusid'])? $_REQUEST['bonusid']:'').'"> ');
	
	
	$decided = isset($_REQUEST['showd']) ? intval($_REQUEST['showd']) : $showAll;
	echo('<select name="showd" style="font-size: small;" title="'.$TAGS['cl_showAllD'][1].'" onchange="document.getElementById(\'refreshlist\').click();"> ');
	echo('<option value="'.$showAll.'" '.($decided==$showAll ? 'selected' : '').'>'.$TAGS['cl_showAllD'][0].'</option>');
	echo('<option value="'.$showOnly.'" '.($decided==$showOnly ? 'selected' : '').'>'.$TAGS['cl_showOnlyD'][0].'</option>');
	echo('<option value="'.$showNot.'" '.($decided==$showNot ? 'selected' : '').'>'.$TAGS['cl_showNotD'][0].'</option>');
	echo('</select> ');
	
	$applied = isset($_REQUEST['showa']) ? intval($_REQUEST['showa']) : $showAll;
	echo('<select name="showa" style="font-size: small;" title="'.$TAGS['cl_showAllA'][1].'" onchange="document.getElementById(\'refreshlist\').click();"> ');
	echo('<option value="'.$showAll.'" '.($applied==$showAll ? 'selected' : '').'>'.$TAGS['cl_showAllA'][0].'</option>');
	echo('<option value="'.$showOnly.'" '.($applied==$showOnly ? 'selected' : '').'>'.$TAGS['cl_showOnlyA'][0].'</option>');
	echo('<option value="'.$showNot.'" '.($applied==$showNot ? 'selected' : '').'>'.$TAGS['cl_showNotA'][0].'</option>');
	echo('</select> ');
	
	echo('<span title="'.$TAGS['cl_DDLabel'][1].'" style="font-size:small;">');
	echo('<label for="decisiondefault">'.$TAGS['cl_DDLabel'][0].'</label> ');
	echo('<select id="decisiondefault" name="dd" style="font-size:small;"> ');
	echo('<option value="-1" '.(!isset($_REQUEST['dd']) || $_REQUEST['dd']=='-1'?'selected':'').'>'.$TAGS['BonusClaimUndecided'][0].'</option>');
	echo('<option value="0" '.(isset($_REQUEST['dd']) && $_REQUEST['dd']=='0'?'selected':'').'>'.$TAGS['BonusClaimOK'][0].'</option>');
	echo('</select> ');
	echo('</span>');
	echo('<input onclick="document.getElementById(\'refreshc\').value=\'shownew\';" type="submit" title="'.$TAGS['cl_PostNewClaim'][1].'" value="'.$TAGS['cl_PostNewClaim'][0].'"> ');
	echo('</form>');
	echo('</div>');
	echo('<table><thead class="listhead">');
	echo('<tr><th>'.$TAGS['cl_EntrantHdr'][0].'</th><th>'.$TAGS['cl_BonusHdr'][0].'</th><th>'.$TAGS['cl_OdoHdr'][0].'<th>'.$TAGS['cl_ClaimedHdr'][0].'</th><th>'.$TAGS['cl_DecisionHdr'][0].'</th><th>'.$TAGS['cl_AppliedHdr'][0].'</th><th>'.$TAGS['cl_LoggedHdr'][0].'</th></tr>');
	echo('<tbody>');
	while ($rd = $R->fetchArray()) {
		echo('<tr data-rowid="'.$rd['rowid'].'">');
		echo('<td title="'.$rd['EntrantID'].'" onclick="showCurrentClaim(this);" class="clickme">');
		$ename = getValueFromDB("SELECT RiderName FROM entrants WHERE EntrantID=".$rd['EntrantID'],"RiderName",$rd['EntrantID']);
		echo(htmlspecialchars($ename).' </td>');
		echo('<td title="');
		fetchBonusName($rd['BonusID']);
		echo('"> '.$rd['BonusID'].' </td>');
		echo('<td> '.$rd['OdoReading'].' </td>');
		echo('<td> '.logtime($rd['ClaimTime']).' </td>');
		if ($rd['Applied']==0) {
			$status = '<select onchange="updateClaimDecision(this);">';
			$status .= '<option value="-1"'.($rd['Judged']==0? ' selected' : '').'>'.$TAGS['BonusClaimUndecided'][0].'</option>';
			for ($i=0; $i<10; $i++)
				$status .= '<option value="'.$i.'"'.($rd['Judged']!=0 && $rd['Decision']==$i? ' selected' :'').'>'.$decisions[$i].'</option>';
			$status .= '</select>';
		} else
			$status = ($rd['Judged'] != 0 ? $decisions[$rd['Decision']] : $TAGS['BonusClaimUndecided'][0]);
		echo('<td> '.$status.' </td>');	
		echo('<td title="'.$TAGS['cl_Applied'][1].'" style="text-align:center;">'.'<input type="checkbox" onchange="updateClaimApplied(this);" value="1"'.($rd['Applied']!=0? ' checked' :'').'>');
		echo('<td> '.logtime($rd['LoggedAt']).' </td>');
		echo('</tr>');
	}
	echo('</tbody></table>');
?>
<script>
setTimeout(function(){document.getElementById('refreshlist').click();},120000);
</script>
<?php

	
}

function logtime($stamp)
/* We're really only interested in the time of day and which of a few days it's on */
{
	//echo($stamp);
	return '<span title="'.$stamp.'">'.gmdate('D H:i',strtotime($stamp)).'</span>';
}

function saveClaim()
{
	global $DB,$TAGS,$KONSTANTS;

	$claimid = isset($_REQUEST['claimid']) ? intval($_REQUEST['claimid']) : 0;
	if ($claimid <= 0) {
		saveNewClaim();
		return;
	}
	
	$sql = "UPDATE claims SET ";
	$sql = '';
	if (isset($_REQUEST['ClaimDate']) && isset($_REQUEST['ClaimTime']))
		$sql .= ($sql==''? '' : ',')."ClaimTime='".$_REQUEST['ClaimDate'].' '.$_REQUEST['ClaimTime']."'";
	foreach (['BonusID'] as $fld)
		if (isset($_REQUEST[$fld]))
			$sql .= ($sql==''? '' : ',').$fld."='".$DB->escapeString($_REQUEST[$fld])."'";
	foreach (['BCMethod','EntrantID','OdoReading','Judged','Decision','Applied'] as $fld)
		if (isset($_REQUEST[$fld]))
			$sql .= ($sql==''? '' : ',').$fld."=".intval($_REQUEST[$fld]);
	if (isset($_REQUEST['Decision']) && !isset($_REQUEST['Judged']))
		$sql .= ($sql==''? '' : ',')."Judged=".(intval($_REQUEST['Decision'])<0? 0 : 1);
	//echo("[[ $sql ]]");
	if ($sql=='')
		return;
	$sql.= " WHERE rowid=".$claimid;
	$DB->exec("UPDATE claims SET ".$sql);
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
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

function showClaim($claimid = 0)
{
	global $DB,$TAGS,$KONSTANTS;

	startHtml('sm: new claim');
	
	if ($claimid > 0) {
		$R  = $DB->query("SELECT * FROM claims WHERE rowid=".$claimid);
		if (!$rd = $R->fetchArray())
			$claimid =0;
	}
	echo('<form method="post" action="claims.php">');
	echo('<input type="hidden" name="c" value="newclaim">');
	echo('<input type="hidden" name="LoggedAt" value="">');
	echo('<input type="hidden" name="BCMethod" value="'.$KONSTANTS['BCM_EBC'].'">');
	echo('<input type="hidden" name="Applied" value="0">');
	echo('<input type="hidden" name="claimid" value="'.$claimid.'">');
	echo('<input type="hidden" name="dd" value="'.(isset($_REQUEST['dd']) ? $_REQUEST['dd'] : '-1').'">');
	echo('<input type="hidden" name="showa" value="'.(isset($_REQUEST['showa']) ? $_REQUEST['showa'] : '-1').'">');
	echo('<input type="hidden" name="showd" value="'.(isset($_REQUEST['showd']) ? $_REQUEST['showd'] : '-1').'">');
	echo('<span class="vlabel" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].'</label> ');
?>
<script>
function showEntrant(str) {
  let xhttp;
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
	echo('<input autofocus type="number" name="EntrantID" id="EntrantID" tabindex="1" onchange="showEntrant(this.value);"');
	echo(' value="'.($claimid > 0 ? $rd['EntrantID'] : '').'"> ');
	echo('<span id="EntrantName">');
	if ($claimid > 0)
		fetchEntrantName($rd['EntrantID']);
	echo('</span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['BonusIDLit'][1].'"><label for="BonusID">'.$TAGS['BonusIDLit'][0].'</label> ');
?>
<script>
function showBonus(str) {
  let xhttp;
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
	echo('<input type="text" name="BonusID" id="BonusID" tabindex="2" onchange="showBonus(this.value);"');
	echo(' value="'.($claimid> 0 ? $rd['BonusID'] : '').'"> ');
	echo('<span id="BonusName">');
	if ($claimid > 0)
		fetchBonusName($rd['BonusID']);
	echo('</span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['OdoReadingLit'][1].'"><label for="OdoReading">'.$TAGS['OdoReadingLit'][0].'</label> ');
	echo('<input type="number" class="bignumber" name="OdoReading" id="OdoReading" tabindex="3"');
	echo(' value="'.($claimid > 0 ? $rd['OdoReading'] : '').'">');
	echo('</span>');

	$ct = splitDatetime($claimid > 0 ? $rd['ClaimTime'] : date('Y-m-d').'T');
	echo('<span class="vlabel" title="'.$TAGS['BonusClaimTime'][1].'"><label for="ClaimTime">'.$TAGS['BonusClaimTime'][0].'</label> ');
	echo('<input type="date" name="ClaimDate" id="ClaimDate" value="'.$ct[0].'" tabindex="8"> ');
	echo('<input type="time" name="ClaimTime" id="ClaimTime" value="'.$ct[1].'" tabindex="4">');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['BonusClaimDecision'][1].'"><label for="Decision">'.$TAGS['BonusClaimDecision'][0].'</label> ');
	echo('<select name="Decision" id="Decision" tabindex="5">');
	$dnum = $claimid > 0 ? ($rd['Judged']!=0 ? $rd['Decision'] : -1) : (isset($_REQUEST['dd']) && $_REQUEST['dd']=='0' ? 0 : -1);
	echo('<option value="-1" '.($dnum==-1 ? 'selected' : '').'>'.$TAGS['BonusClaimUndecided'][0].'</option>');
	echo('<option value="0" '.($dnum==0 ? 'selected' : '').'>'.$TAGS['BonusClaimOK'][0].'</option>');
	$rr = explode("\n",str_replace("\r","",getValueFromDB("SELECT RejectReasons FROM rallyparams","RejectReasons","1=1")));
	foreach($rr as $rt) {
		$rtt = explode('=',$rt);
		if ($rtt[0]!='')
			echo("\r\n".'<option value="'.$rtt[0].'" '.($dnum==$rtt[0] ? 'selected' : '').'>'.$rtt[1].'</option>');
	}
	echo('</select></span>');


	echo('<span class="vlabel" title="'.'"><label for="submitbutton">'.''.'</label>');
	echo('<input type="submit" title="'.$TAGS['SaveRecord'][1].'" name="saveclaim" id="submitbutton" value="'.$TAGS['SaveRecord'][0].'" tabindex="7"> ');
	
	if ($claimid > 0) {
		echo(' &nbsp;&nbsp;<input type="checkbox" id="ReallyDelete"> ');
		echo('<input onclick="return document.getElementById(\'ReallyDelete\').checked;" type="submit" title="'.$TAGS['DeleteClaim'][1].'" name="deleteclaim" id="deletebutton" value="'.$TAGS['DeleteClaim'][0].'" tabindex="9"></span> ');
	}

	echo('</form>');
	echo('<body></html>');
}

function showNewClaim()
{
	showClaim(0);
}

function fetchBonusName($b)
{
	global $DB,$TAGS,$KONSTANTS;

	if ($b=='') {
		echo('');
		return;
	}
	$R = $DB->query("SELECT BriefDesc FROM bonuses WHERE BonusID='".$b."'");
	if ($rd = $R->fetchArray())
		echo(htmlspecialchars($rd['BriefDesc']));
	else {
		$R = $DB->query("SELECT BriefDesc FROM specials WHERE BonusID='".$b."'");
		if ($rd = $R->fetchArray())
			echo(htmlspecialchars($rd['BriefDesc']));
		else {
			$R = $DB->query("SELECT BriefDesc FROM combinations WHERE ComboID='".$b."'");
			if ($rd = $R->fetchArray())
				echo(htmlspecialchars($rd['BriefDesc']));
			else
				echo('***');
		}
	}
}

function updateClaimApplied()
{
	global $DB,$TAGS,$KONSTANTS;

	if (!isset($_REQUEST['claim']) || !isset($_REQUEST['val'])) {
		echo('');
		return;
	}
	$sql = "UPDATE claims SET Applied=".$_REQUEST['val'];
	$sql .= " WHERE rowid=".$_REQUEST['claim'];
	echo($DB->exec($sql) && $DB->changes()==1? 'ok' : 'error' );
}

function updateClaimDecision()
{
	global $DB,$TAGS,$KONSTANTS;

	if (!isset($_REQUEST['claim']) || !isset($_REQUEST['val'])) {
		echo('');
		return;
	}
	$sql = "UPDATE claims SET Judged=".($_REQUEST['val']<0?0:1);
	$sql .= ",Decision=".$_REQUEST['val'];
	$sql .= " WHERE rowid=".$_REQUEST['claim'];
	$sql .= " AND Applied=0";
	echo($DB->exec($sql) && $DB->changes()==1? 'ok' : 'error');
}


function fetchEntrantName($e)
{
	global $DB,$TAGS,$KONSTANTS;

	if ($e=='') {
		echo('');
		return;
	}
	$R = $DB->query("SELECT * FROM entrants WHERE EntrantID=".$e);
	if ($rd = $R->fetchArray())
		echo($rd['RiderName']);
	else
		echo('***');

}
if (isset($_REQUEST['deleteclaim']) && isset($_REQUEST['claimid']) && $_REQUEST['claimid']>0) {
	deleteClaim();
	listclaims();
	exit;
}

if (isset($_REQUEST['saveclaim']))
	saveClaim();
if (isset($_REQUEST['c'])) {
	if ($_REQUEST['c']=='showclaim' && isset($_REQUEST['claimid']) && intval($_REQUEST['claimid'])>0) {
		showClaim(intval($_REQUEST['claimid']));
		exit;
	}
		
	if ($_REQUEST['c']=='shownew') {
		showNewClaim();
		exit;
	}
	if ($_REQUEST['c']=='decideclaim') {
		updateClaimDecision();
		exit;
	}
	if ($_REQUEST['c']=='applyclaim') {
		updateClaimApplied();
		exit;
	}
	if ($_REQUEST['c']=='entnam') {
		$e = (isset($_REQUEST['e']) ? $_REQUEST['e'] : '');
		fetchEntrantName($e);
		exit;
	}
	if ($_REQUEST['c']=='bondes') {
		$b = (isset($_REQUEST['b']) ? $_REQUEST['b'] : '');
		fetchBonusName($b);
		exit;
	}
	
	// If dropped through then just list the buggers.
	listclaims();
}
else
	listclaims();

?>
