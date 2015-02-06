<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 15.09.2014
 * Time: 15:29
 */


namespace App\Helpers;


use App\Core\UploadedFile;
use App\Model\File;
use App\Model\User;
use App\Pixie;

/**
 * Class UserPictureUploader
 * @package App\Helpers
 */
class UserPictureUploader
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var \App\Model\User
     */
    protected $user;

    /**
     * @var UploadedFile
     */
    protected $picture;

    /**
     * @var bool
     */
    protected $removeOld;

    protected $processed = false;

    protected $modifyUser = true;

    protected $result = '';

    public function __construct(Pixie $pixie, User $user, UploadedFile $picture, $removeOld = true)
    {
        $this->pixie = $pixie;
        $this->user = $user;
        $this->picture = $picture;
        $this->removeOld = (boolean) $removeOld;
    }

    public static function create(Pixie $pixie, User $user, UploadedFile $picture, $removeOld = true)
    {
        return new self($pixie, $user, $picture, $removeOld);
    }

    public function execute()
    {
        if ($this->processed) {
            return;
        }

        $this->pixie->session->get();
        if ($this->pixie->getParameter('parameters.use_external_dir')) {
            if ($this->removeOld) {
                if ($this->modifyUser) {
                    $this->user->photo = '';
                }
            }

            if ($this->picture->isLoaded()) {
                $uploadDir = $this->pixie->getParameter('parameters.user_pictures_external_dir');
                $uploadPath = $uploadDir . "/sess_".session_id()."_uploadto";
                if (!file_exists($uploadPath) || !is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $photoName = $this->generatePhotoName($this->picture);

                if ($this->pixie->getParameter('parameters.use_perl_upload')) {
                    $scriptName = $this->pixie->isWindows() ? 'uploadwin.pl' : 'uploadux.pl';
                    $headers = $this->picture->upload('http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://'
                        . $_SERVER['HTTP_HOST'] . '/upload/' . $scriptName, $photoName);

                    if ($headers['X-Created-Filename']) {
                        /** @var File $newFile */
                        $newFile = $this->pixie->orm->get('file');
                        $newFile->path = $headers['X-Created-Filename'];
                        $newFile->user_id = $this->user->id();
                        $newFile->save();
                        $this->result = $newFile->id();
                        if ($this->modifyUser) {
                            $this->user->photo = $newFile->id();
                        }
                    }

                } else {
                    $uniqueUploadPath = $uploadPath . '/' . substr(sha1(time() . $this->picture->getName()), 0, 2);
                    if (!file_exists($uniqueUploadPath) || !is_dir($uniqueUploadPath)) {
                        mkdir($uniqueUploadPath, 0777, true);
                    }
                    $newPhotoPath = $uniqueUploadPath.'/'.$photoName;
                    $this->picture->move($newPhotoPath);
                    /** @var File $newFile */
                    $newFile = $this->pixie->orm->get('file');
                    $newFile->path = $newPhotoPath;
                    $newFile->user_id = $this->user->id();
                    $newFile->save();
                    $this->result = $newFile->id();
                    if ($this->modifyUser) {
                        $this->user->photo = $newFile->id();
                    }
                }
            }

        } else {
            $relativePath = $this->pixie->getParameter('parameters.user_pictures_path');
            $pathDelimiter = preg_match('|^[/\\\\]|', $relativePath) ? '' : DIRECTORY_SEPARATOR;
            $photoPath = preg_replace('#/+$#i', '', $this->pixie->root_dir) . $pathDelimiter . $relativePath;

            if ($this->removeOld && $this->user->photo && file_exists($photoPath . $this->user->photo)) {
                unlink($photoPath . $this->user->photo);
                if ($this->modifyUser) {
                    $this->user->photo = '';
                }
            }

            if ($this->picture->isLoaded()) {
                if ($this->user->photo && file_exists($photoPath . $this->user->photo)) {
                    unlink($photoPath . $this->user->photo);
                }

                $photoName = $this->generatePhotoName($this->picture);
                $uniquePhotoDirName = substr(sha1(time() . $this->picture->getName()), 0, 2);
                $uniquePhotoDir = $photoPath . $uniquePhotoDirName;
                if (!file_exists($uniquePhotoDir) || !is_dir($uniquePhotoDir)) {
                    mkdir($uniquePhotoDir, 0777, true);
                }
                $this->picture->move($uniquePhotoDir . '/' . $photoName);
                $uniquePhotoName = $uniquePhotoDirName . '/' . $photoName;
                $this->result = $uniquePhotoName;
                if ($this->modifyUser) {
                    $this->user->photo = $uniquePhotoName;
                }
            }
        }
        $this->processed = true;
    }

    protected function generatePhotoName(UploadedFile $photo)
    {
        return $photo->generateFileName();
    }

    public function getResultFileName()
    {
    }

    /**
     * @return boolean
     */
    public function getModifyUser()
    {
        return $this->modifyUser;
    }

    /**
     * @param boolean $modifyUser
     */
    public function setModifyUser($modifyUser)
    {
        $this->modifyUser = $modifyUser;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
} 