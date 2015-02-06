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
use VulnModule\VulnerableField;

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

    protected $requestField;


    public function __construct(Pixie $pixie, $name, array $params = [])
    {
        $this->pixie = $pixie;

        $this->params = array_merge_recursive([
            'extensions' => [],
            'types' => []
        ], $params);

        $fileData = $_FILES[$name instanceof VulnerableField ? $name->getName() : $name];

        $this->requestField = $name;

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

        if ($this->requestField instanceof VulnerableField && $this->requestField->isVulnerableTo('ArbitraryFileUpload')) {
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

    /**
     * Upload file to given url
     * @param string $url Where to upload the file
     * @param null|string $fileName File name to be set on uploaded file on the remote server.
     * @return boolean|array Headers of the response
     * @throws \LogicException
     */
    public function upload($url, $fileName = null)
    {
        if (!$this->loaded) {
            throw new \LogicException('Can\'t upload missing file.');
        }

        $curlFile = $this->getCurlValue($this->tmpName, $this->type, $fileName ?: $this->name);

        //NOTE: The top level key in the array is important, as some APIs will insist that it is 'file'.
        $data = ['file' => $curlFile];

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_COOKIE => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true, //Request header
            CURLOPT_HEADER => true, //Return header
            CURLOPT_SSL_VERIFYPEER => false, //Don't verify server certificate
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_COOKIE => "SESSIONID_VULN_SITE=" . session_id(),
            CURLOPT_TRANSFERTEXT => false
        ];

        curl_setopt_array($ch, $options);

        $header = array('Content-Type: multipart/form-data');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($result, $header_size);
        curl_close($ch);

        if (!$result) {
            return false;
        }

        $headersParsed = $this->parseHeaders($body);

        return $headersParsed;
    }

    protected function parseHeaders($headers)
    {
        $lines = preg_split('/[\\n\\r]+/ims', $headers, -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        foreach ($lines as $line) {
            $parts = preg_split('/:\s*/', $line, 2, PREG_SPLIT_NO_EMPTY);
            $result[$parts[0]] = $parts[1];
        }

        return $result;
    }

    protected function getCurlValue($filename, $contentType, $postname)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $contentType, $postname);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename};filename=" . $postname;
        if ($contentType) {
            $value .= ';type=' . $contentType;
        }

        return $value;
    }

    public function generateFileName($prefix = '')
    {
        $ext = FSHelper::cleanFileName($this->getExtension());
        $photoName = ($prefix ?  $prefix.'_' : '')
            . FSHelper::cleanFileName($this->getBaseName()) . ($ext ? '.' . $ext : '');
        return $photoName;
    }
}