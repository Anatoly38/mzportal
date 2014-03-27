<?php
/**
* @version		$Id: frontpage.php,v 1.0 2010/04/18 11:13:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Frontpage
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component.php' );
//require_once ( 'views' . DS . 'view_frontpage.php' );
require_once ( 'views' . DS . 'view_cpanel.php' );

class Frontpage extends Component
{
    protected $default_view = 'view_frontpage';
    
    protected function view_frontpage() 
    {
        self::set_title('Панель управления');
        //$cp = new ViewFrontpage();
        //$this->set_content($cp->get_page());
        $cp = new ViewControlPanel();
        $this->set_content($cp->render());
    }
}
?>