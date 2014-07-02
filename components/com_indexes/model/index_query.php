<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Indexes
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
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );

class IndexQuery implements ActiveRecord
{
    public $oid;
    public $наименование;
    public $описание;
    public $вид;
    public $группа;
    public $тип;
    public $ед_измерения;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.наименование, 
                        a.описание, 
                        a.вид,
                        a.группа,
                        a.тип,
                        a.ед_измерения,
                        b.наименование AS спр_вид, 
                        c.наименование AS спр_группа, 
                        d.наименование AS спр_тип,
                        e.наименование AS спр_ед_измерения,
                        a.дата_утверждения,
                        a.рег_документ
                    FROM `mon_indexes` AS a 
                    LEFT JOIN 
                        (`dic_index_types` AS b, 
                        `dic_index_groups` AS c,
                        `dic_data_types` AS d,
                        `dic_units` AS e) 
                    ON 
                        (a.вид = b.код 
                        AND a.группа = c.код
                        AND a.тип = d.код
                        AND a.ед_измерения = e.код)
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Показатель не существует");
        }
        $this->oid = $oid;
        $this->наименование = $data['наименование'];
        $this->описание = $data['описание'];
        $this->вид = $data['вид'];
        $this->группа = $data['группа'];
        $this->тип = $data['тип'];
        $this->ед_измерения = $data['ед_измерения'];
        $this->дата_утверждения = $data['дата_утверждения'];
        $this->рег_документ = $data['рег_документ'];
        $this->спр_вид = $data['спр_вид'];
        $this->спр_группа = $data['спр_группа'];
        $this->спр_тип = $data['спр_тип'];
        $this->спр_ед_измерения = $data['спр_ед_измерения'];
    }

    public static function findByName($index_name = null)
    {
        if (!index_name) {
            throw new Exception("Имя показателя для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM mon_indexes WHERE наименование = :1";
        list($indexid) = $dbh->prepare($query)->execute($index_name)->fetch_row();
        if(!$indexid) {
            throw new Exception("Код показателя не найден");
        }
        return new IndexQuery($indexid);
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код показателя");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        `mon_indexes`
                    SET 
                        наименование = :1, 
                        описание = :2, 
                        вид = :3,
                        группа = :4, 
                        тип = :5, 
                        ед_измерения = :6,
                        дата_утверждения = :7, 
                        рег_документ = :8
                     WHERE 
                        oid = :9"; 
            $m = Message::getInstance();
            try {
                $dbh->prepare($query)->execute( $this->наименование, 
                                            $this->описание,
                                            $this->вид, 
                                            $this->группа,
                                            $this->тип, 
                                            $this->ед_измерения,
                                            $this->дата_утверждения, 
                                            $this->рег_документ,
                                            $this->oid);
                $m->enque_message('alert', 'Изменения при редактировании показателя успешно сохранены');
        }
        catch (MysqlException $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии показателя не сохранены!');
            return false;
        }
        $obj = new MZObject($this->oid);
        $obj->description = $this->наименование;
        $obj->update();
    }

    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код показателя, вставка невозможна");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = $class_name . ' obj';
        $obj->description = $this->наименование;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO `mon_indexes`
                    (oid, наименование, описание, вид, группа, тип, ед_измерения, дата_утверждения, рег_документ)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( $obj->obj_id,
                                        $this->наименование, 
                                        $this->описание,
                                        $this->вид, 
                                        $this->группа,
                                        $this->тип, 
                                        $this->ед_измерения,
                                        $this->дата_утверждения, 
                                        $this->рег_документ);
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: изменения при редактированиии показателя не сохранены!' . $e->code);
        }
    }
 
    public function delete()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код показателя не определен");
        }
        $query = "DELETE FROM `mon_indexes` WHERE oid = :1";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute($this->oid);
    }
    
    public function get_as_array()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код показателя не определен");
        }
        $fields = array();
        foreach($this as $key => $value) {
            $fields[$key] = $value;
        }
        return $fields;
    }
}

?>