/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * score.js
 *
 * I provide automatic scoring in the browser.
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

"use strict";

/*
 *	2.1	Only autoset EntrantStatus to Finisher if status was 'ok' and CorrectedMiles > 0
 *	2.1	Multiple special groups; cleanup explanations
 *	2.1	Accept/Reject claim handling; 
 *	2.1 kms/mile, mile/kms handling
 *	2.1 Odo check trip reading - used though not stored
 *  2.2	Variable specials
 *	2.3	OdoScaleFactor SanityCheck
 *	2.4	Variable combos, flexible axes
 *
 */
 

// No translateable literals below here


const KmsPerMile = 1.60934;

 
const EntrantDNS = 0;
const EntrantOK = 1;
const EntrantFinisher = 8;
const EntrantDNF = 3;

const COMPULSORYBONUS = '1';
const MUSTNOTMATCH = '2';

const RULE_TRIGGERED = '1';
const RULE_NOT_TRIGGERED = '0';

const MMM_FixedPoints = 0;
const MMM_Multipliers = 1;
const MMM_PointsPerMile = 2;

const TPM_MultPerMin = 3;
const TPM_PointsPerMin = 2;
const TPM_FixedMult = 1;
const TPM_FixedPoints = 0;

/* Combinations */
const CMB_ScorePoints = 0;
const CMB_ScoreMults = 1;


/* Each simple bonus may be classified using
 * this number of axes (1,2,3). This reflects 
 * the database structure, it may not be
 * arbitrarily increased.
 */ 
/*const CALC_AXIS_COUNT = 3;*/

/* This is now overridden in bodyLoaded using DBVERSION */
var CALC_AXIS_COUNT = 3;
var COMBOS_USE_CATS = false;

const CAT_NumBonusesPerCatMethod = 0;
const CAT_ResultPoints = 0;
const CAT_ResultMults = 1;
const CAT_NumNZCatsPerAxisMethod = 1;
const CAT_ModifyBonusScore = 1;
const CAT_ModifyAxisScore = 0;

/* Scoring method enum */
const SM_Manual = 0;
const SM_Simple = 1;
const SM_Compound = 2;

/* Show multipliers */
const SM_ShowMults = 1;
const SM_HideMults = 0;

/* Score explanation DOM id */
const SX_id	= "scorex";
const SX_StoreID = "scorexstore";

/* Tabbed display variables */
var tabLinks = new Array();
var contentDivs = new Array();

/* Time penalty specs */
const TPS_absolute		= 0;
const TPS_rallyDNF		= 1;
const TPS_entrantDNF	= 2;

/* Bonus display classes */
const class_showbonus	= ' showbonus ';
const class_rejected	= ' rejected ';
const class_checked		= ' checked ';
const class_unchecked	= ' unchecked ';

/* Don't create any elements with this id */
const NON_EXISTENT_BONUSID = 'zz'; 

// Nice flexible string formatting
if (!String.format) {
  String.format = function(format) {
    var args = Array.prototype.slice.call(arguments, 1);
    return format.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'
        ? args[number] 
        : match
      ;
    });
  };
}



// Alphabetic order below

function areYouSure(question)
{
	return window.confirm(question);
}

function askPoints(inp)
{
	if (inp.checked)
	{
		var par = inp.parentNode;
		var id = inp.getAttribute('id');
		var val = document.getElementById('ap'+id);
		if (!val)
		{
			var aps = document.getElementById('apspecials');
			if (aps)
			{
				val = document.createElement('input');
				val.setAttribute('type','hidden');
				val.setAttribute('name','ap'+id);
				val.setAttribute('id','ap'+id);
				aps.appendChild(val);
			}
		}
		var lbl = par.firstChild.innerHTML;
		var pts = inp.getAttribute('data-points');
	
		var npts = window.prompt(ASK_POINTS+' '+lbl,pts);
		if (npts != null)
			pts = parseInt(npts);
		
		inp.setAttribute('data-points',pts);
		if (val)
			val.setAttribute('value',pts);
		var tit = par.getAttribute('title');
		var p = tit.indexOf('[');
		if (p >= 0)
			par.setAttribute('title',tit.substr(0,p + 1) + ' ' + pts + ' ]');
	}
	calcScore(true);
}


function bodyLoaded()
{
	//var B = document.getElementsByName('BonusID[]');
	//for (var i = 0; i < B.length; i++)
	//{
	//B[i].parentElement.addEventListener('contextmenu',function(e){e.preventDefault()});
//		for (var j = 0; j < B[i].parentElement.childNodes.length; j++)
	//		B[i].parentElement.childNodes[j].addEventListener('contextmenu',function(e){e.preventDefault()});
	//}
	
	var dbv = document.getElementById('DBVERSION');
	if (dbv)
		switch(parseInt(dbv.value))
		{
			case 3:
				CALC_AXIS_COUNT = 9;
				COMBOS_USE_CATS = true;
		}
	
	showBreadcrumbs();
	
	var isScoresheetpage = document.getElementById("scoresheetpage");
	var hasTabs = document.getElementById('tabs');
	
	if (hasTabs)
		tabsSetupTabs();
	
	if (!isScoresheetpage)
		return;

	trapDirtyPage();
	calcScore(false);	
		
}

function calcComplexScore(res)
/*
 * This handles score calculations involving axis categories
 *
 */
{
	const debug = false;
	
	var totalBonusPoints = 0;
	var totalMultipliers = 0;
	var totalTickedBonuses = 0;
	
	var axisScores	= document.getElementsByName("AxisScores[]");	// Score totals by Axis (reported separately)
	var compoundCalcRules = document.getElementsByName("catcompound[]");	// Rules for calculating compound scores
	var showMults = document.getElementById("ShowMults").value == SM_ShowMults;
	
	var scoreReason = RPT_Tooltip + "\r\n";
	var catCounts	= [];


	function ccs_initialize()
	{
		if (debug) alert("ccs_initialize");
		
		// Establish an array of counts: hits within Cat within Axis and zeroise scores
		for (var i = 0; i < axisScores.length; i++)
		{
			catCounts[parseInt(axisScores[i].getAttribute('data-axis'))] = [];
			axisScores[i].setAttribute('data-points',0);
			axisScores[i].setAttribute('data-mults',0);
		}

		// Fake catCounts[0]
		catCounts[0] = [];
		for (var i = 0; i < axisScores.length; i++)
			catCounts[0][i] = 0;
	
		for (var i = 0; i < compoundCalcRules.length; i++)
			compoundCalcRules[i].setAttribute('data-triggered',RULE_NOT_TRIGGERED);

	} // ccs_initialize
	
	


	function ccs_processBonuses()
	
	// Ordinary bonuses
	
	{
		if (debug) alert("ccs_processBonuses");
		
		var bonuses	= document.getElementsByName("BonusID[]");		// Individual Bonus records
		// Now process individual bonuses
		for (var i = 0; i < bonuses.length; i++)
		{
			if (!bonuses[i].checked)
				continue;
			
			totalTickedBonuses++;
			var bonusPoints = parseInt(bonuses[i].getAttribute('data-points')); // Basic points value of the bonus
			
			// Keep track of number of bonuses per category within axis
			for (var j = 0; j < axisScores.length; j++)
			{
				var axis = parseInt(axisScores[j].getAttribute('data-axis'));
				var cat = parseInt(bonuses[i].getAttribute('data-cat' + axis));

				if (cat > 0)
				{
					if (typeof(catCounts[axis][cat]) == 'undefined')
						catCounts[axis][cat] = 1;
					else
						catCounts[axis][cat]++;
					if (typeof(catCounts[0][cat]) == 'undefined')
						catCounts[0][cat] = 1;
					else
						catCounts[0][cat]++;
				}
			}
			
			
			// Now calculate any compound mod to the basic points
			for (var j = 0; j < compoundCalcRules.length; j++)
			{
				if (compoundCalcRules[j].getAttribute('data-mb') == CAT_ModifyBonusScore)
				{
					var axis = parseInt(compoundCalcRules[j].getAttribute('data-axis'));  // 1, 2 or 3
					var matchcat = parseInt(compoundCalcRules[j].getAttribute('data-cat'));
					
					/* Find appropriate comparator score */
					var axisScore = 0;
					var cat = 0;
					if (axis == 0)
					{
						for (var axix = 0; axix < axisScores.length; axix++)
							axisScore += axisScores[axix];
						cat = matchcat;						/* Take cat, which must not be zero, from compound rule record */
					}
					else
					{
						axisScore = axisScores[axis];
						cat = parseInt(bonuses[i].getAttribute('data-cat'+axis));
					}
					var minBonusesPerCat = parseInt(compoundCalcRules[j].getAttribute('data-min'));
					var scoreFactor = parseInt(compoundCalcRules[j].getAttribute('data-power'));
					
					if (debug) alert("MBS: axis="+axis+" cat="+cat+" mbc="+minBonusesPerCat+" sf="+scoreFactor+" dm="+parseInt(compoundCalcRules[j].getAttribute('data-method')));
					var np = 1;
					switch (parseInt(compoundCalcRules[j].getAttribute('data-method')))
					{
						case CAT_NumBonusesPerCatMethod:
							if (axis < 1)
							{
								np = 0;
								for (var axix = 0; axix < axisScores.length; axix++)
									np += catCounts[axix][cat];
							}
							else
								np = catCounts[axis][cat];
							break;
							
						case CAT_NumNZCatsPerAxisMethod:
							bonusPoints = 1;	// Override the bonus value because calculation based on number of bonuses only
							var axix = axis;
							var axlim = axix + 1;
							if (axix == 0)
								axlim = axisScores.length;
							while (axix < axlim)
							{
								// Count the number of non-zero category entries in this axis
								for (var k = 0; k < catCounts[axix].length; k++ )
									if ( (typeof(catCounts[axix][k]) != 'undefined') && (catCounts[axix][k] > 0) )
										np++;
								axix++;
							}
							break;
								
						default:
							alert(String.format(CFGERR_MethodNIY,compoundCalcRules[j].getAttribute('data-method')));
					}
					if ( (matchcat == 0 || matchcat == cat) && (np >= minBonusesPerCat) )
					{
						if (compoundCalcRules[j].getAttribute('data-reqd') == '0')
							switch (parseInt(compoundCalcRules[j].getAttribute('data-pm')))
							{
								case CAT_ResultPoints:
						
									// 2017 - bps = bps * (Math.pow(np,(ccounts[dc][cat] - 1)))
									//alert('Updating data-points for axis=' + axis + '; cat=' + cat + '; bps =' + bonusPoints+"; sf="+scoreFactor+"; cc="+catCounts[axis][cat]);
									if (scoreFactor == 0)
										bonusPoints = bonusPoints * (np - 1);
									else
										bonusPoints = bonusPoints * (Math.pow(scoreFactor,(np - 1)));
									break;
								
								default:
									alert(String.format(CFGERR_NotBonuses,compoundCalcRules[j].getAttribute('data-pm')));
							}
					
					} // np > min
				} // ModifyBonusScore
			} // axisCalc

			// Any mods complete now so add the score
			totalBonusPoints += bonusPoints;
			sxappend(bonuses[i].getAttribute('id'),bonuses[i].parentNode.getAttribute("title").replace(/\[.+\]/,""),bonusPoints,0,totalBonusPoints);
			
		} // Bonus loop

		scoreReason += "\r\n" + RPT_Bonuses + ": " +totalTickedBonuses;

	} // ccs_processBonuses
	
	
	

	function ccs_catsPerAxisNZ()
	
	// This calculates the axis score using number of non-zero categories within axis
	// This also applies any compulsory axis participation rules
	
	{
		if (debug) alert("ccs_catsPerAxisNZ");
	
		// Now process catcompound entries that depend on number of non-zero entries per axis
		var nzEntriesPerAxis = [];
		
		for (var i = 1; i <= CALC_AXIS_COUNT; i++)
		{
			nzEntriesPerAxis[i] = countNZ(catCounts[i]);
			if (debug) alert("ccs3.2." + i + ' == ' + nzEntriesPerAxis[i]);
		}
	
	
		var lastAxis = -1; 
		for (var i = 0; i < compoundCalcRules.length; i++)
		{
			if (compoundCalcRules[i].getAttribute('data-method') == CAT_NumNZCatsPerAxisMethod && 
				compoundCalcRules[i].getAttribute('data-mb') == CAT_ModifyAxisScore) 
			{
				var axis = parseInt(compoundCalcRules[i].getAttribute('data-axis'));  
				var axisScore = document.getElementById('Axis'+axis+'Score');
				if (axis > lastAxis) // We want to process each axis only once at this stage
				{
					var nzCount = 0;
					if (axis > 0)
						nzCount = nzEntriesPerAxis[axis];
					else
						for (var axix = 1; axix <= CALC_AXIS_COUNT; axix++)
							nzCount += nzEntriesPerAxis[axix];
				
					if (nzCount >= parseInt(compoundCalcRules[i].getAttribute('data-min')))
					{	
						lastAxis = axis;

						compoundCalcRules[i].setAttribute('data-triggered',RULE_TRIGGERED);
						var scoreFactor = parseInt(compoundCalcRules[i].getAttribute('data-power'));

						var points = chooseNZ(scoreFactor,nzCount);
						
						if (compoundCalcRules[i].getAttribute('data-reqd') != '0')  // Compulsory rule, don't want to score, just maybe DNF
						{
							points = scoreFactor;
							if (points == 0) // There's no score as such
								continue;
							// May want to have drop through code below but not at present
						}
						else if (compoundCalcRules[i].getAttribute('data-pm') == CAT_ResultPoints)
						{
							var dmin = compoundCalcRules[i].getAttribute('data-min');
							if (points < 0) // Penalty
								var drel = ': n&lt;'+(++dmin); // cos not enough hits
							else
								var drel = ': n&gt;='+dmin;
							axisScore.setAttribute('data-points',parseInt(axisScore.getAttribute('data-points')) + points);
							totalBonusPoints += points;
							sxappend('R'+i,axisScore.value+drel,points,0,totalBonusPoints);
						}
						else // Multipliers
						{
							var dmin = compoundCalcRules[i].getAttribute('data-min');
							if (mults < 0) // Penalty
								var drel = ': n&lt;'+(++dmin); // cos not enough hits
							else
								var drel = ': n&gt;='+dmin;
							axisScore.setAttribute('data-mults',points);
							totalMultipliers += points;
							sxappend('R'+i,axisScore.value+drel,0,points,totalBonusPoints);
						}
					}
				}
			} // End NonZeroEntries
		}
	} //ccs_catsPerAxisNZ
	
	

	function ccs_entriesPerCat()
	
	// This calculates the axis scores using entries per category
	
	{
		
		if (debug) alert("ccs5");
	
		// Now process catcompound entries that depend on number of non-zero entries per category within axis
	
	
		for (var i = 0; i <= CALC_AXIS_COUNT; i++)
		{
			//alert('cc '+i+' == '+catCounts[i].length);
			for (var j = 0; j < catCounts[i].length; j++)
			{
				var lastAxis = -1;
				for (var k = 0; k < compoundCalcRules.length; k++)
				{
					var axis = parseInt(compoundCalcRules[k].getAttribute('data-axis'));  
					if (axis == i && (axis == 0 || typeof(catCounts[i][j]) != 'undefined'))
					{
						var catCount = 0;
						if (axis == 0)
						{
							for (var axix = 1; axix <= CALC_AXIS_COUNT; axix++)
								if (typeof(catCounts[axix][j]) != 'undefined')
									catCount += catCounts[axix][j];
						}
						else
							catCount = catCounts[i][j];
					
						var matchcat = parseInt(compoundCalcRules[k].getAttribute('data-cat'));
					
						if (catCount >= parseInt(compoundCalcRules[k].getAttribute('data-min')) && 
							compoundCalcRules[k].getAttribute('data-method') == CAT_NumBonusesPerCatMethod && 
							compoundCalcRules[k].getAttribute('data-mb') == CAT_ModifyAxisScore &&
							(matchcat == 0 || matchcat == j))
						{
							lastAxis = axis;
							compoundCalcRules[k].setAttribute('data-triggered',RULE_TRIGGERED);
							//if (axis == 1) alert("ccs5.1; i=" + i + "; j=" + j + "; k =" + k + "; axis=" + axis + "; catCount=" + catCount);
							var scoreFactor = parseInt(compoundCalcRules[k].getAttribute('data-power'));

							var points = chooseNZ(scoreFactor,catCount);
						
							var iplus = i;
							if (iplus < 1)
								iplus = 1;
						
							var catdesc = document.getElementById('cat'+iplus+'_'+j).parentNode.firstChild.innerHTML;
							var ndesc = ': n['+catdesc+']';
							if (compoundCalcRules[i].getAttribute('data-reqd') != '0')  // Compulsory rule, don't want to score, just maybe DNF
							{
								points = scoreFactor;
								if (points == 0) // There's no score as such
									continue;
								// May want to have drop through code below but not at present
							}
							
							else if (compoundCalcRules[k].getAttribute('data-pm') == CAT_ResultPoints)
							{
								var dmin = compoundCalcRules[i].getAttribute('data-min');
								if (points < 0) // Penalty
									var drel = ndesc+'lt;'+(++dmin); // cos not enough hits
								else
									var drel = ndesc+'&gt;='+dmin;
								//if (i==0) alert('hsn1 '+catdesc+'; '+catCount);
								axisScores[i].setAttribute('data-points',parseInt(axisScores[i].getAttribute('data-points')) + points);
								//if (i==0) alert('hsn2 '+catdesc+'; '+catCount);
								totalBonusPoints += points;
								sxappend('R'+k,axisScores[i].value+drel,points,0,totalBonusPoints);
								//if (i==0) alert('hsn3 '+catdesc+'; '+catCount);
							}
							else // Multipliers
							{
								var dmin = compoundCalcRules[i].getAttribute('data-min');
								if (points < 0) // Penalty
									var drel = ndesc+'lt;'+(++dmin); // cos not enough hits
								else
									var drel = ndesc+'&gt;='+dmin;
								axisScores[i].setAttribute('data-mults',points);
								totalMultipliers += points;
								sxappend('R'+k,axisScores[i].value+drel,0,points,totalBonusPoints);
							}
							break;		// Only process one calc
						}
					}
				
				}
			}
		}
	} //ccs_entriesPerCat
	
	

	function ccs_processSpecials()
	{
		// Specials
	
		var sgObj = document.getElementById("SGroupsUsed");
		var sbonuses = [];
		if (sgObj != null)
		{
			var sg = sgObj.value.split(",");
			for (var j = 0; j < sg.length; j++)
			{
				sbonuses = document.getElementsByName("SpecialID_"+sg[j]+"[]");
				for (var i = 0, bps = 0, mults = 0; i < sbonuses.length; i++ )
				{
					if (!sbonuses[i].checked)
						continue;
				
					bps = parseInt(sbonuses[i].getAttribute('data-points'));
					totalBonusPoints += bps;
					mults = parseInt(sbonuses[i].getAttribute('data-mult'));
					totalMultipliers += mults;
					sxappend(sbonuses[i].getAttribute('id'),sbonuses[i].parentNode.firstChild.innerHTML,sbonuses[i].getAttribute('data-points'),sbonuses[i].getAttribute('data-mult'),totalBonusPoints);
				}
			}
		}
	

	
		sbonuses = document.getElementsByName("SpecialID[]");
		for (var i = 0, bps = 0, mults = 0; i < sbonuses.length; i++ )
		{
			if (!sbonuses[i].checked)
				continue;
			bps = parseInt(sbonuses[i].getAttribute('data-points'));
			totalBonusPoints += bps;
			mults = parseInt(sbonuses[i].getAttribute('data-mult'));
			totalMultipliers += mults;
			sxappend(sbonuses[i].getAttribute('id'),sbonuses[i].parentNode.firstChild.innerHTML,sbonuses[i].getAttribute('data-points'),sbonuses[i].getAttribute('data-mult'),totalBonusPoints);			
		}

	

	} // ccs_processSpecials
	

	
	function ccs_processCombos()
	{
		
		if (debug) alert("ccs8");

		// Combos
		var bonuses = document.getElementsByName("ComboID[]");
		for (var i = 0, bps = 0, mults = 0; i < bonuses.length; i++ )
		{
			if (!bonuses[i].checked)
				continue;
		
			//alert(bonuses[i].getAttribute('id')+' checked = '+bonuses[i].getAttribute('data-points'));
			if (parseInt(bonuses[i].getAttribute('data-method')) == CMB_ScoreMults)
			{
				mults = parseInt(bonuses[i].getAttribute('data-points'));
				totalMultipliers += mults;
				sxappend(bonuses[i].getAttribute('id'),bonuses[i].parentNode.firstChild.innerHTML,0,mults,totalMultipliers);			
			}
			else
			{
				bps = parseInt(bonuses[i].getAttribute('data-points'));
				totalBonusPoints += bps;
				sxappend(bonuses[i].getAttribute('id'),bonuses[i].parentNode.firstChild.innerHTML,bps,0,totalBonusPoints);			
			}
			
			if (!COMBOS_USE_CATS)
				continue;
			
			// Keep track of number of bonuses per category within axis
			for (var j = 0; j < axisScores.length; j++)
			{
				var axis = parseInt(axisScores[j].getAttribute('data-axis'));
				var cat = parseInt(bonuses[i].getAttribute('data-cat' + axis));

				if (cat > 0)
				{
					if (typeof(catCounts[axis][cat]) == 'undefined')
						catCounts[axis][cat] = 1;
					else
						catCounts[axis][cat]++;
					if (typeof(catCounts[0][cat]) == 'undefined')
						catCounts[0][cat] = 1;
					else
						catCounts[0][cat]++;
				}
			}
			
		
		}
	} // ccs_processCombos
	

	function ccs_mileagePenalty()
	{
		if (debug) alert("ccs9");

		var MPenalty = calcMileagePenalty();
	
		totalBonusPoints += MPenalty[0];
		totalMultipliers += MPenalty[1];
	
		if (showMults)
		{
			scoreReason += "\r\n" + RPT_MPenalty + ': P-' + MPenalty[0] + ' (M-' + MPenalty[1] + ')';
		if (MPenalty[0] != 0 || MPenalty[1] != 0)
				sxappend('',RPT_MPenalty,MPenalty[0],MPenalty[1],totalBonusPoints);
		}
		else
			if (MPenalty[0] != 0)
			{
				scoreReason += "\r\n" + RPT_MPenalty + ': ' + MPenalty[0];
				sxappend('',RPT_MPenalty,MPenalty[0],0,totalBonusPoints);
			}
	} // ccs_mileagePenalty
	

	function ccs_timePenalty()
	{
		var TPenalty = calcTimePenalty();
	
		totalBonusPoints += TPenalty[0];
		totalMultipliers += TPenalty[1];

		if (showMults)
		{
			scoreReason += "\r\n" + RPT_TPenalty + ': P-' + TPenalty[0] + ' (M-' + TPenalty[1] + ')';
			if (TPenalty[0] !=0 || TPenalty[1] !=0)
				sxappend('',RPT_TPenalty,TPenalty[0],TPenalty[1],totalBonusPoints);
		}
		else
			if (TPenalty[0] != 0)
			{
				scoreReason += "\r\n" + RPT_TPenalty + ': ' + TPenalty[0];
				sxappend('',RPT_TPenalty,TPenalty[0],0,totalBonusPoints);
			}
	} // ccs_timePenalty
	




	

	// 						c a l c C o m p l e x S c o r e   		M A I N L I N E
	
	ccs_initialize();
	ccs_processBonuses();
	ccs_processCombos();
	ccs_processSpecials();
	ccs_catsPerAxisNZ();
	ccs_entriesPerCat();
	ccs_mileagePenalty();
	ccs_timePenalty();
	
	
	res.reason = scoreReason;
	if (totalMultipliers > 0)
		totalBonusPoints = totalBonusPoints * totalMultipliers;
	
	// Now display the results
	document.getElementById('TotalPoints').value = formatNumberScore(totalBonusPoints);
	try {document.getElementById('TotalMults').value = totalMultipliers;} catch(err) {}
	zapScoreDetails();
	
	for (var i = 1; i <= CALC_AXIS_COUNT; i++)
		for (var j = 0; j < catCounts[i].length; j++)
			showCat(i,catCounts[i][j],j);

	return totalBonusPoints;

}

function calcMileagePenalty()
{
	var CM = parseInt(document.getElementById('CorrectedMiles').value);
	var PMM = parseInt(document.getElementById('PenaltyMaxMiles').value);
	var PMMethod = parseInt(document.getElementById('MaxMilesMethod').value);
	var PMPoints = parseInt(document.getElementById('MaxMilesPoints').value);
	var PenaltyMiles = CM - PMM;
	
	if (PenaltyMiles <= 0) // No penalty
		return [0,0]; 
		
	//alert('PM='+PenaltyMiles+'; Method='+PMMethod+'; Points='+PMPoints);
	switch (PMMethod)
	{
		case MMM_PointsPerMile:
			return [0 - PMPoints * PenaltyMiles,0];
		case MMM_Multipliers:
			return [0,PMPoints];
		default:
			return [0 - PMPoints,0];
	}
		
}


function calcScore(enableSave)
/*
 *															c a l c S c o r e
 *
 * This is called on first display and again whenever field values are changed.
 */
{
	var debug = false;
	var sm = parseInt(document.getElementById('ScoringMethod').value);
	var res = { reason: "" };
	var TPS = 0;
	
	if (debug) alert("calcScore[0]");
	
	hidePopup();
	
	if (sm != SM_Manual)
	{
		setFinishTimeDNF();
		if (debug) alert("calcScore[0][1]");
		clearUnrejectedClaims();
		sxstart();
		reportRejectedClaims();
		tickCombos();
		if (debug) alert("calcScore[0][2]");
		repaintBonuses();

		if (debug) alert("calcScore[1]");
		if (sm == SM_Compound)
			TPS = calcComplexScore(res);
		else
			TPS = calcSimpleScore(res);
		if (debug) alert("calcScore[2]");
		sxappend('',RPT_Total,'',0,TPS);
		document.getElementById('TotalPoints').value = formatNumberScore(TPS);
		document.getElementById('TotalPoints').setAttribute('title',res.reason);
	}
	if (debug) alert("calcScore[3]");
	
	setFinisherStatus();
	if (enableSave)
		enableSaveButton();
	
}


function calcSimpleScore(res)
/*
 *															c a l c S i m p l e S c o r e
 *
 * This calculates TotalPoints using the classic points method of scoring 
 *
 */
{
	var debug = false;
	var reason = RPT_Tooltip + "\r\n";  // Contains tooltip explanation of total score
	var TS = 0;
	var bps = 0;
	var ticks = 0;

	var bp = document.getElementsByName("BonusID[]");
	for (var i = 0, bps = 0; i < bp.length; i++ )
		if (bp[i].checked)
		{
			bps += parseInt(bp[i].getAttribute('data-points'));
			ticks++;
			sxappend(bp[i].getAttribute('id'),bp[i].parentNode.getAttribute("title").replace(/\[.+\]/,""),bp[i].getAttribute('data-points'),0,TS + bps);
		}
	reason += "\r\n" + RPT_Bonuses + ': ' + ticks;
		
	TS += bps;

	var sgObj = document.getElementById("SGroupsUsed");
	if (sgObj != null)
	{
		var sg = sgObj.value.split(",");
		for (var j = 0; j < sg.length; j++)
		{
			bp = document.getElementsByName("SpecialID_"+sg[j]+"[]");
			for (var i = 0, bps = 0; i < bp.length; i++ )
				if (bp[i].checked)
				{
					bps = parseInt(bp[i].getAttribute('data-points'));
					TS += bps;
					sxappend(bp[i].getAttribute('id'),bp[i].parentNode.firstChild.innerHTML,bp[i].getAttribute('data-points'),0,TS);
				}
		
		}
	}
	
	bp = document.getElementsByName("SpecialID[]");
	for (var i = 0, bps = 0; i < bp.length; i++ )
		if (bp[i].checked)
		{
			bps += parseInt(bp[i].getAttribute('data-points'));
			sxappend(bp[i].getAttribute('id'),bp[i].parentNode.firstChild.innerHTML,bp[i].getAttribute('data-points'),0,TS + bps);

		}
		
	TS += bps;
		

	bp = document.getElementsByName("ComboID[]");
	for (var i = 0, bps = 0; i < bp.length; i++ )
		if (bp[i].checked)
		{
			bps += parseInt(bp[i].getAttribute('data-points'));
			sxappend(bp[i].getAttribute('id'),bp[i].parentNode.firstChild.innerHTML,bp[i].getAttribute('data-points'),0,TS + bps);

		}
		
	TS += bps;

	if (debug) alert('css[M]');
	var MPenalty = calcMileagePenalty();
	
	TS -= MPenalty[0];
	if (MPenalty[0] != 0)
		reason += "\r\n" + RPT_MPenalty + ': ' + MPenalty[0];
	
	if (MPenalty[0] != 0)
		sxappend('',RPT_MPenalty,MPenalty[0],0,TS);

	var TPenalty = calcTimePenalty();
	if (debug) alert('TP[0]=='+TPenalty[0]+'; TP[1]=='+TPenalty[1]);
	TS -= TPenalty[0];
	if (TPenalty[0] != 0)
		reason += "\r\n" + RPT_TPenalty + ': ' + TPenalty[0];
	if (TPenalty[0] != 0)
		sxappend('',RPT_TPenalty,TPenalty[0],0,TS);

	res.reason = reason;
	
	return TS;
	
}




function calcTimePenalty()
{
	const OneMinute = 1000 * 60;
	var TP = document.getElementsByName('TimePenalty[]');
	var FT = new Date(document.getElementById('FinishDate').value + 'T' + document.getElementById('FinishTime').value+'Z');
	var  FTDate = new Date(FT);
	//alert("TP: "+FTDate);
	for ( var i = 0 ; i < TP.length ; i++ )
	{
		var ds, de, dnf;
		switch(parseInt(TP[i].getAttribute('data-spec')))
		{
			case TPS_rallyDNF:
				dnf = new Date(document.getElementById('RallyTimeDNF').value+'Z');
				ds = dnf - parseInt(TP[i].getAttribute('data-start')) * 60000;
				de = dnf - parseInt(TP[i].getAttribute('data-end')) * 60000;
				break;
			case TPS_entrantDNF:
				dnf = new Date(document.getElementById('FinishTimeDNF').value+'Z');
				ds = dnf - parseInt(TP[i].getAttribute('data-start')) * 60000;
				de = dnf - parseInt(TP[i].getAttribute('data-end')) * 60000;
				break;
			default:
				ds = new Date(TP[i].getAttribute('data-start')+'Z');
				de = new Date(TP[i].getAttribute('data-end')+'Z');
		}
		
		//if (FT >= TP[i].getAttribute('data-start') && FT <= TP[i].getAttribute('data-end'))
		if (FT >= ds && FT <= de)
		{
			var PF = parseInt(TP[i].getAttribute('data-factor'));
			var PM = parseInt(TP[i].getAttribute('data-method'));
			var PStartDate = ds; //new Date(TP[i].getAttribute('data-start'));
			var Mins = 1 + (Math.abs(FTDate - PStartDate) / OneMinute);
			//alert(PStartDate + ' == ' + FTDate + ' == ' + PM + '=' + TPM_PointsPerMin + ' == ' + Mins);
			switch(PM)
			{
				case TPM_MultPerMin:
					return [0,0 - PF * Mins];
				case TPM_PointsPerMin:
					return [0 - PF * Mins,0];
				case TPM_FixedMult:
					return [0,0 - PF];
				default:
					return [0 - PF,0];
			}
		}
	}
	return [0,0];

}

function ccShowSelectAxisCats(axis,sel)
{
	var lst = 0;
	
	try {
		lst = document.getElementById('axis'+axis+'cats');
	} catch(err) {
		return;
	}
	
	var cats = lst.value.split(',');
	var optval = sel.options[sel.selectedIndex].value;
	while (sel.options.length > 0)
		sel.options.remove(0);
	for (var i = 0; i < cats.length; i++)
	{
		var f = cats[i].split('=');
		var opt = document.createElement("option");
		opt.text = f[1]+' ('+f[0]+')';
		opt.value = f[0];
		if (f[0]==optval)
			opt.selected = true;
		sel.options.add(opt);
	}
	if (sel.selectedIndex < 0 && sel.opt.length > 0)
		sel.selectedIndex = 0;

}


function chooseNZ(i,j)
{if (i==0)
		return j;
	else
		return i;
}

function convertUTCDateToLocalDate(date)
{
    var newDate = new Date(date);
    newDate.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return newDate;
}

function countNZ(cnts)
{
	var res = 0;
	for (var i = 0; i < cnts.length; i++)
		if (cnts[i] > 0)
			res++;
	return res;
}


function enableSaveButton()
{
	var cmd;
	
	cmd = document.getElementById('savescorebutton');
	if (cmd == null)
		cmd = document.getElementById('savedata'); /* Forms other than scoresheet */
	if (cmd == null)
		return;
	cmd.disabled = false;
	try {
		var aval = cmd.getAttribute('data-altvalue');
		if (aval != '' && aval != null)
			cmd.value = aval;
	} catch(err) {
	}
	
}

function findEntrant()
{
	var x;
	
	x = window.prompt(LOOKUP_ENTRANT,'');
	if (x == null)
		return true;
	window.location='entrants.php?c=entrants&mode=find&x='+x;
	return false;
}

function formatNumberScore(n,prettyPrint)
/*
 * If n is a number and not equal zero, return its value
 * formatted as the machine's locale
 * otherwise return n, which may be blank or not a number
 *
 */
{
	if (prettyPrint===undefined)
	{
		return n;
	}
	var NF = new Intl.NumberFormat(MY_LOCALE);
	
	if (parseInt(n) > 0)
		return NF.format(n);
	else
		return n;

}
	
function getFirstChildWithTagName( element, tagName ) {  // Tabbing
     for ( var i = 0; i < element.childNodes.length; i++ ) {
        if ( element.childNodes[i].nodeName == tagName ) return element.childNodes[i];
     }
}

function hidePopup()
{
	var menu = document.getElementById('rcmenu');
	menu.style.display='none';
}

/* ODO readings form filler */
function odoAdjust(useTrip)
{
	var odox = document.getElementById('tab_odo');
	if (!odox) return;
	
	// Any non-zero value here means that we're handling kilometres rather than miles
	var basickms = parseInt(document.getElementById('BasicDistanceUnits').value) != 0;
	
	var odokms = document.getElementById('OdoKmsK').checked;
	var odocheckmiles = parseFloat(document.getElementById('OdoCheckMiles').value);
	var correctionfactor = parseFloat(document.getElementById('OdoScaleFactor').value);
	if (correctionfactor < 0.5)	//SanityCheck
		correctionfactor = 1.0;
	if (odocheckmiles > 0.1)
	{
		var odocheckstart = parseFloat(document.getElementById('OdoCheckStart').value);
		var odocheckfinish = parseFloat(document.getElementById('OdoCheckFinish').value);
		var odochecktrip = parseFloat(document.getElementById('OdoCheckTrip').value);
		if (!useTrip)
			odochecktrip = odocheckfinish - odocheckstart;
		
		if (document.getElementById('OdoCheckStart').value != '' || odochecktrip > 0)
		{
			var checkdistance = odochecktrip;
			if (odokms && !basickms) // Want miles, have kms
				checkdistance = checkdistance / KmsPerMile;
			else if (!odokms && basickms) // Want kms, have miles
				checkdistance = checkdistance * KmsPerMile;
			correctionfactor = odocheckmiles / checkdistance ;
			if (correctionfactor < 0.5)	//SanityCheck
				correctionfactor = 1.0;
			document.getElementById('OdoScaleFactor').value = correctionfactor.toString();
		}
	}		
	if (useTrip)
		document.getElementById('OdoCheckFinish').value = odocheckstart + odochecktrip;
	else if (document.getElementById('OdoCheckTrip').value == '')
		document.getElementById('OdoCheckTrip').value = odocheckfinish - odocheckstart;
	
	var odorallystart = parseFloat(document.getElementById('OdoRallyStart').value);
	var odorallyfinish = parseFloat(document.getElementById('OdoRallyFinish').value);
	if (document.getElementById('OdoRallyStart').value != '' && odorallyfinish > odorallystart)
	{
		var rallydistance = (odorallyfinish - odorallystart) * correctionfactor;
		if (odokms && !basickms)
			rallydistance = rallydistance / KmsPerMile;
		else if (!odokms && basickms)
			rallydistance = rallydistance * KmsPerMile;
		
		document.getElementById('CorrectedMiles').value = rallydistance.toFixed(0);
	}
}

function calcMiles()
{
	var basickms = parseInt(document.getElementById('BasicDistanceUnits').value) != 0;
	var odokms = document.getElementById('OdoKmsK').checked;
	var correctionfactor = parseFloat(document.getElementById('OdoScaleFactor').value);
	if (correctionfactor < 0.5)	//SanityCheck
		correctionfactor = 1.0;
	
	var odorallystart = parseFloat(document.getElementById('OdoRallyStart').value);
	var odorallyfinish = parseFloat(document.getElementById('OdoRallyFinish').value);
	if (document.getElementById('OdoRallyStart').value != '' && odorallyfinish > odorallystart)
	{
		var rallydistance = (odorallyfinish - odorallystart) * correctionfactor;
		if (odokms && !basickms)
			rallydistance = rallydistance / KmsPerMile;
		else if (!odokms && basickms)
			rallydistance = rallydistance * KmsPerMile;
		
		document.getElementById('CorrectedMiles').value = rallydistance.toFixed(0);
	}
	
}

function clearUnrejectedClaims()
/*
 * I clear the rejected status on bonuses that are
 * now checked as ok.
 *
 */
 {
	var RC = document.getElementById('RejectedClaims');
	var rca = RC.value.split(',');
	for (var i = 0; i < rca.length; i++ )
	{
		var cr = rca[i].split('=');
		var B = document.getElementById(cr[0]);
		if (B == null)
			continue;
		if (B.checked)
			setRejectedClaim(cr[0],0);
	}
 }
 


function reflectBonusCheckedState(B)
{
	//if (B.id == 'CLinked1')
		//alert('Bonus ' + B.id + ' has checked = ' + B.checked + ' and Reject = ' + B.getAttribute('data-rejected'));
	var S = B.parentElement;
	if (B.checked)
		S.className = class_showbonus + class_checked;
	else if (B.getAttribute('data-rejected') > 0)
		S.className = class_showbonus + class_rejected;
	else
		S.className = class_showbonus + class_unchecked;
}


function repaintBonuses()
{
	walkBonusArrays(function(id) {
		var B = document.getElementById(id);
		reflectBonusCheckedState(B);
		B.parentElement.addEventListener('contextmenu',function(e){e.preventDefault()});
		for (var j = 0; j < B.parentElement.childNodes.length; j++)
			B.parentElement.childNodes[j].addEventListener('contextmenu',function(e){e.preventDefault()});
	});
		
	
}

function reportRejectedClaim(bonusid,reason)
{
//	alert('rRC-'+bonusid);
	var B = document.getElementById(bonusid);
	if (B == null)
		return;
	B.setAttribute('data-rejected',reason);
	setRejectedTooltip(B.parentNode,reason);
	var BP = B.parentNode;
	var R = document.getElementsByName('RejectReason');
	for (var i = 0; i < R.length; i++)
		if (R[i].getAttribute('data-code') == reason)
		{
			//alert("Reporting " + bonusid + " for " + R[i].value);
			if (B.name != 'BonusID[]')
				sxappend(B.getAttribute('id'),B.parentNode.firstChild.innerHTML.replace(/\[.+\]/,""),'X','','');
			else
			{
				var xtit = B.parentNode.getAttribute("title").replace(/\[.+\]/,"");
				var p = xtit.indexOf('\r');
				if (p >= 0)
					xtit = xtit.substr(0,p);
				// Only append the first line of the tooltip, not the whole thing
				sxappend(B.getAttribute('id'),xtit,'X','','');
			}
			sxappend('',CLAIM_REJECTED + ' - ' + R[i].value,'','','');
		}
	if (reason == 0)
		B.parentElement.className = class_showbonus + class_unchecked;
	else
		B.parentElement.className = class_showbonus + class_rejected;
	//alert("Reporting reason " + reason + " for bonus " + bonusid);
}

function reportRejectedClaims()
/*
 * I'm called on page load to mark individual bonuses as being rejected and to
 * report those rejections to the score explanation.
 *
 */
{
	var RC = document.getElementById('RejectedClaims');
//	alert('rca ['+RC.value+']');
	var rca = RC.value.split(',');
//	alert(rca.length);
	for (var i = 0; i < rca.length; i++ )
	{
		var cr = rca[i].split('=');
		reportRejectedClaim(cr[0],cr[1]);
	}
}


function setFinisherStatus()
/*
 *
 *							s e t F i n i s h e r S t a t u s
 *
 * This determines status depending on score, mileage and timings.
 *
 */
{
	
	function SFS(status,x)
	{
		var es = document.getElementById('EntrantStatus');
		es.value = status;
		es.setAttribute('title',x);
		if (x != '')
			sxappend(' '+es.options[status].text,x,'','',0);
		var sxsfs = document.getElementById('sxsfs');
		if (sxsfs)
			sxsfs.innerHTML = es.options[es.selectedIndex].text;
	}
	var CS = parseInt(document.getElementById('EntrantStatus').value);
	//if (CS != EntrantOK && CS != EntrantFinisher)
		//return;
	var TS = parseInt(document.getElementById('TotalPoints').value);
	var MP = parseInt(document.getElementById('MinPoints').value);
	if (MP > 0 && TS < MP)
		return SFS(EntrantDNF,DNF_TOOFEWPOINTS);
	
	var CM = parseInt(document.getElementById('CorrectedMiles').value);
	var MM = parseInt(document.getElementById('MinMiles').value);
	if (MM > 0 && CM < MM)
		return SFS(EntrantDNF,DNF_TOOFEWMILES);
	var PM = parseInt(document.getElementById('PenaltyMilesDNF').value);
	if (PM > 0 && CM > PM)
		return SFS(EntrantDNF,DNF_TOOMANYMILES);

	var DT = document.getElementById('FinishTimeDNF').value;
	var FT = document.getElementById('FinishDate').value + 'T' + document.getElementById('FinishTime').value;
	if (FT != 'T' && FT > DT)
		return SFS(EntrantDNF,DNF_FINISHEDTOOLATE+' > '+DT.replace('T',' '));
	
	var BL = document.getElementsByName('BonusID[]');
	for (var i = 0 ; i < BL.length; i++ )
		if (BL[i].getAttribute('data-reqd')==COMPULSORYBONUS && !BL[i].checked)
			return SFS(EntrantDNF,DNF_MISSEDCOMPULSORY);
	
	BL = document.getElementsByName('SpecialID[]');
	for (var i = 0 ; i < BL.length; i++ )
		if (BL[i].getAttribute('data-reqd')==COMPULSORYBONUS && !BL[i].checked)
			return SFS(EntrantDNF,DNF_MISSEDCOMPULSORY);
	
	var sgObj = document.getElementById('SGroupsUsed');
	if (sgObj != null)
	{
		var sg = sgObj.value.split(',');
		for (var i = 0; i < sg.length; i++)
			BL = document.getElementsByName('SpecialID_' + sg[i] + '[]');
			for (var i = 0 ; i < BL.length; i++ )
				if (BL[i].getAttribute('data-reqd')==COMPULSORYBONUS && !BL[i].checked)
					return SFS(EntrantDNF,DNF_MISSEDCOMPULSORY);
	}
	
	BL = document.getElementsByName('ComboID[]');
	for (var i = 0 ; i < BL.length; i++ )
		if (BL[i].getAttribute('data-reqd')==COMPULSORYBONUS && !BL[i].checked)
			return SFS(EntrantDNF,DNF_MISSEDCOMPULSORY);
	
	BL = document.getElementsByName('catcompound[]');
	for (var i = 0 ; i < BL.length; i++ )
		if (BL[i].getAttribute('data-reqd')==COMPULSORYBONUS && BL[i].getAttribute('data-triggered')!=RULE_TRIGGERED)
			return SFS(EntrantDNF,DNF_COMPOUNDRULE);
		else if (BL[i].getAttribute('data-reqd')==MUSTNOTMATCH && BL[i].getAttribute('data-triggered')==RULE_TRIGGERED)
			return SFS(EntrantDNF,DNF_COMPOUNDRULE);
	
	SFS(EntrantFinisher,'');
	
}

function setFinishTimeDNF()
{
	var CH = parseInt(document.getElementById('CertificateHours').value);
	var ST = document.getElementById('RallyTimeStart').value;
	var st = document.getElementById('StartDate').value + 'T' + document.getElementById('StartTime').value;
	var mst = st < ST ? ST : st;
	var dt = new Date(mst+'Z');
	dt.setHours(dt.getHours()+CH);
	//alert('ST='+st);
	//alert('DNF='+dt.toISOString());
	var FT = document.getElementById('RallyTimeDNF').value;
	var xt = dt.toISOString();
	xt = xt.substring(0,16);
	if (FT < xt)
		xt = FT;
	document.getElementById('FinishTimeDNF').value = xt;
	//alert("set="+xt);

}

function setRejectedClaim(bonusid,reason)
{
	// reason == 0 - unset, claim not rejected
	//alert(' Flagging ' + bonusid + ' as ' + reason);
	var B = document.getElementById(bonusid);
	if (B == null)
		return;
	B.setAttribute('data-rejected',reason);
	var RC = document.getElementById('RejectedClaims');
//	alert('src:' + bonusid + '=' + reason + '; rc=' + RC.value);
	var rca = RC.value.split(',');
	var done = false;
	for (var i = 0; i < rca.length; i++ )
	{
		var cr = rca[i].split('=');
		if (cr[0] == bonusid)
		{
			cr[1] = reason;
			if (reason == 0)
				cr[0] = NON_EXISTENT_BONUSID;
			done = true;
			rca[i] = cr.join('=');	
			break;
		}
	}
	if (!done)
		rca.push(bonusid+'='+reason);
	RC.value = rca.toString();
	if (reason == 0)
		B.parentElement.className = class_showbonus + class_unchecked;
	else
		B.parentElement.className = class_showbonus + class_rejected;
	
	setRejectedTooltip(B.parentNode,reason);
	//alert('Setting RC value == ' + RC.value);
}

function setRejectedTooltip(BP,reason)
{
	var tit = BP.getAttribute('title').split('\r');
	if (reason == 0)
		BP.setAttribute('title',tit[0]);
	else {
		var rcm = document.getElementById('rcmenu');
		BP.setAttribute('title',tit[0]+'\r'+rcm.firstChild.childNodes[reason].innerText);
	}

}

function setSplitNow(id_prefix)
{
	var dt = convertUTCDateToLocalDate(new Date(Date.now()));
	var dtDate = document.getElementById(id_prefix+'Date');
	if (!dtDate) return;
	var dtTime = document.getElementById(id_prefix+'Time');
	if (!dtTime) return;
	var x = dt.toJSON();
	var xd = x.slice(0,10);
	var xt = x.slice(11,16);
	//alert('ssn:'+id_prefix+' x='+x+' xd='+xd+' xt='+xt);
	dtDate.value = xd;
	dtTime.value = xt;
	enableSaveButton();
}

function showBreadcrumbs()
{
	var obj = document.getElementById('breadcrumbs');
	if (!obj) return;
	var nav = document.getElementById('navbar_breadcrumbs');
	if (!nav) return;
	nav.innerHTML = '';
	var bc = obj.value.split(';');
	for (var i = 0; i + 1 < bc.length; i++)
	{
		if (bc[i] != '')
		{
			var e = document.createElement('span');
			e.className = 'breadcrumb';
			e.innerHTML = bc[i];
			nav.appendChild(e);
		}
	}
}

function showCat(cat,N,ent)
{
	if (typeof(N) == 'undefined')
		var X = '';
	else
		var X = N;
	try	{ document.getElementById('cat'+cat+'_'+ent).innerText = X; } catch(err) { }		
}


/* Called from Entrant picklist when an Entrant number is entered */
function showPickedName()
{
	var eid = parseInt(document.getElementById('EntrantID').value);
	var eids = document.getElementsByClassName("EntrantID");
	var enames = document.getElementsByClassName("RiderName");
	document.getElementById("NameFilter").value = '';
	for (var i = 0 ; i < eids.length; i++)
		if (eid == eids[i].innerHTML)
		{
			document.getElementById("NameFilter").value = enames[i].innerHTML;
			break;
		}
	enableSaveButton();
}



/*
 * This is called in response to a contextmenu event, right-click or long press
 * it identifies the clicked bonus then shows a popup menu containing claim
 * reject reasons or 0 to clear the rejection.
 *
 */
function showPopup(obj)
{
	var menu = document.getElementById('rcmenu');
	if (menu == null)
		return true;
	var el = obj;
	//alert(el.tagName + ' == ' + (el.tagName != 'SPAN') + ' id=' + el.id);
	if (el.tagName != 'SPAN')
		el = el.parentElement;
	var ee = el.getBoundingClientRect();
	var B = el.getElementsByTagName('input')[0];
	menu.setAttribute('data-bonusid',B.id);
	menu.onclick = function(e) { 
		menu.style.display='none'; // hide the menu
		var reason = e.target; 
		var code = reason.innerText.split('=')[0];
		var bid = menu.getAttribute('data-bonusid');
		//alert('bid is ' + bid);
		if (code > 0)
			document.getElementById(bid).checked = false; 
//		document.getElementById(bid).disabled = code > 0;
		setRejectedClaim(bid,code);
		calcScore(true);
	}
    menu.style.left = ee.left + window.scrollX + 'px';
    menu.style.top = ee.top + + window.scrollY + 'px';
    menu.style.display = 'block';

	return false;
}



/* Call when score submit button is clicked */
function submitScore()
{
	/* Enable any combos so they'll be saved */
	var cmbs = document.getElementsByName('ComboID[]');
	for (var i = 0; i < cmbs.length; i++ )
	{
		cmbs[i].disabled = false;
	}
	
	/* Save the score explanation as part of the form
	 * so that it can be saved to the entrant record
	 * for later [bulk] printing.
	 */
	var sx = document.getElementById(SX_id);
	var sxs = document.getElementById(SX_StoreID);
	if (sx && sxs)
		sxs.value = sx.innerHTML;
	
	return true;
}


/* Score eXplanations */
function sxappend(id,desc,bp,bm,tp)
{
	var showMults = document.getElementById("ShowMults").value == SM_ShowMults;

	var sx = document.getElementById(SX_id);
	if (!sx) return;
	
	var sxb = getFirstChildWithTagName(sx,'TABLE');
	if (!sxb) return;
	
	//return;
	
	var estat = document.getElementById('EntrantStatus');
	
	//document.getElementById('sxsfs').innerHTML = estat.options[estat.selectedIndex].text;
	
	
	var row = sxb.insertRow(-1);
	var td_id = row.insertCell(-1);
	var id1 = id.substr(0,1);
	if (id1 != ' ' && id1 != '') {
		td_id.innerHTML = id1 + '-' + id.substr(1);
	} else {
		td_id.innerHTML = id;
	}
	td_id.className = 'id';
	var td_desc = row.insertCell(-1);
	td_desc.innerHTML = desc;
	td_desc.className = 'desc';
	var td_bp = row.insertCell(-1);
	td_bp.innerHTML = formatNumberScore(bp,true);
	td_bp.className = 'bp';
	if (showMults)
	{
		var td_bm = row.insertCell(-1);
		td_bm.innerHTML = bm;
		td_bm.className = 'bm';
	}
	var td_tp = row.insertCell(-1);
	td_tp.innerHTML = formatNumberScore(tp,true);
	td_tp.className = 'tp';
}
function sxhide()
{
	var sx = document.getElementById(SX_id);	
	sx.className = 'hidescorex scorex';
	sx.setAttribute('data-show','0');
}
function sxprint()
{
	var ent = document.getElementById('EntrantID').value;
    var mywindow = window.open('entrants.php?c=scorex&entrant='+ent, 'PRINT', 'height=400,width=600');
	
	//var hdrtitle = document.getElementById('hdrRallyTitle').innerHTML;
	
    //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
	//mywindow.document.write('<link rel="stylesheet" type="text/css" href="score.css">');

    //mywindow.document.write('</head><body>');
    //mywindow.document.write('<h1>' + hdrtitle  + '</h1>');
	//mywindow.document.write('<div class="scorex">');
    //mywindow.document.write(document.getElementById(SX_id).innerHTML);
    //mywindow.document.write('</div></body></html>');

    //mywindow.document.close(); // necessary for IE >= 10
    //mywindow.focus(); // necessary for IE >= 10*/

    //mywindow.print();
    //mywindow.close();

    return true;	
}
function sxshow()
{
	var sx = document.getElementById(SX_id);
	if (sx.innerHTML=='')
		calcScore(false);
	sx.className = 'showscorex scorex';
	sx.setAttribute('data-show','1');
}	
function sxstart()
{
//	alert('start');
	var showMults = document.getElementById("ShowMults").value == SM_ShowMults;
	
	var sx = document.getElementById(SX_id);
	if (!sx) return;
	
	var html = '<table><caption>'+document.getElementById("RiderID").innerHTML+' [&nbsp;<span id="sxsfs"></span>&nbsp;]</caption><thead><tr><th class="id">id</th><th class="desc"></th><th class="bp">BP</th>';
	if (showMults) html += '<th class="bm">BM</th>';
	html += '<th class="tp">TP</th></tr></thead><tbody></tbody></table>';
	sx.innerHTML = html;
	
}
function sxtoggle()
{
	hidePopup();
	var sx = document.getElementById(SX_id);	
	if (sx.getAttribute('data-show') != '1')
		sxshow();
	else
		sxhide();
}


function tabsGetHash( url ) {	// Tabbing
     var hashPos = url.lastIndexOf ( '#' );
     return url.substring( hashPos + 1 );
}



function tabsSetupTabs()
{
	// Grab the tab links and content divs from the page
   var tabListItems = document.getElementById('tabs').childNodes;
	
   for ( var i = 0; i < tabListItems.length; i++ ) {
     if ( tabListItems[i].nodeName == "LI" ) {
       var tabLink = getFirstChildWithTagName( tabListItems[i], 'A' );
       var id = tabsGetHash( tabLink.getAttribute('href') );
       tabLinks[id] = tabLink;
       contentDivs[id] = document.getElementById( id );
     }
   }

      // Assign onclick events to the tab links, and
      // highlight the first tab
      var i = 0;

      for ( var id in tabLinks ) {
        tabLinks[id].onclick = tabsShowTab;
        tabLinks[id].onfocus = function() { this.blur() };
        if ( i == 0 ) tabLinks[id].className = 'selected';
        i++;
      }

      // Hide all content divs except the first
      var i = 0;

      for ( var id in contentDivs ) {
        if ( i != 0 ) contentDivs[id].className = 'tabContenthide';
		
		var legend = getFirstChildWithTagName( contentDivs[id], 'LEGEND' );
		
		if ( legend ) legend.innerText = '';
        i++;
      }

}

function tabsShowTab() 
{
      var selectedId = tabsGetHash( this.getAttribute('href') );

      // Highlight the selected tab, and dim all others.
      // Also show the selected content div, and hide all others.
      for ( var id in contentDivs ) {
        if ( id == selectedId ) {
          tabLinks[id].className = 'selected';
          contentDivs[id].className = 'tabContent';
        } else {
          tabLinks[id].className = '';
          contentDivs[id].className = 'tabContenthide';
        }
      }

      // Stop the browser following the link
      return false;
}


function tickCombos()
/*
 * This ticks or unticks combinations depending on the ticked status of their underlying
 * bonuses. A combo is ticked if some or all of its bonuses are ticked, controlled by the
 * value of MinimumTicks in the combo record.
 *
 */
{
	var cmbs = document.getElementsByName('ComboID[]');
	for (var i = 0; i < cmbs.length; i++ )
	{
		var tick = true;
		if (cmbs[i].getAttribute('data-rejected') > 0)
			tick = false;
		else
		{
			var bps = cmbs[i].getAttribute('data-bonuses').split(',');
			var ticks = 0;
			var nticks = 0;
			for (var j = 0; j < bps.length; j++ )
				if (bps[j] != '') 
				{
					var bp = document.getElementById('B'+bps[j]);
					var sp = document.getElementById('S'+bps[j]);
					var cp = document.getElementById('C'+bps[j]);
					if ((bp != null && bp.checked) || (sp != null && sp.checked) || (cp != null && cp.checked))
						ticks++;
					else
						nticks++;
				}
				var minticks = cmbs[i].getAttribute('data-minticks');
				if (minticks == 0)
					tick = ticks > 0 && nticks == 0;
				else
					tick = ticks >= minticks;
				var ptsa = cmbs[i].getAttribute('data-pointsarray').split(',');
				var pts = 0;
				if (ptsa.length < 2)
					pts = ptsa[0];
				else
					if (ticks > 0)
					{
						ticks = ticks - minticks;
						if (minticks == 0 || ticks >= ptsa.length)
							pts = ptsa[ptsa.length - 1];
						else
							pts = ptsa[ticks];
					}
				document.getElementById(cmbs[i].getAttribute('id')).setAttribute('data-points',pts);
		}
		document.getElementById(cmbs[i].getAttribute('id')).checked = tick && 
					(document.getElementById(cmbs[i].getAttribute('id')).getAttribute('data-rejected') < 1);
	}
}


function trapDirtyPage()
{
	
	window.addEventListener('beforeunload', function(e) {
	
	var cmd = document.getElementById('savescorebutton');
	if (cmd == null)
		cmd = document.getElementById('savedata'); /* Forms other than scoresheet */
	if (cmd == null)
		return;
	var myPageIsDirty = !cmd.disabled && cmd.getAttribute('data-triggered')=='0';  //you implement this logic...
	if (myPageIsDirty) {
		//following two lines will cause the browser to ask the user if they
		//want to leave. The text of this dialog is controlled by the browser.
		e.preventDefault(); //per the standard
		e.returnValue = ''; //required for Chrome
	}
		//else: user is allowed to leave without a warning dialog
	});
}

function walkBonusArrays(f)
{
	var bt = "BonusID[],SpecialID[]";
	var sgObj = document.getElementById('SGroupsUsed');
	if (sgObj != null)
	{
		var sg = sgObj.value.split(',');
		for (var i = 0; i < sg.length; i++)
			bt += ',SpecialID_' + sg[i] + '[]';
	}
	bt += ',ComboID[]';
	var bta = bt.split(',');
	for (var i = 0; i < bta.length; i++)
	{
		var ba = document.getElementsByName(bta[i]);
		for (var j = 0; j < ba.length; j++)
			f(ba[j].id);
	}
}


function zapScoreDetails()
{
	var sd = document.getElementsByClassName('scoredetail');
	for (var i = 0 ; i < sd.length; i++)
		sd[i].innerText='';
}




