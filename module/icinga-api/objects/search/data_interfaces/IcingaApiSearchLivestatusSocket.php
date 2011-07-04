<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiSearchLivestatusSocket
	extends IcingaApiSearch {

	/*
	 * VARIABLES
	 */

	public $statements = array ();

	public $queryMap = array (
		self::TARGET_HOST => 'hosts',
		self::TARGET_SERVICE => 'services',
		self::TARGET_HOSTGROUP => 'hostgroups',
		self::TARGET_SERVICEGROUP => 'servicegroups',
		self::TARGET_CONTACT => 'contacts',
		self::TARGET_CONTACTGROUP => 'contactgroups',	// verify
		self::TARGET_TIMEPERIOD => 'timeperiods',
		self::TARGET_CUSTOMVARIABLE => '',
		self::TARGET_CONFIG => '',
		self::TARGET_PROGRAM => '',
		self::TARGET_LOG => 'logs',
		self::TARGET_HOST_STATUS_SUMMARY => '', 
		self::TARGET_SERVICE_STATUS_SUMMARY => '',
		self::TARGET_HOST_STATUS_HISTORY => '',
		self::TARGET_SERVICE_STATUS_HISTORY => '',
		self::TARGET_HOST_PARENTS => '',
		self::TARGET_NOTIFICATIONS => '',
		self::TARGET_HOSTGROUP_SUMMARY => '',
		self::TARGET_SERVICEGROUP_SUMMARY => '',
		self::TARGET_COMMAND => 'commands',
		self::TARGET_DOWNTIME => 'downtimes',
		self::TARGET_COMMENT => 'comments',
		self::TARGET_STATUS => 'status',
	);

	public $matchMap = array (
		self::MATCH_EXACT => '=~',
		self::MATCH_LIKE => '~~',
		self::MATCH_GREATER_THAN => '>',
		self::MATCH_GREATER_OR_EQUAL => '>=',
		self::MATCH_LESS_THAN => '<',
		self::MATCH_LESS_OR_EQUAL => '<=',
	);

	// COLUMNS
	public $columns = array(
/*
		// Program information
		'PROGRAM_INSTANCE_ID' => array('instance_id'),
		'PROGRAM_DATE' => array('program_date'),
		'PROGRAM_VERSION' => array('program_version'),
*/

		// Hostgroup data
		self::TARGET_HOSTGROUP => array (
			'HOSTGROUP_ACTION_URL' => array('action_url'),
			'HOSTGROUP_ALIAS' => array('alias'),
			'HOSTGROUP_MEMBERS' => array('members'),
			'HOSTGROUP_NAME' => array('name'),
			'HOSTGROUP_NOTES' => array('notes'),
			'HOSTGROUP_NOTES_URL' => array('notes_url'),
			'HOSTGROUP_NUM_HOSTS' => array('num_hosts'),
			'HOSTGROUP_NUM_HOSTS_DOWN' => array('num_hosts_down'),
			'HOSTGROUP_NUM_HOSTS_UNREACH' => array('num_hosts_unreach'),
			'HOSTGROUP_NUM_HOSTS_UP' => array('num_hosts_up'),
			'HOSTGROUP_NUM_SERVICES' => array('num_services'),
			'HOSTGROUP_NUM_SERVICES_CRIT' => array('num_services_crit'),
			'HOSTGROUP_NUM_SERVICES_HARD_CRIT' => array('num_services_hard_crit'),
			'HOSTGROUP_NUM_SERVICES_HARD_OK' => array('num_services_hard_ok'),
			'HOSTGROUP_NUM_SERVICES_HARD_UNKNOWN' => array('num_services_hard_unknown'),
			'HOSTGROUP_NUM_SERVICES_HARD_WARN' => array('num_services_hard_warn'),
			'HOSTGROUP_NUM_SERVICES_OK' => array('num_services_ok'),
			'HOSTGROUP_NUM_SERVICES_UNKNOWN' => array('num_services_unknown'),
			'HOSTGROUP_NUM_SERVICES_WARN' => array('num_services_warn'),
			'HOSTGROUP_WORST_HOST_STATE' => array('worst_host_state'),
			'HOSTGROUP_WORST_SERVICE_HARD_STATE' => array('worst_service_hard_state'),
			'HOSTGROUP_WORST_SERVICE_STATE' => array('worst_service_state'),
		),

		// Servicegroup data
		self::TARGET_SERVICEGROUP => array (
			'SERVICEGROUP_ACTION_URL' => array('action_url'),
			'SERVICEGROUP_ALIAS' => array('alias'),
			'SERVICEGROUP_MEMBERS' => array('members'),
			'SERVICEGROUP_NAME' => array('name'),
			'SERVICEGROUP_NOTES' => array('notes'),
			'SERVICEGROUP_NOTES_URL' => array('notes_url'),
			'SERVICEGROUP_NUM_SERVICES' => array('num_services'),
			'SERVICEGROUP_NUM_SERVICES_CRIT' => array('num_services_crit'),
			'SERVICEGROUP_NUM_SERVICES_HARD_CRIT' => array('num_services_hard_crit'),
			'SERVICEGROUP_NUM_SERVICES_HARD_OK' => array('num_services_hard_ok'),
			'SERVICEGROUP_NUM_SERVICES_HARD_UNKNOWN' => array('num_services_hard_unknown'),
			'SERVICEGROUP_NUM_SERVICES_HARD_WARN' => array('num_services_hard_warn'),
			'SERVICEGROUP_NUM_SERVICES_OK' => array('num_services_ok'),
			'SERVICEGROUP_NUM_SERVICES_UNKNOWN' => array('num_services_unknown'),
			'SERVICEGROUP_NUM_SERVICES_WARN' => array('num_services_warn'),
			'SERVICEGROUP_WORST_SERVICE_STATE' => array('worst_service_state'),
		),

/*
		// Contactgroup data
		TODO
*/

		// Contact data
		self::TARGET_CONTACT => array (
			'CONTACT_ADDRESS1' => array('address1'),
			'CONTACT_ADDRESS2' => array('address2'),
			'CONTACT_ADDRESS3' => array('address3'),
			'CONTACT_ADDRESS4' => array('address4'),
			'CONTACT_ADDRESS5' => array('address5'),
			'CONTACT_ADDRESS6' => array('address6'),
			'CONTACT_ALIAS' => array('alias'),
			'CONTACT_CAN_SUBMIT_COMMANDS' => array('can_submit_commands'),
			'CONTACT_CUSTOMVARIABLE_NAMES' => array('custom_variable_names'),
			'CONTACT_CUSTOMVARIABLE_VALUES' => array('custom_variable_values'),
			'CONTACT_EMAIL' => array('email'),
			'CONTACT_HOST_NOTIFICATION_PERIOD' => array('host_notification_period'),
			'CONTACT_HOST_NOTIFICATIONS_ENABLED' => array('host_notifications_enabled'),
			'CONTACT_IN_HOST_NOTIFICATION_PERIOD' => array('in_host_notification_period'),
			'CONTACT_IN_SERVICE_NOTIFICATION_PERIOD' => array('in_service_notification_period'),
			'CONTACT_NAME' => array('name'),
			'CONTACT_PAGER' => array('pager'),
			'CONTACT_SERVICE_NOTIFICATION_PERIOD' => array('service_notification_period'),
			'CONTACT_SERVICE_NOTIFICATIONS_ENABLED' => array('service_notifications_enabled'),
		),

/*
		// Timeperiod data
		'TIMEPERIOD_ID' => array('timeperiod_id'),
		'TIMEPERIOD_OBJECT_ID' => array('object_id'),
		'TIMEPERIOD_INSTANCE_ID' => array('instance_id'),
		'TIMEPERIOD_NAME' => array('name1'),
		'TIMEPERIOD_ALIAS' => array('alias'),
		'TIMEPERIOD_DAY' => array('day'),
		'TIMEPERIOD_STARTTIME' => array('start_sec'),
		'TIMEPERIOD_ENDTIME' => array('end_sec'),

		self::TARGET_TIMEPERIOD => array (
			TODO
		),
*/

/*
		// Customvariable data
		'CUSTOMVARIABLE_ID' => array('customvariable_id'),
		'CUSTOMVARIABLE_OBJECT_ID' => array('object_id'),
		'CUSTOMVARIABLE_INSTANCE_ID' => array('instance_id'),
		'CUSTOMVARIABLE_NAME' => array('varname'),
		'CUSTOMVARIABLE_VALUE' => array('varvalue'),
		'CUSTOMVARIABLE_MODIFIED' => array('has_been_modified'),
		'CUSTOMVARIABLE_UPDATETIME' => array('status_update_time'),
*/

		// Host data
		self::TARGET_HOST => array (
			'HOST_NAME' => array('name'),
			'HOST_ALIAS' => array('alias'),
			'HOST_DISPLAY_NAME' => array('display_name'),
			'HOST_ADDRESS' => array('address'),
			'HOST_ACTIVE_CHECKS_ENABLED' => array('active_checks_enabled'),
			'HOST_FLAP_DETECTION_ENABLED' => array('flap_detection_enabled'),
			'HOST_PROCESS_PERFORMANCE_DATA' => array('process_performance_data'),
			'HOST_FRESHNESS_CHECKS_ENABLED' => array('check_freshness'),
			'HOST_PASSIVE_CHECKS_ENABLED' => array('accept_passive_checks'),
			'HOST_EVENT_HANDLER_ENABLED' => array('event_handler_enabled'),
			//'HOST_ACTIVE_CHECKS_ENABLED' => array('active_checks_enabled'), // checks_enabled ?
			'HOST_NOTIFICATIONS_ENABLED' => array('notifications_enabled'),
			'HOST_NOTES' => array('notes'),
			'HOST_NOTES_URL' => array('notes_url'),
			'HOST_ACTION_URL' => array('action_url'),
			'HOST_ICON_IMAGE' => array('icon_image'),
			'HOST_ICON_IMAGE_ALT' => array('icon_image_alt'),
			//'HOST_IS_ACTIVE' => array('is_active'),
			'HOST_OUTPUT' => array('plugin_output'),
			'HOST_LONG_OUTPUT' => array('long_plugin_output'),
			'HOST_PERFDATA' => array('perf_data'),
			'HOST_CURRENT_STATE' => array('state'),
			'HOST_CURRENT_CHECK_ATTEMPT' => array('current_attempt'),
			'HOST_MAX_CHECK_ATTEMPTS' => array('max_check_attempts'),
			'HOST_LAST_CHECK' => array('last_check'),
			'HOST_LAST_STATE_CHANGE' => array('last_state_change'),
			'HOST_CHECK_TYPE' => array('check_type'),
			'HOST_LATENCY' => array('latency'),
			'HOST_EXECUTION_TIME' => array('execution_time'),
			'HOST_NEXT_CHECK' => array('next_check'),
			'HOST_HAS_BEEN_CHECKED' => array('has_been_checked'),
			'HOST_LAST_HARD_STATE_CHANGE' => array('last_hard_state_change'),
			'HOST_LAST_NOTIFICATION' => array('last_notification'),
			'HOST_STATE_TYPE' => array('state_type'),
			'HOST_IS_FLAPPING' => array('is_flapping'),
			'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('acknowledged'),
			'HOST_SCHEDULED_DOWNTIME_DEPTH' => array('scheduled_downtime_depth'),
			/*
			'HOST_STATUS_UPDATE_TIME' => array('status_update_time'),
			'HOST_EXECUTION_TIME_MIN' => array('execution_time', 'min'),
			'HOST_EXECUTION_TIME_AVG' => array('execution_time', 'avg'),
			'HOST_EXECUTION_TIME_MAX' => array('execution_time', 'max'),
			'HOST_LATENCY_MIN' => array('latency', 'min'),
			'HOST_LATENCY_AVG' => array('latency', 'avg'),
			'HOST_LATENCY_MAX' => array('latency', 'max'),
			*/
			'HOST_STATE' => array('state'),
			//'HOST_STATE_COUNT' => array('current_state'),
			/*
			'HOST_PARENT_NAME' => array('name1'),
			'HOST_CHILD_NAME' => array('name1'),
			*/

			// livestatus only
			'HOST_ACCEPT_PASSIVE_CHECKS' => array('accept_passive_checks'),
			'HOST_ACKNOWLEDGED' => array('acknowledged'),
			'HOST_ACKNOWLEDGEMENT_TYPE' => array('acknowledgement_type'),
			//'HOST_ACTION_URL' => array('action_url'),
			//'HOST_ACTIVE_CHECKS_ENABLED' => array('active_checks_enabled'),
			//'HOST_ADDRESS' => array('address'),
			//'HOST_ALIAS' => array('alias'),
			'HOST_CHECK_COMMAND' => array('check_command'),
			'HOST_CHECK_FRESHNESS' => array('check_freshness'),
			'HOST_CHECK_INTERVAL' => array('check_interval'),
			'HOST_CHECK_OPTIONS' => array('check_options'),
			'HOST_CHECK_PERIOD' => array('check_period'),
			'HOST_CHECK_TYPE' => array('check_type'),
			'HOST_CHECK_ENABLED' => array('checks_enabled'),
			'HOST_CHILDS' => array('childs'),
			'HOST_CONTACTS' => array('contacts'),
			'HOST_CURRENT_ATTEMPT' => array('current_attempt'),
			'HOST_CURRENT_NOTIFICATION_NUMBER' => array('current_notification_number'),
			'HOST_CUSTOM_VARIABLE_NAMES' => array('custom_variable_names'),
			'HOST_CUSTOM_VARIABLE_VALUES' => array('custom_variable_values'),
			//'HOST_DISPLAY_NAME' => array('display_name'),
			'HOST_DOWNTIMES' => array('downtimes'),
			'HOST_EVENT_HANDLER_ENABLED' => array('event_handler_enabled'),
			'HOST_EXECUTION_TIME' => array('execution_time'),
			'HOST_FIRST_NOTIFICATION_DELAY' => array('first_notification_delay'),
			'HOST_FLAP_DETECTION_ENABLED' => array('flap_detection_enabled'),
			'HOST_GROUPS' => array('groups'),
			'HOST_HARD_STATE' => array('hard_state'),
			'HOST_HAS_BEEN_CHECKED' => array('has_been_checked'),
			'HOST_HIGH_FLAP_THRESHOLD' => array('high_flap_threshold'),
			'HOST_ICON_IMAGE' => array('icon_image'),
			'HOST_ICON_IMAGE_ALT' => array('icon_image_alt'),
			'HOST_IN_CHECK_PERIOD' => array('in_check_period'),
			'HOST_IN_NOTIFICATION_PERIOD' => array('in_notification_period'),
			'HOST_INITIAL_STATE' => array('initial_state'),
			'HOST_IS_EXECUTING' => array('is_executing'),
			'HOST_IS_FLAPPING' => array('is_flapping'),
			'HOST_LAST_CHECK' => array('last_check'),
			'HOST_LAST_HARD_STATE' => array('last_hard_state'),
			'HOST_LAST_HARD_STATE_CHANGE' => array('last_hard_state_change'),
			'HOST_LAST_NOTIFICATION' => array('last_notification'),
			'HOST_LAST_STATE' => array('last_state'),
			'HOST_LAST_STATE_CHANGE' => array('last_state_change'),
			'HOST_LATENCY' => array('latency'),
			'HOST_LONG_PLUGIN_OUTPUT' => array('long_plugin_output'),
			'HOST_LOW_FLAP_THRESHOLD' => array('low_flap_threshold'),
			//'HOST_MAX_CHECK_ATTEMPTS' => array('max_check_attempts'),
			//'HOST_NAME' => array('name'),
			//'HOST_NEXT_CHECK' => array('next_check'),
			'HOST_NEXT_NOTIFICATION' => array('next_notification'),
			//'HOST_NOTES' => array('notes'),
			//'HOST_NOTES_URL' => array('notes_url'),
			'HOST_NOTIFICATION_INTERVAL' => array('notification_interval'),
			'HOST_NOTIFICATION_PERIOD' => array('notification_period'),
			'HOST_NOTIFICATION_ENABLED' => array('notifications_enabled'),
			'HOST_NUM_SERVICES' => array('num_services'),
			'HOST_NUM_SERVICES_CRIT' => array('num_services_crit'),
			'HOST_NUM_SERVICES_HARD_CRIT' => array('num_services_hard_crit'),
			'HOST_NUM_SERVICES_HARD_OK' => array('num_services_hard_ok'),
			'HOST_NUM_SERVICES_HARD_UNKNOWN' => array('num_services_hard_unknown'),
			'HOST_NUM_SERVICES_HARD_WARN' => array('num_services_hard_warn'),
			'HOST_NUM_SERVICES_OK' => array('num_services_ok'),
			'HOST_NUM_SERVICES_UNKNOWN' => array('num_services_unknown'),
			'HOST_NUM_SERVICES_WARN' => array('num_services_warn'),
			'HOST_OBSESS_OVER_HOST' => array('obsess_over_host'),
			'HOST_PARENTS' => array('parents'),
			'HOST_PENDING_FLEX_DOWNTIME' => array('pending_flex_downtime'),
			'HOST_PERCENT_STATE_CHANGE' => array('percent_state_change'),
			'HOST_PERF_DATA' => array('perf_data'),
			'HOST_PLUGIN_OUTPUT' => array('plugin_output'),
			//'HOST_PROCESS_PERFORMANCE_DATA' => array('process_performance_data'),
			'HOST_RETRY_INTERVAL' => array('retry_interval'),
			//'HOST_SCHEDULED_DOWNTIME_DEPTH' => array('scheduled_downtime_depth'),
			//'HOST_STATE' => array('state'),
			//'HOST_STATE_TYPE' => array('state_type'),
			'HOST_STATUSMAP_IMAGE' => array('statusmap_image'),
			'HOST_TOTAL_SERVICES' => array('total_services'),
			'HOST_WORST_SERVICE_HARD_STATE' => array('worst_service_hard_state'),
			'HOST_WORST_SERVICE_STATE' => array('worst_service_state'),
			'HOST_X_3D' => array('x_3d'),
			'HOST_Y_3D' => array('y_3d'),
			'HOST_Z_3D' => array('z_3d'),
		),

		// Service data
		self::TARGET_SERVICE => array (
			'HOST_NAME' => array('host_name'),
			'HOST_ALIAS' => array('host_alias'),
			'HOST_DISPLAY_NAME' => array('host_display_name'),
			'HOST_ADDRESS' => array('host_address'),
			'HOST_ACTIVE_CHECKS_ENABLED' => array('host_active_checks_enabled'),
			'HOST_FLAP_DETECTION_ENABLED' => array('host_flap_detection_enabled'),
			'HOST_PROCESS_PERFORMANCE_DATA' => array('host_process_performance_data'),
			'HOST_FRESHNESS_CHECKS_ENABLED' => array('host_check_freshness'),
			'HOST_PASSIVE_CHECKS_ENABLED' => array('host_accept_passive_checks'),
			'HOST_EVENT_HANDLER_ENABLED' => array('host_event_handler_enabled'),
			'HOST_NOTIFICATIONS_ENABLED' => array('notifications_enabled'),
			'HOST_NOTES' => array('host_notes'),
			'HOST_NOTES_URL' => array('host_notes_url'),
			'HOST_ACTION_URL' => array('host_action_url'),
			'HOST_ICON_IMAGE' => array('host_icon_image'),
			'HOST_ICON_IMAGE_ALT' => array('host_icon_image_alt'),
			'HOST_OUTPUT' => array('host_plugin_output'),
			'HOST_LONG_OUTPUT' => array('host_long_plugin_output'),
			'HOST_PERFDATA' => array('host_perf_data'),
			'HOST_CURRENT_STATE' => array('host_state'),
			'HOST_CURRENT_CHECK_ATTEMPT' => array('host_current_attempt'),
			'HOST_MAX_CHECK_ATTEMPTS' => array('host_max_check_attempts'),
			'HOST_LAST_CHECK' => array('host_last_check'),
			'HOST_LAST_STATE_CHANGE' => array('host_last_hard_state'),
			'HOST_CHECK_TYPE' => array('host_check_type'),
			'HOST_LATENCY' => array('host_latency'),
			'HOST_EXECUTION_TIME' => array('host_execution_time'),
			'HOST_NEXT_CHECK' => array('host_next_check'),
			'HOST_HAS_BEEN_CHECKED' => array('host_has_been_checked'),
			'HOST_LAST_HARD_STATE_CHANGE' => array('host_last_hard_state_change'),
			'HOST_LAST_NOTIFICATION' => array('host_last_notification'),
			'HOST_STATE_TYPE' => array('host_state_type'),
			'HOST_IS_FLAPPING' => array('host_is_flapping'),
			'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('host_acknowledged'),
			'HOST_SCHEDULED_DOWNTIME_DEPTH' => array('host_scheduled_downtime_depth'),
			//'SERVICE_CONFIG_TYPE' => array('config_type'),
			//'SERVICE_IS_ACTIVE' => array('is_active'),
			'SERVICE_NAME' => array('description'),
			'SERVICE_DISPLAY_NAME' => array('display_name'),
			'SERVICE_NOTIFICATIONS_ENABLED' => array('notifications_enabled'),
			//'SERVICE_FLAP_DETECTION_ENABLED' => array('flap_detection_enabled'),
			//'SERVICE_PASSIVE_CHECKS_ENABLED' => array('passive_checks_enabled'),
			'SERVICE_EVENT_HANDLER_ENABLED' => array('event_handler_enabled'),
			'SERVICE_ACTIVE_CHECKS_ENABLED' => array('active_checks_enabled'),
			//'SERVICE_RETAIN_STATUS_INFORMATION' => array('retain_status_information'),
			//'SERVICE_RETAIN_NONSTATUS_INFORMATION' => array('retain_nonstatus_information'),
			//'SERVICE_OBSESS_OVER_SERVICE' => array('obsess_over_service'),
			//'SERVICE_FAILURE_PREDICTION_ENABLED' => array('failure_prediction_enabled'),
			'SERVICE_NOTES' => array('notes'),
			'SERVICE_NOTES_URL' => array('notes_url'),
			'SERVICE_ACTION_URL' => array('action_url'),
			'SERVICE_ICON_IMAGE' => array('icon_image'),
			'SERVICE_ICON_IMAGE_ALT' => array('icon_image_alt'),
			'SERVICE_OUTPUT' => array('plugin_output'),
			'SERVICE_LONG_OUTPUT' => array('long_plugin_output'),
			'SERVICE_PERFDATA' => array('perfdata'),
			'SERVICE_CURRENT_STATE' => array('state'),
			'SERVICE_CURRENT_CHECK_ATTEMPT' => array('current_attempt'),
			'SERVICE_MAX_CHECK_ATTEMPTS' => array('max_check_attempts'),
			'SERVICE_LAST_CHECK' => array('last_check'),
			'SERVICE_LAST_STATE_CHANGE' => array('last_state_change'),
			'SERVICE_CHECK_TYPE' => array('check_type'),
			'SERVICE_LATENCY' => array('latency'),
			'SERVICE_EXECUTION_TIME' => array('execution_time'),
			'SERVICE_NEXT_CHECK' => array('next_check'),
			'SERVICE_HAS_BEEN_CHECKED' => array('has_been_checked'),
			'SERVICE_LAST_HARD_STATE_CHANGE' => array('last_hard_state_change'),
			'SERVICE_LAST_NOTIFICATION' => array('last_notification'),
			'SERVICE_STATE_TYPE' => array('state_type'),
			'SERVICE_IS_FLAPPING' => array('is_flapping'),
			'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('acknowledged'),
			'SERVICE_SCHEDULED_DOWNTIME_DEPTH' => array('scheduled_downtime_depth'),
			'SERVICE_STATUS_UPDATE_TIME' => array('status_update_time'),
			/*
			'SERVICE_EXECUTION_TIME_MIN' => array('execution_time', 'min(%s)'),
			'SERVICE_EXECUTION_TIME_AVG' => array('execution_time', 'avg(%s)'),
			'SERVICE_EXECUTION_TIME_MAX' => array('execution_time', 'max(%s)'),
			'SERVICE_LATENCY_MIN' => array('latency', 'min(%s)'),
			'SERVICE_LATENCY_AVG' => array('latency', 'avg(%s)'),
			'SERVICE_LATENCY_MAX' => array('latency', 'max(%s)'),
			*/

			// livestatus only
			'SERVICE_ACCEPT_PASSIVE_CHECKS' => array('accept_passive_checks'),
			'SERVICE_ACKNOWLEDGED' => array('acknowledged'),
			'SERVICE_ACKNOWLEDGEMENT_TYPE' => array('acknowledgement_type'),
			'SERVICE_ACRION_URL' => array('action_url'),
			'SERVICE_ACTIVE_CHECKS_ENABLED' => array('active_checks_enabled'),
			'SERVICE_CHECK_COMMAND_ENABLED' => array('check_command'),
			'SERVICE_CHECK_INTERVAL' => array('check_interval'),
			'SERVICE_CHECK_OPTIONS' => array('check_options'),
			'SERVICE_CHECK_PERIOD' => array('check_period'),
			'SERVICE_CHECK_TYPE' => array('check_type'),
			'SERVICE_CHECKS_ENABLED' => array('checks_enabled'),
			'SERVICE_CONTACTS' => array('contacts'),
			'SERVICE_CURRENT_ATTEMPT' => array('current_attempt'),
			'SERVICE_CURRENT_NOTIFICATION_NUMBER' => array('current_notification_number'),
			'SERVICE_CUSTOM_VARIBALE_NAMES' => array('custom_variable_names'),
			'SERVICE_CUSTUM_VARIABLE_VALUES' => array('custom_variable_values'),
			'SERVICE_DESCRIPTION' => array('description'),
			'SERVICE_DISPLAY_NAME' => array('display_name'),
			'SERVICE_DOWNTIMES' => array('downtimes'),
			'SERVICE_EVENT_HANDLER' => array('event_handler'),
			'SERVICE_EVENT_HANDLER_HANDLER_ENABLED' => array('event_handler_enabled'),
			'SERVICE_EXECUTION_TIME' => array('execution_time'),
			'SERVICE_FIRST_NOTIFICATION_DELAY' => array('first_notification_delay'),
			'SERVICE_GROUPS' => array('groups'),
			'SERVICE_HAS_BEEN_CHECKED' => array('has_been_checked'),
			'SERVICE_HIGH_FLAP_THRESHOLD' => array('high_flap_threshold'),
			'HOST_ACCEPT_PASSIVE_CHECKS' => array('host_accept_passive_checks'),
			'HOST_ACKNOWLEDGED' => array('host_acknowledged'),
			'HOST_ACKNOWLEDGEMENT_TYPE' => array('host_acknowledgement_type'),
			'HOST_ACTION_URL' => array('host_action_url'),
			'HOST_ACTIVE_CHECKS_ENABLED' => array('host_active_checks_enabled'),
			'HOST_ADDRESS' => array('host_address'),
			'HOST_ALIAS' => array('host_alias'),
			'HOST_CHECK_COMMAND' => array('host_check_command'),
			'HOST_CHECK_FRESHNESS' => array('host_check_freshness'),
			'HOST_CHECK_INTERVAL' => array('host_check_interval'),
			'HOST_CHECK_OPTIONS' => array('host_check_options'),
			'HOST_CHECK_PERIOD' => array('host_check_period'),
			'HOST_CHECK_TYPE' => array('host_check_type'),
			'HOST_CHECKS_ENABLED' => array('host_checks_enabled'),
			'HOST_CHILDS' => array('host_childs'),
			'HOST_CONACTS' => array('host_contacts'),
			'HOST_CURRENT_ATTEMPT' => array('host_current_attempt'),
			'HOST_CURRENT_NOTIFICATION_NUMBER' => array('host_current_notification_number'),
			'HOST_CUSTOM_VARIABLE_NAMES' => array('host_custom_variable_names'),
			'HOST_CUSTOM_VARIABLE_VALUES' => array('host_custom_variable_values'),
			'HOST_DISPLAY_NAME' => array('host_display_name'),
			'HOST_DOWNTIMES' => array('host_downtimes'),
			'HOST_EVENT_HANDLER_ENABLED' => array('host_event_handler_enabled'),
			'HOST_EXECUTION_TIME' => array('host_execution_time'),
			'HOST_FIRST_NOTIFICATION_DELAY' => array('host_first_notification_delay'),
			'HOST_FLAP_DETECTION_ENABLED' => array('host_flap_detection_enabled'),
			'HOST_GROUPS' => array('host_groups'),
			'HOST_HARD_STATE' => array('host_hard_state'),
			'HOST_HAS_BEEN_CHECKED' => array('host_has_been_checked'),
			'HOST_HIGH_FLAP_THRESHOLD' => array('host_high_flap_threshold'),
			'HOST_ICON_IMAGE' => array('host_icon_image'),
			'HOST_ICON_IMAGE_ALT' => array('host_icon_image_alt'),
			'HOST_IN_CHECK_PERIOD' => array('host_in_check_period'),
			'HOST_IN_NOTIFICATION_PERIOD' => array('host_in_notification_period'),
			'HOST_INITIAL_STATE' => array('host_initial_state'),
			'HOST_IS_EXECUTING' => array('host_is_executing'),
			'HOST_IS_FLAPPING' => array('host_is_flapping'),
			'HOST_LAST_CHECK' => array('host_last_check'),
			'HOST_LAST_HARD_STATE' => array('host_last_hard_state'),
			'HOST_LAST_HARD_STATE_CHANGE' => array('host_last_hard_state_change'),
			'HOST_LAST_NOTIFICATION' => array('host_last_notification'),
			'HOST_LAST_STATE' => array('host_last_state'),
			'HOST_LAST_STATE_CHANGE' => array('host_last_state_change'),
			'HOST_LATENCY' => array('host_latency'),
			'HOST_LONG_PLUGIN_OUTPUT' => array('host_long_plugin_output'),
			'HOST_LOW_FLAP_THRESHOLD' => array('host_low_flap_threshold'),
			'HOST_MAX_CHECK_ATTEMPTS' => array('host_max_check_attempts'),
			'HOST_NAME' => array('host_name'),
			'HOST_NEXT_CHECK' => array('host_next_check'),
			'HOST_NEXT_NOTIFICATION' => array('host_next_notification'),
			'HOST_NOTES' => array('host_notes'),
			'HOST_NOTES_URL' => array('host_notes_url'),
			'HOST_NOTIFICATION_INTERVAL' => array('host_notification_interval'),
			'HOST_NOTIFICATION_PERIOD' => array('host_notification_period'),
			'HOST_NOTIFICATIONS_ENABLED' => array('host_notifications_enabled'),
			'HOST_NUM_SERVICES' => array('host_num_services'),
			'HOST_NUM_SERVICES_CRIT' => array('host_num_services_crit'),
			'HOST_NUM_SERVICES_HARD_CRIT' => array('host_num_services_hard_crit'),
			'HOST_NUM_SERVICES_HARD_OK' => array('host_num_services_hard_ok'),
			'HOST_NUM_SERVICES_HARD_UNKNOWN' => array('host_num_services_hard_unknown'),
			'HOST_NUM_SERVICES_HARD_WARN' => array('host_num_services_hard_warn'),
			'HOST_NUM_SERVICES_OK' => array('host_num_services_ok'),
			'HOST_NUM_SERVICES_UNKNOWN' => array('host_num_services_unknown'),
			'HOST_NUM_SERVICES_WARN' => array('host_num_services_warn'),
			'HOST_OBSESS_OVER_HOST' => array('host_obsess_over_host'),
			'HOST_PARENTS' => array('host_parents'),
			'HOST_PENDING_FLEX_DOWNTIME' => array('host_pending_flex_downtime'),
			'HOST_PERCENT_STATE_CHANGE' => array('host_percent_state_change'),
			'HOST_PERF_DATA' => array('host_perf_data'),
			'HOST_PLUGIN_OUTPUT' => array('host_plugin_output'),
			'HOST_PROCESS_PERFORMANCE_DATA' => array('host_process_performance_data'),
			'HOST_RETRY_INTERVAL' => array('host_retry_interval'),
			'HOST_SCHEDULED_DOWNTIME_DEPTH' => array('host_scheduled_downtime_depth'),
			'HOST_STATE' => array('host_state'),
			'HOST_STATE_TYPE' => array('host_state_type'),
			'HOST_STATUSMAP_IMAGE' => array('host_statusmap_image'),
			'HOST_TOTAL_SERVICES' => array('host_total_services'),
			'HOST_WORST_SERVICE_HARD_STATE' => array('host_worst_service_hard_state'),
			'HOST_WORST_SERVICE_STATE' => array('host_worst_service_state'),
			'HOST_X_3D' => array('host_x_3d'),
			'HOST_Y_3D' => array('host_y_3d'),
			'HOST_Z_3D' => array('host_z_3d'),
			'SERVICE_ICON_IMAGE' => array('icon_image'),
			'SERVICE_ICON_IMAGE_ALT' => array('icon_image_alt'),
			'SERVICE_IN_CHECK_PERIOD' => array('in_check_period'),
			'SERVICE_IN_NOTIFICATION_PERIOD' => array('in_notification_period'),
			'SERVICE_INITIAL_STATE' => array('initial_state'),
			'SERVICE_IS_EXECUTING' => array('is_executing'),
			'SERVICE_IS_FLAPPING' => array('is_flapping'),
			'SERVICE_LAST_CHECK' => array('last_check'),
			'SERVICE_LAST_HARD_STATE' => array('last_hard_state'),
			'SERVICE_LAST_HARD_STATE_CHANGE' => array('last_hard_state_change'),
			'SERVICE_LAST_NOTIFICATION' => array('last_notification'),
			'SERVICE_LAST_STATE' => array('last_state'),
			'SERVICE_LAST_STATE_CHANGE' => array('last_state_change'),
			'SERVICE_LATENCY' => array('latency'),
			'SERVICE_LONG_PLUGIN_OUTPUT' => array('long_plugin_output'),
			'SERVICE_LOW_FLAP_THRESHOLD' => array('low_flap_threshold'),
			'SERVICE_MAX_CHECK_ATTEMPTS' => array('max_check_attempts'),
			'SERVICE_NEXT_CHECK' => array('next_check'),
			'SERVICE_NEXT_NOTIFICATION' => array('next_notification'),
			'SERVICE_NOTES' => array('notes'),
			'SERVICE_NOTES_URL' => array('notes_url'),
			'SERVICE_NOTIFICATION_INTERVAL' => array('notification_interval'),
			'SERVICE_NOTIFICATION_PERIOD' => array('notification_period'),
			'SERVICE_NOTIFICATIONS_ENABLED' => array('notifications_enabled'),
			'SERVICE_PERCENT_STATE_CHANGE' => array('percent_state_change'),
			'SERVICE_PERF_DATA' => array('perf_data'),
			'SERVICE_PLUGIN_OUTPUT' => array('plugin_output'),
			'SERVICE_PROCESS_PERFORMANCE_DATA' => array('process_performance_data'),
			'SERVICE_RETRY_INTERVAL' => array('retry_interval'),
			'SERVICE_SCHEDULED_DOWNTIME_DEPTH' => array('scheduled_downtime_depth'),
			'SERVICE_STATE' => array('state'),
			'SERVICE_STATE_TYPE' => array('state_type'),
		),

/*
		// Config vars
		'CONFIG_VAR_ID' => array('configfilevariable_id'),
		'CONFIG_VAR_INSTANCE_ID' => array('instance_id'),
		'CONFIG_VAR_NAME' => array('varname'),
		'CONFIG_VAR_VALUE' => array('varvalue'),
	
		// Logentries
		'LOG_ID' => array('logentry_id'),
		'LOG_INSTANCE_ID' => array('instance_id'),
		'LOG_TIME' => array('logentry_time'),
		'LOG_ENTRY_TIME' => array('entry_time'),
		'LOG_ENTRY_TIME_USEC' => array('entry_time_usec'),
		'LOG_TYPE' => array('logentry_type'),
		'LOG_DATA' => array('logentry_data'),
		'LOG_REALTIME_DATA' => array('realtime_data'),
		'LOG_INFERRED_DATA' => array('inferred_data_extracted'),

		self::TARGET_LOG => array (
			TODO
		),
	
		// Statehistory
		'STATEHISTORY_ID' => array('statehistory_id'),
		'STATEHISTORY_INSTANCE_ID' => array('instance_id'),
		'STATEHISTORY_STATE_TIME' => array('state_time'),
		'STATEHISTORY_STATE_TIME_USEC' => array('state_time_used'),
		'STATEHISTORY_OBJECT_ID' => array('object_id'),
		'STATEHISTORY_STATE_CHANGE' => array('state_change'),
		'STATEHISTORY_STATE' => array('state'),
		'STATEHISTORY_STATE_TYPE' => array('state_type'),
		'STATEHISTORY_CURRENT_CHECK_ATTEMPT' => array('current_check_attempt'),
		'STATEHISTORY_MAX_CHECK_ATTEMPTS' => array('max_check_attempts'),
		'STATEHISTORY_LAST_STATE' => array('last_state'),
		'STATEHISTORY_LAST_HARD_STATE' => array('last_hard_state'),
		'STATEHISTORY_OUTPUT' => array('output'),
		'STATEHISTORY_LONG_OUTPUT' => array('long_output'),

		// Notifications
		'NOTIFICATION_ID' => array('notification_id'),
		'NOTIFICATION_INSTANCE_ID' => array('instance_id'),
		'NOTIFICATION_TYPE' => array('notification_type'),
		'NOTIFICATION_REASON' => array('notification_reason'),
		'NOTIFICATION_STARTTIME' => array('start_time'),
		'NOTIFICATION_STARTTIME_USEC' => array('start_time_usec'),
		'NOTIFICATION_ENDTIME' => array('end_time'),
		'NOTIFICATION_ENDTIME_USEC' => array('end_time_usec'),
		'NOTIFICATION_STATE' => array('state'),
		'NOTIFICATION_OUTPUT' => array('output'),
		'NOTIFICATION_LONG_OUTPUT' => array('long_output'),
		'NOTIFICATION_ESCALATED' => array('escalated'),
		'NOTIFICATION_NOTIFIED' => array('contacts_notified'),
		'NOTIFICATION_OBJECT_ID' => array('object_id'),
		'NOTIFICATION_OBJECTTYPE_ID' => array('objecttype_id'),

		// Summary queries
		'HOSTGROUP_SUMMARY_COUNT' => array('object_id', 'count(%s)'),
		'SERVICEGROUP_SUMMARY_COUNT' => array('current_state', 'count(%s)'),
*/
		self::TARGET_COMMAND => array (
			'COMMAND_LINE' => array('line'),
			'COMMAND_LINE' => array('name'),
		),

		self::TARGET_DOWNTIME => array (
			'DOWNTIME_AUTHOR' => array('author'),
			'DOWNTIME_COMMENT' => array('comment'),
			'DOWNTIME_DURATION' => array('duration'),
			'DOWNTIME_END_TIME' => array('end_time'),
			'DOWNTIME_ENTRY_TIME' => array('entry_time'),
			'DOWNTIME_FIXED' => array('fixed'),
			'DOWNTIME_HOST_ACCEPT_PASSIVE_CHECKS' => array('host_accept_passive_checks'),
			'DOWNTIME_HOST_ACKNOWLEDGED' => array('host_acknowledged'),
			'DOWNTIME_HOST_ACKNOWLEDGEMENT_TYPE' => array('host_acknowledgement_type'),
			'DOWNTIME_HOST_ACTION_URL' => array('host_action_url'),
			'DOWNTIME_HOST_ACTIVE_CHECKS_ENABLED' => array('host_active_checks_enabled'),
			'DOWNTIME_HOST_ADDRESS' => array('host_address'),
			'DOWNTIME_HOST_ALIAS' => array('host_alias'),
			'DOWNTIME_HOST_CHECK_COMMAND' => array('host_check_command'),
			'DOWNTIME_HOST_CHECK_FRESHNESS' => array('host_check_freshness'),
			'DOWNTIME_HOST_CHECK_INTERVAL' => array('host_check_interval'),
			'DOWNTIME_HOST_CHECK_OPTIONS' => array('host_check_options'),
			'DOWNTIME_HOST_CHECK_PERIOD' => array('host_check_period'),
			'DOWNTIME_HOST_CHECK_TYPE' => array('host_check_type'),
			'DOWNTIME_HOST_CHECK_ENABLED' => array('host_checks_enabled'),
			'DOWNTIME_HOST_CHILDS' => array('host_childs'),
			'DOWNTIME_HOST_CONTACTS' => array('host_contacts'),
			'DOWNTIME_HOST_CURRENT_ATTEMPT' => array('host_current_attempt'),
			'DOWNTIME_HOST_CURRENT_NOTIFICATION_NUMBER' => array('host_current_notification_number'),
			'DOWNTIME_HOST_CUSTOM_VARIABLE_NAMES' => array('host_custom_variable_names'),
			'DOWNTIME_HOST_CUSTOM_VARIABLE_NUMBERS' => array('host_custom_variable_values'),
			'DOWNTIME_HOST_DISPLAY_NAME' => array('host_display_name'),
			'DOWNTIME_HOST_DOWNTIMES' => array('host_downtimes'),
			'DOWNTIME_HOST_EVENT_HANDLER_ENABLED' => array('host_event_handler_enabled'),
			'DOWNTIME_HOST_EXECUTION_TIME' => array('host_execution_time'),
			'DOWNTIME_HOST_FIRST_NOTIFICATION_DELAY' => array('host_first_notification_delay'),
			'DOWNTIME_HOST_FLAP_DETECTION_ENABLED' => array('host_flap_detection_enabled'),
			'DOWNTIME_HOST_HOST_GROUPS' => array('host_groups'),
			'DOWNTIME_HOST_HARD_STATE' => array('host_hard_state'),
			'DOWNTIME_HOST_HAS_BEEN_CHECKED' => array('host_has_been_checked'),
			'DOWNTIME_HOST_HIGH_FLAP_THRESHOLD' => array('host_high_flap_threshold'),
			'DOWNTIME_HOST_ICON_IMAGE' => array('host_icon_image'),
			'DOWNTIME_HOST_ICON_IMAGE_ALT' => array('host_icon_image_alt'),
			'DOWNTIME_HOST_IN_CHECK_PERIOD' => array('host_in_check_period'),
			'DOWNTIME_HOST_IN_NOTIFICATION_PERIOD' => array('host_in_notification_period'),
			'DOWNTIME_HOST_INITIAL_STATE' => array('host_initial_state'),
			'DOWNTIME_HOST_IS_EXECUTING' => array('host_is_executing'),
			'DOWNTIME_HOST_IS_FLAPPING' => array('host_is_flapping'),
			'DOWNTIME_HOST_LAST_CHECK' => array('host_last_check'),
			'DOWNTIME_HOST_LAST_HARD_STATE' => array('host_last_hard_state'),
			'DOWNTIME_HOST_LAST_HARD_STATE_CHANGE' => array('host_last_hard_state_change'),
			'DOWNTIME_HOST_LAST_NOTIFICATION' => array('host_last_notification'),
			'DOWNTIME_HOST_LAST_STATE' => array('host_last_state'),
			'DOWNTIME_HOST_LAST_STATE_CHANGE' => array('host_last_state_change'),
			'DOWNTIME_HOST_LATENCY' => array('host_latency'),
			'DOWNTIME_HOST_LONG_PLUGIN_OUTPUT' => array('host_long_plugin_output'),
			'DOWNTIME_HOST_LOW_FLAP_THRESHOLD' => array('host_low_flap_threshold'),
			'DOWNTIME_HOST_MAX_CHECK_ATTEMPTS' => array('host_max_check_attempts'),
			'DOWNTIME_HOST_NAME' => array('host_name'),
			'DOWNTIME_HOST_NEXT_CHECK' => array('host_next_check'),
			'DOWNTIME_HOST_NEXT_NOTIFICATION' => array('host_next_notification'),
			'DOWNTIME_HOST_NOTES' => array('host_notes'),
			'DOWNTIME_HOST_NOTES_URL' => array('host_notes_url'),
			'DOWNTIME_HOST_NOTIFICATION_INTERVAL' => array('host_notification_interval'),
			'DOWNTIME_HOST_NOTITICATION_PERIOD' => array('host_notification_period'),
			'DOWNTIME_HOST_NOTITICATIONS_ENABLED' => array('host_notifications_enabled'),
			'DOWNTIME_HOST_NUM_SERVICES' => array('host_num_services'),
			'DOWNTIME_HOST_NUM_SERVICES_CRIT' => array('host_num_services_crit'),
			'DOWNTIME_HOST_NUM_SERVICES_HARD_CRIT' => array('host_num_services_hard_crit'),
			'DOWNTIME_HOST_NUM_SERVICES_HARD_OK' => array('host_num_services_hard_ok'),
			'DOWNTIME_HOST_NUM_SERVICES_HARD_UNKNOWN' => array('host_num_services_hard_unknown'),
			'DOWNTIME_HOST_NUM_SERVICES_HARD_WARN' => array('host_num_services_hard_warn'),
			'DOWNTIME_HOST_NUM_SERVICES_OK' => array('host_num_services_ok'),
			'DOWNTIME_HOST_NUM_SERVICES_UNKNOWN' => array('host_num_services_unknown'),
			'DOWNTIME_HOST_NUM_SERVICES_WARN' => array('host_num_services_warn'),
			'DOWNTIME_HOST_OBSESS_OVER_HOST' => array('host_obsess_over_host'),
			'DOWNTIME_HOST_PARENTS' => array('host_parents'),
			'DOWNTIME_HOST_PENDING_FLEX_DOWNTIME' => array('host_pending_flex_downtime'),
			'DOWNTIME_HOST_PERCENT_STATE_CHANGE' => array('host_percent_state_change'),
			'DOWNTIME_HOST_PERF_DATA' => array('host_perf_data'),
			'DOWNTIME_HOST_PLUGIN_OUTPUT' => array('host_plugin_output'),
			'DOWNTIME_HOST_PROCESS_PERFORMANCE_DATA' => array('host_process_performance_data'),
			'DOWNTIME_HOST_RETRY_INTERVAL' => array('host_retry_interval'),
			'DOWNTIME_HOST_SCHEDULED_DOWNTIME_DEPTH' => array('host_scheduled_downtime_depth'),
			'DOWNTIME_HOST_STATE' => array('host_state'),
			'DOWNTIME_HOST_STATE_TYPE' => array('host_state_type'),
			'DOWNTIME_HOST_STATUSMAP_IMAGE' => array('host_statusmap_image'),
			'DOWNTIME_HOST_TOTAL_SERVICES' => array('host_total_services'),
			'DOWNTIME_HOST_WORST_SERVICE_HARD_STATE' => array('host_worst_service_hard_state'),
			'DOWNTIME_HOST_WORST_SERVICE_STATE' => array('host_worst_service_state'),
			'DOWNTIME_HOST_X_3D' => array('host_x_3d'),
			'DOWNTIME_HOST_Y_3D' => array('host_y_3d'),
			'DOWNTIME_HOST_Z_3D' => array('host_z_3d'),
			'DOWNTIME_ID' => array('id'),
			'DOWNTIME_SERVICE_ACCEPT_PASSIVE_CHECKS' => array('service_accept_passive_checks'),
			'DOWNTIME_SERVICE_ACKNOWLEDGED' => array('service_acknowledged'),
			'DOWNTIME_SERVICE_ACKNOWLEDGEMENT_TYPE' => array('service_acknowledgement_type'),
			'DOWNTIME_SERVICE_ACTION_URL' => array('service_action_url'),
			'DOWNTIME_SERVICE_ACTIVE_CHECKS_ENABLED' => array('service_active_checks_enabled'),
			'DOWNTIME_SERVICE_CHECK_COMMAND' => array('service_check_command'),
			'DOWNTIME_SERVICE_CHECK_INTERVAL' => array('service_check_interval'),
			'DOWNTIME_SERVICE_CHECK_OPTIONS' => array('service_check_options'),
			'DOWNTIME_SERVICE_CHECK_PERIOD' => array('service_check_period'),
			'DOWNTIME_SERVICE_CHECK_TYPE' => array('service_check_type'),
			'DOWNTIME_SERVICE_CHECKS_ENABLED' => array('service_checks_enabled'),
			'DOWNTIME_SERVICE_CONTACTS' => array('service_contacts'),
			'DOWNTIME_SERVICE_CURRENT_ATTEMPT' => array('service_current_attempt'),
			'DOWNTIME_SERVICE_CURRENT_NOTIFICATION_NUMBER' => array('service_current_notification_number'),
			'DOWNTIME_SERVICE_CUSTOM_VARIABLE_NAMES' => array('service_custom_variable_names'),
			'DOWNTIME_SERVICE_CUSTOM_VARIABLE_VALUES' => array('service_custom_variable_values'),
			'DOWNTIME_SERVICE_DESCRIPTION' => array('service_description'),
			'DOWNTIME_SERVICE_DISPLAY_NAME' => array('service_display_name'),
			'DOWNTIME_SERVICE_DOWNTIMES' => array('service_downtimes'),
			'DOWNTIME_SERVICE_EVENT_HANDLER' => array('service_event_handler'),
			'DOWNTIME_SERVICE_EVENT_HANDLER_ENABLED' => array('service_event_handler_enabled'),
			'DOWNTIME_SERVICE_EXECUTION_TIME' => array('service_execution_time'),
			'DOWNTIME_SERVICE_FIRST_NOTIFICATION_DELAY' => array('service_first_notification_delay'),
			'DOWNTIME_SERVICE_GROUPS' => array('service_groups'),
			'DOWNTIME_SERVICE_HAS_BEEN_CHECKED' => array('service_has_been_checked'),
			'DOWNTIME_SERVICE_HIGH_FLAP_THRESHOLD' => array('service_high_flap_threshold'),
			'DOWNTIME_SERVICE_ICON_IMAGE' => array('service_icon_image'),
			'DOWNTIME_SERVICE_ICON_IMAGE_ALT' => array('service_icon_image_alt'),
			'DOWNTIME_SERVICE_IN_CHECK_PERIOD' => array('service_in_check_period'),
			'DOWNTIME_SERVICE_IN_NOTIFICATION_PERIOD' => array('service_in_notification_period'),
			'DOWNTIME_SERVICE_INITIAL_STATE' => array('service_initial_state'),
			'DOWNTIME_SERVICE_IS_EXECUTING' => array('service_is_executing'),
			'DOWNTIME_SERVICE_IS_FLAPPING' => array('service_is_flapping'),
			'DOWNTIME_SERVICE_LAST_CHECK' => array('service_last_check'),
			'DOWNTIME_SERVICE_LAST_HARD_STATE' => array('service_last_hard_state'),
			'DOWNTIME_SERVICE_LAST_HARD_STATE_CHANGE' => array('service_last_hard_state_change'),
			'DOWNTIME_SERVICE_LAST_NOTIFICATION' => array('service_last_notification'),
			'DOWNTIME_SERVICE_LAST_STATE' => array('service_last_state'),
			'DOWNTIME_SERVICE_LAST_STATE_CHANGE' => array('service_last_state_change'),
			'DOWNTIME_SERVICE_LATENCY' => array('service_latency'),
			'DOWNTIME_SERVICE_LONG_PLUGIN_OUTPUT' => array('service_long_plugin_output'),
			'DOWNTIME_SERVICE_LOW_FLAP_THRESHOLD' => array('service_low_flap_threshold'),
			'DOWNTIME_SERVICE_MAX_CHECK_ATTEMPTS' => array('service_max_check_attempts'),
			'DOWNTIME_SERVICE_NEXT_CHECK' => array('service_next_check'),
			'DOWNTIME_SERVICE_NEXT_NOTIFICATION' => array('service_next_notification'),
			'DOWNTIME_SERVICE_NOTES' => array('service_notes'),
			'DOWNTIME_SERVICE_NOTES_URL' => array('service_notes_url'),
			'DOWNTIME_SERVICE_NOTIFICATION_INTERVAL' => array('service_notification_interval'),
			'DOWNTIME_SERVICE_NOTIFICATION_PERIOD' => array('service_notification_period'),
			'DOWNTIME_SERVICE_NOTIFICATIONS_ENABLED' => array('service_notifications_enabled'),
			'DOWNTIME_SERVICE_PERCENT_STATE_CHANGE' => array('service_percent_state_change'),
			'DOWNTIME_SERVICE_PERF_DATA' => array('service_perf_data'),
			'DOWNTIME_SERVICE_PLUGIN_OUTPUT' => array('service_plugin_output'),
			'DOWNTIME_SERVICE_PROCESS_PERFORMANCE_DATA' => array('service_process_performance_data'),
			'DOWNTIME_SERVICE_RETRY_INTERVAL' => array('service_retry_interval'),
			'DOWNTIME_SERVICE_SCHEDULED_DOWNTIME_DEPTH' => array('service_scheduled_downtime_depth'),
			'DOWNTIME_SERVICE_STATE' => array('service_state'),
			'DOWNTIME_SERVICE_STATE_TYPE' => array('service_state_type'),
			'DOWNTIME_START_TIME' => array('start_time'),
			'DOWNTIME_TRIGGERED_BY' => array('triggered_by'),
			'DOWNTIME_TYPE' => array('type'),
		),

		/*
		self::TARGET_COMMENT => array (
			TODO
		),
		*/

		self::TARGET_STATUS => array (
			'STATUS_CONNECTIONS' => array('connections'),
			'STATUS_CONNECTIONS_RATE' => array('connections_rate'),
			'STATUS_HOST_CHECKS' => array('host_checks'),
			'STATUS_HOST_CHECKS_RATE' => array('host_checks_rate'),
			'STATUS_NEB_CALLBACKS' => array('neb_callbacks'),
			'STATUS_NEB_CALLBACKS_RATE' => array('neb_callbacks_rate'),
			'STATUS_REQUESTS' => array('requests'),
			'STATUS_REQUESTS_RATE' => array('requests_rate'),
			'STATUS_SERVICE_CHECKS' => array('service_checks'),
			'STATUS_SERVICE_CHECKS_RATE' => array('service_checks_rate'),
		),
	);

	/*
	 * METHODS
	 */

}

?>