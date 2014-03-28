<?php
/**
* @version		$Id: toolbar_content.php,v 1.2 2011/05/18 19:56:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
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