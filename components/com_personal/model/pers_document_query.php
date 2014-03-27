<?php
/**
* @version		$Id: pers_document_query.php,v 1.0 2011/01/26 00:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Personal
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

class PersDocumentQuery extends ClActiveRecord implements ActiveRecord 
{
    protected $source = 'pers_document';
    public $oid;
    public $тип_документа;
    public $серия_документа;
    public $номер_документа;
    public $кем_выдан;
    public $дата_выдачи;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        тип_документа,
                        серия_документа,
                        номер_документа,
                        кем_выдан,
                        дата_выдачи 
                    FROM {$this->source} 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не найдена");
        }
        $this->oid = $oid;
        $this->фамилия = $data['тип_документа'];
        $this->имя = $data['серия_документа'];
        $this->отчество = $data['номер_документа'];
        $this->пол = $data['кем_выдан'];
        $this->дата_рождения = $data['дата_выдачи'];
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source}
                    SET
                        тип_документа = :1,
                        серия_документа = :2, 
                        номер_документа = :3,
                        кем_выдан =:4,
                        дата_выдачи = :5 
                     WHERE 
                        oid = :6";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->тип_документа, 
                                        $this->серия_документа, 
                                        $this->номер_документа, 
                                        $this->кем_выдан, 
                                        $this->дата_выдачи,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = $this->тип_документа;
            $obj->description = $this->серия_документа .' ' . $this->номер_документа;
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
        $obj->name = $this->тип_документа;
        $obj->description = $this->серия_документа .' ' . $this->номер_документа;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, тип_документа, серия_документа, номер_документа, кем_выдан, дата_выдачи)
                    VALUES(:1, :2, :3, :4, :5, :6)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->тип_документа, 
                                        $this->серия_документа, 
                                        $this->номер_документа, 
                                        $this->кем_выдан, 
                                        $this->дата_выдачи
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
}

?>