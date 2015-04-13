<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Frontpage
* @copyright    Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Tree
{
    protected $document;
    protected $items = array();
    protected $table = null;
    protected $check_acl;
    protected $user_id;
    protected $dbh;
    protected $only_leaf = true;

    public function __construct($table, $name, $check_acl = false)
    {
        $this->table = $table;
        $this->name = $name;
        $this->check_acl = $check_acl;
        $this->dbh = new DB_mzportal();
        $r = Registry::getInstance();
        $this->user_id = $r->user->user_id;
    }

    public function get_tree($parent = null)
    {
        if (!$parent) {
            $w = "WHERE s.родитель IS NULL";
        }
        else {
            $w = "WHERE s.родитель = :1";
        }
        $sql_add1 = null;
        $sql_add2 = null;
        if ($this->check_acl) {
            $sql_add1 = "JOIN `sys_objects` AS `o` ON `s`.`код` = `o`.`oid` AND `o`.`deleted` <> 1
                        JOIN `sys_acl` AS `a` ON `o`.`acl_id` = `a`.`acl_id`";
            $sql_add2 = " AND (a.uid = '{$this->user_id}' OR a.uid IN (SELECT ug.uid FROM sys_users_groups AS ug  WHERE ug.gid = a.acl_id))";
        }
        $query =    "SELECT DISTINCT
                        s.код,
                        s.наименование
                    FROM {$this->table} AS s $sql_add1 $w $sql_add2
                    ORDER BY s.наименование";
                    //print_r($query);
        $stmt = $this->dbh->prepare($query)->execute($parent)->fetchall_assoc();
        //print_r($stmt);
        $tag = "<ul>\n";
        foreach ($stmt as $s) {
            $c = $this->has_children($s['код']);
            $tag .= "   <li id=\"{$s['код']}\">\n";
            $tag .= "       <a href=\"#\">{$s['наименование']}</a>\n";
            if ($c) {
                $tag .= $this->get_tree($s['код']);
            }
            $tag .= "   </li>\n";
        }
        $tag .= "</ul>\n";
        return $tag;
    }

    private function has_children($id)
    {
        $query = "SELECT count(код) FROM {$this->table} WHERE родитель ='$id'";
        list($c) = $this->dbh->execute($query)->fetch_row();
        if ($c > 0) {
            return true;
        }
        return false;
    }
    
    private function js_tree($new_value_func = null)
    {
        $js = Javascript::getInstance();
        if ($this->only_leaf) {
            $prevent_folders = "if (!sel.hasClass('jstree-leaf')) { return false; }";
        }
        $code = 
<<<JS
$(function(){
    var initial = $("#{$this->name}").val();
    $("#btext_{$this->table}").text('Выберите значение');
    $("#selected_{$this->table}").click( function (event) {
        event.preventDefault();
        $("#container_{$this->table}").toggle();
    });
    $("#{$this->table}").jstree({ 
            "core" : { "animation" : 0},
            "plugins" : [ "html_data", "ui", "themeroller", "search"],
            "ui" : {
                "select_limit" : 1,
                "initially_select" : [ initial ]
            },
            "search" : {
                "case_insensitive" : true,
                "show_only_matches" : true
            }
        })
        .bind("loaded.jstree", function (event, data) { 
        })
        .bind("select_node.jstree", function (event, data) { 
            p = $(this).jstree("get_path", data.rslt.obj);
            path = p.join("; ")
            sel = data.rslt.obj;
            $prevent_folders
            $("#btext_{$this->table}").text(path);
            current_value = $("#{$this->name}").val();
            if (current_value != sel.attr("id")) {
                $("#{$this->name}").val(sel.attr("id")).addClass('dirty new').removeClass('old');
                $("#adminForm").addClass('dirty');
                {$new_value_func}
            }
            else {
                $("#{$this->name}").removeClass('new').addClass('old');
            }
            $("#container_{$this->table}").hide();
        });
    $("#search_{$this->table}").click( function (event) {
        event.preventDefault();
        $("#{$this->table}").jstree("search", $("#inp_{$this->table}").val());
    });
    $("#clear_{$this->table}").click( function (event) {
        event.preventDefault();
        $("#{$this->table}").jstree("clear_search");
        $("#inp_{$this->table}").val('');
    });
    $("#inp_{$this->table}").change(function() { 
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
        $html = $this->get_tree();
        $this->js_tree();
        $xml = new DOMdocument();
        $xml->formatOutput = true;
        $root_el = $xml->createElement('div');
        $control_el = $xml->createElement('div');
        $control_el->setAttribute('id', 'container_' . $this->table);
        $control_el->setAttribute('class', 'ui-corner-all ui-resizable');
        $control_el->setAttribute('style', 'display:none;min-width:400px');
        $tree_el = $xml->createElement('div');
        $tree_el->setAttribute('id', $this->table);
        $tree_el->setAttribute('class', 'ui-corner-all');
        $tree_el->setAttribute('style', 'margin-top:5px');
        $tree_el->setAttribute('resizable', 'true');
    
        $search_div_el = $xml->createElement('div');
        $search_inp_el = $xml->createElement('input');
        $search_inp_el->setAttribute('type', 'text');
        $search_inp_el->setAttribute('id', 'inp_' . $this->table);
        $search_inp_el->setAttribute('name', 'search');
        $search_inp_el->setAttribute('value', '');
        $search_inp_el->setAttribute('role', 'textbox'); 
        $search_inp_el->setAttribute('style', 'width:60%;z-index:100;position:relative;float:left;margin-top:5px;margin-right:5px;overflow:hidden');
        $search_inp_el->setAttribute('class', 'ignoredirty');
        $search_div_el->appendChild($search_inp_el);
        $search_but_el = $xml->createElement('button');
        $search_but_el->setAttribute('id', 'search_' . $this->table);
        $search_but_el->setAttribute('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $search_but_el->setAttribute('role', 'button');
        $search_but_el->setAttribute('aria-disabled', 'false');
        $search_but_el->setAttribute('style', 'height:17px;margin-top:5px;margin-right:5px');
        $search_but_text = $xml->createElement('span');
        $search_but_text->setAttribute('id', 'stext_' . $this->table);
        $search_but_text->setAttribute('class', 'ui-button-text');
        $search_but_text->setAttribute('style', 'line-height: 0.8');
        $st = $xml->createTextNode('Найти');
        $search_but_text->appendChild($st);
        $search_but_el->appendChild($search_but_text);
        $search_div_el->appendChild($search_but_el);
        
        $clear_but_el = $xml->createElement('button');
        $clear_but_el->setAttribute('id', 'clear_' . $this->table);
        $clear_but_el->setAttribute('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $clear_but_el->setAttribute('role', 'button');
        $clear_but_el->setAttribute('aria-disabled', 'false');
        $clear_but_el->setAttribute('style', 'height:17px;margin-top:5px');
        $clear_but_text = $xml->createElement('span');
        $clear_but_text->setAttribute('id', 'cl_text_' . $this->table);
        $clear_but_text->setAttribute('class', 'ui-button-text');
        $clear_but_text->setAttribute('style', 'line-height: 0.8');
        $clear_txt = $xml->createTextNode('Очистить');
        $clear_but_text->appendChild($clear_txt);
        $clear_but_el->appendChild($clear_but_text);
        $search_div_el->appendChild($clear_but_el);
        
        $sel_el = $xml->createElement('button');
        $sel_el->setAttribute('id', 'selected_' . $this->table);
        $sel_el->setAttribute('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $sel_el->setAttribute('role', 'button');
        $sel_el->setAttribute('aria-disabled', 'false');
        $button_text = $xml->createElement('span');
        $button_text->setAttribute('id', 'btext_' . $this->table);
        $button_text->setAttribute('class', 'ui-button-text');
        $sel_el->appendChild($button_text);
        $cont_el = $xml->createDocumentFragment();
        $cont_el->appendXML($html);
        $tree_el->appendChild($cont_el);
        $root_el->appendChild($sel_el);
        $root_el->appendChild($control_el);
        $control_el->appendChild($search_div_el);
        $control_el->appendChild($tree_el);
        $xml->appendChild($root_el);
        return $root_el;
    }
}
?>