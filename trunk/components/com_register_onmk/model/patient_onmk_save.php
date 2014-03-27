<?php
/**
* @version		$Id: patient_save.php,v 1.0 2010/05/24 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Register OKS
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

class PatientOnmkSave extends ItemSave
{
    protected $model = 'AdapterOnmkQuery';
    
    public function get_post_values()
    {
        // Демография
        $this->query->pers_id   = Request::getVar('pers_id');
        $this->query->фамилия   = Request::getVar('фамилия');
        $this->query->имя       = Request::getVar('имя');
        $this->query->отчество  = Request::getVar('отчество');
        $this->query->пол       = Request::getVar('пол');
        $this->query->дата_рождения = Request::getVar('дата_рождения');
        // Адрес
        $this->query->строка_адреса = Request::getVar('строка_адреса');
        // ЛПУ
        $lpu = Request::getVar('lpu_id');
        if (!empty($lpu) && $lpu !== '-1') {
            $this->query->lpu_id = $lpu;
        } 
        else {
            $this->query->lpu_id = null;            
        }
        // Лечение и исход ОНМК
        $this->query->направитель           = Request::getVar('направитель');
        $this->query->срок_госпитализации   = Request::getVar('срок_госпитализации');
        $this->query->приемный_покой        = Request::getVar('приемный_покой');
        $this->query->интенсивная_терапия   = Request::getVar('интенсивная_терапия');
        $this->query->дата_поступления      = Request::getVar('дата_поступления');
        $this->query->диагноз_мкб10         = Request::getVar('диагноз_мкб10');
        $this->query->дата_выписки          = Request::getVar('дата_выписки');
        $this->query->исход                 = Request::getVar('исход');
        $this->query->рсц                   = Request::getVar('рсц');
        $this->query->тлт_проведение        = Request::getVar('тлт_проведение');
        $this->query->тлт_срок              = Request::getVar('тлт_срок');
        $this->query->тлт_препарат          = Request::getVar('тлт_препарат');
        $this->query->тлт_эффективность     = Request::getVar('тлт_эффективность');
        $this->query->тлт_осложнения        = Request::getVar('тлт_осложнения');
    }
}
?>