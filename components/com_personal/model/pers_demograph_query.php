<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
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

class PersDemographQuery extends ClActiveRecord implements ActiveRecord 
{
    protected $source = 'pers_demographic';
    public $oid;
    public $фамилия;
    public $имя;
    public $отчество;
    public $пол;
    public $дата_рождения;
    public $место_рождения;
    public $дата_смерти;
    public $место_смерти;
    public $гражданство;
    public $социальный_статус;
    public $занятость;
    public $код_военнослужащего;
    public $инвалидность; 
    public $категория_льготы;
    public $житель_села;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.фамилия,
                        a.имя,
                        a.отчество,
                        a.пол,
                        a.дата_рождения, 
                        a.место_рождения,
                        a.дата_смерти,
                        a.место_смерти,
                        a.гражданство,
                        a.социальный_статус,
                        a.занятость,
                        a.код_военнослужащего,
                        a.инвалидность,
                        a.категория_льготы,
                        a.житель_села
                    FROM {$this->source} AS a
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не найдена");
        }
        $this->oid          = $oid;
        $this->фамилия      = $data['фамилия'];
        $this->имя          = $data['имя'];
        $this->отчество     = $data['отчество'];
        $this->пол          = $data['пол'];
        $this->дата_рождения    = $data['дата_рождения'];
        $this->место_рождения   = $data['место_рождения'];
        $this->дата_смерти      = $data['дата_смерти'];
        $this->место_смерти     = $data['место_смерти'];
        $this->гражданство      = $data['гражданство'];        
        $this->социальный_статус = $data['социальный_статус'];
        $this->занятость        = $data['занятость'];
        $this->код_военнослужащего = $data['код_военнослужащего'];
        $this->инвалидность     = $data['инвалидность'];
        $this->категория_льготы = $data['категория_льготы'];
        $this->житель_села      = $data['житель_села'];
    }

    public static function findByName($name = null)
    {
        if (!name) {
            throw new Exception("Фамилия для поиска не определена");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM " . $this->source . " WHERE фамилия = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Субъект не найден");
        }
        return new PersDemographQuery($id);
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
                        фамилия = :1,
                        имя = :2, 
                        отчество = :3,
                        пол =:4,
                        дата_рождения = :5, 
                        место_рождения = :6,
                        дата_смерти = :7,
                        место_смерти = :8,
                        гражданство = :9,
                        социальный_статус = :10,
                        занятость = :11,
                        код_военнослужащего = :12,
                        инвалидность = :13,
                        категория_льготы = :14,
                        житель_села = :15
                     WHERE 
                        oid = :16";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->фамилия, 
                                        $this->имя, 
                                        $this->отчество, 
                                        $this->пол, 
                                        $this->дата_рождения,
                                        $this->место_рождения, 
                                        $this->дата_смерти,
                                        $this->место_смерти,
                                        $this->гражданство,
                                        $this->социальный_статус,
                                        $this->занятость, 
                                        $this->код_военнослужащего,
                                        $this->инвалидность,
                                        $this->категория_льготы, 
                                        $this->житель_села,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании демографических данных успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии демографических данных не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = $this->фамилия;
            $obj->description = $this->дата_рождения;
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
        $obj->name = $this->фамилия;
        $obj->description = $this->дата_рождения;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source}
                    (oid, 
                    фамилия, 
                    имя, 
                    отчество, 
                    пол, 
                    дата_рождения, 
                    место_рождения, 
                    дата_смерти,
                    место_смерти,
                    гражданство,
                    социальный_статус, 
                    занятость, 
                    код_военнослужащего, 
                    инвалидность, 
                    категория_льготы, 
                    житель_села)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15, :16)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->фамилия, 
                                        $this->имя, 
                                        $this->отчество, 
                                        $this->пол, 
                                        $this->дата_рождения,
                                        $this->место_рождения, 
                                        $this->дата_смерти,
                                        $this->место_смерти,
                                        $this->гражданство,
                                        $this->социальный_статус,
                                        $this->занятость, 
                                        $this->код_военнослужащего,
                                        $this->инвалидность,
                                        $this->категория_льготы, 
                                        $this->житель_села
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
    
    public function get_fio()
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова get_fio() необходим код субъекта");
        }
        return "{$this->фамилия} {$this->имя} {$this->отчество}";
    }
}
?>