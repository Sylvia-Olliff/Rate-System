<?php

	function getAccessorial($CODE) {
		$options2 = array('i5_naming' => DB2_I5_NAMING_ON);

		$execSQL = "SELECT (SELECT CCDESC FROM XL_RBNALT/BNAACCCP WHERE CCCODE = ACCESSORIAL.SRCODE) as DESCRIPTION, SRACCC as FEE, SRNOTE as NOTE FROM XL_RBNALT/BNAMISRP as ACCESSORIAL WHERE SRSCAC = '$CODE'";

		$db_connection2 = xl_db2_connect($options2);

		if (!$db_connection2)
		{
			die('Could not connect to database: ' . db2_conn_error());
		}

		$query = db2_exec($db_connection2, $execSQL, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db_connection2);
			die('<b>Error ' . db2_stmt_error() . ':' . db2_stmt_errormsg() . '</b>');
		}

		$result = "";
		
		while ($row = db2_fetch_assoc($query)) {
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]),ENT_QUOTES,'ISO-8859-1');
				$escapedField = xl_fieldEscape($key);

				if ($escapedField == "DESCRIPTION") {
					$result .= "<p>DESC: $row[$key] ";
				} elseif ($escapedField == "FEE") {
					$result .= "FEE: <span class='money'>$row[$key] </span>";
				} elseif ($escapedField == "NOTE") {
					$result .= " NOTE: $row[$key] </p>";
				}
				
			}
		}

		db2_close($db_connection2);

		return $result;
	} 
	


?>