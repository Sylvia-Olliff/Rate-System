<?php
	require 'errorLogger.php';
	require '/esdi/websmart/v10.6/include/xl_functions001.php';
	require 'settings.php';

	/*
	 * Writes the prepared and validated data to the file, keeping the connection open only as long as necessary 
	 * and inserting only into the relevant fields per the Precedence 
	 */
	function writeData($PREC, $PRECS, $entries, $MODE, $CODE, $EFFDATE, $ENDDATE, $ORIGIN, $DESTIN) {
		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		if (DEBUG_INPUT || DEBUG_ALL) {
			writeErrors("Precedence being written: " . $PREC . "\n");
		}

		//Build the Insert SQL String based on which precedence has been selected.
		switch ($PREC) {
			case $PRECS["ZIP(6) TO ZIP(6)"]: //Zip6 to Zip6
				//Initialize SQL Statement string using Array Field References (the ?s)
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOFZIP, RAOTZIP, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["CITY,ST TO CITY,ST"]: //City, State to City, State
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOCITY, RAOST, RADCITY, RADST, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
			    
				break;

			case $PRECS["ZIP(6) TO ZIP(3)"]: //Zip5 to Zip3
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOFZIP, RAOTZIP, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ZIP(3) TO ZIP(6)"]: //Zip3 to Zip5
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOFZIP, RAOTZIP, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ZIP(3) TO CITY,ST"]: //Zip3 to City, State
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOFZIP, RAOTZIP, RADCITY, RADST, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["CITY,ST TO ZIP(3)"]: //City, State to Zip3
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOCITY, RAOST, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["CITY,ST TO ST"]: //City, State to Zip3
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOCITY, RAOST, RADST, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ST TO ZIP(6)"]: //State to Zip5
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOST, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ST TO CITY,ST"]: //City, State to Zip3
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOST, RADCITY, RADST, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ST TO ZIP(3)"]: //State to Zip3
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOST, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ZIP(3) TO ST"]: //Zip3 to State
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOFZIP, RAOTZIP, RADST, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ST TO ST"]: //State to State
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOST, RADST, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, RAMCDE,
														   RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?,
														   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;

			case $PRECS["ST,ZIP(3) TO ST,ZIP(3)"]:
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOST, RAOFZIP, RAOTZIP, RADST, RADFZIP, RADTZIP, RARPM, RAPTOP, RAMC, RAFINCL, RANOTE, 				  							   RAMCDE, RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, RADCT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 										   ?, ?, ?, ?, ?, ?, ?, ?) with nc";

				break;
			
			case $PRECS["MILEAGE"]:
				$insertSQL = "INSERT INTO JOELIB/BNARATEP (RAOST, RAOFZIP, RAOTZIP, RADST, RADFZIP, RADTZIP, RARPM, RAPTOP, RAFINCL, 
														   RAFMILE, RATMILE, RANOTE, RAMCDE, RATYPE, RACODE, RAFDTE, RAEDTE, RAOCT, 
														   RADCT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) with nc";
				break;			
			
			default:
				$errors = $errors . " Error Generating Insert Statement...\n";
				writeErrors();
				break;
		}

		if (DEBUG_INPUT || DEBUG_ALL) {
			writeErrors("INSERT STATEMENT: " . $insertSQL . "\n");
		}

		//Establish and open the connection to the DB
		$db2conn = xl_db2_connect($options);
		if(!$db2conn) //If connection failed die and report the error
		{
			$errors = $errors . " Could not connect to database: " . db2_conn_error() . "\n";
			writeErrors($errors);
		}
		
		foreach ($entries as $key => $value) { //Read through each Entry (record) Object in $entries
			$stmt = db2_prepare($db2conn, $insertSQL); //Prepare the SQL statement setting the SQL Cursor

			$dataPreppedForWriting = $value->getFields(); //Grab all of the set fields from the Entry

			$iter = count($dataPreppedForWriting); //Capture the length of the Array so that this works for ALL entries

			//Add on all of the header fields, Remember Arrays start at element 0 so don't iterate the first header field
			$dataPreppedForWriting[$iter] = $MODE;
			$iter++;
			$dataPreppedForWriting[$iter] = $PREC;
			$iter++;
			$dataPreppedForWriting[$iter] = $CODE;
			$iter++;
			$dataPreppedForWriting[$iter] = $EFFDATE;
			$iter++;
			$dataPreppedForWriting[$iter] = $ENDDATE;
			$iter++;
			$dataPreppedForWriting[$iter] = $ORIGIN;
			$iter++;
			$dataPreppedForWriting[$iter] = $DESTIN;

			if (DEBUG_INPUT || DEBUG_ALL) {
				writeErrors("Data being written: " . print_r($dataPreppedForWriting, true) . "\n");
			}

			//Write to the DB
			$result = db2_execute($stmt, $dataPreppedForWriting);
			if(!$result) //If the write fails, print the Error code and Message
       		{
       			$errors = $errors . " Data Error: " . db2_stmt_error() . " msg: " . db2_stmt_errormsg() . "data: " . print_r($dataPreppedForWriting, true) . "\n";
   				db2_close($db2conn);
       			writeErrors($errors);
       			return "FALSE";
       		}
       	}

             	

		db2_close($db2conn); //Close the connection

		return "TRUE";
	}


?>