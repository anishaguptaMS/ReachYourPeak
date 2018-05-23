<?php

include_once("ryp.php");

initDB();

$res = hasValidSession($conn);

if($res) {

    echo json_data ('Valid session',  [ 'hasSession' => true, 'name' => $res['name']]);

}  else {
    echo json_encode ([ 'result' => 'OK', 'data' => [ 'hasSession' => false]]);
    
}


?>