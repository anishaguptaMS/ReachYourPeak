<?php

include_once("ryp.php");

initAdmin();

if($_POST['action'] == 'new') {
    unset($_POST['action']);
    unset($_POST['ID']);
    dml($conn, 'insert into ' . $dbTables['cycles'] . ' (name, year, start_date, end_date, active_flag) values (:name, :year, :start_date, :end_date, :active_flag)', [ 'vals' => $_POST]);
    json_data('Cycle added', []);
} else if($_POST['action'] == 'edit') {
    unset($_POST['action']);
    dml($conn, 'update ' . $dbTables['cycles'] . ' set name = :name, year = :year, start_date = :start_date, end_date = :end_date, active_flag = :active_flag where ID = :ID', [ 'vals' => $_POST]);
    json_data('Cycle updated', []);
}
    
?>

