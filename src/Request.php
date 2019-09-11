<?php

namespace Schubec\PHPHelpers;

/**
 * Leichteres Handling von Requests
 *
 * @copyright Copyright (c) 2019, schubec
 * @version 1.0
 * @author Bernhard Schulz <bernhard.schulz@schubec.com>
 */


class Request {
	public static function getParameter($name, $defaultvalue = null) {
		if(self::hasParameter($name)) {
			return $_REQUEST[$name];
		}
		return $defaultvalue;
	}
	public static function getRequiredParameter($name) {
		if(!self::hasParameter($name)) {
			throw new \Exception("Required parameter {$name} is missing.");
		}
		return $_REQUEST[$name];
	}
	public static function hasParameter($name) {
		return array_key_exists($name,$_REQUEST) && $_REQUEST[$name]!=null;
	}
	
}