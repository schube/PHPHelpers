<?php

namespace Schubec\PHPHelpers;

/**
 * Leichteres Handling von Sessions
 * 
 * @copyright Copyright (c) 2019, schubec
 * @version 1.0
 * @author Bernhard Schulz <bernhard.schulz@schubec.com>
 */


class Session {
	public static function set($key, $value) {
		if(session_status() != PHP_SESSION_ACTIVE) {
			throw new \Exception('Session not activated');
		}
		$_SESSION[$key] = $value;
	}
	public static function getRequiredValue($name) {
		if(!self::hasValue($name)) {
			throw new \Exception("Required value {$name} is missing.");
		}
		return $_SESSION[$name];
	}
	public static function hasValue($name) {
		if(session_status() != PHP_SESSION_ACTIVE) {
			throw new \Exception('Session not activated');
		}
		return array_key_exists($name,$_SESSION);
	}
}