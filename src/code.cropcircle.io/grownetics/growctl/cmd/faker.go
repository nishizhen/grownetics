package cmd

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"math/rand"
	"net/http"
	"os"
	"strconv"
	"text/tabwriter"
	"time"

	"growutil"

	"github.com/fatih/color"
	"github.com/spf13/cobra"

	//"github.com/spf13/viper"

	"github.com/dariubs/percent"
	"gopkg.in/cheggaaa/pb.v1"

	consulapi "github.com/hashicorp/consul/api"

	influxapi "github.com/influxdata/influxdb1-client/v2"
)

var deviceCount int
var deviceIdStart int

var pbs []*pb.ProgressBar

var loops int
var host string
var consulHost string
var influxHost string
var refreshRate int
var showDebug bool
var showDeviceTable bool

var humidityUpperBound int
var humidityLowerBound int
var tempUpperBound int
var tempLowerBound int
var co2UpperBound int
var co2LowerBound int
var parUpperBound int
var parLowerBound int

var fastSpeed int
var slowSpeed int

type Device struct {
	id            int
	HumHi         float64
	HumLo         float64
	TempHi        float64
	TempLo        float64
	Co2           int
	Par           int
	TotalRequests int
	FastRequests  int
	SlowRequests  int
}

type Results struct {
	TotalRequests int
	FastRequests  int
	SlowRequests  int
	TotalTime     float64
	ActualTime    float64
	Fastest       float64
	Slowest       float64
}

func deviceRun(host string, device Device, ch chan<- Device, results *Results, kv *consulapi.KV, influxClient influxapi.Client) {
	if loops > 0 {
		for i := 0; i < loops; i++ {
			deviceTick(host, &device, ch, results, kv, influxClient)
		}
	} else {
		for {
			deviceTick(host, &device, ch, results, kv, influxClient)
		}
	}
	ch <- device
}

func deviceTick(host string, device *Device, ch chan<- Device, results *Results, kv *consulapi.KV, influxClient influxapi.Client) {
	start := time.Now()
	redAlert := color.New(color.FgWhite, color.BgRed, color.Bold).SprintFunc()
	pair, _, _ := kv.Get("faker/devices/"+strconv.Itoa(device.id)+"/mode", nil)
	//if err != nil {
	//	panic(err)
	//}
	var mode int
	if pair != nil {
		mode, _ = strconv.Atoi(string(pair.Value))
	} else {
		// Look for Global settings
		pair, _, err := kv.Get("faker/devices/0/mode", nil)
		if err != nil {
			// panic(err)
		}
		if pair != nil {
			mode, _ = strconv.Atoi(string(pair.Value))
		} else {
			// Default to Demo mode
			mode = 7
		}
	}

	makeRequest := true

	switch mode {
	case 2:
		device.HumHi = RandomFloat(float64(humidityLowerBound), float64(humidityUpperBound))
		device.TempLo = RandomFloat(float64(tempLowerBound), float64(tempUpperBound))
		device.TempHi = RandomFloat(float64(tempLowerBound), float64(tempUpperBound))
		device.Co2 = RandomInt(co2LowerBound, co2UpperBound)
		device.Par = RandomInt(parLowerBound, parUpperBound)
	case 3:
		device.HumLo += RandomFloat(-1, 1)
		device.HumHi += RandomFloat(-1, 1)
		device.TempLo += RandomFloat(-1, 1)
		device.TempHi += RandomFloat(-1, 1)
		device.Co2 += RandomInt(-50, 50)
		device.Par += RandomInt(parLowerBound, parUpperBound)
	case 4:
		device.HumLo += RandomFloat(-1, 1)
		device.HumHi += RandomFloat(-1, 1)
		device.TempLo += RandomFloat(0, 1)
		device.TempHi += RandomFloat(0, 1)
		device.Co2 += RandomInt(-50, 50)
		device.Par += RandomInt(parLowerBound, parUpperBound)
	case 5:
		device.HumLo += RandomFloat(-1, 1)
		device.HumHi += RandomFloat(-1, 1)
		device.TempLo += RandomFloat(-1, 0)
		device.TempHi += RandomFloat(-1, 0)
		device.Co2 += RandomInt(-50, 50)
		device.Par += RandomInt(parLowerBound, parUpperBound)
	case 6:
		makeRequest = false
	case 7:
		device.HumLo += RandomFloat(-1, 1)
		if device.HumLo > float64(humidityUpperBound) {
			device.HumLo = float64(humidityUpperBound)
		} else if device.HumLo < float64(humidityLowerBound) {
			device.HumLo = float64(humidityLowerBound)
		}

		device.HumHi += RandomFloat(-1, 1)
		if device.HumHi > float64(humidityUpperBound) {
			device.HumHi = float64(humidityUpperBound)
		} else if device.HumHi < float64(humidityLowerBound) {
			device.HumHi = float64(humidityLowerBound)
		}

		device.TempLo += RandomFloat(-1, 1)
		if device.TempLo > float64(tempUpperBound) {
			device.TempLo = float64(tempUpperBound)
		} else if device.TempLo < float64(tempLowerBound) {
			device.TempLo = float64(tempLowerBound)
		}

		device.TempHi += RandomFloat(-1, 1)
		if device.TempHi > float64(tempUpperBound) {
			device.TempHi = float64(tempUpperBound)
		} else if device.TempHi < float64(tempLowerBound) {
			device.TempHi = float64(tempLowerBound)
		}

		device.Co2 += RandomInt(-50, 50)
		if float64(device.Co2) > float64(co2UpperBound) {
			device.Co2 = co2UpperBound
		} else if float64(device.Co2) < float64(co2LowerBound) {
			device.Co2 = co2LowerBound
		}

		device.Par += RandomInt(-50, 50)
		if float64(device.Par) > float64(parUpperBound) {
			device.Par = parUpperBound
		} else if float64(device.Par) < float64(parLowerBound) {
			device.Par = parLowerBound
		}

	case 8:

		// 20% chance to not send any data
		if rand.Intn(100) < 50 {
			device.HumLo += RandomFloat(-1, 1)
			device.HumHi += RandomFloat(-1, 1)
			device.TempLo += RandomFloat(-1, 1)
			device.TempHi += RandomFloat(-1, 1)
			device.Co2 += RandomInt(-50, 50)
			device.Par += RandomInt(parLowerBound, parUpperBound)
		} else {
			makeRequest = false
		}
	}

	// Reset colors

	// Make actual request
	if makeRequest != false {
		data := fmt.Sprintf("[M1:%.2f-%.2f],[M2:%.2f-%.2f],[M3:%d],[A0:%d]", device.HumLo, device.TempLo, device.HumHi, device.TempHi, device.Co2, device.Par)
		resp, err := http.Get("http://" + host + "/api/raw?q={\"id\":" + strconv.Itoa(device.id) + ",\"v\":\"1.0.13\",\"st\":1,\"m\":2974,\"d\":\"" + data + "\"}")
		if err != nil {
			fmt.Sprintf("%s", err)
			fmt.Println(redAlert("host unreachable!"))
			return
		}
		results.TotalRequests++
		device.TotalRequests++
		body, err := ioutil.ReadAll(resp.Body)

		// Adding .5 to fix rounding in Go.
		secs := int(time.Since(start).Seconds() + 0.5)

		switch {
		case secs < fastSpeed:
			device.FastRequests++
			results.FastRequests++
		case secs > slowSpeed:
			device.SlowRequests++
			results.SlowRequests++
		}

		if showDebug {
			var seconds string

			switch {
			case secs < fastSpeed:
				seconds = growutil.Green(fmt.Sprintf("%d", secs))
			case secs < slowSpeed:
				seconds = growutil.Yellow(fmt.Sprintf("%d", secs))
			default:
				seconds = growutil.RedAlert(fmt.Sprintf("%d", secs))
			}

			fmt.Println(fmt.Sprintf(
				"Elapsed: %s s Response Length: %s Device: %s Data: %s",
				seconds,
				growutil.Highlight(fmt.Sprintf("%d", len(body))),
				growutil.Highlight(fmt.Sprintf("%d", device.id)),
				growutil.Highlight(data)),
			)
		}
		elapsed := time.Since(start).Seconds()
		results.TotalTime += elapsed
		if results.Slowest < elapsed {
			results.Slowest = elapsed
		}
		if results.Fastest > elapsed {
			results.Fastest = elapsed
		}

		// Record requset to Consul.
		p := &consulapi.KVPair{Key: "faker/devices/" + strconv.Itoa(device.id) + "/stats/requests", Value: []byte(strconv.Itoa(device.TotalRequests))}
		_, _ = kv.Put(p, nil)

		// Record requset to InfluxDB
		bp, _ := influxapi.NewBatchPoints(influxapi.BatchPointsConfig{
			Database:  "faker_data",
			Precision: "s",
		})
		// Create a point and add to batch
		fields := map[string]interface{}{
			"value": elapsed,
		}

		tags := map[string]string{
			"device_id": strconv.Itoa(device.id),
		}

		pt, _ := influxapi.NewPoint("response_time", tags, fields)

		bp.AddPoint(pt)

		influxClient.Write(bp)
	}
	time.Sleep(time.Duration(refreshRate) * time.Second)
}

func MakeRange(min, max int) []int {
	a := make([]int, max-min+1)
	for i := range a {
		a[i] = min + i
	}
	return a
}

func RandomInt(min, max int) int {
	return rand.Intn(max-min) + min
}

func RandomFloat(min, max float64) float64 {
	return rand.Float64()*(max-min) + min
}

var fakerCmd = &cobra.Command{
	Use:   "faker",
	Short: "Simulate loads / populate fake data into GrowServer instances",
	Long: `Tool to simulate 3D Crop Sensor devices.

Usage Example: growctl faker -l 2 -d 69 --host cloud.demo.production.cloudforest.io --json | jq .TotalRequests`,
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println("Gathering config settings..")

		rand.Seed(time.Now().UTC().UnixNano())

		config := consulapi.DefaultConfig()
		config.Address = consulHost
		client, err := consulapi.NewClient(config)
		if err != nil {
			panic(err)
		}

		kv := client.KV()

		if !returnJson {
			fmt.Println(fmt.Sprintf("host: %s", growutil.Highlight(host)))
			fmt.Println(fmt.Sprintf("Devices: %s Refresh Rate: %ss", growutil.Highlight(fmt.Sprintf("%d", deviceCount)), growutil.Highlight(fmt.Sprintf("%d", refreshRate))))
			fmt.Println(fmt.Sprintf("Fast Threshold: %ss Slow Threshold: %ss", growutil.Highlight(fmt.Sprintf("%d", fastSpeed)), growutil.Highlight(fmt.Sprintf("%d", slowSpeed))))
			fmt.Println(fmt.Sprintf("Loops: %s", growutil.Highlight(fmt.Sprintf("%d", loops))))
		}

		if showDebug {
			fmt.Println(" Printing verbose output.")
		}

		var results = Results{
			// Set this high so the code has something to compare against other than 0
			Fastest: 1000,
		}

		start := time.Now()

		if deviceCount < 0 {
			resp, err := http.Get("http://" + host + "/devices/count")
			if err != nil {
				panic(err)
			}
			defer resp.Body.Close()
			body, err := ioutil.ReadAll(resp.Body)

			deviceCount, err = strconv.Atoi(string(body))
		}

		deviceIds := MakeRange(deviceIdStart, deviceCount+deviceIdStart-1)
		ch := make(chan Device)
		devices := make([]Device, deviceCount+deviceIdStart)
		pbs = make([]*pb.ProgressBar, deviceCount+deviceIdStart)

		influxClient, err := influxapi.NewHTTPClient(influxapi.HTTPConfig{
			Addr: "http://" + influxHost,
		})

		for _, deviceID := range deviceIds {
			fmt.Println(deviceID)
			devices[deviceID] = Device{
				id:     deviceID,
				HumLo:  RandomFloat(float64(humidityLowerBound), float64(humidityUpperBound)),
				HumHi:  RandomFloat(float64(humidityLowerBound), float64(humidityUpperBound)),
				TempLo: RandomFloat(float64(tempLowerBound), float64(tempUpperBound)),
				TempHi: RandomFloat(float64(tempLowerBound), float64(tempUpperBound)),
				Co2:    RandomInt(co2LowerBound, co2UpperBound),
				Par:    RandomInt(parLowerBound, parUpperBound),
			}
			pbs[deviceID] = pb.New(loops).Prefix("Device " + fmt.Sprintf("%d", deviceID))
		}

		var pool *pb.Pool

		if !returnJson {
			if err != nil {
				panic(err)
			}
		}

		for _, device := range devices {
			if device.id > 0 {
				go deviceRun(host, device, ch, &results, kv, influxClient)
			}
		}

		for ii, _ := range devices {
			devices[ii] = <-ch
		}

		results.ActualTime = time.Since(start).Seconds()

		if returnJson {
			data, _ := json.Marshal(results)
			fmt.Printf("%s\n", data)
		} else {
			pool.Stop()

			w := tabwriter.NewWriter(os.Stdout, 0, 0, 3, ' ', tabwriter.AlignRight|tabwriter.Debug)

			if showDeviceTable {
				fmt.Fprintln(w, fmt.Sprintf("%s \t %s \t %s \t %s \t", growutil.Highlight("Device"), growutil.Highlight("Requests"), growutil.Green("Fast"), growutil.Red("Slow")))
				fmt.Println(growutil.Green("======================================================================="))
				for _, device := range devices {
					fast := fmt.Sprint(percent.PercentOf(device.FastRequests, device.TotalRequests))
					slow := fmt.Sprint(percent.PercentOf(device.SlowRequests, device.TotalRequests))
					fmt.Fprintln(w, fmt.Sprintf("%s \t %s \t %s \t %s \t", growutil.Highlight(fmt.Sprintf("%d", device.id)), growutil.Highlight(fmt.Sprintf("%d", device.TotalRequests)), growutil.Green(fast)+"%", growutil.Red(slow)+"%"))
				}
				w.Flush()
			}

			fmt.Fprintln(w, fmt.Sprintf("%s \t %s \t %s \t", growutil.Highlight("Total Requests"), growutil.Green("Fast"), growutil.Red("Slow")))
			fmt.Println(growutil.Green("======================================================================="))
			fmt.Println()
			fast := fmt.Sprint(percent.PercentOf(results.FastRequests, results.TotalRequests))
			slow := fmt.Sprint(percent.PercentOf(results.SlowRequests, results.TotalRequests))
			fmt.Fprintln(w, fmt.Sprintf("%s \t %s \t %s \t", growutil.Highlight(fmt.Sprintf("%d", results.TotalRequests)), growutil.Green(fast)+"%", growutil.Red(slow)+"%"))
			w.Flush()

			fmt.Printf(" %s average request time\n", growutil.Highlight(fmt.Sprintf("%.1fs", results.TotalTime/float64(results.TotalRequests))))
			fmt.Printf(" %s fastest request\n", growutil.Green(fmt.Sprintf("%.1fs", results.Fastest)))
			fmt.Printf(" %s slowest request\n", growutil.Red(fmt.Sprintf("%.1fs", results.Slowest)))
			fmt.Printf(" %s elapsed\n\n", growutil.Highlight(fmt.Sprintf("%.1fs", results.ActualTime)))
			fmt.Println(growutil.Green("======================================================================="))
		}
	},
}

func init() {
	RootCmd.AddCommand(fakerCmd)

	fakerCmd.Flags().IntVarP(&deviceCount, "devices", "d", -1, "Number of devices to simulate")
	fakerCmd.Flags().IntVarP(&deviceIdStart, "device_id_start", "", 1, "ID of the device to start counting up from.")
	fakerCmd.Flags().IntVarP(&loops, "loops", "l", -1, "Number of times to loop (-1 is forever)")
	fakerCmd.Flags().StringVarP(&host, "host", "", "localhost:81", "host to point at")
	fakerCmd.Flags().StringVarP(&consulHost, "consul_host", "c", "localhost:8500", "Consul host to use")
	fakerCmd.Flags().StringVarP(&influxHost, "influx_host", "i", "localhost:8086", "Influxdb host to use")
	fakerCmd.Flags().IntVarP(&refreshRate, "refresh", "r", 20, "Number of seconds to wait between device updates")
	fakerCmd.Flags().BoolVarP(&showDebug, "debug", "", false, "Display debug information")
	fakerCmd.Flags().IntVarP(&fastSpeed, "fast_seed", "", 4, "Seconds that a request needs to complete within to be considered 'fast'")
	fakerCmd.Flags().IntVarP(&slowSpeed, "slow_speed", "", 8, "Seconds after which a request should be considered 'slow'")
	fakerCmd.Flags().BoolVarP(&showDeviceTable, "show_device_table", "", false, "Display device specific information")
	
	fakerCmd.Flags().IntVarP(&humidityUpperBound, "humidity_upper_bound", "", 60, "Humidity upper boundary")
	fakerCmd.Flags().IntVarP(&humidityLowerBound, "humidity_lower_bound", "", 50, "Humidity lower boundary")
	fakerCmd.Flags().IntVarP(&tempUpperBound, "temp_upper_bound", "", 25, "Temp upper boundary")
	fakerCmd.Flags().IntVarP(&tempLowerBound, "temp_lower_bound", "", 20, "Temp lower boundary")
	fakerCmd.Flags().IntVarP(&co2UpperBound, "co2_upper_bound", "", 1200, "Co2 upper boundary")
	fakerCmd.Flags().IntVarP(&co2LowerBound, "co2_lower_bound", "", 1000, "Co2 lower boundary")
	fakerCmd.Flags().IntVarP(&parUpperBound, "par_upper_bound", "", 200, "Par upper boundary")
	fakerCmd.Flags().IntVarP(&parLowerBound, "par_lower_bound", "", 0, "Par lower boundary")
}
