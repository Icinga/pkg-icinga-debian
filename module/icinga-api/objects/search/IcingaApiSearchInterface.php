<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
interface IcingaApiSearchInterface {

	/**
	 * sets the connection object
	 *
	 * @param	IcingaApiConnectionInterface		$object		connection object
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setConnectionObject(IcingaApiConnectionInterface &$object);

	/**
	 * sets the type of return data
	 *
	 * @param	string		$type				type of data to return (object, array)
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setResultType ($type);

	/**
	 * sets the type of search
	 *
	 * @param	string		$type				search type
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchType ($type);

	/**
	 * sets result columns for query
	 *
	 * @param	mixed		$columns			array of columns or column as string
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	// public function setResultColumns ($columns);

	/**
	 * sets the search target
	 *
	 * @param	string		$target				target to search for
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchTarget ($target);

	/**
	 * sets search filter(s)
	 * @param	mixed		$filter				filter key or associative array of key-value pairs defining filters
	 * @param	mixed		$value				value to filter for
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchFilter ($filter, $value = false, $defaultMatch = IcingaApi::MATCH_EXACT);

	/**
	 * sets custom search filter(s) to append to end of filter statement
	 * @param	mixed		$statement				filter statement
	 * @param	mixed		$searchAggregator		search aggregator to use for appending statement to filter
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchFilterAppendix ($statement, $searchAggregator = self::SEARCH_AND);

	/**
	 * sets columns to group query by
	 * @param	mixed		$columns			array of columns or string of one or more comma-separated columns to group by
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchGroup ($columns);

	/**
	 * sets columns and direction to sort by
	 * @param	mixed		$column				array of columns or string of one or more comma-separated columns including optional directions to sort by
	 * @param	string		$direction			sort direction (asc|desc; optional)
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchOrder ($column, $direction = false);

	/**
	 * sets limits for query
	 * @param	mixed		$start				start row as integer or string containing start and length separated by a comma (NOTE: if $length is missing $start will use as $length instead!)
	 * @param	integer		$length				number of rows to query for (optional)
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setSearchLimit ($start, $length = false);

	/**
	 * set source to find current contact
	 * @param	string		$source				source of contact
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setContactSource ($source);

	/**
	 * sets the current contact for further filtering
	 * @param	string		$contact			contact to use as filter
	 * @return	IcingaApiSearchInterface
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function setContact ($contact);

	/**
	 * check available search data
	 *
	 * @param	void
	 * @return	boolean							true if search data is valid otherwise false
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function searchValid ();

}

?>