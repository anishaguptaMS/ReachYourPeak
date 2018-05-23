<?php

include_once("ryp.php");

initDB();

jsonFullTable($conn, 'locations', ' upper(name)');


?>

