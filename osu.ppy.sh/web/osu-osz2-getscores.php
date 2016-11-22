<?php
    /*
     * Top (50) scores.
     * Still needs some optimization.
     */
    require_once '../inc/functions.php';
if($GETSCORES['bloodcatRank'] == False || $GETSCORES['bloodcatRank'] != True) {
    try {
        // Output variables if we want it
        if ($GETSCORES['outputParams']) {
            outputVariable('getscores-vars.txt', $_POST);
        }

        // Check if everything is set
        if (!isset($_GET['c']) || !isset($_GET['m']) || !isset($_GET['i']) || !isset($_GET['mods']) || !isset($_GET['us']) || !isset($_GET['ha']) || !isset($_GET['f'])) {
            throw new Exception();
        }

        // Check if the user/password is correct
        if (!PasswordHelper::CheckPass($_GET['us'], $_GET['ha'])) {
            throw new Exception('pass');
        }

        // Check if we are banned
        if (getUserAllowed($_GET['us']) == 0) {
            throw new Exception('pass');
        }

        // Find beatmap status based on configuration
        $ranked = getBeatmapRankedStatus($_GET['f'], $_GET['c'], $GETSCORES['everythingIsRanked']);

        // Check if result is not empty
        if ($ranked) {
            // Beatmap found in db, set $ranked to ranked value
            $ranked = current($ranked);
            switch ($ranked) {
                case 0:
                {
                    // Not ranked, check if latest pending version or update to latest version
                    // Compare md5 in db with client's one
                    if (compareBeatmapMd5($_GET['f'], $_GET['c'], $GETSCORES['everythingIsRanked'])) {
                        // Latest pending version
                        printBeatmapHeader(0, $_GET['c']);
                        echo chr(10);
                        printBeatmapSongInfo($_GET['c']);
                        printBeatmapAppreciation();
                    } else {
                        // Update to latest version
                        printBeatmapHeader(1, $_GET['c']);
                        echo chr(10);
                        printBeatmapSongInfo($_GET['c']);
                        printBeatmapAppreciation();
                    }
                }
                break;

                case 1:
                {
                    // Ranked, compare md5 in db with client's one
                    if (compareBeatmapMd5($_GET['f'], $_GET['c'], $GETSCORES['everythingIsRanked'])) {
                        // Ranked and right md5, show top 50 ranks
                        printBeatmapHeader(2, $_GET['c']);
                        echo chr(10);
                        printBeatmapSongInfo($_GET['c']);
                        printBeatmapAppreciation();
                        printBeatmapPlayerScore($_GET['us'], $_GET['c'], $_GET['m']);

                        // Print maintenance or top scores
                        if (checkGameMaintenance()) {
                            printBeatmapMaintenance();
                        } else {
                            printBeatmapTopScores($_GET['c'], $_GET['m'], $_GET['v'], $_GET['us']);
                        }

                        // End of scores
                        echo chr(10);

                        // If everything is ranked, set beatmap name in beatmaps_names
                        // based on file name, so we can do top/latest plays without
                        // having to put every single beatmap in db
                        if ($GETSCORES['everythingIsRanked']) {
                            // Add song to beatmaps names table only if has at least 1 score,
                            // and it's not in the db yet, because if noone has played this beatmap,
                            // it'll never be shown on top/latest rank page
                            //if ( (count($GLOBALS["db"]->fetchAll("SELECT id FROM scores WHERE beatmap_md5 = ?", $_GET["c"])) > 0) && !($GLOBALS["db"]->fetch("SELECT id FROM beatmaps_names WHERE beatmap_md5 = ?", $_GET["c"])) )
                            //{
                                // EDIT:
                                // We save every beatmap to avoid scores not shown on userpages and send fokabot messages in #announce

                                // We have scores and beatmap name isn't in the db yet, add it
                                // (we remove last 4 chars from file name aka .osu)
                                // Oops! Make sure the record doesn't already exit
                                $exists = $GLOBALS['db']->fetch('SELECT id FROM beatmaps_names WHERE beatmap_md5 = ?', [$_GET['c']]);
                            if (!$exists) {
                                $GLOBALS['db']->execute('INSERT INTO beatmaps_names (`id`, `beatmap_md5`, `beatmap_name`) VALUES (NULL, ?, ?)', [$_GET['c'], substr($_GET['f'], 0, strlen($_GET['f']) - 4)]);
                            }
                            //}
                        }
                    } else {
                        // Not submitted
                        printBeatmapHeader(-1);
                    }
                }
                break;

                default:
                {
                    // Some kind of error, not submitted
                    printBeatmapHeader(-1);
                }
                break;
            }
        } else {
            // Beatmap not found in db, not submitted
            printBeatmapHeader(-1);
        }
    } catch (Exception $e) {
        // Error
        echo 'error: '.$e->getMessage();
    }
} elseif($GETSCORES['bloodcatRank']) {
	    try {
        // Output variables if we want it
        if ($GETSCORES['outputParams']) {
            outputVariable('getscores-vars.txt', $_POST);
        }

        // Check if everything is set
        if (!isset($_GET['c']) || !isset($_GET['m']) || !isset($_GET['i']) || !isset($_GET['mods']) || !isset($_GET['us']) || !isset($_GET['ha']) || !isset($_GET['f'])) {
            throw new Exception();
        }

        // Check if the user/password is correct
        if (!PasswordHelper::CheckPass($_GET['us'], $_GET['ha'])) {
            throw new Exception('pass');
        }
        // Check if we are banned
        if (getUserAllowed($_GET['us']) == 0) {
            throw new Exception('pass');
        }
		$exists = $GLOBALS['db']->fetch('SELECT id FROM beatmaps_names WHERE beatmap_md5 = ?', [$_GET['c']]);	
		if(!$exists) {
					// okay this is very beta!
					// Gamemodes
					$bcM = '0,1,2,3';
					// Ranked maps only
					$bcS = '1';
					// Searchstring (as I said, very beta)
					$bcQ = substr($_GET['f'], 0, -4);
					$bcQ = explode(' - ', $bcQ);
					$bcQ = str_replace(' ', '+', $bcQ);

					$bcURL = 'http://bloodcat.com/osu/?mod=json&m='.$bcM.'&s='.$bcS.'&q='.$bcQ[0];
					
					$bcData = json_decode(file_get_contents($bcURL), true);
				
					$bcQ = substr($_GET['f'], 0, -4);
					$bcQ = explode(' - ', $bcQ);
					
					$title = explode(' ',$bcQ[1]);
					$title = $title[1];
					$creator = explode('(', $bcQ[1]);
					$creator = explode(')', $creator[1]);
					
					foreach($bcData as $song) {
						
						if(stripos($song['creator'], $creator[0]) == TRUE || stripos($song['title'], $title) != FALSE) {
							$ranked = TRUE;
						}
					}
							if($ranked == TRUE) {
								// Ranked (hopefully)
								printBeatmapHeader(2, $_GET['c']);
								echo chr(10);
								printBeatmapSongInfo($_GET['c']);
								printBeatmapAppreciation();
								printBeatmapPlayerScore($_GET['us'], $_GET['c'], $_GET['m']);
								printBeatmapTopScores($_GET['c'], $_GET['m'], $_GET['v'], $_GET['us']);
								$rankedval = 1;
							}
							elseif(!$bcData || empty($bcData) || !isset($bcData)) {
							// Not submitted 
								printBeatmapHeader(-1);
								$rankedval = -1;
							} elseif(isset($bcData) && $ranked !== TRUE) {
							// Just latest pending
								printBeatmapHeader(0, $_GET['c']);
								$rankedval = 0;
							}			
			}
			else {

					// Already in db
					$rankedval = current($GLOBALS['db']->fetch('SELECT ranked FROM beatmaps_names WHERE id = ?', current($exists)));
					if($rankedval == 1) {
						// ranked
						printBeatmapHeader(2, $_GET['c']);
						echo chr(10);
						printBeatmapSongInfo($_GET['c']);
						printBeatmapAppreciation();
						printBeatmapPlayerScore($_GET['us'], $_GET['c'], $_GET['m']);
						printBeatmapTopScores($_GET['c'], $_GET['m'], $_GET['v'], $_GET['us']);
					}
					elseif($rankedval == 0) {
                        // Latest pending version
                        printBeatmapHeader(0, $_GET['c']);
                        echo chr(10);
                        printBeatmapSongInfo($_GET['c']);
                        printBeatmapAppreciation();
					} else {
						// Not submitted selfmade/modded
						printBeatmapHeader(-1);
					}
			}
			
			if(empty($exists) || !isset($exists) || !intval($exists)) {
				// Put into db for future requests of this song

				$GLOBALS['db']->execute('INSERT INTO beatmaps_names (`id`, `beatmap_md5`, `beatmap_name`, `ranked`) VALUES (NULL, ?, ?, ?)', [$_GET['c'], substr($_GET['f'], 0, strlen($_GET['f']) - 4), $rankedval]);
			}
		}
		catch (Exception $e) {
        // Error
        echo 'error: '.$e->getMessage();
		}
}