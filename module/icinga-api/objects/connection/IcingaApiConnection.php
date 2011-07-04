<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
abstract class IcingaApiConnection
	extends IcingaApi {

	/*
	 * VARIABLES
	 */

	protected $config = false;
	protected $connectionObject = false;
	protected $connectionResultSet = false;
	protected $searchFilterDefault = array();

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
	 * checks the database configuration
	 *
	 * @param	array		$config				associative array storing configuration
	 * @return	boolean							true if configuration is OK, false on error(s)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkConfig ($config) {

		$configOk = true;

		if (array_key_exists('contact_cource', $config) && empty($config['contact_source'])) {
			$config['contact_source'] = false;
			throw new IcingaApiConnectionException('Configuration error: Invalid contact_source!');
			$configOk = false;
		}

		return $configOk;

	}

	/**
	 * sets default search filter(s)
	 * @param	mixed		$filter				filter key or associative array of key-value pairs defining filters
	 * @param	mixed		$value				value to filter for
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setDefaultSearchFilter ($filter, $value = false, $defaultMatch = IcingaApi::MATCH_EXACT) {
		if (!is_array($filter)) {
			$filter = array(array($filter, $value, $defaultMatch));
		} else {
			if (!is_array($filter[0])) {
				$filter = array($filter);
			}
		}

		foreach ($filter as $currentFilter) {
			array_push($this->searchFilterDefault, $currentFilter);
		}

		return $this;
	}

}

// extend exceptions
class IcingaApiConnectionException extends IcingaApiException {}

?>