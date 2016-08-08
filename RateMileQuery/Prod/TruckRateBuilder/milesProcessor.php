<?php
	function milesProcess($procData, $PRECS) {
		$PREC = $procData["PREC"];
		$beginState = false;
		$beginMiles = false;
		$iterator = 1;
		$setCount = 0;
		$entries;
		$factory = new EntryFactory();
		$stateArray;
		$entrySaveArray;
		$stateTemp = "";
		$zipTemp = "";
		$zips;
		$zipRanges;
		$zipInsert;

		foreach ($procData as $key => $value) {
			if($key == "STATE0") { //Once the beginning of the Entry Form is found begin processing data
				$beginState = true;
			}
			if ($key == "MBEGIN0") {
				$beginState = false;
				$beginMiles = true;
				$setCount = 0;
				$iterator = 1;
			}
			if($beginState) {
				if (substr($key,0,2) == "ST") { //State
					if($value != "") {
						$stateTemp = $value;
						$stateArray[$stateTemp][""] = "";
					}
				} elseif (substr($key,0,5) == "FZIPA") { //From Zip Range
					if($value != "") {
						$zipTemp = $value;
					}
				} elseif (substr($key,0,5) == "FZIPB") { //From Zip Range
					if($value != "") {
						if(isset($stateArray[$stateTemp])) {
							unset($stateArray[$stateTemp]);
						}
						$stateArray[$stateTemp . $setCount][$zipTemp]= $value;
					}
				}

				if($iterator == 3) {
						$iterator = 0;
						$setCount++;
				}
				$iterator++;
			}
			if ($beginMiles) {

				if (substr($key,0,3) == "RPM") { //Rate per Mile

					$entrySaveArray["ENTRY" . $setCount . " RPM"] = $value;
				} elseif (substr($key,0,3) == "FLA") { //Flat Rate
					
					$entrySaveArray["ENTRY" . $setCount . " FLA"] = $value;
				} elseif (substr($key,0,3) == "FUE") { //Fuel Y / N
					if($value != "") {
						$entrySaveArray["ENTRY" . $setCount . " FUE"] = $value;
					} else {
						$entrySaveArray["ENTRY" . $setCount . " FUE"] = "N";
					}
				} elseif (substr($key,0,2) == "MB") { //Mileage Begin
					if($value != "") {
						$entrySaveArray["ENTRY" . $setCount . " MB"] = $value;
					} else {
						unset($entrySaveArray["ENTRY" . $setCount . " RPM"]);
						unset($entrySaveArray["ENTRY" . $setCount . " FLA"]);
						unset($entrySaveArray["ENTRY" . $setCount . " FUE"]);
						break;
					}
				} elseif (substr($key,0,2) == "ME") { //Mileage End

					$entrySaveArray["ENTRY" . $setCount . " ME"] = $value; 
				} elseif (substr($key,0,3) == "COM") { //Comments 
					
					$entrySaveArray["ENTRY" . $setCount . " COM"] = $value;
				}

				if($iterator == 6) {
						$iterator = 0;
						$setCount++;
				}
				$iterator++;
			}
		}
		// echo "<pre>";
		$length = $setCount;
		$setCount = 0;
		$entryCount = 0;
		foreach ($stateArray as $key => $value) {
			// echo "Outer ForEach Key: " . $key . " ";
			// echo "Outer ForEach Value: " . $value . "\n";
			foreach ($stateArray as $key2 => $value2) {
				// echo "Middle ForEach Key: " . $key2 . " ";
				// echo "Middle ForEach Value: " . $value2 . "\n";
				$iterator = 0;
				foreach ($entrySaveArray as $entryKey => $entryValue) {
					// echo "Inner ForEach Key: " . $entryKey . " ";
					// echo "Inner ForEach Value: " . $entryValue . "\n";
					$iterator++;
					if($iterator == 6) {
						$entryObj = $factory->getNewEntry($PREC);			
						$entryObj->setOrgA(substr($key, 0, 2));
						foreach ($value as $A => $B) {
							$entryObj->setOrgB($A);
							$entryObj->setOrgC($B);
						}
						$entryObj->setDesA(substr($key2, 0, 2));
						foreach ($value2 as $A => $B) {
							$entryObj->setDesB($A);
							$entryObj->setDesC($B);
						}
						$entryObj->setRpm((float) $entrySaveArray["ENTRY" . $entryCount . " RPM"]);
						$entryObj->setFlat((float) $entrySaveArray["ENTRY" . $entryCount . " FLA"]);
						$entryObj->setFuel($entrySaveArray["ENTRY" . $entryCount . " FUE"]);
						$entryObj->setMBegin((int) $entrySaveArray["ENTRY" . $entryCount . " MB"]);
						$entryObj->setMEnd((int) $entrySaveArray["ENTRY" . $entryCount . " ME"]);
						$entryObj->setComment($entrySaveArray["ENTRY" . $entryCount . " COM"]);
						$entries[$setCount] = $entryObj;
						$setCount++;
						// echo "Entry Count: " . $entryCount . "\n";
						if ($entryCount == $length - 1) {
							$entryCount = 0;
						} else {
							$entryCount++;
						}

						$iterator = 0;

						// print_r($entryObj);
					}
					
				}
				
			}
		}		

		$MODE = $procData["MODE"];
		$CODE = $procData["SCAC"];
		$EFFDATE = $procData["EFFDATE"];
		$ENDDATE = $procData["ENDDATE"];
		$ORIGIN = $procData["ORIGIN"];
		$DESTIN = $procData["DESTIN"];


		$success = writeData($PREC, $PRECS, $entries, $MODE, $CODE, $EFFDATE, $ENDDATE, $ORIGIN, $DESTIN);

		if (DEBUG_PROC || DEBUG_ALL) {
			writeErrors("Response from Writer: " . $success . "\n");
		}

		return $success;

	}
?>