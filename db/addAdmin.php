<?php

include_once("ryp.php");

initAdmin();

requiresPost();

if(isset($_POST['id'])) {
    $user = getDataById($conn, 'users', $_POST['id']);
    if(isset($user)) {
        addAdminRole($conn, $_POST['id']);
        json_data('Admin added', []);
    } else {
        errorMSG("User not found with this ID: ". $_POST['id'], []);
    }
    
} else {
    
    errorMSG("ID expected", []);
}
    



?>

