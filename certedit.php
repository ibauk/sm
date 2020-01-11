<?php
// certedit.php
/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I provide wysiwyg certificate editing
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



require_once('common.php');

// These database fields should not be included in the dropdown picklist
$EXCLUDE_FIELDS = array(
	"AutoRank","BCMethod","BonusesVisited","CombosTicked","DBState","DBVersion",
	"Cat1Label","Cat2Label","Cat3Label","Cat4Label","Cat5Label","Cat6Label","Cat7Label","Cat8Label","Cat9Label",
	"ExtraData","MaxMilesMethod","NoKName","NoKRelation","NoKPhone","OdoCheckMiles","OdoCheckStart","OdoCheckFinish",
	"OdoKms","RejectReasons","RejectedClaims","ScoreX","ScoredBy","ScoringMethod","ScoringNow","ShowMultipliers",
	"SpecialsTicked","TeamRanking","TiedPointsRanking","EntrantStatus"
);

// These non-database fields should be included in the dropdown picklist`
$CERT_FIELDS = array(
	"DateRallyRange"		=> "DateRallyRange",
	"RallyTitleSplit"		=> "RallyTitleSplit",
	"RallyTitleShort"		=> "RallyTitleShort"
	
);


// Alphabetical order from here on down. Mainline at EOF


function editCertificateW() {
	
	global $DB, $TAGS, $KONSTANTS;

	$EntrantID = (isset($_REQUEST['EntrantID']) ? intval($_REQUEST['EntrantID']) : 0);
	$class = (isset($_REQUEST['Class']) ? intval($_REQUEST['Class']) : 0);
	$rd = fetchCertificateW($EntrantID,$class);
	
	startHtmlW();
	
	echo('<form id="certform" method="post" action="certedit.php" ');
	echo('onsubmit="return document.querySelector('."'#savehtml'".').value=document.querySelector('."'#editor-container>.ql-editor'".').innerHTML;">');
	
	pushBreadcrumb('#');
	emitBreadcrumbs();
	
	echo('<input type="hidden" name="c" value="editcert">');
	echo('<input type="hidden" name="EntrantID" value="'.$EntrantID.'">');

	echo('<div class="editwControls" style="font-size:.8em;">');
	
	// Make provision for multiple classes 
	$MC = getValueFromDB("SELECT count(*) As Rex FROM certificates WHERE EntrantID=$EntrantID","Rex",0);
	if ($MC > 1)
	{
		$R = $DB->query("SELECT Class,Title FROM certificates WHERE EntrantID=$EntrantID ORDER BY Class");
		if ($DB->lastErrorCode() <> 0)
			echo($DB->lastErrorCode().' == '.$DB->lastErrorMsg().'<br>'.$sql.'<hr>');
		$pv = "document.getElementById('Class').value=this.value;";
		$pv .= "var T=this.options[this.selectedIndex].text;";
		$pv .= "document.getElementById('Title').value=T.split(' - ')[1];";
		$pv .= "document.getElementById('certcss').disabled=true;";
		$pv .= "document.getElementById('certhtml').disabled=true;";
		$pv .= "document.getElementById('fetchcert').disabled=false;";
		$pv .= "document.getElementById('fetchcert').click();";
	
		echo('<select onchange="'.$pv.'">');
		while ($rrd = $R->fetchArray())
		{
			echo('<option value="'.$rrd['Class'].'"');
			if ($rrd['Class'] == $rd['Class']) {
				echo(' selected ');
			}
			echo('>'.$rrd['Class'].' - '.$rrd['Title'].'</option>');
		}
		echo('</select> ');
	}

	echo('<label for="Class">'.$TAGS['Class'][0].' </label>');
	$x = ' onchange="document.getElementById('."'".'fetchcert'."'".').disabled=false;"';
	echo('<input title="'.$TAGS['Class'][1].'" type="number" min="0" name="Class" id="Class" value="'.$class.'" '.$x.' class="smallnumber"> ');
	
	echo('<input type="submit" disabled id="fetchcert" name="fetchcert" value="'.$TAGS['FetchCert'][0].'" title="'.$TAGS['FetchCert'][1].'"> ');
	echo('<label for="Title">'.$TAGS['CertTitle'][0].' </label>');
	echo('<input title="'.$TAGS['CertTitle'][1].'" type="text" name="Title" id="Title" value="'.$rd['Title'].'" > ');

	

	
	
	echo('<input type="submit" disabled name="savecert" value="'.$TAGS['RecordSaved'][0].'" id="savedata" data-altvalue="'.$TAGS['SaveCertificate'][0].'" title="'.$TAGS['SaveCertificate'][1].'"> ');

	echo('</div>');
	echo('<input type="hidden" name="certhtml" value="" id="savehtml">');
	echo('</form>');

	loadTableData();
	
	emitContainers($rd['html']);
	echo('</body></html>');
	
	//showFooter();
}


function emitContainers($rtf) {

	global $CERT_FIELDS, $TAGS;
	
?>
<div id="standalone-container" style="max-width: 210mm; height: 297mm; margin: 1em 0;">
  <div id="toolbar-container" style="font-size: .6em;">
    <span class="ql-formats">
      <select class="ql-font"></select>
      <select class="ql-size"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-bold"></button>
      <button class="ql-italic"></button>
      <button class="ql-underline"></button>
      <button class="ql-strike"></button>
    </span>
    <span class="ql-formats">
    <button class="ql-align" value=""></button>
    <button class="ql-align" value="center"></button>
    <button class="ql-align" value="right"></button>
    <button class="ql-align" value="justify"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-list" value="ordered"></button>
      <button class="ql-list" value="bullet"></button>
      <button class="ql-indent" value="-1"></button>
      <button class="ql-indent" value="+1"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-image"></button>
    </span>
  </div>
  
   <div id="editor-container" class="certframe" >
	<div class="ql-editor" data-gramm="false" contenteditable="true"><?php echo($rtf);?></div>
   </div>
</div>


<script>
function showEditor() {
	
// Code to implement Shift+Enter = <BR>
var Delta = Quill.import('delta');
let Break = Quill.import('blots/break');
let Embed = Quill.import('blots/embed');

function lineBreakMatcher() {
  var newDelta = new Delta();
  newDelta.insert({'break': ''});
  return newDelta;
}

class SmartBreak extends Break {
  length () {
    return 1
  }
  value () {
    return '\n'
  }
  
  insertInto(parent, ref) {
    Embed.prototype.insertInto.call(this, parent, ref)
  }
}

SmartBreak.blotName = 'break';
SmartBreak.tagName = 'br'

Quill.register(SmartBreak)

  var quill = new Quill('#editor-container', {
	  debug: 'log',
    modules: {
	  imageResize: {},
      toolbar: {
	    container: '#toolbar-container'
	  },
	  keyboard: {
	  bindings: {
        linebreak: {
          key: 13,
          shiftKey: true,
          handler: function (range) {
            let currentLeaf = this.quill.getLeaf(range.index)[0]
            let nextLeaf = this.quill.getLeaf(range.index + 1)[0]

            this.quill.insertEmbed(range.index, 'break', true, 'user');

            // Insert a second break if:
            // At the end of the editor, OR next leaf has a different parent (<p>)
            if (nextLeaf === null || (currentLeaf.parent !== nextLeaf.parent)) {
              this.quill.insertEmbed(range.index, 'break', true, 'user');
            }

            // Now that we've inserted a line break, move the cursor forward
            this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
          }
        }
	  }
	  }
		  
	},
    theme: 'snow',
  });
  quill.on('text-change',function(delta,source) { enableSaveButton(); });
  
  
  
  const cf = JSON.parse(document.querySelector('#certfields').innerHTML);
  
  const myDropDown = new QuillToolbarDropDown({
	  label: "##",
	  rememberSelection: false
  })
  myDropDown.setItems(cf);
  myDropDown.onSelect = function(label, value, quill) {
    const { index, length } = quill.selection.savedRange
    quill.deleteText(index, length)
    quill.insertText(index, '#'+value+'#')
    quill.setSelection(index + value.length + 2)
  }  
  myDropDown.attach(quill);
}
showEditor();
</script>


<?php
}









// Add fields from the name table into $CERT_FIELDS
function extendCertFields($table) {
	
	global $DB, $CERT_FIELDS;

	$R = $DB->query("PRAGMA table_info($table)");
	while($rd = $R->fetchArray()) {
		$CERT_FIELDS[$rd['name']] = $rd['name'];
	}
}


function fetchCertificateW($EntrantID,$Class) {
	
	global $DB, $TAGS, $KONSTANTS;
	if ($EntrantID == '')
		$EntrantID = 0;
	if ($Class == '')
		$Class = 0;
	$sql = "SELECT * FROM certificates WHERE EntrantID=";
	$R = $DB->query($sql.$EntrantID." AND Class=$Class");
	$rd = $R->fetchArray();
	return ['html'=>$rd['html'],'css'=>$rd['css'],'Title'=>$rd['Title'],'Class'=>$rd['Class']];
	
}





// Complete the $CERT_FIELDS array ready for picking
function loadTableData(){
	
	global $CERT_FIELDS,$EXCLUDE_FIELDS;
	
	extendCertFields('rallyparams');
	extendCertFields('entrants');
	foreach($EXCLUDE_FIELDS as $fld)
		unset($CERT_FIELDS[$fld]);
	asort($CERT_FIELDS);
	echo('<div id="certfields" style="display:none;">');
	echo(JSON_encode($CERT_FIELDS));
	echo('</div>');
}



function saveCertificateW() {
	
	global $DB, $TAGS, $KONSTANTS;
	
	//var_dump($_REQUEST);
	$R = $DB->query("SELECT Count(*) As Rex FROM certificates WHERE EntrantID=".$_REQUEST['EntrantID']." AND Class=".$_REQUEST['Class']);
	$rd = $R->fetchArray();
	$adding = $rd['Rex'] < 1;
	
	if ($adding)
	{
//		echo(' adding ');
		$sql = "INSERT INTO certificates(EntrantID,Class,html,css,Title) VALUES(";
		$sql .= $_REQUEST['EntrantID'];
		$sql .= ",";
		$sql .= $_REQUEST['Class'];
		$sql .= ",'";
		$sql .= $DB->escapeString($_REQUEST['certhtml'])."'";
		$sql .= ",'";
//		$sql .= $DB->escapeString($_REQUEST['certcss'])."'";
//		$sql .= ",'";
		$sql .= $DB->escapeString($_REQUEST['Title'])."'";
		$sql .= ')';
	}
	else
	{
//		echo(' updating ');
		$sql = "UPDATE certificates SET html='".$DB->escapeString($_REQUEST['certhtml'])."'";
//		$sql .= ",css='".$DB->escapeString($_REQUEST['certcss'])."'";
		$sql .= ",Title='".$DB->escapeString($_REQUEST['Title'])."'";
		$sql .= " WHERE EntrantID=".$_REQUEST['EntrantID']." AND Class=".$_REQUEST['Class'];
	}
//	echo($sql."<hr>");
	$DB->exec($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorCode().' == '.$DB->lastErrorMsg().'<br>'.$sql.'<hr>');

	if (retraceBreadcrumb())
		exit;
	
}



function startHtmlW() {

	global $CERT_FIELDS, $TAGS;
	
	startHtml($TAGS['ttSetup'][0]);

?>
<link href="quill/quill.snow.css" rel="stylesheet">
<link href="certificate.css" rel="stylesheet">
<script src="quill/quill.js"></script>
<script src="quill/image-resize.min.js"></script>
<script src="quill/DynamicQuillTools.js"></script>
<?php

}



if (isset($_REQUEST['savecert']))
	saveCertificateW();

editCertificateW();


?>
