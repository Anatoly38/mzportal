<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class DossierCabQuery extends ClActiveRecord 
{
    protected $source = ' attest_dossier_cab_user_view';
    public $uid;
    public $name;
    public $pwd;
    
    public function __construct($dossier = false)
    {
        if (!$dossier) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.uid, 
                        a.name, 
                        a.pwd
                    FROM {$this->source} AS a 
                    WHERE dossier_id = :1";
        $data = $dbh->prepare($query)->execute($dossier)->fetch_assoc();
        if(!$data) {
            throw new Exception("Пользователь не найден");
        }
        $this->uid  = $data['uid'];
        $this->name = $data['name'];
        $this->pwd  = $data['pwd'];
    }
}
?>