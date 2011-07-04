<?php

/**
 * 
 * @author Christian Doebler <christian doebler@netways.de>
 *
 */
class IcingaApiCommandCollection
	extends IcingaApiCommand {
		
	/*
	 * VARIABLES
	 */

	/**
	 * The instance of this class
	 * @var IcingaApiCommandCollection
	 */
	private static $instance = null;
		
	// TODO: add missing commands
	private $commandFields = array (
	
		/*
		 * Downtimes
		 */
		'DEL_HOST_DOWNTIME'						=> array(self::COMMAND_DOWNTIME_ID),
		'DEL_SVC_DOWNTIME'						=> array(self::COMMAND_DOWNTIME_ID),
	
		/*
		 * SERVICE COMMANDS
		 */
		'SET_SVC_NOTIFICATION_NUMBER'			=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_DATA),
		'SCHEDULE_SVC_CHECK'					=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_CHECKTIME),
		'SCHEDULE_FORCED_SVC_CHECK'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_CHECKTIME),
		'ACKNOWLEDGE_SVC_PROBLEM'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_STICKY, self::COMMAND_NOTIFY, self::COMMAND_PERSISTENT, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'REMOVE_SVC_ACKNOWLEDGEMENT'            		=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'SCHEDULE_SVC_DOWNTIME'					=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'PROCESS_SERVICE_CHECK_RESULT'			=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_RETURN_CODE, self::COMMAND_OUTPUT, self::COMMAND_PERFDATA),
		'ADD_SVC_COMMENT'						=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_DATA, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'DISABLE_PASSIVE_SVC_CHECKS'			=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'ENABLE_PASSIVE_SVC_CHECKS'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'DISABLE_SVC_CHECK'						=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'ENABLE_SVC_CHECK'						=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'DISABLE_PASSIVE_SVC_CHECKS'			=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'ENABLE_PASSIVE_SVC_CHECKS'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'START_OBSESSING_OVER_SVC'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'STOP_OBSESSING_OVER_SVC'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'DISABLE_SVC_NOTIFICATIONS'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'ENABLE_SVC_NOTIFICATIONS'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'SEND_CUSTOM_SVC_NOTIFICATION'			=> array(self::COMMAND_HOST, self::COMMAND_SERVICE, self::COMMAND_NOTIFICATION_OPTIONS, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
	
		'ENABLE_SVC_EVENT_HANDLER'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'DISABLE_SVC_EVENT_HANDLER'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
	
		'ENABLE_SVC_FLAP_DETECTION'				=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
		'DISABLE_SVC_FLAP_DETECTION'			=> array(self::COMMAND_HOST, self::COMMAND_SERVICE),
	
		/*
		 * HOST COMMANDS
		 */
		'SCHEDULE_HOST_CHECK'					=> array(self::COMMAND_HOST, self::COMMAND_CHECKTIME),
		'SCHEDULE_FORCED_HOST_CHECK'			=> array(self::COMMAND_HOST, self::COMMAND_CHECKTIME),
		'SCHEDULE_HOST_DOWNTIME'				=> array(self::COMMAND_HOST, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),	
		'SCHEDULE_HOST_SVC_DOWNTIME'			=> array(self::COMMAND_HOST, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'PROCESS_HOST_CHECK_RESULT'				=> array(self::COMMAND_HOST, self::COMMAND_RETURN_CODE, self::COMMAND_OUTPUT, self::COMMAND_PERFDATA),
		'ACKNOWLEDGE_HOST_PROBLEM'				=> array(self::COMMAND_HOST, self::COMMAND_STICKY, self::COMMAND_NOTIFY, self::COMMAND_PERSISTENT, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'REMOVE_HOST_ACKNOWLEDGEMENT'           		=> array(self::COMMAND_HOST),
		'ADD_HOST_COMMENT'						=> array(self::COMMAND_HOST, self::COMMAND_DATA, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'DISABLE_HOST_CHECK'					=> array(self::COMMAND_HOST),
		'ENABLE_HOST_CHECK'						=> array(self::COMMAND_HOST),
		'DISABLE_PASSIVE_HOST_CHECKS'			=> array(self::COMMAND_HOST),
		'ENABLE_PASSIVE_HOST_CHECKS'			=> array(self::COMMAND_HOST),
		'STOP_OBSESSING_OVER_HOST'				=> array(self::COMMAND_HOST),
		'START_OBSESSING_OVER_HOST'				=> array(self::COMMAND_HOST),
		'DISABLE_HOST_NOTIFICATIONS'			=> array(self::COMMAND_HOST),
		'ENABLE_HOST_NOTIFICATIONS'				=> array(self::COMMAND_HOST),
		'SEND_CUSTOM_HOST_NOTIFICATION'			=> array(self::COMMAND_HOST, self::COMMAND_NOTIFICATION_OPTIONS, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'DISABLE_HOST_SVC_NOTIFICATIONS'		=> array(self::COMMAND_HOST),
		'ENABLE_HOST_SVC_NOTIFICATIONS'			=> array(self::COMMAND_HOST),
		'SCHEDULE_FORCED_HOST_SVC_CHECKS'		=> array(self::COMMAND_HOST),
		'DISABLE_HOST_SVC_CHECKS'				=> array(self::COMMAND_HOST),
		'ENABLE_HOST_SVC_CHECKS'				=> array(self::COMMAND_HOST),
		'DISABLE_HOST_EVENT_HANDLER'			=> array(self::COMMAND_HOST),
		'ENABLE_HOST_EVENT_HANDLER'				=> array(self::COMMAND_HOST),
		'DISABLE_HOST_FLAP_DETECTION'			=> array(self::COMMAND_HOST),
		'ENABLE_HOST_FLAP_DETECTION'			=> array(self::COMMAND_HOST),
	
		/*
		 * SERVICEGROUP COMMANDS
		 */
		'SCHEDULE_SERVICEGROUP_HOST_DOWNTIME'		=> array(self::COMMAND_SERVICEGROUP, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'SCHEDULE_SERVICEGROUP_SVC_DOWNTIME'		=> array(self::COMMAND_SERVICEGROUP, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'ENABLE_SERVICEGROUP_HOST_NOTIFICATIONS'	=> array(self::COMMAND_SERVICEGROUP),
		'DISABLE_SERVICEGROUP_HOST_NOTIFICATIONS'	=> array(self::COMMAND_SERVICEGROUP),
		'ENABLE_SERVICEGROUP_SVC_NOTIFICATIONS'		=> array(self::COMMAND_SERVICEGROUP),
		'DISABLE_SERVICEGROUP_SVC_NOTIFICATIONS'	=> array(self::COMMAND_SERVICEGROUP),
		'ENABLE_SERVICEGROUP_SVC_CHECKS'			=> array(self::COMMAND_SERVICEGROUP),
		'DISABLE_SERVICEGROUP_SVC_CHECKS'			=> array(self::COMMAND_SERVICEGROUP),
	
		/*
		 * HOSTGROUP COMMANDS
		 */
		'SCHEDULE_HOSTGROUP_HOST_DOWNTIME'		=> array(self::COMMAND_HOSTGROUP, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'SCHEDULE_HOSTGROUP_SVC_DOWNTIME'		=> array(self::COMMAND_HOSTGROUP, self::COMMAND_STARTTIME, self::COMMAND_ENDTIME, self::COMMAND_FIXED, self::COMMAND_DATA, self::COMMAND_DURATION, self::COMMAND_AUTHOR, self::COMMAND_COMMENT),
		'ENABLE_HOSTGROUP_HOST_NOTIFICATIONS'	=> array(self::COMMAND_HOSTGROUP),
		'DISABLE_HOSTGROUP_HOST_NOTIFICATIONS'	=> array(self::COMMAND_HOSTGROUP),
		'ENABLE_HOSTGROUP_SVC_NOTIFICATIONS'	=> array(self::COMMAND_HOSTGROUP),
		'DISABLE_HOSTGROUP_SVC_NOTIFICATIONS'	=> array(self::COMMAND_HOSTGROUP),
		'ENABLE_HOSTGROUP_SVC_CHECKS'			=> array(self::COMMAND_HOSTGROUP),
		'DISABLE_HOSTGROUP_SVC_CHECKS'			=> array(self::COMMAND_HOSTGROUP),
	);

	private $commandFieldDefinitions = array (
		self::COMMAND_INSTANCE => array(
			'type'		=> 'ro',
			'required'	=> true
		),
		self::COMMAND_HOSTGROUP => array(
			'type'	=> 'ro',
			'required'	=> true
		),
		self::COMMAND_SERVICEGROUP => array(
			'type'	=> 'ro',
			'required'	=> true
		),
		self::COMMAND_HOST => array(
			'type'	=> 'ro',
			'required'	=> true
		),
		self::COMMAND_SERVICE => array(
			'type'	=> 'ro',
			'required'	=> true
		),
		self::COMMAND_ID => array(
			'type'	=> 'ro',
			'required'	=> true
		),
		self::COMMAND_AUTHOR => array(
			'type'	=> 'ro',
			'required'	=> true
		),
		self::COMMAND_COMMENT => array(
			'type'	=> 'textarea',
			'required'	=> true
		),
		self::COMMAND_STARTTIME => array(
			'type'	=> 'date',
			'required'	=> true
		),
		self::COMMAND_ENDTIME => array(
			'type'	=> 'date',
			'required'	=> true
		),
		self::COMMAND_STICKY => array(
			'type'	=> 'checkbox',
			'required'	=> false
		),
		self::COMMAND_PERSISTENT => array(
			'type'	=> 'checkbox',
			'required'	=> false
		),
		self::COMMAND_NOTIFY => array(
			'type'	=> 'checkbox',
			'required'	=> false
		),
		self::COMMAND_RETURN_CODE => array(
			'type'	=> 'return_code',
		),
		self::COMMAND_CHECKTIME => array(
			'type'	=> 'date',
			'required'	=> true
		),
		self::COMMAND_FIXED => array(
			'type'	=> 'checkbox',
			'required'	=> false
		),
		self::COMMAND_OUTPUT => array(
			'type'	=> 'textarea',
			'required'	=> true
		),
		self::COMMAND_PERFDATA => array(
			'type'	=> 'textarea',
			'required'	=> false
		),
		self::COMMAND_DURATION => array(
			'type'	=> 'duration',
			'required'	=> false
		),
		self::COMMAND_DATA => array(
			'type'	=> 'hidden',
			'defaultValue' => 1,
			'required'	=> false
		),
		self::COMMAND_NOTIFICATION_OPTIONS => array (
			'type'	=> 'notification_options',
			'required' => true
		),
		self::COMMAND_DOWNTIME_ID => array(
			'type'	=> 'ro',
			'required'	=> true
		),
	);

	private $commands = array (
		1	=> 'ADD_HOST_COMMENT',
		2	=> 'ADD_SVC_COMMENT',
		3	=> 'DEL_HOST_COMMENT',
		4	=> 'DEL_SVC_COMMENT',
		5	=> 'ENABLE_SVC_CHECK',
		6	=> 'DISABLE_SVC_CHECK',
		7	=> 'DISABLE_NOTIFICATIONS',
		8	=> 'ENABLE_NOTIFICATIONS',
		9	=> 'RESTART_PROGRAM',
		10	=> 'SHUTDOWN_PROGRAM',
		11	=> 'ENABLE_SVC_NOTIFICATIONS',
		12	=> 'DISABLE_SVC_NOTIFICATIONS',
		13	=> 'DEL_ALL_HOST_COMMENTS',
		14	=> 'DEL_ALL_SVC_COMMENTS',
		15	=> 'ENABLE_HOST_NOTIFICATIONS',
		16	=> 'DISABLE_HOST_NOTIFICATIONS',
		17	=> 'ENABLE_ALL_NOTIFICATIONS_BEYOND_HOST',
		18	=> 'DISABLE_ALL_NOTIFICATIONS_BEYOND_HOST',
		19	=> 'ENABLE_HOST_AND_CHILD_NOTIFICATIONS',
		20	=> 'DISABLE_HOST_AND_CHILD_NOTIFICATIONS',
		21	=> 'SET_HOST_NOTIFICATION_NUMBER',
		22	=> 'SET_SVC_NOTIFICATION_NUMBER',
		23	=> 'ENABLE_SERVICE_FRESHNESS_CHECKS',
		24	=> 'ENABLE_HOST_FRESHNESS_CHECKS',
		25	=> 'DISABLE_SERVICE_FRESHNESS_CHECKS',
		26	=> 'DISABLE_HOST_FRESHNESS_CHECKS',
		27	=> 'SCHEDULE_AND_PROPAGATE_TRIGGERED_HOST_DOWNTIME',
		28	=> 'SCHEDULE_AND_PROPAGATE_HOST_DOWNTIME',
		29	=> 'SCHEDULE_SVC_CHECK',
		30	=> 'SCHEDULE_HOST_SVC_CHECKS',
		31	=> 'SAVE_STATE_INFORMATION',
		32	=> 'READ_STATE_INFORMATION',
		33	=> 'ENABLE_HOST_SVC_CHECKS',
		34	=> 'DISABLE_HOST_SVC_CHECKS',
		35	=> 'ENABLE_HOST_SVC_NOTIFICATIONS',
		36	=> 'DISABLE_HOST_SVC_NOTIFICATIONS',
		37	=> 'DELAY_SVC_NOTIFICATION',
		38	=> 'DELAY_HOST_NOTIFICATION',
		39	=> 'ACKNOWLEDGE_HOST_PROBLEM',
		40	=> 'ACKNOWLEDGE_SVC_PROBLEM',
		41	=> 'START_EXECUTING_SVC_CHECKS',
		42	=> 'STOP_EXECUTING_SVC_CHECKS',
		43	=> 'START_ACCEPTING_PASSIVE_SVC_CHECKS',
		44	=> 'STOP_ACCEPTING_PASSIVE_SVC_CHECKS',
		45	=> 'ENABLE_PASSIVE_SVC_CHECKS',
		46	=> 'DISABLE_PASSIVE_SVC_CHECKS',
		47	=> 'ENABLE_EVENT_HANDLERS',
		48	=> 'DISABLE_EVENT_HANDLERS',
		49	=> 'ENABLE_HOST_EVENT_HANDLER',
		50	=> 'DISABLE_HOST_EVENT_HANDLER',
		51	=> 'ENABLE_SVC_EVENT_HANDLER',
		52	=> 'DISABLE_SVC_EVENT_HANDLER',
		53	=> 'ENABLE_HOST_CHECK',
		54	=> 'DISABLE_HOST_CHECK',
		55	=> 'START_OBSESSING_OVER_SVC_CHECKS',
		56	=> 'STOP_OBSESSING_OVER_SVC_CHECKS',
		57	=> 'START_OBSESSING_OVER_HOST_CHECKS',
		58	=> 'STOP_OBSESSING_OVER_HOST_CHECKS',
		59	=> 'START_OBSESSING_OVER_HOST',
		60	=> 'STOP_OBSESSING_OVER_HOST',
		61	=> 'START_OBSESSING_OVER_SVC',
		62	=> 'STOP_OBSESSING_OVER_SVC',
		63	=> 'ENABLE_FAILURE_PREDICTION',
		64	=> 'DISABLE_FAILURE_PREDICTION',
		65	=> 'ENABLE_PERFORMANCE_DATA',
		66	=> 'DISABLE_PERFORMANCE_DATA',
		67	=> 'START_EXECUTING_HOST_CHECKS',
		68	=> 'STOP_EXECUTING_HOST_CHECKS',
		69	=> 'START_ACCEPTING_PASSIVE_HOST_CHECKS',
		70	=> 'STOP_ACCEPTING_PASSIVE_HOST_CHECKS',
		71	=> 'ENABLE_PASSIVE_HOST_CHECKS',
		72	=> 'DISABLE_PASSIVE_HOST_CHECKS',
		73	=> 'ENABLE_FLAP_DETECTION',
		74	=> 'DISABLE_FLAP_DETECTION',
		75	=> 'ENABLE_HOST_FLAP_DETECTION',
		76	=> 'ENABLE_SVC_FLAP_DETECTION',
		77	=> 'DISABLE_HOST_FLAP_DETECTION',
		78	=> 'DISABLE_SVC_FLAP_DETECTION',
		79	=> 'ENABLE_HOSTGROUP_SVC_NOTIFICATIONS',
		80	=> 'DISABLE_HOSTGROUP_SVC_NOTIFICATIONS',
		81	=> 'ENABLE_HOSTGROUP_HOST_NOTIFICATIONS',
		82	=> 'DISABLE_HOSTGROUP_HOST_NOTIFICATIONS',
		83	=> 'ENABLE_HOSTGROUP_SVC_CHECKS',
		84	=> 'DISABLE_HOSTGROUP_SVC_CHECKS',
		85	=> 'ENABLE_HOSTGROUP_HOST_CHECKS',
		86	=> 'DISABLE_HOSTGROUP_HOST_CHECKS',
		87	=> 'ENABLE_HOSTGROUP_PASSIVE_HOST_CHECKS',
		88	=> 'DISABLE_HOSTGROUP_PASSIVE_HOST_CHECKS',
		89	=> 'ENABLE_HOSTGROUP_PASSIVE_SVC_CHECKS',
		90	=> 'DISABLE_HOSTGROUP_PASSIVE_SVC_CHECKS',
		91	=> 'ENABLE_SERVICEGROUP_SVC_NOTIFICATIONS',
		92	=> 'DISABLE_SERVICEGROUP_SVC_NOTIFICATIONS',
		93	=> 'ENABLE_SERVICEGROUP_HOST_NOTIFICATIONS',
		94	=> 'DISABLE_SERVICEGROUP_HOST_NOTIFICATIONS',
		95	=> 'ENABLE_SERVICEGROUP_SVC_CHECKS',
		96	=> 'DISABLE_SERVICEGROUP_SVC_CHECKS',
		97	=> 'ENABLE_SERVICEGROUP_HOST_CHECKS',
		98	=> 'DISABLE_SERVICEGROUP_HOST_CHECKS',
		99	=> 'ENABLE_SERVICEGROUP_PASSIVE_SVC_CHECKS',
		100	=> 'DISABLE_SERVICEGROUP_PASSIVE_SVC_CHECKS',
		101	=> 'ENABLE_SERVICEGROUP_PASSIVE_HOST_CHECKS',
		102	=> 'DISABLE_SERVICEGROUP_PASSIVE_HOST_CHECKS',
		103	=> 'CHANGE_GLOBAL_HOST_EVENT_HANDLER',
		104	=> 'CHANGE_GLOBAL_SVC_EVENT_HANDLER',
		105	=> 'CHANGE_HOST_EVENT_HANDLER',
		106	=> 'CHANGE_SVC_EVENT_HANDLER',
		107	=> 'CHANGE_HOST_CHECK_COMMAND',
		108	=> 'CHANGE_SVC_CHECK_COMMAND',
		109	=> 'CHANGE_NORMAL_HOST_CHECK_INTERVAL',
		110	=> 'CHANGE_NORMAL_SVC_CHECK_INTERVAL',
		111	=> 'CHANGE_RETRY_SVC_CHECK_INTERVAL',
		112	=> 'CHANGE_MAX_HOST_CHECK_ATTEMPTS',
		113	=> 'CHANGE_MAX_SVC_CHECK_ATTEMPTS',
		114	=> 'PROCESS_SERVICE_CHECK_RESULT',
		115	=> 'PROCESS_HOST_CHECK_RESULT',
		116	=> 'REMOVE_HOST_ACKNOWLEDGEMENT',
		117	=> 'REMOVE_SVC_ACKNOWLEDGEMENT',
		118	=> 'SCHEDULE_HOST_DOWNTIME',
		119	=> 'SCHEDULE_SVC_DOWNTIME',
		120	=> 'SCHEDULE_SERVICEGROUP_SVC_DOWNTIME',
		121	=> 'SCHEDULE_SERVICEGROUP_HOST_DOWNTIME',
		122	=> 'SCHEDULE_HOST_SVC_DOWNTIME',
		123	=> 'SCHEDULE_HOSTGROUP_HOST_DOWNTIME',
		124	=> 'SCHEDULE_HOSTGROUP_SVC_DOWNTIME',
		125	=> 'DEL_HOST_DOWNTIME',
		126	=> 'DEL_SVC_DOWNTIME',
		127	=> 'SCHEDULE_HOST_CHECK',
		128	=> 'SCHEDULE_FORCED_HOST_CHECK',
		129	=> 'SCHEDULE_FORCED_SVC_CHECK',
		130	=> 'SCHEDULE_FORCED_HOST_SVC_CHECKS',
		131	=> 'PROCESS_FILE',
		134 => 'SEND_CUSTOM_HOST_NOTIFICATION',
		145 => 'SEND_CUSTOM_SVC_NOTIFICATION'
	);

	/*
	 * METHODS
	 */
	
	/**
	 * Singleton instance method
	 * Creates a new instance if needed and returns the object by ref
	 * @return IcingaApiCommandCollection instance of the class
	 * @author Marius Hein <marius.hein@netways.de>
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new IcingaApiCommandCollection();
		}
		
		return self::$instance;
	}
	
	public function __construct() {
		
		// Prohibit multiple instances of this class
		if (self::$instance !== null) {
			throw new IcingaApiCommandCollectionException('Call IcingaApiCommandCollectionException::getInstance() instead');
		}
	}
	
	protected function __clone() {}
	
	/**
	 * returns a command's name determined by its id
	 * @param	integer			$id				command id
	 * @return	string							command name or boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCommandNameById ($id = false) {
		$commandName = false;
		if ($id !== false && array_key_exists($id, $this->commands)) {
			$commandName = $this->commands[$id];
		} else {
			throw new IcingaApiCommandCollectionException('getCommandNameById(): Invalid command id!');
		}
		return $commandName;
	}

	/**
	 * returns a command's id determined by its name
	 * @param	string			$commandName	command name
	 * @return	integer							command id or boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCommandIdByName ($commandName = false) {
		$commandId = false;
		if ($commandName === false || ($commandId = array_search($commandName, $this->commands)) === false) {
			throw new IcingaApiCommandCollectionException('getCommandIdByName(): Invalid command name!');
		}
		return $commandId;
	}

	/**
	 * returns a command's fields
	 * @param	string			$commandName	command name
	 * @return	array							command fields or boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCommandFields ($commandName = false) {
		$commandFields = false;
		if ($commandName !== false && array_key_exists($commandName, $this->commandFields)) {
			$commandFields = $this->commandFields[$commandName];
		} else {
			throw new IcingaApiCommandCollectionException('getCommandFields(): Invalid command!');
		}
		return $commandFields;
	}

	/**
	 * returns a command field's definition
	 * @param	string			$commandField	command field
	 * @return	array							command field's definition or boolean false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getCommandFieldDefinition ($commandField = false) {
		$commandDef = false;
		if ($commandField !== false && array_key_exists($commandField, $this->commandFieldDefinitions)) {
			$commandDef = $this->commandFieldDefinitions[$commandField];
		} else {
			throw new IcingaApiCommandCollectionException('getCommandFieldDefinition(): Invalid command!');
		}
		return $commandDef;
	}

}

// class exceptions
class IcingaApiCommandCollectionException extends IcingaApiCommandException {}

?>
