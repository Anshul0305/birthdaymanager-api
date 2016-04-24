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
	public $team_id;

	// Gateway to other functions
	public function process_get($member_id, $subquery){
		switch($member_id){
			case "search":{
				return search_member_by_email($subquery);
			}
			break;
			case "upcoming-birthdays":{
				$members = get_upcoming_birthdays();
				return $members;
			}
			break;
		}

		switch($subquery){
			case "celebrations":{
				$team_member_celebration = get_celebrations_by_member_id($member_id);
				return $team_member_celebration;
			}
			break;

			case "transactions":{
				$transactions = get_member_transactions_by_member_id($member_id);
				return $transactions;
			}
			break;

			default:{
				$team_members = get_member_details_by_member_id($member_id);
				return $team_members;
			}
			break;
		}
	}

	public function process_post($action){
		switch($action){
			case "login":{
				$status = login_member($this);
				return $status;
			}
			break;
			case "register":{
				$status = register_new_member($this);
				return $status;
			}
			break;
			case "funds":{
				$status = post_add_fund($this);
				return $status;
			}
			break;
			case "join-team":{
				$status = join_team($this);
				return $status;
			}
			break;
			case "leave-team":{
				$status = leave_team($this);
				return $status;
			}
			default:{

			}
			break;
		}
	}
	
}
