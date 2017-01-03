<?php
class IssueTracker {
	private static $DATA;
	public static $InterfaceColors = array("black","black-light","blue","blue-light","green","green-light","purple","purple-light","red","red-light","yellow","yellow-light");

	public static function randString($pass_len = 50) {
		$allchars = "ABCDEFGHIJKLMNOPQRSYUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$string = "";

		mt_srand((double)microtime() * 1000000);

		for ($i = 0; $i < $pass_len; $i++)
			$string .= $allchars{mt_rand(0, strlen($allchars) - 1)};

		return $string;
	}

	public static function DetectTimeZone() {
		$ip = $_SERVER['REMOTE_ADDR']; // means we got user's IP address
		$json = file_get_contents( 'http://ip-api.com/json/' . $ip); // this one service we gonna use to obtain timezone by IP
// maybe it's good to add some checks (if/else you've got an answer and if json could be decoded, etc.)
		$ipData = json_decode( $json, true);
		if (isset($ipData['timezone']) and $ipData['timezone']) {
			date_default_timezone_set($ipData['timezone']);
		} else {
			date_default_timezone_set('Europe/Moscow');
		}
	}

	public static function TakeAuth() {
		global $user, $db;
		if(!isset($_COOKIE['tracker_sid'])) {
			$user = false;
			return;
		}
		$query = $db->execute("SELECT *, (need_inactive_check AND last_active < NOW() - INTERVAL 1 HOUR) AS `need_check` FROM `sessions`, `users` LEFT JOIN `departments` ON `dep_id`=`department` LEFT JOIN `groups` ON `gr_id`=`group` WHERE `uid`=`id` AND `session`='{$db->safe($_COOKIE['tracker_sid'])}'");
		if($db->num_rows($query) != 1) {
			$user = false;
			return;
		}
		$query = $db->fetch_array($query);
		$tmp = new User($query, 'mysql');
		if($tmp->uid() <= 0) {
			$user = false;
			return;
		}
		$user = $tmp;
		$user->update_online();
//		$sessionID = self::randString(128);
//		$sid_unic = $db->execute("UPDATE `sessions` SET `session`='$sessionID', `valid_until`=NOW() WHERE `sid`='{$query['sid']}'");
//		if (!$sid_unic)
//			return;
//		setcookie("tracker_sid", $sessionID, time() + 3600 * 24 * 30, '/');
//		$_COOKIE['tracker_sid'] = $sessionID;
	}

	public static function SetData($key, $value) {
		global $db;
		$query = $db->execute("INSERT INTO `data` (`key`,`value`) VALUES ('{$db->safe($key)}','{$db->safe($value)}')"
			. "ON DUPLICATE KEY UPDATE `value`='{$db->safe($value)}'");
		return self::$DATA[$key] = ($query)? $value:false;
	}

	public static function GetData($key) {
		global $db;
		if (isset(self::$DATA[$key]))
			return self::$DATA[$key];
		$query = $db->execute("SELECT `value` FROM `data` WHERE `key`='{$db->safe($key)}'");
		$value = $db->fetch_array($query);
		return self::$DATA[$key] = ($db->num_rows($query))? $value['value']:false;
	}

	public static function LoadClass ($class) {
		if (file_exists(TRACKER_ROOT . "classes/$class.class.php")) {
			require_once (TRACKER_ROOT . "classes/$class.class.php");
			return true;
		}
		return false;
	}

	public static function ShowTPL ($tpl) {
		if (file_exists(TRACKER_ROOT . "tpl/$tpl.html")) {
			include(TRACKER_ROOT . "tpl/$tpl.html");
			return true;
		}
		return false;
	}

	public static function PathTPL ($tpl) {
		if (file_exists(TRACKER_ROOT . "tpl/$tpl.html")) {
			return (TRACKER_ROOT . "tpl/$tpl.html");
		}
		return false;
	}

	public static function GeneratePagination($page, $amount_by_page, $total_amount, $link) {
		ob_start();
		self::ShowTPL("pagination/pagin_start");
		$pages_count = (int) ($total_amount / $amount_by_page + 1);
		if ($page <= 5){
			for ($p = 1; $p < $page; $p++){
				$l = $link . $p;
				include self::PathTPL("pagination/pagin_item_inactive");
			}
		} else {
			$p = "&laquo;";
			$l = $link . 1;
			include self::PathTPL("pagination/pagin_item_inactive");
			for ($p = $page - 3; $p < $page; $p++){
				$l = $link . $p;
				include self::PathTPL("pagination/pagin_item_inactive");
			}
		}
		$p = $page;
		$l = $link . $page;
		include self::PathTPL("pagination/pagin_item_active");
		if ($pages_count - $page <= 5){
			for ($p = $page + 1; $p <= $pages_count; $p++){
				$l = $link . $p;
				include self::PathTPL("pagination/pagin_item_inactive");
			}
		} else {
			for ($p = $page + 1; $p <= $page + 3; $p++){
				$l = $link . $p;
				include self::PathTPL("pagination/pagin_item_inactive");
			}
			$p = "&raquo;";
			$l = $link . $pages_count;
			include self::PathTPL("pagination/pagin_item_inactive");
		}
		self::ShowTPL("pagination/pagin_end");
		return ob_get_clean();
	}
}
