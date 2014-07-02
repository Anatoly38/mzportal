<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class MonSectionView extends ClActiveRecord 
{
    protected $source = 'mon_sections_view';
    public $section;
    public $oid;
    public $spattern;
    public $наименование;
    public $описание;
    public $тип;
    public $заполнение;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.section,
                        a.oid,
                        a.spattern,
                        a.наименование,
                        a.описание,
                        a.тип,
                        a.заполнение
                    FROM {$this->source} AS a 
                    WHERE section = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Раздел не существует");
        }
        $this->section      = $oid;
        $this->oid          = $data['oid'];
        $this->spattern     = $data['spattern'];
        $this->наименование = $data['наименование'];
        $this->описание     = $data['описание'];
        $this->тип          = $data['тип'];
        $this->заполнение   = $data['заполнение'];
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