<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Components
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

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
            Message::error('Объект не определен (update)!');
            return false;
        }
        $this->query->update();
        return true;
    }
    
    public function insert_data()
    {
        if ($this->item) {
            Message::error('Добавление объекта невозможно, уже определен идентификатор!');
            return false;
        };
        if ($this->query->insert()) {
            return true;
        };
    }
}
?>