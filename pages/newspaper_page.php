<?php

// $film_id is set in index.php

$q = "SELECT `page`.`id` AS `page_id`,`newspaper`.*,`issue`.*,`page`.*,`film`.*
    FROM `newspaper`,`issue`,`page`,`film`
    WHERE
    `newspaper`.`urltitle`='" . mysql_real_escape_string($newspaper_name) . "' AND
	`issue`.`newspaper_id`=`newspaper`.`id` AND
	`issue`.`id` = '$issue' AND
	`page`.`issue_id`=`issue`.`id` AND
	`page`.`page_no`='$pageno' AND
	`page`.`film_id`=`film`.`id`
	";

$res = do_query($q);

if (mysql_num_rows($res) == 0) {
    $page->title = "$newspaper_name issue $issue, Page $pageno";
    $page->body = "<p>No issues found in the database for this newspaper and year.</p>";
    $page->body .= "<p>If they are supposed to be here, we are probably still digitizing them.</p>";
} else {
    if (!($thispage = mysql_fetch_assoc($res))) {
	$page->title = "Newspaper Not Found";
	$page->body = "The specified newspaper was not found";
    } else {
	$page->title = "{$thispage['title']}: " . date("F m, Y", strtotime($thispage['date'])) . ", page $pageno";

	$page->js[] = "/js/pixastic/pixastic.core.js";
	$page->js[] = "/js/pixastic/actions/rotate.js";
	$page->js[] = "/js/pixastic/actions/fliph.js";
	$page->js[] = "/js/pixastic/actions/invert.js";
	$page->js[] = "/js/pixastic/actions/brightness.js";
	$page->js[] = "/js/pixastic/actions/resize.js";
	$page->js[] = "/js/pixastic/actions/removenoise.js";
	$page->js[] = "/js/pixastic/actions/unsharpmask.js";
	$page->js[] = "/js/zoom.js";
	$page->js[] = "/js/image_editor.js";

	$scan = new NPCScan($thispage['page_id']);
	$newbody = "<div id='pagenav'><ul>";

	if ($url = $scan->firstPage()) {
	    $newbody .= "<li><a href='$url'><img src='" . NPC_BASE_URL . "img/first.png' alt='First page of this issue' title='Previous page of this issue'/></a></li>";
	}

	if ($url = $scan->prevLink()) {
	    $newbody .= "<li><a href='$url'><img src='" . NPC_BASE_URL . "img/prev.png' alt='Previous page of this newspaper' title='Previous page of this newspaper'/></a></li>";
	}

	$newbody .= "<li><a href='" . $scan->getDownloadUrl() . "'><img src='" . NPC_BASE_URL . "img/download.png' alt='Download this page' title='Download this page'/></a></li>";

	$newbody .= "<li class='allpages'><select title='Select a page to go to' id='allpageselect' onchange='window.location = \"" . NPC_BASE_URL . "newspaper/$newspaper_name/issue-$issue/\" + this.value;' class='allpages'>";
	foreach ($scan->allPageLinks() as $page_no => $url) {
	    $newbody .= "<option" . ($page_no == $pageno ? " selected='selected' class='selected'" : '') . ">$page_no</option>";
	}
	$newbody .= "</select></li>";

	if ($url = $scan->nextLink()) {
	    $newbody .= "<li><a href='$url'><img src='" . NPC_BASE_URL . "img/next.png' alt='Next page of this newspaper' title='Next page of this newspaper'/></a></li>";
	}

	if ($url = $scan->lastPage()) {
	    $newbody .= "<li><a href='$url'><img src='" . NPC_BASE_URL . "img/last.png' alt='Last page of this issue' title='Last page of this issue'/></a></li>";
	}

	$newbody .= "<li><a href='" . NPC_BASE_URL . "newspaper/help'><img src='" . NPC_BASE_URL . "img/help.png' alt='Help Me!' title='Help Me!'/></a></li>";

	$newbody .= "
	    </ul>
	</div>
	<div id='pageeditor'>
	<ul>
	    <li><img src='" . NPC_BASE_URL . "img/reset.png' alt='Reset' title='Reset' id='edit_reset' onclick='reset();'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/rotatec.png' alt='Rotate Right 90 degrees' title='Rotate Right 90 degrees' id='edit_rotatec' onclick='rotate(-90);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/rotatecc.png' alt='Rotate Left 90 degrees' title='Rotate Left 90 degrees' id='edit_rotatecc' onclick='rotate(90);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/zoomin.png' alt='Zoom In' title='Zoom In' id='edit_zoomin' onclick='zoom(1.2);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/zoomout.png' alt='Zoom Out' title='Zoom Out' id='edit_zoomout' onclick='zoom(.8);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/mirror.png' alt='View the horizontal mirror image' title='View the horizontal mirror image' id='edit_mirror' onclick='mirror();'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/invert.png' alt='Invert colors' title='Invert colors' id='edit_invert' onclick='invert();'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/brighter.png' alt='Lighten the image' title='Ligten the image' id='edit_lighter' onclick='lighten(25);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/darker.png' alt='Darken the image' title='Darken the image' id='edit_darker' onclick='lighten(-25);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/morecontrast.png' alt='Increase contrast' title='Increase contrast' id='edit_morecontrast' onclick='contrast(0.1);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/lesscontrast.png' alt='Decrease contrast' title='Decrease contrast' id='edit_lesscontrast' onclick='contrast(-0.1);'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/denoise.png' alt='Denoise the image' title='Denoise the image' id='edit_denoise' onclick='denoise()'/></li>
	    <li><img src='" . NPC_BASE_URL . "img/unsharpmask.png' alt='Sharpen the image with an unsharp mask' title='Sharpen the image with the unsharp mask' id='edit_unsharp' onclick='unsharp()'/></li>
	    <li id='edit_busyli'><img src='" . NPC_BASE_URL . "img/busy.gif' alt='An image operation is in progress' title='An image operation is in progress' id='edit_busysignal'/></li>
	</ul>
	<noscript><p>The viewing tools require JavaScript and a
	browser which supports the HTML5 Canvas. The latest version
	of any popular browser should work.</p></noscript>
	</div>";

	$alttitle = addslashes("{$thispage['title']}: " . date("F m, Y", strtotime($thispage['date'])) . ", page $pageno");

	$newbody .= "
	<div id='pageimgdiv'>
	    <canvas id='pageimg' class='medium_online' style='' title='$alttitle'>
		<img src='" . $scan->getMediumUrl() . "' class='medium_online' alt='$alttitle' title='$alttitle'/>
	    </canvas>
	</div>
	<div id='origimg' style='display:none'>
	    <img id='noncanvaspageimg' src = '" . $scan->getMediumUrl() . "' class = 'medium_online' alt = '$alttitle' title = '$alttitle'/>
	</div>";

	$newbody .= "<div id = 'pagedetails'>";

	$newbody .= "<h3>OCR Results</h3><p>
	    These OCR results are generated automatically. In a future version of our site you will be able to submit corrections.
	</p>";

	$newbody .= "<p class='ocrtext'>" . $scan->ocr() . "</p>";

	$newbody .= "</div>";

	// Dublin Core metadata
	$dublinCore = Array(
	    // http://dublincore.org/documents/2000/07/16/usageguide/#usinghtml
	    // Content
	    //'Coverage' => NULL,
	    'Description' => "Digital copy of {$page->title}",
	    'Type' => "Digitized image of a single newspaper page",
	    'Relation' => $thispage['url'],
	    'Source' => $page->title,
	    'Title' => $page->title,
	    // Intellectual Property
	    //'Contributor' => NULL,
	    'Creator' => $thispage['title'],
	    'Publisher' => $thispage['title'],
	    'Rights' => $page->import('copyright'),
	    //Instantiation
	    'Date' => date('Y-m-d', strtotime($thispage['date'])),
	    'Format' => $scan->getMime(),
	    'Identifier' => $page->title,
		//'Language' => NULL,
	);

	global $DC_PAGE_DEFAULTS;
	$page->dublinCore = array_merge($page->dublinCore, $dublinCore, $DC_PAGE_DEFAULTS);

	$page->body = $newbody;
    }
}
