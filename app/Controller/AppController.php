<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = array(
        'DebugKit.Toolbar',
        'Session',
        'Cookie',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'quiz',
                'action' => 'index'
            ),
            'logoutRedirect' => array(
                'controller' => 'user',
                'action' => 'login'
            ),
            'loginAction' => array(
                'controller' => 'user',
                'action' => 'login'
            ),
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'User',
                    'scope' =>  array(
                        'User.activation' => null
                    ),
                    'fields' => array('username' => 'email'),
                    'passwordHasher' => array(
                        'className' => 'Simple',
                        'hashType' => 'sha256'
                    )
                )
            )
        )
    );

    public function beforeFilter() {
        
        // //default cookie seetings
        // $this->Cookie->name = 'VERKKOTESTI';
        // $this->Cookie->time = 3600;  // or '1 hour'
        // $this->Cookie->path = '/';
        // $this->Cookie->domain = false;
        // $this->Cookie->secure = false;
        // $this->Cookie->httpOnly = true;
        
        $setting = $this->_getSettings();
        $this->set(compact('setting'));
       
        if (!empty($setting['offline_status'])) {
            if (($this->request->action != 'logout') && ($this->request->action != 'admin_access') && ($this->request->action != 'notice') && ($this->Auth->user('account_level') != 51)) {
                $this->redirect(array('controller' => 'maintenance', 'action' => 'notice'));
            }
        } 

        // check user language, default language finish
        $language = $this->Auth->user('language');
        if (empty($language) or !file_exists(APP . 'Locale' . DS . $language . DS . 'LC_MESSAGES' . DS . 'default.po'))
            $language = 'fin';
        Configure::write('Config.language', $language);
        if ($this->Session->check('Choice') && ($this->request->action != 'removeChoice' || $this->request->action != 'isAuthorized')) {
            $this->Session->delete('Choice');
        }
    }

    // check account expiration
    public function accountStatus() {
        $this->loadModel('User');
        $c_user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id'),
            ),
            'recursive' => -1
        ));
        $access = false;
        if (!empty($c_user['User']['expired'])) {
            $days_left = floor((strtotime($c_user['User']['expired'])-time())/(60*60*24));
        }

        // For admin role 51 always true
        // For paid users role 1 check expire date
        // For unpaid old user role 0, always true
        // For unpaid new user, check 30 days of expire 
        if ($c_user['User']['account_level'] == 51) { // for admin
            $access = true;
        } elseif(($c_user['User']['account_level'] == 1) && ($days_left >= 0)) { // for paid users
            $access = true;
        } elseif($c_user['User']['account_level'] == 22) { // if new user unpaid 
            $days_left_created = floor((strtotime($c_user['User']['created'])-time())/(60*60*24));
            if ($days_left_created >= -30) {
                $access = true;
            }
            
        } elseif($c_user['User']['account_level'] == 2) { // if new user unpaid 
            $access = true;
        } elseif($c_user['User']['account_level'] == 0) { // for old user
            $access = true;
        }

        if (empty($access)) {
            $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        }
    }


    // Method for accessing of quiz bank
    public function hasQuizBankAccess() {
        $this->loadModel('User');
        $c_user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id'),
            ),
            'recursive' => -1
        ));

        if (!in_array($c_user['User']['account_level'], array(2, 22, 51))) {
            if ($this->request->is('ajax')) {
                echo $this->render('/Elements/no_permission_modal');
                exit;
            } else {
                $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
            }
        }
    }

    // check user status
    public function userPermissions() {
        $this->loadModel('User');
        $c_user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id'),
            ),
            'recursive' => -1
        ));
        $access = false;
        $canCreateQuiz = false;
        $request_sent = false;
        $permissions = array(
            'access' => false,
            'canCreateQuiz' => false,
            'upgraded' => false,
            'request_sent' => false,
            'days_left' => 0
        );
        if (!empty($c_user['User']['expired'])) {
            $days_left = floor((strtotime($c_user['User']['expired'])-time())/(60*60*24));
        } else {
            $days_left = 365; // always acccess for old unpaid users
        }
        // For admin role 51 always true
        // For paid users role 1 check expire date
        // For unpaid old user role 0, always true
        // For unpaid new user, check 30 days of expire 
        if ($c_user['User']['account_level'] == 51) { // for admin
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = true;
            $permissions['upgraded'] = true;
            $permissions['quiz_bank_access'] = true;
        } elseif(($c_user['User']['account_level'] == 1) && ($days_left >= 0)) { // for paid users
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = true;
            $permissions['upgraded'] = true;
        } elseif($c_user['User']['account_level'] == 22) { // if new user unpaid 
            if ($days_left > 30) { // if days left greater than 30 then upgrade request sent
                $permissions['request_sent'] = true;
            }
            $days_left_created = floor((strtotime($c_user['User']['created'])-time())/(60*60*24));

            if ($days_left_created >= -30) {
                $permissions['access'] = true;
                $permissions['canCreateQuiz'] = true;
                $permissions['quiz_bank_access'] = true;
            }
            
        } elseif($c_user['User']['account_level'] == 2) { // if new user unpaid 
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = true;
            $permissions['upgraded'] = true;
            $permissions['quiz_bank_access'] = true;
        } elseif($c_user['User']['account_level'] == 0) { // for old user
            $this->loadModel('Quiz');
            $quiz = $this->Quiz->find('first', array(
                'conditions' => array(
                    'Quiz.user_id' => $this->Auth->user('id')
                ),
                'recursive' => -1
            ));
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = empty($quiz) ? true : false;
            if (!empty($c_user['User']['expired'])) {
                $permissions['request_sent'] = true;
            }
        }
        $permissions['days_left'] = $days_left;
        return $permissions;
    }

    // Method for random string generate
    public function randText($length=40){
        $random= "";
        srand((double)microtime()*1000000);
        $strset  = "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $strset.= "abcdefghijklmnpqrstuvwxyz";
        $strset.= "123456789";
        // Add the special characters to $strset if needed
        
        for($i = 0; $i < $length; $i++) {
            $random.= substr($strset,(rand()%(strlen($strset))), 1);
        }
        return $random;
    }

    /**
     * Get global site settings
     * @return array
     */
    public function _getSettings() {
        $this->loadModel('Setting');
        $this->Setting->cacheQueries = true;
        $settings = $this->Setting->find('list', array('fields' => array('field', 'value')));
        return $settings;
    }
    
}
