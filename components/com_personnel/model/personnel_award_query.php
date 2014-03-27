<?php
/**
* @version		$Id: personnel_award_query.php,v 1.0 2011/07/05 00:05:30 shameev Exp $
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

class PersonnelAwardQuery extends ClActiveRecord
{
    protected $source = 'pers_award';
    public $oid;
    public $номер_награды;
    public $наименование;
    public $дата_получения;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.oid,
                        a.номер_награды,
                        a.наименование,
                        a.дата_получения
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid = $oid;
        $this->номер_награды    = $data['номер_награды'];
        $this->наименование     = $data['наименование'];
        $this->дата_получения   = $data['дата_получения'];
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
        $obj->description = 'Награда//поощрение сотрудника УЗ';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, номер_награды, наименование, дата_получения)
                    VALUES(:1, :2, :3, :4)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute  (
                                            $this->oid,
                                            $this->номер_награды, 
                                            $this->наименование, 
                                            $this->дата_получения
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
                        номер_награды   = :1,
                        наименование    = :2, 
                        дата_получения  = :3
                     WHERE 
                        oid = :4";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                            $this->номер_награды, 
                                            $this->наименование, 
                                            $this->дата_получения,
                                            $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных документа сотрудника успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных документа сотрудника не сохранены!' . $e->getMessage());
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = $this->наименование;
            $obj->description = 'Награда//поощрение сотрудника УЗ';
            $obj->update();
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }
    
    public static function get_card($oid)
    {
        $link = Reference::get_id('награда', 'link_types');
        $data = LinkObjects::get_parents($oid, $link);
        if (is_array($data)) {
            return($data[0]);
        }
    }
}

?>