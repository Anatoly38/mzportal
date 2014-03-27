<?php
/**
* @version		$Id: dictionary_items_list.php,v 1.0 2011/04/04 13:40:30 shameev Exp $
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

class DictionaryItemsList extends ItemList
{
    protected $model = 'DicQuery';
    protected $source = null;
    protected $namespace = 'dictionary_items';
    public    $items = Array();
    protected $constraints = null;
    
    public function __construct($source)
    {
        $this->source = $source;
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    public function get_items_page() 
    {
        $dbh = new DB_mzportal();
        $query = "SELECT * FROM {$this->source}"; 
        $this->items = $dbh->prepare($query)->execute()->fetchall_assoc();
        $page = $this->display_table();
        return $page;
    }

    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'dic_el[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'код' ,'title' => 'Код', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'родитель' ,'title' => 'Родительский элемент', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'наименование' ,'title' => 'Наименование', 'sort' => false, 'type' => 'plain' );  
        $g[0][] = array('name' => 'комментарий' ,'title' => 'Комментарий', 'sort' => false, 'type' => 'plain' );  
           
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item['код'];
                $g[$i][] = $item['код'];
                $g[$i][] = $item['родитель'];
                $g[$i][] = $item['наименование'];
                $g[$i][] = $item['комментарий'];
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