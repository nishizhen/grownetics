__author__ = 'Nick Busey'
__copyright__ = 'Copyright 2016, Grownetics'
__version__ = '1.0.0'
__date__ = '02-23-2016'
__description__ = 'Reads the config.yml of a set, and spawns all the growfaker dockers'

import sys, argparse, re, glob, yaml, os, os.path

request_delay = 1000

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Send fake grow data to Grownetics server')

    parser.add_argument("set",help="The name of the 'set' folder to use.")

    args = parser.parse_args()

    set_name = args.set
    print(set_name)

    with open('sets/'+set_name+'/config.yml', 'r') as f:
        config = yaml.load(f)

    grownetics_url = config["url"]
    if "delay" in config:
        request_delay = config["delay"]

    if "logs" in config and config["logs"] == 1:
        log_file = open(input_file_name,'a')

    # TODO: Check this again
    # if args.inputfile == None:
    #     prnt('ERROR: No input file specified.')
    #     sys.exit()

    print('Config:')
    print('grownetics_api.url: ' + grownetics_url + '\n')
    print('Request delay: {0:.3f} seconds'.format(request_delay))

    input_loops = 1
    if "loops" in config:
        input_loops = config["loops"]
    print('Loading device files from')
    print('/share/sets/'+set_name+'/devices/')

    # Setup Docker
    os.system('docker-machine start default')
    os.system('eval "$(docker-machine env default)"')

    # Launch Instances
    listing = os.listdir('sets/'+set_name+'/devices/')
    for file in listing:
        # print('docker run -d -v /home/core/share:/share quay.io/grownetics/growfaker python3 /share/app/growfaker.py -i '+file+' -o -url '+grownetics_url)
        print('docker run -d -v '+os.path.dirname(os.path.abspath(__file__))+'/../:/share quay.io/grownetics/growfaker python3 /share/app/growfaker.py -i '+file+' -s '+set_name+' -o -u '+grownetics_url+' -l '+str(input_loops))
        os.system('docker run -d -v '+os.path.dirname(os.path.abspath(__file__))+'/../:/share quay.io/grownetics/growfaker python3 /share/app/growfaker.py -i '+file+' -s '+set_name+' -o -u '+grownetics_url+' -l '+str(input_loops))
