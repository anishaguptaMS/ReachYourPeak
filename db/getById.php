<?php 

include_once("ryp.php");

initAdmin();

jsonById($conn, $_GET['table'], $_GET['id']);

?>

