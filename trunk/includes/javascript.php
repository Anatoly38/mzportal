<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Javascript
{
    private static $instance = false;
    private static $version  = 'v64';
    private $container  = null;
    private $jquery     = false;
    private $jquery_validate = false;
    private $datepicker = false;
    private $dirtyforms = false;
    private $treeview   = false;
    private $jstree     = false;
    private $grid       = false;
    private $toolbar    = false;
    private $quiz     = false;
    private $jquery_block = array();

    private function __construct()
    {
        self::set_container();
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Javascript;
        }
        return self::$instance; 
    }

    private function set_container()
    {
        $this->container = new DOMdocument();
        $this->container->formatOutput = true;
        $root = $this->container->createElement('скрипты');
        $this->container->appendChild($root);
    }
    
    public function add_js_link($file_name = null)
    {
        $link_node = false;
        if (!$this->container) {
            $this->set_container();
        }
        if (!$file_name) {
            return true;
        }
        $v = MZConfig::$js_version_contol ? '?' . self::$version : '';
        $link_node = $this->container->createElement('script');
        $link_node->setAttribute('type', 'text/javascript');
        $link_node->setAttribute('src', 'includes/javascript/' . $file_name . $v );
        $this->container->firstChild->appendChild($link_node);
        return true;
    }
    
    public function add_js_text($code = null)
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$code) {
            return true;
        }
        $code_node = $this->container->createElement('script');
        $code_node->setAttribute('type', 'text/javascript');
        $tn = $this->container->createTextNode("\n//");
        $cs = $this->container->createCDATASection("\n" . $code . "\n//");
        $code_node->appendChild($tn);
        $code_node->appendChild($cs);
        $this->container->firstChild->appendChild($code_node);
        return true;
    }
    
    public function add_jquery()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if ($this->jquery) {
            return true;
        }
        //$this->add_js_link('jquery.min.js');
        $this->add_js_link('jquery-2.1.1.min.js');
        $this->add_js_link('jquery-ui.min.js');
        $this->add_js_link('jquery.smartmenus.js');
        $this->add_js_link('jquery-migrate-1.2.1.js');
        $this->jquery = true;
    }
    
    public function add_jblock($code)
    {
        array_push($this->jquery_block, $code); 
    }
    
    public function add_jquery_selector( $selector, $method, $args )
    {
        $code = "$(\"$selector\").$method($args);";
        $this->add_jblock($code);
    }

    public function insert_jblock()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if (count($this->jquery_block) == 0) {
            return true;
        }
        $block = "$(function(){" . implode("\n", $this->jquery_block) . "});"; 
        $this->add_js_text($block);
    }
    
    function add_toolbar_button($options)
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if (count($options) > 0) {
            $js_obj = self::to_js_obj($options);
        }
        if (!$this->toolbar) {
            $this->add_js_link('jquery.toolbar.js');
            $css = CSS::getInstance();
            $css->add_style_link('toolbar-styles.css');
            $this->toolbar = true;
        }
        $code = '$("#toolbar-container").toolbar(' . $js_obj . ');';
        $this->add_jblock($code);
    }
    
    public function add_jquery_validate()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if ($this->jquery_validate) {
            return true;
        }
        $this->add_js_link('jquery.validate.js');
        $this->add_js_link('jquery.validate.messages_ru.js');
        $this->add_js_link('jquery.validate.additional.methods.js');
        $code = 
<<<JS
$("#adminForm").validate({
    onsubmit: false,
    invalidHandler: function(e, validator) {
        var errors = validator.numberOfInvalids();
        if (errors) {
            var message = errors == 1
            ? 'Вы неверно заполнили 1 поле. Оно выделено ниже'
            : 'Вы неверно заполнили ' + errors + ' поля. Они выделены ниже';
            $("div.ui-state-error span").html(message);
            $("#form_error").show();
        } else {
            $("#form_error").hide();
        }
    }
});
JS;
        $this->add_jblock($code);
        $this->jquery_validate = true;
    }

     public function add_datepicker()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        $code =
<<<JS
$("input.popup_date").datepicker({ showButtonPanel: true, changeYear: true, changeMonth: true });
JS;
        $this->add_jblock($code);
        $this->datepicker = true;
    } 

    public function add_treeview()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if ($this->treeview) {
            return true;
        }
        $this->add_js_link('jquery.treeview.js');
        $css = CSS::getInstance();
        $css->add_treeview();
        $code = '$("#root").treeview( {animated: "fast", persist: "location", collapsed: true, unique: false} );';
        $this->add_jblock($code);
        $this->treeview = true;
    }

    public function add_jstree()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if ($this->jstree) {
            return true;
        }
        $this->add_js_link('jquery.jstree.js');
        $this->jstree = true;
    }

    public function add_dirtyforms()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if ($this->dirtyforms) {
            return true;
        }
        $this->add_js_link('jquery.dirtyforms.js');
        $code = '$("#adminForm").dirtyForms();';
        $this->add_jblock($code);
        $this->dirtyforms = true;
    }
    
    public function add_grid()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if ($this->grid) {
            return true;
        }
        $css = CSS::getInstance();
        $css->add_grid();
        $this->add_js_link('jquery.statgrid.js');
        $this->add_js_link('jquery.scrollTo.js');
        $this->add_js_link('jquery.numeric.js');
        $code = "$('#grid').css('height', function() {
            t = $('#grid').offset().top;
            f = $('#footer').height();
            h = $(window).height() - t - f - 80;
            return h;
        });
        $(window).resize(function() {
            $('#grid').css('height', function() {
                t = $('#grid').offset().top;
                h = $(window).height() - t - 60;
                return h;
            });
        });";
        $code .= "$('#grid').statgrid();$('.numeric').numeric();";
        $this->add_jblock($code);
        $this->grid = true;
    }
    
    public function add_quiz()
    {
        if (!$this->container) {
            $this->set_container();
        }
        if (!$this->jquery) {
            $this->add_jquery();
        }
        if ($this->quiz) {
            return true;
        }
        $this->add_js_link('jquery.quiz.js');
        $this->add_js_link('jquery.timeTo.js');
        $this->quiz = true;
    }

    public function get_js_scripts()
    {
        if (!$this->container) {
            $this->set_container();
        }
        $js_nodes = $this->container->getElementsByTagName('script');
        return $js_nodes;
    }
    
    private static function to_json($a=false)
    {
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a)) {
            if (is_float($a)) {
              $a = str_replace(",", ".", strval($a));
            }
            static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
            array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
            return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
            if (key($a) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList) {
            foreach ($a as $v) $result[] = self::to_json($v);
            return '[ ' . join(', ', $result) . ' ]';
        }
        else {
            foreach ($a as $k => $v) $result[] = self::to_json($k).': '.self::to_json($v);
            return '{ ' . join(', ', $result) . ' }';
        }
    }
    
    private static function to_js_obj($a=false, $quotes = true, $function = false)
    {
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a)) {
            if (is_float($a)) {
              $a = str_replace(",", ".", strval($a));
            }
            static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
            array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
            if ($function) {
                return 'function () {' . $a . '}' ;
            } 
            else if ($quotes) {
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            }
            else {
                return $a;
            }
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
            if (key($a) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList) {
            foreach ($a as $v) $result[] = self::to_js_obj($v);
            return '[ ' . join(', ', $result) . ' ]';
        }
        else {
            foreach ($a as $k => $v) {
                $function = strstr($v, '$') ? true : false; 
                $result[] = self::to_js_obj($k, false).': '.self::to_js_obj($v, is_int($v) ? false : true, $function);
            }
            return '{ ' . join(', ', $result) . ' }';
        }
    }
}

?>