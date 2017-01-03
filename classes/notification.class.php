<?php
IssueTracker::LoadClass('entity');
class Notification extends Entity {
	function __construct ($id) {
		//TODO make Notification constuctor
	}

	function get ($parameter) {
		return parent::get($parameter);
	}
	public static function GetAllWhere ($where = '1', $order = '1') {
		global $db;
		$query = $db->execute("SELECT `not_id`, `not_type` FROM `notifications` WHERE " . ($where? $where:'') . ' ORDER BY ' . ($order? $order:''));
		if (!$query)
			return false;
		$notifications = array();
		while($notification = $db->fetch_array($query))
			array_push($notifications, new Notification($notification));
		return $notifications;
	}
}