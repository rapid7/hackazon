<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 12:46
 */


namespace App\Installation\Step;


use App\Model\User;
use PHPixie\DB;

class ConfirmationStep extends AbstractStep
{
    protected $template = 'installation/confirmation';

    /**
     * @var array|AbstractStep[]
     */
    protected $previousSteps = [];

    /**
     * @var string
     */
    protected $configDir;

    /**
     * @var string
     */
    protected $bakDir;

    /**
     * @var bool
     */
    protected $canWrite = true;

    /**
     * @var string Version of config
     */
    protected $configVersion = '';

    /**
     * @var array
     */
    protected $configsToAdd = [];

    public function init()
    {
        $this->configVersion = time();
    }

    protected function persistFields()
    {
        return ['version'];
    }

    /**
     * @inheritdoc
     */
    protected function processRequest(array $data = [])
    {
        $this->collectPreviousSteps();
        $this->configDir = $this->pixie->root_dir.'assets/config';
        $this->bakDir = $this->configDir . '/bak/'.date('Y_m_d_H_i_s');

        try {
            $this->updateConfigs();

            // Ask user to manually needed configs.
            if (!$this->canWrite && count($this->configsToAdd)) {
                return false;
            }

            $this->installDB();

        } catch (\Exception $e) {
            $this->errors[] = 'Error ' . $e->getCode() . ': ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function getViewData()
    {
        $result = [];

        $prev = $this;
        while ($prev = $prev->getPrevStep()) {
            if ($prev->getName() == 'db_settings') {
                $result['database'] = $prev->getViewData();
            }
            if ($prev->getName() == 'email_settings') {
                $result['email'] = $prev->getViewData();
            }
        }

        $result['configsToAdd'] = $this->configsToAdd;

        return $result;
    }

    /**
     * Cleans DB and installs all schema and data from scratch.
     */
    protected function installDB()
    {
        $this->pixie->config->load_inherited_group('db');

        /** @var DB\Mysql\Connection $db */
        $db = $this->pixie->db->get();
        /** @var \PDO $conn */
        $conn = $db->conn;
        $conn->setAttribute(\PDO::ATTR_TIMEOUT, 300);

        $this->pixie->db->get()->execute("SET foreign_key_checks = 0;");
        //$this->view->subview = '';
        // Remove Foreign Keys
        $sql = "SELECT tc.TABLE_NAME `table`, tc.CONSTRAINT_NAME `fk` "
            . "FROM information_schema.TABLE_CONSTRAINTS tc "
            . "WHERE tc.CONSTRAINT_SCHEMA=(SELECT DATABASE()) AND tc.CONSTRAINT_TYPE='FOREIGN KEY'";

        $foreignKeys = $this->pixie->db->get()->execute($sql);

        foreach ($foreignKeys as $fk) {
            if ($fk != "") {
                $conn->exec("ALTER TABLE `{$fk->table}` DROP FOREIGN KEY `{$fk->fk}`;");
            }
        }

        //Remove tables
        $tables = $this->pixie->db->get()->execute("SELECT GROUP_CONCAT(table_name) as tbl FROM information_schema.tables  WHERE table_schema = (SELECT DATABASE())");
        $tblRemove = "";
        foreach ($tables as $table) {
            if ($table->tbl != "") {
                $tblRemove = "DROP TABLE IF EXISTS " . $table->tbl;
            }
        }

        if ($tblRemove != "")
            $this->pixie->db->get()->execute($tblRemove);

        // Install schema
        $dbScript = $this->pixie->root_dir . "database/db.sql";
        $conn->exec(file_get_contents($dbScript));

        // Install migrations
        foreach (scandir($this->pixie->root_dir . "database/migrations") as $file) {
            $file = $this->pixie->root_dir . "database/migrations/" . $file;
            if (is_file($file)) {
                $sqlContent = file_get_contents($file);
                if (strpos($sqlContent, '# IGNORE') !== 0) {
                    $conn->exec($sqlContent);
                }
            }
        }

        // Install demo data
        $demoScript = $this->pixie->root_dir . "database/demo_database.sql";
        $conn->exec(file_get_contents($demoScript));

        // Post-install scripts
        $pixie = $this->pixie;
        $db = $pixie->db;
        $dirIterator = new \DirectoryIterator($this->pixie->root_dir . "database/post_migration");
        /** @var \SplFileInfo $fileInfo */
        foreach ($dirIterator as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->isReadable()) {
                $ext = strtolower($fileInfo->getExtension());
                $filePath = $fileInfo->getRealPath();

                if ($ext == 'sql') {
                    $sqlContent = file_get_contents($filePath);

                    // Ignore files starting with '# IGNORE' comment
                    if (strpos($sqlContent, '# IGNORE') !== 0) {
                        $conn->exec($sqlContent);
                    }

                } else if ($ext == 'php') {
                    $runner = function () use ($filePath, $pixie, $db) {
                        include $filePath;
                    };
                    $runner();
                }
            }
        }
        $this->pixie->db->get()->execute("SET foreign_key_checks = 1;");

        //$params = $this->pixie->config->get('parameters');
        $adminCredentials = $this->previousSteps['admin_credentials']->getViewData();
        /** @var User $userModel */
        $userModel = $this->pixie->orm->get('User');
        $userModel->changeUserPassword('admin', $adminCredentials['password']);
    }

    /**
     * Writes new config to files.
     * @throws \Exception
     */
    private function updateConfigs()
    {
        $configDir = $this->configDir;
        $fileInfo = new \SplFileInfo($configDir);

        if (!$fileInfo->isDir()) {
            if (!mkdir($configDir, 0777, true)) {
                throw new \Exception("Please create config directory [$configDir] and give PHP full access to it.");
            }
        }

        $configDir = realpath($configDir);
        if (!$fileInfo->isWritable()) {
            $this->canWrite = false;
        }

        if (isset($this->previousSteps['admin_credentials'])) {
            $adminCredSettings = $this->previousSteps['admin_credentials']->getViewData();
            $this->pixie->config->load_inherited_group('parameters');
            $paramsConfig = $this->pixie->config->get_group('parameters');
            $paramsConfig['installer_password'] = $adminCredSettings['password'];
            $this->writeConfigFile($this->configDir . "/parameters.php", $paramsConfig);

        } else {
            $this->createOverriddenConfig('parameters');
        }

        $this->createOverriddenConfig('rest');

        // Update DB settings
        $dbConfig = $this->pixie->config->get('db');
        /** @var AbstractStep $dbSettings */
        $dbSettings = $this->previousSteps['db_settings'];
        $dbSettings = $dbSettings->getViewData();
        $dbConfig['default']['user'] = $dbSettings['user'];
        $dbConfig['default']['password'] = $dbSettings['password'];
        $dbConfig['default']['db'] = $dbSettings['db'];
        $dbConfig['default']['host'] = $dbSettings['host'];
        $dbConfig['default']['port'] = $dbSettings['port'];
        $dbConfig['default']['driver'] = 'PDOV';
        $dbConfig['default']['connection'] = 'mysql:host='.$dbSettings['host'].';port='.$dbSettings['port'].';dbname='.$dbSettings['db'];
        $this->writeConfigFile($configDir.'/db.php', $dbConfig);

        // Update email settings
        $dbConfig = $this->pixie->config->get('email');
        /** @var AbstractStep $dbSettings */
        $dbSettings = $this->previousSteps['email_settings'];
        $dbSettings = $dbSettings->getViewData();
        $dbConfig['default']['type'] = $dbSettings['type'];
        $dbConfig['default']['sendmail_command'] = $dbSettings['sendmail_command'];
        $dbConfig['default']['mail_parameters'] = $dbSettings['mail_parameters'];
        $dbConfig['default']['hostname'] = $dbSettings['hostname'];
        $dbConfig['default']['port'] = $dbSettings['port'];
        $dbConfig['default']['username'] = $dbSettings['username'];
        $dbConfig['default']['password'] = $dbSettings['password'];
        $dbConfig['default']['encryption'] = $dbSettings['encryption'];
        $dbConfig['default']['timeout'] = $dbSettings['timeout'];
        $this->writeConfigFile($configDir.'/email.php', $dbConfig);

        // Vuln configs
        $vulnSampleDir = $this->configDir . '/vuln.sample';
        $vulnTargetDir = $this->configDir . '/vuln';

        foreach (new \DirectoryIterator($vulnSampleDir) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getExtension() != 'php') {
                continue;
            }

            $configName = $fileInfo->getBasename();
            $targetFileName = $vulnTargetDir . '/' . $configName;
            if (file_exists($targetFileName) && is_file($targetFileName)) {
                continue;
            }

            try {
                copy($fileInfo->getPathname(), $targetFileName);

            } catch (\Exception $e) {
                $this->configsToAdd[$targetFileName] = file_get_contents($fileInfo->getPathname());
            }
        }
    }

    /**
     * Flattens chain of steps for simple using.
     */
    protected function collectPreviousSteps() {
        $this->previousSteps = [];
        $prev = $this;
        while ($prev = $prev->getPrevStep()) {
            $this->previousSteps[$prev->getName()] = $prev;
        }
    }

    /**
     * @param $fileName
     * @param $config
     */
    protected function writeConfigFile($fileName, $config)
    {
        if (!$this->canWrite) {
            $currentConfig = @include($fileName);
            if (!file_exists($fileName) || $config != $currentConfig) {
                $this->configsToAdd[$fileName] = $this->serializeConfig($config);
            }
            return;
        }
        if (file_exists($fileName) && is_file($fileName) && $this->checkBakDir()) {
            $bakDir = $this->bakDir;
            if (!file_exists($bakDir)) {
                mkdir($bakDir);
            }

            if (file_exists($bakDir) && is_writable($bakDir)) {
                copy($fileName, $bakDir.'/'.basename($fileName));
            }
        }
        file_put_contents($fileName, $this->serializeConfig($config));
    }

    /**
     * Serializes config array as PHP code
     * @param $config
     * @return string
     */
    protected function serializeConfig($config) {
        return "<?php\nreturn ".var_export($config, true).";\n";
    }

    /**
     * Just writes overridden config.
     * @param $groupName
     */
    public function createOverriddenConfig($groupName)
    {
        $this->pixie->config->load_inherited_group($groupName);
        $configData = $this->pixie->config->get_group($groupName);
        $this->writeConfigFile($this->configDir . "/{$groupName}.php", $configData);
    }

    /**
     * Ensures that BAK directory for old configs exists, or creates it.
     * @return bool
     */
    public function checkBakDir()
    {
        if (!$this->canWrite) {
            return false;
        }
        $bakDir = $this->configDir.'/bak';
        if (file_exists($bakDir) && is_dir($bakDir)) {
            return true;
        } else {
            if (is_writable($this->configDir)) {
                mkdir($bakDir);
                return is_writable($bakDir);
            }
        }
        return false;
    }
}