<?php
	require 'convertArray.php';
	require 'EntryClasses.php';
	require 'fuelWriter.php';
	require 'settings.php';

	if(isset($_POST['formData'])) { 
		$data = $_POST['formData']; 
		$procData = convert($data);

		$begin = false;
		$entries;
		$setCount = 1;
		$iterator = 0;
		$endCheck = false;

		$entryObj = new EntryFuel();
		$entryObj->setMode($procData["MODE"]);
		$entryObj->setName($procData["CODE"]);

		foreach ($procData as $key => $value) {
			if ($key == "FAMT0") {
				$begin = true;
			}

			if ($begin) { 
				if (substr($key,0,2) == "FA") {
					if ($value == "") {
						$endCheck = true;
					} else {
						$entryObj->setFAMT((float) $value);
					}
				} elseif (substr($key,0,2) == "TA") {
					$entryObj->setTAMT((float) $value);
				} elseif (substr($key,0,4) == "ADJA") {
					$entryObj->setFuelIndex((float) $value);
				} elseif (substr($key,0,4) == "ADJP") {
					$entryObj->setFuelPrcnt((float) $value);
				}

				if ($endCheck) {
					unset($entryObj);
					break;
				}

				if ($setCount == 4) {
					$entries[$iterator] = $entryObj;
					$setCount = 0;
					$iterator++;

					unset($entryObj);
					$entryObj = new EntryFuel();
					$entryObj->setMode($procData["MODE"]);
					$entryObj->setName($procData["CODE"]);
				}

				$setCount++;
			}
		}

		if (DEBUG_PROC || DEBUG_ALL) {
			writeErrors("Entries submitted for Fuel Form: " . print_r($entries, true) . "\n");
		}

		$success = writeFuelData($entries, $procData["MODE"], $procData["CODE"]);

		die($success);
	}
	
?>