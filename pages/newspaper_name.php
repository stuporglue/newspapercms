<?php

// $film_id is set in index.php

$q = "SELECT *
    FROM `newspaper`
    WHERE `urltitle`='" . mysql_real_escape_string($newspaper_name) . "'";

$res = do_query($q);

if (!($thispage = mysql_fetch_assoc($res))) {
    $page->title = "Newspaper Not Found";
    $page->body = "The specified newspaper was not found";
} else {
    $newspaper_id = $thispage['id'];
    $newspaper_title = $thispage['title'];
    $page->title = $thispage['title'];

    // About
    $newbody = "<h3>About the {$thispage['title']}</h3>";

    $newbody .= "<p>Published from " . date('Y', strtotime($thispage['start_date'])) . " to " . date('Y', strtotime($thispage['end_date'])) . "</p>";

    $newbody .= $thispage['blurb'];


    // By Date
    $q = "SELECT DISTINCT YEAR(`date`) AS `year` FROM `issue`
	WHERE `issue`.`newspaper_id`='$newspaper_id'
    ORDER BY `issue`.`date`
    ";

    $newbody .= "<h3>Browse the <span class='newspapertitle'>{$thispage['title']}</span> by Year</h3>";

    $res = do_query($q);

    $newbody .= "<p class='horizontal-years'>";

    if(mysql_num_rows($res) == 0){
	$newbody .= "We couldn't find any issues of {$thispage['title']}. We may still need to digitize them.";
    }

    while($thispage = mysql_fetch_assoc($res)){
	$newbody .= "<a href='" . NPC_BASE_URL . "newspaper/$newspaper_name/{$thispage['year']}' title='Browse " . addslashes($newspaper_title) . " archives from {$thispage['year']}'>{$thispage['year']}</a> ";
    }
    $newbody .= "</p>";

    $newbody .= "<h3>Films This Newspaper Appears On</h3><p><table><tr><th>Film Title</th><th>Digitized</th></tr>";

    $q = "SELECT DISTINCT `film`.* FROM `issue`,`page`,`film`
    WHERE
    `issue`.`newspaper_id`='$newspaper_id' AND
    `page`.`issue_id`=`issue`.`id` AND
    `film`.`id`=`page`.`film_id`
    ORDER BY `film`.`name`
    ";

    $res = do_query($q);

    while ($thispage = mysql_fetch_assoc($res)) {
	$newbody .= "<tr><td><a href='" . NPC_BASE_URL . "film/{$thispage['id']}' title='Microfilm {$thispage['name']}'>{$thispage['name']}</a></td><td>";
	if ($thispage['scanned'] == 1) {
	    $newbody .= "Yes!";
	} else {
	    $newbody .= "Not Yet!";
	}
	$newbody .= "</td></tr>";
    }
    $newbody .= "</table></p>";

    $page->body = $newbody;
}