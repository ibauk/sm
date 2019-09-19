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
 *
 *	2019-09	First Go release
 *
 */


package main

import ("fmt"
		"os"
		"net"
		"log"
		"time"
		"path/filepath"
		"runtime"
		"context"
		"os/exec"
		"browser"
		"strings"
		"flag")

var PROGTITLE = "ScoreMaster Server v2.4 [2019-09-19]"

var def_phpf	= "php"	// assume portable installation under me

var port = flag.String("port","80","Webserver port specification")
var ipspec = flag.String("ip","*","Webserver IP specification")
var spawnInterval = flag.Int("respawn",60,"Number of minutes before restarting PHP server")
var phpf = flag.String("php",def_phpf,"Folder containing PHP executables")
var cdyf = flag.String("caddy","caddy","Folder containing Caddy files")
var nolocal = flag.Bool("nolocal",false,"Don't start a web browser on the host machine")
var root = flag.String("root","/","HTTP document root")

var cgiport = "127.0.0.1:9000"
var phpx = "php-cgi"
var cdyx = "caddy"
var smf = "sm" 			// Contains ScoreMaster application files
var starturl = "http://localhost"

func init() {
	switch os := runtime.GOOS; os {
	case "darwin":
		// Apple
	case "linux":
		def_phpf = "/usr/bin/php"
	default:
		// freebsd, openbsd,
		// plan9, windows...
	}
}

func main() {

	fmt.Printf("\n%s\t\t%s\n","Iron Butt Association UK","webmaster@ironbutt.co.uk")
	fmt.Printf("\n%s\n\n",PROGTITLE)
	setupRun()
	fmt.Printf("%s IPv4 = %s\n",timestamp(),getOutboundIP())
	go runCaddy()
	go runPHP()
	
	if !*nolocal {
		showInvite()
	}
	
	// Now just kill time and wait for someone to kill me
	for {
		time.Sleep(5*time.Minute)
	}
}

func showInvite() {

	time.Sleep(5*time.Second)
	fmt.Println(timestamp()+" presenting "+starturl+":"+*port)
	browser.OpenURL(starturl+":"+*port)

}


func execPHP() {
// This runs PHP as a background service to an external webserver
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(*spawnInterval)*time.Minute)
	defer cancel()
	fp := filepath.Join(*phpf,phpx)
	if err := exec.CommandContext(ctx, fp,"-b",cgiport).Run(); err != nil {
		log.Fatal(err)
	}
}


func getOutboundIP() net.IP {
	udp := "udp4"
	ip := "8.8.8.8:80"
    conn, err := net.Dial(udp, ip)
    if err != nil {
        log.Fatal(err)
    }
    defer conn.Close()

    localAddr := conn.LocalAddr().(*net.UDPAddr)

    return localAddr.IP
}

func timestamp() string {

		var t = time.Now()
		return t.Format(time.Stamp)

}



func runPHP() {

	cgi := strings.Split(cgiport,":")
	if !raw_portAvail(cgi[1]) {
		fmt.Println(timestamp()+" PHP ["+cgi[1]+"] already listening")
		return
	}
	os.Setenv("PHP_FCGI_MAX_REQUESTS","0") // PHP defaults to die after 500 requests so disable that
	for {
		fmt.Println(timestamp()+" running PHP")
		execPHP()
	}

}

func runCaddy() {

	// If IP is not wildcard then assume that grownup has checked
	if *ipspec=="*" && !raw_portAvail(*port) {
		fmt.Println(timestamp()+" service port "+*port+" already served")
		return
	}
	fmt.Printf(timestamp()+" serving on "+*ipspec+":"+*port+"\n")
	// Create the conf file
	cp := filepath.Join(*cdyf,"caddyfile")
	ep := filepath.Join(*cdyf,"error.log")
	f, err := os.Create(cp)
	if err != nil {
		log.Fatal(err)
	}
	f.WriteString(*ipspec+":"+*port+"\n")
	f.WriteString("root "+smf+"\n")
	f.WriteString("errors "+ep+"\n")
	f.WriteString("fastcgi "+*root+" "+cgiport+" php\n")
	f.Close()
	
	// Now run Caddy
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	fp := filepath.Join(*cdyf,cdyx)
	if err := exec.CommandContext(ctx, fp,"-agree","-conf",cp).Run(); err != nil {
		log.Fatal(err)
	}

}

func setupRun() {

	args := os.Args
	
	// Change to the folder containing this executable
	dir := filepath.Dir(args[0])
	os.Chdir(dir)
	flag.Parse()
	
}


func raw_portAvail(port string) bool {

	timeout := time.Second
    conn, err := net.DialTimeout("tcp", net.JoinHostPort("localhost", port), timeout)
	if err != nil {
		return true
	}
	if conn != nil {
		defer conn.Close()
		return false
	}
	return true
}