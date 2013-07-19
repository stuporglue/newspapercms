<?php

require_once('../conf/config.inc');
require_once('../lib/db.php');

$q = "INSERT IGNORE INTO `film_newspaper` (`film_id`,`newspaper_id`) SELECT DISTINCT `film_id`,`newspaper_id` FROM `page`,`issue` WHERE
  `page`.`issue_id`=`issue`.`id`";
do_query($q);
