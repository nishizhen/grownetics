// Package stringutil contains utility functions for working with strings.
package growutil

import (
	"fmt"

	"github.com/fatih/color"
)

// Reverse returns its argument string reversed rune-wise left to right.
func Highlight(s string) string {
	clr := color.New(color.FgCyan, color.Bold).SprintFunc()

	return fmt.Sprintf(clr(s))
}

func Green(s string) string {
	clr := color.New(color.FgGreen, color.Bold).SprintFunc()

	return fmt.Sprintf(clr(s))
}

func Yellow(s string) string {
	clr := color.New(color.FgYellow, color.Bold).SprintFunc()

	return fmt.Sprintf(clr(s))
}

func Red(s string) string {
	clr := color.New(color.FgRed, color.Bold).SprintFunc()

	return fmt.Sprintf(clr(s))
}

func RedAlert(s string) string {
	clr := color.New(color.BgRed, color.FgWhite, color.Bold).SprintFunc()

	return fmt.Sprintf(clr(s))
}