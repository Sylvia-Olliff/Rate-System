<?php
	
	if (isset($_POST["configData"])) {
		$BASE_PATH = "/www/websmart/htdocs";

		$temp = $_POST["configData"];
		$PATH_ = $BASE_PATH . trim(substr($temp["PATH"], 1));

		put_ini_file($PATH_ . "config.ini", $_POST["configData"]);
	}

	function put_ini_file($file, $array, $i = 0){
  		$str="";
		foreach ($array as $k => $v){
		    if (is_array($v)){
		    	$str.=str_repeat(" ",$i*2)."[$k]".PHP_EOL; 
		      	$str.=put_ini_file("",$v, $i+1);
		    } elseif ($k == "PATH") {
		    	$str.=str_repeat(" ",$i*2)."$v".PHP_EOL; 
		    } else {
		      	$str.=str_repeat(" ",$i*2)."$k = $v".PHP_EOL; 
		    }
		}
		if ($file) {
			$file = trim($file);
		    return file_put_contents($file,$str);
		} else {
		    return $str;
		}
	}

?>