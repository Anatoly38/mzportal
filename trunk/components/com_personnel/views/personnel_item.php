<?php
/**
* @version		$Id: personnel_item.php,v 1.0 2011/01/27 09:02:30 shameev Exp $
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

class PersonnelItem extends Item 
{
    protected $model    = 'AdapterPersonnelQuery';
    protected $form     = 'personnel_form_tmpl';
    
    public function new_item()
    {
        $this->get_template();
    }

    protected function set_new($oid)
    {
        $values['lpu_id'] = $oid;
        $this->form_loader->load_values($values);
    }
    
    public function get_name()
    {
        $title = null;
        if (isset($this->query->фамилия)) {
            $title = "{$this->query->фамилия} {$this->query->имя} {$this->query->отчество}";
        }
        return $title;
    }
    
    public function get_pers_id()
    {
        if (isset($this->query->pers_id)) {
            return $this->query->pers_id;
        }
        return null;
    }
    
    public function get_lpu_id()
    {
        if (isset($this->query->lpu_id)) {
            return $this->query->lpu_id;
        }
        return null;
    }

}

?>