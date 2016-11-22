import urllib.request
import json

import consoleHelper
import bcolors

# API URL
url = "http://ip.zxq.co"


def getCountry(ip):
	"""
	Get country from IP address

	ip -- IP Address
	return -- Country code (2 letters)
	"""

	# Default value, sent if API is memeing
	country = "XX"
	# Try to get country from Pikolo Aul's Go-Sanic ip API
	country = json.loads(urllib.request.urlopen("{}/{}".format(url, ip)).read().decode())["country"]

	return country


def getLocation(ip):
	"""
	Get latitude and longitude from IP address

	ip -- IP address
	return -- [latitude, longitude]
	"""

	# Default value, sent if API is memeing
	data = [0,0]

	# Try to get position from Pikolo Aul's Go-Sanic ip API
	data = json.loads(urllib.request.urlopen("{}/{}".format(url, ip)).read().decode())["loc"].split(",")

	return [float(data[0]), float(data[1])]
