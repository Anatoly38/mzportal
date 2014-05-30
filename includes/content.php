<?php
/**
* @version      $Id: content.php,v 1.0 2014/05/28 19:33:40 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Framework
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО
* @license      GNU/GPL, see LICENSE.php

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Content
{
    private static $instance = false;
    private static $mode = false;    
    private $content;
    private $document;
    private $root;
    private $block = array();
    
    private function __construct()
    {
        $this->set_container();
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Content;
        }
        return self::$instance;    
    }
    
    private function set_container()
    {
        $this->document = new DOMDocument();
        $this->document->formatOutput = true;
        $content_element = $this->document->createElement('div');
        $content_element->setAttribute('class', 'ui-widget-content');
        $this->document->appendChild($content_element);
        $this->root = $this->document->createElement('form');
        $this->root->setAttribute('method', 'post');
        $this->root->setAttribute('action', 'index.php');
        $this->root->setAttribute('name', 'adminForm');
        $this->root->setAttribute('enctype', 'multipart/form-data');
        $content_element->appendChild($this->root);
        $this->set_default_routes();
    }
    
    private function set_default_routes()
    {
        $this->set_route_element('app');
        $this->set_route_element('task');
    }
    
    public function set_route_element($name, $value = null)
    {
        $route_element = $this->document->createElement('input');
        $route_element->setAttribute('type', 'hidden');
        $route_element->setAttribute('name', $name);
        $route_element->setAttribute('id', $name);
        $route_element->setAttribute('value', $value);
        $this->root->appendChild($route_element);
    }
    
    public static function set_route($name, $value = null)
    {
        if (!$name) {
            throw new Exception("Не определено имя элемента (route)");
        }
        $c = Content::getInstance();
        $c->set_route_element($name, $value);
        return true;
    }
    
    public function set_dialog_form($content = null, $title = null, $id = 'dialog-form')
    {
        if (!$content) {
            return;
        }
        $dialog = $this->document->createElement('div');
        $dialog->setAttribute('id', $id);
        $dialog->setAttribute('title', "$title");
        $this->root->appendChild($dialog);
        $fragment = $this->document->createDocumentFragment();
        $fragment->appendXML($content);
        $dialog->appendChild($fragment);
    }

    public function add_content($content = null)
    {
        if (!$content) {
            throw new Exception("Содержимое документа не определено");
        }
        $this->block[] = $content;
    }
    
    public function set_modal()
    {
        self::$mode = 'modal';
    }
    
    public function get_mode() 
    {
        return self::$mode;
    }
    
    public function get_document_node()
    {
        foreach ($this->block as $b) {
            if ($b instanceof DOMElement) {
                $new_content = $this->document->importNode($b, true);
                $this->root->appendChild($new_content);
            }
            elseif ($b instanceof DOMDocument) {
                $root = $b->documentElement;
                $new_content = $this->document->importNode($root, true);
                $this->root->appendChild($new_content);
            }
            else {
                $fragment = $this->document->createDocumentFragment();
                $fragment->appendXML($b);
                $this->root->appendChild($fragment);
            }
        }
        $content_nodes = $this->document->getElementsByTagName('div')->item(0);
        return $content_nodes;
    }
}

?>