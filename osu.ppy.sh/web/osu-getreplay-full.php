<?php
/*
 * Full replay downloading.
*/
header('Content-type: program/osr');


require_once '../inc/functions.php';
require_once '../inc/ModsEnum.php';
	// Get replay data
	$replayData = $GLOBALS['db']->fetch('SELECT * FROM scores WHERE id = ?', [$_GET['c']]);
	// Get file name
	$beatmapName = $GLOBALS['db']->fetch('SELECT beatmap_name FROM beatmaps_names WHERE beatmap_md5 = ?', [$replayData['beatmap_md5']]);
	if ($beatmapName) {
		$beatmapName = current($beatmapName);
	} else {
		$beatmapName = $_GET['c'];
	}
	$fileName = 'replay_'.$_GET['c'];
	header('Content-Disposition: attachment; filename="'.$fileName.'.osr"');
	// Get replay raw data
	$rawData = file_get_contents('../replays/replay_'.$_GET['c'].'.osr');
	// Build memes
	$fullCombo = $replayData['full_combo'] == 1 ? 'True' : 'False';
	// Calculate rank
	$totalNotes = $replayData['300_count'] + $replayData['100_count'] + $replayData['50_count'] + $replayData['misses_count'];
	$perc300 = number_format($replayData['300_count'], 2) / number_format($totalNotes, 2);
	$perc50 = number_format($replayData['50_count'], 2) / number_format($totalNotes, 2);
	$hidden = $replayData['mods'] & ModsEnum::Hidden || $replayData['mods'] & ModsEnum::Flashlight ? true : false;
	if ($perc300 == 1.0) {
		if ($hidden) {
			$rank = 'XH';
		} else {
			$rank = 'X';
		}
	} elseif ($perc300 > 0.9 && $perc40 <= 0.01 && $replayData['misses_count'] == 0) {
		if ($hidden) {
			$rank = 'SH';
		} else {
			$rank = 'S';
		}
	} elseif (($perc300 > 0.8 && $replayData['misses_count'] == 0) || ($perc300 > 0.9)) {
		$rank = 'A';
	} elseif (($perc300 > 0.7 && $replayData['misses_count'] == 0) || ($perc300 > 0.8)) {
		$rank = 'B';
	} elseif ($perc300 > 0.6) {
		$rank = 'C';
	} else {
		$rank = 'D';
	}
	// ...why
	$magicString = md5(sprintf('%dp%do%do%dt%da%dr%de%sy%do%du%s%d%s', $replayData['100_count'] + $replayData['300_count'], $replayData['50_count'], $replayData['gekis_count'], $replayData['katus_count'], $replayData['misses_count'], $replayData['beatmap_md5'], $replayData['max_combo'], $fullCombo, $replayData['username'], $replayData['score'], $rank, $replayData['mods'], 'True'));
	// Build full replay
	$output = '';
	$output .= pack('C', $replayData['play_mode']);
	$output .= pack('I', 20150414);
	$output .= binStr($replayData['beatmap_md5']);
	$output .= binStr($replayData['username']);
	$output .= binStr($magicString);
	$output .= pack('S', $replayData['300_count']);
	$output .= pack('S', $replayData['100_count']);
	$output .= pack('S', $replayData['50_count']);
	$output .= pack('S', $replayData['gekis_count']);
	$output .= pack('S', $replayData['katus_count']);
	$output .= pack('S', $replayData['misses_count']);
	$output .= pack('I', $replayData['score']);
	$output .= pack('S', $replayData['max_combo']);
	$output .= pack('C', $replayData['full_combo']);
	$output .= pack('I', $replayData['mods']);
	$output .= pack('C', 0); // Life bar graph, empty
	$output .= "\x00\x00\x00\x00\x00\x00\x00\x00"; // Time, not implemented (yet)
	$output .= pack('I', strlen($rawData));
	$output .= $rawData;
	$output .= pack('I', 0);
	$output .= pack('I', 0);
	// Redirect to file
	echo $output;
	
?>