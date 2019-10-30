package cmd

import (
	"fmt"
	"strings"

	"growutil"

	"github.com/spf13/cobra"
)


func init() {
	RootCmd.AddCommand(versionCmd)
}

var versionCmd = &cobra.Command{
	Use:   "version",
	Short: "Print the version number of growctl",
	Long:  `All software has versions. This is growctl's'`,
	Run: func(cmd *cobra.Command, args []string) {
		//fmt.Println()
		fmt.Println("Version:\n")
		fmt.Println(growutil.Highlight("growctl v"+version))
		fmt.Println("\x1b[92;1m")

		new_version := getVersion()
		new_version = strings.TrimLeft(strings.TrimRight(new_version,"\""),"var version = \"")

		if strings.Compare(new_version,version) == 0 {
			fmt.Println("You are on the latest version. ♥")
			fmt.Println()
		}
	},
}