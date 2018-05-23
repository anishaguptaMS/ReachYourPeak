<?php

include_once("ryp.php");

initDB();

if(isset($_POST) && isset($_POST['email'])) {
    $email = $_POST['email'];
} else if(isset($_GET)) {
    $email = $_GET['email'];
}    

$user = getUserFromEmail($conn, $email);

if(isset($user) && $user['password'] == md5($_POST['password'])) {
    $sessionKey = createSession($conn, $user['ID'], $_POST['sessionType']);
    json_data('Login correct', [ 'sessionKey' => $sessionKey, 'id' => $user['ID'], 'name' => $user['name']]);
} else {
    errorMSG("Email or password is incorrect", [ "data" => [] ]);
    
}

?>