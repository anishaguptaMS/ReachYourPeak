<?php

include_once("ryp.php");

initDB();

if(isset($_POST) && isset($_POST['email'])) {
    $email = $_POST['email'];
} else if(isset($_GET)) {
    $email = $_GET['email'];
}    

$user = getUserFromEmail($conn, $email);

if(isset($user)) {
    sendPasswordResetLink($conn, $email, $user['NAME']);    
    json_data('Password reset link sent', []);
} else {
    errorMSG("No user with this email found", [ "data" => [] ]);
    
}

?>