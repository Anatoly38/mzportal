<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Register OKS
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

class RegisterOksQuery extends ClActiveRecord implements ActiveRecord 
{
    protected $source = 'register_oks';
    public $oid;
    public $pers_id;
    public $lpu_id;
    public $направитель;
    public $срок_госпитализации;
    public $приемный_покой;
    public $интенсивная_терапия;
    public $дата_поступления;
    public $диагноз_мкб10;
    public $тлт_проведение;
    public $тлт_срок;
    public $тлт_препарат;
    public $тлт_эффективность;
    public $тлт_осложнения;    
    public $дата_выписки;
    public $исход;
    public $рсц;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        pers_id,
                        lpu_id,
                        направитель,
                        срок_госпитализации,
                        приемный_покой,
                        интенсивная_терапия,
                        дата_поступления, 
                        диагноз_мкб10,
                        тлт_проведение,
                        тлт_срок,
                        тлт_препарат,
                        тлт_эффективность,
                        тлт_осложнения,
                        дата_выписки,
                        исход,
                        рсц
                    FROM " . $this->source . "  
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не найдена");
        }
        $this->oid = $oid;
        $this->pers_id = $data['pers_id'];
        $this->lpu_id = $data['lpu_id'];
        $this->направитель = $data['направитель'];
        $this->срок_госпитализации = $data['срок_госпитализации'];
        $this->приемный_покой = $data['приемный_покой'];
        $this->интенсивная_терапия = $data['интенсивная_терапия'];
        $this->дата_поступления = $data['дата_поступления'];
        $this->диагноз_мкб10 = $data['диагноз_мкб10'];
        $this->тлт_проведение = $data['тлт_проведение'];
        $this->тлт_срок = $data['тлт_срок'];
        $this->тлт_препарат = $data['тлт_препарат'];
        $this->тлт_эффективность = $data['тлт_эффективность'];
        $this->тлт_осложнения = $data['тлт_осложнения'];
        $this->дата_выписки = $data['дата_выписки'];
        $this->исход = $data['исход'];
        $this->рсц = $data['рсц'];
    }

    public function update()
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        " . $this->source . " 
                    SET
                        pers_id = :1,
                        lpu_id = :2, 
                        направитель = :3,
                        срок_госпитализации = :4,
                        приемный_покой = :5,
                        интенсивная_терапия = :6,
                        дата_поступления = :7, 
                        диагноз_мкб10 = :8,
                        тлт_проведение = :9,
                        тлт_срок = :10,
                        тлт_препарат = :11,
                        тлт_эффективность = :12,
                        тлт_осложнения = :13,
                        дата_выписки = :14,
                        исход = :15,
                        рсц = :16
                     WHERE 
                        oid = :17";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->pers_id, 
                                        $this->lpu_id, 
                                        $this->направитель,
                                        $this->срок_госпитализации, 
                                        $this->приемный_покой,
                                        $this->интенсивная_терапия,
                                        $this->дата_поступления, 
                                        $this->диагноз_мкб10,
                                        $this->тлт_проведение,
                                        $this->тлт_срок,
                                        $this->тлт_препарат,
                                        $this->тлт_эффективность,
                                        $this->тлт_осложнения,
                                        $this->дата_выписки, 
                                        $this->исход,
                                        $this->рсц,
                                        $this->oid                                        
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных ОКС регистра успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных не сохранены!');
            return false;
        }
        $obj = new MZObject($this->oid);
        $obj->name = $this->pers_id;
        $obj->description = $this->диагноз_мкб10;
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
        $obj->name = $this->pers_id;
        $obj->description = $this->диагноз_мкб10;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO " . $this->source . " 
                    (oid, pers_id, lpu_id, направитель, срок_госпитализации, приемный_покой, интенсивная_терапия,
                    дата_поступления, диагноз_мкб10, тлт_проведение, тлт_срок, тлт_препарат, тлт_эффективность, 
                    тлт_осложнения, дата_выписки, исход, рсц)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15, :16, :17)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->pers_id, 
                                        $this->lpu_id, 
                                        $this->направитель,
                                        $this->срок_госпитализации, 
                                        $this->приемный_покой, 
                                        $this->интенсивная_терапия, 
                                        $this->дата_поступления, 
                                        $this->диагноз_мкб10,
                                        $this->тлт_проведение,
                                        $this->тлт_срок,
                                        $this->тлт_препарат,
                                        $this->тлт_эффективность,
                                        $this->тлт_осложнения,
                                        $this->дата_выписки, 
                                        $this->исход,
                                        $this->рсц
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
}

?>