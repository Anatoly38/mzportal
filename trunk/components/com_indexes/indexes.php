<?php
/**
* @version		$Id$
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
require_once( 'model' . DS . 'index_query.php' );
require_once( 'model' . DS . 'index_save.php' );
require_once( 'model' . DS . 'index_delete.php' );
require_once( 'views' . DS . 'index_list.php' );
require_once( 'views' . DS . 'index_item.php' );


class Indexes extends Component
{
   
    protected function exec_new()
    {
        $this->view_new_item();   
    }

    protected function exec_cancel()
    {
        $this->view_list();
    }

    protected function exec_edit()
    {
        if (!$this->oid) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Редактируемый показатель не определен');
            $this->view_list();
        }
        else {
            $this->view_edit_item();
        }
    }
    
    protected function exec_save()
    {
        if (empty($this->oid[0])) {
            $s = new IndexSave();
            $s->insert_data();
        } 
        else {
            $s = new IndexSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();    
    }
    
    protected function exec_delete()
    {
        $i = new IndexDelete($this->oid);
        $this->view_list();    
    }

    protected function view_list()
    {
        $index_list = new IndexList();
        self::set_title('Список показателей');
        self::set_toolbar_button('new', 'new' , 'Создать');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        self::set_toolbar_button('delete', 'delete' , 'Удалить');        
        $this->set_content($index_list->get_items_page());
    }

    protected function view_new_item()
    {
        self::set_title('Новый показатель');
        $i = new IndexItem();
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);    
    }
    
    protected function view_edit_item()
    {
        self::set_title('Редактировать показатель');
        $i = new IndexItem($this->oid[0]);
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('apply', 'apply' , 'Применить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->edit_item();
        $form = $i->get_form();
        $this->set_content($form);    
    }
}

?>