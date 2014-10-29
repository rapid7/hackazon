<?php

namespace App\Controller;

use App\Core\UploadedFile;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\FSHelper;
use App\Helpers\UserPictureUploader;
use App\Model\File;
use App\Model\Order;
use App\Model\User;
use App\Page;
use PHPixie\Paginate\Pager\ORM as ORMPager;

/**
 * Class Account
 * @package App\Controller
 */
class Account extends Page
{
    protected $useRest = false;

    /**
     * require auth
     */
    public function before() {
        if (is_null($this->pixie->auth->user())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
        if (!$this->execute) {
            return;
        }

        $this->useRest = $this->pixie->getParameter('parameters.rest_in_profile', false);
        $this->view->useRest = $this->useRest;
    }

    public function action_index() {
        if (!$this->useRest) {
            /** @var ORMPager $ordersPager */
            $ordersPager = $this->pixie->orm->get('Order')->order_by('created_at', 'DESC')->getMyOrdersPager(1, 5);
            $myOrders = $ordersPager->current_items()->as_array();
            $this->view->myOrders = $myOrders;
        }
        $this->view->user = $this->pixie->auth->user();
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
            $this->view->items = $order->orderItems->find_all()->as_array();
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
        if ($this->useRest) {
            throw new NotFoundException;
        }
        $user = $this->getUser();
        $fields = ['first_name', 'last_name', 'user_phone'];
        $errors = [];
        $this->view->success = false;

        if ($this->request->method == 'POST') {
            $this->checkCsrfToken('profile');

            $photo = $this->request->uploadedFile('photo', [
                'extensions' => ['jpeg', 'jpg', 'gif', 'png'],
                'types' => ['image']
            ]);

            if ($photo->isLoaded() && !$photo->isValid()) {
                $errors[] = 'Incorrect avatar file';
            }

            $data = $user->filterValues($this->request->post(), $fields);

            if (!count($errors)) {
                UserPictureUploader::create($this->pixie, $user, $photo, $this->request->post('remove_photo'))
                    ->execute();

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

    public function action_add_photo()
    {
        $user = $this->getUser();
        $this->view->success = false;
        $errors = [];

        if ($this->request->method == 'POST') {

            $photo = $this->request->uploadedFile('photo', [
                'extensions' => ['jpeg', 'jpg', 'gif', 'png'],
                'types' => ['image']
            ]);

            if ($photo->isLoaded() && !$photo->isValid()) {
                $errors[] = 'Incorrect avatar file';
            }

            if (!count($errors)) {
                $uploader = UserPictureUploader::create($this->pixie, $user, $photo, $this->request->post('remove_photo'));
                $uploader->setModifyUser(false);
                $uploader->execute();
                $this->jsonResponse(['photo' => $uploader->getResult()]);

            } else {
                $this->jsonResponse(['errors' => $errors]);
            }

        } else {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->pixie->auth->user();
    }
}