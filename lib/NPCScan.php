<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NPCScan
 *
 * @author admin
 */
class NPCScan {

    var $id;
    var $row;

    function __construct($page_id) {
	$this->id = mysql_real_escape_string($page_id);

	$q = "SELECT
	    `page`.`id` AS `page_id`,`page`.*,
	    `issue`.`id` AS `issue_id`,`issue`.*,
	    `newspaper`.`id` AS `newspaper_id`,`newspaper`.*,
	    `film`.`id` AS `film_id`,`film`.*
	    FROM
	    `page`,
	    `issue`,
	    `newspaper`,
	    `film`
	    WHERE
	    `page`.`id`='{$this->id}' AND
	    `page`.`issue_id`=`issue`.`id` AND
	    `issue`.`newspaper_id`=`newspaper`.`id` AND
	    `page`.`film_id`=`film`.`id`
		";
	$res = do_query($q);
	if ($row = mysql_fetch_assoc($res)) {
	    $this->row = $row;
	} else {
	    throw new Exception("Scan not found!");
	}
    }

    /**
     * @brief Return the medium sized URL
     *
     * @todo Make this handle CDNs etc.
     *
     * @return string A URL for downloading or using in an <img> tag
     */
    function getMediumUrl() {
	$medium = NPC_MEDIUM_PATH . $this->row['image'];
	$large = NPC_LARGE_PATH . $this->row['image'];
	if (!file_exists($medium)) {
	    if (!file_exists($large)) {
		throw new Exception("Large image not found at $large");
	    }
	    $destDir = dirname($medium);
	    if (!file_exists($destDir)) {
		mkdir($destDir, '2775', TRUE);
	    }
	    $cmd = escapeshellcmd(IM_CONVERT) . " $large -resize " . escapeshellarg(IM_MEDIUM_RESIZE_OPTIONS) . " $medium";
	    shell_exec($cmd);
	}
	return NPC_CDN_BASE_URL . 'medium/' . $this->row['image'];
    }

    function getLargeUrl() {
	$large = NPC_LARGE_PATH . $this->row['image'];
	if (!file_exists($large)) {
	    throw new Exception("Large image not found at $large");
	}
	return NPC_CDN_BASE_URL . $this->row['image'];
    }

    // Get download URL
    function getDownloadUrl() {
	return NPC_BASE_URL . "download/page-{$this->id}/" . $this->fileName();
    }

    // Headers to download file
    function download() {
	$large = NPC_LARGE_PATH . $this->row['image'];

	if (!file_exists($large)) {
	    return; // NPCPage will 404 for us
	}

	header('Content-Description: File Transfer');
	header("Content-Type: " . $this->getMime());
	header('Content-Disposition: attachment; filename=' . $this->fileName());
	header('Content-Length: ' . filesize($large));
	readfile($large);
    }

    function getMime(){
	$large = NPC_LARGE_PATH . $this->row['image'];
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $large);
	return $mime;
    }

    // FS friendly name
    function fileName() {
	$q = "SELECT * FROM `newspaper`,`issue` WHERE
	    `newspaper`.`id`=`issue`.`newspaper_id` AND
	    `issue`.`id`='{$this->row['issue_id']}'";

	$res = do_query($q);

	if (!($row = mysql_fetch_assoc($res))) {
	    return basename($this->row['image']);
	}

	$extension = pathinfo($this->getLargeUrl(), PATHINFO_EXTENSION);
	$filename = $row['title'] . " issue " . $row['date'] . " page " . $this->row['page_no'] . "." . $extension;
	$filename = preg_replace('/[^a-zA-Z0-9-._]/', '_', $filename);
	$filename = preg_replace('/__*/', '_', $filename);
	return $filename;
    }

    // Previous page, even in other newspaper
    function prevLink() {
	$q = "SELECT `issue`.`id` AS `issue_id`,`page`.*,`issue`.* FROM `issue`,`page`
		  WHERE `issue`.`newspaper_id`='{$this->row['newspaper_id']}' AND
		  `issue`.`id`=`page`.`issue_id` AND
		  (
		      (
		      `issue`.`date` = '{$this->row['date']}' AND
		      `page`.`page_no` < '{$this->row['page_no']}'
		      ) OR
		      `issue`.`date` < '{$this->row['date']}'
		  )
		  ORDER BY `issue`.`date` DESC,`page`.`page_no` DESC
		  LIMIT 1
		  ";

	$res = do_query($q);
	if ($row = mysql_fetch_assoc($res)) {
	    return NPC_BASE_URL . "newspaper/{$this->row['urltitle']}/issue-{$row['issue_id']}/{$row['page_no']}";
	}
	return FALSE;
    }

    // Next page, even in other newspaper
    function nextLink() {
	$q = "SELECT `issue`.`id` AS `issue_id`,`page`.*,`issue`.* FROM `issue`,`page`
		  WHERE `issue`.`newspaper_id`='{$this->row['newspaper_id']}' AND
		  `issue`.`id`=`page`.`issue_id` AND
		  (
		      (
		      `issue`.`date` = '{$this->row['date']}' AND
		      `page`.`page_no` > '{$this->row['page_no']}'
		      ) OR
		      `issue`.`date` > '{$this->row['date']}'
		  )
		  ORDER BY `issue`.`date`,`page`.`page_no`
		  LIMIT 1
		  ";

	$res = do_query($q);
	if ($row = mysql_fetch_assoc($res)) {
	    return NPC_BASE_URL . "newspaper/{$this->row['urltitle']}/issue-{$row['issue_id']}/{$row['page_no']}";
	}
	return FALSE;
    }

    // First page of current newspaper
    function firstPage() {
	$q = "SELECT * FROM `page` WHERE
	    `issue_id`='{$this->row['issue_id']}'
	    ORDER BY `page_no`
	    LIMIT 1";
	$res = do_query($q);
	if ($row = mysql_fetch_assoc($res)) {
	    if ($row['page_no'] != $this->row['page_no']) {
		return NPC_BASE_URL . "newspaper/{$this->row['urltitle']}/issue-{$this->row['issue_id']}/{$row['page_no']}";
	    }
	}
	return FALSE;
    }

    // Last page of current newspaper
    function lastPage() {
	$q = "SELECT * FROM `page` WHERE
	    `issue_id`='{$this->row['issue_id']}'
	    ORDER BY `page_no` DESC
	    LIMIT 1";
	$res = do_query($q);
	if ($row = mysql_fetch_assoc($res)) {
	    if ($row['page_no'] != $this->row['page_no']) {
		return NPC_BASE_URL . "newspaper/{$this->row['urltitle']}/issue-{$this->row['issue_id']}/{$row['page_no']}";
	    }
	}
	return FALSE;
    }

    // Links to every page of the paper
    function allPageLinks(){
	$q = "SELECT `page_no` FROM `page` WHERE `issue_id`='{$this->row['issue_id']}' ORDER BY `page_no`";
	$res = do_query($q);
	$pageLinks = Array();

	while($row = mysql_fetch_assoc($res)){
		$pageLinks[$row['page_no']] = NPC_BASE_URL . "newspaper/{$this->row['urltitle']}/issue-{$this->row['issue_id']}/{$row['page_no']}";
	}

	return $pageLinks;
    }

    function doOcr(){
	$large = NPC_LARGE_PATH . $this->row['image'];

	$tf = tempnam(sys_get_temp_dir(),'ocr');
	$cmd = TESSERACT . " $large $tf >> /dev/null 2>&1; echo $?";
	$res = shell_exec($cmd);
	if($res != 0){
	    var_dump($res);
	    return FALSE;
	}
	$ocr = file_get_contents("$tf.txt");
	unlink($tf);
	unlink("$tf.txt");

	$q = "UPDATE `page` SET `ocr`='" . mysql_real_escape_string($ocr) . "' WHERE `id`='{$this->id}'";
	do_query($q);

	return $ocr;
    }

    /**
     * Get the OCR for this page, creating it if needed
     * @option bool $createNow (optional) Create OCR on the fly, if needed?
     * @return string
     */
    function ocr($createNow = FALSE){
	if(!is_null($this->row['ocr'])){
	    return  $this->row['ocr'];
	}

	if($createNow){
	    if($ocr = $this->doOcr()){
		return $ocr;
	    }
	}

	return "This page has not been OCRed yet. Please check back soon.";
    }
}
