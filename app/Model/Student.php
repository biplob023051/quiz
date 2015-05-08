<?php

class Student extends AppModel {

    public $hasMany = array('Answer');
    public $hasOne = array('Ranking');
    public $belongsTo = array(
        'Quiz' => array(
            'counterCache' => true,
        )
    );
    public $validate = array(
        'fname' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'First name is required',
                'allowEmpty' => false,
                'required'   => false,
            ),
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Invalid Name'
            )
        ),
        'lname' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Last name is required',
                'allowEmpty' => false,
                'required'   => false,
            ),
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Invalid Name'
            )
        ),
        'class' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Class is required',
                'allowEmpty' => false,
                'required'   => false,
            ),
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Invalid Name'
            )
        )
    );

}
