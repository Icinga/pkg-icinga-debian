/*
 * idolitemod.h
 *
 *	Created on:	2009-07-21
 *	Author:		Christian Doebler <christian.doebler@netways.de>
 */

#ifndef IDOLITESTRUCTS_H_
/*
 * idolitemod.h
 *
 *	Created on:	2009-07-21
 *	Author:		Christian Doebler <christian.doebler@netways.de>
 */

#define IDOLITESTRUCTS_H_

#ifdef __cplusplus
  extern "C" {
#endif

/*
 * CONSTANTS
 */
#define IDOLITEMOD_DEBUG 1
#define TIME_STR_LENGTH 12
#define FILENAME_LENGTH 80

#define IDOLITE_LOG_FILE "/usr/local/icinga/var/idolite.log"

#define DATABASE_SEPARATOR ","
#define DATABASE_DATA_MAX_LENGTH 2000

/*
 * DECLARATION OF NEB STRUCTURES
 */
static struct nebstruct_acknowledgement_struct *acknowledgement_data = NULL;
static struct nebstruct_adaptive_contact_data_struct *adaptive_contact_data = NULL;
static struct nebstruct_adaptive_host_data_struct *adaptive_host_data = NULL;
static struct nebstruct_adaptive_program_data_struct *adaptive_program_data = NULL;
static struct nebstruct_adaptive_service_data_struct *adaptive_service_data = NULL;
static struct nebstruct_aggregated_status_struct *aggregated_status_data = NULL;
static struct nebstruct_comment_struct *comment_data = NULL;
static struct nebstruct_contact_notification_struct *contact_notification_data = NULL;
static struct nebstruct_contact_notification_method_struct *contact_notification_method_data = NULL;
static struct nebstruct_contact_status_struct *contact_status_data = NULL;
static struct nebstruct_downtime_struct *downtime_data = NULL;
static struct nebstruct_event_handler_struct *event_handler_data = NULL;
static struct nebstruct_external_command_struct *external_command_data = NULL;
static struct nebstruct_flapping_struct *flapping_data = NULL;
static struct nebstruct_host_check_struct *host_check_data = NULL;
static struct nebstruct_host_status_struct *host_status_data = NULL;
static struct nebstruct_log_struct *log_data = NULL;
static struct nebstruct_notification_struct *notification_data = NULL;
static struct nebstruct_program_status_struct *program_status_data = NULL;
static struct nebstruct_process_struct *process_data = NULL;
static struct nebstruct_aggregated_status_struct *retention_data = NULL;
static struct nebstruct_service_check_struct *service_check_data = NULL;
static struct nebstruct_service_status_struct *service_status_data = NULL;
static struct nebstruct_statechange_struct *state_change_data = NULL;
static struct nebstruct_system_command_struct *system_command_data = NULL;
static struct nebstruct_process_struct *timed_event_data = NULL;

/*
 * DECLARATION OF OBJECT STRUCTURES
 */
static struct contact_struct *contact_data = NULL;
static struct host_struct *host_data = NULL;
static struct hostgroup_struct *hostgroup_data = NULL;
static struct service_struct *service_data = NULL;
static struct servicegroup_struct *servicegroup_data = NULL;

/*
 * DECLARATION OF FUNCTIONS
 */
static int store_data (int event_type, void *data);
static void append_data_char (char *column, char *data);
static void append_data_int (char *column, int data);
static void append_data_double (char *column, double data);
static void append_data_unsigned_long (char *column, unsigned long data);
static void append_data (char *column, char *data);
static void write_data_to_file (void);
static char *idolite_escape_char (char *unescaped);
static int idolite_log (char *msg);
static void convert_time (char *time_str, time_t timeraw);
static void set_base (char *table);

#ifdef __cplusplus
  }
#endif

#endif /* IDOLITESTRUCTS_H_ */
