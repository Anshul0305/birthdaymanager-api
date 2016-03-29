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


// Team Functions
function get_team_details_by_team_id($team_id){
	$connection = connect();
	$sql = $team_id == "" ? ("SELECT team_id, team_name, team_admin_id FROM team") : ( "SELECT team_id, team_name, team_admin_id FROM team WHERE team_id = ". $team_id);
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
	return $team_name;
}
function get_team_member_id_by_team_id($team_id){
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
function get_team_member_name_by_team_member_id_array($team_member_id_array){
	$team_member_name_array = array();
	foreach($team_member_id_array as $team_member_id){
		$team_member_name_array[] = get_team_member_name_by_team_member_id($team_member_id);
	}
	return $team_member_name_array;
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
	return $team_fund_balance;
}
function get_member_fund_by_team_id_and_member_id($team_id, $member_id){
	$connection = connect();
	$sql = "SELECT sum(fund_balance) as fund_balance FROM team_teammember WHERE team_id = ".$team_id." and member_id = ".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$member_fund_balance = "";
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$member_fund_balance = $row["fund_balance"];
		}
	}
	return $member_fund_balance;
}
function post_create_new_team(Team $team){
	$team_name = $team->team_name;
	$team_admin_id = $team->admin_id;
	$connection = connect();
	$sql = "INSERT INTO team (team_id, team_name, team_admin_id) VALUES (NULL,'".$team_name."',".$team_admin_id.")";
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
function search_teams($search_term){
	$connection = connect();
	$sql = "SELECT team_id, team_name, team_admin_id FROM team WHERE team_name like '%".$search_term."%'";
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
function get_team_id_by_member_id($member_id){
	$connection = connect();
	$sql = "SELECT DISTINCT team_id FROM team_teammember WHERE member_id=".$member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_id = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_id[] = $row["team_id"];
		}
	}
	return $team_id;
}
function get_team_details_by_team_id_and_member_id($team_id,$member_id){
	$connection = connect();
	$sql = $team_id == "" ? ("SELECT team_id, team_name, team_admin_id FROM team") : ( "SELECT team_id, team_name, team_admin_id FROM team WHERE team_id = ". $team_id);
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
	$password = $member->password;

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
function register_new_member(Member $member){
	if(!is_member($member->email)) {
		$connection = connect();
		$sql = "INSERT INTO team_members (member_id, first_name, last_name, email, password, dob, official_dob) VALUES (NULL, '".$member->first_name."', '".$member->last_name."', '".$member->email."' , '".$member->password."', '3000-01-01', '".$member->official_dob."')";
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


// Fund Functions
function post_add_fund(Member $member){
	$team_id = $member->team_id;
	$member_id = $member->member_id;
	$fund_amount = $member->fund;
	$connection = connect();
	$sql = "INSERT INTO team_teammember (`team_team_member_id`, `team_id`, `member_id`, `fund_balance`) VALUES (NULL, '".$team_id."', '".$member_id."', '".$fund_amount."');";
	$result = $connection->query($sql);
	disconnect($connection);
	return $result;
}
