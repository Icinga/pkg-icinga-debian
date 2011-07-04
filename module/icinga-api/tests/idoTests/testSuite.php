<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__)."/dbConfig.php";

abstract class idoTests_testSuite {
	/**
	 * TODO: This is not a nice solution of storing connections.
	 */
	public static $connection = null;
	public static $ref_connection = null;
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('ido');
		$suite->addTestSuite('idoTests_idoConnectionTest');
		$suite->addTestSuite('idoTests_idoCreateSearchTest');
		
		return $suite;
	}
}
