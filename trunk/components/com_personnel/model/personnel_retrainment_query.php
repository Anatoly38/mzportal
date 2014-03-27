<?php
/**
* @version		$Id: personnel_retrainment_query.php,v 1.0 2011/04/08 00:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2011 МИАЦ ИО
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

class PersonnelRetrainmentQuery extends ClActiveRecord
{
    protected $source = 'pers_retrainment';
    public $oid;
    public $учебное_заведение;
    public $год_прохождения;
    public $количество_часов;
    public $серия_документа;
    public $номер_документа;
    public $специальность;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.oid,
                        a.учебное_заведение,
                        a.год_прохождения,
                        a.количество_часов,
                        a.серия_документа,
                        a.номер_документа,
                        a.специальность
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid                  = $oid;
        $this->учебное_заведение    = $data['учебное_заведение'];
        $this->год_прохождения      = $data['год_прохождения'];
        $this->количество_часов     = $data['количество_часов'];
        $this->серия_документа      = $data['серия_документа'];
        $this->номер_документа      = $data['номер_документа'];
        $this->специальность        = $data['специальность'];
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
        $obj->name = $this->учебное_заведение;
        $obj->description = 'Переподготовка медработника';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (
                        oid, 
                        учебное_заведение,   
                        год_прохождения,     
                        количество_часов,    
                        серия_документа,     
                        номер_документа,     
                        специальность       
                    )
                    VALUES(:1, :2, :3, :4, :5, :6, :7)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute  (
                                            $this->oid,              
                                            $this->учебное_заведение,   
                                            $this->год_прохождения,     
                                            $this->количество_часов,    
                                            $this->серия_документа,     
                                            $this->номер_документа,     
                                            $this->специальность     
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
            throw new Exception("Для вызова update() необходим код документа сотрудника");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        учебное_заведение = :1,
                        год_прохождения   = :2,
                        количество_часов  = :3,
                        серия_документа   = :4,
                        номер_документа   = :5,
                        специальность     = :6
                    WHERE 
                        oid = :7";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->учебное_заведение,     
                                        $this->год_прохождения,       
                                        $this->количество_часов,   
                                        $this->серия_документа,    
                                        $this->номер_документа,    
                                        $this->специальность,    
                                        $this->oid
                                        );
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных об образовании сотрудника не сохранены!' . $e->getMessage());
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = $this->учебное_заведение;
            $obj->description = 'Переподготовка медработника';
            $obj->update();
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }
    
    public static function get_card($oid)
    {
        $link = Reference::get_id('образование', 'link_types');
        $data = LinkObjects::get_parents($oid, $link);
        if (is_array($data)) {
            return($data[0]);
        }
    }
}

?>