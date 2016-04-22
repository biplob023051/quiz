<?php
class cake_sessions extends AppModel{
    public function beforeSave($options = array()){
    }

    public function getActiveUsers(){
        $minutes = 10; // Conditions for the interval of an active session
        $sessionData = $this->find('all',array(
            'conditions' => array(
                //'expires >=' => time() - ($minutes * 1) // Making sure we only get recent user sessions
            )
        ));
        

      $activeUsers = array();
      foreach($sessionData as $session) {
          $data = $session['cake_sessions']['data'];
          pr($data);
          // Clean the string from unwanted characters
          $data = str_replace('Config','',$data);
          pr($data);
          $data = str_replace('Message','',$data);
          pr($data);
          $data = str_replace('Auth','',$data);
          pr($data);
              $data = substr($data, 1); // Removes the first pipe, don't need it
              pr($data);

          // Explode the string so we get an array of data
          $data = explode('|',$data);

          // Unserialize all the data so we can use it
          $auth = unserialize($data[2]);
          pr($auth);
exit;
          // Check if we are dealing with a signed-in user
          if(!isset($auth['User']) || is_null($auth['User']['id'])) continue;

          /* Because a user session contains all the data of a user 
               * (except the password), I will only return the User id 
               * and the first and last name of the user */

          /* First check if a user id hasn't already been saved 
               * (can happen because of multiple sign-ins on different 
               * browsers / computers!) */
          pr($activeUsers);
          exit;

          if(!in_array($auth['User']['id'], $activeUsers)) {
              // $activeUsers[$auth['User']['id'] = array(
              //   'first_name' => $auth['User']['first_name'], 
              //   'last_name' => $auth['User']['last_name']
              // ); 

               /* Keep in mind, your User table needs to contain 
                * a first- and lastname to return them. If not, 
                * you could use the email address or username 
                * instead of this data. */

          }
      }
      return $activeUsers;
    }
}