<?php
require_once('././scripts/database.php');
require_once('././scripts/auth.php');
require_once('././scripts/mailer.php');

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
	public $team_name;
	public $reset_code;
	public $reset_password_link;
	public $invite_team_link;
	public $reset_password1;
	public $reset_password2;
	public $birthday_members = array();

	public function __construct()
	{

	}

	// Gateway to other functions
	public function process_get($member_id, $subquery){
		switch($member_id){
			case "search":{
				return search_member_by_email($subquery);
			}
			break;
			case "daily":{
				$member_ids = get_all_member_ids();
				foreach ($member_ids as $member_id) {
					$official_dob = get_team_member_official_dob_by_member_id($member_id);
					$d = date('m-d',strtotime($official_dob));
					$c = date('m-d');
					if($d == $c) {
						$members = get_other_members_for_team_member($member_id);
						$this->member_id = $member_id;
						$this->official_dob = get_team_member_official_dob_by_member_id($member_id);
						$this->email = get_team_member_email_by_id($member_id);
						$this->first_name = get_team_member_name_by_team_member_id($member_id);
						$this->birthday_members = $members;
						send_daily_birthday_alert($this);
					}
				}
			}
			break;
			case "weekly":{
				$admin_ids = get_all_admin_ids();
				foreach ($admin_ids as $member_id) {
					if(count(get_upcoming_birthdays($member_id))>0) {
						$members = get_upcoming_birthdays($member_id);
						$this->member_id = $member_id;
						$this->email = get_team_member_email_by_id($member_id);
						$this->first_name = get_team_member_name_by_team_member_id($member_id);
						$this->birthday_members = $members;
						send_weekly_birthday_alert($this);
					}
				}
			}
			break;
		}

		switch($subquery){
			case "celebrations":{
				$team_member_celebration = get_celebrations_by_member_id($member_id);
				return $team_member_celebration;
			}
			break;

			case "greetings":{
				$team_member_greetings = get_greetings_by_member_id($member_id);
				return $team_member_greetings;
			}
			break;

			case "transactions":{
				$transactions = get_member_transactions_by_member_id($member_id);
				return $transactions;
			}
			break;

			case "upcoming-birthdays":{
				$members = get_upcoming_birthdays($member_id);
				return $members;
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
			case "autologin":{
				$status = autologin_member($this);
				return $status;
			}
				break;
			case "reset-password-link":{
				$status = get_reset_password_code($this);
				if($status["status_code"]==200) {
					$this->first_name = get_team_member_name_by_email($this->email);
					$this->reset_code = $status["reset_code"];
					$this->reset_password_link = json_decode(file_get_contents("env.json"))->website_host."/reset-password.php?code=" . $this->reset_code . "&email=" . $this->email;
					send_password_reset_code($this);
				}
				return $status;
			}
			break;
			case "reset-password":{
				$status = reset_password($this);
				return $status;
			}
			break;
			case "register":{
				$status = register_new_member($this);
				if($status["status_code"]==200){
					if($this->team_id!="" && $this->team_name == get_team_name_by_team_id($this->team_id)){
						$this->member_id = get_team_member_id_by_email($this->email);
						join_team($this);
					}
					send_registration_success_email($this);
				}
				return $status;
			}
			break;
			case "funds":{
				$this->email = get_team_member_email_by_id($this->member_id);
				$status = post_add_fund($this);
				send_add_fund_email($this);
				return $status;
			}
			break;
			case "join-team":{
				$this->first_name = get_team_member_name_by_team_member_id($this->member_id);
				$this->team_name = get_team_name_by_team_id($this->team_id);
				$this->email = get_team_member_email_by_id($this->member_id);
				$status = join_team($this);
				send_join_team_email($this);
				return $status;
			}
			break;
			case "leave-team":{
				$status = leave_team($this);
				send_leave_team_email($this);
				return $status;
			}
			break;
			case "invite":{
				$this->team_name = get_team_name_by_team_id($this->team_id);
				if($this->team_name!=""){
					$this->invite_team_link = json_decode(file_get_contents("env.json"))->website_host."/index.php?team-id=" . $this->team_id . "&team-name=" . urlencode($this->team_name);
					$status = invite_to_team($this);
					return $status["status_code"];
				}
				else{
					return 400;
				}
			}
			break;
			case "edit-member":{
				$status = edit_member($this);
				return $status==true?200:400;
			}
			break;
			default:{

			}
			break;
		}
	}

	public function process_put($action){
		switch($action){


		}
	}
}
