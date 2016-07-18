<?php
require_once '././phpmailer/PHPMailerAutoload.php';

// Enable or Disable Email
$enable_email = true;

function email_init(){
  $mail = new PHPMailer();
  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'gator4179.hostgator.com';  // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'no-reply@onlinebirthdaymanager.com';                 // SMTP username
  $mail->Password = 'anshul@123';                           // SMTP password
  $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 465;                                    // TCP port to connect to
  $mail->From = "no-reply@onlinebirthdaymanager.com";
  $mail->FromName = "Online Birthday Manager";
  $mail->CharSet = 'UTF-8';
  $mail->addBCC('anshul.shrivastava123@gmail.com');
  $mail->isHTML(true); 
  return $mail;
}

function get_template($template_name){
  return file_get_contents(getcwd().'/scripts/email_templates/'.$template_name.'.php');
}

function send_password_reset_code(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/reset_password_link.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->Subject = "Password Reset Link - Online Birthday Manager";
  $body = str_replace("{first_name}",$member->first_name, $template);
  $body = str_replace("{reset_password_link}",$member->reset_password_link, $body);
  $mail->Body = $body;
  if($mail->send()){
    error_log("email sent");
  }
  else{
    error_log("mail not sent");
    error_log($mail->ErrorInfo);
  }
}

function send_registration_success_email(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/welcome.php');
  $mail = email_init();
  $mail->addAddress($member->email, $member->first_name);  
  $mail->Subject = 'Welcome to Online Birthday Manager';
  $body = str_replace("{first_name}",$member->first_name, $template);
  $body = str_replace("{magic_link}",get_autologin_link($member->email), $body);
  $mail->Body = $body;
  $GLOBALS['enable_email']==true?$mail->send():"";
}

function send_join_team_email(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/join_team.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->Subject = 'Joined Team - Online Birthday Manager';
  $body = str_replace("{first_name}",$member->first_name, $template);
  $body = str_replace("{team_name}",$member->team_name, $body);
  $mail->Body = $body;
  $GLOBALS['enable_email']==true?$mail->send():"";
}

function send_create_team_email(Team $team){
  $template = file_get_contents(getcwd().'/scripts/email_templates/join_team.php');
  $mail = email_init();
  $mail->addAddress($member->email, $member->first_name);  
  $mail->Subject = 'Team Created - Online Birthday Manager';
  $mail->Body = str_replace("{first_name}",$member->first_name, $template);
  $GLOBALS['enable_email']==true?$mail->send():"";
}

function send_delete_team_email(Team $team){
  $members = get_team_member_id_by_team_id($team->team_id);
  foreach ($members as $member){
    $template = get_template('team_deleted');
    $mail = email_init();
    $member_id = $member["id"];
    $member_name = $member["name"];
    $member_email = get_team_member_email_by_id($member_id);
    $mail->addAddress($member_email);
    $mail->Subject = 'Team Deleted - Online Birthday Manager';
    $body = str_replace("{first_name}",$member_name, $template);
    $body = str_replace("{team_name}",get_team_name_by_team_id($team->team_id), $body);
    $body = str_replace("{team_admin}",get_team_member_name_by_team_member_id(get_team_admin_id_by_team_id($team->team_id)), $body);
    $mail->Body = $body;
    $GLOBALS['enable_email']==true?$mail->send():"";
  }
}

function send_leave_team_email(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/left_team.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->addCC(get_team_member_email_by_id(get_team_admin_id_by_team_id($member->team_id)));
  $mail->Subject = 'Left Team - Online Birthday Manager';
  $body = str_replace("{first_name}",$member->first_name, $template);
  $body = str_replace("{team_name}",$member->team_name, $body);
  $body = str_replace("{team_admin}",get_team_member_name_by_team_member_id(get_team_admin_id_by_team_id($member->team_id)), $body);
  $mail->Body = $body;
  $GLOBALS['enable_email']==true?$mail->send():"";
}

function send_add_fund_email(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/add_fund.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->Subject = 'Fund Added - Online Birthday Manager';
  $member->first_name = get_team_member_name_by_team_member_id($member->member_id);
  $member->team_name = get_team_name_by_team_id($member->team_id);
  $body = str_replace("{first_name}",$member->first_name, $template);
  $body = str_replace("{team_name}",$member->team_name, $body);
  $body = str_replace("{topup}",$member->fund, $body);
  $body = str_replace("{magic_link}",get_autologin_link($member->email), $body);
  $body = str_replace("{fund_balance}", get_member_fund_by_team_id_and_member_id($member->team_id,$member->member_id), $body);
  $mail->Body= $body;
  $GLOBALS['enable_email']==true?$mail->send():"";
}

function send_birthday_celebration_fund_update_email_to_attendees(Celebration $celebration){
  $template = file_get_contents(getcwd().'/scripts/email_templates/fund_deducted.php');
  foreach ($celebration->attendees_member_id_array as $member_id) {
    $mail = email_init();
    $mail->addAddress(get_team_member_email_by_id($member_id));
    $mail->Subject = 'Fund Deducted - Online Birthday Manager';
    $first_name = get_team_member_name_by_team_member_id($member_id);
    $body = str_replace("{first_name}",$first_name, $template);
    $body = str_replace("{birthday_person}",get_team_member_name_by_team_member_id($celebration->birthday_of_member_id), $body);
    $body = str_replace("{team_name}",get_team_name_by_team_id($celebration->team_id), $body);
    $body = str_replace("{contribution}",$celebration->perhead_contribution, $body);
    $body = str_replace("{magic_link}",get_autologin_link(get_team_member_email_by_id($member_id)), $body);
    $body = str_replace("{new_fund_balance}", get_member_fund_by_team_id_and_member_id($celebration->team_id,$member_id), $body);
    $mail->Body= $body;
    $GLOBALS['enable_email']==true?$mail->send():"";
  }
}

function invite_to_team(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/invite_to_team.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->Subject = "Invitation to Join ".$member->team_name." - Online Birthday Manager";
  $body = str_replace("{email}",$member->email, $template);
  $body = str_replace("{team_name}",$member->team_name, $body);
  $body = str_replace("{invite_to_team_link}",$member->invite_team_link, $body);
  $mail->Body = $body;
  if($mail->send()){
    error_log("email sent");
    return array("email_sent" => true, "status_code" => 200);
  }
  else{
    error_log("mail not sent");
    error_log($mail->ErrorInfo);
    return array("email_sent" => false, "status_code" => 400);
  }
}

function send_weekly_birthday_alert(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/cron_weekly.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->Subject = "Upcoming Birthdays - Online Birthday Manager";
  $body = str_replace("{first_name}", $member->first_name, $template);
  $body = str_replace("{body}", "<table>{body}", $body);
  foreach ($member->birthday_members as $birthday_member) {
    $date = date("d M", strtotime($birthday_member["dob"]));
    $day = date("D", strtotime(date("d M", strtotime($birthday_member["dob"]))));
    $body = str_replace("{body}", "<tr>{body}", $body);
    $body = str_replace("{body}", "<td>{body}", $body);
    $body = str_replace("{body}", $birthday_member["first_name"]." ".$birthday_member["last_name"]."</td><td>".$date ." (".$day.")" . "</td>"."{body}", $body);
    $body = str_replace("{body}", "</td>{body}", $body);
    $body = str_replace("{body}", "</tr>{body}", $body);
  }
  $body = str_replace("{body}", "</table>", $body);
  $mail->Body = $body;
  if($mail->send()){
    error_log("email sent");
  }
  else{
    error_log("mail not sent");
    error_log($mail->ErrorInfo);
  }
}

function send_daily_birthday_alert(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/cron_daily.php');
  $mail = email_init();
  $mail->Subject = "Happy Birthday ".$member->first_name ." - Online Birthday Manager";
  $mail->addAddress($member->email);
  foreach ($member->birthday_members as $member_id) {
    $mail->addCC(get_team_member_email_by_id($member_id));
  }
  $body = str_replace("{first_name}", $member->first_name, $template);
  $mail->Body = $body;
  if($mail->send()){
    error_log("email sent");
  }
  else{
    error_log("mail not sent");
    error_log($mail->ErrorInfo);
  }
}
