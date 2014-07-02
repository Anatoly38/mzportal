<?php
/**
 * @version		$Id$
 * @package		MZPortal.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'html'.DS.'select.php' );

class Pagination
{
    public $limitstart = null;
    public $limit = null;
    public $total = null;
    protected $_viewall = false;
    private $task;
    private $pages_total;
    private $pages_current;
    private $pages_start;
    private $pages_stop;
    
    function __construct($total, $limitstart, $limit, $task = null)
    {
        $this->task         = $task;
        $this->total        = (int) $total;
        $this->limitstart   = (int) max($limitstart, 0);
        $this->limit        = (int) max($limit, 0);
        if ($this->limit > $this->total) {
            $this->limitstart = 0;
        }
        if (!$this->limit) {
            $this->limit = $total;
            $this->limitstart = 0;
        }
        if ($this->limitstart > $this->total) {
            $this->limitstart -= $this->limitstart % $this->limit;
            //$this->limitstart = 0;
        }
        // Set the total pages and current page values
        if($this->limit > 0) {
            $this->pages_total = ceil($this->total / $this->limit);
            $this->pages_current = ceil(($this->limitstart + 1) / $this->limit);
        }

        // Set the pagination iteration loop values
        $displayedPages	= 10;
        $this->pages_start = (floor(($this->pages_current -1) / $displayedPages)) * $displayedPages +1;
        if ($this->pages_start + $displayedPages -1 < $this->pages_total) {
            $this->pages_stop = $this->pages_start + $displayedPages -1;
        } 
        else {
            $this->pages_stop = $this->pages_total;
        }

        // If we are viewing all records set the view all flag to true
        if ($this->limit == $total) {
            $this->_viewall = true;
        }
    }

	/**
	 * Return the rationalised offset for a row with a given index.
	 *
	 * @access	public
	 * @param	int		$index The row index
	 * @return	int		Rationalised offset for a row with a given index
	 * @since	1.5
	 */
	public function getRowOffset($index)
	{
		return $index +1 + $this->limitstart;
	}

	/**
	 * Return the pagination data object, only creating it if it doesn't already exist
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	public function getData()
	{
		static $data;
		if (!is_object($data)) {
			$data = $this->_buildDataObject();
		}
		return $data;
	}

	/**
	 * Создает и возвращает счетчик страниц по типу Страница 2 из 4
	 *
	 * @access	public
	 * @return	string	Pagination pages counter string
	 * @since	1.5
	 */
	public function getPagesCounter()
	{
		// Initialize variables
		$html = null;
		if ($this->pages_total > 1) {
			$html .= 'Страница ' . $this->pages_current. ' из ' . $this->pages_total;
		}
		return $html;
	}

	/**
	 * Create and return the pagination result set counter string, ie. Results 1-10 of 42
	 *
	 * @access	public
	 * @return	string	Pagination result set counter string
	 * @since	1.5
	 */
	public function getResultsCounter()
	{
		// Initialize variables
		$html = null;
		$from_result = $this->limitstart + 1;

		// If the limit is reached before the end of the list
		if ($this->limitstart + $this->limit < $this->total) {
			$to_result = $this->limitstart + $this->limit;
		} else {
			$to_result = $this->total;
		}

		// If there are results found
		if ($this->total > 0) {
			$msg = 'Строки ' . $from_result . '-' . $to_result . ' из ' . $this->total;
			$html .= '\n'. $msg;
		} else {
			$html .= '\n' . 'Записи не найдены';
		}

		return $html;
	}

	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	 *
	 * @access	public
	 * @return	string	Pagination page list string
	 * @since	1.0
	 */
	function getPagesLinks()
	{
        // Build the page navigation list
        $data = $this->_buildDataObject();

        $list = array();
        // Build the select list
        if ($data->all->base !== null) {
            $list['all']['active'] = true;
            $list['all']['data'] = $this->_item_active($data->all);
        } 
        else {
            $list['all']['active'] = false;
            $list['all']['data'] = $this->_item_inactive($data->all);
        }

        if ($data->start->base !== null) {
            $list['start']['active'] = true;
            $list['start']['data'] = $this->_item_active($data->start);
        } 
        else {
            $list['start']['active'] = false;
            $list['start']['data'] = $this->_item_inactive($data->start);
        }
	
        if ($data->previous->base !== null) {
            $list['previous']['active'] = true;
            $list['previous']['data'] = $this->_item_active($data->previous);
        } 
        else {
            $list['previous']['active'] = false;
            $list['previous']['data'] = $this->_item_inactive($data->previous);
        }

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = $this->_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = $this->_item_inactive($data->next);
		}
		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = $this->_item_inactive($data->end);
		}

        if ($this->total > $this->limit) {
            return $this->_list_render($list);
        }
        else {
            return '';
        }
	}

	/**
	 * Return the pagination footer
	 *
	 * @access	public
	 * @return	string	Pagination footer
	 * @since	1.0
	 */
    public function getListFooter()
    {
        $list = array();
        $list['limit']          = $this->limit;
        $list['limitstart']     = $this->limitstart;
        $list['total']			= $this->total;
        $list['limitfield']		= $this->getLimitBox();
        $list['pagescounter']	= $this->getPagesCounter();
        $list['pageslinks']		= $this->getPagesLinks();
        return $this->_list_footer($list);
    }

	/**
	 * Creates a dropdown box for selecting how many records to show per page
	 *
	 * @access	public
	 * @return	string	The html for the limit # input box
	 * @since	1.0
	 */
    function getLimitBox()
    {
        // Initialize variables
        $limits = array ();
        // Make the option list
        for ($i = 5; $i <= 30; $i += 5) {
            $limits[] = HTMLSelect::option("$i", "$i");
        }
		$limits[] = HTMLSelect::option('50', '50');
		$limits[] = HTMLSelect::option('100', '100');
		$limits[] = HTMLSelect::option('0', 'Все');

		$selected = $this->_viewall ? 0 : $this->limit;

		// Build the select list
		$html = HTMLSelect::genericlist( $limits, 'limit', 'class="inputbox" size="1" onchange="submitform(\'' . $this->task . '\');"', 'value', 'text', $selected);
		return $html;
	}

	/**
	 * Return the icon to move an item UP
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item up or a space
	 * @since	1.0
	 */
	function orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'Move Up', $enabled = true)
	{
		$html = '&nbsp;';
		if (($i > 0 || ($i + $this->limitstart > 0)) && $condition)
		{
			if($enabled) {
				$html	= '<a href="#reorder" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$alt.'">';
				$html	.= '   <img src="images/uparrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
				$html	.= '</a>';
			} else {
				$html	= '<img src="images/uparrow0.png" width="16" height="16" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}

	/**
	 * Return the icon to move an item DOWN
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	int		$n The number of items in the list
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item down or a space
	 * @since	1.0
	 */
	function orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = 'Move Down', $enabled = true)
	{
		$html = '&nbsp;';
		if (($i < $n -1 || $i + $this->limitstart < $this->total - 1) && $condition)
		{
			if($enabled) {
				$html	= '<a href="#reorder" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$alt.'">';
				$html	.= '  <img src="images/downarrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
				$html	.= '</a>';
			} else {
				$html	= '<img src="images/downarrow0.png" width="16" height="16" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}

	function _list_footer($list)
	{
		// Initialize variables
		$html = '<div class="pagination">';
		$html .= '<div class="limit">' . 'Количество строк: ' . $list['limitfield'].'</div>';
        $html .= $list['pageslinks'];
		$html .= '<div class="limit">' . $list['pagescounter'] . '</div>';
		$html .=  '<input type="hidden" name="limitstart" value="' . $list['limitstart'] . '" />';
		$html .= "\n</div>";

		return $html;
	}

	function _list_render($list)
	{
		// Initialize variables
		$html = null;

		// Reverse output rendering for right-to-left display
		$html .= '<div class="button2-right"><div class="start">';
		$html .= $list['start']['data'];
		$html .= '</div></div>';
		$html .= '<div class="button2-right"><div class="prev">';
        $html .= $list['previous']['data'];
		$html .= '</div></div>';
        $html .= '<div class="button2-left"><div class="page">';
        foreach( $list['pages'] as $page ) {
			$html .= ' '.$page['data'];
		}
        $html .= '</div></div>';
        $html .= '<div class="button2-left"><div class="next">';
		$html .= $list['next']['data'];
		$html .= '</div></div>';
        $html .= '<div class="button2-left"><div class="end">';
		$html .= $list['end']['data'];
		$html .= '</div></div>';

		return $html;
	}

	function _item_active(&$item)
	{
        if ($item->base > 0) {
            return "<a href=\"\" title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=".$item->base."; submitform('" . $this->task . "');return false;\">".$item->text."</a>";
        }
        else {
            return "<a href=\"\" title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=0; submitform('" . $this->task . "');return false;\">".$item->text."</a>";
		}
	}

	function _item_inactive(&$item)
	{
        return "<span>".$item->text."</span>";
	}

	/**
	 * Create and return the pagination data object
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		$data->all	= new PaginationObject('Показать все строки');
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->link	= '&limitstart=';
		}

		// Set the start and previous data objects
		$data->start	= new PaginationObject('Первая');
		$data->previous	= new PaginationObject('Предыдущая');

		if ($this->pages_current > 1)
		{
			$page = ($this->pages_current - 2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= '&limitstart=';
			$data->previous->base	= $page;
			$data->previous->link	= '&limitstart=' . $page;
		}

		// Set the next and end data objects
		$data->next	= new PaginationObject('Следующая');
		$data->end	= new PaginationObject('Последняя');

		if ($this->pages_current < $this->pages_total)
		{
			$next = $this->pages_current * $this->limit;
			$end  = ($this->pages_total - 1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link	= '&limitstart=' . $next;
			$data->end->base	= $end;
			$data->end->link	= '&limitstart=' . $end;
		}

		$data->pages = array();
		$stop = $this->pages_stop;
		for ($i = $this->pages_start; $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route
			$data->pages[$i] = new PaginationObject($i);
			if ($i != $this->pages_current || $this->_viewall)
			{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= '&limitstart=' . $offset;
			}
		}
		return $data;
	}
}

/**
 * Pagination object representing a particular item in the pagination lists
 *
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class PaginationObject //extends JObject
{
    var $text;
    var $base;
    var $link;

    function __construct($text, $base=null, $link=null)
    {
        $this->text = $text;
        $this->base = $base;
        $this->link = $link;
    }
}