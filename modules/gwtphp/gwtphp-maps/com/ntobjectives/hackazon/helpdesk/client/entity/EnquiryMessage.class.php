<?php
class EnquiryMessage implements IsSerializable {
	/**
	 * 
	 * @var string
	*/
	public $message;
	
	/**
	 * 
	 * @var int
	*/
	public $id;
	
	/**
	 * 
	 * @var user
	*/
	public $author;
	
	/**
	 * 
	 * @var enquiry
	*/
	public $enquiry;
	
	/**
	 * 
	 * @var int
	*/
	public $enquiry_id;
	
	/**
	 * 
	 * @var date
	*/
	public $created_on;
	
	/**
	 * 
	 * @var date
	*/
	public $updated_on;
	
	/**
	 * 
	 * @var int
	*/
	public $author_id;
	
}
