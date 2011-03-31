<?php
class icingaApiPDO {
	static public function getPDO($databaseDsn, $user = null, $pass = null, $connectionAttributes = array()) {
		if(substr($databaseDsn,0,4) == 'oci8')
			return new ociPDO(substr($databaseDsn,4),$user,$pass,$connectionAttributes);
		else
			return new PDO($databaseDsn,$user,$pass,$connectionAttributes);
	}
}
