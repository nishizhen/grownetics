package cmd

import (
	"fmt"
	"github.com/spf13/cobra"
	"growutil"
)

var changelogCmd = &cobra.Command{
	Use:   "changelog",
	Short: "Update the changelog file for a new relesae.",
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println(growutil.Highlight("Run `growctl changelog --help` to see available changelog commands."))
	},
}

func init() {
	RootCmd.AddCommand(changelogCmd)
}
