<?php
/**
* @version		$Id: task_tree.php,v 1.0 2010/07/06 16:45:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Frontpage
* @copyright	Copyright (C) 2010 МИАЦ ИО


Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE . DS .'components' . DS .'com_tasks' . DS . 'model' . DS . 'task_query.php' );

class TaskTree
{
    protected $document;
    protected $items = array();
    protected $source = 'tasks_view';
    protected $link = true;
    protected $js = false;
    protected $check_boxes = false;
    protected $acl_restrict = true;

    public function __construct($tmpl)
    {
        if (!$tmpl) {
            throw new Exception("Шаблон не определен");
        }
        $this->load_template($tmpl);
    }
    
    public function load_template($tmpl)
    {
        $this->document = new DOMDocument();
        $this->document->formatOutput = true;
        $this->document->load($tmpl);    
    }

    public function get_applications($user_id = false)
    {
        $acl_restriction = " AND (s.uid = '$user_id' OR s.gid IN (SELECT ug.gid FROM sys_users_groups AS ug  WHERE ug.uid = '$user_id')) ";
        if (!$this->acl_restrict) {
            $acl_restriction = '';
        }
        $dbh = new DB_mzportal();
        $query =    "SELECT DISTINCT 
                        s.oid,
                        s.наименование, 
                        s.описание,
                        s.component_id,
                        s.parent_id
                    FROM {$this->source} AS s 
                    WHERE 1=1 $acl_restriction
                    ORDER BY  s.component_id, s.наименование";
                    //print_r($query);
        $stmt = $dbh->execute($query);
        $obj_array = new DB_Result($stmt);
        while ($obj_array->next())
        {
            $id = $obj_array->oid;
            $p_id = $obj_array->parent_id;
            $n = $obj_array->наименование;
            $d = $obj_array->описание;
            if (!$obj_array->u && !$obj_array->g) {
                $checked = false; 
            } 
            else {
                $checked = true;
            }
            $c_id = $obj_array->component_id;
            $this->load_node($id, $p_id, $n, $d, $c_id, $checked);
        }
    }
    
    public function set_tree($top = null, $user_id = null)
    {
        $acl_restriction = " AND (a.uid = '$user_id' OR a.uid IN (SELECT ug.uid FROM sys_users_groups AS ug  WHERE ug.gid = a.acl_id)) ";
        $dbh = new DB_mzportal();
        $query =    "SELECT DISTINCT 
                        s.oid,
                        s.наименование, 
                        s.описание,
                        s.component_id
                    FROM ({$this->source} AS s 
                        JOIN sys_objects AS o ON s.oid = o.oid  
                        JOIN sys_acl AS a ON o.acl_id = a.acl_id)
                    WHERE s.parent_id = '$top'
                    $acl_restriction
                    ORDER BY  s.component_id, s.наименование";
                    //print_r($query);
        $stmt = $dbh->execute($query);
        $obj_array = new DB_Result($stmt);
        if (!$obj_array) {
            return true;
        }
        while ($obj_array->next())
        {
            $id = $obj_array->oid;
            $n = $obj_array->наименование;
            $d = $obj_array->описание;
            if (!$obj_array->u && !$obj_array->g) {
                $checked = false; 
            } 
            else {
                $checked = true;
            }
            $c_id = $obj_array->component_id;
            $this->load_node_($id, $top, $n, $d, $c_id, $checked);
            $this->set_tree($id, $user_id);
        }
    }

    public function load_node_($id, $p_id, $n, $d ,$c_id, $checked = false)
    {
        if (!$this->document) {
            throw new Exception("Документ DOM не загружен");
        }
        if (!$p_id ) {
            $p_id = 'root';
        }
        $curent_node = $this->find_node_by_id($id);
        if ($curent_node) {
            if ($checked) {
                $curent_node->firstChild->setAttribute('checked', 'checked');
                return true;
            } 
            else {
                return true;            
            }
        } 
        $container = $this->find_node_by_id($p_id);
        //print_r($container->getAttribute('id'));
        if (!$container) {
            return true;
        }
        $new_el_li = $this->document->createElement('li');
        $new_el_li->setAttribute('id', $id);
        $new_el_span = $this->document->createElement('span');
        $new_el_li->appendChild($new_el_span);
        $new_text = $this->document->createTextNode($n);
        if ($c_id != 0 && $this->link) {
            $new_el_a = $this->document->createElement('a');
            $new_el_a->setAttribute('href', 'index.php?app=' . $c_id);
            $new_el_a->appendChild($new_text);
            $new_el_span->appendChild($new_el_a);
            $new_el_span->setAttribute('class', 'file');
        }
        else {
            $new_el_span->appendChild($new_text);
            $new_el_span->setAttribute('class', 'folder');
        } 
        $el_ul = $container->getElementsByTagName('ul');
        if ($el_ul->length > 0 ) {
            $el_ul->item(0)->appendChild($new_el_li);
        } else {
            $new_el_ul = $this->document->createElement('ul');
            $new_el_ul->appendChild($new_el_li);
            $container->appendChild($new_el_ul); 
        }
        return true;
    }

    public function load_node($id, $p_id, $n, $d ,$c_id, $checked = false)
    {
        if (!$this->document) {
            throw new Exception("XML шаблон не загружен");
        }
        if (!$p_id ) {
            $p_id = 'root';
        }
        $curent_node = $this->find_node_by_id($id);
        if ($curent_node) {
            if ($checked) {
                $curent_node->firstChild->setAttribute('checked', 'checked');
                return true;
            } 
            else {
                return true;            
            }
        } 
        $container = $this->find_node_by_id($p_id);
        //print_r($container->getAttribute('id'));
        if (!$container) {
            return true;
        }
        $new_el_li = $this->document->createElement('li');
        $new_el_li->setAttribute('id', $id);
        $new_el_inp = $this->document->createElement('input');
        $new_el_inp->setAttribute('type', 'hidden');
        $new_el_inp->setAttribute('name', 'task_id[]');
        $new_el_inp->setAttribute('value', $id);
        $new_el_inp->setAttribute('onChange', 'selectChilds(this)');
        if ($checked) {
            $new_el_inp->setAttribute('checked', 'checked');
        }
        $new_el_li->appendChild($new_el_inp);
        $new_text = $this->document->createTextNode($n);
        if ($c_id != 0 && $this->link) {
            $new_el_a = $this->document->createElement('a');
            $new_el_a->setAttribute('href', 'index.php?app=' . $c_id);
            $new_el_a->appendChild($new_text);
            $new_el_li->appendChild($new_el_a);
        }
        else {
            $new_el_li->appendChild($new_text);
        } 
        $el_ul = $container->getElementsByTagName('ul');
        if ($el_ul->length > 0 ) {
            $el_ul->item(0)->appendChild($new_el_li);
        } else {
            $new_el_ul = $this->document->createElement('ul');
            $new_el_ul->appendChild($new_el_li);
            $container->appendChild($new_el_ul); 
        }
/*         if ($container->nextSibling instanceof DOMElement) {
            if ($container->nextSibling->tagName == 'ul') {
                $container->nextSibling->appendChild($new_el_li);
                return true;
            }
        }
        $new_el_ul = $this->document->createElement('ul');
        if ($p_id = 'root') {
            $new_el_ul->setAttribute('class', 'treeview');
        }
        $new_el_ul->appendChild($new_el_li);
        $container->parentNode->appendChild($new_el_ul); */
        return true;
    }
    
    private function find_node_by_id($id)
    {
        $q = "//*[@id='". $id ."']";
        $xpath = new DOMXpath($this->document);
        $found_node = $xpath->query($q)->item(0);
        if (!$found_node) {
            return false;
        }
        return $found_node;
    }
    
    public function set_id_input($id = false)
    {
        if (!$this->document) {
            throw new Exception("XML шаблон не загружен");
        }
        if (!$id) {
            throw new Exception("Пользователь/Группа не определена");
        }
        $id_input_el = $this->document->createElement('input');
        $id_input_el->setAttribute('type', 'hidden');
        $id_input_el->setAttribute('name', 'oid[]');
        $id_input_el->setAttribute('value', $id);
        $this->document->firstChild->appendChild($id_input_el);
    }
    
    public function set_js($js)
    {
        $this->js = $js;
    }
    
    public function add_scripts()
    {
        $js = Javascript::getInstance();
        $js->add_treeview();
    }
    
        
    private function _js()
    {
        $script_text = "\n";
        $script_text .= 
<<<JS
    function selectChilds(cb) 
    {
        if (cb.checked) {
            set = true;
        } 
        else {
            set = false;
            unselectParents(cb);
        }
        next = cb.parentNode.nextSibling;
        if (next.nodeName == 'UL') {
            childs = next.getElementsByTagName('input');
            for(var i=0; i < childs.length; i++) {
                childs[i].checked = set;
            }
        }
    }
    
    function unselectParents(cb)
    {
        parent_cb = cb.parentNode.parentNode.previousSibling;
        if (parent_cb.nodeName == 'LI') {
            parent_cb.firstChild.checked = false;
            unselectParents(parent_cb.firstChild);
        }
    }

JS;
        $script_node = $this->document->createElement('script');
        $script_node->setAttribute('type' , 'text/javascript');
        $cm = $this->document->createTextNode("\n//");
        $ct = $this->document->createCDATASection("\n" . $script_text . "\n//");
        $script_node->appendChild($cm);
        $script_node->appendChild($ct);
        $this->document->firstChild->appendChild($script_node);
    }

    public function set_links($l)
    {
        $this->link = $l;
    }

    public function set_check_boxes($c)
    {
        $this->check_boxes = $c;
    }
    
    public function set_restriction($r)
    {
        $this->acl_restrict = $r;
    }
    
/*     
    private function _check_boxes()
    {
        $li_tags = $this->document->getElementsByTagName('li');
        foreach($li_tags as $li) {
            $id = $li->getAttribute('id');
            $cb = $this->document->createElement('input');
            $cb->setAttribute('type', 'checkbox');
            $cb->setAttribute('name', 'task_id[]');
            $cb->setAttribute('value', $id);
            $cb->setAttribute('onChange', 'selectChilds(this)');
            $li->insertBefore($cb, $li->firstChild);
        }
    }
 */
    private function _check_boxes()
    {
        $input_tags = $this->document->getElementsByTagName('input');
        foreach($input_tags as $el) {
            if ($el->getAttribute('name') != 'oid[]') {
                $el->setAttribute('type', 'checkbox');
            }
        }
    }
    
    public function get_page()
    {
        if ($this->js) {
            $this->_js();
        }
        if ($this->check_boxes) {        
            $this->_check_boxes();
        }
        $page = $this->document->getElementsByTagName('frontpage')->item(0);
        return $page;
    }
}
?>