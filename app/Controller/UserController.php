<?php

App::uses('CakeEmail', 'Network/Email');
class UserController extends AppController {

    public $components = array(
        'DebugKit.Toolbar',
        'Email'
    );

    public $helpers = array('Form');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'create', 'contact', 'password_recover', 'ajax_email_checking', 'reset_password', 'buy_create', 'ajax_user_checking', 'admin_access', 'confirmation', 'success');
    }

    public function admin_access() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                if ($this->Auth->user('account_level') != 51) {
                   $this->Auth->logout();
                   $this->Session->setFlash(__('Unauthorized access to this login area!'), 'error_form', array(), 'error');
                    return $this->redirect(array('controller' => 'maintenance', 'action' => 'notice', 'admin' => false));
                }
                // save statistics data
                $this->loadModel('Statistic');
                $arrayToSave['Statistic']['user_id'] = $this->Auth->user('id');
                $arrayToSave['Statistic']['type'] = 'user_login';
                $this->Statistic->save($arrayToSave);
                return $this->redirect(array('controller' => 'maintenance', 'action' => 'settings'));
            }

            $this->Session->setFlash($this->Auth->authError, 'error_form', array(), 'error');
        }
    }

    public function create() {
        // load MathCaptchaComponent on fly
        $site_language = Configure::read('Config.language');
        if ($site_language == 'fin') {
            $this->MathCaptcha = $this->Components->load('MathCaptcha');
        } else {
            $this->MathCaptcha = $this->Components->load('QuizCaptcha');
        }
    
        if ($this->request->is('post')) {
            // $this->request->data['User']['activation'] = $this->User->randText(16);
            // pr($this->request->data);
            // exit;
            if ($this->MathCaptcha->validate($this->request->data['User']['captcha'])) {
                $this->request->data['User']['account_level'] = 22;
                $this->request->data['User']['expired'] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));
                $this->request->data['User']['activation'] = $this->randText(16);
                $this->User->set($this->request->data);
                if ($this->User->validates()) {
                    $user = $this->User->save();
                    $this->Session->delete('UserCreateFormData');
                    // Send email to user for email confirmation
                    $user_email = $this->Email->sendMail($user['User']['email'], __('[Verkkotesti Signup] Please confirm your email address!'), $user, 'user_email');
                    // Send email to admin
                    $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create');
                    $this->Session->write('registration', true);
                    $this->redirect(array('action' => 'success'));
                } else {
                    $error = array();
                    foreach ($this->User->validationErrors as $_error) {
                        $error[] = $_error[0];
                    }
                    $this->Session->setFlash($error, 'error_form', array(), 'error');
                }
            } else {
                $this->Session->setFlash(__('The result of the calculation was incorrect. Please try again.'), 'error_form', array(), 'error');
            }
            $this->Session->write('UserCreateFormData', $this->request->data);
            return $this->redirect(array('action' => 'create'));
        } else {
            $this->set('captcha', $this->MathCaptcha->getCaptcha());
        }
        // language strings
        $lang_strings['empty_name'] = __('Require Name');
        $lang_strings['invalid_characters'] = __('Name contains invalid character');
        $lang_strings['empty_email'] = __('Require Email Address');
        $lang_strings['invalid_email'] = __('Invalid email');
        $lang_strings['unique_email'] = __('Email already registered');
        $lang_strings['empty_password'] = __('Require Password');
        $lang_strings['varify_password'] = __('Password did not match, please try again');
        $lang_strings['character_count'] = __('Password must be 8 characters long');
        $lang_strings['empty_captcha'] = __('Require Captcha');
        $this->set(compact('lang_strings'));

        // load video
        $this->loadModel('Help');
        $create_video = $this->Help->find('first', array(
            'conditions' => array(
                'Help.type' => 'create',
                'Help.status' => 1
            ),
            'order' => array('Help.id desc')
        ));
        $this->set(compact('create_video'));
    }

    public function success() {
        $this->set('title_for_layout', __('Registration Success'));
        if ($this->Session->check('registration')) {
            $this->Session->setFlash(__('Thanks for your registration!'), 'success_form', array(), 'success');
        } else {
            $this->Session->setFlash(__('No direct access to this page!'), 'error_form', array(), 'error');
            $this->redirect(array('action' => 'login'));
        }
    }

    public function confirmation($code = null) {
        if (empty($code)) {
            $this->Session->setFlash(__('No direct access to this page!'), 'error_form', array(), 'error');
            $this->redirect(array('action' => 'create'));
        }
        $response = explode('y-s', $code);
        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $response[0],
                'User.activation' => $response[1]
            ),
            'recursive' => -1
        ));
        if (empty($user)) {
            $this->Session->setFlash(__('This is embrassing, we didn\'t find you!'), 'error_form', array(), 'error');
            $this->redirect(array('action' => 'create'));
        }
        $this->User->id = $user['User']['id'];
        $this->User->saveField('activation', NULL);
        
        $user = $user['User'];
        if ($this->Auth->login($user)) {
            // save statistics data
            $this->loadModel('Statistic');
            $arrayToSave['Statistic']['user_id'] = $this->Auth->user('id');
            $arrayToSave['Statistic']['type'] = 'user_login';
            $this->Statistic->save($arrayToSave);

            $this->Session->setFlash(__('Registration success'), 'notification_form', array(), 'notification');
            return $this->redirect($this->Auth->redirectUrl());
        } else {
            $this->Session->setFlash($this->Auth->authError, 'error_form', array(), 'error');    
        }
    }

    public function login() {
        if ($this->Auth->user()) { // Redirect user if logged in already
            $this->redirect($this->Auth->redirectUrl());
        }
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                // save statistics data
                $this->loadModel('Statistic');
                $arrayToSave['Statistic']['user_id'] = $this->Auth->user('id');
                $arrayToSave['Statistic']['type'] = 'user_login';
                $this->Statistic->save($arrayToSave);
                return $this->redirect($this->Auth->redirectUrl());
            }

            $this->Session->setFlash($this->Auth->authError, 'error_form', array(), 'error');
        }
    }

    public function logout() {
        $this->Session->setFlash(__('You have logged out'), 'notification_form', array(), 'notification');
        return $this->redirect($this->Auth->logout());
    }

    public function settings() {
        $data = $this->request->data;
        $this->User->id = $this->Auth->user('id');

        if ($this->request->is('post')) {
            if (!empty($data['User']['subjects'])) {
                $data['User']['subjects'] = json_encode($data['User']['subjects']);
            } 
            $this->User->set($data);
            if (empty($data['User']['password'])) {
                $this->User->validator()->remove('password');
                unset($data['User']['password']);
            }
            if ($this->User->validates()) {
                $this->User->save();
                $this->Session->write('Auth.User.language', $data['User']['language']);
                $this->Session->write('Auth.User.name', $data['User']['name']);
                $this->Session->write('Auth.User.subjects', $data['User']['subjects']);
                $this->Session->setFlash(__('Settings has been saved'), 'success_form', array(), 'success');
                return $this->redirect(array('controller' => 'quiz'));
            } else {
                $error = array();
                foreach ($this->User->validationErrors as $_error) {
                    $error[] = $_error[0];
                }
                $this->Session->setFlash($error, 'error_form', array(), 'error');
                return $this->redirect(array('action' => 'settings'));
            }
        }

        $userPermissions = $this->userPermissions();
        $this->set(compact('userPermissions'));
        
        $data = $this->User->getUser();
        // pr($data);
        // exit;
        $data['canCreateQuiz'] = $this->User->canCreateQuiz();
        $this->loadModel('Subject');
        $data['subjects'] = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL,
                'Subject.type' => NULL
            )
        ));
        $this->set(compact('data'));
    }

    public function contact() {
        $Email = new CakeEmail();
        $Email->viewVars($this->request->data);
        $Email->from(array('admin@webquiz.fi' => 'WebQuiz.fi'));
        $Email->template('inquary');
        $Email->emailFormat('html');
        $Email->to(Configure::read('AdminEmail'));
        $Email->subject(__('General Inquary'));
        if ($Email->send()) {
            $this->Session->setFlash(__('Your email sent successfully'), 'notification_form', array(), 'notification');    
        } else {
            $this->Session->setFlash(__('Something went wrong, please try again later'), 'error_form', array(), 'error');
        }
        return $this->redirect($this->referer());
        
    }

    /*
    * Request for password recover
    */
    public function password_recover() {
        $this->set('title_for_layout', __('Password Recover'));
        if ($this->request->is('post')) {
            $this->User->unbindModelAll();
            $user = $this->User->findByEmail($this->request->data['User']['email']);
            $dataToSave['User']['reset_code'] = $this->User->randText(16);
            $dataToSave['User']['resettime'] = $this->User->getCurrentDateTime();
            $dataToSave['User']['id'] = $user['User']['id'];
            if ($this->User->save($dataToSave)) {
                $Email = new CakeEmail();
                $vairables['loginUrl'] = Router::url('/',true);
                $vairables['reset_code'] = $dataToSave['User']['reset_code']; 
                $Email->viewVars($vairables);
                $Email->from(array('admin@webquiz.fi' => 'WebQuiz.fi'));
                $Email->template('reset_password');
                $Email->emailFormat('html');
                $Email->to($this->request->data['User']['email']);
                $Email->subject(__('Reset password for your account on Verkkotesti'));
                if ($Email->send()) {
                    $this->Session->setFlash(__('Your request has been received, please check you email.'), 'notification_form', array(), 'notification');    
                } else {
                    $this->Session->setFlash(__('Something went wrong, please try again later'), 'error_form', array(), 'error');
                }
            } else {
                $this->Session->setFlash(__('Something went wrong, please try again later'), 'error_form', array(), 'error');
            }
            return $this->redirect(array('action' => 'password_recover'));
        } 

        $lang_strings['empty_email'] = __('Require Email Address');
        $lang_strings['invalid_email'] = __('Invalid email');
        $lang_strings['not_found_email'] = __('This email has not registered yet!');
        $this->set(compact('lang_strings'));
    }

    /* 
    * Email existance checking for password reset
    */
    public function ajax_email_checking() {
        $this->autoRender = false;
        $this->User->unbindModelAll();
        $user = $this->User->findByEmail($this->request->data['email']);
        if (empty($user)) {
            $response['success'] = false;
        } else {
            $response['success'] = true;
        }
        echo json_encode($response);
    }

    public function reset_password($reset_code) {
        $this->set('title_for_layout', __('Reset Password'));
        if (empty($reset_code)) {
            return $this->redirect('/');
        }
        $this->User->unbindModelAll();
        $user = $this->User->findByResetCode($reset_code);
        if (empty($user)) {
            throw new NotFoundException(__('Password Reset Link Expired.'));
        }
        if ($this->request->is(array('post', 'put'))){
            $this->request->data['User']['reset_code'] = NULL;
            $this->request->data['User']['resettime'] = NULL;
            if ($this->User->validates($this->request->data)) {
                if ($this->User->save($this->request->data)) {
                    $this->Session->setFlash(__('Your password has been successfully changed.'), 'notification_form', array(), 'notification');    
                    return $this->redirect(array('controller'=>'user', 'action'=>'login'));
                } else {
                    $this->Session->setFlash(__('Something went wrong, please try again later'), 'error_form', array(), 'error');
                }
            } else {
                $error = array();
                foreach ($this->User->validationErrors as $_error) {
                    $error[] = $_error[0];
                }
                $this->Session->setFlash($error, 'error_form', array(), 'error');
            }
        } else {
            unset($user['User']['password']);
            $this->request->data = $user;
            $lang_strings['empty_password'] = __('Require New Password');
            $lang_strings['varify_password'] = __('Password did not match, please try again');
            $lang_strings['character_count'] = __('Password must be 8 characters long');
            $this->set(compact('lang_strings'));
        }
    }

    public function buy_create() {
        $this->request->data['User']['activation'] = $this->randText(16);

        $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));

        if ($this->request->data['User']['package'] == 29) {
            $package =  __('29 E/Y');
            $this->request->data['User']['account_level'] = 1;
        } else {
            $package = __('49 E/Y');
            $this->request->data['User']['account_level'] = 2;
        }

        unset($this->request->data['User']['package']);
        $this->request->data['User']['expired'] = $date;

        // pr($this->request->data);
        // exit;

        $this->User->set($this->request->data);
        if ($this->User->validates()) {
            $user = $this->User->save();
            
            // Send email to user for email confirmation
            $user_email = $this->Email->sendMail($user['User']['email'], __('[Verkkotesti Signup] Please confirm your email address!'), $user, 'user_email');
            // Send email to admin
            $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create');
            
            // Send email for upgrade notice to the admin 
            $Email = new CakeEmail();
            $Email->viewVars(array('User' => $user['User'], 'package' => $package));
            $Email->from(array('admin@webquiz.fi' => 'WebQuiz.fi'));
            $Email->template('invoice');
            $Email->emailFormat('html');
            $Email->to(Configure::read('AdminEmail'));
            $Email->subject(__('Upgrade Account'));
            $Email->send();

            $this->Session->write('registration', true);
            $this->redirect(array('action' => 'success'));

                // $this->Session->setFlash(__('Registration success and we will contact you soon to upgrade your account'), 'notification_form', array(), 'notification');
                // return $this->redirect($this->Auth->redirectUrl());
            
        } else {
            $error = array();
            foreach ($this->User->validationErrors as $_error) {
                $error[] = $_error[0];
            }
            $this->Session->setFlash($error, 'error_form', array(), 'error');
            return $this->redirect('/');
        }
    }

     /* 
    * Email existance checking for new registration
    */
    public function ajax_user_checking() {
        $this->autoRender = false;
        $this->User->unbindModelAll();
        $user = $this->User->findByEmail($this->request->data['email']);
        if (empty($user)) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        echo json_encode($response);
    }
    
}
