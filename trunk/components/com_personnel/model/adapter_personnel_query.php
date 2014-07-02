<?php
/**
* @version		$Id$
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
require_once ( MZPATH_BASE .DS.'components'.DS. 'com_personal' .DS.'model' .DS. 'pers_demograph_query.php' );
require_once ( 'personnel_query.php' );

class AdapterPersonnelQuery
{
    protected $source = 'personnel_lpu';
    private $card; // Active Record объект для Карточка сотрудника учреждения здравоохранения (pasp_personnel)
    private $personal; // Active Record объект для персональных демографических данных (pers_demographic)
    private $lpu; // Active Record объект учреждения здравоохранения (pasp_lpu)
    private $lpu_card_link;
    private $card_personal_link;
    public $oid = false; // Идентификатор объекта "Сотрудник учреждения здравоохранения"
    public $pers_id = false; // Идентификатор объекта "Персональные демографические данные"
    public $lpu_id = false; // Идентификатор объекта "Учреждение здравоохранения"
    public $табельный_номер;
    public $снилс;
    public $инн;
    public $телефон;
    public $семейное_положение;
    public $дети;
    public $автомобиль;
    public $фамилия;
    public $имя;
    public $отчество;
    public $пол;
    public $дата_рождения;
    public $дата_смерти;
    public $гражданство;

    public function __construct($oid = false)
    {
        if ($oid) {
            $dbh = new DB_mzportal;
            $query =    "SELECT 
                        a.pers_id,
                        a.lpu_id,
                        a.наименование_лпу,
                        a.табельный_номер,
                        a.снилс,
                        a.инн,
                        a.телефон,
                        a.семейное_положение,
                        a.дети,
                        a.автомобиль
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
            $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
            if(!$data) {
                throw new Exception("Запись не существует");
            }
        }
        $this->oid                  = $oid;
        if (isset($data)) {
            $this->lpu_id           = $data['lpu_id'];
            $this->pers_id          = $data['pers_id'];
            $this->табельный_номер  = $data['табельный_номер'];
            $this->наименование_лпу  = $data['наименование_лпу'];
            $this->снилс            = $data['снилс'];
            $this->инн              = $data['инн'];
            $this->телефон          = $data['телефон'];
            $this->семейное_положение = $data['семейное_положение'];
            $this->дети             = $data['дети'];
            $this->автомобиль       = $data['автомобиль'];
        }
        //$this->lpu                  = new LpuQuery($this->lpu_id);
        $this->personal             = new PersDemographQuery($this->pers_id);
        $this->card                 = new PersonnelQuery($this->oid);
        $this->pers_id              =& $this->personal->oid;
        $this->oid                  =& $this->card->oid;
        $this->табельный_номер      =& $this->card->табельный_номер;
        $this->снилс                =& $this->card->снилс;
        $this->инн                  =& $this->card->инн;
        $this->телефон              =& $this->card->телефон;
        $this->семейное_положение   =& $this->card->семейное_положение;
        $this->дети                 =& $this->card->дети;
        $this->автомобиль           =& $this->card->автомобиль;
        $this->фамилия              =& $this->personal->фамилия;
        $this->имя                  =& $this->personal->имя;
        $this->отчество             =& $this->personal->отчество;
        $this->пол                  =& $this->personal->пол;
        $this->дата_рождения        =& $this->personal->дата_рождения;
        $this->дата_смерти          =& $this->personal->дата_смерти;
        $this->гражданство          =& $this->personal->гражданство;
        $this->card_personal_link   = Reference::get_id('сотрудник_пд', 'link_types');
        $this->lpu_card_link        = Reference::get_id('сотрудник', 'link_types');
    }
    
    public static function get_by_snils($snils, $lpu)
    {
        if (!$snils) {
            throw new Exception("СНИЛС для поиска не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM personnel_lpu WHERE снилс = :1 AND lpu_id = :2";
        list($id) = $dbh->prepare($query)->execute($snils, $lpu)->fetch_row();
        if(!$id) {
            return false;
        }
        return new AdapterPersonnelQuery($id);
    }
    
    public function update_lpu($lpu_id)
    {
        if (!$lpu_id) {
            return false;
        }
        if ($this->lpu_id) {
            LinkObjects::unset_link($this->lpu_id, $this->oid, $this->lpu_card_link);
        }
        LinkObjects::set_link($lpu_id, $this->oid, $this->lpu_card_link);
    }

    public function update()
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        if(!$this->pers_id) 
        {
            throw new Exception("Для вызова update() необходим код объекта персональных демографических данных");
        }
        $this->personal->update();
        $this->card->update();
    }

    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        // Сохранение персональных демографических данных
        if (!$this->personal) {
            $this->personal = new PersDemographQuery();
        }
        $this->personal->insert();
        // Сохранение карточки сотрудника
        if (!$this->card) {
            $this->card = new PersonnelQuery();
        }
        $this->card->insert();
        LinkObjects::set_link($this->oid, $this->pers_id, $this->card_personal_link); // Ассоциация между сотрудником и персональными демографическими данными
        LinkObjects::set_link($this->lpu_id, $this->oid, $this->lpu_card_link); // Ассоциация между сотрудником и учреждением здравоохранения
    }

    public function get_as_array()
    {
        $summary = array();
        $personal   = $this->personal->get_as_array();
        $card   = $this->card->get_as_array();
        $summary = array_merge($personal, $card);
        $summary['lpu_id'] = $this->lpu_id;
        $summary['pers_id'] = $this->pers_id;
        $summary['oid'] = $this->oid;
        return $summary;
    }
}

?>