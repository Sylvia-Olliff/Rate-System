<?php
	require 'errorLogger.php';
	require '/esdi/websmart/v10.6/include/xl_functions001.php';
	require 'settings.php';

	function writeFuelData($entries, $MODE, $CODE) {
		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$insertSQL = "INSERT INTO JOELIB/BNASFUELP (SMODE, SNAME, SPBAMT, SPEAMT, SPFUEL$, SPPCT) VALUES (?, ?, ?, ?, ?, ?) with nc";
		$preSQL = "DELETE FROM JOELIB/BNASFUELP where SMODE = '$MODE' and SNAME = '$CODE'";

		$db2conn = xl_db2_connect($options);
		if(!$db2conn) //If connection failed die and report the error
		{
			$errors = $errors . " Could not connect to database: " . db2_conn_error() . "\n";
			writeErrors($errors);
		}

		db2_exec($db2conn, $preSQL);

		foreach ($entries as $key => $value) {
			$stmt = db2_prepare($db2conn, $insertSQL); 

			$data = $value->getFields();

			$result = db2_execute($stmt, $data);
			if(!$result) //If the write fails, print the Error code and Message
       		{
       			$errors = $errors . " Data Error: " . db2_stmt_error() . " msg: " . db2_stmt_errormsg() . "data: " . print_r($data, true) . "\n";
   				db2_close($db2conn);
       			writeErrors($errors);
       			return "FALSE";
       		}
		}

		db2_close($db2conn);

		return "TRUE";
	}

?>