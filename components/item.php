<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Framework
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'modules'.DS.'mod_form'.DS.'form_template_loader.php' );

class Item 
{
    protected $model;
    protected $form;
    protected $query;
    protected $item = null;
    protected $form_loader;
    
    public function __construct($item = false)
    {
        if (!$item) {
            $this->query = new $this->model();
        }
        else {
            $this->query = new $this->model($item);
            $this->item = $item;
        }
    }
    
    public function get_name()
    {
        $title = null;
        if (isset($this->query->наименование)) {
            $title = $this->query->наименование;
        }
        return $title;
    }
    
    public function edit_item()
    {
        if (!$this->item) {
            throw new Exception("Код объекта не определен для редактирования");
        }
        $this->get_template();
        $this->set_values();
    }
    
    public function new_item()
    {
        $this->get_template();
    }
    
    protected function get_template()
    {
        $f = $this->form;
        $template = MZCONFIG::$$f;
        $full_path = TMPL.DS.$template;
        $this->form_loader = new Form_Template_Loader($full_path);
    }
    
    protected function set_values($add = null)
    {
        $values = $this->query->get_as_array();
        if ($add != null) {
            if (is_array($add)) {
               $values = array_merge($values, $add); 
            }
        }
        //print_r($values);
        $this->form_loader->load_values($values);
    }
    
    public function get_form()
    {
        $form_content = $this->form_loader->render();
        return $form_content;
    }
}

?>