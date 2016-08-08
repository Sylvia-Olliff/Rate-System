<?php
	function getCustomerInfo($SCAC) {
		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$execSQL = sprintf("SELECT RCCONT, RCPHONE FROM XL_RBNALT/BNACONTL1 WHERE RCSCAC = '%s' AND RCPHL = 1", xl_encode($SCAC, 'db2_search'));

		$db2conn = xl_db2_connect($options);

		$query = db2_exec($db2conn, $execSQL, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db2conn);
			die('<b>Error ' . db2_stmt_error() . ':' . db2_stmt_errormsg() . '</b>');
		}

		$row = db2_fetch_array($query); 

		if($row) {
			return $row[0] . " " . $row[1];
		} else {
			return "FALSE";
		}


	}
	
?>