/*
 * I B A U K   -   S C O R E M A S T E R
 *
 * I'm responsible for initiating and maintaining a PHP CGI server.
 *
 * PHP running in CGI mode can suffer from memory leaks and other flaws so it's
 * desirable to restart the executable periodically.
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


package main

import ("fmt" 
		"context"
		"flag"
		"time"
		"log"
		"path/filepath"
		"os"
		"os/exec")

// maybe overwritten by command line args
var	PHPTimeoutSeconds	= 3600 				// respawn PHP after this period
var	PHPExecFolder		= "php"
var PHPExecName			= "php-cgi.exe"	
var	PHPServicePort		= "127.0.0.1:9000"

func main() {
	fmt.Printf("\n\n%s\n\n","ScoreMaster PHP Server v0.1")
	fmt.Printf("%s\n\n","Copyright (c) 2019 Bob Stammers")

	parseArgs()
	
	cwd, err := os.Getwd()
	if err != nil {
	}
	fmt.Printf("%s\n\n",cwd)
	os.Setenv("PHP_FCGI_MAX_REQUESTS","0") // PHP defaults to die after 500 requests so disable that
	for {
		var t = time.Now()
		fmt.Printf(t.Format(time.Stamp))
		fmt.Println(" running PHP")
		RunPHP()
	}
}

func parseArgs() {
	secs	:= flag.Int("secs",3600,"Seconds before respawn") 				// respawn PHP after this period
	phpf	:= flag.String("php","php","Path to PHP folder")
	cgix	:= flag.String("cgi","php-cgi.exe","CGI executable")
	
	flag.Parse()
	PHPTimeoutSeconds = *secs
	PHPExecFolder = *phpf
	PHPExecName = *cgix

}

func RunPHP() {
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(PHPTimeoutSeconds)*time.Second)
	defer cancel()
	fp := filepath.Join(PHPExecFolder,PHPExecName)
	//fmt.Println(fp)
	if err := exec.CommandContext(ctx, fp,"-b",PHPServicePort).Run(); err != nil {
		log.Print(err)
	}
}
