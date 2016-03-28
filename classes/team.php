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
		if($team_id == "search"){
			return search_teams($subquery);
		}
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

	public function process_post($action){
		switch($action){
			case "create-team":{
				$success = post_create_new_team($this);
				if ($success == true){
					return 200;
				}
				else{
					return 400;
				}
			}
			break;
			default:{

			}
			break;
		}
	}
	
}
