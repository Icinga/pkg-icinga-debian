<?php
/**
 * IDO Implementation of the api filter
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 */
class IcingaApiSearchFilterIdo extends IcingaApiSearchFilter {
	public function createQueryStatement() {
		$field = $this->getField();
		$value = $this->getValue();
		$match = $this->getMatch();
		if($match == IcingaApi::MATCH_LIKE || $match == IcingaApi::MATCH_NOT_LIKE)
			$value = str_replace("*","%",$value);
		
		$statementSkeleton = $field." ".$match." '".$value."' ";

		return $statementSkeleton;
	}

}

?>