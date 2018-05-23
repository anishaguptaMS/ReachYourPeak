<?php

include_once("ryp.php");

initSession();

requiresPost();

$user = getUserFromEmail($conn, $_POST['email']);



if($user) {
    errorMSG("User already exists");
    die();
}

$team = getTeamMembership($conn, $_COOKIE['userid']);
if(isset($team)) {

    if($team['ROLE_NAME'] == 'Captain') {
        
        dml($conn, 
            "insert into " . $dbTables['users'] . " (name, email, verified_flag, location_id, active_flag) values (:name, :email, 'N', :location_id, 'A')", 
                [ 'vals' => $_POST] );
        $newUser = getUserFromEmail($conn, $_POST['email']);
        assignRoleToTeam($conn, $newUser['ID'], $team['TCM_ID'], getRoleID($conn, 'Member'));
    } else {
        errorMSG("You need to be Captian of your team");
    }
} else {
    errorMSG("You do not belong to a team");
    
}


?>
