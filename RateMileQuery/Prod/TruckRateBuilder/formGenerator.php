<?php
	require 'convertArray.php';
	require 'getPrecs.php';
	require 'settings.php';
	require 'errorLogger.php';
	
	if(isset($_POST['formData'])) { //Verify that the data passed to this program contains the expected Array
		$data = $_POST['formData']; //place the RAW Array into a local variable to be processed
		$procData = convert($data); //Convert the RAW Array to a more useable Key -> Value Array
		$PREC = $procData["PREC"];  //Set the variable $PREC to reduce Array reads for Precedence Logic
		$PRECS = getPrecs();
		$response = "";

		if (DEBUG_FORM || DEBUG_ALL) {
			writeErrors("Precedence Selected for Form: " . $PREC . "\n");
		}

		/*
	 	 *	Processes the data contained within the prepared Key -> Value Array. If the task is genForm it passes back HTML code 
	 	 *  for the appropriate Form otherwise it calls genEntries to generate an Array of Entry Objects in preperation of writing
	 	 *  to the DB.
	 	 */
		switch ($PREC) {
				case $PRECS["ZIP(6) TO ZIP(6)"]: //Zip6 to Zip6
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From Zip 1</div>
						  <div class='subHeader'>From Zip 2</div>
						  <div class='subHeader'>To Zip 1</div>
						  <div class='subHeader'>To Zip 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip zip6 All' type='text' name='FZIPA" . $i . "' size='6' maxlength='6' /></td>
	               	 		  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip zip6' type='text' name='FZIPB" . $i . "' size='6' maxlength='6' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip6 All' type='text' name='TZIPA" . $i . "' size='6' maxlength='6' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip6' type='text' name='TZIPB" . $i . "' size='6' maxlength='6' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["CITY,ST TO CITY,ST"]: //City, State to City, State
					$response .= "<section>									
					      <span id='headers' class='headers'>
					      <span class='subHeader'>From City</span>
						  <span class='subHeader'>From State</span>
						  <span class='subHeader'>To City</span>
						  <span class='subHeader'>To State</span>
						  <span class='subHeader'>RPM</span>
						  <span class='subHeader'>FLAT</span>
						  <span class='subHeader'>MIN</span>
						  <span class='subHeader'>FUEL (Y/N)</span>
						  <span class='subHeader'>Comments</span>
					      </span>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FCITY" . $i . "' class='entry city' type='text' name='FCITY" . $i . "' size='30' maxlength='30' /></td>
	               	 		  <td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TCITY" . $i . "' class='entry city' type='text' name='TCITY" . $i . "' size='30' maxlength='30' /></td>
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";

					break;

				case $PRECS["ZIP(6) TO ZIP(3)"]: //Zip5 to Zip3
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From Zip5 1</div>
						  <div class='subHeader'>From Zip5 2</div>
						  <div class='subHeader'>To Zip3 1</div>
						  <div class='subHeader'>To Zip3 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip zip5 All' type='text' name='FZIPA" . $i . "' size='5' maxlength='5' /></td>
	               	 		  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip zip5' type='text' name='FZIPB" . $i . "' size='5' maxlength='5' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip3 All' type='text' name='TZIPA" . $i . "' size='5' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip3' type='text' name='TZIPB" . $i . "' size='5' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ZIP(3) TO ZIP(6)"]: //Zip3 to Zip5
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From Zip3 1</div>
						  <div class='subHeader'>From Zip3 2</div>
						  <div class='subHeader'>To Zip5 1</div>
						  <div class='subHeader'>To Zip5 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip zip3 All' type='text' name='FZIPA" . $i . "' size='5' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip zip3' type='text' name='FZIPB" . $i . "' size='5' maxlength='3' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip5 All' type='text' name='TZIPA" . $i . "' size='5' maxlength='5' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip5' type='text' name='TZIPB" . $i . "' size='5' maxlength='5' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ZIP(3) TO CITY,ST"]: //Zip3 to City, State
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From Zip3 1</div>
						  <div class='subHeader'>From Zip3 2</div>
						  <div class='subHeader'>To City</div>
						  <div class='subHeader'>To State</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip zip3 All' type='text' name='FZIPA" . $i . "' size='5' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip zip3' type='text' name='FZIPB" . $i . "' size='5' maxlength='3' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TCITY" . $i . "' class='entry city' type='text' name='TCITY" . $i . "' size='30' maxlength='30' /></td>
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["CITY,ST TO ZIP(3)"]: //City, State to Zip3 
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From City</div>
						  <div class='subHeader'>From State</div>
						  <div class='subHeader'>To Zip3 1</div>
						  <div class='subHeader'>To Zip3 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FCITY" . $i . "' class='entry city' type='text' name='FCITY" . $i . "' size='30' maxlength='30' /></td>
	               	 		  <td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip3 All' type='text' name='TZIPA" . $i . "' size='3' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip3' type='text' name='TZIPB" . $i . "' size='3' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ST,ZIP(3) TO ST,ZIP(3)"]: //State, Zip3 to State, Zip3 
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From State</div>
						  <div class='subHeader'>From Zip3 1</div>
						  <div class='subHeader'>From Zip3 2</div>
						  <div class='subHeader'>To State</div>
						  <div class='subHeader'>To Zip3 1</div>
						  <div class='subHeader'>To Zip3 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip zip3 All' type='text' name='FZIPA" . $i . "' size='3' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip zip3' type='text' name='FZIPB" . $i . "' size='3' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip3 All' type='text' name='TZIPA" . $i . "' size='3' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip3' type='text' name='TZIPB" . $i . "' size='3' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["CITY,ST TO ST"]: //City, State to State 
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From City</div>
						  <div class='subHeader'>From State</div>
						  <div class='subHeader'>To State</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FCITY" . $i . "' class='entry city' type='text' name='FCITY" . $i . "' size='30' maxlength='30' /></td>
	               	 		  <td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='6' maxlength='2' /></td>
	               	 		  
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='6' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ST TO ZIP(6)"]: //State to Zip5
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From State</div>
						  <div class='subHeader'>To Zip5 1</div>
						  <div class='subHeader'>To Zip5 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>	               	 		  
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip5 All' type='text' name='TZIPA" . $i . "' size='5' maxlength='5' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip5' type='text' name='TZIPB" . $i . "' size='5' maxlength='5' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ST TO CITY,ST"]: //State to City, State 
					$response .= "<section>
					      <div id='headers' class='headers'>
						  <div class='subHeader'>From State</div>
						  <div class='subHeader'>To City</div>
						  <div class='subHeader'>To State</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "
	               	 		  <td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='TCITY" . $i . "' class='entry city' type='text' name='TCITY" . $i . "' size='30' maxlength='30' /></td>
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ST TO ZIP(3)"]: //State to Zip3
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From State</div>
						  <div class='subHeader'>To Zip3 1</div>
						  <div class='subHeader'>To Zip3 2</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>	               	 		  
	               	 		  <td class='inputTD'><input id='TZIPA" . $i . "' class='entry zip zip3 All' type='text' name='TZIPA" . $i . "' size='4' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='TZIPB" . $i . "' class='entry zip zip3' type='text' name='TZIPB" . $i . "' size='4' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ZIP(3) TO ST"]: //Zip3 to State
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From Zip3 1</div>
					      <div class='subHeader'>From Zip3 2</div>
						  <div class='subHeader'>To State</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip zip3 All' type='text' name='FZIPA" . $i . "' size='4' maxlength='3' /></td>	               	 		  
	               	 		  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip zip3' type='text' name='FZIPB" . $i . "' size='4' maxlength='3' /></td>
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["ST TO ST"]: //State to State
					$response .= "<section>
					      <div id='headers' class='headers'>
					      <div class='subHeader'>From State</div>
					      <div class='subHeader'>To State</div>
						  <div class='subHeader'>RPM</div>
						  <div class='subHeader'>FLAT</div>
						  <div class='subHeader'>MIN</div>
						  <div class='subHeader'>FUEL (Y/N)</div>
						  <div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >";
					
					$response .= "<tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>";
	               		$response .= "<td class='inputTD'><input id='FSTATE" . $i . "' class='entry state' type='text' name='FSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='TSTATE" . $i . "' class='entry state' type='text' name='TSTATE" . $i . "' size='4' maxlength='2' /></td>
	               	 		  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='MIN" . $i . "' class='entryNum' type='text' name='MIN" . $i . "' size='5' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>";
	               		$response .= "</tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				case $PRECS["MILEAGE"]: //Mileage State to State 
					$response .= "<section class='section1'>
					      <div id='headers2' class='headers'>
						  	<div class='subHeader2'>States</div>
						  	<div class='subHeader2'>Zip From</div>
						  	<div class='subHeader2'>Zip To</div>
					      </div>
					      <div class='container2'>

					      <table id='head-table' class='main-list input-capable-list ui-widget-content inputTable2' cellspacing='2' >
					        <tbody>";
					for ($i=0; $i < 14; $i++) { 
						$response .= "<tr class='inputTR'>
							  <td class='inputTD'><input id='STATE" . $i . "' class='entry state' type='text' name='STATE" . $i . "' size='4' maxlength='2' /></td>
	               			  <td class='inputTD'><input id='FZIPA" . $i . "' class='entry zip3 All' type='text' name='FZIPA" . $i . "' size='4' maxlength='3' /></td>
	               			  <td class='inputTD'><input id='FZIPB" . $i . "' class='entry zip3' type='text' name='FZIPB" . $i . "' size='4' maxlength='3' /></td>
	               			  </tr>";
					}
					$response .= "</tbody></table></div></section>";

					$response .= "<section>
					      <div id='headers' class='headers'>
						  	<div class='subHeader'>Miles Begin</div>
						  	<div class='subHeader'>Miles End</div>
						  	<div class='subHeader'>RPM</div>
						  	<div class='subHeader'>FLAT</div>
						  	<div class='subHeader'>FUEL (Y/N)</div>
						  	<div class='subHeader'>Comments</div>
					      </div>
					      <div class='container'>

					      <table id='list-table' class='main-list input-capable-list ui-widget-content inputTable' cellspacing='2' >
					        <tbody>";
					for ($i=0; $i < 101 ; $i++) {
						$response .= "<tr class='inputTR'>
	               			  <td class='inputTD'><input id='MBEGIN" . $i . "' class='miles All' type='text' name='MBEGIN" . $i . "' size='5' maxlength='5' /></td>
		               	 	  <td class='inputTD'><input id='MEND" . $i . "' class='miles miles2' type='text' name='MEND" . $i . "' size='5' maxlength='5' /></td>
		               	 	  <td class='inputTD'><input id='RPM" . $i . "' class='entryNum' type='text' name='RPM" . $i . "' size='4' maxlength='7' /></td>
		               	 	  <td class='inputTD'><input id='FLAT" . $i . "' class='entryNum' type='text' name='FLAT" . $i . "' size='8' maxlength='12' /></td>
		               	 	  <td class='inputTD'><input id='FUEL" . $i . "' class='entry fuel' type='text' name='FUEL" . $i . "' size='1' maxlength='1' /></td>
		               	 	  <td class='inputTD'><textarea id='COMNTS". $i . "' name='COMNTS". $i . "' maxlength='200' rows='3' cols='30'></textarea></td>
	               		      </tr>";
					}
					$response .= "</tbody></table></div></section>";
					break;

				default:

					$errors = $errors . " There was an error processing your form. " . print_r($procData, true) . "\n";
					writeErrors();
					break;
		}

		echo $response;
		die();
		 
	}
?>