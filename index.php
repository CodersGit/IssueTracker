<?php
define('TRACKER_ROOT', dirname(__FILE__).'/');
define('TRACKER', '1');
$root_url = str_replace('\\', '/', $_SERVER['PHP_SELF']);
$root_url = explode("index.php", $root_url, -1);
define('TRACKER_BASE', (sizeof($root_url))? $root_url[0]:'/');
setlocale(LC_ALL, 'ru_RU.utf8', 'rus_RUS.utf8', 'Russian_Russia.utf8');
require ("config.php");
require("classes/issuetracker.class.php");
IssueTracker::DetectTimeZone();
IssueTracker::LoadClass('db');
IssueTracker::LoadClass('user');
IssueTracker::LoadClass('menu');
$db = new DB($config['db_base'],$config['db_host'],$config['db_user'], $config['db_pass'], $config['db_port']);
$db->connect();
IssueTracker::TakeAuth();
$main_page = 'dashboard';
$page = (isset($_GET['page']))? $_GET['page']:"dashboard"; //Here is default page
$lnk = explode('/', $page);
if ($user) {
	IssueTracker::LoadClass('task');
	$header_tasks = Task::GetAllWhere("`tsk_developer`='{$db->safe($user->uid())}' AND NOT `tsk_closed`", '`last_updated` ASC') or die ($db->error());
}
$mode = (isset($lnk[0]))? $lnk[0]: $main_page;
include (!$user && $mode != 'api')?(TRACKER_ROOT . "pages/login.php"):(($mode != 'api' && $user->need_relogin())?(TRACKER_ROOT . "pages/relogin.php"):((file_exists(TRACKER_ROOT . "pages/$mode.php"))? (TRACKER_ROOT . "pages/$mode.php"): (TRACKER_ROOT . "pages/404.php")));
