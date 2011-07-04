/*
 * idolite2db.c
 *
 *  Created on: 2009-08-11
 *      Author: Christian Doebler <christian.doebler@netways.de>
 */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <dirent.h>
#include <time.h>
#include <unistd.h>

#include "../inc/idolite.h"
#include "../inc/idolite2db.h"
#include "../inc/db_drv/sqlite3.h"

char msg[MSG_MAX_LENGTH];

char database_table[DATABASE_TABLE_MAX_LENGTH];
char database_columns[DATABASE_COLUMNS_MAX_LENGTH];
char database_values[DATABASE_VALUES_MAX_LENGTH];

int file_prefix_length = 0;

struct dirfile *file_list = NULL;
int file_list_num_elements = 0;
int file_list_num_allocated = 0;

int main (int argc, char **argv) {

	file_prefix_length = strlen(DATA_FILE);

	if (idolite_connect() != 0) {
		snprintf(msg, MSG_MAX_LENGTH, "could not connect to database!");
		idolite2db_log(msg);
	}

	while (1) {
		read_data_from_files();
		if (IDOLITE2DB_DEBUG) {
			snprintf(msg, MSG_MAX_LENGTH, "sleeping for %i seconds", IDOLITE2DB_UPDATE_INTERVAL);
			idolite2db_log(msg);
		}
		sleep(IDOLITE2DB_UPDATE_INTERVAL);
	}

	idolite_close();

	return 0;

}

void init_file_list (void) {
	free(file_list);
	file_list = NULL;
	file_list_num_elements = 0;
	file_list_num_allocated = 0;
}

int addfile (struct dirfile item) {

	if (file_list_num_elements == file_list_num_allocated) {
		if (file_list_num_allocated == 0) {
			file_list_num_allocated = 3;
		} else {
			file_list_num_allocated *= 2;
		}

		void *_tmp = realloc(file_list, (file_list_num_allocated * sizeof(struct dirfile)));

		if (!_tmp) {
			fprintf(stderr, "ERROR: Couldn't realloc memory!\n");
			return(-1);
		}

		file_list = (struct dirfile *)_tmp;

	}

	file_list[file_list_num_elements] = item;
	file_list_num_elements++;

	return file_list_num_elements;

}


int dirfilecmp (const struct dirfile *f1, const struct dirfile *f2) {
	return strcmp(f1->name, f2->name);
}


int read_data_from_files (void) {

	char input_file[80];
	DIR *dh;
	struct dirent *dir;

    int file_counter = 0;
    struct dirfile *dir_file;

    dir_file = malloc(sizeof(dir_file));
    dir_file->name = malloc(IDOLITE2DB_FILENAME_MAX_LENGTH);

	dh = opendir(DATA_DIR);

	if (dh) {
		while ((dir = readdir(dh)) != NULL) {
			if (!memcmp(dir->d_name, DATA_FILE, file_prefix_length)) {
				dir_file->name = dir->d_name;
				addfile(*dir_file);
			}
		}

		qsort(file_list, file_list_num_elements, sizeof(struct dirfile), dirfilecmp);

		for (file_counter = 0; file_counter < file_list_num_elements; file_counter++) {
			if (IDOLITE2DB_DEBUG) {
				snprintf(msg, MSG_MAX_LENGTH, "processing file: %s", file_list[file_counter].name);
				idolite2db_log(msg);
			}
			snprintf(input_file, 80, "%s/%s", DATA_DIR, file_list[file_counter].name);
			extract_and_store(input_file);
			unlink(input_file);
		}

		init_file_list();
	}

    closedir(dh);

	return 0;

}

int extract_and_store (char *file_name) {

	FILE *fh;
	char buffer[IDOLITE2DB_DATA_BUFFER_LENGTH], buffer_tmp[DATABASE_VALUES_MAX_LENGTH];
	int read_length = 0, buffer_pos = 0, buffer_tmp_pos = 0;
	int state = IDOLITE2DB_STATE_READ_TABLE;

	fh = fopen(file_name, "r");

	while (!feof(fh)) {

		memset(buffer, 0, IDOLITE2DB_DATA_BUFFER_LENGTH);
		read_length = fread(buffer, IDOLITE2DB_DATA_BUFFER_LENGTH, 1, fh);
		read_length = strlen(buffer);

		for (buffer_pos = 0; buffer_pos < read_length; buffer_pos++) {

			if (buffer[buffer_pos] != '\n') {

				buffer_tmp[buffer_tmp_pos++] = buffer[buffer_pos];

			} else {

				switch (state) {

					case IDOLITE2DB_STATE_READ_TABLE:
						memcpy(database_table, buffer_tmp, buffer_tmp_pos);
						database_table[buffer_tmp_pos] = 0;
						buffer_tmp_pos = 0;
						state++;
						break;

					case IDOLITE2DB_STATE_READ_COLUMNS:
						memcpy(database_columns, buffer_tmp, buffer_tmp_pos);
						database_columns[buffer_tmp_pos] = 0;
						buffer_tmp_pos = 0;
						state++;
						break;

					case IDOLITE2DB_STATE_READ_VALUES:
						memcpy(database_values, buffer_tmp, buffer_tmp_pos);
						database_values[buffer_tmp_pos] = 0;
						state = IDOLITE2DB_STATE_READ_TABLE;
						store_in_db(database_table, database_columns, database_values);
						break;

				}

				/* reset buffer */
				buffer_tmp_pos = 0;

			}

		}

	}

	fclose(fh);

	return 0;

}

/*
 * write to log file
 */
int idolite2db_log (char *msg) {

	FILE *fp;
	char timestamp[22];
	memset(timestamp, 0, 22);
	time_t now;
    struct tm *local_now;

	now = time(NULL);
    local_now = localtime(&now);
	strftime(timestamp, 22, "%Y-%m-%d %H:%M:%S", local_now);

	if ((fp = fopen(IDOLITE2DB_LOG_FILE, "a"))) {
		fprintf(fp, "[%s] idolite2db: %s\n", timestamp, msg);
		fclose(fp);
		return 0;
	}

	return 1;

}
