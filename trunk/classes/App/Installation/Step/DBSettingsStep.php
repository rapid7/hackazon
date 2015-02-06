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

    protected $port = 3306;

    protected $user;

    protected $password;

    protected $db;

    protected $createIfNotExists = false;

    protected $useExistingPassword;

    protected $defaultPassword;

    protected function processRequest(array $data = [])
    {
        $this->isValid = false;

        $this->useExistingPassword = (boolean) $data['use_existing_password'];
        $this->host = $data['host'];
        $this->port = $data['port'];
        $this->user = $data['user'];
        $this->password = $this->useExistingPassword ? $this->defaultPassword : $data['password'];
        $this->db = $data['db'];
        $this->createIfNotExists = $data['create_if_not_exists'];

        if (!$data['host']) {
            $this->errors[] = 'Please enter the host name.';
        }

        if (!$data['port']) {
            $this->errors[] = 'Please enter the port.';
        }

        if (!$data['user']) {
            $this->errors[] = 'Please enter the username.';
        }

        if (!$data['db']) {
            $this->errors[] = 'Please enter the DB name.';
        }

        if (count($this->errors)) {
            return false;
        }

        try {
            $dsn = "mysql:host={$this->host};port={$this->port}";
            // Try to connect
            $conn = new \PDO($dsn, $this->user, $this->password);

            $stmt = $conn->query('USE `'.$this->db.'`');

            if (!$stmt || $stmt->errorCode() > 0) {
                if ($this->createIfNotExists) {
                    $stmt = $conn->query("CREATE DATABASE `".$this->db."` COLLATE 'utf8_general_ci'");

                    if (!$stmt || $stmt->errorCode() > 0) {
                        throw new \Exception('Can\'t create database ' . $this->db);
                    }
                } else {
                    throw new \Exception('Can\'t connect to database ' . $this->db);
                }
            }


        } catch (\Exception $e) {
            $this->errors[] = "Error " . $e->getCode() . ": " . $e->getMessage();
            return false;
        }

        $this->isValid = true;
        return true;
    }

    protected function persistFields()
    {
        return ['host', 'port', 'user', 'password', 'db', 'createIfNotExists', 'useExistingPassword', 'defaultPassword'];
    }

    public function init()
    {
        $this->pixie->config->load_inherited_group('db');
        $config = $this->pixie->config->get_group('db');

        $this->host = $config['default']['host'];
        $this->port = $config['default']['port'];
        $this->user = $config['default']['user'];
        $this->password = $config['default']['password'];
        $this->db = $config['default']['db'];

        $this->defaultPassword = $this->password;
    }

    public function getViewData()
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'password' => $this->password,
            'db' => $this->db,
            'create_if_not_exists' => $this->createIfNotExists,
            'use_existing_password' => $this->useExistingPassword,
        ];
    }
} 