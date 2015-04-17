<?php 
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Layout 
{
    private static $instance = false;
    private $layout; // Документ DOM в который загружаем дерево объектов для панели задач
    
    private function __construct($tmpl_file = null) 
    {
        $this->layout = new DOMdocument();
        $this->layout->formatOutput = true;
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Layout();
        }
        return self::$instance;    
    }
    
    public function load($tmpl_file = null)
    {
        if (!$tmpl_file) {
            return false;
        }
        $this->layout->load($tmpl_file);
        return $this->layout;
    }
    
    public function get_layout()
    {
        return $this->layout;
    }
    
    public function set_visible($layer, $visible = 'block')
    {
        $node = null;
        $xpath = new DOMXPath($this->layout);
        $node = $xpath->query("//*[@id='$layer']")->item(0);
        if (!$node) {
            return true;
        }
        $attr_value = 'display:' . $visible;
        $node->setAttribute('style', $attr_value);
        return true;
    }
    
    public function is_visible()
    {
        $node = null;
        $xpath = new DOMXPath($this->layout);
        $node = $xpath->query("//*[@id='sidebar']")->item(0);
        if (!$node) {
            return true;
        }
        $style = $node->getAttribute('style');
        $found = strpos($style, 'display:none');
        if ($found === false) {
            return true;
        } 
        else {
            return false;
        }
    }
    
    public function add_nodes($nodes, $element = null)
    {
        if (!$nodes instanceof DOMElement) {
            throw new Exeption("Добавляемый фрагмент не является документом/объектом DOM");
        }
        $new_nodes = $this->layout->importNodes($nodes);
        // Если не определен DOM элемент для добавления, добавляем к корневому элементу
        if (!$element) {
            $place_node = $this->layout->firstChild();
        }
        else {
            //$xpath = new DOMXPath($this->layout);
            //$place_node = $xpath->query("//$element")->item(0);
            $place_node = $this->layout->getElementsByTagName($element)->item(0);
            if (!$place_node) {
                throw new Exeption("Не найден элемент для добавления дочерних узлов");
            }
        }
        $place_node->addChild($new_nodes);
    }
}

?>