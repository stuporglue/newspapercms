<?php

/*
 * This is a very simple search engine. It is not very efficient and you should
 * probably replace it with  something better, but I wanted to include searching
 * of some sort.
 *
 * Check out these for simple options:
 * http://www.google.com/cse/
 * http://sphinxsearch.com/downloads/
 * http://xapian.org/
 */

if (!array_key_exists('query', $_POST)) {
    $page->title = "Search " . NPC_SITENAME;
    $newbody = "<h3>Search " . NPC_SITENAME . "</h3>";
    $newbody .= "<p>" . $page->import('search_box') . "</p>";
    $page->body = $newbody;
} else {
    $_POST['query'] = trim(rtrim($_POST['query']));

    $page->title = "Search results for: " . htmlentities('"' . $_POST['query'] . '"');
    $newbody = "<h3>Search results for: " . htmlentities('"' . $_POST['query'] . '"') . "</h3>";

    $newbody .= "<p>You can use the search operators + to include a term, - to
	exclude a term or enclose a phrase in quotes to search for the exact phrase.</p>";

    $query = mysql_real_escape_string($_POST['query']);

    $q = "  SELECT
    `page`.`id`,
    'page' AS`type`,
    CONCAT('newspaper/',`newspaper`.`urltitle`,'/issue-',`issue`.`id`,'/',`page`.`page_no`,'/') AS `path`,
    CONCAT(`newspaper`.`title`,': ',DATE_FORMAT(`issue`.`date`,'%M %d, %Y'),', ',`page`.`page_no`) AS `title`,
    `ocr` AS `found`
    FROM `page`,`issue`,`newspaper`
    WHERE
    `page`.`issue_id`=`issue`.`id` AND
    `issue`.`newspaper_id`=`newspaper`.`id` AND
    MATCH (`page`.`ocr`) AGAINST ( '$query' IN BOOLEAN MODE)

    UNION

    -- find in newspaper titles
    SELECT
    `id`,
    'newspaper' AS `type`,
    CONCAT('newspaper/',`newspaper`.`urltitle`,'/') AS `path`,
    `newspaper`.`title` AS `title`,
    `title` AS `found`
    FROM `newspaper`
    WHERE MATCH (`newspaper`.`title`) AGAINST ( '$query' IN BOOLEAN MODE)

    UNION

    -- find in film names
    SELECT
    `id`,
    'film' AS `type`,
    CONCAT('film/',`film`.`id`) AS `path`,
    `name` AS `title`,
    `name` AS `found`
    FROM `film`
    WHERE MATCH (`film`.`name`) AGAINST ( '$query' IN BOOLEAN MODE)";

    $res = do_query($q);

    $query = str_replace(Array('+','-','"',"'"),'',$_POST['query']);
    $query = preg_quote($query);
    $query = trim($query);

    $newbody .= "<ul class='searchresults'>";
    while ($row = mysql_fetch_assoc($res)) {

	$row['found'] = iconv('UTF-8','UTF-8//IGNORE//TRANSLIT',$row['found']);
	$row['found'] = preg_replace('/[^\x9\xA\xD(\x20-\xD7FF)(\xE000-\xFFFD)]/u','',$row['found']);

	$newbody .= "<li><span class='foundtype'>" . ucfirst($row['type']) . ": </span>
	    <a href='" . NPC_BASE_URL . $row['path'] . "' alt='" . htmlentities($row['title']) . "' title='" . htmlentities($row['title']) . "'>" . htmlentities($row['title']) . "</a>
		<p>";

	// Try to get the 100 words on either side of the search terms
	$searchRegex = preg_replace('/(\s+)/smi', '.*?', $query); // replace non-word boundaries with asterists
	preg_match("/(.{0,200})($searchRegex)(.{0,200})/smi", $row['found'], $matches); // m -- match newlines, i -- case insensitive

	if(count($matches) > 0){
	    $newbody .= htmlentities($matches[1]);

	    $replaceRegex = preg_split('/\s+/', $query);
	    foreach ($replaceRegex as $piece) {
		preg_match("|([^($piece)]*)($piece)|smi", $matches[2], $emp);
		$matches[2] = preg_replace("/^{$emp[0]}/smi", '', $matches[2]);
		$newbody .= htmlentities($emp[1]) . "<strong>" . htmlentities($emp[2]) . "</strong>";
	    }

	    $newbody .= htmlentities($matches[3]);
	}else{
	    // If that didn't work, try to get the 20 words around every search word

	    // remove negative boolean terms
	    $searchPieces = preg_split('/\s+/',$query);
	    $summary = Array();
	    $maxChars = (400/count($searchPieces));
	    foreach($searchPieces as $piece){
		preg_match("/(.{0,$maxChars})($piece)(.{0,$maxChars})/smi", $row['found'], $matches); // m -- match newlines, i -- case insensitive
		if(count($matches)){
		    $summary[] = htmlentities($matches[1]) . "<strong>" . htmlentities($matches[2]) . "</strong>" . htmlentities($matches[3]);
		}
	    }
	    if(count($summary) > 0){
		$summary = implode('&hellip', $summary);
	    }else{
		// If that didn't work, get the first 100 words
		preg_match_all('/([a-zA-Z0-9-_]+)/',$row['found'],$matches);
		$summary = implode(' ',array_slice($matches[0],0,100));
	    }
	    $newbody .= $summary;
	}

	$newbody .= "</p>";



	$newbody .= "</li>";
    }
    $newbody .= "</ul>";

    $page->body = $newbody;
}