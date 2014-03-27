<?php
/**
* @version		$Id: class_query.php,v 1.1 2010/06/22 18:26:30 shameev Exp $
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

class ClassQuery extends ClActiveRecord
{
    protected $source = 'sys_classes';
    public $cid;
    public $name; 
    public $description;
    public $type; 
    public $path;
    public $file_name;
    
    public function __construct($cid = false)
    {
        if (!$cid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT
                        cid,
                        name, 
                        description,
                        type,
                        path,
                        file_name
                    FROM {$this->source}
                    WHERE cid = :1";
        $data = $dbh->prepare($query)->execute($cid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Класс не существует или является классом");
        }
        $this->cid = $cid;
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->type = $data['type'];
        $this->path = $data['path'];    
        $this->file_name = $data['file_name']; 
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
                        name, 
                        description,
                        type,
                        path,
                        file_name
                     WHERE 
                        cid = :10"; 
        $dbh->prepare($query)->execute( 
            $this->name,
            $this->description,
            $this->type,
            $this->path,
            $this->file_name,
            $this->cid
        );
    }
}
?>