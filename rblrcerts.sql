/*
 * RBLR CERTIFICATES
 *
 *
 * As at June 2019
 *
 */


INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 2 NCW -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Fort William, Wick and Edinburgh before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,2,'RBLR 1000 NCW');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 1 NAC -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Edinburgh, Wick and Fort William before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,1,'RBLR 1000 NAC');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 7 500AC -->
<img class="header_badge" src="images/route500AC.jpg" alt="Iron Butt Association Ride Certificate" />
<p>500 miles in less than 24 hours</p>
</div>
<div class="citation">
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 504 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Beverly, Berwick and Millom before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>This ride was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/rblr.png" alt="" style="float:right;padding-top:0em;"/>
<img src="images/poppy.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,7,'RBLR 500 AC');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 6 500CW -->
<img class="header_badge" src="images/route500CW.jpg" alt="Iron Butt Association Ride Certificate" />
<p>500 miles in less than 24 hours</p>
</div>
<div class="citation">
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 504 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Millom, Berwick and Beverly before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>This ride was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/rblr.png" alt="" style="float:right;padding-top:0em;"/>
<img src="images/poppy.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,6,'RBLR 500 CW');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 5 BBG -->
<img class="header_badge" src="images/bbg1500.png" alt="Iron Butt Association Ride Certificate" />
<p>1,500 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,527 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Folkestone, Swansea and Perth before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The BunBurner Gold extreme ride was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,5,'RBLR BBG');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 3 SAC -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em;/>
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Bangor, Brighton and Lowestoft before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,3,'RBLR 1000 SAC');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 4 SCW -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Lowestoft, Brighton and Bangor before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,4,'RBLR 1000 SCW');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 8 Cert NAC -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles starting in Squires cafe, Yorkshire continuing onto Edinburgh, Wick and Fort William before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,8,'RBLR cert NAC');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 9 Cert NCW -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,006 gruelling miles starting in Squires cafe, Yorkshire continuing onto Fort William, Wick and Edinburgh before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,9,'RBLR cert NCW');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 10 Cert SAC -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles starting in Squires cafe, Yorkshire continuing onto Bangor, Brighton and Lowestoft before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,10,'RBLR cert SAC');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 11 Cert SCW -->
<img class="header_badge" src="images/rblrhead.png" alt="RBLR Ride Certificate" />
<h2>The RBLR 1000 mile ride</h2>
<p>1,000 miles in support of the Poppy Appeal</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles starting in Squires cafe, Yorkshire continuing onto Bangor, Brighton and Lowestoft before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict" >
<p>The RBLR 1000 was conducted under very strict guidelines set forth by the Iron Butt Association UK.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,11,'RBLR cert SCW');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 12 BB -->
<img class="header_badge" src="images/bb1500.jpg" alt="Iron Butt Association Ride Certificate" />
<p>1,500 miles in less than 36 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #RiderName# rode a #Bike# a total of 1,527 gruelling miles in less than 36 hours starting in Squires cafe, Yorkshire continuing onto Folkestone, Swansea and Perth before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The BunBurner ride was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:4em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,12,'RBLR bb1500');
INSERT INTO "certificates" ("EntrantID","css","html","options","image","Class","Title") VALUES (0,'/*
 * ridecertsm.css
 *
 */
@page 
{
	margin-top:0; 
	margin-bottom:0;
}
* 
{
	margin:0;padding:0
}

body {
	font-family: "Times New Roman", Helvetica, Verdana, Arial, sans-serif, Times;
	font-size: 100%;
	background: #fff;
	text-align: left;
	padding: 0px;
	color: #000;
}
.certificate
{
	/* 
	 *	A4 is 210 x 297
	 *	Less standard .5in margins = 185 x 272
	 *
	 * These settings produce usable results on Chrome, FireFox and Edge (with fiddling)
	 * Improve them if you must but beware.
	 */
	width: 150mm;
	height: 25.0cm;
    margin-top: 0mm;
	margin-bottom: auto;
    padding: 5mm 10mm 5mm 10mm; 
	border:  double;
	margin-left:auto;
	margin-right:auto;
	page-break-after:always;
	position: relative;
	top: 10mm;
}
.header
{
	text-align: center;
	padding-bottom:2cm;
}
.header img 
{
	width:90%;
	margin-left: auto;
	margin-right: auto;
}
.header p 
{
	font-style: italic;
}
div
{
	padding-bottom:1cm;
}
.citation
{
	text-align: justify;
	font-size:14pt;
}
.strict
{
	font-style: italic;
	text-align:justify;
	font-size:13pt;
}

.header_badge
{
	height: 10cm;
	width: auto;
}

.signature
{
	margin-top:3cm;
	width:7cm;
	border-top:solid;
}


.metadata {
display: none;
}
.floating-menu {
font-family: sans-serif;
background: #8B008B;
padding: 5px;;
width: 130px;
z-index: 100;
position: fixed;
top: 5px;
left: 5px;
border-radius: 5px;
}

.floating-menu a, 
.floating-menu h3 {
font-size: 0.9em;
display: inline;
margin: 0 0.5em;
color: white;
}
@media print {
.floating-menu,
.floating-menu a,
.floating-menu h3 { display: none; }
}
','<div class="header"><!-- 13 FSB -->
<img class="header_badge" src="images/ss1000.jpg" alt="Iron Butt Association Ride Certificate" />
<h2>The RBLR SaddleSore 1000</h2>
<p>1,000 miles in less than 24 hours</p>
</div>
<div class="citation">
<img src="images/smallpoppy.png" alt="" style="float:left;" padding-right:1em; />
<p>This is to certify that on the #DateRallyRange#, #CrewName# rode a #Bike# a total of 1,004 gruelling miles in less than 24 hours starting in Squires cafe, Yorkshire continuing onto Folkestone, Swansea and Bodmin before ending in Squires cafe, Yorkshire while participating in the RBLR1000 event in support of the Royal British Legion''s Poppy Appeal.</p>
</div>
<div class="strict">
<p>The SaddleSore 1000 was conducted under very strict guidelines set forth by the Iron Butt Association.  Only a handful of riders from around the world have managed to solve the challenges such a gruelling ride involves.</p>
</div>
<img src="images/ibauk.png" alt="" style="float:right;padding-top:2em;"/>
<div class="signature">
<p><strong>Philip Weston</strong><br />
President, <br />
The Iron Butt Association UK</p>
</div>',NULL,NULL,13,'RBLR 1000 FSB');

