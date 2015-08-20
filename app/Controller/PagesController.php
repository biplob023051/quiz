<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow('display', 'index', 'contact');
    }

	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;



		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = __(Inflector::humanize($path[$count - 1]));
		}

		if ($this->request->params['pass'][0] == 'contact') {
			$lang_strings['empty_email'] = __('Require Email Address');
			$lang_strings['invalid_email'] = __('Invalid email');
        	$lang_strings['empty_message'] = __('Require Message');
        	$this->set(compact('lang_strings'));
		}

		if (($this->request->params['pass'][0] == '1bgfg9sq') || ($this->request->params['pass'][0] == '4bgfg9sq') || ($this->request->params['pass'][0] == '5bgfg9sq') || ($this->request->params['pass'][0] == '9bgfg9sq')) {
			$lang_strings['empty_name'] = __('Require Name');
			$lang_strings['invalid_characters'] = __('Name contains invalid character');
			$lang_strings['empty_email'] = __('Require Email Address');
			$lang_strings['invalid_email'] = __('Invalid email');
        	$lang_strings['unique_email'] = __('Email already registered');
        	$lang_strings['empty_password'] = __('Require Password');
            $lang_strings['varify_password'] = __('Password did not match, please try again');
            $lang_strings['character_count'] = __('Password must be 8 characters long');
        	$this->set(compact('lang_strings'));
		}

		$this->set('current_page', $this->request->params['pass'][0]);
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	public function index() {
		$this->loadModel('Help');
		$home_video = $this->Help->find('first', array(
			'conditions' => array(
				'Help.type' => 'home',
				'Help.status' => 1
			),
			'order' => array('Help.id desc')
		));
		$this->set(compact('home_video'));
		$this->set('title_for_layout', __('Welcome to Verkkotesti'));
	}
}
