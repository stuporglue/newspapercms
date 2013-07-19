<?php

// $film_id is set in index.php

$q = "SELECT * FROM `film` WHERE `id`='" . mysql_real_escape_string($film_id) . "'";

$res = do_query($q);

if (!($thispage = mysql_fetch_assoc($res))) {
    $page->title = "Film Not Found";
    $page->body = "<p>The specified film was not found</p>";
} else {
    $page->title = "Microfilm {$thispage['name']}";
    if($thispage['url']){
    $newbody = "<p>Source URL: <a href='{$thispage['url']}' target='_blank' title='View microfilm " . urlencode($thispage['name']) . " at its source'>{$thispage['url']}</a></p>";
    }else{
	$newbody = "";
    }

    if (!$thispage['scanned']) {
	$newbody .= "<p>This microfilm has not yet been scanned.</p>";
    }

    $newbody .= "<h3>Newspapers on this Film</h3><p><ol>";
    $q = "SELECT DISTINCT `newspaper`.*
    FROM `page`,`issue`,`newspaper`
    WHERE
    `page`.`film_id`='$film_id' AND
    `page`.`issue_id`=`issue`.`id` AND
    `issue`.`newspaper_id`=`newspaper`.`id`
    ORDER BY `newspaper`.`title`
    ";

    $res = do_query($q);
    $minDate = 9999;
    $maxDate = -1;
    while ($newspaper = mysql_fetch_assoc($res)) {
	$newbody .= "<li><a href='" . NPC_BASE_URL . "newspaper/{$newspaper['urltitle']}' title='{$newspaper['title']}'>{$newspaper['title']} ({$newspaper['start_date']} to {$newspaper['end_date']})</a></li>";
	if (strtotime($newspaper['start_date']) < $minDate) {
	    $minDate = strtotime($newspaper['start_date']);
	}
	if (strtotime($newspaper['end_date']) > $maxDate) {
	    $maxDate = strtotime($newspaper['start_date']);
	}
    }
    $newbody .= "</ol></p>";

    // Dublin Core metadata
    $dublinCore = Array(
	// http://dublincore.org/documents/2000/07/16/usageguide/#usinghtml
	// Content
	//'Coverage' => NULL,
	'Description' => "Digital copy of microfilm {$thispage['name']}",
	'Type' => "Digitized image of microfilmed newspapers",
	'Relation' => $thispage['url'],
	//'Source' => NULL,
	//'Subject' => NULL,
	'Title' => "Microfilm {$thispage['name']}",
	// Intellectual Property
	//'Contributor' => NULL,
	//'Creator' => NULL,
	//'Publisher' => NULL,
	'Rights' => $page->import('copyright'),
	//Instantiation
	'Date' => date('Y-m-d', $minDate) . "-" . date('Y-m-d', $maxDate),
	'Format' => NULL,
	'Identifier' => $thispage['name'],
	    //'Language' => NULL,
    );

    global $DC_FILM_DEFAULTS;
    $page->dublinCore = array_merge($page->dublinCore, $dublinCore, $DC_FILM_DEFAULTS);

    $page->body = $newbody;
}