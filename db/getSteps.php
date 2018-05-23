<?php

include_once("ryp.php");

initSession();

if(isset($_GET['userid'])) {
    $uid = $_GET['userid'];
} else {
    $uid = $userid;
}

$sql=   "select * from steps where user_id = :uid and step_date >= str_to_date(:sd, '%m/%d/%Y')";

//echo debugSql($sql,  ['vals' => [ 'uid' => $uid, 'sd' => $_GET['sd']]]);



$data = sqlToJSON($conn,
    "select * from steps where user_id = :uid and step_date >= :sd",
    ['vals' => [ 'uid' => $uid, 'sd' => $_GET['sd']]]
);

json_data("Steps", $data);


?>