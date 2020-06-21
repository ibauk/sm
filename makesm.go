/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I make standalone, distributable, installations of ScoreMaster
 * ready to be burned to CD/DVD/USB
 *
 * I am written for readability rather than efficiency, please keep me that way.
 *
 *
 * Copyright (c) 2020 Bob Stammers
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
 *
 * The output structure is:-
 *
 * targetFolder
 *	sm
 *		PhpSpreadsheet
 *		vendor
 *		images
 *		jodit
 *		uploads
 *	php
 *	caddy
 *
 */ 


package main

import ("os"
		"io"
		"fmt"
		"log"
		"flag"
		"time"
		"strings"
		"runtime"
		"os/exec"
		"io/ioutil"
		"path/filepath"
)


var SMFLAVOUR	=	"v2.6.1"
var port		=	flag.String("port","80","Webserver port specification")
var caddyFolder	=	flag.String("caddy","..","Path to Caddy folder")
var srcFolder	=	flag.String("src",".","Path to ScoreMaster source")
var phpFolder	=	flag.String("php","C:\\PHP","Path to PHP installation")	
var targetOS	= 	flag.String("os",runtime.GOOS,"Target operating system")
var targetFolder=	flag.String("target","","Path for new installation")
var db2Use		=	flag.String("db","v","v=virgin,r=rblr,l=live database")
var lang2use	=	flag.String("lang","en","Language code (en,de)")
var ok			=	flag.Bool("ok",false,"Overwrite existing target")

var sqlite3 string = "./sqlite3.exe"	// path to executable
var caddy string = "./caddy.exe"

var SMFILES = [...] string {
			"about.php","admin.php","bonuses.php",
			"certedit.php","certificate.css","certificate.php","claims.php","common.php",
			"entrants.php","exportxls.php","emails.php",
			"favicon.ico","importxls.php","index.php",
			"licence.txt","readme.txt","reboot.css",
			"setup.php","score.css","score.js","score.php","sm.php",
			"speeding.php","teams.php","utils.php","timep.php","cats.php",
}

var LANGFILES = [...] string {
			"custom.js", "customvars.php",
}			

var IMAGES	= [...] string {
			"ibauk.png","ibauk90.png",
}

var RBLR_IMAGES = [...] string {
			"ss1000.jpg","smallpoppy.png","rblr.png","poppy.png",
			"rblrhead.png","bb1500.jpg","bbg1500.png",
			"route500AC.jpg","route500CW.jpg",
}

		
var smFolder string

func main() {

	fmt.Println()
	log.Println("MakeSM",SMFLAVOUR,"ScoreMaster installation maker")
	flag.Parse()
	log.Println("Hosted on",runtime.GOOS,"- building for",*targetOS)
	if *targetFolder == "" {
		log.Fatal("You must specify a target folder")
	}
	
	if *ok {
		zapTarget()
	}
	
	// Start by building the folder structure
	log.Println("Building folder structure")
	
	makeFolder(*targetFolder) 
	
	smFolder = filepath.Join(*targetFolder,"sm")
	
	makeFolder(smFolder)
	makeFolder(filepath.Join(smFolder,"images"))
	makeFolder(filepath.Join(smFolder,"uploads"))
	makeFolder(filepath.Join(*targetFolder,"php")) 
	makeFolder(filepath.Join(*targetFolder,"caddy")) 
	
	// Now populate those folders
	copyPHP()
	copyDatabase()
	copySMFiles()
	copyExecs()
	copyImages()
	copyJodit()
	copyPhpPackages()
	log.Println("ScoreMaster installed in "+*targetFolder)
	fmt.Println()

}

func copyDatabase() {

	log.Print("Establishing database")
	if *db2Use != "l" {
	
		if !loadSql("ScoreMaster.sql") {
			log.Fatal("Can't load ScoreMaster.sql")
		}
		if *lang2use != "en" {
			if !loadSql("Reasons-"+*lang2use+".sql") {
				log.Fatal("Can't load foreign reasons")
			}
		}
		if *db2Use == "r" {
			log.Print("Loading RBLR certificates")
			if !loadSql("rblrcerts.sql") {
				log.Fatal("Can't load rblrcerts.sql")
			}
		}
	} else {
		copyFile(filepath.Join(*srcFolder,"ScoreMaster.db"),filepath.Join(smFolder,"ScoreMaster.db"))
	}
	
}

func copyExecs() {

	log.Print("Copying executables")
	copyFile(caddy,filepath.Join(*targetFolder,"caddy","caddy.exe"))
	copyFile(filepath.Join(*srcFolder,"runsm.exe"),filepath.Join(*targetFolder,"runsm.exe"))
	copyFile(filepath.Join(*srcFolder,"runsm.exe"),filepath.Join(*targetFolder,"debugsm.exe"))
}

func copyFile(src, dst string) (int64, error) {
        sourceFileStat, err := os.Stat(src)
        if err != nil {
                return 0, err
        }

        if !sourceFileStat.Mode().IsRegular() {
                return 0, fmt.Errorf("%s is not a regular file", src)
        }

        source, err := os.Open(src)
        if err != nil {
                return 0, err
        }
        defer source.Close()

        destination, err := os.Create(dst)
        if err != nil {
                return 0, err
        }
        defer destination.Close()
        nBytes, err := io.Copy(destination, source)
        return nBytes, err
}


func copyFolderTree(src string, dst string) error {
	var err error
	var fds []os.FileInfo
	var srcinfo os.FileInfo

	if srcinfo, err = os.Stat(src); err != nil {
		return err
	}

	if err = os.MkdirAll(dst, srcinfo.Mode()); err != nil {
		return err
	}

	if fds, err = ioutil.ReadDir(src); err != nil {
		return err
	}
	for _, fd := range fds {
		srcfp := filepath.Join(src, fd.Name())
		dstfp := filepath.Join(dst, fd.Name())

		if fd.IsDir() {
			if err = copyFolderTree(srcfp, dstfp); err != nil {
				fmt.Println(err)
			}
		} else {
			if _, err = copyFile(srcfp, dstfp); err != nil {
				fmt.Println(err)
			}
		}
	}
	return nil
}





func copyImages() {

	log.Print("Copying images")
	copyImageSet(IMAGES[:])
	if *db2Use == "r" {
		copyImageSet(RBLR_IMAGES[:])
	}
}

func copyImageSet(set[]string){

	for _, img := range set {
		_, err := copyFile(filepath.Join(*srcFolder,"images",img),filepath.Join(smFolder,"images",img))
		if err != nil {
			log.Println("Can't copy "+img)
			log.Fatal(err)
		}
	}

}
func copyJodit() {

	log.Print("Copying Jodit WYSIWYG editor")
	
	makeFolder(filepath.Join(smFolder,"jodit"))

	copyFile(filepath.Join(*srcFolder,"jodit-master","build","jodit.min.js"),filepath.Join(smFolder,"jodit","jodit.min.js"))
	copyFile(filepath.Join(*srcFolder,"jodit-master","build","jodit.min.css"),filepath.Join(smFolder,"jodit","jodit.min.css"))
	copyFile(filepath.Join(*srcFolder,"images","icons","fields.png"),filepath.Join(smFolder,"jodit","fields.png"))
	copyFile(filepath.Join(*srcFolder,"images","icons","borders.png"),filepath.Join(smFolder,"jodit","borders.png"))

}

func copyPHP() {

	log.Print("Copying PHP")
	copyFolderTree(*phpFolder,filepath.Join(*targetFolder,"php"))
	ini := filepath.Join(*srcFolder,"php","php.ini")
	if _, err := os.Stat(ini); err == nil {
		copyFile(ini,filepath.Join(*targetFolder,"php","php.ini"))
	}
	
}

func copyPhpPackages() {

	log.Print("Copying PHP packages")
	copyFolderTree(filepath.Join(*srcFolder,"vendor"),filepath.Join(smFolder,"vendor"))

}

func copySMFiles() {

	var lng string = "."
	
	log.Print("Copying main SM application")
	
	if *lang2use != "en" {
		lng = "-"+*lang2use+"."
	}
	for _, s := range SMFILES {
		_, err := copyFile(filepath.Join(*srcFolder,s),filepath.Join(smFolder,s))
		if err != nil {
			log.Println("Can't copy "+s)
		}
	}
	for _, s := range LANGFILES {
		_, err := copyFile(filepath.Join(*srcFolder,strings.Replace(s,".",lng,1)),filepath.Join(smFolder,s))
		if err != nil {
			log.Println("Can't copy "+s+lng)
		}
	}
}


func loadSql(sqlfile string) bool {

	sql, err := ioutil.ReadFile(filepath.Join(*srcFolder,sqlfile))
	cmd := exec.Command(sqlite3,filepath.Join(smFolder,"ScoreMaster.db"))
	stdin, err := cmd.StdinPipe()
	if err != nil {
		log.Fatal("Can't load database from SQL")
	}
	go func() {
		defer stdin.Close()
		io.WriteString(stdin, string(sql))
	}()
	_, err = cmd.CombinedOutput()
	return err == nil
}

func makeFolder(folder string) {

	if !establishFolder(folder) {
		log.Fatal("Can't establish folder "+folder)
	}
	
}

func establishFolder(folder string) bool {

	err := os.Mkdir(folder,0777)
	return err == nil 
}

func zapTarget() {	

	log.Print("Overwriting "+*targetFolder)
	os.RemoveAll(*targetFolder)	
	time.Sleep(1 * time.Second)
}
