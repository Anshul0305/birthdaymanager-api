<?php
require_once "../mailer.php";
require_once "../database.php";
require_once "../../classes/alert.php";

cron_send_weekly_birthday_alert();

// CRON Job Functions
function cron_get_weekly_birthday_alert(){
    $admin_ids = get_all_admin_ids();
    $alerts = array();
    foreach ($admin_ids as $admin_id) {
        $alert = new Alert;
        $alert->email = get_team_member_email_by_member_id($admin_id);
        $alert->name = get_team_member_name_by_team_member_id($admin_id);
        $alert->weekly_birthday_alert_body = get_weekly_birthday_alert_body($admin_id);
        array_push($alerts,$alert);
    }
    return $alerts;
}

function get_weekly_birthday_alert_body($admin_id){
    $member_ids = get_team_members_by_member_id($admin_id);
    $body = get_members_having_birthday_next_week($member_ids);
    return $body;
}

function get_members_having_birthday_next_week($member_ids){
    $body = "";
    foreach ($member_ids as $member_id) {
        if (is_birthday_in_next_week($member_id)){
            $body.=create_weekly_alert_body($body,$member_id);
        }
    }
    return $body;
}

function is_birthday_in_next_week($member_id){
    $dob = get_team_member_official_dob_team_member_id($member_id);
    if($dob >= date("Y-m-d") && $dob < date("Y-m-d", strtotime("+1 week"))){
        return true;
    }
    else{
        return false;
    }

}

function create_weekly_alert_body($body,$member_id){
    $body.=get_team_member_name_by_team_member_id($member_id);
    $body.="<br>";
    $body.=get_team_member_official_dob_team_member_id($member_id);
    $body.="<br><br>";
    return $body;
}
