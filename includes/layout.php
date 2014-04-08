<?php 
/**
* @version      $Id: layout.php,v 1.2 2010/05/17 10:10:27 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Layout 
{
    private static $instance = false;
    private $layout; // Документ DOM в который загружеем дерево объектов для панели задач
    
    private function __construct() 
    {
        $tmpl_file = TEMPLATES . DS. MZConfig::$default_layout;
        $this->layout = new DOMdocument();
        $this->layout->formatOutput = true;
        if ($tmpl_file) {
            $this->layout->load($tmpl_file);
        }
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Layout();
        }
        return self::$instance;    
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