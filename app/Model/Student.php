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
        
    );

}
