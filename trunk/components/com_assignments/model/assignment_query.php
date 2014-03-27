<?php
/**
* @version		$Id: assignment_query.php,v 1.0 2010/12/08 17:32:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Assignments
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

class AssignmentQuery extends ClActiveRecord implements ActiveRecord 
{
    protected $source = 'doc_assignments';
    public $oid;
    public $наименование;
    public $описание;
    public $содержание;
    public $дата_вынесения;
    public $руководитель;    
    public $выполнение;
    public $дата_выполнения;
    public $комментарий;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        наименование,
                        описание,
                        содержание,
                        дата_вынесения,
                        руководитель,
                        выполнение,
                        дата_выполнения, 
                        комментарий
                    FROM " . $this->source . "  
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не найдена");
        }
        $this->oid = $oid;
        $this->наименование = $data['наименование'];
        $this->описание = $data['описание'];
        $this->содержание = $data['содержание'];
        $this->дата_вынесения = $data['дата_вынесения'];
        $this->руководитель = $data['руководитель'];
        $this->выполнение = $data['выполнение'];
        $this->дата_выполнения = $data['дата_выполнения'];
        $this->комментарий = $data['комментарий'];
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
                        наименование = :1,
                        описание = :2, 
                        содержание = :3,
                        дата_вынесения = :4,
                        руководитель = :5,
                        выполнение = :6,
                        дата_выполнения = :7, 
                        комментарий = :8
                     WHERE 
                        oid = :9";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->наименование, 
                                        $this->описание, 
                                        $this->содержание,
                                        $this->дата_вынесения, 
                                        $this->руководитель,
                                        $this->выполнение,
                                        $this->дата_выполнения, 
                                        $this->комментарий,
                                        $this->oid                                        
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных поручения успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных не сохранены!');
            return false;
        }
        $obj = new MZObject($this->oid);
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
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
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO " . $this->source . " 
                    (oid, наименование, описание, содержание, дата_вынесения, руководитель, выполнение, дата_выполнения, комментарий)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->наименование, 
                                        $this->описание, 
                                        $this->содержание,
                                        $this->дата_вынесения, 
                                        $this->руководитель,
                                        $this->выполнение,
                                        $this->дата_выполнения, 
                                        $this->комментарий
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
}

?>