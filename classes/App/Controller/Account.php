<?php

namespace App\Controller;

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
        $this->view->subview = 'account/account';
    }

    public function action_orders() {
        $myOrders = $this->pixie->orm->get('Order')->getMyOrders();
        $this->view->myOrders = $myOrders;
        $this->view->subview = 'account/orders';
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

}