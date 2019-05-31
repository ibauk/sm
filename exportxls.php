<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle data exports
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

/*
 *	2.1	Output ExtraData fields with finishers
 *	2.2 Expressly output Phone/Email
 *
 */
 
$HOME_URL = "admin.php";
require_once('common.php');

function exportFinishers()
{
	global $DB, $KONSTANTS;
	
	header('Content-Type: text/csv; charset=utf-8');
	header("Content-Disposition: attachment; filename=finishers.csv;");
	$sql = "SELECT RiderName,PillionName,Bike,FinishPosition,CorrectedMiles,TotalPoints,RiderIBA,PillionIBA,BikeReg,Class,Phone,Email,ExtraData FROM (SELECT *,Instr(RiderName,' ') As pos FROM entrants) WHERE EntrantStatus=".$KONSTANTS['EntrantFinisher'];
	$R = $DB->query($sql);
	
	ob_end_clean();  // Clear out any whitespace we've accidentally accumulated so far
	
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	$cols = array('RiderName','PillionName','Bike','Placing','Miles','Points','RiderIBA','PillionIBA','BikeReg','Class','Phone','Email');
	$hdrDone = FALSE;
	


	// loop over the rows, outputting them
	while ($rd = $R->fetchArray(SQLITE3_ASSOC))
	{
		$xa = explode("\n",$rd['ExtraData']);
		unset($rd['ExtraData']);
		if (!$hdrDone)
		{
			//var_dump($xa);
			$hdrDone = TRUE;
			foreach ($xa as $itm)
			{
				$cv = explode('=',$itm);
				array_push($cols,$cv[0]);
			}
			fputcsv($output,$cols);
		}
		foreach ($xa as $itm)
		{
			$cv = explode('=',$itm);
			//var_dump($cv);
			$rd[$cv[0]] = $cv[1];
		}
		//var_dump($rd);
		fputcsv($output, $rd);
	}
	exit();


}

if ($_REQUEST['c']=='expfinishers')
	exportFinishers();

?>