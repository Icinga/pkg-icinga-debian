<?php
/**
 * These constants define the database which should be TESTED
 */

define("IDO_TEST_TYPE","pgsql");
define("IDO_TEST_HOST","127.0.0.1");
define("IDO_TEST_PORT","5432");
define("IDO_TEST_DATABASE","testcase");
define("IDO_TEST_USER","testcase");
define("IDO_TEST_PASSWORD","testcase");
define("IDO_TEST_PREFIX","icinga_");
/*

define("IDO_TEST_TYPE","oci8");
define("IDO_TEST_HOST","127.0.0.1");
define("IDO_TEST_PORT","1721");
define("IDO_TEST_DATABASE","XE");
define("IDO_TEST_USER","TESTCASE_USER");
define("IDO_TEST_PASSWORD","TESTCASE_USER");
define("IDO_TEST_PREFIX","");
*/
/**
 * These definitions are the database which already is testet and should be the reference
 */
define("IDO_TEST_REF_TYPE","mysql");
define("IDO_TEST_REF_HOST","127.0.0.1");
define("IDO_TEST_REF_PORT","3306");
define("IDO_TEST_REF_DATABASE","mysql_testdatabase");
define("IDO_TEST_REF_USER","testcase");
define("IDO_TEST_REF_PASSWORD","testcase");
define("IDO_TEST_REF_PREFIX","icinga_");
?>
