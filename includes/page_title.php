<?php
/**
* @version		$Id: page_title.php,v 1.4 2009/09/22 00:10:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
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

class Page_Title
{
    private static $instance = false;
    private $container = null;
    public static $title = null;
    private $root = null;
    
    private function __construct()
    {
        self::set_container();
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Page_Title;
        }
        return self::$instance;    
    }
    
    private function set_container()
    {
        $this->container = new DOMdocument();
        $this->container->formatOutput = true;
        $this->root = $this->container->createElement('h2');
        $this->container->appendChild($this->root);
    }
    
    public function set_title($text = null)
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$text) {
            $text =  'МИАЦ ИО';
        }
        if (!self::$title) {
            self::$title = $text;
            $text_node = $this->container->createTextNode($text);
            $this->root->appendChild($text_node);
        }
    }
    
    public function get_title()
    {
        if (!$this->container) {
            $this->set_container();
        }
        $title_node = $this->container->getElementsByTagName('h2')->item(0);
        return $title_node;
    }
}

?>