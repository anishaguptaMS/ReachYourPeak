<?php


// echo json_encode($conf);


$baseUrl = getenv('baseUrl') ? getenv('baseUrl') : "http://localhost/ryp/";

$conf = parse_ini_file('c:\Bitnami\wampstack-5.6.30-1\apache2\config.ini');


function loadPage($page) {
	echo "<script> $(document).ready(function() {loadPage('" . $page . "'); }); </script>";
}


function json_data($message, $data) {
	echo json_encode([ 'result' => 'OK', 'message' => $message, 'data' => $data]);
}

function jsonp_data($message, $data,  $callback = 'callback') {
	echo "$callback(".json_encode([ 'result' => 'OK', 'message' => $message, 'data' => $data]).")";
}


function addDataTableRowID($res, $pk) {
	// after pivot!
	//echo $pk;
	$colIndex = array_search($pk, array_column($res['columns'], 'title'));
	//echo $colIndex;
	if(isset($colIndex)) {
		array_push($res['columns'], [ "title" => 'DT_RowId']);
		$newData = [];
		foreach($res['data'] as $r) {
			array_push($r, "row_" . $r[$colIndex]);
			array_push($newData, $r);
		}
		$res['data'] = $newData;
	}	
	return $res;
}

function rawNumber($s) {
	return preg_replace('/[\s\$\,]/i', '', $s);
}

function debugSQL($sql, $options) {
    $s = $sql;
    foreach($options['vals'] as $p => $v) {
        $s = str_replace(':'.$p, "'".$v."'", $s);
    }
    return $s;
}


function dataTablesResultSet($res, $options) {
	$o = [];
	$idSrc = 'ID';
	if(isset($options) && isset($options['idSrc'])) {
		$idSrc = $options['idSrc'];
	}
	$dataName = 'data';
	if(isset($options) && isset($options['dataName'])) {
		$dataName = $options['dataName'];
	}
	$i=0;
	$columns = [];
	foreach($res as $row) {
		if($i == 0) {
			foreach($row as $c => $v) {
				array_push($columns, [ "title" => $c, "data" => $c ]);
			}
		}
		array_push($o, array_merge([ 'DT_RowId' =>  'row_' . $row[$idSrc] ], $row));
		$i++;
	}
	return ['data' => $o, 'columns' => $columns ];
}


?>