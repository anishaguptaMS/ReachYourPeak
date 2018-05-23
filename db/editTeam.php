<?php

include_once("ryp.php");

initAdmin();

if($_POST['action'] == 'new') {
    unset($_POST['action']);
    dml($conn, 'insert into ' . $dbTables['teams'] . ' (name, location_id, active_flag) values (:name, :location_id, :active_flag)', [ 'vals' => $_POST]);
    json_data('Team added', []);
} else {
    errorMSG('Not implemented');
}

?>