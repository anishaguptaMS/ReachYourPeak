<?php

include_once("ryp.php");

initAdmin();

dml($conn, "insert into " . $dbTables['locations'] . " (name) values (:name)", [ 'vals' => ['name' => $_POST['name']]]);

json_data('Location added', []);

?>