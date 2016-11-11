<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

    public $hasMany = array('Quiz', 'ImportedQuiz');
    public $validate = array(
        'name' => array(
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' =>  false,
                'allowEmpty'=> false,
                'message' => 'Name contains invalid character'
            )
        ),
        'email' => array(
            'email' => array(
                'rule' => 'email',
                'required' =>  false,
                'allowEmpty'=> false,
                'message' => 'Invalid email'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Email already registered'
            )
        ),
        'password' => array(
            'alphaNumeric' => array(
                'rule' => array('minLength', 8),
                'required' =>  false,
                'allowEmpty'=> false,
                'message' => 'Password must be 8 characters long'
            )
        ),
        'passwordVerify' => array(
            'identical' => array(
                'rule' => array('matchIdentical','password'),
                'message' => 'Password did not match, please try again',
                'required' =>  false,
                'allowEmpty'=> false,
            )
        )
    );

    /* function for checking match value of two fields */
    function matchIdentical($checkField,$compareField) {
        $value = array_values($checkField);
        $value = $value[0];
        return ($this->data[$this->alias][$compareField] == $value);
    }

    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = $this->encryptPassword($this->data[$this->alias]['password']);
        } else {
            unset($this->data[$this->alias]['password']);
        }
        return true;
    }

    public function encryptPassword($password) {
        $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
        return $passwordHasher->hash($password);
    }

    public function canCreateQuiz($userId = null) {

        if (is_null($userId))
            $userId = $this->id;

        $userCount = $this->find('count', array(
            'conditions' => array(
                'User.id' => $userId,
                'User.account_level' => array(1,22),
                'User.expired > NOW()'
            )
        ));

        if ($userCount < 1) {
            $quizCount = $this->Quiz->find('count', array(
                'conditions' => array(
                    'Quiz.user_id' => $userId
            )));
            return $quizCount < 1;
        } else {
            return true;
        }
    }

    public function getUser($id = null) {
        $user = $this->find('first', array(
            'conditions' => array(
                'User.id = ' => ($id === null ? $this->id : $id)
            ),
            'recursive' => -1
        ));
        return $user;
    }

    /*
    * Upgrade user status
    */
    public function upgrade_status($id) {
        $user = $this->find('first', array(
            'conditions' => array(
                'User.id' => $id
            ),
            'fields' => array('User.expired'),
            'recursive' => -1
        ));
        if (empty($user['User']['expired'])) {
            return false;
        } else {
            return true;
        }
    }

    /*
    * Genrate random text
    */
    public function randText($length=40){
        $random= "";
        srand((double)microtime()*1000000);
        $strset  = "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $strset.= "abcdefghijklmnpqrstuvwxyz";
        $strset.= "123456789";
        // Add the special characters to $strset if needed
        
        for($i = 0; $i < $length; $i++) {
            $random.= substr($strset,(rand()%(strlen($strset))), 1);
        }
        return $random;
    }

}
