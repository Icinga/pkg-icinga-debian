<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiConnectionIdo
	extends IcingaApiConnectionDatabase {
	public $type = 'Ido';
	/*
	 * VARIABLES
	 */

	

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
	 * returns type of database which was set via config array
	 * @param	void
	 * @return	string						type of database
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getDbType () {
		$dbType = false;
		if (array_key_exists('type', $this->config)) {
			$type = $this->config['type'];
		} else {
			throw new IcingaApiConnectionIdoException('getDbType(): no db type set!');
		}
		return $type;
	}

	/**
	 * calls initialization method for new search
	 *
	 * @param	void
	 * @return	IcingaApiSearchIdo				search object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function createSearch () {
		$class = 'IcingaApiSearch' . $this->icingaType;
		$searchObject = new $class;
		$searchObject->setDebug($this->debug);
		$searchObject->loadInterfaceClass($this->icingaType, $this->config['type']);
		$searchObject->setConnectionObject($this);
		$searchObject->setTablePrefix($this->config['table_prefix']);
		if (array_key_exists('contact_source', $this->config)) {
			$searchObject->setContactSource($this->config['contact_source']);
		}
		if (count($this->searchFilterDefault)) {
			$searchObject->setSearchFilter($this->searchFilterDefault);
		}
		return $searchObject;
	}

}

// extend exceptions
class IcingaApiConnectionIdoException extends IcingaApiConnectionException {}

?>