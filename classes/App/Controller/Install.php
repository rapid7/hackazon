<?php
namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Exception\RedirectException;
use App\Installation\Installer;
use App\Page;

class Install extends Page {

    /**
     * show overview page
     */
    public function action_index() {
        $this->initView('installation');

        try {
            $installer = $this->pixie->installer;
            $installer->setForceFreshInstall(!!$this->request->get('force', false));
            $result = $installer->init($this->view)->runWizard($this->request);

        } catch (RedirectException $e) {
            $this->redirect($e->getLocation());
            return;

        } catch (ForbiddenException $e) {
            $this->redirect('/install/login');
            return;
        }

        // If wizard successfully passed, install entered data.
        if ($result->isCompleted()) {
            $this->pixie->installer->finish();
            $this->pixie->session->set('isInstalled', true);
            $this->redirect('/');

        } else {
            // Current step (maybe not that we asked by the url, but one of invalid previous ones)
            $step = $result->getStep();

            if (!$step) {
                $step = $result->getLastStartedStep();
                $this->redirect('/install/' . $step->getName());
            }

            // Move next on successful step
            if ($step->getCompleted()) {
                $this->redirect('/install/' . $step->getNextStep()->getName());
                return;
            }

            // Redirect to invalid step if current step is greater than that step.
            if ($result->needRedirect()) {
                $this->redirect('/install/' . $step->getName());
                return;
            }

            $this->view->subview = $step->getTemplate();
            $this->view->errorMessage = implode('<br>', $step->getErrors());
            $this->view->step = $step;

            foreach ($result->getViewData() as $key => $value) {
                $this->view->$key = $value;
            }
            $this->view->bodyClass = "installation-page";
        }
    }

    public function action_login()
    {
        $this->initView('installation');
        $this->pixie->session->get();

        $params = $this->pixie->config->get('parameters') ?: [];
        $storedPassword = trim($params['installer_password']);

        if (!$storedPassword) {
            $this->redirect('/install');
            return;
        }

        if ($this->request->method == 'POST') {
            $password = $this->request->post('password');

            if ($password && $password == $storedPassword) {
                $_SESSION[Installer::SESSION_KEY]['authorized'] = true;
                $this->redirect('/install');
            } else {
                $this->view->errors = "Incorrect password.";
            }
        }

        $this->view->subview = 'installation/login';
    }

    public function action_finish()
    {
        $this->pixie->session->get();
        unset($_SESSION[Installer::SESSION_KEY]);
        $this->redirect('/install');
    }
}