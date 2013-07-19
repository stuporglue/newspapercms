<?php

/**
 * NPCPage -- NewsPaper Cms Page
 *
 * Collect content for a page displayed to the user, build the html and print it
 *
 * For every user request an NPCPage is created. When the NPCPage object is
 * destroyed (as index.php exits) the NPCPage calls its __destruct() method
 * which prints the page.
 *
 * If nothing is added to it, the page returns a 404 (page not found) http status
 * If content is added to it, a 200 http status is returned, the contents are
 * assembled and the page is printed to the user.
 *
 * You can either set the variables directly, or load content from a file via
 * the load() function.
 *
 * @author stuporglue
 */
class NPCPage {

    var $path = Array();
    var $httpHeaders = Array();
    var $meta = Array();
    var $dublinCore = Array(
	// http://dublincore.org/documents/2000/07/16/usageguide/#usinghtml
	// Content
	'Coverage' => NULL,
	'Description' => NULL,
	'Type' => NULL,
	'Relation' => NULL,
	'Source' => NULL,
	'Subject' => NULL,
	'Title' => NULL,

	// Intellectual Property
	'Contributor' => NULL,
	'Creator' => NULL,
	'Publisher' => NULL,
	'Rights' => NULL,

	//Instantiation
	'Date' => NULL,
	'Format' => NULL,
	'Identifier' => NULL,
	'Language' => NULL,
    );
    var $title;
    var $css = Array();
    var $js = Array();
    var $breadcrumb = Array();
    var $body;
    var $silent = FALSE;
    var $bodyclasses = Array();

    /**
     * @brief Create a new NPCPage object
     * @param Array $path (optional) The path the user requested
     */
    function __construct($path = Array()) {
	// Build the default page
	$this->path = $path;
	$this->httpHeaders[] = "Content-Type: text/html;charset=utf-8";
	$this->title = "Newspaper Archives";
	$this->css['css/style.css'] = 'all';
	$this->breadcrumb = $path;
	$this->body = "I'm afraid there was a problem finding the page you requested";
	$this->meta[] = '<meta charset="UTF-8" />';
	$this->js[] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';
	$this->js[] = 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js';

	$this->origVars = Array();
	foreach (get_class_vars('NPCPage') as $name => $value) {
	    $this->origVars[$name] = $this->$name;
	}
    }

    /**
     * @brief Make the <head> section of the html
     * @return string
     */
    function htmlHead() {
	$head = "<!DOCTYPE html><html><head>";
	$head .= "<title>" . NPC_SITENAME . ' | ' . $this->title . "</title>";
	$head .= implode("\n", $this->meta);
	foreach ($this->dublinCore as $k => $v) {
	    if (!is_null($v)) {
		$head .= "<meta name='DC.$k' content='" . htmlentities($v) . "'/>";
	    }
	}
	$head .= "<meta name='creator' content='newspapercms'/>";
	foreach ($this->css as $stylesheet => $media) {
	    $head .= "<link rel='stylesheet' type='text/css' media='$media' href='" . NPC_BASE_URL . "$stylesheet' />";
	}
	$head .= $this->import('analytics');
	$head .= "</head>";
	return $head;
    }

    /**
     * @brief Make the <body> section of the html
     * @return string
     */
    function htmlBody() {
	$body = "<body class='" . implode(' ', preg_replace('/[^a-zA-Z0-9 ]/','-',$this->bodyclasses)) . "'>";
	$body .= $this->header();
	$body .= "<div id='content'>{$this->body}</div>";
	$body .= $this->footer();
	$body .= "</body></html>";
	return $body;
    }

    /**
     * @brief Make the page header (not the html <head> section, not the HTTP headers)
     * @return string
     */
    function header() {
	$header = "<div id='header'>";
	$header .= "<div id='headertitles'>";
	$header .= "<div id='centeringblock'>";
	$header .= "<h1><a href='" . NPC_BASE_URL . "' title='" . NPC_SITENAME ."'>" . NPC_SITENAME . "</a></h1>";
	$header .= "<h2><a href='" . NPC_BASE_URL . "' title='" . NPC_SITENAME ."'>" . $this->title . "</a></h2>";
	$header .= "</div>";
	$header .= "</div>";
	$header .= $this->navigation();
	$header .= "</div>";
	return $header;
    }

    /**
     * @brief Make the navigation menu
     * @return string
     */
    function navigation() {
	$nav = "<div id='rightblock'>";
	$nav .= "<div id='navigation'><ul>";
	reset($this->path);
	$nav .= "<li id='microfilm-menu' class='first".(current($this->path) == 'film' ? ' selected' :'')."'><a href='" . NPC_BASE_URL . "film/'>Microfilms</a></li>";
	$nav .= "<li id='newspaper-menu' class='".(current($this->path) == 'newspaper' ? 'selected' :'')."'><a href='" . NPC_BASE_URL . "newspaper/'>Newspaper</a></li>";
	$nav .= "<li id='date-menu' class='last".(current($this->path) == 'date' ? ' selected' :'')."''><a href='" . NPC_BASE_URL . "date/'>Date</a></li>";
	$nav .= "</ul>";
	$nav .= "</div>";
	$nav .= "<div id='searchbox'>" . $this->import('search_box') . "</div>";
	$nav .= "</div>";
	return $nav;
    }

    /**
     * @brief Make the footer div
     * @return string
     */
    function footer() {
	$footer = "<div id='footer'>";
	$this->js = array_unique($this->js);
	foreach ($this->js as $js) {
	    $footer .= "<script type='text/javascript' src='$js'></script>";
	}
	$footer .= "<div id='copyright'>" . $this->import('copyright') . "</div>";
	$footer .= "</div>";
	return $footer;
    }

    /**
     * @brief Return a string of this page
     * @return String
     */
    function __toString() {
	return $this->htmlHead() . $this->htmlBody();
    }

    /**
     * @brief As this page is about to exit, it will send itself to the user
     */
    function __destruct() {
	if ($this->silent) {
	    return;
	}
	$newvars = Array();
	foreach (get_class_vars('NPCPage') as $name => $value) {
	    $newvars[$name] = $this->$name;
	}

	if ($newvars == $this->origVars) {
	    $this->httpHeaders[] = "HTTP/1.0 404 Not Found";
	}

	if (!headers_sent()) {
	    foreach ($this->httpHeaders as $header) {
		header($header);
	    }
	}
	print $this;
    }

    /**
     * @brief Load a page into the current NPCPage
     *
     * @param String $pagename -- The page contents to load
     * @param Array $args -- An array of variables from the path for the template to use
     */
    function load($pagename, $args = Array()) {
	$this->bodyclasses[] = $pagename;
	extract($args);
	$page = $this;
	if (file_exists(NPC_SERVER_DIR . "custom_pages/$pagename.php")) {
	    require_once(NPC_SERVER_DIR . "custom_pages/$pagename.php");
	} else if (file_exists(NPC_SERVER_DIR . "pages/$pagename.php")) {
	    require_once(NPC_SERVER_DIR . "pages/$pagename.php");
	}
    }

    /**
     * @brief Process a page as PHP and return the output as a string
     *
     * @param string $pagename
     * @return string
     */
    function import($pagename) {
	ob_start();
	if (file_exists(NPC_SERVER_DIR . "custom_pages/$pagename.php")) {
	    require_once(NPC_SERVER_DIR . "custom_pages/$pagename.php");
	} else if (file_exists(NPC_SERVER_DIR . "pages/$pagename.php")) {
	    require_once(NPC_SERVER_DIR . "pages/$pagename.php");
	}
	$ret = ob_get_contents();
	ob_end_clean();
	return $ret;
    }

}