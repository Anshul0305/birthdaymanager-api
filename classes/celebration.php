<?php
require_once('././scripts/database.php');

class Celebration{
	public $birthday_of_member_id;
	public $celebration_date;
	public $cake_amount;
	public $total_attendees;
	public $attendees_member_id;
	public $perhead_contribution;
	public $attendees_id_array;
	
	public function celebrate_birthday(){
		// Get the perhead contribution
		$this->perhead_contribution = ((float)$this->cake_amount)/((int)$this->total_attendees - 1);
		$result = celebration_add_to_db($this->birthday_of_member_id, $this->celebration_date, $this->cake_amount, $this->total_attendees, $this->attendees_member_id, $this->perhead_contribution);
		return $result;
	}
	
	public function get_all_celebrations(){
		$celebrations = get_all_celebrations_from_db();
		return $celebrations;
	}

}


?>