<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiConnectionLivestatus extends IcingaApiConnection {

	/*
	 * VARIABLES
	 */

	public $type = 'Livestatus';

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
	 * checks whether config is OK
	 *
	 * @param	array		$config				associative array storing configuration
	 * @return	boolean							true if configuration is OK, false on error(s)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkConfig (array $config) {
		$configOk = true;

		// check socket type
		if (array_key_exists('type', $config)) {
			if ($config['type'] != 'unix' && $config['type'] != 'tcp') {
				throw new IcingaApiConnectionLivestatusException('Configuration error: unknown livestatus socket type "' . $config['type'] . '" found!');
				$configOk = false;
			}
		} else {
			throw new IcingaApiConnectionLivestatusException('Configuration error: no setting for livestatus socket type found!');
			$configOk = false;
		}

		// check socket
		if ($configOk) {
			switch ($config['type']) {
				case 'unix':
					if (!array_key_exists('path', $config)) {
						throw new IcingaApiConnectionLivestatusException('Configuration error: no setting for livestatus socket path found!');
						$configOk = false;
					}
					break;
				case 'tcp':
					if (!array_key_exists('host', $config)) {
						throw new IcingaApiConnectionLivestatusException('Configuration error: no setting for livestatus socket host found!');
						$configOk = false;
					}
					if (!array_key_exists('port', $config)) {
						throw new IcingaApiConnectionLivestatusException('Configuration error: no setting for livestatus socket port found!');
						$configOk = false;
					}
					break;
			}
		}

		// perform global checks
		$configOkParent = parent::checkConfig($config);
		if (!$configOkParent && $configOk) {
			$configOk = false;
		}

		return $configOk;
	}

	/**
	 * triggers config check and puts config into right place
	 * @param	array		$config				associative array storing configuration
	 * @return	boolean							true on success otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConfig (array $config) {
		if (!$this->checkConfig($config)) {
			return false;
		}
		$this->config = $config;
		return $this;
	}

	/**
	 * right now just a dummy
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function connect () {
		return $this;
	}

	/**
	 * calls initialization method for new search
	 *
	 * @param	void
	 * @return	IcingaApiSearchFile				search object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function createSearch () {
		$class = 'IcingaApiSearch' . $this->icingaType;
		$searchObject = new $class;
		$searchObject->setDebug($this->debug);
		$searchObject->setConfig($this->config);
		$searchObject->loadInterfaceClass($this->icingaType, 'socket');
//		$searchObject->loadColumns($this->icingaType);
		return $searchObject;
	}

}

// extend exceptions
class IcingaApiConnectionLivestatusException extends IcingaApiConnectionException {}

?>