<?php
/**
* @version		$Id: object_query.php,v 1.1 2010/06/22 18:26:30 shameev Exp $
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

class ObjectQuery extends ClActiveRecord
{
    protected $source = 'sys_objects';
    public $oid;
    public $classID; 
    public $name;
    public $description;
    public $deleted; 
    public $created;
    public $changed;
    public $updates;
    public $crc32;
    public $acl_id;
    public $owner;
    
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT
                        classID,
                        name, 
                        description,
                        deleted,
                        created,
                        changed,
                        updates,
                        crc32,
                        acl_id,
                        owner
                    FROM {$this->source}
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Объект не существует или является классом");
        }
        $this->oid = $oid;
        $this->classID = $data['classID'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->deleted = $data['deleted'];
        $this->created = $data['created'];    
        $this->changed = $data['changed']; 
        $this->updates = $data['updates']; 
        $this->crc32 = $data['crc32']; 
        $this->acl_id = $data['acl_id']; 
        $this->owner = $data['owner']; 
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
                        name = :1, 
                        description = :2,
                        deleted = :3,
                        created = :4,
                        changed = :5,
                        updates = :6,
                        crc32 = :7,
                        acl_id = :8,
                        owner = :9
                     WHERE 
                        oid = :10"; 
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
            $this->name,
            $this->description,
            $this->deleted,
            $this->created,
            $this->changed,
            $this->updates,
            $this->crc32,
            $this->acl_id,
            $this->owner,
            $this->oid
            );
            $m->enque_message('alert', 'Изменения при редактировании объекта успешно сохранены');
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии объекта не сохранены (sys_objects)!');
            return false;
        }
    }
}
?>