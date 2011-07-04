<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiSearchColumnsFile {

	// RESULT COLUMNS
	public $result = array(
		'PROGRAM_DATE' => array('info', 'created', array('timeEpochToIso', 'program_date')),
		'PROGRAM_VERSION' => array('info', 'version', 'program_version'),
//		'CONTACTGROUP_NAME' => 'contactgroup_name',
//		'CONTACTGROUP_ALIAS' => 'cg.alias contactgroup_alias',
//		'HOST_ID' => 'h.host_id',
//		'HOST_OBJECT_ID' => 'oh.object_id host_object_id',
		'HOST_NAME' => array(array('servicestatus', 'service', 'serviceescalation', 'servicedowntime', 'servicedependency', 'servicecomment', 'hoststatus', 'host', 'hostescalation', 'hostdowntime', 'hostdependency', 'hostcomment'), 'host_name', 'host_name'),
		'HOST_ALIAS' => array('host', 'alias', 'host_alias'),
//		'HOST_DISPLAY_NAME' => 'h.display_name host_display_name',
		'HOST_ADDRESS' => array('host', 'address', 'host_address'),
		'HOST_ACTIVE_CHECKS_ENABLED' => array(array('hoststatus', 'host'), 'active_checks_enabled', 'host_active_checks_enabled'),
		'HOST_PASSIVE_CHECKS_ENABLED' => array(array('hoststatus', 'host'), 'passive_checks_enabled', 'host_passive_checks_enabled'),
//		'HOST_CONFIG_TYPE' => 'h.config_type host_config_type',
//		'HOST_IS_ACTIVE' => 'oh.is_active host_is_active',
		'HOST_OUTPUT' => array('hoststatus', 'plugin_output', 'host_output'),
		'HOST_PERFDATA' => array('hoststatus', 'performance_data', 'host_perfdata'),
		'HOST_CURRENT_STATE' => array('hoststatus', 'current_state', 'host_current_state'),
		'HOST_CURRENT_CHECK_ATTEMPT' => array('hoststatus', 'current_attempt', 'host_current_check_attempt'),
		'HOST_MAX_CHECK_ATTEMPTS' => array(array('hoststatus', 'host'), 'max_attempts', 'host_max_check_attempts'),
		'HOST_LAST_CHECK' => array('hoststatus', 'last_update', array('timeEpochToIso', 'host_last_check')),
		'HOST_LAST_STATE_CHANGE' => array('hoststatus', 'last_state_change', array('timeEpochToIso', 'host_last_state_change')),
		'HOST_CHECK_TYPE' => array('hoststatus', 'check_type', 'host_check_type'),
		'HOST_LATENCY' => array('hoststatus', 'check_latency', 'host_latency'),
		'HOST_EXECUTION_TIME' => array('hoststatus', 'check_execution_time', 'host_execution_time'),
		'HOST_NEXT_CHECK' => array('hoststatus', 'next_check', array('timeEpochToIso', 'host_next_check')),
		'HOST_HAS_BEEN_CHECKED' => array('hoststatus', 'has_been_checked host_has_been_checked'),
		'HOST_LAST_HARD_STATE_CHANGE' => array('hoststatus', 'last_hard_state_change', array('timeEpochToIso', 'host_last_hard_state_change')),
		'HOST_LAST_NOTIFICATION' => array('hoststatus', 'last_notification', array('timeEpochToIso', 'host_last_notification')),
		'HOST_STATE_TYPE' => array('hoststatus', 'state_type', 'host_state_type'),
		'HOST_IS_FLAPPING' => array('hoststatus', 'is_flapping host_is_flapping'),
		'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('hoststatus', 'problem_has_been_acknowledged', 'host_problem_has_been_acknowledged'),
		'HOST_SCHEDULED_DOWNTIME_DEPTH' => array('hoststatus', 'scheduled_downtime_depth', 'host_scheduled_downtime_depth'),
//		'HOST_STATUS_UPDATE_TIME' => 'hs.status_update_time host_status_update_time',
//		'HOST_EXECUTION_TIME_MIN' => 'min(hs.execution_time) host_min_execution_time',
//		'HOST_EXECUTION_TIME_AVG' => 'avg(hs.execution_time) host_avg_execution_time',
//		'HOST_EXECUTION_TIME_MAX' => 'max(hs.execution_time) host_max_execution_time',
//		'HOST_LATENCY_MIN' => 'min(hs.latency) host_min_latency',
//		'HOST_LATENCY_AVG' => 'avg(hs.latency) host_avg_latency',
//		'HOST_LATENCY_MAX' => 'max(hs.latency) host_max_latency',
//		'HOST_ALL' => 'h.*',
		'HOST_STATUS_ALL' => array('hoststatus'),
//		'SERVICE_ID' => 's.service_id',
//		'SERVICE_CONFIG_TYPE' => 's.config_type service_config_type',
//		'SERVICE_IS_ACTIVE' => 'os.is_active service_is_active',
//		'SERVICE_OBJECT_ID' => 'os.object_id service_object_id',
		'SERVICE_NAME' => array(array('servicestatus', 'service', 'servicegroup', 'serviceescalation', 'servicedowntime', 'servicedependency', 'servicecomment'), 'service_description', 'service_name'),
//		'SERVICE_DISPLAY_NAME' => 's.display_name service_display_name',
		'SERVICE_NOTIFICATIONS_ENABLED' => array(array('servicestatus', 'service'), 'notifications_enabled', 'service_notifications_enabled'),
		'SERVICE_OUTPUT' => array('servicestatus', 'plugin_output', 'service_output'),
		'SERVICE_PERFDATA' => array('servicestatus', 'performance_data', 'service_perfdata'),
		'SERVICE_CURRENT_STATE' => array('servicestatus', 'current_state', 'service_current_state'),
		'SERVICE_CURRENT_CHECK_ATTEMPT' => array('servicestatus', 'current_attempt', 'service_current_check_attempt'),
		'SERVICE_MAX_CHECK_ATTEMPTS' => array(array('servicestatus', 'service'), 'max_attempts', 'service_max_check_attempts'),
		'SERVICE_LAST_CHECK' => array('servicestatus', 'last_check', array('timeEpochToIso', 'timeservice_last_check')),
		'SERVICE_LAST_STATE_CHANGE' => array('servicestatus', 'last_state_change', array('timeEpochToIso', 'service_last_state_change')),
		'SERVICE_CHECK_TYPE' => array('servicestatus', 'check_type', 'service_check_type'),
		'SERVICE_LATENCY' => array('servicestatus', 'check_latency', 'service_latency'),
		'SERVICE_EXECUTION_TIME' => array('servicestatus', 'check_execution_time', 'service_execution_time'),
		'SERVICE_NEXT_CHECK' => array('servicestatus', 'next_check', array('timeEpochToIso', 'service_next_check')),
		'SERVICE_HAS_BEEN_CHECKED' => array('servicestatus', 'has_been_checked', 'service_has_been_checked'),
		'SERVICE_LAST_HARD_STATE_CHANGE' => array('servicestatus', 'last_hard_state_change', array('timeEpochToIso', 'service_last_hard_state_change')),
		'SERVICE_LAST_NOTIFICATION' => array('servicestatus', 'last_notification', array('timeEpochToIso', 'service_last_notification')),
		'SERVICE_STATE_TYPE' => array('servicestatus', 'state_type', 'service_state_type'),
		'SERVICE_IS_FLAPPING' => array('servicestatus', 'is_flapping', 'service_is_flapping'),
		'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('servicestatus', 'problem_has_been_acknowledged', 'service_problem_has_been_acknowledged'),
		'SERVICE_SCHEDULED_DOWNTIME_DEPTH' => array('servicestatus', 'scheduled_downtime_depth', 'service_scheduled_downtime_depth'),
		'SERVICE_STATUS_UPDATE_TIME' => array('servicestatus', 'status_update_time', array('timeEpochToIso', 'service_status_update_time')),
//		'SERVICE_EXECUTION_TIME_MIN' => 'min(ss.execution_time) service_min_execution_time',
//		'SERVICE_EXECUTION_TIME_AVG' => 'avg(ss.execution_time) service_avg_execution_time',
//		'SERVICE_EXECUTION_TIME_MAX' => 'max(ss.execution_time) service_max_execution_time',
//		'SERVICE_LATENCY_MIN' => 'min(ss.latency) service_min_latency',
//		'SERVICE_LATENCY_AVG' => 'avg(ss.latency) service_avg_latency',
//		'SERVICE_LATENCY_MAX' => 'max(ss.latency) service_max_latency',
		'SERVICE_ALL' => array('service'),
		'SERVICE_STATUS_ALL' => array('servicestatus'),
//		'CONFIG_VAR_NAME' => 'cfv.varname config_var_name',
//		'CONFIG_VAR_VALUE' => 'cfv.varvalue config_var_value',
	);

	// COLUMNS FOR INSERTION
	public $insert = false;
	
}

?>