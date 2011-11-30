<?php

define("LOGLEVEL_DEBUG_API", icingaApiDebugger::DEBUG_LEVEL_ERROR | icingaApiDebugger::DEBUG_LEVEL_WARNING | icingaApiDebugger::DEBUG_LEVEL_PHP_EXCEP);
class icingaApiDebugger {
	const DEBUG_LEVEL_ALL = 0xff;
	const DEBUG_LEVEL_ERROR = 0x01;
	const DEBUG_LEVEL_WARNING = 0x02;
	const DEBUG_LEVEL_DEBUG = 0x08;
	const DEBUG_LEVEL_PHP_EXCEP = 0x10;
	
	protected $bufferSize = 5;
	protected $buffered = false;
	protected $buffer = array();
	protected $debugLevel = LOGLEVEL_DEBUG_API; // log Exceptions,Errors and warnings
	protected $debugTarget = null;
	
	public function setDebugLevel($int) {
		$this->debugLevel = $int;
		if($this->getDebugLevel() & self::DEBUG_LEVEL_PHP_EXCEP)
			set_exception_handler('icingaApiDebugger::handleException');
	}
	
	public function getDebugLevel() {
		return $this->debugLevel;
	}
	
	public function enableBuffer() {
		$this->buffered = true;
	}
	public function disableBuffer() {
		$this->buffered = false;
	}
	public function isBuffered() {
		return $this->buffered;
	}
	
	public function setBufferSize($size) {
		$this->bufferSize = $size;
	}
	
	public function getBufferSize() {
		return $this->bufferSize;
	}
	
	protected function setDebugger(icingaApiDebuggerTargetInterface $target) {
		$this->debugTarget = $target;
	}
	
	protected function getDebugTarget() {
		return $this->debugTarget;
	}
	
	protected function addToBuffer($data,$action) {
		$this->buffer[] = array("type"=>$action,"data"=>$data,"time"=>time());
		if($this->buffer > $this->getBufferSize())
			$this->flushBuffer();
	}
	
	static public function handleException(Exception $exception) {
		
		if(strtolower(substr(get_class($exception),0,9) === "icingaapi"))
			self::logException($exception->getMessage());
		restore_exception_handler();
		throw $exception;
	}
	
	public function flushBuffer() {
		foreach($this->buffer as $entry) {
			switch($entry["type"]) {
				case 'error':
					$this->error($entry["data"],array("noBuffer"=>true));
					break;
				case 'warning':
					$this->warning($entry["data"],array("noBuffer"=>true));
					break;
				case 'debug':
					$this->debug($entry["data"],array("noBuffer"=>true));
					break;
			}
		}
	}

	public function error($msg,$flags = array()) {
		$target = $this;
		if(get_class($this) != "icingaApiDebugger")
			$target = icingaApiDebugger::getInstance();
		
		if(!($target->getDebugLevel() & self::DEBUG_LEVEL_ERROR))
			return false;
		
		if(!isset($flags["noBuffer"]) && $target->isBuffered()) {
			$target->addToBuffer($msg,"error");
			return true;	
		} 

		$msg = "[ERROR] ".$msg;
		$target->getDebugTarget()->out($msg);
	}
	
	
	public function warning($msg,$flags = array()) {
		$target = $this;
		if(get_class($this) != "icingaApiDebugger")
			$target = icingaApiDebugger::getInstance();
		
		if(!($target->getDebugLevel() & self::DEBUG_LEVEL_WARNING))
			return false;
		
		if(!isset($flags["noBuffer"]) && $target->isBuffered()) {
			$target->addToBuffer($msg,"warning");
			return true;
		}
		$msg = "[WARNING] ".$msg;
		$target->getDebugTarget()->out($msg);
	}

	public function debug($msg,$flags = array()) {
		$target = $this;
		if(get_class($this) != "icingaApiDebugger")
			$target = icingaApiDebugger::getInstance();
		
		if(!($target->getDebugLevel() & self::DEBUG_LEVEL_DEBUG))
			return false;
		
		if(!isset($flags["noBuffer"]) && $target->isBuffered()) {
			$target->addToBuffer($msg,"debug");
			return true;
		}

		$msg = "[DEBUG] ".$msg;
		
		$target->getDebugTarget()->out($msg);
	}
	
	public function exception($msg,$flags = array()) {
		$target = $this;
		if(get_class($this) != "icingaApiDebugger")
			$target = icingaApiDebugger::getInstance();
			
		if(!($target->getDebugLevel() & self::DEBUG_LEVEL_PHP_EXCEP))
			return false;
		
		$msg = "[EXCEPTION] ".$msg;
		
		$target->getDebugTarget()->out($msg);
	}

	public static function logDebug($msg) {
		$obj = icingaApiDebugger::getInstance();
		$obj->debug($msg);
	}

	public static function logError($msg) {
		$obj = icingaApiDebugger::getInstance();
		$obj->error($msg);
	}

	public static function logWarning($msg) {
		$obj = icingaApiDebugger::getInstance();
		$obj->warning($msg);
	}
	
	public static function logException($msg) {
		$obj = icingaApiDebugger::getInstance();
		$obj->exception($msg);
	}
	
	
	public function debugOut($msg) {}
	
	/**
	 * @return icingaApiDebugger
	 */
	public static function getInstance() {
		if(self::$instance == null)
			self::$instance = new self();

		return self::$instance;
	}
	
	protected static $instance = null;
	protected function __construct() {
		$this->setDebugger(new icingaApiFileDebugger());

	}
	
	public function __destruct() {
		if($this->isBuffered())
			$this->flushBuffer();

	}
}
