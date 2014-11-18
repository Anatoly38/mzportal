<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
//defined( '_MZEXEC' ) or die( 'Restricted access' );

// Обработка ошибок подключения к БД
class MysqlException extends Exception {
    public $backtrace;
    public $message;
    public $code;
    
    public function __construct($message = false, $code = false) {
    if(!$message) {
        $this->message = mysql_error();
    }
    if(!$code) {
        $this->code = mysql_errno();
    }
    $this->backtrace = debug_backtrace();
  }
}
 
class DB_Mysql {
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
    protected function connect() {
        $this->dbh = mysql_pconnect($this->dbhost, $this->user, $this->pass, 65536);
        if(!is_resource($this->dbh) || !mysql_select_db($this->dbname, $this->dbh)) {
            throw new MysqlException("Ошибка подключения к базе данных");
        }
        else {
            $set_col = mysql_query("SET NAMES 'utf8'", $this->dbh);
            if(!$set_col) {
                throw new MysqlException("Ошибка установки кодовой страницы при подключении к базе данных");
            }      
        }
    }
    public function execute($query) {
        if(!$this->dbh) {
            $this->connect();
        }
        $ret = mysql_query($query, $this->dbh);
        if(!$ret) {
            throw new MysqlException("Пустой результат запроса");
        }
        else if(!is_resource($ret)) {
            return true;
        } 
        else {
            $stmt = new DB_MysqlStatement($this->dbh, $query);
            $stmt->result = $ret;
            return $stmt;
        }
    }
    public function prepare($query) {
        if(!$this->dbh) {
            $this->connect();
        }
        return new DB_MysqlStatement($this->dbh, $query);
    }
    public function get_fields($tablename) {
        if(!$this->dbh) {
            $this->connect();
        }
        $query = "SHOW FULL COLUMNS FROM {$tablename}";
        $stmt = $this->execute($query);
        $fields = $stmt->get_fields();
        return $fields;
    }
}

class DB_MysqlStatement 
{
    public $result;
    public $query;
    protected $dbh;
    protected $binds = array();
    
  
    public function __construct($dbh, $query) 
    {
        $this->query = $query;
        $this->dbh = $dbh;
        if(!is_resource($dbh)) {
            throw new MysqlException("Ошибка соединения с базой данных");
        }
    }
    public function execute() 
    {
        $binds = func_get_args();
        foreach($binds as $index => $name) {
            $this->binds[$index + 1] = $name;
        }
        $cnt = count($binds);
        $query = $this->query;
        foreach ($this->binds as $ph => $pv) {
            if (!is_null($pv)) {
                $query = preg_replace('/(:'.$ph.'){1}/', "'".mysql_escape_string($pv)."'" , $query, 1);
            }
            else {
                $query = preg_replace('/(:'.$ph.'){1}/', ' NULL ' , $query, 1);
            }
        }
        //print_r($query);
        $this->result = mysql_query($query, $this->dbh);
        if(!$this->result) {
          throw new MysqlException("Запрос не выполнен");
        }
        return $this;
    }
    public function fetch_row() 
    {
        if(!$this->result) {
            throw new MysqlException("Запрос не выполнен");
        } 
        return mysql_fetch_row($this->result);
    }
    public function fetch_assoc() 
    {
        if(!$this->result) {
            throw new MysqlException("Запрос не выполнен");
        }
        return mysql_fetch_assoc($this->result);
    }
    public function fetchall_assoc() 
    {
        if(!$this->result) {
            throw new MysqlException("Запрос не выполнен");
        }
        $retval = array();
        while($row = $this->fetch_assoc()) {
            $retval[] = $row;
        }
        return $retval;
    }
    
    public function fetch() 
    {
        if(!$this->result) {
            throw new MysqlException("Запрос не выполнен");
        }
        $retval = array();
        while (list($value) = $this->fetch_row()) {
            $retval[] = $value;
        }
        return $retval;
    }

    public function get_fields() {
        $fields = array();
        while ($row_fieldsquery = $this->fetch_assoc()) {
            $name     = $row_fieldsquery['Field'];
            $fields[$name] = array();
            $fields[$name]["type"]      = $row_fieldsquery['Type'];
            $fields[$name]["null"]      = $row_fieldsquery['Null'];
            $fields[$name]["key"]       = $row_fieldsquery['Key'];
            $fields[$name]["default"]   =  $row_fieldsquery['Default'];
            $fields[$name]["comment"]   = $row_fieldsquery['Comment'];
        }
        return $fields;
    }
}

// Универсальная обработка набора записей из БД
class DB_Result {
    protected $stmt;
    protected $result = array();
    private $rowIndex = 0;
    private $currIndex = 0;
    private $done = false;
    public function __construct(DB_MysqlStatement $stmt) {
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
class DB_mzportal extends DB_Mysql 
{
    protected $user   = "root";
    protected $pass   = "4lbt2f";
    protected $dbhost = "localhost";
    //protected $dbname = "attest";
    protected $dbname = "mzportal";

    public function __construct() { }
}

// Установка подключения к БД add
class DB_add extends DB_Mysql 
{
    protected $user   = "root";
    protected $pass   = "4lbt2f";
    protected $dbhost = "localhost";
    protected $dbname = "add";

    public function __construct() { }
}

?>