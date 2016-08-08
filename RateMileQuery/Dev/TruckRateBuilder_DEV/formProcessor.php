<?php
	require 'convertArray.php';
	require 'EntryClasses.php';
	require 'getPrecs.php';
	require 'errorLogger.php';
	require 'writer.php';
	require 'milesProcessor.php';
	require 'settings.php';


	if(isset($_POST['formData'])) { //Verify that the data passed to this program contains the expected Array
		$data = $_POST['formData']; //place the RAW Array into a local variable to be processed
		$procData = convert($data); //Convert the RAW Array to a more useable Key -> Value Array
		$PREC = $procData["PREC"];  //Set the global variable $PREC to reduce Array reads for Precedence Logic
		$PRECS = getPrecs();

		if (DEBUG_PROC || DEBUG_ALL) {
			writeErrors("Precedence of submission: " . $PREC . "\n");
			writeErrors("Data recieved from form: " . print_r($procData, true) . "\n");
		}

		$alternative;
		$mileage;
		$errors;
		$first = "";
		$begin= false;
		$iterator = 0;
		$setCount = 1;
		$entries;
		$factory = new EntryFactory();

		switch ($PREC) {
			case $PRECS["ZIP(6) TO ZIP(6)"]: // Zip6 to Zip6
				$formType = 1;
				$length = 9; //Number of fields in this entry type
				$first = "FZIPA0";

				$fromSizeA = 5;
				$fromSizeB = 5;
				$toSizeA = 5;
				$toSizeB = 5;
				$fromSelectorA = "FZIPA";
				$fromSelectorB = "FZIPB";
				$toSelectorA = "TZIPA";
				$toSelectorB = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevOrgB = $procData["FZIPA0"];
				$prevDesA = $procData["TZIPA0"];
				$prevDesB = $procData["TZIPB0"];

				break;

			case $PRECS["CITY,ST TO CITY,ST"]: // City, State to City, State
				$formType = 1;
				$length = 9; //Number of fields in this entry type
				$first = "FCITY0";
				
				$fromSizeA = 2;
				$fromSizeB = 2;
				$toSizeA = 2;
				$toSizeB = 2;
				$fromSelectorA = "FC";
				$fromSelectorB = "FS";
				$toSelectorA = "TC";
				$toSelectorB = "TS";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevOrgB = $procData["FSTATE0"];
				$prevDesA = $procData["TCITY0"];
				$prevDesB = $procData["TSTATE0"];

				break;

			case $PRECS["ZIP(6) TO ZIP(3)"]: // Zip5 to Zip3
				$formType = 1;
				$length = 9; //Number of fields in this entry type
				$first = "FZIPA0";

				$fromSizeA = 5;
				$fromSizeB = 5;
				$toSizeA = 5;
				$toSizeB = 5;
				$fromSelectorA = "FZIPA";
				$fromSelectorB = "FZIPB";
				$toSelectorA = "TZIPA";
				$toSelectorB = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevOrgB = $procData["FZIPA0"];
				$prevDesA = $procData["TZIPA0"];
				$prevDesB = $procData["TZIPB0"];

				break;

			case $PRECS["ZIP(3) TO ZIP(6)"]: // Zip3 to Zip5
				$formType = 1;
				$length = 9; //Number of fields in this entry type
				$first = "FZIPA0";

				$fromSizeA = 5;
				$fromSizeB = 5;
				$toSizeA = 5;
				$toSizeB = 5;
				$fromSelectorA = "FZIPA";
				$fromSelectorB = "FZIPB";
				$toSelectorA = "TZIPA";
				$toSelectorB = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevOrgB = $procData["FZIPA0"];
				$prevDesA = $procData["TZIPA0"];
				$prevDesB = $procData["TZIPB0"];
				break;

			case $PRECS["ZIP(3) TO CITY,ST"]: // Zip3 to City, State
				$formType = 1;
				$length = 9; //Number of fields in this entry type
				$first = "FZIPA0";

				$fromSizeA = 5;
				$fromSizeB = 5;
				$toSizeA = 2;
				$toSizeB = 2;
				$fromSelectorA = "FZIPA";
				$fromSelectorB = "FZIPB";
				$toSelectorA = "TC";
				$toSelectorB = "TS";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevOrgB = $procData["FZIPA0"];
				$prevDesA = $procData["TCITY0"];
				$prevDesB = $procData["TSTATE0"];
				break;

			case $PRECS["CITY,ST TO ZIP(3)"]: // City, State to Zip3
				$formType = 1;
				$length = 9; //Number of fields in this entry type
				$first = "FCITY0";

				$fromSizeA = 2;
				$fromSizeB = 2;
				$toSizeA = 5;
				$toSizeB = 5;
				$fromSelectorA = "FC";
				$fromSelectorB = "FS";
				$toSelectorA = "TZIPA";
				$toSelectorB = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevOrgB = $procData["FSTATE"];
				$prevDesA = $procData["TZIPA0"];
				$prevDesB = $procData["TZIPB0"];
				break;

			case $PRECS["CITY,ST TO ST"]: // City, State to State
				$formType = 3;
				$length = 8; //Number of fields in this entry type
				$first = "FCITY0";
				
				$fromSizeA = 2;
				$toSizeA = 2;
				$toSizeB = 2;
				$fromSelectorA = "FC";
				$toSelectorA = "FS";
				$toSelectorB = "TS";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["FSTATE0"];
				$prevDesB = $procData["TSTATE0"];

				break;

			case $PRECS["ST TO ZIP(6)"]: // State to Zip5
				$formType = 3;
				$length = 8; //Number of fields in this entry type
				$first = "FSTATE0";

				$fromSizeA = 2;
				$toSizeA = 5;
				$toSizeB = 5;
				$fromSelectorA = "FS";
				$toSelectorA = "TZIPA";
				$toSelectorB = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["TZIPA0"];
				$prevDesB = $procData["TZIPB0"];
				break;

			case $PRECS["ST TO CITY,ST"]: // State to City, State
				$formType = 3;
				$length = 8; //Number of fields in this entry type
				$first = "FSTATE0";
				
				$fromSizeA = 2;
				$toSizeA = 2;
				$toSizeB = 2;
				$fromSelectorA = "FS";
				$toSelectorA = "TC";
				$toSelectorB = "TS";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["TCITY0"];
				$prevDesB = $procData["TSTATE0"];

				break;

			case $PRECS["ST TO ZIP(3)"]: // State to Zip3
				$formType = 3;
				$length = 8; //Number of fields in this entry type
				$first = "FSTATE0";

				$fromSizeA = 2;
				$toSizeA = 5;
				$toSizeB = 5;
				$fromSelectorA = "FS";
				$toSelectorA = "TZIPA";
				$toSelectorB = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["TZIPA0"];
				$prevDesB = $procData["TZIPB0"];
				break;

			case $PRECS["ZIP(3) TO ST"]: // Zip3 to State
				$formType = 3;
				$length = 8; //Number of fields in this entry type
				$first = "FZIPA0";

				$fromSizeA = 5;
				$toSizeA = 5;
				$toSizeB = 1;
				$fromSelectorA = "FZIPA";
				$toSelectorA = "FZIPB";
				$toSelectorB = "T";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["FZIPB0"];
				$prevDesB = $procData["TSTATE0"];
				break;

			case $PRECS["ST TO ST"]: // State to State
				$formType = 2;
				$length = 7; //Number of fields in this entry type
				$first = "FSTATE0";

				$fromSizeA = 2;
				$toSizeA = 2;
				$fromSelectorA = "FS";
				$toSelectorA = "TS";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["TSTATE0"];
				break;

			case $PRECS["ST,ZIP(3) TO ST,ZIP(3)"]: // State to State
				$formType = 4;
				$length = 11; //Number of fields in this entry type
				$first = "FSTATE0";

				$fromSizeA = 2;
				$fromSizeB = 5;
				$fromSizeC = 5;
				$toSizeA = 2;
				$toSizeB = 5;
				$toSizeC = 5;
				$fromSelectorA = "FS";
				$fromSelectorB = "FZIPA";
				$fromSelectorC = "FZIPB";
				$toSelectorA = "TS";
				$toSelectorB = "TZIPA";
				$toSelectorC = "TZIPB";
				
				//Initialize the 'previous' variables for tracking repeating Origins and/or Destinations. 
				$prevOrgA = $procData[$first];
				$prevDesA = $procData["TSTATE0"];
				break;

			case $PRECS["MILEAGE"]: // Mileage State to State
				$success = milesProcess($procData, $PRECS);
				die($success);
				break;

			default:
				$errors = $errors . " There was an error processing your form. \n";
				writeErrors($errors);
				break;

		}

		if($procData[$first] == "") { //Verify that data has been entered before attempting to process
			$errors = $errors . " Failed to process. You must enter a starting location. \n";
			writeErrors($errors);
		}

		$MODE = $procData["MODE"];
		$CODE = $procData["SCAC"];
		$EFFDATE = $procData["EFFDATE"];
		$ENDDATE = $procData["ENDDATE"];
		$ORIGIN = $procData["ORIGIN"];
		$DESTIN = $procData["DESTIN"];

		if (DEBUG_PROC || DEBUG_ALL) {
			writeErrors("Form Type of submission: " . $formType . "\n");
		}

		switch ($formType) {
			case 1:
				foreach ($procData as $key => $value) {
					if($key == $first) { //Once the beginning of the Entry Form is found begin processing data
						$begin = true;
						$entryObj = $factory->getNewEntry($PREC); //Initialize the first Entry (record) Object
					}
					if($begin) {
						if (substr($key,0,$fromSizeA) == $fromSelectorA) { //From A
							if($value == "") {
								$endCheckOrg = true;
							} else {
								$endCheckOrg = false;
							}
							if ($prevOrgA != $value && $value != "") { 
								$prevOrgA = $value;
								$entryObj->setOrgA($prevOrgA);

							} else {
								$entryObj->setOrgA($prevOrgA);

							}
						} elseif (substr($key,0,$fromSizeB) == $fromSelectorB) { //From B

							if ($prevOrgB != $value && $value != "") {
								$prevOrgB = $value;
								$entryObj->setOrgB($prevOrgB);

							} else {
								$entryObj->setOrgB($prevOrgB);

							}
						} elseif (substr($key,0,$toSizeA) == $toSelectorA) { //To A

							if ($prevDesA != $value && $value != "") {
								$prevDesA = $value;
								$entryObj->setDesA($prevDesA);
							} else {
								$entryObj->setDesA($prevDesA);
							}
							if($value == "") {
								$endCheckDes = true;
							} else {
								$endCheckDes = false;
							}
						} elseif (substr($key,0,$toSizeB) == $toSelectorB) { //To B
							if ($prevDesB != $value && $value != "") {
								$prevDesB = $value;
								$entryObj->setDesB($prevDesB);
							} else {
								$entryObj->setDesB($prevDesB);
							}
						} elseif (substr($key,0,3) == "RPM") { //Rate Per Mile
									
							$entryObj->setRpm((float) $value);
						} elseif (substr($key,0,3) == "FLA") { //Flate Rate

							$entryObj->setFlat((float) $value);
						} elseif (substr($key,0,3) == "FUE") { //Fuel Flag

							$entryObj->setFuel($value);
						} elseif (substr($key,0,3) == "MIN") { //Minimum Charge

							$entryObj->setMin((float) $value);
						} elseif (substr($key,0,3) == "COM") { //Record Comments

							$entryObj->setComment($value);
						} elseif (substr($key,0,2) == "MB") { //Mileage Begin

							$entryObj->setMBegin((integer) $value);
						} elseif (substr($key,0,2) == "ME") { //Mileage End

							$entryObj->setMEnd((integer) $value);
						}

						//If both the From City and To City were blank, end of data.
						if($endCheckOrg == true && $endCheckDes == true) { 

							//The last object is never used so Unset for intelligent garbage collection
							unset($entryObj); 
							break;
						}

						//If a full record has been read, add the Entry (record) Object to the Array, 
						//reset field count and generate new Entry (record) Object
						if($setCount == $length) {
							$entries[$iterator] = $entryObj;

							$setCount = 0;
							$iterator++;
							$entryObj = $factory->getNewEntry($PREC);

						}

						$setCount++;
					}
				}
				break;

			case 2:
				foreach ($procData as $key => $value) {
					if($key == $first) { //Once the beginning of the Entry Form is found begin processing data
						$begin = true;
						$entryObj = $factory->getNewEntry($PREC); //Initialize the first Entry (record) Object
					}
					if($begin) {
						if (substr($key,0,$fromSizeA) == $fromSelectorA) { //From State
							if($value == "") {
								$endCheckOrg = true;
							} else {
								$endCheckOrg = false;
							}
							if ($prevOrgA != $value && $value != "") { 
								$prevOrgA = $value;
								$entryObj->setOrgA($prevOrgA);

							} else {							
								$entryObj->setOrgA($prevOrgA);

							}
						} elseif (substr($key,0,$toSizeA) == $toSelectorA) { //To State

							if ($prevDesA != $value && $value != "") {
								$prevDesA = $value;
								$entryObj->setDesA($prevDesA);
							} else {
								$entryObj->setDesA($prevDesA);
							}
							if($value == "") {
								$endCheckDes = true;
							} else {
								$endCheckDes = false;
							}
						} elseif (substr($key,0,3) == "RPM") { //Rate Per Mile
									
							$entryObj->setRpm((float) $value);
						} elseif (substr($key,0,3) == "FLA") { //Flate Rate

							$entryObj->setFlat((float) $value);
						} elseif (substr($key,0,3) == "FUE") { //Fuel Flag

							$entryObj->setFuel($value);
						} elseif (substr($key,0,2) == "MB") { //Mileage Begin

							$entryObj->setMBegin((integer) $value);
						} elseif (substr($key,0,2) == "MI") { //Mileage Begin

							$entryObj->setMin((float) $value);
						} elseif (substr($key,0,2) == "ME") { //Mileage End

							$entryObj->setMEnd((integer) $value);
						} elseif (substr($key,0,3) == "COM") { //Record Comments

							$entryObj->setComment($value);
						}

						//If both the From City and To City were blank, end of data.
						if($endCheckOrg == true && $endCheckDes == true) { 

							//The last object is never used so Unset for intelligent garbage collection
							unset($entryObj); 
							break;
						}

						//If a full record has been read, add the Entry (record) Object to the Array, 
						//reset field count and generate new Entry (record) Object
						if($setCount == $length) {
							$entries[$iterator] = $entryObj;
							$setCount = 0;
							$iterator++;
							$entryObj = $factory->getNewEntry($PREC);

						}

						$setCount++;
					}
				} 
				break;

			case 3:
				foreach ($procData as $key => $value) {
					if($key == $first) { //Once the beginning of the Entry Form is found begin processing data
						$begin = true;
						$entryObj = $factory->getNewEntry($PREC); //Initialize the first Entry (record) Object
					}
					if($begin) {
						if (substr($key,0,$fromSizeA) == $fromSelectorA) { 
							if($value == "") {
								$endCheckOrg = true;
							} else {
								$endCheckOrg = false;
							}
							if ($prevOrgA != $value && $value != "") { 
								$prevOrgA = $value;
								$entryObj->setOrgA($prevOrgA);

							} else {
								$entryObj->setOrgA($prevOrgA);

							}
						} elseif (substr($key,0,$toSizeA) == $toSelectorA) { 

							if ($prevDesA != $value && $value != "") {
								$prevDesA = $value;
								$entryObj->setDesA($prevDesA);
							} else {
								$entryObj->setDesA($prevDesA);
							}
							if($value == "") {
								$endCheckDesA = true;
							} else {
								$endCheckDesA = false;
							}
						} elseif (substr($key,0,$toSizeB) == $toSelectorB) {
							if ($prevDesB != $value && $value != "") {
								$prevDesB = $value;
								$entryObj->setDesB($prevDesB);
							} else {
								$entryObj->setDesB($prevDesB);
							}
							if($value == "") {
								$endCheckDesB = true;
							} else {
								$endCheckDesB = false;
							}
						} elseif (substr($key,0,3) == "RPM") { //Rate Per Mile
									
							$entryObj->setRpm((float) $value);
						} elseif (substr($key,0,3) == "FLA") { //Flate Rate

							$entryObj->setFlat((float) $value);
						} elseif (substr($key,0,3) == "FUE") { //Fuel Flag

							$entryObj->setFuel($value);
						} elseif (substr($key,0,2) == "MB") { //Mileage Begin

							$entryObj->setMBegin((integer) $value);
						} elseif (substr($key,0,2) == "MI") { //Mileage Begin

							$entryObj->setMin((float) $value);
						} elseif (substr($key,0,2) == "ME") { //Mileage End

							$entryObj->setMEnd((integer) $value);
						} elseif (substr($key,0,3) == "COM") { //Record Comments

							$entryObj->setComment($value);
						}

						//If both the From and To were blank, end of data.
						if($endCheckOrg && $endCheckDesA && $endCheckDesB) { 

							//The last object is never used so Unset for intelligent garbage collection
							unset($entryObj); 
							break;
						}

						//If a full record has been read, add the Entry (record) Object to the Array, 
						//reset field count and generate new Entry (record) Object
						if($setCount == $length) {
							$entries[$iterator] = $entryObj;

							$setCount = 0;
							$iterator++;
							$entryObj = $factory->getNewEntry($PREC);

						}

						$setCount++;
					}
				}
				break;

			case 4:
				foreach ($procData as $key => $value) {
					if($key == $first) { //Once the beginning of the Entry Form is found begin processing data
						$begin = true;
						$entryObj = $factory->getNewEntry($PREC); //Initialize the first Entry (record) Object
					}
					if($begin) {
						if (substr($key,0,$fromSizeA) == $fromSelectorA) { //From A
							if ($prevOrgA != $value && $value != "") { 
								$prevOrgA = $value;
								$entryObj->setOrgA($prevOrgA);

							} else {
								$entryObj->setOrgA($prevOrgA);

							}
						} elseif (substr($key,0,$fromSizeB) == $fromSelectorB) { //From B

							if ($prevOrgB != $value && $value != "") { 
								$prevOrgB = $value;
								$entryObj->setOrgB($prevOrgB);

							} else {
								$entryObj->setOrgB($prevOrgB);

							}

						} elseif (substr($key,0,$fromSizeC) == $fromSelectorC) { //From C

							if ($prevOrgC != $value && $value != "") { 
								$prevOrgC = $value;
								$entryObj->setOrgC($prevOrgC);

							} else {
								$entryObj->setOrgC($prevOrgC);

							}

						} elseif (substr($key,0,$toSizeA) == $toSelectorA) { //To A

							if ($prevDesA != $value && $value != "") {
								$prevDesA = $value;
								$entryObj->setDesA($prevDesA);
							} else {
								$entryObj->setDesA($prevDesA);
							}
						} elseif (substr($key,0,$toSizeB) == $toSelectorB) { //To B

							if ($prevDesB != $value && $value != "") {
								$prevDesB = $value;
								$entryObj->setDesB($prevDesB);
							} else {
								$entryObj->setDesB($prevDesB);
							}
							
						} elseif (substr($key,0,$toSizeC) == $toSelectorC) { //To C

							if ($prevDesC != $value && $value != "") {
								$prevDesC = $value;
								$entryObj->setDesC($prevDesC);
							} else {
								$entryObj->setDesC($prevDesC);
							}

						} elseif (substr($key,0,3) == "RPM") { //Rate Per Mile
							if($value == "") {
								$endCheckOrg = true;
							} else {
								$endCheckOrg = false;
							}									
							$entryObj->setRpm((float) $value);
						} elseif (substr($key,0,3) == "FLA") { //Flate Rate
							if($value == "") {
								$endCheckDes = true;
							} else {
								$endCheckDes = false;
							}
							$entryObj->setFlat((float) $value);
						} elseif (substr($key,0,3) == "FUE") { //Fuel Flag

							$entryObj->setFuel($value);
						} elseif (substr($key,0,3) == "MIN") { //Minimum Charge

							$entryObj->setMin((float) $value);
						} elseif (substr($key,0,3) == "COM") { //Record Comments

							$entryObj->setComment($value);
						} elseif (substr($key,0,2) == "MB") { //Mileage Begin

							$entryObj->setMBegin((integer) $value);
						} elseif (substr($key,0,2) == "ME") { //Mileage End

							$entryObj->setMEnd((integer) $value);
						}

						//If both the From City and To City were blank, end of data.
						if($endCheckOrg == true && $endCheckDes == true) { 

							//The last object is never used so Unset for intelligent garbage collection
							unset($entryObj); 
							break;
						}

						//If a full record has been read, add the Entry (record) Object to the Array, 
						//reset field count and generate new Entry (record) Object
						if($setCount == $length) {
							$entries[$iterator] = $entryObj;

							$setCount = 0;
							$iterator++;
							$entryObj = $factory->getNewEntry($PREC);

						}

						$setCount++;
					}
				}
				break;
			
			default:
				$errors = $errors . " There was an error processing your form. \n";
				writeErrors($errors);
				break;
		}

		if (DEBUG_PROC || DEBUG_ALL) {
			writeErrors("Entries sent to Writer: " . print_r($entries, true) . "\nMODE: " . $MODE . "\nCODE: " . $CODE . "\nEFFDATE: " . $EFFDATE . "\nENDDATE: " . $ENDDATE . "\nOrigin Country: " . $ORIGIN . "\nDestination Country: " . $DESTIN . "\n");
		}

		//Once all of the data in the form has been processed and prepped for writing to the DB 
		//Pass the constants and the Array of Entry (record) Objects to the write function
		$success = writeData($PREC, $PRECS, $entries, $MODE, $CODE, $EFFDATE, $ENDDATE, $ORIGIN, $DESTIN);

		die($success);
	}
?>