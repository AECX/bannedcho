<?php

class Leaderboard {
	const PageID = 13;
	const URL = 'leaderboard';
	const Title = 'Bannedcho - Leaderboard';
	//const LoggedIn = true;

	public function P() {
		// Leaderboard names (to bold the selected mode)
		$modesText = [0 => 'osu!standard', 1 => 'Taiko', 2 => 'Catch the Beat', 3 => 'osu!mania'];
		// Set $m value to 0 if not set
		if (!isset($_GET['m']) || empty($_GET['m'])) {
			$m = 0;
		} else {
			$m = $_GET['m'];
		}
		// Get stats for selected mode
		$modeForDB = getPlaymodeText($m);
		$modeReadable = getPlaymodeText($m, true);
		// Make sure that $m is a valid mode integer
		$m = ($m < 0 || $m > 3 ? 0 : $m);
		// Bold the selected mode
		$modesText[$m] = '<b>'.$modesText[$m].'</b>';
		// Header stuff
		echo '<blockquote><p><font size="4"><i>"Ouchi ouchi ouch.<br>Ouch again."</font></i></p><footer>Gideon Strohmeyer</footer></blockquote>';
		echo '<a href="index.php?p=13&m=0">'.$modesText[0].'</a> | <a href="index.php?p=13&m=1">'.$modesText[1].'</a> | <a href="index.php?p=13&m=2">'.$modesText[2].'</a> | <a href="index.php?p=13&m=3">'.$modesText[3].'</a>';
		// Leaderboard
		echo '<table class="table table-striped table-hover">
		<thead>
		<tr>
		<th>Rank</th>
		<th>League</th>
		<th>Player</th>
		<th>Accuracy</th>
		<th>Playcount</th>
		<th>Performance</th>
		</tr>
		</thead>';
		echo '<tbody>';
		// Get all user data and order them by pp
		$tb = 'leaderboard_'.getPlaymodeText($m);
		$leaderboard = $GLOBALS['db']->fetchAll("SELECT users_stats.*, $tb.* FROM users_stats INNER JOIN $tb ON users_stats.id=$tb.user ORDER BY $tb.position;");
		// Set rank to 0
		$r = 0;
		$allowedUsers = getAllowedUsers();
		// Print table rows
		foreach ($leaderboard as $lbUser) {
			// Make sure that this user is not banned
			if ($allowedUsers[$lbUser['username']]) {
				// Increment rank
				$r++;
				// Style for top and noob players
				if ($r <= 3) {
					// Yellow bg and trophy for top 3 players
					$tc = 'warning';
					$rankSymbol = '<i class="fa fa-trophy"></i> ';
				} else {
					// Standard table style for everyone else
					$tc = 'default';
					$rankSymbol = '#';
				}
				// Draw table row for this user
				echo '<tr class="'.$tc.'">
				<td><b>'.$rankSymbol.$r.'</b></td>';
				if ($lbUser['country'] != 'XX' && $lbUser['show_country'] == 1) {
					$country = strtolower($lbUser['country']);
				} else {
					$country = 'xx';
				}
				$division = '<img src="http://sk2.op.gg/images/medals/'.getLeague($lbUser['id']).'.png" height="28"></img>';
				echo '<td>'.$division.strtoupper(substr(getLeague($lbUser['id']),0,-2)).'</td>
				<td><img src="./images/flags/'.$country.'.png"/>	<a href="index.php?u='.$lbUser['id'].'&m='.$m.'">'.$lbUser['username'].'</a></td>				
				<td>'.(is_numeric($lbUser['avg_accuracy_'.$modeForDB]) ? accuracy($lbUser['avg_accuracy_'.$modeForDB]) : '0.00').'%</td>
				<td>'.number_format($lbUser['playcount_'.$modeForDB]).'<i> (lvl.'.$lbUser['level_'.$modeForDB].')</i></td>
				<td>'.number_format($lbUser['pp_'.$modeForDB]).' pp</td>
				</tr>';
			}
		}
		// Close table
		echo '</tbody></table>';
	}

	public static function GetUserRank($u, $mode) {
		$query = $GLOBALS['db']->fetch("SELECT position FROM leaderboard_$mode WHERE user = ?;", [$u]);
		if ($query !== false) {
			$rank = (string) current($query);
		} else {
			$rank = 'Unknown';
		}

		return $rank;
	}

	public static function BuildLeaderboard() {
		// Declare stuff that will be used later on.
		$modes = ['std', 'taiko', 'ctb', 'mania'];
		$data = ['std' => [], 'taiko' => [], 'ctb' => [], 'mania' => []];
		$allowedUsers = getAllowedUsers('id');
		// Get all user's stats
		$users = $GLOBALS['db']->fetchAll('SELECT id, pp_std, pp_taiko, pp_ctb, pp_mania FROM users_stats');
		// Put the data in the correct way into the array.
		foreach ($users as $user) {
			if (!$allowedUsers[$user['id']]) {
				continue;
			}
			foreach ($modes as $mode) {
				$data[$mode][] = ['user' => $user['id'], 'pp' => $user['pp_'.$mode]];
			}
		}
		// We're doing the sorting for every mode.
		// Also 'pp' is the pp of the user
		foreach ($modes as $mode) {
			// Do the sorting
			usort($data[$mode], function ($a, $b) {
				if ($a['pp'] == $b['pp']) {
					return 0;
				}
				// We're doing ? 1 : -1 because we're doing in descending order.
				return ($a['pp'] < $b['pp']) ? 1 : -1;
			});
			// Remove all data from the table
			$GLOBALS['db']->execute("TRUNCATE TABLE leaderboard_$mode;");
			// And insert each user.
			foreach ($data[$mode] as $key => $val) {
				if($val['user'] != 999) {
					$GLOBALS['db']->execute("INSERT INTO leaderboard_$mode (position, user, v) VALUES (?, ?, ?)", [$key + 1, $val['user'], $val['pp']]);
				}
			}
		}
	}

	public static function Update($userID, $newpp, $mode) {
		// Who are we?
		$us = $GLOBALS['db']->fetch("SELECT * FROM leaderboard_$mode WHERE user=?", [$userID]);
		$newplayer = false;
		if (!$us) {
			$newplayer = true;
		}
		// Find player who is right below our pp
		$target = $GLOBALS['db']->fetch("SELECT * FROM leaderboard_$mode WHERE v <= ? ORDER BY position ASC LIMIT 1", [$newpp]);
		$plus = 0;
		if (!$target) {
			// Wow, this user completely sucks at this game.
			$target = $GLOBALS['db']->fetch("SELECT * FROM leaderboard_$mode ORDER BY position DESC LIMIT 1");
			$plus = 1;
		}
		// Set $newT
		if (!$target) {
			// Okay, nevermind. It's not this user to suck. It's just that no-one has ever entered the leaderboard thus far.
			// So, the player is now #1. Yay!
			$newT = 1;
		} else {
			// Otherwise, just give them the position of the target.
			$newT = $target['position'] + $plus;
		}
		// Make some place for the new "place holder".
		if ($newplayer) {
			$GLOBALS['db']->execute("UPDATE leaderboard_$mode SET position = position + 1 WHERE position >= ? ORDER BY position DESC", [$newT]);
		} else {
			$GLOBALS['db']->execute("DELETE FROM leaderboard_$mode WHERE user = ?", [$userID]);
			$GLOBALS['db']->execute("UPDATE leaderboard_$mode SET position = position + 1 WHERE position < ? AND position >= ? ORDER BY position DESC", [$us['position'], $newT]);
		}
		// Finally, insert the user back.
		if($val['user'] != 999) {
			$GLOBALS['db']->execute("INSERT INTO leaderboard_$mode (position, user, v) VALUES (?, ?, ?);", [$newT, $userID, $newpp]);
		}
	}
}
