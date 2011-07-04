<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
class IcingaApiCommand
	extends IcingaApi {

	/*
	 * VARIABLES
	 */

	private $config = array (
		'command_id'	=> false,
		'command'		=> false,
		'fields'		=> false,
		'target'		=> false,
	);
	
	private $commandLine = false;

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 * @param	void
	 * @return	IcingaApiCommand				instance of IcingaApiCommand object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {
		
	}
	
	/**
	 * sets an icinga command
	 * @param	mixed				$command	command id (integer) or command name (string)
	 * @return	IcingaApiCommand				the current object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 * @author	Marius Hein <marius.hein@netways.de>
	 */
	public function setCommand ($command) {
		
		$cc = self::getCommandCollection();
		
		if ($command !== false) {
			
			$commandId = false;
			$commandName = false;
			
			if (is_int($command)) {
				$commandName = $cc->getCommandNameById($command);
				
				if ($commandName !== false) {
					$commandId = $command;
				}
			} else {
				$commandId = $cc->getCommandIdByName($command);
				
				if ($commandId !== false) {
					$commandName = $command;
				}
			}
			
			if ($commandId !== false && $commandName !== false) {
				
				$commandFields = $cc->getCommandFields($commandName);
				
				$this->config['command_id'] = $commandId;
				$this->config['command'] = $commandName;
				$this->config['fields']	= $commandFields;
			} else {
				$this->config['command_id'] = false;
				$this->config['command'] = false;
				$this->config['fields']	= false;
			}
			
		} else {
			throw new IcingaApiCommandException('setCommand(): Invalid command!');
		}
		
		$this->createCommandLine();
		
		return $this;
	}

	/**
	 * checks target (command parameter)
	 * @param	string			$target				target
	 * @return	boolean								true if target is valid otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function checkTarget ($target = false) {
		$targetOk = false;
		if (
			$target == self::COMMAND_INSTANCE ||
			$target == self::COMMAND_HOSTGROUP ||
			$target == self::COMMAND_SERVICEGROUP ||
			$target == self::COMMAND_HOST ||
			$target == self::COMMAND_SERVICE ||
			$target == self::COMMAND_ID ||
			$target == self::COMMAND_AUTHOR ||
			$target == self::COMMAND_COMMENT ||
			$target == self::COMMAND_STARTTIME ||
			$target == self::COMMAND_ENDTIME ||
			$target == self::COMMAND_STICKY ||
			$target == self::COMMAND_PERSISTENT ||
			$target == self::COMMAND_NOTIFY ||
			$target == self::COMMAND_RETURN_CODE ||
			$target == self::COMMAND_CHECKTIME ||
			$target == self::COMMAND_FIXED ||
			$target == self::COMMAND_OUTPUT ||
			$target == self::COMMAND_PERFDATA ||
			$target == self::COMMAND_DURATION ||
			$target == self::COMMAND_DATA
		) {
			$targetOk = true;
		}
		return $targetOk;
	}

	/**
	 * sets command target (command parameter)
	 * @param	string			$key				command key
	 * @param	mixed			$value				command value
	 * @return	IcingaApiCommand					instance of IcingaApiCommand object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setTarget ($key = false, $value = false) {
		if (!empty($key) && $value !== false) {
			if ($this->config['target'] === false) {
				$this->target = array();
			}
			$this->config['target'][$key] = $value;
		} else {
			throw new IcingaApiCommandException('setTarget(): Invalid key or value!');
		}
		$this->createCommandLine();
		return $this;
	}

	/**
	 * Return the target instance of the command
	 * @return string
	 * @author Marius Hein <marius.hein@netways.de>
	 */
	public function getCommandInstance() {
		if (isset($this->config['target'][self::COMMAND_INSTANCE])) {
			return $this->config['target'][self::COMMAND_INSTANCE];
		}

		return null;
	}

	/**
	 * checks fields (command parameter values)
	 * @param	void
	 * @return	boolean								true if fields are ok otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function checkFields () {
		$fieldsOk = true;
		if (is_array($this->config['fields']) && is_array($this->config['target'])) {
			foreach ($this->config['fields'] as $currentField) {
				if (!array_key_exists($currentField, $this->config['target'])) {
					$fieldsOk = false;
					break;
				}
			}
		}
		return $fieldsOk;
	}

	/**
	 * generated the command line which will be sent to icinga
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function createCommandLine () {
		if (
			$this->config['command_id'] !== false &&
			$this->config['command'] !== false &&
			$this->checkFields()
		) {
			
			$template = '[%s] %s' . str_repeat(';%s', count($this->config['fields']));
			$variables = array(time(), $this->config['command']);
			foreach ($this->config['fields'] as $currentField) {
				array_push($variables, $this->config['target'][$currentField]);
			}
			$this->commandLine = vsprintf($template, $variables);
		}
	}

	/**
	 * returns the command line which will be sent to icinga
	 * @param	void
	 * @return	string									command line
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCommandLine () {
		return $this->commandLine;
	}

}

// class execeptions
class IcingaApiCommandException extends IcingaApiException {};

?>