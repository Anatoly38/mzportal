<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport_LPU
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class TaxQuery extends ClActiveRecord
{
    protected $source = 'taxes';
    public $oid;
    public $инн;
    public $кпп;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.инн,
                        a.кпп
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Информация о налоговой идентификации не найдена");
        }
        $this->oid = $oid;
        $this->инн = $data['инн'];
        $this->кпп = $data['кпп'];
    }
    
    public static function find_by_inn($inn)
    {
        if (!$inn) {
            throw new Exception("Код учреждения для поиска не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM {$this->source} WHERE inn = :1";
        list($id) = $dbh->prepare($query)->execute($inn)->fetch_row();
        if(!$id) {
            throw new Exception("Код учреждения не найден");
        }
        return new TaxQuery($id);

    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код записи");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        инн = :1,
                        кпп = :2 
                     WHERE 
                        oid = :3";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->инн, 
                                        $this->кпп,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = 'Налогововая идентификация';
            $obj->description = "Инн: {$this->инн}, КПП: {$this->кпп}";
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
        $obj->name = 'Налогововая идентификация';
        $obj->description = "Инн: {$this->инн}, КПП: {$this->кпп}";
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, инн, кпп)
                    VALUES(:1, :2, :3)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->инн,
                                        $this->кпп
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }

    }
}

?>