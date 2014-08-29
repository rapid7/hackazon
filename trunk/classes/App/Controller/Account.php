<?php

namespace App\Controller;

use App\Core\UploadedFile;
use App\Exception\NotFoundException;
use App\Helpers\FSHelper;
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
        $this->view->pageTitle = 'Documents';
        $files = [];
        $basePath = $this->common_path . "../content_pages";
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

    /**
     * Show single document.
     */
    public function action_show() {
        $page = $this->request->get('page');
        $path = realpath($this->common_path . "../content_pages/") . DIRECTORY_SEPARATOR . $page;
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

                $photoPath = preg_replace('#/+$#i', '', $this->pixie->root_dir) . $this->pixie->config->get('page.user_pictures_path');

                if ($this->request->post('remove_photo')
                    && $user->photo
                    && file_exists($photoPath . $user->photo)
                ) {
                    unlink($photoPath.$user->photo);
                    $user->photo = '';
                }

                $user->values($data);

                if ($photo->isLoaded()) {
                    if ($user->photo && file_exists($photoPath . $user->photo)) {
                        unlink($photoPath . $user->photo);
                    }

                    $ext = FSHelper::cleanFileName($photo->getExtension());
                    $photoName = $user->id() . '_' . substr(sha1(time() . $photo->getName()), 0, 6) . '_'
                        . FSHelper::cleanFileName($photo->getBaseName(), 32) . ($ext ? '.' . $ext : '');

                    $photo->move($photoPath . $photoName);
                    $user->photo = $photoName;
                }

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
}