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
	public function getParameter($name, $defaultvalue = null) {
		if(array_key_exists($name,$_REQUEST)) {
			return $_REQUEST[$name];
		}
		return $defaultvalue;
	}
}