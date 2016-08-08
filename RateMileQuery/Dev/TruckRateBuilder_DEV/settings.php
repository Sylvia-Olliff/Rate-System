<?php
	$settings = parse_ini_file("config.ini");

	//DEBUG Modes
	define('DEBUG_ALL', "{$settings['DEBUG_ALL']}");
	define('DEBUG_FORM', "{$settings['DEBUG_FORM']}");
	define('DEBUG_PROC', "{$settings['DEBUG_PROC']}");
	define('DEBUG_INPUT', "{$settings['DEBUG_INPUT']}");
	define('DEBUG_RESPONSE', "{$settings['DEBUG_RESPONSE']}");
	define('DEBUG_PRECS', "{$settings['DEBUG_PRECS']}");

	define('DISPLAY', "{$settings['DISPLAY']}");
?>