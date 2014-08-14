<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 14.08.2014
 * Time: 11:57
 */


namespace App\Controller;

/**
 * Class Page
 * @package App\Controller
 */
class Page extends \App\Page
{
    /**
     * List all available pages
     */
    public function action_index()
    {
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
        $this->view->subview = 'pages/pages';
    }

    /**
     * Show single page.
     */
    public function action_show()
    {
        $page = $this->request->get('page');
        $path = realpath($this->common_path . "../content_pages/") . DIRECTORY_SEPARATOR .$page;
        $service = $this->pixie->getVulnService();
        $vuln = $service->getVulnerability('os_command');

        if (!$vuln['enabled']) {
            $path = escapeshellarg($path);
        }

        // Determine OS and execute the ping command.
        if (stristr(php_uname('s'), 'Windows NT')) {   // $this->dumpx(rawurlencode('/'), 'type ' . $path);
            exec('type ' . $path, $content);

        } else {
            exec('cat ' . $path, $content);
        }

        $this->view->pageTitle = ucwords(preg_replace('/\.html$/i', '', $page));
        $this->view->pageContent = implode("\n", $content);
        $this->view->subview = 'pages/page';
    }
} 