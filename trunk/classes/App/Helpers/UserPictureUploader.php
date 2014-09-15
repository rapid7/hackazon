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
                $this->user->photo = '';
            }

            if ($this->picture->isLoaded()) {
                $uploadDir = $this->pixie->getParameter('parameters.user_pictures_external_dir');
                $uploadPath = $uploadDir . "/sess_".session_id()."_uploadto";
                if (!file_exists($uploadPath) || !is_dir($uploadPath)) {
                    mkdir($uploadPath);
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
                        $this->user->photo = $newFile->id();
                    }

                } else {
                    $newPhotoPath = $uploadPath.'/'.$photoName;
                    $this->picture->move($newPhotoPath);
                    /** @var File $newFile */
                    $newFile = $this->pixie->orm->get('file');
                    $newFile->path = $newPhotoPath;
                    $newFile->user_id = $this->user->id();
                    $newFile->save();
                    $this->user->photo = $newFile->id();
                }
            }

        } else {
            $relativePath = $this->pixie->getParameter('parameters.user_pictures_path');
            $pathDelimiter = preg_match('|^[/\\\\]|', $relativePath) ? '' : DIRECTORY_SEPARATOR;
            $photoPath = preg_replace('#/+$#i', '', $this->pixie->root_dir) . $pathDelimiter . $relativePath;

            if ($this->removeOld && $this->user->photo && file_exists($photoPath . $this->user->photo)) {
                unlink($photoPath . $this->user->photo);
                $this->user->photo = '';
            }

            if ($this->picture->isLoaded()) {
                if ($this->user->photo && file_exists($photoPath . $this->user->photo)) {
                    unlink($photoPath . $this->user->photo);
                }

                $photoName = $this->generatePhotoName($this->picture);
                $this->picture->move($photoPath . $photoName);
                $this->user->photo = $photoName;
            }
        }
        $this->processed = true;
    }

    protected function generatePhotoName(UploadedFile $photo)
    {
        return $photo->generateFileName($this->user->id());
    }

    public function getResultFileName()
    {
    }
} 