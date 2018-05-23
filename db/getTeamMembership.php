<?php

include_once("ryp.php");

initSession();


$res = getTeamMembership($conn, $userid);

json_data('Team membership', $res);



?>