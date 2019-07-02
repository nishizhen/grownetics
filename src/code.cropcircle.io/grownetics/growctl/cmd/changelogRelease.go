package cmd

import (
	"bufio"
	"fmt"
	"io/ioutil"
	"log"
	"os"
	"path/filepath"
	"strconv"
	"time"

	"gopkg.in/yaml.v2"

	"github.com/spf13/cobra"
	"github.com/spf13/viper"

	"growutil"
)

var newVersion string

type changelog struct {
	Title string `yaml:"title"`
	Type  string `yaml:"type"`
}

var changes []changelog

var changelogReleaseCmd = &cobra.Command{
	Use:   "release",
	Short: "Update the changelog file for a new relesae.",
	Run: func(cmd *cobra.Command, args []string) {
		// Require a version number
		fmt.Println("Generating changelog for release " + growutil.Highlight(newVersion) + "...")

		// Load the CHANGELOG.md for output

		oldChangelog, err := ioutil.ReadFile(viper.GetString("RepoPath") + "Server/CHANGELOG.md")
		if err != nil {
			log.Fatal(err)
		}

		file, err := os.Create(viper.GetString("RepoPath") + "Server/CHANGELOG.md")
		if err != nil {
			log.Fatal("Cannot create file", err)
		}
		defer file.Close()

		// Loop through all files in Changelogs

		searchDir := viper.GetString("RepoPath") + "Server/Changelogs"

		fileList := []string{}
		_ = filepath.Walk(searchDir, func(path string, f os.FileInfo, err error) error {
			fileList = append(fileList, path)
			return nil
		})

		for _, file := range fileList {
			var change changelog
			yamlFile, err := ioutil.ReadFile(file)
			if err == nil && filepath.Base(file) != ".gitkeep" {
				os.Remove(file)
				_ = yaml.Unmarshal(yamlFile, &change)
				fmt.Println("Got change: " + growutil.Highlight(change.Title))
				changes = append(changes, change)
			}
		}

		// Create a buffered writer from the file
		bufferedWriter := bufio.NewWriter(file)
		currentTime := time.Now().Local()
		bufferedWriter.WriteString("## " + newVersion + " (" + currentTime.Format("2006-01-02") + ")\n")

		// This could obviously be made better, for now, let's KISS
		var additions []changelog
		var fixed []changelog
		var changed []changelog
		var removed []changelog
		var security []changelog
		var other []changelog
		// Additions
		for ii := range changes {
			change := changes[ii]
			if change.Type == "Added" {
				additions = append(additions, change)
			}
			if change.Type == "Fixed" {
				fixed = append(fixed, change)
			}
			if change.Type == "Change" {
				fixed = append(changed, change)
			}
			if change.Type == "Removed" {
				removed = append(removed, change)
			}
			if change.Type == "Security" {
				security = append(security, change)
			}
			if change.Type == "Other" {
				other = append(other, change)
			}
		}
		if len(additions) > 0 {
			bufferedWriter.WriteString("\n### Additions (" + strconv.Itoa(len(additions)) + " changes)\n\n")
			for ii := range additions {
				addition := additions[ii]
				bufferedWriter.WriteString("- " + addition.Title + "\n")
			}
		}

		if len(fixed) > 0 {
			bufferedWriter.WriteString("\n### Fixes (" + strconv.Itoa(len(fixed)) + " changes)\n\n")
			for ii := range fixed {
				fixedChange := fixed[ii]
				bufferedWriter.WriteString("- " + fixedChange.Title + "\n")
			}
		}

		if len(changed) > 0 {
			bufferedWriter.WriteString("\n### Changes (" + strconv.Itoa(len(changed)) + " changes)\n\n")
			for ii := range changed {
				change := changed[ii]
				bufferedWriter.WriteString("- " + change.Title + "\n")
			}
		}

		if len(removed) > 0 {
			bufferedWriter.WriteString("\n### Removals (" + strconv.Itoa(len(removed)) + " changes)\n\n")
			for ii := range removed {
				removedChange := removed[ii]
				bufferedWriter.WriteString("- " + removedChange.Title + "\n")
			}
		}

		if len(security) > 0 {
			bufferedWriter.WriteString("\n### Security (" + strconv.Itoa(len(security)) + " changes)\n\n")
			for ii := range security {
				securityChange := security[ii]
				bufferedWriter.WriteString("- " + securityChange.Title + "\n")
			}
		}

		if len(other) > 0 {
			bufferedWriter.WriteString("\n### Other (" + strconv.Itoa(len(other)) + " changes)\n\n")
			for ii := range other {
				otherChange := other[ii]
				bufferedWriter.WriteString("- " + otherChange.Title + "\n")
			}
		}

		// One last line break to separate between earlier changelogs.
		bufferedWriter.WriteString("\n")
		bufferedWriter.Write(oldChangelog)

		// Write memory buffer to disk
		bufferedWriter.Flush()

		growutil.Done()
	},
}

func init() {
	changelogCmd.AddCommand(changelogReleaseCmd)

	changelogReleaseCmd.Flags().StringVarP(&newVersion, "version", "v", "", "Release version number (required)")
	changelogReleaseCmd.MarkFlagRequired("version")
}
