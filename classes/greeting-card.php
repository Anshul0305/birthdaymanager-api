<?php
require_once('././scripts/database.php');

class GreetingCard{
	public $receiver_id;
	public $sender_id;
	public $team_id;
	public $greeting_card_id;
	public $greeting_card_mail_subject;
	public $greeting_card_message;
	public $greeting_card_link;
	public $creation_date;
	public $send_date;
	public $message_for_team;

	public function process_get($greeting_card_id, $subquery){
		switch($subquery){
			case "stub":{

			}
			break;

			default:{
				$greeting_card_list = get_greetings_by_greeting_card_id($greeting_card_id);
				return $greeting_card_list;
			}
			break;
		}
	}

	public function process_post($action){
		switch($action){
			case "greeting-card":{
				$db_status = post_greeting_card($this);
				if($db_status["status_code"] == 200){
					$this->greeting_card_id = $db_status["greeting_card_id"];
					$this->greeting_card_link = $this->get_greeting_card_link($this);
					send_greeting_card_email($this);
					return "200";
				}
				else return "401";
			}
			break;
			case "team-greeting-card":{
				$db_status = post_greeting_card($this);
				if($db_status["status_code"] == 200){
					$this->greeting_card_id = $db_status["greeting_card_id"];
					send_team_greeting_card_email($this);
					return "200";
				}
				else return "401";
			}
			break;
			case "sign-team-greeting-card":{
				$db_status = post_sign_greeting_card($this);
				if($db_status["status_code"] == 200){
					return "200";
				}
				else return "401";
			}
			break;
			default:{

			}
		}
	}

	private function get_greeting_card_link(GreetingCard $greetingCard){
		$link = get_autologin_link_by_member_id($greetingCard->receiver_id);
		$link .="&destination=greetings?greeting-card-id=".$greetingCard->greeting_card_id;
		return $link;
	}
}