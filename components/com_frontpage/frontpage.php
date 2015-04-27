<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Frontpage
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component.php' );
require_once ( 'views' . DS . 'view_cpanel.php' );

class Frontpage extends Component
{
    protected   $default_view = 'view_frontpage';
    private     $custom_components = array();
    
    protected function view_frontpage() 
    {
        self::set_title('Панель управления');
        $cp = new ViewControlPanel();
        $this->set_content($cp->render());
    }
}
?>