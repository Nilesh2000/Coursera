import urllib.request, urllib.parse, urllib.error
import json
import ssl

# Ignore SSL certificate errors
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

place = "Universidad Nacional Costa Rica"
params = dict()
params['place'] = place
url = "http://py4e-data.dr-chuck.net/json?key=42&address=" + urllib.parse.urlencode(params)
url_conn = urllib.request.urlopen(url, context=ctx)
data = url_conn.read().decode()
load_data = json.loads(data)
print(load_data['results'][0]['place_id'])
