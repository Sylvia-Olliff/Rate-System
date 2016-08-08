<?php
	function getMiles($originCity, $originState, $destinCity, $destinState) {
		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$execSQL = "SELECT BPMILE FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '" . $originCity . "' AND BOST = '" . $originState . "' AND
					BDCITY = '" . $destinCity . "' AND BDST = '" . $destinState . "'";

		$db2conn = xl_db2_connect($options); 

		$query = db2_exec($db2conn, $execSQL, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db2conn);
			die('<b>Error ' . db2_stmt_error() . ':' . db2_stmt_errormsg() . '</b>');
		}

		$row = db2_fetch_array($query); 

		if($row) {
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]),ENT_QUOTES,'ISO-8859-1');
				return $row[$key];
			}	
		} else {
			return "FALSE";
		}
	}


?>