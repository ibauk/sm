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

$IMPORTSPEC['xlsname']		= "bbr.xlsx";		// Actual file is uploaded and called this
$IMPORTSPEC['whichsheet']	= 0;				// zero-based index to sheet within spreadsheet
$IMPORTSPEC['FirstDataRow']	= 2;				// Points to the first row of data starting at row 1

// Following list uses zero-based column numbers
$IMPORTSPEC['cols']['EntrantID']	= 0;
$IMPORTSPEC['cols']['RiderName']	= '1:2';
$IMPORTSPEC['cols']['RiderFirst']	= 1;
$IMPORTSPEC['cols']['PillionName']	= '3:4';
$IMPORTSPEC['cols']['PillionFirst']	= 3;
$IMPORTSPEC['cols']['Bike'] 		= 13;
$IMPORTSPEC['cols']['BikeReg']		= 14;
$IMPORTSPEC['cols']['Email']		= 12;
$IMPORTSPEC['cols']['Phone']		= 11;
$IMPORTSPEC['cols']['NoKName']		= '20:21';
$IMPORTSPEC['cols']['NoKPhone']		= 28;
$IMPORTSPEC['cols']['NoKRelation']	= 29;


// data collects lines to be stored as ExtraData
$IMPORTSPEC['data']['Postcode']		= 9;
$IMPORTSPEC['data']['Country']		= 10;
$IMPORTSPEC['data']['Postal_Address']		= '5:6:7:8';

// If the content of the indexed column matches the RE, reject (don't load) the entry
$IMPORTSPEC['reject'][32]	= '/1Unpaid|1Refunded/';


 
?>
