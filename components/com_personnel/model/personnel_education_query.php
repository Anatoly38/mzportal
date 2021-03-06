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

class PersonnelEducationQuery extends ClActiveRecord
{
    protected $source = 'pers_education';
    public $oid;
    public $учебное_заведение;
    public $год_окончания;
    public $серия_диплома;
    public $номер_диплома;
    public $специальность;
    public $тип_образования;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.oid,
                        a.учебное_заведение,
                        a.год_окончания,
                        a.серия_диплома,
                        a.номер_диплома,
                        a.специальность,
                        a.тип_образования
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid = $oid;
        $this->учебное_заведение    = $data['учебное_заведение'];
        $this->год_окончания        = $data['год_окончания'];
        $this->серия_диплома        = $data['серия_диплома'];
        $this->номер_диплома        = $data['номер_диплома'];
        $this->специальность        = $data['специальность'];
        $this->тип_образования      = $data['тип_образования'];
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
        $obj->description = 'Специальность сотрудника УЗ по диплому';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, учебное_заведение, год_окончания,серия_диплома, номер_диплома, специальность, тип_образования)
                    VALUES(:1, :2, :3, :4, :5, :6, :7)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute  (
                                            $this->oid,
                                            $this->учебное_заведение, 
                                            $this->год_окончания, 
                                            $this->серия_диплома, 
                                            $this->номер_диплома,
                                            $this->специальность,
                                            $this->тип_образования
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
                        год_окончания = :2,
                        серия_диплома = :3, 
                        номер_диплома = :4,
                        специальность = :5,
                        тип_образования = :6
                     WHERE 
                        oid = :7";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->учебное_заведение, 
                                        $this->год_окончания, 
                                        $this->серия_диплома, 
                                        $this->номер_диплома,
                                        $this->специальность,
                                        $this->тип_образования,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных об образовании сотрудника успешно сохранены');
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