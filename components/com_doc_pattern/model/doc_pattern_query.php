<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2009-2010 МИАЦ ИО
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
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

class DocPatternQuery extends ClActiveRecord
{
    protected $source = 'mon_doc_patterns';
    public $oid;
    public $имя;
    public $наименование;
    public $описание;
    public $вид;
    public $периодичность;
    public $версия;
    public $статус;
    public $дата_утверждения;
    public $дата_исключения;
    public $основание;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.имя, 
                        a.наименование, 
                        a.описание,
                        a.вид,
                        a.периодичность,
                        a.версия,
                        a.статус,
                        a.дата_утверждения,
                        a.дата_исключения,
                        a.основание
                    FROM {$this->source} AS a 
                    WHERE a.oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Описание не найдено");
        }
        $this->oid = $oid;
        $this->имя              = $data['имя'];
        $this->наименование     = $data['наименование'];
        $this->описание         = $data['описание'];
        $this->вид              = $data['вид'];
        $this->периодичность    = $data['периодичность'];
        $this->версия           = $data['версия'];
        $this->статус           = $data['статус'];
        $this->дата_утверждения = $data['дата_утверждения'];
        $this->дата_исключения  = $data['дата_исключения'];
        $this->основание        = $data['основание'];
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
        return new DocPatternQuery($id);
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
                        a.имя           = :1, 
                        a.наименование  = :2, 
                        a.описание      = :3,
                        a.вид           = :4,
                        a.периодичность = :5,
                        a.версия        = :6,
                        a.статус        = :7,
                        a.дата_утверждения  = :8,
                        a.дата_исключения   = :9,
                        a.основание         = :10
                     WHERE 
                        a.oid = :11"; 
        try {
            $dbh->prepare($query)->execute( 
                                        $this->имя,
                                        $this->наименование,
                                        $this->описание,
                                        $this->вид,
                                        $this->периодичность,
                                        $this->версия, 
                                        $this->статус,
                                        $this->дата_утверждения,
                                        $this->дата_исключения,
                                        $this->основание,
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании описания документа успешно сохранены');
        }
        catch (MysqlException $e) {
            Message::error('Ошибка: изменения при редактированиии описания документа не сохранены!');
            return false;
        }
        $obj = new MZObject($this->oid);
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
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
        $obj->description = $this->описание;
        $obj->deleted = 0;
        $obj->create();
        $dbh = new DB_mzportal;
        $this->uid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, имя, наименование, описание, вид, периодичность, версия, статус, дата_утверждения, дата_исключения, основание )
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11)";
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->имя,
                                        $this->наименование,
                                        $this->описание,
                                        $this->вид,
                                        $this->периодичность,
                                        $this->версия, 
                                        $this->статус,
                                        $this->дата_утверждения,
                                        $this->дата_исключения,
                                        $this->основание
                                        );
        }
        catch (MysqlException $e) {
            Message::error('Ошибка сохранения БД ' . $e->code);
        }
    }

}
?>