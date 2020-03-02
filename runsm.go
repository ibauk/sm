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
 *	2019-09-21	First Go release
 *	2019-09-29	Check/report changed IP
 *	2019-11-05	Linux setup; webserver/PHP already available
 *	2020-02-19	Apple Mac setup
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
		"github.com/pkg/browser"
		"strings"
		"flag")

const PROGTITLE = "ScoreMaster Server v2.5 [2020-02-19]"

var phpcgi		= filepath.Join("php","php-cgi")
var phpdbg 		= ""

var debug			= flag.Bool("debug",false,"Run in PHP debug mode (Windows only)")
var port 			= flag.String("port","80","Webserver port specification")
var ipspec 			= flag.String("ip","*","Webserver IP specification")
var spawnInterval 	= flag.Int("respawn",60,"Number of minutes before restarting PHP server")
var nolocal 		= flag.Bool("nolocal",false,"Don't start a web browser on the host machine")
var root 			= flag.String("root","/","HTTP document root")

const cgiport 			= "127.0.0.1:9000"
const sm_caddyFolder	= "caddy"
const starturl 			= "http://localhost"

func init() {
	os := runtime.GOOS;
	switch os {
	case "darwin":  // Apple
	
		phpdbg = "/usr/bin/php"
		
	case "linux":
	
	case "windows":
	
		phpdbg = "\\php\\php"
		
	default:
		// freebsd, openbsd,
		// plan9, ...
	}
}

func main() {

	fmt.Printf("\n%s\t\t%s\n","Iron Butt Association UK","webmaster@ironbutt.co.uk")
	fmt.Printf("\n%s\n\n",PROGTITLE)
	setupRun()
	serverIP := getOutboundIP()
	fmt.Printf("%s IPv4 = %s\n",timestamp(),serverIP)
	
	if *debug && runtime.GOOS != "windows" {
		
		*debug = false
	}
	if *debug && phpdbg != "" {
		debugPHP()
	} else if runtime.GOOS != "linux" {
		go runCaddy()
		go runPHP()
	}
	
	if !*nolocal {
		showInvite()
	}
	
	if *debug {
		fmt.Printf("%s quitting\n\n",timestamp())
		os.Exit(0)
	}
	
	// Now just kill time and wait for someone to kill me
	for {
		time.Sleep(1*time.Minute)
		myIP := getOutboundIP()
		if !myIP.Equal(serverIP) {
			serverIP = myIP
			fmt.Printf("%s IPv4 = %s\n",timestamp(),serverIP)
		}
	}
}

func showInvite() {

	time.Sleep(5*time.Second)
	fmt.Println(timestamp()+" presenting "+starturl+":"+*port)
	browser.OpenURL(starturl+":"+*port)

}

func debugPHP() {
// This runs PHP as a local, single user, webserver as an aid to debugging or for
// very lightweight usage

	if phpdbg == "" {
		return
	}
	fmt.Println(timestamp()+" debugging PHP")
	cmd := exec.Command("cmd","/C","start","/min",phpdbg,"-S","127.0.0.1:"+*port,"-t","sm","-c",filepath.Join("php","php.ini"))
	cmd.Env = append(os.Environ(),"PHP_FCGI_MAX_REQUESTS=0")
	err := cmd.Start()
	if err != nil {
		log.Fatal(err)
	}

}


func execPHP() {
// This runs PHP as a background service to an external webserver
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(*spawnInterval)*time.Minute)
	defer cancel()
	if err := exec.CommandContext(ctx, phpcgi,"-b",cgiport).Run(); err != nil {
		//fmt.Println(phpcgi+" <=== ")
		log.Println(err)
	}
}


func getOutboundIP() net.IP {
	udp := "udp4"
	ip := "8.8.8.8:80"	// Google public DNS
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
	x := "spawning"
	for {
		fmt.Printf("%s %s PHP\n",timestamp(),x)
		x = "respawning"
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
	cp := filepath.Join(sm_caddyFolder,"caddyfile")
	ep := filepath.Join(sm_caddyFolder,"error.log")
	f, err := os.Create(cp)
	if err != nil {
		log.Fatal(err)
	}
	f.WriteString(*ipspec+":"+*port+"\n")
	f.WriteString("root sm\n")
	f.WriteString("errors "+ep+"\n")
	f.WriteString("fastcgi "+*root+" "+cgiport+" php\n")
	f.Close()
	
	// Now run Caddy
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	fp := filepath.Join(sm_caddyFolder,"caddy")
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
	if !*debug && runtime.GOOS == "windows" {
		filename := filepath.Base(os.Args[0])
		*debug = strings.Index(filename,"debug") >= 0
	}
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

