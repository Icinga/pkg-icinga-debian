/*
 * idolitemod.c
 *
 *	Created on:	2009-07-21
 *	Author:		Christian Doebler <christian.doebler@netways.de>
 */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <time.h>

#include "../icinga/include/broker.h"
#include "../icinga/include/nebcallbacks.h"
#include "../icinga/include/nebstructs.h"

#include "../inc/idolite.h"
#include "../inc/idolitemod.h"


NEB_API_VERSION(CURRENT_NEB_API_VERSION);


static nebmodule *idolitemod_handle;

static char database_table[DATABASE_TABLE_MAX_LENGTH];
static char database_columns[DATABASE_COLUMNS_MAX_LENGTH];
static char *database_columns_ptr = NULL;
static char database_values[DATABASE_VALUES_MAX_LENGTH];
static char *database_values_ptr = NULL;

static char msg[MSG_MAX_LENGTH];

/*
 * module initialization
 */
int nebmodule_init (int flags, char *arg, nebmodule *mod) {

	memset(msg, 0, MSG_MAX_LENGTH);
	idolitemod_handle = mod;

	snprintf(msg, MSG_MAX_LENGTH,"idolitemod: Copyright (c) 2009 Christian Doebler <christian.doebler@netways.de>");
	write_to_all_logs(msg, NSLOG_INFO_MESSAGE);

	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH,"Copyright (c) 2009 Christian Doebler <christian.doebler@netways.de>");
		idolite_log(msg);
		snprintf(msg, MSG_MAX_LENGTH,"debugging enabled");
		idolite_log(msg);
	}

	neb_register_callback(NEBCALLBACK_PROCESS_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_TIMED_EVENT_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_LOG_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_SYSTEM_COMMAND_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_EVENT_HANDLER_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_NOTIFICATION_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_SERVICE_CHECK_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_HOST_CHECK_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_COMMENT_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_DOWNTIME_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_FLAPPING_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_PROGRAM_STATUS_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_HOST_STATUS_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_SERVICE_STATUS_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_ADAPTIVE_PROGRAM_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_ADAPTIVE_HOST_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_ADAPTIVE_SERVICE_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_EXTERNAL_COMMAND_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_AGGREGATED_STATUS_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_RETENTION_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_CONTACT_NOTIFICATION_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_CONTACT_NOTIFICATION_METHOD_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_ACKNOWLEDGEMENT_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_STATE_CHANGE_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_CONTACT_STATUS_DATA, mod, 0, store_data);
	neb_register_callback(NEBCALLBACK_ADAPTIVE_CONTACT_DATA, mod, 0, store_data);

	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH,"callbacks registered");
		idolite_log(msg);
	}

	return 0;

}

/*
 * module deinitialization
 */
int nebmodule_deinit (int flags, int reason) {

	return neb_deregister_module_callbacks(idolitemod_handle);

}

/*
 * main function for data storage
 *
 * unassigned types:	NEBTYPE_NONE, NEBTYPE_HELLO, NEBTYPE_GOODBYE, NEBTYPE_INFO
 *
 * unimplemented types:
 *	nebstruct_process_struct		NEBCALLBACK_PROCESS_DATA
 *									NEBCALLBACK_AGGREGATED_STATUS_DATA
 *	nebstruct_retention_struct		NEBCALLBACK_RETENTION_DATA
 *
 *	nebstruct_timed_event_struct	NEBCALLBACK_TIMED_EVENT_DATA
 */
static int store_data (int event_type, void *data) {

	char time_str[TIME_STR_LENGTH];

	// skip timed events
	if (event_type == 8) {
		return 0;
	}

	// initialize
	memset(database_columns, 0, DATABASE_COLUMNS_MAX_LENGTH);
	database_columns_ptr = NULL;
	memset(database_values, 0, DATABASE_VALUES_MAX_LENGTH);
	database_values_ptr = NULL;

	/*
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "retrieved data (not identified yet) - event_type: %i", event_type);
		idolite_log(msg);
	}
	*/

	switch (event_type) {

		case NEBCALLBACK_PROCESS_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved process_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			process_data = (struct nebstruct_process_struct *)data;
			switch (process_data->type) {
				case NEBTYPE_PROCESS_START:
				case NEBTYPE_PROCESS_DAEMONIZE:
				case NEBTYPE_PROCESS_RESTART:
				case NEBTYPE_PROCESS_SHUTDOWN:
				case NEBTYPE_PROCESS_PRELAUNCH:
				case NEBTYPE_PROCESS_EVENTLOOPSTART:
				case NEBTYPE_PROCESS_EVENTLOOPEND:

					break;
			}
			break;

		case NEBCALLBACK_TIMED_EVENT_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved timed_event_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			timed_event_data = (struct nebstruct_process_struct *)data;
			switch (timed_event_data->type) {
				case NEBTYPE_TIMEDEVENT_ADD:
				case NEBTYPE_TIMEDEVENT_REMOVE:
				case NEBTYPE_TIMEDEVENT_EXECUTE:
				case NEBTYPE_TIMEDEVENT_SLEEP:

					break;
				case NEBTYPE_TIMEDEVENT_DELAY:
				case NEBTYPE_TIMEDEVENT_SKIP:
					// NOT IMPLEMENTED
					break;
			}
			break;

		case NEBCALLBACK_LOG_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved log_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			log_data = (struct nebstruct_log_struct *)data;
			switch (log_data->type) {
				case NEBTYPE_LOG_DATA:
				case NEBTYPE_LOG_ROTATION:
					if (IDOLITEMOD_DEBUG) {
						snprintf(msg, MSG_MAX_LENGTH,"log_data - current data type: %i", log_data->type);
						idolite_log(msg);
					}
					convert_time(time_str, log_data->entry_time);
					append_data_char("timestamp", time_str);
					append_data_char("message", log_data->data);
					set_base("log_data");
					break;
			}
			break;

		case NEBCALLBACK_SYSTEM_COMMAND_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved system_command_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			system_command_data = (struct nebstruct_system_command_struct *)data;
			switch (system_command_data->type) {
				case NEBTYPE_SYSTEM_COMMAND_START:
				case NEBTYPE_SYSTEM_COMMAND_END:
					convert_time(time_str, system_command_data->start_time.tv_sec);
					append_data_char("start_time", time_str);
					convert_time(time_str, system_command_data->end_time.tv_sec);
					append_data_char("end_time", time_str);
					append_data_int("timeout", system_command_data->timeout);
					append_data_char("command_line", system_command_data->command_line);
					append_data_int("early_timeout", system_command_data->early_timeout);
					append_data_double("execution_time", system_command_data->execution_time);
					append_data_int("return_code", system_command_data->return_code);
					append_data_char("output", system_command_data->output);
					set_base("system_command_data");
					break;
			}
			break;

		case NEBCALLBACK_EVENT_HANDLER_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved event_handler_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			event_handler_data = (struct nebstruct_event_handler_struct *)data;
			switch (event_handler_data->type) {
				case NEBTYPE_EVENTHANDLER_START:
				case NEBTYPE_EVENTHANDLER_END:
					append_data_int("eventhandler_type", event_handler_data->eventhandler_type);
					append_data_char("host_name", event_handler_data->host_name);
					append_data_char("service_description", event_handler_data->service_description);
					append_data_int("state_type", event_handler_data->state_type);
					append_data_int("state", event_handler_data->state);
					append_data_int("timeout", event_handler_data->timeout);
					append_data_char("command_name", event_handler_data->command_name);
					append_data_char("command_args", event_handler_data->command_args);
					append_data_char("command_line", event_handler_data->command_line);
					convert_time(time_str, event_handler_data->start_time.tv_sec);
					append_data_char("start_time", time_str);
					convert_time(time_str, event_handler_data->end_time.tv_sec);
					append_data_char("end_time", time_str);
					append_data_int("early_timeout", event_handler_data->early_timeout);
					append_data_double("execution_time", event_handler_data->execution_time);
					append_data_int("return_code", event_handler_data->return_code);
					append_data_char("output", event_handler_data->output);
					set_base("event_handler_data");
					break;
			}
			break;

		case NEBCALLBACK_NOTIFICATION_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved notification_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			notification_data = (struct nebstruct_notification_struct *)data;
			switch (notification_data->type) {
				case NEBTYPE_NOTIFICATION_START:
				case NEBTYPE_NOTIFICATION_END:
					append_data_int("notification_type", notification_data->notification_type);
					convert_time(time_str, notification_data->start_time.tv_sec);
					append_data_char("start_time", time_str);
					convert_time(time_str, notification_data->end_time.tv_sec);
					append_data_char("end_time", time_str);
					append_data_char("host_name", notification_data->host_name);
					append_data_char("service_description", notification_data->service_description);
					append_data_int("reason_type", notification_data->reason_type);
					append_data_int("state", notification_data->state);
					append_data_char("output", notification_data->output);
					append_data_char("ack_author", notification_data->ack_author);
					append_data_char("ack_data", notification_data->ack_data);
					append_data_int("escalated", notification_data->escalated);
					append_data_int("contacts_notified", notification_data->contacts_notified);
					set_base("notification_data");
					break;
			}
			break;

		case NEBCALLBACK_SERVICE_CHECK_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved service_check_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			service_check_data = (struct nebstruct_service_check_struct *)data;
			switch (service_check_data->type) {
				case NEBTYPE_SERVICECHECK_INITIATE:
				case NEBTYPE_SERVICECHECK_PROCESSED:
				case NEBTYPE_SERVICECHECK_RAW_START:
				case NEBTYPE_SERVICECHECK_RAW_END:
                    append_data_char("host_name", service_check_data->host_name);
                    append_data_char("service_description", service_check_data->service_description);
                    append_data_int("check_type", service_check_data->check_type);
                    append_data_int("current_attempt", service_check_data->current_attempt);
                    append_data_int("max_attempts", service_check_data->max_attempts);
                    append_data_int("state_type", service_check_data->state_type);
                    append_data_int("state", service_check_data->state);
                    append_data_int("timeout", service_check_data->timeout);
                    append_data_char("command_name", service_check_data->command_name);
                    append_data_char("command_args", service_check_data->command_args);
                    append_data_char("command_line", service_check_data->command_line);
					convert_time(time_str, service_check_data->start_time.tv_sec);
                    append_data_char("start_time", time_str);
					convert_time(time_str, service_check_data->end_time.tv_sec);
                    append_data_char("end_time", time_str);
                    append_data_int("early_timeout", service_check_data->early_timeout);
                    append_data_double("execution_time", service_check_data->execution_time);
                    append_data_double("latency", service_check_data->latency);
                    append_data_int("return_code", service_check_data->return_code);
                    append_data_char("output", service_check_data->output);
                    append_data_char("long_output", service_check_data->long_output);
                    append_data_char("perf_data", service_check_data->perf_data);
                    set_base("service_check_data");
                    break;
			}
			break;

		case NEBCALLBACK_HOST_CHECK_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved host_check_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			host_check_data = (struct nebstruct_host_check_struct *)data;
			switch (host_check_data->type) {
				case NEBTYPE_HOSTCHECK_INITIATE:
				case NEBTYPE_HOSTCHECK_PROCESSED:
				case NEBTYPE_HOSTCHECK_RAW_START:
				case NEBTYPE_HOSTCHECK_RAW_END:
					if (IDOLITEMOD_DEBUG) {
						snprintf(msg, MSG_MAX_LENGTH,"host_check_data - current data type: %i", host_check_data->type);
						idolite_log(msg);
					}
					append_data_char("host_name", host_check_data->host_name);
					append_data_int("check_type", host_check_data->check_type);
					append_data_int("current_attempt", host_check_data->current_attempt);
					append_data_int("max_attempts", host_check_data->max_attempts);
					append_data_int("state_type", host_check_data->state_type);
					append_data_int("state", host_check_data->state);
					append_data_int("timeout", host_check_data->timeout);
					append_data_char("command_name", host_check_data->command_name);
					append_data_char("command_args", host_check_data->command_args);
					append_data_char("command_line", host_check_data->command_line);
					convert_time(time_str, host_check_data->start_time.tv_sec);
					append_data_char("start_time", time_str);
					convert_time(time_str, host_check_data->end_time.tv_sec);
					append_data_char("end_time", time_str);
					append_data_int("early_timeout", host_check_data->early_timeout);
					append_data_double("execution_time", host_check_data->execution_time);
					append_data_double("latency", host_check_data->latency);
					append_data_int("return_code", host_check_data->return_code);
					append_data_char("output", host_check_data->output);
					append_data_char("long_output", host_check_data->long_output);
					append_data_char("perf_data", host_check_data->perf_data);
					set_base("host_check_data");
					break;
			}
			break;

		case NEBCALLBACK_COMMENT_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved comment_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			comment_data = (struct nebstruct_comment_struct *)data;
			switch (comment_data->type) {
				case NEBTYPE_COMMENT_ADD:
				case NEBTYPE_COMMENT_DELETE:
				case NEBTYPE_COMMENT_LOAD:
					append_data_int("comment_type", comment_data->comment_type);
					append_data_char("host_name", comment_data->host_name);
					append_data_char("service_description", comment_data->service_description);
					convert_time(time_str, comment_data->entry_time);
					append_data_char("entry_time", time_str);
					append_data_char("author_name", comment_data->author_name);
					append_data_char("comment_data", comment_data->comment_data);
					append_data_int("persistent", comment_data->persistent);
					append_data_int("source", comment_data->source);
					append_data_int("entry_type", comment_data->entry_type);
					append_data_int("expires", comment_data->expires);
					convert_time(time_str, comment_data->expire_time);
					append_data_char("expire_time", time_str);
					append_data_unsigned_long("comment_id", comment_data->comment_id);
					set_base("comment_data");
					break;
			}
			break;

		case NEBCALLBACK_DOWNTIME_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved downtime_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			downtime_data = (struct nebstruct_downtime_struct *)data;
			switch (downtime_data->type) {
				case NEBTYPE_DOWNTIME_ADD:
				case NEBTYPE_DOWNTIME_DELETE:
				case NEBTYPE_DOWNTIME_LOAD:
				case NEBTYPE_DOWNTIME_START:
				case NEBTYPE_DOWNTIME_STOP:
					append_data_int("downtime_type", downtime_data->downtime_type);
					append_data_char("host_name", downtime_data->host_name);
					append_data_char("service_description", downtime_data->service_description);
					convert_time(time_str, downtime_data->entry_time);
					append_data_char("entry_time", time_str);
					append_data_char("author_name", downtime_data->author_name);
					append_data_char("comment_data", downtime_data->comment_data);
					convert_time(time_str, downtime_data->start_time);
					append_data_char("start_time", time_str);
					convert_time(time_str, downtime_data->end_time);
					append_data_char("end_time", time_str);
					append_data_int("fixed", downtime_data->fixed);
					append_data_unsigned_long("duration", downtime_data->duration);
					append_data_unsigned_long("triggered_by", downtime_data->triggered_by);
					append_data_unsigned_long("downtime_id", downtime_data->downtime_id);
					set_base("downtime_data");
					break;
			}
			break;

		case NEBCALLBACK_FLAPPING_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved flapping_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			flapping_data = (struct nebstruct_flapping_struct *)data;
			switch (flapping_data->type) {
				case NEBTYPE_FLAPPING_START:
				case NEBTYPE_FLAPPING_STOP:
					append_data_int("flapping_type", flapping_data->flapping_type);
					append_data_char("host_name", flapping_data->host_name);
					append_data_char("service_description", flapping_data->service_description);
					append_data_double("percent_change", flapping_data->percent_change);
					append_data_double("high_threshold", flapping_data->high_threshold);
					append_data_double("low_threshold", flapping_data->low_threshold);
					append_data_unsigned_long("comment_id", flapping_data->comment_id);
					set_base("flapping_data");
					break;
			}
			break;

		case NEBCALLBACK_PROGRAM_STATUS_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved program_status_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			program_status_data = (struct nebstruct_program_status_struct *)data;
			switch (program_status_data->type) {
				case NEBTYPE_PROGRAMSTATUS_UPDATE:
					convert_time(time_str, program_status_data->program_start);
					append_data_char("program_start", time_str);
					append_data_int("pid", program_status_data->pid);
					append_data_int("daemon_mode", program_status_data->daemon_mode);
					convert_time(time_str, program_status_data->last_command_check);
					append_data_char("last_command_check", time_str);
					convert_time(time_str, program_status_data->last_log_rotation);
					append_data_char("last_log_rotation", time_str);
					append_data_int("notifications_enabled", program_status_data->notifications_enabled);
					append_data_int("active_service_checks_enabled", program_status_data->active_service_checks_enabled);
					append_data_int("passive_service_checks_enabled", program_status_data->passive_service_checks_enabled);
					append_data_int("active_host_checks_enabled", program_status_data->active_host_checks_enabled);
					append_data_int("passive_host_checks_enabled", program_status_data->passive_host_checks_enabled);
					append_data_int("event_handlers_enabled", program_status_data->event_handlers_enabled);
					append_data_int("flap_detection_enabled", program_status_data->flap_detection_enabled);
					append_data_int("failure_prediction_enabled", program_status_data->failure_prediction_enabled);
					append_data_int("process_performance_data", program_status_data->process_performance_data);
					append_data_int("obsess_over_hosts", program_status_data->obsess_over_hosts);
					append_data_int("obsess_over_services", program_status_data->obsess_over_services);
					append_data_unsigned_long("modified_host_attributes", program_status_data->modified_host_attributes);
					append_data_unsigned_long("modified_service_attributes", program_status_data->modified_service_attributes);
					append_data_char("global_host_event_handler", program_status_data->global_host_event_handler);
					append_data_char("global_service_event_handler", program_status_data->global_service_event_handler);
					set_base("program_status_data");
					break;
			}
			break;

		case NEBCALLBACK_HOST_STATUS_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved host_status_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			host_status_data = (struct nebstruct_host_status_struct *)data;
			host_data = (struct host_struct *)host_status_data->object_ptr;
			switch (host_status_data->type) {
				case NEBTYPE_HOSTSTATUS_UPDATE:
					append_data_char("host_name", host_data->name);
					append_data_char("display_name", host_data->display_name);
					append_data_char("alias", host_data->alias);
					append_data_char("address", host_data->address);
//					append_data_hostsmember("parent_hosts", host_data->parent_hosts);
//					append_data_hostsmember("child_hosts", host_data->child_hosts);
//					append_data_servicesmember("services", host_data->services);
					append_data_char("host_check_command", host_data->host_check_command);
					append_data_int("initial_state", host_data->initial_state);
					append_data_double("check_interval", host_data->check_interval);
					append_data_double("retry_interval", host_data->retry_interval);
					append_data_int("max_attempts", host_data->max_attempts);
//					append_data_char("event_handler", host_data->event_handler);
//					append_data_contactgroupsmember("contact_groups", host_data->contact_groups);
//					append_data_contactsmember("contacts", host_data->contacts);
					append_data_double("notification_interval", host_data->notification_interval);
					append_data_double("first_notification_delay", host_data->first_notification_delay);
					append_data_int("notify_on_down", host_data->notify_on_down);
					append_data_int("notify_on_unreachable", host_data->notify_on_unreachable);
					append_data_int("notify_on_recovery", host_data->notify_on_recovery);
					append_data_int("notify_on_flapping", host_data->notify_on_flapping);
					append_data_int("notify_on_downtime", host_data->notify_on_downtime);
					append_data_char("notification_period", host_data->notification_period);
					append_data_char("check_period", host_data->check_period);
					append_data_int("flap_detection_enabled", host_data->flap_detection_enabled);
					append_data_double("low_flap_threshold", host_data->low_flap_threshold);
					append_data_double("high_flap_threshold", host_data->high_flap_threshold);
					append_data_int("flap_detection_on_up", host_data->flap_detection_on_up);
					append_data_int("flap_detection_on_down", host_data->flap_detection_on_down);
					append_data_int("flap_detection_on_unreachable", host_data->flap_detection_on_unreachable);
					append_data_int("stalk_on_up", host_data->stalk_on_up);
					append_data_int("stalk_on_down", host_data->stalk_on_down);
					append_data_int("stalk_on_unreachable", host_data->stalk_on_unreachable);
					append_data_int("check_freshness", host_data->check_freshness);
					append_data_int("freshness_threshold", host_data->freshness_threshold);
					append_data_int("process_performance_data", host_data->process_performance_data);
					append_data_int("checks_enabled", host_data->checks_enabled);
					append_data_int("accept_passive_host_checks", host_data->accept_passive_host_checks);
					append_data_int("event_handler_enabled", host_data->event_handler_enabled);
					append_data_int("retain_status_information", host_data->retain_status_information);
					append_data_int("retain_nonstatus_information", host_data->retain_nonstatus_information);
					append_data_int("failure_prediction_enabled", host_data->failure_prediction_enabled);
					append_data_char("failure_prediction_options", host_data->failure_prediction_options);
					append_data_int("obsess_over_host", host_data->obsess_over_host);
					append_data_char("notes", host_data->notes);
					append_data_char("notes_url", host_data->notes_url);
					append_data_char("action_url", host_data->action_url);
					append_data_char("icon_image", host_data->icon_image);
					append_data_char("icon_image_alt", host_data->icon_image_alt);
					append_data_char("vrml_image", host_data->vrml_image);
					append_data_char("statusmap_image", host_data->statusmap_image);
					append_data_int("have_2d_coords", host_data->have_2d_coords);
					append_data_int("x_2d", host_data->x_2d);
					append_data_int("y_2d", host_data->y_2d);
					append_data_int("have_3d_coords", host_data->have_3d_coords);
					append_data_double("x_3d", host_data->x_3d);
					append_data_double("y_3d", host_data->y_3d);
					append_data_double("z_3d", host_data->z_3d);
					append_data_int("should_be_drawn", host_data->should_be_drawn);
//					append_data_customvariablesmember("custom_variables", host_data->custom_variables);
					set_base("hosts");
					break;
			}
			break;

		case NEBCALLBACK_SERVICE_STATUS_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved service_status_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			service_status_data = (struct nebstruct_service_status_struct *)data;
			service_data = (struct service_struct *)service_status_data->object_ptr;
			switch (service_status_data->type) {
				case NEBTYPE_SERVICESTATUS_UPDATE:
					append_data_char("host_name", service_data->host_name);
					append_data_char("service_description", service_data->description);
					append_data_char("display_name", service_data->display_name);
					append_data_char("service_check_command", service_data->service_check_command);
//					append_data_char("event_handler", service_data->event_handler);
					append_data_int("initial_state", service_data->initial_state);
					append_data_double("check_interval", service_data->check_interval);
					append_data_double("retry_interval", service_data->retry_interval);
					append_data_int("max_attempts", service_data->max_attempts);
					append_data_int("parallelize", service_data->parallelize);
//					append_data_contactgroupsmember("contact_groups", service_data->contact_groups);
//					append_data_contactsmember("contacts", service_data->contacts);
					append_data_double("notification_interval", service_data->notification_interval);
					append_data_double("first_notification_delay", service_data->first_notification_delay);
					append_data_int("notify_on_unknown", service_data->notify_on_unknown);
					append_data_int("notify_on_warning", service_data->notify_on_warning);
					append_data_int("notify_on_critical", service_data->notify_on_critical);
					append_data_int("notify_on_recovery", service_data->notify_on_recovery);
					append_data_int("notify_on_flapping", service_data->notify_on_flapping);
					append_data_int("notify_on_downtime", service_data->notify_on_downtime);
					append_data_int("stalk_on_ok", service_data->stalk_on_ok);
					append_data_int("stalk_on_warning", service_data->stalk_on_warning);
					append_data_int("stalk_on_unknown", service_data->stalk_on_unknown);
					append_data_int("stalk_on_critical", service_data->stalk_on_critical);
					append_data_int("is_volatile", service_data->is_volatile);
					append_data_char("notification_period", service_data->notification_period);
					append_data_char("check_period", service_data->check_period);
					append_data_int("flap_detection_enabled", service_data->flap_detection_enabled);
					append_data_double("low_flap_threshold", service_data->low_flap_threshold);
					append_data_double("high_flap_threshold", service_data->high_flap_threshold);
					append_data_int("flap_detection_on_ok", service_data->flap_detection_on_ok);
					append_data_int("flap_detection_on_warning", service_data->flap_detection_on_warning);
					append_data_int("flap_detection_on_unknown", service_data->flap_detection_on_unknown);
					append_data_int("flap_detection_on_critical", service_data->flap_detection_on_critical);
					append_data_int("process_performance_data", service_data->process_performance_data);
					append_data_int("check_freshness", service_data->check_freshness);
					append_data_int("freshness_threshold", service_data->freshness_threshold);
					append_data_int("accept_passive_service_checks", service_data->accept_passive_service_checks);
					append_data_int("event_handler_enabled", service_data->event_handler_enabled);
					append_data_int("checks_enabled", service_data->checks_enabled);
					append_data_int("retain_status_information", service_data->retain_status_information);
					append_data_int("retain_nonstatus_information", service_data->retain_nonstatus_information);
					append_data_int("notifications_enabled", service_data->notifications_enabled);
					append_data_int("obsess_over_service", service_data->obsess_over_service);
					append_data_int("failure_prediction_enabled", service_data->failure_prediction_enabled);
					append_data_char("failure_prediction_options", service_data->failure_prediction_options);
					append_data_char("notes", service_data->notes);
					append_data_char("notes_url", service_data->notes_url);
					append_data_char("action_url", service_data->action_url);
					append_data_char("icon_image", service_data->icon_image);
					append_data_char("icon_image_alt", service_data->icon_image_alt);
//					append_data_customvariablesmember("custom_variables", service_data->custom_variables);
					set_base("services");
					break;
			}
			break;

		case NEBCALLBACK_ADAPTIVE_PROGRAM_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved adaptive_program_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			adaptive_program_data = (struct nebstruct_adaptive_program_data_struct *)data;
			switch (adaptive_program_data->type) {
				case NEBTYPE_ADAPTIVEPROGRAM_UPDATE:
					append_data_int("command_type", adaptive_program_data->command_type);
					append_data_unsigned_long("modified_host_attribute", adaptive_program_data->modified_host_attribute);
					append_data_unsigned_long("modified_host_attributes", adaptive_program_data->modified_host_attributes);
					append_data_unsigned_long("modified_service_attribute", adaptive_program_data->modified_service_attribute);
					append_data_unsigned_long("modified_service_attributes", adaptive_program_data->modified_service_attributes);
					set_base("adaptive_program_data");
					break;
			}
			break;

		case NEBCALLBACK_ADAPTIVE_HOST_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved adaptive_host_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			adaptive_host_data = (struct nebstruct_adaptive_host_data_struct *)data;
			switch (adaptive_host_data->type) {
				case NEBTYPE_ADAPTIVEHOST_UPDATE:
					append_data_int("command_type", adaptive_host_data->command_type);
					append_data_unsigned_long("modified_attribute", adaptive_host_data->modified_attribute);
					append_data_unsigned_long("modified_attributes", adaptive_host_data->modified_attributes);
					set_base("adaptive_host_data");
					break;
			}
			break;

		case NEBCALLBACK_ADAPTIVE_SERVICE_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved adaptive_service_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			adaptive_service_data = (struct nebstruct_adaptive_service_data_struct *)data;
			switch (adaptive_service_data->type) {
				case NEBTYPE_ADAPTIVESERVICE_UPDATE:
					append_data_int("command_type", adaptive_service_data->command_type);
					append_data_unsigned_long("modified_attribute", adaptive_service_data->modified_attribute);
					append_data_unsigned_long("modified_attributes", adaptive_service_data->modified_attributes);
					set_base("adaptive_service_data");
					break;
			}
			break;

		case NEBCALLBACK_EXTERNAL_COMMAND_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved external_command_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			external_command_data = (struct nebstruct_external_command_struct *)data;
			switch (external_command_data->type) {
				case NEBTYPE_EXTERNALCOMMAND_START:
				case NEBTYPE_EXTERNALCOMMAND_END:
					append_data_int("command_type", external_command_data->command_type);
					convert_time(time_str, external_command_data->entry_time);
					append_data_char("entry_time", time_str);
					append_data_char("command_string", external_command_data->command_string);
					append_data_char("command_args", external_command_data->command_args);
					set_base("external_command_data");
					break;
			}
			break;

		case NEBCALLBACK_AGGREGATED_STATUS_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved aggregated_status_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			aggregated_status_data = (struct nebstruct_aggregated_status_struct *)data;
			switch (aggregated_status_data->type) {
				case NEBTYPE_AGGREGATEDSTATUS_STARTDUMP:
				case NEBTYPE_AGGREGATEDSTATUS_ENDDUMP:

					break;
			}
			break;

		case NEBCALLBACK_RETENTION_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved retention_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			retention_data = (struct nebstruct_aggregated_status_struct *)data;
			switch (retention_data->type) {
				case NEBTYPE_RETENTIONDATA_STARTLOAD:
				case NEBTYPE_RETENTIONDATA_ENDLOAD:
				case NEBTYPE_RETENTIONDATA_STARTSAVE:
				case NEBTYPE_RETENTIONDATA_ENDSAVE:

					break;
			}
			break;

		case NEBCALLBACK_CONTACT_NOTIFICATION_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved contact_notification_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			contact_notification_data = (struct nebstruct_contact_notification_struct *)data;
			switch (contact_notification_data->type) {
				case NEBTYPE_CONTACTNOTIFICATION_START:
				case NEBTYPE_CONTACTNOTIFICATION_END:
					append_data_int("notification_type", contact_notification_data->notification_type);
					convert_time(time_str, contact_notification_data->start_time.tv_sec);
					append_data_char("start_time", time_str);
					convert_time(time_str, contact_notification_data->end_time.tv_sec);
					append_data_char("end_time", time_str);
					append_data_char("host_name", contact_notification_data->host_name);
					append_data_char("service_description", contact_notification_data->service_description);
					append_data_char("contact_name", contact_notification_data->contact_name);
					append_data_int("reason_type", contact_notification_data->reason_type);
					append_data_int("state", contact_notification_data->state);
					append_data_char("output", contact_notification_data->output);
					append_data_char("ack_author", contact_notification_data->ack_author);
					append_data_char("ack_data", contact_notification_data->ack_data);
					append_data_int("escalated", contact_notification_data->escalated);
					set_base("contact_notification_data");
					break;
			}
			break;

		case NEBCALLBACK_CONTACT_NOTIFICATION_METHOD_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved contact_notification_method_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			contact_notification_method_data = (struct nebstruct_contact_notification_method_struct *)data;
			switch (contact_notification_method_data->type) {
				case NEBTYPE_CONTACTNOTIFICATIONMETHOD_START:
				case NEBTYPE_CONTACTNOTIFICATIONMETHOD_END:
					append_data_int("notification_type", contact_notification_method_data->notification_type);
					convert_time(time_str, contact_notification_method_data->start_time.tv_sec);
					append_data_char("start_time", time_str);
					convert_time(time_str, contact_notification_method_data->end_time.tv_sec);
					append_data_char("end_time", time_str);
					append_data_char("host_name", contact_notification_method_data->host_name);
					append_data_char("service_description", contact_notification_method_data->service_description);
					append_data_char("contact_name", contact_notification_method_data->contact_name);
					append_data_char("command_name", contact_notification_method_data->command_name);
					append_data_char("command_args", contact_notification_method_data->command_args);
					append_data_int("reason_type", contact_notification_method_data->reason_type);
					append_data_int("state", contact_notification_method_data->state);
					append_data_char("output", contact_notification_method_data->output);
					append_data_char("ack_author", contact_notification_method_data->ack_author);
					append_data_char("ack_data", contact_notification_method_data->ack_data);
					append_data_int("escalated", contact_notification_method_data->escalated);
					set_base("contact_notification_method_data");
					break;
			}
			break;

		case NEBCALLBACK_ACKNOWLEDGEMENT_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved acknowledgement_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			acknowledgement_data = (struct nebstruct_acknowledgement_struct *)data;
			switch (acknowledgement_data->type) {
				case NEBTYPE_ACKNOWLEDGEMENT_ADD:
				case NEBTYPE_ACKNOWLEDGEMENT_REMOVE:
				case NEBTYPE_ACKNOWLEDGEMENT_LOAD:
					append_data_int("acknowledgement_type", acknowledgement_data->acknowledgement_type);
					append_data_char("host_name", acknowledgement_data->host_name);
					append_data_char("service_description", acknowledgement_data->service_description);
					append_data_int("state", acknowledgement_data->state);
					append_data_char("author_name", acknowledgement_data->author_name);
					append_data_char("comment_data", acknowledgement_data->comment_data);
					append_data_int("is_sticky", acknowledgement_data->is_sticky);
					append_data_int("persistent_comment", acknowledgement_data->persistent_comment);
					append_data_int("notify_contacts", acknowledgement_data->notify_contacts);
					set_base("acknowledgement_data");
					break;
			}
			break;

		case NEBCALLBACK_STATE_CHANGE_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved state_change_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			state_change_data = (struct nebstruct_statechange_struct *)data;
			switch (state_change_data->type) {
				case NEBTYPE_STATECHANGE_START:
				case NEBTYPE_STATECHANGE_END:
					append_data_int("statechange_type", state_change_data->statechange_type);
					append_data_char("host_name", state_change_data->host_name);
					append_data_char("service_description", state_change_data->service_description);
					append_data_int("state", state_change_data->state);
					append_data_int("state_type", state_change_data->state_type);
					append_data_int("current_attempt", state_change_data->current_attempt);
					append_data_int("max_attempts", state_change_data->max_attempts);
					append_data_char("output", state_change_data->output);
					set_base("state_change_data");
					break;
			}
			break;

		case NEBCALLBACK_CONTACT_STATUS_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved contact_status_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			contact_status_data = (struct nebstruct_contact_status_struct *)data;
			contact_data = (struct contact_struct *)contact_status_data->object_ptr;
			append_data_char("name", contact_data->name);
			append_data_char("alias", contact_data->alias);
			append_data_char("email", contact_data->email);
			append_data_char("pager", contact_data->pager);
			append_data_char("address", *contact_data->address);
//			append_data_commandsmember("host_notification_commands", contact_data->host_notification_commands);
//			append_data_commandsmember("service_notification_commands", contact_data->service_notification_commands);
			append_data_int("notify_on_service_unknown", contact_data->notify_on_service_unknown);
			append_data_int("notify_on_service_warning", contact_data->notify_on_service_warning);
			append_data_int("notify_on_service_critical", contact_data->notify_on_service_critical);
			append_data_int("notify_on_service_recovery", contact_data->notify_on_service_recovery);
			append_data_int("notify_on_service_flapping", contact_data->notify_on_service_flapping);
			append_data_int("notify_on_service_downtime", contact_data->notify_on_service_downtime);
			append_data_int("notify_on_host_down", contact_data->notify_on_host_down);
			append_data_int("notify_on_host_unreachable", contact_data->notify_on_host_unreachable);
			append_data_int("notify_on_host_recovery", contact_data->notify_on_host_recovery);
			append_data_int("notify_on_host_flapping", contact_data->notify_on_host_flapping);
			append_data_int("notify_on_host_downtime", contact_data->notify_on_host_downtime);
			append_data_char("host_notification_period", contact_data->host_notification_period);
			append_data_char("service_notification_period", contact_data->service_notification_period);
			append_data_int("host_notifications_enabled", contact_data->host_notifications_enabled);
			append_data_int("service_notifications_enabled", contact_data->service_notifications_enabled);
			append_data_int("can_submit_commands", contact_data->can_submit_commands);
			append_data_int("retain_status_information", contact_data->retain_status_information);
			append_data_int("retain_nonstatus_information", contact_data->retain_nonstatus_information);
//			append_data_customvariablesmember("custom_variables", contact_data->custom_variables);
			set_base("contacts");
			break;

		case NEBCALLBACK_ADAPTIVE_CONTACT_DATA:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH,"retrieved contact_data (event type: %i)", event_type);
				idolite_log(msg);
			}
			adaptive_contact_data = (struct nebstruct_adaptive_contact_data_struct *)data;
			append_data_int("command_type", adaptive_contact_data->command_type);
			append_data_unsigned_long("modified_attribute", adaptive_contact_data->modified_attribute);
			append_data_unsigned_long("modified_attributes", adaptive_contact_data->modified_attributes);
			append_data_unsigned_long("modified_host_attribute", adaptive_contact_data->modified_host_attribute);
			append_data_unsigned_long("modified_host_attributes", adaptive_contact_data->modified_host_attributes);
			append_data_unsigned_long("modified_service_attribute", adaptive_contact_data->modified_service_attribute);
			append_data_unsigned_long("modified_service_attributes", adaptive_contact_data->modified_service_attributes);
			set_base("adaptive_contact_data");
			break;

		default:
			if (IDOLITEMOD_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH, "event type (%i) unidentified", event_type);
				idolite_log(msg);
			}
			break;

	}

	if (database_columns_ptr != NULL) {
		/*
		if (IDOLITEMOD_DEBUG) {
			snprintf(msg, MSG_MAX_LENGTH,"data identified and assigned - calling storage routine");
			idolite_log(msg);
		}
		*/
		write_data_to_file();
		/*
	} else {
		if (IDOLITEMOD_DEBUG) {
			snprintf(msg, MSG_MAX_LENGTH,"could not identify retrieved data");
			idolite_log(msg);
		}
		*/
	}

	/*
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "end of data storage");
		idolite_log(msg);
	}
	*/

	return 0;

}

/*
 * wrapper for append_data() for strings
 */
static void append_data_char (char *column, char *data) {
	char *data_new = NULL;
	data_new = idolite_escape_char(data);
	append_data(column, data_new);
	free(data_new);
}

/*
 * wrapper for append_data() for integers
 */
static void append_data_int (char *column, int data) {
	char data_new[DATABASE_DATA_MAX_LENGTH];
	snprintf(data_new, DATABASE_DATA_MAX_LENGTH, "'%i'", data);
	append_data(column, data_new);
}

/*
 * wrapper for append_data() for doubles
 */
static void append_data_double (char *column, double data) {
	char data_new[DATABASE_DATA_MAX_LENGTH];
	snprintf(data_new, DATABASE_DATA_MAX_LENGTH, "'%f'", data);
	append_data(column, data_new);
}

/*
 * wrapper for append_data() for unsigned longs
 */
static void append_data_unsigned_long (char *column, unsigned long data) {
	char data_new[DATABASE_DATA_MAX_LENGTH];
	snprintf(data_new, DATABASE_DATA_MAX_LENGTH, "'%lu'", data);
	append_data(column, data_new);
}

/*
 * appends new columns and data for query
 */
static void append_data (char *column, char *data) {

	int data_len = 0, column_len = 0;

	column_len = strlen(column);
	data_len = strlen(data);

	/*
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "set column and data: %s - %s", column, data);
		idolite_log(msg);
	}
	*/

	if (database_columns_ptr != NULL) {
		memcpy(database_columns_ptr, ",", 1);
		memcpy(database_values_ptr, ",", 1);
		database_columns_ptr++;
		database_values_ptr++;
	} else {
		database_columns_ptr = database_columns;
		database_values_ptr = database_values;
	}

	memcpy(database_columns_ptr, column, column_len);
	memcpy(database_values_ptr, data, data_len);

	database_columns_ptr += column_len;
	database_values_ptr += data_len;

	/*
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "column and data set: %s - %s", database_columns, database_values);
		idolite_log(msg);
	}
	*/

}

static void write_data_to_file (void) {

	int buffer_length = 0;
	char file_name[FILENAME_LENGTH];
	char *buffer = NULL;
    struct timeval now;
	FILE *output_file;

	buffer_length = strlen(database_table) + strlen(database_columns) + strlen(database_values) + 4;
	buffer = malloc(buffer_length);
	snprintf(buffer, buffer_length, "%s\n%s\n%s\n", database_table, database_columns, database_values);

    gettimeofday(&now, 0);
	snprintf(file_name, FILENAME_LENGTH, "%s/%s%i.%06i", DATA_DIR, DATA_FILE, (int)now.tv_sec, (int)now.tv_usec);
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "writing data to file: %s", file_name);
		idolite_log(msg);
	}
	output_file = fopen(file_name, "a+");

	fwrite(buffer, strlen(buffer), 1, output_file);

	fclose(output_file);
	free(buffer);

}

/*
 * escapes special characters and quotes
 */
static char *idolite_escape_char (char *unescaped) {

	int pos_old = 0, pos_new = 0, len_old = 0;
	char *escaped = NULL;

	/*
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "escape and quote data: %s", unescaped);
		idolite_log(msg);
	}
	*/

	if (unescaped) {

		len_old = strlen(unescaped);
		escaped = (char *)malloc(len_old * 2 + 3);

		escaped[pos_new++] = '\'';

		for (pos_old = 0; pos_old < len_old; pos_old++) {
			switch (unescaped[pos_old]) {
				case '\'':
					escaped[pos_new++] = '\'';
					escaped[pos_new++] = '\'';
					break;
				case '\t':
					escaped[pos_new++] = '\\';
					escaped[pos_new++] = 't';
					break;
				case '\\':
					escaped[pos_new++] = '\\';
					escaped[pos_new++] = '\\';
					break;
				case '\n':
					escaped[pos_new++] = '\\';
					escaped[pos_new++] = 'n';
					break;
				case '\r':
					escaped[pos_new++] = '\\';
					escaped[pos_new++] = 'r';
					break;
				default:
					escaped[pos_new++] = unescaped[pos_old];
					break;
			}
		}

		escaped[pos_new++] = '\'';
		escaped[pos_new] = 0;

	} else {

		escaped = (char *)malloc(3);
		escaped[0] = '\'';
		escaped[1] = '\'';
		escaped[2] = 0;

	}

	/*
	if (IDOLITEMOD_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "escaped and quoted data: %s", escaped);
		idolite_log(msg);
	}
	*/

	return escaped;

}

/*
 * write to log file
 */
static int idolite_log (char *msg) {

	FILE *fp;
	char timestamp[22];
	memset(timestamp, 0, 22);
	time_t now;
    struct tm *local_now;

	now = time(NULL);
    local_now = localtime(&now);
	strftime(timestamp, 22, "%Y-%m-%d %H:%M:%S", local_now);

	if ((fp = fopen(IDOLITE_LOG_FILE, "a"))) {
		fprintf(fp, "[%s] idolitemod: %s\n", timestamp, msg);
		fclose(fp);
		return 0;
	}

	return 1;

}

/*
 * convert epoch timestamp to iso timestamp
 */
static void convert_time (char *time_str, time_t timeraw) {
    struct tm *time_tmp;
    time_tmp = localtime(&timeraw);
	strftime(time_str, 11, "%s", time_tmp);
}

/*
 * set basic information for storage
 */
static void set_base (char *table) {

	/* set database table */
	memset(database_table, 0, DATABASE_TABLE_MAX_LENGTH);
	snprintf(database_table, DATABASE_TABLE_MAX_LENGTH, "%s", table);

}
