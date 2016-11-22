<?php
require_once './functions.php';

echo 'JSON decode initialized... <br>';

$beatmaps = json_decode(file_get_contents('http://m.zxq.co/index.json'), true);

foreach($beatmaps as $beatmap) {
	echo '<h1> Getting ranked stat for '.$beatmap["SetID"].'</h1>';
	
	if($beatmap["RankedStatus"] == 1){
	echo '<h2> !!! RANKED !!!</h2>';
	foreach($beatmap["ChildrenBeatmaps"] as $childbm){
		echo '<br> Getting beatmap stats...<br>';		
		$beatmapData = json_decode(file_get_contents('http://m.zxq.co/b/'.$childbm.'.json'), true);
		
		$exists = $GLOBALS['db']->fetch('SELECT beatmap_md5 FROM beatmaps_names WHERE beatmap_md5 = ?', $beatmapData["FileMD5"]);
		if(!$exists) {
			$name = $beatmap["Artist"].' - '.$beatmap["Title"].' ('.$beatmap["Creator"].') ['.$beatmapData["DiffName"].']';
			//$GLOBALS['db']->execute('INSERT INTO beatmaps_names (`id`, `beatmap_md5`, `beatmap_name`) VALUES (NULL, ?, ?)', [$beatmapData["FileMD5"], $name]);
			  $GLOBALS['db']->execute('INSERT INTO beatmaps_names (`id`, `beatmap_md5`, `beatmap_name`) VALUES (NULL, ?, ?)', [$beatmapData["FileMD5"], $name]);
			  echo 'md5: '.$beatmapData["FileMD5"].' name :'.$name;
		}
		//ARTIST - NAME (creator) [DIFF]
		}
	}
}
echo 'DONE';
?>