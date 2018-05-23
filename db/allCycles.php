<?php

include_once("ryp.php");

initSession();

$sql = "select * from " . $dbTables['cycles'] . " order by year desc, start_date desc, name asc";


$data = sqlToJSON($conn, $sql);
	
json_data("Cycles", 
	$data
);

?>

