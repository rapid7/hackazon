<?php
abstract class HelpdeskService implements RemoteService {
	
	public abstract function getDate();
	
	public abstract function getAppData();
	
	public abstract function getMessage($msg);
	
	public abstract function getEnquiryById($id);
	
	public abstract function getEnquiries();
	
	public abstract function createEnquiryMessage($message);
	
	public abstract function getEnquiriesPagePerPage($page, $perPage);
	
	public abstract function getEnquiriesPage($page);
	
	public abstract function createEnquiry($enquiry);
	
	public abstract function isUserAuthenticated();
	
	public abstract function getEnquiryMessages($enquiryId);
}
