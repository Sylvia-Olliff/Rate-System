<?php
	function writeErrors($errors) {
		$File = "error.log";

		date_default_timezone_set('America/New_York');

		$errors = date(DATE_COOKIE) . " " . $errors;

		file_put_contents($File, $errors . PHP_EOL, FILE_APPEND | LOCK_EX);
	}
?>