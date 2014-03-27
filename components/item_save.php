<?php
/**
* @version		$Id: item_save.php,v 1.1 2010/04/19 00:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Components
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

class ItemSave 
{
    protected $model;
    protected $query;
    protected $item;
    
    public function __construct($item = false, $model = null)
    {
        if (!$item) {
            $this->query = new $this->model();
        }
        else {
            $this->query = new $this->model($item);
        }
        $this->item = $item;
        $this->get_post_values();
    }
    
    public function get_post_values()
    {
    }

    public function update_data()
    {
        if (!$this->item) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Объект не определен (update)!');
            return false;
        }
        $this->query->update();
        return true;
    }
    
    public function insert_data()
    {
        $m = Message::getInstance();
        if ($this->item) {
            $m->enque_message('error', 'Добавление объекта невозможно, уже определен идентификатор!');
            return false;
        }
        $this->query->insert();
        return true;
    }
}
?>