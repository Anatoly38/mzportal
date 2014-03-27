<?php
/**
* @version		$Id: assignment_save.php,v 1.0 2010/12/09 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Assignments
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class AssignmentSave extends ItemSave
{
    protected $model = 'AssignmentQuery';
    
    public function get_post_values()
    {
        $this->query->наименование   = Request::getVar('наименование');
        $this->query->описание       = Request::getVar('описание');
        $this->query->содержание  = Request::getVar('содержание');
        $this->query->дата_вынесения       = Request::getVar('дата_вынесения');
        $this->query->руководитель = Request::getVar('руководитель');
        $this->query->выполнение = Request::getVar('выполнение');
        $this->query->дата_выполнения           = Request::getVar('дата_выполнения');
        }
}
?>