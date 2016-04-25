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
  $mail = email_init();
  $mail->addAddress("anshul.shrivastava123@gmail.com");
  $mail->Subject = "Password Reset Code";
  $GLOBALS['enable_email']==true?$mail->send():"";
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
