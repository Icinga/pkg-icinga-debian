/*
 * sqlite.c
 *
 *  Created on: 2009-08-17
 *      Author: Christian Doebler <christian.doebler@netways.de>
 */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include <sqlite3.h>

#include "../../inc/idolite.h"
#include "../../inc/idolite2db.h"
#include "../../inc/db_drv/sqlite3.h"

sqlite3* db;

/*
 * connect to database
 */
int idolite_connect (void) {

	int connection = 0;
	//int threadsafe = 0;

	/*
	if (IDOLITE2DB_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "connecting to database %s", DATABASE);
		idolite2db_log(msg);
	}
	*/

    connection = sqlite3_open(DATABASE, &db);

    if (IDOLITE2DB_DEBUG) {
    	if (connection != SQLITE_OK) {
    		/*
    		if (IDOLITE2DB_DEBUG) {
    			snprintf(msg, MSG_MAX_LENGTH, "could not connect to database (%i)!", connection);
    			idolite2db_log(msg);
    		}
    		*/
    		return 1;
    	} else {
    		/*
    		if (IDOLITE2DB_DEBUG) {
    			threadsafe =  sqlite3_threadsafe();
    			if (threadsafe) {
    				snprintf(msg, MSG_MAX_LENGTH, "sqlite is threadsafe.");
    			} else {
    				snprintf(msg, MSG_MAX_LENGTH, "sqlite is NOT threadsafe!");
    			}
    			idolite2db_log(msg);
    		}
    		*/
    	}
    }

	return 0;

}

void idolite_close (void) {
	sqlite3_close(db);
}

/*
 * store data in database
 */
int store_in_db (char *database_table, char *database_columns, char *database_values) {

	char query[DATABASE_QUERY_MAX_LENGTH];

	/*
	if (IDOLITE2DB_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "query: insert into %s (%s) values (%s);", database_table, database_columns, database_values);
		idolite2db_log(msg);
	}
	*/

	snprintf(
		query, DATABASE_QUERY_MAX_LENGTH,
    	"insert into %s (%s) values (%s);",
    	database_table,
    	database_columns,
    	database_values
	);

	sqlite3_exec(db, query, NULL, NULL, NULL);

	/*
	if (IDOLITE2DB_DEBUG) {
		snprintf(msg, MSG_MAX_LENGTH, "data stored");
		idolite2db_log(msg);
	}
	*/

	return 0;

}
