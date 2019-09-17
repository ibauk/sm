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
 * it under the terms of the MIT License
 *
 * IBAUK-SCOREMASTER is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * MIT License for more details.
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
$IMPORTSPEC['cols']['RiderName']	= '8:9';
$IMPORTSPEC['cols']['RiderFirst']	= 8;
$IMPORTSPEC['cols']['PillionName']	= '10:11';
$IMPORTSPEC['cols']['PillionFirst']	= 10;
$IMPORTSPEC['cols']['Bike'] 		= 20;
$IMPORTSPEC['cols']['BikeReg'] 		= 21;
$IMPORTSPEC['cols']['Email']		= 19;
$IMPORTSPEC['cols']['Phone']		= 18;
$IMPORTSPEC['cols']['NoKName']		= 22;
$IMPORTSPEC['cols']['NoKPhone']		= 23;
$IMPORTSPEC['cols']['NoKRelation']	= 24;

// data collects lines to be stored as ExtraData
$IMPORTSPEC['data']['Postcode']		= 16;
$IMPORTSPEC['data']['Country']		= 17;
$IMPORTSPEC['data']['Postal_Address']		= '12:13:14:15:16:17';

// If the content of the indexed column matches the RE, reject (don't load) the entry
//$IMPORTSPEC['reject'][18]	= '/Unpaid/';






 
?>
