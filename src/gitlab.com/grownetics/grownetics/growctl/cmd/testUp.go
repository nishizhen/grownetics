package cmd

import (
	"fmt"

	"log"

	"growutil"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"

	"golang.org/x/net/context"
	"github.com/docker/libcompose/docker"
	"github.com/docker/libcompose/docker/ctx"
	"github.com/docker/libcompose/project"
	"github.com/docker/libcompose/project/options"

)

// upCmd represents the up command
var testUpCmd = &cobra.Command{
	Use:   "up",
	Short: "Spin up a local docker testing environment",
	Long: `Spin up a local docker testing environment`,
	Run: func(cmd *cobra.Command, args []string) {
		project, err := docker.NewProject(&ctx.Context{
			Context: project.Context{
				ComposeFiles: []string{
					viper.GetString("RepoPath") + "Server/docker-compose.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.override.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.selenium.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.selenium.override.yml",
				},
				ProjectName:  "growserver",
			},
		}, nil)

		if err != nil {
			log.Fatal(err)
		}

		fmt.Println(growutil.Highlight("Clearing existing test environment..."))
		err = project.Down(context.Background(), options.Down{})

		if err != nil {
			log.Fatal(err)
		}

		fmt.Println(growutil.Highlight("Spinning up test environment..."))
		err = project.Up(context.Background(), options.Up{})

		if err != nil {
			log.Fatal(err)
		}
		growutil.Done()
	},
}

func init() {
	testCmd.AddCommand(testUpCmd)
}
