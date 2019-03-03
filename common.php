<?php

/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I contain all the text literals used throughout the system. If translation/improvement
 * is needed, this is the file to be doing it.
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


$KONSTANTS['DistanceIsMiles'] = 0;
$KONSTANTS['DistanceIsKilometres'] = 1;
$KONSTANTS['OdoCountsMiles'] = 0;
$KONSTANTS['OdoCountsKilometres'] = 1;


/*
 * This next constant determines whether the basic unit of distance used
 * by this application is the mile or the kilometre. The field names on
 * the database remain as 'miles' or 'mileage' as they're only used 
 * internally but calculations switching between miles and kilometres
 * are affected by this setting. Field labels and tooltips must also
 * be altered manually. It is assumed that a particular instance of this 
 * application will always use the same unit of measure.
 *
 * Field labels affected are marked  // Miles/Kms
 *
 */
 
$KONSTANTS['BasicDistanceUnits'] = $KONSTANTS['DistanceIsMiles'];
// $KONSTANTS['BasicDistanceUnits'] = $KONSTANTS['DistanceIsKilometres'];




// Default settings

// This setting should normally reflect BasicDistanceUnits above
$KONSTANTS['DefaultKmsOdo'] = $KONSTANTS['OdoCountsMiles']; 
// $KONSTANTS['DefaultKmsOdo'] = $KONSTANTS['OdoCountsKilometres'];



// Used when setting up new entrants onscreen

$KONSTANTS['DefaultCountry'] = 'UK';




// This array specifies labels and tooltips for each onscreen field to avoid the need for 'literals in the procedure division'.
// This is in alphabetic order by key. It doesn't need to be but it makes your life easier, doesn't it?
// DO NOT alter key names!
$TAGS = array(
	'abtAuthor'			=> array('Author','Who developed this application'),
	'abtBasicDistance'	=> array('Basic distance unit','Miles or Kilometres'),
	'abtDatabase'		=> array('Database file','Full path to the file containing the database'),
	'abtDBVersion'		=> array('DB schema version',''),
	'abtDefaultOdo'		=> array('Odo default','Odometers assumed to record miles or kilometres'),
	'abtDocAdminGuide'	=> array('Administration Guide','User guide for rally administrators'),
	'abtDocDBSpec'		=> array('Database specs','Full contents of the database'),
	'abtDocTechRef'		=> array('Technical reference','For application developers'),
	'abtHostname'		=> array('Hostname','Name of the computer hosting this application'),
	'abtHostOS'			=> array('HostOS','Details of the host\'s operating system'),
	'abtLicence'		=> array('Licence','The licence controlling use of this application'),
	'abtOnlineDoc'		=> array('Online documentation','Current application manuals available on the web'),
	'abtPHP'			=> array('PHP version',''),
	'abtSQLite'			=> array('SQLite version',''),
	'abtWebserver'		=> array('Webserver','What webserver software is hosting this'),
	'AddPoints'			=> array('Add points',''),
	'AddMults'			=> array('Add multipliers',''),
	'AdmBonusHeader'	=> array('Bonuses',''),
	'AdmBonusTable'		=> array('Ordinary bonuses','View/edit schedule of ordinary bonuses'),
	'AdmCatTable'		=> array('Categories','View/edit axis categories'),
	'AdmCombosTable'	=> array('Combinations','View/edit combination bonuses'),
	'AdmCompoundCalcs'	=> array('Compound calculations','Maintain table of calculation records'),
	'AdmDoBlank'		=> array('Blank score sheet','Show blank score sheet ready for printing'),
	'AdmDoScoring'		=> array('Scoring','Score individual entrants'),
	'AdmEditCert'		=> array('Edit certificate content','Edit the HTML &amp; CSS of the master certificate'),
	'AdmEntrants'		=> array('Entrants table','View/edit list of Entrants'),
	'AdmEntrantChecks'	=> array('Check-out/in','Entrant checks @ start/end of rally'),
	'AdmEntrantsHeader'	=> array('Entrants',''),
	'AdmExportFinishers'=> array('Export finishers','Save CSV containing details of finishers'),
	'AdmImportEntrants'	=> array('Import Entrants','Load entrant details from a spreadsheet'),
	'AdminMenu'			=> array('Rally Administration','Logon to carry out administration (not scoring) of the rally'),
	'AdmMenuHeader'		=> array('Rally administration',''),
	'AdmNewEntrant'		=> array('Setup new entrant','Add details of another entrant'),
	'AdmPrintCerts'		=> array('Print finisher certificates','Print certificates for finishers'),
	'AdmPrintQlist'		=> array('Finisher quicklist','Print quick list of finishers'),
	'AdmPrintScoreX'	=> array('Score explanations','Print score explanations for everyone not DNS'),
	'AdmRallyParams'	=> array('Rally parameters','View/edit current rally parameters'),
	'AdmRankEntries'	=> array('Rank finishers','Calculate and apply the rank of each finisher'),
	'AdmSelectTag'		=> array('Search by keyword','Choose a tag to list relevant functions'),
	'AdmSetupHeader'	=> array('Rally setup',''),
	'AdmSetupWiz'		=> array('Setup wizard','Basic rally setup wizard'),
	'AdmSGroups'		=> array('Specials groups','Maintain groups of specials'),
	'AdmShowSetup'		=> array('Rally setup &amp; config','View/maintain rally configuration records'),
	'AdmShowTagMatches'	=> array('Items matching ','Showing functions matching tag '),
	'AdmSpecialTable'	=> array('Special bonuses','View/edit special bonuses'),
	'AdmTimePenalties'	=> array('Time penalties','Maintain table of time penalties'),
	'AdmUtilHeader'		=> array('Utility functions',',,'),
	
	'AskEnabledSave'	=> array('Save this scoresheet?',''),
	'AskPoints'			=> array('Variable?','Ask for points value during scoring'),
	'AxisLit'			=> array('Axis','The set of categories this rule applies to'),
	'BasicDetails'		=> array('Basic',''),
	'BasicRallyConfig'	=> array('Basic','Basic rally configuration fields'),
	'Bike'				=> array('Bike','Make &amp; model of bike'),
	'BikeReg'			=> array('Registration','Registration number of the bike if known'),
	'BonusesLit'		=> array('Bonuses','Ordinary bonuses'),
	'BonusIDLit'		=> array('BonusID',''),
	'BonusListLit'		=> array('Combination bonuses','List of ordinary &amp; special bonus IDs'),
	'BonusMaintHead'	=> array('Ordinary Bonuses','List of Ordinary (geographic) bonuses'),
	'BonusPoints'		=> array('Points','The basic points value of this bonus'),
	'BriefDescLit'		=> array('Brief description',''),
	'CalcMaintHead'		=> array('Compound Calculation Rules','List of rules for compound score calculations'),
	'Cat1Label'			=> array('X-axis is','What do values on the X-axis represent?'),
	'Cat2Label'			=> array('Y-axis is','What do values on the Y-axis represent?'),	
	'Cat3Label'			=> array('Z-axis is','What do values on the Z-axis represent?'),
	'CatBriefDesc'		=> array('Description',''),
	'CategoryLit'		=> array('Category',''),
	'CatEntry'			=> array('Category','The number of this category within the axis'),
	'CatEntryCC'		=> array('Which category','Which cat(s) does this rule apply to'),
	'CatExplainer'		=> array('CatExplainer','You can amend the description of entries or delete them entirely. New entries must have an entry number which is unique within the axis.'),
	'ccApplyToAll'		=> array('all cats','applies to all cats'),
	
	'CertExplainer'		=> array('Certificates are "web" documents comprising well-formed HTML and CSS parts.',
									'Please carefully specify the certificate layout and content in the texts below.'),
	
	'CertificateHours'	=> array('Certificate hours','The duration of the rally in hours for the certificate'),
	'CertTitle'			=> array('Title','Description of this certificate class'),
	'Class'				=> array('Class #','The certificate class applicable'),
	'ChooseEntrant'		=> array('Choose entrant','Pick an entrant from this list'),
	'ComboIDLit'		=> array('ComboID',''),
	'ComboMaintHead'	=> array('Combination Bonuses','List of Combination bonuses'),
	'CombosLit'			=> array('Combinations','Combination bonuses'),
	'CommaSeparated'	=> array('Comma separated list',''),
	'CompulsoryBonus'	=> array('Compulsory?','This bonus is required for Finisher status'),
	'ConfirmDelEntrant'	=> array('Delete this entrant?','Confirm deletion of this entrant'),
	
	'CorrectedMiles'	=> array('Miles ridden','Official rally mileage'),	// Miles/Kms
	
	'Country'			=> array('Country',"Entrant's home country"),
	'dblclickprint'		=> array('Double-click to print',''),
	'DeleteEntrant'		=> array('Go ahead, delete the bugger!','Execute the deletion'),
	'DeleteEntryLit'	=> array('Delete?',''),
	'EntrantDNF'		=> array('DNF','Did not qualify as a finisher'),
	'EntrantDNS'		=> array('DNS','Entrant failed to start the rally'),
	'EntrantFinisher'	=> array('Finisher','Rally finisher'),
	'EntrantID'			=> array('Entrant #','The unique reference for this Entrant'),
	'EntrantListBonus'	=> array('Entrants claiming bonus','List of entrants claiming a particular bonus'),
	'EntrantListCheck'	=> array('Entrant check-ins/outs','Choose an entrant for checkin-in or checking-out'),
	'EntrantListCombo'	=> array('Entrants claiming combo','List of entrants claiming a particular combination'),
	'EntrantListFull'	=> array('Full list of Entrants','Choose an entrant to view/edit his/her details'),
	'EntrantListSpecial'=> array('Entrants claiming special','List of entrants claiming a particular special'),
	'EntrantOK'			=> array('ok','Status normal'),
	'EntrantStatus'		=> array('Status','The current rally status of this entrant'),
	
							// Careful! this is executed as PHP, get it right.
	'EntrantStatusV'	=> array('array("0" => "DNS", "1" => "ok", "8" => "Finisher", "3" => "DNF");','array used for vertical tables'),
	
	'ExcessMileage'		=> array('Excess miles',''),						// Miles/Kms
	
	'ExtraData'			=> array('ExtraData','Extra data to be passed on to the main database. Format is <i>name</i>=<i>value</i>'),
	
	'FetchCert'			=> array('Fetch certificate','Fetch the HTML, CSS &amp; options for this certificate'),
	'FinishDate'		=> array('Finish date','The last riding day of the rally.'),
	'FinishersExported'	=> array('Finishers exported!','Finisher details exported to CSV'),
	'FinishPosition'	=> array('Final place','Finisher ranking position',''),
	'FinishTime'		=> array('Finish time','Official finish time. Entrants finishing later are DNF'),

	'FullDetails'		=> array('Full details','Show the complete record'),

	'gblMainMenu'		=> array('Main menu','Return to main menu'),
	
	'GroupNameLit'		=> array('Special group','Group used for presentation purposes'),
	'HelpAbout'			=> array('About ScoreMaster',''),
	'InsertNewCC'		=> array('Enter new compound calc',''),
	'LegendPenalties'	=> array('Penalties',''),
	'LegendScoring'		=> array('Scoring &amp; Ranking',''),
	'LegendTeams'		=> array('Teams'),
	'login'				=> array('login','Go on, log me in then!'),
	'LogoutScorer'		=> array('Logout','Log the named scorer off this terminal'),
	
	'MaxMilesFixedM'	=> array('Multiplier','Excess mileage incurs deduction of multipliers'),							// Miles/Kms
	'MaxMilesFixedP'	=> array('Fixed points','Excess mileage incurs fixed points deduction'),							// Miles/Kms
	'MaxMilesPerMile'	=> array('Points per mile','Excess mileage incurs points deduction per excess mile'),				// Miles/Kms
	'MaxMilesPoints'	=> array('Points or Multipliers deducted','Number of points or multipliers for excess mileage'),	// Miles/Kms
	'MaxMilesUsed'		=> array('Tick if maximum miles used','Will entrants be DNF if they exceed a maximum distance?'),	// Miles/Kms
	'MilesPenaltyText'	=> array('Mileage penalty deduction',''),															// Miles/Kms
	'MinMiles'			=> array('Minimum miles','Minimum number of miles to qualify as a finisher'),						// Miles/Kms
	'MinMilesUsed'		=> array('Tick if minimum miles used','Will entrants need to ride a minimum distance in order to qualify as finishers?'), // Miles/Kms
	
	'MinPoints'			=> array('Minimum points','Minimum points scored to be a finisher'),
	'MinPointsUsed'		=> array('Tick if minimum points used','Will entrants need to score a minimum number of points in order to qualify as finishers?'),
	'ModBonus0'			=> array('ModBonus0','Affects compound axis score'),
	'ModBonus1'			=> array('ModBonus1','Modifies bonus score'),
	'ModBonusLit'		=> array('Usage','1=This calc directly affects bonus value, 0=This calc builds the axis score'),
	'NameFilter'		=> array('Rider name','Use this to filter the list of riders shown below'),
	'NewEntrantNum'		=> array('New number','What\'s the number number for this entrant'),
	'NewPlaceholder'	=> array('start new entry','Placeholder for new table entries'),
	'NMethod-1'			=> array('NMethod-1','Not used'),
	'NMethod0'			=> array('NMethod0','No of bonuses per cat'),
	'NMethod1'			=> array('NMethod1','No of NZ cats per axis'),
	'NMethodLit'		=> array('NMethod','0=# entries per cat, 1=# of NZ cats, -1=record not used'),
	'NMinLit'			=> array('NMin','The minimum value of N before this rule is triggered'),
	'NoCerts2Print'		=> array('Sorry, no certificates to print.',''),
	'NoScoreX2Print'	=> array('Sorry, no score explanations to print.',''),
	'nowlit'			=> array('Now','Record the current date/time'),
	'NPowerLit'			=> array('NPower',"If bonus rule &amp; this is 0, R=bonuspoints(N-1)\n".
											"If bonus rule &amp; this > 0, R=bonuspoints(this^(N-1))\n".
											"If axis rule &amp; this is 0, R=N\n".
											"If axis rule &amp; this > 0, R=this value"),
											
	'OdoCheckFinish'	=> array('Odo check finish','The odometer reading at the end of the odo check'),					// Miles/Kms
	'OdoCheckMiles'		=> array('Odo check distance','The length of the route used to check the accuracy of odometers'),	// Miles/Kms
	'OdoCheckStart'		=> array('Odo check start','The reading at the start of the odometer check'),						// Miles/Kms
	'OdoCheckTrip'		=> array('Odo check trip','What distance did the trip meter record?'),								// Miles/Kms
	'OdoCheckUsed'		=> array('Tick if odo check used','Will entrants be required to ride an odometer check route?'),	// Miles/Kms
	'OdoKms'			=> array('Odo counts',''),																			// Miles/Kms
	'OdoKmsK'			=> array('kilometres',''),																			// Miles/Kms
	'OdoKmsM'			=> array('miles',''),																				// Miles/Kms
	'Odometer'			=> array('Odo readings',''),																		// Miles/Kms
	'OdoRallyStart'		=> array('Start of rally','The reading at the start of the rally'),									// Miles/Kms
	'OdoRallyFinish'	=> array('At end of rally','The odometer reading at the end of the rally'),							// Miles/Kms
	'OdoScaleFactor'	=> array('Correction factor','The number to multiply odo readings to get true distance'),			// Miles/Kms
	
	'OfferScore'		=> array('OfferScore','Would you like to help score this rally? If so, please tell me your name'),
	
	'PenaltyMaxMiles'	=> array('Max miles (penalties)','Mileage beyond this incurs penalties; 0=doesn\'t apply'),			// Miles/Kms
	'PenaltyMilesDNF'	=> array('DNF mileage','Miles beyond here result in DNF; 0=doesn\'t apply'),						// Miles/Kms
	
	'PickAnEntrant'		=> array('Pick an entrant','Pick an entrant using the list below or by entering an Entrant number. Type a name to filter the list.'),
	'PillionFirst'		=> array('Informal name',"Used for repeat mentions on finisher's certificate"),
	'PillionIBA'		=> array('IBA #',"Pillion's IBA number if known"),
	'PillionName'		=> array('Pillion','Full name of the pillion rider'),
	'PointsMults'		=> array('Points/Mults','The value of this is either points or multipliers'),
	'PointsMults0'		=> array('PointsMults0','Points'),
	'PointsMults1'		=> array('PointsMults1','Multipliers'),
	
	'PreviewCert'		=> array('Preview','What will this certificate look like'),
	
	// Quick dirty list headings
	'qPlace'			=> array('Rank',''),
	'qName'				=> array('Name',''),
	
	'qMiles'			=> array('Miles',''),						// Miles/Kms
	
	'qPoints'			=> array('Points',''),
	
	// Renumber All Entrants texts
	'raeConfirm'		=> array('Are you sure','Must be checked before submission'),
	'raeFirst'			=> array('Starting number','The first number to be used'),
	'raeOrder'			=> array('In what order','How to sort the entrants for renumbering'),
	'raeRandom'			=> array('Random','Sort randomly'),
	'raeRiderFirst'		=> array('Firstname','Sort on first name'),
	'raeRiderLast'		=> array('Lastname','Sort on surname'),
	'raeSortA'			=> array('Ascending','Sort A-Z, 1-9'),
	'raeSortD'			=> array('Descending','Sort Z-A, 9-1'),
	'raeSubmit'			=> array('Go ahead, Renumber all entrants','Go ahead! Renumber all entrants'),
	
	'RallyResults'		=> array('Rally results',''),
	'RallySlogan'		=> array('Rally slogan','Brief description of the rally, usually shown on finisher certificates.'),
	'RallyTitle'		=> array('Rally title','Formal title of the rally. Surround an optional part with [ ]; Use | for newlines'),
	
	// Used as 'clear' line in claim reject popup menu
	'RejectReason0'		=> array('0=not rejected','Bonus claim is not rejected'),
	
	// These are actually held in the rallyparams table
	'RejectReason1'		=> array('1=Photo missing',''),
	'RejectReason2'		=> array('2=Photo wrong',''),
	'RejectReason3'		=> array('3=Photo unclear',''),
	'RejectReason4'		=> array('4=Out of hours',''),
	'RejectReason5'		=> array('5=Wrong info',''),
	'RejectReason6'		=> array('6=Reason 6',''),
	'RejectReason7'		=> array('7=Reason 7',''),
	'RejectReason8'		=> array('8=Reason 8',''),
	'RejectReason9'		=> array('9=Ask Rallymaster',''),
	
	'RejectReasons'		=> array('RejectReasons','Reasons for bonus claim rejection'),
	
	'RejectsLit'		=> array('Rejections','Rejected bonus claims'),
	'RenumberGo'		=> array('Go ahead, renumber','Submit the request'),
	
	'RiderFirst'		=> array('Informal name',"Used for repeat mentions on finisher's certificate"),
	'RiderIBA'			=> array('IBA #',"Rider's IBA number if known"),
	'RiderName'			=> array('Rider name','The full name of the rider'),
	'ROUseScore'		=> array('ReadOnly','These fields may not be changed here, use Scoring instead'),
	'SaveCertificate'	=> array('Save certificate','Save the updated copy of this certificate'),
	'SaveEntrantRecord' => array('Save entrant details',''),
	'SaveNewCC'			=> array('Save new CC',''),
	'SaveRallyConfig'	=> array('Update rally configuration parameters',''),
	'SaveScore'			=> array('Save scorecard','Save the updated score/status of this entrant'),
	'ScoredBy'			=> array('Scored by','Who is (or did) scoring this entrant?'),
	'ScoreNow'			=> array('Score now','Switch to live scoring this entrant(new tab)'),
	'ScoreMethodLit'	=> array('Score method',''),
	'Scorer'			=> array('Scorer','Person doing the scoring'),
	'ScoreSaved'		=> array('Scorecard saved','This screen matches the database, no changes yet'),
	'ScoreThis'			=> array('Score this rider',''),
	'ScorexLit'			=> array('ScoreX','Score explanation'),
	'ScoringMethod'		=> array('Scoring method',''),
	'ScoringMethodA'	=> array('Automatic','The system will figure it out'),
	'ScoringMethodC'	=> array('Compound','Bonuses are ticked and points accrued by category'),
	'ScoringMethodM'	=> array('Manual','Entrant scores are entered manually as number of points'),
	'ScoringMethodS'	=> array('Simple','Bonuses are ticked and points added up'),
	
	// Texts for use in setup wizard
	'ScoringMethodWA'	=> array('Automatic','The system takes care of scoring method decisions based on your other configuration choices. This is probably the setting you should use.'),
	'ScoringMethodWC'	=> array('Compound scoring','Scoring makes use of categories to modify bonus scores or provide an extra layer of scoring with/without multipliers'),
	'ScoringMethodWM'	=> array('Manual scoring','Scores will be calculated manually by the scorers and entered as a simple points value'),
	'ScoringMethodWS'	=> array('Simple scoring','The rally uses only ordinary bonuses, special bonuses and combination bonuses'),
	'ScoringNow'		=> array('Being scored now','Is this entrant being scored by someone right now?'),
	'SGroupLit'			=> array('Specials Group','Specials group name'),
	'SGroupMaintHead'	=> array('Specials Bonus Groups','List of groups for specials'),
	'SGroupTypeLit'		=> array('Interface','Radio buttons or checkboxes'),
	'SGroupTypeC'		=> array('Checkbox','Checkboxes, multiple choices'),
	'SGroupTypeR'		=> array('Radio','Radio buttons, one choice'),
	'ShowClaimsButton'	=> array('Claims','Show claims of this bonus by entrant'),
	'ShowClaimsCount'	=> array('Claims','Number of claims by entrants'),
	'ShowEntrants'		=> array('Show entrant picklist','Return to entrant picklist'),
	'ShowMultipliers'	=> array('Show multipliers',''),
	'ShowMultipliersA'	=> array('Automatic','Let the system decide'),
	'ShowMultipliersN'	=> array('Hide','Don\'t show multipliers'),
	'ShowMultipliersY'	=> array('Show','Show multipliers scored'),
	'SMDesc'			=> array('ScoreMaster description','An application designed to make scoring &amp; administration of IBA style motorcycle rallies easy'),
	'SpecialMaintHead'	=> array('Special Bonuses','List of Special bonuses'),
	'SpecialMultLit'	=> array('Multipliers',''),
	'SpecialPointsLit'	=> array('Points',''),
	'SpecialsLit'		=> array('Specials','Special bonuses'),
	'StartDate'			=> array('Start date','The first day of the rally. Rally riding day as opposed to must arrive by day'),
	'StartTime'			=> array('Start time','Official start time. Rally clock starts at this time.'),
	'TeamID'			=> array('Team #','The team number this Entrant is a member of'),
	'TeamRankingH'		=> array('Highest ranked member','Rank team as highest member'),
	'TeamRankingI'		=> array('Individual placing','Rank each team member separately'),
	'TeamRankingL'		=> array('Lowest ranked member','Rank team as lowest member'),
	'TeamRankingText'	=> array('Teams are ranked according to',''),
	
	'TiedPointsRanking'	=> array('Split ties by mileage','In the event of a tie entrants will be ranked by mileage'),	// Miles/Kms
	
	'TimepMaintHead'	=> array('Time Penalties','List of time penalty entries'),

	
	'ToggleScoreX'		=> array('Toggle ScoreX','Click to show/hide score explanation'),
	
	// time penalties
	'tpFactorLit'		=> array('Number','Number of points/mults'),
	'tpFinishLit'		=> array('Finish time','Time this penalty ends'),
	'tpMethod0'			=> array('tpMethod0','Deduct points'),
	'tpMethod1'			=> array('tpMethod1','Deduct multipliers'),
	'tpMethod2'			=> array('tpMethod2','Points per minute'),
	'tpMethod3'			=> array('tpMethod3','Mults per minute'),
	'tpMethodLit'		=> array('Penalty method','Which penalty method applies'),
	'tpStartLit'		=> array('Start time','Time this penalty starts from'),

	'TotalMults'		=> array('Total multipliers','The number of multipliers applied compiling the total score'),
	'TotalPoints'		=> array('Total points','Final rally score'),
	'unset'				=> array('unset, empty, null',''),
	'unused'			=> array('unused',''),
	'UpdateAxis'		=> array('Update these records',''),
	'UpdateBonuses'		=> array('Update bonuses',''),
	'UpdateCategory'	=> array('Update category',''),
	'UpdateCCs'			=> array('Update compound calcs',''),
	'UpdateSGroups'		=> array('Update special groups',''),
	'UpdateTimeP'		=> array('Update time penalties',''),
	
	'Upload'			=> array('Upload','Upload the file to the server'),
	'UploadEntrantsH1'	=> array('Uploading Entrants','Upload Entrants data from spreadsheet'),
	'UploadForce'		=> array('Force overwrite','Overwrite existing Entrant records'),
	'UploadPickFile'	=> array('Pick a file','Please select the input file'),

	'UtlDeleteEntrant'	=> array('Delete entrant','Delete an entrant record from the database'),
	'UtlFolderMaker'	=> array('Folder maker','Generate script to make entrant/bonus folders'),
	'UtlRAE'			=> array('Renumber all entrants','Renumber all the entrants, regardless of status'),
	'UtlRenumEntrant'	=> array('Renumber entrant','Assign a new entrant number to an existing entrant'),
	
	'WizNextPage'		=> array('Next','Save and move to the next page of the wizard'),
	'WizPrevPage'		=> array('Previous','Save and return to the previous wizard page'),
	'WizFinish'			=> array('Finish','Save and finish the wizard'),
	
	// This one's different; both entries are pure text blobs, each presented as an HTML paragraph
	'WizFinishText'		=> array('You have now completed the basic setup of the rally. <span style="font-size: 2em;">&#9786;</span>',
									'When you click [Finish] the main rally setup menu is presented and you can<ul><li>enter the details ' .
									'of ordinary and special bonuses</li><li>alter the text and layout of finisher certificates</li><li>load or enter details ' .
									'of rally entrants</li></ul> and maintain all other aspects of the rally configuration.'),

	'WizTitle'			=> array('This rally needs to be configured, please fill in the blanks',''),
	
	'xlsImporting'		=> array('Importing','Importing entrants data from spreadsheet'),
	'xlsNoSpecfile'		=> array('!specfile','No "specfile" parameter supplied'),
	'xlsNotEmpty'		=> array('Entrants already setup!','The table of entrants is not empty, please tick override and retry'),
	
	'ZapDatabaseOffer'	=> array('Zap / Reinitialize Database','Clear the database ready to start from scratch'),
	'ZapDatabaseZapped'	=> array('Database Zapped/Initialized','The database is empty and ready to start from scratch'),
	
						/* Index 1 used as html content of P, pay attention */
	'ZapDBCaution'		=> array('BEWARE!','This will empty the database of all content except certificate templates. The rally database must then be setup from scratch.'),
						/* Index 1 used as default values in database, beware SQL */
	'ZapDBRallySlogan'	=> array('Toughest Motorcycle Rally','Toughest Motorcycle Rally'),
	'ZapDBRallyTitle'	=> array('IBA Motorcycle Rally','IBA Motorcycle Rally'),
	
	'ZapDBGo'			=> array('Go ahead, Zap the lot!','Execute the zap command'),
	
						/* Index 0 is the truth value of the checkbox, Index 1 is the associated question */
	'ZapDBRUSure1'		=> array('yESiMsURE','I am absolutely sure I want to do this'),
	'ZapDBRUSure2'		=> array('ImReallySure','Quite, quite definitely'),
	'ZapDBRUCancel'		=> array('NoIWont','I don\'t really mean this'),
	
	
	'zzzzzzzzzz'		=> array('zzz','dummy to mark end of array')
	);


// Full/relative path to database file
$DBFILENAME = 'ScoreMaster.db';

	
	
// Uninteresting values
$KONSTANTS['MaxMilesFixedP'] = 0;
$KONSTANTS['MaxMilesFixedM'] = 1;
$KONSTANTS['MaxMilesPerMile'] = 2;
$KONSTANTS['ManualScoring'] = 0;
$KONSTANTS['SimpleScoring'] = 1;
$KONSTANTS['CompoundScoring'] = 2;
$KONSTANTS['AutoScoring'] = 3;
$KONSTANTS['SuppressMults'] = 0;
$KONSTANTS['ShowMults'] = 1;
$KONSTANTS['AutoShowMults'] = 2;
$KONSTANTS['TiedPointsSplit'] = 1;
$KONSTANTS['RankTeamsAsIndividuals'] = 0;	
$KONSTANTS['RankTeamsHighest'] = 1;
$KONSTANTS['RankTeamsLowest'] = 2;
$KONSTANTS['EntrantDNS'] = 0;
$KONSTANTS['EntrantOK'] = 1;
$KONSTANTS['EntrantFinisher'] = 8;
$KONSTANTS['EntrantDNF'] = 3;
$KONSTANTS['BeingScored'] = 1;
$KONSTANTS['NotBeingScored'] = 0;
$KONSTANTS['AreYouSureYes'] = 'yesIamSure';
$KONSTANTS['TiesSplitByMiles'] = 1;
$KONSTANTS['TeamRankIndividuals'] = 0;
$KONSTANTS['TeamRankHighest'] = 1;
$KONSTANTS['TeamRankLowest'] = 2;

// Beware, these next two used for combinations & catcompounds
$KONSTANTS['ComboScoreMethodPoints'] = 0;
$KONSTANTS['ComboScoreMethodMults'] = 1;

/* Each simple bonus may be classified using
 * this number of categories. This reflects 
 * the database structure, it may not be
 * arbitrarily increased.
 */
$KONSTANTS['NUMBER_OF_COMPOUND_AXES'] = 3;

$KONSTANTS['DefaultOdoScaleFactor'] = 1;
$KONSTANTS['DefaultEntrantStatus'] = $KONSTANTS['EntrantOK'];




// Common subroutines below here; nothing translateable below
	
	
// Open the database	
try
{
	$DB = new SQLite3($DBFILENAME);
} catch(Exception $ex) {
	echo("OMG ".$ex->getMessage().' file=[ '.$DBFILENAME.' ]');
}

$RALLY_INITIALISED = (1==1);
$HTML_STARTED = false;

// Common subroutines

function properName($enteredName)
// Used to fix names entered online; not everyone knows about shift keys
// If they've tried, I just return what they entered but if not I'll
// return initial capitals followed by lowercase
{
	$x = explode(' ',$enteredName);
	$z = false;
	for ($i = 0; $i < sizeof($x); $i++)
		if (ctype_lower($x[$i]) || ctype_upper($x[$i]))
			$z = true;
	if ($z)
		return ucwords(strtolower(str_replace('  ',' ',$enteredName)));
	else
		return str_replace('  ',' ',$enteredName);
	
}

function splitDatetime($dt)
/* Accept either 'T' or space as splitting date/time */
{
	if (strpos($dt,'T'))
		$S = 'T';
	else if (strpos($dt,' '))
		$S = ' ';
	else
		return ['','']; 
	
	$dtx = explode($S,$dt);
	return $dtx;
		
}

function getValueFromDB($sql,$col,$defaultvalue)
{
	global $DB;
	
	try {
		$R = $DB->query($sql);
		if ($rd = $R->fetchArray())
			return $rd[$col];
		else
			return $defaultvalue;
	} catch (Exception $ex) {
		return $defaultvalue;
	}
}

function startHtml($otherInfo = '')
{
	global $DB, $TAGS, $KONSTANTS, $HTML_STARTED;
	global $HOME_URL;
	
	if ($HTML_STARTED)
		return;
	
	$HTML_STARTED = true;
	
	$R = $DB->query('SELECT * FROM rallyparams');
	$rd = $R->fetchArray();
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>ScoreMaster</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="score.css?ver=<?= filemtime('score.css')?>">
<script src="score.js?ver=<?= filemtime('score.js')?>" defer="defer"></script>
</head>
<body onload="bodyLoaded();">
<?php echo('<input type="hidden" id="BasicDistanceUnits" value="'.$KONSTANTS['BasicDistanceUnits'].'"/>'); ?>
<div id="header">
<?php	
	echo("<a href=\"".$HOME_URL);
	if (isset($_REQUEST['ScorerName']))
	{
		$scorer = $_REQUEST['ScorerName'];
		if ($scorer <> '')
			echo("?ScorerName=$scorer&amp;clear");
	}
	echo("\">");
	echo('<span id="hdrRallyTitle" title="'.htmlspecialchars($TAGS['gblMainMenu'][1]).'"> '.htmlspecialchars(preg_replace('/\[|\]|\|/','',$rd['RallyTitle'])).' </span>');
	echo("</a>");
	echo('<span id="hdrOtherInfo">'.$otherInfo.'</span>');
	echo("\r\n</div>\r\n");
	
}
function showFooter()
{
	global $DB, $TAGS;
	echo('<div id="footer">');
	echo('<span id="ftrAdminMenu" title="'.$TAGS['AdminMenu'][1].'"><a href="admin.php">'.$TAGS['AdminMenu'][0].'</a></span></div>');
	echo('</body></html>');
}

function rally_params_established()
{
	global $DB;
	
	$sql = "SELECT DBState FROM rallyparams";
	$R = $DB->query($sql);
	$rd = $R->fetchArray();
	return ($rd['DBState'] > 0);
}

?>

