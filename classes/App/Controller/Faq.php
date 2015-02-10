<?php
namespace App\Controller;


use VulnModule\Config\Annotations as Vuln;
use App\Page;

/**
 * Class Faq
 * @property \App\Model\Faq $model
 * @package App\Controller
 */
class Faq extends Page
{
    /**
     * @throws \App\Exception\HttpException
     * @Vuln\Description("View: pages/faq. Or AJAX action.")
     */
    public function action_index() {
        $this->view->pageTitle = "Frequently Asked Questions";

        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('faq', null, !$this->request->is_ajax());

            $post = $this->request->postWrap();
            $item = $this->pixie->orm->get('Faq')->create($post);
            $this->pixie->session->flash('success', 'Thank you for your question. We will contact you as soon.');
            $this->response->body = json_encode(array($item->as_array()));

            $this->execute = false;
            return;
        }

        $service = $this->pixie->vulnService;
        $this->view->subview = 'pages/faq';

        $entries = $this->model->getEntries()->as_array();
        foreach ($entries as $key => $entry) {
            $entry->question = $service->wrapValueByPath($entry->question, 'default->faq->index|userQuestion:any|0', true);
            $entries[$key] = $entry;
        }
        $this->view->entries = $entries;
    }
}