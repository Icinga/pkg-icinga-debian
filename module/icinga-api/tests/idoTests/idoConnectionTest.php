<?php

class idoTests_idoConnectionTest extends PHPUnit_Framework_TestCase{
	public function testApiAvaibility() {
		require_once(ICINGA_API_BASE."/IcingaApi.php");
		if(!class_exists('IcingaApi'))
			$this->fail("Could not find icinga api");	
	}
	/**
	 * @depends testApiAvaibility
	 */ 
	public function testConnectionSetup() {
		echo "\n* Testing database connection.";
		$conn = IcingaApi::getConnection(IcingaApi::CONNECTION_IDO,array(
			"type" => IDO_TEST_TYPE,
			"host" => IDO_TEST_HOST,
			"port" => IDO_TEST_PORT,
			"database" => IDO_TEST_DATABASE, 
			"user" => IDO_TEST_USER,
			"password" => IDO_TEST_PASSWORD,
			"table_prefix" => IDO_TEST_PREFIX
		));
		if(!$conn)
			$this->fail("Couldn't connect to test db");
		idoTests_testSuite::$connection = $conn;
	}	
	/**
	 * @depends testConnectionSetup
	 */ 
	public function testReferenceConnectionSetup() {
		echo "\n* Testing reference database connection.";
		$conn = IcingaApi::getConnection(IcingaApi::CONNECTION_IDO,array(
			"type" => IDO_TEST_REF_TYPE,
			"host" => IDO_TEST_REF_HOST,
			"port" => IDO_TEST_REF_PORT,
			"database" => IDO_TEST_REF_DATABASE, 
			"user" => IDO_TEST_REF_USER,
			"password" => IDO_TEST_REF_PASSWORD,
			"table_prefix" => IDO_TEST_REF_PREFIX
		));
		if(!$conn)
			$this->fail("Couldn't connect to reference db");
		idoTests_testSuite::$ref_connection = $conn;
	}	
}