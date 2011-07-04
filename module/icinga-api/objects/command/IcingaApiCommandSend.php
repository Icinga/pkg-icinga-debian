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
	protected $commands = false;

	/*
	 * METHODS
	 */

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommand#setCommand()
	 */
	public function setCommand (array $command) {
		if (count($command)) {
			$this->commands = $command;
		} else {
			throw new IcingaApiCommandSendException('setCommand(): No command(s) defined!');
		}
	}

}

// class exceptions
class IcingaApiCommandSendException extends IcingaApiCommandException {}

?>