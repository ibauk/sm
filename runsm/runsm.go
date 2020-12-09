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
 *	2020-06-29	Window title
 *	2020-12-04	Bumped to v2.7, Caddy v2
 *
 */

package main

import (
	"context"
	"flag"
	"fmt"
	"log"
	"net"
	"os"
	"os/exec"
	"path/filepath"
	"runtime"
	"strings"
	"time"

	"github.com/pkg/browser"
)

const myPROGTITLE = "ScoreMaster Server v2.7 [2020-12-09]"
const myWINTITLE = "IBA ScoreMaster"

var phpcgi = filepath.Join("php", "php-cgi")
var phpdbg = ""

var debug = flag.Bool("debug", false, "Run in PHP debug mode (Windows only)")
var port = flag.String("port", "80", "Webserver port specification")
var ipspec = flag.String("ip", "*", "Webserver IP specification")
var spawnInterval = flag.Int("respawn", 60, "Number of minutes before restarting PHP server")
var nolocal = flag.Bool("nolocal", false, "Don't start a web browser on the host machine")
var root = flag.String("root", "/", "HTTP document root")

const cgiport = "127.0.0.1:9000"
const smCaddyFolder = "caddy"
const starturl = "http://localhost"

type logWriter struct {
}

func (writer logWriter) Write(bytes []byte) (int, error) {
	return fmt.Print(time.Now().UTC().Format("2006-01-02 15:04:05") + " " + string(bytes))
}

func init() {

	log.SetFlags(0)
	log.SetOutput(new(logWriter))

	os := runtime.GOOS
	switch os {
	case "darwin": // Apple

		phpdbg = "/usr/bin/php"

	case "linux":

	case "windows":

		phpdbg = "\\php\\php"
		setMyWindowTitle(myWINTITLE)

	default:
		// freebsd, openbsd,
		// plan9, ...
	}
}

func main() {

	fmt.Printf("\n%s\t\t%s\n", "Iron Butt Association UK", "webmaster@ironbutt.co.uk")
	fmt.Printf("\n%s\n\n", myPROGTITLE)

	setupRun()
	serverIP := getOutboundIP()
	fmt.Printf("%s IPv4 = %s\n", timestamp(), serverIP)

	if *debug && runtime.GOOS != "windows" {

		*debug = false
	}
	if *debug && phpdbg != "" {
		debugPHP()
	} else {
		go runCaddy()
		go runPHP()
	}

	if !*nolocal {
		showInvite()
	}

	if *debug {
		fmt.Printf("%s quitting\n\n", timestamp())
		os.Exit(0)
	}

	// Now just kill time and wait for someone to kill me
	for {
		time.Sleep(1 * time.Minute)
		myIP := getOutboundIP()
		if !myIP.Equal(serverIP) {
			serverIP = myIP
			fmt.Printf("%s IPv4 = %s\n", timestamp(), serverIP)
		}
	}
}

func showInvite() {

	time.Sleep(5 * time.Second)
	fmt.Println(timestamp() + " presenting " + starturl + ":" + *port)
	browser.OpenURL(starturl + ":" + *port)

}

func debugPHP() {
	// This runs PHP as a local, single user, webserver as an aid to debugging or for
	// very lightweight usage

	if phpdbg == "" {
		return
	}
	fmt.Println(timestamp() + " debugging PHP")
	cmd := exec.Command("cmd", "/C", "start", "/min", phpdbg, "-S", "127.0.0.1:"+*port, "-t", "sm", "-c", filepath.Join("php", "php.ini"))
	cmd.Env = append(os.Environ(), "PHP_FCGI_MAX_REQUESTS=0")
	err := cmd.Start()
	if err != nil {
		log.Fatal(err)
	}

}

func execPHP() {
	// This runs PHP as a background service to an external webserver
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(*spawnInterval)*time.Minute)
	defer cancel()
	if err := exec.CommandContext(ctx, phpcgi, "-b", cgiport).Run(); err != nil {
		//fmt.Println(phpcgi+" <=== ")
		log.Println(err)
	}
}

func getOutboundIP() net.IP {
	udp := "udp"
	ip := "8.8.8.8:80" // Google public DNS

	//	udp := "udp6"
	//	ip := "[2a03:2880:f003:c07:face:b00c::2]:80"	// Facebook public DNS

	conn, err := net.Dial(udp, ip)
	if err != nil {
		log.Print(err)
		return net.IPv4(127, 0, 0, 1)
	}
	defer conn.Close()

	localAddr := conn.LocalAddr().(*net.UDPAddr)

	return localAddr.IP
}

func timestamp() string {

	var t = time.Now()
	return t.Format("2006-01-02 15:04:05")

}

func runPHP() {

	cgi := strings.Split(cgiport, ":")
	if !rawPortAvail(cgi[1]) {
		fmt.Println(timestamp() + " PHP [" + cgi[1] + "] already listening")
		return
	}
	os.Setenv("PHP_FCGI_MAX_REQUESTS", "0") // PHP defaults to die after 500 requests so disable that
	x := "spawning"
	for {
		fmt.Printf("%s %s PHP\n", timestamp(), x)
		x = "respawning"
		execPHP()
	}

}

func runCaddy() {

	// If IP is not wildcard then assume that grownup has checked
	if *ipspec == "*" && (!rawPortAvail(*port) || !testWebPort(*port)) {
		fmt.Println(timestamp() + " service port " + *port + " already served")
		return
	}
	fmt.Printf(timestamp() + " serving on " + *ipspec + ":" + *port + "\n")
	// Create the conf file
	cp := filepath.Join(smCaddyFolder, "caddyfile")
	//	ep := filepath.Join(smCaddyFolder, "error.log")
	f, err := os.Create(cp)
	if err != nil {
		log.Fatal(err)
	}
	f.WriteString("{\nhttp_port " + *port + "\n}\n")
	f.WriteString(*ipspec + ":" + *port + "\n")
	f.WriteString("file_server\n")
	f.WriteString("root sm\n")
	//	f.WriteString("errors " + ep + "\n")
	f.WriteString("php_fastcgi " + *root + " " + cgiport + " php\n")
	f.Close()

	// Now run Caddy
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	fp := filepath.Join(smCaddyFolder, "caddy")
	if err := exec.CommandContext(ctx, fp, "start").Run(); err != nil {
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
		*debug = strings.Index(filename, "debug") >= 0
	}
}

func rawPortAvail(port string) bool {

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

func testWebPort(port string) bool {

	ln, err := net.Listen("tcp", ":"+port)

	if err != nil {
		fmt.Fprintf(os.Stderr, "Can't listen on port %q: %s", port, err)
		return false
	}

	ln.Close()
	fmt.Printf("TCP Port %q is available", port)
	return true
}
