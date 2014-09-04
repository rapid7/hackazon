<?php
$gwtphpmap = array(
	'className' => 'com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage',
	'mappedBy' => 'com.ntobjectives.hackazon.helpdesk.client.entity.EnquiryMessage',
	'typeCRC' => '689768322',
	'fields' => array (
		array(
			'name' => 'message',
			'type' => 'java.lang.String',
		),
		array(
			'name' => 'id',
			'type' => 'I',
		),
		array(
			'name' => 'author',
			'type' => 'com.ntobjectives.hackazon.helpdesk.client.entity.User',
		),
		array(
			'name' => 'enquiry',
			'type' => 'com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry',
		),
		array(
			'name' => 'enquiry_id',
			'type' => 'I',
		),
		array(
			'name' => 'created_on',
			'type' => 'java.util.Date',
		),
		array(
			'name' => 'updated_on',
			'type' => 'java.util.Date',
		),
		array(
			'name' => 'author_id',
			'type' => 'I',
		)
	),
);
