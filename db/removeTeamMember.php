<?php

include_once("ryp.php");

initAdmin();

requiresPost();

dml($conn, "delete from " . $dbTables['roleMembers']. " where id = :id", [ 'vals' => $_POST]);

json_data('Team member removed', []);

?>