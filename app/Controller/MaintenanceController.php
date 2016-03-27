<?php

class MaintenanceController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('notice');
    }

    public function notice() {
    	// Remove maintenance mode
    	$this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        $this->set('title_for_layout', __('Pardon for the dust!'));
        $this->render('/Elements/Maintenance/notice');
    }
    
}
