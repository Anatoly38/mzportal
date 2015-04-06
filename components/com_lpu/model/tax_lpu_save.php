<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class TaxLpuSave extends ItemSave
{
    protected $model = 'TaxQuery';
    
    public function get_post_values()
    {
        $this->query->инн   = Request::getVar('инн');
        $this->query->кпп   = Request::getVar('кпп');
        $this->lpu          = Request::getVar('lpu');
    }

    public function set_assoc()
    {
        $link_type = Reference::get_id('налоги', 'link_types');
        try {
            LinkObjects::set_link($this->lpu, $this->query->oid, $link_type, true); // Права наследуем от ЛПУ 
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами (LpuQuery, TaxQuery) не сохранена!');
            return false;
        }
    }
}
?>