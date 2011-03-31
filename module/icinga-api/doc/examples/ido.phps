<?

$apiFile = 'icinga-api/IcingaApi.php';
$idoConfig = array (
	'type'			=> 'mysql',
	'host'			=> 'localhost',
	'database'		=> 'icinga',
	'user'			=> 'icinga',
	'password'		=> 'icinga',
	'table_prefix'	=> 'icinga_',
);

require_once($apiFile);

$api = IcingaApi::getConnection(IcingaApi::CONNECTION_IDO, $idoConfig);

/*
 * query for program date
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_PROGRAM)
	->setResultColumns('PROGRAM_DATE)
	->fetch();

/*
 * query for contact group by contact
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_CONTACTGROUP')
	->setResultColumns('CONTACTGROUP_NAME')
	->setSearchFilter('CONTACT_NAME', 'nagios-admin')
	->fetch();

/*
 * query for host status
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_HOST)
	->setResultColumns(array('HOST_NAME', 'HOST_ALIAS', 'HOST_DISPLAY_NAME', 'HOST_ADDRESS', 'HOST_OUTPUT', 'HOST_PERFDATA', 'HOST_CURRENT_STATE', 'HOST_CURRENT_CHECK_ATTEMPT', 'HOST_MAX_CHECK_ATTEMPTS', 'HOST_LAST_CHECK', 'HOST_LAST_STATE_CHANGE', 'HOST_CHECK_TYPE', 'HOST_LATENCY', 'HOST_EXECUTION_TIME', 'HOST_NEXT_CHECK', 'HOST_HAS_BEEN_CHECKED', 'HOST_LAST_HARD_STATE_CHANGE', 'HOST_LAST_NOTIFICATION', 'HOST_STATE_TYPE', 'HOST_IS_FLAPPING', 'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'HOST_SCHEDULED_DOWNTIME_DEPTH', 'HOST_STATUS_UPDATE_TIME'))
	->setSearchFilter('CONTACTGROUP_NAME', '%', IcingaApi::MATCH_LIKE)
	->setSearchFilter('HOST_NAME', 'localhost')
	->setSearchGroup('HOST_ID')
	->setSearchOrder('HOST_NAME')
	->fetch();

/*
 * query for host execution time and latency
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_HOST)
	->setResultColumns(array('HOST_EXECUTION_TIME_MIN', 'HOST_EXECUTION_TIME_AVG', 'HOST_EXECUTION_TIME_MAX', 'HOST_LATENCY_MIN', 'HOST_LATENCY_AVG', 'HOST_LATENCY_MAX'))
	->setSearchFilter('CONTACTGROUP_NAME', '%', IcingaApi::MATCH_LIKE)
	->setSearchFilter('HOST_NAME', 'localhost')
	->setSearchGroup('HOST_CONFIG_TYPE')
	->fetch();

/*
 * query for active hosts
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_HOST)
	->setSearchType(IcingaApi::SEARCH_TYPE_COUNT)
	->setResultColumns('HOST_OBJECT_ID')
	->setSearchFilter('CONTACTGROUP_NAME', '%', IcingaApi::MATCH_LIKE)
	->setSearchFilter('HOST_ACTIVE_CHECKS_ENABLED', 1)
	->setSearchGroup('HOST_IS_ACTIVE')
	->fetch();

/*
 * query for service execution time and latency
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_SERVICE)
	->setResultColumns(array('SERVICE_EXECUTION_TIME_MIN', 'SERVICE_EXECUTION_TIME_AVG', 'SERVICE_EXECUTION_TIME_MAX', 'SERVICE_LATENCY_MIN', 'SERVICE_LATENCY_AVG', 'SERVICE_LATENCY_MAX'))
	->setSearchFilter(array(
		array('CONTACTGROUP_NAME', '%', IcingaApi::MATCH_LIKE),
		array('HOST_NAME', 'localhost'),
		array('SERVICE_NAME', 'PING'),
	))
	->fetch();

/*
 * query for service status
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_SERVICE)
	->setResultColumns(array('HOST_STATUS_ALL', 'SERVICE_NAME', 'SERVICE_DISPLAY_NAME', 'SERVICE_NOTIFICATIONS_ENABLED', 'SERVICE_OUTPUT', 'SERVICE_PERFDATA', 'SERVICE_CURRENT_STATE', 'SERVICE_CURRENT_CHECK_ATTEMPT', 'SERVICE_MAX_CHECK_ATTEMPTS', 'SERVICE_LAST_CHECK', 'SERVICE_LAST_STATE_CHANGE', 'SERVICE_CHECK_TYPE', 'SERVICE_LATENCY', 'SERVICE_EXECUTION_TIME', 'SERVICE_NEXT_CHECK', 'SERVICE_HAS_BEEN_CHECKED', 'SERVICE_LAST_HARD_STATE_CHANGE', 'SERVICE_LAST_NOTIFICATION', 'SERVICE_STATE_TYPE', 'SERVICE_IS_FLAPPING', 'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'SERVICE_SCHEDULED_DOWNTIME_DEPTH', 'SERVICE_STATUS_UPDATE_TIME'))
	->setSearchFilter(array(
		array('CONTACTGROUP_NAME', '%', IcingaApi::MATCH_LIKE),
		array('HOST_NAME', 'localhost'),
		array('SERVICE_NAME', 'PING'),
	))
	->setSearchGroup('SERVICE_ID')
	->setSearchOrder(array('HOST_NAME', 'SERVICE_NAME'))
	->fetch();

/*
 * query for config variables
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_CONFIG)
	->setResultColumns('CONFIG_VAR_NAME')
	->setResultColumns('CONFIG_VAR_VALUE')
	->setSearchFilter('CONFIG_VAR_NAME', '%file', IcingaApi::MATCH_LIKE)
	->setSearchOrder('CONFIG_VAR_NAME')
	->fetch();

/*
 * query for host status summary
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_HOST_STATUS_SUMMARY)
	->fetch();

/*
 * query for service status summary
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_SERVICE_STATUS_SUMMARY)
	->fetch();

/*
 * add custom-search-filter appendix
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_HOST)
	->setResultColumns(array('HOST_NAME', 'HOST_ALIAS', 'HOST_DISPLAY_NAME', 'HOST_ADDRESS',))
	->setSearchFilter('HOST_NAME', 'www%', IcingaApi::MATCH_LIKE)
	->setSearchFilterAppendix('${CONTACT_NAME} like \'%admin\'', IcingaApi::SEARCH_AND)
	->setSearchOrder('HOST_NAME')
	->fetch();

/*
 * fetch complete result set as array
 */
$resultArray = $api->createSearch()
	->setResultType(IcingaApi::RESULT_ARRAY)
	->setSearchTarget(IcingaApi::TARGET_HOST_STATUS_SUMMARY)
	->fetch()
	->getAll();

?>
