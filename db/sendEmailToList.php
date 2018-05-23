<?php

include_once("ryp.php");
include_once('emails.php');

initAdmin();

requiresPost();

//$text = $_POST['body'];

$emails = extractEmails(getEmailList($conn, 2));

storeDraftEmail($conn, [
   'subject' => $_POST['subject'],
   'body' => $_POST['body'],
   'bcc' => $emails
]);

json_data("Emails stored", []);

?>

