<?

$apiFile = 'icinga-api/IcingaApi.php';
$idoConfig = array (
	'type'			=> 'mysql',
	'host'			=> 'localhost',
	'database'		=> 'icinga',
	'user'			=> 'icinga',
	'password'		=> 'icinga',
	'table_prefix'	=> 'icinga_',
	'contact_source'	=> IcingaApi::CONTACT_SOURCE_PHP_AUTH_USER,		// NOTE: since this way enables the API to determine the contact name on its own IT IS A SECURITY RISK!
	                                                                    //       ONLY LAZY CODERS SHOULD USE THIS FEATURE!
);

require_once($apiFile);

$api = IcingaApi::getConnection(IcingaApi::CONNECTION_IDO, $idoConfig);

/*
 * query for service status
 */
$apiRes = $api->createSearch()
	->setSearchTarget(IcingaApi::TARGET_SERVICE)
	->setResultColumns(array('HOST_STATUS_ALL', 'SERVICE_NAME', 'SERVICE_DISPLAY_NAME', 'SERVICE_NOTIFICATIONS_ENABLED', 'SERVICE_OUTPUT', 'SERVICE_PERFDATA', 'SERVICE_CURRENT_STATE', 'SERVICE_CURRENT_CHECK_ATTEMPT', 'SERVICE_MAX_CHECK_ATTEMPTS', 'SERVICE_LAST_CHECK', 'SERVICE_LAST_STATE_CHANGE', 'SERVICE_CHECK_TYPE', 'SERVICE_LATENCY', 'SERVICE_EXECUTION_TIME', 'SERVICE_NEXT_CHECK', 'SERVICE_HAS_BEEN_CHECKED', 'SERVICE_LAST_HARD_STATE_CHANGE', 'SERVICE_LAST_NOTIFICATION', 'SERVICE_STATE_TYPE', 'SERVICE_IS_FLAPPING', 'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED', 'SERVICE_SCHEDULED_DOWNTIME_DEPTH', 'SERVICE_STATUS_UPDATE_TIME'))
	->setSearchFilter(array(
		array('HOST_NAME', 'localhost'),
		array('SERVICE_NAME', 'PING'),
	))
	->setSearchGroup('SERVICE_ID')
	->setSearchOrder(array('HOST_NAME', 'SERVICE_NAME'))
	->fetch();

?>