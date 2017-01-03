<?php
$redirect_url = (isset($_REQUEST['redirect_url']))? $_REQUEST['redirect_url'] : $page;
$code = 888;
$message = '';
$params = array();
if(!isset($lnk[1])) $lnk[1] = '404';
switch ($lnk[1]) {
	case 'relogin':
		if($user) {
			if(isset($_POST['password'])) {
				if($user->check_pass($_POST['password'])) {
					$db->execute("UPDATE `sessions` SET `last_active`=NOW() WHERE `session`='{$db->safe($_COOKIE['tracker_sid'])}'");
					$code = 0;
					$message = 'success';
				} else {
					$code = 1;
					$message = "Неверный пароль";
				}
			} else {
				$code = 2;
				$message = 'Пароль не передан';
			}
		} else {
			$code = 3;
			$message = 'Вы не залогинены';
		}
		break;
	case 'logout':
		if($user) {
			User::Logout();
		} else {
			$code = 1;
			$message = 'Вы не залогинены';
		}
		break;
	case 'tasks':
		IssueTracker::LoadClass('task');
		if($user and !$user->need_relogin()) {
			$tasks = Task::GetAllWhere("`tsk_developer`='{$db->safe($user->uid())}' AND NOT `tsk_closed`", '`last_updated` ASC');
			$code = 0;
			$params['count'] = count($tasks);
			ob_start();
			foreach($tasks as $task) {
				include IssueTracker::PathTPL("api/task");
			}
			$message = ob_get_clean();
		} else {
			$code = 1;
			$message = 'Вы не залогинены';
		}
		break;
	case 'login':
		if(isset($_POST['login']) and isset($_POST['password'])) {
			$user = new User($_POST['login'], 'nickname');
			if($user->uid() != -1 and $user->check_pass($_POST['password'])) {
				$sessionID = IssueTracker::randString(128);
				$db->execute("INSERT INTO `sessions` (`uid`,`session`,`valid_until`,`last_active`,`need_inactive_check`) VALUES ('{$user->uid()}', '{$db->safe($sessionID)}', NOW(), NOW(),'" . ((isset($_POST['remember']))? 0 : 1) . "')");
				setcookie("tracker_sid", $sessionID, time() + 3600 * 24 * 30, '/');
				$code = 0;
				$message = 'success';
			} else {
				$code = 1;
				$message = "Неверный логин или пароль";
			}
		} else {
			$code = 2;
			$message = 'Не все передано';
		}
		break;
	case 'setcolor':
		if(!isset($lnk[2]) or !$user or $user->need_relogin()) {
			$code = 1;
			$message = 'Не все передано или Вы не вошли';
			break;
		}
		$user->update_default_color(str_replace('skin-', '', $lnk[2]));
		$code = 0;
		$message = 'success';
		break;
	case 'chat':
		if($user and !$user->need_relogin()) {
			if(!isset($lnk[2]) or !$user or $user->need_relogin()) {
				$code = 2;
				$message = 'Не все передано';
			}
			$update = (isset($lnk[3]))? (int) $lnk[3] : 0;
			$limit_first = (isset($lnk[4]))? (int) $lnk[4] : 0;
			$limit = (isset($lnk[5]))? (int) $lnk[5] : (isset($lnk[3]))? 0 : 32;
			$where = '';
			if($update) {
				$where .= " AND `ch_id`>'{$db->safe($update)}'";
			}
			$add = '';
			if($limit) {
				$add .= " LIMIT {$db->safe($limit_first)}, {$db->safe($limit)}";
			}
			$query = $db->execute("SELECT * FROM `chat`, `users` WHERE `ch_dep`='{$db->safe($lnk[2])}' AND `ch_author`=`id`$where ORDER BY `ch_date` ASC$add") or die($db->error());

			$code = 0;
			ob_start();
			while($ch_message = $db->fetch_array($query)) {
				include IssueTracker::PathTPL(($ch_message['ch_author'] == $user->uid())? "api/chat_me" : "api/chat_notme");
				$params['last_id'] = $ch_message['ch_id'];
			}
			$message = ob_get_clean();
		} else {
			$code = 1;
			$message = 'Вы не залогинены';
		}
		break;
	case 'chat_message':
		if($user and !$user->need_relogin()) {
			if(isset($_POST['message']) and isset($lnk[2])) {
				$check = $db->execute("INSERT INTO `chat` (`ch_author`, `ch_dep`, `ch_date`, `ch_message`) VALUES ('{$user->uid()}', '{$db->safe((int) $lnk[2])}', NOW(), '{$db->safe($_POST['message'])}')");
				if ($check) {
					$code = 0;
					$message = 'success';
				} else {
					$code = 1;
					$message = 'Ошибка записи в БД';
				}
			} else {
				$code = 2;
				$message = 'Сообщение не передано';
			}
		} else {
			$code = 3;
			$message = 'Вы не залогинены';
		}
		break;
	default:
		include TRACKER_BASE . 'pages/404.php';
		break;
}

exit(json_encode(array('code' => $code, 'message' => $message, 'params' => $params)));