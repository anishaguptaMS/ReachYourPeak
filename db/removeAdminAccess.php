<?php

include_once("ryp.php");

initAdmin();

delById($conn, 'roleMembers', $_POST['id']);

json_data('Admin removed', []);


?>