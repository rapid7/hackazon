<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 11:27
 */


namespace App\Installation\Step;


class DBSettingsStep extends AbstractStep
{
    protected $template = 'installation/dbsettings';

    protected $host;

    protected $user;

    protected $password;

    protected $db;

    protected function processRequest(array $data = [])
    {
        $this->isValid = false;

        $this->host = $data['host'];
        $this->user = $data['user'];
        $this->password = $data['password'];
        $this->db = $data['db'];

        if (!$data['host']) {
            $this->errors[] = 'Please enter host name.';
        }

        if (!$data['user']) {
            $this->errors[] = 'Please enter username.';
        }

        if (!$data['db']) {
            $this->errors[] = 'Please enter DB name.';
        }

        if (count($this->errors)) {
            return false;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db}";
            $conn = new \PDO($dsn, $this->user, $this->password);

        } catch (\PDOException $e) {
            $this->errors[] = "Error " . $e->getCode() . ": " . $e->getMessage();
            return false;
        }

        $this->isValid = true;
        return true;
    }

    protected function persistFields()
    {
        return ['host', 'user', 'password', 'db'];
    }

    public function init()
    {
        $this->pixie->config->load_inherited_group('db');
        $config = $this->pixie->config->get_group('db');

        $this->host = $config['default']['host'];
        $this->user = $config['default']['user'];
        $this->password = $config['default']['password'];
        $this->db = $config['default']['db'];
    }

    public function getViewData()
    {
        return [
            'host' => $this->host,
            'user' => $this->user,
            'password' => $this->password,
            'db' => $this->db,
        ];
    }
} 