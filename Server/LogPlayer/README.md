# LogPlayer

LogPlayer is really GrowFaker 1.0. It plays back log files against servers.

## Setup:
 1. Install python 3.
 2. Install pyyaml somehow, e.g.:
    ```
        $ brew install libyaml
        $ sudo python -m easy_install pyyaml
    ```
 3. Run a script
    ```
        $ cd app/
        $ python growfakerd.py SimpleFaker
    ```
 4. Go to https://dev.cropcircle.io/raw and notice there are three new entries from very recently.


## Usage

python3 growfaker.py -i Device2.txt -s SimpleFaker -u http://growserver.dev/api/ -l 4 -o


### Requirements
requests==2.9.1

[< Software Development](growserver.md)

# GrowFaker

GrowFaker is designed to simulate real life loads against the automation system.

It pretends to be our Arduino hardware devices that communicate with the system (it 'fake's devices).  This can be used for testing or to backfill data from client facilities by passing in Apache log files. 

## Setup:

1. Install python 3.
2. Install pyyaml somehow, e.g.:
	`$ brew install libyaml`
	or
	`$ sudo python -m easy_install pyyaml`
3. Install Python Requirements
	requests==2.9.1
3. Run a script
	`$ cd app/`
	`$ python3 growfaker.py -i Log.txt -s CCV09-11-16  -u http://growserver.dev/api`
4. Go to https://dev.cropcircle.io/raw and notice there are three new entries from very recently.

## Usage:

Show options

`python3 growfaker.py --help`

Use data from CCV, passing in the date for the system to use and store the datapoints with

`python3 growfaker.py -i Log.txt -s CCV09-11-16  -u http://web1.growserver.dev/api/ --date`

Same data from CCV, not passing in dates (so the data is timestamped as it comes in). Loops infinitely, until you stop it. (Ctrl+C).

`python3 growfaker.py -i Log.txt -s CCV09-11-16  -u http://web1.growserver.dev/api/ --inf=true`
