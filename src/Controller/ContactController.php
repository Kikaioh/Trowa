<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\ContactForm;

class ContactController extends AppController
{
	public function index()
	{
		$contact = new ContactForm();

		// So, if we're loading the contact form page after the form has been submitted, you know, process the form
		if($this->request->is('post')) {
			if($contact->execute($this->request->getData())){
				$this->Flash->success('We will get back to you soon');
			} else {
				$this->Flash->error('There was a problem submitting your form.');
			}
		}
		
		if($this->request->is('get')){
			// Values from the User model e.g.
			$this->request->data('name','John Doe');
			$this->request->data('email','john.doe@example.com');
		}

		$errors = $contact->errors();
		var_dump($errors);

		$this->set(compact('contact'));
	}

	public function isAuthorized($user){
		return true;
	}
}


?>
