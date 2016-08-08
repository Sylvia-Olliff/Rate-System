<?php
	$settings = parse_ini_file("config.ini");

	//DEBUG Modes
	define('DEBUG_ALL', "{$settings['DEBUG_ALL']}");
	define('DEBUG_QUERY', "{$settings['DEBUG_QUERY']}");
	define('DEBUG_OUTPUT', "{$settings['DEBUG_OUTPUT']}");
	define('DEBUG_PRECS', "{$settings['DEBUG_PRECS']}");
	define('DISPLAY', "{$settings['DISPLAY']}");
?>