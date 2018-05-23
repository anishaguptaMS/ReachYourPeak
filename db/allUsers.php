<?php

include_once("ryp.php");

initAdmin();

$sql = "select id, name, email, verified_flag, last_logged_in, active_flag, reset_link_date from " . $dbTables['users'];

jsonData($conn, 'Users', $sql)


?>

