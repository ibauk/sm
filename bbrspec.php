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

$IMPORTSPEC['xlsname']		= "bbr.xlsx";		// Actual file is uploaded and called this
$IMPORTSPEC['whichsheet']	= 0;				// zero-based index to sheet within spreadsheet
$IMPORTSPEC['FirstDataRow']	= 4;				// Points to the first row of data starting at row 1

// Following list uses zero-based column numbers
$IMPORTSPEC['cols']['EntrantID']	= 0;
$IMPORTSPEC['cols']['RiderName']	= '1:2';
$IMPORTSPEC['cols']['RiderFirst']	= 1;
$IMPORTSPEC['cols']['PillionName']	= '3:4';
$IMPORTSPEC['cols']['PillionFirst']	= 3;
$IMPORTSPEC['cols']['Bike'] 		= 5;

$IMPORTSPEC['data']['email']		= 6;



 
?>
