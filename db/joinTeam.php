<?php

include_once("ryp.php");

initSession();

$tc = getTeamCycle($conn, $_POST['team_id']);


assignRoleToTeam($conn, $userid, $tc['ID'], getRoleId($conn, 'Member'));

json_data('You joined the team', []);



?>