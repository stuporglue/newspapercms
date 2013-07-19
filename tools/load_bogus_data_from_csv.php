<?php

// issue_id,page_no,newspaper_id,large
$file = file_get_contents('test_delete.csv');
$lines = explode("\n",trim(rtrim($file)));

require_once('../conf/config.inc');
require_once('../lib/db.php');

$newspapers = Array();
$issues = Array();

foreach($lines as $line){
  $line = explode(',',$line);
  // Create newspaper
  if(!array_key_exists($line[2],$newspapers)){
    $title = implode(' ',array_map('ucfirst',explode('_',$line[2])));
    $q = "INSERT INTO `newspaper`
      (`title`,`urltitle`,`start_date`,`end_date`) VALUES
      ('$title','{$line[2]}','1950-04-15','1990-10-25')";
    do_query($q);
    $newspapers[$line[2]] = mysql_insert_id();
  }

  // Create issue
  if(!array_key_exists($line[0],$issues)){
    $q = "INSERT INTO `issue` (`newspaper_id`,`date`) VALUES
      ('{$newspapers[$line[2]]}','".rand(1950,1990)."-".rand(1,12)."-".rand(1,28)."')";
    do_query($q);
    $issues[$line[0]] = mysql_insert_id();
  }

  // Insert page
  $q = "INSERT INTO `page` (`issue_id`,`page_no`,`film_id`,`slide_id`,`large`) VALUES
    ('{$issues[$line[0]]}','{$line[1]}','113','".rand(0,600)."','{$line[3]}')";
  do_query($q);
}
