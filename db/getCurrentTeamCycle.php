<?php

include_once("ryp.php");

initSession();

$id = $_GET['id'];

$team = getDataById($conn, 'teams', $id);


$cycle = getTeamCycle($conn, $id);

$location = getDataById($conn, 'locations', $team['location_id']);

if(isset($cycle)) {
    $team['cycle_id'] = $cycle['cycle_id'];
    $cycle_name = getDataById($conn, 'cycles', $cycle['cycle_id']);
    if(isset($cycle_name)) $team['cycle'] = $cycle_name['name'];
}

if(isset($location)) {
    $team['location'] = $location['name'];
}

json_data('Team', $team);



?>
