<?php

include_once("ryp.php");

initDB();

jsonById($conn, 'teams', 41);

die();

$conn = openANCPROD();

getFirstRecord($conn, "select * from z_ryp_team_cycles where cycle_id = :cycle and team_id = :team", 
            [ 'vals' => [ 'team' => 16, 'cycle' => 1]]);

            
die();            
//var_dump(getRecByID($conn, 'z_ryp_team_cycles', 14)) ;

$id = '13';

$cycleId = '1';

$cid = getFirstRecord($conn, "select * from z_ryp_team_cy_2 where cy_id = :cycle and team_id = :team", 
            [ "vals" => [ "cycle" => $cycleId, "team" => $id ]]);
var_dump($cid);

/*

var_dump(sqlToJSON($conn, "select * from z_ryp_team_cycles where id = '14' and team_id = '14'" , []));


$res = sqlToJSON($conn,   "select * from z_ryp_team_cycles where cycle_id = :bert and team_id = :team", ['vals' =>[ 'bert' => 1, 'team' => 14] ]);

echo "<br><br>";
var_dump($res);
*/

$r = [ 'vals' => [ 'bert' => 1, 'team' => 14]];

    $sql = "select * from z_ryp_team_cycles where cycle_id = :bert and team_id = :team";
	$stid = oci_parse($conn, $sql);
    $rr = $r['vals'];
    foreach($r['vals'] as $p => $v) {
        echo $p . ' '. $v;
        oci_bind_by_name ($stid, ':' . $p, $r['vals'][$p]);
    }
	oci_execute($stid);
	oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);	
	oci_free_statement($stid);
    var_dump($res); 


?>

<p>
Hello world!

</p>