<?php
/**
* @version		$Id: adapter_oks_query.php,v 1.0 2010/05/24 06:30:30 shameev Exp $
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
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_personal' .DS.'model' .DS. 'pers_demograph_query.php' );
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_adress' .DS.'model' .DS. 'adress_query.php' );
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_lpu' .DS.'model' .DS. 'lpu_query.php' );
require_once ( 'register_oks_query.php' );

class AdapterOksQuery
{
    private $oks; // Active Record объект для данных регистра ОКС
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
    //public $строка_адреса;
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
        $this->oks                  = new RegisterOksQuery($oid);
        $this->oid                  = $oid;
        $this->lpu_id               =& $this->oks->lpu_id;
        $this->направитель          =& $this->oks->направитель;
        $this->срок_госпитализации  =& $this->oks->срок_госпитализации;
        $this->приемный_покой       =& $this->oks->приемный_покой;
        $this->интенсивная_терапия  =& $this->oks->интенсивная_терапия;
        $this->дата_поступления     =& $this->oks->дата_поступления;
        $this->диагноз_мкб10        =& $this->oks->диагноз_мкб10;
        $this->дата_выписки         =& $this->oks->дата_выписки;
        $this->исход                =& $this->oks->исход;
        $this->рсц                  =& $this->oks->рсц;
        $this->тлт_проведение       =& $this->oks->тлт_проведение;
        $this->тлт_препарат         =& $this->oks->тлт_препарат;
        $this->тлт_срок             =& $this->oks->тлт_срок;
        $this->тлт_эффективность    =& $this->oks->тлт_эффективность;
        $this->тлт_осложнения       =& $this->oks->тлт_осложнения;
        $this->personal             = new PersDemographQuery($this->oks->pers_id);
        $this->pers_id              =& $this->personal->pers_id;
        $this->фамилия              =& $this->personal->фамилия;
        $this->имя                  =& $this->personal->имя;
        $this->отчество             =& $this->personal->отчество;
        $this->пол                  =& $this->personal->пол;
        $this->дата_рождения        =& $this->personal->дата_рождения;
/*         if (!$this->personal->адрес_регистрации) {
            $this->adress = new AdressQuery();
        } 
        else {
            try {
                $this->adress = new AdressQuery($this->personal->адрес_регистрации);
            } 
            catch (AdressQueryException $e) {
                $this->adress = new AdressQuery();
                $m = Message::getInstance();
                $m->enque_message('error', $e->get_message());
            }
        }
        $this->строка_адреса =& $this->adress->строка_адреса; */
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
/*         if (!$this->adress) {
            $this->adress = new AdressQuery();
        }
        if ($this->adress->oid) {
            $this->adress->update();
        } 
        else {
            $this->adress->insert();
            $this->personal->адрес_регистрации = $this->adress->oid;
        }*/
        $this->personal->update();
        $this->oks->update();
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
        // if (!$this->строка_адреса) {
            // throw new Exception("Не введен адрес пациента , вставка невозможна");
        // }
        // Сохранение адресных данных
        // if (!$this->adress) {
            // $this->adress = new AdressQuery();
        // }
        // $this->adress->insert();
        // Сохранение персональных демографических данных
        if (!$this->personal) {
            $this->personal = new PersDemographQuery();
        }
        $this->personal->адрес_регистрации =& $this->adress->oid;
        $this->personal->insert();
        // Сохранение собственно данных регистра ОКС
        if (!$this->oks) {
            $this->oks = new RegisterOksQuery();
        }
        $this->oks->pers_id =& $this->personal->oid;
        $this->oks->insert();
        $this->oid =& $this->oks->oid;
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
        $oks = $this->oks->get_as_array();
        $adress = array();
        // if ($this->adress->oid) {
            // $adress = $this->adress->get_as_array();
        // }
        $fields = array_merge($adress, $personal, $oks);
        $fields['territory'] = $this->territory;
        return $fields;
    }
}

?>