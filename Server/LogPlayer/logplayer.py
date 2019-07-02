__author__ = 'Adam Litton, Nick Busey'
__copyright__ = 'Copyright 2016, Grownetics'
__version__ = '1.1'
__date__ = '03.02.2016'
__description__ = 'Reads input file containing a series of Grow Data (interspersed with optional ''sleep <n>'' statements) to send to Grownetics Server. See sampleinput.txt for input file format.'

import sys, configparser, argparse, re, time, requests, os

request_delay = 0

input_loops_default = 1

start_line = 1

# configured host name for grownetics rest api
grownetics_url = ''

send_date = False

#util methods
def is_blank(strippedline): return strippedline.startswith('#') or strippedline == ''
def to_num(s): return int(float(s))


#generic regex sub-pattern
positive_integer_pattern = r'\d+'


# sleep <seconds>
sleep_pattern = re.compile(r'^sleep (?P<seconds>' + positive_integer_pattern + r')$')
def is_sleep(strippedline):
    sleepmatch = sleep_pattern.match(strippedline)
    if sleepmatch:
        return to_num(sleepmatch.group('seconds'))
    else:
        return 0

data_pattern_shorthand = re.compile(r'(?P<ip>[0-9.]*).*\[(?P<date>.*)\].*\{(?P<data>.*)\}.*')

def send_grow_data(data,date):
    if (send_date):
        request_str = '{{{0},"date":"{1}"}}'.format(
            data,
            date)
    else:
        request_str = '{{{0}}}'.format(
            data)

    prnt('Send request string:\n' +  request_str)

    request = grownetics_url + 'raw?q=' + request_str

    prnt('Parsed request: ' + request)

    try:
        response = requests.get(request, verify=False)
    except requests.exceptions.RequestException as e:
        prnt(e)
        sys.exit(1)

    prnt(response.status_code)
    prnt(response.text)

def prnt(output):
    print(output)
    sys.stdout.flush()
    if log_file:
        log_file.write(str(output)+'\n')

def process_growfile(input_file_name):
    input_file = open(input_file_name,'r')
    prnt('process growfile')
    lines = input_file.readlines()
    lines = lines[start_line:]
    # remove whitespace from every line
    lines = map(str.strip, lines)
    # filter blank lines and comments
    lines = filter(lambda line: not is_blank(line), lines)
    line_num = start_line
    for line in lines:
        # delay data sends by specified amount
        time.sleep(request_delay)

        prnt('[Line {}]'.format(line_num))
        line_num += 1

        sleeptime = is_sleep(line)
        if sleeptime:
            prnt('Sleep for {} seconds...'.format(sleeptime))
            time.sleep(sleeptime)
        else:
            # we expect a data_match at this point
            data_match = data_pattern_shorthand.match(line)
            if data_match:
                data_date = data_match.group('date')
                data = data_match.group('data')
                data = data.replace("\\",'');
                send_grow_data(data,data_date)
            else:
                prnt('Line skipped due to unrecognized format')

if __name__ == '__main__':
    config = configparser.SafeConfigParser()
    config.read('/share/app/growfaker.ini')

    parser = argparse.ArgumentParser(description='Send fake grow data to Grownetics server')
    parser.add_argument('-i',
        dest='inputfile',
        type=str,
        help='File containing fake grow data to send to Grownetics server')

    parser.add_argument('-u',
        dest='url',
        type=str,
        help='Url of the API server')

    parser.add_argument('-d', '--delay',
        dest='delay',
        type=float,
        default=request_delay,
        help='Time in seconds between each request sent to the Grownetics server')

    parser.add_argument('-l', '--loop',
        dest='input_loops',
        type=int,
        default=input_loops_default,
        help='How many times GrowFaker should loop over the input file.')

    parser.add_argument('-o',
        dest='output_logs',
        action='store_true',
        help='Whether or not GrowFaker should output logs.')

    parser.add_argument('-s',
        dest='set_name',
        help='The data set being iterated on.')

    parser.add_argument('--inf',
        dest='infinite',
        help='If set it will loop until the process is stopped.')

    parser.add_argument('--date',
        dest='send_date',
        action='store_true',
        help='If set it will send the date of the log timestamp, used to backfill old data')

    parser.add_argument('--line',
        dest='start_line',
        type=int,
        help='Line number of the log file to start from.')

    args = parser.parse_args()

    grownetics_url = args.url
    input_file_name = args.inputfile
    request_delay = args.delay
    set_name = args.set_name
    infinite = args.infinite
    send_date = args.send_date
    log_file = False
    if args.start_line:
        start_line = args.start_line

    set_dir = os.path.dirname(__file__)+'sets/'+set_name

    if args.output_logs:
        os.makedirs(set_dir+'/logs', 0o0755, True)
        log_file = open(set_dir+'/logs/'+input_file_name+'.log','a+')

    prnt("Got input file name: "+input_file_name)

    if args.inputfile == None or args.url == None:
        prnt('ERROR: No input file specified.')
        sys.exit()

    prnt('Arguments:')
    prnt('grownetics_api.url: ' + grownetics_url)
    prnt('Request delay: {0:.3f} seconds'.format(request_delay))


    input_loops = args.input_loops
    num = 0
    while infinite or num < input_loops:
        process_growfile(set_dir+ '/devices/'+input_file_name)
        num =+ 1

    prnt(' ')
