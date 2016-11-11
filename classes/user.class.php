<?php
class User {
	public  static $RIGHTS = array('gr_id','gr_name','can_cp','can_ep','can_cm','can_em','can_at','can_tt','can_et','can_gt','can_ctt','can_ett','can_admin',);
	// ID, имя, создать, редактировать проект, создать, редактировать веху, добавить, взять, редактировать, назначить таск, создать, редактировать шаблон задачи, админить
	private $id;
	private $nickname;
	private $name;
	private $mail;
	private $password;
	private $group;
	private $section;
	private $section_name;
	private $register_date;
	private $login_date;
	private $need_check;
	private $default_color;
	public function __construct($search, $in = 'id') {
		global $db;
		if ($in != 'mysql') {
			$search = $db->execute("SELECT * FROM `users` LEFT JOIN `departments` ON `dep_id`=`department` LEFT JOIN `groups` ON `gr_id`=`group` WHERE `{$db->safe($in)}`='{$db->safe($search)}'") or die($db->error());
			if (!$search or $db->num_rows($search) != 1) {
				$this->id = -1;
				return false;
			}
			$search = $db->fetch_array($search);
		}
		$this->id = $search['id'];
		$this->nickname = $search['nickname'];
		$this->name = $search['name'];
		$this->mail = $search['mail'];
		$this->password = $search['password'];
		$this->section = $search['department'];
		$this->need_check = (isset($search['need_check']))? $search['need_check']: false;
		$this->section_name = $search['dep_name'];
		$this->register_date = $search['register_date'];
		$this->login_date = strtotime($search['online_date']);
		$this->default_color = $search['default_color'];
		foreach (self::$RIGHTS as $right)
			$this->group[$right] = $search[$right];
	}
	public function uid() {
		return $this->id;
	}
	public function login() {
		return $this->nickname;
	}
	public function need_relogin() {
		return $this->need_check;
	}
	public function full_name() {
		return $this->name;
	}
	public function group($info) {
		return (isset($this->group[$info]))?$this->group[$info]:false;
	}
	public function department() {
		return $this->section;
	}
	public function department_name() {
		return $this->section_name;
	}
	public function is_online () {
		return (time() - $this->login_date) <= 15*60? true:false;
	}
	public function update_online () {
		global $db;
		$db->execute("UPDATE `users` SET `online_date`=NOW() WHERE `id`='{$this->id}'");
		$this->login_date = time();
	}
	public function get_default_color () {
		return IssueTracker::$InterfaceColors[($this->default_color < count(IssueTracker::$InterfaceColors) and $this->default_color >= 0)? $this->default_color : 0];
	}
	public function update_default_color ($new_color_name) {
		global $db;
		if (!in_array($new_color_name, IssueTracker::$InterfaceColors))
			return;
		$new_color = array_search ($new_color_name, IssueTracker::$InterfaceColors);
		$query = $db->execute("UPDATE `users` SET `default_color`='{$db->safe($new_color)}' WHERE `id`='{$db->safe($this->id)}'") or die($db->error());
		if ($query)
			$this->default_color = $new_color;
	}
	public function check_pass($password) {
		return hash('sha256', $password) == $this->password;
	}
	public function get_avatar() {
		return (file_exists(TRACKER_ROOT . 'avatars/' . $this->id . '.png'))? ('/avatars/' . $this->id . '.png'):('/tpl/img/noavatar.png');
	}
}
