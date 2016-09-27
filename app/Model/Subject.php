<?php

class Subject extends AppModel {
	public $displayField = 'title';

	public $validate = array(
        'title' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Title is required',
                'allowEmpty' => false,
                'required'   => false,
            ),
            'isUnique' => array(
		        'rule' => 'isUnique',
		        'message' => 'Subject already created'
		    )
        ),
    );
}