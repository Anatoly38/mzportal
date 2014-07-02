<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Document Patterns
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class PatientOnmk extends Item 
{
    protected $model = 'AdapterOnmkQuery';
    protected $form = 'patient_onmk_form';
    
    public function get_name()
    {
        $title = null;
        if (isset($this->query->фамилия)) {
            $title = $this->query->фамилия . ' ' . $this->query->имя . ' ' . $this->query->отчество; 
        }
        return $title;
    }
    
    public function new_item()
    {
        $this->get_template();
        $defaults = array();
        $defaults['приемный_покой'] = '0';
        $defaults['интенсивная_терапия'] = '1';
        $this->form_loader->load_values($defaults);
    }

}

?>