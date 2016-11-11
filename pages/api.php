<?php
$redirect_url = (isset($_REQUEST['redirect_url'])) ? $_REQUEST['redirect_url'] : $page;
$code = 888;
$message = '';
$params = array();
if (!isset($lnk[1])) $lnk[1] = '404';
switch ($lnk[1]) {
	case 'relogin':
		if ($user) {
			if (isset($_POST['password'])) {
				if ($user->check_pass($_POST['password'])) {
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
	case 'login':
		if (isset($_POST['login']) and isset($_POST['password'])) {
			$user = new User($_POST['login'], 'nickname');
			if ($user->uid() != -1 and $user->check_pass($_POST['password'])) {
				$sessionID = IssueTracker::randString(128);
				$db->execute("INSERT INTO `sessions` (`uid`,`session`,`valid_until`,`last_active`,`need_inactive_check`) VALUES ('{$user->uid()}', '{$db->safe($sessionID)}', NOW(), NOW(),'" . ((isset($_POST['remember']))?0:1) . "')");
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
		if (!isset($lnk[2]) or !$user or $user->need_relogin()) {
			$code = 1;
			$message = 'Не все передано или Вы не вошли';
			break;
		}
		$user->update_default_color(str_replace('skin-', '', $lnk[2]));
		$code = 0;
		$message = 'success';
		break;
	default:
		include TRACKER_BASE . 'pages/404.php';
		break;
}

exit(json_encode(array('code' => $code, 'message' => $message, 'params' => $params)));