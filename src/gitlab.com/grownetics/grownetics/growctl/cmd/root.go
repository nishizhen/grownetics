package cmd

import (
    "bufio"
    "fmt"
    "io/ioutil"
    "log"
    "os"
    "strings"

    "growutil"

    homedir "github.com/mitchellh/go-homedir"
    "github.com/spf13/cobra"
    "github.com/spf13/viper"
)

var cfgFile string

var version = "1.2.2"

var returnJson bool

// RootCmd represents the base command when called without any subcommands
var RootCmd = &cobra.Command{
    Use:   "growctl",
    Short: "A tool to manage Grownetics environments",
    Long: `This tool provides easy access to many common actions
performed when working with Grownetics servers`,
}

// Execute adds all child commands to the root command and sets flags appropriately.
// This is called by main.main(). It only needs to happen once to the rootCmd.
func Execute() {
    if err := RootCmd.Execute(); err != nil {
        fmt.Println(err)
        os.Exit(1)
    }
}

func init() {
    cobra.OnInitialize(initConfig)

    RootCmd.PersistentFlags().BoolVarP(&returnJson, "json", "j", false, "Return output in json format")
    RootCmd.PersistentFlags().StringVar(&cfgFile, "config", "", "config file (default is $HOME/.growctl.yaml)")
}

func getVersion() string {
    file, err := os.Open(viper.GetString("RepoPath")+"/src/gitlab.com/grownetics/grownetics/growctl/cmd/root.go")
    if err != nil {
        if !returnJson {
            fmt.Println(err)
        }
    } else {
        scanner := bufio.NewScanner(file)

        line := 1
        // https://golang.org/pkg/bufio/#Scanner.Scan
        for scanner.Scan() {
            if strings.Contains(scanner.Text(), "var version") {
                return scanner.Text()
            }

            line++
        }
    }
    return version

}

func check(e error) {
    if e != nil {
        panic(e)
    }
}

// initConfig reads in config file and ENV variables if set.
func initConfig() {
    if !returnJson {
        // Output Green
        fmt.Println("\x1b[92;1m")
        fmt.Println("🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 🌱 ")
        fmt.Println("===================================================================================")
        fmt.Println(`
    [38;5;199m [0m[38;5;199m [0m[38;5;199m [0m[38;5;199m [0m[38;5;199m [0m[38;5;199m [0m[38;5;163m [0m[38;5;163m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;128m [0m[38;5;128m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;99m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;69m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39mo[0m[38;5;39m8[0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;38mo[0m[38;5;38m8[0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m [0m[38;5;44m [0m
    [38;5;199m [0m[38;5;199m [0m[38;5;199mo[0m[38;5;163mo[0m[38;5;163mo[0m[38;5;164mo[0m[38;5;164mo[0m[38;5;164mo[0m[38;5;164mo[0m[38;5;164mo[0m[38;5;164m8[0m[38;5;164m [0m[38;5;164mo[0m[38;5;164mo[0m[38;5;164m [0m[38;5;128mo[0m[38;5;128mo[0m[38;5;129mo[0m[38;5;129mo[0m[38;5;129mo[0m[38;5;129mo[0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;93mo[0m[38;5;93mo[0m[38;5;93mo[0m[38;5;93mo[0m[38;5;93mo[0m[38;5;93mo[0m[38;5;93mo[0m[38;5;93m [0m[38;5;93m [0m[38;5;99m [0m[38;5;63mo[0m[38;5;63mo[0m[38;5;63mo[0m[38;5;63mo[0m[38;5;63m [0m[38;5;63m [0m[38;5;63mo[0m[38;5;63m [0m[38;5;63m [0m[38;5;63mo[0m[38;5;63mo[0m[38;5;69mo[0m[38;5;33mo[0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33mo[0m[38;5;33mo[0m[38;5;33mo[0m[38;5;33mo[0m[38;5;33mo[0m[38;5;39mo[0m[38;5;39mo[0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39mo[0m[38;5;39m8[0m[38;5;39m8[0m[38;5;39m8[0m[38;5;38mo[0m[38;5;38mo[0m[38;5;44m [0m[38;5;44m [0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m [0m[38;5;44m [0m
    [38;5;163m8[0m[38;5;163m8[0m[38;5;164m8[0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;164m8[0m[38;5;164m8[0m[38;5;164mo[0m[38;5;164m [0m[38;5;164m [0m[38;5;128m [0m[38;5;128m8[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;93m [0m[38;5;93m8[0m[38;5;93m8[0m[38;5;93m8[0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;99m8[0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m [0m[38;5;63m [0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m [0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m8[0m[38;5;69m [0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m [0m[38;5;33m [0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m8[0m[38;5;39m8[0m[38;5;39m8[0m[38;5;39m [0m[38;5;39m [0m[38;5;38m8[0m[38;5;38m8[0m[38;5;44m8[0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m [0m[38;5;44m [0m
    [38;5;164m [0m[38;5;164m8[0m[38;5;164m8[0m[38;5;164m8[0m[38;5;164mo[0m[38;5;164mo[0m[38;5;164m8[0m[38;5;164m8[0m[38;5;164m8[0m[38;5;128mo[0m[38;5;128m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m8[0m[38;5;93m8[0m[38;5;93m8[0m[38;5;93m [0m[38;5;93m [0m[38;5;99m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m8[0m[38;5;69m8[0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m8[0m[38;5;39m8[0m[38;5;39m8[0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;38m [0m[38;5;38m [0m[38;5;44m [0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m8[0m[38;5;44m8[0m[38;5;43m8[0m[38;5;49m [0m[38;5;49m [0m
    [38;5;164m8[0m[38;5;164m8[0m[38;5;164m8[0m[38;5;164m [0m[38;5;164m [0m[38;5;164m [0m[38;5;128m [0m[38;5;128m [0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m [0m[38;5;129mo[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129mo[0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;99m8[0m[38;5;63m8[0m[38;5;63mo[0m[38;5;63mo[0m[38;5;63mo[0m[38;5;63m8[0m[38;5;63m8[0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;69m [0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m8[0m[38;5;33m8[0m[38;5;33m [0m[38;5;33m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m8[0m[38;5;39m8[0m[38;5;39mo[0m[38;5;39mo[0m[38;5;39mo[0m[38;5;38m8[0m[38;5;38m8[0m[38;5;44m8[0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44m8[0m[38;5;44mo[0m[38;5;44m [0m[38;5;43mo[0m[38;5;49m8[0m[38;5;49m8[0m[38;5;49m8[0m[38;5;49mo[0m[38;5;49m [0m
    [38;5;164m [0m[38;5;164m8[0m[38;5;164m8[0m[38;5;128m8[0m[38;5;128mo[0m[38;5;129mo[0m[38;5;129mo[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m8[0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;129m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;93m [0m[38;5;99m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;63m [0m[38;5;69m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;33m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;39m [0m[38;5;38m [0m[38;5;38m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;44m [0m[38;5;43m [0m[38;5;49m [0m[38;5;49m [0m[38;5;49m [0m[38;5;49m [0m[38;5;49m [0m[38;5;49m [0m[38;5;49m [0m[38;5;49m [0m
    `)
        fmt.Println("\x1b[92;1m===================================================================================")
        fmt.Println()
    }

    if cfgFile != "" {
        // Use config file from the flag.
        viper.SetConfigFile(cfgFile)
    } else {
        // Find home directory.
        home, err := homedir.Dir()
        if err != nil {
            fmt.Println(err)
            os.Exit(1)
        }

        // Search config in home directory with name ".growctl" (without extension).
        viper.AddConfigPath(home)
        viper.SetConfigName(".growctl")
    }

    viper.AutomaticEnv() // read in environment variables that match

    // If a config file is found, read it in.
    if err := viper.ReadInConfig(); err == nil {
        if !returnJson {
            fmt.Println("✔ Using config file: " + growutil.Highlight(viper.ConfigFileUsed()))
            fmt.Println("\x1b[92;1m")
        }
    } else {
        if !returnJson {
            fmt.Println("No config file found! Enter the path to your Grownetics source directory\n(example: /Users/nickgrownetics/Code/Grownetics/")
            var response string
            _, err := fmt.Scanln(&response)
            if err != nil {
             log.Fatal(err)
            }
            
            home, err := homedir.Dir()
            d1 := []byte("RepoPath: "+response+"\n")
            err = ioutil.WriteFile(home+"/.growctl.yaml", d1, 0644)
            check(err)
            fmt.Println(fmt.Sprintf("\nConfig written successfully to %s/.growctl.yaml Please run command again.",home))
            os.Exit(1)
        }
    }

    new_version := getVersion()
    new_version = strings.TrimLeft(strings.TrimRight(new_version,"\""),"var version = \"")

    if strings.Compare(new_version,version) != 0 && !returnJson {
        fmt.Println()
        fmt.Println(growutil.Red("Update Available! Run ")+growutil.Highlight("$ growctl update"))
        // Reset green color
        fmt.Println("\x1b[92;1m")
    }

}
