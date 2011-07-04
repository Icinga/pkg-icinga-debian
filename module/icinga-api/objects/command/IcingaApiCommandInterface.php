<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
interface IcingaApiCommandInterface {

	/**
	 * checks the configuration values for the current interface
	 * @param	array		$config				interface configuration
	 * @return	boolean							true if configuration data is valid otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkConfig (array $config);

	/**
	 * sets the configuration values for the current interface
	 * @param	array		$config				interface configuration
	 * @return	IcingaApiCommandSend Object		current interface object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConfig (array $config);

	/**
	 * sets the command(s) to send
	 * @param	array		$command			commands to send
	 * @return	IcingaApiCommandSend Object		current interface object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setCommand (array $command);

	/**
	 * sends the commands by using the current interface
	 * @param	void
	 * @return	boolean							true on success otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function send ();

	/**
	 * returns the call stack
	 * @param	void
	 * @return	array							call stack
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCallStack();

}

?>