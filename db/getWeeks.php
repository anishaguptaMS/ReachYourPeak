<?php

include_once("ryp.php");

initSession();

if(isset($_GET['userid'])) {
    $uid = $_GET['userid'];
} else {
    $uid = $userid;
}

$data = getFirstRecord($conn,  "SELECT c.* FROM users u 
    join role_members r on r.user_id = u.id 
    join team_cycles tc on r.team_id = tc.id 
    join cycle c on tc.cycle_id = c.ID where u.id = :uid", [ 'vals' => ['uid' => $uid ]]);

json_data('Weeks', $data);


?>
