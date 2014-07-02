<?php
/**
* @version		$Id$
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

class PersonnelPostEducationQuery extends ClActiveRecord
{
    protected $source = 'pers_posteducation';
    public $oid;
    public $базовая_организация;
    public $тип_образования;
    public $начало_прохождения;
    public $окончание_прохождения;
    public $дата_получ_документа;
    public $ученая_степень;
    public $серия_диплома;
    public $номер_диплома;
    public $специальность;

    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.oid,
                        a.базовая_организация,
                        a.тип_образования,
                        a.начало_прохождения,
                        a.окончание_прохождения,
                        a.дата_получ_документа,
                        a.ученая_степень,
                        a.серия_диплома,
                        a.номер_диплома,
                        a.специальность
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid                  = $oid;
        $this->базовая_организация  = $data['базовая_организация'];
        $this->тип_образования      = $data['тип_образования'];
        $this->начало_прохождения   = $data['начало_прохождения'];
        $this->окончание_прохождения= $data['окончание_прохождения'];
        $this->дата_получ_документа = $data['дата_получ_документа'];
        $this->ученая_степень       = $data['ученая_степень'];
        $this->серия_диплома        = $data['серия_диплома'];
        $this->номер_диплома        = $data['номер_диплома'];
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
        $obj->name = $this->специальность;
        $obj->description = 'Специальность сотрудника при последипломном образовании';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, базовая_организация, тип_образования,начало_прохождения,окончание_прохождения, дата_получ_документа,ученая_степень,серия_диплома,номер_диплома,специальность)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute  (
                                            $this->oid,                  
                                            $this->базовая_организация,  
                                            $this->тип_образования,      
                                            $this->начало_прохождения,   
                                            $this->окончание_прохождения,
                                            $this->дата_получ_документа, 
                                            $this->ученая_степень,       
                                            $this->серия_диплома,        
                                            $this->номер_диплома,        
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
                        базовая_организация  = :1,
                        тип_образования      = :2,
                        начало_прохождения   = :3, 
                        окончание_прохождения = :4,
                        дата_получ_документа = :5,
                        ученая_степень       = :6,
                        серия_диплома        = :7,
                        номер_диплома        = :8,
                        специальность        = :9
                    WHERE 
                        oid = :10";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->базовая_организация,  
                                        $this->тип_образования,      
                                        $this->начало_прохождения,   
                                        $this->окончание_прохождения,
                                        $this->дата_получ_документа, 
                                        $this->ученая_степень,       
                                        $this->серия_диплома,        
                                        $this->номер_диплома,        
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
            $obj->name = $this->специальность;
            $obj->description = 'Специальность сотрудника УЗ по диплому';
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