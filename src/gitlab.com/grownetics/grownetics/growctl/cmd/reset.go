package cmd

import (
	"fmt"
	"os"

	"growutil"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"
)

var resetCmd = &cobra.Command{
	Use:   "reset",
	Short: "Resets development databases",
	Long: `This deletes the data/ folder that docker uses to store DB state.`,
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println(growutil.Highlight("Resetting all container volumes"))
		os.RemoveAll(viper.GetString("RepoPath") + "Server/data/")
		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(resetCmd)

}
