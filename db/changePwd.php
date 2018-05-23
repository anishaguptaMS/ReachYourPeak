<?php

include_once("ryp.php");

initSession();

requiresPost();

if(strlen($_POST['password']) < 8) {
    errorMSG('Password needs to be at least 8 characters', []);
    die();
}

$rec = getFirstRecord($conn, 
     "select * from ". $dbTables['users']. " where password = :pwd and id = :id", 
    [ 'vals' => ["pwd" => md5($_POST['oldPassword']), 'id' => $userid]]
);

if(! $rec) {
    errorMSG("Your old password does not match", []);
    die();
}



$pwd = md5($_POST['password']);

dml($conn, "update ". $dbTables['users'] . "  set password = :pwd, reset_link_date = now() where id = :id", [ 'vals' => [ 'id' => $userid,  'pwd' => $pwd]]);

json_data('Password set', []);


?>