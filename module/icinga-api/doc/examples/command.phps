<?

/*
 * send command through pipe
 */
$config = array(
	'pipe'			=> '/usr/local/icinga/var/rw/icinga.cmd',
);
$cmd = IcingaApi::getCommandObject();
$cmd->setCommand('SCHEDULE_SVC_CHECK')->setTarget(IcingaApi::COMMAND_INSTANCE, '1')->setTarget(IcingaApi::COMMAND_HOST, 'linux-www-20')->setTarget(IcingaApi::COMMAND_SERVICE, 'apache-procs')->setTarget(IcingaApi::COMMAND_CHECKTIME, '1251449262');
$cmdDisp = IcingaApi::getCommandDispatcher();
$cmdDisp->setInterface(IcingaApi::COMMAND_PIPE, $config)->setCommand($cmd)->send();

/*
 * send command by ssh
 */
$config = array (
	'ssh_bin'		=> '/usr/bin/ssh',
	'ssh_user'		=> 'icinga',
	'ssh_host'		=> 'icinga_host',
	'ssh_port'		=> 22,
	'ssh_timeout'	=> 20,
	'ssh_pipe'		=> '/usr/local/icinga/var/rw/icinga.cmd',
);
$cmd = IcingaApi::getCommandObject();
$cmd->setCommand('SCHEDULE_SVC_CHECK')->setTarget(IcingaApi::COMMAND_INSTANCE, '1')->setTarget(IcingaApi::COMMAND_HOST, 'linux-www-20')->setTarget(IcingaApi::COMMAND_SERVICE, 'apache-procs')->setTarget(IcingaApi::COMMAND_CHECKTIME, '1251449262');
$cmdDisp = IcingaApi::getCommandDispatcher();
$cmdDisp->setInterface(IcingaApi::COMMAND_SSH, $config)->setCommand($cmd)->send();

/*
 * send multiple commands
 */
$config = array(
	'pipe'			=> '/usr/local/icinga/var/rw/icinga.cmd',
);
$cmd = IcingaApi::getCommandObject();
$cmd->setCommand('SCHEDULE_SVC_CHECK')->setTarget(IcingaApi::COMMAND_INSTANCE, '1')->setTarget(IcingaApi::COMMAND_HOST, 'linux-www-20')->setTarget(IcingaApi::COMMAND_SERVICE, 'apache-procs')->setTarget(IcingaApi::COMMAND_CHECKTIME, '1251449262');
$cmd2 = IcingaApi::getCommandObject();
$cmd2->setCommand('SCHEDULE_SVC_CHECK')->setTarget(IcingaApi::COMMAND_INSTANCE, '1')->setTarget(IcingaApi::COMMAND_HOST, 'linux-db-3')->setTarget(IcingaApi::COMMAND_SERVICE, 'mysql-procs')->setTarget(IcingaApi::COMMAND_CHECKTIME, '1251449265');
$cmdDisp = IcingaApi::getCommandDispatcher();
$cmdDisp->setInterface(IcingaApi::COMMAND_PIPE, $config)->setCommand(array($cmd, $cmd2))->send();


/*
 * get call stack AFTER sending command via dispatcher (for error output and further checking)
 */
$callStack = $cmdDisp->getCallStack();

?>
