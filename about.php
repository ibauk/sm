<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I merely provide info about the application / server
 *
 * I am written for readability rather than efficiency, please keep me that way.
 *
 *
 * Copyright (c) 2019 Bob Stammers
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


$PROGRAM = array("version" => "2.4.1",	"title"	=> "ScoreMaster");
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
 *	2.1	22Sep18	Live at Jorvic 18
 *
 *	2.2			Accept zero miles as finisher, after min/max checks
 *				Show entrant bonus arrays with same formatting as scoresheet
 *				Ability to reject combos; Special/combo rejections report title
 *		Issued to John Cunniffe
 *	2.2.1		Programmable admin menus
 *				Full display of entrant table with scorex and rejects
 *
 *	2.3	13Jun19	Post BBR19
 *				Breadcrumbs, new CSS, MIT licence, Tabnames
 *
 *	2.3.1		Post BBL19
 *				OdoScaleFactor SanityCheck, QuickList spacing, Ticksheet print font size
 *
 *	2.4			Pre Magic-12
 *				Major update
 *
 *	2.4.1		Post Magic-12
 *				parseInt(EntrantID) in scoring picklist
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
	global $PROGRAM, $TAGS, $DBFILENAME, $DB, $KONSTANTS;
	
	startHtml($TAGS['ttAbout'][0],'',false);
	
	
	$serveraddr = $_SERVER['HTTP_HOST'];

	if (isset($_SERVER['SERVER_ADDR']))
		$serveraddr = gethostbynamel($_SERVER['SERVER_ADDR']);
	if ($serveraddr=='')
		$serveraddr = $_SERVER['LOCAL_ADDR'];
	
	echo("\n<div id=\"helpabout\">\n");
	echo('<h1>'.$PROGRAM['title'].' v'.$PROGRAM['version'].'</h1>');
	echo('<p class="slogan">'.$TAGS['SMDesc'][1].'</p>');
	echo('<hr>');
	echo('<dl class="main">');
	if (is_array($serveraddr))
	{
		$serverdetail = '';
		foreach($serveraddr as $ip)
		{
			if ($serverdetail != '')
				$serverdetail .= ', ';
			$serverdetail .= $ip;
		}
		$serverdetail = implode(',',$serveraddr);
	}
	else
		$serverdetail = $serveraddr;
	echo('<dt title="'.$TAGS['abtHostname'][1].'">'.$TAGS['abtHostname'][0].'</dt><dd>'.php_uname('n').' [ '.$serverdetail.' ]</dd>');
	echo('<dt title="'.$TAGS['abtDatabase'][1].'">'.$TAGS['abtDatabase'][0].'</dt><dd>'.absolutePath($DBFILENAME).'</dd>');
	echo('</dl><hr>');
	echo('<dl class="techie">');
	echo('<dt title="'.$TAGS['abtOnlineDoc'][1].'">'.$TAGS['abtOnlineDoc'][0].'</dt>');
	echo('<dd>');
	echo('<span class="dox" title="'.$TAGS['abtDocAdminGuide'][1].'">');
	echo('<a href="https://docs.google.com/document/d/1SFTU79AvWniOubc6psYkj55m3JCwlYJp1QkSMSJzij4/preview" target="smdox">'.$TAGS['abtDocAdminGuide'][0].'</a>');
	echo('</span>');
	echo('<span class="dox" title="'.$TAGS['abtDocDBSpec'][1].'">');
	echo('<a href="https://docs.google.com/document/d/1oRSSBPdAJdHNgKaB3ZlFlFsqYm-PtBxjBkM7-QwG9xo/preview" target="smdox">'.$TAGS['abtDocDBSpec'][0].'</a>');
	echo('</span>');
	echo('<span class="dox" title="'.$TAGS['abtDocTechRef'][1].'">');
	echo('<a href="https://docs.google.com/document/d/1IUiCZhgov1RSNxQ26CvoiSGEdEyFchIaFsXWaLnvUjM/preview" target="smdox">'.$TAGS['abtDocTechRef'][0].'</a>');
	echo('</span>');
	echo('</dd>');
	echo('</dl><hr>');
	echo('<dl class="techie">');
	$dbversion = 0;
	if ($R = $DB->query("SELECT DBVersion FROM rallyparams"))
	{
		$rd = $R->fetchArray();
		$dbversion = $rd['DBVersion'];
	}
	echo('<dt title="'.$TAGS['abtDBVersion'][1].'">'.$TAGS['abtDBVersion'][0].'</dt><dd>'.$dbversion.'</dd>');
	
	echo('<dt title="'.$TAGS['abtHostOS'][1].'">'.$TAGS['abtHostOS'][0].'</dt><dd>'.php_uname('s').' [ '.php_uname('v').' ]</dd>');
	echo('<dt title="'.$TAGS['abtWebserver'][1].'">'.$TAGS['abtWebserver'][0].'</dt><dd>'.$_SERVER['SERVER_SOFTWARE'].'</dd>');
	echo('<dt title="'.$TAGS['abtPHP'][1].'">'.$TAGS['abtPHP'][0].'</dt><dd>'.phpversion().'</dd>');
	echo('<dt title="'.$TAGS['abtSQLite'][1].'">'.$TAGS['abtSQLite'][0].'</dt><dd>'.SQLite3::version()['versionString'].'</dd>');
	$mk = ($KONSTANTS['BasicDistanceUnits'] == $KONSTANTS['DistanceIsMiles'] ? 'miles' : 'kilometres');
	echo('<dt title="'.$TAGS['abtBasicDistance'][1].'">'.$TAGS['abtBasicDistance'][0].'</dt><dd>'.$mk.'</dd>');
	$mk = ($KONSTANTS['DefaultKmsOdo'] == $KONSTANTS['OdoCountsMiles'] ? 'miles' : 'kilometres');
	echo('<dt title="'.$TAGS['abtDefaultOdo'][1].'">'.$TAGS['abtDefaultOdo'][0].'</dt><dd>'.$mk.'</dd>');
	echo('<dt title="'.$TAGS['abtAuthor'][1].'">'.$TAGS['abtAuthor'][0].'</dt><dd>Bob Stammers &lt;webmaster@ironbutt.co.uk&gt; on behalf of <span title="Iron Butt Association (UK)">IBAUK</span></dd>');
	echo('<dt title="'.$TAGS['abtLicence'][1].'">'.$TAGS['abtLicence'][0].'</dt><dd>MIT</dd>');
	echo('</dl>');
	echo("</div> <!-- helpabout -->\n");
	
	if (isset($_REQUEST['?']))
		echo '<pre>' . var_export($_SERVER, true) . '</pre>';
}

showAbout();	
?>
