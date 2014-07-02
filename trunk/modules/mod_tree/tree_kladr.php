<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Frontpage
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

Прямой доступ запрещен
*/
//defined( '_MZEXEC' ) or die( 'Restricted access' );

class TreeKladr
{
    protected $document;
    protected $items = array();
    protected $table = null;
    protected $item = null;
    protected $dbh;
    protected $code;

    public function __construct( $table = 'dic_kladr' , $current_code = null )
    {
        $this->table = $table;
        $this->dbh = new DB_add();
        $this->code = $current_code;
    }
    
    public function search_tree($search_string) 
    {
        if (!$search_string) {
            return false;
        }
        $path = array();
        $query = "SELECT current_id FROM {$this->table} WHERE наименование LIKE '%$search_string%' AND CHAR_LENGTH(`current_id`) < 12";
        //$stmt = $this->dbh->prepare($query)->execute($parent)->fetchall_assoc();
        list($id) = $this->dbh->prepare($query)->execute()->fetch_row();
        if (!$id) {
            return null;
        } 
        else {
            $path_query = "SELECT parent_id FROM {$this->table} WHERE current_id = :1";
            $p = $this->dbh->prepare($path_query);
            $top = false;
            while (!$top) {
                list($match) = $p->execute($id)->fetch_row();
                if (!$match) {
                   $id = null; 
                   $top = true;
                } else {
                    $id = $match;
                    $path[] = $match;
                }
            }
        }
        $path = array_reverse($path);
        $result = "[";
        foreach ($path as $lev) { 
            $result .= "\"#$lev\",";
        }
        $result = substr($result, 0, -1);
        $result .= "]";
        return $result;
    }

    public function get_tree($parent = null)
    {
        if (!$parent) {
            $w = "s.parent_id IS NULL";
        }
        else {
            $w = "s.parent_id = :1";
        }
        $query =    "SELECT 
                        s.current_id,
                        s.наименование,
                        s.сокращение,
                        s.код_кладр
                    FROM {$this->table} AS s WHERE CHAR_LENGTH(`current_id`) < 12 AND $w 
                    ORDER BY s.наименование";
                    //print_r($query);
        $stmt = $this->dbh->prepare($query)->execute($parent)->fetchall_assoc();
        if (!$stmt) {
            return false;
        }
        $tag = "<ul>\n";
        foreach ($stmt as $s) {
            $class = "jstree-leaf";
            $c = $this->has_children($s['current_id']);
            if ($c) {
                $class = "jstree-closed";
            } 
            $tag .= "   <li class=\"$class\" id=\"{$s['current_id']}\" name=\"{$s['код_кладр']}\">\n";
            $tag .= "       <a href=\"#\">{$s['сокращение']} {$s['наименование']}</a>\n";
            $tag .= "   </li>\n";
        }
        $tag .= "</ul>\n";
        return $tag;
    }
    
    private function has_children($id)
    {
        if (!$id) {
            return false;
        }
        $query = "SELECT count(current_id) FROM {$this->table} WHERE CHAR_LENGTH(`current_id`) < 12 AND parent_id = '$id'";
        list($c) = $this->dbh->execute($query)->fetch_row();
        if ($c > 0) {
            return true;
        }
        return false;
    }

    private function js_tree()
    {
        $js = Javascript::getInstance();
        
        $code = 
<<<JS
$(function () {
    $("#btext_kladr").text('Выбор адреса из классификатора');
    $("#selected_kladr").click( function (event) {
        event.preventDefault();
        $("#container_kladr").toggle();
    });
    $("#kladr").jstree({ 
            "plugins" : [ "html_data", "ui", "themeroller", "search"],
            "ui" : {
                "select_limit" : 1,
            },
            "html_data" : {
                "ajax" : {
                    "url" : "./modules/mod_tree/kladr_request.php",
                    "data" : function (n) {
                        return { id : n.attr ? n.attr("id") : 0 };
                    }
                }
            },
            "search" : {
                    "case_insensitive" : true,
                    "ajax" : {
                        "url" : "./modules/mod_tree/kladr_request.php"
                    }
            }
        })
        .bind("select_node.jstree", function (event, data) { 
            p = $(this).jstree("get_path", data.rslt.obj);
            path = p.join("; ")
            sel = data.rslt.obj;
            $("#btext_kladr").text(path);
            $("#код_кладр").val(sel.attr("name")).addClass('dirty');
            $("#adminForm").addClass('dirty');
            $("#container_kladr").hide();
        })
        .delegate("a", "click", function (event, data) { event.preventDefault(); });

    $("#search_kladr").click( function (event) {
        event.preventDefault();
        $("#kladr").jstree("search", $("#inp_kladr").val());
    });
    $("#clear_kladr").click( function (event) {
        event.preventDefault();
        $("#kladr").jstree("clear_search");
        $("#inp_kladr").val('');
    });
    $("#inp_kladr").change(function() { 
        if ($(this).hasClass('dirty')) {
            $(this).removeClass('dirty');
        }
    });
});
JS;
        $js->add_jstree();
        $js->add_jblock($code);
    }

    public function get_page()
    {
        if (!$this->table) {
            return false;
        }
        $this->js_tree();
        $xml = new DOMdocument();
        $xml->formatOutput = true;
        $root_el = $xml->createElement('div');
        $control_el = $xml->createElement('div');
        $control_el->setAttribute('id', 'container_kladr');
        $control_el->setAttribute('class', 'ui-corner-all ui-resizable');
        $control_el->setAttribute('style', 'display:none;min-width:400px');
        $tree_el = $xml->createElement('div');
        $tree_el->setAttribute('id', 'kladr');
        $tree_el->setAttribute('class', 'ui-corner-all');
        $tree_el->setAttribute('style', 'margin-top:5px');
        $tree_el->setAttribute('resizable', 'true');
    
        $search_div_el = $xml->createElement('div');
        $search_inp_el = $xml->createElement('input');
        $search_inp_el->setAttribute('type', 'text');
        $search_inp_el->setAttribute('id', 'inp_kladr');
        $search_inp_el->setAttribute('name', 'search');
        $search_inp_el->setAttribute('value', '');
        $search_inp_el->setAttribute('role', 'textbox'); 
        $search_inp_el->setAttribute('style', 'width:60%;z-index:100;position:relative;float:left;margin-top:5px;margin-right:5px;overflow:hidden');
        $search_inp_el->setAttribute('class', 'ignoredirty');
        $search_div_el->appendChild($search_inp_el);
        $search_but_el = $xml->createElement('button');
        $search_but_el->setAttribute('id', 'search_kladr');
        $search_but_el->setAttribute('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $search_but_el->setAttribute('role', 'button');
        $search_but_el->setAttribute('aria-disabled', 'false');
        $search_but_el->setAttribute('style', 'height:17px;margin-top:5px;margin-right:5px');
        $search_but_text = $xml->createElement('span');
        $search_but_text->setAttribute('id', 'stext_kladr');
        $search_but_text->setAttribute('class', 'ui-button-text');
        $search_but_text->setAttribute('style', 'line-height: 0.8');
        $st = $xml->createTextNode('Найти');
        $search_but_text->appendChild($st);
        $search_but_el->appendChild($search_but_text);
        $search_div_el->appendChild($search_but_el);
        
        $clear_but_el = $xml->createElement('button');
        $clear_but_el->setAttribute('id', 'clear_kladr');
        $clear_but_el->setAttribute('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $clear_but_el->setAttribute('role', 'button');
        $clear_but_el->setAttribute('aria-disabled', 'false');
        $clear_but_el->setAttribute('style', 'height:17px;margin-top:5px');
        $clear_but_text = $xml->createElement('span');
        $clear_but_text->setAttribute('id', 'cl_text_kladr');
        $clear_but_text->setAttribute('class', 'ui-button-text');
        $clear_but_text->setAttribute('style', 'line-height: 0.8');
        $clear_txt = $xml->createTextNode('Очистить');
        $clear_but_text->appendChild($clear_txt);
        $clear_but_el->appendChild($clear_but_text);
        $search_div_el->appendChild($clear_but_el);
        
        $sel_el = $xml->createElement('button');
        $sel_el->setAttribute('id', 'selected_kladr');
        $sel_el->setAttribute('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $sel_el->setAttribute('role', 'button');
        $sel_el->setAttribute('aria-disabled', 'false');
        $button_text = $xml->createElement('span');
        $button_text->setAttribute('id', 'btext_kladr');
        $button_text->setAttribute('class', 'ui-button-text');
        $sel_el->appendChild($button_text);
        $root_el->appendChild($sel_el);
        $root_el->appendChild($control_el);
        $control_el->appendChild($search_div_el);
        $control_el->appendChild($tree_el);
        
        $xml->appendChild($root_el);
        return $root_el;
    }
}
?>