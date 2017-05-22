<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class BookmarksController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            //'contain' => ['Users']
			'conditions' => [
				'Bookmarks.user_id' => $this->Auth->user('id')
			]
        ];
        $bookmarks = $this->paginate($this->Bookmarks);
        $this->set(compact('bookmarks'));
        $this->set('_serialize', ['bookmarks']);
    }

    /**
     * View method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Users', 'Tags']
        ]);

        $this->set('bookmark', $bookmark);
        $this->set('_serialize', ['bookmark']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bookmark = $this->Bookmarks->newEntity();
        if ($this->request->is('post')) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
			$bookmark->user_id = $this->Auth->user('id'); //ADDED THIS HERE
            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('The bookmark has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The bookmark could not be saved. Please, try again.'));
        }
        // $users = $this->Bookmarks->Users->find('list', ['limit' => 200]); REMOVED THIS HERE
        $tags = $this->Bookmarks->Tags->find('list', ['limit' => 200]);
        $this->set(compact('bookmark', 'users', 'tags'));
        $this->set('_serialize', ['bookmark']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		if(empty($id)){
			$this->Flash->error(__('Bookmark not selected for editing.'));
			return $this->redirect(['action' => 'index']);
		}
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Tags']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
			$bookmark->user_id = $this->Auth->user('id');
            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('The bookmark has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The bookmark could not be saved. Please, try again.'));
        }
        //$users = $this->Bookmarks->Users->find('list', ['limit' => 200]);
        $tags = $this->Bookmarks->Tags->find('list', ['limit' => 200]);
        $this->set(compact('bookmark', 'users', 'tags'));
        $this->set('_serialize', ['bookmark']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookmark = $this->Bookmarks->get($id);
        if ($this->Bookmarks->delete($bookmark)) {
            $this->Flash->success(__('The bookmark has been deleted.'));
        } else {
            $this->Flash->error(__('The bookmark could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


	// Because of the Router::scope added to the routes.php file
	// we were able to connect the /bookmarks/tags/* URL to not just the BookmarksController.php
	// Controller class, but also to this tags() function.
	// Oh yeah, one last thing --- because the function is called "tags", this will map to a "tags.ctp" file in our view!
	public function tags() {

		// Getting all passed parameters from the URL
		$tags = $this->request->params['pass'];

		// Calls on the conventions-generated "Bookmarks" Table object in the $this object
		// Uses the find function with the 'tagged' argument to automatically try to invoke the findTagged() method of the Table object
		// and then, passes the request tags parameters
		$bookmarks = $this->Bookmarks->find('tagged', [
			'tags' => $tags, 'user_id' => $this->Auth->user('id')
		]);

		// This sets variables for the view for both bookmarks and tags objects
		// The compact() method is a shorthand way of writing
		// 'bookmarks' => $bookmarks,
		// 'tags' => $tags
		$this->set(compact('bookmarks', 'tags'));
	
	}


	// Okay, so this is basically what I'm getting so far as my understanding...
	// we added the 'authorize => Controller' line to the component loaded in AppsController, SPECIFICALLY to allow
	// for a new type of convention when it comes to authorizing Controller actions
	// In a nutshell, that line makes is so that we can add the function isAuthorized to our controllers, and determine on a controller
	// by controller basis the visibility of the different actions for the controller.
	public function isAuthorized($user)
	{
		// So here, we're checking what the currently running action is
		$action = $this->request->getParam('action');

		// We return true (i.e., the user is authorized) in the event that the Bookmarks action that's being performed is
		// index, add or tags
		// The add and index actions are always allowed.
		if (in_array($action, ['index', 'add', 'tags'])) {
			return true;
		}

		// All other actions require an id.
		if(!$this->request->getParam('pass.0')) {  // What the hell is pass.0???  Hell if I know, maybe just another convention
			return false;
		}

		// Check that the bookmark belongs to the current user.
		$id = $this->request->getParam('pass.0');
		$bookmark = $this->Bookmarks->get($id);
		if($bookmark->user_id == $user['id']) {
			return true;
		}

		return parent::isAuthorized($user);
	}
}























