<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle imports of data from spreadsheets
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


 // If you make a new import specification file, include it here so it'll
 // be offered as a choice.
 
 // The format is Option prompt => specificationfile.php, event title
$SPECFILES = array(	'BBR' => array('bbrspec.php','Brit Butt rally'),
					'BBL' => array('bblspec.php','Brit Butt Light'),
					'Jorvic' => array('jorvicspec.php','Jorvic rally'),
					'RBLR' => array('rblrspec.php','RBLR1000')
				);
?>
