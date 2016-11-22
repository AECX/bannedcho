"""Some console related functions"""

import bcolors
import glob

def printServerStartHeader(asciiArt):
	"""Print server start header with optional ascii art

	asciiArt -- if True, will print ascii art too"""

	if asciiArt == True:
		print("- BANNEDCHO OSU!SERVER STARTED -")
		print("                          .. o  .")
		print("                         o.o o . o")
		print("                        oo...")
		print("                    __[]__")
		print("    nyo -->  _\\:D/_/o_o_o_|__")
		print("             \\\"\"\"\"\"\"\"\"\"\"\"\"\"\"/")
		print("              \\ . ..  .. . /")
		print("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^{}".format(bcolors.ENDC))

	printColored("> Welcome to pep.py osu!bancho server v{}".format(glob.VERSION), bcolors.GREEN)
	printColored("> Made by the Ripple team | Modded/'optimized' by [RiSEN]", bcolors.GREEN)
	printColored("> {}https://github.com/osuripple/ripple | just RiSEN, nothing special.".format(bcolors.UNDERLINE), bcolors.GREEN)
	printColored("> Press CTRL+C to exit\n | Start it with '$ > nohup python3 pep.py & ' for run it in Background\n",bcolors.GREEN)


def printNoNl(string):
	"""
	Print string without new line at the end

	string -- string to print
	"""

	print(string, end="")


def printColored(string, color):
	"""
	Print colored string

	string -- string to print
	color -- see bcolors.py
	"""

	print("{}{}{}".format(color, string, bcolors.ENDC))


def printError():
	"""Print error text FOR LOADING"""

	printColored("Error", bcolors.RED)


def printDone():
	"""Print error text FOR LOADING"""

	printColored("Done", bcolors.GREEN)


def printWarning():
	"""Print error text FOR LOADING"""

	printColored("Warning", bcolors.YELLOW)
