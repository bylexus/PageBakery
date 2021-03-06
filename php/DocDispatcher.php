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
 * This is it: The Page Bakery workhorse! This class is responsible for
 * the whole functionality of The Page Bakery.
 *
 */
require_once(dirname(__FILE__).'/Smarty-3.1.10/Smarty.class.php');
require_once(dirname(__FILE__).'/Config.php');

/**
 * DocDispatcher is responsible for do the page output,
 * either as dynamic web page or generate static HTML files.
 *
 * Usage
 * ======
 *
 * Call it the following ways:
 *
 * As dynamic web page:
 * $dd = new DocDispatcher();
 * $dd->doWebOutput();
 *
 * Generate static output:
 * $dd = new DocDispatcher();
 * $dd->createStaticPages('/path/to/an/output/dir');
 *
 * That's it!
 *
 *
 * Config and pages
 * ==================
 * Config is done in doc_config.php, see there.
 *
 * Each page has its template-representative in pages/<pagename>.html. Those
 * are smarty templates that are used for generating the output.
 */
class DocDispatcher {
	private $smarty = null;
	private $page;
	private $sub = null;
	private $tmp;
	private $pagedir;

	public function __construct() {
		$this->tmp = Config::get('tmpDir',realpath(dirname(__FILE__).'/../tmp'));
		if (!is_dir($this->tmp)) mkdir($this->tmp,0777,true);

		$this->pagedir = Config::get('pageDir',realpath(dirname(__FILE__).'/../pages'));
		$this->page = Config::get('defaultPage','index');
		$this->setupSmarty();
	}

	public function doWebOutput() {
		$this->parseRequest();
		$this->outputPage('web');
	}

	public function createStaticPages($outputDir) {
		if (!$outputDir) {
			echo "Please provide the output directory on the command line.\n";
			exit(1);
		}
		if (!is_dir($outputDir)) {
			mkdir($outputDir,0777,true) or die ("Error on creating output directory {$outputDir}.\n");
		}

		// Loop over all pages in doc_config.php and generate the final static output.
		$pages = Config::get('pages');
		foreach($pages as $page=>$pageInfo) {
			$this->page = basename($page);
			$this->sub = null;
			
			// Default sub page for page?
			if (isset($pageInfo['defaultSubpage'])) {
				$this->sub = basename($pageInfo['defaultSubpage']);
			}

			echo "Building page {$this->page}...\n";
			ob_start();
			$this->outputPage('static');
			$output = ob_get_contents();
			file_put_contents($outputDir.'/'.$this->page.'.html',$output);
			ob_end_clean();

			// Are there sub-pages? Build them too:
			if (isset($pageInfo['subpages']) && is_array($pageInfo['subpages'])) {
				foreach($pageInfo['subpages'] as $subpage=>$subpageInfo) {
					$this->sub = basename($subpage);
					echo "    Sub-Page {$this->sub}...\n";
					ob_start();
					$this->outputPage('static');
					$output = ob_get_contents();
					file_put_contents($outputDir.'/'.$this->page.'.'.$this->sub.'.html',$output);
					ob_end_clean();					
				}
			}
		}

		// Copy also static resources:
		$res = Config::get('staticResources',array());
		$here = realpath(dirname(__FILE__).'/..');
		if (is_array($res) && count($res) > 0) {
			echo "Copy static resources ...\n";
			foreach($res as $r) {
				echo "    {$here}/${r}...\n";
				$this->recursiveCopy($here.'/'.$r,$outputDir);
			}
		}

		echo "\ndone.\n";
	}

	private function recursiveCopy($source, $dest) {
	    if (!is_dir($dest))
	    	mkdir($dest);

	    if (is_dir($source)) {
	    	$sourceHandle = opendir($source);
	    	$destDirname = $dest.'/'.basename($source);
	    	@mkdir($destDirname);

	    	while($res = readdir($sourceHandle)){ 
		        if($res == '.' || $res == '..') 
		            continue; 
		        if(is_dir($source . '/' . $res)){
		            $this->recursiveCopy($source . '/' . $res, $destDirname); 
		        } else {
		            copy($source . '/' . $res, $destDirname . '/' . $res); 
		        } 
		    }
		    closedir($sourceHandle);
	    } else {
	    	copy($source,$dest);
	    }
	}


	private function setupSmarty() {
		$this->smarty = new Smarty();
		$this->smarty->setCompileDir($this->tmp);
		$this->smarty->setCacheDir($this->tmp);
	}

	private function getPagePath($pageName) {
		$page = basename($pageName);
		$pagepath = $this->pagedir.'/'.$page.'.html';
		if ($page != '' && is_file($pagepath)) {
			return $pagepath;
		} else return null;
	}

	private function getLayoutForPage($page = null) {
		$pc = Config::get('pages');
		if ($page && isset($pc[$page]['layout'])) {
			return $pc[$page]['layout'];
		} else return Config::get('defaultLayout','layout');
	}

	private function getLayoutPath($page = null) {
		return $this->pagedir . '/'.$this->getLayoutForPage($page).'.html';
	}

	private function parseRequest() {
		$page = null;
		if (isset($_REQUEST['page'])) {
			$page = basename($_REQUEST['page']);
			if ($this->getPagePath($page)) {
				$this->page = $page;
			} else $this->page = null;
		}

		// Default sub page for page?
		$conf = Config::get('pages');
		if (isset($conf[$this->page]) && isset($conf[$this->page]['defaultSubpage'])) {
			$this->sub = basename($conf[$this->page]['defaultSubpage']);
		}

		if ($page && isset($_REQUEST['sub'])) {
			$sub = basename($_REQUEST['sub']);
			if ($this->getPagePath($page.'.'.$sub)) {
				$this->sub = $sub;
			} else $this->sub = null;
		}
	}

	private function createPageLink($page, $mode, $subpage = null) {
		$link = '';
		if ($mode == 'web')	{
				$link = './index.php?page='.basename($page);
				if ($subpage) {
					$link .= '&sub='.basename($subpage);
				}
			} else {
				$link = basename($page);
				if ($subpage) {
					$link .= '.'.basename($subpage);
				}
				$link .= '.html';
		}
		return $link;
	}

	private function enhancePageConfig($mode = 'web') {
		$config = Config::get('pages');
		// Enhance all pages entries with a 'link' entry to be used in the template:
		foreach($config as $key=>$entry) {
			$entry['link'] = $this->createPageLink($key,$mode);
			// Subpages present? do it also for those.
			if (isset($entry['subpages']) && is_array($entry['subpages'])) {
				foreach($entry['subpages'] as $subkey=>$subentry) {
					$entry['subpages'][$subkey]['link'] = $this->createPageLink($key,$mode,$subkey);
				}
			}
			$config[$key] = $entry;
		}

		return $config;
	}

	private function outputPage($mode = 'web') {
		if ($mode == 'web') {
			header('Content-Type: '.Config::get('contentType','text/html;charset=UTF-8'));
		}

		$page = '';
		$pagePath = $this->getPagePath($this->page);

		$pages = $this->enhancePageConfig($mode);
		

		$this->smarty->assign('pages',$pages);
		$this->smarty->assign('config',Config::getConfigArray());
		$defaultPage = $pages[Config::get('defaultPage','index')];
		$this->smarty->assign('defaultPage',$defaultPage);
		
		if ($pagePath) {
			$this->smarty->assign('error',false);
			$this->smarty->assign('page_key',$this->page);
			$this->smarty->assign('sub_key',$this->sub);
			$this->smarty->assign('page',$pages[$this->page]);

			// Subpage present?
			if ($this->sub) {
				$this->smarty->assign('sub',$pages[$this->page]['subpages'][$this->sub]);
				$sub = $this->smarty->fetch('file:'.$this->getPagePath($this->page.'.'.$this->sub));
				$this->smarty->assign('subpage_content',$sub);
			}

			$page = $this->smarty->fetch('file:'.$this->getPagePath($this->page));

		} else {
			$this->smarty->assign('error',true);
			$this->smarty->assign('error_msg','Page not found.');
			$this->smarty->assign('page_key',$this->page);
			$this->smarty->assign('sub_key',$this->sub);
			$page = $this->smarty->fetch('file:'.$this->getPagePath('error'));
		}
		$this->smarty->assign('page_content',$page);
		$layout = $this->smarty->fetch('file:'.$this->getLayoutPath($this->page));
		print $layout;
	}
}

