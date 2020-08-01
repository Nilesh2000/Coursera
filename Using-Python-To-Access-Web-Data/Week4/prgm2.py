import urllib.request, urllib.parse, urllib.error
from bs4 import BeautifulSoup
import ssl

# Ignore SSL certificate errors
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

page_url = "http://py4e-data.dr-chuck.net/known_by_Plamedie.html"

counter = 7
i = 0
while i < counter:
    url = page_url
    html = urllib.request.urlopen(url, context=ctx).read()
    soup = BeautifulSoup(html, 'html.parser')
    tags = soup('a')
    tag = tags[17]
    page_url = tag.get('href', None)
    i+=1
print(tag.text)
