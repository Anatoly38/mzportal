<?php
/**
* @version		$Id: personnel_record_item.php,v 1.0 2011/02/15 09:02:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class PersonnelRecordItem extends Item 
{
    protected $model    = 'PersonnelRecordQuery';
    protected $form     = 'personnel_record_tmpl';
    
    public function new_item()
    {
        $this->get_template();
    }
    
    public function edit_item()
    {
        if (!$this->item) {
            throw new Exception("Код объекта не определен для редактирования");
        }
        $this->get_template();
        $this->set_values();
    }
}

?>