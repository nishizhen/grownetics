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

var downCmd = &cobra.Command{
	Use:   "down",
	Short: "Spins down the docker stack.",
	Long: `A longer description that spans multiple lines and likely contains examples
and usage of using your command. For example:

Cobra is a CLI library for Go that empowers applications.
This application is a tool to generate the needed files
to quickly create a Cobra application.`,
	Run: func(cmd *cobra.Command, args []string) {
		project, err := docker.NewProject(&ctx.Context{
			Context: project.Context{
				ComposeFiles: []string{
					viper.GetString("RepoPath") + "Server/docker-compose.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.override.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.faker.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.selenium.yml",
                    viper.GetString("RepoPath") + "Server/docker-compose.selenium.override.yml",
				},
				ProjectName:  "growserver",
			},
		}, nil)

		if err != nil {
			log.Fatal(err)
		}

		fmt.Println(growutil.Highlight("Clearing existing dev environment..."))
		err = project.Down(context.Background(), options.Down{})

		if err != nil {
			log.Fatal(err)
		}

		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(downCmd)
}
