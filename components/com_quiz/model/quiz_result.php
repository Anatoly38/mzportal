<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class QuizResult  
{
    private $source = 'quiz_result';
    public $oid;
    public $result;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT
                        `a`.`result`
                    FROM {$this->source} AS a 
                    WHERE `a`.`oid` = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Не найден результат тестирования");
        }
        $this->oid      = $oid;
        $this->result   = $data['result'];
    }

    public function update() 
    {
        if($this->oid === null || $this->oid === false)
        {
            throw new Exception("Для вставки/обновления результата теста необходим код попытки");
        }
        $dbh = new DB_mzportal;
        $query = "INSERT INTO {$this->source} (`oid`, `result`) VALUES(:1, :2) ON DUPLICATE KEY UPDATE `result` = :2";
        try {
            $dbh->prepare($query)->execute( 
                                            $this->oid,
                                            $this->result
                                          );
            return true;
        } 
        catch (Exception $e) {
            return false;
        }
    }

}
?>