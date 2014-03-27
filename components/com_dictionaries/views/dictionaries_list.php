<?php
/**
* @version		$Id: dictionaries_list.php,v 1.0 2011/03/22 13:40:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DictionariesList extends ItemList
{
    protected $model = null;
    protected $source = null;
    protected $namespace = 'dictionaries';
    public    $items = Array();
    protected $constraints = null;
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    public function get_items_page() 
    {
        $dbh = new DB_mzportal();
        $query = "SHOW TABLE STATUS WHERE NAME LIKE 'dic_%' AND Engine IS NOT NULL"; 
        $this->items = $dbh->prepare($query)->execute()->fetchall_assoc();
        $page = $this->display_table();
        return $page;
    }

    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'dic_name[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'Name' ,'title' => 'Имя таблицы', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'Rows' ,'title' => 'Кол-во строк', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'Create_time' ,'title' => 'Создана', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'Update_time' ,'title' => 'Изменена', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'Comment','title' => 'Описание', 'sort' => false, 'type' => 'plain' );
           
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item['Name'];
                $g[$i][] = $item['Name'];
                $g[$i][] = $item['Rows'];
                $g[$i][] = $item['Create_time'];
                $g[$i][] = $item['Update_time'];
                $g[$i][] = $item['Comment'];
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $table = $t->render_table();
        $admin_form = $this->constraints . $table;
        return $admin_form;
    }
}
?>