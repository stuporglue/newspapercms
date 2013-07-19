<?php

header("Content-type: text/plain");

set_time_limit(0);

require_once('../lib/startup.php');

$morepages = "SELECT `newspaper`.`title`,`issue`.`date`,`page`.`page_no`,`page`.`id`
FROM `newspaper`,`issue`,`page`
WHERE `page`.`ocr` IS NULL AND
`page`.`issue_id`=`issue`.`id` AND
`issue`.`newspaper_id`=`newspaper`.`id`
ORDER BY `issue`.`date`,`page`.`page_no`
LIMIT 10
";


$res = do_query($morepages);

while (mysql_num_rows($res) > 0) {
    while ($row = mysql_fetch_assoc($res)) {
	print "OCRing {$row['title']}, issue {$row['date']}, page {$row['page_no']}...";
	flush();
	$scan = new NPCScan($row['id']);
	$ocr = $scan->doOcr();
	if ($ocr !== FALSE) {
	    print "SUCCESS!\n";
	} else {
	    print "ERROR!\n";
	}
    }
    $res = do_query($morepages);
}