<?php

include_once("ryp.php");

initSession();

startTeam($conn, $_POST['name'], $_POST['cycleId'], $userid);

json_data("Team created", []);

?>
