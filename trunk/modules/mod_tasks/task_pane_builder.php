<?php 
/**
* @version		$Id: task_pane_builder.php,v 1.1 2009/12/16 00:10:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class TaskPaneBuilder 
{
    private static $instance = false;
    private $task_pane; // Документ DOM в который загружеем дерево объектов для панели задач
    private $user; // Текущий пользователь
    private $active_node = null; //Выбранный узел
    private $open_path = array(); // Массив содержащий путь к активному узлу
    private $tp_link_type; // тип иерархии для объектов панели задач
    
    private function __construct()
    {
        $this->task_pane = new DOMDocument();
        $this->task_pane->formatOutput = true;
        $tmpl_file = TEMPLATES . DS. MZConfig::$task_pane_tmpl;
        $this->task_pane->load($tmpl_file);
    }
    
    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new TaskPaneBuilder();
        }
        return self::$instance;    
    }
    
    public function set_default_tree($active_node = false, $user_id = null)
    {
        $this->user = $user_id;    
        $this->tp_link_type = MZConfig::$tp_link_type; // Код связи между объектами, которые отображаются в панели задач
        if ($active_node) {
            $this->active_node = $active_node;
            $this->set_open_path($active_node);
        }
        $this->set_branches(); // Все объекты в иерархии, у которых имеются дочерние узлы
        $this->set_leaves(); // "Листья" в иерархии, не имеющие дочерних объектов
    }
    
    private function set_branches()
    {
        $dbh = new DB_mzportal();
        $nodes_query = 
        "SELECT o.oid AS id, l.left AS parent_id, o.description 
            FROM sys_objects o, sys_obj_links l 
            WHERE o.oid = l.right and l.link_type ='" . $this->tp_link_type . "' AND o.deleted = '0'
            AND l.right NOT IN 
            (SELECT t1.right FROM sys_obj_links AS t1 LEFT JOIN sys_obj_links as t2 ON t1.right = t2.left WHERE t2.right IS NULL)
            ORDER BY l.left" ;
        $stmt = $dbh->execute($nodes_query);
        $obj_array = new DB_Result($stmt);
        while ($obj_array->next()) 
        {
            $id = $obj_array->id;
            $p_id = $obj_array->parent_id;
            $d = $obj_array->description;
            $this->load_node($id, $p_id, $d, false);
        }
    }
    
    private function set_leaves()
    {
        if (!$this->user) {
            throw new Exception("Текущий пользователь не определен!");
        }
        $dbh = new DB_mzportal();
        $leaf_query = 
        "SELECT o.oid as id, l.left AS parent_id, o.description 
            FROM sys_objects o, sys_obj_links l 
            WHERE o.oid = l.right and l.link_type = '" . $this->tp_link_type . "'
            AND l.right in 
                (SELECT t1.right FROM sys_obj_links AS t1 LEFT JOIN sys_obj_links as t2 ON t1.right = t2.left WHERE t2.right IS NULL)
            AND o.oid IN
            (
            SELECT 
                obj.oid
            FROM 
                sys_objects AS obj 
            LEFT JOIN 
                (sys_acl AS acl, sys_users AS u)
            ON 
                (obj.oid = acl.oid AND acl.uid = u.uid)
            WHERE 
                u.uid = '" . $this->user . "'
            OR 
                obj.owner = '" . $this->user . "'
            OR
                u.uid IN 
                (SELECT ug.gid FROM sys_users_groups AS ug WHERE ug.uid = '" . $this->user . "')
            )
            ORDER BY l.left";
        $stmt = $dbh->execute($leaf_query);
        $leaf_array = new DB_Result($stmt);
        while ($leaf_array->next()) 
        {
            $id = $leaf_array->id;
            $p_id = $leaf_array->parent_id;
            $d = $leaf_array->description;
            $route = "app=$id";
            $this->load_node($id, $p_id, $d, $route);
        }
    }
    
    private function set_open_path($node) 
    {
        $this->open_path[] = $node;
        $dbh = new DB_mzportal();
        $query="SELECT l.left FROM sys_obj_links l WHERE l.right = :1 AND l.link_type = :2 ";
        $cur = $dbh->prepare($query)->execute($node, $this->tp_link_type); 
        $row = $cur->fetch_assoc();
        if($row) {
            $parent_node = $row['left'];
            $this->set_open_path($parent_node);
        }
        else {
            return true;
        }
    }
    
    public function load_node($id, $p_id, $d, $route = null) 
    {
        $q1 = "//ul[@id='". $p_id ."']"; // путь к узлу дерева куда добавляем ветвь 
        $xpath = new DOMXpath($this->task_pane);
        $found_node1 = $xpath->query($q1)->item(0);
        if (!$found_node1) {
            return false;
        }
        $q2 = "//ul[@id='". $id ."']"; // проверим нет ли узла с таким же id 
        $found_node2 = $xpath->query($q2)->item(0);
        if ($found_node2) {
            return true;
        }
        $level_fragment = '<li ';
        if (!$route ) {
            if (!in_array( $id, $this->open_path )) { // ветвь к текущему узлу отображаем развернутой
                $level_fragment .= 'class="cl"';
            }
        }
        $level_fragment .= '><div>';
        if (!$route) {
            $level_fragment .= '<p onclick="return UnHide(this)">';
            if (in_array( $id, $this->open_path )) { 
                $level_fragment .= '<a href="#" class="sc">&#9660;</a>';
            }
            else {
                $level_fragment .= '<a href="#" class="sc">&#9658;</a>';
            }
        }
        if (!$route) {
            $level_fragment .= $d .'</p></div>';
        } 
        else {
            $level_fragment .= '<p><a href="index.php?'. $route. '">'. $d .'</a></p></div>';        
        }
        if (!$route) {
            $level_fragment .= '<ul id="'. $id .'" />';
        }
        $level_fragment .= '</li>';
        $fragment = $this->task_pane->createDocumentFragment();
        $fragment->appendXML($level_fragment);
        if ($found_node1 instanceof DOMElement) {
            $found_node1->appendChild($fragment);
            $this->show_task_pane();
            return true;
        }
        else {
            throw new Exception("Ошибка добавления узла в иерархии объектов панели задач");
        }
    }
    
    private function show_task_pane()
    {
        $l = Layout::getInstance();
        if  ($l->is_visible()) {
            return true;
        }
        $l->set_visible('sidebar' , 'block');
    }
    
    public function render_tree()
    {
        $tree = $this->task_pane->getElementsByTagName('task_pane')->item(0);
        return $tree;
    }
}

?>