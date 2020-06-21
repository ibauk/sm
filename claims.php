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
	url += '&showe='+document.getElementById('showe').value;
	url += '&showb='+document.getElementById('showb').value;
	window.location.href = url;
}
function updateClaimApplied(obj)
{
	let val = obj.checked ? 1 : 0;
	let oldval = !obj.checked;
	let rowid = obj.parentNode.parentNode.getAttribute('data-rowid');
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		let ok = new RegExp("\W*ok\W*");
		if (this.readyState == 4 && this.status == 200) {
			console.log('{'+this.responseText+'}');
			if (!ok.test(this.responseText)) {
				obj.checked = oldval;
				alert(UPDATE_FAILED);
			}
		}
	};
	xhttp.open("GET", "claims.php?c=applyclaim&claim="+rowid+'&val='+val, true);
	xhttp.send();
	
}
function updateClaimDecision(obj)
{
	let val = obj.value;
	let oldval = obj.getAttribute('data-oldval');
	console.log(val+' == '+oldval);
	let rowid = obj.parentNode.parentNode.getAttribute('data-rowid');
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		let ok = new RegExp("\W*ok\W*");
		if (this.readyState == 4 && this.status == 200) {
			console.log('{'+this.responseText+'}');
			if (!ok.test(this.responseText)) {
				obj.value = oldval;
				alert(UPDATE_FAILED);
			}
			else
				obj.setAttribute('data-oldval',obj.value);
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
	
	$virtualrally = getValueFromDB("SELECT isvirtual FROM rallyparams","isvirtual",0) != 0;
	
	$rr = explode("\n",str_replace("\r","",getValueFromDB("SELECT RejectReasons FROM rallyparams","RejectReasons","1=1")));
	$decisions = [];
	$decisions['0'] = $TAGS['BonusClaimOK'][0];
	foreach($rr as $rt) {
		$rtt = explode('=',$rt);
		if (isset($rtt[1]))
			$decisions[$rtt[0]] = $rtt[1];
	}
	$sql = "SELECT Count(*) AS rex FROM claims";
	$sqlw = '';
	if (isset($_REQUEST['showe']) || isset($_REQUEST['showb']) || isset($_REQUEST['showd']) || isset($_REQUEST['showa']) ) {
		if (isset($_REQUEST['showe']) && $_REQUEST['showe']!='')
			$sqlw .= "EntrantID=".$_REQUEST['showe'];
		if (isset($_REQUEST['showb']) && $_REQUEST['showb']!='')
			$sqlw .= ($sqlw != ''? ' AND ' : '')."Bonusid='".$DB->escapeString($_REQUEST['showb'])."'";
		if (isset($_REQUEST['showd']) && $_REQUEST['showd']!=$KONSTANTS['showAll'] && $_REQUEST['showd']!='')
			$sqlw .= ($sqlw != ''? ' AND ' : '').'Decision'.($_REQUEST['showd']==$KONSTANTS['showNot'] ? '='.$KONSTANTS['UNDECIDED_CLAIM']: '<>'.$KONSTANTS['UNDECIDED_CLAIM']);
		if (isset($_REQUEST['showa']) && $_REQUEST['showa']!=$KONSTANTS['showAll']  && $_REQUEST['showa']!='')
			$sqlw .= ($sqlw != ''? ' AND ' : '').'Applied'.($_REQUEST['showa']==$KONSTANTS['showNot'] ? '=0': '<>0');
	}
	if ($sqlw !='')
		$sql .= " WHERE ".$sqlw;
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	$rd = $R->fetchArray();
	$rex = ($rd ? $rd['rex'] :-1);
	$sql = "SELECT rowid, * FROM claims";
	if ($sqlw !='')
		$sql .= " WHERE ".$sqlw;
	$sql .= " ORDER BY LoggedAt DESC";
//	echo($sql);
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	startHtml($TAGS['ttClaims'][0]);
	emitClaimsJS();
	
	echo('<p>'.$TAGS['cl_ClaimsBumf'][1].'</p>');
	
	echo('<div id="listctrl">');
	echo('<form method="get" action="claims.php">');
	echo('<span title="'.$TAGS['cl_NumClaims'][1].'">'.$rex.' </span>');
	echo('<input id="refreshc" type="hidden" name="c" value="listclaims">');
	echo('<input type="hidden" name="nobc" value="1">');
	echo('<input type="submit" id="refreshlist" onclick="document.getElementById(\'refreshc\').value=\'listclaims\';" title="'.$TAGS['cl_RefreshList'][1].'" value="'.$TAGS['cl_RefreshList'][0].'"> ');
	echo('<input type="number" placeholder1="'.$TAGS['cl_FilterEntrant'][0].'" title="'.$TAGS['cl_FilterEntrant'][1].'" id="showe" name="showe" value="'.(isset($_REQUEST['showe'])? $_REQUEST['showe']:'').'"> ');
	echo('<input type="text" placeholder1="'.$TAGS['cl_FilterBonus'][0].'" title="'.$TAGS['cl_FilterBonus'][1].'" id="showb" name="showb" value="'.(isset($_REQUEST['showb'])? $_REQUEST['showb']:'').'"> ');
	
	$mybc = "<a href='claims.php'>".$TAGS['AdmClaims'][0]."</a>";
	if (!isset($_REQUEST['nobc']))
		pushBreadcrumb($mybc);
	emitBreadcrumbs();
	
	$decided = isset($_REQUEST['showd']) ? intval($_REQUEST['showd']) : $KONSTANTS['showAll'];
	echo('<select name="showd" style="font-size: small;" title="'.$TAGS['cl_showAllD'][1].'" onchange="document.getElementById(\'refreshlist\').click();"> ');
	echo('<option value="'.$KONSTANTS['showAll'].'" '.($decided==$KONSTANTS['showAll'] ? 'selected' : '').'>'.$TAGS['cl_showAllD'][0].'</option>');
	echo('<option value="'.$KONSTANTS['showOnly'].'" '.($decided==$KONSTANTS['showOnly'] ? 'selected' : '').'>'.$TAGS['cl_showOnlyD'][0].'</option>');
	echo('<option value="'.$KONSTANTS['showNot'].'" '.($decided==$KONSTANTS['showNot'] ? 'selected' : '').'>'.$TAGS['cl_showNotD'][0].'</option>');
	echo('</select> ');
	
	$applied = isset($_REQUEST['showa']) ? intval($_REQUEST['showa']) : $KONSTANTS['showAll'];
	echo('<select name="showa" style="font-size: small;" title="'.$TAGS['cl_showAllA'][1].'" onchange="document.getElementById(\'refreshlist\').click();"> ');
	echo('<option value="'.$KONSTANTS['showAll'].'" '.($applied==$KONSTANTS['showAll'] ? 'selected' : '').'>'.$TAGS['cl_showAllA'][0].'</option>');
	echo('<option value="'.$KONSTANTS['showOnly'].'" '.($applied==$KONSTANTS['showOnly'] ? 'selected' : '').'>'.$TAGS['cl_showOnlyA'][0].'</option>');
	echo('<option value="'.$KONSTANTS['showNot'].'" '.($applied==$KONSTANTS['showNot'] ? 'selected' : '').'>'.$TAGS['cl_showNotA'][0].'</option>');
	echo('</select> ');
	
	
	echo('<span title="'.$TAGS['cl_DDLabel'][1].'" style="font-size:small;">');
	echo('<label for="decisiondefault">'.$TAGS['cl_DDLabel'][0].'</label>');
	echo('<select id="decisiondefault" name="dd" style="font-size:small;"> ');
	echo('<option value="-1" '.(!isset($_REQUEST['dd']) || $_REQUEST['dd']=='-1'?'selected':'').'>'.$TAGS['BonusClaimUndecided'][0].'</option>');
	echo('<option value="0" '.(isset($_REQUEST['dd']) && $_REQUEST['dd']=='0'?'selected':'').'>'.$TAGS['BonusClaimOK'][0].'</option>');
	echo('</select> ');
	echo('<input type="date" style="font-size:small;" name="ddate" value="'.(isset($_REQUEST['ddate'])?$_REQUEST['ddate']:date('Y-m-d')).'"> ');
	echo('</span>');
	echo('<input onclick="document.getElementById(\'refreshc\').value=\'shownew\';" type="submit" title="'.$TAGS['cl_PostNewClaim'][1].'" value="'.$TAGS['cl_PostNewClaim'][0].'"> ');
	
	echo('</form>');
	echo('</div>');
	echo('<table><thead class="listhead">');
	echo('<tr><th>'.$TAGS['cl_EntrantHdr'][0].'</th><th>'.$TAGS['cl_BonusHdr'][0].'</th><th>'.$TAGS['cl_OdoHdr'][0].'<th>'.$TAGS['cl_ClaimedHdr'][0].'</th>');
	echo('<th>'.$TAGS['cl_DecisionHdr'][0].'</th>');
	if ($virtualrally) {
		echo('<th title="'.$TAGS['cl_PenaltyFuel'][1].'">'.$TAGS['cl_PenaltyFuel'][0].'</th>');
		echo('<th title="'.$TAGS['cl_PenaltySpeed'][1].'">'.$TAGS['cl_PenaltySpeed'][0].'</th>');
		echo('<th title="'.$TAGS['cl_PenaltyMagic'][1].'">'.$TAGS['cl_PenaltyMagic'][0].'</th>');
	}
	echo('<th>'.$TAGS['cl_AppliedHdr'][0].'</th><th>'.$TAGS['cl_LoggedHdr'][0].'</th></tr>');
	echo('<tbody>');
	while ($rd = $R->fetchArray()) {
		echo('<tr class="link" data-rowid="'.$rd['rowid'].'">');
		echo('<td title="'.$rd['EntrantID'].'" onclick="showCurrentClaim(this);" class="clickme">');
		$ename = getValueFromDB("SELECT RiderName FROM entrants WHERE EntrantID=".$rd['EntrantID'],"RiderName",$rd['EntrantID']);
		echo(htmlspecialchars($ename).' </td>');
		echo('<td title="');
		fetchBonusName($rd['BonusID']);
		echo('" onclick="showCurrentClaim(this);"> '.$rd['BonusID'].' </td>');
		echo('<td onclick="showCurrentClaim(this);"> '.$rd['OdoReading'].' </td>');
		echo('<td onclick="showCurrentClaim(this);"> '.logtime($rd['ClaimTime']).' </td>');
		if ($rd['Applied']==0) {
			$status = '<select onchange="updateClaimDecision(this);">';
			$status .= '<option value="-1"'.($rd['Decision']==$KONSTANTS['UNDECIDED_CLAIM']? ' selected' : '').'>'.$TAGS['BonusClaimUndecided'][0].'</option>';
			for ($i=0; $i<10; $i++)
				$status .= '<option value="'.$i.'"'.($rd['Decision']==$i? ' selected' :'').'>'.$decisions[$i].'</option>';
			$status .= '</select>';
		} else
			$status = ($rd['Decision'] != $KONSTANTS['UNDECIDED_CLAIM'] ? $decisions[$rd['Decision']] : $TAGS['BonusClaimUndecided'][0]);
		echo('<td> '.$status.' </td>');	
		if ($virtualrally) {
			echo('<td onclick="showCurrentClaim(this);" title="'.$TAGS['cl_PenaltyFuel'][1].'">'.($rd['FuelPenalty'] != 0 ? '&nbsp;*' : '').'</td>');
			echo('<td onclick="showCurrentClaim(this);" title="'.$TAGS['cl_PenaltySpeed'][1].'">'.($rd['SpeedPenalty'] != 0 ? '&nbsp;*' : '').'</td>');
			echo('<td onclick="showCurrentClaim(this);" title="'.$TAGS['cl_PenaltyMagic'][1].'">'.($rd['MagicPenalty'] != 0 ? '&nbsp;*' : '').'</td>');
		}
		echo('<td title="'.$TAGS['cl_Applied'][1].'" style="text-align:center;">'.'<input type="checkbox" '.($rd['Decision']==$KONSTANTS['UNDECIDED_CLAIM']? ' disabled ' : '').' onchange="updateClaimApplied(this);" value="1"'.($rd['Applied']!=0? ' checked' :'').'>');
		echo('<td onclick="showCurrentClaim(this);"> '.logtime($rd['LoggedAt']).'</td>');
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
	try {
		$dt = new DateTime($stamp);
		$dtf = $dt->format('D H:i');
	} catch (Exception $e) {
		$dtf = $stamp;
	}
	return '<span title="'.$stamp.'">'.$dtf.'</span>';
}

function parseTimeMins($tx)
/*
 * I accept a string containing a duration specification which
 * I return as an integral number of minutes. I expect hours and
 * minutes or just minutes, entered in a variety of formats.
 *
 */
{
	if (preg_match('/(\d*)[alpha|blank|:|.]+(\d*)/',$tx,$matches)) {
		$hh = $matches[1];
		$mm = $matches[2];
	} else {
		$hh = 0;
		$mm = intval($tx);
	}
//	echo('hh='.$hh.' mm='.$mm.'<br>');
	return $hh * 60 + $mm;
	
}

function saveClaim()
{
	global $DB,$TAGS,$KONSTANTS;
	
	//print_r($_REQUEST);
	
	$claimid = isset($_REQUEST['claimid']) ? intval($_REQUEST['claimid']) : 0;
	if ($claimid <= 0) {
		saveNewClaim();
		return;
	}
	
	$virtualrally = false;
	$virtualstopmins = 0;
	$R = $DB->query("SELECT * FROM rallyparams");
	if ($rd = $R->fetchArray()) {
		$virtualrally = ($rd['isvirtual'] != 0);
		$virtualstopmins = $rd['stopmins'];
	}
	$sql = "UPDATE claims SET ";
	$sql = '';
	if (isset($_REQUEST['ClaimDate']) && isset($_REQUEST['ClaimTime']))
		$sql .= ($sql==''? '' : ',')."ClaimTime='".joinDateTime($_REQUEST['ClaimDate'],$_REQUEST['ClaimTime'])."'";
	if (isset($_REQUEST['NextTimeMins']))
		$sql .= ($sql==''? '' : ',')."NextTimeMins=".parseTimeMins($_REQUEST['NextTimeMins']);
	foreach (['BonusID'] as $fld)
		if (isset($_REQUEST[$fld]))
			$sql .= ($sql==''? '' : ',').$fld."='".$DB->escapeString($_REQUEST[$fld])."'";
	foreach (['BCMethod','EntrantID','OdoReading','Decision','Applied','FuelBalance'] as $fld)
		if (isset($_REQUEST[$fld]))
			$sql .= ($sql==''? '' : ',').$fld."=".intval($_REQUEST[$fld]);
	//echo("[[ $sql ]]");

	if (isset($_REQUEST['MagicWord']))
		$sql .= ($sql==''? '' : ',')."MagicWord='".$DB->escapeString($_REQUEST['MagicWord'])."'";
	
	$XF = ['FuelBalance','SpeedPenalty','FuelPenalty','MagicPenalty'];
	if (isset($_REQUEST['NextTimeMins'])){
		$mins = parseTimeMins($_REQUEST['NextTimeMins']);
		$sql .= ($sql==''? '' : ',').'NextTimeMins='.$mins;
	}
	foreach($XF as $F)
		if (isset($_REQUEST[$F]))
			$sql .= ($sql==''? '' : ',').$F.'='.intval($_REQUEST[$F]);
		else
			$sql .= ($sql==''? '' : ',').$F.'=0';
	if ($sql=='')
		return;
	$sql.= " WHERE rowid=".$claimid;
	
	//echo('<br>'.$sql.'<br>');
	
	$DB->exec("UPDATE claims SET ".$sql);
	if ($DB->lastErrorCode()<>0) {
		echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
		exit;
	}
	
}

function saveNewClaim()
{
	global $DB,$TAGS,$KONSTANTS;
	
//	print_r($_REQUEST);
	
	$virtualrally = false;
	$virtualstopmins = 0;
	$R = $DB->query("SELECT * FROM rallyparams");
	if ($rd = $R->fetchArray()) {
		$virtualrally = ($rd['isvirtual'] != 0);
		$virtualstopmins = $rd['stopmins'];
	}
	$XF = ['FuelBalance','SpeedPenalty','FuelPenalty','MagicPenalty'];
	
	$sql = "INSERT INTO claims (LoggedAt,ClaimTime,BCMethod,EntrantID,BonusID,OdoReading,Decision,Applied";
	if (isset($_REQUEST['MagicWord'])) 
		$sql .= ",MagicWord";
	if (isset($_REQUEST['NextTimeMins'])) 
		$sql .= ",NextTimeMins";
	foreach ($XF as $F)
		if (isset($_REQUEST[$F]))
			$sql .= ",$F";
	$sql .= ") VALUES(";
	$dtn = new DateTime(now,new DateTimeZone($KONSTANTS['LocalTZ']));
	$datenow = $dtn->format('c');
	$la = (isset($_REQUEST['LoggedAt']) && $_REQUEST['LoggedAt'] != '' ? $_REQUEST['LoggedAt'] : $datenow);
	$sql .= "'".$la."'";
	$cd = (isset($_REQUEST['ClaimDate']) && $_REQUEST['ClaimDate'] != '' ? $_REQUEST['ClaimDate'] : date('Y-m-d'));
	$ct = (isset($_REQUEST['ClaimTime']) && $_REQUEST['ClaimTime'] != '' ? joinDateTime($cd,$_REQUEST['ClaimTime']) : $la);
	$sql .= ",'".$ct."'";
	$sql .= ",".(isset($_REQUEST['BCMethod']) ? $_REQUEST['BCMethod'] : $KONSTANTS['BCM_EBC']);
	$sql .= ",".intval($_REQUEST['EntrantID']);
	$sql .= ",'".$DB->escapeString($_REQUEST['BonusID'])."'";
	$sql .= ",".intval($_REQUEST['OdoReading']);
	$sql .= ",".(isset($_REQUEST['Decision']) ? $_REQUEST['Decision'] : 0);
	$sql .= ",".(isset($_REQUEST['Applied']) ? $_REQUEST['Applied'] : 0);
	
	if (isset($_REQUEST['MagicWord']))
		$sql .= ",'".$DB->escapeString($_REQUEST['MagicWord'])."'";
	
	if (isset($_REQUEST['NextTimeMins'])){
		$mins = parseTimeMins($_REQUEST['NextTimeMins']);
		if ($virtualrally)
			$mins += $virtualstopmins;
		$sql .= ",".$mins;
	}
	foreach($XF as $F)
		if (isset($_REQUEST[$F]))
			$sql .= ",".intval($_REQUEST[$F]);
	
	$sql .= ")";
	//echo('<br>'.$sql.'<br>');
	$DB->exec($sql);
	
}

function showClaim($claimid = 0)
{
	global $DB,$TAGS,$KONSTANTS;

	startHtml($TAGS['ttClaims'][0]);

	$R = $DB->query("SELECT StartTime,FinishTime FROM rallyparams");
	if ($rd = $R->fetchArray()) {
		echo('<input type="hidden" id="rallyStart" value="'.$rd['StartTime'].'">');
		echo('<input type="hidden" id="rallyFinish" value="'.$rd['FinishTime'].'">');
	}
	$virtualrally = false;
	$tankrange = 0;
	$refuelstops = 'NONE'; // re matching nothing
	$stopmins = 0;

	$sql = "SELECT * FROM rallyparams";
	$R = $DB->query($sql);
	if ($rd = $R->fetchArray()) 
		$virtualrally = ($rd['isvirtual'] != 0);
	if ($virtualrally) {
		$tankrange = $rd['tankrange'];
		$refuelstops = $rd['refuelstops'];
		$stopmins = $rd['stopmins'];
	}
	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" id="virtualrally" value="'.($virtualrally ? 1 : 0).'">');
	echo('<input type="hidden" id="tankrange" value="'.$rd['tankrange'].'">'); 
	echo('<input type="hidden" id="refuelstops" value="'.$rd['refuelstops'].'">');
	echo('<input type="hidden" id="stopmins" value="'.$rd['stopmins'].'">');

	if ($claimid == 0 && $virtualrally) {		//Only use magic words to validate new claims
		$sql = "SELECT * FROM magicwords ORDER BY asfrom";
		$R = $DB->query($sql);
		while ($rd = $R->fetchArray()) 
			echo('<input type="hidden" name="mw" data-asfrom="'.$rd['asfrom'].'" value="'.$rd['magic'].'">');		
	}
	if ($claimid > 0) {  //Check that this is actually a claim record and not called with random number
		$R  = $DB->query("SELECT * FROM claims WHERE rowid=".$claimid);
		if (!$rd = $R->fetchArray())
			$claimid =0;
	}
?>
<script>
function checkMagicWord() {
	let lmw = '';
	let lmwtime = '';
	let mwok = document.getElementById('mwok');
	mwok.innerHTML = '';
	let mw = document.getElementById('magicword').value;
	console.log('cmw={'+mw+'}');
	let ct = document.getElementById('ClaimDate').value+'T'+document.getElementById('ClaimTime').value;
	let wms = document.getElementsByName('mw');
	let n = 0;
	
	for (let i = wms.length - 1; i >= 0; i--) {
		console.log(ct+' '+wms[i].getAttribute('data-asfrom')+' '+wms[i].value);
		if (ct >= wms[i].getAttribute('data-asfrom')) { // in force at the time of this claim
			n++; // count the number of records in time
			if (mw.toLowerCase() == wms[i].value.toLowerCase() ) {
				console.log('Match found at '+i);
				if (n == 1)	{ // Most recent
					mwok.innerHTML = ' &checkmark; ';
					mwok.className = 'green';
					return true;		// all good
				}
				if (n == 2) {
					mwok.innerHTML = ' &checkmark; &nbsp;&nbsp;\''+lmw+ '\' >= '+lmwtime+' '+'<input type="checkbox" value="1" name="MagicPenalty">';
					mwok.className = 'yellow';
					return false;
				}
			}
		}
		
		lmw = wms[i].value;
		lmwtime = wms[i].getAttribute('data-asfrom');
	}
	mwok.innerHTML = ' &cross;  <input type="checkbox" value="1" name="MagicPenalty" checked>';
	mwok.className = 'red';
	return false;
}
function checkBonusFuel(str,fuelAnyway) {
	if (!fuelAnyway) {
		console.log('Checking bonus fuel ... '+str);
		let rs = document.getElementById('refuelstops');
		if (!rs)
			return false;
		let rx = new RegExp(rs.value);
		rx.ignoreCase = true;
		console.log("Testing "+rx.source);
		if (!rx.test(str)) 
			return false;
		console.log(rs.value+' true');
	}
	// This bonus is a refuel stop!
	
	let fb = document.getElementById('FuelBalance');
	fb.value = document.getElementById('tankrange').value;
	fb.setAttribute('title',fb.value);
	return true;

}

function checkDateTime() {

	let cdt = document.getElementById('ClaimDate');
	let ctm = document.getElementById('ClaimTime');
	console.log('cdt testing {'+ctm.value+'}');
	if (ctm.value == '')
		return;
	console.log('Checking claim date ... ');
	let rs = document.getElementById('rallyStart');
	let rf = document.getElementById('rallyFinish');
	let lc = document.getElementById('lastClaimTime');
	let ct = cdt.value+'T'+ctm.value;
	console.log(rs.value+'  '+rf.value+' '+lc.value+' '+ct);
	let ok = (!lc || ct > lc.value) && (!rs || ct >= rs.value) && (!rf || ct <= rf.value);
	console.log('Decided it was ok = '+ok);
	if (ok) {
		cdt.classList.remove('yellow');
		ctm.classList.remove('yellow');
	} else {
		cdt.classList.add('yellow');
		ctm.classList.add('yellow');
	}
}

function addMins(dt,m) {
	let tm = Date.parse(dt);
	tm = tm + m * 60 * 1000;
	return new Date(tm);
}
function formatDatetime(dt) {

	let yy = dt.getFullYear();
	let mm = dt.getMonth() + 1;
	let dd = dt.getDate();
	let hh = dt.getHours();
	let nn = dt.getMinutes();
	
	let edate = '' + yy + "-";
	edate = edate + (mm < 10 ? '0' : '') + mm + '-';
	edate = edate + (dd < 10 ? '0' : '') + dd + 'T';
	edate = edate + (hh < 10 ? '0' : '') + hh + ':';
	edate = edate + (nn < 10 ? '0' : '') + nn;
	return edate;
	
}

function checkSpeeding() {

	checkDateTime();  // Is the current claim time reasonable ?
	
	let claimid = parseInt(document.getElementById('claimid').value);
	console.log('Claimid='+claimid);
	if (claimid > 0)
		return;
	
	console.log('Checking speed');
	let speedok = document.getElementById('SpeedWarning');
	if (speedok) {
		speedok.style.display = 'none';
		speedok.innerHTML = '';
	}
	console.log('Warning cleared');
	let lct = document.getElementById('lastClaimTime').value;
	if (lct=='')
		return;
	console.log('Fetched last claim: t='+lct);
	let ct = document.getElementById('ClaimDate').value+'T'+document.getElementById('ClaimTime').value;
	let nm = document.getElementById('lastNextMins').value;
	let ed = addMins(lct,nm);
	let edate = formatDatetime(ed);
	console.log('ct='+ct);
	console.log('earliest+'+nm+'='+ed+' == '+edate+' ??? '+(ct < edate));
	if (speedok && ct < edate) {
		console.log('OMG!');
		let tickspeed = ' <input type="checkbox" value="1" name="SpeedPenalty" checked>';
		speedok.innerHTML = ' < ' + edate.replace('T',' ') + tickspeed;
		speedok.style.display = 'inline';
	}
}

	
function odoChanged(odo,emptyok) {

	console.log('Odo has changed  '+emptyok);
	let lastodo = 0;
	let vr = document.getElementById('virtualrally').value != 0;
	if (!vr)
		return;
	let fb = document.getElementById('FuelBalance');
	if (!fb)
		return;
	else
		fbd = parseInt(fb.getAttribute('data-value'));
	try {
		lastodo = parseInt(document.getElementById('lastOdoReading').value);
	}catch(e){
	}
	let thisleg = (odo > lastodo ? odo - lastodo : 0);
	let odor = document.getElementById('OdoReading');
	if (odo < lastodo)
		odor.classList.add('yellow');
	else
		odor.classList.remove('yellow');
	
	let fw = document.getElementById('FuelWarning');
	let tick = document.getElementById('TickFW');

	console.log('thisleg='+thisleg+' fbd='+fbd);
	if (thisleg <= fbd) {		// enough fuel
		tick.checked = false;
		fw.style.display = "none";
		if (emptyok) {
			fb.value = fbd - thisleg;
			fb.setAttribute('title',fb.value);
			console.log('New fb is '+fb.value);
			checkBonusFuel(document.getElementById('BonusID').value,false);
			console.log('After checking bonus = '+fb.value);
		}
	} else if (emptyok) {		// don't care
		checkBonusFuel(document.getElementById('BonusID').value,true)
		console.log('EmptyOk/cbf='+fb.value+'; fbd='+fbd+' thisleg='+thisleg);
		fb.value = fb.value + (fbd - thisleg);
		console.log('and now '+fb.value);
		fb.setAttribute('title',fb.value);
		//alert('EmptyOk ?? ');
		
	} else {
		fw.style.display = 'inline';
		fw.setAttribute('title',(fbd-thisleg));
		if (tick)
			tick.checked = true;
	}
		
}

function showBonus(obj) {
	let str = obj.value
	let xhttp;
	if (str == "") {
		obj.parentNode.classList.remove('yellow');
		document.getElementById("BonusName").innerHTML = "";
		return;
	}
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("BonusName").innerHTML = this.responseText;
			if (this.responseText.trim().startsWith('*'))
				obj.parentNode.classList.add('yellow');
			else
				obj.parentNode.classList.remove('yellow');
		}
	};
	xhttp.open("GET", "claims.php?c=bondes&b="+str, true);
	xhttp.send();
}

function showEntrant(obj) {
	let str = obj.value;
	let xhttp;
	if (str == "") {
		obj.parentNode.classList.remove('yellow');
		document.getElementById("EntrantName").innerHTML = "";
		return;
	}
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("EntrantName").innerHTML = this.responseText;
			console.log('{ '+this.responseText+' }');
			if (this.responseText.trim().startsWith('*'))
				obj.parentNode.classList.add('yellow');
			else
				obj.parentNode.classList.remove('yellow');
			let lastodo = document.getElementById('lastOdoReading');
			let thisodo = document.getElementById('OdoReading');
			if (lastodo && thisodo)
				thisodo.setAttribute('placeholder',lastodo.value);
		}
	};
	xhttp.open("GET", "claims.php?c=entnam&e="+str, true);
	xhttp.send();
}

function validateClaim() {
	
	console.log('Validating form ... ');
	if (document.getElementById('EntrantID').value == '') {
		document.getElementById('EntrantID').focus();
		return false;
	}
	if (document.getElementById('BonusID').value == '') {
		document.getElementById('BonusID').focus();
		return false;
	}
	if (document.getElementById('OdoReading').value == '') {
		document.getElementById('OdoReading').focus();
		return false;
	}
	if (document.getElementById('ClaimTime').value == '') {
		document.getElementById('ClaimTime').focus();
		return false;
	}
	let claimid = parseInt(document.getElementById('claimid').value);
	console.log('Claimid='+claimid);
	//alert('Check1');
	if (claimid > 0)
		return true;
	
	let ntm = document.getElementById('NextTimeMins');
	if (ntm && ntm.value == '') {
		ntm.focus();
		return false;
	}
	let mw = document.getElementById('magicword');
	if (mw && mw.value == '') {
		let wms = document.getElementsByName('mw');
		if (wms.length > 0) {
			mw.focus();
			return false;
		}
	}
	let odo = document.getElementById('OdoReading').value;
	//alert('Check2');
	odoChanged(odo,true);
	//alert('Returning true');
	let fb = document.getElementById('FuelBalance');
	if (fb) {
		console.log('saving fb of '+fb.value);
		document.getElementById('saveFuelBalance').value = fb.value;
	}

	
	return true;
}
</script>
<?php	
	echo('<form method="post" action="claims.php" onsubmit="return validateClaim();">');
	echo('<input type="hidden" name="c" value="newclaim">');
	echo('<input type="hidden" name="nobc" value="1">');
	echo('<input type="hidden" name="LoggedAt" value="">');
	echo('<input type="hidden" name="BCMethod" value="'.$KONSTANTS['BCM_EBC'].'">');
	echo('<input type="hidden" name="Applied" value="0">');
	echo('<input type="hidden" id="claimid" name="claimid" value="'.$claimid.'">');
	echo('<input type="hidden" name="dd" value="'.(isset($_REQUEST['dd']) ? $_REQUEST['dd'] : '-1').'">');
	echo('<input type="hidden" name="showa" value="'.(isset($_REQUEST['showa']) ? $_REQUEST['showa'] : $KONSTANTS['showAll']).'">');
	echo('<input type="hidden" name="showd" value="'.(isset($_REQUEST['showd']) ? $_REQUEST['showd'] : $KONSTANTS['showAll']).'">');
	echo('<input type="hidden" name="ddate" value="'.(isset($_REQUEST['ddate'])?$_REQUEST['ddate']:date('Y-m-d')).'">');
	if (isset($_REQUEST['showe']))
		echo('<input type="hidden" name="showe" value="'.$_REQUEST['showe'].'">');
	if (isset($_REQUEST['showb']))
		echo('<input type="hidden" name="showb" value="'.$_REQUEST['showb'].'">');
	echo('<input type="hidden" name="FuelBalance" id="saveFuelBalance" value="'.($claimid > 0 ? $rd['FuelBalance'] : 0 ).'">');
	
	echo('<div class="frmContent" style="max-width: 45em;">');
	
	echo('<span class="vlabel" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].'</label> ');
	echo('<input autofocus type="number" name="EntrantID" id="EntrantID" tabindex="1" oninput="enableSaveButton();" onchange="showEntrant(this);"');
	echo(' value="'.($claimid > 0 ? $rd['EntrantID'] : '').'"> ');
	echo('<span id="EntrantName" style="display:inline-block; vertical-align: top;">');
	if ($claimid > 0)
		fetchEntrantDetail($rd['EntrantID']);
	echo('</span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['BonusIDLit'][1].'"><label for="BonusID">'.$TAGS['BonusIDLit'][0].'</label> ');
	echo('<input type="text" name="BonusID" id="BonusID" tabindex="2" oninput="enableSaveButton();" onchange="showBonus(this);"');
	echo(' value="'.($claimid> 0 ? $rd['BonusID'] : '').'"> ');
	echo('<span id="BonusName">');
	if ($claimid > 0)
		fetchBonusName($rd['BonusID']);
	echo('</span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['OdoReadingLit'][1].'"><label for="OdoReading">'.$TAGS['OdoReadingLit'][0].'</label> ');
	echo('<input type="number" class="bignumber" name="OdoReading" oninput="enableSaveButton();" id="OdoReading"');
	if ($claimid==0)
		echo(' onchange="odoChanged(this.value,false);"');
	echo(' tabindex="3"');
	echo(' value="'.($claimid > 0 ? $rd['OdoReading'] : '').'"> ');
	$ck = ($claimid > 0 && $rd['FuelPenalty'] != 0 ? ' checked ' : '');
	$ds = ($claimid > 0 && $rd['FuelPenalty'] != 0 ? 'inline' : 'none');
	echo('<span id="FuelWarning" style="display:'.$ds.';" title="'.$TAGS['FuelWarning'][1].'"> ');
	$fw = $TAGS['FuelWarning'][0].' <input type="checkbox" name="FuelPenalty" value="1"  oninput="enableSaveButton();" id="TickFW" '.$ck.'>';
	echo($fw);
	echo(' </span>');
	echo('</span> ');

	$ct = splitDatetime($claimid > 0 ? $rd['ClaimTime'] : (isset($_REQUEST['ddate'])?$_REQUEST['ddate']:date('Y-m-d')).'T ');
	echo('<span class="vlabel" title="'.$TAGS['BonusClaimTime'][1].'"><label for="ClaimTime">'.$TAGS['BonusClaimTime'][0].'</label> ');
	$oc = ($claimid == 0 ? ' onchange="checkSpeeding();"' : '');
	echo('<input type="date" name="ClaimDate" id="ClaimDate" value="'.$ct[0].'" oninput="enableSaveButton();" tabindex="8"'.$oc.'> ');
	echo('<input type="time" name="ClaimTime" id="ClaimTime" value="'.trim($ct[1]).'" oninput="enableSaveButton();" tabindex="4"'.$oc.'> ');
	$ds = ($claimid > 0 && $rd['SpeedPenalty'] != 0 ? 'inline' : 'none');
	$oc = ($claimid > 0 && $rd['SpeedPenalty'] != 0 ? '&dzigrarr; <input type="checkbox" value="1" name="SpeedPenalty" oninput="enableSaveButton();" checked>' : '');
	echo('<span id="SpeedWarning" style="display:'.$ds.';">'.$oc.'</span>');
	echo('</span>');
	
	if ($virtualrally) {
		echo('<span class="vlabel" title="'.$TAGS['NextTimeMins'][1].'"><label for="NextTimeMins">'.$TAGS['NextTimeMins'][0].'</label> ');
		echo('<input type="text" class="bignumber" name="NextTimeMins" id="NextTimeMins" value="'.($claimid>0?showTimeMins($rd['NextTimeMins']):'').'" oninput="enableSaveButton();" tabindex="5">');
		echo('</span>');
		
		echo('<span class="vlabel" id="magicspan" title="'.$TAGS['magicword'][1].'">');
		echo('<label for="magicword">'.$TAGS['magicword'][0].'</label> ');
		$oc =  ($claimid == 0 ? ' oninput="checkMagicWord();"' : '');
		echo('<input type="text" style="width:10em;" id="magicword" name="MagicWord"'.$oc.' value="'.($claimid > 0 ? $rd['MagicWord'] : '').'" oninput="enableSaveButton();" tabindex="5"> ');
		$oc = ($claimid > 0 && $rd['MagicPenalty'] != 0 ? ' &cross;  <input type="checkbox" value="1" name="MagicPenalty" checked>' : '');
		$cl = ($oc != '' ? ' class="yellow"' : '');
		echo('<span id="mwok"'.$cl.'>'.$oc.'</span>');
		echo('</span>');
	}
	echo('<span class="vlabel" title="'.$TAGS['BonusClaimDecision'][1].'"><label for="Decision">'.$TAGS['BonusClaimDecision'][0].'</label> ');
	echo('<select name="Decision" id="Decision" tabindex="5" style="font-size:smaller;" oninput="enableSaveButton();">');
	//$dnum = $claimid > 0 ? ($rd['Judged']!=0 ? $rd['Decision'] : -1) : (isset($_REQUEST['dd']) && $_REQUEST['dd']=='0' ? 0 : -1);
	$dnum = $claimid > 0 ? $rd['Decision'] : (isset($_REQUEST['dd']) ? $_REQUEST['dd'] : $KONSTANTS['UNDECIDED_CLAIM']);
	echo('<option value="-1" '.($dnum==-1 ? 'selected' : '').'>'.$TAGS['BonusClaimUndecided'][0].'</option>');
	echo('<option value="0" '.($dnum==0 ? 'selected' : '').'>'.$TAGS['BonusClaimOK'][0].'</option>');
	$rr = explode("\n",str_replace("\r","",getValueFromDB("SELECT RejectReasons FROM rallyparams","RejectReasons","1=1")));
	foreach($rr as $rt) {
		$rtt = explode('=',$rt);
		if ($rtt[0]!='')
			echo("\r\n".'<option value="'.$rtt[0].'" '.($dnum==$rtt[0] ? 'selected' : '').'>'.$rtt[1].'</option>');
	}
	echo('</select></span>');

	echo('</div>'); // frmContent

	echo('<span class="vlabel" title="'.'"><label for="savedata">'.''.'</label>');
	echo('<input  disabled type="submit" id="savedata" data-altvalue="'.$TAGS['SaveRecord'][1].'" title="'.$TAGS['SaveRecord'][1].'" name="saveclaim" id="savedata"');
	echo(' onclick="'."this.setAttribute('data-triggered','1');".'"');
	echo(' data-triggered="0" value="'.$TAGS['SaveRecord'][0].'" tabindex="7"> ');
	
	if ($claimid > 0) {
		echo(' &nbsp;&nbsp;<input type="checkbox" id="ReallyDelete" onchange="document.getElementById(\'deletebutton\').disabled=!this.checked;"> ');
		echo('<input disabled onclick="return document.getElementById(\'ReallyDelete\').checked;" type="submit" title="'.$TAGS['DeleteClaim'][1].'" name="deleteclaim" id="deletebutton" value="'.$TAGS['DeleteClaim'][0].'" tabindex="9"></span> ');
	}
	echo('</span>');
	echo('</form>');
	echo('</body></html>');
}

function showNewClaim()
{
	showClaim(0);
}

function showTimeMins($mins)
{
	$h = intval($mins / 60);
	$m = $mins % 60;
	$res = ($h > 0 ? ''.$h.'h ' : '');
	$res .= ($m > 0 ? ''.$m.'m' : '');
	return $res;
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
	$sql = "UPDATE claims SET Decision=".$_REQUEST['val'];
	$sql .= " WHERE rowid=".$_REQUEST['claim'];
	$sql .= " AND Applied=0";
	echo($DB->exec($sql) && $DB->changes()==1? 'ok' : 'error');
}


function fetchEntrantDetail($e)
{
	global $DB,$TAGS,$KONSTANTS;
	
	if ($e=='') {
		echo('');
		return;
	}
	$virtualrally = getValueFromDB("SELECT isvirtual FROM rallyparams","isvirtual",0);
	$R = $DB->query("SELECT * FROM entrants WHERE EntrantID=".$e);
	if ($rd = $R->fetchArray()) {
		echo($rd['RiderName']);

		$tankrange = getValueFromDB("SELECT tankrange FROM rallyparams","tankrange",0);
		
		$sql = "SELECT * FROM claims WHERE EntrantID=".$e;
		$sql .= " ORDER BY ClaimTime DESC";
		$R = $DB->query($sql);
		$lastodo = 0;
		$lastct = '';
		$nextmins = 0;
		if (($rd = $R->fetchArray()) && isset($rd['FuelBalance'])) {
			$fuelbalance = $rd['FuelBalance'];
			$lastodo = $rd['OdoReading'];
			$lastct = $rd['ClaimTime'];
			$nextmins = $rd['NextTimeMins'];
		} else
			$fuelbalance = $tankrange;
			
		if ($virtualrally) {
			$lo = $tankrange * .10;
			$hi = $tankrange * .90; 
			//echo('<br>');
			echo('  <label for="FuelBalance">'.$TAGS['FuelBalance'][0].'</label> ');
			echo('  <meter title="'.$fuelbalance.'" id="FuelBalance" low="'.$lo.'" high="'.$hi.'"');
			echo('min="0" max="'.$tankrange.'" data-value="'.$fuelbalance.'" value="'.$fuelbalance.'"> ['.$fuelbalance.']</meter>');
			
		}
		
		echo('<input type="hidden" id="lastOdoReading" value="'.$lastodo.'">');
		echo('<input type="hidden" id="lastClaimTime" value="'.$lastct.'">');
		echo('<input type="hidden" id="lastNextMins" value="'.$nextmins.'">');	
	} else {
		echo('***');
	}

}


function extractClaims()
// One-off for no ride rally April 2020
{
	global $DB,$TAGS,$KONSTANTS;

	echo('Extracting claims ... ');
	$R = $DB->query("SELECT EntrantID,BonusesVisited FROM entrants");
	$DB->exec('BEGIN TRANSACTION');
	while($rd = $R->fetchArray()) {
		$B = explode(',',$rd['BonusesVisited']);
		foreach($B as $bns) {
			if ($bns != '')
				$DB->exec("INSERT INTO eclaims(EntrantID,BonusID) VALUES(".$rd['EntrantID'].",'".$bns."')");
		}
	}
	$DB->exec('COMMIT');
	echo('done');

}
function applyClaimsForm()
{
	global $TAGS;
	
	startHtml($TAGS['cl_ClaimsTitle'][0]);
	echo('<h3>'.$TAGS['cl_ApplyHdr'][0].'</h3>');
	echo('<p>'.$TAGS['cl_ApplyHdr'][1].'</p>');
	pushBreadcrumb('#');
	emitBreadcrumbs();
	echo('<form action="claims.php" method="post">');
	echo('<input type="hidden" name="c" value="applyclaims">');
	echo('<span class="vlabel" title="'.$TAGS['cl_DateFrom'][1].'"><label for="lodate">'.$TAGS['cl_DateFrom'][0].'</label> <input type="date" id="lodate" name="lodate" value="'.date("Y-m-d").'"></span>');
	echo('<span class="vlabel" title="'.$TAGS['cl_DateTo'][1].'"><label for="hidate">'.$TAGS['cl_DateTo'][0].'</label> <input type="date" id="hidate" name="hidate" value="'.date("Y-m-d").'"></span>');
	echo('<span class="vlabel" title="'.$TAGS['cl_TimeFrom'][1].'"><label for="lotime">'.$TAGS['cl_TimeFrom'][0].'</label> <input type="time" id="lotime" name="lotime" value="00:00"></span>');
	echo('<span class="vlabel" title="'.$TAGS['cl_TimeTo'][1].'"><label for="hitime">'.$TAGS['cl_TimeTo'][0].'</label> <input type="time" id="hitime" name="hitime" value="23:59"></span>');
	echo('<input type="hidden" name="decisions" value="0">'); // Only process good claims
	echo('<span class="vlabel"><label for="gobutton"></label> <input id="gobutton" type="submit" value="'.$TAGS['cl_Go'][0].'"></span>');
	echo('</form>');
}

function applyClaims()
// One-off for no ride rally April 2020
// and again May 2020
{
	global $DB,$TAGS,$KONSTANTS;

	if (!isset($_REQUEST['lodate']) || !isset($_REQUEST['hidate']) ||
		!isset($_REQUEST['lotime']) || !isset($_REQUEST['hitime']) ||
		!isset($_REQUEST['decisions']) ) {								// Should only be used for Decision=0=Good Claim
		applyClaimsForm();
		//echo('<hr>NOT ENOUGH PARAMETERS<hr>');
		exit;
	}
	
	startHtml($TAGS['cl_ClaimsTitle'][0]);
	emitBreadcrumbs();
	echo('<h3>'.$TAGS['cl_Applying'][0].'</h3>');
	$sql = "SELECT claims.*,bonuses.BriefDesc FROM claims JOIN bonuses ON claims.BonusID=bonuses.BonusID WHERE ";
	
	// Because of the link to bonuses, only ordinary bonus claims will be processed here.
	// Claims for specials, combos or non-existent bonuses must be handled by hand.
	
	$loclaimtime = joinDateTime($_REQUEST['lodate'],$_REQUEST['lotime']);
	$hiclaimtime = joinDateTime($_REQUEST['hidate'],$_REQUEST['hitime']);
	
	$sqlW = "Applied=0 AND ";		// Not already applied
	
	$sqlW .= "ClaimTime>='".$loclaimtime."' AND ";
	$sqlW .= "ClaimTime<='".$hiclaimtime."' AND ";
	$sqlW .= "Decision IN (".$_REQUEST['decisions'].") ";
	
	$sqlW .= " AND SpeedPenalty=0 AND FuelPenalty=0 AND MagicPenalty=0"; // Penalties applied by hand
	
	if (isset($_REQUEST['entrants']))
		$sqlW .= " AND EntrantID IN (".$_REQUEST['entrants'].")";
	if (isset($_REQUEST['bonuses']))
		$sqlW .= " AND BonusID IN (".$_REQUEST['bonuses'].")";
	if (isset($_REQUEST['exclude']))
		$sqlW .= " AND BonusID NOT IN (".$_REQUEST['exclude'].")";
	$sql .= $sqlW;
	$sql .= " ORDER BY ClaimTime";
	
	echo('<div style="font-size: small;">');
	echo($sql.'<hr><br></div>');
	// Load all claims records into memory
	// organised as EntrantID, BonusID
	$claims = [];
	$R = $DB->query($sql);
	while ($rd = $R->fetchArray()){
		if (!isset($claims[$rd['EntrantID']])) 
			$claims[$rd['EntrantID']] = [];
		if (!isset($claims[$rd['BonusID']]))
			$claims[$rd['EntrantID']][$rd['BonusID']] = [str_replace(' ','T',$rd['ClaimTime']),$rd['OdoReading']]; // If ClaimTime was date time, make it dateTtime
	}
	//print_r($claims);
	//return;
	
	$DB->exec('BEGIN TRANSACTION');
	foreach($claims as $entrant => $bonuses) {
		
		$sql = "SELECT IfNull(FinishTime,'2020-01-01') As FinishTime,IfNull(OdoRallyFinish,0) As OdoRallyFinish,BonusesVisited";
		$sql .= ",IfNull(OdoRallyStart,0) As OdoRallyStart,IfNull(OdoScaleFactor,1) As OdoScaleFactor";
		$sql .= ",IfNull(CorrectedMiles,0) As CorrectedMiles,OdoKms";
		$sql .= " FROM entrants WHERE EntrantID=".$entrant;
		$R = $DB->query($sql);
		if (!$rd = $R->fetchArray())
			continue;

		$ft = $rd['FinishTime'];
		$fo = $rd['OdoRallyFinish'];
		$cm = $rd['CorrectedMiles'];
		
		$bv = explode(',',$rd['BonusesVisited']);
		foreach($bonuses as $bonus => $stats)  {
			
			if ($stats[0] > $ft)
				$ft = $stats[0];	// Straight compare of dateTtime
			if ($stats[1] > $fo) {
				$fo = $stats[1];
				$cm = calcCorrectedMiles($rd['OdoKms'],$rd['OdoRallyStart'],$fo,$rd['OdoScaleFactor']);
			}
			if (!in_array($bonus,$bv)) {
				array_push($bv,$bonus);
			}
		}
		//print_r($bv);
		$sql = "UPDATE entrants SET BonusesVisited='".implode(',',$bv)."', Confirmed=".$KONSTANTS['ScorecardIsDirty'];
		$sql .= ",FinishTime='".$ft."',OdoRallyFinish=".$fo;
		$sql .= ",CorrectedMiles=".$cm;
		$sql .= " WHERE EntrantID=$entrant";
		echo('<div style="font-size: small;">');
		echo($sql.'<br>');
		$DB->exec($sql);
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			$DB->exec('ROLLBACK');
			exit;
		}
		$sql = "UPDATE claims SET Applied=1 WHERE ";
		$sql .= "EntrantID=$entrant AND ";
		$sql .= $sqlW;
		echo($sql.'<hr><br></div>');
		$DB->exec($sql);
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			$DB->exec('ROLLBACK');
			exit;
		}

	}
	$DB->exec('COMMIT');
	echo('<br>');
	echo($TAGS['cl_Complete'][0]);
}

function saveMagicWords()
{
	global $DB,$TAGS,$KONSTANTS;
	
	$arr = $_REQUEST['id'];
	$DB->exec('BEGIN');
	$DB->exec('DELETE FROM magicwords');
	for ($i = 0; $i < count($arr); $i++) {
		if ($_REQUEST['magic'][$i] != '') {
			$sql = "INSERT INTO magicwords(asfrom,magic) VALUES(";
			$sql .= "'".$_REQUEST['asfromdate'][$i].' '.$_REQUEST['asfromtime'][$i]."'";
			$sql .= ",'".$DB->escapeString($_REQUEST['magic'][$i])."')";
//			echo($sql.'<hr>');
			$DB->exec($sql);
			if ($DB->lastErrorCode() <> 0)
				echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		}
	}
	$DB->exec('COMMIT');
}

function magicWords()
{
	global $DB,$TAGS,$KONSTANTS;


?>
<script>
function deleteRow(e) {
    e = e || window.event;
    let target = e.target || e.srcElement;	
	document.querySelector('#magicwords').deleteRow(target.parentNode.parentNode.rowIndex);
	enableSaveButton();
}
function triggerNewRow(obj) {
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('magicwords').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	obj.onchange = '';
}
</script>
<?php	

	startHtml($TAGS['ttMagicWords'][0]);
	
	echo('<form method="post" action="claims.php">');

	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" name="c" value="magic">');
	echo('<input type="hidden" name="menu" value="setup">');
	echo('<p>'.htmlentities($TAGS['AdmMagicWords'][1]).'</p>');
	echo('<table id="magicwords">');
	echo('<thead><tr><th>'.$TAGS['mw_AsFrom'][0].'</th><th>'.$TAGS['mw_Word'][0].'</th>');
	echo('<th></th>');
	echo('</tr>');
	echo('</thead><tbody>');
	
	$R = $DB->query('SELECT rowid AS id,asfrom,magic FROM magicwords ORDER BY asfrom');
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	
	while ($rd = $R->fetchArray())
	{
		echo("\n".'<tr class="hoverlite">');
		echo('<td><input type="hidden" name="id[]" value="'.$rd['id'].'">');
		$afdt = explode(' ',$rd['asfrom']);
		echo('<input type="date" name="asfromdate[]" value="'.$afdt[0].'" onchange="enableSaveButton();"> ');
		echo('<input type="time" name="asfromtime[]" value="'.$afdt[1].'" onchange="enableSaveButton();"></td>');
		echo('<td><input type="text" name="magic[]" value="'.$rd['magic'].'" onchange="enableSaveButton();"></td>');
		echo('<td class="center"><button value="-" onclick="deleteRow(event);return false;">-</button></td>');
		echo('</tr>');
	}	
	echo('<tr class="newrow hide"><td><input type="hidden" name="id[]" value="">');
	echo('<input class="date" type="date" name="asfromdate[]" value="" onchange="enableSaveButton();"> ');
	echo('<input class="time" type="time" name="asfromtime[]" value="" onchange="enableSaveButton();"></td>');
	echo('<td ><input class="text" type="text" name="magic[]" value="" onchange="enableSaveButton();"> ');
	echo('<td class="center"><button value="-" onclick="deleteRow(event);return false;">-</button></td>');
	echo('</tr>');
	echo('</tbody></table>');
	echo('<button value="+" onclick="triggerNewRow(this);return false;">+</button><br>');
	
	echo('<input type="submit" class="noprint" title="'.$TAGS['SaveSettings'][1].'" id="savedata" data-triggered="0" onclick="'."this.setAttribute('data-triggered','1')".'" disabled accesskey="S" name="savemw" data-altvalue="'.$TAGS['SaveSettings'][0].'" value="'.$TAGS['SettingsSaved'][0].'" /> ');
	echo('</form>');
	//showFooter();
	
}

if (isset($_REQUEST['deleteclaim']) && isset($_REQUEST['claimid']) && $_REQUEST['claimid']>0) {
	deleteClaim();
	if (retraceBreadcrumb())
		;
	listclaims();
	exit;
}
//print_r($_REQUEST);
if (isset($_REQUEST['savemw'])) {
	saveMagicWords();
	if (retraceBreadcrumb())
		;
}	
if (isset($_REQUEST['saveclaim'])) {
	saveClaim();
	if (isset($_REQUEST['nobc']))
		unset($_REQUEST['nobc']);
	if (retraceBreadcrumb());
}	
if (isset($_REQUEST['c'])) {
	
	if ($_REQUEST['c']=='extractclaims') {
		extractClaims();
		exit;
	}
	
	if ($_REQUEST['c']=='applyclaims') {
		applyClaims();
		exit;
	}
	
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
		fetchEntrantDetail($e);
		exit;
	}
	if ($_REQUEST['c']=='bondes') {
		$b = (isset($_REQUEST['b']) ? $_REQUEST['b'] : '');
		fetchBonusName($b);
		exit;
	}
	
	if ($_REQUEST['c']=='magic') {
		magicWords();
		exit;
	}
	
	// If dropped through then just list the buggers.
//	echo('dropped through<br>');
	listclaims();
}
else
	listclaims();

?>
