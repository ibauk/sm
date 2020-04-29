<?php


// customvars-de.php    German literals

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
 * it under the terms of the MIT License
 *
 * IBAUK-SCOREMASTER is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * MIT License for more details.
 *
 */


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

//$KONSTANTS['BasicDistanceUnits'] = $KONSTANTS['DistanceIsMiles'];
$KONSTANTS['BasicDistanceUnits'] = $KONSTANTS['DistanceIsKilometres'];


// Default settings

// This setting should normally reflect BasicDistanceUnits above
//$KONSTANTS['DefaultKmsOdo'] = $KONSTANTS['OdoCountsMiles']; 
$KONSTANTS['DefaultKmsOdo'] = $KONSTANTS['OdoCountsKilometres'];
$KONSTANTS['DecimalPointIsComma']  = true;


// Used when setting up new entrants onscreen

$KONSTANTS['DefaultCountry'] = 'DE';

// Assume this value, which may be blank, if not overridden at run time
$KONSTANTS['DefaultScorer'] = 'Bob';


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
	'AdmClaims'			=> array('Claims','Access log of bonus claims'),
	'AdmCombosTable'	=> array('Combinations','View/edit combination bonuses'),
	'AdmCompoundCalcs'	=> array('Compound calculations','Maintain table of calculation records'),
	'AdmConfirm'		=> array('Reconcile scores','Confirm scorecards as accurate'),
	'AdmDoBlank'		=> array('Post score ticksheet','Show blank score with reject reasons sheet ready for printing'),
	'AdmDoBlankB4'		=> array('Scoring ticksheet','Show paper scoring log sheet ready for printing'),
	'AdmDoScoring'		=> array('Scoring','Score individual entrants'),
	'AdmEditCert'		=> array('Edit certificate content','Edit the HTML &amp; CSS of the master certificate'),
	'AdmEntrants'		=> array('Entrants table','View/edit list of Entrants'),
	'AdmEntrantChecks'	=> array('Check-out/in','Entrant checks @ start/end of rally'),
	'AdmEntrantsHeader'	=> array('Entrants',''),
	'AdmExportFinishers'=> array('Export finishers','Save CSV containing details of finishers'),
	'AdmImportEntrants'	=> array('Import Entrants','Load entrant details from a spreadsheet'),
	'AdminMenu'			=> array('Rally Administration','Logon to carry out administration (not scoring) of the rally'),
	'AdmMenuHeader'		=> array('Rally administration',''),
	'AdmNewBonus'		=> array('Setup new bonus','Add details of another bonus'),
	'AdmNewEntrant'		=> array('Setup new entrant','Add details of another entrant'),
	'AdmPrintCerts'		=> array('Print finisher certificates','Print certificates for finishers'),
	'AdmPrintQlist'		=> array('Finisher quicklist','Print quick list of finishers'),
	'AdmPrintScoreX'	=> array('Score explanations','Print score explanations for everyone not DNS'),
	'AdmRallyParams'	=> array('Rally parameters','View/edit current rally parameters'),
	'AdmRankEntries'	=> array('Rank finishers','Calculate and apply the rank of each finisher'),
	'AdmSelectTag'		=> array('Search by keyword','Choose a tag to list relevant functions'),
	'AdmSetupHeader'	=> array('Setup',''),
	'AdmSetupWiz'		=> array('Setup wizard','Basic rally setup wizard'),
	'AdmSGroups'		=> array('Specials groups','Maintain groups of specials'),
	'AdmShowSetup'		=> array('Rally setup &amp; config','View/maintain rally configuration records'),
	'AdmShowTagMatches'	=> array('Items matching ','Showing functions matching tag '),
	'AdmSpecialTable'	=> array('Special bonuses','View/edit special bonuses'),
	'AdmSpeedPenalties'	=> array('Speed penalties','Maintain table of speed penalties'),
	'AdmThemes'			=> array('Display themes','Change the colourways used'),
	'AdmTimePenalties'	=> array('Time penalties','Maintain table of time penalties'),
	'AdmUtilHeader'		=> array('Utility functions',',,'),
	
	'AskEnabledSave'	=> array('Save this scoresheet?',''),
	'AskMinutes'		=> array('Variable?','Ask for this during scoring'),
	'AskMinutes0'		=> array('Fixed',''),
	'AskMinutes1'		=> array('Variable',''),
	'AskPoints'			=> array('Variable?','Ask for points value during scoring'),
	'AskPoints0'		=> array('Fixed','Points value is fixed'),
	'AskPoints1'		=> array('Variable','Points value entered during scoring'),
	'AxisLit'			=> array('Axis','The set of categories this rule applies to'),
	'AutoRank'			=> array('Automatic Ranking','Rank automatically recalculated when scorecard updated'),
	'BasicDetails'		=> array('Basic',''),
	'BasicRallyConfig'	=> array('Basic','Basic rally configuration fields'),
	'BCMethod'			=> array('Bonus claiming','Method of bonus claim: 0=unknown,1=EBC,2=paper'),
	'BCMethod0'			=> array('unknown',''),
	'BCMethod1'			=> array('EBC','Electronic Bonus Claiming'),
	'BCMethod2'			=> array('Paper','Paper claiming'),
	'Bike'				=> array('Bike','Make &amp; model of bike'),
	'BikeReg'			=> array('Registration','Registration number of the bike if known'),
	'BonusClaimDecision'=> array('Decision','The status of this claim'),
	'BonusClaimOK'		=> array('Good claim',''),
	'BonusClaimTime'	=> array('Claim time','The claimed time of this Bonus claim'),
	'BonusClaimUndecided'
						=> array('undecided',''),	
	'BonusesLit'		=> array('Bonuses','Ordinary bonuses'),
	'BonusIDLit'		=> array('BonusID',''),
	'BonusListLit'		=> array('Underlying bonuses','Comma separated list of ordinary, special &amp combo bonus IDs'),
	'BonusMaintHead'	=> array('Ordinary Bonuses','List of Ordinary (geographic) bonuses'),
	'BonusPoints'		=> array('Points','The basic points value of this bonus'),
	'BriefDescLit'		=> array('Description',''),
	'CalculatedAvgSpeed'=> array('','Calculated average speed'),
	'CalcMaintHead'		=> array('Compound Calculation Rules','List of rules for compound score calculations'),
	'Cat0Label'			=> array('Total','If summing across axes, use this label'),
	'Cat1Label'			=> array('X-axis is','What do values on the X-axis represent?'),
	'Cat2Label'			=> array('Y-axis is','What do values on the Y-axis represent?'),	
	'Cat3Label'			=> array('Z-axis is','What do values on the Z-axis represent?'),
	'Cat4Label'			=> array('Axis 4 is','What do values on this axis represent?'),
	'Cat5Label'			=> array('Axis 5 is','What do values on this axis represent?'),
	'Cat6Label'			=> array('Axis 6 is','What do values on this axis represent?'),
	'Cat7Label'			=> array('Axis 7 is','What do values on this axis represent?'),
	'Cat8Label'			=> array('Axis 8 is','What do values on this axis represent?'),
	'Cat9Label'			=> array('Axis 9 is','What do values on this axis represent?'),
	'CatBriefDesc'		=> array('Description',''),
	'CategoryLit'		=> array('Category',''),
	'CatEntry'			=> array('Category','The number of this category within the axis'),
	'CatEntryCC'		=> array('Which category','Which cat(s) does this rule apply to'),
	'CatExplainer'		=> array('CatExplainer','You can amend the description of categories or delete them entirely. New entries must have an category number which is unique within the axis.'),
	'CatNotUsed'		=> array('(not used)',''),
	'ccApplyToAll'		=> array('all cats','applies to all cats'),
	'ccCompulsory'		=> array('Ruletype','1=DNF if not triggered;2=DNF if triggered; else 0'),
	'ccCompulsory0'		=> array('Regular rule','Ordinary scoring rule'),
	'ccCompulsory1'		=> array('Untrig=DNF','DNF unless this rule triggered'),
	'ccCompulsory2'		=> array('Trigger=DNF','DNF if this rule triggered'),
	'ccCompulsory3'		=> array('Placeholder','Placeholder rule'),
	
	'CertExplainer'		=> array('Certificates are "web" documents comprising well-formed HTML and CSS parts.',
									'Please carefully specify the certificate layout and content in the texts below.'),
	'CertExplainerW'	=> array('Certificates are "web" documents. This editor allows you to define the content and layout in a user-friendly way.',''),
	
	'CertTitle'			=> array('Title','Description of this certificate class'),

	'cl_Applied'		=> array('Applied?','Has this claim been applied to the entrant\'s scorecard?'),
	'cl_AppliedHdr'		=> array('Applied',''),
	'cl_BonusHdr'		=> array('Bonus',''),
	'cl_ClaimedHdr'		=> array('Claimed',''),
	'cl_DDLabel'		=> array('New default','Default decision when posting new claims'),
	'cl_DecisionHdr'	=> array('Decision',''),
	'cl_EntrantHdr'		=> array('Entrant',''),
	'cl_FilterBonus'	=> array('B#','Filter list by Bonus number'),
	'cl_FilterEntrant'	=> array('E#','Filter list by Entrant number'),
	'cl_LoggedHdr'		=> array('Logged',''),
	'cl_OdoHdr'			=> array('Odo',''),
	'cl_RefreshList'	=> array('&circlearrowright;','Refresh the claims list'),
	'cl_PostNewClaim'	=> array('Post new claim',''),
	'cl_showAllA'		=> array('show all','Filter list on applied to scorecard status'),
	'cl_showNotA'		=> array('unapplied',''),
	'cl_showOnlyA'		=> array('applied',''),
	'cl_showAllD'		=> array('show all','Filter list on decided/judged status'),
	'cl_showNotD'		=> array('undecided',''),
	'cl_showOnlyD'		=> array('decided',''),
	
	'Class'				=> array('Class #','The certificate class applicable'),
	'ChooseEntrant'		=> array('Choose entrant','Pick an entrant from this list'),
	'ComboIDLit'		=> array('ComboID',''),
	'ComboMaintHead'	=> array('Combination Bonuses','List of Combination bonuses'),
	'ComboScoreMethod'	=> array('Scoretype','Does this combo score points? or multipliers?'),
	'CombosLit'			=> array('Combinations','Combination bonuses'),
	'CommaSeparated'	=> array('Comma separated list',''),
	'CompulsoryBonus'	=> array('Compulsory?','This bonus is required for Finisher status'),
	'CompulsoryBonus0'	=> array('Optional',''),
	'CompulsoryBonus1'	=> array('Compulsory',''),
	'ConfirmDelEntrant'	=> array('Delete this entrant?','Confirm deletion of this entrant'),
	'ConfirmedBonusTick'=> array('&#10004;','This bonus has been confirmed/reconciled'),
	
	'ContactDetails'	=> array('Contacts',''),
	
	'CorrectedMiles'	=> array('Km ridden','Official rally distance'),	// Miles/Kms
	
	'Country'			=> array('Country',"Entrant's home country"),
	'dberroragain'		=> array('Please resubmit. If problem persists tell Bob','The database save failed, probably temporary lock issue'),
	
	'dblclickprint'		=> array('Double-click to print',''),
	'DeleteBonus'		=> array('Delete this bonus',''),
	'DeleteClaim'		=> array('Delete this claim',''),
	'DeleteEntrant'		=> array('Go ahead, delete the bugger!','Execute the deletion'),
	'DeleteEntryLit'	=> array('Delete?','Delete this record from the database?'),
	'EntrantDNF'		=> array('DNF','Did not qualify as a finisher'),
	'EntrantDNS'		=> array('DNS','Entrant failed to start the rally'),
	'EntrantEmail'		=> array('Entrant email','Email for this entrant'),
	'EntrantFinisher'	=> array('Finisher','Rally finisher'),
	'EntrantID'			=> array('Motorrad #','The unique reference for this Entrant'),
	'EntrantListBonus'	=> array('Entrants claiming bonus','List of entrants claiming a particular bonus'),
	'EntrantListCheck'	=> array('Entrant check-ins/outs','Choose an entrant for checkin-in or checking-out'),
	'EntrantListCombo'	=> array('Entrants claiming combo','List of entrants claiming a particular combination'),
	'EntrantListFull'	=> array('Full list of Entrants','Choose an entrant to view/edit his/her details'),
	'EntrantListSpecial'=> array('Entrants claiming special','List of entrants claiming a particular special'),
	'EntrantOK'			=> array('ok','Status normal'),
	'EntrantPhone'		=> array('Entrant phone','Contact phone for this entrant'),
	'EntrantStatus'		=> array('Status','The current rally status of this entrant'),
	
							// Careful! this is executed as PHP, get it right.
	'EntrantStatusV'	=> array('array("0" => "DNS", "1" => "ok", "8" => "Finisher", "3" => "DNF");','array used for vertical tables'),
	
	'ExcessMileage'		=> array('Excess kms',''),						// Miles/Kms
	
	'ExtraData'			=> array('ExtraData','Extra data to be passed on to the main database. Format is <i>name</i>=<i>value</i>'),
	
	'FetchCert'			=> array('Fetch certificate','Fetch the HTML, CSS &amp; options for this certificate'),
	'FinishDate'		=> array('Finish date','Der letzte Reittag der Rallye.'),
	'FinishDateE'		=> array('Finish date','Der letzte Reittag der Rallye.'),
	'FinishersExported'	=> array('Finishers exported!','Finisher details exported to CSV'),
	'FinishPosition'	=> array('Final place','Finisher ranking position',''),
	'FinishTime'		=> array('Ziel zeit','Offizielle Zielzeit. Teilnehmer, die später fertig werden, sind DNF'),
	'FinishTimeE'		=> array('Ziel zeit','Official finish time. The check-in time'),

	'FuelBalance'		=> array('Fuel','Fuel distance remaining'),
	'FuelWarning'		=> array('OUT OF FUEL!','This leg exceeded the remaining fuel capacity'),
	'FullDetails'		=> array('Full details','Show the complete record'),

	'gblMainMenu'		=> array('Main menu','Return to main menu'),
	
	'GroupNameLit'		=> array('Special group','Group used for presentation purposes'),
	'HelpAbout'			=> array('About ScoreMaster',''),
	
	// If an imported bike field matches this re, replace with the phrase
	//                            re    phrase
	'ImportBikeTBC'		=> array('/tbc|tba|unknown/i','motorbike','Replace re with literal'),
	'InsertNewCC'		=> array('Enter new compound calc',''),
	'InsertNewCombo'	=> array('New combo','Setup a new combination bonus'),
	'jodit_Borders'		=> array('Print borders',''),
	'jodit_Borders_Double'
						=> array('Double',''),
	'jodit_Borders_None'=> array('None',''),
	'jodit_Borders_Solid'
						=> array('Solid',''),
	'jodit_InsertField'	=> array('Insert database field',''),
	
	'LegendPenalties'	=> array('Penalties',''),
	'LegendScoring'		=> array('Scoring &amp; Ranking',''),
	'LegendTeams'		=> array('Teams'),
	'login'				=> array('login','Go on, log me in then!'),
	'LogoutScorer'		=> array('Logout','Log the named scorer off this terminal'),

	'magicword'			=> array('Magic','The \'magic\' word associated with this claim'),
	'MarkConfirmed'		=> array('Mark as confirmed','Mark all bonus claim decisions as having been confirmed'),
	
	'MaxHours'			=> array('Max hours','Die Dauer der Rallye in Stunden. Wird zur Berechnung der DNF-Zeit verwendet und kann auf Zertifikaten angezeigt werden'),
	'MaxMilesFixedM'	=> array('Multiplier','Excess distance incurs deduction of multipliers'),							// Miles/Kms
	'MaxMilesFixedP'	=> array('Fixed points','Excess distance incurs fixed points deduction'),							// Miles/Kms
	'MaxMilesPerMile'	=> array('Points per KM','Excess distance incurs points deduction per excess kilometre'),				// Miles/Kms
	'MaxMilesPoints'	=> array('Points or Multipliers deducted','Number of points or multipliers for excess distance'),	// Miles/Kms
	'MaxMilesUsed'		=> array('Tick if maximum kms used','Werden Teilnehmer DNF sein, wenn sie eine maximale Entfernung überschreiten?'),	// Miles/Kms
	'MilesPenaltyText'	=> array('Distance penalty deduction',''),															// Miles/Kms
	'MinimumTicks'		=> array('MinTicks','The minimum number of underlying bonus ticks needed to score this combo; 0=all'),
	'MinMiles'			=> array('Minimum kms','Minimum distance to qualify as a finisher'),						// Miles/Kms
	'MinMilesUsed'		=> array('Tick if minimum kms used','Müssen Teilnehmer eine Mindeststrecke zurücklegen, um sich als Finisher zu qualifizieren?'), // Miles/Kms
	
	'MinPoints'			=> array('Minimum points','Minimum points scored to be a finisher'),
	'MinPointsUsed'		=> array('Tick if minimum points used','Müssen Teilnehmer eine Mindestpunktzahl erreichen, um sich als Finisher zu qualifizieren?'),
	'ModBonus0'			=> array('Axis','Affects compound axis score'),
	'ModBonus1'			=> array('Bonus','Modifies bonus score'),
	'ModBonusLit'		=> array('Usage','1=This calc directly affects bonus value, 0=This calc builds the axis score'),
	'NameFilter'		=> array('Rider name','Use this to filter the list of riders shown below'),
	'NewEntrantNum'		=> array('New number','What\'s the number number for this entrant'),
	'NewPlaceholder'	=> array('start new entry','Placeholder for new table entries'),
	'NextTimeMins'		=> array('Time next leg','Enter estimated time of the next leg eg: 1h 35m; 1.35,1:35'),
	'NMethod-1'			=> array('Unused','Not used'),
	'NMethod0'			=> array('Bonuses/cat','No of bonuses per cat'),
	'NMethod1'			=> array('Cats/axis','No of NZ cats per axis'),
	'NMethodLit'		=> array('NMethod','0=# entries per cat, 1=# of NZ cats, -1=record not used'),
	'NMinLit'			=> array('NMin','The minimum value of N before this rule is triggered'),
	'NoCerts2Print'		=> array('Sorry, no certificates to print.',''),
	
	'NoKName'			=> array('NoK name','Name of Next of Kin'),
	'NoKPhone'			=> array('NoK phone','Phone number for Next of Kin'),
	'NoKRelation'		=> array('NoK relation','Relationship of Next of Kin'),
	'NoScoreX2Print'	=> array('Sorry, no score explanations to print.',''),
	'nowlit'			=> array('Now','Record the current date/time'),
	'NPowerLit'			=> array('NPower',"If bonus rule &amp; this is 0, R=bonuspoints(N-1)\n".
											"If bonus rule &amp; this > 0, R=bonuspoints(this^(N-1))\n".
											"If axis rule &amp; this is 0, R=N\n".
											"If axis rule &amp; this <> 0, R=this value"),
											
	'OdoCheckFinish'	=> array('Odo check finish','The odometer reading at the end of the odo check'),					// Miles/Kms
	'OdoCheckMiles'		=> array('Odo check distance','The length of the route used to check the accuracy of odometers'),	// Miles/Kms
	'OdoCheckStart'		=> array('Odo check start','The reading at the start of the odometer check'),						// Miles/Kms
	'OdoCheckTrip'		=> array('Odo check trip','What distance did the trip meter record?'),								// Miles/Kms
	'OdoCheckUsed'		=> array('Tick if odo check used','Müssen Teilnehmer eine Kilometerzähler-Kontrollroute fahren??'),	// Miles/Kms
	'OdoKms'			=> array('Odo counts',''),																			// Miles/Kms
	'OdoKmsK'			=> array('kilometres',''),																			// Miles/Kms
	'OdoKmsM'			=> array('miles',''),																				// Miles/Kms
	'Odometer'			=> array('Odo&nbsp;readings',''),																		// Miles/Kms
	'OdoRallyStart'		=> array('Km start','The reading at the start of the rally'),									// Miles/Kms
	'OdoRallyFinish'	=> array('Km finish','The odometer reading at the end of the rally'),							// Miles/Kms
	'OdoReadingLit'		=> array('Odo','Odo reading'),
	'OdoScaleFactor'	=> array('Correction factor','The number to multiply odo readings to get true distance'),			// Miles/Kms
	
	'OfferScore'		=> array('OfferScore','Would you like to help score this rally? If so, please tell me your name'),
	'optCompulsory'		=> array('Compulsory',''),
	'optOptional'		=> array('Optional',''),
	'PenaltyMaxMiles'	=> array('Max kms (penalties)','Distance beyond this incurs penalties; 0=doesn\'t apply'),			// Miles/Kms
	'PenaltyMilesDNF'	=> array('DNF distance','Distance beyond here result in DNF; 0=doesn\'t apply'),						// Miles/Kms
	
	'PickAnEntrant'		=> array('Pick an entrant','Pick an entrant using the list below or by entering an Entrant number. Type a name to filter the list.'),
	'PillionFirst'		=> array('Informal name',"Used for repeat mentions on finisher's certificate"),
	'PillionIBA'		=> array('IBA #',"Pillion's IBA number if known"),
	'PillionName'		=> array('Pillion','Full name of the pillion rider'),
	'PointsMults'		=> array('Result','Results in points or multipliers'),
	'PointsMults0'		=> array('PointsMults0','Points'),
	'PointsMults1'		=> array('PointsMults1','Multipliers'),
	
	'PreviewCert'		=> array('Preview','What will this certificate look like'),
	
	// Quick dirty list headings
	'qPlace'			=> array('Platz',''),
	'qName'				=> array('Name',''),
	
	'qMiles'			=> array('Km',''),						// Miles/Kms
	
	'qPoints'			=> array('Punkte',''),
	
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
	
	'RallyResults'		=> array('Rally&nbsp;results',''),
	'RallySlogan'		=> array('Rally slogan','Kurze Beschreibung der Rallye, normalerweise auf Finisher-Zertifikaten angegeben.'),
	'RallyTitle'		=> array('Rallye titel','Formeller Titel der Rallye. Surround an optional part with [ ]; Use | for newlines'),
	'rcCategories'		=> array('Categories','Schedule of categories used for scoring'),
	'RecordSaved'		=> array('Record saved',''),
	
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
	'RestMinutesLit'	=> array('Rest minutes','The number of minutes of rest/sleep this bonus represents'),
	
	'RiderFirst'		=> array('Informal name',"Used for repeat mentions on finisher's certificate"),
	'RiderIBA'			=> array('IBA #',"Rider's IBA number if known"),
	'RiderName'			=> array('Rider name','The full name of the rider'),
	'ROUseScore'		=> array('ReadOnly','These fields may not be changed here, use Scoring instead'),
	'SaveCertificate'	=> array('Save certificate','Save the updated copy of this certificate'),
	'SaveEntrantRecord' => array('Save entrant details',''),
	'SaveNewCC'			=> array('Update database',''),
	'SaveRallyConfig'	=> array('Update rally configuration parameters',''),
	'SaveRecord'		=> array('Save record','Save record to the database'),
	'SaveScore'			=> array('Save scorecard','Save the updated score/status of this entrant'),
	'SaveSettings'		=> array('Save settings','Save these details to the database'),
	'ScorecardIsDirty'	=> array('!?!','Scorecard is dirty'),
	
	'ScoredBy'			=> array('Scored by','Who is (or did) scoring this entrant?'),
	'ScoreNow'			=> array('Score now','Switch to live scoring this entrant(new tab)'),
	'ScoreMethodLit'	=> array('Score method',''),
	'Scorer'			=> array('Scorer','Person doing the scoring'),
	'ScoreSaved'		=> array('Scorecard saved','This screen matches the database, no changes yet'),
	'ScoreThis'			=> array('Score this rider',''),
	'ScoreValue'		=> array('Value(s)','The number of points or multipliers; use commas for variable values starting with MinTicks'),
	'ScorexHints'		=> array('Right-click to reorder; double-click to print',''),
	'ScorexLit'			=> array('ScoreX','Score explanation'),
	'ScoringMethod'		=> array('Scoring method',''),
	'ScoringMethodA'	=> array('Automatic','The system will figure it out'),
	'ScoringMethodC'	=> array('Compound','Bonuses are ticked and points accrued by category'),
	'ScoringMethodM'	=> array('Manual','Entrant scores are entered manually as number of points'),
	'ScoringMethodS'	=> array('Simple','Bonuses are ticked and points added up'),
	
	// Texts for use in setup wizard
	'ScoringMethodWA'	=> array('Automatic','Das System kümmert sich um Entscheidungen zu Bewertungsmethoden basierend auf Ihren anderen Konfigurationsoptionen. Dies ist wahrscheinlich die Einstellung, die Sie verwenden sollten.'),
	'ScoringMethodWC'	=> array('Compound scoring','Bei der Wertung werden Kategorien verwendet, um die Bonuspunktzahlen zu ändern oder eine zusätzliche Bewertungsebene mit / ohne Multiplikatoren bereitzustellen'),
	'ScoringMethodWM'	=> array('Manual scoring','Die Punktzahlen werden von den Torschützen manuell berechnet und als einfacher Punktewert eingegeben'),
	'ScoringMethodWS'	=> array('Simple scoring','Bei der Rallye werden nur normale Boni, Spezialboni und Kombinationsboni verwendet'),
	'ScoringNow'		=> array('Being scored now','Is this entrant being scored by someone right now?'),
	'SettingsSaved'		=> array('Settings saved','This screen matches the database, no changes yet'),
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
	'SpecialMultLit'	=> array('Multipliers','Used in compound bonus calculations'),
	'SpecialPointsLit'	=> array('Points',''),
	'SpecialsLit'		=> array('Specials','Special bonuses'),
	'SpeedPExplain'		=> array('Penalties for speeding based on average speed. The unit is either MPH or Km/h depending on the rally setting. Only the highest matching speed is applied.',''),
	'spMinSpeedCol'		=> array('Speed','Minimum average speed'),
	'spPenaltyPointsCol'
						=> array('Points','Number of penalty points'),
	'spPenaltyTypeCol'	=> array('Penalty','Type of penalty'),
	'spPenaltyTypeDNF'	=> array('DNF','Penalty applied is DNF'),
	'spPenaltyTypePoints'
						=> array('Points','Penalty points'),
	'StartDate'			=> array('Start date','Der erste Tag der Rallye. Rallye-Reittag im Gegensatz zu muss am Tag ankommen'),
	'StartDateE'		=> array('Start date','Der erste Tag der Rallye. Rallye-Reittag im Gegensatz zu muss am Tag ankommen'),
	'StartTime'			=> array('Start zeit/time','Offizielle Startzeit. Die Rallye-Uhr beginnt um diese Zeit.'),
	'StartTimeE'		=> array('Start zeit/time','Offizielle Startzeit. Die Rallye-Uhr beginnt um diese Zeit.'),
	
	'TeamID'			=> array('Team #','The team number this Entrant is a member of'),
	'TeamRankingC'		=> array('Team cloning','Team scores are cloned to all members'),
	'TeamRankingH'		=> array('Highest ranked member','Rank team as highest member'),
	'TeamRankingI'		=> array('Individual placing','Rank each team member separately'),
	'TeamRankingL'		=> array('Lowest ranked member','Rank team as lowest member'),
	'TeamRankingText'	=> array('Teams are ranked according to',''),
	'TeamWatch'			=> array('Team watch','Inspect claims history looking for potential teams/missed claims'),
	
	'ThemeApplyLit'		=> array('Yes, apply this theme','Yes, apply this theme'),
	'ThemeLit'			=> array('Theme','The name of the theme to apply'),

	'TiedPointsRanking'	=> array('Split ties by distance','In the event of a tie entrants will be ranked by kilometres'),	// Miles/Kms
	
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
	'tpTimeSpec0'		=> array('Date &amp; time','Absolute date/time'),
	'tpTimeSpec1'		=> array('Mins &lt; RallyDNF','Minutes before overall rally DNF'),
	'tpTimeSpec2'		=> array('Mins &lt; EntrantDNF','Minutes before individual entrant DNF'),
	'tpTimeSpecLit'		=> array('TimeSpec','Time specification flag'),
	
	'TotalMults'		=> array('Total multipliers','The number of multipliers applied compiling the total score'),
	'TotalPoints'		=> array('Total points','Final rally score'),
	
	// Titles for browser tabs
	'ttWelcome'			=> array('ScoreMaster','Welcome page for anyone'),
	'ttAdminMenu'		=> array('ScoreMaster','Showing main admin menu'),
	'ttAbout'			=> array('SM:About',''),
	'ttEntrants'		=> array('SM:Entrants',''),
	'ttFinishers'		=> array('SM:Finishers','Quicklists'),
	'ttCertificates'	=> array('Certificates',''),
	'ttScoreX'			=> array('ScoreX',''),
	'ttTeams'			=> array('SM:Teams','Potential team matches'),
	'ttUpload'			=> array('SM:Upload','File pick screen'),
	'ttImport'			=> array('SM:Import','Importing'),
	'ttScoring'			=> array('Scoring','Logged on to scoring'),
	'ttSetup'			=> array('SM:Setup','Edit setups'),
	
	
	'unset'				=> array('unset, empty, null',''),
	'unused'			=> array('unused',''),
	'UpdateAxis'		=> array('Update these records',''),
	'UpdateBonuses'		=> array('Update bonuses',''),
	'UpdateCategory'	=> array('Update category',''),
	'UpdateCCs'			=> array('Update compound calcs',''),
	'UpdateCombo'		=> array('Update combination','Save this record to the database'),
	'UpdateSGroups'		=> array('Update special groups',''),
	'UpdateTimeP'		=> array('Update time penalties',''),
	
	'Upload'			=> array('Upload','Upload the file to the server'),
	'UploadEntrantsH1'	=> array('Uploading Entrants','Upload Entrants data from spreadsheet'),
	'UploadForce'		=> array('Force overwrite','Overwrite existing Entrant records'),
	'UploadPickFile'	=> array('Pick a file','Please select the input file'),

	'UtlDeleteEntrant'	=> array('Delete entrant','Delete an entrant record from the database'),
	'UtlFindEntrant'	=> array('Find entrant','Search for a particular entrant'),
	'UtlFolderMaker'	=> array('Folder maker','Generate script to make entrant/bonus folders'),
	'UtlRAE'			=> array('Renumber all entrants','Renumber all the entrants, regardless of status'),
	'UtlRenumEntrant'	=> array('Renumber entrant','Assign a new entrant number to an existing entrant'),
	
	'WizNextPage'		=> array('Next','Save and move to the next page of the wizard'),
	'WizPrevPage'		=> array('Previous','Save and return to the previous wizard page'),
	'WizFinish'			=> array('Finish','Save and finish the wizard'),
	
	// This one's different; both entries are pure text blobs, each presented as an HTML paragraph
	'WizFinishText'		=> array('Sie haben nun die Grundeinstellung der Rallye abgeschlossen. <span style="font-size: 2em;">&#9786;</span>',
									'Wenn Sie auf [Finish] klicken, wird das Hauptmenü für die Rallye-Einrichtung angezeigt und Sie können<ul><li>Geben Sie die Details der normalen und speziellen Boni ein ' .
									'</li><li>Ändern Sie den Text und das Layout der Finisher-Zertifikate</li><li>Laden oder geben Sie Details der Rallye-Teilnehmer ein' .
									'</li></ul> und pflegen Sie alle anderen Aspekte der Rallye-Konfiguration.'),

	'WizTitle'			=> array('Diese Rallye muss konfiguriert werden, bitte füllen Sie die Lücken aus',''),
	
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

// This is a list of makes/models of bike used to clean up the values entered by their owners during import
// Each key here uses the letter case shown here, mostly uppercase but could be anything
$KNOWN_BIKE_WORDS = array('BMW','BSA','cc','DCT','DVT','FJR','GS','GSA','GT','GTR','Harley-Davidson','KLE','KTM','LC',$TAGS['ImportBikeTBC'][1],'MV','RS','RT','SE','ST','TVS','VFR','V-Strom','VTR','XC','XRT');


// Full/relative path to database file
$DBFILENAME = 'ScoreMaster.db';

	
	

?>
