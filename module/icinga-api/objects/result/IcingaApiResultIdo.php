<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiResultIdo
	extends IcingaApiResult {

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
	 * sets the search object
	 *
	 * @param	PDOStatement	$object		search object for further processing
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchObject (&$object) {
		$this->searchObject = $object;
 		$this->numResults = $this->searchObject->rowCount();
	}

	/**
	 * sets next result row
	 *
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
 	public function next () {
 		switch ($this->resultType) {
 			case self::RESULT_OBJECT:
				$this->resultRow = $this->searchObject->fetchObject();
				if ($this->resultRow !== false) {
					if ($this->offset === false) {
						$this->offset = 0;
					} else {
						$this->offset++;
					}
				} else {
					$this->offset = false;
				}
				break;
 			case self::RESULT_ARRAY:
 				if ($this->resultArray === false) {
 					$this->resultArray = $this->searchObject->fetchAll(PDO::FETCH_ASSOC);
 					if ($this->resultType == self::RESULT_ARRAY && $this->dbType == 'oci') {
 						$this->resultArray = array_change_key_case($this->resultArray, CASE_LOWER);
 					}
 				}
				if ($this->offset === false) {
					$this->offset = 0;
				} else {
					$this->offset++;
				}
				if ($this->offset >= $this->numResults) {
					$this->offset = false;
				}
				if ($this->offset !== false) {
					$this->resultRow = $this->resultArray[$this->offset];
				} else {
					$this->resultRow = false;
				}
 				break;
 		}
 	}

 	/**
 	 * (non-PHPdoc)
 	 * @see objects/result/IcingaApiResult#rewind()
 	 */
 	public function rewind () {
 		switch ($this->resultType) {
 			case self::RESULT_OBJECT:
 				// TODO: implement rewind for objects
		 		// throw new IcingaApiResultException('rewind() not supported by target type!');
 				break;
 			case self::RESULT_ARRAY:
 				$this->offset = false;
 				$this->next();
 				break;
 		}
 	}

 	/**
 	 * (non-PHPdoc)
 	 * @see objects/result/IcingaApiResult#getAll()
 	 */
	public function getAll () {
		$returnData = false;
 		switch ($this->resultType) {
 			case self::RESULT_OBJECT:
 				// TODO: implement getting complete result set for objects
		 		throw new IcingaApiResultException('getAll() not supported by target type!');
 				break;
 			case self::RESULT_ARRAY:
				$returnData = $this->resultArray;
				break;
 		}
 		return $returnData;
	}

}

?>