<?php
	require('/esdi/websmart/v8.8/include/xl_functions001.php');

	$options = array('i5_naming' => DB2_I5_NAMING_ON);

	if(isset($_POST["checkData"])) {
		$SCAC = $_POST["checkData"];

		$execSQL = sprintf("SELECT RPCNAME FROM XL_RBNALT/BNAPROFP WHERE RPSCAC = '%s'", xl_encode($SCAC, 'db2_search'));

		$db2conn = xl_db2_connect($options);

		$result = db2_exec($db2conn, $execSQL);
		$row = db2_fetch_array($result);

		if(isset($row[0])) {
			echo "<b>CUSTOMER NAME: " . $row[0] . "</b><br /><br />";
		} else {
			$execSQL = sprintf("SELECT NAMECS FROM QS36F/BNACUSMP WHERE CUS#CS = '%s'", xl_encode($SCAC, 'db2_search'));			

			$result = db2_exec($db2conn, $execSQL); // submit the query
		
			$row = db2_fetch_array($result); 

			if($row[0] > 0) {
				echo "<b>CUSTOMER NAME: " . $row[0] . "</b><br /><br />";
			} else {
				echo "<b>CUSTOMER NAME: No Information Available </b><br /><br />";
			}
			
		}

	}

?>