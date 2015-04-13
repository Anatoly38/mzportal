<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

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
    
    public static function set($text) 
    {
        $t = Page_Title::getInstance();
        $t->set_title($text);
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