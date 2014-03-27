<?php
/**
* @version		$Id: territory_save.php,v 1.1 2009/12/03 00:50:30 shameev Exp $
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

class TerritorySave 
{
    protected $valid = false;
    protected $territory_query;
    protected $form = 'territory_form_tmpl';
    protected $item = null;
    
    public function __construct($item = false)
    {
        if (!$item) {
            $this->territory_query = new TerritoryQuery();
        }
        else {
            $this->territory_query = new TerritoryQuery($item);
        }
        $this->item = $item;
        $this->get_post_values();
    }
    
    public function get_post_values()
    {
        $this->territory_query->уровень = $_POST['уровень'];
        $this->territory_query->ОКАТО = $_POST['ОКАТО'];
        $this->territory_query->наименование = $_POST['наименование'];
        $this->territory_query->сокр_наименование = $_POST['сокр_наименование'];
        $this->territory_query->код_ОУЗ = $_POST['код_ОУЗ'];
    }

    public function update_data()
    {
        $m = Message::getInstance();
        if (!$this->item) {
            $m->enque_message('error', 'Территория не определена (update)!');
        }
        try {
            $this->territory_query->update();
            $m->enque_message('alert', 'Изменения при редактировании территории успешно сохранены');
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии территории не сохранены!');
            return false;
        }
    }
    
    public function insert_data()
    {
        $m = Message::getInstance();
        if ($this->item) {
            $m->enque_message('error', 'Добавление территории невозможно, уже определен идентификатор!');
        }
        try {
            $this->territory_query->insert();
            $m->enque_message('alert', 'Территория успешно добавлена');
        }
        catch (Exception $e) {
            $m->enque_message('error', $e . 'Ошибка: изменения при добавлении территории не сохранены!');
            return false;
        }
    }
}
?>