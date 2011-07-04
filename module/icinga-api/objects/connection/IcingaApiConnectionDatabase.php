<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
abstract class IcingaApiConnectionDatabase
	extends IcingaApiConnection
	implements IcingaApiConnectionInterface {
	public $type = 'Database';
	/*
	 * VARIABLES
	 */

	private $databaseDsn = false;

	public $connectionStatement = false;

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
	 * calls initialization method for new search
	 *
	 * @param	void
	 * @return	mixed							search object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	abstract public function createSearch ();

	/**
	 * checks the database configuration
	 *
	 * @param	array		$config				associative array storing configuration
	 * @return	boolean							true if configuration is OK, false on error(s)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkConfig (array $config) {

		$configOk = true;

		if (!array_key_exists('type', $config)) {
			throw new IcingaApiConnectionDatabaseException('Configuration error: No database type defined!');
			$configOk = false;
		}

		if (!array_key_exists('host', $config)) {
			throw new IcingaApiConnectionDatabaseException('Configuration error: No database host defined!');
			$configOk = false;
		}

		if (array_key_exists('port', $config)) {
			$port = (int)$config['port'];
			if ($port < 1 || $port > 65535) {
				throw new IcingaApiConnectionDatabaseException('Configuration error: Invalid database port!');
				$configOk = false;
			}
		}

		if (!array_key_exists('database', $config)) {
			throw new IcingaApiConnectionDatabaseException('Configuration error: No database defined!');
			$configOk = false;
		}

		if (!array_key_exists('user', $config)) {
			throw new IcingaApiConnectionDatabaseException('Configuration error: No database user defined!');
			$configOk = false;
		}

		if (!array_key_exists('password', $config)) {
			throw new IcingaApiConnectionDatabaseException('Configuration error: No database password defined!');
			$configOk = false;
		}

		$configOkParent = parent::checkConfig($config);
		if (!$configOkParent && $configOk) {
			$configOk = false;
		}

		return $configOk;

	}

	/**
	 * assembles the database DSN
	 *
	 * @param	array		$config				associative array storing database connection settings
	 * @return	mixed							DSN string on success otherwise boolean false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConfig (array $config) {

		if ($this->databaseDsn === false) {

			if (!$this->checkConfig($config)) {
				return false;
			}

			if (!array_key_exists('table_prefix', $config)) {
				$config['table_prefix'] = null;
			}
			$this->config = $config;

			if ($this->config['type'] != 'oci' && $this->config['type'] != 'oci8')  {

				$this->databaseDsn = sprintf(
					'%s:host=%s;dbname=%s',
					$this->config['type'],
					$this->config['host'],
					$this->config['database']
				);
				if (array_key_exists('port', $this->config)) {
					$this->databaseDsn .= ';port=' . (int)$this->config['port'];
				}

			} else if( $this->config['type'] == 'oci8') {
				$this->databaseDsn = $this->config['type'].$this->config['host']."/".$this->config['database'];
				
			} else {

				if (array_key_exists('port', $this->config)) {
					$dbPort = ':' . $this->config['port'];
				} else {
					$dbPort = null;
				}
				$this->databaseDsn = sprintf(
					'%s:dbname=//%s%s/%s',
					$this->config['type'],
					$this->config['host'],
					$dbPort,
					$this->config['database']
				);

			}

		}

		return $this;

	}

	/**
	 * connects to database
	 *
	 * @param	array		$config				associative array storing database connection settings
	 * @return	IcingaApiConnectionDatabase		database object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function connect () {

		if ($this->databaseDsn !== false) {

			if (array_key_exists('persistent', $this->config)) {
				$connectionAttributes = array (
					PDO::ATTR_PERSISTENT	=> true,
				);
			} else {
				$connectionAttributes = false;
			}

			try {

				if ($connectionAttributes !== false) {
					$this->connectionObject = icingaApiPDO::getPDO($this->databaseDsn, $this->config['user'], $this->config['password'], $connectionAttributes);
				} else {
					$this->connectionObject = icingaApiPDO::getPDO($this->databaseDsn, $this->config['user'], $this->config['password']);
				}

			} catch (PDOException $e) {

				throw new IcingaApiConnectionDatabaseException('Database connection failed: ' . $e->getMessage());

			}

		}
		return $this;

	}

	/**
	 * queries database using PDOs
	 *
	 * @param	string			$query				query to execute
	 * @param	array			$queryParams		query parameters to use with query
	 * @return	IcingaApiConnectionDatabase			database object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function query ($query, $queryParams = array()) {

		try {
			$this->connectionStatement = $this->connectionObject->prepare($query);			
		} catch (PDOException $e) {
			throw new IcingaApiConnectionDatabaseException('Database-query prepare failed: ' . $e->getMessage());
		}

		if ($this->connectionStatement !== false) {
			try {
				$this->connectionStatement->execute($queryParams);
			} catch (PDOException $e) {
				throw new IcingaApiConnectionDatabaseException('Database-query execute failed: ' . $e->getMessage());
			}
		}

		return $this;

	}

}

// extend exceptions
class IcingaApiConnectionDatabaseException extends IcingaApiConnectionException {}

?>
