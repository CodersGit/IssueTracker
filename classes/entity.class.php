<?php
class Entity {
	public function get ($parameter) {
		return (isset($this->$parameter))? $this->$parameter: false;
	}
	public static function GetAllWhere ($where = '1', $order = '1') {}
}