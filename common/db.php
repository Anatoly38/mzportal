<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
//defined( '_MZEXEC' ) or die( 'Restricted access' );

class DB_PDO {
    protected $user;
    protected $pass;
    protected $dbhost;
    protected $dbname;
    protected $dbh;    // Идентификатор подключения к базе данных

    public function __construct($user, $pass, $dbhost, $dbname) {
        $this->user = $user;
        $this->pass = $pass;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
    }
    
    protected function connect() { // По умолчанию Mysql
        try {  
            $this->dbh = new PDO("mysql:host={$this->dbhost};dbname={$this->dbname}", $this->user, $this->pass);  
            $this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
        }  
        catch(PDOException $e) {  
            echo 'Connection failed: ' . $e->getMessage();  
        }
    }
    
    public function query($query) {
        if(!$this->dbh) {
            $this->connect();
        }
        try 
        {
            $ret = $this->dbh->query($query);
        } 
        catch(PDOException $e) {
            echo 'Query failed: ' . $e->getMessage();  
            $ret = null;
        }
        $stmt = new Statement_PDO($this->dbh);
        $stmt->pdo_object = $ret;
        return $stmt;
    }    
    
    public function execute($query) {
        $stmt = $this->query($query);
        return $stmt;
    }
    
    public function prepare($query) {
        if(!$this->dbh) {
            $this->connect();
        }
        $stmt = new Statement_PDO($this->dbh, $query);
        $stmt->prepare();
        return $stmt;
    }
    
    public function get_fields($tablename) {
        if(!$this->dbh) {
            $this->connect();
        }
        $query = "SHOW FULL COLUMNS FROM {$tablename}";
        $stmt = $this->execute($query);
        $fields = array();
        while ($row_fieldsquery = $stmt->fetch_assoc()) {
            $name     = $row_fieldsquery['Field'];
            $fields[$name] = array();
            $fields[$name]["type"]      = $row_fieldsquery['Type'];
            $fields[$name]["null"]      = $row_fieldsquery['Null'];
            $fields[$name]["key"]       = $row_fieldsquery['Key'];
            $fields[$name]["comment"]   = $row_fieldsquery['Comment'];
        }
        return $fields;
    }
}

class Statement_PDO
{
    public $query;
    public $pdo_object;
    protected $dbh;
  
    public function __construct($dbh, $query = null) 
    {
        $this->query = $query;
        $this->dbh = $dbh;
        if(!$dbh) {
            throw new PDOException("PDO объект пуст");
        }
    }
    
    public function prepare()
    {
        $this->pdo_object = $this->dbh->prepare($this->query);
    }

    public function execute() 
    {
        $arr_values = func_get_args();
        $binds = array();
        foreach($arr_values as $index => $value) {
            $order = $index + 1;
            $binds[':' . $order] = $value;
        }
        try 
        {
            $this->pdo_object->execute($binds);
        } 
        catch(PDOException $e) {
            echo 'Execute failed: ' . $e->getMessage();  
        }
        return $this;
    }

    public function fetch_row() 
    {
        if(!$this->pdo_object) {
            throw new PDOException("Запрос не выполнен");
        } 
        return $this->pdo_object->fetch(PDO::FETCH_NUM);
    }
    
    public function fetch_assoc() 
    {
        if(!$this->pdo_object) {
            throw new PDOException("Запрос не выполнен");
        }
        return $this->pdo_object->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetchall_assoc() 
    {
        if(!$this->pdo_object) {
            throw new PDOException("Запрос не выполнен");
        }
        return $this->pdo_object->fetchAll(PDO::FETCH_ASSOC);    
    }
    
    public function fetch() 
    {
        if(!$this->pdo_object) {
            throw new PDOException("Запрос не выполнен");
        }
        $retval = array();
        return $this->pdo_object->fetchAll(PDO::FETCH_COLUMN, 0);    
    }
}

// Универсальная обработка набора записей из БД
class DB_Result {
    protected $stmt;
    protected $result = array();
    private $rowIndex = 0;
    private $currIndex = 0;
    private $done = false;
    public function __construct($stmt) {
        $this->stmt = $stmt;
    }
    public function first() {
        if(!$this->result) {
            $this->result[$this->rowIndex++] = $this->stmt->fetch_assoc();
        }
        $this->currIndex = 0;
        return $this;
    }
    public function last() {
        if(!$this->done) {
            array_push($this->result, $this->stmt->fetchall_assoc());
        }
        $this->done = true;
        $this->currIndex = $this->rowIndex = count($this->result) - 1;
        return $this;
    }
    public function next() {
        //$offset = 0;
        if($this->done) {
            return false;
        }
        $offset = $this->currIndex + 1;
        if(!isset($this->result[$offset])) {
            $row = $this->stmt->fetch_assoc();
            if(!$row) {
                $this->done = true;
                return false;
            }
            $this->result[$offset] = $row;
            ++$this->rowIndex;
            ++$this->currIndex;
            return $this;
        }
        else {
            ++$this->currIndex;
            return $this;
        }
    }
    public function prev() {
        if($this->currIndex == 0) {
            return false;
        }
        --$this->currIndex;
        return $this;
    }
    public function __get($column) {
        if(array_key_exists($column,  $this->result[$this->currIndex])) {
            return $this->result[$this->currIndex][$column];
        }
    }
}

// Установка подключения к БД mzportal
class DB_mzportal extends DB_PDO 
{
    protected $user   = "root";
    protected $pass   = "4lbt2f";
    protected $dbhost = "localhost";
    protected $dbname = "mzportal";
    //protected $dbname = "attest";

    public function __construct() { }
}


?>