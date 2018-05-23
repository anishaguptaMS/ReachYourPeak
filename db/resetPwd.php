<?php

include_once("ryp.php");

initDB();

$rec = getFirstRecord($conn, 
     "select * from ". $dbTables['users']. " where pwd_reset_link = :link", 
    [ 'vals' => ["link" => $_POST['link']]]
);

if(! $rec ) {
    errorMSG("Invalid or expired link", []);
    die();
}

try {
    dml($conn, 
        "update " . $dbTables['users'] . " set password = :password, verified_flag='Y' where pwd_reset_link = :link", 
            [ 'vals' => [ "password" => md5($_POST['password']), "link" => $_POST['link']]] );
} catch(Exception $e) {
    errorMSG($e, []);
    die();
}

json_data('Password has been reset.  Now please log in.', []);

?>