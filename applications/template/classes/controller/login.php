<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller_Template {
	public $template = 'login';

	public function action_index()
	{
	if ($_POST['submit'] == 'Login')
		{
		if ($_POST['username'] == 'ehauge' && 
                    $_POST['pass'] == 'zaq12wsx')
			{
			$this->request->redirect('http://jhoppe.jhexperiment.com/index.php/image_upload');
			}
		}
	}

} // End Welcome
