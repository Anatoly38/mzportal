<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitoring
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class MonDocumentViewQuery extends ClActiveRecord 
{
    protected $source = 'mon_documents_view';
    public $oid;
    public $pattern_id;
    public $lpu_id;
    public $period_id;
    public $monitoring_id;
    public $мониторинг;
    public $шаблон;
    public $лпу;
    public $начало;
    public $окончание;
    public $наименование_периода;
    public $Период;
    public $код_периода;
    public $тип_отчета;
    public $год;
    public $статус;
    public $шаблон_печати;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.pattern_id,
                        a.lpu_id,
                        a.period_id,
                        a.monitoring_id,
                        a.мониторинг,
                        a.шаблон,
                        a.лпу,
                        a.начало,
                        a.окончание,
                        a.наименование_периода,
                        a.Период,
                        a.код_периода,
                        a.тип_отчета,
                        a.год,
                        a.статус,
                        a.шаблон_печати
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Документ не существует");
        }
        $this->oid          = $oid;
        $this->pattern_id   = $data['pattern_id'];
        $this->lpu_id       = $data['lpu_id'];
        $this->period_id    = $data['period_id'];
        $this->monitoring_id = $data['monitoring_id'];
        $this->мониторинг   = $data['мониторинг'];
        $this->шаблон       = $data['шаблон'];
        $this->лпу          = $data['лпу'];
        $this->начало       = $data['начало'];
        $this->окончание    = $data['окончание'];
        $this->наименование_периода = $data['наименование_периода'];
        $this->Период       = $data['Период'];
        $this->код_периода  = $data['код_периода'];
        $this->тип_отчета   = $data['тип_отчета'];
        $this->год          = $data['год'];
        $this->статус       = $data['статус'];
        $this->шаблон_печати = $data['шаблон_печати'];
    }
    public function delete()
    {
    }
    public function update() 
    {
    }
    public function insert()
    {
    }
}
?>