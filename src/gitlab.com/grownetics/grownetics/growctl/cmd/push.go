package cmd

import (
	"fmt"
	"log"
	"os/exec"

	"growutil"

	"github.com/spf13/cobra"
)

var pushCmd = &cobra.Command{
	Use:   "push",
	Short: "Push the docker images up to the registry.",
	Long: `A longer description that spans multiple lines and likely contains examples
and usage of using your command. For example:

Cobra is a CLI library for Go that empowers applications.
This application is a tool to generate the needed files
to quickly create a Cobra application.`,
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println(growutil.Highlight("Building docker images..."))
		fmt.Println()

		url := "code.cropcircle.io:4567/grownetics/grownetics/"
		images := []string{"php","growdash"}
		tag := "latest"

		for _, image := range images {
			build_target := url + image + ":" + tag

			fmt.Println(growutil.Highlight(fmt.Sprintf("Pushing image %s ...", build_target)))

			out, err := exec.Command("docker", "push", build_target ).Output()
			if err != nil {
				log.Fatal(err)
			}
			if false {
				fmt.Printf("%s\n", out)
			}
			fmt.Println(growutil.Highlight(fmt.Sprintf("Finished pushing image %s ", build_target)))
			fmt.Println()
		}

		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(pushCmd)

	// Here you will define your flags and configuration settings.

	// Cobra supports Persistent Flags which will work for this command
	// and all subcommands, e.g.:
	// pushCmd.PersistentFlags().String("foo", "", "A help for foo")

	// Cobra supports local flags which will only run when this command
	// is called directly, e.g.:
	// pushCmd.Flags().BoolP("toggle", "t", false, "Help message for toggle")
}
