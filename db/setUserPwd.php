<?php

include_once("ryp.php");

initAdmin();

requiresPost();

$pwd = md5($_POST['password']);

dml($conn, "update ". $dbTables['users'] . "  set password = :pwd, reset_link_date = now() where id = :id", [ 'vals' => [ 'id' => $_POST['id'],  'pwd' => $pwd]]);

json_data('Password set', []);


?>
