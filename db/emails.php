<?php

include_once("ryp.php");

initDB();

$emailConfig = [
  'defaultFrom' => 'donotreply@mountsinai.org',
  'defaultReplyTo' => 'donotreply@mountsinai.org',
];

function getEmailHeader($config = NULL) {
    global $emailConfig;
    if(! $config) $config = $emailConfig;
    $headers = 'From: ' .  $config['defaultFrom'] . "\r\n" .
        'Reply-To: ' . $config['defaultReplyTo'] . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    return $headers;
}

function getEmailList($conn, $id) {
    $emails = getDataById($conn, 'mailList', $id);
    if($emails) {        
        return sqlToJSON($conn, $emails['sql'], []);
    }
}

function extractEmails($list) {
    $ar = [];
    foreach($list as $r) {
        array_push($ar, $r['email']);
    }
    return $ar;
}

function loadTargets($conn, $email_id, $target, $type) {
    if(gettype($target) == 'string')  {
        dml($conn, "insert into email_targets (email_id, target_type, email_list) values (:email_id, :type, :emails)", 
            [ 'vals' => [ 
                'email_id' => $email_id,
                'type' => $type,
                'emails' => $target
            ]]
        );
    } else if(gettype($target) == 'array') {
        $len = sizeof($target) / 100;
        for($i=0; $i<$len; $i++) {
            $slice = array_slice($target, $i * 100, 100);            
            dml($conn, "insert into email_targets (email_id, target_type, email_list) values (:email_id, :type, :target)",
            [ 'vals' => [
                'email_id' => $email_id,
                'type' => $type,
                'target' => join(';', $target)
            ]]
            
            ); 
        }
    }
}

function storeDraftEmail($conn, $email) {
    global $emailConfig;
    $email_id = dml($conn, "insert into emails (subject, body, from_email, reply_to) values (:subject, :body, :from_email, :reply_to)",
       [ 'vals' => [ 
            'subject' => isset($email['subject']) ? $email['subject'] : 'No subject', 
            'body' => isset($email['body']) ? $email['body'] : '',
            'from_email' => isset($email['from']) ? $email['from'] : $emailConfig['defaultFrom'],
            'reply_to' => isset($email['replyTo']) ? $email['replyTo'] : $emailConfig['defaultReplyTo']
       ]]
    );
    if(isset($email['to'])) {
        loadTargets($conn, $email_id, $email['to'], 'To'); 
    }
    if(isset($email['cc'])) {
        loadTargets($conn, $email_id, $email['cc'], 'CC'); 
    }
    if(isset($email['bcc'])) {
        loadTargets($conn, $email_id, $email['bcc'], 'BCC'); 
    }
}


function getEmail($conn, $id) {
    $email = getDataById($conn, 'emails', $id);
    $targets = sqlToJSON($conn, "select * from email_targets where email_id = :id", [ 'vals' => [ 'id' => $id]] );
    for($i=0; $i<sizeof($targets); $i++) {
        if(!isset($email[$targets[$i]['target_type']])) $email[$targets[$i]['target_type']] = '';
        $email[$targets[$i]['target_type']] .= $targets[$i]['email_list'];
    }
    return $email;
}

function serveEmail($conn, $id) {
    $email = getEmail($conn, $id);
    dml($conn, "update emails set status = 'SERVED' where id = :id", [ 'vals' => [ 'id' => $id ]]);
    json_data('email', $email);
}

function findDraftEmails($conn, $limit = 100) {
    $emails = sqlToJSON($conn, "select id from emails where status = 'DRAFT' order by date_added limit " . $limit, []);
    return $emails;
}

function processRequest($conn) {
    if($_GET['key'] = '218jassbdjay938y4hkals218jassbdjay938y4hkals') {
        if($_GET['what'] == 'EMAIL') {
            serveEmail($conn, $_GET['id']);
        } else if($_GET['what'] == 'drafts') {
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
            $data = findDraftEmails($conn, $limit);
            json_data('emails', $data);
        }
    } else {
        errorMSG    ("Missing authorization key", []);
    }
}

?>