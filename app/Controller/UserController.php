<?php

App::uses('CakeEmail', 'Network/Email');
class UserController extends AppController {

    public $components = array(
        'DebugKit.Toolbar'
    );

    public $helpers = array('Form');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'create', 'contact', 'password_recover', 'ajax_email_checking', 'reset_password', 'buy_create', 'ajax_user_checking');
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
            if ($this->MathCaptcha->validate($this->request->data['User']['captcha'])) {
                $this->User->set($this->request->data);
                if ($this->User->validates()) {
                    $this->User->save();
                    $this->Session->delete('UserCreateFormData');
                    // auto login of the newly registered user to the site
                    if ($this->Auth->login()) {
                        // save statistics data
                        $arrayToSave['Statistic']['logged_user_id'] = $this->Auth->user('id');
                        $arrayToSave['Statistic']['type'] = 'user_login';
                        $this->User->Statistic->save($arrayToSave);
                        $this->Session->setFlash(__('Registration success'), 'notification_form', array(), 'notification');
                        return $this->redirect($this->Auth->redirectUrl());
                    } else {
                        $this->Session->setFlash($this->Auth->authError, 'error_form', array(), 'error');    
                    }
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
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                // save statistics data
                $arrayToSave['Statistic']['logged_user_id'] = $this->Auth->user('id');
                $arrayToSave['Statistic']['type'] = 'user_login';
                $this->User->Statistic->save($arrayToSave);
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
            $this->User->set($data);
            
            if (empty($data['User']['password'])) {
                $this->User->validator()->remove('password');
                unset($data['User']['password']);
            }

            if ($this->User->validates()) {
                $this->User->save();
                $this->Session->write('Auth.User.language', $data['User']['language']);
                $this->Session->write('Auth.User.name', $data['User']['name']);
                $this->Session->setFlash(__('Settings has been saved'), 'notification_form', array(), 'notification');
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
        $data = $this->User->getUser();
        $data['canCreateQuiz'] = $this->User->canCreateQuiz();
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
        $this->User->set($this->request->data);
        if ($this->User->validates()) {
            $this->User->save();
            // auto login of the newly registered user to the site
            if ($this->Auth->login()) {
                // save statistics data
                $arrayToSave['Statistic']['logged_user_id'] = $this->Auth->user('id');
                $arrayToSave['Statistic']['type'] = 'user_login';
                $this->User->Statistic->save($arrayToSave);

                // send upgrade email request
                $user = $this->User->getUser($this->Auth->user('id'));

                if (empty($user))
                    throw new NotFoundException;

                $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5));

                // increate user account expired time
                $this->User->id = $user['User']['id'];
                $this->User->saveField('expired', $date);

                $Email = new CakeEmail();
                $Email->viewVars($user);
                $Email->from(array('admin@webquiz.fi' => 'WebQuiz.fi'));
                $Email->template('invoice');
                $Email->emailFormat('html');
                $Email->to(Configure::read('AdminEmail'));
                $Email->subject(__('Upgrade Account'));
                $Email->send();

                $this->Session->setFlash(__('Registration success and we will contact you soon to upgrade your account'), 'notification_form', array(), 'notification');
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash($this->Auth->authError, 'error_form', array(), 'error');    
            }
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
