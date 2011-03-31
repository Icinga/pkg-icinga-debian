<?php
/**
 * IDO Implementation of filtergroup
 *
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 *
 */
class IcingaApiSearchFilterGroupIdo extends IcingaApiSearchFilterGroup {
	public function createQueryStatement() {
		$statement = "";
		foreach($this as $filter) {
			if($statement)
				$statement .= " ".$this->getType()." "; // Add chain type (AND/OR)
			$statement .= $filter->createQueryStatement();
		}
		icingaApiDebugger::logDebug("Created filtergroup statement ".$statement);
		return "(".$statement.")";
	}
}

?>
