<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
class IcingaApiCommandSendSsh
	extends IcingaApiCommandSend
	implements IcingaApiCommandInterface {

	/*
	 * VARIABLES
	 */

	protected $config = array (
		'ssh_bin'		=> '/usr/bin/ssh',
		'ssh_user'		=> 'icinga',
		'ssh_host'		=> 'localhost',
		'ssh_port'		=> 22,
		'ssh_timeout'	=> 20,
		'ssh_pipe'		=> '/usr/local/icinga/var/rw/icinga.cmd',
	);

	private $callStack = array();

	/*
	 * CONSTANTS
	 */

	const SSH_CALL_TEMPLATE = '%s -p %d -oConnectTimeout=%d %s@%s \'echo "%s" > %s\'';
	const SSH_DIR = '/tmp';

	/*
	 * METHODS
	 */

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#checkConfig()
	 */
	public function checkConfig (array $config) {
		$configOk = true;
		foreach ($config as $key => $value) {
			if (!array_key_exists($key, $config)) {
				$configOk = false;
				throw new IcingaApiCommandSendSshException('checkConfig(): Invalid key "' . $key . '"!');
			}
		}
		return $configOk;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#setConfig()
	 */
	public function setConfig (array $config) {
		if ($this->checkConfig($config)) {
			foreach ($config as $key => $value) {
				$this->config[$key] = $config[$key];
			}
		}
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/command/IcingaApiCommandInterface#send()
	 */
	public function send () {
		$success = false;
		if ($this->commands !== false) {
			foreach ($this->commands as $commandObject) {
				if (isset($commandObject) && $commandObject instanceof IcingaApiCommand) {
					$sshCall = $this->getSshCall($commandObject->getCommandLine());
					if (!$this->executeCall($sshCall)) {
						throw new IcingaApiCommandSendSshException('send(): command exeution failed!');
					}
				}
			}
		} else {
			throw new IcingaApiCommandSendSshException('send(): Config or command(s) missing!');
		}
		return $success;
	}

	/**
	 * generates the ssh-command line
	 * @param	string		$command				icinga command to be sent
	 * @return	string								ssh-command line
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getSshCall ($command) {
		$sshCall = sprintf(
			self::SSH_CALL_TEMPLATE,
			$this->config['ssh_bin'],
			$this->config['ssh_port'],
			$this->config['ssh_timeout'],
			$this->config['ssh_user'],
			$this->config['ssh_host'],
			str_replace('"', '\\"', $command),
			$this->config['ssh_pipe']
		);
		return $sshCall;
	}

	/**
	 * executes the current ssh-command line
	 * @param	string		$call					ssh-command line
	 * @return	boolean								true on success, false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function executeCall ($call) {
		$executionOk = false;
		$env = Array ('PATH' => '');
		$pipes = false;
		$desc = Array (
			array ('pipe', 'r'), // STDIN
			array ('pipe', 'w'), // STDOUT
			array ('pipe', 'w'), // STDERR
		);
		$proc = proc_open($call, $desc, $pipes, self::SSH_DIR);
		if (is_resource($proc)) {
			$aux = stream_get_contents($pipes[0]);
			$aux = stream_get_contents($pipes[1]);
			$stderr = stream_get_contents($pipes[2]);
			$exit = (int)proc_close($proc);
			if ($exit == 0) {
				$executionOk = true;
			}
		}
		array_push($this->callStack, array($call => $executionOk));
		return $executionOk;
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
class IcingaApiCommandSendSshException extends IcingaApiCommandSendException {}

?>