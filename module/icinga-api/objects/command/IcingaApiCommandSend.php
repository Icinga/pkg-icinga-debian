<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
class IcingaApiCommandSend
	extends IcingaApiCommand {

	/*
	 * VARIABLES
	 */
	protected $config = false;
	
	protected $commands = array ();

	/*
	 * METHODS
	 */

	public function setCommands(array $commands) {
		$this->commands = (array)$commands + (array)$this->commands;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommand#setCommand()
	 */
	public function setCommand ($command) {
		$this->setCommands(array($command));
	}
}

// class exceptions
class IcingaApiCommandSendException extends IcingaApiCommandException {}

?>