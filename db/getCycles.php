<?php

include_once("ryp.php");

initSession();

json_data('Cycles retrieved', getCycles($conn, $_GET['status']));


?>