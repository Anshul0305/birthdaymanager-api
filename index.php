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
$first_name = $_POST["first_name"];  // first name of member
$last_name = $_POST["last_name"];  // last name of member
$email = $_POST["email"];  // email address of member
$password = $_POST["password"];  // password of member
$official_dob = $_POST["official_dob"];  // official dob of member
$member_type = $_POST["member_type"];  // member type of member
$member_id = $_POST["member_id"];  // member id of team member
$fund = $_POST["fund"];  // fund amount of team member
$cake_amount = $_POST["cake_amount"];  // cake amount for birthday celebration
$other_expense = $_POST["other_expense"];  // other expense amount
$total_attendees = $_POST["total_attendees"];  // count of total attendees
$celebration_date = $_POST["celebration_date"];  // birthday celebration date
$celebration_time = $_POST["celebration_time"];  // birthday celebration time
$birthday_invitation_message = $_POST["birthday_invitation_message"];  // birthday invitation message
$birthday_invitation_location = $_POST["birthday_invitation_location"];  // birthday invitation location
$per_head_contribution = $_POST["per_head_contribution"];  // per head contribution amount
$birthday_of_member_id = $_POST["birthday_of_member_id"];  // member id of member having birthday
$attendees_member_id = $_POST["attendees_member_id"];  // array of celebrations attendees
$team_name = $_POST["team_name"];  // team name
$team_admin_id = $_POST["team_admin_id"];  // array of team admin ids
$team_id = $_POST["team_id"];  // team id
$reset_code = $_POST["reset_code"];  // reset password code
$password1 = $_POST["password1"];  // password 1
$password2 = $_POST["password2"];  // password 2
$message = $_POST["message"];  // message to be posted in team

//Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//Debug Define
$enable_debug=false;
if($enable_debug==true) {
	$method = "POST";
	$query = "birthday-invitation";
	$val = "weekly";
	$subquery = "weekly";
	$team_name = "Test";
	$team_admin_id = 1;
	$email = "anshul121@gm.com";
	$password = "anshul";
	$official_dob = "2010-01-01";
	$first_name = "Ansh";
	$last_name = "Shr";
	$member_id = 14;
	$team_id = 11;
	$fund = 100;
	$attendees_member_id = [1, 2, 3, 4, 5, 6, 7];
	$reset_code = "1234";
	$password1 = "anshul";
	$password2 = "anshul";
	$message = "debug";
	$birthday_of_member_id = 11;
	$celebration_date = "2016-10-11";
	$celebration_time = "";
	$birthday_invitation_location = "location";
	$birthday_invitation_message  = "message";
}

// Handle Methods
try {
	  switch($method){
	  case 'PUT':
		handle_put($query, $member_id, $email, $official_dob, $first_name, $last_name);
		break;
	  case 'POST':
		handle_post($query,$team_name, $team_admin_id,$email,$password, $official_dob, $first_name, $last_name,$team_id, $member_id, $fund,$birthday_of_member_id, $cake_amount,$other_expense, $celebration_date, $celebration_time, $birthday_invitation_message, $birthday_invitation_location,$attendees_member_id, $reset_code, $password1, $password2, $message);
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

	case "cron":{
		date_default_timezone_set('Europe/London');
		$member = new Member();
		$member->process_get($val, $subquery);
		echo "CRON Job executed!";
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
function handle_post($query, $team_name, $team_admin_id, $email, $password, $official_dob, $first_name, $last_name,$team_id, $member_id, $fund, $birthday_of_member_id, $cake_amount,$other_expense, $celebration_date, $celebration_time, $birthday_invitation_message,$birthday_invitation_location,$attendees_member_id, $reset_code, $password1, $password2, $message){
	switch($query){
		case "teams":{
			$team_obj = new Team();
			$team_obj->team_name = $team_name;
			$team_obj->admin_id = $team_admin_id;
			$create_team_result = json_encode($team_obj->process_post("create-team"));
			$status_code = json_decode($create_team_result)->status_code;
			show_response($status_code);
			echo $create_team_result;
			return $status_code;
		}
		break;
		case "team-message":{
			$team_obj = new Team();
			$team_obj->team_id = $team_id;
			$team_obj->message = $message;
			$status_code = $team_obj->process_post("team-message");
			show_response($status_code);
			return $status_code;
		}
		break;
		case "team-admin":{
			$team_obj = new Team();
			$team_obj->team_id = $team_id;
            $team_obj->team_name = get_team_name_by_team_id($team_id);
			$team_obj->admin_id = $team_admin_id;
			$status_code = $team_obj->process_post("team-admin");
			show_response($status_code);
			return $status_code;
		}
        break;
        case "revoke-admin":{
            $team_obj = new Team();
            $team_obj->team_id = $team_id;
            $team_obj->team_name = get_team_name_by_team_id($team_id);
            $team_obj->admin_id = $team_admin_id;
            $status_code = $team_obj->process_post("revoke-admin");
            show_response($status_code);
            return $status_code;
        }
            break;
		case "login":{
			$member_obj = new Member();
			$member_obj->email = $email;
			$member_obj->password = $password;
			$json_login_result = json_encode($member_obj->process_post("login"));
			$status_code = json_decode($json_login_result)->status_code;
			show_response($status_code);
			echo $json_login_result;
			return $status_code;
		}
		break;
		case "autologin":{
			$member_obj = new Member();
			$member_obj->email = $email;
			$member_obj->reset_code = $reset_code;
			$json_login_result = json_encode($member_obj->process_post("autologin"));
			$status_code = json_decode($json_login_result)->status_code;
			show_response($status_code);
			echo $json_login_result;
			return $status_code;
		}
			break;
		case "reset-password-link":{
			$member_obj = new Member();
			$member_obj->email = $email;
			$json_result = json_encode($member_obj->process_post("reset-password-link"));
			$status_code = json_decode($json_result)->status_code;
			show_response($status_code);
			echo $json_result;
			return $status_code;
		}
		case "reset-password":{
			$member_obj = new Member();
			$member_obj->email = $email;
			$member_obj->reset_code = $reset_code;
			$member_obj->reset_password1 = $password1;
			$member_obj->reset_password2 = $password2;
			$json_login_result = json_encode($member_obj->process_post("reset-password"));
			$status_code = json_decode($json_login_result)->status_code;
			show_response($status_code);
			echo $json_login_result;
			return $status_code;
		}
		break;
		case "register":{
			$member_obj = new Member();
			$member_obj->password = $password;
			$member_obj->email = $email;
			$member_obj->official_dob = $official_dob;
			$member_obj->first_name = ucfirst($first_name);
			$member_obj->last_name = ucfirst($last_name);
			$member_obj->team_id = $team_id;
			$member_obj->team_name = $team_name;
			$json_register_result = json_encode($member_obj->process_post("register"));
			$status_code = json_decode($json_register_result)->status_code;
			show_response($status_code);
			echo $json_register_result;
			return $status_code;
		}
		break;
		case "funds":{
			$member_obj = new Member();
			$member_obj->member_id = $member_id;
			$member_obj->team_id = $team_id;
			$member_obj->fund = $fund;
			$status_code = $member_obj->process_post("funds") == true?200:400;
			show_response($status_code);
			return $status_code;
		}
		break;
		case "join-team":{
			$member_obj = new Member();
			$member_obj->member_id = $member_id;
			$member_obj->team_id = $team_id;
			$status_code = $member_obj->process_post("join-team");
			show_response($status_code);
			return $status_code;
		}
		break;
		case "leave-team":{
			$member_obj = new Member();
			$member_obj->member_id = $member_id;
			$member_obj->team_id = $team_id;
			$member_obj->first_name = get_team_member_name_by_team_member_id($member_obj->member_id);
			$member_obj->team_name = get_team_name_by_team_id($member_obj->team_id);
			$member_obj->email = get_team_member_email_by_id($member_obj->member_id);
			$status_code = $member_obj->process_post("leave-team");
			show_response($status_code);
			return $status_code;
		}
		case "invite":{
			$member_obj = new Member();
			$member_obj->email = $email;
			$member_obj->team_id = $team_id;
			$status_code = $member_obj->process_post("invite");
			show_response($status_code);
			return $status_code;
		}
		break;
		case "celebrations":{
			$celebration_obj = new Celebration();
			$celebration_obj->birthday_of_member_id = $birthday_of_member_id;
			$celebration_obj->cake_amount = $cake_amount;
			$celebration_obj->other_expense = $other_expense;
			$celebration_obj->celebration_date = $celebration_date;
			$celebration_obj->total_attendees = count($attendees_member_id);
			$celebration_obj->team_id = $team_id;
			$celebration_obj->attendees_member_id_array = $attendees_member_id;
			$status_code = $celebration_obj->process_post("celebrate");
			show_response($status_code);
			return $status_code;
		}
		break;
		case "members": {
			$member_obj = new Member();
			$member_obj->member_id = $member_id;
			$member_obj->email = $email;
			$member_obj->official_dob = $official_dob;
			$member_obj->first_name = $first_name;
			$member_obj->last_name = $last_name;
			$status_code = $member_obj->process_post("edit-member");
			show_response($status_code);
			return $status_code;
		}
		break;
		case "birthday-invitation": {
			$birthday_invitation_obj = new Celebration();
			$birthday_invitation_obj->birthday_of_member_id = $birthday_of_member_id;
			$birthday_invitation_obj->celebration_date = $celebration_date;
			$birthday_invitation_obj->celebration_time = date("h:i a", strtotime($celebration_time));
			$birthday_invitation_obj->birthday_invitation_message = $birthday_invitation_message;
			$birthday_invitation_obj->birthday_invitation_location = $birthday_invitation_location;
			$birthday_invitation_obj->team_id = $team_id;
			$birthday_invitation_obj->attendees_member_id_array = $attendees_member_id;
			$status_code = $birthday_invitation_obj->process_post("birthday-invitation");
			show_response($status_code);
			return $status_code;
		}
		break;
		default:{

		}
		break;
	}
}

// Handle PUT Request
function handle_put($query, $member_id, $email, $official_dob, $first_name, $last_name){
	switch($query){
		default:{

		}
	}
}

// Handle Delete Request
function handle_delete($query,$val){
	switch($query) {
		case "teams": {
			$team = new Team();
			$team->team_id = $val;
			$status = $team->process_delete();
			return $status;
		}
		break;

		default: {

		}
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
		case 409:{
			header("HTTP/1.1 409");
		}
		break;
		default:{
			header("HTTP/1.1 500");
		}
	}
}



