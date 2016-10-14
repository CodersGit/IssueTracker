<?php
if (!isset($lnk[1])) exit("Error 800: Request Not Defined");
switch ($lnk[1]) {
	case 'chat':
		if (!isset($lnk[2])) exit("Error 810: Chat: Department Not Defined");
		$db->execute("SELECT * FROM `chat`, `users` WHERE `ch_dep`='{$db->safe($lnk[2])}' AND `ch_author`=`id`");

		break;
	default:
		exit("Error 801: Bad Request");
		break;
}
