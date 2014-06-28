<?php
/** 
* @version		$Id: form_template_loader.php,v 1.1 2014/06/03 23:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Form Loader
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once('print_select.php');
require_once( MODULES . DS . 'mod_tree' . DS . 'tree.php');
require_once( MODULES . DS . 'mod_tree' . DS . 'tree_kladr.php');

class Form_Template_Loader 
{
    private $form_xml; // DOM XML объект
    private $tag_handler = array
        (
            'справочник'    => 'create_selects',
            'выбор_лпу'     => 'set_lpu',
        ); 
    
    public function __construct($template_file_url = false) {
        $this->form_xml = new DOMdocument();
        $this->form_xml->formatOutput = true;
        if ($template_file_url) {
            $this->form_xml->load($template_file_url);
        }
        $this->append_script_tags();
        $this->set_error_container();
        foreach ($this->tag_handler as $tag => $method) 
        {
            $search_tags = $this->form_xml->getElementsByTagName($tag); // 
            if ($search_tags->length > 0) {
                $this->$method($search_tags);
            }
        }
    }

    private function create_selects($tags) { // Создаем выпадающие списки
        if (!$tags) {
            return true;
        }
        for ($i = $tags->length; --$i >= 0;) {
            $ref_name = $tags->item($i)->getAttribute('имя');
            $select_name = $tags->item($i)->getAttribute('name');
            $validation = $tags->item($i)->getAttribute('проверка');
            $order      = $tags->item($i)->getAttribute('сортировка');
            if ($tags->item($i)->getAttribute('вид') == 'список') {
                $list_builder = new Print_Select();
                $new_node = $this->form_xml->importNode($list_builder->build_select($ref_name, $select_name, $validation, $order), true);
            } elseif ($tags->item($i)->getAttribute('вид') == 'дерево') {
                $acl = false;
                if ($tags->item($i)->getAttribute('check_acl') == 'required') {
                    $acl = true;
                }
                $tree = new Tree($ref_name, $select_name, $acl);
                $new_node = $this->form_xml->importNode($tree->get_page(), true);
            } elseif ($tags->item($i)->getAttribute('вид') == 'кладр') {
                $tree_kladr = new TreeKladr();
                $new_node = $this->form_xml->importNode($tree_kladr->get_page(), true);
            }
            $tags->item($i)->parentNode->replaceChild($new_node, $tags->item($i));
        }
        return true;
    }
    
    private function set_error_container()
    {
        $fragment = "<div id=\"form_error\" style=\"display: none;\"><div style=\"height: 18px;\" class=\"ui-state-error\"><span style=\"float:left; margin-right:0.5em;\" class=\"ui-icon ui-icon-alert\" ></span><span class=\"ui-state-error-text\" ></span></div></div>";
        $l = $this->form_xml->getElementsByTagName('legend')->item(0);
        $doc_frag = $this->form_xml->createDocumentFragment();
        $doc_frag->appendXML($fragment);
        $this->insert_node($doc_frag, $l, 'before');
        return true;
    }
    
    private function append_script_tags()
    {
        $css = CSS::getInstance();
        $css->add_style_link('mzportal.form.css');
        $js = Javascript::getInstance();
        $js->add_jquery();
        $js->add_jquery_validate();
        $js->add_datepicker();
        $js->add_dirtyforms();
        $q="//input[@default_date]"; // Обработка дат по умолчанию
        $xpath = new DOMXpath($this->form_xml);
        $dom_node_list = $xpath->query($q);
        for ($i = 0; $i < $dom_node_list->length; $i++) {
            $default_date = $dom_node_list->item($i)->getAttribute('default_date');
            $input_id = $dom_node_list->item($i)->getAttribute('id');
            if ($default_date) {
                $args = "\"option\", \"defaultDate\", \"$default_date\"" ;
                $selector = "#$input_id";
                $js->add_jquery_selector( $selector, 'datepicker', $args );
            }
        }
    }
  
    public function load_values($values = null) 
    {
        if (!$values || !is_array($values)) {
            return true;
        }
        //$js = Javascript::getInstance();
        foreach($values as $key => $value) {
            $q="//*[@name='$key'] | //*[@name='". $key . "[]']"; // 
            $xpath = new DOMXpath($this->form_xml);
            $dom_node_list = $xpath->query($q);
            if ($dom_node_list->length > 0) {
                $node = $dom_node_list->item(0);
                if ($node->nodeName == 'input') {
                    $input_type = $node->getAttribute('type');
                    if (!$input_type || $input_type == 'text' || $input_type == 'hidden') {
                        if ($value != "0000-00-00 00:00:00" &&  $value != "0000-00-00") {
                            $node->setAttribute('value', htmlspecialchars($value));
                        }
                    }
                    if ($input_type == 'radio') {
                        foreach ($dom_node_list as $radio) {
                            if ($radio->getAttribute('value') == $value) {
                                $radio->setAttribute('checked', '');
                            }
                        }
                    }
                    if ($input_type == 'checkbox') {
                        foreach ($dom_node_list as $checkbox) {
                            if ($checkbox->getAttribute('value') == $value) {
                                $checkbox->setAttribute('checked', '');
                            }
                        }
                    }
                }
                else if ($node->nodeName == 'textarea') {
                    $text_node = $this->form_xml->createTextNode(htmlspecialchars($value));
                    $node->appendChild($text_node);
                }
                else if ($node->nodeName == 'select') {
                    $options = $node->getElementsByTagName('option');
                    for ($i = 0; $i < $options->length; $i++) {
                        if ($options->item($i)->getAttribute('value') == $value) {
                            $options->item($i)->setAttribute("selected", "selected");
                        }
                    }
                }
                if ($node->hasAttribute('disabled')) {
                    $node->removeAttribute('disabled');
                }
            }
        }
    }
    
    private function insert_node($newNode, $refNode, $insertMode=null) {
       
        if(!$insertMode || $insertMode == "inside") {
           
            $refNode->appendChild($newNode);
           
        } else if($insertMode == "before") {
           
            $refNode->parentNode->insertBefore($newNode, $refNode);
           
        } else if($insertMode == "after") {
           
            if($refNode->nextSibling) {
                $refNode->parentNode->insertBefore($newNode, $refNode->nextSibling);
            } else {
                $refNode->parentNode->appendChild($newNode);
            }     
        }
    }
    
    public function render()
    {
        $c = Content::getInstance();
        $c->set_modal();
        $form_nodes = $this->form_xml->getElementsByTagName('form_pane')->item(0);
        return $form_nodes;
    }
}

?>