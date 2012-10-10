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
 * Static pages generator. Generagtes static HTML files from your
 * page configs, filling in template variables and resolving links
 * into static html references. Also copies static files / dirs
 * defined in Config::set('staticResources',array()) into the target
 * folder.
 *
 * Usage:
 *
 * php generate_static.php <output-dir>
 *
 */
require_once(dirname(__FILE__).'/php/DocDispatcher.php');
require_once(dirname(__FILE__).'/doc_config.php');

if (php_sapi_name() != 'cli') {
	echo "ERROR: Can only be started from command line.\n";
	exit(1);
}

$d = new DocDispatcher();
array_shift($argv);
$outDir = array_shift($argv);
$d->createStaticPages($outDir);