<?php
/**
* @version		$Id$
* @package		Monitorings
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
class MonSectionQuery extends ClActiveRecord 
{
    protected $source = 'mon_sections';
    public $section;
    public $document;
    public $spattern;
    public $cache;
    public $заполнение;
   
    public function __construct($section = false)
    {
        if (!$section) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT document, spattern, заполнение FROM {$this->source} AS a WHERE a.section = :1";
        $data = $dbh->prepare($query)->execute($section)->fetch_assoc();
        if(!$data) {
            throw new Exception("Раздел документа не существует");
        }
        $this->section      = $section;
        $this->document     = $data['document'];
        $this->spattern     = $data['spattern'];
        $this->заполнение   = $data['заполнение'];
    }
    
    public static function get_by_doc_pattern($doc_id = false, $spattern_id = false)
    {
        if (!$doc_id || !$spattern_id) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.section
                    FROM mon_sections AS a 
                    WHERE a.document = :1 AND a.spattern = :2 ";
        list($id) = $dbh->prepare($query)->execute($doc_id, $spattern_id)->fetch_row();
        if(!$id) {
            throw new Exception("Раздел отчетного документа не найден");
        }
        $section_obj = new MonSectionQuery($id);
        return $section_obj;
    }

    public function insert()
    {
        if ($this->section) {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        if (!$this->document) {
            throw new Exception("Не определен отчетный документ, в который добавляется раздел");
        }
        if (!$this->spattern) {
            throw new Exception("Не определен шаблон, на основе которого заполняется раздел отчет");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = $this->document;
        $obj->description = $this->spattern;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $dbh = new DB_mzportal;
        $query = "INSERT INTO {$this->source} (section, document, spattern) VALUES(:1, :2, :3)";
        try {
            $dbh->prepare($query)->execute($obj->obj_id, $this->document, $this->spattern);
            $this->section = $obj->obj_id;
        }
        catch (MysqlException $e) {
            Message::error('Ошибка создания раздела документа ' . $e->code);
        }
    }
    
    public function save_cache($data) 
    {
        if(!$this->section)
        {
            throw new Exception("Для вызова update() необходим код раздела");
        }
        $dbh = new DB_mzportal;
        $query = "UPDATE {$this->source} AS a SET a.cache = :2 WHERE a.section = :1";
        try {
            $dbh->prepare($query)->execute($this->section, $data);
            return true;
        }
        catch (MysqlException $e) {
            Message::error('Ошибка: кэш таблицы не сохранен');
            return false;
        }
    }
    
    public function update() 
    {
        if(!$this->section)
        {
            throw new Exception("Для вызова update() необходим код раздела");
        }
        $dbh = new DB_mzportal;
        $query = "UPDATE {$this->source} AS a 
            SET a.заполнение = :2
            WHERE a.section = :1";
        try {
            $dbh->prepare($query)->execute($this->section, $this->заполнение);
            return true;
        }
        catch (MysqlException $e) {
            Message::error('Ошибка: данные таблицы не обновлены');
            return false;
        }
    }
 
    public function get_cache()
    {
        if(!$this->section) 
        {
            throw new Exception("Код раздела не определен");
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.cache
                    FROM {$this->source} AS a 
                    WHERE a.section = :1";
        list($text) = $dbh->prepare($query)->execute($this->section)->fetch_row();
        return $text;
    }
}
?>