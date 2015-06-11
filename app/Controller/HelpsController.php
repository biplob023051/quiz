<?php

class HelpsController extends AppController {

	public $helpers = array('Quiz');

	public function admin_index($parent_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->set('title_for_layout',__('Helps List'));
		// find the main tile
		$this->set('parentsOptions', $this->Help->parentsOptions());
		if ($parent_id) {
			$conditions = array('Help.parent_id' => $parent_id, 'Help.type' => 'help');
		} else {
			$conditions = array('Help.parent_id != ' => null, 'Help.type' => 'help');
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
			
			$this->request->data['Help']['slug'] = $this->Help->makeSlug($this->request->data['Help']['slug'], $this->request->data['Help']['id']);
	
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
				$this->Session->setFlash(__('Help saved failed'), 'error_form', array(), 'error');
			}
		} elseif(!empty($help_id)) {
			$conditions = array(
				'Help.id' => $help_id,
				'Help.status'=> 1,
				'Help.type'=> 'help'
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
		$this->set('title_for_layout',__('Main Title List'));
		
		$options = array(
			'conditions' => array(
				'Help.parent_id' => null,
				'Help.type' => 'help'
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
				$this->Session->setFlash(__('Title saved failed'), 'error_form', array(), 'error');
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

	public function admin_create($help_id = null) {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		if(empty($help_id)){
			$this->set('title_for_layout', __('New Site Video'));
		} else {
			$this->set('title_for_layout', __('Edit Site Video'));
		}

		$this->set('siteOptions', $this->Help->siteOptions);
		
		if ($this->request->is(array('post','put'))) {
			if(empty($this->request->data['Help']['slug'])) {
				$this->request->data['Help']['slug']=$this->request->data['Help']['title'];
			}
			
			$this->request->data['Help']['slug'] = $this->Help->makeSlug($this->request->data['Help']['slug'], $this->request->data['Help']['id']);
	
			$this->request->data['Help']['user_id'] = $this->Auth->user('id');
			if (!empty($this->request->data['Help']['url'])) {
				$youtube = explode('?', $this->request->data['Help']['url']);
				$youtube = explode('=', $youtube[1]);
				$youtube = explode('&', $youtube[1]);	
				$this->request->data['Help']['url_src'] = $youtube[0];
			}

			if (empty($this->request->data['Help']['id']) && !empty($this->request->data['Help']['photo'])) {
                $newpath = WWW_ROOT . 'uploads' . DS . 'videos';
                if (!file_exists($newpath)) {
                    mkdir($newpath, 0777, true);
                }
                copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['Help']['photo'], $newpath . DS . $this->request->data['Help']['photo']);
                copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['Help']['photo'], $newpath . DS . 't_' . $this->request->data['Help']['photo']);

                unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['Help']['photo']);
                unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['Help']['photo']);
            }
			
			if ($this->Help->saveAll($this->request->data)) {
				$this->Session->setFlash(__('Site videos saved successfully'), 'notification_form', array(), 'notification');
				if(isset($this->params['url']['redirect_url'])){			
					return $this->redirect(urldecode($this->params['url']['redirect_url']));
				} else {
					return $this->redirect(array('controller' => 'helps', 'action' => 'videos', 'admin' => true));
				}
			} else {
				$this->Session->setFlash(__('Site videos saved failed'), 'error_form', array(), 'error');
			}
		} elseif(!empty($help_id)) {
			$conditions = array(
				'Help.id' => $help_id,
				'Help.status' => 1,
				'Help.type !=' => 'help'
			);
			
			if ($this->Help->hasAny($conditions)){				
				$options = array(
					'conditions'=>$conditions
				);
				$this->request->data=$this->Help->find('first',$options);
			} else {
				$this->Session->setFlash(__('Site videos not found'), 'error_form', array(), 'error');
				$this->redirect($this->referer());
			}

		}
		$lang_strings['upload_button'] = __('Upload a Picture');
		$this->set(compact('lang_strings'));
	}

	public function admin_videos() {
		if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
		$this->set('title_for_layout',__('Site Videos List'));
		// find the siteOptions
		$this->set('siteOptions', $this->Help->siteOptions);
		
		$conditions = array('Help.parent_id = ' => null, 'Help.type !=' => 'help');
		
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
			return $this->redirect(array('controller' => 'helps', 'action' => 'videos', 'admin' => true));
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
					$this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
			}	
		} else {
			$this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
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
					$this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
			}	
		} else {
			$this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
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
					'Help.status' => 1, 
					'Help.parent_id' => $parent_id,
					'Help.type' => 'help'
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