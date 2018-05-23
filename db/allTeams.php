<?php

include_once("ryp.php");

initSession();

$sql = "select t.id, t.name, l.name as location, t.active_flag from " . $dbTables['teams'] . " t join " . $dbTables['locations'] . " l on l.id = t.location_id order by upper(t.name)";



$data = sqlToJson($conn, $sql);

json_data("Teams", 
	$data
);

?>

