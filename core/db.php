<?php 

if(file_exists(CORE_DIR.'config.php')){
	require_once(CORE_DIR.'config.php');#get db access data
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if($mysqli->connect_errno){
		die("Not able to connect to db!");
	}else{
		global $db;
		$db = new DB($mysqli);
	}	
}

class DB{
	private $mysqli;
	/** Init Database Object **/
	public function DB(){
		$this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	}
	/** Get arrays from stmt **/
	private function getArrayFromSTMT($stmt){
		$meta = $stmt->result_metadata();
		while ($field = $meta->fetch_field()) {
			$parameters[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $parameters);
		while ($stmt->fetch()) {
			foreach($row as $key => $val) {
				$x[$key] = $val;
			}
			$results[] = $x;
		}
		if(isset($results)&&!empty($results)){
			return $results;
		}else{
			return array();
		}
	}
	/** Generate sql statement **/
	private function generateSQLStatement($arrayOfKeys){
		$statement = " WHERE ".$arrayOfKeys[0]."=?";
		for($i=1;$i<count($arrayOfKeys);$i++){
			$statement.=" AND ".$arrayOfKeys[$i]."=?";
		}
		return $statement;
	}
	/** Generate types string **/
	private function generateTypesString($arrayOfValues){
		$types="";
		foreach($arrayOfValues as $key => $value){
			if(is_string($value)){
				$types.="s";
			}else if(is_int($value)){
				$types.="i";
			}else if(is_double($value)){
				$types.="d";
			}else if(is_bool($value)){
				$arrayOfValues[$key]=(int)$value;
				$types.="i";
			}else{
				throw new Exception("Unimplemented data type: " . gettype($value));
				return false;
			}
		}
		return $types;
	}
	/** Binds parameters with types and values **/
	private function bindParameters($stmt, $types, $values){
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $values));
		return true;
	}
	/** Get table from the database **/
	function getAllInformationFromTable($tableName){
		if ($stmt = $this->mysqli->prepare("SELECT * FROM ".$tableName)) {
			$stmt->execute();
			$data = $this->getArrayFromSTMT($stmt);
			$stmt->close();
			return $data;
		}
		return false;
	}
	/** Get row(s) from the database **/
	function getAllInformationFrom($tableName, $arrayOfKeys, $arrayOfValues){
		if(count($arrayOfKeys)==count($arrayOfValues)){
			$types = $this->generateTypesString($arrayOfValues);
			$values = array();
			for($i = 0;$i<count($arrayOfValues);$i++){
				$values[$i] = &$arrayOfValues[$i];
			}
			$statement = $this->generateSQLStatement($arrayOfKeys);
			if ($stmt = $this->mysqli->prepare("SELECT * FROM ".$tableName.$statement)) {
				$this->bindParameters($stmt, $types, $values);
				$stmt->execute();
				$data=$this->getArrayFromSTMT($stmt);
				$stmt->close();
				return $data;
			}
		}
		return false;
	}
	/** Adds something to the database **/
	function addToDatabase($tableName, $arrayOfKeys, $arrayOfValues){
		if((count($arrayOfKeys)==count($arrayOfValues)) && count($arrayOfKeys)>0){
			$keys = implode(", ", $arrayOfKeys);
			$qMarks = "?";
			for($i = 1;$i<(count($arrayOfKeys));$i++){
				$qMarks.=", ?";
			}
			$types = $this->generateTypesString($arrayOfValues);
			$values = array();
			for($i = 0;$i<count($arrayOfValues);$i++){
				$values[$i] = &$arrayOfValues[$i];
			}
			if($stmt = $this->mysqli->prepare("INSERT INTO ".$tableName." (".$keys.") VALUES (".$qMarks.")")) {
				$this->bindParameters($stmt, $types, $values);
				$stmt->execute();
				$stmt->close();
				return true;
			}
		}
		return false;
	}
	/** Remove something from the database **/
	function removeFromDatabase($tableName, $arrayOfKeys, $arrayOfValues){
		if(count($arrayOfKeys)==count($arrayOfValues)){
			$types = $this->generateTypesString($arrayOfValues);
			$values = array();
			for($i = 0;$i<count($arrayOfValues);$i++){
				$values[$i] = &$arrayOfValues[$i];
			}
			$statement = $this->generateSQLStatement($arrayOfKeys);
			if ($stmt = $this->mysqli->prepare("DELETE FROM ".$tableName.$statement)) {
				$this->bindParameters($stmt, $types, $values);
				$stmt->execute();
				$stmt->close();
				return true;
			}
		}
		return false;
	}
	/** Update something in the database **/
	function updateInDatabase($tableName, $keysToUpdate, $valuesToUpdate, $keysToSearch, $valuesToSearch){
		if((count($keysToUpdate)==count($valuesToUpdate)) && count($valuesToUpdate) >= 1){
			$set = "SET ".$keysToUpdate[0]."=?";
			for($i=1;$i<count($valuesToUpdate);$i++){
				$set.=", ".$keysToUpdate[$i]." =?";
			}
			$types = $this->generateTypesString($valuesToUpdate);
			$types.=$this->generateTypesString($valuesToSearch);
			$statement = $this->generateSQLStatement($keysToSearch);
			$values = array();
			for($i = 0;$i<count($valuesToUpdate);$i++){
				$values[$i] = &$valuesToUpdate[$i];
			}
			$values_ = array();
			for($i = 0;$i<count($valuesToSearch);$i++){
				$values_[$i] = &$valuesToSearch[$i];
			}
			if ($stmt = $this->mysqli->prepare("UPDATE ".$tableName ." ". $set . " ". $statement)) {
				$this->bindParameters($stmt, $types, array_merge($values, $values_));
				$stmt->execute();
				$stmt->close();
				return true;
			}
		}
		return false;
	}
	/** Add a user **/
	function addUser($username, $password, $mail){
		return $this->addToDatabase('users', array('username', 'password', 'mail'), array($username, $password, $mail));
	}
}

function setupTables(){
	#create needed tables
	
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	#settings
	$table =
	'CREATE TABLE IF NOT EXISTS settings(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    settingsKey VARCHAR(200) NOT NULL,
    value VARCHAR(800) NOT NULL
	)';
	$mysqli->query($table);
	#users
	$table =
	'CREATE TABLE IF NOT EXISTS users(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(200) NOT NULL,
    password VARCHAR(300) NOT NULL,
	mail VARCHAR(200) NOT NULL
	)';
	$mysqli->query($table);
	
	#tokens
	$table='
	CREATE TABLE IF NOT EXISTS tokens (
	tokenID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tokenContent VARCHAR(30) NOT NULL,
	tokenUser INT NOT NULL,
	tokenIP VARCHAR(30) NOT NULL,
	tokenExpireTime timestamp NOT NULL
	)';
	$mysqli->query($table);
	#mailTokens
	$table='
	CREATE TABLE IF NOT EXISTS mailTokens (
	tokenID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tokenContent VARCHAR(30) NOT NULL,
	tokenMeta VARCHAR(200) NOT NULL,
	tokenMail VARCHAR(30) NOT NULL,
	tokenType INT NOT NULL,
	tokenIP VARCHAR(30) NOT NULL,
	tokenExpireTime timestamp NOT NULL
	)';/* tokenType: 0=register, 1=resetPW*/
	$mysqli->query($table);
	
	#tracks
	$table='
	CREATE TABLE IF NOT EXISTS tracks (
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(200) NOT NULL,
	artist VARCHAR(200) NOT NULL,
	album VARCHAR(200) NOT NULL,
	track_number INT NOT NULL
	)';
	$mysqli->query($table);
	
	$mysqli->close();
}
?>
