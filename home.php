<?php
require_once('classes/team_member.php');
require_once('classes/celebration.php');
include('ChromePhp.php');

$method = $_SERVER['REQUEST_METHOD'];

// Get Params
$query = $_GET["query"];
$subquery = $_GET["subquery"];
//$subquery = "1";
$get_member_id = $_GET["member_id"];

// Post Params
$first_name = $_POST["first_name"];
$last_name = $_POST["last_name"];
$email = $_POST["email"];
$password = $_POST["password"];
$official_dob = $_POST["official_dob"];
$member_type = $_POST["member_type"];
$member_id = $_POST["member_id"];
$fund = $_POST["fund"];
$cake_amount = $_POST["cake_amount"];
$total_attendees = $_POST["total_attendees"];
$perhead_contribution = $_POST["perhead_contribution"];
$birthday_of_member_id = $_POST["birthday_of_member_id"];
$attendees_member_id = $_POST["attendees_member_id"];

// Headers
//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
	
switch($method){
	case 'PUT':
    handle_put($query,$subquery);  
    break;
  case 'POST':
    handle_post($query,$subquery);  
    break;
  case 'GET':
    handle_get($query,$subquery);  
    break;
  case "DELETE":
    handle_delete($query,$subquery);  
    break;
  default:
    handle_error($query,$subquery);  
    break;
}


// Handle Get Requests
function handle_get($query,$subquery){

	switch($query){
			
	case "team-member":{
		$team_member = new TeamMember();
		$team_member_list = $team_member->get_team_member($subquery);
		echo json_encode($team_member_list);
	}
	break;
	
	case "fund":{
		$funds = new TeamMember();
		$fund_list = $funds->get_fund($subquery);
	 	echo json_encode($fund_list);
	}
	break;
	
	case "celebration":{
		$celebration = new Celebration();
		$celebration_list = $celebration->get_all_celebrations();
		echo json_encode($celebration_list);
	}
	break;
	
	default: {
		$default=array();
		$default['Birthday_Manager'] = array('Version' => '1.0', 'Query' =>$query);
		echo json_encode($default);

	}
}
	
}

// Handle Post Requests
function handle_post($query, $subquery){
	switch($query){
		case "team-member":{
		$team_member = new TeamMember();
		$team_member->first_name = $first_name;
		$team_member->last_name = $last_name;
		$team_member->email = $email;
		$team_member->official_dob = $official_dob;
		$team_member->member_type = $member_type;
		$add_result = $team_member->add_team_member();
		echo json_encode($add_result);
	}
	break;
	
	case "fund":{
		$team_member = new TeamMember();
		$team_member->member_id = $member_id;
		$result = $team_member->add_fund($fund);
		echo json_encode($result);
	}
	break;
			
	case "celebration":{
		$celebration = new Celebration();
		$celebration->birthday_of_member_id = $birthday_of_member_id;
		$celebration->celebration_date = date("Y-m-d");
		$celebration->cake_amount = $cake_amount;
		$celebration->total_attendees = $total_attendees;
		$celebration->attendees_member_id = $attendees_member_id;
		$result = $celebration->celebrate_birthday();
		echo json_encode($result);
	}
	break;
			
	}
}




?>