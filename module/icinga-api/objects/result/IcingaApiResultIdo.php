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
	protected $substitutedColumns = array();
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

	public function setSubstitutedColumns(array $sub = array()) {
		$this->substitutedColumns = $sub;
	}
	/**
	 * Rename masked columns (i.e. columns that were originally longer than 31 chars)
	 * to their original name
	 * @param unknown_type $resultSet
	 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
	 */
	public function rebuildColumnNames($resultSet) {

		if(is_array($resultSet)) {
			$rebuildResultSet = array();
			foreach($resultSet as $column=>$value) {
				$column  =strtoupper($column);
				if(isset($this->substitutedColumns[$column]))
					$column = $this->substitutedColumns[$column];
				$rebuildResultSet[$column] = $value;
			}
			return $rebuildResultSet;
		} else if (is_object($resultSet)) {
			foreach($this->substitutedColumns as $val=>$orig) {
				if(isset($resultSet->{$val}))
					$resultSet->{$orig} = $resultSet->{$val};
			}

			return $resultSet;
		} 

		return $resultSet;	
	}
	
	/**
	 * sets the search object
	 *
	 * @param	PDOStatement	$object		search object for further processing
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchObject (&$object) {
		$this->searchObject = $object;
 		 if(!$this->numResults)
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
				$this->resultRow = $this->rebuildColumnNames($this->resultRow);
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
 					
 					foreach($this->resultArray as &$result) {
 						$result = $this->rebuildColumnNames($result);
 					}
 					if ($this->resultType == self::RESULT_ARRAY  && $this->dbType == 'oci8') {
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
 		if($this->resultRow)
	 		foreach($this->resultRow as $val=>$entry) {
	 		 	if(is_object($this->resultRow))
	 		 		$this->resultRow->{strtoupper($val)} = $entry;
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