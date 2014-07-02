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
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class PersonnelQuery extends ClActiveRecord
{
    protected $source = 'pasp_personnel';
    public $oid;
    public $табельный_номер;
    public $снилс;
    public $инн;
    public $телефон;
    public $семейное_положение;
    public $дети;
    public $автомобиль;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.oid,
                        a.табельный_номер,
                        a.снилс,
                        a.инн,
                        a.телефон,
                        a.семейное_положение,
                        a.дети,
                        a.автомобиль
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid = $oid;
        $this->табельный_номер  = $data['табельный_номер'];
        $this->снилс            = $data['снилс'];
        $this->инн              = $data['инн'];
        $this->телефон          = $data['телефон'];
        $this->семейное_положение = $data['семейное_положение'];
        $this->дети             = $data['дети'];
        $this->автомобиль       = $data['автомобиль'];
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
        $obj->name = $this->табельный_номер;
        $obj->description = 'Карта сотрудника УЗ';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, табельный_номер, инн, снилс, телефон, семейное_положение, дети, автомобиль)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute(                                         
                                        $this->oid,
                                        $this->табельный_номер, 
                                        $this->инн,
                                        $this->снилс,
                                        $this->телефон,
                                        $this->семейное_положение,
                                        $this->дети,
                                        $this->автомобиль
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код сотрудника");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        табельный_номер = :1,
                        снилс = :2, 
                        инн = :3,
                        телефон = :4,
                        семейное_положение = :5,
                        дети =:6,
                        автомобиль =:7
                     WHERE 
                        oid = :8";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->табельный_номер, 
                                        $this->снилс, 
                                        $this->инн,
                                        $this->телефон,
                                        $this->семейное_положение,
                                        $this->дети,
                                        $this->автомобиль,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных сотрудника успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных сотрудника не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = 'Карточка сотрудника';
            $obj->description = $this->oid . ' ' . $this->табельный_номер . ' ' . $this->снилс . ' ' . $this->инн;
            $obj->update();
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }
    
    public static function get_lpu($oid)
    {
        $link = Reference::get_id('сотрудник', 'link_types');
        $data = LinkObjects::get_parents($oid, $link);
        if (is_array($data)) {
            return($data[0]);
        }
    }
}

?>