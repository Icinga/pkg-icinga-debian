<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiResultLivestatus
	extends IcingaApiResult {

	/*
	 * VARIABLES
	 */

	private $searchColumns = array();
	private $lastRowCombined = false;
	private $lastOffset = false;

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
	 * sets search-column names
	 *
	 * @param	array		$columns			list of column names
	 * @return	IcingaApiResultLivestatus		this object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchColumns (array $columns) {
		$this->searchColumns = $columns;
 		return $this;
	}

	/**
	 * sets search result
	 *
	 * @param	array		$data				result of query
	 * @return	IcingaApiResultLivestatus		this object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchResult (array $data) {
		$this->resultArray = $data;
		$this->numResults = count($data);
		$this->lastOffset = $this->numResults - 1;
		return $this;
	}

	/**
	 * sets the search object
	 *
	 * @param	PDOStatement	$object			search object for further processing
	 * @return	IcingaApiResultLivestatus		this object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchObject (&$object) {
		$this->searchObject = $object;
 		return $this;
	}

	/**
	 * sets next row of result array
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function setNextResultArrayRow () {
		if ($this->offset < $this->lastOffset || $this->offset === false) {

			// set row offset
			if ($this->offset === false) {
				$this->offset = 0;
			} else {
				$this->offset++;
			}

			if ($this->lastRowCombined !== false) {

				if ($this->offset > $this->lastRowCombined) {

					// current row not converted to associative array -> convert
					$this->resultArray[$this->offset] = array_combine(
						$this->searchColumns,
						$this->resultArray[$this->offset]
					);
					$this->lastRowCombined = $this->offset;

				}

			} else {

				// first row -> convert to associative array
				$this->resultArray[$this->offset] = array_combine(
					$this->searchColumns,
					$this->resultArray[$this->offset]
				);
				$this->lastRowCombined = 0;

			}

			// set row
			$this->resultRow = $this->resultArray[$this->offset];

		} else {

			// no rows left -> reset
			$this->offset = false;
			$this->resultRow = false;

		}
	}

	/**
	 * sets next row of result object
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function setNextResultObjectRow () {
		$this->setNextResultArrayRow();

		if ($this->resultRow !== false) {
			if (is_array($this->resultRow)) {
				// convert array into object
				$rowStr = var_export($this->resultRow, true);
				$rowArr = explode("\n", $rowStr);
				array_pop($rowArr);
				array_shift($rowArr);
				$rowStr = implode("\n", $rowArr);

				$rowStr = preg_replace('/^[^A-Z]+\'([A-Z_]+)\' => ([^\n]+)/m', 'public \$$1 = $2', $rowStr);
				$rowStr = preg_replace('/,\n|,$/m', ';', $rowStr);

				$className = 'ResultRow' . $this->offset;
				$rowStr = 'class ' . $className . ' {' . $rowStr . '} $resultRow = new ' . $className . '();';
				eval($rowStr);

				$this->resultArray[$this->offset] = $resultRow;
			}

			$this->resultRow = $this->resultArray[$this->offset];
		}
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
				$this->setNextResultObjectRow();
				break;
 			case self::RESULT_ARRAY:
 				$this->setNextResultArrayRow();
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