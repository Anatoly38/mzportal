<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'html'.DS.'select.php' );

class Constraint 
{
    private static $instance = false;
    private $filter = array();
    private $where = array();
    private $options = null;
    private $clear_filter = null;
    private $namespace = 'default';
    private $task;
    private $active = false;
    
    private function __construct()
    {
        $r = Registry::getInstance();
        $this->task = $r->current_task;
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Constraint;
        }
        return self::$instance;       
    }
    
    public function set_namespace($ns)
    {
        $this->namespace = $ns;
    }

    public function get_env($condition) 
    {
        $filter_key = 'фильтр_' . $condition;
        $value = Request::getVar($filter_key);
        if (isset($value)) {
            MZSession::set($filter_key, $value, $this->namespace);
            return trim($value);
        } 
        if (MZSession::has($filter_key, $this->namespace)) {
            $value = MZSession::get($filter_key, $this->namespace);
            return trim($value);
        }
        MZSession::clear($filter_key, $this->namespace);
        return null;
    }
    
    public function add_filter($condition, $source = null, $order = 'наименование', $field_name = 'наименование', $filter_name = null, $add_cond = null)
    {
        $this->filter[] = array($condition, $source, $order, $field_name, $filter_name, $add_cond);
    }
    
    public function get_filters()
    {
        $f = $this->filter;
        $c = count($this->filter);
        $dbh = new DB_mzportal();
        for ($i = 0; $i < $c; $i++) {
            if (!$f[$i][1]) {
                $this->options .= $this->get_constraint_input($f[$i][0]) . ' ';
            }
            else {
                $items = array();
                $field_name = $f[$i][3];
                $add_cond = $f[$i][5];
                $query = "SELECT DISTINCT * FROM {$f[$i][1]} WHERE 1=1 {$add_cond} ORDER BY {$f[$i][2]}";
                $stmt = $dbh->execute($query);
                if (!$stmt) {
                    return;
                }
                $result = new DB_Result($stmt);
                while ($result->next()) {
                    $result->код ? $items[$result->код] = $result->$field_name : $items[$result->oid] = $result->$field_name;
                }
                $this->options .= $this->get_constraint_select($items, $f[$i][0], $f[$i][4]) . ' ';
                $this->clear_filter .= "document.getElementById('фильтр_" . $f[$i][0] . "').value='-1';";      
            }
        }
    }
    
    private function get_constraint_select($items, $condition, $field_name = null)
    {
        $selected = $this->get_env($condition);
        $highlight = null;
        if ($selected && $selected !=-1) {
            $this->active = true;
            $highlight = 'ui-state-highlight';
        }
        $field_name ? $name = $field_name : $name = $condition;
        $options[] = HTMLSelect::option('-1', $name);
        foreach($items as $k => $v) {
            $options[] = HTMLSelect::option("$k", "$v");
        }
        $html = HTMLSelect::genericlist
            ( $options, 
                'фильтр_' . $condition, "class=\"inputbox {$highlight}\" size=\"1\" onchange=\"submitform('{$this->task}');\"", 'value', 'text', $selected
            );
        return $html;
    }
    
    private function get_constraint_input($condition)
    {
        $match = $this->get_env($condition);
        $highlight = null;
        if ($match) {
            $this->active = true;
            $highlight = 'ui-state-highlight';
        }
        $html =  "<input class=\"text_area {$highlight}\" type=\"text\" id=\"фильтр_{$condition}\" name=\"фильтр_{$condition}\"" ;
        $html .= ' value="' . $match . '" onchange="submitform(\''. $this->task .'\');" title = "фильтровать по полю ' . $condition . '"/>';
        $this->clear_filter .= "document.getElementById('фильтр_" . $condition . "').value='';";
        return $html;
    }
    
    public function get_where()
    {
        $f = $this->filter;
        $c = count($this->filter);
        $where = array();
        for ($i = 0; $i < $c; $i++) {
            $v = $this->get_env($f[$i][0]);
            if (isset($v) && $v !== '-1') {
                if (!$f[$i][1]) {
                    if (!empty($v)) {
                        $where[] = " AND s.{$f[$i][0]} LIKE '%{$v}%'" ;
                    }
                }
                else {
                    $where[] = " AND s.{$f[$i][0]} = '{$v}'" ;
                }
            }
        }
        if (count($where) > 0) {
            return(implode($where));
        }
        return null;
    }
    
    private function set_js()
    {
        $js = Javascript::getInstance();
        $code = 
<<<JS
$(function(){
    $("#filter_sw").click( function (event) {
        $("#filter_cn").toggle();
        $("#filter_sw_icon").toggleClass("ui-icon-triangle-1-e");
        $("#filter_sw_icon").toggleClass("ui-icon-triangle-1-s");
    });
});
JS;
        $js->add_jblock($code);
    }
    
    public function get_constraints()
    {
        $show = 'none';
        if ($this->active) {
            $show = 'block';
        }
        $this->set_js();
        $filter  =  "<fieldset class=\"ui-corner-all\" style=\"margin:2px;\">";
        $filter .= "<legend id=\"filter_sw\" style=\"cursor: pointer;\"><span id=\"filter_sw_icon\" class=\"ui-icon ui-icon-triangle-1-e\" style=\"float: left;\"></span>Фильтр</legend>";
        $filter .= "<div id=\"filter_cn\" style=\"min-height: 20px; display:{$show}\">";
        $filter .= "<div style=\"max-width:60%;margin-right:190px; float:left\">{$this->options}</div>";
        $filter .= "<div style=\"width:190px;float:right;\"><button onclick=\"submitform('{$this->task}');\">Применить</button>";
        $filter .= "<button onclick=\"{$this->clear_filter}submitform('{$this->task}');\">Сбросить</button></div></div></fieldset>";
        return $filter;
    }

}

?>