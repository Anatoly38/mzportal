<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Message
{
    private static $instance = false;
    private $type;
    private $document = false; // DOM объект с узлами сообщений
    private $root;
    private $alert_node; 
    private $error_node; 

    private function __construct() {
        if (!$this->document) {
            $this->message_doc_create();
        }
    }

    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Message;
        }
        return self::$instance;
    }
    
    public static function error($text)
    {
        $m = self::getInstance();
        $m->enque_message('error', $text);
        return;
    }

    public static function alert($text)
    {
        $m = self::getInstance();
        $m->enque_message('alert', $text);
        return;
    }

    public function enque_message($type = null, $message) 
    {
        switch ($type) {
            case null :
            case 'alert' :
                $this->set_alert_massage($message);
                break;
            case 'error' :
                $this->set_error_massage($message);
                break;       
        }
    }

    private function message_doc_create()
    {   
        $this->document = new DOMdocument();
        $this->document->formatOutput = true;
        $this->root = $this->document->createElement('div');
        $this->root->setAttribute('class', 'ui-widget');
        $this->document->appendChild($this->root);
    }

    private function set_alert_massage($message)
    {
        if (!$message) {
            return true;
        }
        if (!$this->alert_node) {
            $this->alert_node = $this->document->createElement('div');
            $this->alert_node->setAttribute('class', 'ui-state-highlight ui-corner-all');
            $this->alert_node->setAttribute('id', 'message');
            $this->root->appendChild($this->alert_node);
        }
        $p = $this->document->createElement('p');
        $icon = $this->document->createElement('span');
        $icon->setAttribute('class', 'ui-icon ui-icon-info');
        $icon->setAttribute('style', 'float: left; margin-right: .3em;');
        $p->appendChild($icon);
        $this->alert_node->appendChild($p);
        $text = $this->document->createTextNode('<strong>'.$message . '</strong><br />');
        $p->appendChild($text);
    }

    private function set_error_massage($message)
    {
        if (!$message) {
            return true;
        }
        if (!$this->error_node) {
            $this->error_node = $this->document->createElement('div');
            $this->error_node->setAttribute('class', 'ui-state-error ui-corner-all');
            $this->error_node->setAttribute('style', 'padding: .7em .7em .7em .7em;');
            $this->root->appendChild($this->error_node);
            $p = $this->document->createElement('p');
            $icon = $this->document->createElement('span');
            $icon->setAttribute('class', 'ui-icon ui-icon-alert');
            $icon->setAttribute('style', 'float: left; margin-right: .3em;');
            $p->appendChild($icon);
            $this->error_node->appendChild($p);

        }
        $text = $this->document->createTextNode('<strong>'.$message . '</strong><br />');
        $this->error_node->appendChild($text);
    }

    public function get_message()
    {
        if (!$this->document) {
            $this->message_doc_create();
        }
        $message_nodes = $this->document->getElementsByTagName('div')->item(0);
        return $message_nodes;
    }

}