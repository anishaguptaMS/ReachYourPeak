<?php

include_once("ryp.php");

initAdmin();

requiresPost();

if($_POST['id']) {
   dml($conn, "update " . $dbTables['users'] ." set name = :name, email = :email, verified_flag = :verified_flag, active_flag = :active_flag, location_id = :location_id where id = :id", [ 'vals' => $_POST]);
   json_data('User updated', []);
} else {
    unset($_POST['id']);
    $user = getUserFromEmail($conn, $_POST['email']);

    if($user) {
        errorMSG("User already exists");
        die();
    }
    
    dml($conn, 
            "insert into " . $dbTables['users'] . " (name, email, verified_flag, location_id, active_flag) values (:name, :email, :verified_flag, :location_id, :active_flag)", 
                [ 'vals' => $_POST] );
    json_data('User created', []);
                
}



?>

