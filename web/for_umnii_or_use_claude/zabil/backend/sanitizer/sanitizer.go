package sanitizer

import (
	"fmt"
	"strings"

	"golang.org/x/net/html"
	"golang.org/x/net/html/atom"
)

var blockedTags = map[atom.Atom]bool{
	atom.Script:   true,
	atom.Style:    true,
	atom.Iframe:   true,
	atom.Object:   true,
	atom.Embed:    true,
	atom.Applet:   true,
	atom.Base:     true,
	atom.Link:     true,
	atom.Meta:     true,
	atom.Svg:      true,
	atom.Math:     true,
	atom.Template: true,
	atom.Form:     true,
	atom.Input:    true,
	atom.Textarea: true,
	atom.Button:   true,
	atom.Details:  true,
	atom.Dialog:   true,
	atom.Frameset: true,
	atom.Frame:    true,
	atom.Noscript: true,
	atom.Title:    true,
}

var blockedTagNames = map[string]bool{
	"xmp":       true,
	"plaintext": true,
	"listing":   true,
	"comment":   true,
	"xml":       true,
	"xss":       true,
	"image":     true,
}

var dangerousProtocols = []string{
	"javascript:",
	"data:",
	"vbscript:",
	"mhtml:",
	"file:",
}

var dangerousAttrs = map[string]bool{
	"style":      true,
	"srcdoc":     true,
	"xmlns":      true,
	"xlink:href": true,
	"formaction": true,
	"ping":       true,
	"is":         true,
}

var urlAttrs = map[string]bool{
	"href":       true,
	"src":        true,
	"action":     true,
	"formaction": true,
	"data":       true,
	"poster":     true,
	"background": true,
	"dynsrc":     true,
	"lowsrc":     true,
	"codebase":   true,
}

func Sanitize(input string) (string, error) {
	if len(input) > 50000 {
		return "", fmt.Errorf("content exceeds maximum length")
	}

	doc, err := html.Parse(strings.NewReader(input))
	if err != nil {
		return "", fmt.Errorf("malformed HTML: %w", err)
	}

	if err := validate(doc); err != nil {
		return "", err
	}

	return input, nil
}

func validate(n *html.Node) error {
	if n.Type == html.ElementNode {
		tag := strings.ToLower(n.Data)

		if blockedTags[n.DataAtom] {
			return fmt.Errorf("disallowed HTML content")
		}
		if blockedTagNames[tag] {
			return fmt.Errorf("disallowed HTML content")
		}

		for _, attr := range n.Attr {
			if err := checkAttr(tag, attr); err != nil {
				return err
			}
		}
	}

	for c := n.FirstChild; c != nil; c = c.NextSibling {
		if err := validate(c); err != nil {
			return err
		}
	}
	return nil
}

func checkAttr(tag string, attr html.Attribute) error {
	name := strings.ToLower(attr.Key)

	if strings.HasPrefix(name, "on") {
		return fmt.Errorf("disallowed HTML content")
	}

	if dangerousAttrs[name] {
		return fmt.Errorf("disallowed HTML content")
	}

	if urlAttrs[name] {
		val := strings.TrimSpace(strings.ToLower(attr.Val))
		val = strings.Map(func(r rune) rune {
			if r == '\t' || r == '\n' || r == '\r' {
				return -1
			}
			return r
		}, val)
		for _, proto := range dangerousProtocols {
			if strings.HasPrefix(val, proto) {
				return fmt.Errorf("disallowed HTML content")
			}
		}
	}

	return nil
}
