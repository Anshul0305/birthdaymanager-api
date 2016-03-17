<?php
require_once('././scripts/database.php');
require_once('././scripts/auth.php');

class Member {
	public $member_id;
	public $first_name;
	public $last_name;
	public $email;
	public $password;
	public $dob;
	public $official_dob;
	public $member_type;
	public $fund;

	// Gateway to other functions
	public function process_get($member_id, $subquery){
		switch($subquery){
			case "celebrations":{
				//$team_member_celebration = get_team_member_celebrations_from_db($id);
				//return $team_member_celebration;
			}
			break;

			case "funds":{
				//$team_member_fund = get_team_member_fund_from_db($id);
				//return $team_member_fund;
			}
			break;

			default:{
				$team_members = get_member_details_by_member_id($member_id);
				return $team_members;
			}
			break;
		}
	}
	
}
?>