<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2010 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

class DocDpSectionQuery extends ClActiveRecord
{
    protected $source = 'mon_section_patterns';
    public $oid;
    public $doc_pattern_id;
    public $наименование;
    public $описание;
    public $тип;
    public $диапазон_данных;
    public $диапазон_печати;
    public $шаблон;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.doc_pattern_id,
                        a.наименование, 
                        a.описание,
                        a.диапазон_данных,
                        a.диапазон_печати,
                        a.тип
                    FROM {$this->source} AS a 
                    WHERE a.oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Описание не найдено");
        }
        $this->oid = $oid;
        $this->doc_pattern_id   = $data['doc_pattern_id'];;
        $this->наименование     = $data['наименование'];
        $this->описание         = $data['описание'];
        $this->диапазон_данных  = $data['диапазон_данных'];
        $this->диапазон_печати  = $data['диапазон_печати'];
        $this->тип              = $data['тип'];
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Наименование описания не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM {$this->source} WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код описания не найден");
        }
        return new DocDpSectionQuery($id);
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код описания");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        {$this->source} AS a 
                    SET 
                        a.doc_pattern_id = :2, 
                        a.наименование = :3, 
                        a.описание = :4,
                        a.диапазон_данных = :5,
                        a.диапазон_печати = :6,
                        a.тип = :7
                     WHERE 
                        a.oid = :1";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->doc_pattern_id,
                                        $this->наименование,
                                        $this->описание,
                                        $this->диапазон_данных,
                                        $this->диапазон_печати,
                                        $this->тип
                                        );
            $m->enque_message('alert', 'Изменения при редактировании раздела документа успешно сохранены');
        }
        catch (MysqlException $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии раздела документа не сохранены!');
            return false;
        }
        $obj = new MZObject($this->oid);
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
        $obj->update();
    }

        public function save_template() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код раздела");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        {$this->source} AS a 
                    SET 
                        a.шаблон = :2
                     WHERE 
                        a.oid = :1";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->шаблон
                                        );
            $m->enque_message('alert', 'Изменения при редактировании шаблона раздела успешно сохранены');
        }
        catch (MysqlException $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии шаблона раздела не сохранены!');
            return false;
        }
    }
    
    public function insert()
    {
        if ($this->oid) {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        if (!$this->doc_pattern_id) {
            throw new Exception("Не определено описание документа, в который добавляется раздел");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
        $obj->deleted = 0;
        $obj->create();
        $dbh = new DB_mzportal;
        $this->uid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, doc_pattern_id, наименование, описание, диапазон_данных, диапазон_печати, тип)
                    VALUES(:1, :2, :3, :4, :5, :6, :7)";
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->doc_pattern_id,
                                        $this->наименование,
                                        $this->описание,
                                        $this->диапазон_данных,
                                        $this->диапазон_печати,
                                        $this->тип
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }
 
    public function delete()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код не определен, удаление не возможно");
        }
        $query = "DELETE FROM {$this->source} WHERE oid = :1";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute($this->oid);
    }
    
    public function get_pattern()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код раздела не определен");
        }
        return $this->doc_pattern_id;
    }
    
    public function get_template_text()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код раздела не определен");
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.шаблон
                    FROM {$this->source} AS a 
                    WHERE a.oid = :1";
        list($text) = $dbh->prepare($query)->execute($this->oid)->fetch_row();
        return $text;
    }

}
?>