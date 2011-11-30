<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
class IcingaApiCommandDispatcher
	extends IcingaApiCommandSend {

	/*
	 * VARIABLES
	 */

	private $interface = false;
	private $settings = false;
	private $sendObject = false;

	/*
	 * METHODS
	 */

	/**
	 * clears the command pool
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function clearCommands () {
		$this->commands = array ();
	}

	/**
	 * sets the interface to be used for sending commands
	 * @param	string			$interface				command interface
	 * @param	array			$config					interface config
	 * @return	IcingaApiCommandDispatcher				instance of IcingaApiCommandDispatcher object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setInterface($interface, array $config = array()) {
		$this->interface = $interface;
		$this->settings = $config;
		return $this;
	}

	/**
	 * sends command(s) by using the defined interface
	 * @param	void
	 * @return	boolean									true on success otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function send () {
		$sendOk = false;
		if ($this->interface !== false && $this->settings !== false && $this->commands !== false) {
			
			$class = 'IcingaApiCommandSend' . $this->interface;
			$this->sendObject = new $class;
			$this->sendObject->setConfig($this->settings);
			$this->sendObject->setCommands($this->commands);
			$this->sendObject->send();
			
		} else {
			throw new IcingaApiCommandDispatcherException('send(): interface, settings or command(s) invalid!');
		}
		return $sendOk;
	}

	/**
	 * returns the call stack for further processing
	 * @param	void
	 * @return	array									call stack of sent commands; if send() has not been called yet, return value will be boolean false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCallStack () {
		$returnData = false;
		
		if ($this->sendObject !== false) {
			$returnData = $this->sendObject->getCallStack();
		} else {
			throw new IcingaApiCommandDispatcherException('getCallStack(): Call Object not set yet!');
		}
		
		return $returnData;
	}

}

// class exceptions
class IcingaApiCommandDispatcherException extends IcingaApiCommandException {}

?>