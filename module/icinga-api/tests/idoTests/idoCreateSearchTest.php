<?php
@include_once(ICINGA_API_BASE."/IcingaApi.php");

class idoTests_idoCreateSearchTest extends PHPUnit_Framework_TestCase {
	static public $connection;
	static public $ref_connection;
	
	public static function setUpBeforeClass() {
		self::$connection = @IcingaApi::getConnection(IcingaApi::CONNECTION_IDO,array(
			"type" => IDO_TEST_TYPE,
			"host" => IDO_TEST_HOST,
			"port" => IDO_TEST_PORT,
			"database" => IDO_TEST_DATABASE, 
			"user" => IDO_TEST_USER,
			"password" => IDO_TEST_PASSWORD,
			"table_prefix" => IDO_TEST_PREFIX
		));
		
		self::$ref_connection = @IcingaApi::getConnection(IcingaApi::CONNECTION_IDO,array(
			"type" => IDO_TEST_REF_TYPE,
			"host" => IDO_TEST_REF_HOST,
			"port" => IDO_TEST_REF_PORT,
			"database" => IDO_TEST_REF_DATABASE, 
			"user" => IDO_TEST_REF_USER,
			"password" => IDO_TEST_REF_PASSWORD,
			"table_prefix" => IDO_TEST_REF_PREFIX
		));
	}
	
	/**
	 * @depends idoTests_idoConnectionTest::testConnectionSetup
	 */
	public function testSearch() {
		$api = self::$connection;
		icingaApiDebugger::getInstance()->setDebugLevel(icingaApiDebugger::DEBUG_LEVEL_ALL);
		$search = $api->createSearch();
		if(!$search)
			$this->fail("Couldn't create search");
	}
	
	/**
	 * @depends testSearch
	 * @dataProvider searchProvider
	 */
	public function testSearchTargets($def) {
		$name = $def["name"];
		$this->target = constant($def["target"]);		
		$this->columns = $def["columns"];
		$this->limit = $def["limit"];
		$this->sort = $def["sort"];
		$this->groups = $def["groups"];
		$this->filters = $def["filters"];
		$this->type = $def["type"];
		
		echo "\n* Testing ".$name. " (Target : ".$this->target.")";
		$test = $this->getTestResult();
		$reference = $this->getReferenceResult();
		if(count($test) != count($reference)) {
			$this->fail("Got ".count($test)." results, should be ".count($reference));
		}
		if(empty($test) && empty($reference))  {
			echo "-->Empty result (skipping) ";
			$this->markTestIncomplete("Got empty result");
		}
		if($def["strict"]) {
			if($diff = $this->getDifferences($test,$reference))  {
				$testResult = print_r($test[$diff[0]][$diff[1]],true);
				if(!isset($reference[$diff[0]][$diff[1]]))
					$diff[1] = strtoupper($diff[1]);
				$referenceResult = print_r($reference[$diff[0]][$diff[1]],true);
				print_r($reference);
				print_r($test);
				$this->fail("Different results at nr ".$diff[0]." key ".$diff[1]." \n'".$testResult."' != '".$referenceResult."'");
			}
		} else {
			if($diff = $this->checkNonStrictSetDifferences($test,$reference))  {
				print_r($reference);
				print_r($test);
				$this->fail("Different results, couldn't find entry ".print_r($diff[1],true)." in reference set");
				
			}
		}

	}
	/**
	 * Strictly checks for differences, i.e. also different sorting.
	 * Returns false if no differences were found, otherwise an array with the entry nr and its field key
	 * @param array $test
	 * @param array $reference
	 * @return array
	 */
	public function getDifferences(array $test,array $reference) {
		foreach($test as $nr=>$testElem) {
			foreach($testElem as $key=>$value) {
				if(!isset($reference[$nr]))
					return array($nr,$key);
				if(!isset($reference[$nr][$key]))
					return array($nr,$key);
				if(@$reference[$nr][$key] != $value)
					return array($nr,$key);
			}
		}
		return false;
	}
	static public function caseInsensitiveKeyCheck($k1,$k2) {
		return(strtolower($k1) != strtolower($k2));
	}
	/**
	 * Non strict check for differences. Just looks if the resultset of ref is the same as the resultset of target
	 * @param array $test
	 * @param array $reference
	 */
	public function checkNonStrictSetDifferences(array $target,array $reference) {
		$alreadyChecked = array();
		foreach($reference as $nr=>$testEntry) {
			$found = false;
			foreach($target as $key=>$refValue) {
				if(isset($alreadyChecked[$key]))
					continue;
				
				$diff =  array_diff_uassoc($testEntry,$refValue,"idoTests_idoCreateSearchTest::caseInsensitiveKeyCheck");
				if(empty($diff)) {
					$found = true;
					$alreadyChecked[$key] = true;
					break;
				}		
			}
			if(!$found)
				return array($nr,$testEntry);
		}
		return false;
	}
	
	public function setupSearch(IcingaApiSearch &$search) {
		$search->setSearchTarget($this->target);
		$search->setResultColumns($this->columns);
		$search->setResultType(IcingaApiSearch::RESULT_ARRAY);
		
		if($this->limit)
			$search->setSearchLimit($this->limit[0],$this->limit[1]);
		
		if($this->sort)	
			foreach($this->sort as $sortfield)	{
				$search->setSearchOrder($sortfield[0],$sortfield[1]);
			}
		
		if($this->groups) {
			foreach($this->groups as $group)
				$search->setSearchGroup($group);	
		}
		
		if($this->filters) {
			foreach($this->filters as $filter)
				$search->setSearchFilter($filter[0],$filter[1],constant($filter[2]));
		}
		
		if($this->type) {
			$search->setSearchType($this->type);
		}
		
	}
	
	public function getTestResult() {
		$api = self::$connection;	
		$search = $api->createSearch();
		$this->setupSearch($search);
		
		try {
			$result = $search->fetch()->getAll();

			return $result;
		} catch(Exception $e) {
			$this->fail($e->getMessage()."\n during test Query:\n".ociPDO::getLastQuery());		
		}
	}
	
	
	public function getReferenceResult() {
		$api = self::$ref_connection;
		$search = $api->createSearch();
		$this->setupSearch($search);

		try {
			$result = $search->fetch()->getAll();
			return $result;
		} catch(Exception $e) {
			$this->fail($e->getMessage()."\n during reference Query:\n".ociPDO::getLastQuery());		
		}
	}

	
	/**
	 * These are the test cases
	 */
	public function searchProvider() {
	   	$testFolder = dirname(__FILE__)."/apiSearchDefinitions";
	   	$tests = scandir($testFolder);
	   	$searches = array();
	   	foreach($tests as $testFile) {
	   		if(substr($testFile,-3) == 'ini')
	   			$searches = array_merge($searches,parse_ini_file($testFolder."/".$testFile,true));
	   	}
	 	foreach($searches as $name=>&$search) {
	 		$search["name"] = $name;
	 		if(!isset($search["limit"]))
	 			$search["limit"] = false;
	 		if(!isset($search["groups"]))
	 			$search["groups"] = false;
	 		if(!isset($search["strict"]))
	 			$search["strict"] = false;
	 		if(!isset($search["type"]))
	 			$search["type"] = false;
	 		else 
	 			$search["type"] = constant($search["type"]);
	 			
	 		if(!isset($search["sort"])) {
	 			$search["sort"] = false;
	 		} else {
	 			foreach($search["sort"] as &$sort)
	 				$sort = explode(",",$sort);
	 		}	
	 		
	 		if(!isset($search["filters"]))
	 			$search["filters"] = false;
	 		else 
	 			foreach($search["filters"] as &$filter) {
	 				$filter = explode(";",$filter);
	 			}
 			$search = array($search);

	 	}
		return $searches;
	}	
}
