<?php
$redirect_url = (isset($_REQUEST['redirect_url']))? $_REQUEST['redirect_url'] : $page;
if (isset($_POST['login']) and isset($_POST['password'])) {
	$user = new User($_POST['login'], 'nickname');
	if ($user->uid() != -1 and $user->check_pass($_POST['password'])) {
		$sessionID = IssueTracker::randString(128);
		$db->execute("INSERT INTO `sessions` (`uid`,`session`,`valid_until`,`last_active`,`need_inactive_check`) VALUES ('{$user->uid()}', '{$db->safe($sessionID)}', NOW(), NOW(),'" . ((isset($_POST['remember']))?0:1) . "')");
		setcookie("tracker_sid", $sessionID, time() + 3600 * 24 * 30, '/');
		header("Location: /" . $redirect_url);
		exit();
	}
}
include IssueTracker::PathTPL("login");
