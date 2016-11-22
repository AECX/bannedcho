<?php
require_once '../osu.ppy.sh/inc/functions.php';

// Starts/stops the server
//shell_exec('python3 ../c.ppy.sh/pep.py &');

/*
				HELP
		$id[0] = PID of pep.py
		$id[1] = PID of avatar server
		$id[2] = PID of interface server

*/

$PIDS = shell_exec('ps -e | grep python3');

$PIDS = explode('python3', $PIDS);

$server = ['pep.py','avatar','interface'];
$i = 0;
foreach($PIDS as $PID) {
	$PID = explode(' ', $PID);
	$servername = $server[$i];
	$GLOBALS['db']->execute('UPDATE servers_info SET server = ? AND PID = ? WHERE id = ?', [$servername, $PID[1], $i]);
	$i++;
}

?>