<?php
define('TRACKER_ROOT', dirname(__FILE__).'/');
$root_url = str_replace('\\', '/', $_SERVER['PHP_SELF']);
$root_url = explode("index.php", $root_url, -1);
define('TRACKER_BASE', (sizeof($root_url))? $root_url[0]:'/');
require ("config.php");
date_default_timezone_set('Europe/Moscow');
require ("classes/sys.class.php");
IssueTracker::LoadClass('db');
IssueTracker::LoadClass('user');
IssueTracker::LoadClass('menu');
$db = new DB($config['db_base'],$config['db_host'],$config['db_user'], $config['db_pass'], $config['db_port']);
$db->connect();
IssueTracker::TakeAuth();
$main_page = 'dashboard';
$page = (isset($_GET['page']))? $_GET['page']:"dashboard"; //Here is default page
$lnk = explode('/', $page);
$mode = (isset($lnk[0]))? $lnk[0]: $main_page;
include (!$user)?(TRACKER_ROOT . "pages/login.php"):((file_exists(TRACKER_ROOT . "pages/$mode.php"))? (TRACKER_ROOT . "pages/$mode.php"): (TRACKER_ROOT . "pages/404.php"));
