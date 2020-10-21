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


 
 
require_once('common.php');



function showBonuses()
/*
 *										s h o w B o n u s e s
 *
 * This handles viewing and maintenance of the table of ordinary bonuses
 *
 */
{
	global $DB, $TAGS, $KONSTANTS;
	

?>
<script>
function deleteRow(obj)
{
	let tr = obj.parentNode.parentNode.parentNode;
	let B = tr.cells[0].firstChild.value;

	let xhttp;
 
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText.trim()=='')
				document.getElementById('bonuses').deleteRow(tr.rowIndex);
		}
	};
	
	xhttp.open("POST", "bonuses.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(encodeURI("c=deletebonus&bid="+B));
	return false;
}
function enableKill(obj)
{
	let bonuskiller = obj.parentNode.childNodes[2];
	bonuskiller.disabled = !obj.checked;
	return false;
}
function enableNewSave(obj)
{
	let tr = obj.parentNode.parentNode;
	let B = tr.cells[0].firstChild.value;
	console.log('B=='+B+';');
	let bd = tr.cells[1].firstChild.value;
	console.log('bd=='+bd+';');
	tr.cells[tr.cells.length - 1].childNodes[1].firstChild.disabled = (B == '' || bd == '');  // Save button
	return false;
}
function saveBonus(obj,isNew)
{
	let tr = obj.parentNode.parentNode.parentNode;
	let xhttp;
 
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText.trim()=='')
				showdelete(tr);
		}
	};
	

	let ix = 0;
	let rec = 'bid='+tr.cells[ix++].firstChild.value;
	rec += '&bd='+tr.cells[ix++].firstChild.value;
	rec += '&p='+tr.cells[ix++].firstChild.value;
	let nc = document.getElementById('numcats').value;
	for (let i = 1; i <= nc; i++) {
		let c = tr.cells[ix++].firstChild;
		let axis = c.getAttribute('data-axis');
		rec += '&cat'+axis+'='+c.value;
	}
	
	let comp = tr.cells[ix++].firstChild.checked ? 1 : 0;
	rec += '&comp='+comp;
	
	if (isNew)
		cmd = "c=insertbonus&";
	else
		cmd = "c=updatebonus&";

	xhttp.open("POST", "bonuses.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(encodeURI(cmd+rec));
	console.log('sNB: '+rec+' Saved!');
	return false;
	
}
function saveNewBonus(obj)
{
	return saveBonus(obj,true);
	
}
function saveOldBonus(obj)
{
	return saveBonus(obj,false);
	
}
function showdelete(tr)
{
	swapdelsave(tr,false);
}
function showsave(obj)
{
	let tr = obj.parentNode.parentNode;
	swapdelsave(tr,true);
}
function swapdelsave(tr,showsave)
{
	let ix = tr.cells.length - 1;
	if (!tr.cells[ix].classList.contains('buttons'))
		ix--;
	let ds = tr.cells[ix].childNodes[0];
	let ss = tr.cells[ix].childNodes[1];
	if (showsave) {
		ds.style.display = 'none';
		ss.style.display = 'inline';
	} else {
		ss.style.display = 'none';
		ds.style.display = 'inline';
	}
	
	return false;
}

function triggerNewRow()
{
	var oldnewrow = document.getElementsByClassName('newrow')[0];
	tab = document.getElementById('bonuses').getElementsByTagName('tbody')[0];
	var row = tab.insertRow(tab.rows.length);
	row.innerHTML = oldnewrow.innerHTML;
	row.firstChild.firstChild.focus();
	return false;
}
</script>
<?php	
	
	pushBreadcrumb('#');

	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		$catlabels[$i] = $rd['Cat'.$i.'Label'];
	

	emitBreadcrumbs();

	$R = $DB->query('SELECT * FROM categories ORDER BY Axis,BriefDesc');
	$lc = 0;
	while ($rd = $R->fetchArray())
	{
		if (!isset($cats[$rd['Axis']]))
			$cats[$rd['Axis']][0] = '';
		$cats[$rd['Axis']][$rd['Cat']] = $rd['BriefDesc'];
		
	}
	//print_r($cats1);
	
	
	
	$showclaimsbutton = (getValueFromDB("SELECT count(*) As rex FROM entrants","rex",0) > 0);
	
	for ($i=1, $j = 0; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($cats[$i]))
			$j++;
		
	echo('<input type="hidden" id="numcats" value="'.$j.'">');
	
	echo('<input type="hidden" name="c" value="bonuses">');
//	echo('<input type="hidden" name="menu" value="setup">');
	echo("\r\n");

	echo('<p>'.$TAGS['BonusMaintHead'][1].'</p>');
	echo('<table id="bonuses">');
//	echo('<caption title="'.htmlentities($TAGS['BonusMaintHead'][1]).'">'.htmlentities($TAGS['BonusMaintHead'][0]).'</caption>');
	echo('<thead class="listhead"><tr><th style="text-align:left;">'.$TAGS['BonusIDLit'][0].'</th>');
	//echo('<thead><tr><th>B</th>');
	echo('<th>'.$TAGS['BriefDescLit'][0].'</th>');
	echo('<th>'.$TAGS['BonusPoints'][0].'</th>');

	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($cats[$i]))
			echo('<th>'.$catlabels[$i].'</th>');		
		
	echo('<th>'.$TAGS['CompulsoryBonus'][0].'</th>');
	echo('<th>'.$TAGS['DeleteEntryLit'][0].'</th>');
	if ($showclaimsbutton)
		echo('<th class="ClaimsCount">'.$TAGS['ShowClaimsCount'][0].'</th>');
	echo("</tr>\r\n");
	echo('</thead><tbody>');
	
	
	$sql = 'SELECT * FROM bonuses ORDER BY BonusID';
	$R = $DB->query($sql);
	if ($DB->lastErrorCode() <> 0)
		echo($DB->lastErrorMsg().'<br>'.$sql.'<hr>');
	while ($rd = $R->fetchArray())
	{
		$rex = getValueFromDB("SELECT count(*) As rex FROM entrants WHERE ',' || BonusesVisited || ',' LIKE '%,".$rd['BonusID'].",%'","rex",0);
		
		echo('<tr class="hoverlite"><td><input class="BonusID" type="text" readonly  value="'.$rd['BonusID'].'"></td>');
		echo('<td><input class="BriefDesc" type="text" value="'.$rd['BriefDesc'].'" oninput="return showsave(this);"></td>');
		echo('<td><input type="number" value="'.$rd['Points'].'" oninput="return showsave(this);"></td>');
		for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
			if (isset($cats[$i]))
			{
				echo('<td><select data-axis="'.$i.'" oninput="return showsave(this);">');
				foreach ($cats[$i] as $ce => $bd)
				{
					echo('<option value="'.$ce.'" ');
					if ($ce == $rd['Cat'.$i])
						echo('selected ');
					echo('>'.htmlspecialchars($bd).'</option>');
				}
				echo('</select></td>');
			}
		
		if ($rd['Compulsory']==1)
			$chk = " checked ";
		else
			$chk = "";
		echo('<td class="center"><input type="checkbox"'.$chk.' title="'.$TAGS['CompulsoryBonus'][1].'" name="Compulsory[]" value="'.$rd['BonusID'].'" oninput="return showsave(this);"></td>');
		
		

		echo('<td class="center buttons">');
		echo('<span class="deletebutton">');

		if ($rex > 0) {
			;
		} else {
			echo('<input type="checkbox" onchange="enableKill(this);"> <button disabled value="-" onclick="return deleteRow(this);">-</button>');
		}
		echo('</span>');
		echo('<span class="savebutton" style="display:none;">');
		echo('<button title="'.$TAGS['SaveRecord'][1].'" onclick="return saveOldBonus(this);">'.$TAGS['SaveRecord'][0].'</button>');
		echo('</span>');
		echo('</td>');
		
		
		if ($showclaimsbutton)
		{
			echo('<td class="ClaimsCount" title="'.$TAGS['ShowClaimsButton'][1].'">');
			if ($rex > 0)
				echo('<a href='."'entrants.php?c=entrants&mode=bonus&bonus=".$rd['BonusID']."'".'> '.$rex.' </a>');
			echo('</td>');
		}
		echo("</tr>\r\n");
	}
	echo('<tr class="newrow hide"><td><input title="'.$TAGS['BonusIDLit'][1].'" class="BonusID" type="text" onblur="enableNewSave(this);"></td>');
	echo('<td><input type="text" onblur="enableNewSave(this);"></td>');
	echo('<td><input type="number" value="1" onblur="enableNewSave(this);"></td>');
	for ($i=1; $i <= $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($cats[$i]))
		{
			$S = ' selected ';
			echo('<td><select data-axis="'.$i.'" onblur="enableNewSave(this);">');
			foreach ($cats[$i] as $ce => $bd)
			{
				echo('<option value="'.$ce.'" ');
				echo($S);
				$S = '';
				echo('>'.htmlspecialchars($bd).'</option>');
			}
			echo('</select></td>');
		}
		
	echo('<td class="center"><input title="'.$TAGS['CompulsoryBonus'][1].'" type="checkbox" onblur="enableNewSave(this);"></td>');
	echo('<td class="center buttons">');
	echo('<span class="deletebutton hide" >');
	echo('<input type="checkbox" onchange="enableKill(this);"> <button disabled  value="-" onclick="return deleteRow(this);">-</button>');
	echo('</span>');
	echo('<span>');
	echo('<button disabled title="'.$TAGS['SaveRecord'][1].'" onclick="return saveNewBonus(this);">'.$TAGS['SaveRecord'][0].'</button>');
	echo('</span>');
	echo('</td>');
	echo('</tr>');
	
	echo('</tbody></table>');
	echo('<button value="+" onclick="return triggerNewRow();">+</button><br>');
	
	
}





function callbackDeleteBonus($b)
{
	global $DB;
	
	$sql = "DELETE FROM bonuses WHERE BonusID='".$DB->escapeString($b)."'";
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) 
		return dberror();
}

function callbackInsertBonus()
{
	global $DB, $KONSTANTS;
	
	$sql = "INSERT INTO bonuses (BonusID,BriefDesc,Points";
	for ($i = 1; $i < $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($_POST['cat'.$i]))
			$sql .= ",Cat$i";
	$sql .= ",Compulsory) VALUES(";
	$sql .= "'".$DB->escapeString($_POST['bid'])."'";
	$sql .= ",'".$DB->escapeString($_POST['bd'])."'";
	$sql .= ",".intval($_POST['p']);
	for ($i = 1; $i < $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($_POST['cat'.$i]))
			$sql .= ",".intval($_POST['cat'.$i]);
	$sql .= ",".intval($_POST['comp']);
	$sql .= ")";
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) 
		return dberror();
	
}

function callbackUpdateBonus()
{
	global $DB, $KONSTANTS;

	$sql = "UPDATE bonuses SET BriefDesc='".$DB->escapeString($_POST['bd'])."'";
	$sql .= ",Points=".intval($_POST['p']);
	for ($i = 1; $i < $KONSTANTS['NUMBER_OF_COMPOUND_AXES']; $i++)
		if (isset($_POST['cat'.$i]))
			$sql .= ",Cat".$i."=".intval($_POST['cat'.$i]);
	$sql .= ",Compulsory=".intval($_POST['comp']);
	$sql .= " WHERE BonusID='".$DB->escapeString($_POST['bid'])."'";
	$DB->exec($sql);
	if ($DB->lastErrorCode()<>0) 
		return dberror();
		
}



if (isset($_REQUEST['c']))
	switch($_REQUEST['c']) {
		case 'insertbonus':
			callbackInsertBonus();
			exit;
		case 'updatebonus':
			callbackUpdateBonus();
			exit;
		case 'deletebonus':
			callbackDeleteBonus($_POST['bid']);
			exit;
	}




startHtml($TAGS['ttSetup'][0]);


if (isset($_REQUEST['c']))
{
	switch($_REQUEST['c'])
	{
			
		case 'bonuses':
			showBonuses();
			break;

		default:
			echo("<p>I don't know what to do with '".$_REQUEST['c']."'!");
	}
} else
	include "score.php"; // Some mistake has happened or maybe someone just tried logging on
//	print_r($_REQUEST);

?>

