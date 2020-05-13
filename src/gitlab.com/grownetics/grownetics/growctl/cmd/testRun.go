package cmd

import (
	"fmt"

	"log"
	"strings"

	"growutil"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"

	"golang.org/x/net/context"
	"github.com/docker/libcompose/docker"
	"github.com/docker/libcompose/docker/ctx"
	"github.com/docker/libcompose/project"
	"github.com/docker/libcompose/project/options"

)

var test_suite string
var filter string

var testRunCmd = &cobra.Command{
	Use:   "run",
	Short: "A brief description of your command",
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
					viper.GetString("RepoPath") + "Server/docker-compose.selenium.yml",
					viper.GetString("RepoPath") + "Server/docker-compose.selenium.override.yml",
				},
				ProjectName:  "growserver",
			},
		}, nil)

		if err != nil {
			log.Fatal(err)
		}

		var test_params string
		if strings.Compare(test_suite,"") != 0 {
			test_params = " --testsuite " + test_suite
		}
		if strings.Compare(filter,"") != 0 {
            test_params = " --filter " + filter
        }

		fmt.Println(growutil.Highlight("Running tests..."))
		_, err = project.Run(context.Background(), "phpunit", []string{ "/var/www/html/test.sh", test_params }, options.Run{})

		if err != nil {
			log.Fatal(err)
		}

		growutil.Done()
	},
}

func init() {
	testCmd.AddCommand(testRunCmd)
	testRunCmd.Flags().StringVarP(&test_suite, "testsuite", "t", "", "TestSuite to execute")
	testRunCmd.Flags().StringVarP(&filter, "filter", "f", "", "Filter tests")
}
