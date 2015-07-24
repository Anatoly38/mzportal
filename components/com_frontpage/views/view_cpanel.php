<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Frontpage
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE . DS .'components' . DS .'com_tasks' . DS . 'model' . DS . 'task_query.php' );
require_once ( MZPATH_BASE . DS .'components' . DS .'com_tasks' . DS . 'views' . DS . 'task_list.php' );

class ViewControlPanel 
{
    protected $img_path = 'includes/style/images/cpanel/';
    protected $cpanel;
    protected $cpanel_components = array(
            //6   => array( 'title' => 'Пользователи'             , 'icon' => 'icon-48-user.png' ),
            //4   => array( 'title' => 'Территории'               , 'icon' => 'icon-48-territory.png' ),
            //9   => array( 'title' => 'Паспорта ЛПУ'             , 'icon' => 'icon-48-hospital.png' ),
            58  => array( 'title' => 'Аттестация медработников' , 'icon' => 'icon-48-employee.png' ),
            54  => array( 'title' => 'Тестирование'             , 'icon' => 'icon-48-quiz.png' )
        );
    
    public function __construct()
    {
        $cpanel = $this->load_template();
        $components = $this->get_user_components();
        $this->set_content($components);
        $this->add_style();
    }
    
    public function load_template()
    {
        $this->cpanel = new DOMDocument();
        $this->cpanel->formatOutput = true;
        $tmpl = '<div class="cpanel" />';
        $this->cpanel->loadXML($tmpl);
    }

    protected function get_user_components() 
    {
        $obj = new TaskList();
        $obj->set_limit(0);
        $obj->get_items();
        $components = array();
        $i = 0;
        foreach($obj->items as $item) {
            if (array_key_exists($item->component_id, $this->cpanel_components)) {
                $components[$i] = $item->component_id;
            }
            $i++;
        }
        return $components;
    }
    
    public function set_content($components)
    {
        if (!$components) {
            Message::error('Нет доступных приложений');
            return false;
        }
        //print_r($components);
        foreach ($components as $c ) {
                $new_wrapper_div = $this->cpanel->createElement('div');
                $new_wrapper_div->setAttribute('class', 'icon-wrapper');
                $new_icon_div = $this->cpanel->createElement('div');
                $new_icon_div->setAttribute('class', 'icon');
                $new_link_el = $this->cpanel->createElement('a');
                $new_link_el->setAttribute('href', 'index.php?app=' . $c);
                $new_img_el = $this->cpanel->createElement('img');
                $new_img_el->setAttribute('src', $this->img_path . $this->cpanel_components[$c]['icon']);
                $new_img_el->setAttribute('border', '0');
                $new_span_el = $this->cpanel->createElement('span');
                $span_text_node = $this->cpanel->createTextNode($this->cpanel_components[$c]['title']);
                $new_span_el->appendChild($span_text_node);
                $new_link_el->appendChild($new_img_el);
                $new_link_el->appendChild($new_span_el);
                $new_icon_div->appendChild($new_link_el);
                $new_wrapper_div->appendChild($new_icon_div);
                $this->cpanel->firstChild->appendChild($new_wrapper_div);
        }
    }
    
    protected function add_style()
    {
        $css = CSS::getInstance();
        $css->add_style_link('cpanel.css');
    }
    
    public function render()
    {
        $out = $this->cpanel->getElementsByTagName('div');
        return $out->item(0);
    }
}
?>