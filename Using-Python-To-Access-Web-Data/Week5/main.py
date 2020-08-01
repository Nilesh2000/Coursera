import urllib.request, urllib.parse, urllib.error
import xml.etree.ElementTree as ET
import ssl

# Ignore SSL certificate errors
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

url = "http://py4e-data.dr-chuck.net/comments_852534.xml"
url_conn = urllib.request.urlopen(url, context=ctx)
data = url_conn.read()
tree = ET.fromstring(data)
s = 0
counts = tree.findall('.//count')
for count in counts:
    s += int(count.text)
print(s)
