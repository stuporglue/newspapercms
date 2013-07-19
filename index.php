<?php

require_once('lib/startup.php');

// NPCPage has a __destruct method which prints itself if it hasn't been printed yet
$path = array_filter(explode('/', NPC_REQUESTED_URL));
$page = new NPCPage($path);

// @TODO Implement Search
// @TODO Implement Sitemap.xml
switch (array_shift($path)) {
    case NULL:
	$page->load('homepage');
	break;
    case 'film':
	switch ($film_id = array_shift($path)) {
	    case NULL:
		$page->load('film');
		break;
	    default:
		$page->load('film_id', Array('film_id' => $film_id));
	}
	break;
    case 'search':
	$page->load('search_results');
	break;
    case 'newspaper':
	switch ($newspaper_name = array_shift($path)) {
	    case NULL:
		$page->load('newspaper');
		break;
	    case 'help':
		$page->load('newspaper_page_help');
		break;
	    default:
		$year_or_issue = array_shift($path);
		if (is_null($year_or_issue)) {
		    $page->load('newspaper_name', Array('newspaper_name' => $newspaper_name));
		} else if (is_numeric($year_or_issue)) {
		    if ($month = array_shift($path)) {
			$page->load('newspaper_month', Array('month' => $month, 'year' => $year_or_issue, 'newspaper_name' => $newspaper_name));
		    } else {
			$page->load('newspaper_year', Array('year' => $year_or_issue, 'newspaper_name' => $newspaper_name));
		    }
		} else if (preg_match('/issue-[0-9]*/', $year_or_issue)) {
		    $issue = str_replace('issue-', '', $year_or_issue);
		    switch ($pageno = array_shift($path)) {
			case NULL:
			    $page->load('newspaper_issue', Array('newspaper_name' => $newspaper_name, 'issue' => $issue));
			    break;
			default:
			    $page->load('newspaper_page', Array('newspaper_name' => $newspaper_name, 'issue' => $issue, 'pageno' => $pageno));
		    }
		}
	}
	break;
    case 'date':
	switch ($year = array_shift($path)) {
	    case NULL:
		$page->load('date');
		break;
	    default:
		switch ($month = array_shift($path)) {
		    case NULL:
			$page->load('date_year', Array('year' => $year));
			break;
		    default:
			$page->load('date_month', Array('year' => $year, 'month' => $month));
		}
	}
	break;
    case 'download':
	$pageno = array_shift($path);
	if (preg_match('/page-/', $pageno)) {
	    $pageno = str_replace('page-', '', $pageno);
	    $page->silent = TRUE;
	    $scan = new NPCScan($pageno);
	    $scan->download();
	}
	break;
    case 'sitemap.xml':
	$page->silent = TRUE;
	new NPCSitemap();
	break;
    default:
	header("Location:" . NPC_BASE_URL);
}
