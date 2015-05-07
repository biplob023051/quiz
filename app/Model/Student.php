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
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Invalid Name'
            )
        ),
        'lname' => array(
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Invalid Name'
            )
        ),
        'class' => array(
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Invalid Name'
            )
        )
    );

}
