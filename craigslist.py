#!/usr/bin/env python

import feedparser
from BeautifulSoup import BeautifulSoup
import re
import urllib
import sys
import MySQLdb
import time
import datetime
from lxml.html.clean import Cleaner

### craiglist domain ###
subcity = 'newyork'
subtown = 'wch'

##categories##
'''
sss = everything
ata = antiques
baa = baby and kids
bar = barter
bia = bikes
boo = boats
bka = books
bfa = business
sya = computer
zip = free stuff
fua = furniture
foa = general
has = household
jwa = jewlery
maa = materials
rva= rv and camp
sga = sports
tia = tickets
tla = tools
waa = wanted
ppa = appliances
ara = art and crafts
sna = atv/utv/sno
pta = autoparts
haa = beauty and health
      cars and trucks
ema = cds dvds vhs
moa = cell phones
cla = clothes and acc
cba = colletibles
ela = electronics
gra = farm and garden
gms = garage sale
hva = heavy equipment
      motorcycles
msa = music instruments
pha = photo equipment
taa = toys and games
vga = video games
'''

### Search Parameters ###

search_terms = ["playset","playground","welder","lumber"]
search_categories = [['sss'],['baa','zip'],['zip','tla'],['zip']]
search_city = ["wch","wch","wch","wch"]
### Database Information ###

#db_file = "/var/db/craigslist.db"
db = MySQLdb.connect(host="localhost", # your host, usually localhost
                     user="root", # your username
                      passwd="eimajimi", # your password
                      db="craigslist") # name of the data base

### Craigslist RSS Search URL ###

rss_generic_link = "http://"+subcity+".craigslist.org/search/sss/"+subtown+"?catAbb=%s&query=%s&s=0&format=rss"

db_cursor = db.cursor()

# Generate the RSS links
rss_links = []
lst_links = [] #hold the listing we are looking at, for tagging later
trm_links = []

update_time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

termcount = 0
for term in search_terms:
	for cat in search_categories[termcount]:
		term = urllib.quote(term)
		rss_link = rss_generic_link % (cat, term)

		rss_links.append(rss_link)
		lst_links.append(cat)
		trm_links.append(term)
	termcount+=1
# Retrieve the RSS feeds

cleaner = Cleaner(remove_unknown_tags=False, allow_tags=['img', 'p', 'a', 'b', 'em', 'div']);


count=0#start counting so we can apply the right tags for term and catergory

for rss_link in rss_links:
	listings = feedparser.parse(rss_link)
	
	for listing in listings.entries:
		title = listing["title"]
		url = listing["link"]
		text = cleaner.clean_html(listing["description"])
		lis = lst_links[count]
		term = trm_links[count]

		#go get an image if there is one
		imgurl = ""
		page = urllib.urlopen(url)
		soup = BeautifulSoup(page)
		comments = soup.find(text=re.compile("imgList"))
		if comments:
			if len(comments):
				imglist = comments.split('"')
				imgurl = imglist[1]

		#db_cursor.execute("""SELECT last_update FROM listings WHERE title = ?""", (title,))
		db_cursor.execute("""SELECT last_update FROM listings WHERE title = %s""",(title,))
		#r=db.store_result()

		if db_cursor.fetchone() == None:
			db_cursor.execute("""
				INSERT INTO listings 
				(url, imgurl, title, text, tagcat, tagterm, last_update, new, found) 
				VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)""",
				(url, imgurl, title, text, lis, term, update_time, 1, update_time,)
			)
		else:
			db_cursor.execute("""
				UPDATE listings
				SET last_update = %s
				WHERE title = %s
				""",
				(update_time, title,)
			)
	count+=1
	

# Clean out expired entries

db_cursor.execute("""
	DELETE FROM listings
	WHERE last_update != %s
	""",
	(update_time,)
)

db.commit()
db.close()

