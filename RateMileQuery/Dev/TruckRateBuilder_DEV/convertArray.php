<?php
	function convert($data) {
		$alternate = 0;
		$tempKey = "";
		$tempValue = "";

		//The RAW Array contains an Array of Arrays that each contain two key value pairs. Name = Key and 
		//Value = Value, use print_r to view the RAW contents for example.
		foreach ($data as $key => $value) {
			foreach ($value as $key2 => $value2) {
				if ($alternate == 0) {
					$tempKey = $value2;
					$alternate = 1;
				} elseif ($alternate == 1) {
					$tempValue = $value2;
					$alternate = 0;
				}
			}
			$arrayKV[$tempKey] = $tempValue;
		}

		return $arrayKV;
	}
?>