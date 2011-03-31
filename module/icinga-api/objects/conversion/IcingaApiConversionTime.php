<?php

/**
 * conversion helper
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class IcingaApiConversionTime {

	/**
	 * converts time from epoch to iso (etc.) and back
	 * @param	mixed		$time				time as unix timestamp or ist, etc.
	 * @param	string		$format				output format if $time is a unix timestamp
	 * @return	mixed							converted time or false on error
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function convertTime ($time, $format = '%Y-%m-%d %H:%M:%S') {
		$returnValue = false;
		if (is_int($time)) {
			$returnValue = strftime($format, $time);
		} elseif (is_string($time)) {
			$returnValue = strtotime($time);
		} else {
			throw new IcingaApiConversionTimeException('convertTime(): invalid input time!');
		}
		return $returnValue;
	}

}

// extend exceptions
class IcingaApiConversionTimeException extends Exception {};

?>