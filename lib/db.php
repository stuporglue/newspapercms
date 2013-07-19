<?php

mysql_connect(MYSQL_HN, MYSQL_UN, MYSQL_PW);
mysql_select_db(MYSQL_DB);

function do_query($q){
    $res = mysql_query($q);
    if($res){
        return $res;
    }
    // @TODO: Handle mysql errors nicely
    print "<!-- \n";
    debug_print_backtrace();
    print mysql_error();
    print "\n -->";
    die("Database error. Please notify us if this problem persists.");
}