<?php

class Help extends AppModel {

    public $siteOptions;
    public function __construct($id = false , $table = null , $ds = null ){
        parent::__construct($id,$table,$ds);
        // initialize siteOptions constant
        $this->siteOptions = array('home' => __('Home Page'), 'create' => __('User Create Page'));
    }
    
    public $actsAs = array(
        'Tree'
    );
    
    public $validate = array(
        'title' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Title is required',
                'allowEmpty' => false,
                'required'   => false,
            )
        ),
        'parent_id' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Main title is required',
                'allowEmpty' => false,
                'required'   => false,
            )
        ),
        'url' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Youtube video url is required',
            ),
            'website'=>array(
                 'rule'      => 'url',
                 'message'   => 'Valid youtube video url is required',
            )
        ),
        'type' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Display page is required',
                'allowEmpty' => false,
                'required'   => false,
            )
        )
    );

    // list of active parent 
    public function parentsOptions() {
        $options = $this->find('list', array(
                            'conditions' => array(
                                $this->alias.'.status' => 1,
                                $this->alias.'.type' => 'help',
                                $this->alias.'.parent_id' => null
                            ),
                            'order' => array(
                                    $this->alias.'.lft'=>' DESC',
                                    $this->alias.'.rght'=>' ASC',
                                )
                        ));
        return $options;
    } 

}