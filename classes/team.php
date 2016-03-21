<?php
require_once('././scripts/database.php');

class Team {
	public $team_id;
	public $team_name;
	public $members;
	public $team_fund_balance;
	public $admin_id;

	// Gateway to other functions
	public function process_get($team_id, $subquery){
		switch($subquery){
			case "stub":{

			}
			break;

			default:{
				return get_team_details_by_team_id($team_id);
			}
			break;
		}
	}

	public function process_post(){
		$success = post_create_new_team($this);
		if ($success == true){
			return 200;
		}
		else{
			return 400;
		}
	}
	
}
