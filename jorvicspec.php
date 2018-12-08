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

// This matches the latest wufoo forms used to register entrants in the
// 2018 Jorvic rally

$IMPORTSPEC['xlsname']		= "jorvic.xlsx";	// Actual file is uploaded and called this
$IMPORTSPEC['whichsheet']	= 0;				// zero-based index to sheet within spreadsheet
$IMPORTSPEC['FirstDataRow']	= 2;				// Points to the first row of data starting at row 1

// Following lists use zero-based column numbers

// cols represent fields in the ScoreMaster.entrants table
$IMPORTSPEC['cols']['EntrantID']	= 0;
$IMPORTSPEC['cols']['RiderName']	= '1:2';
$IMPORTSPEC['cols']['RiderFirst']	= 1;
$IMPORTSPEC['cols']['PillionName']	= '3:4';
$IMPORTSPEC['cols']['PillionFirst']	= 3;
$IMPORTSPEC['cols']['Bike'] 		= 13;
$IMPORTSPEC['cols']['BikeReg'] 		= 14;

// data collects lines to be stored as ExtraData
$IMPORTSPEC['data']['Email']		= 12;
$IMPORTSPEC['data']['Postcode']		= 9;
$IMPORTSPEC['data']['Phone']		= 11;
$IMPORTSPEC['data']['Country']		= 10;
$IMPORTSPEC['data']['NoKName']		= 15;
$IMPORTSPEC['data']['NoKPhone']		= 16;
$IMPORTSPEC['data']['NoKRelation']	= 17;
$IMPORTSPEC['data']['Postal_Address']		= '5:6:7:8:9:10';

// If the content of the indexed column matches the RE, reject (don't load) the entry
$IMPORTSPEC['reject'][18]	= '/Unpaid/';






 
?>