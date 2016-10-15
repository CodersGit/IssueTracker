<?php
$redirect_url = (isset($_REQUEST['redirect_url']))? $_REQUEST['redirect_url'] : $page;
if (!$user->need_relogin())
	header("Location: /" . $redirect_url);
$error = '';
if (isset($_POST['password'])) {
	if ($user->check_pass($_POST['password'])) {
		$db->execute("UPDATE `sessions` SET `last_active`=NOW() WHERE `session`='{$db->safe($_COOKIE['tracker_sid'])}'");
		header("Location: /" . $redirect_url);
		exit();
	} else $error = "Неверный пароль";
}
include IssueTracker::PathTPL("relogin");
