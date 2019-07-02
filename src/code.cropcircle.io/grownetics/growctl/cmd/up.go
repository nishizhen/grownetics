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

var upCmdFaker bool
var upCmdDashonly bool
var upCmdRestart bool

// upCmd represents the up command
var upCmd = &cobra.Command{
	Use:   "up",
	Short: "Spin up docker environment",
	Long: `Spins up docker environments, by default for local development.

Can be used by CI servers and production deploys as well.

Farms out calls to docker-compose really.`,
	Run: func(cmd *cobra.Command, args []string) {

		var proj project.APIProject
		var err error
		if upCmdFaker {
			proj, err = docker.NewProject(&ctx.Context{
				Context: project.Context{
					ComposeFiles: []string{
						viper.GetString("RepoPath") + "Server/docker-compose.yml",
						viper.GetString("RepoPath") + "Server/docker-compose.faker.yml",
						viper.GetString("RepoPath") + "Server/docker-compose.override.yml",
					},
					ProjectName:  "growserver",
				},
			}, nil)
		} else if upCmdDashonly {
		    proj, err = docker.NewProject(&ctx.Context{
                Context: project.Context{
                    ComposeFiles: []string{
                        viper.GetString("RepoPath") + "Server/docker-compose.dashonly.yml",
                    },
                    ProjectName:  "growserver",
                },
            }, nil)
		} else {
			proj, err = docker.NewProject(&ctx.Context{
				Context: project.Context{
					ComposeFiles: []string{
						viper.GetString("RepoPath") + "Server/docker-compose.yml",
						viper.GetString("RepoPath") + "Server/docker-compose.override.yml",
					},
					ProjectName:  "growserver",
				},
			}, nil)
		}

		if err != nil {
			log.Fatal(err)
		}

		if (upCmdRestart) {
			fmt.Println(growutil.Highlight("Clearing existing dev environment..."))
			err = proj.Down(context.Background(), options.Down{})
		}
		
		fmt.Println(growutil.Highlight("Spinning up local dev environment..."))
		err = proj.Up(context.Background(), options.Up{})

		if err != nil {
			log.Fatal(err)
		}
		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(upCmd)

	upCmd.Flags().BoolVarP(&upCmdFaker, "faker", "f", false, "Whether to enable Faker or not.")
	upCmd.Flags().BoolVarP(&upCmdDashonly, "dashonly", "d", false, "Only spin up growdash, not the rest of the stack. Set to true to save battery/cpu.")
	upCmd.Flags().BoolVarP(&upCmdRestart, "restart", "r", false, "Restart containers.")
}
