<?php

include_once('db.php');

function openMySql() {
    $dns = getenv('MYSQL_DSN') ? getenv('MYSQL_DSN') : 'mysql:host=127.0.0.1;dbname=ryp;charset=utf8';
    $user = getenv('MYSQL_USER') ? getenv('MYSQL_USER') : 'root';
    $pwd = getenv('MYSQL_PASSWORD') ? getenv('MYSQL_PASSWORD') : 'Password';  // Please change the password
	$db = new PDO($dns, $user, $pwd);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	return $db;
}

function sqlToJSON($connection, $sql, $options = NULL) {
    $stmt = $connection->prepare($sql);
    // echo $sql;
	if(isset($options) && isset($options['vals'])) {
		foreach($options['vals'] as $p => $v) {
			if(is_string($options['vals'][$p])) {
				$stmt->bindValue(':' . $p, $options['vals'][$p], PDO::PARAM_STR);
			} else if(is_integer($options['vals'][$p])){
				$stmt->bindValue(':' . $p, $options['vals'][$p], PDO::PARAM_INT);
			} else if(is_numeric($options['vals'][$p])) {
				$stmt->bindValue(':' . $p, $options['vals'][$p], PDO::PARAM_STR);
			}
		}
	}
	$stmt->execute();
	$rec = $stmt->fetchAll(PDO::PARAM_STR);
	return $rec;
}

function getRSET($connection, $sql, $options = NULL) {
    return sqlToJSON($connection, $sql, $options);
}

function getFirstRecord($connection, $sql, $options) {
    $res = sqlToJSON($connection, $sql, $options);
    if($res && sizeof($res) > 0) return $res[0];
    return null;
}

function getRecByID($connection, $tableName, $id) {
    $sql = "select * from ". $tableName . " where id = :id";
    return getFirstRecord($connection, $sql, [ 'vals' => [ 'id' => $id]]);
}

function dml($connection, $sql, $options) {
    $stmt = $connection->prepare($sql);
	if(isset($options) && isset($options['vals'])) {
		foreach($options['vals'] as $p => $v) {
			if(is_string($options['vals'][$p])) {
				$stmt->bindValue(':' . $p, $options['vals'][$p], PDO::PARAM_STR);
			} else if(is_integer($options['vals'][$p])){
				$stmt->bindValue(':' . $p, $options['vals'][$p], PDO::PARAM_INT);
			} else if(is_numeric($options['vals'][$p])) {
				$stmt->bindValue(':' . $p, $options['vals'][$p], PDO::PARAM_STR);
			}
		}
	}
	$stmt->execute();
    if(strpos($sql, 'insert') >= 0) { 
        
        return $connection->lastInsertId();
    }    
}



?>

