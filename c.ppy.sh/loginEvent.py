import userHelper
import serverPackets
import exceptions
import glob
import consoleHelper
import bcolors
import locationHelper
import countryHelper
import time
import generalFunctions
import channelJoinEvent

def handle(flaskRequest):
	# Data to return
	responseTokenString = "ayy"
	responseData = bytes()
	# The IP for your private network, to get the right location you should use your
	# public IP (e.g http://ping.eu)
	localIP = "172.20.7.107" # The ip you log in with
	publicIP = "8.8.8.8" # google lul

	# Get IP from flask request
	requestIP = flaskRequest.headers.get("X-Forwarded-For")
	if requestIP == localIP:
		requestIP = publicIP

	# Console output
	print("> Accepting connection from {}...".format(requestIP))

	# Split POST body so we can get username/password/hardware data
	# 2:-3 thing is because requestData has some escape stuff that we don't need
	loginData = str(flaskRequest.data)[2:-3].split("\\n")

	# Process login
	print("> Processing login request for {}...".format(loginData[0]))
	try:
		# If true, print error to console
		err = False

		# Try to get the ID from username
		userID = userHelper.getID(str(loginData[0]))

		if userID == False:
			# Invalid username
			raise exceptions.loginFailedException()
		if userHelper.checkLogin(userID, loginData[1]) == False:
			# Invalid password
			raise exceptions.loginFailedException()

		# Make sure we are not banned
		userAllowed = userHelper.getAllowed(userID)
		if userAllowed == 0:
			# Banned
			raise exceptions.loginBannedException()
		
		# Activate user (obviously not the banned.-.)
		# But those who created an account without logging in through bancho yet
		if userAllowed == 2:
			# Not activated yet
			userHelper.Activate(userID)

		# No login errors!
		# Delete old tokens for that user and generate a new one
		glob.tokens.deleteOldTokens(userID)
		responseToken = glob.tokens.addToken(userID)
		responseTokenString = responseToken.token

		# Get silence end
		userSilenceEnd = max(0, userHelper.getSilenceEnd(userID)-int(time.time()))

		# Get supporter/GMT
		userRank = userHelper.getRankPrivileges(userID)
		userGMT = False
		userSupporter = True
		if userRank >= 3:
			userGMT = True

		# Server restarting check
		if glob.restarting == True:
			raise exceptions.banchoRestartingException()

		# Maintenance check
		if glob.banchoConf.config["banchoMaintenance"] == True:
			if userGMT == False:
				# We are not mod/admin, delete token, send notification and logout
				glob.tokens.deleteToken(responseTokenString)
				raise exceptions.banchoMaintenanceException()
			else:
				# We are mod/admin, send warning notification and continue
				responseToken.enqueue(serverPackets.notification("Bancho is in maintenance mode. Only mods/admins have full access to the server.\nType !system maintenance off in chat to turn off maintenance mode."))

		# Send all needed login packets
		responseToken.enqueue(serverPackets.silenceEndTime(userSilenceEnd))
		responseToken.enqueue(serverPackets.userID(userID))
		responseToken.enqueue(serverPackets.protocolVersion())
		responseToken.enqueue(serverPackets.userSupporterGMT(userSupporter, userGMT))
		responseToken.enqueue(serverPackets.userPanel(userID))
		responseToken.enqueue(serverPackets.userStats(userID))

		# Channel info end (before starting!?! wtf bancho?)
		responseToken.enqueue(serverPackets.channelInfoEnd())

		# Default opened channels
		# TODO: Configurable default channels
		channelJoinEvent.joinChannel(responseToken, "#osu")
		channelJoinEvent.joinChannel(responseToken, "#announce")
		if userRank >= 3:
			# Join admin chanenl if we are mod/admin
			# TODO: Separate channels for mods and admins
			channelJoinEvent.joinChannel(responseToken, "#admin")

		# Output channels info
		for key, value in glob.channels.channels.items():
			if value.publicRead == True:
				responseToken.enqueue(serverPackets.channelInfo(key))

		responseToken.enqueue(serverPackets.friendList(userID))

		# Send main menu icon and login notification if needed
		if glob.banchoConf.config["menuIcon"] != "":
			responseToken.enqueue(serverPackets.mainMenuIcon(glob.banchoConf.config["menuIcon"]))

		if glob.banchoConf.config["loginNotification"] != "":
			responseToken.enqueue(serverPackets.notification(glob.banchoConf.config["loginNotification"]))

		# Get everyone else userpanel
		# TODO: Better online users handling
		for key, value in glob.tokens.tokens.items():
			responseToken.enqueue(serverPackets.userPanel(value.userID))
			responseToken.enqueue(serverPackets.userStats(value.userID))

		# Send online users IDs array
		responseToken.enqueue(serverPackets.onlineUsers())
		
		if requestIP == None:
			# Get Last 'usual' IP from user (default 8.8.8.8 / USA / Google)
			requestIP = userHelper.logInIP(userID)		
		# Get location and country from ip.zxq.co or database
		if generalFunctions.stringToBool(glob.conf.config["server"]["localizeusers"]):
			# Get location and country from IP
			location = locationHelper.getLocation(requestIP)
			country = countryHelper.getCountryID(locationHelper.getCountry(requestIP))
		else:
			# Set location to 0,0 and get country from db
			print("[!] Location skipped")
			location = [0,0]
			country = countryHelper.getCountryID(userHelper.getCountry(userID))

		# Set location and country
		responseToken.setLocation(location)
		responseToken.setCountry(country)

		# Send to everyone our userpanel and userStats (so they now we have logged in)
		glob.tokens.enqueueAll(serverPackets.userPanel(userID))
		glob.tokens.enqueueAll(serverPackets.userStats(userID))

		# Set reponse data to right value and reset our queue
		responseData = responseToken.queue
		responseToken.resetQueue()

		# Some things about IP
		logInIP = userHelper.logInIP(userID)
		logInIP = logInIP['ip']
		print("[!] First IP: "+format(logInIP))
		if logInIP != requestIP:
			# We'll inform...
			message = "This is not your usual IP! Remember we don't like multiaccounting! (ignore if you did not)"
			responseToken.enqueue(serverPackets.notification(message))

		# Print logged in message
		consoleHelper.printColored("> {} logged in ({})".format(loginData[0], responseToken.token), bcolors.GREEN)
	except exceptions.loginFailedException:
		# Login failed error packet
		# (we don't use enqueue because we don't have a token since login has failed)
		err = True
		responseData += serverPackets.loginFailed()
	except exceptions.loginBannedException:
		# Login banned error packet
		err = True
		responseData += serverPackets.loginBanned()
	except exceptions.banchoMaintenanceException:
		# Bancho is in maintenance mode
		responseData += serverPackets.notification("Our bancho server is in maintenance mode. Please try to login again later.")
		responseData += serverPackets.loginError()
	except exceptions.banchoRestartingException:
		# Bancho is restarting
		responseData += serverPackets.notification("Bancho is restarting. Try again in a few minutes.")
		responseData += serverPackets.loginError()
	finally:
		# Print login failed message to console if needed
		if err == True:
			consoleHelper.printColored("> {}'s login failed".format(loginData[0]), bcolors.YELLOW)

		return (responseTokenString, responseData)
