package cmd

import (
	"fmt"

	"github.com/spf13/cobra"
)

var testCmd = &cobra.Command{
	Use:   "test",
	Short: "Run test suites",
	Long: `By default all test suites are executed.

To run only a specific suite pass '--testsuite FrontEnd'.
You can pass it any PHPunit parameters.`,
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println("run both")
		testUpCmd.Run(cmd, args)
		testRunCmd.Run(cmd, args)
	},
}

func init() {
	RootCmd.AddCommand(testCmd)

}
