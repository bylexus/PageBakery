<?php
/**
 * The Page Bakery!
 * (c) 2012 Alexander Schenkel, alex@alexi.ch
 * This software has no special license besides the mentioned Copyright.
 * You can use it wherever you want, but please mention my name and drop
 * me a note, thanks.
 *
 * This software makes use of the following free software:
 * - Smarty, the famous PHP Template engine (http://smarty.net)
 * - Twitter Bootstrap (http://twitter.github.com/bootstrap/)
 * - jQuery (http://jquery.com)
 * ----------------------------------------------------------------------------
 *
 * Web startup point. Request this file from your web browser on a PHP-
 * enabled web-server.
 */
require_once(dirname(__FILE__).'/php/DocDispatcher.php');
require_once(dirname(__FILE__).'/doc_config.php');

// Execute when not from command line-triggered
if (php_sapi_name() != 'cli') {
	$d = new DocDispatcher();	
	$d->doWebOutput();
} else {
	echo "Sorry, not meant to be called from command line.\nPlease invoke me from a web server.\n";
	exit(1);
}

