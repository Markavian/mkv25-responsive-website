<?php	

# title		:	SQL Class
# version	:	1.25
# author	:	Markavian
# last edit	:	24/02/2015
# function	:	MySQL abstraction layer, custom set of MySQL functions by Markavian
# contact	:	johnbeech@mkv25.net

class Sql
{
	static $singleton;

	var $host;
	var $database;
	var $user;
	var $password;
	var $connect  = 0;
	var $result	= array();
	var $record	= array();
	var $row	= 0;
	var $count	= 0;
	var $queries = 0;

	// Static accessor for SQL connection
	public static function getInstance() {
		if(!Sql::$singleton) {

			global $SQL_CONNECTION_DETAILS;

			$host     = $SQL_CONNECTION_DETAILS[0];
			$database = $SQL_CONNECTION_DETAILS[1];
			$user     = $SQL_CONNECTION_DETAILS[2];
			$password = $SQL_CONNECTION_DETAILS[3];

			Sql::$singleton = new Sql($host, $database, $user, $password);
		}

		return Sql::$singleton;
	}

	// Connect using environment variables
	public function __construct($host, $database, $user, $password) {
		$this->host     = $host;
	    $this->database = $database;
	    $this->user     = $user;
	    $this->password = $password;
		
		$this->connect();
	}

	// Error display formatting
	// Feel free to write in error logging on server file (append file, date/time, etc.)
	function error($title, $type, $file, $line, $error, $explained, $suggestion) {
		echo "<pre>$title<br>";
		echo "_________________________<br>";
		echo "$type<br>";
		echo "$error<br>";
		echo "$explained<br>";
		echo "$suggestion</pre>";
	}

	// Connect to database
	function connect() {
	
		if($this->connect == 0) {
			$this->connect = mysql_connect($this->host, $this->user, $this->password);

			if(!$this->connect) {
				$this->error
				(
					"MySQL Error",
					"Unable to connect",
					__FILE__,
					__LINE__,
					mysql_error(),
					"Unauthorized access.",
					"Check to make sure you have the correct mysql information entered into this file. Such as password and username. \nThe host is usually localhost or localhost:/tmp/mysql5.sock."
				);
			} else {

				@mysql_select_db($this->database,$this->connect);

				if(!$this->connect) {
					$this->error
					(
						"MySQL Error",
						"Unable to connect",
						__FILE__,
						__LINE__,
						mysql_error(),
						"Able to connect, but unable to select a database.",
						"Check to make sure you have the correct mysql database entered into this file."
					);
				}				
			}
		}
  	}

	// Close connection, free results
	function close( $freeresults = 1 ) {
	
		if( $this->connect ) {
			if( $freeresults !== 1 ) {
				@mysql_free_result($this->result);
			}

			$return = @mysql_close($this->connect);
			return $return;
		} else {
			return false;
		}
	}

	// Run query on database, with identifier
	function query($query, $name = "") {
 
 		// Increment number of queries executed in this script
		$this->queries++;

		// Free result
		if (@$this->result[$name]) {
			@mysql_free_result($this->result);
		}

		// Run query
		$this->result[$name] = mysql_query($query, $this->connect);
		
		if ( !$this->result[$name] ) {
			
			$this->error(
				"MySQL Error",
				"Could not run the query: $query",
				__FILE__,
				__LINE__,
				mysql_error(),
				"You have a error in your query string.",
				"Check to make sure you dont have any common errors."
			);
			return false;
		} else {
			return $this->result[$name];
		}
	}
	
	// Run query on database, with identifier
	function multiquery($querytext, $namePrefix) {
 
 		// Separate querys, based on ';'s
		$queryArray = explode(";", $querytext);
 
 		// Reset counter
		$n = 0;		
		
 		// Run separate queries
 		foreach($queryArray as $query) {
			$this->query($query, $namePrefix.'_'.$n);
			$n++;
		}
	}

	// Return is_array? state of next row in result set $name
	function next( $name = "", $record_name = "" ) {
	
		if( $record_name == '' ) {
			$this->record = mysql_fetch_array($this->result[$name]);
		} else {
			$this->record[$record_name] = mysql_fetch_array($this->result[$name]);
		}
		return is_array( $this->record );
	}

	// Fetch an associative array of results, from specified identifier
	function fetch($name = "") {
		// Fetch array
		$this->record[$name] = mysql_fetch_array($this->result[$name]);
		
		// Return array, or false if non existant
		if(is_array( $this->record[$name] )) {
			return $this->record[$name];
		} else {
			return false;
		}
	}
	
	// Execute query $query and fetch an associate array from result set $name
	function fetch_query($query, $name) {
	
		// Execute query
		if($this->query($query, $name) != false) {
			// Fetch array
			$this->record[$name] = @mysql_fetch_array($this->result[$name]);
			
			// Return array, or false if non existant
			if(is_array( $this->record[$name] )) {
				return $this->record[$name];
			} else {
				return false;
			}
		}
	}

	// Returns the number of rows in the result set $name
	function num_rows($name = "") {
		return @mysql_num_rows($this->result[$name]);
	}

	// Returns the number of fields in the result set $name
	function num_fields($name = "") {
		return @mysql_num_fields($this->result[$name]);
	}
	
	// Return an array of field names in the result set $name
	function field_names($name = "")	{
	
		// Get number of fields
		$num_fields = $this->num_fields($name);
		
		// Create array
		if($num_fields > 0) {
			$data = array();
			for($i=0; $i<$num_fields; $i++) {
				$data[] = mysql_field_name($this->result[$name], $i);
			}
			return $data;
		} else {
			return false;
		}
	}
	
	// Return array of field types from result set $name
	function field_types($name = "") {
	
		// Get num fields
		$num_fields = $this->num_fields($name, 1);
		
		// Create array
		if($num_fields > 0) {
			$data = array();
			for($i=0; $i<$num_fields; $i++) {
				$data[mysql_field_name($this->result[$name], $i)] = mysql_field_type($this->result[$name], $i);
			}
			return $data;
		} else {
			return false;
		}
	}
	
	// Return array of field lengths from result set $name
	function field_lengths($name = "") {
	
		// Get num fields
		$num_fields = $this->num_fields($name, 1);
		
		// Create array
		if($num_fields > 0) {
			$data = array();
			for($i=0; $i<$num_fields; $i++) {
				$data[mysql_field_name($this->result[$name], $i)] = mysql_field_len($this->result[$name], $i);
			}
			return $data;
		} else {
			return false;
		}
	}
	
	// Return array of named fields, usually with the properties: Field, Type, Null, Key, Default, Extra  
	function table_info($tableName = "") {
		$data = array();
		$resultName = 'table_info_'.$tableName;
		$this->query('SHOW COLUMNS FROM '.$tableName, $resultName);
		if($this->num_rows($resultName) > 0)
		{
			while($field = $this->fetch($resultName)) {
				$data[$field['Field']] = $field;
			}
			return $data;
		} else {
			return false;
		}
	}
	
	// Return array of key properties, usually with the properties: Table, Non_unique, Key_name, Seq_in_index, Column_name, Collation, Cardinality, Sub_part, Packed, Null, Index_type, Comment    
	function table_keys($tableName = "") {
		$data = array();
		$resultName = 'table_info_'.$tableName;
		$this->query('SHOW KEYS FROM '.$tableName, $resultName);
		if($this->num_rows($resultName) > 0)
		{
			while($field = $this->fetch($resultName)) {
				$data[] = $field;
			}
			return $data;
		} else {
			return false;
		}
	}
	
	// Return number of affected rows from the last query
	function affected() {
		return @mysql_affected_rows();
	}

	// Free result set
	function free($name = "") {

		// Destroy stored data
		unset($this->record);
		unset($this->row);
		
		// Free result set
		if ($this->result[$name]) {
			@mysql_free_result( $this->result[$name] );
			return true;
		} else {
			return false;
		}
	}

	// Move pointer to location in result set $name
	function move_pointer($name = "", $number) {
		if(mysql_data_seek($this->result[$name], $number)) {
			return true;
		} else {
			return false;
		}
	}
	
	// Return last INSERT ID from query link $name
	function insert_id() {
		return mysql_insert_id();
	}
	
	function table_names($filter='')
	{
		$data = array();
		$resultName = 'database_tables_'.$this->database;
		$this->query('SHOW TABLES FROM `'.$this->database.'`', $resultName);
		if($this->num_rows($resultName) > 0)
		{
			while($field = $this->fetch($resultName)) {
				$tableName = $field[0];
				if(stripos($tableName, $filter) !== false || $filter == '') {
					$data[] = $tableName;
				}
			}
			return $data;
		} else {
			return false;
		}
	}
}
