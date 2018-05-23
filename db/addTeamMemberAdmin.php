<?php

include_once("ryp.php");

initAdmin();

requiresPost();

$user = getDataById($conn, 'users', $_POST['id']);

if(! $user) {
    errorMSG("User does not exists");
    die();
}

if($_POST['role']) {
    $role_id = getRoleID($conn, $_POST['role']);
    $teamCycle = getTeamCycle($conn, $_POST['teamId']);
    assignRoleToTeam($conn, $_POST['id'], $teamCycle['ID'], $role_id);        
    json_data('Team member added', []);
} else {
    errorMSG("Role required");
    die();
}


?>
