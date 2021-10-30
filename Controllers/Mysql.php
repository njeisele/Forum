<?php

include 'config/Dbconfig.php';

class Mysql extends Dbconfig {

    public $connectionString;
    public $dataSet;
    private $sqlQuery;
    
    protected $databaseName;
    protected $hostName;
    protected $userName;
    protected $passCode;

    function Mysql() {
        $this -> connectionString = NULL;
        $this -> sqlQuery = NULL;
        $this -> dataSet = NULL;

        $dbPara = new Dbconfig();
        $this -> databaseName = $dbPara -> dbName;
        $this -> hostName = $dbPara -> serverName;
        $this -> userName = $dbPara -> userName;
        $this -> passCode = $dbPara ->passCode;
        $dbPara = NULL;
    }
  
    function dbConnect()    {
        $this -> connectionString = mysql_connect($this -> serverName,$this -> userName,$this -> passCode);
        mysql_select_db($this -> databaseName,$this -> connectionString);
        return $this -> connectionString;
    }

    function dbDisconnect() {
        $this -> connectionString = NULL;
        $this -> sqlQuery = NULL;
        $this -> dataSet = NULL;
        $this -> databaseName = NULL;
        $this -> hostName = NULL;
        $this -> userName = NULL;
        $this -> passCode = NULL;
    }

    function selectAll($tableName)  {
        $this -> sqlQuery = 'SELECT * FROM '.$this -> databaseName.'.'.$tableName;
        $this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
        return $this -> dataSet;
    }

    function selectWhere($tableName,$rowName,$operator,$value,$valueType)   {
        $this -> sqlQuery = 'SELECT * FROM '.$tableName.' WHERE '.$rowName.' '.$operator.' ';
        if($valueType == 'int') {
            $this -> sqlQuery .= $value;
        }
        else if($valueType == 'char')   {
            $this -> sqlQuery .= "'".$value."'";
        }
        $this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
        $this -> sqlQuery = NULL;
        return $this -> dataSet;
        #return $this -> sqlQuery;
    }


    function insertInto($tableName,$values) {
        $i = NULL;
        #$this -> sqlQuery = NULL;
    }

    function selectFreeRun($query) {
        $this -> dataSet = mysql_query($query,$this -> connectionString);
        return $this -> dataSet;
    }

    function freeRun($query) {
        return mysql_query($query,$this -> connectionString);
    }
	
    function query($query) {
	return mysql_query($query, $this -> connectionString);
    } 
    
     function getSingle($query) {
	$result = $this -> query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
     }	
	
}
?>

