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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class IndexSave extends ItemSave
{
    protected $model = 'IndexQuery';
    
    public function get_post_values()
    {
        $this->query->наименование = Request::getVar('наименование');
        $this->query->описание = Request::getVar('описание');
        $this->query->вид = Request::getVar('вид');
        $this->query->группа = Request::getVar('группа');
        $this->query->тип = Request::getVar('тип');
        $this->query->ед_измерения = Request::getVar('ед_измерения');
        $this->query->дата_утверждения = Request::getVar('дата_утверждения');
        $this->query->рег_документ = Request::getVar('рег_документ');
    }
 
}
?>