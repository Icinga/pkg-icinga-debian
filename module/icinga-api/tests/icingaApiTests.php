<?php
require_once 'PHPUnit/Framework.php';

define("ICINGA_API_BASE",dirname(__FILE__)."/../");

class icingaApiTests {

	
	public static function autoload($classname) {
		$filename = preg_replace("/_/","/",$classname).".php";
		if(file_exists($filename))
			require_once($filename);

	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('IcingaApi');
	
		$suite->addTest(idoTests_testSuite::suite());
		return $suite;
	}
	
}

spl_autoload_register(array('icingaApiTests', 'autoload'));
