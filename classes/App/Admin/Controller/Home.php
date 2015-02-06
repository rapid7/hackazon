<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 13:51
 */


namespace App\Admin\Controller;


use App\Admin\Controller;
use VulnModule\Storage\PHPFileReader;
use VulnModule\VulnerabilityMatrixRenderer;

class Home extends Controller
{
    public function action_index()
    {
        $reader = new PHPFileReader($this->vulnConfigDir);

        $matrixRenderer = new VulnerabilityMatrixRenderer($reader);
        $matrix = $matrixRenderer->render();

        $this->view->matrix = $matrix['html'];
        $this->view->subview = 'vulnerability/matrix2';
        $this->view->message = "Index page";
        $this->view->pageTitle = "Vulnerability Matrix";
    }
} 