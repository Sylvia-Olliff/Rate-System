<?php
	require '/esdi/websmart/v10.6/include/xl_functions001.php';
	require 'getMiles.php';
	require 'convertArray.php';
	require 'viewAccessorial.php';
	require 'errorLogger.php';
	require 'settings.php';
	
	global $selString;
	global $PRECS;
	global $miles;

	if(isset($_POST["viewData"])) {
		$convData = convert($_POST["viewData"]);
		$procData = genDate($convData);
		getPrecs();
		buildSelect($procData);
		genView();
	}

	function getPrecs() {
		global $PRECS;

		$selString = "SELECT PRTYPE, PRDESC FROM JOELIB/BNAPRECP";

		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$db_connection = xl_db2_connect($options);

		if (!$db_connection)
		{
			writeErrors(" Could not connect to the database for PRECS. " . db2_conn_error() . "\n");
		}

		$query = db2_exec($db_connection, $selString, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db_connection);
			writeErrors("Error ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "\n");
		}

		while ($row = db2_fetch_assoc($query)) {
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]),ENT_QUOTES,'ISO-8859-1');
				$escapedField = xl_fieldEscape($key);

				if ($escapedField == "PRTYPE") {
					$tempType = $row[$key];
				} elseif ($escapedField == "PRDESC") {
					$PRECS[(string) trim($row[$key])] = $tempType;
				}
			}
		}

		db2_close($db_connection);
		if(DEBUG_PRECS || DEBUG_ALL) {
			writeErrors(" Value of PRECS: " . print_r($PRECS, true) . "\n");
		}
	}

	function genDate($convData) { //Create ISO Date format number and add it to Data Array

		if($convData["EFFDATE"] == "") {
		
			$month = strftime("%m");

			$day = strftime("%d");
			
			$year = strftime("%Y");

			$currentDate = $year . $month . $day;
			$currentDate = (integer) $currentDate;

			$convData["DATE"] = $currentDate;

		} else {
			$convData["DATE"] = $convData["EFFDATE"];			
		}

		return $convData;

	}

	function buildSelect($procData) {
		global $selString;

		$miles = $procData["MILES"];
		$originCity = $procData["FCITY"];
		$destinCity = $procData["TCITY"];
		$originState = $procData["FSTATE"];
		$destinState = $procData["TSTATE"];
		$date = $procData["DATE"];

		if ($miles == "") {
			$selString = " SELECT RACODE, RPCNAME, CONTACT, PHONE, BASE, IFNULL(FUEL, 0) as FUELC, (BASE + IFNULL(FUEL, 0)) as TOTAL, MILE, RARPM, RANOTE FROM (

						SELECT DISTINCT
						RATYPE as PREC, 
						RACODE, 
						RPCNAME, 
						(SELECT RCCONT FROM XL_RBNALT/BNACONTP WHERE RCSCAC = RATE.RACODE AND RCDEP = 'DISPATCH/CS' AND RCPHL = 1 ORDER BY RCCONT FETCH FIRST ROW ONLY) as CONTACT, 
						(SELECT RCPHONE FROM XL_RBNALT/BNACONTP WHERE RCSCAC = RATE.RACODE AND RCDEP = 'DISPATCH/CS' AND RCPHL = 1 ORDER BY RCCONT FETCH FIRST ROW ONLY) as PHONE,
						CASE 
							WHEN RAPTOP <> 0
								THEN RAPTOP
								ELSE CASE
										WHEN (SELECT COUNT(*) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState') <> 0 
											
											THEN CASE
												WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM' 
													THEN (SELECT CASE WHEN (BSMILE * RATE.RARPM) < RATE.RAMC THEN RATE.RAMC ELSE (BSMILE * RATE.RARPM) END FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
													ELSE (SELECT CASE WHEN (BPMILE * RATE.RARPM) < RATE.RAMC THEN RATE.RAMC ELSE (BPMILE * RATE.RARPM) END FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
											END
								END			
						END as BASE,
						CASE 
							WHEN RAFINCL <> 'Y' 
								THEN CASE 
									WHEN (SELECT PRFTABL FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) = ''
										THEN CASE 								
											WHEN RATE.RAFUEL <> '' 
												THEN (SELECT 
													CASE 
														WHEN SP.SPPCT = 0 
															THEN CASE
																WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
																	THEN (SELECT (BSMILE * SP.SPFUEL$) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																	ELSE (SELECT (BPMILE * SP.SPFUEL$) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																END
															ELSE CASE 
																WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
																   THEN (SELECT (BSMILE * SP.SPPCT) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																   ELSE (SELECT (BPMILE * SP.SPPCT) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																END
													END
													FROM JOELIB/BNASFUELP as SP 
													WHERE SPBAMT < CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END 
														  AND SPEAMT > CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
														  AND SNAME = RATE.RAFUEL) 
												ELSE (SELECT 
													CASE 
														WHEN SP.SPPCT = 0 
															THEN CASE
																WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
																	THEN (SELECT (BSMILE * SP.SPFUEL$) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																	ELSE (SELECT (BPMILE * SP.SPFUEL$) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																END
															ELSE CASE 
																WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
																   THEN (SELECT (BSMILE * SP.SPPCT) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																   ELSE (SELECT (BPMILE * SP.SPPCT) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
																END
													END
													FROM JOELIB/BNASFUELP as SP 
													WHERE SPBAMT < CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
													AND SPEAMT > CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
													AND SNAME = '*DEF')
												END 
										ELSE (SELECT 
											CASE 
												WHEN SP.SPPCT = 0 
													THEN CASE
														WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
															THEN (SELECT (BSMILE * SP.SPFUEL$) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
															ELSE (SELECT (BPMILE * SP.SPFUEL$) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
														END
													ELSE CASE 
														WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
														   THEN (SELECT (BSMILE * SP.SPPCT) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
														   ELSE (SELECT (BPMILE * SP.SPPCT) FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
														END
											END
											FROM JOELIB/BNASFUELP as SP
											WHERE SPBAMT < CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
											AND SPEAMT > CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
											AND SNAME = (SELECT PRFTABL FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE)) 
									END
								ELSE 0
						END as FUEL,
						CASE 
							WHEN (SELECT PRMILEA FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) <> 'PM'
								THEN (SELECT BSMILE FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
								ELSE (SELECT BPMILE FROM XL_RBNALT/BNAMILEP WHERE BOCITY = '$originCity' AND BOST = '$originState' AND BDCITY = '$destinCity' AND BDST = '$destinState' AND BMIL = 'MCV')
						END as MILE,
						RARPM, RANOTE 

						FROM JOELIB/BNARATEP as RATE LEFT JOIN XL_RBNALT/BNAPROFP ON RACODE = RPSCAC WHERE (";
		} else {
			$selString = " SELECT RACODE, RPCNAME, CONTACT, PHONE, BASE, IFNULL(FUEL, 0) as FUELC, (BASE + IFNULL(FUEL, 0)) as TOTAL, MILE, RARPM, RANOTE FROM (

						SELECT DISTINCT 
						RACODE, 
						RPCNAME, 
						(SELECT RCCONT FROM XL_RBNALT/BNACONTP WHERE RCSCAC = RATE.RACODE AND RCDEP = 'DISPATCH/CS' AND RCPHL = 1 ORDER BY RCCONT FETCH FIRST ROW ONLY) as CONTACT, 
						(SELECT RCPHONE FROM XL_RBNALT/BNACONTP WHERE RCSCAC = RATE.RACODE AND RCDEP = 'DISPATCH/CS' AND RCPHL = 1 ORDER BY RCCONT FETCH FIRST ROW ONLY) as PHONE,
						RAMC,
						CASE 
							WHEN RAPTOP <> 0
								THEN RAPTOP
								ELSE($miles * RARPM)			
						END as BASE,
						CASE 
							WHEN RAFINCL <> 'Y' 
								THEN CASE 
									WHEN (SELECT PRFTABL FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE) = ''
										THEN CASE 
											WHEN RAFUEL <> '' 
												THEN (SELECT 
													CASE 
														WHEN SPPCT = 0 
															THEN (SPFUEL$ * $miles) 
															ELSE ((SPPCT + 1) * $miles)
													END 
													FROM JOELIB/BNASFUELP 
													WHERE SPBAMT < CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
														  AND SPEAMT > CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
														  AND SNAME = RATE.RAFUEL) 
												ELSE (SELECT 
													CASE 
														WHEN SPPCT = 0 
															THEN (SPFUEL$ * $miles) 
															ELSE ((SPPCT + 1) * $miles)
													END 
													FROM JOELIB/BNASFUELP 
													WHERE SPBAMT < CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
													AND SPEAMT > CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
													AND SNAME = '*DEF')
												END
										ELSE  (SELECT 
												CASE 
													WHEN SPPCT = 0 
														THEN (SPFUEL$ * $miles) 
														ELSE ((SPPCT + 1) * $miles)
												END 
												FROM JOELIB/BNASFUELP 
												WHERE SPBAMT < CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
												AND SPEAMT > CASE
																	WHEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY) < $date 
																		THEN (SELECT FPFUEL$ FROM QS36F/BNAFUELP ORDER BY FPENDDT DESC FETCH FIRST ROW ONLY)
																		ELSE (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date)
																	END
												AND SNAME = (SELECT PRFTABL FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE))
										END
								ELSE 0
						END as FUEL,
						$miles as MILE,
						RARPM, RANOTE 

						FROM JOELIB/BNARATEP as RATE LEFT JOIN XL_RBNALT/BNAPROFP ON RACODE = RPSCAC WHERE (";

		}
		
		
		$selString = $selString . buildWhereClause($procData);
		$selString = $selString . buildOrderClause();

		if (DEBUG_QUERY || DEBUG_ALL) {
			writeErrors("Query: " . print_r($selString, true));
			
		} 
	}

	function buildWhereClause($procData) {
		global $selString;
		global $PRECS;
		global $miles;

		//Prevent multiple reads to an Array
		$originCity = $procData["FCITY"];
		$destinCity = $procData["TCITY"];
		$originState = $procData["FSTATE"];
		$destinState = $procData["TSTATE"];
		$originZip = $procData["FZIP"];
		$destinZip = $procData["TZIP"];
		$originCountry = $procData["ORIGIN"];
		$destinCountry = $procData["DESTIN"];
		$currentDate = $procData["DATE"];
		$miles = $procData["MILES"];

		//Checks
		$cityState_cityState_check = "(( RAOCITY = '" . $originCity . "' AND RAOST = '" . $originState . "') AND 
									  ( RADCITY = '" . $destinCity . "' AND RADST = '" . $destinState . "') AND 
									  (RATYPE = " . $PRECS["CITY,ST TO CITY,ST"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";


		$zip_zip_check = genZipWhere(strlen($originZip), strlen($destinZip), $originZip, $destinZip);

		$zip_cityState_check = "((RAOFZIP <= '" . substr($originZip, 0, 3) . "' AND RAOTZIP >= '" . substr($originZip, 0, 3) . "') AND
							    ( RADCITY = '" . $destinCity . "' AND RADST = '" . $destinState . "') AND 
							    (RATYPE = " . $PRECS["ZIP(3) TO CITY,ST"] . " OR RATYPE = " . $PRECS["MILEAGE"] . ")) ";

		$cityState_zip_check = "((RADFZIP <= '" . substr($destinZip, 0, 3) . "' AND RADTZIP >= '" . substr($destinZip, 0, 3) . "') AND
							    ( RAOCITY = '" . $originCity . "' AND RAOST = '" . $originState . "') AND 
							    (RATYPE = " . $PRECS["CITY,ST TO ZIP(3)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . ")) ";

		$cityState_state_check = "(RAOCITY = '" . $originCity ."' AND RAOST = '" . $originState . "' AND RADST = '" . $destinState . "' AND RATYPE = 
								 " . $PRECS["CITY,ST TO ST"] . ") ";

		$state_cityState_check = "(RAOST = '" . $originState ."' AND RADCITY = '" . $destinCity . "' AND RADST = '" . $destinState . "' AND RATYPE = 
								 " . $PRECS["ST TO CITY,ST"] . ") ";

		$state_zip5_check = "(RAOST = '" . $originState . "' AND RADFZIP <= '" . substr($destinZip, 0, 5) ."' AND RADTZIP >= '" . 
							  substr($destinZip, 0, 5) ."' AND 
							  (RATYPE = " . $PRECS["ST TO ZIP(6)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . ")) ";

		$state_zip3_check = "(RAOST = '" . $originState . "' AND RADFZIP <= '" . substr($destinZip, 0, 3)."' AND RADTZIP >= '" . 
							  substr($destinZip, 0, 3) ."' AND 
							  (RATYPE = " . $PRECS["ST TO ZIP(3)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . ")) ";

		$zip3_state_check = "(RADST = '" . $destinState . "' AND RAOFZIP <= '" . substr($originZip, 0, 3) ."' AND RAOTZIP >= '" . 
							  substr($originZip, 0, 3) ."' AND 
							  (RATYPE = " . $PRECS["ZIP(3) TO ST"] . " OR RATYPE = " . $PRECS["MILEAGE"] . ")) ";

		$state_state_check = "((RAOST = '" . $originState . "' AND RADST = '" . $destinState . "') AND 
							  (RATYPE = " . $PRECS["ST TO ST"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";	

		$stateZip_stateZip_check = "(RAOST = '" . $originState ."' AND RAOFZIP <= " . substr($originZip, 0, 3) . " AND RAOTZIP >= "
									 . substr($originZip, 0, 3) . " AND RADST = '" . $destinState ."' AND RADFZIP <= " . 
									 substr($destinZip, 0, 3) . " AND RADTZIP >= " . substr($destinZip, 0, 3) . " AND (RATYPE = " 
									 . $PRECS["ST,ZIP(3) TO ST,ZIP(3)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";

		$typeCheck = "";

		if($originCity <> "") {
			$typeCheck = $typeCheck . "C";
		}
		if($originState <> "") {
			$typeCheck = $typeCheck . "S";
		}
		if($originZip <> "") {
			$typeCheck = $typeCheck . "Z";
		}

		$typeCheck = $typeCheck . "-";

		if($destinCity <> "") {
			$typeCheck = $typeCheck . "C";
		}
		if($destinState <> "") {
			$typeCheck = $typeCheck . "S";
		}
		if($destinZip <> "") {
			$typeCheck = $typeCheck . "Z";
		}

		if (DEBUG_OUTPUT || DEBUG_ALL) {
			writeErrors("TypeCheck: " . $typeCheck . " \n");	
		} 

		switch ($typeCheck) {
			case 'CSZ-CSZ':
				if (strlen($destinZip) < 5) {
					$selString = $selString . $zip_zip_check . " OR " . $cityState_cityState_check . " OR " . $zip_cityState_check . " OR " . 
							 $cityState_zip_check . " OR " . $state_zip3_check . " OR " . $zip3_state_check . " OR " . $state_state_check
							  . " OR " . $cityState_state_check . " OR " . $state_cityState_check . " OR " . $stateZip_stateZip_check;	
				} else {
					$selString = $selString . $zip_zip_check . " OR " . $cityState_cityState_check . " OR " . $zip_cityState_check . " OR " . 
							 $cityState_zip_check . " OR " . $state_zip5_check . " OR " . $state_zip3_check . " OR " . $zip3_state_check . " OR " . 
							 $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check . " OR " . $stateZip_stateZip_check;
				}
				break;

			case 'CSZ-CS':
				$selString = $selString . $cityState_cityState_check . " OR " . $zip_cityState_check . " OR " . $zip3_state_check . " OR " . 
							 $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				break;

			case 'CSZ-S':
				$selString = $selString . $zip3_state_check . " OR " . $state_state_check . " OR " . $cityState_state_check;
				break;

			case 'CSZ-Z':
				if (strlen($destinZip) < 5) {
					$selString = $selString . $zip_zip_check . " OR " . $cityState_zip_check . " OR " . $state_zip3_check;
				} else {
					$selString = $selString . $zip_zip_check . " OR " . $cityState_zip_check . " OR " . $state_zip5_check . " OR " . $state_zip3_check;
				}
				break;

			case 'CS-CSZ':
				if($destinZip < 5) {
					$selString = $selString . $cityState_cityState_check . " OR " . $cityState_zip_check . " OR " . 
								 $state_zip3_check . " OR " . $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				} else {
					$selString = $selString . $cityState_cityState_check . " OR " . $cityState_zip_check . " OR " . $state_zip5_check . " OR " . 
								 $state_zip3_check . " OR " . $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				}
				break;

			case 'CS-CS':
				$selString = $selString . $cityState_cityState_check . " OR " . $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				break;

			case 'CS-S':
				$selString = $selString . $state_state_check . " OR " . $cityState_state_check;
				break;

			case 'CS-Z':
				if($destinZip < 5) {
					$selString = $selString . $cityState_zip_check . " OR " . $state_zip3_check;
				} else {
					$selString = $selString . $cityState_zip_check . " OR " . $state_zip5_check . " OR " . $state_zip3_check;
				}
				break;

			case 'S-CSZ':
				if($destinZip < 5) {
					$selString = $selString . $state_zip3_check . " OR " . $state_state_check . " OR " . $state_cityState_check;
				} else {
					$selString = $selString . $state_zip5_check . " OR " . $state_zip3_check . " OR " . $state_state_check . " OR " . $state_cityState_check;
				}
				break;

			case 'S-CS':
				$selString = $selString . $state_state_check . " OR " . $state_cityState_check;
				break;

			case 'S-S':
				$selString = $selString . $state_state_check;
				break;

			case 'S-SZ':
				$selString = $selString . $state_state_check;
				break;

			case 'SZ-S':
				$selString = $selString . $state_state_check;
				break;

			case 'S-Z':
				if ($destinZip < 5) {
					$selString = $selString . $state_zip3_check;	
				} else {
					$selString = $selString . $state_zip5_check . " OR " . $state_zip3_check;
				}
				break;

			case 'Z-CSZ':
				$selString = $selString . $zip_zip_check . " OR " . $zip3_cityState_check . " OR " . $zip3_state_check;
				break;

			case 'Z-CS':
				$selString = $selString . $zip3_cityState_check . " OR " . $zip3_state_check; 
				break;

			case 'Z-S':
				$selString = $selString . $zip3_state_check;
				break;

			case 'Z-Z':
				$selString = $selString . $zip_zip_check;
				break;

			case 'SZ-SZ':
				$selString = $selString . $state_state_check . " OR " . $zip_zip_check . " OR " . $stateZip_stateZip_check;
				break;
			
			default:
				#code ...
				break;
		}

		if($miles == "") {
			if(stripos($typeCheck, "S") >= 0) {
				$miles = getMiles($originCity, $originState, $destinCity, $destinState);
				if($miles <> "FALSE") {
					$selString = $selString . ") AND ((RAFMILE <= " . $miles . " AND RATMILE >= " . $miles . ") OR (RAFMILE = 0 AND RATMILE = 0)) ";
				} else {
					$selString = $selString . ") AND RAFMILE = 0 AND RATMILE = 0 ";
				}
			}
		} else {
			if ($originZip == "" && $destinZip == "") {
				$selString = $selString . ") AND ((RAFMILE <= " . $miles . " AND RATMILE >= " . $miles . " AND RAOFZIP = '' AND RAOTZIP = ''
											AND RADFZIP = '' AND RADTZIP = '') OR (RAFMILE = 0 AND RATMILE = 0)) ";
			}
			$selString = $selString . " AND ((RAFMILE <= " . $miles . " AND RATMILE >= " . $miles . ") OR (RAFMILE = 0 AND RATMILE = 0)) ";
		}

		$selString = $selString . " AND RAFDTE <= " . $currentDate . " AND RAEDTE >= " . $currentDate . " ";

		$selString = $selString . " AND RAOCT = '" . $originCountry . "' AND RADCT = '" . $destinCountry . "'";

		$selString = $selString . " AND RAMCDE = '" . $procData["MODE"] . "'";

	}

	function buildOrderClause() {
		global $selString;

		$selString .= ") as RESULTS ORDER BY PREC";
	}

	function genZipWhere($lenO, $lenD, $originZip, $destinZip) {
		global $PRECS;
		if($lenO == 6) {
			$zipWhere = "(((RAOFZIP <= '" . $originZip ."' AND RAOTZIP >= '" . $originZip ."') OR 
					 	 		   (RAOFZIP <= '" . substr($originZip, 0, 5) . "' AND RAOTZIP >= '" . substr($originZip, 0, 5) . "') OR
					 	 		   (RAOFZIP <= '" . substr($originZip, 0, 3) . "' AND RAOTZIP >= '" . substr($originZip, 0, 3) . "')) AND ";

		} elseif ($lenO == 5) {
			$zipWhere = "(((RAOFZIP <= '" . $originZip ."' AND RAOTZIP >= '" . $originZip ."') OR 
					 	 		   (RAOFZIP <= '" . substr($originZip, 0, 3) . "' AND RAOTZIP >= '" . substr($originZip, 0, 3) . "')) AND ";
		} else {
			$zipWhere = "((RAOFZIP <= '" . $originZip ."' AND RAOTZIP >= '" . $originZip ."') AND ";
		}


		if($lenD == 6) {
			$zipWhere .= "((RADFZIP <= '" . $destinZip ."' AND RADTZIP >= '" . $destinZip ."') OR 
					 	 		   (RADFZIP <= '" . substr($destinZip, 0, 5) . "' AND RADTZIP >= '" . substr($destinZip, 0, 5) . "') OR
					 	 		   (RADFZIP <= '" . substr($destinZip, 0, 3) . "' AND RADTZIP >= '" . substr($destinZip, 0, 3) . "')) AND 
					 	 		   (RATYPE = " . $PRECS["ZIP(6) TO ZIP(6)"] . " OR RATYPE = " . $PRECS["ZIP(6) TO ZIP(3)"] . " OR 
					 	 		   RATYPE = " . $PRECS["ZIP(3) TO ZIP(6)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";

		} elseif ($lenD == 5) {
			$zipWhere .= "((RADFZIP <= '" . $destinZip ."' AND RADTZIP >= '" . $destinZip ."') OR 
					 	 		   (RADFZIP <= '" . substr($destinZip, 0, 3) . "' AND RADTZIP >= '" . substr($destinZip, 0, 3) . "')) AND 
					 	 		   (RATYPE = " . $PRECS["ZIP(6) TO ZIP(6)"] . " OR RATYPE = " . $PRECS["ZIP(6) TO ZIP(3)"] . " OR 
					 	 		   RATYPE = " . $PRECS["ZIP(3) TO ZIP(6)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";
		} else {
			$zipWhere .= "(RADFZIP <= '" . $destinZip ."' AND RADTZIP >= '" . $destinZip ."') AND 
						 (RATYPE = " . $PRECS["ZIP(6) TO ZIP(6)"] . " OR RATYPE = " . $PRECS["ZIP(6) TO ZIP(3)"] . " OR 
					 	  RATYPE = " . $PRECS["ZIP(3) TO ZIP(6)"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";
		}

		return $zipWhere;

	}

	function genView() {
		global $selString;
		global $PRECS;
		global $miles;

		$resultsArray;

		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$db_connection = xl_db2_connect($options);

		if (!$db_connection)
		{
			writeErrors('Could not connect to database: ' . db2_conn_error());
		}

		$query = db2_exec($db_connection, $selString, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db_connection);
			writeErrors('<b>Error ' . db2_stmt_error() . ':' . db2_stmt_errormsg() . '</b>');
		}		

		$response = "<table class='table header-fixed table-condensed'>
				<thead>
					<tr>
						<th class='text-center bg-info col-md-3 name-column'>NAME</th>
						<th class='text-center bg-info contact-column'>CONTACT</th>
						<th class='text-center bg-info normal-column'>TOTAL BASE</th>
						<th class='text-center bg-info normal-column'>FUEL COSTS</th>
						<th class='text-center bg-info normal-column'>TOTAL COST</th>
						<th class='text-center bg-info normal-column'>MILES</th>
						<th class='text-center bg-info normal-column'>RPM</th>
						<th class='text-center bg-info comment-column'>COMMENTS</th>
					</tr>
				</thead>
				<tbody>";

		$rowString = "";
		$prevCarrier;
		$testField = "";
		$nothingFound = true;
		$count = 1;

		while ($row = db2_fetch_assoc($query)) {
			if (DEBUG_OUTPUT || DEBUG_ALL) {
				writeErrors("Query Results: " . print_r($row, true));
			}
			$nothingFound = false;

			$result;
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]), ENT_QUOTES, 'ISO-8859-1');
				$escapedField = xl_fieldEscape($key);

				//Though the if chain is technically unneccessary it makes sure that the fields you are getting are what you are expecting.
				if ($escapedField == "RACODE") {
					$testField = $row[$key];
					$result["CODE"] = $row[$key];
				} elseif( $escapedField == "RPCNAME" ) {
					$result["NAME"] = $row[$key];
				} elseif ($escapedField == "CONTACT") {
					$result["CONTACT"] = $row[$key];
				} elseif ($escapedField == "PHONE") {
					$result["CONTACT"] .= "<br />" . $row[$key];
				} elseif ($escapedField == "BASE" ) {
					$result["BASE"] = ($row[$key] == "") ? "Mileage not found" : $row[$key];
				} elseif ($escapedField == "FUELC") {
					$result["FUEL"] = ($row[$key] == 0) ? "INCLUDED" : $row[$key];
				} elseif ($escapedField == "TOTAL") {
					$result["TOTAL"] = $row[$key];
				} elseif ($escapedField == "MILE") {
					$result["MILES"] = $row[$key];
				} elseif ($escapedField == "RARPM") {
					$result["RPM"] = ($row[$key] == 0) ? "FLAT" : $row[$key];;
				} elseif ($escapedField == "RANOTE") {
					$result["NOTE"] = $row[$key];
				}

				

				if(!array_key_exists($testField, $prevCarrier) && $count == 10) {
					if (DEBUG_ALL || DEBUG_OUTPUT) {
						writeErrors("Testfield: " . $testField . "\nPrevious Carriers: " . print_r($prevCarrier, true) . "\n");	
					}
					$prevCarrier[$testField] = $testField;
					$resultsArray[$result["CODE"]] = $result;	
					$count = 0;
				}

				$count++;

				if (DEBUG_ALL || DEBUG_OUTPUT) {
					writeErrors("Results Added: " . print_r($resultsArray, true) . "\n");	
				}
			}

		}

		usort($resultsArray, "priceSort");

		$count = 0;

		foreach ($resultsArray as $key => $route) {
			$response .= "<tr class='text-center'>";
			$response .= "<td id='Carrier Name_$count' class='viewTD name-column'>{$route['NAME']}<p id='CODE_$count' hidden>{$route['CODE']}</p></td>";
			$response .= "<td id='contact_$count' class='viewTD contact-column'>{$route['CONTACT']}</td>";
			$response .= ($route['BASE'] == "Mileage not found") ? "<td id='base_$count' class='viewTD normal-column'>{$route['BASE']}</td>" : "<td id='base_$count' class='viewTD money normal-column'>{$route['BASE']}</td>";
			$response .= ($route['FUEL'] == "INCLUDED") ? "<td id='fuel_$count' class='viewTD normal-column'>{$route['FUEL']}</td>" : "<td id='fuel_$count' class='viewTD money normal-column'>{$route['FUEL']}</td>";
			$response .= "<td id='total_$count' class='viewTD money normal-column'>{$route['TOTAL']}</td>";
			$response .= "<td id='mile_$count' class='viewTD normal-column'>{$route['MILES']}</td>";
			$response .= ($route['RPM'] == "FLAT") ? "<td id='RPM_$count' class='viewTD normal-column'>{$route['RPM']}</td>" : "<td id='RPM_$count' class='viewTD money normal-column'>{$route['RPM']}</td>";
			$response .= "<td id='comment_$count' class='viewTD comment-column'>{$route['NOTE']}</td></tr>";
		}

		$response .= "</tbody></table></div></section>";

		db2_close($db_connection);
		if(DISPLAY) {
			die($response);
		} else {
			$response = "<br /><br /><br /><h3>The Query system has been disabled. Please contact SYSOP at ext. 351 for further information.</h3>";
			die($response);
		}
	}


	function priceSort($item1, $item2) {
		if ($item1['TOTAL'] == $item2['TOTAL']) return 0;
    	return $item1['TOTAL'] < $item2['TOTAL'] ? -1 : 1;
	}

?>