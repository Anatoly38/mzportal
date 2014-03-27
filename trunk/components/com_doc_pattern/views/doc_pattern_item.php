<?php
/**
* @version		$Id: doc_pattern_item.php,v 1.0 2010/04/24 16:50:30 shameev Exp $
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );
require_once ( 'section_tree.php' );

class DocPatternItem extends Item 
{
    protected $model = 'DocPatternQuery';
    protected $form = 'doc_pattern_form_tmpl';
    
}

?>