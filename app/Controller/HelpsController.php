<?php

class HelpsController extends AppController {

	public function admin_index($parent_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->set('title_for_layout',__('Helps List'));
		// find the main tile
		$this->set('parentsOptions', $this->Help->parentsOptions());
		if ($parent_id) {
			$conditions = array('Help.parent_id' => $parent_id);
		} else {
			$conditions = array('Help.parent_id != ' => null);
		}
		$options = array(
			'conditions' => $conditions,
			'order' => array(
				'Help.lft'=>' DESC',
				'Help.rght'=>' ASC',
			)
		);
		
		try {
			$this->set('helps', $this->Help->find('all', $options));
		} catch (NotFoundException $e) { 
			// when pagination error found redirect to first page e.g. paging page not found
			return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
		}
	}

	public function admin_insert($help_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		if(empty($help_id)){
			$this->set('title_for_layout',__('New Help'));
		} else {
			$this->set('title_for_layout',__('Edit Help'));
		}

		$this->set('parentsOptions', $this->Help->parentsOptions());
		
		if ($this->request->is(array('post','put'))) {
			if(empty($this->request->data['Help']['slug'])) {
				$this->request->data['Help']['slug']=$this->request->data['Help']['title'];
			}
			
			$this->request->data['Help']['slug']=$this->Help->makeSlug($this->request->data['Help']['slug'], $this->request->data['Help']['id']);
	
			$this->request->data['Help']['user_id'] = $this->Auth->user('id');
			if (!empty($this->request->data['Help']['url'])) {
				$youtube = explode('?', $this->request->data['Help']['url']);
				$youtube = explode('=', $youtube[1]);
				$youtube = explode('&', $youtube[1]);	
				$this->request->data['Help']['url_src'] = $youtube[0];
			}
			
			if ($this->Help->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Help saved successfully'), 'notification_form', array(), 'notification');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->Session->setFlash(__('Help created failed'), 'error_form', array(), 'error');
			}
		} elseif(!empty($help_id)) {
			$conditions = array(
				'Help.id' => $help_id,
				'Help.status'=> 1
			);
			
			if ($this->Help->hasAny($conditions)){				
				$options = array(
					'conditions'=>$conditions
				);
				$this->request->data=$this->Help->find('first',$options);
			} else {
				$this->Session->setFlash(__('Help not found'), 'error_form', array(), 'error');
				$this->redirect($this->referer());
			}

		}

	}

	public function admin_titles() {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->set('title_for_layout',__('Main Titles List'));
		
		$options = array(
			'conditions' => array(
				'Help.parent_id' => null
			),
			'order' => array(
				'Help.lft'=>' DESC',
				'Help.rght'=>' ASC',
			)
		);
		
		try {
			$this->set('helps', $this->Help->find('all', $options));
		} catch (NotFoundException $e) { 
			// when pagination error found redirect to first page e.g. paging page not found
			return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
		}
	}

	public function admin_add($help_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		if(empty($help_id)){
			$this->set('title_for_layout',__('New Main Title'));
		} else {
			$this->set('title_for_layout',__('Edit Main Title'));
		}
		
		if ($this->request->is(array('post','put'))) {

			$this->request->data['Help']['user_id'] = $this->Auth->user('id');

			if ($this->Help->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Title saved successfully'), 'notification_form', array(), 'notification');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'helps', 'action' => 'titles', 'admin' => true));
				}
			} else {
				$this->Session->setFlash(__('Title created failed'), 'error_form', array(), 'error');
			}
		} elseif(!empty($help_id)) {
			$conditions = array(
				'Help.id' => $help_id,
				'Help.parent_id' => null
			);
			
			if ($this->Help->hasAny($conditions)){				
				$options = array(
					'conditions'=>$conditions
				);
				$this->request->data=$this->Help->find('first',$options);
			} else {
				$this->Session->setFlash(__('Title not found'), 'error_form', array(), 'error');
				$this->redirect($this->referer());
			}

		}

	}

	/**
	* method of help soft delete from admin
	*/
	public function admin_delete($help_id) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->autoRender=false;
		
		$conditions = array(
			'Help.id' => $help_id,
			'Help.status'=> 0,
		);
		
		if ($this->Help->hasAny($conditions)){
			$this->Help->delete($help_id);
		} else {
			$this->Session->setFlash(__('Can not delete'), 'error_form', array(), 'error');
		}		
			
		if(isset($this->params['url']['redirect_url'])){			
			return $this->redirect(urldecode($this->params['url']['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
		}
		
	}
	
	/**
	* method of help active/deactive from admin
	*/
	public function admin_active($help_id,$active=NULL) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->autoRender=false;
		
		$conditions = array(
			'Help.id' => $help_id,
		);
		
		if ($this->Help->hasAny($conditions)){
			$this->Help->updateAll(
				array(
					'Help.status' =>$active
				),
				$conditions
			);
			$this->Help->afterSave(false);
		} else {
			$this->Session->setFlash(__('Can not save'), 'error_form', array(), 'error');
		}			
		
		if(isset($this->params['url']['redirect_url'])){			
			return $this->redirect(urldecode($this->params['url']['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
		}
		
	}


	function  admin_moveup($help_id) {		
		$this->autoRender=false;
		
		$conditions = array(
			'Help.id' => $help_id
		);
		
		if ($this->Help->hasAny($conditions)){
			$options = array(
				'conditions'=>$conditions,
				'contain'=>array()
			);
			$help=$this->Help->find('first',$options);
			if($help){
				$this->Help->id=$help['Help']['id'];
				if($this->Help->moveDown()==false)
					$this->Session->setFlash(__('Can not sort'), 'error_form', array(), 'error');
			}	
		} else {
			$this->Session->setFlash(__('Can not sort'), 'error_form', array(), 'error');
		}			
			
		if(isset($this->params['url']['redirect_url'])){			
			return $this->redirect(urldecode($this->params['url']['redirect_url']));
		} else {
			return $this->redirect(array('action' => 'index'));
		}	
		
	}
	
	function admin_movedown($help_id) {
		$conditions = array(
			'Help.id' => $help_id
		);
		
		if ($this->Help->hasAny($conditions)){
			$options = array(
				'conditions'=>$conditions,
				'contain'=>array()
			);
			$help=$this->Help->find('first',$options);
			if($help){
				$this->Help->id=$help['Help']['id'];
				if($this->Help->moveUp()==false)
					$this->Session->setFlash(__('Can not sort'), 'error_form', array(), 'error');
			}	
		} else {
			$this->Session->setFlash(__('Can not sort'), 'error_form', array(), 'error');
		}		
			
		if(isset($this->params['url']['redirect_url'])){			
			return $this->redirect(urldecode($this->params['url']['redirect_url']));
		} else {
			return $this->redirect(array('action' => 'index'));
		}
	}

	public function index() {
		$this->set('title_for_layout',__('Help'));
		$helps = $this->Help->parentsOptions();
		foreach ($helps as $parent_id => $value) {
			$helps[$value] = $this->Help->find('all', array(
				'conditions' => array(
					'Help.status' => 1, 'Help.parent_id' => $parent_id
					),
				'order' => array(
						'Help.lft'=>' DESC',
						'Help.rght'=>' ASC',
					)
				)
			);
			unset($helps[$parent_id]);
		}
		$this->set(compact('helps'));
	}

}