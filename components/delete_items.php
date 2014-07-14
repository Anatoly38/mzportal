<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Indexes
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

class DeleteItems 
{
    protected $items = array();
    protected $error_message = "Удаляемые объекты не определены";
    protected $alert_message = "Удалены объекты";
    
    public function __construct($items = false)
    {
        if (!$items) {
            throw new Exception($this->error_message);
        } 
        else {
            $this->items = $items;
            $this->set_sys_objects();
        }
    }
    
    protected function set_sys_objects()
    {
        if (is_array($this->items)) {
            for ($i = 0, $cnt = count($this->items); $i < $cnt; $i++) {
                $this->set_object_deleted($this->items[$i]);
            }
            Message::alert($this->alert_message . ' ('. $cnt .')');
        }
    }
    
    protected function set_object_deleted($oid)
    {
        $obj = new MZObject($oid);
        $obj->delete();
    }
    
    public static function set_confirm_dialog($title, $action = 'delete')
    {
        $c = Content::getInstance();
        $text  = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>';
        $text .= 'Эти объекты будут удалены. Вы уверены?</p>';
        $dialog_id = 'confirm-delete';
        $c->set_dialog_form($text, $title, $dialog_id);
        $tb = Toolbar_Content::getInstance();
        $del_button = $tb->get_button($action);
        $code = '$( "#' . $dialog_id . '" ).dialog( "open" );';
        $del_button->set_option('dialog', $code);
        $jq_block =
<<<JS
    $( "#$dialog_id" ).dialog({
        resizable: false,
        autoOpen: false,
        height: 170,
        modal: true,
        buttons: {
            "Удалить": function() {
                $( this ).dialog( "close" );
                $("#adminForm").submit();
                return true;
            },
            "Отменить": function() {
                $( this ).dialog( "close" );
                $("#task").val(null);
                return false;
            }
        }
    });
JS;
        $js = Javascript::getInstance();
        $js->add_jblock($jq_block);
        return true;
    }
}

?>