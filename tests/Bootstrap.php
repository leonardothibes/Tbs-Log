<?php

define('LIBRARY_PATH', dirname(__FILE__) . '/../src');
define('STUFF_PATH', dirname(__FILE__) . '/Tbs/.stuff');

set_include_path(implode(PATH_SEPARATOR, array(
	LIBRARY_PATH,
	get_include_path(),
)));

$logfile = LIBRARY_PATH . '/../tmp/logs/tbs_' . date('Y-m-d') . '.log';
\Tbs\Log::getInstance()->setLogger(
	new \Tbs\Log\File($logfile)
);
