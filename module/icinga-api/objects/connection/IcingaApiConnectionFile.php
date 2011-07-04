<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiConnectionFile extends IcingaApiConnection {

	/*
	 * VARIABLES
	 */

	protected $inputFiles = array();
	private $inputFilesCheck = array('status.dat', 'objects.cache', 'retention.dat');

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
	public function __construct () {}

	/**
	 * checks whether all defined files are accessible
	 *
	 * @param	array		$config				associative array storing configuration
	 * @return	boolean							true if configuration is OK, false on error(s)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkConfig (array $config) {

		$configOk = true;

		// loop through array of input files, check availability and add them to list of available ones
		foreach ($this->inputFilesCheck as $currentFile) {

			if (array_key_exists($currentFile, $config)) {
				if (!file_exists($config[$currentFile]) ) {
					throw new IcingaApiConnectionFileException('Configuration error: ' . $currentFile . ' not found!');
					$configOk = false;
				} elseif (!is_readable($config[$currentFile])) {
					throw new IcingaApiConnectionFileException('Configuration error: ' . $currentFile . ' not readable!');
					$configOk = false;
				} else {
					$this->inputFiles[$currentFile] = $config[$currentFile];
				}
			}

		}

		// perform global checks
		$configOkParent = parent::checkConfig($config);
		if (!$configOkParent && $configOk) {
			$configOk = false;
		}

		return $configOk;

	}

	/**
	 * triggers config check and puts config into right place
	 * @param	array		$config				associative array storing configuration
	 * @return	boolean							true on success otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConfig (array $config) {
		if (!$this->checkConfig($config)) {
			return false;
		}
		$this->config = $config;
		return $this;
	}

	/**
	 * right now just a dummy
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function connect () {
		return $this;
	}

	/**
	 * calls initialization method for new search
	 *
	 * @param	void
	 * @return	IcingaApiSearchFile				search object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function createSearch () {
		$class = 'IcingaApiSearch' . $this->icingaType;
		$searchObject = new $class;
		$searchObject->setDebug($this->debug);
		$searchObject->setInputFiles($this->config);
		$searchObject->loadColumns($this->icingaType);
		return $searchObject;
	}

}

// extend exceptions
class IcingaApiConnectionFileException extends IcingaApiConnectionException {}

?>