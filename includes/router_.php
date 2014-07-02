<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Application
* @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Router
{
    private $base_path = null;
    public $query = Array();

	public function __construct($full = true)
	{
        $this->base_path = URI::get_path($full);
	}
    
    public function add_query($parameter, $value = null)
    {
        if (!$value) {
            return false;
        }
        $this->query[$parameter] = $value;
    }
    
    public function get_path()
    {
        $path = $this->base_path;
        if (!empty($this->query)) {
            if (!strpos($path, '?')) {
                $path .= '?';
            }
            foreach ($this->query as $key => $value)
            {
                if (substr($path, -1) != '?') {
                    $path .= '&amp;';
                }
                $path .= $key . '=' . $value;
            }
        }
        return $path;
    }
}

class Route
{
    private $route;
    
    private function __construct()
    {
    
    }
}

?>