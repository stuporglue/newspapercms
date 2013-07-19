<?php

// $film_id is set in index.php

$q = "SELECT * FROM `newspaper`
    WHERE
    `newspaper`.`urltitle`='" . mysql_real_escape_string($newspaper_name) . "'";
//AND
//    `issue`.`date` >= '" . mysql_real_escape_string($year) . "-01-01' AND
//    `issue`.`date` <= '" . mysql_real_escape_string($year) . "-12-31'
//	";

$res = do_query($q);

if (mysql_num_rows($res) == 0) {
    $page->title = "$newspaper_name issues in $year";
    $page->body = "<p>No issues found in the database for this newspaper and year.</p>";
    $page->body .= "<p>If they are supposed to be here, we are probably still digitizing them.</p>";
} else {
    if (!($thispage = mysql_fetch_assoc($res))) {
	$page->title = "Newspaper Not Found";
	$page->body = "The specified newspaper was not found";
    } else {
	$newspaper_id = $thispage['id'];
	$newspaper_title = $thispage['title'];
	$page->title = "$newspaper_title issues in $year";

	$month_text = date("F", mktime(0, 0, 0, ($month)));
	$newbody = "<h3>List of issues from $month_text, $year</h3>";


	// List of issues
	$q = "SELECT `issue`.*,`page`.`page_no` FROM `issue`,`page`
	WHERE `issue`.`newspaper_id`='$newspaper_id' AND
	YEAR(`date`) = '$year' AND
	MONTH(`date`) = '$month'
	GROUP BY `issue`.`id`
	ORDER BY `date`,`page`.`page_no`
	";

	$res = do_query($q);

	$newbody .= "<ul>";
	while ($thispage = mysql_fetch_assoc($res)) {
	    $newbody .= "<li><a href='" . NPC_BASE_URL . "newspaper/$newspaper_name/issue-{$thispage['id']}/{$thispage['page_no']}' title='" .
		    date('F d, Y', strtotime($thispage['date'])) . " issue of " . htmlentities($newspaper_name)
		    . "'>" . date('F d, Y', strtotime($thispage['date'])) . " issue of " . htmlentities($newspaper_name) . "</a></li>";
	}
	$newbody .= "</ul>";

	$page->body = $newbody;
    }
}