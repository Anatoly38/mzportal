<?php
/**
* @version		$Id: passport.php,v 1.10 2009/09/21 11:13:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Indexes
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

require_once ( MZPATH_BASE .DS.'components'.DS.'component.php' );
require_once ( 'lpu.php' );
require_once ( 'uchr_zdrav.php' );
require_once ( 'helpers' . DS . 'lpu_query.php' );

class Passport extends Component
{
    protected static $default_view = 'list';
    protected $content;
    
    // функции определены в классе Component:
    // get_parameters($request) 
    // get_content()
    
    protected function process_task()
    {
        if (!$this->task) {
            $this->process_view('list');
            return;
        }
        switch ($this->task) {
            case 'new':
                $this->process_view('new');
                break;
            case 'cancel':
                $this->process_view('list');
                break;                
            case 'edit':
                if (!$this->oid) {
                    $m =& Message::getInstance();
                    $m->enque_message('error', 'ЛПУ не определено!');
                    $this->process_view('list');
                }
                else {
                    $this->process_view('item');
                }
                break;
            case 'save':
                if (!$this->oid) {
                    $s = new LPU();
                    $s->insert_data();
                } 
                else {
                    $s = new LPU($this->oid[0]);
                    $s->update_data();
                }
                $this->process_view('list');
                break;
            case 'apply':
                break;
            case 'delete':
                $i = new LPU($this->oid);
                $this->process_view('list');
                break;
        }
    }
    
    protected function process_view($view = false)
    {
        switch ($view) {
            case false:
            case 'list':
                $index_list = new LPU_List();
                self::set_title('Список ЛПУ');
                self::set_toolbar('new', 'Создать');
                self::set_toolbar('edit', 'Редактировать');
                self::set_toolbar('delete', 'Удалить');
                $this->content = $index_list->get_indexes_page();
            break;
            case 'item':
                self::set_title('Редактировать данные ЛПУ');
                $i = new Edit_Index($this->oid[0]);
                self::set_toolbar('close', 'Закрыть');
                self::set_toolbar('save', 'Сохранить');
                $form = $i->get_form();
                $this->content = $form;
            break;
            case 'new':
                self::set_title('Новое ЛПУ');
                $i = new Edit_Index();
                self::set_toolbar('close', 'Закрыть');
                self::set_toolbar('save', 'Сохранить');
                $form = $i->get_form();
                $this->content = $form;
            break;
        }
    }
}

?>