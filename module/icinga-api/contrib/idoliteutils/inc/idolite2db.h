/*
 * idolite2db.h
 *
 *  Created on: 2009-08-11
 *      Author: Christian Doebler <christian.doebler@netways.de>
 */

#ifndef IDOLITE2DB_H_
#define IDOLITE2DB_H_

#ifdef __cplusplus
  extern "C" {
#endif

/*
 * CONSTANTS
 */
#define IDOLITE2DB_DEBUG 1

#define IDOLITE2DB_UPDATE_INTERVAL 10
#define IDOLITE2DB_FILENAME_MAX_LENGTH 80

#define IDOLITE2DB_DATA_BUFFER_LENGTH 2000

#define IDOLITE2DB_STATE_READ_TABLE 0
#define IDOLITE2DB_STATE_READ_COLUMNS 1
#define IDOLITE2DB_STATE_READ_VALUES 2

#define IDOLITE2DB_LOG_FILE "/usr/local/icinga/var/idolite2db.log"

#define DATABASE_FILE_NAME_LENGTH 100
#define DATABASE_TIMEOUT 1000
#define	DATABASE_DIR "/usr/local/icinga/var/idolite/"
#define DATABASE_NAME "idolite.sqlite3"
#define DATABASE "/usr/local/icinga/var/idolite/idolite.sqlite3"
#define DATABASE_QUERY_MAX_LENGTH 21000

/*
 * STRUCTURES
 */
struct dirfile {
	char *name;
};

/*
 * FUNCTIONS
 */
int addfile (struct dirfile item);
int dirfilecmp (const struct dirfile *f1, const struct dirfile *f2);
int read_data_from_files (void);
int extract_and_store (char *file_name);
int idolite2db_log (char *msg);

#ifdef __cplusplus
  }
#endif

#endif /* IDOLITE2DB_H_ */
