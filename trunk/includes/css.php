<?php
/**
* @version		$Id: css.php,v 1.0 2014/06/03 00:10:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class CSS
{
    private static $instance = false;
    private static $version  = 'v2';
    private $container  = null;
    private $calendar   = false;
    private $datepicker = false;
    private $treeview   = false;
    private $jquery_ui  = false;
    private $sheet      = false;
    private $grid      = false;

    private function __construct()
    {
        self::set_container();
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new CSS;
        }
        return self::$instance; 
    }

    private function set_container()
    {
        $this->container = new DOMdocument();
        $this->container->formatOutput = true;
        $root = $this->container->createElement('css');
        $this->container->appendChild($root);
    }
    
    public function add_style_link($css_file = null)
    {
        $link_node = false;
        if (!$this->container) {
            $this->set_container();
        }
        if (!$css_file) {
            return true;
        }
        $v = MZConfig::$js_version_contol ? '?' . self::$version : '';
        $text = 'includes/style/' . $css_file ;
        $link_node = $this->container->createElement('link');
        $link_node->setAttribute('rel', 'stylesheet');
        $link_node->setAttribute('type', 'text/css');
        $link_node->setAttribute('href', $text . $v);
        $this->container->firstChild->appendChild($link_node);
        return true;
    }
    
    public function add_style_text($text = null)
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$text) {
            return true;
        }
        $code_node = $this->container->createElement('style');
        $code_node->setAttribute('type', 'text/css');
        $tn = $this->container->createTextNode("\n//");
        $cs = $this->container->createCDATASection("\n" . $text . "\n//");
        $code_node->appendChild($tn);
        $code_node->appendChild($cs);
        $this->container->firstChild->appendChild($code_node);
        return true;
    }
    
    public function add_treeview()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if ($this->treeview) {
            return true;
        }
        $this->add_style_link('treeview.css');
        $this->treeview = true;
    }
    
    public function add_jquery_ui()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if ($this->jquery_ui) {
            return true;
        }
        $this->add_style_link('jquery-ui-1.8.12.custom.css');
        $this->jquery_ui = true;
    }       
    
    public function add_sheet()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if ($this->sheet) {
            return true;
        }
        $this->add_style_link('jquery.sheet.css');
        $this->add_style_link('jquery.colorPicker.css');
        $this->sheet = true;
    }

    public function add_grid()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if ($this->grid) {
            return true;
        }
        $this->add_style_link('grid.css');
        $this->grid = true;
    }
    
    public function get_css()
    {
        if (!$this->container) {
            $this->set_container();
        }
        $css_nodes = $this->container->getElementsByTagName('link');
        return $css_nodes;
    }
}

?>