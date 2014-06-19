<?php

namespace App\Model;

class User extends \PHPixie\ORM\Model {

    public $table = 'tbl_users';
    public $id_field = 'id';

    public function checkExistingUser($dataUser){
        if(iterator_count($this->getUserByUsername($dataUser['username'])) > 0 || iterator_count($this->getUserByEmail($dataUser['email']))>0 )
            return true;
        else
            return false;
    }


    protected  function getUserByUsername($username) {
        return $this->pixie->db->query('select')
                                ->table($this->table)
                                ->where('username', $username)    
                                ->execute();  
    }

    protected function getUserByEmail($email){
        return $this->pixie->db->query('select')
            ->table($this->table)
            ->where('email', $email)
            ->execute();
    }

    public function RegisterUser($dataUser) {
        $dataUser['password'] = $this->pixie->auth->provider('password')->hash_password($dataUser['password']);
        $dataUser['created_on'] = $dataUser['last_login'] = date('Y-m-d H:i:s');
        $this->pixie->orm->get('User')->values($dataUser)->save();
    }

    public function checkLoginUser($login){
        if (preg_match("/[a-z0-9_-]+(\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})/i", $login)){
            $user=$this->pixie->orm->get('User')->where('email',$login)->find();
            if($user->loaded())
                $login = $user->username;
        }
        return $login;
    }

    public function loadUserModel($login){
        $user = $this->pixie->orm->get('User')->where('username',$login)->find();
        if($user->loaded())
            return $user;
    }

    public function saveOAuthUser($username,$oauth_uid, $oauth_provider){
        $user = $this->pixie->orm->get('User');
        $user->username = $username;
        $user->oauth_provider = $oauth_provider;
        $user->oauth_uid = $oauth_uid;
        $user->created_on =  date('Y-m-d H:i:s');
        return $user->save();
    }

    public function getEmailData($email){
        $user = $this->pixie->orm->get('User')->where('email',$email)->find();

        if($user->loaded()){

            return array(
                'to' => $email,
                'from' => 'RobotHackazon@hackazon.com',
                'subject' => 'recovering password',
                'text' => 'Hello, '.$user->username.'.
Recovering link is here
http://hackazon.com/user/recover?username='.$user->username.'&recover='.$this->getTempPassword($user),
            );
        }
        return null;
    }

    private function getTempPassword($user){
        $arr = array(
            'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'q', 'r',
            's', 't', 'u', 'v', 'w', 'x',
            'y', 'z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '1', '2',
            '3', '4', '5', '6', '7', '8',
            '9', '0',
        );
        $password = "";
        for ($i = 0; $i < 32; $i++)
            $password .= $arr[rand(0, count($arr) - 1)];
        $user->recover_passw = md5($password);
        $user->save();
        if($user->loaded())
            return $password;
    }

    public function checkRecoverPass($username, $recover_passw){
        $user = $this->loadUserModel(substr($username,2));
            if($user && md5(substr($recover_passw,2)) === $user->recover_passw)
                return true;
            else
                return false;
    }

    public function changeUserPassword($username, $new_passw){
        $user = $this->loadUserModel(substr($username,2));
        if($user){
            $user->password = $this->pixie->auth->provider('password')->hash_password($new_passw);
            $user->recover_passw = null;
            $user->save();
            if($user->loaded())
                return true;
        }
        return false;
    }

}