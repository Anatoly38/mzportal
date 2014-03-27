<?php
/**
* @version		$Id: register_oks_list.php,v 1.0 2010/05/12 13:40:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2010 МИАЦ ИО
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

class RegisterOksList extends ItemList
{
    protected $model = 'AdapterOksQuery';
    protected $source = 'register_oks_view';
    protected $namespace = 'oks';
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('фамилия');
        $constr->add_filter('пол' , 'dic_administrative_sex');
        $constr->add_filter('направитель' , 'dic_senders');
        $constr->add_filter('диагноз_мкб10' , 'dic_mkb10_oks');
        $constr->add_filter('исход' , 'dic_outcomes');
        $constr->get_filters();
    }
    
    protected function add(AdapterOksQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'oid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'фамилия' ,'title' => 'Фамилия, Имя, Отчество', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'пол' ,'title' => 'Пол', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'дата_рождения' ,'title' => 'Дата рождения', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'lpu_id' ,'title' => 'ЛПУ', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'направитель','title' => 'Направитель', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_поступления','title' => 'Поступил', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'диагноз_мкб10','title' => 'МКБ 10', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_выписки','title' => 'Выписан', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'исход','title' => 'Исход', 'sort' => true, 'type' => 'plain' ); 
           
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->фамилия . ' ' . $item->имя . ' ' . $item->отчество ; 
                $g[$i][] = Reference::get_name($item->пол, 'administrative_sex');
                $g[$i][] = $item->дата_рождения;
                $g[$i][] = $item->get_lpu_name();
                $g[$i][] = Reference::get_name($item->направитель, 'senders');
                $g[$i][] = $item->дата_поступления;
                $g[$i][] = $item->диагноз_мкб10;
                $g[$i][] = $item->дата_выписки;
                $g[$i][] = Reference::get_name($item->исход, 'outcomes');
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>