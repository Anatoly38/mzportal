<?php
/**
* @version		$Id: spec_save.php,v 1.0 2011/01/22 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2011 МИАЦ ИО
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

class PersonnelSave extends ItemSave
{
    protected $model = 'AdapterPersonnelQuery';

    public function get_post_values()
    {
        $this->query->фамилия = Request::getVar('фамилия');
        $this->query->имя = Request::getVar('имя');
        $this->query->отчество = Request::getVar('отчество');
        $this->query->пол = Request::getVar('пол');
        $this->query->дата_рождения = Request::getVar('дата_рождения');
        $this->query->дата_смерти = Request::getVar('дата_смерти');
        $this->query->гражданство = Request::getVar('гражданство');
        $this->query->табельный_номер = Request::getVar('табельный_номер');
        $this->query->снилс = Request::getVar('снилс');
        $this->query->инн = Request::getVar('инн');
        $this->query->телефон = Request::getVar('телефон');
        $this->query->семейное_положение = Request::getVar('семейное_положение');
        $this->query->дети = Request::getVar('дети');
        $this->query->автомобиль = Request::getVar('автомобиль');
        $this->query->pers_id = Request::getVar('pers_id');
        $lpu_id = Request::getVar('lpu_id');
        if ($lpu_id) {
            $this->query->update_lpu($lpu_id);
        }
        if (!$this->item) {
            return;
        }
    }
}
?>