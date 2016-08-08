<?php
	require '/esdi/websmart/v8.8/include/xl_functions001.php';

	$options = array('i5_naming' => DB2_I5_NAMING_ON);

	//Verify that the correct information has been sent to this program.
	if(isset($_POST["checkData"])) {
		$SCAC = $_POST["checkData"]; //Store the field that is needed, do this to reduce reads into an Array (memory management)

		//Use String Print Formatted to convert a given string to a specific format for use in a SQL statement. This method 
		//is most useful for statements that only require 1-3 fields from the program. 
		$execSQL = sprintf("SELECT COUNT(*) FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = '%s'", xl_encode($SCAC, 'db2_search'));

		//
		$db2conn = xl_db2_connect($options); // open a connection to the database

		$result = db2_exec($db2conn, $execSQL); // submit the query
		// the DB returns an array as the results, even if the result is only one row, one element. So here we retrieve the first "row" of data 
		// from the DB results
		$row = db2_fetch_array($result); 

		//check the first element of the first row of the results from the DB this corrisponds with the "COUNT(*)" in the query.
		//If the value is 1 that means that a Carrier with that SCAC code was in the file, otherwise it wasn't.
		if($row[0] > 0) {
			echo "TRUE";
		} else {
			$execSQL = sprintf("SELECT COUNT(*) FROM QS36F/BNACUSMP WHERE CUS#CS = '%s'", xl_encode($SCAC, 'db2_search'));			

			$result = db2_exec($db2conn, $execSQL); // submit the query
		
			$row = db2_fetch_array($result); 

			if($row[0] > 0) {
				echo "TRUE";
			} else {
				$execSQL = sprintf("SELECT COUNT(*) FROM XL_RBNALT/BNAPROFP WHERE RPSCAC = '%s'", xl_encode($SCAC, 'db2_search'));			

				$result = db2_exec($db2conn, $execSQL); // submit the query
		
				$row = db2_fetch_array($result); 

				if($row[0] > 0) {
					echo "TRUE";
				} else {
					echo "FALSE";
				}
			}
		}

		db2_close($db2conn); //Close the connection (VERY IMPORTANT THAT YOU REMEMBER TO DO THIS!)

	}

?>