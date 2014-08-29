<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 29.08.2014
 * Time: 14:53
 */


namespace App\Core;


use App\Helpers\FSHelper;
use App\Pixie;

class UploadedFile
{
    protected $loaded = false;

    protected $moved = false;

    protected $valid;

    protected $extension;

    protected $baseName;

    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $tmpName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $error;

    protected $params = [];

    protected $errors = [];


    public function __construct(Pixie $pixie, $name, array $params = [])
    {
        $this->pixie = $pixie;

        $this->params = array_merge_recursive([
            'extensions' => [],
            'types' => []
        ], $params);

        $fileData = $_FILES[$name];

        if ($fileData) {

            $this->name = $fileData['name'];
            $this->type = $fileData['type'];
            $this->tmpName = $fileData['tmp_name'];
            $this->error = $fileData['error'];
            $this->size = $fileData['size'];

            if ($fileData['tmp_name'] && !$fileData['error'] && file_exists($fileData['tmp_name'])) {
                $this->loaded = true;
            }
        }
    }

    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Move uploaded file to new position.
     * @param $path
     * @param bool $replace
     * @throws \Exception
     */
    public function move($path, $replace = true)
    {
        if (!$this->loaded) {
            throw new \Exception('File "' . $this->name . '" is not loaded, so it cannot be moved.');
        }

        if ($this->moved) {
            throw new \Exception('File "' . $this->name . '" is already moved');
        }

        if (file_exists($path)) {
            if ($replace) {
                unlink($path);
            } else {
                throw new \Exception('File on path "' . $this->name . '" exists already.');
            }
        }

        move_uploaded_file($this->tmpName, $path);
        $this->moved = true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getTmpName()
    {
        return $this->tmpName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return boolean
     */
    public function isMoved()
    {
        return $this->moved;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (!$this->loaded) {
            return false;
        }

        $vuln = $this->pixie->getVulnService()->getField('photo');
        if (is_array($vuln) && in_array('ArbitraryFileUpload', $vuln)) {
            return true;
        }

        $ext = $this->getExtension();
        if (!in_array($ext, $this->params['extensions'])) {
            return false;
        }

        if (count($this->params['types'])) {
            if (in_array('image', $this->params['types'])) {

                try {
                    $size = getimagesize($this->getTmpName(), $imageInfo);
                } catch (\Exception $e) {
                    $size = false;
                }

                if ($size === false || $size[0] == 0 || $size[1] == 0) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getExtension()
    {
        if (!$this->loaded) {
            return '';
        }

        if ($this->extension === null) {
            $this->prepareFileName();
        }

        return $this->extension === false ? '' : $this->extension;
    }

    public function getBaseName()
    {
        if (!$this->loaded) {
            return '';
        }

        if ($this->baseName === null) {
            $this->prepareFileName();
        }

        return $this->baseName === false ? '' : $this->baseName;
    }

    protected function prepareFileName()
    {
        if (preg_match('#(.*?)\.([\w\d_]{1,5})$#i', $this->name, $matches)) {
            $this->baseName = $matches[1];
            $this->extension = $matches[2];
        } else {
            $this->extension = false;
            $this->baseName = $this->name;
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}