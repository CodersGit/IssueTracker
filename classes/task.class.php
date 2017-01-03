<?php
IssueTracker::LoadClass('entity');
class Task extends Entity {
	protected $id;
	protected $tid;
	protected $type;
	protected $date;
	protected $title;
	protected $closed;
	protected $content;
	protected $project;
	protected $milestone;
	protected $author_id;
	protected $developer_id;
	protected $author_nickname;
	protected $author_name;
	protected $developer_nickname;
	protected $developer_name;
	protected $milestone_name;
	protected $project_name;
	protected $progress;
	protected $last_updated;
	public static $Types = [1 => 'BG', 2 => 'EN', 3 => 'BB',];
	public function __construct(array $task) {
		$this->id = $task['tsk_id'];
		$this->tid = self::$Types[$task['tsk_type']] . $this->id;
		$this->type = $task['tsk_type'];
		$this->date = $task['tsk_date'];
		$this->title = $task['tsk_title'];
		$this->closed = (bool) $task['tsk_closed'];
		$this->content = $task['tsk_content'];
		$this->project = $task['tsk_project'];
		$this->milestone = $task['tsk_milestone'];
		$this->author_id = $task['tsk_author'];
		$this->developer_id = $task['tsk_developer'];
		$this->author_nickname = $task['author_nickname'];
		$this->author_name = $task['author_name'];
		$this->developer_nickname = $task['developer_nickname'];
		$this->developer_name = $task['developer_name'];
		$this->milestone_name = $task['milestone_name'];
		$this->project_name = $task['project_name'];
		$this->progress = (int) $task['progress'];
		$this->last_updated = ($task['last_updated'] == null)? $this->date : $task['last_updated'];
	}
	public static function GetAllWhere ($where = '1', $order = '1') {
		global $db;
		$query = $db->execute("SELECT *," .
							" (SELECT `nickname` FROM `users` WHERE `id`=`tsk_author`) AS `author_nickname`," .
							" (SELECT `name` FROM `users` WHERE `id`=`tsk_author`) AS `author_name`," .
							" (SELECT `nickname` FROM `users` WHERE `id`=`tsk_developer`) AS `developer_nickname`," .
							" (SELECT `name` FROM `users` WHERE `id`=`tsk_developer`) AS `developer_name`," .
							" (SELECT `ms_name` FROM `milestones` WHERE `ms_id`=`tsk_milestone`) AS `milestone_name`," .
							" (SELECT `pr_name` FROM `projects` WHERE `pr_id`=`tsk_project`) AS `project_name`," .
							" (SELECT SUM(`tse_progress`) FROM `task_events` WHERE `tse_task`=`tsk_id`) AS `progress`," .
							" (SELECT `tse_date` FROM `task_events` WHERE `tse_task`=`tsk_id` ORDER BY `tse_date` DESC) AS `last_updated`" .
							" FROM `tasks` WHERE " . ($where? $where:'') . ' ORDER BY ' . ($order? $order:''));
		if (!$query)
			return false;
		$tasks = array();
		while($task = $db->fetch_array($query))
			array_push($tasks, new Task($task));
		return $tasks;
	}
}