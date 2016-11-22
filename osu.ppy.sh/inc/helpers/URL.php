<?php

class URL {
	public static function Avatar() {
		global $URL;

		return isset($URL['server']) ? $URL['avatar'] : 'http://a.bannedcho.ml';
	}

	public static function Server() {
		global $URL;

		return isset($URL['server']) ? $URL['server'] : 'http://osu.bannedcho.ml';
	}
	public static function Name() {
		global $URL;

		return isset($URL['name']) ? $URL['name'] : 'Bannedcho';
	}
}
