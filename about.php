<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I merely provide info about the application / server
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


$PROGRAM = array("version" => "2.1",	"title"	=> "ScoreMaster");
/*
 *	2.0	25May18	Used live at BBR18
 *
 *				Bug fixes from BBR18
 *				EntrantStatus = Finisher changed from 2 to 8
 *				Scoring status to Finisher if was OK and CorrectedMiles > 0
 *				Multiple radio button groups of specials
 *				Autosuppress Team# in listings
 *				Certificate class
 *				Programmable certificate sequence
 *				Accept/Reject claim handling
 *				Include ExtraData with finisher export
 *	2.1	
 *
 */
$HOME_URL = "admin.php";
require_once("common.php");

/*
 * convert the path supplied, good within the hosting environment, into a host absolute path
 */
function absolutePath($webfile)
{
	$basepath = $_SERVER['DOCUMENT_ROOT'];
	$pos = strpos($basepath,'/');
	if ($pos === FALSE)
		$pathsep = '\\';
	else
		$pathsep = '/';
	if (substr($basepath,1,-1) != $pathsep && substr($webfile,0,1) != $pathsep)
		$basepath .= $pathsep;
	$basepath .= $webfile;
	return $basepath;
}

function showAbout()
{
	global $PROGRAM, $TAGS, $DBFILENAME;
	
	startHtml();
	
	
	$serveraddr = $_SERVER['HTTP_HOST'];
	if ($serveraddr=='')
		$serveraddr = $_SERVER['LOCAL_ADDR'];
	
	echo("\n<div id=\"helpabout\">\n");
	echo('<h1>'.$PROGRAM['title'].' v'.$PROGRAM['version'].'</h1>');
	echo('<p class="slogan">'.$TAGS['SMDesc'][1].'</p>');
	echo('<hr>');
	echo('<dl class="main">');
	echo('<dt title="'.$TAGS['abtHostname'][1].'">'.$TAGS['abtHostname'][0].'</dt><dd>'.php_uname('n').' [ '.$serveraddr.' ]</dd>');
	echo('<dt title="'.$TAGS['abtDatabase'][1].'">'.$TAGS['abtDatabase'][0].'</dt><dd>'.absolutePath($DBFILENAME).'</dd>');
	echo('</dl><hr>');
	echo('<dl class="techie">');
	echo('<dt title="'.$TAGS['abtHostOS'][1].'">'.$TAGS['abtHostOS'][0].'</dt><dd>'.php_uname('s').' [ '.php_uname('v').' ]</dd>');
	echo('<dt title="'.$TAGS['abtWebserver'][1].'">'.$TAGS['abtWebserver'][0].'</dt><dd>'.$_SERVER['SERVER_SOFTWARE'].'</dd>');
	echo('<dt title="'.$TAGS['abtPHP'][1].'">'.$TAGS['abtPHP'][0].'</dt><dd>'.phpversion().'</dd>');
	echo('<dt title="'.$TAGS['abtSQLite'][1].'">'.$TAGS['abtSQLite'][0].'</dt><dd>'.SQLite3::version()['versionString'].'</dd>');
	echo('<dt title="'.$TAGS['abtAuthor'][1].'">'.$TAGS['abtAuthor'][0].'</dt><dd>Bob Stammers &lt;webmaster@ironbutt.co.uk&gt; on behalf of <span title="Iron Butt Association (UK)">IBAUK</span></dd>');
	echo('</dl>');
	echo("</div> <!-- helpabout -->\n");
	
	if (isset($_REQUEST['?']))
		echo '<pre>' . var_export($_SERVER, true) . '</pre>';
}

showAbout();	
?>
