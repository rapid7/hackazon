<?php
use App\IPixifiable;
use App\Pixie;
use GWTModule\IGWTService;
use GWTModule\RemoteServiceServlet;
use VulnModule\Config\Annotations as Vuln;

/**
 * Class HelpdeskServiceImpl
 * Implementation of HelpdeskService interface.
 * @Vuln\Description("Helpdesk GWT service.")
 */
class HelpdeskServiceImpl extends HelpdeskService implements IPixifiable, IGWTService {

    /**
     * @var User
     */
    protected $user;
    protected $initialized = false;

    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var RemoteServiceServlet
     */
    protected $servlet;

    /**
     * @var \VulnModule\Config\Context|null
     */
    protected $context;

    /**
     * @var \App\Core\Request|null
     */
    protected $request;

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

    /**
     * @return ApplicationData
     */
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

    /**
     * @param $id
     * @return IsSerializable|null|Enquiry
     * @Vuln\Description("Fetches an enquiry by its ID. ID is an integer number.")
     */
    public function getEnquiryById($id)
    {
        $idWrapped = $this->wrap('id', $id);
        return $this->servlet->getRepository()->findOne('Enquiry', $idWrapped);
    }

    /**
     * @return ArrayList<Enquiry>|Enquiry[]
     * @throws IllegalArgumentException
     */
    public function getEnquiries()
    {
        $this->checkAuthorized();

        return $this->servlet->getRepository()->getUserEnquiries();
    }

    /**
     * @param EnquiryMessage $message
     * @return EnquiryMessage
     * @throws IllegalArgumentException
     * @Vuln\Description("Accepts a message as a parameter.")
     */
    public function createEnquiryMessage($message)
    {
        $this->checkAuthorized();

        if ($message === null || !$message->message) {
            throw new IllegalArgumentException("Errors!!!");
        }

        $message->message = $this->wrap('message', $message->message);

        $message->author_id = $this->user->id;
        $message->created_on = new Date(time());
        $message->updated_on = new Date(time());
        $this->servlet->getRepository()->persistObject($message);

        $message->message = $message->message->getFilteredValue();
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

        $enquiry->title = $this->wrap('title', $enquiry->title);
        $enquiry->description = $this->wrap('description', $enquiry->description);

        $enquiry->created_by = $this->user->id;
        $enquiry->status = "new";
        $enquiry->created_on = new Date(time());
        $enquiry->updated_on = new Date(time());
        $this->servlet->getRepository()->persistObject($enquiry);

        $enquiry->title = $enquiry->title->getFilteredValue();
        $enquiry->description = $enquiry->description->getFilteredValue();

        return $enquiry;
    }

    public function isUserAuthenticated()
    {
    }

    /**
     * @param $enquiryId
     * @return ArrayList<EnquiryMessage>|EnquiryMessage[]
     * @throws IllegalArgumentException
     */
    public function getEnquiryMessages($enquiryId)
    {
        $this->checkAuthorized();
        return $this->servlet->getRepository()->getEnquiryMessages($this->wrap('enquiryId', $enquiryId));
    }

    /**
     * @return bool
     */
    protected function isAuthorized()
    {
        return $this->user !== null;
    }

    /**
     * @throws IllegalArgumentException
     */
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

    function getPixie()
    {
        return $this->pixie;
    }

    function setPixie(Pixie $pixie = null)
    {
        $this->pixie = $pixie;
    }

    function getServlet()
    {
        return $this->servlet;
    }

    function setServlet(RemoteServiceServlet $servlet = null)
    {
        return $this->servlet = $servlet;
    }

    public function wrap($key, $rawValue)
    {
        return $this->pixie->vulnService->wrapValue($key, $rawValue, \VulnModule\Config\FieldDescriptor::SOURCE_BODY);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext(\VulnModule\Config\Context $context = null)
    {
        $this->context = $context;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(\App\Core\Request $request = null)
    {
        $this->request = $request;
    }
}
