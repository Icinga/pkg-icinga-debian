<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
class IcingaApiCommandSendPipe
	extends IcingaApiCommandSend
	implements IcingaApiCommandInterface {

	/*
	 * VARIABLES
	 */
	protected $config = false;

	private $callStack = array();

	/*
	 * METHODS
	 */

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#checkConfig()
	 */
	public function checkConfig (array $config) {
		$configOk = false;
		if (array_key_exists('pipe', $config) && !empty($config['pipe'])) {
			if (file_exists($config['pipe']) && is_writable($config['pipe'])) {
				$configOk = true;
			} else {
				throw new IcingaApiCommandSendPipeException('checkConfig(): Pipe "' . $config['pipe'] . '" is missing or not writable!');
			}
		} else {
			throw new IcingaApiCommandSendPipeException('checkConfig(): Setting for key "pipe" is missing!');
		}
		return $configOk;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#setConfig()
	 */
	public function setConfig (array $config) {
		if ($this->checkConfig($config)) {
			$this->config = $config;
		}
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#send()
	 * TODO: fix blocking on pipes which are not read
	 */
	public function send () {
		$success = false;
		if ($this->config !== false && count($this->commands)) {
			
			$pipe = fopen($this->config['pipe'], 'w');
			if ($pipe) {
				
				$commands = $this->getCommandLineStrings();
				icingaApiDebugger::logDebug("Sending to commandpipe  :".$commands);
				$commandLength = strlen($commands);
				$sizeWritten = fwrite($pipe, $commands, $commandLength);
				
				if ($sizeWritten < $commandLength) {
					throw new IcingaApiCommandSendPipeException('send(): Commands not completely transmitted!');
				} else {
					$success = true;
				}
				
				fclose($pipe);
				
			} else {
				throw new IcingaApiCommandSendPipeException('send(): Could not open pipe!');
			}
		} else {
			throw new IcingaApiCommandSendPipeException('send(): Config or command(s) missing!');
		}
		
		$this->callStack[] = array($this->config['pipe'], $commands, $success);
		
		return $success;
	}
	
	protected function getCommandLineStrings() {
		if (count($this->commands)) {
			
			$tarry = array ();
			
			foreach ($this->commands as $commandObject) {
				if ($commandObject instanceof IcingaApiCommand && $commandObject->getCommandLine()) {
					$tarry[] = $commandObject->getCommandLine();
				}
			}
			
			if (count($tarry)) {
				return implode(chr(10), $tarry). chr(10);
			}
			
			throw new IcingaApiCommandSendException('Commandstring is empty');
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#getCallStack()
	 */
	public function getCallStack () {
		return $this->callStack;
	}

}

// class exceptions
class IcingaApiCommandSendPipeException extends IcingaApiCommandException {}

?>