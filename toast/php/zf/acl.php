<?php

// index.php

// Initialise configuration / environment
$config = new Zend_Config(new Zend_Config_Ini('../application/config/config.ini', 'live'));

// Create sitemap from .ini using structure from example
$sitemap = new Zend_Config(new Zend_Config_Ini('../application/config/sitemap.ini', 'live'));

// Create db object and enable/disable debugging
$db = Zend_Db::factory($config->db->connection, $config->db->asArray());
//...etc...

// Create auth object
$auth = Zend_Auth::getInstance();

// Create acl object
$acl = new MyAcl($auth); // see

// Create router and configure (LIFO order for routes)
$router = new Zend_Controller_RewriteRouter;
//...add rules...

// Create view and register objects
$view = new My_View;
//...init view...

$front = Zend_Controller_Front::getInstance();
$front->throwExceptions(true);
$front->setRouter($router)
->setDispatcher(new Zend_Controller_ModuleDispatcher())
->registerPlugin(new My_Plugin_Auth($auth, $acl))
->registerPlugin(new My_Plugin_Agreement($auth))
->registerPlugin(new My_Plugin_View($view))
->setControllerDirectory(array('default' => realpath('../application/controllers/default'),
'admin' => realpath('../application/controllers/admin')))
->setParam('auth', $auth)
->setParam('view', $view)
->setParam('config', $config)
->setParam('sitemap', $sitemap)
->dispatch();
?>

<?php

class MyAcl extends Zend_Acl
{
	public function __construct(Zend_Auth $auth)
	{
		parent::__construct();

		$roleGuest = new Zend_Acl_Role('guest');

		$this->add(new Zend_Acl_Resource('home'));
		$this->add(new Zend_Acl_Resource('news'));
		$this->add(new Zend_Acl_Resource('tutorials'));
		$this->add(new Zend_Acl_Resource('forum'));
		$this->add(new Zend_Acl_Resource('support'));
		$this->add(new Zend_Acl_Resource('admin'));

		$this->addRole(new Zend_Acl_Role('guest'));
		$this->addRole(new Zend_Acl_Role('member'), 'guest');
		$this->addRole(new Zend_Acl_Role('admin'), 'member');

		// Guest may only view content
		$this->allow('guest', 'home');
		$this->allow('guest', 'news');
		$this->allow('guest', 'tutorials');
		$this->allow('member', 'forum');
		$this->deny('member', 'forum', 'update'); // Remove specific privilege
		$this->allow('member', 'support');
		$this->allow('admin'); // unrestricted access

		// Add authoring ACL check
		$this->allow('member', 'forum', 'update', new MyAcl_Forum_Assertion($auth));
		// NOTE: Dependency on auth object to allow getIdentity() for authenticated user object
	}
}


class My_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	private $_auth;
	private $_acl;

	private $_noauth = array('module' => 'default',
	'controller' => 'login',
	'action' => 'index');

	private $_noacl = array('module' => 'default',
	'controller' => 'error',
	'action' => 'privileges');

	public function __construct($auth, $acl)
	{
		$this->_auth = $auth;
		$this->_acl = $acl;
	}

	public function preDispatch($request)
	{
		if ($this->_auth->hasIdentity()) {
			$role = $this->_auth->getIdentity()->getUser()->role;
		} else {
			$role = 'guest';
		}

		$controller = $request->controller;
		$action = $request->action;
		$module = $request->module;
		$resource = $controller;

		if (!$this->_acl->has($resource)) {
			$resource = null;
		}

		if (!$this->_acl->isAllowed($role, $resource, $action)) {
			if (!$this->_auth->hasIdentity()) {
				$module = $this->_noauth['module'];
				$controller = $this->_noauth['controller'];
				$action = $this->_noauth['action'];
			} else {
				$module = $this->_noacl['module'];
				$controller = $this->_noacl['controller'];
				$action = $this->_noacl['action'];
			}
		}

		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
	}
}


//Agreement.php
//Many sites choose to enforce a set of terms and conditions to access. This intercepting plugin simply checks the Zend_Auth identity method hasAgreement() (for the sake of demonstration lets just say this is a boolean property that has been set in the user table of the database). Again, this is only enacted if an identity exists, and the request is redirected to a specific agreement controller/action.

class MyPlugin_Agreement extends Zend_Controller_Plugin_Abstract
{
	private $_auth;

	private $_noagreement = array('module' => 'default',
	'controller' => 'login',
	'action' => 'agreement');

	public function __construct($auth)
	{
		$this->_auth = $auth;
	}

	public function preDispatch($request)
	{
		if ($request->controller != 'logout' && $this->_auth->hasIdentity()) {
			if (!$this->_auth->getIdentity()->getUser()->hasAgreement()) {
				$request->setModuleName($this->_noagreement['module']);
				$request->setControllerName($this->_noagreement['controller']);
				$request->setActionName($this->_noagreement['action']);
			}
		}
	}
}


//Login (Authentication)
//The act of authentication - in my app - all happens within a domain model - MyForm_Login. Using Matt Zandstra's excellent reference on Observers/Observable at zend.com as a starting point I have created a form object that extends the PEAR HTML_Quickform component to allow one or more observers to be added to the form and activated upon validation.
//
//The form is constructed (and auto-populated in my domain-specific instance with form elements like 'Username', 'Password' and a 'Remember Me' checkbox), then several observers are added to it.
//
//When a form validates, the observers are all notified and given an instance of the form values and the Zend_Auth instance. From there, it is simply a matter of checking the sanitised form values (we've applied our form filters, right? :) and passing them to a domain-specific Zend_Auth_Identity object to query the database, perform a lookup and then either spit out an error message or start the login session.
//
//The example below would also create a hypothetical log observer to record the login time, date, details, etc.
//
//BTW, in case you're wondering why the $view->render() isn't called, it's because I generally have a View_Plugin that's registered in the the Front_Controller that kicks in during dispatch shutdown. It allows me to incrementally add components/properties to the view as the dispatcher loops through all the application actions.
//
//
//LoginController.php
class LoginController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$auth = $this->getInvokeArg('auth');
		$view = $this->getInvokeArg('view');

		if ($auth->hasIdentity()) {
			$this->_redirect('/home/index'); // Already authenticated? Navigate away
		}

		$form = new MyForm_Login(); // creates all fields, adds filters, etc...
		$form->attach(new MyPlugin_Login_User($auth)); // Perform login of user identity
		$form->attach(new MyPlugin_Login_Log($auth)); // Perhaps log the event?

		if ($form->validate()) {
			$this->_redirect('/home/index');
		}

		// Render page
		$this->getInvokeArg('view')->title = 'Login';
		$this->getInvokeArg('view')->template = 'login/index.tpl';
		$this->getInvokeArg('view')->form = $form->render();
	}

	public function agreementAction()
	{
		$auth = $this->getInvokeArg('auth');
		$view = $this->getInvokeArg('view');

		$form = new MyForm_Agreement();
		$form->attach(new MyPlugin_Agreement_User($auth));

		if ($form->validate()) {
			$this->_redirect('/home/index');
		}

		// Render page
		$this->getInvokeArg('view')->title = 'Agreement';
		$this->getInvokeArg('view')->template = 'login/agreement.tpl';
		$this->getInvokeArg('view')->form = $form->render();
	}
}


//User.php

class MyPlugin_Login_User implements Observer
{
	function notify($form)
	{
		$auth = $this->_auth;
		$values = $form->exportValues();

		$adapter = new MyAuth_Adapter();
		$adapter->setUsername($values['username']);
		$adapter->setPassword($values['password']);

		try {
			$auth->authenticate($adapter);
		} catch (MyAuth_Adapter_Exception_Missing $e) {
			// Let form know that login has failed...
		} catch (MyAuth_Adapter_Exception_Locked $e) {
			// Let form know that login has failed...
		}

		if (!$auth->isAuthenticated()) {
			// Let form know that password was incorrect or your account is not active...
		}

		$identity = $auth->getIdentity();

		// Retrieve row of user info and store inside Identity object (including role!)
		$userTable = new MyUser_Table; // Instance of Zend_Db_Table or similar...
		$identity->setUser($userTable->find($identity->getIdentifier()));
	}
}


//Conclusions:
//-------------
//This is obviously an over-simplified example that attempts to address the challenges of the Acl/Auth components in relation to the MVC components.
//
//I believe this approach - though needing some further bulletproofing - demonstrates good practice and encourages the developer to think about logical and clean ways of separating the process of authentication. Some benefits are:-
//
//* You could easily drop in ACL rules in the one point and have a more complex and rich set of rules without needing any update to your controllers, relieving a lot of maintenance issues.
//* Processing user input happens only in a single point in an area of the application that makes it more natural for developers to understand and build upon. The filters and validation take place in a separate form model that can be replaced/updated without affecting any other portion of the process. Post-login business rules can be added without touching other plugins or the bootstrap.
//
//I hope this is useful! Would very much appreciate feedback - even if to say I'm doing it all wrong! :-) I'm still learning a lot from listening to the discussions on this list and I'm keen to find out how others approach this kind of layer in their own applications.