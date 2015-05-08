<?php

class UserController extends AppController {

    public $components = array(
        'DebugKit.Toolbar'
    );

    public $helpers = array('Form');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'create');
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
                $this->Session->setFlash('The result of the calculation was incorrect. Please, try again.', 'error_form', array(), 'error');
            }
            $this->Session->write('UserCreateFormData', $this->request->data);
            return $this->redirect(array('action' => 'create'));
        } else {
            $this->set('captcha', $this->MathCaptcha->getCaptcha());
        }
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login())
                return $this->redirect($this->Auth->redirectUrl());

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
    
}
