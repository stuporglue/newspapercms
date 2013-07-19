<?php

$q = "SELECT DISTINCT YEAR(`date`) AS `year`
    FROM `issue`
    ORDER BY `date`
    ";
$res = do_query($q);

$newbody = "Browse our collection by date. Start by choosing a year, then you can browse by microfilm, newspaper, or month.";

$centuries_found = Array();

while($thispage = mysql_fetch_assoc($res)){
    $year = $thispage['year'];
    $century = preg_replace('/(..)(..)/', "\${1}00", $year);
    if (!in_array($century, $centuries_found)) {
        if (!empty($centuries_found)) {
            $newbody .= implode(' | ', $year_urls);
            $newbody .= "</p>";
        }
        $newbody .= "<h3>$century</h3><p>";
        $centuries_found[] = $century;
    }
    $year_urls[] = "<a href='" . NPC_BASE_URL . "date/$year' title='Browse microfilms and newspapers from the year $year'>$year</a>";
}
$newbody .= implode(' | ', $year_urls);
$newbody .= "</p>";

$page->body = $newbody;
$page->title = "Browse our papers and films by date";