<?php

/**
 * 
 * @author Christian Doebler <christian.doebler@netways.de>
 *
 */
class IcingaApiResultFile
	extends IcingaApiResult {

	/*
	 * VARIABLES
	 */

		

	/*
	 * METHODS
	 */

	/**
	 * class constructor
	 *
	 * @param	void
	 * @return	void
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {}

	public function setSearchObject (&$object) {
	}

 	public function next () {
 		switch ($this->resultType) {
 			case self::RESULT_OBJECT:
				break;
 			case self::RESULT_ARRAY:
 				break;
 		}
 	}

 	public function rewind () {
 		switch ($this->resultType) {
 			case self::RESULT_OBJECT:
 				break;
 			case self::RESULT_ARRAY:
 				break;
 		}
 	}

}

?>