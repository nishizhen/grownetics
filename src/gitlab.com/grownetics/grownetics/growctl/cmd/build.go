package cmd

import (
	"fmt"
	"log"
	"time"
	"os/exec"

	"growutil"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"
	"github.com/briandowns/spinner"

)

var show_build_output bool
var build_dev bool

var buildCmd = &cobra.Command{
	Use:   "build",
	Short: "Builds docker images",
	Long: `By default it builds the docker images needed for development.

You can pass in a specific image with '-d GrowDash' to just build one, or build an image other than the defaults.`,
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println(growutil.Highlight("Building docker images..."))
		fmt.Println()

		url := "grownetics/"
		images := []string{"php","growdash","nginx","growctl"}

        var dockerfile string
        var tag string

        if build_dev {
            dockerfile = "Dockerfile.dev"
		    tag = "dev"
        } else {
            dockerfile = "Dockerfile"
            tag = "latest"
        }

		for _, image := range images {
			dockerfile_path := viper.GetString("RepoPath")+"/Server/html/images/" + image + "/" + dockerfile
			build_target := url + image + ":" + tag

			s := spinner.New(spinner.CharSets[5], 100*time.Millisecond)  // Build our new spinner

			s.Prefix = growutil.Green(fmt.Sprintf("Building image %s ...", build_target))
			s.Color("green")
			s.Start()                                                    // Start the spinner


			out, err := exec.Command("docker", "build", viper.GetString("RepoPath")+"/Server/html", "-t", build_target, "-f", dockerfile_path).Output()

			if err != nil {
				fmt.Println("?")
				fmt.Printf("%s\n", err)
				log.Fatal(err)
			}
			s.Stop()
			if show_build_output {
				fmt.Printf("%s\n", out)
			}
			fmt.Println(growutil.Highlight(fmt.Sprintf("Finished building image %s ", build_target)))
			fmt.Println()
		}

		dockerfile_path := viper.GetString("RepoPath")+"/src/code.cropcircle.io/grownetics/growctl/Dockerfile"
		build_target := url + "growctl:" + tag

		s := spinner.New(spinner.CharSets[5], 100*time.Millisecond)  // Build our new spinner

		s.Prefix = growutil.Green(fmt.Sprintf("Building image %s ...", build_target))
		s.Color("green")
		s.Start()                                                    // Start the spinner

		out, err := exec.Command("docker", "build", viper.GetString("RepoPath")+"/src", "-t", build_target, "-f", dockerfile_path).Output()

		if err != nil {
			fmt.Println("?")
			fmt.Printf("%s\n", err)
			log.Fatal(err)
		}
		s.Stop()
		if show_build_output {
			fmt.Printf("%s\n", out)
		}

		growutil.Done()
	},
}

func init() {
	RootCmd.AddCommand(buildCmd)

	buildCmd.Flags().BoolVar(&show_build_output, "show_output", false, "Show build output")
	buildCmd.Flags().BoolVar(&build_dev, "build_dev", false, "Build dev images")
}
