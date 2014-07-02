<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitotings
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class MonReestrQuery extends ClActiveRecord implements ActiveRecord 
{
    protected $source = 'mon_monitorings';
    public $oid;
    public $наименование;
    public $описание;
    public $рег_документ;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.наименование,
                        a.описание,
                        a.рег_документ
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Мониторинг не существует");
        }
        $this->oid = $oid;
        $this->наименование = $data['наименование'];
        $this->описание     = $data['описание'];
        $this->рег_документ = $data['рег_документ'];
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Наименование мониторинга для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM {$this->source} WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код мониторинга не найден");
        }
        return new MonReestrQuery($id);
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
                        наименование    = :1,
                        описание        = :2,
                        рег_документ    = :3
                     WHERE 
                        oid             = :4";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->наименование,
                                        $this->описание,
                                        $this->рег_документ,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных мониторинга успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных мониторинга не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = $this->наименование;
            $obj->description = $this->описание;
            $obj->update();
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения <object> не сохранены!');
            return false;
        }
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
        $obj->description = $this->описание;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, наименование, описание, рег_документ)
                    VALUES(:1, :2, :3, :4)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->наименование,
                                        $this->описание,
                                        $this->рег_документ
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
}

?>