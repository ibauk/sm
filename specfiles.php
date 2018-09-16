<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I handle imports of data from spreadsheets
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


 // If you make a new import specification file, include it here so it'll
 // be offered as a choice.
 
 // The format is Option prompt => specificationfile.php, event title
$SPECFILES = array(	'BBR' => array('bbrspec.php','Brit Butt rally'),
					'Jorvic' => array('jorvicspec.php','Jorvic rally'),
					'RBLR' => array('rblrspec.php','RBLR1000')
				);
?>
