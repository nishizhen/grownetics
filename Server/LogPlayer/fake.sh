cd /Faker
# Wait for the deviceapi to be ready before we start hitting it with api requests
python3 growfaker.py -i Log.txt -s CCV09-11-16 -u http://nginx/api/ --inf=true