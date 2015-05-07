<?php

App::uses('Controller', 'Controller');

class AJAXController extends Controller {

    public function beforeFilter() {
        if(!$this->request->isAjax())
            throw new NotFoundException();
        
        $this->layout = 'ajax';
    }
    
    public function beforeRender()
    {
        $this->viewPath = 'AJAX';
        $this->view = 'json';
    }

}
