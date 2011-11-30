<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
interface IcingaApiConnectionInterface {

	public function setConfig (array $config);
	public function connect ();
	public function query ($query, $queryParams = array());

}

?>