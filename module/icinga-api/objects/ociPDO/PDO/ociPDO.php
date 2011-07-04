<?php

class ociPDO extends PDO {
	const ATTR_CHARSET = "oci_character_set"; 
	const ATTR_SESSION_MODE = "oci_session_mode";
	const ATTR_EXEC_MODE = "oci_execute_mode";
	
	public static $lastQuery = "";
	private $ociHandler = null;
	private $isConnected = false;
	private $attributes = array(
		"oci_execute_mode" => OCI_COMMIT_ON_SUCCESS
	);
	
	public static function getLastQuery() {
		return self::$lastQuery;
	}
	
	public function getHandler() {
		return $this->ociHandler;
	}
	
	protected function connectToDatabase($dn,$user,$pass,$connectionAttributes) {
		$handler = null;

		if(!$this->getAttribute(PDO::ATTR_PERSISTENT))
			$handler = oci_connect($user,$pass,$dn,$this->getAttribute(ociPDO::ATTR_CHARSET),$this->getAttribute(ociPDO::ATTR_SESSION_MODE));
		else 
			$handler = oci_pconnect($user,$pass,$dn,$this->getAttribute(ociPDO::ATTR_CHARSET),$this->getAttribute(ociPDO::ATTR_SESSION_MODE));
		
		if(is_null($handler))
			return false;

		$this->isConnected = true;
		$this->ociHandler = $handler; 

		return true;
	}
	
	public function __construct($databaseDsn, $user = null, $pass = null, $connectionAttributes = array()) {
		foreach($connectionAttributes as $attribute=>$value)
			$this->setAttribute($attribute,$value);
			
		if(!$this->connectToDatabase($databaseDsn,$user,$pass,$connectionAttributes)) {
			$error = $this->errorInfo(true);
			$errorMsg = "Unknown error";
			if($error)
				$errorMsg = $error["message"];

			throw new PDOException("Couldn't connect to oci8 db $databaseDsn :".$errorMsg);
		}
		
		return $this;
	}
	
	public function beginTransaction() {
		$this->setAttribute(self::ATTR_EXEC_MODE,OCI_NO_AUTO_COMMIT);
		return true;
	}
	
	public function commit() {
		$this->setAttribute(self::ATTR_EXEC_MODE,OCI_COMMIT_ON_SUCCESS);
		return oci_commit($this->ociHandler);
	} 
	
	public function errorCode($noConnectionHandler = false) {
		$error = $this->errorInfo($noConnectionHandler);
		if(!$error)
			return $error["code"];
		return false;
	}
	
	public function errorInfo($connHandler = false) {
		$errorInfo = oci_error($connHandler);
		return $errorInfo;
	}
	
	public function exec($statement) {
		$statementRessource = oci_parse($this->ociHandler,$statement);
		if(!$statement)
			$this->throwOciError();
		$result = oci_execute($statementRessource,$this->getAttribute(self::ATTR_EXEC_MODE));
		if(!$result)
			$this->throwOciError();
		$this->driver = "oci8";
		return oci_num_rows($statementRessource);
	}
	
	public function query($statement, $fetchtype = null, $classname  = "", $ctorargs = array ()) {
		$stmt = new ociPDOStatement($this,$statement);
		$stmt->execute();
		return $stmt;
	}
	
	public function prepare($statement,$options = array()) {
		$stmt = new ociPDOStatement($this,$statement);
		return $stmt;
	}
	
	public function getAttribute ($attribute) {
		if(isset($this->attributes[$attribute]))
			return $this->attributes[$attribute];
		return null;
	}
	
	static public function getAvailableDrivers() {
		return array('oci8');
	}
	
	public function lastInsertId ($name = null) {
		return 0; 
	}
	
	public function rollBack() {
		$result = oci_rollback($this->ociHandler);
		$this->setAttribute(self::ATTR_EXEC_MODE,OCI_COMMIT_ON_SUCCESS);
		return $result;
	}
	
	public function setAttribute($attribute, $value) {
		$this->attributes[$attribute] = $value;
	}
	
	public function __destruct() {
		if($this->ociHandler)
			oci_close($this->ociHandler);
	}
	
	
	public function throwOciError($hdlr = false) {
		$error = $this->errorInfo($hdlr);
		$errorMsg = "Unknown error";
		if($error) {
			$errorMsg = $error["message"];;
			if($hdlr)
				$errorMsg .= "\n".$error["sqltext"];
		}
		throw new icingaApiOciPDOException("OCI Error $databaseDsn :".$errorMsg);
	}
}

class icingaApiOciPDOException extends PDOException {
	public function __construct($msg) {
		icingaApiDebugger::logException($msg);
		parent::__construct($msg);
	}
}