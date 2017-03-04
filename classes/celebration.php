<?php
require_once('././scripts/database.php');

class Celebration{
	public $birthday_of_member_id;
	public $celebration_date;
	public $celebration_time;
	public $birthday_invitation_message;
	public $birthday_invitation_location;
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

	public function process_post($action){
		switch($action){
			case "celebrate":{
				$this->perhead_contribution = round(($this->cake_amount + $this->other_expense)/($this->total_attendees),2);
				$result = post_add_celebration($this);
				send_birthday_celebration_fund_update_email_to_attendees($this);
				return $result;
			}
			break;
			case "birthday-invitation":{
				$status = send_birthday_invitation($this);
				send_birthday_invitation_email($this);
				return $status;
			}
				break;
			default:{

			}
		}
	}
}