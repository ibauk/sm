<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle imports of data from spreadsheets
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
 */

$debuglog = TRUE;
$HOME_URL = "admin.php?c=entrants";

require_once("common.php");

$target_dir = './uploads/';
$upload_state = '';

//print_r($_REQUEST);

// These are defaults but, let's face it, there's never going to be a need to override
$IMPORTSPEC['xlsname']	= "import.xls";
$IMPORTSPEC['whichsheet']	= 0;
$IMPORTSPEC['FirstDataRow']	= 2;

// Declare psuedo fields here then load from database schema
$IGNORE_COLUMN = 'zzzzzzz';
$ENTRANT_FIELDS = [$IGNORE_COLUMN=>'ignore','RiderLast'=>'RiderLast','PillionLast'=>'PillionLast','NoKFirst'=>'NoKFirst','NoKLast'=>'NoKLast'];
$BONUS_FIELDS = [$IGNORE_COLUMN=>'ignore'];

// Load list of templates
$SPECFILES = ['_unknown'=>'unknown'];
$sql = "SELECT * FROM importspecs WHERE importType=0 ORDER BY specid";
$R = $DB->query($sql);
while ($rd = $R->fetchArray()) {
	$SPECFILES[$rd['specid']] = $rd['specTitle'];
}

// Loadup PhpSpreadsheet
require_once("vendor\autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;


function buildList($lst,$itm)
{
	if ($lst != '')
		$res = $lst.','.$itm;
	else
		$res = $itm;
	return $res;
}



function cleanBikename($bike)
{
	$words = explode(' ',$bike);
	for ($i = 0; $i < sizeof($words); $i++)
	{
		$wk = '';
		if (knownWord($words[$i],$wk))
			$words[$i] = $wk;
		else if (preg_match('/\\d/', $words[$i]) > 0)
			$words[$i] = strtoupper($words[$i]);
		else
			$words[$i] = properName(strtolower($words[$i]));
	}
	return implode(' ',$words);
}

function extendEntrantFields() {
	
	global $DB, $ENTRANT_FIELDS;

	$R = $DB->query("PRAGMA table_info(entrants)");
	while($rd = $R->fetchArray()) {
		$ENTRANT_FIELDS[$rd['name']] = $rd['name'];
	}
	asort($ENTRANT_FIELDS);
}


function getMergeCols($sheet,$row,$colspec,$sep = ' ')
// Extract and return the contents of one or more cells
{
	if ($colspec === NULL)
		return '';
	$cols = explode(':',$colspec);
	$res = '';
	for ($i = 0; $i < sizeof($cols); $i++)
	{
		if ($res <> '') $res .= $sep;
		//echo("  C=$cols[$i],R=$row  ");
		
		// PhpSpreadsheet uses columns starting at 1
		// PHPExcel (deprecated) used columns starting at 0
		// Need to add 1 to column values
		$res .= $sheet->getCellByColumnAndRow($cols[$i]+1,$row)->getValue();
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


function knownWord($w,&$formattedword)
{
	global $KNOWN_BIKE_WORDS;
	
	for ($i = 0; $i < sizeof($KNOWN_BIKE_WORDS); $i++)
		if (strtolower($w)==strtolower($KNOWN_BIKE_WORDS[$i]))
		{
			$formattedword = $KNOWN_BIKE_WORDS[$i];
			return true;
		}
	return false;
}


function loadSpecs()
{
	global $DB,$IMPORTSPEC,$TAGS;

	if (!isset($_REQUEST['specfile']))
		return;
	
	$sql = "SELECT * FROM importspecs WHERE specid='".$_REQUEST['specfile']."'";
	$R = $DB->query($sql);
	if (!$rd =$R->fetchArray())
		return;
		//die($TAGS['xlsNoSpecfile'][1]);
	

	try {
		eval($rd['fieldSpecs']);
	} catch (Exception $e) {
		die($e->getMessage());
	}
	
}

function loadSpreadsheet()
{
	global $DB,$TAGS,$IMPORTSPEC,$KONSTANTS,$target_dir;

	// Load the relevant specs

	loadSpecs();
	
	if (isset($_REQUEST['colmaps']))
		replaceColMaps();



	//Now we're doing it - load the spreadsheet

	startHtml($TAGS['ttImport'][0],$TAGS['xlsImporting'][0]);
	emitBreadcrumbs();


	$sheet = openWorksheet();
	$maxrow = $sheet->getHighestRow();
	$row = $IMPORTSPEC['FirstDataRow'];  // Skip the column headers
	$nrows = 0;

	echo("<p>".$TAGS['xlsImporting'][1]."</p>");

	// Check for overwriting
	$sql = "SELECT Count(*) AS Rex FROM entrants";
	$R = $DB->query($sql);
	$rr = $R->fetchArray();
	if ($rr['Rex'] > 0) {
		if (!isset($_REQUEST['force'])) {
			die($TAGS['xlsNotEmpty'][1]);
			exit;
		}
	}
	
	// All good now
	
	$R = $DB->query("PRAGMA table_info(entrants)");
	while($rd = $R->fetchArray()) {
		$fldval[$rd['name']] = $rd['dflt_value']; // Text values will come with single quotes
		$fldtyp[$rd['name']] = $rd['type'];
	}
	
	// These fields need handling beyond simple copying
	$specialfields = ['RiderName','RiderLast','RiderFirst',
						'PillionName','PillionLast','PillionFirst',
						'NoKName','NoKLast','NoKFirst',
						'Bike','Make','Model','BikeReg'
					];
	
	$DB->query("BEGIN TRANSACTION");
	$DB->query("DELETE FROM entrants");

	$SqlBuilt = FALSE;

	echo('<p class="techie">');

	$row = $row - 1;  // Step back one so I can bump below

	while ($row++ <= $maxrow) {
		if (isset($IMPORTSPEC['cols']['EntrantID'])) {
			$entrantid = getMergeCols($sheet,$row,$IMPORTSPEC['cols']['EntrantID']);
			if ($entrantid == '')
				break;				// Blank line so all done now
			if ($entrantid == 'X')	// Entrant flagged as withdrawn for whatever reason
				continue;
		} else
			$entrantid = '';
			
		// Should I be rejecting this entry?
		if (isset($IMPORTSPEC['reject']))
			foreach($IMPORTSPEC['reject'] as $col => $re) {
				$val = getMergeCols($sheet,$row,$col);
				if (preg_match($re,$val) === 1 ? TRUE : FALSE)
					continue 2; // Move to next entry row
			}

		// Am I positively selecting things?
		if (isset($IMPORTSPEC['select'])) {
			$ok = TRUE;
			foreach($IMPORTSPEC['select'] as $col => $re) {
				$val = getMergeCols($sheet,$row,$col);
				$ok = $ok && preg_match($re,$val);
			}
			if (!$ok)
				continue;
		}
		
		$ridernames = getNameFields($sheet,$row,array('RiderName','RiderFirst','RiderLast'));
		//echo('[ ');print_r($ridernames);echo(' ]');
		if (trim($ridernames[0])=='') 
				break;						// Blank line, all done

		$fldval['RiderName'] = properName(trim($ridernames[0]));
		$fldval['RiderFirst'] = properName(trim($ridernames[1]));

		if (isset($IMPORTSPEC['default']))
			foreach($IMPORTSPEC['default'] as $fld => $defval) {
				$fldval[$fld] = $defval;
				if (isset($IMPORTSPEC['setif'][$fld]))
					foreach($IMPORTSPEC['setif'][$fld] as $val => $mtch)
						if (preg_match($mtch[1],getMergeCols($sheet,$row,$mtch[0])))
							$fldval[$fld] = $val;
			}

		$pillionnames = getNameFields($sheet,$row,array('PillionName','PillionFirst','PillionLast'));
		$fldval['PillionName'] = properName(trim($pillionnames[0]));
		$fldval['PillionFirst'] = properName(trim($pillionnames[1]));

		$noknames = getNameFields($sheet,$row,array('NoKName','NoKFirst','NoKLast'));
		$fldval['NoKName'] = properName(trim($noknames[0]));

		$bike = getNameFields($sheet,$row,array('Bike','Make','Model'));
		if(preg_match($TAGS['ImportBikeTBC'][0],trim($bike[0])))
			$bike[0] = $TAGS['ImportBikeTBC'][1];

		$fldval['Bike'] = cleanBikename(trim($bike[0]));
			
		if (isset($IMPORTSPEC['cols']['BikeReg']))
			$fldval['BikeReg'] = strtoupper(trim(getMergeCols($sheet,$row,$IMPORTSPEC['cols']['BikeReg'])));


		foreach ($fldval as $col => $val)
			if (isset($IMPORTSPEC['cols'][$col]) && !array_search($col,$specialfields))
				$fldval[$col] = getMergeCols($sheet,$row,$IMPORTSPEC['cols'][$col]);
		
		$xtraData = '';
		foreach($IMPORTSPEC['data'] as $k => $kcol)
			$xtraData .= $k.'='.getMergeCols($sheet,$row,$IMPORTSPEC['data'][$k])."\n";
		$fldval['ExtraData'] = $xtraData;
		
		if (!$SqlBuilt) {
			$sql = "INSERT INTO entrants (";
			$fl = '';
			foreach ($fldval as $fld => $val) 
				$fl = buildList($fl,$fld);
			$sql .= $fl;
			$sql .= ") VALUES (";
			$fl = '';
			foreach ($fldval as $fld => $val) 
				$fl = buildList($fl,":$fld");
			$sql .= $fl;		
			$sql .= ")";
			
			try {
				$stmt = $DB->prepare($sql);
				if ($stmt == FALSE)
					die("Prepare failed ".$DB->lastErrorMsg().'<hr>'.$sql);
			} catch(Exception $e) {
				die($e->getMessage());
			}
			$SqlBuilt = TRUE;
		}
		try {
			foreach ($fldval as $fld => $val) {
				$typ = ($fldtyp[$fld] == 'INTEGER' ? SQLITE3_INTEGER : SQLITE3_TEXT);
				$stmt->bindValue(":$fld",$val,$typ);
			}
		
			echo($fldval["EntrantID"].' : '.$fldval["RiderName"].' - '.$fldval["Bike"]."<br>");
			$stmt->execute();
			$nrows++;
			
		} catch(Exception $e) {
			die("Caught ".$e->getMessage());
			break;
		}
	}
	$DB->query("COMMIT TRANSACTION");

	echo("</p><p>All done - $nrows rows loaded </p>");
	
} //loadSpreadsheet



function openWorksheet()
{
	global $target_dir,$IMPORTSPEC;
	
	$filetype = \PhpOffice\PhpSpreadsheet\IOFactory::identify($target_dir.$IMPORTSPEC['xlsname']);

	$rdr = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($filetype);

	$rdr->setReadDataOnly(true);
	$rdr->setLoadSheetsOnly($IMPORTSPEC['whichsheet']);
	try {
		$xls = $rdr->load($target_dir.$IMPORTSPEC['xlsname']);
	} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
		die("Error: ".$e->getMessage());
	}

	$sheet = $xls->getSheet($IMPORTSPEC['whichsheet']);
	return $sheet;
}

function previewSpreadsheet()
{
	global $DB,$target_dir, $ENTRANT_FIELDS, $IMPORTSPEC, $IGNORE_COLUMN;
	
	loadSpecs();
	
	if (!isset($IMPORTSPEC['xlsname'])) 
		return;

	//echo('1 .. ');
	$sheet = openWorksheet();
	//echo('2 .. ');
	$maxcol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
	//echo('3 .. ');
	// Build column lookup by name table
	for ($i=1; $i<=$maxcol;$i++)
	{
		$colname = $sheet->getCellByColumnAndRow($i,1)->getValue();
		$XLSFIELDS[$colname]=$i;
	}
	
	
	//echo('preview-4 ');

	$row = $IMPORTSPEC['FirstDataRow'];  // Skip the column headers
	$row = 0;
	
	//print_r($IMPORTSPEC['cols']);
	echo('<table>');
	echo('<tr>');
	for ($col = 1; $col <= $maxcol; $col++) {
		echo('<td><select name="colmaps[]" class="fldsel">');
		$selfld = '';
		foreach($ENTRANT_FIELDS as $k => $v) {
			echo('<option value="'.$k.'"');
			if (isset($IMPORTSPEC['cols']) && array_search($col - 1,$IMPORTSPEC['cols'])==$k){
				$selfld = $k;
				echo(" selected ");
			} else if ($k == $IGNORE_COLUMN && $selfld == '')
				echo(" selected ");
			echo('>'.$v.'</option>');
		}
		echo('</select></td>');
	}
	echo('</tr>');
	while ($row++ < 10)
	{
		echo('<tr>');
		$col = 0;
		while ($col++ <= $maxcol)
			echo('<td>'.$sheet->getCellByColumnAndRow($col,$row)->getValue().'</td>');
		echo('</tr>');
	}
	echo('</table>');
}

function processUpload()
{
	global $IMPORTSPEC, $target_dir, $TAGS, $upload_state;
	
	if(isset($_FILES['fileid']['tmp_name']) && $_FILES['fileid']['tmp_name']!='')
	  if (!move_uploaded_file($_FILES['fileid']['tmp_name'],$target_dir.$IMPORTSPEC['xlsname']))
		die('Upload failed ['.$_FILES['fileid']['tmp_name'].']==>['.$target_dir.$IMPORTSPEC['xlsname'].']');
	$upload_state = 2;
}


function replaceColMaps()
{
	global $IMPORTSPEC;
	
	if (isset($IMPORTSPEC['cols'])) {
		$ck = array_keys($IMPORTSPEC['cols']);
		$nc = count($ck);
		for ($i = 0; $i < $nc; $i++)
			unset ($IMPORTSPEC['cols'][$ck[$i]]);
	}
	$ck = array_keys($_REQUEST['colmaps']);
	$nc = count($ck);
	for ($i = 0; $i < $nc; $i++)
		if ($_REQUEST['colmaps'] != '')
			$IMPORTSPEC['cols'][$_REQUEST['colmaps'][$i]] = $i;
}


function showUpload()
{
	global $TAGS, $SPECFILES, $upload_state;
	
	startHtml($TAGS['ttUpload'][0]);
?>

<form id="uploadxls" action="importxls.php" method="post" enctype="multipart/form-data">
<?php
	pushBreadcrumb('#');
	emitBreadcrumbs();

	echo('<h2>'.$TAGS['UploadEntrantsH1'][1].'</h2>');
	$myfile = (isset($_REQUEST['filename']) ? htmlentities(basename($_REQUEST['filename']))	: '');
?>


<input type="hidden" id="fileuploaded" name="fileuploaded" value="<?php echo($upload_state);?>">
<span class="vlabel" <?php echo(($myfile=='' ? ' style="display:none;">' : '>'));?>
<label for="filename">File loaded</label> 
<input type="text" readonly name="filename" id="filename" <?php echo(' value="'.$myfile.'" ');?>> 
<button onclick="document.querySelector('#filepick').style.display='block';this.disabled=true;return false;">Choose another</button>
</span>
<span class="vlabel" id="filepick"<?php echo(($myfile!='' ? ' style="display:none;">' : '>'));?>
<label for="fileid"><?php echo($TAGS['UploadPickFile'][1]);?> </label>
<input type="file" name="fileid" id="fileid" onchange="document.querySelector('#fileuploaded').value=1;document.querySelector('#filename').value=this.value;document.querySelector('#uploadxls').submit();">
</span>
<?php
	$chk = isset($_REQUEST['specfile']) ? $_REQUEST['specfile'] : '';
	$i = 0;
	if (!isset($_REQUEST['fileuploaded'])) 
		return;
	
	echo('<span class="vlabel">');
	echo('<label for="specfile1">File format</label> ');
	echo('<select name="specfile" onchange="'."document.querySelector('#uploadxls').submit();".'">');
	foreach ($SPECFILES as $spc => $specs)
	{
		$i++;
		echo('<option id="specfile'.$i.'"');
		if ($chk==$spc) {
			echo(' selected');
			$chk = FALSE;
		}
		echo(' value="'.$spc.'">'.$specs.'</option>');
	}
	echo('</select>');
	echo('</span>');
	
	previewSpreadsheet();
		
	
?>

<br><br>
<label for="force"><?php echo($TAGS['UploadForce'][1]);?> </label>
<input type="checkbox" name="force" id="force">

<br><br>
<input type="submit" name="load" value="<?php echo($TAGS['Upload'][0]);?>">
</form>
<?php
}






// Mainline here

extendEntrantFields();

//print_r($ENTRANT_FIELDS);


if (isset($_REQUEST['fileuploaded']) && $_REQUEST['fileuploaded']=='1')
{
	//echo('a ... ');
	processUpload();
	//echo('b ... ');
	showUpload();
	//echo('ccc ... ');
	exit;
}


//if (!isset($_REQUEST['specfile']))
//	die($TAGS['xlsNoSpecfile'][1]);

if (isset($_REQUEST['load'])) {
	loadSpreadsheet();
	exit;
}

showUpload();

?>
