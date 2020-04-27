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
	
	$virtualrally = getValueFromDB("SELECT isvirtual FROM virtualrally","isvirtual",0) != 0;
	
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
	$sql = "SELECT Count(*) AS rex FROM claims";
	$sqlw = '';
	if (isset($_REQUEST['entrantid']) || isset($_REQUEST['bonusid']) || isset($_REQUEST['showd']) || isset($_REQUEST['showa']) ) {
		if (isset($_REQUEST['entrantid']) && $_REQUEST['entrantid']!='')
			$sqlw .= "EntrantID=".$_REQUEST['entrantid'];
		if (isset($_REQUEST['bonusid']) && $_REQUEST['bonusid']!='')
			$sqlw .= ($sqlw != ''? ' AND ' : '')."Bonusid='".$DB->escapeString($_REQUEST['bonusid'])."'";
		if (isset($_REQUEST['showd']) && $_REQUEST['showd']!=$showAll)
			$sqlw .= ($sqlw != ''? ' AND ' : '').'Judged'.($_REQUEST['showd']==$showNot ? '=0': '<>0');
		if (isset($_REQUEST['showa']) && $_REQUEST['showa']!=$showAll)
			$sqlw .= ($sqlw != ''? ' AND ' : '').'Applied'.($_REQUEST['showa']==$showNot ? '=0': '<>0');
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
	//echo($sql);
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	startHtml("claims");
	emitClaimsJS();
	echo('<div>');
	echo('<form method="get" action="claims.php">');
	echo($rex.' ');
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
	echo('<input type="date" style="font-size:small;" name="ddate" value="'.(isset($_REQUEST['ddate'])?$_REQUEST['ddate']:date('Y-m-d')).'"> ');
	echo('</span>');
	echo('<input onclick="document.getElementById(\'refreshc\').value=\'shownew\';" type="submit" title="'.$TAGS['cl_PostNewClaim'][1].'" value="'.$TAGS['cl_PostNewClaim'][0].'"> ');
	
	echo('</form>');
	echo('</div>');
	echo('<table><thead class="listhead">');
	echo('<tr><th>'.$TAGS['cl_EntrantHdr'][0].'</th><th>'.$TAGS['cl_BonusHdr'][0].'</th><th>'.$TAGS['cl_OdoHdr'][0].'<th>'.$TAGS['cl_ClaimedHdr'][0].'</th>');
	echo('<th>'.$TAGS['cl_DecisionHdr'][0].'</th>');
	if ($virtualrally) {
		echo('<th>F</th><th>S</th><th>M</th>');
	}
	echo('<th>'.$TAGS['cl_AppliedHdr'][0].'</th><th>'.$TAGS['cl_LoggedHdr'][0].'</th></tr>');
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
		if ($virtualrally) {
			echo('<td>'.($rd['FuelPenalty'] != 0 ? '*' : '').'</td>');
			echo('<td>'.($rd['SpeedPenalty'] != 0 ? '*' : '').'</td>');
			echo('<td>'.($rd['MagicPenalty'] != 0 ? '*' : '').'</td>');
		}
		echo('<td title="'.$TAGS['cl_Applied'][1].'" style="text-align:center;">'.'<input type="checkbox" onchange="updateClaimApplied(this);" value="1"'.($rd['Applied']!=0? ' checked' :'').'>');
		echo('<td> '.logtime($rd['LoggedAt']).'z </td>');
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
	return '<span title="'.$stamp.'">'.date('D H:i',strtotime($stamp)).'</span>';
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
	$R = $DB->query("SELECT * FROM virtualrally");
	if ($rd = $R->fetchArray()) {
		$virtualrally = ($rd['isvirtual'] != 0);
		$virtualstopmins = $rd['stoptime'];
	}
	$sql = "UPDATE claims SET ";
	$sql = '';
	if (isset($_REQUEST['ClaimDate']) && isset($_REQUEST['ClaimTime']))
		$sql .= ($sql==''? '' : ',')."ClaimTime='".$_REQUEST['ClaimDate'].' '.$_REQUEST['ClaimTime']."'";
	if (isset($_REQUEST['NextTimeMins']))
		$sql .= ($sql==''? '' : ',')."NextTimeMins=".parseTimeMins($_REQUEST['NextTimeMins']);
	foreach (['BonusID'] as $fld)
		if (isset($_REQUEST[$fld]))
			$sql .= ($sql==''? '' : ',').$fld."='".$DB->escapeString($_REQUEST[$fld])."'";
	foreach (['BCMethod','EntrantID','OdoReading','Judged','Decision','Applied','FuelBalance'] as $fld)
		if (isset($_REQUEST[$fld]))
			$sql .= ($sql==''? '' : ',').$fld."=".intval($_REQUEST[$fld]);
	if (isset($_REQUEST['Decision']) && !isset($_REQUEST['Judged']))
		$sql .= ($sql==''? '' : ',')."Judged=".(intval($_REQUEST['Decision'])<0? 0 : 1);
	//echo("[[ $sql ]]");

	if (isset($_REQUEST['MagicWord']))
		$sql .= ($sql==''? '' : ',')."MagicWord='".$DB->escapeString($_REQUEST['MagicWord'])."'";
	
	$XF = ['FuelBalance','SpeedPenalty','FuelPenalty','MagicPenalty'];
	if (isset($_REQUEST['NextTimeMins'])){
		$mins = parseTimeMins($_REQUEST['NextTimeMins']);
		if ($virtualrally)
			$mins += $virtualstopmins;
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
	$R = $DB->query("SELECT * FROM virtualrally");
	if ($rd = $R->fetchArray()) {
		$virtualrally = ($rd['isvirtual'] != 0);
		$virtualstopmins = $rd['stoptime'];
	}
	$XF = ['FuelBalance','SpeedPenalty','FuelPenalty','MagicPenalty'];
	
	$sql = "INSERT INTO claims (LoggedAt,ClaimTime,BCMethod,EntrantID,BonusID,OdoReading,Judged,Decision,Applied";
	if (isset($_REQUEST['MagicWord'])) 
		$sql .= ",MagicWord";
	if (isset($_REQUEST['NextTimeMins'])) 
		$sql .= ",NextTimeMins";
	foreach ($XF as $F)
		if (isset($_REQUEST[$F]))
			$sql .= ",$F";
	$sql .= ") VALUES(";
	$la = (isset($_REQUEST['LoggedAt']) && $_REQUEST['LoggedAt'] != '' ? $_REQUEST['LoggedAt'] : date('c'));
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

	startHtml('sm: new claim');

	$virtualrally = false;
	$tankrange = 0;
	$refuelstops = 'NONE'; // re matching nothing
	$stoptime = 0;

	$sql = "SELECT * FROM virtualrally";
	$R = $DB->query($sql);
	if ($rd = $R->fetchArray()) 
		$virtualrally = ($rd['isvirtual'] != 0);
	if ($virtualrally) {
		$tankrange = $rd['tankrange'];
		$refuelstops = $rd['refuelstops'];
		$stoptime = $rd['stoptime'];
	}
	echo('<input type="hidden" id="virtualrally" value="'.($virtualrally ? 1 : 0).'">');
	echo('<input type="hidden" id="tankrange" value="'.$rd['tankrange'].'">'); 
	echo('<input type="hidden" id="refuelstops" value="'.$rd['refuelstops'].'">');
	echo('<input type="hidden" id="stoptime" value="'.$rd['stoptime'].'">');

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
	console.log('cmw1');
	let lmw = '';
	let lmwtime = '';
	let mwok = document.getElementById('mwok');
	mwok.innerHTML = '';
	let mw = document.getElementById('magicword').value;
	let ct = document.getElementById('ClaimDate').value + ' ' + document.getElementById('ClaimTime').value;
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
function checkBonusFuel(str) {
	let rs = document.getElementById('refuelstops');
	if (!rs)
		return false;
	let rx = new RegExp(rs.value);
	if (!rx.test(str)) 
		return false;
	
	console.log(rs.value+' true');
	// This bonus is a refuel stop!
	
	let fb = document.getElementById('FuelBalance');
	fb.value = document.getElementById('tankrange').value;
	fb.setAttribute('title',fb.value);
	return true;

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
	edate = edate + (dd < 10 ? '0' : '') + dd + ' ';
	edate = edate + (hh < 10 ? '0' : '') + hh + ':';
	edate = edate + (nn < 10 ? '0' : '') + nn;
	return edate;
	
}

function checkSpeeding() {
	
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
	console.log('Fetched last claim');
	let ct = document.getElementById('ClaimDate').value+' '+document.getElementById('ClaimTime').value;
	let nm = document.getElementById('lastNextMins').value;
	let ed = addMins(lct,nm);
	let edate = formatDatetime(ed);
	console.log('lc='+lct);
	console.log('ct='+ct);
	console.log('earliest+'+nm+'='+ed);
	console.log(edate);
	if (speedok && ct < edate) {
		console.log('OMG!');
		let tickspeed = ' <input type="checkbox" value="1" name="SpeedPenalty" checked>';
		speedok.innerHTML = ' < ' + edate + tickspeed;
		speedok.style.display = 'inline';
	}
}

	
function odoChanged(odo,emptyok) {

	console.log('Odo has changed');
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
	let tick = document.getElementById('TickFW');

	
	if (thisleg <= fbd) {		// enough fuel
		tick.checked = false;
		if (emptyok) {
			fb.value = fbd - thisleg;
			fb.setAttribute('title',fb.value);
			checkBonusFuel(document.getElementById('BonusID').value);
		}
	} else if (emptyok) {		// don't care
		checkBonusFuel(document.getElementById('BonusID').value)
		fb.value = fb.value + (fbd - thisleg);
		fb.setAttribute('title',fb.value);
		
	} else {
		let fw = document.getElementById('FuelWarning');
		fw.style.display = 'inline';
		fw.setAttribute('title',(fbd-thisleg));
		if (tick)
			tick.checked = true;
	}
		
}

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
	let ntm = document.getElementById('NextTimeMins');
	if (ntm && ntm.value == '') {
		ntm.focus();
		return false;
	}
	let mw = document.getElementById('magicword');
	if (mw && mw.value == '') {
		mw.focus();
		return false;
	}
	let odo = document.getElementById('OdoReading').value;
	odoChanged(odo,true);
	let fb = document.getElementById('FuelBalance');
	if (fb) 
		document.getElementById('saveFuelBalance').value = fb.value;
	return true;
}
</script>
<?php	
	echo('<form method="post" action="claims.php" onsubmit="return validateClaim();">');
	echo('<input type="hidden" name="c" value="newclaim">');
	echo('<input type="hidden" name="LoggedAt" value="">');
	echo('<input type="hidden" name="BCMethod" value="'.$KONSTANTS['BCM_EBC'].'">');
	echo('<input type="hidden" name="Applied" value="0">');
	echo('<input type="hidden" name="claimid" value="'.$claimid.'">');
	echo('<input type="hidden" name="dd" value="'.(isset($_REQUEST['dd']) ? $_REQUEST['dd'] : '-1').'">');
	echo('<input type="hidden" name="showa" value="'.(isset($_REQUEST['showa']) ? $_REQUEST['showa'] : '-1').'">');
	echo('<input type="hidden" name="showd" value="'.(isset($_REQUEST['showd']) ? $_REQUEST['showd'] : '-1').'">');
	echo('<input type="hidden" name="ddate" value="'.(isset($_REQUEST['ddate'])?$_REQUEST['ddate']:date('Y-m-d')).'">');
	if (isset($_REQUEST['entrantid']))
		echo('<input type="hidden" name="entrantid" value="'.$_REQUEST['entrantid'].'">');
	if (isset($_REQUEST['bonusid']))
		echo('<input type="hidden" name="bonusid" value="'.$_REQUEST['bonusid'].'">');
	echo('<input type="hidden" name="FuelBalance" id="saveFuelBalance" value="'.($claimid > 0 ? $rd['FuelBalance'] : 0 ).'">');
	
	
	echo('<span class="vlabel" title="'.$TAGS['EntrantID'][1].'"><label for="EntrantID">'.$TAGS['EntrantID'][0].'</label> ');
	echo('<input autofocus type="number" name="EntrantID" id="EntrantID" tabindex="1" onchange="showEntrant(this.value);"');
	echo(' value="'.($claimid > 0 ? $rd['EntrantID'] : '').'"> ');
	echo('<span id="EntrantName">');
	if ($claimid > 0)
		fetchEntrantDetail($rd['EntrantID']);
	echo('</span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['BonusIDLit'][1].'"><label for="BonusID">'.$TAGS['BonusIDLit'][0].'</label> ');
	echo('<input type="text" name="BonusID" id="BonusID" tabindex="2" onchange="showBonus(this.value);"');
	echo(' value="'.($claimid> 0 ? $rd['BonusID'] : '').'"> ');
	echo('<span id="BonusName">');
	if ($claimid > 0)
		fetchBonusName($rd['BonusID']);
	echo('</span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['OdoReadingLit'][1].'"><label for="OdoReading">'.$TAGS['OdoReadingLit'][0].'</label> ');
	echo('<input type="number" class="bignumber" name="OdoReading" id="OdoReading"');
	if ($claimid==0)
		echo(' onchange="odoChanged(this.value,false);"');
	echo(' tabindex="3"');
	echo(' value="'.($claimid > 0 ? $rd['OdoReading'] : '').'"> ');
	$ck = ($claimid > 0 && $rd['FuelPenalty'] != 0 ? ' checked ' : '');
	$ds = ($claimid > 0 && $rd['FuelPenalty'] != 0 ? 'inline' : 'none');
	echo('<span id="FuelWarning" style="display:'.$ds.';" title="'.$TAGS['FuelWarning'][1].'"> ');
	$fw = $TAGS['FuelWarning'][0].' <input type="checkbox" name="FuelPenalty" value="1" id="TickFW" '.$ck.'>';
	echo($fw);
	echo(' </span>');
	echo('</span> ');

	$ct = splitDatetime($claimid > 0 ? $rd['ClaimTime'] : (isset($_REQUEST['ddate'])?$_REQUEST['ddate']:date('Y-m-d')).'T ');
	echo('<span class="vlabel" title="'.$TAGS['BonusClaimTime'][1].'"><label for="ClaimTime">'.$TAGS['BonusClaimTime'][0].'</label> ');
	$oc = ($claimid == 0 ? ' onchange="checkSpeeding();"' : '');
	echo('<input type="date" name="ClaimDate" id="ClaimDate" value="'.$ct[0].'" tabindex="8"'.$oc.'> ');
	echo('<input type="time" name="ClaimTime" id="ClaimTime" value="'.$ct[1].'" tabindex="4"'.$oc.'> ');
	$ds = ($claimid > 0 && $rd['SpeedPenalty'] != 0 ? 'inline' : 'none');
	$oc = ($claimid == 0 && $rd['SpeedPenalty'] != 0 ? '' : '&dzigrarr; <input type="checkbox" value="1" name="SpeedPenalty" checked>');
	echo('<span id="SpeedWarning" style="display:'.$ds.';">'.$oc.'</span>');
	echo('</span>');
	
	if ($virtualrally) {
		echo('<span class="vlabel" title="'.$TAGS['NextTimeMins'][1].'"><label for="NextTimeMins">'.$TAGS['NextTimeMins'][0].'</label> ');
		echo('<input type="text" class="bignumber" name="NextTimeMins" id="NextTimeMins" value="'.($claimid>0?showTimeMins($rd['NextTimeMins']):'').'" tabindex="5">');
		echo('</span>');
		
		echo('<span class="vlabel" id="magicspan" title="'.$TAGS['magicword'][1].'">');
		echo('<label for="magicword">'.$TAGS['magicword'][0].'</label> ');
		$oc =  ($claimid == 0 ? 'oninput="checkMagicWord();"' : '');
		echo('<input type="text" id="magicword" name="MagicWord"'.$oc.' value="'.($claimid > 0 ? $rd['MagicWord'] : '').'" tabindex="5"> ');
		$oc = ($claimid > 0 && $rd['MagicPenalty'] != 0 ? ' &cross;  <input type="checkbox" value="1" name="MagicPenalty" checked>' : '');
		$cl = ($oc != '' ? ' class="yellow"' : '');
		echo('<span id="mwok"'.$cl.'>'.$oc.'</span>');
		echo('</span>');
	}
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
	$sql = "UPDATE claims SET Judged=".($_REQUEST['val']<0?0:1);
	$sql .= ",Decision=".$_REQUEST['val'];
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
	$virtualrally = getValueFromDB("SELECT isvirtual FROM virtualrally","isvirtual",0);
	$R = $DB->query("SELECT * FROM entrants WHERE EntrantID=".$e);
	if ($rd = $R->fetchArray()) {
		echo($rd['RiderName']);

		$tankrange = getValueFromDB("SELECT tankrange FROM virtualrally","tankrange",0);
		
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

function applyClaims()
// One-off for no ride rally April 2020
{
	global $DB,$TAGS,$KONSTANTS;

	echo('Applying claims... <br>');
	if (!isset($_REQUEST['lodate']) || !isset($_REQUEST['hidate']) ||
		!isset($_REQUEST['lotime']) || !isset($_REQUEST['hitime']) ||
		!isset($_REQUEST['decisions']) ) {
		echo('<hr>NOT ENOUGH PARAMETERS<hr>');
		exit;
	}
	
	$sql = "SELECT * FROM claims WHERE ";
	$loclaimtime = $_REQUEST['lodate'].' '.$_REQUEST['lotime'];
	$hiclaimtime = $_REQUEST['hidate'].' '.$_REQUEST['hitime'];
	//$sqlW = "Applied=0 AND ";
	$sqlW .= "ClaimTime>='".$loclaimtime."' AND ";
	$sqlW .= "ClaimTime<='".$hiclaimtime."' AND ";
	$sqlW .= "Decision IN (".$_REQUEST['decisions'].") ";
	if (isset($_REQUEST['entrants']))
		$sqlW .= " AND EntrantID IN (".$_REQUEST['entrants'].")";
	if (isset($_REQUEST['bonuses']))
		$sqlW .= " AND BonusID IN (".$_REQUEST['bonuses'].")";
	if (isset($_REQUEST['exclude']))
		$sqlW .= " AND BonusID NOT IN (".$_REQUEST['exclude'].")";
	$sql .= $sqlW;
	$sql .= " ORDER BY ClaimTime";
	echo($sql.'<hr>');
	// Load all claims records into memory_get_peak_usage
	// organised as EntrantID, BonusID
	$claims = [];
	$R = $DB->query($sql);
	while ($rd = $R->fetchArray()){
		if (!isset($claims[$rd['EntrantID']])) 
			$claims[$rd['EntrantID']] = [];
		if (!isset($claims[$rd['BonusID']]))
			$claims[$rd['EntrantID']][$rd['BonusID']] = 1;
	}
	//print_r($claims);
	//return;
	
	$DB->exec('BEGIN TRANSACTION');
	foreach($claims as $entrant => $bonuses) {
		echo('Update claims for '.$entrant.' ');
		//print_r($entrant); print_r($bonuses);
		$sql = "SELECT BonusesVisited FROM entrants WHERE EntrantID=".$entrant;
		//echo($sql.'<hr>');
		$bv = explode(',',getValueFromDB($sql,"BonusesVisited",""));
		//print_r($bv);
		foreach(array_keys($bonuses) as $bonus)  {
			if (!in_array($bonus,$bv)) {
				array_push($bv,$bonus);
			}
		}
		//print_r($bv);
		$sql = "UPDATE entrants SET BonusesVisited='".implode(',',$bv)."' WHERE EntrantID=$entrant";
		echo($sql.'<hr>');
		$DB->exec($sql);
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			exit;
		}
		$sql = "UPDATE claims SET Applied=1, Judged=1, Decision=0 WHERE ";    ////////////////////////////////////////
		$sql .= "EntrantID=$entrant AND ";
		$sql .= $sqlW;
		echo($sql.'<hr>');
		$DB->exec($sql);
		if ($DB->lastErrorCode()<>0) {
			echo("SQL ERROR: ".$DB->lastErrorMsg().'<hr>'.$sql.'<hr>');
			exit;
		}

	}
	$DB->exec('COMMIT');
	echo('<br>All done!');
}


if (isset($_REQUEST['deleteclaim']) && isset($_REQUEST['claimid']) && $_REQUEST['claimid']>0) {
	deleteClaim();
	listclaims();
	exit;
}

if (isset($_REQUEST['saveclaim']))
	saveClaim();
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
	
	// If dropped through then just list the buggers.
	listclaims();
}
else
	listclaims();

?>
