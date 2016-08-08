<?php
	require 'errorLogger.php';
	require '/esdi/websmart/v10.6/include/xl_functions001.php';

	function getPrecs() {
		$PRECS;

		$selString = "SELECT PRTYPE, PRDESC FROM JOELIB/BNAPRECP";

		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$db_connection = xl_db2_connect($options);

		if (!$db_connection)
		{
			$errors = $errors . " Could not connect to the database for PRECS. " . db2_conn_error() . "\n";
			writeErrors();
		}

		$query = db2_exec($db_connection, $selString, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db_connection);
			$errors = $errors . "Error ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "\n";
			writeErrors();
		}

		while ($row = db2_fetch_assoc($query)) {
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]),ENT_QUOTES,'ISO-8859-1');
				$escapedField = xl_fieldEscape($key);

				if ($escapedField == "PRTYPE") {
					$tempType = $row[$key];
				} elseif ($escapedField == "PRDESC") {
					$PRECS[(string) trim($row[$key])] = $tempType;
				}
			}
		}

		return $PRECS;
	}
	
?>