/*
 * sqlite3.h
 *
 *  Created on: 2009-08-17
 *      Author: Christian Doebler <christian.doebler@netways.de>
 */

#ifndef SQLITE3_H_
#define SQLITE3_H_

int idolite_connect (void);
void idolite_close (void);
int store_in_db (char *database_table, char *database_columns, char *database_values);

#endif /* SQLITE3_H_ */
