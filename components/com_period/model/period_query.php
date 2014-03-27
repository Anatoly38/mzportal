<?php
/**
* @version		$Id: period_query.php,v 1.0 2011/09/16 13:13:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2009 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class PeriodQuery extends ClActiveRecord 
{
    protected $source = 'periods';
    public $oid;
    public $начало;
    public $окончание;
    public $шаблон_периода;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.начало,
                        a.окончание,
                        a.шаблон_периода
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Период не существует");
        }
        $this->oid              = $oid;
        $this->начало           = $data['начало'];
        $this->окончание        = $data['окончание'];
        $this->шаблон_периода   = $data['шаблон_периода'];
    }
    
    public static function find_period($begin = null, $end = null)
    {
        if (!$begin) {
            throw new Exception("Начало периода не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM periods WHERE начало = :1 AND окончание = :2";
        list($id) = $dbh->prepare($query)->execute($begin, $end)->fetch_row();
        if(!$id) {
            throw new Exception("Код периода не найден");
        }
        return new PeriodQuery($id);
    }

    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = $this->начало . ' ' . $this->окончание ;
        $obj->description = $this->шаблон_периода;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    начало,
                    окончание,     
                    шаблон_периода)
                    VALUES(:1, :2, :3, :4)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->начало,
                                        $this->окончание,
                                        $this->шаблон_периода
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
}
?>