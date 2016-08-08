<?php
	require 'errorLogger.php';
	require 'fuelWriter.php';
	require 'convertArray.php';
	require '/esdi/websmart/v10.6/include/xl_functions001.php';

	$options = array('i5_naming' => DB2_I5_NAMING_ON);


	if (isset($_POST["formData"])) {
		$procData = convert($_POST["formData"]);
		$orgSCAC = $procData["orgTable"];
		$orgMode = $procData["orgMode"];
		$desSCAC = $procData["desTable"];
		$desMode = $procData["desMode"];

		$execSQL = "SELECT COUNT(*) FROM JOELIB/BNASFUELP WHERE SNAME = '$desSCAC' and SMODE = '$desMode'";

		$db2conn = xl_db2_connect($options); 

		$result = db2_exec($db2conn, $execSQL); 
		
		$row = db2_fetch_array($result); 

		if ($row[0] > 0) {
			echo "EXISTS";
		} else {
			$execSQL = "INSERT INTO JOELIB/BNASFUELP (SMODE, SNAME, SPBAMT, SPEAMT, SPFUEL$, SPPCT)
						SELECT '$desMode', '$desSCAC', SPBAMT, SPEAMT, SPFUEL$, SPPCT FROM JOELIB/BNASFUELP 
						WHERE SNAME = '$orgSCAC' and SMODE = '$orgMode'";

			$result = db2_exec($db2conn, $execSQL); 

			if(!$result) //If the write fails, print the Error code and Message
       		{
       			$errors = $errors . " Data Error: " . db2_stmt_error() . " msg: " . db2_stmt_errormsg() . "data: " . print_r($desMode, true) .
       					  " " . print_r($desSCAC, true) . "\n";
   				db2_close($db2conn);
       			writeErrors($errors);
       			echo "FALSE";
       		} else {
       			db2_close($db2conn);
       			echo "TRUE";
       		}			
		}

	}

	die();

?>