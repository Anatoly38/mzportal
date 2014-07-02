<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Framework
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'com_frontpage'.DS.'views' .DS. 'view_cpanel.php' );

class Component
{
    protected $task;
    protected $content;
    protected $user_id;
    protected $oid = array();
    protected $default_view = 'view_list';
    
    function __construct()
    {
        $this->init();
        $this->process_task();
    }
    
    protected function init()
    {
        $r = Registry::getInstance();
        $this->task = $r->task;
        $this->user_id = $r->user->user_id;
        $this->user_name = $r->user->name;
        $this->rights = $r->rights;
        $this->oid = (array)Request::getVar('oid');
    }
    
    protected function exec_default()
    {
        $d = $this->default_view;
        $this->$d();
    }
    
    protected function process_task($task_preffix = 'exec_')
    {
        $d = $this->default_view;
        if (!$this->task) {
            $this->$d();
        return;
        }
        $do = $task_preffix . $this->task; 
        $this->$do();
    }
    
    protected static function set_toolbar_button($icon, $task, $caption)
    {
        $tb = Toolbar_Content::getInstance();
        $b = $tb->add_button($icon, $task, $caption);
        return $b;
    }
    
    protected static function set_title($text) 
    {
    
        $t = Page_Title::getInstance();
        $t->set_title($text);
    }
    
    protected function exec_cpanel()
    {
        self::set_title('Доступные приложения');
        $cp = new ViewControlPanel();
        $this->set_content($cp->render());
        $document = Content::getInstance();
        $document->set_modal();
    }

    public function set_content($content)
    {
        if (!$content) {
            return false;
        }
        $c = Content::getInstance();
        $c->add_content($content);
        return true;
    }
}

?>