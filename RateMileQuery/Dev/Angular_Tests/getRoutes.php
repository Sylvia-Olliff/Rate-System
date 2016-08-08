<?php
	require '/esdi/websmart/v10.6/include/xl_functions001.php';
	require 'viewAccessorial.php';
	require 'getMiles.php';
	require 'errorLogger.php';
	/**
	*  Route Data
	*/
	class RouteData
	{
		public $ACCESSORIAL = "";
		public $CODE = "";
		public $NAME = "";
		public $CONTACT = "";
		public $PHONE = "";
		public $BASE = "";
		public $FUELC = "";
		public $TOTAL = "";
		public $MILE = "";
		public $RPM = "";
		public $NOTE = "";
	}

	$searchParms = json_decode(file_get_contents("php://input"));

	if (!isset($searchParms->data->EFFDATE)) {
		$date = genDate();		
	} else {
		$date = $searchParms->data->EFFDATE;
	}

	// writeErrors("search Parms: " . print_r($searchParms, true));

	$mile = (isset($searchParms->data->MILE)) ? $searchParms->data->MILE : "";
	$originCity = strtoupper((isset($searchParms->data->FCITY)) ? $searchParms->data->FCITY : "");
	$originState = strtoupper((isset($searchParms->data->FSTATE)) ? $searchParms->data->FSTATE : "");
	$originZip = (isset($searchParms->data->FZIP)) ? $searchParms->data->FZIP : "";
	$originCountry = strtoupper((isset($searchParms->data->OCOUNTRY)) ? $searchParms->data->OCOUNTRY : "");
	$destinCity = strtoupper((isset($searchParms->data->TCITY)) ? $searchParms->data->TCITY : "");
	$destinState = strtoupper((isset($searchParms->data->TSTATE)) ? $searchParms->data->TSTATE : "");
	$destinZip = (isset($searchParms->data->TZIP)) ? $searchParms->data->TZIP : "";
	$destinCountry = strtoupper((isset($searchParms->data->DCOUNTRY)) ? $searchParms->data->DCOUNTRY : "");
	$mode = (isset($searchParms->data->MODE)) ? $searchParms->data->MODE : "";

	// writeErrors("miles: " . print_r($mile, true) . " originCity: " . print_r($originCity, true) . " originState: " . print_r($originState, true) . " originZip: " . print_r($originZip, true) . " originCountry: " . print_r($originCountry, true) . " destinCity: " . print_r($destinCity, true) . " destinState: " . print_r($destinState, true) . " destinZip: " . print_r($destinZip, true) . " destinCountry: " . print_r($destinCountry, true) . " mode: " . print_r($mode, true));

	$selString = buildSelect($date, $miles, $originCity, $destinCity, $originState, $destinState, $originZip, $destinZip, $originCountry, $destinCountry, $mode);

	
	// writeErrors($selString);

	echo json_encode(genData($selString));


	function genDate() { //Create ISO Date format number and add it to Data Array

		$month = strftime("%m");

		$day = strftime("%d");
			
		$year = strftime("%Y");

		$currentDate = $year . $month . $day;
		$currentDate = (integer) $currentDate;

		return $currentDate;
	}


	function buildSelect($date, $miles, $originCity, $destinCity, $originState, $destinState, $originZip, $destinZip, $originCountry, $destinCountry, $mode) {

		if ($miles == "") {
			$selString = " SELECT RACODE, RPCNAME, CONTACT, PHONE, BASE, IFNULL(FUEL, 0) as FUELC, (BASE + IFNULL(FUEL, 0)) as TOTAL, MILE, RARPM, RANOTE FROM (

						SELECT DISTINCT 
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
													WHERE SPBAMT < (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
														  AND SPEAMT > (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
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
													WHERE SPBAMT < (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
													AND SPEAMT > (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
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
											WHERE SPBAMT < (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
											AND SPEAMT > (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
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
													WHERE SPBAMT < (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
														  AND SPEAMT > (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
														  AND SNAME = RATE.RAFUEL) 
												ELSE (SELECT 
													CASE 
														WHEN SPPCT = 0 
															THEN (SPFUEL$ * $miles) 
															ELSE ((SPPCT + 1) * $miles)
													END 
													FROM JOELIB/BNASFUELP 
													WHERE SPBAMT < (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
													AND SPEAMT > (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
													AND SNAME = '*DEF')
												END
										ELSE  (SELECT 
												CASE 
													WHEN SPPCT = 0 
														THEN (SPFUEL$ * $miles) 
														ELSE ((SPPCT + 1) * $miles)
												END 
												FROM JOELIB/BNASFUELP 
												WHERE SPBAMT < (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
												AND SPEAMT > (SELECT FPFUEL$ FROM QS36F/BNAFUELP WHERE FPBEGDT <= $date AND FPENDDT >= $date ORDER BY FPBEGDT DESC FETCH FIRST ROW ONLY) 
												AND SNAME = (SELECT PRFTABL FROM XL_RBNALT/BNARAPRP WHERE PRBCDE = RATE.RACODE))
										END
								ELSE 0
						END as FUEL,
						$miles as MILE,
						RARPM, RANOTE 

						FROM JOELIB/BNARATEP as RATE LEFT JOIN XL_RBNALT/BNAPROFP ON RACODE = RPSCAC WHERE (";

		}

		// writeErrors("route data: (in select clause area) " . $originCity . " " . $originState . " " . $originZip . "\n" .  $destinCity . " " . $destinState . " " . $destinZip . " " . $date . " " . $mode . "\n");

		$selString = $selString . buildWhereClause($date, $miles, $originCity, $destinCity, $originState, $destinState, $originZip, $destinZip, $originCountry, $destinCountry, $mode) . buildOrderClause();

		return $selString;
	}


	function buildWhereClause($date, $miles, $originCity, $destinCity, $originState, $destinState, $originZip, $destinZip, $originCountry, $destinCountry, $mode) {
		$whereString = "";

		$PRECS = getPrecs();

		//Checks
		$cityState_cityState_check = "(( RAOCITY = '" . $originCity . "' AND RAOST = '" . $originState . "') AND 
									  ( RADCITY = '" . $destinCity . "' AND RADST = '" . $destinState . "') AND 
									  (RATYPE = " . $PRECS["CITY,ST TO CITY,ST"] . " OR RATYPE = " . $PRECS["MILEAGE"] . "))";


		$zip_zip_check = genZipWhere(strlen($originZip), strlen($destinZip), $originZip, $destinZip, $PRECS);

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

		// writeErrors("route data: " . $originCity . " " . $originState . " " . $originZip . "\n" .  $destinCity . " " . $destinState . " " . $destinZip . "\n");

		// writeErrors("TypeCheck: TEST " . $typeCheck . " \n" . print_r($PRECS, true));

		switch ($typeCheck) {
			case 'CSZ-CSZ':
				if (strlen($destinZip) < 5) {
					$whereString = $whereString . $zip_zip_check . " OR " . $cityState_cityState_check . " OR " . $zip_cityState_check . " OR " . 
							 $cityState_zip_check . " OR " . $state_zip3_check . " OR " . $zip3_state_check . " OR " . $state_state_check
							  . " OR " . $cityState_state_check . " OR " . $state_cityState_check . " OR " . $stateZip_stateZip_check;	
				} else {
					$whereString = $whereString . $zip_zip_check . " OR " . $cityState_cityState_check . " OR " . $zip_cityState_check . " OR " . 
							 $cityState_zip_check . " OR " . $state_zip5_check . " OR " . $state_zip3_check . " OR " . $zip3_state_check . " OR " . 
							 $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check . " OR " . $stateZip_stateZip_check;
				}
				break;

			case 'CSZ-CS':
				$whereString = $whereString . $cityState_cityState_check . " OR " . $zip_cityState_check . " OR " . $zip3_state_check . " OR " . 
							 $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				break;

			case 'CSZ-S':
				$whereString = $whereString . $zip3_state_check . " OR " . $state_state_check . " OR " . $cityState_state_check;
				break;

			case 'CSZ-Z':
				if (strlen($destinZip) < 5) {
					$whereString = $whereString . $zip_zip_check . " OR " . $cityState_zip_check . " OR " . $state_zip3_check;
				} else {
					$whereString = $whereString . $zip_zip_check . " OR " . $cityState_zip_check . " OR " . $state_zip5_check . " OR " . $state_zip3_check;
				}
				break;

			case 'CS-CSZ':
				if($destinZip < 5) {
					$whereString = $whereString . $cityState_cityState_check . " OR " . $cityState_zip_check . " OR " . 
								 $state_zip3_check . " OR " . $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				} else {
					$whereString = $whereString . $cityState_cityState_check . " OR " . $cityState_zip_check . " OR " . $state_zip5_check . " OR " . 
								 $state_zip3_check . " OR " . $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				}
				break;

			case 'CS-CS':
				$whereString = $whereString . $cityState_cityState_check . " OR " . $state_state_check . " OR " . $cityState_state_check . " OR " . $state_cityState_check;
				break;

			case 'CS-S':
				$whereString = $whereString . $state_state_check . " OR " . $cityState_state_check;
				break;

			case 'CS-Z':
				if($destinZip < 5) {
					$whereString = $whereString . $cityState_zip_check . " OR " . $state_zip3_check;
				} else {
					$whereString = $whereString . $cityState_zip_check . " OR " . $state_zip5_check . " OR " . $state_zip3_check;
				}
				break;

			case 'S-CSZ':
				if($destinZip < 5) {
					$whereString = $whereString . $state_zip3_check . " OR " . $state_state_check . " OR " . $state_cityState_check;
				} else {
					$whereString = $whereString . $state_zip5_check . " OR " . $state_zip3_check . " OR " . $state_state_check . " OR " . $state_cityState_check;
				}
				break;

			case 'S-CS':
				$whereString = $whereString . $state_state_check . " OR " . $state_cityState_check;
				break;

			case 'S-S':
				$whereString = $whereString . $state_state_check;
				break;

			case 'S-SZ':
				$whereString = $whereString . $state_state_check;
				break;

			case 'SZ-S':
				$whereString = $whereString . $state_state_check;
				break;

			case 'S-Z':
				if ($destinZip < 5) {
					$whereString = $whereString . $state_zip3_check;	
				} else {
					$whereString = $whereString . $state_zip5_check . " OR " . $state_zip3_check;
				}
				break;

			case 'Z-CSZ':
				$whereString = $whereString . $zip_zip_check . " OR " . $zip3_cityState_check . " OR " . $zip3_state_check;
				break;

			case 'Z-CS':
				$whereString = $whereString . $zip3_cityState_check . " OR " . $zip3_state_check; 
				break;

			case 'Z-S':
				$whereString = $whereString . $zip3_state_check;
				break;

			case 'Z-Z':
				$whereString = $whereString . $zip_zip_check;
				break;

			case 'SZ-SZ':
				$whereString = $whereString . $state_state_check . " OR " . $zip_zip_check . " OR " . $stateZip_stateZip_check;
				break;
			
			default:
				#code ...
				break;
		}

		if($miles == "") {
			if(stripos($typeCheck, "S") >= 0) {
				$miles = getMiles($originCity, $originState, $destinCity, $destinState);
				if($miles <> "FALSE") {
					$whereString = $whereString . ") AND ((RAFMILE <= " . $miles . " AND RATMILE >= " . $miles . ") OR (RAFMILE = 0 AND RATMILE = 0)) ";
				} else {
					$whereString = $whereString . ") AND RAFMILE = 0 AND RATMILE = 0 ";
				}
			}
		} else {
			if ($originZip == "" && $destinZip == "") {
				$whereString = $whereString . ") AND ((RAFMILE <= " . $miles . " AND RATMILE >= " . $miles . " AND RAOFZIP = '' AND RAOTZIP = ''
											AND RADFZIP = '' AND RADTZIP = '') OR (RAFMILE = 0 AND RATMILE = 0)) ";
			}
			$whereString = $whereString . " AND ((RAFMILE <= " . $miles . " AND RATMILE >= " . $miles . ") OR (RAFMILE = 0 AND RATMILE = 0)) ";
		}

		$whereString = $whereString . " AND RAFDTE <= " . $date . " AND RAEDTE >= " . $date . " ";

		$whereString = $whereString . " AND RAOCT = '" . $originCountry . "' AND RADCT = '" . $destinCountry . "'";

		$whereString = $whereString . " AND RAMCDE = '" . $mode . "'";

		return $whereString;

	}

	function buildOrderClause() {
		return ") as RESULTS ORDER BY TOTAL";
	}

	function getPrecs() {
		$selString = "SELECT PRTYPE, PRDESC FROM JOELIB/BNAPRECP";

		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$db_connection = xl_db2_connect($options);

		if (!$db_connection)
		{
			$errors = " Could not connect to the database for PRECS. " . db2_conn_error() . "\n";
			writeErrors($errors);
		}

		$query = db2_exec($db_connection, $selString, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			db2_close($db_connection);
			$errors = "Error ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "\n";
			writeErrors($errors);
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

		return $PRECS;
	}

	function genZipWhere($lenO, $lenD, $originZip, $destinZip, $PRECS) {
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

	function genData($selString) {
		$route = new RouteData();
		$routes = array();
		$prevCarrier;

		$options = array('i5_naming' => DB2_I5_NAMING_ON);

		$db_connection = xl_db2_connect($options);

		if (!$db_connection)
		{
			$errors = " Could not connect to the database for PRECS. " . db2_conn_error() . "\n";
			writeErrors($errors);
			die();
		}

		$query = db2_exec($db_connection, $selString, array('CURSOR' => DB2_SCROLLABLE));
		if(!$query) {
			$errors = "Error ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "\n";
			writeErrors($errors);
			db2_close($db_connection);
			die();
		}

		while ($row = db2_fetch_assoc($query)) {
			$nothingFound = false;
			
			foreach (array_keys($row) as $key) {
				$row[$key] = htmlspecialchars(rtrim($row[$key]),ENT_QUOTES,'ISO-8859-1');
				$escapedField = xl_fieldEscape($key);

				if ($escapedField == "RACODE") {
					$testField = $row[$key];
					$route->CODE = $row[$key];
				} elseif ($escapedField == "RPCNAME") {
					$route->NAME = $row[$key];
				} elseif ($escapedField == "CONTACT") {
					$route->CONTACT = $row[$key];
				} elseif ($escapedField == "PHONE") {
					$route->PHONE = $row[$key];
				} elseif ($escapedField == "BASE") {
					$route->BASE = $row[$key];
				} elseif ($escapedField == "FUELC") {
					$route->FUELC = ($row[$key] == 0) ? "INCLUDED" : $row[$key];
				} elseif ($escapedField == "TOTAL") {
					$route->TOTAL = $row[$key];
				} elseif ($escapedField == "MILE") {
					$route->MILE = $row[$key];
				} elseif ($escapedField == "RARPM") {
					$route->RPM = $row[$key];
				} elseif ($escapedField == "RANOTE") {
					$route->NOTE = $row[$key];
				}
								
			}
			
			
			if(!array_key_exists($testField, $prevCarrier)) {
				$prevCarrier[$testField] = $testField;
				array_push($routes, $route);
				$route = new RouteData();
			} 
		}

		if($nothingFound) {
			$route = new RouteData();
			$route->NAME = "No Results";
			array_push($routes, $route);
		}

		// writeErrors("This should be returned \n" . print_r($routes, true));

		db2_close($db_connection);
		return $routes;
	}



?>