from flask import Flask, send_file, redirect, request, jsonify
import os
import itertools
app = Flask(__name__)

"""
These 2 guys don't need .osz
"""
#bmlink = "http://osu.uu.gl/s/"
#bmlink = "http://bloodcat.com/osu/s/"
"""
This one is qite specific, for europe
this link will be (I think) the fastest one
It's hosted by Howl, one of the creators of
Ripple! Thanks!

!!! TO DOWNLOAD .osz beatmaps we need to add the '.osz' after the link!
!!! If we use other links like bloodcat we don't, care about that!
"""
bmlink  = "http://m.zxq.co/"


bmlookup = "http://new.ppy.sh/s/" # Map information and stuff, if a user clicks beatmap listing
bannedcho = "http://osu.bannedcho.ml"



@app.route("/status")
def serverStatus():
	return jsonify({
		"response" : 200,
		"status" : 1
	})

# General Server
@app.route("/")
def serveWebsite():
	return redirect(bannedcho)

# Ingame customization redirect
@app.route("/forum/ucp.php")
def serveCustomize():
	return redirect(bannedcho+"/?p=5")

# Beatmapserver
@app.route("/d/<int:bmid>")
def serverBeatmap(bmid):
	return redirect(bmlink+str(bmid)+".osz")


# Beatmapinformation redirection
@app.route("/b/<int:bmid>")
def serveBeatmapInfro(bmid):
	return redirect(bmlookup+str(bmid))

# Userserver
@app.route("/u/<string:uid>")
def serveUser(uid):
	return redirect(bannedcho+"/submit.php?action=search&name="+uid)
#Run server
app.run(host="0.0.0.0", port=5000)
