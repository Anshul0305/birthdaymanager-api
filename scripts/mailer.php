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
  $mail->addBCC('anshul.shrivastava123@gmail.com');
  $mail->isHTML(true); 
  return $mail;
}

function send_password_reset_code(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/reset_password_link.php');
  $mail = email_init();
  $mail->addAddress($member->email);
  $mail->Subject = "Password Reset Code - Online Birthday Manager";
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
  $mail->Body = str_replace("{first_name}",$member->first_name, $template);
  $GLOBALS['enable_email']==true?$mail->send():"";
}

function send_join_team_email(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/join_team.php');
  $mail = email_init();
  $mail->addAddress($member->email, $member->first_name);  
  $mail->Subject = 'Team Joined - Online Birthday Manager';
  $mail->Body = str_replace("{first_name}",$member->first_name, $template);
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

function send_add_fund_email(Member $member){
  $template = file_get_contents(getcwd().'/scripts/email_templates/add_fund.php');
  $mail = email_init();
  $mail->addAddress($member->email, $member->first_name);  
  $mail->Subject = 'Fund Added - Online Birthday Manager';
  $mail->Body = str_replace("{first_name}",$member->first_name, $template);
  $GLOBALS['enable_email']==true?$mail->send():"";
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
  }
  else{
    error_log("mail not sent");
    error_log($mail->ErrorInfo);
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
