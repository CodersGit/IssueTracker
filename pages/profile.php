<?php
$tmp_user = new User($lnk[1], 'nickname');
$page_title = "Профиль пользователя - " . $tmp_user->login();
include IssueTracker::PathTPL("header");
include IssueTracker::PathTPL("sidebar_left");
include IssueTracker::PathTPL("profile");
include IssueTracker::PathTPL("sidebar_right");
include IssueTracker::PathTPL("footer");