<?php
if (!$user) {
	include(TRACKER_ROOT . "pages/login.php");
	exit();
}
$page_title = 'Страница не найдена';
header("HTTP/1.0 404 Not Found");
include IssueTracker::PathTPL("header");
include IssueTracker::PathTPL("sidebar_left");
include IssueTracker::PathTPL("404");
include IssueTracker::PathTPL("sidebar_right");
include IssueTracker::PathTPL("footer");