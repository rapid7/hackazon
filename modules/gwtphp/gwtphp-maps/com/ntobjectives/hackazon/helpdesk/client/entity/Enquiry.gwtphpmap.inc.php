<?php
$gwtphpmap = array(
	'className' => 'com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry',
	'mappedBy' => 'com.ntobjectives.hackazon.helpdesk.client.entity.Enquiry',
	'typeCRC' => '1412794674',
	'fields' => array (
		array(
			'name' => 'assigned_to',
			'type' => 'I',
		),
		array(
			'name' => 'created_by',
			'type' => 'I',
		),
		array(
			'name' => 'id',
			'type' => 'I',
		),
		array(
			'name' => 'title',
			'type' => 'java.lang.String',
		),
		array(
			'name' => 'status',
			'type' => 'java.lang.String',
		),
		array(
			'name' => 'created_on',
			'type' => 'java.util.Date',
		),
		array(
			'name' => 'description',
			'type' => 'java.lang.String',
		),
		array(
			'name' => 'updated_on',
			'type' => 'java.util.Date',
		),
		array(
			'name' => 'assignedToUser',
			'type' => 'com.ntobjectives.hackazon.helpdesk.client.entity.User',
		),
		array(
			'name' => 'createdByUser',
			'type' => 'com.ntobjectives.hackazon.helpdesk.client.entity.User',
		)
	),
);
