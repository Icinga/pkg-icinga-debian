<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
interface IcingaApiSearchIdoInterface {

	/**
	 * creates limit information for query
	 * @param	array		$searchLimit			start and length of query limit
	 * @return	array								limit template and values
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function createQueryLimit ($searchLimit = false);

	/**
	 * creates group information for query
	 * @param	array		$searchGroup			group information
	 * @param	array		$resultColumns			result columns (select columns)
	 * @return	array								group template and values
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function createQueryGroup ($searchGroup = false, $resultColumns = false);

	/**
	 * provides post-processing for query generator
	 * @param	string		$query					query to post process
	 * @param	array		$resultColumnKeys		keys of result columns (select columns)
	 * @param	string		$searchOrder			columns of 'order by' statement
	 * @param	array		$searchLimit			limit values of query
	 * @return	string								post processed query
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function postProcessQuery ($query, $resultColumnKeys, $searchOrder, $searchLimit);

}

?>