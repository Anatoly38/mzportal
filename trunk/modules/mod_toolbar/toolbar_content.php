<?php
/**
* @version		$Id: toolbar_content.php,v 1.2 2014/06/02 19:56:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ('toolbar_button.php');

class Toolbar_Content
{
    private static $instance = false;
    private $container = null;
    private $buttons = Array();
   
    private function __construct()
    {
        $this->set_container();
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Toolbar_Content;
        }
        return self::$instance;
    }
    private function set_container()
    {
        $this->container = new DOMdocument();
        $this->container->formatOutput = true;
        $xml= '<div id="toolbar" class="toolbar"><table class="toolbar"><tbody><tr id="toolbar-container"></tr></tbody></table></div>';
        $this->container->loadXML($xml);
    }
    
    public function add_button($icon, $action, $title)
    {
        $b = new Toolbar_Button($icon, $action, $title);
        $this->buttons[$action] = $b;
        return $b;
    }
    
    public function get_button($id)
    {
        if (array_key_exists($id, $this->buttons)) {
            return $this->buttons[$id];
        } 
        else {
            return false;
        }
    }
    
    private function _appendFirst(DOMNode $newnode, DOMNode $ref)
    {
        $child = $ref->firstChild;
        if ($child) {
            return $child->parentNode->insertBefore($newnode, $child);
        } else {
            return $ref->appendChild($newnode);
        }
    } 
    
    public function get_toolbar()
    {
        if (!$this->container) {
            $this->set_container();
        }
        $js = Javascript::getInstance();
        foreach ($this->buttons as $b) {
            $js->add_toolbar_button($b->options);
        }
        $toolbar = $this->container->getElementsByTagName('table')->item(0);
        return $toolbar;
    }
}

?>