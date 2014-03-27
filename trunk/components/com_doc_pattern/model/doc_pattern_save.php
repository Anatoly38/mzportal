<?php
/**
* @version		$Id: doc_pattern_save.php,v 1.0 2010/04/24 16:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
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

class DocPatternSave extends ItemSave
{
    protected $model = 'DocPatternQuery';
    
    public function get_post_values()
    {
        $this->query->имя           = Request::getVar('имя');
        $this->query->наименование  = Request::getVar('наименование');
        $this->query->описание      = Request::getVar('описание');
        $this->query->периодичность = Request::getVar('периодичность');
        $this->query->вид           = Request::getVar('вид');
        $this->query->версия        = Request::getVar('версия');
        $this->query->статус        = Request::getVar('статус');
        $this->query->дата_утверждения  = Request::getVar('дата_утверждения');
        $this->query->дата_исключения   = Request::getVar('дата_исключения');
        $this->query->основание         = Request::getVar('основание');
    }
}
?>