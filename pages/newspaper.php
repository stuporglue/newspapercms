<?php

$page->title = "List of newspapers available";

$q = "SELECT *
    FROM `newspaper`
    ORDER BY `start_date`";

$res = do_query($q);

$newbody = "<h3>The Following Newspapers are Available</h3>";
$newbody .= "<ol>";
while($thispage = mysql_fetch_assoc($res)){
    $newbody .= "<li><a href='" . $thispage['urltitle'] . "/' title='Newspaper ". htmlentities($thispage['title']) ."'>";
    $newbody .= $thispage['title'] . " (" . date('Y',strtotime($thispage['start_date'])) . "-" . date('Y',strtotime($thispage['end_date'])) . ")";
    $newbody .= "</a></li>";
}
$newbody .= "</ol>";
$page->title = "List of Available Newspapers";
$page->body = $newbody;