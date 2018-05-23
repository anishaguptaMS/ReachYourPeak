<?php

include_once("ryp.php");

initSession();

function isInteger($input){
    return(ctype_digit(strval($input)));
}

if(! isset($_GET) || ! $_GET['search']) {
    errorMSG('Search expected', []);
    die();
}

$search = $_GET['search'];

if(isInteger($search)) {
    $data = getDataById($conn, 'users', intval($search));
    json_data('Users', [$data]);
} else {
    $sql = "select * from " . $dbTables['users'] . " where upper(name) like upper(concat('%', :s1, '%')) or upper(email) like upper(concat('%', :s2, '%'))";
    json_data('Users', sqlToJSON($conn, $sql, [ 'vals' => ['s1' => $search, 's2' => $search]]));
} 


?>