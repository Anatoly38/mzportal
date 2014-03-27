<?php
/**
* @version		$Id: tax_lpu_save.php,v 1.0 2010/07/31 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class TaxLpuSave extends ItemSave
{
    protected $model = 'TaxQuery';
    
    public function get_post_values()
    {
        $this->query->инн = Request::getVar('инн');
        $this->query->кпп = Request::getVar('кпп');
        $this->lpu      = Request::getVar('lpu');
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