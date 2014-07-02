<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2009 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );
require_once ( MZPATH_BASE .DS.'common'.DS.'mod_form'.DS.'form_template_loader.php' );

class Item { }

class LPU extends Item 
{
    protected $valid = false;
    protected $lpu_query;
    protected $основное_учр = true;
    protected $form = 'lpu_form_tmpl';
    protected $item;
    
    public function __construct($item = false)
    {
        if (!$item) {
            $this->lpu_query = new LPU_Query();
        }
        else {
            $this->lpu_query = new LPU_Query($item);
        }
    }
    
    public function get_post_values()
    {
        $this->lpu_query->код_территории = $_POST['код_территории'];
        $this->lpu_query->почтовый_адрес = $_POST['почтовый_адрес'];
        $this->lpu_query->фактический_адрес = $_POST['фактический_адрес'];
        $this->lpu_query->руководитель = $_POST['руководитель'];
        $this->lpu_query->наименование = $_POST['наименование'];
        $this->lpu_query->налоговая_идентификация = $_POST['налоговая_идентификация'];
        $this->lpu_query->дата_создания = $_POST['дата_создания'];
        $this->lpu_query->дата_ликвидации = $_POST['дата_ликвидации'];
        $this->lpu_query->население = $_POST['население'];
        $this->lpu_query->номенклатура = $_POST['номенклатура'];
        $this->lpu_query->категория = $_POST['категория'];
        $this->lpu_query->возрастная_группа = $_POST['возрастная_группа'];
        $this->lpu_query->крр = $_POST['крр'];
        $this->lpu_query->дополнительно = $_POST['дополнительно'];
    }
    
    public function insert_data()
    {
    
    }
    
    public function update_data()
    {
    
    }

    public function get_template()
    {
        $f = $this->form;
        $template = MZCONFIG::$$f;
        $full_path = TMPL.DS.$template;
        $this->form_loader = new Form_Template_Loader($full_path);
        $this->form_loader->create_selects();
        $this->form_loader->create_dates();
        
    }
    
    public function set_values(LPU_Query $item)
    {
        $values = $index->get_as_array();
        $this->form_loader->load_values($values);
    }
    
    public function get_form()
    {
        $form_content = $this->form_loader->render();
        return $form_content;
    }
    
    
}

?>