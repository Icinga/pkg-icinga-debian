<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiSearchIdo
	extends IcingaApiSearch {

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
	public function __construct () {
		parent::__construct();
		return $this;
	}

	/**
	 * sets the table prefix
	 *
	 * @param	string		$prefix				table prefix
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setTablePrefix ($prefix = null) {
		$this->ifSettings->tablePrefix = $prefix;
	}

	/**
	 * processes custom filter and returns it
	 *
	 * @param	void
	 * @return	string							custom filter
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getFilterAppendix () {
		$customFilter = null;

		foreach ($this->searchFilterAppend as $currentFilter) {
			$conditionPrefix = '${';
			$numFilterMatches = 0;
			$filterMatches = array();

			if(strpos($currentFilter, '${') !== false) {
				$filterPattern = '/\\' . $conditionPrefix . '([^}]+)}/';
				$numFilterMatches = preg_match_all($filterPattern, $currentFilter, $filterMatches);
			}

			if ($numFilterMatches) {
				for ($x = 0; $x < $numFilterMatches; $x++) {
					$match = $filterMatches[0][$x];
					$column = $filterMatches[1][$x];

					if (($column = $this->getColumn($column)) !== false) {
						$currentFilter = str_replace($match, $column, $currentFilter);
					} else {
						throw new IcingaApiSearchIdoException('getFilterAppendix(): unknown column "' . $column . '"!');
						return null;
					}
				}
				$customFilter .= $currentFilter;
			}
		}

		return $customFilter;
	}

	/**
	 * creates where statement for database query
	 *
	 * @param	void 
	 * @return	array							associative array w/ where statement and corresponding values
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function createQueryFilter () {

		$whereStatementArray = array();
		$whereStatementValues = array();

		foreach ($this->searchFilter as $key => $filterData) {

			$tmpStatementArray = array();

			foreach ($filterData as $matchType => $valueArray) {

				// create template for current part of where statement
				$separator = $key . ' ' . $matchType . ' ?';
				$numValues = count($valueArray);
				for ($x = 0; $x < $numValues; $x++) {
					array_push($tmpStatementArray, $separator);
				}

				// populate value array
				$whereStatementValues = array_merge($whereStatementValues, $valueArray);

				// create temporary statement
				$tmpStatement = '(' . implode(' or ', $tmpStatementArray) . ')';

			}

			// extend final where statement
			array_push($whereStatementArray, $tmpStatement);

		}

		// create final array
		$whereStatement = 'and ' . implode(' and ', $whereStatementArray);

		$returnData = array (
			'statement'	=> $whereStatement,
			'values'	=> $whereStatementValues,
		);

		return $returnData;

	}

	/**
	 * applies a search type to search columns
	 * @param	string				$fields			fields to extend by search type
	 * @return	string								modified fields
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function applySearchType ($fields) {
		switch ($this->searchType) {
			case IcingaApi::SEARCH_TYPE_COUNT:
				$fieldsArray = explode(' ', $fields);
				$countFieldsArray = count($fieldsArray);
				if ($countFieldsArray > 2) {
					throw new IcingaApiSearchException('applySearchType(): invalid number of columns for use of \'count\'!');
				} else {
					$suffix = 'COUNT';
					if ($countFieldsArray == 2) {
						$suffix .= '_' . $fieldsArray[1];
					}
					$fields = 'count(distinct ' . $fieldsArray[0] . ') ' . $suffix;
				}
				break;
		}
		return $fields;
	}

	/**
	 * creates a column to insert into query and pushes it onto the processed stack
	 * @param	string		$columnKey				key that identifies the current column
	 * @return	string								processed column; boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	protected function getColumn ($columnKey) {
		$columnProcessed = false;

		if (array_key_exists($columnKey, $this->ifSettings->columns)) {

			if (array_key_exists($columnKey, $this->columnsProcessed)) {
				$columnProcessed = $this->columnsProcessed[$columnKey];
			} else {
				$table = $this->ifSettings->columns[$columnKey][0];
				$column = $this->ifSettings->columns[$columnKey][1];
				$function = (count($this->ifSettings->columns[$columnKey]) == 3) ?
								$this->ifSettings->columns[$columnKey][2] : false;

				// get TABLE.COLUMN string
				$columnProcessed = sprintf('%s.%s', $table, $column);
	
				// wrap up in function if necessary
				if ($function !== false) {
					$columnProcessed = sprintf ($function, $columnProcessed);
				}

				// store table and processed string for further processing
				if (!in_array($table, $this->joinTables)) {
					array_push($this->joinTables, $table);
				}
				$this->columnsProcessed[$columnKey] = $columnProcessed;
			}

		} else {
			throw new IcingaApiSearchException('getColumn(): Unknown column "' . $columnKey . '"!');
		}

		return $columnProcessed;
	}

	/**
	 * sets result columns for query
	 *
	 * @param	mixed		$columns			array of columns or column as string
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setResultColumns ($columns) {
		if (!is_array($columns)) {
			$columns = array($columns);
		}

		foreach ($columns as $currentColumn) {
			if (($processedColumn = $this->getColumn($currentColumn)) !== false) {
				array_push($this->resultColumnKeys, $currentColumn);
				array_push($this->resultColumnsNoAliases, $processedColumn);
				$processedColumn .= ' ' . $currentColumn;
				array_push($this->resultColumns, $processedColumn);
			}
		}

		return $this;
	}
	/**
	 * Returns an array containing the columns of the result
	 * 
	 * @return array
	 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
	 */
	public function getResultColumns() {
		return $this->resultColumnKeys;
	}

	/**
	 * replaces query variables by corresponding data
	 * @param	string			$query			query template
	 * @param	string			$variableName	name of query variable to replace by defined values
	 * @return	string							processed query
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function replaceQueryVariables ($query, $variableName) {

		$fieldDefaultMatches = array ();
		$variableValuesPrefix = false;
		$variableValuesTemplate = false;
		$variableValues = false;
		$variableNamePrefix = '${' . $variableName;

		if (strpos($query, $variableNamePrefix . ':') !== false) {
			$fieldPattern = '/\\' . $variableNamePrefix . ':([^}]+)}/';
			preg_match_all($fieldPattern, $query, $fieldDefaultMatches);
		}

		$loopCounter = 0;
		while ($loopCounter < 2 && $variableValues == false) {
			if ($loopCounter && count($fieldDefaultMatches)) {
				$variableValues = $fieldDefaultMatches[1][0];
			}

			switch ($variableName) {
				case 'FIELDS':
					if (!$loopCounter) {
						if (count($this->resultColumns)) {
							$variableValues = $this->applySearchType(implode(',', $this->resultColumns));
							if ($this->ifSettings->statements['fieldsSuffix'] !== false) {
								$variableValues .= $this->ifSettings->statements['fieldsSuffix'];
							}
						}
					} else {
						// add default values to joins
						$valuesLong = explode(',', $variableValues);
						foreach ($valuesLong as $currentValueLong) {
							$valueShort = explode(' ', trim($currentValueLong));
							list($table, $column) = explode('.', $valueShort[0]);
							if (!in_array($table, $this->joinTables)) {
								array_push($this->joinTables, $table);
							}
						}
					}
					break;

				case 'FILTER':
					// TODO: insert processing of default values
					break;

				case 'GROUPBY':
					if (!$loopCounter) {
						list($variableValuesTemplate, $variableValues) =
							$this->ifSettings->createQueryGroup($this->searchGroup, $this->resultColumnsNoAliases);
					} elseif ($variableValues !== false) {
						list($variableValuesTemplate, $variableValues) =
							$this->ifSettings->createQueryGroup(explode(',', $variableValues), $this->resultColumnsNoAliases);
					}
					break;

				case 'ORDERBY':
					if (!$loopCounter) {
						$variableValuesPrefix = $this->ifSettings->statements['order'];
						if (count($this->searchOrder)) {
							$this->searchOrder = implode(',', $this->searchOrder);
							$variableValues = $this->searchOrder;
						}
					} elseif ($variableValues !== false) {
						$this->searchOrder = $variableValues;
					}
					break;

				case 'LIMIT':
					if (!$loopCounter) {
						list($variableValuesTemplate, $variableValues) =
							$this->ifSettings->createQueryLimit($this->searchLimit);
					} elseif ($variableValues !== false) {
						$this->searchLimit = explode(',', $variableValues);
					}
					break;
			}

			$loopCounter++;
		}

		$variableNameComplete = (count($fieldDefaultMatches)) ? $fieldDefaultMatches[0][0] : $variableNamePrefix . '}';

		if (!empty($variableValues)) {
			if (!in_array($variableName, $this->ifSettings->clearVariables)) {
				if ($variableValuesPrefix !== false) {
					$variableValues = $variableValuesPrefix . $variableValues;
				}
				if ($variableValuesTemplate !== false) {
					$variableValues = sprintf (
						$variableValuesTemplate,
						$variableValues
					);
				}
			} else {
				$variableValues = null;
			}
		} else {
			$variableValues = null;
		}

		$query = str_replace($variableNameComplete, $variableValues, $query);

		return $query;

	}

	/**
	 * set joins in query by checking available column tables to reduce overhead
	 * @param	string		$query					base query to process
	 * @return	string								processed query with joins
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function setQueryJoins ($query) {
		$conditionPrefix = '${if_table:';
		$numFilterMatches = 0;
		$filterMatches = array();

		if(strpos($query, '${') !== false) {
			$filterPattern = '/\\' . $conditionPrefix . '([^:]+):([^}]+)}/';
			$numFilterMatches = preg_match_all($filterPattern, $query, $filterMatches);
		}

		if ($numFilterMatches) {
			// resolve dependencies of joins
			foreach ($filterMatches[1] as $offset => $tables) {
				$tables = explode(',', $tables);
				if (in_array($tables[0], $this->joinTables)) {
					foreach ($tables as $currentTable) {
						if (!in_array($currentTable, $this->joinTables)) {
							array_push($this->joinTables, $currentTable);
						}
					}
				}
				$filterMatches[1][$offset] = $tables[0];
			}

			// activate joins and remove the ones not needed
			for ($x = 0; $x < $numFilterMatches; $x++) {
				$match = $filterMatches[0][$x];
				$table = $filterMatches[1][$x];
				$join = $filterMatches[2][$x];

				if (in_array($table, $this->joinTables)) {
					$query = str_replace($match, $join, $query);
				} else {
					$query = str_replace($match, null, $query);
				}
			}
		}

		return $query;
	}

	/**
	 * creates and executes database query
	 *
	 * @param	void
	 * @return	boolean							true on success otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function executeQuery () {

		$success = true;

		if ($this->searchValid()) {

			// create base for query
			$query = $this->ifSettings->queryMap[$this->searchTarget];

			// add query fields
			$query = $this->replaceQueryVariables($query, 'FIELDS');

			// add filter
			if (count($this->searchFilter)) {
				$filterData = $this->createQueryFilter();
				$filterStatement = $filterData['statement'];
				$queryValues = $filterData['values'];
			} else {
				$filterStatement = null;
				$queryValues = array();
			}

			// add custom filter to append
			$filterStatement .= $this->getFilterAppendix();

			// replace query variable by filter
			$query = str_replace('${FILTER}', $filterStatement, $query);

			// add 'group by'
			$query = $this->replaceQueryVariables($query, 'GROUPBY');

			// add order
			$query = $this->replaceQueryVariables($query, 'ORDERBY');

			// add limit
			$query = $this->replaceQueryVariables($query, 'LIMIT');

			// set table prefixes
			$query = str_replace('${TABLE_PREFIX}', $this->ifSettings->tablePrefix, $query);

			// set query joins
			$query = $this->setQueryJoins($query);

			// postprocessing
			if ($this->ifSettings->postProcess === true) {
				$query = $this->ifSettings->postProcessQuery($query, $this->resultColumnKeys, $this->searchOrder, $this->searchLimit);
			}

			$dbResult = $this->connectionObject->query($query, $queryValues);

		} else {

			$success = false;

		}

		return $success;

	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearch#fetch()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function fetch () {
		$object = false;

		$class = 'IcingaApiResultIdo';
		$object = new $class;

		if ($this->executeQuery()) {
			$object->setSearchObject($this->connectionObject->connectionStatement);
			$object->setDbType($this->connectionObject->getDbType());
			if ($this->resultType !== false) {
				$object->setResultType($this->resultType);
			}
			$object->next();
		}

		parent::fetch();
		return $object;
	}

	/**
	 * Map defining which table (shortcuts) will be fetched by which targets
	 */
	public $tableMap = array(
		self::TARGET_HOST => array('h','oh','i','hcg','cg','ocg','cgm','hg','hgm','ohg','cvsh'),
		self::TARGET_SERVICE => array('os','s','i','scg','cg','oc','ss','ocg','hs','sgm','sg','hs','oh','hgm','hg','ohg','cvsh','cvss','cvsc'),
		self::TARGET_HOSTGROUP => array('hg','ohg','hgm','oh'),
		self::TARGET_SERVICEGROUP => array('sg','osg','sgm','os'),
		self::TARGET_CONTACTGROUP => array('cg','ocg','cgm','oc','cvsc'),
		self::TARGET_TIMEPERIOD => array('otp','tp','otp'),
		self::TARGET_CUSTOMVARIABLE => array('cv','cvs'),
		self::TARGET_CONFIG => array('cfv'),
		self::TARGET_PROGRAM => array('pe'),
		self::TARGET_LOG => array('le'),
		self::TARGET_HOST_STATUS_SUMMARY => array('hs','oh','h','i','hcg','cg','cgm','oc','hgm','cvsh','cvsc'),
		self::TARGET_SERVICE_STATUS_SUMMARY => array('os','ss','s','i','scg','cg','cgm','oc','ocg','hs','oh','hgm','ohg','hg','cvsh','cvss','cvsc'),
		self::TARGET_HOST_STATUS_HISTORY => array('oh','sh','h','hcg','cg','ocg','cgm','hgm','hg','cvsh'),
		self::TARGET_SERVICE_STATUS_HISTORY => array('sh','os','s','oh','scg','cg','cgm','hgm','hg','ohg','cvsh','cvss'),
		self::TARGET_HOST_PARENTS => array('ohp','hph','h','oh'),
		self::TARGET_NOTIFICATIONS => array('n','on','s','h','oh','os'),
		self::TARGET_HOSTGROUP_SUMMARY => array('hg','ohg','hgm','oh','hs'),
		self::TARGET_SERVICEGROUP_SUMMARY => array('sg','osg','sgm','ss','os')
	);
	
	/**
	 * returns an array containing the names of all affected columns
	 * @return array
	 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
	 */
	public function getAffectedColumns() {
		$map = $this->tableMap[$this->getSearchTarget()];
		$affected = array();
		$columns = $this->ifSettings->columns;
		foreach($map as $table) {
			foreach($columns as $name=>$column) {
				if($column[0] == $table)
					$affected[] = $name;
			}
		}
		return $affected;
	}
	
	
}

// extend exceptions
class IcingaApiSearchIdoException extends IcingaApiSearchException {}

?>
