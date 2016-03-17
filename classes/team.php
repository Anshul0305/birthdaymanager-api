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
				$team_details = get_team_details_by_team_id($team_id);
				return $team_details;
			}
			break;
		}
	}
	
}
?>