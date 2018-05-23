<?php

include_once("ryp.php");

initDB();

$res = getAllActiveTeams($conn);
json_data('All teams retrieved', $res);

echo " Hello";

?>