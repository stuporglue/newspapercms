<?php

$q = "SELECT DISTINCT `newspaper`.* FROM `newspaper`,`issue` WHERE
`issue`.`newspaper_id`=`newspaper`.`id` AND
YEAR(`issue`.`date`)='$year' AND
MONTH(`issue`.`date`)='$month'
ORDER BY `issue`.`date`
";

$res = do_query($q);

$month_str = date('F', mktime(0, 0, 0, $month));
$newbody = "<h3>Newspapers published in $month_str, $year</h3><p><ul>";
while ($thispage = mysql_fetch_assoc($res)) {
    $newbody .= "<li><a href='" . NPC_BASE_URL . "newspaper/{$thispage['urltitle']}/$year' title='Browse " . htmlentities($thispage['title']) . " issues from $year'>";
    $newbody .= $thispage['title'];
    $newbody .= "</a></li>";
}
$newbody .= "</ul></p>";

$q = "SELECT DISTINCT `film`.* FROM `issue`,`page`,`film`
     WHERE YEAR(`issue`.`date`)='$year' AND
     MONTH(`issue`.`date`)='$month' AND
    `page`.`issue_id`=`issue`.`id` AND
    `page`.`film_id`=`film`.`id`
    ORDER BY `film`.`name`
";
$newbody .= "<h3>Microfilms published in $year</h3><p><ul>";
$res = do_query($q);
while ($thispage = mysql_fetch_assoc($res)) {
    $newbody .= "<li><a href='" . NPC_BASE_URL . "film/{$thispage['id']}' title='View contents of microfilm {$thispage['name']}'>{$thispage['name']}</a></li>";
}
$newbody .= "</ul></p>";

$page->title = "Newspapers and Microfilms from $year";
$page->body = $newbody;