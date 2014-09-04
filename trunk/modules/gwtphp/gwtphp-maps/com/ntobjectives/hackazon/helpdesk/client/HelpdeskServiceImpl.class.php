<?php

/**
 * Class HelpdeskServiceImpl
 * Implementation of HelpdeskService interface.
 */
class HelpdeskServiceImpl extends HelpdeskService {
    use \App\Traits\Pixifiable;
    use \GWTModule\Servletable;

    /**
     * @var User
     */
    protected $user;
    protected $initialized = false;

    protected function init()
    {
        if ($this->initialized) {
            return;
        }

        $user = $this->pixie->auth->user();
        $this->user = $user ? $this->servlet->getRepository()->transform($user) : $user;
        $this->initialized = true;
    }

    public function getDate()
    {
    }

    public function getAppData()
    {
        $this->init();

        $appData = new ApplicationData();
        $appData->isAutorized = $this->isAuthorized();
        $appData->user = $this->user;
        return $appData;
    }

    public function getMessage($msg)
    {
    }

    public function getEnquiryById($id)
    {
        return $this->servlet->getRepository()->findOne('Enquiry', $id);
    }

    public function getEnquiries()
    {
        $this->checkAuthorized();

        return $this->servlet->getRepository()->getUserEnquiries();
    }

    /**
     * @param EnquiryMessage $message
     * @return EnquiryMessage
     * @throws IllegalArgumentException
     */
    public function createEnquiryMessage($message)
    {
        $this->checkAuthorized();

        if ($message == null || !$message->message) {
            throw new IllegalArgumentException("Errors!!!");
        }

        $message->author_id = $this->user->id;
        $message->created_on = new Date(time());
        $message->updated_on = new Date(time());
        $this->servlet->getRepository()->persistObject($message);

        return $message;
    }

    /**
     * @param Enquiry $enquiry
     * @return \Enquiry
     * @throws IllegalArgumentException
     */
    public function createEnquiry($enquiry)
    {
        $this->checkAuthorized();

        if ($enquiry == null || !$enquiry->title || !$enquiry->description) {
            throw new IllegalArgumentException("Errors!!!");
        }

        $enquiry->created_by = $this->user->id;
        $enquiry->status = "new";
        $enquiry->created_on = new Date(time());
        $enquiry->updated_on = new Date(time());
        $this->servlet->getRepository()->persistObject($enquiry);
        return $enquiry;
    }

    public function isUserAuthenticated()
    {
    }

    public function getEnquiryMessages($enquiryId)
    {
        $this->checkAuthorized();
        return $this->servlet->getRepository()->getEnquiryMessages($enquiryId);
    }

    protected function isAuthorized()
    {
        return $this->user !== null;
    }

    protected function checkAuthorized()
    {
        $this->init();
        if (!$this->isAuthorized()) {
            throw new IllegalArgumentException;
        }
    }

    public function getEnquiriesPagePerPage($page, $perPage)
    {
    }

    public function getEnquiriesPage($page)
    {
    }
}
