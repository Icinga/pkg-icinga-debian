<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
interface IcingaApiResultInterface {

	/**
	 * sets the type of return data
	 *
	 * @param	string		$type				type of data to return (object, array)
	 * @return	boolean							true on success otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setResultType ($type);

	/**
	 * returns result data
	 *
	 * @param	string		$name			name of column to return
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __get ($name);

	/**
	 * returns result data
	 *
	 * @param	string		$name			name of column to return
	 * @param	array		$arguments		NOT USED
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __call ($name, $arguments = array());

	/**
	 * returns query result
	 *
	 * @param	string		$searchField		field name to return value of
	 * @return	mixed							search result
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function get ($searchField = false);

	/**
	 * returns a complete row from query result
	 *
	 * @param	void
	 * @return	mixed							search-result row
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getRow ();

	/**
	 * return the number of rows which where returned by a query
	 *
	 * @return	integer							number of rows
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getResultCount ();

}

?>