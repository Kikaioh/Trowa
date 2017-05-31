<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

		/*	Here, in the initialize() method, we can load "components", which are basically chunks of library Controller code
			that make development so much easier.
			We're loading the 'Auth' library, since that's used for login authentication for general web applications.
		*/
		$this->loadComponent('Auth', [
			// There's three parts to loading the Auth component
			
			// First, we need to specify what sort of form fields are used for authentication
			// We're specifying the email and password fields for that here.
			'authorize' => 'Controller',
			'authenticate' => [
				'Form' => [
					'fields' => [
						'username' => 'email',
						'password' => 'password'
					]
				]
			],

			// Next, we need to specify the controller and action that are responsible for the login process
			'loginAction' => [
				'controller' => 'Users',
				'action' => 'login'
			],

			// Finally we can specify where the login should go if the person isn't authorized to view a page
			'unauthorizedRedirect' => $this->referer() // If unauthorized, return them to page they were at
		]);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }

		// Login Check
		// So this is basically going to allow us to set a variable that we add to the session that determines
		// whether a user is logged in or not.
		// Since the AppController class gets called first, since it's the parent of all the other controllers
		// this basically gets run before the page displays. So the beforeRender() function is a great place
		// to do a check like this!  I guess!
		if($this->request->session()->read('Auth.User')){
			$this->set('loggedIn', true);
		} else {
			$this->set('loggedIn', false);
		}
    }

	public function isAuthorized($user)
	{
		return false;
	}
}
