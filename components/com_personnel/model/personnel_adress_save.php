<?php
/**
* @version		$Id: personal_aderss_save.php,v 1.0 2011/07/03 15:02:30 shameev Exp $
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
require_once ( MODULES . DS . 'mod_kladr' . DS . 'kladr.php' );

class PersonnelAdressSave extends ItemSave
{
    protected $model = 'AdressQuery';
    private $personnel_id;

    public function get_post_values()
    {
        $this->query->строка_адреса     = Request::getVar('строка_адреса');
        $this->query->вид_адреса        = Request::getVar('вид_адреса');
        $this->query->код_кладр         = Request::getVar('код_кладр');
        $this->query->индекс            = Request::getVar('индекс');
        $this->query->область           = Request::getVar('область');
        $this->query->район             = Request::getVar('район');
        $this->query->город             = Request::getVar('город');
        $this->query->населенный_пункт  = Request::getVar('населенный_пункт');
        $this->query->улица             = Request::getVar('улица');
        $this->query->дом               = Request::getVar('дом');
        $this->query->строение          = Request::getVar('строение');
        $this->query->квартира          = Request::getVar('квартира');
        $this->query->дата_регистрации  = Request::getVar('дата_регистрации');
        $this->query->регистрация       = Request::getVar('регистрация');
        $this->human                    = Request::getVar('human');
        $this->query->область = 'Иркутская область';
        $district   = Kladr::getDistrict($this->query->код_кладр);
        $city       = Kladr::getCity($this->query->код_кладр);
        if (!$this->query->индекс) {
            $this->query->индекс    = Kladr::getIndex($this->query->код_кладр);        
        }
        $this->query->район = $district['prefix'] . ' ' . $district['name'];
        $this->query->населенный_пункт = $city['prefix'] . ' ' . $city['name'];
        $adr = $this->query->индекс . ', ' . $this->query->населенный_пункт . ', ' . $this->query->улица . ', ';
        $adr = $adr . 'д.' . $this->query->дом . ', ' . $this->query->строение . ', кв.' . $this->query->квартира;
        $this->query->строка_адреса = $adr;
    }
    
    public function set_assoc()
    {
        $document_link = Reference::get_id('адрес', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $document_link); // Ассоциация между карточкой сотрудника и документом
        }
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (PersDemographic, Adress) не сохранена!');
            return false;
        }
    }
    
    
}
?>