<?php

include_once("ryp.php");

initAdmin();

if($_POST['action'] == 'new') {
    $team_oid = dml($conn, "insert into " . $dbTables['teams']. " (name, location_id, active_flag) values (:name, :location_id, :active_flag)", 
        [ 'vals' => [ 'name' => $_POST['name'], 'location_id' => $_POST['location_id'], 'active_flag' => $_POST['active_flag']]] );
    dml($conn, "insert into " . $dbTables['teamCycles']. " (team_id, cycle_id, active_flag) values (:id, :cycle_id, :active_flag)", 
        [ 'vals' => [ 'id' => $team_id, 'cycle_id' => $_POST['cycle_id'], 'active_flag' => $_POST['active_flag']]]);
    
} else if ($_POST['action'] == 'edit') {
    dml($conn, "update " . $dbTables['teams']. " set name = :name, location_id = :location_id, active_flag = :active_flag where id = :id",
        [ 'vals' => [ 'name' => $_POST['name'], 'location_id' => $_POST['location_id'], 'active_flag' => $_POST['active_flag'], 'id' => $_POST['id']]] );
    $cycle = getFirstRecord($conn, "SELECT * FROM `team_cycles` WHERE team_id = :id and active_flag in ('A', 'E')" , [ 'vals' => [ 'id' => $_POST['id']]]);
    if(isset($cycle)) {
        dml($conn, "update " . $dbTables['teamCycles']. " set cycle_id = :cycle_id, active_flag = :active_flag where id = :id",         
            [ 'vals' => [ 'id' => $cycle['id'], 'cycle_id' => $_POST['cycle_id'], 'active_flag' => $_POST['active_flag']]]);
    } else {
        dml($conn, "insert into " . $dbTables['teamCycles']. " (team_id, cycle_id, active_flag) values (:id, :cycle_id, :active_flag)", 
            [ 'vals' => [ 'id' => $_POST['id'], 'cycle_id' => $_POST['cycle_id'], 'active_flag' => $_POST['active_flag']]]);
    }
}
     

json_data('Team updated', []);

?>