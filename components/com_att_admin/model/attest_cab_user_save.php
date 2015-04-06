<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Attest
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class AttestCabUserSave extends ItemSave
{
    protected $model = 'AttestCabUserQuery';
    protected $dossier; 
    
    public function get_post_values()
    {
        $this->query->user  = Request::getVar('user');
        $this->query->pwd   = Request::getVar('pwd');
        $this->dossier = Request::getVar('dossier');
    }

    public function set_assoc()
    {
        $link_type = Reference::get_id('аттестационное_дело-пользователь', 'link_types');
        try {
            LinkObjects::set_link($this->dossier, $this->query->oid, $link_type);  
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами (DossierQuery, AttestCabUserQuery) не сохранена!');
            return false;
        }
    }
}
?>