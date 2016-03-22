<?php
require_once('classes/team.php');
require_once('classes/member.php');
require_once('classes/celebration.php');

$method = $_SERVER['REQUEST_METHOD'];

// Get Params
$query = $_GET["query"];
$val = $_GET["val"];
$subquery = $_GET["subquery"];

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
$per_head_contribution = $_POST["per_head_contribution"];
$birthday_of_member_id = $_POST["birthday_of_member_id"];
$attendees_member_id = $_POST["attendees_member_id"];
$team_name = $_POST["team_name"];
$team_admin_id = $_POST["team_admin_id"];

//Headers
//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");

//Debug Define
//$method = "POST";
//$query = "login";
//////$subquery = "celebration";
//$team_name = "Test";
//$team_admin_id = 1;
//$email = "anshul@gmail.com";
//$password = "anshul";

// Handle Methods
try {
	  switch($method){
	  case 'PUT':
		handle_put($query,$val,$subquery);
		break;
	  case 'POST':
		$response_code = handle_post($query,$team_name, $team_admin_id,$email,$password);
		show_response($response_code);
		break;
	  case 'GET':
		handle_get($query,$val,$subquery);
		break;
	  case "DELETE":
		handle_delete($query,$val,$subquery);
		break;
	  default:
		//handle_error($query,$val,$subquery);
		break;
	  }
	}
catch (Exception $e) {
		echo "here";
}

// Handle Get Requests
function handle_get($query,$val,$subquery){
	switch($query){

	case "teams":{
		$team = new Team();
		$team_list = $team->process_get($val, $subquery);
		echo json_encode($team_list);
	}
	break;

	case "members":{
		$member = new Member();
		$member_list = $member->process_get($val, $subquery);
		echo json_encode($member_list);
	}
	break;
	
	case "funds":{
		$funds = new Member();
		$fund_list = $funds->process_get($val, $subquery);
	 	echo json_encode($fund_list);
	}
	break;
	
	case "celebrations":{
		$celebration = new Celebration();
		$celebration_list = $celebration->process_get($val, $subquery);
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
function handle_post($query, $team_name, $team_admin_id, $email, $password){
	switch($query){
		case "teams":{
			$team_obj = new Team();
			$team_obj->team_name = $team_name;
			$team_obj->admin_id = $team_admin_id;
			return $team_obj->process_post("create-team");
		}
		break;
		case "login":{
			$member_obj = new Member();
			$member_obj->email = $email;
			$member_obj->password = $password;
			return $member_obj->process_post("login");
		}
		break;
		default:{

		}
		break;
	}
}

// Show Response Status Code
function show_response($response_code){
	switch($response_code){
		case 200:{
			header("HTTP/1.1 200");
		}
		break;
		case 400:{
			header("HTTP/1.1 400");
		}
		break;
		case 401:{
			header("HTTP/1.1 401");
		}
		break;
		default:{
			header("HTTP/1.1 400");
		}
	}
}



