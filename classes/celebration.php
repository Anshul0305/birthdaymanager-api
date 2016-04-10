<?php
require_once('././scripts/database.php');

class Celebration{
	public $birthday_of_member_id;
	public $celebration_date;
	public $team_id;
	public $cake_amount;
	public $other_expense;
	public $total_attendees;
	public $attendees_member_id_array;
	public $perhead_contribution;

	public function process_get($id, $subquery){

	}

	public function process_post(){
		$result = post_add_celebration($this);
		return $result;
	}
}