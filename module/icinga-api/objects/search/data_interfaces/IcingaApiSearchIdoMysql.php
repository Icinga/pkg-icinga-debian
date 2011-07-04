<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiSearchIdoMysql
	extends IcingaApiSearch
	implements IcingaApiSearchIdoInterface {

	/*
	 * VARIABLES
	 */

	public $tablePrefix = 'icinga_';
	public $postProcess = false;

	public $clearVariables = array();

	public $statements = array (
		'fieldsSuffix'	=> false,
		'group'			=> ' group by %s ',
		'order'			=> ' order by ',
		'limit'			=> ' limit %s '
	);

	public $queryMap = array (

		self::TARGET_INSTANCE =>
			'select
				${FIELDS}
			from ${TABLE_PREFIX}instances i
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',

		self::TARGET_HOST =>
			'select
				distinct ${FIELDS}
			from
				${TABLE_PREFIX}objects oh
			${if_table:h:inner join ${TABLE_PREFIX}hosts h on h.host_object_id = oh.object_id}
			${if_table:hs:inner join ${TABLE_PREFIX}hoststatus hs on hs.host_object_id = oh.object_id}
			${if_table:i,h:inner join ${TABLE_PREFIX}instances i on i.instance_id = h.instance_id}
			${if_table:hcg,h:inner join ${TABLE_PREFIX}host_contactgroups hcg on hcg.host_id = h.host_id}
			${if_table:cg,h:inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = hcg.contactgroup_object_id}
			${if_table:ocg,hcg,h:inner join ${TABLE_PREFIX}objects ocg on ocg.object_id = hcg.contactgroup_object_id and ocg.objecttype_id = 11}
			${if_table:cgm,cg,hcg,h:inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg,hcg,h:inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id}
			${if_table:hgm:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.host_object_id = oh.object_id}
			${if_table:hg,hgm:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_id = hgm.hostgroup_id}
			${if_table:ohg,hg,hgm:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id}
			${if_table:cvsh,oh:inner join ${TABLE_PREFIX}customvariablestatus cvsh on oh.object_id = cvsh.object_id}
			${if_table:cvsc,oc,cgm,cg,hcg,h:inner join ${TABLE_PREFIX}customvariablestatus cvsc on oc.object_id = cvsc.object_id}
			where
				oh.objecttype_id = 1
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_SERVICE =>
			'select
				distinct ${FIELDS}
			from
				${TABLE_PREFIX}objects os
			${if_table:s:inner join ${TABLE_PREFIX}services s on s.service_object_id = os.object_id}
			${if_table:i,s:inner join ${TABLE_PREFIX}instances i on i.instance_id = s.instance_id}
			${if_table:scg,s:inner join ${TABLE_PREFIX}service_contactgroups scg on scg.service_id = s.service_id}
			${if_table:cg,scg,s:inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = scg.contactgroup_object_id}
			${if_table:cgm,cg,scg,s:inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg,scg,s:inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id}
			${if_table:ss:inner join ${TABLE_PREFIX}servicestatus ss on ss.service_object_id = os.object_id}
			${if_table:ocg,scg,s:inner join ${TABLE_PREFIX}objects ocg on ocg.object_id = scg.contactgroup_object_id and ocg.objecttype_id = 11}
			${if_table:hs,s:inner join ${TABLE_PREFIX}hoststatus hs on hs.host_object_id = s.host_object_id}
			${if_table:oh,s:inner join ${TABLE_PREFIX}objects oh on oh.object_id = s.host_object_id and oh.objecttype_id = 1}
			${if_table:sgm:left join icinga_servicegroup_members sgm on sgm.service_object_id = os.object_id}
			${if_table:sg,sgm:left join icinga_servicegroups sg on sg.servicegroup_id = sgm.servicegroup_id}
			${if_table:osg,sg,sgm:left join icinga_objects osg on osg.object_id = sg.servicegroup_object_id}
			${if_table:hgm,oh,s:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.host_object_id = oh.object_id}
			${if_table:hg,hgm,oh,s:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_id = hgm.hostgroup_id}
			${if_table:ohg,hg,hgm,oh,s:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id}
			${if_table:cvsh,oh,s:inner join ${TABLE_PREFIX}customvariablestatus cvsh on oh.object_id = cvsh.object_id}
			${if_table:cvss:inner join ${TABLE_PREFIX}customvariablestatus cvss on os.object_id = cvss.object_id}
			${if_table:cvsc,oc,cgm,cg,scg,s:inner join ${TABLE_PREFIX}customvariablestatus cvsc on oc.object_id = cvsc.object_id}
			where
				os.objecttype_id = 2
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_HOSTGROUP =>
			'select
				distinct ${FIELDS}
			from
				${TABLE_PREFIX}objects ohg
			${if_table:hg:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_object_id = ohg.object_id}
			${if_table:hgm,hg:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.hostgroup_id = hg.hostgroup_id}
			${if_table:oh,hgm,hg:inner join ${TABLE_PREFIX}objects oh on oh.object_id = hgm.host_object_id and oh.objecttype_id = 1}
			where
				ohg.objecttype_id = 3
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_SERVICEGROUP =>
			'select
				distinct ${FIELDS}
			from
				${TABLE_PREFIX}objects osg
			${if_table:sg:inner join ${TABLE_PREFIX}servicegroups sg on sg.servicegroup_object_id = osg.object_id}
			${if_table:sgm,sg:inner join ${TABLE_PREFIX}servicegroup_members sgm on sgm.servicegroup_id = sg.servicegroup_id}
			${if_table:os,sgm,sg:inner join ${TABLE_PREFIX}objects os on os.object_id = sgm.service_object_id and os.objecttype_id = 2}
			where
				osg.objecttype_id=4
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_CONTACTGROUP =>
			'select
				distinct ${FIELDS}
			from
				 ${TABLE_PREFIX}objects ocg
			${if_table:cg:inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = ocg.object_id}
			${if_table:cgm,cg:inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg:inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id and oc.objecttype_id = 10}
			${if_table:cvsc,oc,cgm,cg:inner join ${TABLE_PREFIX}customvariablestatus cvsc on oc.object_id = cvsc.object_id}
			where
				ocg.objecttype_id = 11
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_TIMEPERIOD =>
			'select
				distinct ${FIELDS}
			from
				${TABLE_PREFIX}objects otp
			${if_table:tp:inner join ${TABLE_PREFIX}timeperiods tp on tp.timeperiod_object_id = otp.object_id}
			${if_table:tptr,tp:inner join ${TABLE_PREFIX}timeperiod_timeranges tptr on tptr.timeperiod_id = tp.timeperiod_id}
			where
				otp.objecttype_id = 9
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_CUSTOMVARIABLE =>
			'select
				distinct ${FIELDS}
			from 
				${TABLE_PREFIX}customvariables cv
			${if_table:cvs:inner join ${TABLE_PREFIX}customvariablestatus cvs on cvs.object_id = cv.object_id}
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_CONFIG =>
			'select
				${FIELDS}
			from
				${TABLE_PREFIX}configfilevariables cfv
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_PROGRAM =>
			'select
				${FIELDS}
			from
				${TABLE_PREFIX}processevents pe
			where
				pe.event_type = 100
				${FILTER}
			order by
				pe.event_time desc
			limit 1',
		self::TARGET_LOG => 
			'select
				${FIELDS}
			from
				${TABLE_PREFIX}logentries le
			where 1 
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_HOST_STATUS_SUMMARY => 
			'select
				${FIELDS:hs.current_state HOST_STATE, count(hs.current_state) COUNT}
			from
				${TABLE_PREFIX}hoststatus hs
			${if_table:oh:inner join ${TABLE_PREFIX}objects oh on oh.object_id = hs.host_object_id}
			${if_table:h,oh:inner join ${TABLE_PREFIX}hosts h on h.host_object_id = oh.object_id}
			${if_table:i,h,oh:inner join ${TABLE_PREFIX}instances i on i.instance_id = h.instance_id}
			${if_table:hcg,h,oh:-- inner join ${TABLE_PREFIX}host_contactgroups hcg on hcg.host_id = h.host_id}
			${if_table:cg,hcg,h,oh:-- inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = hcg.contactgroup_object_id}
			${if_table:ocg,hcg,h,oh:-- inner join ${TABLE_PREFIX}objects ocg on ocg.object_id = hcg.contactgroup_object_id}
			${if_table:cgm,cg,hcg,h,oh:-- inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg,hcg,h,oh:-- inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id}
			${if_table:hgm,oh:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.host_object_id = oh.object_id}
			${if_table:hg,hgm,oh:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_id = hgm.hostgroup_id}
			${if_table:ohg,hg,hgm,oh:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id}
			${if_table:cvsh,oh:inner join ${TABLE_PREFIX}customvariablestatus cvsh on oh.object_id = cvsh.object_id}
			${if_table:cvsc,oc,cgm,cg,hcg,h,oh:-- inner join ${TABLE_PREFIX}customvariablestatus cvsc on oc.object_id = cvsc.object_id}
			where 1
			${FILTER}
			group by
				hs.current_state
			${ORDERBY:hs.current_state}
			${LIMIT}',
		self::TARGET_SERVICE_STATUS_SUMMARY =>
			'select
				${FIELDS:ss.current_state SERVICE_STATE, count(ss.current_state) COUNT}
			from
				${TABLE_PREFIX}servicestatus ss
			${if_table:os:inner join ${TABLE_PREFIX}objects os on os.object_id = ss.service_object_id}
			${if_table:s,os:inner join ${TABLE_PREFIX}services s on s.service_object_id = os.object_id}
			${if_table:i,s,os:inner join ${TABLE_PREFIX}instances i on i.instance_id = s.instance_id}
			${if_table:scg,s,os:-- inner join ${TABLE_PREFIX}service_contactgroups scg on scg.service_id = s.service_id}
			${if_table:cg,scg,s,os:-- inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = scg.contactgroup_object_id}
			${if_table:cgm,cg,scg,s,os:-- inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg,scg,s,os:-- inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id}
			${if_table:ocg,scg,s,os:-- inner join ${TABLE_PREFIX}objects ocg on ocg.object_id = scg.contactgroup_object_id}
			${if_table:hs,s,os,:-- inner join ${TABLE_PREFIX}hoststatus hs on hs.host_object_id = s.host_object_id}
			${if_table:oh,s,os:inner join ${TABLE_PREFIX}objects oh on oh.object_id = s.host_object_id}
			${if_table:hgm,oh,s,os:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.host_object_id = oh.object_id}
			${if_table:hg,hgm,oh,s,os:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_id = hgm.hostgroup_id}
			${if_table:ohg,hg,hgm,oh,s,os:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id}
			${if_table:cvsh,oh,s,os:inner join ${TABLE_PREFIX}customvariablestatus cvsh on oh.object_id = cvsh.object_id}
			${if_table:cvss,os:inner join ${TABLE_PREFIX}customvariablestatus cvss on os.object_id = cvss.object_id}
			${if_table:cvsc,oc,cgm,cg,scg,s,os:-- inner join ${TABLE_PREFIX}customvariablestatus cvsc on oc.object_id = cvsc.object_id}
			where 1
			${FILTER}
			group by
				ss.current_state
			${ORDERBY:ss.current_state}
			${LIMIT}',
		self::TARGET_HOST_STATUS_HISTORY => 
			'select
				${FIELDS}
			from 
				${TABLE_PREFIX}statehistory sh
			${if_table:oh:inner join ${TABLE_PREFIX}objects oh on oh.object_id = sh.object_id and oh.objecttype_id = 1}
			${if_table:h,oh:inner join ${TABLE_PREFIX}hosts h on h.host_object_id = oh.object_id}
			${if_table:i,h:inner join ${TABLE_PREFIX}instances i on i.instance_id = h.instance_id}
			${if_table:hcg,h:inner join ${TABLE_PREFIX}host_contactgroups hcg on hcg.host_id = h.host_id}
			${if_table:cg,h:inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = hcg.contactgroup_object_id}
			${if_table:ocg,hcg,h:inner join ${TABLE_PREFIX}objects ocg on ocg.object_id = hcg.contactgroup_object_id and ocg.objecttype_id = 11}
			${if_table:cgm,cg,hcg,h:inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg,hcg,h:inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id}
			${if_table:hgm,oh:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.host_object_id = oh.object_id}
			${if_table:hg,hgm,oh:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_id = hgm.hostgroup_id}
			${if_table:ohg,hg,hgm,oh:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id}
			${if_table:cvsh,oh:inner join ${TABLE_PREFIX}customvariablestatus cvsh on oh.object_id = cvsh.object_id}
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_SERVICE_STATUS_HISTORY => 
			'select
				${FIELDS}
			from 
				${TABLE_PREFIX}statehistory sh
			${if_table:os:inner join ${TABLE_PREFIX}objects os on os.object_id = sh.object_id and os.objecttype_id = 2}
			${if_table:s,os:inner join ${TABLE_PREFIX}services s on s.service_object_id = os.object_id}
			${if_table:i,s:inner join ${TABLE_PREFIX}instances i on i.instance_id = s.instance_id}
			${if_table:oh,s,os:inner join ${TABLE_PREFIX}objects oh on oh.object_id = s.host_object_id}
			${if_table:h,oh,s,os:inner join ${TABLE_PREFIX}hosts h on h.host_object_id = oh.object_id}
			${if_table:scg,s:inner join ${TABLE_PREFIX}service_contactgroups scg on scg.service_id = s.service_id}
			${if_table:cg,scg,s:inner join ${TABLE_PREFIX}contactgroups cg on cg.contactgroup_object_id = scg.contactgroup_object_id}
			${if_table:cgm,cg,scg,s:inner join ${TABLE_PREFIX}contactgroup_members cgm on cgm.contactgroup_id = cg.contactgroup_id}
			${if_table:oc,cgm,cg,scg,s:inner join ${TABLE_PREFIX}objects oc on oc.object_id = cgm.contact_object_id}
			${if_table:hgm,oh,s,os:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.host_object_id = oh.object_id}
			${if_table:hg,hgm,oh,s,os:inner join ${TABLE_PREFIX}hostgroups hg on hg.hostgroup_id = hgm.hostgroup_id}
			${if_table:ohg,hg,hgm,oh,s,os:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id}
			${if_table:cvsh,oh,s,os:inner join ${TABLE_PREFIX}customvariablestatus cvsh on oh.object_id = cvsh.object_id}
			${if_table:cvss,os:inner join ${TABLE_PREFIX}customvariablestatus cvss on os.object_id = cvss.object_id}
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY}
			${LIMIT}',
		self::TARGET_HOST_PARENTS =>
			'select
				${FIELDS:ohp.object_id HOST_PARENT_OBJECT_ID, ohp.name1 HOST_PARENT_NAME, oh.object_id HOST_CHILD_OBJECT_ID, oh.name1 HOST_CHILD_NAME}
			from
				${TABLE_PREFIX}objects ohp
			${if_table:hph:inner join ${TABLE_PREFIX}host_parenthosts hph on hph.parent_host_object_id = ohp.object_id}
			${if_table:h,hph:inner join ${TABLE_PREFIX}hosts h on h.host_id = hph.host_id}
			${if_table:oh,h,hph:inner join ${TABLE_PREFIX}objects oh on oh.object_id = h.host_object_id and oh.objecttype_id = 1}
			where
				ohp.objecttype_id = 1
			${FILTER}
			${GROUPBY}
			${ORDERBY:ohp.name1 asc, oh.name1 asc}
			${LIMIT}',
		self::TARGET_NOTIFICATIONS => 
			'select
				${FIELDS}
			from
				${TABLE_PREFIX}notifications n
			${if_table:on:inner join ${TABLE_PREFIX}objects `on` on on.object_id = n.object_id and on.is_active = 1}
			${if_table:s,on:left join ${TABLE_PREFIX}services s on s.service_object_id = on.object_id}
			${if_table:h,s,on:left join ${TABLE_PREFIX}hosts h on h.host_object_id = on.object_id or h.host_object_id = s.host_object_id}
			${if_table:oh,h,s,on:left join ${TABLE_PREFIX}objects oh on oh.object_id = h.host_object_id}
			${if_table:os,s,on:left join ${TABLE_PREFIX}objects os on os.object_id = s.service_object_id}
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY:n.start_time asc}
			${LIMIT}',
		self::TARGET_HOSTGROUP_SUMMARY => 
			'select
				${FIELDS}
			from
				${TABLE_PREFIX}hostgroups hg
			${if_table:ohg:inner join ${TABLE_PREFIX}objects ohg on ohg.object_id = hg.hostgroup_object_id and ohg.is_active = 1}
			${if_table:hgm:inner join ${TABLE_PREFIX}hostgroup_members hgm on hgm.hostgroup_id = hg.hostgroup_id}
			${if_table:oh,hgm:inner join ${TABLE_PREFIX}objects oh on oh.object_id = hgm.host_object_id}
			${if_table:hs,oh,hgm:inner join ${TABLE_PREFIX}hoststatus hs on hs.host_object_id = oh.object_id}
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY:hs.current_state asc}
			${LIMIT}',
		self::TARGET_SERVICEGROUP_SUMMARY =>
			'select
				${FIELDS}
			from
			${TABLE_PREFIX}servicegroups sg
			${if_table:osg:inner join ${TABLE_PREFIX}objects osg on osg.object_id = sg.servicegroup_object_id and osg.is_active = 1}
			${if_table:sgm:inner join ${TABLE_PREFIX}servicegroup_members sgm on sgm.servicegroup_id = sg.servicegroup_id}
			${if_table:os,sgm:inner join ${TABLE_PREFIX}objects os on os.object_id = sgm.service_object_id}
			${if_table:ss,os,sgm:inner join ${TABLE_PREFIX}servicestatus ss on ss.service_object_id = os.object_id}
			where 1
			${FILTER}
			${GROUPBY}
			${ORDERBY:ss.current_state asc}
			${LIMIT}'
	);

	// COLUMNS
	public $columns = array(
		// Program information
		'PROGRAM_INSTANCE_ID' => array('pe', 'instance_id'),
		'PROGRAM_DATE' => array('pe', 'program_date'),
		'PROGRAM_VERSION' => array('pe', 'program_version'),

		// Instance things
		'INSTANCE_ID' => array('i', 'instance_id'),
		'INSTANCE_NAME' => array('i', 'instance_name'),
		'INSTANCE_DESCRIPTION' => array('i', 'instance_description'),
		
		// Hostgroup data
		'HOSTGROUP_ID' => array('hg', 'hostgroup_id'),
		'HOSTGROUP_OBJECT_ID' => array('ohg', 'object_id'),
		'HOSTGROUP_INSTANCE_ID' => array('hg', 'instance_id'), 
		'HOSTGROUP_NAME' => array('ohg', 'name1'),
		'HOSTGROUP_ALIAS' => array('hg', 'alias'),

		// Servicegroup data
		'SERVICEGROUP_ID' => array('sg', 'servicegroup_id'),
		'SERVICEGROUP_OBJECT_ID' => array('osg', 'object_id'),
		'SERVICEGROUP_INSTANCE_ID' => array('sg', 'instance_id'),
		'SERVICEGROUP_NAME' => array('osg', 'name1'),
		'SERVICEGROUP_ALIAS' => array('sg', 'alias'),

		// Contactgroup data
		'CONTACTGROUP_ID' => array('cg', 'contactgroup_id'),
		'CONTACTGROUP_OBJECT_ID' => array('ocg', 'object_id'),
		'CONTACTGROUP_INSTANCE_ID' => array('cg', 'instance_id'),
		'CONTACTGROUP_NAME' => array('ocg', 'name1'),
		'CONTACTGROUP_ALIAS' => array('cg', 'alias'),

		// Contact data
		'CONTACT_NAME' => array('oc', 'name1'),
		'CONTACT_CUSTOMVARIABLE_NAME' => array('cvsc', 'varname'),
		'CONTACT_CUSTOMVARIABLE_VALUE' => array('cvsc', 'varvalue'),

		// Timeperiod data
		'TIMEPERIOD_ID' => array('tp', 'timeperiod_id'),
		'TIMEPERIOD_OBJECT_ID' => array('otp', 'object_id'),
		'TIMEPERIOD_INSTANCE_ID' => array('tp', 'instance_id'),
		'TIMEPERIOD_NAME' => array('otp', 'name1'),
		'TIMEPERIOD_ALIAS' => array('tp', 'alias'),
		'TIMEPERIOD_DAY' => array('tptr', 'day'),
		'TIMEPERIOD_STARTTIME' => array('tptr', 'start_sec'),
		'TIMEPERIOD_ENDTIME' => array('tptr', 'end_sec'),

		// Customvariable data
		'CUSTOMVARIABLE_ID' => array('cv', 'customvariable_id'),
		'CUSTOMVARIABLE_OBJECT_ID' => array('cv', 'object_id'),
		'CUSTOMVARIABLE_INSTANCE_ID' => array('cv', 'instance_id'),
		'CUSTOMVARIABLE_NAME' => array('cv', 'varname'),
		'CUSTOMVARIABLE_VALUE' => array('cv', 'varvalue'),
		'CUSTOMVARIABLE_MODIFIED' => array('cvs', 'has_been_modified'),
		'CUSTOMVARIABLE_UPDATETIME' => array('cvs', 'status_update_time'),

		// Host data
		'HOST_ID' => array('h', 'host_id'),
		'HOST_OBJECT_ID' => array('oh', 'object_id'),
		'HOST_INSTANCE_ID' => array('h', 'instance_id'),
		'HOST_NAME' => array('oh', 'name1'),
		'HOST_ALIAS' => array('h', 'alias'),
		'HOST_DISPLAY_NAME' => array('h', 'display_name'),
		'HOST_ADDRESS' => array('h', 'address'),
		'HOST_ACTIVE_CHECKS_ENABLED' => array('h', 'active_checks_enabled'),
		'HOST_CONFIG_TYPE' => array('h', 'config_type'),
		'HOST_FLAP_DETECTION_ENABLED' => array('hs', 'flap_detection_enabled'),
		'HOST_PROCESS_PERFORMANCE_DATA' => array('hs', 'process_performance_data'),
		'HOST_FRESHNESS_CHECKS_ENABLED' => array('hs', 'freshness_checks_enabled'),
		'HOST_FRESHNESS_THRESHOLD' => array('hs', 'freshness_threshold'),
		'HOST_PASSIVE_CHECKS_ENABLED' => array('hs', 'passive_checks_enabled'),
		'HOST_EVENT_HANDLER_ENABLED' => array('hs', 'event_handler_enabled'),
		'HOST_ACTIVE_CHECKS_ENABLED' => array('hs', 'active_checks_enabled'),
		'HOST_RETAIN_STATUS_INFORMATION' => array('h', 'retain_status_information'),
		'HOST_RETAIN_NONSTATUS_INFORMATION' => array('h', 'retain_nonstatus_information'),
		'HOST_NOTIFICATIONS_ENABLED' => array('hs', 'notifications_enabled'),
		'HOST_OBSESS_OVER_HOST' => array('h', 'obsess_over_host'),
		'HOST_FAILURE_PREDICTION_ENABLED' => array('hs', 'failure_prediction_enabled'),
		'HOST_NOTES' => array('h', 'notes'),
		'HOST_NOTES_URL' => array('h', 'notes_url'),
		'HOST_ACTION_URL' => array('h', 'action_url'),
		'HOST_ICON_IMAGE' => array('h', 'icon_image'),
		'HOST_ICON_IMAGE_ALT' => array('h', 'icon_image_alt'),
		'HOST_IS_ACTIVE' => array('oh', 'is_active'),
		'HOST_OUTPUT' => array('hs', 'output'),
		'HOST_LONG_OUTPUT' => array('hs', 'long_output'),
		'HOST_PERFDATA' => array('hs', 'perfdata'),
		'HOST_CURRENT_STATE' => array('hs', 'current_state'),
		'HOST_CURRENT_CHECK_ATTEMPT' => array('hs', 'current_check_attempt'),
		'HOST_MAX_CHECK_ATTEMPTS' => array('hs', 'max_check_attempts'),
		'HOST_LAST_CHECK' => array('hs', 'last_check'),
		'HOST_LAST_STATE_CHANGE' => array('hs', 'last_state_change'),
		'HOST_CHECK_TYPE' => array('hs', 'check_type'),
		'HOST_LATENCY' => array('hs', 'latency'),
		'HOST_EXECUTION_TIME' => array('hs', 'execution_time'),
		'HOST_NEXT_CHECK' => array('hs', 'next_check'),
		'HOST_HAS_BEEN_CHECKED' => array('hs', 'has_been_checked'),
		'HOST_LAST_HARD_STATE_CHANGE' => array('hs', 'last_hard_state_change'),
		'HOST_LAST_NOTIFICATION' => array('hs', 'last_notification'),
		'HOST_STATE_TYPE' => array('hs', 'state_type'),
		'HOST_IS_FLAPPING' => array('hs', 'is_flapping'),
		'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('hs', 'problem_has_been_acknowledged'),
		'HOST_SCHEDULED_DOWNTIME_DEPTH' => array('hs', 'scheduled_downtime_depth'),
		'HOST_STATUS_UPDATE_TIME' => array('hs', 'status_update_time'),
		'HOST_EXECUTION_TIME_MIN' => array('min(hs', 'execution_time)'),
		'HOST_EXECUTION_TIME_AVG' => array('avg(hs', 'execution_time)'),
		'HOST_EXECUTION_TIME_MAX' => array('max(hs', 'execution_time)'),
		'HOST_LATENCY_MIN' => array('min(hs', 'latency)'),
		'HOST_LATENCY_AVG' => array('avg(hs', 'latency)'),
		'HOST_LATENCY_MAX' => array('max(hs', 'latency)'),
		'HOST_ALL' => array('h', '*'),
		'HOST_STATUS_ALL' => array('hs', '*'),
		'HOST_STATE' => array('hs', 'current_state'),
		'HOST_STATE_COUNT' => array('count(hs', 'current_state)'),
		'HOST_PARENT_OBJECT_ID' => array('ohp', 'object_id'),
		'HOST_PARENT_NAME' => array('ohp', 'name1'),
		'HOST_CHILD_OBJECT_ID' => array('oh', 'object_id'),
		'HOST_CHILD_NAME' => array('oh', 'name1'),
		'HOST_CUSTOMVARIABLE_NAME' => array('cvsh', 'varname'),
		'HOST_CUSTOMVARIABLE_VALUE' => array('cvsh', 'varvalue'),
	
		// Service data
		'SERVICE_ID' => array('s', 'service_id'),
		'SERVICE_INSTANCE_ID' => array('s', 'instance_id'),
		'SERVICE_CONFIG_TYPE' => array('s', 'config_type'),
		'SERVICE_IS_ACTIVE' => array('os', 'is_active'),
		'SERVICE_OBJECT_ID' => array('os', 'object_id'),
		'SERVICE_NAME' => array('os', 'name2'),
		'SERVICE_DISPLAY_NAME' => array('s', 'display_name'),
		'SERVICE_NOTIFICATIONS_ENABLED' => array('ss', 'notifications_enabled'),
		'SERVICE_FLAP_DETECTION_ENABLED' => array('ss', 'flap_detection_enabled'),
		'SERVICE_PASSIVE_CHECKS_ENABLED' => array('ss', 'passive_checks_enabled'),
		'SERVICE_EVENT_HANDLER_ENABLED' => array('ss', 'event_handler_enabled'),
		'SERVICE_ACTIVE_CHECKS_ENABLED' => array('ss', 'active_checks_enabled'),
		'SERVICE_RETAIN_STATUS_INFORMATION' => array('s', 'retain_status_information'),
		'SERVICE_RETAIN_NONSTATUS_INFORMATION' => array('s', 'retain_nonstatus_information'),
		'SERVICE_OBSESS_OVER_SERVICE' => array('ss', 'obsess_over_service'),
		'SERVICE_FAILURE_PREDICTION_ENABLED' => array('ss', 'failure_prediction_enabled'),
		'SERVICE_NOTES' => array('s', 'notes'),
		'SERVICE_NOTES_URL' => array('s', 'notes_url'),
		'SERVICE_ACTION_URL' => array('s', 'action_url'),
		'SERVICE_ICON_IMAGE' => array('s', 'icon_image'),
		'SERVICE_ICON_IMAGE_ALT' => array('s', 'icon_image_alt'),
		'SERVICE_OUTPUT' => array('ss', 'output'),
		'SERVICE_LONG_OUTPUT' => array('ss', 'long_output'),
		'SERVICE_PERFDATA' => array('ss', 'perfdata'),
		'SERVICE_CURRENT_STATE' => array('ss', 'current_state'),
		'SERVICE_CURRENT_CHECK_ATTEMPT' => array('ss', 'current_check_attempt'),
		'SERVICE_MAX_CHECK_ATTEMPTS' => array('ss', 'max_check_attempts'),
		'SERVICE_LAST_CHECK' => array('ss', 'last_check'),
		'SERVICE_LAST_STATE_CHANGE' => array('ss', 'last_state_change'),
		'SERVICE_CHECK_TYPE' => array('ss', 'check_type'),
		'SERVICE_LATENCY' => array('ss', 'latency'),
		'SERVICE_EXECUTION_TIME' => array('ss', 'execution_time'),
		'SERVICE_NEXT_CHECK' => array('ss', 'next_check'),
		'SERVICE_HAS_BEEN_CHECKED' => array('ss', 'has_been_checked'),
		'SERVICE_LAST_HARD_STATE_CHANGE' => array('ss', 'last_hard_state_change'),
		'SERVICE_LAST_NOTIFICATION' => array('ss', 'last_notification'),
		'SERVICE_STATE_TYPE' => array('ss', 'state_type'),
		'SERVICE_IS_FLAPPING' => array('ss', 'is_flapping'),
		'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED' => array('ss', 'problem_has_been_acknowledged'),
		'SERVICE_SCHEDULED_DOWNTIME_DEPTH' => array('ss', 'scheduled_downtime_depth'),
		'SERVICE_STATUS_UPDATE_TIME' => array('ss', 'status_update_time'),
		'SERVICE_EXECUTION_TIME_MIN' => array('ss', 'execution_time', 'min(%s)'),
		'SERVICE_EXECUTION_TIME_AVG' => array('ss', 'execution_time', 'avg(%s)'),
		'SERVICE_EXECUTION_TIME_MAX' => array('ss', 'execution_time', 'max(%s)'),
		'SERVICE_LATENCY_MIN' => array('ss', 'latency', 'min(%s)'),
		'SERVICE_LATENCY_AVG' => array('ss', 'latency', 'avg(%s)'),
		'SERVICE_LATENCY_MAX' => array('ss', 'latency', 'max(%s)'),
		'SERVICE_ALL' => array('s', '*'),
		'SERVICE_STATUS_ALL' => array('ss', '*'),
		'SERVICE_CUSTOMVARIABLE_NAME' => array('cvss', 'varname'),
		'SERVICE_CUSTOMVARIABLE_VALUE' => array('cvss', 'varvalue'),
	
		// Config vars
		'CONFIG_VAR_ID' => array('cfv', 'configfilevariable_id'),
		'CONFIG_VAR_INSTANCE_ID' => array('cfv', 'instance_id'),
		'CONFIG_VAR_NAME' => array('cfv', 'varname'),
		'CONFIG_VAR_VALUE' => array('cfv', 'varvalue'),
	
		// Logentries
		'LOG_ID' => array('le', 'logentry_id'),
		'LOG_INSTANCE_ID' => array('le', 'instance_id'),
		'LOG_TIME' => array('le', 'logentry_time'),
		'LOG_ENTRY_TIME' => array('le', 'entry_time'),
		'LOG_ENTRY_TIME_USEC' => array('le', 'entry_time_usec'),
		'LOG_TYPE' => array('le', 'logentry_type'),
		'LOG_DATA' => array('le', 'logentry_data'),
		'LOG_REALTIME_DATA' => array('le', 'realtime_data'),
		'LOG_INFERRED_DATA' => array('le', 'inferred_data_extracted'),
	
		// Statehistory
		'STATEHISTORY_ID' => array('sh', 'statehistory_id'),
		'STATEHISTORY_INSTANCE_ID' => array('sh', 'instance_id'),
		'STATEHISTORY_STATE_TIME' => array('sh', 'state_time'),
		'STATEHISTORY_STATE_TIME_USEC' => array('sh', 'state_time_used'),
		'STATEHISTORY_OBJECT_ID' => array('sh', 'object_id'),
		'STATEHISTORY_STATE_CHANGE' => array('sh', 'state_change'),
		'STATEHISTORY_STATE' => array('sh', 'state'),
		'STATEHISTORY_STATE_TYPE' => array('sh', 'state_type'),
		'STATEHISTORY_CURRENT_CHECK_ATTEMPT' => array('sh', 'current_check_attempt'),
		'STATEHISTORY_MAX_CHECK_ATTEMPTS' => array('sh', 'max_check_attempts'),
		'STATEHISTORY_LAST_STATE' => array('sh', 'last_state'),
		'STATEHISTORY_LAST_HARD_STATE' => array('sh', 'last_hard_state'),
		'STATEHISTORY_OUTPUT' => array('sh', 'output'),
		'STATEHISTORY_LONG_OUTPUT' => array('sh', 'long_output'),

		// Notifications
		'NOTIFICATION_ID' => array('n', 'notification_id'),
		'NOTIFICATION_INSTANCE_ID' => array('n', 'instance_id'),
		'NOTIFICATION_TYPE' => array('n', 'notification_type'),
		'NOTIFICATION_REASON' => array('n', 'notification_reason'),
		'NOTIFICATION_STARTTIME' => array('n', 'start_time'),
		'NOTIFICATION_STARTTIME_USEC' => array('n', 'start_time_usec'),
		'NOTIFICATION_ENDTIME' => array('n', 'end_time'),
		'NOTIFICATION_ENDTIME_USEC' => array('n', 'end_time_usec'),
		'NOTIFICATION_STATE' => array('n', 'state'),
		'NOTIFICATION_OUTPUT' => array('n', 'output'),
		'NOTIFICATION_LONG_OUTPUT' => array('n', 'long_output'),
		'NOTIFICATION_ESCALATED' => array('n', 'escalated'),
		'NOTIFICATION_NOTIFIED' => array('n', 'contacts_notified'),
		'NOTIFICATION_OBJECT_ID' => array('on', 'object_id'),
		'NOTIFICATION_OBJECTTYPE_ID' => array('on', 'objecttype_id'),

		// Summary queries
		'HOSTGROUP_SUMMARY_COUNT' => array('oh', 'object_id', 'count(%s)'),
		'SERVICEGROUP_SUMMARY_COUNT' => array('ss', 'current_state', 'count(%s)'),
	);

	/*
	 * METHODS
	 */

	/**
	 * (non-PHPdoc)
	 * @see objects/search/ido_interfaces/IcingaApiSearchIdoInterface#createQueryLimit($searchLimit)
	 */
	public function createQueryLimit ($searchLimit = false) {
		$returnValue = array($this->statements['limit']);

		if ($searchLimit !== false) {
			array_push($returnValue, implode(',', $searchLimit));
		} else {
			array_push($returnValue, false);
		}

		return $returnValue;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/ido_interfaces/IcingaApiSearchIdoInterface#createQueryGroup($searchGroup)
	 */
	public function createQueryGroup ($searchGroup = false, $resultColumns = false) {
		$returnValue = array($this->statements['group']);

		if (!empty($searchGroup)) {
			array_push($returnValue, implode(',', $searchGroup));
		} else {
			array_push($returnValue, false);
		}

		return $returnValue;
	}

	/**
	 * (non-PHPdoc)
	 * @see objects/search/ido_interfaces/IcingaApiSearchIdoInterface#postProcessQuery($query, $resultColumnKeys, $searchOrder, $searchLimit)
	 */
	public function postProcessQuery ($query, $resultColumnKeys, $searchOrder, $searchLimit) {
		return $query;
	}

}

?>