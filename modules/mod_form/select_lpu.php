<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Form Loader
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

//Построение выпадающих списков из справочника

class SelectLpu
{
    private $dbh; // соединение с БД
    private $stmt; // рекордсет
    private $dom; // DOM объект с формой
    private $name;
    private $territories_book = null;
    private $lpu_book = null;

    public function __construct($name) 
    {
        $this->name = $name;
        $this->dbh  = new DB_mzportal();
        $this->dom  = new DOMdocument();
        $this->dom->formatOutput = true;
        $this->ensemble();
    }

    private function set_territory() 
    {
        $query = "SELECT oid, наименование FROM pasp_territory WHERE уровень >= '3' ORDER BY ОКАТО";
        $this->stmt = $this->dbh->execute($query);
        $result = new DB_Result($this->stmt);
        $territory = $this->dom->createElement('select');
        $territory->setAttribute('name', 'territory');
        $territory->setAttribute('id', 'territory');
        $territory->setAttribute('onchange', 'set_lpu_options(this)');
        $empty_option = $this->dom->createElement('option');
        $default_label = $this->dom->createTextNode('Выберите муниципальное образование');
        $empty_option->appendChild($default_label);
        $empty_option->setAttribute('value', '0');
        $territory->appendChild($empty_option);
        while ($result->next()) {
            $oid = $result->oid;
            $title = $result->наименование;
            $new_option = $this->dom->createElement('option');
            $value_attr = $new_option->setAttribute('value', $oid);
            $option_label = $this->dom->createTextNode($title);
            $new_option->appendChild($option_label);
            $territory->appendChild($new_option);
            $this->_js_tb($oid, $this->_get_lpu_from_mo($oid));
        }
        return $territory;
    }
    
    private function _get_lpu_from_mo($mo)
    {
        if (!$mo) {
            return;
        }
        $lpu_list = null;
        $query = "SELECT l.right FROM sys_obj_links AS l, pasp_lpu AS p
                    WHERE 
                        l.right = p.oid
                        AND p.обособленность = '1'
                        AND p.состояние = '1'
                        AND l.link_type = '4' 
                        AND l.left = '$mo'";
        $this->stmt = $this->dbh->execute($query);
        $r = new DB_Result($this->stmt);
        while ($r->next()) {
            $lpu_list .= $r->right .",";
        }
        $lpu_list = rtrim($lpu_list, ",");
        return $lpu_list;
    }
    
    private function _js_tb($territory, $lpu_list)
    {
        $this->territories_book .= "'$territory' : '$lpu_list', \n";
    }
    
    private function _js_lb($oid, $name)
    {
        $name = addslashes($name);
        $this->lpu_book .= "'$oid' : '$name', \n";
    }
        
    private function set_lpu() 
    {
        $lpu = $this->dom->createElement('select');
        $lpu->setAttribute('name', 'lpu_id');
        $lpu->setAttribute('id', 'lpu_id');
        $lpu->setAttribute('disabled', 'disabled');
        $empty_option = $this->dom->createElement('option');
        $empty_option->setAttribute('value', '111');
        $default_label = $this->dom->createTextNode('Выберите лечебное учреждение');
        $empty_option->appendChild($default_label);
        $lpu->appendChild($empty_option);
        
        $query = "SELECT oid, наименование FROM pasp_lpu 
                    WHERE 
                        обособленность = '1'
                        AND состояние = '1'
                    ORDER BY oid"; 
        $this->stmt = $this->dbh->execute($query);
        $r = new DB_Result($this->stmt);
        while ($r->next()) {
            $oid = $r->oid;
            $title = $r->наименование;
            $new_option = $this->dom->createElement('option');
            $value_attr = $new_option->setAttribute('value', $oid);
            $option_label = $this->dom->createTextNode($title);
            $new_option->appendChild($option_label);
            $lpu->appendChild($new_option);
            $this->_js_lb($r->oid, $title);
        }
        
        return $lpu;
    }

    private function set_js()
    {
        $script_text = "var Territories = { \n";
        $script_text .= rtrim($this->territories_book, ", \n");
        $script_text .= "\n}\n";
        $script_text .= "var Lpu = { \n";
        $script_text .= rtrim($this->lpu_book, ", \n");
        $script_text .= "\n}\n";        
        $script_text .= 
<<<JS
    function set_lpu_options(select_field) {
        var value=select_field.value;
        if (value in Territories) {
                list_options(Territories[value]);
        } 
    }

   function list_options(lpu) {
        var lpu_list = lpu.split(",");
        var select_field=document.getElementById('lpu_id');
        var loopIndex;
        if (lpu_list.length > 0) {
            select_field.options.length = 1;
            select_field.options[0] = new Option("Выберите лечебное учреждение", "-1");
            for (loopIndex = 0; loopIndex < lpu_list.length; loopIndex++ ) {
                new_option_value = lpu_list[loopIndex];
                new_option_text = Lpu[lpu_list[loopIndex]];
                select_field.options[loopIndex+1] = new Option(new_option_text, new_option_value);
            }
            select_field.disabled = false;
        } else {
            select_field.options[0] = new Option("Нет подчиненных учреждений");
            select_field.options[0].selected = true;
            select_field.disabled = true;
      }
    }
JS;
        $js = $this->dom->createElement('script');
        $js->setAttribute('type' , 'text/javascript');
        $cm = $this->dom->createTextNode("\n//");
        $ct = $this->dom->createCDATASection("\n" . $script_text . "\n//");
        $js->appendChild($cm);
        $js->appendChild($ct);
        return $js;
    }
    
    public function ensemble() 
    {
        $root = $this->dom->createElement('div');
        $root->setAttribute('id' , 'lpu_selection');
        $this->dom->appendChild($root);
        $j = $this->set_js();
        if ($j instanceof DOMElement) {
            $root->appendChild($j);
        }
        $div1 = $this->dom->createElement('div');
        $div1->setAttribute('id' , 'territory');
        $div1->setAttribute('style' , 'padding: 5px');
        $t = $this->set_territory();
        if ($t instanceof DOMElement) {
            $div1->appendChild($t);
        }
        $div2 = $this->dom->createElement('div');
        $div2->setAttribute('id' , 'lpu');
        $div2->setAttribute('style' , 'padding: 5px');
        $l = $this->set_lpu();
        if ($t instanceof DOMElement) {
            $div2->appendChild($l);
        }
        $root->appendChild($div1);
        $root->appendChild($div2);
        return $root;
    }
}
?>