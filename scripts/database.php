<?php

// General Functions
function connect(){
	$env_var = file_get_contents("././env.json");
	$server_name = json_decode($env_var)->server_name;
	$username = json_decode($env_var)->db_username;
	$password = json_decode($env_var)->db_password;
	$db = json_decode($env_var)->db_name;
	$connection = new mysqli($server_name, $username, $password, $db);
	return $connection;
}
function disconnect($connection){
	$connection->close();
}
function return_message($result){
	if($result){
		return array("Result"=>"Success");
	}else{
		return array("Result"=>"Fail");
	}
}

function query_sql($sql){
	$connection=connect();
	$result=$connection->query($sql);
	disconnect($connection);
	return$result;
}
function template(){
	$result=query_sql("");
	$admin_id=array();
	while($row=$result->fetch_assoc()){
		$admin_id[]=$row[""];
	}
	return$admin_id;
}
function get_all_admin_ids(){
	$sql="SELECT DISTINCT `team_admin_id` FROM `team` WHERE `deleted`!=1";
	$result=query_sql($sql);
	$admin_id=array();
	while($row=$result->fetch_assoc()){
		$admin_id[]=$row["team_admin_id"];
	}
	return$admin_id;
}
function get_all_member_ids(){
	$sql="SELECT `member_id` FROM `team_members`";
	$result=query_sql($sql);
	$member_id=array();
	while($row=$result->fetch_assoc()){
		$member_id[]=$row["member_id"];
	}
	return$member_id;
}

// Team Functions

function is_team_deleted($team_id){
	$connection = connect();
	$sql = "SELECT deleted FROM team WHERE team_id=".$team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$deleted = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$deleted = $row["deleted"];
		}
	}
	return ($deleted == 1)?true:false;
}
function get_team_id_by_member_id($member_id){
	$connection = connect();
	$sql = "SELECT DISTINCT team_id FROM team_teammember WHERE member_id=".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_id = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			if(!is_team_deleted($row["team_id"])){
				$team_id[] = $row["team_id"];
			}
		}
	}
	return $team_id;
}
function get_team_details_by_team_id($team_id){
	$connection = connect();
	$sql = $team_id == "" ? ("SELECT team_id, team_name, team_admin_id FROM team WHERE deleted = 0") : ( "SELECT team_id, team_name, team_admin_id FROM team WHERE deleted = 0 and team_id = ". $team_id);
	$result = $connection->query($sql);
	disconnect($connection);
	$team_detail_list = array();
	if ($result->num_rows>0) {
		$team_detail = array();
		while ($row = $result->fetch_assoc()) {
			$team_detail[] = array(
				'id' => $row["team_id"],
				'name' => $row["team_name"],
				'admin_id' => $row["team_admin_id"],
				'admin_name' => get_team_member_name_by_team_member_id($row["team_admin_id"]),
				'fund_balance' => get_team_fund_by_team_id($row["team_id"]),
				'members' => get_team_member_id_by_team_id($row["team_id"]),
			);
		}
		$team_detail_list = $team_detail;
	}
	return $team_detail_list;
}
function get_team_admin_id_by_team_id($team_id){
	$connection = connect();
	$sql = "SELECT team_admin_id FROM team WHERE team_id = ". $team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_admin_id = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_admin_id = $row["team_admin_id"];
		}
	}
	return $team_admin_id;
}
function get_team_details_by_team_ids($team_ids){
	$team_details = array();
	foreach($team_ids as $team_id){
		$team_details[] = get_team_details_by_team_id($team_id);
	}
	return $team_details;
}
function get_team_name_by_team_id($team_id){
	$connection = connect();
	$sql = "SELECT team_name FROM team WHERE team_id = ". $team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_name = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_name = $row["team_name"];
		}
	}
	if(is_team_deleted($team_id)){
		$team_name = $team_name." (Deleted)";
	}
	return $team_name;
}
function get_team_details_by_team_id_and_member_id($team_id,$member_id){
	// This function checks if a member is admin of a team or not
	$connection = connect();
	$sql = $team_id == "" ? ("SELECT team_id, team_name, team_admin_id FROM team where deleted = 0") : ( "SELECT team_id, team_name, team_admin_id FROM team WHERE deleted = 0 and team_id = ". $team_id);
	$result = $connection->query($sql);
	disconnect($connection);
	$team_detail_list = array();
	if ($result->num_rows>0) {
		$team_detail = "";
		while ($row = $result->fetch_assoc()) {
			$team_detail = array(
				'id' => $row["team_id"],
				'name' => $row["team_name"],
				'is_admin' => ($row["team_admin_id"] == $member_id)?"true":"false",
				'admin_name' => get_team_member_name_by_team_member_id($row["team_admin_id"]),
				'member_fund_balance' => get_member_fund_by_team_id_and_member_id($row["team_id"],$member_id),
				'members' => get_team_member_name_by_team_member_id_array(get_team_member_id_by_team_id($row["team_id"]))
			);
		}
		$team_detail_list = $team_detail;
	}
	return $team_detail_list;
}
function get_team_details_by_team_ids_and_member_id($team_ids, $member_id){
	$team_details = array();
	foreach($team_ids as $team_id){
		$team_details[] = get_team_details_by_team_id_and_member_id($team_id, $member_id);
	}
	return $team_details;
}
function get_team_message($team_id){
	$connection = connect();
	$sql = "SELECT message FROM team WHERE team_id = ". $team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_name = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_name = $row["message"];
		}
	}
	return $team_name;
}
function post_create_new_team(Team $team){
	$team_name = $team->team_name;
	$team_admin_id = $team->admin_id;
	$team_message = $team->message;
	$connection = connect();
	$sql = "INSERT INTO team (team_id, team_name, team_admin_id, deleted, message) VALUES (NULL,'".sanitize($team_name)."',".sanitize($team_admin_id).",0, '".sanitize($team_message)."')";
	$result_1 = $connection->query($sql);
	$sql = "SELECT team_id FROM team ORDER BY team_id DESC LIMIT 1";
	$result = $connection->query($sql);
	$last_team_id = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$last_team_id = $row["team_id"];
		}
	}
	$sql = "INSERT INTO team_teammember (team_team_member_id, team_id, member_id, fund_balance) VALUES (NULL, ".$last_team_id.", ".$team_admin_id.", 0)";
	$result_2 = $connection->query($sql);
	disconnect($connection);

	if($result_1 == true && $result_2 == true){
		return true;
	}
	else {
		return false;
	}
}
function post_team_message(Team $team){
	$team_id = $team->team_id;
	$team_message = $team->message;
	$connection = connect();
	$sql = "UPDATE `team` SET `message`= '".$team_message."' WHERE `team_id` = ".$team_id;
	$result = $connection->query($sql);
	disconnect($connection);

	if($result == true){
		return true;
	}
	else{
		return false;
	}
}
function search_teams($search_term){
	$connection = connect();
	$sql = "SELECT team_id, team_name, team_admin_id FROM team WHERE deleted = 0 and team_name like '%".$search_term."%'";
	$result = $connection->query($sql);
	disconnect($connection);
	$team_detail_list = array();
	if ($result->num_rows>0) {
		$team_detail = array();
		while ($row = $result->fetch_assoc()) {
			$team_detail[] = array(
				'id' => $row["team_id"],
				'name' => $row["team_name"],
				'admin_id' => $row["team_admin_id"],
				'admin_name' => get_team_member_name_by_team_member_id($row["team_admin_id"]),
				'fund_balance' => get_team_fund_by_team_id($row["team_id"]),
				'member_id' => get_team_member_id_by_team_id($row["team_id"])
			);
		}
		$team_detail_list = $team_detail;
	}
	return $team_detail_list;
}
function join_team(Member $member ){
	$team_id = $member->team_id;
	$member_id = $member->member_id;
   	$connection = connect();
	$sql ="INSERT INTO team_teammember (team_team_member_id, team_id, member_id, fund_balance) VALUES (null,".$team_id." ,".$member_id.",'0')";
	$result = $connection->query($sql);
	disconnect($connection);
	return $result;
}
function leave_team(Member $member){
	$team_id = $member->team_id;
	$member_id = $member->member_id;
	$connection = connect();
	$sql ="DELETE FROM `team_teammember` WHERE `team_id` = ".$team_id." and `member_id` = ".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	return $result;
}
function delete_team($team_id){
	$connection = connect();
	$sql = "UPDATE `team` SET `deleted`=1 WHERE team_id=".$team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	return $result;
}


// Member Functions
function get_member_details_by_member_id($member_id){
	$connection = connect();
	$sql = $member_id == "" ? ("SELECT member_id, first_name, last_name, email, official_dob FROM team_members") : ("SELECT member_id, first_name, last_name, email, official_dob FROM team_members WHERE member_id =".$member_id);
	$result = $connection->query($sql);
	disconnect($connection);

	$member_details = array();
	if ($result->num_rows>0) {
		$member_detail = array();
		while ($row = $result->fetch_assoc()) {
			$member_detail[] = array(
				'id' => $row["member_id"],
				'first_name' => $row["first_name"],
				'last_name' => $row["last_name"],
				'dob' => $row["official_dob"],
				'email' => $row["email"],
				'teams' => get_team_details_by_team_ids_and_member_id(get_team_id_by_member_id($row["member_id"]),$row["member_id"])
			);
		}
		$member_details = $member_detail;
	}
	return $member_details;
}
function get_team_member_id_by_team_id($team_id){
	$connection = connect();
	$sql = "SELECT DISTINCT member_id FROM team_teammember WHERE team_id=".$team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_id = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_id[] = array('id' => $row["member_id"],'name'=> get_team_member_name_by_team_member_id($row["member_id"]));
		}
	}
	return $team_member_id;
}
function get_team_member_id_only_by_team_id($team_id){
	$connection = connect();
	$sql = "SELECT DISTINCT member_id FROM team_teammember WHERE team_id=".$team_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_id = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_id[] = $row["member_id"];
		}
	}
	return $team_member_id;
}
function get_team_member_name_by_team_member_id($team_member_id){
	$connection = connect();
	$sql = "SELECT first_name, last_name FROM team_members WHERE member_id = ". $team_member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_first_name = "";
	$team_member_last_name = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_first_name = $row["first_name"];
			$team_member_last_name = $row["last_name"];
		}
	}
	return $team_member_first_name . " " . $team_member_last_name;
}
function get_team_member_name_by_email($email){
	$connection = connect();
	$sql = "SELECT first_name FROM team_members WHERE email = '". $email."'";
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_first_name = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_first_name = $row["first_name"];
		}
	}
	return $team_member_first_name;
}
function get_team_member_id_by_email($email){
	$connection = connect();
	$sql = "SELECT member_id FROM team_members WHERE email = '". $email."'";
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_id = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_id = $row["member_id"];
		}
	}
	return $team_member_id;
}
function get_team_member_email_by_id($member_id){
	$connection = connect();
	$sql = "SELECT email FROM team_members WHERE member_id = ". $member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_email = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_email = $row["email"];
		}
	}
	return $team_member_email;
}
function get_team_member_official_dob_by_member_id($member_id){
	$connection = connect();
	$sql = "SELECT official_dob FROM team_members WHERE member_id = ". $member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_member_dob = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_member_dob = $row["official_dob"];
		}
	}
	return $team_member_dob;
}
function get_team_member_name_by_team_member_id_array($team_member_id_array){
	$team_member_name_array = array();
	foreach($team_member_id_array as $team_member_id){
		$team_member_name_array[] = get_team_member_name_by_team_member_id($team_member_id["id"]);
	}
	return $team_member_name_array;
}
function is_member($email){
	$connection = connect();
	$sql = "SELECT member_id, email FROM team_members WHERE email = '".$email."'";
	$result = $connection->query($sql);
	disconnect($connection);
	while($result->fetch_assoc()){
		return true;
	}
	return false;
}
function login_member(Member $member){
	$username = $member->email;
	$password = md5($member->password);
	$connection = connect();
	$sql = "SELECT member_id, email, password FROM team_members";
	$result = $connection->query($sql);
	disconnect($connection);
	$db_pass = "";
	$member_id = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			if($row["email"] == $username){
				$member_id = $row["member_id"];
				$db_pass = $row["password"];
			}
		}
	}
	if($password == $db_pass){
		$result = array("logged_in" => true, "status_code" => 200, "member_id" => $member_id);
	}
	else{
		$result = array("logged_in" => false, "status_code"=> 401);
	}

	return $result;
}
function autologin_member(Member $member){
	$username = $member->email;
	$code = $member->reset_code;
	$connection = connect();
	$sql = "SELECT member_id, email, reset_code FROM team_members";
	$result = $connection->query($sql);
	disconnect($connection);
	$db_code = "";
	$member_id = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			if($row["email"] == $username){
				$member_id = $row["member_id"];
				$db_code = $row["reset_code"];
			}
		}
	}
	if($code == $db_code){
		$result = array("logged_in" => true, "status_code" => 200, "member_id" => $member_id);
	}
	else{
		$result = array("logged_in" => false, "status_code"=> 401);
	}

	return $result;
}
function get_autologin_link($email){
	$connection = connect();
	$sql = "SELECT member_id, email, reset_code FROM team_members";
	$result = $connection->query($sql);
	disconnect($connection);
	$db_code = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			if($row["email"] == $email){
				$db_code = $row["reset_code"];
			}
		}
	}
	$result = json_decode(file_get_contents("././env.json"))->website_host."/autologin.php?signin-email=".$email."&signin-code=".$db_code;
	return $result;
}
function get_reset_password_code(Member $member){
	$email = $member->email;
	$connection = connect();
	$sql = "SELECT email, reset_code FROM team_members Where email = '".$email."'";
	$result = $connection->query($sql);
	disconnect($connection);
	$q_result =  array("message" => "Invalid Email Id", "status_code"=> 400);
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			if($row["email"] == $email){
				$reset_code = $row["reset_code"];
				$q_result =  array("email" => $email, "status_code"=>200, "reset_code"=> $reset_code);
			}
		}
	}
	return $q_result;
}
function reset_password(Member $member){
	$email = $member->email;
	$password1 = $member->reset_password1;
	$password2 = $member->reset_password2;
	$reset_code = $member->reset_code;
	$connection = connect();
	$sql = "SELECT member_id, email, reset_code FROM team_members where email='".$email."'";
	$result = $connection->query($sql);
	$db_reset_code = "";
	$member_id = "";
	if($password1 != $password2){
		$q_result = array("message" => "Password do not match", "status_code"=> 409);
	}
	else {
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				if ($row["email"] == $email) {
					$member_id = $row["member_id"];
					$db_reset_code = $row["reset_code"];
				}
			}
			if ($reset_code == $db_reset_code) {
				$password1 = md5($password1);
				$sql = "UPDATE `team_members` SET `password`='" . $password1 . "', reset_code='".get_uuid()."' WHERE `email` = '" . $email . "'";
				$connection->query($sql);
				$q_result = array("message" => "password updated", "status_code" => 200, "member_id" => $member_id);
			} else {
				$q_result = array("message" => "invalid reset code", "status_code" => 401);
			}
		} else {
			$q_result = array("message" => "invalid reset code", "status_code" => 401);
		}
	}
	disconnect($connection);
	return $q_result;
}
function register_new_member(Member $member){
	if(!is_member($member->email)) {
		$connection = connect();
		$member->password = md5($member->password);
		$sql = "INSERT INTO team_members (member_id, first_name, last_name, email, password, dob, official_dob, reset_code) VALUES (NULL, '".$member->first_name."', '".$member->last_name."', '".$member->email."' , '".$member->password."', '3000-01-01', '".$member->official_dob."','".get_uuid()."')";
		$result = $connection->query($sql);
		disconnect($connection);
		if($result == true){
			$result = array("registered" => true, "status_code" => 200);
		}
		else{
			$result = array("registered" => false, "status_code"=> 401, "error" => "Registration failed due to internal error");
		}
	}
	else{
		$result = array("registered" => false, "status_code"=> 409, "error" => "Member already Registered");
	}
	return $result;
}
function search_member_by_email($email){
	$connection = connect();
	$sql = "SELECT member_id, first_name, last_name, email, official_dob FROM team_members WHERE email = '".$email."'";
	$result = $connection->query($sql);
	disconnect($connection);

	$member_details = array();
	if ($result->num_rows>0) {
		$member_detail = array();
		while ($row = $result->fetch_assoc()) {
			$member_detail[] = array(
				'id' => $row["member_id"],
				'first_name' => $row["first_name"],
				'last_name' => $row["last_name"],
				'dob' => $row["official_dob"],
				'email' => $row["email"],
				'teams' => get_team_details_by_team_ids_and_member_id(get_team_id_by_member_id($row["member_id"]),$row["member_id"])
			);
		}
		$member_details = $member_detail;
	}
	return $member_details;
}
function get_upcoming_birthdays($member_id){
	$current_month = date('m');
	$current_date = date('d');
	$connection = connect();
	$members = get_other_members_for_team_member($member_id);
	$sql = "SELECT member_id, first_name,last_name,official_dob  FROM team_members WHERE member_id in (".implode(',',$members).") and ((Month(official_dob) = ".$current_month." and Day(official_dob) >= ".$current_date.") OR Month(official_dob) = ".($current_month+1).")";
	$result = $connection->query($sql);
	disconnect($connection);

	$member_details = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$member_detail = array(
				'id' => $row["member_id"],
				'first_name' => $row["first_name"],
				'last_name' => $row["last_name"],
				'dob' => $row["official_dob"],
			);
			$member_details[] = $member_detail;
		}
	}
	usort($member_details, function($a, $b) {
		return strcmp(date("m-d",strtotime($a['dob'])),date("m-d",strtotime($b['dob'])));
	});
	return $member_details;
}
function get_other_members_for_team_member($member_id){
	$team_ids = get_team_id_by_member_id($member_id);
	$members = array();
	foreach ($team_ids as $team_id) {
		$member_ids = get_team_member_id_only_by_team_id($team_id);
		foreach ($member_ids as $member_id){
			array_push($members,$member_id);
		}
	}
	return array_unique($members);
}


// Transactions Functions
function get_member_transactions_by_member_id($member_id){
	$connection = connect();
	$sql = "SELECT `transaction_id`, `team_id`, `transaction_amount`, `transaction_date`, `transaction_type`, `celebration_id` FROM `transactions` WHERE `member_id` = ".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$transactions = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			if (is_member_part_of_team($member_id, $row["team_id"])) {
				$transactions[] = $row["transaction_type"] == "debit" ? array(
					'transaction_id' => $row["transaction_id"],
					'transaction_type' => $row["transaction_type"],
					'team_name' => get_team_name_by_team_id($row["team_id"]),
					'transaction_date' => $row["transaction_date"],
					'transaction_amount' => $row["transaction_amount"],
					'birthday_celebration_of' => get_team_member_name_by_team_member_id(get_birthday_of_member_id_by_celebration_id($row["celebration_id"]))
				) : array(
					'transaction_id' => $row["transaction_id"],
					'transaction_type' => $row["transaction_type"],
					'team_name' => get_team_name_by_team_id($row["team_id"]),
					'transaction_date' => $row["transaction_date"],
					'transaction_amount' => $row["transaction_amount"]
				);
			}
		}
	}
	return $transactions;
}


// Fund Functions
function post_add_fund(Member $member){
	$team_id = $member->team_id;
	$member_id = $member->member_id;
	$fund_amount = $member->fund;
	$connection = connect();
	$get_fund_sql = "SELECT `fund_balance` FROM `team_teammember` WHERE `team_id` = ".$team_id." and `member_id` = ".$member_id;
	$fund_result = $connection->query($get_fund_sql);
	if($fund_result->num_rows>0){
		while($row = $fund_result->fetch_assoc()){
			$current_fund_balance = $row["fund_balance"];
		}
	}
	$fund_amount = $current_fund_balance + $fund_amount; // Get existing fund balance and add it to new amount
	$update_sql = "UPDATE `team_teammember` SET `fund_balance`= ".$fund_amount." WHERE `team_id`= ".$team_id." and `member_id`= ".$member_id;
	$update_result = $connection->query($update_sql);
	$transaction_sql = "INSERT INTO `transactions` (`transaction_id`, `transaction_amount`, `member_id`, `team_id`, `transaction_date`, `transaction_type`, `celebration_id`) VALUES (NULL, '".$member->fund."', '".$member_id."', '".$team_id."', curdate(), 'credit', NULL)";
	$transaction_result = $connection->query($transaction_sql);
	disconnect($connection);
	return ($update_result == true && $transaction_result == true) ? true : false;
}
function get_team_fund_by_team_id($team_id){
	$connection = connect();
	$sql = "SELECT sum(fund_balance) as team_fund_balance FROM team_teammember WHERE team_id=".$team_id." GROUP BY team_id";
	$result = $connection->query($sql);
	disconnect($connection);
	$team_fund_balance = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_fund_balance = $row["team_fund_balance"];
		}
	}
	return round($team_fund_balance, 2);
}
function get_member_fund_by_team_id_and_member_id($team_id, $member_id){
	$connection = connect();
	$sql = "SELECT fund_balance FROM team_teammember WHERE team_id = ".$team_id." and member_id = ".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$member_fund_balance = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$member_fund_balance = $row["fund_balance"];
		}
	}
	return round($member_fund_balance,2);
}


// Celebrations Functions
function get_celebrations_by_celebration_id($celebration_id){
	$connection = connect();
	$sql = ($celebration_id == "") ? "SELECT celebration_id, team_id, birthday_of_member_id, celebration_date, cake_amount, other_expense, perhead_contribution, total_attendees FROM celebrations" : "SELECT celebration_id, team_id, birthday_of_member_id, celebration_date, cake_amount, other_expense, perhead_contribution, total_attendees FROM celebrations WHERE celebration_id = ".$celebration_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$celebration_list = array();
	if ($result->num_rows>0) {
		$celebration = array();
		while ($row = $result->fetch_assoc()) {
			$celebration[] = array(
				'celebration_id' => $row["celebration_id"],
				'team_id' => $row["team_id"],
				'team_name' => get_team_name_by_team_id($row["team_id"]),
				'birthday_of_member_id' => $row["birthday_of_member_id"],
				'birthday_of_member_name' => get_team_member_name_by_team_member_id($row["birthday_of_member_id"]),
				'celebration_date' => $row["celebration_date"],
				'cake_amount' => $row["cake_amount"],
				'other_expense' => $row["other_expense"],
				'perhead_contribution' => round($row["perhead_contribution"],2),
				'total_attendees' => $row["total_attendees"],
				'attendees' => get_attendees_by_celebration_id($row["celebration_id"])
			);
		}
		$celebration_list = $celebration;
	}
	return $celebration_list;
}
function get_birthday_of_member_id_by_celebration_id($celebration_id){
	$connection = connect();
	$sql = "SELECT birthday_of_member_id FROM celebrations WHERE celebration_id = ".$celebration_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$birthday_of_member_id = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$birthday_of_member_id = $row["birthday_of_member_id"];
		}
	}
	return $birthday_of_member_id;
}
function get_attendees_by_celebration_id($celebration_id){
	$connection = connect();
	$sql = "SELECT DISTINCT member_id FROM celebration_attendees WHERE celebration_id = ".$celebration_id;
	$result = $connection->query($sql);
	disconnect($connection);

	if($result->num_rows>0){
		$attendees = array();
		while($row = $result->fetch_assoc()){
			$attendees[] = array("id" => $row["member_id"], "name" => get_team_member_name_by_team_member_id($row["member_id"]));
		}
	}
	return $attendees;
}
function post_add_celebration(Celebration $celebration){
	$connection = connect();
	$sql_celebration = "INSERT INTO celebrations (celebration_id, team_id, birthday_of_member_id, celebration_date, cake_amount, other_expense, perhead_contribution, total_attendees) VALUES (NULL, '".$celebration->team_id."', '".$celebration->birthday_of_member_id."', '".$celebration->celebration_date."', '".$celebration->cake_amount."', '".$celebration->other_expense."', '".$celebration->perhead_contribution."', '".$celebration->total_attendees."')";
	$result_celebration = $connection->query($sql_celebration);
	$sql_get_celebration_id = "SELECT celebration_id FROM celebrations ORDER BY celebration_id DESC LIMIT 1";
	$result_get_celebration_id = $connection->query($sql_get_celebration_id);
	$celebration_id = "";
	if ($result_get_celebration_id->num_rows>0) {
		while ($row = $result_get_celebration_id->fetch_assoc()) {
			$celebration_id = $row["celebration_id"];
		}
	}
	foreach ($celebration->attendees_member_id_array as $member_id) {
		// Entry in celebration attendees table for all members
		$sql_attendees = "INSERT INTO celebration_attendees (celebration_member_key, celebration_id, member_id) VALUES (NULL, '".$celebration_id."', '".$member_id."')";
		$result_attendees = $connection->query($sql_attendees);
		// Entry in transactions table for all members
		$transaction_sql = "INSERT INTO `transactions` (`transaction_id`, `transaction_amount`, `member_id`, `team_id`, `transaction_date`, `transaction_type`, `celebration_id`) VALUES (NULL, '".$celebration->perhead_contribution."', '".$member_id."', '".$celebration->team_id."', curdate(), 'debit', '".$celebration_id."' )";
		$transaction_result = $connection->query($transaction_sql);
		// Entry in team team members table for fund balance update
		$current_balance = get_member_fund_by_team_id_and_member_id($celebration->team_id,$member_id);
		$new_balance = ($current_balance - $celebration->perhead_contribution);
		$update_sql = "UPDATE `team_teammember` SET `fund_balance`= ".$new_balance." WHERE `team_id`= ".$celebration->team_id." and `member_id`= ".$member_id;
		$update_result = $connection->query($update_sql);
	}
	disconnect($connection);
	return ($result_celebration == true && $result_attendees == true && $transaction_result == true && $update_result == true)?true:false;
}
function get_celebrations_by_member_id($member_id){
	$connection = connect();
	$sql = "SELECT `celebration_id` FROM `celebration_attendees` WHERE `member_id` = ".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	if ($result->num_rows>0) {
		$celebration = array();
		while ($row = $result->fetch_assoc()) {
			$celebration_team_id = get_celebrations_by_celebration_id($row["celebration_id"])[0]["team_id"];
			if(is_member_part_of_team($member_id,$celebration_team_id)) {
				$celebration[] = get_celebrations_by_celebration_id($row["celebration_id"])[0];
			}
		}
	}
	return $celebration;
}


// Other Functions
function sanitize($data){
	return str_replace("'","''",$data);
}
function get_uuid(){
	return file_get_contents("https://www.uuidgenerator.net/api/version1");
}

function is_member_part_of_team($member_id, $team_id){
	$teams = get_member_details_by_member_id($member_id)[0]["teams"];
	foreach($teams as $team){
		if($team["id"] == $team_id){
			return true;
		}
	}
	return false;
}