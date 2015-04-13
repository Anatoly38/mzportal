<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Form Loader
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

//Построение выпадающих списков из справочника

class Print_Select 
{
    private $dbh; // соединение с БД
    private $stmt; // рекордсет
    private $select_xml; // DOM объект с формой

    public function __construct() 
    {
        $this->dbh = new DB_mzportal();
        $this->select_xml = new DOMdocument();
        $this->select_xml->formatOutput = true;
    }

    public function build_select($spr, $name, $validation = false, $order) 
    {
        if (!$order) {
            $order = 'наименование';
        }
        $query = "SELECT * FROM {$spr} ORDER BY {$order}";
        try {
            $this->stmt = $this->dbh->execute($query);
            $result = new DB_Result($this->stmt);
            $select_root = $this->select_xml->createElement('select');
            $name_attr = $select_root->setAttribute('name', $name);
            $id_attr = $select_root->setAttribute('id', $name);
            if ($validation) {
                $select_root->setAttribute('class', $validation);
            }
            $this->select_xml->appendChild($select_root);
            $empty_option = $this->select_xml->createElement('option');
            $empty_value_attr = $empty_option->setAttribute('value', '');
            $this->select_xml->firstChild->appendChild($empty_option);
            while ($result->next()) {
                $id = $result->код;
                $title = $result->наименование;
                $new_option = $this->select_xml->createElement('option');
                $value_attr = $new_option->setAttribute('value', $id);
                $option_label = $this->select_xml->createTextNode($title);
                $this->select_xml->firstChild->appendChild($new_option);
                $this->select_xml->firstChild->lastChild->appendChild($option_label);
            }
            $select_node = $this->select_xml->getElementsByTagName('select')->item(0);
            return $select_node;
        }
        catch (Exception $e) {
            $node_root = $this->select_xml->createElement('font');
            $name_attr = $node_root->setAttribute('color', '#FF0000');
            $this->select_xml->appendChild($node_root);
            $error_msg = 'Ошибка загрузки требуемого справочника "' . $spr .'"';
            $error_text_node = $this->select_xml->createTextNode($error_msg);
            $this->select_xml->firstChild->appendChild($error_text_node);
            $error_node = $this->select_xml->getElementsByTagName('font')->item(0);
            return $error_node;
        }
    }  
}
?>