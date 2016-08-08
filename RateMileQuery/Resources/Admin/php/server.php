<?php
	require('/esdi/websmart/v10.10/include/xl_functions001.php');

	if (isset($_POST["command"])) {
		$command = $_POST["command"];

		$conn = xl_i5_connect();

		$output["out1"] = "";
		$parms["parm1"] = "";

		if ($command == "START") {
			$output = i5_command("CALL JOELIB/TESTC", $parms, $output, $conn);
		} elseif ($command == "STOP") {
			
		}
		
	}

?>