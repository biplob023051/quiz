<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('AJAXController', 'Controller');

class InvoiceController extends AJAXController {

    public $components = array('Auth');
    public $uses = array('User');

    public function create() {
        $user = $this->User->getUser($this->Auth->user('id'));

        if (empty($user))
            throw new NotFoundException;

        $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));

        if ($this->request->data['package'] == 29) {
            $package =  __('29 E/Y');
            $this->request->data['User']['account_level'] = 1;
        } else {
            $package = __('49 E/Y');
            $this->request->data['User']['account_level'] = 2;
        }
        
        // increate user account expired time
        $this->request->data['User']['id'] = $user['User']['id'];
        $this->request->data['User']['expired'] = $date;
        $this->User->save($this->request->data);

        $Email = new CakeEmail();
        //$Email->viewVars($user);
        $Email->viewVars(array('User' => $user['User'], 'package' => $package));
        $Email->from(array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi'));
        $Email->template('invoice');
        $Email->emailFormat('html');
        $Email->to(Configure::read('AdminEmail'));
        $Email->subject(__('Upgrade Account'));
        $Email->send();

        $this->set('data', array('success' => true));
    }
}