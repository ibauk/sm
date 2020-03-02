/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * custom.js
 *
 * I hold all translateable/customisable variables and settings
 *
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

 
const DNF_TOOFEWPOINTS = "Not enough points";
const DNF_TOOFEWMILES = "Not enough miles";
const DNF_TOOMANYMILES = "Too many miles";
const DNF_FINISHEDTOOLATE = "Finished too late";
const DNF_MISSEDCOMPULSORY = "Missed a compulsory bonus";
const DNF_COMPOUNDRULE = "Breached a compound rule";
const DNF_SPEEDING = "Excessive speed";

// Elements of Score explanation, include trailing space, etc
const RPT_Tooltip	= "Click for explanation\rdoubleclick to print";
const RPT_Bonuses	= "Bonuses ticked";
const RPT_Specials	= "Specials";
const RPT_Combos	= "Combos";
const RPT_MPenalty	= "Mileage penalty";
const RPT_TPenalty	= "Late penalty";
const RPT_Total 	= "TOTAL";
const RPT_SPenalty	= "Speed penalty";

const CFGERR_MethodNIY = "Error: compoundCalcRuleMethod {0} not implemented yet";
const CFGERR_NotBonuses = "Error: compoundCalcRuleType {0} not applicable to bonuses";

const ASK_MINUTES = "Please enter the number of rest minutes for ";
const ASK_POINTS = "Please enter the points for";
const LOOKUP_ENTRANT = "Find entrant record matching what?";
const CLAIM_REJECTED = "Claim rejected";
const FINISHERS_EXPORTED = "Finishers Exported!";

const OBSORTAZ = "Sort into Bonus id order";
const APPLYCLOSE = "Apply changes/close";

const MY_LOCALE	= "en-GB";