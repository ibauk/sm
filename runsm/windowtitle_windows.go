package main

import (
	"syscall"
	"unsafe"
)

func setMyWindowTitle(txt string) {
	/* Windows only */
	mod := syscall.NewLazyDLL("user32.dll")
	proc := mod.NewProc("GetForegroundWindow")
	hwnd, _, _ := proc.Call()
	if hwnd != 0 {
		proc1 := mod.NewProc("SetWindowTextW")
		buf := make([]uint16, len(txt))
		buf = syscall.StringToUTF16(txt)
		proc1.Call(uintptr(hwnd), uintptr(unsafe.Pointer(&buf[0])))
	}

}
