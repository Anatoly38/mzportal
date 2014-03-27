<?php
/**
* @version		$Id: import.php,v 1.1 2009/07/22 06:26:29 shameev Exp $
* @package		MZoomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* MZoomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_MZEXEC' ) or die( 'Restricted access' );

/**
 * Load the loader class
 */
if (! class_exists('MZLoader')) {
    require_once( MZPATH_ROOT .DS.'includes'.DS.'loader.php');
}

/**
 * MZoomla! library imports
 */

//Base classes
MZLoader::import( 'joomla.base.object' 			);

//Environment classes
MZLoader::import( 'joomla.environment.request'   );
MZRequest::clean();

MZLoader::import( 'joomla.environment.response'  );

//Factory class and methods
MZLoader::import( 'joomla.factory' 				);
MZLoader::import( 'joomla.version' 				);
if (!defined('MZVERSION')) {
	$version = new MZVersion();
	define('MZVERSION', $version->getShortVersion());
}

//Error
MZLoader::import( 'joomla.error.error' 			);
MZLoader::import( 'joomla.error.exception' 		);

//Utilities
MZLoader::import( 'joomla.utilities.arrayhelper' );

//Filters
MZLoader::import( 'joomla.filter.filterinput'	);
MZLoader::import( 'joomla.filter.filteroutput'	);

//Register class that don't follow one file per class naming conventions
MZLoader::register('MZText' , dirname(__FILE__).DS.'methods.php');
MZLoader::register('MZRoute', dirname(__FILE__).DS.'methods.php');
