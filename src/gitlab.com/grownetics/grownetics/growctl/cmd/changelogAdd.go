package cmd

import (
	"bufio"
	"fmt"
	"io/ioutil"
	"os"
	"os/exec"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"

	"growutil"
)

var changelogAddCmd = &cobra.Command{
	Use:   "add",
	Short: "Add a new changelog entry.",
	Run: func(cmd *cobra.Command, args []string) {
		// Get the branch name.
		branch, _ := exec.Command("git", "symbolic-ref", "--short", "HEAD").Output()
		branchString := fmt.Sprintf("Creating a changelog for branch %s", branch)
		fmt.Println(growutil.Highlight(branchString))
		reader := bufio.NewReader(os.Stdin)
		var message string
		for len(message) < 5 {
			fmt.Print(growutil.Highlight("Enter changelog message: "))
			message, _ = reader.ReadString('\n')
		}

		var changelogType string
		for len(changelogType) < 2 {
			fmt.Print(growutil.Highlight("Select changelog type: \n\n1: Added\n2: Fixed\n3: Changed\n4: Removed\n5: Security\n6: Other\n\nType: "))
			changelogType, _ = reader.ReadString('\n')
		}

		var changelogTypeString string
		switch changelogType {
		case "1\n":
			changelogTypeString = "Added"
		case "2\n":
			changelogTypeString = "Fixed"
		case "3\n":
			changelogTypeString = "Changed"
		case "4\n":
			changelogTypeString = "Removed"
		case "5\n":
			changelogTypeString = "Security"
		case "6\n":
			changelogTypeString = "Other"
		}

		content := []byte(fmt.Sprintf("---\ntitle: %stype: %s", message, changelogTypeString))
		fileString := viper.GetString("RepoPath") + "Server/Changelogs/" + fmt.Sprintf("%s", branch) + ".md"
		err := ioutil.WriteFile(fileString, content, 0644)
		check(err)
		fmt.Println(growutil.Highlight("\nCreated "+fileString))
		growutil.Done()
	},
}

func init() {
	changelogCmd.AddCommand(changelogAddCmd)
}
