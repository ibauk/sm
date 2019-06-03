<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle imports/exports of data
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

// xlsspec_bbr.php

$IMPORTSPEC['xlsname']		= "rblr.xlsx";		// Actual file is uploaded and called this
$IMPORTSPEC['whichsheet']	= 0;				// zero-based index to sheet within spreadsheet
$IMPORTSPEC['FirstDataRow']	= 2;				// Points to the first row of data starting at row 1

// Following list uses zero-based column numbers
$IMPORTSPEC['cols']['EntrantID']	= 0;
$IMPORTSPEC['cols']['RiderFirst']	= 8;
$IMPORTSPEC['cols']['RiderLast']	= 9;
$IMPORTSPEC['cols']['RiderIBA']		= 10;
$IMPORTSPEC['cols']['PillionFirst']	= 12;
$IMPORTSPEC['cols']['PillionLast']	= 13;
$IMPORTSPEC['cols']['PillionIBA']	= 15;
$IMPORTSPEC['cols']['Bike'] 		= 27;
$IMPORTSPEC['cols']['Country']		= 21;
$IMPORTSPEC['cols']['ScoredBy']		= 9; 	// RiderLast for surname sorting (cheat)

$IMPORTSPEC['cols']['Email']		= 25;
$IMPORTSPEC['cols']['Phone']		= 24;
$IMPORTSPEC['cols']['NoKName']		= '30:31';
$IMPORTSPEC['cols']['NoKPhone']		= 38;
$IMPORTSPEC['cols']['NoKRelation']	= 39;


$IMPORTSPEC['cols']['FinishPosition'] = 0; /* Same as EntrantID to preserve order */

// Now choose only rows matching the regex below; multiple rows = and
//$IMPORTSPEC['select'][21]			= '/North Anti Clock Wise/';


/* Set the field after 'setif' to the following value if the column matches the regex */
$IMPORTSPEC['default']['EntrantStatus']	= 8; // Finisher so certificate can be printed straight away

$IMPORTSPEC['default']['Class']		= 0;
$IMPORTSPEC['setif']['Class'][1]	= array(28,'/North Anti Clock Wise/');
$IMPORTSPEC['setif']['Class'][2]	= array(28,'/North Clock Wise/');
$IMPORTSPEC['setif']['Class'][3]	= array(28,'/South Anti Clock Wise/');
$IMPORTSPEC['setif']['Class'][4]	= array(28,'/South Clock Wise/');
$IMPORTSPEC['setif']['Class'][5]	= array(28,'/BBG 1500/');
$IMPORTSPEC['setif']['Class'][6]	= array(28,'/500 Clock Wise/');
$IMPORTSPEC['setif']['Class'][7]	= array(28,'/500 Anti Clock Wise/');


// Copy extra fields to be passed through to any further data transfer
$IMPORTSPEC['data']['email']		= 26;
$IMPORTSPEC['data']['address']		= '17:18:19:20';
$IMPORTSPEC['data']['postcode']		= 20;
$IMPORTSPEC['data']['country']		= 21;
$IMPORTSPEC['data']['phone']		= 22;
$IMPORTSPEC['data']['mobile']		= 24;



 
?>
