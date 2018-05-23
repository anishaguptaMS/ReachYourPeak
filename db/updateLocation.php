<?php

include_once("ryp.php");

initAdmin();

requiresPost();

dml($conn, "update " .$dbTables['locations'] . ' set name = :name where id = :id', [ 'vals' => $_POST ]);

json_data('Location updated', []);

?>