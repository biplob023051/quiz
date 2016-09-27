<?php

class SubjectsController extends AppController {

	public $components = array('Paginator');
	public $paginate = array(
        'limit' => RESULT_LIMIT
    );

	// Method for displaying all subjects
	public function admin_index() {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout', __('All Subjects'));
		$this->Paginator->settings = $this->paginate;
		$this->Paginator->settings['conditions'] = array(
			'Subject.is_del' => NULL
		);
	    $subjects = $this->Paginator->paginate();
	    $this->set(compact('subjects'));
	}

	// Method for active deactive subjects
	public function admin_active($subject_id, $active=NULL) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		
		$conditions = array(
			'Subject.id' => $subject_id,
		);
		
		if ($this->Subject->hasAny($conditions)){
			$this->Subject->updateAll(
				array(
					'Subject.isactive' =>$active
				),
				$conditions
			);
			$this->Subject->afterSave(false);
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
	* method of subject soft delete from admin
	*/
	public function admin_delete($subject_id) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->autoRender=false;
		
		$conditions = array(
			'Subject.id' => $subject_id,
			'Subject.isactive' => NULL,
			'Subject.is_del'=> NULL,
		);
		
		if ($this->Subject->hasAny($conditions)){
			$this->Subject->updateAll(
				array(
					'Subject.is_del' => 1
				),
				$conditions
			);
			$this->Subject->afterSave(false);
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
	* Method for subject create / edit
	*/
	public function admin_insert($subject_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		if(empty($subject_id)){
			$this->set('title_for_layout',__('New Subject'));
		} else {
			$this->set('title_for_layout',__('Edit Subject'));
		}
		
		if ($this->request->is(array('post','put'))) {			
			if ($this->Subject->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Subject saved successfully'), 'success_form', array(), 'success');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'subjects', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->Session->setFlash(__('Subject saved failed'), 'error_form', array(), 'error');
			}
		} elseif(!empty($subject_id)) {
			$conditions = array(
				'Subject.id' => $subject_id,
				'Subject.isactive' => 1,
				'Subject.is_del'=> NULL
			);
			
			if ($this->Subject->hasAny($conditions)){				
				$options = array(
					'conditions'=>$conditions
				);
				$this->request->data=$this->Subject->find('first',$options);
			} else {
				$this->Session->setFlash(__('Subject not found'), 'error_form', array(), 'error');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'subjects', 'action' => 'index', 'admin' => true));
				}
			}

		}

	}

}