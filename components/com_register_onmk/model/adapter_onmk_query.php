<?php
/**
* @version		$Id: adapter_onmk_query.php,v 1.0 2010/07/24 20:07:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Register ONMK
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
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_personal' .DS.'model' .DS. 'pers_demograph_query.php' );
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_adress' .DS.'model' .DS. 'adress_query.php' );
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_lpu' .DS.'model' .DS. 'lpu_query.php' );
require_once ( 'register_onmk_query.php' );

class AdapterOnmkQuery
{
    private $onmk; // Active Record объект для данных регистра ОНМК
    private $personal; // Active Record объект для персональных демографических данных
    private $lpu; // Active Record объект для данных ЛПУ
    private $adress; // Active Record объект для адресных данных
    public $oid;
    // Демографическая часть
    public $pers_id;
    public $фамилия;
    public $имя;
    public $отчество;
    public $пол;
    public $дата_рождения;
    // Адрес регистрации
//    public $строка_адреса;
    // Лечебное учреждение
    public $lpu_id;
    public $territory;
    // Собственно регистр ОКС
    public $направитель;
    public $срок_госпитализации;
    public $приемный_покой;
    public $интенсивная_терапия;
    public $дата_поступления;
    public $диагноз_мкб10;
    public $дата_выписки;
    public $исход;
    public $рсц;
    public $тлт_проведение;
    public $тлт_срок;
    public $тлт_препарат;
    public $тлт_эффективность;
    public $тлт_осложнения;

    
    public function __construct($oid = false)
    {
        $this->onmk                  = new RegisterOnmkQuery($oid);
        $this->oid                  = $oid;
        //$this->pers_id              =& $this->onmk->pers_id;
        $this->lpu_id               =& $this->onmk->lpu_id;
        $this->направитель          =& $this->onmk->направитель;
        $this->срок_госпитализации  =& $this->onmk->срок_госпитализации;
        $this->приемный_покой       =& $this->onmk->приемный_покой;
        $this->интенсивная_терапия  =& $this->onmk->интенсивная_терапия;
        $this->дата_поступления     =& $this->onmk->дата_поступления;
        $this->диагноз_мкб10        =& $this->onmk->диагноз_мкб10;
        $this->дата_выписки         =& $this->onmk->дата_выписки;
        $this->исход                =& $this->onmk->исход;
        $this->рсц                  =& $this->onmk->рсц;
        $this->тлт_проведение       =& $this->onmk->тлт_проведение;
        $this->тлт_препарат         =& $this->onmk->тлт_препарат;
        $this->тлт_срок             =& $this->onmk->тлт_срок;
        $this->тлт_эффективность    =& $this->onmk->тлт_эффективность;
        $this->тлт_осложнения       =& $this->onmk->тлт_осложнения;
        $this->personal             = new PersDemographQuery($this->onmk->pers_id);
        $this->pers_id              =& $this->personal->pers_id;
        $this->фамилия              =& $this->personal->фамилия;
        $this->имя                  =& $this->personal->имя;
        $this->отчество             =& $this->personal->отчество;
        $this->пол                  =& $this->personal->пол;
        $this->дата_рождения        =& $this->personal->дата_рождения;
        //$this->adress               = new AdressQuery($this->personal->адрес_регистрации);
        //$this->строка_адреса        =& $this->adress->строка_адреса;
        if ($this->lpu_id) {
            $this->territory = LpuQuery::get_territory($this->lpu_id);
        }
    }

    public function update()
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        if(!$this->pers_id) 
        {
            throw new Exception("Для вызова update() необходим код пациента");
        }
        if(!$this->lpu_id) 
        {
            throw new Exception("Для вызова update() необходим код ЛПУ");
        }
        if ($this->adress->oid) {
            $this->adress->update();
        } 
        else {
            $this->adress->insert();
            $this->personal->адрес_регистрации = $this->adress->oid;
        }
        $this->personal->update();
        $this->onmk->update();
    }

    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        if (!$this->lpu_id) {
            throw new Exception("Не определен код ЛПУ, вставка невозможна");
        }
        if (!$this->строка_адреса) {
            throw new Exception("Не введен адрес пациента , вставка невозможна");
        }
        // Сохранение адресных данных
        if (!$this->adress) {
            $this->adress = new AdressQuery();
        }
        $this->adress->insert();
        // Сохранение персональных демографических данных
        if (!$this->personal) {
            $this->personal = new PersDemographQuery();
        }
        $this->personal->адрес_регистрации =& $this->adress->oid;
        $this->personal->insert();
        // Сохранение собственно данных регистра ОКС
        if (!$this->onmk) {
            $this->onmk = new RegisterOksQuery();
        }
        $this->onmk->pers_id =& $this->personal->oid;
        $this->onmk->insert();
        $this->oid =& $this->onmk->oid;
    }
    
  
    public function get_lpu_name()
    {
        if (!$this->lpu) {
            $this->lpu = new LpuQuery($this->lpu_id);
        }
        return $this->lpu->наименование;
    }
    
    public function get_as_array()
    {
        $fields = array();
        $personal = $this->personal->get_as_array();
        $onmk = $this->onmk->get_as_array();
        $adress = array();
        if ($this->adress->oid) {
            $adress = $this->adress->get_as_array();
        }
        $fields = array_merge($adress, $personal, $onmk);
        $fields['territory'] = $this->territory;
        return $fields;
    }
}

?>