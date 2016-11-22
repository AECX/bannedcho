<?php
//if(empty($_POST['DATABASE_NAME']) || empty($_POST['DATABASE_USER']) || empty($_POST['DATABASE_PASS']) || empty($_POST['DATABASE_HOST']) || empty($_POST['DATABASE_WHAT']) || empty($_POST['avatar']) || empty($_POST['server']) || empty($_POST['name']) || empty($_POST['AESkey']) || empty($_POST['submitoutputParams']) || empty($_POST['saveFailedScores']) || empty($_POST['okOutput']) || empty($_POST['everythingIsRanked']) || empty($_POST['bloodcatRank']) || empty($_POST['getscoresoutputParams']) || empty($_POST['statusAvatar']) || empty($_POST['statusInterface']) || empty($_POST['statusBancho'])

			echo'
			<body style="text-align: center;">

			<h1> WELCOME TO THE VERY ADVANCED ULTRA SPECIAL EXTRA LIMITED SETUP FOR BANNEDCHO </h1>

			<br>

			You can set up the config file through browser!
			Or just go to ./inc/ and rename config.sample.php to config.php
			<br>
			then just edit it with any kind of editor.
			<br><br>


			<div id="config" style="text-align: left; background-color: grey;">
			<form action="/inc/setup.php" method="POST">
			<h2>Database:</h2>
			<table>
				<tr><td>DATABASE_NAME:</td><td><input type="text" name="DATABASE_NAME" placeholder="Bannedcho"></Input></td></tr>
				<tr><td>DATABASE_USER:</td><td><input type="text" name="DATABASE_USER" placeholder="root"></Input></td></tr>
				<tr><td>DATABASE_PASS:</td><td><input type="password" name="DATABASE_PASS" placeholder="******"></Input></td></tr>
				<tr><td>DATABASE_HOST:</td><td><input type="text" name="DATABASE_HOST" placeholder="localhost"></Input></td></tr>
				<tr><td>DATABASE_WHAT:</td><td><select name="DATABASE_WHAT">
												<option value="host">host</option>
												<option value="unix_socket">unix_socket</option>
												</select></td></tr>
			</table>

			<h2>Uniform Resource Locators</h2>
			<table>
				<tr><td>Avatar:</td><td><input type="text" name="avatar" placeholder="http://a.ppy.sh"></input></td></tr>
				<tr><td>Server:</td><td><input type="text" name="server" placeholder="http://osu.ppy.sh"></input></td></tr>
				<tr><td>Name:  </td><td><input type="text" name="name" placeholder="Bannedcho"></input></td></tr>
			</table>

			<h2>osu! Submission(s)</h2>
			<table>
				<tr><td></td><td><input type="text" name="AESKey" value="h89f2-890h2h89b34g-h80g134n90133" hidden></input></td></tr>
				<tr><td>OutputParams:</td><td><select type="number" name="submitoutputParams">
												<option value="0">False</option>
												<option value="1">True</option>
												</select></td></tr>
				<tr><td></td><td><input type="number" name="saveFailedScores" value="0" hidden></input></td></tr>
				<tr><td></td><td><input type="text"   name="okOutput" value="ok" hidden></input></td></tr>
			</table>

			<h2>osu! Ranking</h2>
			<table>
				<tr><td>Everything is ranked:</td><td><select type="number" name="everythingIsRanked">
														<option value="0">False</option>
														<option value="1">True</option>
														</select></td></tr>
				<tr><td>Bloodcat Ranking:</td><td><select type="number" name="bloodcatRank">
													<option value="0">False</option>
													<option value="1">True</option>
													</select></td></tr>
				<tr><td>OutputParams:</td><td><select type="number" name="getscoresoutputParams">
												<option value="0">False</option>
												<option value="1">True</option>
												</select></td></tr>
			</table>

			<h2>Server Status Configuration</h2>
			<table>
				<tr><td>Avatars URL:</td><td><input type="text" name="statusAvatar" placeholder="http://127.0.0.1:4999"></Input></td></tr>
				<tr><td>Interface URL:</td><td><input type="text" name="statusInterface" placeholder="http://127.0.0.1:5000"</Input></td></tr>
				<tr><td>Bancho URL:</td><td><input type="text" name="statusBancho" placeholder="http://127.0.0.1:5001"></Input></td></tr>
			</table>
			<button type="submit"><font size="5"><br>Install</button>
			</form>
			</div>
			';
			
			$URL = '$URL';
			$SUBMIT = '$SUBMIT';
			$GETSCORES = '$GETSCORES';
			
			$mailgunConfig = '$MailgunConfig = [\'domain\' => \'\', \'key\' => \'\'];';
			
			
			$serverStatusConfig = '
	// Server status configuration
	$ServerStatusConfig = [\'service_status\' => [\'enable\' => true, // Must be true if you want to enable "Service status" section
	\'bancho_url\'                                         => \'http://127.0.0.1:5001\', // Bancho URL
	\'avatars_url\'                                        => \'http://127.0.0.1:5000\', // Avatar server URL
	\'interface_url\'									   => \'http://127.0.0.1:5000\', // Interface | Redirection
	], \'netdata\' => [\'enable\'                            => true,
	\'server_url\'                                         => \'http://127.0.0.1:19999\',
	\'header_enable\'                                      => true,
	\'system_enable\'                                      => true,
	\'network_enable\'                                     => true,
	\'disk_enable\'                                        => true,
	\'disk_name\'                                          => \'vda\',
	\'mysql_server\'                                       => \'srv\',
	\'mysql_enable\'                                       => true,
	\'apache_enable\'                                       => true,
	]];';
			
			
			
			$config = "<?php\n//THE VERY ADVANCED SUPER [...] setup created this\n";
			$config .= "define('DATABASE_NAME', '".$_POST['DATABASE_NAME']."');\n";
			$config .= "define('DATABASE_USER', '".$_POST['DATABASE_USER']."');\n";
			$config .= "define('DATABASE_PASS', '".$_POST['DATABASE_PASS']."');\n";
			$config .= "define('DATABASE_HOST', '".$_POST['DATABASE_HOST']."');\n";
			$config .= "define('DATABASE_WHAT', '".$_POST['DATABASE_WHAT']."');\n";
			
			$config .= $URL."['avatar'] = '".$_POST['avatar']."';\n";
			$config .= $URL."['server'] = '".$_POST['server']."';\n";
			$config .= $URL."['name'] = '".$_POST['name']."';\n";
			
			$config .= $SUBMIT."['AESKey'] = '".$_POST['AESKey']."';\n";
			$config .= $SUBMIT."['outputParams'] = ".getTrueFalse($_POST['outputParams']).";\n";
			$config .= $SUBMIT."['saveFailedScores'] = ".getTrueFalse($_POST['saveFailedScores']).";\n";
			$config .= $SUBMIT."['okOutput'] = '".$_POST['okOutput']."';\n";
			
			$config .= $GETSCORES."['everythingIsRanked'] = ".getTrueFalse($_POST['everythingIsRanked']).";\n";
			$config .= $GETSCORES."['bloodcatRank'] = ".getTrueFalse($_POST['bloodcatRank']).";\n";
			$config .= $GETSCORES."['submitoutputParams'] = ".getTrueFalse($_POST['submitoutputParams']).";\n";
			
			$config .= $mailgunConfig;
			
			$config .= "\n";
			
			$config .= $serverStatusConfig;
			
		
			$file = fopen('./config.php', 'w');
			fwrite($file, $config);
			fclose($file);
		
	
	function getTrueFalse($value) {
		switch($value) {
			case 0:
				return 'False';
			break;
			case 1:
				return 'True';
			break;
			default:
				return 'False';
			break;
		}
	}

?>
<html>
</body>
