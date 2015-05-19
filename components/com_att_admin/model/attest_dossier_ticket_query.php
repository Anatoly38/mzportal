<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class AttestDossierTicketQuery extends ClActiveRecord 
{
    protected $source = 'attest_dossier_ticket_view';
    public $oid;
    public $тема;
    public $настройка;
    public $в_процессе;
    public $текущий_вопрос;
    public $начало_теста;
    public $окончание_теста;
    public $продолжительность;
    public $реализована;
    public $статус;
    public $оценка;
    public $балл;
    public $dossier_id;
    public $фио;
    
    public function __construct($oid = false)
    {
        if ($oid === false) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.тема,
                        a.настройка,
                        a.запуск_теста,
                        a.в_процессе,
                        a.текущий_вопрос,
                        a.начало_теста,
                        a.окончание_теста,
                        a.продолжительность,
                        a.реализована,
                        a.статус,
                        a.оценка,
                        a.балл,
                        a.dossier_id,
                        a.фио
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Попытка тестирования не существует");
        }
        $this->oid              = $oid;
        $this->тема             = $data['тема'];
        $this->настройка        = $data['настройка'];
        $this->запуск_теста     = $data['запуск_теста'];
        $this->в_процессе       = $data['в_процессе'];
        $this->текущий_вопрос   = $data['текущий_вопрос'];
        $this->начало_теста     = $data['начало_теста'];
        $this->окончание_теста  = $data['окончание_теста'];
        $this->продолжительность = $data['продолжительность'];
        $this->реализована      = $data['реализована'];
        $this->статус           = $data['статус'];
        $this->оценка           = $data['оценка'];
        $this->балл             = $data['балл'];
        $this->dossier_id       = $data['dossier_id'];
        $this->фио              = $data['фио'];
        
    }

}
?>