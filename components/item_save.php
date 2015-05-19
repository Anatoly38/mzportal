<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Components
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class ItemSave 
{
    protected $model;
    protected $query = null;
    protected $item;
    protected $l_obj = null;
    protected $r_obj = null;
    
    public function __construct($item = null)
    {
        if ($this->model) {
            if (!$item) {
                $this->query = new $this->model();
            }
            else {
                $this->query = new $this->model($item);
            }
        }
        $this->item = $item;
        $this->get_post_values();
    }
    
    public function __set($name, $value)
    {
        if ($this->query) {
            $this->query->$name = $value;            
        }
    }
    
    public function set_model($model)
    {
        if ($model) {
            $this->model = $model;
            if (!$item) {
                $this->query = new $this->model();
            }
            else {
                $this->query = new $this->model($item);
            }
        }
    }
    
    public function get_item()
    {
        return $this->item;
    }
    
    public function get_post_values()
    {
    }
    
    public function save()
    {
        if (!$this->query) {
            throw new Exception("Объект ActiveRecord не существует (ItemSave)");
        }
        if (!$this->item) {
            $this->query->insert();
            $this->item = $this->query->oid;
        }
        else {
            $this->query->update();
        }
        return $this->query->oid;
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
        }
        if ($this->query->insert()) {
            $this->item = $this->query->oid;
            return true;
        }
    }
    
    public function set_left_obj($o)
    {
        $this->l_obj = $o;
        return $this->l_obj;
    }
    
    public function set_right_obj($o)
    {
        $this->r_obj = $o;
        return $this->r_obj;
    }
    
    public function set_association($link_type = null)
    {
        if (!$link_type) {
            throw new Exception("Не указан вид ассоциации");
        }
        if (!$this->l_obj || !$this->r_obj) {
            throw new Exception("Один или оба объекта для установки ассоциации не существуют");
        }
        try {
            LinkObjects::set_link($this->l_obj, $this->r_obj, $link_type);  
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами не сохранена!');
            return false;
        }
    }
}
?>