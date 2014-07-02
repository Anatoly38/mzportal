<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
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

class PersonnelList extends ItemList
{
    protected $model = 'AdapterPersonnelQuery';
    protected $source = 'personnel_lpu';
    protected $namespace = 'personnel_list';
    protected $task = 'personnel_list';
    protected $lpu; 

    public function __construct($lpu =false)
    {
        parent::__construct($this->model, $this->source, $this->namespace);
    }

    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('фамилия');
        $constr->add_filter('пол', 'dic_administrative_sex' );
        $constr->get_filters();
    }

    protected function add(AdapterPersonnelQuery $item)
    {
        parent::add($item);
    }

    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'personnel' , 'title' => '', 'sort' => false, 'type' => 'checkbox' );
        $g[0][] = array('name' => 'табельный_номер' ,'title' => 'ТН', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'фамилия' ,'title' => 'Фамилия', 'sort' => true, 'type' => 'link' );          
        $g[0][] = array('name' => 'имя','title' => 'Имя', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'отчество','title' => 'Отчество', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'наименование_лпу' ,'title' => 'ЛПУ', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'пол','title' => 'Пол', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_рождения','title' => 'ДР', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'снилс','title' => 'СНИЛС', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'инн','title' => 'ИНН', 'sort' => true, 'type' => 'plain' );

        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->табельный_номер;
                $g[$i][] = $item->фамилия;
                $g[$i][] = $item->имя;
                $g[$i][] = $item->отчество;
                $g[$i][] = $item->наименование_лпу;
                $g[$i][] = Reference::get_name($item->пол, 'administrative_sex');
                $g[$i][] = $item->дата_рождения;
                $g[$i][] = $item->снилс;
                $g[$i][] = $item->инн;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_personnel');
        $t->set_object_name('personnel');
        $t->set_order_task('personnel_list');
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>