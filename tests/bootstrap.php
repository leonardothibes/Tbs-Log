<?php

define('LIBRARY_PATH', dirname(__FILE__) . '/../src');
define('LOGS_PATH', dirname(__FILE__) . '/../logs');
define('STUFF_PATH', dirname(__FILE__) . '/Tbs/.stuff');

set_include_path(implode(PATH_SEPARATOR, array(
	LIBRARY_PATH,
	//get_include_path(),
)));

require_once LIBRARY_PATH . '/../vendor/autoload.php';

$logfile = LOGS_PATH . '/debug_' . date('Y-m-d') . '.log';
\Tbs\Log::getInstance()->setLogger(
	new \Tbs\Log\File($logfile)
);
