<?php

$q = "SELECT * FROM `film`
    ORDER BY `name`
    ";
$res = do_query($q);

$newbody = "The following microfilms are available:";
$newbody .= "<ul>";
while($thispage = mysql_fetch_assoc($res)){
    $newbody .= "<li><a href='" . $thispage['id'] . "/'>" . $thispage['name'] . "</a></li>";
}
$newbody .= "</ul>";
$page->title = "List of Available Microfilms";
$page->body = $newbody;
