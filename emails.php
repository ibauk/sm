<?php

/*
 * I B A U K   -   S C O R E M A S T E R
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

 

$HOME_URL = 'admin.php';

require_once('common.php');
require_once("vendor\autoload.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/PHPMailer/PHPMailer-master/src/Exception.php';
require 'vendor/PHPMailer/PHPMailer-master/src/PHPMailer.php';
require 'vendor/PHPMailer/PHPMailer-master/src/SMTP.php';

require_once('certificate.php');

function newMailer()
{
	$params = json_decode(getValueFromDB("SELECT EmailParams FROM rallyparams","EmailParams","{}"));
	
	$mail = new PHPMailer();

	foreach ($params as $key => $val) 
		switch($key) {
			case 'SetFrom':
				$mail->SetFrom($val[0],$val[1]);
				break;
			default:
				$mail->$key = $val;
		}

	$mail->IsSMTP();
	$mail->Mailer = "smtp";
	$mail->IsHTML(true);
	
	return $mail;
}


function cleanFilename($filename)
{
	return str_replace(' ','_',$filename); // Crude sanity check
}



function setupEmailRun()
{
	global $DB, $KONSTANTS, $TAGS;
	
	startHtml($TAGS['ttEmails'][0]);

?>
<link rel="stylesheet" href="jodit/jodit.min.css"/>
<script src="jodit/jodit.min.js"></script>
<?php
	
	pushBreadcrumb('#');
	emitBreadcrumbs();

?>
<script>
<!--
function countrecs() {
	let dns = document.getElementById('EntrantDNS').checked;
	let ok = document.getElementById('EntrantOK').checked;
	let finisher = document.getElementById('EntrantFinisher').checked;
	let dnf = document.getElementById('EntrantDNF').checked;
	let ids = document.getElementById('EntrantID').value;
	let status = '';
	if (dns) status = ' '+EntrantDNS;
	if (ok) status += ' '+EntrantOK;
	if (finisher) status += ' '+EntrantFinisher;
	if (dnf) status += ' '+EntrantDNF;
	if (status != '') status = status.trim().replace(/ /g,',');
	let sql = (status != '' ? 'EntrantStatus In ('+status+')' : '');
	if (ids != '') {
		if (sql != '')
			sql += ' or ';
		sql += 'EntrantID In ('+ids+')';
	}
	console.log('sql.where = '+sql);
	document.getElementById('wheresql').value = sql;
	if (sql == '') {
		document.getElementById("selectedcount").innerHTML = '0';
		document.getElementById('sendmail').disabled = true;
		return;
	}

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("selectedcount").innerHTML = this.responseText;
			let rex = parseInt(this.responseText);
			document.getElementById('sendmail').disabled = rex < 1;
		}
	};
	xhttp.open("GET", "emails.php?c=count&where="+sql, true);
	xhttp.send();
	
}
function showEntrants() {
	let ids = document.getElementById('EntrantID').value
	let names = document.getElementById('entrantnames');
	names.innerHTML = '';
	if (ids=='') return;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			names.innerHTML = this.responseText;
		}
	};
	xhttp.open("GET", "emails.php?c=names&e="+ids, true);
	xhttp.send();
}
function validate() {
	let fld = document.getElementById('Subject');
	if (fld.value=='') {
		console.log('Subject is blank!');
		fld.focus();
		return false;
	}
	fld = document.getElementById('Body');
	if (fld.value=='') {
		console.log('Body is blank!');
		fld.focus();
		return false;
	}
	return true;
}
-->
</script>
<?php	
	echo('<h4>'.$TAGS['ttEmails'][1].'</h4>');
	
	
	echo('<form method="post" action="emails.php" enctype="multipart/form-data" onsubmit="return validate();">');
	echo('<input type="hidden" name="c" value="email">');
	echo('<input type="hidden" id="wheresql" name="wheresql">');
	
	echo('<span class="vlabel" title="'.$TAGS['em_EntrantStatus'][1].'"><label  style="vertical-align: middle;" for="EntrantDNS">'.$TAGS['em_EntrantStatus'][0].'</label> ');
	echo('<label for="EntrantDNS">'.$TAGS['EntrantDNS'][0].'</label> <input onchange="countrecs();" type="checkbox" id="EntrantDNS" name="EntrantStatus" value="'.$KONSTANTS['EntrantDNS'].'"> ');
	echo('<label for="EntrantOK">'.$TAGS['EntrantOK'][0].'</label> <input onchange="countrecs();" type="checkbox" id="EntrantOK" name="EntrantStatus" value="'.$KONSTANTS['EntrantOK'].'"> ');
	echo('<label for="EntrantFinisher">'.$TAGS['EntrantFinisher'][0].'</label> <input onchange="countrecs();" type="checkbox" id="EntrantFinisher" name="EntrantStatus" value="'.$KONSTANTS['EntrantFinisher'].'"> ');
	echo('<label for="EntrantDNF">'.$TAGS['EntrantDNF'][0].'</label> <input onchange="countrecs();" type="checkbox" id="EntrantDNF" name="EntrantStatus" value="'.$KONSTANTS['EntrantDNF'].'"> ');
	//echo('</span>');
	
	//echo('<span class="vlabel" title="'.$TAGS['em_EntrantID'][1].'"><label  style="vertical-align: middle;" for="EntrantID">'.$TAGS['em_EntrantID'][0].'</label> ');
	echo('<br><label  style="vertical-align: middle;" for="EntrantID">'.$TAGS['em_EntrantID'][0].'</label> ');
	echo('<input type="text" name="EntrantID" id="EntrantID" onchange="countrecs();showEntrants();" > ');
	echo('<span id="entrantnames"></span>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['em_Subject'][1].'"><label for="Subject">'.$TAGS['em_Subject'][0].'</label> ');
	echo('<input placeholder="'.$TAGS['em_NotBlank'][0].'" type="text" name="Subject" id="Subject" class="textarea" style="width:20em;">');
	echo('</span>');
	
	//echo('<span class="vlabel" title="'.$TAGS['em_Body'][1].'"><label for="Body" style="vertical-align: top;">'.$TAGS['em_Body'][0].'</label> ');
	echo('<span class="vlabel" title="'.$TAGS['em_Body'][1].'">');
	echo('<textarea   title="'.$TAGS['em_Body'][1].'" placeholder="'.$TAGS['em_NotBlank'][0].'" id="Body" name="Body" cols="80" rows="10" ></textarea>');
	echo('</span>');

	echo('<span class="vlabel" title="'.$TAGS['em_includeScorex'][1].'">');
	echo('<label for="includeScorex">'.$TAGS['em_includeScorex'][0].' </label> ');
	echo('<input type="checkbox" name="includeScorex" id="includeScorex"> ');
	echo('</span>');
	//echo('<span class="vlabel" title="'.$TAGS['em_Signature'][1].'"><label for="Signature" style="vertical-align: top;">'.$TAGS['em_Signature'][0].'</label> ');
	echo('<span class="vlabel" title="'.$TAGS['em_Signature'][1].'">');
	echo('<textarea  title="'.$TAGS['em_Signature'][1].'" id="Signature" name="Signature" cols="80" rows="2" ></textarea>');
	echo('</span>');
	
	echo('<span class="vlabel" title="'.$TAGS['em_includeCertificate'][1].'">');
	echo('<label for="includeCertificate">'.$TAGS['em_includeCertificate'][0].' </label> ');
	echo('<input type="checkbox" name="includeCertificate" id="includeCertificate"> ');
	echo('</span>');
		

	echo('<span class="vlabel" title="'.$TAGS['em_Attachment'][1].'">');
	echo('<label for="Attachment">'.$TAGS['em_Attachment'][0].'</label> ');
	echo('<input id="Attachment" name="Attachment[]" type="file" multiple>');
	echo('</span>');

	
	echo('<span class="vlabel" title="'.$TAGS['em_NumberSelected'][1].'"><label >'.$TAGS['em_NumberSelected'][0].'</label> ');
	echo('<span id="selectedcount" style="font-size:larger; font-weight: bold; padding-left:.5em;">0</span>');
	echo(' <input disabled type="submit" id="sendmail" value="'.$TAGS['em_Submit'][0].'">');
	echo('</span>');
	
	echo('</form>');

?>
<script>
var bodyJodit = new Jodit('#Body', {
       //
beautifyHTMLCDNUrlsJS: '',
useAceEditor: false,
sourceEditorCDNUrlsJS : '',
width: '50em',
height: '15em',
tabIndex: '0',
uploader: {'insertImageAsBase64URI':true},

buttons: [
	'source','|',
	'bold',
	'strikethrough',
	'underline',
	'italic',
	'|',
	'font',
	'fontsize',
	'brush',
	'align',
	'|',
	'image',
	'link',
	//,'about'
	] // Buttons
});
var sigJodit = new Jodit('#Signature', {
       //
beautifyHTMLCDNUrlsJS: '',
useAceEditor: false,
sourceEditorCDNUrlsJS : '',
width: '50em',
//minHeight: '2em',
tabIndex: '0',
uploader: {'insertImageAsBase64URI':true},

buttons: [
	'source','|',
	'bold',
	'strikethrough',
	'underline',
	'italic',
	'|',
	'font',
	'fontsize',
	'brush',
	'align',
	'|',
	'image',
	'link',
	//,'about'
	] // Buttons
});
	
</script>

<?php



	
	echo('</body></html>');
}

function getCountWhere($where)
{

	$sql = "SELECT count(*) As Rex FROM entrants WHERE ".$where;
	return getValueFromDB($sql,"Rex",0);
}

function echoCountWhere()
{
	$where = $_REQUEST['where'];
	echo(getCountWhere($where));
}

function getNames($ids)
{
	global $DB;
	
	$sql = "SELECT RiderName FROM entrants WHERE EntrantID In ($ids)";
	$R = $DB->query($sql);
	$res = '';
	while ($rd = $R->fetchArray())
		$res .= ($res != '' ? ', '.$rd['RiderName'] : $rd['RiderName']);
	return $res;
}

function echoNames()
{
	$ids = $_REQUEST['e'];
	echo(getNames($ids));
}


function sendMail()
{
	global $DB, $KONSTANTS, $TAGS;
	
	foreach(['wheresql','Subject','Body'] as $key)
		if (!isset($_REQUEST[$key]))
			return;
	$recipients = getCountWhere($_REQUEST['wheresql']);
	if ($recipients < 1)
		return;
	
	
	$mail = newMailer();
	$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
	$mail->SMTPDebug = 0;
	$mail->Subject = $_REQUEST['Subject'];


	$sql = "SELECT EntrantID, RiderName, Email, ScoreX, EntrantStatus FROM entrants WHERE ".$_REQUEST['wheresql'];
	$R = $DB->query($sql);
	while ($rd = $R->fetchArray()) {
		$mail->clearAddresses();
		$mail->clearAttachments();		
		try {
			$mail->AddAddress($rd['Email'],$rd['RiderName']);
		} catch (Exception $e) {
			echo('******* '.htmlspecialchars($rd['Email']).' - '.htmlspecialchars($rd['RiderName']).'<br>');
			continue;
		}
		$msg = '<p>'.$_REQUEST['Body'].'</p>';
		if (isset($_REQUEST['includeScorex']) && ($rd['EntrantStatus']==$KONSTANTS['EntrantFinisher'] || $rd['EntrantStatus']==$KONSTANTS['EntrantDNF']) )
			$msg .= '<p>'.$rd['ScoreX'].'</p>';
		if (isset($_REQUEST['Signature']))
			$msg .= '<p>'.$_REQUEST['Signature'].'</p>';
		$mail->MsgHTML($msg);
		if (isset($_REQUEST['includeCertificate']) && $rd['EntrantStatus']==$KONSTANTS['EntrantFinisher'])
			$mail->addStringAttachment(getViewCertificate($rd['EntrantID']), 'certificate.html');
		if (isset($_FILES['Attachment']) && $_FILES['Attachment']['name'][0] != '') {
			$nfiles = count($_FILES['Attachment']['name']);
			error_log("Attaching $nfiles files");
			for ($i = 0; $i < $nfiles; $i++) {
				$filename = cleanFilename($_FILES['Attachment']['name'][$i]); 
				$uploaded = joinPaths($KONSTANTS['UPLOADS_FOLDER'],$filename);
				error_log("Moving $filename to $uploaded");
				if (!move_uploaded_file($_FILES['Attachment']['tmp_name'][$i],$uploaded))
					die('!!!!!!!!! ['.$_FILES['Attachment']['tmp_name'][$i].']==>['.$uploaded.']');
				$mail->addAttachment($uploaded,$filename);
			}
		}
		try {
			//$mail->Send();
			echo(htmlspecialchars($rd['Email']).' - '.htmlspecialchars($rd['RiderName']).'<br>');
		} catch (Exception $e) {
			echo('******* '.htmlspecialchars($rd['Email']).' - '.htmlspecialchars($rd['RiderName']).' ('.$mail->ErrorInfo.')<br>');
			$mail->getSMTPInstance()->reset();
		}
	}

	
}

function prgCleanForm()
/*
 * prg = post/redirect/get
 *
 * Called to get browser to ask for picklist after a post
 *
 */
{
	$get = "emails.php";
	header("Location: ".$get);
	exit;
}

//var_dump($_FILES);
//echo('<hr>');
//var_dump($_REQUEST);
//echo('<hr>');

if (isset($_REQUEST['c'])) {
	switch($_REQUEST['c']) {
		case 'count':
			echoCountWhere();
			exit;
		case 'names':
			echoNames();
			exit;
		case 'email':
			sendMail();
			if (!retraceBreadcrumb())
				prgCleanForm();
			
			
	}
}


setupEmailRun();

?>
