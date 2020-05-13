package cmd

import (
	"fmt"
	"log"
	"os/exec"

	"growutil"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"
)

// updateCmd represents the update command
var updateCmd = &cobra.Command{
	Use:   "update",
	Short: "Rebuilds and installs growctl from local source",
	Long: `Updates your /usr/local/bin/growctl based on the latest code in your repository.`,
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println(growutil.Highlight("Updating go dependencies"))

		_, err := exec.Command("go","get","code.cropcircle.io/grownetics/..." ).Output()
		if err != nil {
			fmt.Printf("Error: %s\n", err)
			log.Fatal(err)
		}

		fmt.Println(growutil.Highlight("Building and installing grow tools"))

		_, err = exec.Command("go","build","-o","growctl",viper.GetString("RepoPath")+"/src/code.cropcircle.io/grownetics/growctl/main.go" ).Output()
		if err != nil {
			fmt.Printf("Error: %s\n", err)
			log.Fatal(err)
		}
		_, err = exec.Command("mv","growctl","/usr/local/bin/").Output()
		if err != nil {
			fmt.Printf("Error: %s\n", err)
			log.Fatal(err)
		}


		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(updateCmd)

	// Here you will define your flags and configuration settings.

	// Cobra supports Persistent Flags which will work for this command
	// and all subcommands, e.g.:
	// updateCmd.PersistentFlags().String("foo", "", "A help for foo")

	// Cobra supports local flags which will only run when this command
	// is called directly, e.g.:
	// updateCmd.Flags().BoolP("toggle", "t", false, "Help message for toggle")
}
