###############################################################################
#																			  #
#           I B A U K   S C O R E M A S T E R   A P P L I C A T I O N         #
#                                                                             #
#                                                                             #
# Author: Bob Stammers <stammers.bob@gmail.com>                               #
#                                                                             #
# Licence: GPL                                                                #
#                                                                             #
#                                                                             #
###############################################################################

Portable rally scoring system runs on any host webserver serving PHP & SQLite

admin.php		Entry page for administrators
certificate.php	Certificate printing
common.php		Literals + page header + footer
entrants.php	Admin routines handling entrants
exportxls.php	Data export handling
importxls.php	Data import handling
index.php		Default handler for new connections
score.js		Score calculations
score.php		Entry page for scorers
ScoreMaster.db	SQLite database
sm.php			Administration subroutines
readme.txt		This file.

certificates	Certificate HTML templates
images			Images used in certificate production
PHPExcel		Library code for handling spreadsheets & CSVs
uploads			Folder used to hold uploaded files
