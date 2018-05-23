<?php

include_once("ryp.php");

initAdmin();

$admins = sqlToJSON($conn,
    "SELECT u.id, u.name, u.email, u.active_flag, m.id as rm_id FROM users u
            join role_members m on m.user_id = u.id
            join roles r on r.id = m.role_id where r.name = 'Admin'  order by upper(u.name)", []
);


json_data('Admins', $admins);


?>