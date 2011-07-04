<?php

require_once('objects/IcingaApiConstantsInterface.php');

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApi
	implements IcingaApiConstantsInterface {

	/*
	 * CONSTANTS AND VARIABLES
	 */

	const ObjectsFileSuffix = '.php';

	protected $debug = false;
	private static $objectsFound = false;

	private $searchObject = false;

	protected $icingaType = false;

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 *
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {
	
		return $this;
	}

	/**
	 * dynamically loads classes on demand
	 *
	 * @param	string		$className			name of class to load
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public static function autoload ($className) {

		$classFile = null;

		if (self::$objectsFound === false) {

			// fetch list of available objects and store them in an array for faster processing
			$objectsFound = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__)), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($objectsFound as $classAbsFileName => $fileInfoObject) {
				if ($fileInfoObject->isFile()) {
					$key = $fileInfoObject->getBaseName(self::ObjectsFileSuffix);
					if ($key == $className) {
						$classFile = $classAbsFileName;
					}
					self::$objectsFound[$key] = $classAbsFileName;
				}
			}

		} else {

			// search array of objects for $className
			if (array_key_exists($className, self::$objectsFound)) {
				$classFile = self::$objectsFound[$className];
			}

		}

		if ($classFile !== null && file_exists($classFile)) {
			require_once($classFile);
		}

	}

	/**
	 * sets the icinga type for further generation of class names
	 * @param	string		$type				connection type
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	protected function setIcingaType ($type) {
		$this->icingaType = $type;
	}

	/**
	 * calls initialization method for new connection
	 *
	 * @param	string		$type				type of connection object
	 * @param	mixed		$config				configuration settings of connection object
	 * @return	IcingaApiConnection				connection object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public static function getConnection ($type, $config) {
		$class = 'IcingaApiConnection' . $type;
	
		try {
			$connectionObject = new $class;
			$connectionObject->setConfig($config);
			$connectionObject->setIcingaType($type);
			$connectionObject->connect();
		} catch (Exception $e) {
			throw new IcingaApiException('getConnection failed: ' . $e->getMessage());
		}

		return $connectionObject;
	}

	/**
	 * calls initialization method for new command
	 *
	 * @param	void
	 * @return	IcingaApiCommand				command object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public static function getCommandObject () {
		$commandObject = new IcingaApiCommand();
		return $commandObject;
	}

	/**
	 * calls initialization method for new command dispatcher
	 *
	 * @param	void
	 * @return	IcingaApiCommandDispatcher		command-dispatcher object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public static function getCommandDispatcher () {
		$commandDispatcher = new IcingaApiCommandDispatcher();
		return $commandDispatcher;
	}

	/**
	 * calls initialization method for new command collection
	 *
	 * @param	void
	 * @return	IcingaApiCommandCollection		command-collection object
	 * @author	Marius Hein <marius.hein@netways.de>
	 */
	public static function getCommandCollection() {
		return IcingaApiCommandCollection::getInstance();
	}

	
	/**
	 * sets debugging levels
	 * @param	mixed		$options			debugging options (see constants
	 * @return	IcingaAPI						IcingaAPI object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function debug ($options = false) {
		if ($options !== false) {
			$this->debug = array();
			if (!is_array($options)) {
				$options = array($options);
			}
			foreach ($options as $currentOption) {
				$this->debug[$currentOption] = false;
			}
			if (array_key_exists(self::DEBUG_OVERALL_TIME, $this->debug)) {
				$this->debug[self::DEBUG_OVERALL_TIME] = microtime(true);
			}
		}
		return $this;
	}
}

// extend exceptions
class IcingaApiException extends Exception {
	public function __construct($msg) {
		icingaApiDebugger::logException($msg);
		parent::__construct($msg);
	}
}

// register autoloader
spl_autoload_register(array('IcingaApi', 'autoload'));

?>