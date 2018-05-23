<?php

include_once("ryp.php");

initSession();


json_data('Check admin', [ 'isAdmin' => isAdmin($conn, $userid) ]);


?>

