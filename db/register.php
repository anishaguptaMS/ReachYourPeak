<?php

include_once("ryp.php");

initDB();

try {
    dml($conn, 
        "insert into " . $dbTables['users'] . " (name, email, verified_flag, location_id, active_flag) values (:name, :email, 'N', :location_id, 'A')", [ 'vals' => $_POST, 'debug' => true ] );
} catch(Exception $e) {
    errorMSG($e, []);
    die();
}

sendPasswordResetLink($conn, $_POST['email'], $_POST['name']);
json_data('Registration is completed.  Wait for email to confirm.   This may take a while.', []);

?>