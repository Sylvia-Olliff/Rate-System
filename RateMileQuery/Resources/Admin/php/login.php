<?php
	require '/esdi/websmart/v10.6/include/xl_functions001.php';
	

	if (isset($_POST['loginData'])) {
		$userData = split(',', $_POST['loginData']);
		$username = strtoupper($userData[0]);
		$pass = $userData[1];
		$auth = $userData[2];

		$loggedIn = i5_connect("172.27.80.110", $username, $pass);

		if ($loggedIn) {
			$options = array('i5_naming' => DB2_I5_NAMING_ON);

			$execSQL = "SELECT COUNT(*) FROM QS36F/FSECF001 WHERE SEUSER = '{$userData[0]}' AND SEPGM = '{$auth}'"; //'RBNLADMINW'

			$db2conn = xl_db2_connect($options); 

			$query = db2_exec($db2conn, $execSQL, array('CURSOR' => DB2_SCROLLABLE));
			if(!$query) {
				db2_close($db2conn);
				die('<b>Error ' . db2_stmt_error() . ':' . db2_stmt_errormsg() . '</b>');
			}

			$row = db2_fetch_array($query); 

			if ($row[0] >= 1) {
				die("SUCCESS");
			} else {
				die("ACCESS");
			}
			

		} else {
			die("INVALID");
		}
		
	}
?>