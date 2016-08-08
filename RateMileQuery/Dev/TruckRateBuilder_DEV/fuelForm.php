<?php
	require 'convertArray.php';
	require '/esdi/websmart/v10.6/include/xl_functions001.php';
	require 'settings.php';
	require 'errorLogger.php';

	if(isset($_POST['formData'])) { 
		$data = $_POST['formData']; 
		$procData = convert($data);

		$selString = "SELECT SPBAMT, SPEAMT, SPFUEL$, SPPCT FROM JOELIB/BNASFUELP WHERE SMODE = '" . $procData["MODE"] . "' AND SNAME = '" . $procData["CODE"] . "'";

		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$db_connection = xl_db2_connect($options);

		if (!$db_connection)
		{
			writeErrors("Could not connect to database: " . db2_conn_error() . "\n");
		}

		$query = db2_exec($db_connection, $selString, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db_connection);
			writeErrors("Error " . db2_stmt_error() . ":" . db2_stmt_errormsg() . "\n");
		}

		$response = "";

		$response .= "<section>
			  	<div id='headers' class='headers'>
			  	 <div class='subHeader'>From Amount</div>
			  	 <div class='subHeader'>To Amount</div>
				 <div class='subHeader'>Adj RPM</div>
				 <div class='subHeader'>Adjust Percent</div>
				</div>
				<div class='container'>

				<table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >
				 <tbody>";

		$count = 0;

		while ($row = db2_fetch_assoc($query)) {
			$rowString = "<tr class='inputTR'>";
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]),ENT_QUOTES,'ISO-8859-1');
				$escapedField = xl_fieldEscape($key);
				if ($escapedField == "SPBAMT") {
					$rowString .= "<td class='inputTD'><input id='FAMT" . $count . "' class='entryNum' type='text' name='FAMT" . $count . "' size='6' maxlength='7' value='" . $row[$key] . "' /></td>";
				} elseif ($escapedField == "SPEAMT") {
					$rowString .= "<td class='inputTD'><input id='TAMT" . $count . "' class='entryNum' type='text' name='TAMT" . $count . "' size='6' maxlength='7' value='" . $row[$key] . "' /></td>";
				} elseif ($escapedField == 'SPFUEL_') {
					$rowString .= "<td class='inputTD'><input id='ADJAMT" . $count . "' class='entryNum' type='text' name='ADJAMT" . $count . "' size='6' maxlength='7' value='" . $row[$key] . "' /></td>";
				} elseif ($escapedField == "SPPCT") {
					$rowString .= "<td class='inputTD'><input id='ADJPRCNT". $count . "' class='entryNum' type='text' name='ADJPRCNT". $count . "' size='6' maxlength='7' value='" . $row[$key] . "' /> </td>";
				}

				$count++;
			}
			$rowString .= "</tr>";	
			$response .= $rowString;
		}
	
		

		

		for ($i=$count; $i < 201; $i++) {
			$response .= "<tr class='inputTR'>
		   	 	   <td class='inputTD'><input id='FAMT" . $i . "' class='entryNum' type='text' name='FAMT" . $i . "' size='6' maxlength='7' /></td>
		   	 	   <td class='inputTD'><input id='TAMT" . $i . "' class='entryNum' type='text' name='TAMT" . $i . "' size='6' maxlength='7' /></td>
		   	 	   <td class='inputTD'><input id='ADJAMT" . $i . "' class='entry fuel' type='text' name='ADJAMT" . $i . "' size='6' maxlength='7' /></td>
		   	 	   <td class='inputTD'><input id='ADJPRCNT". $i . "' class='' type='text' name='ADJPRCNT". $i . "' size='6' maxlength='7' /> </td>
	      	      </tr>";
		}
		$response .= "</tbody></table></div></section><br />";

		$response .= "<input id='fuelCopy' class='mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent' type='button' value='Copy Table' /> ";


		if (DEBUG_FORM || DEBUG_ALL) {
			writeErrors("Fuel Form HTML Response: " . $response . "\n");
		}

		echo $response;

	}
	

?>