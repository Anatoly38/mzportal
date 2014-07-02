<?php
/**
* @version		$Id$
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

class PersonnelRetrainmentItem extends Item 
{
    protected $model    = 'PersonnelRetrainmentQuery';
    protected $form     = 'personnel_retrainment_tmpl';
    
    public function new_item()
    {
        $card = Request::getVar('personnel');
        if (!$card) {
            throw new Exception("Не определена карточка сотрудника для ввода данных");
        }
        $this->get_template();
        $this->set_parent($card[0]);
    }
    
    public function edit_item()
    {
        $req = Request::getVar('personnel');
        $card['personnel_id'] = $req[0];
        if (!$this->item) {
            throw new Exception("Код объекта не определен для редактирования");
        }
        $this->get_template();
        $this->set_values($card);
    }
    
    protected function set_parent($card)
    {
        $values['personnel_id'] = $card;
        $this->form_loader->load_values($values);
    }
    
    

}

?>