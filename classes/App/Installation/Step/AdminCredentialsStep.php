<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 11:27
 */


namespace App\Installation\Step;


class AdminCredentialsStep extends AbstractStep
{
    protected $template = 'installation/admin_credentials';

    protected $password;

    protected function processRequest(array $data = [])
    {
        $this->isValid = false;

        $this->password = trim($data['password']);

        if (!$this->password || $this->password != trim($data['password_confirmation'])) {
            $this->errors[] = 'Password is missing or passwords don\'t match.';
        }

        if (count($this->errors)) {
            return false;
        }

        $this->isValid = true;
        return true;
    }

    protected function persistFields()
    {
        return ['password'];
    }

    public function init()
    {
    }

    public function getViewData()
    {
        return [
            'errors' => $this->errors,
            'step' => $this,
            'password' => $this->password
        ];
    }
} 