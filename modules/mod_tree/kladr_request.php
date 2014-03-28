<?php
/**
* @version		$Id: kladr_request.php,v 1.0 2011/06/28 00:44:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Frontpage
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

Прямой доступ запрещен
*/
//defined( '_MZEXEC' ) or die( 'Restricted access' );
define( 'MZPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once('tree_kladr.php');
require_once ( 'C:\Apache\htdocs\mzportal\common\database.php' );

$tree_kladr = new TreeKladr('dic_kladr_reg');

$id = $_REQUEST['id'];
if (isset($_REQUEST['search_string'])) { 
    $search_string = $_REQUEST['search_string'];
}

header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

if (isset($_REQUEST['search_string'])) {
    echo $tree_kladr->search_tree($search_string);
} 
else {
    echo $tree_kladr->get_tree($id);
}
die();

?>
