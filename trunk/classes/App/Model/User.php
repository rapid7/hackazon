<?php

namespace App\Model;

class User extends \PHPixie\ORM\Model {

    public $table = 'tbl_users';
    public $id_field = 'id';

    public function GetUserByUsername($username) {
        return $this->pixie->db->query('select')
                                ->table($this->table)
                                ->where('username', $username)    
                                ->execute();  
    }
    
    public function RegisterUser($username, $password) {
        $hash = $this->pixie->auth->provider('password')->hash_password($password);
        $user = $this->pixie->orm->get('User');
        $user->username = $username;
        $user->password=$hash;
        $user->save();
    }
}