<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Frontpage
* @copyright	Copyright (C) 2011 МИАЦ ИО

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
