<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle imports/exports of data
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

// xlsspec_bbr.php

$IMPORTSPEC['xlsname']		= "rblr.xlsx";		// Actual file is uploaded and called this
$IMPORTSPEC['whichsheet']	= 0;				// zero-based index to sheet within spreadsheet
$IMPORTSPEC['FirstDataRow']	= 2;				// Points to the first row of data starting at row 1

// Following list uses zero-based column numbers
$IMPORTSPEC['cols']['EntrantID']	= 0;
$IMPORTSPEC['cols']['RiderFirst']	= 1;
$IMPORTSPEC['cols']['RiderLast']	= 2;
$IMPORTSPEC['cols']['RiderIBA']		= 3;
$IMPORTSPEC['cols']['PillionFirst']	= 5;
$IMPORTSPEC['cols']['PillionLast']	= 6;
$IMPORTSPEC['cols']['PillionIBA']	= 8;
$IMPORTSPEC['cols']['Bike'] 		= 20;
$IMPORTSPEC['cols']['Country']		= 14;
$IMPORTSPEC['cols']['ScoredBy']		= 2; 	// RiderLast for surname sorting (cheat)


$IMPORTSPEC['cols']['FinishPosition'] = 0; /* Same as EntrantID to preserve order */

// Now choose only rows matching the regex below; multiple rows = and
//$IMPORTSPEC['select'][21]			= '/North Anti Clock Wise/';


/* Set the field after 'setif' to the following value if the column matches the regex */
$IMPORTSPEC['default']['EntrantStatus']	= 8; // Finisher so certificate can be printed straight away

$IMPORTSPEC['default']['Class']		= 0;
$IMPORTSPEC['setif']['Class'][1]	= array(21,'/North Anti Clock Wise/');
$IMPORTSPEC['setif']['Class'][2]	= array(21,'/North Clock Wise/');
$IMPORTSPEC['setif']['Class'][3]	= array(21,'/South Anti Clock Wise/');
$IMPORTSPEC['setif']['Class'][4]	= array(21,'/South Clock Wise/');
$IMPORTSPEC['setif']['Class'][5]	= array(21,'/BBG 1500/');
$IMPORTSPEC['setif']['Class'][6]	= array(21,'/500 Clock Wise/');
$IMPORTSPEC['setif']['Class'][7]	= array(21,'/500 Anti Clock Wise/');


// Copy extra fields to be passed through to any further data transfer
$IMPORTSPEC['data']['email']		= 19;
$IMPORTSPEC['data']['address']		= '9:10:11:12';
$IMPORTSPEC['data']['postcode']		= 13;
$IMPORTSPEC['data']['country']		= 14;
$IMPORTSPEC['data']['phone']		= 16;
$IMPORTSPEC['data']['mobile']		= 17;



 
?>
