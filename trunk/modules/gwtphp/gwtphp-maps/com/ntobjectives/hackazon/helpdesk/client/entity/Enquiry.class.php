<?php
class Enquiry implements IsSerializable {
	/**
	 * 
	 * @var int
	*/
	public $assigned_to;
	
	/**
	 * 
	 * @var int
	*/
	public $created_by;
	
	/**
	 * 
	 * @var int
	*/
	public $id;
	
	/**
	 * 
	 * @var string
	*/
	public $title;
	
	/**
	 * 
	 * @var string
	*/
	public $status;
	
	/**
	 * 
	 * @var date
	*/
	public $created_on;
	
	/**
	 * 
	 * @var string
	*/
	public $description;
	
	/**
	 * 
	 * @var date
	*/
	public $updated_on;
	
	/**
	 * 
	 * @var user
	*/
	public $assignedToUser;
	
	/**
	 * 
	 * @var user
	*/
	public $createdByUser;
	
}
