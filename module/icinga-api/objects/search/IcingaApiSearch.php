<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
abstract class IcingaApiSearch
	extends IcingaApi
	implements IcingaApiSearchInterface {

	/*
	 * VARIABLES
	 */

	protected $debug = false;

	public $connectionObject = false;
	protected $resultType = false;
	protected $resultColumnKeys = array();
	protected $resultColumns = array();
	protected $resultColumnsNoAliases = array();
	protected $columnsProcessed = array();
	protected $joinTables = array();
	protected $searchTarget = false;
	protected $searchType = false;
	protected $searchFilter = null;
	protected $searchFilterAppend = array();
	protected $searchGroup = array();
	protected $searchOrder = array();
	protected $searchOrderColumns = array();
	protected $searchLimit = false;
	protected $ifSettings = false;
	protected $icingaType = null;
	protected $columns = false;

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 *
	 * @param	void
	 * @return	IcingaApiSearch					instance of IcingaApiSearch object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {}

	/**
	 * sets debugging
	 * @param	integer				$debug			debugging options
	 * @param	array				$debugData		debugging data
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setDebug ($debug = false) {
		if ($debug !== false) {
			$this->debug = $debug;
			$this->debugData = $debugData;
		}
	}
	
	/**
	 * returns whether debugging is enabled
	 * @return boolean
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getDebug() {
		return $this->debug;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setConnectionObject()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConnectionObject (IcingaApiConnectionInterface &$object) {
		$this->connectionObject = $object;
	}

	/**
	 * returns the connection object 
	 * @return IcingaApiConnectionInterface
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getConnectionObject() {
		return $this->connectionObject;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setResultType()
	 */
	public function setResultType ($type) {
		switch ($type) {
			case self::RESULT_OBJECT:
			case self::RESULT_ARRAY:
				$this->resultType = $type;
				break;
			default:
				throw new IcingaApiSearchException('setResultType(): unknown result type "' . $type . '"!');
				$error = true;
				break;
		}
		return $this;
	}

	/**
	 * Returns the current result type or false if none set
	 * @return string
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getResultType() {
		return $this->resultType;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchType()
	 */
	public function setSearchType ($type) {
		switch ($type) {
			case self::SEARCH_TYPE_COUNT:
				$this->searchType = $type;
				break;
			default:
				throw new IcingaApiSearchException('setSearchType(): unknown result type "' . $type . '"!');
				$error = true;
				break;
		}
		return $this;
	}
	
	/**
	 * returns the current search type or false if none is set
	 * @return string
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getSearchType() {
		return $this->searchType;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchTarget()
	 */
	public function setSearchTarget ($target) {
		if (empty($target)) {
			throw new IcingaApiSearchException('setTarget(): unknown target!');
		} else {
			$this->searchTarget = $target;
		}
		return $this;
	}

	/**
	 * returns the current search target or false if none is set
	 * @return string
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getSearchTarget() {
		return $this->searchTarget;
	}
	
	/**
	 * You should now use createFilter and createFilterGroup and use them as the filter parameter
	 * Using $value and $defaultMatch is @deprecated
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchFilter()
	 */
	public function setSearchFilter ($filter, $value = false, $defaultMatch = IcingaApi::MATCH_EXACT) {
		if(!$this->searchFilter)
			$this->searchFilter = IcingaApiSearchFilterGroup::createInstance($this);
		
		if($filter instanceof IcingaApiSearchFilterInterface) {
			$this->searchFilter->addFilter($filter);

		} else if (!is_array($filter) && $value === false) {
			throw new IcingaApiSearchException('setSearchFilter(): invalid definition of key-value pair(s)!');
		
		} else { // support the previous behaviour of the API and wrap it with filtergroups
			// convert filter into array
			if (!is_array($filter)) {
				$filter = array(array($filter, $value, $defaultMatch));
			} else {
				if(isset($filter["val"])) {
					$this->reIndexFilter($filter);
				}
			}
			
			// loop through array and apply filters
			foreach ($filter as $filterData) {
				// check length
				$filterDataCount = count($filterData);
				if ($filterDataCount < 1 || $filterDataCount > 3) {
					throw new IcingaApiSearchException('setSearchFilter(): invalid definition of key-value pair(s)!');
				}
				// set default match type
				if ($filterDataCount == 2) {
					$filterData[2] = $defaultMatch;
				}
				
				$matchType = $filterData[2];

				// add values to filter
				$filtersForGroup = $this->createFilterGroup();
				$filtersForGroup->setType(IcingaApi::SEARCH_OR);
				if (!is_array($filterData[1])) {
					$filterData[1] = array($filterData[1]);
				}
				foreach ($filterData[1] as $filterValue) {
					icingaApiDebugger::logDebug("Adding filter ".$filterData[0]." ".$filterData[2]." ".$filterValue);
					$filtersForGroup->addFilter($this->createFilter($filterData[0],$filterValue,$filterData[2]));
				}
				$this->searchFilter->addFilter($filtersForGroup);
			}
		}

		return $this;
	}

	protected function reIndexFilter(&$filter) {
		$filter[1] = $filter["val"];
		$filter[0] = $filter["field"];
		$filter[2] = $filter["op"];
		unset($filter["val"]);
		unset($filter["field"]);
		unset($filter["op"]);
		$filter = array($filter);
	}

	public function createFilterGroup($type = null) {
		$filterGroup = IcingaApiSearchFilterGroup::createInstance($this,$type);
		return $filterGroup;
	}

	public function createFilter($field = null,$value = null,$match = null) {
		$filter = IcingaApiSearchFilter::createInstance($this,$field,$value,$match);
		return $filter;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchFilterAppend($statement, $searchAggregator)
	 */
	public function setSearchFilterAppendix ($statement, $searchAggregator = self::SEARCH_AND) {
		if ($searchAggregator == self::SEARCH_AND || $searchAggregator == self::SEARCH_OR) {
			$searchFilter = ' ' . $searchAggregator . ' (' . $statement . ')';
			array_push($this->searchFilterAppend, $searchFilter);
		} else {
			throw new IcingaApiSearchException('setSearchFilterAppendix(): unknown search aggregator "' . $searchAggregator . '"!');
		}
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchGroup()
	 */
	public function setSearchGroup ($columns) {
		if (!is_array($columns)) {
			$columns = array($columns);
		}
		foreach ($columns as $currentColumn) {
			if (($processedColumn = $this->getColumn($currentColumn)) !== false) {
				array_push($this->searchGroup, $processedColumn);
			}
		}
		return $this;
	}

	/**
	 * returns an array of the columns the result be grouped with
	 * @return array
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getSearchGroup() {
		return $this->searchGroup;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchOrder()
	 */
	public function setSearchOrder ($column, $direction = 'asc') {
		
		if (!is_array($column)) {
			$column = array($column);
		}
		foreach ($column as $currentColumn) {
			if (($processedColumn = $this->getColumn($currentColumn)) !== false) {
				$processedColumn .= ' ' . $direction;
				array_push($this->searchOrder, $processedColumn);
				array_push($this->searchOrderColumns,$currentColumn);
			}
		}
		return $this;
	}

	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setSearchLimit()
	 */
	public function setSearchLimit ($start, $length = false) {
		if ($this->searchLimit === false) {
			$this->searchLimit = array();
		}

		array_push($this->searchLimit, (int)$start);
		if ($length !== false) {
			array_push($this->searchLimit, (int)$length);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setContactSource()
	 */
	public function setContactSource ($source) {
		switch ($source) {
			case self::CONTACT_SOURCE_PHP_AUTH_USER:
				if (empty($_SERVER[$source])) {
					throw new IcingaApiSearchException('Empty contact name!');
				} else {
					$this->setContact($_SERVER[$source]);
				} 
				break;
			default:
				throw new IcingaApiSearchException();
				break;
		}
	}

	
	
	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#setContact()
	 */
	public function setContact ($contact) {
		if (!is_string($contact)) {
			throw new IcingaApiSearchException('setContact(): Invalid type for contact!');
		} else {
			$this->setSearchFilter($this->ifSettings->columns['CONTACT_NAME'], $contact);
		}
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearchInterface#checkSearch()
	 */
	public function searchValid () {

		$valid = true;

		if ($this->searchTarget === false) {
			throw new IcingaApiSearchException('No search target defined!');
			$valid = false;
		}

		if ($valid && array_key_exists($this->searchTarget, $this->ifSettings->queryMap)) {

			$query = $this->ifSettings->queryMap[$this->searchTarget];

			if ($this->resultColumns === false && strpos($query, '${FIELDS:') === false) {
				throw new IcingaApiSearchException('No result column defined!');
				$valid = false;
			}

		}

		return $valid;

	}

	/**
	 * loads additional database/ interface settings (base queries, columns, etc.)
	 *
	 * @params	void
	 * @return	boolean								true on success, otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function loadInterfaceClass ($sourceType, $dbType = false) {
		$success = false;

		if ($dbType !== false) {
			$classSuffix = ucfirst(strtolower($dbType));
			$class = 'IcingaApiSearch' . $sourceType . $classSuffix;
			icingaApiDebugger::logDebug("Creating new interface ".$class);
			$this->ifSettings = new $class;
			$success = true;
		}

		return $success;
	}

	/**
	 * initializes result object and returns it
	 *
	 * @param	void
	 * @return	IcingaApiResult							result object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function fetch () {
		
	}

}

// extend exceptions
class IcingaApiSearchException extends IcingaApiException {	
}

?>