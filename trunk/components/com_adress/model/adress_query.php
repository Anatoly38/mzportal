<?php
/**
* @version		$Id: adress_query.php,v 1.0 2010/05/28 14:24:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Adress
* @copyright	Copyright (C) 2010 МИАЦ ИО
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

class AdressQuery extends ClActiveRecord implements ActiveRecord 
{
    protected $source = 'adress';
    public $oid;
    public $строка_адреса;
    public $вид_адреса;
    public $код_кладр;
    public $индекс;
    public $область;
    public $район;
    public $город;
    public $населенный_пункт;
    public $улица;
    public $дом;
    public $строение; 
    public $квартира;
    public $дата_регистрации;
    public $регистрация;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        строка_адреса,
                        вид_адреса,
                        код_кладр,
                        индекс,
                        область, 
                        район,
                        город,
                        населенный_пункт,
                        улица,
                        дом,
                        строение,
                        квартира,
                        дата_регистрации,
                        регистрация
                    FROM {$this->source}  
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись адреса не найдена");
        }
        $this->oid = $oid;
        $this->строка_адреса = $data['строка_адреса'];
        $this->вид_адреса   = $data['вид_адреса'];
        $this->код_кладр    = $data['код_кладр'];
        $this->индекс       = $data['индекс'];
        $this->область      = $data['область'];
        $this->район        = $data['район'];
        $this->город        = $data['город'];
        $this->населенный_пункт = $data['населенный_пункт'];
        $this->улица        = $data['улица'];
        $this->дом          = $data['дом'];
        $this->строение     = $data['строение'];
        $this->квартира     = $data['квартира'];
        $this->дата_регистрации = $data['дата_регистрации'];
        $this->регистрация  = $data['регистрация'];
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код субъекта");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source}  
                    SET
                        строка_адреса = :1,
                        вид_адреса = :2, 
                        код_кладр = :3,
                        индекс =:4,
                        область = :5, 
                        район = :6,
                        город = :7,
                        населенный_пункт = :8,
                        улица = :9,
                        дом = :10,
                        строение = :11,
                        квартира = :12,
                        дата_регистрации = :13,
                        регистрация = :14
                     WHERE 
                        oid = :15";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->строка_адреса, 
                                        $this->вид_адреса, 
                                        $this->код_кладр, 
                                        $this->индекс, 
                                        $this->область,
                                        $this->район, 
                                        $this->город, 
                                        $this->населенный_пункт,
                                        $this->улица,
                                        $this->дом,
                                        $this->строение, 
                                        $this->квартира,
                                        $this->дата_регистрации,
                                        $this->регистрация,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании адреса успешно сохранены');
        } 
        catch (MysqlException $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии адреса не сохранены!');
            return false;
        }
        $obj = new MZObject($this->oid);
        $obj->name = $this->код_кладр;
        $obj->description = $this->улица . ' ' . $this->дом . ' ' . $this->квартира ;
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
        $obj->name = $this->код_кладр;
        $obj->description = $this->улица . ' ' . $this->дом . ' ' . $this->квартира ;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source}  
                    ( oid, строка_адреса, вид_адреса, код_кладр, индекс, область, район, город, 
                    населенный_пункт, улица, дом, строение, квартира, дата_регистрации, регистрация )
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14 , :15)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->строка_адреса, 
                                        $this->вид_адреса, 
                                        $this->код_кладр, 
                                        $this->индекс, 
                                        $this->область,
                                        $this->район, 
                                        $this->город, 
                                        $this->населенный_пункт,
                                        $this->улица,
                                        $this->дом,
                                        $this->строение, 
                                        $this->квартира,
                                        $this->дата_регистрации,
                                        $this->регистрация
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка при вводе адреса ' . $e->code);
        }
        
    }
}

?>