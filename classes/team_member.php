<?php
require_once('././scripts/database.php');
require_once('././scripts/auth.php');

class TeamMember{
	public $member_id;
	public $first_name;
	public $last_name;
	public $email;
	public $password;
	public $dob;
	public $official_dob;
	public $member_type;
	public $fund;
	
	// Register new user
	public function register(){
    $result = add_team_member_to_db($this->first_name, $this->last_name, $this->email, $this->password, $this->official_dob, $this->member_type);
		return $result;
  	}
  
	// Login a user
	public function login(){
	    
	}
	
	// Get List of Team Member
	public function get_team_member($member_id){
        $teammembers = get_team_member_from_db($member_id);
	    return $teammembers;	
	}

	// Add Team Member
	public function add_team_member(){
	  $result = add_team_member_to_db($this->first_name, $this->last_name, $this->email, get_random_password(), $this->official_dob, $this->member_type);
		return $result;
	}

	// Get Current Fund of Team Member
	public function get_fund($member_id){
		$fund = get_fund_from_db($member_id);
		return $fund;
	}
	
	// Add Fund
	public function add_fund($topup_amount){
		// Get current fund balance + Topup amount
		$new_fund_balance = ((int)(get_fund_from_db($this->member_id)) + (int)($topup_amount));
		$result = add_fund_to_db($this->member_id, $new_fund_balance, $topup_amount);
		return $result;
	}
	
}


?>