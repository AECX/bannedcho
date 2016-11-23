#!/bin/bash
nohup python3 ../c.ppy.sh/pep.py &
nohup python3 ../a.ppy.sh/start_server.py &
nohup python3 ../if.ppy.sh/osuwebserver.py &

echo "\n\n Done..."
