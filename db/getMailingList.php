<?php

include_once("ryp.php");

initAdmin();

jsonData($conn, 'Mailing lists', 'select * from mail_list order by name');


?>