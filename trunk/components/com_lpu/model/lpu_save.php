<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport
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

class LpuSave extends ItemSave
{
    protected $model = 'LpuQuery';
    
    public function get_post_values()
    {
        $this->query->обособленность    = Request::getVar('обособленность');
        $this->query->код_территории    = Request::getVar('код_территории');
        $this->query->огрн              = Request::getVar('огрн');
        $this->query->код_оуз           = Request::getVar('код_оуз');
        $this->query->почтовый_адрес    = Request::getVar('почтовый_адрес');
        $this->query->фактический_адрес = Request::getVar('фактический_адрес');
        $this->query->руководитель      = Request::getVar('руководитель');
        $this->query->главный_бухгалтер = Request::getVar('главный_бухгалтер');
        $this->query->счет              = Request::getVar('счет');
        $this->query->наименование      = Request::getVar('наименование');
        $this->query->сокращенное_наименование = Request::getVar('сокращенное_наименование');
        $this->query->опф               = Request::getVar('опф');
        $this->query->состояние         = Request::getVar('состояние');
        $this->query->дата_создания     = Request::getVar('дата_создания');
        $this->query->дата_ликвидации   = Request::getVar('дата_ликвидации');
        $this->query->население         = Request::getVar('население');
        $this->query->уровень           = Request::getVar('уровень');
        $this->query->номенклатура      = Request::getVar('номенклатура');
        $this->query->категория         = Request::getVar('категория');
        $this->query->уровень_мп        = Request::getVar('уровень_мп');
        $this->query->возростная_группа = Request::getVar('возростная_группа');
        $this->query->смп               = Request::getVar('смп');
        $this->query->село              = Request::getVar('село');
        $this->query->дети              = Request::getVar('дети');
        $this->query->крр               = Request::getVar('крр');
        $this->query->омс               = Request::getVar('омс');
        $this->query->дополнительно     = Request::getVar('дополнительно');
        $this->query->егрюл             = Request::getVar('егрюл');
        $this->query->вэб_сайт          = Request::getVar('вэб_сайт');
    }
}
?>