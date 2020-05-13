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

var seedCmd = &cobra.Command{
	Use:   "seed",
	Short: "Run migrations and user seeds",
	Long: `Run migrations and user seeds`,
	Run: func(cmd *cobra.Command, args []string) {
		project, err := docker.NewProject(&ctx.Context{
			Context: project.Context{
				ComposeFiles: []string{
					viper.GetString("RepoPath") + "Server/docker-compose.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.override.yml",
				},
				ProjectName:  "growserver",
			},
		}, nil)

		if err != nil {
			log.Fatal(err)
		}

		fmt.Println(growutil.Highlight("Running seeds..."))
		_, err = project.Run(context.Background(), "growdash", []string{"/var/www/html/seed.sh"}, options.Run{})

		if err != nil {
			log.Fatal(err)
		}

		_, err = project.Run(context.Background(), "grafana", []string{"/var/www/html/grafana.sh"}, options.Run{})

		if err != nil {
			log.Fatal(err)
		}

		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(seedCmd)
}
