<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2010-2014 МИАЦ ИО

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'common'.DS.'database.php' );
require_once ( MZPATH_BASE .DS.'configuration.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'session'.DS.'session.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'authorization.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'application.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'content.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'layout.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'page_title.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'javascript.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'css.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'request.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'registry.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'uri.php' );
require_once ( MODULES     .DS.'mod_tasks'.DS.'components.php' );
require_once ( MODULES     .DS.'mod_user'.DS.'user.php' );
require_once ( MODULES     .DS.'mod_toolbar'.DS.'toolbar_content.php' );
require_once ( MODULES     .DS.'mod_message'.DS.'message.php' );

class MZFactory
{
private $layout;
private $registry;
private $user_id = null;
private $default_application;
private $tools_pane;        // DOMnode объект для панели инструментов
private $task_pane;         // DOMnode объект для панели задач
private $content;           // DOMnode объект собственно содержания
public $site;               // Сведенная воедино html-страница

    public function __construct() 
    {
        $this->init();
    }
    
    public function check_auth() {
        if ($this->registry->task == 'logout') {
            $auth = new Authorization();
            MZSession::destroy();
            $auth->logout();
            header("Location: index.php");
        }
        try {
            $auth = new Authorization();
            $auth->validate();
            $this->user_id = $auth->user_id;
            $s = new MZSession();
            $s->start(md5($auth->user_id)); // ????? user ID в качестве идентификатора сессии
            $this->registry->user = new User($auth->user_id);
            $apps = Application::get_user_applications($this->registry->user->user_id);
            if (count($apps) == 1) {
                $this->default_application = $apps[0];
            } 
            else {
                $this->default_application = MZConfig::$default_application;
            }
        }
        catch (AuthException $e) {
            header("Location: login.php?originating_uri=".$_SERVER['REQUEST_URI']);
            exit;
        }
    }

    private function init()
    {
        $this->registry = Registry::getInstance();
        $this->registry->application = Request::getVar('app');
        $this->registry->task = Request::getVar('task');
        $this->registry->oid = Request::getVar('oid');
    }

    public function get_layout()
    {
        $l = Layout::getInstance();
        $this->layout = $l->get_layout();
    }

    public function get_message()
    {
        $m = Message::getInstance();
        $message_nodes = $m->get_message();
        return $message_nodes;
    }
 
    public function set_message_queue()
    {
        $message_nodes = $this->get_message();
        $q="//*[@имя='сообщения']"; // Запрос на поиск элемента с аттрибутом имя со значением 'сообщения'
        $xpath = new DOMXpath($this->layout);
        $domNodeList = $xpath->query($q);
        $message_layer = $domNodeList->item(0);
        // Импортируем загруженные элементы области сообщений
        $new_node = $this->layout->importNode($message_nodes, true); 
        $message_layer->parentNode->replaceChild($new_node, $message_layer);
    }

    public function get_content()
    {
        try {
            $a = new Application($this->registry->application);
            $c = Content::getInstance();
            $content_node = $c->get_document_node();
            if (!$content_node) {
                $content_node = false;
            }
        }
       catch (AppException $e) {
            $content_node = false;
            Message::error($e->get_message());
        }
        return $content_node;
    }
 
    public function set_content() 
    {
        $q="//*[@имя='содержание']"; // Запрос на поиск элемента с аттрибутом имя со значением 'содержание'
        $xpath = new DOMXpath($this->layout);
        $domNodeList = $xpath->query($q);
        $content_module = $domNodeList->item(0);
        $this->registry->application;
        if (!$this->registry->application) {
            $this->registry->application = $this->default_application;
        }
        $this->registry->rights = Components::get_rights($this->registry->application, $this->user_id);
        $nodes = $this->get_content();
        // Импортируем загруженные элементы содержания документа
        if ($nodes instanceof DOMElement) {
            $content_node = $this->layout->importNode($nodes, true); 
        } 
        else {
            $nodes = $this->_content();
            $content_node = $this->layout->importNode($nodes, true);
        } 
        $content_module->parentNode->replaceChild($content_node, $content_module);
    }

    private function _content()
    {
        $content = "<hr/>";
        $doc = Content::getInstance();
        $doc->add_content($content);
        $nodes = $doc->get_document_node();
        return $nodes;
    }

    public function set_title()
    {
        $t = Page_Title::getInstance();
        if (!Page_Title::$title) {
            $t->set_title();
        }
        $title_node = $t->get_title();
        $q="//*[@имя='заголовок']"; 
        $xpath = new DOMXpath($this->layout);
        $domNodeList = $xpath->query($q);
        $title_layer = $domNodeList->item(0);
        if ($title_node instanceof DOMElement) {
            $new_node = $this->layout->importNode($title_node, true);
            $title_layer->parentNode->replaceChild($new_node, $title_layer);
        }
    }

    public function set_route()
    {
        if ($this->registry->application) {
            $q="//input[@name='app']"; // Разыскиваем элемент input с атрибутом name="app"
            $xpath = new DOMXpath($this->layout);
            $domNodeList = $xpath->query($q);
            $route_input = $domNodeList->item(0);
            if ($route_input instanceof DOMElement) {
                $route_input->setAttribute('value', $this->registry->application);
            }
        }
        return true;
    }

    public function get_toolbar()
    {
        $m = Toolbar_Content::getInstance();
        $c = Content::getInstance();
        if (!$c->get_mode() && $this->registry->application != $this->default_application ) {
            $js = Javascript::getInstance();
            $options = array('icon' => 'cpanel',  'action' => 'cpanel', 'title' => 'Панель управления' );
            $js->add_toolbar_button($options); 
        }
        $tools_nodes = $m->get_toolbar();
        return $tools_nodes;
    }
    
    public function set_toolbar()
    {
        $button_nodes = $this->get_toolbar();
        $q="//*[@имя='инструменты']"; // Запрос на поиск элемента с аттрибутом имя со значением 'инструменты'
        $xpath = new DOMXpath($this->layout);
        $domNodeList = $xpath->query($q);
        $button_layer = $domNodeList->item(0);
        $new_node = $this->layout->importNode($button_nodes, true); // Импортируем загруженные элементы области сообщений
        $button_layer->parentNode->replaceChild($new_node, $button_layer);
    }

/*     public function get_task_pane()
    {
        $task_object = TaskPaneBuilder::getInstance();
        //$task_object->set_default_tree($this->registry->application, $this->user_id);
        $this->task_pane = $task_object->render_tree();
    }

    public function set_task_pane()
    {
        $this->get_task_pane();
        //$q='//модуль[@имя="задачи"]';  
        $q="//*[@имя='задачи']"; // Запрос на поиск элемента с аттрибутом имя со значением 'задачи'
        $xpath = new DOMXpath($this->layout);
        $domNodeList = $xpath->query($q);
        $task_module = $domNodeList->item(0);
        $new_node = $this->layout->importNode($this->task_pane, true); // Импортируем загруженные элементы панели задач
        $task_module->parentNode->replaceChild($new_node, $task_module);
    } */
    
    public function set_user_pane()
    {
        $q="//*[@имя='пользователь']"; 
        //$user = new User($this->user_id);
        $xpath = new DOMXpath($this->layout);
        $domNodeList = $xpath->query($q);
        $user_module = $domNodeList->item(0);
        $module_content = '<span class="loggedin-user">' . $this->registry->user->name . ' ('. $this->registry->user->description .')</span>' ;
        $module_content .= '<span class="logout"><a href="#" onclick="submitform(\'logout\')"> Завершить работу </a></span>';
        $user_node = $this->layout->createDocumentFragment(); 
        $user_node->appendXML($module_content);
        $user_module->parentNode->replaceChild($user_node, $user_module);
    }
    
    public function set_javascripts()
    {
        $js = Javascript::getInstance();
        $js->add_jquery();
        $js->insert_jblock();
        $js_nodes = $js->get_js_scripts();
        if ($js_nodes instanceof DOMNodeList) {
            $head = $this->layout->getElementsbyTagName('head')->item(0);
            foreach ($js_nodes as $n) {
                $new_node = $this->layout->importNode($n, true);
                $head->appendChild($new_node);
            }
        }
    }

    public function set_css()
    {
        $css = CSS::getInstance();
        $css->add_style_link('mzportal.main.css');
        $css->add_jquery_ui();
        $css_nodes = $css->get_css();
        if ($css_nodes instanceof DOMNodeList) {
            $head = $this->layout->getElementsbyTagName('head')->item(0);
            foreach ($css_nodes as $n) {
                $new_node = $this->layout->importNode($n, true);
                $head->appendChild($new_node);
            }
        }
    }

    public function render() 
    {
        $this->site = html_entity_decode($this->layout->saveXML(), ENT_QUOTES, 'UTF-8');
        //$this->site = $this->layout->saveHTML();
        //$this->site = html_entity_decode($this->layout->saveHTML(), ENT_QUOTES, 'UTF-8');
        //$this->site = urldecode($this->layout->saveHTML());
        return true;
    }
}

?>