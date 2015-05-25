<?php

class Help extends AppModel {
    
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
        )
    );

    // list of active parent 
    public function parentsOptions() {
        $options = $this->find('list', array(
                            'conditions' => array(
                                $this->alias.'.status' => 1,
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