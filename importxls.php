<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle imports of data from spreadsheets
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

$debuglog = TRUE;
$HOME_URL = "admin.php?c=entrants";

require_once("common.php");

//$target_dir = __DIR__ .'/uploads/';
$target_dir = './uploads/';


/**
$SPECFILES = array(	'BBR' => array('bbrspec.php','Brit Butt rally'),
					'Jorvic' => array('jorvicspec.php','Jorvic rally'),
					'RBLR' => array('rblrspec.php','RBLR1000')
				);
**/
require_once("specfiles.php");

require_once './PHPExcel/Classes/PHPExcel/IOFactory.php';

function getMergeCols($sheet,$row,$colspec,$sep = ' ')
// Extract and return the contents of one or more cells
{
//	if ($sheet < 0)
//		var_dump($colspec);
	if ($colspec === NULL)
		return '';
	$cols = explode(':',$colspec);
	$res = '';
	for ($i = 0; $i < sizeof($cols); $i++)
	{
		if ($res <> '') $res .= $sep;
		$res .= $sheet->getCellByColumnAndRow($cols[$i],$row)->getValue();
	}
	return $res;
}

function getNameFields($sheet,$row,$namelabels)
{
	global $IMPORTSPEC;
	
	$res = ['',''];
	if (isset($IMPORTSPEC['cols'][$namelabels[0]])) {
		$res[0] = getMergeCols($sheet,$row,$IMPORTSPEC['cols'][$namelabels[0]]);
		$res[1] = explode(' ',$res[0])[0].' ';
		$res[0] .= ' ';
	} elseif (isset($IMPORTSPEC['cols'][$namelabels[1]]) && isset($IMPORTSPEC['cols'][$namelabels[2]])) {
		$res[0] = getMergeCols($sheet,$row,$IMPORTSPEC['cols'][$namelabels[1]].':'.$IMPORTSPEC['cols'][$namelabels[2]]);
		$res[1] = getMergeCols($sheet,$row,$IMPORTSPEC['cols'][$namelabels[1]]);
	}
	else
		;//var_dump($IMPORTSPEC);
	return $res;

}

function buildList($lst,$itm)
{
	if ($lst != '')
		$res = $lst.','.$itm;
	else
		$res = $itm;
	return $res;
}


function showUpload()
{
	global $TAGS, $SPECFILES;
	
	startHtml();

	echo('<h1>'.$TAGS['UploadEntrantsH1'][1].'</h1>');
?>
<form action="importxls.php" method="post" enctype="multipart/form-data">
<?php
	$chk = TRUE;
	$i = 0;
	foreach ($SPECFILES as $spc => $specs)
	{
		$i++;
		echo('<span title="'.$specs[1].'">');
		echo('<label for="specfile'.$i.'">'.$spc.' </label>');
		echo('<input type="radio" name="specfile" id="specfile'.$i.'" value="'.$specs[0].'"');
		if ($chk)
			echo(' checked=checked ');
		echo('></span> &nbsp;&nbsp;');
		$chk = FALSE;
	}
?>
<!-- <input type="hidden" name="specfile" value="rblr"> -->
<br>
<label for="fileid"><?php echo($TAGS['UploadPickFile'][1]);?> </label>
<input type="file" name="fileid" id="fileid">
<br><br>
<label for="force"><?php echo($TAGS['UploadForce'][1]);?> </label>
<input type="checkbox" name="force" id="force">
<br><br>
<input type="submit" name="fileuploaded" value="<?php echo($TAGS['Upload'][0]);?>">
</form>
<?php
}

function processUpload()
{
	global $IMPORTSPEC, $target_dir;
	
	
	if (file_exists($target_dir.IMPORTSPEC['xlsname']))
		die("file [".$target_dir.IMPORTSPEC['xlsname']."] already exists");
	if (!move_uploaded_file($_FILES['fileid']['tmp_name'],$target_dir.$IMPORTSPEC['xlsname']))
		die('Upload failed ['.$_FILES['fileid']['tmp_name'].']==>['.$target_dir.$IMPORTSPEC['xlsname'].']');
}

if (isset($_REQUEST['showupload']))
{
	showUpload();
	exit;
}

if (!isset($_REQUEST['specfile']))
	die($TAGS['xlsNoSpecfile'][1]);

require_once($_REQUEST['specfile']);

startHtml($TAGS['xlsImporting'][0]);

$debugging = FALSE;

echo("debugging=".$debugging."; specfile=".$_REQUEST['specfile']."<br>");

if (!isset($IMPORTSPEC['xlsname'])) 
	die("No spreadsheet name found");

if (isset($_REQUEST['fileuploaded']))
	processUpload();

if ($debugging) echo("1 Testing ".$target_dir.$IMPORTSPEC['xlsname']."<br>");
try {
	$xlstype = PHPExcel_IOFactory::identify($target_dir.$IMPORTSPEC['xlsname']);
} catch (Exception $e) {
	die("Error: ".$e->getMessage());
}
if ($debugging) echo("2 $xlstype<br>");
$rdr = PHPExcel_IOFactory::createReader($xlstype); 
$rdr->setReadDataOnly(true);
$rdr->setLoadSheetsOnly($IMPORTSPEC['whichsheet']);
if ($debugging) echo("3<br>");
try {
	$xls = $rdr->load($target_dir.$IMPORTSPEC['xlsname']);
} catch (Exception $e) {
	die("Error: ".$e->getMessage());
}
if ($debugging)
{
	if ($xls)
		echo(" xls ");
	else
		echo(" !!! ");
}
if ($debugging) echo("4<br>");
$sheet = $xls->getSheet();
$row = $IMPORTSPEC['FirstDataRow'];  // Skip the column headers
if ($debugging) echo("5 [$row]<br>");	
$nrows = 0;

echo("<p>".$TAGS['xlsImporting'][1]."</p>");

$sql = "SELECT Count(*) AS Rex FROM entrants";
$R = $DB->query($sql);
$rr = $R->fetchArray();
if ($rr['Rex'] > 0) {
	if (!isset($_REQUEST['force'])) {
		die($TAGS['xlsNotEmpty'][1]);
		exit;
	}
}
$DB->query("BEGIN TRANSACTION");
$DB->query("DELETE FROM entrants");

$SqlBuilt = FALSE;

echo('<p class="techie">');

$row = $row - 1;  // Step back one so I can bump below

while ($row++ >= 0) {
	try {
		if ($debugging) echo("a ");
		if (isset($IMPORTSPEC['reject']))
			foreach($IMPORTSPEC['reject'] as $col => $re)
			{
				$val = getMergeCols($sheet,$row,$col);
				if (preg_match($re,$val) === 1 ? TRUE : FALSE)
					$ok = TRUE;
				else
					$ok = FALSE;
				if ($debugging)
					echo(' ['.$val.'] != ['.$re.']');
				if ($ok)
				{
					continue 2;
				}
			}
		if (isset($IMPORTSPEC['cols']['EntrantID']))
		{
			$entrantid = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['EntrantID']);
			if ($entrantid == '')
				break;
			if ($entrantid == 'X') // Entrant flagged as withdrawn for whatever reason
				continue;
		}
		else
			$entrantid = '';
		if (isset($IMPORTSPEC['cols']['FinishPosition']))
			$finishposition = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['FinishPosition']);
		else
			$finishposition = 0;
		if (isset($IMPORTSPEC['select']))
		{
			$ok = TRUE;
			foreach($IMPORTSPEC['select'] as $col => $re)
			{
				$val = getMergeCols($sheet,$row,$col);
				if ($debugging)
					echo(' ['.$val.']==['.$re.'] ');
				$ok = $ok && preg_match($re,$val);
			}
			if (!$ok)
				continue;
		}
		if ($debugging) echo("[$entrantid] b  ");
		$ridernames = getNameFields($sheet,$row,array('RiderName','RiderFirst','RiderLast'));
		if ($debugging) echo("c  [".$ridernames[0]."]");
		
		if (trim($ridernames[0])=='') 
			break;

		$pillionnames = getNameFields($sheet,$row,array('PillionName','PillionFirst','PillionLast'));
		$bike = getNameFields($sheet,$row,array('Bike','Make','Model'));
		if ($debugging) echo("d [$bike[0]] ");
		if (isset($IMPORTSPEC['cols']['BikeReg']))
			$bikereg = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['BikeReg']);
		else
			$bikereg = '';
		
		$rideriba = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['RiderIBA']);
		$pillioniba = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['PillionIBA']);
		$teamid = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['TeamID']);
		$country = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['Country']);
		$scoredby = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['ScoredBy']);
		
		$xtraData = '';
		foreach($IMPORTSPEC['data'] as $k => $kcol)
		{
			$xtraData .= $k.'='.getMergeCols($sheet,$row,$IMPORTSPEC['data'][$k])."\n";
		}
		if ($debugging) echo("e  ");
		if (!$SqlBuilt)
		{
			$sql = "INSERT INTO entrants (";
			$fl = '';
		
			if ($entrantid != '')
				$fl .= 'EntrantID';
			$fl = buildList($fl,'RiderName');
		
			$fl = buildList($fl,'RiderFirst');
			$fl = buildList($fl,'PillionName');
			$fl = buildList($fl,'PillionFirst');
			if ($bike[0] != '')
				$fl = buildList($fl,'Bike');
			if (isset($IMPORTSPEC['cols']['BikeReg']))
				$fl = buildList($fl,'BikeReg');
			
			$fl = buildList($fl,'FinishPosition');
			$fl = buildList($fl,'RiderIBA');
			$fl = buildList($fl,'PillionIBA');
			$fl = buildList($fl,'TeamID');
			$fl = buildList($fl,'Country');
			$fl = buildList($fl,'ScoredBy');
			
			foreach($IMPORTSPEC['default'] as $fld => $val)
				$fl = buildList($fl,$fld);
				
			if (count($IMPORTSPEC['data'])>0)
				$fl = buildList($fl,'ExtraData');
			$sql .= $fl;
			$sql .= ") VALUES (";
			$fl = '';
			if ($entrantid != '')
				$fl .= ':EntrantID';
			$fl = buildList($fl,':RiderName');
		
			$fl = buildList($fl,':RiderFirst');
			$fl = buildList($fl,':PillionName');
			$fl = buildList($fl,':PillionFirst');
			if ($bike[0] != '')
				$fl = buildList($fl,':Bike');
			if (isset($IMPORTSPEC['cols']['BikeReg']))
				$fl = buildList($fl,':BikeReg');
			
			$fl = buildList($fl,':FinishPosition');
			$fl = buildList($fl,':RiderIBA');
			$fl = buildList($fl,':PillionIBA');
			$fl = buildList($fl,':TeamID');
			$fl = buildList($fl,':Country');
			$fl = buildList($fl,':ScoredBy');
			
			foreach($IMPORTSPEC['default'] as $fld => $val)
				$fl = buildList($fl,':'.$fld);

			if (count($IMPORTSPEC['data'])>0)
				$fl = buildList($fl,':ExtraData');
		
			$sql .= $fl;
		
			$sql .= ")";
			if ($debugging) echo("{{".$sql."}}<br>");
			try {
				$stmt = $DB->prepare($sql);
				if ($stmt == FALSE)
					die("Prepare failed ".$DB->lastErrorMsg());
			} catch(Exception $e) {
				die($e->getMessage());
			}
			$SqlBuilt = TRUE;
		}
		if ($debugging) echo("A [".$entrantid."]  ");
		//if ($debugging) var_dump($stmt);
		try
		{
		if ($entrantid != '')
			$stmt->bindValue(':EntrantID',$entrantid,SQLITE3_INTEGER);
		} catch(Exception $e) {
			die($e->getMessage());
		}
		if ($debugging) echo("B  ");
		$stmt->bindValue(':RiderName',properName(trim($ridernames[0])),SQLITE3_TEXT);
		if ($debugging) echo("C  ");
		$stmt->bindValue(':RiderFirst',properName(trim($ridernames[1])),SQLITE3_TEXT);
		if ($debugging) echo("D  ");
		if ($debugging) var_dump($pillionnames);
		
		// Not everyone can follow simple form-filling instructions
		if (trim($pillionnames[0])==trim($ridernames[0]))
		{
			$pillionnames[0] = '';
			$pillionnames[1] = '';
		}
		$stmt->bindValue(':PillionName',properName(trim($pillionnames[0])),SQLITE3_TEXT);
		if ($debugging) echo("E  ");
		$stmt->bindValue(':PillionFirst',properName(trim($pillionnames[1])),SQLITE3_TEXT);
		if ($debugging) echo("F  ");
		$stmt->bindValue(':Bike',properName(trim($bike[0])),SQLITE3_TEXT);
		if (isset($IMPORTSPEC['cols']['BikeReg']))
			$stmt->bindValue(':BikeReg',strtoupper(trim($bikereg)),SQLITE3_TEXT);
		
		$stmt->bindValue(':FinishPosition',$finishposition,SQLITE3_INTEGER);
		$stmt->bindValue(':RiderIBA',$rideriba,SQLITE3_INTEGER);
		$stmt->bindValue(':PillionIBA',$pillioniba,SQLITE3_INTEGER);
		$stmt->bindValue(':TeamID',$teamid,SQLITE3_INTEGER);
		$stmt->bindValue(':Country',$country,SQLITE3_TEXT);
		$stmt->bindValue(':ScoredBy',$scoredby,SQLITE3_TEXT);
		
		if ($debugging) echo("G  ");
		
		foreach($IMPORTSPEC['default'] as $fld => $defval)
		{
			if ($debugging) echo(' ['.$fld.'='.$defval.']');
			$stmt->bindValue(':'.$fld,$defval);
			foreach($IMPORTSPEC['setif'][$fld] as $val => $mtch)
				if (preg_match($mtch[1],getMergeCols($sheet,$row,$mtch[0])))
				{
					if ($debugging) echo(' ['.$fld.'=='.$val.']');
					$stmt->bindValue(':'.$fld,$val);
				}
		}
		
		if (count($IMPORTSPEC['data'])>0)
			$stmt->bindValue(':ExtraData',trim($xtraData),SQLITE3_TEXT);
		
		echo("$entrantid : $ridernames[0] - $bike[0]<br>");
		$stmt->execute();
		$nrows++;
		
	} catch(Exception $e) {
		die("Caught ".$e->getMessage());
		break;
	}
}
$DB->query("COMMIT TRANSACTION");

echo("</p><p>All done - $nrows rows loaded </p>");
 
?>
