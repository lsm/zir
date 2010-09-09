<?php
/**
 * Adjust settings below to satisfy your requirement
 */
// set error handling
error_reporting(E_ALL | E_STRICT);

// set timezone
date_default_timezone_set('UTC');

// define path for application, library etc.
define('ROOT_PATH', '/home/www/phpcosmos/oo.a.la/tb-tpl'); // use absolute path here
define('APP_PATH', ROOT_PATH . '/usr');
define('LIB_PATH', '/home/www/phpcosmos/zf/library');
define('ETC_PATH', ROOT_PATH . '/etc');
define('VAR_PATH', ROOT_PATH . '/var');
define('LOG_PATH', VAR_PATH . '/log');

// set which config set to use
define('CONFIG_SECTION', 'production');

// set error logging
ini_set('log_errors', 'On');
ini_set('log_errors_max_len', 4096);
ini_set('error_log', LOG_PATH . DIRECTORY_SEPARATOR . 'php_errors');

/**
 * Please do NOT modify things below here.
 */

//require_once(APP_PATH . '/boot.php');

/**
 * ATTENTION: Global boot script file, any changes may cause problem.
 */

// set for security reason
ini_set('magic_quotes_gpc', 'Off');
ini_set('register_globals', 'Off');

set_include_path('.' . PATH_SEPARATOR . LIB_PATH
. PATH_SEPARATOR . APP_PATH . '/models'
	 . PATH_SEPARATOR . get_include_path());

// Load necessary libraries.
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Registry');
//Zend_Loader::loadClass('Zend_Session');
//Zend_Loader::loadClass('Zend_Session_SaveHandler_DbTable');
Zend_Loader::loadClass('Zend_View');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
//Zend_Loader::loadClass('Zend_Filter_StripTags');
//Zend_Loader::loadClass('Zend_Filter_Alpha');
//Zend_Loader::loadClass('Zend_Auth');
//Zend_Loader::loadClass('Zend_Acl');
//Zend_Loader::loadClass('Zend_Acl_Resource');
//Zend_Loader::loadClass('Zend_Acl_Role');

// load configuration
$config = new Zend_Config_Ini(ETC_PATH . '/config.ini', CONFIG_SECTION);
//Zend_Registry::set('config', $config);

// setup database
$db = Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
$db->query('SET NAMES UTF8');
Zend_Db_Table::setDefaultAdapter($db);
//Zend_Registry::set('db', $db);

// setup session
//ini_set('session.name', $config->session->name);
//ini_set('session.hash_function', $config->session->hash_function);
//$sessionDbParams = array(
//'name' => 'sessions', //table name as per Zend_Db_Table
//'primary' => 'id', //the sessionID given by php
//'modifiedColumn' => 'modified', //time the session should expire
//'dataColumn' => 'data', //serialized data
//'lifetimeColumn' => 'lifetime' //end of life for a specific record
//);
//$sessionhandler = new Zend_Session_SaveHandler_DbTable($sessionDbParams);
//$sessionhandler->setLifetime($config->session->lifetime);
//Zend_Session::setSaveHandler($sessionhandler);
//$session = new Zend_Session_Namespace();
//Zend_Registry::set('session', $session);

//setup default exception handler
//Zend_Registry::set('debug',(boolean)$config->debug->exception);//need set this for handler excpetion
//set_exception_handler(array("Myngle_Sys","Error"));

// setup memcache
//$cached = Myngle_Cache::initialize($config);
//Zend_Registry::set('cached',$cached);

// setup view helper
//$view = new Myngle_View_Template();
//$view->setEncoding('UTF-8');
//Zend_Registry::set('view', $view);
//$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
//$viewRenderer->setViewBasePathSpec(APP_PATH . '/views')
//->setViewScriptPathSpec(':module/:controller/:action.:suffix')
//->setViewScriptPathNoControllerSpec(':action.:suffix')
//->setViewSuffix('tpl');
//Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

// setup controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions((boolean) $config->debug->exception);

// setup module directory
//$moduleDir = array(
//'default' => APP_PATH . '/default/controllers',
//'user' => APP_PATH . '/user/controllers',
//'course' => APP_PATH . '/course/controllers',
//'class' => APP_PATH . '/class/controllers',
//'school' => APP_PATH . '/school/controllers');
$frontController->setControllerDirectory(APP_PATH . '/controllers');


//// setup acl
//$auth = Zend_Auth::getInstance();
//Zend_Registry::set('auth', $auth);
//$acl = new Myngle_Acl($auth);
//Zend_Registry::set('acl', $acl);
//$frontController->registerPlugin(new Myngle_Controller_Plugin_Auth($auth, $acl))
//->registerPlugin(new Myngle_Controller_Plugin_PermissionCheck())
////->setParam('auth', $auth)
////->setParam('view', $view)
//;

// do dispatch
$frontController->dispatch();
?>