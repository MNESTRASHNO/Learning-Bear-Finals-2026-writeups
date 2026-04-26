package sanitizer

import (
	"testing"
)

func TestSanitize_AllowsSafeHTML(t *testing.T) {
	safe := []string{
		"<p>hello</p>",
		"<b>bold</b> and <i>italic</i>",
		"<a href=\"https://example.com\">link</a>",
		"<h1>heading</h1><h2>sub</h2>",
		"<ul><li>one</li><li>two</li></ul>",
		"<table><tr><td>cell</td></tr></table>",
		"<blockquote>quote</blockquote>",
		"<img src=\"https://example.com/img.png\" alt=\"pic\">",
		"<br><hr>",
		"plain text with no tags",
		"<div class=\"note\"><span id=\"x\">content</span></div>",
	}
	for _, input := range safe {
		out, err := Sanitize(input)
		if err != nil {
			t.Errorf("Sanitize(%q) returned error: %v", input, err)
		}
		if out != input {
			t.Errorf("Sanitize(%q) = %q, want original", input, out)
		}
	}
}

func TestSanitize_BlocksTags(t *testing.T) {
	blocked := []string{
		"<script>alert(1)</script>",
		"<SCRIPT>alert(1)</SCRIPT>",
		"<iframe src=x></iframe>",
		"<object data=x></object>",
		"<embed src=x>",
		"<svg onload=alert(1)>",
		"<math><mi>x</mi></math>",
		"<style>body{display:none}</style>",
		"<base href=x>",
		"<link rel=stylesheet href=x>",
		"<meta http-equiv=refresh content=0>",
		"<form action=x><input></form>",
		"<template><img src=x></template>",
		"<textarea>x</textarea>",
		"<button>x</button>",
		"<details open><summary>x</summary></details>",
		"<dialog open>x</dialog>",
		"<noscript><img src=x></noscript>",
		"<title>x</title>",
		"<applet code=x></applet>",
	}
	for _, input := range blocked {
		_, err := Sanitize(input)
		if err == nil {
			t.Errorf("Sanitize(%q) should have returned error", input)
		}
	}
}

func TestSanitize_BlocksEventHandlers(t *testing.T) {
	blocked := []string{
		`<img src=x onerror=alert(1)>`,
		`<div onclick=alert(1)>x</div>`,
		`<body onload=alert(1)>`,
		`<a onmouseover=alert(1)>x</a>`,
		`<input onfocus=alert(1)>`,
		`<marquee onstart=alert(1)>`,
		`<video onloadstart=alert(1)><source src=x>`,
		`<details ontoggle=alert(1) open>`,
		`<img src=x ONERROR=alert(1)>`,
	}
	for _, input := range blocked {
		_, err := Sanitize(input)
		if err == nil {
			t.Errorf("Sanitize(%q) should have returned error", input)
		}
	}
}

func TestSanitize_BlocksDangerousProtocols(t *testing.T) {
	blocked := []string{
		`<a href="javascript:alert(1)">x</a>`,
		`<a href="JAVASCRIPT:alert(1)">x</a>`,
		`<a href="data:text/html,<script>alert(1)</script>">x</a>`,
		`<a href="vbscript:alert(1)">x</a>`,
		`<img src="javascript:alert(1)">`,
		`<a href="	javascript:alert(1)">tab</a>`,
	}
	for _, input := range blocked {
		_, err := Sanitize(input)
		if err == nil {
			t.Errorf("Sanitize(%q) should have returned error", input)
		}
	}
}

func TestSanitize_BlocksDangerousAttrs(t *testing.T) {
	blocked := []string{
		`<div style="background:url(x)">x</div>`,
		`<iframe srcdoc="<script>alert(1)</script>"></iframe>`,
		`<a ping="https://evil.com">x</a>`,
	}
	for _, input := range blocked {
		_, err := Sanitize(input)
		if err == nil {
			t.Errorf("Sanitize(%q) should have returned error", input)
		}
	}
}

func TestSanitize_BlocksBlockedTagNames(t *testing.T) {
	blocked := []string{
		"<xmp><script>alert(1)</script></xmp>",
		"<plaintext>",
	}
	for _, input := range blocked {
		_, err := Sanitize(input)
		if err == nil {
			t.Errorf("Sanitize(%q) should have returned error", input)
		}
	}
}

func TestSanitize_ContentTooLong(t *testing.T) {
	long := make([]byte, 50001)
	for i := range long {
		long[i] = 'a'
	}
	_, err := Sanitize(string(long))
	if err == nil {
		t.Error("Sanitize should reject content over 50000 bytes")
	}
}

func TestSanitize_SelectBypass(t *testing.T) {
	payloads := []string{
		`<select><img src=x onerror=alert(1)></select>`,
		`<select><b onclick=alert(1)>X</b></select>`,
		`<select><a href="javascript:alert(1)">click</a></select>`,
		`<select><div onmouseover=alert(1)>X</div></select>`,
		`<select><svg onload=alert(1)></select>`,
		`<select><iframe src=x></select>`,
	}
	for _, input := range payloads {
		out, err := Sanitize(input)
		if err != nil {
			t.Errorf("Sanitize(%q) blocked (err=%v), but Go parser drops content inside <select> — this payload bypasses the sanitizer", input, err)
			continue
		}
		if out != input {
			t.Errorf("Sanitize(%q) = %q, want original preserved", input, out)
		}
	}
}
