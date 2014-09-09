<?php
namespace App\Controller;

class Install extends \App\Page {

    /**
     * show overview page
     */
    public function action_index() {
        $this->initView('installation');
        $result = $this->pixie->installer->init($this->view)->runWizard($this->request);

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

    /**
     * Step 1
     */
	public function action_step1() {
		$this->view->subview = 'install/step1';
		$this->view->tab = 'step1';
		$this->view->step = 'Step 1';
    }

    /**
     * Step 2
     */
	public function action_step2() {
		$this->view->subview = 'install/step2';
		$this->view->tab = 'step2';
		$this->view->step = 'Step 2';
    }


}