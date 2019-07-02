// Copyright © 2017 NAME HERE <EMAIL ADDRESS>
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

package cmd

import (
	"fmt"
	"log"
	"os/exec"
	"time"

	"growutil"

	"github.com/spf13/cobra"
	"github.com/briandowns/spinner"

)

var api bool
var load_url string

// loadCmd represents the load command
var loadCmd = &cobra.Command{
	Use:   "load",
	Short: "Generate load against a target url",
	Long: `Generates load against a target url. With the --api flag can simulate device load.`,
	Run: func(cmd *cobra.Command, args []string) {
		if api {
			load_url = load_url + "api/raw?q={%22id%22:1,%22v%22:%221.0.13%22,%22st%22:1,%22m%22:2974,%22d%22:%22[M1:76.12-84.3],[M2:84.89-81.45],[M3:933]%22}"
		}

		fmt.Println(growutil.Highlight(fmt.Sprintf("Generating load against %s",load_url)))
		fmt.Println()

		s := spinner.New(spinner.CharSets[growutil.SpinnerChar], 100*time.Millisecond)  // Build our new spinner

		s.Prefix = growutil.Green("Hammering server: ")
		s.Color("green")
		s.Start()                                                    // Start the spinner

		_, err := exec.Command("go","get","-u","github.com/rakyll/hey" ).Output()
		if err != nil {
			log.Fatal(err)
		}
		out, err := exec.Command( "hey", load_url ).Output()

		s.Stop()

		fmt.Println()
		fmt.Printf("Output: %s\n", out)
		fmt.Println()
		fmt.Println(growutil.Highlight("Load run completed."))
	},
}

func init() {
	RootCmd.AddCommand(loadCmd)

	loadCmd.Flags().BoolVarP(&api, "api", "a", false, "Hit the api with sensor data")
	loadCmd.Flags().StringVarP(&load_url, "url", "u", "http://localhost/", "Url to target")
}
