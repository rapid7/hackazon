<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 12:45
 */


namespace App\Installation\Step;


class EmailSettingsStep extends AbstractStep
{
    protected $template = 'installation/emailsettings';

    /**
     * @var string Possible variants: 'smtp', 'sendmail' or 'native'
     */
    protected $type = 'sendmail';

    // PHP Mail settings
    /**
     * @var string Defaults to "-f%s"
     */
    protected $mail_parameters;

    // Sendmail settings
    /**
     * @var string Defaults to "/usr/sbin/sendmail -bs"
     */
    protected $sendmail_command;

    // SMTP settings
    protected $hostname = 'localhost';

    protected $port = 25;

    protected $username;

    protected $password;

    protected $encryption;

    protected $timeout;

    protected $useExistingPassword;

    protected $defaultPassword;

    protected function processRequest(array $data = [])
    {
        $this->isValid = false;

        $this->type = $data['type'];

        if (!in_array($this->type, $this->getValidTypes())) {
            $this->errors[] = 'Please select correct type.';
            return false;
        }

        if ($this->type == 'native') {
            $this->mail_parameters = $data['mail_parameters'] ?: null;

        } else if ($this->type == 'sendmail') {
            $this->sendmail_command = $data['sendmail_command'] ?: null;

        } else if ($this->type == 'smtp') {
            $this->useExistingPassword = (boolean) $data['use_existing_password'];
            $this->hostname = $data['hostname'];
            $this->port = $data['port'];
            $this->username = $data['username'] ?: null;
            $this->password = $this->useExistingPassword ? $this->defaultPassword : $data['password'];
            $this->encryption = $data['encryption'] ?: null;
            $this->timeout = $data['timeout'] ?: null;

            if (!$data['hostname']) {
                $this->errors[] = 'Please enter hostname.';
            }

            if (!$data['port']) {
                $this->errors[] = 'Please enter port.';
            }

            if ($this->encryption && !in_array($this->encryption, ['ssl', 'tls'])) {
                $this->errors[] = 'Please enter correct encryption.';
            }
        }

        if (count($this->errors)) {
            return false;
        }

        $this->isValid = true;
        return true;
    }

    protected function persistFields()
    {
        return ['type', 'mail_parameters', 'sendmail_command', 'username', 'password', 'hostname', 'port',
            'encryption', 'timeout', 'useExistingPassword', 'defaultPassword'];
    }

    public function init()
    {
        $this->pixie->config->load_inherited_group('email');
        $config = $this->pixie->config->get_group('email');

        $this->hostname = $config['default']['hostname'];
        $this->port = $config['default']['port'];
        $this->username = $config['default']['username'];
        $this->password = $config['default']['password'];
        $this->encryption = $config['default']['encryption'];
        $this->timeout = $config['default']['timeout'];
        $this->type = $config['default']['type'];
        $this->mail_parameters = $config['default']['mail_parameters'];
        $this->sendmail_command = $config['default']['sendmail_command'];

        $this->defaultPassword = $this->password;
    }

    public function getViewData()
    {
        return [
            'hostname' => $this->hostname,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'encryption' => $this->encryption,
            'timeout' => $this->timeout,
            'type' => $this->type,
            'mail_parameters' => $this->mail_parameters,
            'sendmail_command' => $this->sendmail_command,
            'use_existing_password' => $this->useExistingPassword,
        ];
    }

    public function getValidTypes()
    {
        return ['smtp', 'sendmail', 'native'];
    }
} 