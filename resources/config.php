<?php

$config = array(
	'db' => array(
		'name' => 'diary',
		'user' => 'postgres',
		'pass' => 'admin',
		'host' => 'localhost'
	)
);

defined("LIBRARY_PATH") or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));
     
defined("TEMPLATES_PATH") or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));

?>
