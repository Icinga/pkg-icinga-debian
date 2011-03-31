<?php


class ociPDOStatement extends PDOStatement {
	const NO_FIELDS = 0x00;
	const NAMED_FIELDS = 0x01;
	const NR_FIELDS = 0x02;
	
	protected $attributes = array();
	protected $PDO = null;
	protected $stmt = null;
	protected $stmt_handler = null;
	protected $fields = null;
	protected $rowCount = 0;
	protected $buffer = array();
	
	public $query;
	
	/* Methods */
	/**
	 * Not supported 
	
	public function bindColumn($column, &$param, $type, $maxlen, $driverdata) {
	//	$this->buffer = null;
		return parent::bindColumn($column, $param, $type, $maxlen, $driverdata);
	}

	 * Not supported 

	public function bindParam($parameter, &$variable,  $data_type = PDO::PARAM_STR, $length, $driver_options) {
	//	$this->buffer = null;
		return parent::bindColumn($parameter, $variable, $data_type, $length, $driver_options);
	}

	 * Not supported 
	
	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR) {
	//	$this->buffer = null;
		return parent::bindValue($parameter, $value, $data_type);
	}*/
	/**
	 * Not supported 
	 */
	public function closeCursor() {
		return parent::closeCursor();
	}
	
	public function columnCount() {
		return parent::columnCount();
	}
	public function debugDumpParams() {
		return parent::debugDumpParams();
	}
	public function errorCode() {
		return parent::errorCode();
	}
	public function errorInfo() {
		return parent::errorInfo();
	}
	
	public function execute( $input_parameters = array()) {
		
		$this->stmt = preg_replace("/(\t|\r|\n)+/"," ",$this->stmt);
		$this->stmt = preg_replace("/ {2,}/"," ",$this->stmt);

		ociPDO::$lastQuery = $this->stmt;
		$stmt_handler = oci_parse($this->PDO->getHandler(),$this->stmt);
		$this->stmt_handler = $stmt_handler; 

		foreach($input_parameters as $key=>&$value) {
			$offset = $key;
			if(is_int($key))
				$key = ":oci_val_".$key;

			if(!oci_bind_by_name($stmt_handler,$key,$value))
				$this->PDO->throwOciError($stmt_handler);
		}

		icingaApiDebugger::logDebug("ociPDO - Executing ".$this->stmt);
		$result = oci_execute($stmt_handler,$this->PDO->getAttribute(ociPDO::ATTR_EXEC_MODE));
		if(!$result)
			$this->PDO->throwOciError($stmt_handler);
		
		$buffer = array();
		$this->rowCount = oci_fetch_all($this->stmt_handler,$buffer,null,null,OCI_FETCHSTATEMENT_BY_ROW);
		$this->buffer = $buffer;	
		return $result;
		
	}
	
	public function fetch($fetch_style = PDO::FETCH_BOTH, $cursor_orientation = PDO::FETCH_ORI_NEXT,$cursor_offset = 0 ) {
		if(empty($this->buffer))
			return array();
		$result = $this->buffer[0];
		$this->buffer = array_slice($this->buffer,1);
		return $result;
	}
	
	public function fetchAll($fetch_style = PDO::FETCH_BOTH,$column_index = 0,$ctor_args = array()) {
		if(empty($this->buffer))
			return array();
			
		$b = $this->buffer;
		$this->buffer = array();
		return $b;
	}
	
	public function fetchColumn($column_number = 0) {
		return parent::fetchColumns($column_number);
	}
	
	public function fetchObject($class_name = "stdClass" ,  $ctor_args =array()) {
		$row = $this->fetch();
		if(!$row)
			return false;
		$obj = new stdClass();
		foreach($row as $key=>$val)
			$obj->{$key} = $val;
		
		icingaApiDebugger::logDebug("ociPDO - Fetch object returned: ".print_r($obj,true));

		return $obj;
	}
	
	public function getAttribute($attribute) {
		if(isset($this->attribute[$attribute]))
			return $this->attribute[$attribute];
		return null;
	}
	
	public function getColumnMeta($column) {
		return parent::getColumnMeta($column);
	}
	
	public function nextRowset() {
		return parent::nextRowset();
	}
	
	public function rowCount() {
		return $this->rowCount;
	}
	
	public function setAttribute($attribute,$value) {
		$this->attribute[$attribute] = $value;
		return true;
	}
	/*
	public function setFetchMode($mode) {
		return parent::setFetchmode($mode);
	}*/


	public function __construct(ociPDO $PDO,$stmt) {
		$this->PDO = $PDO;
		$this->stmt = $stmt;
		$this->analyseStatement();
	}
		
	private function analyseStatement() {
	//	if(!$this->checkIfNamed())
		$this->checkIfNumbered();

	}
		
	private function checkIfNamed() {
		$stmt = $this->stmt;
		$result = array();
		if(!preg_match_all('/:(\w+)/',$stmt,$result))
			return false;
		foreach($result[0] as $key=>$result_typo)
			$this->fields[$result[1][$key]] = $result_typo;
		return true;
	}
	
	private function checkIfNumbered() {
		$stmt = $this->stmt;
		$matches = array();
		$nr = preg_match_all("/\?/",$stmt,$matches);
		
		if(!$nr)
			return false;
	
		for($curNr=0;$curNr<$nr;$curNr++) {
			$stmt = preg_replace("/\?/",":oci_val_".$curNr,$stmt,1);
			$this->fields[$curNr] = ":oci_val_".$curNr; 
		}

		$this->stmt = $stmt;
		
	}
}