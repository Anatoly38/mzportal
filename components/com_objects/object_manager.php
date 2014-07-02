<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Territory
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
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'object_query.php' );
//require_once ( 'model' . DS . 'object_delete.php' );
require_once ( 'model' . DS . 'object_save.php' );
require_once ( 'views' . DS . 'object_list.php' );
require_once ( 'views' . DS . 'object_item.php' );

class ObjectManager extends Component
{

    protected function exec_cancel()
    {
        $this->view_list();
    }
    
    protected function exec_edit()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', ' не определена!');
            $this->view_list();
        }
        else {
            $this->view_item();
        }
    }
    
    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Объект не определен!');
            $this->view_list();
        }
        $s = new ObjectSave($this->oid[0]);
        $s->update_data();
        $this->view_list();    
    }
    
    protected function exec_delete()
    {
        $i = new ObjectDelete($this->oid);
        $this->view_list();
    }

    protected function view_list()
    {
        self::set_title('Реестр объектов');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        self::set_toolbar_button('delete', 'delete' , 'Удалить');        
        $list = new ObjectList();
        $this->set_content($list->get_items_page());
    }

    protected function view_item()
    {
        $i = new ObjectItem($this->oid[0]);
        self::set_title('Редактирование данных объекта id' . $i->query->oid );
        $i->edit_item();
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }

}
?>