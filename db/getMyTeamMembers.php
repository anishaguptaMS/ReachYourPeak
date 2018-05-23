<?php

include_once("ryp.php");

initSession();

$sql = "select m.id, u.name, r.name as role_name, t.name as team_name, u.email, u.active_flag, l.name as location from " . $dbTables['teams'] . " t
join " . $dbTables['teamCycles'] . "  tc on tc.team_id = t.id
join " . $dbTables['roleMembers'] . "  m on m.team_id = tc.id
join " . $dbTables['roles'] . "  r on m.role_id = r.id
join " . $dbTables['users'] . "  u on m.user_id = u.id 
join " . $dbTables['locations'] . "  l on u.location_id = l.id
where t.active_flag in ('E', 'A') and t.id = :id";

$data = sqlToJSON($conn, 
		$sql,
        [   "vals" => ['id' => $_GET['TEAM_ID'] ]]);
	

json_data("Users", 
	$data
);


?>