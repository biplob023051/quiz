<?php

class StandardsController extends AppController {

	public $components = array('Paginator');
	public $paginate = array(
        'limit' => RESULT_LIMIT
    );

	// Method for displaying all standards
	public function admin_index() {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout', __('All Class'));
		$this->Paginator->settings = $this->paginate;
		$this->Paginator->settings['conditions'] = array(
			'Standard.is_del' => NULL,
			'Standard.type' => 1 
		);
	    $standards = $this->Paginator->paginate();
	    $this->set(compact('standards'));
	}

	// Method for active deactive standards
	public function admin_active($standard_id, $active=NULL) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		
		$conditions = array(
			'Standard.id' => $standard_id,
		);
		
		if ($this->Standard->hasAny($conditions)){
			$this->Standard->updateAll(
				array(
					'Standard.isactive' =>$active
				),
				$conditions
			);
			$this->Standard->afterSave(false);
			$message = empty($active) ? __('You have successfully deactivated!') : __('You have successfully activated');
			$this->Session->setFlash($message, 'success_form', array(), 'success');
		} else {
			$this->Session->setFlash(__('Can not save'), 'error_form', array(), 'error');
		}			
		
		if(isset($this->params['url']['redirect_url'])){			
			return $this->redirect(urldecode($this->params['url']['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
		}
	}

	/**
	* method of standard soft delete from admin
	*/
	public function admin_delete($standard_id) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->autoRender=false;
		
		$conditions = array(
			'Standard.id' => $standard_id,
			'Standard.isactive' => NULL,
			'Standard.is_del'=> NULL,
		);
		
		if ($this->Standard->hasAny($conditions)){
			$this->Standard->updateAll(
				array(
					'Standard.is_del' => 1
				),
				$conditions
			);
			$this->Standard->afterSave(false);
			$this->Session->setFlash(__('You have successfully deleted.'), 'success_form', array(), 'success');
		} else {
			$this->Session->setFlash(__('Can not delete'), 'error_form', array(), 'error');
		}

		if(isset($this->params['url']['redirect_url'])){			
			return $this->redirect(urldecode($this->params['url']['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
		}
		
	}

	/*
	* Method for standard create / edit
	*/
	public function admin_insert($standard_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		if(empty($standard_id)){
			$this->set('title_for_layout',__('New Class'));
		} else {
			$this->set('title_for_layout',__('Edit Class'));
		}
		
		if ($this->request->is(array('post','put'))) {
			if (empty($this->request->data['Standard']['id'])) {
				$this->request->data['Standard']['type'] = 1;
			}	
			if ($this->Standard->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Class saved successfully'), 'success_form', array(), 'success');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'standards', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->Session->setFlash(__('Class saved failed'), 'error_form', array(), 'error');
			}
		} elseif(!empty($standard_id)) {
			$conditions = array(
				'Standard.id' => $standard_id,
				'Standard.isactive' => 1,
				'Standard.is_del'=> NULL,
				'Standard.type' => 1
			);
			
			if ($this->Standard->hasAny($conditions)){				
				$options = array(
					'conditions'=>$conditions
				);
				$this->request->data=$this->Standard->find('first',$options);
			} else {
				$this->Session->setFlash(__('Class not found'), 'error_form', array(), 'error');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'standards', 'action' => 'index', 'admin' => true));
				}
			}

		}

	}

}