<?php
/**
 * Debugger that simply prints to stdout (i.e. the webbrowser in most cases)
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 *
 */
class icingaApiEchoDebugger implements icingaApiDebuggerTargetInterface {
	public $breakStyle = "<br/>";
	
	public function __construct(array $params = array()) {
		if(isset($params["break"]))
			$this->breakStyle = $params["break"];
	}
	
	public function out($msg) {
		echo $msg.$this->breakStyle;
	}
}