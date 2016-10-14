<?php
class Project {
	private $id;
	private $name;
	public function __construct($id) {
		global $db;
		$query = $db->execute("SELECT * FROM `projects` WHERE `pr_id`='{$db->safe($id)}'");
		if ($db->num_rows($query) != 1) {
			$this->id = -1;
			$this->name = false;
			return;
		}
		$this->id = $query['pr_id'];
		$this->name = $query['pr_name'];
	}

	public function pr_id () { return $this->id; }
	public function pr_name () { return $this->name; }

	public function join ($uid) {
		global $db;
		$user = new User($uid);
		if ($user->uid() < 1) return 1;
		if (!$db->num_rows($db->execute("SELECT `id` FROM `projects_developers` WHERE `pd_project`='{$this->id}' AND `pd_developer`='{$user->uid()}'")))
			return ($db->execute("INSERT INTO `projects_developers` (`pd_project`,`pd_developer`) VALUES ('{$this->id}','{$user->uid()}')"))? 0:2;
		return 3;
	}

	public function join_current_user () {
		global $db, $user;
		if (!$db->num_rows($db->execute("SELECT `id` FROM `projects_developers` WHERE `pd_project`='{$this->id}' AND `pd_developer`='{$user->uid()}'")))
			return ($db->execute("INSERT INTO `projects_developers` (`pd_project`,`pd_developer`) VALUES ('{$this->id}','{$user->uid()}')"))? 0:2;
		return 3;
	}

	public static function CreateProject ($name) {
		global $db;
		$success = $db->execute("INSERT INTO `projects` (`pr_name`) VALUES ('{$db->safe(mb_strcut($name,0,127,'utf8'))}')");
		return ($success)? new Project($db->insert_id()):false;
	}
}