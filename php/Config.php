<?php
/**
 * The Page Bakery!
 * (c) 2012 Alexander Schenkel, alex@alexi.ch
 * This software has no special license besides the mentioned Copyright.
 * You can use it wherever you want, but please mention my name and drop
 * me a note, thanks.
 * This software makes use of the following free software:
 * - Smarty, the famous PHP Template engine (http://smarty.net)
 * - Twitter Bootstrap (http://twitter.github.com/bootstrap/)
 * - jQuery (http://jquery.com)
 * ----------------------------------------------------------------------------
 *
 * Config class for The Page Bakery.
 */
class Config {
	private static $inst = null;
	
	private $conf = array();
	private function __construct() {
	}
	
	public static function set($key, $value) {
		$i = self::getInst();
		$i->conf[$key] = $value;
	}
	
	
	public static function get($key, $default = null) {
		$i = self::getInst();
		if (isset($i->conf[$key]))
			return $i->conf[$key];
		else return $default;
	}
	
	private static function getInst() {
		if (self::$inst == null) {
			self::$inst = new Config();
		}
		return self::$inst;
	}

	public static function getConfigArray() {
		$inst = self::getInst();
		return $inst->conf;
	}
}

