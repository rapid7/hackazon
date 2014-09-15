<?php

namespace App\Controller;

use App\Core\UploadedFile;
use App\Exception\NotFoundException;
use App\Helpers\FSHelper;
use App\Model\File;
use App\Model\Order;
use App\Model\User;
use PHPixie\Paginate\Pager\ORM as ORMPager;

class Account extends \App\Page {

    /**
     * require auth
     */
    public function before() {
        if (is_null($this->pixie->auth->user())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
    }

    public function action_index() {
        /** @var ORMPager $ordersPager */
        $ordersPager = $this->pixie->orm->get('Order')->order_by('created_at', 'DESC')->getMyOrdersPager(1, 5);
        $myOrders = $ordersPager->current_items()->as_array();
        $this->view->user = $this->pixie->auth->user();
        $this->view->myOrders = $myOrders;
        $this->view->subview = 'account/account';
    }

    public function action_orders()
    {
        /** @var Order $orderModel */
        $orderModel = $this->pixie->orm->get('Order');

        if ($orderId = $this->request->param('id')) { // Show single order
            $order = $orderModel->getByIncrement($orderId);
            if (!$order->loaded()) {
                throw new NotFoundException();
            }
            $this->view->id = $orderId;
            $this->view->order = $order;
            $this->view->subview = 'account/order';

        } else { // List orders
            $page = $this->request->get('page', 1);
            /** @var ORMPager $ordersPager */
            $ordersPager = $orderModel->order_by('created_at', 'DESC')->getMyOrdersPager($page, 5);
            $myOrders = $ordersPager->current_items()->as_array();
            $this->view->pager = $ordersPager;
            $this->view->myOrders = $myOrders;
            $this->view->subview = 'account/orders';
        }
    }

    public function action_documents() {
        if ($this->request->get('page')) {
            $page = $this->request->get('page');
            $path = realpath($this->common_path . "../content_pages/documents/") . DIRECTORY_SEPARATOR . $page;
            $service = $this->pixie->getVulnService();
            $vuln = $service->getVulnerability('os_command');

            if (!$vuln['enabled']) {
                $path = escapeshellarg($path);
            }

            // Determine OS and execute the ping command.
            if (stristr(php_uname('s'), 'Windows NT')) {
                exec('type ' . $path, $content);
            } else {
                exec('cat ' . $path, $content);
            }

            $this->view->pageTitle = ucwords(preg_replace('/\.html$/i', '', $page));
            $this->view->pageContent = implode("\n", $content);
            $this->view->subview = 'account/document';

        } else {
            $this->view->pageTitle = 'Documents';
            $files = [];
            $basePath = $this->common_path . "../content_pages/documents";
            $dirIterator = new \DirectoryIterator($basePath);
            /** @var \SplFileInfo $fileInfo */
            foreach ($dirIterator as $fileInfo) {
                if ($fileInfo->isFile() && preg_match('/html/i', $fileInfo->getExtension()) && $fileInfo->isReadable()) {
                    $pathinfo = pathinfo($fileInfo->getRealPath());
                    $files[$pathinfo['filename']] = $pathinfo['basename'];
                }
            }

            $this->view->files = $files;
            $this->view->subview = 'account/documents';
        }
    }

    public function action_help_articles() {
        if ($this->request->get('page')) {
            $page = $this->request->get('page');

            $service = $this->pixie->getVulnService();
            $vulnField = $service->getField('page');

            if (!is_array($vulnField) || !in_array('RemoteFileInclude', $vulnField)) {
                $files = $this->getHelpArticlesFiles();
                if (!in_array($page, $files)) {
                    throw new NotFoundException();
                }
            }

            $this->view->pageTitle = ucwords(str_replace('_', ' ', $page));
            $this->view->page = $page;
            $this->view->subview = 'account/help_article';

        } else {
            $this->view->pageTitle = 'Help Articles';
            $this->view->files = $this->getHelpArticlesFiles();
            $this->view->subview = 'account/help_articles';
        }
    }

    protected function getHelpArticlesFiles()
    {
        $files = [];
        $basePath = $this->common_path . "../content_pages/help_articles";
        $dirIterator = new \DirectoryIterator($basePath);
        /** @var \SplFileInfo $fileInfo */
        foreach ($dirIterator as $fileInfo) {
            if ($fileInfo->isFile() && preg_match('/php/i', $fileInfo->getExtension()) && $fileInfo->isReadable()) {
                $pathinfo = pathinfo($fileInfo->getRealPath());
                $files[str_replace('_', ' ', $pathinfo['filename'])] = $pathinfo['filename'];
            }
        }
        return $files;
    }

    public function action_edit_profile()
    {
        $user = $this->getUser();
        $fields = ['first_name', 'last_name', 'user_phone'];
        $errors = [];
        $this->view->success = false;

        if ($this->request->method == 'POST') {
            // $this->checkCsrfToken('profile');

            $photo = $this->request->uploadedFile('photo', [
                'extensions' => ['jpeg', 'jpg', 'gif', 'png'],
                'types' => ['image']
            ]);

            if ($photo->isLoaded() && !$photo->isValid()) {
                $errors[] = 'Incorrect avatar file';
            }

            $data = $user->filterValues($this->request->post(), $fields);

            if (!count($errors)) {
                $this->pixie->session->get();
                if ($this->pixie->getParameter('parameters.use_external_dir')) {
                    if ($this->request->post('remove_photo')) {
                        $user->photo = '';
                    }

                    if ($photo->isLoaded()) {
                        $uploadDir = $this->pixie->getParameter('parameters.user_pictures_external_dir');
                        $uploadPath = $uploadDir . "/sess_".session_id()."_uploadto";
                        if (!file_exists($uploadPath) || !is_dir($uploadPath)) {
                            mkdir($uploadPath);
                        }
                        $photoName = $this->generatePhotoName($photo);

                        if ($this->pixie->getParameter('parameters.use_perl_upload')) {
                            $scriptName = $this->pixie->isWindows() ? 'uploadwin.pl' : 'uploadux.pl';
                            $headers = $photo->upload('http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://'
                                . $_SERVER['HTTP_HOST'] . '/upload/' . $scriptName, $photoName);

                            if ($headers['X-Created-Filename']) {
                                /** @var File $newFile */
                                $newFile = $this->pixie->orm->get('file');
                                $newFile->path = $headers['X-Created-Filename'];
                                $newFile->user_id = $user->id();
                                $newFile->save();
                                $user->photo = $newFile->id();
                            }

                        } else {
                            $newPhotoPath = $uploadPath.'/'.$photoName;
                            $photo->move($newPhotoPath);
                            $newFile = $this->pixie->orm->get('file');
                            $newFile->path = $newPhotoPath;
                            $newFile->user_id = $user->id();
                            $newFile->save();
                            $user->photo = $newFile->id();
                        }
                    }

                } else {
                    $relativePath = $this->pixie->getParameter('parameters.user_pictures_path');
                    $pathDelimeter = preg_match('|^[/\\\\]|', $relativePath) ? '' : DIRECTORY_SEPARATOR;
                    $photoPath = preg_replace('#/+$#i', '', $this->pixie->root_dir) . $pathDelimeter . $relativePath;

                    if ($this->request->post('remove_photo')
                        && $user->photo
                        && file_exists($photoPath . $user->photo)
                    ) {
                        unlink($photoPath . $user->photo);
                        $user->photo = '';
                    }

                    if ($photo->isLoaded()) {
                        if ($user->photo && file_exists($photoPath . $user->photo)) {
                            unlink($photoPath . $user->photo);
                        }

                        $photoName = $this->generatePhotoName($photo);
                        $photo->move($photoPath . $photoName);
                        $user->photo = $photoName;
                    }
                }

                $user->values($data);

                $user->save();

                $this->pixie->session->flash('success', 'You have successfully updated your profile.');

                if ($this->request->post('_submit') == 'Save and Exit') {
                    $this->redirect('/account#profile');

                } else {
                    $this->redirect('/account/profile/edit');
                }

                return;

            } else {
                $data['photo'] = $user->photo;
            }

        } else {
            $data = $user->getFields(array_merge($fields, ['photo']));
        }

        foreach ($data as $key => $value) {
            $this->view->$key = $value;
        }
        $this->view->success = $this->pixie->session->flash('success') ?: '';
        $this->view->errorMessage = implode('<br>', $errors);
        $this->view->user = $user;
        $this->view->subview = 'account/edit_profile';
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->pixie->auth->user();
    }

    protected function generatePhotoName(UploadedFile $photo)
    {
        $user = $this->getUser();
        return $photo->generateFileName($user->id());
    }
}