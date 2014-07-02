<?php
/**
* @version		$Id$
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

class TerritoryQuery extends ClActiveRecord implements ActiveRecord
{
    protected $source = 'pasp_territory';
    public $oid;
    public $уровень; // Федеральный округ, Регион, Муниципальное образование
    public $ОКАТО;
    public $наименование;
    public $сокр_наименование;
    public $код_ОУЗ; // Код присвоенный МО органом управления здравоохранения
    public $подчинение;
    public $parent_id;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $this->link_type = MZConfig::$territory_lpu;
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        уровень, 
                        ОКАТО,
                        наименование,
                        сокр_наименование,
                        код_ОУЗ
                    FROM {$this->source}
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Объект не существует");
        }
        $this->oid = $oid;
        $this->уровень = $data['уровень'];
        $this->ОКАТО = $data['ОКАТО'];
        $this->наименование = $data['наименование'];
        $this->сокр_наименование = $data['сокр_наименование'];
        $this->код_ОУЗ = $data['код_ОУЗ'];    
        $this->parent_id = $this->get_parent();
        $parent = new TerritoryQuery($this->parent_id);
        $this->подчинение = $parent->наименование;
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Имя территории для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM {$this->source} WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код территории не найден");
        }
        return new TerritoryQuery($id);
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код учреждения");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        {$this->source} 
                    SET 
                        уровень = :1, 
                        ОКАТО = :2,
                        наименование = :3,
                        сокр_наименование = :4,
                        код_ОУЗ = :5
                     WHERE 
                        oid = :6"; 
        $dbh->prepare($query)->execute( 
                                        $this->уровень, 
                                        $this->ОКАТО, 
                                        $this->наименование,
                                        $this->сокр_наименование,
                                        $this->код_ОУЗ,
                                        $this->oid
                                        );
        $obj = new MZObject($this->oid);
        $obj->name = $this->наименование;
        $obj->update();
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
        $obj->name = $this->наименование; 
        $obj->description = $class_name . ' объект';
        $obj->deleted = 0;
        $obj->create();
        $dbh = new DB_mzportal;
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source}  
                    (oid, уровень, ОКАТО, наименование, сокр_наименование, код_ОУЗ)
                    VALUES(:1, :2, :3, :4, :5, :6)";
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->уровень, 
                                        $this->ОКАТО, 
                                        $this->наименование,
                                        $this->сокр_наименование,
                                        $this->код_ОУЗ
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
}

?>