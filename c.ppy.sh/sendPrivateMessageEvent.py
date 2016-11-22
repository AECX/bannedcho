import consoleHelper
import bcolors
import clientPackets
import serverPackets
import glob
import Rohwabot
import exceptions

def handle(userToken, packetData):
	"""
	Event called when someone sends a private message

	userToken -- request user token
	packetData -- request data bytes
	"""

	try:
		# Get usertoken username
		username = userToken.username

		# Private message packet
		packetData = clientPackets.sendPrivateMessage(packetData)

		if packetData["to"] == "Rohwabot":
			# Rohwabot command check
			rohwaMessage = Rohwabot.RohwabotResponse(username, packetData["to"], packetData["message"])
			if rohwaMessage != False:
				userToken.enqueue(serverPackets.sendMessage("Rohwabot", username, rohwaMessage))
				consoleHelper.printColored("> Rohwabot>{}: {}".format(packetData["to"], str(rohwaMessage.encode("UTF-8"))), bcolors.PINK)
		else:
			# Send packet message to target if it exists
			token = glob.tokens.getTokenFromUsername(packetData["to"])
			if token == None:
				raise exceptions.tokenNotFoundException()

			# Send message to target
			token.enqueue(serverPackets.sendMessage(username, packetData["to"], packetData["message"]))
			# Save message in db for Admin panel chatlog
			glob.db.execute("INSERT INTO bancho_private_messages (`msg_from_username`, `msg_to`, `msg`, `time`) VALUES (?, ?, ?, ?)", [str(username), packetData["to"], packetData["msg"], System.currentTimeMillis()/1000])
			# Send away message to sender if needed
			if token.awayMessage != "":
				userToken.enqueue(serverPackets.sendMessage(packetData["to"], username, "This user is away: {}".format(token.awayMessage)))

		# Console output
		consoleHelper.printColored("> {}>{}: {}".format(username, packetData["to"], packetData["message"]), bcolors.PINK)
	except exceptions.tokenNotFoundException:
		# Token not found, user disconnected
		consoleHelper.printColored("[!] {} tried to send a message to {}, but their token couldn't be found".format(username, packetData["to"]), bcolors.RED)
