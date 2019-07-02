# GrowCtl

GrowCtl is an internal utility that we use to perform a variety of development and testing tasks. It is a command line application written in [Go](http://golang.org/).

## Installing GrowCtl

### Quick Install

`cp bin/macos/growctl /usr/local/bin/`

### Building from Source

To use Go with our setup correctly, you need to add the root of the Grownetics repo to your GoPath. So if you have your repo checked out to ~/Code/Grownetics you need to first run `export GOPATH=$GOPATH:~/Code/Grownetics`

Now run `go get src/code.cropcircle.io/grownetics/growctl/` to download the necessary Go packages. This will take some time.

Then you can run `go run src/code.cropcircle.io/grownetics/growctl/main.go update` to install the code as an executable on your machine.

Or you can run `go build -o growctl src/code.cropcircle.io/grownetics/growctl/main.go && mv growctl /usr/local/bin/` to install 'by hand'.

## Commands

Every command listed below, and indeed growctl itself, can be passed a `--help` flag at the end to see more information about the command, flags that can be passed to it, etc.

### `growctl build`

Builds new versions of the docker base images based on the various Dockerfiles.

Pass it `--build_dev true` to build images ending in `:dev` rather than `:latest`, configured for local development. (PHP for example includes XDebug in the Development version.)

### `growctl changelog`

Pass this a version number when creating a new release. It will update the CHANGELOG.md file based off of the files in Changelogs/ then remove the files it imported.

When you commit the updated changelog and removed changelogs files, that commit will be tagged with the release number. Any new files going into Changelogs/ will be pulled in by the next release.

### `growctl down`

Spins down the docker stack.

### `growctl faker`

Creates virtual 'fake' 3D Crop Sensor devices, which hit the DeviceApi with data. The data can be controlled through the GrowDash interface on the /devices page. There are several modes available.

#### Flat

Returns unchanging data. It picks random initial values then doesn't change with each request.

#### Random

Every request returns completely new, random data.

#### Drift

Every request returns slightly different data than the last request, nudged in a random direction.

#### Heat

Every request raise the temperature, while the other sensor type values drift.

#### Cool

Every request lower the temperature, while the other sensor type values drift.

#### Dead

Does not make requests.

#### Demo

Drifts randomly within pre defined ranges.

#### Sketchy

20% chance to not send any data each request, values drift otherwise.

### `growctl faker` Examples

Simulate just DR power panel devices (120-122): `growctl faker -r 2 --device_id_start 120 -d 3`

### `growctl load`

Run a load test against the API. Can be used to help debug / fine tune performance.

### `growctl mattermost`

Start a Mattermost bot for the local stack.

### `growctl push`

Push the docker images up to the registry

### `growctl reset`

Clear your local development data to start with a fresh install.

### `growctl seed`

Run the default Seed scripts to populate the DB with starting data.

### `growctl test`

By default, spins up the test stack, and executes all tests.

#### `growctl test up`

Spins down, then spins up the test stack, doesn't execute any tests

#### `growctl test run`

Runs the tests, doesn't change the state of the stack.

To run just one test: `docker exec -it growserver_phpunit_1 /var/www/html/test.sh --filter DevicesTest`

### `growctl up`

Spins up a full local GrowServer development Docker stack.

#### `growctl up -d`

Spins up just the bare minimum needed for the dashboard to work.
Useful if you only need to make front-end changes, don't need the whole
stack, and would like to save some battery and CPU cycles.

### `growctl update`

Builds and installs GrowCtl locally, fresh from the code in the repository. GrowCtl should monitor the source code for changes it the version number there, if it detects a change it will when ran to be upgraded by running this command. So, any changes to GrowCtl must be accompanied by a version bump.