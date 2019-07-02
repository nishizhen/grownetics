package growutil

import (
	"fmt"
)

func Done () {
	fmt.Println(Highlight("==================================================================================="))
	fmt.Println(Highlight("                                   All done! ")+Green("✔"))
	fmt.Println(Highlight("==================================================================================="))
}