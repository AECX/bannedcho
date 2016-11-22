<?php
/*
 * lastfm.php seems to be called when:
 * - A map is started
 * - Probably more.
 * It theorically should be for sending data to last.fm, but I suspect it can also be used to change status on bancho.
 *
 * GET parameters:
 * b - the beatmap ID the user is listening/playing
 * action - 'np' if the song just started, 'scrobble' if it's been playing for over 40 seconds or if 50% of it passed
 * us - The username of who is listening to that song.
 * ha - The password hash of the username.
 *
 * Response:
 * "-3" if user doesn't have a last.fm account associated with their account
 * anything else if the client does, the client doesn't contain a check for the response
*/
require_once '../inc/functions.php';

$user = $_GET['us'];

// Lets use this for IP update (every two weeks!)
$time = current($GLOBALS['db']->fetch('SELECT ip_time FROM users WHERE username = ?', $user));
if(!$time || $time == 0) {
    $time = time()-1209600;
}


if(time()-$time >= 1209600) {
    // Update IP
    $GLOBALS['db']->execute("UPDATE users SET ip = ? WHERE username = ?;", [getIP(), $user]);
    // Update ip_time
    $GLOBALS['db']->execute("UPDATE users SET ip_time = ? WHERE username = ?;", [time(), $user]);
    echo $user.'s time is now '.time().' and ip set to '.getIP();
} else {
	echo 'No update yet! Wait '.strval(1209600-(time()-$time)).' seconds!';
}