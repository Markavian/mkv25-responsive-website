<?php

# title     :  SQL Class
# version   :  1.28
# author    :  Markavian
# last edit :  04/04/2015
# function  :  MySQL abstraction layer, custom set of MySQL functions by Markavian
# contact   :  johnbeech@mkv25.net

class Sql
{
  static $singleton;

  static $errorReportsOn = true;
  static $errorReportsVisible = true;

  var $host;
  var $database;
  var $user;
  var $password;
  var $connect  = 0;
  var $result  = array();
  var $record  = array();
  var $row  = 0;
  var $count  = 0;
  var $queries = 0;

  // Static accessor for SQL connection
  public static function getInstance()
  {
    if (!Sql::$singleton)
    {
      $SQL_CONNECTION_DETAILS = Environment::get('SQL_CONNECTION_DETAILS');

      $host     = $SQL_CONNECTION_DETAILS[0];
      $database = $SQL_CONNECTION_DETAILS[1];
      $user     = $SQL_CONNECTION_DETAILS[2];
      $password = $SQL_CONNECTION_DETAILS[3];

      Sql::$singleton = new Sql($host, $database, $user, $password);
    }

    return Sql::$singleton;
  }

  // Connect using environment variables
  public function __construct($host, $database, $user, $password)
  {
    $this->error
    (
      "SQL Error",
      "Unable to connect.",
      __FILE__,
      __LINE__,
      '',
      "Unable to establish database connection.",
      "Check to make sure you have the correct mysql information entered into this file. Such as password and username. \nThe host is usually localhost or localhost:/tmp/mysql5.sock."
    );

    $this->host     = $host;
    $this->database = $database;
    $this->user     = $user;
    $this->password = $password;

    $this->connect();
  }

  // Error display formatting
  // Feel free to write in error logging on server file (append file, date/time, etc.)
  function error($title, $type, $file, $line, $error, $explanation, $suggestion)
  {
    $visible = (self::$errorReportsVisible) ? 'block' : 'none';

    if (self::$errorReportsOn)
    {
      echo <<< END
<sqlerror>
  <errorTitle>$title</errorTitle>
  <errorType>$type</errorType>
  <errorMessage>$error</errorMessage>
  <errorExplanation>$explanation</errorExplanation>
  <errorSuggestion>$suggestion</errorSuggestion>
</sqlerror>

<style>
sqlerror, sqlerror > * {
  display: $visible;
  padding: 4px;
}
sqlerror > errorTitle { font-weight: bold; }
sqlerror > errorMessage { font-weight: bold; color: red; }
</style>

END;
    }
  }

  // Connect to database
  function connect()
  {
    if ($this->connect === 0)
    {
      $this->connect = new mysqli($this->host, $this->user, $this->password, $this->database);

      if ($mysqli->connect_errno)
      {
        $this->error
        (
          "SQL Error",
          "Unable to connect.",
          __FILE__,
          __LINE__,
          $mysqli->connect_error,
          "Unable to establish database connection.",
          "Check to make sure you have the correct mysql information entered into this file. Such as password and username. \nThe host is usually localhost or localhost:/tmp/mysql5.sock."
        );
      }
    }
  }

  // Close connection, free results
  function close($freeresults=1)
  {
    $result = false;

    if ($this->connect)
    {
      if ($freeresults !== 1)
      {
        @mysqli_stmt::free_result($this->result);
      }

      $result = @mysql_close($this->connect);
    }
    return $result;
  }

  // Run query on database, with identifier
  function query($query, $name = "")
  {
    if (!$this->connect) return false;

    $result = false;

     // Increment number of queries executed in this script
    $this->queries++;

    // Free result
    if (@$this->result[$name])
    {
      @mysqli_stmt::free_result($this->result);
    }

    // Run query
    $this->result[$name] = mysqli_query($query, $this->connect);

    if (!$this->result[$name])
    {
      $this->error(
        "SQL Error",
        "Could not run the query: $query",
        __FILE__,
        __LINE__,
        $mysqli->error,
        "There was an error with the query string.",
        "Check the query string to make sure there are no syntax errors."
      );
    }
    else
    {
      $result = $this->result[$name];
    }

    return $result;
  }

  // Run query on database, with identifier
  function multiquery($querytext, $namePrefix)
  {
     // Separate querys, based on ';'s
    $queryArray = explode(";", $querytext);

     // Reset counter
    $n = 0;

     // Run separate queries
     foreach($queryArray as $query)
    {
      $this->query($query, $namePrefix.'_'.$n);
      $n++;
    }
  }

  // Fetch an associative array of results, from specified identifier
  function fetch($name = "")
  {
    $result = false;

    // Fetch array
    $this->record[$name] = mysqli_result::fetch_array($this->result[$name]);

    // Return array, or false if non existant
    if (is_array($this->record[$name]))
    {
      $result = $this->record[$name];
    }

    return $result;
  }

  // Execute query $query and fetch an associate array from result set $name
  function fetch_query($query, $name)
  {
    if (!$this->connect) return false;

    $result = false;

    // Execute query
    if ($this->query($query, $name) != false)
    {
      // Fetch array
      $this->record[$name] = @mysqli_result::fetch_array($this->result[$name]);

      // Return array, or false if non existant
      if (is_array($this->record[$name]))
      {
        $result = $this->record[$name];
      }
    }

    return $result;
  }

  // Returns the number of rows in the result set $name
  function num_rows($name = "")
  {
    return @mysqli_num_rows($this->result[$name]);
  }

  // Returns the number of fields in the result set $name
  function num_fields($name = "")
  {
    return @mysql_num_fields($this->result[$name]);
  }

  // Return an array of field names in the result set $name
  function field_names($name = "")
  {
    if (!$this->connect) return false;

    $result = false;

    // Get number of fields
    $num_fields = $this->num_fields($name);

    // Create array
    if ($num_fields > 0)
    {
      $data = array();
      for ($i = 0; $i < $num_fields; $i++)
      {
        $data[] = mysql_field_name($this->result[$name], $i);
      }
      $result = $data;
    }

    return $result;
  }

  // Return array of field types from result set $name
  function field_types($name = "")
  {
    if (!$this->connect) return false;

    $result = false;

    // Get num fields
    $num_fields = $this->num_fields($name, 1);

    // Create array
    if ($num_fields > 0)
    {
      $data = array();
      for ($i = 0; $i < $num_fields; $i++)
      {
        $key = mysql_field_name($this->result[$name], $i);
        $value = mysql_field_type($this->result[$name], $i);
        $data[$key] = $value;
      }
      $result = $data;
    }

    return $result;
  }

  // Return array of field lengths from result set $name
  function field_lengths($name = "")
  {
    if (!$this->connect) return false;

    $result = false;

    // Get num fields
    $num_fields = $this->num_fields($name, 1);

    // Create array
    if ($num_fields > 0)
    {
      $data = array();
      for ($i = 0; $i < $num_fields; $i++)
      {
        $key = mysql_field_name($this->result[$name], $i);
        $value = mysql_field_len($this->result[$name], $i);
        $data[$key] = $value;
      }
      $result = $data;
    }

    return $result;
  }

  // Return array of named fields, usually with the properties: Field, Type, Null, Key, Default, Extra
  function table_info($tableName = "")
  {
    if (!$this->connect) return false;

    $result = false;

    $data = array();
    $resultName = 'table_info_'.$tableName;
    $this->query('SHOW COLUMNS FROM '.$tableName, $resultName);

    if($this->num_rows($resultName) > 0)
    {
      while ($field = $this->fetch($resultName))
      {
        $data[$field['Field']] = $field;
      }
      $result = $data;
    }

    return $result;
  }

  // Return array of key properties, usually with the properties: Table, Non_unique, Key_name, Seq_in_index, Column_name, Collation, Cardinality, Sub_part, Packed, Null, Index_type, Comment
  function table_keys($tableName = "")
  {
    if (!$this->connect) return false;

    $result = false;

    $data = array();
    $resultName = 'table_info_'.$tableName;
    $this->query('SHOW KEYS FROM '.$tableName, $resultName);

    if($this->num_rows($resultName) > 0)
    {
      while ($field = $this->fetch($resultName))
      {
        $data[] = $field;
      }
      $result = $data;
    }

    return $result;
  }

  // Return number of affected rows from the last query
  function affected()
  {
    return @mysql_affected_rows();
  }

  // Free result set
  function free($name = "")
  {
    $result = false;

    // Destroy stored data
    unset($this->record);
    unset($this->row);

    // Free result set
    if ($this->result[$name])
    {
      @mysqli_stmt::free_result($this->result[$name]);
      $result = true;
    }

    return $result;
  }

  // Move pointer to location in result set $name
  function move_pointer($name = "", $number)
  {
    $result = false;

    if (mysql_data_seek($this->result[$name], $number))
    {
      $result = true;
    }

    return $result;
  }

  // Return last INSERT ID from query link $name
  function insert_id()
  {
    return @mysql_insert_id();
  }

  function table_names($filter='')
  {
    if (!$this->connect) return false;

    $result = false;

    $data = array();
    $resultName = 'database_tables_'.$this->database;
    $this->query('SHOW TABLES FROM `'.$this->database.'`', $resultName);

    if($this->num_rows($resultName) > 0)
    {
      while ($field = $this->fetch($resultName))
      {
        $tableName = $field[0];
        if (stripos($tableName, $filter) !== false || $filter == '')
        {
          $data[] = $tableName;
        }
      }
      $result = $data;
    }

    return $result;
  }
}
