<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle certificate printing
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

/*
 * 	2.1	seq=cols
 *
 */
require_once('common.php');
 
$CERTOPTS = [];

// Debug facility to include unranked entrants (normally false)
$ShowPositionZero = false;

function formattedDate($isodate)
{
	$dt = DateTime::createFromFormat("Y-m-d",$isodate);
	$res = $dt->format("j\<\s\u\p\>S\<\/\s\u\p\> F Y");
	return $res;
}

function formattedDateRange($daterange)
{
	$dts = explode(';',$daterange);
	try
	{
		$dt = explode('T',$dts[0]);
		$dt1 = DateTime::createFromFormat('Y-m-d',$dt[0]);
		$dt = explode('T',$dts[1]);
		$dt2 = DateTime::createFromFormat('Y-m-d',$dt[0]);
		if ($dt1 == $dt2)
			return $dt1->format("j\<\s\u\p\>S\<\/\s\u\p\> F Y");
		
		if ($dt1->format('m')==$dt2->format('m'))
		{
			if (intval($dt2->format('d')) == intval($dt1->format('d'))+1)
				$hyphen = ' / ';
			else
				$hyphen = ' - ';
			$res = $dt1->format("j\<\s\u\p\>S\<\/\s\u\p\>").$hyphen.$dt2->format("j\<\s\u\p\>S\<\/\s\u\p\>");
			$res .= ' ';
			$res .= $dt2->format('F Y');
		} else if ($dt1->format('Y') == $dt2->format('Y'))
		{
			$res = $dt1->format("j\<\s\u\p\>S\<\/\s\u\p\> F").' - '.$dt2->format("j\<\s\u\p\>S\<\/\s\u\p\> F Y");
		} else
		{
			$res = $dt1->format("j\<\s\u\p\>S\<\/\s\u\p\> F Y").' - '.$dt2->format("j\<\s\u\p\>S\<\/\s\u\p\> F Y");
		}
	} catch (Exception $e) {
		echo($e->getMessage());
	}
		
	return $res;
	
}

function crewNames($fldval)
{
	$names = explode(';',$fldval);
	if ($names[1]=='')
		return $names[0];
	else
		return $names[0].' &amp; '.$names[1];
}
function spellNumber($n)
{
$ones = array(
 "",
 "one",
 "two",
 "three",
 "four",
 "five",
 "six",
 "seven",
 "eight",
 "nine",
 "ten",
 "eleven",
 "twelve",
 "thirteen",
 "fourteen",
 "fifteen",
 "sixteen",
 "seventeen",
 "eighteen",
 "nineteen"
);

$tens = array(
 "",
 "",
 "twenty",
 "thirty",
 "forty",
 "fifty",
 "sixty",
 "seventy",
 "eighty",
 "ninety"
);

	if ($n >= 100)
		return strval($n);
	$ntens = floor($n / 10);
	$nones = $n % 10;
	$restens = $tens[$ntens];
	$resones = $ones[$nones];
	if ($restens != '' && $resones != '')
		return $restens.'-'.$resones;
	else
		return $restens.$resones;
}

function formattedPlace($place)
{
	switch($place)
	{
		case 11:
		case 12:
		case 13:
			$sup = 'th';
			break;
		default:
			$mod = $place % 10;
			switch($mod)
			{
				case 1: $sup = 'st'; break;
				case 2: $sup = 'nd'; break;
				case 3: $sup = 'rd'; break;
				default:
					$sup = 'th';			
			}
	}
	return $place.'<sup>'.$sup.'</sup>';
}
function formattedRallyTitle($rt,$full,$split)
{
	//echo("frt=".$rt." full?".$full." split?".$split." &nbsp;&nbsp;&nbsp;  ");
	$p = strpos($rt,'[');
	$q = strpos($rt,']');
	if ($full || $p == false) // No removeable section
		$res = str_replace('[','',str_replace(']','',$rt));
	else
	{
		if ($q == false || $q < $p)
			$res = str_replace('[','',str_replace(']','',$rt));
		else
		{
			if ($p > 0)
				$res = substr($rt,0,$p-1);
			else
				$res = '';
			$res .= substr($rt,$q+1);
		}
	}
	if ($split)
		return str_replace('|','<br>',$res);
	else
		return str_replace('|','',$res);

}
function formattedField($fldname,$fldval)
{
	//echo('fF{'.$fldname.'=='.$fldval.'}');
	if (preg_match("/DateRallyRange/",$fldname)) {
		return formattedDateRange($fldval);
	} else if (preg_match("/RallyTitleSplit/",$fldname)) {
		return formattedRallyTitle($fldval,true,true);
	} else if (preg_match("/RallyTitleShort/",$fldname)) {
		return formattedRallyTitle($fldval,false,false);
	} else if (preg_match("/RallyTitle/",$fldname)) {
		return formattedRallyTitle($fldval,true,false);
	} else if (preg_match("/FinishPosition/",$fldname)) {
		return formattedPlace($fldval);
	} else if (preg_match("/Crew/",$fldname)) {
		return crewNames($fldval);
	} else if (preg_match("/Date/",$fldname))	{
		return formattedDate($fldval);
	} else if (preg_match("/CorrectedMiles|TotalPoints/",$fldname)) {
		return number_format($fldval,0);
	} else
		return $fldval;
}

function parseCertificateOptions($opts)
{
	global $CERTOPTS;
	
	$optarray = explode(';',$opts);
	foreach ($optarray as $opt)
	{
		$kv = explode('=',trim($opt));
		$CERTOPTS[$kv[0]] = $kv[1];
	}
}	

function fetchCertificateText($EntrantID)
{
	global $DB, $TAGS, $KONSTANTS, $CERTOPTS;
	
	$Miles2Kilometres = 1.60934;

	$sql = "SELECT * FROM certificates WHERE EntrantID=";
	// Try for a specific text for this entrant
	$R = $DB->query($sql.$EntrantID);
	if (!$rd = $R->fetchArray())
	{
		if ($EntrantID == 0)
			return;
		// No specific text, fetch the master text
		// First, fetch the class value from the entrant rec
		$R = $DB->query("SELECT Class FROM entrants WHERE EntrantID=$EntrantID");
		if (!$rd = $R->fetchArray())
			return;
		
		$R = $DB->query($sql.'0'.' AND Class='.$rd['Class']);
		if (!$rd = $R->fetchArray())
			return;
	}
	if ($CERTOPTS==[])
		parseCertificateOptions($rd['options']);
	$res = $rd['html'];
	$css = $rd['css'];

	//echo (" @@@ ");
	
	if ($EntrantID <> 0)
	{
		$sql = "SELECT *, rallyparams.StartTime || ';' || rallyparams.FinishTime AS DateRallyRange, RiderName || ';' || COALESCE(PillionName,'') AS CrewName,RiderFirst || ';' || COALESCE(PillionFirst,'') AS CrewFirst, RallyTitle AS RallyTitleSplit, RallyTitle AS RallyTitleShort FROM rallyparams JOIN entrants WHERE EntrantID=".$EntrantID;
		$R = $DB->query($sql);
		$rd = $R->fetchArray();

		$mt = [];
		preg_match_all("/(#[\\w]+#)/",$res,$mt,PREG_SET_ORDER);
		foreach ($mt as $fld)
		{
			$fldname = substr($fld[0],1,strlen($fld[0])-2);
			//echo (" [ $fldname ] ");
			try
			{
				$res = str_replace($fld,formattedField($fldname,$rd[$fldname]),$res);
			}
			catch(Exception $e)
			{
				echo(" Error: ".$e->getMessage()."; ");
			}
		}
	}


	return ['html'=>$res,'css'=>$css];
}

function startCertificateHtml($css)
{
?><!DOCTYPE html>
<html>
<head>
<title>ScoreMaster:Certificates</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
<!--	
<?php
echo($css);
?>

.main
{
	text-align: justify;
}
.popmenu
{
width: 10em;
height: 6em;
background: lightgray;
border: single;
}
.popmenu ul
{
	list-style-type: none;
}
@media print {
.noprint		{ display: none; }
}

-->
</style>
</head>
<body>
<?php
}

function certificateMenuText($EntrantID,$CertName)
{
	$mnu = "<div class=\"noprint popmenu\">";
	$mnu .= "<p>$EntrantID $CertName</p>";
	$mnu .= "<ul>";
	$mnu .= "<li>Save this certificate</li>";
	$mnu .= "</ul>";
	$mnu .= "</div>";
	return $mnu;
}

function viewCertificate($EntrantID=0,$DontStart=false,$DontTail=false)
{
	global $DB, $TAGS, $KONSTANTS;
	
	//echo(' fetching certificate ... ');
	$rd = fetchCertificateText($EntrantID);
	//echo(' certificate fetched ... ');
	if (!$DontStart)
		StartCertificateHtml($rd['css']);
	echo('<div class="certificate" contenteditable="true">');
	//echo(certificateMenuText($EntrantID,'Crap'));
	echo($rd['html']);
	echo('</div>');
	if (!$DontTail)
		echo('</body></html');
}

function repeatFirstCertificate()
{
	global $CERTOPTS;
	//var_dump($CERTOPTS);
	switch($CERTOPTS['optDoublefirst'])
	{
		case 'OFF':
			return false;
		case 'AUTO':
			$browser = $_SERVER['HTTP_USER_AGENT'];
			//echo("Browser=".$browser."; match=".$CERTOPTS['doublebrowser'].";");
			return preg_match($CERTOPTS['txtDoublebrowser'],$browser);
	}
	return true;
}

function viewCertificates()
{
	global $DB, $TAGS, $KONSTANTS, $CERTOPTS, $ShowPositionZero;

	//echo("Fetching records to print<br>");
	$sortspec = 'FinishPosition DESC';
	if (isset($_REQUEST['seq']))
		$sortspec = $_REQUEST['seq'];
	$sql = "SELECT EntrantID FROM entrants WHERE EntrantStatus=".$KONSTANTS['EntrantFinisher'].' AND FinishPosition>';
	if ($ShowPositionZero)
		$sql .= '=';
	$sql .= '0 ORDER BY '.$sortspec;
	$R = $DB->query($sql);
	$ds = false;
	while ($rd = $R->fetchArray())
	{
		//echo("Printing ".$rd['EntrantID']."<br>");
		viewCertificate($rd['EntrantID'],$ds,true);
		if (!$ds && repeatFirstCertificate())
			viewCertificate($rd['EntrantID'],true,true);
		$ds = true;
	}
	//echo '<hr>'.$_SERVER['HTTP_USER_AGENT'];
	if (!$ds)
	{
		StartCertificateHtml('');
		echo($TAGS['NoCerts2Print'][0]);
	}
	echo('</body></html>');
}

function saveCertificate()
{
	global $DB, $TAGS, $KONSTANTS;

	$page_detail = $_REQUEST['certtext'];
	$EntrantID = $_REQUEST['EntrantID'];
	
	$sql = "INSERT OR REPLACE INTO certificates (EntrantID,html) VALUES(".$EntrantID.",'".safesql($page_detail)."')";
	$DB->exec($sql);
	
}

if ($_REQUEST['show0'])
	$ShowPositionZero = true;

if ($_REQUEST['c']=='viewcert')
{
	if (isset($_REQUEST['EntrantID']))
		$eid = $_REQUEST['EntrantID'];
	else
		$eid = 0;
	viewCertificate($eid,false,false);
	exit;
}
if ($_REQUEST['c'] == 'showcerts')
{
	viewCertificates();
	exit;
}


?>
