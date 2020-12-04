<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle [F1] help
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


$HOME_URL = "showhelp.php";

require_once('common.php');

function getFileText($topic)
{
    global $KONSTANTS;

    $filename = $KONSTANTS['doxpath'].DIRECTORY_SEPARATOR.$topic.'.hlp';
    if (!file_exists($filename))
        return '!!! '.$filename;
    else
        return file_get_contents($filename);

}

function modifyHtml($html)
{
    global $KONSTANTS;

    return str_replace('src="./','src="'.$KONSTANTS['doxpath'].DIRECTORY_SEPARATOR,str_replace('<a href="help:','<a href="showhelp.php?topic=',$html));
}

function showhelptopic($topic)
{
    global $KONSTANTS, $TAGS;

    startHtml('sm:?:'.$topic,'<a href="about.php" class="techie" title="'.$TAGS['HelpAbout'][1].'">'.$TAGS['HelpAbout'][0].'</a>',false);
    echo('<div class="currenttopic">');
    if ($topic != 'index') {
        $html = getFileText('helpindex');
        echo(modifyHtml($html));
    }
    $html = getFileText($topic);
    echo(modifyHtml($html));
    echo('</div>');
}

if (isset($_REQUEST['topic']))
    showhelptopic($_REQUEST['topic']);
else
    showhelptopic('index');
?>
