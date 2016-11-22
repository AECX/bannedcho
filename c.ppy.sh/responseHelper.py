import flask
import gzip

def generateResponse(token, data = None):
	"""
	Return a flask response with required headers for osu! client, token and gzip compressed data

	token -- user token
	data -- plain response body
	return -- flask response
	"""

	resp = flask.Response(gzip.compress(data, 6))
	resp.headers['cho-token'] = token
	resp.headers['cho-protocol'] = '19'
	resp.headers['Keep-Alive'] = 'timeout=5, max=100'
	resp.headers['Connection'] = 'keep-alive'
	resp.headers['Content-Type'] = 'text/html; charset=UTF-8'
	resp.headers['Vary'] = 'Accept-Encoding'
	resp.headers['Content-Encoding'] = 'gzip'
	return resp


def HTMLResponse():
	"""Return HTML bancho meme response"""

	html = 	"<html><head><title>UHINF?!</title><style type='text/css'>body{width:30%}</style></head><body><pre>"
	html += "<marquee style='white-space:pre;'><br>"
	html += "                          .. o  .<br>"
	html += "                         o.o o . o<br>"
	html += "                        oo...<br>"
	html += "                    __[]__<br>"
	html += "    phwr-->  _\\:D/_/o_o_o_|__     <span style=\"font-family: 'Comic Sans MS'; font-size: 8pt;\">u wot m8</span><br>"
	html += "             \\\"\"\"\"\"\"\"\"\"\"\"\"\"\"/<br>"
	html += "              \\ . ..  .. . /<br>"
	html += "^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^<br>"
	html += "</marquee></pre></body></html>"
	return html
