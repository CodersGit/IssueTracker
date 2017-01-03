<?php
$page_title = 'Дашборд';
IssueTracker::LoadClass('task');
$old_tasks = Task::GetAllWhere("`tsk_developer`='{$db->safe($user->uid())}' AND NOT `tsk_closed`", '`last_updated` ASC LIMIT 5');// OR `tsk_author`='{$db->safe($user->uid())}'
$free_tasks = Task::GetAllWhere("`tsk_developer`='0'", '`tsk_date` ASC LIMIT 5');
include IssueTracker::PathTPL("header");
include IssueTracker::PathTPL("sidebar_left");
include IssueTracker::PathTPL("dashboard");
include IssueTracker::PathTPL("sidebar_right");
include IssueTracker::PathTPL("footer");
