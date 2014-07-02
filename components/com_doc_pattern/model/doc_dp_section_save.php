<?php
/**
* @version		$Id$
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class DocDpSectionSave extends ItemSave
{
    protected $model = 'DocDpSectionQuery';
    
    public function get_post_values()
    {
        $this->query->doc_pattern_id = Request::getVar('pattern');
        $this->query->наименование  = Request::getVar('наименование');
        $this->query->описание      = Request::getVar('описание');
        $this->query->тип           = Request::getVar('тип');
        $this->query->диапазон_данных = Request::getVar('диапазон_данных');
        $this->query->диапазон_печати = Request::getVar('диапазон_печати');
        //$this->query->шаблон_формы  = Request::getVar('шаблон_формы');
        
    }
}
?>