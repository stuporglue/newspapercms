<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class NPCSitemap {
    var $sitemap = "";
    //put your code here
    function __construct(){
	$this->header();
	$this->pages();
	$this->newspapers();
	$this->films();
	$this->sitemap .= '</urlset>';
	print $this->sitemap;
    }

    function header(){
	$this->sitemap = '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="'.NPC_BASE_URL.'css/sitemap.xsl"?>
<!-- generator="newspapercms/"' . NPC_VERSION . ' -->
<!-- generated-on="' . date('F j, Y g:i a') . '" -->
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    function pages(){
        $q = "SELECT `page`.*,`newspaper`.`urltitle`,`issue`.`id` AS `issue_id` FROM `page`,`issue`,`newspaper` WHERE
	   `page`.`issue_id`=`issue`.`id` AND
	   `issue`.`newspaper_id`=`newspaper`.`id`";

        $res = do_query($q);
        while($row = mysql_fetch_assoc($res)){
	   $this->newURL(NPC_BASE_URL . "newspaper/{$row['urltitle']}/{$row['issue_id']}/{$row['page_no']}/",strtotime($row['modified']),'weekly','0.6');
        }
    }

    function newspapers(){
        $q = "SELECT * FROM `newspaper`";
        $res = do_query($q);
        while($row = mysql_fetch_assoc($res)){
	   $this->newURL(NPC_BASE_URL . "newspaper/{$row['urltitle']}/",strtotime($row['modified']),'monthly','0.3');
        }
    }

    function films(){
        $q = "SELECT * FROM `film`";
        $res = do_query($q);
        while($row = mysql_fetch_assoc($res)){
	   $this->newURL(NPC_BASE_URL . "film/{$row['id']}/",strtotime($row['modified']),'monthly','0.2');
        }
    }

    function newURL($url,$mod,$freq,$priority){
	$this->sitemap .= "<url>
		<loc>$url</loc>
		<lastmod>".date('Y-m-dTH:i:s+00:00',$mod)."</lastmod>
		<changefreq>$freq</changefreq>
		<priority>$priority</priority>
	</url>";
    }
}
