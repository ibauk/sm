<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once('common.php');

function errorlog($x)
{
	echo($x.'<br>');
}

function srvCalcMileagePenalty($RP,$rd)
{
	global $KONSTANTS;

	$CM = $rd['CorrectedMiles'];
	$PMM = $RP['PenaltyMaxMiles'];
	$PMMethod = $RP['MaxMilesMethod'];
	$PMPoints = $RP['MaxMilesPoints'];
	$PenaltyMiles = $CM - $PMM;
	
	if ($PenaltyMiles <= 0) // No penalty
		return [0,0]; 
		
	switch ($PMMethod) 	{
		case $KONSTANTS['MMM_PointsPerMile']:
			return [0 - $PMPoints * $PenaltyMiles,0];
		case $KONSTANTS['MMM_Multipliers']:
			return [0,$PMPoints];
		default:
			return [0 - $PMPoints,0];
	}
		
}


function srvCalcRestMinutes($specials,$rejected)
{
	global $DB;

	$sb = explode(',',$specials);
	$rb = explode(',',$rejected);
	$mins = 0;
	foreach ($sb as $bonus) {
		$matches = [];
		if (preg_match('/([a-z0-9\-]+)\=(\d*)\;(\d+)/i',$bonus,$matches))
			$mins += $matches[3];
		else {
			if (preg_match('/(a-z0-9\-]+)/i',$bonus,$matches))
				$bid = $matches[1];
			else
				$bid = $bonus;
			errorlog($bid);
			$mins += getValueFromDB("SELECT RestMinutes FROM specials WHERE BonusID='".$bid."'","RestMinutes",0);
		}
	}
	return $mins;
}
function srvCalcScore($e)
/*
 *	I recalculate a scorecard using the information currently stored in that record as well as
 *	the current values of rallyparams and other variables. My primary use is to update 'dirty'
 *	cards after posting claims records but I can also be used to recalculate all records in the
 *	case of some global update such as resetting the rally finish time or the value of a bonus.
 *
 *	This is a silent procedure returning true for a successful recalculation or false otherwise.
 */
{
	global $DB, $KONSTANTS;
	
	if (!srvGetScorecardLock($e))
		return false;
	if (!$R = $DB->query("SELECT * FROM rallyparams"))
		return srvReleaseLock(false,$e);
	if (!$RP = $R->fetchArray())
		return srvReleaseLock(false,$e);
	
	if ($RP['ScoringMethod'] == $KONSTANTS['ManualScoring'])
		return srvReleaseLock(false,$e);
	if (!$R = $DB->query("SELECT * FROM entrants WHERE EntrantID=$e"))
		return srvReleaseLock(false,$e);
	if (!$rd = $R->fetchArray())
		return srvReleaseLock(false,$e);

	if ($RP['ScoringMethod'] == $KONSTANTS['AutoScoring'])
		$RP['ScoringMethod'] = chooseScoringMethod();
	
	if ($RP['ShowMultipliers'] == $KONSTANTS['AutoShowMults'])
		$RP['ShowMultipliers'] = chooseShowMults($RP['ScoringMethod']);
	
	errorlog('Server recalculating '.$e);
	// Calculate FinishTimeDNF
	$dt = new DateTime(is_null($rd['StartTime']) ? $RP['StartTime'] : $rd['StartTime']);
	$dt = $dt->add(new DateInterval('PT'.$RP['MaxHours'].'H'));
	$rd['FinishTimeDNF'] = ($dt > new DateTime($RP['FinishTime']) ? $RP['FinishTime'] : $dt->format('c'));
	
	// Calculate CorrectedMiles (actually rally unit of distance)
	if (is_null($rd['OdoRallyFinish']) || is_null($rd['OdoRallyStart']) || $rd['OdoRallyFinish'] <= $rd['OdoRallyStart'])
		$miles = $rd['CorrectedMiles'];
	else {
		$miles = ($rd['OdoRallyFinish'] - $rd['OdoRallyStart']) * $rd['OdoScaleFactor'];
		if ($RP['MilesKms'] != $rd['OdoKms']) // Convert to the rally unit
			if ($RP['MilesKms'] != 0)
				$miles = $miles * $KONSTANTS['KmsPerMile'];
			else
				$miles = $miles / $KONSTANTS['KmsPerMile'];
		$rd['CorrectedMiles'] = round($miles); 				// Check agrees with Javascript
	}
	
	//errorlog('   DNF='.$rd['FinishTimeDNF'].'; Miles='.$rd['CorrectedMiles']);
	
	// Calculate average speed
	$dt2 = new DateTime($rd['FinishTime']);
	$dt = new DateTime($rd['StartTime']); 
	$gap = $dt2->diff($dt); // DateInterval object
	$elapsedmins = ($gap->days * 24 * 60) + ($gap->h * 60) + $gap->i;
	$rd['RestMinutes'] = srvCalcRestMinutes($rd['SpecialsTicked'],$rd['RejectedClaims']);
	$hours = ($elapsedmins - $rd['RestMinutes']) / 60;
	if ($rd['CorrectedMiles'] < 1 || $hours < 1)
		$rd['CalculatedAvgSpeed'] = 0;
	else
		$rd['CalculatedAvgSpeed'] = round((($rd['CorrectedMiles'] / $hours) * 100) / 100,2);
	$rd['AvgSpeed'] = $rd['CalculatedAvgSpeed'].' '.($RP['MilesKms'] != 0 ? 'km/h' : 'mph');
	
	errorlog('   dnf='.$rd['FinishTimeDNF']);
	errorlog('   end='.$rd['FinishTime'].'; Rest='.$rd['RestMinutes'].'; Miles='.$rd['CorrectedMiles'].'; Speed='.$rd['AvgSpeed']);
	
	$rd['ScoreX'] = srvStartScorex($RP,$rd);

	$rd['SplitBV'] = explode(',',$rd['BonusesVisited']);
	$rd['SplitST'] = explode(',',$rd['SpecialsTicked']);
	$rcp = explode(',',$rd['RejectedClaims']);
	$rd['SplitRC'] = [];
	foreach($rcp as $bcp) {
		$bc = explode('=',$bcp);
		if ($bc[0] != '')
			$rd['SplitRC'][$bc[0]] = $bc[1];
	}


	$combos = srvTickCombos($RP,$rd);
	$rd['CombosTicked'] = $combos[0];
	$rd['ComboVals'] = $combos[1];
	$rd['SplitCB'] = explode(',',$rd['CombosTicked']);

	// Need to store the values also
	$tmp = explode(',',$rd['ComboVals']);
	$rd['SplitCV'] = [];
	foreach($tmp as $cv) {
		$cvp = explode('=',$cv);
		$rd['SplitCV'][$cvp[0]] = $cvp[1];
	}
	
	if ($RP['ScoringMethod'] == $KONSTANTS['CompoundScoring'])
		;
	else
		srvCalcScoreSimple($RP,$rd);
	

	$points = $rd['TotalPoints'];

	$MPenalty = srvCalcMileagePenalty($RP,$rd);
	$points += $MPenalty[0];
	if ($MPenalty[0] != 0) {
		errorlog('Mileage penalty is '.$MPenalty[0]);
		$rd['ScoreX'] .= srvAppendScorex('',$KONSTANTS['RPT_MPenalty'],$MPenalty[0],0,$points,false);
	}
	
	$TPenalty = srvCalcTimePenalty($RP,$rd);
	$points += $TPenalty[0];
	if ($TPenalty[0] != 0) {
		errorlog('Time penalty is '.$TPenalty[0]);
		$rd['ScoreX'] .= srvAppendScorex('',$KONSTANTS['RPT_TPenalty'],$TPenalty[0],0,$points,false);
	}
	
	$SPenalty = srvCalcSpeedPenalty(false,$rd);
	$points += $SPenalty;
	if ($SPenalty != 0) {
		errorlog('Speed penalty is '.$SPenalty);
		$rd['ScoreX'] .= srvAppendScorex('',$KONSTANTS['RPT_SPenalty'],$SPenalty,0,$points,false);
	}
		
	$rd['TotalPoints']	= $points;


	srvSetFinisherStatus($RP,$rd);

	srvCompleteScorex($RP,$rd);

	errorlog('    '.$e.': Points='.$rd['TotalPoints'].'; '.$rd['EntrantStatus'].' = '.$rd['StatusReason']);

	$sql = "UPDATE entrants SET TotalPoints=".$rd['TotalPoints'];
	$sql .= ",ScoringNow=0,Confirmed=0,ScoreX='".$DB->escapeString($rd['ScoreX'])."'";
	$sql .= ",AvgSpeed='".$rd['AvgSpeed']."'";
	$sql .= ",RestMinutes=".$rd['RestMinutes'];
	$sql .= ",CorrectedMiles=".$rd['CorrectedMiles'];
	$sql .= ",CombosTicked='".$rd['CombosTicked']."'";
	$sql .= ",EntrantStatus=".$rd['EntrantStatus'];
	$sql .= " WHERE EntrantID=$e";
	//errorlog($sql);
	$DB->exec($sql);
}

function srvCalcScoreComplex($RP,&$rd)
{
	global $KONSTANTS;

	$totalBonusPoints = 0;
	$totalMultipliers = 0;
	$totalTickedBonuses = 0;

}

function srvCalcScoreSimple($RP,&$rd)
{
	global $KONSTANTS;

	$points = 0;

	foreach($rd['SplitBV'] as $bonusid) {
		$Bbonusid = $KONSTANTS['ORDINARY_BONUS_PREFIX'].$bonusid;
		$bd = getValueFromDB("SELECT BriefDesc FROM bonuses WHERE BonusID='".$bonusid."'","BriefDesc",$bonusid);
		if (trim($bd) != '') {
			if (isset($rd['SplitRC'][$Bbonusid])) {
				errorlog('Rejecting '.$bonusid);
				$rd['ScoreX'] .= srvReportRejectedClaim($Bbonusid,$bd,$rd['SplitRC'][$Bbonusid],$RP['RejectReasons'],$RP['ShowMultipliers']==$KONSTANTS['ShowMults']);
			} else {
				$bp = getValueFromDB("SELECT Points FROM bonuses WHERE BonusID='".$bonusid."'","Points",0);
				$points += $bp;
				$rd['ScoreX'] .= srvAppendScorex($KONSTANTS['ORDINARY_BONUS_PREFIX'].'-'.$bonusid,$bd,$bp,0,$points,$RP['ShowMultipliers']==$KONSTANTS['ShowMults']); // Report the claims
			}
		}
	}
	
	foreach($rd['SplitST'] as $bonusid) {
		$Bbonusid = $KONSTANTS['SPECIAL_BONUS_PREFIX'].$bonusid;
		$bd = getValueFromDB("SELECT BriefDesc FROM specials WHERE BonusID='".$bonusid."'","BriefDesc",$bonusid);
		if (trim($bd) != '') {
			if (isset($rd['SplitRC'][$Bbonusid])) {
				$rd['ScoreX'] .= srvReportRejectedClaim($Bbonusid,$bd,$rd['SplitRC'][$Bbonusid],$RP['RejectReasons'],$RP['ShowMultipliers']==$KONSTANTS['ShowMults']); // Report the rejected bonus
			} else {
				$bp = getValueFromDB("SELECT Points FROM specials WHERE BonusID='".$bonusid."'","Points",0);
				$points += $bp;
				$rd['ScoreX'] .= srvAppendScorex($KONSTANTS['SPECIAL_BONUS_PREFIX'].'-'.$bonusid,$bd,$bp,0,$points,$RP['ShowMultipliers']==$KONSTANTS['ShowMults']); // Report the claims
			}
		}
		
	}

	foreach($rd['SplitCV'] as $bonusid => $bp) {
		$Bbonusid = $KONSTANTS['COMBO_BONUS_PREFIX'].$bonusid;
		$bd = getValueFromDB("SELECT BriefDesc FROM combinations WHERE ComboID='".$bonusid."'","BriefDesc",$bonusid);
		if (trim($bd) != '') {
			if (isset($rd['SplitRC'][$Bbonusid])) {
				$rd['ScoreX'] .= srvReportRejectedClaim($Bbonusid,$bd,$rd['SplitRC'][$Bbonusid],$RP['RejectReasons'],$RP['ShowMultipliers']==$KONSTANTS['ShowMults']); // Report the rejected bonus
			} else {
				//$bp = getValueFromDB("SELECT ScorePoints As Points FROM combinations WHERE ComboID='".$bonusid."'","Points",0);
				$points += $bp;
				$rd['ScoreX'] .= srvAppendScorex($KONSTANTS['COMBO_BONUS_PREFIX'].'-'.$bonusid,$bd,$bp,0,$points,$RP['ShowMultipliers']==$KONSTANTS['ShowMults']); // Report the claims
			}
		}
		
	}


	$rd['TotalPoints'] = $points;
}

function srvGetScorecardLock($e)
{
	global $DB;
	
	// Get lock on unused or dirty (but not locked) scorecard
	if (!$DB->exec("UPDATE entrants SET ScoringNow=1 WHERE EntrantID=$e AND ScoringNow<>1") || $DB->changes() != 1)
		return srvReleaseLock(false,$e);
	return true;
}

function srvReleaseLock($b,$e)
{
	global $DB;
	errorlog("Releasing lock!");
	$DB->exec("UPDATE entrants SET ScoringNow=0 WHERE EntrantID=$e");
	return $b;
}


function srvReportRejectedClaim($bonusid,$descr,$reason,$reasons,$showmults)
{
	global $KONSTANTS;

	if ($bonusid=='')
		return;
	$reasontext = explode('=',explode("\n",$reasons)[$reason - 1]);
	$bid = substr($bonusid,0,1).'-'.substr($bonusid,1);
	$res = srvAppendScorex($bid,$descr,'X','','',$showmults);
	$res .= srvAppendScorex('',$KONSTANTS['CLAIM_REJECTED'].' - '.$reasontext[1],'','','',$showmults);
	return $res;

}

function srvTickCombos($RP,$rd)
/*
 * This rebuilds CombosTicked but does not affect their entry in RejectedClaims
 * 
 */
{
	global $DB;

	$combosticked = '';
	$combovals = '';
	$sql = "SELECT * FROM combinations ORDER BY ComboID";
	$R = $DB->query($sql);
	while($cd = $R->fetchArray()) {
		$bn = explode(',',$cd['Bonuses']); // Bonuses needed
		$cta = explode(',',$combosticked); // Combos Ticked Already
		$num_ok = 0;
		$num_needed = ($cd['MinimumTicks'] > 0 ? $cd['MinimumTicks'] : count($bn));
		//errorlog('Combo '.$cd['ComboID'].' needs '.$num_needed.' matches');
		foreach($bn as $bonus) {
			if (in_array($bonus,$rd['SplitRC']))
				continue;
			if (in_array($bonus,$rd['SplitBV']) || in_array($bonus,$rd['SplitST']) || in_array($bonus,$cta)) 
				$num_ok++;
		}
		if ($num_ok >= $num_needed) {
			if ($combosticked != '')
				$combosticked .= ',';
			$combosticked .= $cd['ComboID'];
			if ($combovals != '')
				$combovals .= ',';
			$ptsa = explode(',',$cd['ScorePoints']);
			if (count($ptsa) < 2)
				$pts = $cd['ScorePoints'];
			else {
				$i = $num_ok - $num_needed;
				if ($i >= count($ptsa))
					$i = count($ptsa) - 1;
				$pts = $ptsa[$i];
				errorlog('Part combo '.$cd['ComboID'].' has '.$num_ok.' = '.$pts);
			}
			$combovals .= $cd['ComboID'].'='.$pts;
		}
	}

	return [$combosticked,$combovals];
}

function srvAppendScorex($bonusid,$descr,$bonuspoints,$mults,$totalpoints,$showmults)
{
	//errorlog('NIY: srvAppendScorex');
	$res = '<tr><td class="id">'.$bonusid.'</td>';
	$res .= '<td class="desc">'.$descr.'</td><td class="bp">'.$bonuspoints.'</td>';

	if ($showmults)
		$res .= '<td class="bm">'.$mults.'</td>';

	$res .= '<td class="tp">'.$totalpoints.'</td></tr>';
	return $res;
}

function srvCalcSpeedPenalty($dnf,$rd)
/*
 * If parameter dnf is false then
 * This will return the number of penalty points (not multipliers) or 0
 * If highest match gives DNF, I return 0
 *
 * If parameter dnf is true then
 * If highest match give DNF, return true otherwise false
 *
 */
{
	global $DB;

	$speed = $rd['CalculatedAvgSpeed'];
	$R = $DB->query('SELECT Basis,MinSpeed,PenaltyType,PenaltyPoints FROM speedpenalties ORDER BY MinSpeed DESC');
	while ($sd = $R->fetchArray()) {
		if ($speed < $sd['MinSpeed']) 
			continue;

		if ($sd['PenaltyType']==1) // DNF
			return ($dnf ? true : 0);
		if ($dnf)
			return false;
		return 0 - $sd['PenaltyPoints'];
	}
		return 0;
}


function srvCalcTimePenalty($RP,$rd)
{
	global $DB, $KONSTANTS;

	$ft = new DateTime($rd['FinishTime'].'Z');
	$sql = "SELECT * FROM timepenalties ORDER BY PenaltyStart";
	$R = $DB->query($sql);
	while($td = $R->fetchArray()) {
		$dtiFrom = DateInterval::createFromDateString('- '.$td['PenaltyStart'].' minutes');
		$dtiTo = DateInterval::createFromDateString('- '.$td['PenaltyFinish'].' minutes');
		switch($td['TimeSpec']) {
			case $KONSTANTS['TimeSpecRallyDNF']:
				$dnf = new DateTime($RP['FinishTime'].'Z');
				$dnfc = clone $dnf;
				$ds = $dnf->add($dtiFrom);
				$de = $dnfc->add($dtiTo);
				break;
			case $KONSTANTS['TimeSpecEntrantDNF']:
				$dnf = new DateTime($rd['FinishTimeDNF'].'Z');
				$dnfc = clone $dnf;
				$ds = $dnf->add($dtiFrom);
				errorlog($dnf->format('c').'; '.$dnfc->format('c'));
				$de = $dnfc->add($dtiTo);
				break;
			default:
				$ds = new DateTime($td['PenaltyStart'].'Z');
				$de = new DateTime($td['PenaltyFinish'].'Z');
		}
		errorlog('Considering time penalty '.$ft->format('c').' == '.$ds->format('c').'/'.$de->format('c'));
		if ($ft >= $ds && $ft <= $de) {
			$d = $ft->diff($ds);
			$mins = ($d->h * 60) + $d->i;
			errorlog('Giving time penalty '.$ft->format('c').' == '.$ds->format('c').'/'.$de->format('c').' Mins='.$mins);
			switch($td['PenaltyMethod']) {
				case $KONSTANTS['TPM_MultPerMin']:
					return [0,0 - $td['PenaltyFactor'] * $mins];
				case $KONSTANTS['TPM_PointsPerMin']:
					return [0 - $td['PenaltyFactor'] * $mins,0];
				case $KONSTANTS['TPM_FixedMult']:
					return [0,0 - $td['PenaltyFactor']];
				default:
					return [0 - $td['PenaltyFactor'],0];
			}
		}
	}
	return [0,0];

}





function srvSetFinisherStatus($RP,&$rd)
{
	global $KONSTANTS, $DB;

	$rd['EntrantStatus'] = $KONSTANTS['EntrantFinisher'];
	$rd['StatusReason'] = '';

	if (srvCalcSpeedPenalty(true,$rd)) {
		$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
		$rd['StatusReason'] = $KONSTANTS['DNF_SPEEDING'];
		return;
	}
	if ($RP['MinMiles'] > 0 && $rd['CorrectedMiles'] < $RP['MinMiles']) {
		$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
		$rd['StatusReason'] = $KONSTANTS['DNF_TOOFEWMILES'];
		return;
	}
	if ($RP['PenaltyMilesDNF'] > 0 && $rd['CorrectedMiles'] > $RP['PenaltyMilesDNF']) {
		$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
		$rd['StatusReason'] = $KONSTANTS['DNF_TOOMANYMILES'];
		return;
	}
	if ($rd['FinishTime'] > $rd['FinishTimeDNF']) {
		$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
		$rd['StatusReason'] = $KONSTANTS['DNF_FINISHEDTOOLATE']. ' > '.$rd['FinishTimeDNF'];
		return;
	}

	$sql = "SELECT * FROM bonuses ORDER BY BonusID";
	$R = $DB->query($sql);
	while ($bd = $R->fetchArray()) 
		if ($bd['Compulsory'] == $KONSTANTS['COMPULSORYBONUS']) 
			if (in_array($bd['BonusID'],$rd['SplitRC']) || !in_array($bd['BonusID'],$rd['SplitBV'])) {
				$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
				$rd['StatusReason'] = $KONSTANTS['DNF_MISSEDCOMPULSORY'].' ['.$KONSTANTS['ORDINARY_BONUS_PREFIX'].$bd['BonusID'].']';
				return;
			}
	$sql = "SELECT * FROM specials ORDER BY BonusID";
	$R = $DB->query($sql);
	while ($bd = $R->fetchArray()) 
		if ($bd['Compulsory'] == $KONSTANTS['COMPULSORYBONUS']) {
			if (in_array($bd['BonusID'],$rd['SplitRC']) || !in_array($bd['BonusID'],$rd['SplitBV'])) {
				$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
				$rd['StatusReason'] = $KONSTANTS['DNF_MISSEDCOMPULSORY'].' ['.$KONSTANTS['SPECIAL_BONUS_PREFIX'].$bd['BonusID'].']';
				return;
			}
		} else if ($bd['Compulsory'] == $KONSTANTS['MUSTNOTMATCH']) {
			if (!in_array($bd['BonusID'],$rd['SplitRC']) && in_array($bd['BonusID'],$rd['SplitBV'])) {
				$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
				$rd['StatusReason'] = $KONSTANTS['DNF_HITMUSTNOT'].' ['.$KONSTANTS['SPECIAL_BONUS_PREFIX'].$bd['BonusID'].']';
				return;
			}
		}
	$combos = explode(',',$rd['CombosTicked']);
	$sql = "SELECT * FROM combinations ORDER BY ComboID";
	$R = $DB->query($sql);
	while ($bd = $R->fetchArray()) 
		if ($bd['Compulsory'] == $KONSTANTS['COMPULSORYBONUS']) {
			if (in_array($bd['ComboID'],$rd['SplitRC']) || !in_array($bd['ComboID'],$combos)) {
				$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
				$rd['StatusReason'] = $KONSTANTS['DNF_MISSEDCOMPULSORY'].' ['.$bd['ComboID'].']';
				return;
			}
		} else if ($bd['Compulsory'] == $KONSTANTS['MUSTNOTMATCH']) {
			if (!in_array($bd['ComboID'],$rd['SplitRC']) && in_array($bd['ComboID'],$combos)) {
				$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
				$rd['StatusReason'] = $KONSTANTS['DNF_HITMUSTNOT'].' ['.$bd['ComboID'].']';
				return;
			}
		}
		
	if ($rd['TotalPoints'] < $RP['MinPoints'])	{
		$rd['EntrantStatus'] = $KONSTANTS['EntrantDNF'];
		$rd['StatusReason'] = $KONSTANTS['DNF_TOOFEWPOINTS'];
		return;
	}
		

}

function srvCompleteScorex($RP,&$rd)
{
	global $KONSTANTS, $TAGS;

	eval("\$evs = ".$TAGS['EntrantStatusV'][0]);

	$sex = '<table><caption>'.$TAGS['EntrantID'][0].' '.$rd['EntrantID'].' ';
	$sex .= $rd['RiderName'];
	if ($rd['PillionName'] != '')
		$sex .= ' & '.$rd['PillionName'];
	$sex .= ' [&nbsp;'.$evs[$rd['EntrantStatus']].'&nbsp;]';

	$distance = '';
	$cm = $rd['CorrectedMiles'];
	if ($cm > 0)
		$distance = $distance.$cm.' '.($KONSTANTS['BasicDistanceUnit']==$KONSTANTS['DistanceIsMiles'] ? $TAGS['OdoKmsM'][0] : $TAGS['OdoKmsK'][0]);
	$avg = $rd['AvgSpeed'];
	if ($avg != '')
		$distance = $distance . ' @ ' . $avg;
	if ($distance != '')
		$sex .= '<br><span class="explain">'.$distance.'</span>';


	$sex .= '</caption>';
	$sex .= '<thead><tr><th class="id">ID</th><th class="desc"></th><th class="bp">BP</th>';
	if ($RP['ShowMultipliers']==$KONSTANTS['ShowMults'])
		$sex .= '<th class="bm"></th>';
	$sex .= '<th class="tp">TP</th></tr></thead><tbody>';
	$sex .= $rd['ScoreX'];
	$sex .= '<tr><td class="id"></td><td class="desc"> '.$KONSTANTS['RPT_Total'].'</td><td class="bp">';
	if ($RP['ShowMultipliers']==$KONSTANTS['ShowMults'])
		$sex .= '<td class="bm"></th>';
	$sex .= '</td><td class="tp">'.$rd['TotalPoints'].'</td></tr>';
	if ($rd['StatusReason'] != '') {
		$sex .= '<tr><td class="id"> '.$evs[$rd['EntrantStatus']].'<td><td class="desc">'.$rd['StatusReason'].'</td>';
		$sex .= '<td class="bp"></td>';
		if ($RP['ShowMultipliers']==$KONSTANTS['ShowMults'])
			$sex .= '<th class="bm"></th>';
		$sex .= '<td class="tp>0</td></tr>';
	}
	$sex .= '</tbody></table>';
	$rd['ScoreX'] = $sex;

}

function srvStartScorex($RP,$rd)
{
	return '';
}

if (isset($_REQUEST['e']))
	srvCalcScore($_REQUEST['e']);
?>