<?php


function connect(){
	$servername = 'localhost';
	$username = 'anshul';
	$password = 'anshul';
	$db = 'birthdaymanager';
	$connection = new mysqli($servername, $username, $password, $db);
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
	

// ************************** Team Members **************************

function get_team_member_from_db($id, $subquery){
	$connection = connect();
	switch ($subquery) {
		case '':
			switch($id){
				case '':
					$sql = "SELECT member_id, first_name, last_name, email, dob, official_dob, member_type FROM team_members";
					break;
				default:
					$sql = "SELECT member_id, first_name, last_name, email, dob, official_dob, member_type FROM team_members WHERE member_id=".$id;
					break;
				}
			break;
		case 'member-id':
			$sql = "SELECT member_id, first_name, last_name, email, dob, official_dob, member_type FROM team_members WHERE member_id=".$id;
			break;
		case 'team-id':
			$sql = "SELECT member_id, first_name, last_name, email, dob, official_dob, member_type FROM team_members WHERE member_id in (SELECT member_id from team_teammember where team_id = ".$id.")";
			break;
		default:
			# code...
			break;
	}
	$result = $connection->query($sql);
	disconnect($connection);
	$teammembers = array();
		if($result->num_rows>0){
			while($row = $result->fetch_assoc()){
					$teammembers[] = array('member_id'=>$row['member_id'], 'first_name'=>$row['first_name'],'last_name'=>$row['last_name'], 'dob'=>$row['dob'], 'official_dob'=>$row['official_dob'], 'email'=>$row['email'], 'team_details'=> get_team_details_for_teammember($row['member_id']));
			}
		}
	return $teammembers;
}

function get_team_details_for_teammember($member_id)
{
	ChromePhp::log("here");
	$connection = connect();
	$sql = "SELECT team_id, member_type, fund_balance FROM team_teammember WHERE member_id = ". $member_id;
	$result = $connection->query($sql);
	disconnect($connection);
	$team_details = array();
	if ($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
			$team_details[] = array("team_name" => get_team_name_by_id($row["team_id"]), "member_type" => $row["member_type"], "fund_balance" => $row["fund_balance"]);
		}
	}
	return $team_details;
}

function get_team_name_by_id($team_id){
	ChromePhp::log("here");
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

// ************************** Funds **************************

function get_fund_from_db($id, $subquery){
	$connection = connect();
	switch ($subquery) {
		case '':
			$sql = "Select member_id, first_name, last_name, fund_balance from team_members";
			break;
		case 'fund':
			if ($id = "") {
				$sql = "Select member_id, first_name, last_name, fund_balance from team_members";
			} else {
				$sql = "Select member_id, first_name, last_name, fund_balance from team_members where member_id = " . $id;
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$funds[] = array('member_id' => $row['member_id'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'fund_balance' => $row['fund_balance'], 'last_topup' => get_last_topup_from_db($row['member_id']));
					}
				}
				return $funds;
			}

			function get_last_topup_from_db($member_id)
			{
				$connection = connect();
				$sql = "SELECT `transaction_amount` FROM `transactions` WHERE member_id=" . $member_id . " and transaction_type=\"credit\" ORDER BY `transaction_id` DESC LIMIT 1";
				$result = $connection->query($sql);
				disconnect($connection);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$topup = $row['transaction_amount'];
					}
				}
				return $topup;
			}


// ************************** Others **************************

			function add_team_member_to_db($first_name, $last_name, $email, $password, $official_dob, $member_type)
			{
				$connection = connect();
				$sql = "INSERT INTO `team_members`(`first_name`, `last_name`, `email`, `password`, `dob`, `official_dob`, `member_type`) VALUES ('" . $first_name . "','" . $last_name . "','" . $email . "', '" . $password . "', '" . $official_dob . "','" . $official_dob . "','" . $member_type . "')";
				$result = $connection->query($sql);
				disconnect($connection);
				return return_message($result);
			}

			function add_fund_to_db($member_id, $new_fund_balance, $topup_amount)
			{
				$connection = connect();
				$sql_team_member = "UPDATE `team_members` SET `fund_balance` = '" . $new_fund_balance . "' WHERE `team_members`.`member_id` = " . $member_id . "";
				$result = $connection->query($sql_team_member);

				$sql_transaction = "INSERT INTO `transactions` ( `transaction_amount`, `member_id`, `transaction_date`, `transaction_type`, `celebration_id`) VALUES ('" . $topup_amount . "', '" . $member_id . "', '" . date('Y-m-d') . "', 'credit', NULL)";
				$result_transaction = $connection->query($sql_transaction);

				disconnect($connection);
				return return_message($result);
			}


			function celebration_add_to_db($birthday_of_member_id, $celebartion_date, $cake_amount, $total_attendees, $attendees_member_id, $perhead_contribution)
			{
				$connection = connect();

				// Insert into Celebrations table
				$sql = "INSERT INTO `celebrations` (`birthday_of_member_id`, `celebration_date`, `cake_amount`, `total_attendees`, `perhead_contribution`) VALUES ('" . $birthday_of_member_id . "', '" . $celebartion_date . "', '" . $cake_amount . "', '" . $total_attendees . "', '" . $perhead_contribution . "')";
				$result = $connection->query($sql);

				// Get the celebration ID of last celebration
				$sql = "SELECT `celebration_id` FROM `celebrations` ORDER BY celebration_id DESC LIMIT 1";
				$result = $connection->query($sql);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$celebration_id = $row['celebration_id'];
					}
				}

				// Insert attendees data in Celebration attendees table
				foreach ($attendees_member_id as $member_id) {
					$sql = "INSERT INTO `celebration_attendees` (`celebration_id`, `member_id`) VALUES ('" . $celebration_id . "', '" . $member_id . "')";
					$result = $connection->query($sql);
				}

				// Add fund debit data in Transaction table
				foreach ($attendees_member_id as $member_id) {
					$sql = "INSERT INTO `transactions` ( `transaction_amount`, `member_id`, `transaction_date`, `transaction_type`, `celebration_id`) VALUES ('" . $perhead_contribution . "', '" . $member_id . "', '" . date('Y-m-d') . "', 'debit', " . $celebration_id . ")";
					$result = $connection->query($sql);
				}

				// Update the Team member table with fund balance of each member
				foreach ($attendees_member_id as $member_id) {
					$member_current_fund_balance = get_fund_from_db($member_id);
					$new_fund_balance = ((float)$member_current_fund_balance - (float)$perhead_contribution);
					$sql = "UPDATE `team_members` SET `fund_balance` = '" . $new_fund_balance . "' WHERE `team_members`.`member_id` = " . $member_id . "";
					$result = $connection->query($sql);
				}

				// Disconnect the connection and return the result
				disconnect($connection);
				return return_message($result);
			}


			function get_all_celebrations_from_db()
			{
				$connection = connect();
				$sql = "SELECT `celebration_id`, `birthday_of_member_id`, `celebration_date`, `cake_amount`, `total_attendees`, `perhead_contribution` FROM `celebrations`";
				$result = $connection->query($sql);
				disconnect($connection);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$celebrations[] = array("team_name" => "BBC Team", "birthday_of_member" => get_name_by_id($row["birthday_of_member_id"]), "celebration_date" => $row["celebration_date"], "cake_amount" => "GBP " . $row["cake_amount"], "total_attendees" => $row["total_attendees"], "attendees" => get_attendees_by_celebration_id($row["celebration_id"]), "perhead_contribution" => "GBP " . $row["perhead_contribution"]);
					}
				}
				return $celebrations;
			}


			function get_attendees_by_celebration_id($celebration_id)
			{
				$connection = connect();
				$sql = "SELECT `member_id` FROM `celebration_attendees` WHERE `celebration_id` = " . $celebration_id;
				$result = $connection->query($sql);
				disconnect($connection);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$attendees[] = get_name_by_id($row['member_id']);
					}
				}
				return $attendees;
			}

			function get_name_by_id($member_id)
			{
				$connection = connect();
				$sql = "SELECT `first_name`, `last_name` FROM `team_members` WHERE `member_id` = " . $member_id;
				$result = $connection->query($sql);
				disconnect($connection);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$first_name = $row['first_name'];
						$last_name = $row['last_name'];
					}
				}
				ChromePhp::log($member_id);
				$full_name = $first_name . " " . $last_name;
				return $full_name;
			}

	}
}

?>