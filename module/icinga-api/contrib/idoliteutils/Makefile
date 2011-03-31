#########################################################################################################
# Makefile for IDOLITE - 2009 by Christian Doebler <christian.doebler@netways.de>
#########################################################################################################

# Source code directories
SRC_BASE=./src/
DB_BASE=./db/
DRV_BASE=$(SRC_BASE)/db_drv/
#SRC_INCLUDE=../include

CC=gcc
MOD_CFLAGS=-fPIC
CFLAGS=-g -O2 -DHAVE_CONFIG_H -Wall 
MOD_LDFLAGS=-shared
LDFLAGS=
LIBS=
LIBSDAEMON=
LIBSDB_DRV=-lsqlite3

prefix=/usr/local/icinga
exec_prefix=${prefix}
BINDIR=${exec_prefix}/bin/
DBDIR=${prefix}/var/idolite/
MODULENAME=idolitemod
DAEMONNAME=idolite2db
DRVNAME=sqlite3
INSTALL=/usr/bin/install -c
INSTALL_OPTS=-o icinga -g icinga
COMMAND_OPTS=-o icinga -g icinga
STRIP=/usr/bin/strip

CP=@CP@

none:
	$(CC) $(CFLAGS) $(MOD_CFLAGS) -o $(SRC_BASE)$(MODULENAME).o $(SRC_BASE)$(MODULENAME).c $(MOD_LDFLAGS) $(LDFLAGS) $(LIBS)
	$(CC) $(CFLAGS) $(LDFLAGS) -o $(SRC_BASE)$(DAEMONNAME) $(SRC_BASE)$(DAEMONNAME).c $(DRV_BASE)$(DRVNAME).c $(LIBSDB_DRV) $(LIBSDAEMON)

clean:
	rm -f $(DRV_BASE)$(DRVNAME).o
	rm -f $(SRC_BASE)$(MODULENAME).o
	rm -f $(SRC_BASE)$(DAEMONNAME)

install:
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(BINDIR)
	$(INSTALL) -m 755 $(INSTALL_OPTS) $(SRC_BASE)$(MODULENAME).o $(BINDIR)$(MODULENAME).o
	$(INSTALL) -m 755 $(INSTALL_OPTS) $(SRC_BASE)$(DAEMONNAME) $(BINDIR)$(DAEMONNAME)
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DBDIR)
	$(INSTALL) -m 644 $(INSTALL_OPTS) $(DB_BASE)idolite.sqlite3 $(DBDIR)idolite.sqlite3
