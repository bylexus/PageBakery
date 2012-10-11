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
 * Config for the Page Bakery.
 * The following configs can / must be set:
 *
 * Page config:
 * -------------
 * 'pages': array of page definitions. Each pages entry's key represents a
 * page template in the <pageDir>/ directory. The framework just adds '.html'.
 * The value of each entry is a config array which then can be accessed in the
 * Template by using {$pages.<pagekey>.<valuekey>}.
 * The framework knows the following special keys:
 * - layout: A layout is the "skeleton" for all pages. It defines the
 *     frame for each page. Within the layout template, the variable
 *     {$page_content} can be used to output the
 *     actual page on this position.
 *     If set, the layout file <layout>.html is used instead of defaultLayout
 * - link: Added by the framework itself, represents the (dynamic or static) link
 *     to the page. Useful for inter-page-links.
 * An example:
 * <code>
 * Config::set('pages',array(
 *   'index' => array('title'=>'Home','param1'=>'value1'), // page template: <pages>/index.html
 *   'page1' => array('title'=>'Page 1','layout'=>'2ndlayout') // uses 2ndlayout.html
 * ));
 * </code>
 *
 * while pagename is the name of the HTML template in pages/ (without .html),
 * title is the Page's display title, and iconCls is an icon class for the Bootstrap iconset.
 *
 * The static $staticResources array defines static dirs / files to be copied in addition
 * to the generated static HTML files.
 */

/**
 * Page config, see above
 */
Config::set('pages',array(
	'index' => array('title' => 'Overview','iconCls'=> 'icon-home'),
	'usage' => array('title' => 'Usage','iconCls' => 'icon-wrench','layout'=>'layout',
		'subpages' => array(
			'requirements' => array('title' => 'Requirements'),
			'configuration' => array('title' => 'Configuration'),
			'writing' => array('title' => 'Writing Pages'),
			'writing_subpages' => array('title' => 'Writing Subpages'),
			'layouts' => array('title' => 'Defining Layouts'),
			'dynamic' => array('title' => 'Dynamic page display'),
			'static' => array('title' => 'Generate Static HTML Pages')
		)
	),
	'credits' => array('title' => 'Credits','iconCls' => 'icon-globe'),
	'contact' => array('title' => 'Contact','iconCls' => 'icon-comment')
));

/**
 * Defines static resources to be copied in addition when creating
 * the static version of the page. Relative to the root dir of The Page Bakery.
 */
Config::set('staticResources', array(
	'static'
));

/**
 * pageDir sets the directory where the page templates are stored.
 */
Config::set('pageDir',realpath(dirname(__FILE__).'/pages'));

/**
 * tmpDir sets the (writable) temp dir for the Smarty engine.
 */
Config::set('tmpDir',dirname(__FILE__).'/tmp');

/**
 * defaultPage defines an entry in the 'pages' config which
 * represents the default page. Can be accessed in the template by
 * using {$defaultPage[.link ...]}
 */
Config::set('defaultPage','index');

/**
 * defaultLayout is the layout file (.html is appended) used for all pages,
 * except those that define its own layout.
 */
Config::set('defaultLayout','layout');

