<?php

include_once('db.php');


function openANCPROD() {
	$conn = oci_connect("HRDW_TST", "wyh4g78fk", 'ANCPRD01');
	return $conn;
}

function getOracleColumns($conn, $table_name) {
	$stid = oci_parse($conn, "SELECT * FROM ".$table_name. " where rownum < 2");
	oci_execute($stid, OCI_DESCRIBE_ONLY);
	$ncols = oci_num_fields($stid);
	$res = [];
	for ($i = 1; $i <= $ncols; $i++) {
		array_push($res, 
		[
			'name' => oci_field_name($stid, $i),
			'type' => oci_field_type($stid, $i),
			'size' => oci_field_size($stid, $i),			
			'prec' => oci_field_precision($stid, $i)
		]);
	}
	return $res;
}


function sqlToJSON($connection, $sql, $options) {
	$stid = oci_parse($connection, $sql);
	if(isset($options) && isset($options['vals'])) {
		foreach($options['vals'] as $p => $v) {
            $param = ':'. $p;
            $val = $v;
			oci_bind_by_name($stid, $param, $options['vals'][$p]); 
		}
	} 
	oci_execute($stid);
	oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);	
	oci_free_statement($stid);
	return $res;
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
	$stid = oci_parse($connection, $sql);
	if(isset($options) && isset($options['vals'])) {
		foreach($options['vals'] as $p => $v) {
			oci_bind_by_name ($stid, 
				':'. $p, 
				$options['vals'][$p], 
				strlen($options['vals'][$p]), 
				(gettype($v) == 'string' ? SQLT_CHR : SQLT_INT)
			);
		}
	} 
	oci_execute($stid);
	oci_free_statement($stid);
}

function getInStr($ar) {
	$s = '';
	foreach($ar as $v) {
		$s .= "'". $v . "',";
	}
	if(substr($s, strlen($s) -1, 1) == ',') $s = substr($s, 0, strlen($s) - 1);
	return $s;
}

function commit($conn) {
	oci_commit($conn);
}

function closeConnection($conn) {
	oci_close($conn);
}

function getRSET($conn, $options) {
	$res = sqlToJSON($conn, $options['sql'], $options);
	if(isset($options['dt']) && $options['dt'] == true) $res = dataTablesResultSet($res, $options);
	return $res;
}

function buildUpdate2($conn, $table_name, $cols) {
	$o = [
		"cols" => getOracleColumns($conn, $table_name)
	];
	
	$sel = "select 'x' from ". $table_name . " where ". $cols[0] . " = :" . $cols[0];
	$ins = "insert into ". $table_name . " (";
	
	$ins .= ') values (';
	$ins .= ')';
	return o;
} 

function buildInsert($table, $data) {
	$s = "insert into $table (ID, ";
	$s2 = " values((select max(id) + 1 from $table), ";
	foreach($data as $p => $v) {
		$s .= $p . ",";
		$s2 .= ":" . $p . ",";
	}
	$s = substr($s, 0, -1). ') ';
	$s2 = substr($s2, 0, -1). ')';
	return $s . $s2;
}

function buildInsertStatementFromModel($table, $model) {
	$s = "insert into $table (";
	$s2 = " values(";
	foreach($model as $p => $v) {
		$s .= $p . ",";
		$s2 .= $v .",";
	}
	$s = substr($s, 0, -1). ') ';
	$s2 = substr($s2, 0, -1). ')';
	return $s . $s2;
}

function getExcelColName($n) {
	// start with 1
	$s = '';
	if($n<27) {
		$s .= chr(64+$n);
	} else {
		$s .= chr(64 + floor(($n - 1) / 26)) . chr(($n - 1) % 26 + 65);
	}
	return $s;
}


function buildUpdate($table, $data) {
	$s = "update $table set ";
	$s2 = " where id = :id ";
	foreach($data as $p => $v) {
		if(strtoupper($p) != 'ID') {
			$s .= $p. " = :" . $p  . ",";
		}
	}
	$s = substr($s, 0, -1). ' ';
	return $s . $s2;
}


function REST($conf) {
    $method = $_POST['action'];
	$table = $conf['table'];
	$connection = isset($conf['connection']) ? $conf['connection'] : openSC();
	$returnData = [];
	switch ($method) {
		case 'GET': 
			$sql = "select * from $table where id = :id";
			break;
		case 'create':
			$ins =  buildInsert($table, $_POST['data'][0]);
			dml($connection, $ins, ['vals' => $_POST['data'][0]]);
			$returnData = $_POST['data'];
			break;
		case 'remove' :
			$sql = "delete from $table where id= :id";
			foreach($_POST['data'] as $p => $v ) {
				$id = $_POST['data'][$p]['ID'];
				dml($connection, $sql, ['vals' => [ 'id' => $id ]]);
			}
			break;
		case 'edit' : 
			foreach($_POST['data'] as $p => $v ) {
				$row = $_POST['data'][$p];	
				$sql = buildUpdate($table, $_POST['data'][$p]);
				$row['id'] = str_replace('row_', '', $p);
				dml($connection, $sql, ['vals' => $row]);
			}
			$returnData = $_POST['data'];
			break;
	}
	$input = json_decode(file_get_contents('php://input'),true);
	closeConnection($connection);
	json_data("Data updated", $returnData);
}



?>