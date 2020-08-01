import urllib.request, urllib.parse, urllib.error
import ssl
import json

# Ignore SSL certificate errors
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

s = 0
url = "http://py4e-data.dr-chuck.net/comments_852535.json"
url_conn = urllib.request.urlopen(url, context=ctx)
data = url_conn.read().decode()
load_data = json.loads(data)
comments = load_data['comments']
for comment in comments:
    s += comment['count']
print(s)
    
