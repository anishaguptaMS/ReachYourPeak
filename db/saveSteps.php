<?php

include_once("ryp.php");

initSession();

if(isset($_GET['userid'])) {
    if(isAdmin($userid)) {
        $uid = $_GET['userid'];
    } else {
        errorMSG('You do not have rights to add steps');
        die();
    }
} else {
    $uid = $userid;
}

//var_dump($_POST)

foreach ($_POST as $p => $v) {
    if(strpos($p, 'date') !== false) {
        $sql =  "delete from steps where user_id = :uid and step_date = str_to_date(:d, '%m/%d/%Y')";
        //echo debugSQL($sql, [ 'vals' => [ 'uid' => $uid, 'd' => $v]]);
        dml($conn, $sql, [ 'vals' => [ 'uid' => $uid, 'd' => $v]]);
    }
}

foreach ($_POST as $p => $v) {
    if(strpos($p, 'steps') !== false) {
        if($v && $v > 0) {
            echo $p . ' -- ' . str_replace('steps', 'date', $p) . ' -- ' . $_POST[str_replace('steps', 'date', $p)];
            $sql =  "insert into steps (user_id, step_date, steps, date_entered) values (:uid, str_to_date(:d, '%m/%d/%Y'), :steps, now())";
            echo debugSQL($sql, [ 'vals' => [ 'uid' => $uid, 'd' => $_POST[str_replace('steps', 'date', $p)] , 'steps' => $v]]);
            dml($conn, 
                $sql, 
                [ 'vals' => [ 'uid' => $uid, 'd' => $_POST[str_replace('steps', 'date', $p)] , 'steps' => $v]]);
        }
    }
}

json_data('Steps updated', []);


?>
