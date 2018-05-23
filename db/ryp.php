<?php

include_once("db.php");

// include_once("oracledb.php");
include_once("mySQLdb.php");

include_once('emails.php');

/*
$dbTables = [
    'users' => 'z_ryp_users',
    'sessions' => 'z_ryp_session',
    'cycles' => 'z_ryp_cycle',
    'locations' => 'z_ryp_locations',
    'roles' => 'z_ryp_roles',
    'steps' => 'z_ryp_steps',
    'teams' => 'z_ryp_team',
    'roleMembers' => 'z_ryp_role_members',
    'teamCycles' => 'z_ryp_team_cycles'
]; */

$dbTables = [
    'users' => 'users',
    'sessions' => 'session',
    'cycles' => 'cycle',
    'locations' => 'locations',
    'roles' => 'roles',
    'steps' => 'steps',
    'teams' => 'team',
    'roleMembers' => 'role_members',
    'teamCycles' => 'team_cycles',
    'mailList' => 'mail_list',
    'emails' => 'emails',
    'emailTargets' => 'email_targets'
];


$df = '%m/%d/%Y';

$dpdf = '%Y-%m-%d';

function getFullTableSQL($conn, $tableName, $order = NULL) {
    global $dbTables;            
    $sql = "select * from " . $dbTables[$tableName];
    if(isset($order)) {
        $sql .= ' order by ' . $order;
    }
    return $sql;
}

function getFullTableData($conn, $tableName, $order = NULL) {    
    return sqlToJSON($conn, getFullTableSQL($conn, $tableName, $order), []);
}

function getDataById($conn, $tableName, $id) {
    global $dbTables;        
    $sql = "select * from ". $dbTables[$tableName]. " where id = :id";    
//    echo $sql;
    return getFirstRecord($conn, $sql, [ 'vals' => [ 'id' => $id ]]);
}

function jsonById($conn, $tableName, $id) {
    $rec = getDataById($conn, $tableName, $id);
    if($rec) {
        json_data("Record retrieved for $tableName - $id", $rec);
    } else {
        errorMSG("Record with $id not found");
    }
}

function jsonFullTable($conn, $tableName, $order = NULL) {
    json_data('Data retrieved for ' . $tableName, getFullTableData($conn, $tableName, $order));
}

function jsonData($conn, $message, $sql, $errorMSG = 'Error retrieving data', $options = NULL) {
    $res = sqlToJSON($conn, $sql, $options);
    if($res) {
        json_data($message, $res);
    } else {
        errorMSG($errorMSG, []);
    }
}

function delById($conn, $table, $id) {
    global $dbTables;
    dml($conn, "delete from ". $dbTables[$table]. " where id = :id", [ 'vals' => ['id' => $id]]);
}

function tplSubst($text, $ar) {
    $nt = $text;
    foreach($ar as $p => $v) {
        $nt = str_replace('{'.$p.'}',$v, $nt);
    }
    return $nt;
}

function sendPasswordResetLink($conn, $email, $name) {
    global $dbTables;    
    $linkVal = md5(strval(rand()));
    global $baseUrl;
    $link = $baseUrl . "resetPassword.html?link=" . $linkVal;
    dml($conn, "update " . $dbTables['users'] . " set pwd_reset_link = :link, reset_link_date = now() where lower(email) = lower(:email)", [ 'vals' => ['link' => $linkVal, 'email' => $email]]);
    $tpl = file_get_contents("register_email.txt");
    
    $text = tplSubst($tpl, ['name' => $name, 'link' => $link]);
    storeDraftEmail($conn, [
        'subject' => "Reach your peak - Please verify your email",
        'body' => $text,
        'to' => $email ]
    );
}

function errorMSG($msg, $data) {
	$r = [ "result" => "error", "error" => $msg ];
	if(isset($data)) {
		array_merge($r, $data);
	}
	echo json_encode($r);
}


function getUserFromEmail($conn, $email) {
    //echo '---- ' . $email . ' ---- ';
    global $dbTables;        
    $res = sqlToJSON($conn, "select * from " . $dbTables['users'] . "  where lower(email) = lower(:email)", [ "vals" => [ "email" => $email]]);
    if(isset($res) && sizeof($res) > 0) return $res[0];
    return null;
}

function createSession($conn, $id, $sessionType) { 
    global $dbTables;
    $sessionKey = md5(strval(rand()));
    dml($conn, "insert into " . $dbTables['sessions'] . " (user_id, browser_string, session_cookie, session_start, session_type, session_ip) values 
        (:id, :browser, :sk, now(), :st, :sip)", 
     [ 'vals' => ['id' => $id, 'sk' => $sessionKey, 'browser' => $_SERVER['HTTP_USER_AGENT'], 'st' => $sessionType, 'sip' => $_SERVER['REMOTE_ADDR']]]);
    return $sessionKey;
}

function checkSession($conn, $email, $sessionKey, $userid) {
    //echo '---- ' . $email . ' ---- ';
    global $dbTables;    
    $sql = "select * from " .  $dbTables['users']. "   u
        join ". $dbTables['sessions'] ."  s on u.id = s.user_id
        where lower(email) = lower(:email) and s.session_cookie = :sessionKey and u.id = :userid";
    $res = getFirstRecord($conn, $sql, [ "vals" => [ "email" => $email, 'sessionKey' => $sessionKey, 'userid' => $userid]]);
    if($res) {
        return $res;
    } else {
        return null;
    }
}

function hasValidSession($conn) {
    // all 3 have to match:  id, email and sessionKey.   
	if(isset($_COOKIE) && isset($_COOKIE['sessionKey']) && isset($_COOKIE['email']) && isset($_COOKIE['userid'])) {
		return checkSession($conn, $_COOKIE['email'], $_COOKIE['sessionKey'], $_COOKIE['userid']);
	} 
	return null;
}

function destroyAllSessions($conn, $userid) {
    global $dbTables;    
    dml($conn, "delete from " . $dbTables['sessions']. " where userid = :userid", ['vals' => ['userid' => $userid]]);    
}

function exitNoSession($conn) {
    if(!hasValidSession($conn)) {
        errorMSG('No valid session', []);
        die();
    }
}

function logout($conn) {
    global $dbTables;    
    $email = $_COOKIE['email'];
    $sk = $_COOKIE['sessionKey'];
    dml($conn, "update " . $dbUsers['users'] . " set session_key = ''  where email = :email and session_key = :sk", 
    [ 'vals' => ['email' => $email, 'sk' => $sk]]);
    json_data('You have logged out', ['data' => []]);
}

function getRoles($conn) {
    global $dbTables;    
    return sqlToJSON($conn, "select * from ". $dbTables['roles'] . " order by name", []);
}

function getRoleID($conn, $name) {
    $roles = getRoles($conn);
    for($i=0; $i<sizeof($roles); $i++) {
        if($roles[$i]['name'] == $name) {
            return $roles[$i]['ID'];
        }
    }
    return null;
}

function getTeamCycle($conn, $team_id) {
    global $dbTables;         
    return getFirstRecord($conn, "SELECT * FROM ". $dbTables['teamCycles'] . "  WHERE team_id = :id and active_flag in ('A', 'E')" , [ 'vals' => [ 'id' => $team_id ]]);
}

function removeAdminRole($conn, $userid, $roleId) {
    global $dbTables;    
    if(isset($roleId)){
        
        dml($conn, "delete from " . $dbTables['roleMembers'] . " where user_id = :userid and role_id = :roleid", 
            [ 'vals' => [ 'userid' => $userid, 'roleid' => $roleId]]);  
        
    }
}

function addAdminRole($conn, $userid) {
    global $dbTables;
    $roleId = getRoleID($conn, 'Admin');
     
    removeAdminRole($conn, $userid, $roleId);
    dml($conn, 'insert into  '. $dbTables['roleMembers'] . ' (role_id, user_id) values (:roleid, :userid)', 
            [ 'vals' => [ 'userid' => $userid, 'roleid' => $roleId]]);
}

function getCycles($conn, $activeFlag) {
    global $dbTables;
    return sqlToJSON($conn, "select * from " . $dbTables['cycles'] . "  where active_flag = :f  order by name", [ 'vals' => ['f' => $activeFlag]]);   
}

function getTeamByName($conn, $name) {
    global $dbTables;
    return getFirstRecord($conn, "select * from " . $dbTables['teams'] . " where lower(name) = lower(:name)", [ 'vals' => ['name' => $name]]);
}

function getAllActiveTeams($conn) {
    global $dbTables;
    $sql = "select * from ". $dbTables['teams'];
    return sqlToJSON($conn, $sql, []);
}

function assignRoleToTeam($conn, $userid, $cycle_team_id, $role_id) {
    global $dbTables;
    $captainRoleId = getRoleId($conn, 'Captain');
    if($role_id == $captainRoleId) {
        $memberRoleId = getRoleId($conn, 'Member');
        // downgrade any captain to member
        dml($conn, "update ". $dbTables['roleMembers'] . " set role_id = :m_role_id where role_id = :c_role_id and team_id = :team_id",
            [ 'vals' => [ 'm_role_id' => $memberRoleId, 'c_role_id' => $captainRoleId, 'team_id' => $cycle_team_id]]);
    }
    // remove all roles for the user!
    dml($conn, "delete from " . $dbTables['roleMembers'] . " where team_id = :team_id and user_id = :user_id",
        [ "vals" => [ 'user_id' => intval($userid), 'team_id' => intval($cycle_team_id) ]]
    );    
    
    dml($conn, "insert into " . $dbTables['roleMembers'] . " (role_id, user_id, team_id) values (:role_id, :user_id, :team_id)",
        [ "vals" => [ 'role_id' => intval($role_id), 'user_id' => intval($userid), 'team_id' => intval($cycle_team_id) ]]
    );    
}

function assignCaptain($conn, $userid, $cycle_team_id) {
    $role_id = getRoleID($conn, 'Captain');
    assignRoleToTeam($conn, $userid, $cycle_team_id, $role_id);
}
  
function startTeam($conn, $name, $cycleId, $userid) {
    global $dbTables;
    $team = getTeamByName($conn, $name);
    if($team) {
        errorMSG("This team already exists.  Please pick another name.", []);
    } else {
        dml($conn, "insert into " . $dbTables['teams'] . " (name, active_flag) values (:name, 'E')", 
            [ 'vals' => [ 'name' => $name]]);
        $team = getTeamByName($conn, $name);
        $id = $team['ID'];
        dml($conn, "insert into " . $dbTables['cycles'] . " (team_id, cycle_id, active_flag) values (:team, :cycle, 'E')", 
            [ 'vals' => [ 'team' => $id, 'cycle' => $cycleId]]);
            
        $cid = getFirstRecord($conn, "select * from " . $dbTables['teamCycles'] . " where cycle_id = :cycle and team_id = :team", 
            [ 'vals' => [ 'team' => $id, 'cycle' => $cycleId]]);
        assignCaptain($conn, $userid, $cid['ID']);
    }
}
  
function getTeamMembership($conn, $user_id) {
    // only get active or enroll teams.
    global $dbTables;
    $sql = "select t.id as team_id, u.name, t.name as team_name, r.name as role_name, c.name as cycle_name, c.start_date, c.end_date, tc.id as tcm_id, l.name as location from  " 
     . $dbTables['users'] . " u
join " . $dbTables['roleMembers'] . " m on u.id = m.user_id
join " . $dbTables['teamCycles'] . " tc on tc.id = m.team_id
join " . $dbTables['teams'] . " t on t.id = tc.team_id
join " . $dbTables['locations'] . " l on l.id = t.location_id
join " . $dbTables['roles'] . " r on m.role_id = r.id
join " . $dbTables['cycles'] . " c on tc.cycle_id = c.id
where t.active_flag in ('E', 'A') and u.id = :id";
    $res = getFirstRecord($conn, $sql, [ 'vals' => [ 'id' => $user_id]]);
    return $res;
}

function isAdmin($conn, $userid) {
    global $dbTables;    
    $sql = "select u.name,  r.name as role_name from " . $dbTables['users'] . " u 
join " . $dbTables['roleMembers'] . "  m on u.id = m.user_id
join " . $dbTables['roles'] . " r on m.role_id = r.id
where r.name = 'Admin' and u.id = :id";
    $res = getFirstRecord($conn, $sql, [ 'vals' => [ 'id' => $userid]]);
    return $res;
}

function exitNoAdmin($conn, $userid) {
    if(! isAdmin($conn, $userid)) {
        errorMSG("No admin privileges");
        die();
    }
}

function getTeamCycleId($conn, $team_id) {
        
}

$conn;
$userid;

function initDB() {
    global $conn;
    $conn =  openMySql();
}

function initSession() {
    global $conn;
    global $userid;
    initDB();
    exitNoSession($conn);
    $userid = $_COOKIE['userid'];        
}

function initAdmin() {
    global $conn;  
    global $userid;
    initSession();
    exitNoAdmin($conn, $userid);
}

function requiresPost() {
    if(! isset($_POST)) {
        errorMSG('Data required');
        die();
    }
}

function getAllTeamsSQL($where = '', $order = '') {
    global $dbTables;
    $sql = "SELECT t.name as team_name, l.name as location, r.name as role_name, u.name, u.email, c.name as cycle, c.start_date, c.end_date FROM " . $dbTables['teams'] .
        " t join ". $dbTables['locations']. " l on l.id = t.location_id 
          join " . $dbTables['teamCycles'] . " tc on tc.team_id = t.id and tc.active_flag in ('A', 'E') 
          join " . $dbTables['cycles'] . " c on c.id = tc.cycle_id
          left join ". $dbTables['roleMembers'] . "  m on m.team_id = tc.ID
          left join ". $dbTables['roles'] . "  r on m.role_id = r.id
          left join ". $dbTables['users'] . " u on m.user_id = u.ID " . $where . " " . $order;
    
    return $sql;
}

function getAllTeamsDetails($conn) {
    $sql = getAllTeamsSQL('',  ' order by c.name, l.name, t.name, u.name');
    $data = sqlToJSON($conn,  $sql);
    json_data('All team data', $data ? $data : []);
}

function getAllTeamsSummary($conn) {
    $sql = getAllTeamsSQL(" where r.name = 'Captain'");
    $data = sqlToJSON($conn,  $sql);
    json_data('All team data', $data ? $data : []);
}

?>