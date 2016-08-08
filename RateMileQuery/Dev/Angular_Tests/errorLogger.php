<?php
	function writeErrors($errors) {
		$File = "error_log.txt";
		file_put_contents($File, $errors . PHP_EOL, FILE_APPEND | LOCK_EX);
	}
?>