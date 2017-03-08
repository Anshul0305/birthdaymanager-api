<?php
require_once('././scripts/database.php');

class GreetingCard{
	public $receiver_id;
	public $sender_id;
	public $greeting_card_mail_subject;
	public $greeting_card_message;
	public $greeting_card_link;

	public function process_post($action){
		switch($action){
			case "greeting-card":{
				$db_status = send_greeting_card($this);
				$mail_status = send_greeting_card_email($this);
				if($db_status == 200 && $mail_status == 200)
				return "200";
				else return "401";
			}
			break;
			default:{

			}
		}
	}
}