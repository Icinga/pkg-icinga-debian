<?php

/**
 *
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiSearchFile
	extends IcingaApiSearch {

	/*
	 * VARIABLES
	 */
	private $inputFiles = array ();
	private $fileContent = false;

	private $queryType = false;

	private $references = array();
	private $lastOffset = array();

	private $sourceOrder = array(
		'command'			=> self::FILE_OBJECTS,
		'contactgroup'		=> self::FILE_OBJECTS,
		'contact'			=> array(self::FILE_RETENTION, self::FILE_OBJECTS),
		'contactstatus'		=> self::FILE_STATUS,
		'hostcomment'		=> array(self::FILE_STATUS, self::FILE_RETENTION),
		'hostdependency'	=> self::FILE_OBJECTS,
		'hostdowntime'		=> array(self::FILE_STATUS, self::FILE_RETENTION),
		'hostescalation'	=> self::FILE_OBJECTS,
		'hostgroup'			=> self::FILE_OBJECTS,
		'host'				=> array(self::FILE_RETENTION, self::FILE_OBJECTS),
		'hoststatus'		=> self::FILE_STATUS,
		'info'				=> array(self::FILE_STATUS, self::FILE_RETENTION),
		'program'			=> self::FILE_RETENTION,
		'programstatus'		=> self::FILE_STATUS,
		'servicecomment'	=> array(self::FILE_STATUS, self::FILE_RETENTION),
		'servicedependency'	=> self::FILE_OBJECTS,
		'servicedowntime'	=> array(self::FILE_STATUS, self::FILE_RETENTION),
		'serviceescalation'	=> self::FILE_OBJECTS,
		'servicegroup'		=> self::FILE_OBJECTS,
		'service'			=> array(self::FILE_RETENTION, self::FILE_OBJECTS),
		'servicestatus'		=> self::FILE_STATUS,
		'timeperiod'		=> self::FILE_OBJECTS,
	);

	private $relationsMap = array (
		'command'			=> array(
			'primary_key'		=> 'command_name',
		),
		'contact'			=> array(
			'primary_key'		=> 'contact_name',
			'has_one'			=> array(
				'host_notification_period'		=> 'timeperiod',
				'service_notification_period'	=> 'timeperiod',
			),
			'has_many'		=> array(
				'host_notification_commands'	=> 'command',
				'service_notification_commands'	=> 'command',
			),
		),
		'contactgroup'		=> array(
			'primary_key'		=> 'contactgroup_name',
			'has_many'			=> array(
				'members'			=> 'contact',
			),
		),
		'contactstatus'		=> array(
			'primary_key'		=> 'contact_name',
			'has_one'			=> array(
				'_PRIMARY_KEY_'					=> 'contact',
				'host_notification_period'		=> 'timeperiod',
				'service_notification_period'	=> 'timeperiod',
			),
		),
		'host'				=> array(
			'primary_key'		=> 'host_name',
			'has_one'			=> array(
				'check_command'			=> 'command',
				'notification_period'	=> 'timeperiod',
			),
			'has_many'			=> array(
				'_PRIMARY_KEY_'		=> 'host',
				'contact_groups'	=> 'contactgroup',
			),
		),
		'hostcomment'		=> array(
			'primary_key'		=> 'comment_id',
			'has_one'			=> array(
				'host_name'			=> 'host',
				'author'			=> 'contact',
			),
		),
		'hostdependency'	=> array(
			'primary_key'		=> 'host_name',
			'has_one'				=> array(
				'dependent_host_name'	=> 'host',
			),
		),
		'hostdowntime'		=> array(
			'primary_key'		=> 'downtime_id',
			'has_one'			=> array(
				'host_name'			=> 'host',
				'author'			=> 'contact',
			),
		),
		'hostescalation'	=> array(
			'primary_key'		=> 'host_name',
			'has_one'			=> array(
				'escalation_period'	=> 'timeperiod',
			),
			'has_many'			=> array(
				'contacts'			=> 'contact',
				'contact_groups'	=> 'contactgroup',
			),
		),
		'hostgroup'		     => array(
			'primary_key'	   => 'hostgroup_name',
			'has_many'		      => array(
				'members'		       => 'host',
	),
	),
		'hoststatus'	    => array(
			'primary_key'	   => 'host_name',
			'has_one'		       => array(
				'_PRIMARY_KEY_'		 => 'host',
				'check_command'		 => 'command',
				'check_period'		  => 'timeperiod',
				'notification_period'   => 'timeperiod',
	),
	),
		'info'			  => array(),
		'program'		       => array(),
		'programstatus'	 => array(),
		'service'		       => array(
			'primary_key'	   => array('host_name', 'service_description',),
			'has_one'		       => array(
				'host_name'			     => 'host',
				'check_period'		  => 'timeperiod',
				'check_command'		 => 'command',
				'notification_period'   => 'timeperiod',
	),
			'has_many'		      => array(
				'contact_groups'	=> 'contactgroup',
	),
	),
		'servicecomment'	=> array(
			'primary_key'	   => 'comment_id',
			'has_one'		       => array(
				'_FOREIGN_KEY_'	 => 'service',
				'author'			=> 'contact',
	),
	),
		'servicedependency'     => array(
			'primary_key'	   => array('host_name', 'service_description',),
			'has_one'		       => array(
				'dependent_host_name'		   => 'host',
				'dependent_service_description' => 'service',
	),
	),
		'servicedowntime'       => array(
			'primary_key'	   => 'downtime_id',
			'has_one'		       => array(
				'_FOREIGN_KEY_'	 => 'service',
				'author'			=> 'contact',
	),
	),
		'serviceescalation'     => array(
			'primary_key'	   => array('host_name', 'service_description',),
			'has_one'		       => array(
				'escalation_period'     => 'timeperiod',
	),
			'has_many'		      => array(
				'contacts'		      => 'contact',
				'contact_groups'	=> 'contactgroup',
	),
	),
		'servicegroup'	  => array(
			'primary_key'	   => 'servicegroup_name',
			'has_many'		      => array(
				'members'		       => 'service',
	),
	),
		'servicestatus'	 => array(
			'primary_key'	   => array('host_name', 'service_description',),
			'has_one'		       => array(
				'_PRIMARY_KEY_'		 => 'service',
				'check_command'		 => 'command',
				'check_period'		  => 'timeperiod',
				'notification_period'   => 'timeperiod',
	),
	),
		'timeperiod'	    => array(
			'primary_key'	   => 'timeperiod_name',
	),
	);

	private $dataRaw = false;
	private $dataRawFilter = array();
	private $dataProcessed = false;

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 *
	 * @param       void
	 * @return      void
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {}

	/**
	 * sets the query type a.k.a. search target (DEPRECATED)
	 * @param       string		  $type			   type of query for further filter action
	 * @return      IcingaApiSearchFile				     IcingaApiSearchFile object
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	public function setQueryType ($type) {
		$this->queryType = $type;
		return $this;
	}

	/**
	 * returns processed data
	 * @return      object							  NOT DEFINED YET
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	public function getData () {
		return $this->dataProcessed;
	}

	/**
	 * sets file config
	 * @param       array		   $files			  associative array defining file locations
	 * @return      IcingaApiSearchFile				     IcingaApiSearchFile object
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	public function setInputFiles ($files) {
		$this->inputFiles = $files;
		return $this;
	}

	/**
	 * reads data from all available input files
	 * @param       void
	 * @return      void
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	private function readInputFiles () {
		foreach ($this->inputFiles as $key => $currentFile) {
			$this->fileContent = file_get_contents($currentFile);
			switch ($key) {
				case self::FILE_OBJECTS:
					$this->convertFileObjectsCache();
					break;
				case self::FILE_STATUS:
					$this->convertFileStatusDat();
					break;
				case self::FILE_RETENTION:
					$this->convertFileRetentionDat();
					break;
				default:
					throw new IcingaApiSearchFileException('readInputFiles(): Unknown file type "' . $key . '" for file "' . $currentFile . '"!');
					break;
			}
		}
	}

	/**
	 * converts data of status into an associative array and stores it
	 * @param       void
	 * @return      void
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	private function convertFileStatusDat () {

		// apostrophes
		$tmp = preg_replace('/\'/s', '\\\'', $this->fileContent);

		// block starting
		$tmp = preg_replace("/(\n)([a-z]+) {/s", '\1$dataRaw[\'\2\'][]=array(', $tmp);

		// block data
		$tmp = preg_replace("/\t([^\n=]+)=([^\n]+)?(\n)/s", '\'\1\'=>\'\2\',\3', $tmp);

		// block ending
		$tmp = preg_replace("/\t}\n/s", ');', $tmp);

		eval("$tmp");
		$this->dataRaw[self::FILE_STATUS] = $dataRaw;

	}

	/**
	 * converts data of objects into an associative array and stores it
	 * @param       void
	 * @return      void
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	private function convertFileObjectsCache () {

		// apostrophes
		$tmp = preg_replace('/\'/s', '\\\'', $this->fileContent);

		// block starting
		$tmp = preg_replace("/(\n)define ([a-z]+) {/s", '\1$dataRaw[\'\2\'][]=array(', $tmp);

		// block data
		$tmp = preg_replace("/\t([^\n\t]+)\t([^\n]+)?(\n)/s", '\'\1\'=>\'\2\',\3', $tmp);

		// block ending
		$tmp = preg_replace("/\t}\n/s", ');', $tmp);

		eval("$tmp");
		$this->dataRaw[self::FILE_OBJECTS] = $dataRaw;

	}

	/**
	 * converts data of retention into an associative array and stores it
	 * @param       void
	 * @return      void
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	private function convertFileRetentionDat () {

		// apostrophes
		$tmp = preg_replace('/\'/s', '\\\'', $this->fileContent);

		// block data
		$tmp = preg_replace("/([^\n=]+)=([^\n]+)?/s", '\'\1\'=>\'\2\',', $tmp);

		// block starting
		$tmp = preg_replace("/(\n)([a-z]+) {/s", '\1$dataRaw[\'\2\'][]=array(', $tmp);

		// block ending
		$tmp = preg_replace("/\n}\n?/s", "\n);\n", $tmp);

		eval("$tmp");
		$this->dataRaw[self::FILE_RETENTION] = $dataRaw;

	}

	/**
	 * applies filters to converted data
	 * @param       void
	 * @return      void
	 * @author      Christian Doebler <christian.doebler@netways.de>
	 */
	private function processRawData () {

var_dump(array(
	'resultColumns'		=> $this->resultColumns,
	'searchFilter'		=> $this->searchFilter,
));

	}

	/**
	 * determines possible result objects by getting intersections of all available objects
	 * @param		void
	 * @return		array							list of possible result objects
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
/*
	private function getProcessingObjects () {

		$objectCounts = array();

		// get all available columns by merging result and filter
		$processingColumns = $this->resultColumns;
		$processingColumnKeys = $this->resultColumnKeys;
		foreach ($this->searchFilter as $filterKey => $filterData) {
			if (!in_array($filterKey, $processingColumnKeys)) {
				array_push($processingColumnKeys, $filterKey);
				array_push($processingColumns, $this->columns->result[$filterKey]);
			}
		}

		// sort column sets by number of available objects
		foreach ($processingColumns as $currentColumn) {
			$currentCount = (is_array($currentColumn[0])) ? count($currentColumn[0]) : 1;
			array_push($objectCounts, $currentCount);
		}
		asort($objectCounts);

		// get result columns
		$countKeys = array_keys($objectCounts);
		$resultObjects = $processingColumns[array_shift($countKeys)];
		foreach ($countKeys as $currentKey) {
			$availableKeys = $processingColumns[$currentKey][0];
			if (!is_array($availableKeys)) {
				$availableKeys = array($availableKeys);
			}
			$intersection = array_intersect($resultObjects, $availableKeys);
//var_dump($intersection);
			if (count($intersection)) {
				$resultObjects = $intersection;
			} else {
var_dump(array(
	$availableKeys,
	$resultObjects
));
				$resultObjects = array_merge($resultObjects, $availableKeys);
var_dump($resultObjects);
			}
		}

		return $resultObjects;

	}
*/

	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearch#fetch()
	 */
	public function fetch () {
		$this->readInputFiles();
		$this->processRawData();
		parent::fetch();
		return $this;
	}


}

// extend exceptions
class IcingaApiSearchFileException extends IcingaApiSearchException {}

?>
