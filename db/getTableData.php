<?php 

include_once("ryp.php");

initAdmin();

jsonFullTable($conn, $_GET['table']);

?>

