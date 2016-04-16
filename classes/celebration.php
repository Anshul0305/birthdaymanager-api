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

	public function process_get($celebration_id, $subquery){
		switch($subquery){
			case "stub":{

			}
			break;

			default:{
				$celebrations = get_celebrations_by_celebration_id($celebration_id);
				return $celebrations;
			}
			break;
		}
	}

	public function process_post(){
		$this->perhead_contribution = ($this->cake_amount + $this->other_expense)/($this->total_attendees);
		$result = post_add_celebration($this);
		return $result;
	}
}