<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiSearchLivestatus
	extends IcingaApiSearch {

	/*
	 * VARIABLES
	 */

	protected $config = false;
	private $socket = false;

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
	 * set config 
	 * @param array $config
	 * @return unknown
	 */
	public function setConfig (array $config) {
		$this->config = $config;
		return $this;
	}

	/**
	 * creates and connects to livestatus socket
	 * @param	void
	 * @return	boolean							true on success otherwise false
	 */
	private function connectSocket() {
		$returnValue = true;

		switch ($this->config['type']) {
			case 'unix':
				$this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
				break;
			case 'tcp':
				$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
				break;
		}

		if($this->socket === false) {
			throw new IcingaApiSearchLivestatusException('connectSocket(): could not create livestatus socket!');
			$returnValue = false;
		} else {
			switch ($this->config['type']) {
				case 'unix':
					$result = socket_connect($this->socket, $this->config['path']);
					break;
				case 'tcp':
					$result = socket_connect($this->socket, $this->config['host'], $this->config['port']);
					break;
			}

			if($result === false) {
				throw new IcingaApiSearchLivestatusException('connectSocket(): could not connect to livestatus socket!');
				$returnValue = false;
			}
		}

		return $returnValue;
	}

	/**
	 * reads data from socket (inspired by NagVis' GlobalBackendmklivestatus)
	 * @param	integer			$length			length of data to read
	 * @return	string							read data; boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function readSocket ($length) {
		$offset = 0;
		$data = null;

		while($offset < $length) {
			if(($dataTmp = @socket_read($this->socket, $length - $offset)) === false) {
				$data = false;
				break;
			}

			$dataLength = strlen($dataTmp);

			if ($dataLength) {
				$offset += $dataLength;
				$data .= $dataTmp;
			} else {
				break;
			}
		}

		return $data;
	}

	/**
	 * creates a column to insert into query and pushes it onto the processed stack
	 * @param	string		$columnKey				key that identifies the current column
	 * @return	string								processed column; boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	protected function getColumn ($columnKey) {
		$columnProcessed = false;

		if ($this->searchTarget !== false) {
			if (array_key_exists($columnKey, $this->ifSettings->columns[$this->searchTarget])) {

				if (array_key_exists($columnKey, $this->columnsProcessed)) {
					$columnProcessed = $this->columnsProcessed[$columnKey];
				} else {
					$columnProcessed = $this->ifSettings->columns[$this->searchTarget][$columnKey][0];

					// store table and processed string for further processing
					$this->columnsProcessed[$columnKey] = $columnProcessed;
				}

			} else {
				throw new IcingaApiSearchException('getColumn(): Unknown column "' . $columnKey . '"!');
			}
		} else {
			throw new IcingaApiSearchException('getColumn(): no search target defined!');
		}

		return $columnProcessed;
	}

	/**
	 * sets result columns for query
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
				array_push($this->resultColumns, $processedColumn);
			}
		}

		return $this;
	}

	/**
	 * creates a list of columns used for the query
	 * @param	void
	 * @return	string							filter statements
	 */
	private function getQueryColumns () {
		$columns = null;

		if (count($this->resultColumns)) {
			$columns = 'Columns: ' .
				implode(' ', $this->resultColumns) . "\n";
		}

		return $columns;
	}

	/**
	 * creates the filter statements for livestatus query and returns them
	 * @param	void
	 * @return	string							filter statements
	 */
	private function getQueryFilter () {
		$filter = null;

		//$numAnd = count($this->searchFilter);

		if (count($this->searchFilter)) {
			foreach ($this->searchFilter as $column => $columnData) {
				$numOr = 0;

				foreach ($columnData as $matchType => $filterStrArr) {
					$numOr += count($filterStrArr);

					$matchRegEx = ($matchType == self::MATCH_LIKE) ? true : false;

					foreach ($filterStrArr as $filterStr) {
						if ($matchRegEx) {
							$filterStr =
								'^' . str_replace('%', '.*' , $filterStr) . '$';
						}

						$filter .= sprintf (
							"Filter: %s %s %s\n",
								$column,
								$this->ifSettings->matchMap[$matchType],
								$filterStr
						);
					}
				}

				if ($numOr > 1) {
					$filter .= 'Or: ' . $numOr . "\n";
				}
			}

			/*
			if ($numAnd > 1) {
				$filter .= 'And: ' . $numAnd . "\n";
			}
			*/
		}

		return $filter;
	}

	/**
	 * creates the limit statements for livestatus query and returns it
	 * @param	void
	 * @return	string							filter statements
	 */
	private function getQueryLimit () {
		$limit = null;

		if (count($this->searchLimit)) {
			$limit = 'Limit: ' . $this->searchLimit[0] . "\n";
		}

		return $limit;
	}

	/**
	 * creates query string for livestatus
	 * @param unknown_type $queryObject
	 * @return unknown_type
	 */
	private function createQuery () {
		$query = sprintf(
			"GET %s\n%s%s%sOutputFormat: json\nKeepAlive: on\nResponseHeader: fixed16\n\n",
			$this->ifSettings->queryMap[$this->searchTarget],
			$this->getQueryColumns(),
			$this->getQueryFilter(),
			$this->getQueryLimit()
		);

		return $query;
	}

	/**
	 * queries livestatus socket and returns answer (inspired by NagVis' GlobalBackendmklivestatus)
	 * @param	string		$queryObject		livestatus object to query for
	 * @return	
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function executeQuery () {
		$data = null;

		$query = $this->createQuery();

		$this->connectSocket();

		if ($this->socket !== false) {

			socket_write($this->socket, $query);
			$data = $this->readSocket(16);

			if ($data !== false) {

				// extract status code
				$status = substr($data, 0, 3);

				// extract content length
				$length = intval(trim(substr($data, 4, 11)));

				// read remaining data
				$data = $this->readSocket($length);

				if ($data === false) {
					throw new IcingaApiSearchLivestatusException('querySocket(): could not read from socket!');
				}

				if ($status != '200') {
					throw new IcingaApiSearchLivestatusException('querySocket(): could not read from socket: status ' . $status . ', message: ' . $data);
				}

				// check for 'connection reset by peer'
				if (socket_last_error($this->socket) == 104) {
					throw new IcingaApiSearchLivestatusException('querySocket(): could not read from socket: connection reset by peer!');
				}

			} else {

				throw new IcingaApiSearchLivestatusException('querySocket(): could not read from socket!');

			}

		}

		$data = json_decode(utf8_decode($data));

		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/IcingaApiSearch#fetch()
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function fetch () {
		$object = false;

		$class = 'IcingaApiResultLivestatus';
		$object = new $class;

		if (($data = $this->executeQuery()) !== false) {
			$object->setSearchColumns($this->resultColumnKeys);
			$object->setSearchResult($data);
			if ($this->resultType !== false) {
				$object->setResultType($this->resultType);
			}
			$object->next();
		}

		parent::fetch();
		return $object;
	}

}

// extend exceptions
class IcingaApiSearchLivestatusException extends IcingaApiSearchException {}

?>
