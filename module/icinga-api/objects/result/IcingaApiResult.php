<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
abstract class IcingaApiResult
	extends IcingaApi
	implements Iterator, IcingaApiResultInterface {

	/*
	 * VARIABLES
	 */

	protected $searchObject = false;
	protected $resultType = self::RESULT_OBJECT;
	protected $resultArray = false;
	protected $resultRow = false;
	protected $numResults = false;
	protected $offset = false;

	protected $dbType = false;

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
	 * (non-PHPdoc)
	 * @see objects/result/IcingaApiResultInterface#__get()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __get ($name) {
		$returnValue = false;
		switch ($this->resultType) {
			case self::RESULT_OBJECT:
				if ($this->resultRow->{$name} === null) {
					throw new IcingaApiResultException('Search field "' . $name . '" not available!');
				} else {
					$returnValue = $this->resultRow->{$name};
				}
				break;
			case self::RESULT_ARRAY:
				if ($this->resultRow !== false) {
					if (!array_key_exists($name, $this->resultRow)) {
						throw new IcingaApiResultException('Search field "' . $name . '" not available!');
					} else {
						$returnValue = $this->resultRow[$name];
					}
				}
				break;
		}
		return $returnValue;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/result/IcingaApiResultInterface#__call()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __call ($name, $arguments = array()) {
		return $this->__get($name);
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/result/IcingaApiResultInterface#get()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function get ($searchField = false) {

		$returnData = false;

		if ($searchField === false) {
			throw new IcingaApiResultException('get(): No search field defined!');
			return false;
		}

		if ($this->resultRow !== false) {
			$returnData = $this->__get($searchField);
		}

		return $returnData;

	}

	/**
	 * returns complete result set
	 * @param	void
	 * @return	mixed					complete result set
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	abstract public function getAll ();

	/**
	 * (non-PHPdoc)
	 * @see objects/result/IcingaApiResultInterface#get()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getRow () {
		return $this->resultRow;
	}

	/**
	 * sets the search object
	 *
	 * @param	mixed		$object		search object for further processing
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	abstract public function setSearchObject (&$object);

	/**
	 * (non-PHPdoc)
	 * @see objects/result/IcingaApiResultInterface#setResultType()
	 */
	public function setResultType ($type) {
		$this->resultType = $type;
	}

	/**
	 * returns current result object
	 *
	 * @param	void
	 * @return	mixed
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function current () {
 		return $this;
 	}

	/**
	 * returns current offset
	 *
	 * @param	void
	 * @return	scalar
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
 	public function key () {
 		return $this->offset;
 	}

	/**
	 * checks whether there's a result row left
	 *
	 * @param	void
	 * @return	boolean
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
 	public function valid () {
 		return ($this->offset !== false) ? true : false;
 	}

 	/**
 	 * (non-PHPdoc)
 	 * @see objects/result/IcingaApiResultInterface#getResultCount()
 	 */
 	public function getResultCount () {
 		return $this->numResults;
 	}

 	/**
 	 * sets the database type (mysql, oci, etc.)
 	 * @param	string		$type		type of database
 	 * @return	void
 	 * @author	Christian Doebler <christian.doebler@netways.de>
 	 */
 	public function setDbType ($type) {
 		$this->dbType = $type;
 	}
 
}

// extend exceptions
class IcingaApiResultException extends IcingaApiException {}

?>